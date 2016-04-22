<?php
/**
 * WooCommerce POS General Settings
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/settings
 * @category	Class
 * @since     0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_POS_Settings_General' ) ) :

/**
 * WC_POS_Settings_General
 */
class WC_POS_Settings_General extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general_pos';
		$this->label = __( 'General', 'woocommerce' );

		add_filter( 'wc_pos_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'wc_pos_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'wc_pos_settings_save_' . $this->id, array( $this, 'save' ) );

	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		global $woocommerce;

		$order_statuses = wc_get_order_statuses();
		foreach ($order_statuses as $key => $value) {
			$a = substr($key, 3);
			$statuses[$a] = $value;
		}
		return apply_filters( 'woocommerce_point_of_sale_general_settings_fields', array(



			array( 'title' => __( 'General Options', 'wc_point_of_sale' ), 'type' => 'title', 'desc' => '', 'id' => 'general_pos_options' ),

	      	array(
				'name' => __( 'Stock Quantity', 'wc_point_of_sale' ),
				'id'   => 'wc_pos_show_stock',
				'type' => 'checkbox',
				'desc' => __( 'Enable stock quantity identifier', 'wc_point_of_sale' ),
				'desc_tip' => __( 'Shows the remaining stock when adding products to the basket.', 'wc_point_of_sale' ),
				'default'	=> 'yes',
				'autoload'  => true					
			),
      

			array(
					'name'    => __( 'Discount Presets', 'wc_point_of_sale' ),
					'desc_tip'    => __( 'Define the preset discount buttons when applying discount to the order.', 'wc_point_of_sale' ),
					'id'      => 'woocommerce_pos_register_discount_presets',
					'class'   => 'wc-enhanced-select',
					'type'    => 'multiselect',
					'options' => apply_filters('woocommerce_pos_register_discount_presets', array(
						5 => __( '5%', 'wc_point_of_sale' ),
						10 => __( '10%', 'wc_point_of_sale' ),
						15 => __( '15%', 'wc_point_of_sale' ),
						20 => __( '20%', 'wc_point_of_sale' ),
						25 => __( '25%', 'wc_point_of_sale' ),
						30 => __( '30%', 'wc_point_of_sale' ),
						35 => __( '35%', 'wc_point_of_sale' ),
						40 => __( '40%', 'wc_point_of_sale' ),
						45 => __( '45%', 'wc_point_of_sale' ),
						50 => __( '50%', 'wc_point_of_sale' ),
						55 => __( '55%', 'wc_point_of_sale' ),
						60 => __( '60%', 'wc_point_of_sale' ),
						65 => __( '65%', 'wc_point_of_sale' ),
						70 => __( '70%', 'wc_point_of_sale' ),
						75 => __( '75%', 'wc_point_of_sale' ),
						80 => __( '80%', 'wc_point_of_sale' ),
						85 => __( '85%', 'wc_point_of_sale' ),
						90 => __( '90%', 'wc_point_of_sale' ),
						95 => __( '95%', 'wc_point_of_sale' ),
						100 => __( '100%', 'wc_point_of_sale' )
						)),
					'default' => array(5, 10, 15, 20),
				),

			
			
      		array(
					'name'    => __( 'Filters', 'wc_point_of_sale' ),
					'desc_tip'    => __( 'Select which filters appear on the Orders page.', 'wc_point_of_sale' ),
					'id'      => 'woocommerce_pos_order_filters',
					'class'   => 'wc-enhanced-select',
          			'type'    => 'multiselect',
					'options' => array(
						'register' => __('Registers', 'wc_point_of_sale'),
						'outlet'   => __('Outlets', 'wc_point_of_sale'),
						),
					'autoload' => true
				),
				
			array(
				'title'           => __( 'SSL Options', 'wc_point_of_sale' ),
				'desc'            => __( 'Force secure checkout', 'wc_point_of_sale' ),
				'id'              => 'woocommerce_pos_force_ssl_checkout',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'start',
				'desc_tip'			  => __( 'Force SSL (HTTPS) on the POS page (an SSL Certificate is required).', 'wc_point_of_sale' ),
			),

			
			
			array( 'type' => 'sectionend', 'id' => 'tile_options'),
			
			array( 'title' => __( 'Tile Options', 'woocommerce' ), 'desc' => __( 'The following options affect how the tiles appear on the product grid.', 'woocommerce' ), 'type' => 'title', 'id' => 'checkout_page_options' ),
			
			array(
					'name' => __( 'Quantity Increment', 'wc_point_of_sale' ),
					'id'   => 'woocommerce_pos_register_instant_quantity',
					'std'  => '',
					'type' => 'checkbox',
					'desc' => __( 'Enable quantity increment', 'wc_point_of_sale' ),
					'desc_tip' => __( 'Shows a quantity increment button when adding products to the basket.', 'wc_point_of_sale' ),
					'default'	=> 'no',
					'autoload'  => false					
				),
			array(
					'name' => __( 'Quantity Keypad', 'wc_point_of_sale' ),
					'id'   => 'woocommerce_pos_register_instant_quantity_keypad',
					'std'  => '',
					'type' => 'checkbox',
					'desc' => __( 'Enable quantity keypad', 'wc_point_of_sale' ),
					'desc_tip' => __( 'Shows a quantity increment button and a keypad when adding products to the basket.', 'wc_point_of_sale' ),
					'default'	=> 'no',
					'autoload'  => false					
				),
			
			array(
			'name' => __( 'Image Tiles', 'wc_point_of_sale' ),
			'id'   => 'wc_pos_hide_text_on_tiles',
			'type' => 'checkbox',
			'desc' => __( 'Enable image only tiles', 'wc_point_of_sale' ),
			'desc_tip' => __( 'Displays the product image as the tile.', 'wc_point_of_sale' ),
			'default'	=> 'no',
			'autoload'  => true					
		),
		
			array(
					'title'    => __( 'Default Tile Sorting', 'woocommerce' ),
					'desc_tip'     => __( 'This controls the default sort order of the tile.', 'woocommerce' ),
					'id'       => 'wc_pos_default_tile_orderby',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'menu_order',
					'type'     => 'select',
					'options'  => apply_filters( 'woocommerce_default_catalog_orderby_options', array(
						'menu_order' => __( 'Default sorting (custom ordering + name)', 'woocommerce' ),
						'popularity' => __( 'Popularity (sales)', 'woocommerce' ),
						'rating'     => __( 'Average Rating', 'woocommerce' ),
						'date'       => __( 'Sort by most recent', 'woocommerce' ),
						'price'      => __( 'Sort by price (asc)', 'woocommerce' ),
						'price-desc' => __( 'Sort by price (desc)', 'woocommerce' ),
					) ),
				),
			
			array( 'type' => 'sectionend', 'id' => 'status_options'),
			
			array( 'title' => __( 'Status Options', 'woocommerce' ), 'desc' => __( 'The following options affect the status of the orders when using the register.', 'woocommerce' ), 'type' => 'title', 'id' => 'checkout_page_options' ),
			
			array(
					'name'    => __( 'Complete Order', 'woocommerce' ),
					'desc_tip'    => __( 'Select the order status of completed orders when using the register.', 'wc_point_of_sale' ),
					'id'      => 'woocommerce_pos_end_of_sale_order_status',
					'css'     => '',
					'std'     => '',
					'class'   => 'wc-enhanced-select',
					'type'    => 'select',
					'options' => apply_filters('woocommerce_pos_end_of_sale_order_status', $statuses),
					'default' => 'processing'
				),
				
			
			array(
					'name'    => __( 'Save Order', 'woocommerce' ),
					'desc_tip'    => __( 'Select the order status of saved orders when using the register.', 'wc_point_of_sale' ),
					'id'      => 'wc_pos_save_order_status',
					'type'    => 'select',
					'class'   => 'wc-enhanced-select',
					'options' => apply_filters('wc_pos_save_order_status', $order_statuses),
					'default' => 'wc-pending'
				),
				
			array(
					'name'    => __( 'Load Order ', 'woocommerce' ),
					'desc_tip'    => __( 'Select the order status of loaded orders when using the register.', 'wc_point_of_sale' ),
					'id'      => 'wc_pos_load_order_status',
					'class'   => 'wc-enhanced-select',
					'type'    => 'multiselect',
					'options' => apply_filters('wc_pos_load_order_status', $order_statuses),
					'default' => 'wc-pending'
				),
				
			array(
					
					'id'   => 'wc_pos_load_web_order',
					'std'  => '',
					'type' => 'checkbox',
					'desc' => __( 'Load web orders', 'wc_point_of_sale' ),
					'desc_tip' => __( 'Check this box to load orders placed through the web store.', 'wc_point_of_sale' ),
					'default'	=> 'no',
					'autoload'  => true					
				),

			array( 'type' => 'sectionend', 'id' => 'scanning_options'),
			
			array( 'title' => __( 'Scanning Options', 'woocommerce' ), 'desc' => __( 'The following options affect the use of scanning hardware such as barcode scanners and magnetic card readers.', 'woocommerce' ), 'type' => 'title', 'id' => 'checkout_page_options' ),
			
			array(
					'title' => __( 'Barcode Scanning', 'wc_point_of_sale' ),
					'id'   => 'woocommerce_pos_register_ready_to_scan',
					'std'  => '',
					'type' => 'checkbox',
					'desc' => __( 'Enable barcode scanning', 'wc_point_of_sale' ),
					'desc_tip' => __( 'Listens to barcode scanners and adds item to basket. Carriage return in scanner recommended.', 'wc_point_of_sale' ),
					'default'	=> 'no',
					'autoload'  => false					
				),

			array(
					'name' => __( 'Credit/Debit Card Scanning', 'wc_point_of_sale' ),
					'id'   => 'woocommerce_pos_register_cc_scanning',
					'std'  => '',
					'type' => 'checkbox',
					'desc' => __( 'Enable credit/debit card scanning', 'wc_point_of_sale' ),
					'desc_tip' => sprintf(__( 'Allows magnetic card readers to parse scanned output into checkout fields. Supported payment gateways can be found here %shere%s.', 'wc_point_of_sale' ), 
				'<a href="http://actualityextensions.com/supported-payment-gateways/" target="_blank">', '</a>'),
					'default'	=> 'no',
					'autoload'  => false					
				),
			
			array( 'type' => 'sectionend', 'id' => 'account_options'),
			
			array( 'title' => __( 'Account Options', 'woocommerce' ), 'desc' => __( 'The following options affect the account creation process when creating customers.', 'woocommerce' ), 'type' => 'title', 'id' => 'checkout_page_options' ),
			
			array(
				'name'    => __( 'Username', 'wc_point_of_sale' ),
				'desc_tip'    => __( 'Choose what the username should be when customer is created.', 'wc_point_of_sale' ),
				'id'      => 'woocommerce_pos_end_of_sale_username_add_customer',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					1 => __('First & Last Name e.g. johnsmith', 'wc_point_of_sale'),
					2 => __('First & Last Name With Hyphen e.g. john-smith', 'wc_point_of_sale'),
					3 => __('Email address', 'wc_point_of_sale')
				),
				'autoload' => true
			),
			
			array( 'type' => 'sectionend', 'id' => 'email_options'),
			
			array( 'title' => __( 'Email Options', 'woocommerce' ), 'desc' => __( 'The following options affect the email notifications when orders are placed and accounts are created.', 'woocommerce' ), 'type' => 'title', 'id' => 'checkout_page_options' ),
			
			array(
				'name' => __( 'New Order', 'wc_point_of_sale' ),
				'id'   => 'wc_pos_email_notifications',
				'type' => 'checkbox',
				'desc' => __( 'Enable new order notification', 'wc_point_of_sale' ),
				'desc_tip' => sprintf(__( 'New order emails are sent to the recipient list when an order is received as shown %shere%s.', 'wc_point_of_sale' ), 
					'<a href="'.admin_url('admin.php?page=wc-settings&tab=email&section=wc_email_new_order').'">', '</a>'),
				'default'	=> 'no',
				'autoload'  => true					
			),
			
		  	array(
				'name' => __( 'Account Creation', 'wc_point_of_sale' ),
				'id'   => 'wc_pos_automatic_emails',
				'type' => 'checkbox',
				'desc' => __( 'Enable account creation notification', 'wc_point_of_sale' ),
				'desc_tip' => sprintf(__( 'Customer emails are sent to the customer when a customer signs up via checkout or account pages as shown %shere%s.', 'wc_point_of_sale' ), 
					'<a href="'.admin_url('admin.php?page=wc-settings&tab=email&section=wc_email_customer_new_account').'">', '</a>'),
				'default'	=> 'yes',
				'autoload'  => true					
			),
			
		/*array(
			'name' => __( 'Disable Search Results', 'wc_point_of_sale' ),
			'id'   => 'wc_pos_disable_search_results',
			'type' => 'checkbox',
			'desc' => __( 'Check this box to disable the search results from showing, recommended for large library of products/variations', 'wc_point_of_sale' ),
			'default'	=> 'no',
			'autoload'  => true					
		),*/
		
			array( 'type' => 'sectionend', 'id' => 'general_pos_options'),
		
		) ); // End general settings

	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();

		WC_Pos_Settings::save_fields( $settings );
	}

}

endif;

return new WC_POS_Settings_General();
