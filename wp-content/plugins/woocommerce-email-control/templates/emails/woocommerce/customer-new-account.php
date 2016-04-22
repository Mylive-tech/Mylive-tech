<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$email_heading = get_option( "ec_woocommerce_customer_new_account_heading" );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo get_option( "ec_woocommerce_customer_new_account_main_text" ); ?>
				
<?php if ( ( get_option( 'woocommerce_registration_generate_password' ) == 'yes' && $password_generated) || isset( $_REQUEST['ec_render_email'] ) ) : ?>
	
	<?php echo get_option( "ec_woocommerce_customer_new_account_main_text_generate_pass" ); ?>
	
<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
