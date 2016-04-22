<?php
/**
 * Display order status links if theme support is enabled.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Order Status
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
 * Display order status links for quicker access.
 *
 * Will only be displayed if theme support is added via
 *    "add_theme_support( 'wcaba-order-status' );" to active theme/ child theme.
 *
 * @since 2.2.0
 */
/** Get order status counts */
$wcaba_pending_count    = get_term_by( 'slug', 'pending', 'shop_order_status' )->count;
$wcaba_on_hold_count    = get_term_by( 'slug', 'on-hold', 'shop_order_status' )->count;
$wcaba_processing_count = get_term_by( 'slug', 'processing', 'shop_order_status' )->count;
$wcaba_completed_count  = get_term_by( 'slug', 'completed', 'shop_order_status' )->count;
$wcaba_refunded_count   = get_term_by( 'slug', 'refunded', 'shop_order_status' )->count;
$wcaba_cancelled_count  = get_term_by( 'slug', 'cancelled', 'shop_order_status' )->count;
$wcaba_failed_count     = get_term_by( 'slug', 'failed', 'shop_order_status' )->count;

/** Display order status links */
$menu_items[ 'os-pending' ] = array(
	'parent' => $orders,
	'title'  => __( 'Pending Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_pending_count ) . ')',
	'href'   => admin_url( 'edit.php?post_type=shop_order&shop_order_status=pending' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Pending Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_pending_count ) . ')'
	)
);

$menu_items[ 'os-onhold' ] = array(
	'parent' => $orders,
	'title'  => __( 'On-Hold Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_on_hold_count ) . ')',
	'href'   => admin_url( 'edit.php?post_type=shop_order&shop_order_status=on-hold' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'On-Hold Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_on_hold_count ) . ')'
	)
);

$menu_items[ 'os-processing' ] = array(
	'parent' => $orders,
	'title'  => __( 'Processing Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_processing_count ) . ')',
	'href'   => admin_url( 'edit.php?post_type=shop_order&shop_order_status=processing' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Processing Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_processing_count ) . ')'
	)
);

$menu_items[ 'os-completed' ] = array(
	'parent' => $orders,
	'title'  => __( 'Completed Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_completed_count ) . ')',
	'href'   => admin_url( 'edit.php?post_type=shop_order&shop_order_status=completed' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Completed Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_completed_count ) . ')'
	)
);

$menu_items[ 'os-refunded' ] = array(
	'parent' => $orders,
	'title'  => __( 'Refunded Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_refunded_count ) . ')',
	'href'   => admin_url( 'edit.php?post_type=shop_order&shop_order_status=refunded' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Refunded Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_refunded_count ) . ')'
	)
);

$menu_items[ 'os-cancelled' ] = array(
	'parent' => $orders,
	'title'  => __( 'Cancelled Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_cancelled_count ) . ')',
	'href'   => admin_url( 'edit.php?post_type=shop_order&shop_order_status=cancelled' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Cancelled Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_cancelled_count ) . ')'
	)
);

$menu_items[ 'os-failed' ] = array(
	'parent' => $orders,
	'title'  => __( 'Failed Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_failed_count ) . ')',
	'href'   => admin_url( 'edit.php?post_type=shop_order&shop_order_status=failed' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Failed Orders', 'woocommerce-admin-bar-addition' ) . ' (' . number_format_i18n( $wcaba_failed_count ) . ')'
	)
);