<?php
/*
Plugin Name: Advanced Woocommerce Reporting System
Plugin URI: http://proword.net/Advanced_Reporting/
Description: WooCommerce Advance Reporting plugin is a comprehensive and the most complete reporting system.
Version: 1.0.0
Author: Proword.net
Author URI: http://proword.net/
Text Domain: wcx_wcreport_textdomain
Domain Path: /languages/
*/

if(!class_exists('wcx_wcreport_class')){

	//USE IN INCLUDE
	define( '__WCX_WCREPORT_ROOT_DIR__', dirname(__FILE__));
	
	//USE IN ENQUEUE AND IMAGE
	define( '__WCX_WCREPORT_CSS_URL__', plugins_url('assets/css/',__FILE__));
	define( '__WCX_WCREPORT_JS_URL__', plugins_url('assets/js/',__FILE__));
	define ('__WCX_WCREPORT_URL__',plugins_url('', __FILE__));
	
	//PERFIX
	define ('__WCX_WCREPORT_FIELDS_PERFIX__', 'custom_wcx_' );
	
	//TEXT DOMAIN FOR MULTI LANGUAGE
	define ('__WCX_WCREPORT_TEXTDOMAIN__', 'wcx_wcreport_textdomain' );
	
	//THE PLUGIN PAGES IS CREATED IN THIS FILE
	include('class/custommenu.php');
	
	//CLASS FOR ENQUEUE SCRIPTS AND STYLES
	class wcx_wcreport_class{
		function __construct(){
			add_action('wp_print_scripts',array($this,'wcx_backend_enqueue'));
		}
		function wcx_backend_enqueue(){
			include ("includes/admin-embed.php");
		}	
	}
	new wcx_wcreport_class;
}
?>