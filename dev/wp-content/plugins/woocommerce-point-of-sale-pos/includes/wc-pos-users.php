<?php
/**
 * Logic related to displaying Users page.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale
 * @since    0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_filter('set-screen-option', 'wc_point_of_sale_set_users_options', 10, 3);

function wc_point_of_sale_add_options_users() {

  $option = 'per_page';
  $args = array(
    'label' => __( 'Cashiers', 'wc_point_of_sale' ),
    'default' => 10,
    'option' => 'users_per_page'
  );
  add_screen_option( $option, $args );

  WC_POS()->users_table();

}

function wc_point_of_sale_set_users_options($status, $option, $value) {
    if ( 'users_per_page' == $option ) return $value;
    return $status;
}


function wc_point_of_sale_render_users() {
  WC_POS()->user()->display();
}