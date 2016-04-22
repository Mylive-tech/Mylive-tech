<?php

/**
 * POS Pro Orders Class
 *
 * @class    WC_POS_Pro_API_Orders
 * @package  WooCommerce POS Pro
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_API_Orders extends WC_POS_API_Abstract {

  public function __construct() {
    add_filter( 'woocommerce_api_create_order_data', array( $this, 'create_order_data'), 20, 1 );
    add_filter( 'woocommerce_api_edit_order_data', array( $this, 'edit_order_data'), 20, 2 );
    add_action( 'woocommerce_api_create_order', array( $this, 'create_order'), 5, 1);
    add_action( 'woocommerce_pos_process_payment', array( $this, 'process_payment' ), 5, 2 );
  }

  /**
   * Return raw data
   * @param $data
   * @return array data
   */
  public function create_order_data($data) {
    add_filter( 'woocommerce_find_rates', array( $this, 'find_rates'), 20, 2 );
    return $data;
  }

  /**
   * Edit order data
   *
   * @param $data
   * @param $order_id
   * @return array
   */
  public function edit_order_data($data, $order_id){
    return $this->create_order_data($data);
  }

  /**
   * @param $matched_tax_rates
   * @param $args
   * @return array
   */
  public function find_rates( $matched_tax_rates, $args ){

    // remove to let WC_Tax::find_rates run
    remove_filter( 'woocommerce_find_rates', array( $this, 'find_rates'), 20, 2 );

    $store_id = get_user_option( 'woocommerce_pos_store' );
    $tax_address  = get_post_meta( $store_id, '_tax_address', true );

    if( $store_id && $tax_address == 'store' ){
      $matched_tax_rates = WC_Tax::find_rates( array(
        'country' 	=> get_post_meta( $store_id, '_country', true ),
        'state' 	  => get_post_meta( $store_id, '_state', true ),
        'city' 		  => get_post_meta( $store_id, '_city', true ),
        'postcode' 	=> get_post_meta( $store_id, '_postcode', true ),
        'tax_class' => $args['tax_class']
      ) );
    }

    // add filter for future find_rates, eg: shipping, fees
    add_filter( 'woocommerce_find_rates', array( $this, 'find_rates'), 20, 2 );

    return $matched_tax_rates;
  }

  /**
   * Create order complete
   * @param $order_id
   */
  public function create_order( $order_id ){
    $store_id = get_user_option( 'woocommerce_pos_store' );
    if( is_numeric( $store_id ) ){
      update_post_meta($order_id, '_pos_store', $store_id );
      update_post_meta($order_id, '_pos_store_title', get_the_title( $store_id ));
    }
  }

  /**
   * @param $order_id
   * @param $data
   */
  public function process_payment( $order_id, $data ){

    if(!isset($data['payment_details'])){
      return;
    }

    // prepare payment data
    $this->parse_payment_details($data['payment_details']);

  }

  /**
   * Normalize cc data
   * @param $payment_details
   */
  private function parse_payment_details($payment_details){

    foreach($payment_details as $key => $value){

      $_POST[$key] = $value;

      // match credit card number
      if(strpos($key, 'number') !== false){
        $_POST['number'] = $value;

        // match expiry month
      } elseif(strpos($key, 'month') !== false){
        $_POST['month'] = $value;

        // match expiry year
      } elseif(strpos($key, 'year') !== false){
        $_POST['year'] = $value;

        // match cvv
      } elseif(strpos($key, 'cvv') !== false){
        $_POST['cvv'] = $value;
      }

      if($key == 'stripe_token'){
        $_POST['stripe_token'] = $value;
        $_POST['stripeToken'] = $value;
      }

    }

  }

}