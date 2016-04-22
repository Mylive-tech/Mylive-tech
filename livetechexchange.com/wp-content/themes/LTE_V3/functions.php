<?php
/**
 * Theme Functions
 */


define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

define('THEME_NAME', 'Doover');
define('THEME_VERSION', '2.1.1');

define('LIBS_DIR', THEME_DIR. '/functions');
define('LIBS_URI', THEME_URI. '/functions');
define('LANG_DIR', THEME_DIR. '/languages');


/* ---------------------------------------------------------------------------
 * Loads Theme Textdomain
 * --------------------------------------------------------------------------- */
load_theme_textdomain( 'doover', LANG_DIR );


/* ---------------------------------------------------------------------------
 * Loads the Options Panel
 * --------------------------------------------------------------------------- */
if ( ! function_exists( 'optionsframework_init' ) ) {
	if ( STYLESHEETPATH == TEMPLATEPATH ) {
		define('OPTIONS_FRAMEWORK_URL', TEMPLATEPATH . '/admin/');
		define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/admin/');
	} else {
		define('OPTIONS_FRAMEWORK_URL', STYLESHEETPATH . '/admin/');
		define('OPTIONS_FRAMEWORK_DIRECTORY', get_stylesheet_directory_uri() . '/admin/');
	}
	require_once (OPTIONS_FRAMEWORK_URL . 'options-framework.php');
}


/* ---------------------------------------------------------------------------
 * Loads Theme Functions
 * --------------------------------------------------------------------------- */

// Functions --------------------------------------------------------------------
require_once(LIBS_DIR.'/theme-functions.php');

// Header -----------------------------------------------------------------------
require_once(LIBS_DIR.'/theme-head.php');

// Menu -------------------------------------------------------------------------
require_once(LIBS_DIR.'/theme-menu.php');

// Post types -------------------------------------------------------------------
require_once(LIBS_DIR.'/theme-posttypes.php');
require_once(LIBS_DIR.'/theme-sliderfields.php');
require_once(LIBS_DIR.'/theme-megamenufields.php');

// Shortcodes -------------------------------------------------------------------
require_once(LIBS_DIR.'/theme-shortcodes.php');

// Widgets ----------------------------------------------------------------------
require_once(LIBS_DIR.'/widget-functions.php');
require_once(LIBS_DIR.'/widget-menu.php');

// Plugins ---------------------------------------------------------------------- 
require_once(LIBS_DIR.'/plugins/dropdown-menus.php');

// TinyMCE ---------------------------------------------------------------------- 
require_once(LIBS_DIR.'/tinymce/tinymce.php');
?>