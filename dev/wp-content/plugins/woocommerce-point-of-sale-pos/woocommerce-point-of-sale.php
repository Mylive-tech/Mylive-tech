<?php
/**
 * Plugin Name: WooCommerce Point of Sale
 * Plugin URI: http://actualityextensions.com/
 * Description: WooCommerce Point of Sale is an extension which allows you to enter a customer order using the point of sale interface. This extension is suitable for retailers who have both on online and offline store.
 * Version: 2.4.20
 * Author: Actuality Extensions
 * Author URI: http://actualityextensions.com/
 * Tested up to: 4.2
 *
 * Copyright: (c) 2015 Actuality Extensions (info@actualityextensions.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Point-Of-Sale
 * @author      Actuality Extensions
 * @category    Plugin
 * @copyright   Copyright (c) 2015, Actuality Extensions
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (function_exists('is_multisite') && is_multisite()) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) )
        return;
}else{
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
        return; // Check if WooCommerce is active    
}


if (!class_exists('WoocommercePointOfSale')) {

/**
 * Main WoocommercePointOfSale Class
 *
 * @class WoocommercePointOfSale
 * @version 1.9
 */
class WoocommercePointOfSale {

    /**
     * @var string
     */
    public $version = '2.4.1';

    /**
     * @var string
     */
    public $db_version = '3.0';

    /**
     * @var bool
     */
    public $is_pos = null;

    /**
     * @var bool
     */
    public $wc_api_is_active = false;

    /**
     * @var string
     */
    public $permalink_structure = '';

    /**
     * @var WoocommercePointOfSale The single instance of the class
     * @since 1.9
     */
    protected static $_instance = null;

    /**
     * The plugin's ids
     * @var string
     */
    public $id           = 'wc_point_of_sale';
    public $id_outlets   = 'wc_pos_outlets';
    public $id_registers = 'wc_pos_registers';
    public $id_grids     = 'wc_pos_grids';
    public $id_tiles     = 'wc_pos_tiles';
    public $id_users     = 'wc_pos_users';
    public $id_receipts  = 'wc_pos_receipts';
    public $id_barcodes  = 'wc_pos_barcodes';
    public $id_settings  = 'wc_pos_settings';

    /**
     * Main WoocommercePointOfSale Instance
     *
     * Ensures only one instance of WoocommercePointOfSale is loaded or can be loaded.
     *
     * @since 1.9
     * @static
     * @see WC_POS()
     * @return WoocommercePointOfSale - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.9
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '1.9' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.9
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '1.9' );
    }

    /**
     * WoocommercePointOfSale Constructor.
     * @access public
     * @return WoocommercePointOfSale
     */

    public function __construct() {
        $this->define_constants();
        $this->init_hooks();

        do_action( 'woocommerce_poin_of_sale_loaded' );
        
    }

    public function define_constants()
    {
        $this->define( 'WC_POS_PLUGIN_FILE', __FILE__ );
        $this->define( 'WC_POS_VERSION', $this->version );
    }

    /**
     * Define constant if not already set
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    public function init_hooks()
    {
        include_once( 'includes/classes/class-wc-pos-install.php');
        register_activation_hook( __FILE__, array( 'WoocommercePointOfSale_Install', 'activate') );

        $this->wc_api_is_active    = $this->check_api_active();
        $this->permalink_structure = get_option('permalink_structure');

        add_action( 'admin_notices', array($this, 'admin_notices'), 20 );
        add_action('admin_enqueue_scripts', array($this, 'enqueue_dependencies_admin'));

        /* Change the Guest in to Walk in Customer */
        add_filter('manage_shop_order_posts_custom_column', array($this, 'pos_custom_columns'), 2);
        add_action( 'wp_trash_post', array($this, 'delete_tile'), 10 );

        if( (isset($_POST['register_id']) && !empty($_POST['register_id'])) || (isset($_GET['page']) && $_GET['page'] == 'wc_pos_registers' && isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id']) && !empty($_GET['action']) ) ) {
            add_filter('woocommerce_customer_taxable_address', 'set_outlet_taxable_address' );
        }

        add_action('wp_login', array( $this, 'set_last_login') );
        add_action( 'wc_pos_restrict_list_users', array( $this, 'restrict_list_users'));
        add_filter('woocommerce_attribute_label', array( $this, 'tile_attribute_label') );
        add_filter('woocommerce_get_checkout_order_received_url', array( $this, 'order_received_url') );

        add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_type_column'), 9999);            
        add_action( 'manage_shop_order_posts_custom_column', array( $this, 'display_order_type_column'), 2 );

        /******* product_grid *********/
        add_filter( 'manage_edit-product_columns', array( $this, 'add_product_grid_column'), 9999);            
        add_action( 'manage_product_posts_custom_column', array( $this, 'display_product_grid_column'), 2 );
        add_action( 'admin_footer', array( $this, 'product_grid_bulk_actions'), 11 );
        add_action( 'load-edit.php', array( $this, 'product_grid_bulk_actions_handler') );
        /******* end product_grid *********/

        add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_orders' ), 5 );
        add_filter( 'request', array( $this, 'orders_by_order_type' ));


        add_filter( 'woocommerce_admin_order_actions', array( $this, 'order_actions_reprint_receipts' ), 2, 20);        
        add_filter( 'woocommerce_api_check_authentication', array( $this, 'wc_api_authentication' ), 20, 1 );
        add_action( 'woocommerce_api_server_before_serve', array( $this, 'wc_api_init') );

        add_filter('woocommerce_order_number', array($this, 'add_prefix_suffix_order_number'), 10, 2);

        add_action( 'template_redirect', array( $this, 'template_redirect' ) );
        add_action('plugins_loaded', array( $this, 'init') );

        add_action('woocommerce_get_product_from_item', array( $this, 'get_product_from_item'), 15, 2 );
        add_action( 'init', array($this, 'print_report') );
        

        add_action( 'woocommerce_loaded', array($this, 'woocommerce_loaded') );
        add_action( 'woocommerce_available_payment_gateways', array($this, 'wc_pos_available_payment_gateways'), 100, 1 );
        add_action( 'init', array($this, 'wc_pos_checkout_gateways') );

        add_action( 'woocommerce_api_coupon_response', array($this, 'api_coupon_response'), 20, 4 );
        add_action( 'admin_init', array($this, 'add_caps'), 20, 4 );

        add_action( 'init', array( $this, 'wc_filter_action_customer_created' ) );
        add_action( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ), 150, 1 );
    }

    public function hidden_order_itemmeta($meta_keys = array())
    {
        $meta_keys[] = '_pos_custom_product';
        return $meta_keys;
    }
    
    public function wc_filter_action_customer_created()
    {
        if(defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] == 'wc_pos_add_customer')
            add_filter('woocommerce_email_actions', array($this, 'automatic_emails_new_customer'), 150, 1);
    }
    public function automatic_emails_new_customer($email_actions)
    {
        $new_actions = array();
        $aenc = get_option('wc_pos_automatic_emails');
        if($aenc == '') $aenc = 'yes';
        foreach ($email_actions as $action) {
            if($action == 'woocommerce_created_customer' && $aenc == 'no' )
                continue;

            $new_actions[] = $action;
        }
        return $new_actions;
    }
    public function add_caps()
    {
        $role = get_role( 'shop_manager' );
        $role->add_cap( 'read_private_products' ); 
    }

    public function api_coupon_response($coupon_data, $coupon, $fields, $server)
    {
        if(!empty($coupon_data) && is_array($coupon_data)){
            $used_by = get_post_meta( $coupon_data['id'], '_used_by' );
            if($used_by)
                $coupon_data['used_by'] = (array) $used_by;
            else
                $coupon_data['used_by'] = null;

            if(!$coupon->expiry_date)
                $coupon_data['expiry_date'] = false;
            
            $coupon_data['maximum_amount'] = $coupon->maximum_amount;
            
        }
        return $coupon_data;
    }

    public function woocommerce_loaded(){
        WoocommercePointOfSale_Install::init();
        $this->includes();
    }

    public function wc_pos_available_payment_gateways($_available_gateways)
    {
    	
    	if( ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'wc_pos_checkout' ) || $this->is_pos() ){
	    	$_available_gateways = array();
	    	$payment_gateways = WC()->payment_gateways->payment_gateways;
	    	$enabled_gateways = get_option( 'pos_enabled_gateways', array() );

			foreach ( $payment_gateways as $gateway ) {
				if ( in_array($gateway->id, $enabled_gateways) ) {
					$_available_gateways[ $gateway->id ] = $gateway;
				}
			}
		}

		return $_available_gateways;
    }

    public function wc_pos_checkout_gateways(){        
        if( ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'wc_pos_checkout' ) || $this->is_pos() ){
        	$enabled_gateways = get_option( 'pos_enabled_gateways', array() );
	        $pos_exist_gateways = get_option( 'pos_exist_gateways', array() );

	        foreach ($pos_exist_gateways as $gateway_id) {
	            if(!in_array($gateway_id, $enabled_gateways) ){
	                add_filter( 'option_woocommerce_'.$gateway_id.'_settings', array($this, 'disable_gateway') );
	            }else{
	                if($gateway_id == 'cod')
	                    add_filter( 'pre_option_woocommerce_'.$gateway_id.'_settings', array($this, 'enable_gateway_cod') );
	                else
	                    add_filter( 'option_woocommerce_'.$gateway_id.'_settings', array($this, 'enable_gateway') );
	            }

	        }
        }
    }

    public function disable_gateway($val){
        $val['enabled'] = 'no';
        return $val;
    }
    public function enable_gateway($val){
    	
        $val['enabled'] = 'yes';
        if(isset($val['enable_for_virtual']))
            $val['enable_for_virtual'] = 'yes';

        if(isset($val['enable_for_methods']))
            $val['enable_for_methods'] = array();
        
        return $val;
    }
    function enable_gateway_cod(){

    	$val = array();
        $val['enabled'] = 'yes';
        $val['enable_for_virtual'] = 'yes';
        $val['enable_for_methods'] = array();
        
        return $val;
    }
    function print_report(){
        if ( isset($_GET['print_pos_report'] ) && !empty($_GET['print_pos_report']) ) {
            $nonce = $_REQUEST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'print_pos_report' ) || ! is_user_logged_in() ) die( 'You are not allowed to view this page.' );

            remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );

            $rg_id = $_GET['print_pos_report'];
            $data  = $this->register()->get_data($rg_id);
            $data  = $data[0];
            $outlets_name = $this->outlet()->get_data_names();
            $outlet       = $outlets_name[$data['outlet']];


            include_once ($this->plugin_views_path().'/header.php');
            ?>
            <style>
            html, body{
                background: #fff;
            }
            #sale_report_popup_inner{
                padding: 20px;
            }
            </style>
            <?php
            include_once ($this->plugin_views_path().'/html-admin-registers-sale_report_overlay.php');
            include_once ($this->plugin_views_path().'/footer.php');
            exit;
        }elseif( isset($_GET['print_pos_receipt'] ) && !empty($_GET['print_pos_receipt']) && isset($_GET['order_id'] ) && !empty($_GET['order_id'])  ){
            
            $nonce = $_REQUEST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'print_pos_receipt' ) || ! is_user_logged_in() ) die( 'You are not allowed to view this page.' );
            $order_id    = $_GET['order_id'];
            $register_ID = get_post_meta( $order_id, 'wc_pos_id_register', true );

            $register    = $this->register()->get_data($register_ID);
            $register     = $register[0];
            $register_name = $register['name'];

            $receipt_ID  = $register['detail']['receipt_template'];
            $outlet_ID   = $register['outlet'];

            $preview     = false;

            $order = new WC_Order($order_id);
            $receipt_options = WC_POS()->receipt()->get_data($receipt_ID);
            $receipt_style   = WC_POS()->receipt()->get_style_templates();
            $receipt_options = $receipt_options[0];
            $attachment_image_logo = wp_get_attachment_image_src( $receipt_options['logo'], 'full' );

            

            $outlet = $this->outlet()->get_data($outlet_ID);
            $outlet = $outlet[0];
            $address = $outlet['contact'];
            $address['first_name'] = '';
            $address['last_name'] = '';
            $address['company'] = '';
            $outlet_address = WC()->countries->get_formatted_address( $address );

            remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
            include_once( $this->plugin_views_path().'/html-print-receipt.php' );
        }
    }


    /**
     * Init POS after WooCommerce Initialises.
     */
    public function init() {
        // Set up localisation
        $this->load_plugin_textdomain();
    }

    /**
     * Load Localisation files.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'wc_point_of_sale', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    }


    /**
    * Check if page is POS Register 
    * @since 1.9
    * @return bool
    */
    function is_pos(){
        global $wp_query;
        if( isset($this->is_pos) && !is_null($this->is_pos) ) {
            return $this->is_pos;
        }
        else{
            $q = $wp_query->query;
            if( isset( $q['page'] ) && $q['page'] == 'wc_pos_registers' && isset( $q['action'] ) && $q['action'] == 'view' ) {
                $this->is_pos = true;
            }else{
                $this->is_pos = false;
            }
            return $this->is_pos;
        }
    }

    /**
	 * Display POS page or login screen
	 */
	public function template_redirect() {
	    // bail if not pos
	    if( !$this->is_pos() ) 
	        return;

	    // set up $current_user for use in includes
	    global $current_user;
	    get_currentuserinfo();
	    $pos_ssl = get_option('woocommerce_pos_force_ssl_checkout');
	    if ( ! is_ssl() && $pos_ssl == 'yes') {
	            
	            if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
	                wp_safe_redirect( preg_replace( '|^http://|', 'https://', $_SERVER['REQUEST_URI'] ) );
	                exit;
	            } else {
	                wp_safe_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	                exit;
	            }
	        }
	        
	        
	    // check page and credentials
	    if ( is_user_logged_in() ) {
	        include_once( 'includes/views/html-admin-pos.php' );
	        exit;
	    } else {
	        auth_redirect();
	    }
	}
    function set_last_login($login) {
       $user = get_user_by('login', $login);
       update_user_meta( $user->ID, 'last_login', current_time('mysql') );
    }

    /**
     * Bypass authenication for WC REST API
     * @return WP_User object
     */
    public function wc_api_authentication( $user ) {

        if( $this->is_pos_referer() === true ){

            global $current_user;
            $user = $current_user;

            if( ! user_can( $user->ID, 'view_register' ) )
              $user = new WP_Error(
                'woocommerce_pos_authentication_error',
                __( 'User not authorized to access WooCommerce POS', 'wc_point_of_sale' ),
                array( 'status' => 401 )
              );
            
        }

        return $user;

    }

    public function is_pos_referer()
    {
        $referer = wp_get_referer();
        $pos_url = get_home_url()."/point-of-sale/";

        if( strpos( $referer, $pos_url) !== false){
            return true;
        }
        return false;
    }

    /**
     * Instantiate the Product Class when making api requests
     * @param  object $api_server  WC_API_Server Object      
     */
    public function wc_api_init( $api_server ) {
        
        // check both GET & POST requests
        $params = array_merge($api_server->params['GET'], $api_server->params['POST']);
        if( isset($params['action']) && $params['action'] ==  'wc_pos_json_search_products_all' ) {
            include_once( 'includes/classes/class-wc-pos-product.php' );
            $this->product = new WC_Pos_Product();
        }
    }

    /**
     * Enqueue admin CSS and JS dependencies
     */
    public function enqueue_dependencies_admin() {

        
        $wc_pos_version = WC_POS()->version;
        wp_enqueue_style('wc-pos-fonts', $this->plugin_url() . '/assets/css/fonts.css', array(), $wc_pos_version);
        if(pos_admin_page()){

            wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'jquery-ui-core', 'jquery-ui-datepicker'));

            wp_enqueue_script('jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js', array('jquery'), '2.66');
            wp_enqueue_script('woocommerce_admin_pos', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.min.js', array('jquery', 'jquery-blockui', 'jquery-placeholder', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip'));

            wp_enqueue_script('woocommerce_tiptip_js', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js');
            wp_enqueue_script('postbox_', admin_url() . '/js/postbox.min.js', array(), '2.66');
            
            /****** START STYLE *****/
            wp_enqueue_style('thickbox');            
            wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

            wp_enqueue_style('woocommerce_frontend_styles', WC()->plugin_url() . '/assets/css/admin.css');

            wp_enqueue_style('woocommerce-style', WC()->plugin_url() . '/assets/css/woocommerce-layout.css', array(), $wc_pos_version);
            wp_enqueue_style('wc-pos-style', $this->plugin_url() . '/assets/css/admin.css', array(), $wc_pos_version);

            /****** END STYLE *****/

            if ( defined('WC_VERSION') && version_compare( WC_VERSION, '2.3', '>=' ) ) {
                if( !wp_script_is( 'select2', 'enqueued' ) ){
                    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
                    wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array( 'jquery' ), '3.5.2' );
                    wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), WC_VERSION );
                    wp_localize_script( 'select2', 'wc_select_params', array(
                        'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
                        'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
                        'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
                        'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
                        'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
                        'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
                        'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
                        'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
                        'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
                        'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
                        'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
                        'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
                    ) );
                    wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', array(
                        'ajax_url'                         => admin_url( 'admin-ajax.php' ),
                        'search_products_nonce'            => wp_create_nonce( 'search-products' ),
                        'search_customers_nonce'           => wp_create_nonce( 'search-customers' )
                    ) );

                    wp_enqueue_script('wc-enhanced-select');
                }

            }else{
                wp_register_script('chosen_js', WC()->plugin_url() . '/assets/js/chosen/chosen.jquery.min.js', array('jquery'), '2.66', true);
                wp_register_script('ajax-chosen_js', WC()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery.min.js', array('jquery'), '2.66', true);

                wp_enqueue_script('chosen_js');
                wp_enqueue_script('ajax-chosen_js');                
            }

            if( pos_tiles_admin_page() ){
                wp_enqueue_media();
                wp_enqueue_script('custom-background');
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('jquery_cycle', $this->plugin_url() . '/assets/plugins/jquery.cycle.all.js', array('jquery'), $wc_pos_version);
                wp_enqueue_script('pos-colormin', $this->plugin_url() . '/assets/js/colormin.js', array('jquery'), $wc_pos_version);

                wp_enqueue_script('pos-script-tile-ordering', $this->plugin_url() . '/assets/js/tile-ordering.js', array('jquery'), $wc_pos_version);
                
            }

            if( pos_receipts_admin_page() && isset($_GET['action']) && ( $_GET['action'] == 'edit' || $_GET['action'] == 'add')){
              wp_enqueue_media();
              wp_enqueue_script('postbox');
              wp_enqueue_script('pos-script-receipt_options', $this->plugin_url() . '/assets/js/receipt_options.js', array('jquery'), $wc_pos_version);
              wp_localize_script('pos-script-receipt_options', 'wc_pos_receipt', array(
                'pos_receipt_style' => $this->receipt()->get_style_templates()
                ) );
              
            }
            if (pos_settings_admin_page() ) {
              wp_enqueue_media();
            }

            wp_enqueue_script('wc-pos-script-admin', $this->plugin_url() . '/assets/js/admin.js', array('jquery'), $wc_pos_version);
            pos_localize_script('wc-pos-script-admin');

        }
        if( pos_shop_order_page() ){
            if( !wp_script_is( 'jquery', 'enqueued' ) )
                wp_enqueue_script('jquery');

            wp_enqueue_script('jquery_barcodelistener', $this->plugin_url() . '/assets/plugins/anysearch.js', array('jquery'), $wc_pos_version);
            wp_enqueue_script('wc-pos-script-admin', $this->plugin_url() . '/assets/js/admin.js', array('jquery'), $wc_pos_version); // R1 Software - Scan Orders Fix 
            pos_localize_script('wc-pos-script-admin');
            wp_enqueue_script('wc-pos-shop-order-page-script', $this->plugin_url() . '/assets/js/shop-order-page-script.js', array('jquery'), $wc_pos_version);

            wp_enqueue_style('wc-pos-style', $this->plugin_url() . '/assets/css/admin.css', array(), $wc_pos_version);
        }

    }    
    
    function get_product_from_item($_product, $item){
        if($item['product_id'] == (int)get_option('wc_pos_custom_product_id') ){
            return false;
        }
        return $_product;
    }
    
    

    /**
     * Include required files
     */
    public function includes() {    	
    	
        if (is_admin() ){
            include_once( 'includes/classes/class-wc-pos-outlets-table.php');
            include_once( 'includes/classes/class-wc-pos-registers-table.php');
            include_once( 'includes/classes/class-wc-pos-grids-table.php');
            include_once( 'includes/classes/class-wc-pos-tiles-table.php');
            include_once( 'includes/classes/class-wc-pos-users-table.php');
            include_once( 'includes/classes/class-wc-pos-receipts-table.php');
        }
        #if (is_admin() || $this->is_pos() ) {

            include_once( 'includes/functions.php' );
            include_once( 'includes/classes/class-wc-pos-outlets.php');
            include_once( 'includes/classes/class-wc-pos-registers.php');

            
            include_once( 'includes/classes/class-wc-pos-checkout.php');
            #include_once( 'includes/classes/class-wc-pos-registers-order.php');
            
            include_once( 'includes/classes/class-wc-pos-grids.php');
            include_once( 'includes/classes/class-wc-pos-tiles.php');
            include_once( 'includes/classes/class-wc-pos-users.php');
            include_once( 'includes/classes/class-wc-pos-receipts.php');
            include_once( 'includes/classes/class-wc-pos-barcodes.php');
            //include_once( dirname(WC_PLUGIN_FILE).'/includes/wc-notice-functions.php' );

            include_once( 'includes/wc-pos-outlets.php' );
            include_once( 'includes/wc-pos-grids.php' );
            include_once( 'includes/wc-pos-tiles.php' );
            include_once( 'includes/wc-pos-receipt.php' );
            include_once( 'includes/wc-pos-users.php' );
            include_once( 'includes/wc-pos-barcodes.php');
            include_once( 'includes/wc-pos-settings.php');
            include_once( 'includes/wc-pos-register.php' );

            include_once( 'includes/admin-init.php' ); // Admin section


            if (defined('DOING_AJAX')) {
                $this->ajax_includes();
            }
       # }
    }

    /**
     * Include required ajax files.
     */
    public function ajax_includes() {
        include_once( 'includes/classes/class-wc-pos-ajax.php' );         // Ajax functions for admin and the front-end
    }
     /**
     * Change the Guest in to Walk in Customer
     */
    function pos_custom_columns() {
        global $post, $woocommerce, $the_order;
        if (empty($the_order) || $the_order->id != $post->ID) {
            $the_order = new WC_Order($post->ID);
        }

        if (!$the_order->billing_first_name) {

            $the_order->billing_first_name = 'Walk-in Customer';
        }
    }
    function delete_tile($pid){
        global $wpdb;
        $table_name = $wpdb->prefix . "wc_poin_of_sale_tiles";
        $query = "DELETE FROM $table_name WHERE product_id = $pid";
        $wpdb->query( $query );
    }
    function restrict_list_users()
    {
        $wc_pos_filters = array('outlets', 'usernames');
        ?>
        <div class="alignleft actions">
            <?php
                foreach ($wc_pos_filters as $value) {
                        add_action( 'wc_pos_add_filters_users', array($this, 'wc_pos_'.$value.'_filter') );
                }
                do_action( 'wc_pos_add_filters_users');
            ?>
        <input type="submit" id="post-query-submit" class="button action" value="Filter"/>
        </div>
        <?php
         $js = "
         if( jQuery().select2 ){
            var $ = jQuery;
            jQuery('select#dropdown_outlets').css('width', '150px').select2();
            jQuery('select#dropdown_usernames').each(function() {
                var v,t;
                $(this).find('option:selected').each(function(index, el) {
                    v = $(el).val();
                    t = $(el).text();
                });
                var _id = $(this).attr('id');
                var _class = $(this).attr('class');
                var _name = $(this).attr('name');
                $(this).replaceWith('<input type=\"text\" id=\"'+_id+'\" class=\"'+_class+'\" name=\"'+_name+'\" />');
                $('input#'+_id).select2({
                    allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
                    placeholder: $( this ).data( 'placeholder' ) ? $( this ).data( 'placeholder' ) : 'Search a customer',
                    minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                    escapeMarkup: function( m ) {
                        return m;
                    },
                    ajax: {
                        url:         wc_pos_params.ajax_url,
                        dataType:    'json',
                        quietMillis: 250,
                        data: function( term, page ) {
                            return {
                                term    : term,
                                action  : 'wc_pos_json_search_customers',
                                security: wc_pos_params.search_customers
                            };
                        },
                        results: function( data, page ) {
                            var terms = [];
                            if ( data ) {
                                        $.each( data, function( id, text ) {
                                            terms.push( { id: id, text: text } );
                                        });
                                    }
                          return { results: terms };
                        },
                        cache: true
                    },
                });
                if(typeof v != 'undefined'){
                    var preselect = {id: v, text: t};
                    $('input#'+_id).select2('data', preselect);
                }
                
            });
         }else{
            jQuery('select#dropdown_outlets').css('width', '150px').chosen();

            jQuery('select#dropdown_usernames').css('width', '200px').ajaxChosen({
                method:         'GET',
                url:            '" . admin_url( 'admin-ajax.php' ) . "',
                dataType:       'json',
                afterTypeDelay: 100,
                minTermLength:  2,
                data:       {
                    action:     'wc_pos_json_search_usernames',
                    security:   '" . wp_create_nonce( "search-usernames" ) . "',
                    default:    '" . __( 'Show all cashiers ', 'wc_point_of_sale' ) . "',
                }
            }, function (data) {

                var terms = {};

                $.each(data, function (i, val) {
                    terms[i] = val;
                });

                return terms;
            });            
         }
        ";
         if ( class_exists( 'WC_Inline_Javascript_Helper' ) ) {
            $woocommerce->get_helper( 'inline-javascript' )->add_inline_js( $js );
          } elseif( function_exists('wc_enqueue_js') ){
            wc_enqueue_js($js);
          }  else {
            $woocommerce->add_inline_js( $js );
          }
    }
    function wc_pos_outlets_filter() {        
        $outlet_arr = $this->outlet()->get_data_names();
        if ( isset($_POST['_outlets_filter']) && !empty( $_POST['_outlets_filter'] ) ) {
            $outlet_id = $_POST['_outlets_filter'];
        }else{
            $outlet_id = 0;
        }
        ?>
        <select id="dropdown_outlets" name="_outlets_filter">
          <option value=""><?php _e( 'Show all outlets', 'wc_point_of_sale' ) ?></option>
          <?php
          foreach ($outlet_arr as $key => $value) {
              if ( $outlet_id ) {
                echo '<option value="' . $key . '" ';
                selected( 1, 1 );
                echo '>' . $value . '</option>';
              }else{
                echo '<option value="' . $key . '" >' . $value . '</option>';
              }
          }
          ?>
        </select>
        <?php
    }
    function wc_pos_usernames_filter() {
        ?>
        <select id="dropdown_usernames" name="_usernames_filter">
          <option value=""><?php _e( 'Show all cashiers', 'wc_point_of_sale' ) ?></option>
          <?php
          if ( !empty( $_POST['_usernames_filter'] ) ) {
            $user_id  = $_POST['_usernames_filter'];
            $userdata = get_userdata( $user_id );

            echo '<option value="' . $user_id . '" ';
            selected( 1, 1 );
            echo '>' . $userdata->user_nicename . '</option>';
          }
          ?>
        </select>
        <?php
    }
    function tile_attribute_label($label)
    {
        if(isset($_GET['page']) && $_GET['page'] == $this->id_tiles && isset($_GET['grid_id']))
            return '<strong>' . $label . '</strong>';
        else return  $label;
    }
    function order_received_url($order_received_url)
    {   
        if( isset($_GET['page']) && $_GET['page'] == 'wc_pos_registers' &&  isset($_GET['reg']) && !empty($_GET['reg']) &&  isset($_GET['outlet']) && !empty($_GET['outlet']) ){
            $register = $_GET['reg'];
            $outlet   = $_GET['outlet'];

            setcookie ("wc_point_of_sale_register", $register ,time()-3600*24*120, '/');
            $register_url = get_home_url()."/point-of-sale/$outlet/$register";
            
            if ( is_ssl() || get_option('woocommerce_pos_force_ssl_checkout') == 'yes' ) {
                $register_url = str_replace( 'http:', 'https:', $register_url );
            }

            return $register_url;
        }
        else{
            return $order_received_url;
        }
    }
    function add_order_type_column($columns)
    {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if($key == 'order_status')
                $new_columns['wc_pos_order_type'] = __( '<span class="order-type tips" data-tip="Order Type">Order Type</span>', 'wc_point_of_sale' );
        }
        return $new_columns;
    }
    
    function display_order_type_column($column)
    {
        global $post, $woocommerce, $the_order;

            if ( empty( $the_order ) || $the_order->id != $post->ID )
                $the_order = new WC_Order( $post->ID );

            if ( $column == 'wc_pos_order_type' ) {
                $order_type = __( '<span class="order-type-web tips" data-tip="Website Order">web<span>', 'wc_point_of_sale' );
                $amount_change = get_post_meta( $the_order->id, 'wc_pos_order_type', true );
                if($amount_change) $order_type = __( '<span class="order-type-pos tips" data-tip="Point of Sale Order">pos<span>', 'wc_point_of_sale' );
                echo $order_type;
            }
    }
    /******* product_grid *********/
    function add_product_grid_column($columns)
    {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if($key == 'product_tag')
                $new_columns['wc_pos_product_grid'] = __( 'Product Grid', 'wc_point_of_sale' );
        }
        return $new_columns;
    }
    
    function display_product_grid_column($column)
    {
        global $post, $woocommerce;
        if ( $column == 'wc_pos_product_grid' ) {
            $product_id = $post->ID;
            $grids      = wc_point_of_sale_get_grids_names_for_product($product_id);
            $links      = array();
            if(!empty($grids)){
              foreach ($grids as $id => $name) {
                $url = admin_url( 'admin.php?page=wc_pos_tiles&grid_id=').$id;
                $links[] = '<a href="'.$url.'">'.$name.'</a>';
              }
              echo implode(', ', $links);
            }else{
              echo '<span class="na">–</span>';
            }
        }
    }

    function product_grid_bulk_actions(){
      global $post_type;
      if ( 'product' == $post_type ) {
      ?>
         <script type="text/javascript">
             jQuery(document).ready(function() {
                  <?php
                  $grids = wc_point_of_sale_get_grids();
                  if(!empty($grids)){
                   foreach($grids as $grid){ ?>
                       jQuery('<option>').val('wc_pos_add_to_grid_<?php echo $grid->ID; ?>')
                            .text('<?php printf( __( "Add to %s", "wc_point_of_sale" ), $grid->name ); ?>').appendTo('select[name=action]');
                       jQuery('<option>').val('wc_pos_add_to_grid_<?php echo $grid->ID; ?>')
                            .text('<?php printf( __( "Add to %s", "wc_point_of_sale" ), $grid->name ); ?>').appendTo('select[name=action2]');
                    <?php
                    }
                  }
                 ?>
              });
        </script>
      <?php
      }
    }

    function product_grid_bulk_actions_handler(){
      if(!isset($_REQUEST['post'])){
        return;
      }
      $wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
      $action = $wp_list_table->current_action();
      
      global $wpdb;
      $changed = 0;
      $post_ids = array_map( 'absint', (array) $_REQUEST['post'] );
      if(strstr($action,'wc_pos_add_to_grid_')) {
        $grid_id = (int)substr($action,strlen('wc_pos_add_to_grid_'));
        $report_action = "products_added_to_grid";
        foreach( $post_ids as $post_id ) {
            if(!product_in_grid($post_id, $grid_id)){
              $order_position = 1;
              $position = get_last_position_of_tile($grid_id);
              if(!empty($position->max)) $order_position = $position->max + 1;
              $data = array(
                'grid_id'           => $grid_id,
                'product_id'        => $post_id,
                'colour'            => 'ffffff',
                'background'        => '8E8E8E',
                'default_selection' => 0,
                'order_position'    => $order_position,
                'style'             => 'image'
              );
              $wpdb->insert( $wpdb->prefix.'wc_poin_of_sale_tiles', $data );
              $changed++;
            }
        }
      } else{
          return;
      }
      $sendback = esc_url_raw( add_query_arg( array( 'post_type' => 'product', $report_action => $changed, 'ids' => join( ',', $post_ids ) ), '' ) );
      wp_redirect( $sendback );
      exit();
    }
    /******* end product_grid *********/


    function restrict_manage_orders($value='')
    {
        global $woocommerce, $typenow;
        if ( 'shop_order' != $typenow ) {
            return;
        }
        $req_type = isset($_REQUEST['shop_order_wc_pos_order_type']) ? $_REQUEST['shop_order_wc_pos_order_type'] : '';
        $req_reg  = isset($_REQUEST['shop_order_wc_pos_filter_register']) ? $_REQUEST['shop_order_wc_pos_filter_register'] : '';
        $req_out  = isset($_REQUEST['shop_order_wc_pos_filter_outlet']) ? $_REQUEST['shop_order_wc_pos_filter_outlet'] : '';
        ?>
        <select name='shop_order_wc_pos_order_type' id='dropdown_shop_order_wc_pos_order_type'>
            <option value=""><?php _e( 'All types', 'wc_point_of_sale' ); ?></option>
            <option value="online" <?php selected($req_type, 'online', true); ?> ><?php _e( 'Online', 'wc_point_of_sale' ); ?></option>
            <option value="POS" <?php selected($req_type, 'POS', true); ?> ><?php _e( 'POS', 'wc_point_of_sale' ); ?></option>
        </select>
        <?php
        $filters = get_option('woocommerce_pos_order_filters');

        if( !$filters || !is_array($filters)) return;

        if( in_array('register', $filters)) {
            $registers = $this->register()->get_data();
            if($registers){
            ?>
            <select name='shop_order_wc_pos_filter_register' id='shop_order_wc_pos_filter_register'>
            <option value=""><?php _e('All registers', 'wc_point_of_sale'); ?></option>
            <?php
            foreach ($registers as $register) {
                echo '<option value="'.$register['ID'].'" ' . selected($req_reg, $register['ID'], false) . ' >'.$register['name'].'</option>';
            }
            ?>
            </select>
            <?php
            }
        }
        if( in_array('outlet', $filters)) {
            $outlets = $this->outlet()->get_data();
            if($outlets){
            ?>
            <select name='shop_order_wc_pos_filter_outlet' id='shop_order_wc_pos_filter_outlet'>
            <option value=""><?php _e('All outlets', 'wc_point_of_sale'); ?></option>
            <?php
            foreach ($outlets as $outlet) {
                echo '<option value="'.$outlet['ID'].'" ' . selected($req_out, $outlet['ID'], false) . ' >'.$outlet['name'].'</option>';
            }
            ?>
            </select>
            <?php
            }
        }
        
    }
    public function orders_by_order_type( $vars ) {
        global $typenow, $wp_query;
        if ( $typenow == 'shop_order' ) {

            if(isset( $_GET['shop_order_wc_pos_order_type'] ) && $_GET['shop_order_wc_pos_order_type'] != ''){
                
                if($_GET['shop_order_wc_pos_order_type'] == 'POS'){
                    $vars['meta_query'][] = array(
                                'key'     => 'wc_pos_order_type',
                                'value'   => 'POS',
                                'compare' => '=',
                            );
                }elseif($_GET['shop_order_wc_pos_order_type'] == 'online'){
                    $vars['meta_query'][] = array(
                                'key'     => 'wc_pos_order_type',
                                'compare' => 'NOT EXISTS'
                            );
                }

            }

            if(isset( $_GET['shop_order_wc_pos_filter_register'] ) && $_GET['shop_order_wc_pos_filter_register'] != ''){
                $vars['meta_query'][] = array(
                            'key'     => 'wc_pos_id_register',
                            'value'   => $_GET['shop_order_wc_pos_filter_register'],
                            'compare' => '=',
                        );

            }
            if(isset( $_GET['shop_order_wc_pos_filter_outlet'] ) && $_GET['shop_order_wc_pos_filter_outlet'] != ''){
                $registers = pos_get_registers_by_outlet($_GET['shop_order_wc_pos_filter_outlet']);
                $vars['meta_query'][] = array(
                            'key'     => 'wc_pos_id_register',
                            'value'   => $registers,
                            'compare' => 'IN',
                        );

            }
            
        }

        return $vars;
    }
    function order_actions_reprint_receipts($actions, $the_order){
        $amount_change = get_post_meta( $the_order->id, 'wc_pos_order_type', true );
        $id_register = get_post_meta( $the_order->id, 'wc_pos_id_register', true );
        if($amount_change && $id_register){
            $data = $this->register()->get_data($id_register);
            if(!empty($data) && !empty($data[0])){
                $data = $data[0];
                $actions['reprint_receipts'] = array(
                    'url'       => wp_nonce_url( admin_url( 'admin.php?print_pos_receipt=true&order_id=' . $the_order->id ), 'print_pos_receipt' ),
                    'name'      => __( 'Reprint receipts', 'wc_point_of_sale' ),
                    'action'    => "reprint_receipts"
                );    
            }
            
        }
        
        return $actions;
    }

    function add_prefix_suffix_order_number($order_id, $order)
    {
        $redister_id = get_post_meta($order->id, 'wc_pos_id_register', true);
        
        if($redister_id){

            $_order_id = get_post_meta($order->id, 'wc_pos_prefix_suffix_order_number', true);
            if( $_order_id == '' ){
                $reg = $this->register()->get_data($redister_id);
                if($reg){
                    $reg = $reg[0];
                    $_order_id = $reg['detail']['prefix'] . $order->id . $reg['detail']['suffix'];
                    add_post_meta($order->id, 'wc_pos_prefix_suffix_order_number', $_order_id, true);
                    add_post_meta($order->id, 'wc_pos_order_tax_number', $reg['detail']['tax_number'], true);
                }
            }
            $order_id = str_replace('#', '', $_order_id);
        }
        return $order_id;
    }

    /**
     * Check API is active
     * @return boolean
     */
    public function check_api_active() {
        $api_access = false;
        if( get_option('woocommerce_api_enabled') == 'yes' ) {
            $api_access = true;
        }
        return $api_access;
    }

    function admin_notices(){
        if(!$this->wc_api_is_active){
            ?>
            <div class="error">
                <p><?php _e('The WooCommerce API is disabled on this site.', 'wc_point_of_sale'); ?> <a href="<?php echo admin_url('admin.php?page=wc-settings'); ?>"><?php _e( 'Enable the REST API', 'wc_point_of_sale' ); ?></a></p>
            </div>
            <?php
        }
        if($this->permalink_structure == ''){
            ?>
            <div class="error">
                <p><?php _e('Incorrect Permalinks Structure.', 'wc_point_of_sale'); ?> <a href="<?php echo admin_url('options-permalink.php'); ?>"><?php _e( 'Change Permalinks', 'wc_point_of_sale' ); ?></a></p>
            </div>
            <?php   
        }
    }

    /** Helper functions ******************************************************/
    
    /**
     * Get WooCommerce API endpoint.
     *
     * @return string
     */
    public function wc_api_url() {
        return home_url('/wc-api/v3/', 'relative');
    }

    /**
     * Get the plugin file.
     *
     * @return string
     */
    public function plugin_file() {
        return __FILE__ ;
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_views_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ).'/includes/views' );
    }
    /**
     * Get the sound url.
     *
     * @return string
     */
    public function plugin_sound_url() {
        return untrailingslashit( plugins_url( '/assets/plugins/ion.sound/sounds', __FILE__ ) );
    }

    /**
     * Get Outlets class
     *
     * @since 1.9
     * @return WC_Pos_Outlets
     */
    public function outlet() {
        return WC_Pos_Outlets::instance();
    }

    /**
     * Get Outlets table class
     *
     * @since 1.9
     * @return WC_Pos_Outlets_Table
     */
    public function outlet_table() {
        return new WC_Pos_Outlets_Table;
    }

    /**
     * Get Registers class
     *
     * @since 1.9
     * @return WC_Pos_Registers
     */
    public function register() {
        return WC_Pos_Registers::instance();
    }

    /**
     * Get Registers Table class
     *
     * @since 1.9
     * @return WC_Pos_Registers_Table
     */
    public function registers_table() {
        return new WC_Pos_Registers_Table;
    }


    /**
     * Get Grids class
     *
     * @since 1.9
     * @return WC_Pos_Grids
     */
    public function grid() {
        return WC_Pos_Grids::instance();
    }

    /**
     * Get Grids Table class
     *
     * @since 1.9
     * @return WC_Pos_Grids_Table
     */
    public function grids_table() {
        return new WC_Pos_Grids_Table;
    }

    /**
     * Get Tiles class
     *
     * @since 1.9
     * @return WC_Pos_Tiles
     */
    public function tile() {
        return WC_Pos_Tiles::instance();
    }
    /**
     * Get Tiles Table class
     *
     * @since 1.9
     * @return WC_Pos_Tiles_Table
     */
    public function tiles_table() {
        return new WC_Pos_Tiles_Table;
    }

    /**
     * Get Users class
     *
     * @since 1.9
     * @return WC_Pos_Users
     */
    public function user() {
        return WC_Pos_Users::instance();
    }

    /**
     * Get Users Table class
     *
     * @since 1.9
     * @return WC_Pos_users_Table
     */
    public function users_table() {
        return new WC_Pos_users_Table;
    }

    /**
     * Get Receipts class
     *
     * @since 1.9
     * @return WC_Pos_Receipts
     */
    public function receipt() {
        return WC_Pos_Receipts::instance();
    }

    /**
     * Get Receipts Table class
     *
     * @since 1.9
     * @return WC_Pos_Receipts_Table
     */
    public function receipts_table() {
        return new WC_Pos_Receipts_Table();
    }

    /**
     * Get Barcodes class
     *
     * @since 1.9
     * @return WC_Pos_Barcodes
     */
    public function barcode() {
        return WC_Pos_Barcodes::instance();
    }

}
/**
 * Returns the main instance of WoocommercePointOfSale to prevent the need to use globals.
 *
 * @since  1.9
 * @return WoocommercePointOfSale
 */
function WC_POS() {
    return WoocommercePointOfSale::instance();
}

// Global for backwards compatibility.
global $wc_point_of_sale, $wc_pos_db_version;

$wc_pos_db_version      = WC_POS()->db_version;
$wc_point_of_sale       = WC_POS();
$GLOBALS['woocommerce'] = WC_POS();   

}