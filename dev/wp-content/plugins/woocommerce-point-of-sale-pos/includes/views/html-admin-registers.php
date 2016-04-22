<?php
/**
 * HTML for a view registers page in admin.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale/views
 * @since    0.1
 */
$data = array();

WC_POS()->register()->init_form_fields();

if ($id) {
    $reg_slug = $id;
    $data = WC_POS()->register()->get_data_by_slug($id);
    $data = $data[0];

    foreach ($data['detail'] as $i => $val) {
        $data[$i] = $val;
    }
    foreach ($data['settings'] as $i => $val) {
        $data[$i] = $val;
    }
}
?>
<script>
    var pos_register_data = <?php echo json_encode($data) ?>;
</script>
<?php
$error_string = '';

$detail_fields    = WC_POS()->register()->get_register_detail_fields();
$detail_data      = $data['detail'];
if(isset($detail_fields['grid_template']['options'][$detail_data['grid_template']]))
    $grid_template    = $detail_fields['grid_template']['options'][$detail_data['grid_template']];
else
    $grid_template = '';
$receipt_template = $detail_fields['receipt_template']['options'][$detail_data['receipt_template']];



if(!$grid_template || empty($grid_template))
  $error_string   .= '<p>No product grid assigned.</p>';
if(!$receipt_template)
  $error_string   .= '<b>Receipt Template </b> is required<br>';

$outlets_name = WC_POS()->outlet()->get_data_names();

if(!$outlets_name[$data['outlet']])
  $error_string   .= '<b>Outlet </b> is required<br>';

if(!empty($error_string)){ ?>
<div id="post-lock-dialog" class="notification-dialog-wrap ">
    <div class="notification-dialog-background"></div>
    <div class="notification-dialog">
    <div class="post-locked-message not_close">
        <p class="currently-editing wp-tab-first" tabindex="0"><?php echo $error_string; ?></p>
        <p>
        <a class="button" href="<?php echo admin_url('admin.php?page=wc_pos_registers&action=edit&id='.$data['ID']); ?>"><?php  _e( 'Edit Register', 'wc_point_of_sale' ); ?></a>
                </p>
    </div>
    </div>
</div>
<?php
}
elseif(empty($error_string) && !WC_POS()->wc_api_is_active){
?>
<div id="post-lock-dialog" class="notification-dialog-wrap ">
    <div class="notification-dialog-background"></div>
    <div class="notification-dialog">
    <div class="post-locked-message not_close">
        <p class="currently-editing wp-tab-first" tabindex="0"><?php _e('The WooCommerce API is disabled on this site.', 'wc_point_of_sale'); ?></p>
        <p>
            <a class="button" href="<?php echo admin_url('admin.php?page=wc-settings'); ?>"><?php _e( 'Enable the REST API', 'wc_point_of_sale' ); ?></a>
            <a class="button" href="<?php echo admin_url('admin.php?page=wc_pos_registers' ); ?>"><?php _e( 'All Registers', 'wc_point_of_sale' ); ?></a>
        </p>
    </div>
    </div>
</div>
<?php
}
else{
    _admin_notice_register_locked($data['ID']);
    
    if (!pos_check_register_is_open($data['ID'])) {
        pos_set_register_lock($data['ID']);
    }
}
if( ( (int)$data['email_receipt'] ) ){
    echo '<input type="hidden" readonly value="yes" id="pos_sent_email_receipt">';
}
?>

<style>
    #adminmenuback{
        display: none;
    }
    #adminmenuwrap{
        display: none;
    }
    #wpcontent{
        margin-left: 0px;
    }
    #wpbody-content {
	    padding: 0px;
    }
</style>
<div class="wrap" id="wc-pos-registers-edit">
<?php 
$admin_url = get_admin_url(get_current_blog_id(), '/');
if(isset($_SERVER['HTTP_REFERER'])){
    $ref = $_SERVER['HTTP_REFERER'];
    if( !empty($_SERVER['HTTPS']) && !empty($ref) && strpos($ref, 'https://') === false ){
        $admin_url = str_replace('https://', 'http://', $admin_url);
    }
}
 ?>
    <h2>
        <?php echo $data['name']; ?>
        <a class="button" href="<?php echo $admin_url; ?>admin.php?page=wc_pos_registers" id="go_back_register"> <?php _e('Back', 'wc_point_of_sale'); ?> </a>
        <a class="button" href="#" id="retrieve_sales"> <?php _e('Load', 'wc_point_of_sale'); ?> </a>
        <?php if ( current_user_can( 'edit_private_shop_orders' ) ) { ?>
        <a class="button" href="<?php echo $admin_url; ?>edit.php?post_type=shop_order" id="orders_page"> <?php _e('Orders', 'wc_point_of_sale'); ?> </a>
        <?php } ?>
        <?php if ( current_user_can( 'manage_wc_point_of_sale' ) ) { ?>
        <a class="button" href="<?php echo $admin_url; ?>admin.php?page=wc_pos_settings" id="settings_page"> <?php _e('Settings', 'wc_point_of_sale'); ?> </a>
        <?php } ?>

        <button class="button ladda-button" data-spinner-color="#6d6d6d" id="sync_data"><span class="ladda-label"><span id="last_sync_time"></span></span></button>
        
        <div class="button offline-ui-up" id="offline_indication">
            <div class="offline-ui-content"></div>
            <a class="offline-ui-retry" href=""></a>
        </div>

        
        <a class="button button-primary" style="float:right;"href="<?php echo $admin_url; ?>admin.php?page=wc_pos_registers&amp;close=<?php echo $data['ID']; ?>" id="close_register"> <?php _e('Close', 'wc_point_of_sale'); ?> </a>
        <?php $current_user = wp_get_current_user();      ?>
        <a class="pos_register_user_panel" href="<?php echo $admin_url;?>profile.php">
        	<span class="pos_register_user_image"><?php echo get_avatar( $current_user->ID, 64); ?></span>
        	<span class="pos_register_user_name"><?php echo $current_user->display_name; ?></span>
        </a>

    </h2>
    <div class="notification-dialog-wrap" id="printing_receipt">
        <div class="notification-dialog-background"></div>
        <div class="notification-dialog">
            <div class="post-locked-message">
                <?php _e( 'Printing&hellip;', 'wc_point_of_sale' ); ?>
            </div>
        </div>
    </div>
    <?php
    wc_clear_notices();
    if($data['change_user']){
        $return_url = admin_url( 'admin.php?page=wc_pos_registers&logout='.$data['ID'] );
        if ( is_ssl() || get_option('woocommerce_pos_force_ssl_checkout') == 'yes' ) {
            $return_url = str_replace( 'http:', 'https:', $return_url );
        }
        ?>
        <script>
        var wc_pos_change_user = '<?php echo $return_url; ?>';
        </script>
        <?php
    }

    if ( get_post_status ( $data['order_id'] ) != 'publish' || get_post_type( $data['order_id'] ) != 'pos_temp_register_or') {
      $data['order_id'] = 0;
    }
    $data['order_id'] = $data['order_id'] != 0 ? $data['order_id'] : WC_POS()->register()->crate_order_id($data['ID']);
    ?>
    <div class="error below-h2" id="message_pos" style=""></div>
    <div id="ajax-response"></div>
    <?php
    $action_url = admin_url( 'admin.php?page=wc_pos_registers&action=view&outlet=' . $_GET['outlet'] . '&reg='. $_GET['reg']);
    ?>
    <form id="edit_wc_pos_registers" class="validate" action="<?php echo $action_url; ?>" method="post" autocomplete="off">
        <input type="hidden" value="save-wc-pos-registers-as-order" name="action">
        <input type="hidden" value="<?php echo $data['ID']; ?>" name="id_register" id="id_register">
        <input type="hidden" value="<?php echo $data['order_id']; ?>" name="id" id="order_id">
        
        <input type="hidden" value="<?php echo $data['receipt_template']; ?>" id="print_receipt_ID">

        <input type="hidden" value="<?php echo $data['outlet']; ?>" id="outlet_ID" name="outlet_ID">
        <?php wp_nonce_field('nonce-save-wc-pos-registers-as-order', '_wpnonce_save-wc-pos-registers-as-order'); ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">
                    <div id="wc-pos-register-grids" class="postbox ">
                    <?php
                    $pos_layout = get_option('woocommerce_pos_register_layout', 'product_grids');
                    
                    if($pos_layout == 'product_grids') :
                        $grid_id = $data['grid_template'];
                        if($grid_id == 'all'){
                            ?>
                            <h3 class="hndle">
                                <span id="wc-pos-register-grids-title"><?php _e('All products', 'wc_point_of_sale' ); ?></span>
                            </h3>
                            <div class="inside" id="grid_layout_cycle">
                                <?php 
                                the_grid_layout_cycle('all');
                                ?>
                            </div>
                            <div class="previous-next-toggles">
                                <span class="previous-grid-layout tips" data-tip="<?php _e('Previous', 'wc_point_of_sale'); ?>"></span>
                                <div id="nav_layout_cycle"></div>
                                <span class="next-grid-layout tips" data-tip="<?php _e('Next', 'wc_point_of_sale'); ?>"></span>
                            </div>
                            <?php
                        }else if($grid_id == 'categories'){
                            ?>
                            <h3 class="hndle">
                                <span id="wc-pos-register-grids-title" class="cat_title" data-parent="0"><?php _e('Categories', 'wc_point_of_sale' ); ?></span>
                                <div class="clear"></div>
                            </h3>
                            <div class="inside" id="grid_category_cycle">
                                <?php 
                                the_grid_category_layout_cycle('all');
                                ?>
                            </div>
                            <div class="previous-next-toggles">
                                <span class="previous-grid-layout tips" data-tip="<?php _e('Previous', 'wc_point_of_sale'); ?>"></span>
                                <div id="nav_layout_cycle"></div>
                                <span class="next-grid-layout tips" data-tip="<?php _e('Next', 'wc_point_of_sale'); ?>"></span>
                            </div>
                            <?php
                        }else{
                        $grids_single_record = wc_point_of_sale_tile_record($grid_id);
                        $grids_all_record    = wc_point_of_sale_get_all_grids($grid_id);
                        ?>
                        <h3 class="hndle">
                            <span id="wc-pos-register-grids-title"><?php if(!empty($grids_single_record)) _e( ucfirst($grids_single_record[0]->name).' Layout', 'wc_point_of_sale' ) ?></span>
                            <div class="clear"></div>
                        </h3>
                        <div class="inside" id="grid_layout_cycle">
                            <?php 
                            if(!empty($grids_single_record))
                                the_grid_layout_cycle($grids_single_record[0]);
                            if (!empty($grids_all_record) ) 
                                foreach ($grids_all_record as $grid)
                                    the_grid_layout_cycle($grid);
                            ?>
                        </div>
                        <div class="previous-next-toggles">
                            <span class="previous-grid-layout tips" data-tip="<?php _e('Previous', 'wc_point_of_sale'); ?>"></span>
                            <div id="nav_layout_cycle"></div>
                            <span class="next-grid-layout tips" data-tip="<?php _e('Next', 'wc_point_of_sale'); ?>"></span>
                        </div>
                        <?php }
                         else: ?>
                        <div class="inside" id="grid_layout_cycle">
                        <?php if($pos_layout == 'company_image'){ 
                            $woocommerce_pos_company_logo = get_option('woocommerce_pos_company_logo', '');
                            $src = '';
                            if(!empty($woocommerce_pos_company_logo) ){
                                $src = wp_get_attachment_image_src( $woocommerce_pos_company_logo, 'full' );
                                $src = $src[0];
                            }
                            ?>
                            <div class="grid_logo" style="height: 100%; ">
                                <img src="<?php echo $src; ?>" alt="">
                            </div>
                        <?php } elseif($pos_layout == 'text'){ ?>
                            <div class="grid_text" style="height: 100%; ">
                                <?php echo get_option('woocommerce_pos_register_layout_text', ''); ?>                                
                            </div>
                        <?php } elseif ($pos_layout == 'company_image_text'){ 
                            $woocommerce_pos_company_logo = get_option('woocommerce_pos_company_logo', '');
                            $src = '';
                            if(!empty($woocommerce_pos_company_logo) ){
                                $src = wp_get_attachment_image_src( $woocommerce_pos_company_logo, 'full' );
                                $src = $src[0];
                            }
                            ?>
                            <div class="grid_logo" style="height: 33%; ">
                                <img src="<?php echo $src; ?>" alt="">
                            </div>
                            <div class="grid_text" style="height: 67%; ">
                                <?php echo get_option('woocommerce_pos_register_layout_text', ''); ?>                                
                            </div>
                        <?php } ?>
                        </div>                            
                    <?php
                    endif; ?>
                    </div>
                    <div id="wc-pos-register-buttons" class="postbox ">
                        <div class="register_buttons">
                            <p>
                                <button class="button tips wc_pos_register_void button-primary" type="button" data-tip="<?php _e('Void Order', 'wc_point_of_sale'); ?>"><?php _e('Void', 'wc_point_of_sale'); ?></button>
                                <button class="button tips wc_pos_register_save button-primary" type="submit" data-tip="<?php _e('Save Order', 'wc_point_of_sale'); ?>"><?php _e('Save', 'wc_point_of_sale'); ?></button>
                                <button class="button tips wc_pos_register_notes button-primary" type="button" data-tip="<?php _e('Add A Note', 'wc_point_of_sale'); ?>"><?php _e('Note', 'wc_point_of_sale'); ?></button>
                                <?php 
                                $discount = esc_attr( get_user_meta( get_current_user_id(), 'discount', true ) );
                                if($discount != 'disable'): ?>
                                    <button class="button tips wc_pos_register_discount button-primary" type="button" data-tip="<?php _e('Apply Discount', 'wc_point_of_sale'); ?>"><?php _e('Discount', 'wc_point_of_sale'); ?></button>
                                <?php endif; ?>
                                <button class="button tips wc_pos_register_pay button-primary" type="button" data-tip="<?php _e('Accept Payment', 'wc_point_of_sale'); ?>"><?php _e('Pay', 'wc_point_of_sale'); ?></button>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                    <div id="wc-pos-register-data" class="postbox ">
                        <div class="hndle">
                            <p class="add_items">
                                <input id="add_product_id" class="ajax_chosen_select_products_and_variations" data-placeholder="<?php _e('Search Products', 'wc_point_of_sale'); ?>" />
                                </select>
                                <a class="tips shopopup" id="add_product_to_register" data-modal="add_custom_product_overlay_popup" data-tip="<?php _e('Add Custom Product', 'wc_point_of_sale'); ?>"><span></span></a>
                                <a class="tips shopopup" id="add_shipping_to_register" data-modal="add_shipping_overlay_popup" data-tip="<?php _e('Add Shipping', 'wc_point_of_sale'); ?>"><span></span></a>
                            </p>
                            <span class="clearfix"></span>
                        </div>
                        <div class="inside">
                            <div class="woocommerce_order_items_wrapper">
                                <table class="woocommerce_order_items" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="item"><?php _e('Item', 'wc_point_of_sale'); ?></th>
                                            <th class="line_cost"><?php _e('Price', 'wc_point_of_sale'); ?></th>
                                            <th class="quantity"><?php _e('Qty', 'wc_point_of_sale'); ?></th>
                                            <th class="line_cost_total"><?php _e('Total', 'wc_point_of_sale'); ?></th>
                                            <th class="line_remove">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order_items_list">
                                        <?php
                                        $order = new WC_Order($data['order_id']);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="wc_pos_register_subtotals">
                                <table class="woocommerce_order_items" cellspacing="0" cellpadding="0">
                                    <tr id="tr_order_subtotal_label">
                                        <th class="subtotal_label"><?php _e('Subtotal', 'wc_point_of_sale'); ?></th>
                                        <td class="subtotal_amount">
                                            <strong id="subtotal_amount"></strong>
                                            <input id="inp_subtotal_amount" type="hidden" value="0">
                                        </td>
                                    </tr>
                                    <?php /********************************/ ?>
                                    <?php
                                    if(isset($detail_data['default_shipping_method']) && $detail_data['default_shipping_method'] != ''){
                                    ?>
                                    <tr class="shipping_methods_register" style="display: table-row;">
                                    <?php
                                    }else{
                                    ?>
                                    <tr class="shipping_methods_register">
                                    <?php } ?>
                                        <th>
                                        <?php
                                        if(isset($detail_data['default_shipping_method']) && $detail_data['default_shipping_method'] != ''){
                                            _e( 'Shipping and Handling', 'woocommerce' );                                            
                                        }
                                        ?>
                                        </th>
                                        <td>
                                        <?php
                                        if(isset($detail_data['default_shipping_method']) && $detail_data['default_shipping_method'] != ''){
                                            $chosen_method = $detail_data['default_shipping_method'];
                                            $shipping_methods = WC()->shipping->load_shipping_methods();
                                            #var_dump($shipping_methods);
                                        ?>
                                            <select name="shipping_method[0]" data-index="0" id="shipping_method_0" class="shipping_method">
                                            <option value="no_shipping" <?php selected( 'no_shipping', $chosen_method ); ?> data-cost="0"><?php _e('No Shipping','wc_point_of_sale' ); ?></option>
                                            <?php
                                            foreach ($shipping_methods as $key => $method) {
                                                ?>
                                                <option value="<?php echo esc_attr( $method->id ); ?>" <?php selected( $method->id, $chosen_method ); ?> data-cost="<?php echo isset($method->cost) ? $method->cost : 0; ?>"><?php echo $method->get_title(); ?> <?php echo isset($method->cost) ? wc_price($method->cost) : ''; ?></option>
                                                <?php
                                            }
                                            ?>
                                            </select>
                                        <?php
                                        }
                                        ?>
                                        </td>
                                    </tr>
                                    <?php /********************************/ ?>
                                    <?php
                                    $wc_calc_taxes   = get_option('woocommerce_calc_taxes', 'no');
                                    $wc_pos_tax_calculation = get_option('woocommerce_pos_tax_calculation', 'disabled');
                                    if($wc_calc_taxes == 'yes' && $wc_pos_tax_calculation == 'enabled'){
                                    ?>
                                    <tr class="tax_row">
                                        <td colspan="2" class="tax_col"><table></table></td>
                                        <!-- <th class="tax_label"><?php _e('Tax', 'wc_point_of_sale'); ?></th>
                                        <td class="tax_amount"><strong id="tax_amount"></strong></td> -->
                                    </tr>
                                    <?php
                                    }

                                     if ($d = $order->get_total_discount()) { ?>
                                        <tr id="tr_order_discount">
                                            <th class="total_label"><?php _e('Order Discount', 'wc_point_of_sale'); ?>
	                                            <span id="span_clear_order_discount"></span>
                                            </th>
                                            <td class="total_amount">
                                                <input type="hidden" value="<?php echo $d; ?>" id="order_discount" name="order_discount">
                                                <strong id="formatted_order_discount"><?php echo wc_price($d, array('currency' => $order->get_order_currency())); ?></strong>
                                                
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr id="tr_order_total_label">
                                        <th class="total_label"><?php _e('Total', 'wc_point_of_sale'); ?></th>
                                        <td class="total_amount"><strong id="total_amount"></strong></td>
                                    </tr>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="wc-pos-customer-data" class="postbox ">
                        <div class="hndle">
                            <p class="add_items">
                                <select id="customer_user" class="ajax_chosen_select_customer">
                                    <option value=""><?php _e('Search Customers', 'wc_point_of_sale'); ?></option>
                                </select>
                                <a title="<?php _e('Add Customer', 'wc_point_of_sale'); ?>" class="tips" id="add_customer_to_register" type="button" data-tip="<?php _e('Add Customer', 'wc_point_of_sale'); ?>"><span><?php _e('', 'wc_point_of_sale'); ?></span></a>
                            </p>
                            <span class="clearfix"></span>
                        </div>
                        <div class="inside">
                            <div class="woocommerce_order_items_wrapper">
                                <table class="woocommerce_order_items" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr>
                                            <th class="customer" colspan="2"><?php _e('Customer Name', 'wc_point_of_sale'); ?></th>
                                            <th width="1%">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customer_items_list">
                                        <?php
                                        $user_to_add = absint($data['default_customer']);
                                        ob_start();
                                        $default_customer = pos_get_user_html($user_to_add);
                                        ob_end_clean();
                                        echo $default_customer;
                                        $default_customer = trim(preg_replace('/\s+/', ' ', $default_customer));
                                        $default_customer = str_replace("'", '"', $default_customer);

                                        ob_start();
                                        $default_guest = pos_get_user_html(0);
                                        ob_end_clean();
                                        $default_guest = trim(preg_replace('/\s+/', ' ', $default_guest));
                                        $default_guest = str_replace("'", '"', $default_guest);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <script type="text/javascript" >
                default_customer = '<?php echo $default_customer; ?>';
                default_guest    = '<?php echo $default_guest; ?>';
                </script>
            </div>
        </div>        
        <?php require_once( 'modal/html-modal-comments.php' ); ?>    
        <?php require_once( 'modal/html-modal-discount.php' ); ?>    
        <?php require_once( 'modal/html-modal-payments.php' ); ?>    
    </form>    
    <?php require_once( 'modal/html-modal-add-shipping.php' ); ?>
    <?php require_once( 'modal/html-modal-custom-product.php' ); ?>
    <?php require_once( 'modal/html-modal-product-custom-meta.php' ); ?>
    <?php require_once( 'modal/html-modal-retrieve-sales.php' ); ?>    
    <?php require_once( 'modal/html-modal-add-new-customer.php' ); ?>    
     
    <div class="overlay_order_popup" id="popup_choose_attributes">
        <div id="popup_choose_attributes_content">
            <div class="media-frame-title">
                <h1><?php _e('Select Variation', 'wc_point_of_sale'); ?></h1>
            </div>
            <span class="close_popup"></span>
            <div id="popup_choose_attributes_inner">	        
            </div>
            <div id="popup_choose_attributes_button">
	            
            </div>
        </div>
    </div>    

</div>
<script>
    var note_request = <?php echo isNoteRequest( $data['ID'] ); ?>;
    
</script>
<style>
    .keypad-popup{
        z-index: 999;
        position: fixed !important;
        bottom: 0 !important;
        top: inherit !important;
    }
</style>