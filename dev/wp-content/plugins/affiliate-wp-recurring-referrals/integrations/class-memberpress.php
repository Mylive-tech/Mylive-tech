<?php

class Affiliate_WP_Recurring_MemberPress extends Affiliate_WP_Recurring_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.4
	*/
	public function init() {

		$this->context = 'memberpress';

		add_action( 'mepr-txn-status-complete', array( $this, 'record_referral_on_payment' ), -1 );

	}

	/**
	 * Insert referrals on subscription payments
	 *
	 * @access  public
	 * @since   1.4
	*/
	public function record_referral_on_payment( $txn ) {

		if( empty( $txn->subscription_id ) ) {
			return;
		}

		$referral = affiliate_wp()->referrals->get_by( 'custom', $txn->subscription_id, $this->context );

		if( ! $referral || ! is_object( $referral ) || 'rejected' == $referral->status ) {
			return false; // This signup wasn't referred or is the very first payment of a referred subscription
		}

		$args = array(
			'reference'    => $txn->id,
			'affiliate_id' => $referral->affiliate_id,
			'description'  => sprintf( __( 'Subscription payment for %s', 'affiliate-wp-recurring' ), $txn->subscription_id ),
			'amount'       => $txn->amount,
			'custom'       => $referral->reference
		);

		$referral_id = $this->insert_referral( $args );

		$this->complete_referral( $referral_id );

	}

}
new Affiliate_WP_Recurring_MemberPress;