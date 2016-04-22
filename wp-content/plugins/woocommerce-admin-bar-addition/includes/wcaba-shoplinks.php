<?php
/**
 * Display shop links if theme support is enabled
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Shop Links
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
 * Display shop links for testing purposes
 * Will only be displayed if theme support is added via
 * "add_theme_support( 'wcaba-shop-links' );" to active theme/child theme
 *
 * @since 2.2
 */
$menu_items[ 'shoplink' ] = array(
	'parent' => $woocommercebar,
	'title'  => __( 'Go to Shop', 'woocommerce-admin-bar-addition' ),
	'href'   => get_permalink( woocommerce_get_page_id( 'shop' ) ),
	'meta'   => array(
		'target' => '_new',
		'title'  => _x( 'Go to Shop Base Page', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
	)
);

$menu_items[ 'shoplink-cart' ] = array(
	'parent' => $shoplink,
	'title'  => __( 'Cart Page', 'woocommerce-admin-bar-addition' ),
	'href'   => get_permalink( woocommerce_get_page_id( 'cart' ) ),
	'meta'   => array(
		'target' => '_new',
		'title'  => __( 'Cart Page', 'woocommerce-admin-bar-addition' )
	)
);

$menu_items[ 'shoplinkcheckout' ] = array(
	'parent' => $shoplink,
	'title'  => __( 'Checkout Page', 'woocommerce-admin-bar-addition' ),
	'href'   => get_permalink( woocommerce_get_page_id( 'checkout' ) ),
	'meta'   => array(
		'target' => '_new',
		'title'  => __( 'Checkout Page', 'woocommerce-admin-bar-addition' )
	)
);

	$menu_items[ 'shoplinkcheckout-pay' ] = array(
		'parent' => $shoplinkcheckout,
		'title'  => __( 'Pay Page', 'woocommerce-admin-bar-addition' ),
		'href'   => get_permalink( woocommerce_get_page_id( 'pay' ) ),
		'meta'   => array(
			'target' => '_new',
			'title'  => __( 'Pay Page', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'shoplinkcheckout-thanks' ] = array(
		'parent' => $shoplinkcheckout,
		'title'  => __( 'Thanks Page', 'woocommerce-admin-bar-addition' ),
		'href'   => get_permalink( woocommerce_get_page_id( 'thanks' ) ),
		'meta'   => array(
			'target' => '_new',
			'title'  => __( 'Thanks Page', 'woocommerce-admin-bar-addition' )
		)
	);

$menu_items[ 'shoplinkmyaccount' ] = array(
	'parent' => $shoplink,
	'title'  => __( 'My Account Page', 'woocommerce-admin-bar-addition' ),
	'href'   => get_permalink( woocommerce_get_page_id( 'myaccount' ) ),
	'meta'   => array(
		'target' => '_new',
		'title'  => __( 'My Account Page', 'woocommerce-admin-bar-addition' )
	)
);

	$menu_items[ 'shoplinkmyaccount-editaddress' ] = array(
		'parent' => $shoplinkmyaccount,
		'title'  => __( 'Edit Address Page', 'woocommerce-admin-bar-addition' ),
		'href'   => get_permalink( woocommerce_get_page_id( 'edit_address' ) ),
		'meta'   => array(
			'target' => '_new',
			'title'  => __( 'Edit Address Page', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'shoplinkmyaccount-vieworder' ] = array(
		'parent' => $shoplinkmyaccount,
		'title'  => __( 'View Order Page', 'woocommerce-admin-bar-addition' ),
		'href'   => get_permalink( woocommerce_get_page_id( 'view_order' ) ),
		'meta'   => array(
			'target' => '_new',
			'title'  => __( 'View Order Page', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'shoplinkmyaccount-changepassword' ] = array(
		'parent' => $shoplinkmyaccount,
		'title'  => __( 'Change Password Page', 'woocommerce-admin-bar-addition' ),
		'href'   => get_permalink( woocommerce_get_page_id( 'change_password' ) ),
		'meta'   => array(
			'target' => '_new',
			'title'  => __( 'Change Password Page', 'woocommerce-admin-bar-addition' )
		)
	);

$menu_items[ 'shoplink-terms' ] = array(
	'parent' => $shoplink,
	'title'  => __( 'Terms &amp; Conditions Page', 'woocommerce-admin-bar-addition' ),
	'href'   => get_permalink( woocommerce_get_page_id( 'terms' ) ),
	'meta'   => array(
		'target' => '_new',
		'title'  => __( 'Terms &amp; Conditions Page', 'woocommerce-admin-bar-addition' )
	)
);

/** Display only for WooCommerce German Extension */
if ( class_exists( 'Woocommerce_German_Extension' ) ) {

	$menu_items[ 'shoplinkde' ] = array(
		'parent' => $shoplink,
		'title'  => '<abbr title="' . _x( 'German Extension for Legal Certainty (by Inpsyde GmbH)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' ) . '">' . __( 'German Extension:', 'woocommerce-admin-bar-addition' ) . '</abbr>',
		'href'   => false,
		'meta'   => array(
			'target' => '_new',
			'title'  => _x( 'German Extension for Legal Certainty (by Inpsyde GmbH)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'shoplinkde-imprint' ] = array(
			'parent' => $shoplinkde,
			'title'  => __( 'Imprint Page', 'woocommerce-admin-bar-addition' ),
			'href'   => get_permalink( woocommerce_get_page_id( 'impressum' ) ),
			'meta'   => array(
				'target' => '_new',
				'title'  => __( 'Imprint Page', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'shoplinkde-shippingcost' ] = array(
			'parent' => $shoplinkde,
			'title'  => __( 'Shipping Costs Page', 'woocommerce-admin-bar-addition' ),
			'href'   => get_permalink( woocommerce_get_page_id( 'versandkosten' ) ),
			'meta'   => array(
				'target' => '_new',
				'title'  => __( 'Shipping Costs Page', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'shoplinkde-refund' ] = array(
			'parent' => $shoplinkde,
			'title'  => __( 'Refund Policy Page', 'woocommerce-admin-bar-addition' ),
			'href'   => get_permalink( woocommerce_get_page_id( 'widerruf' ) ),
			'meta'   => array(
				'target' => '_new',
				'title'  => __( 'Refund Policy Page', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'shoplinkde-privacy' ] = array(
			'parent' => $shoplinkde,
			'title'  => __( 'Privacy Page', 'woocommerce-admin-bar-addition' ),
			'href'   => get_permalink( woocommerce_get_page_id( 'datenschutz' ) ),
			'meta'   => array(
				'target' => '_new',
				'title'  => __( 'Privacy Page', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'shoplinkde-orderinstructions' ] = array(
			'parent' => $shoplinkde,
			'title'  => __( 'Order Instructions Page', 'woocommerce-admin-bar-addition' ),
			'href'   => get_permalink( woocommerce_get_page_id( 'bestellvorgang' ) ),
			'meta'   => array(
				'target' => '_new',
				'title'  => __( 'Order Instructions Page', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'shoplinkde-paymentgateways' ] = array(
			'parent' => $shoplinkde,
			'title'  => __( 'Payment Gateways Page', 'woocommerce-admin-bar-addition' ),
			'href'   => get_permalink( woocommerce_get_page_id( 'zahlungsarten' ) ),
			'meta'   => array(
				'target' => '_new',
				'title'  => __( 'Payment Gateways Page', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'shoplinkde-checkorder' ] = array(
			'parent' => $shoplinkde,
			'title'  => __( 'Check Order Page', 'woocommerce-admin-bar-addition' ),
			'href'   => get_permalink( woocommerce_get_page_id( 'check' ) ),
			'meta'   => array(
				'target' => '_new',
				'title'  => __( 'Check Order Page', 'woocommerce-admin-bar-addition' )
			)
		);

}  // end-if German Extension plugin