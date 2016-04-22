<?php
/**
 * Customer New Account
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

do_action( 'woocommerce_email_header', $email_heading );
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" class="top_content_container">
			
			<div class="top_heading">
				<?php echo get_option( "ec_supreme_customer_new_account_heading" ); ?>
			</div>
			<div class="top_paragraph">
				
				<?php echo get_option( "ec_supreme_customer_new_account_main_text" ); ?>
				
				<?php if ( (get_option( 'woocommerce_registration_generate_password' ) == 'yes' && $password_generated) || isset( $_REQUEST['ec_render_email'] ) ) : ?>
					
					<?php echo get_option( "ec_supreme_customer_new_account_main_text_generate_pass" ); ?>
					
					<?php if ( isset( $_REQUEST['ec_render_email'] ) ) { ?>
						<p class="state-guide">
							▲ <?php _e("If admin sets auto generated passwords",'email-control') ?>
						<p>
					<?php } ?>
						
				<?php endif; ?>
			</div>
			
		</td>
	</tr>
</table>

<?php do_action( 'woocommerce_email_footer' ); ?>