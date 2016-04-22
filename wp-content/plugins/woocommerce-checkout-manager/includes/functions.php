<?php
function wooccm_error_log( $message = '' ) {

	if( $message == '' )
		return;

	if( class_exists( 'WC_Logger' ) ) {
		$logger = new WC_Logger();
		$logger->add( 'wooccm', $message );
		return true;
	} else {
		// Fallback where the WooCommerce logging engine is unavailable
		error_log( sprintf( '[checkout-manager] %s', $message ) );
	}

}
?>