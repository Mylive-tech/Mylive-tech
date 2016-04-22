<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( !defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCAN_Navigation_Widget_Premium' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation Widget
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Navigation_Widget_Premium extends YITH_WCAN_Navigation_Widget {

        public function form( $instance ) {
            /* === Add Premium Widget Types === */
            add_filter( 'yith_wcan_widget_types', array( $this, 'premium_widget_types' ) );
            add_filter( 'yith-wcan-attribute-list-class', array( $this, 'set_attribute_style' ) );

            parent::form( $instance );

            $defaults = array(
                'type'            => 'list',
                'style'           => 'square',
                'show_count'      => 0,
                'dropdown'        => 0,
                'dropdown_type'   => 'open',
                'tags_list'       => array(),
                'tags_list_query' => 'exclude'
            );

            $instance = wp_parse_args( (array) $instance, $defaults );
            $terms    = get_terms( 'product_tag', array( 'hide_empty' => false ) ); ?>

            <div class="yit-wcan-widget-tag-list <?php echo $instance['type'] ?>">
                <?php

                if ( is_wp_error( $terms ) || empty( $terms ) ) {
                    _e( 'No tags found.', 'yith-woocommerce-ajax-navigation' );
                }

                else { ?>
                    <strong><?php _ex( 'Tag List', 'Admin: Section title', 'yith-woocommerce-ajax-navigation' ) ?></strong>
                    <select class="yith_wcan_tags_query_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'tags_list_query' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tags_list_query' ) ); ?>">
                        <option value="include" <?php selected( 'include', $instance['tags_list_query'] ) ?>> <?php _e( 'Show Selected', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                        <option value="exclude" <?php selected( 'exclude', $instance['tags_list_query'] ) ?>>  <?php _e( 'Hide Selected', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                    </select>
                    <div class="yith-wcan-select-option">
                        <a href="#" class="select-all">
                            <?php _e( 'Select all', 'yith-woocommerce-ajax-navigation' ) ?>
                        </a>
                        <a href="#" class="unselect-all">
                            <?php _e( 'Unselect all', 'yith-woocommerce-ajax-navigation' ) ?>
                        </a>
                        <small class="yith-wcan-admin-note"><?php echo '* ' . _x( 'Note: tags with no assigned products will not be showed in the front end', 'Admin: user note', 'yith-woocommerce-ajax-navigation' ) ?></small>
                    </div>
                    <div class="yith_wcan_select_tag_wrapper">
                        <table class="yith_wcan_select_tag">
                            <thead>
                            <tr>
                                <td><?php _e( 'Tag name', 'yith-woocommerce-ajax-navigation' ) ?></td>
                                <td><?php _e( 'Count', 'yith-woocommerce-ajax-navigation' ) ?>
                                    <small class="yith-wcan-admin-note-star">(*)</small>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ( $terms as $term ) : ?>
                                <tr>
                                    <td class="term_name">
                                        <?php $checked = is_array( $instance['tags_list'] ) && array_key_exists( $term->term_id, $instance['tags_list'] ) ? 'checked' : ''; ?>
                                        <input type="checkbox" value="<?php echo $term->slug ?>" name="<?php echo esc_attr( $this->get_field_name( 'tags_list' ) ); ?>[<?php echo $term->term_id; ?>]" class="<?php echo esc_attr( $this->get_field_name( 'tags_list' ) ); ?> yith_wcan_tag_list_checkbox" id="<?php echo esc_attr( $this->get_field_id( 'tags_list' ) ); ?>" <?php echo $checked; ?>/>
                                        <label for=""><?php echo $term->name; ?></label>
                                    </td>
                                    <td class="term_count">
                                        <?php echo $term->count; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <p id="yit-wcan-style" class="yit-wcan-style-<?php echo $instance['type'] ?>">
                <label for="<?php echo $this->get_field_id( 'style' ); ?>">
                    <strong><?php _ex( 'Color Style:', 'Select if you want to show round color box or square color box', 'yith-woocommerce-ajax-navigation' ) ?></strong>
                </label>
                <select class="yith_wcan_style widefat" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
                    <option value="square" <?php selected( 'square', $instance['style'] ) ?>> <?php _e( 'Square', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                    <option value="round" <?php selected( 'round', $instance['style'] ) ?>>  <?php _e( 'Round', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                </select>
            </p>

            <p id="yit-wcan-show-count" class="yit-wcan-show-count-<?php echo $instance['type'] ?>">
                <label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e( 'Hide product count', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1" <?php checked( $instance['show_count'], 1, true ) ?> class="widefat" />
                </label>
            </p>

            <p id="yit-wcan-dropdown-<?php echo $instance['type'] ?>" class="yith-wcan-dropdown">
                <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Show widget dropdown', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" value="1" <?php checked( $instance['dropdown'], 1, true ) ?> class="yith-wcan-dropdown-check widefat" />
                </label>
            </p>

            <p id="yit-wcan-dropdown-type" class="yit-wcan-dropdown-type-<?php echo $instance['type'] ?>" style="display: <?php echo !empty( $instance['dropdown'] ) ? 'block' : 'none' ?>;">
                <label for="<?php echo $this->get_field_id( 'dropdown_type' ); ?>"><strong><?php _ex( 'Dropdown style:', 'Select if you want to show the widget open or closed', 'yith-woocommerce-ajax-navigation' ) ?></strong></label>
                <select class="yith-wcan-dropdown-type widefat" id="<?php echo esc_attr( $this->get_field_id( 'dropdown_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown_type' ) ); ?>">
                    <option value="open" <?php selected( 'open', $instance['dropdown_type'] ) ?>> <?php _e( 'Opened', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                    <option value="close" <?php selected( 'close', $instance['dropdown_type'] ) ?>>  <?php _e( 'Closed', 'yith-woocommerce-ajax-navigation' ) ?> </option>
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

        public function update( $new_instance, $old_instance ) {

            $instance = parent::update( $new_instance, $old_instance );

            $instance['style']           = $new_instance['style'];
            $instance['show_count']      = isset( $new_instance['show_count'] ) ? 1 : 0;
            $instance['dropdown']        = isset( $new_instance['dropdown'] ) ? 1 : 0;
            $instance['dropdown_type']   = $new_instance['dropdown_type'];
            $instance['tags_list']       = !empty( $new_instance['tags_list'] ) ? $new_instance['tags_list'] : array();
            $instance['tags_list_query'] = isset( $new_instance['tags_list_query'] ) ? $new_instance['tags_list_query'] : 'include';

            return $instance;
        }

        public function widget( $args, $instance ) {
            add_filter( "{$args['widget_id']}-li_style", array( $this, 'color_and_label_style' ), 10, 2 );
            add_filter( "{$args['widget_id']}-show_product_count", array( $this, 'show_product_count' ), 10, 2 );
            add_filter( "yith_widget_title_ajax_navigation", array( $this, 'widget_title' ), 10, 3 );
            add_action( 'yith_wcan_widget_display_multicolor', array( $this, 'show_premium_widget' ), 10, 5 );
            add_action( 'yith_wcan_widget_display_categories', array( $this, 'show_premium_widget' ), 10, 5 );

            /* === Tag & Brand Filter === */
            $is_attribute = ( 'tags' == $instance['type'] || 'brands' == $instance['type'] ) ? '__return_false' : '__return_true';
            add_filter( 'yith_wcan_get_terms_params', array( $this, 'get_terms_params' ), 10, 3 );
            add_filter( 'yith_wcan_display_type_list', array( $this, 'add_display_type_case' ) );
            add_filter( 'yith_wcan_list_type_query_arg', array( $this, 'type_query_args' ), 10, 3 );
            add_filter( 'yith_wcan_term_param_uri', array( $this, 'term_param_uri' ), 10, 3 );
            add_filter( 'yith_wcan_list_type_current_widget_check', array( $this, 'filter_current_widget' ), 10, 4 );
            add_filter( 'yith_wcan_is_attribute_check', $is_attribute );
            add_filter( 'yith_wcan_list_filter_operator', array( $this, 'tag_filter_operator' ), 10, 2 );

            if ( 'tags' == $instance['type'] ) {
                $query_option = isset( $instance['tags_list_query'] ) ? $instance['tags_list_query'] : 'include';
                add_filter( "yith_wcan_{$query_option}_terms", array( $this, 'yith_wcan_include_exclude_terms' ), 10, 2 );
                add_filter( 'yith_wcan_list_filter_query_product_tag', array( $this, 'yith_wcan_list_filter_query_product_tag' ) );
            }

            parent::widget( $args, $instance );
        }

        public function color_and_label_style( $li_style, $instance ) {

            if ( !empty( $instance['style'] ) && 'round' == $instance['style'] ) {
                $li_style .= 'border-radius: 50%;';
            }

            return $li_style;
        }

        public function show_product_count( $show, $instance ) {
            return empty( $instance['show_count'] ) ? true : false;
        }

        public function widget_title( $title, $instance, $id_base ) {
            $span_class = apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' );
            $title      = !empty( $instance['dropdown'] ) ? $title . '<span class="' . $span_class . '" data-toggle="' . $instance['dropdown_type'] . '"></span>' : $title;

            return $title;
        }

        public function premium_widget_types( $types ) {
            $types['categories'] = __( 'Categories', 'yith-woocommerce-ajax-navigation' );
            $types['multicolor'] = __( 'BiColor', 'yith-woocommerce-ajax-navigation' );
            $types['tags']       = __( 'Tag', 'yith-woocommerce-ajax-navigation' );

            if ( defined( 'YITH_WCBR_PREMIUM_INIT' ) && YITH_WCBR_PREMIUM_INIT ) {
                $types['brands'] = __( 'Brand', 'yith-woocommerce-ajax-navigation' );
            }
            return $types;
        }

        public function show_premium_widget( $args, $instance, $display_type, $terms, $taxonomy ) {
            global $_chosen_attributes, $woocommerce;
            extract( $args );

            $_attributes_array = yit_wcan_get_product_taxonomy();

            if ( apply_filters( 'yith_wcan_is_search', is_search() ) ) {
                return;
            }

            if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ) ) ) {
                return;
            }

            $current_term    = $_attributes_array && is_tax( $_attributes_array ) ? get_queried_object()->term_id : '';
            $current_tax     = $_attributes_array && is_tax( $_attributes_array ) ? get_queried_object()->taxonomy : '';
            $title           = apply_filters( 'yith_widget_title_ajax_navigation', ( isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '' ), $instance, $this->id_base );
            $query_type      = isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
            $display_type    = isset( $instance['type'] ) ? $instance['type'] : 'list';
            $is_child_class  = 'yit-wcan-child-terms';
            $is_chosen_class = 'chosen';
            $terms_type_list = ( isset( $instance['display'] ) && ( $display_type == 'list' || $display_type == 'select' ) ) ? $instance['display'] : 'all';

            $instance['attribute'] = empty( $instance['attribute'] ) ? '' : $instance['attribute'];

            if ( 'multicolor' == $display_type ) {
                // List display
                echo "<ul class='yith-wcan-color yith-wcan yith-wcan-group'>";

                foreach ( $terms as $term ) {

                    // Get count based on current view - uses transients
                    $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                    //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                    $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                    set_transient( $transient_name, $_products_in_term );
                    //}

                    $option_is_set = ( isset( $_chosen_attributes[$taxonomy] ) && in_array( $term->term_id, $_chosen_attributes[$taxonomy]['terms'] ) );

                    // If this is an AND query, only show options with count > 0
                    if ( $query_type == 'and' ) {

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->filtered_product_ids ) );

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        if ( $count > 0 && $current_term !== $term->term_id ) {
                            add_filter( 'yith_wcan_found_taxonomy', '__return_true' );
                        }

                        if ( $count == 0 && !$option_is_set ) {
                            continue;
                        }

                        // If this is an OR query, show all options so search can be expanded
                    }
                    else {

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->unfiltered_product_ids ) );

                        if ( $count > 0 ) {
                            add_filter( 'yith_wcan_found_taxonomy', '__return_true' );
                        }
                    }

                    $arg = 'filter_' . sanitize_title( $instance['attribute'] );

                    $current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();

                    if ( !is_array( $current_filter ) ) {
                        $current_filter = array();
                    }

                    $current_filter = array_map( 'esc_attr', $current_filter );

                    if ( !in_array( $term->term_id, $current_filter ) ) {
                        $current_filter[] = $term->term_id;
                    }

                    $link = yit_get_woocommerce_layered_nav_link();

                    // All current filters
                    if ( $_chosen_attributes ) {
                        foreach ( $_chosen_attributes as $name => $data ) {
                            if ( $name !== $taxonomy ) {

                                // Exclude query arg for current term archive term
                                while ( in_array( $current_term, $data['terms'] ) ) {
                                    $key = array_search( $current_term, $data );
                                    unset( $data['terms'][$key] );
                                }

                                // Remove pa_ and sanitize
                                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                                if ( !empty( $data['terms'] ) ) {
                                    $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                                }

                                if ( $data['query_type'] == 'or' ) {
                                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                                }
                            }
                        }
                    }

                    // Min/Max
                    if ( isset( $_GET['min_price'] ) ) {
                        $link = add_query_arg( 'min_price', $_GET['min_price'], $link );
                    }

                    if ( isset( $_GET['max_price'] ) ) {
                        $link = add_query_arg( 'max_price', $_GET['max_price'], $link );
                    }

                    if ( isset( $_GET['product_tag'] ) ) {
                        $link = add_query_arg( 'product_tag', urlencode( $_GET['product_tag'] ), $link );
                    }

                    // Current Filter = this widget
                    if ( isset( $_chosen_attributes[$taxonomy] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[$taxonomy]['terms'] ) ) {

                        $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                        // Remove this term is $current_filter has more than 1 term filtered
                        if ( sizeof( $current_filter ) > 1 ) {
                            $current_filter_without_this = array_diff( $current_filter, array( $term->term_id ) );
                            $link                        = add_query_arg( $arg, implode( ',', $current_filter_without_this ), $link );
                        }
                    }
                    else {
                        $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';
                        $link  = add_query_arg( $arg, implode( ',', $current_filter ), $link );
                    }

                    // Search Arg
                    if ( get_search_query() ) {
                        $link = add_query_arg( 's', get_search_query(), $link );
                    }

                    // Post Type Arg
                    if ( isset( $_GET['post_type'] ) ) {
                        $link = add_query_arg( 'post_type', $_GET['post_type'], $link );
                    }

                    // Query type Arg
                    if ( $query_type == 'or' && !( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                        $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                    }

                    $term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );

                    if ( !empty( $instance['multicolor'][$term_id] ) && is_array( $instance['multicolor'][$term_id] ) && !empty( $instance['multicolor'][$term_id][0] ) ) {

                        $a_style   = '';
                        $is_single = false;
                        $a_class   = '';

                        if ( empty( $instance['multicolor'][$term_id][1] ) ) {
                            $a_style   = apply_filters( "{$args['widget_id']}-a_style", 'background-color:' . $instance['multicolor'][$term_id][0] . ';', $instance );
                            $is_single = true;
                            $a_class   = 'singlecolor';
                        }

                        else {
                            $color_1_style = 'border-color: ' . $instance['multicolor'][$term_id][0] . ' transparent;';
                            $color_2_style = 'border-color: ' . $instance['multicolor'][$term_id][1] . ' transparent;';
                            $a_class       = 'multicolor ' . $instance['style'];
                        }

                        echo '<li ' . $class . '>';

                        echo ( $count > 0 || $option_is_set ) ? '<a class="' . $a_class . '" style="' . $a_style . '" href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '" title="' . $term->name . '" >' : '<span style="background-color:' . $instance['multicolor'][$term_id][0] . ';" >';

                        if ( !$is_single ) {
                            echo '<span class="multicolor color-1 ' . $instance['style'] . '" style=" ' . $color_1_style . ' "></span>';
                            echo '<span class="multicolor color-2 ' . $instance['style'] . '" style=" ' . $color_2_style . ' "></span>';
                        }

                        echo $term->name;

                        echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
                    }
                }
                echo "</ul>";
            }
            elseif ( 'categories' == $display_type ) {
                $categories_filter_operator = 'and' == $query_type ? '+' : ',';
                $terms = yit_get_terms( 'all', $taxonomy );
                // List display
                echo "<ul class='yith-wcan-list yith-wcan'>";

                foreach ( $terms as $term ) {

                    // Get count based on current view - uses transients
                    $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                    //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                    $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                    set_transient( $transient_name, $_products_in_term );
                    //}

                    $option_is_set = ( isset( $_chosen_attributes[$taxonomy] ) && in_array( $term->term_id, $_chosen_attributes[$taxonomy]['terms'] ) );

                    $term_param = apply_filters( 'yith_wcan_term_param_uri', $term->slug, $display_type, $term );

                    // If this is an AND query, only show options with count > 0
                    if ( $query_type == 'and' ) {

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->filtered_product_ids ) );

                        // skip the term for the current archive
                        if ( $current_term == $term_param ) {
                            continue;
                        }

                        if ( $count > 0 && $current_term !== $term_param ) {
                            add_filter( 'yith_wcan_found_taxonomy', '__return_true' );
                        }

                        if ( ( ( $terms_type_list == 'hierarchical' || ( $terms_type_list == 'tags' && $instance['display'] == 'hierarchical' ) ) || !yit_term_has_child( $term, $taxonomy ) ) && $count == 0 && !$option_is_set ) {
                            continue;
                        }

                        // If this is an OR query, show all options so search can be expanded
                    }
                    else {

                        // skip the term for the current archive
                        if ( $current_term == $term_param ) {
                            continue;
                        }

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->unfiltered_product_ids ) );

                        if ( $count > 0 ) {
                            add_filter( 'yith_wcan_found_taxonomy', '__return_true' );
                        }

                    }

                    $arg = apply_filters( 'yith_wcan_categories_type_query_arg', $taxonomy, $display_type, $term );

                    $current_filter = ( isset( $_GET[$arg] ) ) ? explode( apply_filters( 'yith_wcan_list_filter_operator', $categories_filter_operator, $display_type ), apply_filters( "yith_wcan_list_filter_query_{$arg}", urlencode( $_GET[$arg] ) ) ) : array();

                    if ( !is_array( $current_filter ) ) {
                        $current_filter = array();
                    }

                    $current_filter = array_map( 'esc_attr', $current_filter );

                    if ( !in_array( $term_param, $current_filter ) ) {
                        $current_filter[] = $term_param;
                    }

                    $link = is_product_category() ? get_post_type_archive_link( 'product' ) : yit_get_woocommerce_layered_nav_link();

                    // All current filters
                    if ( $_chosen_attributes ) {
                        foreach ( $_chosen_attributes as $name => $data ) {
                            if ( $name !== $taxonomy ) {

                                // Exclude query arg for current term archive term
                                while ( in_array( $current_term, $data['terms'] ) ) {
                                    $key = array_search( $current_term, $data );
                                    unset( $data['terms'][$key] );
                                }

                                // Remove pa_ and sanitize
                                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                                if ( !empty( $data['terms'] ) ) {
                                    $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                                }

                                if ( $data['query_type'] == 'or' ) {
                                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                                }
                            }
                        }
                    }

                    // Min/Max
                    if ( isset( $_GET['min_price'] ) ) {
                        $link = add_query_arg( 'min_price', $_GET['min_price'], $link );
                    }

                    if ( isset( $_GET['max_price'] ) ) {
                        $link = add_query_arg( 'max_price', $_GET['max_price'], $link );
                    }

                    if ( isset( $_GET['product_tag'] ) && $display_type != 'tags' ) {
                        $link = add_query_arg( 'product_tag', urlencode( $_GET['product_tag'] ), $link );
                    }

                    $_chosen_categories = isset( $_GET['product_cat'] ) ? explode( $categories_filter_operator, urlencode( $_GET['product_cat'] ) ) : array();

                    // Current Filter = this widget
                    if ( apply_filters( 'yith_wcan_categories_type_current_widget_check', in_array( $term->slug, $_chosen_categories ), $current_filter, $display_type, $term_param ) ) {
                        $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                        // Remove this term is $current_filter has more than 1 term filtered
                        if ( sizeof( $current_filter ) > 1 ) {
                            $current_filter_without_this = array_diff( $current_filter, array( $term_param ) );
                            $value                       = urlencode( implode( apply_filters( 'yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type ), $current_filter_without_this ) );
                            $link                        = add_query_arg( $arg, $value, $link );
                        }
                    }

                    else {
                        $class = ( ( $terms_type_list == 'hierarchical' || ( $terms_type_list == 'tags' && $instance['display'] == 'hierarchical' ) ) && yit_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';
                        $link  = add_query_arg( $arg, implode( apply_filters( 'yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type ), $current_filter ), $link );
                    }

                    // Search Arg
                    if ( get_search_query() ) {
                        $link = add_query_arg( 's', get_search_query(), $link );
                    }

                    // Post Type Arg
                    if ( isset( $_GET['post_type'] ) ) {
                        $link = add_query_arg( 'post_type', $_GET['post_type'], $link );
                    }

                    $is_attribute = apply_filters( 'yith_wcan_is_attribute_check', true );

                    // Query type Arg
                    if ( $is_attribute && $query_type == 'or' && !( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                        $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                    }


                    echo '<li ' . $class . '>';

                    echo ( $count > 0 || $option_is_set ) ? '<a href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '">' : '<span>';

                    echo $term->name;

                    echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';

                    if ( $count != 0 && apply_filters( "{$args['widget_id']}-show_product_count", true, $instance ) ) {
                        echo ' <small class="count">' . $count . '</small><div class="clear"></div></li>';
                    }
                }

                echo "</ul>";
            }
        }

        public function get_terms_params( $param, $instance, $type ) {
            if ( 'tags' == $instance['type'] ) {
                if ( 'taxonomy_name' == $type ) {
                    $param = 'product_tag';
                }

                elseif ( 'terms_type' == $type ) {
                    $param = 'tags';
                }
            }

            elseif ( 'brands' == $instance['type'] && defined( 'YITH_WCBR_PREMIUM_INIT' ) && YITH_WCBR_PREMIUM_INIT ) {
                if ( 'taxonomy_name' == $type ) {
                    $param = YITH_WCBR::$brands_taxonomy;
                }

                elseif ( 'terms_type' == $type ) {
                    $param = 'brands';
                }
            }

            elseif( 'categories' == $instance['type'] && 'taxonomy_name' == $type ){
               $param = 'product_cat';
            }
            return $param;
        }

        public function add_display_type_case( $args ) {
            $args[] = 'tags';
            $args[] = 'brands';
            return $args;
        }

        public function type_query_args( $arg, $type, $term ) {
            if ( 'tags' == $type ) {
                $arg = 'product_tag';
            }

            elseif ( 'brands' == $type && defined( 'YITH_WCBR_PREMIUM_INIT' ) && YITH_WCBR_PREMIUM_INIT ) {
                $arg = YITH_WCBR::$brands_taxonomy;
            }

            return $arg;
        }

        public function term_param_uri( $term_param, $type, $term ) {
            if ( 'tags' == $type || 'brands' == $type ) {
                $term_param = $term->slug;
            }

            return $term_param;
        }

        public function filter_current_widget( $check_for_current_widget, $current_term, $type, $term_param ) {
            $brands_taxonomy = defined( 'YITH_WCBR_PREMIUM_INIT' ) && YITH_WCBR_PREMIUM_INIT ? YITH_WCBR::$brands_taxonomy : '';

            if ( 'tags' == $type && isset( $_GET['product_tag'] ) ) {
                $current_filters = explode( '+', urlencode( $_GET['product_tag'] ) );
                if ( in_array( $term_param, $current_filters ) ) {
                    $check_for_current_widget = true;
                }
            }

            elseif ( 'brands' == $type && isset( $_GET[$brands_taxonomy] ) ) {
                $current_filters = explode( ',', $_GET[$brands_taxonomy] );
                if ( in_array( $term_param, $current_filters ) ) {
                    $check_for_current_widget = true;
                }
            }
            return $check_for_current_widget;
        }

        public function tag_filter_operator( $operator, $display_type ) {
            return 'tags' == $display_type ? '+' : $operator;
        }

        public function yith_wcan_include_exclude_terms( $ids, $instance ) {
            $option_ids = empty( $instance['tags_list'] ) ? array() : $instance['tags_list'];

            if ( empty( $option_ids ) ) {
                if ( 'yith_wcan_include_terms' == current_filter() ) {
                    $option_ids = array( - 1 );
                }

                elseif ( 'yith_wcan_exclude_terms' == current_filter() ) {
                    $option_ids = array();
                }
            }

            else {
                $option_ids = is_array( $option_ids ) ? array_keys( $option_ids ) : array();
            }

            return array_merge( $ids, $option_ids );
        }

        public function yith_wcan_list_filter_query_product_tag( $_get ) {
            return urlencode( $_get );
        }
    }
}