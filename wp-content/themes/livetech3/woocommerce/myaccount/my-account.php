<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$woocommerce->show_messages(); 

$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
 
if ( $myaccount_page_id ) {
 
  $logout_url = wp_logout_url( get_permalink( $myaccount_page_id ) );
 
  if ( get_option( 'woocommerce_force_ssl_checkout' ) == 'yes' )
    $logout_url = str_replace( 'http:', 'https:', $logout_url );
}
?>

<!--<p class="myaccount_user">
	<?php
	printf(
		__( 'Hello, <strong>%s</strong>. From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="%s">change your password</a>.', 'woocommerce' ),
		$current_user->display_name,
		get_permalink( woocommerce_get_page_id( 'change_password' ) )
	);
	?>
</p>-->

<p class="myaccount_user">Hello <strong><?php echo $current_user->display_name; ?>,</strong></p>

<p class="myaccount_user">Welcome to your Live-Tech account dashboard. Here you can view your recent orders, subscriptions, downloads, manage your shipping & billing addresses as well as saved Credit Cards.</p>

<ul class="g1-grid" style="text-align:center;"><li class="g1-column g1-one-fifth g1-valign-top">
<style scoped="scoped" type="text/css">#g1-button-6.g1-button {background-color: #6800db; border-color: #6800db}</style><a title="Read Knowledge Base Articles" href="<?php echo get_permalink(woocommerce_get_page_id( 'myaccount' ) ); ?>?edit-account" class="g1-button g1-button--small g1-button--solid g1-button--wide" id="g1-button-6"><i class="icon-folder-open"></i>Change Password</a>
</li><!-- --><li class="g1-column g1-one-fifth g1-valign-top">
<style scoped="scoped" type="text/css">#g1-button-4.g1-button {background-color: #6bc700; border-color: #6bc700}</style><a title="Submit A Ticket" href="http://support.mylive-tech.com/index.php?_m=tickets&amp;_a=submit" class="g1-button g1-button--small g1-button--solid g1-button--wide " id="g1-button-4" target="_blank"><i class="icon-envelope-alt"></i>Submit A Ticket</a>
</li><!-- --><li class="g1-column g1-one-fifth g1-valign-top">
<style scoped="scoped" type="text/css">#g1-button-2.g1-button {background-color: #e85d00; border-color: #e85d00;}</style><a title="Live Chat With Agent"  onclick="window.open('http://support.mylive-tech.com/visitor/index.php?_m=livesupport&_a=startclientchat&sessionid=yrph78uldkjpq9mkvf3jwaeca5hghb5p&proactive=0&departmentid=0&randno=19&fullname=&email=', 'newwindow', 'width=500, height=350'); return false;" href="#" class="g1-button g1-button--small g1-button--solid g1-button--wide" id="g1-button-2" target="_blank"><i class="icon-user"></i>Live&nbsp;Chat</a>
</li><!-- --><li class="g1-column g1-one-fifth g1-valign-top">
<style scoped="scoped" type="text/css">#g1-button-5.g1-button {background-color: #666666; border-color: #666666;}</style><a title="Live-Tech Support Suite" href="<?php echo  $logout_url; ?>" class="g1-button g1-button--small g1-button--solid g1-button--wide" id="g1-button-5"><i class="icon-livetech-suite"></i>Log Out</a>
</li><!-- --></ul>

<div id="g1-tabs-1" class="g1-tabs g1-tabs--simple g1-type--click g1-tabs--horizontal g1-tabs--top g1-align-left "><div><ul class="g1-tabs-nav"><li class="g1-tabs-nav-item g1-tabs-nav-current-item" style="background:lightblue">
<div id="tab-title-counter-1" class="g1-tab-title "><i class="icon-th-list"></i> My Subscriptions</div></li><li class="g1-tabs-nav-item">
<div id="tab-title-counter-2" class="g1-tab-title "><i class="icon-shopping-cart"></i> My Orders</div></li><li class="g1-tabs-nav-item">
<div id="tab-title-counter-3" class="g1-tab-title "><i class="icon-download"></i> My Downloads</div></li><li class="g1-tabs-nav-item">
<div id="tab-title-counter-4" class="g1-tab-title "><i class="icon-align-justify"></i> My Addresses</div></li><li class="g1-tabs-nav-item">
<div id="tab-title-counter-5" class="g1-tab-title "><i class="icon-credit-card"></i> My Saved Cards</div></li></ul><div class="g1-tabs-viewport">
<div class="g1-tabs-viewport-item" style="display: block;"><div id="tab-content-counter-1" class="g1-tab-content ">
<?php do_action( 'woocommerce_before_my_account' ); ?>
</div></div><div class="g1-tabs-viewport-item" style="display: none;"><div id="tab-content-counter-2" class="g1-tab-content ">
<?php woocommerce_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
</div></div><div class="g1-tabs-viewport-item" style="display: none;"><div id="tab-content-counter-3" class="g1-tab-content ">
<?php woocommerce_get_template( 'myaccount/my-downloads.php' ); ?>
</div></div>
<div class="g1-tabs-viewport-item" style="display: none;"><div id="tab-content-counter-4" class="g1-tab-content ">
<?php woocommerce_get_template( 'myaccount/my-address.php' ); ?>
</div></div>
<div class="g1-tabs-viewport-item" style="display: none;"><div id="tab-content-counter-5" class="g1-tab-content ">
<?php do_action( 'woocommerce_after_my_account' ); ?>
<?php woocommerce_get_template( 'myaccount/firstdata-my-cards.php' ); ?>
</div></div>
</div>
</div></div>
