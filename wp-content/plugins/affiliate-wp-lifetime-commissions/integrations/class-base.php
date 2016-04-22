<?php

class Affiliate_WP_Lifetime_Commissions_Base {

	public $context;

	public function __construct() {
		add_action( 'affwp_complete_referral', array( $this, 'complete_referral' ), 10, 3 );

		$this->init();
	}

	/**
	 * Gets things started
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
	 */
	public function init() {

	}

	/**
	 * Store the affiliate ID with the user
	 * 
	 * @return [type] [description]
	 * @since  1.0
	 */
	public function complete_referral( $referral_id, $referral, $reference ) {

		// make sure the function only runs for the current context
		if ( $this->context != $referral->context ) {
			return;
		}

		// get affiliate ID
		$affiliate_id      = $referral->affiliate_id;

		$affiliate_user_id = affwp_get_affiliate_user_id( $affiliate_id );

		// get the user's ID (for logged in users)
		$user_id = $this->get( $referral->context, 'user_id', $referral->reference );

		// get the user's email
		// if a user changes their email address at checkout, we'll add this to the $lifetime_user_emails array
		// get the user's email address from the referral
		$user_email = $this->get( $referral->context, 'email', $referral->reference );

		// get a customers lifetime emails
		$lifetime_user_emails = get_user_meta( $user_id, 'affwp_lc_email' );

		// can the affiliate receive lifetime commissions?
		$lifetime_commissions = $this->can_receive_lifetime_commissions( $affiliate_user_id );

		// affiliate can receive lifetime commissions 
		if ( $lifetime_commissions ) {

			// user has an account. Can't be -1 (guest)
			if ( $user_id && $user_id != -1 ) {

				if ( ! in_array( $user_id, $this->get_affiliates_customer_ids( $affiliate_id ) ) ) {
					add_user_meta( $affiliate_user_id, 'affwp_lc_customer_id', $user_id );
				}

				// also store the affiliate ID against the user. A user can only have 1 affiliate assigned to them
				update_user_meta( $user_id, 'affwp_lc_affiliate_id', $affiliate_id );

				if ( ! in_array( $user_email, $lifetime_user_emails ) ) {
					// add it to the user meta
					add_user_meta( $user_id, 'affwp_lc_email', $user_email );

					// add it to affiliate user meta
					add_user_meta( $affiliate_user_id, 'affwp_lc_customer_email', $user_email );
				}

				// add all the customers emails to the new affiliate
				if ( $lifetime_user_emails ) {
					foreach ( $lifetime_user_emails as $email ) {
						// loop through and delete all associated email addresses for the old affiliate
						delete_user_meta( $affiliate_user_id, 'affwp_lc_customer_email', $email );

						// loop through and add all associated email addresses to the new affiliate
						add_user_meta( $affiliate_user_id, 'affwp_lc_customer_email', $email );
					}
				}
			} else {
				// no user account
				// add customer's email to affiliate's usermeta for future guest purchases
				if ( ! in_array( $user_email, $this->get_affiliates_customer_emails( $affiliate_id ) ) ) {
					add_user_meta( $affiliate_user_id, 'affwp_lc_customer_email', $user_email );
				}
				
			}

		}

		// customer is now linked to affiliate, huzzah!
	}

	/**
	 * Get array of affiliate's customer email addresses
	 *
	 * @return array customer emails linked to an affiliate
	 * @since  1.0
	 */
	public function get_affiliates_customer_emails( $affiliate_id = 0 ) {
		if ( ! $affiliate_id ) {
			return;
		}

		$emails = get_user_meta( affwp_get_affiliate_user_id( $affiliate_id ), 'affwp_lc_customer_email' );

		return (array) $emails;
	}

	/**
	 * Get array of affiliate's customer IDs
	 *
	 * @return array customer ids linked to an affiliate
	 * @since  1.0
	 */
	public function get_affiliates_customer_ids( $affiliate_id = 0 ) {
		if ( ! $affiliate_id ) {
			return;
		}

		$ids = get_user_meta( affwp_get_affiliate_user_id( $affiliate_id ), 'affwp_lc_customer_id' );

		return (array) $ids;
	}

	/**
	 * Retrieves the affiliate ID that should receive commission
	 *
	 * If a user is logged in, the affiliate ID is looked up via the user's ID
	 * If a user is not logged in, the affiliate ID is looked up via the user's email address 
	 *
	 * @return absint $lifetime_affiliate_id ID of affiliate linked to user, false otherwise
	 * @since  1.0
	 */
	public function get_users_lifetime_affiliate( $reference = 0 ) {

		// get ID of currently logged in user
		$user_id = get_current_user_id();

		if ( $user_id ) {
			// user must be logged in.
			$affiliate_id = get_user_meta( $user_id, 'affwp_lc_affiliate_id', true );

			// user has linked affiliate ID, use that
			if ( $affiliate_id ) {
				$lifetime_affiliate_id = $affiliate_id;
			} else {
				// user is a guest and has a linked affiliate but has created an account at checkout.

				// lookup affiliate ID by customer email
				$customer_email_address = $this->get( $this->context, 'email', $reference );
				$lifetime_affiliate_id  = $this->get_affiliate_id_from_email( $customer_email_address );

				// store the lifetime affiliate ID with the affiliate's user account for later use
				if ( $lifetime_affiliate_id ) {
					update_user_meta( $user_id, 'affwp_lc_affiliate_id', $lifetime_affiliate_id );
				}

				// store their email against their new user account
				update_user_meta( $user_id, 'affwp_lc_email', $customer_email_address );
			}

		} else {
			// must not be logged in, as user ID will be 0
			
			// get each email by context
			$customer_email_address = $this->get( $this->context, 'email', $reference );

			// lookup affiliate ID by customer email
			$lifetime_affiliate_id = $this->get_affiliate_id_from_email( $customer_email_address );
		}

		if ( $lifetime_affiliate_id ) {
			return absint( affwp_get_affiliate_user_id( $lifetime_affiliate_id ) );
		}

		return false;
	}

	/**
	 * Can the affiliate receive lifetime commissions?
	 * 
	 * @since  1.0
	 */
	public function can_receive_lifetime_commissions( $user_id ) {
		$global_lifetime_commissions_enabled = affiliate_wp()->settings->get( 'lifetime_commissions' );

		// all affiliates can earn lifetime commissions
		if ( $global_lifetime_commissions_enabled ) {
			return true;
		}

		$allowed = get_user_meta( $user_id, 'affwp_lc_enabled', true );

		if ( $allowed ) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the user's email or ID depending on the referral's context
	 *
	 * @param string $context the referrals context
	 * @param string $get what to retrieve
	 * @param int $reference Payment reference number
	 * 
	 * @since 1.0
	 */
	public function get( $context = '', $get = '', $reference = 0 ) {

		switch ( $context ) {

			case 'edd':
				$email_address = edd_get_payment_user_email( $reference );
				$user_id       = edd_get_payment_user_id( $reference );
				break;
			
			case 'woocommerce':
				$order         = new WC_Order( $reference );
				$email_address = $order->billing_email;
				$user_id       = get_post_meta( $reference, '_customer_user', true );
				break;

			case 'it-exchange':

				$payment_meta   = get_post_meta( $reference, '_it_exchange_cart_object', true );
				$guest_checkout = isset( $payment_meta->is_guest_checkout ) ? $payment_meta->is_guest_checkout : false;

				// if logged in, get email from payment meta
				if ( ! $guest_checkout ) {
					$email_address = $payment_meta->shipping_address['email'];
				} 
				// get it from the ID field for guest purchases
				else {
					$email_address = get_post_meta( $reference, '_it_exchange_customer_id', true );
				}
				
				// get the ID 
				if ( ! $guest_checkout ) {
					$user_id = get_post_meta( $reference, '_it_exchange_customer_id', true );
				} else {
					$user_id = 0;
				}

				break;	

		}

		if ( 'email' == $get ) {
			return $email_address;
		} elseif( 'user_id' == $get ) {
			return $user_id;
		}
		
	}

	/**
	 * Get an affiliate's ID from a customer's email address
	 *
	 * @param $customer_email_address The customer's email address
	 * @return int affiliate's ID
	 * @since  1.0
	 */
	public function get_affiliate_id_from_email( $customer_email_address = '' ) {

		if ( ! $customer_email_address ) {
			return;
		}

		$args = array(
			'meta_key'   => 'affwp_lc_customer_email',
			'meta_value' => $customer_email_address,
			'fields'     => 'ID',
			'number'     => '1' // there will/can only be one linked customer
		);

		$users = get_users( $args );
		
		if ( $users ) {
			return (int) $this->get_affiliate_id( $users[0] );
		}
		
		return false;
	}

	/**
	 * Get an affiliate's ID from user's ID
	 * Based on affwp_get_affiliate_id() but does not return the currently logged in affiliate ID when no user is passed in
	 *
	 * @param $user_id user ID of specified user
	 * @return int affiliate's ID
	 * @since  1.0.1
	 */
	public function get_affiliate_id( $user_id = 0 ) {

		if ( empty( $user_id ) ) {
			return false;
		}

		$affiliate = affiliate_wp()->affiliates->get_by( 'user_id', $user_id );

		if ( $affiliate ) {
			return $affiliate->affiliate_id;
		}

		return false;

	}

}