<?php
/*
Plugin Name: WooCommerce Quick View
Plugin URI: http://woothemes.com/woocommerce
Description: Let customers quick view products and add them to their cart from a lightbox. Supports variations. Requires WC 2.0+
Version: 1.1.4
Author: Mike Jolley
Author URI: http://mikejolley.com

	Copyright: © 2009-2015 WooThemes.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '619c6e57ce72c49c4b57e15b06eddb65', '187509' );

/**
 * Plugin page links
 */
function wc_quick_view_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="http://support.woothemes.com/">' . __( 'Support', 'wc_quick_view' ) . '</a>',
		'<a href="http://docs.woothemes.com/document/woocommerce-quick-view/">' . __( 'Docs', 'wc_quick_view' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_quick_view_plugin_links' );

if ( is_woocommerce_active() ) {

	/**
	 * Localisation
	 **/
	load_plugin_textdomain( 'wc_quick_view', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

	/**
	 * WC_Quick_View class
	 **/
	if ( ! class_exists( 'WC_Quick_View' ) ) {

		class WC_Quick_View {

			private $quick_view_trigger;

			/**
			 * __construct function.
			 */
			public function __construct() {

				// Default option
				add_option( 'quick_view_trigger', 'button' );

				// Load options
				$this->quick_view_trigger = get_option( 'quick_view_trigger' );

				// Scripts
				add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 11 );

				// Show a product via API
				add_action( 'woocommerce_api_wc_quick_view', array( $this, 'quick_view' ) );

				// Settings
				add_filter( 'woocommerce_general_settings' , array( $this, 'settings' ) );
			}

			/**
			 * settings function.
			 *
			 * @param array $settings
			 */
			public function settings( $settings ) {

				$settings[] = array(
					'name' => __( 'Quick View', 'wc_quick_view' ),
					'type' => 'title',
					'desc' => 'The following options are used to configure the Quick View extension.',
					'id'   => 'wc_quick_view'
				);

				$settings[] = array(
					'id'      => 'quick_view_trigger',
					'name'    => __( 'Quick View Trigger', 'wc_quick_view' ),
					'desc'    => __( 'Choose what event should trigger quick view', 'wc_quick_view' ),
					'type'    => 'select',
					'options' => array(
						'button'        => __( 'Quick View Button', 'wc_quick_view' ),
						//'thumbnail'     => __( 'Product Thumbnail', 'wc_quick_view' ),
						'non_ajax'      => __( 'Any non-ajax add to cart button', 'wc_quick_view' )
					)
				);

				$settings[] = array(
					'type' => 'sectionend',
					'id'   => 'wc_quick_view'
				);

				return $settings;
			}

			/**
			 * scripts function.
			 */
			public function scripts() {
				global $woocommerce;

				do_action( 'wc_quick_view_enqueue_scripts' );

				wp_enqueue_script( 'prettyPhoto', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.min.js', array( 'jquery' ), $woocommerce->version, true );
				wp_enqueue_script( 'wc-add-to-cart-variation' );
				wp_enqueue_style( 'woocommerce_prettyPhoto_css', $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css' );
				wp_enqueue_style( 'wc_quick_view', plugins_url( 'assets/css/style.css' , __FILE__ ) );

				switch ( $this->quick_view_trigger ) {
					case 'non_ajax' :
						$ajax_cart_en = get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes' ? true : false;

						if ( $ajax_cart_en ) {
							$selector = "'.product a.button:not(.add_to_cart_button), .product a.button.product_type_addons, .product a.product_type_variable'";
						} else {
							$selector = "'.product a.button'";
							add_filter( 'add_to_cart_url', array( $this, 'get_quick_view_url' ) );
						}

						add_filter( 'addons_add_to_cart_url', array( $this, 'get_quick_view_url' ) );
						add_filter( 'variable_add_to_cart_url', array( $this, 'get_quick_view_url' ) );
						add_filter( 'grouped_add_to_cart_url', array( $this, 'get_quick_view_url' ) );
						add_filter( 'external_add_to_cart_url', array( $this, 'get_quick_view_url' ) );
						add_filter( 'bundle_add_to_cart_url', array( $this, 'get_quick_view_url' ), 11 );
						add_filter( 'bto_add_to_cart_url', array( $this, 'get_quick_view_url' ), 11 );
						add_filter( 'not_purchasable_url', array( $this, 'get_quick_view_url' ) );
						add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'get_quick_view_url_for_product' ), 10, 2 );
					break;
					default :
						$selector = "'a.quick-view-button'";

						add_action( 'woocommerce_after_shop_loop_item', array( $this, 'quick_view_button' ), 5 );
					break;
				}

				$selector = apply_filters( 'quick_view_selector', $selector );

				if ( version_compare( WOOCOMMERCE_VERSION, '2.3.0', '<' ) ) {
					$quantity_input = "jQuery('div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)').addClass('buttons_added').append(\"<input type='button' value='+' class='plus' />\").prepend(\"<input type='button' value='-' class='minus' />\");";
				} else {
					$quantity_input = '';
				}

				$js = "
					$(document).on( 'click', " . $selector . ", function() {

						$.fn.prettyPhoto({
							social_tools: false,
							theme: 'pp_woocommerce pp_woocommerce_quick_view',
							opacity: 0.8,
							modal: false,
							horizontal_padding: 40,
							changepicturecallback: function() {
								jQuery('.variations_form').wc_variation_form();
								jQuery('.variations_form').trigger('wc_variation_form');
								jQuery('.variations_form .variations select').change();
								" . $quantity_input . "
								var container = jQuery('.quick-view-content').closest('.pp_content_container');
								jQuery('body').trigger('quick-view-displayed');
							}
						});

						$.prettyPhoto.open( $(this).attr( 'href' ) );

						return false;
					});
				";

				if ( function_exists( 'wc_enqueue_js' ) ) {
					wc_enqueue_js( $js );
				} else {
					$woocommerce->add_inline_js( $js );
				}
			}

			/**
			 * get_quick_view_url function.
			 */
			public function get_quick_view_url() {
				global $product;

				$link = add_query_arg(
					apply_filters( 'woocommerce_loop_quick_view_link_args', array(
						'wc-api'  => 'WC_Quick_View',
						'product' => $product->id,
						'width'   => '90%',
						'height'  => '90%',
						'ajax'    => 'true'
					) ),
					home_url( '/' )
				);

				return $link;
			}

			public function get_quick_view_url_for_product( $url, $product ) {
				$url = $this->get_quick_view_url();

				return $url;
			}

			/**
			 * quick_view function.
			 */
			public function quick_view() {
				global $woocommerce, $post;

				$product_id = absint( $_GET['product'] );

				if ( $product_id ) {

					// Get product ready
					$post = get_post( $product_id );

					setup_postdata( $post );

					woocommerce_get_template(
						'quick-view.php',
						array(),
						'woocommerce-quick-view',
						untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/'
					);
				}

				exit;
			}

			/**
			 * quick_view_button function.
			 */
			public function quick_view_button() {
				woocommerce_get_template(
					'loop/quick-view-button.php',
					array( 'link' => $this->get_quick_view_url() ),
					'woocommerce-quick-view',
					untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/'
				);
			}
		}

		$GLOBALS['WC_Quick_View'] = new WC_Quick_View();
	}
}
