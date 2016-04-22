<?php
/**
 * Logic related to displaying barcodes page.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale
 * @since    0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



function wc_point_of_sale_render_settings() {
  require_once( 'classes/class-wc-pos-settings.php');
  $wc_pos_settings = new WC_Pos_Settings();;
  $wc_pos_settings->output();
}