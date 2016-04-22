<?php
function wooccm_custom_checkout_process() {

	global $woocommerce;

	$options = get_option( 'wccs_settings' );

	if( count( $options['buttons'] ) == 0 )
		return;

	foreach( $options['buttons'] as $btn ) {

		foreach( $woocommerce->cart->cart_contents as $key => $values ) {
        
			$multiproductsx = $btn['single_p'];
			$show_field_single = $btn['single_px'];
			$multiproductsx_cat = $btn['single_p_cat'];
			$show_field_single_cat = $btn['single_px_cat'];

			$productsarraycm[] = $values['product_id'];

			// Products 
			// hide field

			// show field without more
			if( !empty($btn['single_px']) && empty($btn['more_content']) ) {
				$show_field_array = explode('||',$show_field_single);

				if( in_array($values['product_id'], $show_field_array) && ( count($woocommerce->cart->cart_contents) < 2) ) {
					if( !empty ($btn['checkbox']) && !empty( $btn['label'] ) && ($btn['type'] !== 'changename')  ) {
						if( empty( $_POST[''.$btn['cow'].''] ) ) {
							$message = sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), '<strong>'.wooccm_wpml_string($btn['label']).'</strong>' );
							wc_add_notice( $message, 'error' );
						}
					}
				}
			}

			// Category
			// hide field
			$terms = get_the_terms( $values['product_id'], 'product_cat' );
			if( !empty( $terms ) ) {
				foreach( $terms as $term ) {

					$categoryarraycm[] = $term->slug;

					// without more

					// show field without more
					if( !empty($btn['single_px_cat']) && empty($btn['more_content']) ) {
						$show_field_array_cat = explode('||',$show_field_single_cat);

						if( in_array($term->slug, $show_field_array_cat)  && ( count($woocommerce->cart->cart_contents) < 2 ) ) {
							if( !empty ($btn['checkbox']) && !empty( $btn['label'] ) && ($btn['type'] !== 'changename')  ) {
								if( empty( $_POST[''.$btn['cow'].''] ) ) {
									$message = sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), '<strong>'.wooccm_wpml_string($btn['label']).'</strong>' );
									wc_add_notice( $message, 'error' );
								}
							}
						}
					}

				}
			}

		}
		// end cart

// ===========================================================================================

		// Products
		// hide field

		// show field with more
		if( !empty($btn['single_px']) && !empty($btn['more_content']) ) {
			$show_field_array = explode('||',$show_field_single);
			if(array_intersect($productsarraycm, $show_field_array) ){
				if( !empty ($btn['checkbox']) && !empty( $btn['label'] ) && ($btn['type'] !== 'changename')  ) {
					if( empty( $_POST[''.$btn['cow'].''] ) ) {
						$message = sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), '<strong>'.wooccm_wpml_string($btn['label']).'</strong>' );
						wc_add_notice( $message, 'error' );
					}
				}
			}
		}

		// Category
		// hide field

		// with more

		// show field with more
		if ( !empty($btn['single_px_cat']) && !empty($btn['more_content']) ) {
			$show_field_array_cat = explode('||',$show_field_single_cat);
			if(array_intersect($categoryarraycm, $show_field_array_cat)  ){
				if ( !empty ($btn['checkbox']) && !empty( $btn['label'] ) && ($btn['type'] !== 'changename')  ) {
					if( empty( $_POST[''.$btn['cow'].''] ) ) {
						$message = sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), '<strong>'.wooccm_wpml_string($btn['label']).'</strong>' );
						wc_add_notice( $message, 'error' );
					}
				}
			}
		}

		$categoryarraycm = '';
		$productsarraycm = '';

	}

}
?>