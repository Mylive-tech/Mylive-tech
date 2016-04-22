<?php 
/**
 * Main plugin file.
 * This plugin adds useful admin links and massive resources for the
 *    WooCommerce Shop Plugin to the WordPress Toolbar / Admin Bar.
 *
 * @package     WooCommerce Admin Bar Addition
 * @author      David Decker
 * @copyright   Copyright (c) 2011-2014, David Decker - DECKERWEB
 * @license     GPL-2.0+
 * @link        http://deckerweb.de/twitter
 *
 * @wordpress-plugin
 * Plugin Name: WooCommerce Admin Bar Addition
 * Plugin URI:  http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * Description: This plugin adds useful admin links and resources for the WooCommerce Shop Plugin to the WordPress Toolbar / Admin Bar.
 * Version:     2.7.0
 * Author:      David Decker - DECKERWEB
 * Author URI:  http://deckerweb.de/
 * License:     GPL-2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: woocommerce-admin-bar-addition
 * Domain Path: /languages/
 *
 * Copyright (c) 2011-2014 David Decker - DECKERWEB
 *
 *     This file is part of WooCommerce Admin Bar Addition,
 *     a plugin for WordPress.
 *
 *     WooCommerce Admin Bar Addition is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 2 of the License, or (at your option)
 *     any later version.
 *
 *     WooCommerce Admin Bar Addition is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Prevent direct access to this file.
 *
 * @since 2.6.0
 */
if ( ! defined( 'WPINC' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Setting constants.
 *
 * @since 1.0.0
 */
/** Plugin directory */
define( 'WCABA_PLUGIN_DIR', trailingslashit( dirname( __FILE__ ) ) );

/** Plugin base directory (folder) */
define( 'WCABA_PLUGIN_BASEDIR', trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );


/**
 * Load extra set of helper functions.
 *
 * @since  2.6.0
 *
 * @return string Path to plugin's languages directory.
 */
require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-functions.php' );


add_action( 'init', 'ddw_woocommerce_aba_init' );
/**
 * Load the text domain for translation of the plugin.
 * Load admin helper functions - only within 'wp-admin'.
 * 
 * @since 1.0.0
 *
 * @uses  is_admin()
 * @uses  get_locale()
 * @uses  load_textdomain()	To load translations first from WP_LANG_DIR sub folder.
 * @uses  load_plugin_textdomain() To additionally load default translations from plugin folder (default).
 * @uses  ddw_wcaba_plugin_lang_dir() To get filterable path to plugin's languages directory.
 */
function ddw_woocommerce_aba_init() {

	/** Set unique textdomain string */
	$wcaba_textdomain = 'woocommerce-admin-bar-addition';

	/** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
	$locale = apply_filters( 'plugin_locale', get_locale(), $wcaba_textdomain );

	/** Set filter for WordPress languages directory */
	$wcaba_wp_lang_dir = apply_filters(
		'wcaba_filter_wp_lang_dir',
		trailingslashit( WP_LANG_DIR ) . 'woocommerce-admin-bar-addition/' . $wcaba_textdomain . '-' . $locale . '.mo'
	);

	/** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
	load_textdomain( $wcaba_textdomain, $wcaba_wp_lang_dir );

	/** Translations: Secondly, look in plugin's "languages" folder = default */
	load_plugin_textdomain( $wcaba_textdomain, FALSE, esc_attr( ddw_wcaba_plugin_lang_dir() ) );


	/** If 'wp-admin' include admin helper functions */
	if ( is_admin() ) {

		require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-admin.php' );

	}  // end if

	/** Add "WooCommerce Settings Page" link to plugin page */
	if ( is_admin() && current_user_can( 'manage_woocommerce' ) ) {

		add_filter(
			'plugin_action_links_' . plugin_basename( __FILE__ ),
			'ddw_wcaba_settings_page_link'
		);

	}  // end if


	/** Define constants and set defaults for removing all or certain sections */
	if ( ! defined( 'WCABA_DISPLAY' ) ) {
		define( 'WCABA_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_EXTENSIONS_DISPLAY' ) ) {
		define( 'WCABA_EXTENSIONS_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_THEMES_DISPLAY' ) ) {
		define( 'WCABA_THEMES_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_RESOURCES_DISPLAY' ) ) {
		define( 'WCABA_RESOURCES_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_DEBUG_DISPLAY' ) ) {
		define( 'WCABA_DEBUG_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_REPORTS_DISPLAY' ) ) {
		define( 'WCABA_REPORTS_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_DE_DISPLAY' ) ) {
		define( 'WCABA_DE_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_NL_DISPLAY' ) ) {
		define( 'WCABA_NL_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_ES_DISPLAY' ) ) {
		define( 'WCABA_ES_DISPLAY', TRUE );
	}

	if ( ! defined( 'WCABA_CZ_DISPLAY' ) ) {
		define( 'WCABA_CZ_DISPLAY', TRUE );
	}

}  // end of function ddw_woocommerce_aba_init


/**
 * Check for current WooCommerce version (branch).
 *
 * @since  2.7.0
 *
 * @return bool Returns TRUE if current WooCommerce version is at least '2.1.0',
 *              otherwise FALSE.
 */
function ddw_wcaba_woocommerce_current() {

	/** Get WooCommerce version constants */
	$wc_20x = ( defined( 'WOOCOMMERCE_VERSION' ) ) ? WOOCOMMERCE_VERSION : 'foo';
	$wc_21x = ( defined( 'WC_VERSION' ) ) ? WC_VERSION : 'foo';

	/** Check WooCommerce versions */
	if ( version_compare( $wc_21x, '2.1.0', '>=' ) ) {

		return TRUE;

	} elseif ( version_compare( $wc_20x, '2.0.9999', '<=' ) ) {

		return FALSE;

	}  // end if

}  // end of function ddw_wcaba_woocommerce_current


add_action( 'admin_bar_menu', 'ddw_woocommerce_aba_admin_bar_menu', 98 );
/**
 * Add new menu items to the WordPress Toolbar / Admin Bar.
 * 
 * @since  1.0.0
 *
 * @uses   ddw_wcaba_woocommerce_current() To detect current WooCommerce version branch.
 *
 * @global mixed $wp_admin_bar 
 */
function ddw_woocommerce_aba_admin_bar_menu() {

	global $wp_admin_bar;

	/**
	 * Allows for filtering the general user capability to see main & sub-level items
	 *
	 * Default capability: 'manage_woocommerce' (set by WooCommerce plugin itself!)
	 */
	$wcaba_filter_capability = apply_filters( 'wcaba_filter_capability_all', 'manage_woocommerce' );

	/**
	 * Required WooCommerce/ WordPress cabability to display new admin bar entry
	 * Only showing items if toolbar / admin bar is activated and user is logged in!
	 */
	if ( ! is_user_logged_in()
		|| ! is_admin_bar_showing()
		|| ! current_user_can( $wcaba_filter_capability )		// users without proper cap won't see anything!
		|| ( defined( 'WCABA_DISPLAY' ) && ! WCABA_DISPLAY )	// allows for custom disabling
	) {

		return;

	}  // end if

	/** Set unique prefix */
	$prefix = 'ddw-woocommerce-';
	
	/** Create parent menu item references */
	$woocommercebar = $prefix . 'admin-bar';		// root level
		$support = $prefix . 'wcsupport';				// sub level: support
		$wcdocs = $prefix . 'wcdocs';					// sub level: wc docs
		$woocommercesites = $prefix . 'woocommercesites';		// sub level: wc sites
		$settings = $prefix . 'settings';				// sub level: settings
			$sgeneralsettings = $prefix . 'sgeneralsettings';		// third level: general settings / wc status
			$semails = $prefix . 'semails';		                    // third level: general settings / emails
			$settingsintegration = $prefix . 'settingsintegration';		// third level: integration(s)
			$s_dynpricing = $prefix . 's_dynpricing';			// third level settings: dynamic pricing
			$s_commissionking = $prefix . 's_commissionking';		// third level settings: comission king
		$products = $prefix . 'products';				// sub level: products
			$p_compareproductslite = $prefix . 'p_compareproductslite';	// third level products: compare products lite
			$p_compareproductspro = $prefix . 'p_compareproductspro';	// third level products: compare products pro
			$p_csvimportsuite = $prefix . 'p_csvimportsuite';		// third level products: csv import suite
		$coupons = $prefix . 'coupons';					// sub level: coupons
		$orders = $prefix . 'orders';					// sub level: orders
		$reports = $prefix . 'reports';					// sub level: reports
			$reportssales = $prefix . 'reportssales';			// third level: reports sales
			$reportsproducts = $prefix . 'reportsproducts';		// third level: reports products
			$reportscategories = $prefix . 'reportscategories';	// third level: reports categories
			$reportscoupons = $prefix . 'reportscoupons';		// third level: reports coupons
			$reportscustomers = $prefix . 'reportscustomers';	// third level: reports customers
			$reportsstock = $prefix . 'reportsstock';			// third level: reports stock
		$shoplink = $prefix . 'shoplink';				// sub level: shop link
			$shoplinkcheckout = $prefix . 'shoplinkcheckout';		// third level: shop link checkout
			$shoplinkmyaccount = $prefix . 'shoplinkmyaccount';		// third level: shop link my account
			$shoplinkde = $prefix . 'shoplinkde';				// third level: shop link german extension
		$extensions = $prefix . 'extensions';				// sub level: extensions
			$extpideal = $prefix . 'extpideal';				// third level plugin ext.: pronamic ideal
			$extwtsoftwareaddon = $prefix . 'extwtsoftwareaddon';		// third level plugin ext.: software add-on
			$extwtdynpricing = $prefix . 'extwtdynpricing';			// third level plugin ext.: dynamic pricing
			$extwtcommissionking = $prefix . 'extwtcommissionking';		// third level plugin ext.: comission king
			$extwtclickatellsms = $prefix . 'extwtclickatellsms';		// third level plugin ext.: clickatell sms
			$extwtquickbooks = $prefix . 'extwtquickbooks';			// third level plugin ext.: quickbooks
			$extwccplite = $prefix . 'extwccplite';				// third level plugin ext.: compare products lite
			$extwccppro = $prefix . 'extwccppro';				// third level plugin ext.: compare products pro
			$extwtcsvimportsuite = $prefix . 'extwtcsvimportsuite';		// third level plugin ext.: csv import suite
		$wcgroup = $prefix . 'wcgroup';					// sub level: wc group (resources)


	/** Make the "WooCommerce" name filterable within menu items */
	$wcaba_woocommerce_name = apply_filters(
		'wcaba_filter_woocommerce_name',
		__( 'WooCommerce', 'woocommerce-admin-bar-addition' )
	);

	/** Make the "WooCommerce" name's tooltip filterable within menu items */
	$wcaba_woocommerce_name_tooltip = apply_filters(
		'wcaba_filter_woocommerce_name_tooltip',
		_x( 'WooCommerce', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
	);


	/** Display the following items also when WooCommerce plugin is not installed */
	if ( defined( 'WCABA_RESOURCES_DISPLAY' ) && WCABA_RESOURCES_DISPLAY ) {

		/** Include plugin file with resources links */
		require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-resources.php' );

	}  // end if

	/** Include plugin file with language specific items/ links */
	if ( ! ddw_wcaba_detect_locales( 'en_US', 'en_GB', 'en_CA', 'en_NZ', 'en_AU' ) ) {

		require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-languages.php' );

	}  // end if

	/**
	 * Show these items only if WooCommerce plugin is actually installed
	 * Check for classes "WooCommerce" (v2.1.0+) or "Woocommerce" (2.0.x)
	 */
	if ( class_exists( 'WooCommerce' ) || class_exists( 'Woocommerce' ) ) {

		/** Display "Orders" section only for users with the capability 'edit_shop_order' */
		if ( current_user_can( 'edit_shop_orders' ) ) {

			$menu_items[ 'orders' ] = array(
				'parent' => $woocommercebar,
				'title'  => __( 'Orders', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'edit.php?post_type=shop_order' ),
				'meta'   => array(
					'target' => '',
					'title'  => __( 'Orders', 'woocommerce-admin-bar-addition' )
				)
			);

				/** Display order status links if theme support is added */
				if ( current_theme_supports( 'wcaba-order-status' ) ) {

					require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-orderstatus.php' );

				}  // end if theme support check

				$menu_items[ 'o-add-order' ] = array(
					'parent' => $orders,
					'title'  => __( 'Add new order', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'post-new.php?post_type=shop_order' ),
					'meta'   => array(
						'target' => '',
						'title'  => __( 'Add new order', 'woocommerce-admin-bar-addition' )
					)
				);

				$menu_items[ 'o-customers' ] = array(
					'parent' => $orders,
					'title'  => __( 'Current Customers', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'users.php?role=customer' ),
					'meta'   => array(
						'target' => '',
						'title'  => __( 'Current Customers', 'woocommerce-admin-bar-addition' )
					)
				);

		}  // end if orders cap check

		/** Display "Reports" section only for users with the capability 'view_woocommerce_reports' */
		if ( ( defined( 'WCABA_REPORTS_DISPLAY' ) && WCABA_REPORTS_DISPLAY )
			&& current_user_can( 'view_woocommerce_reports' )
		) {

			/** Include reports links per version branch */
			if ( ddw_wcaba_woocommerce_current() ) {

				require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-reports-v21x.php' );

			} else {

				require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-reports-v20x.php' );

			}  // end if

		}  // end if reports cap check

		/** Display "Products" section only for users with the capability 'edit_products' */
		if ( current_user_can( 'edit_products' ) ) {

			$menu_items[ 'products' ] = array(
				'parent' => $woocommercebar,
				'title'  => __( 'Products', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'edit.php?post_type=product' ),
				'meta'   => array(
					'target' => '',
					'title'  => __( 'Products', 'woocommerce-admin-bar-addition' )
				)
			);

			$menu_items[ 'p-add-product' ] = array(
				'parent' => $products,
				'title'  => __( 'Add new product', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'post-new.php?post_type=product' ),
				'meta'   => array(
					'target' => '',
					'title'  => __( 'Add new product', 'woocommerce-admin-bar-addition' )
				)
			);

			/** Various 'product' taxonomies */
			if ( current_user_can( 'manage_product_terms' ) ) {

				$menu_items[ 'p-product-categories' ] = array(
					'parent' => $products,
					'title'  => __( 'Product Categories', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ),
					'meta'   => array(
						'target' => '',
						'title'  => __( 'Product Categories', 'woocommerce-admin-bar-addition' )
					)
				);

				$menu_items[ 'p-product-tags' ] = array(
					'parent' => $products,
					'title'  => __( 'Product Tags', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'edit-tags.php?taxonomy=product_tag&post_type=product' ),
					'meta'   => array(
						'target' => '',
						'title'  => __( 'Product Tags', 'woocommerce-admin-bar-addition' )
					)
				);

				$menu_items[ 'p-product-shippingclasses' ] = array(
					'parent' => $products,
					'title'  => __( 'Products - Shipping Classes', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'edit-tags.php?taxonomy=product_shipping_class&post_type=product' ),
					'meta'   => array(
						'target' => '',
						'title'  => __( 'Products - Shipping Classes', 'woocommerce-admin-bar-addition' )
					)
				);

				$menu_items[ 'p-product-variants' ] = array(
					'parent' => $products,
					'title'  => __( 'Product Variants (Attributes)', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'edit.php?post_type=product&page=woocommerce_attributes' ),
					'meta'   => array(
						'target' => '',
						'title' => __( 'Product Variants (Attributes)', 'woocommerce-admin-bar-addition' )
					)
				);

			}  // end if

		}  // end if products cap check

		/** Display "Coupons" section only for users with the capability 'edit_shop_coupon' */
		if ( current_user_can( 'edit_shop_coupons' ) ) {

			$menu_items[ 'coupons' ] = array(
				'parent' => $woocommercebar,
				'title'  => __( 'Coupons', 'woocommerce-admin-bar-addition' ),
				'href'   => admin_url( 'edit.php?post_type=shop_coupon' ),
				'meta'   => array(
					'target' => '',
					'title'  => __( 'Coupons', 'woocommerce-admin-bar-addition' )
				)
			);

				$menu_items[ 'c-add-coupon' ] = array(
					'parent' => $coupons,
					'title'  => __( 'Add new coupon', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'post-new.php?post_type=shop_coupon' ),
					'meta'   => array(
						'target' => '',
						'title'  => __( 'Add new coupon', 'woocommerce-admin-bar-addition' )
					)
				);

		}  // end if

		/** Settings links */
		if ( current_user_can( 'manage_woocommerce' ) ) {

			/** Include settings links per version branch */
			if ( ddw_wcaba_woocommerce_current() ) {

				require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-settings-v21x.php' );

			} else {

				require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-settings-v20x.php' );

			}  // end if


			/** Setting links continued once again... */
			if ( current_user_can( 'edit_theme_options' ) ) {

				$menu_items[ 's-widgets' ] = array(
					'parent' => $settings,
					'title'  => esc_attr__( $wcaba_woocommerce_name ) . ' ' . __( 'Widgets', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'widgets.php' ),
					'meta'   => array(
						'target' => '',
						'title'  => esc_attr__( $wcaba_woocommerce_name_tooltip ) . ' ' . __( 'Widgets', 'woocommerce-admin-bar-addition' )
					)
				);

				$menu_items[ 's-menus' ] = array(
					'parent' => $settings,
					'title'  => esc_attr__( $wcaba_woocommerce_name ) . ' ' . __( 'Menus', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'nav-menus.php' ),
					'meta'   => array(
						'target' => '',
						'title'  => esc_attr__( $wcaba_woocommerce_name_tooltip ) . ' ' . __( 'Menus', 'woocommerce-admin-bar-addition' )
					)
				);

			}  // end if cap check

		}  // end if WC (main/ settings) cap check

		/** Display shop links if theme support is added */
		if ( current_theme_supports( 'wcaba-shop-links' ) && current_user_can( 'edit_pages' ) ) {

			require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-shoplinks.php' );

		}  // end if theme support check

		/**
		 * Display last main item in the menu for active extensions/plugins
		 * ATTENTION: This is where plugins/extensions hook in on the sub-level hierarchy
		 *
		 * @since 1.0.0
		 */
		if ( ( defined( 'WCABA_EXTENSIONS_DISPLAY' ) && WCABA_EXTENSIONS_DISPLAY )
			&& current_user_can( 'activate_plugins' )
		) {

			$menu_items[ 'extensions' ] = array(
				'parent' => $woocommercebar,
				'title'  => __( 'Active Extensions', 'woocommerce-admin-bar-addition' ),
				'href'   => is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' ),
				'meta'   => array(
					'target' => '',
					'title'  => __( 'Active Extensions', 'woocommerce-admin-bar-addition' )
				)
			);

			/** Add-Ons admin page link */
			if ( ddw_wcaba_woocommerce_current() ) {

				$menu_items[ 'extensions-browser' ] = array(
					'parent' => $extensions,
					'title'  => __( 'Add-On Browser', 'woocommerce-admin-bar-addition' ),
					'href'   => admin_url( 'admin.php?page=wc-addons' ),
					'meta'   => array(
						'target' => '',
						'title'  => __( 'Add-On Browser', 'woocommerce-admin-bar-addition' )
					)
				);

			}  // end if


			/**
			 * Action Hook 'wcaba_custom_extension_items'
			 * allows for hooking other extension-related items in
			 */
			do_action( 'wcaba_custom_extension_items' );

		}  // end if

	}  // end if WooCommerce conditional for settings items


	/**
	 * Display links to active WooCommerce specific themes settings' pages.
	 */
		/** Include plugin file with theme support links */
		if ( defined( 'WCABA_THEMES_DISPLAY' ) && WCABA_THEMES_DISPLAY ) {

			require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-themes.php' );

			/**
			 * Action Hook 'wcaba_custom_theme_items'
			 * allows for hooking other theme-related items in
			 */
			do_action( 'wcaba_custom_theme_items' );

		}  // end if constant check


	/**
	 * Display links to active WooCommerce plugins/extensions settings' pages.
	 */
		/** Include plugin file with plugin support links */
		require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-plugins.php' );


	/** Allow menu items to be filtered, but pass in parent menu item IDs */
	$menu_items = (array) apply_filters(
		'wcaba_filter_menu_items',
		$menu_items, $woogroup_menu_items, $prefix, $woocommercebar, 
						$support, $woocommercesites, $settings, $sgeneralsettings, $semails, $settingsintegration, 
							$s_dynpricing, $s_commissionking, 
						$products, $p_compareproductslite, $p_compareproductspro, $p_csvimportsuite, $coupons, 
							$orders, 
						$reports, $reportssales, $reportsproducts, $reportscategories, $reportscoupons, $reportscustomers, $reportsstock, $shoplink, $shoplinkcheckout, $shoplinkmyaccount, $shoplinkde, 
						$extensions, $extpideal, $extwtdynpricing, $extwtcommissionking, 
						$extwtclickatellsms, $extwtquickbooks, $extwccplite, $extwccppro, $extwtcsvimportsuite, 
						$wcgroup
	);  // end of array


	/**
	 * Add the WooCommerce top-level menu item.
	 */
		/** Filter the main item name */
		$wcaba_main_item_title = apply_filters(
			'wcaba_filter_main_item',
			_x( 'WooCommerce', 'Translators: Main item', 'woocommerce-admin-bar-addition' )
		);

		/** Filter the main item name's tooltip */
		$wcaba_main_item_title_tooltip = apply_filters(
			'wcaba_filter_main_item_tooltip',
			_x(
				'Current Orders - WooCommerce Shop',
				'Translators: Main item - for the tooltip',
				'woocommerce-admin-bar-addition'
			)
		);

		/** Main item URL helpers */

		$wcaba_main_url_orders   = ( current_user_can( 'edit_shop_orders' ) ) ? admin_url( 'edit.php?post_type=shop_order' ) : '#';
		$wcaba_wc_settings_url   = ddw_wcaba_woocommerce_current() ? admin_url( 'admin.php?page=wc-settings' ) : admin_url( 'admin.php?page=woocommerce_settings' );
		$wcaba_main_url_settings = ( current_user_can( 'manage_woocommerce' ) ) ? $wcaba_wc_settings_url : '#';

		/** Filter the main item icon's url */
		$wcaba_main_item_url = apply_filters(
			'wcaba_filter_main_item_url',
			( defined( 'WP_DEBUG') && WP_DEBUG ) ? $wcaba_main_url_settings : $wcaba_main_url_orders
		);	

		/** Filter the main item icon's class/display */
		$wcaba_main_item_icon_display = apply_filters(
			'wcaba_filter_main_item_icon_display',
			'icon-woocommerce'
		);

		/** Add the top-level menu item */
		$wp_admin_bar->add_menu( array(
			'id'    => $woocommercebar,
			'title' => $wcaba_main_item_title,
			'href'  => esc_url( $wcaba_main_item_url ),
			'meta'  => array(
				'class' => $wcaba_main_item_icon_display,
				'title' => $wcaba_main_item_title_tooltip
			)
		) );


	/** Loop through the menu items */
	foreach ( $menu_items as $id => $menu_item ) {
		
		/** Add in the item ID */
		$menu_item[ 'id' ] = $prefix . $id;

		/** Add meta target to each item where it's not already set, so links open in new window/tab */
		if ( ! isset( $menu_item[ 'meta' ][ 'target' ] ) ) {

			$menu_item[ 'meta' ][ 'target' ] = '_blank';

		}  // end if

		/** Add class to links that open up in a new window/tab */
		if ( '_blank' === $menu_item[ 'meta' ][ 'target' ] ) {

			if ( ! isset( $menu_item[ 'meta' ][ 'class' ] ) ) {

				$menu_item[ 'meta' ][ 'class' ] = '';

			}  // end if

			$menu_item[ 'meta' ][ 'class' ] .= $prefix . 'wcaba-new-tab';

		}  // end if

		/** Add menu items */
		$wp_admin_bar->add_menu( $menu_item );

	}  // end foreach


	/**
	 * Action Hook 'wcaba_custom_main_items'
	 * allows for hooking other main items in
	 */
	do_action( 'wcaba_custom_main_items' );


	/**
	 * Check for WordPress version to add resource links group
	 */
	$wp_admin_bar->add_group( array(
		'parent' => $woocommercebar,
		'id'     => $wcgroup,
		'meta'   => array( 'class' => 'ab-sub-secondary' )
	) );


	/** WooCommerce Group: Loop through the group menu items */
	foreach ( $woogroup_menu_items as $id => $woogroup_menu_item ) {
		
		/** WooCommerce Group: Add in the item ID */
		$woogroup_menu_item[ 'id' ] = $prefix . $id;

		/** WooCommerce Group: Add meta target to each item where it's not already set, so links open in new window/tab */
		if ( ! isset( $woogroup_menu_item[ 'meta' ][ 'target' ] ) ) {

			$woogroup_menu_item[ 'meta' ][ 'target' ] = '_blank';

		}  // end if

		/** WooCommerce Group: Add class to links that open up in a new window/tab */
		if ( '_blank' === $woogroup_menu_item[ 'meta' ][ 'target' ] ) {

			if ( ! isset( $woogroup_menu_item[ 'meta' ][ 'class' ] ) ) {

				$woogroup_menu_item[ 'meta' ][ 'class' ] = '';

			}  // end if

			$woogroup_menu_item[ 'meta' ][ 'class' ] .= $prefix . 'wcaba-new-tab';

		}  // end if

		/** WooCommerce Group: Add menu items */
		$wp_admin_bar->add_menu( $woogroup_menu_item );

	}  // end foreach


	/**
	 * Action Hook 'wcaba_custom_group_items'
	 * allows for hooking other WooCommerce Group items in
	 */
	do_action( 'wcaba_custom_group_items' );

}  // end of main function


add_action( 'wp_head', 'ddw_woocommerce_aba_admin_style' );
add_action( 'admin_head', 'ddw_woocommerce_aba_admin_style' );
/**
 * Add the styles for new WordPress Toolbar / Admin Bar entry.
 * 
 * @since  1.0.0
 *
 * @global string $GLOBALS[ 'wp_version' ]
 */
function ddw_woocommerce_aba_admin_style() {

	/** No styles if admin bar is disabled or user is not logged in or items are disabled via constant */
	if ( ! is_admin_bar_showing()
		|| ! is_user_logged_in()
		|| ( defined( 'WCABA_DISPLAY' ) && ! WCABA_DISPLAY )
	) {

		return;

	}  // end if

	if ( version_compare( $GLOBALS[ 'wp_version' ], '3.7.9999', '<=' ) ) {

		/**
		 * Add CSS styles to 'wp_head'/ 'admin_head'
		 */
		$wcaba_main_icon = apply_filters(
			'wcaba_filter_main_icon',
			plugins_url( 'woocommerce-admin-bar-addition/images/wcaba-icon.png', dirname( __FILE__ ) )
		);

		?>
			<style type="text/css">
				#wpadminbar.nojs .ab-top-menu > li.menupop.icon-woocommerce:hover > .ab-item,
				#wpadminbar .ab-top-menu > li.menupop.icon-woocommerce.hover > .ab-item,
				#wpadminbar.nojs .ab-top-menu > li.menupop.icon-woocommerce > .ab-item,
				#wpadminbar .ab-top-menu > li.menupop.icon-woocommerce > .ab-item {
					background-image: url('<?php echo $wcaba_main_icon; ?>');
					background-repeat: no-repeat;
					background-position: 0.85em 50%;
					padding-left: 30px;
				}
			</style>
		<?php

	} else {

		?>
			<style type="text/css">
				#wpadminbar.nojs .ab-top-menu > li.menupop.icon-woocommerce:hover > .ab-item:before,
				#wpadminbar .ab-top-menu > li.menupop.icon-woocommerce.hover > .ab-item:before,
				#wpadminbar.nojs .ab-top-menu > li.menupop.icon-woocommerce > .ab-item:before,
				#wpadminbar .ab-top-menu > li.menupop.icon-woocommerce > .ab-item:before {
					content: "\f174";
					font-family: 'dashicons';
				}
				@media screen and ( max-width: 782px ) {
					#wp-toolbar > ul > li#wp-admin-bar-ddw-woocommerce-admin-bar {
						display: block;
					}
					#wp-toolbar > ul > li#wp-admin-bar-ddw-woocommerce-admin-bar > a {
						margin: 0;
						padding: 0;
						position: relative;
						text-indent: -9999px;
						width: 52px;
					}
					#wp-toolbar > ul > li#wp-admin-bar-ddw-woocommerce-admin-bar > .ab-item:before {
						display: block;
						font: 400 32px/1 dashicons;
						margin: 0;
						text-align: center;
						text-indent: 0;
						top: 7px;
						width: 52px;
					}
					#wp-toolbar > ul > li#wp-admin-bar-ddw-woocommerce-admin-bar ul li .ab-sub-wrapper,
					#wp-toolbar > ul > li#wp-admin-bar-ddw-woocommerce-admin-bar ul#wp-admin-bar-ddw-woocommerce-wcgroup {
						display: none;
					}
				}
			</style>
		<?php

	}  // end if

	?>
		<style type="text/css">

			#wp-admin-bar-ddw-woocommerce-languages-de > .ab-item:before,
			#wp-admin-bar-ddw-woocommerce-wclanguages-de > .ab-item:before,
			#wp-admin-bar-ddw-woocommerce-wclanguages-nl > .ab-item:before,
			#wp-admin-bar-ddw-woocommerce-wclanguages-es > .ab-item:before,
			#wp-admin-bar-ddw-woocommerce-wclanguages-cz > .ab-item:before {
				content: '• ';
				margin-top: -2px;
			}
			#wpadminbar abbr,
			#wpadminbar .wcaba-search-input,
			#wpadminbar .wcaba-search-go {
				color: #666;
				text-shadow: none;
			}
			#wpadminbar a > abbr,
			#wpadminbar .wcaba-search-input,
			#wpadminbar .wcaba-search-go {
				color: #21759b;
			}
			#wpadminbar .wcaba-search-input,
			#wpadminbar .wcaba-search-go {
				background-color: #fff;
			}
			#wpadminbar .wcaba-search-go {
				font-size: 0.8em;
				margin: 0 0 0 2px;
				padding: 1px 4px;
			}
			.wp-admin #wpadminbar .wcaba-search-go {
				margin: 5px 0 0 2px;
			}
		</style>
	<?php

}  // end of function ddw_woocommerce_aba_admin_style


/**
 * Helper functions for custom branding of the plugin
 * Plus: Integrated support for "WooCommerce Branding" (extension) styles/icons/names
 *
 * @since 2.2.0
 */
	/** Include plugin file with special "WC Branding" extension stuff/integration */
	require_once( WCABA_PLUGIN_DIR . 'includes/wcaba-wcbranding.php' );


add_action( 'wp_before_admin_bar_render', 'ddw_woocommerce_aba_admin_tweaks' );
/**
 * In backend, remove "View Order" and "View Coupon" links from toolbar/admin bar
 * Only for the post types "shop_order" and "shop_coupon" as there's no frontend view for both!
 *
 * @since  2.2.0
 *
 * @global mixed $GLOBALS[ 'wp_admin_bar' ]
 */
function ddw_woocommerce_aba_admin_tweaks() {

	//global $wp_admin_bar;

	if ( is_admin() && ( 'shop_order' == get_post_type()
		|| 'shop_coupon' == get_post_type() )
	) {

		$GLOBALS[ 'wp_admin_bar' ]->remove_menu( 'view' );

	}  // end if

}  // end of function ddw_woocommerce_aba_admin_tweaks


/**
 * Returns current plugin's header data in a flexible way.
 *
 * @since  2.6.0
 *
 * @uses   is_admin()
 * @uses   get_plugins()
 * @uses   plugin_basename()
 *
 * @param  $wcaba_plugin_value
 *
 * @return string Plugin data.
 */
function ddw_wcaba_plugin_get_data( $wcaba_plugin_value ) {

	/** Bail early if we are not in wp-admin */
	if ( ! is_admin() ) {
		return;
	}

	/** Include WordPress plugin data */
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	$wcaba_plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$wcaba_plugin_file = basename( ( __FILE__ ) );

	return $wcaba_plugin_folder[ $wcaba_plugin_file ][ $wcaba_plugin_value ];

}  // end of function ddw_wcaba_plugin_get_data