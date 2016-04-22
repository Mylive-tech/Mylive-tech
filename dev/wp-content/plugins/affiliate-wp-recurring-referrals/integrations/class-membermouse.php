<?php

class Affiliate_WP_Recurring_MemberMouse extends Affiliate_WP_Recurring_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.4
	*/
	public function init() {

		$this->context = 'membermouse';

		add_filter( 'affwp_insert_pending_referral', array( $this, 'set_recurring_flag' ),         10, 8 );
		add_action( 'mm_commission_rebill',          array( $this, 'record_referral_on_renewal' ), 10    );

	}

	public function set_recurring_flag( $args, $amount, $reference, $description, $affiliate_id, $visit_id, $data, $context ) {

		if( $this->context == $context ) {

			// Separate member ID from order+transaction ID
			$parts = explode( '|', $reference );		

			// Set custom to the member ID
			$args['custom'] = $parts[0];

		}

		return $args;
	}

	/**
	 * Insert referrals on subscription payments
	 *
	 * @access  public
	 * @since   1.4
	*/
	public function record_referral_on_renewal( $affiliate_data ) {

		// Look up the original order to see if it had a referral recorded with it
		$is_valid = affiliate_wp()->tracking->is_valid_affiliate( $affiliate_data['order_affiliate_id'] );

		if( ! $is_valid ) {
			return;
		}

		$args = array(
			'reference'    => $affiliate_data['member_id'] . '|' . $affiliate_data['order_number'],
			'affiliate_id' => $affiliate_data['order_affiliate_id'],
			'description'  => sprintf( __( 'Subscription payment for member #%d', 'affiliate-wp-recurring' ), $affiliate_data['member_id'] ),
			'amount'       => $affiliate_data['order_total']
		);

		$referral_id = $this->insert_referral( $args );

		$this->complete_referral( $referral_id );

	}

}
new Affiliate_WP_Recurring_MemberMouse;