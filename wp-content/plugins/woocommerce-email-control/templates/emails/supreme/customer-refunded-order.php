<?php
/**
 * Customer refunded order email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" class="top_content_container">
		
			<?php if ( $partial_refund || isset( $_REQUEST['ec_render_email'] ) ) { ?>

				<div class="top_heading">
					<?php echo get_option( "ec_supreme_customer_refunded_order_heading_partial" ); ?>
				</div>
				<div class="top_paragraph">
					<?php echo get_option( "ec_supreme_customer_refunded_order_main_text_partial" ); ?>
				</div>
				
				<?php if ( isset( $_REQUEST['ec_render_email'] ) ) { ?>
					<p class="state-guide">
						▲ <?php _e("Partial Refund",'email-control') ?>
					<p>
				<?php } ?>
				
			<?php } ?>
			
			<?php if ( !$partial_refund || isset( $_REQUEST['ec_render_email'] ) ) { ?>
				
				<div class="top_heading">
					<?php echo get_option( "ec_supreme_customer_refunded_order_heading_full" ); ?>
				</div>
				<p class="top_paragraph">
					<?php echo get_option( "ec_supreme_customer_refunded_order_main_text_full" ); ?>
				</p>
				
				<?php if ( isset( $_REQUEST['ec_render_email'] ) ) { ?>
					<p class="state-guide">
						▲ <?php _e("Refund",'email-control') ?>
					<p>
				<?php } ?>

			<?php } ?>

		</td>
	</tr>
</table>

<?php

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
