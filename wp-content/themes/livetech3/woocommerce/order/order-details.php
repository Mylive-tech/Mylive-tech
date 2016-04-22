<?php
/**
 * Order details
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$order = new WC_Order( $order_id );
?>


<div id="g1-tabs-1" class="g1-tabs g1-tabs--simple g1-type--click g1-tabs--horizontal g1-tabs--top g1-align-left ">
<div><ul class="g1-tabs-nav"><li class="g1-tabs-nav-item g1-tabs-nav-current-item">
<div id="tab-title-counter-1" class="g1-tab-title "><i class="icon-th-list"></i> Order Details</div></li><li class="g1-tabs-nav-item">
<div id="tab-title-counter-2" class="g1-tab-title "><i class="icon-user"></i> Customer Details</div></li>
</ul>

<div class="g1-tabs-viewport"><div class="g1-tabs-viewport-item" style="display: block;"><div id="tab-content-counter-1" class="g1-tab-content ">
<!-- <h2><?php _e( 'Order Details', 'woocommerce' ); ?></h2>-->
<div class="g1-table g1-table--solid " id="g1-table-1">
<table class="shop_table order_details">
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tfoot>
	<?php
		if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
			?>
			<tr>
				<th scope="row"><?php echo $total['label']; ?></th>
				<td><?php echo $total['value']; ?></td>
			</tr>
			<?php
		endforeach;
	?>
	</tfoot>
	<tbody>
		<?php
		if ( sizeof( $order->get_items() ) > 0 ) {

			foreach( $order->get_items() as $item ) {
				$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
				$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );

				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
					<td class="product-name">
						<?php
							if ( $_product && ! $_product->is_visible() )
								echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
							else
								echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ), $item );

							echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );

							$item_meta->display();

							if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

								$download_files = $order->get_item_downloads( $item );
								$i              = 0;
								$links          = array();

								foreach ( $download_files as $download_id => $file ) {
									$i++;

									$links[] = '<small><a href="' . esc_url( $file['download_url'] ) . '">' . sprintf( __( 'Download file%s', 'woocommerce' ), ( count( $download_files ) > 1 ? ' ' . $i . ': ' : ': ' ) ) . esc_html( $file['name'] ) . '</a></small>';
								}

								echo '<br/>' . implode( '<br/>', $links );
							}
						?>
					</td>
					<td class="product-total">
						<?php echo $order->get_formatted_line_subtotal( $item ); ?>
					</td>
				</tr>
				<?php

				if ( in_array( $order->status, array( 'processing', 'completed' ) ) && ( $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) ) {
					?>
					<tr class="product-purchase-note">
						<td colspan="3"><?php echo apply_filters( 'the_content', $purchase_note ); ?></td>
					</tr>
					<?php
				}
			}
		}

		do_action( 'woocommerce_order_items_table', $order );
		?>
	</tbody>
</table>
</div>
</div></div> <!--end tab 1-->


<!-- Start tab 2-->
<div class="g1-tabs-viewport-item" style="display: none;"><div id="tab-content-counter-2" class="g1-tab-content "> 
<div class="customer_details">
<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

 <!--	<h3><?php _e( 'Customer details', 'woocommerce' ); ?></h3> -->

<div class="customer_details">
<?php
	if ( $order->billing_email ) echo '<p><strong>' . __( 'Email:', 'woocommerce' ) . '</strong>&nbsp;' . $order->billing_email . '</p>';
	if ( $order->billing_phone ) echo '<p><strong>' . __( 'Telephone:', 'woocommerce' ) . '</strong>&nbsp;' . $order->billing_phone . '</p>';

	// Additional customer details hook
	do_action( 'woocommerce_order_details_after_customer_details', $order );
?>
</div></div>





<?php if ( get_option( 'woocommerce_ship_to_billing_address_only' ) === 'no' && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

<div class="lt-myaccount-header">

<div class="g1-box__inner lt-myadds">



<?php endif; ?>

<div class="col2-set addresses">

<div class="col-1 address">

		<header class="title">
			<h3><i id="icon-1" class="icon-usd g1-icon g1-icon--solid g1-icon--small g1-icon--circle "></i><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>
		</header>
		<address>
			<?php
				if ( ! $order->get_formatted_billing_address() ) _e( 'Not Applicable', 'woocommerce' ); else echo $order->get_formatted_billing_address();
			?>
		</address>
		
		</div>

<?php if ( get_option( 'woocommerce_ship_to_billing_address_only' ) === 'no' && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

	

	<div class="col-2 address">

		<header class="title">
			<h3><i id="icon-1" class="icon-truck g1-icon g1-icon--solid g1-icon--small g1-icon--circle "></i><?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>
		</header>
		<address>
			<?php
				if ( ! $order->get_formatted_shipping_address() ) _e( 'Not Applicable', 'woocommerce' ); else echo $order->get_formatted_shipping_address();
			?>
		</address>

	</div><!-- /.col-2 -->
	
	</div>
	
</div>

</div>

</div></div>
</div> </div></div>


<?php endif; ?>

<div class="clear"></div>
