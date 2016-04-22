<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wc_print_notices(); ?>

<div class="lt-user-info">
<div class="lt-avatar"><?php
    global $current_user;
    $known = !empty($current_user->nickname);
?> 	
 <?php echo do_shortcode ( '[basic-user-avatars]') ?>
 </div>
<h4 class="myaccount_user">Hello <strong><?php echo $current_user->display_name; ?>,</strong></h4>
<p class="myaccount_user">Welcome to your Live-Tech account dashboard. Here you can view your recent orders, subscriptions, downloads, manage your shipping & billing addresses as well as saved Credit Cards.</p>
</div>


            
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
            
            <?php echo do_shortcode ( '[tabs slidertype="top tabs"] [tabcontainer] [tabtext][icon name="list" class=""] My Subscriptions[/tabtext] [tabtext][icon name="shopping-cart" class=""] My Orders[/tabtext] [tabtext][icon name="download" class=""] My Downloads[/tabtext] [tabtext][icon name="align-justify" class=""] My Addresses[/tabtext] [tabtext][icon name="credit-card" class=""] My Saved Cards[/tabtext] [/tabcontainer] [tabcontent] [tab]'. $mysubs .'[/tab] [tab]'. $ltorders .'[/tab] [tab] ' . $ltdownloads. '[/tab] [tab]'. $ltaddress.'[/tab] [tab]'.$ltafteracc.'[/tab] [/tabcontent] [/tabs]') ?>
			
            <h4> Need help? Call us toll free on 1 (888) 361-8511 </h4>