<?php
require_once(ABSPATH . 'wp-admin/includes/admin.php');
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));


// In case admin-header.php is included in a function.
global $title, $hook_suffix, $current_screen, $wp_locale, $pagenow, $wp_version,
	$update_title, $total_update_count, $parent_file;
//var_dump($pagenow); die;
	$pagenow = 'POS';
	
$title = __('Point of Sale', 'woocommerce-pos') . ' - ' . get_bloginfo('name');

register_admin_color_schemes();

	global $is_IE;

	$admin_html_class = ( is_admin_bar_showing() ) ? 'wp-toolbar' : '';

	if ( $is_IE )
		@header('X-UA-Compatible: IE=edge');

/**
 * Fires inside the HTML tag in the admin header.
 *
 * @since 2.2.0
 */
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" class="ie8 <?php echo $admin_html_class; ?>" <?php do_action( 'admin_xml_ns' ); ?> <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8) ]><!-->
<?php /** This action is documented in wp-admin/includes/template.php */ ?>
<html xmlns="http://www.w3.org/1999/xhtml" class="<?php echo $admin_html_class; ?>" <?php do_action( 'admin_xml_ns' ); ?> <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" name="viewport" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>, width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
	<title><?php echo $title; ?></title>
<?php

wp_enqueue_style( 'colors' );
wp_enqueue_style( 'ie' );
wp_enqueue_script('utils');
wp_enqueue_script( 'svg-painter' );



$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
	adminpage = '<?php echo $admin_body_class; ?>',
	thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
	decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
	isRtl = <?php echo (int) is_rtl(); ?>;
</script>

<?php
remove_all_actions('admin_enqueue_scripts') ;
remove_action('admin_print_scripts', 'wplc_admin_scripts') ;
front_enqueue_dependencies();
do_action( 'admin_enqueue_scripts', $hook_suffix );
        
do_action( 'admin_print_scripts' );

do_action( 'admin_print_styles' );



if ( get_user_setting('mfold') == 'f' )
	$admin_body_class .= ' folded';

if ( !get_user_setting('unfold') )
	$admin_body_class .= ' auto-fold';

if ( is_admin_bar_showing() )
	$admin_body_class .= ' admin-bar';

if ( is_rtl() )
	$admin_body_class .= ' rtl';


$admin_body_class .= ' branch-' . str_replace( array( '.', ',' ), '-', floatval( $wp_version ) );
$admin_body_class .= ' version-' . str_replace( '.', '-', preg_replace( '/^([.0-9]+).*/', '$1', $wp_version ) );
$admin_body_class .= ' admin-color-' . sanitize_html_class( get_user_option( 'admin_color' ), 'fresh' );
$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

if ( wp_is_mobile() )
	$admin_body_class .= ' mobile';

if ( is_multisite() )
	$admin_body_class .= ' multisite';

if ( is_network_admin() )
	$admin_body_class .= ' network-admin';

$admin_body_class .= ' no-customize-support no-svg';

?>
</head>
<body class="wp-admin wp-core-ui no-js wc_poin_of_sale_body <?php echo $admin_body_class; ?>">
<?php if(!isset($_GET['print_pos_report']) && !isset($_GET['print_pos_receipt']) ){ ?>
	<script type="text/javascript">
		document.body.className = document.body.className.replace('no-js','js');
	</script>


	<div id="wpwrap">
	<a tabindex="1" href="#wpbody-content" class="screen-reader-shortcut"><?php _e('Skip to main content'); ?></a>

	<div id="wpcontent">

	<?php
	/**
	 * Fires at the beginning of the content section in an admin page.
	 *
	 * @since 3.0.0
	 */
	do_action( 'in_admin_header' );

	set_current_screen('pos_page');
	?>

	<div id="wpbody">
	<?php
	unset($title_class, $blog_name, $total_update_count, $update_title);
	?>

	<div id="wpbody-content" aria-label="<?php esc_attr_e('Main content'); ?>" tabindex="0">
	<?php
	do_action( 'all_admin_notices' );

}