<?php
/**
 * WooCommerce First Data Payment Gateway
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce First Data Payment Gateway to newer
 * versions in the future. If you wish to customize WooCommerce First Data Payment Gateway for your
 * needs please refer to http://docs.woothemes.com/document/firstdata/ for more information.
 *
 * @package     WC-Gateway-First-Data/Classes
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * First Data Legacy Global Gateway Base Class
 *
 * This implements the Legacy (though still used) Global Gateway class
 *
 * @since 3.0
 */
class WC_Gateway_FirstData_Global_Gateway extends WC_Payment_Gateway {


	/** The secure (live) URL endpoint */
	const SECURE_URL_ENDPOINT = 'secure.linkpt.net';

	/** The sandbox URL endpoint */
	const SANDBOX_URL_ENDPOINT = 'staging.linkpt.net';


	/** @var string the transaction environment, one of 'production' or 'sandbox', defaults to 'production' */
	private $environment;

	/** @var string The First Data store number */
	private $storenum;

	/** @var string The full path to the PEM file */
	private $pemfile;


	/**
	 * Initialize the First Data Global Gateway gateway
	 *
	 * @since 3.0
	 */
	public function __construct() {

		$this->id                 = WC_FirstData::GLOBAL_GATEWAY_ID;
		$this->method_title       = __( 'First Data Global Gateway', 'woocommerce-gateway-firstdata' );
		$this->method_description = __( 'Allow customers to checkout with their credit cards through the First Data Global Gateway.', 'woocommerce-gateway-firstdata' );

		$this->supports = array( 'products' );

		$this->has_fields = true;

		$this->icon = apply_filters( 'wc_firstdata_global_gateway_icon', '' );

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
			add_action( 'woocommerce_update_options_payment_gateways',              array( $this, 'process_admin_options' ) ); // WC < 2.0
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) ); // WC >= 2.0
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
				'title'       => __( 'Enable / Disable', 'woocommerce-gateway-firstdata' ),
				'label'       => __( 'Enable this gateway', 'woocommerce-gateway-firstdata' ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),

			'title' => array(
				'title'       => __( 'Title', 'woocommerce-gateway-firstdata' ),
				'type'        => 'text',
				'desc_tip   ' => __( 'Payment method title that the customer will see during checkout.', 'woocommerce-gateway-firstdata' ),
				'default'     => 'Credit Card',
			),

			'description' => array(
				'title'       => __( 'Description', 'woocommerce-gateway-firstdata' ),
				'type'        => 'textarea',
				'desc_tip'    => __( 'Payment method description that the customer will see during checkout.', 'woocommerce-gateway-firstdata' ),
				'default'     => __( 'Pay securely using your credit card.', 'woocommerce-gateway-firstdata' ),
			),

			'environment' => array(
				'title'         => __( 'Environment', 'woocommerce-gateway-firstdata' ),
				'type'          => 'select',
				'default'       => 'production',
				'desc_tip'      => __( "Select your processing environment, use 'sandbox' only if you have a sandbox account for testing.", 'woocommerce-gateway-firstdata' ),
				'options'       => array(
					'production' => __( 'Production', 'woocommerce-gateway-firstdata' ),
					'sandbox'    => __( 'Sandbox', 'woocommerce-gateway-firstdata' ),
				),
			),

			'storenum' => array(
				'title'    => __( 'Store Number', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'desc_tip' => __('Your First Data store number.', 'woocommerce-gateway-firstdata' ),
				'default'  => ''
			),

			'pemfile' => array(
				'title'       => __( 'PEM File', 'woocommerce-gateway-firstdata' ),
				'type'        => 'textarea',
				'description' => __( 'The full system path to your .PEM file from First Data, for security reasons you should store this outside of your web root. For your information your ABSPATH is: ', 'woocommerce-gateway-firstdata' ) . ABSPATH,
				'default'     => ABSPATH . 'FIRSTDATA.pem'
			),
		);
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
		if ( ! $this->get_storenum() || ! $this->pemfile_exists() )
			$is_available = false;

		// all dependencies met
		if ( count( wc_firstdata()->get_missing_dependencies() ) > 0 )
			$is_available = false;

		return apply_filters( 'wc_gateway_firstdata_global_gateway_is_available', $is_available );
	}


	/**
	 * Display the payment fields on the checkout page
	 *
	 * @since 3.0
	 */
	function payment_fields() {

		// safely display the description, if there is one
		if ( $this->description )
			echo '<p>' . wp_kses_post( $this->description ) . '</p>';

		?>
		<fieldset>
			<p class="form-row form-row-wide">
				<label for="firstdata-account-number"><?php esc_html_e( 'Credit Card Number', 'woocommerce-gateway-firstdata' ); ?> <span class="required">*</span></label>
				<input id="firstdata-account-number" name="firstdata-account-number" size="19" type="text" maxlength="16" class="input-text" autocomplete="off" />
			</p>
			<div class="clear"></div>

			<p class="form-row form-row-first">
				<label for="firstdata-exp-month"><?php esc_html_e( 'Expiration Date', 'woocommerce-gateway-firstdata' ); ?> <span class="required">*</span></label>
				<select id="firstdata-exp-month" name="firstdata-exp-month" style="width:auto;">
					<?php foreach ( range( 1, 12 ) as $month ) : ?>
						<option value="<?php echo sprintf( '%02d', $month ); ?>"><?php echo sprintf( '%02d', $month ); ?></option>
					<?php endforeach; ?>
				</select>
				<select id="firstdata-exp-year" name="firstdata-exp-year" style="width:auto;">
					<?php foreach ( range( date( 'Y' ), date( 'Y' ) + 10 ) as $year ) : ?>
						<option value="<?php echo $year ?>"><?php echo $year ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="form-row form-row-last">
					<label for="firstdata-cvv"><?php esc_html_e( "Card Security Code", 'woocommerce-gateway-firstdata' ) ?> <span class="required">*</span></label>
					<input type="text" class="input-text" id="firstdata-cvv" name="firstdata-cvv" maxlength="4" style="width:60px" autocomplete="off" />
			</p>
			<div class="clear"></div>
		</fieldset>
		<?php
	}


	/**
	 * Validate the payment fields when processing the checkout
	 *
	 * @since 3.0
	 * @return bool true if fields are valid, false otherwise
	 */
	public function validate_fields() {

		$is_valid = parent::validate_fields();

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

		return $is_valid;
	}


	/**
	 * Handles payment processing
	 *
	 * @since 3.0
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		$firstdata = new lphp();

		$args = array(
			'host'          => $this->get_api_endpoint(),
			'port'          => '1129',
			'keyfile'       => $this->pemfile,
			'configfile'    => $this->get_storenum(),
			'cardnumber'    => $this->get_post( 'firstdata-account-number' ),
			'cvmvalue'      => $this->get_post( 'firstdata-cvv' ),
			'cvmindicator'  => 'provided',
			'cardexpmonth'  => $this->get_post( 'firstdata-exp-month' ),
			'cardexpyear'   => substr( $this->get_post( 'firstdata-exp-year' ), -2, 2 ),
			'chargetotal'   => number_format( $order->get_total(), 2, '.', '' ),
			'shipping'      => number_format( $order->get_total_shipping(), 2, '.', '' ),
			'tax'           => number_format( $order->get_total_tax(), 2, '.', '' ),
			'subtotal'      => number_format( $order->get_total() - $order->get_total_shipping() - $order->get_total_tax(), 2, '.', '' ),
			'ordertype'     => 'SALE',
			'oid'           => 'woocommerce_' . time() . '_' . $order->id,
			'ip'            => $_SERVER['REMOTE_ADDR'],
			'userid'        => $order->get_user_id(),
			'name'          => esc_attr( $order->billing_first_name . ' ' . $order->billing_last_name ),
			'company'       => esc_attr( $order->billing_company ),
			'address1'      => esc_attr( $order->billing_address_1 ),
			'address2'      => esc_attr( $order->billing_address_2 ),
			'city'          => esc_attr( $order->billing_city ),
			'zip'           => esc_attr( $order->billing_postcode ),
			'country'       => esc_attr( $order->billing_country ),
			'state'         => esc_attr( $order->billing_state ),
			'email'         => esc_attr( $order->billing_email ),
			'phone'         => esc_attr( $order->billing_phone ),
		);

		$result = $firstdata->curl_process( $args );

		if ( 'APPROVED' == $result['r_approved'] ) {

			$order->add_order_note( __( 'First Data Purchase Approved', 'woocommerce-gateway-firstdata' ) );

			$order->payment_complete();

			WC()->cart->empty_cart();

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);

		} else {

			$reason = is_array( $result ) ? $result['r_error'] : $result;
			SV_WC_Helper::wc_add_notice( sprintf( __( 'Transaction Failed: %s', 'woocommerce-gateway-firstdata' ), $reason ), 'error' );

		}

	}


	/** Helper methods ******************************************************/


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


	/** Getter methods ******************************************************/


	/**
	 * Returns true if sandbox mode is enabled
	 *
	 * @since 3.0
	 * @return boolean true if sandbox is enabled, false otherwise
	 */
	private function is_sandbox() {
		return 'sandbox' == $this->environment;
	}


	/**
	 * Returns the configured First Data storenum
	 *
	 * @since 3.0
	 * @return string the storenum
	 */
	private function get_storenum() {
		return $this->storenum;
	}


	/**
	 * Returns true if the PEM file is configured, exists and is readable
	 *
	 * @since 3.0
	 * @return string the storenum
	 */
	private function pemfile_exists() {
		return $this->pemfile && is_readable( $this->pemfile );
	}


	/**
	 * Returns the API URL endpoint for the configured environment
	 *
	 * @since 3.0
	 * @return string the API URL endpoint
	 */
	private function get_api_endpoint() {
		return $this->is_sandbox() ? self::SANDBOX_URL_ENDPOINT : self::SECURE_URL_ENDPOINT;
	}


}
