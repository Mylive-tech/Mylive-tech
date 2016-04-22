<?php
/**
 * Email Addresses
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?><table cellspacing="0" cellpadding="10" style="width: 100%; font-size:14px; vertical-align: top;" border="1" bordercolor="#eeeeee" bgcolor="#f8f8f8">

	<tr>

		<td valign="top" width="50%">

			<h3 style="font-size:16px;font-weight:bold;color:#737373"><?php _e( 'Billing address', 'woocommerce' ); ?></h3>

			<p><?php echo $order->get_formatted_billing_address(); ?></p>

		</td>

		<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() ) ) : ?>

		<td valign="top" width="50%">

			<h3 style="font-size:16px;font-weight:bold;color:#737373"><?php _e( 'Shipping address', 'woocommerce' ); ?></h3>

			<p><?php echo $shipping; ?></p>

		</td>

		<?php endif; ?>

	</tr>

</table>
