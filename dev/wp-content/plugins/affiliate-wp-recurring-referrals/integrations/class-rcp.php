<?php

class Affiliate_WP_Recurring_RCP extends Affiliate_WP_Recurring_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {

		$this->context = 'rcp';

		add_action( 'rcp_insert_payment', array( $this, 'record_referral_on_payment' ), -1, 3 );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );
	}

	/**
	 * Insert referrals on subscription payments
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function record_referral_on_payment( $payment_id, $args, $amount ) {

		$referral  = affiliate_wp()->referrals->get_by( 'reference', $args['subscription_key'], $this->context );

		if( ! $referral || ! is_object( $referral ) || 'rejected' == $referral->status ) {
			return false; // This signup wasn't referred or is the very first payment of a referred subscription
		}

		$args = array(
			'reference'    => $payment_id,
			'affiliate_id' => $referral->affiliate_id,
			'description'  => $args['subscription'],
			'amount'       => $amount,
			'custom'       => $args['subscription_key']
		);

		$referral_id = $this->insert_referral( $args );

		$this->complete_referral( $referral_id );

	}

	/**
	 * Builds the reference link for the referrals table
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function reference_link( $reference = 0, $referral ) {

		if( empty( $referral->context ) || 'rcp' != $referral->context ) {

			return $reference;

		}

		if( ! empty( $referral->custom ) && 32 == strlen( $referral->custom ) ) {
			$reference = $referral->custom;
		}

		$url = admin_url( 'admin.php?page=rcp-payments&s=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}


}
new Affiliate_WP_Recurring_RCP;