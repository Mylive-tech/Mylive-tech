<?php if (!defined('ABSPATH')) die('not allowed');

class mymail {

	private $template;
	private $post_data;
	private $campaign_data;
	private $mail = array();

	public $wp_mail = NULL;

	private $_classes = array();

	static $form_active;

	public function __construct() {

		register_activation_hook(MYMAIL_FILE, array( &$this, 'activate'));
		register_deactivation_hook(MYMAIL_FILE, array( &$this, 'deactivate'));

		$classes = array('campaigns', 'subscribers', 'lists', 'manage', 'templates', 'widget', 'frontpage', 'statistics', 'ajax', 'cron', 'queue', 'actions', 'bounce', 'update', 'helpmenu', 'dashboard', 'settings', 'geo');

		add_action('plugins_loaded', array( &$this, 'init'), 1);

		add_action('widgets_init', create_function( '', 'register_widget( "MyMail_Signup_Widget" );register_widget( "MyMail_Newsletter_List_Widget" );' ) );

		foreach($classes as $class){
			require_once MYMAIL_DIR . "classes/$class.class.php";
			$classname = "mymail_$class";
			if(class_exists($classname)) $this->_classes[$class] = new $classname();
		}

		$this->wp_mail = function_exists('wp_mail');

	}

	public function __call($method, $args) {

		if(!isset($this->_classes[$method])) throw new Exception("Class $method doesn't exists", 1);

		if(!is_a($this->_classes[$method], 'mymail_'.$method)) throw new Exception("__CALL Class $method doesn't exists", 1);

		return $this->_classes[$method];
	}


	public function stats() {
		return $this->statistics();
	}

	public function mail() {
		require_once MYMAIL_DIR . 'classes/mail.class.php';

		return mymail_mail::get_instance();
	}
	public function placeholder($content = '') {
		require_once MYMAIL_DIR . 'classes/placeholder.class.php';

		return new mymail_placeholder($content);
	}
	public function notification($file = 'notification.html', $template = NULL) {
		require_once MYMAIL_DIR . 'classes/notification.class.php';
		if(is_null($template)) $template = 'basic';
		return mymail_notification::get_instance($template, $file);
	}
	public function template($slug = NULL, $file = NULL) {
		if(is_null($slug)){
			$slug = mymail_option('default_template', 'mymail');
		}
		$file = is_null($file) ? 'index.html' : $file;
		require_once MYMAIL_DIR . 'classes/template.class.php';

		return new mymail_template($slug, $file);
	}
	public function form() {
		require_once MYMAIL_DIR . 'classes/form.class.php';

		return new mymail_form();
	}
	public function helper() {
		require_once MYMAIL_DIR . 'classes/helper.class.php';

		return new mymail_helper();
	}

	public function init() {

		load_plugin_textdomain( 'mymail', false, basename(MYMAIL_DIR) . '/languages' );

		//remove revisions if newsletter is finished
		add_action('mymail_reset_mail', array( &$this, 'reset_mail_delayed'), 10, 3);

		//add_action('mymail_cron', array( &$this, 'optimize_tables'), 99);

		$this->wp_mail_setup();

		if (is_admin()) {

			add_action('admin_enqueue_scripts', array( &$this, 'admin_scripts_styles'), 10, 1);
			add_action('admin_menu', array( &$this, 'special_pages'), 60);
			add_action('admin_notices', array( &$this, 'admin_notices') );

			add_filter('plugin_action_links', array( &$this, 'add_action_link'), 10, 2 );
			add_filter('plugin_row_meta', array( &$this, 'add_plugin_links'), 10, 2 );

			add_filter('install_plugin_complete_actions', array( &$this, 'add_install_plugin_complete_actions'), 10, 3 );

			//frontpage stuff (!is_admin())
		} else {

			add_action('wp_enqueue_scripts', array( &$this, 'style'));

		}


	}


	public function save_admin_notices() {

		global $mymail_notices;

		update_option( 'mymail_notices', $mymail_notices );

	}

	public function admin_notices() {

		global $mymail_notices;

		if($mymail_notices = get_option( 'mymail_notices' )){

			$updated = array();
			$errors = array();
			$msg;
			$dismiss = isset($_GET['mymail_remove_notice_all']) ? esc_attr($_GET['mymail_remove_notice_all']) : false;

			if(isset($_GET['mymail_remove_notice'])){

				unset($mymail_notices[$_GET['mymail_remove_notice']]);

				update_option( 'mymail_notices', $mymail_notices );

			}

			foreach($mymail_notices as $id => $notice){

				$msg = '<div id="mymail-notice-'.$id.'">';

				if(!$notice['once']){
					$msg .= '<a href="'.add_query_arg(array('mymail_remove_notice' => $id), esc_url($_SERVER['REQUEST_URI'])).'" class="rkt-cross mymail-dismiss alignright" title="'.__('dismiss message', 'mymail').'"><span class="mymail-icon icon-mm-delete"></span></a>';
				}else{
					unset($mymail_notices[$id]);
				}

				$msg .= '<p>'.($notice['text'] ? $notice['text'] : '&nbsp;').'</p>';
				$msg .= '</div>';

				if($notice['type'] == 'updated' && $dismiss != 'updated'){
					$updated[] = $msg;
				}elseif($notice['type'] == 'error' && $dismiss != 'error'){
					$errors[] = $msg;
				}

				if($dismiss == 'updated' && isset($mymail_notices[$id])){
					unset($mymail_notices[$id]);
				}
				if($dismiss == 'error' && isset($mymail_notices[$id])){
					unset($mymail_notices[$id]);
				}

			}

			wp_enqueue_script('mymail-notice', MYMAIL_URI . 'assets/js/notice-script.js', array('jquery'), MYMAIL_VERSION, true);
			if(!empty($errors)){
				echo '<div class="mymail-notices error">';
				if(count($errors) > 1) echo '<a class="mymail-dismiss-all" style="float:right;text-decoration:none;font-size:12px;" href="'.add_query_arg(array('mymail_remove_notice_all' => 'error'), esc_url($_SERVER['REQUEST_URI'])).'">dismiss all</a><br>';
				echo implode('', $errors);
				echo '</div>';
			}
			if(!empty($updated)){
				echo '<div class="mymail-notices updated">';
				if(count($updated) > 1) echo '<a class="mymail-dismiss-all" style="float:right;text-decoration:none;font-size:12px;" href="'.add_query_arg(array('mymail_remove_notice_all' => 'updated'), esc_url($_SERVER['REQUEST_URI'])).'">dismiss all</a><br>';
				echo implode('', $updated);
				echo '</div>';
			}

			add_action('shutdown', array( &$this, 'save_admin_notices') );

		}

	}


	public function get_base_link($campaign_id = '') {

		$is_permalink = mymail('helper')->using_permalinks();

		if(!function_exists('got_url_rewrite'))
			require_once( ABSPATH . 'wp-admin/includes/misc.php' );

		$prefix = (function_exists('got_url_rewrite') && !got_url_rewrite() ) ? '/index.php' : '';

		return $is_permalink
			? home_url($prefix.'/mymail/'.$campaign_id)
			: add_query_arg('mymail', $campaign_id, home_url($prefix));

	}


	public function get_unsubscribe_link($campaign_id = '') {

		$is_permalink = mymail('helper')->using_permalinks();
		if(!function_exists('got_url_rewrite'))
			require_once( ABSPATH . 'wp-admin/includes/misc.php' );

		$prefix = (function_exists('got_url_rewrite') && !got_url_rewrite() ) ? '/index.php' : '/';

		$unsubscribe_homepage = apply_filters('mymail_unsubscribe_link', (get_page( mymail_option('homepage') ))
			? get_permalink(mymail_option('homepage'))
			: get_bloginfo('url'));

		$slugs = mymail_option('slugs');
		$slug = isset($slugs['unsubscribe']) ? $slugs['unsubscribe'] : 'unsubscribe';

		if(!$is_permalink)
			$unsubscribe_homepage = str_replace(trailingslashit(get_bloginfo('url')), untrailingslashit(get_bloginfo('url')).$prefix, $unsubscribe_homepage);

		return $is_permalink
			? trailingslashit( $unsubscribe_homepage ).$slug
			: add_query_arg('unsubscribe', md5($campaign_id . '_unsubscribe'), $unsubscribe_homepage );

	}

	public function get_forward_link($campaign_id, $email = '') {

		$page = get_permalink($campaign_id);

		return add_query_arg(array('forward' => urlencode($email)), $page);

	}

	public function get_profile_link($campaign_id, $hash = '') {

		$is_permalink = mymail('helper')->using_permalinks();
		if(!function_exists('got_url_rewrite'))
			require_once( ABSPATH . 'wp-admin/includes/misc.php' );

		$prefix = (function_exists('got_url_rewrite') && !got_url_rewrite() ) ? '/index.php' : '';

		$homepage = get_page( mymail_option('homepage') )
			? get_permalink(mymail_option('homepage'))
			: get_bloginfo('url');

		$slugs = mymail_option('slugs');
		$slug = isset($slugs['profile']) ? $slugs['profile'] : 'profile';

		if(!$is_permalink)
			$homepage = str_replace(trailingslashit(get_bloginfo('url')), untrailingslashit(get_bloginfo('url')).$prefix, $homepage);

		return $is_permalink
			? trailingslashit( $homepage ).$slug
			: add_query_arg('profile', $hash, $homepage );


	}

	public function check_link_structure() {

		$args = array(
		);

		//only if permalink structure is used
		if(mymail('helper')->using_permalinks()){

			$hash = str_repeat('0', 32);

			$urls = array(
				trailingslashit($this->get_unsubscribe_link(0)) . $hash,
				trailingslashit($this->get_profile_link(0)) . $hash,
				trailingslashit($this->get_base_link(0)) . $hash,
			);

			foreach($urls as $url){

				$response = wp_remote_get($url, $args);

				$code = wp_remote_retrieve_response_code($response);
				if($code != 200){
					return false;
				}

			}

		}

		return true;

	}

	public function replace_links($content = '', $hash = '', $campaing_id = '') {

		//get all links from the basecontent
		preg_match_all('#href=(\'|")?(https?[^\'"]+)(\'|")?#', $content, $links);
		$links = $links[2];

		$used = array();

		$new_structure = mymail('helper')->using_permalinks();
		$base = $this->get_base_link($campaing_id);

		foreach ( $links as $link ) {

			$link = apply_filters('mymail_replace_link', $link, $base, $hash);

			if($new_structure){
				$replace = trailingslashit($base) . $hash . '/' . rtrim(strtr(base64_encode($link), '+/', '-_'), '=');

				!isset($used[$link])
					? $used[$link] = 1
					: $replace .= '/'.($used[$link]++);

			}else{
				$secure = strpos($link, 'https://') === 0;
				$dest = str_replace( array('http://', 'https://'), '', $link);
				$target = str_replace( array('%7B', '%7D') , array( '{', '}' ), urlencode( $dest ) );
				$replace = $base . '&k=' . $hash . '&t=' . $target .'&s=' . $secure;
				!isset($used[$link])
					? $used[$link] = 1
					: $replace .= '&c='.($used[$link]++);

			}

			$link = '"'.$link.'"';
			if (($pos = strpos($content, $link)) !== false)
				$content = substr_replace( $content, '"'.$replace.'"', $pos, strlen($link) );

		}

		return $content;

	}


	/*----------------------------------------------------------------------*/
	/* Filters
	/*----------------------------------------------------------------------*/

	public function sanitize_content($content, $userstyle = false, $bodyonly = false, $customhead = NULL) {
		if (empty($content))
			return '';

		if(function_exists('mb_convert_encoding')){
			$encoding = mb_detect_encoding($content, 'auto');
			if($encoding != 'UTF-8')
				$content = mb_convert_encoding($content, $encoding, 'UTF-8');
		}
		$content = stripslashes($content);

		preg_match('#<body[^>]*>(.*?)<\/body>#is', $content, $matches);
		if(!empty($matches)) $content = $matches[1];

		$content = str_replace('src="//', 'src="'.(is_ssl() ? 'https' : 'http').'://', $content);
		$content = str_replace('<module', "\n<module", $content);
		$content = preg_replace('#<div ?[^>]+?class=\"modulebuttons(.*)<\/div>#i', '', $content);
		$content = preg_replace('#<script[^>]*?>.*?</script>#si', '', $content);
		$content = str_replace(array('mymail-highlight','mymail-loading','ui-draggable', ' -handle'), '', $content);

		$allowed_tags = apply_filters('mymail_allowed_tags', array('address', 'a', 'big', 'blockquote', 'body', 'br', 'b', 'center', 'cite', 'code', 'dd', 'dfn', 'div', 'dl', 'dt', 'em', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'img', 'i', 'kbd', 'li', 'meta', 'ol', 'pre', 'p', 'span', 'small', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'thead', 'tfoot', 'td', 'th', 'title', 'tr', 'tt', 'ul', 'u', 'map', 'area', 'video', 'audio', 'buttons', 'single', 'multi', 'modules', 'module'));

		//save comments with conditional stuff
		preg_match_all('#<!--\s?\[\s?if.*-->#sU', $content, $comments);

		$commentid = uniqid();
		foreach($comments[0] as $i => $comment)
			$content = str_replace($comment, 'HTML_COMMENT_'.$i.'_'.$commentid, $content);

		$content = strip_tags($content, '<'.implode('><', $allowed_tags).'>');

		foreach($comments[0] as $i => $comment)
			$content = str_replace('HTML_COMMENT_'.$i.'_'.$commentid, $comment, $content);

		$content = str_replace(' !DOCTYPE', '!DOCTYPE', $content);
		$content = str_replace('< html PUBLIC', '<!DOCTYPE html PUBLIC', $content);
		$content = preg_replace('/(\r|\n|\r\n){2,}/', "\n", $content);

		if($bodyonly) return $content;

		$content = preg_replace(array('#<title>[^<]*?</title>#'), '', $content);

		$head = !empty($customhead) ? stripslashes($customhead) : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n".'<html xmlns="http://www.w3.org/1999/xhtml">'."\n".'<head>'."\n\t".'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n\t".'<meta name="viewport" content="width=device-width" />'."\n\t".'<title>{subject}</title>'."\n".'</head>';

		$content = $head."\n<body>\n".trim($content)."\n</body></html>";

		//custom styles
		global $mymail_mystyles;

		if($userstyle && !empty($mymail_mystyles)){
			//check for existing styles
			preg_match_all('#(<style ?[^<]+?>([^<]+)<\/style>)#', $content, $originalstyles);

			if(!empty($originalstyles[0])){
				foreach($mymail_mystyles as $style){
					$block = end($originalstyles[0]);
					$content = str_replace($block, $block.'<style type="text/css">'.$style.'</style>', $content);
				}
			}

		}

		return apply_filters('mymail_sanitize_content', $content);
	}


	public function plain_text($html, $linksonly = false) {

		//allow to hook into this method
		$result = apply_filters('mymail_plain_text', NULL, $html, $linksonly);
		if(!is_null($result)) return $result;

		if($linksonly){
			$links = '/< *a[^>]*href *= *"([^#]*)"[^>]*>(.*)< *\/ *a *>/Uis';
			$text = preg_replace($links,'${2} [${1}]',$html);
			$text = str_replace(array(" ","&nbsp;"),' ',strip_tags($text));
			$text = @html_entity_decode($text, ENT_QUOTES, 'UTF-8' );

			return trim($text);

		}else{
			require_once MYMAIL_DIR . 'classes/libs/class.html2text.php';
			$htmlconverter = new html2text($html, false, array('width' => 20000));

			return trim($htmlconverter->get_text());

		}

	}


	public function add_action_link( $links, $file ) {
		if ( $file == MYMAIL_SLUG ) {
			array_unshift( $links, '<a href="edit.php?post_type=newsletter&page=mymail_addons">'.__('Add Ons', 'mymail').'</a>' );
			array_unshift( $links, '<a href="options-general.php?page=newsletter-settings">' . __('Settings', 'mymail') . '</a>' );
		}
		return $links;
	}

	public function add_plugin_links($links, $file) {
		if ( $file == MYMAIL_SLUG ) {
			$links[] = '<a href="edit.php?post_type=newsletter&page=mymail_templates">'.__('Templates', 'mymail').'</a>';
		}
		return $links;
	}

	public function add_install_plugin_complete_actions($install_actions, $api, $plugin_file) {

		if(!isset($_GET['mymail-addon'])) return $install_actions;
		$install_actions['mymail_addons'] = '<a href="edit.php?post_type=newsletter&page=mymail_addons">'.__('Return to Add Ons Page', 'mymail').'</a>';

		if(isset($install_actions['plugins_page'])) unset($install_actions['plugins_page']);

		return $install_actions;
	}

	public function special_pages() {

		$page = add_submenu_page(NULL, 'Welcome', 'Welcome', 'read', 'mymail_welcome', array( &$this, 'welcome_page' ));
		$page = add_submenu_page('edit.php?post_type=newsletter', __( 'Add Ons', 'mymail' ), __( 'Add Ons', 'mymail' ), 'install_plugins', 'mymail_addons', array( &$this, 'addon_page' ));
		add_action( 'load-'.$page, array( &$this, 'addon_scripts_styles' ) );

	}

	public function welcome_page() {

		include MYMAIL_DIR . 'views/welcome.php';

	}

	public function addon_page() {

		include MYMAIL_DIR . 'views/addons.php';

	}


	/*----------------------------------------------------------------------*/
	/* Styles & Scripts
	/*----------------------------------------------------------------------*/


	public function admin_scripts_styles($hook) {

		wp_register_style('mymail-icons', MYMAIL_URI . 'assets/css/icons.css', array(), MYMAIL_VERSION);
		wp_enqueue_style('mymail-icons');
		wp_register_style('mymail-admin', MYMAIL_URI . 'assets/css/admin.css', array('mymail-icons'), MYMAIL_VERSION);
		wp_enqueue_style('mymail-admin');

	}
	public function addon_scripts_styles($hook) {

		wp_register_style('mymail-addons', MYMAIL_URI . 'assets/css/addons.css', array(), MYMAIL_VERSION);
		wp_enqueue_style('mymail-addons');

	}

	public function style() {
		if(mymail_option('form_css')){
			if(mymail_option('embed_form_css')){
				echo '<style type="text/css" media="screen">';
				echo $this->form_css(true);
				echo '</style>';
			}else{
				wp_register_style('mymail-form', admin_url('admin-ajax.php?action=mymail_form_css'), NULL, mymail_option('form_css_hash'));
				wp_enqueue_style('mymail-form');
			}
		}
	}


	public function form_css($return = false) {

		if( !$return ){
			header( 'Content-Type: text/css' );
			header( 'Expires: Thu, 31 Dec 2050 23:59:59 GMT' );
			header( 'Pragma: cache' );
		}

		if ( false === ( $css = get_transient( 'mymail_form_css' ) ) ) {

			$css = mymail_option('form_css');
			$css = strip_tags($css);
			$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
			$css = trim(str_replace(array("\r\n", "\r", "\n", "\t", '   ', '  '), '', $css));
			$css = str_replace(' {', '{', $css);
			$css = str_replace(' }', '}', $css);
			set_transient( 'mymail_form_css', $css );

		}

		if($return) return $css;

		echo $css;
		exit();
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

			$isNew = get_option('mymail') == false;

			if ($isNew){
				update_option('mymail_dbversion', MYMAIL_DBVERSION);
				$this->dbstructure();
				add_action('shutdown', array( &$this, 'send_welcome_mail'), 100 );
			}

			if(function_exists('get_filesystem_method') && 'direct' != get_filesystem_method()){
				mymail_notice('<strong>'.sprintf(__('MyMail is not able access the filesystem! If you have issues saving templates or other files please add this line to you wp-config.php: %s', 'mymail'), "<pre><code>define('FS_METHOD', 'direct');</code>").'</strong></pre>', 'error', false, 'filesystemaccess');
			}


		}

		if($blog_id){
			switch_to_blog($old_blog);
			return;
		}

		return $isNew;

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

			flush_rewrite_rules();
		}

		if($blog_id) switch_to_blog($old_blog);
	}

	public function dbstructure($output = false, $execute = true, $set_charset = true){

		global $wpdb;

		$charset_collate = '';

		if($set_charset){
			if ( method_exists($wpdb, 'has_cap') ) {
				if ( $wpdb->has_cap('collation') ) {
					if( ! empty($wpdb->charset ) ) $charset_collate .= "DEFAULT CHARACTER SET $wpdb->charset";
					if( ! empty($wpdb->collate ) ) $charset_collate .= " COLLATE $wpdb->collate";
				}
			} else {
				if ( $wpdb->supports_collation() ) {
					if( ! empty($wpdb->charset ) ) $charset_collate .= "DEFAULT CHARACTER SET $wpdb->charset";
					if( ! empty($wpdb->collate ) ) $charset_collate .= " COLLATE $wpdb->collate";
				}
			}
		}


		$tables = array(

			"CREATE TABLE {$wpdb->prefix}mymail_subscribers (
				ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				hash varchar(32) NOT NULL,
				email varchar(191) NOT NULL,
				wp_id bigint(20) unsigned NOT NULL DEFAULT 0,
				status int(11) unsigned NOT NULL DEFAULT 0,
				added int(11) unsigned NOT NULL DEFAULT 0,
				updated int(11) unsigned NOT NULL DEFAULT 0,
				signup int(11) unsigned NOT NULL DEFAULT 0,
				confirm int(11) unsigned NOT NULL DEFAULT 0,
				ip_signup varchar(45) NOT NULL DEFAULT '',
				ip_confirm varchar(45) NOT NULL DEFAULT '',
				PRIMARY KEY  (ID),
				UNIQUE KEY hash (hash),
				UNIQUE KEY email (email),
				KEY status (status),
				KEY wp_id (wp_id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_subscriber_fields (
				subscriber_id bigint(20) unsigned NOT NULL,
				meta_key varchar(191) NOT NULL,
				meta_value longtext NOT NULL,
				UNIQUE KEY id (subscriber_id,meta_key),
				KEY subscriber_id (subscriber_id),
				KEY meta_key (meta_key)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_subscriber_meta (
				subscriber_id bigint(20) unsigned NOT NULL,
				campaign_id bigint(20) unsigned NOT NULL,
				meta_key varchar(191) NOT NULL,
				meta_value longtext NOT NULL,
				UNIQUE KEY id (subscriber_id,campaign_id,meta_key),
				KEY subscriber_id (subscriber_id),
				KEY campaign_id (campaign_id),
				KEY meta_key (meta_key)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_queue (
				subscriber_id bigint(20) unsigned NOT NULL,
				campaign_id bigint(20) unsigned NOT NULL,
				requeued tinyint(1) unsigned NOT NULL,
				added int(11) unsigned NOT NULL,
				timestamp int(11) unsigned NOT NULL,
				sent int(11) unsigned NOT NULL,
				priority tinyint(1) unsigned NOT NULL,
				count tinyint(1) unsigned NOT NULL,
				error tinyint(1) unsigned NOT NULL,
				ignore_status tinyint(1) unsigned NOT NULL,
				options varchar(191) NOT NULL,
				UNIQUE KEY id (subscriber_id,campaign_id,requeued,options),
				KEY subscriber_id (subscriber_id),
				KEY campaign_id (campaign_id),
				KEY requeued (requeued),
				KEY timestamp (timestamp),
				KEY priority (priority),
				KEY count (count),
				KEY error (error),
				KEY ignore_status (ignore_status)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_actions (
				subscriber_id bigint(20) unsigned NOT NULL,
				campaign_id bigint(20) unsigned NOT NULL,
				timestamp int(11) unsigned NOT NULL,
				count int(11) unsigned NOT NULL,
				type tinyint(1) NOT NULL,
				link_id bigint(20) unsigned NOT NULL,
				UNIQUE KEY id (subscriber_id,campaign_id,type,link_id),
				KEY subscriber_id (subscriber_id),
				KEY campaign_id (campaign_id),
				KEY type (type)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_links (
				ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				link varchar(2083) NOT NULL,
				i tinyint(1) unsigned NOT NULL,
				PRIMARY KEY  (ID)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_lists (
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				parent_id bigint(20) unsigned NOT NULL,
				name varchar(191) NOT NULL,
				slug varchar(191) NOT NULL,
				description longtext NOT NULL,
				added int(11) unsigned NOT NULL,
				updated int(11) unsigned NOT NULL,
				PRIMARY KEY  (ID),
				UNIQUE KEY name (name),
				UNIQUE KEY slug (slug)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_lists_subscribers (
				list_id bigint(20) unsigned NOT NULL,
				subscriber_id bigint(20) unsigned NOT NULL,
				added int(11) unsigned NOT NULL,
				UNIQUE KEY id (list_id,subscriber_id),
				KEY list_id (list_id),
				KEY subscriber_id (subscriber_id)
			) $charset_collate;",


		);

		if(!function_exists('dbDelta'))
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  		$results = array();

		$wpdb->hide_errors();

  		foreach($tables as $tablequery){
			$results[] = dbDelta($tablequery, $execute);
		}

		if($output){
			foreach($results as $result)
				if($result) echo implode("\n",$result)."\n";
		}

		return true;

	}


	public function optimize_tables() {

		global $wpdb;

		$tables = array('subscribers','subscriber_fields','subscriber_meta','queue','actions','links','lists','lists_subscribers');

		return false !== $wpdb->query("OPTIMIZE TABLE {$wpdb->prefix}mymail_".implode(", {$wpdb->prefix}mymail_", $tables));
	}



	public function send_welcome_mail() {

		$current_user = wp_get_current_user();

		$n = mymail('notification');
		$n->to($current_user->user_email);
		$n->subject(__('Your MyMail Newsletter Plugin is ready!', 'mymail'));
		$n->replace(array(
			'headline' => '',
			'baseurl' => admin_url(),
			'notification' => 'This welcome mail was sent from your website <a href="'.home_url().'">'.get_bloginfo( 'name' ).'</a>. This also makes sure you can send emails with your current settings',
			'name' => $current_user->display_name,
			'preheader' => 'Thank you, '.$current_user->display_name.'! ',
		));
		$n->requeue(false);
		$n->template('welcome_mail');
		return $n->add();

	}



	public function get_custom_fields($keysonly = false) {

		$fields = mymail_option('custom_field', array());
		$fields = $keysonly ? array_keys($fields) : $fields;

		return array_splice($fields, 0, 58);

	}

	public function get_custom_date_fields($keysonly = false) {

		$fields = array();

		$all_fields = $this->get_custom_fields(false);
		foreach($all_fields as $key => $data){
			if($data['type'] == 'date')
				$fields[$key] = $data;
		}
		return $keysonly ? array_keys($fields) : $fields;

	}


	private function check_homepage(){

		$hp = get_permalink( mymail_option('homepage') );

		if(!$hp) mymail_notice(sprintf('<strong>'.__('You haven\'t defined a homepage for the newsletter. This is required to make the subscription form work correctly. Please check the %1$s or %2$s', 'mymail'), '<a href="options-general.php?page=newsletter-settings&mymail_remove_notice=mymail_no-homepage#frontend">'.__('frontend settings page', 'mymail').'</a>', '<a href="'.add_query_arg('mymail_create_homepage', 1, admin_url()).'">'.__('create it right now', 'mymail').'</a>').'</strong>', 'error', false, 'no-homepage');

	}

	public function wp_mail_setup($system_mail = NULL){

		if(is_null($system_mail)) $system_mail = mymail_option('system_mail');

		if($system_mail){

			if($system_mail == 'template'){

				add_filter( 'wp_mail', array( &$this, 'wp_mail_set' ) );
				add_filter( 'wp_mail_content_type', array( &$this, 'wp_mail_content_type' ) );

			}else{

				if($this->wp_mail)
					add_action('admin_notices', array( &$this, 'wp_mail_notice' ) );

			}

			add_filter('retrieve_password_message', array( &$this, 'wp_mail_password_reset_link_fix'), 10, 2);

		}
	}

	public function wp_mail_content_type( $content_type ){
		return 'text/html';
	}

	public function wp_mail_set( $args ){

		$template = mymail_option('default_template');
		$file = apply_filters( 'mymail_wp_mail_template_file', mymail_option('system_mail_template', 'notification.html') );

		if ($template) {
			$template = mymail('template', $template, $file);
			$content = $template->get(true, true);
		}else {
			$content = $headline.'<br>'.$content;
		}

		$replace = apply_filters( 'mymail_send_replace', array('notification' => '') );
		$message = apply_filters( 'mymail_send_message', $args['message'] );
		$subject = apply_filters( 'mymail_send_subject', $args['subject'] );
		$headline = apply_filters( 'mymail_send_headline', $args['subject'] );

		if(apply_filters('mymail_wp_mail_htmlify', true)){
			$message = str_replace(array('<br>', '<br />', '<br/>'), "\n", $message);
			$message = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $message);
			$message = wpautop($message, true);
		}

		$placeholder = mymail('placeholder', $content);

		$placeholder->add( array(
			'subject' => $subject,
			'preheader' => $headline,
			'headline' => $headline,
			'content' => $message,
		));

		$placeholder->add($replace);

		$message = $placeholder->get_content();

		$message = mymail('mail')->add_mymail_styles($message);
		$message = mymail('mail')->inline_style($message);

		$args['message'] = $message;

		$placeholder->set_content($subject);

		$args['subject'] = $placeholder->get_content();

		return $args;
	}

	public function wp_mail_notice(){
		echo '<div class="error"><p>function <strong>wp_mail</strong> already exists from a different plugin! Please disable it before using MyMails wp_mail alternative!</p></div>';
	}

	public function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {

		if(is_array($headers)) $headers = implode("\r\n", $headers)."\r\n";
		//only if content type is not html
		if(!preg_match('#content-type:(.*)text/html#i', $headers)){
			$message = str_replace(array('<br>', '<br />', '<br/>'), "\n", $message);
			$message = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $message);
			$message = wpautop($message, true);
		}

		$template = apply_filters( 'mymail_wp_mail_template_file', mymail_option('system_mail_template', 'notification.html') );

		return mymail_wp_mail( $to, $subject, $message, $headers, $attachments, $template );

	}

	public function wp_mail_password_reset_link_fix($message, $key){
		$str = network_site_url("wp-login.php?action=rp&key=$key");
		//remove '<' and '>' surrounding the link
		return preg_replace('#<'.preg_quote($str).'([^>]+)>#', $str.'\\1', $message);

	}


	public function meta($post_id, $part = NULL, $meta_key) {

		$meta = get_post_meta($post_id, $meta_key, true);

		if(is_null($part)) return $meta;

		if(isset($meta[$part])) return $meta[$part];

		return false;

	}


	public function update_meta($id, $key, $value = NULL, $meta_key) {
		if(is_array($key)){
			$meta = $key;
			return update_post_meta( $id, $meta_key, $meta );
		}
		$meta = $this->meta($id, NULL, $meta_key);
		$old = isset($meta[$key]) ? $meta[$key] : '';
		$meta[$key] = $value;
		return update_post_meta( $id, $meta_key, $meta, $old );
	}


	private function thirdpartystuff() {

		do_action('mymail_thirdpartystuff');

		if (function_exists('w3tc_objectcache_flush'))
			add_action('shutdown', 'w3tc_objectcache_flush');

		if (function_exists('wp_cache_clear_cache'))
			add_action('shutdown', 'wp_cache_clear_cache');

	}


}

?>
