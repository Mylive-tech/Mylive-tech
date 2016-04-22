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
 * @package     WC-First-Data/Gateway
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * First Data Gateway Base Class
 *
 * Handles all purchases, displaying saved cards, etc
 *
 * @since 3.0
 */
class WC_Gateway_FirstData extends WC_Payment_Gateway {


	/** Sends through sale and request for funds to be charged to cardholder's credit card. */
	const TRANSACTION_TYPE_CHARGE = 'charge';

	/** Sends through a request for funds to be "reserved" on the cardholder's credit card. A standard authorization is reserved for 2-5 days. Reservation times are determined by cardholder's bank. */
	const TRANSACTION_TYPE_AUTHORIZATION = 'authorization';

	/** The REST production URL endpoint (requires version number to be substituted in) */
	const PRODUCTION_URL_ENDPOINT = 'https://api.globalgatewaye4.firstdata.com/transaction/v%s';

	/** The REST demo URL endpoint (requires version number to be substituted in) */
	const DEMO_URL_ENDPOINT       = 'https://api.demo.globalgatewaye4.firstdata.com/transaction/v%s';

	/** @var string the transaction environment, one of 'production' or 'demo', defaults to 'production' */
	private $environment;

	/** @var string the unique production account gateway id */
	private $gateway_id;

	/** @var string the private production gateway password */
	private $gateway_password;

	/** @var string the production terminal API Access Key Id used for v12 authentication */
	private $key_id;

	/** @var string the private production terminal API Access Key used for v12 authentication */
	private $hmac_key;

	/** @var string the unique demo account gateway id */
	private $demo_gateway_id;

	/** @var string the private demo gateway password */
	private $demo_gateway_password;

	/** @var string the demo terminal API Access Key Id used for v12 authentication */
	private $demo_key_id;

	/** @var string the private demo terminal API Access Key used for v12 authentication */
	private $demo_hmac_key;

	/** @var string the type of transaction, whether purchase or pre-authorization, defaults to 'purchase' */
	protected $transaction_type;

	/** @var string support partial redemption transactions (ie $100 transaction, with only $80 available on the card). One of 'yes' or 'no', defaults to 'no' */
	private $partial_redemption;

	/** @var array card types to show images for */
	private $card_types;

	/** @var string indicates whether tokenization is enabled, either 'yes' or 'no' */
	private $tokenization;

	/** @var string 4 options for debug mode - off, checkout, log, both */
	public $debug_mode;

	/** @var WC_FirstData_API instance */
	protected $api;

	/** @var string 'yes' if soft descriptors are enabled, false otherwise */
	private $soft_descriptors_enabled;

	/** @var string Soft Descriptor DBA Name */
	private $soft_descriptor_dba_name;

	/** @var string Soft Descriptor Business address */
	private $soft_descriptor_street;

	/** @var string Soft Descriptor Business city */
	private $soft_descriptor_city;

	/** @var string Soft Descriptor Business region */
	private $soft_descriptor_region;

	/** @var string Soft Descriptor Business postal code */
	private $soft_descriptor_postal_code;

	/** @var string Soft Descriptor Business country */
	private $soft_descriptor_country_code;

	/** @var string Soft Descriptor merchant id */
	private $soft_descriptor_mid;

	/** @var string Soft Descriptor merchant category code */
	private $soft_descriptor_mcc;

	/** @var string Soft Descriptor merchant contact information */
	private $soft_descriptor_merchant_contact_info;


	/**
	 * Initialize the First Data gateway
	 *
	 * @since 3.0
	 */
	public function __construct() {

		$this->id                 = WC_FirstData::GGE4_GATEWAY_ID;
		$this->method_title       = __( 'First Data Payeezy', 'woocommerce-gateway-firstdata' );
		$this->method_description = __( '(Formely First Data GGe4) Allow customers to securely save their credit card to their account for use with single purchases, pre-orders, and subscriptions.', 'woocommerce-gateway-firstdata' );

		$this->supports = array( 'products', 'refunds' );

		$this->has_fields = true;

		$this->icon = apply_filters( 'wc_firstdata_icon', '' );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings
		$this->init_settings();

		// Define user set variables
		foreach ( $this->settings as $setting_key => $setting ) {
			$this->$setting_key = $setting;
		}

		// pay page fallback
		add_action( 'woocommerce_receipt_' . $this->id, create_function( '$order', 'echo "<p>" . __( "Thank you for your order.", "woocommerce-gateway-firstdata" ) . "</p>";' ) );

		// Save settings
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
	}


	/**
	 * Initialize payment gateway settings fields
	 *
	 * @since 3.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'enabled' => array(
				'title'   => __( 'Enable / Disable', 'woocommerce-gateway-firstdata' ),
				'label'   => __( 'Enable this gateway', 'woocommerce-gateway-firstdata' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),

			'title' => array(
				'title'    => __( 'Title', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'desc_tip' => __( 'Payment method title that the customer will see during checkout.', 'woocommerce-gateway-firstdata' ),
				'default'  => 'Credit Card',
			),

			'description' => array(
				'title'    => __( 'Description', 'woocommerce-gateway-firstdata' ),
				'type'     => 'textarea',
				'desc_tip' => __( 'Payment method description that the customer will see during checkout.', 'woocommerce-gateway-firstdata' ),
				'default'  => __( 'Pay securely using your credit card.', 'woocommerce-gateway-firstdata' ),
			),

			'card_types' => array(
				'title'    => __( 'Accepted Card Logos', 'woocommerce-gateway-firstdata' ),
				'type'     => 'multiselect',
				'desc_tip' => __( 'Select which card types you accept to display the logos for on your checkout page.  This is purely cosmetic and optional, and will have no impact on the cards actually accepted by your account.', 'woocommerce-gateway-firstdata' ),
				'default'  => array( 'VISA', 'MC', 'AMEX', 'JCB', 'DISC', 'DINERS' ),
				'class'    => 'wc-enhanced-select',
				'css'      => 'width: 350px;',
				'options'  => apply_filters( 'wc_firstdata_card_types',
					array(
						'VISA'   => 'Visa',
						'MC'     => 'MasterCard',
						'AMEX'   => 'American Express',
						'JCB'    => 'JCB',
						'DISC'   => 'Discover',
						'DINERS' => 'Diners',
					)
				),
			),

			'environment' => array(
				'title'    => __( 'Environment', 'woocommerce-gateway-firstdata' ),
				'type'     => 'select',
				'default'  => 'production',
				'desc_tip' => __( 'The production environment should be used unless you have a separate demo account.', 'woocommerce-gateway-firstdata' ),
				'options'  => array(
					'production' => __( 'Production', 'woocommerce-gateway-firstdata' ),
					'demo'       => __( 'Demo', 'woocommerce-gateway-firstdata' ),
				),
			),

			'gateway_id' => array(
				'title'    => __( 'Gateway ID', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'class'    => 'production-field',
				'desc_tip' => __( 'Your unique Gateway ID, note this is not the same as your account user name.', 'woocommerce-gateway-firstdata' ),
			),

			'gateway_password' => array(
				'title'    => __( 'Gateway Password', 'woocommerce-gateway-firstdata' ),
				'type'     => 'password',
				'class'    => 'production-field',
				'desc_tip' => __( 'Your private gateway password, note this is not the same as your account user password.', 'woocommerce-gateway-firstdata' ),
			),

			'key_id' => array(
				'title'    => __( 'Key id', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'class'    => 'production-field',
				'desc_tip' => __( 'Your terminal API Access Key id.', 'woocommerce-gateway-firstdata' ),
			),

			'hmac_key' => array(
				'title'    => __( 'Hmac Key', 'woocommerce-gateway-firstdata' ),
				'type'     => 'password',
				'class'    => 'production-field',
				'desc_tip' => __( 'Your terminal API secret Hmac key.', 'woocommerce-gateway-firstdata' ),
			),

			'demo_gateway_id' => array(
				'title'    => __( 'Gateway ID', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'class'    => 'demo-field',
				'desc_tip' => __( 'Your unique Gateway ID, note this is not the same as your account user name and will be in the format of Axxxxx-xx.', 'woocommerce-gateway-firstdata' ),
			),

			'demo_gateway_password' => array(
				'title'    => __( 'Gateway Password', 'woocommerce-gateway-firstdata' ),
				'type'     => 'password',
				'class'    => 'demo-field',
				'desc_tip' => __( 'Your private gateway password, note this is not the same as your account user password.', 'woocommerce-gateway-firstdata' ),
			),

			'demo_key_id' => array(
				'title'    => __( 'Key id', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'class'    => 'demo-field',
				'desc_tip' => __( 'Your terminal API Access Key id.', 'woocommerce-gateway-firstdata' ),
			),

			'demo_hmac_key' => array(
				'title'    => __( 'Hmac Key', 'woocommerce-gateway-firstdata' ),
				'type'     => 'password',
				'class'    => 'demo-field',
				'desc_tip' => __( 'Your terminal API  secret Hmac key.', 'woocommerce-gateway-firstdata' ),
			),

			'transaction_type' => array(
				'title'    => __( 'Transaction Type', 'woocommerce-gateway-firstdata' ),
				'type'     => 'select',
				'desc_tip' => __( 'Select how transactions should be processed. Purchase submits all transactions for settlement, Pre-Authorization simply authorizes the order total for capture later.', 'woocommerce-gateway-firstdata' ),
				'default'  => self::TRANSACTION_TYPE_CHARGE,
				'options'  => array(
					self::TRANSACTION_TYPE_CHARGE        => __( 'Purchase', 'woocommerce-gateway-firstdata' ),
					self::TRANSACTION_TYPE_AUTHORIZATION => __( 'Pre-Authorization', 'woocommerce-gateway-firstdata' ),
				),
			),

			'partial_redemption' => array(
				'title'    => __( 'Partial Redemption', 'woocommerce-gateway-firstdata' ),
				'label'    => __( 'Enable Partial Redemption', 'woocommerce-gateway-firstdata' ),
				'type'     => 'checkbox',
				'desc_tip' => __( 'A partial redemption will be returned if only a portion of the requested funds are available. For example, if a transaction is submitted for $100, but only $80 is available on the customer\'s card, the $80 will be authorized or captured when this property is set to true. This property can be used for all types of pre-authorization and purchase transactions.',  'woocommerce-gateway-firstdata' ),
				'default'  => 'no',
			),

			'tokenization' => array(
				'title'       => __( 'Tokenization', 'woocommerce-gateway-firstdata' ),
				'label'       => __( 'Allow customers to securely save their credit card details for future checkout.', 'woocommerce-gateway-firstdata' ),
				'type'        => 'checkbox',
				'description' => 'Note that you <strong>must first</strong> enable TransArmor Multi-Pay token processing in your account by contacting your merchant services provider for your token and configuring your account with it.',
				'default'     => 'no',
			),

			'soft_descriptors_enabled' => array(
				'title'       => __( 'Soft Descriptors', 'woocommerce-gateway-firstdata' ),
				'label'       => __( 'Enable soft descriptors', 'woocommerce-gateway-firstdata' ),
				'type'        => 'checkbox',
				'description' => 'All of the soft descriptors are optional.  If you would like to use Soft Descriptors, please contact your First Data Relationship Manager or Sales Rep and have them set your "Foreign Indicator" in your "North Merchant Manager File" to "5".',
				'default'     => 'no',
			),

			'soft_descriptor_dba_name' => array(
				'title'    => __( 'DBA Name', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business name.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_street' => array(
				'title'    => __( 'Street', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business address.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_city' => array(
				'title'    => __( 'City', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business city.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_region' => array(
				'title'    => __( 'Region', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business region.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_postal_code' => array(
				'title'    => __( 'Postal Code', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business postal/zip code.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_country_code' => array(
				'title'    => __( 'Country Code', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business country.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_mid' => array(
				'title'    => __( 'MID', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Merchant ID.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_mcc' => array(
				'title'    => __( 'MCC', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Your Merchant Category Code.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_merchant_contact_info' => array(
				'title'    => __( 'Merchant Contact Info', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Merchant contact information.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'debug_mode' => array(
				'title'    => __( 'Debug Mode', 'woocommerce-gateway-firstdata' ),
				'type'     => 'select',
				'desc_tip' => __( 'Show Detailed Error Messages and API requests / responses on the checkout page and/or save them to the log for debugging purposes.', 'woocommerce-gateway-firstdata' ),
				'default'  => 'off',
				'options'  => array(
					'off'      => __( 'Off', 'woocommerce-gateway-firstdata' ),
					'checkout' => __( 'Show on Checkout Page', 'woocommerce-gateway-firstdata' ),
					'log'      => __( 'Save to Log', 'woocommerce-gateway-firstdata' ),
					'both'     => __( 'Both', 'woocommerce-gateway-firstdata' )
				),
			),
		);
	}


	/**
	 * Display settings page with some additional javascript for hiding conditional fields
	 *
	 * @since 3.0
	 */
	public function admin_options() {

		parent::admin_options();

		// add inline javascript
		ob_start();
		?>
			$( '#woocommerce_firstdata_environment' ).change( function() {

				var environment = $( this ).val();

				if ( 'production' == environment ) {
					$( '.production-field' ).closest( 'tr' ).show();
					$( '.demo-field' ).closest( 'tr' ).hide();
				} else {
					$( '.demo-field' ).closest( 'tr' ).show();
					$( '.production-field' ).closest( 'tr' ).hide();
				}
			} ).change();

			$( '#woocommerce_firstdata_soft_descriptors_enabled' ).change( function() {

				var enabled = $( this ).is( ':checked' );

				if ( enabled )
					$( '.soft-descriptor' ).closest( 'tr' ).show();
				else
					$( '.soft-descriptor' ).closest( 'tr' ).hide();

			} ).change();
		<?php

		wc_enqueue_js( ob_get_clean() );
	}


	/**
	 * Checks for proper gateway configuration (required fields populated, etc)
	 * and that there are no missing dependencies
	 *
	 * @since 3.0
	 */
	public function is_available() {

		// is enabled check
		$is_available = parent::is_available();

		// proper configuration
		if ( ! $this->get_gateway_id() || ! $this->get_gateway_password() )
			$is_available = false;

		// all dependencies met
		if ( count( wc_firstdata()->get_missing_dependencies() ) > 0 )
			$is_available = false;

		return apply_filters( 'wc_gateway_firstdata_is_available', $is_available );
	}


	/**
	 * Add selected card icons to payment method label, defaults to Visa/MC/Amex/JCB/Discover/Diners
	 *
	 * @since 3.0
	 */
	public function get_icon() {

		$icon = '';

		if ( $this->icon ) {

			// use icon provided by filter
			$icon = '<img src="' . esc_url( WC_HTTPS::force_https_url( $this->icon ) ) . '" alt="' . esc_attr( $this->title ) . '" />';

		} elseif ( ! empty( $this->card_types ) ) {

			// display icons for the selected card types
			foreach ( $this->card_types as $card_type ) {

				if ( is_readable( wc_firstdata()->get_plugin_path() . '/assets/images/card-' . strtolower( $card_type ) . '.png' ) )
					$icon .= '<img src="' . esc_url( WC_HTTPS::force_https_url( wc_firstdata()->get_plugin_url() ) . '/assets/images/card-' . strtolower( $card_type ) . '.png' ) . '" alt="' . esc_attr( strtolower( $card_type ) ) . '" />';
			}

		}

		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
	}


	/**
	 * Display the payment fields on the checkout page
	 *
	 * @since 3.0
	 */
	public function payment_fields() {

		woocommerce_firstdata_payment_fields( $this );
	}


	/**
	 * Validate the payment fields when processing the checkout
	 *
	 * @since 3.0
	 * @return bool true if fields are valid, false otherwise
	 */
	public function validate_fields() {

		$is_valid = parent::validate_fields();

		// tokenized transaction?
		if ( $this->get_post( 'firstdata-token' ) ) {

			// unknown token?
			if ( ! $this->get_credit_card_token_details( get_current_user_id(), $this->get_post( 'firstdata-token' ) ) ) {
				SV_WC_Helper::wc_add_notice( __( 'Payment error, please try another payment method or contact us to complete your transaction.', 'woocommerce-gateway-firstdata' ), 'error' );
				$is_valid = false;
			}

			// no more validation to perform
			return $is_valid;
		}

		$card_number      = $this->get_post( 'firstdata-account-number' );
		$expiration_month = $this->get_post( 'firstdata-exp-month' );
		$expiration_year  = $this->get_post( 'firstdata-exp-year' );
		$cvv              = $this->get_post( 'firstdata-cvv' );

		// check security code
		if ( empty( $cvv ) ) {
			SV_WC_Helper::wc_add_notice( __( 'Card security code is missing', 'woocommerce-gateway-firstdata' ), 'error' );
			$is_valid = false;
		}

		// digit check
		if ( ! ctype_digit( $cvv ) ) {
			SV_WC_Helper::wc_add_notice( __( 'Card security code is invalid (only digits are allowed)', 'woocommerce-gateway-firstdata' ), 'error' );
			$is_valid = false;
		}

		// length check
		if ( strlen( $cvv ) < 3 || strlen( $cvv ) > 4 ) {
			SV_WC_Helper::wc_add_notice( __( 'Card security code is invalid (must be 3 or 4 digits)', 'woocommerce-gateway-firstdata' ), 'error' );
			$is_valid = false;
		}

		// check expiration data
		$current_year  = date( 'Y' );
		$current_month = date( 'n' );

		if ( ! ctype_digit( $expiration_month ) || ! ctype_digit( $expiration_year ) ||
			$expiration_month > 12 ||
			$expiration_month < 1 ||
			$expiration_year < $current_year ||
			( $expiration_year == $current_year && $expiration_month < $current_month ) ||
			$expiration_year > $current_year + 20
		) {
			SV_WC_Helper::wc_add_notice( __( 'Card expiration date is invalid', 'woocommerce-gateway-firstdata' ), 'error' );
			$is_valid = false;
		}

		// check card number
		$card_number = str_replace( array( ' ', '-' ), '', $card_number );

		if ( empty( $card_number ) || ! ctype_digit( $card_number ) || ! $this->luhn_check( $card_number ) ) {
			SV_WC_Helper::wc_add_notice( __( 'Card number is invalid', 'woocommerce-gateway-firstdata' ), 'error' );
			$is_valid = false;
		}

		return apply_filters( 'wc_first_data_validate_fields', $is_valid, $this );
	}


	/**
	 * Handles payment processing
	 *
	 * @since 3.0
	 */
	public function process_payment( $order_id ) {

		// add payment information to order
		$order = $this->get_order( $order_id );

		try {

			// payment failures are handled internally by do_transaction()
			if ( $this->do_transaction( $order ) ) {

				$order->payment_complete(); // mark order as having received payment

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
	}


	/**
	 * Process refund
	 *
	 * @since 3.5.0
	 * @param int $order_id the order ID
	 * @param float $amount the amount to refund
	 * @param string $reason an optional reason for refund (unused)
	 * @return bool|WP_Error true if refund was successful, or a WP_Error object if not
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {

		$order = wc_get_order( $order_id );

		// add info required by First Data to process refund
		$order->refund                    = new stdClass();
		$order->refund->amount            = $amount;
		$order->refund->transaction_tag   = $order->wc_firstdata_transaction_tag;
		$order->refund->authorization_num = $order->wc_firstdata_authorization_num;

		if ( ! $order->refund->transaction_tag || ! $order->refund->authorization_num ) {
			return new WP_Error( 'wc_first_data_refund_error', __( 'First Data Transaction Tag or Authorization Number missing, cannot refund transaction.', 'woocommerce-gateway-firstdata' ) );
		}

		try {

			// perform the refund
			$response = $this->get_api()->create_new_refund( $order );

			$this->log_api();

			if ( $response->transaction_approved() ) {

				// add transaction identifiers
				add_post_meta( $order->id, '_wc_firstdata_refund_transaction_tag',   $response->get_transaction_tag() );
				add_post_meta( $order->id, '_wc_firstdata_refund_authorization_num', $response->get_authorization_num() );
				add_post_meta( $order->id, '_wc_firstdata_refund_sequence_no',       $response->get_sequence_no() );

				// add order note
				$order->add_order_note( sprintf( __( 'First Data Refund in the amount of %s approved.', 'woocommerce-gateway-firstdata' ), wc_price( $amount, array( 'currency' => $order->get_order_currency() ) ) ) );

				return true;

			} else {

				$order->add_order_note( sprintf( __( 'First Data Refund failed: %s', 'woocommerce-gateway-firstdata' ), $response->get_failure_message() ) );

				return new WP_Error( 'wc_first_data_refund_failed', $response->get_failure_message() );
			}

		} catch ( Exception $e ) {

			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}


	/**
	 * Add payment and transaction information as class members of WC_Order instance
	 *
	 * @since 3.0
	 * @param int $order_id order ID being processed
	 * @return WC_Order object with payment and transaction information attached
	 */
	protected function get_order( $order_id ) {

		$order = wc_get_order( $order_id );

		// add payment info
		$order->payment = new stdClass();

		if ( $this->get_post( 'firstdata-account-number' ) && ! $this->get_post( 'firstdata-token' ) ) {

			// paying with credit card
			$order->payment->account_number = $this->get_post( 'firstdata-account-number' );
			$order->payment->exp_date       = $this->get_post( 'firstdata-exp-month' ) . substr( $this->get_post( 'firstdata-exp-year' ), -2 );
			$order->payment->cvv            = $this->get_post( 'firstdata-cvv' );

		} elseif ( $this->get_post( 'firstdata-token' ) ) {

			// paying with tokenized credit card
			$card = $this->get_credit_card_token_details( get_current_user_id(), $this->get_post( 'firstdata-token' ) );

			$order->payment->token    = $this->get_post( 'firstdata-token' );
			$order->payment->exp_date = $card['exp_date'];
			$order->payment->type     = $card['type'];
		}

		// set payment total here so it can be modified for later by add-ons like subscriptions which may need to charge an amount different than the get_total()
		$order->payment_total = number_format( $order->get_total(), 2, '.', '' );

		// for $0 transactions perform a Zero Dollar Pre-Authorization Only https://firstdata.zendesk.com/entries/407571-First-Data-Global-Gateway-e4-Web-Service-API-Reference-Guide#3.2
		$order->transaction_type   = 0 != $order->payment_total ? $this->transaction_type : WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION_ONLY;
		$order->partial_redemption = $this->allow_partial_redemption();

		if ( $this->soft_descriptors_enabled() ) {

			$order->payment->soft_descriptor = new stdClass();
			$order->payment->soft_descriptor->dba_name              = SV_WC_Helper::str_to_ascii( $this->soft_descriptor_dba_name );
			$order->payment->soft_descriptor->street                = SV_WC_Helper::str_to_ascii( $this->soft_descriptor_street );
			$order->payment->soft_descriptor->city                  = SV_WC_Helper::str_to_ascii( $this->soft_descriptor_city );
			$order->payment->soft_descriptor->region                = substr( SV_WC_Helper::str_to_ascii( $this->soft_descriptor_region ), 0, 3 );
			$order->payment->soft_descriptor->postal_code           = SV_WC_Helper::str_to_ascii( $this->soft_descriptor_postal_code );
			$order->payment->soft_descriptor->country_code          = substr( SV_WC_Helper::str_to_ascii( $this->soft_descriptor_country_code ), 0, 3 );
			$order->payment->soft_descriptor->mid                   = SV_WC_Helper::str_to_ascii( $this->soft_descriptor_mid );
			$order->payment->soft_descriptor->mcc                   = SV_WC_Helper::str_to_ascii( $this->soft_descriptor_mcc );
			$order->payment->soft_descriptor->merchant_contact_info = SV_WC_Helper::str_to_ascii( $this->soft_descriptor_merchant_contact_info );
		}

		return $order;
	}


	/**
	 * Attempts to tokenize the current payment method
	 *
	 * Note: this method performs a zero-dollar pre auth to create the token,
	 * but that token can not be safely used for a purchase/authorize transaction
	 * until a minimum of 10 seconds has elapsed, so if this is part of a
	 * tokenize/purchase transaction, the purchase will be made with the original
	 * credit card data.
	 *
	 * @since 3.1
	 * @param WC_Order $order the order object
	 * @return WC_Order the order object with a payment->token member added (which might be null)
	 * @throws Exception on network error or request error, or transaction not approved
	 */
	protected function create_payment_token( $order ) {

		// save the values for the actual transaction to perform (auth or purchase)
		$transaction_type = $order->transaction_type;
		$payment_total    = $order->payment_total;

		// prepare for pre-auth/tokenization
		$order->payment_total    = 0;
		$order->transaction_type = WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION_ONLY;

		// issue the pre-auth request to tokenize the card
		$response = $this->get_api()->create_new_transaction( $order );

		$this->log_api();

		if ( $response->transaction_approved() ) {

			// card info
			$card_last_four = substr( $order->payment->account_number, -4 );
			$card_exp_date  = substr( $response->get_expiry_date(), 0, 2 ) . '/' . substr( $response->get_expiry_date(), -2 );

			$message = sprintf( __( 'First Data Zero Dollar Pre-Authorization Approved: %s ending in %s (expires %s).', 'woocommerce-gateway-firstdata' ), $response->get_card_type(), $card_last_four, $card_exp_date );

			// now, did we actually get a token back?
			if ( $response->get_transarmor_token() ) {

				// token will be used for the actual transaction request
				$order->payment->token = $response->get_transarmor_token();
				$order->payment->type  = $response->get_card_type();

				// add the token to the customers wallet
				$card = array(
					'type'      => $response->get_card_type(),
					'last_four' => $card_last_four,
					'exp_date'  => $response->get_expiry_date(),
					'active'    => true,
				);
				$this->add_credit_card_token( get_current_user_id(), $response->get_transarmor_token(), $card );

				// attach the token to the order
				update_post_meta( $order->id, '_wc_firstdata_transarmor_token', $response->get_transarmor_token() );

			} else {
				// indicate that a token was not returned, which points to a configuration issue with the account
				$order->payment->token = null;
				$message .= __( '  Expected token not returned for transaction, verify Transarmor Token is properly configured in your First Data account.', 'woocommerce-gateway-firstdata' );
			}

			// add the transaction/card data to the order in case this is the final request of this transaction
			$this->add_transaction_data( $order, $response, $card_last_four );

			$order->add_order_note( $message );

		} else {

			// transaction declined, which means it's not worth trying to run the actual transaction (auth/purchase)
			throw new Exception( $response->get_failure_message() );
		}

		// reset the original values so the actual transaction (auth/purchase) can be processed
		$order->transaction_type = $transaction_type;
		$order->payment_total    = $payment_total;

		return $order;
	}


	/**
	 * Create a transaction
	 *
	 * @since 3.0
	 * @param WC_Order $order the order object
	 * @return bool true if transaction was successful, false otherwise
	 * @throws Exception network timeouts, etc
	 */
	protected function do_transaction( $order ) {

		// perform the transaction
		$response = $this->get_api()->create_new_transaction( $order );

		$this->log_api();

		// success! update order record
		if ( $response->transaction_approved() ) {

			// retrive the last four digits of the card used for this transaction
			$card_last_four = $this->get_account_last_four( $order );

			// expiration date to display, MM/DD
			$card_exp_date = substr( $response->get_expiry_date(), 0, 2 ) . '/' . substr( $response->get_expiry_date(), -2 );

			// tokenizing a new card
			if ( $this->get_post( 'firstdata-tokenize-card' ) && ( ! isset( $order->payment->token ) || ! $order->payment->token ) ) {

				// now, did we actually get a token back?
				if ( $response->get_transarmor_token() ) {

					// add the token to the customers wallet
					$card = array(
						'type'      => $response->get_card_type(),
						'last_four' => $card_last_four,
						'exp_date'  => $response->get_expiry_date(),
						'active'    => true,
					);

					$this->add_credit_card_token( get_current_user_id(), $response->get_transarmor_token(), $card );

					// attach the token to the order
					update_post_meta( $order->id, '_wc_firstdata_transarmor_token', $response->get_transarmor_token() );

					$message = sprintf( __( 'First Data Payment Method Saved: %s ending in %s (expires %s)', 'woocommerce-gateway-firstdata' ),
						$response->get_card_type(),
						$card_last_four,
						$response->get_expiry_date()
					);

					$order->add_order_note( $message );

				} else {
					// indicate that a token was not returned, which points to a configuration issue with the account
					if ( ! ( wc_firstdata()->is_subscriptions_active() && WC_Subscriptions_Order::order_contains_subscription( $order ) ) ) {
						// if subscriptions is active this note will be added by the subscriptions class
						$order->add_order_note( __( 'Expected token not returned for transaction, verify Transarmor Token is properly configured in your First Data account.', 'woocommerce-gateway-firstdata' ) );
					}
				}

			}

			// if an existing token was used make it the new default
			if ( $token = $this->get_post( 'firstdata-token' ) ) {
				$this->set_active_credit_card_token( get_current_user_id(), $token );
			}

			// order note based on transaction type
			$message = '';

			switch ( $response->get_transaction_type() ) {
				case self::TRANSACTION_TYPE_CHARGE:
					$message = sprintf( __( 'First Data Purchase Approved: %s ending in %s (expires %s)', 'woocommerce-gateway-firstdata' ), $response->get_card_type(), $card_last_four, $card_exp_date );
				break;
				case self::TRANSACTION_TYPE_AUTHORIZATION:
					$message = sprintf( __( 'First Data Pre-Authorization Approved: %s ending in %s (expires %s)', 'woocommerce-gateway-firstdata' ), $response->get_card_type(), $card_last_four, $card_exp_date );
				break;
				case WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION_ONLY:
					$message = sprintf( __( 'First Data Zero Dollar Pre-Authorization Approved: %s ending in %s (expires %s)', 'woocommerce-gateway-firstdata' ), $response->get_card_type(), $card_last_four, $card_exp_date );
				break;
			}

			if ( $message ) {
				$order->add_order_note( $message );
			}

			// transaction identifiers
			$this->add_transaction_data( $order, $response, $card_last_four );

			// tokenized credit card used
			if ( $token ) {
				update_post_meta( $order->id, '_wc_firstdata_transarmor_token', $token );
			}

			return true;

		} else { // failure

			$this->mark_order_as_failed( $order, $response->get_failure_message() );

			return false;
		}
	}


	/**
	 * Returns the last four digits of the account number used for the payment
	 * of $order.
	 *
	 * @since 3.1
	 * @param WC_Order $order the order object
	 * @return string the last four digits of the payment account
	 */
	private function get_account_last_four( $order ) {

		$card_last_four = '';

		// token used for checkout?
		if ( $token = $this->get_post( 'firstdata-token' ) ) {

			// if a token was used, retrieve the last four digits of the number from the local token array
			$card = $this->get_credit_card_token_details( get_current_user_id(), $token );

			if ( isset( $card['last_four'] ) && $card['last_four'] ) {
				$card_last_four = $card['last_four'];
			}

		} else {

			// regular card transaction, get the last four from the order object, the response won't include them if tokenization is enabled
			$card_last_four = substr( $order->payment->account_number, -4 );
		}

		return $card_last_four;
	}


	/**
	 * Adds the First Data transaction data to the order
	 *
	 * @since 3.1
	 * @param WC_Order $order the order object
	 * @param WC_FirstData_API_Response $response the transaction response
	 * @param string $card_last_four the last four digits of the card from the transaction
	 */
	private function add_transaction_data( $order, $response, $card_last_four ) {

		// transaction identifiers
		update_post_meta( $order->id, '_wc_firstdata_transaction_tag',   $response->get_transaction_tag() );
		update_post_meta( $order->id, '_wc_firstdata_authorization_num', $response->get_authorization_num() );
		update_post_meta( $order->id, '_wc_firstdata_sequence_no',       $response->get_sequence_no() );

		// card info
		update_post_meta( $order->id, '_wc_firstdata_card_type',        $response->get_card_type() );
		update_post_meta( $order->id, '_wc_firstdata_card_last_four',   $card_last_four );
		update_post_meta( $order->id, '_wc_firstdata_card_expiry_date', $response->get_expiry_date() );
		update_post_meta( $order->id, '_wc_firstdata_environment',      $this->get_environment() );
	}


	/**
	 * Mark the given order as failed and set the order note
	 *
	 * @since 3.0
	 * @param WC_Order $order the order
	 * @param string $error_message a message to display inside the "Credit Card Payment Failed" order note
	 * @param string $user_error_message optional message to display to the
	 *        user on the checkout page.  If not set a default message to retry
	 *        the transaction will be used.
	 */
	protected function mark_order_as_failed( $order, $error_message, $user_error_message = null ) {

		if ( is_null( $user_error_message ) ) {
			$user_error_message = __( 'An error occurred, please try again or try an alternate form of payment.', 'woocommerce-gateway-firstdata' );
		}

		$order_note = sprintf( __( 'First Data Payment Failed (%s)', 'woocommerce-gateway-firstdata' ), $error_message );

		// Mark order as failed if not already set, otherwise, make sure we add the order note so we can detect when someone fails to check out multiple times
		if ( ! $order->has_status( 'failed' ) ) {
			$order->update_status( 'failed', $order_note );
		} else {
			$order->add_order_note( $order_note );
		}

		$this->add_debug_message( $error_message, 'error' );

		SV_WC_Helper::wc_add_notice( $user_error_message, 'error' );
	}


	/**
	 * Get the available credit card tokens for a user as an associative array in format:
	 *
	 * [ transarmor token ] => Array(
	 *      type      => type of card, e.g. Amex (or Bank Account for eChecks)
	 *      last_four => last four card digits, e.g. 1234
	 *      exp_date  => expiration date in MMYY format
	 *      active    => boolean, default card if true
	 * )
	 *
	 * @since 3.0
	 * @param int $user_id user identifier
	 * @return array containing credit card tokens and details
	 */
	public function get_credit_card_tokens( $user_id ) {

		$tokens = get_user_meta( $user_id, '_wc_firstdata_credit_card_tokens', true );

		return is_array( $tokens ) ? $tokens : array();
	}


	/**
	 * Returns the active credit card token, or the first one found, or null
	 *
	 * @since 3.3.1
	 * @param int $user_id user identifier
	 * @return string active credit card token, or null
	 */
	public function get_active_credit_card_token( $user_id ) {

		$first_token = null;

		foreach ( $this->get_credit_card_tokens( $user_id ) as $token => $data ) {

			if ( null == $first_token ) {
				$first_token = $token;
			}

			if ( $data['active'] ) {
				return $token;
			}
		}

		return $first_token;
	}


	/**
	 * Get the credit card token for a user as an associative array in format:
	 *
	 * Array(
	 *      type      => type of card, e.g. Amex (or Bank Account for eChecks)
	 *      last_four => last four card digits, e.g. 1234
	 *      exp_date  => expiration date in MMYY format
	 *      active    => boolean, default card if true
	 * )
	 *
	 * @since 3.0
	 * @param int $user_id user identifier
	 * @param int $token the token
	 * @return array of credit card token details
	 */
	public function get_credit_card_token_details( $user_id, $token ) {

		$tokens = $this->get_credit_card_tokens( $user_id );

		return isset( $tokens[ $token ] ) ? $tokens[ $token ] : array();
	}


	/**
	 * Add a payment method and token as user meta.
	 *
	 * @since 3.0
	 * @param int $user_id user identifier
	 * @param int $token TransArmor credit card token from First Data
	 * @param array $payment payment info to add, this should be an associative
	 *        array with keys 'type', 'last_four', 'exp_date', 'active'
	 * @return bool|int false if token not added, user meta ID if added
	 */
	public function add_credit_card_token( $user_id, $token, $payment ) {

		// get existing tokens
		$tokens = $this->get_credit_card_tokens( $user_id );

		// if this token is set as active, mark all others as false
		if ( isset( $payment['active'] ) && $payment['active'] ) {
			foreach ( $tokens as $key => $val ) {
				$tokens[ $key ]['active'] = false;
			}
		}

		// add the new token
		$tokens[ $token ] = $payment;

		// persist the updated tokens
		return update_user_meta( $user_id, '_wc_firstdata_credit_card_tokens', $tokens );
	}


	/**
	 * Delete a credit card token from user meta
	 *
	 * @since 3.0
	 * @param int $user_id user identifier
	 * @param int $token the TransArmor credit card token to delete
	 * @return bool|int false if not deleted, updated user meta ID if deleted
	 */
	public function delete_credit_card_token( $user_id, $token ) {

		// get existing tokens
		$tokens = $this->get_credit_card_tokens( $user_id );

		// nothing to do
		if ( ! isset( $tokens[ $token ] ) )
			return false;

		$was_active = $tokens[ $token ]['active'];

		unset( $tokens[ $token ] );

		// if the deleted card was the active one, make another one
		if ( $was_active ) {
			foreach ( $tokens as $_token => $payment ) {
				$tokens[ $_token ]['active'] = true;
				break;
			}
		}

		return update_user_meta( $user_id, '_wc_firstdata_credit_card_tokens', $tokens );
	}


	/**
	 * Set the active token for a user. This is shown as "Default card" in the frontend and will be auto-selected
	 * during checkout
	 *
	 * @since 3.0
	 * @param int $user_id user identifier
	 * @param int $token the token
	 * @return string|bool false if not set, updated user meta ID if set
	 */
	public function set_active_credit_card_token( $user_id, $token ) {

		// get existing tokens
		$tokens = $this->get_credit_card_tokens( $user_id );

		// unknown token
		if ( ! isset( $tokens[ $token ] ) )
			return false;

		// mark $token as the only active
		foreach ( $tokens as $_token => $payment ) {

			if ( $token == $_token )
				$tokens[ $_token ]['active'] = true;
			else
				$tokens[ $_token ]['active'] = false;
		}

		return update_user_meta( $user_id, '_wc_firstdata_credit_card_tokens', $tokens );
	}


	/**
	 * Returns the card image URL (if any) for the given $type
	 *
	 * @since 3.0
	 * @param string $type the cc type as returned by First Data
	 * @return string the card image URL or null
	 */
	public function get_card_image( $type ) {

		switch( strtolower( $type ) ) {
			case 'american express': $image_type = 'amex';   break;
			case 'diners':           $image_type = 'diners'; break;
			case 'discover':         $image_type = 'disc';   break;
			case 'jcb':              $image_type = 'jcb';    break;
			case 'mastercard':       $image_type = 'mc';     break;
			case 'visa':             $image_type = 'visa';   break;
			default: return null;
		}

		if ( is_readable( wc_firstdata()->get_plugin_path() . '/assets/images/card-' . $image_type . '.png' ) )
			return WC_HTTPS::force_https_url( wc_firstdata()->get_plugin_url() ) . '/assets/images/card-' . $image_type . '.png';

		return null;
	}


	/**
	 * Display the 'My Payment Methods' section on the 'My Account' page and
	 * handle token actions if the gateway is avaialable and tokenization is
	 * enabled
	 *
	 * @since 3.0
	 */
	public function show_my_payment_methods() {

		if ( ! $this->is_available() || ! $this->tokenization_enabled() )
			return;

		// process payment method actions
		if ( ! empty( $_GET['wc-firstdata-token'] ) && ! empty( $_GET['wc-firstdata-action'] ) && ! empty( $_GET['_wpnonce'] ) ) {

			// security check
			if ( false === wp_verify_nonce( $_GET['_wpnonce'], 'wc-firstdata-token-action' ) )
				wp_die( __( 'There was an error with your request, please try again.', 'woocommerce-gateway-firstdata' ) );

			$token = trim( $_GET['wc-firstdata-token'] );

			if ( ! $token )
				wp_die( __( 'Token is invalid, please try again.', 'woocommerce-gateway-firstdata' ) );

			$user_id = get_current_user_id();

			// handle deletion
			if ( 'delete' === $_GET['wc-firstdata-action'] )
				$this->delete_credit_card_token( $user_id, $token );

			// handle active change
			if ( 'make-active' === $_GET['wc-firstdata-action'] )
				$this->set_active_credit_card_token( $user_id, $token );
		}

		// add delete icon
		wp_enqueue_style( 'dashicons' );
		add_action( 'wp_footer', array( $this, 'render_my_payment_methods_css' ) );

		woocommerce_firstdata_show_my_payment_methods( $this );
	}


	/**
	 * Render CSS to display the X icon for the Delete Payment Method action
	 *
	 * @since 3.6.0
	 */
	public function render_my_payment_methods_css() {

		?>
			<style type="text/css">
				.wc-firstdata-delete-payment-method {text-decoration: none;}
				.wc-firstdata-delete-payment-method:before {font-family: 'dashicons', Monospace;content:"\f158";font-size:200%;-webkit-font-smoothing:antialiased;speak:none;font-weight:400;font-variant:normal;text-transform:none;}
			</style>
		<?php
	}


	/** Helper methods ******************************************************/


	/**
	 * Perform standard luhn check.  Algorithm:
	 *
	 * 1. Double the value of every second digit beginning with the second-last right-hand digit.
	 * 2. Add the individual digits comprising the products obtained in step 1 to each of the other digits in the original number.
	 * 3. Subtract the total obtained in step 2 from the next higher number ending in 0.
	 * 4. This number should be the same as the last digit (the check digit). If the total obtained in step 2 is a number ending in zero (30, 40 etc.), the check digit is 0.
	 *
	 * @since 3.0
	 * @param string $account_number the credit card number to check
	 * @return bool true if $account_number passes the check, false otherwise
	 */
	private function luhn_check( $account_number ) {
		$sum = 0;
		for ( $i = 0, $ix = strlen( $account_number ); $i < $ix - 1; $i++) {
			$weight = substr( $account_number, $ix - ( $i + 2 ), 1 ) * ( 2 - ( $i % 2 ) );
			$sum += $weight < 10 ? $weight : $weight - 9;
		}

		return substr( $account_number, $ix - 1 ) == ( ( 10 - $sum % 10 ) % 10 );
	}


	/**
	 * Safely get and trim data from $_POST
	 *
	 * @since 3.0
	 * @param string $key array key to get from $_POST array
	 * @return string value from $_POST or blank string if $_POST[ $key ] is not set
	 */
	protected function get_post( $key ) {

		if ( isset( $_POST[ $key ] ) )
			return trim( $_POST[ $key ] );

		return '';
	}


	/**
	 * Adds debug messages to the page as a WC message/error, and/or to the WC Error log
	 *
	 * @since 3.0
	 * @param string $message message to add
	 * @param string $type how to add the message, options are:
	 *     'message' (styled as WC message), 'error' (styled as WC Error) or 'xml' (XML formatted for output as HTML)
	 * @param bool $set_message sets any WC messages/errors provided so they appear on the next page load, useful for display messages on the thank you page
	 */
	protected function add_debug_message( $message, $type = 'message', $set_message = false ) {

		// do nothing when debug mode is off or no message
		if ( 'off' == $this->debug_mode || ! $message )
			return;

		// add debug message to woocommerce->errors/messages if checkout or both is enabled
		if ( $this->debug_checkout() && ( ! is_admin() || is_ajax() ) ) {

			if ( 'message' === $type ) {

				SV_WC_Helper::wc_add_notice( $message );

			} elseif ( 'xml' === $type ) {

				$dom = new DOMDocument();
				$dom->loadXML( $message );
				$dom->formatOutput = true;
				SV_WC_Helper::wc_add_notice( "API Request/Response: <br/><pre>" . htmlspecialchars( $dom->saveXML() ) . "</pre>" );

			} else {

				// defaults to error message
				SV_WC_Helper::wc_add_notice( $message, 'error' );
			}
		}

		// add log message to WC logger if log/both is enabled
		if ( $this->debug_log() ) {
			wc_firstdata()->log( $message );
		}
	}


	/**
	 * Helper to get API response/request XML saved to messages/log
	 *
	 * @since 3.0
	 */
	protected function log_api() {

		// log request/response XML if enabled
		$this->add_debug_message( $this->get_api()->get_request_xml(), 'xml', true );
		$this->add_debug_message( $this->get_api()->get_response_xml(), 'xml', true );
	}


	/**
	 * Returns true if tokenization is forced, false otherwise
	 *
	 * @since 3.0
	 * @return boolean true if tokenization should be forced on the checkout page, false otherwise
	 */
	public function tokenization_forced() {
		return false;
	}


	/** Getter methods ******************************************************/


	/**
	 * Get the API object
	 *
	 * @since 3.0
	 * @return WC_FirstData_API
	 */
	public function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		return $this->api = new WC_FirstData_API( $this->get_api_endpoint(), $this->get_gateway_id(), $this->get_gateway_password(), $this->get_key_id(), $this->get_hmac_key() );
	}


	/**
	 * Returns the API version based on the plugin configuration
	 *
	 * @since 3.0
	 * @return int API version, one of 11 or 12
	 */
	public function get_api_version() {

		// version 12 requires additional configuration for the security parameters
		if ( $this->get_key_id() && $this->get_hmac_key() ) return 12;

		// default to 11 which does not
		return 11;
	}


	/**
	 * Returns the environment setting, one of 'production' or 'demo'
	 *
	 * @since 3.0
	 * @return string the configured environment name
	 */
	public function get_environment() {
		return $this->environment;
	}


	/**
	 * Returns the description setting
	 *
	 * @since 3.0
	 * @return string the checkout page description
	 */
	public function get_description() {
		return $this->description;
	}


	/**
	 * Returns the API URL endpoint for the configured environment and API version
	 *
	 * @since 3.0
	 * @return string the API URL endpoint
	 */
	public function get_api_endpoint() {
		return sprintf( 'production' == $this->get_environment() ? self::PRODUCTION_URL_ENDPOINT : self::DEMO_URL_ENDPOINT, $this->get_api_version() );
	}


	/**
	 * Returns the gateway id based on the current environment
	 *
	 * @since 3.0
	 * @return string the gateway id to use
	 */
	public function get_gateway_id() {
		return 'production' == $this->get_environment() ? $this->gateway_id : $this->demo_gateway_id;
	}


	/**
	 * Returns the gateway password based on the current environment
	 *
	 * @since 3.0
	 * @return string the gateway password to use
	 */
	public function get_gateway_password() {
		return 'production' == $this->get_environment() ? $this->gateway_password : $this->demo_gateway_password;
	}


	/**
	 * Returns the terminal API Access Key Id based on the current environment.
	 * This is used as part of the enhanced security for the v12 API
	 *
	 * @since 3.0
	 * @return string the terminal Key Id to use
	 */
	public function get_key_id() {
		return 'production' == $this->get_environment() ? $this->key_id : $this->demo_key_id;
	}


	/**
	 * Returns the terminal secret Hmac key based on the current environment.
	 * This is used as part of the enhanced security for the v12 API
	 *
	 * @since 3.0
	 * @return string the terminal secret Hmac key to use
	 */
	public function get_hmac_key() {
		return 'production' == $this->get_environment() ? $this->hmac_key : $this->demo_hmac_key;
	}


	/**
	 * Returns true if partial redemptions are allowed (ie $100 transaction,
	 * with only $80 available on the card)
	 *
	 * @since 3.0
	 * @return boolean true if partial redemptions are allowed, false otherwise
	 */
	public function allow_partial_redemption() {
		return 'yes' == $this->partial_redemption;
	}


	/**
	 * Returns true if tokenization is enabled
	 *
	 * @since 3.0
	 * @return boolean true if tokenization is enabled
	 */
	public function tokenization_enabled() {
		return 'yes' == $this->tokenization;
	}


	/**
	 * Returns true if soft descriptors are enabled
	 *
	 * @link https://firstdata.zendesk.com/entries/407571-First-Data-Global-Gateway-e4-Web-Service-API-Reference-Guide#4.3
	 * @since 3.1
	 * @return boolean true if soft descriptors are enabled
	 */
	public function soft_descriptors_enabled() {
		return 'yes' == $this->soft_descriptors_enabled;
	}


	/**
	 * Returns true if debug logging is enabled
	 *
	 * @since 3.0
	 * @return boolean if debug logging is enabled
	 */
	public function debug_log() {
		return 'log' === $this->debug_mode || 'both' === $this->debug_mode;
	}


	/**
	 * Returns true if checkout debugging is enabled.  This will cause debugging
	 * statements to be displayed on the checkout/pay pages
	 *
	 * @since 3.0
	 * @return boolean if checkout debugging is enabled
	 */
	public function debug_checkout() {
		return 'checkout' === $this->debug_mode || 'both' === $this->debug_mode;
	}


}
