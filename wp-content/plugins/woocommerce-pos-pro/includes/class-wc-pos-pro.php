<?php

/**
 * The main POS Pro Class
 *
 * @class 	  WC_POS_Pro
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro {

	/**
	 * Constructor
	 */
	public function __construct() {

		// auto load classes
		if ( function_exists( 'spl_autoload_register' ) ) {
			spl_autoload_register( array( $this, 'autoload' ) );
		}

    add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'init', array( $this, 'init' ));
    add_action( 'woocommerce_api_loaded', array( $this, 'load_woocommerce_api_patches') );

    // custom post type has capabilities of post, it should have capabilities of product
    // - patch suggested: https://github.com/woothemes/woocommerce/pull/8250/
    // - fixed in version 2.4
    if( version_compare( WC()->version, '2.4', '<' ) ) {
      add_filter( 'woocommerce_register_post_type_product_variation', array($this, 'post_type_product_variation') );
    }

  }

  /**
   * @param $custom_post_type
   * @return mixed
   */
  public function post_type_product_variation( $custom_post_type ){
    $custom_post_type['capability_type'] = 'product';
    return $custom_post_type;
  }

	/**
	 * Autoload classes
	 * turns WC_POS_i18n into includes/class-wc-pos-i18n.php and
	 * WC_POS_Admin_Settings into includes/admin/class-wc-pos-settings.php
	 *
	 * @param $class
	 */
	private function autoload( $class ) {
		$cn = preg_replace( '/^wc_pos_pro_/', '', strtolower( $class ), 1, $count );
		if( $count ) {
			$path = explode('_', $cn);
			$last = 'class-wc-pos-pro-'. array_pop( $path ) .'.php';
			array_push( $path, $last );
			$file = WC_POS_PRO_PLUGIN_PATH . 'includes/' . implode( '/', $path );
			if( is_readable( $file ) )
				require_once $file;
		}
	}

  /**
   * Widgets
   * - Stores hours widget
   */
  public function widgets_init() {
    register_widget( 'WC_POS_Pro_Widgets_Hours' );
  }

	/**
	 * Load the required resources
	 */
	public function init() {

    // global helper functions
    require_once WC_POS_PRO_PLUGIN_PATH . 'includes/wc-pos-pro-functions.php';

    $this->_init();
    new WC_POS_Pro_i18n();
    new WC_POS_Pro_Settings();

    // admin only
    if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
      new WC_POS_Pro_Admin();
    }

    // ajax only
    // note: settings ajax required for license activation
    if (is_admin() && defined('DOING_AJAX') && DOING_AJAX ) {
      new WC_POS_Pro_AJAX();
    }

	}

  /**
   * License required
   */
  private function _init(){

    if( !wc_pos_pro_activated() ){
      return;
    }

    new WC_POS_Pro_Stores();
    new WC_POS_Pro_Gateways();
    new WC_POS_Pro_Template();
    new WC_POS_Pro_Params();

  }

  /**
   * License required
   */
  public function load_woocommerce_api_patches(){
    if( !wc_pos_pro_activated() ){
      return;
    }

    new WC_POS_Pro_API();
  }

}
