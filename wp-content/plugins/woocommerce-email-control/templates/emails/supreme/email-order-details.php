<?php
/**
 * Order details table shown in emails.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="top_content_container">
			
			<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

			<?php ec_special_title( __("Order Details", 'email-control'), array("border_position" => "center", "text_position" => "center", "space_after" => "3", "space_before" => "3" ) ); ?>

			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="order-table-heading" style="text-align:left; padding: 12px 0 6px;">
						<span class="highlight">
							<?php _e( 'Order Number:', 'email-control' ) ?>
						</span>
						<?php if ( ! $sent_to_admin ) : ?>
	                        <?php echo $order->get_order_number(); ?>
                        <?php else : ?>
	               			<a class="link" href="<?php echo esc_url( admin_url( 'post.php?post=' . $order->id . '&action=edit' ) ); ?>"><?php printf( __( 'Order #%s', 'woocommerce'), $order->get_order_number() ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ); ?>)</h2>
                        <?php endif; ?>
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
					<?php echo $order->email_order_items_table( array(
						'show_sku'      => $sent_to_admin,
						'show_image'    => false,
						'image_size'    => array( 32, 32 ),
						'plain_text'    => $plain_text,
						'sent_to_admin' => $sent_to_admin
					) ); ?>
				</tbody>
				<tfoot>
					<?php
						if ( $totals = $order->get_order_item_totals() ) {
							$i = 0;
							foreach ( $totals as $total ) {
								$i++;
								?>
								<tr class="order_items_table_total_row order_items_table_total_row_<?php echo esc_attr( sanitize_title( $total['label'] ) ) ?>">
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

			<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>
		
		</td>
	</tr>
</table>
