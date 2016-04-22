<?php
/*
Plugin Name: WooCommerce WooSales Mobile
Plugin URI: http://woosalesmobile.com/
Description: This plugin extends your WooCommerce default reports onto your Android or iOS device.
Version: 2.9.5
Author: Southern Cross Global Consulting
Author URI: http://www.oceanicbreeze.com
*/
ob_start();


define( 'EDD_WSM_STORE_URL', 'https://scglobal.com.au' );
define( 'EDD_WSM_ITEM_NAME', 'WooCommerce WooSales Mobile' ); 

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/admin/EDD_SL_Plugin_Updater.php' );
}

function edd_sl_wsm_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'edd_wsm_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( EDD_WSM_STORE_URL, __FILE__, array(
			'version' 	=> '2.9.5', 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => EDD_WSM_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'Southern Cross Global Consulting'  // author of this plugin
		)
	);

}
add_action( 'admin_init', 'edd_sl_wsm_plugin_updater', 0 );



require_once (plugin_dir_path( __FILE__ ).'/admin/wp-init.php');
// Settings Link
add_filter('plugin_action_links', 'woosales_plugin_action_links', 10, 2);
function woosales_plugin_action_links($links, $file) {
    static $this_plugin;
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
    if ($file == $this_plugin) {
        $settings_link = '<a href="' . site_url() . '/wp-admin/admin.php?page=woosales">Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}








/*
add_action( 'woocommerce_order_status_pending',     'woosalesnotification');
add_action( 'woocommerce_order_status_failed',      'woosalesnotification');
add_action( 'woocommerce_order_status_on-hold',     'woosalesnotification');
add_action( 'woocommerce_order_status_processing',  'woosalesnotification');
add_action( 'woocommerce_order_status_completed',   'woosalesnotification');
add_action( 'woocommerce_order_status_refunded',    'woosalesnotification');
add_action( 'woocommerce_order_status_cancelled',   'woosalesnotification');


function woosalesnotification ($order_id) {

	$order = new WC_Order($order_id);
	$wsm_order_id = $order_id;
	$wsm_order_currency = $order->get_order_currency();
	$formatter = new NumberFormatter('en_US',  NumberFormatter::CURRENCY);
	$wsm_order_total = $formatter->formatCurrency($order->get_total(), $wsm_order_currency);
	$wsm_order_status = $order->get_status();
	
	
	$fields = array
	(
	    'wsm_id'  => 'woosales',
	    'wsm_domain'              => $_SERVER['HTTP_HOST'],
	    'wsm_device_id'  => get_option('woosales_deviceid'),
	    'wsm_order_id'              => $wsm_order_id,
	    'wsm_order_total'              => $wsm_order_total,
	    'wsm_order_status'              => $wsm_order_status
	);
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'http://oceanicbreeze.com/woosalesmobile/' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS,  $fields );
	$result = curl_exec($ch );
	curl_close( $ch ); 

}
*/









// WooCommerce is active?
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {










}

