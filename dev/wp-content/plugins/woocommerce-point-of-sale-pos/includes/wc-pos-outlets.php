<?php
/**
 * Logic related to displaying Outlets page.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale
 * @since    0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function wc_point_of_sale_add_options_outlets() {
	$option = 'per_page';
	$args = array(
		'label' => __( 'Outlets', 'wc_point_of_sale' ),
		'default' => 10,
		'option' => 'outlets_per_page'
	);
	add_screen_option( $option, $args );

    WC_POS()->outlet_table();

}
add_filter('set-screen-option', 'wc_point_of_sale_set_outlets_options', 10, 3);
function wc_point_of_sale_set_outlets_options($status, $option, $value) {
    if ( 'outlets_per_page' == $option ) return $value;
    return $status;
}
add_action( 'admin_init', 'wc_point_of_sale_actions_outlets' );

function wc_point_of_sale_render_outlets() {

    if(isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == 'edit' && $_GET['id'] != '')
      WC_POS()->outlet()->display_edit_form($_GET['id']);
    else
      WC_POS()->outlet()->display();
}
function wc_point_of_sale_actions_outlets() {
    if(!isset($_GET['page'])) return;
    if(!isset($_POST['action']) && !isset($_GET['action'])) return;
    if(isset($_GET['page']) && $_GET['page'] != 'wc_pos_outlets') return;

    if(isset($_POST['action']) && $_POST['action'] == 'add-wc-pos-outlets'){
        WC_POS()->outlet()->save_outlet();
    }
    else if(isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == 'delete' && $_GET['id'] != ''){
        WC_POS()->outlet()->delete_outlet($_GET['id']);
    }
    else if( ( isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']) ) || ( isset($_POST['action']) && $_GET['action'] == 'delete' && isset($_GET['id']) && !empty($_GET['id']) ) ) {
        WC_POS()->outlet()->delete_outlet();
    }else if ( isset($_POST['action2']) &&  $_POST['action2'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']) )  {
        WC_POS()->outlet()->delete_outlet();
    }
    else if(isset($_POST['action']) && $_POST['action'] == 'edit-wc-pos-outlets' && isset($_POST['id']) && !empty($_POST['id']) ){
        WC_POS()->outlet()->save_outlet();
    }
}