<?php

abstract class Affiliate_WP_Recurring_Base {

	public $context;

	public function __construct() {
		$this->init();
	}

	public function init() {

	}

	public function was_signup_referred() {
		return false;
	}

	public function insert_referral( $args = array() ) {

		if( affiliate_wp()->referrals->get_by( 'reference', $args['reference'], $this->context ) ) {
			return false; // Referral already created for this reference
		}

		$amount = affwp_calc_referral_amount( $args['amount'], $args['affiliate_id'], $args['reference'] );

		if( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}

		return affiliate_wp()->referrals->add( array(
			'amount'       => $amount,
			'reference'    => $args['reference'],
			'description'  => $args['description'],
			'affiliate_id' => $args['affiliate_id'],
			'context'      => $this->context,
			'custom'       => ! empty( $args['custom'] ) ? $args['custom'] : ''
		) );

	}

	public function complete_referral( $referral_id = 0 ) {

		if ( empty( $referral_id ) ) {
			return false;
		}

		if ( affwp_set_referral_status( $referral_id, 'unpaid' ) ) {

			do_action( 'affwp_complete_recurring_referral', $referral_id );

			return true;
		}

		return false;

	}

}