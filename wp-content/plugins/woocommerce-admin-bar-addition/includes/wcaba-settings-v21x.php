<?php
/**
 * Display the settings links for WooCommerce 2.1.x branch.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Settings Links
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
 * Include settings menu items for WooCommerce 2.1.x branch:
 *
 * @since 2.7.0
 */
$menu_items[ 'settings' ] = array(
	'parent' => $woocommercebar,
	'title'  => __( 'Shop Settings', 'woocommerce-admin-bar-addition' ),
	'href'   => admin_url( 'admin.php?page=wc-settings' ),
	'meta'   => array(
		'target' => '',
		'title'  => __( 'Shop Settings', 'woocommerce-admin-bar-addition' )
	)
);

	$menu_items[ 'sgeneralsettings' ] = array(
		'parent' => $settings,
		'title'  => __( 'General Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-settings&tab=general' ),
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
				'href'   => admin_url( 'admin.php?page=wc-status' ),
				'meta'   => array(
					'target' => '',
					'title'  => _x( 'System Status (WooCommerce Debugging)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
				)
			);

			$menu_items[ 'sgeneralsettings-tools' ] = array(
				'parent' => $sgeneralsettings,
				'title'  => __( 'System Tools', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'admin.php?page=wc-status&tab=tools' ),
				'meta'   => array(
					'target' => '',
					'title'  => _x( 'System Tools', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
				)
			);

		}  // end if

	$menu_items[ 's-products-options' ] = array(
		'parent' => $settings,
		'title'  => __( 'Products: Options', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-settings&tab=products' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Products: Options', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-products-inventory' ] = array(
		'parent' => $settings,
		'title'  => __( 'Products: Inventory', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-settings&tab=products&section=inventory' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Products: Inventory', 'woocommerce-admin-bar-addition' )
		)			
	);

	$menu_items[ 's-taxes' ] = array(
		'parent' => $settings,
		'title'  => __( 'Taxes', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-settings&tab=tax' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Taxes', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-checkout-gateways' ] = array(
		'parent' => $settings,
		'title'  => __( 'Checkout &amp; Payment Gateways', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-settings&tab=checkout' ),
		'meta'   => array(
			'target' => '',
			'title'  => _x( 'Checkout Options &amp; Payment Gateways', 'Translators: for the tooltip', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-shipping' ] = array(
		'parent' => $settings,
		'title'  => __( 'Shipping Options', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-settings&tab=shipping' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Shipping Options', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 's-useraccounts' ] = array(
		'parent' => $settings,
		'title'  => __( 'User Accounts', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-settings&tab=account' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'User Accounts', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'semails' ] = array(
		'parent' => $settings,
		'title'  => __( 'Emails', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc-settings&tab=email' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Emails', 'woocommerce-admin-bar-addition' )
		)
	);

		$menu_items[ 'semails-options' ] = array(
			'parent' => $semails,
			'title'  => _x( 'General Options', 'Translators: for Email template options', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email' ),
			'meta'   => array(
				'target' => '',
				'title'  => _x( 'General Options', 'Translators: for tooltip at Email Template options', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'semails-new' ] = array(
			'parent' => $semails,
			'title'  => _x( 'New Order', 'Translators: for Email Template options', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_new_order' ),
			'meta'   => array(
				'target' => '',
				'title'  => _x( 'New Ordner', 'Translators: for tooltip at Email Template options', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'semails-processing' ] = array(
			'parent' => $semails,
			'title'  => __( 'Order Processing', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_processing_order' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Order Processing', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'semails-completed' ] = array(
			'parent' => $semails,
			'title'  => __( 'Order Completed', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_completed_order' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Order Completed', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'semails-invoice' ] = array(
			'parent' => $semails,
			'title'  => __( 'Customer Invoice', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_invoice' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Customer Invoice', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'semails-note' ] = array(
			'parent' => $semails,
			'title'  => __( 'Customer Note', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_note' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Customer Note', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'semails-pwreset' ] = array(
			'parent' => $semails,
			'title'  => __( 'Password Reset', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_reset_password' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Password Reset', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'semails-useraccount' ] = array(
			'parent' => $semails,
			'title'  => __( 'New User Account', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_new_account' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'New User Account', 'woocommerce-admin-bar-addition' )
			)
		);

	/** Fully optional "Integration" settings links */
	if ( function_exists( 'wc_google_analytics_add_integration' )
		|| function_exists( 'wc_sharethis_add_integration' )
	) {

		$menu_items[ 'settingsintegration' ] = array(
			'parent' => $settings,
			'title'  => __( 'Integration (Other Services)', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=integration' ),
			'meta'   => array(
				'target' => '',
				'title'  => _x( 'Integration (Other Services)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
			)
		);

		if ( function_exists( 'wc_google_analytics_add_integration' ) ) {

			$menu_items[ 'settingsintegration-googleanalytics' ] = array(
				'parent' => $settingsintegration,
				'title'  => __( 'Google Analytics', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=integration&section=google_analytics' ),
				'meta'   => array(
					'target' => '',
					'title'  => __( 'Google Analytics', 'woocommerce-admin-bar-addition' )
				)
			);

		}  // end if

		if ( function_exists( 'wc_sharethis_add_integration' ) ) {

			$menu_items[ 'settingsintegration-sharethis' ] = array(
				'parent' => $settingsintegration,
				'title'  => __( 'ShareThis', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=integration&section=sharethis' ),
				'meta'   => array(
					'target' => '',
					'title'  => __( 'ShareThis', 'woocommerce-admin-bar-addition' )
				)
			);

		}  // end if

	}  // end if