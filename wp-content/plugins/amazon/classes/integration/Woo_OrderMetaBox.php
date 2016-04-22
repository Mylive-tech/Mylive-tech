<?php
/**
 * add amazon metabox to order edit page
 */

class WPLA_Order_MetaBox {
	var $providers;

	/**
	 * Constructor
	 */
	function __construct() {

		add_action( 'add_meta_boxes',                       array( &$this, 'add_meta_box' ) );
        add_action( 'wp_ajax_wpla_update_amazon_shipment', 	array( &$this, 'update_amazon_shipment' ) ); 
        add_action( 'wp_ajax_wpla_submit_order_to_fba', 	array( &$this, 'submit_order_to_fba' ) ); 

		// handle order status changed to "completed" - and complete Amazon order
		add_action( 'woocommerce_order_status_completed', array( &$this, 'handle_woocommerce_order_status_update_completed' ), 0, 1 );

		// add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'save_meta_box' ), 0, 2 );
	}

	static function getShippingProviders() {

		$providers = array(
			'Blue Package',
			'USPS',
			'UPS',
			'UPSMI',
			'FedEx',
			'DHL',
			'DHL Global Mail',
			'Fastway',
			'UPS Mail Innovations',
			'Lasership',
			'Royal Mail',
			'FedEx SmartPost',
			'OSM',
			'OnTrac',
			'Streamlite',
			'Newgistics',
			'Canada Post',
			'City Link',
			'GLS',
			'GO!',
			'Hermes Logistik Gruppe',
			'Parcelforce',
			'TNT',
			'Target',
			'SagawaExpress',
			'NipponExpress',
			'YamatoTransport',
			'Other'
		);

		return $providers;
	}


	/**
	 * Add the meta box for shipment info on the order page
	 *
	 * @access public
	 */
	function add_meta_box() {
		global $post;
		if ( ! isset( $_GET['post'] ) ) return;

		// check if this is an order created by WP-Lister for Amazon
		$amazon_order_id = get_post_meta( $post->ID, '_wpla_amazon_order_id', true );
		if ( $amazon_order_id ) {

			// show meta box for Amazon orders
			$title = __('Amazon', 'wpla') . ' <small style="color:#999"> #' . $amazon_order_id . '</small>';
			add_meta_box( 'woocommerce-amazon-details', $title, array( &$this, 'meta_box_for_amazon_orders' ), 'shop_order', 'side', 'core');		

		} elseif ( get_option( 'wpla_fba_enabled' ) ) {

			// show FBA meta box for Non-Amazon orders
			$title = __('Amazon', 'wpla');
			add_meta_box( 'woocommerce-amazon-details', $title, array( &$this, 'meta_box_for_non_amazon_orders' ), 'shop_order', 'side', 'core');		

		}

	}


	/**
	 * Show the FBA meta box for Non-Amazon orders
	 *
	 * @access public
	 */
	function meta_box_for_non_amazon_orders() {
		global $post;

		// check if order is eligible to be fillfilled via FBA
		$checkresult = WPLA_FbaHelper::orderCanBeFulfilledViaFBA( $post );
		if ( ! is_array( $checkresult ) ) {
	        echo '<p>' . $checkresult . '</p>';
	        return;
		}

		// all right - this order can be fulfilled via FBA
		$items_available_on_fba = $checkresult;
        echo '<p>' . __('This order can be fulfilled by Amazon.', 'wpla') . '</p>';

        echo '<table style="width:100%">';
    	echo '<tr>';
    	echo '<th style="text-align:left;">'.'ASIN'.'</th>';
    	echo '<th style="text-align:left;">'.'Purchased'.'</th>';
    	echo '<th style="text-align:left;">'.'FBA Qty'.'</th>';
    	echo '</tr>';
        foreach ( $items_available_on_fba as $listing ) {
        	echo '<tr>';
        	echo '<td>'.$listing->asin.'</td>';
        	echo '<td>'.$listing->purchased_qty.'</td>';
        	echo '<td>'.$listing->fba_quantity.'</td>';
        	echo '</tr>';
        }
        echo '</table>';

        // DeliverySLA option
        $default_sla = get_option( 'wpla_fba_default_delivery_sla', 'Standard' );
		echo '<p class="form-field wpla_DeliverySLA_field"><label for="wpla_DeliverySLA">' . __('Shipping service', 'wpla') . '</label><br/><select id="wpla_DeliverySLA" name="wpla_DeliverySLA" class="chosen_select" style="width:100%;">';
		echo '<option value="Standard"  '.( $default_sla == 'Standard'  ? 'selected' : '').' > '  . __('Standard', 'wpla') . ' (3-5 business days)</option>';
		echo '<option value="Expedited" '.( $default_sla == 'Expedited' ? 'selected' : '').' > ' . __('Expedited', 'wpla') . ' (2 business days)</option>';
		echo '<option value="Priority"  '.( $default_sla == 'Priority'  ? 'selected' : '').' > '  . __('Priority', 'wpla') . ' (1 business day)</option>';
		echo '</select> ';

		woocommerce_wp_text_input( array(
			'id' 			=> 'wpla_NotificationEmail',
			'label' 		=> __('Notification Email', 'wpla'),
			'placeholder' 	=> '',
			'description' 	=> '',
			'value'			=> get_post_meta( $post->ID, '_billing_email', true )
		) );

		woocommerce_wp_text_input( array(
			'id' 			=> 'wpla_DisplayableOrderComment',
			'label' 		=> __('Packing Slip Comment', 'wpla'),
			'placeholder' 	=> 'Thank you for your order.',
			'description' 	=> '',
			'value'			=> get_option( 'wpla_fba_default_order_comment' )
		) );


        echo '<p>';
        echo '<div id="btn_submit_order_to_fba_spinner" style="float:right;display:none"><img src="'.WPLA_URL.'/img/ajax-loader.gif"/></div>';
        echo '<div class="spinner"></div>';
        echo '<a href="#" id="btn_submit_order_to_fba" class="button button-primary">'.'Submit to FBA'.'</a>';
        echo '<div id="amazon_result_info" class="updated" style="display:none"><p></p></div>';
        echo '</p>';

        wc_enqueue_js("

            var wpla_submitOrderToFBA = function ( post_id ) {

				var wpla_DeliverySLA             = jQuery('#wpla_DeliverySLA').val();
				var wpla_NotificationEmail       = jQuery('#wpla_NotificationEmail').val();
				var wpla_DisplayableOrderComment = jQuery('#wpla_DisplayableOrderComment').val();

                // prepare request
                var params = {
					action: 'wpla_submit_order_to_fba',
					order_id: post_id,
					wpla_DeliverySLA: wpla_DeliverySLA,
					wpla_NotificationEmail: wpla_NotificationEmail,
					wpla_DisplayableOrderComment: wpla_DisplayableOrderComment,
                    nonce: 'TODO'
                };
                var jqxhr = jQuery.getJSON( ajaxurl, params )
                .success( function( response ) { 

                    jQuery('#woocommerce-amazon-details .spinner').hide();

                    if ( response.success ) {

                        var logMsg = 'Order was submitted to Amazon.';
                        jQuery('#amazon_result_info p').html( logMsg );
	                    jQuery('#amazon_result_info').addClass( 'updated' ).removeClass('error');
                        jQuery('#amazon_result_info').slideDown();
                        jQuery('#btn_submit_order_to_fba').hide('fast');

                    } else {

                        var logMsg = '<b>There was a problem submitting this order to Amazon</b><br><br>'+response.error;
                        jQuery('#amazon_result_info p').html( logMsg );
                        jQuery('#amazon_result_info').addClass( 'error' ).removeClass('updated');
                        jQuery('#amazon_result_info').slideDown();

                        jQuery('#btn_submit_order_to_fba').removeClass('disabled');
                    }


                })
                .error( function(e,xhr,error) { 
                    jQuery('#amazon_result_info p').html( 'The server responded: ' + e.responseText + '<br>' );
                    jQuery('#amazon_result_info').addClass( 'error' ).removeClass('updated');
                    jQuery('#amazon_result_info').slideDown();

                    jQuery('#woocommerce-amazon-details .spinner').hide();
                    jQuery('#btn_submit_order_to_fba').removeClass('disabled');

                    console.log( 'error', xhr, error ); 
                    console.log( e.responseText ); 
                });

            }

            jQuery('#btn_submit_order_to_fba').click(function(){

                var post_id = jQuery('#post_ID').val();

                // jQuery('#btn_submit_order_to_fba_spinner').show();
                jQuery('#woocommerce-amazon-details .spinner').show();
                jQuery(this).addClass('disabled');
                wpla_submitOrderToFBA( post_id );

                return false;
            });


        ");

	} // meta_box_for_non_amazon_orders()


	/**
	 * Show the meta box for shipment info on the order page
	 *
	 * @access public
	 */
	function meta_box_for_amazon_orders() {
		global $post;

		$amazon_order_id    = get_post_meta( $post->ID, '_wpla_amazon_order_id', true );
		$selected_provider  = get_post_meta( $post->ID, '_wpla_tracking_provider', true );
		$shipping_providers = apply_filters( 'wpla_available_shipping_providers', self::getShippingProviders() );

		// get order details
		$om    = new WPLA_OrdersModel();
		$order = $om->getOrderByOrderID( $amazon_order_id );

        if ( $order ) {

	        // display amazon account
	        $account = WPLA_AmazonAccount::getAccount( $order->account_id );
	        if ( $account ) {
		        echo '<p>';
		        echo __('This order was placed on Amazon.', 'wpla');
		        echo '('.$account->title.')';
		        echo ' [<a href="admin.php?page=wpla-orders&s='.$amazon_order_id.'" target="_blank">view</a>]';
		        echo '</p>';
	        }

			// check for FBA
        	$order_details = json_decode( $order->details );
	        if ( is_object( $order_details ) && ( $order_details->FulfillmentChannel == 'AFN' ) ) {
		        echo '<p>';
		        echo __('This order is fulfilled by Amazon.', 'wpla');
		        echo '</p>';
		        return;
	        }

        }

		echo '<p class="form-field wpla_tracking_provider_field"><label for="wpla_tracking_provider">' . __('Shipping service', 'wpla') . ':</label><br/><select id="wpla_tracking_provider" name="wpla_tracking_provider" class="chosen_select" style="width:100%;">';

		echo '<option value="">-- ' . __('Select shipping service', 'wpla') . ' --</option>';
		foreach ( $shipping_providers as $provider ) {
			echo '<option value="' . $provider . '" ' . selected( $provider, $selected_provider, true ) . '>' . $provider . '</option>';
		}

		echo '</select> ';

		woocommerce_wp_text_input( array(
			'id' 			=> 'wpla_tracking_service_name',
			'label' 		=> __('Service provider', 'wpla'),
			'placeholder' 	=> '',
			'description' 	=> '',
			'value'			=> get_post_meta( $post->ID, '_wpla_tracking_service_name', true )
		) );

		woocommerce_wp_text_input( array(
			'id' 			=> 'wpla_tracking_number',
			'label' 		=> __('Tracking ID', 'wpla'),
			'placeholder' 	=> '',
			'description' 	=> '',
			'value'			=> get_post_meta( $post->ID, '_wpla_tracking_number', true )
		) );

		$dt = new DateTime('now', new DateTimeZone('UTC'));
		woocommerce_wp_text_input( array(
			'id' 			=> 'wpla_date_shipped',
			'label' 		=> __('Shipping date', 'wpla'),
			'placeholder' 	=> 'Current date: ' . $dt->format('Y-m-d'),
			'description' 	=> '',
			'class'			=> 'date-picker-field',
			'value'			=> get_post_meta( $post->ID, '_wpla_date_shipped', true )
		) );

		woocommerce_wp_text_input( array(
			'id' 			=> 'wpla_time_shipped',
			'label' 		=> __('Shipping time', 'wpla'),
			'placeholder' 	=> 'Current time: ' . $dt->format('H:i:s') . ' UTC',
			'description' 	=> '',
			'class'			=> 'time-picker-field',
			'value'			=> get_post_meta( $post->ID, '_wpla_time_shipped', true )
		) );

		// woocommerce_wp_checkbox( array( 'id' => 'wpla_update_amazon_on_save', 'wrapper_class' => 'update_amazon', 'label' => __('Update on save?', 'wpla') ) );

		// show submission status if it exists
        if ( $submission_status = get_post_meta( $post->ID, '_wpla_submission_result', true ) ) {
	        echo '<p>';
	        if ( $submission_status == 'success' ) {
		        echo 'Submitted to Amazon: yes';
	        } else {
	        	$history = maybe_unserialize( $submission_status );
		        echo 'Submission Log:';
		        echo '<div style="color:darkred; font-size:0.8em;">';
	            if ( is_array( $history ) ) {
	                foreach ($history['errors'] as $result) {
	                    echo '<b>'.$result['error-type'].':</b> '.$result['error-message'].' ('.$result['error-code'].')<br>';
	                }
	                foreach ($history['warnings'] as $result) {
	                    echo '<b>'.$result['error-type'].':</b> '.$result['error-message'].' ('.$result['error-code'].')<br>';
	                }
	            }
		        echo '</div>';        	
	        }
	        echo '</p>';        	
        }

        echo '<p>';
        echo '<div id="btn_update_amazon_shipment_spinner" style="float:right;display:none"><img src="'.WPLA_URL.'/img/ajax-loader.gif"/></div>';
        echo '<div class="spinner"></div>';
        echo '<a href="#" id="btn_update_amazon_shipment" class="button button-primary">'.'Mark as shipped on Amazon'.'</a>';
        echo '<div id="amazon_result_info" class="updated" style="display:none"><p></p></div>';
        echo '</p>';

        wc_enqueue_js("

            var wpla_updateAmazonFeedback = function ( post_id ) {


                var tracking_provider 		= jQuery('#wpla_tracking_provider').val();
                var tracking_service_name 	= jQuery('#wpla_tracking_service_name').val();
                var tracking_number 		= jQuery('#wpla_tracking_number').val();
                var date_shipped 			= jQuery('#wpla_date_shipped').val();
                var time_shipped 			= jQuery('#wpla_time_shipped').val();
                
                // load task list
                var params = {
                    action: 'wpla_update_amazon_shipment',
                    order_id: post_id,
                    wpla_tracking_provider: tracking_provider,
                    wpla_tracking_service_name: tracking_service_name,
                    wpla_tracking_number: tracking_number,
                    wpla_date_shipped: date_shipped,
                    wpla_time_shipped: time_shipped,
                    nonce: 'TODO'
                };
                var jqxhr = jQuery.getJSON( ajaxurl, params )
                .success( function( response ) { 

                    // jQuery('#btn_update_amazon_shipment_spinner').hide();
                    jQuery('#woocommerce-amazon-details .spinner').hide();

                    if ( response.success ) {

                        var logMsg = 'Shipping status was updated and will be submitted to Amazon.';
                        jQuery('#amazon_result_info p').html( logMsg );
	                    jQuery('#amazon_result_info').addClass( 'updated' ).removeClass('error');
                        jQuery('#amazon_result_info').slideDown();
                        jQuery('#btn_update_amazon_shipment').hide('fast');

                    } else {

                        var logMsg = '<b>There was a problem updating this order on Amazon</b><br><br>'+response.error;
                        jQuery('#amazon_result_info p').html( logMsg );
                        jQuery('#amazon_result_info').addClass( 'error' ).removeClass('updated');
                        jQuery('#amazon_result_info').slideDown();

                        jQuery('#btn_update_amazon_shipment').removeClass('disabled');
                    }


                })
                .error( function(e,xhr,error) { 
                    jQuery('#amazon_result_info p').html( 'The server responded: ' + e.responseText + '<br>' );
                    jQuery('#amazon_result_info').addClass( 'error' ).removeClass('updated');
                    jQuery('#amazon_result_info').slideDown();

                    // jQuery('#btn_update_amazon_shipment_spinner').hide();
                    jQuery('#woocommerce-amazon-details .spinner').hide();
                    jQuery('#btn_update_amazon_shipment').removeClass('disabled');

                    console.log( 'error', xhr, error ); 
                    console.log( e.responseText ); 
                });

            }

            jQuery('#btn_update_amazon_shipment').click(function(){

                var post_id = jQuery('#post_ID').val();

                // jQuery('#btn_update_amazon_shipment_spinner').show();
                jQuery('#woocommerce-amazon-details .spinner').show();
                jQuery(this).addClass('disabled');
                wpla_updateAmazonFeedback( post_id );

                return false;
            });

            jQuery('#wpla_tracking_provider').change(function(){

                var tracking_provider = jQuery('#wpla_tracking_provider').val();
                // alert(tracking_provider);

                if ( tracking_provider == 'Other' ) {
	                jQuery('.wpla_tracking_service_name_field').slideDown();
                } else {
	                jQuery('.wpla_tracking_service_name_field').slideUp();
                }

                return false;
            });
            jQuery('.wpla_tracking_service_name_field').hide();

            // fix jQuery datepicker today button
			jQuery('button.ui-datepicker-current').live('click', function() {
			    jQuery.datepicker._curInst.input.datepicker('setDate', new Date()).datepicker('hide').blur();
			});

        ");
	
	} // meta_box_for_amazon_orders()


	// handle order status changed to "completed" - and complete amazon order
    public function handle_woocommerce_order_status_update_completed( $post_id ) {

    	// check if auto complete option is enabled
    	if ( get_option( 'wpla_auto_complete_sales' ) != 1 ) return;

    	// check if default status for new created orders is completed - skip further processing if it is
		if ( get_option( 'wpla_new_order_status', 'completed' ) == 'completed' ) return;

    	// check if this order came in from amazon
    	if ( ! get_post_meta( $post_id, '_wpla_amazon_order_id', true ) ) return;

    	// check if this order has already been submitted to Amazon
    	if ( get_post_meta( $post_id, '_wpla_date_shipped', true ) != '' ) return;

		// set shipping date and time to now
		$dt = new DateTime('now', new DateTimeZone('UTC'));
		update_post_meta( $post_id, '_wpla_date_shipped', 			$dt->format('Y-m-d') );
		update_post_meta( $post_id, '_wpla_time_shipped', 			$dt->format('H:i:s') );
		update_post_meta( $post_id, '_wpla_tracking_provider', 		get_option( 'wpla_default_shipping_provider', '' ) );
		update_post_meta( $post_id, '_wpla_tracking_service_name', 	get_option( 'wpla_default_shipping_service_name', '' ) );

		// update shipment feed
		$feed = new WPLA_AmazonFeed();
		$feed->updateShipmentFeed( $post_id );

    }

    /**
     * update shipping date and tracking details on amazon (ajax)
     */
    function update_amazon_shipment() {

		// get field values
        $post_id 					= $_REQUEST['order_id'];
		$wpla_tracking_provider		= trim( esc_attr( $_REQUEST['wpla_tracking_provider'] ) );
		$wpla_tracking_number 		= trim( esc_attr( $_REQUEST['wpla_tracking_number'] ) );
		$wpla_date_shipped			= trim( esc_attr( $_REQUEST['wpla_date_shipped'] ) );
		$wpla_time_shipped			= trim( esc_attr( $_REQUEST['wpla_time_shipped'] ) );
		$wpla_tracking_service_name	= trim( esc_attr( $_REQUEST['wpla_tracking_service_name'] ) );

		// if tracking number is set, but date is missing, set date to today.
		if ( $wpla_tracking_number && ! $wpla_date_shipped ) {
			$wpla_date_shipped = date('Y-m-d');
		}

		// update order data
		update_post_meta( $post_id, '_wpla_tracking_provider', 		$wpla_tracking_provider );
		update_post_meta( $post_id, '_wpla_tracking_number', 		$wpla_tracking_number );
		update_post_meta( $post_id, '_wpla_date_shipped', 			$wpla_date_shipped );
		update_post_meta( $post_id, '_wpla_time_shipped', 			$wpla_time_shipped );
		update_post_meta( $post_id, '_wpla_tracking_service_name', 	$wpla_tracking_service_name );


		$response = new stdClass();

		if ( ! $wpla_date_shipped ) {
			$response->success = false;
			$response->error = 'You need to select a shipping date.';
		} else {
			$feed = new WPLA_AmazonFeed();
			$feed->updateShipmentFeed( $post_id );
			$response->success = true;
		}

        $this->returnJSON( $response );
        exit();

    } // update_amazon_shipment()


    /**
     * submit order to be fulfilled via FBA (ajax)
     */
    function submit_order_to_fba() {

		// get field values
        $post_id = $_REQUEST['order_id'];

		// update order data
		update_post_meta( $post_id, '_wpla_DeliverySLA', 			 trim( esc_attr( $_REQUEST['wpla_DeliverySLA'] ) ) );
		update_post_meta( $post_id, '_wpla_NotificationEmail', 		 trim( esc_attr( $_REQUEST['wpla_NotificationEmail'] ) ) );
		update_post_meta( $post_id, '_wpla_DisplayableOrderComment', trim( esc_attr( $_REQUEST['wpla_DisplayableOrderComment'] ) ) );
		// update_post_meta( $post_id, '_wpla_fba_submission_status',   'submitted' );

		// create FBA feed
		$response = WPLA_FbaHelper::submitOrderToFBA( $post_id );

		// if ( $missing ) {
		// 	$response = new stdClass();
		// 	$response->success = false;
		// 	$response->error = 'You need to select a shipping date.';
		// }

        $this->returnJSON( $response );
        exit();

    } // submit_order_to_fba()

    public function returnJSON( $data ) {
        header('content-type: application/json; charset=utf-8');
        echo json_encode( $data );
    }
    

} // class WPLA_Order_MetaBox
// $WPLA_Order_MetaBox = new WPLA_Order_MetaBox();
