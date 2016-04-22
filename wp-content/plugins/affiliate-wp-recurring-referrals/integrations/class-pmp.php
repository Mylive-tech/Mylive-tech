<?php

class Affiliate_WP_Recurring_PMP extends Affiliate_WP_Recurring_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function init() {

		$this->context = 'pmp';

		add_action( 'pmpro_added_order', array( $this, 'record_referral_on_payment' ), -1 );

	}

	/**
	 * Insert referrals on subscription payments
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function record_referral_on_payment( $order ) {

		$first_order = $this->get_first_order( $order );

		if( empty( $first_order ) || ! pmpro_isOrderRecurring( $order ) ) {
			return;
		}

		$referral = affiliate_wp()->referrals->get_by( 'reference', $first_order->id, $this->context );

		if( ! $referral || ! is_object( $referral ) || 'rejected' == $referral->status ) {
			return false; // This signup wasn't referred or is the very first payment of a referred subscription
		}

		$args = array(
			'reference'    => $order->id,
			'affiliate_id' => $referral->affiliate_id,
			'description'  => sprintf( __( 'Subscription payment for %s', 'affiliate-wp-recurring' ), $order->membership_name ),
			'amount'       => $order->subtotal,
			'custom'       => $first_order->id
		);

		$referral_id = $this->insert_referral( $args );

		$this->complete_referral( $referral_id );

	}

	/**
	 * Retrieves the order ID of the first payment made for a subscription
	 *
	 * @access  public
	 * @since   1.1
	*/
	private function get_first_order( $order ) {
 
		global $wpdb;
	 
		// Make sure a subscription transaction ID is present
		if( empty( $order->subscription_transaction_id ) ) {
			return false;
		}
	 
		// get the order ID of the first payment of this subscription
		$query = "SELECT MIN(id), payment_transaction_id FROM $wpdb->pmpro_membership_orders WHERE
					gateway = '" . esc_sql( $order->gateway ) . "' AND
					gateway_environment = '" . esc_sql( $order->gateway_environment ) . "' AND
					user_id = '" . esc_sql( $order->user_id) . "' AND
					membership_id = '" . esc_sql( $order->membership_id ) . "' AND
					subscription_transaction_id = '" . esc_sql( $order->subscription_transaction_id ) . "' ";
					
		//if this is an existing order, make sure we don't select our self
		if( ! empty( $order->id ) ) {
			$query .= "AND id < '" . esc_sql( $order->id ) . "' ";
		}

		//just the first
		$query .= "LIMIT 1";
		
		return $wpdb->get_row( $query );
	 
	}


}
new Affiliate_WP_Recurring_PMP;

// function to test if an order is recurring or not. Function provided by Jason Coleman. May get put in PMP core.
if( ! function_exists( 'pmpro_isOrderRecurring' ) ) {
	function pmpro_isOrderRecurring( $order, $test_checkout = false ) {
		global $wpdb;
		
		//must have a subscription_transaction_id
		if( empty( $order->subscription_transaction_id ) )
			return false;
		
		//check that we aren't processing at checkout
		if( $test_checkout && ! empty( $_REQUEST['submit-checkout'] ) ) {
			return false;
		}
			
		//check for earlier orders with the same gateway, user_id, membership_id, and subscription_transaction_id
		$sqlQuery = "SELECT id FROM $wpdb->pmpro_membership_orders WHERE 
				gateway = '" . esc_sql($order->gateway) . "' AND 
				gateway_environment = '" . esc_sql($order->gateway_environment) . "' AND 
				user_id = '" . esc_sql($order->user_id) . "' AND 
				membership_id = '" . esc_sql($order->membership_id) . "' AND 
				subscription_transaction_id = '" . esc_sql($order->subscription_transaction_id) . "' AND
				timestamp < '" . date("Y-m-d", $order->timestamp) . "' ";
		
		if( ! empty( $order->id ) ) {
			$sqlQuery .= " AND id <> '" . esc_sql($order->id) . "' ";
		}
		
		$sqlQuery .= "LIMIT 1";			
		
		$earlier_order = $wpdb->get_var( $sqlQuery );				
		
		if( empty( $earlier_order ) ) {
			return false;					
		}
			
		//must be recurring
		return true;
	}
}