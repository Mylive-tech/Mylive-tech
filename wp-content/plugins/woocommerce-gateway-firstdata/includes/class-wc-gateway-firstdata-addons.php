<?php
/**
 * WooCommerce First Data
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce First Data to newer
 * versions in the future. If you wish to customize WooCommerce First Data for your
 * needs please refer to http://docs.woothemes.com/document/firstdata/
 *
 * @package     WC-First-Data/Gateway-Addons
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * First Data Addons Class
 *
 * Extends the base First Data gateway to provide support for WC Add-ons -- Subscriptions and Pre-Orders
 *
 * @since 3.0
 */
class WC_Gateway_FirstData_Addons extends WC_Gateway_FirstData {


	/**
	 * Load parent gateway and add-on specific hooks
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// load parent gateway
		parent::__construct();

		// add subscription support if active and First Data tokenization is enabled
		if ( $this->tokenization_enabled() && wc_firstdata()->is_subscriptions_active() ) {

			$this->supports = array_merge( $this->supports,
				array(
					'subscriptions',
					'subscription_suspension',
					'subscription_cancellation',
					'subscription_reactivation',
					'subscription_amount_changes',
					'subscription_date_changes',
					// 1.5.x
					'subscription_payment_method_change',
					// 2.0.x
					'multiple_subscriptions',
					'subscription_payment_method_change_customer',
					'subscription_payment_method_change_admin',
				)
			);

			if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {

				// 2.0.x

				// process renewal payments
				add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'process_subscription_renewal_payment' ), 10, 2 );

				// update the customer/token ID on the subscription when updating a previously failing payment method
				add_action( 'woocommerce_subscription_failing_payment_method_updated_' . $this->id, array( $this, 'update_failing_payment_method' ), 10, 2 );

				// display the current payment method used for a subscription in the "My Subscriptions" table
				add_filter( 'woocommerce_my_subscriptions_payment_method', array( $this, 'maybe_render_subscription_payment_method' ), 10, 3 );

				// don't copy over order-specific meta to the WC_Subscription object during renewal processing
				add_filter( 'wcs_renewal_order_meta', array( $this, 'subscriptions_do_not_copy_order_meta' ) );

				// remove order-specific meta from the Subscription object after the change payment method action
				add_filter( 'woocommerce_subscriptions_process_payment_for_change_method_via_pay_shortcode', array( $this, 'remove_order_meta_from_subscriptions_change_payment' ), 10, 2 );

				// don't copy over order-specific meta to the new WC_Subscription object during upgrade to 2.0.x
				add_filter( 'wcs_upgrade_subscription_meta_to_copy', array( $this, 'do_not_copy_order_meta_during_subscriptions_upgrade' ) );

				// admin change payment method feature
				add_filter( 'woocommerce_subscription_payment_meta', array( $this, 'subscriptions_admin_add_payment_meta' ), 10, 2 );
				add_action( 'woocommerce_subscription_validate_payment_meta_' . $this->id, array( $this, 'subscriptions_admin_validate_payment_meta' ), 10 );

			} else {

				// 1.5.x

				// process scheduled subscription payments
				add_action( 'scheduled_subscription_payment_' . $this->id, array( $this, 'process_subscription_renewal_payment' ), 10, 3 );

				// prevent unnecessary order meta from polluting parent renewal orders
				add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', array( $this, 'remove_subscriptions_renewal_order_meta' ), 10, 4 );

				// update the customer payment profile ID on the original order when making payment for a failed automatic renewal order
				add_action( 'woocommerce_subscriptions_changed_failing_payment_method_' . $this->id, array( $this, 'update_failing_payment_method_1_5' ), 10, 2 );
			}
		}

		// add pre-orders support if active and First Data tokenization is enabled
		if ( $this->tokenization_enabled() && wc_firstdata()->is_pre_orders_active() ) {

			$this->supports = array_merge( $this->supports,
				array(
					'pre-orders',
				)
			);

			// process batch pre-order payments
			add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array( $this, 'process_pre_order_payment' ) );
		}
	}


	/**
	 * Don't render the "securely save card" element on the pay page for
	 * subscription transactions or pre-order requiring payment tokenization
	 * as the card will always be saved
	 *
	 * @since 3.0
	 * @return boolean true if tokenization should be forced on the checkout page, false otherwise
	 */
	public function tokenization_forced() {

		// tokenize if cart contains subscription, or payment for existing subscription is being changed
		if ( wc_firstdata()->is_subscriptions_active() ) {

			if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {

				return WC_Subscriptions_Cart::cart_contains_subscription() ||
					   wcs_cart_contains_renewal() ||
					   WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment ||
					   ( is_checkout_pay_page() && wcs_order_contains_subscription( isset( $GLOBALS['wp']->query_vars['order-pay'] ) ? absint( $GLOBALS['wp']->query_vars['order-pay'] ) : 0 ) );

			} else {

				return WC_Subscriptions_Cart::cart_contains_subscription() ||
					   ( is_checkout_pay_page() && WC_Subscriptions_Order::order_contains_subscription( isset( $GLOBALS['wp']->query_vars['order-pay'] ) ? absint( $GLOBALS['wp']->query_vars['order-pay'] ) : 0 ) ) ||
					   WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment;
			}
		}

		// always tokenize card for pre-orders charged upon release
		if ( wc_firstdata()->is_pre_orders_active() ) {

			return WC_Pre_Orders_Cart::cart_contains_pre_order() &&
				   WC_Pre_Orders_Product::product_is_charged_upon_release( WC_Pre_Orders_Cart::get_pre_order_product() );
		}

		return parent::tokenization_forced();
	}


	/**
	 * Process payment for an order:
	 * 1) If the order contains a subscription, process the initial subscription payment (could be $0 if a free trial exists)
	 * 2) If the order contains a pre-order, process the pre-order total (could be $0 if the pre-order is charged upon release)
	 * 3) Otherwise use the parent::process_payment() method for regular product purchases
	 *
	 * @since 3.0
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {

		// processing subscription
		if ( wc_firstdata()->is_subscriptions_active() && ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ? wcs_order_contains_subscription( $order_id ) : WC_Subscriptions_Order::order_contains_subscription( $order_id ) ) ) {

			$order = $this->get_order( $order_id );

			// set subscription-specific order description
			$order->description = sprintf( __( '%s - Subscription Order %s', 'woocommerce-gateway-firstdata' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );

			// get subscription amount, only for 1.5.x
			if ( ! SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {
				$order->payment_total = WC_Subscriptions_Order::get_total_initial_payment( $order );
			}

			try {

				// existing tokenized payment method?
				if ( isset( $order->payment->token ) && $order->payment->token ) {

					// zero dollar initial payment, there's nothing further to do, just record the
					if ( 0 == $order->payment_total ) {

						// save the tokenized card info for charging the subscription in the future since we'll be skipping the do_transaction() call
						update_post_meta( $order->id, '_wc_firstdata_card_type',        $order->payment->type );
						update_post_meta( $order->id, '_wc_firstdata_card_expiry_date', $order->payment->exp_date );
						update_post_meta( $order->id, '_wc_firstdata_environment',      $this->get_environment() );
						update_post_meta( $order->id, '_wc_firstdata_transarmor_token', $order->payment->token );

					}

				} else {

					if ( 0 == $order->payment_total ) {

						// otherwise if this is a zero-dollar pre-auth, then tokenize the payment method
						$order = $this->create_payment_token( $order );

						if ( ! isset( $order->payment->token ) || ! $order->payment->token ) {
							// no token returned, indicating an incorrectly configured account, can't go any further
							$this->mark_order_as_failed( $order, __( "Tokenization attempt failed.", 'woocommerce-gateway-firstdata' ), __( 'An error occurred during payment, please contact us to complete your transaction.', 'woocommerce-gateway-firstdata' ) );
							return;
						}

					}

				}

				// process transaction: if this is a zero dollar order and we already have a credit card token there's no need to perform a transaction, otherwise do so
				if (  0 == $order->payment_total || $this->do_transaction( $order ) ) {

					$order->payment_complete(); // mark order as having received payment

					$token = get_post_meta( $order->id, '_wc_firstdata_transarmor_token', true );

					// if a token wasn't set on the order (ie wasn't returned from First Data) this means tokenization might not be enabled on the First Data account
					if ( empty( $token ) ) {
						$order->update_status( 'on-hold', __( 'Expected token not returned for transaction, verify Transarmor Token is properly configured in your First Data account.', 'woocommerce-gateway-firstdata' ) );
					}

					// for Subscriptions 2.0.x, save payment token to subscription object
					if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {

						// a single order can contain multiple subscriptions
						foreach ( wcs_get_subscriptions_for_order( $order->id ) as $subscription ) {

							// payment token
							if ( ! empty( $token ) ) {
								update_post_meta( $subscription->id, '_wc_firstdata_transarmor_token', $token );
							}
						}
					}

					WC()->cart->empty_cart();

					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);
				}

			} catch ( Exception $e ) {

				// log API requests/responses here too, as exceptions could be thrown before $response object is returned
				$this->log_api();

				$this->mark_order_as_failed( $order, $e->getMessage() );
			}

		} elseif ( wc_firstdata()->is_pre_orders_active() && WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) ) {
			// processing pre-order

			// do pre-authorization
			if ( WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {

				$order = $this->get_order( $order_id );

				// set pre-order-specific order description
				$order->description = sprintf( __( '%s - Pre-Order Authorization %s', 'woocommerce-gateway-firstdata' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );

				try {

					// using an existing tokenized cc
					if ( isset( $order->payment->token ) && $order->payment->token ) {

						// save the tokenized card info for completing the pre-order in the future
						update_post_meta( $order->id, '_wc_firstdata_card_type',        $order->payment->type );
						update_post_meta( $order->id, '_wc_firstdata_card_expiry_date', $order->payment->exp_date );
						update_post_meta( $order->id, '_wc_firstdata_environment',      $this->get_environment() );
						update_post_meta( $order->id, '_wc_firstdata_transarmor_token', $order->payment->token );

					} else {

						// otherwise tokenize the payment method
						$order = $this->create_payment_token( $order );

						if ( ! $order->payment->token ) {
							// no token returned, indicating an incorrectly configured account, can't go any further
							$this->mark_order_as_failed( $order, __( "Tokenization attempt failed.", 'woocommerce-gateway-firstdata' ), __( 'An error occurred during payment, please contact us to complete your transaction.', 'woocommerce-gateway-firstdata' ) );
							return;
						}
					}

					// mark order as pre-ordered / reduce order stock
					WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );

					// empty cart
					WC()->cart->empty_cart();

					// redirect to thank you page
					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);

				} catch ( Exception $e ) {

					// log API requests/responses here too, as exceptions could be thrown before $response object is returned
					$this->log_api();

					$this->mark_order_as_failed( $order, $e->getMessage() );
				}

			} else {

				// charged upfront (or paying for a newly-released pre-order with the gateway), process just like regular product
				return parent::process_payment( $order_id );
			}

		} else {

			// processing regular product
			return parent::process_payment( $order_id );
		}
	}


	/**
	 * Process a pre-order payment when the pre-order is released
	 *
	 * @since 3.0
	 * @param \WC_Order $order original order containing the pre-order
	 * @throws Exception
	 */
	public function process_pre_order_payment( $order ) {

		// set order defaults
		$order = $this->get_order( $order->id );

		// order description
		$order->description = sprintf( __( '%s - Pre-Order Release Payment for Order %s', 'woocommerce-gateway-firstdata' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );

		// get the card token to complete the order
		$order->payment->token    = $order->wc_firstdata_transarmor_token;
		$order->payment->exp_date = $order->wc_firstdata_card_expiry_date;
		$order->payment->type     = $order->wc_firstdata_card_type;

		try {

			// token, expiration date and type are required
			if ( ! $order->payment->token || ! $order->payment->exp_date || ! $order->payment->type )
				throw new Exception( __( 'Pre-Order Release: Customer or Payment Profile is missing.', 'woocommerce-gateway-firstdata' ) );

			$response = $this->get_api()->create_new_transaction( $order );

			$this->log_api();

			// success! update order record
			if ( $response->transaction_approved() ) {

				// add order note
				$order->add_order_note( sprintf( __( 'First Data Pre-Order Release Payment Approved (Sequence Number: %s) ', 'woocommerce-gateway-firstdata' ), $response->get_sequence_no() ) );

				// complete the order
				$order->payment_complete();

			} else {

				// log API requests/responses here too, as exceptions could be thrown before $response object is returned
				$this->log_api();

				// failure
				throw new Exception( $response->get_failure_message() );
			}

		} catch ( Exception $e ) {

			// Mark order as failed
			$message = sprintf( __( 'First Data Pre-Order Release Payment Failed (Result: %s)', 'woocommerce-gateway-firstdata' ), $e->getMessage() );

			if ( ! $order->has_status( 'failed' ) ) {
				$order->update_status( 'failed', $message );
			} else {
				$order->add_order_note( $message );
			}

			$this->add_debug_message( $e->getMessage(), 'error' );
		}
	}


	/**
	 * Process subscription renewal for Subscriptions 1.5.x/2.0.x
	 *
	 * @since 3.7.1
	 * @param float $amount_to_charge subscription amount to charge, could include multiple renewals if they've previously failed and the admin has enabled it
	 * @param WC_Order $order original order containing the subscription
	 * @param int $product_id the ID of the subscription product
	 * @throws Exception
	 */
	public function process_subscription_renewal_payment( $amount_to_charge, $order, $product_id = null ) {

		try {

			// set order defaults
			$order = $this->get_order( $order->id );

			// set custom class members used by API ( @see WC_Gateway_FirstData::get_order() )
			$order->payment_total    = $amount_to_charge;
			$order->transaction_type = 0 != $order->payment_total ? $this->transaction_type : WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION_ONLY;
			$order->description      = sprintf( __( '%s - Renewal for Subscription Order %s', 'woocommerce-gateway-firstdata' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $order->get_order_number() );

			$token = $order->wc_firstdata_transarmor_token;
			$card  = $token ? $this->get_credit_card_token_details( $order->get_user_id(), $token ) : null;

			// either missing token from parent order, or the token no longer corresponds to one of our saved cards
			if ( ! $token || ! $card ) {
				// just default to the active token, if any
				$token = $this->get_active_credit_card_token( $order->get_user_id() );

				// ok nothing else we can do
				if ( ! $token ) {
					throw new Exception( __( 'Subscription Renewal: TransArmor token is missing.', 'woocommerce-gateway-firstdata' ) );
				} else {
					// set it to the order and get the card
					$card = $this->get_credit_card_token_details( $order->get_user_id(), $token );
					update_post_meta( $order->id, '_wc_firstdata_transarmor_token', $token );

					if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {

						foreach ( wcs_get_subscriptions_for_renewal_order( $order ) as $subscription ) {
							update_post_meta( $subscription->id, '_wc_firstdata_transarmor_token', $token );
						}
					}
				}
			}

			// set the fields required for correct order processing
			$order->payment->token    = $token;
			$order->payment->exp_date = $card['exp_date'];
			$order->payment->type     = $card['type'];

			$response = $this->get_api()->create_new_transaction( $order );

			// log the transaction
			$this->add_debug_message( __( 'Subscription Renewal', 'woocommerce-gateway-firstdata' ) );
			$this->log_api();

			// success! update order record
			if ( $response->transaction_approved() ) {

				// add order note
				$order->add_order_note( __( 'First Data Subscription Renewal Payment Approved', 'woocommerce-gateway-firstdata' ) );

				// update subscription
				if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {

					$order->payment_complete( $response->get_transaction_tag() );

				} else {

					WC_Subscriptions_Manager::process_subscription_payments_on_order( $order, $product_id );
				}

			} else {
				// failure
				throw new Exception( $response->get_failure_message() );
			}

		} catch ( Exception $e ) {

			// log API requests/responses here too, as exceptions could be thrown before $response object is returned
			$this->log_api();

			$this->add_debug_message( $e->getMessage(), 'error' );

			$failed_message = sprintf( __( 'First Data Subscription Renewal Payment Failed (Result: %s)', 'woocommerce-gateway-firstdata' ), $e->getMessage() );

			// update subscription
			if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {

				$this->mark_order_as_failed( $order, $failed_message );

			} else {

				$order->add_order_note( $failed_message );

				WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order, $product_id );
			}
		}
	}


	/** Subscriptions 2.0.x Support *******************************************/


	/**
	 * Don't copy order-specific meta to renewal orders from the WC_Subscription
	 * object. Generally the subscription object should not have any order-specific
	 * meta (aside from `payment_token` and `customer_id`) as they are not
	 * copied during the upgrade (see do_not_copy_order_meta_during_upgrade()), so
	 * this method is more of a fallback in case meta accidentally is copied.
	 *
	 * @since 3.7.1
	 * @param array $order_meta order meta to copy
	 * @return array
	 */
	public function subscriptions_do_not_copy_order_meta( $order_meta ) {

		$meta_keys = $this->get_order_specific_meta_keys();

		foreach ( $order_meta as $index => $meta ) {

			if ( in_array( $meta['meta_key'], $meta_keys ) ) {
				unset( $order_meta[ $index ] );
			}
		}

		return $order_meta;
	}


	/**
	 * Don't copy order-specific meta to the new WC_Subscription object during
	 * upgrade to 2.0.x. This only allows the `_wc_firstdata_transamor_token`
	 * meta to be copied.
	 *
	 * @since 3.7.1
	 * @param array $order_meta order meta to copy
	 * @return array
	 */
	public function do_not_copy_order_meta_during_subscriptions_upgrade( $order_meta ) {

		foreach ( $this->get_order_specific_meta_keys() as $meta_key ) {

			if ( isset( $order_meta[ $meta_key ] ) ) {
				unset( $order_meta[ $meta_key ] );
			}
		}

		return $order_meta;
	}


	/**
	 * Remove order meta (like trans ID) that's added to a Subscription object
	 * during the change payment method flow, which uses WC_Payment_Gateway::process_payment(),
	 * thus some order-specific meta is added that is undesirable to have copied
	 * over to renewal orders.
	 *
	 * @since 3.7.1
	 * @param array $result process_payment() result, unused
	 * @param \WC_Subscription $subscription subscription object
	 * @return array
	 */
	public function remove_order_meta_from_subscriptions_change_payment( $result, $subscription ) {

		// remove order-specific meta
		foreach ( $this->get_order_specific_meta_keys() as $meta_key ) {
			delete_post_meta( $subscription->id, $meta_key );
		}

		// if the payment method has been changed to another gateway, additionally remove the old payment token and customer ID meta
		if ( $subscription->payment_method !== $this->id && $subscription->old_payment_method === $this->id ) {
			delete_post_meta( $subscription->id, '_wc_firstdata_transarmor_token' );
		}

		return $result;
	}


	/**
	 * Update the payment token and optional customer ID for a subscription after a customer
	 * uses this gateway to successfully complete the payment for an automatic
	 * renewal payment which had previously failed.
	 *
	 * @since 3.7.1
	 * @param \WC_Subscription $subscription subscription being updated
	 * @param \WC_Order $renewal_order order which recorded the successful payment (to make up for the failed automatic payment).
	 */
	public function update_failing_payment_method( $subscription, $renewal_order ) {

		update_post_meta( $subscription->id, '_wc_firstdata_transarmor_token', get_post_meta( $renewal_order->id, '_wc_firstdata_transarmor_token', true ) );
	}


	/**
	 * Get the order-specific meta keys that should not be copied to the WC_Subscription
	 * object during upgrade to 2.0.x or during change payment method actions
	 *
	 * @since 3.7.1
	 * @return array
	 */
	protected function get_order_specific_meta_keys() {

		return array(
			'_wc_firstdata_transaction_tag',
			'_wc_firstdata_authorization_num',
			'_wc_firstdata_sequence_no',
			'_wc_firstdata_card_type',
			'_wc_firstdata_card_last_four',
			'_wc_firstdata_card_expiry_date',
			'_wc_firstdata_environment',
		);
	}


	/**
	 * Render the payment method used for a subscription in the "My Subscriptions" table
	 *
	 * @since 3.7.1
	 * @param string $payment_method_to_display the default payment method text to display
	 * @param \WC_Subscription $subscription
	 * @return string the subscription payment method
	 */
	public function maybe_render_subscription_payment_method( $payment_method_to_display, $subscription ) {

		// bail for other payment methods
		if ( $this->id !== $subscription->payment_method ) {
			return $payment_method_to_display;
		}

		$token = get_post_meta( $subscription->id, '_wc_firstdata_transarmor_token', true );
		$card = $token ? $this->get_credit_card_token_details( $subscription->get_user_id(), $token ) : null;

		if ( $card ) {
			$payment_method_to_display = sprintf( __( 'Via %s ending in %s', 'woocommerce-gateway-firstdata' ), $card['type'], $card['last_four'] );
		}

		return $payment_method_to_display;
	}


	/**
	 * Include the payment meta data required to process automatic recurring
	 * payments so that store managers can manually set up automatic recurring
	 * payments for a customer via the Edit Subscriptions screen in 2.0.x
	 *
	 * @since 3.7.1
	 * @param array $meta associative array of meta data required for automatic payments
	 * @param \WC_Subscription $subscription subscription object
	 * @return array
	 */
	public function subscriptions_admin_add_payment_meta( $meta, $subscription ) {

		$meta[ $this->id ] = array(
			'post_meta' => array(
				'_wc_firstdata_transarmor_token'   => array(
					'value' => get_post_meta( $subscription->id, '_wc_firstdata_transarmor_token', true ),
					'label' => __( 'TransArmor Token', 'woocommerce-gateway-firstdata' ),
				),
			)
		);

		return $meta;
	}


	/**
	 * Validate the payment meta data required to process automatic recurring
	 * payments so that store managers can manually set up automatic recurring
	 * payments for a customer via the Edit Subscriptions screen in 2.0.x
	 *
	 * @since 3.7.1
	 * @param array $meta associative array of meta data required for automatic payments
	 * @throws Exception if payment token or customer ID is missing or blank
	 */
	public function subscriptions_admin_validate_payment_meta( $meta ) {

		// payment token
		if ( empty( $meta['post_meta']['_wc_firstdata_transarmor_token']['value'] ) ) {
			throw new Exception( __( 'TransArmor Token is required.', 'woocommerce-gateway-firstdata' ) );
		} elseif ( ! ctype_digit( (string) $meta['post_meta']['_wc_firstdata_transarmor_token']['value'] ) ) {
			throw new Exception( __( 'TransArmor Token must be numeric.', 'woocommerce-gateway-firstdata' ) );
		}
	}


	/** Subscriptions 1.5.x Support *******************************************/


	/**
	 * Update the order token meta for a subscription after a customer used
	 * First Data to successfully complete the payment for an automatic renewal
	 * payment which had previously failed.
	 *
	 * @since 3.3
	 * @param WC_Order $original_order The original order in which the subscription was purchased
	 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment)
	 */
	public function update_failing_payment_method_1_5( WC_Order $original_order, WC_Order $renewal_order ) {

		update_post_meta( $original_order->id, '_wc_firstdata_transarmor_token', $renewal_order->wc_firstdata_transarmor_token );
	}


	/**
	 * Don't copy over profile/payment meta when creating a parent renewal order
	 *
	 * @since 3.0
	 * @param array $order_meta_query MySQL query for pulling the metadata
	 * @param int $original_order_id Post ID of the order being used to purchased the subscription being renewed
	 * @param int $renewal_order_id Post ID of the order created for renewing the subscription
	 * @param string $new_order_role The role the renewal order is taking, one of 'parent' or 'child'
	 * @return string
	 */
	public function remove_subscriptions_renewal_order_meta( $order_meta_query, $original_order_id, $renewal_order_id, $new_order_role ) {

		if ( 'parent' == $new_order_role )
			$order_meta_query .= " AND `meta_key` NOT IN ("
							  . "'_wc_firstdata_transaction_tag', "
							  . "'_wc_firstdata_authorization_num', "
							  . "'_wc_firstdata_sequence_no', "
							  . "'_wc_firstdata_card_type', "
							  . "'_wc_firstdata_card_last_four', "
							  . "'_wc_firstdata_card_expiry_date', "
							  . "'_wc_firstdata_environment', "
							  . "'_wc_firstdata_transarmor_token' )";

		return $order_meta_query;
	}


}
