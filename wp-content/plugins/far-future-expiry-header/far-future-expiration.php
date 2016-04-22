<?php
/*
Plugin Name: Far Future Expiration Plugin
Plugin URI: http://www.tipsandtricks-hq.com/wordpress-far-future-expiration-plugin-5980
Description: This plugin will add a "far future expiration" date for various file types to improve site performance.
Author: Tips and Tricks HQ
Version: 1.3
Author URI: http://www.tipsandtricks-hq.com/
License: GPLv2 or later
*/

/*
 * The main plugin class, 
 * initialized right after declaration
 */
if(!class_exists('farFutureExpiration')) 
{
	class farFutureExpiration 
	{
		/*
		 * Declare keys here as well as our tabs array which 
		 * is populated when registering settings
		 */
		private $edit_ffe_settings_page_key = 'edit_ffe_settings_page';
		private $ffe_options_key = 'ffe_plugin_options';
		private $ffe_settings_tabs = array();
	
		function __construct() {
			$this->define_constants();
			$this->loader_operations();
		}

		function define_constants() {
			global $wpdb;
			define('FFE_PLUGIN_DB_VERSION', '1.0');
			define('FFE_PLUGIN_PATH', dirname(__FILE__));
			define('FFE_PLUGIN_URL', plugins_url('',__FILE__));
		}

		function loader_operations(){
			add_action('plugins_loaded', array( &$this, 'ffe_execute_plugins_loaded_operations'));
		}
		
		function ffe_execute_plugins_loaded_operations()
		{	
			add_action( 'init', array( &$this, 'load_settings' ) );
			add_action( 'init', array( &$this, 'do_init_time_tasks' ) );
			add_action( 'admin_init', array( &$this, 'register_ffe_settings_page' ) );
			add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
		}
		
		/*
		 * Loads tab settings from
		 * the database into their respective arrays. Uses
		 * array_merge to merge with default values if they're
		 * missing.
		 */
		function load_settings() {
			$this->edit_ffe_settings_page = (array) get_option( $this->edit_ffe_settings_page_key );
			
			// Merge with defaults
			$this->edit_ffe_settings_page = array_merge( array(
				'edit_ffe_option' => 'Floating Cart Settings Page'
			), $this->edit_ffe_settings_page );
			
		}
	
		function do_init_time_tasks()
		{
                    if(!is_admin()){//Front end init time tasks
                        $ffe_settings = get_option('far_future_expiration_settings');
                        if($ffe_settings['enable_gzip']){//Gzip compression is enabled
                            ob_start('ob_gzhandler');
                        }
                    }
		}
		
		
		/*
		 * Registers the display templates page via the Settings API,
		 * appends the setting to the tabs array of the object.
		 */
		function register_ffe_settings_page() {
			$this->ffe_settings_tabs[$this->edit_ffe_settings_page_key] = 'Far Future Expiration Settings';
			register_setting( $this->edit_ffe_settings_page_key, $this->edit_ffe_settings_page_key );
		}
		
		/******************************************************************************
		 * Now we just need to define an admin page.
		 ******************************************************************************/
	
		/*
		 * Called during admin_menu, adds an options
		 * page under Settings 
		*/
		
		function add_admin_menus(){	    
			add_options_page('FarFutureExpiry', 'FarFutureExpiry', 'manage_options', $this->ffe_options_key, array(&$this, 'ffe_plugin_option_page'));
		}
	
		/*
		 * Plugin Options page rendering goes here, checks
		 * for active tab and replaces key with the related
		 * settings key. Uses the plugin_options_tabs method
		 * to render the tabs.
		 */
		function ffe_plugin_option_page() {
			$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->edit_ffe_settings_page_key;
			?>
			<div class="wrap">
				<?php 
				$this->plugin_options_tabs();
				if ($tab == $this->edit_ffe_settings_page_key)
				{
					include_once('far-future-expiration-settings.php');
					displayFarFutureExpirationSettings();				
				}
				?>
			</div>
			<?php
		}
		
		function current_tab() {
			$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->edit_ffe_settings_page_key;
			return $tab;
		}
		
		/*
		 * Renders our tabs in the plugin options page,
		 * walks through the object's tabs array and prints
		 * them one by one. Provides the heading for the
		 * plugin_options_page method.
		 */
		function plugin_options_tabs() {
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->edit_ffe_settings_page_key;
	
			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $this->ffe_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->ffe_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
			}
			echo '</h2>';
		}
		
		
		function write_to_htaccess()
	    {   
	        //clean up old rules first
	        if ($this->delete_from_htaccess() == -1) 
	        {
	            return -1; //unable to write to the file
	        }
	
	        $htaccess = ABSPATH . '.htaccess';
	        //get the subdirectory if it is installed in one
	        $siteurl = explode( '/', get_option( 'siteurl' ) );
			if (isset($siteurl[3])) 
	        {
	            $dir = '/' . $siteurl[3] . '/';
	        } 
	        else
	        {
	            $dir = '/';
			}        
	        
	        if (!$f = @fopen($htaccess, 'a+')) 
	        {
	            @chmod( $htaccess, 0644 );
	            if (!$f = @fopen( $htaccess, 'a+')) 
	            {
	                return -1;
	            }					
	        }
	        //backup_a_file($htaccess); //should we back up htaccess file??
	        @ini_set( 'auto_detect_line_endings', true );
	        $ht = explode( PHP_EOL, implode( '', file( $htaccess ) ) ); //parse each line of file into array
		
	        $rules = $this->getrules();
	        if ($rules == -1)
	        {
	            return -1;
	        }
	        
			$rulesarray = explode( PHP_EOL, $rules );
			$contents = array_merge( $rulesarray, $ht );
	        
	        if (!$f = @fopen($htaccess, 'w+')) 
	        {
	            return -1; //we can't write to the file
	        }
	        
	        $blank = false;
	        
	        //write each line to file
			foreach ( $contents as $insertline ) 
		    {
		            if ( trim( $insertline ) == '' ) 
		            {
		                if ( $blank == false ) 
		                {
		                    fwrite( $f, PHP_EOL . trim( $insertline ) );
		                }
		                $blank = true;	
		            }
		            else
		            {
		                $blank = false;
				fwrite( $f, PHP_EOL . trim( $insertline ) );
		            }
			}
	        @fclose( $f );
			return 1; //success
	    }
		
		function getrules()
	    {
	        @ini_set( 'auto_detect_line_endings', true );
			
	        //figure out what server they're using
	        if (strstr(strtolower(filter_var($_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING)), 'apache'))
	        {
	            $aiowps_server = 'apache';
	        } 
	        else if (strstr(strtolower(filter_var($_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING)), 'nginx'))
	        {
	            $aiowps_server = 'nginx';
	        } 
	        else if (strstr(strtolower(filter_var($_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING)), 'litespeed'))
	        {
	            $aiowps_server = 'litespeed';
	        }
	        else 
	        { //unsupported server
	            return -1;
	        }

	        $rules = '';
	        $far_future_expiration_settings = get_option('far_future_expiration_settings');
	        $expire_time = $far_future_expiration_settings['num_expiry_days'];
	        if($far_future_expiration_settings['enable_ffe']=='1'){
	        	//write the rules
	        	$rules .= '<IfModule mod_expires.c>' . PHP_EOL;
	        	$rules .= 'ExpiresActive on' . PHP_EOL;
	        	$rules .= '<FilesMatch "\.(';
	        	if($far_future_expiration_settings['enable_gif']=='1'){
	        		$rules .= 'gif|';
	        	}
	        	if($far_future_expiration_settings['enable_jpeg']=='1'){
	        		$rules .= 'jpeg|';
	        	}
	        	if($far_future_expiration_settings['enable_jpg']=='1'){
	        		$rules .= 'jpg|';
	        	}
	        	if($far_future_expiration_settings['enable_png']=='1'){
	        		$rules .= 'png|';
	        	}
	        	if($far_future_expiration_settings['enable_ico']=='1'){
	        		$rules .= 'ico|';
	        	}
	        	if($far_future_expiration_settings['enable_js']=='1'){
	        		$rules .= 'js|';
	        	}
	        	if($far_future_expiration_settings['enable_css']=='1'){
	        		$rules .= 'css|';
	        	}
	        	if($far_future_expiration_settings['enable_swf']=='1'){
	        		$rules .= 'swf|';
	        	}
	        	$expire_time_in_hours = $expire_time * 24;
	        	//Let's remove any trailing "|" if necessary
	        	$rules = rtrim($rules, "|");
	        	$rules .= ')$">' . PHP_EOL;
	        	$rules .= 'ExpiresDefault "access plus '.$expire_time_in_hours.' hours"'. PHP_EOL;
	        	$rules .= '</FilesMatch>'. PHP_EOL;
	        	$rules .= '</IfModule>' . PHP_EOL;
	        }
	        
	        //Add outer markers if we have rules
			if ($rules != '')
		        {
		            $rules = "# BEGIN Far Future Expiration Plugin" . PHP_EOL . $rules . "# END Far Future Expiration Plugin" . PHP_EOL;
			}
	        
	        return $rules;
	    }
	    
		
	    function delete_from_htaccess($section = 'Far Future Expiration Plugin')
	    {
	        $htaccess = ABSPATH . '.htaccess';
				
			@ini_set('auto_detect_line_endings', true);
			if (!file_exists($htaccess)) 
	        {
	            $ht = @fopen($htaccess, 'a+');
	            @fclose($ht);
			}
	        $ht_contents = explode(PHP_EOL, implode('', file($htaccess))); //parse each line of file into array
			if ($ht_contents) 
	        { //as long as there are lines in the file
	            $state = true;
	            if (!$f = @fopen($htaccess, 'w+')) 
	            {
	                @chmod( $htaccess, 0644 );
					if (!$f = @fopen( $htaccess, 'w+')) 
	                {
	                    return -1;
	                }
	            }
	            
	            foreach ( $ht_contents as $n => $markerline ) 
	            { //for each line in the file
	                if (strpos($markerline, '# BEGIN ' . $section) !== false) 
	                { //if we're at the beginning of the section
	                    $state = false;
	                }
	                if ($state == true) 
	                { //as long as we're not in the section keep writing
	                    fwrite($f, trim($markerline) . PHP_EOL);
					}
	                if (strpos($markerline, '# END ' . $section) !== false) 
	                { //see if we're at the end of the section
	                    $state = true;
	                }
	            }
	            @fclose($f);
	            return 1;
	        }
	        return 1;
	    }
		
	} //end class
}
$GLOBALS['ffe_plugin'] = new farFutureExpiration();
