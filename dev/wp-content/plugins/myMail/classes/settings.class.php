<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_settings {

	public function __construct() {

		register_activation_hook(MYMAIL_FILE, array( &$this, 'activate'));
		register_deactivation_hook(MYMAIL_FILE, array( &$this, 'deactivate'));

		add_action('init', array( &$this, 'init'));

	}

	public function init() {

		if(is_admin()){
			add_action('admin_menu', array( &$this, 'add_register_menu'), 70);
			add_action('admin_init', array( &$this, 'register_settings'));
			add_action('admin_init', array( &$this, 'actions'));

			if(mymail_option('_flush_rewrite_rules')){
				flush_rewrite_rules();
				mymail_update_option('_flush_rewrite_rules', false);
			}

			if(isset($_GET['mymail_create_homepage']) && $_GET['mymail_create_homepage']){

				include MYMAIL_DIR . 'includes/static.php';

				if($id = wp_insert_post($mymail_homepage)){
					mymail_notice(__('Homepage created', 'mymail'), '', true);
					mymail_update_option('homepage', $id);
					wp_redirect('post.php?post='.$id.'&action=edit&message=10&mymail_remove_notice=mymail_no-homepage');
					exit;
				}


			}
		}

	}


	/*----------------------------------------------------------------------*/
	/* Settings
	/*----------------------------------------------------------------------*/

	private function get_defaults() {

		$current_user = wp_get_current_user();

		global $wp_roles;

		include MYMAIL_DIR . 'includes/static.php';

		return array(
			'from_name' => get_bloginfo('name'),
			'from' => $current_user->user_email,
			'reply_to' => $current_user->user_email,
			'default_template' => 'mymail',
			'send_offset' => 30,
			'embed_images' => false,
			'charset' => 'UTF-8',
			'encoding' => '8bit',
			'post_count' => 30,
			'autoupdate' => 'minor',
			'trackcountries' => false,

			'bounce' => false,
			'bounce_server' => '',
			'bounce_port' => 110,
			'bounce_user' => '',
			'bounce_pwd' => '',
			'bounce_attempts' => 3,
			'bounce_delete' => true,
			'system_mail' => false,

			'homepage' => false,
			'share_button' => true,
			'share_services' => array(
				'twitter',
				'facebook',
				'google',
			),
			'frontpage_pagination' => true,
			'slug' => 'newsletter',
			'slugs' => array(
				'confirm' => 'confirm',
				'subscribe' => 'subscribe',
				'unsubscribe' => 'unsubscribe',
				'profile' => 'profile'
			),

			'subscriber_notification_receviers' => $current_user->user_email,
			'text' => array(
				'confirmation' => __('Please confirm your subscription!', 'mymail'),
				'success' => __('Thanks for your interest!', 'mymail'),
				'error' => __('Following fields are missing or incorrect', 'mymail'),
				'newsletter_signup' => __('Sign up to our newsletter', 'mymail'),
				'unsubscribe' => __('You have successfully unsubscribed!', 'mymail'),
				'unsubscribeerror' => __('An error occurred! Please try again later!', 'mymail'),
				'profile_update' => __('Profile updated!', 'mymail'),
				'email' => __('Email', 'mymail'),
				'firstname' => __('First Name', 'mymail'),
				'lastname' => __('Last Name', 'mymail'),
				'lists' => __('Lists', 'mymail'),
				'submitbutton' => __('Subscribe', 'mymail'),
				'profilebutton' => __('Update Profile', 'mymail'),
				'unsubscribebutton' => __('Yes, unsubscribe me', 'mymail'),
				'unsubscribelink' => _x('unsubscribe', 'unsubscribelink', 'mymail'),
				'webversion' => __('webversion', 'mymail'),
				'forward' => __('forward to a friend', 'mymail'),
				'profile' => __('update profile', 'mymail'),
			),
			'custom_field' => array(),
			'synclist' => array(
				'firstname' => 'first_name',
				'lastname' => 'last_name'
			),
			'register_comment_form_status' => array('1', '0'),
			'register_comment_form_confirmation' => true,
			'register_comment_form_lists' => array(),
			'register_signup_confirmation' => true,
			'register_signup_lists' => array(),
			'register_other' => true,
			'register_other_confirmation' => true,
			'register_other_lists' => array(),
			'register_other_roles' => ($wp_roles) ? array_keys($wp_roles->get_names()) : array('administrator'),
			'ajax_form' => true,
			'forms' => array(
				array(
					'name' => __('Default Form', 'mymail'),
					'id' => 0,
					'asterisk' => true,
					'submitbutton' => __('Subscribe', 'mymail'),
					'lists' => array(),
					'order' => array(
						'email', 'firstname', 'lastname',
					),
					'required' => array(
						'email'
					),
					'double_opt_in' => true,
					'template' => 'notification.html',
					'subscription_resend_count' => 2,
					'subscription_resend_time' => 48,
					'text' => array(
						'subscription_subject' => __('Please confirm', 'mymail'),
						'subscription_headline' => __('Please confirm your Email Address', 'mymail'),
						'subscription_text' => sprintf(__("You'll need to confirm your email address. Please click the link below to confirm. %s", 'mymail'), "\n{link}"),
						'subscription_link' => __('Click here to confirm', 'mymail'),
					),

				),
			),
			'profile_form' => 0,

			'form_css' => str_replace(array('MYMAIL_URI'), array(MYMAIL_URI), $mymail_form_css),
			'embed_form_css' => true,

			'tags' => array(
				'can-spam' => sprintf(__('You have received this email because you have subscribed to %s as {email}. If you no longer wish to receive emails please {unsub}', 'mymail'), '<a href="{homepage}">{company}</a>'),
				'notification' => __("If you received this email by mistake, simply delete it. You won't be subscribed if you don't click the confirmation link", 'mymail'),
				'copyright' => '&copy; {year} {company}, ' . __('All rights reserved', 'mymail'),
				'company' => get_bloginfo('name'),
				'homepage' => get_bloginfo('url')
			),
			'custom_tags' => array(
				'my-tag' => __('Replace Content', 'mymail')
			),

			'tweet_cache_time' => 60,

			'interval' => 5,
			'send_at_once' => 20,
			'send_limit' => 10000,
			'send_period' => 24,
			'split_campaigns' => true,
			'pause_campaigns' => true,
			'send_delay' => 0,
			'max_execution_time' => 0,
			'cron_service' => 'wp_cron',
			'cron_secret' => md5(uniqid()),
			'cron_lasthit' => false,

			'deliverymethod' => 'simple',
			'sendmail_path' => '/usr/sbin/sendmail',
			'smtp' => false,
			'smtp_host' => '',
			'smtp_port' => 25,
			'smtp_timeout' => 10,
			'smtp_secure' => '',
			'smtp_auth' => false,
			'smtp_user' => '',
			'smtp_pwd' => '',

			'bounce_check' => 5,
			'bounce_delay' => 60,

			'dkim' => false,
			'dkim_selector' => 'mymail',
			'dkim_domain' => $_SERVER['HTTP_HOST'],
			'dkim_identity' => '',
			'dkim_passphrase' => '',

			'purchasecode' => '',
			'ID' => md5(uniqid()),
			'welcome' => true,

		);

	}

	private function define_settings($capabilities = true) {

		update_option( 'mymail_purchasecode_disabled', false );

		global $mymail_options;

		$options = $this->get_defaults();

		//merge options with MyMail options (don't override)
		$mymail_options = wp_parse_args($mymail_options, $options);

		update_option( 'mymail_options', $mymail_options );

		if($capabilities) $this->set_capabilities();


	}
	public function actions() {

		if(isset($_GET['reset-settings']) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'mymail-reset-settings' ))
			$this->reset_settings(true);

		if(isset($_GET['reset-capabilities']) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'mymail-reset-capabilities' ))
			$this->reset_capabilities(true);

		if(isset($_GET['reset-limits']))
			$this->reset_limits(true);

	}


	public function add_register_menu() {

		global $submenu;

		$page = add_submenu_page('options-general.php', __('Newsletter Settings', 'mymail'), __('Newsletter', 'mymail'), 'manage_options', 'newsletter-settings', array( &$this, 'newsletter_settings'));
		add_action('load-' . $page, array( &$this, 'scripts_styles'));

		if(current_user_can('manage_options')){
			$submenu['edit.php?post_type=newsletter'][] = array(
				__( 'Settings', 'mymail' ),
				'manage_options',
				'options-general.php?page=newsletter-settings',
				__( 'Settings', 'mymail' ),
			);
		}

	}

	public function scripts_styles() {
		wp_register_script('mymail-settings-script', MYMAIL_URI . 'assets/js/settings-script.js', array('jquery'), MYMAIL_VERSION);
		wp_enqueue_script('mymail-settings-script');
		wp_localize_script('mymail-settings-script', 'mymailL10n', array(
			'add' => __('add', 'mymail'),
			'fieldname' => __('Field Name', 'mymail'),
			'tag' => __('Tag', 'mymail'),
			'type' => __('Type', 'mymail'),
			'textfield' => __('Textfield', 'mymail'),
			'dropdown' => __('Dropdown Menu', 'mymail'),
			'radio' => __('Radio Buttons', 'mymail'),
			'checkbox' => __('Checkbox','mymail'),
			'datefield' => __('Date','mymail'),
			'default' => __('default', 'mymail'),
			'default_checked' => __('checked by default', 'mymail'),
			'default_selected' => __('this field is selected by default', 'mymail'),
			'add_field' => __('add field', 'mymail'),
			'options' => __('Options', 'mymail'),
			'loading' => __('Loading', 'mymail'),
			'remove_field' => __('remove field', 'mymail'),
			'move_up' => __('move up', 'mymail'),
			'move_down' => __('move down', 'mymail'),
			'reserved_tag' => __('%s is a reserved tag!', 'mymail'),
			'create_new_keys' => __('You are about to create new DKIM keys. The old ones will get deleted. Continue?', 'mymail'),
			'sync_wp_user' => __('You are about to overwrite all subscriber data with the matching WordPress User data. Continue?', 'mymail'),
			'sync_subscriber' => __('You are about to overwrite all WordPress User data with the matching subscriber data. Continue?', 'mymail'),
		));
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-touch-punch');
		wp_register_style('mymail-settings-style', MYMAIL_URI . 'assets/css/settings-style.css', array(), MYMAIL_VERSION);
		wp_enqueue_style('mymail-settings-style');

	}
	public function register_settings() {

		//General
		register_setting('newsletter_settings', 'mymail_options', array( &$this, 'verify'));

		//Purchasecode
		if (!get_option('mymail_purchasecode_disabled')) {
			register_setting('newsletter_settings', 'mymail_purchasecode_disabled');
		}
	}

	public function newsletter_settings() {
		include MYMAIL_DIR . 'views/settings.php';
	}

	/*----------------------------------------------------------------------*/
	/* Plugin Activation / Deactivation
	/*----------------------------------------------------------------------*/



	public function activate() {

		global $wpdb, $mymail;

		if (is_network_admin() && is_multisite()) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		}else{

			$blogids = array(false);

		}

		foreach ($blogids as $blog_id) {

			if($blog_id) switch_to_blog( $blog_id );

			if(!get_option('mymail')) $this->define_settings();

			update_option('mymail', true);

		}

		if($blog_id) switch_to_blog($old_blog);


	}


	public function deactivate() {

		global $wpdb, $mymail;

		if (is_network_admin() && is_multisite()) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		}else{

			$blogids = array(false);

		}

		foreach ($blogids as $blog_id) {

			if($blog_id) switch_to_blog( $blog_id );

		}

		if($blog_id) switch_to_blog($old_blog);
	}




	public function reset_settings($redirect = false) {

		if(is_super_admin()){

			global $mymail_options;

			$mymail_options = $this->get_defaults();

			if(update_option( 'mymail_options', $mymail_options )){
				mymail_notice( __('Options have been reseted!', 'mymail'), 'updated', true );
				if($redirect){
					wp_redirect( 'options-general.php?page=newsletter-settings' );
					exit;
				}
			}
		}
	}




	public function reset_limits($redirect = false) {

		update_option('_transient_timeout__mymail_send_period_timeout', false);
		update_option('_transient__mymail_send_period_timeout', false);
		update_option('_transient__mymail_send_period', 0);

		mymail_notice(__('Limits have been reseted', 'mymail'), '', true);
		mymail_remove_notice('dailylimit');

		if($redirect){
			wp_redirect( 'options-general.php?page=newsletter-settings#delivery' );
			exit;
		}

	}




	public function reset_capabilities($redirect = false) {

		if(current_user_can('mymail_manage_capabilities')){

			$this->remove_capabilities();
			$this->set_capabilities();

			if($redirect){
				wp_redirect( 'options-general.php?page=newsletter-settings#capabilities' );
				exit;
			}
		}


	}

	public function update_capabilities() {

		global $wp_roles;

		if(!$wp_roles) return;

		include_once(MYMAIL_DIR . 'includes/capability.php');

		foreach($mymail_capabilities as $capability => $data){

			//admin has the cap so go on
			if(isset($wp_roles->roles['administrator']['capabilities'][$capability])) continue;

			$wp_roles->add_cap( 'administrator', $capability );

			foreach($wp_roles->roles as $role => $d){
				if(!isset($d['capabilities'][$capability]) && in_array($role, $data['roles'])) $wp_roles->add_cap( $role, $capability );
			}

		}

		return true;
	}


	public function set_capabilities() {

		include (MYMAIL_DIR . 'includes/capability.php');

		global $wp_roles;
		if(!$wp_roles){
			add_action('shutdown', array(&$this, 'set_capabilities'));
			return;
		}

		$roles = $wp_roles->get_names();
		$newcap = array();

		foreach($roles as $role => $title){

			$newcap[$role] = array();
		}


		foreach($mymail_capabilities as $capability => $data){

			//give admin all rights
			array_unshift($data['roles'], 'administrator');

			foreach($data['roles'] as $role){
				$wp_roles->add_cap( $role, $capability);
				$newcap[$role][] = $capability;

			}

		}

	}


	public function remove_capabilities() {

		$roles = mymail_option('roles');

		$newcap = array();

		if($roles){

			global $wp_roles;

			foreach($roles as $role => $capabilities){

				$newcap[$role] = array();

				foreach($capabilities as $capability){

					$wp_roles->remove_cap( $role, $capability);

				}

			}
		}

	}



	public function verify($options) {

		global $mymail;

		//create dkim keys
		if(isset($_POST['mymail_generate_dkim_keys'])){

			try {

				$res = openssl_pkey_new(array('private_key_bits' => isset($options['dkim_bitsize']) ? (int) $options['dkim_bitsize'] : 512));
				openssl_pkey_export($res, $dkim_private_key);
				$dkim_public_key = openssl_pkey_get_details($res);
				$dkim_public_key = $dkim_public_key["key"];
				$options['dkim_public_key'] = $dkim_public_key;
				$options['dkim_private_key'] = $dkim_private_key;
				add_settings_error( 'mymail_options', 'mymail_options', __('New DKIM keys have been created!', 'mymail'), 'updated' );

			} catch ( Exception $e ) {

				add_settings_error( 'mymail_options', 'mymail_options', __('Not able to create new DKIM keys!', 'mymail'));

			}

		}

		//uploaded country database
		if(!empty($_FILES['country_db_file']['name'])){

			$file = $_FILES['country_db_file'];

			$dest = MYMAIL_UPLOAD_DIR.'/'.$file['name'];
			if(move_uploaded_file($file['tmp_name'], $dest)){
				if(is_file($dest)){
					$options['countries_db'] = $dest;
					add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('File uploaded to %s', 'mymail'), '"'.$dest.'"'), 'updated' );
				}else{
					$options['countries_db'] = '';
				}
			}else{
				add_settings_error( 'mymail_options', 'mymail_options', __('unable to upload file', 'mymail') );
				$options['countries_db'] = '';
			}

		}

		//uploaded city database
		if(!empty($_FILES['city_db_file']['name'])){
			$file = $_FILES['city_db_file'];

			$dest = MYMAIL_UPLOAD_DIR.'/'.$file['name'];
			if(move_uploaded_file($file['tmp_name'], $dest)){
				if(is_file($dest)){
					$options['cities_db'] = $dest;
					add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('File uploaded to %s', 'mymail'), '"'.$dest.'"'), 'updated' );
				}else{
					$options['cities_db'] = '';
				}
			}else{
				add_settings_error( 'mymail_options', 'mymail_options', __('unable to upload file', 'mymail') );
				$options['cities_db'] = '';
			}
		}


		//change language
		if(isset($_POST['change-language']) && isset($_POST['language-file'])){

			$file = $_POST['language-file'] == 'en_US' ? false : MYMAIL_DIR . 'languages/mymail-'.$_POST['language-file'].'.mo';

			if(is_readable($file) || !$file){
				global $locale;
				$current_language = $locale;

				load_textdomain( 'mymail', $file );

				$defaults = $this->get_defaults();

				$options['text'] = $defaults['text'];
				$options['tags'] = $defaults['tags'];
				foreach($options['forms'] as $i => $form){
					$options['forms'][$i]['text'] = $defaults['forms'][0]['text'];
				}

				load_textdomain( 'mymail', MYMAIL_DIR . 'languages/mymail-'.$current_language.'.mo' );
				add_settings_error( 'mymail_options', 'mymail_options', __('Language changed!', 'mymail'), 'updated' );
			}

		}

		$verify = array('from', 'reply_to', 'homepage', 'trackcountries', 'trackcities', 'slug', 'slugs', 'hasarchive', 'custom_field', 'synclist', 'forms', 'form_css', 'send_at_once', 'send_delay', 'send_period', 'bounce', 'cron_service', 'cron_secret', 'interval', 'roles', 'tweet_cache_time', 'deliverymethod', 'smtp_host', 'bounce_check', 'bounce_delay', 'dkim_domain', 'dkim_selector', 'dkim_identity', 'dkim_passphrase', 'dkim_private_key', 'purchasecode');

		foreach($verify as $id){

			if(!isset($options[$id])) continue;

			$value = $options[$id];
			$old = mymail_option( $id );

			switch($id){

				case 'from':
				case 'reply_to':
				case 'bounce':
						if($value && !mymail_is_email($value)){
							add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('%s is not a valid email address', 'mymail'), '"'.$value.'"' ) );
							$value = $old;
						}
				break;

				case 'trackcountries':
						if(empty($options['countries_db'])) $options['countries_db'] = MYMAIL_UPLOAD_DIR.'/GeoIPv6.dat';
						if(!isset($options['countries_db']) || !is_file($options['countries_db'])){
							add_settings_error( 'mymail_options', 'mymail_options', __('No country database found! Please load it!', 'mymail'));
							$value = false;
						}
				break;

				case 'trackcities':
						if(empty($options['cities_db'])) $options['cities_db'] = MYMAIL_UPLOAD_DIR.'/GeoIPCity.dat';
						if(!isset($options['cities_db']) || !is_file($options['cities_db'])){
							add_settings_error( 'mymail_options', 'mymail_options', __('No city database found! Please load it!', 'mymail'));
							$value = false;
						}
				break;

				case 'homepage':
					if($old != $value){
						mymail_remove_notice('no-homepage');
						$options['_flush_rewrite_rules'] = true;
					}
					if(!get_permalink( $value ))
						add_settings_error( 'mymail_options', 'mymail_options', __('Please define a homepage for the newsletter on the frontend tab', 'mymail'));

				break;


				case 'slug':
					if($old != $value){
						$value = sanitize_title($value);
						$options['_flush_rewrite_rules'] = true;
					}
				break;


				case 'slugs':
					if(serialize($old) != serialize($value)){
						foreach($value as $key => $v){
							$v = sanitize_title($v);
							$value[$key] = (empty($v) ? $key : $v);
						}
						$options['_flush_rewrite_rules'] = true;
					}
				break;


				case 'hasarchive':
					$page = get_page_by_path($options['slug']);
					if($page){
						add_settings_error( 'mymail_options', 'mymail_options', sprintf(__("Please change the slug or permalink of %s since it's used by the archive page", 'mymail'), '<a href="post.php?post='.$page->ID.'&action=edit">'.$page->post_title.'</a>'));
					}
					if($old != $value){
						$options['_flush_rewrite_rules'] = true;
					}
				break;


				case 'interval':

					$value = max(0.1, $value);

				break;


				case 'cron_service':

						wp_clear_scheduled_hook('mymail_cron_worker');
						if ($value == 'wp_cron'){
							if(!wp_next_scheduled('mymail_cron_worker')) {
								wp_schedule_event(floor(time()/300)*300, 'mymail_cron_interval', 'mymail_cron_worker');
							}
						}

				break;


				case 'cron_secret':

						if($old != $value){
							if($value == '') $value = md5(uniqid());
						}

				break;


				case 'custom_field':
						if(serialize($old) != serialize($value)){

							if(count($value) > 58){
								$removed = wp_list_pluck(array_splice($value, 58), 'name');
								add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('You can only have up to %d custom fields! Following fields have been removed: %s', 'mymail'), 58, '<br>"'.implode('"," ', $removed).'"'));

								$value = array_splice($value, 0, 58);
							}

						}
				break;

				case 'synclist':

					if(serialize($old) != serialize($value)){
						$data = $value;
						$value = array();

						foreach ($data as $syncitem) {
							if(isset($syncitem['field']) && $syncitem['field'] != -1 && $syncitem['meta'] != -1){
								$value[$syncitem['field']] = $syncitem['meta'];
							}
						}
					}

				break;


				case 'forms':
					if(serialize($old) != serialize($value)){

						$folder = MYMAIL_UPLOAD_DIR;

						foreach($value as $i => $form){

							if(empty($value[$i]['vcard_content'])) $value[$i]['vcard'] = false;

							if(!is_dir($folder)) wp_mkdir_p($folder);

							$value[$i]['vcard_filename'] = sanitize_file_name($value[$i]['vcard_filename']);

							$filename = $folder.'/'.$value[$i]['vcard_filename'];
							//$value[$i]['vcard_filename'] = basename($filename);

							if(!empty($value[$i]['vcard'])){
								file_put_contents( $filename , $value[$i]['vcard_content']);
							}else{
								if(file_exists($filename)) @unlink( $filename );
							}

						}

					}

				break;


				case 'form_css':

					if(isset($_POST['mymail_reset_form_css'])) {
						require_once(MYMAIL_DIR . 'includes/static.php');
						$value = $mymail_form_css;
						add_settings_error( 'mymail_options', 'mymail_options', __('Form CSS reseted!', 'mymail'), 'updated' );
					}
					if($old != $value){
						delete_transient( 'mymail_form_css' );
						$value = str_replace(array('MYMAIL_URI'), array(MYMAIL_URI), $value);
						$options['form_css_hash'] = md5(MYMAIL_VERSION.$value);

					}
				break;


				case 'send_at_once':

					if($old != $value){
						//at least 1
						$value = max($value, 1);
						if($value >= 300) add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('sending %s emails at once can cause problems with statistics cause of a server timeout or to much memory usage! You should decrease it if you have problems!', 'mymail'), $value) );
					}

				break;


				case 'send_delay':

					//at least 0
					$value = max($value, 0);

				break;


				case 'send_period':
						if($old != $value){
							if($timestamp = get_option('_transient_timeout__mymail_send_period_timeout')){
								$new = time()+$value*3600;
								update_option('_transient_timeout__mymail_send_period_timeout', $new);
							}else{
								update_option('_transient__mymail_send_period_timeout', false);
							}
							mymail_remove_notice('dailylimit');
						}

				break;


				case 'deliverymethod':

					if($old != $value){

						if($value == 'gmail'){
							if($options['send_limit'] != 500){
								$options['send_limit'] = 500;
								$options['send_period'] = 24;
								update_option('_transient__mymail_send_period_timeout', false);
								add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('Send limit has been adjusted to %d for Gmail', 'mymail'), 500) );
							}

							if(function_exists( 'fsockopen' )){
								$host = 'smtp.googlemail.com';
								$port = 587;
								$conn = fsockopen($host, $port, $errno, $errstr, 5);

								if(is_resource($conn)){

									fclose($conn);

								}else{

									add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('Not able to connected to %1$s via port %2$s! You may not be able to send mails cause of the locked port %3$s. Please contact your host or choose a different delivery method!', 'mymail'), '"'.$host.'"', $port, $port) );

								}

							}
						}
					}

				break;


				case 'smtp_host':

					if(function_exists( 'fsockopen' ) && $options['deliverymethod'] == 'smtp') :
						$host = $options['smtp_host'];
						$port = intval($options['smtp_port']);
						$conn = fsockopen($host, $port, $errno, $errstr, 5);

						if(is_resource($conn)){

							fclose($conn);

						}else{

								add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('Not able to connected to %1$s via port %2$s! You may not be able to send mails cause of the locked port %3$s. Please contact your host or choose a different delivery method!', 'mymail'), '"'.$host.'"', $port, $port) );

						}

					endif;

				break;


				case 'roles':
						if(serialize($old) != serialize($value)){
							require_once(MYMAIL_DIR . 'includes/capability.php');

							global $wp_roles;

							if(!$wp_roles) break;

							$newvalue = array();
							//give admin all rights
							$value['administrator'] = array();
							//foreach role
							foreach($value as $role => $capabilities){

								if(!isset($newvalue[$role])) $newvalue[$role] = array();

								foreach($mymail_capabilities as $capability => $data){
									if(in_array($capability, $capabilities) || 'administrator' == $role){

										$wp_roles->add_cap( $role, $capability);
										$newvalue[$role][] = $capability;
									}else{
										$wp_roles->remove_cap( $role, $capability);
									}
								}



							}
							$value = $newvalue;
						}

				break;


				case 'tweet_cache_time':
					$value = (int) $value;
					if($value < 10){
						$value = 10;
						add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('The caching time for tweets must be at least %d minutes', 'mymail'), '10' ) );
					}

				break;

				case 'bounce_check':
				case 'bounce_delay':

					//at least 1
					$value = intval(max($value, 1));

				break;

				case 'dkim_domain':
				case 'dkim_selector':
				case 'dkim_identity':
						if($old != $value){
							$value = trim($value);
						}
					break;
				case 'dkim_private_key':

						if(!$options['dkim']) break;

						$hash = md5($value);

						$file = MYMAIL_UPLOAD_DIR.'/dkim/'.$hash.'.pem';

						WP_Filesystem();
						global $wp_filesystem;

						//remove old
						if(isset($options['dkim_private_hash']) && is_file($folder . '/' . $options['dkim_private_hash'].'.pem')){
							if($hash != $options['dkim_private_hash'])
								$wp_filesystem->delete($folder.'/'.$options['dkim_private_hash'].'.pem');
						}


						//create folder
						if(!is_dir(dirname($file))){
							wp_mkdir_p(dirname($file));
							$wp_filesystem->put_contents( dirname($file).'/index.php', '<?php //silence is golden ?>', FS_CHMOD_FILE);
						}

						if ($wp_filesystem->put_contents( $file, $value ) ) {
							$options['dkim_private_hash'] = $hash;
						}

				break;


				case 'purchasecode':

						if($old != $value && $value){
							if(preg_match('#^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$#', $value)){
							}else{
								add_settings_error( 'mymail_options', 'mymail_options', sprintf(__('The provided purchasecode %s is invalid', 'mymail'), '"'.$value.'"' ) );
								$value = '';
							}
						}
				break;


			}

			$options[$id] = $value;

		}

		//no need to save them
		if(isset($options['roles'])) unset($options['roles']);

		$options = apply_filters('mymail_verify_options', $options);

		//clear everything thats cached
		mymail_clear_cache();

		return $options;
	}



	public function get_vcard() {
		$current_user = wp_get_current_user();

		$text = 'BEGIN:VCARD'."\n";
		$text .= 'N:Firstname;Lastname;;;'."\n";
		$text .= 'ADR;INTL;PARCEL;WORK:;;StreetName;City;State;123456;Country'."\n";
		$text .= 'EMAIL;INTERNET:'.$current_user->user_email.''."\n";
		$text .= 'ORG:'.get_bloginfo('name').''."\n";
		$text .= 'URL;WORK:'.home_url().''."\n";
		$text .= 'END:VCARD'."\n";
		return $text;
	}


	private function check_port($host, $port){

		if(!function_exists( 'fsockopen' )) return 'requires fsockopen to check ports.';

		$conn = @fsockopen($host, $port, $errno, $errstr, 5);

		$return = (is_resource($conn) ? '(' . getservbyport($port, 'tcp') . ') open.' : 'closed ['.$errstr.']');

		is_resource($conn) ? fclose($conn) : '';

		return $return;

	}



	public function get_system_info($space = 30){

		global $wpdb;

		$mail = mymail('mail');
		$mail->to = 'deadend@newsletter-plugin.com';
		$mail->subject = 'test';
		$mail->debug();

		if($mail->send_notification( 'Sendtest', 'this test message can get deleted', array('notification' => ''), false )){
			$send_success = 'OK';
		}else{
			$send_success = strip_tags($mail->get_errors());
		}
		$request['cmd'] = '_notify-validate';

		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', array(
			'sslverify' => true,
			'timeout'   => 5,
			'body'      => array('cmd' => '_notify-validate'),
		) );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$wp_remote_post = 'works';
		} else {
			$wp_remote_post = 'does not work: ' . $response->get_error_message();
		}
		$response = wp_remote_post( 'https://update.revaxarts.com/');
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$update_server = 'works';
		} else {
			$update_server = '' . $response->get_error_message() . " - Please allow connection to update.revaxarts.com!";
		}

		$lasthit = get_option('mymail_cron_lasthit', array());

		$homepage = get_permalink( mymail_option('homepage') );
		$endpoints = mymail('helper')->using_permalinks() ? array_values(mymail_option('slugs')) : false;

		$cron_url = defined('MYMAIL_MU_CRON')
			? add_query_arg(array('action' => 'mymail_cron_worker', 'secret' => mymail_option('cron_secret')), admin_url('admin-ajax.php'))
			: MYMAIL_URI . 'cron.php?'.mymail_option('cron_secret');

		$settings = array(
			"SITE_URL" =>              site_url(),
			"HOME_URL" =>              home_url(),
			'--',
			"MyMail Version" =>        MYMAIL_VERSION,
			"Updated From" =>          get_option( 'mymail_version_old', 'N/A' ),
			"WordPress Version" =>     get_bloginfo( 'version' ),
			"MyMail DB version" =>     get_option( 'mymail_dbversion' ),
			"Permalink Structure" =>   get_option( 'permalink_structure' ),
			"MyMail Licensecode" =>    mymail_option('purchasecode') ? mymail_option('purchasecode') : 'Not defined! check "Purchasecode" tab',
			'--',
			"Newsletter Homepage" =>   $homepage.' (#'.mymail_option('homepage').')',
			"Endpoints" =>             $endpoints ? '/'.implode(', /', $endpoints) : 'No Permalink structure',
			"Track Countries" =>       mymail_option('trackcountries') ? 'Yes' : 'No',
			"Country DB" =>            file_exists(mymail_option('countries_db')) ? 'DB exists ('.date('Y-m-d H:i:s', filemtime(mymail_option('countries_db'))).', '.human_time_diff(filemtime(mymail_option('countries_db'))).')' : 'DB is missing',
			"Track Cities" =>          mymail_option('trackcities') ? 'Yes' : 'No',
			"City DB" =>               file_exists(mymail_option('cities_db')) ? 'DB exists ('.date('Y-m-d H:i:s', filemtime(mymail_option('cities_db'))).', '.human_time_diff(filemtime(mymail_option('cities_db'))).')' : 'DB is missing',
			'--',
			"WordPress Cron" =>        (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) ? 'Not available - remove DISABLE_WP_CRON constant' : 'Available',
			"Cron Service" =>          mymail_option('cron_service'),
			"Cron URL" =>              $cron_url,
			"Cron Interval" =>         mymail_option('interval').' MIN',
			"Cron Lasthit" =>          !empty($lasthit['timestamp']) ? (date('Y-m-d H:i:s', $lasthit['timestamp']).', '.human_time_diff($lasthit['timestamp'])) : 'NEVER',
			'--',
			"Delivery Method" =>       mymail_option('deliverymethod'),
			"SMTP Port check" =>       mymail_option('deliverymethod') == 'smtp'
										? mymail_option('smtp_host').':'.mymail_option('smtp_port').' - '.$this->check_port(mymail_option('smtp_host'), mymail_option('smtp_port'))
										: 'no smtp',
			"Send at once" =>          mymail_option('send_at_once'),
			"Send limit" =>            mymail_option('send_limit'),
			"Send period" =>           mymail_option('send_period'),
			'--',
			"Test Mail" =>             $send_success,
			'--',
			"Port 110" =>              $this->check_port('pop.gmx.net', 110),
			"Port 995" =>              $this->check_port('pop.gmail.com', 995),
			"Port 993" =>              $this->check_port('smtp.gmail.com', 993),
			"Port 25" =>               $this->check_port('smtp.gmail.com', 25),
			"Port 465" =>              $this->check_port('smtp.gmail.com', 465),
			"Port 587" =>              $this->check_port('smtp.gmail.com', 587),
			'--',
			"PHP Version" =>           PHP_VERSION,
			"MySQL Version" =>         $wpdb->db_version(),
			"Web Server Info" =>       $_SERVER['SERVER_SOFTWARE'],
			"Multi-site" =>            is_multisite() ? 'Yes' . "\n" : 'No',
			'--',
			"PHP Safe Mode" =>         ini_get( 'safe_mode' ) ? "Yes" : "No",
			"PHP Memory Limit" =>      ini_get( 'memory_limit' ),
			"PHP Post Max Size" =>     ini_get( 'post_max_size' ),
			"PHP Time Limit" =>        ini_get( 'max_execution_time' ) . " sec",
			"PHP Max Input Vars" =>    ini_get( 'max_input_vars'),
			'--',
			"WP_DEBUG" =>              defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set',
			"DISPLAY ERRORS" =>        ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A',
			'--',
			"WP Table Prefix" =>       "Length: ". strlen( $wpdb->prefix ) . " Status:". ( strlen( $wpdb->prefix )>16  ? " ERROR: Too Long" : " Acceptable" ),
			'--',
			"Session" =>               isset( $_SESSION ) ? 'Enabled' : 'Disabled',
			"Session Name" =>          esc_html( ini_get( 'session.name' ) ),
			"Cookie Path" =>           esc_html( ini_get( 'session.cookie_path' ) ),
			"Save Path" =>             esc_html( ini_get( 'session.save_path' ) ),
			"Use Cookies" =>           ini_get( 'session.use_cookies' ) ? 'On' : 'Off',
			"Use Only Cookies" =>      ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off',
			'--',
			"UPLOAD_MAX_FILESIZE" =>   ( function_exists( 'phpversion' ) ) ? ( size_format ( ini_get( 'upload_max_filesize' )*1048576 ) ) : 'unknown',
			"POST_MAX_SIZE" =>         ( function_exists( 'phpversion' ) ) ? ( size_format ( ini_get( 'post_max_size' )*1048576 ) ) : 'unknown',
			"WordPress Memory Limit" =>( size_format( WP_MEMORY_LIMIT*1048576 ) ) ,
			"FSOCKOPEN" =>             ( function_exists( 'fsockopen' ) ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.',
			"DOMDocument" =>             ( class_exists( 'DOMDocument' ) ) ? 'DOMDocument extension installed' : 'DOMDocument is missing!',
			"SUHOSIN Installed" =>       extension_loaded('suhosin') ? "Yes" : "No",
			'--',
			"wp_remote_post" =>        $wp_remote_post,
			"External HTTP Requests" =>	( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) &&  WP_HTTP_BLOCK_EXTERNAL ) ? 'blocked' : 'not blocked',
			"Update Server Access" =>	$update_server,
			'--',
			"TEMPLATES" =>        '',
			'--',
			"ACTIVE PLUGINS" =>        '',
			'--',
			"CURRENT THEME" =>        '',
		);

		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $plugin_path => $plugin ):
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) )
				continue;

			$settings['ACTIVE PLUGINS'] .= $plugin['Name'].': '.$plugin['Version'] ."\n".str_repeat(' ', $space);

		endforeach;

		$templates = mymail('templates')->get_templates();
		$active = mymail_option('default_template');

		foreach ( $templates as $slug => $template ):

			$settings['TEMPLATES'] .= $template['name'].': '.$template['version'] . ' by ' . $template['author'] . ($active == $slug ? ' (default)' : '')."\n".str_repeat(' ', $space);

		endforeach;

		if ( function_exists('wp_get_theme') ) {
			$theme_data = wp_get_theme();
			$settings['CURRENT THEME'] = $theme_data->Name . ': ' . $theme_data->Version."\n".str_repeat(' ', $space).$theme_data->get('Author').' ('.$theme_data->get('AuthorURI').')';
		} else {
			$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$settings['CURRENT THEME'] = $theme_data['Name'] . ': ' . $theme_data['Version']."\n".str_repeat(' ', $space).$theme_data['Author'].' ('.$theme_data['AuthorURI'].')';
		}

		return apply_filters( 'mymail_system_info' , $settings );

	}

}
?>
