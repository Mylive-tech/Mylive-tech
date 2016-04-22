<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Stock_On_Sale_Widget' ) ) {
    /**
     * YITH_WCAN_Sort_By_Widget
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Stock_On_Sale_Widget extends WP_Widget {

        protected $_id_base = 'yith-woo-ajax-navigation-stock-on-sale';

        public function __construct() {
            $widget_ops  = array( 'classname' => 'yith-wcan-stock-on-sale', 'description' => __( 'Display on sale and in stock WooCommerce products', 'yith-woocommerce-ajax-navigation' ) );
            $control_ops = array( 'width' => 400, 'height' => 350 );
            parent::__construct( $this->_id_base, __( 'YITH WooCommerce Ajax In Stock/On Sale Filters', 'yith-woocommerce-ajax-navigation' ), $widget_ops, $control_ops );

            if ( ! is_admin() ) {
                $sidebars_widgets = wp_get_sidebars_widgets();
                $regex            = '/^' . $this->_id_base . '-\d+/';

                foreach ( $sidebars_widgets as $sidebar => $widgets ) {
                    if ( is_array( $widgets ) ) {
                        foreach ( $widgets as $widget ) {
                            if ( preg_match( $regex, $widget ) ) {
                                $this->actions();
                                break;
                            }
                        }
                    }
                }
            }
        }

        public function actions(){
            add_action( 'woocommerce_product_query', array( $this, 'show_in_stock_products' ) );
            add_filter( 'woocommerce_layered_nav_link', array( $this, 'stock_on_sale_filter_args' ),15 );
            add_filter( 'loop_shop_post_in', array( $this, 'show_on_sale_products' ) );

            /* === Dropdown === */
            add_filter( "yith_widget_title_stock_onsale", array( $this, 'widget_title' ), 10, 3 );
        }

        public function widget( $args, $instance ) {
            if( empty( $instance['onsale'] ) && empty( $instance['instock'] ) ) {
                return;
            }

            $_attributes_array = yit_wcan_get_product_taxonomy();

            if ( apply_filters( 'yith_wcan_is_search', is_search() ) ) {
                return;
            }

            if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ) ) ) {
                return;
            }

            extract( $instance );
            extract( $args );

            $onsale_text   = apply_filters( 'yith_wcan_onsale_text', __( 'Show only "On Sale" products', 'yith-woocommerce-ajax-navigation' ) );
            $instock_text  = apply_filters( 'yith_wcan_instock_text', __( 'Show only "In Stock" products', 'yith-woocommerce-ajax-navigation' ) );

            $onsale_class  = apply_filters( 'yith_wcan_onsale_class', ! empty( $_GET['onsale_filter'] )  ? 'yith-wcan-onsale-button active' : 'yith-wcan-onsale-button' );
            $instock_class = apply_filters( 'yith_wcan_onsale_class', ! empty( $_GET['instock_filter'] ) ? 'yith-wcan-instock-button active' : 'yith-wcan-instock-button' );

            echo $before_widget;

            $title = apply_filters( 'widget_title', $title );

            if ( $title ) {
                echo $before_title . apply_filters( 'yith_widget_title_stock_onsale', $title, $instance, $this->number ) . $after_title;
            }

            echo '<ul class="yith-wcan-stock-on-sale">';

            if( $instance['onsale'] ){
                $filter_link = ! empty( $_GET['onsale_filter'] ) ? remove_query_arg( 'onsale_filter' ) : add_query_arg( array( 'onsale_filter' => 1 ) );
                echo '<li><a href="' . esc_url( $filter_link ) . '" class="' . $onsale_class . '">' . $onsale_text . '</a></li>';
            }

            if( $instance['instock'] ){
                $instock_link = ! empty( $_GET['instock_filter'] ) ? remove_query_arg( 'instock_filter' ) : add_query_arg( array( 'instock_filter' => 1 ) );
                echo '<li><a href="' . esc_url( $instock_link ) . '" class="' . $instock_class . '">' . $instock_text . '</a></li>';
            }

            echo '</ul>';
            echo $after_widget;
        }


        public function form( $instance ) {
            global $woocommerce;

            $defaults = array(
                'title'         => _x( 'Stock/On sale', 'Product sorting', 'yith-woocommerce-ajax-navigation' ),
                'onsale'        => 1,
                'instock'       => 1,
                'dropdown'      => 0,
                'dropdown_type' => 'open'
            );

            $instance = wp_parse_args( (array) $instance, $defaults );
            ?>

            <p>
                <label>
                    <strong><?php _e( 'Title', 'yith-woocommerce-ajax-navigation' ) ?>:</strong><br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
                </label>
            </p>

             <p id="yit-wcan-onsale-<?php echo $instance['onsale'] ? 'enabled' : 'disabled' ?>" class="yith-wcan-onsale">
                <label for="<?php echo $this->get_field_id( 'onsale' ); ?>"><?php _e( 'Show "On Sale" filter', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'onsale' ); ?>" name="<?php echo $this->get_field_name( 'onsale' ); ?>" value="1" <?php checked( $instance['onsale'], 1, true )?> class="yith-wcan-onsalen-check widefat" />
                </label>
            </p>

             <p id="yit-wcan-instock-<?php echo $instance['instock'] ? 'enabled' : 'disabled' ?>" class="yith-wcan-instock">
                <label for="<?php echo $this->get_field_id( 'instock' ); ?>"><?php _e( 'Show "In Stock" filter', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'instock' ); ?>" name="<?php echo $this->get_field_name( 'instock' ); ?>" value="1" <?php checked( $instance['instock'], 1, true )?> class="yith-wcan-instockn-check widefat" />
                </label>
            </p>

              <p id="yit-wcan-dropdown" class="yith-wcan-dropdown">
                <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Show widget dropdown', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" value="1" <?php checked( $instance['dropdown'], 1, true )?> class="yith-wcan-dropdown-check widefat" />
                </label>
            </p>

            <p id="yit-wcan-dropdown-type" class="yit-wcan-dropdown-type-<?php echo $instance['dropdown_type'] ?>" style="display: <?php echo ! empty( $instance['dropdown'] ) ? 'block' : 'none'?>;">
                <label for="<?php echo $this->get_field_id( 'dropdown_type' ); ?>"><strong><?php _ex( 'Dropdown style:', 'Select if you want to show the widget open or closed', 'yith-woocommerce-ajax-navigation' ) ?></strong></label>
                <select class="yith-wcan-dropdown-type widefat" id="<?php echo esc_attr( $this->get_field_id( 'dropdown_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown_type' ) ); ?>">
                    <option value="open" <?php selected( 'open', $instance['dropdown_type'] ) ?>> <?php _e( 'Opened', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                    <option value="close"  <?php selected( 'close', $instance['dropdown_type'] ) ?>>  <?php _e( 'Closed', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                </select>
            </p>
             <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('change', '.yith-wcan-dropdown-check', function () {
                        jQuery.select_dropdown(jQuery(this));
                    });
                });
            </script>
        <?php
        }

        public function stock_on_sale_filter_args( $link ){
            if ( ! empty( $_GET['onsale_filter'] ) ) {
                $link = add_query_arg( array( 'onsale_filter' => $_GET['onsale_filter'] ), $link );
            }

            if ( ! empty( $_GET['instock_filter'] ) ) {
                $link = add_query_arg( array( 'instock_filter' => $_GET['instock_filter'] ), $link );
            }

            return $link;
        }

        public function show_in_stock_products( $q ) {
            $current_widget_options = $this->get_settings();

            if ( ! empty( $_GET['instock_filter'] ) && ! empty( $current_widget_options[ $this->number ]['instock'] ) ) {
                //in stock products
                $meta_query = array(
                    array(
                        'key'     => '_stock_status',
                        'value'   => 'instock',
                        'compare' => '='
                    )
                );

                $q->set( 'meta_query', array_merge( WC()->query->get_meta_query(), $meta_query ) );
            }
        }

        public function show_on_sale_products( $ids ) {
            $current_widget_options = $this->get_settings();

            if ( ! empty( $_GET['onsale_filter'] ) && ! empty( $current_widget_options[$this->number]['onsale'] ) ) {
                $ids = array_merge( $ids, wc_get_product_ids_on_sale() );
            }
            return $ids;
        }

        public function update( $new_instance, $old_instance ) {

            $instance = $old_instance;

            $instance['title']          = strip_tags( $new_instance['title'] );
            $instance['onsale']         = isset( $new_instance['onsale'] ) ? 1 : 0;
            $instance['instock']        = isset( $new_instance['instock'] ) ? 1 : 0;
            $instance['dropdown']       = isset( $new_instance['dropdown'] ) ? 1 : 0;
            $instance['dropdown_type']  = $new_instance['dropdown_type'];

            return $instance;
        }

         public function widget_title( $title, $instance, $id_base ) {
            $span_class = apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' );
            $title = ! empty( $instance['dropdown'] ) ? $title . '<span class="' . $span_class .'" data-toggle="' . $instance['dropdown_type'] . '"></span>' : $title;

            return $title;
        }
    }
}