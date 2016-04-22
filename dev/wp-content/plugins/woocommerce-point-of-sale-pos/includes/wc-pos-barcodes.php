<?php
/**
 * Logic related to displaying barcodes page.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale
 * @since    0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wc_point_of_sale_render_barcodes() {
  WC_POS()->barcode()->display_single_barcode_page();
}
add_action( 'admin_init', 'wc_point_of_sale_actions_barcodes' );

function wc_point_of_sale_actions_barcodes() {
    if(isset($_GET['page']) && $_GET['page'] != WC_POS()->id_barcodes) return;

    if(isset($_POST['action']) && $_POST['action'] == 'save_barcode'){
        WC_POS()->barcode()->save_barcode();
    }
}