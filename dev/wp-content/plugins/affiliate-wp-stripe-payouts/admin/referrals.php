<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Stripe_Payouts_Referrals_Admin {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {
		
		add_filter( 'affwp_referral_action_links', array( $this, 'action_links' ), 10, 2 );
		add_filter( 'affwp_referrals_bulk_actions', array( $this, 'bulk_actions' ), 10, 2 );
		
		add_action( 'affwp_referrals_page_buttons', array( $this, 'bulk_pay_form' ) );
		add_action( 'affwp_pay_now', array( $this, 'process_pay_now' ) );
		add_action( 'affwp_referrals_do_bulk_action_pay_now', array( $this, 'process_bulk_action_pay_now' ) );
		add_action( 'affwp_process_bulk_stripe_payout', array( $this, 'process_bulk_stripe_payout' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	
	}

	/**
	 * Add new action links to the referral actions column
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function action_links( $links, $referral ) {

		$recipient_id = affiliate_wp_stripe()->get_recipient_id( $referral->affiliate_id );

		if( 'unpaid' == $referral->status && current_user_can( 'manage_referrals' ) && $recipient_id ) {
			$links[] = '<a href="' . esc_url( add_query_arg( array( 'affwp_action' => 'pay_now', 'referral_id' => $referral->referral_id, 'affiliate_id' => $referral->affiliate_id ) ) ) . '">' . __( 'Pay Now', 'affwp-stripe-payouts' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Register a new bulk action
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function bulk_actions( $actions ) {

		$actions['pay_now'] = __( 'Pay Now', 'affwp-stripe-payouts' );

		return $actions;
	}

	/**
	 * Render the Bulk Pay section
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function bulk_pay_form() {
?>
		<script>
		jQuery(document).ready(function($) {
			// Show referral export form
			$('.affwp-referrals-stripe-payout-toggle').click(function() {
				$('.affwp-referrals-stripe-payout-toggle').toggle();
				$('#affwp-referrals-stripe-payout-form').slideToggle();
			});
			$('#affwp-referrals-stripe-payout-form').submit(function() {
				if( ! confirm( "<?php _e( 'Are you sure you want to payout referrals for the specified time frame via Stripe?', 'affwp-stripe-payouts' ); ?>" ) ) {
					return false;
				}
			});
		});
		</script>
		<button class="button-primary affwp-referrals-stripe-payout-toggle"><?php _e( 'Bulk Pay via Stripe', 'affwp-stripe-payouts' ); ?></button>
		<button class="button-primary affwp-referrals-stripe-payout-toggle" style="display:none"><?php _e( 'Close', 'affwp-stripe-payouts' ); ?></button>
		<form id="affwp-referrals-stripe-payout-form" class="affwp-gray-form" style="display:none;" action="<?php echo admin_url( 'admin.php?page=affwp-stripe-payouts-referrals' ); ?>" method="post">
			<p>
				<input type="text" class="affwp-datepicker" autocomplete="off" name="from" placeholder="<?php _e( 'From - mm/dd/yyyy', 'affwp-stripe-payouts' ); ?>"/>
				<input type="text" class="affwp-datepicker" autocomplete="off" name="to" placeholder="<?php _e( 'To - mm/dd/yyyy', 'affwp-stripe-payouts' ); ?>"/>
				<input type="text" class="affwp-text" name="minimum" placeholder="<?php esc_attr_e( 'Minimum amount', 'affwp-stripe-payouts' ); ?>"/>
				<input type="hidden" name="affwp_action" value="process_bulk_stripe_payout"/>
				<input type="submit" value="<?php _e( 'Process Payout via Stripe', 'affwp-stripe-payouts' ); ?>" class="button-secondary"/>
				<p><?php printf( __( 'This will send payments via Stripe for all unpaid referrals in the specified timeframe.', 'affwp-stripe-payouts' ), admin_url( 'admin.php?page=affiliate-wp-tools&tab=export_import' ) ); ?></p>
			</p>
		</form>
<?php
	}

	/**
	 * Process a single referral payment
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process_pay_now( $data ) {

		$referral_id  = absint( $data['referral_id'] );

		if( empty( $referral_id ) ) {
			return;
		}

		if( ! current_user_can( 'manage_referrals' ) ) {
			wp_die( __( 'You do not have permission to process payments', 'affwp-stripe-payouts' ) );
		}

		$transfer = $this->pay_referral( $referral_id );

		if( is_wp_error( $transfer ) ) {

			wp_safe_redirect( admin_url( 'admin.php?page=affiliate-wp-referrals&affwp_notice=stripe_error&message=' . urlencode( $transfer->get_error_message() ) ) ); exit;

		}

		wp_safe_redirect( admin_url( 'admin.php?page=affiliate-wp-referrals&affwp_notice=stripe_success&referral=' . $referral_id . '&transfer=' . $transfer->id ) ); exit;
	
	}

	/**
	 * Process a referral payment for a bulk payout
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process_bulk_action_pay_now( $referral_id ) {

		if( empty( $referral_id ) ) {
			return;
		}

		if( ! current_user_can( 'manage_referrals' ) ) {
			return;
		}

		$transfer = $this->pay_referral( $referral_id );

	}

	/**
	 * Payouts referrals in bulk for a specified timeframe
	 *
	 * All referrals are summed and then paid as a single transfer for each affiliate
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process_bulk_stripe_payout() {

		if( ! current_user_can( 'manage_referrals' ) ) {
			wp_die( __( 'You do not have permission to process payments', 'affwp-stripe-payouts' ) );
		}

		$start = ! empty( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : false;
		$end   = ! empty( $_POST['to'] )   ? sanitize_text_field( $_POST['to'] )   : false;

		$args = array(
			'status' => 'unpaid',
			'date'   => array(
				'start' => $start,
				'end'   => $end
			),
			'number' => -1
		);

		// Final  affiliate / referral data to be paid out
		$data         = array();

		// The affiliates that have earnings to be paid
		$affiliates   = array();

		// The list of referrals that are possibly getting marked as paid
		$to_maybe_pay = array();

		// Retrieve the referrals from the database
		$referrals    = affiliate_wp()->referrals->get_referrals( $args );

		// The minimum payout amount
		$minimum      = ! empty( $_POST['minimum'] ) ? sanitize_text_field( affwp_sanitize_amount( $_POST['minimum'] ) ) : 0;

		if( $referrals ) {

			foreach( $referrals as $referral ) {

				if( in_array( $referral->affiliate_id, $affiliates ) ) {

					// Add the amount to an affiliate that already has a referral in the export

					$amount = $data[ $referral->affiliate_id ]['amount'] + $referral->amount;

					$data[ $referral->affiliate_id ]['amount'] = $amount;

				} else {

					$email = affwp_get_affiliate_email( $referral->affiliate_id );

					$data[ $referral->affiliate_id ] = array(
						'email'    => $email,
						'amount'   => $referral->amount,
						'currency' => ! empty( $referral->currency ) ? $referral->currency : affwp_get_currency()
					);

					$affiliates[] = $referral->affiliate_id;

				}

				// Add the referral to the list of referrals to maybe payout
				if ( ! array_key_exists( $referral->affiliate_id, $to_maybe_pay ) ) {

					$to_maybe_pay[ $referral->affiliate_id ] = array();

				}

				$to_maybe_pay[ $referral->affiliate_id ][] = $referral->referral_id;

			}

			// Process all payouts
			$errors = array();
			foreach( $data as $affiliate_id => $payout ) {

				$recipient_id = affiliate_wp_stripe()->get_recipient_id( $affiliate_id );

				if ( ! $recipient_id ) {
				
					// Ensure this affiliate can be paid via Stripe

					// Remove the affiliate's referrals from the list to be marked as paid
					unset( $to_maybe_pay[ $affiliate_id ] );

					// Skip to the next affiliate
					continue;
				
				} else if ( $minimum > 0 && $payout['amount'] < $minimum ) {
				
					// Ensure the minimum amount was reached

					// Remove the affiliate's referrals from the list to be marked as paid
					unset( $to_maybe_pay[ $affiliate_id ] );
				
					// Skip to the next affiliate
					continue;

				}

				$api_keys = affiliate_wp_stripe()->get_api_credentials();

				Stripe::setApiKey( affiliate_wp_stripe()->get_secret_key() );

				try {

					$transfer = Stripe_Transfer::create( array(
						'amount'      => $this->sanitize_amount_for_stripe( $payout['amount'] ),
						'currency'    => strtolower( affwp_get_currency() ),
						'recipient'   => $recipient_id,
						'description' => sprintf( __( 'Payment for referrals between %s and %s from %s', 'affwp-stripe-payouts' ), $start, $end, home_url() )
					) );

				} catch ( Stripe_CardError $e ) {

					$body = $e->getJsonBody();
					$err  = $body['error'];

					$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

				} catch ( Stripe_ApiConnectionError $e ) {

					$body = $e->getJsonBody();
					$err  = $body['error'];

					$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

				} catch ( Stripe_InvalidRequestError $e ) {

					$body = $e->getJsonBody();
					$err  = $body['error'];

					$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

				} catch ( Stripe_ApiError $e ) {

					$body = $e->getJsonBody();
					$err  = $body['error'];

					$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );
				
				} catch ( Stripe_AuthenticationError $e ) {

					$body = $e->getJsonBody();
					$err  = $body['error'];

					$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

				} catch ( Stripe_Error $e ) {

					$body = $e->getJsonBody();
					$err  = $body['error'];

					$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

				} catch ( Exception $e ) {

					// some sort of other error
					$body = $e->getJsonBody();
					$err  = $body['error'];

					$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

				}

				if( ! empty( $error_message ) ) {

					$errors[ $affiliate_id ] = $error_message;

					// Remove the affiliate's referrals from the list to be marked as paid
					unset( $to_maybe_pay[ $affiliate_id ] );
				}

			}

			// We now know which referrals should be marked as paid
			foreach ( $to_maybe_pay as $referral_list ) {

				foreach ( $referral_list as $referral_id ) {

					affwp_set_referral_status( $referral_id, 'paid' );
				}

			}

		}

		$redirect = admin_url( 'admin.php?page=affiliate-wp-referrals&affwp_notice=stripe_bulk_pay_success' );
		
		if( ! empty( $errors ) ) {

			foreach( $errors as $affiliate_id => $error_message ) {

				$redirect .= '&affiliate[' . $affiliate_id . ']=' . urlencode( $error_message );

			}

		}

		// A header is used here instead of wp_redirect() due to the esc_url() bug that removes [] from URLs
		header( 'Location:' . $redirect ); exit;


	}

	/**
	 * Pay a referral
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	private function pay_referral( $referral_id = 0 ) {

		if( empty( $referral_id ) ) {
			return false;
		}

		$referral = affwp_get_referral( $referral_id );

		if( empty( $referral ) ) {
			return new WP_Error( 'invalid_referral', __( 'The specified referral does not exist', 'affwp-stripe-payouts' ) );
		}

		if( empty( $referral->affiliate_id ) ) {
			return new WP_Error( 'no_affiliate', __( 'There is no affiliate connected to this referral', 'affwp-stripe-payouts' ) );
		}

		if( 'unpaid' != $referral->status ) {
			return new WP_Error( 'referral_not_unpaid', __( 'A payment cannot be processed for this referral since it is not marked as Unpaid', 'affwp-stripe-payouts' ) );
		}

		$recipient_id = affiliate_wp_stripe()->get_recipient_id( $referral->affiliate_id );

		if( empty( $recipient_id ) ) {
			return new WP_Error( 'no_recipient_id', __( 'This affiliate account does not have a Stripe Recipient ID attached', 'affwp-stripe-payouts' ) );
		}

		$transfer = false;
		$api_keys = affiliate_wp_stripe()->get_api_credentials();

		Stripe::setApiKey( affiliate_wp_stripe()->get_secret_key() );

		try {

			$transfer = Stripe_Transfer::create( array(
				'amount'      => $this->sanitize_amount_for_stripe( $referral->amount ),
				'currency'    => strtolower( affwp_get_currency() ),
				'recipient'   => $recipient_id,
				'description' => sprintf( __( 'Payment for Referral #%d for %s on %s', 'affwp-stripe-payouts' ), $referral_id, $referral->description, home_url() )
			) );

			affwp_set_referral_status( $referral_id, 'paid' );

		} catch ( Stripe_CardError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

		} catch ( Stripe_ApiConnectionError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

		} catch ( Stripe_InvalidRequestError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

		} catch ( Stripe_ApiError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );
		
		} catch ( Stripe_AuthenticationError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

		} catch ( Stripe_Error $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

		} catch ( Exception $e ) {

			// some sort of other error
			$body = $e->getJsonBody();
			$err  = $body['error'];

			$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'affwp-stripe-payouts' );

		}

		if( ! empty( $error_message ) ) {

			return new WP_Error( 'api_error', $error_message ); 

		}

		return $transfer;

	}

	/**
	 * Admin notices for success and error messages
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_notices() {

		$secret_key = affiliate_wp_stripe()->get_secret_key();
		if( empty( $secret_key ) ) {
			echo '<div class="error"><p>' . sprintf( __( 'Enter your Stripe API keys in the <a href="%s">settings</a> to enable Stripe Payouts.', 'affwp-stripe-payouts' ), admin_url( 'admin.php?page=affiliate-wp-settings&tab=stripe' ) ) . '</p></div>';
		}

		if( empty( $_REQUEST['affwp_notice' ] ) ) {
			return;
		}

		$affiliates  = ! empty( $_REQUEST['affiliate'] ) ? $_REQUEST['affiliate']                        : 0;
		$referral_id = ! empty( $_REQUEST['referral'] )  ? absint( $_REQUEST['referral'] )               : 0;
		$transfer_id = ! empty( $_REQUEST['transfer'] )  ? sanitize_text_field( $_REQUEST['transfer'] )  : '';
		$message     = ! empty( $_REQUEST['message'] )   ? urldecode( $_REQUEST['message'] )             : '';

		switch( $_REQUEST['affwp_notice'] ) {

			case 'stripe_success' :

				echo '<div class="updated"><p>' .  sprintf( __( 'Referral #%d paid out via <a href="https://dashboard.stripe.com/transfers/%s" target="_blank">Transfer %s</a> in Stripe successfully', 'affwp-stripe-payouts' ), $referral_id, $transfer_id, $transfer_id ) . '</p></div>';
				break;

			case 'stripe_error' :

				echo '<div class="error"><p><strong>' .  __( 'Error:', 'affwp-stripe-payouts' ) . '</strong>&nbsp;' . esc_html( $message ) . '</p></div>';
				break;

			case 'stripe_bulk_pay_success' :

				echo '<div class="updated"><p>' . __( 'All referrals available to be paid out have been successfully paid via Stripe', 'affwp-stripe-payouts' ) . '</p></div>';

				if( ! empty( $affiliates ) ) {

					// Some affiliates could not be paid out

					foreach( $affiliates as $affiliate_id => $error_message ) {

						$affiliate_url = admin_url( 'admin.php?page=affiliate-wp-affiliates&action=edit_affiliate&affiliate_id=' . $affiliate_id );

						echo '<div class="error"><p>' . sprintf( __( 'Affiliate <a href="%s" target="_blank">#%d</a> could not be paid: <strong>%s</strong>', 'affwp-stripe-payouts' ), $affiliate_url, $affiliate_id, urldecode( $error_message ) ) . '</p></div>';

					}

				}

		}

	}

	/**
	 * Removes decimals from specific currencies
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function sanitize_amount_for_stripe( $amount ) {

		$zero_decimal = false;
		$currency     = affwp_get_currency();

		switch( $currency ) {

			case 'BIF' :
			case 'CLP' :
			case 'DJF' :
			case 'GNF' :
			case 'JPY' :
			case 'KMF' :
			case 'KRW' :
			case 'MGA' :
			case 'PYG' :
			case 'RWF' :
			case 'VND' :
			case 'VUV' :
			case 'XAF' :
			case 'XOF' :
			case 'XPF' :

				$zero_decimal = true;
				break;

		}

		if( ! $zero_decimal ) {

			$amount *= 100;

		}

		return $amount;

	}

}
new AffiliateWP_Stripe_Payouts_Referrals_Admin;