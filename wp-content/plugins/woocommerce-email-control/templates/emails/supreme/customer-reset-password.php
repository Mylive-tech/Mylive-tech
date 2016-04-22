<?php
/**
 * Customer Reset Password email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" class="top_content_container">
			
			<div class="top_heading">
				<?php echo get_option( "ec_supreme_customer_reset_password_heading" ); ?>
			</div>
			<div class="top_paragraph">
				<?php echo get_option( "ec_supreme_customer_reset_password_main_text" ); ?>
			</div>
			
		</td>
	</tr>
</table>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
