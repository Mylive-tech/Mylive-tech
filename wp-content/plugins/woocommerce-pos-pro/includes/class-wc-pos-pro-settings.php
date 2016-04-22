<?php
/**
 *
 *
 * @class    WC_POS_Pro_Settings
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_Settings {

	public function __construct() {

    // this needs to be ajax + admin + public facing
    add_filter( 'woocommerce_pos_settings_handlers', array( $this, 'settings_pages' ), 99, 1 );

    // ajax only?
    add_filter( 'woocommerce_pos_general_settings_defaults', array( $this, 'defaults' ) );
    add_action( 'woocommerce_pos_general_settings_after_output', array( $this, 'general_settings_after_output' ) );

  }

  /**
   * Add Pro settings
   *
   * @param $settings
   * @return array
   */
  static public function settings_pages( $settings ) {
    return array_slice( $settings, 0, 2, true ) +
      array( 'customers' => 'WC_POS_Pro_Admin_Settings_Customers' ) +
      array_slice( $settings, 2, count($settings)-2, true ) +
      array( 'license' => 'WC_POS_Pro_Admin_Settings_License' );
  }

  /**
   * @param array $defaults
   * @return array
   */
  public function defaults( array $defaults ){
    return $defaults + array(
      'barcode_field' => '_sku'
    );
  }

  /**
   *
   */
  public function general_settings_after_output(){
    WC_POS_Pro_Admin_Settings_General::output();
  }

}