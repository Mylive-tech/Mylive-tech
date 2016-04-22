<?php

	#FRAME SECURITY
	class hmenu_frame_sec{
		
		#CLASS VARS
		private $plugin_dir;
		private $path_to_frame_sec;
		private $frame_sec_dir = 'frame_sec/';
		private $tag_validity_period = 1200; //validity in seconds
		
		#CONSTRUCT
		public function __construct($plugin_dir){
			//set class vars
			$this->plugin_dir = $plugin_dir;
			$this->path_to_frame_sec = $plugin_dir .'/'. $this->frame_sec_dir;
		}
		
		#GET SECURITY CODE
		public function get_security_code(){
			//generate security code
			$security_code = $this->generate_security_code();
			//return code via AJAX
			echo json_encode($security_code);
			exit();
		}
		
		#GENERATE SECURITY CODE
		private function generate_security_code(){
			//load global helper
			global $hmenu_helper;
			//generate security code
			$security_code = str_replace('-','',$hmenu_helper->genGUID());
			//persist security tag
			$this->persist_security_tag($security_code);
			//return security tag
			return $security_code;
		}
		
		#PERSIST SECURITY CODE
		private function persist_security_tag($security_code){
			//check directory
			if(!is_dir($this->path_to_frame_sec)){
				mkdir($this->path_to_frame_sec);
			}
			//clean old tags
			$this->clean_old_security_tags();
			//place security tag
			$sec_tag = fopen($this->path_to_frame_sec . $security_code, "w");
			fclose($sec_tag);
			//return
			return true;
		}
		
		#CLEAN OLD SECURITY TAG
		private function clean_old_security_tags(){
			//check for old
			if($handle = opendir($this->path_to_frame_sec)){
				while(false !== ($file = readdir($handle))){
					if('.' === $file) continue;
					if('..' === $file) continue;
					if(intval(time() - filemtime($this->path_to_frame_sec . $file)) > intval($this->tag_validity_period)){
						unlink($this->path_to_frame_sec . $file);
					}
				}
				closedir($handle);
			}
		}
		
	}