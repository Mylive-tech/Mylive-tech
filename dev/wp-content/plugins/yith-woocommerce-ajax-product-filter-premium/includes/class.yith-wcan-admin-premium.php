<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Admin_Premium' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Admin_Premium extends YITH_WCAN_Admin {

        /**
         * Construct
         *
         * @param $version Plugin version
         */
        public function __construct( $version ) {
            parent::__construct( YITH_WCAN_VERSION );

            /* Admin Panel */
            add_filter( 'yith_wcan_settings_tabs', array( $this, 'add_settings_tabs' ) );
            add_action( 'yith_wcan_after_option_panel', array( $this, 'premium_panel_init' ), 10, 1 );

            /* Register plugin to licence/update system */
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            /* Premium Options */
            add_filter( 'yith_wcan_panel_frontend_options', array( $this, 'panel_frontend_options' ) );
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @param $tabs Tabs array
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function add_settings_tabs( $tabs ) {
            unset( $tabs['premium'] );

            $to_add = array(
                'general'   => __( 'Settings', 'yith-woocommerce-ajax-navigation' ),
                'seo'       => __( 'SEO', 'yith-woocommerce-ajax-navigation' ),
            );

            return array_merge( $tabs, $to_add );
        }
        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation() {
            if( ! class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once 'plugin-fw/licence/lib/yit-licence.php';
                require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register( YITH_WCAN_INIT, YITH_WCAN_SECRET_KEY, YITH_WCAN_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates() {
            if( ! class_exists( 'YIT_Upgrade' ) ) {
                require_once( 'plugin-fw/lib/yit-upgrade.php' );
            }
            YIT_Upgrade()->register( YITH_WCAN_SLUG, YITH_WCAN_INIT );
        }

        /**
         * Create default panel option db record
         *
         * @param $args The panel args array
         *
         * @return   void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function premium_panel_init( $args ) {
            ! get_option( YITH_WCAN()->admin->_main_panel_option ) && add_option( YITH_WCAN()->admin->_main_panel_option, YITH_WCAN()->admin->_panel->get_default_options() );
        }

        /**
         * Add Premium Frontend Options
         *
         * @param $options the standard options array
         *
         * @return   void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function panel_frontend_options( $options ){
            $options['frontend']['settings'][] = array( 'type' => 'open' );

            $options['frontend']['settings'][] =  array(
                'name' => __( 'Widget Title Tag', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Put here the HTML tag for the widget title', 'yith-woocommerce-ajax-navigation' ) . ' (Default: <strong>h3</strong>)',
                'id'   => 'yith_wcan_ajax_widget_title_class',
                'type' => 'text',
                'std'  => 'h3.widget-title'
            );

            $options['frontend']['settings'][] = array( 'type' => 'close' );

            return $options;
        }
    }
}
