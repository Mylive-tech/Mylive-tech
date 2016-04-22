<?php
/**
 * Per Product Shipping Uninstall
 */
if ( ! defined('WP_UNINSTALL_PLUGIN') ) {
	exit();
}

global $wpdb;

// Tables
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "woocommerce_per_product_shipping_rules" );