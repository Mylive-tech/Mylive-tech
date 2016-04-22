<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices(); ?>

<p class="myaccount_user">
	<?php
	printf(
		__( 'Hello <strong>%1$s</strong> (not %1$s? <a href="%2$s">Sign out</a>).', 'woocommerce' ) . ' ',
		$current_user->display_name,
		wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) )
	);

	printf( __( 'Welcome to your Live-Tech account dashboard. Here you can view your recent orders, subscriptions, downloads, manage your shipping & billing addresses as well as saved Credit Cards.', 'woocommerce' ),
		wc_customer_edit_account_url()
	);
	?>
</p>

            <?php ob_start();
do_action( 'woocommerce_before_my_account' );
$mysubs = ob_get_clean();
ob_start();
woocommerce_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) );
$ltorders = ob_get_clean();
ob_start();
woocommerce_get_template( 'myaccount/my-downloads.php' );
$ltdownloads = ob_get_clean();
ob_start();
woocommerce_get_template( 'myaccount/my-address.php' );
$ltaddress = ob_get_clean();
ob_start();
woocommerce_get_template( 'myaccount/firstdata-my-cards.php' );
$ltcards = ob_get_clean();
ob_start();
do_action( 'woocommerce_after_my_account' );
$ltafteracc = ob_get_clean();
?>
            
            <?php echo do_shortcode ( '[tabgroup] [tab title="My Subscriptions"] ' . $mysubs . ' [/tab] [tab title="My Orders"] ' . $ltorders . ' [/tab] [tab title="My Downloads"]' . $ltdownloads. ' [/tab] [tab title="My Addresses"] ' . $ltaddress . ' [/tab] [tab title="My Saved Cards"]  ' . $ltafteracc . ' [/tab] [/tabgroup]') ?>
			
            <h4> Need help? Call us toll free on 1 (888) 361-8511 </h4>
