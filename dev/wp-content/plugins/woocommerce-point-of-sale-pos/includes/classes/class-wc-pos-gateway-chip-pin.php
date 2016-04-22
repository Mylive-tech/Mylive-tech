<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class pos_chip_pin_gateway extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     */
	public function __construct() {
		$this->id                 = 'pos_chip_pin';
		$this->icon               = apply_filters( 'woocommerce_pos_chip_pin_icon', '' );
		$this->method_title       = __( 'Chip & PIN', 'wc_point_of_sale' );
		$this->method_description = '';
		$this->has_fields         = false;


		$enabled_gateways   = get_option( 'pos_enabled_gateways', array() );

		// Get settings
		$this->title              = $this->method_title;
		$this->description        = $this->method_description;
		$this->instructions       = '';
		$this->enabled            = in_array('pos_chip_pin', $enabled_gateways);
	}

	/**
	 * Check If The Gateway Is Available For Use
	 *
	 * @return bool
	 */
	public function is_available() {
		return $this->enabled;
	}


    /**
     * Process the payment and return the result
     *
     * @param int $order_id
     * @return array
     */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		// Mark as processing (payment won't be taken until delivery)
		$order->update_status( 'processing' );

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		WC()->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> $this->get_return_url( $order )
		);
	}
}
