<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

// add custom column headers
function wooccm_csv_export_modify_column_headers( $column_headers ) {

	$new_headers = array();
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
					$new_headers['_'.$name.'_'.$btn['cow'].''] = ''.wooccm_wpml_string($btn['label']).'';
				}
			}
		}
		$inc--;
	}

	$options = get_option( 'wccs_settings' );
	if( !empty( $options['buttons'] ) ) {
		foreach( $options['buttons'] as $btn ) {
			$new_headers[''.$btn['cow'].''] = ''.wooccm_wpml_string($btn['label']).'';
		}
	}

	return array_merge( $column_headers, $new_headers  );

}

// set the data for each for custom columns
function wooccm_csv_export_modify_row_data( $order_data, $order, $csv_generator ) {
 
	$custom_data = array();

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

					if( get_post_meta( $order->id, '_'.$name.'_'.$btn['cow'].'', true ) && (($btn['type'] !== 'multiselect') || ($btn['type'] !== 'multicheckbox')) && ($btn['type'] !== 'heading') ) {
						$custom_data['_'.$name.'_'.$btn['cow'].''] = get_post_meta( $order->id, '_'.$name.'_'.$btn['cow'].'', true );
					}

					if( get_post_meta( $order->id, '_'.$name.'_'.$btn['cow'].'', true )  && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' ) {
						$custom_data['_'.$name.'_'.$btn['cow']] = '';
						$value = get_post_meta( $order->id , '_'.$name.'_'.$btn['cow'], true );
						$strings = maybe_unserialize( $value );
						if( !empty( $strings ) ) {
							if( is_array( $strings ) ) {
								$iww = 0;
								$len = count($strings);
								foreach( $strings as $key ) {
									if ( $iww == $len - 1) {
										$custom_data['_'.$name.'_'.$btn['cow']] .= $key;
									} else {
										$custom_data['_'.$name.'_'.$btn['cow']] .= $key.', ';
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

				}

			}
		}
		$inc--;
	}

	$options = get_option( 'wccs_settings' );
	if( !empty( $options['buttons'] ) ) {
		foreach( $options['buttons'] as $btn ) {

			if( get_post_meta( $order->id, ''.$btn['cow'].'', true ) && (($btn['type'] !== 'multiselect') || ($btn['type'] !== 'multicheckbox')) && ($btn['type'] !== 'heading') ) {
				$custom_data[''.$btn['cow'].''] = get_post_meta( $order->id, ''.$btn['cow'].'', true );
			}

			if( get_post_meta( $order->id, $btn['cow'], true )  && (($btn['type'] == 'multiselect') || ($btn['type'] == 'multicheckbox')) && ($btn['type'] !== 'heading') && $btn['type'] !== 'wooccmupload' ) {
				$custom_data[''.$btn['cow'].''] = '';
				$value = get_post_meta( $order->id , ''.$btn['cow'], true );
				$strings = maybe_unserialize( $value );	  
				if( !empty( $strings ) ) {
					if( is_array( $strings ) ) {
						$iww = 0;
						$len = count($strings);
						foreach( $strings as $key ) {
							if( $iww == $len - 1) {
								$custom_data[$btn['cow']] .= $key;
							} else {
								$custom_data[$btn['cow']] .= $key.', ';
							}
							$iww++;
						}
						echo $strings;
					}
				} else {
					echo '-';
				}
			}

		}
	}

	// defaults set back
	$new_order_data = array();

	if( isset( $csv_generator->order_format ) && ( 'default_one_row_per_item' == $csv_generator->order_format || 'legacy_one_row_per_item' == $csv_generator->order_format ) ) {
		if( !empty( $order_data ) ) {
			foreach( $order_data as $data ) {
				$new_order_data[] = array_merge( (array) $data, $custom_data );
			}
		}
	} else {
		$new_order_data = array_merge( $order_data, $custom_data );
	}

	return $new_order_data;

}

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wooccm_additional_gen( $tab, $abbr, $section, $wooname = '' ) {

	global $woocommerce, $wpdb;

	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' ); // shipping
	$options3 = get_option( 'wccs_settings3' ); // billing

	$args = array(
		'post_type' => 'shop_order',
		'posts_per_page' 	=> -1,
		'post_status' => array( 'wc-processing', 'wc-completed' )
	);

	$loop = new WP_Query( $args );
	$csv_output = '';

	$optionname = false;
	switch( $wooname ) {

		case 'additional':
			$optionname = $options['buttons'];
			break;

		case 'shipping':
			$optionname = $options2['shipping_buttons'];
			break;

		case 'billing':
			$optionname = $options3['billing_buttons'];
			break;

	}

	if( !empty($abbr) && $section == 1 ) {
		if( $tab == $wooname ) {

			while( $loop->have_posts() ) {
				$loop->the_post();
				$order_id = $loop->post->ID;
				$order = new WC_Order( $order_id );
				if( get_post_meta($order_id, $abbr, true) ) {
					$csv_output .= '["'.$order->billing_first_name.' '.$order->billing_last_name.'", "'.get_post_meta($order_id, $abbr, true).'" ], ';
				}
			}

		} elseif( $tab == 'heading' ) {

			$csv_output .= '["Name","'.$abbr.'"]';

		}
	} elseif( empty($abbr) && $section == 2 ) {
		if( $tab == $wooname ) {

			$listida = array();
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$order_id = $loop->post->ID;
				$order = new WC_Order( $order_id );
				if( !empty( $optionname ) ) {
					foreach( $optionname as $name ) {
						if ( get_post_meta($order_id, $name['cow'], true) ) {
							$listida[] = $order_id;	
						}
					}
				}
			}
			$csv_output = array_unique( $listida );

		} elseif( $tab == 'heading' ) {

			$lista = array();
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$order_id = $loop->post->ID;
				$order = new WC_Order( $order_id );
				if( !empty( $optionname ) ) {
					foreach( $optionname as $n ) {
						if( get_post_meta($order_id, $n['cow'], true) ) {	
								$lista[] = $n['label'];
						}
					}
				}
			}
			$csv_output = array_unique( $lista );

		}
	}
	return $csv_output;

}

/**
* Converting data to CSV [ SETTINGS DATA ]
*/
function wooccm_generate_csv( $tab = '' ) {

	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
	$options3 = get_option( 'wccs_settings3' );

	$csv_output = '';
	switch( $tab ) {

		case 'additional':
			if ( !empty($options['buttons']) ) {
				$total = count($options['buttons']) - 1;
				foreach( $options['buttons'] as $i => $btn ) {
					if( $i != 999 && !empty($btn['cow']) ) {
						$csv_output .= '[';
						foreach( $btn as $n => $dataw ) {
							$csv_output .= '"'.$dataw.'",';
						}
						if( $i != $total ) {
							$csv_output .= '], ';
						} else {
							$csv_output .= ']';   
						}
					}
				}
			}
			break;

		case 'billing':
			if( !empty($options3['billing_buttons']) ) {
				$total = count($options3['billing_buttons']) - 1;
				foreach( $options3['billing_buttons'] as $i => $btn ) {
					if( $i != 999 && !empty($btn['cow']) ) {
						$csv_output .= '[';
						foreach( $btn as $n => $dataw ) {
							$csv_output .= '"'.$dataw.'",';
						}
						if( $i != $total ) {
							$csv_output .= '], ';
						} else {
							$csv_output .= ']';
						}
					}
				}
			}
			break;

		case 'shipping':
			if( !empty($options2['shipping_buttons']) ) {
				$total = count($options2['shipping_buttons']) -1;
				foreach( $options2['shipping_buttons'] as $i => $btn) {
					if( $i != 999 && !empty($btn['cow']) ) {
						$csv_output .= '[';
						foreach($btn as $n => $dataw) {
							$csv_output .= '"'.$dataw.'",';	
						}
						if( $i != $total ) {
							$csv_output .= '], ';
						} else {
							$csv_output .= ']';   
						}
					}
				}
			}
			break;

		case 'general':
			if( !empty($options['checkness']) ) {
				$csv_output .= '[';
				foreach( $options['checkness'] as $i => $btn ) {
					$csv_output .= '"'.$btn.'",';
				}
				$csv_output .= ']'; 
			}
			break;

		case 'heading':
			if( !empty($options3['billing_buttons']) ) {
				$csv_output .= '[';
				foreach( $options3['billing_buttons'][0] as $n => $dataw) {
					$csv_output .= '"'.$n.'",';
				}
				$csv_output .= ']';   
			}
			break;

		case 'heading2':
			if( !empty($options['checkness']) ) {
				$csv_output .= '[';
				foreach( $options['checkness'] as $n => $btn) {
					$csv_output .= '"'.$n.'",';
				}
				$csv_output .= ']'; 
			}
			break;

		case 'heading3':
			if( !empty($options['buttons']) ) {
				$csv_output .= '[';
				foreach( $options['buttons'][0] as $n => $dataw) {
					$csv_output .= '"'.$n.'",';
				}
				$csv_output .= ']';  
			}
			break;

	}
	return $csv_output;

}
// --------------- END SETTINGS DATA ----------------

function wooccm_csvall_heading( $heading ) {

	$csv_output = '';
	$csv_output .= '["Name", ';
	foreach($heading as $data ){
		$csv_output .= '"'.$data.'", ';
	}
	$csv_output .= ']';
	return $csv_output;

}

function wooccm_csvall_info( $orderids, $wooname = '' ){

	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
	$options3 = get_option( 'wccs_settings3' );

	$csv_output = '';
	if( !empty( $orderids ) ) {
		foreach( $orderids as $order_id ) {
			$csv_output .= '["'.get_post_meta($order_id, '_billing_first_name', true).' '.get_post_meta($order_id, '_billing_last_name', true).'", ';
			switch( $wooname ) {

				case 'additional':
					if( !empty( $options['buttons'] ) ) {
						foreach( $options['buttons'] as $name2 ) {
							$csv_output .= '"'.get_post_meta($order_id, $name2['cow'], true).'", ';
						}
					}
					break;

				case 'billing':
					if( !empty( $options3['billing_buttons'] ) ) {
						foreach( $options3['billing_buttons'] as $name2 ) {
							$csv_output .= '"'.get_post_meta($order_id, $name2['cow'], true).'", ';
						}
					}
					break;

				case 'shipping':
					if( !empty( $options2['shipping_buttons'] ) ) {
						foreach( $options2['shipping_buttons'] as $name2 ) {
							$csv_output .= '"'.get_post_meta($order_id, $name2['cow'], true).'", ';
						}
					}
					break;

			}
			$csv_output .= '], ';
		}
	}
	return $csv_output;

}


function wooccm_advance_export(){

	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
	$options3 = get_option( 'wccs_settings3' );

	$single_download = ( isset( $_POST['single-download'] ) ? sanitize_text_field( $_POST['single-download'] ) : false );
	if( !empty( $single_download ) ) {
		switch( $single_download ) {

			case 'additional':
				$csv = wooccm_additional_gen('additional', ( isset( $_POST['selectedval'] ) ? sanitize_text_field( $_POST['selectedval'] ) : false ), 1, 'additional' );
				$heading = wooccm_additional_gen('heading', ( isset( $_POST['selectedval'] ) ? sanitize_text_field( $_POST['selectedval'] ) : false ), 1, 'additional' );	
				break;

			case 'shipping':
				$csv = wooccm_additional_gen('shipping', ( isset( $_POST['shippingselectedval'] ) ? sanitize_text_field( $_POST['shippingselectedval'] ) : false ), 1, 'shipping');
				$heading = wooccm_additional_gen('heading', ( isset( $_POST['shippingselectedval'] ) ? sanitize_text_field( $_POST['shippingselectedval'] ) : false ), 1, 'shipping' );	
				break;

			case 'billing':
				$csv = wooccm_additional_gen('billing', ( isset( $_POST['billingselectedval'] ) ? $_POST['billingselectedval'] : false ), 1, 'billing' );
				$heading = wooccm_additional_gen('heading', ( isset( $_POST['billingselectedval'] ) ? $_POST['billingselectedval'] : false ), 1, 'billing' );	
				break;

		}
?>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		var A = [<?php echo $heading.','.$csv; ?>];  // initialize array of rows with header row as 1st item

		var csvRows = [];
		for(var i=0, l=A.length; i<l; ++i){ // for each array( [..] ), join with commas for csv
		for (index = 0; index < A[i].length; ++index) {
		    A[i][index] = '"'+A[i][index]+'"'; // add back quotes for each string, to store special characters and commas
		}
		    csvRows.push( A[i] );   // put data in a java useable array
		}

		var csvString = csvRows.join("\n"); // make rows for each array

		var a = document.createElement('a');

		a.href     = 'data:attachment/csv,' + encodeURIComponent(csvString);
		a.target   = '_blank';
		a.download = 'only_additional_fieldname.csv';
		document.body.appendChild(a);
		a.click();

	});
</script>

<?php
	}

	// ----------- ALL DOWNLOAD ---------
	$all_download = ( isset( $_POST['all-download'] ) ? sanitize_text_field( $_POST['all-download'] ) : false );
	if( !empty( $all_download ) ) {

		$abbr = '';
		switch( $all_download ) {

			case 'additional':
				$csv = wooccm_additional_gen('additional', $abbr, 2);
				$csv = wooccm_csvall_info($csv, 'additional' );
				$heading = wooccm_additional_gen('heading', $abbr, 2);	
				$heading = wooccm_csvall_heading($heading);
				break;

			case 'shipping':
				$csv = wooccm_additional_gen('additional', $abbr, 2, 'shipping' );
				$csv = wooccm_csvall_info($csv, 'shipping' );
				$heading = wooccm_additional_gen('heading', $abbr, 2, 'shipping');	
				$heading = wooccm_csvall_heading($heading);
				break;

			case 'billing':
				$csv = wooccm_additional_gen('billing', $abbr, 2, 'billing' );
				$csv = wooccm_csvall_info($csv, 'billing' );
				$heading = wooccm_additional_gen('heading', $abbr, 2, 'billing' );	
				$heading = wooccm_csvall_heading($heading);
				break;

		}
?>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		var A = [<?php echo $heading.','.$csv; ?>];  // initialize array of rows with header row as 1st item

		var csvRows = [];
		for(var i=0, l=A.length; i<l; ++i){ // for each array( [..] ), join with commas for csv
		for (index = 0; index < A[i].length; ++index) {
		    A[i][index] = '"'+A[i][index]+'"'; // add back quotes for each string, to store special characters and commas
		}
		    csvRows.push( A[i] );   // put data in a java useable array
		}

		var csvString = csvRows.join("\n"); // make rows for each array

		var a = document.createElement('a');

		a.href     = 'data:attachment/csv,' + encodeURIComponent(csvString);
		a.target   = '_blank';
		a.download = 'only_additional_fieldname.csv';
		document.body.appendChild(a);
		a.click();

	});
</script>

<?php
	}
	// ---------- END ALL DOWNLOAD --------------

	// ---------- SETTING DOWNLOAD --------------
	$setting_download = ( isset( $_POST['setting-download'] ) ? sanitize_text_field( $_POST['setting-download'] ) : false );
	if( !empty( $setting_download ) ) {
		switch( $setting_download ) {

			case 'additional':
				$csv = wooccm_generate_csv('additional');
				$heading = wooccm_generate_csv('heading3');
				break;

			case 'billing':
				$csv = wooccm_generate_csv('billing');
				$heading = wooccm_generate_csv('heading');
				break;

			case 'shipping':
				$csv = wooccm_generate_csv('shipping');
				$heading = wooccm_generate_csv('heading');
				break;

			case 'general':
				$csv = wooccm_generate_csv('general');
				$heading = wooccm_generate_csv('heading2');
				break;

		}
?>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		var A = [<?php echo $heading.','.$csv; ?>];  // initialize array of rows with header row as 1st item

		var csvRows = [];
		for(var i=0, l=A.length; i<l; ++i){ // for each array( [..] ), join with commas for csv
		for (index = 0; index < A[i].length; ++index) {
		    A[i][index] = '"'+A[i][index]+'"'; // add back quotes for each string, to store special characters and commas
		}
		    csvRows.push( A[i] );   // put data in a java useable array
		}

		var csvString = csvRows.join("\n"); // make rows for each array

		var a = document.createElement('a');

		a.href     = 'data:attachment/csv,' + encodeURIComponent(csvString);
		a.target   = '_blank';
		a.download = 'only_additional_fieldname.csv';
		document.body.appendChild(a);
		a.click();

	});
</script>

<?php
	}
	// ---------------- END SETTING DOWNLOAD --------------
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(function () {

			jQuery(".button.single-download.additional").click(function() {
				jQuery("input[name=single-download]").val("additional");
				jQuery("#additional_export").submit();
			});
			
			jQuery(".button.all-download.additional").click(function() {
				jQuery("input[name=all-download]").val("additional");
				jQuery("#additional_export").submit();
			});
			
			jQuery(".button.setting-download.additional").click(function() {
				jQuery("input[name=setting-download]").val("additional");
				jQuery("#additional_export").submit();
			});

			<!-- shipping -->
			jQuery(".button.single-download.shipping").click(function() {
				jQuery("input[name=single-download]").val("shipping");
				jQuery("#additional_export").submit();
			});
			
			jQuery(".button.all-download.shipping").click(function() {
				jQuery("input[name=all-download]").val("shipping");
				jQuery("#additional_export").submit();
			});
			jQuery(".button.setting-download.additional").click(function() {
				jQuery("input[name=setting-download]").val("shipping");
				jQuery("#additional_export").submit();
			});
			<!-- end shipping -->

			<!-- billing -->
			jQuery(".button.single-download.billing").click(function() {
				jQuery("input[name=single-download]").val("billing");
				jQuery("#additional_export").submit();
			});
			
			jQuery(".button.all-download.billing").click(function() {
				jQuery("input[name=all-download]").val("billing");
				jQuery("#additional_export").submit();
			});
			
			jQuery(".button.setting-download.additional").click(function() {
				jQuery("input[name=setting-download]").val("billing");
				jQuery("#additional_export").submit();
			});
			<!-- end billing -->

		});
		
	});
</script>

<h2><?php _e( 'WooCommerce Checkout Manager', 'woocommerce-checkout-manager' ); ?></h2>
<div class="wrap">

	<div id="content">

		<h2 class="nav-tab-wrapper add_tip_wrap">
			<a class="nav-tab general-tab nav-tab-active"><?php _e( 'Export', 'woocommerce-checkout-manager' ); ?></a>
			<a class="nav-tab star" href="https://wordpress.org/support/view/plugin-reviews/woocommerce-checkout-manager?filter=5" target="_blank">
				<div id="star-five" title="<?php _e('Like the plugin? Rate it! On WordPress.org', 'woocommerce-checkout-manager' ); ?>">
					<div class="star-rating">
						<div class="star star-full"></div>
						<div class="star star-full"></div>
						<div class="star star-full"></div>
						<div class="star star-full"></div>
						<div class="star star-full"></div>
					</div>
					<!-- .star-rating -->
				</div>
				<!-- #star-five -->
			</a>
		</h2>
		<!-- .nav-tab-wrapper -->

		<ul class="subsubsub">
			<li><a href="#billing-fields-section"><?php _e( 'Billing Fields', 'woocommerce-checkout-manager' ); ?></a> |</li>
			<li><a href="#shipping-fields-section"><?php _e( 'Shipping Fields', 'woocommerce-checkout-manager' ); ?></a> |</li>
			<li><a href="#additional-fields-section"><?php _e( 'Additional Fields', 'woocommerce-checkout-manager' ); ?></a></li>
		</ul>
		<!-- .subsubsub -->
		<br class="clear">

		<h3><?php _e( 'Field Data Export', 'woocommerce-checkout-manager'); ?></h3>

		<div id="welcome-panel" class="welcome-panel heading">

			<form name="additionalexport" method="post" action="" id="additional_export">

				<input type="hidden" name="single-download" val="" />
				<input type="hidden" name="all-download" val="" />
				<input type="hidden" name="setting-download" val="" />

				<div id="welcome-panel" class="welcome-panel left billing">

					<!-- BILLING SECTION -->

					<p id="billing-fields-section" class="about-description heading"><?php _e( 'Billing Fields Section', 'woocommerce-checkout-manager'); ?></p>
					<hr />

<?php if( !empty( $options3['billing_buttons'] ) ) { ?>
					<div class="welcome-panel-content">

						<p class="about-description inner"><?php _e( 'Export All Orders with abbreviation name : ', 'woocommerce-checkout-manager'); ?>
							<select name="billingselectedval">
	<?php foreach( $options3['billing_buttons'] as $name ) { ?>
								<option value="<?php echo $name['cow']; ?>"><?php echo $name['cow']; ?></option>
	<?php } ?>
							</select>
						</p>
						<!-- .about-description inner -->

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero single-download billing" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
									</li>
								</ul>
							</div>
						</div>
						<!-- .welcome-panel-column-container -->
<?php } ?>
				
						<div class="sheet"></div>
						<p style="clear:both;" class="about-description inner"><?php _e( 'Export All Orders', 'woocommerce-checkout-manager'); ?></p>

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero all-download billing" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>	
									</li>
								</ul>
							</div>
						</div>
						<!-- .welcome-panel-column-container -->

						<div class="sheet"></div>
						<p style="clear:both;" class="about-description inner"><?php _e( 'Export Settings', 'woocommerce-checkout-manager'); ?></p>

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero setting-download billing" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
									</li>
								</ul>  
							</div>
						</div>
						<!-- .welcome-panel-column-container -->

					</div>
					<!-- .welcome-panel-content -->
				</div>
				<!-- #welcome-panel -->

				<!-- END BILLING SECTION -->

				<div id="welcome-panel" class="welcome-panel left shipping">

					<!-- SHIPPING SECTION -->

					<p id="shipping-fields-section" class="about-description heading"><?php _e( 'Shipping Fields Section', 'woocommerce-checkout-manager'); ?></p>
					<hr />

<?php if( !empty( $options2['shipping_buttons'] ) ) { ?>
					<div class="welcome-panel-content">

						<p class="about-description inner"><?php _e( 'Export All Orders with abbreviation name : ', 'woocommerce-checkout-manager'); ?>
							<select name="shippingselectedval">
	<?php foreach( $options2['shipping_buttons'] as $name ) { ?>
								<option value="<?php echo $name['cow']; ?>"><?php echo $name['cow']; ?></option>
	<?php } ?>
							</select>
						</p>
						<!-- .about-description inner -->

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero single-download shipping" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
									</li>
								</ul>
							</div>
						</div>
						<!-- .welcome-panel-column-container -->
<?php } ?>

						<div class="sheet"></div>
						<p style="clear:both;" class="about-description inner"><?php _e( 'Export All Orders', 'woocommerce-checkout-manager'); ?></p>

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero all-download shipping" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>	
									</li>
								</ul>
							</div>
						</div>
						<!-- .welcome-panel-column-container -->

						<div class="sheet"></div>
						<p style="clear:both;" class="about-description inner"><?php _e( 'Export Settings', 'woocommerce-checkout-manager'); ?></p>

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero setting-download shipping" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
									</li>
								</ul>
							</div>
						</div>
						<!-- .welcome-panel-column-container -->

					</div>
					<!-- .welcome-panel-content -->

				</div>
				<!-- #welcome-panel -->

			<!-- END SHIPPING SECTION -->

<?php if( !empty( $options['buttons'] ) ) { ?>
				<div id="welcome-panel" class="welcome-panel left">

					<!-- ADDITIONAL SECTION --> 
					<p id="additional-fields-section" class="about-description heading"><?php _e( 'Additional Fields Section', 'woocommerce-checkout-manager'); ?></p>
					<hr />
					<div class="welcome-panel-content">

						<p class="about-description inner"><?php _e( 'Export All Orders with abbreviation name : ', 'woocommerce-checkout-manager'); ?>
							<select name="selectedval">
	<?php foreach( $options['buttons'] as $name ) { ?>
								<option value="<?php echo $name['cow']; ?>"><?php echo $name['cow']; ?></option>
	<?php } ?>
							</select>
						</p>
						<!-- .about-description inner -->

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero single-download additional" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
									</li>
								</ul>
							</div>
						</div>
						<!-- .welcome-panel-column-container -->
<?php } ?>

						<div class="sheet"></div>
						<p style="clear:both;" class="about-description inner"><?php _e( 'Export All Orders', 'woocommerce-checkout-manager'); ?></p>

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero all-download additional" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>	
									</li>
								</ul>
							</div>
						</div>
						<!-- .welcome-panel-column-container -->

						<div class="sheet"></div>
						<p style="clear:both;" class="about-description inner"><?php _e( 'Export Settings', 'woocommerce-checkout-manager'); ?></p>

						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<ul>
									<li>
										<a class="button button-primary button-hero setting-download additional" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
									</li>
								</ul>
							</div>
						</div>
						<!-- .welcome-panel-column-container -->

					</div>
					<!-- .welcome-panel-content -->

					<!-- // END ADDITIONAL SECTION -->

				</div>
				<!-- #welcome-panel -->

			</form>
			<!-- #additional_export -->

		</div>
		<!-- #welcome-panel -->

	</div>
	<!-- #content -->

</div>
<!-- .wrap -->
<?php
}
?>