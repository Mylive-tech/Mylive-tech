<?php
	
	#DEACTIVATE PLUGIN
	class hmenu_deactivate{
		
		#CLASS VARS
		private $plugin_name;
		
		#CONSTRUCT
		public function __construct($plugin_name){
			//set class vars
			$this->plugin_name = $plugin_name;
		}
		
		#TEARDOWN PLUGIN
		public function teardown_plugin(){
			//access globals
			global $wpdb;
			//deactivate plugin
			/*
				-- deactive your plugin here --
				
				note: This hook is not really necessary. Please be sure that you are using this correctly.
			*/
			//mark plugin as inactive
			$wpdb->query("UPDATE `". $wpdb->base_prefix ."hplugin_root` SET `active` = 0 WHERE `plugin_name` = '". $this->plugin_name ."';");
		}
		
	}