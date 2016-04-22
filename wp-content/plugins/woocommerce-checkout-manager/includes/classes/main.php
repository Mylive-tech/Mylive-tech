<?php 
/**
 * WooCommerce Checkout Manager
 *
 * MAIN
 *
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wooccm_add_payment_method_to_new_order( $order, $sent_to_admin, $plain_text = '' ) {

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

	$names = array( 'billing', 'shipping' );
	$inc = 3;

 	if( $plain_text ) {

		foreach( $names as $name ) {

			$array = ($name == 'billing') ? $billing : $shipping;

			$options = get_option( 'wccs_settings'.$inc.'' );
			if( !empty( $options[''.$name.'_buttons'] ) ) {
				foreach( $options[''.$name.'_buttons'] as $btn ) {

					if ( !in_array( $btn['cow'], $array ) ) {
						if (  (''.get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && ($btn['type'] !== 'multiselect') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multicheckbox') ) {
							echo ''.wooccm_wpml_string($btn['label']).': '.nl2br(get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true)).'';
							echo "\n";
						} elseif ( !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] == 'heading') && ($btn['type'] !== 'multiselect') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multicheckbox') ) {
							echo '' .wooccm_wpml_string($btn['label']). '';
							echo "\n";
						} elseif (  (''.get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'], true).'' !== '') && !empty( $btn['label'] ) && empty( $btn['deny_receipt'] ) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) ) {
							$value = get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'], true );
							$strings = maybe_unserialize( $value );
							echo wooccm_wpml_string($btn['label']).': ';
							if( !empty( $strings ) ) {
								if( is_array( $strings ) ) {
									$iww = 0;
									$len = count( $strings );
									foreach( $strings as $key ) {
										if( $iww == $len - 1 ) {
											echo $key;
										} else {
											echo $key.', ';
										}
										$iww++;
									}
								} else {
									echo $strings;
								}
							} else {
								echo '-';
							}
							echo "\n";
						} elseif( $btn['type'] == 'wooccmupload' ) {
							$info = explode( "||", get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true));
							$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
							echo ''.wooccm_wpml_string( trim( $btn['label'] ) ).': '.$info[0].'';
							echo "\n";
						}
					}

				}
			}
			$inc--;

		}

		$options = get_option( 'wccs_settings' );
		if( !empty( $options['buttons'] ) ) {
			foreach( $options['buttons'] as $btn ) {

				if ( (''.get_post_meta( $order->id , ''.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty($btn['deny_receipt']) && ($btn['type'] !== 'heading') && ($btn['type'] !== 'multiselect') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multicheckbox')  ) {
					echo ''.wooccm_wpml_string($btn['label']).': '.nl2br(get_post_meta( $order->id , ''.$btn['cow'].'', true)).'';
					echo "\n";
				} elseif ( !empty( $btn['label'] ) && empty($btn['deny_receipt']) && ($btn['type'] == 'heading') && ($btn['type'] !== 'multiselect') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multicheckbox') ) {
					echo ''.wooccm_wpml_string($btn['label']).'';
					echo "\n";
				} elseif ( (''.get_post_meta( $order->id , ''.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty($btn['deny_receipt']) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) ) {
					$value = get_post_meta( $order->id , $btn['cow'], true );
					$strings = maybe_unserialize( $value );
					echo ''.wooccm_wpml_string($btn['label']).': ';
					if( !empty( $strings ) ) {
						if( is_array( $strings ) ) {
							$iww = 0;
							$len = count($strings);
							foreach($strings as $key ) {
								if ($iww == $len - 1) {
									echo ''.$key.'';
								} else {
									echo ''.$key.', ';
								}
								$iww++;
							}
						} else {
							echo $strings;
						}
					} else {
						echo '-';
					}
					echo "\n";
				} elseif( $btn['type'] == 'wooccmupload' ) {
					$info = explode("||", get_post_meta( $order->id , ''.$btn['cow'].'', true));
					$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
					echo ''.wooccm_wpml_string( trim( $btn['label'] ) ).': '.$info[0].'';
					echo "\n";
				}

			}
		}

		if ( !empty($options['checkness']['set_timezone']) ) {
			date_default_timezone_set(''.$options['checkness']['set_timezone'].'');
		}
		$date = ( !empty($options['checkness']['twenty_hour'])) ? date("G:i T (P").' GMT)' : date("g:i a");
		$options['checkness']['time_stamp'] = ( isset( $options['checkness']['time_stamp'] ) ? $options['checkness']['time_stamp'] : false );
		if ( $options['checkness']['time_stamp'] == true ) {
			echo ''.$options['checkness']['time_stamp_title'].' ' . $date . "\n";
		}
		if ( $order->payment_method_title && $options['checkness']['payment_method_t'] == true ) {
			echo ''.$options['checkness']['payment_method_d'].': ' . $order->payment_method_title . "\n";
		}
		if ( $order->shipping_method_title && ($options['checkness']['shipping_method_t'] == true)) {
			echo ''.$options['checkness']['shipping_method_d'].': ' . $order->shipping_method_title . "\n";
		}

		echo "\n";

	} else {

		foreach( $names as $name ) {

			$array = ($name == 'billing') ? $billing : $shipping;

			$options = get_option( 'wccs_settings'.$inc.'' );
			if( !empty( $options[''.$name.'_buttons'] ) ) {
				foreach( $options[''.$name.'_buttons'] as $btn ) {

					if( !in_array( $btn['cow'], $array ) ) {
						if(
							( get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'], true) !== '') && 
							!empty( $btn['label'] ) && 
							empty( $btn['deny_receipt'] ) && 
							( $btn['type'] !== 'heading' ) && 
							( $btn['type'] !== 'multiselect' ) && 
							$btn['type'] !== 'wooccmupload' && 
							( $btn['type'] !== 'multicheckbox' )
						) {
							echo '
<p>
	<strong>'.wooccm_wpml_string($btn['label']).':</strong> '.nl2br(get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true)).'
</p>';
						} elseif (
							!empty( $btn['label'] ) && 
							empty( $btn['deny_receipt'] ) && 
							( $btn['type'] == 'heading' ) && 
							( $btn['type'] !== 'multiselect' ) && 
							$btn['type'] !== 'wooccmupload' && 
							( $btn['type'] !== 'multicheckbox' )
						) {
							echo '
<h2>' .wooccm_wpml_string($btn['label']). '</h2>';
						} elseif (
							( get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true) !== '' ) && 
							!empty( $btn['label'] ) && 
							empty( $btn['deny_receipt'] ) && 
							( $btn['type'] !== 'heading' ) && 
							$btn['type'] !== 'wooccmupload' && 
							(
								( $btn['type'] == 'multiselect' ) || ( $btn['type'] == 'multicheckbox' )
							)
						) {
							$value = get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true);
							$strings = maybe_unserialize( $value );
							echo '
<p>
	<strong>'.wooccm_wpml_string($btn['label']).':</strong> ';
							if( !empty( $strings ) ) {
								if( is_array( $strings ) ) {
									$iww = 0;
									$len = count( $strings );
									foreach( $strings as $key ) {
										if( $iww == $len - 1 ) {
											echo $key;
										} else {
											echo $key.', ';
										}
										$iww++;
									}
								} else {
									echo $strings;
								}
							} else {
								echo '-';
							}
							echo '
</p>';
						} elseif( $btn['type'] == 'wooccmupload' ) {
							$info = explode("||", get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'].'', true));
							$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
							echo '
<p>
	<strong>'.wooccm_wpml_string( trim( $btn['label'] ) ).':</strong> '.$info[0].'
</p>';
						}
					}

				}
			}
			$inc--;

		}

		$options = get_option( 'wccs_settings' );
		if( !empty( $options['buttons'] ) ) {
			foreach( $options['buttons'] as $btn ) {

				if ( (''.get_post_meta( $order->id , ''.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty($btn['deny_receipt']) && ($btn['type'] !== 'heading') && ($btn['type'] !== 'multiselect') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multicheckbox') ) {
					echo '<p><strong>'.wooccm_wpml_string($btn['label']).':</strong> '.nl2br(get_post_meta( $order->id , ''.$btn['cow'].'', true)).'</p>';
				} elseif ( !empty( $btn['label'] ) && empty($btn['deny_receipt']) && ($btn['type'] == 'heading') && ($btn['type'] !== 'multiselect') && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'multicheckbox') ) {
					echo '<h2>'.wooccm_wpml_string($btn['label']).'</h2>';
				} elseif ( (''.get_post_meta( $order->id , ''.$btn['cow'].'', true).'' !== '') && !empty( $btn['label'] ) && empty($btn['deny_receipt']) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) ) {
					$value = get_post_meta( $order->id , $btn['cow'], true );
					$strings = maybe_unserialize( $value );
					echo '
<p>
	<strong>'.wooccm_wpml_string($btn['label']).':</strong> ';
					if( !empty( $strings ) ) {
						if( is_array( $strings ) ) {
							$iww = 0;
							$len = count( $strings );
							foreach( $strings as $key ) {
								if( $iww == $len - 1 ) {
									echo $key;
								} else {
									echo $key.', ';
								}
								$iww++;
							}
						} else {
							echo $strings;
						}
					} else {
						echo '-';
					}
					echo '
</p>';
				} elseif( $btn['type'] == 'wooccmupload' ) {
					$info = explode( "||", get_post_meta( $order->id , ''.$btn['cow'].'', true));
					$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
					echo '
<p>
	<strong>'.wooccm_wpml_string( trim( $btn['label'] ) ).':</strong> '.$info[0].'
</p>';
				}

			}
		}

		// @mod - We are not doing any checking for valid TimeZone
		if ( !empty($options['checkness']['set_timezone']) ) {
			date_default_timezone_set(''.$options['checkness']['set_timezone'].'');
		}
		$date = ( !empty($options['checkness']['twenty_hour'])) ? date("G:i T (P").' GMT)' : date("g:i a");
		$options['checkness']['time_stamp'] = ( isset( $options['checkness']['time_stamp'] ) ? $options['checkness']['time_stamp'] : false );
		if ( $options['checkness']['time_stamp'] == true ) {
			echo '
<p>
	<strong>'.$options['checkness']['time_stamp_title'].':</strong> ' . $date . '
</p>';
		}
		if ( $order->payment_method_title && $options['checkness']['payment_method_t'] == true ) {
			echo '
<p>
	<strong>'.$options['checkness']['payment_method_d'].':</strong> ' . $order->payment_method_title . '
</p>';
		}
		if ( $order->shipping_method_title && ($options['checkness']['shipping_method_t'] == true)) {
			echo '
<p>
	<strong>'.$options['checkness']['shipping_method_d'].':</strong> ' . $order->shipping_method_title . '
</p>';
		}

	}

}

function wooccm_custom_checkout_fields( $checkout ) {

	$options = get_option( 'wccs_settings' );
	if( !empty($options['buttons'])) {
		foreach ( $options['buttons'] as $btn ) {

			if ( $btn['type'] == 'heading' && empty($btn['deny_checkout'] ) ) {
				echo '
<h3 class="form-row '.$btn['position'].'" id="'.$btn['cow'].'_field">' . wooccm_wpml_string(''.$btn['label'].'') . '</h3>';
			}

			if ( $btn['type'] == 'wooccmtext' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'wooccmtext',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'placeholder'       => wooccm_wpml_string(''.$btn['placeholder'].''),
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}


			if ( $btn['type'] == 'wooccmtextarea' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'wooccmtextarea',
					'class'         => array(''.$btn['position'].' wccs-form-row-wide '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'placeholder'       => wooccm_wpml_string(''.$btn['placeholder'].''),
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

			if ( $btn['type'] == 'colorpicker' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'colorpicker',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' wccs_colorpicker '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'placeholder'       => wooccm_wpml_string(''.$btn['placeholder'].''),
					'color' => ''. ( isset( $btn['colorpickerd'] ) ? $btn['colorpickerd'] : '' ) .'',
					'colorpickertype' => ''. ( isset( $btn['colorpickertype'] ) ? $btn['colorpickertype'] : '' ) .''
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}


			if ( $btn['type'] == 'datepicker' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'wooccmtext',
					'class'         => array(''.$btn['position'].' MyDate'.$btn['cow'].' wccs-form-row-wide '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'placeholder'       => wooccm_wpml_string(''.$btn['placeholder'].''),
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

			if ( $btn['type'] == 'time' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'wooccmtext',
					'class'         => array(''.$btn['position'].' MyTime'.$btn['cow'].' wccs-form-row-wide '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'placeholder'       => wooccm_wpml_string(''.$btn['placeholder'].''),
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}


			if ( $btn['type'] == 'checkbox_wccm' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'checkbox_wccm',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'options'       => ''.$btn['option_array'].'',
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

			if ( $btn['type'] == 'wooccmpassword' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'wooccmpassword',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' '.$btn['extra_class'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'placeholder'       => ''.$btn['placeholder'].'',
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

			if ( $btn['type'] == 'wooccmradio' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'wooccmradio',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'default' => ''.$btn['force_title2'].'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'options'       => ''.$btn['option_array'].'',
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

			if ( $btn['type'] == 'multiselect' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'multiselect',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'options'       => ''.$btn['option_array'].'',
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

			if ( $btn['type'] == 'multicheckbox' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'multicheckbox',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'options'       => ''.$btn['option_array'].'',
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

			if ( $btn['type'] == 'wooccmselect' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'wooccmselect',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'fancy' => ''. ( isset( $btn['fancy'] ) ? $btn['fancy'] : '' ) .'',
					'default' => ''.$btn['force_title2'].'',
					'options'       => ''.$btn['option_array'].'',
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

			if ( $btn['type'] == 'wooccmupload' ) {
				woocommerce_form_field( ''.$btn['cow'].'' , array(
					'type'          => 'wooccmupload',
					'placeholder'          => ''.$btn['placeholder'].'',
					'class'         => array(''.$btn['position'].' '.$btn['conditional_tie'].' '.$btn['extra_class'].''),
					'label'         =>  wooccm_wpml_string(''.$btn['label'].''),
					'wooccm_required'  => ''. ( isset( $btn['checkbox'] ) ? $btn['checkbox'] : '' ) .'',
					'clear'  => ''. ( isset( $btn['clear_row'] ) ? $btn['clear_row'] : '' ) .'',
					'user_role'  => ''. ( isset( $btn['user_role'] ) ? $btn['user_role'] : '' ) .'',
					'role_options' => ''. ( isset( $btn['role_options'] ) ? $btn['role_options'] : '' ) .'',
					'role_options2' => ''. ( isset( $btn['role_options2'] ) ? $btn['role_options2'] : '' ) .'',
					'fancy' => ''. ( isset( $btn['fancy'] ) ? $btn['fancy'] : '' ) .'',
					'default' => ''.$btn['force_title2'].'',
					'options'       => ''.$btn['option_array'].'',
				), $checkout->get_value( ''.$btn['cow'].'' ));
			}

		}
	}

}

function wooccm_positioning() {

	$options = get_option( 'wccs_settings' );
	$position = ( !empty( $options['checkness']['position'] ) ? sanitize_text_field( $options['checkness']['position'] ) : false );
	switch( $position ) {

		case 'before_shipping_form':
		case 'after_shipping_form':
		case 'before_billing_form':
		case 'after_billing_form':
		case 'after_order_notes':
			return $position;
			break;

	}

}

function wooccm_custom_checkout_field_update_order_meta( $order ) {

	$options = get_option( 'wccs_settings' );
	if( !empty( $options['buttons'] ) ) {
		foreach( $options['buttons'] as $btn ) {
			$label = ( isset( $btn['label'] ) ) ? $btn['label'] : '';
			if ( $btn['type'] !== 'multiselect' && $btn['type'] !== 'multicheckbox' ) {
				if ( !empty( $_POST[ ''.$btn['cow'].'' ] ) ) {
					update_post_meta( $order, ''.$btn['cow'].'' , sanitize_text_field( $_POST[ ''.$btn['cow'].'' ] ) );
				}
			} elseif ( $btn['type'] == 'multiselect' || $btn['type'] == 'multicheckbox' ) {
				if ( !empty( $_POST[ ''.$btn['cow'].'' ]) ) {
					update_post_meta( $order, ''.$btn['cow'].'' , maybe_serialize( array_map( 'sanitize_text_field', $_POST[ ''.$btn['cow'].'' ] ) ) );
				}
			}
		}
	}

}

function wooccm_custom_checkout_field_process() {

	global $woocommerce;

	$options = get_option( 'wccs_settings' );
	if( !empty( $options['buttons'] ) ) {
		foreach ( $options['buttons'] as $btn ) {
			if( isset( $btn['checkbox'] ) && $btn['checkbox'] === 'true' ) {
				// without checkbox
				if(
					empty($btn['single_px_cat']) && 
					empty($btn['single_p_cat']) && 
					empty($btn['single_px']) && 
					empty($btn['single_p']) && 
					!empty( $btn['label'] ) && 
					$btn['type'] !== 'wooccmupload' && 
					$btn['type'] !== 'changename' && 
					$btn['type'] !== 'heading'
				) {
					if( empty( $_POST[''.$btn['cow'].''] ) ) {
						$message = sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), '<strong>'.wooccm_wpml_string($btn['label']).'</strong>' );
						wc_add_notice( $message, 'error' );
					}
				}
				// checkbox
				if( 
					empty($btn['single_px_cat']) && 
					empty($btn['single_p_cat']) && 
					empty($btn['single_px']) && 
					empty($btn['single_p']) && 
					$btn['type'] == 'checkbox' && 
					!empty( $btn['label'] ) && 
					$btn['type'] !== 'changename' && 
					$btn['type'] !== 'wooccmupload' && 
					$btn['type'] !== 'heading'
				) {
					if( ( sanitize_text_field( $_POST[ ''.$btn['cow'].'' ] ) == ''.$btn['check_2'].'')  && (!empty ($btn['checkbox']) ) ) {
						$message = sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), '<strong>'.wooccm_wpml_string($btn['label']).'</strong>' );
						wc_add_notice( $message, 'error' );
					}
				}
			}
		}
	}

}

function wooccm_options_validate( $input ) {

	$detect_error = 0;
	// translate additional fields
	if( !empty($input['buttons']) ) {
		foreach( $input['buttons'] as $i => $btn ) {

			if( function_exists( 'icl_register_string' ) ) {
				if( !empty($btn['label']) ) {
					icl_register_string('WooCommerce Checkout Manager', ''.$btn['label'].'', ''.$btn['label'].'');
				}
				if( !empty($btn['placeholder']) ) {
					icl_register_string('WooCommerce Checkout Manager', ''.$btn['placeholder'].'', ''.$btn['placeholder'].'');
				}

				if( !empty($btn['option_array']) ) {	
					$mysecureop = explode( '||', $btn['option_array']);
					foreach ( $mysecureop as $one ) {
						icl_register_string('WooCommerce Checkout Manager', ''.$one.'', ''.$one.'');
					}
				}
			}

			if( !empty($btn['role_options']) && !empty($btn['role_options2']) ) { 
				$input['buttons'][$i]['role_options2'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both role options. OK.', 'woocommerce-checkout-manager' ),
					'error'
				);
			} 

			if( !empty($btn['single_p']) && !empty($btn['single_px']) ) { 
				$input['buttons'][$i]['single_px'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both hidden product options. OK.', 'woocommerce-checkout-manager' ),
					'error'
				);
			} 

			if( !empty($btn['single_p_cat']) && !empty($btn['single_px_cat']) ) { 
				$input['buttons'][$i]['single_px_cat'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both hidden category options. OK.', 'woocommerce-checkout-manager' ),
					'error'
				);
			} 

			if( empty( $btn['cow'] ) && empty( $btn['label'] ) && empty( $btn['placeholder'] ) ) {
				unset( $input['buttons'][$i] );

				if( $i != 999 ) {
					$detect_error++;
					$fieldnum = $i + 1;
					add_settings_error(
						'wooccm_settings_errors',
						esc_attr( 'settings_updated' ),
						__( 'Sorry! An error occurred. WooCommerce Checkout Manager removed field # '.$fieldnum.' because no Label or Placeholder name was detected.', 'woocommerce-checkout-manager' ),
						'error'
					);
				}
			}

			if ( empty( $btn['cow'] ) && (!empty( $btn['label'] ) || !empty( $btn['placeholder'] )) ) {
				$newNum = $i + 1;
				if( wooccm_mul_array( 'myfield'.$newNum.'' , $input['buttons'] ) ) {
					$input['buttons'][$i]['cow'] = 'myfield'.$newNum.'c';
				} else {
					$input['buttons'][$i]['cow'] = 'myfield'.$newNum.'';
				}
			}

			if( !empty( $btn['cow'] ) && empty( $btn['label'] ) && empty( $btn['placeholder'] ) ) {
				unset( $input['buttons'][$i] );

				if( $i != 999 ) {
					$detect_error++;
					$fieldnum = $i + 1;
					add_settings_error(
						'wooccm_settings_errors',
						esc_attr( 'settings_updated' ),
						__( 'Sorry! An error occurred. WooCommerce Checkout Manager removed field # '.$fieldnum.' because no Label or Placeholder name was detected.', 'woocommerce-checkout-manager' ),
						'error'
					);
				}
			}

		}
	}
	if( $detect_error == 0 ) {
		add_settings_error(
			'wooccm_settings_errors',
			esc_attr( 'settings_updated' ),
			__( 'Your changes have been saved.', 'woocommerce-checkout-manager' ),
			'updated'
		);
	}
	return $input;

}

function wooccm_options_validate_shipping( $input ) {

	$detect_error = 0;
	// translate shipping fields
	if( !empty( $input['shipping_buttons'] ) ) {
		foreach( $input['shipping_buttons'] as $i => $btn ) {

			if( function_exists( 'icl_register_string' ) ) {
				if( !empty($btn['label']) ) {
					icl_register_string('WooCommerce Checkout Manager', ''.$btn['label'].'', ''.$btn['label'].'');
				}
				if( !empty($btn['placeholder']) ) {
					icl_register_string('WooCommerce Checkout Manager', ''.$btn['placeholder'].'', ''.$btn['placeholder'].'');
				}

				if( !empty($btn['option_array']) ) {
					$mysecureop = explode( '||', $btn['option_array']);
					foreach ( $mysecureop as $one ) {
						icl_register_string('WooCommerce Checkout Manager', ''.$one.'', ''.$one.'');
					}
				}
			}

			if( !empty($btn['role_options']) && !empty($btn['role_options2']) ) {
				$input['buttons'][$i]['role_options2'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both role options.', 'woocommerce-checkout-manager' ),
					'error'
				);
			}

			if( !empty($btn['single_p']) && !empty($btn['single_px']) ) {
				$input['buttons'][$i]['single_px'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both hidden product options.', 'woocommerce-checkout-manager' ),
					'error'
				);
			}

			if( !empty($btn['single_p_cat']) && !empty($btn['single_px_cat']) ) { 
				$input['buttons'][$i]['single_px_cat'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both hidden category options.', 'woocommerce-checkout-manager' ),
					'error'
				);
			}

			if( empty( $btn['cow'] ) && empty( $btn['label'] ) && empty( $btn['placeholder'] ) ) {
				unset( $input['shipping_buttons'][$i] );

				if ( $i != 999 ) {
					$detect_error++;
					$fieldnum = $i + 1;
					add_settings_error(
						'wooccm_settings_errors',
						esc_attr( 'settings_updated' ),
						__( 'Sorry! An error occurred. WooCommerce Checkout Manager removed field # '.$fieldnum.' because no Label or Placeholder name was detected.', 'woocommerce-checkout-manager' ),
						'error'
						);
				}
			}

			if( empty( $btn['cow'] ) && (!empty( $btn['label'] ) || !empty( $btn['placeholder'] )) ) {
				$newNum = $i + 1;
				if( wooccm_mul_array( 'myfield'.$newNum.'' , $input['shipping_buttons'] ) ) {
					$input['shipping_buttons'][$i]['cow'] = 'myfield'.$newNum.'c';
				} else {
					$input['shipping_buttons'][$i]['cow'] = 'myfield'.$newNum.'';
				}
			} 

			if( !empty( $btn['cow'] ) && empty( $btn['label'] ) && empty( $btn['placeholder'] ) ) {
				unset( $input['shipping_buttons'][$i] );

				if( $i != 999 ) {
					$detect_error++;
					$fieldnum = $i + 1;
					add_settings_error(
						'wooccm_settings_errors',
						esc_attr( 'settings_updated' ),
						__( 'Sorry! An error occurred. WooCommerce Checkout Manager removed field # '.$fieldnum.' because no Label or Placeholder name was detected.', 'woocommerce-checkout-manager' ),
						'error'
					);
				}
			}

		}
	}

	if( $detect_error == 0 ) {
		add_settings_error(
			'wooccm_settings_errors',
			esc_attr( 'settings_updated' ),
			__( 'Your changes have been saved.', 'woocommerce-checkout-manager' ),
			'updated'
		);
	}

	return $input;

}

function wooccm_options_validate_billing( $input ) {

	$detect_error = 0;

	// translate billing fields
	if( !empty($input['billing_buttons']) ) {
		foreach( $input['billing_buttons'] as $i => $btn ) {

			if( function_exists( 'icl_register_string' ) ) {
				if( !empty($btn['label']) ) {
					icl_register_string('WooCommerce Checkout Manager', ''.$btn['label'].'', ''.$btn['label'].'');
				}
				if( !empty($btn['placeholder']) ) {
					icl_register_string('WooCommerce Checkout Manager', ''.$btn['placeholder'].'', ''.$btn['placeholder'].'');
				}

				if ( !empty($btn['option_array']) ) {
					$mysecureop = explode( '||', $btn['option_array']);
					foreach( $mysecureop as $one ) {
						icl_register_string('WooCommerce Checkout Manager', ''.$one.'', ''.$one.'');
					}
				}
			}

			if( !empty($btn['role_options']) && !empty($btn['role_options2']) ) { 
				$input['buttons'][$i]['role_options2'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both role options.', 'woocommerce-checkout-manager' ),
					'error'
				);
			}

			if( !empty($btn['single_p']) && !empty($btn['single_px']) ) { 
				$input['buttons'][$i]['single_px'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both hidden product options.', 'woocommerce-checkout-manager' ),
					'error'
				);
			}

			if( !empty($btn['single_p_cat']) && !empty($btn['single_px_cat']) ) { 
				$input['buttons'][$i]['single_px_cat'] = '';
				add_settings_error(
					'wooccm_settings_errors',
					esc_attr( 'settings_updated' ),
					__( 'Sorry! An error occurred. WooCommerce Checkout Manager requires you to not have values in both hidden category options.', 'woocommerce-checkout-manager' ),
					'error'
				);
			}

			if( empty( $btn['cow'] ) && empty( $btn['label'] ) && empty( $btn['placeholder'] ) ) { 
				unset( $input['billing_buttons'][$i] );

				if( $i != 999 ) {
					$detect_error++;
					$fieldnum = $i + 1;
					add_settings_error(
						'wooccm_settings_errors',
						esc_attr( 'settings_updated' ),
						__( 'Sorry! An error occurred. WooCommerce Checkout Manager removed field # '.$fieldnum.' because no Label or Placeholder name was detected.', 'woocommerce-checkout-manager' ),
						'error'
					);
				}
			}

			if( empty( $btn['cow'] ) && (!empty( $btn['label'] ) || !empty( $btn['placeholder'] )) ) {
				$newNum = $i + 1;
				if( wooccm_mul_array( 'myfield'.$newNum.'' , $input['billing_buttons'] ) ) {
					$input['billing_buttons'][$i]['cow'] = 'myfield'.$newNum.'c';
				} else {
					$input['billing_buttons'][$i]['cow'] = 'myfield'.$newNum.'';
				}
			}

			if( !empty( $btn['cow'] ) && empty( $btn['label'] ) && empty( $btn['placeholder'] ) ) { 
				$detect_error++;
				unset( $input['billing_buttons'][$i] );

				if( $i != 999 ) {
					$detect_error++;
					$fieldnum = $i + 1;
					add_settings_error(
						'wooccm_settings_errors',
						esc_attr( 'settings_updated' ),
						__( 'Sorry! An error occurred. WooCommerce Checkout Manager removed field # '.$fieldnum.' because no Label or Placeholder name was detected.', 'woocommerce-checkout-manager' ),
						'error'
					);
				}
			}

		}
	}

	if( $detect_error == 0 ) {
		add_settings_error(
			'wooccm_settings_errors',
			esc_attr( 'settings_updated' ),
			__( 'Your changes have been saved.', 'woocommerce-checkout-manager' ),
			'updated'
		);
	}

	return $input;

}

function wooccm_checkout_text_after() {

	$options = get_option( 'wccs_settings' );

	if( !empty($options['checkness']['text2']) ) {
		if( $options['checkness']['checkbox3'] == true || $options['checkness']['checkbox4'] == true ) {
			if( $options['checkness']['checkbox4'] == true ) {
				echo ''.$options['checkness']['text2'].'';
			}
		}
	}

	if( !empty($options['checkness']['text1']) ) {
		if( $options['checkness']['checkbox1'] == true || $options['checkness']['checkbox2'] == true ) {
			if( $options['checkness']['checkbox2'] == true ) {
				echo ''.$options['checkness']['text1'].'';
			}
		}
	}

}

function wooccm_checkout_text_before(){

	$options = get_option( 'wccs_settings' );

	if( !empty($options['checkness']['text2']) ) {
		if( $options['checkness']['checkbox3'] == true || $options['checkness']['checkbox4'] == true ) {
			if( $options['checkness']['checkbox3'] == true ) {
				echo ''.$options['checkness']['text2'].'';
			}
		}
	}

	if( !empty($options['checkness']['text1']) ) {
		if( $options['checkness']['checkbox1'] == true || $options['checkness']['checkbox2'] == true ) {
			if( $options['checkness']['checkbox1'] == true ) {
				echo ''.$options['checkness']['text1'].'';
			}
		}
	}

}

function wooccm_remove_fields_filter($fields){

	global $woocommerce;

	// Check if the cart is not empty
	if( empty( $woocommerce->cart->cart_contents ) )
		return $fields;

	$options = get_option( 'wccs_settings' );

	foreach( $woocommerce->cart->cart_contents as $key => $values ) {

		$multiCategoriesx = ( isset( $options['checkness']['productssave'] ) ? $options['checkness']['productssave'] : '' );
		$multiCategoriesArrayx = explode(',',$multiCategoriesx);

		if( in_array($values['product_id'],$multiCategoriesArrayx) && ($woocommerce->cart->cart_contents_count < 2) ) {
			unset($fields['billing']['billing_address_1']);
			unset($fields['billing']['billing_address_2']);
			unset($fields['billing']['billing_phone']);
			unset($fields['billing']['billing_country']);
			unset($fields['billing']['billing_city']);
			unset($fields['billing']['billing_postcode']);
			unset($fields['billing']['billing_state']);
			break;
		}

	}
	return $fields;

}

function wooccm_remove_fields_filter3($fields){

	global $woocommerce;

	// Check if the cart is not empty
	if( empty( $woocommerce->cart->cart_contents ) )
		return $fields;

	$options = get_option( 'wccs_settings' );

	foreach( $woocommerce->cart->cart_contents as $key => $values ) {

		$multiCategoriesx = ( isset( $options['checkness']['productssave'] ) ? $options['checkness']['productssave'] : '' );
		$multiCategoriesArrayx = explode(',',$multiCategoriesx);
		$_product = $values['data'];

		if( ($woocommerce->cart->cart_contents_count > 1) && ($_product->needs_shipping()) ){
			remove_filter('woocommerce_checkout_fields','wooccm_remove_fields_filter',15);
			break;
		}

	}
	return $fields;

}

if( wooccm_validator_changename() ) {

	function wooccm_before_checkout() {

		$options = get_option( 'wccs_settings' );

		// Check if the buttons exist
		if( empty( $options['buttons'] ) )
			return;

		foreach( $options['buttons'] as $btn ) {
			$label = ( isset( $btn['label'] ) ) ? $btn['label'] : '';
			ob_start();
		}

	}

	function wooccm_after_checkout() {

		$options = get_option( 'wccs_settings' );

		// Check if the buttons exist
		if( empty( $options['buttons'] ) )
			return;

		foreach( $options['buttons'] as $btn ) {
			if( $btn['type'] == 'changename' ) {
				$content = ob_get_clean();
				echo str_replace( ''.$btn['changenamep'].'', ''.$btn['changename'].'', $content);                   
			}
		}

	}

}

function wooccm_display_front() {

	global $woocommerce;

	if( !is_checkout() )
		return;

	$options = get_option( 'wccs_settings' );

	if( !empty($options['checkness']['additional_info']) ) {
		echo '<style type="text/css">.woocommerce-shipping-fields h3:first-child { display: none; }</style>';
	}

	echo '
<style type="text/css">';
if( !empty( $options['checkness']['custom_css_w'] ) ) {
	echo esc_textarea( $options['checkness']['custom_css_w'] );
}
echo '

@media screen and (max-width: 685px) {
	.woocommerce .checkout .container .wooccm-btn {
		padding: 1% 6%;
	}
}

@media screen and (max-width: 685px) {
	.woocommerce .checkout .container .wooccm-btn {
		padding: 1% 8%;
	}
}

@media screen and (max-width: 770px) {
	.checkout .wooccm_each_file .wooccm-image-holder {
		width: 20%;
	}
	.checkout name.wooccm_name, .wooccm_each_file span.container{
		width: 80%;
	}
	.checkout .container .wooccm-btn {
		padding: 1% 10%;
	}
}

@media screen and (max-width: 992px) {
	.wooccm_each_file .wooccm-image-holder {
		width: 26%;
	}
	name.wooccm_name, .wooccm_each_file span.container{
		width: 74%;
	}
	.container .wooccm-btn {
		padding: 5px 8px;
		font-size: 12px;
	}
}

.container .wooccm-btn {
	padding: 1.7% 6.7%;
}

#caman_content .blockUI.blockOverlay:before, #caman_content .loader:before {
	height: 1em;
	width: 1em;
	position: absolute;
	top: 50%;
	left: 50%;
	margin-left: -.5em;
	margin-top: -.5em;
	display: block;
	-webkit-animation: spin 1s ease-in-out infinite;
	-moz-animation: spin 1s ease-in-out infinite;
	animation: spin 1s ease-in-out infinite;
	content: "";
	/* @mod - We need to check this file exists */
	background: url('.plugins_url( 'woocommerce/assets/images/icons/loader.svg' ).') center center/cover;
	line-height: 1;
	text-align: center;
	font-size: 2em;
	color: rgba(0,0,0,.75);
}
body.admin-bar #caman_content {
	margin-top:	32px;
}

.file_upload_button_hide {
	display: none;
}

.wooccm_each_file {
	display: block;
	padding-top: 20px;
	clear: both;
	text-align: center;
}
.wooccm_each_file .wooccm-image-holder {
	width: 20%;
	display: block;
	float: left;
}
.wooccm-btn.disable {
	margin-right: 10px;
	cursor: auto;
}
zoom.wooccm_zoom, edit.wooccm_edit, dele.wooccm_dele {
	padding: 5px;
}
.wooccm_each_file name {
	font-size: 18px;
}
name.wooccm_name, .wooccm_each_file span.container {
	display: block;
	padding: 0 0 10px 20px;
	float: left;
	width: 80%;
}

.wooccm_each_file img{ 
	display: inline-block;
	height: 90px;
	border: 2px solid #767676;
	border-radius: 4px;
}
.file_upload_account:before{ content: "\f317";font-family: dashicons; margin-right: 10px; }
.wooccm_each_file .wooccm_zoom:before{ content: "\f179";font-family: dashicons; margin-right: 5px; }
.wooccm_each_file .wooccm_edit:before{ content: "\f464";font-family: dashicons; margin-right: 5px; }
.wooccm_each_file .wooccm_dele:before{ content: "\f158";font-family: dashicons; margin-right: 5px; }
.wooccm-btn{
	display: inline-block;
	padding: 6px 12px;
	margin-bottom: 0;
	font-size: 14px;
	font-weight: 400;
	line-height: 1.42857143;
	text-align: center;
	white-space: nowrap;
	vertical-align: middle;
	cursor: pointer;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	background-image: none;
	border: 1px solid transparent;
	border-radius: 4px;
	font-family: "Raleway", Arial, Helvetica, sans-serif; 
	color: #767676;
	background-color: buttonface;
	align-items: flex-start;
	text-indent: 0px;
	text-shadow: none;
	letter-spacing: normal;
	word-spacing: normal;
	text-rendering: auto;
}
.wooccm-btn-primary {
	width: 100%;
	color: #fff;
	background-color: #428bca;
	border-color: #357ebd;
}

.wooccm-btn-danger {
	color: #fff;
	background-color: #d9534f;
	border-color: #d43f3a;
	margin-right: 10px;
}
.wooccm_each_file .container a:hover, .wooccm_each_file .container a:focus, .wooccm_each_file .container a:active, .wooccm_each_file .container a:visited, .wooccm_each_file .container a:link {
	color: #fff;
}
#caman_content #wooccmtoolbar #close:hover, #caman_content #wooccmtoolbar #save:hover {
	background: #1b1917;
}
.wooccm-btn-zoom {
	color: #fff;
	background-color: #5cb85c;
	border-color: #4cae4c;
	margin-right: 10px;
} 

.wooccm-btn-edit {
	color: #fff;
	background-color: #f0ad4e;
	border-color: #eea236;
	margin-right: 10px;
}

</style>';

}

// -----------------------------------------------------------
// -----------------------------------------------------------
// -----------------------------------------------------------
// -----------------------------------------------------------

function wooccm_validator_changename() {

	$options = get_option( 'wccs_settings' );

	if( !empty($options['buttons']) ) {
		foreach( $options['buttons'] as $btn ) {
			if (!empty($btn['type']) ) {
				if ( $btn['type'] == 'changename' && !empty($btn['label']) ){
					return true;
				}
			}
		}
	}

}

if( wooccm_validator_changename() ) {

	// @mod - This function isn't referenced anywhere
	function wooccm_string_replacer( $order ) {

		$options = get_option( 'wccs_settings' );
?>
<header>
	<h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>
</header>

<dl class="customer_details">
<?php 
		if( $order->billing_email ) echo '<dt>'.__( 'Email:', 'woocommerce' ).'</dt><dd>'.$order->billing_email.'</dd>';
		if( $order->billing_phone ) echo '<dt>'.__( 'Telephone:', 'woocommerce' ).'</dt><dd>'.$order->billing_phone.'</dd>';
?>
</dl>

<?php if( get_option('woocommerce_ship_to_billing_address_only') == 'no' ) { ?>

<div class="col2-set addresses">

	<div class="col-1">

<?php } ?>

		<header class="title">
			<h3><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>
		</header>

		<address>
			<p><?php if (!$order->get_formatted_billing_address()) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_billing_address(); ?></p>
		</address>

<?php if( get_option('woocommerce_ship_to_billing_address_only') == 'no' ) { ?>

	</div>
	<!-- .col-1 -->

	<div class="col-2">

		<header class="title">
			<h3><?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>
		</header>

		<address>
			<p><?php if (!$order->get_formatted_shipping_address()) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_shipping_address(); ?></p>
		</address>

	</div>
	<!-- .col-2 -->

</div>
<!-- .col2-set -->

<?php } ?>

<div class="clear"></div>

<script type="text/javascript">
	var array = [];
<?php foreach( $options['buttons'] as $btn ) { ?>
	array.push("<?php echo $btn['changenamep']; ?>" , "<?php echo $btn['changename']; ?>")
<?php } ?>
	b(array);
	function b(array){
		for(var i = 0; i<(array.length-1); i=i+2) {
			document.body.innerHTML= document.body.innerHTML.replace(array[i],array[i+1])
		}
	}
</script>

<?php
	}

}

if( wooccm_enable_auto_complete() ) {

	function wooccm_retain_field_values() {

		$options = get_option( 'wccs_settings' );
		$options2 = get_option( 'wccs_settings2' );
		$options3 = get_option( 'wccs_settings3' );

		if( is_checkout() == false )
			return;

		$saved = WC()->session->get('wooccm_retain', array() );
?>

<script type="text/javascript">

	jQuery(document).ready(function() {
		window.onload = function() {

<?php 
		if( !empty( $options['buttons'] ) ) {
			foreach ( $options['buttons'] as $btn ) {
				if(
					$btn['type'] !== 'wooccmupload' && 
					$btn['type'] !== 'changename' && 
					$btn['type'] !== 'heading' && 
					$btn['disabled'] !== 'true' && 
					empty($btn['tax_remove']) && 
					empty($btn['add_amount']) 
				) {
?>
			document.forms['checkout'].elements['<?php echo $btn['cow']; ?>'].value = "<?php echo $saved[''.$btn['cow'].'']; ?>";
<?php
				}
			}
		}

		if( !is_user_logged_in() ) {

			if( WC()->cart->needs_shipping_address() === true && sanitize_text_field( $_POST['ship_to_different_address'] ) == 1 ) {

				if( !empty( $options2['shipping_buttons'] ) ) {
					foreach ( $options2['shipping_buttons'] as $btn ) {
						if(
							$btn['type'] !== 'wooccmupload' && 
							$btn['type'] !== 'changename' && 
							$btn['type'] !== 'heading' && 
							$btn['disabled'] !== 'true' && 
							empty($btn['tax_remove']) && 
							empty($btn['add_amount'])
						) {
?>
			document.forms['checkout'].elements['shipping_<?php echo $btn['cow']; ?>'].value = "<?php echo $saved['shipping_'.$btn['cow'].'']; ?>";
<?php
						}
					}
				}

			}

			if( !empty( $options3['billing_buttons'] ) ) {
				foreach( $options3['billing_buttons'] as $btn ) {
					if(
						$btn['type'] !== 'wooccmupload' && 
						$btn['type'] !== 'changename' && 
						$btn['type'] !== 'heading' && 
						$btn['disabled'] !== 'true' && 
						empty($btn['tax_remove']) && 
						empty($btn['add_amount'])
					) {
?>
			document.forms['checkout'].elements['billing_<?php echo $btn['cow']; ?>'].value = "<?php echo $saved['billing_'.$btn['cow'].'']; ?>";
<?php
					}
				}
			}

		}
?>

		} 
	}); 
</script>

<script type="text/javascript">

	jQuery(document).ready(function() {
		jQuery('body').change(function() {

			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
			data = { action: 'retain_val_wccs',

<?php
		if( !empty( $options['buttons'] ) ) {
			foreach( $options['buttons'] as $btn ) {
				if ( $btn['type'] !== 'wooccmupload' && $btn['type'] !== 'changename' && $btn['type'] !== 'heading' && empty($btn['tax_remove']) && empty($btn['add_amount']) ) {
?>
			<?php echo $btn['cow']; ?>: jQuery("#<?php echo $btn['cow']; ?>").val(),
<?php
				}
			}
		}

		if( !is_user_logged_in() ) {

			if( WC()->cart->needs_shipping_address() === true && sanitize_text_field( $_POST['ship_to_different_address'] ) == 1 ) {

				if( !empty( $options2['shipping_buttons'] ) ) {
					foreach ( $options2['shipping_buttons'] as $btn ) {
						if ( $btn['type'] !== 'wooccmupload' && $btn['type'] !== 'changename' && $btn['type'] !== 'heading' && empty($btn['tax_remove']) && empty($btn['add_amount']) ) {
?>
			shipping_<?php echo $btn['cow']; ?>: jQuery("shipping_<?php echo $btn['cow']; ?>").val(),
<?php
						}
					}
				}

			}

			if( !empty( $options3['billing_buttons'] ) ) {
				foreach( $options3['billing_buttons'] as $btn ) {
					if ( $btn['type'] !== 'wooccmupload' && $btn['type'] !== 'changename' && $btn['type'] !== 'heading' && empty($btn['tax_remove']) && empty($btn['add_amount']) ) {
?>
			billing_<?php echo $btn['cow']; ?>: jQuery("#billing_<?php echo $btn['cow']; ?>").val(),
<?php
					}
				}
			}

		}
?>
			};

			jQuery.post(ajaxurl, data, function(response) { });
			return false;

		});
	});

</script>

<?php 

	}

	function wooccm_retain_val_callback() {

		global $wpdb;

		$options = get_option( 'wccs_settings' );
		$options2 = get_option( 'wccs_settings2' );
		$options3 = get_option( 'wccs_settings3' );

		if( !empty( $options['buttons'] ) ) {
			foreach( $options['buttons'] as $btn ) {
				if( $btn['type'] !== 'wooccmupload' && $btn['type'] !== 'changename' && $btn['type'] !== 'heading' && empty($btn['tax_remove']) && empty($btn['add_amount']) ) {
					if( !empty( $_POST[''.$btn['cow'].''] ) ) {
						$saved[''.$btn['cow'].''] = sanitize_text_field( $_POST[''.$btn['cow'].''] );      
					}
				}
			}
		}

		if( WC()->cart->needs_shipping_address() === true && sanitize_text_field( $_POST['ship_to_different_address'] ) == 1 ) {
			if( !empty( $options2['shipping_buttons'] ) ) {
				foreach( $options2['shipping_buttons'] as $btn ) {
					if( $btn['type'] !== 'wooccmupload' && $btn['type'] !== 'changename' && $btn['type'] !== 'heading' && empty($btn['tax_remove']) && empty($btn['add_amount']) ) {
						if( !empty( $_POST['shipping_'.$btn['cow'].''] ) ) {
							$saved['shipping_'.$btn['cow'].''] = sanitize_text_field( $_POST['shipping_'.$btn['cow'].''] );
						}
					}
				}
			}
		}

		if( !empty( $options3['billing_buttons'] ) ) {
			foreach( $options3['billing_buttons'] as $btn ) {
				if( $btn['type'] !== 'wooccmupload' && $btn['type'] !== 'changename' && $btn['type'] !== 'heading' && empty($btn['tax_remove']) && empty($btn['add_amount']) ) {
					if( !empty( $_POST['billing_'.$btn['cow'].''] ) ) {
						$saved['billing_'.$btn['cow'].''] = sanitize_text_field( $_POST['billing_'.$btn['cow'].''] );
					}
				}
			}
		}

		WC()->session->set('wooccm_retain', $saved );

		die(); 

	}
	add_action( 'wp_ajax_retain_val_wccs', 'wooccm_retain_val_callback' );
	add_action('wp_ajax_nopriv_retain_val_wccs', 'wooccm_retain_val_callback');

}

function wooccm_enable_auto_complete() {

	$options = get_option( 'wccs_settings' );

	if( !empty($options['checkness']['retainval']) ) {
		return true;
	} else {
		return false;
	}

}

function wooccm_autocreate_account( $fields ) {

	$options = get_option( 'wccs_settings' );

	if( !empty($options['checkness']['auto_create_wccm_account']) ) {
?>
<script type="text/javascript">

	jQuery(document).ready(function() {
		jQuery( "input#createaccount" ).prop("checked","checked");
	});

</script>

<style type="text/css">
.create-account {
	display:none;
}
</style>

<?php
	}

}

function wooccm_remove_tax_wccm() {

	$saved['wooccm_addamount453userf'] = sanitize_text_field( $_POST['add_amount_faj'] );
	$saved['wooccm_tax_save_method'] = sanitize_text_field( $_POST['tax_remove_aj'] );
	$saved['wooccm_addamount453user'] = sanitize_text_field( $_POST['add_amount_aj'] );
	WC()->session->set('wooccm_retain', $saved );

	die();

}
add_action( 'wp_ajax_remove_tax_wccm', 'wooccm_remove_tax_wccm' );
add_action( 'wp_ajax_nopriv_remove_tax_wccm', 'wooccm_remove_tax_wccm' );

function wooccm_custom_user_charge_man( $cart ) {

	global $woocommerce, $wpdb;

	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
	$options3 = get_option( 'wccs_settings3' );

	$saved = WC()->session->get('wooccm_retain', array() );

	if( !empty( $options['buttons'] ) ) {
		foreach( $options['buttons'] as $btn ) {

			if( !empty( $btn['add_amount'] ) && !empty( $btn['add_amount_field'] ) && !empty( $btn['label'] ) && !empty( $btn['fee_name'] ) ) {
				if( $saved['wooccm_addamount453user'] == $btn['chosen_valt'] ) {        
					$woocommerce->cart->add_fee( $btn['fee_name'], $btn['add_amount_field'], false, '' );                    
				}
			}

			if( !empty( $btn['add_amount'] ) && empty( $btn['add_amount_field'] ) && !empty( $btn['label'] ) && !empty( $btn['fee_name'] ) ) {	                
				if( !empty($saved['wooccm_addamount453userf']) && is_numeric($saved['wooccm_addamount453userf']) ) {
					$woocommerce->cart->add_fee( $btn['fee_name'], $saved['wooccm_addamount453userf'], false, '' );  
				}
			}

		}
	}

	if( !empty( $options3['billing_buttons'] ) ) {
		foreach( $options3['billing_buttons'] as $btn ) {

			if( !empty( $btn['add_amount'] ) && !empty( $btn['add_amount_field'] ) && !empty( $btn['label'] ) && !empty( $btn['fee_name'] ) ) {
				if( $saved['wooccm_addamount453user'] == $btn['chosen_valt'] ) {        
					$woocommerce->cart->add_fee( $btn['fee_name'], $btn['add_amount_field'], false, '' );                    
				}
			}

			if( !empty( $btn['add_amount'] ) && empty( $btn['add_amount_field'] ) && !empty( $btn['label'] ) && !empty( $btn['fee_name'] ) ) {	                
				if( !empty($saved['wooccm_addamount453userf']) && is_numeric($saved['wooccm_addamount453userf']) ) {
					$woocommerce->cart->add_fee( $btn['fee_name'], $saved['wooccm_addamount453userf'], false, '' );  
				}
			}

		}
	}

	if( !empty( $options2['shipping_buttons'] ) ) {
		foreach( $options2['shipping_buttons'] as $btn ) {

			if( !empty( $btn['add_amount'] ) && !empty( $btn['add_amount_field'] ) && !empty( $btn['label'] ) && !empty( $btn['fee_name'] ) ) {
				if( $saved['wooccm_addamount453user'] == $btn['chosen_valt'] ) {        
					$woocommerce->cart->add_fee( $btn['fee_name'], $btn['add_amount_field'], false, '' );                    
				}
			}

			if( !empty( $btn['add_amount'] ) && empty( $btn['add_amount_field'] ) && !empty( $btn['label'] ) && !empty( $btn['fee_name'] ) ) {	                
				if( !empty($saved['wooccm_addamount453userf']) && is_numeric($saved['wooccm_addamount453userf']) ) {
					$woocommerce->cart->add_fee( $btn['fee_name'], $saved['wooccm_addamount453userf'], false, '' );  
				}
			}

		}
	}

}
add_action( 'woocommerce_cart_calculate_fees','wooccm_custom_user_charge_man' );

function wooccm_remove_tax_for_exempt( $cart ) {

	global $woocommerce, $wpdb;

	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
	$options3 = get_option( 'wccs_settings3' );

	$saved = WC()->session->get('wooccm_retain', array() );

	if( !empty( $options['buttons'] ) ) {
		foreach( $options['buttons'] as $btn ) {
			if( !empty( $btn['tax_remove'] ) ) {
				if( $saved['wooccm_tax_save_method'] == $btn['chosen_valt'] ) {
					$cart->remove_taxes();
				}
			}
		}
	}

	if( !empty( $options3['billing_buttons'] ) ) {
		foreach( $options3['billing_buttons'] as $btn ) {
			if( !empty( $btn['tax_remove'] ) ) {
				if( $saved['wooccm_tax_save_method'] == $btn['chosen_valt'] ) {
					$cart->remove_taxes();
				}
			}
		}
	}

	if( !empty( $options2['shipping_buttons'] ) ) {
		foreach( $options2['shipping_buttons'] as $btn ) {
			if( !empty( $btn['tax_remove'] ) ) {
				if( $saved['wooccm_tax_save_method'] == $btn['chosen_valt'] ) {
					$cart->remove_taxes();
				}
			}
		}
	}

	return $cart;

}
add_action( 'woocommerce_calculate_totals', 'wooccm_remove_tax_for_exempt' );


function wooccm_state_default_switch() {

	$options = get_option( 'wccs_settings' );

	if( !empty($options['checkness']['per_state']) && !empty($options['checkness']['per_state_check']) ) {
		return ''.$options['checkness']['per_state'].''; 
	}

}

function wooccm_woocommerce_delivery_notes_compat( $fields, $order ) {

	$new_fields = array();

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

					if( get_post_meta( $order->id, '_'.$name.'_'.$btn['cow'].'', true ) && (($btn['type'] !== 'multiselect') || ($btn['type'] !== 'multicheckbox')) &&  $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'heading') ) {
						$new_fields['_'.$name.'_'.$btn['cow'].''] = array( 
							'label' => ''.wooccm_wpml_string($btn['label']).'',
							'value' => get_post_meta( $order->id, '_'.$name.'_'.$btn['cow'].'', true )
						);
					}

					if( get_post_meta( $order->id, '_'.$name.'_'.$btn['cow'].'', true )  && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'heading')) {
						$new_fields['_'.$name.'_'.$btn['cow'].'']['label'] = ''.wooccm_wpml_string($btn['label']).'';
						$new_fields['_'.$name.'_'.$btn['cow'].'']['value'] = '';
						$value = get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'], true );
						$strings = maybe_unserialize( $value );
						if( !empty( $strings ) ) {
							if( is_array( $strings ) ) {
								$iww = 0;
								$len = count( $strings );
								foreach( $strings as $key ) {
									if( $iww == $len - 1 ) {
										$new_fields['_'.$name.'_'.$btn['cow']]['value'] .= $key;
									} else {
										$new_fields['_'.$name.'_'.$btn['cow']]['value'] .= $key.', ';
									}
									$iww++;
								}
							} else {
								echo $strings;
							}
						} else {
							echo '-';
						}

					} elseif( $btn['type'] == 'wooccmupload' ) {
						$info = explode("||",get_post_meta( $order->id, '_'.$name.'_'.$btn['cow'].'', true ));
						$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
						$new_fields['_'.$name.'_'.$btn['cow'].''] = array( 
							'label' => wooccm_wpml_string( trim( $btn['label'] ) ),
							'value' => $info[0]
						);
					}

				}

			}
		}
		$inc--;

	}

	$options = get_option( 'wccs_settings' );
	if( !empty( $options['buttons'] ) ) {
		foreach( $options['buttons'] as $btn ) {

			if( get_post_meta( $order->id, ''.$btn['cow'].'', true ) && (($btn['type'] !== 'multiselect') || ($btn['type'] !== 'multicheckbox')) && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'heading') ) {
				$new_fields[''.$btn['cow'].''] = array( 
					'label' => ''.wooccm_wpml_string($btn['label']).'',
					'value' => get_post_meta( $order->id, ''.$btn['cow'].'', true )
				);
			}

			if( get_post_meta( $order->id, ''.$btn['cow'].'', true )  && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) && $btn['type'] !== 'wooccmupload' && ($btn['type'] !== 'heading')) {
				$new_fields[$btn['cow']]['label'] = ''.wooccm_wpml_string($btn['label']).'';
				$new_fields[$btn['cow']]['value'] = '';
				$value = get_post_meta( $order->id , $btn['cow'], true );
				$strings = maybe_unserialize( $value );
				if( !empty( $strings ) ) {
					if( is_array( $strings ) ) {
						$iww = 0;
						$len = count( $strings );
						foreach( $strings as $key ) {
							if( $iww == $len - 1) {
								$new_fields[$btn['cow']]['value'] .= $key;
							} else {
								$new_fields[$btn['cow']]['value'] .= $key.', ';
							}
							$iww++;
						}
					} else {
						echo $strings;
					}
				} else {
					echo '-';
				}
			}

			if( $btn['type'] == 'wooccmupload' ){
				$info = get_post_meta( $order->id, ''.$btn['cow'].'', true );
				$btn['label'] = ( !empty( $btn['force_title2'] ) ? $btn['force_title2'] : $btn['label'] );
				$new_fields[''.$btn['cow'].''] = array( 
					'label' => wooccm_wpml_string( trim( $btn['label'] ) ),
					'value' => $info[0]
				);
			}
		}
	}

	return array_merge( $fields, $new_fields );

}

function wooccm_order_notes( $fields ) {

	$options = get_option( 'wccs_settings' );

	if( !empty($options['checkness']['noteslabel']) ) {
		$fields['order']['order_comments']['label'] = $options['checkness']['noteslabel'];
	}
	if( !empty($options['checkness']['notesplaceholder']) ) {
		$fields['order']['order_comments']['placeholder'] = $options['checkness']['notesplaceholder'];
	}
	if( !empty($options['checkness']['notesenable']) ) {
		unset($fields['order']['order_comments']);
	}
	return $fields;

}

function woooccm_restrict_manage_posts() {

	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
	$options3 = get_option( 'wccs_settings3' );

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
		'email','phone'
	);
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

	$post_type = 'shop_order';
	if( get_current_screen()->post_type == $post_type ) {

		$values = array();
		if( !empty( $options['buttons'] ) ) {
			foreach( $options['buttons'] as $name ) {
				$values[$name['label']] = $name['cow'];
			}
		}
		if( !empty( $values ) ) {
			array_unique($values);
		}

		$values2 = array();
		if( !empty( $options2['shipping_buttons'] ) ) {
			foreach( $options2['shipping_buttons'] as $name ) {
				if( !in_array($name['cow'], $shipping) ) {
					$values2['Shipping '.$name['label']] = '_shipping_'.$name['cow'];
				}
			}
		}
		if( !empty($values2) ) {
			array_unique($values2);
		}

		$values3 = array();
		if( !empty( $options3['billing_buttons'] ) ) {
			foreach( $options3['billing_buttons'] as $name ) {
				if( !in_array($name['cow'], $billing)){
					$values3['Billing '.$name['label']] = '_billing_'.$name['cow'];
				}
			}
		}
		if( !empty($values3) ) {
			array_unique($values3);
		}

		if( !empty($values) && !empty($values2) && !empty($values3) ) {
			$values = array_merge($values, $values2);
			$values = array_merge($values, $values3);
		} elseif( !empty($values) && !empty($values2) && empty($values3) ) {
			$values = array_merge($values, $values2);
		} elseif( !empty($values) && empty($values2) && !empty($values3) ) {
			$values = array_merge($values, $values3);
		} elseif( empty($values) && !empty($values2) && !empty($values3) ) {
			$values = array_merge($values2, $values3);
		} elseif( empty($values) && empty($values2) && !empty($values3) ) {
			$values = $values3;
		} elseif( empty($values) && !empty($values2) && empty($values3) ) {
			$values = $values2;
		} elseif( !empty($values) && empty($values2) && empty($values3) ) {
			$values = $values;
		}
?>
<select name="wooccm_abbreviation">
<?php if( empty($values) && empty($values2) && empty($values3) ) { ?>
	<option value=""><?php _e('No Added Fields', 'woocommerce-checkout-manager'); ?></option>
<?php } else { ?>
	<option value=""><?php _e('Field Name', 'woocommerce-checkout-manager'); ?></option>
<?php } ?>
<?php
		$current_v = ( isset( $_GET['wooccm_abbreviation'] ) ? sanitize_text_field( $_GET['wooccm_abbreviation'] ) : '' );
		if( !empty( $values ) ) {
			foreach( $values as $label => $value ) {
				printf(
					'<option value="%s"%s>%s</option>',
					$value,
					$value == $current_v? ' selected="selected"':'',
					$label
				);
			}
		}
?>
</select>
<?php

	}

}

function wooccm_query_list( $query ) {

	global $pagenow;

	$wooccm_abbreviation = ( isset( $_GET['wooccm_abbreviation'] ) ? sanitize_text_field( $_GET['wooccm_abbreviation'] ) : '' );
	if( is_admin() && $pagenow == 'edit.php' && $wooccm_abbreviation != '' ) {
		$query->query_vars[ 'meta_key' ] = $wooccm_abbreviation;
	}

}

// ========================================
// Remove conditional notices
// ========================================

function wooccm_remove_notices_conditional( $posted ) {

	$notice = WC()->session->get( 'wc_notices' );

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

		$options2 = get_option( 'wccs_settings'.$inc.'' );
		if( !empty( $options2[''.$name.'_buttons'] ) ) {
			foreach( $options2[''.$name.'_buttons'] as $btn ) {

				if( !empty($btn['chosen_valt']) && !empty($btn['conditional_parent_use']) && !empty($btn['conditional_tie']) && $btn['type'] !== 'changename' && ($btn['type'] !== 'heading') && !empty($btn['conditional_parent']) ) {
					if( !empty( $_POST[$btn['cow']] ) ) {
						foreach( $options['buttons'] as $btn2 ) {

							if( !empty($btn2['chosen_valt']) && !empty($btn2['conditional_parent_use']) && !empty($btn2['conditional_tie']) && $btn2['type'] !== 'changename' && ($btn2['type'] !== 'heading') && empty($btn2['conditional_parent']) ) {
								if( sanitize_text_field( $_POST[''.$btn['cow'].''] ) != $btn2['chosen_valt'] ) {
									if( empty($_POST[''.$btn2['cow'].'']) ) {
										foreach( $notice['error'] as $position => $value ) {

											if( strip_tags($value) == sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), wooccm_wpml_string($btn2['label']) ) ) {
												unset($notice['error'][$position]);
											}

										}
									}
								} 
							}
						}

					} else {
						foreach( $notice['error'] as $position => $value ) {

							if( strip_tags($value) == sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), wooccm_wpml_string($btn2['label']) ) ) {
								unset($notice['error'][$position]);
							}

						}
					}
				}

			}
		}
		$inc--;

	}

	$options = get_option( 'wccs_settings' );

	global $woocommerce;

	if( !empty( $options['buttons'] ) ) {
		foreach( $options['buttons'] as $btn ) {

			if( !empty($btn['chosen_valt']) && !empty($btn['conditional_parent_use']) && !empty($btn['conditional_tie']) && $btn['type'] !== 'changename' && ($btn['type'] !== 'heading') && !empty($btn['conditional_parent']) ) {

				if( !empty( $_POST[$btn['cow']] ) ) {

					foreach( $options['buttons'] as $btn2 ) {

						if( !empty($btn2['chosen_valt']) && !empty($btn2['conditional_parent_use']) && !empty($btn2['conditional_tie']) && $btn2['type'] !== 'changename' && ($btn2['type'] !== 'heading') && empty($btn2['conditional_parent']) ) {
							if( sanitize_text_field( $_POST[''.$btn['cow'].''] ) != $btn2['chosen_valt'] ) {
								if( empty( $_POST[''.$btn2['cow'].''] ) ) {
									foreach( $notice['error'] as $position => $value ) {
										if( strip_tags($value) == sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), wooccm_wpml_string($btn2['label']) ) ) {
											unset($notice['error'][$position]);
										}
									}
								}
							} 
						}

					}

				} else {

					foreach( $notice['error'] as $position => $value ) {

						if( strip_tags($value) == sprintf( __( '%s is a required field.', 'woocommerce-checkout-manager' ), wooccm_wpml_string($btn2['label']) ) ) {
							unset($notice['error'][$position]);
						}

					}
				}

			}

		}
	}

	WC()->session->set( 'wc_notices', $notice );

}
add_action('woocommerce_after_checkout_validation', 'wooccm_remove_notices_conditional');
?>