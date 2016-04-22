<?php
/**
 * Frontend class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Frontend_Premium' ) ) {
    /**
     * Frontend class.
     * The class manage all the frontend behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Frontend_Premium extends YITH_WCAN_Frontend {

        public function __construct( $version ) {
            parent::__construct( $version );

            add_action( 'wp_head', array( $this, 'meta_robot_generator' ) );
        }

        /**
         * Enqueue Script for Premium version
         *
         * @since 2.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com
         * @return void
         */
        public function enqueue_styles_scripts(){
            parent::enqueue_styles_scripts();

            if ( yith_wcan_can_be_displayed() ) {
                $suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                $loader_url = YITH_WCAN_URL . 'assets/images/ajax-loader.gif';

                $options = array(
                    'ajax_wc_price_filter'                  => yith_wcan_get_option( 'yith_wcan_enable_ajax_price_filter' ),
                    'wc_price_filter_slider'                => yith_wcan_get_option( 'yith_wcan_enable_ajax_price_filter_slider' ),
                    'wc_price_filter_slider_in_ajax'        => yith_wcan_get_option( 'yith_wcan_enable_slider_in_ajax' ),
                    'wc_price_filter_dropdown'              => yith_wcan_get_option( 'yith_wcan_enable_dropdown_price_filter' ),
                    'wc_price_filter_dropdown_style'        => yith_wcan_get_option( 'yith_wcan_dropdown_style' ),
                    'wc_price_filter_dropdown_widget_class' => yith_wcan_get_option( 'yith_wcan_ajax_widget_title_class', 'h3.widget-title' ),
                    'price_filter_dropdown_class'           => apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' )
                );

                wp_enqueue_script( 'yith_wcan_frontend-premium', YITH_WCAN_URL . 'assets/js/yith-wcan-frontend-premium' . $suffix . '.js', array( 'jquery' ), $this->version, true );
                wp_localize_script( 'yith_wcan_frontend', 'yith_wcan_frontend', array( 'loader_url' => yith_wcan_get_option( 'yith_wcan_ajax_loader', $loader_url ) ) );
                wp_localize_script( 'yith_wcan_frontend-premium', 'yith_wcan_frontend_premium', $options );
            }
        }

        /**
         * Add Meta Robots in the <head> section
         *
         * @since 2.4.1
         * @author Andrea Grillo <andrea.grillo@yithemes.com
         * @return void
         */
        public function meta_robot_generator(){
            global $_chosen_attributes;
            $enable_seo = 'yes' == yith_wcan_get_option( 'yith_wcan_enable_seo' ) ? true : false;
            $has_filtered_url = ! empty( $_chosen_attributes ) || ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) || isset( $_GET['product_tag'] );
            if( $enable_seo && yith_wcan_can_be_displayed() && $has_filtered_url && ( is_product_tag() || is_product_taxonomy() || is_product_category() || is_shop() ) ){
                $meta_options = yith_wcan_get_option( 'yith_wcan_seo_value', 'noindex-follow' );
                printf( '<meta name="robots" content="%s">', str_replace( '-', ', ', $meta_options ) );
            }

        }
    }
}
