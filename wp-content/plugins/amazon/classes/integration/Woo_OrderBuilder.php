<?php

class WPLA_OrderBuilder {

	var $logger;
	var $vat_enabled = false;
	var $vat_total   = 0;

	public function __construct() {
		//parent::__construct();
		global $wpla_logger;
		$this->logger = &$wpla_logger;
	}


	public function updateOrders( $orders ) {

		foreach ( $orders as $order ) {

			//if ($cat['parent_id']==0) # only top level for now
			// $data = $this->mapAmazonOrderToWoo( $order );
			// $this->updateOrder( $data );

		}

	} // updateOrders()


	public function createOrders( $orders ) {

		foreach ( $orders as $order ) {

			$this->createWooOrderFromAmazonOrder( $order );

		}

	} // createOrders()



	//
	// create woo order from amazon order
	// 
	function createWooOrderFromAmazonOrder( $id ) {
		global $wpdb;

		// get order details
		$ordersModel = new WPLA_OrdersModel();		
		$item        = $ordersModel->getItem( $id );
		$details     = $item['details'];

		$timestamp     = strtotime($item['date_created'].' UTC');
		$post_date     = $ordersModel->convertTimestampToLocalTime( $timestamp );
		$post_date_gmt = date_i18n( 'Y-m-d H:i:s', $timestamp, true );
		// $date_created  = $item['date_created'];
		// $post_date_gmt = date_i18n( 'Y-m-d H:i:s', strtotime($item['date_created'].' UTC'), true );
		// $post_date     = date_i18n( 'Y-m-d H:i:s', strtotime($item['date_created'].' UTC'), false );

		// create order comment
		$order_comment = 'Amazon Order ID: ' . $item['order_id'];

		// Create shop_order post object
		$post_data = array(
			'post_title'     => 'Order &ndash; '.date('F j, Y @ h:i A', strtotime( $post_date ) ),
			'post_content'   => '',
			'post_excerpt'   => stripslashes( $order_comment ),
			'post_date'      => $post_date, //The time post was made.
			'post_date_gmt'  => $post_date_gmt, //The time post was made, in GMT.
			'post_type'      => 'shop_order',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_status'    => function_exists('wc_create_order') ? 'wc-pending' : 'publish' // WC2.2 support
		);

		// Insert order into the database
		$post_id = wp_insert_post( $post_data );

		// Update wp_order_id of order record
		$ordersModel->updateWpOrderID( $id, $post_id );	
		// $ordersModel->updateOrder( $id, array( 'post_id' => $post_id ) );

		/* the following code is inspired by woocommerce_process_shop_order_meta() in writepanel-order_data.php */

		// Add key
		add_post_meta( $post_id, '_order_key', uniqid('order_'), true );

		// Update post data
		// update_post_meta( $post_id, '_transaction_id', $id );
		// update_post_meta( $post_id, '_amazon_item_id', $item['item_id'] );
		update_post_meta( $post_id, '_wpla_amazon_order_id', $item['order_id'] );


		$billing_details = $details->ShippingAddress;
		$shipping_details = $details->ShippingAddress;

		// optional billing address / RegistrationAddress
		// if ( isset( $details->Buyer->RegistrationAddress ) ) {
		// 	$billing_details = $details->Buyer->RegistrationAddress;
		// }

		// if AddressLine1 is missing or empty, use AddressLine2 instead
		if ( empty( $billing_details->AddressLine1 ) ) {
			$billing_details->AddressLine1 = @$billing_details->AddressLine2;
			$billing_details->AddressLine2 = '';
		}
		if ( empty( $shipping_details->AddressLine1 ) ) {
			$shipping_details->AddressLine1 = @$shipping_details->AddressLine2;
			$shipping_details->AddressLine2 = '';
		}

		// optional fields
		if ($billing_details->Phone == 'Invalid Request') $billing_details->Phone = '';
		update_post_meta( $post_id, '_billing_phone', stripslashes( $billing_details->Phone ));

		// billing address
		@list( $billing_firstname, $billing_lastname )     = explode( " ", $details->BuyerName, 2 );
		update_post_meta( $post_id, '_billing_first_name', 	stripslashes( $billing_firstname ) );
		update_post_meta( $post_id, '_billing_last_name', 	stripslashes( $billing_lastname ) );
		// update_post_meta( $post_id, '_billing_company', 	stripslashes( $billing_details->CompanyName ) );
		update_post_meta( $post_id, '_billing_address_1', 	stripslashes( @$billing_details->AddressLine1 ) );
		update_post_meta( $post_id, '_billing_address_2', 	stripslashes( @$billing_details->AddressLine2 ) );
		update_post_meta( $post_id, '_billing_city', 		stripslashes( @$billing_details->City ) );
		update_post_meta( $post_id, '_billing_postcode', 	stripslashes( @$billing_details->PostalCode ) );
		update_post_meta( $post_id, '_billing_country', 	stripslashes( @$billing_details->CountryCode ) );
		update_post_meta( $post_id, '_billing_state', 		stripslashes( @$billing_details->StateOrRegion ) );
		
		// shipping address
		@list( $shipping_firstname, $shipping_lastname )   = explode( " ", $shipping_details->Name, 2 );
		update_post_meta( $post_id, '_shipping_first_name', stripslashes( $shipping_firstname ) );
		update_post_meta( $post_id, '_shipping_last_name', 	stripslashes( $shipping_lastname ) );
		// update_post_meta( $post_id, '_shipping_company', 	stripslashes( $shipping_details->CompanyName ) );
		update_post_meta( $post_id, '_shipping_address_1', 	stripslashes( @$shipping_details->AddressLine1 ) );
		update_post_meta( $post_id, '_shipping_address_2', 	stripslashes( @$shipping_details->AddressLine2 ) );
		update_post_meta( $post_id, '_shipping_city', 		stripslashes( @$shipping_details->City ) );
		update_post_meta( $post_id, '_shipping_postcode', 	stripslashes( @$shipping_details->PostalCode ) );
		update_post_meta( $post_id, '_shipping_country', 	stripslashes( @$shipping_details->CountryCode ) );
		update_post_meta( $post_id, '_shipping_state', 		stripslashes( @$shipping_details->StateOrRegion ) );
		
		// order details
		update_post_meta( $post_id, '_billing_email', 		$item['buyer_email']);
		update_post_meta( $post_id, '_cart_discount', 		'0');
		update_post_meta( $post_id, '_order_discount', 		'0');
		update_post_meta( $post_id, '_order_tax', 			'0.00' );
		update_post_meta( $post_id, '_order_shipping_tax', 	'0.00' );
		update_post_meta( $post_id, '_customer_user', 		'0' );
		update_post_meta( $post_id, '_prices_include_tax', 	'yes' );


		// Order Total
		$order_total = $details->OrderTotal->Amount;
		update_post_meta( $post_id, '_order_total', rtrim(rtrim(number_format( $order_total, 4, '.', ''), '0'), '.') );
		update_post_meta( $post_id, '_order_currency', $details->OrderTotal->CurrencyCode );


		// Payment method handling
		$payment_method = $details->PaymentMethod;
		if ( $payment_method == 'PayPal' ) $payment_method = 'paypal'; // TODO: more mapping
		if ( $payment_method == 'Other'  ) $payment_method = get_option( 'wpla_orders_default_payment_title', 'Other' );
		update_post_meta( $post_id, '_payment_method', $payment_method ); 
		update_post_meta( $post_id, '_payment_method_title', $payment_method );
	

		// Order line item(s)
		$this->processOrderLineItems( $item['items'], $post_id );

		// shipping info
		$this->processOrderShipping( $post_id, $item );

		// process tax
		$this->processOrderVAT( $post_id, $item );


		// prevent WooCommerce from sending out notification emails when updating order status or creating customers
		$this->disableEmailNotifications();

		// create user account for customer - if enabled
		if ( get_option( 'wpla_create_customers' ) ) {
			$user_id = $this->addCustomer( $item['buyer_email'], $details );
			update_post_meta( $post_id, '_customer_user', $user_id );
		}

		// support for WooCommerce Sequential Order Numbers Pro 1.5.6
		if ( isset( $GLOBALS['wc_seq_order_number_pro'] ) && method_exists( $GLOBALS['wc_seq_order_number_pro'], 'set_sequential_order_number' ) )
			$GLOBALS['wc_seq_order_number_pro']->set_sequential_order_number( $post_id );



		// order metadata had been saved, now get it so we can manipulate status
		$order = new WC_Order( $post_id );

		// would be nice if this worked:
		// $order->calculate_taxes();
		// $order->update_taxes();
		
		// order status
		if ( $item['status'] == 'Unshipped') { // TODO: what's the status when payment is complete?
			// unshipped orders: use config
			$new_order_status = get_option( 'wpla_new_order_status', 'completed' );
			$order->update_status( $new_order_status );
		} elseif ( $item['status'] == 'Shipped') {
			// shipped orders: completed 
			$order->update_status( 'completed' );
		} else {
			// anything else: on hold
			$order->update_status( 'on-hold' );
		}

		// allow other developers to post-process orders created by WP-Lister
		// if you hook into this, please check if get_product() actually returns a valid product object
		// WP-Lister might create order line items which do not exist in WooCommerce!
		// 
		// bad code looks like this:
		// $product = get_product( $item['product_id'] );
		// echo $product->get_sku();
		//
		// good code should look like this:
		// $_product = $order->get_product_from_item( $item );
		// if ( $_product->exists() ) { ... };

		do_action( 'wpla_after_create_order_with_nonexisting_items', $post_id );

		return $post_id;

	} // createWooOrderFromAmazonOrder()


	// process shipping info - create shipping line item
	function processOrderShipping( $post_id, $item ) {
		
		// shipping fee (gross)
		$shipping_total = $this->getShippingTotal( $item['items'] );

		// calculate shipping tax amount (VAT is usually applied to shipping fee)
		$shipping_tax_amount = 0;
		if ( $this->vat_enabled ) {
			$vat_percent         = get_option( 'wpla_orders_fixed_vat_rate' );
			$shipping_tax_amount = $shipping_total / ( 1 + ( 1 / ( $vat_percent / 100 ) ) );	// calc VAT from gross amount
			$shipping_total      = $shipping_total - $shipping_tax_amount;
		}

		// update shipping total (net - after substracting taxes)
		update_post_meta( $post_id, '_order_shipping', $shipping_total );

		// shipping method
		$details = $item['details'];
		$shipping_method_id_map    = apply_filters( 'wpla_shipping_service_id_map', array() );
		$shipping_method_id        = array_key_exists($details->ShipServiceLevel, $shipping_method_id_map) ? $shipping_method_id_map[$details->ShipServiceLevel] : $details->ShipServiceLevel;
		$shipping_method_title_map = apply_filters( 'wpla_shipping_service_title_map', array() );
		$shipping_method_title     = array_key_exists($details->ShipServiceLevel, $shipping_method_title_map) ? $shipping_method_title_map[$details->ShipServiceLevel] : $details->ShipServiceLevel;
		// this only works up to WC2.1:
		// update_post_meta( $post_id, '_shipping_method', 	  $shipping_method_id );
		// update_post_meta( $post_id, '_shipping_method_title', $shipping_method_title );

		// create shipping info as order line items - WC2.2
		$item_id = woocommerce_add_order_item( $post_id, array(
	 		'order_item_name' 		=> $shipping_method_title,
	 		'order_item_type' 		=> 'shipping'
	 	) );
	 	if ( $item_id ) {
		 	woocommerce_add_order_item_meta( $item_id, 'cost', 		$shipping_total );
		 	woocommerce_add_order_item_meta( $item_id, 'method_id', $shipping_total == 0 ? 'free_shipping' : 'other' );
		 	woocommerce_add_order_item_meta( $item_id, 'taxes', 	$shipping_tax_amount == 0 ? array() : array( 1 => $shipping_tax_amount ) );
		}

		// filter usage:
		// add_filter( 'wpla_shipping_service_title_map', 'my_amazon_shipping_service_title_map' );
		// function my_amazon_shipping_service_title_map( $map ) {
		// 	$map = array_merge( $map, array(
		// 		'Std DE Dom' => 'DHL Paket'
		// 	));
		// 	return $map;
		// }
		// add_filter( 'wpla_shipping_service_id_map', 'my_amazon_shipping_service_id_map' );
		// function my_amazon_shipping_service_id_map( $map ) {
		// 	$map = array_merge( $map, array(
		// 		'Std DE Dom' => 'flat_rate'
		// 	));
		// 	return $map;
		// }

	} // processOrderShipping()



	function processOrderVAT( $post_id, $item ) {
		global $wpdb;
		if ( ! $this->vat_enabled ) return;

		// shipping fee (gross)
		$shipping_total = $this->getShippingTotal( $item['items'] );

		// calculate shipping tax amount (VAT is usually applied to shipping fee)
		$vat_percent         = get_option( 'wpla_orders_fixed_vat_rate' );
		$shipping_tax_amount = $shipping_total / ( 1 + ( 1 / ( $vat_percent / 100 ) ) );	// calc VAT from gross amount

		// get tax rate
		$tax         = new WC_Tax();
		$tax_rate_id = get_option( 'wpla_orders_tax_rate_id' );
		$tax_rate    = $wpdb->get_row( "SELECT tax_rate_id, tax_rate_country, tax_rate_state, tax_rate_name, tax_rate_priority FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = '$tax_rate_id'" );

		$code      = WC_Tax::get_rate_code( $tax_rate_id );
		$tax_code  = $code ? $code : __('VAT','wpla');
		$tax_label = $tax_rate_id ? $tax_rate->tax_rate_name : WC()->countries->tax_or_vat();

		$item_id = woocommerce_add_order_item( $post_id, array(
	 		'order_item_name' 		=> $tax_code,
	 		'order_item_type' 		=> 'tax'
	 	) );

	 	// Add line item meta
	 	if ( $item_id ) {
		 	woocommerce_add_order_item_meta( $item_id, 'compound', 0 );
		 	woocommerce_add_order_item_meta( $item_id, 'tax_amount', $this->format_decimal( $this->vat_total ) );
		 	woocommerce_add_order_item_meta( $item_id, 'shipping_tax_amount', $this->format_decimal( $shipping_tax_amount ) );

		 	if ( $tax_rate_id ) {
		 		woocommerce_add_order_item_meta( $item_id, 'rate_id', $tax_rate_id );
		 		woocommerce_add_order_item_meta( $item_id, 'label', $tax_label );
		 	}
		}

		// store total order tax
		update_post_meta( $post_id, '_order_tax', $this->format_decimal( $this->vat_total ) ); 			
		update_post_meta( $post_id, '_order_shipping_tax', $this->format_decimal( $shipping_tax_amount ) ); 			

	} // processOrderVAT()



	function createOrderLineItem( $item, $post_id ) {

		// get listing item from db
		$listingsModel = new WPLA_ListingsModel();
		$listingItem = $listingsModel->getItemByASIN( $item->ASIN );


		$product_id			= $listingItem ? $listingItem->post_id : '0';
		$item_name 			= $listingItem ? $listingItem->listing_title : $item->Title;
		$item_quantity 		= $item->QuantityOrdered;
		
		$line_subtotal		= $item->ItemPrice->Amount;
		$line_total 		= $item->ItemPrice->Amount;


		// default to no tax
		$line_subtotal_tax	= '0.00';
		$line_tax		 	= '0.00';
		$item_tax_class		= '';


		// regard global VAT processing setting
		$vat_percent = get_option( 'wpla_orders_fixed_vat_rate' );
		if ( $vat_percent ) $vat_enabled = true;

		// process VAT if enabled
		if ( $vat_enabled ) {
			$this->logger->info( 'VAT%: '.$vat_percent );

			// calculate VAT included in line total
			// $vat_tax = $line_total * $vat_percent / 100; 					// calc VAT from net amount
			$vat_tax = $line_total / ( 1 + ( 1 / ( $vat_percent / 100 ) ) );	// calc VAT from gross amount
			// $this->logger->info( 'VAT: '.$vat_tax );

			// keep record of total VAT
			$this->vat_enabled = true;
			$this->vat_total  += $vat_tax;

			// $vat_tax = wc_round_tax_total( $vat_tax );
			$vat_tax = $this->format_decimal( $vat_tax );
			$this->logger->info( 'VAT: '.$vat_tax );
			$this->logger->info( 'vat_total: '.$this->vat_total );

			$line_subtotal_tax	= $vat_tax;
			$line_tax		 	= $vat_tax;

			// adjust item price if prices include tax
			if ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {
				$line_total    = $line_total    - $vat_tax;
				$line_subtotal = $line_subtotal - $vat_tax;
			}

			// try to get product object to set tax class
			$_product = get_product( $product_id );

			// set tax class
			if ( $_product && is_object($_product) ) 
				$item_tax_class		= $_product->get_tax_class();

			$this->logger->info( 'tax_class: '.$item_tax_class );
		}
		

		// check if item has variation 
		// $isVariation = false;
		// $VariationSpecifics = array();
        // if ( is_object( @$item->Variation ) ) {
            // foreach ($item->Variation->VariationSpecifics as $spec) {
                // $VariationSpecifics[ $spec->Name ] = $spec->Value[0];
            // }
		// 	$isVariation = true;
        // } 

		// get variation_id
		// if ( $isVariation ) {
		// 	$variation_id = WPLA_ProductWrapper::findVariationID( $product_id, $VariationSpecifics );
		// }



		$order_item = array();

		$order_item['product_id'] 			= $product_id;
		$order_item['variation_id'] 		= isset( $variation_id ) ? $variation_id : '0';
		$order_item['name'] 				= $item_name;
		// $order_item['tax_class']			= $_product->get_tax_class();
		$order_item['tax_class']			= $item_tax_class;
		$order_item['qty'] 					= $item_quantity;
		$order_item['line_subtotal'] 		= $this->format_decimal( $line_subtotal );
		$order_item['line_subtotal_tax'] 	= $line_subtotal_tax;
		$order_item['line_total'] 			= $this->format_decimal( $line_total );
		$order_item['line_tax'] 			= $line_tax;
		$order_item['line_tax_data'] 		= array( 
			'total' 	=> array( 1 => $line_tax ),
			'subtotal' 	=> array( 1 => $line_subtotal_tax ),
		);

		// Add line item
	   	$item_id = woocommerce_add_order_item( $post_id, array(
	 		'order_item_name' 		=> $order_item['name'],
	 		'order_item_type' 		=> 'line_item'
	 	) );

	 	// Add line item meta
	 	if ( $item_id ) {
		 	woocommerce_add_order_item_meta( $item_id, '_qty', 				$order_item['qty'] );
		 	woocommerce_add_order_item_meta( $item_id, '_tax_class', 		$order_item['tax_class'] );
		 	woocommerce_add_order_item_meta( $item_id, '_product_id', 		$order_item['product_id'] );
		 	woocommerce_add_order_item_meta( $item_id, '_variation_id', 	$order_item['variation_id'] );
		 	woocommerce_add_order_item_meta( $item_id, '_line_subtotal', 	$order_item['line_subtotal'] );
		 	woocommerce_add_order_item_meta( $item_id, '_line_subtotal_tax',$order_item['line_subtotal_tax'] );
		 	woocommerce_add_order_item_meta( $item_id, '_line_total', 		$order_item['line_total'] );
		 	woocommerce_add_order_item_meta( $item_id, '_line_tax', 		$order_item['line_tax'] );
		 	woocommerce_add_order_item_meta( $item_id, '_line_tax_data', 	$order_item['line_tax_data'] );
	 	}

	} // createOrderLineItem()


	function processOrderLineItems( $items, $post_id ) {

		// WC 2.0 only
		if ( ! function_exists('woocommerce_add_order_item_meta') ) return;

		#echo "<pre>";print_r($items);echo"</pre>";die();

		foreach ( $items as $item ) {
			$this->createOrderLineItem( $item, $post_id );
		}
		 
	} // processOrderLineItems()


	function getShippingTotal( $items ) {
		$shipping_total = 0;

		foreach ( $items as $item ) {
			if ( isset( $item->ShippingPrice ) ) {
				$shipping_total += $item->ShippingPrice->Amount;
			}
		}
		return $shipping_total;	 

	} // getShippingTotal()





	public function updateOrder( $order_id, $data ) {
		#...
		// if ( $updated ) {
		// 	$woocommerce->clear_order_transients( $order_id );
		// 	$this->logger->info( "updated order $order_id ($asin): $orders_name " );
		// 	$this->updated_count++;
		// }

		return $order_id;
	}






	/**
	 * addCustomer, adds a new WordPress user account
	 *
	 * @param unknown $customers_name
	 * @return $customers_id
	 */
	public function addCustomer( $user_email, $details ) {
		global $wpdb;
		// $this->logger->info( "addCustomer() - data: ".print_r($details,1) );

		// skip if user_email exists
		if ( $user_id = email_exists( $user_email ) ) {
			// $this->show_message('Error: email already exists: '.$user_email, 1 );
			$this->logger->info( "email already exists $user_email" );
			return $user_id;
		}

		// get user data
		$amazon_user_email  = $details->BuyerEmail;

		// get shipping address with first and last name
		$shipping_details = $details->ShippingAddress;
		@list( $shipping_firstname, $shipping_lastname ) = explode( " ", $shipping_details->Name, 2 );
		$user_firstname  = sanitize_user( $shipping_firstname, true );
		$user_lastname   = sanitize_user( $shipping_lastname, true );
		$user_fullname   = sanitize_user( $shipping_details->Name, true );

		// generate password
		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );


		// create wp_user
		$wp_user = array(
			'user_login' => $amazon_user_email,
			'user_email' => $user_email,
			'first_name' => $user_firstname,
			'last_name'  => $user_lastname,
			// 'user_registered' => date( 'Y-m-d H:i:s', strtotime($customer['customers_info_date_account_created']) ),
			'user_pass' => $random_password,
			'role' => 'customer'
			);
		$user_id = wp_insert_user( $wp_user ) ;

		if ( is_wp_error($user_id)) {

			$this->logger->error( 'error creating user '.$user_email.' - WP said: '.$user_id->get_error_message() );
			return false;

		} else {

			// add user meta
			update_user_meta( $user_id, '_amazon_user_email', 	$amazon_user_email );
			update_user_meta( $user_id, 'billing_email', 		$user_email );
			update_user_meta( $user_id, 'paying_customer', 		1 );
			
			// optional phone number
			if ($shipping_details->Phone == 'Invalid Request') $shipping_details->Phone = '';
			update_user_meta( $user_id, 'billing_phone', 		stripslashes( $shipping_details->Phone ));

			// if AddressLine1 is missing or empty, use AddressLine2 instead
			if ( empty( $shipping_details->AddressLine1 ) ) {
				$shipping_details->AddressLine1 = @$shipping_details->AddressLine2;
				$shipping_details->AddressLine2 = '';
			}

			// billing
			update_user_meta( $user_id, 'billing_first_name', 	$user_firstname );
			update_user_meta( $user_id, 'billing_last_name', 	$user_lastname );
			update_user_meta( $user_id, 'billing_company', 		stripslashes( @$shipping_details->CompanyName ) );
			update_user_meta( $user_id, 'billing_address_1', 	stripslashes( @$shipping_details->AddressLine1 ) );
			update_user_meta( $user_id, 'billing_address_2', 	stripslashes( @$shipping_details->AddressLine2 ) );
			update_user_meta( $user_id, 'billing_city', 		stripslashes( @$shipping_details->City ) );
			update_user_meta( $user_id, 'billing_postcode', 	stripslashes( @$shipping_details->PostalCode ) );
			update_user_meta( $user_id, 'billing_country', 		stripslashes( @$shipping_details->CountryCode ) );
			update_user_meta( $user_id, 'billing_state', 		stripslashes( @$shipping_details->StateOrRegion ) );
			
			// shipping
			update_user_meta( $user_id, 'shipping_first_name', 	$user_firstname );
			update_user_meta( $user_id, 'shipping_last_name', 	$user_lastname );
			update_user_meta( $user_id, 'shipping_company', 	stripslashes( @$shipping_details->CompanyName ) );
			update_user_meta( $user_id, 'shipping_address_1', 	stripslashes( @$shipping_details->AddressLine1 ) );
			update_user_meta( $user_id, 'shipping_address_2', 	stripslashes( @$shipping_details->AddressLine2 ) );
			update_user_meta( $user_id, 'shipping_city', 		stripslashes( @$shipping_details->City ) );
			update_user_meta( $user_id, 'shipping_postcode', 	stripslashes( @$shipping_details->PostalCode ) );
			update_user_meta( $user_id, 'shipping_country', 	stripslashes( @$shipping_details->CountryCode ) );
			update_user_meta( $user_id, 'shipping_state', 		stripslashes( @$shipping_details->StateOrRegion ) );
			
			$this->logger->info( "added customer $user_id ".$user_email." ($amazon_user_email) " );

		}

		return $user_id;

	} // addCustomer()



	function disableEmailNotifications() {

		// prevent WooCommerce from sending out notification emails when updating order status
		if ( get_option( 'wpla_disable_new_order_emails', 1 ) )
			add_filter( 'woocommerce_email_enabled_new_order', 					array( $this, 'returnFalse' ), 10, 2 );
		if ( get_option( 'wpla_disable_completed_order_emails', 1 ) )
			add_filter( 'woocommerce_email_enabled_customer_completed_order', 	array( $this, 'returnFalse' ), 10, 2 );
		if ( get_option( 'wpla_disable_processing_order_emails', 1 ) )
			add_filter( 'woocommerce_email_enabled_customer_processing_order', 	array( $this, 'returnFalse' ), 10, 2 );
		if ( get_option( 'wpla_disable_new_account_emails', 1 ) )
			add_filter( 'woocommerce_email_enabled_customer_new_account', 		array( $this, 'returnFalse' ), 10, 2 );

	}

	function returnFalse( $param1, $param2 = false ) {
		return false;
	}


	function format_decimal( $number ) {

		// wc_format_decimal() exists in WC 2.1+ only
		if ( function_exists('wc_format_decimal') ) 
			return wc_format_decimal( $number );

		$dp     = get_option( 'woocommerce_price_num_decimals' );
		$number = number_format( floatval( $number ), $dp, '.', '' );
		return $number;
		 
	} // format_decimal()




} // class WPLA_OrderBuilder
