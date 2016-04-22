<?php

class Affiliate_WP_Lifetime_Commissions_Exchange extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {

		$this->context = 'it-exchange';
		
		// Filter the affiliate ID when a pending referral is created
		add_filter( 'affwp_insert_pending_referral', array( $this, 'pending_referral_affiliate' ), 10, 8 );

		// set the product rate to that of the lifetime affiliate
		add_filter( 'affwp_get_product_rate', array( $this, 'set_affiliate_product_rate' ), 10, 5 );

	}

	/**
	 * Set the product rate based on the lifetime affiliate's ID, but only if there's a per-affiliate rate
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function set_affiliate_product_rate( $rate, $product_id, $args = array(), $affiliate_id, $context ) {

		// don't filter if not the right context
		if ( $this->context != $context ) {
			return $rate;
		}

		$lifetime_affiliate = (int) $this->get_affiliate_id( $this->get_users_lifetime_affiliate( $args['reference'] ) );
		
		// return if no lifetime affiliate
		if ( ! $lifetime_affiliate ) {
			return $rate;
		}

		// we're only changing the rate if the lifetime affiliate has a per-affiliate rate set, otherwise it will be calcualted at the per-product rate
		$per_affiliate_rate = affiliate_wp()->affiliates->get_column( 'rate', $lifetime_affiliate );

		// if there's a per-affiliate rate, use that instead of the per-product rate
		if ( $per_affiliate_rate ) {
			$rate = affwp_get_affiliate_rate( $lifetime_affiliate );
		}

		return $rate;

	}

	/**
	 * Filter the affiliate ID when a pending referral is created
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function pending_referral_affiliate( $args, $amount, $reference, $description, $affiliate_id, $visit_id, $data, $context ) {

		// don't filter if not the right context
		if ( $this->context != $args['context'] ) {
			return $args;
		}

		// don't filter if tracked coupon is used
		if ( $this->has_tracked_coupon( $reference ) ) {
			return $args;
		}
		
		$lifetime_affiliate = (int) $this->get_affiliate_id( $this->get_users_lifetime_affiliate( $reference ) );

		if ( $lifetime_affiliate ) {
			$args['affiliate_id'] = $lifetime_affiliate;
		}

		return $args;

	}

	/**
	 * Was tracked coupon used?
	 *
	 * @access  private
	 * @since   1.0
	*/
	private function has_tracked_coupon( $transaction_id = 0 ) {
	    $has_coupon  = false;
	    
	    $this->transaction = get_post_meta( $transaction_id, '_it_exchange_cart_object', true );

	    if ( $this->transaction->coupons && is_array( $this->transaction->coupons ) ) {

	    	if ( ! empty( $this->transaction->coupons['cart'] ) ) {

	    		foreach ( $this->transaction->coupons['cart'] as $coupon ) {

	    			$affiliate_id = get_post_meta( $coupon['id'], 'affwp_coupon_affiliate', true );

	    			if ( ! $affiliate_id ) {
	    				continue;
	    			}

	    			$this->affiliate_id = $affiliate_id;
	    			$has_coupon = true;
	    			break;

	    		}

	    	}

	    }

		return $has_coupon;
	}

}
new Affiliate_WP_Lifetime_Commissions_Exchange;