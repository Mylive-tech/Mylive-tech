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
 * @package     WC-First-Data/API
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * WooCommerce First Data API Classes
 *
 * This file contains three classes used by the WooCommerce First Data API:
 *
 * + `WC_FirstData_API` - The main API class responsible for communication with the First Data API
 * + `WC_FirstData_API_Request` - Represents an API request
 * + `WC_FirstData_API_Response` Represents an API response
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * First Data API Class
 *
 * Handles sending/receiving/parsing of First Data XML
 *
 * @since 3.0
 */
class WC_FirstData_API {


	/** Sends through sale and request for funds to be charged to cardholder's credit card. */
	const TRANSACTION_TYPE_PURCHASE = '00';

	/** Sends through a request for funds to be "reserved" on the cardholder's credit card. A standard pre-authorization is reserved for 2-5 days. Reservation times are determined by cardholder's bank. */
	const TRANSACTION_TYPE_PRE_AUTHORIZATION = '01';

	/** Used to perform a zero dollar pre-authorization for the purposes of tokenizing a credit card */
	const TRANSACTION_TYPE_PRE_AUTHORIZATION_ONLY = '05';

	/** Used to refund a tagged transaction */
	const TRANSACTION_TYPE_TAGGED_REFUND = '34';


	/** @var string API URL endpoint */
	private $endpoint;

	/** @var string the unique account gateway id */
	private $gateway_id;

	/** @var string the private gateway password */
	private $gateway_password;

	/** @var string optional production terminal API Access Key Id used for v12 authentication */
	public $key_id;

	/** @var string optional private production terminal API Access Key used for v12 authentication */
	public $hmac_key;

	/** @var string generated request XML */
	private $request_xml;

	/** @var string retrieved response XML */
	private $response_xml;


	/**
	 * Constructor - setup request object and set endpoint
	 *
	 * @since 3.0
	 * @param string $api_endpoint API URL endpoint
	 * @param string $gateway_id unique account gateway id
	 * @param string $gateway_password private gateway password
	 * @param string $key_id optional production terminal API Access Key Id used for v12 authentication
	 * @param string $hmac_key optional private production terminal API Access Key used for v12 authentication
	 */
	public function __construct( $api_endpoint, $gateway_id, $gateway_password, $key_id, $hmac_key ) {

		$this->endpoint         = $api_endpoint;
		$this->gateway_id       = $gateway_id;
		$this->gateway_password = $gateway_password;
		$this->key_id           = $key_id;
		$this->hmac_key         = $hmac_key;

	}


	/**
	 * Create a new cc transaction using First Data XML API
	 *
	 * @since 3.0
	 * @param WC_Order $order the order
	 * @return WC_FirstData_API_Response First Data API response object
	 * @throws Exception network timeouts, etc
	 */
	public function create_new_transaction( $order ) {

		$request = new WC_FirstData_API_Request( $this->gateway_id, $this->gateway_password );

		$response = $this->perform_request( $request->get_transaction_request_xml( $order ) );

		return $this->parse_response( $response );
	}


	/**
	 * Create a new refund transaction using the First Data API
	 *
	 * @since 3.5.0
	 * @param WC_Order $order the order
	 * @return WC_FirstData_API_Response First Data API response object
	 * @throws Exception network timeouts, etc
	 */
	public function create_new_refund( $order ) {

		$request = new WC_FirstData_API_Request( $this->gateway_id, $this->gateway_password );

		$response = $this->perform_request( $request->get_refund_request_xml( $order ) );

		return $this->parse_response( $response );
	}


	/**
	 * HTTP POST request XML to active endpoint using wp_remote_post() and set the request/response XML
	 *
	 * @since 3.0
	 * @param string $xml request xml
	 * @return string response xml
	 * @throws Exception network timeouts, etc
	 */
	private function perform_request( $xml ) {

		// set the request xml
		$this->request_xml = trim( $xml );

		$content_type = 'application/xml';

		// perform the request
		//  Note: the 'redirection' => 0 is critical because the response includes a
		//  "location: https://api.demo.globalgatewaye4.firstdata.com/transaction/v11/6527498"
		//  header which causes a series of requests to be initiated that ultimately end in failure,
		//  despite the fact that the first request returns the result we want
		$wp_http_args = array(
			'timeout'     => apply_filters( 'wc_firstdata_api_timeout', 45 ), // default to 45 seconds
			'redirection' => 0,
			'httpversion' => '1.0',
			'sslverify'   => true,
			'blocking'    => true,
			'headers'     => array(
				'accept'       => $content_type,
				'content-type' => $content_type,
			),
			'body'        => $this->request_xml,
			'cookies'     => array(),
			'user-agent'  => "PHP " . PHP_VERSION,
		);

		// v12 authentication: https://firstdata.zendesk.com/entries/22069302-api-security-hmac-hash
		// There is a helpful test terminal for performing this calculation found by logging into the First Data account and going to Administration > Terminals > {Terminal} > API Access
		if ( $this->key_id && $this->hmac_key ) {

			$method         = 'POST';
			$content_digest = sha1( $this->request_xml );
			$gge4_date      = gmdate( 'Y-m-d\TH:i:s\Z' );
			$request_url    = parse_url( $this->endpoint, PHP_URL_PATH );

			$hmac_hash = base64_encode( hash_hmac( 'sha1', $method . "\n" . $content_type . "\n" . $content_digest . "\n" . $gge4_date . "\n" . $request_url, $this->hmac_key, true ) );

			$wp_http_args['headers']['authorization']       = 'GGE4_API ' . $this->key_id . ':' . $hmac_hash;
			$wp_http_args['headers']['x-gge4-date']         = $gge4_date;
			$wp_http_args['headers']['x-gge4-content-sha1'] = $content_digest;
		}

		$response = wp_safe_remote_post( $this->endpoint, $wp_http_args );

		// Check for Network timeout, etc.
		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		// invalid API requests (such as a missing required element) will return a non-201 code with an error message set as the response body
		if ( 201 != $response['response']['code'] ) {
			// response will include the http status code/message
			$message = sprintf( "HTTP %s: %s", $response['response']['code'], $response['response']['message'] );

			// the body (if any)
			if ( trim( $response['body'] ) ) {
				$message .= ' - ' . $response['body'];
			}

			// and the x-api-deprecated header (if any)
			if ( isset( $response['headers']['x-api-deprecated'] ) && $response['headers']['x-api-deprecated'] ) {
				$message .= ' - x-api-deprecated: ' . $response['headers']['x-api-deprecated'];
			}

			throw new Exception( $message );
		}

		// return blank XML document if response body doesn't exist
		$response = ( isset( $response[ 'body' ] ) ) ? $response[ 'body' ] : '<?xml version="1.0" encoding="utf-8"?>';

		$this->response_xml = $response;

		return $response;
	}


	/**
	 * Return a new WC_FirstData_API_Response object from the response XML
	 *
	 * @since 3.0
	 * @param string $response xml response
	 * @return WC_FirstData_API_Response API response object
	 * @throws Exception any API error
	 */
	private function parse_response( $response ) {

		// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
		$response = new WC_FirstData_API_Response( $response, LIBXML_NOCDATA );

		// Throw exception for true API errors
		if ( $response->has_transaction_error() ) {
			throw new Exception( sprintf( __( 'Error: [%s] - %s', 'woocommerce-gateway-firstdata' ), $response->get_error_number(), $response->get_error_description() ) );
		}

		return $response;
	}


	/**
	 * Get the request XML stripped of any confidential information (merchant auth, card number, CVV code)
	 *
	 * @since 3.0
	 * @return string cleaned request XML
	 */
	public function get_request_xml() {

		$request_xml = $this->request_xml;

		// replace merchant authentication
		if ( preg_match( '/<Password>(.*)<\/Password>/', $request_xml, $matches ) ) {
			$request_xml = preg_replace( '/<Password>.*<\/Password>/', '<Password>' . str_repeat( '*', strlen( $matches[1] ) ) . '</Password>', $request_xml );
		}

		// replace real card number
		if ( preg_match( '/<Card_Number>([0-9]*)<\/Card_Number>/', $this->request_xml, $matches ) ) {
			$request_xml = preg_replace( '/<Card_Number>[0-9]*<\/Card_Number>/', '<Card_Number>' . str_repeat( '*', strlen( $matches[1] ) - 4 ) . substr( $matches[1], -4 ) . '</Card_Number>', $request_xml );
		}

		// replace real CVV code
		if ( preg_match( '/<VerificationStr2>([0-9]*)<\/VerificationStr2>/', $request_xml, $matches ) ) {
			$request_xml = preg_replace( '/<VerificationStr2>[0-9]*<\/VerificationStr2>/', '<VerificationStr2>' . str_repeat( '*', strlen( $matches[1] ) ) . '</VerificationStr2>', $request_xml );
		}

		return $request_xml;
	}


	/**
	 * Get the response XML
	 *
	 * @since 3.0
	 * @return string raw response XML
	 */
	public function get_response_xml() {

		return $this->response_xml;
	}


}// end WC_FirstData_API class


/**
 * First Data API Request Class
 *
 * Generates XML required by API specs
 *
 * @link https://firstdata.zendesk.com/entries/407571-First-Data-Global-Gateway-e4-Web-Service-API-Reference-Guide#4
 *
 * @since 3.0
 */
class WC_FirstData_API_Request extends XMLWriter {


	/** CVV2 value provided by the cardholder */
	const CVD_PROVIDED_BY_CARDHOLDER = '1';

	/**
	 * designates a transaction between a cardholder and a merchant
	 * consummated via the Internet where the transaction includes the use of
	 * transaction encryption such as SSL, but authentication was not
	 * performed. The cardholder payment data was protected with a form of
	 * Internet security, such as SSL, but authentication was not performed.
	 */
	const ECI_CHANNEL_ENCRYPTED_TRANSACTION = 7;

	/**
	 * designates a transaction between a cardholder and a merchant consummated
	 * via the Internet where the transaction does not include the use of any
	 * transaction encryption such as SSL, no authentication performed, no
	 * management of a cardholder certificate.
	 */
	const ECI_NON_SECURE_ELECTRONIC_COMMERCE_TRANSACTION = 8;


	/** @var string the unique account gateway id */
	private $gateway_id;

	/** @var string the private gateway password */
	private $gateway_password;


	/**
	 * Open XML document in memory, set auth information
	 *
	 * @since 3.0
	 * @param string $gateway_id the unique account gateway id
	 * @param string $gateway_password the private gateway password
	 */
	public function __construct( $gateway_id, $gateway_password ) {

		// Create XML document in memory
		$this->openMemory();

		// Set XML version & encoding
		$this->startDocument( '1.0', 'UTF-8' );

		$this->gateway_id       = $gateway_id;
		$this->gateway_password = $gateway_password;
	}


	/**
	 * Create XML for creating a transaction
	 *
	 * @link https://firstdata.zendesk.com/entries/407571-First-Data-Global-Gateway-e4-Web-Service-API-Reference-Guide#4
	 * @since 3.0
	 * @param WC_Order $order the order object
	 * @return string the XML request
	 */
	public function get_transaction_request_xml( $order ) {

		// root element <Transaction>
		$this->startElement( 'Transaction' );

		$this->add_auth_elements();

		$this->writeElement( 'Transaction_Type', $this->get_transaction_type( $order ) );
		$this->writeElement( 'DollarAmount',     $order->payment_total );

		if ( isset( $order->payment->token ) ) {
			$this->writeElement( 'TransarmorToken', $order->payment->token );
			$this->writeElement( 'CardType',        $order->payment->type );
		} elseif ( isset( $order->payment->account_number ) ) {
			$this->writeElement( 'Card_Number',     $order->payment->account_number );
		}
		$this->writeElement( 'Expiry_Date',      $order->payment->exp_date );
		$this->writeElement( 'CardHoldersName',  $order->billing_first_name . ' ' . $order->billing_last_name );

		$this->writeElement( 'VerificationStr1', $this->get_avs_str( $order ) );
		if ( isset( $order->payment->cvv ) ) {
			$this->writeElement( 'VerificationStr2', $order->payment->cvv );
			$this->writeElement( 'CVD_Presence_Ind', self::CVD_PROVIDED_BY_CARDHOLDER );
		}

		// strip leading hash mark from order number.  Also remove all invalid characters (some of these are actually valid for international or non-international orders, but seems easiest to just remove them all)
		$this->writeElement( 'Reference_No', ltrim( str_replace( array( '|', '^', '%', '\\', '/', '[', ']', '~', '`' ), '', $order->get_order_number() ), _x( '#', 'hash before order number', 'woocommerce-gateway-firstdata' ) ) );
		$this->writeElement( 'ZipCode',      $order->billing_postcode );
		$this->writeElement( 'Tax1Amount',   number_format( $order->get_total_tax(), 2 ) );  // TODO: will this cause any issues with subscriptions?

		// customer identifier for non-guest transactions
		if ( $order->get_user_id() ) {
			$this->writeElement( 'Customer_Ref', $order->get_user_id() );
		}

		$this->writeElement( 'Reference_3',       $order->id );                         // order reference number
		$this->writeElement( 'Client_IP',         $_SERVER['REMOTE_ADDR'] );            // fraud investigation
		$this->writeElement( 'Client_Email',      $order->billing_email );              // fraud investigation
		$this->writeElement( 'Currency',          $order->get_order_currency() );
		$this->writeElement( 'PartialRedemption', $order->partial_redemption );
		$this->writeElement( 'Ecommerce_Flag',    $this->get_ecommerce_flag() );

		// soft descriptors enabled and valid transaction type?
		if ( in_array( $order->transaction_type, array( WC_Gateway_FirstData::TRANSACTION_TYPE_CHARGE, WC_Gateway_FirstData::TRANSACTION_TYPE_AUTHORIZATION ) ) && isset( $order->payment->soft_descriptor ) ) {
			$this->add_soft_descriptor_elements( $order );
		}

		// </Transaction>
		$this->endElement();

		return $this->get_xml();
	}


	/**
	 * Create XML for creating a refund transaction
	 *
	 * @link https://firstdata.zendesk.com/entries/407571-First-Data-Global-Gateway-e4-Web-Service-API-Reference-Guide#4
	 * @since 3.5.0
	 * @param WC_Order $order the order object
	 * @return string the XML request
	 */
	public function get_refund_request_xml( $order ) {

		// root element <Transaction>
		$this->startElement( 'Transaction' );

		$this->add_auth_elements();

		$this->writeElement( 'Transaction_Type', WC_FirstData_API::TRANSACTION_TYPE_TAGGED_REFUND );

		// required elements for tagged refund
		$this->writeElement( 'Transaction_Tag',   $order->refund->transaction_tag );
		$this->writeElement( 'DollarAmount',      $order->refund->amount );
		$this->writeElement( 'Authorization_Num', $order->refund->authorization_num );

		// standard elements
		$this->writeElement( 'Reference_No',   SV_WC_Helper::str_to_ascii( ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce-gateway-firstdata' ) ) ) );
		$this->writeElement( 'Reference_3',    $order->id );                         // order reference number
		$this->writeElement( 'Client_IP',      $_SERVER['REMOTE_ADDR'] );            // fraud investigation
		$this->writeElement( 'Client_Email',   $order->billing_email );              // fraud investigation
		$this->writeElement( 'Currency',       $order->get_order_currency() );
		$this->writeElement( 'Ecommerce_Flag', $this->get_ecommerce_flag() );

		// </Transaction>
		$this->endElement();

		return $this->get_xml();
	}


	/**
	 * Generates authorization elements that are included with every request
	 *
	 * @since 3.0
	 */
	private function add_auth_elements() {

		// <ExactID>{gateway_id}</ExactID>
		$this->writeElement( 'ExactID', $this->gateway_id );

		// <Password>{gateway_password}</Password>
		$this->writeElement( 'Password', $this->gateway_password );
	}


	/**
	 * Generates optional soft descriptor elements
	 *
	 * @since 3.1
	 * @param WC_Order $order the order object
	 */
	private function add_soft_descriptor_elements( $order ) {

		// <SoftDescriptor>
		$this->startElement( 'SoftDescriptor' );

		if ( $order->payment->soft_descriptor->dba_name )
			$this->writeElement( 'DBAName', $order->payment->soft_descriptor->dba_name );

		if ( $order->payment->soft_descriptor->street )
			$this->writeElement( 'Street', $order->payment->soft_descriptor->street );

		if ( $order->payment->soft_descriptor->city )
			$this->writeElement( 'City', $order->payment->soft_descriptor->city );

		if ( $order->payment->soft_descriptor->region )
			$this->writeElement( 'Region', $order->payment->soft_descriptor->region );

		if ( $order->payment->soft_descriptor->postal_code )
			$this->writeElement( 'PostalCode', $order->payment->soft_descriptor->postal_code );

		if ( $order->payment->soft_descriptor->country_code )
			$this->writeElement( 'CountryCode', $order->payment->soft_descriptor->country_code );

		if ( $order->payment->soft_descriptor->mid )
			$this->writeElement( 'MID', $order->payment->soft_descriptor->mid );

		if ( $order->payment->soft_descriptor->mcc )
			$this->writeElement( 'MCC', $order->payment->soft_descriptor->mcc );

		if ( $order->payment->soft_descriptor->merchant_contact_info )
			$this->writeElement( 'MerchantContactInfo', $order->payment->soft_descriptor->merchant_contact_info );

		// </SoftDescriptor>
		$this->endElement();

	}


	/**
	 * Returns the AVS verification string for the billing address for the given order
	 *
	 * @since 3.0
	 * @param WC_Order $order the order
	 * @return string the AVS verification string (address pieces separated by '|')
	 */
	protected function get_avs_str( $order ) {

		$pieces = array(
			empty( $order->billing_address_2 ) ? $order->billing_address_1 : $order->billing_address_1 . ' ' . $order->billing_address_2,
			$order->billing_postcode,
			$order->billing_city,
			$order->billing_state,
			$order->billing_country,
		);

		return implode( '|', $pieces );
	}


	/**
	 * Given an order, return the transaction type value expected by the First
	 * Data API.  'charge' => '00', 'authorization' => '01' and zero-dollar
	 * pre-auth is '05' and requires no translation
	 *
	 * @since 3.1.1
	 * @param WC_Order $order the order object
	 * @return string First Data transaction type value
	 */
	protected function get_transaction_type( $order ) {

		switch ( $order->transaction_type ) {

			case WC_Gateway_FirstData::TRANSACTION_TYPE_CHARGE:        return WC_FirstData_API::TRANSACTION_TYPE_PURCHASE;
			case WC_Gateway_FirstData::TRANSACTION_TYPE_AUTHORIZATION: return WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION;
			default: return WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION_ONLY;

		}

		return null;
	}


	/**
	 * Returns the appropriate ecommerce flag for the transaction request
	 *
	 * @link https://firstdata.zendesk.com/entries/21531261-ecommerce-flag-values
	 * @since 3.0
	 * @return int the ecommerce flag
	 */
	protected function get_ecommerce_flag() {
		return is_ssl() ? self::ECI_CHANNEL_ENCRYPTED_TRANSACTION : self::ECI_NON_SECURE_ELECTRONIC_COMMERCE_TRANSACTION;
	}

	/**
	 * Helper to return completed XML document
	 *
	 * @since 3.0
	 * @return string XML
	 */
	private function get_xml() {

		$this->endDocument();

		return $this->outputMemory();
	}


} // end WC_FirstData_API_Request class


/**
 * First Data API Response Class
 *
 * Parses XML received by First Data API
 *
 * The (string) casts here are critical, without these you'll tend to get untraceable
 * errors like "Serialization of 'WC_FirstData_API_Response' is not allowed"
 *
 * @link https://firstdata.zendesk.com/entries/407571-First-Data-Global-Gateway-e4-Web-Service-API-Reference-Guide#5
 *
 * @since 3.0
 */
class WC_FirstData_API_Response extends SimpleXMLElement {

	/* Cannot override __construct when extending SimpleXMLElement */


	/**
	 * Checks if the transaction was successful
	 *
	 * @since 3.0
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		return 'true' == $this->Transaction_Approved;
	}


	/**
	 * Checks if the transaction failed
	 *
	 * @since 3.0
	 * @return bool true if there was an error with the transaction
	 */
	public function has_transaction_error() {

		// neither an error nor approved flag, invalid response
		if ( ! isset( $this->Transaction_Error ) && ! isset( $this->Transaction_Approved ) )
			return true;

		// true if there's an error
		return $this->Transaction_Error && 'false' != $this->Transaction_Error;
	}


	/**
	 * Gets the exact response code
	 *
	 * @since 3.0
	 * @return string response code
	 */
	public function get_exact_response_code() {

		if ( ! isset( $this->EXact_Resp_Code ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->EXact_Resp_Code;
	}


	/**
	 * Gets the exact message
	 *
	 * @since 3.0
	 * @return string response message
	 */
	public function get_exact_message() {

		if ( ! isset( $this->EXact_Message ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->EXact_Message;
	}


	/**
	 * Gets the transaction error number
	 *
	 * @since 3.0
	 * @return string error number
	 */
	public function get_error_number() {

		if ( ! isset( $this->Error_Number ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->Error_Number;
	}


	/**
	 * Gets the transaction error description
	 *
	 * @since 3.0
	 * @return string error description
	 */
	public function get_error_description() {

		if ( ! isset( $this->Error_Description ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->Error_Description;
	}


	/**
	 * Gets the transaction dollar amount
	 *
	 * @since 3.0
	 * @return float transaction dollar amount
	 */
	public function get_dollar_amount() {

		if ( ! isset( $this->DollarAmount ) )
			return null;

		return (float) $this->DollarAmount;
	}


	/**
	 * Gets the transaction card type
	 *
	 * @since 3.0
	 * @return string card type
	 */
	public function get_card_type() {

		if ( ! isset( $this->CardType ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->CardType;
	}


	/**
	 * Gets the transarmor token, if tokenization is enabled
	 *
	 * @since 3.0
	 * @return string token if tokenization is enabled, or null otherwise
	 */
	public function get_transarmor_token() {

		if ( ! isset( $this->TransarmorToken ) )
			return null;

		return (string) $this->TransarmorToken;
	}


	/**
	 * Gets the transaction tag.  Tagged transactions enable a transaction to be processed without having to send a credit card number
	 *
	 * @since 3.0
	 * @return string transaction tag
	 */
	public function get_transaction_tag() {

		if ( ! isset( $this->Transaction_Tag ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->Transaction_Tag;
	}


	/**
	 * Gets the authorization number.  This is the authorization number returned by the cardholder's financial institution
	 * when a transaction has been approved. This value needs to be sent when sending various transaction types such as
	 * preauthorization completion, void, or tagged transaction
	 *
	 * @since 3.0
	 * @return string authorization number
	 */
	public function get_authorization_num() {

		if ( ! isset( $this->Authorization_Num ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->Authorization_Num;
	}


	/**
	 * Gets the transaction type
	 *
	 * @since 3.0
	 * @return string transaction type
	 */
	public function get_transaction_type() {

		if ( ! isset( $this->Transaction_Type ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		$transaction_type = (string) $this->Transaction_Type;

		// '00' => 'charge', '01' => 'authorization', '05' (zero dollar pre-auth) => '05
		switch ( $transaction_type ) {

			case WC_FirstData_API::TRANSACTION_TYPE_PURCHASE:                return WC_Gateway_FirstData::TRANSACTION_TYPE_CHARGE;
			case WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION:       return WC_Gateway_FirstData::TRANSACTION_TYPE_AUTHORIZATION;
			case WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION_ONLY : return WC_FirstData_API::TRANSACTION_TYPE_PRE_AUTHORIZATION_ONLY;

			default: return __( 'N/A', 'woocommerce-gateway-firstdata' );
		}
	}


	/**
	 * Gets the expiry date
	 *
	 * @since 3.0
	 * @return string expiry date in the form of MMYY
	 */
	public function get_expiry_date() {

		if ( ! isset( $this->Transaction_Type ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->Expiry_Date;
	}


	/**
	 * Gets the sequence number.  A digit sequentially incremented number
	 * generated by Global Gateway e4 and passed through to the financial
	 * institution. It is also passed back to the client in the transaction
	 * response. This number can be used for tracking and audit purposes.
	 *
	 * @since 3.0
	 * @return string transaction type
	 */
	public function get_sequence_no() {

		if ( ! isset( $this->SequenceNo ) )
			return __( 'N/A', 'woocommerce-gateway-firstdata' );

		return (string) $this->SequenceNo;
	}


	/**
	 * Gets the transaction failure message
	 *
	 * @link https://firstdata.zendesk.com/entries/407657-how-to-generate-unsuccessful-transactions-during-testing
	 * @since 3.0
	 * @return string
	 */
	public function get_failure_message() {

		if ( isset( $this->Bank_Resp_Code ) && (string) $this->Bank_Resp_Code ) {
			// usually null
			$resp_code_2 = ( (string) $this->Bank_Resp_Code_2 ) ? ' ' . sprintf( __( '[Code2 %s]', 'woocommerce-gateway-firstdata' ), (string) $this->Bank_Resp_Code_2 ) : '';

			return sprintf( __( 'Bank Response [Code %s]%s Message: %s', 'woocommerce-gateway-firstdata' ), (string) $this->Bank_Resp_Code, $resp_code_2, (string) $this->Bank_Message );

		} elseif ( isset( $this->Error_Number ) && (string) $this->Error_Number ) {
			return sprintf( __( 'Error Response [Code %s] Description: %s', 'woocommerce-gateway-firstdata' ), (string) $this->Error_Number, (string) $this->Error_Description );
		}

		return __( 'Unknown error', 'woocommerce-gateway-firstdata' );
	}


}
