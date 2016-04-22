<?php

class G1_Theme_Admin {

    public function __construct() {
        add_action( 'after_setup_theme', array( $this, 'setup_hooks' ) );
    }

    public function setup_hooks() {

        // Enable editor style
        $this->setup_editor_style();

        // Improve usability
        $this->setup_usability();

	    // Default image dimensions
        global $pagenow;
        if ( isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
            add_action( 'init', array($this, 'woocommerce_image_dimensions'), 1 );
        }

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    public function woocommerce_image_dimensions() {
        $catalog = array(
            'width'     => '239',       // px
            'height'    => '319',       // px
            'crop'              => 1            // true
        );

        $single = array(
            'width'     => '482',       // px
            'height'    => '643',       // px
            'crop'              => 1            // true
        );

        $thumbnail = array(
            'width'     => '55',        // px
            'height'    => '73',        // px
            'crop'              => 0            // false
        );

        // Update image sizes
        update_option( 'shop_catalog_image_size', $catalog );
        update_option( 'shop_single_image_size', $single );
        update_option( 'shop_thumbnail_image_size', $thumbnail );
    }

    public function setup_editor_style() {
        add_theme_support( 'editor_style' );
        add_editor_style();
    }

    public function setup_usability() {
        add_filter( 'manage_posts_columns',         array( $this, 'add_id_column') );
        add_action( 'manage_posts_custom_column',   array( $this, 'render_id_column') );
    }

    public function admin_notices () {
        global $pagenow;

        if ( $pagenow == 'options-reading.php' ) {
            $notice_template = 'To set up your <strong>%s</strong> go to the <a href="%s">%s</a>.';

            $notice1 = sprintf( __( $notice_template, 'g1_theme' ), 'Front page', '/wp-admin/themes.php?page=g1_theme_options', 'Appearance > Theme Options > Pages');
            $notice2 = sprintf( __( $notice_template, 'g1_theme' ), 'Posts page', '/wp-admin/themes.php?page=g1_theme_options', 'Appearance > Theme Options > Posts > Posts Archive Page');

            if ( strlen($notice1) > 0 && strlen($notice2) > 0 ) {
                echo '<div class="updated">';
                echo '<p>'. $notice1 .'</p>';
                echo '<p>'. $notice2 .'</p>';
                echo '</div>';
            }
        }
    }

    public function add_id_column( $columns ) {
        $new_columns = array();

        foreach ( $columns as $k => $v ) {
            $new_columns[ $k ] = $v;
            if ( 'cb' == $k ) {
                $new_columns[ 'id' ] = 'ID';
            }
        }

        return $new_columns;
    }


    public function render_id_column( $name ) {
        global $post;

        if ( 'id' === $name ) {
            echo $post->ID;
        }
    }
}
/**
 * Quasi-singleton for our theme
 *
 * @return G1_Theme_Admin
 */
function G1_Theme_Admin() {
    static $instance;

    if ( !isset( $instance ) ) {
        $instance = new G1_Theme_Admin();
    }

    return $instance;
}
// Fire in the hole :)
G1_Theme_Admin();



/**
 * Adding our custom fields to the $form_fields array
 *
 * @param array $form_fields
 * @param object $attachment
 * @return array
 */
function g1_attachment_image_fields_to_edit( $form_fields, $attachment ) {

    if ( substr( $attachment->post_mime_type, 0, 5 ) == 'image' ){
        $form_fields[ 'type' ] = array(
            'label' => __( 'Type', 'g1_theme' ),
            'input' => 'html',
            'helps' => __( 'Specify how the media should be treated', 'g1_theme' ),
        );

        // attachment type
        $types = array(
            ''          => 'regular attachment',
            'exclude'   => __('exclude from mediabox', 'g1_theme'),
        );

        $value = get_post_meta( $attachment->ID, '_g1_type', true);

        $html = '<select style="width:100%; max-width:100%;" name="attachments[' . $attachment->ID . '][type]" id="attachments[' . $attachment->ID .'][type]">';
        foreach( $types as $option => $label ) {
            if( $value === $option )
                $html .= '<option selected="selected" value="' . esc_attr( $option ) . '">' . esc_html( $label ) . '</option>';
            else
                $html .= '<option value="' . esc_attr( $option ) . '">' . esc_html( $label ) . '</option>';
        }
        $html .= '</select>';

        $form_fields['type'][ 'html' ] = $html;
    }

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'g1_attachment_image_fields_to_edit', null, 2 );




/**
 * @param array $post
 * @param array $attachment
 * @return array
 */
function g1_attachment_image_fields_to_save( $post, $attachment ) {
    if ( isset( $attachment[ 'alt_link' ] ) ){
        update_post_meta( $post[ 'ID' ], '_g1_alt_link', $attachment[ 'alt_link' ] );
    }

    if ( isset( $attachment[ 'alt_linking' ] ) ){
        update_post_meta( $post[ 'ID' ], '_g1_alt_linking', $attachment[ 'alt_linking' ] );
    }

    if ( isset( $attachment[ 'type' ] ) ){
        update_post_meta( $post[ 'ID' ], '_g1_type', $attachment[ 'type' ] );
    }

    return $post;
}
add_filter( 'attachment_fields_to_save', 'g1_attachment_image_fields_to_save', null, 2 );




function g1_mediabox_get_help() {
    $out = '';

    $out .= '<p>' . __( 'A media box is a part of a template, that displays entry attachments.', 'g1_theme' ) . '</p>';
    $out .= '<p>' . __( 'The <strong>list</strong> displays image &amp; audio attachments.', 'g1_theme' ) . '</p>';
    $out .= '<p>' . __( 'The <strong>slider</strong> displays only image attachments.', 'g1_theme' ) . '</p>';
    $out .= '<p>' . __( 'The <strong>featured media</strong> displays featured image.', 'g1_theme' ) . '</p>';
    $out .= '<p>' . __( 'The <strong>none</strong> displays nothing.', 'g1_theme' ) . '</p>';

    return apply_filters( 'g1_mediabox_help', $out );
}
