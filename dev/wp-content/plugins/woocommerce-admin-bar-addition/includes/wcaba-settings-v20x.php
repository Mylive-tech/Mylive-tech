<?php
/**
 * Display the settings links for WooCommerce 2.0.x branch.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Settings Links
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
 * Include settings menu items for WooCommerce 2.0.x branch:
 *
 * @since 1.0.0
 */
$menu_items[ 'settings' ] = array(
	'parent' => $woocommercebar,
	'title'  => __( 'Shop Settings', 'woocommerce-admin-bar-addition' ),
	'href'   => admin_url( 'admin.php?page=woocommerce_settings' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Shop Settings', 'woocommerce-admin-bar-addition' )
	)
);

	$menu_items[ 'sgeneralsettings' ] = array(
		'parent' => $settings,
		'title'  => __( 'General Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=general' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'General Settings', 'woocommerce-admin-bar-addition' )
		)
	);

		/** Allows for hiding the WC Debug link */
		if ( defined( 'WCABA_DEBUG_DISPLAY' ) && WCABA_DEBUG_DISPLAY ) {

			$menu_items[ 'sgeneralsettings-debug' ] = array(
				'parent' => $sgeneralsettings,
				'title'  => __( 'System Status', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'admin.php?page=woocommerce_status' ),
				'meta'   => array(
					'target' => '',
					'title'  => _x( 'System Status (WooCommerce Debugging)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
				)
			);

			$menu_items[ 'sgeneralsettings-tools' ] = array(
				'parent' => $sgeneralsettings,
				'title'  => __( 'System Tools', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'admin.php?page=woocommerce_status&tab=tools' ),
				'meta'   => array(
					'target' => '',
					'title'  => _x( 'System Tools', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
				)
			);

		}  // end if

	$menu_items[ 's-shop-pages' ] = array(
		'parent' => $settings,
		'title'  => __( 'Shop Pages', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=pages' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Shop Pages', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-shop-catalog' ] = array(
		'parent' => $settings,
		'title'  => __( 'Shop Catalog', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=catalog' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Shop Catalog', 'woocommerce-admin-bar-addition' )
		)			
	);

	$menu_items[ 's-inventory' ] = array(
		'parent' => $settings,
		'title'  => __( 'Inventory', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=inventory' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Inventory', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-taxes' ] = array(
		'parent' => $settings,
		'title'  => __( 'Taxes', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=tax' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Taxes', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-shipping-costs' ] = array(
		'parent' => $settings,
		'title'  => __( 'Shipping Options &amp; Costs', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=shipping' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Shipping Options &amp; Costs', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-payment-gateways' ] = array(
		'parent' => $settings,
		'title'  => __( 'Paymet Gateways', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Paymet Gateways', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-emails' ] = array(
		'parent' => $settings,
		'title'  => __( 'Emails', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=email' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Emails', 'woocommerce-admin-bar-addition' )
		)
	);

	/** "Integration" settings links */
	$menu_items[ 'settingsintegration' ] = array(
		'parent' => $settings,
		'title'  => __( 'Integration (Other Services)', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=integration' ),
		'meta'   => array(
			'target' => '',
			'title'  => _x( 'Integration (Other Services)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'settingsintegration-googleanalytics' ] = array(
			'parent' => $settingsintegration,
			'title'  => __( 'Google Analytics', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=integration&section=google_analytics' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Google Analytics', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'settingsintegration-sharethis' ] = array(
			'parent' => $settingsintegration,
			'title'  => __( 'ShareThis', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=integration&section=sharethis' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'ShareThis', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'settingsintegration-shareyourcart' ] = array(
			'parent' => $settingsintegration,
			'title'  => __( 'ShareYourCart', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=integration&section=shareyourcart' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'ShareYourCart', 'woocommerce-admin-bar-addition' )
			)
		);