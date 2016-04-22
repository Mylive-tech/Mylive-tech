<?php 
/**
 * Class for tinymce integration and functions for Cool Video Gallery
 * @author Praveen Rajan
 */
class CvgTinyMCE extends CoolVideoGallery {
		
	/**
	 * Constructor of class
	 * @author Praveen Rajan
	 */
	function CvgTinyMCE() {
		
		add_action('init', array(&$this, 'cvg_button') );
		add_action('wp_ajax_cvg_tinymce', array(&$this, 'cvg_ajax_tinymce') );
	}
	
	/**
	 * Functions for button registration to tinymce
	 * @author Praveen Rajan
	 */
	function cvg_button() {
	 
	   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
	     return;
	   }
	 
	   if ( get_user_option('rich_editing') == 'true' ) {
	     add_filter( 'mce_external_plugins', array(&$this, 'add_plugin') );
	     add_filter( 'mce_buttons', array(&$this, 'register_button') );
	   }
	}
	
	function register_button( $buttons ) {
	
		array_push( $buttons, "|", "cvglink" );
	 	return $buttons;
	}

	function add_plugin( $plugin_array ) {
	   $plugin_array['cvglink'] = trailingslashit( WP_PLUGIN_URL . '/' .	dirname( plugin_basename(__FILE__))) . '/tinymce.js';
	   return $plugin_array;
	}

	/**
	 * Call TinyMCE window content via admin-ajax
	 *
	 * @return html content
	 * @author Praveen Rajan
	 */
	function cvg_ajax_tinymce() {
	
	    // check for rights
	    if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') )
	        die(__("You are not allowed to be here"));
	
	       include_once( dirname(__FILE__) . '/window.php');
	   
	    die();   
	}
}
$tinymce = new CvgTinyMCE();
?>