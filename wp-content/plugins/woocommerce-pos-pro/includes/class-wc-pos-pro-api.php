<?php

/**
 * WC REST API Class
 *
 * @class    WC_POS_Pro_API
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_API {

  public function __construct() {
    if (!is_pos())
      return;

    add_filter( 'woocommerce_api_dispatch_args', array( $this, 'dispatch_args'), 20, 2 );
  }

  /**
   * @param $args
   * @param $callback
   * @return mixed
   */
  public function dispatch_args($args, $callback){
    $wc_api_handler = get_class($callback[0]);

    $has_data = in_array( $args['_method'], array(2, 4, 8) ) && isset( $args['data'] ) && is_array($args['data']);
    if( $has_data ){
      // remove status
      if( array_key_exists('status', $args['data']) ){
        unset($args['data']['status']);
      }
    }

    switch($wc_api_handler){
      case 'WC_API_Products':
        if( $has_data && !isset( $args['data']['product'] ) ){
          $data = $args['data'];
          unset( $args['data'] );
          $args['data']['product'] = $data;
        }
        new WC_POS_Pro_API_Products();
        break;
      case 'WC_API_Orders':
        // data fix happens in woocommerce-pos
        new WC_POS_Pro_API_Orders();
        break;
      case 'WC_API_Customers':
        if( $has_data && !isset( $args['data']['customer'] ) ){
          $data = $args['data'];
          unset( $args['data'] );
          $args['data']['customer'] = $data;
        }
        new WC_POS_Pro_API_Customers();
        break;
      case 'WC_API_Coupons':
        if( $has_data && !isset( $args['data']['coupon'] ) ){
          $data = $args['data'];
          unset( $args['data'] );
          $args['data']['coupon'] = $data;
        }
        new WC_POS_Pro_API_Coupons();
        break;
    }

    return $args;
  }

}