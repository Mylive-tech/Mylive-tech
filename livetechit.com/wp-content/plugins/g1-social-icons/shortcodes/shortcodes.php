<?php

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php
if ( ! class_exists( 'G1_Social_Icons_Shortcode' ) ):

class G1_Social_Icons_Shortcode {
    private $id;
    private $elements;
    private $items;
    private static $counter;

    public function __construct( $id ) {
        $this->id = $id;
        $this->elements = array(
            'icon'      => 'icon',
            'label'     => 'label',
            'caption'   => 'caption',
        );

        $this->setup_hooks();
    }

    public function get_id () {
        return $this->id;
    }

    public function get_item ( $name ) {
        if ( empty( $this->items ) ) {
            $this->items = get_option( $this->get_id() );
        }

        return !empty($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * Shortcode callback function.
     *
     * @return string
     */
    public function do_shortcode ( $atts ) {
        extract( shortcode_atts( array(
            'include' => '',
            'exclude' => '',
            'template' => 'list-vertical',
            'size' => '24',
            'hide' => '',
        ), $atts ) );

        $size = absint( $size );

        $data = get_option( G1_Social_Icons()->get_option_name(), array() );

        $feeds = array();
        $hide = $this->string_to_bools($hide);

        // Process the 'include' variable
        $include = explode(',', $include);
        foreach ( $include as $feed ) {
            $feed = preg_replace('/[^a-zA-Z0-9_-]*/', '', $feed);

            if( !empty( $feed ) ) {
                $val = $this->get_item($feed);

                if ( $val !== null && strlen( $val['link'] ) ) {
                    $feeds[$feed] = $val;
                }
            }
        }

        // Populate 'feeds' array only if there are no feeds from the 'include' variable
        if ( !count( $feeds ) ) {
            foreach ( $data as $item_id => $item_args ) {
                if ( count( $item_args ) && strlen( $item_args[ 'link' ] ) ) {
                    $feeds[ str_replace( 'feed_', '', $item_id)] = $item_args;
                }
            }

            // Exclude feeds bapadding-left:21px; sed on the 'exclude' variable
            if ( count( $feeds ) ) {
                $exclude = explode(',', $exclude);
                foreach ( $exclude as $feed ) {
                    $feed = preg_replace('/[^a-zA-Z0-9_-]*/', '', $feed);

                    if ( isset($feeds[$feed] ) )
                        unset($feeds[$feed]);
                }
            }
        }

        // Compose output
        $out = '';
        $_out  = '';

        $final_id = !empty( $id ) ? $id : 'g1-social-icons-' . $this->get_counter();

        $final_class = array(
            'g1-social-icons',
            'g1-social-icons--' . $template,
            'g1-social-icons--' . $size,
        );


        if ( !isset( $hide['icon'] ) ) {
            $final_class[] = 'g1-social-icons--icon';
        } else {
            $final_class[] = 'g1-social-icons--noicon';
        }


        if ( count( $feeds ) ) {
            // Compose a template for a single feed
            $temp = '<li>' .
                        '<a class="%link_class%" href="%href%">%img%%label%</a>' .
                        '%caption%' .
                    '</li>';

            foreach ( $feeds as $feed => $args ) {
                // WPML fallback
                if ( function_exists( 'icl_t' ) ) {
                    $args['label'] = icl_t( 'G1 Social_Icons', 'g1_social_icon_' . esc_attr( $feed ) . '_label' );
                    $args['caption'] = icl_t( 'G1 Social_Icons', 'g1_social_icon_' . esc_attr( $feed ) . '_caption_' );
                }

                $path = trailingslashit( G1_Social_Icons()->get_plugin_dir_url() );
                $path .= 'images/' . $feed . '/' . $feed . '-' . $size*2 . '.png';


                $_class = array(
                    'g1-social-icon',
                    'g1-social-icon--' . $feed,
                );

                $_img = sprintf( '<span class="%s"><img src="%s" alt="%s" width="%d" height="%d" /></span>',
                    sanitize_html_classes( $_class ),
                    esc_url( $path ),
                    esc_attr( $feed ),
                    $size,
                    $size
                );


                $_label = '';
                if ( !isset( $hide['label'] ) ) {
                    $_label = '<strong>' . esc_html( $args['label'] ) . '</strong>';
                }

                $_link_class = '';
                $_link_class .= $args[ 'linking' ] == 'new-window' ? 'g1-new-window "' : '';

                $_href = esc_url( $args['link'] );

                $_caption = '';
                if ( !isset( $hide['caption'] ) ) {
                    $_caption = '<i class="g1-meta">' . esc_html( $args[ 'caption' ] ) . '</i>';
                }

                $_out .= str_replace(
                    array(
                        '%link_class%',
                        '%href%',
                        '%img%',
                        '%label%',
                        '%caption%',
                    ),
                    array(
                        sanitize_html_classes( $_link_class),
                        $_href,
                        $_img,
                        $_label,
                        $_caption
                    ),
                    $temp
                );

            }

            // Compose a template for the whole shortcode
            $temp = '<div id="%id%" class="%class%">' .
                        '<ul>' .
                            '%_out%' .
                        '</ul>' .
                    '</div>';

            $out = str_replace(
                array(
                    '%id%',
                    '%class%',
                    '%_out%',
                ),
                array(
                    esc_attr( $final_id ),
                    sanitize_html_classes( $final_class ),
                    $_out,
                ),
                $temp
            );

        }
        return $out;
    }

    protected function setup_hooks () {
        add_shortcode( $this->id, array($this, 'do_shortcode') );
    }

    protected  function get_counter () {
        if ( empty(self::$counter) ) {
            self::$counter = 0;
        }

        return ++self::$counter;
    }

    protected function get_collection_templates() {
        $templates = array(
            'list-vertical'		=> __( 'list-vertical', 'g1_social_icons' ),
            'list-horizontal'	=> __( 'list-horizontal', 'g1_social_icons' ),
        );

        return apply_filters( 'g1_social_icons_collection_templates', $templates );
    }

    protected function get_collection_sizes() {
        $templates = array(
            '24'    => '24',
            '32'    => '32',
            '48'    => '48',
        );

        return apply_filters( 'g1_social_icons_collection_sizes', $templates );
    }

    function string_to_bools( $string ) {
        $string = preg_replace( '/[^0-9a-zA-Z,_-]*/', '', $string );

        $results = array();
        $bools = explode( ',', $string );

        foreach ( $bools as $key => $value )
            $results[$value] = true;

        return $results;
    }
}
endif;

function G1_Social_Icons_Shortcode() {
    static $instance = null;

    if ( null === $instance )
        $instance = new G1_Social_Icons_Shortcode( 'g1_social_icons' );

    return $instance;
}

G1_Social_Icons_Shortcode();