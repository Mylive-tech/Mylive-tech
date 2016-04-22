<?php
/**
 * Show messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="woocommerce-message"><i class="fa fa-star fa-spin fa-2x"></i><p><?php echo wp_kses_post( $message ); ?></p></div>
<?php endforeach; ?>


<style>.woocommerce-message p{
	
	margin-left:7%; margin-top:-3.2%;}
    
    </style>