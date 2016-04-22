<?php
define( 'IFRAME_REQUEST', true );
define( 'WP_USE_THEMES', false );
define( 'VFB_PRO_PREVIEW', true );

// Find the wp-load either one or two levels up
function vfb_get_wp_root_path(){
    $base = dirname( __FILE__ );
    $path = false;

    if ( @file_exists( dirname( dirname( $base ) ) . '/wp-load.php' ) ) :
        $path = dirname( dirname( $base ) ) . '/wp-load.php';
    else :
	    if ( @file_exists( dirname( dirname( dirname( $base ) ) ) . '/wp-load.php' ) )
	        $path = dirname( dirname( dirname( $base ) ) ) . '/wp-load.php';
	    else
	    	$path = false;
	endif;

    if ( $path != false )
        $path = str_replace( '\\', '/', $path );

    if ( @file_exists( $_SERVER['DOCUMENT_ROOT'] . '/vfb-pro-abspath.php' ) )
    	include_once( $_SERVER['DOCUMENT_ROOT'] . '/vfb-pro-abspath.php' );

    if ( defined( 'VFB_PRO_ABSPATH' ) )
    	$path = VFB_PRO_ABSPATH;

    return $path;
}

// Include the WP header so we can use WP functions and run a PHP page
if ( @is_file( vfb_get_wp_root_path() ) )
	require_once( vfb_get_wp_root_path() );
else
	die( 'Error: could not access wp-load.php. This typically happens when the wp-content folder has been moved. Please set the VFB_PRO_ABSPATH constant to manually set the path.' );

if ( !is_user_logged_in() )
	wp_die( __( 'You need to be logged in to view this document', 'visual-form-builder-pro' ) );

// If you don't have permission, get lost
if ( !current_user_can( 'vfb_edit_forms' ) )
	wp_die( __('You do not have sufficient permissions to view the preview for this site.') );

// Let's roll.
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

global $wpdb;
$form_table_name = $wpdb->prefix . 'vfb_pro_forms';

// Get form id.  Allows use of [vfb id=1] or [vfb 1]
$form_id = isset( $_REQUEST['form'] ) ? absint( $_REQUEST['form'] ) : 0;

// Get form title
$form_title = $wpdb->get_var( $wpdb->prepare( "SELECT form_title FROM $form_table_name WHERE form_id = %d", $form_id ) );

$direction  = is_rtl() ? 'rtl' : 'ltr';
$language   = get_bloginfo( 'language' );

// Visual Form Builder Pro class
$vfb_pro_preview = new Visual_Form_Builder_Pro();
?>
<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php echo esc_html( $form_title ); ?> &lsaquo; Form Preview &#8212; Visual Form Builder Pro</title>
<?php
wp_head();

$body_css = ( isset( $_REQUEST['preview'] ) && 1 == $_REQUEST['preview'] ) ? 'width:50%;margin:0 auto;' : 'width:100%;margin-top:-20px;margin-left:0;margin-right:0;';

?>
<style type="text/css">
html{
	margin-top:0 !important;
	overflow: auto;
}
body{
	<?php echo $body_css; ?>
	height:100%;
	font-family: sans-serif;
	margin-bottom:10px;
	overflow: auto;
}
</style>
</head>
<body <?php body_class(); ?>>
<?php
// Output form
echo $vfb_pro_preview->form_code( array( 'id' => $form_id ), $output = '' );

wp_footer();
?>
</body>
</html>