<?php
function wooccm_front_endupload() {

	require_once( ABSPATH . 'wp-admin/includes/file.php' ); 
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$wp_upload_dir = wp_upload_dir();
	$name = ( isset( $_REQUEST["name"] ) ? $_REQUEST["name"] : false );
	$number_of_files = 0;

	// Check if a file has been uploaded
	if( empty( $_FILES ) ) {
		wooccm_error_log( '[' . $name . '] $_FILES is empty' );
		return;
	}

	// Check if the $_REQUEST name attribute matches the $_FILES field name
	if( !isset( $_FILES[$name] ) ) {
		wooccm_error_log( '[' . $name . '] $_REQUEST name does not match' );
		return;
	}

	$file = array(
		'name'     => $_FILES[''.$name.'']['name'],
		'type'     => $_FILES[''.$name.'']['type'],
		'tmp_name' => $_FILES[''.$name.'']['tmp_name'],
		'error'    => $_FILES[''.$name.'']['error'],
		'size'     => $_FILES[''.$name.'']['size']
	);

	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $file, $upload_overrides );

	// Check if upload was successful
	if( isset( $movefile['error'] ) && $movefile['error'][0] > 0 ) {
		wooccm_error_log( '[' . $name . '] upload failed: ' . print_r( $movefile, true ) );
		return;
	} else {
		$attachment = array(
			'guid' => ( isset( $movefile['url'] ) ? $movefile['url'] : false ),
			'post_mime_type' => ( isset( $movefile['type'] ) ? $movefile['type'] : $file['type'] ),
			'post_title' => preg_replace( '/\.[^.]+$/', '', ( isset( $movefile['file'] ) ? basename( $movefile['file'] ) : basename( $file['name'] ) ) ),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		if( !empty( $movefile['url'] ) ) {
			$attach_id = wp_insert_attachment( $attachment, $movefile['url'] );
			$number_of_files++;
			echo json_encode( $attach_id );
			// echo json_encode( array( $number_of_files, $attach_id ) );
		}
	}

	die();

}
add_action("wp_ajax_wooccm_front_endupload", "wooccm_front_endupload");
add_action("wp_ajax_nopriv_wooccm_front_endupload", "wooccm_front_endupload");

function wooccm_front_enduploadsave() {

	global $wpdb, $woocommerce, $post; 

	require_once( ABSPATH . 'wp-admin/includes/file.php' ); 
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$name = ( isset( $_REQUEST["name"] ) ? $_REQUEST["name"] : false );
	$attachtoremove = ( isset( $_REQUEST["remove"] ) ? $_REQUEST["remove"] : false );

	wp_delete_attachment( $attachtoremove );

	$file = array(
		'name'     => $_FILES[''.$name.'']['name'],
		'type'     => $_FILES[''.$name.'']['type'],
		'tmp_name' => $_FILES[''.$name.'']['tmp_name'],
		'error'    => $_FILES[''.$name.'']['error'],
		'size'     => $_FILES[''.$name.'']['size']
	);

	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $file, $upload_overrides );

	$attachment = array(
		'guid' => $movefile['url'], 
		'post_mime_type' => $movefile['type'],
		'post_title' => preg_replace( '/\.[^.]+$/', '', basename($movefile['file'])),
		'post_content' => '',
		'post_status' => 'inherit'
	);

	$attach_id = wp_insert_attachment( $attachment, $movefile['url'] );

	echo json_encode( $attach_id );

	die();

}
//frontend handle
add_action("wp_ajax_wooccm_front_enduploadsave", "wooccm_front_enduploadsave");
add_action("wp_ajax_nopriv_wooccm_front_enduploadsave", "wooccm_front_enduploadsave");

function wooccm_update_attachment_ids( $order_id = 0 ) {

	$shipping = array(
		'country',
		'first_name', 
		'last_name', 
		'company', 
		'address_1', 
		'address_2', 
		'city', 
		'state', 
		'postcode'
	);
	$billing = array(
		'country', 
		'first_name', 
		'last_name', 
		'company', 
		'address_1', 
		'address_2',
		'city',
		'state',
		'postcode',
		'email',
		'phone'
	);

	$names = array(
		'billing',
		'shipping'
	);
	$inc = 3;

	foreach( $names as $name ) {

		$array = ($name == 'billing') ? $billing : $shipping;

		$options = get_option( 'wccs_settings'.$inc.'' );
		if( !empty( $options[''.$name.'_buttons'] ) ) {
			foreach( $options[''.$name.'_buttons'] as $btn ) {

				if( !in_array( $btn['cow'], $array ) ) {
					if( $btn['type'] == 'wooccmupload' ) {
						$value = get_post_meta( $order_id , '_'.$name.'_'.$btn['cow'], true );
						if( !empty( $value ) ) {
							$attachments = explode( "||", $value );
							if( !empty( $attachments ) ) {
								foreach( $attachments as $image_id ) {

									if( !empty( $image_id ) ){
										wp_update_post( array( 'ID' => $image_id,  'post_parent' => $order_id ));
										require_once( ABSPATH . 'wp-admin/includes/image.php' );
										wp_update_attachment_metadata( $image_id, wp_generate_attachment_metadata( $image_id, get_attached_file( $image_id ) ) );
									}

								}
							}
						}
					}
				}

			}
		}
		$inc--;
	}

	$options = get_option( 'wccs_settings' );
	if( !empty( $options['buttons'] ) ) {
		foreach( $options['buttons'] as $btn ) {

			if( $btn['type'] == 'wooccmupload' ) {
				$info = explode( "||", get_post_meta( $order_id , ''.$btn['cow'].'', true));
				if( $info ) {
					$new_info = explode( "||", $info[1] );
					if( !empty( $new_info ) ) {
						foreach( $new_info as $image_id ) {

							if( !empty( $image_id ) ) {
								wp_update_post( array( 'ID' => $image_id,  'post_parent' => $order_id ));
								require_once( ABSPATH . 'wp-admin/includes/image.php' );
								wp_update_attachment_metadata( $image_id, wp_generate_attachment_metadata( $image_id, get_attached_file( $image_id ) ) );
							}

						}
					}
				}
			}

		}
	}

}
add_action( 'woocommerce_thankyou', 'wooccm_update_attachment_ids' );
// @mod - Change to thank you page to catch all Order Status
add_action( 'woocommerce_order_status_completed', 'wooccm_update_attachment_ids' );

// Checkout - Order Received
function wooccm_custom_checkout_details( $order ) {

	$shipping = array(
		'country', 
		'first_name', 
		'last_name', 
		'company', 
		'address_1', 
		'address_2', 
		'city', 
		'state', 
		'postcode'
	);
	$billing = array(
		'country', 
		'first_name', 
		'last_name', 
		'company', 
		'address_1', 
		'address_2', 
		'city', 
		'state', 
		'postcode', 
		'email', 
		'phone'
	);
	$names = array(
		'billing',
		'shipping'
	);
	$inc = 3;

	if( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) {

		foreach( $names as $name ) {

			$array = ($name == 'billing') ? $billing : $shipping;

			$options = get_option( 'wccs_settings'.$inc.'' );
			if( !empty( $options[''.$name.'_buttons'] ) ) {
				foreach( $options[''.$name.'_buttons'] as $btn ) {

					if( !in_array( $btn['cow'], $array ) ) {
						if( (''.get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'], true).'' !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multiselect') && ($btn['type'] !== 'multicheckbox') ) {
							echo '
<tr>
	<th>'.wooccm_wpml_string($btn['label']).':</th>
	<td>'.nl2br(get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true)).'</td>
</tr>';
						} elseif ( !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] == 'heading') && ($btn['type'] !== 'multiselect') && ($btn['type'] !== 'multicheckbox') ) {
							echo '
<tr>
	<th colspan="2">' .wooccm_wpml_string($btn['label']). '</th>
</tr>';
						} elseif ( ( get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true) !== '') && $btn['type'] !== 'wooccmupload' && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) ) {
							$value = get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true );
							$strings = maybe_unserialize( $value );
							echo '
<tr>
	<th>'.wooccm_wpml_string($btn['label']).':</th>
	<td data-title="' .wooccm_wpml_string($btn['label']). '">';
							if( !empty( $strings ) ) {
								if( is_array( $strings ) ) {
									foreach( $strings as $key ) {
										echo wooccm_wpml_string( $key ) . ', ';
									}
								} else {
									echo $strings;
								}
							} else {
								echo '-';
							}
									echo '
	</td>
</tr>';
						} elseif( $btn['type'] == 'wooccmupload' ) {
							$info = explode("||", get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true));
							$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
							echo '
<tr>
	<th>'.wooccm_wpml_string( trim( $btn['label'] ) ).':</th>
	<td>'.$info[0].'</td>
</tr>';
						}
					}

				}
			}
			$inc--;

		}

		$options = get_option( 'wccs_settings' );
		if( !empty( $options['buttons'] ) ) {
			foreach( $options['buttons'] as $btn ) {

				if( (''.get_post_meta( $order->id , ''.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multiselect') && ($btn['type'] !== 'multicheckbox') ) {
					echo '
<tr>
	<th>'.wooccm_wpml_string($btn['label']).':</th>
	<td data-title="' .wooccm_wpml_string($btn['label']). '">'.nl2br(get_post_meta( $order->id , ''.$btn['cow'].'', true)).'</td>
</tr>';
				} elseif( !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] == 'heading') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multiselect') && ($btn['type'] !== 'multicheckbox') ) {
					echo '
<tr>
	<th colspan="2">' .wooccm_wpml_string($btn['label']). '</th>
</tr>';
				} elseif( (''.get_post_meta( $order->id , ''.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) ) {
					$value = get_post_meta( $order->id , $btn['cow'], true );
					$strings = maybe_unserialize( $value );
					echo '
<tr>
	<th>'.wooccm_wpml_string($btn['label']).':</th>
	<td data-title="' .wooccm_wpml_string($btn['label']). '">';
					if( !empty( $strings ) ) {
						if( is_array( $strings ) ) {
							foreach( $strings as $key ) {
								echo wooccm_wpml_string($key) . ', ';
							}
						} else {
							echo $strings;
						}
					} else {
						echo '-';
					}
					echo '
	</td>
</tr>';
				} elseif( $btn['type'] == 'wooccmupload' ) {
					$info = explode("||", get_post_meta( $order->id , $btn['cow'], true));
					$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
					echo '
<tr>
	<th>'.wooccm_wpml_string( trim( $btn['label'] ) ).':</th>
	<td data-title="' .wooccm_wpml_string( trim( $btn['label'] ) ). '">'.$info[0].'</td>
</tr>';
				}

			}
		}

	} else {

		foreach( $names as $name ) {

			$array = ($name == 'billing') ? $billing : $shipping;

			$options = get_option( 'wccs_settings'.$inc.'' );
			if( !empty( $options[''.$name.'_buttons'] ) ) {
				foreach( $options[''.$name.'_buttons'] as $btn ) {

					if( !in_array( $btn['cow'], $array ) ) {
						if( (''.get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && ($btn['type'] !== 'multiselect') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multicheckbox') ) {
							echo '
<dt>'.wooccm_wpml_string($btn['label']).':</dt>
<dd>'.nl2br(get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true)).'</dd>';
						} elseif( !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] == 'heading') && ($btn['type'] !== 'multiselect') && ($btn['type'] !== 'multicheckbox') ) {
							echo '
<h2>' .wooccm_wpml_string($btn['label']). '</h2>';
						} elseif( (''.get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'], true ) !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) ) {
							$value = get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'], true );
							$strings = maybe_unserialize( $value );
							echo '
<dt>'.wooccm_wpml_string($btn['label']).':</dt>
<dd>';
							if( !empty( $strings ) ) {
								if( is_array( $strings ) ) {
									foreach( $strings as $key ) {
										echo wooccm_wpml_string($key).', ';
									}
								} else {
									echo $strings;
								}
							} else {
								echo '-';
							}
							echo '
</dd>';
						} elseif( $btn['type'] == 'wooccmupload' ) {
							$info = explode("||", get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true));
							$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
							echo '
<dt>'.wooccm_wpml_string( trim( $btn['label'] ) ).':</dt>
<dd>'.$info[0].'</dd>';
						}
					}

				}
			}
			$inc--;

		}

		$options = get_option( 'wccs_settings' );
		if( !empty( $options['buttons'] ) ) {
			foreach( $options['buttons'] as $btn ) {

				if( (''.get_post_meta( $order->id , ''.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && ($btn['type'] !== 'wooccmupload' && $btn['type'] !== 'multiselect') && ($btn['type'] !== 'multicheckbox') ) {
					echo '
<dt>'.wooccm_wpml_string($btn['label']).':</dt>
<dd>'.nl2br(get_post_meta( $order->id , ''.$btn['cow'].'', true)).'</dd>';
				} elseif( !empty( $btn['label'] ) && $btn['type'] !== 'wooccmupload' && empty( $btn['deny_receipt'] ) && ($btn['type'] == 'heading') && ($btn['type'] !== 'multiselect') && ($btn['type'] !== 'multicheckbox') ) {
					echo '
<h2>' .wooccm_wpml_string($btn['label']). '</h2>';
				} elseif( (''.get_post_meta( $order->id , ''.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) ) {
					$value = get_post_meta( $order->id , $btn['cow'], true );
					$strings = maybe_unserialize( $value );
					echo '
<dt>'.wooccm_wpml_string($btn['label']).':</dt>
<dd>';
					if( !empty( $strings ) ) {
						if( is_array( $strings ) ) {
							foreach( $strings as $key ) {
								echo wooccm_wpml_string($key).', ';
							}
						} else {
							echo $strings;
						}
					} else {
						echo '-';
					}
					echo '
</dd>';
				} elseif( $btn['type'] == 'wooccmupload' ) {
					$info = explode("||", get_post_meta( $order->id , ''.$btn['cow'].'', true));
					$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
					echo '
<dt>'.wooccm_wpml_string( trim( $btn['label'] ) ).':</dt>
<dd>'.$info[0].'</dd>';
				}

			}
		}

	}

}
?>