<?php
/*
Plugin Name: Broken Link Manager
Plugin URI: https://wordpress.org/plugins/broken-link-manager
Description: WBLM -> Wordpress Broken Link Manager. This plugin helps you check, organise and monitor your broken backlinks.
Version: 0.6.5
Author: Hüseyin Kocak
Author URI: http://k-78.de
Text Domain: broken-link-manager

WordPress Broken Link Manager Plugin
Copyright (C) 2014, K78 - info@k-78.de

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$get_wblm = get_plugin_data(__FILE__);
if(!defined('WBLM_PLUGIN_PATH')) {
	define( 'WBLM_PLUGIN_PATH', trailingslashit( dirname( __FILE__ ) ) );
}
if(!defined('WBLM_VERSION')) {
	define( 'WBLM_VERSION', $get_wblm['Version'] );
}
if(!defined('WBLM_CONFIG_PATH')) {
	define( 'WBLM_CONFIG_PATH', WBLM_PLUGIN_PATH . 'config/' );
}
if(!defined('WBLM_PLUGIN_URL')) {
	define( 'WBLM_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
}
if(!defined('WBLM_DIRNAME')) {
	define( 'WBLM_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
}
if(!defined('WBLM_NAME')) {
	define( 'WBLM_NAME', strtoupper($get_wblm['Name']) );
}
if(!defined('WBLM_ICON')) {
	define( 'WBLM_ICON', $get_wblm['AuthorURI'].'/wblm/icon.png');
}
global $wpdb;
if(!defined('TABLE_WBLM')) {
	define( 'TABLE_WBLM', $wpdb->prefix . 'wblm' );
}
if(!defined('TABLE_WBLM_LOG')) {
	define( 'TABLE_WBLM_LOG', $wpdb->prefix . 'wblm_log' );
}
if(get_option('wblm_mysql_ver')){
	define( 'MYSQL_VER', get_option('wblm_mysql_ver'));
}else{
	add_option( 'wblm_mysql_ver', '3', '', 'yes' );
	define( 'MYSQL_VER', get_option('wblm_mysql_ver'));
}

$settingsSaveFunc  = isset($_GET['settingsSave']) ? 'on' : null;
$editURLFunc  = isset($_GET['editURL']) ? 'on' : null;
$addURLFunc  = isset($_GET['addURL']) ? 'on' : null;
$delURLFunc  = isset($_GET['delURL']) ? 'on' : null;
$emptyLOGFunc  = isset($_GET['emptyLOG']) ? 'on' : null;
$emptyLOGStatu  = isset($_GET['emptyLOGStatu']) ? 'on' : null;
$emptyBrokenUrlsFunc  = isset($_GET['emptyBrokenUrls']) ? 'on' : null;

include WBLM_CONFIG_PATH . 'functions.php';

add_action( 'plugins_loaded', 'wblm_textdomain' );
function wblm_textdomain() {
  load_plugin_textdomain( 'wblm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}

function add_standart_stylesheet() {
    wp_enqueue_style( 'wblm-bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ) );
    wp_enqueue_style( 'wblm-font-awesome-4.2.0', plugins_url( '/font-awesome-4.2.0/css/font-awesome.min.css', __FILE__ ) );
    wp_enqueue_style( 'wblm-style', plugins_url( '/css/style.css', __FILE__ ) );
}
function add_dashboard_stylesheet() {
    wp_enqueue_style( 'wblm-morris', plugins_url( '/css/plugins/morris.css', __FILE__ ) );
}
function add_standart_script() {
    wp_enqueue_script( 'wblm-bootstrap', plugins_url( '/js/bootstrap.min.js', __FILE__ ), array('jquery'), null, true );
}
function add_dashboard_script() {
    wp_enqueue_script( 'wblm-raphael', plugins_url( '/js/plugins/morris/raphael.min.js', __FILE__ ), array('jquery', 'wblm-bootstrap'), null, true );
    wp_enqueue_script( 'wblm-morris', plugins_url( '/js/plugins/morris/morris.min.js', __FILE__ ), array('jquery', 'wblm-bootstrap', 'wblm-raphael'), null, true );
    wp_enqueue_script( 'wblm-dashboard-data', plugins_url( '/js/dashboard.php', __FILE__ ), array('jquery', 'wblm-bootstrap', 'wblm-raphael', 'wblm-morris'), null, true );
}
function menuDashboardFunc(){
    include 'wblm-dashboard.php';
}
function menuRedirectUrlFunc(){
    include 'wblm-redirect-url.php';
}
function menuBrokenUrlFunc(){
    include 'wblm-broken-url.php';
}
function menuSettingsFunc(){
    include 'wblm-settings.php';
}
function menuEditUrlFunc(){
    include 'wblm-url-edit.php';
}
function menuAddUrlFunc(){
    include 'wblm-url-add.php';
}
function menuLogFunc(){
    include 'wblm-url-log.php';
}
/*************************************************************************************
 *	DATABANKS
 *************************************************************************************/
function create_wblm_table(){
	global $wpdb;
		
	$sql_wblm = "CREATE TABLE IF NOT EXISTS " . TABLE_WBLM . " (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`old_url` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`new_url` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`http_statu` INT NOT NULL ,
	`type` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`hit` INT NOT NULL,
	`active` TINYINT NOT NULL
	) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ";
	$wpdb->query($sql_wblm);
	    
	$sql_wblm_log = "CREATE TABLE IF NOT EXISTS " . TABLE_WBLM_LOG . " (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`url` INT ,
	`date` DATETIME NOT NULL,
	`domain` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`referer` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`useragent` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`ip` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`redirect` TINYINT NOT NULL,
	`broken` TINYINT NOT NULL,
	`http_statu` INT NOT NULL
	) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ";
	$wpdb->query($sql_wblm_log);
}
if (MYSQL_VER < 3){
	$sql_wblm_add_http_statu = "ALTER TABLE `". TABLE_WBLM ."` ADD `http_statu` INT NULL AFTER `type`";
	$sql_wblm_log_add_domain = "ALTER TABLE `". TABLE_WBLM_LOG ."` ADD `domain` VARCHAR(200) NOT NULL AFTER `date`";
	$wpdb->query($sql_wblm_add_http_statu);
	$wpdb->query($sql_wblm_log_add_domain);
	update_option( wblm_mysql_ver, '3' );
	define( 'MYSQL_VER', get_option('wblm_mysql_ver'));
}	
/*************************************************************************************
 *	LOG PATH (SIMDILIK SADECE KLASOR OLUSTURULUYOR)
 *************************************************************************************/
	$log_dir = WBLM_PLUGIN_PATH.'/log';
	if (!file_exists($log_dir)) { 
	$golustur = mkdir($log_dir, 0777);
	chmod($log_dir, 0777);
	}else {  
	}
if(!defined('WBLM_LOG_URL')) {
	define( 'WBLM_LOG_URL', WBLM_PLUGIN_URL.'/log' );
}
/*************************************************************************************
 *	OPTIONS
 *************************************************************************************/
if(get_option('wblm_send_email')){
	define( 'SEND_EMAIL', get_option('wblm_send_email'));
}else{
	add_option( 'wblm_send_email', '', '', 'yes' );
	define( 'SEND_EMAIL', get_option('wblm_send_email'));
}
if(get_option('wblm_save_broken_urls')){
	define( 'SAVE_BROKEN_URLS', get_option('wblm_save_broken_urls'));
}else{
	add_option( 'wblm_save_broken_urls', 'on', '', 'yes' );
	define( 'SAVE_BROKEN_URLS', get_option('wblm_save_broken_urls'));
}
if(get_option('wblm_save_url_stats')){
	define( 'SAVE_URL_STATS', get_option('wblm_save_url_stats'));
}else{
	add_option( 'wblm_save_url_stats', 'on', '', 'yes' );
	define( 'SAVE_URL_STATS', get_option('wblm_save_url_stats'));
}
if(get_option('wblm_save_url_log')){
	define( 'SAVE_URL_LOG', get_option('wblm_save_url_log'));
}else{
	add_option( 'wblm_save_url_log', 'on', '', 'yes' );
	define( 'SAVE_URL_LOG', get_option('wblm_save_url_log'));
}
if(get_option('wblm_from_email')){
	define( 'FROM_EMAIL', get_option('wblm_from_email'));
}else{
	add_option( 'wblm_from_email', get_option('admin_email'), '', 'yes' );
	define( 'FROM_EMAIL', get_option('wblm_from_email'));
}
if(get_option('wblm_to_email')){
	define( 'TO_EMAIL', get_option('wblm_to_email'));
}else{
	add_option( 'wblm_to_email', get_option('admin_email'), '', 'yes' );
	define( 'TO_EMAIL', get_option('wblm_to_email'));
}
if(get_option('wblm_cc_email')){
	define( 'CC_EMAIL', get_option('wblm_cc_email'));
}else{
	add_option( 'wblm_cc_email', '', '', 'yes' );
	define( 'CC_EMAIL', '');
}
if(get_option('wblm_bcc_email')){
	define( 'BCC_EMAIL', get_option('wblm_bcc_email'));
}else{
	add_option( 'wblm_bcc_email', '', '', 'yes' );
	define( 'BCC_EMAIL', '');
}
if(get_option('wblm_redirect_default_url')){
	define( 'REDIRECT_DEFAULT_URL', get_option('wblm_redirect_default_url'));
}else{
	add_option( 'wblm_redirect_default_url', '', '', 'yes' );
	define( 'REDIRECT_DEFAULT_URL', get_option('wblm_redirect_default_url'));
}
if(get_option('wblm_default_url')){
	define( 'DEFAULT_URL', get_option('wblm_default_url'));
}else{
	add_option( 'wblm_default_url', get_home_url(), '', 'yes' );
	define( 'DEFAULT_URL', get_option('wblm_default_url'));
}
register_activation_hook( __FILE__, 'create_wblm_table' );
/*************************************************************************************
 *	FUNCTIONS
 *************************************************************************************/
add_action('template_redirect', '_custom_redirect');

function createBaclinksMenu() {
    $menu_wblm_dashboard = add_menu_page("Broken Backlinks", "Broken Backlinks", 'manage_options', "wblm-dashboard", "menuDashboardFunc", WBLM_ICON);
    $menu_wblm_redirecturl = add_submenu_page("wblm-dashboard", "Redirected URLs", "Redirected URLs", 'manage_options', "wblm-redirect", "menuRedirectUrlFunc");
    $menu_wblm_brokenurl = add_submenu_page("wblm-dashboard", "Broken URLs", "Broken URLs", 'manage_options', "wblm-broken", "menuBrokenUrlFunc");
    $menu_wblm_log = add_submenu_page("wblm-dashboard", "URLs Log", "URLs Log", 'manage_options', "wblm-log", "menuLogFunc");
    $menu_wblm_addurl = add_submenu_page("wblm-dashboard", "Add URL", "Add URL", 'manage_options', "wblm-add-url", "menuAddUrlFunc");    
    $menu_wblm_settings = add_submenu_page("wblm-dashboard", "Settings", "Settings", 'manage_options', "wblm-settings", "menuSettingsFunc");    
    $menu_wblm_editurl = add_submenu_page("wblm-settings", "Edit URL", "Edit URL", 'manage_options', "wblm-edit-url", "menuEditUrlFunc");
    add_action( 'admin_print_styles-' . $menu_wblm_dashboard, 'add_standart_stylesheet' );
    add_action( 'admin_print_styles-' . $menu_wblm_dashboard, 'add_dashboard_stylesheet' );
	add_action( 'admin_print_styles-' . $menu_wblm_redirecturl, 'add_standart_stylesheet' );	
	add_action( 'admin_print_styles-' . $menu_wblm_brokenurl, 'add_standart_stylesheet' );
	add_action( 'admin_print_styles-' . $menu_wblm_log, 'add_standart_stylesheet' );
	add_action( 'admin_print_styles-' . $menu_wblm_addurl, 'add_standart_stylesheet' );
	add_action( 'admin_print_styles-' . $menu_wblm_settings, 'add_standart_stylesheet' );
	add_action( 'admin_print_styles-' . $menu_wblm_editurl, 'add_standart_stylesheet' );
    add_action( 'admin_print_scripts-' . $menu_wblm_dashboard, 'add_standart_script' );
    add_action( 'admin_print_scripts-' . $menu_wblm_dashboard, 'add_dashboard_script' );  
    add_action( 'admin_print_scripts-' . $menu_wblm_redirecturl, 'add_standart_script' );  
    add_action( 'admin_print_scripts-' . $menu_wblm_brokenurl, 'add_standart_script' ); 
    add_action( 'admin_print_scripts-' . $menu_wblm_log, 'add_standart_script' );    
    add_action( 'admin_print_scripts-' . $menu_wblm_addurl, 'add_standart_script' );
    add_action( 'admin_print_scripts-' . $menu_wblm_settings, 'add_standart_script' );
    add_action( 'admin_print_scripts-' . $menu_wblm_editurl, 'add_standart_script' );  
    add_action( "load-$menu_wblm_brokenurl", 'add_brokenurl_options' );
    add_action( "load-$menu_wblm_redirecturl", 'add_redirected_options' );
    add_action( "load-$menu_wblm_log", 'add_log_options' );
}
function add_brokenurl_options() {
require_once( WBLM_CONFIG_PATH . 'class/broken_url.php');

  global $WblmListTable;
  $option = 'per_page';
  $args = array(
         'label' => 'Urls',
         'default' => 10,
         'option' => 'per_page'
         );
  add_screen_option( $option, $args );
  $WblmListTable = new wblm_List_Table();
}
function add_redirected_options() {
require_once( WBLM_CONFIG_PATH . 'class/redirected_url.php');
  global $WblmListTable;
  $option = 'per_page';
  $args = array(
         'label' => 'Urls',
         'default' => 10,
         'option' => 'per_page'
         );
  add_screen_option( $option, $args );
  $WblmListTable = new wblm_List_Table();
}
function add_log_options() {
require_once( WBLM_CONFIG_PATH . 'class/log_url.php');
  global $WblmListTable;
  $option = 'per_page';
  $args = array(
         'label' => 'Urls',
         'default' => 10,
         'option' => 'per_page'
         );
  add_screen_option( $option, $args );
  $WblmListTable = new wblm_List_Table();
}
add_action("admin_menu", "createBaclinksMenu");