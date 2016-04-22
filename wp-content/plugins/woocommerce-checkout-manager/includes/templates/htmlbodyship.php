<?php 
/**
 * WooCommerce Checkout Manager 
 *
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
?>

<td style="display:none; text-align:center;" class="more_toggler1c">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][more_content]" type="checkbox" value="1" <?php if (  !empty ($options2['shipping_buttons'][$ix]['more_content'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none;" class="more_toggler1c">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_p]" placeholder="<?php _e('Product ID(s) e.g 1674||1233','woocommerce-checkout-manager'); ?>" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['single_p'])) ? '' :  $options2['shipping_buttons'][$ix]['single_p']; ?>" />
</td>

<td style="display:none;" class="more_toggler1c">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_px]" placeholder="<?php _e('Product ID(s) e.g 1674||1233','woocommerce-checkout-manager'); ?>" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['single_px'])) ? '' :  $options2['shipping_buttons'][$ix]['single_px']; ?>" />
</td>

<td style="display:none;" class="more_toggler1c">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_p_cat]" placeholder="<?php _e('Category Slug(s) e.g my-cat||my-cat2','woocommerce-checkout-manager'); ?>" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['single_p_cat'])) ? '' :  $options2['shipping_buttons'][$ix]['single_p_cat']; ?>" />
</td>

<td style="display:none;" class="more_toggler1c">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_px_cat]" placeholder="<?php _e('Category Slug(s) e.g my-cat||my-cat2','woocommerce-checkout-manager'); ?>" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['single_px_cat'])) ? '' :  $options2['shipping_buttons'][$ix]['single_px_cat']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_time">
	<input type="text" placeholder="6" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][start_hour]" value="<?php echo (empty($options2['shipping_buttons'][$ix]['start_hour'])) ? '' : $options2['shipping_buttons'][$ix]['start_hour']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_time">
	<input type="text" placeholder="9" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][end_hour]" value="<?php echo (empty($options2['shipping_buttons'][$ix]['end_hour'])) ? '' : $options2['shipping_buttons'][$ix]['end_hour']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_time">
	<input type="text" placeholder="15" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][interval_min]" value="<?php  echo (empty($options2['shipping_buttons'][$ix]['interval_min'])) ? '' : $options2['shipping_buttons'][$ix]['interval_min']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_time">
	<input type="text" placeholder="0, 10, 20, 30, 40" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][manual_min]" value="<?php  echo (empty($options2['shipping_buttons'][$ix]['manual_min'])) ? '' : $options2['shipping_buttons'][$ix]['manual_min']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" placeholder="dd-mm-yy" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][format_date]" value="<?php echo (empty($options2['shipping_buttons'][$ix]['format_date'])) ? '' : $options2['shipping_buttons'][$ix]['format_date']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" placeholder="+3" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][min_before]" value="<?php echo (empty($options2['shipping_buttons'][$ix]['min_before'])) ? '' : $options2['shipping_buttons'][$ix]['min_before']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" placeholder="3" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][max_after]" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['max_after'])) ? '' : $options2['shipping_buttons'][$ix]['max_after']; ?>" />
</td>

<td style="display:none; text-align:center;" class="hide_stuff_color daoo">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['days_disabler'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none; text-align:center;" class="hide_stuff_days">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler0]" type="checkbox" value="1" <?php if (  !empty ($options2['shipping_buttons'][$ix]['days_disabler0'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none; text-align:center;" class="hide_stuff_days">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler1]" type="checkbox" value="1" <?php if (  !empty ($options2['shipping_buttons'][$ix]['days_disabler1'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none; text-align:center;" class="hide_stuff_days">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler2]" type="checkbox" value="1" <?php if (  !empty ($options2['shipping_buttons'][$ix]['days_disabler2'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none; text-align:center;" class="hide_stuff_days">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler3]" type="checkbox" value="1" <?php if (  !empty ($options2['shipping_buttons'][$ix]['days_disabler3'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none; text-align:center;" class="hide_stuff_days">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler4]" type="checkbox" value="1" <?php if (  !empty ($options2['shipping_buttons'][$ix]['days_disabler4'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none; text-align:center;" class="hide_stuff_days">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler5]" type="checkbox" value="1" <?php if (  !empty ($options2['shipping_buttons'][$ix]['days_disabler5'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none; text-align:center;" class="hide_stuff_days">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler6]" type="checkbox" value="1" <?php if (  !empty ($options2['shipping_buttons'][$ix]['days_disabler6'])) echo "checked='checked'"; ?> />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<span class="spongagge"><?php _e( 'Min Date', 'woocommerce-checkout-manager' ); ?></span>
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_yy]" placeholder="<?php _e('2013','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'yy', 'woocommerce-checkout-manager' ); ?>" value="<?php echo (empty($options2['shipping_buttons'][$ix]['single_yy'])) ? '' : $options2['shipping_buttons'][$ix]['single_yy']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_mm]" placeholder="<?php _e('10','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'mm', 'woocommerce-checkout-manager' ); ?>" value="<?php echo (empty($options2['shipping_buttons'][$ix]['single_mm'])) ? '' : $options2['shipping_buttons'][$ix]['single_mm']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_dd]" placeholder="<?php _e('25','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'dd', 'woocommerce-checkout-manager' ); ?>" value="<?php echo (empty($options2['shipping_buttons'][$ix]['single_dd'])) ? '' : $options2['shipping_buttons'][$ix]['single_dd']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><span class="spongagge"><?php _e( 'Max Date', 'woocommerce-checkout-manager' ); ?></span></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_max_yy]" placeholder="<?php _e('2013','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'yy', 'woocommerce-checkout-manager' ); ?>" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['single_max_yy'])) ? '' : $options2['shipping_buttons'][$ix]['single_max_yy']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_max_mm]" placeholder="<?php _e('10','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'mm', 'woocommerce-checkout-manager' ); ?>" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['single_max_mm'])) ? '' :  $options2['shipping_buttons'][$ix]['single_max_mm']; ?>" />
</td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_max_dd]" placeholder="<?php _e('25','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'dd', 'woocommerce-checkout-manager' ); ?>" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['single_max_dd'])) ? '' : $options2['shipping_buttons'][$ix]['single_max_dd']; ?>" />
</td>

<td class="more_toggler1" style="text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][checkbox]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['checkbox'])) echo "checked='checked'"; ?> />
</td>

<td class="more_toggler1" style="text-align:center;">
	<select name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][position]" >  <!--Call run() function-->
		<option value="form-row-first" <?php (!isset($options2['shipping_buttons'][$ix]['position'])) ? '' : selected( $options2['shipping_buttons'][$ix]['position'], 'form-row-first' ); ?>><?php _e('Left','woocommerce-checkout-manager'); ?></option>
		<option value="form-row-last" <?php (!isset($options2['shipping_buttons'][$ix]['position'])) ? '' : selected( $options2['shipping_buttons'][$ix]['position'], 'form-row-last' ); ?>><?php _e('Right','woocommerce-checkout-manager'); ?></option>
		<option value="form-row-wide" <?php (!isset($options2['shipping_buttons'][$ix]['position'])) ? '' : selected( $options2['shipping_buttons'][$ix]['position'], 'form-row-wide' ); ?>><?php _e('Center','woocommerce-checkout-manager'); ?></option>
	</select>
</td>

<td class="more_toggler1" style="text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][clear_row]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['clear_row'])) echo "checked='checked'"; ?> />
</td>

<td class="filter_field" style="display:none;text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][deny_checkout]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['deny_checkout'])) echo "checked='checked'"; ?> />
</td>

<td class="filter_field_tog add_amount_field condition_tick hide_stuff_time hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c" style="display:none;">
	<?php _e('Filter Toggler', 'woocommerce-checkout-manager' ); ?>
</td>

<td class="filter_field" style="display:none;text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][tax_remove]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['tax_remove'])) echo "checked='checked'"; ?> />
</td>

<td class="filter_field" style="display:none;text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][deny_receipt]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['deny_receipt'])) echo "checked='checked'"; ?> />
</td>

<td class="filter_field condition_tick hide_stuff_change hide_stuff_time hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c" style="display:none;text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][add_amount]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['add_amount'])) echo "checked='checked'"; ?> />
</td>

<td class="add_amount_field" style="display:none;text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][fee_name]" type="text" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['fee_name'] ) ? $options2['shipping_buttons'][$ix]['fee_name'] : '' ); ?>" placeholder="<?php _e('My Custom Charge','woocommerce-checkout-manager'); ?>" />
</td>

<td class="add_amount_field" style="display:none;text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][add_amount_field]" type="text" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['add_amount_field'] ) ? $options2['shipping_buttons'][$ix]['add_amount_field'] : '' ); ?>" placeholder="50" />
</td>

<td class="filter_field add_amount_field hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_time hide_stuff_color more_toggler1 more_toggler1c" style="display:none;text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][conditional_parent_use]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['conditional_parent_use'])) echo "checked='checked'"; ?> />
</td>

<td class="condition_tick" style="display:none;text-align:center;">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][conditional_parent]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['conditional_parent'])) echo "checked='checked'"; ?> />
</td>

<td class="more_toggler1">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][label]" placeholder="<?php _e('My Field Name','woocommerce-checkout-manager'); ?>" value="<?php echo esc_attr( $options2['shipping_buttons'][$ix]['label'] ); ?>" />
</td>

<td class="more_toggler1">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][placeholder]" placeholder="<?php _e('Example red','woocommerce-checkout-manager'); ?>" value="<?php echo (empty($options2['shipping_buttons'][$ix]['placeholder'])) ? '' : $options2['shipping_buttons'][$ix]['placeholder']; ?>" <?php if ( $options2['shipping_buttons'][$ix]['cow'] == 'country' || $options2['shipping_buttons'][$ix]['cow'] == 'state' ) { echo 'readonly="readonly"'; } ?> />
</td>

<td style="display:none;" class="filter_field add_amount_field hide_stuff_time hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c condition_tick add_amount_field">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][chosen_valt]" placeholder="<?php _e('Yes','woocommerce-checkout-manager'); ?>" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['chosen_valt'] ) ? $options2['shipping_buttons'][$ix]['chosen_valt'] : '' ); ?>" />
</td>

<td style="display:none;" class="condition_tick">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][conditional_tie]" placeholder="<?php _e('Parent Abbr. Name','woocommerce-checkout-manager'); ?>" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['conditional_tie'] ) ? $options2['shipping_buttons'][$ix]['conditional_tie'] : '' ); ?>" />
</td>

<td style="display:none;" class="filter_field">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][colorpickerd]" id="shipping-colorpic<?php echo $ix; ?>" placeholder="<?php _e('#000000','woocommerce-checkout-manager'); ?>" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['colorpickerd'] ) ? $options2['shipping_buttons'][$ix]['colorpickerd'] : '' ); ?>" />
</td>

<td style="display:none;" class="filter_field">
	<select name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][colorpickertype]">
		<option value="farbtastic" <?php (!isset($options2['shipping_buttons'][$ix]['colorpickertype'])) ? '' : selected( $options2['shipping_buttons'][$ix]['colorpickertype'], 'farbtastic' ); ?>><?php _e('Farbtastic','woocommerce-checkout-manager'); ?></option>
		<option value="iris" <?php (!isset($options2['shipping_buttons'][$ix]['colorpickertype'])) ? '' :  selected( $options2['shipping_buttons'][$ix]['colorpickertype'], 'iris' ); ?>><?php _e('Iris','woocommerce-checkout-manager'); ?></option>
	</select>
</td>

<td style="display:none;text-align:center;" class="filter_field">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][user_role]" type="checkbox" value="user_role" <?php if (  !empty ($options2['shipping_buttons'][$ix]['user_role'])) echo "checked='checked'"; ?> />
</td>

<td class="filter_field" style="display:none;">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][role_options]" placeholder="Option 1||Option 2||Option 3" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['role_options'] ) ? $options2['shipping_buttons'][$ix]['role_options'] : '' ); ?>" />
</td>

<td class="filter_field" style="display:none;">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][role_options2]" placeholder="Option 1||Option 2||Option 3" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['role_options2'] ) ? $options2['shipping_buttons'][$ix]['role_options2'] : '' ); ?>" />
</td>

<td style="display:none;" class="filter_field add_amount_field hide_stuff_time hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c condition_tick add_amount_field">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][extra_class]" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['extra_class'] ) ? $options2['shipping_buttons'][$ix]['extra_class'] : '' ); ?>" />
</td>

<td style="display:none;text-align:center;" class="hide_stuff_op wccm1">
	<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][fancy]" type="checkbox" value="country_select" <?php if (  !empty ($options2['shipping_buttons'][$ix]['fancy'])) echo "checked='checked'"; ?> />
</td>

<td class="hide_stuff_op wccm1" style="display:none;">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][force_title2]" placeholder="<?php _e('Name Guide','woocommerce-checkout-manager'); ?>" value="<?php echo (empty($options2['shipping_buttons'][$ix]['force_title2'])) ? '' : $options2['shipping_buttons'][$ix]['force_title2']; ?>" />
</td>

<td class="hide_stuff_op wccm1" style="display:none;">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][option_array]" placeholder="Option 1||Option 2||Option 3" value="<?php echo ( isset( $options2['shipping_buttons'][$ix]['option_array'] ) ? $options2['shipping_buttons'][$ix]['option_array'] : '' ); ?>" />
</td>

<td class="filter_field add_amount_field hide_stuff_time condition_tick hide_stuff_change hide_stuff_opcheck hide_stuff_color more_toggler1 more_toggler1c" style="display:none;">
	<?php _e('Options Toggler', 'woocommerce-checkout-manager' ); ?>
</td>

<td class="filter_field add_amount_field condition_tick hide_stuff_change hide_stuff_timef hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c" style="display:none;">
	<?php _e('Time Toggler', 'woocommerce-checkout-manager' ); ?>
</td>

<td class="filter_field add_amount_field hide_stuff_time condition_tick hide_stuff_change hide_stuff_opcheck hide_stuff_op more_toggler1 more_toggler1c hide_stuff_days" style="display:none;">
	<?php _e('Date Toggler', 'woocommerce-checkout-manager' ); ?>
</td>

<td style="display:none;" class="filter_field add_amount_field hide_stuff_time condition_tick hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1">
	<?php _e('Hidden Toggler', 'woocommerce-checkout-manager' ); ?>
</td>

<td class="filter_field add_amount_field condition_tick hide_stuff_time hide_stuff_change hide_stuff_opcheck hide_stuff_color hide_stuff_op more_toggler more_toggler1c">
	<?php _e('More Toggler', 'woocommerce-checkout-manager' ); ?>
</td>

<td class="more_toggler1">
	<select name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][type]" <?php if ( $options2['shipping_buttons'][$ix]['cow'] == 'country' || $options2['shipping_buttons'][$ix]['cow'] == 'state' ) { echo 'readonly="readonly" style="pointer-events:none;"'; } ?> >  <!--Call run() function-->
		<option value="wooccmtext" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' : selected( $options2['shipping_buttons'][$ix]['type'], 'wooccmtext' ); ?>><?php _e('Text Input','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmtextarea" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' :  selected( $options2['shipping_buttons'][$ix]['type'], 'wooccmtextarea' ); ?>><?php _e('Text Area','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmpassword" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' :  selected( $options2['shipping_buttons'][$ix]['type'], 'wooccmpassword' ); ?>><?php _e('Password','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmradio" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' :  selected( $options2['shipping_buttons'][$ix]['type'], 'wooccmradio' ); ?>><?php _e('Radio Button','woocommerce-checkout-manager'); ?></option>
		<option value="checkbox_wccm" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' :  selected( $options2['shipping_buttons'][$ix]['type'], 'checkbox_wccm' ); ?>><?php _e('Check Box','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmselect" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' :  selected( $options2['shipping_buttons'][$ix]['type'], 'wooccmselect' ); ?>><?php _e('Select Options','woocommerce-checkout-manager'); ?></option>
		<option value="datepicker" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' :  selected( $options2['shipping_buttons'][$ix]['type'], 'datepicker' ); ?>><?php _e('Date Picker','woocommerce-checkout-manager'); ?></option>
		<option value="time" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' :  selected( $options2['shipping_buttons'][$ix]['type'], 'time' ); ?>><?php _e('Time Picker','woocommerce-checkout-manager'); ?></option>
		<option value="colorpicker" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' : selected( $options2['shipping_buttons'][$ix]['type'], 'colorpicker' ); ?>><?php _e('Color Picker','woocommerce-checkout-manager'); ?></option>
		<option value="heading" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' : selected( $options2['shipping_buttons'][$ix]['type'], 'heading' ); ?>><?php _e('Heading','woocommerce-checkout-manager'); ?></option>
		<option value="multiselect" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' : selected( $options2['shipping_buttons'][$ix]['type'], 'multiselect' ); ?>><?php _e('Multi-Select','woocommerce-checkout-manager'); ?></option>
		<option value="multicheckbox" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' : selected( $options2['shipping_buttons'][$ix]['type'], 'multicheckbox' ); ?>><?php _e('Multi-Checkbox','woocommerce-checkout-manager'); ?></option>
		<option <?php if ( $options2['shipping_buttons'][$ix]['cow'] == 'country' ) { echo 'selected="selected"'; } ?> value="wooccmcountry" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' : selected( $options2['shipping_buttons'][$ix]['type'], 'wooccmcountry' ); ?>><?php _e('Country','woocommerce-checkout-manager'); ?></option>
		<option <?php if ( $options2['shipping_buttons'][$ix]['cow'] == 'state' ) { echo 'selected="selected"'; } ?> value="wooccmstate" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' : selected( $options2['shipping_buttons'][$ix]['type'], 'wooccmstate' ); ?>><?php _e('State','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmupload" <?php (!isset($options2['shipping_buttons'][$ix]['type'])) ? '' : selected( $options2['shipping_buttons'][$ix]['type'], 'wooccmupload' ); ?>><?php _e('File Picker','woocommerce-checkout-manager'); ?></option> 
	</select>
</td>

<td class="more_toggler1">
	<input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][cow]" placeholder="MyField" value="<?php echo $options2['shipping_buttons'][$ix]['cow']; ?>" <?php if ( empty($options['checkness']['abbreviation'])) { echo 'readonly="readonly"'; } ?> <?php if ( in_array($options2['shipping_buttons'][$ix]['cow'], $htmlshippingabbr ) ) { echo 'readonly="readonly" style="pointer-events:none;"'; } ?> />
</td>