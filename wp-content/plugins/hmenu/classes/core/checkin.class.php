<?php
	
	#UPDATE MANAGEMENT
	class hmenu_checkin{
		
		#CLASS VARS
		private $development_mode = false;
		private $plugin_slug;
		private $plugin_name;
		private $plugin_friendly_name;
		private $plugin_version;
		private $plugin_uuid;
		private $installed;
		private $api_version;
		private $plugin_checkin_url;
		private $plugin_update_info_url;
		private $blog_url;
		
		#CONSTRUCT
		public function __construct($plugin_slug,$plugin_name,$plugin_friendly_name,$api_version){
			//access globals
			global $hmenu_helper;
			//define class vars
			$this->plugin_slug = $plugin_slug;
			$this->plugin_name = $plugin_name;
			$this->plugin_friendly_name = $plugin_friendly_name;
			$this->api_version = $api_version;
			//set blog url
			$this->blog_url = $hmenu_helper->get_blog_domain();
			//get plugin info
			$this->get_plugin_info();
			//construct checkin link
			$this->plugin_checkin_url = 'https://www.herodatacenter.com/'. $this->api_version .'/checkin/'. $this->plugin_name .'/'. $this->plugin_version .'/'. $this->plugin_uuid .'/'. $this->installed .'/'. $this->blog_url .'/'. $this->development_mode;
			//construct info link
			$this->plugin_update_info_url = 'https://www.herodatacenter.com/'. $this->api_version .'/update_info/'. $this->plugin_name .'/'. $this->plugin_version .'/'. $this->plugin_uuid .'/'. $this->installed .'/'. $this->blog_url .'/'. $this->development_mode;
			//define the alternative function for plugin update info check
			add_filter('plugins_api', array(&$this, 'check_update_info'), 10, 3);
		}
		
		#CHECKIN WITH API
		public function check_in($transient){			
			//check transient
			if(empty($transient->checked)){
				return $transient;
			}
			//query the api
			$result = wp_remote_retrieve_body(wp_remote_get($this->plugin_checkin_url));
			$result_object = json_decode($result);
			//check for success
			if(isset($result_object) && $result_object->status == 200){
				//get data
				$lastest_version = $result_object->data->version;
				$download_link = $result_object->data->download_link;
				//check current version
				if(version_compare($lastest_version .'', $this->plugin_version, ">")){ 
					//new version available
					$obj = new stdClass();
					$obj->slug = $this->plugin_slug; //plugin slug
					$obj->new_version = $lastest_version; //new version number
					//$obj->url = 'http://heroplugins.com'; //link to plugin page on website
					$obj->package = $download_link; //link to download file
					//append to transient
					$transient->response[$this->plugin_slug] = $obj;
					//return transient to WP
					return $transient;
				}
			}
			return $transient;
		}
	
		#CHECK INFO
		public function check_update_info($result, $action, $arg){
			//check transient for plugin
			if(isset($arg->slug) && $arg->slug === $this->plugin_slug){
				//query the api
				$result = wp_remote_retrieve_body(wp_remote_get($this->plugin_update_info_url));
				$result_object = json_decode($result);
				if($result_object->status == 200){
					$response = new stdClass();
					$response->name = $this->plugin_friendly_name; //plugin name
					$response->slug = $this->plugin_slug; //plugin slug
					$response->requires = $result_object->data->requires; //compatible from
					$response->tested = $result_object->data->tested; //compatible to
					$response->downloaded = $result_object->data->downloaded; //number of downloads for plugin
					$response->added = $result_object->data->added; //date plugin added
					$response->last_updated = $result_object->data->last_updated; //date plugin last updated
					$sections = array();
					foreach($result_object->data->sections as $key => $tab){
						$sections[$key] = $tab;
					}
					$response->sections = $sections;
					$response->download_link = $result_object->data->download_link; //link to download file
					return $response;
				}
			}
			return $result;
		}
		
		#GET PLUGIN INFORMATION FROM DB
		private function get_plugin_info(){ //extract info from db
			global $wpdb;
			if($wpdb->get_var("SHOW TABLES LIKE '". $wpdb->base_prefix ."hplugin_root'") == $wpdb->base_prefix .'hplugin_root'){
				$plugin_lookup = $wpdb->get_results("SELECT `plugin_version`, `plugin_uuid`, DATE(`date_created`) AS 'installed' FROM `". $wpdb->base_prefix ."hplugin_root` WHERE `plugin_name` = '". $this->plugin_name ."';");
				if($plugin_lookup){
					$this->plugin_version = $plugin_lookup[0]->plugin_version;
					$this->plugin_uuid = $plugin_lookup[0]->plugin_uuid;
					$this->installed = str_replace('-','',$plugin_lookup[0]->installed);
					return true;
				}
			}
			return false;
		}
		
	}