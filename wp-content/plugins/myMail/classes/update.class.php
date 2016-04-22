<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_update {

	private $performance = 1;
	private $starttime;

	public function __construct() {

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
							mymail_notice('<p><strong>'.__( 'An additional update is required for MyMail!', 'mymail').'</strong></p><a class="button button-primary" href="'.$redirectto.'">'.__('progress update now', 'mymail').'</a>', 'error', true, 'update_required');
						}
					}
				}

			}else if(mymail_option('welcome')) {

				mymail_remove_notice('no_homepage');
				$this->check_db_version();

				if(!is_network_admin() &&
					(($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] = 'newsletter'))){
					mymail_update_option('welcome', false);
					wp_redirect('edit.php?post_type=newsletter&page=mymail_welcome', 302);
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
				$return['output'] .= "* OUTPUT for $id (".date('H:i:s', current_time('timestamp')).") - ".size_format(memory_get_peak_usage(true), 2)."\n";
				$return['output'] .= "======================================================\n";
				$return['output'] .= strip_tags($output)."\n";
			}else{
				// $return['output']  = "======================================================\n";
				// $return['output'] .= "* NO OUTPUT for $id (".date('Y-m-d H:i:s', current_time('timestamp')).")\n";
				// $return['output'] .= "======================================================\n";
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

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script('mymail-update-script', MYMAIL_URI . 'assets/js/update-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);

		$db_version = get_option('mymail_dbversion', 0);

		$actions = array('db_structure' => 'checking DB structure');

		if(isset($_GET['hard'])) {
			$db_version = 0;
			$actions = wp_parse_args( $actions, array('remove_db_structure' => 'removing DB structure') );
		}
		if(isset($_GET['redo'])) {
			$db_version = 0;
		}

		if ( $db_version < 20140924 || false )
			$actions = wp_parse_args(array(
				'update_lists' => 'updating Lists',
				'update_forms' => 'updating Forms',
				'update_campaign' => 'updating Campaigns',
				'update_subscriber' => 'updating Subscriber',
				'update_list_subscriber' => 'update Lists <=> Subscribers',
				'update_actions' => 'updating Actions',
				'update_pending' => 'updating Pending Subscribers',
				'update_autoresponder' => 'updating Autoresponder',
				'update_settings' => 'updating Settings',
			), $actions);

		if ( $db_version < 20150924 || false )
			$actions = wp_parse_args( array(
				'update_forms' => 'updating Forms',
				), $actions );

		if ( $db_version < 20151130 || false )
			$actions = wp_parse_args( array(
				'update_rating_init' => 'preparing Rating',
				'update_rating' => 'updating Rating',
				), $actions );

		if ( $db_version < 20151218 || false )
			$actions = wp_parse_args( array(
				'update_db_structure' => 'changes in DB structure',
				), $actions );

		if ( $db_version < 20160105 || false )
			$actions = wp_parse_args( array(
				'remove_old_data' => 'Removing MyMail 1.x data',
				), $actions );



		$actions = wp_parse_args( array(
			'db_check' => 'Database integrity',
			'cleanup' => 'cleanup',
			//'change_plugin_slug' => 'Change Plugin Slug',
		), $actions );

		wp_localize_script('mymail-update-script', 'mymail_updates', $actions);
		$performance = isset($_GET['performance']) ? max(1, intval($_GET['performance'])) : 1;
		wp_localize_script('mymail-update-script', 'mymail_updates_performance', array($performance));

		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

	}

	public function page() {

	?>
	<div class="wrap">
		<h2>MyMail Batch Update</h2>
		<?php wp_nonce_field( 'mymail_nonce', 'mymail_nonce', false );
		?>
		<p><strong>Some additional updates are required! Please keep this browser tab open until all updates are finished!</strong></p>
		<p>
			<form action="" method="get">
			<input type="hidden" name="post_type" value="newsletter">
			<input type="hidden" name="page" value="mymail_update">
				<input type="submit" class="button button-small" name="redo" value="redo update" onclick="return confirm('Do you really like to redo the update?');">
			</form>
		</p>
		<div class="alignleft" style="width:54%">
			<div id="output"></div>
			<div id="error-list"></div>
		</div>

		<div class="alignright" style="width:45%">
			<textarea id="textoutput" class="widefat" rows="30" style="width:100%;font-size:12px;font-family:monospace;background:none"></textarea>
		</div>

	</div>
	<?php
	}


	private function do_remove_db_structure(){

		global $wpdb;

		$tables = mymail()->get_tables();

		foreach($tables as $table){
			if(false !== $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_$table")){
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
		return mymail()->dbstructure(true, true, true, true);
	}

	private function do_db_check(){

		ob_start();

		mymail()->dbstructure(true, true, true, false);

		$output = ob_get_contents();

		ob_end_clean();
		if(!$output){
			echo 'No problem found'."\n";
		}

		if(function_exists('maybe_convert_table_to_utf8mb4')){
			$tables = mymail()->get_tables(true);

			foreach ($tables as $table) {

				maybe_convert_table_to_utf8mb4($table);
				# code...
			}

		}

		return true;

	}


	private function do_update_db_structure(){

		global $wpdb;

		$wpdb->query("ALTER TABLE {$wpdb->prefix}mymail_queue CHANGE subscriber_id  subscriber_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE campaign_id campaign_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE requeued requeued TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE added added INT( 11 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE timestamp timestamp INT( 11 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE sent sent INT( 11 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE priority priority TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE count count TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE error error TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE ignore_status ignore_status TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE options options VARCHAR( 191 ) NOT NULL DEFAULT ''");

		$wpdb->query("ALTER TABLE {$wpdb->prefix}mymail_actions CHANGE subscriber_id  subscriber_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE campaign_id campaign_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE timestamp timestamp INT( 11 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE count count TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE type type TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE link_id link_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0'");

		return true;
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


	private function do_update_forms(){

		global $wpdb;

		$now = time();

		$forms = mymail_option('forms');

		if(empty($forms)) return true;

		$ids = wp_list_pluck($forms, 'id' );

		$form_css = mymail_option('form_css');

		foreach ($forms as $id => $form) {

			if($wpdb->query($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mymail_forms WHERE ID = %d", $id))) continue;

			$sql = "INSERT INTO {$wpdb->prefix}mymail_forms
				(ID, name, submit, asterisk, userschoice, precheck, dropdown, prefill, inline, addlists, style, custom_style, doubleoptin, subject, headline, link, content, resend, resend_count, resend_time, template, vcard, vcard_content, confirmredirect, redirect, added, updated) VALUES
				(%d, %s, %s, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %s, %s, %s, %d, %d)

				ON DUPLICATE KEY UPDATE updated=%d";

			$sql = $wpdb->prepare($sql, $id, $form['name'], $form['submitbutton'], isset($form['asterisk']), isset($form['userschoice']), isset($form['precheck']), isset($form['dropdown']), isset($form['prefill']), isset($form['inline']), isset($form['addlists']), '', str_replace('.mymail-form ', '.mymail-form-'.$id.' ', $form_css), isset($form['double_opt_in']), $form['text']['subscription_subject'], $form['text']['subscription_headline'], $form['text']['subscription_link'], $form['text']['subscription_text'], isset($form['subscription_resend']), $form['subscription_resend_count'], $form['subscription_resend_time'], $form['template'], isset($form['vcard']), $form['vcard_content'], $form['confirmredirect'], $form['redirect'], $now, $now, $now);

			if($wpdb->query($sql)){
				if($wpdb->insert_id != $id){
					$wpdb->query($wpdb->prepare("UPDATE  {$wpdb->prefix}mymail_forms SET `ID` = %d WHERE {$wpdb->prefix}mymail_forms.ID = %d;", $id, $wpdb->insert_id));
				}


				foreach ($form['order'] as $position => $field_id) {

					$sql = "INSERT INTO {$wpdb->prefix}mymail_form_fields (form_id, field_id, name, required, position) VALUES (%d, %s, %s, %d, %d)";
					$sql = $wpdb->prepare($sql, $id, $field_id, $form['labels'][$field_id], in_array($field_id, $form['required']), $position);
					$wpdb->query($sql);
				}


				echo 'updated form '.$form['name']." \n";
				if(mymail('forms')->assign_lists($id, $form['lists'], false)){
					echo 'assigned lists to form '.$form['name']." \n";
				}

			}


		}

		$wpdb->query($wpdb->prepare("ALTER TABLE {$wpdb->prefix}mymail_forms AUTO_INCREMENT = %d", count($forms)));

		$wpdb->query("UPDATE {$wpdb->posts} SET `post_content` = replace(post_content, '[newsletter_signup_form]', '[newsletter_signup_form id=0]')");

		return true;

	}

	private function do_update_rating_init(){
		global $wpdb;

		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}mymail_subscribers SET rating = %d WHERE rating = %f", 2, 0.25));

		return true;

	}

	private function do_update_rating(){

		global $wpdb;

		$all = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mymail_subscribers WHERE rating = 2");

		$subscribers = $wpdb->get_col("SELECT ID FROM {$wpdb->prefix}mymail_subscribers WHERE rating = 2 LIMIT 1000");

		if(empty($all)){
			echo '0 subscriber ratings left to update'."\n";
			return true;

		}

		mymail('subscribers')->update_rate($subscribers);

		echo $all.' subscriber ratings left to update'."\n";

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

		if(empty($forms)) return true;

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

		delete_transient( 'mymail_cron_lock' );

		update_option('mymail_dbversion', MYMAIL_DBVERSION);
		mymail_update_option('update_required', false);

		if($count = $wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_actions AS a JOIN (SELECT b.campaign_id, b.subscriber_id FROM {$wpdb->prefix}mymail_actions AS b LEFT JOIN {$wpdb->posts} AS p ON p.ID = b.campaign_id WHERE p.ID IS NULL ORDER BY b.campaign_id LIMIT 1000) AS ab ON (a.campaign_id = ab.campaign_id AND a.subscriber_id = ab.subscriber_id)")){
			echo "removed actions where's no campaign\n";
			return false;
		}

		if($count = $wpdb->query("DELETE a FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.post_id WHERE p.ID IS NULL AND a.meta_key LIKE '_mymail_%'")){
			echo "removed meta where's no campaign\n";
			return false;
		}

		if($count = $wpdb->query("DELETE FROM {$wpdb->prefix}mymail_subscriber_meta AS a WHERE (meta_value = '' OR subscriber_id = 0) LIMIT 1")){
			echo "removed unassigned subscriber meta\n";
			return false;
		}

		if($count = mymail('subscribers')->wp_id()){
			echo "assign $count WP users\n";
			return false;
		}

		$wpdb->query("UPDATE {$wpdb->prefix}mymail_subscribers SET ip_signup = '' WHERE ip_signup = 0");
		$wpdb->query("UPDATE {$wpdb->prefix}mymail_subscribers SET ip_confirm = '' WHERE ip_confirm = 0");

		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}mymail_subscribers SET rating = %f WHERE rating = %d", 0.25, 2));

		delete_option('updatecenter_plugins');
		do_action('updatecenterplugin_check');

		return true;

	}

	private function do_change_plugin_slug(){

		//NOPE
		return true;

		global $wpdb;

		$from = 'myMail/myMail.php';
		$to = 'mymail/mymail.php';

		$old_destination = WP_PLUGIN_DIR.'/'.$from;
		$new_destination = WP_PLUGIN_DIR.'/'.$to;

		//old location doesn't exist (anymore)
		if(!file_exists($old_destination)) return true;
		//new location is the same as the old one
		if($old_destination == $new_destination) return true;

		//do the magic

		//delete folder
		echo 'Removing the old file of the plugin… ';
		if(rename( $old_destination , dirname($old_destination).'/'.basename($new_destination) )){
			echo 'done';
		}else{
			echo 'failed';
		}
		echo "\n";

		echo 'Removing the old directory of the plugin… ';
		if(rename( dirname($old_destination) , dirname($new_destination) )){
			echo 'done';
		}else{
			echo 'failed';
		}
		echo "\n";

		//rewrite location in the database
		echo 'Moving the plugin to new location… ';
		if(false !== $wpdb->query($wpdb->prepare("UPDATE {$wpdb->options} SET `option_value` = replace(option_value, %s, %s)", $from, $to))){
			echo 'done';
		}else{
			echo 'failed';
		}
		echo "\n";

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
		if(empty($object)){
			$d = html_entity_decode($serialized_string, ENT_QUOTES, 'UTF-8');
			$d = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $d );
			$object = maybe_unserialize($d);
		}

		return $object;

	}


}
