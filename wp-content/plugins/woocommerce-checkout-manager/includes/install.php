<?php
// Plugin activation script
function wooccm_install() {

	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
	$options3 = get_option( 'wccs_settings3' );

	update_option('wooccm_update_notice', 0 );

	if ( function_exists( 'icl_register_string' ) ) {
		icl_register_string( 'WooCommerce Checkout Manager', 'is a required field.', 'is a required field.' );
	}
	if (empty($options['checkness']['position'])) {
		$options['checkness']['position'] = 'after_order_notes';
	}
	if (empty($options['checkness']['wooccm_notification_email'])) {
		$options['checkness']['wooccm_notification_email'] = get_option('admin_email');
	}
	if ( empty($options['checkness']['payment_method_d']) ) {
		$options['checkness']['payment_method_d'] = 'Payment Method';
	}
	if ( empty($options['checkness']['time_stamp_title']) ) {
		$options['checkness']['time_stamp_title'] = 'Order Time';
	}
	if ( empty($options['checkness']['payment_method_t']) ) {
		$options['checkness']['payment_method_t'] = '1';
	}
	if ( empty($options['checkness']['shipping_method_d']) ) {
		$options['checkness']['shipping_method_d'] = 'Shipping Method';
	}
	if ( empty($options['checkness']['shipping_method_t']) ) {
		$options['checkness']['shipping_method_t'] = '1';
	}
	if ( empty($options2['shipping_buttons']) ) {
		$shipping = array( 
			'country' => 'Country', 
			'first_name' => 'First Name', 
			'last_name' => 'Last Name', 
			'company' => 'Company Name', 
			'address_1' => 'Address', 
			'address_2' => '', 
			'city' => 'Town/ City', 
			'state' => 'State', 
			'postcode' => 'Zip'
		);
		$ship = 0;
		foreach( $shipping as $name => $value ) {

			$options2['shipping_buttons'][$ship]['label'] = __( ''.$value.'', 'woocommerce' );
			$options2['shipping_buttons'][$ship]['cow'] = $name;
			$options2['shipping_buttons'][$ship]['checkbox']  = 'true';
			$options2['shipping_buttons'][$ship]['order'] = $ship + 1;
			$options2['shipping_buttons'][$ship]['type'] = 'wooccmtext';

			if ( $name == 'country') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
			}	

			if ( $name == 'first_name') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-first';
			}	

			if ( $name == 'last_name') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-last';
				$options2['shipping_buttons'][$ship]['clear_row'] = true;
			}

			if ( $name == 'company') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
			}

			if ( $name == 'address_1') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
				$options2['shipping_buttons'][$ship]['placeholder'] = __('Street address', 'woocommerce');
			}

			if ( $name == 'address_2') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
				$options2['shipping_buttons'][$ship]['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'woocommerce');
			}

			if ( $name == 'city') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
				$options2['shipping_buttons'][$ship]['placeholder'] = __('Town / City', 'woocommerce');
			}

			if ( $name == 'state') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-first';
			}

			if ( $name == 'postcode') {
				$options2['shipping_buttons'][$ship]['position'] = 'form-row-last';
				$options2['shipping_buttons'][$ship]['placeholder'] = __('Postcode / Zip', 'woocommerce');
				$options2['shipping_buttons'][$ship]['clear_row'] = true;
			}

			$ship++;
		}
	}

	if ( empty($options3['billing_buttons']) ) {
		$billing = array( 
			'country' => 'Country', 
			'first_name' => 'First Name', 
			'last_name' => 'Last Name', 
			'company' => 'Company Name', 
			'address_1' => 'Address', 
			'address_2' => '', 
			'city' => 'Town/ City', 
			'state' => 'State', 
			'postcode' => 'Zip', 
			'email' => 'Email Address', 
			'phone' => 'Phone'
		);
		$bill = 0;
		foreach( $billing as $name => $value ) {

			$options3['billing_buttons'][$bill]['label'] = __(''.$value.'', 'woocommerce');
			$options3['billing_buttons'][$bill]['cow'] = $name;
			$options3['billing_buttons'][$bill]['checkbox']  = 'true';
			$options3['billing_buttons'][$bill]['order'] = $bill + 1;
			$options3['billing_buttons'][$bill]['type'] = 'wooccmtext';

			if ( $name == 'country') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
			}

			if ( $name == 'first_name') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-first';
			}

			if ( $name == 'last_name') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-last';
				$options3['billing_buttons'][$bill]['clear_row'] = true;
			}

			if ( $name == 'company') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
			}

			if ( $name == 'address_1') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
				$options3['billing_buttons'][$bill]['placeholder'] = __('Street address', 'woocommerce');
			}

			if ( $name == 'address_2') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
				$options3['billing_buttons'][$bill]['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'woocommerce');
			}

			if ( $name == 'city') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
				$options3['billing_buttons'][$bill]['placeholder'] = __('Town / City', 'woocommerce');
			}

			if ( $name == 'state') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-first';
			}

			if ( $name == 'postcode') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-last';
				$options3['billing_buttons'][$bill]['placeholder'] = __('Postcode / Zip', 'woocommerce');
				$options3['billing_buttons'][$bill]['clear_row'] = true;
			}

			if ( $name == 'email') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-first';
			}

			if ( $name == 'phone') {
				$options3['billing_buttons'][$bill]['position'] = 'form-row-last';
				$options3['billing_buttons'][$bill]['clear_row'] = true;
			}

			$bill++;
		}
	}

	if ( !empty($options['buttons']) ) {
		foreach( $options['buttons'] as $i => $btn ) {

			if( !empty($btn['check_1']) || !empty($btn['check_2']) ) {
				$options['buttons'][$i]['option_array'] = implode( '||', array(''.wooccm_wpml_string(''.$btn['check_1'].'').'',''.wooccm_wpml_string(''.$btn['check_2'].'').'') );
				$options['buttons'][$i]['check_1'] = '';
				$options['buttons'][$i]['check_2'] = '';
			} 

			$options['buttons'][$i]['type'] = ( $btn['type'] == 'checkbox' ) ? 'checkbox_wccm' : $btn['type'];
			$options['buttons'][$i]['type'] = ( $btn['type'] == 'text' ) ? 'wooccmtext' : $btn['type'];
			$options['buttons'][$i]['type'] = ( $btn['type'] == 'select' ) ? 'wooccmselect' : $btn['type'];
			$options['buttons'][$i]['type'] = ( $btn['type'] == 'date' ) ? 'datepicker' : $btn['type'];

			if (empty($btn['option_array'])) {
				$btn['option_array'] = '';
			}

			$mysecureop = explode( '||', $btn['option_array']);

			if ( !empty($btn['option_a']) ) {
				array_push($mysecureop, $btn['option_a'] );
			}

			if ( !empty($btn['option_b']) ) {
				array_push($mysecureop, $btn['option_b'] );
			}

			$uniqueThevalues = array_unique($mysecureop);

			$options['buttons'][$i]['option_array'] = implode( '||', $uniqueThevalues);

		}
	}

	foreach( $options3['billing_buttons'] as $i => $btn ) {

		if( !empty($btn['check_1']) || !empty($btn['check_2']) ) {
			$options3['billing_buttons'][$i]['option_array'] = implode( '||', array(''.wooccm_wpml_string(''.$btn['check_1'].'').'',''.wooccm_wpml_string(''.$btn['check_2'].'').'') );
			$options3['billing_buttons'][$i]['check_1'] = '';
			$options3['billing_buttons'][$i]['check_2'] = '';
		} 

		$options3['billing_buttons'][$i]['type'] = ( $btn['type'] == 'checkbox' ) ? 'checkbox_wccm' : $btn['type'];
		$options3['billing_buttons'][$i]['type'] = ( $btn['type'] == 'text' ) ? 'wooccmtext' : $btn['type'];
		$options3['billing_buttons'][$i]['type'] = ( $btn['type'] == 'select' ) ? 'wooccmselect' : $btn['type'];
		$options3['billing_buttons'][$i]['type'] = ( $btn['type'] == 'date' ) ? 'datepicker' : $btn['type'];

	}

	foreach( $options2['shipping_buttons'] as $i => $btn ) {

		if( !empty($btn['check_1']) || !empty($btn['check_2']) ) {
			$options2['shipping_buttons'][$i]['option_array'] = implode( '||', array(''.wooccm_wpml_string(''.$btn['check_1'].'').'',''.wooccm_wpml_string(''.$btn['check_2'].'').'') );
			$options2['shipping_buttons'][$i]['check_1'] = '';
			$options2['shipping_buttons'][$i]['check_2'] = '';
		}

		$options2['shipping_buttons'][$i]['type'] = ( $btn['type'] == 'checkbox' ) ? 'checkbox_wccm' : $btn['type'];
		$options2['shipping_buttons'][$i]['type'] = ( $btn['type'] == 'text' ) ? 'wooccmtext' : $btn['type'];
		$options2['shipping_buttons'][$i]['type'] = ( $btn['type'] == 'select' ) ? 'wooccmselect' : $btn['type'];
		$options2['shipping_buttons'][$i]['type'] = ( $btn['type'] == 'date' ) ? 'datepicker' : $btn['type'];

	}

	update_option( 'wccs_settings', $options );
	update_option( 'wccs_settings2', $options2 );
	update_option( 'wccs_settings3', $options3 );

}
?>