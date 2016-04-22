<?php
/**
 * Helper functions for custom branding & capabilities
 * Plus: Integrate "WooCommerce Branding" settings into Admin Bar Addition stuff.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Branding & Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2012-2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * @link       http://deckerweb.de/twitter
 *
 * @since      2.2.0
 */

/**
 * Prevent direct access to this file.
 *
 * @since 2.6.0
 */
if ( ! defined( 'WPINC' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Helper functions for returning a few popular roles/capabilities.
 *
 * @since  2.2.0
 *
 * @return role/capability
 */
	/**
	 * Helper function for returning 'administrator' role/capability.
	 *
	 * @since  2.2.0
	 *
	 * @return 'administrator' role
	 */
	function __wcaba_admin_only() {

		return 'administrator';
	}

	/**
	 * Helper function for returning WooCommerce specific 'shop_manager' role/capability.
	 *
	 * @since  2.2.0
	 *
	 * @return 'shop_manager' role
	 */
	function __wcaba_role_shop_manager() {

		return 'shop_manager';
	}

	/**
	 * Helper function for returning 'editor' role/capability.
	 *
	 * @since  2.2.0
	 *
	 * @return 'editor' role
	 */
	function __wcaba_role_editor() {

		return 'editor';
	}

	/**
	 * Helper function for returning 'manage_options' capability.
	 *
	 * @since  2.2.0
	 *
	 * @return 'manage_options' capability
	 */
	function __wcaba_cap_manage_options() {

		return 'manage_options';
	}

	/**
	 * Helper function for returning 'install_plugins' capability.
	 *
	 * @since  2.2.0
	 *
	 * @return 'install_plugins' capability
	 */
	function __wcaba_cap_install_plugins() {

		return 'install_plugins';
	}


/**
 * Helper functions for returning colored icons.
 *
 * @since  2.2.0
 *
 * @return colored icon image
 */
	/**
	 * Helper function for returning the blue icon.
	 *
	 * @since  2.2.0
	 *
	 * @return blue icon
	 */
	function __wcaba_blue_icon() {

		return plugins_url( 'images/wcaba-icon-blue.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the green icon.
	 *
	 * @since  2.2.0
	 *
	 * @return green icon
	 */
	function __wcaba_green_icon() {

		return plugins_url( 'images/wcaba-icon-green.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the grey icon.
	 *
	 * @since  2.2.0
	 *
	 * @return grey icon
	 */
	function __wcaba_grey_icon() {

		return plugins_url( 'images/wcaba-icon-grey.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the orange icon.
	 *
	 * @since  2.2.0
	 *
	 * @return orange icon
	 */
	function __wcaba_orange_icon() {

		return plugins_url( 'images/wcaba-icon-orange.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the pink icon.
	 *
	 * @since  2.2.0
	 *
	 * @return pink icon
	 */
	function __wcaba_pink_icon() {

		return plugins_url( 'images/wcaba-icon-pink.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the red icon.
	 *
	 * @since  2.2.0
	 *
	 * @return red icon
	 */
	function __wcaba_red_icon() {

		return plugins_url( 'images/wcaba-icon-red.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the teal icon.
	 *
	 * @since  2.2.0
	 *
	 * @return teal icon
	 */
	function __wcaba_teal_icon() {

		return plugins_url( 'images/wcaba-icon-teal.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning a custom icon (icon-wcaba.png) from stylesheet/child/theme "images" folder.
	 *
	 * @since  2.3.0
	 *
	 * @return wcaba custom icon
	 */
	function __wcaba_theme_images_icon() {

		return get_stylesheet_directory_uri() . '/images/icon-wcaba.png';
	}


/**
 * Helper functions for returning icon class.
 *
 * @since  2.2.0
 *
 * @return icon class
 */
	/**
	 * Helper function for returning no icon class.
	 *
	 * @since  2.2.0
	 *
	 * @return no icon class
	 */
	function __wcaba_no_icon_display() {

		return NULL;
	}


/**
 * Special "WooCommerce Branding" extension stuff/ integration.
 *
 * @since 2.2.0
 */
if ( in_array( 'woocommerce-branding/woocommerce-branding.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
) {

	add_filter( 'wcaba_filter_main_icon', 'ddw_wcaba_wcbranding_icon' );
	/**
	 * If set, replace main item icon with custom icon url from "WooCommerce Branding" extension setting
	 *
	 * If no icon URL is set, apply pre-selected colored icon style
	 * from "WooCommerce Branding" extension to our main item icon
	 *
	 * @since  2.2.0
	 *
	 * @return icon image
	 */
	function ddw_wcaba_wcbranding_icon() {

		/** Get the custom icon url setting */
		$wcaba_wcbranding_icon_url = get_option( 'woocommerce_branding_icon' );

		/** Get the icon color setting */
		$wcaba_wcbranding_color = get_option( 'woocommerce_branding_colour' );

		/** Apply the custom icon url */
		if ( '' != $wcaba_wcbranding_icon_url ) {
			return '"' . $wcaba_wcbranding_icon_url . '"';
		/** Apply proper icon color setting plus our icon */
		} elseif ( 'blue' == $wcaba_wcbranding_color && $wcaba_wcbranding_icon_url == '' ) {
			return __wcaba_blue_icon();
		} elseif ( 'green' == $wcaba_wcbranding_color && $wcaba_wcbranding_icon_url == '' ) {
			return __wcaba_green_icon();
		} elseif ( 'grey' == $wcaba_wcbranding_color && $wcaba_wcbranding_icon_url == '' ) {
			return __wcaba_grey_icon();
		} elseif ( 'orange' == $wcaba_wcbranding_color && $wcaba_wcbranding_icon_url == '' ) {
			return __wcaba_orange_icon();
		} elseif ( 'pink' == $wcaba_wcbranding_color && $wcaba_wcbranding_icon_url == '' ) {
			return __wcaba_pink_icon();
		} elseif ( 'red' == $wcaba_wcbranding_color && $wcaba_wcbranding_icon_url == '' ) {
			return __wcaba_red_icon();
		} elseif ( 'teal' == $wcaba_wcbranding_color && $wcaba_wcbranding_icon_url == '' ) {
			return __wcaba_teal_icon();
		} elseif ( '' == $wcaba_wcbranding_color && $wcaba_wcbranding_icon_url == '' ) {
			return plugins_url( 'images/wcaba-icon.png', dirname( __FILE__ ) );
		}  // end-if icon check

	}  // end of function ddw_wcaba_wcbranding_icon


	add_filter( 'wcaba_filter_main_item', 'ddw_wcaba_wcbranding_main_item_name' );
	add_filter( 'wcaba_filter_main_item_tooltip', 'ddw_wcaba_wcbranding_main_item_name' );
	add_filter( 'wcaba_filter_woocommerce_name', 'ddw_wcaba_wcbranding_main_item_name' );
	add_filter( 'wcaba_filter_woocommerce_name_tooltip', 'ddw_wcaba_wcbranding_main_item_name' );
	/**
	 * Replace main item name & tooltip with custom name set in "WooCommerce Branding" extension setting
	 * Also, replace certain occurences of "WooCommerce" within menu items with custom branding name
	 *
	 * @since  2.2.0
	 *
	 * @return text string
	 */
	function ddw_wcaba_wcbranding_main_item_name() {

		/** Get custom name setting */
		$wcaba_wcbranding_name = get_option( 'woocommerce_branding_name' );

		/** Apply custom name setting */
		if ( '' != $wcaba_wcbranding_name ) {

			return $wcaba_wcbranding_name;

		}  // end if

	}  // end of function ddw_wcaba_wcbranding_main_item_name

}  // end-if WC Branding conditional