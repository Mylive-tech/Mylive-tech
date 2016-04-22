<?php
define( 'WP_USE_THEMES', false );

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
if ( !current_user_can( 'vfb_edit_email_design' ) )
	wp_die( __('You do not have sufficient permissions to view the preview for this site.', 'visual-form-builder-pro' ) );


global $wpdb;

$form_table_name = $wpdb->prefix . 'vfb_pro_forms';

// Tells us which form to get from the database
$form_id = absint( $_REQUEST['form'] );

// Query to get all forms
$order = sanitize_sql_orderby( 'form_id DESC' );
$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $form_table_name WHERE form_id = %d ORDER BY $order", $form_id ) );

// Get sender and email details
$form_title     = stripslashes( $form->form_title );
$form_subject   = stripslashes( $form->form_email_subject );
$email_design   = unserialize( $form->form_email_design );

// Set email design variables
$color_scheme 		= ( !empty( $email_design['color_scheme'] ) ) ? stripslashes( $email_design['color_scheme'] ) : '';
$format 			= ( !empty( $email_design['format'] ) ) ? stripslashes( $email_design['format'] ) : 'html';
$link_love 			= ( !empty( $email_design['link_love'] ) ) ? stripslashes( $email_design['link_love'] ) : 'yes';
$footer_text 		= ( !empty( $email_design['footer_text'] ) ) ? stripslashes( $email_design['footer_text'] ) : '';
$background_color 	= ( !empty( $email_design['background_color'] ) ) ? stripslashes( $email_design['background_color'] ) : '#eeeeee';
$header_text 		= ( !empty( $email_design['header_text'] ) ) ? stripslashes( $email_design['header_text'] ) : $form_subject;
$header_image 		= ( !empty( $email_design['header_image'] ) ) ? stripslashes( $email_design['header_image'] ) : '';
$header_color 		= ( !empty( $email_design['header_color'] ) ) ? stripslashes( $email_design['header_color'] ) : '#810202';
$header_text_color 	= ( !empty( $email_design['header_text_color'] ) ) ? stripslashes( $email_design['header_text_color'] ) : '#ffffff';
$fieldset_color 	= ( !empty( $email_design['fieldset_color'] ) ) ? stripslashes( $email_design['fieldset_color'] ) : '#680606';
$section_color 		= ( !empty( $email_design['section_color'] ) ) ? stripslashes( $email_design['section_color'] ) : '#5C6266';
$section_text_color	= ( !empty( $email_design['section_text_color'] ) ) ? stripslashes( $email_design['section_text_color'] ) : '#ffffff';
$text_color 		= ( !empty( $email_design['text_color'] ) ) ? stripslashes( $email_design['text_color'] ) : '#333333';
$link_color 		= ( !empty( $email_design['link_color'] ) ) ? stripslashes( $email_design['link_color'] ) : '#1b8be0';
$row_color 			= ( !empty( $email_design['row_color'] ) ) ? stripslashes( $email_design['row_color'] ) : '#ffffff';
$row_alt_color 		= ( !empty( $email_design['row_alt_color'] ) ) ? stripslashes( $email_design['row_alt_color'] ) : '#eeeeee';
$border_color 		= ( !empty( $email_design['border_color'] ) ) ? stripslashes( $email_design['border_color'] ) : '#cccccc';
$footer_color 		= ( !empty( $email_design['footer_color'] ) ) ? stripslashes( $email_design['footer_color'] ) : '#333333';
$footer_text_color 	= ( !empty( $email_design['footer_text_color'] ) ) ? stripslashes( $email_design['footer_text_color'] ) : '#ffffff';
$font_family 		= ( !empty( $email_design['font_family'] ) ) ? stripslashes( $email_design['font_family'] ) : 'Arial';
$header_font_size 	= ( !empty( $email_design['header_font_size'] ) ) ? stripslashes( $email_design['header_font_size'] ) : 32;
$fieldset_font_size = ( !empty( $email_design['fieldset_font_size'] ) ) ? stripslashes( $email_design['fieldset_font_size'] ) : 20;
$section_font_size 	= ( !empty( $email_design['section_font_size'] ) ) ? stripslashes( $email_design['section_font_size'] ) : 15;
$text_font_size 	= ( !empty( $email_design['text_font_size'] ) ) ? stripslashes( $email_design['text_font_size'] ) : 13;
$footer_font_size 	= ( !empty( $email_design['footer_font_size'] ) ) ? stripslashes( $email_design['footer_font_size'] ) : 11;

$html_link_love = $plain_text_link_love = '';

// Setup the link love
if ( empty( $link_love ) || $link_love == 'yes' ) {
	$html_link_love = 'This email was built and sent using <a href="http://vfbpro.com" style="font-size: ' . $footer_font_size . 'px;font-family: ' . $font_family . ';color:' . $link_color . ';">Visual Form Builder Pro</a>.';
	$plain_text_link_love = 'This email was built and sent using<br/>Visual Form Builder Pro (http://vfbpro.com)';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title><?php echo $form_title; ?></title>
</head>
<?php if ( empty( $format ) || $format == 'html' ): ?>
<body style="background-color: <?php echo $background_color; ?>">
<table class="bg1" cellspacing="0" border="0" style="background-color: <?php echo $background_color; ?>;" cellpadding="0" width="100%">
    <tr>
        <td align="center">
            <table class="bg2" cellspacing="0" border="0" style="background-color: #ffffff;" cellpadding="0" width="600">
                <tr>
                    <td class="permission" align="center" style="background-color: <?php echo $background_color; ?>;padding: 10px 20px 10px 20px;">&nbsp;</td>
                </tr>
                <tr>
                	<?php if ( $header_image == '' ): ?>
                    <td class="header" align="left" style="background-color:<?php echo $header_color; ?>;padding: 50px 20px 50px 20px;"><h1 style="font-family: <?php echo $font_family; ?>;font-size: <?php echo $header_font_size; ?>px;font-weight:normal;margin:0;padding:0;color:<?php echo $header_text_color; ?>;"><?php echo $header_text; ?></h1></td>
                    <?php else: ?>
                    <?php @list( $width, $height, $type, $attr ) = getimagesize( $header_image ); ?>
                    <td class="header" align="left"><img <?php echo $attr; ?> src="<?php echo $header_image; ?>" /></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td class="body" valign="top" style="background-color: <?php echo $row_color; ?>;padding: 0 20px 20px 20px;">
                        <table cellspacing="0" border="0" cellpadding="0" width="100%">
                            <tr>
                                <td class="mainbar" align="left" valign="top"><h2 style="font-size: <?php echo $fieldset_font_size; ?>px; font-weight: bold; margin: 10px 0 10px 0; font-family: <?php echo $font_family; ?>; color: <?php echo $fieldset_color; ?>; padding: 0;">Fieldset</h2>
                                    <table cellspacing="0" border="0" cellpadding="0" width="100%">
                                        <tr>
                                            <td colspan="2" style="background-color:<?php echo $section_color; ?>;color:<?php echo $section_text_color; ?>;"><h3 style="font-size: <?php echo $section_font_size; ?>px; font-weight: bold; margin: 14px 14px 14px 10px; font-family: <?php echo $font_family; ?>; color: <?php echo $section_text_color; ?>; padding: 0;">Section</h3></td>
                                        </tr>

                                        <tr>
                                            <td class="mainbar" align="left" valign="top" width="100" style="border-bottom:1px solid <?php echo $border_color; ?>;"><p style="font-size: <?php echo $text_font_size; ?>px; font-weight: bold; margin: 14px 0 14px 5px; font-family: <?php echo $font_family; ?>; color: <?php echo $text_color; ?>; padding: 0;">First Row:</p></td>
                                            <td class="mainbar" align="left" valign="top" width="300" style="border-bottom:1px solid <?php echo $border_color; ?>;"><p style="font-size: <?php echo $text_font_size; ?>px; font-weight: normal; margin: 14px 0 14px 0; font-family: <?php echo $font_family; ?>; color: <?php echo $text_color; ?>; padding: 0;">Lorem ipsum</p></td>
                                        </tr>

                                        <tr>
                                            <td class="mainbar" align="left" valign="top" width="100" style="background-color:<?php echo $row_alt_color; ?>;border-bottom:1px solid <?php echo $border_color; ?>;"><p style="font-size: <?php echo $text_font_size; ?>px; font-weight: bold; margin: 14px 0 14px 5px; font-family: <?php echo $font_family; ?>; color: <?php echo $text_color; ?>; padding: 0;">Second Row:</p></td>
                                            <td class="mainbar" align="left" valign="top" width="300" style="background-color:<?php echo $row_alt_color; ?>;border-bottom:1px solid <?php echo $border_color; ?>;"><p style="font-size: <?php echo $text_font_size; ?>px;font-weight: normal; margin: 14px 0 14px 0; font-family: <?php echo $font_family; ?>; color: <?php echo $text_color; ?>; padding: 0;">Lorem Ipsum</p></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="footer" height="61" align="left" valign="middle" style="background-color: <?php echo $footer_color; ?>; padding: 0 20px 0 20px; height: 61px; vertical-align: middle;"><p style="font-size: <?php echo $footer_font_size; ?>px; font-weight: normal; margin: 0; font-family: <?php echo $font_family; ?>;line-height: 16px; color: <?php echo $footer_text_color; ?>;padding: 0;">
                    <?php echo $html_link_love; ?>
                    <?php echo $footer_text; ?>
                    </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="permission" align="center" style="background-color: <?php echo $background_color; ?>; padding: 10px 20px 10px 20px;">&nbsp;</td>
    </tr>
</table>
</body>
<?php elseif ( $format == 'text' ): ?>
<body>
<table class="bg1" cellspacing="0" border="0" style="background-color: white;font-size:12px; font-family: 'Bitstream Vera Sans Mono',monaco,'Courier New',courier,monospace;" cellpadding="0" width="100%">
    <tr>
        <td>
        ============ <?php echo $form_subject; ?> =============
		</td>
    </tr>
    <tr>
        <td>
        ------------------------------<br />
        Fieldset<br />
        ------------------------------
		</td>
    </tr>
    <tr>
        <td>
        *** Section ***
		</td>
    </tr>
    <tr>
        <td>
        First Row: Lorem ipsum
		</td>
    </tr>
    <tr>
        <td>
        Second Row: Lorem ipsum
		</td>
    </tr>
    <tr>
        <td>
        - - - - - - - - - - - -<br />
        <?php echo $plain_text_link_love; ?>
		</td>
    </tr>
    <tr>
        <td>
        <?php echo $footer_text; ?>
		</td>
    </tr>
</table>
<?php endif; ?>
</html>