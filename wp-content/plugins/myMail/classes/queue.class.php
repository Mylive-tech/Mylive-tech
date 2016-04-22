<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_queue {

	private $max_retry_after_error = 3;

	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init' ), 1 );

	}

	public function init() {

		add_action('mymail_cron', array( &$this, 'update_status'), 10);
		add_action('mymail_cron', array( &$this, 'update'), 20);
		add_action('mymail_cron', array( &$this, 'autoresponder_timebased'), 30);
		add_action('mymail_cron', array( &$this, 'autoresponder_usertime'), 30);
		add_action('mymail_cron', array( &$this, 'autoresponder'), 30);
		add_action('mymail_cron', array( &$this, 'cleanup'), 50);

		add_action('mymail_cron_worker', array( &$this, 'update_status'), 10);
		add_action('mymail_cron_worker', array( &$this, 'update'), 20);
		add_action('mymail_cron_worker', array( &$this, 'autoresponder'), 30);
		add_action('mymail_cron_worker', array( &$this, 'progress'), 50);
		add_action('mymail_cron_worker', array( &$this, 'finish_campaigns'), 100);
		add_action('mymail_cron_worker', array( &$this, 'autoresponder_timebased'), 110);

		add_action('mymail_update_queue', array( &$this, 'autoresponder'), 30);
		add_action('mymail_update_queue', array( &$this, 'update_status'), 30);
		add_action('mymail_update_queue', array( &$this, 'update'), 30);

		add_action('mymail_bounce', array( &$this, 'add_after_bounce'), 10, 3);

		//hooks to remove subscriber from the queue
		if(!defined('MYMAIL_DO_BULKIMPORT')){
			add_action('mymail_subscriber_change_status', array( &$this, 'subscriber_change_status'), 10, 3);
			add_action('mymail_unassign_lists', array( &$this, 'unassign_lists'), 10, 3);
			add_action('mymail_update_subscriber', array( &$this, 'update_subscriber'), 10, 3);
		}

	}


	public function add($args) {

		global $wpdb;

		$now = time();

		$args = wp_parse_args( $args, array(
			'added' => $now,
			'timestamp' => $now,
			'priority' => 10,
			'count' => 1,
			'sent' => 0,
		) );

		if(isset($args['options'])) $args['options'] = esc_sql(maybe_serialize( $args['options'] ));

		$sql = "INSERT INTO {$wpdb->prefix}mymail_queue (".implode(', ', array_keys($args)).")";

		$sql .= " VALUES ('".implode("','", array_values($args))."')";

		$sql .= " ON DUPLICATE KEY UPDATE count = count+1, timestamp = values(timestamp), sent = values(sent), priority = values(priority)";

		return false !== $wpdb->query($sql);

	}


	public function add_after_bounce($subscriber_id, $campaign_id, $hard) {

		//only softbounce
		if($hard) return;

		$now = time();
		$delay = mymail_option('bounce_delay', 60)*60;

		return $this->add(array(
			'campaign_id' => $campaign_id,
			'subscriber_id' => $subscriber_id,
			'timestamp' => $now+$delay,
			'priority' => 15,
			'count' => 2,
			'requeued' => 1,
		));

	}


	public function bulk_add($campaign_id, $subscribers, $timestamp = NULL, $priority = 10, $clear = false, $ignore_status = false, $reset = false) {

		global $wpdb;

		if($clear) $this->clear($campaign_id, $subscribers);

		if(empty($subscribers)) return;

		if(is_null($timestamp)) $timestamp = time();
		$timestamps = !is_array($timestamp)
					? array_fill(0, count($subscribers), $timestamp)
					: $timestamp;

		$now = time();

		$campaign_id = intval($campaign_id);
		$subscribers = array_filter($subscribers, 'is_numeric');


		if(empty($subscribers)) return true;
		$inserts = array();

		foreach($subscribers as $i => $subscriber_id){
			$inserts[] = "($subscriber_id,$campaign_id,$now,".$timestamps[$i].",$priority,1,'$ignore_status')";
		}

		$chunks = array_chunk($inserts, 2000);

		$success = true;

		foreach($chunks as $insert){
			$sql = "INSERT INTO {$wpdb->prefix}mymail_queue (subscriber_id, campaign_id, added, timestamp, priority, count, ignore_status) VALUES";

			$sql .= " ".implode(',', $insert);

			$sql .= " ON DUPLICATE KEY UPDATE timestamp = values(timestamp), ignore_status = values(ignore_status)";
			if($reset) $sql .= ', sent = 0';

			$success = $success && false !== $wpdb->query($sql);

		}

		return $success;

	}


	public function remove($campaign_id = NULL, $subscribers = NULL, $requeued = false) {

		global $wpdb;

		$sql = "DELETE a FROM {$wpdb->prefix}mymail_queue AS a WHERE 1";
		if(!is_null($campaign_id)) $sql .= $wpdb->prepare(" AND a.campaign_id = %d", $campaign_id);
		if(!$requeued) $sql .= " AND a.requeued = 0";

		if(!is_null($subscribers)){
			if(!is_array($subscribers)) $subscriber = array($subscribers);
			$subscribers = array_filter($subscribers, 'is_numeric');
			if(empty($subscribers)) $subscribers = array(-1);

			$sql .= " AND a.subscriber_id NOT IN (".implode(',', $subscribers).")";

		}

		return false !== $wpdb->query($sql);

	}

	//clear queue from subscribers who where previously in the queue but no longer assigned to the campaign
	public function clear($campaign_id = NULL, $subscribers = array()) {

		global $wpdb;

		$campaign_id = intval($campaign_id);
		$subscribers = array_filter($subscribers, 'is_numeric');

		if(empty($subscribers)) $subscribers = array(-1);

		$sql = "DELETE a FROM {$wpdb->prefix}mymail_queue AS a WHERE a.sent = 0 AND a.subscriber_id NOT IN (".implode(',', $subscribers).")";
		if(!is_null($campaign_id)) $sql .= $wpdb->prepare(" AND a.campaign_id = %d", $campaign_id);

		return false !== $wpdb->query($sql);

	}


	public function cleanup(){

		global $wpdb;

		//remove all entries from the queue where subscribers are hardbounced
		$wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_queue AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id WHERE b.type = 5 AND a.requeued = 1 AND a.sent != 0");

		//remove all entries from the queue where subscribers got a certain autoresponder and are sent over 24h ago
		$wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_queue AS a LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id AND p.post_status = 'autoresponder' WHERE sent != 0 AND sent < ".(time()-86400));

		//remove all entries from the queue where campaign has been removed
		$wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_queue AS a LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id AND p.post_type = 'newsletter' WHERE p.ID IS NULL");

		//remove some entrys which are not removed from the queue and are already sent
		//$wpdb->query("DELETE a FROM {$wpd->prefix}mymail_queue AS a LEFT JOIN {$wpd->prefix}mymail_actions AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id AND b.type = 1 WHERE a.options = '' AND b.subscriber_id IS NOT NULL AND a.requeued = 0")

	}


	public function update_status() {

		$campaigns = mymail('campaigns')->get_campaigns(array('post_status' => array('queued')));

		$now = time();

		foreach($campaigns as $campaign){

			if($campaign->post_status != 'queued') continue;

			$timestamp = mymail('campaigns')->meta($campaign->ID, 'timestamp');
			$timezone = mymail('campaigns')->meta($campaign->ID, 'timezone');

			//change status to active 24h if user based timezone is enabled
			if($timestamp-$now <= ($timezone ? 86400 : 0)){
				mymail('campaigns')->change_status($campaign, 'active');
			}

		}
	}


	public function update() {

		global $wpdb;
		//update the regular queue
		$campaigns = mymail('campaigns')->get_campaigns(array('post_status' => array('active')));

		$now = time();

		foreach($campaigns as $campaign){

			if($campaign->post_status != 'active') continue;

			//check if subscribers have to get queue
			if(!mymail('campaigns')->get_unsent_subscribers($campaign->ID, array(1), false, true)) continue;

			$timestamp = mymail('campaigns')->meta($campaign->ID, 'timestamp');
			$timezone = mymail('campaigns')->meta($campaign->ID, 'timezone');

			$offset = 0;
			$limit = 100000;

			//as long we have subscribers
			while (count($subscribers = mymail('campaigns')->get_unsent_subscribers($campaign->ID, array(1), true, true, $limit, $offset))) {


				//get users timeoffsets
				if($timezone)
					$timestamp = mymail('subscribers')->get_timeoffset_timestamps($subscribers, $timestamp);

				$this->bulk_add($campaign->ID, $subscribers, $timestamp, 10, false);

				$offset += $limit;
			}

		}

	}

	public function autoresponder($campaign_id = NULL, $force = false) {

		global $wpdb;
		static $mymail_autoresponder;
		if(!isset($mymail_autoresponder)) $mymail_autoresponder = array();

		$campaigns = empty($campaign_id) ? mymail('campaigns')->get_autoresponder() : array(mymail('campaigns')->get($campaign_id));

		if(empty($campaigns)) return;

		$now = time();
		$timeoffset = mymail('helper')->gmt_offset(true);

		foreach($campaigns as $campaign){

			if($campaign->post_status != 'autoresponder') continue;

			if(in_array($campaign->ID, $mymail_autoresponder) && !$force) continue;
			$mymail_autoresponder[] = $campaign->ID;

			$meta = mymail('campaigns')->meta($campaign->ID);

			if(!$meta['active']){

				$this->remove($campaign->ID);
				continue;
			}

			$autoresponder_meta = $meta['autoresponder'];

			if(is_numeric($autoresponder_meta['unit'])){

				mymail_notice(sprintf('Auto responder campaign %s has been deactivated caused by an old timeformat. Please update your campaign!', '<strong>"<a href="post.php?post='.$campaign->ID.'&action=edit">'.$campaign->post_title.'</a>"</strong>'), 'error', false, 'camp_error_'.$campaign->ID);
				mymail('campaigns')->update_meta($campaign->ID, 'active', false);
				continue;
			}

			if('mymail_subscriber_insert' == $autoresponder_meta['action']){

				$offset = $autoresponder_meta['amount'].' '.strtoupper($autoresponder_meta['unit']);

				$sql = $wpdb->prepare("SELECT a.ID, UNIX_TIMESTAMP(FROM_UNIXTIME(IF(a.confirm, a.confirm, a.signup)) + INTERVAL $offset) AS timestamp FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d AND b.type = 1 LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON a.ID = ab.subscriber_id", $campaign->ID);

				if(!empty($meta['list_conditions'])) $sql .= mymail('campaigns')->get_sql_join_by_condition($meta['list_conditions']);

				$sql .= " WHERE (a.confirm != 0 OR a.signup != 0)";

				$sql .= $wpdb->prepare(" AND a.signup >= %d", $meta['timestamp']);

				$to = $now-$offset+3600;

				$sql .= " AND a.status = 1 AND b.subscriber_id IS NULL";

				if(!empty($meta['list_conditions'])) $sql .= mymail('campaigns')->get_sql_by_condition($meta['list_conditions']);

				if(empty($meta['ignore_lists']) && !empty($meta['lists'])){
					$meta['lists'] = array_filter($meta['lists'], 'is_numeric');
					$sql .= " AND ab.list_id IN(".implode(', ', $meta['lists']).")";
				}

				$sql .= $wpdb->prepare(" HAVING timestamp <= %d", $to);

				if($subscribers = $wpdb->get_results($sql)){
					$subscriber_ids = wp_list_pluck( $subscribers, 'ID' );
					$timestamps = wp_list_pluck( $subscribers, 'timestamp' );

					$this->bulk_add($campaign->ID, $subscriber_ids, $timestamps, 15);
				}

			}else if('mymail_subscriber_unsubscribed' == $autoresponder_meta['action']){

				$offset = $autoresponder_meta['amount'].' '.strtoupper($autoresponder_meta['unit']);

				$sql = $wpdb->prepare("SELECT a.ID, UNIX_TIMESTAMP(FROM_UNIXTIME(b.timestamp) + INTERVAL $offset) AS timestamp FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.ID = b.subscriber_id AND b.type = 4 LEFT JOIN {$wpdb->prefix}mymail_actions AS c ON a.ID = c.subscriber_id AND c.type = 1 AND c.campaign_id = %d LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON a.ID = ab.subscriber_id", $campaign->ID);

				if(!empty($meta['list_conditions'])) $sql .= mymail('campaigns')->get_sql_join_by_condition($meta['list_conditions']);

				$sql .= " WHERE 1";

				$sql .= $wpdb->prepare(" AND b.timestamp >= %d", $meta['timestamp']);

				$to = $now-$offset+3600;

				$sql .= " AND a.status = 2 AND b.subscriber_id IS NOT NULL AND c.timestamp IS NULL";

				if(!empty($meta['list_conditions'])) $sql .= mymail('campaigns')->get_sql_by_condition($meta['list_conditions']);

				if(empty($meta['ignore_lists']) && !empty($meta['lists'])){
					$meta['lists'] = array_filter($meta['lists'], 'is_numeric');
					$sql .= " AND ab.list_id IN(".implode(', ', $meta['lists']).")";
				}

				$sql .= $wpdb->prepare(" HAVING timestamp <= %d", $to);

				if($subscribers = $wpdb->get_results($sql)){

					$subscriber_ids = wp_list_pluck( $subscribers, 'ID' );
					$timestamps = wp_list_pluck( $subscribers, 'timestamp' );

					$this->bulk_add($campaign->ID, $subscriber_ids, $timestamps, 15, false, true);
				}

			}else if('mymail_autoresponder_followup' == $autoresponder_meta['action'] && $campaign->post_parent){

				$offset = $autoresponder_meta['amount'].' '.strtoupper($autoresponder_meta['unit']);

				$sql = $wpdb->prepare("SELECT a.ID, UNIX_TIMESTAMP(FROM_UNIXTIME(b.timestamp) + INTERVAL $offset) AS timestamp FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.ID = b.subscriber_id AND b.type = %d AND b.campaign_id = %d LEFT JOIN {$wpdb->prefix}mymail_actions AS c ON a.ID = c.subscriber_id AND c.type = 1 AND c.campaign_id = %d LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON a.ID = ab.subscriber_id", $autoresponder_meta['followup_action'], $campaign->post_parent, $campaign->ID);

				if(!empty($meta['list_conditions'])) $sql .= mymail('campaigns')->get_sql_join_by_condition($meta['list_conditions']);

				$sql .= " WHERE 1";

				$sql .= " AND a.status = 1 AND b.subscriber_id IS NOT NULL AND c.subscriber_id IS NULL";

				if(!empty($meta['list_conditions'])) $sql .= mymail('campaigns')->get_sql_by_condition($meta['list_conditions']);

				if(empty($meta['ignore_lists']) && !empty($meta['lists'])){
					$meta['lists'] = array_filter($meta['lists'], 'is_numeric');
					$sql .= " AND ab.list_id IN(".implode(', ', $meta['lists']).")";
				}

				$sql .= $wpdb->prepare(" HAVING timestamp <= %d", $now+3600);

				if($subscribers = $wpdb->get_results($sql)){

					$subscriber_ids = wp_list_pluck( $subscribers, 'ID' );
					$timestamps = wp_list_pluck( $subscribers, 'timestamp' );

					$this->bulk_add($campaign->ID, $subscriber_ids, $timestamps, 15, false);
				}

			}


		}


	}


	public function autoresponder_timebased($campaign_id = NULL, $force = false) {

		global $wpdb;
		static $mymail_autoresponder;
		if(!isset($mymail_autoresponder)) $mymail_autoresponder = array();

		$campaigns = empty($campaign_id) ? mymail('campaigns')->get_autoresponder() : array(mymail('campaigns')->get($campaign_id));

		if(empty($campaigns)) return;

		$now = time();
		$timeoffset = mymail('helper')->gmt_offset(true);

		foreach($campaigns as $campaign){

			if($campaign->post_status != 'autoresponder') continue;

			if(in_array($campaign->ID, $mymail_autoresponder) && !$force) continue;
			$mymail_autoresponder[] = $campaign->ID;

			$meta = mymail('campaigns')->meta($campaign->ID);

			$autoresponder_meta = $meta['autoresponder'];
			$time_conditions = isset($autoresponder_meta['time_conditions']);

			if('mymail_autoresponder_timebased' != $autoresponder_meta['action']) continue;

			if(!$meta['active']){

				$this->remove($campaign->ID);
				continue;
			}

			$starttime = $meta['timestamp'];
			$delay = $starttime-$now;

			//more than an hour in the future (without timezone) or more than 24h in the future (with timezone)
			if(!$time_conditions && ($delay > 3600 && !$meta['timezone']
				|| ($delay > 86400))) continue;

			if(isset($autoresponder_meta['endschedule']) && $autoresponder_meta['endtimestamp']){
				$endtime = $autoresponder_meta['endtimestamp'];

				//endtime has passed
				if($endtime-$now < 0){

					//disable this campaign
					mymail('campaigns')->update_meta($campaign->ID, 'active', false);
					mymail_notice(sprintf(__('Auto responder campaign %s has been finished and is deactivated!', 'mymail'), '<strong>"<a href="post.php?post='.$campaign->ID.'&action=edit">'.$campaign->post_title.'</a>"</strong>'), 'updated', false, 'autoresponder_'.$campaign_id);
					continue;


				}

			}

			$doit = true;
			$new_id = null;
			$schedule_new = null;

			//check for conditions
			if($time_conditions){

				//if post count is reached
				if($autoresponder_meta['post_count_status']
					&& $autoresponder_meta['post_count_status'] >= $autoresponder_meta['time_post_count']){

					//reduce counter with required posts counter
					$autoresponder_meta['post_count_status'] = $autoresponder_meta['post_count_status'] - $autoresponder_meta['time_post_count'];

				}else{
					//schedule new event if it's in the past
					if($delay < 0) $schedule_new = true;
					$doit = false;

				}

			}

			if($doit){

				if($new_id = mymail('campaigns')->autoresponder_to_campaign($campaign->ID, $delay, $autoresponder_meta['issue']++)){

					$newCamp = mymail('campaigns')->get($new_id);
					$schedule_new = true;

					mymail_notice(sprintf(__('New campaign %s has been created!', 'mymail'), '<strong>"<a href="post.php?post='.$newCamp->ID.'&action=edit">'.$newCamp->post_title.'</a>"</strong>'), 'error', true, 'autoresponder_'.$new_id);

					do_action('mymail_autoresponder_timebased', $campaign->ID, $new_id);
				}

			}

			if($schedule_new){
				$nextdate = mymail('helper')->get_next_date_in_future($starttime, $autoresponder_meta['interval'], $autoresponder_meta['time_frame'], $autoresponder_meta['weekdays']);

				mymail('campaigns')->update_meta($campaign->ID, 'timestamp', $nextdate);
				mymail('campaigns')->update_meta($campaign->ID, 'autoresponder', $autoresponder_meta);

			}

		}

	}


	public function autoresponder_usertime($campaign_id = NULL, $force = false) {

		global $wpdb;
		static $mymail_autoresponder;
		if(!isset($mymail_autoresponder)) $mymail_autoresponder = array();

		$campaigns = empty($campaign_id) ? mymail('campaigns')->get_autoresponder() : array(mymail('campaigns')->get($campaign_id));

		$now = time();
		$timeoffset = mymail('helper')->gmt_offset(true);

		foreach($campaigns as $campaign){

			if($campaign->post_status != 'autoresponder') continue;

			if(in_array($campaign->ID, $mymail_autoresponder) && !$force) continue;
			$mymail_autoresponder[] = $campaign->ID;

			$meta = mymail('campaigns')->meta($campaign->ID);

			$autoresponder_meta = $meta['autoresponder'];

			if('mymail_autoresponder_usertime' != $autoresponder_meta['action']) continue;

			if(!$meta['active']){

				$this->remove($campaign->ID);
				continue;
			}

			$timezone_based = $meta['timezone'];

			$date_fields = mymail()->get_custom_date_fields(true);

			if(!in_array($autoresponder_meta['uservalue'], $date_fields)){
				mymail_notice(sprintf('Auto responder campaign %s has been deactivated caused by a missing date field. Please update your campaign!', '<strong>"<a href="post.php?post='.$campaign->ID.'&action=edit">'.$campaign->post_title.'</a>"</strong>'), 'error', false, 'camp_error_'.$campaign->ID);
				mymail('campaigns')->update_meta($campaign->ID, 'active', false);
				$this->remove($campaign->ID);
				continue;
			}

			$integer = floor($autoresponder_meta['amount']);
			$decimal = $autoresponder_meta['amount']-$integer;

			$useroffset = strtotime('+'.$autoresponder_meta['useramount'].' '.$autoresponder_meta['userunit'], 0);
			$send_offset = (strtotime('+'.$integer.' '.$autoresponder_meta['unit'], 0)+(strtotime('+1 '.$autoresponder_meta['unit'], 0)*$decimal))*$autoresponder_meta['before_after'];
			$offsettimestamp = strtotime('+'.($send_offset/-3600).' hours', strtotime('tomorrow midnight'))+$timeoffset;

			$subscriber_ids = array();
			$timestamps = array();

			$once = isset($autoresponder_meta['once']) && $autoresponder_meta['once'];

			$exact_date = isset($autoresponder_meta['userexactdate']) && $autoresponder_meta['userexactdate'];

			if($exact_date){
				$specialcond = " AND x.meta_value = '".date('Y-m-d', $offsettimestamp)."'";
			}else{
				$specialcond = $wpdb->prepare(" AND STR_TO_DATE(x.meta_value, %s) <= %s", '%Y-%m-%d', date('Y-m-d', $offsettimestamp));
				switch ($autoresponder_meta['userunit']) {
					case 'year':
						$specialcond .= " AND x.meta_value LIKE '%".date('-m-d', $offsettimestamp)."'";
						break;
					case 'month':
						$specialcond .= " AND x.meta_value LIKE '%".date('-d', $offsettimestamp)."'";
						break;
					default:
						$specialcond .= " AND x.meta_value != ''";
						break;
				}
			}

			//get SQL only
			$sql = mymail('campaigns')->get_subscribers($campaign->ID, NULL, true, $once, false, NULL, 0, true);

			//do some magic replace
			$replace = array(
				"SELECT a.ID" 	=> "SELECT a.ID, x.meta_value AS date",
				"WHERE 1" 		=> "LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS x ON a.ID = x.subscriber_id WHERE 1".$wpdb->prepare(" AND x.meta_key = %s", $autoresponder_meta['uservalue']).$specialcond,
				"ORDER BY a.ID" => "ORDER BY x.meta_value",
			);

			$sql = str_replace(array_keys($replace), $replace, $sql);

			$subscribers = $wpdb->get_results($sql);

			foreach($subscribers as $subscriber){

				$nextdate = strtotime($subscriber->date) + $send_offset - $timeoffset;

				//in the past already so get next date in future
				if($nextdate - $now < 0 && !$exact_date)
					$nextdate = mymail('helper')->get_next_date_in_future($nextdate, $autoresponder_meta['useramount'], $autoresponder_meta['userunit']);

				$timedelay = $nextdate - $now;

				if($timedelay < ($timezone_based ? 86400 : 3600) && $timedelay >= 0){
					$subscriber_ids[] = $subscriber->ID;
					$timestamps[] = $nextdate;
				}

			}

			if(!empty($subscriber_ids)){
				if($timezone_based)
					$timestamps = mymail('subscribers')->get_timeoffset_timestamps($subscriber_ids, $timestamps);

				$this->bulk_add($campaign->ID, $subscriber_ids, $timestamps, 15);

				do_action('mymail_autoresponder_usertime', $campaign->ID, $subscriber_ids);
			}

		}
	}


	public function finish_campaigns() {

		global $wpdb;

		//remove not sent queues which have a wrong status
		$wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_queue AS a LEFT JOIN {$wpdb->prefix}mymail_subscribers AS b ON a.subscriber_id = b.ID WHERE (a.sent = 0 OR a.ignore_status = 1) AND b.status != 1");

		//select all finished campaigns
		$sql = "SELECT posts.ID, queue.sent FROM {$wpdb->prefix}posts AS posts LEFT JOIN {$wpdb->prefix}mymail_queue AS queue ON posts.ID = queue.campaign_id LEFT JOIN {$wpdb->prefix}mymail_actions AS actions ON actions.subscriber_id = queue.subscriber_id AND actions.campaign_id = queue.campaign_id AND actions.type = 1 WHERE posts.post_status IN ('active') AND posts.post_type = 'newsletter' AND queue.requeued = 0 GROUP BY posts.ID HAVING SUM(queue.sent = 0) = 0 OR queue.sent IS NULL";

		$ids = $wpdb->get_col($sql);

		foreach($ids as $id){

			$totals = mymail('campaigns')->get_totals($id);
			$sent = mymail('campaigns')->get_sent($id);
			if(!$totals || !$sent) continue;

			mymail('campaigns')->finish($id, false);

		}

		//remove notifications which are sent over an hour ago
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}mymail_queue WHERE sent != 0 AND campaign_id = 0 AND sent < %d", (time()-3600)));

	}


	public function progress($cron_used = false) {

		global $wpdb, $pagenow;

		if(get_transient( 'mymail_cron_lock' )){
			echo 'CRON LOCK!';
			return false;
		}

		$microtime = microtime(true);
		$globaltime = isset($GLOBALS['time_start']) ? $GLOBALS['time_start'] : $microtime;

		$timeoffset = mymail('helper')->gmt_offset(true);

		set_transient( 'mymail_cron_lock', $microtime, 120 );

		$last_hit = get_option('mymail_cron_lasthit');
		if(empty($last_hit)) {
			$last_hit = array(
				'timestamp' => $microtime,
				'timemax' => 0
			);
		}

		$last_hit = array(
			'ip' => mymail_get_ip(),
			'timestamp' => $microtime,
			'oldtimestamp' => $last_hit['timestamp'],
			'time' => 0,
			'timemax' => $last_hit['timemax'],
		);

		update_option('mymail_cron_lasthit', $last_hit);

		$safe_mode = @ini_get('safe_mode');
		$memory_limit = @ini_get('memory_limit');
		$max_execution_time_ini = @ini_get('max_execution_time');

		$send_at_once = mymail_option('send_at_once');
		$max_bounces = mymail_option('bounce_attempts');
		$max_execution_time = mymail_option('max_execution_time', 0);

		$sent_this_turn = 0;
		$send_delay = mymail_option('send_delay', 0)/1000;
		$mail_send_time = 0;
		$MID = mymail_option('ID');
		$unsubscribe_homepage = apply_filters('mymail_unsubscribe_link', (get_page( mymail_option('homepage') ) ? get_permalink(mymail_option('homepage')) : get_bloginfo('url')));

		$custom_field_names = mymail()->get_custom_fields(true);
		$custom_field_names = array_slice($custom_field_names, 0, 55);

		$campaign_errors = array();

		$to_send = $this->size($microtime);

		$queue_update_sql = "UPDATE {$wpdb->prefix}mymail_queue SET sent = %d, error = %d, priority = %d, count = %d WHERE subscriber_id = %d AND campaign_id = %d AND requeued = %d AND options = %s LIMIT 1";

		$this->cron_log('UTC', '<strong>' . date('Y-m-d H:i:s').' - '.time() . '</strong>');
		$this->cron_log('Local Time', '<strong>' . date('Y-m-d H:i:s', time()+$timeoffset). '</strong>');

		if($memory_limit)
			$this->cron_log('memory limit', '<strong>' . intval($memory_limit) . ' MB</strong>');

		$this->cron_log('safe_mode', '<strong>' . ($safe_mode ? 'enabled' : 'disabled') . '</strong>');
		$this->cron_log('max_execution_time', '<strong>' . $max_execution_time_ini . ' seconds</strong>');
		$this->cron_log('queue size', '<strong>' . number_format_i18n($to_send) . ' mails</strong>');
		$this->cron_log('send max at once', '<strong>'.number_format_i18n($send_at_once).'</strong>');

		if($to_send){

			$sql = "SELECT queue.campaign_id, queue.count AS _count, queue.requeued AS _requeued, queue.options AS _options, queue.priority AS _priority, subscribers.*, firstname.meta_value AS firstname, lastname.meta_value AS lastname, TRIM(CONCAT(IFNULL(firstname.meta_value, ''), ' ', IFNULL(lastname.meta_value, ''))) as fullname";

			foreach($custom_field_names as $i => $name){
				$sql .= ", meta_$i.meta_value AS '$name'";
			}

			$sql .= " FROM {$wpdb->prefix}mymail_queue AS queue";
			$sql .= " LEFT JOIN {$wpdb->posts} AS posts ON posts.ID = queue.campaign_id";
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscribers AS subscribers ON subscribers.ID = queue.subscriber_id";
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_actions AS actions ON actions.subscriber_id = queue.subscriber_id AND actions.campaign_id = queue.campaign_id AND actions.type = 1";

			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS firstname ON firstname.subscriber_id = subscribers.ID AND firstname.meta_key = 'firstname'";
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS lastname ON lastname.subscriber_id = subscribers.ID AND lastname.meta_key = 'lastname'";

			foreach($custom_field_names as $i => $name){
				$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS meta_$i ON meta_$i.subscriber_id = subscribers.ID AND meta_$i.meta_key = '$name'";
			}

			//time is in the past and errors are within the range
			$sql .= " WHERE queue.timestamp <= ".intval($microtime)." AND queue.sent = 0 AND queue.error < {$this->max_retry_after_error}";

			//post status is important or is '0' (transactional email)
			$sql .= " AND (posts.post_status IN ('finished', 'active', 'queued', 'autoresponder') OR queue.campaign_id = 0)";

			//subscriber status is 1 (subscribed) or ignore_status
			$sql .= " AND (subscribers.status = 1 OR queue.ignore_status = 1)";

			//subscriber exists or is not subscriber_id
			$sql .= " AND (subscribers.ID IS NOT NULL OR queue.subscriber_id = 0)";

			$sql .= " ORDER BY queue.priority ASC, subscribers.rating DESC";

			$sql .= !mymail_option('split_campaigns') ? ", queue.campaign_id ASC" : ", RAND()";

			$sql .= " LIMIT $send_at_once";

			$result = $wpdb->get_results($sql);

			if($wpdb->last_error)
				$this->cron_log('DB Error', '&nbsp;<span class="error">'.$wpdb->last_error.'</span>');

			$this->cron_log('subscribers found', '<strong>'.number_format_i18n(count($result)).'</strong>');

			$this->cron_log();

			$this->cron_log('#', 'email', 'campaign', 'try', 'time (sec.)');

			$userdatafields = wp_parse_args((array) $custom_field_names, array('firstname', 'lastname', 'fullname'));

			foreach($result as $i => $data){

				if(connection_aborted())
					break;

				if($max_execution_time && microtime(true)-$globaltime > $max_execution_time-1){
					$this->cron_log('', '&nbsp;<span class="error">'.__('timeout reached', 'mymail').'</span>', '', '', '');
					if(!$send_this_turn)
						mymail_notice(sprintf(__( 'MyMail is not able to send your campaign cause of a server timeout. Please increase the %1$s on the %2$s', 'mymail'), '<strong>&quot;'.__('Max. Execution Time' ,'mymail').'&quot;</strong>', '<a href="options-general.php?page=newsletter-settings&mymail_remove_notice=mymail_max_execution_time#delivery">'.__('settings page' ,'mymail').'</a>'), 'error', false, 'max_execution_time');
					break;
				}

				//only userdata
				$userdata = mymail('subscribers')->get_userdata($data);

				//only metadata
				$metadata = mymail('subscribers')->get_metadata($data, $userdata);


				$send_start_time = microtime(true);

				if($metadata->campaign_id){

					//check if status hasn't changed yet
					// if(!$wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE ID = %d AND post_status NOT IN ('paused', 'finished' ) AND post_type = 'newsletter'", $metadata->campaign_id)) && $metadata->requeued == 0){

					// 	array_push($campaign_errors, $metadata->campaign_id);
					// }

					if(in_array($metadata->campaign_id, $campaign_errors))
						continue;

					//regular campaign
					$result = mymail('campaigns')->send($metadata, $userdata, true, false, false);

					$options = false;

				}else if($metadata->_options){

					$options = unserialize($metadata->_options);

					$result = mymail('notification')->send($metadata->ID, $options);

				}else{

					continue;

				}

				$took = microtime(true)-$send_start_time;
				$mail_send_time += $took;

				//success
				if(!is_wp_error( $result )){

					$wpdb->query($wpdb->prepare($queue_update_sql, time(), 0, $data->_priority, $data->_count, $metadata->ID, $metadata->campaign_id, $data->_requeued, $metadata->_options));

					if(!$options){
						$this->cron_log($i+1, $metadata->ID.' '.$metadata->email, $metadata->campaign_id, $metadata->_count, $took > 2 ? '<span class="error">'.$took.'</span>' : $took);

						do_action('mymail_send', $metadata->ID, $metadata->campaign_id, $options);

					}else{
						$this->cron_log($i+1, print_r($options, true), $options['template'], $metadata->_count, $took > 2 ? '<span class="error">'.$took.'</span>' : $took);

					}

					$sent_this_turn++;

				//error
				}else{

					$this->cron_log($i+1, '<span class="error">'.$metadata->ID.' '.$metadata->email.'</span>', $metadata->campaign_id ? $metadata->campaign_id : $options['template'], $metadata->_count, $took > 2 ? '<span class="error">'.$took.'</span>' : $took);
					$this->cron_log('', '&nbsp;<span class="error">'.$result->get_error_message().'</span>', '', '', '');

					//user_error
					if($result->get_error_code() == 'user_error'){

						$error = $data->_count >= $this->max_retry_after_error;

						$wpdb->query($wpdb->prepare($queue_update_sql, 0, $data->_count, 15, $data->_count+1, $metadata->ID, $metadata->campaign_id, $data->_requeued, $metadata->_options));

						if($error){
							do_action('mymail_subscriber_error', $metadata->ID, $metadata->campaign_id, $result->get_error_message());

							mymail('subscribers')->change_status($metadata->ID, 4);
						}

					//notification_error
					}else if($result->get_error_code() == 'notification_error'){

						$error = $data->_count >= $this->max_retry_after_error;

						$wpdb->query($wpdb->prepare($queue_update_sql, 0, $data->_count, 15, $data->_count+1, $metadata->ID, $metadata->campaign_id, $data->_requeued, $metadata->_options));

						if($error){
							mymail_notice(sprintf(__( 'Notification %1$s has thrown an error: %2$s', 'mymail'), '<strong>&quot;'.$options['template'].'&quot;</strong>', '<strong>'.implode('',$result->get_error_messages())).'</strong>', 'error', false);

							do_action('mymail_notification_error', $metadata->ID, $result->get_error_message());
						}

					//campaign_error
					}else if($result->get_error_code() == 'error'){

						$campaign = mymail('campaigns')->get($metadata->campaign_id);

						if($campaign->post_status == 'autoresponder'){
							mymail_notice(sprintf(__( 'Autoresponder %1$s has caused sending error: %2$s', 'mymail'), '<a href="post.php?post='.$campaign->ID.'&action=edit"><strong>'.$campaign->post_title.'</strong></a>', '<strong>'.implode('',$result->get_error_messages())).'</strong>', 'updated', true, 'camp_error_'.$campaign->ID);

						}else{

							if(mymail_option('pause_campaigns')){
								mymail('campaigns')->change_status($campaign, 'paused');
								mymail_notice(sprintf(__( 'Campaign %1$s has been paused cause of a sending error: %2$s', 'mymail'), '<a href="post.php?post='.$campaign->ID.'&action=edit"><strong>'.$campaign->post_title.'</strong></a>', '<strong>'.implode('',$result->get_error_messages())).'</strong>', 'error', false, 'camp_error_'.$campaign->ID);

							}else{
								mymail_notice(sprintf(__( 'Campaign %1$s has some delay cause of a sending error: %2$s', 'mymail'), '<a href="post.php?post='.$campaign->ID.'&action=edit"><strong>'.$campaign->post_title.'</strong></a>', '<strong>'.implode('',$result->get_error_messages())).'</strong>', 'updated', true, 'camp_error_'.$campaign->ID);

							}

						}
						array_push($campaign_errors, $metadata->campaign_id);

						do_action('mymail_campaign_error', $metadata->ID, $metadata->campaign_id, $result->get_error_message());

					}

				}

				//pause between mails
				if($send_delay) usleep(max(1,round(($send_delay-(microtime(true)-$send_start_time)), 3)*1000000));

			}

		}
		$this->cron_log();

		$max_memory_usage = memory_get_peak_usage(true);

		$took = (microtime(true) - $microtime);

		if($max_memory_usage) $this->cron_log('max. memory usage','<strong>'.size_format($max_memory_usage, 2).'</strong>');

		$this->cron_log('sent this turn', $sent_this_turn);

		if($sent_this_turn){
			$this->cron_log('time', round($took,2).' sec., ('.round($mail_send_time/$sent_this_turn, 4).' sec./mail)');
			mymail_remove_notice('max_execution_time');
		}

		if (is_user_logged_in())
			$this->show_cron_log();

		delete_transient( 'mymail_cron_lock' );

		$last_hit['time'] = $took;
		$last_hit['timemax'] = max($last_hit['timemax'], $took);
		update_option('mymail_cron_lasthit', $last_hit);

		do_action('mymail_cron_finished');

		return true;

	}


	public function size($microtime = NULL) {

		global $wpdb;

		if(is_null($microtime)) $microtime = microtime(true);

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mymail_queue AS queue LEFT JOIN {$wpdb->prefix}mymail_subscribers AS subscribers ON subscribers.ID = queue.subscriber_id LEFT JOIN {$wpdb->posts} AS posts ON posts.ID = queue.campaign_id WHERE queue.timestamp <= ".intval($microtime)." AND queue.sent = 0 AND queue.error < {$this->max_retry_after_error} AND (posts.post_status IN ('finished', 'active', 'queued', 'autoresponder') OR queue.campaign_id = 0) AND (subscribers.status = 1 OR queue.ignore_status = 1) AND (subscribers.ID IS NOT NULL OR queue.subscriber_id = 0)";

		return intval($wpdb->get_var($sql));
	}



	public function cron_log() {

		global $mymail_cron_log, $mymail_cron_log_max_fields;

		if (!$mymail_cron_log) $mymail_cron_log = array();

		if ($a = func_get_args()) {
			array_unshift($a, microtime(true));
			$mymail_cron_log[] = $a;
			$mymail_cron_log_max_fields = max($mymail_cron_log_max_fields || 0, count($a));
		}else{
			$mymail_cron_log_max_fields = 0;
			$mymail_cron_log[] = array();
		}

	}

	public function show_cron_log() {

		global $mymail_cron_log, $mymail_cron_log_max_fields;

		$timeoffset = mymail('helper')->gmt_offset(true);

		$html = '<table cellpadding="0" cellspacing="0" width="100%">';
		$i = 1;
		foreach($mymail_cron_log as $logs){
			if(empty($logs)){
				$i = 1;
				$html .= '</table><table cellpadding="0" cellspacing="0" width="100%">';
				continue;
			}
			$time = array_shift($logs);
			$html .= '<tr class="'.($i%2 ? 'odd' : 'even').'">';
			foreach($logs as $j => $log){
				$html .= '<td>'.$log.'</td>';
			}
			$html .= str_repeat('<td>&nbsp;</td>', max(0, ($mymail_cron_log_max_fields+2)-$j-4));
			$html .= '<td width="50">'.date('H:i:s', $time+$timeoffset).':'.round(($time-floor($time))*10000).'</td>';
			$html .= '</tr>';
			$i++;
		}
		$html .= '</table>';
		echo $html;
	}


	public function subscriber_change_status($new_status, $old_status, $subscriber){
		if($new_status != 1){
			$this->remove_subscriber($subscriber->ID);
		}
	}

	public function unassign_lists($subscriber_ids, $lists, $not_list){
		$this->remove_subscriber($subscriber_ids);
	}

	public function update_subscriber($subscriber_id){
		$this->remove_subscriber($subscriber_id);
	}


	public function remove_subscriber($subscribers, $campaign_id = NULL){

		global $wpdb;

		$sql = "DELETE a FROM {$wpdb->prefix}mymail_queue AS a WHERE 1";
		if(!is_null($campaign_id)) $sql .= $wpdb->prepare(" AND a.campaign_id = %d", $campaign_id);

		if(!is_array($subscribers)) $subscribers = array($subscribers);
		$subscribers = array_filter($subscribers, 'is_numeric');

		$sql .= " AND a.subscriber_id IN (".implode(',', $subscribers).")";

		return false !== $wpdb->query($sql);

	}


	public function get_job_count($campaign_id = NULL, $timestamp = NULL){

		global $wpdb;

		if(is_null($timestamp)) $timestamp = time();
		if($timestamp === false) $timestamp = 0;

		if(false === ($job_counts = mymail_cache_get( 'job_counts_'.$timestamp ))){
			$sql = "SELECT a.campaign_id AS ID, COUNT(DISTINCT a.subscriber_id) AS count FROM {$wpdb->prefix}mymail_queue AS a WHERE a.sent = 0 AND a.timestamp > %d GROUP BY a.campaign_id";

			$result = $wpdb->get_results($wpdb->prepare($sql, $timestamp));
			$job_counts = array();

			foreach($result as $row){
				$job_counts[$row->ID] = intval($row->count);
			}

			mymail_cache_add( 'job_counts_'.$timestamp, $job_counts );

		}

		return (is_null($campaign_id)) ? $job_counts : (isset($job_counts[$campaign_id]) ? $job_counts[$campaign_id] : 0 );

	}


}
