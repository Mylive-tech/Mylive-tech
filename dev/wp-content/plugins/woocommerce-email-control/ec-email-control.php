<?php
/**
 * Plugin Name: WooCommerce Email Customizer
 * Description: WooCommerce Email Customizer plugin allows you to fully customize the styling, colors, logo and text in the emails sent from your WooCommerce store.
 * Author: cxThemes
 * Author URI: http://codecanyon.net/user/cxThemes
 * Plugin URI: http://codecanyon.net/item/email-customizer-for-woocommerce/8654473
 * Version: 2.31
 * Text Domain: email-control
 * Domain Path: /languages/
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    cxThemes
 * @category  WooCommerce, WordPress
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define Constants
 */
define( 'WC_EMAIL_CONTROL_VERSION', '2.31' );
define( 'WC_EMAIL_CONTROL_REQUIRED_WOOCOMMERCE_VERSION', 2.2 );
define( 'WC_EMAIL_CONTROL_DIR' , plugin_dir_path( __FILE__ ) );
define( 'WC_EMAIL_CONTROL_URI' , plugin_dir_url( __FILE__ ) );

/**
 * Update Check
 */
require 'plugin-updates/plugin-update-checker.php';
$wc_email_control_update = new PluginUpdateChecker(
	'http://cxthemes.com/plugins/woocommerce-email-control/email-control.json',
	__FILE__,
	'email-control'
);

/**
 * Check if WooCommerce is active, and is required WooCommerce version.
 */
if ( ! WC_Email_Control::is_woocommerce_active() || version_compare( get_option( 'woocommerce_version' ), WC_EMAIL_CONTROL_REQUIRED_WOOCOMMERCE_VERSION, '<' ) ){
	add_action( 'admin_notices', array( 'WC_Email_Control', 'woocommerce_inactive_notice' ) );
	return;
}
	
/**
 * Includes
 */
include_once( 'includes/ec-woo-back-compat-functions.php' );
include_once( 'includes/ec-functions.php' );
include_once( 'includes/class-ec-settings.php' );
include_once( 'ec-template-deluxe.php' ); // Template
include_once( 'ec-template-supreme.php' ); // Template

/**
 * Instantiate plugin.
 */
$wc_email_control = WC_Email_Control::get_instance();

/**
 * Main Class.
 */
class WC_Email_Control {
	
	private $id = 'woocommerce_email_control';
	
	private static $instance;
	
	/**
	* Get Instance creates a singleton class that's cached to stop duplicate instances
	*/
	public static function get_instance() {
		if ( !self::$instance ) {
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
	public function init() {
		
		// Translations
		add_action( 'init', array( $this, 'load_translation' ) );
		
		// Enqueue Scripts/Styles - in head of admin page
		add_action( 'admin_enqueue_scripts', array( $this, 'ec_head_scripts' ) );
		
		// Enqueue Scripts/Styles - in head of email template page
		add_action( 'ec_render_template_head_scripts', array( $this, 'ec_head_scripts' ), 102 );
		
		// Add menu item
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
		// Ajax saving of options
		add_action( 'wp_ajax_save_meta', array( $this, 'save_meta' ) );
		add_action( 'wp_ajax_nopriv_save_meta', array( $this, 'nopriv_save_meta' ) );
		
		// Ajax saving of options new
		add_action( 'wp_ajax_save_option',	 array( $this, 'save_option' ) );
		add_action( 'wp_ajax_nopriv_save_option',	 array( $this, 'nopriv_save_option' ) );
		
		// Ajax send email
		add_action( 'wp_ajax_ec_send_email',	 array( $this, 'send_email' ) );
		add_action( 'wp_ajax_nopriv_ec_send_email',	 array( $this, 'nopriv_send_email' ) );
		
		// Ajax saving of all edit settings
		add_action( 'wp_ajax_save_edit_email', array( $this, 'save_edit_email' ) );
		add_action( 'wp_ajax_nopriv_save_edit_email', array( $this, 'nopriv_save_edit_email' ) );
		
		// WooCommerce order page meta boxe
		add_action( 'add_meta_boxes', array( $this, 'order_page_meta_box' ), 35 );
		
		// Register email templates.
		do_action( 'register_email_template' );
		
		// Check Templates
		add_filter( 'wc_get_template', array( $this, 'ec_get_template' ), 10, 5 ); // > WC 2.2 (added this filter recently so can't use until more regular support)
		
		//Email Customizer - Admin and Template pages only
		if ( isset($_REQUEST["page"]) && $_REQUEST["page"] == $this->id ) {
			
			// Remove all notifications
			remove_all_actions( 'admin_notices' );
			
			// Remove admin bar
			require_once( 'includes/toolbar-removal/wp-toolbar-removal.php');
						
			if ( !isset( $_REQUEST["ec_render_email"] ) ) {
				
				//Email Customizer - Admin Page only
				add_action( 'in_admin_header', array( $this, 'ec_render_admin_page' ) );
			}
			else {
				
				//Email Customizer - Template page only
				add_filter( 'wp_print_scripts', array( $this, 'deregister_all_scripts' ), 101 );
				add_action( 'wp_print_scripts', array( $this, 'ec_head_scripts' ), 102 );
				add_action( 'admin_init', array( $this, 'ec_render_template_page' ) );
			}
		}
		
		// Admin boody class for when in popup from eg WC order page
		add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ) );
		
		// Add Button in WooCommerce->Settings->Email
		add_action( 'woocommerce_settings_tabs_email', array( $this, 'woocommerce_settings_button' ) );
		
		// Setup global template args.
		add_action( 'woocommerce_before_template_part', array( $this, 'ec_before_template_setup_args_global' ) , 10, 4 );
		add_action( 'woocommerce_after_template_part', array( $this, 'ec_after_template_setup_args_global' ) , 10, 4 );
		
		// Setup options filtering.
		add_action( 'woocommerce_before_template_part', array( $this, 'ec_before_template_filter_options' ) , 10, 4 );
		
		// Modify email headers.
		add_action( 'woocommerce_email_headers', array( $this, 'ec_email_headers' ) );
		
		// Other simpler WooCommerce emails - Content.
		add_filter( 'woocommerce_email_content_low_stock', array( $this, 'woocommerce_simple_email_content' ), 10, 2 );
		add_filter( 'woocommerce_email_content_no_stock', array( $this, 'woocommerce_simple_email_content' ), 10, 2 );
		add_filter( 'woocommerce_email_content_backorder', array( $this, 'woocommerce_simple_email_content' ), 10, 2 );
		// Other simpler WooCommerce emails - Headers.
		add_filter( 'woocommerce_email_headers', array( $this, 'woocommerce_simple_email_headers' ), 10, 2 );
		add_filter( 'woocommerce_email_headers', array( $this, 'woocommerce_simple_email_headers' ), 10, 2 );
		add_filter( 'woocommerce_email_headers', array( $this, 'woocommerce_simple_email_headers' ), 10, 2 );
	}
	
	/**
	 * Body classes on admin page when in popup - e.g. wc order page
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 *
	 * @param 	string $classes CSS class names passed by the wp filter
	 * @return	string Concatenated string of css class names
	 */
	function admin_body_classes($classes) {
		
		if ( isset($_REQUEST["ec_in_popup"]) )
			$classes .= "pe-in-popup ";
			
		return $classes;
	}
	
	/**
	 * Dergister all scripts & styles
	 *
	 * Deregister all scripts so the email template preview is
	 * css clean and free of other plugins js bugs
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 */
	function deregister_all_scripts() {
		
		global $wp_scripts,  $wp_styles;
		
		// Dequeue All Scripts
		if (false != $wp_scripts->queue) {
			foreach($wp_scripts->queue as $script) {
				$wp_scripts->dequeue( $script );
				
				// if (isset($wp_scripts->registered[$script])) {
				// 	$wp_scripts->registered[$script]->deps = array();
				// }
			}
		}
		
		// Dequeue All Styles
		if (false != $wp_styles->queue) {
			foreach($wp_styles->queue as $script) {
				$wp_styles->dequeue( $script );
				
				// if (isset($wp_styles->registered[$script])) {
				// 	$wp_styles->registered[$script]->deps = array();
				// }
			}
		}
	}
	
	/**
	 * Enqueue CSS and Scripts
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 */
	public function ec_head_scripts() {
		
		global $woocommerce, $wp_scripts, $current_screen;
		
		// All Pages
		wp_register_style( 'woocommerce_admin', $woocommerce->plugin_url() . '/assets/css/admin.css' );
		wp_enqueue_style( 'woocommerce_admin' );
		
		wp_enqueue_script( 'woocommerce_admin' );
		
		wp_register_script( 'jquery-tiptip', $woocommerce->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js', array('jquery') );
		wp_enqueue_script( 'jquery-tiptip' );
		
		// Email Customizer - Admin page only
		if 	(
				( isset($_REQUEST["page"]) && $_REQUEST["page"] == $this->id)
				||
				( isset($_REQUEST["page"]) && $_REQUEST["page"] == "wc-settings")
				||
				( isset($_REQUEST["ec_render_email"]) )
				||
				( isset($current_screen->id) && $current_screen->id == "shop_order")
			) {
			
			
			// For image uplaoder on settings page_link
			wp_enqueue_media();
			
			
			// Magnificent Popup
			wp_register_script( 'magnificent-popup', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/assets/js/magnificent-popup/magnificent.js', basename( __FILE__ ) ), array('jquery'), WC_EMAIL_CONTROL_VERSION );
			wp_enqueue_script( 'magnificent-popup' );
			wp_register_style( 'magnificent-popup', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/assets/js/magnificent-popup/magnificent.css', basename( __FILE__ ) ), '', WC_EMAIL_CONTROL_VERSION, 'screen' );
			wp_enqueue_style( 'magnificent-popup' );
			
			// Notification Systsem
			wp_register_style( 'cx-notification', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/assets/css/notification.css', basename( __FILE__ ) ), '', WC_EMAIL_CONTROL_VERSION, 'screen' );
			wp_enqueue_style( 'cx-notification' );
			
			// Font Awesome
			wp_register_style( 'fontawesome', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/assets/css/fontawesome/css/font-awesome.min.css', basename( __FILE__ ) ), '', WC_EMAIL_CONTROL_VERSION, 'screen' );
			wp_enqueue_style( 'fontawesome' );
			
			// Email Customizer Custom Scripts
			wp_register_style( 'email-control', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/assets/css/email-control-back-end.css', basename( __FILE__ ) ), '', WC_EMAIL_CONTROL_VERSION, 'screen' );
			wp_enqueue_style( 'email-control' );
			wp_register_script( 'email-control', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/assets/js/email-control-back-end.js', basename( __FILE__ ) ), array('jquery', 'jquery-tiptip', 'iris'), WC_EMAIL_CONTROL_VERSION );
			wp_enqueue_script( 'email-control' );
			wp_localize_script('email-control', 'woocommerce_email_control', array(
				'home_url' => get_home_url(),
				'admin_url' => admin_url(),
				'ajaxurl' => admin_url('admin-ajax.php')
			));
			
			// Open Sans - incase it has not yet
			wp_enqueue_style( 'open-sans' );
			
		}
		
		wp_enqueue_style( 'open-sans' );
		
		// Email Customizer - Template page only
		if ( ( isset($_REQUEST["page"]) && $_REQUEST["page"] == $this->id ) && isset( $_REQUEST["ec_render_email"] ) ) {
			
			// Load jQuery
			wp_enqueue_script( 'jquery' );
			
			// Load Dashicons
			wp_enqueue_style( 'dashicons' );
			
			// Email Customizer Custom Scripts
			wp_register_style( 'email-control', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/assets/css/email-control-back-end.css', basename( __FILE__ ) ), '', WC_EMAIL_CONTROL_VERSION, 'screen' );
			wp_enqueue_style( 'email-control' );
			
		}
	}
	
	/**
	 * Localization
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 */
	public static function load_translation() {
		
		// Domain ID - used in eg __( 'Text', 'email-control' )
		$domain = 'email-control';
		
		// get the languages locale eg 'en_US'
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		
		// Look for languages here: wp-content/languages/pluginname/pluginname-en_US.mo
		//load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' ); // Removed this location.
		
		// Look for languages here: wp-content/languages/pluginname/pluginname-en_US.mo
		load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo' );
		
		// Look for languages here: wp-content/languages/pluginname-en_US.mo
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '-' . $locale . '.mo' );
		
		// Look for languages here: wp-content/plugins/pluginname/languages/pluginname-en_US.mo
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	/*
	*  Save option
	*
	*  @date	20-08-2014
	*  @since	1.0
	*/
	function save_meta() {

		/*
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "save_meta_nonce")) {
		  exit("No naughty business please");
		}
		*/
		
		global $current_user;
		get_currentuserinfo();
		
		$field_name  = (isset($_REQUEST["field_name"])) ? $_REQUEST["field_name"] : "" ;
		$field_value  = (isset($_REQUEST["field_value"])) ? $_REQUEST["field_value"] : "" ;

		if ( strpos($field_name, "userspecifc") ) {
			//Save the option specific to the current user
			update_user_meta( $current_user->ID, $field_name, $field_value );
		}
		else {
			//Save the option to the global options
			update_option( $field_name, $field_value );
		}
		
		die();
	}
	
	function nopriv_save_meta() {
		_e('You must be logged in','email-control');
		die();
	}
	
	/*
	*  Save option new
	*
	*  @date	20-08-2014
	*  @since	1.0
	*/
	function save_option() {
		/*
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "save_option_nonce")) {
		  exit("No naughty business please");
		}
		*/
		
		$field_name  = (isset($_REQUEST["field_name"])) ? $_REQUEST["field_name"] : "" ;
		$field_value  = (isset($_REQUEST["field_value"])) ? $_REQUEST["field_value"] : "" ;
		
		update_option( $field_name, $field_value );
		
		die();
	}
	
	function nopriv_save_option() {
		_e('You must be logged in', 'email-control');
		die();
	}
	
	/*
	*  Ajax send email
	*
	*  @date	20-08-2014
	*  @since	1.0
	*/
	public function send_email() {
		
		global $order, $woocommerce;
		
		$email_type			= $_REQUEST['ec_email_type'];
		$email_order		= $_REQUEST['ec_email_order'];
		//$email_addresses	= $_REQUEST['ec_email_addresses'];
		$email_template_id	= $_REQUEST['ec_email_template'];
		
		// Handle button actions
		if ( !empty( $_REQUEST['ec_email_type'] ) ) {

			// Load mailer
			$mailer = $woocommerce->mailer();
			$mails = $mailer->get_emails();
			
			// Ensure gateways are loaded in case they need to insert data into the emails
			$woocommerce->payment_gateways();
			$woocommerce->shipping();
			
			$email_to_send = wc_clean( $_REQUEST['ec_email_type'] );

			if ( !empty( $mails ) ) {
				foreach ( $mails as $mail ) {
					if ( $mail->id == $email_to_send ) {
						
						// Old method - used our own Sedning function
						//$this->trigger_send_email( $order->id, $mail, $email_addresses);
						
						// New method - filters the recicpeint address and used the respective mails own sending function to send
						add_filter( 'woocommerce_email_recipient_' . $mail->id, array( $this, 'woocommerce_email_recipient' ) );
						$mail->trigger( $email_order );
					}
				}
			}
				
		}
		
		die();
	}
	
	function nopriv_send_email() {
		_e('You must be logged in', 'email-control');
		die();
	}
	
	/*
	* Filter used to modify the receivers email address so tester can specify their own.
	 */
	function woocommerce_email_recipient () {
		if ( isset( $_REQUEST['ec_email_addresses'] ) ) {
			return $_REQUEST['ec_email_addresses'];
		}
	}
	
	/*
	*  Ajax send email
	*
	*  @date	20-08-2014
	*  @since	1.0
	*/
	function trigger_send_email( $order, $mail, $email_addresses) {
		
		$email_addresses = str_replace( " ", "", $email_addresses );
		$email_addresses = explode( ",", $email_addresses );
		
		$mail->object		= new WC_Order($order);
		
		
		$mail->find[] = '{order_date}';
		$mail->replace[] = date_i18n( wc_date_format(), strtotime( $mail->object->order_date ) );

		$mail->find[] = '{order_number}';
		$mail->replace[] = $mail->object->get_order_number();
		
		
		$mail->recipient	= $mail->object->billing_email;
		
		$email_subject		= $mail->get_subject();
		$email_content		= $mail->get_content();
		$email_headers		= $mail->get_headers();
		$email_attachments	= $mail->get_attachments();
		
		
		foreach ($email_addresses as $email_address) {
			echo $mail->send( $email_address, $email_subject, $email_content, $email_headers, $email_attachments );
		}
		
		die();
	}
	
	/*
	*  Save all edit template options.
	*
	*  @date	20-08-2014
	*  @since	1.0
	*/
	public function save_edit_email() {
				
		$email_type		= ($_REQUEST['ec_email_type']) ? $_REQUEST['ec_email_type'] : false ;
		$email_id		= ($_REQUEST['ec_email_id']) ? $_REQUEST['ec_email_id'] : false ;
		
		$settings = ec_get_settings($email_id);
		
		EC_Settings::save_fields( $settings );
		
		die();
	}
	
	function nopriv_save_edit_email() {
		_e('You must be logged in', 'email-control');
		die();
	}
	
	/**
	 * WC order page meta box
	 */
	public function order_page_meta_box() {
		
		add_meta_box(
			'woocommerce-order-actions-new',
			__( 'Email Customizer', 'email-control' ),
			array($this, 'order_meta_box'),
			'shop_order',
			'side',
			'high'
		);
	}
	
	/**
	 * WC order page meta box
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 *
	 * @param object $post The order post
	 */
	public function order_meta_box( $post ) {
		global $woocommerce, $theorder, $wpdb;

		if ( !is_object( $theorder ) )
			$theorder = new WC_Order( $post->ID );

		$order = $theorder;
		?>
		
		<div class="ec_order_page_ui">
			
			<div class="ec_actions_dropdown help_tip_new"  data-tip="<?php _e( 'Choose which email to preview or send.', 'email-control' ); ?>" >
				
				<?php do_action( 'woocommerce_order_actions_start', $post->ID ); ?>
				
				<select name="ec_order_action" id="ec_order_action">
					<option value=""><?php _e( 'Emails', 'email-control' ); ?></option>
					
					<?php
					// Load mailer
					if ( class_exists('WC') ) {
						$mailer = WC()->mailer();
						$mails = $mailer->get_emails();
						
						// Ensure gateways are loaded in case they need to insert data into the emails
						WC()->payment_gateways();
						WC()->shipping();
						
					}
					else{
						$mailer = $woocommerce->mailer();
						$mails = $mailer->get_emails();
						
						// Ensure gateways are loaded in case they need to insert data into the emails
						$woocommerce->payment_gateways();
						$woocommerce->shipping();
					}
										
					if ( !empty( $mails ) ) {
						foreach ( $mails as $mail ) {
							?>
							<option value="send_email_<?php echo esc_attr( $mail->id ) ?>">
								<?php echo esc_html( $mail->title ) ?>
							</option>
							<?php
						}
					}
					?>
				</select>
				
			</div>
			<div class="ec_actions_buttons">
				
				<!-- Buttons Row -->
				<a class="button help_tip_new" id="preview-email-button" data-tip="<?php _e( "Preview the email selected above.", 'email-control' ); ?>" target="_blank" ><?php _e( 'Preview Email', 'email-control' ); ?></a>
				<a class="button help_tip_new" id="send-email" data-tip="<?php _e( "Send the email selected above to this customer's billing address email. Will default to 'New Order' email if nothing is selected.", 'email-control' ); ?>" target="_blank" ><?php _e( 'Send Email', 'email-control' ); ?></a>
				<!-- /Buttons Row -->
				
			</div>
			
		</div>
		
		<?php
	}
	
	/**
	 * Render admin page.
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 */
	public function ec_render_admin_page() {
		
		require_once( 'pages/ec-admin-page.php');
	}
	
	/**
	 * Render template page.
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 */
	public function ec_render_template_page() {
		
		require_once( 'pages/ec-template-page.php');
	}
	
	/**
	 * Add a submenu item to the WooCommerce menu
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 */
	public function admin_menu() {
		
		/*
		global $submenu;
		$submenu['woocommerce'][500] = array(
			__( 'Email Customizer', 'email-control' ),
			'manage_woocommerce',
			admin_url( "?ec_show_interface=true" )
		);
		*/
		
		add_submenu_page(
			'woocommerce',
			__('Email Customizer', 'email-control'),
			__('Email Customizer', 'email-control'),
			'manage_woocommerce',
			$this->id,
			array( $this, 'ec_render_admin_page' )
		);
	}
	
	/**
	 * Add info and button to WooCommerce->settings->email page
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 */
	function woocommerce_settings_button($data) {
		
		global $woocommerce, $wp_scripts, $current_screen;
		
		$ec_url = "";
		$ec_url .= admin_url();
		$ec_url .= "admin.php";
		$ec_url .= "?";
		$ec_url .= "page=woocommerce_email_control";
		
		if ( isset($_REQUEST["section"]) ) {
			
			if ( class_exists('WC') ) {
				$mailer = WC()->mailer();
				$mails = $mailer->get_emails();
			}
			else{
				$mailer = $woocommerce->mailer();
				$mails = $mailer->get_emails();
			}
			
			if ( !empty($mails) ) {
				foreach ( $mails as $mail ) {
					$template = str_replace("wc_email_", "", $_REQUEST["section"] );
					if ( $mail->id == $template ) {
						$ec_url .= "&ec_email_type=" . $template;
					}
				}
			}
		}
		
		?>
		<div class="pe-wc-settings-holder">
			
			<?php if ( isset($_REQUEST["section"]) && $_REQUEST["section"] != "" ) { ?>
				
				<!-- Inner Tabs -->
				<h4>Email Customizer</h4>
				<p>
					<a class="button ec" href="<?php echo $ec_url ?>" target="preview_email">Preview Email</a>
					<?php _e("Preview and test emails as they will appear in mail clients when received.", "email-control") ?>
				</p>
				
			<?php } else { ?>
			
				<!-- First Tab -->
				<h3>Email Customizer</h3>
				<p>
					<a class="button ec" href="<?php echo $ec_url ?>" target="preview_email">Preview Email</a>
					<?php _e("Preview and test emails as they will appear in mail clients when received.", "email-control") ?>
				</p>
				
			<?php } ?>
			
		</div>
		<?php
	}
	
	/**
	 * Check for and return our template.
	 *
	 * WC 2.2 and above - added this filter recently so can't use until more regular support
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 *
	 * @return string	Template file location
	 */
	function ec_get_template( $located, $template_name, $args, $template_path, $default_path ) {
		
		// echo "<br>";
		// echo "get_template";
		// echo "<br>";
		// echo "<br>";
		// echo 'located--------------------: ' . $located;
		// echo "<br>";
		// echo 'template_name----------: ' . $template_name;
		// echo "<br>";
		// echo "args------------------------: ";
		// print_r( $args );
		// echo "<br>";
		// echo 'template_path-----------: ' . $template_path;
		// echo "<br>";
		// echo 'default_path-------------: ' . $default_path;
		// echo "<br><br><br><br>";
		
		if ( FALSE !== strrpos( $template_name, 'emails' ) ) {
			
			global $ec_email_templates, $woocommerce;
			
			// Use the selected template from database
			$ec_template_selected = get_option("ec_template");
			
			// Overide selected template with that passed by preview
			if ( isset( $_REQUEST["ec_email_template_preview"] ) ) $ec_template_selected = $_REQUEST["ec_email_template_preview"];
			
			$this_template = false;
			
			if ( is_array( $ec_email_templates ) && isset( $ec_template_selected ) && $ec_template_selected !== false ) {
				if ( array_key_exists( $ec_template_selected, $ec_email_templates ) ) {
					
					// It's one of our templates so do custom filters
					add_filter( 'woocommerce_email_custom_details_header', '__return_empty_string' ); // Remove the header of the customer details.
					
					$this_plugin_path		= trailingslashit( $ec_email_templates[$ec_template_selected]["template_folder_path"] );
					$this_template_path		= trailingslashit( "templates" );
					$this_template_name		= str_replace(".php", "-". str_replace ( "_", "-", $ec_template_selected) .".php", $template_name);
					
					// First check in all the normal overide places. In this case it will be just the theme folder.
					// template name emails/admin-new-order.php becomes emails/admin-new-order-inlinesky.php
					// for this test.
					$wc_template_path = $woocommerce->template_path();
					$this_template = locate_template( array(
						trailingslashit( $wc_template_path ) . $this_template_name,
						$this_template_name
					));
					
					// else check if we cover this template in the plugin template folder.
					// again we are looking for this file emails/admin-new-order-inlinesky.php
					if ( !$this_template ) {
						$this_template = trailingslashit( $this_plugin_path ) . trailingslashit( $this_template_path ) . $this_template_name;
						if ( !file_exists($this_template) ) $this_template = false;
					}
					//else also check the path if the template was registered with the path to the woocommerce template folder
					if ( !$this_template ) {
						$this_template = trailingslashit( $this_plugin_path ) . $this_template_name;
						if ( !file_exists($this_template) ) $this_template = false;
					}
					// else set this template to what it was passed to this - $located
					if ( !$this_template ) {
						$this_template = $located;
					}
					
					//set the located as $this_template.
					$located = $this_template;
				}
			}
		}
		
		// echo "<br>";
		// echo "get_template";
		// echo "<br>";
		// echo "<br>";
		// echo 'located--------------------: ' . $located;
		// echo "<br>";
		// echo 'template_name----------: ' . $template_name;
		// echo "<br>";
		// echo "args------------------------: ";
		// print_r( $args );
		// echo "<br>";
		// echo 'template_path-----------: ' . $template_path;
		// echo "<br>";
		// echo 'default_path-------------: ' . $default_path;
		// echo "<br><br><br><br>";
		
		return $located;
	}
	
	/**
	 * Modify options before template
	 *
	 * Only if one of the email templates are run, so as not to waste processing time,
	 * and check if REQUEST fields are being posted and rather use those.
	 *
	 * @date	20-08-2014
	 * @since	1.0
	 */
	function ec_before_template_filter_options( $template_name, $template_path, $located, $args ) {
		
		if ( FALSE !== strrpos( $template_name, 'email' ) ) {
			
			// Get active templates.
			$ec_template_selected = false;
			if ( get_option( "ec_template" ) ) {
				$ec_template_selected = get_option( "ec_template" );
			}
			if ( isset( $_REQUEST['ec_email_template'] ) ) {
				$ec_template_selected = $_REQUEST['ec_email_template'];
			}
			
			// Modify if theres preview fields.
			$settings = ec_get_settings( $ec_template_selected );
			
			if ( $settings ) {
				foreach ( $settings as $setting_key => $setting_value ) {
					
					$field_id	= $setting_value["id"];
					$field_type	= $setting_value["type"];
					
					add_filter( "default_option_{$field_id}", array('EC_Settings', 'ec_default_option') );
					add_filter( "option_{$field_id}", create_function( '$field_value', 'return EC_Settings::ec_render_option("'.$field_id.'", $field_value ); ' ) );
				}
			}
			
			// Only do this once, the first time an email template is called.
			remove_filter( 'woocommerce_before_template_part', array( $this, 'ec_before_template_filter_options' ) );
		}
	}
	
	/**
	 * Push the args into a global.
	 *
	 * To be used in the shortcodes. Has to be done this way while we are
	 * not getting passed $args due to not being able to use WC new filter
	 * in wc_get_template as it was only released late - around 2.2
	 *
	 * @date	20-08-2014
	 * @since	2.12
	 */
	function ec_before_template_setup_args_global( $template_name, $template_path, $located, $args ) {
		
		// Only do this for email templates.
		if ( FALSE !== strrpos( $template_name, 'email' ) ) {
			
			global $ec_template_args;
			
			if ( NULL == $ec_template_args ) {
				
				$ec_template_args = array_merge( $args, array( 'ec_template_name' => $template_name ) );
				
				// Debugging
				//echo 'start:&nbsp;' . $template_name;
			}
		}
	}
	
	/**
	 * Remove the args from global on last call to the template function.
	 *
	 * @date	09-02-2015
	 * @since	2.17
	 */
	function ec_after_template_setup_args_global( $template_name, $template_path, $located, $args ) {
		
		// Only do this for email templates.
		if ( FALSE !== strrpos( $template_name, 'email' ) ) {
			
			global $ec_template_args;
			
			if ( isset( $ec_template_args['ec_template_name'] ) && $template_name == $ec_template_args['ec_template_name'] ) {
				
				$ec_template_args = NULL;
				
				// Debugging
				//echo 'end:&nbsp;' . $template_name;
			}
		}
	}
	
	/**
	 * Force UTF-8 to email headers
	 *
	 * @date	09-02-2015
	 * @since	2.17
	 */
	function ec_email_headers( $headers ) {
		
		//$headers = str_replace( "\r\n", '; charset=ISO-8859-2' . "\r\n" , $headers );
		$headers = str_replace( "\r\n", '; charset=UTF-8' . "\r\n" , $headers );
		return $headers;
	}
	
	/**
	 * Format the other simpler WooCommerce emails - Content.
	 */
	function woocommerce_simple_email_content( $message ) {
		
		ob_start();
		wc_get_template('emails/email-header.php' );
		echo $message;
		wc_get_template('emails/email-footer.php' );
		return ob_get_clean();
	}
	/**
	 * Format the other simpler WooCommerce emails - Headers.
	 */
	function woocommerce_simple_email_headers() {
		
		return "Content-Type: text/html; charset=UTF-8\r\n";
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
							__( '%sEmail Customizer for WooCommerce needs WooCommerce%s %sWooCommerce%s must be active for Email Customizer to work. Please install & activate WooCommerce.', 'email-control' ),
							'<strong>',
							'</strong><br>',
							'<a href="http://wordpress.org/extend/plugins/woocommerce/" target="_blank" >',
							'</a>'
						);
						?>
					</p>
				</div>
				<?php
			elseif ( version_compare( get_option( 'woocommerce_db_version' ), WC_EMAIL_CONTROL_REQUIRED_WOOCOMMERCE_VERSION, '<' ) ) :
				?>
				<div id="message" class="error">
					<!--<p style="float: right; color: #9A9A9A; font-size: 13px; font-style: italic;">For more information <a href="http://cxthemes.com/plugins/update-notice.html" target="_blank" style="color: inheret;">click here</a></p>-->
					<p>
						<?php
						printf(
							__( '%sEmail Customizer for WooCommerce is inactive%s This version of Email Customizer requires WooCommerce %s or newer. For more information about our WooCommerce version support %sclick here%s.', 'email-control' ),
							'<strong>',
							'</strong><br>',
							WC_EMAIL_CONTROL_REQUIRED_WOOCOMMERCE_VERSION,
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
