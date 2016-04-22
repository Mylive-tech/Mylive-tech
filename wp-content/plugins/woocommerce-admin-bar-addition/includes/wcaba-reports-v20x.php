<?php
/**
 * Display the settings links for WooCommerce 2.0.x branch.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Reports Links
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2011-2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.0.0
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
 * Include reports menu items for WooCommerce 2.0.x branch:
 *
 * @since 1.0.0
 */
/** Reports/ Sales */
$menu_items[ 'reports' ] = array(
	'parent' => $woocommercebar,
	'title'  => __( 'Reports', 'woocommerce-admin-bar-addition' ),
	'href'   => admin_url( 'admin.php?page=woocommerce_reports' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Reports', 'woocommerce-admin-bar-addition' )
	)
);

	/** Displaying sub-level reports tabs links */
	$menu_items[ 'reportssales' ] = array(
		'parent' => $reports,
		'title'  => __( 'Sales Overview', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=sales&chart=overview' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Sales Overview', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reportssales-byday' ] = array(
		'parent' => $reportssales,
		'title'  => __( 'Sales by Day', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=sales&chart=sales_by_day' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Sales by Day', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reportssales-bymonth' ] = array(
		'parent' => $reportssales,
		'title'  => __( 'Sales by Month', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=sales&chart=sales_by_month' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Sales by Month', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reportssales-products' ] = array(
		'parent' => $reportssales,
		'title'  => __( 'Product Sales', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=sales&chart=product_sales' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Product Sales', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reportssales-topsellers' ] = array(
		'parent' => $reportssales,
		'title'  => __( 'Top Sellers', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=sales&chart=top_sellers' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Top Sellers', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reportssales-topearners' ] = array(
		'parent' => $reportssales,
		'title'  => __( 'Top Earners', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=sales&chart=top_earners' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Top Earners', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reportssales-bycategory' ] = array(
		'parent' => $reportssales,
		'title'  => __( 'Sales by Category', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=sales&chart=sales_by_category' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Sales by Category', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reportssales-taxesbymonth' ] = array(
		'parent' => $reportssales,
		'title'  => __( 'Taxes by Month', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=sales&chart=taxes_by_month' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Taxes by Month', 'woocommerce-admin-bar-addition' )
		)
	);

/** Coupons */
$menu_items[ 'reportscoupons' ] = array(
	'parent' => $reports,
	'title'  => __( 'Coupons', 'woocommerce-admin-bar-addition' ),
	'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=coupons' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Coupons', 'woocommerce-admin-bar-addition' )
	)
);

	$menu_items[ 'reportscoupons-overview' ] = array(
		'parent' => $reportscoupons,
		'title'  => __( 'Overview', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=coupons&chart=overview' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Overview', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'reportscoupons-discountbycoupon' ] = array(
		'parent' => $reportscoupons,
		'title'  => __( 'Discounts by Coupon', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=coupons&chart=discounts_by_coupon' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Discounts by Coupon', 'woocommerce-admin-bar-addition' )
		)
	);

/** Customers */
$menu_items[ 'reports-customers' ] = array(
	'parent' => $reports,
	'title'  => __( 'Customers', 'woocommerce-admin-bar-addition' ),
	'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=customers' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Customers', 'woocommerce-admin-bar-addition' )
	)
);

/** Stock */
$menu_items[ 'reports-stock' ] = array(
	'parent' => $reports,
	'title'  => __( 'Stock', 'woocommerce-admin-bar-addition' ),
	'href'   => admin_url( 'admin.php?page=woocommerce_reports&tab=stock' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Stock', 'woocommerce-admin-bar-addition' )
	)
);