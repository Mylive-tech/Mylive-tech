<?php 
/**
 * WooCommerce Checkout Manager 
 *
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
?>

<td style="display:none;text-align:center;" class="more_toggler1c"><input type="checkbox" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][more_content]" title="<?php esc_attr_e( 'More than two content', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="more_toggler1c"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_p]" placeholder="<?php _e('Product ID(s) e.g 1674||1233','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'Hide field from this Products Only', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="more_toggler1c"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_px]" placeholder="<?php _e('Product ID(s) e.g 1674||1233','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'Display Field for these Products Only', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="more_toggler1c"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_p_cat]" placeholder="<?php _e('Category Slug(s) e.g my-cat||my-cat2','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'Hide field from Category', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="more_toggler1c"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_px_cat]" placeholder="<?php _e('Category Slug(s) e.g my-cat||my-cat2','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'Show Field for Category', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="hide_stuff_time"><input type="text" placeholder="6" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][start_hour]" value="" /></td>

<td style="display:none;" class="hide_stuff_time"><input type="text" placeholder="9" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][end_hour]" value="" /></td>

<td style="display:none;" class="hide_stuff_time"><input type="text" placeholder="15" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][interval_min]" value="" /></td>

<td style="display:none;" class="hide_stuff_time"><input type="text" placeholder="0, 10, 20, 30, 40" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][manual_min]" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][format_date]" placeholder="dd-mm-yy" title="dd-mm-yy" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][min_before]" placeholder="+3" title="Days Before" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][max_after]" placeholder="3" title="Days After" value="" /></td>

<td style="display:none;text-align:center;" class="hide_stuff_color daoo"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler]" type="checkbox" value="" /></td>

<td style="display:none;text-align:center;" class="hide_stuff_days"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler0]" type="checkbox" value="" /></td>

<td style="display:none;text-align:center;" class="hide_stuff_days"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler1]" type="checkbox" value="" /></td>

<td style="display:none;text-align:center;" class="hide_stuff_days"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler2]" type="checkbox" value="" /></td>

<td style="display:none;text-align:center;" class="hide_stuff_days"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler3]" type="checkbox" value="" /></td>

<td style="display:none;text-align:center;" class="hide_stuff_days"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler4]" type="checkbox" value="" /></td>

<td style="display:none;text-align:center;" class="hide_stuff_days"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler5]" type="checkbox" value="" /></td>

<td style="display:none;text-align:center;" class="hide_stuff_days"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][days_disabler6]" type="checkbox" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days" title="<?php esc_attr_e( 'Min Date', 'woocommerce-checkout-manager' ); ?>"><span class="spongagge"><?php _e( 'Min Date', 'woocommerce-checkout-manager' ); ?></span></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_yy]" placeholder="<?php _e('2013','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'yy', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_mm]" placeholder="<?php _e('10','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'mm', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_dd]" placeholder="<?php _e('25','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'dd', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days" title="<?php esc_attr_e( 'Max Date', 'woocommerce-checkout-manager' ); ?>"><span class="spongagge"><?php _e( 'Max Date', 'woocommerce-checkout-manager' ); ?></span></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_max_yy]" placeholder="<?php _e('2013','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'yy', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_max_mm]" placeholder="<?php _e('10','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'mm', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="hide_stuff_color hide_stuff_days"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][single_max_dd]" placeholder="<?php _e('25','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'dd', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td class="more_toggler1" style="text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][checkbox]" type="checkbox" title="<?php esc_attr_e( 'Add/Remove Required Attribute', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td class="more_toggler1" style="text-align:center;">
	<select name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][position]" >  <!--Call run() function-->
		<option value="form-row-first" ><?php _e('Left','woocommerce-checkout-manager'); ?></option>
		<option value="form-row-last" ><?php _e('Right','woocommerce-checkout-manager'); ?></option>
		<option value="form-row-wide" ><?php _e('Center','woocommerce-checkout-manager'); ?></option>
	</select>
</td>

<td class="more_toggler1" style="text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][clear_row]" type="checkbox" title="<?php esc_attr_e( 'Clear Row', 'woocommerce-checkout-manager' ); ?>" value=" " /></td>

<td class="filter_field" style="display:none;text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][deny_checkout]" type="checkbox" title="<?php esc_attr_e( 'Deny Checkout', 'woocommerce-checkout-manager' ); ?>" value=" " /></td>

<td class="filter_field_tog add_amount_field condition_tick hide_stuff_time hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c" style="display:none;"><?php _e('Filter Toggler', 'woocommerce-checkout-manager' ); ?></td>

<td class="filter_field" style="display:none;text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][tax_remove]" type="checkbox" title="<?php esc_attr_e( 'Remove tax', 'woocommerce-checkout-manager' ); ?>" value=" " /></td>

<td class="filter_field" style="display:none;text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][deny_receipt]" type="checkbox" title="<?php esc_attr_e( 'Deny Receipt', 'woocommerce-checkout-manager' ); ?>" value=" " /></td>

<td class="filter_field condition_tick hide_stuff_change hide_stuff_time hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c" style="display:none;text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][add_amount]" type="checkbox" title="<?php esc_attr_e( 'Add Amount', 'woocommerce-checkout-manager' ); ?>" value=" " /></td>

<td class="add_amount_field" style="display:none;text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][fee_name]" type="text" title="<?php esc_attr_e( 'Amount Name', 'woocommerce-checkout-manager' ); ?>" value="" placeholder="<?php _e('My Custom Charge','woocommerce-checkout-manager'); ?>" /></td>

<td class="add_amount_field" style="display:none;text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][add_amount_field]" type="text" title="<?php esc_attr_e( 'Add Amount Field', 'woocommerce-checkout-manager' ); ?>" value="" placeholder="50" /></td>

<td class="filter_field add_amount_field hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_time hide_stuff_color more_toggler1 more_toggler1c" style="display:none;text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][conditional_parent_use]" type="checkbox" title="<?php esc_attr_e( 'Conditional Field On', 'woocommerce-checkout-manager' ); ?>" value=" " /></td>

<td class="condition_tick" style="display:none;text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][conditional_parent]" type="checkbox" title="<?php esc_attr_e( 'Conditional Parent', 'woocommerce-checkout-manager' ); ?>" value=" " /></td>

<td class="more_toggler1"><input placeholder="<?php _e('My Field Name','woocommerce-checkout-manager'); ?>" type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][label]" title="<?php esc_attr_e( 'Label of the New Field', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td class="more_toggler1"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][placeholder]" placeholder="<?php _e('Example red','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'Placeholder - Preview of Data to Input', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="filter_field add_amount_field hide_stuff_time hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c condition_tick add_amount_field"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][chosen_valt]" placeholder="<?php _e('Yes','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'Chosen value for conditional', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="condition_tick"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][conditional_tie]" placeholder="<?php _e('Parent Abbr. Name','woocommerce-checkout-manager'); ?>" title="<?php esc_attr_e( 'Parent Abbr. Name', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td style="display:none;" class="filter_field"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][colorpickerd]" id="shipping-colorpic<?php echo $ix; ?>" title="<?php esc_attr_e( 'Default Color', 'woocommerce-checkout-manager' ); ?>" value="#000000" /></td>

<td style="display:none;" class="filter_field">
	<select name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][colorpickertype]" >  <!--Call run() function-->
		<option value="farbtastic" ><?php _e('Farbtastic','woocommerce-checkout-manager'); ?></option>
		<option value="iris" ><?php _e('Iris','woocommerce-checkout-manager'); ?></option>
	</select>
</td>

<td style="display:none;text-align:center;" class="filter_field"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][user_role]" type="checkbox" value="" /></td>

<td class="filter_field" style="display:none;"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][role_options]" placeholder="Option 1||Option 2||Option 3" value="" /></td>

<td class="filter_field" style="display:none;"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][role_options2]" placeholder="Option 1||Option 2||Option 3" value="" /></td>

<td style="display:none;" class="filter_field add_amount_field hide_stuff_change hide_stuff_time hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c condition_tick add_amount_field"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][extra_class]" value="" /></td>


<td style="display:none;text-align:center;" class="hide_stuff_op wccm1"><input type="checkbox" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][fancy]" title="<?php esc_attr_e( 'Adapt to woocommerce style', 'woocommerce-checkout-manager' ); ?>" value="" /></td>

<td class="hide_stuff_op wccm1" style="display:none;"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][force_title2]" value="" placeholder="<?php _e('Name Guide','woocommerce-checkout-manager'); ?>" /></td>

<td class="hide_stuff_op wccm1" style="display:none;"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][option_array]" value="" placeholder="Option 1||Option 2||Option 3" /></td>

<td class="filter_field add_amount_field hide_stuff_time condition_tick hide_stuff_change hide_stuff_opcheck hide_stuff_color more_toggler1 more_toggler1c" style="display:none;"><?php _e('Options Toggler', 'woocommerce-checkout-manager' ); ?></td>

<td class="filter_field add_amount_field condition_tick hide_stuff_change hide_stuff_timef hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1 more_toggler1c" style="display:none;"><?php _e('Time Toggler', 'woocommerce-checkout-manager' ); ?></td>

<td class="filter_field add_amount_field hide_stuff_time condition_tick hide_stuff_change hide_stuff_opcheck hide_stuff_op more_toggler1 more_toggler1c hide_stuff_days" style="display:none;"><?php _e('Date Toggler', 'woocommerce-checkout-manager' ); ?></td>

<td style="display:none;" class="filter_field add_amount_field hide_stuff_time condition_tick hide_stuff_change hide_stuff_opcheck hide_stuff_op hide_stuff_color more_toggler1"><?php _e('Hidden Toggler', 'woocommerce-checkout-manager' ); ?></td>

<td class="filter_field add_amount_field condition_tick hide_stuff_time hide_stuff_change hide_stuff_opcheck hide_stuff_color hide_stuff_op more_toggler more_toggler1c"><?php _e('More Toggler', 'woocommerce-checkout-manager' ); ?></td>

<td class="more_toggler1">
	<select name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][type]" >  <!--Call run() function-->
		<option value="wooccmtext" ><?php _e('Text Input','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmtextarea" ><?php _e('Text Area','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmpassword" ><?php _e('Password','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmradio" ><?php _e('Radio Button','woocommerce-checkout-manager'); ?></option>
		<option value="checkbox_wccm" ><?php _e('Check Box','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmselect" ><?php _e('Select Options','woocommerce-checkout-manager'); ?></option>
		<option value="datepicker" ><?php _e('Date Picker','woocommerce-checkout-manager'); ?></option>
		<option value="time" ><?php _e('Time Picker','woocommerce-checkout-manager'); ?></option>
		<option value="colorpicker" ><?php _e('Color Picker','woocommerce-checkout-manager'); ?></option>
		<option value="heading" ><?php _e('Heading','woocommerce-checkout-manager'); ?></option>
		<option value="multiselect" ><?php _e('Multi-Select','woocommerce-checkout-manager'); ?></option>
		<option value="multicheckbox" ><?php _e('Multi-Checkbox','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmcountry" ><?php _e('Country','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmstate" ><?php _e('State','woocommerce-checkout-manager'); ?></option>
		<option value="wooccmupload"><?php _e('File Picker','woocommerce-checkout-manager'); ?></option>
	</select>
</td>

<td class="more_toggler1"><input type="text" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][cow]" placeholder="MyField" title="<?php esc_attr_e( 'Abbreviation (No spaces)', 'woocommerce-checkout-manager' ); ?>" value=""  <?php if ( empty($options['checkness']['abbreviation'])) { echo 'readonly="readonly"'; } ?> /></td>