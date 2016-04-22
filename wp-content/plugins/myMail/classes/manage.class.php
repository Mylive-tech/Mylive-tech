<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_manage {


	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init' ) );

	}

	public function init() {

		if(is_admin()){
			add_action('admin_menu', array( &$this, 'add_menu'), 40);
			add_action('wp_ajax_mymail_import_subscribers_upload_handler', array( &$this, 'ajax_import_subscribers_upload_handler'));
			add_action('wp_ajax_mymail_get_import_data', array( &$this, 'ajax_get_import_data'));
			add_action('wp_ajax_mymail_do_import', array( &$this, 'ajax_do_import'));
			add_action('wp_ajax_mymail_export_contacts', array( &$this, 'ajax_export_contacts' ) );
			add_action('wp_ajax_mymail_do_export', array( &$this, 'ajax_do_export' ) );
			add_action('wp_ajax_mymail_download_export_file', array( &$this, 'ajax_download_export_file' ) );
			add_action('wp_ajax_mymail_delete_contacts', array( &$this, 'ajax_delete_contacts' ) );
			add_action('wp_ajax_mymail_delete_old_bulk_jobs', array( &$this, 'ajax_delete_old_bulk_jobs' ) );
		}

	}


	/*----------------------------------------------------------------------*/
	/* Settings
	/*----------------------------------------------------------------------*/


	public function add_menu() {

		$page = add_submenu_page('edit.php?post_type=newsletter', __('Manage Subscribers', 'mymail'), __('Manage Subscribers', 'mymail'), 'mymail_manage_subscribers', 'mymail_subscriber-manage', array( &$this, 'subscriber_manage' ));
		add_action( 'load-'.$page, array( &$this, 'scripts_styles' ) );

	}

	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script('mymail-manage-script', MYMAIL_URI . 'assets/js/manage-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);
		wp_localize_script('mymail-manage-script', 'mymailL10n', array(
				'select_status' => __( 'Please select the status for the importing contacts!', 'mymail' ),
				'select_emailcolumn' => __( 'Please select at least the column with the email addresses!', 'mymail' ),
				'prepare_data' => __( 'preparing data', 'mymail' ),
				'uploading' => __( 'uploading...%s', 'mymail' ),
				'import_contacts' => __( 'Importing Contacts...%s', 'mymail' ),
				'current_stats' => __( 'Currently %s of %s imported with %s errors. %s memory usage', 'mymail' ),
				'estimate_time' => __( 'Estimate time left: %s minutes', 'mymail' ),
				'continues_in' => __( 'continues in %s seconds', 'mymail' ),
				'error_importing' => __( 'There was a problem during importing contacts. Please check the error logs for more information!', 'mymail' ),
				'prepare_download' => __( 'Preparing Download...%s', 'mymail' ),
				'write_file' => __( 'writing file: %s', 'mymail' ),
				'download_finished' => __( 'Download finished', 'mymail' ),
				'downloading' => __( 'Downloading...', 'mymail' ),
				'no_lists' => __( 'Please select at least one list!', 'mymail' ),
				'confirm_import' => __( 'Do you really like to import these contacts?', 'mymail' ),
				'import_complete' => __( 'Import complete!', 'mymail' ),
				'confirm_delete' => __( 'You are about to delete these subscribers permanently. This step is irreversible!', 'mymail')."\n".sprintf(__('Type "%s" to confirm deletion', 'mymail'), 'DELETE'),
				'onbeforeunloadimport' => __('You are currently importing subscribers! If you leave the page all pending subscribers don\'t get imported!', 'mymail'),
				'onbeforeunloadexport' => __('Your download is preparing! If you leave this page the progress will abort!', 'mymail'),
			) );
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-touch-punch');
		wp_enqueue_style('mymail-manage-style', MYMAIL_URI . 'assets/css/manage-style'.$suffix.'.css', array(), MYMAIL_VERSION);

		wp_enqueue_style('jquery-style', MYMAIL_URI . 'assets/css/libs/jquery-ui'.$suffix.'.css');
		wp_enqueue_style('jquery-datepicker', MYMAIL_URI . 'assets/css/datepicker'.$suffix.'.css');

		wp_enqueue_script('jquery-ui-datepicker');

	}

	public function subscriber_manage() {

		remove_action('post-plupload-upload-ui', 'media_upload_flash_bypass');
		wp_enqueue_script('plupload-all');

		include MYMAIL_DIR . 'views/manage.php';
	}



	public function ajax_import_subscribers_upload_handler() {

		global $wpdb;

		$safe_mode = @ini_get('safe_mode');
		$memory_limit = @ini_get('memory_limit');
		$max_execution_time = @ini_get('max_execution_time');

		if(!$safe_mode){
			@set_time_limit(0);

			if(intval($max_execution_time) < 300){
				@ini_set( 'max_execution_time', 300 );
			}
			if(intval($memory_limit) < 256){
				@ini_set( 'memory_limit', '256M' );
			}
		}

		if(isset($_FILES['async-upload'])){

			if(!current_user_can('mymail_import_subscribers')){
				die('not allowed');
			}

			$file = $_FILES['async-upload'];
			$raw_data = (file_get_contents($file['tmp_name']));


		}else if(isset($_POST['data'])){

			$return['success'] = false;

			$this->ajax_nonce(json_encode($return));

			if(!current_user_can('mymail_import_subscribers')){

				@header( 'Content-type: application/json' );
				echo json_encode($return);
				exit;
			}

			$raw_data = esc_attr(stripslashes($_POST['data']));
			$return['success'] = true;

		}else if(isset($_POST['wordpressusers'])){

			if(!current_user_can('mymail_import_wordpress_users')){

				@header( 'Content-type: application/json' );
				echo json_encode($return);
				exit;
			}

			parse_str($_POST['wordpressusers'], $data);

			$roles = isset($data['roles']) ? (array) $data['roles'] : array();
			$no_role = isset($data['no_role']);
			$meta_values = isset($data['meta_values']) ? (array) $data['meta_values'] : array();

			$sql = "SELECT u.user_email, IF(meta_role.meta_value = 'a:0:{}',NULL,meta_role.meta_value) AS '_role', meta_firstname.meta_value AS 'firstname', meta_lastname.meta_value AS 'lastname', u.display_name, u.user_nicename, u.user_registered";

			foreach($meta_values as $i => $meta_value){
				$sql .= ", meta_$i.meta_value AS '$meta_value'";
			}

			$sql .= " FROM {$wpdb->users} AS u";
			$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_role ON meta_role.user_id = u.id AND meta_role.meta_key = '{$wpdb->prefix}capabilities'";
			$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_firstname ON meta_firstname.user_id = u.id AND meta_firstname.meta_key = 'first_name'";
			$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_lastname ON meta_lastname.user_id = u.id AND meta_lastname.meta_key = 'last_name'";
			//$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_nickname ON meta_nickname.user_id = u.id AND meta_nickname.meta_key = 'nickname'";

			foreach($meta_values as $i => $meta_value){
				$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_$i ON meta_$i.user_id = u.id AND meta_$i.meta_key = '$meta_value'";
			}

			$sql .= "";

			$users = $wpdb->get_results($sql);

			$raw_data = '<b>'.mymail_text('email').'</b>;<b>'.mymail_text('firstname').'</b>;<b>'.mymail_text('lastname').'</b>;<b>'.__('nickname', 'mymail').'</b>;<b>'.__('display name', 'mymail').'</b>;<b>'.__('registered', 'mymail').'</b>;<b>'.implode('</b>;<b>', $meta_values)."</b>;\n";

			foreach($users as $user){

				//no role set and roles is a must
				if(!$user->_role && !$no_role) continue;

				//role is set but not in the list
				if($user->_role && !array_intersect(array_keys(unserialize($user->_role)), $roles)) continue;

				foreach($user as $key => $data){
					if($key == '_role') continue;
					if($key == 'firstname' && !$data) $data = $user->display_name;
					$raw_data .= $data.';';
				}

				$raw_data .= "\n";

			}

			$return['success'] = true;

		}else{

			die('not allowed');

		}

		$raw_data = (trim(str_replace(array("\r", "\r\n", "\n\n"), "\n", $raw_data)));

		if(function_exists('mb_convert_encoding')){
			$encoding = mb_detect_encoding($raw_data, 'auto');
		}else{
			$encoding = 'UTF-8';
		}
		if($encoding != 'UTF-8'){
			$raw_data = utf8_encode($raw_data);
			$encoding = mb_detect_encoding($raw_data, 'auto');
		}
		$lines = explode("\n", $raw_data);
		$parts = array_chunk($lines, max(50, round(count($lines)/200)));
		$partcount = count($parts);

		$bulkimport = array(
			'ids' => array(),
			'imported' => 0,
			'errors' => 0,
			'encoding' => $encoding,
			'parts' => $partcount,
			'lines' => count($lines),
			'separator' => $this->get_separator( implode($parts[0]) ),
		);

		$charset_collate = '';

		if ( ! empty($wpdb->charset) )
			$charset_collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mymail_temp_import (ID bigint(20) NOT NULL AUTO_INCREMENT, data longtext NOT NULL, identifier char(13) NOT NULL, PRIMARY KEY (ID) ) $charset_collate");

		$return['identifier'] = $identifier = uniqid();

		for($i = 0; $i < $partcount; $i++){

			$part = $parts[$i];

			//remove quotes;
			$part = str_replace(array("'".$bulkimport['separator']."'", '"'.$bulkimport['separator'].'"'), $bulkimport['separator'], $part);
			$part = preg_replace('#^("|\')#', '', $part);
			$part = preg_replace('#("|\')$#', '', $part);

			$new_value = base64_encode(serialize($part));

			$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}mymail_temp_import (data, identifier) VALUES (%s, %s)", $new_value, $identifier));

			$bulkimport['ids'][] = $i;
		}

		$return['memoryusage'] = size_format(memory_get_peak_usage(true),2);
		update_option('mymail_bulk_import', $bulkimport);

		if(isset($return)){

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;
		}

	}


	public function ajax_get_import_data() {

		global $wpdb;

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		if(!current_user_can('mymail_import_subscribers')){

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;
		}

		$return['identifier'] = $identifier = $_POST['identifier'];

		$return['data'] = get_option( 'mymail_bulk_import' );

		//get first and last entry
		$entries = $wpdb->get_row($wpdb->prepare("SELECT
(SELECT data FROM {$wpdb->prefix}mymail_temp_import WHERE identifier = %s ORDER BY ID ASC LIMIT 1) AS first, (SELECT data FROM {$wpdb->prefix}mymail_temp_import WHERE identifier = %s ORDER BY ID DESC LIMIT 1) AS last", $identifier, $identifier));

		$first = unserialize(base64_decode($entries->first));
		$last = unserialize(base64_decode($entries->last));

		$firstline = explode($return['data']['separator'], $first[0]);
		$data = explode($return['data']['separator'], $first[count($first)-1]);
		$cols = count($data);
		$contactcount = $return['data']['lines'];

		$custom_fields = mymail()->get_custom_fields();

		$fields = array(
			'email' => mymail_text('email'),
			'firstname' => mymail_text('firstname'),
			'lastname' => mymail_text('lastname'),
			'first_last' => mymail_text('firstname').'⎵'.mymail_text('lastname'),
			'last_first' => mymail_text('lastname').'⎵'.mymail_text('firstname'),
		);
		$meta_dates = array(
			'_signup' => __('Signup Date', 'mymail'),
			'_confirm' => __('Confirm Date', 'mymail'),
			'_confirm_signup' => __('Signup + Confirm Date', 'mymail'),
		);
		$meta_ips = array(
			'_ip' => __('IP Address', 'mymail'),
			'_ip_signup' => __('Signup IP Address', 'mymail'),
			'_ip_confirm' => __('Confirm IP Address', 'mymail'),
			'_ip_confirm_signup' => __('Confirm + Signup IP Address', 'mymail'),
			'_ip_all' => __('all IP Addresses', 'mymail'),
		);
		$meta_other = array(
			'_status' => __('Status', 'mymail').' [0...6]',
			'_lang' => __('Language', 'mymail'),
		);

		$html = '<h2>'.__('Select columns', 'mymail').'</h2>';
		$html .= '<p class="description">'.__('Define which column represents which field', 'mymail').'</p>';
		$html .= '<form id="subscriber-table" class="stuffbox"><table class="wp-list-table widefat">';
		$html .= '<thead><tr><td style="width:20px;">#</td>';
		$emailfield = false;
		for($i = 0; $i < $cols; $i++){
			$ismail = mymail_is_email(trim($data[$i]));
			$select = '<select name="order[]">';
			$select .= '<option value="-1">'.__('Ignore column', 'mymail').'</option>';
			$select .= '<option value="-1">----------</option>';
			$select .= '<optgroup label="'.__('Basic', 'mymail').'">';
			foreach($fields as $key => $value){
				$is_selected = (($ismail && $key == 'email' && !$emailfield && $emailfield = true) ||
				(strip_tags($firstline[$i]) == mymail_text('firstname') && $key == 'firstname') ||
				(strip_tags($firstline[$i]) == mymail_text('lastname') && $key == 'lastname'));
				$select .= '<option value="'.$key.'" '.($is_selected ? 'selected' : '').'>'.$value.'</option>';
			}
			$select .= '</optgroup>';
			if(!empty($custom_fields)){
				$select .= '<optgroup label="'.__('Custom Fields', 'mymail').'">';
				foreach($custom_fields as $key => $d){
					$select .= '<option value="'.$key.'">'.$d['name'].'</option>';
				}
				$select .= '</optgroup>';
			}else{
				$select .= '<optgroup label="'.__('no Custom Fields defined!', 'mymail').'">';
				$select .= '</optgroup>';
			}
			$select .= '<optgroup label="'.__('Time Options', 'mymail').'">';
			foreach($meta_dates as $key => $value){
				$is_selected = ((strip_tags($firstline[$i]) == __('registered', 'mymail') && $key == '_signup'));
				$select .= '<option value="'.$key.'" '.($is_selected ? 'selected' : '').'>'.$value.'</option>';
			}
			$select .= '</optgroup>';
			$select .= '<optgroup label="'.__('IP Options', 'mymail').'">';
			foreach($meta_ips as $key => $value){
				$is_selected = ((strip_tags($firstline[$i]) == __('registered', 'mymail') && $key == '_signup'));
				$select .= '<option value="'.$key.'" '.($is_selected ? 'selected' : '').'>'.$value.'</option>';
			}
			$select .= '</optgroup>';
			$select .= '<optgroup label="'.__('Other Meta', 'mymail').'">';
			foreach($meta_other as $key => $value){
				$is_selected = ((strip_tags($firstline[$i]) == __('registered', 'mymail') && $key == '_signup'));
				$select .= '<option value="'.$key.'" '.($is_selected ? 'selected' : '').'>'.$value.'</option>';
			}
			$select .= '</optgroup>';
			$select .= '</select>';
			$html .= '<td>'.$select.'</td>';
		}
		$html .= '</tr></thead>';

		$html .= '<tbody>';
		for($i = 0; $i < min(10, $contactcount); $i++){
			$data = explode($return['data']['separator'], ($first[$i]));
			$html .= '<tr class="'.($i%2 ? '' : 'alternate').'"><td>'.number_format_i18n($i+1).'</td>';
			foreach($data as $cell){
				$html .= '<td title="'.strip_tags($cell).'">'.($cell).'</td>';
			}
			$html .= '<tr>';
		}
		if($contactcount > 10){
			$html .= '<tr class="alternate"><td>&nbsp;</td><td colspan="'.($cols).'"><span class="description">&hellip;'.sprintf(__('%s contacts are hidden', 'mymail'), number_format_i18n($contactcount-11) ).'&hellip;</span></td>';

			$data = explode($return['data']['separator'], array_pop($last));
			$html .= '<tr'.($i%2 ? '' : ' class="alternate"').'><td>'.number_format_i18n($contactcount).'</td>';
			foreach($data as $cell){
				$html .= '<td title="'.strip_tags($cell).'">'.($cell).'</td>';
			}
			$html .= '<tr>';
		}
		$html .= '</tbody>';

		$html .= '</table></form>';
		$html .= '<div class="stuffbox import-options">';
		$html .= '<div class="submit-button-box alignright"><button class="do-import button button-hero button-primary">'.(sprintf(__('Import %s contacts', 'mymail'), '<strong>'.number_format_i18n($contactcount).'</strong>')).'*</button><p>* '.__('Please make sure you have the permission to import these contacts!', 'mymail').'</p></div>';
		$html .= '<h3>'.__('add contacts to following lists', 'mymail').'</h3>';
		$html .= '<form id="lists"><ul>';
		$lists = mymail('lists')->get(NULL, NULL, true);
		if($lists && !is_wp_error($lists)){
			foreach($lists as $list){
				$html .= '<li><label><input name="lists[]" value="'.$list->name.'" type="checkbox"> '.$list->name.' <span class="count">('.number_format_i18n($list->subscribers).')</span></label></li>';
			}
		}
		$html .= '</ul></form>';
		$html .= '<p><label for="new_list_name">'.__('add new list', 'mymail').': </label><input type="text" id="new_list_name" value=""> <button class="button" id="addlist">'.__('add', 'mymail').'</button></p>
';
		$html .= '<h3>'.__('Import as', 'mymail').':</h3><p>';
		$statuses = mymail('subscribers')->get_status(NULL, true);
		$html .= '<label><input type="radio" name="status" value="-1"> '.__('ignore or as defined above', 'mymail').' </label> (<abbr title="'.__('Use following numbers for different statuses', 'mymail').': '."\n".substr(print_r($statuses, true), 10, -3).'">?</abbr>)</p><p>';
		foreach($statuses as $i => $name ){
			if(in_array($i, array(4,5,6))) continue;
			$html .= '<label><input type="radio" name="status" value="'.$i.'" '.checked( 1, $i, false ).'> '.$name.' </label>';
		}
		$html .= '</p><p class="pending-info description">'.__('choosing "pending" as status will force a confirmation message to the subscriber', 'mymail').'</p>';

		$html .= '<h3>'.__('Existing subscribers', 'mymail').':</h3><p><label> <input type="radio" name="existing" value="skip" checked> '.__('skip', 'mymail').' </label> <label><input type="radio" name="existing" value="overwrite"> '.__('overwrite', 'mymail').' </label><input type="radio" name="existing" value="merge"> '.__('merge', 'mymail').' </label></p>';
		$html .= '<h3>'.__('Other', 'mymail').':</h3><p><label>';
		$html .= '<p><label><input type="checkbox" id="signup" name="signup">'.__('Use a signup date if not defined', 'mymail').': <input type="text" value="'.date('Y-m-d').'" class="datepicker" id="signupdate" name="signupdate" disabled></label>';
		$html .= '<br><span class="description">'.__('Some Auto responder require a signup date. Define it here if it is not set or missing', 'mymail').'</span></p>';
		$html .= '<p><label><input type="checkbox" id="performance" name="performance"> '.__('low memory usage (slower)', 'mymail').'</label></p>';
		$html .= '<input type="hidden" id="identifier" value="'.$identifier.'">';
		$html .= '</div>';

		$return['html'] = $html;

		@header( 'Content-type: application/json' );
		echo json_encode($return);
		exit;

	}


	public function ajax_do_import() {

		global $wpdb;

		define('MYMAIL_DO_BULKIMPORT', true);

		$safe_mode = @ini_get('safe_mode');
		$memory_limit = @ini_get('memory_limit');
		$max_execution_time = @ini_get('max_execution_time');

		@ini_set('display_errors',0);

		if(!$safe_mode){
			@set_time_limit(0);

			if(intval($max_execution_time) < 300){
				@ini_set( 'max_execution_time', 300 );
			}
			if(intval($memory_limit) < 256){
				@ini_set( 'memory_limit', '256M' );
			}
		}

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		if(!current_user_can('mymail_import_subscribers')){
			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;
		}

		$timeoffset = mymail('helper')->gmt_offset(true);

		$bulkdata = wp_parse_args($_POST['options'], get_option( 'mymail_bulk_import' ));
		$erroremails =  get_option( 'mymail_bulk_import_errors', array() );

		$bulkdata['existing'] = esc_attr($bulkdata['existing']);
		$bulkdata['keepstatus'] = !!($bulkdata['keepstatus'] === 'true');
		$bulkdata['performance'] = !!($bulkdata['performance'] === 'true');
		$bulkdata['signupdate'] = $bulkdata['signupdate'];

		parse_str($bulkdata['order']);
		parse_str($bulkdata['lists']);

		$list_ids = array();

		foreach ( (array) $lists as $list) {

			$list_id = mymail('lists')->get_by_name($list, 'ID');

			if(!$list_id){
				$list_id = mymail('lists')->add($list);
				if ( is_wp_error($list_id ) )
					continue;
			}

			$list_ids[] = $list_id;
		}

		$parts_at_once = $bulkdata['performance'] ? 2 : 8;

		$bulkdata['current'] = intval($_POST['id']);

		$sql = "SELECT data FROM {$wpdb->prefix}mymail_temp_import WHERE identifier = %s ORDER BY ID ASC LIMIT %d, $parts_at_once";

		$lists = $wpdb->get_col($wpdb->prepare($sql, $bulkdata['identifier'], $bulkdata['current']*$parts_at_once));

		$return['sql'] = $wpdb->prepare($sql, $bulkdata['identifier'], $bulkdata['current']*$parts_at_once);

		if($lists){

			foreach($lists as $list){

				$list = unserialize(base64_decode($list));

				foreach($list as $line){
					if(!trim($line)){
						$bulkdata['lines']--;
						continue;
					}

					@set_time_limit(10);

					$data = explode($bulkdata['separator'], $line);

					$userlists = $lists;

					$insert = array(
						'signup' => 0,
						'confirm' => 0,
						'ip' => '',
						'ip_signup' => '',
						'ip_confirm' => '',
						'lang' => '',
					);

					$insert = array();

					for($i = 0; $i < count($data); $i++){

						$d = trim($data[$i]);
						switch ($order[$i]) {

							case 'email':
								$insert[$order[$i]] = strtolower($d);
								break;
							case '_signup':
							case '_confirm':
								if(!is_numeric($d) && !empty($d)) $d = strtotime($d);
							case '_ip':
							case '_ip_signup':
							case '_ip_confirm':
							case '_lang':
							case '_status':
								$insert[substr($order[$i], 1)] = $d;
								break;
							case '_ip_all':
								$insert['ip'] = $d;
							case '_ip_confirm_signup':
								$insert['ip_signup'] = $d;
								$insert['ip_confirm'] = $d;
								break;
							case '_confirm_signup':
								if(!is_numeric($d) && !empty($d)) $d = strtotime($d);
								$insert['signup'] = $d;
								$insert['confirm'] = $d;
								break;

							case 'first_last':
								$split = explode(' ', $d);
								$insert['firstname'] = $split[0];
								$insert['lastname'] = $split[1];
								break;
							case 'last_first':
								$split = explode(' ', $d);
								$insert['firstname'] = $split[1];
								$insert['lastname'] = $split[0];
								break;
							case '-1':
								break;
							default:
								$insert[$order[$i]] = $d;
						}
					}

					//apply global status
					if($bulkdata['status'] == -1 && is_numeric($insert['status'])){
						$insert['status'] = intval($insert['status']);
					}else{
						$insert['status'] = $bulkdata['status'];
					}

					if(!isset($insert['signup']) || empty($insert['signup']))
						$insert['signup'] = $bulkdata['signupdate'] ? strtotime($bulkdata['signupdate'])-$timeoffset : 0;

					if(empty($insert['signup']) && $bulkdata['existing'] == 'merge'){
						unset($insert['signup']);
					}

					if(!isset($insert['confirm'])) $insert['confirm'] = 0;

					if(!mymail_is_email( $insert['email'] )){
						$erroremails[$insert['email']] = __('invalid email address', 'mymail');
						$bulkdata['errors']++;
						continue;
					}

					$insert['referer'] = 'import';

					switch($bulkdata['existing']){
						case 'merge':
							$subscriber_id = mymail('subscribers')->merge($insert);
							break;
						case 'overwrite':
							$subscriber_id = mymail('subscribers')->add($insert, true);
							break;
						case 'skip':
							$subscriber_id = mymail('subscribers')->add($insert, false);
							break;
					}

					if(is_wp_error( $subscriber_id )){
						$erroremails[$insert['email']] = $subscriber_id->get_error_message();
						$bulkdata['errors']++;
					}else{
						mymail('subscribers')->assign_lists($subscriber_id, $list_ids);
						$bulkdata['imported']++;
					}

				}
			}
		}

		$return['memoryusage'] = size_format(memory_get_peak_usage(true),2);
		$return['errors'] = ($bulkdata['errors']);
		$return['imported'] = ($bulkdata['imported']);
		$return['total'] = ($bulkdata['lines']);
		$return['f_errors'] = number_format_i18n($bulkdata['errors']);
		$return['f_imported'] = number_format_i18n($bulkdata['imported']);
		$return['f_total'] = number_format_i18n($bulkdata['lines']);

		$return['html'] = '';

		if($bulkdata['imported'] + $bulkdata['errors'] >= $bulkdata['lines']){
			$return['html'] .= '<p>'.sprintf(__('%1$s of %2$s contacts imported', 'mymail'), '<strong>'.number_format_i18n($bulkdata['imported']).'</strong>', '<strong>'.number_format_i18n($bulkdata['lines']).'</strong>') .'<p>';
			if($bulkdata['errors']){
				$i = 0;
				$table = '<p>'.__('The following addresses were not imported', 'mymail').':</p>';
				$table .= '<table class="wp-list-table widefat fixed">';
				$table .= '<thead><tr><td width="5%">#</td><td>'.mymail_text('email').'</td><td>'.__('Reason', 'mymail').'</td></tr></thead><tbody>';
				foreach($erroremails as $email => $e){
					$table .= '<tr'.($i%2 ? '' : ' class="alternate"').'><td>'.(++$i).'</td><td>'.$email.'</td><td>'.$e.'</td></tr></thead>';
				}
				$table .= '</tbody></table>';
				$return['html'] .= $table;
			}

			delete_option( 'mymail_bulk_import' );
			delete_option( 'mymail_bulk_import_errors' );
			$wpdb->query("DROP TABLE {$wpdb->prefix}mymail_temp_import");

			$return['wpusers'] = mymail('subscribers')->wp_id();

			mymail()->optimize_tables();

		}else{

			update_option('mymail_bulk_import', $bulkdata);
			update_option('mymail_bulk_import_errors', $erroremails);

		}
		$return['success'] = true;

		@header( 'Content-type: application/json' );
		echo json_encode($return);
		exit;
	}


	public function ajax_export_contacts() {


		global $wpdb, $wp_filesystem;
		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if(!current_user_can('mymail_export_subscribers')){
			$return['msg'] = 'no allowed';

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;
		}

		parse_str($_POST['data'], $d);

		$listids = isset($d['lists']) ? array_filter($d['lists'], 'is_numeric') : array();
		$statuses = isset($d['status']) ? array_filter($d['status'], 'is_numeric') : array();

		$count = 0;

		if(isset($d['nolists']))
			$count += mymail('lists')->count(false, $statuses);

		if(!empty($listids))
			$count += mymail('lists')->count($listids, $statuses);

		$return['count'] = $count;

		if($count){

			if(!is_dir(MYMAIL_UPLOAD_DIR)) wp_mkdir_p(MYMAIL_UPLOAD_DIR);

			$filename = MYMAIL_UPLOAD_DIR.'/~mymail_export_'.date('Y-m-d-H-i-s').'.tmp';

			set_transient( 'mymail_export_filename', $filename );

			try {

				add_filter( 'filesystem_method', create_function( '$a', 'return "direct";' ) );
				mymail_require_filesystem();


				if(!($return['success'] = $wp_filesystem->put_contents( $filename, '', FS_CHMOD_FILE))){
					$return['msg'] = sprintf(__('Not able to create file in %s. Please make sure WordPress can write files to your filesystem!', 'mymail'), MYMAIL_UPLOAD_DIR);
				}

			} catch ( Exception $e){


				$return['success'] = false;
				$return['msg'] = $e->getMessage();
			}


		}else{

			$return['msg'] = __('no subscribers found', 'mymail');
		}

		@header( 'Content-type: application/json' );
		echo json_encode($return);
		exit;

	}


	public function ajax_do_export() {

		global $wpdb;

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if(!current_user_can('mymail_export_subscribers')){
			$return['msg'] = 'no allowed';

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;
		}

		$filename = get_transient( 'mymail_export_filename' );

		if(!file_exists($filename) || !is_writeable($filename)){
			$return['msg'] = 'error';

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;
		}

		parse_str($_POST['data'], $d);

		$offset = intval($_POST['offset']);
		$limit = intval($_POST['limit']);
		$raw_data = array();

		$listids = isset($d['lists']) ? array_filter($d['lists'], 'is_numeric') : array();
		$statuses = isset($d['status']) ? array_filter($d['status'], 'is_numeric') : array();

		$encoding = $d['encoding'];
		$outputformat = $d['outputformat'];
		$dateformat = $d['dateformat'];

		$useheader = $offset === 0 && isset($d['header']);

		$custom_fields = mymail()->get_custom_fields();
		$custom_date_fields = mymail()->get_custom_date_fields();
		$custom_field_names = array_merge(array('firstname', 'lastname'), array_keys($custom_fields));
		$custom_field_names = array_keys(array_intersect_key(array_flip($custom_field_names), array_flip( $d['column'] )));

		if($useheader){

				$row = array();

				foreach($d['column'] as $col){
					switch ($col) {
						case '_number':
							$val = '#';
							break;
						case 'email':
						case 'firstname':
						case 'lastname':
							$val = mymail_text($col, $col);
							break;
						case '_listnames':
							$val = __('Lists', 'mymail');
							break;
						case 'hash':
							$val = __('Hash', 'mymail');
							break;
						case 'status':
							$val = __('Status', 'mymail');
							break;
						case 'statuscode':
							$val = __('Statuscode', 'mymail');
							break;
						case 'ip':
							$val = __('IP Address', 'mymail');
							break;
						case 'signup':
							$val = __('Signup Date', 'mymail');
							break;
						case 'ip_signup':
							$val = __('Signup IP', 'mymail');
							break;
						case 'confirm':
							$val = __('Confirm Date', 'mymail');
							break;
						case 'ip_confirm':
							$val = __('Confirm IP', 'mymail');
							break;
						case 'added':
							$val = __('Added', 'mymail');
							break;
						case 'updated':
							$val = __('Updated', 'mymail');
							break;
						default:
							$val = (isset($custom_fields[$col])) ? $custom_fields[$col]['name'] : '';
					}
					if(function_exists('mb_convert_encoding'))
						$val = mb_convert_encoding($val, $encoding, 'UTF-8');

					$row[] = str_replace(';', ',', $val);
				}

				$raw_data[] = $row;

		}

		$offset = $offset*$limit;

		$field_names = array('hash', 'email', 'status', 'added', 'signup', 'confirm', 'updated', 'ip_signup', 'ip_confirm', 'lang');
		$fields = array_keys(array_intersect_key(array_flip($field_names), array_flip( $d['column'] )));

		if(isset($d['nolists'])){


			$sql = "SELECT a.ID, a.".implode(', a.', $fields).", ab.list_id";

			foreach($custom_field_names as $i => $name){
				$sql .= ", meta_$i.meta_value AS '$name'";
			}

			$sql .= " FROM {$wpdb->prefix}mymail_subscribers as a LEFT JOIN ({$wpdb->prefix}mymail_lists AS b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.list_id) ON a.ID = ab.subscriber_id";

			foreach($custom_field_names as $i => $name){
				$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS meta_$i ON meta_$i.subscriber_id = a.ID AND meta_$i.meta_key = '$name'";
			}

			$sql .= " WHERE 1=1 AND a.status IN (".implode(',', $statuses).") AND b.ID IS NULL GROUP BY a.ID LIMIT $offset, $limit";

			$data = $wpdb->get_results($sql);

		}

		if(!empty($listids)){

			$sql = "SELECT a.ID, a.".implode(', a.', $fields).", ab.list_id";

			foreach($custom_field_names as $i => $name){
				$sql .= ", meta_$i.meta_value AS '$name'";
			}

			$sql .= " FROM {$wpdb->prefix}mymail_subscribers as a LEFT JOIN ({$wpdb->prefix}mymail_lists AS b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.list_id) ON a.ID = ab.subscriber_id";

			foreach($custom_field_names as $i => $name){
				$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS meta_$i ON meta_$i.subscriber_id = a.ID AND meta_$i.meta_key = '$name'";
			}

			$sql .= " WHERE 1=1 AND a.status IN (".implode(',', $statuses).") AND ab.list_id IN (".implode(',', $listids).") GROUP BY a.ID LIMIT $offset, $limit";

			$data2 = $wpdb->get_results($sql);
			if(!empty($data)){
				$data = array_merge($data, $data2);
			}else{
				$data = $data2;
			}

		}

		$counter = 1+$offset;

		$statusnames = mymail('subscribers')->get_status(NULL, true);

		foreach($data as $user){

			$row = array();

			foreach($d['column'] as $col){
				switch ($col) {
					case '_number':
						$val = $counter;
						break;
					case 'email':
						$val = $user->email;
						break;
					case '_listnames':
						$list = mymail('subscribers')->get_lists($user->ID);
						$val = implode(', ', wp_list_pluck( $list, 'name' ) );
						break;
					case 'status':
						$val = $statusnames[$user->status];
						break;
					case 'statuscode':
						$val = $user->status;
						break;
					case 'ip':
					case 'ip_signup':
					case 'ip_comfirm':
						$val = isset($user->{$col}) ? $user->{$col} : '';
						break;
					case 'added':
					case 'updated':
					case 'signup':
					case 'confirm':
						$val = !empty($user->{$col}) ? ($dateformat ? date($dateformat, $user->{$col}) : $user->{$col}) : '';
						break;
					default:
						$val = isset($user->{$col}) ? $user->{$col} : '';
						if($dateformat && in_array($col, $custom_date_fields))
							$val = date($dateformat, strtotime($user->{$col}));
						//remove linebreaks
						$val = preg_replace("/[\n\r]/"," ",$val);
				}

				if(function_exists('mb_convert_encoding'))
					$val = mb_convert_encoding($val, $encoding, 'UTF-8');

				$row[] = str_replace(';', ',', $val);
			}

			$raw_data[] = $row;

			$counter++;
		}

		$output = '';

		if($outputformat == 'html'){

			if($useheader){
				$firstrow = array_shift($raw_data);
				$output .= '<tr><th>'.implode('</th><th>', $firstrow)."</th></tr>\n";
			}
			foreach($raw_data as $row){
				$output .= '<tr><td>'.implode('</td><td>', $row)."</td></tr>\n";
			}

		}else{

			foreach($raw_data as $row){
				$output .= implode(';', $row)."\n";
			}

		}

		try {

			$bytes = file_put_contents($filename, $output, FILE_APPEND);

			$return['total'] = size_format(filesize($filename), 2);

			$return['success'] = true;
			$return['bytes'] = $bytes;

			if($bytes === 0){

				$return['finished'] = true;

				//finished
				$folder = MYMAIL_UPLOAD_DIR;

				$finalname = dirname($filename).'/mymail_export_'.date('Y-m-d-H-i-s').'.'.$outputformat;
				$return['success'] = copy($filename, $finalname);
				@unlink( $filename );
				$return['filename'] = admin_url('admin-ajax.php?action=mymail_download_export_file&file='.basename($finalname).'&format='.$outputformat.'&_wpnonce='.wp_create_nonce ('mymail_nonce'));

			}

		} catch ( Exception $e){

			$return['success'] = false;
			$return['msg'] = $e->getMessage();

		}


		@header( 'Content-type: application/json' );
		echo json_encode($return);
		exit;
	}


	public function ajax_download_export_file() {

		$this->ajax_nonce( 'not allowed' );

		$folder = MYMAIL_UPLOAD_DIR;

		$file = $folder.'/'.$_REQUEST['file'];

		if(!file_exists($file)) die( 'not found' );

		$format = $_REQUEST['format'];

		$filename = basename($file);

		send_nosniff_header();
		nocache_headers();

		switch($format){
			case 'html':
				header('Content-Type: text/html; name="'.$filename.'.html"');
				break;
			case 'csv' :
				header('Content-Type: text/csv; name="'.$filename.'.csv"');
				header('Content-Transfer-Encoding: binary');
				break;
			default;
				die('format not allowed');
		}

		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header('Content-Length: '.filesize($file));
		header('Connection: close');

		if($format == 'html') echo '<table>';
		readfile($file);
		if($format == 'html') echo '</table>';

		mymail_require_filesystem();

		global $wp_filesystem;

		$wp_filesystem->delete( $file );
		exit;

	}


	public function ajax_delete_contacts() {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if(!current_user_can('mymail_bulk_delete_subscribers')){
			$return['msg'] = 'no allowed';

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;
		}

		parse_str($_POST['data'], $d);


		global $wpdb;

		$count = 0;
		$listids = isset($d['lists']) ? array_filter($d['lists'], 'is_numeric') : array();
		$statuses = isset($d['status']) ? array_filter($d['status'], 'is_numeric') : array();

		set_transient( 'mymail_cron_lock', time() );

		if(isset($d['nolists'])){

			$count += mymail('lists')->count(false, $statuses);

			$subscriber_ids = mymail('campaigns')->get_subscribers_by_lists(NULL, NULL, NULL, true);

			$ra = isset($d['remove_actions']) ? ',g' : '';

			$sql = "DELETE a,b,c,e,f $ra FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS b ON ( a.ID = b.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS c ON ( a.ID = c.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_lists AS d ON ( d.ID = b.list_id ) LEFT JOIN {$wpdb->prefix}mymail_queue AS e ON ( a.ID = e.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS f ON ( a.ID = f.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_actions AS g ON ( a.ID = g.subscriber_id ) WHERE a.status IN (".implode(', ', $statuses).") AND d.ID IS NULL";

			if($return['success'] = !!$wpdb->query($sql)){
				$wpdb->query("UPDATE {$wpdb->prefix}mymail_actions SET subscriber_id = 0 WHERE subscriber_id IN (".implode(',', $subscriber_ids).")");
			}

		}

		if(!empty($listids)){

			$count += mymail('lists')->count($listids, $statuses);

			$subscriber_ids = mymail('campaigns')->get_subscribers_by_lists($listids, NULL, NULL, true);

			$rl = isset($d['remove_lists']) ? ',d' : '';
			$ra = isset($d['remove_actions']) ? ',g' : '';

			$sql = "DELETE a,b,c $rl,e,f $ra FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS b ON ( a.ID = b.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS c ON ( a.ID = c.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_lists AS d ON ( d.ID = b.list_id ) LEFT JOIN {$wpdb->prefix}mymail_queue AS e ON ( a.ID = e.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS f ON ( a.ID = f.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_actions AS g ON ( a.ID = g.subscriber_id ) WHERE a.status IN (".implode(', ', $statuses).") AND d.ID IN (".implode(', ', $listids).")";

			if($return['success'] = !!$wpdb->query($sql)){
				$wpdb->query("UPDATE {$wpdb->prefix}mymail_actions SET subscriber_id = 0 WHERE subscriber_id IN (".implode(',', $subscriber_ids).")");

			}

		}

		if($return['success']){

			mymail()->optimize_tables();
			$return['msg'] = sprintf(__('%s subscribers removed', 'mymail'), number_format_i18n($count));

		}else{

			$return['msg'] = __('no subscribers removed', 'mymail');
		}

		delete_transient( 'mymail_cron_lock' );

		@header( 'Content-type: application/json' );
		echo json_encode($return);
		exit;

	}


	private function ajax_nonce($return = NULL, $nonce = 'mymail_nonce') {
		if (!wp_verify_nonce($_REQUEST['_wpnonce'], $nonce)) {
			if (is_string($return)) {
				wp_die($return);
			}else {
				die($return);
			}
		}

	}


	public function media_upload_form( $errors = null ) {

		global $type, $tab, $pagenow, $is_IE, $is_opera;

		if ( function_exists('_device_can_upload') && ! _device_can_upload() ) {
			echo '<p>' . __('The web browser on your device cannot be used to upload files. You may be able to use the <a href="http://wordpress.org/extend/mobile/">native app for your device</a> instead.', 'mymail') . '</p>';
			return;
		}

		$upload_size_unit = $max_upload_size = wp_max_upload_size();
		$sizes = array( 'KB', 'MB', 'GB' );

		for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) {
			$upload_size_unit /= 1024;
		}

		if ( $u < 0 ) {
			$upload_size_unit = 0;
			$u = 0;
		} else {
			$upload_size_unit = (int) $upload_size_unit;
		}
	?>

	<div id="media-upload-notice"><?php

		if (isset($errors['upload_notice']) )
			echo $errors['upload_notice'];

	?></div>
	<div id="media-upload-error"><?php

		if (isset($errors['upload_error']) && is_wp_error($errors['upload_error']))
			echo $errors['upload_error']->get_error_message();

	?></div>
	<?php
	if ( is_multisite() && !is_upload_space_available() ) {
		return;
	}

	$post_params = array(
			"action" => "mymail_import_subscribers_upload_handler",
			"_wpnonce" => wp_create_nonce('mymail_nonce'),
	);
	$upload_action_url = admin_url('admin-ajax.php');


	$plupload_init = array(
		'runtimes' => 'html5,silverlight,flash,html4',
		'browse_button' => 'plupload-browse-button',
		'container' => 'plupload-upload-ui',
		'drop_element' => 'drag-drop-area',
		'file_data_name' => 'async-upload',
		'multiple_queues' => true,
		'max_file_size' => $max_upload_size . 'b',
		'url' => $upload_action_url,
		'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
		'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
		'filters' => array( array('title' => __( 'Comma-separated values (CSV)', 'mymail' ), 'extensions' => 'csv') ),
		'multipart' => true,
		'urlstream_upload' => true,
		'multipart_params' => $post_params,
		'multi_selection' => false
	);

	?>

	<script type="text/javascript">
	var wpUploaderInit = <?php echo json_encode($plupload_init); ?>;
	</script>

	<div id="plupload-upload-ui" class="hide-if-no-js">
	<div id="drag-drop-area">
		<div class="drag-drop-inside">
		<p class="drag-drop-info"><?php _e('Drop your list here', 'mymail'); ?></p>
		<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files', 'mymail'); ?></p>
		<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select File', 'mymail'); ?>" class="button" /></p>
		</div>
	</div>
	</div>

	<div id="html-upload-ui" class="hide-if-js">
		<p id="async-upload-wrap">
			<label class="screen-reader-text" for="async-upload"><?php _e('Upload', 'mymail'); ?></label>
			<input type="file" name="async-upload" id="async-upload" />
			<?php submit_button( __( 'Upload', 'mymail' ), 'button', 'html-upload', false ); ?>
			<a href="#" onclick="try{top.tb_remove();}catch(e){}; return false;"><?php _e('Cancel', 'mymail'); ?></a>
		</p>
		<div class="clear"></div>
	</div>

	<p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %s.', 'mymail' ), esc_html($upload_size_unit.$sizes[$u]) ); ?> <?php _e('Split your lists into max 50.000 subscribers each', 'mymail'); ?></p>
	<?php
	if ( ($is_IE || $is_opera) && $max_upload_size > 100 * 1024 * 1024 ) { ?>
		<span class="big-file-warning"><?php _e('Your browser has some limitations uploading large files with the multi-file uploader. Please use the browser uploader for files over 100MB.', 'mymail'); ?></span>
	<?php }

	}


	private function get_separator( $string, $fallback = ';') {
		$seps = array(';',',','|',"\t");
		$max = 0;
		$separator = false;
		foreach($seps as $sep){
			$count = substr_count($string, $sep);
			if($count > $max){
				$separator = $sep;
				$max = $count;
			}
		}

		if($separator) return $separator;
		return $fallback;
	}


}
