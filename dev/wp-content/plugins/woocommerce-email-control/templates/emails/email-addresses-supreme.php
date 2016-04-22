<?php
/**
 * Email Addresses
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		
		<td width="50%" valign="top" class="order_items_table_column_pading_first">
			
			<p><strong><?php _e("Billing address", 'email-control') ?>:</strong></p>
			<p><?php echo $order->get_formatted_billing_address(); ?></p>
			
		</td>
		
		<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() ) ) : ?>
			<td width="50%" valign="top" class="order_items_table_column_pading_last">
				
				<p><strong><?php _e("Shipping address", 'email-control') ?>:</strong></p>
				<p><?php echo $order->get_formatted_shipping_address(); ?></p>
				
			</td>
		<?php endif; ?>
		
	</tr>
</table>