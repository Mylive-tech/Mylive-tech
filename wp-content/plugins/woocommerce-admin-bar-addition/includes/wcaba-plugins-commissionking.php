<?php
/**
 * Display links to active extensions specific settings' pages: WooCommerce Commission King.
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
 * WooCommerce Commission King (premium, by WooThemes)
 *
 * @since 1.8.0
 */
	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwtcommissionking' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Commission King Options', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=woo_ck' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Commission King Options', 'woocommerce-admin-bar-addition' ) )
	);

	$menu_items[ 'extwtcommissionking-commissions' ] = array(
		'parent' => $extwtcommissionking,
		'title'  => __( 'Commissions', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'edit.php?post_type=shop_commission' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Commissions', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwtcommissionking-pay' ] = array(
		'parent' => $extwtcommissionking,
		'title'  => __( 'Pay Commissions', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'edit.php?post_type=shop_commission&page=woo_ck_commission_pay' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Pay Commissions', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwtcommissionking-settings' ] = array(
		'parent' => $extwtcommissionking,
		'title'  => __( 'More Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'edit.php?post_type=shop_commission&page=woo_ck_commission_settings' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'More Settings', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Settings" level submenu */
	$menu_items[ 's_commissionking' ] = array(
		'parent' => $settings,
		'title'  => __( 'Commission King Options', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=woo_ck' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Commission King Options', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's_commissionking-commissions' ] = array(
		'parent' => $s_commissionking,
		'title'  => __( 'Commissions', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'edit.php?post_type=shop_commission' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Commissions', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's_commissionking-pay' ] = array(
		'parent' => $s_commissionking,
		'title'  => __( 'Pay Commissions', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'edit.php?post_type=shop_commission&page=woo_ck_commission_pay' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Pay Commissions', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's_commissionking-settings' ] = array(
		'parent' => $s_commissionking,
		'title'  => __( 'More Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'edit.php?post_type=shop_commission&page=woo_ck_commission_settings' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'More Settings', 'woocommerce-admin-bar-addition' )
		)
	);