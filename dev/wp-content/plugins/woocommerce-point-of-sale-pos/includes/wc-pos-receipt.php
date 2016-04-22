<?php
/**
 * Logic related to displaying receipts page.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale
 * @since    0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wc_point_of_sale_add_options_receipts() {
  $option = 'per_page';
  $args = array(
    'label' => __( 'Receipts', 'wc_point_of_sale' ),
    'default' => 10,
    'option' => 'receipt_per_page'
  );
  add_screen_option( $option, $args );
  WC_POS()->receipts_table();

}
add_filter('set-screen-option', 'wc_point_of_sale_set_receipts_options', 10, 3);
function wc_point_of_sale_set_receipts_options($status, $option, $value) {
    if ( 'receipt_per_page' == $option ) return $value;
    return $status;
}

function wc_point_of_sale_render_receipts() {

    if(isset($_GET['action']) && $_GET['action'] == 'add')
      WC_POS()->receipt()->display_single_receipt_page();

    elseif(isset($_GET['action']) && $_GET['action'] == 'edit')
      WC_POS()->receipt()->display_single_receipt_page();

    else
      WC_POS()->receipt()->display_receipt_table();
}
add_action( 'admin_init', 'wc_point_of_sale_actions_receipts' );

function wc_point_of_sale_actions_receipts() {
    if(isset($_GET['page']) && $_GET['page'] != WC_POS()->id_receipts) return;

    if(isset($_POST['action']) && $_POST['action'] == 'save_receipt'){
        WC_POS()->receipt()->save_receipt();
    }
    elseif(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']) && !empty($_GET['id']))
      WC_POS()->receipt()->delete_receipt($_GET['id']);
    elseif(isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']))
      WC_POS()->receipt()->delete_receipt($_POST['id']);
    elseif(isset($_POST['action2']) && $_POST['action2'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']))
      WC_POS()->receipt()->delete_receipt($_POST['id']);
}