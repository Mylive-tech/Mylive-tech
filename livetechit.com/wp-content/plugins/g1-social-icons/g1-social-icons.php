<?php
/*
Plugin Name: G1 Social Icons
Plugin URI: http://www.bringthepixel.com
Description: Social Icons
Author: bringthepixel
Version: 1.1.0
Author URI: http://www.bringthepixel.com
License: GPLv2 or later
*/

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php
if ( ! class_exists( 'G1_Social_Icons' ) ):



class G1_Social_Icons {
    private $version = '1.1.0';
    private static $option_name = 'g1_social_icons';
    private static $items;

    public function __construct() {
        // Standard hooks for plugins
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( 'G1_Social_Icons', 'uninstall' ) );


        add_filter( 'plugins_url', array( $this, 'fix_plugin_url_symlink' ), 10, 3 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'widgets_init', array( $this, 'register_widget' ) );

        if ( is_admin() ) {
            require_once( plugin_dir_path( __FILE__ ) . 'admin.php' );
        } else {
            require_once( plugin_dir_path( __FILE__ ) . 'front.php' );
        }
    }

    public function register_widget () {
        require_once( plugin_dir_path( __FILE__ ) . '/widgets/widgets.php' );

        register_widget( 'G1_Social_Icons_Widget' );
    }

    public function enqueue() {
        wp_register_style(
            'g1-social-icons',
            trailingslashit( $this->get_plugin_dir_url() ) . 'css/main.css',
            array(),
            $this->get_version()
        );

        wp_enqueue_style( 'g1-social-icons' );
    }


    public function fix_plugin_url_symlink( $url, $path, $plugin ) {
        if ( strstr( $plugin, basename(__FILE__) ) )
            return str_replace( dirname(__FILE__), '/' . basename( dirname( $plugin ) ), $url );

        return $url;
    }

    public function get_plugin_dir_path() {
        return plugin_dir_path( __FILE__ );
    }

    public function get_plugin_dir_url() {
        return plugin_dir_url( __FILE__ );
    }

    public function get_version() {
        return $this->version;
    }

    public function get_option_name() {
        return self::$option_name;
    }

    public function get_icon_path ( $name, $size ) {
        $items = $this->get_items();

        if (isset($items[$name]['icon_dir'])) {
            $images_url = trailingslashit($items[$name]['icon_dir']);
        } else {
            $images_url = $this->get_plugin_dir_url() . 'images/';
        }

        $icon_path = $images_url . $name .  '/' . $name . '-'. $size .'.png';

        return $icon_path;
    }

    public function activate() {

    }

    public function deactivate() {

    }

    public static function uninstall() {
        delete_option( self::$option_name );
    }

    public function get_items() {
        if (empty(self::$items)) {
            $items = apply_filters( 'g1_social_icons_items', $this->get_default_items() );
            $stored_icons = get_option($this->get_option_name());

            if ($stored_icons) {
                self::$items = array();

                foreach ($stored_icons as $icon_name => $icon_data) {
                    if (!empty($items[$icon_name])) {
                        self::$items[$icon_name] = $items[$icon_name];
                    }

                    unset($items[$icon_name]);
                }

                // some icons are still in items array (new added via hook)
                if (!empty($items)) {
                    self::$items = array_merge(self::$items, $items);
                }
            } else {
                self::$items = $items;
            }
        }

        return self::$items;
    }

    private function get_default_items () {
        return array(
            '500px'             => array('color' => '#444444'),
            'aboutme'           => array('color' => '#00405D'),
            'alistapart'        => array('color' => '#222222'),
            'amazon'            => array('color' => '#FF9900'),
            'amazonwishlist'    => array('color' => '#FF9900'),
            'android'           => array('color' => '#A4C639'),
            'appdotnet'         => array('color' => '#898D90'),
            'apple'             => array('color' => '#B9BFC1'),
            'audioboo'          => array('color' => '#AE006E'),
            'aws'               => array('color' => '#FF9900'),
            'bebo'              => array('color' => '#EE1010'),
            'behance'           => array('color' => '#1769FF'),
            'blip'              => array('color' => '#FF1919'),
            'blogger'           => array('color' => '#F57D00'),
            'bootstrap'         => array('color' => '#0088CC'),
            'codepen'           => array('color' => '#231F20'),
            'codeschool'        => array('color' => '#C68044'),
            'codecademy'        => array('color' => '#0088CC'),
            'coderwall'         => array('color' => '#3E8DCC'),
            'conservatives'     => array('color' => '#0087DC'),
            'coursera'          => array('color' => '#3A6D8E'),
            'css3'              => array('color' => '#0092BF'),
            'delicious'         => array('color' => '#3274D1'),
            'designernews'      => array('color' => '#1C52A2'),
            'deviantart'        => array('color' => '#4B5D50'),
            'digg'              => array('color' => '#14589E'),
            'disqus'            => array('color' => '#2E9FFF'),
            'dribbble'          => array('color' => '#EA4C89'),
            'dropbox'           => array('color' => '#2281CF'),
            'drupal'            => array('color' => '#0077C0'),
            'ebay'              => array('color' => '#0064D2'),
            'email'             => array('color' => '#666666'),
            'eventstore'        => array('color' => '#6BA300'),
            'eventbrite'        => array('color' => '#F3844C'),
            'evernote'          => array('color' => '#7AC142'),
            'exfm'              => array('color' => '#0097F8'),
            'facebook'          => array('color' => '#3B5998'),
            'flickr'            => array('color' => '#0063DB'),
            'formspring'        => array('color' => '#0076C0'),
            'forrst'            => array('color' => '#5B9A68'),
            'foursquare'        => array('color' => '#2398C9'),
            'geeklist'          => array('color' => '#8CC63E'),
            'github'            => array('color' => '#4183C4'),
            'goodreads'         => array('color' => '#5A471B'),
            'google'            => array('color' => '#245DC1'),
            'googleplus'        => array('color' => '#D14836'),
            'govuk'             => array('color' => '#231F20'),
            'grooveshark'       => array('color' => '#000000'),
            'hackernews'        => array('color' => '#FF6600'),
            'heroku'            => array('color' => '#6762A6'),
            'html5'             => array('color' => '#F06529'),
            'imdb'              => array('color' => '#F3CE00'),
            'instagram'         => array('color' => '#3F729B'),
            'jquery'            => array('color' => '#0867AB'),
            'jqueryui'          => array('color' => '#FEA620'),
            'jsdb'              => array('color' => '#DA320B'),
            'jsfiddle'          => array('color' => '#4679A4'),
            'justgiving'        => array('color' => '#78256D'),
            'kickstarter'       => array('color' => '#87C442'),
            'klout'             => array('color' => '#E24A25'),
            'labour'            => array('color' => '#C41230'),
            'laravel'           => array('color' => '#FB502B'),
            'lastfm'            => array('color' => '#D51007'),
            'layervault'        => array('color' => '#26AE90'),
            'letterboxd'        => array('color' => '#2C3641'),
            'liberaldemocrats'  => array('color' => '#F7B135'),
            'linkedin'          => array('color' => '#007FB1'),
            'mediatemple'       => array('color' => '#000000'),
            'mendeley'          => array('color' => '#B61F2F'),
            'modernizr'         => array('color' => '#D81A76'),
            'myspace'           => array('color' => '#008DDE'),
            'nationalrail'      => array('color' => '#003366'),
            'newsvine'          => array('color' => '#075B2F'),
            'office'            => array('color' => '#EB3C00'),
            'orkut'             => array('color' => '#ED2590'),
            'outlook'           => array('color' => '#0072C6'),
            'path'              => array('color' => '#E41F11'),
            'php'               => array('color' => '#6181B6'),
            'pinboard'          => array('color' => '#0000FF'),
            'pingup'            => array('color' => '#00B1AB'),
            'pinterest'         => array('color' => '#CB2027'),
            'posterous'         => array('color' => '#FFDD68'),
            'protoio'           => array('color' => '#40C8F4'),
            'rails'             => array('color' => '#A62C39'),
            'readability'       => array('color' => '#870000'),
            'reddit'            => array('color' => '#FF4500'),
            'rss'               => array('color' => '#FF8300'),
            'simpleicons'       => array('color' => '#BF1813'),
            'skydrive'          => array('color' => '#094AB1'),
            'skype'             => array('color' => '#00AFF0'),
            'slideshare'        => array('color' => '#009999'),
            'smashingmagazine'  => array('color' => '#E95C33'),
            'soundcloud'        => array('color' => '#FF6600'),
            'spotify'           => array('color' => '#80B719'),
            'squarespace'       => array('color' => '#000000'),
            'stackexchange'     => array('color' => '#1F5196'),
            'stackoverflow'     => array('color' => '#F47920'),
            'stumbleupon'       => array('color' => '#EB4924'),
            'superuser'         => array('color' => '#2DABE2'),
            'ted'               => array('color' => '#FF2B06'),
            'trakt'             => array('color' => '#222222'),
            'treehouse'         => array('color' => '#7FA24C'),
            'tripadvisor'       => array('color' => '#589442'),
            'tumblr'            => array('color' => '#2C4762'),
            'twitter'           => array('color' => '#39A9E0'),
            'typo3'             => array('color' => '#FF8700'),
            'viadeo'            => array('color' => '#F4982B'),
            'vimeo'             => array('color' => '#44BBFF'),
            'vine'              => array('color' => '#00A47A'),
            'visualstudio'      => array('color' => '#68217A'),
            'w3c'               => array('color' => '#0066B0'),
            'windows'           => array('color' => '#00BDF6'),
            'wordpress'         => array('color' => '#21759B'),
            'yahoo'             => array('color' => '#731A8B'),
            'yelp'              => array('color' => '#C93C27'),
            'youtube'           => array('color' => '#CD332D'),
        );
    }
}
endif;

function G1_Social_Icons() {
    static $instance = null;

    if ( null === $instance )
        $instance = new G1_Social_Icons();

    return $instance;
}
// Fire in the hole :)
G1_Social_Icons();