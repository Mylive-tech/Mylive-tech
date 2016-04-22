<?php
/**
 * Customer invoice email
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

			<?php if ( $order->has_status( 'pending' ) || isset( $_REQUEST['ec_render_email'] ) ) { ?>

				<div class="top_heading">
					<?php echo get_option( "ec_supreme_customer_invoice_heading_pending" ); ?>
				</div>
				<div class="top_paragraph">
					<?php echo get_option( "ec_supreme_customer_invoice_main_text_pending" ); ?>
				</div>
				
				<?php if ( isset( $_REQUEST['ec_render_email'] ) ) { ?>
					<p class="state-guide">
						▲ <?php _e("Payment Pending",'email-control') ?>
					<p>
				<?php } ?>
				
			<?php } ?>

			<?php if ( ! $order->has_status( 'pending' ) || isset( $_REQUEST['ec_render_email'] ) ) { ?>
				
				<div class="top_heading">
					<?php echo get_option( "ec_supreme_customer_invoice_heading_complete" ); ?>
				</div>
				<p class="top_paragraph">
					<?php echo get_option( "ec_supreme_customer_invoice_main_text_complete" ); ?>
				</p>
				
				<?php if ( isset( $_REQUEST['ec_render_email'] ) ) { ?>
					<p class="state-guide">
						▲ <?php _e("Payment Complete",'email-control') ?>
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
