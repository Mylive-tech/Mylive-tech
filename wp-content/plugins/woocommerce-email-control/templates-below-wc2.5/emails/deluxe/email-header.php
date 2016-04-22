<?php
/**
 * Email Header
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $ec_email_html, $ec_email_css;

/* Load WooCommerce settings
---------------------------------------- */
$wc_bg 			= get_option( 'woocommerce_email_background_color' );
$wc_body		= get_option( 'woocommerce_email_body_background_color' );
$wc_base 		= get_option( 'woocommerce_email_base_color' );
$wc_base_text 	= wc_light_or_dark( $wc_base, '#202020', '#ffffff' );
$wc_text 		= get_option( 'woocommerce_email_text_color' );

$wc_bg_darker_10 = wc_hex_darker( $wc_bg, 10 );
$wc_base_lighter_20 = wc_hex_lighter( $wc_base, 20 );
$wc_text_lighter_20 = wc_hex_lighter( $wc_text, 20 );

if (false) {
	?>
	<span class="testing-block" style="background: <?php echo $wc_bg ?> ">woocommerce_email_background_color</span>
	<span class="testing-block" style="background: <?php echo $wc_body ?> ">woocommerce_email_body_background_color</span>
	<span class="testing-block" style="background: <?php echo $wc_base ?> ">woocommerce_email_base_color</span>
	<span class="testing-block" style="background: <?php echo $wc_base_text ?> ">base_text (automatically set. light or dark according to base color)</span>
	<span class="testing-block" style="background: <?php echo $wc_text ?> ">woocommerce_email_text_color</span>

	<span class="testing-block" style="background: <?php echo $wc_bg_darker_10 ?> ">$wc_bg_darker_10 (automatically set)</span>
	<span class="testing-block" style="background: <?php echo $wc_base_lighter_20 ?> ">$wc_base_lighter_20 (automatically set)</span>
	<span class="testing-block" style="background: <?php echo $wc_text_lighter_20 ?> ">$wc_text_lighter_20 (automatically set)</span>
	<?php
}

/* Load our settings
---------------------------------------- */
//Background Styling
$back_bg_color	= get_option( "ec_deluxe_all_background_color" ); //"#f7f7f5";

//Email Sizing
$email_width  				= get_option( "ec_deluxe_all_email_width" ); //700px
$email_border_radius		= get_option( "ec_deluxe_all_border_radius" ); //5px
$email_padding_left_right	= 55; //px
$email_padding_top_bottom	= 30; //px

//Header Styling
$header_bg_color				= get_option( "ec_deluxe_all_header_color" );  //"#fdfdfd"; //$wc_base;
$header_text_color				= wc_light_or_dark( $header_bg_color, wc_hex_darker($header_bg_color, 55), wc_hex_lighter($header_bg_color, 85) );
$header_border_bottom_color		= wc_hex_darker($header_bg_color, 5);
$header_logo_alignment			= get_option( "ec_deluxe_all_logo_position" );

$header_img_src					= esc_url_raw( get_option( 'ec_deluxe_all_header_logo' ) );
if ( !isset($header_img_src) || $header_img_src=='' )
	$header_img_src = esc_url_raw( get_option( 'woocommerce_email_header_image' ) );


//Main Body Styling
$body_bg_color			= get_option( "ec_deluxe_all_page_color" ); //"#ffffff";
$body_color				= get_option( "ec_deluxe_all_text_color" );
$body_accent_color		= get_option( "ec_deluxe_all_text_accent_color" ); //#988255
$body_text_color		= get_option( "ec_deluxe_all_text_color" ); // "#3d3d3d"; //$wc_text;
$body_text_size 		= 14; //px
$body_letter_spacing	= 0.1; //em
$body_line_height		= get_option( "ec_deluxe_all_line_height" ); //1.3em
$body_border_color		= wc_hex_darker($back_bg_color, 6);


$top_nav_bg_color		= wc_hex_darker($body_bg_color, 2);
$top_nav_border_color	= wc_hex_darker($body_bg_color, 4);
$top_nav_position		= ( $header_logo_alignment == 'center' ) ? 'center' : 'right' ;


$heading_1_size			= get_option( "ec_deluxe_all_heading_1_size" ); //px

$body_h2_color 			= $body_text_color; //"3d3d3d"
$body_h2_size 			= get_option( "ec_deluxe_all_heading_2_size" ); //px
$body_h2_decoration 	= "none";
$body_h2_style			= "none";
$body_h2_weight			= "bold";
$body_h2_transform		= "uppercase";
$body_h2_border_width	= get_option( "ec_deluxe_all_heading_2_line_width" ); //2px
$body_h2_border_color	= get_option( "ec_deluxe_all_heading_2_line_color" ); //"#000000";

$body_a_color 			= $body_accent_color;
$body_a_decoration 		= "underline";
$body_a_style			= "none";

$body_important_a_color 	= $body_accent_color;
$body_important_a_decoration	= "underline";
$body_important_a_style		= "none";
$body_important_a_size		= "17";
$body_important_a_weight	= "bold";

$body_highlight_color		= $body_accent_color;
$body_highlight_decoration	= "none";
$body_highlight_style		= "none";

//Oder Items Table

$order_items_table_outer_border_style		= get_option( "ec_deluxe_all_order_item_table_style" ); //none, dotted, etc;
$order_items_table_outer_border_width		= get_option( "ec_deluxe_all_order_item_table_outer_border_width" ); //0 px
$order_items_table_outer_border_color		= get_option( "ec_deluxe_all_table_outer_border_color" ); //red";

$order_items_table_bg_color					= ( $order_items_table_outer_border_style != 'none' ) ? get_option( "ec_deluxe_all_order_item_table_bg_color" ) : 'none' ; //red";
$order_items_table_outer_border_radius		= ( $order_items_table_outer_border_style != 'none' ) ? get_option( "ec_deluxe_all_order_item_table_radius" ) : '0' ; //3px

$order_items_table_inner_border_width		= 1; //px
$order_items_table_inner_border_style		= get_option( "ec_deluxe_all_border_style" ); //"dotted";
$order_items_table_inner_border_color		= get_option( "ec_deluxe_all_border_color" ); //"#d4d4d4";

$order_items_table_header_bg_color		= "none";
$order_items_table_td_padding			= 9; //px

//Footer Styling
$footer_bg_color			= $top_nav_bg_color; // get_option( "ec_deluxe_all_footer_color" ); // "#F9F9F5";
$footer_text_color			= wc_light_or_dark( $footer_bg_color, wc_hex_darker($footer_bg_color, 60), wc_hex_lighter($footer_bg_color, 60) );
$footer_border_bottom_color	= wc_hex_darker($footer_bg_color, 5);
$footer_a_color				= "#3C3C3C";
$footer_a_decoration		= "none";
$footer_a_style				= "none";


/* Generate CSS
---------------------------------------- */
ob_start();
?>
<style>

/* Main Styles ---------- */
body { margin: 0; padding: 0; font-family: Arial, sans-serif; line-height: <?php echo $body_line_height ?>em; }
table, td, tr { font-family: Arial, sans-serif; line-height: <?php echo $body_line_height ?>em; }
table { color: <?php echo $body_text_color ?>;}
p { margin: .6em 0; }
ul { padding-left: 18px; }
li { padding-bottom: 3px; }
h1, h2, h3, h4, h5, h6 { font-family: Arial, sans-serif; }
/* img { vertical-align: text-bottom; } */

a { color: <?php echo $body_text_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: <?php echo $body_a_decoration ?>; }

.wrapper { font-family: Arial, sans-serif; font-size: <?php echo $body_text_size ?>px; color: <?php echo $body_text_color ?>; background-color: <?php echo esc_attr( $back_bg_color ) ?>; width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 50px 0 50px 0;}
.main-body { font-family: Arial, sans-serif; box-shadow: 0 3px 9px rgba(0, 0, 0, 0.03); border-radius: <?php echo $email_border_radius ?>px !important; overflow: hidden; background-color: #ffffff; border: 1px solid <?php echo wc_hex_darker($back_bg_color, 10) ?>; width: <?php echo $email_width ?>px;}

.template_header { font-family: Arial, sans-serif; background-color:<?php echo esc_attr( $header_bg_color ) ?>; color: <?php echo $header_text_color ?>; border-top-left-radius:2px !important; border-top-right-radius:2px !important; border-bottom: 1px solid <?php echo $header_border_bottom_color ?>; font-family:Arial; font-weight:bold; vertical-align:middle; text-align: <?php echo $header_logo_alignment ?>; padding: <?php echo $email_padding_top_bottom/1.8 ?>px <?php echo $email_padding_top_bottom/1.3 ?>px ;}
.template_header a { color: <?php echo $header_text_color ?> !important; font-weight: normal; text-decoration: none; font-size: 13px; margin: 0 0 0 12px; }

.top_nav_holder { background: <?php echo $top_nav_bg_color ?>; border-bottom: 1px solid <?php echo $top_nav_border_color ?> !important; }

.top_nav { }
.top_nav tr td { height: 38px; font-size: 14px; }
.top_nav tr td.nav-text-block { padding: 8px 12px;  }
.top_nav tr td.nav-text-block-with-image { padding-left: 0px; }
.top_nav tr td.nav-image-block { padding: 8px 6px; }
.top_nav tr td.nav-spacer-block { padding: 8px 6px; }
.top_nav a { text-decoration: none; }

.bottom-nav {  }
.bottom-nav .top_nav { }
.bottom-nav .top_nav tr td { height: 18px; font-size: 11px; }
.bottom-nav .top_nav tr td.nav-text-block { padding: 2px 6px;  }
.bottom-nav .top_nav tr td.nav-text-block-with-image { padding-left: 0px; }
.bottom-nav .top_nav tr td.nav-image-block { padding: 2px 4px; }
.bottom-nav .top_nav tr td.nav-spacer-block { padding: 2px 0; display: none; font-size: 1px; width: 1px; }
.bottom-nav .top_nav a { text-decoration: none; }

.body_content { font-family: Arial, sans-serif; color: <?php echo $body_color ?>; background-color: <?php echo esc_attr( $body_bg_color ) ?>; }
.body_content_inner { font-family: Arial, sans-serif; font-family:Arial; text-align:left; padding-left: <?php echo $email_padding_left_right ?>px; padding-right: <?php echo $email_padding_left_right ?>px; padding-top: <?php echo $email_padding_top_bottom ?>px; padding-bottom: <?php echo $email_padding_top_bottom ?>px;}

.shortcode-error { color: #FFF; font-size: 12px; background-color: #545454; border-radius: 3px; padding: 2px 6px 1px; }

/* General Headings, Text, Links Styling ---------- */

/* a tags that are body colour with uderline set in the global styles */
.a_tag { color: <?php echo $body_text_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: <?php echo $body_a_decoration ?>; }
/* a tags that are body colour with no uderline forced on */
.a_tag_clean { color: <?php echo $body_text_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: none; }
/* a tags that are specific colour with uderline set in the global styles */
.a_tag_color { color: <?php echo $body_a_color ?>; text-decoration: <?php echo $body_a_decoration ?>; font-style: <?php echo $body_a_style ?>;}
/* a tags that are colour with no uderline forced on */
.a_tag_color_clean { color: <?php echo $body_a_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: none;}
.highlight { color: <?php echo $body_highlight_color ?>; text-decoration: <?php echo $body_highlight_decoration ?>; font-style: <?php echo $body_highlight_style ?>; }

/* Special Title Function ---------- */
.special-title-holder td { font-size: 1px; }
.special-title-holder .header_content_h2 { font-family: Arial,sans-serif; font-weight: <?php echo $body_h2_weight ?>; font-style: <?php echo $body_h2_style ?>; font-size: <?php echo $body_h2_size ?>px; color: <?php echo $body_h2_color ?>; text-decoration: <?php echo $body_h2_decoration ?>; text-transform: <?php echo $body_h2_transform ?>; margin: 0; padding: 0px 5px; white-space: nowrap;}
.special-title-holder .header_content_h2_border { border-top: <?php echo $body_h2_border_width ?>px solid <?php echo $body_h2_border_color ?>;}
.special-title-holder .header_content_h2_space_before { height: 6px; }
.special-title-holder .header_content_h2_space_after { height: 18px; }

/*table { border: 1px solid red; }*/

/* Order Table ---------- */
.order-table-heading {  }
.order-table-heading .highlight { color: <?php echo $body_highlight_color ?>; text-decoration: <?php echo $body_highlight_decoration ?>; font-style: <?php echo $body_highlight_style ?>; }
.order-table-heading a { color: <?php echo $body_text_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: none; }

/* General Columns (so that columns look nice with more width on left and right tan the gutter) ---------- */
.order_items_table_column_pading { padding-left: <?php echo $email_padding_left_right ?>px; padding-right: <?php echo $email_padding_left_right ?>px;}
.order_items_table_column_pading_first { padding-left: 0px; padding-right: <?php echo ($email_padding_left_right/2) ?>px;}
.order_items_table_column_pading_last { padding-left: <?php echo ($email_padding_left_right/2) ?>px; padding-right: 0px;}

/* Intro Content ---------- */
.top_content_container { padding: 22px 0 22px 0;}
.top_heading { font-family: Arial, sans-serif; font-size: <?php echo $heading_1_size ?>px; text-align: left; font-weight: bold;}
.top_paragraph { font-family: Arial, sans-serif; text-align: left; margin: 9px 0;}
h2 { font-family: Arial, sans-serif; font-size: <?php echo $heading_1_size ?>px; text-align: left; font-weight: bold; }

/* Payment Gateway Options ---------- */
.pay_link { font-size: <?php echo $body_important_a_size ?>px; font-weight: <?php echo $body_important_a_weight ?>; font-style: <?php echo $body_important_a_style ?>; color: <?php echo $body_important_a_color ?>; text-decoration: <?php echo $body_important_a_decoration ?>;}

/* Order Items Table ---------- */
.order_items_table { margin: 15px 0; overflow: hidden; width: 100%; background: <?php echo $order_items_table_bg_color ?>; }
.order_items_table_th_style { font-family: Arial, sans-serif; text-align: left; text-transform: uppercase; font-size: 10px; font-weight: normal; padding:0; margin:0; line-height: .8em; }
.order_items_table_th_bg_color { background-color: <?php echo $order_items_table_header_bg_color ?>;}
.order_items_table_td_style { font-family: Arial, sans-serif; text-align:left; vertical-align:middle; word-wrap:break-word; font-size: 14px; }
.order_items_table_totals_style { font-family: Arial, sans-serif; text-align: left; text-transform: uppercase; font-size: 14px; line-height: 1em; }

/* Product Items Table Border Radius */
<?php if ( isset($order_items_table_outer_border_radius) && $order_items_table_outer_border_radius > 0 ) { ?>
	.order_items_table { border-radius: <?php echo $order_items_table_outer_border_radius ?>px; }
<?php } ?>

/* Product Items Table td's whether it has Border or not and then handle the padding */
.order_items_table_td { padding:<?php echo $order_items_table_td_padding ?>px <?php echo ($order_items_table_td_padding*1.3) ?>px <?php echo ($order_items_table_td_padding-1) ?>px <?php echo ($order_items_table_td_padding*1.5) ?>px ; border-top:<?php echo $order_items_table_inner_border_width ?>px <?php echo $order_items_table_inner_border_style ?> <?php echo $order_items_table_inner_border_color ?>;}
.order_items_table_td_product { padding-top:<?php echo $order_items_table_td_padding * 1.9 ?>px; padding-bottom:<?php echo $order_items_table_td_padding * 1.9 ?>px; }

<?php
if ( $order_items_table_outer_border_style != 'none' && $order_items_table_outer_border_width != 0) {
	?>
	.order_items_table { border-bottom: <?php echo $order_items_table_outer_border_width ?>px <?php echo $order_items_table_outer_border_style ?> <?php echo $order_items_table_outer_border_color ?>; border-left: <?php echo $order_items_table_outer_border_width ?>px <?php echo $order_items_table_outer_border_style ?> <?php echo $order_items_table_outer_border_color ?>; border-right: <?php echo $order_items_table_outer_border_width ?>px <?php echo $order_items_table_outer_border_style ?> <?php echo $order_items_table_outer_border_color ?>; border-bottom: <?php echo $order_items_table_outer_border_width ?>px <?php echo $order_items_table_outer_border_style ?> <?php echo $order_items_table_outer_border_color ?>; }
	.order_items_table_td_top { border-top: <?php echo $order_items_table_outer_border_width ?>px <?php echo $order_items_table_outer_border_style ?> <?php echo $order_items_table_outer_border_color ?>; }
	<?php
}
else{
	?>
	.order_items_table_td_left { padding-left:0px; }
	.order_items_table_td_right { padding-right:0px; }
	.order_items_table_td_both { padding-right:0px; padding-left:0px; }
	.order_items_table { border-bottom: <?php echo $order_items_table_inner_border_width ?>px <?php echo $order_items_table_inner_border_style ?> <?php echo $order_items_table_inner_border_color ?>; }
	<?php
}
?>

/* Footer Styling ---------- */
.footer_container { font-family: Arial,sans-serif; font-size: 12px; text-align: center; padding: 12px <?php echo $email_padding_left_right / 2 ?>px 16px; border-top: 1px solid <?php echo $footer_border_bottom_color ?>; color: <?php echo $footer_text_color ?>; background-color: <?php echo $footer_bg_color ?>; }
.footer_container_inner { font-family: Arial,sans-serif; font-size: 12px; color: <?php echo $footer_text_color ?>; }
.footer_a_tag { color: <?php echo $footer_a_color ?>; text-decoration: <?php echo $footer_a_decoration ?>; }

/* Admin Styles ---------- */
.testing-block { padding:8px 10px; color: rgb(59, 59, 59); box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.07) inset; font-family: sans-serif; font-size:11px; margin: 0 auto 4px; text-shadow: 0 0px 3px rgba(255, 255, 255, 0.54); display: inline-block; }
.state-guide { font-size: 10px; color: #AEAEAE; margin: 0; padding: 6px 0; text-transform: uppercase; }

/* Custom CSS ---------- */
<?php echo wp_strip_all_tags( get_option( "ec_deluxe_all_custom_css" ) ); ?>

</style>
<?php
$ec_email_css = ob_get_clean();


/* EC Special Title
---------------------------------------- */
if (! function_exists( 'ec_special_title' )):
	function ec_special_title ($pass_heading_text, $args) {
		
		$defaults = array (
			'text_position'		=> 'left',	// text_position = center, left, right
			'border_position'	=> 'right',	// border_position = center, bottom, none
			'space_before'		=> '',		// 6px or 6 (px will get added automatically)
			'space_after'		=> '',		// 6px or (px will get added automatically)
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		if ( $args['space_before'] != '' ) {
			if ( !strpos($args['space_before'], "px") ) {
				$args['space_before'] = $args['space_before'] . "px";
			}
		}
		
		if ( $args['space_after'] != '' ) {
			if ( !strpos($args['space_after'], "px") ) {
				$args['space_after'] = $args['space_after'] . "px";
			}
		}
		
		// Parse incoming $args into an array and merge it with $defaults
		$args = wp_parse_args( $args, $defaults );
		
		$pass_heading_text = str_replace ( " " , "&nbsp;" , $pass_heading_text );
		?>
		<table class="special-title-holder" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="header_content_h2_space_before" style="<?php if ( $args['space_before'] ) echo 'height: ' . $args['space_before'] . ';' ; ?> font-size:0px; " >s</td>
						</tr>
					</table>
					<?php
					
					if ($args['border_position'] == "center" && $args['text_position'] == "center") {
						
						?>
						<!-- Heading with lines on either side -->
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50%">
									<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50%" style="height:50%;" >
											<td>&nbsp;</td>
										</tr>
										<tr height="50%" style="height:50%;" >
											<td class="header_content_h2_border"></td>
										</tr>
									</table>
								</td>
								<td width="1%" style="padding-right:6px; padding-left:6px; " class="header_content_h2" >
									<?php echo $pass_heading_text; ?>
								</td>
								<td width="50%">
									<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50%" style="height:50%;" >
											<td>&nbsp;</td>
										</tr>
										<tr height="50%" style="height:50%;" >
											<td class="header_content_h2_border"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
					
					}
					if ($args['border_position'] == "center" && $args['text_position'] == "left") {
						
						?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="1%" style="padding-right:6px;" class="header_content_h2" >
									<?php echo $pass_heading_text; ?>
								</td>
								<td width="99%">
									<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50%" style="height:50%;" >
											<td>&nbsp;</td>
										</tr>
										<tr height="50%" style="height:50%;" >
											<td class="header_content_h2_border"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php
						
					}
					if ($args['border_position'] == "center" && $args['text_position'] == "right") {
						
						?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="99%">
									<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50%" style="height:50%;" >
											<td>&nbsp;</td>
										</tr>
										<tr height="50%" style="height:50%;" >
											<td class="header_content_h2_border"></td>
										</tr>
									</table>
								</td>
								<td width="1%" style="padding-left:6px;" class="header_content_h2" >
									<?php echo $pass_heading_text; ?>
								</td>
							</tr>
						</table>
						<?php
						
					}
					
					if ($args['border_position'] == "bottom" || $args['border_position'] == "border-none") {
						
						?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="100%" style="text-align: <?php echo $args['text_position']; ?>;" class="header_content_h2" >
									<?php echo $pass_heading_text; ?>
								</td>
							</tr>
						</table>
						
						<?php if ($args['border_position'] == "bottom") { ?>
							<table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding-top:6px; padding-bottom:6px;">
								<tr>
									<td width="100%" class="header_content_h2_border" >
									</td>
								</tr>
							</table>
						<?php } ?>
						
						<?php
						
					}
					
					?>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="header_content_h2_space_after" <?php if ( $args['space_after'] ) echo 'style="height: ' . $args['space_after'] . ';"' ; ?> ></td>
						</tr>
					</table>
					
				</td>
			</tr>
		</table>
		<?php
	}
endif;




/* EC Special Title
---------------------------------------- */
if (! function_exists( 'ec_nav_bar' )):
	function ec_nav_bar () {
		
		$return = false;
		
		$link_text_1	= get_option('ec_deluxe_all_link_1_text');
		$link_image_1	= get_option('ec_deluxe_all_link_1_image');
		$link_url_1		= get_option('ec_deluxe_all_link_1_url');
		
		$link_text_2	= get_option('ec_deluxe_all_link_2_text');
		$link_image_2	= get_option('ec_deluxe_all_link_2_image');
		$link_url_2		= get_option('ec_deluxe_all_link_2_url');
		
		$link_text_3	= get_option('ec_deluxe_all_link_3_text');
		$link_image_3	= get_option('ec_deluxe_all_link_3_image');
		$link_url_3		= get_option('ec_deluxe_all_link_3_url');
		
		$link_text_4	= get_option('ec_deluxe_all_link_4_text');
		$link_image_4	= get_option('ec_deluxe_all_link_4_image');
		$link_url_4		= get_option('ec_deluxe_all_link_4_url');
		
		$link_text_5	= get_option('ec_deluxe_all_link_5_text');
		$link_image_5	= get_option('ec_deluxe_all_link_5_image');
		$link_url_5		= get_option('ec_deluxe_all_link_5_url');
		
		$link_text_6	= get_option('ec_deluxe_all_link_6_text');
		$link_image_6	= get_option('ec_deluxe_all_link_6_image');
		$link_url_6		= get_option('ec_deluxe_all_link_6_url');
		
		if 	( $link_text_1 || $link_image_1 || $link_text_2 || $link_image_2 || $link_text_3 || $link_image_3 || $link_text_4 || $link_image_4 || $link_text_5 || $link_image_5 || $link_text_6 || $link_image_6 ) {
		
			ob_start();
			?>
			<table border="0" cellpadding="0" cellspacing="0" width="auto" class="top_nav">
				<tr>
					<td class="nav-spacer-block">&nbsp;
						
					</td>
					
					<?php
					if ( $link_text_1 || $link_image_1 ) {
						?>
						<?php if ( $link_image_1 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_1 ) { ?><a href="<?php echo esc_url_raw($link_url_1);?>"><?php } ?>
									<img src="<?php echo get_option('ec_deluxe_all_link_1_image') ?>" />
								<?php if ( $link_url_1 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_1 ) { ?>
							<td class="nav-text-block <?php if ($link_image_1) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_1 ) { ?><a href="<?php echo esc_url_raw($link_url_1);?>"><?php } ?>
									<?php echo get_option('ec_deluxe_all_link_1_text') ?>
								<?php if ( $link_url_1 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_2 || $link_image_2 ) {
						?>
						<?php if ( $link_image_2 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_2 ) { ?><a href="<?php echo esc_url_raw($link_url_2);?>"><?php } ?>
									<img src="<?php echo get_option('ec_deluxe_all_link_2_image') ?>" />
								<?php if ( $link_url_2 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_2 ) { ?>
							<td class="nav-text-block <?php if ($link_image_2) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_2 ) { ?><a href="<?php echo esc_url_raw($link_url_2);?>"><?php } ?>
									<?php echo get_option('ec_deluxe_all_link_2_text') ?>
								<?php if ( $link_url_2 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_3 || $link_image_3 ) {
						?>
						<?php if ( $link_image_3 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_3 ) { ?><a href="<?php echo esc_url_raw($link_url_3);?>"><?php } ?>
									<img src="<?php echo get_option('ec_deluxe_all_link_3_image') ?>" />
								<?php if ( $link_url_3 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_3 ) { ?>
							<td class="nav-text-block <?php if ($link_image_3) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_3 ) { ?><a href="<?php echo esc_url_raw($link_url_3);?>"><?php } ?>
									<?php echo get_option('ec_deluxe_all_link_3_text') ?>
								<?php if ( $link_url_3 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_4 || $link_image_4 ) {
						?>
						<?php if ( $link_image_4 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_4 ) { ?><a href="<?php echo esc_url_raw($link_url_4);?>"><?php } ?>
									<img src="<?php echo get_option('ec_deluxe_all_link_4_image') ?>" />
								<?php if ( $link_url_4 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_4 ) { ?>
							<td class="nav-text-block <?php if ($link_image_4) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_4 ) { ?><a href="<?php echo esc_url_raw($link_url_4);?>"><?php } ?>
									<?php echo get_option('ec_deluxe_all_link_4_text') ?>
								<?php if ( $link_url_4 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_5 || $link_image_5 ) {
						?>
						<?php if ( $link_image_5 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_5 ) { ?><a href="<?php echo esc_url_raw($link_url_5);?>"><?php } ?>
									<img src="<?php echo get_option('ec_deluxe_all_link_5_image') ?>" />
								<?php if ( $link_url_5 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_5 ) { ?>
							<td class="nav-text-block <?php if ($link_image_5) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_5 ) { ?><a href="<?php echo esc_url_raw($link_url_5);?>"><?php } ?>
									<?php echo get_option('ec_deluxe_all_link_5_text') ?>
								<?php if ( $link_url_5 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_6 || $link_image_6 ) {
						?>
						<?php if ( $link_image_6 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_6 ) { ?><a href="<?php echo esc_url_raw($link_url_6);?>"><?php } ?>
									<img src="<?php echo get_option('ec_deluxe_all_link_6_image') ?>" />
								<?php if ( $link_url_6 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_6 ) { ?>
							<td class="nav-text-block <?php if ($link_image_6) { ?>nav-text-block-with-image<?php } ?>" >
								<?php if ( $link_url_6 ) { ?><a href="<?php echo esc_url_raw($link_url_6);?>"><?php } ?>
									<?php echo get_option('ec_deluxe_all_link_6_text') ?>
								<?php if ( $link_url_6 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<td class="nav-spacer-block">&nbsp;
						
					</td>
				</tr>
			</table>
			<?php
			$return = ob_get_clean();
		
		}
		
		return $return;
	}
endif;




ob_start();
?>
<!DOCTYPE html>
<html dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	</head>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		
		<table class="wrapper" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
			<tr>
				<td align="center" valign="top">
					
					
					<table class="main-body" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" valign="top">
								
								<!-- Header -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td class="template_header" >
											<a href="<?php echo get_site_url(); ?>" border="0">
												<?php
												if ( $header_img_src ) {
													?>
													<img src="<?php echo $header_img_src ?>" />
													<?php
												}
												else{
													?>
													<br />
													<br />
													<br />
													<?php
												}
												?>
											</a>
											
										</td>
									</tr>
								</table>
								<!-- End Header -->
								
							</td>
						</tr>
						
						
						<?php if ( ec_nav_bar() ) { ?>
							<tr>
								<td align="<?php echo $top_nav_position; ?>" valign="top" class="top_nav_holder">
									
									<?php echo ec_nav_bar(); ?>
								
								</td>
							</tr>
						<?php } ?>
						
						
						<tr>
							<td align="left" valign="top">
								
								
								<!-- Body -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
									<tr>
										<td valign="top" class="body_content">
											
											
											<!-- Content -->
											<table border="0" cellspacing="0" width="100%">
												<tr>
													<td valign="top" class="body_content_inner">