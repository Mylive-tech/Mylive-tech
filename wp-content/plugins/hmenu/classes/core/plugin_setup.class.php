<?php
	
	#PLUGIN SETUP
	class hmenu_setup{
		
		#CLASS VARS
		private $capability = 'publish_posts';
		private $plugin_name;
		private $plugin_friendly_name;
		private $plugin_version;
		private $plugin_prefix;
		private $plugin_dir;
		private $plugin_url;
		private $first_release;
		private $last_update;
		private $plugin_friendly_description;
		private $display;
		
		#CONSTRUCT
		public function __construct($plugin_name,$plugin_dir,$plugin_url,$plugin_friendly_name,$plugin_version,$plugin_prefix,$first_release,$last_update,$plugin_friendly_description){	
			//define class vars
			$this->plugin_name = $plugin_name;
			$this->plugin_dir = $plugin_dir;
			$this->plugin_url = $plugin_url;
			$this->plugin_friendly_name = $plugin_friendly_name;
			$this->plugin_version = $plugin_version;
			$this->plugin_prefix = $plugin_prefix;
			$this->first_release = $first_release;
			$this->last_update = $last_update;
			$this->plugin_friendly_description = $plugin_friendly_description;
			//construct admin menu
			add_action('admin_menu', array(&$this, 'construct_admin_menu'));
			//add meta
			add_action('admin_head',array(&$this,'add_admin_meta'));
			//load javascript
			add_action('admin_enqueue_scripts', array(&$this, 'load_admin_javascript'));
			//load css
			add_action('admin_enqueue_scripts', array(&$this, 'load_admin_css'));
			//instantiate display class
			$this->display = new hmenu_display($this->plugin_dir);
			//initialise shortcode listener
			$shortcode = new hmenu_shortcodes($this->plugin_prefix,$this->plugin_name,$this->plugin_dir,$this->plugin_url);
			add_action('init', array(&$shortcode,'initialise_shortcode_listener'));
			//non shortcode front-end output
			add_filter('wp_nav_menu_items',array(&$this,'implement_custom_menu'), 1, 2);
			add_filter('wp_nav_menu_args',array(&$this,'implement_fallback_menu'), 12, 2);
		}
		
		#MENU FILTER FUNCTION
		public function implement_fallback_menu($args = array()) {			
			
			#GLOBALS
			global $wpdb;
			
			$args['fallback_cb'] = array(&$this, 'menu_fallback');
			
			#GET MENU LOCATION
			$menus_locations = get_registered_nav_menus();
			
			#GET MENUS
			$result = $wpdb->get_row("SELECT * FROM ". $wpdb->base_prefix ."hmenu_menu WHERE overwrite ='".$args['theme_location']."' AND deleted = '0' ORDER BY created DESC");
			
			#CHECK THE RUSULT
			if($result){
				foreach($menus_locations as $location => $description){
					if($result->overwrite == $location){
						$args['menu_class'] = '';
						$args['container'] = '';
						$args['items_wrap'] = '%3$s';
					};
				}	
			}
					
			return $args;	
		}
		
		#MENU CALL BACK FUNCTION
		public function menu_fallback($args){
			
			#INSTANTIATE SHORTCODE CLASS
			$shortcode = new hmenu_shortcodes('hmenu_','hmenu', dirname(__FILE__),plugins_url('hmenu') .'/');
			
			#GLOBALS
			global $wpdb;
			
			#GET MENU LOCATION
			$menus_locations = get_registered_nav_menus();
			
			#GET MENUS
			$result = $wpdb->get_row("SELECT * FROM ". $wpdb->base_prefix ."hmenu_menu WHERE overwrite ='".$args['theme_location']."' AND deleted = '0' ORDER BY created DESC");
			
			#CHECK THE RUSULT
			if($result){
				foreach($menus_locations as $location => $description){
					if($result->overwrite == $location){
						$atts['id'] = $result->menuId;
						echo $shortcode->use_shortcode($atts);
					};
				}	
			}
			
		}
		
		#CUSTOM MENU IMPLEMENTATION
		public function implement_custom_menu($menu, $args){
			
			#INSTANTIATE SHORTCODE CLASS
			$shortcode = new hmenu_shortcodes('hmenu_','hmenu', dirname(__FILE__),plugins_url('hmenu') .'/');
			
			#GLOBALS
			global $wpdb;
			
			#GET MENU LOCATION
			$args = (array)$args;
			
			#GET MENUS
			$result = $wpdb->get_row("SELECT * FROM ". $wpdb->base_prefix ."hmenu_menu WHERE overwrite ='".$args['theme_location']."' AND deleted = '0' ORDER BY created DESC");
			
			#CHECK THE RUSULT
			if($result && $args['theme_location'] != ''){				
				$atts['id'] = $result->menuId;
				return $shortcode->use_shortcode($atts);
			}
			
			#RETURN
			return $menu;
			
		}		
				
		#PAGE LOADER
		public function load_page(){
			//load global helper
			global $hplugin_helper;
			//load page content
			$this->display->output_admin($hplugin_helper,$this->plugin_name,$this->plugin_friendly_name,$this->plugin_version,$this->plugin_url,$this->first_release,$this->last_update, $this->plugin_friendly_description);
		}
		
		#CONSTRUCT ADMIN MENU ITEM
		public function construct_admin_menu(){
			add_menu_page($this->plugin_friendly_name, 'Hero Menu' , $this->capability, 'hmenu', array(&$this,'load_page'), 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iVW50aXRsZWQtMS5mdy1QYWdlX3gyNV8yMDEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSItOTcyLjUgNDkwLjUgMTAwIDEwMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAtOTcyLjUgNDkwLjUgMTAwIDEwMCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PHBhdGggZmlsbD0ibm9uZSIgZD0ieiIvPjxnIGlkPSJMYXllcl94MjVfMjAxIj48cGF0aCBmaWxsPSIjREU1MjVCIiBkPSJNLTg4OC40LDU5MC41Yy0wLjMsMC0wLjYsMC0wLjktMC4xYy0yLjUtMC4zLTMuOS0xLjgtNC0xLjljMCwwLDAsMCwwLTAuMWwtMC4xLTAuMmwtMS0wLjVjLTAuMSwwLTAuMS0wLjEtMC4xLTAuMmMwLDAsMC0wLjItMC4xLTAuNWMtMC4xLTEsMC0yLjQsMC41LTNjLTAuNS0wLjgtMC40LTEuNy0wLjMtMi40YzAtMC4xLDAtMC4zLDAuMS0wLjRjMC4xLTEuMS0xLjQtNi0yLjItNy42Yy0wLjgtMS42LTQuNC03LjQtNC41LTcuNWMwLDAsMC0wLjEsMC0wLjJjMC4xLTAuMywwLjItMC44LDAuNS0xLjNjLTAuMi0xLjQsMC4xLTYuNiwwLjItNi44YzAsMCwwLDAsMCwwYy0wLjYtMC44LTEuNC0xLjgtMi4yLTIuOGMtMC45LTEuMS0yLTIuNC0zLjEtMy44YzAsMCwwLDAsMC0wLjFjLTAuMiwxLjEtMC45LDQuNS0xLDcuNWMwLDAsMCwwLDAsMGMwLjEsMC4yLDIuNCw0LjYsMi44LDcuMWMwLjEsMCwwLjEsMC4xLDAuMSwwLjFjMCwwLDAuNSwwLjktMC4xLDIuMmMtMC4yLDAuNS0wLjksMS42LTEuNiwyLjhjLTAuOSwxLjQtMS45LDMuMS0yLjEsMy43Yy0wLjQsMS4xLTEuMyw3LjUtMSw5LjNjMCwwLDAsMC4xLDAsMC4xYzAuNSwwLjgsMSwyLDAuNiwzLjJsLTAuMiwwLjVjMCwwLjEtMC4xLDAuMS0wLjIsMC4xYzAsMC0zLjQsMC4xLTQuMSwwLjJjLTAuNywwLjEtMS4xLDAuMS0yLDAuNGMtMC42LDAuMi0yLjcsMC4zLTQuNywwLjNjLTEsMC0yLjksMC0zLjctMC4zYy0wLjEsMC0wLjEtMC4xLTAuMS0wLjFsLTAuMi0wLjdjLTAuMS0wLjMtMC4yLTEuMSwwLjUtMS41YzAuNS0wLjMsMS4xLTAuNSwxLjctMC43YzAuMy0wLjEsMC43LTAuMiwxLTAuM2MwLjMtMC4xLDAuNi0wLjIsMS0wLjNjMCwwLDAsMCwwLDBjMCwwLDEuNy0xLjIsMi42LTEuOGMwLjItMC4xLDAuNi0wLjQsMS4xLTAuNWMwLTAuMSwwLTAuMiwwLTAuM2MwLTAuMSwwLjEtMC4yLDAuMy0wLjRjMC0wLjMtMC4xLTEsMC4yLTEuN2MtMC4xLTAuMy0wLjMtMS4zLDAuMy0yLjRjMC0wLjMtMC4xLTEuMywwLjEtMS45Yy0wLjEtMC4zLTAuMi0xLjQsMC4xLTIuMWMtMC4xLTAuMy0wLjMtMS4xLDAuMS0yLjFjLTAuMi0wLjMtMC42LTEuNC0wLjEtMi40YzAtMC4xLTAuMS0wLjQtMC4xLTAuOGMwLDAsMC0wLjEsMC0wLjFsLTAuMS0yLjNjMCwwLDAtMC4xLDAtMC4xYzAuMi0wLjUsMC42LTEuMywxLjItMi4xYzAsMCwwLDAsMCwwYzAuMS0wLjgtMC4xLTEuNC0wLjQtMmMtMC4xLTAuMi0wLjItMC40LTAuMi0wLjZjMC0wLjEtMC4xLTAuMy0wLjEtMC40Yy0wLjItMC42LTAuNC0xLTAuMy0xLjRjMC0wLjEsMC0wLjEsMC0wLjJjLTAuMi0wLjMtMC4yLTAuNy0wLjItMS4yYzAtMC4xLDAtMC4yLDAtMC4zYzAtMC43LTAuNC0xLjQtMC41LTEuNGMwLDAsMCwwLDAsMGMwLDAsMCwwLDAtMC4xbC0wLjUsMS41YzAsMC4xLTAuMSwwLjEtMC4yLDAuMmMwLDAsMCwwLDAsMGMtMC4xLDAtMC4yLDAtMC4yLTAuMWMtMi43LTQuMS04LjItNC4zLTEyLjMtNC41Yy0xLjYtMC4xLTIuOS0wLjEtMy45LTAuNGMtMS0wLjMtMS44LTAuNy0yLjMtMS4xYy0wLjYtMC41LTAuOC0xLjItMC41LTEuN2MwLDAsMCwwLDAsMGMwLTAuMSwwLTAuMSwwLTAuMmwxLjYtNC4yYy0wLjQsMC0wLjktMC4xLTEuNi0wLjFjLTIuMi0wLjEtNS44LTAuMS0xMS4xLTEuNmMtNy40LTIuMS05LjYtOS40LTEwLjYtMTNjLTEtMy41LTYuNi03LjYtNi42LTcuNmMtMC4xLDAtMC4xLTAuMS0wLjEtMC4yYzAtMC4xLDAtMC4xLDAuMS0wLjJjNC4zLTMuOSwxMi00LjgsMTguMS01LjVjMS42LTAuMiwzLjEtMC40LDQuMy0wLjZjNS43LTAuOSwxMi4zLTUuNywxNS04LjVjMC41LTAuNSwwLjktMC45LDEuMy0xLjNjMS43LTEuOCwzLjEtMy40LDUuNy0zLjRjMC4xLDAsMC4zLDAsMC40LDBjMC41LDAsMSwwLjEsMS41LDAuMmwwLDBjMS0wLjgsMi4zLTEsMi4zLTEuMWMwLDAsMCwwLDAuMSwwYzAsMCwwLjEsMCwwLjEsMGMwLjIsMC4xLDAuNCwwLjMsMC42LDAuNGwwLjItMC4xYzAuNC0wLjIsMC45LTAuNCwxLjMtMC42YzAuMSwwLDAuMSwwLDAuMi0wLjFjMC4zLTAuMSwwLjQtMC4yLDAuNC0wLjRsMCwwYzAtMC4xLDAtMC4yLDAtMC4yYzAsMC0wLjIsMC0wLjIsMGMtMC4zLDAtMC41LDAtMC42LTAuMWMtMC40LTAuNC0wLjctMS4xLTAuNi0xLjhjMC4xLTAuOCwwLjEtMS45LDAtMi4zYzAtMC4xLDAtMC4xLDAtMC4yYy0wLjEtMC40LTAuMi0wLjcsMC4yLTEuNGMwLTAuMiwwLTAuNS0wLjEtMC42Yy0wLjMtMC40LDAuMS0xLDAuMi0xLjJjMC4yLTAuMywwLjQtMC41LDAuNS0wLjdjLTAuMS0wLjQtMC41LTEuNiwwLjMtMmMwLjItMC4xLDAuNC0wLjIsMC42LTAuM2MwLjYtMC4zLDEuMS0wLjUsMS4yLTAuOWMwLTAuMSwwLjEtMC4xLDAuMS0wLjFjMCwwLDAuMSwwLDAuMSwwYzAsMCwwLjEsMCwwLjEsMGMwLjEsMCwwLjQsMC4yLDAuNSwwLjVjMC4yLTAuMSwwLjUtMC4yLDAuOC0wLjJjMC41LDAsMSwwLjIsMS42LDAuNWMyLjIsMS4xLDQsMi4xLDQuMywyLjJjMC4xLDAsMC4yLDAuMSwwLjIsMC4zYzAsMC4yLTAuMiwwLjgtMC40LDEuMWwwLjMsMC4zYzAuMSwwLjEsMC4xLDAuMiwwLDAuM2wtMC4xLDAuMWwwLjEsMC4zYzAsMC4xLDAsMC4xLDAsMC4ydjBjMCwwLTAuMSwwLjEtMC4xLDAuNGMwLDAuNC0wLjEsMS4zLTAuMSwxLjNjMCwwLDAsMC4xLDAsMC4xbC0wLjIsMC4zbDAsMC40YzAsMCwwLDAuMSwwLDAuMWMtMC4xLDAuMi0wLjEsMC40LTAuMSwwLjZjMCwwLDAsMCwwLDBjMCwwLjMtMC4xLDAuNi0wLjEsMC44bDAsMGMwLDAuMiwwLDAuNSwwLDEuNGMwLjEsMC4zLDAuMiwwLjQsMC4yLDAuNGMwLjIsMC4yLDAuMywwLjMsMC40LDAuM2MwLjItMC4yLDAuNS0wLjMsMC43LTAuNGMwLDAsMC4xLDAsMC4xLDBjMCwwLDAuMSwwLDAuMSwwbDEuNSwwLjdjMC4zLTAuMSwwLjYtMC4yLDAuOS0wLjNjMCwwLDAsMCwwLDBjMCwwLDAuMSwwLDAuMSwwbDEuOSwwLjljMC45LTAuMywxLjYtMC40LDIuMS0wLjVjMCwwLDAsMCwwLDBjMC4xLDAsMC4xLDAsMC4yLDAuMWMwLjEsMC4xLDEsMC44LDEuMywxLjRjMC40LDAsMC44LTAuMSwxLjEtMC4xYzAuNCwwLDAuOCwwLDEuMiwwLjFjMC45LDAuMSwxLjYsMC40LDIsMC45YzAuNSwwLjYsMC42LDEuNCwwLjQsMi40Yy0wLjIsMS4zLTAuOSwyLjUtMSwyLjZjMCwwLDAsMCwwLDBjMCwwLjEsMCwwLjIsMCwwLjNjMC41LDAuMywyLjIsMS4zLDIuNCwzLjljMC4xLDEsMC4xLDEuNywwLjEsMi4yYzAuNiwwLjksMS44LDMuMSwyLjQsMy44YzAuOCwxLDIsMy43LDIuMyw0LjRjMC4xLDAuMiwxLDEuNSwwLjEsMi4zYy0wLjUsMC41LTIuMSwxLjQtMy41LDIuMmMtMC43LDAuNC0xLjMsMC43LTEuOCwxYy0xLjEsMC43LTQsMS43LTYuNiwyLjFjLTAuNiwxLTMuMyw1LjItNC42LDYuN2MwLDAsMCwwLDAsMGMwLjQsMS40LDAuNywyLjcsMS4xLDMuN2MwLjcsMi4zLDIuMSw3LjEsMywxMC41YzAuNiwyLjIsMS4xLDQsMS4yLDQuM2MwLjEsMC40LDAuNywxLjMsMS4yLDIuM2MwLjUsMC45LDEsMS43LDEuMiwyLjFjMC43LDAuNCwxLjUsMC44LDEuNywxYzAuMSwwLDAuMSwwLjEsMC4xLDAuMmwxLDE5LjdjMC4xLDAuMSwwLjIsMC4xLDAuMywwLjJjMCwwLDAsMCwwLDAuMWMwLjQsMC42LDEuNCwyLjIsMS45LDIuNWMwLjYsMC40LDIuNSwyLjQsMi41LDIuNGwwLDBjMCwwLDAsMC4xLDAuMSwwLjFjMCwwLDAuMiwwLjgtMC40LDEuMkMtODg0LjMsNTg5LjYtODg2LjMsNTkwLjUtODg4LjQsNTkwLjVDLTg4OC40LDU5MC41LTg4OC40LDU5MC41LTg4OC40LDU5MC41eiIvPjwvZz48cGF0aCBmaWxsPSJub25lIiBkPSJ6Ii8+PC9zdmc+');
		}
		
		#ADD META TO ADMIN
		public function add_admin_meta(){
			//load global helper
			global $hmenu_helper;
			if(is_admin() && $hmenu_helper->onAdmin()){ //admin panel
				echo "<meta name='robots' content='noindex, nofollow' />\n";
			}
		}
		
		#LOAD JAVASCRIPT
		public function load_admin_javascript(){
			//load global helper
			global $hmenu_helper;
			//load jQuery
			wp_enqueue_script('jquery');
			wp_enqueue_media();
			//load plugin js
			if(is_admin() && $hmenu_helper->onAdmin()){ //admin panel
				//admin core scripts
				wp_register_script($this->plugin_prefix .'admin', $this->plugin_url .'assets/js/admin_core.js');
				wp_enqueue_script($this->plugin_prefix .'admin');
				//component manager scripts
				wp_register_script($this->plugin_prefix .'component_manager', $this->plugin_url .'assets/js/component_manager.js');
				wp_enqueue_script($this->plugin_prefix .'component_manager');
				//jquery ui
				wp_register_script($this->plugin_prefix .'jquery_ui', $this->plugin_url .'assets/js/jquery-ui.js');
				wp_enqueue_script($this->plugin_prefix .'jquery_ui');
				//toggle manager scripts
				wp_register_script($this->plugin_prefix .'toggle_manager', $this->plugin_url .'assets/js/toggle_manager.js');
				wp_enqueue_script($this->plugin_prefix .'toggle_manager');
				//toggle manager scripts
				wp_register_script($this->plugin_prefix .'file_processor', $this->plugin_url .'assets/js/font_processor.js');
				wp_enqueue_script($this->plugin_prefix .'file_processor');
			}
		}

		#LOAD STYLES
		public function load_admin_css(){
			//load global helper
			global $hmenu_helper;
			//load plugin css
			if(is_admin() && $hmenu_helper->onAdmin()){ //admin panel
				//admin core css
				wp_register_style($this->plugin_prefix .'adminstyles', $this->plugin_url .'assets/css/admin_styles.css');
				wp_enqueue_style($this->plugin_prefix .'adminstyles');
				//backend user css
				wp_register_style($this->plugin_prefix .'backendstyles', $this->plugin_url .'assets/css/backend_styles.css');
				wp_enqueue_style($this->plugin_prefix .'backendstyles');
				//google fonts
				wp_register_style($this->plugin_prefix .'googlefonts', '//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700');
				wp_enqueue_style($this->plugin_prefix .'googlefonts');
				
			}
		}
		
	}