<?php
/**
 * Customer Reset Password email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$email_heading = get_option( "ec_woocommerce_customer_reset_password_heading" );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo get_option( "ec_woocommerce_customer_reset_password_main_text" ); ?>
<p></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
