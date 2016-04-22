<?php
	
	#PLUGIN BACK-END MANAGEMENT
	class hmenu_class_file_processor{
		
		#CLASS VARS
		private $plugin_dir;
		private $backend_class;
		
		private $font_pack_dir = '/_tmp_fonts/';
		private $fonts_dir = '/_fonts/';
		
		#CONSTRUCT
		public function __construct($plugin_dir, $backend){
			$this->plugin_dir = $plugin_dir;
			$this->backend_class = $backend;
		}
		
		#PROCESS FONT PACKS PACKS
		public function process_file(){
			//loop through directory
			if($handle = opendir($this->plugin_dir . $this->font_pack_dir)){
				while(false !== ($file = readdir($handle))){
					if('.' === $file) continue;
					if('..' === $file) continue;
					//unzip font pack
					$this->unzip_font_pack($file);
					//remove zip file
					$this->remove_zip($file);
					//check if marker pack is valid
					if($this->check_valid_font_pack(basename($file,'.zip'))){
						//process the font pack
						$status = $this->process_font_pack(basename($file,'.zip'));
					}
					//remove directory
					$this->remove_directory(basename($file,'.zip'));
				}
				closedir($handle);
			}
			//respond when processing complete
			echo json_encode($status);
			exit();
		}
		
		#UNZIP DIR
		private function unzip_font_pack($file){
			$zip = new ZipArchive;
			if($zip->open($this->plugin_dir . $this->font_pack_dir . $file) === TRUE){
				$zip->extractTo($this->plugin_dir . $this->font_pack_dir . basename($file,'.zip'));
				$zip->close();
			}
		}
		
		#REMOVE ZIP
		private function remove_zip($file){
			if(is_file($this->plugin_dir . $this->font_pack_dir . $file)){
				unlink($this->plugin_dir . $this->font_pack_dir . $file);
			}
		}
		
		#CHECK FOR VALID MARKER PACK
		private function check_valid_font_pack($dir_name){
			//check for config file and image directory
			if(is_file($this->plugin_dir . $this->font_pack_dir . $dir_name .'/json_fontpack.js') && is_dir($this->plugin_dir . $this->font_pack_dir . $dir_name .'/fonts')){
				return true;
			}
			return false;
		}
		
		#REMOVE DIRECTORY AND CONTENTS
		private function remove_directory($dir){
			$it = new RecursiveDirectoryIterator($this->plugin_dir . $this->font_pack_dir . $dir);
			$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			foreach($files as $file){
				if($file->getFilename() === '.' || $file->getFilename() === '..'){
					continue;
				}
				if($file->isDir()){
					rmdir($file->getRealPath());
				}else{
					unlink($file->getRealPath());
				}
			}
			rmdir($this->plugin_dir . $this->font_pack_dir . $dir);
		}	
		
		#PROCESS FONT PACK AND SAVE TO db
		private function process_font_pack($dir){
			
			#JSON FILE DATA
			$json_file_data = json_decode(file_get_contents($this->plugin_dir . $this->font_pack_dir . $dir .'/json_fontpack.js'));
			
			#FONTS DIRECTORY - CONTAINS ALL FONT DATA
			if($handle = opendir($this->plugin_dir . $this->font_pack_dir . $dir .'/fonts/')){
				while(false !== ($file = readdir($handle))){
					if('.' === $file) continue;
					if('..' === $file) continue;
					#GET FONT FILE CONTENTS
					$font_file_location = $this->plugin_dir . $this->font_pack_dir . $dir . '/fonts/' . $file;
					$font_file_data = file_get_contents($font_file_location);
					$encoded_font_64 = base64_encode($font_file_data);
					#PLACE DATA INTO ARRAY
					$ext = substr($file, strpos($file,'.') + 1);
					$fonts[$ext] = $encoded_font_64;
				}
				closedir($handle);
			}
			
			#INSERT - HERE - once it hits this it goes KABOOOM!
			$status = $this->backend_class->insert_font($json_file_data, $fonts);			
			
			return ($status);
			
		}
		
		
		
	}
	
	
	
	
	
	
	
	
	
	