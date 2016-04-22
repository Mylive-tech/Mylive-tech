<?php
/**
 * WooCommerce Checkout Manager 
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wooccm_billing_hide_required() {

	global $woocommerce;

	$options = get_option( 'wccs_settings3' );

	$billing = array(
		'address_1',
		'address_2',
		'city',
		'state',
		'postcode'
	);

	if( !empty( $options['billing_buttons'] ) ) {
		echo '
<style>
';
		foreach( $options['billing_buttons'] as $btn ) {
			if( in_array($btn['cow'], $billing) && empty($btn['checkbox']) ) {
				echo '
p#billing_'.$btn['cow'].'_field .required{
	display:none;
}';
			}
		}
		echo '
</style>';
	}

}

function wooccm_billing_scripts() {

	global $woocommerce;

	$options = get_option( 'wccs_settings3' );

	$saved = WC()->session->get( 'wooccm_retain', array() );

	// Check if we have any buttons
	if( empty($options['billing_buttons']) )
		return;

	foreach( $options['billing_buttons'] as $btn ) {

		if( $btn['type'] == 'datepicker' ) {
			echo '
<script type="text/javascript">
jQuery(document).ready(function() {
	var today = new Date();
	if( jQuery.isFunction(jQuery.fn.datepicker) ) {
		jQuery("input#billing_'.$btn['cow'].'").datepicker({
';
			if( empty($btn['format_date']) ) {
				echo 'dateFormat : "dd-mm-yy",'; 
			}
			if( !empty($btn['format_date']) ) {
				echo 'dateFormat : "'.str_replace( ' ', '', $btn['format_date'] ).'",'; 
			}
			if( !empty($btn['single_yy']) ) {
				echo 'minDate: new Date( '.$btn['single_yy'].', '.$btn['single_mm'].' - 1, '.$btn['single_dd'].'),';
			}
			if( !empty($btn['min_before']) ) {
				echo 'minDate: '.$btn['min_before'].',';
			}
			if( !empty($btn['single_max_yy']) ) {
				echo 'maxDate: new Date( '.$btn['single_max_yy'].', '.$btn['single_max_mm'].' - 1, '.$btn['single_max_dd'].'),';
			}
			if( !empty($btn['max_after']) ) {
				echo 'maxDate: '.$btn['max_after'].',';
			}
			if( !empty($btn['days_disabler']) ) {
				echo 'beforeShowDay: function(date) { var day = date.getDay(); return [(';
				if( !empty($btn['days_disabler0']) ) {
					echo 'day == 0';
				} else { echo 'day == "x"'; }
				if( !empty($btn['days_disabler1']) ) {
					echo ' || day == 1';
				}
				if( !empty($btn['days_disabler2']) ) {
					echo ' || day == 2';
				}
				if( !empty($btn['days_disabler3']) ) {
					echo ' || day == 3';
				}
				if( !empty($btn['days_disabler4']) ) {
					echo ' || day == 4';
				}
				if( !empty($btn['days_disabler5']) ) {
					echo ' || day == 5';
				}
				if( !empty($btn['days_disabler6']) ) {
					echo '|| day == 6';
				}
				echo ')]; }';
			}
			echo '
		});
	}
});
</script>
';
		}

		if( $btn['type'] == 'time' ) {
			$args = '
			showPeriod: true,';
			if( !empty($btn['start_hour']) ) {
				$args .= '
			hours: { starts: '.$btn['start_hour'].', ends: '.$btn['end_hour'].' },';
			}
			if( !empty($btn['interval_min']) ) {
				$args .= '
			minutes: {interval: '.$btn['interval_min'].', manual: ['.$btn['manual_min'].'] },';
			}
			$args .= '
			showLeadingZero: true';
			$args = apply_filters( 'wooccm_timepicker_jquery_args', $args, $btn );
			echo '
<!-- Billing section: TimePicker -->
<script type="text/javascript">
jQuery(document).ready(function() {
	if( jQuery.isFunction(jQuery.fn.timepicker) ) {
		jQuery("#billing_'.$btn['cow'].'_field input#billing_'.$btn['cow'].'").timepicker({
';
			echo $args;
			echo '
		});
	}
});
</script>
';
		}

		if ( $btn['type'] == 'password' ) {
			echo '
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("p#billing_'.$btn['cow'].'_field").css("display");
});
</script>
';
		}

		if ( $btn['type'] == 'colorpicker' && $btn['colorpickertype'] == 'farbtastic' ) { ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	jQuery('#billing_<?php echo $btn['cow']; ?>_colorpickerdiv').hide();
	jQuery('#billing_<?php echo $btn['cow']; ?>_colorpickerdiv').farbtastic("#billing_<?php echo $btn['cow']; ?>_colorpicker");
	jQuery("#billing_<?php echo $btn['cow']; ?>_colorpicker").click(function(){jQuery('#billing_<?php echo $btn['cow']; ?>_colorpickerdiv').slideToggle()});
});
</script>
<?php
		}

		if ( $btn['type'] == 'colorpicker' && $btn['colorpickertype'] == 'iris' ) {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {

	$( '#<?php echo $btn['cow']; ?>_colorpicker' ).css( 'color', '#fff' );
	$( '#<?php echo $btn['cow']; ?>_colorpicker' ).css( 'background', '<?php echo $btn['colorpickerd']; ?>' );

	var options = {
		wccmclass: "billing_<?php echo $btn['cow']; ?>_colorpickerdiv",
		palettes: true,
		color: "",
		hide: false,
		change: function(event,ui){
			$( '#billing_<?php echo $btn['cow']; ?>_colorpicker' ).css( 'color', '#000' );
			$( '#billing_<?php echo $btn['cow']; ?>_colorpicker' ).css( 'background', ui.color.toString());
		}
	};

	jQuery('#billing_<?php echo $btn['cow']; ?>_colorpicker').iris( options );
	jQuery('.billing_<?php echo $btn['cow']; ?>_colorpickerdiv').hide();
	jQuery("#billing_<?php echo $btn['cow']; ?>_colorpicker").click(function(){jQuery('.billing_<?php echo $btn['cow']; ?>_colorpickerdiv').slideToggle()});

});
</script>
<?php
		}

// ============================== radio button & checkbox ===========================================

		if ( ($btn['type'] == 'wooccmradio' || $btn['type'] == 'checkbox_wccm') && !empty( $btn['tax_remove'] ) ) {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {

<?php if( !empty($saved['wooccm_tax_save_method']) ) { ?>
	jQuery('#billing_<?php echo $btn['cow']; ?>_field input[name=billing_<?php echo $btn['cow']; ?>]').prop("checked", true);
<?php } ?>

	jQuery('#billing_<?php echo $btn['cow']; ?>_field input').click(function() {

		$( 'form.checkout' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

		var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
		data = {
			action: 'remove_tax_wccm',
			tax_remove_aj: jQuery('#billing_<?php echo $btn['cow']; ?>_field input[name=billing_<?php echo $btn['cow']; ?>]:checked').val()     
		};

		jQuery.post(ajaxurl, data, function(response) {
			$( 'body' ).trigger( 'update_checkout' );
			jQuery('form.checkout').unblock();
		});

	});
});
</script>
<?php
		}

		if ( ($btn['type'] == 'wooccmradio' || $btn['type'] == 'checkbox_wccm') && !empty( $btn['add_amount'] ) && !empty( $btn['fee_name'] ) && !empty( $btn['add_amount_field'] ) ) {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {

<?php if( !empty($saved['wooccm_addamount453user']) ) { ?>
	jQuery('#billing_<?php echo $btn['cow']; ?>_field input[name=billing_<?php echo $btn['cow']; ?>]').prop("checked", true);
<?php } ?>
	jQuery('#billing_<?php echo $btn['cow']; ?>_field input').click(function() {

		$( 'form.checkout' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

		var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
		data = {
			action: 'remove_tax_wccm',
			add_amount_aj: jQuery('#billing_<?php echo $btn['cow']; ?>_field input[name=billing_<?php echo $btn['cow']; ?>]:checked').val()
		};

		jQuery.post(ajaxurl, data, function(response) {
			$( 'body' ).trigger( 'update_checkout' );
			jQuery('form.checkout').unblock();
		});

	});
});
</script>
<?php
		}

// =========================================== select options =========================================

		if ( ($btn['type'] == 'wooccmselect') && !empty( $btn['tax_remove'] ) ) {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {

<?php if( !empty($saved['wooccm_tax_save_method']) ) { ?>                                
	jQuery('#billing_<?php echo $btn['cow']; ?>_field select').val( '<?php echo $saved['wooccm_tax_save_method']; ?>' );
<?php } ?>
	jQuery('#billing_<?php echo $btn['cow']; ?>_field select').change(function() {

		$( 'form.checkout' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

		var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
		data = {
			action: 'remove_tax_wccm',
			tax_remove_aj: jQuery('#billing_<?php echo $btn['cow']; ?> option:selected').val()     
		};

		jQuery.post(ajaxurl, data, function(response) {
			$( 'body' ).trigger( 'update_checkout' );
			jQuery('form.checkout').unblock();	
		});

	});
});
</script>
<?php
		}

		if ( ($btn['type'] == 'wooccmselect') && !empty( $btn['add_amount'] ) && !empty( $btn['fee_name'] ) && !empty( $btn['add_amount_field'] ) ) {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {

<?php if( !empty($saved['wooccm_addamount453user']) ) { ?>                                
	jQuery('#billing_<?php echo $btn['cow']; ?>_field select').val( '<?php echo $saved['wooccm_addamount453user']; ?>' );
<?php } ?>

	jQuery('#billing_<?php echo $btn['cow']; ?>_field select').change(function() {

		$( 'form.checkout' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

		var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
		data = {
			action: 'remove_tax_wccm',
			add_amount_aj: jQuery('#billing_<?php echo $btn['cow']; ?> option:selected').val()
		};

		jQuery.post(ajaxurl, data, function(response) {
			$( 'body' ).trigger( 'update_checkout' );
			jQuery('form.checkout').unblock();	
		});

	});
});
</script>
<?php
		}

// =========================================== add apply button ==========================================

		if ( ($btn['type'] == 'text') && !empty( $btn['add_amount'] ) && !empty( $btn['fee_name'] ) && empty( $btn['add_amount_field'] ) ) {
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery( "#billing_<?php echo $btn['cow']; ?>_field" ).append( '<span id="billing_<?php echo $btn['cow']; ?>_applynow"><?php _e('Apply','woocommerce-checkout-manager'); ?></span>' );
});

jQuery(document).ready(function($) {

<?php if( !empty($saved['wooccm_addamount453userf']) ) { ?>                                
	jQuery('input#billing_<?php echo $btn['cow']; ?>').val( '<?php echo $saved['wooccm_addamount453userf']; ?>' );
<?php } ?>

	jQuery('#billing_<?php echo $btn['cow']; ?>_field #billing_<?php echo $btn['cow']; ?>_applynow').click(function() {

		$( 'form.checkout' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

		var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
		data = {
			action: 'remove_tax_wccm',
			add_amount_faj: jQuery('input#billing_<?php echo $btn['cow']; ?>').val()     
		};

		jQuery.post(ajaxurl, data, function(response) {
			$( 'body' ).trigger( 'update_checkout' );
			jQuery('form.checkout').unblock();	
		});

	});

});
</script>
<?php
		}

// =====================================================

		if ( $btn['type'] == 'checkbox_wccm' ) {
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#billing_<?php echo $btn['cow']; ?>_checkbox').change(function(){
		if(jQuery(this).is(':checked')){
			jQuery("#billing_<?php echo $btn['cow']; ?>_checkboxhiddenfield").prop("disabled", true);
		}else{
			jQuery("#billing_<?php echo $btn['cow']; ?>_checkboxhiddenfield").prop("disabled", false);
		}
	});
});
</script>
<?php
		}

	}

}

// --------------------------------------------------------
// --------------------------------------------------------
// --------------------------------------------------------

function wooccm_billing_override_this() {

	global $woocommerce;

	$options = get_option( 'wccs_settings3' );

	$options['billing_buttons'] = ( isset( $options['billing_buttons'] ) ? $options['billing_buttons'] : array() );

	// Check if there are any buttons
	if ( count( $options['billing_buttons'] ) == 0 )
		return;

	$i = 0;

	// css sub-parent hide
	foreach( $options['billing_buttons'] as $btn ) {
		if( ($btn['type'] == 'text') && !empty( $btn['add_amount'] ) && !empty( $btn['fee_name'] ) && empty( $btn['add_amount_field'] ) ) {
			echo '
<style type="text/css">
#billing_'.$btn['cow'].'_applynow {
	background: -webkit-gradient(linear,left top,left bottom,from(#ad74a2),to(#96588a));
	background: -webkit-linear-gradient(#ad74a2,#96588a);
	background: -moz-linear-gradient(center top,#ad74a2 0,#96588a 100%);
	background: -moz-gradient(center top,#ad74a2 0,#96588a 100%);
	border-color: #76456c;
	color: #fff;
	text-shadow: 0 -1px 0 rgba(0,0,0,.6);
	width: 100%;
	text-align: center;
	float: right;
	cursor: pointer;
	position: relative;
}
#billing_'.$btn['cow'].'_applynow:active {
	top: 1px;
}
</style>';
		}
		if ( !empty($btn['conditional_tie']) && empty($btn['conditional_parent']) && !empty($btn['conditional_parent_use'])) {
			echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field.'.$btn['conditional_tie'].',
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field.'.$btn['conditional_tie'].', 
.woocommerce form.checkout #billing_'.$btn['cow'].'_field.'.$btn['conditional_tie'].' { 
	display: none; 
}
</style>';
		}
	}

// ====================== CHECKBOX =============================
// script when clicked show
// =============================================================
?>
<!-- Billing section: Checkbox -->
<script type="text/javascript">
jQuery(document).ready(function($){
        
<?php
	foreach( $options['billing_buttons'] as $btn ) {
		if ( $btn['type'] == 'checkbox_wccm' ) {

			if( !empty($btn['conditional_parent']) && !empty($btn['conditional_parent_use']) && !empty($btn['chosen_valt'])) {
?>

	jQuery("#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie']; ?> input[name=billing_<?php echo $btn['cow']; ?>]").click(function(){

<?php
				foreach( $options['billing_buttons'] as $btn3 ) {
					if ( empty($btn3['conditional_parent']) && !empty($btn3['conditional_parent_use']) && !empty($btn3['conditional_tie'])) {
?>
		if(jQuery('#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie']; ?> input[name=billing_<?php echo $btn['cow']; ?>]:checked').val() === '<?php echo $btn3['chosen_valt']; ?>' ) {
			jQuery("#billing_<?php echo ''.$btn3['cow'].'_field.'.$btn['conditional_tie']; ?>").show( "slow" );
		}
		if(jQuery('#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie']; ?> input[name=billing_<?php echo $btn['cow']; ?>]:checked').val() !== '<?php echo $btn3['chosen_valt']; ?>' ) {
			jQuery("#billing_<?php echo ''.$btn3['cow'].'_field.'.$btn['conditional_tie']; ?>").hide( "slow" );

<?php if ( !empty($btn2['fee_name']) && !empty($btn2['add_amount']) ) { ?>

			$( 'form.checkout' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

			var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
			data = {
				action: 'remove_tax_wccm',
				empty_check_add: 'none'
			};
                                	
			jQuery.post(ajaxurl, data, function(response) {
				$( 'body' ).trigger( 'update_checkout' );
				jQuery('form.checkout').unblock();	
			});

<?php } ?>

		}

<?php
						}
					}
?>

	});

<?php
			}
		}
	}
?>
});
</script>
<?php

// ================================ END!!! =====================================
// =============================================================================

// ====================== SELECT OPTIONS =============================
// script when clicked show
// =============================================================
?>
<!-- Billing section: Select options -->
<script type="text/javascript">
jQuery(document).ready(function($){

<?php
	foreach( $options['billing_buttons'] as $btn ) {
		if( ($btn['type'] == 'wooccmselect') ) {

			if( !empty($btn['conditional_parent']) && !empty($btn['conditional_parent_use']) && !empty($btn['chosen_valt'])) {
?>

	jQuery("#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie']; ?> select").change(function(){

<?php
				foreach( $options['billing_buttons'] as $btn3 ) {
					if ( empty($btn3['conditional_parent']) && !empty($btn3['conditional_parent_use']) && !empty($btn3['conditional_tie'])) {
?>

		if(jQuery('#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie'].' #billing_'.$btn['cow'].''; ?> option:selected').val() === '<?php echo $btn3['chosen_valt']; ?>' ) {
			jQuery("#billing_<?php echo ''.$btn3['cow'].'_field.'.$btn['conditional_tie']; ?>").show( "slow" );
		}

		if(jQuery('#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie'].' #billing_'.$btn['cow'].''; ?> option:selected').val() !== '<?php echo $btn3['chosen_valt']; ?>' ) {
			jQuery("#billing_<?php echo ''.$btn3['cow'].'_field.'.$btn['conditional_tie']; ?>").hide( "slow" );

<?php if ( !empty($btn2['fee_name']) && !empty($btn2['add_amount']) ) { ?>

			$( 'form.checkout' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

			var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
			data = {
				action: 'remove_tax_wccm',
				empty_check_add: 'none'
			};

			jQuery.post(ajaxurl, data, function(response) {
				$( 'body' ).trigger( 'update_checkout' );
				jQuery('form.checkout').unblock();	
			});

<?php } ?> 
		}
<?php
					}
				}
?>
	});
<?php
			}
		}
	}
?>
});
</script>

<?php

// ================================ END!!! =====================================
// =============================================================================

// ====================== RADIO BUTTON =============================
// script when clicked show
// =============================================================

?>
<!-- Billing section: Radio buttons -->
<script type="text/javascript">
jQuery(document).ready(function($){

<?php
	foreach( $options['billing_buttons'] as $btn ) {
		if ( $btn['type'] == 'wooccmradio' ) {

			if ( !empty($btn['conditional_parent']) && !empty($btn['conditional_parent_use']) && !empty($btn['chosen_valt'])) {
?>

	jQuery("#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie']; ?> input").click(function(){

<?php
				foreach( $options['billing_buttons'] as $btn3 ) {
					if( empty($btn3['conditional_parent']) && !empty($btn3['conditional_parent_use']) && !empty($btn3['conditional_tie'])) {
?>

		if(jQuery('#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie']; ?> input[name=billing_<?php echo $btn['cow']; ?>]:checked').val() === '<?php echo $btn3['chosen_valt']; ?>' ) {
			jQuery("#billing_<?php echo ''.$btn3['cow'].'_field.'.$btn['conditional_tie']; ?>").show( "slow" );
		}

		if(jQuery('#billing_<?php echo ''.$btn['cow'].'_field.'.$btn['conditional_tie']; ?> input[name=billing_<?php echo $btn['cow']; ?>]:checked').val() !== '<?php echo $btn3['chosen_valt']; ?>' ) {
			jQuery("#billing_<?php echo ''.$btn3['cow'].'_field.'.$btn['conditional_tie']; ?>").hide( "slow" );

<?php if ( !empty($btn2['fee_name']) && !empty($btn2['add_amount']) ) { ?>

			$( 'form.checkout' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

			var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
			data = {
				action: 'remove_tax_wccm',
				empty_check_add: 'none'
			};

			jQuery.post(ajaxurl, data, function(response) {
				$( 'body' ).trigger( 'update_checkout' );
				jQuery('form.checkout').unblock();	
			});

<?php } ?> 

		}

<?php
					}
				}
?>

	});
<?php
			}
		}
	}
?>
});
</script>

<?php

// ================================ END!!! =====================================
// =============================================================================

// ----------------------------- CLEAR ---------------------------------
// ---------------------------------------------------------------------
// ---------------------------------------------------------------------

	foreach( $options['billing_buttons'] as $btn ) {
		foreach ($woocommerce->cart->cart_contents as $key => $values ) {

			$multiproductsx = ( isset( $btn['single_p'] ) ? $btn['single_p'] : '' );
			$show_field_single = ( isset( $btn['single_px'] ) ? $btn['single_px'] : '' );
			$multiproductsx_cat = ( isset( $btn['single_p_cat'] ) ? $btn['single_p_cat'] : '' );
			$show_field_single_cat = ( isset( $btn['single_px_cat'] ) ? $btn['single_px_cat'] : '' );

			$productsarraycm[] = $values['product_id'];

			// Products
			// hide field

			// without more
			if ( !empty($btn['single_p']) && empty($btn['more_content']) ) {

				$multiarrayproductsx = explode(',',$multiproductsx);

				if(in_array($values['product_id'],$multiarrayproductsx) && ( count($woocommerce->cart->cart_contents) < 2) ){
					echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: none; 
}
</style>';
				}
			}

			// show field without more
			if ( !empty($btn['single_px']) && empty($btn['more_content']) ) {

				$show_field_array = explode('||',$show_field_single);

				if(in_array($values['product_id'], $show_field_array) && ( count($woocommerce->cart->cart_contents) < 2) ){
					echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: inline-block; 
}
</style>';
				}

				if(!in_array($values['product_id'], $show_field_array) && ( count($woocommerce->cart->cart_contents) < 2) ){
					echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: none; 
}
</style>';
				}
			}

			// Category
			// hide field
			$terms = get_the_terms( $values['product_id'], 'product_cat' );
			if( !empty($terms) ) {
				foreach( $terms as $term ) {

					$categoryarraycm[] = $term->slug;

					// without more

					if ( !empty($btn['single_p_cat']) && empty($btn['more_content']) ) {

						$multiarrayproductsx_cat = explode(',',$multiproductsx_cat);

						if(in_array($term->slug,$multiarrayproductsx_cat) && ( count($woocommerce->cart->cart_contents) < 2) ){
							echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field, 
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: none; 
}
</style>';
						}
					}

					// show field without more
					if ( !empty($btn['single_px_cat']) && empty($btn['more_content']) ) {

						$show_field_array_cat = explode('||',$show_field_single_cat);

						if(in_array($term->slug, $show_field_array_cat)  && ( count($woocommerce->cart->cart_contents) < 2)  ){
							echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: inline-block; 
}
</style>';
						}

						if( !in_array($term->slug, $show_field_array_cat)  && ( count($woocommerce->cart->cart_contents) < 2)  ){
							echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: none; 
}
</style>';
						}
					}

				}
			}
		}
		// end cart

// ===========================================================================================

		// Products
		// hide field

		// with more
		if ( !empty($btn['single_p']) && !empty($btn['more_content']) ) {

			$multiarrayproductsx = explode(',',$multiproductsx);

			if(array_intersect($productsarraycm,$multiarrayproductsx)  ){
				echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: none; 
}
</style>';
			}
		}

		// show field with more
		if ( !empty($btn['single_px']) && !empty($btn['more_content']) ) {

			$show_field_array = explode('||',$show_field_single);

			if(array_intersect($productsarraycm, $show_field_array) ){
				echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field, 
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: inline-block; 
}
</style>';
			}

			if(!array_intersect($productsarraycm, $show_field_array)  ){
				echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: none; 
}
</style>';
			}
		}

		// Category
		// hide field

		// with more
		if ( !empty($btn['single_p_cat']) && !empty($btn['more_content']) ) {

			$multiarrayproductsx_cat = explode(',',$multiproductsx_cat);

			if(array_intersect($categoryarraycm,$multiarrayproductsx_cat) ){
				echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: none; 
}
</style>';
			}
		}

		// show field with more
		if ( !empty($btn['single_px_cat']) && !empty($btn['more_content']) ) {

			$show_field_array_cat = explode('||',$show_field_single_cat);

			if(array_intersect($categoryarraycm, $show_field_array_cat)  ){
				echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: inline-block; 
}
</style>';
			}

			if( !array_intersect($categoryarraycm, $show_field_array_cat) ){
				echo '
<style type="text/css">
.woocommerce form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce-page form #customer_details #billing_'.$btn['cow'].'_field,
.woocommerce form.checkout #billing_'.$btn['cow'].'_field { 
	display: none; 
}
</style>';
			}
		}

		$categoryarraycm = '';
		$productsarraycm = '';

	} // btn cut
}
?>