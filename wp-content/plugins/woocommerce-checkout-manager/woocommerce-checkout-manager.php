<?php
/*
Plugin Name: WooCommerce Checkout Manager
Plugin URI: https://wordpress.org/plugins/woocommerce-checkout-manager/
Description: Manages WooCommerce Checkout, the advanced way.
Contributors: visser, Emark
Author: Visser Labs
Author URI: http://www.visser.com.au
Version: 4.0.3
Text Domain: woocommerce-checkout-manager
Domain Path: /languages/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*
Notice of change of Plugin ownership (11/03/2016)

This Plugin was released to the WordPress community on 06/08/2013 and maintained till 10/03/2016 by Emark (https://profiles.wordpress.org/emark/).
On 11/03/2016 Plugin ownership was transferred from Emark to visser (https://profiles.wordpress.org/visser/) who will be responsible for resolving 
critical Plugin issues and ensuring the Plugin meets WordPress security and coding standards in the form of regular Plugin updates.
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

define( 'WOOCCM_DIRNAME', basename( dirname( __FILE__ ) ) );
define( 'WOOCCM_RELPATH', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'WOOCCM_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOOCCM_PREFIX', 'wooccm' );

include( WOOCCM_PATH.'includes/install.php' );	
include( WOOCCM_PATH.'includes/functions.php' );
include( WOOCCM_PATH.'includes/formatting.php' );	
include( WOOCCM_PATH.'includes/admin.php' );
include( WOOCCM_PATH.'includes/template.php' );
include( WOOCCM_PATH.'includes/export.php' );
include( WOOCCM_PATH.'includes/classes/main.php' );
include( WOOCCM_PATH.'includes/classes/field_filters.php' );
include( WOOCCM_PATH.'includes/classes/file_upload/main_upload.php' );

// @mod - We need to load the templates conditionally
include( WOOCCM_PATH.'includes/templates/functions/add_functions.php' );
include( WOOCCM_PATH.'includes/templates/functions/billing_functions.php' );
include( WOOCCM_PATH.'includes/templates/functions/shipping_functions.php' );
include( WOOCCM_PATH.'includes/templates/functions/add_wooccmupload.php' );
include( WOOCCM_PATH.'includes/templates/functions/billing_wooccmupload.php' );
include( WOOCCM_PATH.'includes/templates/functions/shipping_wooccmupload.php' );
include( WOOCCM_PATH.'includes/templates/functions/required/add_required.php' );
include( WOOCCM_PATH.'includes/templates/functions/required/billing_required.php' );
include( WOOCCM_PATH.'includes/templates/functions/required/shipping_required.php' ); 
include( WOOCCM_PATH.'includes/templates/functions/woocm_editing_wrapper.php' );

// @mod - We need to clean this up

register_activation_hook( __FILE__, 'wooccm_install' );

add_action( 'woocommerce_before_checkout_form' , 'wooccm_autocreate_account' );
add_action( 'woocommerce_email_after_order_table', 'wooccm_add_payment_method_to_new_order', 10, 3);
add_action(	'woocommerce_checkout_update_order_meta', 'wooccm_custom_checkout_field_update_order_meta');
add_action(	'woocommerce_checkout_process', 'wooccm_custom_checkout_field_process');
// Checkout - Order Received
add_action(	'woocommerce_order_details_after_customer_details', 'wooccm_custom_checkout_details');
add_action(	'woocommerce_checkout_after_customer_details','wooccm_checkout_text_after');
add_action(	'woocommerce_checkout_before_customer_details','wooccm_checkout_text_before');
add_filter(	'woocommerce_checkout_fields','wooccm_remove_fields_filter',15);
add_filter(	'woocommerce_checkout_fields','wooccm_remove_fields_filter3',1);
add_action(	'wp_head','wooccm_display_front');
add_action(	'wp_head','wooccm_billing_hide_required');
add_action(	'wp_head','wooccm_shipping_hide_required');
// @mod - wooccm_run_color_inner does not exist
// add_action(	'wooccm_run_color_innerpicker','wooccm_run_color_inner'); run color inside options page (proto)
add_action(	'woocommerce_before_checkout_form', 'wooccm_override_this');
add_filter( 'woocommerce_billing_fields', 'wooccm_billing_fields', 1000 );
add_filter( 'woocommerce_shipping_fields', 'wooccm_shipping_fields', 1000 );
add_filter( 'wcdn_order_info_fields', 'wooccm_woocommerce_delivery_notes_compat', 10, 2 );
add_filter( 'wc_customer_order_csv_export_order_row', 'wooccm_csv_export_modify_row_data', 10, 3 );
add_filter( 'wc_customer_order_csv_export_order_headers', 'wooccm_csv_export_modify_column_headers' );
add_filter( 'default_checkout_state', 'wooccm_state_default_switch' );
add_action(	'woocommerce_checkout_process', 'wooccm_custom_checkout_process');
add_action(	'woocommerce_checkout_process', 'wooccm_billing_custom_checkout_process');
add_action(	'woocommerce_checkout_process', 'wooccm_shipping_custom_checkout_process');

add_action( 'woocommerce_before_checkout_form', 'wooccm_upload_billing_scripts');
add_action( 'woocommerce_before_checkout_form', 'wooccm_upload_shipping_scripts');
add_action(	'woocommerce_before_checkout_form', 'wooccm_billing_scripts');
add_action(	'woocommerce_before_checkout_form', 'wooccm_shipping_scripts');
add_action(	'woocommerce_before_checkout_form', 'wooccm_billing_override_this');
add_action(	'woocommerce_before_checkout_form', 'wooccm_shipping_override_this');
add_action( 'woocommerce_before_checkout_form', 'wooccm_scripts');
add_action( 'woocommerce_before_checkout_form', 'wooccm_upload_scripts');

add_action('woocommerce_checkout_fields', 'wooccm_order_notes');
add_filter( 'parse_query', 'wooccm_query_list' );
add_action( 'restrict_manage_posts', 'woooccm_restrict_manage_posts' );

switch( wooccm_positioning() ) {

	case 'before_shipping_form':
		add_action('woocommerce_before_checkout_shipping_form', 'wooccm_custom_checkout_fields');
		break;

	case 'after_shipping_form':
		add_action('woocommerce_after_checkout_shipping_form', 'wooccm_custom_checkout_fields');
		break;

	case 'before_billing_form':
		add_action('woocommerce_before_checkout_billing_form', 'wooccm_custom_checkout_fields');
		break;

	case 'after_billing_form':
		add_action('woocommerce_after_checkout_billing_form', 'wooccm_custom_checkout_fields');
		break;

	case 'after_order_notes':
		add_action('woocommerce_after_order_notes', 'wooccm_custom_checkout_fields');
		break;

}

if( wooccm_validator_changename() ) {

	add_action('woocommerce_before_cart', 'wooccm_before_checkout');
	add_action('woocommerce_admin_order_data_after_order_details', 'wooccm_before_checkout');
	add_action('woocommerce_before_my_account', 'wooccm_before_checkout');
	add_action('woocommerce_email_header', 'wooccm_before_checkout');
	add_action('woocommerce_before_checkout_form', 'wooccm_before_checkout');
	add_action('woocommerce_after_cart', 'wooccm_after_checkout');
	add_action('woocommerce_admin_order_data_after_shipping_address', 'wooccm_after_checkout');
	add_action('woocommerce_after_my_account', 'wooccm_after_checkout');
	add_action('woocommerce_email_footer', 'wooccm_after_checkout');
	add_action('woocommerce_after_checkout_form', 'wooccm_after_checkout');

}

if( wooccm_enable_auto_complete() ) {

	add_action( 'woocommerce_before_checkout_form', 'wooccm_retain_field_values' );

}

function wooccm_load_textdomain() {

	$options = get_option( 'wccs_settings' );
	if( !empty($options['checkness']['admin_translation']) ) {
		load_plugin_textdomain( 'woocommerce-checkout-manager', false, WOOCCM_DIRNAME . '/languages/' ); 
	}

}
add_action( 'plugins_loaded', 'wooccm_load_textdomain' );

function wooccm_jquery_init() {

	global $woocommerce;

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	if( is_account_page() ) {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'wooccm-button-style', plugins_url( 'includes/classes/file_upload/button_style.css', WOOCCM_RELPATH ), false, '1.0', 'all' );
	}

	if( is_checkout() ) {

		// WPML - https://wpml.org/
		$current_language = ( defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : apply_filters( 'wooccm_language_code', false ) );

		// DatePicker
		wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
		if( defined( 'ICL_LANGUAGE_CODE' ) || !empty( $current_language ) ) {
			// Check if WPML is in use or the WordPress Filter has been used
			if(
				(
					function_exists( 'icl_register_string' ) && 
					ICL_LANGUAGE_CODE == $current_language && 
					ICL_LANGUAGE_CODE !== 'en'
				) || 
				!empty( $current_language )
			) {
				wp_enqueue_script( 'jquery.ui.datepicker-'.$current_language, plugins_url( 'includes/pickers/di18n/jquery.ui.datepicker-'.$current_language.'.js', WOOCCM_RELPATH ), array( 'jquery' ) );
			}
		}

		wp_enqueue_style('jquery-style', plugins_url( 'includes/pickers/jquery.ui.css', WOOCCM_RELPATH ) );

		// TimePicker - http://fgelinas.com/code/timepicker/
		wp_enqueue_script( 'jquery-ui-timepicker', plugins_url( 'includes/pickers/jquery.ui.timepicker.js', WOOCCM_RELPATH ), array( 'jquery' ) );
		wp_enqueue_style( 'jquery-ui-timepicker', plugins_url( 'includes/pickers/jquery.ui.timepicker.css', WOOCCM_RELPATH ) );
		wp_enqueue_style( 'jquery-ui-timepicker-min', plugins_url( 'includes/pickers/include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css', WOOCCM_RELPATH ) );
		// @mod - Do we need this any more?

		// wp_enqueue_script( 'jquery-lib', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js' );

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'iris', plugins_url( 'includes/pickers/iris/dist/iris.js', WOOCCM_RELPATH ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
		// @mod - Check if this file exists
		wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, 1 );
		// load the style and script for farbtastic
		// @mod - Check if the file exists
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic', admin_url( '/js/farbtastic.js' ) );

		wp_enqueue_style('dashicons');

		wp_enqueue_style( 'magnific-popup', plugins_url( 'includes/pickers/magnificpopup/dist/magnific-popup.css', WOOCCM_RELPATH ) );
		wp_enqueue_script( 'magnific-popup', plugins_url( 'includes/pickers/magnificpopup/dist/jquery.magnific-popup.js', WOOCCM_RELPATH ) );

		wp_enqueue_script( 'caman', plugins_url( 'includes/pickers/caman/dist/caman.js', WOOCCM_RELPATH ) );
		wp_enqueue_style( 'caman', plugins_url( 'includes/pickers/caman/dist/caman.css', WOOCCM_RELPATH ) );

		wp_enqueue_script( 'jcrop-color', plugins_url( 'includes/pickers/jcrop/js/jquery.color.js', WOOCCM_RELPATH ) );
		wp_enqueue_script( 'jcrop', plugins_url( 'includes/pickers/jcrop/js/jquery.Jcrop.js', WOOCCM_RELPATH ) );

	}

}
add_action( 'wp_enqueue_scripts', 'wooccm_jquery_init' );
?>