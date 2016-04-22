<?php

	#PLUGIN AUTO-GENERATION MANAGEMENT
	class hmenu_autogenerate{
		
		#CLASS VARS
		private $plugin_prefix;
		
		#CONSTRUCT
		public function __construct($plugin_prefix){
			//define class vars
			$this->plugin_prefix = $plugin_prefix;
		}
		
		#AUTO-GENERATE VIEWS BASED ON MENU OBJECT
		public function create_views(){
			//get menu object
			$menu_object = $_POST['menu_object'];
			//check for sidebar prepopulation javascript
			if(!file_exists(realpath($this->plugin_prefix .'/views') .'/sidebar_prepopulation.js')){
				$handle = fopen(realpath($this->plugin_prefix .'/views') .'/sidebar_prepopulation.js', 'w');
				fwrite($handle, '//SIDEBAR PRE_POPULATION' ."\n\n");
				fwrite($handle, '//load' ."\n");
				fwrite($handle, 'function prepopulate_sidebar_elements(){' ."\n");
				fwrite($handle, '}');
				fclose($handle);
			}
			//iterate menu object and create views where required
			foreach($menu_object as $item){
				//check for view directory
				if(isset($item['viewpath']) && $item['viewpath'] != ''){
					//directory and default viewport
					$dir = $this->plugin_prefix .'/views/'. $item['viewpath'];
					if(!is_dir($dir) && $item['type'] != 'button'){
						//create the directory
						mkdir($dir);
						mkdir($dir .'/js/');
						mkdir($dir .'/html_snippets/');
						//place the view core js
						$handle = fopen(realpath($dir .'/js/') .'/view.core.js', 'w');
						fwrite($handle, '//'. strtoupper(str_replace('/','',$item['viewpath'])) .' VIEW CORE');
						fclose($handle);
						//place the core view (index.php)
						$handle = fopen(realpath($dir) .'/index.php', 'w');
						fwrite($handle, '<script type="text/javascript" src="<?php echo $_GET[\'v\']; ?>js/view.core.js"></script>' ."\n");
						fwrite($handle, '<div class="hero_viewport">'. "\n" .'</div>');
						fclose($handle);
					}
					//sub views
					if(isset($item['views'])){
						foreach($item['views'] as $view){
							foreach($view['submenu'] as $sub){
								//place the view js
								if(!file_exists(realpath($dir) .'/js/'. $sub['view'] .'.view.js')){
									$handle = fopen(realpath($dir) .'/js/'. $sub['view'] .'.view.js', 'w');
									fwrite($handle, '//'. strtoupper(str_replace('/','',$sub['view'])) .' VIEW');
									fclose($handle);
								}
								if(!file_exists(realpath($dir) .'/'. $sub['view'] .'.view.php')){
									//place the view
									$handle = fopen(realpath($dir) .'/'. $sub['view'] .'.view.php', 'w');
									fwrite($handle, '<script type="text/javascript" src="<?php echo $_GET[\'vp\']; ?>js/'. $sub['view'] .'.view.js"></script>' ."\n");
									fclose($handle);
								}
							}
						}
					}
				}
			}
			exit();
		}
		
	}