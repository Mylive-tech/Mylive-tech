<?php
/**
 * For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Theme03
 * @subpackage G1_Theme
 * @since G1_Theme 1.0.0
 */

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php

/**
 * Our Theme
 */
class G1_Theme {
    protected $id;
    protected $base_id;

    protected $version;



    public function __construct( $id ) {
        $this->base_id = $id;
        $this->set_id( $id );

        $this->set_version( '3.0.1' );

        add_action( 'after_setup_theme', array( $this, 'setup_hooks' ) );
    }

    public function get_id() { return $this->id; }
    public function set_id( $val ) { $this->id = $val; }

    public function get_version() { return $this->version; }
    public function set_version( $val ) { $this->version = $val; }


    public function setup_hooks() {
        add_action( 'init', array($this, 'setup_session'), 1 );

        // WPML integration
        add_action( 'init', array( $this, 'setup_wpml' ) );

        add_filter( 'g1_site_id_markup', array( $this, 'change_site_id_markup_besides_front_page' ), 20 );

        // Enable post thumbnails
        add_action( 'init', array( $this, 'setup_post_thumbnails' ) );

        // Set up shortcodes and related stuff
        $this->setup_shortcodes();

        // Set up widgets and related stuff
        $this->setup_widgets();

        // Register sidebars
        add_action( 'init', array( $this, 'setup_sidebars' ), 1 );

        // Register custom navigation locations
        add_action( 'init', array( $this, 'setup_nav_menus' ) );

        // Enable WP Auto Feed Links
        add_theme_support( 'automatic-feed-links' );

        $this->setup_post_formats();

        $this->load_embed_video_simple_interface();


        $this->setup_archive_templates();
        $this->setup_collections();
        $this->setup_single_templates();

        if ( is_plugin_active( 'revslider/revslider.php' ) ) {
            add_filter( 'g1_element_slider_choices' ,       array($this, 'add_rev_slider_choices') );
        }

        add_filter( 'default_content', array( $this, 'setup_default_discussion_options' ), 10, 2 );
        add_filter( 'embed_defaults', array( $this, 'embed_defaults' ), 999 );
    }


    /**
     * Set up the WordPress Multilingual Plugin if available
     *
     * You can read more about this plugin here:
     * http://wpml.org/
     */
    public function setup_wpml() {
        if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {

            // Remove @lang from term title
            global $sitepress;
            if ( $sitepress ) {
                add_filter('single_term_title', array( $sitepress, 'the_category_name_filter' ) );
            }
        }
    }


    public function embed_defaults ($args) {
        $args['height'] = round( $args['width'] * 9/16 );

        return $args;
    }

    public function setup_default_discussion_options ( $post_content, $post ) {
        if( !empty($post->post_type) ) {
            switch( $post->post_type ) {
                case 'page':
                    $post->comment_status = g1_get_theme_option('post_type_page', 'comment_status', 'open');
                    $post->ping_status = g1_get_theme_option('post_type_page', 'ping_status', 'open');
                    break;
            }
        }

        return $post_content;
    }


    public function add_rev_slider_choices ($choices) {
        if ( !class_exists('GlobalsRevSlider') ) {
            return array();
        }

        global $wpdb;

        $tableSliders = $wpdb->prefix . GlobalsRevSlider::TABLE_SLIDERS_NAME;

        $res = $wpdb->get_results( "SELECT id, title, alias FROM $tableSliders", ARRAY_A );

        foreach ($res as $slider) {
            $choices['revslider_'.$slider['alias']] = 'RevSlider: ' . $slider['title'] . ' (ID:' . $slider['id'] . ')';
        }

        return $choices;
    }



    public function setup_session () {
        if (defined('G1_CUSTOMIZE_MODE')) {
    	    $dontUseSession = !empty($_COOKIE['g1_dont_use_session']) ? (bool)$_COOKIE['g1_dont_use_session'] : false;

            if ($dontUseSession) {
                return;
            }

            if ( !session_id() ) {
                session_start();
            }

            // get
            $demo_nr = isset($_GET['demo']) ? (integer)$_GET['demo'] : null;

            if ($demo_nr !== null && $demo_nr >= 1 && $demo_nr <= 12) {
                $theme_id = $this->base_id;

                if ( $demo_nr >= 2 ) {
                    $theme_id .= '_v' . str_pad($demo_nr, 2, '0', STR_PAD_LEFT);
                }

                $_SESSION['theme_id'] = $theme_id;
            }

            // session
            $session_theme_id = isset($_SESSION['theme_id']) ? $_SESSION['theme_id'] : $this->base_id;

            if ($session_theme_id !== null) {
                $this->set_id( $session_theme_id );
            }
        }
    }

    public function gallery_shortcode ( $attr ) {
        $attr = wp_parse_args( $attr, array(
            'itemtag'    => 'li',
            'icontag'    => 'div',
            'captiontag' => 'div',
            'lightbox' => true
        ));

        $open_in_lightbox = $attr['lightbox'] !== 'false';

        if ( $open_in_lightbox ) {
            $attr['link'] = 'file';
        }

        $html = gallery_shortcode( $attr );

        $html = preg_replace( '/<br[^>]*>/i', '', $html );

        $open_tag_found = preg_match('/^\s*(<div[^>]*>)/i', $html, $open_matches);
        $close_tag_found = preg_match('/(<\/div>)\s*$/i', $html);

        // replace first opening and last closing tag
        if ( $open_tag_found && $close_tag_found ) {

            $open_div = $open_matches[1];
            $new_open_div = str_replace('div', 'figure', $open_div) . '<ol>';
            $html = str_replace($open_div, $new_open_div, $html);

            $html = preg_replace('/<\/div>\s*$/', '</ol></figure>', $html);

            $lightbox_group_id = rand();

            if ( $open_in_lightbox ) {
                $html = str_replace('<a', '<a data-g1-lightbox="gallery-'. $lightbox_group_id .'"', $html);
            }
        }

        return $html;
    }

    public function setup_post_formats () {
        add_theme_support(
            'post-formats',
            array(
                'aside',
                'gallery',
                'link',
                'image',
                'quote',
                'status',
                'video',
                'audio',
                'chat'
            )
        );

        require_once( G1_LIB_DIR . '/g1-post-formats/g1-post-formats.php' );
    }

    public function load_embed_video_simple_interface () {
        add_filter( 'embed_oembed_html', 'g1_youtube_simple_interface', 10, 3 );
        add_filter( 'embed_oembed_html', 'g1_vimeo_simple_interface', 10, 3 );
    }

    public function load_embed_video_rich_interface () {
        remove_filter( 'embed_oembed_html', 'g1_youtube_simple_interface' );
        remove_filter( 'embed_oembed_html', 'g1_vimeo_simple_interface' );
    }

    /**
     * @param $size
     *
     * @return boolean
     */
    public function get_crop_for_size ( $size ) {
        $image_size_properties = g1_get_theme_option('image_size', $size, array());

        if ( !empty($image_size_properties['crop']) ) {
            return (bool)($image_size_properties['crop'] === 'standard');
        }

        // size doesn't exist
        return false;
    }

    public function default_post_thumbnails_sizes_crops () {
        return array(
            'g1_max'        => false,
            'g1_two_third'  => false,
            'g1_one_half'   => true,
            'g1_one_third'  => true,
            'g1_one_fourth' => true,
            'g1_one_twelfth'=> true,
        );
    }

    public function setup_post_thumbnails() {
        // Enable post-thumbnails feature
        add_theme_support( 'post-thumbnails' );

        // Add image sizes based on our grid
        // Please note that modules can add their own sizes

        $unit_width = 55;
        $gutter_width = 28;

        add_image_size( 'g1_max', 968, 9999, $this->get_crop_for_size( 'g1_max' ) );

        add_image_size( 'g1_two_third',
            8*$unit_width + 7*$gutter_width,
            9999,
            $this->get_crop_for_size( 'g1_two_third' )
        );

        add_image_size( 'g1_one_half',
            482,
            round( 482 * (9/16) ),
            $this->get_crop_for_size( 'g1_one_half' )
        );

        add_image_size( 'g1_one_third',
            320,
            round( 320 * (9/16) ),
            $this->get_crop_for_size( 'g1_one_third' )
        );

        add_image_size( 'g1_one_third_flexible',
            320,
            9999,
            $this->get_crop_for_size( 'g1_one_third_flexible' )
        );

        add_image_size( 'g1_one_fourth',
            239,
            round( 239 * (9/16) ),
            $this->get_crop_for_size( 'g1_one_fourth' )
        );

        add_image_size( 'g1_one_fourth_flexible',
            239,
            9999,
            $this->get_crop_for_size( 'g1_one_fourth_flexible' )
        );


        add_image_size( 'g1_one_twelfth',
            $unit_width,
            $unit_width,
            $this->get_crop_for_size( 'g1_one_twelfth' )
        );
    }








    /**
     * Registers sidebars
     *
     * Registers permanent sidebars and custom ones from the sidedar generator
     */
    public function setup_sidebars() {
        $sidebars = g1_get_theme_option( 'sidebars', '', array() );

        // Normalize | sanitize
        foreach ( $sidebars as $index => $sidebar ) {
            $sidebar = preg_replace( '/[^0-9a-zA-Z_-]/', '-', $sidebar );
            $sidebar = preg_replace( '/\-{2,}/', '-', $sidebar );
            $sidebar = strtolower($sidebar);

            $sidebars[ $index ] = $sidebar;
        }

        // Prepend default sidebars
        array_unshift(
            $sidebars,
            'primary',
            'preheader-1',
            'preheader-2',
            'preheader-3',
            'preheader-4',
            'preheader-5',
            'prefooter-1',
            'prefooter-2',
            'prefooter-3',
            'prefooter-4',
            'prefooter-5'
        );

        $sidebars = apply_filters( 'g1_setup_sidebars', $sidebars );

        if ( count( $sidebars ) ) {
            foreach ( $sidebars as $sidebar) {
                if ( strlen( $sidebar ) ) {
                    register_sidebar( array(
                        'name'				=> $sidebar,
                        'id'				=> $sidebar,
                        'before_widget' 	=> '<section id="%1$s" class="widget %2$s g1-widget--cssclass">',
                        'after_widget' 		=> '</section>',
                        'before_title'  	=> '<header><h3 class="widgettitle">',
                        'after_title'   	=> '</h3></header>'

                    ));
                }
            }
        }
    }

    /**
     * Registers Custom Navigation Locations
     */
    public function setup_nav_menus() {
        register_nav_menus(  array(
            'primary_nav'   => __( 'Primary Navigation', 'g1_theme' ),
            'secondary_nav' => __( 'Secondary Navigation', 'g1_theme' ),
            'footer_nav'	=> __( 'Footer Navigation', 'g1_theme' ),
        ));
    }




    public function setup_shortcodes() {
        require_once( G1_LIB_DIR.'/g1-shortcodes/basic.php' );
        require_once( G1_LIB_DIR.'/g1-shortcodes/grid.php' );
        require_once( G1_LIB_DIR.'/g1-shortcodes/panels.php' );
        require_once( G1_LIB_DIR.'/g1-shortcodes/misc.php' );


        // gallery shortcode
        remove_shortcode( 'gallery' );
        add_shortcode( 'gallery', array($this, 'gallery_shortcode') );
        add_filter( 'use_default_gallery_style', '__return_false' );


        if ( is_admin() ) {
            require_once( G1_LIB_DIR.'/g1-shortcodes/admin/home_page_snippets.php' );
            require_once( G1_LIB_DIR.'/g1-shortcodes/admin/page_snippets.php' );
        }

        //add_shortcode('wp_caption', 'g1_img_caption_shortcode');
        //add_shortcode('caption', 'g1_img_caption_shortcode');
    }

    public function setup_widgets() {
        // Enable shortcodes in the Text Widget
        add_filter( 'widget_text', 'do_shortcode', 11 );

        // The title of a widget can be empty now
        add_filter( 'widget_title',  array( $this, 'allow_empty_widget_title' ), 9999 );
    }

    public function setup_archive_templates() {
        add_action( 'g1_archive_templates_register', array( $this, 'register_archive_templates' ), 8 );
    }

    public function setup_single_templates() {
        add_action( 'g1_single_templates_register', array( $this, 'register_single_templates' ), 8 );
    }

    public function setup_collections() {
        add_action( 'g1_collections_register', array( $this, 'register_collections' ), 8 );
    }


    public function allow_empty_widget_title( $title ) {
        $title = trim( $title );
        $title = ( $title === "&nbsp;" ) ? '' : $title;

        return $title;
    }




    public function register_archive_templates( $manager ) {
        $dir = '/';
        $uri = trailingslashit( get_template_directory_uri() ) . 'images/templates/';

        // Add archive templates
        $templates = array(
            '1col_sidebar_right' => array(
                'label' => __( '1 Column, Right Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_1col_sidebar_right.php',
                'icon'  => $uri . 'g1_template_archive_1col_sidebar_right.png',
            ),
            '1col_sidebar_left' => array(
                'label' => __( '1 Column, Left Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_1col_sidebar_left.php',
                'icon'  => $uri . 'g1_template_archive_1col_sidebar_left.png',
            ),
            '1col' => array(
                'label' => __( '1 Column', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_1col.php',
                'icon'  => $uri . 'g1_template_archive_1col.png',
            ),
            '2col' => array(
                'label' => __( '2 Columns', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col.php',
                'icon'  => $uri . 'g1_template_archive_2col.png',
            ),
            '2col_filterable' => array(
                'label' => __( '2 Columns Filterable', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_filterable.php',
                'icon'  => $uri . 'g1_template_archive_2col_filterable.png',
            ),
            '2col_gallery' => array(
                'label' => __( '2 Columns Gallery', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_gallery.php',
                'icon'  => $uri . 'g1_template_archive_2col_gallery.png',
            ),
            '2col_gallery_filterable' => array(
                'label' => __( '2 Columns Gallery Filterable', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_gallery_filterable.php',
                'icon'  => $uri . 'g1_template_archive_2col_gallery_filterable.png',
            ),
            '2col_sidebar_left' => array(
                'label' => __( '2 Columns, Left Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_sidebar_left.php',
                'icon'  => $uri . 'g1_template_archive_2col_sidebar_left.png',
            ),
            '2col_filterable_sidebar_left' => array(
                'label' => __( '2 Columns Filterable, Left Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_filterable_sidebar_left.php',
                'icon'  => $uri . 'g1_template_archive_2col_filterable_sidebar_left.png',
            ),
            '2col_gallery_sidebar_left' => array(
                'label' => __( '2 Columns Gallery, Left Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_gallery_sidebar_left.php',
                'icon'  => $uri . 'g1_template_archive_2col_gallery_sidebar_left.png',
            ),
            '2col_gallery_filterable_sidebar_left' => array(
                'label' => __( '2 Columns Gallery Filterable, Left Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_gallery_filterable_sidebar_left.php',
                'icon'  => $uri . 'g1_template_archive_2col_gallery_filterable_sidebar_left.png',
            ),
            '2col_sidebar_right' => array(
                'label' => __( '2 Column, Right Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_sidebar_right.php',
                'icon'  => $uri . 'g1_template_archive_2col_sidebar_right.png',
            ),
            '2col_filterable_sidebar_right' => array(
                'label' => __( '2 Columns Filterable, Right Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_filterable_sidebar_right.php',
                'icon'  => $uri . 'g1_template_archive_2col_filterable_sidebar_right.png',
            ),
            '2col_gallery_sidebar_right' => array(
                'label' => __( '2 Column Gallery, Right Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_gallery_sidebar_right.php',
                'icon'  => $uri . 'g1_template_archive_2col_gallery_sidebar_right.png',
            ),
            '2col_gallery_filterable_sidebar_right' => array(
                'label' => __( '2 Columns Gallery Filterable, Right Sidebar', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_2col_gallery_filterable_sidebar_right.php',
                'icon'  => $uri . 'g1_template_archive_2col_gallery_filterable_sidebar_right.png',
            ),
            '3col' => array(
                'label' => __( '3 Columns', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_3col.php',
                'icon'  => $uri . 'g1_template_archive_3col.png',
            ),
            '3col_filterable' => array(
                'label' => __( '3 Columns Filterable', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_3col_filterable.php',
                'icon'  => $uri . 'g1_template_archive_3col_filterable.png',
            ),
            '3col_masonry' => array(
                'label' => __( '3 Columns Masonry', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_3col_masonry.php',
                'icon'  => $uri . 'g1_template_archive_3col_masonry.png',
            ),
            '3col_gallery' => array(
                'label' => __( '3 Columns Gallery', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_3col_gallery.php',
                'icon'  => $uri . 'g1_template_archive_3col_gallery.png',
            ),
            '3col_gallery_filterable' => array(
                'label' => __( '3 Columns Gallery Filterable', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_3col_gallery_filterable.php',
                'icon'  => $uri . 'g1_template_archive_3col_gallery_filterable.png',
            ),
            '4col' => array(
                'label' => __( '4 Columns', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_4col.php',
                'icon'  => $uri . 'g1_template_archive_4col.png',
            ),
            '4col_masonry' => array(
                'label' => __( '4 Columns Masonry', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_4col_masonry.php',
                'icon'  => $uri . 'g1_template_archive_4col_masonry.png',
            ),
            '4col_gallery' => array(
                'label' => __( '4 Columns Gallery', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_4col_gallery.php',
                'icon'  => $uri . 'g1_template_archive_4col_gallery.png',
            ),
            '4col_filterable' => array(
                'label' => __( '4 Columns Filterable', 'g1_theme' ),
                'file'  => $dir . 'g1_template_archive_4col_filterable.php',
                'icon'  => $uri . 'g1_template_archive_4col_filterable.png',
            ),
        );

        foreach ( $templates as $id => $args ) {
            $manager->add_template( $id, $args );
        }
    }

    public function register_collections( $manager ) {
        $template_parts_dir = 'template-parts/';

        $templates = array(
            'one_fourth' => array(
                'file'              => $template_parts_dir . 'g1_collection_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-fourth', 'g1-collection--simple' ),
                'image_size'        => 'g1_one_fourth',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_fourth_gallery' => array(
                'file'              => $template_parts_dir . 'g1_collection_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-fourth', 'g1-collection--gallery' ),
                'image_size'        => 'g1_one_fourth',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_fourth_filterable' => array(
                'file'              => $template_parts_dir . 'g1_collection_filterable_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-fourth', 'g1-collection--filterable' ),
                'image_size'        => 'g1_one_fourth',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_fourth_masonry' => array(
                'file'              => $template_parts_dir . 'g1_collection_masonry',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-fourth', 'g1-collection--masonry' ),
                'image_size'        => 'g1_one_fourth_flexible',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => false,
            ),
            'one_third' => array(
                'file'              => $template_parts_dir . 'g1_collection_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-third', 'g1-collection--simple' ),
                'image_size'        => 'g1_one_third',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_third_filterable' => array(
                'file'              => $template_parts_dir . 'g1_collection_filterable_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-third', 'g1-collection--filterable' ),
                'image_size'        => 'g1_one_third',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_third_gallery' => array(
                'file'              => $template_parts_dir . 'g1_collection_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-third', 'g1-collection--gallery' ),
                'image_size'        => 'g1_one_third',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_third_gallery_filterable' => array(
                'file'              => $template_parts_dir . 'g1_collection_filterable_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-third', 'g1-collection--gallery' ),
                'image_size'        => 'g1_one_third',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_third_masonry' => array(
                'file'              => $template_parts_dir . 'g1_collection_masonry',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-third', 'g1-collection--masonry' ),
                'image_size'        => 'g1_one_third_flexible',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => false,
            ),
            'one_half' => array(
                'file'              => $template_parts_dir . 'g1_collection_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-half', 'g1-collection--simple' ),
                'image_size'        => 'g1_one_half',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_half_filterable' => array(
                'file'              => $template_parts_dir . 'g1_collection_filterable_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-half', 'g1-collection--filterable' ),
                'image_size'        => 'g1_one_half',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_half_gallery' => array(
                'file'              => $template_parts_dir . 'g1_collection_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-half', 'g1-collection--gallery' ),
                'image_size'        => 'g1_one_half',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'one_half_gallery_filterable' => array(
                'file'              => $template_parts_dir . 'g1_collection_filterable_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--one-half', 'g1-collection--gallery' ),
                'image_size'        => 'g1_one_half',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => true,
            ),
            'two_third' => array(
                'file'              => $template_parts_dir . 'g1_collection_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--two-third', 'g1-collection--simple' ),
                'image_size'        => 'g1_two_third',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => false,
            ),
            'max' => array(
                'file'              => $template_parts_dir . 'g1_collection_grid',
                'classes'           => array( 'g1-collection--grid', 'g1-collection--max', 'g1-collection--simple' ),
                'image_size'        => 'g1_max',
                'lightbox_group'    => 'g1-works',
                'force_placeholder' => false,
            ),
        );

        foreach ( $templates as $id => $args ) {
            $manager->add_collection( $id, $args );
        }
    }



    public function register_single_templates( $manager ) {
        $dir = '/';
        $uri = trailingslashit( get_template_directory_uri() ) . 'images/templates/';

        // Add archive templates
        $templates = array(
            'sidebar_right' => array(
                'label' => __( 'Sidebar Right', 'g1_theme' ),
                'file'  => $dir . 'g1_template_single_sidebar_right.php',
                'icon'  => $uri . 'g1_template_single_sidebar_right.png',
            ),
            'sidebar_left' => array(
                'label' => __( 'Sidebar Left', 'g1_theme' ),
                'file'  => $dir . 'g1_template_single_sidebar_left.php',
                'icon'  => $uri . 'g1_template_single_sidebar_left.png',
            ),
            'overview_right' => array(
                'label' => __( 'Overview Right', 'g1_theme' ),
                'file'  => $dir . 'g1_template_single_overview_right.php',
                'icon'  => $uri . 'g1_template_single_overview_right.png',
            ),
            'overview_left' => array(
                'label' => __( 'Overview Left', 'g1_theme' ),
                'file'  => $dir . 'g1_template_single_overview_left.php',
                'icon'  => $uri . 'g1_template_single_overview_left.png',
            ),
            'full' => array(
                'label' => __( 'Full', 'g1_theme' ),
                'file'  => $dir . 'g1_template_single_full.php',
                'icon'  => $uri . 'g1_template_single_full.png',
            ),
        );

        foreach ( $templates as $id => $args ) {
            $manager->add_template( $id, $args );
        }
    }


    public function get_theme_areas() {
        $result = array(
            'preheader'     => __( 'Preheader', 'g1_theme' ),
            'header'        => __( 'Header', 'g1_theme' ),
            'precontent'    => __( 'Precontent', 'g1_theme' ),
            'content'       => __( 'Content', 'g1_theme' ),
            'prefooter'     => __( 'Prefooter', 'g1_theme' ),
            'footer'        => __( 'Footer', 'g1_theme' ),
        );

        return $result;
    }

    public function change_site_id_markup_besides_front_page ( $markup ) {
        if ( !is_front_page() ) {
            $markup = '<p class="site-title"><a href="%1$s" title="'. __( 'Go back to the homepage', 'g1_theme' ) .'">%2$s</a></p>';
        }

        return $markup;
    }


    /**
     * Captures HTML with site identification.
     *
     * @return string
     */
    public function capture_site_id() {
        $out = '';

        // Get all required data
        $name = get_bloginfo( 'name' );
        $description = get_bloginfo( 'description' );
        $desktop_src = g1_get_theme_option( 'branding', 'logo' );

        // Compose logo element
        $out_desktop = '';
        $out_mobile = '';

        // image logo
        if ( ! empty( $desktop_src) ) {
            $set = array();

            // set desktop logo as a defualt for every media width
            $data = array(
                'data-g1-src-desktop' => $desktop_src,
                'data-g1-src-desktop-hdpi' => $desktop_src,
                'data-g1-src-mobile' => $desktop_src,
                'data-g1-src-mobile-hdpi' => $desktop_src,
            );

            // Desktop - HDPI screens
            $desktop_hdpi_src = g1_get_theme_option( 'branding', 'logo_hdpi' );
            if ( !empty( $desktop_hdpi_src ) ) {
                $data['data-g1-src-desktop-hdpi'] = $desktop_hdpi_src;
            }

            // Mobile
            $mobile_src = g1_get_theme_option( 'branding', 'logo_mobile' );
            if (  !empty( $mobile_src ) ) {
                $data['data-g1-src-mobile'] = $mobile_src;
            }

            // Mobile - HDPI screens
            $mobile_hdpi_src = g1_get_theme_option( 'branding', 'logo_mobile_hdpi' );
            if (  !empty( $mobile_hdpi_src ) ) {
                $data['data-g1-src-mobile-hdpi'] = $mobile_hdpi_src;
            } else if ( !empty( $desktop_hdpi_src ) ) {
                $data['data-g1-src-mobile-hdpi'] = $desktop_hdpi_src;
            }

            // markup
            $data_desktop_str =  sprintf(' %s="%s"', 'data-g1-src-desktop', esc_url( $data['data-g1-src-desktop'] ));
            $data_desktop_str .= sprintf(' %s="%s"', 'data-g1-src-desktop-hdpi', esc_url( $data['data-g1-src-desktop-hdpi'] ));

            $data_mobile_str =  sprintf(' %s="%s"', 'data-g1-src-mobile', esc_url( $data['data-g1-src-mobile'] ));
            $data_mobile_str .= sprintf(' %s="%s"', 'data-g1-src-mobile-hdpi', esc_url( $data['data-g1-src-mobile-hdpi'] ));

            $data_desktop_str = trim( $data_desktop_str );
            $data_mobile_str = trim( $data_mobile_str );

            $size_desktop = '';
            $logo_width = absint(g1_get_theme_option('branding', 'logo_width'));
            $logo_height = absint(g1_get_theme_option('branding', 'logo_height'));

            if ( !empty($logo_width) && !empty($logo_height) ) {
                $size_desktop = sprintf(' width="%s" height="%s"', $logo_width, $logo_height);
            }

            $size_mobile = '';
            $logo_mobile_width = absint(g1_get_theme_option('branding', 'logo_mobile_width'));
            $logo_mobile_height = absint(g1_get_theme_option('branding', 'logo_mobile_height'));

            if ( !empty($logo_mobile_width) && !empty($logo_mobile_height) ) {
                $size_mobile = sprintf(' width="%s" height="%s"', $logo_mobile_width, $logo_mobile_height);
            } else if ( !empty($logo_width) && !empty($logo_height) ) {
                $size_mobile = sprintf(' width="%s" height="%s"', $logo_width, $logo_height);
            }

            $out_desktop =
                '<img'. $size_desktop .' id="g1-logo" src="'. esc_url($desktop_src) .'" alt="'. esc_attr($name) .'" '. $data_desktop_str .' />
                <noscript><img src="'. esc_url($desktop_src) .'" alt="'. esc_attr($name) .'" /></noscript>';

            $out_mobile =
                '<img'. $size_mobile .' id="g1-mobile-logo" src="'. esc_url($data['data-g1-src-mobile']) .'" alt="'. esc_attr($name) .'" '. $data_mobile_str .' />';

            // text logo
        } else {
            $out_desktop = esc_html( $name );
        }

        $markup = apply_filters( 'g1_site_id_markup', '<h1 class="site-title"><a href="%1$s">%2$s</a></h1>' );

        $out .= '<div id="g1-id">';

        $out .= sprintf(
            $markup,
            esc_url( home_url() ),
            $out_desktop . $out_mobile
        );

        // Capture tagline
        if ( 'none' !== g1_get_theme_option( 'ta_header', 'tagline' ) && strlen( $description ) ) {
            if ( is_front_page() ) {
                $out .= '<p class="site-description"><strong>' . esc_html( $description ) . '</strong></p>';
            } else {
                $out .= '<p class="site-description">' . esc_html( $description ) . '</p>';
            }
        }
        $out .= '</div><!-- #id -->';

        return $out;
    }

    public function render_site_id() {
        echo $this->capture_site_id();
    }

    public function primary_full_body_class( $classes ) {
        $classes[] = 'g1-primary-full';
        return $classes;
    }

    public function secondary_before_body_class( $classes ) {
        $classes[] = 'g1-secondary-before';
        return $classes;
    }
    public function secondary_after_body_class( $classes ) {
        $classes[] = 'g1-secondary-after';
        return $classes;
    }
    public function secondary_none_body_class( $classes ) {
        $classes[] = 'g1-secondary-none';
        return $classes;
    }
    public function secondary_wide_body_class( $classes ) {
        $classes[] = 'g1-secondary-wide';
        return $classes;
    }
    public function secondary_narrow_body_class( $classes ) {
        $classes[] = 'g1-secondary-narrow';
        return $classes;
    }


    public function mediabox_before_body_class( $classes ) {
        $classes[] = 'g1-mediabox-before';
        return $classes;
    }
    public function mediabox_after_body_class( $classes ) {
        $classes[] = 'g1-mediabox-after';
        return $classes;
    }
    public function mediabox_wide_body_class( $classes ) {
        $classes[] = 'g1-mediabox-wide';
        return $classes;
    }
    public function mediabox_narrow_body_class( $classes ) {
        $classes[] = 'g1-mediabox-narrow';
        return $classes;
    }





}

/**
 * Quasi-singleton for our theme
 *
 * @return G1_Theme
 */
function G1_Theme() {
    static $instance;

    if ( !isset( $instance ) ) {
        $instance = new G1_Theme( 'g1_theme03' );
    }

    return $instance;
}
// Fire in the hole :)
G1_Theme();


if ( is_admin() ) {
    require_once( G1_LIB_DIR . '/theme-admin.php' );
} else {
    require_once( G1_LIB_DIR . '/theme-front.php' );
}




/**
 * Gets a theme option
 */
function g1_get_theme_option( $base, $key, $default = false ) {
    static $storage_values = null;

    $storage_name = G1_Theme()->get_id();

    if ( null === $storage_values || defined('G1_CUSTOMIZE_MODE')) {
        $storage_values = get_option( $storage_name , array() );
    }

    $option_name = $base;

    if ( strlen($key) > 0 ) {
        $option_name .= '_' .$key;
    }

    $result = isset( $storage_values[ $option_name ] ) ? $storage_values[ $option_name ] : $default;

    return $result;
}

function g1_get_font_awesome ( $icon_name = null ) {
    $list = array('adjust' => 'adjust',
        'adn' => 'adn',
        'align-center' => 'align-center',
        'align-justify' => 'align-justify',
        'align-left' => 'align-left',
        'align-right' => 'align-right',
        'ambulance' => 'ambulance',
        'anchor' => 'anchor',
        'android' => 'android',
        'angle-down' => 'angle-down',
        'angle-left' => 'angle-left',
        'angle-right' => 'angle-right',
        'angle-up' => 'angle-up',
        'apple' => 'apple',
        'archive' => 'archive',
        'arrow-down' => 'arrow-down',
        'arrow-left' => 'arrow-left',
        'arrow-right' => 'arrow-right',
        'arrow-up' => 'arrow-up',
        'asterisk' => 'asterisk',
        'backward' => 'backward',
        'ban-circle' => 'ban-circle',
        'bar-chart' => 'bar-chart',
        'barcode' => 'barcode',
        'beaker' => 'beaker',
        'beer' => 'beer',
        'bell' => 'bell',
        'bell-alt' => 'bell-alt',
        'bitbucket' => 'bitbucket',
        'bitbucket-sign' => 'bitbucket-sign',
        'bold' => 'bold',
        'bolt' => 'bolt',
        'book' => 'book',
        'bookmark' => 'bookmark',
        'bookmark-empty' => 'bookmark-empty',
        'briefcase' => 'briefcase',
        'btc' => 'btc',
        'bug' => 'bug',
        'building' => 'building',
        'bullhorn' => 'bullhorn',
        'bullseye' => 'bullseye',
        'calendar' => 'calendar',
        'calendar-empty' => 'calendar-empty',
        'camera' => 'camera',
        'camera-retro' => 'camera-retro',
        'caret-down' => 'caret-down',
        'caret-left' => 'caret-left',
        'caret-right' => 'caret-right',
        'caret-up' => 'caret-up',
        'certificate' => 'certificate',
        'check' => 'check',
        'check-empty' => 'check-empty',
        'check-minus' => 'check-minus',
        'check-sign' => 'check-sign',
        'chevron-down' => 'chevron-down',
        'chevron-left' => 'chevron-left',
        'chevron-right' => 'chevron-right',
        'chevron-sign-down' => 'chevron-sign-down',
        'chevron-sign-left' => 'chevron-sign-left',
        'chevron-sign-right' => 'chevron-sign-right',
        'chevron-sign-up' => 'chevron-sign-up',
        'chevron-up' => 'chevron-up',
        'circle' => 'circle',
        'circle-arrow-down' => 'circle-arrow-down',
        'circle-arrow-left' => 'circle-arrow-left',
        'circle-arrow-right' => 'circle-arrow-right',
        'circle-arrow-up' => 'circle-arrow-up',
        'circle-blank' => 'circle-blank',
        'cloud' => 'cloud',
        'cloud-download' => 'cloud-download',
        'cloud-upload' => 'cloud-upload',
        'cny' => 'cny',
        'code' => 'code',
        'code-fork' => 'code-fork',
        'coffee' => 'coffee',
        'cog' => 'cog',
        'cogs' => 'cogs',
        'collapse' => 'collapse',
        'collapse-alt' => 'collapse-alt',
        'collapse-top' => 'collapse-top',
        'columns' => 'columns',
        'comment' => 'comment',
        'comment-alt' => 'comment-alt',
        'comments' => 'comments',
        'comments-alt' => 'comments-alt',
        'compass' => 'compass',
        'copy' => 'copy',
        'credit-card' => 'credit-card',
        'crop' => 'crop',
        'css3' => 'css3',
        'cut' => 'cut',
        'dashboard' => 'dashboard',
        'desktop' => 'desktop',
        'double-angle-down' => 'double-angle-down',
        'double-angle-left' => 'double-angle-left',
        'double-angle-right' => 'double-angle-right',
        'double-angle-up' => 'double-angle-up',
        'download' => 'download',
        'download-alt' => 'download-alt',
        'dribble' => 'dribble',
        'dropbox' => 'dropbox',
        'edit' => 'edit',
        'edit-sign' => 'edit-sign',
        'eject' => 'eject',
        'ellipsis-horizontal' => 'ellipsis-horizontal',
        'ellipsis-vertical' => 'ellipsis-vertical',
        'envelope' => 'envelope',
        'envelope-alt' => 'envelope-alt',
        'eraser' => 'eraser',
        'eur' => 'eur',
        'exchange' => 'exchange',
        'exclamation' => 'exclamation',
        'exclamation-sign' => 'exclamation-sign',
        'expand' => 'expand',
        'expand-alt' => 'expand-alt',
        'external-link' => 'external-link',
        'external-link-sign' => 'external-link-sign',
        'eye-close' => 'eye-close',
        'eye-open' => 'eye-open',
        'facebook' => 'facebook',
        'facebook-sign' => 'facebook-sign',
        'facetime-video' => 'facetime-video',
        'fast-backward' => 'fast-backward',
        'fast-forward' => 'fast-forward',
        'female' => 'female',
        'fighter-jet' => 'fighter-jet',
        'file' => 'file',
        'file-alt' => 'file-alt',
        'file-text' => 'file-text',
        'file-text-alt' => 'file-text-alt',
        'film' => 'film',
        'filter' => 'filter',
        'fire' => 'fire',
        'fire-extinguisher' => 'fire-extinguisher',
        'flag' => 'flag',
        'flag-alt' => 'flag-alt',
        'flag-checkered' => 'flag-checkered',
        'flickr' => 'flickr',
        'folder-close' => 'folder-close',
        'folder-close-alt' => 'folder-close-alt',
        'folder-open' => 'folder-open',
        'folder-open-alt' => 'folder-open-alt',
        'font' => 'font',
        'food' => 'food',
        'forward' => 'forward',
        'foursquare' => 'foursquare',
        'frown' => 'frown',
        'fullscreen' => 'fullscreen',
        'gamepad' => 'gamepad',
        'gbp' => 'gbp',
        'gift' => 'gift',
        'github' => 'github',
        'github-alt' => 'github-alt',
        'github-sign' => 'github-sign',
        'gittip' => 'gittip',
        'glass' => 'glass',
        'globe' => 'globe',
        'google-plus' => 'google-plus',
        'google-plus-sign' => 'google-plus-sign',
        'group' => 'group',
        'h-sign' => 'h-sign',
        'hand-down' => 'hand-down',
        'hand-left' => 'hand-left',
        'hand-right' => 'hand-right',
        'hand-up' => 'hand-up',
        'hdd' => 'hdd',
        'headphones' => 'headphones',
        'heart' => 'heart',
        'heart-empty' => 'heart-empty',
        'home' => 'home',
        'hospital' => 'hospital',
        'html5' => 'html5',
        'inbox' => 'inbox',
        'indent-left' => 'indent-left',
        'indent-right' => 'indent-right',
        'info' => 'info',
        'info-sign' => 'info-sign',
        'inr' => 'inr',
        'instagram' => 'instagram',
        'italic' => 'italic',
        'jpy' => 'jpy',
        'key' => 'key',
        'keyboard' => 'keyboard',
        'krw' => 'krw',
        'laptop' => 'laptop',
        'leaf' => 'leaf',
        'legal' => 'legal',
        'lemon' => 'lemon',
        'level-down' => 'level-down',
        'level-up' => 'level-up',
        'lightbulb' => 'lightbulb',
        'link' => 'link',
        'linkedin' => 'linkedin',
        'linkedin-sign' => 'linkedin-sign',
        'linux' => 'linux',
        'list' => 'list',
        'list-alt' => 'list-alt',
        'list-ol' => 'list-ol',
        'list-ul' => 'list-ul',
        'location-arrow' => 'location-arrow',
        'lock' => 'lock',
        'long-arrow-down' => 'long-arrow-down',
        'long-arrow-left' => 'long-arrow-left',
        'long-arrow-right' => 'long-arrow-right',
        'long-arrow-up' => 'long-arrow-up',
        'magic' => 'magic',
        'magnet' => 'magnet',
        'mail-reply-all' => 'mail-reply-all',
        'male' => 'male',
        'map-marker' => 'map-marker',
        'maxcdn' => 'maxcdn',
        'medkit' => 'medkit',
        'meh' => 'meh',
        'microphone' => 'microphone',
        'microphone-off' => 'microphone-off',
        'minus' => 'minus',
        'minus-sign' => 'minus-sign',
        'minus-sign-alt' => 'minus-sign-alt',
        'mobile-phone' => 'mobile-phone',
        'money' => 'money',
        'moon' => 'moon',
        'move' => 'move',
        'music' => 'music',
        'off' => 'off',
        'ok' => 'ok',
        'ok-circle' => 'ok-circle',
        'ok-sign' => 'ok-sign',
        'paper-clip' => 'paper-clip',
        'paste' => 'paste',
        'pause' => 'pause',
        'pencil' => 'pencil',
        'phone' => 'phone',
        'phone-sign' => 'phone-sign',
        'picture' => 'picture',
        'pinterest' => 'pinterest',
        'pinterest-sign' => 'pinterest-sign',
        'plane' => 'plane',
        'play' => 'play',
        'play-circle' => 'play-circle',
        'play-sign' => 'play-sign',
        'plus' => 'plus',
        'plus-sign' => 'plus-sign',
        'plus-sign-alt' => 'plus-sign-alt',
        'print' => 'print',
        'pushpin' => 'pushpin',
        'puzzle-piece' => 'puzzle-piece',
        'qrcode' => 'qrcode',
        'question' => 'question',
        'question-sign' => 'question-sign',
        'quote-left' => 'quote-left',
        'quote-right' => 'quote-right',
        'random' => 'random',
        'refresh' => 'refresh',
        'remove' => 'remove',
        'remove-circle' => 'remove-circle',
        'remove-sign' => 'remove-sign',
        'renren' => 'renren',
        'reorder' => 'reorder',
        'repeat' => 'repeat',
        'reply' => 'reply',
        'reply-all' => 'reply-all',
        'resize-full' => 'resize-full',
        'resize-horizontal' => 'resize-horizontal',
        'resize-small' => 'resize-small',
        'resize-vertical' => 'resize-vertical',
        'retweet' => 'retweet',
        'road' => 'road',
        'rocket' => 'rocket',
        'rss' => 'rss',
        'rss-sign' => 'rss-sign',
        'save' => 'save',
        'screenshot' => 'screenshot',
        'search' => 'search',
        'share' => 'share',
        'share-alt' => 'share-alt',
        'share-sign' => 'share-sign',
        'shield' => 'shield',
        'shopping-cart' => 'shopping-cart',
        'sign-blank' => 'sign-blank',
        'signal' => 'signal',
        'signin' => 'signin',
        'signout' => 'signout',
        'sitemap' => 'sitemap',
        'skype' => 'skype',
        'smile' => 'smile',
        'sort' => 'sort',
        'sort-by-alphabet' => 'sort-by-alphabet',
        'sort-by-alphabet-alt' => 'sort-by-alphabet-alt',
        'sort-by-attributes' => 'sort-by-attributes',
        'sort-by-attributes-alt' => 'sort-by-attributes-alt',
        'sort-by-order' => 'sort-by-order',
        'sort-by-order-alt' => 'sort-by-order-alt',
        'sort-down' => 'sort-down',
        'sort-up' => 'sort-up',
        'spinner' => 'spinner',
        'stackexchange' => 'stackexchange',
        'star' => 'star',
        'star-empty' => 'star-empty',
        'star-half' => 'star-half',
        'star-half-empty' => 'star-half-empty',
        'step-backward' => 'step-backward',
        'step-forward' => 'step-forward',
        'stethoscope' => 'stethoscope',
        'stop' => 'stop',
        'strikethrough' => 'strikethrough',
        'subscript' => 'subscript',
        'suitcase' => 'suitcase',
        'sun' => 'sun',
        'superscript' => 'superscript',
        'table' => 'table',
        'tablet' => 'tablet',
        'tag' => 'tag',
        'tags' => 'tags',
        'tasks' => 'tasks',
        'terminal' => 'terminal',
        'text-height' => 'text-height',
        'text-width' => 'text-width',
        'th' => 'th',
        'th-large' => 'th-large',
        'th-list' => 'th-list',
        'thumbs-down' => 'thumbs-down',
        'thumbs-down-alt' => 'thumbs-down-alt',
        'thumbs-up' => 'thumbs-up',
        'thumbs-up-alt' => 'thumbs-up-alt',
        'ticket' => 'ticket',
        'time' => 'time',
        'tint' => 'tint',
        'trash' => 'trash',
        'trello' => 'trello',
        'trophy' => 'trophy',
        'truck' => 'truck',
        'tumblr' => 'tumblr',
        'tumblr-sign' => 'tumblr-sign',
        'twitter' => 'twitter',
        'twitter-sign' => 'twitter-sign',
        'umbrella' => 'umbrella',
        'underline' => 'underline',
        'undo' => 'undo',
        'unlink' => 'unlink',
        'unlock' => 'unlock',
        'unlock-alt' => 'unlock-alt',
        'upload' => 'upload',
        'upload-alt' => 'upload-alt',
        'usd' => 'usd',
        'user' => 'user',
        'user-md' => 'user-md',
        'vk' => 'vk',
        'volume-down' => 'volume-down',
        'volume-off' => 'volume-off',
        'volume-up' => 'volume-up',
        'warning-sign' => 'warning-sign',
        'weibo' => 'weibo',
        'windows' => 'windows',
        'wrench' => 'wrench',
        'xing' => 'xing',
        'xing-sign' => 'xing-sign',
        'youtube' => 'youtube',
        'youtube-play' => 'youtube-play',
        'youtube-sign' => 'youtube-sign',
        'zoom-in' => 'zoom-in',
        'zoom-out' => 'zoom-out',
    );

    if ( !is_null($icon_name) ) {
        $index = array_search( $icon_name, $list );

        if ( $index !== false ) {
            return array(
                'code'  =>  $index,
                'name'  =>  $list[$index]
            );
        }

        return null;
    }

    return $list;
}



