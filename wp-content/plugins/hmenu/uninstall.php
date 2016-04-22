<?php

	#UNINSTALL PLUGIN
	if(!defined('WP_UNINSTALL_PLUGIN')){
		exit();
	}
	
	//access globals
	global $wpdb;
	
	//flag deleted
	$wpdb->query("UPDATE `". $wpdb->base_prefix ."hplugin_root` SET `deleted` = 1 WHERE `plugin_name` = 'hmenu';"); //flag deleted
	
	//clean up
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_presets`;"); //color presets
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_font_icons`;"); //font icons
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_font_pack`;"); //font pack
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_social`;"); //social
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_search`;"); //search
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_product`;"); //product
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_map`;"); //map
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_list_items`;"); //list items
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_list`;"); //list
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_image`;"); //image
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_content`;"); //content
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_contact_fields`;"); //contact fields
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_contact`;"); //contact
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_blog`;"); //blog
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_menu`;"); //mega menu
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_nav_items`;"); //nav items
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_cart`;"); //cart
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_font_styles`;"); //mega font styles
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mobile_styles`;"); //mobile styles
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_styles`;"); //mega styles
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_dropdown_styles`;"); //dropdown styles
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_main_styles`;"); //main styles
	$wpdb->query("DROP TABLE IF EXISTS `". $wpdb->base_prefix ."hmenu_menu`;"); //menu