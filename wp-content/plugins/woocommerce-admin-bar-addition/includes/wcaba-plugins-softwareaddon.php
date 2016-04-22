<?php
/**
 * Display links to active extensions specific settings' pages: WooCommerce Software Add-On.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2011-2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * @link       http://deckerweb.de/twitter
 *
 * @since      2.4.0
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
 * WooCommerce Software Add-On (premium, by WooThemes)
 *
 * @since 2.4.0
 */

	/** Entries at "Extensions" level submenu */
	$menu_items[ 'extwtsoftwareaddon' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Software: Licence Keys', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_software_keys' ),
		'meta'   => array(
			'target' => '',
			'title'  => _x( 'Licence Keys (Software Add-On)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwtsoftwareaddon-reports-sales' ] = array(
		'parent' => $extwtsoftwareaddon,
		'title'  => __( 'Sales Reports', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=software&chart=0' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Sales Reports', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwtsoftwareaddon-reports-activations' ] = array(
		'parent' => $extwtsoftwareaddon,
		'title'  => __( 'Software Activations', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=software&chart=1' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Software Activations', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Products" level submenu */
	$menu_items[ 'p_softwareaddon' ] = array(
		'parent' => $products,
		'title'  => __( 'Software Licence Keys', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_software_keys' ),
		'meta'   => array(
			'target' => '',
			'title'  => _x( 'Licence Keys (Software Add-On)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entries at "Reports" level submenu */
	$menu_items[ 'reports-software-sales' ] = array(
		'parent' => $reports,
		'title'  => __( 'Software Sales', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=software&chart=0' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Software Sales', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reports-software-activations' ] = array(
		'parent' => $reports,
		'title'  => __( 'Software Activations', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=software&chart=1' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Software Activations', 'woocommerce-admin-bar-addition' )
		)
	);