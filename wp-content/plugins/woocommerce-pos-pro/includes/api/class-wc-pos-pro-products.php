<?php

/**
 * POS Pro Products Class
 *
 * @class    WC_POS_Pro_Products
 * @package  WooCommerce POS Pro
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_API_Products extends WC_POS_API_Abstract {

  /**
   * Constructor
   */
  public function __construct() {
    add_filter( 'woocommerce_api_create_product_data', array( $this, 'product_data') );
    add_filter( 'woocommerce_api_edit_product_data', array( $this, 'product_data') );
    add_action( 'woocommerce_api_edit_product', array( $this, 'edit_product'), 10, 2 );
//    add_filter( 'woocommerce_api_product_response', array( $this, 'product_response' ), 10, 4 );
    add_filter( 'woocommerce_pos_barcode_meta_key', array( $this, 'barcode_meta_key' ) );
  }

  /**
   * @param $data
   * @return mixed
   */
  public function product_data( $data ) {

    // todo: add a product drawer w/ more options, eg: tax_status & tax_class
    if( isset( $data['taxable'] ) ){
      $data['tax_status'] = $data['taxable'] ? 'taxable' : 'none';
    }

    // todo: submit fix to wc rest api
    if( isset( $data['type'] ) && $data['type'] == 'variation' ){
//      unset($data['type']);
      unset($data['title']);
    }

    // todo: inconsistent slugs and names
    if( isset($data['type']) && $data['type'] == 'variable' ){
      unset( $data['variations'] );
    }

    // allow toggle on_sale
    if( isset( $data['on_sale'] ) && !isset( $data['sale_price'] ) ){
      $data['sale_price'] = $data['on_sale'] ? 0 : '';
    }

    return $data;
	}

  /**
   * @param $id
   * @param $data
   */
  public function edit_product( $id, $data ){

    // allow decimal quantity
    // todo: submit fix to WC
    if( isset( $data['stock_quantity'] ) && is_float( $data['stock_quantity'] ) ){
      $product = wc_get_product( $id );
      $product->set_stock( $data['stock_quantity'] );
    }

  }

  /**
   * @param $product_data
   * @param $product
   * @param $fields
   * @param $server
   * @return mixed
   */
  public function product_response( $product_data, $product, $fields, $server ){
      return $product_data;
  }

  /**
   * @param $key
   * @return bool
   */
  public function barcode_meta_key( $key ){
    $custom_key = WC_POS_Settings::get_option( 'general', 'barcode_field' );
    return $custom_key ? $custom_key : $key;
  }

}