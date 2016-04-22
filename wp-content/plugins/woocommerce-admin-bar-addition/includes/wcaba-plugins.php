<?php
/**
 * Display links to active WooCommerce plugins/extensions settings' pages
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Plugin/Extension Support
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
 * WooCommerce Print Invoices & Delivery Notes (free, by Steve Clark,
 *    Triggvy Gunderson, David Decker, David Decker - DECKERWEB)
 *
 * @since 1.5.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-delivery-notes/woocommerce-delivery-notes.php' ) )
	|| class_exists( 'WooCommerce_Delivery_Notes' ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Check for plugin version/admin URL */
	if ( defined( 'WCDN_PLUGIN_BASEDIR' ) ) {

		$wcaba_ext_wcdn_aurl = admin_url( 'admin.php?page=woocommerce_delivery_notes' );

	} else {

		$wcaba_ext_wcdn_aurl = admin_url( 'admin.php?page=woocommerce&tab=delivery-notes' );

	}  // end if

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'ext-wcdn' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Invoices &amp; Delivery Notes Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => $wcaba_ext_wcdn_aurl,
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Invoices &amp; Delivery Notes Settings', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Orders" level submenu */
	$menu_items[ 'o-delivery-notes' ] = array(
		'parent' => $orders,
		'title'  => __( 'Invoices &amp; Delivery Notes Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => $wcaba_ext_wcdn_aurl,
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Invoices &amp; Delivery Notes Settings', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Print Invoices & Delivery Notes


/**
 * WooCommerce Print Invoice/Packing List (premium, by Ilari Mäkelä)
 *
 * @since 2.2.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-pip/woocommerce-pip.php' ) ) || function_exists( 'woocommerce_pip_scripts' ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'ext-wcpip' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Print Invoice/Packing List Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_pip' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Print Invoice/Packing List Settings', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Orders" level submenu */
	$menu_items[ 'o-wcpip' ] = array(
		'parent' => $orders,
		'title'  => __( 'Print Invoice/Packing List Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_pip' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Print Invoice/Packing List Settings', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Print Invoice/Packing List


/**
 * WooCommerce Bulk Stock Management (premium, by Mike Jolley/WooThemes)
 *
 * @since 2.3.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-bulk-stock-management/bulk-stock-management.php' ) ) || class_exists( 'WC_Advanced_Stock_Management' ) )
	&& current_user_can( 'edit_products' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwcbulkstockm' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Stock Management', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'edit.php?post_type=product&page=wc_stock_management' ),
		'meta'   => array(
			'target' => '',
			'title'  => _x( 'Bulk Stock Management', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Products" level submenu */
	$menu_items[ 'p_bulkstockm' ] = array(
		'parent' => $products,
		'title'  => __( 'Stock Management', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'edit.php?post_type=product&page=wc_stock_management' ),
		'meta'   => array(
			'target' => '',
			'title'  => _x( 'Bulk Stock Management', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Bulk Stock Management


/**
 * WooCommerce Pay to Upload (free, by Patrick Garman)
 *
 * @since 2.5.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-pay-to-upload/wc-pay-to-upload.php' ) ) || class_exists( 'WC_Pay_To_Upload' ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'ext-wcpaytoupload' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Pay to Upload Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=wc_pay_to_upload' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Pay to Upload Settings', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Pay to Upload


/**
 * WooCommerce Compare Products Lite (free, by A3 Revolution Software Development)
 *
 * @since 2.2.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-compare-products/compare_products.php' ) ) || class_exists( 'WOO_Compare_Class' ) )
	&& current_user_can( 'manage_options' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwccplite' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Compare Products Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings&tab=settings' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Compare Products Settings', 'woocommerce-admin-bar-addition' )
		)
	);
	$menu_items[ 'extwccplite-features' ] = array(
		'parent' => $extwccplite,
		'title'  => __( 'Features', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings&tab=features' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Features', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Products" level submenu */
	$menu_items[ 'p_compareproductslite' ] = array(
		'parent' => $products,
		'title'  => __( 'Compare Products Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings&tab=settings' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Compare Products Settings', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'p_compareproductslite-features' ] = array(
		'parent' => $p_compareproductslite,
		'title'  => __( 'Features', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo-compare-settings&tab=features' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Features', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Compare Products Lite


/**
 * WooCommerce Compare Products Pro (premium, by A3 Revolution Software Development)
 *
 * Note: Plugin version 1.0.0 or higher required!
 *
 * @since 2.3.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-compare-products-pro/compare_products.php' ) ) || class_exists( 'WOO_Compare_Data' ) )
	&& current_user_can( 'manage_options' )
) {

	/** Include plugin file with neccessary code parts */
	require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-plugins-compareproductspro.php' );

}  // end if WC Compare Products Pro


/**
 * Smart Manager for WP e-Commerce & WooCommerce (Lite) (free lite version, by Store Apps)
 *
 * @since 1.6.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'smart-manager-for-wp-e-commerce/smart-manager.php' ) ) || function_exists( 'smart_admin_init' ) )
	&& current_user_can( 'edit_products' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'ext-smartmanager' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Smart Manager for', 'woocommerce-admin-bar-addition' ) . ' ' . $wcaba_woocommerce_name,
		'href'   => admin_url( 'edit.php?post_type=product&page=smart-manager-woo' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Smart Manager for', 'woocommerce-admin-bar-addition' ) . ' ' . $wcaba_woocommerce_name_tooltip
		)
	);

	/** Entry at "Products" level submenu */
	$menu_items[ 'p-smart-manager' ] = array(
		'parent' => $products,
		'title'  => __( 'Smart Manager for', 'woocommerce-admin-bar-addition' ) . ' ' . $wcaba_woocommerce_name,
		'href'   => admin_url( 'edit.php?post_type=product&page=smart-manager-woo' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Smart Manager for', 'woocommerce-admin-bar-addition' ) . ' ' . $wcaba_woocommerce_name_tooltip
		)
	);

}  // end if Smart Manager


/**
 * Support for free & premium version of Mediaburst SMS Extension:
 * Mediaburst: Ecommerce SMS Notifications (free, by Mediaburst + Simon Wheatley)
 * WooCommerce SMS Notifications (premium, by WooThemes + Mediaburst + Simon Wheatley)
 *
 * @since 1.7.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'mediaburst-ecommerce-sms-notifications/ecommerce-sms-notifications.php' ) ) || class_exists( 'mediaburstSMS' ) )
	&& current_user_can( 'manage_options' )
) {

	$menu_items[ 'ext-mediaburstsms' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Mediaburst: SMS Notifications', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=email' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Mediaburst: SMS Notifications', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if Mediaburst SMS Extension


/**
 * WooCommerce Clickatell SMS Notifications (premium, by WooThemes)
 *
 * @since 1.9.0
 */
if ( ( in_array( 'woocommerce-clickatell-sms-notifications/clickatell-sms-notifications.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	$menu_items[ 'extwtclickatellsms' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Clickatell SMS Notifications', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_sms_order_updates' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Clickatell SMS Notifications', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwtclickatellsms-gateway' ] = array(
		'parent' => $extwtclickatellsms,
		'title'  => __( 'Gateway Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_sms_order_updates' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Gateway Settings', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Clickatell SMS Notifications


/**
 * Pronamic iDEAL settings pages (free, by Pronamic)
 *
 * @since 1.7.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'pronamic-ideal/pronamic-ideal.php' ) ) || class_exists( 'Pronamic_IDeal_IDeal' ) )
	&& current_user_can( 'pronamic_ideal' )
) {

	/** Include plugin file with neccessary code parts */
	require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-plugins-pronamicideal.php' );

}  // end if Pronamic iDEAL


/**
 * WooCommerce E-Mail Attachments (premium, by Guenter Schoenmann - InoPlugs)
 *
 * @since 2.1.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-email-attachments/woocommerce_email_attachments_plugin.php' ) ) || function_exists( 'woocommerce_email_attachments_init' ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwt-emailattachments' ] = array(
		'parent' => $extensions,
		'title'  => __( 'E-Mail Attachments', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=inoplugs_email' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'E-Mail Attachments', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Settings" level submenu */
	$menu_items[ 's-email-attachments' ] = array(
		'parent' => $settings,
		'title'  => __( 'E-Mail Attachments', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=inoplugs_email' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'E-Mail Attachments', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC E-Mail Attachments


/**
 * Affiliates WooCommerce Integration Light (free, by Karim Rahimpur)
 *
 * @since 1.8.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'affiliates-woocommerce-light/affiliates-woocommerce-light.php' ) ) || class_exists( 'Affiliates_WooCommerce_Light_Integration' ) ) && current_user_can( 'aff_admin_options' ) ) {

	$menu_items[ 'ext-affiliates-wclight' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Affiliates Integration Light', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=affiliates-admin-woocommerce-light' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Affiliates Integration Light', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Affiliates Integration Light


/**
 * WooCommerce Substribe to Newsletter (premium, by WooThemes)
 *
 * @since 1.8.0
 */
if ( ( in_array( 'woocommerce-subscribe-to-newsletter/subscribe-to-newsletter.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwt-newsletter' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Newsletter Configuration', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=newsletter' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Newsletter Configuration', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Settings" level submenu */
	$menu_items[ 's-newsletter' ] = array(
		'parent' => $settings,
		'title'  => __( 'Newsletter Configuration', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=newsletter' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Newsletter Configuration', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Subscribe to Newsletter


/**
 * WooCommerce AWeber Newsletter Subscription (premium, by Gerhard Potgieter)
 *
 * @since 2.0.0
 */
if ( ( in_array( 'woocommerce-aweber-newsletter-subscription/aweber-newsletter-subscription.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwt-aweber' ] = array(
		'parent' => $extensions,
		'title'  => __( 'AWeber Newsletter', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=woo_aweber' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'AWeber Newsletter', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Settings" level submenu */
	$menu_items[ 's-aweber' ] = array(
		'parent' => $settings,
		'title'  => __( 'AWeber Newsletter', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=woo_aweber' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'AWeber Newsletter', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC AWeber Newsletter Subscription


/**
 * WooCommerce Software Add-On (premium, by WooThemes)
 *
 * @since 2.4.0
 */
if ( ( in_array( 'woocommerce-software-add-on/woocommerce-software.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Include plugin file with neccessary code parts */
	require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-plugins-softwareaddon.php' );

}  // end if WC Software Add-On


/**
 * WooCommerce Dynamic Pricing (premium, by WooThemes)
 *
 * @since 1.8.0
 */
if ( ( in_array( 'woocommerce-dynamic-pricing/woocommerce_pricing.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Include plugin file with neccessary code parts */
	require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-plugins-dynamicpricing.php' );

}  // end if WC Dynamic Pricing


/**
 * WooCommerce Commission King (premium, by WooThemes)
 *
 * @since 1.8.0
 */
if ( ( in_array( 'woocommerce-commission-king/commission-king.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Include plugin file with neccessary code parts */
	require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-plugins-commissionking.php' );

}  // end if WC Commission King


/**
 * WooCommerce jPlayer Product Sampler (premium, by WooThemes)
 *
 * @since 2.1.0
 */
if ( ( in_array( 'woocommerce-jplayer-product-sampler/jplayer-product-sampler.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	$menu_items[ 'extwt-jplayerps' ] = array(
		'parent' => $extensions,
		'title'  => __( 'jPlayer Product Sampler', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo_jplayer_product_sampler' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'jPlayer Product Sampler', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC jPlayer Product Sampler


/**
 * WooCommerce Watermark (premium, by David Baker)
 *
 * @since 2.3.0
 */
if ( ( in_array( 'woocommerce-product-image-watermark/woocommerce-watermark.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	$menu_items[ 'extwt-watermark' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Image Watermark Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_watermark' ),
		'meta'   => array(
			'target' => '',
			'title' => __( 'Image Watermark Settings', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Professor Cloud Zoom


/**
 * WooCommerce Professor Cloud Zoom (premium, by WooThemes)
 *
 * @since 1.8.0
 */
if ( ( in_array( 'woocommerce-professor-cloud/woocommerce-professor-cloud.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	$menu_items[ 'extwt-cloudzoom' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Professor Cloud Zoom Configuration', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=professor_cloud' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Professor Cloud Zoom Configuration', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Professor Cloud Zoom


/**
 * WooCommerce Cloud Zoom Image Plugin (free, by mRova)
 *
 * @since 2.3.0
 */
if ( ( in_array( 'cloud-zoom-for-woocommerce/index.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_options' )
) {

	$menu_items[ 'ext-cloudzoom' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Cloud Zoom Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'options-general.php?page=cloud-zoom-for-woocommerce/index.php' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Cloud Zoom Settings', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Cloud Zoom Image


/**
 * WooCommerce Catalog Visibility Options (premium, by Lucas Stark)
 *
 * @since 2.0.0
 */
if ( ( in_array( 'woocommerce-catalog-visibility-options/woocommerce-catalog-visibility-options.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwt-catalogvisibility' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Catalog Visibility Options', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=visibility_options' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Catalog Visibility Options', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Settings" level submenu */
	$menu_items[ 's-catalogvisibility' ] = array(
		'parent' => $settings,
		'title'  => __( 'Catalog Visibility Options', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=visibility_options' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Catalog Visibility Options', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Catalog Visibility Options


/**
 * WooCommerce Custom Status (free, by Garman Technical Services)
 *
 * @since 2.3
 */
if ( ( in_array( 'woocommerce-custom-statuses/woocommerce-custom-status.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'ext-customstatus' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Custom Order Statuses', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_order_status' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Custom Order Statuses', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Orders" level submenu */
	$menu_items[ 'o-customstatus' ] = array(
		'parent' => $orders,
		'title'  => __( 'Custom Order Statuses', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_order_status' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Custom Order Statuses', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Custom Status


/**
 * WooCommerce Wishlist Member Integration (premium, by Radomir van Dalen)
 *
 * @since 2.1.0
 */
if ( ( in_array( 'woocommerce-wishlist-member/woocommerce_wishlist.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwt-wishlistmember' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Wishlist Member Integration', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=wishlist' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Wishlist Member Integration', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Settings" level submenu */
	$menu_items[ 's-wishlistmember' ] = array(
		'parent' => $settings,
		'title'  => __( 'Wishlist Member', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=wishlist' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Wishlist Member', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Wishlist Member Integration


/**
 * WooCommerce Facebook Tab (premium, by David Baker)
 *
 * @since 2.0.0
 */
if ( ( in_array( 'woocommerce-facebook-tab/woocommerce-facebook-tab.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	$menu_items[ 'extwt-fbtab' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Facebook Tab Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_facebook' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Facebook Tab/Pages Settings', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Facebook Tab


/**
 * WooCommerce Amazon S3 Storage (premium, by Gerhard Potgieter)
 *
 * @since 2.1.0
 */
if ( ( in_array( 'woocommerce-amazon-s3-storage/amazon-s3-storage.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	$menu_items[ 'extwt-amazons3' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Amazon S3 Storage', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woo_amazon_s3_storage' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Amazon S3 Storage', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Amazon S3 Storage


/**
 * WooCommerce FreshBooks (premium, by Patrick Garman)
 *
 * @since 2.1.0
 */
if ( ( in_array( 'woocommerce-freshbooks/woocommerce-freshbooks.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_woocommerce' )
) {

	$menu_items[ 'extwt-freshbooks' ] = array(
		'parent' => $extensions,
		'title'  => __( 'FreshBooks Integration', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce_freshbooks' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'FreshBooks Integration', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC FreshBooks


/**
 * SOD QuickBooks Connector (premium, by Patrick Garman)
 *
 * @since 2.1.0
 */
if ( ( in_array( 'woocommerce-quickbooks/Server.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	&& current_user_can( 'manage_options' )
) {

	$menu_items[ 'extwtquickbooks' ] = array(
		'parent' => $extensions,
		'title'  => __( 'QuickBooks Integration', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=quickbooks_setup&tab=sod_qbconnector_setup' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'QuickBooks Integration', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwtquickbooks-sync' ] = array(
		'parent' => $extwtquickbooks,
		'title'  => __( 'Product Sync', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=quickbooks_setup&tab=sod_qbconnector_productssync' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Product Sync', 'woocommerce-admin-bar-addition' )
		)
	);

	$menu_items[ 'extwtquickbooks-connector' ] = array(
		'parent' => $extwtquickbooks,
		'title'  => __( 'Webconnector', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=quickbooks_setup&tab=sod_web_qbconnector_setup' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Webconnector', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC QuickBooks


/**
 * WooCommerce Branding (premium, by WooThemes)
 *
 * @since 2.2.0
 */
if ( class_exists( 'WC_Branding' ) && current_user_can( 'manage_woocommerce' ) ) {

	/** Entry at "Extensions" level submenu */
	$menu_items[ 'extwt-wcbranding' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Custom Branding', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=branding' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Custom Branding', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Entry at "Settings" level submenu */
	$menu_items[ 's-wcbranding' ] = array(
		'parent' => $settings,
		'title'  => __( 'Custom Branding', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=branding' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Custom Branding', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if WC Branding


/**
 * KISS Metrics for WooCommerce (free, by Max Rice)
 *
 * @since 2.4.0
 */
if ( function_exists( 'wc_init_km' ) && current_user_can( 'manage_woocommerce' ) ) {

	/** Entry at "Settings > Integrations" level submenu */
	$menu_items[ 'int-kissmetrics' ] = array(
		'parent' => $settingsintegration,
		'title'  => __( 'KISS Metrics Tracking', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woocommerce&tab=integration&section=kiss_metrics' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'KISS Metrics Tracking', 'woocommerce-admin-bar-addition' )
		)
	);

}  // end if KISS Metrics


/**
 * Various importers/ converters, exporters, toolkits - to list them as last entries of "active extensions" menu entry
 *
 * @since 1.4.0
 */

	/**
	 * WooCommerce Product CSV Import Suite (premium, by Mike Jolley/WooThemes)
	 *
	 * @since 2.3.0
	 */
	if ( ( in_array( 'woocommerce-product-csv-import-suite/product-csv-import.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		&& current_user_can( 'manage_woocommerce' )
	) {

		/** Entry at "Extensions" level submenu */
		$menu_items[ 'extwtcsvimportsuite' ] = array(
			'parent' => $extensions,
			'title'  => __( 'Import Products', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_csv_import_suite' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Import Products', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'extwtcsvimportsuite-export' ] = array(
			'parent' => $extwtcsvimportsuite,
			'title'  => __( 'Export Products', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_csv_import_suite&tab=export' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Export Products', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'extwtcsvimportsuite-documentation' ] = array(
			'parent' => $extwtcsvimportsuite,
			'title'  => _x( 'Documentation', 'Translators: For CSV Import Suite extension', 'woocommerce-admin-bar-addition' ),
			'href'   => 'http://www.woothemes.com/woocommerce-docs/user-guide/extensions/product-csv-import-suite/',
			'meta'   => array( 'title' => _x( 'Documentation', 'Translators: For CSV Import Suite extension', 'woocommerce-admin-bar-addition' ) )
		);

		$menu_items[ 'extwtcsvimportsuite-samplecsv' ] = array(
			'parent' => $extwtcsvimportsuite,
			'title'  => __( 'Sample CSV File', 'woocommerce-admin-bar-addition' ),
			'href'   => plugin_dir_url( 'woocommerce-product-csv-import-suite/sample.csv' ) . 'sample.csv',
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Sample CSV File', 'woocommerce-admin-bar-addition' )
			)
		);

		/** Entry at "Products" level submenu */
		$menu_items[ 'p_csvimportsuite' ] = array(
			'parent' => $products,
			'title'  => __( 'Import Products', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_csv_import_suite' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Import Products', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'p_csvimportsuite-export' ] = array(
			'parent' => $p_csvimportsuite,
			'title'  => __( 'Export Products', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_csv_import_suite&tab=export' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Export Products', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'p_csvimportsuite-documentation' ] = array(
			'parent' => $p_csvimportsuite,
			'title'  => _x( 'Documentation', 'Translators: For CSV Import Suite extension', 'woocommerce-admin-bar-addition' ),
			'href'   => 'http://www.woothemes.com/woocommerce-docs/user-guide/extensions/product-csv-import-suite/',
			'meta'   => array( 'title' => _x( 'Documentation', 'Translators: For CSV Import Suite extension', 'woocommerce-admin-bar-addition' ) )
		);

		$menu_items[ 'p_csvimportsuite-samplecsv' ] = array(
			'parent' => $p_csvimportsuite,
			'title'  => __( 'Sample CSV File', 'woocommerce-admin-bar-addition' ),
			'href'   => plugin_dir_url( 'woocommerce-product-csv-import-suite/sample.csv' ) . 'sample.csv',
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Sample CSV File', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end if WC Product CSV Import Suite


	/**
	 * WooCommerce Export CSV (premium, by Ilari Mäkelä)
	 *
	 * @since 2.0.0
	 */
	if ( ( in_array( 'woocommerce-export-csv/woocommerce-export-csv.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		&& current_user_can( 'manage_woocommerce' )
	) {

		/** Entry at "Extensions" level submenu */
		$menu_items[ 'extwt-exportcsv' ] = array(
			'parent' => $extensions,
			'title'  => __( 'Export to CSV', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_export_csv' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Export Orders/Clients to CSV', 'woocommerce-admin-bar-addition' )
			)
		);

		/** Entry at "Orders" level submenu */
		$menu_items[ 'o-exportcsv' ] = array(
			'parent' => $orders,
			'title'  => __( 'Export to CSV', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_export_csv' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Export Orders/Clients to CSV', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end if WC Export CSV


	/**
	 * WooCommerce Exporter (free, by Visser Labs)
	 *
	 * @since 1.0.0
	 */
	if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-exporter/exporter.php' ) ) || function_exists( 'woo_ce_admin_init' ) )
		&& current_user_can( 'manage_options' )
	) {

		$menu_items[ 'ext-exporter' ] = array(
			'parent' => $extensions,
			'title'  => __( 'WooCommerce Exporter', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'tools.php?page=woo_ce' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'WooCommerce Exporter', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end if WC Exporter


	/**
	 * WooCommerce Store Toolkit (free, by Visser Labs)
	 *
	 * @since 1.0.0
	 */
	if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce-store-toolkit/store-toolkit.php' ) ) || function_exists( 'woo_st_init' ) )
		&& current_user_can( 'manage_options' )
	) {

		$menu_items[ 'ext-store-toolkit' ] = array(
			'parent' => $extensions,
			'title'  => __( 'WooCommerce Store Toolkit', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'tools.php?page=woo_st' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'WooCommerce Store Toolkit', 'woocommerce-admin-bar-addition' )
			)
		);
	
	}  // end if WC Store Toolkit


	/**
	 * WooCommerce - Product Importer Deluxe (premium, by Visser Labs/WooThemes)
	 *
	 * @since 1.8.0
	 */
	if ( ( in_array( 'woocommerce-product-importer-deluxe/product-importer-deluxe.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		&& current_user_can( 'manage_options' )
	) {

		$menu_items[ 'extwt-pideluxe' ] = array(
			'parent' => $extensions,
			'title'  => __( 'Product Importer Deluxe', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?import=woo_pd' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Product Importer Deluxe', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end if WC Product Importer Deluxe


	/**
	 * WP e-Commerce to WooCommerce Converter (free, by ralcus at GitHub)
	 *
	 * @since 1.4.0
	 */
	if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wpec-to-woo/wpec-to-woo.php' ) ) || class_exists( 'ralc_wpec_to_woo' ) )
		&& current_user_can( 'manage_options' )
	) {

		$menu_items[ 'ext-wpectowoo' ] = array(
			'parent' => $extensions,
			'title'  => __( 'WP e-Commerce to WooCommerce', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'tools.php?page=wpec-to-woo' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'WP e-Commerce to WooCommerce', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end if WP e-Commerce Converter (free)


	/**
	 * Jigoshop to WooCommerce Converter/Importer (premium, by Agus MU/WooThemes)
	 *
	 * @since 1.8.0
	 */
	if ( ( in_array( 'woocommerce-jigoshop-converter/woocommerce-jigo-converter.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		&& current_user_can( 'import' )
	) {

		$menu_items[ 'extwt-jigo-wc' ] = array(
			'parent' => $extensions,
			'title'  => __( 'Jigoshop to WooCommerce Converter', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?import=woo_jigo' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Jigoshop to WooCommerce Converter', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end if Jigoshop Converter


	/**
	 * MarketPress to WooCommerce Converter/Importer (premium, by Agus MU/WooThemes)
	 *
	 * @since 1.8.0
	 */
	if ( ( in_array( 'woocommerce-mp-converter/woocommerce-mp-converter.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		&& current_user_can( 'import' )
	) {

		$menu_items[ 'extwt-mp-wc' ] = array(
			'parent' => $extensions,
			'title'  => __( 'MarketPress to WooCommerce Converter', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?import=woo_mp' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'MarketPress to WooCommerce Converter', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end if MarketPress Converter


	/**
	 * WP e-Commerce to WooCommerce Converter/Importer (premium, by Agus MU/WooThemes)
	 *
	 * @since 1.8
	 */
	if ( ( in_array( 'woocommerce-wpec-converter/woocommerce-wpec-converter.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		&& current_user_can( 'import' )
	) {

		$menu_items[ 'extwt-wpec-wc' ] = array(
			'parent' => $extensions,
			'title'  => __( 'WP e-Commerce to WooCommerce Converter', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?import=woo_wpec' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'WP e-Commerce to WooCommerce Converter', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end if WP e-Commerce Converter (premium)