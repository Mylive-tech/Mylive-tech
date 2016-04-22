<?php

	#PLUGIN REGISTRATION MANAGEMENT
	class hmenu_registration{
		
		#CLASS VARS
		private $plugin_prefix;
		public $backend;
		public $frontend;
		public $class_update;
		public $class_insert;
		public $class_get;
		public $class_generate;
		
		#CONSTRUCT
		public function __construct($plugin_prefix,$backend,$frontend,$class_update,$class_insert,$class_get,$class_generate){
			//define class vars
			$this->plugin_prefix = $plugin_prefix;
			$this->backend = $backend;
			$this->frontend = $frontend;
			$this->class_update = $class_update;
			$this->class_insert = $class_insert;
			$this->class_get = $class_get;
			$this->class_generate = $class_generate;
			//register ajax hooks
			$this->register_backend_ajax_calls();
			$this->register_class_update_ajax_calls();
			$this->register_class_insert_ajax_calls();
			$this->register_class_get_ajax_calls();
			$this->register_class_generate_ajax_calls();
			$this->register_frontend_ajax_calls();			
		}
		
		#REGISTER ADMIN AJAX CALLS
		private function register_backend_ajax_calls(){
			//reference global
			global $backend_ajax_calls;
			//construct hooks
			if(isset($backend_ajax_calls) && count($backend_ajax_calls) > 0){
				foreach($backend_ajax_calls as $call){
					add_action('wp_ajax_'. $this->plugin_prefix . $call['action'], array(&$this->backend, $call['method']));
				}
			}
		}
		private function register_class_update_ajax_calls(){
			//reference global
			global $class_update_ajax_calls;
			//construct hooks
			if(isset($class_update_ajax_calls) && count($class_update_ajax_calls) > 0){
				foreach($class_update_ajax_calls as $call){
					add_action('wp_ajax_'. $this->plugin_prefix . $call['action'], array(&$this->class_update, $call['method']));
				}
			}
		}
		private function register_class_insert_ajax_calls(){
			//reference global
			global $class_insert_ajax_calls;
			//construct hooks
			if(isset($class_insert_ajax_calls) && count($class_insert_ajax_calls) > 0){
				foreach($class_insert_ajax_calls as $call){
					add_action('wp_ajax_'. $this->plugin_prefix . $call['action'], array(&$this->class_insert, $call['method']));
				}
			}
		}
		private function register_class_get_ajax_calls(){
			//reference global
			global $class_get_ajax_calls;
			//construct hooks
			if(isset($class_get_ajax_calls) && count($class_get_ajax_calls) > 0){
				foreach($class_get_ajax_calls as $call){
					add_action('wp_ajax_'. $this->plugin_prefix . $call['action'], array(&$this->class_get, $call['method']));
				}
			}
		}
		private function register_class_generate_ajax_calls(){
			//reference global
			global $class_generate_ajax_calls;
			//construct hooks
			if(isset($class_generate_ajax_calls) && count($class_generate_ajax_calls) > 0){
				foreach($class_generate_ajax_calls as $call){
					add_action('wp_ajax_'. $this->plugin_prefix . $call['action'], array(&$this->class_generate, $call['method']));
				}
			}
		}
		
		#REGISTER USER AJAX CALLS
		private function register_frontend_ajax_calls(){
			//reference global
			global $frontend_ajax_calls;
			//construct hooks
			if(isset($frontend_ajax_calls) && count($frontend_ajax_calls) > 0){
				foreach($frontend_ajax_calls as $call){
					add_action('wp_ajax_'. $this->plugin_prefix . $call['action'], array(&$this->frontend, $call['method']));
					add_action('wp_ajax_nopriv_'. $this->plugin_prefix . $call['action'], array(&$this->frontend, $call['method']));
				}
			}
		}
		
	}