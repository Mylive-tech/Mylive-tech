<?php if (!defined('ABSPATH')) die('not allowed');


class mymail_lists {



	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init' ) );

	}


	public function init() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 30 );

	}


	public function admin_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', __( 'Lists', 'mymail' ), __( 'Lists', 'mymail' ), 'mymail_edit_lists', 'mymail_lists', array( &$this, 'view_lists' )  );

		if(isset($_GET['ID']) || isset($_GET['new'])) :

			add_action('load-'.$page, array( &$this, 'edit_entry'), 99);

		else :

			add_action('load-'.$page, array( &$this, 'bulk_actions'), 99);
			add_filter('manage_'.$page.'_columns', array(  &$this, 'get_columns'));

		endif;

	}

	public function get_columns() {
		$columns = array(
			'cb'		=> '<input type="checkbox" />',
			'name' => __( 'Name', 'mymail' ),
			'description' => __( 'Description', 'mymail' ),
			'subscribers' => __( 'Subscribers', 'mymail' ),
			'updated' => __( 'Updated', 'mymail' ),
			'added' => __( 'Added', 'mymail' ),

		);
		return $columns;
	}

	public function view_lists( ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if(isset($_GET['ID']) || isset($_GET['new'])) :

			wp_enqueue_script('easy-pie-chart', MYMAIL_URI . 'assets/js/libs/easy-pie-chart'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);
			wp_enqueue_style('easy-pie-chart', MYMAIL_URI . 'assets/css/libs/easy-pie-chart'.$suffix.'.css', array(), MYMAIL_VERSION);

			wp_enqueue_script('mymail-list-detail', MYMAIL_URI . 'assets/js/list-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);
			wp_enqueue_style('mymail-list-detail', MYMAIL_URI . 'assets/css/list-style'.$suffix.'.css', array(), MYMAIL_VERSION);
			wp_localize_script( 'mymail-list-detail', 'mymailL10n', array(
					'next' => __('next', 'mymail'),
					'prev' => __('prev', 'mymail'),
				) );

			include MYMAIL_DIR . 'views/lists/detail.php';

		else :

			wp_enqueue_style('mymail-lists-table', MYMAIL_URI . 'assets/css/lists-table-style'.$suffix.'.css', array(), MYMAIL_VERSION);

			include MYMAIL_DIR . 'views/lists/overview.php';

		endif;
	}

	public function bulk_actions( ) {

		if(empty($_POST)) return;

		if(empty($_POST['lists'])) return;

		if ( isset( $_POST['action'] ) && -1 != $_POST['action'] )
			$action = $_POST['action'];

		if ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] )
			$action = $_POST['action2'];

		$redirect = add_query_arg($_GET);

		switch($action){

			case 'delete':
				if(current_user_can('mymail_delete_lists')){

					if($this->remove($_POST['lists']))
						mymail_notice(sprintf(__('%d Lists have been removed', 'mymail'), count($_POST['lists'])), 'error', true);

					wp_redirect( $redirect );
					exit;

				}
				break;
			case 'delete_subscribers':
				if(current_user_can('mymail_delete_lists') && current_user_can('mymail_delete_subscribers')){

					if($this->remove($_POST['lists'], true))
						mymail_notice(sprintf(__('%d Lists with subscribers have been removed', 'mymail'), count($_POST['lists'])), 'error', true);

					wp_redirect( $redirect );
					exit;

				}
				break;
			case 'subscribe':
				if($count = $this->change_status($_POST['lists'], 1)){

					mymail_notice(__('Subscribers have been subscribed', 'mymail'), 'error', true);

					wp_redirect( $redirect );
					exit;
				}
				break;
			case 'unsubscribe':
				if($this->unsubscribe($_POST['lists'])){

					mymail_notice(__('Subscribers have been unsubscribed', 'mymail'), 'error', true);

					wp_redirect( $redirect );
					exit;
				}
				break;
			case 'merge':

				if(count($_POST['lists']) == 1){

					mymail_notice(__('Please selected at least two lists!', 'mymail'), 'error', true);

					wp_redirect( $redirect );
					exit;

				}elseif($this->merge($_POST['lists'])){

					mymail_notice(sprintf(__('Lists have been merged. Please update your %s if necessary!', 'mymail'), '<a href="edit.php?post_type=newsletter">'.__('campaigns', 'mymail').'</a>'), 'updated', true);

					wp_redirect( $redirect );
					exit;
				}
				break;
			case 'send_campaign':

				$link = 'post-new.php?post_type=newsletter';
				$link = add_query_arg(array('lists' => $_POST['lists']), $link);

				wp_redirect( $link );
				exit;
				break;

		}

	}


	public function edit_entry( ) {

		if(isset($_POST['mymail_data'])){

			if(isset($_POST['save'])) :

				parse_str($_POST['_wp_http_referer'], $urlparams);

				$empty = $this->get_empty();

				//sanitize input;
				$entry = (object) (array_intersect_key($_POST['mymail_data'], (array) $empty));
				$list_id = isset($urlparams['new']) ? $this->add($entry) : $this->update($entry);

				if(is_wp_error($list_id)){

					switch($list_id->get_error_code()){
						case 'email_exists':
							$subscriber = $this->get_by_mail($entry->email);

							$msg = sprintf(__('%1$s already exists. %2$s', 'mymail'), '<strong>&quot;'.$subscriber->email.'&quot;</strong>', '<a href="edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber->ID.'">'.__('Edit this user', 'mymail').'</a>');
							break;
						default:
							$msg = $list_id->get_error_message();
					}

					mymail_notice($msg, 'error', true);

				}else{

					$list = $this->get($list_id);

					mymail_notice(isset($urlparams['new']) ? __('List added', 'mymail') : __('List saved', 'mymail'), 'updated', true);
					do_action( 'mymail_list_save', $list_id );
					wp_redirect( 'edit.php?post_type=newsletter&page=mymail_lists&ID='.$list->ID );
					exit;
				}

			elseif($_POST['delete'] || $_POST['delete_subscribers']):

				if($list = $this->get(intval($_POST['mymail_data']['ID']), false)){

					$delete_subscribers = isset($_POST['delete_subscribers']);

					if($this->remove($list->ID, $delete_subscribers)){
						mymail_notice(sprintf(__('List %s has been removed', 'mymail'), '<strong>&quot;'.$list->name.'&quot;</strong>'), 'error', true);
						do_action( 'mymail_list_delete', $list->ID );
						wp_redirect( 'edit.php?post_type=newsletter&page=mymail_lists' );
						exit;
					}
				}

			endif;


		}
	}


	public function get_empty() {
		return (object) array(
			'ID' => 0,
			'parent_id' => 0,
			'name' => '',
			'slug' => '',
			'description' => '',
			'added' => 0,
			'updated' => 0,
			'subscribers' => 0
		);
	}


	public function add_segment($entry = array(), $overwrite = false, $subscriber_ids = NULL) {

		$id = get_option('mymail_list_segment_id', 1);

		$segment = $this->get($segment_id = get_option('mymail_list_segment_parent_id'));
		if(!$segment){
			$segment_id = $this->add(array(
				'name' => __('Segments', 'mymail'),
				'description' => __('contains all segments', 'mymail'),
			), true);
			update_option('mymail_list_segment_parent_id', $segment_id);
		}

		$name = isset($entry['name']) && !empty($entry['name']) ? $entry['name'] : sprintf(__('Segment #%d', 'mymail'), $id++);

		$current_user = wp_get_current_user();

		$list_id = $this->add(wp_parse_args($entry, array(
			'name' => $name,
			'parent_id' => $segment_id,
			'slug' => sanitize_title($name).'-'.time(),
			'description' => sprintf(__('Segment created by %s', 'mymail'), $current_user->display_name),
		)), $overwrite, $subscriber_ids);

		if(!is_wp_error( $list_id )){
			update_option('mymail_list_segment_id', $id);
			return $list_id;
		}

		return false;

	}



	public function update($entry, $overwrite = true, $subscriber_ids = NULL) {

		global $wpdb;

		$entry = (array) $entry;

		$field_names = array('ID'=>'%d','parent_id'=>'%d','name'=>'%s','slug'=>'%s','description'=>'%d','added'=>'%d','updated'=>'%d');

		$now = time();

		$data = array();

		$entry = apply_filters('mymail_verify_listxw', $entry);
		if(is_wp_error( $entry )){
			return $entry;
		}else if($entry === false){
			return new WP_Error('not_verified', __('List failed verification', 'mymail'));
		}

		foreach($entry as $key => $value){
			if(isset($field_names[$key]))
				$data[$key] = $value;
		}

		if(isset($data['name']) && empty($data['name'])) $data['name'] = __('undefined', 'mymail');

		$sql = "INSERT INTO {$wpdb->prefix}mymail_lists (".implode(', ', array_keys( $data )).")";

		$sql .= " VALUES ('".implode("', '", array_map('esc_sql', array_values($data)))."')";

		if($overwrite){
			$sql .= " ON DUPLICATE KEY UPDATE updated = $now";
			foreach($data as $field => $value)
					$sql .= ", $field = values($field)";
		}

		$wpdb->suppress_errors();

		if(false !== $wpdb->query($sql)){

			if($subscriber_ids) $this->assign_subscribers($wpdb->insert_id, $subscriber_ids);

			$list_id = !empty($wpdb->insert_id) ? $wpdb->insert_id : intval($data['ID']);

			do_action('mymail_update_list', $list_id);

			return $list_id;

		}else{

			return new WP_Error('list_exists', $wpdb->last_error);
		}


	}


	public function add($entry, $overwrite = false, $subscriber_ids = NULL) {

		$now = time();

		$entry = is_string($entry) ? (object) array('name' => $entry) : (object) $entry;

		$entry = (array) $entry;

		$entry = wp_parse_args( $entry, array(
			//'ID' => NULL,
			'parent_id' => 0,
			'slug' => sanitize_title($entry['name']),
			'description' => '',
			'added' => $now,
			'updated' => $now,
		));

		add_action('mymail_update_list', array(&$this, 'update_forms'));

		return $this->update($entry, $overwrite, $subscriber_ids);

	}


	public function update_forms($list_id) {

		$forms = mymail('forms')->get_all();

		foreach($forms as $form){
			if(!$form->addlists) continue;

			mymail('forms')->assign_lists($form->ID, $list_id);
		}

	}

	public function remove_from_forms($list_id) {

		$forms = mymail('forms')->get_all();

		foreach($forms as $form){
			mymail('forms')->unassign_lists($form->ID, $list_id);
		}

	}

	public function assign_subscribers($ids, $subscriber_ids){

		global $wpdb;

		if(!is_array($ids)) $ids = array($ids);
		if(!is_array($subscriber_ids)) $subscriber_ids = array($subscriber_ids);

		$now = time();

		$inserts = array();
		foreach ($ids as $list_id) {
			foreach($subscriber_ids as $subscriber_id){
				$inserts[] = "($list_id, $subscriber_id, $now)";
			}
		}

		if(empty($inserts)) return true;

		$chunks = array_chunk($inserts, 200);

		$success = true;

		foreach($chunks as $insert){
			$sql = "INSERT INTO {$wpdb->prefix}mymail_lists_subscribers (list_id, subscriber_id, added) VALUES ";

			$sql .= " ".implode(',', $insert);

			$sql .= " ON DUPLICATE KEY UPDATE list_id = values(list_id), subscriber_id = values(subscriber_id)";

			$success = $success && (false !== $wpdb->query($sql));

		}

		return $success;

	}


	public function remove($ids, $subscribers = false){

		global $wpdb;

		$ids = is_numeric($ids) ? array($ids) : $ids;

		if($subscribers){
			$sql = "DELETE a,b,c,d,e,f FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers b ON a.ID = b.subscriber_id LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields c ON a.ID = c.subscriber_id LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS d ON a.ID = d.subscriber_id LEFT JOIN {$wpdb->prefix}mymail_actions AS e ON a.ID = e.subscriber_id LEFT JOIN {$wpdb->prefix}mymail_queue AS f ON a.ID = f.subscriber_id WHERE b.list_id IN (".implode(', ', array_filter($ids, 'is_numeric')).")";

				$wpdb->query($sql);
		}

		$sql = "DELETE a,b FROM {$wpdb->prefix}mymail_lists AS a LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers b ON a.ID = b.list_id WHERE a.ID IN (".implode(', ', array_filter($ids, 'is_numeric')).")";

		if(false !== $wpdb->query($sql)){

			foreach($ids as $list_id){
				$this->remove_from_forms($list_id);
			}

			return true;
		}

		return false;

	}


	public function subscribe($ids) {

		return $this->change_status($ids, 1);

	}

	public function unsubscribe($ids) {

		return $this->change_status($ids, 2);

	}

	public function merge($ids, $newname = NULL) {

		global $wpdb;

		//need at least 2 lists
		if(!is_array($ids) || count($ids) < 2) return false;

		$now = time();
		$ids = is_numeric($ids) ? array($ids) : $ids;
		$lists = $this->get($ids);

		if(empty($lists)) return false;

		$list_names = wp_list_pluck($lists, 'name' );
		$segment_id = get_option('mymail_list_segment_id', 1);
		$merge_list_id = get_option('mymail_merged_list_id', 1);

		$name = sprintf(__('Merged List #%d', 'mymail'), $merge_list_id);

		$new_id = $this->add(array(
			'name' => $name,
			'slug' => sanitize_title($name).'-'.$now,
			'description' => __('A merged list of', 'mymail').":\n".implode(', ', $list_names),
		));

		if(!is_wp_error( $new_id )){

			//move connections
			$sql = "UPDATE IGNORE {$wpdb->prefix}mymail_lists_subscribers SET list_id = %d, added = %d WHERE list_id IN (".implode(', ', array_filter($ids, 'is_numeric')).")";
			$wpdb->query($wpdb->prepare($sql, $new_id, $now));

			$this->remove($ids, false);

			update_option('mymail_merged_list_id', ++$merge_list_id);

			return true;

		}

		return false;

	}


	public function change_status($ids, $new_status) {

		global $wpdb;

		$ids = is_numeric($ids) ? array($ids) : $ids;

		$sql = "UPDATE {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS b ON a.ID = b.subscriber_id  SET status = %d, updated = %d WHERE b.list_id IN (".implode(', ', array_filter($ids, 'is_numeric')).")";

		return false !== $wpdb->query($wpdb->prepare($sql, $new_status, time()));

	}

	public function move_subscribers($from, $to, $added = false) {

		global $wpdb;

		$from = is_numeric($from) ? array($from) : $from;

		$sql = "UPDATE {$wpdb->prefix}mymail_lists_subscribers SET list_id = %d".($added ? ', added = '.time() : '')." WHERE {$wpdb->prefix}mymail_lists_subscribers.list_id IN (".implode(', ', array_filter($from, 'is_numeric')).");";

		return false !== $wpdb->query($wpdb->prepare($sql, $to));

	}


	public function get($id = NULL, $status = NULL, $counts = false) {

		global $wpdb;

		if(is_null($status)){
			$status = array(1);
		}elseif($status === false){
			$status = array(0,1,2,3,4,5,6);
		}
		$statuses = !is_array($status) ? array($status) : $status;
		$statuses = array_filter($statuses, 'is_numeric');

		if(is_null($id)){

			if($counts){
				$sql = "SELECT a.*, COUNT(DISTINCT b.ID) AS subscribers, CASE WHEN a.parent_id = 0 THEN a.ID*10 ELSE a.parent_id*10+1 END AS _sort FROM {$wpdb->prefix}mymail_lists AS a LEFT JOIN ( {$wpdb->prefix}mymail_subscribers AS b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.subscriber_id AND b.status IN(".implode(', ', $statuses).")) ON a.ID = ab.list_id GROUP BY a.ID ORDER BY _sort ASC";
			}else{
				$sql = "SELECT a.*, CASE WHEN a.parent_id = 0 THEN a.ID*10 ELSE a.parent_id*10+1 END AS _sort FROM {$wpdb->prefix}mymail_lists AS a ORDER BY _sort ASC";

			}

			return $wpdb->get_results($sql);

		}else if(is_numeric($id)){

			$sql = ($counts)
				? "SELECT a.*, COUNT(DISTINCT b.ID) AS subscribers FROM {$wpdb->prefix}mymail_lists AS a LEFT JOIN ( {$wpdb->prefix}mymail_subscribers AS b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.subscriber_id AND b.status IN(".implode(', ', $statuses).")) ON a.ID = ab.list_id WHERE a.ID = %d GROUP BY a.ID"
				: "SELECT a.* FROM {$wpdb->prefix}mymail_lists AS a WHERE a.ID = %d GROUP BY a.ID";

			$result = $wpdb->get_row($wpdb->prepare($sql, $id));

			return $result;

		}

		$ids = !is_array($id) ? array($id) : $id;
		$ids = array_filter($ids, 'is_numeric');

		if(empty($ids)) return array();

		return $wpdb->get_results("SELECT a.*, COUNT(DISTINCT b.ID) AS subscribers FROM {$wpdb->prefix}mymail_lists AS a LEFT JOIN ( {$wpdb->prefix}mymail_subscribers AS b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.subscriber_id AND b.status IN(".implode(', ', $ids).")) ON a.ID = ab.list_id WHERE a.ID IN(".implode(', ', $ids).") GROUP BY a.ID");


	}


	public function get_by_name($name, $field = NULL, $status = 1) {

		global $wpdb;

		if(!is_null($field) && $field != 'subscribers'){
			return $wpdb->get_var($wpdb->prepare("SELECT ".esc_sql($field)." FROM {$wpdb->prefix}mymail_lists WHERE (name = %s OR slug = %s) LIMIT 1", $name, $name));
		}

		$stati = !is_array($status) ? array($status) : $status;

		$stati = array_filter($stati, 'is_numeric');


		$result = $wpdb->get_row($wpdb->prepare("SELECT a.*, COUNT(DISTINCT ab.subscriber_id) as subscribers FROM {$wpdb->prefix}mymail_lists as a LEFT JOIN ({$wpdb->prefix}mymail_subscribers as b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.subscriber_id) ON a.ID = ab.list_id WHERE b.status IN(".implode(', ', $stati).") AND (a.name = %s OR a.slug = %s) GROUP BY a.ID", $name, $name));

		if(is_null($field)) return $result;

		if(isset($result->{$field})) return $result->{$field};

		return false;

	}



	public function count($lists = NULL, $statuses = NULL){

		global $wpdb;

		if($lists && !is_array($lists)) $lists = array($lists);
		if(!is_null($statuses) && !is_array($statuses)) $statuses = array($statuses);

		if(is_array($lists)) $lists = array_filter($lists, 'is_numeric');
		if(is_array($statuses)) $statuses = array_filter($statuses, 'is_numeric');

		$sql = "SELECT COUNT(DISTINCT a.ID) FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN ({$wpdb->prefix}mymail_lists AS b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.list_id) ON a.ID = ab.subscriber_id WHERE 1=1";

		$sql .= (is_array($lists))
					? " AND b.ID IN (".implode(',', $lists).")"
					: ($lists === false ? " AND b.ID IS NULL" : '');

		if(is_array($statuses))
			$sql .= " AND a.status IN (".implode(',', $statuses).")";

		$result = $wpdb->get_var($sql);

		return $result ? intval($result) : 0;

	}

	public function get_list_count(){

		global $wpdb;

		$sql = "SELECT COUNT( * ) AS count FROM {$wpdb->prefix}mymail_lists";

		return $wpdb->get_var($sql);
	}

	public function get_total($campaign_id){

		return 123;

	}

	public function get_sent($list_id, $total = false) {

		return mymail('actions')->get_by_list($list_id, 'sent'.($total ? '_total' : '' ), true);

	}
	public function get_opens($list_id, $total = false) {

		return mymail('actions')->get_by_list($list_id, 'opens'.($total ? '_total' : '' ), true);

	}
	public function get_clicks($list_id, $total = false) {

		return mymail('actions')->get_by_list($list_id, 'clicks'.($total ? '_total' : '' ), true);

	}
	public function get_unsubscribes($list_id, $total = false) {

		return mymail('actions')->get_by_list($list_id, 'unsubscribes'.($total ? '_total' : '' ), true);

	}
	public function get_bounces($list_id, $total = false) {

		return mymail('actions')->get_by_list($list_id, 'bounces'.($total ? '_total' : '' ), true);

	}

	public function get_activity($id, $limit = NULL, $exclude = NULL) {

		return mymail('actions')->get_list_activity($id, $limit, $exclude);

	}

	public function get_member_count($list_id = NULL, $statuses = NULL){

		global $wpdb;

		$statuses = !is_null($statuses) && !is_array($statuses) ? array($statuses) : $statuses;
		$key = 	is_array($statuses) ? 'list_counts_'.implode('|', $statuses) : 'list_counts';

		if ( false === ($list_counts = mymail_cache_get( $key )) ) {

			$sql = "SELECT a.ID, a.parent_id, COUNT(DISTINCT ab.subscriber_id) AS count FROM {$wpdb->prefix}mymail_lists AS a LEFT JOIN ({$wpdb->prefix}mymail_subscribers AS b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.subscriber_id) ON a.ID = ab.list_id";

			if(is_array($statuses))
				$sql .= " AND b.status IN (".implode(',', array_filter($statuses, 'is_numeric')).")";

			$sql .= " GROUP BY a.ID";

			$result = $wpdb->get_results($sql);

			$list_counts = array();

			foreach($result as $list){
				if(!isset($list_counts[$list->ID])) $list_counts[$list->ID] = 0;
				$list_counts[$list->ID] += intval($list->count);
				if($list->parent_id) $list_counts[$list->parent_id] += intval($list->count);
			}

			mymail_cache_add( $key, $list_counts );

		}

		if(is_null($list_id)) return $list_counts;

		return isset($list_counts[$list_id]) && isset($list_counts[$list_id]) ? intval($list_counts[$list_id]) : 0;


	}

	public function print_it($id = NULL, $status = NULL, $name = 'mymail_lists', $show_count = true, $checked = array()){

		if($lists = $this->get($id, $status, !!$show_count)){

			if(!is_array($checked)) $checked = array($checked);

			echo '<ul>';
			foreach($lists as $list){
				echo '<li><label title="'.($list->description ? $list->description : $list->name).'">'.($list->parent_id ? '&nbsp;┗&nbsp;' : '').'<input type="checkbox" value="'.$list->ID.'" name="'.$name.'[]" '.checked(in_array($list->ID, $checked), true, false).' class="list'.($list->parent_id ? ' list-parent-'.$list->parent_id : '').'"> '.$list->name.''.($show_count ? ' <span class="count">('.number_format_i18n($list->subscribers).(is_string($show_count) ? ' '.$show_count : '').')</span>' : '').'</label></li>';
			}
			echo '</ul>';

		}else{
			echo '<ul><li>'.__('No Lists found!' ,'mymail').'</li><li><a href="edit.php?post_type=newsletter&page=mymail_lists&new">'.__('Create a List now' ,'mymail').'</a></li></ul>';
		}

	}


	public function on_activate($new) {


	}



}
