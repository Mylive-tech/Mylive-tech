<?php

/**
 * POS Pro Customers Class
 *
 * @class    WC_POS_Pro_Customers
 * @package  WooCommerce POS Pro
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_API_Customers extends WC_POS_API_Abstract {

  /**
   * Constructor
   */
  public function __construct() {
    add_filter( 'woocommerce_api_create_customer_data', array( $this, 'customer_data') );
//    add_filter( 'woocommerce_api_edit_customer_data', array( $this, 'customer_data') );
//    add_action( 'pre_get_users', array( $this, 'pre_get_users' ) );
//    add_filter( 'woocommerce_api_customer_response', array( $this, 'customer_response' ), 10, 4 );
  }

	/**
	 *
	 *
	 * @param $data
	 * @return mixed
	 */
	public function customer_data( $data ) {
    add_filter( 'pre_option_woocommerce_registration_generate_password', array( $this, 'woocommerce_registration_generate_password' ) );
    if( wc_pos_get_option( 'customers', 'generate_username' ) ){
      add_filter( 'pre_option_woocommerce_registration_generate_username', array( $this, 'woocommerce_registration_generate_username' ) );
    }
    return $data;
	}

  /**
   * Force 'Automatically generate password'
   * WooCommerce > Settings > Accounts
   *
   * @return string
   */
  public function woocommerce_registration_generate_password(){
    return 'yes';
  }

  /**
   * Force 'Automatically generate username from customer email'
   * WooCommerce > Settings > Accounts
   *
   * @return string
   */
  public function woocommerce_registration_generate_username(){
    return 'yes';
  }

}