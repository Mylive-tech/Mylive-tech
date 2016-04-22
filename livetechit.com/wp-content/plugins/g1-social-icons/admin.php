<?php
// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php
if ( ! class_exists( 'G1_Social_Icons_Admin' ) ):

class G1_Social_Icons_Admin {
    public function __construct() {
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
    }

    public function admin_init() {
        register_setting( G1_Social_Icons()->get_option_name(), G1_Social_Icons()->get_option_name() );
    }

    public function add_admin_page() {
        add_options_page(
            __( 'G1 Social Icons', 'g1_theme' ),
            __( 'G1 Social Icons', 'g1_theme' ),
            'manage_options',
            'g1_social_icons_options',
            array( $this, 'render_admin_page' )
        );
    }

    public function capture_admin_page() {
        ob_start();
            include( 'tpl_admin.php');
        $out = ob_get_clean();

        return $out;
    }

    public function render_admin_page() {
        echo $this->capture_admin_page();
    }

    public function capture_item( $name, $color, $icon_path ) {
        $value = $this->get_item_value($name);
        $base_option_name = sprintf('%s[%s]', G1_Social_Icons()->get_option_name(), $name);

        ob_start();
        include( 'tpl_admin_item.php');
        $out = ob_get_clean();

        return $out;
    }

    public function render_item( $name, $color, $icon_path ) {
        echo $this->capture_item( $name, $color, $icon_path );
    }

    public function get_item_value ( $name ) {
        $options = get_option(G1_Social_Icons()->get_option_name());
        $value = !empty($options[$name]) ? $options[$name]: array();

        $default_value = array(
            'label'     => '',
            'caption'   => '',
            'link'      => '',
            'linking'   => 'standard'
        );

        return wp_parse_args( $value, $default_value );
    }
}
endif;

function G1_Social_Icons_Admin() {
    static $instance = null;

    if ( null === $instance )
        $instance = new G1_Social_Icons_Admin();

    return $instance;
}
// Fire in the hole :)
G1_Social_Icons_Admin();