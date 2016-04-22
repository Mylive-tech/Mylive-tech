<?php
/**
 *
 *
 * @class    WC_POS_Pro_Tax
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_Tax {

  /**
   * @param $base_rates
   * @param bool|false $store_id
   * @param bool|false $tax_address
   * @return mixed
   */
  static public function tax_rates($base_rates, $store_id = false, $tax_address = false){
    $store_id     = $store_id ? $store_id : get_user_option( 'woocommerce_pos_store' );
    $tax_address  = $tax_address ? $tax_address : get_post_meta( $store_id, '_tax_address', true );

    if($store_id && $tax_address == 'store'){
      $args = array(
        'country' 	=> get_post_meta( $store_id, '_country', true ),
        'state' 	  => get_post_meta( $store_id, '_state', true ),
        'city' 		  => get_post_meta( $store_id, '_city', true ),
        'postcode' 	=> get_post_meta( $store_id, '_postcode', true )
      );

      $tax_classes = method_exists( 'WC_POS_Tax','tax_classes' ) ? WC_POS_Tax::tax_classes() : self::tax_classes();

      foreach( $tax_classes as $class => $label ) {
        $args['tax_class'] = $class;
        if( $rate = WC_Tax::find_rates( $args ) )
          $rates[$class] = $rate;
      }
    }

    return isset($rates) ? $rates : $base_rates;
  }

  /**
   * Depreciate this - use DRY filter instead
   * @return array
   */
  static private function tax_classes() {
    $classes = array(
      /* translators: woocommerce */
      '' => __( 'Standard', 'woocommerce' )
    );
    // get_tax_classes method introduced in WC 2.3
    if( method_exists( 'WC_Tax','get_tax_classes' ) ){
      $labels = WC_Tax::get_tax_classes();
    } else {
      $labels = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );
    }
    foreach( $labels as $label ){
      $classes[ sanitize_title($label) ] = $label;
    }
    return $classes;
  }

}