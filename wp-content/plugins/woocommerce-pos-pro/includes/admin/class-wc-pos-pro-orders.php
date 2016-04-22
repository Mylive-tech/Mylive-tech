<?php
/**
 * POS Pro Order Admin Class
 * - only active for $screen-id = edit-shop_order or shop_order
 * - allow users to change store
 *
 * @class    WC_POS_Pro_Admin_Orders
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */
class WC_POS_Pro_Admin_Orders {

  public function __construct() {
    // option for order type
    add_action('woocommerce_admin_order_data_after_order_details', array($this, 'order_details'), 20, 1);
    add_action('woocommerce_process_shop_order_meta', array($this, 'save'), 20, 2);
  }

  /**
   * Add select dropdown
   * @param $order
   */
  public function order_details($order){
    $pos_order = get_post_meta( $order->id, '_pos', true );
    $store_id = get_post_meta( $order->id, '_pos_store', true );
    $stores = get_pages( array( 'post_type' => 'stores' ) );
    if( $pos_order && count($stores) > 0 ){
      include 'views/order-details.php';
    }
  }

  /**
   * Save the order type
   * @param $post_id
   * @param $post
   */
  public function save( $post_id, $post ){
    if( !isset( $_POST['wc_pos_pro_store'] ) || !is_numeric( $_POST['wc_pos_pro_store'] ) )
      return;

    $store_id = absint( $_POST['wc_pos_pro_store'] );
    update_post_meta($post_id, '_pos_store', $store_id );
    update_post_meta($post_id, '_pos_store_title', get_the_title( $store_id ));
  }

}