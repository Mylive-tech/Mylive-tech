<?php
/**
 *
 *
 * @class    WC_POS_Pro_Params
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_Params {

	public function __construct() {

    // frontend
    if( is_pos() ) {
      add_filter( 'woocommerce_pos_menu', array($this, 'menu'), 5 );
      add_filter( 'woocommerce_pos_params', array($this, 'params'), 5 );
    }

	}

  /**
   * @param array $params
   * @return array
   */
  public function params( array $params ) {
    $params['tax_rates'] = WC_POS_Pro_Tax::tax_rates( $params['tax_rates'] );
    $params['order_status'] = wc_get_order_statuses();
    $params['generate_username'] = wc_pos_get_option( 'customers', 'generate_username' );
		return $params;
	}

  /**
   * @param $items
   * @return mixed
   * todo: don't rely on order here, what happens if someone removes a menu item?!
   */
  public function menu( $items ) {
    $items[1]['href'] = '#products';
    $items[2]['href'] = '#orders';
    $items[3]['href'] = '#customers';
    $items[4]['href'] = '#coupons';
    $support = $items[5]['label'];
    $items[5]['label'] = 'Pro '.$support;

    array_push( $items, array(
      'id'     => 'wordpress',
      /* translators: woocommerce */
      'label'  => 'WP ' . __( 'Dashboard' ),
      'href'   => admin_url()
    ));

    return $items;
  }

}