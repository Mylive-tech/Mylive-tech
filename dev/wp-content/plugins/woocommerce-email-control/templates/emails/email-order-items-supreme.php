<?php
/**
 * Email Order Items
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if ( version_compare( $woocommerce->version, '2.4', '>=' ) ) {
	
	// WC2.4 and above
	
	foreach ( $items as $item_id => $item ) :
		
		$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
		$item_meta    = new WC_Order_Item_Meta( $item, $_product );
		?>
		<tr>
			<td class="order_items_table_td_style order_items_table_td order_items_table_td_left order_items_table_td_product">
				
				<?php
				
				// Show title/image etc
				if ( $show_image ) {
					echo apply_filters( 'woocommerce_order_item_thumbnail', '<span style="margin-bottom: 5px"><img src="' . ( $_product->get_image_id() ? current( wp_get_attachment_image_src( $_product->get_image_id(), 'thumbnail') ) : wc_placeholder_img_src() ) .'" alt="' . esc_attr__( 'Product Image', 'email-control' ) . '" height="' . esc_attr( $image_size[1] ) . '" width="' . esc_attr( $image_size[0] ) . '" style="vertical-align:middle; margin-right: 10px;" /></span>', $item );
				}

				// Product name
				echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item, false );

				// SKU
				if ( $show_sku && is_object( $_product ) && $_product->get_sku() ) {
					echo ' (#' . $_product->get_sku() . ')';
				}
				
				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );
				
				// Variation
				if ( ! empty( $item_meta->meta ) ) {
					echo '<br/><small>' . nl2br( $item_meta->display( true, true, '_', "\n" ) ) . '</small>';
				}

				// File URLs
				if ( $show_download_links && is_object( $_product ) && $_product->exists() && $_product->is_downloadable() ) {

					$download_files = $order->get_item_downloads( $item );
					$i              = 0;

					foreach ( $download_files as $download_id => $file ) {
						$i++;

						if ( count( $download_files ) > 1 ) {
							$prefix = sprintf( __( 'Download %d', 'email-control' ), $i );
						} elseif ( $i == 1 ) {
							$prefix = __( 'Download', 'email-control' );
						}

						echo '<br/><small class="order_item_download">' . $prefix . ': <a href="' . esc_url( $file['download_url'] ) . '" target="_blank">' . esc_html( $file['name'] ) . '</a></small>' . "\n";
					}
				}
				
				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
				?>
				
			</td>
			<td class="order_items_table_td_style order_items_table_td order_items_table_td order_items_table_td_product">
				<?php echo $item['qty'] ;?>
			</td>
			<td class="order_items_table_td_style order_items_table_td order_items_table_td_right order_items_table_td_product" style="text-align:right">
				<?php echo $order->get_formatted_line_subtotal( $item ); ?>
			</td>
		</tr>

		<?php if ( $show_purchase_note && is_object( $_product ) && $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) : ?>
			<tr>
				<td colspan="3" class="order_items_table_td_style order_items_table_td order_items_table_td_both order_items_table_td_product">
					<?php echo apply_filters( 'the_content', $purchase_note ); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php
	endforeach;

}
elseif ( version_compare( $woocommerce->version, '2.2', '>=' ) ) {
	
	// WC2.2 and above
	
	foreach ( $items as $item ) :
		$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
		$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );
		?>
		<tr>
			<td class="order_items_table_td_style order_items_table_td order_items_table_td_left order_items_table_td_product">
				<?php
				// Show title/image etc
				if ( $show_image ) {
					echo apply_filters( 'woocommerce_order_item_thumbnail', '<span style="margin-bottom: 5px"><img src="' . ( $_product->get_image_id() ? current( wp_get_attachment_image_src( $_product->get_image_id(), 'thumbnail') ) : wc_placeholder_img_src() ) .'" alt="' . __( 'Product Image', 'email-control' ) . '" height="' . esc_attr( $image_size[1] ) . '" width="' . esc_attr( $image_size[0] ) . '" style="vertical-align:middle; margin-right: 10px;" /></span>', $item );
				}

				// Product name
				echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );

				// SKU
				if ( $show_sku && is_object( $_product ) && $_product->get_sku() ) {
					echo ' (#' . $_product->get_sku() . ')';
				}

				// File URLs
				if ( $show_download_links && is_object( $_product ) && $_product->exists() && $_product->is_downloadable() ) {

					$download_files = $order->get_item_downloads( $item );
					$i              = 0;

					foreach ( $download_files as $download_id => $file ) {
						$i++;

						if ( count( $download_files ) > 1 ) {
							$prefix = sprintf( __( 'Download %d', 'email-control' ), $i );
						} elseif ( $i == 1 ) {
							$prefix = __( 'Download', 'email-control' );
						}

						echo '<br/><small class="order_item_download">' . $prefix . ': <a href="' . esc_url_raw( $file['download_url'] ) . '" target="_blank">' . esc_html( $file['name'] ) . '</a></small>';
					}
				}

				// Variation
				if ( $item_meta->meta ) {
					echo '<br/><small>' . nl2br( $item_meta->display( true, true ) ) . '</small>';
				}
				?>
				
			</td>
			<td class="order_items_table_td_style order_items_table_td order_items_table_td order_items_table_td_product">
				<?php echo $item['qty'] ;?>
			</td>
			<td class="order_items_table_td_style order_items_table_td order_items_table_td_right order_items_table_td_product" style="text-align:right">
				<?php echo $order->get_formatted_line_subtotal( $item ); ?>
			</td>
		</tr>

		<?php if ( $show_purchase_note && is_object( $_product ) && $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) : ?>
			<tr>
				<td colspan="3" class="order_items_table_td_style order_items_table_td order_items_table_td_both order_items_table_td_product">
					<?php echo apply_filters( 'the_content', $purchase_note ); ?>
				</td>
			</tr>
		<?php endif; ?>

	<?php
	endforeach;
}
?>