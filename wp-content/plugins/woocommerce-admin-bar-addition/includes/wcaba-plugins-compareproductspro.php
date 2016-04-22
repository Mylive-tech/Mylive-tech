<?php
/**
 * Display links to active extensions specific settings' pages: WooCommerce Compare Products Pro.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2011-2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * @link       http://deckerweb.de/twitter
 *
 * @since      2.3.0
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
 * WooCommerce Compare Products Pro (premium, by A3 Revolution Software Development)
 *
 * Note: Plugin version 1.0.0 or higher required!
 *
 * @since 2.3.0
 */
	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwccppro' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Compare Products Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Compare Products Settings', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwccppro-categories' ] = array(
		'parent' => $extwccppro,
		'title'  => __( 'Features/ Categories', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings&tab=features' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Features/ Categories', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwccppro-features' ] = array(
		'parent' => $extwccppro,
		'title'  => __( 'Products Manager', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings&tab=compare-products' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Products Manager', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Products" level submenu */
	$menu_items[ 'p_compareproductspro' ] = array(
		'parent' => $products,
		'title'  => __( 'Compare Products Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Compare Products Settings', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'p_compareproductspro-categories' ] = array(
		'parent' => $p_compareproductspro,
		'title'  => __( 'Features/ Categories', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings&tab=features' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Features/ Categories', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'p_compareproductspro-features' ] = array(
		'parent' => $p_compareproductspro,
		'title'  => __( 'Products Manager', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings&tab=compare-products' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Products Manager', 'woocommerce-admin-bar-addition' )
		)
	);