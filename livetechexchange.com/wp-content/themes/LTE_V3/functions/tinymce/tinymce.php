<?php
define('PLUGIN_NAME', 'doover_mce');
define('PLUGIN_URI', LIBS_URI .'/tinymce/');

function doover_mce_version( $version ) 
{
	return $version + 100;
}	
add_filter('tiny_mce_version', 'doover_mce_version' );

function doover_mce_init() 
{
	global $page_handle;
	if ( ! current_user_can('edit_posts') || ! current_user_can('edit_pages') )  return;
	
	if ( get_user_option('rich_editing') == 'true') 
	{
		add_filter("mce_external_plugins", 'doover_mce_plugin' );
		add_filter('mce_buttons', 'doover_mce_buttons' );
		add_filter('mce_external_languages', 'doover_mce_languages');
	}
}
add_action( 'init', 'doover_mce_init' );

function doover_mce_plugin( $array ) 
{
	$path = LIBS_URI .'/tinymce/'; 	
	$array['doover_mce'] =  PLUGIN_URI .'plugin.js';
	return $array;
}

function doover_mce_buttons( $buttons ) 
{
	array_push($buttons, 'separator', 'doover_mce' );
	return $buttons;
}

function doover_mce_languages( $array ) 
{	
	$array['doover_mce'] = PLUGIN_URI .'/langs.php';
	return $array;
}
?>