<?php
/**
 * Plugin Name: WooCommerce Shop as Customer
 * Description: Shop as Customer allows a store Administrator or Shop Manager to shop the front-end of the store as another User, allowing all functionality such as, order creation, checkout and plugins that only work on the product or cart pages and not the Admin Order page, to function normally as if they were that Customer.
 * Author: cxThemes
 * Author URI: http://codecanyon.net/user/cxThemes
 * Plugin URI: http://codecanyon.net/item/shop-as-customer-for-woocommerce/7043722
 * Version: 1.14
 * Text Domain: shop-as-customer
 * Domain Path: /languages/
 *
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-SHOP-AS-USER
 * @author    cxThemes
 * @category  WooCommerce
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define Constants
 */
define( 'WC_SHOP_AS_CUSTOMER_VERSION', '1.14' );
define( 'WC_SHOP_AS_CUSTOMER_REQUIRED_WOOCOMMERCE_VERSION', 2.2 );

/**
 * Update Check
 */
require 'plugin-updates/plugin-update-checker.php';
$wc_shop_as_customer_update = new PluginUpdateChecker(
	'http://cxthemes.com/plugins/woocommerce-shop-as-customer/shop-as-customer.json',
	__FILE__,
	'shop-as-customer'
);

/**
 * Check if WooCommerce is active, and is required WooCommerce version.
 */
if ( ! WC_Shop_As_Customer::is_woocommerce_active() || version_compare( get_option( 'woocommerce_version' ), WC_SHOP_AS_CUSTOMER_REQUIRED_WOOCOMMERCE_VERSION, '<' ) ){
	add_action( 'admin_notices', array( 'WC_Shop_As_Customer', 'woocommerce_inactive_notice' ) );
	return;
}

/**
 * Includes
 */
include_once( 'shop-as-customer-functions.php' );

/**
 * Instantiate plugin.
 */
$wc_shop_as_customer = WC_Shop_As_Customer::get_instance();

/**
 * Main Class.
 */
class WC_Shop_As_Customer {

	private $id = 'woocommerce_shop_as_customer';

	private static $instance;

	/**
	* Get Instance creates a singleton class that's cached to stop duplicate instances
	*/
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	* Construct empty on purpose
	*/
	private function __construct() {}

	/**
	* Init behaves like, and replaces, construct
	*/
	public function init(){
		
		// Translations
		add_action( 'init', array( $this, 'load_translation' ) );

		add_action( 'plugins_loaded', array( $this, 'action_plugins_loaded' ) );

		add_action( 'plugins_loaded', array( $this, 'shop_as_customer_load' ) );
		
		// Admin settings page
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'shop_as_customer_save_options' ) );
			add_action( 'admin_notices', array( $this, 'shop_as_customer_order_notice' ) );
		}
	}

	/**
	 * Add actions and hooks after plugins have been loaded
	 */
	public function shop_as_customer_load() {
		
		// Get miimum required user role.
		$shop_as_user_role = get_option( 'shop_as_user_role', 'shop_manager' );

		if ( ( self::test_user_role($shop_as_user_role) ) || ( $old_user = self::get_old_user() ) ) {
			
			add_action( 'wp_loaded', array( $this, 'checkout_action' ), 30 );

			add_action( 'wp_ajax_woocommerce_checkout', array( $this, 'checkout' ), 1 );
			add_action( 'wp_ajax_nopriv_woocommerce_checkout', array( $this, 'checkout' ), 1 );
			add_action( 'wc_ajax_checkout', array( $this, 'checkout' ), 1 );

			if ( is_admin() ) {
				add_action( 'admin_print_styles', array( $this, 'admin_scripts' ) );
			}
			else {
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			}

			# Required functionality:
			add_filter( 'user_has_cap',                    array( $this, 'filter_customer_has_cap' ), 10, 3 );
			add_filter( 'map_meta_cap',                    array( $this, 'filter_map_meta_cap' ), 10, 4 );

			add_filter( 'user_row_actions',                array( $this, 'filter_customer_row_actions' ), 10, 2 );
			add_action( 'init',                            array( $this, 'action_init' ) );
			add_action( 'all_admin_notices',               array( $this, 'action_admin_notices' ), 1 );
			add_action( 'wp_logout',                       'wp_clear_old_user_cookie' );
			add_action( 'wp_logout',                       'wp_clear_previous_switched_cookie' );
			add_action( 'wp_login',                        'wp_clear_old_user_cookie' );
			add_action( 'wp_login',                        'wp_clear_previous_switched_cookie' );

			add_filter( 'ms_user_row_actions',             array( $this, 'filter_customer_row_actions' ), 10, 2 );
			add_filter( 'login_message',                   array( $this, 'filter_login_message' ), 1 );
			add_action( 'personal_options',                array( $this, 'action_personal_options' ) );
			add_action( 'admin_bar_menu',                  array( $this, 'action_admin_bar_menu' ), 999 );

			if ( isset( $_GET["order_on_behalf"] ) || isset( $_GET["key"] ) ) {

				add_action( 'wp',    array( $this, 'add_checkout_success_message' ) );
				//add_action( 'wp',    array( $this, 'send_customer_invoice' ) );
			}

			add_action('wp_ajax_woocommerce_json_shop_as_customers_search', array( $this, 'woocommerce_json_shop_as_customers_search') );

		}

	}

	/**
	 * Localization
	 */
	public function load_translation() {

		// Domain ID - used in eg __( 'Text', 'email-control' )
		$domain = 'shop-as-customer';

		// get the languages locale eg 'en_US'
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		// Look for languages here: wp-content/languages/pluginname/pluginname-en_US.mo
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );

		// Look for languages here: wp-content/languages/pluginname-en_US.mo
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '-' . $locale . '.mo' );

		// Look for languages here: wp-content/plugins/pluginname/languages/pluginname-en_US.mo
		load_plugin_textdomain($domain, FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
	}

	/**
	 * Add a submenu item to the WooCommerce menu
	 */
	public function admin_menu() {

		add_submenu_page(
			'woocommerce',
			__( 'Shop as Customer', 'shop-as-customer' ),
			__( 'Shop as Customer', 'shop-as-customer' ),
			'create_users',
			$this->id,
			array( $this, 'admin_page' )
		);
	}

	/**
	 * Include admin scripts
	 */
	public function admin_scripts() {

		global $woocommerce, $wp_scripts;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'woocommerce_admin' );
    	wp_enqueue_script( 'farbtastic' );
    	wp_enqueue_script( 'ajax-chosen' );
    	wp_enqueue_script( 'chosen' );
    	wp_enqueue_script( 'jquery-ui-sortable' );

    	/* Top Drop-Down Stuff */
		wp_register_style( 'woocommerce-shop-as-customer', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/css/shop-as-customer-styles.css', basename( __FILE__ ) ), '', WC_SHOP_AS_CUSTOMER_VERSION, 'screen' );
		wp_enqueue_style( 'woocommerce-shop-as-customer' );
		wp_register_script( 'woocommerce-shop-as-customer', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/js/shop-as-customer.js', basename( __FILE__ ) ), array('jquery'), $woocommerce->version );
		wp_enqueue_script( 'woocommerce-shop-as-customer' );

		/* Options Page Stuff */
		wp_register_style( 'woocommerce-shop-as-customer-options', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/css/options-page-style.css', basename( __FILE__ ) ), '', WC_SHOP_AS_CUSTOMER_VERSION, 'screen' );
		wp_enqueue_style( 'woocommerce-shop-as-customer-options' );

		$woocommerce_shop_as_customer_params = array(
			'ajax_url' 						=> admin_url('admin-ajax.php'),
			'nonce'							=> wp_create_nonce("search-customers")
		);

		wp_localize_script( 'woocommerce-shop-as-customer', 'woocommerce_shop_as_customer_params', $woocommerce_shop_as_customer_params );

	}

	/**
	 * Include frontend scripts
	 */
	public function frontend_scripts() {

		global $woocommerce, $wp_scripts;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'chosen',
			$woocommerce->plugin_url() . '/assets/js/chosen/chosen.jquery'.$suffix.'.js',
			array('jquery'),
			$woocommerce->version
		);
		
		wp_register_script(
			'ajax-chosen',
			$woocommerce->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery'.$suffix.'.js',
			array('jquery',
				'chosen'),
			$woocommerce->version
		);
		
		wp_register_script(
			'jquery-tiptip',
			$woocommerce->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip'.$suffix.'.js',
			array('jquery'),
			$woocommerce->version
		);

		wp_enqueue_script( 'woocommerce_admin' );
    	wp_enqueue_script( 'farbtastic' );
    	wp_enqueue_script( 'chosen' );
    	wp_enqueue_script( 'ajax-chosen' );
    	wp_enqueue_script( 'jquery-ui-sortable' );
    	wp_enqueue_script( 'jquery-ui-autocomplete' );
    	wp_enqueue_script( 'jquery-migrate' );
		wp_enqueue_script( 'jquery-tiptip' );

    	wp_enqueue_style(
			'woocommerce-shop-as-customer',
    		plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/css/shop-as-customer-styles.css', basename( __FILE__ ) ),
    		array(),
    		WC_SHOP_AS_CUSTOMER_VERSION,
    		'screen'
		);

    	wp_enqueue_script(
			'woocommerce-shop-as-customer',
    		plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/js/shop-as-customer.js', basename( __FILE__ ) ),
    		array('jquery'),
    		$woocommerce->version,
    		true
		);

		wp_localize_script( 'woocommerce-shop-as-customer', 'woocommerce_shop_as_customer_params', array(
			'ajax_url'	=> admin_url('admin-ajax.php'),
			'nonce'		=> wp_create_nonce("search-customers"),
		));

	}

	/**
	 * Define the name of the old user cookie. Uses WordPress' cookie hash for increased security.
	 *
	 * @return null
	 */
	public function action_plugins_loaded() {

		if ( !defined( 'OLD_USER_COOKIE' ) ) {
			define( 'OLD_USER_COOKIE', 'wordpress_old_user_' . COOKIEHASH );
		}
		if ( !defined( 'SWITCHED_USERS_COOKIE' ) ) {
			define( 'SWITCHED_USERS_COOKIE', 'wordpress_switched_users_' . COOKIEHASH );
		}
		if ( $old_user = self::get_old_user() ) {
			if ( !is_admin_bar_showing() ) {
				add_filter('show_admin_bar', '__return_true');
			}

			add_filter( 'woocommerce_order_button_html', array( $this, 'edit_place_order_button_text' ) );
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'redirect_customer_on_order_processed' ) );
		}
	}

	/**
	 * Process ajax checkout form
	 */
	public function checkout() {
		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) )
			define( 'WOOCOMMERCE_CHECKOUT', true );

		if ( ! isset( $_POST["payment_method"] ) ) {
			add_filter( 'woocommerce_cart_needs_payment', '__return_false' );
		}

		$woocommerce_checkout = WC()->checkout();
		$woocommerce_checkout->process_checkout();

		die(0);
	}

	/**
	 * Override the default order button on the checkout page
	 *
	 * @return null
	 */
	public function edit_place_order_button_text($link) {

		ob_start();

		$user_id      = get_current_user_id();
		$current_user = wp_get_current_user();

		$avatar = get_avatar( $user_id, 26 );
		$shopping_as  = sprintf( __('Shopping as %1$s', 'shop-as-customer'), $current_user->display_name );
		$class  = empty( $avatar ) ? '' : ' with-avatar'; ?>

		<div class="sac-frontend sac-frontend-checkout">

			<div class="shopping-as <?php echo $class; ?>">
				<?php echo $shopping_as . " &nbsp;" . $avatar; ?>
			</div>

			<span class="button-block create-this-order-block">

				<input type="submit" class="button alt" name="woocommerce_checkout_save_order" id="shop_as_customer_save_order" value="<?php _e("Create this Order", "shop-as-customer") ?>" />
				<br />
				<span class="sac-info create-this-order-info-tooltip"><?php _e("what this button does", 'shop-as-customer'); ?> &nbsp;<span class="sac-info-icon">&nbsp;</span></span>

				<div class="sac-tooltip create-this-order-info-tooltip-html">
					<span class="sac-tip-heading"><?php _e("Choosing", 'shop-as-customer'); ?> <i><?php _e("Create This Order", 'shop-as-customer'); ?></i> <?php _e("will", 'shop-as-customer'); ?>:</span>
					<ul>
						<li><?php _e("Create the Order in WooCommerce", 'shop-as-customer'); ?></li>
						<li><?php _e("Take you to the next page where you are able to send the Customer Invoice email to the customer with a link for them to Pay.", 'shop-as-customer'); ?></li>
						<li><?php _e("It will not contact the customer until you choose to on the next page", 'shop-as-customer'); ?></li>
					</ul>
				</div>
			</span>
			&nbsp;
			<span class="button-block pay-order-order-block">

				<input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="shop_as_customer_place_order" value="<?php _e("Pay for this Order", "shop-as-customer") ?>" />
				<br />
				<span class="sac-info pay-order-order-info-tooltip"><?php _e("what this button does", 'shop-as-customer'); ?> &nbsp;<span class="sac-info-icon">&nbsp;</span></span>

				<div class="sac-tooltip pay-order-order-info-tooltip-html">
					<span class="sac-tip-heading"><?php _e("Choosing", 'shop-as-customer'); ?> <i> <?php _e("Pay for This Order", 'shop-as-customer'); ?> </i> <?php _e("will", 'shop-as-customer'); ?>:</span>
					<ul>
						<li><?php _e("Proceed normally with selected Payment Method. You will be expected to pay 'on behalf' of the customer", 'shop-as-customer'); ?></li>
						<li><?php _e("Create the order in WooCommerce", 'shop-as-customer'); ?></li>
						<li><?php _e("On your successful payment, the Processing Order email will be sent to the customer and the order can be dealt with in the normal way", 'shop-as-customer'); ?></li>
					</ul>
				</div>
			</span>

		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Process the checkout form.
	 */
	public function checkout_action() {
		
		if ( isset( $_POST['woocommerce_checkout_save_order'] ) ) {

			add_filter( 'woocommerce_cart_needs_payment', '__return_false' );

			global $woocommerce;

			if ( sizeof( $woocommerce->cart->get_cart() ) == 0 ) {
				wp_redirect( get_permalink( woocommerce_get_page_id( 'cart' ) ) );
				exit;
			}

			if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
				define( 'WOOCOMMERCE_CHECKOUT', true );
			}

			$woocommerce_checkout = $woocommerce->checkout();
			$woocommerce_checkout->process_checkout();
		}
	}

	/**
	 * Stop order processing after pending order is created and redirect to success page
	 *
	 * @return null
	 */
	public function redirect_customer_on_order_processed( $order_id, $posted = null ) {
		
		// global $wp_rewrite;
		// if ( ! isset( $wp_rewrite ) ) $wp_rewrite = new WP_Rewrite();

		if ( ( isset( $_POST['woocommerce_checkout_save_order'] ) ) || ( ! isset( $_POST["payment_method"] ) ) ) {

			global $woocommerce;
			$order = new WC_Order( $order_id );

			if ( $old_user = self::get_old_user() ) {
				if ( function_exists('wc_add_notice') ) {
					$order->add_order_note( sprintf( __( 'Order created by %1$s using Shop as Customer', 'shop-as-customer' ), $old_user->display_name ), 0);
					update_post_meta( $order_id, 'create_by', 'shop_as_customer' );
				}
			}

			if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
				$thanks_page_id = woocommerce_get_page_id( 'thanks' );
				$thanks_page    = get_permalink( $thanks_page_id );
				$thanks_page = esc_url_raw( add_query_arg( 'key', $order->order_key, add_query_arg( 'order', $order_id, add_query_arg( 'order_on_behalf', 1, $thanks_page ) ) ) );
			}
			else {
				$thanks_page = wc_get_endpoint_url( 'order-received', $order_id, get_permalink( wc_get_page_id( 'checkout' ) ) );
				$thanks_page = esc_url_raw( add_query_arg( 'key', $order->order_key, add_query_arg( 'order_on_behalf', 1, $thanks_page ) ) );
			}
			$result = array( "result" => "success", "redirect" => $thanks_page );

			if ( is_ajax() ) {
				wp_send_json( $result );
			}
			else {
				wp_redirect( $result['redirect'] );
				exit;
			}

		}
		else {

			global $woocommerce;
			$order = new WC_Order( $order_id );

			if ( $old_user = self::get_old_user() ) {

				if ( function_exists('wc_add_notice') ) {
					$order->add_order_note( sprintf( __( 'Order created by %1$s using Shop as Customer', 'shop-as-customer' ), $old_user->display_name ), 0);
					update_post_meta( $order_id, 'create_by', 'shop_as_customer' );
				}

			}

		}
	}

	public function add_checkout_success_message() {
		global $woocommerce, $wp;

		// Exit if neither of the required order id's exist
		if ( ! isset( $wp->query_vars['order-received'] ) ) return;

		$order_id = $wp->query_vars['order-received'];

		if ( isset( $order_id ) ) {

			$order = new WC_Order( absint($order_id) );

			add_action( 'woocommerce_thankyou_' . $order->payment_method, array( $this, "post_checkout_as_customer_options" ) );
		}
	}

	/**
	 *
	 *
	 * @return null
	 */
	public function post_checkout_as_customer_options($order_id) {
		global $woocommerce;

		$order = new WC_Order( $order_id );

		// Send invoice emails.
		$this->send_customer_invoice();
		
		// Display notification if invoice was sent.
		$invoice_sent = isset( $_GET["invoice_sent"] ) ? $_GET["invoice_sent"] : null;
		if ( isset($invoice_sent) ) {
			echo '<div class="woocommerce-message sac-woocommerce-message">'.__('Invoice sent to Customer successfully.', 'shop-as-customer').'</div>';
		}

		$send_invoice_url = esc_url_raw(
			add_query_arg(
				array(
					'key' => $order->order_key,
					'order_on_behalf' => 1,
					'send_invoice' => 1,
				),
				wc_get_endpoint_url(
					'order-received',
					$order_id,
					get_permalink( wc_get_page_id( 'checkout' ) )
				)
			)
		);

		$admin_order_url = esc_url_raw( add_query_arg( array(
			'redirect_to_order' => $order_id
		), self::switch_back_url() ) );

		$user_id      = get_current_user_id();
		$current_user = wp_get_current_user();

		$avatar = get_avatar( $user_id, 26 );
		$shopping_as  = sprintf( __('Shopping as %1$s', 'shop-as-customer'), $current_user->display_name );
		$class  = empty( $avatar ) ? '' : ' with-avatar'; ?>

		<div class="sac-frontend sac-frontend-complete">

			<div class="shopping-as <?php echo $class; ?>">
				<?php echo $shopping_as . " &nbsp;" . $avatar; ?>
			</div>

			<?php
			if ( $order->needs_payment() ) {
			?>
				<div class="button-block send-out-invoice-block">

					<a class="button shop-as-customer-button shop-as-customer-button-send-invoice" href="<?php echo $send_invoice_url; ?>"><?php echo __('Send Request-to-Pay to Customer', 'shop-as-customer'); ?></a>
					<br />
					<span class="sac-info send-out-invoice-info-tooltip"><span class="sac-info-icon">&nbsp;</span> <?php echo __("what this button does", 'shop-as-customer'); ?></span>

					<div class="sac-tooltip send-out-invoice-info-tooltip-html">
						<span class="sac-tip-heading"><?php _e("Choosing", 'shop-as-customer'); ?> <i> <?php _e("Send Request-to-Pay to Customer", 'shop-as-customer'); ?> </i> <?php _e("will", 'shop-as-customer'); ?>:</span>
						<ul>
							<li><?php _e("Send the Customer Invoice email to the customer with a link to pay", 'shop-as-customer'); ?></li>
							<li><?php _e("The customer will link back to the Checkout page with available payment options presented to them where they can choose one and pay", 'shop-as-customer'); ?></li>
							<li><?php _e("The order will remain as Pending until they successfully Pay wherafter it will change to Processing", 'shop-as-customer'); ?></li>
						</ul>
					</div>
				</div>
			<?php
			}
			else {
			?>
				<div class="button-block send-out-invoice-block">

					<a class="button shop-as-customer-button shop-as-customer-button-send-invoice" href="<?php echo $send_invoice_url; ?>"><?php echo __('Send the Invoice Email to Customer', 'shop-as-customer'); ?></a>
					<br />
					<span class="sac-info send-out-invoice-info-tooltip"><span class="sac-info-icon">&nbsp;</span> <?php echo __("what this button does", 'shop-as-customer'); ?></span>

					<div class="sac-tooltip send-out-invoice-info-tooltip-html">
						<span class="sac-tip-heading"><?php _e("Choosing", 'shop-as-customer'); ?> <i> <?php _e("Send the Invoice Email to Customer", 'shop-as-customer'); ?> </i> <?php _e("will", 'shop-as-customer') ?>:</span>
						<ul>
							<li><?php _e("Send the Customer Invoice email with a summary of the order. There is no payment due and no Pay link will display on the email", 'shop-as-customer'); ?></li>
						</ul>
					</div>
				</div>
			<?php
			}
			?>

			&nbsp;

			<div class="button-block switch-back-view-block">

				<a class="button shop-as-customer-button shop-as-customer-button-switch-back" href="<?php echo $admin_order_url; ?>"><?php echo __('Switch back and View Order', 'shop-as-customer'); ?></a>
				<br />
				<span class="sac-info switch-back-view-info-tooltip"><span class="sac-info-icon">&nbsp;</span> <?php echo __("what this button does", 'shop-as-customer'); ?></span>

				<div class="sac-tooltip switch-back-view-info-tooltip-html">
					<span class="sac-tip-heading"><?php _e("Choosing", 'shop-as-customer'); ?> <i> <?php _e("Switch Back and View Order", 'shop-as-customer'); ?> </i> <?php _e("will", 'shop-as-customer'); ?>:</span>
					<ul>
						<li><?php _e("Switch back to your main user (Admin or Shop Manager)", 'shop-as-customer'); ?></li>
						<li><?php _e("Take you to the order in the admin section of WooCommerce", 'shop-as-customer'); ?></li>
						<li><?php _e("Not send any email to the customer", 'shop-as-customer'); ?></li>
					</ul>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 *
	 *
	 * @return null
	 */
	public function send_customer_invoice() {

		global $woocommerce, $wp;

		// Exit if neither of the required order id's exist
		if ( ! isset( $wp->query_vars['order-received'] ) ) return;

		$order_id        = $wp->query_vars['order-received'];
		$order_on_behalf = ( isset( $_GET["order_on_behalf"] ) ) ? $_GET["order_on_behalf"] : null;
		$send_invoice    = ( isset( $_GET["send_invoice"] ) ) ? $_GET["send_invoice"] : null;

		if ( isset( $order_id ) && isset( $order_on_behalf ) && isset( $send_invoice ) ) {

			$order = new WC_Order( $order_id );

			if ( isset( $order ) ) {
				$mailer = $woocommerce->mailer();
				$mails = $mailer->get_emails();
				if ( ! empty( $mails ) ) {
					foreach ( $mails as $mail ) {
						if ( "customer_invoice" == $mail->id ) {
							$mail->trigger( $order->id );
						}
					}
				}
			}
			
			$invoice_sent_url = esc_url_raw(
				add_query_arg(
					array(
						'key' => $order->order_key,
						'order_on_behalf' => 1,
						'invoice_sent' => 1,
					),
					wc_get_endpoint_url(
						'order-received',
						$order_id,
						get_permalink( wc_get_page_id( 'checkout' ) )
					)
				)
			);
			
			wp_redirect( $invoice_sent_url );
			exit;
		}
	}

	/**
	 * Output the 'Shop As' link on the customer editing screen if we have permission to shop as this customer.
	 *
	 * @param WP_User $user User object for this screen
	 * @return null
	 */
	public function action_personal_options( WP_User $user ) {

		if ( ! $link = self::maybe_shop_as_url( $user->ID ) )
			return;

		?>
		<tr>
			<th scope="row"><?php _ex( 'Shop as Customer', 'Shop as Customer title on user profile screen', 'shop-as-customer' ); ?></th>
			<td><a href="<?php echo $link; ?>"><?php _e( 'Shop&nbsp;As', 'shop-as-customer' ); ?></a></td>
		</tr>
		<?php
	}

	/**
	 * Return whether or not the current logged in user is being remembered in the form of a persistent browser
	 * cookie (ie. they checked the 'Remember Me' check box when they logged in). This is used to persist the
	 * 'remember me' value when the user switches to another user.
	 *
	 * @return bool Whether the current user is being 'remembered' or not.
	 */
	public static function remember() {

		$current     = wp_parse_auth_cookie( '', 'logged_in' );
		$cookie_life = apply_filters( 'auth_cookie_expiration', 172800, get_current_user_id(), false );

		# Here we calculate the expiration length of the current auth cookie and compare it to the default expiration.
		# If it's greater than this, then we know the user checked 'Remember Me' when they logged in.
		return ( ( $current['expiration'] - time() ) > $cookie_life );

	}

	/**
	 * Route actions depending on the 'action' query var.
	 *
	 * @return null
	 */
	public function action_init() {
		
		if ( ! isset( $_REQUEST['action'] ) )
			return;

		if ( isset( $_REQUEST['redirect_to'] ) and ! empty( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = self::remove_query_args( $_REQUEST['redirect_to'] );
		}
		else {
			$redirect_to = false;
		}

		switch ( $_REQUEST['action'] ) {
			
			# We're attempting to switch to another user:
			case 'shop_as_customer':
				
				$user_id = absint( $_REQUEST['user_id'] );

				check_admin_referer( "shop_as_customer_{$user_id}" );

				# Switch user:
				if ( switch_to_customer( $user_id, self::remember() ) ) {
					
					# Redirect to the dashboard or the home URL depending on capabilities:
					if ( $redirect_to ) {
						wp_safe_redirect( esc_url_raw( add_query_arg( array( 'shopping_as_customer' => 'true' ), $redirect_to ) ) );
					}
					else if ( ! current_user_can( 'read' ) ) {
						wp_redirect( esc_url_raw( add_query_arg( array( 'shopping_as_customer' => 'true' ), get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) ) );
					}
					else {
						wp_redirect( esc_url_raw( add_query_arg( array( 'shopping_as_customer' => 'true' ), get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) ) );
					}
					
					die();

				}
				else {

					$referer_link = '';
					
					if( wp_get_referer() ) {
						$referer_link = ' <a href="' . wp_get_referer() . '">← ' . __('back to previous page','shop-as-customer') . '</a>';
					}

					wp_die( __( "Sorry, you can't shop as this customer.", 'shop-as-customer' ) . '<br />' . __( 'They have higher capabilites so switching would not be secure.', 'shop-as-customer' ) . '<br />' . $referer_link );
				}
				
			break;

			# We're attempting to switch back to the originating user:
			case 'back_to_old_user':

				check_admin_referer( 'back_to_old_user' );

				# Fetch the originating user data:
				if ( !$old_user = self::get_old_user() )
					wp_die( __( 'Could not switch back to originating user.', 'shop-as-customer' ) );

				# Switch user:
				if ( switch_to_customer( $old_user->ID, self::remember(), false ) ) {
					if ( isset( $_REQUEST['redirect_to_order'] ) and !empty( $_REQUEST['redirect_to_order'] ) ) {
						$redirect_to_order = html_entity_decode( get_edit_post_link( $_REQUEST['redirect_to_order'] ) );
						wp_safe_redirect($redirect_to_order);
					}
					else {
						if ( $redirect_to )
							wp_safe_redirect( esc_url_raw( add_query_arg( array( 'shopping_as_customer' => 'true', 'switched_back_user' => 'true' ), $redirect_to ) ) );
						else
							wp_redirect( esc_url_raw( add_query_arg( array( 'shopping_as_customer' => 'true', 'switched_back_user' => 'true' ), admin_url( 'users.php' ) ) ) );
					}
					die();
				}
				else {
					wp_die( __( 'Could not switch back to originating user.', 'shop-as-customer' ) );
				}
				
			break;
		}

	}

	/**
	 * Display the 'Shop as {user}' and 'Back to {user}' messages in the admin area.
	 *
	 * @return null
	 */
	public function action_admin_notices() {
		$user = wp_get_current_user();

		if ( $old_user = self::get_old_user() ) {

			?>
			<div id="user_switching" class="updated">
				<p><?php
					if ( isset( $_GET['shopping_as_customer'] ) )
						printf( __( 'Back to %1$s (%2$s).', 'shop-as-customer' ), $user->display_name, $user->user_login );
					$url = esc_url_raw( add_query_arg( array(
						'redirect_to' => urlencode( self::current_url() )
					), self::switch_back_url() ) );
					printf( ' <a href="%s">%s</a>.', $url, sprintf( __( 'Back to %1$s (%2$s)', 'shop-as-customer' ), $old_user->display_name, $old_user->user_login ) );
				?></p>
			</div>
			<?php

		}
		else if ( isset( $_GET['shopping_as_customer'] ) ) {

			?>
			<div id="user_switching" class="updated">
				<p><?php
					if ( isset( $_GET['switched_back_user'] ) )
						printf( __( 'Back to %1$s (%2$s).', 'shop-as-customer' ), $user->display_name, $user->user_login );
					else
						printf( __( 'Shop as %1$s (%2$s).', 'shop-as-customer' ), $user->display_name, $user->user_login );
				?></p>
			</div>
			<?php

		}
	}

	/**
	 * Validate the latest item in the old_user cookie and return its user data.
	 *
	 * @return bool|WP_User False if there's no old user cookie or it's invalid, WP_User object if it's present and valid.
	 */
	public static function get_old_user() {
		$cookie = wp_get_old_user_cookie();
		if ( !empty( $cookie ) ) {
			if ( $old_user_id = wp_validate_auth_cookie( end( $cookie ), 'old_user' ) ) {
				return get_userdata( $old_user_id );
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Validate the all items of the previously switched user cookie and return all user data.
	 *
	 * @return bool|WP_User False if there's no old user cookie or it's invalid, WP_User object if it's present and valid.
	 */
	public static function get_all_previous_switched_users() {
		$cookie = wp_get_previous_switched_cookie();
		if ( ! empty( $cookie ) ) {
			
			$collect_users = array();

			foreach ( $cookie as $user ) {
				$user_data = get_userdata( wp_validate_auth_cookie( $user, 'switched_users' ) );
				if ( FALSE !== $user_data ) {
					$collect_users[] = get_userdata( wp_validate_auth_cookie( $user, 'switched_users' ) );
				}
			}
			return $collect_users;
		}
		return false;
	}

	/**
	 * Adds a 'Switch back to {user}' link to the account menu in WordPress' admin bar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The admin bar object
	 * @return null
	 */
	public function action_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {

		global $woocommerce;

		if ( !function_exists( 'is_admin_bar_showing' ) )
			return;

		if ( $old_user = self::get_old_user() ) {

			self::build_shopping_as_user_menu( $old_user );

		}
		else {

			self::build_default_old_user_menu();

		}


	}

	/**
	 * Adds a 'Switch back to {user}' link to the WordPress login screen.
	 *
	 * @param string $message The login screen message
	 * @return string The login screen message
	 */
	public function filter_login_message( $message ) {

		if ( $old_user = self::get_old_user() ) {
			$link = sprintf( __( 'Back to %1$s (%2$s)', 'shop-as-customer' ), $old_user->display_name, $old_user->user_login );
			$url = self::switch_back_url();
			if ( isset( $_REQUEST['redirect_to'] ) and !empty( $_REQUEST['redirect_to'] ) ) {
				$url = esc_url_raw( add_query_arg( array(
					'redirect_to' => $_REQUEST['redirect_to']
				), $url ) );
			}
			$message .= '<p class="message"><a href="' . $url . '">' . $link . '</a></p>';
		}

		return $message;

	}

	/**
	 * Adds a 'Switch To' link to each list of user actions on the Users screen.
	 *
	 * @param array   $actions The actions to display for this user row
	 * @param WP_User $user    The user object displayed in this row
	 * @return array The actions to display for this user row
	 */
	public function filter_customer_row_actions( array $actions, WP_User $user ) {

		if ( ! $link = self::maybe_shop_as_url( $user->ID ) )
			return $actions;

		$actions['shop_as_customer'] = '<a href="' . $link . '">' . __( 'Shop&nbsp;As', 'shop-as-customer' ) . '</a>';

		return $actions;
	}

	/**
	 * Helper function. Returns the switch to or switch back URL for a given user ID.
	 *
	 * @param int $user_id The user ID to be switched to.
	 * @return string|bool The required URL, or false if there's no old user or the user doesn't have the required capability.
	 */
	public static function maybe_shop_as_url( $user_id ) {

		$old_user = self::get_old_user();

		if ( $old_user and ( $old_user->ID == $user_id ) )
			return self::switch_back_url();
		else if ( current_user_can( 'shop_as_customer', $user_id ) )
			return self::switch_to_url( $user_id );
		else
			return false;

	}

	/**
	 * Helper function. Returns the nonce-secured URL needed to switch to a given user ID.
	 *
	 * @param int $user_id The user ID to be switched to.
	 * @return string The required URL
	 */
	public static function switch_to_url( $user_id ) {
		return esc_url_raw(
					wp_nonce_url(
						add_query_arg(
							array(
								'action'  => 'shop_as_customer',
								'user_id' => $user_id,
							),
							wp_login_url()
						),
						"shop_as_customer_{$user_id}"
					)
				);
	}

	/**
	 * Helper function. Returns the nonce-secured URL needed to switch back to the originating user.
	 *
	 * @return string The required URL
	 */
	public static function switch_back_url() {
		return esc_url_raw( wp_nonce_url( add_query_arg( array(
			'action' => 'back_to_old_user'
		), wp_login_url() ), 'back_to_old_user' ) );
	}

	/**
	 * Helper function. Returns the current URL.
	 *
	 * @return string The current URL
	 */
	public static function current_url() {
		return ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 * Helper function. Removes a list of common confirmation-style query args from a URL.
	 *
	 * @param string $url A URL
	 * @return string The URL with the listed query args removed
	 */
	public static function remove_query_args( $url ) {
		return esc_url_raw(
					remove_query_arg(
						array(
							'shopping_as_customer',
							'switched_back_user',
							'message',
							'updated',
							'settings-updated',
							'saved',
							'activated',
							'activate',
							'deactivate',
							'locked',
							'skipped',
							'deleted',
							'trashed',
							'untrashed',
							// Cart (new)
							'add-to-cart',
							'remove_item',
							'add-to-cart',
							'added-to-cart',
						),
						$url
					)
				);
	}

	/**
	 * Filter the user's capabilities so they can be added/removed on the fly.
	 *
	 * This is used to grant the 'shop_as_user' capability to a user if they have the ability to edit the user
	 * they're trying to switch to (and that user is not themselves), and to grant the 'switch_off' capability to
	 * a user if they can edit users.
	 *
	 * Important: This does not get called for Super Admins. See filter_map_meta_cap() below.
	 *
	 * @param array $user_caps     User's capabilities
	 * @param array $required_caps Actual required capabilities for the requested capability
	 * @param array $args          Arguments that accompany the requested capability check:
	 *                             [0] => Requested capability from current_user_can()
	 *                             [1] => Current user ID
	 *                             [2] => Optional second parameter from current_user_can()
	 * @return array User's capabilities
	 */
	public function filter_customer_has_cap( array $user_caps, array $required_caps, array $args ) {
		if ( 'shop_as_customer' == $args[0] )
			$user_caps['shop_as_customer'] = ( user_can( $args[1], 'edit_user', $args[2] ) and ( $args[2] != $args[1] ) );

		return $user_caps;
	}

	/**
	 * Filters the actual required capabilities for a given capability or meta capability.
	 *
	 * This is used to add the 'do_not_allow' capability to the list of required capabilities when a super admin
	 * is trying to switch to themselves. It affects nothing else as super admins can do everything by default.
	 *
	 * @param array  $required_caps Actual required capabilities for the requested action
	 * @param string $cap           Capability or meta capability being checked
	 * @param string $user_id       Current user ID
	 * @param array  $args          Arguments that accompany this capability check
	 * @return array Required capabilities for the requested action
	 */
	public function filter_map_meta_cap( array $required_caps, $cap, $user_id, array $args ) {
		if ( ( 'shop_as_customer' == $cap ) and ( $args[0] == $user_id ) )
			$required_caps[] = 'do_not_allow';
		return $required_caps;
	}

	/**
	 * Remove wordpress my-account admin menu options and build shop as user menu
	 *
	 */
	public function build_shopping_as_user_menu( $old_user ) {
		global $woocommerce, $wp_admin_bar;

		$wp_admin_bar->remove_node( "my-account" );

		$old_user_id = $old_user->ID;

		$old_avatar = get_avatar( $old_user_id, 26 );
		$old_howdy  = sprintf( __('Howdy, %1$s', 'shop-as-customer'), $old_user->data->display_name );
		$old_class  = empty( $old_avatar ) ? '' : 'with-avatar';
		$old_profile_url  = get_edit_profile_url( $old_user_id );

		// Add shopping as user menu options to admin menu
		$user_id      = get_current_user_id();
		$current_user = wp_get_current_user();
		$profile_url  = get_edit_profile_url( $user_id );

		if ( ! $user_id )
			return;

		$avatar = get_avatar( $user_id, 26 );
		$shopping_as  = sprintf( __('Shopping as %1$s', 'shop-as-customer'), $current_user->display_name );
		$class  = empty( $avatar ) ? '' : 'with-avatar';

		$top_heading_shop_as_user = '<span class="top-howdy top-howdy-main">';
		$top_heading_shop_as_user .= $old_howdy . $old_avatar;
		$top_heading_shop_as_user .= '</span>';
		$top_heading_shop_as_user .= '<span class="top-howdy top-howdy-secondry">';
		$top_heading_shop_as_user .= $shopping_as . $avatar;
		$top_heading_shop_as_user .= '</span>';

		$wp_admin_bar->add_menu( array(
			'id'        => 'my-account',
			'parent'    => 'top-secondary',
			'title'     => $top_heading_shop_as_user,
			'href'      => $profile_url,
			'meta'      => array(
				'class'     => "shopping-as-user ".$class,
				'title'     => $shopping_as,
			),
		) );

		$wp_admin_bar->remove_node('user-actions');

		$wp_admin_bar->add_group( array(
			'parent'	=> 'my-account',
			'id'		=> 'shopping-as-actions',
			'meta'		=> array(
				'class'		=> 'shop-as-user-profile-menu shop-as-user-profile-menu-second-user'
			)
		));

		$wp_admin_bar->add_menu( array(
			'parent' => 'shopping-as-actions',
			'id'     => 'main-avatar',
			'title'  => get_avatar( $user_id, 64 )
		));

		$wp_admin_bar->add_menu( array(
			'parent' => 'shopping-as-actions',
			'id'     => 'sac-heading-h3',
			'title'  => __( 'Shopping as', 'shop-as-customer' ),
			'meta'   => array(
				'tabindex'	=> -1
			)
		));

		/*
		$user_details = "";
		$user_details .= $current_user->display_name;
		if ( $current_user->display_name !== $current_user->user_login )
			$user_details .= $current_user->user_login;
		*/


		/* Compile new user display details */
		$new_user_name = $current_user->display_name;
		$new_user_email = $current_user->user_email;

		$new_user_details = sprintf(__('
			<div class="shopping-as-details-holder">
				<div class="shopping-as-name">%1$s</div>
				<div class="shopping-as-email">%2$s</div>
			</div>
		','shop-as-customer'), $new_user_name, $new_user_email);


		/* Compile old user display details */
		$old_user_name = $old_user->display_name;
		$old_user_email = $old_user->user_email;

		$old_user_details = sprintf( __( '<div class="email-holder">Back to %1$s</div>', 'shop-as-customer' ), $old_user_name);


		$wp_admin_bar->add_menu( array(
			'parent' => 'shopping-as-actions',
			'id'     => 'sac-user-info',
			'title'  => $new_user_details,
			'meta'   => array(
				'tabindex' => -1,
			),
		));
		$wp_admin_bar->add_menu( array(
			'parent' => "shopping-as-actions",
			'id'     => 'sac-back-to-original-user',
			'title'  => $old_user_details,
			'href'   => esc_url_raw( add_query_arg( array(
				'redirect_to' => urlencode( self::current_url() )
			), self::switch_back_url() ) )
		));
	}

	/**
	 * Add admin bar menu for the original user.
	 */
	public function build_default_old_user_menu() {
		
		global $woocommerce, $wp_admin_bar;
		
		// Get the past swicthed user list.
		$all_switched_users = false;
		$switched_users_html = '';
		
		if ( ( $all_switched_users = self::get_all_previous_switched_users() ) && ( ! self::get_old_user() ) ) {
			
			// Reverse the order of past user list.
			$all_switched_users = array_reverse( $all_switched_users, true );
			
			$switched_users_html .= "<div class='previous-switched-users'>";
			foreach ( $all_switched_users as $swited_user ) {
				$link = self::switch_to_url( $swited_user->ID );
				$orders_link = admin_url( 'edit.php?post_status=all&post_type=shop_order&action=-1&shop_order_status&_customer_user=' . absint( $swited_user->ID ) . '' );
				$user_link = esc_url_raw( network_admin_url( 'user-edit.php?user_id=' . $swited_user->ID ) );

				$switched_users_html .= '<div class="previous-switched-user-group">';
				$switched_users_html .= '	<div class="previous-switched-user-name">'. $swited_user->data->display_name . '</div>';
				$switched_users_html .= '	<a class="previous-switched-user-link" href="'. $link . '">Switch to</a>';
				$switched_users_html .= '	<span class="previous-switched-user-link-divider">|</span>';
				$switched_users_html .= '	<a class="previous-switched-user-link" href="'. $orders_link . '">View Orders</a>';
				$switched_users_html .= '	<span class="previous-switched-user-link-divider">|</span>';
				$switched_users_html .= '	<a class="previous-switched-user-link" href="'. $user_link . '">Edit Profile</a>';
				$switched_users_html .= '</div>';
			}
			$switched_users_html .= "</div>";
		}

		$wp_admin_bar->add_group( array(
			'parent'	=> "my-account",
			'id'		=> 'shop-as-customer',
			'meta'		=> array(
				'class'		=> 'shop-as-user-profile-menu shop-as-user-profile-main-user'
			)
		));


		$wp_admin_bar->add_menu( array(
			'parent'	=> "shop-as-customer",
			'id'     => 'sac-heading-h3',
			'title'  => __( "Shop as Customer", "shop-as-customer" ),
			'meta'   => array(
				'tabindex'	=> -1
			)
		));
		$wp_admin_bar->add_menu( array(
			'parent'	=> "shop-as-customer",
			'id'		=> 'search-users',
			'href'		=> false,
			'title'		=> '
				<select id="shop_as_user_search_users" name="shop_as_user_search_users" class="ajax_chosen_shop_as_user_search_users">
					<option value="">'.__( 'Find a Customer...', 'shop-as-customer' ).'</option>
				</select>
				<div class="searched-switch-links"></div>
				<div class="shop-as-customer-switch-button"></div>
				'
		));

		//Show Recent logins, if there are any
		if( $all_switched_users ){
			$wp_admin_bar->add_menu( array(
				'parent'	=> "shop-as-customer",
				'id'		=> 'sac-heading-h4',
				'title'		=> __( "Recent", "shop-as-customer" )
			));
			$wp_admin_bar->add_menu( array(
				'parent'	=> "shop-as-customer",
				'id'     => 'sac-recent-users',
				'title'  => $switched_users_html,
				'meta' => array(
						"class" => "sac-hover-links"
					)
			));
		}

	}

	/**
	 * Render the admin page
	 */
	public function admin_page() {

		global $woocommerce;

		$action = 'admin.php?page=woocommerce_shop_as_customer';
		?>
		<div class="wrap woocommerce woocommerce-shop-as-user-wrap">

			<h2><?php _e( 'Shop as Customer', 'shop-as-customer' ); ?></h2>

			<form id="shop-as-user-form" method="post" action="<?php echo esc_attr( $action ); ?>">
				<?php
				if ( ! empty( $_POST ) ) {
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been successfully saved.', 'shop-as-customer' ) . '</strong></p></div>';
				}
				$this->admin_form();
				?>
				<?php wp_nonce_field('shop-as-customer', 'search-customers'); ?>
			</form>

		</div>
		<?php

	}

	/**
	 * Render the body of the admin starting page
	 */
	private function admin_form() {
		
		global $woocommerce;
		?>
		<div id="woocommerce-order-items" class="postbox " >
			<div class="inside">
				<table class="settings-table">
					<tbody>

						<tr>
							<td class="label">
								<label><?php _e( 'User Role', 'shop-as-customer' ); ?></label>
								<p class="description"><?php _e( 'Which users can shop as other users', 'shop-as-customer' ); ?></p>
							</td>
							<td>
								<div class="form-field">
									<?php $shop_as_user_role = get_option( 'shop_as_user_role', 'shop_manager' ); ?>
									<select name='shop_as_user_role' >
										<option value='shop_manager' <?php selected( $shop_as_user_role, 'shop_manager', TRUE ); ?> >
											<?php _e( 'Shop Manager', 'shop-as-customer' ); ?>
										</option>
										<option value='administrator' <?php selected( $shop_as_user_role, 'administrator', TRUE ); ?> >
											<?php _e( 'Administrator', 'shop-as-customer' ); ?>
										</option>
									</select>
								</div>
							</td>
						</tr>

						<tr>
							<td class="label">
								<p class="description"></p>
							</td>
							<td>
								<input type="submit" class="button button-primary submit-button" name="submit" id="submit" value="<?php _e( 'Save Settings', 'shop-as-customer' ); ?>" />
							</td>
						</tr>

					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Save Options
	 */
	function shop_as_customer_save_options(){
		global $woocommerce;

		if ( isset( $_POST["shop_as_user_role"] ) ) {
			if ( isset( $_POST['search-customers'] ) && wp_verify_nonce( $_POST['search-customers'], 'shop-as-customer' ) ) {
				$shop_as_user_role = $_POST["shop_as_user_role"];
				update_option( 'shop_as_user_role', $shop_as_user_role );
			}
		}
	}

	public function shop_as_customer_order_notice() {
		global $post;
		$screen = get_current_screen();

		if ( ! isset( $post ) ) {
			return;
		}

		$created_by = get_post_meta( $post->ID, 'create_by', true );

		if ( empty( $created_by ) ) {
			return;
		}

		if ( ! isset( $screen->id ) ) {
			return;
		}

		if ( 'shop_as_customer' === $created_by && 'shop_order' === $screen->id ) {
			printf ( '
				<div id="shop-as-customer-message" class="updated notice notice-success below-h2">
					<p>%s</p>
				</div>',
				__( 'Order created using <strong>Shop as Customer</strong>', 'shop-as-customer' )
			);

		}
	}

	/**
	 * Test a users capability
	 */
	public static function test_user_role( $role ) {

		$capability = "manage_options";
		//$capability = "read";

		switch ($role) {
			case 'shop_manager':
				$capability = "manage_woocommerce";
				break;
			case 'administrator':
				$capability = "manage_options";
				break;
		}

		// Get current user, or the original user if in swicthed state
		if ( $old_user = self::get_old_user() ) {
			$user_id = $old_user->ID;
		}
		else {
			$user_id = get_current_user_id();
		}

		return user_can( $user_id, $capability );

	}

	/**
	 * Search for customers and return json
	 *
	 * @access public
	 * @return void
	 */
	function woocommerce_json_shop_as_customers_search() {

		check_ajax_referer( 'search-customers', 'security' );

		$user_id = get_current_user_id();

		header( 'Content-Type: application/json; charset=utf-8' );

		$term = woocommerce_clean( urldecode( stripslashes( $_GET['term'] ) ) );

		if ( empty( $term ) )
			die();

		$default = isset( $_GET['default'] ) ? $_GET['default'] : __( 'Find a Customer...', 'shop-as-customer' );

		$found_customers = array( '' => $default );

		add_action( 'pre_user_query', array( $this, 'json_search_customer_name' ) );

		$customers_query = new WP_User_Query( array(
			'fields'			=> 'all',
			'orderby'			=> 'display_name',
			'search'			=> '*' . $term . '*',
			'search_columns'	=> array( 'ID', 'user_login', 'user_email', 'user_nicename' )
		) );

		remove_action( 'pre_user_query', array( $this, 'json_search_customer_name' ) );

		$customers = $customers_query->get_results();

		if ( $customers ) {
			foreach ( $customers as $customer ) {
				if ( $user_id != $customer->ID ) {
					$link = self::switch_to_url( $customer->ID );

					$found_customers[] = array( "id" => $customer->ID, "label" => $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . sanitize_email( $customer->user_email ) . ')', "link" => $link );
				}
			}
		}

		echo json_encode( $found_customers );




		die();
	}

	/**
	 * When searching using the WP_User_Query, search names (user meta) too
	 * @param  object $query
	 * @return object
	 */
	public function json_search_customer_name( $query ) {
		global $wpdb;

		$term = ( function_exists( 'wc_clean' ) ) ? wc_clean( stripslashes( $_GET['term'] ) ) : sanitize_text_field( stripslashes( $_GET['term'] ) );
		if ( method_exists( $wpdb, 'esc_like' ) ) {
			$term = $wpdb->esc_like( $term );
		}
		else {
			$term = like_escape( $term );
		}

		$query->query_from  .= " INNER JOIN {$wpdb->usermeta} AS user_name ON {$wpdb->users}.ID = user_name.user_id AND ( user_name.meta_key = 'first_name' OR user_name.meta_key = 'last_name' ) ";
		$query->query_where .= $wpdb->prepare( " OR user_name.meta_value LIKE %s ", '%' . $term . '%' );
	}
	
	/**
	 * Is WooCommerce active.
	 */
	public static function is_woocommerce_active() {
		
		$active_plugins = (array) get_option( 'active_plugins', array() );
		
		if ( is_multisite() )
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		
		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}

	/**
	 * Display Notifications on specific criteria.
	 *
	 * @since	2.14
	 */
	public static function woocommerce_inactive_notice() {
		if ( current_user_can( 'activate_plugins' ) ) :
			if ( !class_exists( 'WooCommerce' ) ) :
				?>
				<div id="message" class="error">
					<p>
						<?php
						printf(
							__( '%sShop as Customer for WooCommerce needs WooCommerce%s %sWooCommerce%s must be active for Shop as Customer to work. Please install & activate WooCommerce.', 'shop-as-customer' ),
							'<strong>',
							'</strong><br>',
							'<a href="http://wordpress.org/extend/plugins/woocommerce/" target="_blank" >',
							'</a>'
						);
						?>
					</p>
				</div>
				<?php
			elseif ( version_compare( get_option( 'woocommerce_db_version' ), WC_SHOP_AS_CUSTOMER_REQUIRED_WOOCOMMERCE_VERSION, '<' ) ) :
				?>
				<div id="message" class="error">
					<!--<p style="float: right; color: #9A9A9A; font-size: 13px; font-style: italic;">For more information <a href="http://cxthemes.com/plugins/update-notice.html" target="_blank" style="color: inheret;">click here</a></p>-->
					<p>
						<?php
						printf(
							__( '%sShop as Customer for WooCommerce is inactive%s This version of Shop as Customer requires WooCommerce %s or newer. For more information about our WooCommerce version support %sclick here%s.', 'shop-as-customer' ),
							'<strong>',
							'</strong><br>',
							WC_SHOP_AS_CUSTOMER_REQUIRED_WOOCOMMERCE_VERSION,
							'<a href="https://helpcx.zendesk.com/hc/en-us/articles/202241041/" target="_blank" style="color: inheret;" >',
							'</a>'
						);
						?>
					</p>
					<div style="clear:both;"></div>
				</div>
				<?php
			endif;
		endif;
	}

}
