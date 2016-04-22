<?php if (!defined('ABSPATH')) die('not allowed');

class mymail {

	private $template;
	private $post_data;
	private $campaign_data;
	private $mail = array();
	private $tables = array('actions','forms','forms_lists','form_fields','links','lists','lists_subscribers','queue','subscribers','subscriber_fields','subscriber_meta');

	public $wp_mail = NULL;

	private $_classes = array();

	static $form_active;

	public function __construct() {

		register_activation_hook(MYMAIL_FILE, array( &$this, 'activate'));
		register_deactivation_hook(MYMAIL_FILE, array( &$this, 'deactivate'));

		$classes = array('translations', 'campaigns', 'subscribers', 'lists', 'forms', 'manage', 'templates', 'widget', 'frontpage', 'statistics', 'ajax', 'cron', 'queue', 'actions', 'bounce', 'update', 'helpmenu', 'dashboard', 'settings', 'geo');

		add_action('plugins_loaded', array( &$this, 'init'), 1);

		add_action('widgets_init', create_function( '', 'register_widget( "MyMail_Signup_Widget" );register_widget( "MyMail_Newsletter_List_Widget" );register_widget( "MyMail_Newsletter_Subscribers_Count_Widget" );' ) );

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

		//remove revisions if newsletter is finished
		add_action('mymail_reset_mail', array( &$this, 'reset_mail_delayed'), 10, 3);

		add_action('mymail_cron', array( &$this, 'check_homepage'));


		$this->wp_mail_setup();

		if (is_admin()) {

			add_action('admin_enqueue_scripts', array( &$this, 'admin_scripts_styles'), 10, 1);
			add_action('admin_menu', array( &$this, 'special_pages'), 60);
			add_action('admin_notices', array( &$this, 'admin_notices') );

			add_filter('plugin_action_links', array( &$this, 'add_action_link'), 10, 2 );
			add_filter('plugin_row_meta', array( &$this, 'add_plugin_links'), 10, 2 );

			add_filter('install_plugin_complete_actions', array( &$this, 'add_install_plugin_complete_actions'), 10, 3 );

			add_filter('add_meta_boxes', array( &$this, 'add_homepage_info'), 10, 3 );

			add_filter('wp_import_post_data_processed', array( &$this, 'import_post_data'), 10, 2 );
			add_filter('display_post_states', array( &$this, 'display_post_states'), 10, 2 );

			//frontpage stuff (!is_admin())
		} else {


		}


	}


	public function save_admin_notices() {

		global $mymail_notices;

		update_option( 'mymail_notices', empty($mymail_notices) ? NULL : $mymail_notices );

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

				$msg = '<div data-id="'.$id.'" id="mymail-notice-'.$id.'" class="mymail-notice '.esc_attr($notice['type']).'">';

				$text = (isset($notice['text']) ? $notice['text'] : '');
				$text = isset($notice['cb']) && function_exists($notice['cb'])
					? call_user_func($notice['cb'], $text)
					: $text;

				if($text === false) continue;

				$msg .= '<p>'.($text ? $text : '&nbsp;').'</p>';
				if(!$notice['once']){
					$msg .= '<button type="button" class="notice-dismiss" title="'.__('Dismiss this notice (Alt-click to dismiss all notices)', 'mymail').'"><span class="screen-reader-text">'.__('Dismiss this notice (Alt-click to dismiss all notices)', 'mymail').'</span></button>';
				}else{
					unset($mymail_notices[$id]);
				}

				$msg .= '</div>';

				if($notice['type'] == 'updated' && $dismiss != 'updated')
					$updated[] = $msg;

				if($notice['type'] == 'error' && $dismiss != 'error')
					$errors[] = $msg;

				if($dismiss == 'updated' && isset($mymail_notices[$id]))
					unset($mymail_notices[$id]);

				if($dismiss == 'error' && isset($mymail_notices[$id]))
					unset($mymail_notices[$id]);

			}

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script('mymail-notice', MYMAIL_URI . 'assets/js/notice-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION, true);
			wp_enqueue_style('mymail-notice', MYMAIL_URI . 'assets/css/notice-style'.$suffix.'.css', array(), MYMAIL_VERSION);

			if(!empty($errors))
				echo implode('', $errors);

			if(!empty($updated))
				echo implode('', $updated);

			add_action('shutdown', array( &$this, 'save_admin_notices') );

		}

	}


	public function get_base_link($campaign_id = '') {

		$is_permalink = mymail('helper')->using_permalinks();

		$prefix = !mymail_option('got_url_rewrite') ? '/index.php' : '/';

		return $is_permalink
			? home_url($prefix.'/mymail/'.$campaign_id)
			: add_query_arg('mymail', $campaign_id, home_url($prefix));

	}


	public function get_unsubscribe_link($campaign_id = '') {

		$is_permalink = mymail('helper')->using_permalinks();

		$prefix = !mymail_option('got_url_rewrite') ? '/index.php' : '/';

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

		$prefix = !mymail_option('got_url_rewrite') ? '/index.php' : '/';

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

		$args = array();

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

				$link = str_replace( array('%7B', '%7D') , array( '{', '}' ), $link );
				$target = rtrim(strtr(base64_encode($link), '+/', '-_'), '=');
				$replace = $base . '&k=' . $hash . '&t=' . $target;
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

	public function get_last_post( $offset = 0, $post_type = 'post', $term_ids = array(), $simple = false ) {

		global $wpdb;

		$cache_key = 'get_last_post';
		$key = md5(serialize(array($offset, $post_type, $term_ids, $simple)));

		$posts = mymail_cache_get( $cache_key );

		if(!$posts) $posts = array();

		if (isset($posts[$key]) ) return $posts[$key];

		$args = array(
			'posts_per_page' => 1,
			'numberposts' => 1,
			'post_type' => $post_type,
			'offset' => $offset,
			'update_post_meta_cache' => false,
			'no_found_rows' => true,
			'cache_results' => false,
		);

		$exclude = $wpdb->get_col("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'mymail_ignore' AND meta_value != '0'");
		if(!empty($exclude ))
			$args['post__not_in'] = $exclude;

		if(!empty($term_ids)){

			$tax_query = array();

			$taxonomies = get_object_taxonomies( $post_type, 'names' );

			for($i = 0; $i < count($term_ids); $i++){
				if(empty($term_ids[$i])) continue;
				$tax_query[] = array(
					'taxonomy' => $taxonomies[$i],
					'field' => 'id',
					'terms' => explode(',', $term_ids[$i]),
				);
			}

			if(!empty($tax_query)){
				$tax_query['relation'] = 'AND';
				$args = wp_parse_args( $args, array('tax_query' => $tax_query));
			}

		}else{
			$args['update_post_term_cache'] = false;
		}

		$post = get_posts( $args );

		if($post){
			$post = $post[0];

			if(!$simple){

				if(!$post->post_excerpt){
					if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
						$content = explode($matches[0], $post->post_content, 2);
						$post->post_excerpt = trim($content[0]);
					}
				}

				$post->post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );

				$post->post_content = apply_filters( 'the_content', $post->post_content );
			}

		}else{
			$post = false;
		}

		$posts[$key] = $post;

		mymail_cache_set( $cache_key, $posts );

		return $post;
	}


	/*----------------------------------------------------------------------*/
	/* Filters
	/*----------------------------------------------------------------------*/

	public function sanitize_content($content, $userstyle = false, $customhead = NULL) {
		if (empty($content))
			return '';

		if(function_exists('mb_convert_encoding')){
			$encoding = mb_detect_encoding($content, 'auto');
			if($encoding != 'UTF-8')
				$content = mb_convert_encoding($content, $encoding, 'UTF-8');
		}

		$content = stripslashes($content);
		$bodyattributes = '';
		$pre_stuff = '';
		$protocol = (is_ssl() ? 'https' : 'http');

		preg_match('#^(.*)?<head([^>]*)>(.*?)<\/head>#is', is_null($customhead) ? $content : stripslashes($customhead), $matches);
		if(!empty($matches)){
			$pre_stuff = $matches[1];
			$head = '<head'.$matches[2].'>'.$matches[3].'</head>';
		}else{
			$pre_stuff = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n".'<html xmlns="http://www.w3.org/1999/xhtml">'."\n";
			$head = '<head>'."\n\t".'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n\t".'<meta name="viewport" content="width=device-width" />'."\n\t".'<title>{subject}</title>'."\n".'</head>';
		}

		preg_match('#<body([^>]*)>(.*?)<\/body>#is', $content, $matches);
		if(!empty($matches)){
			$bodyattributes = $matches[1];
			$bodyattributes = ' '.trim(str_replace(array('position: relative;', 'mymail-loading', ' class=""', ' style=""'), '', $bodyattributes));
			$body = $matches[2];
		}else{
			$body = $content;
		}

		//custom styles
		global $mymail_mystyles;

		if($userstyle && !empty($mymail_mystyles)){
			//check for existing styles
			preg_match_all('#(<style ?[^<]+?>([^<]+)<\/style>)#', $body, $originalstyles);

			if(!empty($originalstyles[0])){
				foreach($mymail_mystyles as $style){
					$block = end($originalstyles[0]);
					$body = str_replace($block, $block.'<style type="text/css">'.$style.'</style>', $body);
				}
			}

		}

		$content = $head."\n<body$bodyattributes>\n".trim($body)."\n</body></html>";

		$content = str_replace('<body >', '<body>', $content);
		$content = str_replace(' src="//', ' src="'.$protocol.'://', $content);
		$content = str_replace(' href="//', ' href="'.$protocol.'://', $content);
		$content = preg_replace('#<div ?[^>]+?class=\"modulebuttons(.*)<\/div>#i', '', $content);
		$content = preg_replace('#<script[^>]*?>.*?</script>#si', '', $content);
		$content = str_replace(array('mymail-highlight','mymail-loading','ui-draggable', ' -handle'), '', $content);

		$allowed_tags = apply_filters('mymail_allowed_tags', array('address', 'a', 'big', 'blockquote', 'body', 'br', 'b', 'center', 'cite', 'code', 'dd', 'dfn', 'div', 'dl', 'dt', 'em', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'img', 'i', 'kbd', 'li', 'meta', 'ol', 'pre', 'p', 'span', 'small', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'thead', 'tfoot', 'td', 'th', 'title', 'tr', 'tt', 'ul', 'u', 'map', 'area', 'video', 'audio', 'buttons', 'single', 'multi', 'modules', 'module', 'if', 'elseif', 'else'));

		$allowed_tags = '<'.implode('><', $allowed_tags).'>';

		//save comments with conditional stuff
		preg_match_all('#<!--\s?\[\s?if(.*)?>(.*)?<!\[endif\]-->#sU', $content, $comments);

		$commentid = uniqid();
		foreach($comments[0] as $i => $comment)
			$content = str_replace($comment, 'HTML_COMMENT_'.$i.'_'.$commentid, $content);

		$content = strip_tags($content, $allowed_tags);

		foreach($comments[0] as $i => $comment)
			$content = str_replace('HTML_COMMENT_'.$i.'_'.$commentid, $comment, $content);

		$content = $pre_stuff.$content;

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
			$links[] = '<a href="edit.php?post_type=newsletter&page=mymail_templates&more">'.__('Templates', 'mymail').'</a>';
			$links[] = '<a class="thickbox" href="'.network_admin_url('plugin-install.php?tab=plugin-information&plugin=mymail&TB_iframe=true&width=600&height=550').'">'.__('View details', 'mymail').'</a>';
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
		add_action( 'load-'.$page, array( &$this, 'welcome_scripts_styles' ) );

		$page = add_submenu_page('edit.php?post_type=newsletter', __( 'Add Ons', 'mymail' ), __( 'Add Ons', 'mymail' ), 'install_plugins', 'mymail_addons', array( &$this, 'addon_page' ));
		add_action( 'load-'.$page, array( &$this, 'addon_scripts_styles' ) );

	}

	public function welcome_page() {

		mymail_update_option('welcome', false);
		include MYMAIL_DIR . 'views/welcome.php';

	}

	public function addon_page() {

		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');

		include MYMAIL_DIR . 'views/addons.php';

	}


	/*----------------------------------------------------------------------*/
	/* Styles & Scripts
	/*----------------------------------------------------------------------*/


	public function admin_scripts_styles($hook) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style('mymail-icons', MYMAIL_URI . 'assets/css/icons'.$suffix.'.css', array(), MYMAIL_VERSION);
		wp_enqueue_style('mymail-admin', MYMAIL_URI . 'assets/css/admin'.$suffix.'.css', array('mymail-icons'), MYMAIL_VERSION);

	}
	public function welcome_scripts_styles($hook) {

		//no notices here
		remove_action('admin_notices', array( &$this, 'admin_notices') );

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style('mymail-welcome', MYMAIL_URI . 'assets/css/welcome-style'.$suffix.'.css', array(), MYMAIL_VERSION);

	}
	public function addon_scripts_styles($hook) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style('mymail-addons', MYMAIL_URI . 'assets/css/addons-style'.$suffix.'.css', array(), MYMAIL_VERSION);

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

			$this->on_activate($isNew);

			foreach ($this->_classes as $classname => $class) {
				if(method_exists($class, 'on_activate'))
					$class->on_activate($isNew);
			}


		}

		if($blog_id){
			switch_to_blog($old_blog);
			return;
		}

	}


	public function deactivate() {

		global $wpdb;

		if (is_network_admin() && is_multisite()) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		}else{

			$blogids = array(false);

		}

		foreach ($blogids as $blog_id) {

			if($blog_id) switch_to_blog( $blog_id );

			foreach ($this->_classes as $classname => $class) {
				if(method_exists($class, 'on_deactivate'))
					$class->on_deactivate();
			}

			$this->on_deactivate();

		}

		if($blog_id){
			switch_to_blog($old_blog);
			return;
		}

	}

	public function on_activate($new) {

		global $wpdb;

		$this->dbstructure();

		if ($new){
			if(!is_network_admin())
				add_action('activated_plugin', array( &$this, 'redirect_to_welcome_page') );
			update_option('mymail', true);
			update_option('mymail_dbversion', MYMAIL_DBVERSION);
		}else{
			//set autoload to 'yes' on plugin deactivation
			$wpdb->query("UPDATE {$wpdb->options} SET `autoload` = 'yes' WHERE option_name LIKE 'mymail%'");
		}

		if(function_exists('get_filesystem_method') && 'direct' != get_filesystem_method()){
			mymail_notice('<strong>'.sprintf(__('MyMail is not able access the filesystem! If you have issues saving templates or other files please add this line to you wp-config.php: %s', 'mymail'), "<pre><code>define('FS_METHOD', 'direct');</code>").'</strong></pre>', 'error', false, 'filesystemaccess');
		}

	}

	public function on_deactivate() {

		global $wpdb;

		flush_rewrite_rules();

		//set autoload to 'no' on plugin deactivation
		$wpdb->query("UPDATE {$wpdb->options} SET `autoload` = 'no' WHERE option_name LIKE 'mymail%'");

	}

	public function get_tables($fullnames = false){

		global $wpdb;

		if(!$fullnames) return $this->tables;

		$tables = array();
		foreach ($this->tables as $table) {
			$tables[] = "{$wpdb->prefix}mymail_$table";
		}

		return $tables;

	}

	public function dbstructure($output = false, $execute = true, $set_charset = true, $hide_errors = true){

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
				rating decimal(3,2) unsigned NOT NULL DEFAULT 0.25,
				PRIMARY KEY  (ID),
				UNIQUE KEY hash (hash),
				UNIQUE KEY email (email),
				KEY wp_id (wp_id),
				KEY status (status),
				KEY rating (rating)
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
				subscriber_id bigint(20) unsigned NOT NULL DEFAULT 0,
				campaign_id bigint(20) unsigned NOT NULL DEFAULT 0,
				requeued tinyint(1) unsigned NOT NULL DEFAULT 0,
				added int(11) unsigned NOT NULL DEFAULT 0,
				timestamp int(11) unsigned NOT NULL DEFAULT 0,
				sent int(11) unsigned NOT NULL DEFAULT 0,
				priority tinyint(1) unsigned NOT NULL DEFAULT 0,
				count tinyint(1) unsigned NOT NULL DEFAULT 0,
				error tinyint(1) unsigned NOT NULL DEFAULT 0,
				ignore_status tinyint(1) unsigned NOT NULL DEFAULT 0,
				options varchar(191) NOT NULL DEFAULT '',
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
				subscriber_id bigint(20) unsigned NOT NULL DEFAULT 0,
				campaign_id bigint(20) unsigned NOT NULL DEFAULT 0,
				timestamp int(11) unsigned NOT NULL DEFAULT 0,
				count int(11) unsigned NOT NULL DEFAULT 0,
				type tinyint(1) NOT NULL DEFAULT 0,
				link_id bigint(20) unsigned NOT NULL DEFAULT 0,
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

			"CREATE TABLE {$wpdb->prefix}mymail_forms (
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				name varchar(191) NOT NULL DEFAULT '',
				submit varchar(191) NOT NULL DEFAULT '',
				asterisk tinyint(1) DEFAULT 1,
				userschoice tinyint(1) DEFAULT 0,
				precheck tinyint(1) DEFAULT 0,
				dropdown tinyint(1) DEFAULT 0,
				prefill tinyint(1) DEFAULT 0,
				inline tinyint(1) DEFAULT 0,
				overwrite tinyint(1) DEFAULT 0,
				addlists tinyint(1) DEFAULT 0,
				style longtext,
				custom_style longtext,
				doubleoptin tinyint(1) DEFAULT 1,
				subject longtext,
				headline longtext,
				content longtext,
				link longtext,
				resend tinyint(1) DEFAULT 0,
				resend_count int(11) DEFAULT 2,
				resend_time int(11) DEFAULT 48,
				template varchar(191) NOT NULL DEFAULT '',
				vcard tinyint(1) DEFAULT 0,
				vcard_content longtext,
				confirmredirect varchar(2083) DEFAULT NULL,
				redirect varchar(2083) DEFAULT NULL,
				added int(11) unsigned DEFAULT NULL,
				updated int(11) unsigned DEFAULT NULL,
				PRIMARY KEY  (ID)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_form_fields (
				form_id bigint(20) unsigned NOT NULL,
				field_id varchar(191) NOT NULL,
				name varchar(191) NOT NULL,
				error_msg varchar(191) NOT NULL,
				required tinyint(1) unsigned NOT NULL,
				position int(11) unsigned NOT NULL,
				UNIQUE KEY id (form_id,field_id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}mymail_forms_lists (
				form_id bigint(20) unsigned NOT NULL,
				list_id bigint(20) unsigned NOT NULL,
				added int(11) unsigned NOT NULL,
				UNIQUE KEY id (form_id,list_id),
				KEY form_id (form_id),
				KEY list_id (list_id)
			) $charset_collate;",

		);

		if(!function_exists('dbDelta'))
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  		$results = array();

		if($hide_errors) $wpdb->hide_errors();

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

		return false !== $wpdb->query("OPTIMIZE TABLE {$wpdb->prefix}mymail_".implode(", {$wpdb->prefix}mymail_", $this->get_tables()));
	}


	public function redirect_to_welcome_page( $plugin ) {

		//only on single plugin activation
		if($plugin != MYMAIL_SLUG || !isset($_GET['plugin'])) return;

		$this->send_welcome_mail();
		wp_redirect( admin_url('edit.php?post_type=newsletter&page=mymail_welcome'), 302 );
		exit;

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


	public function check_homepage(){

		$hp = get_post( mymail_option('homepage') );

		mymail_remove_notice('no_homepage');
		mymail_remove_notice('wrong_homepage_status');

		if(!$hp || $hp->post_status == 'trash') {

			mymail_notice(sprintf('<strong>'.__('You haven\'t defined a homepage for the newsletter. This is required to make the subscription form work correctly. Please check the %1$s or %2$s', 'mymail'), '<a href="options-general.php?page=newsletter-settings&mymail_remove_notice=mymail_no_homepage#frontend">'.__('frontend settings page', 'mymail').'</a>', '<a href="'.add_query_arg('mymail_create_homepage', 1, admin_url()).'">'.__('create it right now', 'mymail').'</a>').'</strong>', 'error', false, 'no_homepage');

		}else if($hp->post_status != 'publish'){

			mymail_notice(sprintf('<strong>'.__('Your newsletter homepage is not visible. Please %s the page', 'mymail'), '<a href="post.php?post='.$hp->ID.'&action=edit&mymail_remove_notice=mymail_wrong_homepage_status">'.__('update', 'mymail').'</a>').'</strong>', 'error', false, 'wrong_homepage_status');

		}

	}

	public function add_homepage_info( $post_type, $post ) {

		if($post_type != 'page') return;

		if(mymail_option('homepage') == $post->ID){

			if(!preg_match('#\[newsletter_signup\]#', $post->post_content)
				|| !preg_match('#\[newsletter_signup_form#', $post->post_content)
				|| !preg_match('#\[newsletter_confirm\]#', $post->post_content)
				|| !preg_match('#\[newsletter_unsubscribe\]#', $post->post_content)){

				mymail_notice('<strong>'.sprintf(__('This is your newsletter homepage but it seems it is not set up correctly! Please follow %s for help!','mymail'), '<a href="https://help.revaxarts.com/how-can-i-setup-the-newsletter-homepage/">'.__('this guid', 'mymail').'</a>').'</strong>', 'error', true, 'homepage_info');

			}else{

			}

		}


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

		//map links with anchor tags
		if(preg_match_all('/(<)(https?:\/\/\S*)(>)/', $message, $links)){
			foreach ($links[0] as $i => $link) {
				$message = preg_replace('/'.preg_quote($links[0][$i], '/').'/', '<a href="'.$links[2][$i].'">'.$links[2][$i].'</a>', $message, 1);
			}
		}
		if(preg_match_all('/(\s)(https?:\/\/\S*)(\s)?/', $message, $links)){
			foreach ($links[2] as $i => $link) {
				$message = preg_replace('/'.preg_quote($links[1][$i].$links[2][$i], '/').'/', $links[1][$i].'<a href="'.$links[2][$i].'">'.$links[2][$i].'</a>'.$links[3][$i], $message, 1);
			}
		}

		//only if content type is not html
		if(!preg_match('#content-type:(.*)text/html#i', $headers)){
			$message = str_replace(array('<br>', '<br />', '<br/>'), "\n", $message);
			$message = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $message);
			$message = wpautop($message, true);
		}

		$template = apply_filters( 'mymail_wp_mail_template_file', mymail_option('system_mail_template', 'notification.html') );

		return mymail_wp_mail( $to, $subject, $message, $headers, $attachments, $template );

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


	public function display_post_states($post_states, $post) {

		if($post->ID == mymail_option('homepage'))
			$post_states['mymail_is_homepage'] = __('Newsletter Homepage', 'mymail');

		return $post_states;

	}


	public function import_post_data($postdata, $post) {

		if(!isset($postdata['post_type']) || $postdata['post_type'] != 'newsletter') return $postdata;

		kses_remove_filters();

		preg_match_all('/(src|background|href)=["\'](.*)["\']/Ui', $postdata['post_content'], $links);
		$links = $links[2];

		$old_home_url = '';
		foreach ($links as $link) {
			if(preg_match('/(.*)wp-content(.*)\/myMail/U', $link, $match)){
				$new_link = str_replace($match[0], MYMAIL_UPLOAD_URI, $link);
				$old_home_url = $match[1];
				$postdata['post_content'] = str_replace($link, $new_link, $postdata['post_content']);
			}
		}

		if($old_home_url)
			$postdata['post_content'] = str_replace($old_home_url, trailingslashit(home_url()), $postdata['post_content']);

		mymail_notice('<strong>'.__('Please make sure all your campaigns are imported correctly!', 'mymail').'</strong>', 'error', false, 'import_campaings');

		return $postdata;

	}


	private function thirdpartystuff() {

		do_action('mymail_thirdpartystuff');

		if (function_exists('w3tc_objectcache_flush'))
			add_action('shutdown', 'w3tc_objectcache_flush');

		if (function_exists('wp_cache_clear_cache'))
			add_action('shutdown', 'wp_cache_clear_cache');

	}


}
