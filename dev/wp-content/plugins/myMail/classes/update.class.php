<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_update {

	private $performance = 1;
	private $starttime;

	public function __construct() {

		register_activation_hook  ( MYMAIL_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook  ( MYMAIL_FILE, array( &$this, 'deactivate' ) );

		add_action('init', array( &$this, 'init' ), 1 );

	}

	public function init() {


		add_action('admin_menu', array( &$this, 'admin_menu'));
		add_action('wp_ajax_mymail_batch_update', array( &$this, 'run_update'));

		if(is_admin() && !defined('DOING_AJAX')){

			global $pagenow;

			$old_version = get_option('mymail_version');

			if ($old_version != MYMAIL_VERSION) {

				if (version_compare($old_version, MYMAIL_VERSION, '<')) {
					include MYMAIL_DIR . 'includes/updates.php';
				}

				$this->check_db_version();

				update_option('mymail_version', MYMAIL_VERSION);

			}

			if(mymail_option('update_required')) {

				$db_version = get_option('mymail_dbversion');

				if (version_compare($db_version, MYMAIL_DBVERSION, '<') && $pagenow != 'update.php') {
					$redirectto = 'edit.php?post_type=newsletter&page=mymail_update';

					if(isset($_GET['post_type']) && $_GET['post_type'] == 'newsletter' && isset($_GET['page']) && $_GET['page'] == 'mymail_update'){
					}else{
						if(!is_network_admin() && isset($_GET['post_type']) && $_GET['post_type'] = 'newsletter'){
							wp_redirect($redirectto);
							exit;
						}else{
							mymail_notice('<h4>'.sprintf(__( 'An additional update is required!. Please visit %s to finish the update progress', 'mymail'), '<a class="" href="'.$redirectto.'">'.__('the update page', 'mymail').'</a>').'</h4>', 'error', true, 'update_required');
						}
					}
				}

			}else if(mymail_option('welcome')) {

				$this->check_db_version();

				if(!is_network_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] = 'newsletter'){
					mymail_update_option('welcome', false);
					wp_redirect('edit.php?post_type=newsletter&page=mymail_welcome');
					exit;
				}

			}
		}

	}


	public function run_update() {

		//cron look
		set_transient( 'mymail_cron_lock', microtime(true), 360 );

		global $mymail_batch_update_output;

		$this->starttime = microtime();

		$return['success'] = false;

		$id = $_POST['id'];
		$this->performance = isset($_POST['performance']) ? intval($_POST['performance']) : $this->performance;

		if(method_exists($this, 'do_'.$id)){
			$return['success'] = true;
			ob_start();
			$return[$id] = $this->{'do_'.$id}();
			$output = ob_get_contents();
			ob_end_clean();
			if(!empty($output)){
				$return['output']  = "======================================================\n";
				$return['output'] .= "* OUTPUT for $id (".date('Y-m-d H:i:s', current_time('timestamp')).")\n";
				$return['output'] .= "======================================================\n";
				$return['output'] .= strip_tags($output)."\n";
			}
		}


		@header( 'Content-type: application/json' );
		echo json_encode($return);
		exit;

	}


	public function admin_menu($args) {

		$page = add_submenu_page(NULL, 'MyMail Update', 'MyMail Update', 'manage_options', 'mymail_update', array( &$this, 'page' ));
		add_action('load-' . $page, array( &$this, 'scripts_styles'));

	}

	public function scripts_styles() {
		wp_register_script('mymail-update-script', MYMAIL_URI . 'assets/js/update-script.js', array('jquery'), MYMAIL_VERSION);
		wp_enqueue_script('mymail-update-script');

		$db_version = get_option('mymail_dbversion', 0);

		$actions = array('db_structure' => 'checking DB structure');

		if(isset($_GET['hard'])) {
			$db_version = 0;
			$actions = wp_parse_args( $actions, array('remove_db_structure' => 'removing DB structure') );
		}
		if(isset($_GET['redo'])) {
			$db_version = 0;
		}

		if ( $db_version < 20140924 ) {
			$actions = wp_parse_args(array(
				'update_lists' => 'updating Lists',
				'update_campaign' => 'updating Campaigns',
				'update_subscriber' => 'updating Subscriber',
				'update_list_subscriber' => 'update Lists <=> Subscribers',
				'update_actions' => 'updating Actions',
				'update_pending' => 'updating Pending Subscribers',
				'update_autoresponder' => 'updating Autoresponder',
				'update_settings' => 'updating Settings',
			), $actions);
		}

		if(isset($_GET['removeold'])) {
			$actions = wp_parse_args( array('remove_old_data' => 'Removing MyMail 1.x data'), $actions );
		}

		$actions = wp_parse_args( array(
			'cleanup' => 'cleanup'
		), $actions );


		wp_localize_script('mymail-update-script', 'mymail_updates', $actions);
		$performance = isset($_GET['performance']) ? max(1, intval($_GET['performance'])) : 1;
		wp_localize_script('mymail-update-script', 'mymail_updates_performance', array($performance));
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

	}

	public function page() {

	?>
	<div class="wrap">
		<h2>MyMail Update</h2>
		<?php wp_nonce_field( 'mymail_nonce', 'mymail_nonce', false );
		?>
		<p><strong>Some additional updates are required! Please keep this browser tab open until all updates are finished!</strong></p>
		<p>MyMail will now import all MyMail 1.x data to the new structure of MyMail 2</p>
		<?php if(get_option('mymail_dbversion', 0)) :?>
		<p>
			<form action="" method="get">
			<input type="hidden" name="post_type" value="newsletter">
			<input type="hidden" name="page" value="mymail_update">
				<input type="submit" class="button button-primary" name="removeold" value="Remove MyMail 1.x data" onclick="return confirm('You are about to delete all old MyMail 1.x data.\n\nPlease make sure all campaigns, subscribers and data has been transfered correctly to MyMail 2.\n\nContinue?');">
				<input type="submit" class="button button-small" name="redo" value="redo update" onclick="return confirm('Do you really like to redo the update?');">
				<input type="submit" class="button button-small" name="hard" value="remove all MyMail 2 data and start over" onclick="return confirm('Do you really like to start over again and delete all NEW data from MyMail 2?\n\nThis cannot be undone!');">
			</form>
		</p>
		<?php endif; ?>
		<div class="alignleft" style="width:54%">
			<div id="output"></div>
			<div id="error-list"></div>
		</div>

		<div class="alignright" style="width:45%">
			<textarea id="textoutput" class="widefat" rows="30" style="width:100%;font-size:12px;font-family:monospace"></textarea>
		</div>

	</div>
	<?php
	}


	private function do_remove_db_structure(){

		global $wpdb;

		$sqls = array(
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_actions",
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_links",
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_campaigns",
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_lists",
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_lists_subscribers",
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_queue",
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_subscribers",
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_subscriber_fields",
			"DROP TABLE IF EXISTS {$wpdb->prefix}mymail_subscriber_meta",
		);

		foreach($sqls as $sql){
			if(false !== $wpdb->query($sql)){
				echo $sql."\n";
			}
		}

		return true;
	}

	private function do_remove_old_data(){

		global $wpdb;

		if($count = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'mymail-campaign' LIMIT 1000")){
			echo 'old Campaign Data removed'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'mymail-campaigns' LIMIT 1000")){
			echo 'old Campaign related User Data removed'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'mymail-userdata' LIMIT 10000")){
			echo 'old User Data removed'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'mymail-data' LIMIT 1000")){
			echo 'old User Data removed'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE m FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id WHERE p.post_type = 'subscriber' AND m.post_id")){
			echo 'old User related data removed'."\n";
			return false;
		}
		// if($count = $wpdb->query("DELETE t FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->term_relationships} AS t ON p.ID = t.object_id WHERE p.post_type = 'subscriber' AND t.object_id")){
		// 	echo 'old User related data removed'."\n";
		// 	return false;
		// }
		if($count = $wpdb->query("DELETE a,b,c FROM {$wpdb->term_taxonomy} AS a LEFT JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id JOIN {$wpdb->term_taxonomy} AS c ON c.term_taxonomy_id = a.term_taxonomy_id WHERE a.taxonomy = 'newsletter_lists'")){
			echo 'old Lists removed'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'subscriber' LIMIT 10000")){
			echo $count.' old User removed'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name = 'mymail_confirms'")){
			echo $count.' old Pending User removed'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name = 'mymail_autoresponders'")){
			echo $count.' old Autoresponder Data'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name = 'mymail_subscribers_count'")){
			echo $count.' old Cache'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mymail_bulk_%'")){
			echo $count.' old import data'."\n";
			return false;
		}
		if($count = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name IN ('mymail_countries', 'mymail_cities')")){
			echo $count.' old data'."\n";
			return false;
		}

		return true;

	}

	private function do_db_structure(){
		return mymail()->dbstructure(true, true);
	}


	private function do_update_lists(){

		global $wpdb;

		$now = time();

		$limit = ceil(25*$this->performance);

		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->terms} AS a LEFT JOIN {$wpdb->term_taxonomy} as b ON b.term_id = a.term_id LEFT JOIN {$wpdb->prefix}mymail_lists AS c ON c.ID = a.term_id WHERE b.taxonomy = 'newsletter_lists' AND c.ID IS NULL");

		echo $count.' lists left'."\n";

		$sql = "SELECT a.term_id AS ID, a.name, a.slug, b.description FROM {$wpdb->terms} AS a LEFT JOIN {$wpdb->term_taxonomy} as b ON b.term_id = a.term_id LEFT JOIN {$wpdb->prefix}mymail_lists AS c ON c.ID = a.term_id WHERE b.taxonomy = 'newsletter_lists' AND c.ID IS NULL LIMIT $limit";

		$lists = $wpdb->get_results($sql);
		if(!count($lists)) return true;

		foreach($lists as $list){
			$sql = "INSERT INTO {$wpdb->prefix}mymail_lists (ID, parent_id, name, slug, description, added, updated) VALUES (%d, '0', %s, %s, %s, %d, %d)";

			if(false !== $wpdb->query($wpdb->prepare($sql, $list->ID, $list->name, $list->slug, $list->description, $now, $now))){
				echo 'added list '.$list->name."\n";
			}
		}

		return false;

	}
	private function do_update_campaign(){

		global $wpdb;

		$limit = ceil(25*$this->performance);

		$timeoffset = mymail('helper')->gmt_offset(true);

		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} AS m LEFT JOIN {$wpdb->posts} AS p ON p.ID = m.post_id LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id LEFT JOIN {$wpdb->postmeta} AS b ON b.post_id = p.ID AND b.meta_key = '_mymail_timestamp' WHERE m.meta_key = 'mymail-data' AND c.meta_key = 'mymail-campaign' AND p.post_type = 'newsletter' AND b.meta_key IS NULL");

		echo $count.' campaigns left'."\n";

		$sql = "SELECT p.ID, p.post_title, p.post_status, m.meta_value as meta, c.meta_value AS campaign FROM {$wpdb->postmeta} AS m LEFT JOIN {$wpdb->posts} AS p ON p.ID = m.post_id LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id LEFT JOIN {$wpdb->postmeta} AS b ON b.post_id = p.ID AND b.meta_key = '_mymail_timestamp' WHERE m.meta_key = 'mymail-data' AND c.meta_key = 'mymail-campaign' AND p.post_type = 'newsletter' AND b.meta_key IS NULL LIMIT $limit";

		$campaigns = $wpdb->get_results($sql);

		//no campaigns left => update ok
		if(!count($campaigns)) return true;

		foreach ($campaigns as $data) {

			$meta = $this->unserialize($data->meta);

			$campaign = wp_parse_args(array(
				'original_campaign' => '',
				'finished' => '',
				'timestamp' => '',
				'totalerrors' => '',
			), $this->unserialize($data->campaign));

			//$lists = $wpdb->get_results($wpdb->prepare("SELECT b.* FROM {$wpdb->term_relationships} AS a LEFT JOIN {$wpdb->terms} AS b ON b.term_id = a. term_taxonomy_id WHERE object_id = %d", $data->ID));
			$lists = $wpdb->get_results($wpdb->prepare("SELECT b.* FROM {$wpdb->term_relationships} AS a LEFT JOIN {$wpdb->term_taxonomy} AS b ON b.term_taxonomy_id = a.term_taxonomy_id WHERE object_id = %d", $data->ID));

			$listids = wp_list_pluck( $lists, 'term_id' );

			if($data->post_status == 'autoresponder'){
				$autoresponder = $meta['autoresponder'];
				$active = isset($meta['active_autoresponder']) && $meta['active_autoresponder'];
				$timestamp = isset($autoresponder['timestamp']) ? $autoresponder['timestamp'] : strtotime($autoresponder['date'].' '.$autoresponder['time']);

			}else{
				$autoresponder = '';
				$active = isset($meta['active']) && $meta['active'] && !$campaign['finished'];
				$timestamp = isset($meta['timestamp']) ? $meta['timestamp'] : time();
			}

			$timestamp = $timestamp-$timeoffset;

			if($data->post_status == 'finished'){
				$campaign['finished'] = $campaign['finished'] ? $campaign['finished']-$timeoffset : $timestamp;
			}

			$values = array(
				//'campaign_id' => $data->ID,
				'parent_id' => $campaign['original_campaign'],
				'timestamp' => $timestamp,
				'finished' => $campaign['finished'],
				'active' => $active, //all campaigns inactive
				//'sent' => $campaign['finished'] ? $campaign['sent'] : 0,
				//'error' => $campaign['totalerrors'],
				'from_name' => $meta['from_name'],
				'from_email' => $meta['from'],
				'reply_to' => $meta['reply_to'],
				'subject' => $meta['subject'],
				'preheader' => $meta['preheader'],
				'template' => $meta['template'],
				'file' => $meta['file'],
				'lists' => array_unique($listids),
				'ignore_lists' => 0,
				'autoresponder' => $autoresponder,
				'head' => trim($meta['head']),
				'background' => $meta['background'],
				'colors' => ($meta['newsletter_color']),
				'embed_images' => isset($meta['embed_images']),
			);

			//return false;

			if($data->post_status == 'active'){
				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->posts} SET post_status = 'queued' WHERE ID = %d AND post_type = 'newsletter'", $data->ID));
			}

			mymail('campaigns')->update_meta( $data->ID, $values );

			echo 'updated campaign '.$data->post_title."\n";


		}

		return false;
	}




	private function do_update_subscriber(){

		global $wpdb;

		$timeoffset = mymail('helper')->gmt_offset(true);

		$limit = ceil(500*$this->performance);

		$now = time();

		//$wpdb->query("ALTER TABLE {$wpdb->prefix}mymail_subscribers CHARACTER SET utf8 COLLATE utf8_general_ci");

		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->prefix}mymail_subscribers AS s ON s.ID = p.ID LEFT JOIN {$wpdb->prefix}mymail_subscribers AS s2 ON s2.email = p.post_title LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id AND c.meta_key = 'mymail-campaigns' LEFT JOIN {$wpdb->postmeta} AS u ON p.ID = u.post_id AND u.meta_key = 'mymail-userdata' WHERE p.post_type = 'subscriber' AND post_status IN ('subscribed', 'unsubscribed', 'hardbounced', 'error') AND s.ID IS NULL AND (s2.email != p.post_title OR s2.email IS NULL)");

		//$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->prefix}mymail_subscribers AS s ON s.ID = p.ID LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id AND c.meta_key = 'mymail-campaigns' LEFT JOIN {$wpdb->postmeta} AS u ON p.ID = u.post_id AND u.meta_key = 'mymail-userdata' WHERE p.post_type = 'subscriber' AND post_status IN ('subscribed', 'unsubscribed', 'hardbounced', 'error') AND s.ID IS NULL");

		echo $count.' subscribers left'."\n\n";

		$sql = "SELECT p.ID, p.post_title AS email, p.post_status AS status, p.post_name AS hash, c.meta_value as campaign, u.meta_value as userdata FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->prefix}mymail_subscribers AS s ON s.ID = p.ID LEFT JOIN {$wpdb->prefix}mymail_subscribers AS s2 ON s2.email = p.post_title LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id AND c.meta_key = 'mymail-campaigns' LEFT JOIN {$wpdb->postmeta} AS u ON p.ID = u.post_id AND u.meta_key = 'mymail-userdata' WHERE p.post_type = 'subscriber' AND post_status IN ('subscribed', 'unsubscribed', 'hardbounced', 'error') AND s.ID IS NULL AND (s2.email != p.post_title OR s2.email IS NULL) GROUP BY p.ID ORDER BY p.post_title ASC LIMIT $limit";

		//$sql = "SELECT p.ID, p.post_title AS email, p.post_status AS status, p.post_name AS hash, c.meta_value as campaign, u.meta_value as userdata FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->prefix}mymail_subscribers AS s ON s.ID = p.ID LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id AND c.meta_key = 'mymail-campaigns' LEFT JOIN {$wpdb->postmeta} AS u ON p.ID = u.post_id AND u.meta_key = 'mymail-userdata' WHERE p.post_type = 'subscriber' AND post_status IN ('subscribed', 'unsubscribed', 'hardbounced', 'error') AND s.ID IS NULL AND (s.email != p.post_title OR s.email IS NULL) GROUP BY p.ID ORDER BY p.post_title ASC LIMIT $limit";

		//$sql = "SELECT p.ID, p.post_title AS email, p.post_status AS status, p.post_name AS hash, c.meta_value as campaign, u.meta_value as userdata FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->prefix}mymail_subscribers AS s ON s.ID = p.ID LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id AND c.meta_key = 'mymail-campaigns' LEFT JOIN {$wpdb->postmeta} AS u ON p.ID = u.post_id AND u.meta_key = 'mymail-userdata' WHERE p.post_type = 'subscriber' AND post_status IN ('subscribed', 'unsubscribed', 'hardbounced', 'error') AND s.ID IS NULL GROUP BY p.ID ORDER BY p.post_title ASC LIMIT $limit";

		$users = $wpdb->get_results($sql);

		$count = count($users);

		//no users left => update ok
		if(!$count) return true;

		foreach ($users as $data) {
			$userdata = $this->unserialize($data->userdata);

			$meta = array(
				'confirmtime' => 0,
				'signuptime' => 0,
				'signupip' => '',
				'confirmip' => '',
			);

			if(is_array($userdata) && isset($userdata['_meta'])){
				$meta = wp_parse_args($userdata['_meta'], $meta);
				unset($userdata['_meta']);
			}

			$status = mymail('subscribers')->get_status_by_name($data->status);

			$values = array(
				'ID' => $data->ID,
				'email' => addcslashes($data->email, "'"),
				'hash' => $data->hash,
				'status' => $status,
				'added' => isset($meta['imported']) ? $meta['imported'] : (isset($meta['confirmtime']) ? $meta['confirmtime'] : $now),
				'updated' => $now,
				'signup' => $meta['signuptime'],
				'confirm' => $meta['confirmtime'],
				'ip_signup' => $meta['signupip'],
				'ip_confirm' => $meta['confirmip'],
			);

			$campaign_data = $this->unserialize($data->campaign);

			$sql = "INSERT INTO {$wpdb->prefix}mymail_subscribers (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."') ON DUPLICATE KEY UPDATE updated = values(updated);";

			if(false !== $wpdb->query($sql)){

				echo 'added '.$data->email."\n";
				// $lists = $wpdb->get_results($wpdb->prepare("SELECT b.* FROM {$wpdb->term_relationships} AS a LEFT JOIN {$wpdb->terms} AS b ON b.term_id = a.term_taxonomy_id WHERE object_id = %d", $data->ID));

				// $listids = wp_list_pluck( $lists, 'term_id' );

				// foreach($listids as $listid){
				// 	$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}mymail_lists_subscribers (list_id, subscriber_id, added) VALUES (%d, %d, %d)", $listid, $data->ID, $now );

				// 	$wpdb->query($sql);
				// }

				$this->update_customfields($data->ID);
				echo "\n";


			}


		}


	//not finished yet (but successfull)
		return false;

	}


	private function do_update_list_subscriber(){

		global $wpdb;

		$limit = ceil(500*$this->performance);

		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->term_relationships} AS a LEFT JOIN {$wpdb->term_taxonomy} AS b ON a.term_taxonomy_id = b.term_taxonomy_id LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS c ON c.subscriber_id = a.object_id AND c.list_id = b.term_id WHERE b.taxonomy = 'newsletter_lists' AND c.subscriber_id IS NULL");

		echo $count.' list - subscriber connections left'."\n\n";

		$sql = "SELECT a.object_id AS subscriber_id, b.term_id AS list_id FROM {$wpdb->term_relationships} AS a LEFT JOIN {$wpdb->term_taxonomy} AS b ON a.term_taxonomy_id = b.term_taxonomy_id LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS c ON c.subscriber_id = a.object_id AND c.list_id = b.term_id WHERE b.taxonomy = 'newsletter_lists' AND c.subscriber_id IS NULL LIMIT $limit";

		$connections = $wpdb->get_results($sql);
		if(!count($connections)) return true;

		$inserts = array();

		$now = time();

		$sql = "INSERT INTO {$wpdb->prefix}mymail_lists_subscribers (list_id, subscriber_id, added) VALUES";

		foreach($connections as $connection){
			$inserts[] = $wpdb->prepare('(%d, %d, %d)', $connection->list_id, $connection->subscriber_id, $now);
		}

		if(empty($inserts)) return true;

		$sql .= implode(',' , $inserts);

		$wpdb->query($sql);

		return false;
	}



	private function update_customfields($id){
		global $wpdb;

		$timeoffset = mymail('helper')->gmt_offset(true);

		$now = time();

		$id = intval($id);

		$sql = "SELECT a.meta_value AS meta FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS b ON b.subscriber_id = a.post_id WHERE a.meta_key = 'mymail-userdata' AND b.subscriber_id IS NULL AND a.post_id = %d LIMIT 1";

		if($usermeta = $wpdb->get_var($wpdb->prepare($sql, $id))){

			$userdata = $this->unserialize($usermeta);
			if(!is_array($userdata)){
				'ERROR: Corrupt data: "'.$userdata.'"';
				return;
			}

			$meta = array();
			if(isset($userdata['_meta'])){
				$meta = $userdata['_meta'];
				unset($userdata['_meta']);
			}

			foreach($userdata as $field => $value){
				if($value == '') continue;
				$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}mymail_subscriber_fields (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $id, trim($field), trim($value) );

				if(false !== $wpdb->query($sql)){
					echo "added field '$field' => '$value' \n";
				}

			}

			foreach($meta as $field => $value){
				if($value == '' || !in_array($field, array('ip', 'lang'))) continue;
				$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}mymail_subscriber_meta (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $id, trim($field), trim($value) );

				if(false !== $wpdb->query($sql)){
					echo "added meta field '$field' => '$value' \n";
				}

			}

		}

	}


	private function do_update_customfields(){

		global $wpdb;

		$timeoffset = mymail('helper')->gmt_offset(true);

		$limit = ceil(2500*$this->performance);

		$now = time();

		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS b ON b.subscriber_id = a.post_id WHERE a.meta_key = 'mymail-userdata' AND b.subscriber_id IS NULL");

		echo $count.' customfields left'."\n\n";

		$sql = "SELECT a.post_id AS ID, a.meta_value AS meta FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS b ON b.subscriber_id = a.post_id WHERE a.meta_key = 'mymail-userdata' AND b.subscriber_id IS NULL LIMIT $limit";

		$usermeta = $wpdb->get_results($sql);

		//no usermeta left => update ok
		if(!count($usermeta)) return true;

		foreach ($usermeta as $data) {
			$userdata = $this->unserialize($data->meta);
			$meta = array();
			if(isset($userdata['_meta'])){
				$meta = $userdata['_meta'];
				unset($userdata['_meta']);
			}

			if(empty($userdata)){
				$sql = "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = 'mymail-userdata'";
				$wpdb->query($wpdb->prepare($sql, $data->ID));
			}


			foreach($userdata as $field => $value){
				if($value == '') continue;
				$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}mymail_subscriber_fields (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s)", $data->ID, trim($field), trim($value) );

				$wpdb->query($sql);

			}
			foreach($meta as $field => $value){
				if($value == '' || !in_array($field, array('ip', 'lang'))) continue;
				$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}mymail_subscriber_meta (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $data->ID, trim($field), trim($value) );

				$wpdb->query($sql);

			}
			echo 'added fields for '.$data->ID."\n";

		}


		//not finished yet (but successfull)
		return false;

	}


	private function do_update_actions(){

		global $wpdb;

		$timeoffset = mymail('helper')->gmt_offset(true);

		$limit = ceil(500*$this->performance);

		$offset = get_transient( 'mymail_do_update_actions' );

		if(!$offset) $offset = 0;

		$now = time();

		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.post_id = b.subscriber_id AND a.meta_key = 'mymail-campaigns' WHERE b.subscriber_id IS NULL AND a.meta_key = 'mymail-campaigns' AND a.meta_value != 'a:0:{}' ORDER BY a.post_id ASC");

		echo $count.' actions left'."\n\n";

		$sql = "SELECT a.post_id AS ID, a.meta_value AS meta FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.post_id = b.subscriber_id AND a.meta_key = 'mymail-campaigns' WHERE b.subscriber_id IS NULL AND a.meta_key = 'mymail-campaigns' AND a.meta_value != 'a:0:{}' GROUP BY a.post_id ORDER BY a.post_id ASC LIMIT $limit";

		$campaignmeta = $wpdb->get_results($sql);

		//nothing left
		if(!count($campaignmeta)){
			delete_transient( 'mymail_do_update_actions' );
			return true;
		}

		$bounce_attempts = mymail_option('bounce_attempts');

		$old_unsubscribelink = add_query_arg(array('unsubscribe' => ''), get_permalink(mymail_option('homepage')));
		$new_unsubscribelink = mymail()->get_unsubscribe_link();

		foreach ($campaignmeta as $data) {

			$userdata = $this->unserialize($data->meta);

			foreach($userdata as $campaign_id => $infos){

				$default = array(
					'subscriber_id' => $data->ID,
					'campaign_id' => $campaign_id,
					//'added' => $now,
					'count' => 1,
				);
				foreach($infos as $info_key => $info_value){

					echo 'added action '.$info_key." => ".$info_value."\n";
					switch($info_key){
						case 'sent':

							if(gettype($info_value) == 'boolean' && !$info_value) $info_value = $now;

							if($info_value){
								$values = wp_parse_args( array(
									'timestamp' => $info_value,
									'type' => 1
								), $default );

								$wpdb->query("INSERT INTO {$wpdb->prefix}mymail_actions (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)");
							}else{

								$values = wp_parse_args( array(
									'timestamp' => $now,
									'sent' => $info_value,
									'priority' => 10,
								), $default );

								$wpdb->query("INSERT INTO {$wpdb->prefix}mymail_queue (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)");
							}

						break;
						case 'open':
							$values = wp_parse_args( array(
								'timestamp' => $info_value,
								'type' => 2
							), $default );

							$wpdb->query("INSERT INTO {$wpdb->prefix}mymail_actions (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)");
						break;

						case 'clicks':
							foreach($info_value as $link => $count){

								//new unsubscribe links
								if($link == $old_unsubscribelink){
									$link = $new_unsubscribelink;
								}

								$values = wp_parse_args( array(
									'timestamp' => $infos['firstclick'],
									'type' => 3,
									'link_id' => mymail('actions')->get_link_id($link, 0),
									'count' => $count,
								), $default );

								$wpdb->query("INSERT INTO {$wpdb->prefix}mymail_actions (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)");

							}
						break;

						case 'unsubscribe':
							$values = wp_parse_args( array(
								'timestamp' => $info_value,
								'type' => 4
							), $default );

							$wpdb->query("INSERT INTO {$wpdb->prefix}mymail_actions (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)");

						break;

						case 'bounces':
							$values = wp_parse_args( array(
								'timestamp' => $now,
								'type' => $info_value >= $bounce_attempts ? 6 : 5,
								'count' => $info_value >= $bounce_attempts ? $bounce_attempts : 1,
							), $default );

							$wpdb->query("INSERT INTO {$wpdb->prefix}mymail_actions (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)");

						break;

					}

				}
			}

		}

		set_transient( 'mymail_do_update_actions', $offset+$limit );

		//not finished yet (but successfull)
		return false;

		return new WP_Error('update_error', 'An error occured during batch update');

	}

	private function do_update_pending(){

		global $wpdb;

		$timeoffset = mymail('helper')->gmt_offset(true);

		$now = time();

		$limit = ceil(25*$this->performance);

		$pending = get_option('mymail_confirms', array());

		$i = 0;

		foreach($pending as $hash => $user){

			$userdata = $user['userdata'];
			$meta = array();
			if(isset($userdata['_meta'])){
				$meta = $userdata['_meta'];
				unset($userdata['_meta']);
			}

			$values = array(
				'email' => $userdata['email'],
				'hash' => $hash,
				'status' => 0,
				'added' => $user['timestamp'],
				'updated' => $now,
				'signup' => $user['timestamp'],
				'ip_signup' => $meta['signupip'],
			);

			$sql = "INSERT INTO {$wpdb->prefix}mymail_subscribers (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."')";

			if(false !== $wpdb->query($sql)){

				$subscriber_id = $wpdb->insert_id;

				// $metavalues = array(
				// 	'subscriber_id' => $subscriber_id,
				// 	'campaign_id' => 0,
				// 	'added' => $user['timestamp'],
				// 	'timestamp' => $user['timestamp'],
				// 	'sent' => $user['last'],
				// 	'priority' => 5,
				// 	'count' => $user['try']+1,
				// );

				unset($userdata['email']);

				foreach($userdata as $field => $value){
					if($value == '') continue;
					$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}mymail_subscriber_fields (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $subscriber_id, trim($field), trim($value) );

					if(false !== $wpdb->query($sql)){
						echo "added field '$field' => '$value' \n";
					}
				}

				foreach($meta as $field => $value){
					if($value == '' || !in_array($field, array('ip', 'lang'))) continue;
					$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}mymail_subscriber_meta (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $subscriber_id, trim($field), trim($value) );

					if(false !==$wpdb->query($sql)){
						echo "added meta field '$field' => '$value' \n";
					}

				}

				echo 'added pending user '.$values['email']."\n";

			}


		}

		return true;

	}


	private function do_update_autoresponder(){

		global $wpdb;

		$timeoffset = mymail('helper')->gmt_offset(true);

		$now = time();

		$limit = ceil(25*$this->performance);

		$cron = get_option('cron', array());

		foreach($cron as $timestamp => $jobs){
			if(!is_array($jobs)) continue;
			foreach($jobs as $id => $data){
				if($id != 'mymail_autoresponder') continue;
				foreach($data as $crondata){
					$args = $crondata['args'];

					$values = array(
						'subscriber_id' => $args['args'][0],
						'campaign_id' => $args['campaign_id'],
						'added' => $now,
						'timestamp' => $timestamp,
						'sent' => 0,
						'priority' => 15,
						'count' => $args['try'],
						'ignore_status' => $args['action'] == 'mymail_subscriber_unsubscribed',
					);

					$wpdb->query("INSERT INTO {$wpdb->prefix}mymail_queue (".implode(',', array_keys($values)).") VALUES ('".implode("','", array_values($values))."')");

				}

			}
		}


		return true;

	}

	private function do_update_settings(){

		global $wpdb;

		$forms = mymail_option('forms');

		foreach($forms as $id => $form){

			//Stop if all list items are numbers (MyMail 2 already)
			if(!isset($form['lists']) || !is_array($form['lists'])) continue;
			if(count(array_filter($form['lists'], 'is_numeric')) == count($form['lists'])) continue;

			$sql = "SELECT a.ID FROM {$wpdb->prefix}mymail_lists AS a WHERE a.slug IN ('".implode("','", $form['lists'])."')";

			$lists = $wpdb->get_col($sql);

			$forms[$id]['lists'] = $lists;



			echo "updated form ".$form['name']."\n";

		}

		mymail_update_option('forms', $forms);

		$texts = mymail_option('text');

		$texts['profile_update'] = !empty($texts['profile_update']) ? $texts['profile_update'] : __('Profile Updated!', 'mymail');
		$texts['profilebutton'] = !empty($texts['profilebutton']) ? $texts['profilebutton'] : __('Update Profile', 'mymail');
		$texts['forward'] = !empty($texts['forward']) ? $texts['forward'] : __('forward to a friend', 'mymail');
		$texts['profile'] = !empty($texts['profile']) ? $texts['profile'] : __('update profile', 'mymail');

		echo "updated texts\n";

		mymail_update_option('text', $texts);

		return true;

	}

	private function do_cleanup(){

		global $wpdb;

		//remove actions where's no campaign
		if($count = $wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_actions AS a LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id WHERE p.ID IS NULL")){
			echo "removed actions where's no campaign\n";
			return false;
		}

		//remove meta where's no campaign
		if($count = $wpdb->query("DELETE a FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.post_id WHERE p.ID IS NULL AND a.meta_key LIKE '_mymail_%'")){
			echo "removed meta where's no campaign\n";
			return false;
		}

		mymail('subscribers')->wp_id();
		echo "assign WP users\n";

		//flush_rewrite_rules( true );

		delete_transient( 'mymail_cron_lock' );

		update_option('mymail_dbversion', MYMAIL_DBVERSION);
		mymail_update_option('update_required', false);

		delete_option('updatecenter_plugins');
		do_action('updatecenterplugin_check');

		return true;

	}

	private function output($content = ''){


		global $mymail_batch_update_output;

		$mymail_batch_update_output[] = $content;

	}

	private function check_db_version(){

		if(MYMAIL_DBVERSION != get_option('mymail_dbversion')){
			mymail_update_option('update_required', true);
		}else{
			mymail_update_option('update_required', false);
		}

	}


	public function unserialize($serialized_string) {

		$object = maybe_unserialize($serialized_string);
		if(empty($userdata)){
			$d = html_entity_decode($serialized_string, ENT_QUOTES, 'UTF-8');
			$d = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $d );
			$object = maybe_unserialize($d);
		}

		return $object;

	}

	/*----------------------------------------------------------------------*/
	/* Plugin Activation / Deactivation
	/*----------------------------------------------------------------------*/



	public function activate() {

		global $wpdb;

		if (is_network_admin() && is_multisite()) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		}else{

			$blogids = array(false);

		}

		foreach ($blogids as $blog_id) {

			if($blog_id) switch_to_blog( $blog_id );

			$this->check_db_version();

		}

		if($blog_id){
			switch_to_blog($old_blog);
			return;
		}

		return;

	}


	public function deactivate() {

	}


}


//backward compatibility
if(!class_exists('Envato_Plugin_Update')) {
class Envato_Plugin_Update {

	private static $option_name = 'envato_plugins';

	private static $plugins = null;
	private static $plugin_data = array();

	private static $saved = false;
	private static $_instance = null;

	public static function add($purchasecode, $args = array()){

		if (!isset(self::$_instance)){

			self::$_instance = new self();

		}

		$plugin_data = (object) wp_parse_args($args, array(
			'purchasecode' => $purchasecode,
			'remote_url' => false,
			'version' => false,
			'plugin_slug' => false
		));

		if(!isset(self::$plugin_data[$plugin_data->plugin_slug])){

			self::$plugin_data[$plugin_data->plugin_slug] = $plugin_data;

		}

		return self::$_instance;
	}

	private function __construct() {

		self::$plugins = self::get_plugin_options();

		add_action( 'admin_init', array( &$this, 'init' ), 100 );
		add_filter( 'site_transient_update_plugins', array( &$this, 'update_plugins_filter' ), 1 );

		add_action( 'wp_update_plugins', array( &$this, 'check_periodic_updates' ), 99 );
		add_action( 'envatopluginupdate_check', array( &$this, 'check_periodic_updates' ) );
		//add_action( 'shutdown', array( &$this, 'schedule' ), 100 );

		add_filter( 'http_request_args', array( &$this, 'http_request_args' ), 100, 2 );

	}

	public function init() {

		if(is_admin() && current_user_can("update_plugins")){

			global $pagenow, $wp_header_to_desc;

			$wp_header_to_desc[678] = 'No Purchasecode entered! Please provide a purchasecode!';
			$wp_header_to_desc[679] = 'Purchasecode invalid!';
			$wp_header_to_desc[680] = 'Purchasecode already in use!';

			if($pagenow == 'update-core.php'){

				//force check on the updates page
				do_action( 'envatopluginupdate_check' );

			}else if($pagenow == 'plugin-install.php'){

				if(isset($_GET['plugin']) && in_array($_GET['plugin'], array_keys(self::$plugin_data))){
					add_filter( "plugins_api",  array( &$this, 'plugins_api' ), 10, 3);
					add_filter( "plugins_api_result",  array( &$this, 'plugins_api_result' ), 10, 3);
				}
			}

		}
	}

	public function http_request_args($r, $url) {

		//don't change requests to the wordpress api
		if(false !== strpos($url, '//api.wordpress.org/')){
			return $r;
		}

		foreach(self::$plugins as $slug => $plugin){
			if($url == $plugin->package){
				$r['method'] = 'POST';
				$r['body'] = $this->header_infos($slug);
				return $r;
			}
		}
		return $r;
	}

	public function plugins_api($noidea, $action, $args) {

		global $pagenow;

		if($pagenow != 'update-core.php'){
			$slug = $args->slug;
			$plugin = self::$plugin_data[$slug];

			$version_info = $this->perform_remote_request( $slug, $plugin->remote_url );

			if(!$version_info) wp_die('There was an error while getting the information about the plugin. Please try again later');

			$res = $version_info->data;
			$res->slug = $slug;
			if(isset($res->contributors))$res->contributors = (array) $res->contributors;
			$res->sections = (array) $res->sections;

		} else {

			$res = self::$plugins[$slug];
		}

		return $res;

	}

	public function plugins_api_result($res, $action, $args) {
		if(!isset($this->plugin_slug)) return $res;

		if($args->slug == $this->plugin_slug){
			$res->external = true;
		}

		return $res;

	}

	public function check_periodic_updates( ) {

		switch(current_filter()){
			case 'envatopluginupdate_check';
				$timeout = 60;
				break;
			case 'upgrader_post_install';
				$timeout = 0;
				break;
			default:
				$timeout = 3600;
		}

		foreach(self::$plugin_data as $slug => $plugin){

			if(time()-self::$plugins[$slug]->last_update >= $timeout ){
				$this->check_for_update( $slug );
			}

		}
	}

	public function clear_option() {
		is_multisite() ? update_site_option( self::$option_name, '' ) : update_option( self::$option_name, '' );
	}

	private static function get_plugin_options() {
		//Get plugin options
		$options = is_multisite() ? get_site_option( self::$option_name ) : get_option( self::$option_name );

		if ( !$options ) $options = array();

		return $options;
	}

	public function save_plugin_options() {

		foreach(self::$plugin_data as $slug => $plugin){
			if(isset(self::$plugins[$slug]->item_data)) unset(self::$plugins[$slug]->item_data);
		}
		is_multisite() ? update_site_option( self::$option_name, self::$plugins ) : update_option( self::$option_name, self::$plugins );
	}

	public function check_for_update( $slug ) {

		if( empty(self::$plugin_data ) ) return false;

		if ( !isset( self::$plugin_data[ $slug ] ) ) return false;

		if ( !is_array( self::$plugins ) ) return false;

		$save = false;

		$plugin = self::$plugin_data[ $slug ];

		//Check to see that plugin options exist
		if ( !isset( self::$plugins[ $slug ] ) ) {

			$plugin_options = new stdClass;
			$plugin_options->slug = $slug;
			$plugin_options->purchasecode = $plugin->purchasecode;
			$plugin_options->package = '';
			$plugin_options->upgrade_notice = '';
			$plugin_options->new_version = $plugin->version;
			$plugin_options->last_update = time();

			self::$plugins[ $slug ] = $plugin_options;

		}

		$current_plugin = self::$plugins[ $slug ];
		$current_plugin->purchasecode = $plugin->purchasecode;

		//Check for updates
		unset($current_plugin->error);
		$version_info = $this->perform_remote_request( $slug, $plugin->remote_url );

		if ( is_wp_error( $version_info ) || !$version_info){
			global $notice;
			self::$plugins[ $slug ]->error = is_wp_error( $version_info ) ? $version_info->get_error_message() : $notice;
			self::$plugins[ $slug ]->last_update = time();
			self::$plugins[ $slug ]->new_version = NULL;
			$save = true;

		//$version_info should be an array with keys ['version'] and ['download_url']
		}else if ( isset( $version_info->version ) && isset( $version_info->download_url ) ) {
			$current_plugin->new_version = $version_info->version;
			$current_plugin->package = $version_info->download_url;
			$current_plugin->last_update = time();

			if( isset( $version_info->upgrade_notice ) ) $current_plugin->upgrade_notice = $version_info->upgrade_notice;
			if( isset( $version_info->data ) ) $current_plugin->item_data = $version_info->data;
			self::$plugins[ $slug ] = $current_plugin;
			$save = true;

		}

		if($save && !self::$saved){
			add_action( 'shutdown', array( &$this, 'save_plugin_options' ), 100 );
			self::$saved = true;
		}


		return self::$plugins[ $slug ];

	}

	public function perform_remote_request( $slug, $url, $body = array(), $headers = array() ) {

		if ( false === ( $result = mymail_cache_get( 'plugin_info_'.$slug ) ) ) {

			$body = wp_parse_args( $body, $this->header_infos( $slug ) ) ;

			$body = http_build_query( $body, '', '&' );

			$headers = wp_parse_args( $headers, array(
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Content-Length' => strlen( $body ),
				'X-ip' => $_SERVER['SERVER_ADDR'],
			) );


			$post = array( 'headers' => $headers, 'body' => $body );
			//Retrieve response
			$response = wp_remote_post( add_query_arg( array('envato_item_info' => '' ), esc_url( $url )), $post );
			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );

			if ( $response_code != 200 || is_wp_error( $response_body ) ) {
				return $response_body;
			}

			$result = json_decode( $response_body );

			if(!empty($result->error)){
				global $notice;
				$notice = $result->error;
				return false;
			}

			mymail_cache_set( 'plugin_info_'.$slug, $result );
		}

		return $result;

	}

	public function update_plugins_filter( $value ) {

		foreach(self::$plugin_data as $slug => $plugin){

			if( !isset( self::$plugins[ $slug ] ) ) continue;

			if( empty(self::$plugins[ $slug ]->package) ) continue;

			if( version_compare( $plugin->version, self::$plugins[ $slug ]->new_version, '>=' ) ) continue;

			$value->response[ $slug ] = self::$plugins[ $slug ];

		}
		return $value;
	}

	private function header_infos( $slug ) {

		include ABSPATH . WPINC . '/version.php';

		if(!$wp_version) global $wp_version;

		$return = array(
			'purchasecode' => self::$plugin_data[ $slug ]->purchasecode,
			'version' => self::$plugin_data[ $slug ]->version,
			'slug' => $slug,
			'wp-version' => $wp_version,
			'referer' => home_url(),
			'multisite' => is_multisite(),
		);

		return $return;
	}


}
}
?>
