<?php
/**
 * Logic related to displaying Registers page.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale
 * @since    0.1
 */
if(isset($_GET['page'])){
    $curent_screen = $rest = substr($_GET['page'], 0, 7);
    if($curent_screen == 'wc_pos_')
        add_filter('screen_options_show_screen', '__return_false');
}

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wc_point_of_sale_add_options_registers() {
	$option = 'per_page';
	$args = array(
		'label' => __( 'Registers', 'wc_point_of_sale' ),
		'default' => 10,
		'option' => 'registers_per_page'
	);
	add_screen_option( $option, $args );

    WC_POS()->registers_table();

}
add_filter('set-screen-option', 'wc_point_of_sale_set_registers_options', 10, 3);
function wc_point_of_sale_set_registers_options($status, $option, $value) {
    if ( 'registers_per_page' == $option ) return $value;
    return $status;
}
add_action( 'admin_init', 'wc_point_of_sale_actions_registers' );

function wc_point_of_sale_render_registers() {
    if(isset($_GET['action']) && isset($_GET['reg']) && $_GET['action'] == 'view' && $_GET['reg'] != '')
      WC_POS()->register()->display_register_detail($_GET['reg']);

    elseif(isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == 'edit' && $_GET['id'] != '')
      WC_POS()->register()->display_edit_form($_GET['id']);

    else{
      WC_POS()->register()->display();
    }
}
function wc_point_of_sale_actions_registers() {
    global $wpdb;
    if(isset($_GET['page']) &&  $_GET['page'] != WC_POS()->id_registers) return;

    if( isset($_GET['logout']) && !empty($_GET['logout']) ){
        setcookie ("wc_point_of_sale_register", $_GET['logout'] ,time()-3600*24*120, '/');

        $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
        $register_id = $_GET['logout'];
        $db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $register_id");

        if ( $db_data && 0 != ($user_id = get_current_user_id()) ){
            $row = $db_data[0];
            
            $lock_user = $row->_edit_last;
            if($lock_user == $user_id){
                $now = current_time( 'mysql' );    
                $data['closed']     = $now;
                $data['_edit_last'] = $user_id;
                $rows_affected = $wpdb->update( $table_name, $data, array( 'ID' => $register_id ) );

                wp_redirect(wp_login_url(  get_admin_url(get_current_blog_id(), '/').'admin.php?page=wc_pos_registers' ) );
            }
        }
        
    }elseif( isset($_GET['close']) && !empty($_GET['close']) ){
        setcookie ("wc_point_of_sale_register", $_GET['close'] ,time()-3600*24*120, '/');

        $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
        $register_id = $_GET['close'];
        $db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $register_id");

        if ( $db_data && 0 != ($user_id = get_current_user_id()) ){
            $row = $db_data[0];
            
            $lock_user = $row->_edit_last;
            if($lock_user == $user_id){
                $now = current_time( 'mysql' );    
                $data['closed']     = $now;
                $data['_edit_last'] = $user_id;
                $rows_affected = $wpdb->update( $table_name, $data, array( 'ID' => $register_id ) );

                wp_redirect( get_admin_url(get_current_blog_id(), '/').'admin.php?page=wc_pos_registers&report='.$register_id );
            }
        }
    }
    if( isset($_POST['action']) && $_POST['action'] == 'add-wc-pos-registers'){
        WC_POS()->register()->save_register();
    }else if( isset($_GET['action']) &&  $_GET['action'] == 'delete' && isset($_GET['id']) && !empty($_GET['id'])  ) {
        WC_POS()->register()->delete_register();
    }
    else if( isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']) ) {
        WC_POS()->register()->delete_register();
    }
    else if ( isset($_POST['action2']) &&  $_POST['action2'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']) )  {
        WC_POS()->register()->delete_register();
    }
    else if( isset($_POST['action']) && $_POST['action'] == 'edit-wc-pos-registers' && isset($_POST['id']) && !empty($_POST['id']) ){
        WC_POS()->register()->save_register();
    }
    else if( isset($_POST['action']) && $_POST['action'] == 'save-wc-pos-registers-as-order' && isset($_POST['id']) && !empty($_POST['id']) ){
        WC_POS()->register()->save_register_as_order();
    }
    else if( isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['reg']) && !empty($_GET['reg']) ){
        setcookie ("wc_point_of_sale_register", $_GET['reg'] ,time()+3600*24*120, '/');
    }
}

function wc_point_of_sale_hide_admin_bar()
{
    add_filter( 'show_admin_bar', '__return_false' );
    add_filter( 'wp_admin_bar_class', '__return_false' );
}
function wc_point_of_sale_hide_admin_bar_css()
{
    ?>
    <style>
    html{padding-top: 0 !important;}
    </style>
    <?php
}

if( isset($_GET['page']) && $_GET['page'] == 'wc_pos_registers' && isset($_GET['reg']) && $_GET['reg'] != ''){
    $woocommerce_pos_company_logo = get_option('woocommerce_pos_register_layout_admin_bar', 'no');
    if ($woocommerce_pos_company_logo == 'yes'){
        add_filter( 'init', 'wc_point_of_sale_hide_admin_bar', 9 );
        add_action( 'admin_head', 'wc_point_of_sale_hide_admin_bar_css' );
    }
}
