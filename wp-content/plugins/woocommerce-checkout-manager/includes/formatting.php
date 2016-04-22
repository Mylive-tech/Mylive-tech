<?php
function wooccm_mul_array( $val, $array ) {

	if( !empty( $array ) ) {
		if( is_array( $array ) ) {
			foreach( $array as $item ) {
				if( isset( $item['cow'] ) && $item['cow'] == $val ) {
					return true;
				}
			}
		}
	}

}

function wooccm_mul_array2( $val ) {

	global $wpdb;

	foreach( $wpdb->last_result as $item => $tru ) {
		if( isset($tru->meta_key) && $tru->meta_key == $val ) {
			return true;
		}
	}
	return false;

}

function wooccm_get_value_by_key( $array, $key ) {

	if( !empty( $array ) ) {
		foreach( $array as $k => $each ){
			if( $k == $key ) {
				return $each;
			}
			if( is_array( $each ) ) {
				if( $return = wooccm_get_value_by_key( $each,$key ) ) {
					return $return;
				}
			}
		}
	}

}

function wooccm_does_existw( $array ) {

	if( empty( $array ) )
		return;

	if( !is_array( $array ) )
		return;

	foreach( $array as $sub ) {
		if( wooccm_mul_array2( wooccm_get_value_by_key( $sub, 'cow' ) ) ) {
			return true;
		}
	}

}

function wooccm_clean( $string ) {

	$trim_length = 200;  //desired length of text to display
	$string = str_replace('-', '', $string); // Replaces all spaces with hyphens.
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	//  $string = preg_replace('/\s+/', '', strip_tags($string)); // removes html and spaces
	//  $string = preg_replace('/\d/', '', $string); // Replaces multiple hyphens with single one.
	return rtrim( substr( $string, 0, $trim_length ) );

}

function wooccm_wpml_string( $input = '' ) {

	if( function_exists( 'icl_t' ) ) {
		return icl_t('WooCommerce Checkout Manager', $input, $input );
	} else {
		return $input;
	}

}
?>