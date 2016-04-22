<?php if(!defined('ABSPATH')) die('not allowed');

//Version 1.1
//UpdateCenterPlugin Class
if ( !class_exists( "UpdateCenterPlugin" ) ) :

class UpdateCenterPlugin {

	private static $plugins = null;
	private static $plugin_data = array();
	public static $caller = null;

	private static $_instance = null;
	private static $optionname = 'updatecenter_plugins';

	public static function add($args = array()){

		if(isset($args['slug'])) $args['plugin'] = $args['slug'];

		if(!isset($args['plugin'])){
			$caller = array_shift(debug_backtrace());
			$error = sprintf('[UpdateCenter] You have to define a "plugin" parameter for your plugin in %s on line %d', $caller['file'], $caller['line'] );

			return (is_admin())
				? wp_die( $error )
				: false;

		}

		if (!isset(self::$_instance))
			self::$_instance = new self($args['plugin']);

		$plugin_data = (object) wp_parse_args($args, array(
			'remote_url' => NULL,
			'slug' => NULL
		));

		$plugin_data->slug = dirname($plugin_data->plugin);

		$plugin_data->remote_url = trailingslashit($plugin_data->remote_url);

		self::$plugin_data[$plugin_data->slug] = $plugin_data;

		register_activation_hook($plugin_data->plugin, array( 'UpdateCenterPlugin', 'clear_options' ));
		register_deactivation_hook($plugin_data->plugin, array( 'UpdateCenterPlugin', 'clear_options' ));

		return self::$_instance;
	}

	private function __construct($caller) {

		self::$caller = $caller;

		self::$plugins = self::get_options();

		add_action( 'admin_init', array( &$this, 'init' ), 100 );
		add_filter( 'site_transient_update_plugins', array( &$this, 'update_plugins_filter' ), 1 );

		add_action( 'wp_update_plugins', array( &$this, 'check_periodic_updates' ), 99 );
		add_action( 'updatecenterplugin_check', array( &$this, 'check_periodic_updates' ) );
		add_filter( 'upgrader_post_install', array( &$this, 'check_periodic_updates' ), 99 );

		add_filter( 'auto_update_plugin', array( &$this, 'auto_update' ), 10, 2 );

		add_filter( 'http_request_args', array( &$this, 'http_request_args' ), 100, 2 );

	}

	public function init() {

		global $pagenow;

		if(!is_admin()) return;
		if(!current_user_can('update_plugins')) return;

		if($pagenow == 'update-core.php'){

			//force check on the updates page
			do_action( 'updatecenterplugin_check' );
			add_filter( 'plugins_api',  array( &$this, 'plugins_api' ), 10, 3);


		}else if($pagenow == 'plugin-install.php'){

			if(isset($_GET['plugin']) && in_array($_GET['plugin'], array_keys(self::$plugin_data))){
				add_filter( 'plugins_api',  array( &$this, 'plugins_api' ), 10, 3);
				add_filter( 'plugins_api_result',  array( &$this, 'plugins_api_result' ), 10, 3);
			}

		}

		if( is_multisite() && !is_network_admin() ) {

			if ( !function_exists( 'is_plugin_active_for_network' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

			foreach(self::$plugins as $slug => $plugin){
				if( !is_plugin_active_for_network( plugin_basename( $plugin->plugin ) ) && time()-$plugin->last_update > 3600 ){
					do_action( 'updatecenterplugin_check' );
					break;
				}
			}

		}

		add_filter( 'admin_notices', array( &$this, 'admin_notices' ), 99 );

		if(empty(self::$plugins) ) do_action( 'updatecenterplugin_check' );

	}

	public function admin_notices() {

		global $pagenow;

		if(!current_user_can('update_plugins') || $pagenow == 'update.php') return;

		foreach(self::$plugins as $slug => $data){

			$notices = array();

			if(isset($data->admin_notices)){
				foreach ($data->admin_notices as $version => $notice) {
					if(version_compare($version, $data->version, '<=')) continue;
					$notices[] = stripslashes($notice);

				}
			}else if(!empty($data->admin_notice) && version_compare($data->version, $data->new_version, '<=')){
				$notices[] = stripslashes($data->admin_notice);
			}

			if(empty($notices)) continue;

			$nonce = wp_create_nonce('upgrade-plugin_' . $data->plugin);
			foreach ($notices as $notice) {
				echo '<div class="update-nag">'.str_replace(
						array('%%updateurl%%'),
						array(admin_url('update.php?action=upgrade-plugin&plugin='.urlencode( $data->plugin ).'&_wpnonce='.$nonce)),
						$notice.'</div>');
			}
		}

	}

	public function auto_update($update, $item) {

		//explicit
		if($update) return true;

		if(!isset(self::$plugin_data[$item->slug])) return false;

		//return default if not set
		if(!isset(self::$plugin_data[$item->slug]->autoupdate)) return $update;

		//if only "minor" updates
		if(self::$plugin_data[$item->slug]->autoupdate === 'minor'){
			return $this->version_compare(self::$plugins[$item->slug]->new_version, self::$plugins[$item->slug]->version, true);
		}

		return !!(self::$plugin_data[$item->slug]->autoupdate);

	}

	public function version_compare($new_version, $old_version, $only_minor = false){

		if($only_minor){

			$new = explode(".", rtrim($new_version, ".0"));
			$old = explode(".", rtrim($old_version, ".0"));

			$is_major_update = version_compare($new[1], $old[1], '>') || version_compare(intval($new_version), intval($old_version), '>');

			$is_minor_update = (!$is_major_update && version_compare(strstr($new_version, '.'), strstr($old_version, '.'), '>'));

			return $is_minor_update;
		}

		return version_compare($new_version, $old_version, '>');

	}

	public function http_request_args($r, $url) {

		if(false !== strpos($url, 'api.wordpress.org/plugins/update-check/1.1')){

			if(!isset($r['body']['plugins'])) return $r;

			//remove updatecenter plugins from the wordpress check
			$original = json_decode($r['body']['plugins'], true);

			$plugins = $original['plugins'];
			$uc_plugins = array_keys(self::$plugin_data);

			$r['body']['plugins'] = json_encode(array(
				'plugins' => array_intersect_key($plugins, array_flip(array_keys(array_diff_key($plugins, array_flip($uc_plugins))))),
				'active' => array_merge(array_diff(array_merge(array_diff($original['active'], $uc_plugins)), $uc_plugins))
			));

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


	public function plugins_api($res, $action, $args) {

		global $pagenow;

		$slug = $args->slug;

		if(!isset(self::$plugins[$slug])) return $res;
		if(!isset(self::$plugin_data[$slug])) return $res;

		if($pagenow != 'update-core.php'){

			$version_info = $this->check_info( $slug );

			if(!$version_info) wp_die('There was an error while getting the information about the plugin. Please try again later');

			$res = (object) $version_info;
			$res->slug = $slug;
			if(isset($res->contributors))$res->contributors = (array) $res->contributors;
			$res->sections = isset($res->sections) ? (array) $res->sections : array();

		}else{

			$res = self::$plugins[$slug];

		}

		return $res;


	}

	public function plugins_api_result($res, $action, $args) {

		if(!isset($this->slug)) return $res;

		if($args->slug == $this->slug)
			$res->external = true;

		return $res;

	}

	public function check_periodic_updates( ) {

		if(did_action('updatecenterplugin_check') > 1) return;

		//get the actual version
		foreach(self::$plugin_data as $slug => $plugin){
			if(!isset(self::$plugins[ $plugin->slug ]))
				self::$plugins[ $plugin->slug ] = (object) array(
					'slug' => $slug,
					'plugin' => $plugin->plugin,
					'version' => NULL,
					'package' => NULL,
					'new_version' => NULL,
					'last_update' => 0,
				);
			if(is_readable(WP_PLUGIN_DIR .'/'. $plugin->plugin)){
				$plugin_data = get_plugin_data( WP_PLUGIN_DIR .'/'. $plugin->plugin );
				self::$plugins[ $plugin->slug ]->version = $plugin_data['Version'];
			}

		}

		$collection = $this->get_collection();

		foreach($collection as $remote_url => $plugins){

			if(empty($plugins)) continue;

			$result = $this->check_collection( $remote_url, $plugins );

			if( is_wp_error( $result ) || empty($result) || !is_array($result)) continue;

			foreach($result as $slug => $updatecenterinfo){

				if ( !is_object($updatecenterinfo) ){

					self::$plugins[ $slug ]->last_update = time();
					self::$plugins[ $slug ]->new_version = NULL;

				//$version_info should be an array with keys ['version'] and ['download_link']
				} else if ( isset( $updatecenterinfo->version ) && isset( $updatecenterinfo->download_link ) ) {

					self::$plugins[ $slug ]->new_version = $updatecenterinfo->version;
					self::$plugins[ $slug ]->package = $updatecenterinfo->download_link;
					self::$plugins[ $slug ]->last_update = time();

					if( isset( $updatecenterinfo->requires ) ) self::$plugins[ $slug ]->requires = $updatecenterinfo->requires;
					if( isset( $updatecenterinfo->tested ) ) self::$plugins[ $slug ]->tested = $updatecenterinfo->tested;
					if( isset( $updatecenterinfo->upgrade_notice ) ) self::$plugins[ $slug ]->upgrade_notice = stripslashes($updatecenterinfo->upgrade_notice);
					if( isset( $updatecenterinfo->admin_notice ) ) self::$plugins[ $slug ]->admin_notice = stripslashes($updatecenterinfo->admin_notice);
					if( isset( $updatecenterinfo->admin_notices ) ) self::$plugins[ $slug ]->admin_notices = $updatecenterinfo->admin_notices;

				}


			}

		}

		$this->save_options();

	}

	public function get_collection() {

		switch(current_filter()){
			case 'updatecenterplugin_check';
				$timeout = 60;
				break;
			case 'upgrader_post_install';
				$timeout = 0;
				break;
			default:
				$timeout = 43200;
		}

		$collection = array();

		foreach(self::$plugin_data as $slug => $plugin){

			if(time()-self::$plugins[$slug]->last_update >= $timeout ){
				$collection[$plugin->remote_url] = isset($collection[$plugin->remote_url]) ? $collection[$plugin->remote_url] : array();
				$collection[$plugin->remote_url][$slug] = $this->header_infos( $slug );
			}

		}

		return $collection;
	}

	public static function clear_options() {

		self::$plugins = array();
		update_option( self::$optionname, self::$plugins );

	}

	private static function get_options() {

		return get_option( self::$optionname, array() );

	}

	public function save_options() {

		update_option( self::$optionname, self::$plugins );

	}

	public function check_collection($remote_url, $plugins) {

		$body = http_build_query( array('updatecenter_data' => array_values($plugins)), null, '&' );
		$post = array(
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Content-Length' => strlen( $body ),
				'X-ip' => isset($_SERVER['SERVER_ADDR'])
						? $_SERVER['SERVER_ADDR'] : (function_exists('getenv') ? getenv('SERVER_ADDR') : NULL),
				),
			'body' => $body,
		);

		$response = wp_remote_post( add_query_arg(array(
			'updatecenter_action' => 'versions',
			'updatecenter_slug' => array_keys($plugins),
		), $remote_url), $post );

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = trim( wp_remote_retrieve_body( $response ) );

		if ( $response_code != 200 || is_wp_error( $response_body ) ) {
			return $response_body;
		}

		$result = json_decode( $response_body );

		if(empty($result)) return array_flip(array_keys($plugins));

		return is_array($result) ? array_combine( array_keys($plugins), $result ) : array();

	}

	public function check_info( $slug ) {

		$body = http_build_query( $this->header_infos( $slug ), null, '&' );
		$post = array(
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Content-Length' => strlen( $body ),
				'X-ip' => isset($_SERVER['SERVER_ADDR'])
						? $_SERVER['SERVER_ADDR'] : (function_exists('getenv') ? getenv('SERVER_ADDR') : NULL),
				),
			'body' => $body,
		);

		$response = wp_remote_post( add_query_arg(array(
			'updatecenter_action' => 'info',
			'updatecenter_slug' => $slug,
		), self::$plugin_data[ $slug ]->remote_url), $post );

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = trim( wp_remote_retrieve_body( $response ) );

		if ( $response_code != 200 || is_wp_error( $response_body ) ) {
			return $response_body;
		}

		$result = json_decode( $response_body, true );

		return $result;

	}

	public function update_plugins_filter( $value ) {

		global $pagenow;

		if(empty(self::$plugins)) return $value;

		foreach(self::$plugins as $slug => $plugin){

			if( empty($plugin->package) || version_compare( $plugin->version, $plugin->new_version, '>=' ) ) continue;

			$value->response[ $plugin->plugin ] = self::$plugins[ $slug ];

			// //Ticket #35032 https://core.trac.wordpress.org/ticket/35032
			// if('plugins.php' == $pagenow)
			// 	$value->response[ $plugin->plugin ]->slug = strtolower($slug);

		}

		return $value;
	}

	private function header_infos( $slug ) {

		global $pagenow;

		include ABSPATH . WPINC . '/version.php';

		if(!$wp_version) global $wp_version;

		$return = array(
			'licensecode' => isset(self::$plugin_data[ $slug ]->licensecode) ? self::$plugin_data[ $slug ]->licensecode : NULL,
			'version' => self::$plugins[ $slug ]->version,
			'wp-version' => $wp_version,
			'referer' => home_url(),
			'multisite' => is_multisite(),
			'auto' => $pagenow == 'wp-cron.php',
		);

		if(isset(self::$plugin_data[ $slug ]->custom)) $return['custom'] = self::$plugin_data[ $slug ]->custom;

		return $return;
	}

}

endif;
