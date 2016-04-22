<?php
/**
 * WoocommercePointOfSale Registers Order Class
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/Registers
 * @category	Class
 * @since     0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( !class_exists( 'WC_Checkout' ) ) {
  require_once( dirname(WC_PLUGIN_FILE) . '/includes/class-wc-checkout.php' );
}
if ( !function_exists('wc_add_notice') ) {
  include_once( dirname(WC_PLUGIN_FILE).'/includes/wc-notice-functions.php' );
}
class WC_Pos_Checkout extends WC_Checkout{
  /** @var int ID of order. */
  private $order_id;

  private $cart;
  private $customer;
  private $cur_id;
  private $chosen_shipping_methods;
  private $wc_tax_based_on;
  protected $billing_address = array();

  public function __construct ($order_id, $customer_id) {
    $this->cur_id = get_current_user_id();
    if (!$order_id ) {
      $order_id = WC_POS()->register()->crate_order_id($_POST['id_register']);
    }
    $this->discount = isset( $_POST['order_discount'] ) ? $_POST['order_discount'] : 0;
    $this->discount_percent = isset( $_POST['order_discount_percent'] ) ? (double)$_POST['order_discount_percent'] : 0;

    add_action( 'woocommerce_checkout_billing', array( $this,'checkout_form_billing' ) );
    add_action( 'woocommerce_checkout_shipping', array( $this,'checkout_form_shipping' ) );

    add_filter( 'woocommerce_available_payment_gateways', array( $this,'pos_chip_pin_payment_gateway' ), 180, 1 );
    add_filter( 'woocommerce_get_discounted_price', array( $this,'pos_get_discounted_price' ), 999, 3 );

    $this->order_id                  = $order_id;
    $this->customer_id               = $customer_id;
    $this->enable_signup             = false;
    $this->enable_guest_checkout     = true;
    $this->must_create_account       = false;
    $this->payment_method            = false;
    $this->needs_payment             = isset( $_POST['payment_method'] ) ? true : false;
    $this->order_awaiting_payment    = 0;
    //$this->old_calc_shipping         = get_option('woocommerce_calc_shipping');

    $this->wc_calc_taxes             = get_option('woocommerce_calc_taxes') ? get_option('woocommerce_calc_taxes') : 'no';
    $this->wc_pos_tax_calculation    = get_option('woocommerce_pos_tax_calculation') ? get_option('woocommerce_pos_tax_calculation') : 'disabled';

    if($this->wc_pos_tax_calculation != 'enabled'){
      add_filter( 'option_woocommerce_calc_taxes', array($this, 'woocommerce_calc_taxes_no') );
    }

    include_once( dirname(WC_PLUGIN_FILE).'/includes/abstracts/abstract-wc-session.php' );
    $session_class                   = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
    WC()->session                    = new $session_class();
    WC()->cart                       = new WC_Cart();
    WC()->customer                   = new WC_Customer();

    // Define all Checkout fields
    $this->checkout_fields['billing']   = WC()->countries->get_address_fields( $this->get_value( 'billing_country' ), 'billing_' );
    $this->checkout_fields['shipping']  = WC()->countries->get_address_fields( $this->get_value( 'shipping_country' ), 'shipping_' );


    $this->checkout_fields['order'] = array(
      'order_comments' => array(
        'type' => 'textarea',
        'class' => array('notes'),
        'label' => __( 'Order Notes', 'woocommerce' ),
        'placeholder' => _x('Notes about your order, e.g. special notes for delivery.', 'placeholder', 'woocommerce')
        )
      );

    $this->checkout_fields = apply_filters( 'woocommerce_checkout_fields', $this->checkout_fields );


    /*******/
    if(!isset( $_POST['shipping_method'] ) ){
      add_filter( 'option_woocommerce_calc_shipping', array($this, 'woocommerce_calc_shipping_no') );
    }

    $this->wc_tax_based_on = get_option('woocommerce_tax_based_on') ? get_option('woocommerce_tax_based_on') : 'shipping';
    $wc_pos_tax_based_on    = get_option('woocommerce_pos_calculate_tax_based_on', 'default');

    if($this->wc_calc_taxes == 'yes' && $this->wc_pos_tax_calculation == 'enabled'){
      if($wc_pos_tax_based_on != 'default' && $this->wc_tax_based_on != $wc_pos_tax_based_on){
        switch ($wc_pos_tax_based_on) {
          case 'shipping':
            add_filter( 'option_woocommerce_tax_based_on', array($this, 'woocommerce_tax_based_on_shipping') );
            break;
          case 'billing':
            add_filter( 'option_woocommerce_tax_based_on', array($this, 'woocommerce_tax_based_on_billing') );
            break;
          case 'base':
            add_filter( 'option_woocommerce_tax_based_on', array($this, 'woocommerce_tax_based_on_base') );
            break;
          case 'outlet':
            $id_register = (isset($_POST['id_register']) && !empty($_POST['id_register']) ) ? $_POST['id_register'] : '';
            if(!empty($id_register)){
              add_filter( 'option_woocommerce_default_country', array($this, 'woocommerce_default_country'), 999 );
              add_filter( 'woocommerce_countries_base_country', array($this, 'woocommerce_default_country'), 999 );
              add_filter( 'option_woocommerce_tax_based_on', array($this, 'woocommerce_tax_based_on_base'), 999 );
            }
            break;
        }
      }      
    }
    /*******/
    WC()->mailer()->init();
    $this->remove_email_actions();

    $this->save_new_order();
    $this->process_checkout();

  }

  public function remove_email_actions()
  {
    $email_actions = apply_filters( 'woocommerce_email_actions', array(
      'woocommerce_low_stock',
      'woocommerce_no_stock',
      'woocommerce_product_on_backorder',
      'woocommerce_order_status_pending_to_processing',
      'woocommerce_order_status_pending_to_completed',
      'woocommerce_order_status_pending_to_cancelled',
      'woocommerce_order_status_pending_to_on-hold',
      'woocommerce_order_status_failed_to_processing',
      'woocommerce_order_status_failed_to_completed',
      'woocommerce_order_status_on-hold_to_processing',
      'woocommerce_order_status_on-hold_to_cancelled',
      'woocommerce_order_status_completed',
      'woocommerce_new_customer_note',
      'woocommerce_created_customer'
    ) );
    $email_actions = apply_filters( 'woocommerce_pos_checkout_remove_email_actions', $email_actions );
    
    if( is_array($email_actions) ){
      foreach ($email_actions as $action) {
        remove_all_actions($action);
      }
    }
  }
  public function save_new_order() {
    // add to cart
    $ids      = isset( $_POST['product_item_id'] ) ? $_POST['product_item_id'] : array();
    $qty      = isset( $_POST['order_item_qty'] ) ? $_POST['order_item_qty'] : array();

    foreach ($ids as $item_key => $id ) {
      $this->add_to_cart($id, $qty[$item_key], $item_key);
    }

    if( isset($_POST['order_coupon']) && !empty($_POST['order_coupon']) && is_array($_POST['order_coupon'])){
      foreach ($_POST['order_coupon'] as $coupon) {
        WC()->cart->add_discount( $coupon );   
      }
    }
    WC()->cart->calculate_totals();
  }


    /**
   * Process the checkout after the confirm order button is pressed
   *
   * @access public
   * @return void
   */
  public function process_checkout(){
    if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) )
      define( 'WOOCOMMERCE_CHECKOUT', true );

    // Prevent timeout
    @set_time_limit(0);

    do_action( 'woocommerce_before_checkout_process' );

    if ( sizeof( WC()->cart->get_cart() ) == 0 )
      wc_add_notice( sprintf( __( 'Please add products.', 'woocommerce' ), home_url() ), 'error' );

    // Note if we skip shipping
    $skipped_shipping = false;

    do_action( 'woocommerce_checkout_process' );

    // Checkout fields (not defined in checkout_fields)
    $this->posted['terms']                     = 1;
    $this->posted['createaccount']             = 0;
    $this->posted['payment_method']            = isset( $_POST['payment_method'] ) ? stripslashes( $_POST['payment_method'] ) : '';

    if (isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ){
      foreach ( $_POST['shipping_method']  as $key => $value) {
        if($value == 'no_shipping'){
          unset($_POST['shipping_method'][$key]);
          $skipped_shipping = true;
        }
      }
      if(empty($_POST['shipping_method']) ) unset($_POST['shipping_method']);
    }

    $this->posted['shipping_method']           = isset( $_POST['shipping_method'] ) ? $_POST['shipping_method'] : '';

    $this->posted['ship_to_different_address'] = isset( $_POST['ship_to_different_address'] ) ? true : false;
    $this->posted['order_comments']            = isset( $_POST['order_comments'] ) ? $_POST['order_comments'] : '';

    if(! empty( $_POST[ 'customer_details' ] ) ){
      parse_str($_POST['customer_details'], $userdata);
      if(isset($userdata['shipping_country']) && !empty($userdata['shipping_country']))
        $this->posted['ship_to_different_address']  = true;
    }else if($this->customer_id){
      $this->posted['ship_to_different_address']  = true;
    }

    // Ship to billing only option
    if ( WC()->cart->ship_to_billing_address_only() ) {
      $this->posted['ship_to_different_address']  = false;
    }

    // Update customer shipping and payment method to posted method
    $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
    if ( isset( $this->posted['shipping_method'] ) && is_array( $this->posted['shipping_method'] ) ) {
      foreach ( $this->posted['shipping_method'] as $i => $value ) {
        $chosen_shipping_methods[ $i ] = wc_clean( $value );
      }
    }
    

    WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
    WC()->session->set( 'chosen_payment_method', $this->posted['payment_method'] );

    // Get posted checkout_fields and do validation
    foreach ( $this->checkout_fields as $fieldset_key => $fieldset ) {

      // Skip shipping if not needed
      if ( $fieldset_key == 'shipping' && !isset($_POST['custom_shipping']) ){
        if ( $this->posted['ship_to_different_address'] == false || !WC()->cart->needs_shipping() ) {
          $skipped_shipping = true;
          continue;
        }
      }

      foreach ( $fieldset as $key => $field ) {
        $this->posted[ $key ] = $this->get_value($key);
      }

    }

    // Update customer location to posted location so we can correctly check available shipping methods
    if ( isset( $this->posted['billing_country'] ) ) {
      WC()->customer->set_country( $this->posted['billing_country'] );
    }
    if ( isset( $this->posted['billing_state'] ) ) {
      WC()->customer->set_state( $this->posted['billing_state'] );
    }
    if ( isset( $this->posted['billing_postcode'] ) ) {
      WC()->customer->set_postcode( $this->posted['billing_postcode'] );
    }

    // Shipping Information
    if ( ! $skipped_shipping ) {


      // Update customer location to posted location so we can correctly check available shipping methods
      if ( isset( $this->posted['shipping_country'] ) ) {
        WC()->customer->set_shipping_country( $this->posted['shipping_country'] );
      }
      if ( isset( $this->posted['shipping_state'] ) ) {
        WC()->customer->set_shipping_state( $this->posted['shipping_state'] );
      }
      if ( isset( $this->posted['shipping_postcode'] ) ) {
        WC()->customer->set_shipping_postcode( $this->posted['shipping_postcode'] );
      }

    } else {

      // Update customer location to posted location so we can correctly check available shipping methods
      if ( isset( $this->posted['billing_country'] ) ) {
        WC()->customer->set_shipping_country( $this->posted['billing_country'] );
      }
      if ( isset( $this->posted['billing_state'] ) ) {
        WC()->customer->set_shipping_state( $this->posted['billing_state'] );
      }
      if ( isset( $this->posted['billing_postcode'] ) ) {
        WC()->customer->set_shipping_postcode( $this->posted['billing_postcode'] );
      }

    }

    // Update cart totals now we have customer address
    WC()->cart->calculate_totals();

    if ( WC()->cart->needs_shipping() ) {

      if ( ! in_array( WC()->customer->get_shipping_country(), array_keys( WC()->countries->get_shipping_countries() ) ) ) {
        wc_add_notice( sprintf( __( 'Unfortunately <strong>we do not ship %s</strong>. Please enter an alternative shipping address.', 'woocommerce' ), WC()->countries->shipping_to_prefix() . ' ' . WC()->customer->get_shipping_country() ), 'error' );
      }

      // Validate Shipping Methods
      $packages               = WC()->shipping->get_packages();
      $this->shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

      foreach ( $packages as $i => $package ) {
        if ( ! isset( $package['rates'][ $this->shipping_methods[ $i ] ] ) ) {
          wc_add_notice( __( 'Invalid shipping method.', 'woocommerce' ), 'error' );
          $this->shipping_methods[ $i ] = '';
        }
      }
    }

    if ( $this->needs_payment ) {

      // Payment Method
      $available_gateways = WC()->payment_gateways->get_available_payment_gateways();


      if ( ! isset( $available_gateways[ $this->posted['payment_method'] ] ) ) {
        $this->payment_method = '';
        wc_add_notice( __( 'Invalid payment method.', 'woocommerce' ), 'error' );
      } else {
        $this->payment_method = $available_gateways[ $this->posted['payment_method'] ];
        $this->payment_method->validate_fields();
      }
    }

    // Action after validation

      try {
        // Do a final stock check at this point
        $this->check_cart_items();

        WC()->cart->calculate_totals();

        // Abort if errors are present
        if ( wc_notice_count( 'error' ) > 0 )
          throw new Exception();

        $order_id = $this->create_order();

        if ( is_wp_error( $order_id ) ) {
          throw new Exception( $order_id->get_error_message() );
        }

        do_action( 'woocommerce_checkout_order_processed', $order_id, $this->posted );
        
        global $wpdb;
        $table_name    = $wpdb->prefix . "wc_poin_of_sale_registers";
        $new_order_id  = WC_POS()->register()->crate_order_id($_POST['id_register']);
        $rows_affected = $wpdb->update( $table_name, array('order_id' => $new_order_id), array('ID' => $_POST['id_register'] ) );

        // Process payment
        
        if ( $this->needs_payment ) {

          // Store Order ID in session so it can be re-used after payment failure
          WC()->session->order_awaiting_payment = $order_id;

          if($this->posted['payment_method'] == 'braintree'){
            $_POST['cvv']    = $_POST['braintree-cc-cvv'];
            $_POST['month']  = $_POST['braintree-cc-exp-month'];
            $_POST['year']   = $_POST['braintree-cc-exp-year'];
            $_POST['number'] = $_POST['braintree-cc-number'];

            if ( !empty($this->billing_address) ) {
              $msg = '';
              foreach ( $this->checkout_fields['billing'] as $field => $bil ) {
                $field_name = str_replace( 'billing_', '', $field );
                if( empty($this->billing_address[$field_name]) && isset($bil['label'])){
                 
                 $msg .= 'Billing ' .$bil['label'] . ' is required.<br>';
                }
              }
              if(!empty($msg)){
                echo '<!--WC_START-->' . json_encode(
                  array(
                    'result'  => 'failure',
                    'messages'  => $msg,
                    'refresh'   => isset( WC()->session->refresh_totals ) ? 'true' : 'false',
                    'reload'    => isset( WC()->session->reload_checkout ) ? 'true' : 'false'
                  )
                ) . '<!--WC_END-->';
                exit;
              }
            }else{
              echo '<!--WC_START-->' . json_encode(
                array(
                  'result'  => 'failure',
                  'messages'  => 'Please enter customer billing detail.',
                  'refresh'   => isset( WC()->session->refresh_totals ) ? 'true' : 'false',
                  'reload'    => isset( WC()->session->reload_checkout ) ? 'true' : 'false'
                )
              ) . '<!--WC_END-->';
              exit;
            }

          }


          // Process Payment
          $result = $available_gateways[ $this->posted['payment_method'] ]->process_payment( $order_id );


          // Redirect to success/confirmation/payment page
          if ( $result['result'] == 'success' ) {

            $order_data = array(
              'ID' => $order_id,
              'post_type' =>'shop_order'
              );
            wp_update_post( $order_data );

            $result = apply_filters( 'woocommerce_payment_successful_result', $result, $order_id );


            $order__ = new WC_Order( $order_id );
            add_post_meta($order_id, 'wc_pos_amount_pay', isset($_POST['amount_pay']) ? $_POST['amount_pay'] : '', true);
            add_post_meta($order_id, 'wc_pos_amount_change', isset($_POST['amount_change']) ? $_POST['amount_change'] : '', true);
            

            $count_orders = esc_attr( get_user_meta( get_current_user_id(), 'wc_pos_count_orders', true ) );
            update_user_meta( get_current_user_id(), 'wc_pos_count_orders', $count_orders+1  );
            
            $old_status = $order__->get_status();            
            $new_status = WC_Admin_Settings::get_option( 'woocommerce_pos_end_of_sale_order_status', 'processing' );

            sentEmailReceipt($_POST['id_register'], $order_id);

            $order__->update_status( $new_status, __( 'Point of Sale transaction completed.', 'woocommerce' ) );

              if( isset( $_POST['payment_print_receipt'] ) && $_POST['payment_print_receipt'] == 'yes' ){
                $result['print_receipt'] = (int)$order_id;
              }

            if ( is_ajax() ) {
              $result['new_order_id'] = $new_order_id;
              $return_url = wc_get_endpoint_url( 'order-received', '', get_permalink( wc_get_page_id( 'checkout' ) ) );
              $return_url = str_replace('http://', '', $return_url ); 
              $return_url = str_replace('https://', '', $return_url );

              if (strpos($result['redirect'],$return_url) !== false) {
                unset($result['redirect']);
                wc_add_notice( __( 'Order successful', 'wc_point_of_sale' ), 'error' );
                ob_start();
                wc_print_notices();
                $messages = ob_get_clean();
                $result['messages'] = $messages;
              }
              echo '<!--WC_START-->' . json_encode( $result ) . '<!--WC_END-->';
              wp_set_current_user( $this->cur_id );
              exit;
            } else {
              wp_redirect( $result['redirect'] );
              exit;
            }

          }

        } else {
          $order_data = array(
            'ID' => $order_id,
            'post_type' =>'shop_order'
            );
          wp_update_post( $order_data );
          wc_add_notice( __( 'Order saved', 'wc_point_of_sale' ), 'error' );          
          ob_start();
          wc_print_notices();
          $messages = ob_get_clean();
            
          // Empty the Cart
          WC()->cart->empty_cart();
          $session_class                   = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
          WC()->session                    = new $session_class();
          WC()->cart                       = new WC_Cart();
          WC()->customer                   = new WC_Customer();


          sentEmailReceipt($_POST['id_register'], $order_id);

          // Redirect to success/confirmation/payment page
          if ( is_ajax() ) {
            
            echo '<!--WC_START-->' . json_encode(
              array(
                'result'  => 'success',
                'new_order_id' => $new_order_id,
                'messages'  => isset( $messages ) ? $messages : '',
              )
            ) . '<!--WC_END-->';
            exit;
          } else {
            if ( empty( $order ) ) {
              $order = wc_get_order( $order_id );
            }
            $return_url = $order->get_checkout_order_received_url();
            wp_safe_redirect(
              apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', $return_url, $order )
            );
            exit;
          }

        }

      } catch ( Exception $e ) {
        if ( ! empty( $e ) ) {
          wc_add_notice( $e->getMessage(), 'error' );
        }
      }// try / catch

     

    // If we reached this point then there were errors
    if ( is_ajax() ) {

      // only print notices if not reloading the checkout, otherwise they're lost in the page reload
      if ( ! isset( WC()->session->reload_checkout ) ) {
        ob_start();
        wc_print_notices();
        $messages = ob_get_clean();
      }


      echo '<!--WC_START-->' . json_encode(
        array(
          'result'  => 'failure',
          'messages'  => isset( $messages ) ? $messages : '',
          'refresh'   => isset( WC()->session->refresh_totals ) ? 'true' : 'false',
          'reload'    => isset( WC()->session->reload_checkout ) ? 'true' : 'false'
        )
      ) . '<!--WC_END-->';

      unset( WC()->session->refresh_totals, WC()->session->reload_checkout );
      exit;
    }
  }

  /**
   * create_order function.
   * @access public
   * @throws Exception
   * @return int
   */
  public function create_order() {

    // Give plugins the opportunity to create an order themselves
    $order_id = $this->order_id;
    $order  = '';    

    global $wpdb;

    try {
      // Start transaction if available
      $wpdb->query( 'START TRANSACTION' );


      // Create Order (send cart variable so we can record items and reduce inventory). Only create if this is a new order, not if the payment was rejected.
      $post_status    = WC_Admin_Settings::get_option( 'wc_pos_save_order_status', 'wc-pending' );
      if(empty($post_status))
        $post_status = 'wc-pending';

      $order_data = apply_filters( 'woocommerce_new_order_data', array(
        'post_status' => $post_status,
        'post_date'   => current_time( 'mysql' ),
        'post_title'  => sprintf( __( 'Order &ndash; %s', 'woocommerce' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'woocommerce' ) ) ),
        'ping_status'   => 'closed',
        'post_excerpt'  => isset( $this->posted['order_comments'] ) ? $this->posted['order_comments'] : '',
        'post_author'   => get_current_user_id(),
        'post_password' => uniqid( 'order_' ) // Protects the post just in case
      ) );

      if ( !is_numeric( $order_id ) ){

        $order = wc_create_order( $order_data );

        if ( is_wp_error( $order ) ) {
          throw new Exception( __( 'Error: Unable to create order. Please try again.', 'woocommerce' ) );
        } else {
          $order_id = $order->id;
          do_action( 'woocommerce_new_order', $order_id );
        }
      }
      else
      {
        $order_data['ID'] = $order_id;
        wp_update_post( $order_data );
      }

      if ( empty( $order ) )
        $order = new WC_Order( $order_id );


      // Clear the old line items - we'll add these again in case they changed
      $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d )", $order_id ) );

      $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d", $order_id ) );

      // Trigger an action for the resumed order
      do_action( 'woocommerce_resume_order', $order_id );
      add_post_meta($order_id, 'wc_pos_order_type', 'POS', true);
      add_post_meta($order_id, 'wc_pos_id_register', $_POST['id_register'], true);

      if( get_post_meta($order_id, 'wc_pos_prefix_suffix_order_number', true) == '' ){
        $reg = WC_POS()->register()->get_data($_POST['id_register']);
        $reg = $reg[0];
        $order_number = '#' . $reg['detail']['prefix'] . $order_id . $reg['detail']['suffix'];
        add_post_meta($order_id, 'wc_pos_prefix_suffix_order_number', $order_number, true);
        add_post_meta($order_id, 'wc_pos_order_tax_number', $reg['detail']['tax_number'], true);
      }

      $cart_discount = 0;
      $cart_discount_tax = 0;
      // Store the line items to the new/resumed order
      foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

        $line_subtotal = $values['line_subtotal'];
        $line_subtotal_tax = $values['line_subtotal_tax'];

        if (isset($values['pos_custom_product']['original_price'])) {
          $original_price = (double)$values['pos_custom_product']['original_price'];
          $price          = (double)$values['pos_custom_product']['price'];
          if($original_price != $price){

            $line_subtotal     = $original_price * $values['quantity'];
            $pos_calc_tax = get_option( 'woocommerce_pos_tax_calculation');
            $include_tax  = get_option( 'woocommerce_prices_include_tax');

            if( $pos_calc_tax == 'enabled' ){
                $tax_class = $values['data']->get_tax_class();
                $taxes             = $this->calc_inline_tax($tax_class, $line_subtotal);
                $values['line_tax_data']['subtotal'] = $taxes;
                $line_subtotal_tax = array_sum( $taxes );
              if($include_tax == 'yes'){
                $line_subtotal -= $line_subtotal_tax;
              }
            }

            $cart_discount += ( $line_subtotal - (double)$values['line_subtotal'] );
          }
        }


        $item_id = $order->add_product(
          $values['data'],
          $values['quantity'],
          array(
            'variation' => $values['variation'],
            'totals'    => array(
              'subtotal'     => $line_subtotal,
              'subtotal_tax' => $line_subtotal_tax,
              'total'        => $values['line_total'],
              'tax'          => $values['line_tax'],
              'tax_data'     => $values['line_tax_data'] // Since 2.2
            )
          )
        );

        if ( ! $item_id ) {
          throw new Exception( __( 'Error: Unable to create order. Please try again.', 'woocommerce' ) );
        }

        // Allow plugins to add order item meta
        do_action( 'woocommerce_add_order_item_meta', $item_id, $values, $cart_item_key );
      }

      // Store fees
      foreach ( WC()->cart->get_fees() as $fee_key => $fee ) {
        $item_id = $order->add_fee( $fee );

        if ( ! $item_id ) {
          throw new Exception( __( 'Error: Unable to create order. Please try again.', 'woocommerce' ) );
        }

        // Allow plugins to add order item meta to fees
        do_action( 'woocommerce_add_order_fee_meta', $order_id, $item_id, $fee, $fee_key );
      }

      // Store shipping for all packages
      foreach ( WC()->shipping->get_packages() as $package_key => $package ) {
        if ( isset( $package['rates'][ $this->shipping_methods[ $package_key ] ] ) ) {
          $item_id = $order->add_shipping( $package['rates'][ $this->shipping_methods[ $package_key ] ] );

          if ( ! $item_id ) {
            throw new Exception( __( 'Error: Unable to create order. Please try again.', 'woocommerce' ) );
          }

          // Allows plugins to add order item meta to shipping
          do_action( 'woocommerce_add_shipping_order_item', $order_id, $item_id, $package_key );
        }
      }
      if( isset( $_POST['custom_shipping'] ) && is_array($_POST['custom_shipping'])){
        foreach ($_POST['custom_shipping'] as $ship) {
          $shipping        = new stdClass();
          $shipping->label = isset($ship[0]) ? $ship[0] : '';
          $shipping->id    = isset($ship[1]) ? $ship[1] : '';
          $shipping->cost  = isset($ship[2]) ? $ship[2] : '';
          $shipping->taxes = array();
          $item_id         = $order->add_shipping( $shipping );
          if ( ! $item_id ) {
            throw new Exception( __( 'Error: Unable to create order. Please try again.', 'woocommerce' ) );
          }

          // Allows plugins to add order item meta to shipping
          do_action( 'woocommerce_add_shipping_order_item', $order_id, $item_id, $shipping->id );
        }
      }

      // Store tax rows
      foreach ( array_keys( WC()->cart->taxes + WC()->cart->shipping_taxes ) as $tax_rate_id ) {
        if ( $tax_rate_id && ! $order->add_tax( $tax_rate_id, WC()->cart->get_tax_amount( $tax_rate_id ), WC()->cart->get_shipping_tax_amount( $tax_rate_id ) ) && apply_filters( 'woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated' ) !== $tax_rate_id ) {
          throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 405 ) );
        }
      }

      
      // Store coupons
      foreach ( WC()->cart->get_coupons() as $code => $coupon ) {
        if ( ! $order->add_coupon( $code, WC()->cart->get_coupon_discount_amount( $code ), 0 ) ) {
          throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 406 ) );
        }
      }
      
      if($this->discount){
        if ( ! $order->add_coupon( "POS Discount", $this->discount, 0 ) ) {
          throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 406 ) );
        } 
      }

     

      // Billing address
      $billing_address = array();
      if ( $this->checkout_fields['billing'] ) {
        foreach ( array_keys( $this->checkout_fields['billing'] ) as $field ) {
          $field_name = str_replace( 'billing_', '', $field );
          $billing_address[ $field_name ] = $this->get_posted_address_data( $field_name );
        }
      }
      $this->billing_address = $billing_address;

      // Shipping address.
      $shipping_address = array();
      if ( $this->checkout_fields['shipping'] ) {
        foreach ( array_keys( $this->checkout_fields['shipping'] ) as $field ) {
          $field_name = str_replace( 'shipping_', '', $field );
          $shipping_address[ $field_name ] = $this->get_posted_address_data( $field_name, 'shipping' );
        }
      }

      $order->set_address( $billing_address, 'billing' );
      $order->set_address( $shipping_address, 'shipping' );
      if ( $this->payment_method ) {
        $order->set_payment_method( $this->payment_method );
      }
        
    $order->set_total( WC()->cart->shipping_total, 'shipping' );
    
    $order->set_total( WC()->cart->get_cart_discount_total() + $cart_discount, 'cart_discount' );
    $order->set_total( WC()->cart->get_cart_discount_tax_total() + $cart_discount_tax, 'cart_discount_tax' );
         
    $order->set_total( WC()->cart->tax_total, 'tax' );
    
    $order->set_total( WC()->cart->shipping_tax_total, 'shipping_tax' );
    $order->set_total( WC()->cart->total );

    update_post_meta( $order_id, '_order_key',        'wc_' . apply_filters('woocommerce_generate_order_key', uniqid('order_') ) );
    update_post_meta( $order_id, '_customer_user',      absint( $this->customer_id ) );
    update_post_meta( $order_id, '_order_currency',     get_woocommerce_currency() );
    update_post_meta( $order_id, '_prices_include_tax',   get_option( 'woocommerce_prices_include_tax' ) );
    update_post_meta( $order_id, '_customer_ip_address',  isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] );
    update_post_meta( $order_id, '_customer_user_agent',  isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' );

    do_action( 'woocommerce_checkout_update_order_meta', $order_id, $this->posted );

    #$order->reduce_order_stock();
    $this->stock_modified($order);

      // If we got here, the order was created without problems!
      $wpdb->query( 'COMMIT' );

    } catch ( Exception $e ) {
      // There was an error adding order data!
      $wpdb->query( 'ROLLBACK' );
      return new WP_Error( 'checkout-error', $e->getMessage() );
    }
    wc_delete_shop_order_transients( $order_id );
    return $order_id;
  }

  /**
   * Get a posted address field after sanitization and validation.
   * @param string $key
   * @param string $type billing for shipping
   * @return string
   */
  public function get_posted_address_data( $key, $type = 'billing' ) {
    if ( 'billing' === $type || false === $this->posted['ship_to_different_address'] ) {
      $return = isset( $this->posted[ 'billing_' . $key ] ) ? $this->posted[ 'billing_' . $key ] : '';
    } else {
      $return = isset( $this->posted[ 'shipping_' . $key ] ) ? $this->posted[ 'shipping_' . $key ] : '';
    }
    return $return;
  }

  /**
   * Gets the value either from the posted data, or from the users meta data
   *
   * @access public
   * @param string $input
   * @return string|null
   */
  public function get_value( $input ) {

    if ( ! empty( $_POST[ $input ] ) ) {
      return wc_clean( $_POST[ $input ] );

    } else {

      $value = apply_filters( 'woocommerce_checkout_get_value', null, $input );

      if ( $value !== null ) {
        return $value;
      }

      // Get the billing_ and shipping_ address fields
      $address_fields = array_merge( WC()->countries->get_address_fields(), WC()->countries->get_address_fields( '', 'shipping_' ) );

      if ( $this->customer_id && array_key_exists( $input, $address_fields ) ) {
        $current_user = wp_get_current_user();

        if ( $meta = get_user_meta( $this->customer_id, $input, true ) ) {
          return $meta;
        }

        if ( $input == "billing_email" ){
          $user_info = get_userdata($this->customer_id);
          return $user_info->user_email;
        }
      }
      if(! empty( $_POST[ 'customer_details' ] ) ){
        parse_str($_POST['customer_details'], $userdata);
        if ( $input == "billing_email" && isset($userdata['billing_email']) ){
          return $userdata['billing_email'];
        }
      }

      switch ( $input ) {
        case 'billing_country' :
          return apply_filters( 'default_checkout_country', isset($userdata['billing_country']) ? $userdata['billing_country'] : WC()->countries->get_base_country(), 'billing' );
        case 'billing_state' :
          return apply_filters( 'default_checkout_state', isset($userdata['billing_state']) ? $userdata['billing_state'] : '', 'billing' );
        case 'billing_postcode' :
          return apply_filters( 'default_checkout_postcode', isset($userdata['billing_postcode']) ? $userdata['billing_postcode'] : '', 'billing' );
        case 'shipping_country' :
          return apply_filters( 'default_checkout_country', isset($userdata['shipping_country']) ? $userdata['shipping_country'] : WC()->countries->get_base_country(), 'shipping' );
        case 'shipping_state' :
          return apply_filters( 'default_checkout_state', isset($userdata['shipping_state']) ? $userdata['shipping_state'] : '', 'shipping' );
        case 'shipping_postcode' :
          return apply_filters( 'default_checkout_postcode', isset($userdata['shipping_postcode']) ? $userdata['shipping_postcode'] : '', 'shipping' );
        default :
          return apply_filters( 'default_checkout_' . $input, isset($userdata[$input]) ? $userdata[$input] : '', $input );
      }
    }
  }


  function update_order_item_meta_name($item_id, $item_name)
    {
      global $wpdb;
      $wpdb->update(
        $wpdb->prefix . "woocommerce_order_items",
        array('order_item_name' => $item_name),
        array('order_item_id'   => $item_id)
      );
    }

  /**
   * Add to cart
   */
  public function add_to_cart($product_id, $qty = 1, $item_key) {

    $product_parent_id  = wp_get_post_parent_id( $product_id );
    $variation_id       = $product_parent_id ? $product_id : '';
    $product_id         = $product_parent_id ? $product_parent_id : $product_id;
    $adding_to_cart     = wc_get_product( $product_id );
    $quantity           = empty( $qty ) ? 1 : wc_stock_amount( $qty );
    $missing_attributes = array();
    $variations         = array();
    $attributes         = $adding_to_cart->get_attributes();
    $variation          = wc_get_product( $variation_id );

    $this->custom_product_id = $product_id;

    // Verify all attributes
    foreach ( $attributes as $attribute ) {
      if ( ! $attribute['is_variation'] ) {
        continue;
      }

      $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

      if ( isset( $_REQUEST[ 'variations' ][$item_key][$taxonomy] ) ) {
        $request_variations = $_REQUEST[ 'variations' ][$item_key][$taxonomy];
        // Get value from post data
        if ( $attribute['is_taxonomy'] ) {
          // Don't use wc_clean as it destroys sanitized characters
          $value = sanitize_title( stripslashes( $request_variations ) );
        } else {
          $value = wc_clean( stripslashes( $request_variations ) );
        }

        // Get valid value from variation
        $valid_value = $variation->variation_data[ $taxonomy ];

        // Allow if valid
        if ( '' === $valid_value || $valid_value === $value ) {
          $variations[ $taxonomy ] = $value;
          unset($_REQUEST[ 'variations' ][$item_key][$taxonomy]);
          continue;
        }

      } else {
        $missing_attributes[] = wc_attribute_label( $attribute['name'] );
      }
    }

    if(!empty($_REQUEST[ 'variations' ][$item_key])){
      foreach ($_REQUEST[ 'variations' ][$item_key] as $taxonomy => $value) {
        $variations[ $taxonomy ] = $value;
      }
    }

    // Add to cart validation
    if( !empty($variation_id) ){
      $passed_validation  = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations ); 
    }else{
      $passed_validation  = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
    }

    if ( $passed_validation ) {
      $custom_id = (int)get_option('wc_pos_custom_product_id');

      if($product_id == $custom_id && empty($variations) ){
        $variations['_pos_custom_product'] = $item_key;
      }
      $cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations );

      if($cart_item_key !== false && isset($_REQUEST[ 'order_item_price' ][$item_key]) ){

        $price = $_REQUEST[ 'order_item_price' ][$item_key];
        $len   = strlen($price) - wc_get_price_decimals() - 1;
        $dec   = substr($price, $len);
        $price = substr($price, 0, $len);
        
        $price = str_replace(wc_get_price_thousand_separator(), '', $price) ;

        if(isset(WC()->cart->cart_contents[$cart_item_key])){
          if($product_id != $custom_id ){

            if( !empty($variation_id) ){
              $prod = get_product($variation_id);
            } else {
              $prod = get_product($product_id);
            }

            WC()->cart->cart_contents[$cart_item_key]['pos_custom_product']['original_price'] = $prod->get_price();
          }
            
          WC()->cart->cart_contents[$cart_item_key]['data']->set_price( $price . $dec );
          WC()->cart->cart_contents[$cart_item_key]['pos_custom_product']['price'] = $price . $dec;
          
        }
      }
      if( $cart_item_key !== false && isset($_REQUEST[ 'custom_product_name' ][$item_key]) ){
        WC()->cart->cart_contents[$cart_item_key]['data']->post->post_title = $_REQUEST[ 'custom_product_name' ][$item_key];
      }
    }

    $this->custom_product_id = 0;

  }
  public function check_cart_items() {
    // When we process the checkout, lets ensure cart items are rechecked to prevent checkout
    WC()->cart->check_cart_items();
  }
  

  public function stock_modified( $order ) {
      
    $post_modified     = current_time( 'mysql' );
    $post_modified_gmt = current_time( 'mysql', 1 );

    foreach ( $order->get_items() as $item ) {
      // TODO: if variable, update the parent?
      $id = isset( $item['variation_id'] ) && is_numeric( $item['variation_id'] ) && $item['variation_id'] > 0 ? $item['variation_id'] : $item['product_id'] ;

      wp_update_post( array(
        'ID'                => $id,
        'post_modified'     => $post_modified,
        'post_modified_gmt' => $post_modified_gmt
      ));
    }
  }


  /*******************option hooks********************/
  function woocommerce_calc_shipping_no($val){
    $val = 'no';
    return $val;
  }
  function woocommerce_tax_based_on_shipping($val){
    $val = 'shipping';
    return $val;
  }
  function woocommerce_tax_based_on_billing($val){
    $val = 'billing';
    return $val;
  }
  function woocommerce_tax_based_on_base($val){
    if($val != 'base'){
      $val = 'base';
    }
    return $val;
  }
  function woocommerce_default_country($val){
    $id_register = (isset($_POST['id_register']) && !empty($_POST['id_register']) ) ? $_POST['id_register'] : '';
    if(!empty($id_register)){ 
      $outlet_ID      = $_POST['outlet_ID'];
      $outlet_data    = WC_POS()->outlet()->get_data($outlet_ID);
      $outlet_country = $outlet_data[0]['contact']['country'];
      $outlet_state   = isset($outlet_data[0]['contact']['state']) ? $outlet_data[0]['contact']['state'] : '';
      $val = $outlet_country;
      if( !empty($outlet_state)){
        $states = WC()->countries->get_states( $outlet_country );
        
        if( $states && isset($states[$outlet_state]) ){
          $val = $outlet_country . ":" . $outlet_state;
        }        
      }
    }
    return $val;
  }
  function woocommerce_calc_taxes_no($val){
    $val = 'no';
    return $val;
  }
  /***********************************************/

  public function pos_chip_pin_payment_gateway($_available_gateways)
  {
    include_once 'class-wc-pos-gateway-chip-pin.php';
    $gateway = new pos_chip_pin_gateway();
    $_available_gateways[ $gateway->id ] = $gateway;
    return $_available_gateways;
  }

  public function pos_get_discounted_price($price, $values, $cart)
  {

    if ( ! $price ) {
      return $price;
    }
    if($this->discount_percent){
      $price = (double)$price;
      
      $discount_amount = $price * $this->discount_percent / 100;
      

      $price           = max( $price - $discount_amount, 0 );

              $product = $values['data'];
              $total_discount     = $discount_amount * $values['quantity'];
              $total_discount_tax = 0;

              if ( wc_tax_enabled() ) {
                $tax_rates          = WC_Tax::get_rates( $product->get_tax_class() );
                $taxes              = WC_Tax::calc_tax( $discount_amount, $tax_rates, $cart->prices_include_tax );
                $total_discount_tax = WC_Tax::get_tax_total( $taxes ) * $values['quantity'];
                $total_discount     = $cart->prices_include_tax ? $total_discount - $total_discount_tax : $total_discount;
                WC()->cart->discount_cart_tax += $total_discount_tax;
              }

              WC()->cart->discount_cart     += $total_discount;
    }
    
    return $price;
  }

  private function calc_inline_tax($_tax_class, $line_subtotal)
  {
    
    $subtotal_tax      = array();
    //var_dump($_tax_class);
    
    $shipping_country = isset( $this->posted['shipping_country'] ) ? $this->posted['shipping_country'] : '';
    $billing_country  = $this->posted['billing_country'];
    $country          = WC()->countries->get_base_country();
    
    $state            = '';
    $postcode         = '';
    $city             = '';

    $c = explode(':', $country);
    if( count($c) > 1 ){
      $country = $c[0];
      $state   = $c[1];
    }
    if ( $shipping_country ) {
      $country  = $shipping_country;
      $state    = isset( $this->posted['shipping_state'] ) ? $this->posted['shipping_state'] : '';
      $postcode = isset( $this->posted['shipping_postcode'] ) ? $this->posted['shipping_postcode'] : '';
      $city     = isset( $this->posted['shipping_city'] ) ? $this->posted['shipping_city'] : '';
    } else if ( $billing_country ) {
      $country  = $billing_country;
      $state    = $this->posted['billing_state'];
      $postcode = $this->posted['billing_postcode'];
      $city     = $this->posted['billing_city'];
    }

      $tax_rates = WC_Tax::find_rates( array(
        'country'   => $country,
        'state'     => $state,
        'postcode'  => $postcode,
        'city'      => $city,
        'tax_class' => $_tax_class
      ) );
      

      $line_subtotal_taxes = WC_Tax::calc_tax( $line_subtotal, $tax_rates, false );

      // Set the new subtotal_tax
      foreach ( $line_subtotal_taxes as $_tax_id => $_tax_value ) {
        $subtotal_tax[ $_tax_id ] = $_tax_value;
      }

      $subtotal_tax = array_map( 'wc_format_decimal', $subtotal_tax );
    return $subtotal_tax;
  }

}