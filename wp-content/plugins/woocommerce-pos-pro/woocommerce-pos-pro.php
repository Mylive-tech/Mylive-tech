<?php

/**
 * Plugin Name: WooCommerce POS Pro
 * Plugin URI:  https://woopos.com.au/pro
 * Description: WooCommerce POS Pro adds extra functionality to the free plugin. Requires <a href="http://wordpress.org/plugins/woocommerce-pos/">WooCommerce POS</a>.
 * Version:     0.4.5
 * Author:      kilbot
 * Author URI:  http://www.kilbot.com.au
 * Text Domain: woocommerce-pos-pro
 * Domain Path: /languages
 * License:     Copyright 2015 The Kilbot Factory
 *
 * @package     WooCommerce POS Pro
 * @author      Paul Kilmurray <paul@kilbot.com.au>
 * @link        http://woopos.com.au
 * @copyright   Copyright (c) The Kilbot Factory
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define plugin constants.
 */
define( 'WC_POS_PRO_VERSION', '0.4.5' );
define( 'WC_POS_PRO_PLUGIN_FILE', plugin_basename( __FILE__ ) ); // 'woocommerce-pos-pro/woocommerce-pos-pro.php'
define( 'WC_POS_PRO_PLUGIN_NAME', 'woocommerce-pos-pro' );
define( 'WC_POS_PRO_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WC_POS_PRO_PLUGIN_URL', trailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

/**
 * The code that runs during plugin activation.
 */
require_once WC_POS_PRO_PLUGIN_PATH . 'includes/class-wc-pos-pro-activator.php';
new WC_POS_Pro_Activator( plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin deactivation.
 */
require_once WC_POS_PRO_PLUGIN_PATH . 'includes/class-wc-pos-pro-deactivator.php';
new WC_POS_Pro_Deactivator( plugin_basename( __FILE__ ) );