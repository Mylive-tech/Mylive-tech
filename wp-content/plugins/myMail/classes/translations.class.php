<?php if (!defined('ABSPATH')) die('not allowed');

class mymail_translations {

	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init'), 1);

	}


	public function init() {

		load_plugin_textdomain( 'mymail', false, basename(MYMAIL_DIR) . '/languages' );
		add_filter( 'site_transient_update_plugins', array( &$this, 'update_plugins_filter'), 1 );
		add_action( 'delete_site_transient_update_plugins', array( &$this, 'clear_transient') );
	}


	public function clear_transient(){
		delete_transient( '_mymail_translation' );
	}


	public function update_plugins_filter($value){
		//no translation support
		if(!isset($value->translations)) return $value;

		$data = $this->get_translation_data();

		if(!empty($data)) $value->translations[] = $data;

		return $value;
	}


	public function get_translation_data(){

		if ( false === ( $data = get_transient( '_mymail_translation' ) ) ) {

			//check if a newer version is available once a day
			$recheckafter = 86400;

			$locale = get_locale();

			if('en_US' == $locale){
				set_transient( '_mymail_translation', array(), $recheckafter );
				return false;
			}

			$file = 'mymail-'.$locale;
			$url = apply_filters('mymail_translation_url', 'http://mymailapp.github.io/languages/mymail/'.$locale.'.zip', $locale);
			$location = WP_LANG_DIR.'/plugins';
			$mo_file = $location.'/'.$file.'.mo';
			$filemtime = file_exists($mo_file) ? filemtime($mo_file) : 0;

			$r = wp_remote_get( $url, array('method' => 'HEAD'));
			$code = wp_remote_retrieve_response_code($r);

			if(200 == $code){

				$headers = wp_remote_retrieve_headers($r);
				$lastmodified = strtotime($headers['last-modified']);

				if($lastmodified-$filemtime > 0){
					$data = array(
						'type' => 'plugin',
						'slug' => 'myMail',
						'language' => $locale,
						'version' => MYMAIL_VERSION,
						'updated' => date('Y-m-d H:i:s', $lastmodified),
						'current' => $filemtime,
						'package' => $url,
						'autoupdate' => !!mymail_option('autoupdate'),
					);
				}

			}else{
				//the language was not found, maybe to another time ( 30 days )
				$recheckafter = 86400*30;
			}

			set_transient( '_mymail_translation', $data, $recheckafter );
		}

		return $data;

	}

}
