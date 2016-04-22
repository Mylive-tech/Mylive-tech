<?php

class Affiliate_WP_Recurring_IT_Exchange extends Affiliate_WP_Recurring_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {

		$this->context = 'it-exchange';

		add_action( 'it_exchange_add_child_transaction', array( $this, 'record_referral_on_payment' ), -1 );

	}

	/**
	 * Insert referrals on subscription payments
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function record_referral_on_payment( $transaction_id ) {

		$parent_txn_id = get_post_meta( $transaction_id, '_it_exchange_parent_tx_id', true );
		$referral      = affiliate_wp()->referrals->get_by( 'reference', $parent_txn_id, $this->context );

		if( ! $referral || ! is_object( $referral ) || 'rejected' == $referral->status ) {
			return false; // This signup wasn't referred or is the very first payment of a referred subscription
		}

		$transaction = get_post_meta( $transaction_id, '_it_exchange_cart_object', true );

		$args = array(
			'reference'    => $transaction_id,
			'affiliate_id' => $referral->affiliate_id,
			'description'  => sprintf( __( 'Subscription payment for %d', 'affiliate-wp-recurring' ), $parent_txn_id ),
			'amount'       => $transaction->total,
			'custom'       => $parent_txn_id
		);

		$referral_id = $this->insert_referral( $args );

		$this->complete_referral( $referral_id );

	}

}
new Affiliate_WP_Recurring_IT_Exchange;