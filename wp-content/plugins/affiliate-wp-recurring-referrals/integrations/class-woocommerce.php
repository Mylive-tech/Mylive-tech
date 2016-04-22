<?php

class Affiliate_WP_Recurring_WooCommerce extends Affiliate_WP_Recurring_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {

		$this->context = 'woocommerce';

		add_action( 'woocommerce_renewal_order_payment_complete', array( $this, 'record_referral_on_payment' ), -1 );

	}

	/**
	 * Insert referrals on subscription payments
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function record_referral_on_payment( $order_id ) {

		$renewal_order     = new WC_Order( $order_id );
		$original_order_id = WC_Subscriptions_Renewal_Order::get_parent_order_id( $order_id );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $original_order_id, $this->context );

		if( ! $referral || ! is_object( $referral ) || 'rejected' == $referral->status ) {
			return false; // This signup wasn't referred or is the very first payment of a referred subscription
		}

		$args = array(
			'reference'    => $order_id,
			'affiliate_id' => $referral->affiliate_id,
			'description'  => sprintf( __( 'Subscription payment for %d', 'affiliate-wp-recurring' ), $original_order_id ),
			'amount'       => $renewal_order->get_total(),
			'custom'       => $original_order->ID
		);

		$referral_id = $this->insert_referral( $args );

		$this->complete_referral( $referral_id );

	}

}
new Affiliate_WP_Recurring_WooCommerce;