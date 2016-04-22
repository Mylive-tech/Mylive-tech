<?php
/**
 * Plugin Name: WooCommerce First Data Payeezy Gateway
 * Plugin URI: http://www.woothemes.com/products/firstdata/
 * Description: Accept credit cards in WooCommerce through the First Data Payeezy (formerly GGe4) or Global Gateway
 * Author: WooThemes / SkyVerge
 * Author URI: http://www.woothemes.com/
 * Version: 3.8.1
 * Text Domain: woocommerce-gateway-firstdata
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2013-2016 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-First-Data
 * @author    SkyVerge
 * @category  Payment-Gateways
 * @copyright Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'eb3e32663ec0810592eaf0d097796230', '18645' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.2.0', __( 'WooCommerce First Data Gateway', 'woocommerce-gateway-firstdata' ), __FILE__, 'init_woocommerce_gateway_first_data', array( 'minimum_wc_version' => '2.3.6', 'backwards_compatible' => '4.2.0' ) );

function init_woocommerce_gateway_first_data() {

/**
 * The main class for the First Data Gateway.  This class handles all the
 * non-gateway tasks such as verifying dependencies are met, loading the text
 * domain, etc.  It also loads the First Data Gateway when needed now that the
 * gateway is only created on the checkout & settings pages / api hook.  The gateway is
 * also loaded in the following instances:
 *
 * + On the My Account page to display / change saved payment methods
 *
 */
class WC_FirstData extends SV_WC_Plugin {


	/** version number */
	const VERSION = '3.8.1';

	/** @var WC_FirstData single instance of this plugin */
	protected static $instance;

	/** the plugin identifier */
	const PLUGIN_ID = 'firstdata';

	/** plugin text domain, DEPRECATED as of 3.8.0 */
	const TEXT_DOMAIN = 'woocommerce-gateway-firstdata';

	/** the global gateway class name */
	const GLOBAL_GATEWAY_CLASS_NAME = 'WC_Gateway_FirstData_Global_Gateway';

	/** the Payeezy (formerly GGe4) gateway class name */
	const GGE4_GATEWAY_CLASS_NAME = 'WC_Gateway_FirstData';

	/** the Payeezy (formerly GGe4) addons gateway class name */
	const GGE4_ADDONS_GATEWAY_CLASS_NAME = 'WC_Gateway_FirstData_Addons';

	/** the legacy global gateway id */
	const GLOBAL_GATEWAY_ID = 'firstdata-global-gateway';

	/** the Payeezy (formerly GGe4) gateway id */
	const GGE4_GATEWAY_ID = 'firstdata';


	/** @var string class to load as gateway, can be base or add-ons class */
	public $gateway_class_name;


	/**
	 * Setup main plugin class
	 *
	 * @since 3.0
	 * @see SV_WC_Plugin::__construct()
	 */
	public function __construct() {

		parent::__construct( self::PLUGIN_ID, self::VERSION );

		// Load First Data API / Gateway
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'init' ) );

		add_action( 'init', array( $this, 'include_template_functions' ), 25 );

		// Admin
		if ( is_admin() && ! is_ajax() ) {

			// show any tokenized payment methods
			add_action( 'show_user_profile', array( $this, 'add_customer_data' ) );
			add_action( 'edit_user_profile', array( $this, 'add_customer_data' ) );

			// update tokenized payment methods
			add_action( 'personal_options_update',  array( $this, 'save_customer_data' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_customer_data' ) );

			// handle switching between Payeezy (formerly GGe4) and Global Gateway versions
			add_action( 'admin_action_wc_firstdata_change_gateway', array( $this, 'change_gateway' ) );

		}
	}


	/**
	 * Initialize the gateway once all plugins are loaded:
	 * Loads API and Gateway classes
	 *
	 * @since 3.0
	 */
	public function init() {

		$plugin_path = $this->get_plugin_path();

		// Base gateway class
		if ( $this->using_gge4_gateway() ) {

			// FirstData API
			require_once( $plugin_path . '/includes/class-wc-firstdata-api.php' );
			require_once( $plugin_path . '/includes/class-wc-gateway-firstdata.php' );

			// load add-ons class if subscriptions and/or pre-orders are active and using the gge4 gateway
			if ( $this->is_subscriptions_active() || $this->is_pre_orders_active() ) {

				require( $plugin_path . '/includes/class-wc-gateway-firstdata-addons.php' );
			}

			// Add the 'Manage My Payment Methods' on the 'My Account' page for the GGe4 gateway
			add_action( 'woocommerce_after_my_account', array( $this, 'add_my_payment_methods' ) );

		} else {

			// legacy global gateway class and library
			require_once( $plugin_path . '/includes/class-wc-gateway-firstdata-global-gateway.php' );
			require_once( $plugin_path . '/lib/lphp.php' );
		}

		// Add classes to WC Payment Methods
		add_filter( 'woocommerce_payment_gateways', array( $this, 'load_gateway' ) );

	}


	/**
	 * Function used to init WooCommerce First Data template functions,
	 * making them pluggable by plugins and themes.
	 *
	 * @since 3.0
	 */
	public function include_template_functions() {
		include_once( $this->get_plugin_path() . '/includes/wc-gateway-firstdata-template.php' );
	}


	/**
	 * Adds First Data to the list of available payment gateways
	 *
	 * @since 3.0
	 * @param array $gateways
	 * @return array $gateways
	 */
	public function load_gateway( $gateways ) {

		$gateways[] = $this->get_gateway_class_name();

		return $gateways;
	}


	/**
	 * Load plugin text domain
	 *
	 * @see SV_WC_Plugin::load_translation()
	 * @since 3.0
	 */
	public function load_translation() {
		load_plugin_textdomain( 'woocommerce-gateway-firstdata', false, dirname( plugin_basename( $this->get_file() ) ) . '/i18n/languages' );
	}


	/** Admin methods ******************************************************/


	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 3.2
	 * @param array $actions associative array of action names to anchor tags
	 * @return array associative array of plugin action links
	 */
	public function plugin_action_links( $actions ) {

		global $status, $page, $s;

		// get the standard action links
		$actions = parent::plugin_action_links( $actions );

		// add an action to switch between the legacy Global Gateway and Payeezy (formerly GGe4) versions
		if ( $this->using_gge4_gateway() ) {
			$actions['use_global_gateway'] = sprintf(
				'<a href="%s" title="%s">%s</a>',
					esc_url( wp_nonce_url(
						add_query_arg(
							array(
								'action'        => 'wc_firstdata_change_gateway',
								'gateway'       => self::GLOBAL_GATEWAY_CLASS_NAME,
								'plugin_status' => $status,
								'paged'         => $page,
								's'             => $s ),
							'admin.php' ),
						'wc-firstdata-change-gateway_' . $this->get_file() ) ),
					esc_attr__( 'Use the Global Gateway', 'woocommerce-gateway-firstdata' ),
					__( 'Use Global Gateway', 'woocommerce-gateway-firstdata' )
				);
		} else {
			$actions['use_gge4_gateway'] = sprintf(
				'<a href="%s" title="%s">%s</a>',
					esc_url( wp_nonce_url(
						add_query_arg(
							array(
								'action'        => 'wc_firstdata_change_gateway',
								'gateway'       => self::GGE4_GATEWAY_CLASS_NAME,
								'plugin_status' => $status,
								'paged'         => $page,
								's'             => $s ),
							'admin.php' ),
						'wc-firstdata-change-gateway_' . $this->get_file() ) ),
					esc_attr__( 'Use the Payeezy gateway', 'woocommerce-gateway-firstdata' ),
					__( 'Use Payeezy', 'woocommerce-gateway-firstdata' )
			);
		}

		return $actions;
	}


	/**
	 * Gets the gateway configuration URL
	 *
	 * @since 3.0
	 * @see SV_WC_Plugin::get_settings_url()
	 * @param string $plugin_id the plugin identifier.  Note that this can be a
	 *        sub-identifier for plugins with multiple parallel settings pages
	 *        (ie a gateway that supports both credit cards and echecks)
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {
		return $this->get_payment_gateway_configuration_url( $this->get_gateway_class_name() );
	}


	/**
	 * Returns true if on the gateway settings page
	 *
	 * @since 3.2
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the admin gateway settings page
	 */
	public function is_plugin_settings() {
		return $this->is_payment_gateway_configuration_page( $this->get_gateway_class_name() );
	}


	/**
	 * Returns the admin configuration url for the gateway with class name
	 * $gateway_class_name
	 *
	 * @since 2.2.0-1
	 * @param string $gateway_class_name the gateway class name
	 * @return string admin configuration url for the gateway
	 */
	public function get_payment_gateway_configuration_url( $gateway_class_name ) {

		return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . strtolower( $gateway_class_name ) );
	}


	/**
	 * Returns true if the current page is the admin configuration page for the
	 * gateway with class name $gateway_class_name
	 *
	 * @since 2.2.0-1
	 * @param string $gateway_class_name the gateway class name
	 * @return boolean true if the current page is the admin configuration page for the gateway
	 */
	public function is_payment_gateway_configuration_page( $gateway_class_name ) {

		return isset( $_GET['page'] ) && 'wc-settings' == $_GET['page'] &&
		isset( $_GET['tab'] ) && 'checkout' == $_GET['tab'] &&
		isset( $_GET['section'] ) && strtolower( $gateway_class_name ) == $_GET['section'];
	}


	/**
	 * Checks if required PHP extensions are loaded and SSL is enabled. Adds an admin notice if either check fails.
	 * Also gateway settings are checked as well.
	 *
	 * @since  3.4.2
	 * @see SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		// show a notice when switching between the Global and GGe4 gateways
		$this->add_gateway_switch_admin_notice();

		// show a notice for any settings/configuration issues
		$this->add_settings_admin_notices();
	}


	/**
	 * Render a notice when switching between the Global and GGe4 gateway modes
	 *
	 * @since 3.4.2
	 */
	private function add_gateway_switch_admin_notice() {

		// Display a message on the Plugins list table if we've switched between Payeezy (formerly GGe4) or Global Gateway
		if ( isset( $_GET['wc_firstdata_gateway'] ) && $_GET['wc_firstdata_gateway'] ) {
			if ( self::GGE4_GATEWAY_CLASS_NAME == $_GET['wc_firstdata_gateway'] ) {
				$message = __( "First Data Payeezy Gateway is now being used.", 'woocommerce-gateway-firstdata' );
			} elseif ( self::GLOBAL_GATEWAY_CLASS_NAME == $_GET['wc_firstdata_gateway'] ) {
				$message = __( "First Data Global Gateway is now being used.", 'woocommerce-gateway-firstdata' );
			}
			$this->get_admin_notice_handler()->add_admin_notice( $message, 'first-data-gg-gge4', array( 'dismissible' => false ) );
		}
	}


	/**
	 * Render the SSL Required and Key ID/Hmac Key recommended notices, as needed
	 *
	 * @since 3.4.2
	 */
	private function add_settings_admin_notices() {

		// check settings:  gateway active and SSl enabled, and GGe4 v12 API enabled
		$settings = get_option( 'woocommerce_' . $this->get_gateway_id() . '_settings' );

		if ( isset( $settings['enabled'] ) && 'yes' == $settings['enabled'] ) {

			if ( isset( $settings['environment'] ) && 'production' == $settings['environment'] ) {
				// SSL check if gateway enabled/production mode
				if ( 'no' === get_option( 'woocommerce_force_ssl_checkout' ) ) {
					$message = sprintf(
						__( "%sFirst Data Error%s: WooCommerce is not being forced over SSL; your customer's credit card data is at risk.", 'woocommerce-gateway-firstdata' ),
						'<strong>', '</strong>'
					);
					$this->get_admin_notice_handler()->add_admin_notice( $message, 'ssl-required' );
				}
			}

			// current environment
			$environment_prefix = isset( $settings['environment'] ) && 'demo' == $settings['environment'] ? 'demo_' : '';

			// if the GGe4 gateway is enabled and gateway id/password is configured (v11 API) but not
			//  the key id and hmac key (v12 API) display a notice recommending they take action to do so.  Note
			//  that we're pulling the raw settings array here, which kind of sucks but is more convenient than
			//  loading the gateway on every admin page request
			if ( isset( $settings[ $environment_prefix . 'gateway_id' ] ) && $settings[ $environment_prefix . 'gateway_id' ] &&
				isset( $settings[ $environment_prefix . 'gateway_password' ] ) && $settings[ $environment_prefix . 'gateway_password' ] &&
				( ! isset( $settings[ $environment_prefix . 'key_id' ] ) || ! $settings[ $environment_prefix . 'key_id' ] ||
				! isset( $settings[ $environment_prefix . 'hmac_key' ] ) || ! $settings[ $environment_prefix . 'hmac_key' ] )  ) {

				$message = sprintf(
					__( "%sFirst Data Payeezy Gateway:%s It's recommended that you %sconfigure%s the new Key ID and Hmac Key settings for increased transaction security.  See the %sdocumentation%s for further details.", 'woocommerce-gateway-firstdata' ),
					'<strong>', '</strong>',
					'<a href="' . $this->get_settings_url() . '">', '</a>',
					'<a href="http://docs.woothemes.com/document/firstdata#api-security">', '</a>'
				);
				$this->get_admin_notice_handler()->add_admin_notice( $message, 'key-notice' );
			}
		}
	}


	/**
	 * Implements the Admin Plugins page change gateway action
	 *
	 * @since 3.0
	 */
	public function change_gateway() {

		// Plugins page arguments
		$plugin_status = isset( $_GET['plugin_status'] ) ? $_GET['plugin_status'] : '';
		$page          = isset( $_GET['paged'] ) ? $_GET['paged'] : '';
		$s             = isset( $_GET['s'] ) ? $_GET['s'] : '';

		// the gateway version to use
		$gateway = isset( $_GET['gateway'] ) ? $_GET['gateway'] : '';

		// get the base return url
		$return_url = admin_url( 'plugins.php?plugin_status=' . $plugin_status . '&paged=' . $page . '&s=' . $s );

		// security check
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'wc-firstdata-change-gateway_' . $this->get_file() ) ) {
			wp_redirect( $return_url );
			exit;
		}

		// one of the known gateways
		if ( self::GLOBAL_GATEWAY_CLASS_NAME == $gateway || self::GGE4_GATEWAY_CLASS_NAME == $gateway ) {
			update_option( 'wc_firstdata_gateway', $gateway );
			$return_url = add_query_arg( array( 'wc_firstdata_gateway' => $gateway ), $return_url );
		}

		// back to whence we came
		wp_redirect( $return_url );
		exit;
	}


	/**
	 *
	 *
	 * @since 3.3
	 * @param WP_User $user user object for the current edit page
	 */
	public function add_customer_data( $user ) {

		// bail if the current user is not allowed to manage woocommerce or using legacy gateway
		if ( ! current_user_can( 'manage_woocommerce' ) || $this->using_global_gateway() ) {
			return;
		}

		$gateway = new WC_Gateway_FirstData();

		// tokenization disabled?
		if ( ! $gateway->tokenization_enabled() ) {
			return;
		}

		// get any payment tokens
		$payment_tokens = $gateway->get_credit_card_tokens( $user->ID );

		?>
		<h3><?php printf( esc_html__( '%s Customer Details', 'woocommerce-gateway-firstdata' ), $this->get_plugin_name() ); ?></h3>

		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Payment Tokens', 'woocommerce-gateway-firstdata' ); ?></th>
				<td>
					<?php
					if ( empty( $payment_tokens ) ):
						echo '<p>' . esc_html__( 'This customer has no saved payment tokens', 'woocommerce-gateway-firstdata' ) . '</p>';
					else:
						?>
						<ul style="margin:0;">
							<?php
							$i = 0;
							foreach ( $payment_tokens as $token => $payment ) :

								?>
									<li>
										<?php echo $token; ?> (<?php _e( sprintf( '%s ending in %s expiring %s', $payment['type'], $payment['last_four'], substr( $payment['exp_date'], 0, 2 ) . '/' . substr( $payment['exp_date'], -2 ) ) ); echo ( $payment['active'] ? ' <strong>' . __( 'Default card', 'woocommerce-gateway-firstdata' ) . '</strong>' : '' ); ?>)
										<a href="#" class="wc-firstdata-payment-token-delete" data-payment_token="<?php echo esc_attr( $token ); ?>"><?php esc_html_e( 'Delete', 'woocommerce-gateway-firstdata' ); ?></a>
									</li>
								<?php

							endforeach; ?>
						</ul>
						<input type="hidden" id="wc-firstdata-payment-tokens-deleted" name="wc_firstdata_payment_tokens_deleted" value="" />
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Add a Payment Token', 'woocommerce-gateway-firstdata' ); ?></th>
				<td>
					<input type="text" id="wc-firstdata-payment-token" name="wc_firstdata_payment_token" placeholder="<?php esc_attr_e( 'Token', 'woocommerce-gateway-firstdata' ); ?>" style="width:145px;" />
					<select id="wc-firstdata-payment-token-type" name="wc_firstdata_payment_token_type">
						<option value=""><?php esc_html_e( 'Card Type', 'woocommerce-gateway-firstdata' ); ?></option>
						<?php
						foreach ( array( "American Express", "Visa", "Mastercard", "Discover", "Diners Club", "JCB" ) as $card_type ) :
							?>
							<option><?php echo $card_type; ?></option>
							<?php
						endforeach;
						?>
					</select>
					<input type="text" id="wc-firstdata-payment-token-exp-date" name="wc_firstdata_payment_token_exp_date" placeholder="<?php esc_attr_e( 'Expiry Date (01/17)', 'woocommerce-gateway-firstdata' ); ?>" style="width:140px;" />
					<br/>
					<span class="description"><?php esc_html_e( 'Payment tokens can be added with the information found in the transaction list in your First Data account, so long as your TransArmor token was configured at the time of the transaction.  For "Token" use the "Card Number" for the tokenized transaction, found in your First Data account transaction list.', 'woocommerce-gateway-firstdata' ); ?></span>
				</td>
			</tr>
		</table>
		<?php
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( '.wc-firstdata-payment-token-delete' ).click( function() {

					if ( ! confirm( '<?php _e( 'Are you sure you wish to do this?  Change will not be finalized until you click "Update"', 'woocommerce-gateway-firstdata' ); ?>' ) ) {
						return false;
					}

					$( '#wc-firstdata-payment-tokens-deleted' ).val( $( this ).data( 'payment_token' ) + ',' + $( '#wc-firstdata-payment-tokens-deleted' ).val() );
					$( this ).closest( 'li' ).remove();
					return false;
				} );
			} );
		</script>
		<?php
	}


	/**
	 * Persist any changes to the gateway payment tokens
	 *
	 * @since 3.3
	 * @param int $user_id identifies the user to save the settings for
	 */
	public function save_customer_data( $user_id ) {

		// bail if the current user is not allowed to manage woocommerce or using legacy gateway
		if ( ! current_user_can( 'manage_woocommerce' ) || $this->using_global_gateway() ) {
			return;
		}

		$gateway = new WC_Gateway_FirstData();

		// tokenization disabled?
		if ( ! $gateway->tokenization_enabled() ) {
			return;
		}

		// deleting any payment tokens?
		$delete_payment_tokens = $_POST['wc_firstdata_payment_tokens_deleted'] ? explode( ',', trim( $_POST['wc_firstdata_payment_tokens_deleted'], ',' ) ) : array();

		// see whether we're deleting any
		foreach ( $delete_payment_tokens as $token ) {
			$gateway->delete_credit_card_token( $user_id, $token );
		}

		// adding a new payment token?
		if ( $_POST['wc_firstdata_payment_token'] && $_POST['wc_firstdata_payment_token_type'] && $_POST['wc_firstdata_payment_token_exp_date'] ) {

			// get any currently saved payment tokens
			$saved_payment_tokens = $gateway->get_credit_card_tokens( $user_id );

			// add the new payment token, making it active if this is the first card
			$gateway->add_credit_card_token(
				$user_id,
				$_POST['wc_firstdata_payment_token'],
				array(
					'type'      => $_POST['wc_firstdata_payment_token_type'],
					'last_four' => substr( $_POST['wc_firstdata_payment_token'], -4 ),
					'exp_date'  => str_replace( '/', '', $_POST['wc_firstdata_payment_token_exp_date'] ),
					'active'    => empty( $saved_payment_tokens ),
				)
			);
		}
	}


	/** Frontend methods ******************************************************/


	/**
	 * Helper to add the 'My Cards' section to the 'My Account' page
	 *
	 * Available only for the GGe4 gateway
	 *
	 * @since 3.0
	 */
	public function add_my_payment_methods() {

		$gateway = new WC_Gateway_FirstData();

		$gateway->show_my_payment_methods();
	}


	/** Helper methods ******************************************************/


	/**
	 * Main First Data Instance, ensures only one instance is/can be loaded
	 *
	 * @since 3.6.0
	 * @see wc_firstdata()
	 * @return WC_FirstData
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Gets the plugin documentation url, which for First Data is non-standard
	 *
	 * @since 3.2
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woothemes.com/document/firstdata/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 3.7.0
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'http://support.woothemes.com/';
	}


	/**
	 * Get the PHP dependencies for extension depending on the gateway being used
	 *
	 * @since 3.0
	 * @see SV_WC_Plugin::get_dependencies()
	 * @return array of required PHP extension names, based on the gateway in use
	 */
	protected function get_dependencies() {

		if ( $this->using_global_gateway() ) {
			return array( 'curl' );
		} else {
			return array( 'SimpleXML', 'xmlwriter', 'dom', 'hash' );  // GGe4 dependencies
		}
	}


	/**
	 * Checks is WooCommerce Subscriptions is active
	 *
	 * Needed until this class extends from SV_WC_Payment_Gateway_Plugin
	 *
	 * @since 3.0
	 * @return bool true if WCS is active, false if not active
	 */
	public function is_subscriptions_active() {

		return $this->is_plugin_active( 'woocommerce-subscriptions.php' );
	}


	/**
	 * Checks is WooCommerce Pre-Orders is active
	 *
	 * Needed until this class extends from SV_WC_Payment_Gateway_Plugin
	 *
	 * @since 3.0
	 * @return bool true if WC Pre-Orders is active, false if not active
	 */
	public function is_pre_orders_active() {

		return $this->is_plugin_active( 'woocommerce-pre-orders.php' );
	}


	/** Getter methods ******************************************************/


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 3.2
	 * @see SV_WC_Payment_Gateway::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce First Data', 'woocommerce-gateway-firstdata' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 3.2
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Gets the gateway class name.  This is one of the following:
	 *
	 * * WC_Gateway_FirstData_Global_Gateway - if the legacy gateway is enabled
	 * * WC_Gateway_FirstData - if the GGe4 gateway is enabled (default)
	 * * WC_Gateway_FirstData_Addons - if GGe4 is enabled and pre-orders or subscriptions plugins are installed and active
	 *
	 * @since 3.0
	 * @return string the gateway class name
	 */
	public function get_gateway_class_name() {

		if ( ! isset( $this->gateway_class_name ) ) {

			// get the configured gateway class
			$this->gateway_class_name = get_option( 'wc_firstdata_gateway', self::GGE4_GATEWAY_CLASS_NAME );

			// GGe4 version supports subscriptions/pre-orders
			if ( self::GGE4_GATEWAY_CLASS_NAME == $this->gateway_class_name && ( $this->is_subscriptions_active() || $this->is_pre_orders_active() ) ) {
				$this->gateway_class_name = self::GGE4_ADDONS_GATEWAY_CLASS_NAME;
			}
		}

		return $this->gateway_class_name;
	}


	/**
	 * Gets the gateway id for the current gateway
	 *
	 * @since 3.0
	 * @return string returns either 'firstdata' or 'firstdata-global-gateway'
	 */
	public function get_gateway_id() {
		return $this->using_gge4_gateway() ? self::GGE4_GATEWAY_ID : self::GLOBAL_GATEWAY_ID;
	}


	/**
	 * Returns true if the legacy Global Gateway is being used
	 *
	 * @since 3.0
	 * @return boolean true if the legacy Global Gateway is being used
	 */
	private function using_global_gateway() {
		return self::GLOBAL_GATEWAY_CLASS_NAME == $this->get_gateway_class_name();
	}


	/**
	 * Returns true if the GGe4 Gateway is being used
	 *
	 * @since 3.0
	 * @return boolean true if the GGe4 Gateway is being used
	 */
	private function using_gge4_gateway() {
		return ! $this->using_global_gateway();
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Handles upgrades
	 *
	 * @since 3.0
	 * @param string $installed_version the currently installed version
	 */
	protected function upgrade( $installed_version ) {

		$settings = get_option( 'woocommerce_' . self::GGE4_GATEWAY_ID . '_settings' );

		if ( ! $installed_version && $settings ) {
			// upgrading from the pre-rewrite version, need to adjust the settings array

			if ( isset( $settings['pemfile'] ) ) {
				// Global Gateway: the new global gateway id is firstdata-global-gateway, so
				//  we'll make that change and set the Global Gateway as the active version
				//  for a seamless "upgrade" from the previous standalone Global Gateway plugin

				// sandbox -> environment
				if ( isset( $settings['sandbox'] ) && 'yes' == $settings['sandbox'] ) {
					$settings['environment'] = 'sandbox';
				} else {
					$settings['environment'] = 'production';
				}
				unset( $settings['sandbox'] );

				// rename the settings option
				delete_option( 'woocommerce_' . self::GGE4_GATEWAY_ID . '_settings' );
				update_option( 'woocommerce_' . self::GLOBAL_GATEWAY_ID . '_settings', $settings );

				// Make the Global Gateway version active
				update_option( 'wc_firstdata_gateway', self::GLOBAL_GATEWAY_CLASS_NAME );

			} else {
				// GGe4

				// logger -> debug_mode
				if ( ! isset( $settings['logger'] ) || 'no' == $settings['logger'] ) {
					$settings['debug_mode'] = 'off';
				} elseif ( isset( $settings['logger'] ) && 'yes' == $settings['logger'] ) {
					$settings['debug_mode'] = 'log';
				}
				unset( $settings['logger'] );

				// set demo fields
				if ( isset( $settings['environment'] ) && 'demo' == $settings['environment'] ) {
					$settings['demo_gateway_id']       = $settings['gateway_id'];
					$settings['demo_gateway_password'] = $settings['gateway_password'];

					$settings['gateway_id']       = '';
					$settings['gateway_password'] = '';
				}

				// set the updated options array
				update_option( 'woocommerce_firstdata_settings', $settings );
			}
		}

		if ( -1 === version_compare( $installed_version, '3.1.1' ) && $settings ) {

			// standardize transaction type setting: '00' => 'purchase', '01' => 'authorization'
			if ( isset( $settings['transaction_type'] ) ) {
				if ( '01' == $settings['transaction_type'] ) {
					$settings['transaction_type'] = 'authorization';
				} else {
					$settings['transaction_type'] = 'charge';
				}
			}

			// set the updated options array
			update_option( 'woocommerce_firstdata_settings', $settings );

		}
	}


} // end WC_FirstData


/**
 * Returns the One True Instance of First Data
 *
 * @since 3.6.0
 * @return WC_FirstData
 */
function wc_firstdata() {
	return WC_FirstData::instance();
}

// fire it up!
wc_firstdata();

} // init_woocommerce_gateway_first_data()
