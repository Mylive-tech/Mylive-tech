<?php
/**
 * Display the settings links for WooCommerce 2.1.x branch.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Reports Links
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * @link       http://deckerweb.de/twitter
 *
 * @since      2.7.0
 */

/**
 * Prevent direct access to this file.
 *
 * @since 2.7.0
 */
if ( ! defined( 'WPINC' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Include reports menu items for WooCommerce 2.1.x branch:
 *
 * @since 2.7.0
 */
/** Reports */
$menu_items[ 'reports' ] = array(
	'parent' => $woocommercebar,
	'title'  => __( 'Reports', 'woocommerce-admin-bar-addition' ),
	'href'   => admin_url( 'admin.php?page=wc-reports' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Reports', 'woocommerce-admin-bar-addition' )
	)
);

	/** Orders & Sales */
	$menu_items[ 'reportssales' ] = array(
		'parent' => $reports,
		'title'  => __( 'Orders &amp; Sales', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Orders &amp; Sales', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'reportssales-last7days' ] = array(
			'parent' => $reportssales,
			'title'  => __( 'Last 7 Days', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&range=7day' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Last 7 Days', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportssales-thismonth' ] = array(
			'parent' => $reportssales,
			'title'  => __( 'This Month', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&range=month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'This Month', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportssales-lastmonth' ] = array(
			'parent' => $reportssales,
			'title'  => __( 'Last Month', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&range=last_month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Last Month', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportssales-year' ] = array(
			'parent' => $reportssales,
			'title'  => __( 'Year', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&range=last_month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Year', 'woocommerce-admin-bar-addition' )
			)
		);

	/** By Products */
	$menu_items[ 'reportsproducts' ] = array(
		'parent' => $reports,
		'title'  => __( 'Product Sales', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_product' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Product Sales', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'reportsproducts-last7days' ] = array(
			'parent' => $reportsproducts,
			'title'  => __( 'Last 7 Days', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_product&range=7day' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Last 7 Days', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportsproducts-thismonth' ] = array(
			'parent' => $reportsproducts,
			'title'  => __( 'This Month', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_product&range=month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'This Month', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportsproducts-lastmonth' ] = array(
			'parent' => $reportsproducts,
			'title'  => __( 'Last Month', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_product&range=last_month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Last Month', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportsproducts-year' ] = array(
			'parent' => $reportsproducts,
			'title'  => __( 'Year', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_product&range=year' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Year', 'woocommerce-admin-bar-addition' )
			)
		);

	/** by Category */
	$menu_items[ 'reportscategories' ] = array(
		'parent' => $reports,
		'title'  => __( 'Category Sales', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_category' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Category Sales', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'reportscategories-last7days' ] = array(
			'parent' => $reportscategories,
			'title'  => __( 'Last 7 Days', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_category&range=7day' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Last 7 Days', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportscategories-thismonth' ] = array(
			'parent' => $reportscategories,
			'title'  => __( 'This Month', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_category&range=month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'This Month', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportscategories-lastmonth' ] = array(
			'parent' => $reportscategories,
			'title'  => __( 'Last Month', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_category&range=last_month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Last Month', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportscategories-year' ] = array(
			'parent' => $reportscategories,
			'title'  => __( 'Year', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_category&range=year' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Year', 'woocommerce-admin-bar-addition' )
			)
		);

	/** by Coupon */
	$menu_items[ 'reportscoupons' ] = array(
		'parent' => $reports,
		'title'  => __( 'Coupon Usage', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=coupon_usage' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Coupon Usage', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'reportscoupons-last7days' ] = array(
			'parent' => $reportscoupons,
			'title'  => __( 'Last 7 Days', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=coupon_usage&range=7day' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Last 7 Days', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportscoupons-thismonth' ] = array(
			'parent' => $reportscoupons,
			'title'  => __( 'This Month', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=coupon_usage&range=month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'This Month', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportscoupons-lastmonth' ] = array(
			'parent' => $reportscoupons,
			'title'  => __( 'Last Month', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=coupon_usage&range=month' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Last Month', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportscoupons-year' ] = array(
			'parent' => $reportscoupons,
			'title'  => __( 'Year', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=coupon_usage&range=year' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Year', 'woocommerce-admin-bar-addition' )
			)
		);

	/** Customers */
	$menu_items[ 'reportscustomers' ] = array(
		'parent' => $reports,
		'title'  => __( 'Customers', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-reports&tab=customers' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Customers', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'reportscustomers-vsguests' ] = array(
			'parent' => $reportscustomers,
			'title'  => __( 'Customers vs. Guests', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=customers&report=customers' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Customers vs. Guests', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportscustomers-list' ] = array(
			'parent' => $reportscustomers,
			'title'  => __( 'Customer Listing', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=customers&report=customer_list' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Customer Listing', 'woocommerce-admin-bar-addition' )
			)
		);

	/** Stock */
	$menu_items[ 'reportsstock' ] = array(
		'parent' => $reports,
		'title'  => __( 'Stock', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-reports&tab=stock' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Stock', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'reportsstock-low' ] = array(
			'parent' => $reportsstock,
			'title'  => __( 'Low in Stock', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=stock&report=low_in_stock' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Low in Stock', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportsstock-out' ] = array(
			'parent' => $reportsstock,
			'title'  => __( 'Out of Stock', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=stock&report=out_of_stock' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Out of Stock', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'reportsstock-most' ] = array(
			'parent' => $reportsstock,
			'title'  => __( 'Most Stocked', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=stock&report=most_stocked' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Most Stocked', 'woocommerce-admin-bar-addition' )
			)
		);