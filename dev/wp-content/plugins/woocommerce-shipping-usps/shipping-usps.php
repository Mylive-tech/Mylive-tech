<?php
/*
	Plugin Name: WooCommerce USPS Shipping
	Plugin URI: http://www.woothemes.com/products/usps-shipping-method/
	Description: Obtain shipping rates dynamically via the USPS Shipping API for your orders.
	Version: 4.2.12
	Author: WooThemes
	Author URI: http://woothemes.com

	Copyright: 2009-2011 WooThemes.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html

	https://www.usps.com/webtools/htm/Rate-Calculators-v1-5.htm
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '83d1524e8f5f1913e58889f83d442c32', '18657' );

/**
 * Check if WooCommerce is active
 */
if ( is_woocommerce_active() ) {

	/**
	 * WC_USPS class
	 */
	class WC_USPS {

		/**
		 * Constructor
		 */
		public function __construct() {
			register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'init' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'add_method' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		}

		/**
		 * Check plugin can run
		 */
		public function activation_check() {
			if ( ! function_exists( 'simplexml_load_string' ) ) {
		        deactivate_plugins( basename( __FILE__ ) );
		        wp_die( "Sorry, but you cannot run this plugin, it requires the SimpleXML library installed on your server/hosting to function." );
			}
		}

		/**
		 * Localisation
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'woocommerce-shipping-usps', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Plugin page links
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=wc_shipping_usps' ) . '">' . __( 'Settings', 'woocommerce-shipping-usps' ) . '</a>',
				'<a href="http://support.woothemes.com/">' . __( 'Support', 'woocommerce-shipping-usps' ) . '</a>',
				'<a href="http://docs.woothemes.com/document/usps-shipping/">' . __( 'Docs', 'woocommerce-shipping-usps' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Load gateway class
		 */
		public function init() {
			include_once( 'includes/class-wc-shipping-usps.php' );
		}

		/**
		 * Add method to WC
		 */
		public function add_method( $methods ) {
			$methods[] = 'WC_Shipping_USPS';
			return $methods;
		}

		/**
		 * Enqueue scripts
		 */
		public function scripts() {
			wp_enqueue_script( 'jquery-ui-sortable' );
		}
	}
	new WC_USPS();
}
