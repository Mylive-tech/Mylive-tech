<?php
/**
 * Plugin Name: AffiliateWP - Stripe Payouts
 * Plugin URI: http://affiliatewp.com/addons/stripe
 * Description: Pay your affiliates via Stripe directly to their bank accounts or debit cards
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.0.5
 * Text Domain: affwp-stripe-payouts
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package AffiliateWP Stripe Payouts
 * @category Core
 * @author Pippin Williamson
 * @version 1.0.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AffiliateWP_Stripe_Payouts {

	/** Singleton *************************************************************/

	/**
	 * @var AffiliateWP_Stripe_Payouts The one true AffiliateWP_Stripe_Payouts
	 * @since 1.0
	 */
	private static $instance;

	public static $plugin_dir;
	public static $plugin_url;
	private static $version;

	/**
	 * Main AffiliateWP_Stripe_Payouts Instance
	 *
	 * Insures that only one instance of AffiliateWP_Stripe_Payouts exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true AffiliateWP_Stripe_Payouts
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Stripe_Payouts ) ) {
			self::$instance = new AffiliateWP_Stripe_Payouts;

			self::$plugin_dir = plugin_dir_path( __FILE__ );
			self::$plugin_url = plugin_dir_url( __FILE__ );
			self::$version    = '1.0.5';

			if( function_exists( 'affiliate_wp' ) && is_object( affiliate_wp()->settings ) ) {

				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->init();

			}

		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affwp-stripe-payouts' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affwp-stripe-payouts' ), '1.0' );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'affwp_stripe_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affwp-stripe-payouts' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'affwp-stripe-payouts', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affwp-stripe-payouts/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affwp-stripe-payouts/ folder
			load_textdomain( 'affwp-stripe-payouts', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affwp-stripe-payouts/languages/ folder
			load_textdomain( 'affwp-stripe-payouts', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affwp-stripe-payouts', false, $lang_dir );
		}
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		if( is_admin() ) {

			require_once self::$plugin_dir . 'admin/affiliates.php';
			require_once self::$plugin_dir . 'admin/referrals.php';
			require_once self::$plugin_dir . 'admin/settings.php';

		}

		if( ! class_exists( 'Stripe' ) ) {

			require_once self::$plugin_dir . 'stripe/lib/Stripe.php';

		}

	}

	/**
	 * Add in our filters to affect affiliate rates
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function init() {

		/** Filters **/

		add_filter( 'affwp_template_paths', array( $this, 'template_paths' ) );

		/** Actions **/

		add_action( 'affwp_affiliate_dashboard_before_submit', array( $this, 'profile_settings' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		add_action( 'affwp_update_profile_settings', array( $this, 'process_affiliate_card_update' ), 9 );

		if( is_admin() ) {
			self::$instance->updater();
		}

	}

	/**
	 * Registers our add-on's templates folder in the template path stack
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function template_paths( $paths = array() ) {

		$paths[] = self::$plugin_dir . 'templates';

		return $paths;
	}

	/**
	 * Loads the credit / bank account form in the affiliate area
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function profile_settings() {

		if( 'active' != affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) {
			return;
		}

		affiliate_wp()->templates->get_template_part( 'stripe', 'settings' );

	}

	/**
	 * Loads our frontend javascript
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function scripts() {

		$affiliate_area = affiliate_wp()->settings->get( 'affiliates_page' );

		if( ! is_page( $affiliate_area ) ) {

			return;

		}

		$keys = $this->get_api_credentials();

		if( $this->is_test_mode() ) {
			$publishable_key = $keys['test_publishable_key'];
		} else {
			$publishable_key = $keys['live_publishable_key'];
		}

		wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v2/', array( 'jquery' ) );
		wp_enqueue_script( 'affwp-stripe', self::$plugin_url . 'assets/js/affwp-stripe.js', array( 'jquery' ) );
		wp_localize_script( 'affwp-stripe', 'affwp_stripe_vars', array(
			'publishable_key' => $publishable_key
		) );
	}

	/**
	 * Processes the debit card form in the affiliate area
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process_affiliate_card_update( $data ) {

		if( ! affwp_is_affiliate() ) {
			return;
		}

		$affiliate_id = affwp_get_affiliate_id();
		$recipient_id = $this->get_recipient_id( $affiliate_id );
		$type         = ! empty( $data['recipient_type'] ) ? sanitize_text_field( $data['recipient_type'] ) : 'individual';

		if( 'active' != affwp_get_affiliate_status( $affiliate_id ) ) {
			return;
		}

		if( empty( $data['stripeToken'] ) ) {
			return;
		}

		Stripe::setApiKey( $this->get_secret_key() );

		try {

			if( $recipient_id ) {

				// Update existing recipient
				$rp         = Stripe_Recipient::retrieve( $recipient_id );
				$rp->type   = $type;
				$rp->card   = $data['stripeToken'];
				$rp->email  = affwp_get_affiliate_email( $affiliate_id );
				$rp->name   = sanitize_text_field( $data['card_name'] );
				$rp->tax_id = sanitize_text_field( $data['tax_id'] );
				$rp->save();

			} else {

				// Create a new recipient
				$recipient   = Stripe_Recipient::create( array(
					'name'   => sanitize_text_field( $data['card_name'] ),
					'type'   => $type,
					'card'   => $data['stripeToken'],
					'email'  => affwp_get_affiliate_email( $affiliate_id ),
					'tax_id' => sanitize_text_field( $data['tax_id'] )
				) );

				$this->set_recipient_id( $affiliate_id, $recipient->id );
			}

		} catch ( Stripe_CardError $e ) {

			// Since it's a decline, Stripe_CardError will be caught
			$body = $e->getJsonBody();
			$err  = $body['error'];
		
		} catch ( Stripe_InvalidRequestError $e ) {

			// Invalid parameters were supplied to Stripe's API
			$body = $e->getJsonBody();
			$err  = $body['error'];

		} catch ( Stripe_AuthenticationError $e ) {

			// Authentication with Stripe's API failed
			// (maybe you changed API keys recently)
			$body = $e->getJsonBody();
			$err  = $body['error'];

		} catch ( Stripe_ApiConnectionError $e ) {

			// Network communication with Stripe failed
			$body = $e->getJsonBody();
			$err  = $body['error'];

		} catch ( Stripe_Error $e ) {

			// Display a very generic error to the user, and maybe send
			// yourself an email
			$body = $e->getJsonBody();
			$err  = $body['error'];


		} catch ( Exception $e ) {

			// Something else happened, completely unrelated to Stripe
			$body = $e->getJsonBody();
			$err  = $body['error'];

		}

		if( ! empty( $error ) ) {

			$redirect = add_query_arg( array( 'error' => $err['type'], 'message' => urlencode( $err['message'] ), 'code' => $e->getHttpStatus() ) );

		} else {

			$redirect = add_query_arg( 'success', 1 );

		}

		wp_redirect( $redirect ); exit;

	}

	/**
	 * Gets the Stripe API keys
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function get_api_credentials() {

		$keys = array(
			'test_secret_key'      => affiliate_wp()->settings->get( 'stripe_test_secret_key', '' ),
			'test_publishable_key' => affiliate_wp()->settings->get( 'stripe_test_publishable_key', '' ),
			'live_secret_key'      => affiliate_wp()->settings->get( 'stripe_live_secret_key', '' ),
			'live_publishable_key' => affiliate_wp()->settings->get( 'stripe_live_publishable_key', '' )
		);

		return $keys;
	}

	/**
	 * Gets the Stripe secret key
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function get_secret_key() {

		$keys = $this->get_api_credentials();

		if( $this->is_test_mode() ) {
			$secret = trim( $keys['test_secret_key'] );
		} else {
			$secret = trim( $keys['live_secret_key'] );
		}

		return $secret;
	}

	/**
	 * Determines if we are in test mode
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function is_test_mode() {

		return affiliate_wp()->settings->get( 'stripe_test_mode', false );
	}

	/**
	 * Gets the Stripe recipient ID for an affiliate
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function get_recipient_id( $affiliate_id = 0 ) {

		if( empty( $affiliate_id ) ) {
			return false;
		}

		$user_id = affwp_get_affiliate_user_id( $affiliate_id );

		return get_user_meta( $user_id, 'affwp_stripe_recipient_id', true );
	}

	/**
	 * Sets the Stripe recipient ID for an affiliate
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function set_recipient_id( $affiliate_id = 0, $recipient_id = '' ) {

		if( empty( $affiliate_id ) ) {
			return false;
		}

		$user_id = affwp_get_affiliate_user_id( $affiliate_id );

		return update_user_meta( $user_id, 'affwp_stripe_recipient_id', $recipient_id );
	}

	/**
	 * Sets up the plugin updater class
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	public function updater() {

		if( class_exists( 'AffWP_AddOn_Updater' ) ) {
			$updater = new AffWP_AddOn_Updater( 4878, __FILE__, self::$version );
		}
	}

}

/**
 * The main function responsible for returning the one true AffiliateWP_Stripe_Payouts
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $AffiliateWP_Stripe_Payouts = affiliate_wp_stripe(); ?>
 *
 * @since 1.0
 * @return object The one true AffiliateWP_Stripe_Payouts Instance
 */
function affiliate_wp_stripe() {
	return AffiliateWP_Stripe_Payouts::instance();
}
add_action( 'plugins_loaded', 'affiliate_wp_stripe', 100 );