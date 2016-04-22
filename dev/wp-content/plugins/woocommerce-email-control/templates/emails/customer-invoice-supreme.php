<?php
/**
 * Customer Invoice
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

do_action('woocommerce_email_header', $email_heading);
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" class="top_content_container">

			<?php if ( $order->status === 'pending' || isset( $_REQUEST['ec_render_email'] ) ) { ?>

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

			<?php if ( $order->status !== 'pending' || isset( $_REQUEST['ec_render_email'] ) ) { ?>
				
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
			
			<div class="top_paragraph">
				<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>
			</div>

		</td>
	</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="top_content_container">
			
			<?php ec_special_title( __("Order Details", 'email-control'), array("border_position" => "center", "text_position" => "center", "space_after" => "3", "space_before" => "3" ) ); ?>
			
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="order-table-heading" style="text-align:left; padding: 12px 0 6px;">
						<span class="highlight">
							<?php _e( 'Order Number:', 'email-control' ) ?>
						</span> 
						<?php echo $order->get_order_number(); ?>
					</td>
					<td class="order-table-heading" style="text-align:right; padding: 12px 0 6px;">
						<span class="highlight">
							<?php _e( 'Order Date:', 'email-control' ) ?>
						</span> 
						<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ); ?>
					</td>
				</tr>
			</table>
			
			<table cellspacing="0" cellpadding="0" class="order_items_table" border="0" >
				<thead>
					<tr>
						<th scope="col" class="order_items_table_th_style order_items_table_td order_items_table_td_left order_items_table_th_bg_color order_items_table_td_top"><?php _e( 'Product', 'email-control' ); ?></th>
						<th scope="col" class="order_items_table_th_style order_items_table_td order_items_table_th_bg_color order_items_table_td_top"><?php _e( 'Quantity', 'email-control' ); ?></th>
						<th scope="col" class="order_items_table_th_style order_items_table_td order_items_table_td_right order_items_table_th_bg_color order_items_table_td_top" style="text-align:right"><?php _e( 'Price', 'email-control' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					switch ( $order->get_status() ) {
						case "completed" :
							echo $order->email_order_items_table( $order->is_download_permitted(), false, true );
						break;
						case "processing" :
							echo $order->email_order_items_table( $order->is_download_permitted(), true, true );
						break;
						default :
							echo $order->email_order_items_table( $order->is_download_permitted(), true, false );
						break;
					}
					?>
				</tbody>
				<tfoot>
					<?php
					if ( $totals = $order->get_order_item_totals() ) {
						$i = 0;
						foreach ( $totals as $total ) {
							$i++;
							?>
							<tr>
								<th scope="row" colspan="2" class="order_items_table_totals_style order_items_table_td order_items_table_td_left order_items_table_th_bg_color">
									<?php _e( $total['label'], 'email-control' ); ?>
								</th>
								<td class="order_items_table_totals_style order_items_table_td order_items_table_td_right order_items_table_th_bg_color" style="text-align:right;" >
									<?php _e( $total['value'], 'email-control' ); ?>
								</td>
							</tr>
							<?php
						}
					}
					?>
				</tfoot>
			</table>
			
			<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>
			
		</td>
	</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="top_content_container">

			<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text ); ?>
			
		</td>
	</tr>
</table>

<?php do_action( 'woocommerce_email_footer' ); ?>