<?php

/* Global Theme Options variable */
global $flatsome_opt;
$flatsome_opt = $smof_data;


/* Check if WooCommerce is active */
function ux_is_woocommerce_active(){
	$plugin = 'woocommerce/woocommerce.php';
	$network_active = false;
	if ( is_multisite() ) {
		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[$plugin] ) ) {
			$network_active = true;
		}
	}
	return in_array( $plugin, get_option( 'active_plugins' ) ) || $network_active;
	if(class_exists( 'WooCommerce' )){ return true; }
}


/* Check if WooCommerce is Active */
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		return class_exists( 'woocommerce' ) ? true : false;
	}
}


/* Check if Extensions Exists */
if ( ! function_exists( 'is_extension_activated' ) ) {
	function is_extension_activated( $extension ) {
		return class_exists( $extension ) ? true : false;
	}
}