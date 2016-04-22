<?php
/**
 * Display links to active extensions specific settings' pages: WooCommerce Dynamic Pricing.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2011-2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.8.0
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
 * WooCommerce Dynamic Pricing (premium, by WooThemes)
 *
 * @since 1.8.0
 */
	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwtdynpricing' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Dynamic Pricing - Roles', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_dynamic_pricing&tab=roles' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Dynamic Pricing - Roles', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwt-dynpricing-cat' ] = array(
		'parent' => $extwtdynpricing,
		'title'  => __( 'Category Pricing', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_dynamic_pricing&tab=category' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Category Pricing', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwt-dynpricing-cat-adv' ] = array(
		'parent' => $extwtdynpricing,
		'title'  => __( 'Advanced Category Pricing', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_dynamic_pricing&tab=category&view=1' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Advanced Category Pricing', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Settings" level submenu */
	$menu_items[ 's_dynpricing' ] = array(
		'parent' => $settings,
		'title'  => __( 'Dynamic Pricing - Roles', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_dynamic_pricing&tab=roles' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Dynamic Pricing - Roles', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's_dynpricing-cat' ] = array(
		'parent' => $s_dynpricing,
		'title'  => __( 'Category Pricing', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_dynamic_pricing&tab=category' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Category Pricing', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's_dynpricing-cat-adv' ] = array(
		'parent' => $s_dynpricing,
		'title'  => __( 'Advanced Category Pricing', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_dynamic_pricing&tab=category&view=1' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Advanced Category Pricing', 'woocommerce-admin-bar-addition' )
		)
	);