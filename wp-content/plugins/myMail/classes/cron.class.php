<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_cron {


	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init' ), 1 );

	}

	public function init() {

		add_filter('cron_schedules', array( &$this, 'filter_cron_schedules'));
		add_action('mymail_cron', array( &$this, 'hourly_cronjob'));
		add_action('mymail_cron', array( &$this, 'check'));
		add_action('mymail_cron_worker', array( &$this, 'handler'), -1);
		add_action('mymail_cron_worker', array( &$this, 'check'), 99);

		add_action('mymail_campaign_pause', array( &$this, 'update'));
		add_action('mymail_campaign_start', array( &$this, 'update'));
		add_action('mymail_campaign_duplicate', array( &$this, 'update'));

		if (!wp_next_scheduled('mymail_cron')) $this->update(true);

	}


	//Checks for new newsletter in the queue to start new cronjob
	public function hourly_cronjob() {

		//check for bounced emails
		do_action('mymail_check_bounces');

		//send confirmations again
		do_action('mymail_resend_confirmations');

		if(mymail('queue')->size(time()+3600)){
			$this->update();
		}else{
			$this->remove_crons();
		}

		if(version_compare(PHP_VERSION, '5.3') < 0){
			mymail_notice('<strong>'.sprintf('MyMail requires PHP version 5.3 and above. Your current version is %s so please update or ask your provider to help you with updating!', '<code>'.PHP_VERSION.'</code>').'</strong>', 'error', false, 'minphpversion');
		}else{
			mymail_remove_notice('minphpversion');
		}

	}

	public function handler() {

		if (defined('MYMAIL_DOING_CRON') || defined('DOING_AJAX') || defined('DOING_AUTOSAVE') || defined('WP_INSTALLING') || defined('MYMAIL_DO_UPDATE')) return false;

		define('MYMAIL_DOING_CRON', microtime(true));

		register_shutdown_function( array( &$this, 'shutdown_function') );

	}


	public function shutdown_function() {

		if(!defined('MYMAIL_DOING_CRON')) return;

		$error = error_get_last();

		if( !is_null($error) && $error["type"] == 1) {

			mymail_notice('<strong>'.sprintf(__('It seems your last cronjob hasn\'t been finished! Increase the %1$s, add %2$s to your wp-config.php or reduce the %3$s in the settings' , 'mymail'), "'max_execution_time'", '<code>define("WP_MEMORY_LIMIT", "256M");</code>', '<a href="options-general.php?page=newsletter-settings&settings-updated=true#delivery">'.__('Number of mails sent', 'mymail').'</a>').'</strong>', 'error', false, 'cron_unfinished');


		}else{

			mymail_remove_notice('cron_unfinished');

		}

	}


	public function update($hourly_only = false) {

		if (!wp_next_scheduled('mymail_cron')) {

			//main schedule always 5 minutes before full hour
			wp_schedule_event(strtotime('midnight')-300, 'hourly', 'mymail_cron');
			//stop here cause mymail_cron triggers the worker if required
			return true;
		} elseif ($hourly_only) {
			return false;
		}

		//remove the WordPress cron if "normal" cron is used
		if (mymail_option('cron_service') != 'wp_cron') {
			wp_clear_scheduled_hook('mymail_cron_worker');
			return false;
		}

		//add worker only once
		if (!wp_next_scheduled('mymail_cron_worker')) {
			wp_schedule_event(floor(time()/300)*300, 'mymail_cron_interval', 'mymail_cron_worker');
			return true;
		}

		return false;

	}


	// add custom time to cron
	public function filter_cron_schedules($cron_schedules) {

		$cron_schedules['mymail_cron_interval'] = array(
			'interval' => mymail_option('interval', 5) * 60, // seconds
			'display' => 'myMail Cronjob Interval'
		);

		return $cron_schedules;
	}



	public function remove_crons($general = false) {
		wp_clear_scheduled_hook('mymail_cron_worker');
		if ($general)
			wp_clear_scheduled_hook('mymail_cron');
	}

	public function check(){

		global $wpdb;

		$now = time();
		$cron_service = mymail_option('cron_service');

		if(!mymail('queue')->size()) :

			mymail_remove_notice('check_cron');

		else:

			$interval = mymail_option('interval')*60;
			$last_hit = get_option('mymail_cron_lasthit', array(
				'ip' => mymail_get_ip(),
				'timestamp' => $now,
				'oldtimestamp' => $now-$interval,
			));

			//get real delay...
			$real_delay = max($interval, $last_hit['timestamp']-$last_hit['oldtimestamp']);
			$current_delay = $now-$last_hit['timestamp'];

			//..and compare it with the interval (3 times) - also something in the queue
			if(($current_delay > $real_delay*3 || !$real_delay && !$current_delay )) :

				if($cron_service == 'wp-cron' && defined('DISABLE_WP_CRON') && DISABLE_WP_CRON){
					mymail_notice(sprintf(__( 'The WordPress Cron is disabled! Please remove the %s constant from your wp-config.php file or switch to a real cron job!', 'mymail'), '<code>DISABLE_WP_CRON</code>'), 'error', false, 'check_cron');
				}else{
					mymail_notice(sprintf(__( 'Are your campaigns not sending? You may have to check your %1$s', 'mymail'), '<a href="options-general.php?page=newsletter-settings&mymail_remove_notice=check_cron#cron"><strong>'.__('cron settings', 'mymail').'</strong></a>'), 'error', false, 'check_cron');
				}

				$this->update();

			else:

				mymail_remove_notice('check_cron');

			endif;

		endif;

	}


	/*----------------------------------------------------------------------*/
	/* Plugin Activation / Deactivation
	/*----------------------------------------------------------------------*/



	public function on_activate($new) {

		$this->update();

	}


	public function on_deactivate() {

		$this->remove_crons(true);

	}


}
