<?php
	
	#PROMOTION MANAGEMENT
	class hmenu_promo{
		
		#CLASS VARS
		private $plugin_slug;
		private $plugin_name;
		private $plugin_version;
		private $plugin_uuid;
		private $installed;
		private $api_version;
		private $plugin_getrate_url;
		private $blog_url;
		
		#CONSTRUCT
		public function __construct($plugin_slug,$plugin_name,$api_version){
			//access globals
			global $hmenu_helper;
			//define class vars
			$this->plugin_slug = $plugin_slug;
			$this->plugin_name = $plugin_name;
			$this->api_version = $api_version;
			//set blog url
			$this->blog_url = $hmenu_helper->get_blog_domain();
			//get plugin info
			$this->get_plugin_info();
			//construct promo link
			$this->plugin_promo_url = 'https://www.herodatacenter.com/'. $this->api_version .'/promo/'. $this->plugin_name .'/'. $this->plugin_version .'/'. $this->plugin_uuid .'/'. $this->installed .'/'. $this->blog_url .'/false';
		}
		
		#GET PROMOTION
		public function get_promotion(){
			//api query
			$result = wp_remote_retrieve_body(wp_remote_get($this->plugin_promo_url));
			$result_object = json_decode($result);
			if($result_object->status == 200){
				echo json_encode(array(
					'image' => $result_object->data->promotion_image_location,
					'link' => $result_object->data->promotion_link
				));
				exit;
			}
			echo json_encode(false);
			exit;
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