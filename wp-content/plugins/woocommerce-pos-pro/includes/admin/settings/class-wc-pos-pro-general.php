<?php

/**
 * Adds to the general settings
 *
 * @class    WC_POS_Pro_Admin_Settings_General
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_Admin_Settings_General {

  /**
   *
   */
  static public function output(){
    include 'views/general.php';
  }

  /**
   * @param string $q
   * @return array
   */
  static public function search_barcode_fields( $q = '' ){
    global $wpdb;
    $result = $wpdb->get_col(
      $wpdb->prepare(
        "
        SELECT DISTINCT(pm.meta_key)
        FROM $wpdb->postmeta AS pm
        JOIN $wpdb->posts AS p
        ON p.ID = pm.post_id
        WHERE p.post_type IN ('product', 'product_variation')
        AND pm.meta_key LIKE %s
        ORDER BY pm.meta_key
        ", '%'. $q . '%'
      )
    );
    return $result;
  }

}