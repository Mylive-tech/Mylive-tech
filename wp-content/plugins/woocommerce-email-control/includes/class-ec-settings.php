<?php

$GLOBALS['EC_Settings'] = new EC_Settings();

/*
*  EC_settings
*
* Settings for eg email template editing
* @since: 3.6
* @created: 25/01/13
*/

class EC_Settings {
	
	/**
	 * Construct and initialize the main plugin class
	 */
	public function __construct() {
		
		/* Init Shortcodes */
		add_shortcode( 'ec_firstname',				array( "EC_Settings", 'ec_firstname' ) );
		add_shortcode( 'ec_lastname',				array( "EC_Settings", 'ec_lastname' ) );
		
		add_shortcode( 'ec_email',				    array( "EC_Settings", 'ec_email' ) );
		
		add_shortcode( 'ec_order',					array( "EC_Settings", 'ec_order' ) );
		add_shortcode( 'ec_order_link',				array( "EC_Settings", 'ec_order' ) );
		add_shortcode( 'ec_user_order_link',		array( "EC_Settings", 'ec_order' ) );
		add_shortcode( 'ec_pay_link',				array( "EC_Settings", 'ec_pay_link' ) );
		
		add_shortcode( 'ec_customer_note',			array( "EC_Settings", 'ec_customer_note' ) );
		
		add_shortcode( 'ec_user_login',				array( "EC_Settings", 'ec_user_login' ) );
		add_shortcode( 'ec_account_link',			array( "EC_Settings", 'ec_account_link' ) );
		add_shortcode( 'ec_user_password',			array( "EC_Settings", 'ec_user_password' ) );
		add_shortcode( 'ec_reset_password_link',	array( "EC_Settings", 'ec_reset_password_link' ) );
		add_shortcode( 'ec_login_link',				array( "EC_Settings", 'ec_login_link' ) );
		add_shortcode( 'ec_site_link',				array( "EC_Settings", 'ec_site_link' ) );
		add_shortcode( 'ec_site_name',				array( "EC_Settings", 'ec_site_name' ) );
	}
	
	/**
	 * Output admin fields.
	 *
	 * Loops though the options array and outputs each field.
	 *
	 * @access public
	 * @param array $options Options array to output
	 */
	public static function output_fields( $options ) {
		
		// Sections
		// -------------------------
		
		// Get Sections array.
		$sections = ec_get_settings( false, array('type'=>'section') );
		
		// Set the all section so the fields that aren't in a section are not left out.
		$sections['all'] = array(
			'id'	=>'all',
			'name'	=>'All'
		);
		
		// Collect a new array of only the sections that have fields.
		$collect_sections = array();
		foreach ( $options as $value ) :
			if ( !isset( $value['section'] ) )		$value['section'] = 'all';
			$collect_sections[ $value['section'] ] = $sections[ $value['section'] ];
		endforeach;
		
		// Set sections back the new array of only sections with fields in them.
		$sections = $collect_sections;
		
		foreach ( $sections as $section_value_array ) :
			?>
			
			<div class="section">
				<h3><?php echo $section_value_array['name'] ?></h3>
				
				<div class="section-inner">
					<?php
					foreach ( $options as $value ) :
						
						if ( !isset( $value['type'] ) )		continue;
						if ( !isset( $value['id'] ) )			$value['id'] = '';
						if ( !isset( $value['title'] ) )		$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
						if ( !isset( $value['class'] ) )		$value['class'] = '';
						if ( !isset( $value['field_class'] ) )	$value['field_class'] = '';
						if ( !isset( $value['css'] ) )			$value['css'] = '';
						if ( !isset( $value['default'] ) )		$value['default'] = '';
						if ( !isset( $value['desc'] ) )		$value['desc'] = '';
						if ( !isset( $value['tip'] ) )			$value['tip'] = false;
						if ( !isset( $value['size'] ) )		$value['size'] = 'full';
						if ( !isset( $value['section'] ) )		$value['section'] = 'all';
						
						
						if ( $value['section'] == $section_value_array['id'] ) :
							
							
							// Custom attribute handling
							$custom_attributes = array();

							if ( !empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) )
								foreach ( $value['custom_attributes'] as $attribute => $attribute_value )
									$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';

							// Description handling
							if ( $value['tip'] === true ) {
								$description = '';
								$tip = $value['desc'];
							}
							elseif ( !empty( $value['tip'] ) ) {
								$description = $value['desc'];
								$tip = $value['tip'];
							}
							elseif ( !empty( $value['desc'] ) ) {
								$description = $value['desc'];
								$tip = '';
							}
							else {
								$description = $tip = '';
							}

							if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ) ) ) {
								$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
							}
							elseif ( $description && in_array( $value['type'], array( 'checkbox' ) ) ) {
								$description =  wp_kses_post( $description );
							}
							elseif ( $description ) {
								$description = '<span class="description">' . wp_kses_post( $description ) . '</span>';
							}

							if ( $tip && in_array( $value['type'], array( 'checkbox' ) ) ) {
								$tip = '<span class="help-icon help_tip_new" data-tip="' . esc_attr($tip) . '" >&nbsp;</span>';
							}
							elseif ( $tip ) {
								$tip = '<span class="help-icon help_tip_new" data-tip="' . esc_attr($tip) . '" >&nbsp;</span>';
							}
							

							// Switch based on type
							switch( $value['type'] ) {
								
								// CX
								
								case 'image_upload':
									$type 			= $value['type'];
									$class 			= '';
									$option_value 	= self::get_option( $value['id'], $value['default'] );
									?>
									
									<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
										<label class="controls-label">
											<?php echo esc_html( $value['title'] ); ?>
											<?php echo $tip; ?>
										</label>
										<?php // echo $tip; ?>
										
										<div class="controls-field">
											<div class="controls-inner-row">
												
												<?php if ($value['default']): ?>
													<span class="reset-to-default help_tip_new" data-tip="<?php echo esc_attr( __("Reset to - ",'email-control') . ' ' . $value['default'] ); ?>" data-default="<?php echo esc_html( $value['default'] ); ?>">
														Reset to default <i class="fa fa-refresh"></i>
													</span>
												<?php endif ?>
				                                
				                                <input
				                                    name="<?php echo esc_attr( $value['id'] ); ?>"
				                                    id="<?php echo esc_attr( $value['id'] ); ?>"
				                                    type="text"
				                                    style="<?php echo esc_attr( $value['css'] ); ?>"
				                                    value="<?php echo esc_attr( $option_value ); ?>"
				                                    placeholder="http://"
				                                    
				                                    class="upload_image <?php echo esc_attr( $value['field_class'] ); ?>"
				                                    autocomplete="off"
				                                    <?php echo implode( ' ', $custom_attributes ); ?>
				                                />
				                                <input class="upload_image_button button" type="button" value="Upload" />
												
												<?php // echo $description; ?>
											</div>
										</div>
									</div>
									
									<?php
								
								break;
								
								// Standard text inputs and subtypes like 'number'
								case 'text':
								case 'email':
								case 'number':
								case 'color' :
								case 'password' :

									$type 			= $value['type'];
									$class 			= '';
									$option_value 	= self::get_option( $value['id'], $value['default'] );

									if ( $value['type'] == 'color' ) {
										$type = 'text';
										$value['field_class'] .= 'ec-colorpick';
										$description .= '<div id="colorPickerDiv_' . esc_attr( $value['id'] ) . '" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>';
									}
									?>
									
									<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
										<label class="controls-label">
											<?php echo esc_html( $value['title'] ); ?>
											<?php echo $tip; ?>
										</label>
										
										<div class="controls-field">
											<div class="controls-inner-row">
												
												<?php if ($value['default']): ?>
													<span class="reset-to-default help_tip_new" data-tip="<?php echo esc_attr( __("Reset to - ",'email-control') . ' ' . $value['default'] ); ?>" data-default="<?php echo esc_html( $value['default'] ); ?>">
														Reset to default <i class="fa fa-refresh"></i>
													</span>
												<?php endif ?>
												
												<input
													name="<?php echo esc_attr( $value['id'] ); ?>"
													id="<?php echo esc_attr( $value['id'] ); ?>"
													type="<?php echo esc_attr( $type ); ?>"
													style="<?php echo esc_attr( $value['css'] ); ?>"
													value="<?php echo esc_attr( $option_value ); ?>"
													class="<?php echo esc_attr( $value['field_class'] ); ?>"
													autocomplete="off"
													<?php echo implode( ' ', $custom_attributes ); ?>
												/>
												<?php // echo $description; ?>
											</div>
										</div>
									</div>
									
									<?php
								break;
								
								case 'heading':
									
									$type 			= $value['type'];
									$class 			= '';
									?>
									
									<div class="main-controls-heading forminp-<?php echo sanitize_title( $value['type'] ) ?>  <?php echo esc_attr( $value['class'] ); ?>">
										<?php echo esc_html( $value['title'] ); ?>
										<?php echo $tip; ?>
									</div>
									
									<?php
								break;

								// Textarea
								case 'textarea':

									$option_value 	= self::get_option( $value['id'], $value['default'] );
									?>
									
									<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
										<label class="controls-label">
											<?php echo esc_html( $value['title'] ); ?>
											<?php echo $tip; ?>
										</label>
										<?php // echo $tip; ?>
										<?php // echo $description; ?>
										
										<div class="controls-field">
											<div class="controls-inner-row">
												
												<?php if ($value['default']): ?>
													<span class="reset-to-default help_tip_new" data-tip="<?php echo esc_attr( __("Reset to - ",'email-control') . ' ' . $value['default'] ); ?>" data-default="<?php echo esc_attr( $value['default'] ); ?>">
														Reset to default <i class="fa fa-refresh"></i>
													</span>
												<?php endif ?>
												
												<textarea
													name="<?php echo esc_attr( $value['id'] ); ?>"
													id="<?php echo esc_attr( $value['id'] ); ?>"
													style="<?php echo esc_attr( $value['css'] ); ?>"
													class="<?php echo esc_attr( $value['field_class'] ); ?>"
													<?php echo implode( ' ', $custom_attributes ); ?>
												><?php echo esc_textarea( $option_value ); ?></textarea>
												
											</div>
										</div>
									</div>
									
									<?php
									
								break;
								
								
								// /CX

								// Section Titles
								case 'title':
									if ( !empty( $value['title'] ) ) {
										echo '<h3>' . esc_html( $value['title'] ) . '</h3>';
									}
									if ( !empty( $value['desc'] ) ) {
										echo wpautop( wptexturize( wp_kses_post( $value['desc'] ) ) );
									}
									echo '<table class="form-table">'. "\n\n";
									if ( !empty( $value['id'] ) ) {
										do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) );
									}
								break;

								// Section Ends
								case 'sectionend':
									if ( !empty( $value['id'] ) ) {
										do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_end' );
									}
									echo '</table>';
									if ( !empty( $value['id'] ) ) {
										do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_after' );
									}
								break;
								
								// Select boxes
								case 'select' :
								case 'multiselect' :

									$option_value 	= self::get_option( $value['id'], $value['default'] );

									?>
									<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
										<label class="controls-label">
											<?php echo esc_html( $value['title'] ); ?>
											<?php echo $tip; ?>
										</label>
										<?php // echo $tip; ?>
										<?php // echo $description; ?>
										
										<div class="controls-field">
											<div class="controls-inner-row">
												
												<?php if ($value['default']): ?>
													<span class="reset-to-default help_tip_new" data-tip="<?php echo esc_attr( __("Reset to - ",'email-control') . ' ' . $value['default'] ); ?>" data-default="<?php echo esc_attr( $value['default'] ); ?>">
														Reset to default <i class="fa fa-refresh"></i>
													</span>
												<?php endif ?>
												
												<select
													name="<?php echo esc_attr( $value['id'] ); ?><?php if ( $value['type'] == 'multiselect' ) echo '[]'; ?>"
													id="<?php echo esc_attr( $value['id'] ); ?>"
													style="<?php echo esc_attr( $value['css'] ); ?>"
													class="<?php echo esc_attr( $value['field_class'] ); ?>"
													<?php echo implode( ' ', $custom_attributes ); ?>
													<?php if ( $value['type'] == 'multiselect' ) echo 'multiple="multiple"'; ?>
													>
													<?php
														foreach ( $value['options'] as $key => $val ) {
															?>
															<option value="<?php echo esc_attr( $key ); ?>" <?php

																if ( is_array( $option_value ) )
																	selected( in_array( $key, $option_value ), true );
																else
																	selected( $option_value, $key );

															?>><?php echo $val ?></option>
															<?php
														}
													?>
											   </select>
											   
											</div>
										</div>
									</div>
									<?php
								break;

								// Radio inputs
								case 'radio' :

									$option_value 	= self::get_option( $value['id'], $value['default'] );

									?><tr valign="top">
										<th scope="row" class="titledesc">
											<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
											<?php echo $tip; ?>
										</th>
										<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
											<fieldset>
												<?php echo $description; ?>
												<ul>
												<?php
													foreach ( $value['options'] as $key => $val ) {
														?>
														<li>
															<label><input
																name="<?php echo esc_attr( $value['id'] ); ?>"
																value="<?php echo $key; ?>"
																type="radio"
																style="<?php echo esc_attr( $value['css'] ); ?>"
																class="<?php echo esc_attr( $value['field_class'] ); ?>"
																<?php echo implode( ' ', $custom_attributes ); ?>
																<?php checked( $key, $option_value ); ?>
																/> <?php echo $val ?></label>
														</li>
														<?php
													}
												?>
												</ul>
											</fieldset>
										</td>
									</tr><?php
								break;

								// Checkbox input
								case 'checkbox' :
									?>
									<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
										<label class="controls-label">
											<?php echo esc_html( $value['title'] ); ?>
											<?php echo $tip; ?>
										</label>
										
										<div class="controls-field">
											<div class="controls-inner-row">
												
												<?php if ($value['default']): ?>
													<span class="reset-to-default help_tip_new" data-tip="<?php echo esc_attr( __("Reset to - ",'email-control') . ' ' . $value['default'] ); ?>" data-default="<?php echo esc_html( $value['default'] ); ?>">
														Reset to default <i class="fa fa-refresh"></i>
													</span>
												<?php endif ?>
												<input
													name="<?php echo esc_attr( $value['id'] ); ?>"
													id="<?php echo esc_attr( $value['id'] ); ?>"
													type="checkbox"
													style="<?php echo esc_attr( $value['css'] ); ?>"
													value="<?php echo esc_attr( $option_value ); ?>"
													class="<?php echo esc_attr( $value['field_class'] ); ?>"
													<?php echo implode( ' ', $custom_attributes ); ?>
												/>
												<?php // echo $description; ?>
											</div>
										</div>
									</div>
									<?php
								break;

								// Image width settings
								case 'image_width' :

									$width 	= self::get_option( $value['id'] . '[width]', $value['default']['width'] );
									$height = self::get_option( $value['id'] . '[height]', $value['default']['height'] );
									$crop 	= checked( 1, self::get_option( $value['id'] . '[crop]', $value['default']['crop'] ), false );

									?><tr valign="top">
										<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?> <?php echo $tip; ?></th>
										<td class="forminp image_width_settings">

											<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo $width; ?>" autocomplete="off" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo $height; ?>" autocomplete="off" />px

											<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" <?php echo $crop; ?> /> <?php _e( 'Hard Crop?', 'email-control' ); ?></label>

											</td>
									</tr><?php
								break;

								// Single page selects
								case 'single_select_page' :

									$args = array( 'name'				=> $value['id'],
												   'id'					=> $value['id'],
												   'sort_column' 		=> 'menu_order',
												   'sort_order'			=> 'ASC',
												   'show_option_none' 	=> ' ',
												   'field_class'		=> $value['field_class'],
												   'echo' 				=> false,
												   'selected'			=> absint( self::get_option( $value['id'] ) )
												   );

									if ( isset( $value['args'] ) )
										$args = wp_parse_args( $value['args'], $args );

									?><tr valign="top" class="single_select_page">
										<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?> <?php echo $tip; ?></th>
										<td class="forminp">
											<?php echo str_replace(' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'email-control' ) .  "' style='" . $value['css'] . "' class='" . $value['field_class'] . "' id=", wp_dropdown_pages( $args ) ); ?> <?php echo $description; ?>
										</td>
									</tr><?php
								break;

								// Single country selects
								case 'single_select_country' :
									$country_setting = (string) self::get_option( $value['id'] );
									
									if ( class_exists('WC') )
										$countries       = WC()->countries->countries;
									else
										$countries       = $woocommerce->countries->countries;

									if ( strstr( $country_setting, ':' ) ) {
										$country_setting = explode( ':', $country_setting );
										$country         = current( $country_setting );
										$state           = end( $country_setting );
									}
									else {
										$country = $country_setting;
										$state   = '*';
									}
									?><tr valign="top">
										<th scope="row" class="titledesc">
											<label><?php echo esc_html( $value['title'] ); ?></label>
											<?php echo $tip; ?>
										</th>
										<td class="forminp"><select name="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" data-placeholder="<?php _e( 'Choose a country&hellip;', 'email-control' ); ?>" title="Country" class="chosen_select">
											<?php
											if ( class_exists('WC') )
												WC()->countries->country_dropdown_options( $country, $state );
											else
												$woocommerce->countries->country_dropdown_options( $country, $state );
											?>
										</select> <?php echo $description; ?>
										</td>
									</tr><?php
								break;

								// Country multiselects
								case 'multi_select_countries' :

									$selections = (array) self::get_option( $value['id'] );

									if ( !empty( $value['options'] ) )
										$countries = $value['options'];
									else
										$countries = WC()->countries->countries;

									asort( $countries );
									?><tr valign="top">
										<th scope="row" class="titledesc">
											<label><?php echo esc_html( $value['title'] ); ?></label>
											<?php echo $tip; ?>
										</th>
										<td class="forminp">
											<select multiple="multiple" name="<?php echo esc_attr( $value['id'] ); ?>[]" style="width:350px" data-placeholder="<?php _e( 'Choose countries&hellip;', 'email-control' ); ?>" title="Country" class="chosen_select">
												<?php
													if ( $countries )
														foreach ( $countries as $key => $val )
															echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $selections ), true, false ).'>' . $val . '</option>';
												?>
											</select> <?php if ( $description ) echo $description; ?> </br><a class="select_all button" href="#"><?php _e( 'Select all', 'email-control' ); ?></a> <a class="select_none button" href="#"><?php _e( 'Select none', 'email-control' ); ?></a>
										</td>
									</tr><?php
								break;

								// Default: run an action
								default:
									do_action( 'woocommerce_admin_field_' . $value['type'], $value );
								break;
							}
							
						endif;
						
					endforeach;
					?>
				</div>
			</div>
			
			<?php
			
		endforeach;
	}
	
	/**
	 * Get a setting from the settings API.
	 *
	 * @param	mixed $option
	 * @return	string
	 */
	public static function get_option( $option_name, $default = '' ) {
		
		if ( strstr( $option_name, '[' ) ) {
			
			// Array value
			parse_str( $option_name, $option_array );

			// Option name is first key
			$option_name = current( array_keys( $option_array ) );

			// Get value
			$option_values = get_option( $option_name, '' );

			$key = key( $option_array[ $option_name ] );

			if ( isset( $option_values[ $key ] ) )
				$option_value = $option_values[ $key ];
			else
				$option_value = null;

		}
		else {
			
			// Single value
			$option_value = get_option( $option_name, null );
		}

		if ( is_array( $option_value ) )
			$option_value = array_map( 'stripslashes', $option_value );
		elseif ( !is_null( $option_value ) )
			$option_value = stripslashes( $option_value );

		return $option_value === null ? $default : $option_value;
	}
	
	/**
	 * Save admin fields.
	 *
	 * Loops though the options array and save each field.
	 *
	 * @access	public
	 * @param 	array $options Opens array to output
	 * @return	bool
	 */
	public static function save_fields( $options ) {
		
		// if ( empty( $_POST ) )
		// 	return false;

		// Options to update will be stored here
		$update_options = array();

		// Loop options and get values to save
		foreach ( $options as $value ) {

			if ( !isset( $value['id'] ) )
				continue;

			$type = isset( $value['type'] ) ? sanitize_title( $value['type'] ) : '';

			// Get the option name
			$option_value = null;

			switch ( $type ) {
				
				// CX
				
				case 'textarea' :

					if ( isset( $_POST[$value['id']] ) ) {
						$option_value = wp_kses_post( trim( stripslashes( $_POST[ $value['id'] ] ) ) );
					}
					else {
						$option_value = '';
					}

				break;

				case 'text' :
				case 'email':
				case 'number':
				case 'select' :
				case 'color' :
				case 'password' :
				case 'single_select_page' :
				case 'single_select_country' :
				case 'radio' :
				case 'image_upload' :

					if ( $value['id'] == 'woocommerce_price_thousand_sep' || $value['id'] == 'woocommerce_price_decimal_sep' ) {

						// price separators get a special treatment as they should allow a spaces (don't trim)
						if ( isset( $_POST[ $value['id'] ] )  ) {
							$option_value = wp_kses_post( stripslashes( $_POST[ $value['id'] ] ) );
						}
						else {
							$option_value = '';
						}

					}
					elseif ( $value['id'] == 'woocommerce_price_num_decimals' ) {

						// price separators get a special treatment as they should allow a spaces (don't trim)
						if ( isset( $_POST[ $value['id'] ] )  ) {
							$option_value = absint( $_POST[ $value['id'] ] );
						}
						else {
						   $option_value = 2;
						}

					}
					elseif ( $value['id'] == 'woocommerce_hold_stock_minutes' ) {

						// Allow > 0 or set to ''
						if ( !empty( $_POST[ $value['id'] ] )  ) {
							$option_value = absint( $_POST[ $value['id'] ] );
						}
						else {
							$option_value = '';
						}

						wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );

						if ( $option_value != '' )
							wp_schedule_single_event( time() + ( absint( $option_value ) * 60 ), 'woocommerce_cancel_unpaid_orders' );

					}
					else {

					   if ( isset( $_POST[$value['id']] ) ) {
							$option_value = wc_clean( stripslashes( $_POST[ $value['id'] ] ) );
						}
						else {
							$option_value = '';
						}

					}

				break;
				
				// /CX

				// Standard types
				case 'checkbox' :

					if ( isset( $_POST[ $value['id'] ] ) ) {
						$option_value = 'yes';
					}
					else {
						$option_value = 'no';
					}

				break;

				// Special types
				case 'multiselect' :
				case 'multi_select_countries' :

					// Get countries array
					if ( isset( $_POST[ $value['id'] ] ) )
						$selected_countries = array_map( 'wc_clean', array_map( 'stripslashes', (array) $_POST[ $value['id'] ] ) );
					else
						$selected_countries = array();

					$option_value = $selected_countries;

				break;

				case 'image_width' :

					if ( isset( $_POST[$value['id'] ]['width'] ) ) {

						$update_options[ $value['id'] ]['width']  = wc_clean( stripslashes( $_POST[ $value['id'] ]['width'] ) );
						$update_options[ $value['id'] ]['height'] = wc_clean( stripslashes( $_POST[ $value['id'] ]['height'] ) );

						if ( isset( $_POST[ $value['id'] ]['crop'] ) )
							$update_options[ $value['id'] ]['crop'] = 1;
						else
							$update_options[ $value['id'] ]['crop'] = 0;

					}
					else {
						$update_options[ $value['id'] ]['width'] 	= $value['default']['width'];
						$update_options[ $value['id'] ]['height'] 	= $value['default']['height'];
						$update_options[ $value['id'] ]['crop'] 	= $value['default']['crop'];
					}

				break;

				// Custom handling
				default :

					do_action( 'woocommerce_update_option_' . $type, $value );

				break;

			}

			if ( !is_null( $option_value ) ) {
				// Check if option is an array
				if ( strstr( $value['id'], '[' ) ) {

					parse_str( $value['id'], $option_array );

					// Option name is first key
					$option_name = current( array_keys( $option_array ) );

					// Get old option value
					if ( !isset( $update_options[ $option_name ] ) )
						 $update_options[ $option_name ] = get_option( $option_name, array() );

					if ( !is_array( $update_options[ $option_name ] ) )
						$update_options[ $option_name ] = array();

					// Set keys and value
					$key = key( $option_array[ $option_name ] );

					$update_options[ $option_name ][ $key ] = $option_value;

				// Single value
				}
				else {
					$update_options[ $value['id'] ] = $option_value;
				}
			}

			// Custom handling
			do_action( 'woocommerce_update_option', $value );
		}

		// Now save the options
		foreach( $update_options as $name => $value ) {
			if ( str_replace(PHP_EOL, "\n", $value) != str_replace(PHP_EOL, "\n", self::get_option_array($name, 'default')) ) {
				update_option( $name, $value );
				//update_option( $name, self::get_option_array($name, 'default') );
			}
			else{
				delete_option( $name );
			}
		}

		return true;
	}
	
	/**
	 * Save Defaults
	 *
	 * @deprecated 2.0 Never used - rather use sane defaults
	 *
	 */
	function save_defaults( $id, $settings ) {
		
		foreach ($settings as $setting_key => $setting_args) {
					
			$template_id_part = $id . "_";
			$settings_group_part = $setting_args["group"] . "_";
			
			$field_id = "ec_" . $template_id_part . $settings_group_part . $setting_args["id"];
			
			if ( get_option($field_id)=='' && isset($setting_args["default"]) ) {
				update_option( $field_id, $setting_args["default"] );
			}
		}
		
	}
	
	/**
	 * Get a setting from the settings API.
	 *
	 * @param 	mixed $option
	 * @return	string Value of the option.
	 */
	public static function get_option_array( $option_name, $option_key = false ) {
		
		global $ec_cache_options;
		
		$return_value = false;
		
		if ( ! isset( $ec_cache_options ) ) {
			$ec_cache_options = ec_get_settings();
		}
		
		if ( isset( $ec_cache_options[$option_name] ) ) {
			$return_value = $ec_cache_options[$option_name];
		}
		
		// No option array found return false
		if ( ! $return_value ) {
			return false;
		}
		
		$defaults = array(
			'type'		=> '',
			'default'	=> '',
		);
		
		$return_value = wp_parse_args( $return_value, $defaults );
		
		
		if ( $option_key ) {
			if ( isset( $return_value[ $option_key ] ) )
				$return_value = $return_value[ $option_key ];
			else
				$return_value = false;
		}
		
		return $return_value;
	}
	
	/**
	 * Render Option
	 *
	 * @param mixed $option
	 * @return string
	 */
	
	public static function ec_default_option( $default ) {
		
		$option_name  = str_replace( 'default_option_', '', current_filter() );
		$option_value = self::get_option_array( $option_name, 'default' );
		$option_value = EC_Settings::ec_render_option( $option_name, $option_value );
		
		return $option_value;
	}
	
	/**
	 * Render Option
	 *
	 * @param 	mixed $option
	 * @return	string
	 */
	public static function ec_render_option( $option_name, $option_value = '' ) {
		
		if ( ! $option_value ) {
			$option_value = self::get_option_array($option_name, 'default');
		}
		
		if ( isset( $_REQUEST[$option_name] ) ) {
			$option_value = stripslashes( $_REQUEST[$option_name] );
		}
		
		$option_value = __( $option_value, 'email-control' ); // Translation.
		$option_value = do_shortcode( $option_value ); // Convert shortcodes.
		$option_value = wptexturize( $option_value ); // Texturize all fields.
		
		// stylise certain content types, eg textarea
		if ( in_array( self::get_option_array( $option_name, 'type' ), array( 'textarea' ) ) ) {
			$option_value = wpautop( $option_value ); // Auto paragraphs.
		}
		
		$option_value = __( $option_value, 'email-control' ); // Translation - pretty pointless tho after the dynamic shortcodes, and textures happen.
		
		return $option_value;
	}
	
	/**
	 * Helper to check whether to show shortcode elements.
	 */
	public static function check_display( $shortcode_args, $check ) {
		
		//Set to arrays
		$shortcode_args['show'] = array_map('trim', explode(',', $shortcode_args['show'] ));
		$shortcode_args['hide'] = array_map('trim', explode(',', $shortcode_args['hide'] ));
		
		//Remove any show's that have been set as hide's
		foreach ($shortcode_args['hide'] as $key => $value) {
			if ( in_array($value, $shortcode_args['show'] ) ) {
				unset( $shortcode_args['show'] [array_search( $value, $shortcode_args['show'] )] );
			}
		}
		
		return in_array( $check, $shortcode_args['show'] );
	}
	
	public static function ec_normalize_template_args() {
		
		global $ec_template_args;
		
		$defaults = array(
			'user_id'       => '' ,
			'user_login'    => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**username**' : '' ,
			'user_nicename' => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**nicename**' : '' ,
			'email'         => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**email**' : '' ,
			'first_name'    => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**firstname**' : '' ,
			'last_name'     => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**lastname**' : '' ,
		);
		
		$ec_template_args = wp_parse_args( $ec_template_args, $defaults );
		
		if ( isset( $ec_template_args['order'] ) ) {
			
			$order = $ec_template_args['order'];
			
			if ( ( $user_id = get_post_meta( $order->id, '_customer_user', true ) ) ) {
				
				$user = get_user_by( 'id', $user_id );
				
				// All emails with Orders in them - with an existing user account
				
				if ( isset( $user->user_login ) ) $ec_template_args['user_login'] = $user->user_login ; // brentvr
				if ( isset( $user->user_nicename ) ) $ec_template_args['user_nicename'] = $user->user_nicename ; // BrentVR
				if ( $billing_email = get_post_meta( $order->id, '_billing_email', true ) ) $ec_template_args['email'] = $billing_email ; // brentvr@gmail.com
				if ( $billing_first_name = get_post_meta( $order->id, '_billing_first_name', true ) ) $ec_template_args['first_name'] = $billing_first_name ; // Brent
				if ( $billing_last_name = get_post_meta( $order->id, '_billing_last_name', true ) ) $ec_template_args['last_name'] = $billing_last_name ; // VanRensburg
			}
			else{
				
				// All emails with Orders in them - without a user account
				
				$ec_template_args['user_login'] = ''; //$user->user_login; // brentvr
				$ec_template_args['user_nicename'] = ''; //$user->user_nicename; // BrentVR
				if ( $billing_email = get_post_meta( $order->id, '_billing_email', true ) ) $ec_template_args['email'] = get_post_meta( $order->id, '_billing_email', true ); // brentvr@gmail.com
				if ( $billing_first_name = get_post_meta( $order->id, '_billing_first_name', true ) ) $ec_template_args['first_name'] = get_post_meta( $order->id, '_billing_first_name', true ); // Brent
				if ( $billing_last_name = get_post_meta( $order->id, '_billing_last_name', true ) ) $ec_template_args['last_name'] = get_post_meta( $order->id, '_billing_last_name', true ); // VanRensburg
			}
		}
		elseif ( isset( $ec_template_args['user_login'] ) ) {
			
			// New account email.
			
			$user = get_user_by( 'login', $ec_template_args['user_login'] );
			
			if ( isset( $user->ID ) ) $ec_template_args['user_id'] = $user->ID; // 2
			if ( isset( $user->user_login ) ) $ec_template_args['user_login'] = $user->user_login; // brentvr@gmail.com
			if ( isset( $user->user_nicename ) ) $ec_template_args['user_nicename'] = $user->user_nicename; // brentvr@gmail.com
			if ( isset( $user->user_email ) ) $ec_template_args['email'] = $user->user_email; // brentvr@gmail.com
			
			if ( ( $first_name = get_user_meta( $ec_template_args['user_id'], 'first_name', true ) ) )
				$ec_template_args['first_name'] = $first_name; // Brent
			elseif ( isset( $_POST['billing_first_name'] ) )
				$ec_template_args['first_name'] = $_POST['billing_first_name']; // Brent
			
			if ( ( $last_name = get_user_meta( $ec_template_args['user_id'], 'last_name', true ) ) )
				$ec_template_args['last_name'] = $last_name; // Van Rensburg
			elseif ( isset( $_POST['billing_last_name'] ) )
				$ec_template_args['last_name'] = $_POST['billing_last_name']; // Van Rensburg
		}
	}
	
	public static function ec_user_login( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		$content = esc_html( $ec_template_args['user_login'] );
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_user_login">' . $content . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_firstname( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		// Check if necessary template args exits
		if ( !isset( $ec_template_args['first_name'] ) ) return;
		
		$content = $ec_template_args['first_name'];
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_firstname">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_lastname( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		// Check if necessary template args exits
		if ( !isset( $ec_template_args['last_name'] ) ) return;
		
		$content = $ec_template_args['last_name'];
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_lastname">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_email( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		// Check if necessary template args exits
		if ( !isset( $ec_template_args['email'] ) ) return;
		
		$content = $ec_template_args['email'];
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_lastname">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_order( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => '#, number, date, link, container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		// Check if this shortcode can be used here
		if ( !isset( $ec_template_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
		
		// Check if necessary template args exits
		if ( !isset( $ec_template_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">[' . __( 'Order shortcodes cannot be used in this email', 'email-control' ) . ']</span>';
			else return;
		}
		
		if ( isset( $ec_template_args['sent_to_admin'] ) && $ec_template_args['sent_to_admin'] ) {
			
			//Admin Order URL
			$order_url = admin_url( 'post.php?post=' . $ec_template_args['order']->id . '&action=edit' );
		}
		else {
			
			//Front End Order URL
			$order_url = $ec_template_args['order']->get_view_order_url();
		}
		
		//start the return output.
		$content = '';
		
		if ( self::check_display( $shortcode_args, '#' ) || self::check_display( $shortcode_args, 'number' ) ) {
			
			if ( self::check_display( $shortcode_args, 'link' ) ) {
				$content .= '<a href="' . $order_url . '">';
			}
			
			if ( self::check_display( $shortcode_args, '#' ) )
				$content .= '#';
			if ( self::check_display( $shortcode_args, 'number' ) )
				$content .= ltrim( $ec_template_args['order']->get_order_number(), '#' );
			
			if ( self::check_display( $shortcode_args, 'link' ) ) {
				$content .= '</a>';
			}
			
			//Add space.
			$content .= " ";
		}
		
		if ( self::check_display( $shortcode_args, 'date' ) ) {
			$content .= '<span class="ec_datetime">(' . sprintf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $ec_template_args['order']->order_date ) ), date_i18n( wc_date_format(), strtotime( $ec_template_args['order']->order_date ) ) ) . ')</span>';
			
			//Add space.
			$content .= " ";
		}
		
		//Trim spaces beginning and end.
		$content = trim($content);
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_order">' . $content . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_customer_note( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		// Check if this shortcode can be used here
		if ( !isset( $ec_template_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
		if ( isset( $_REQUEST["ec_email_type"] ) && $_REQUEST["ec_email_type"] !== 'customer_note' ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
			
		$content = '';
		
		// Check if necessary template args exits
		if ( isset( $ec_template_args['customer_note'] ) ) {
			$content = $ec_template_args['customer_note'];
		}
		elseif ( isset( $_REQUEST["ec_email_template"] ) ) {
			$content = '*** Note will be inserted here ***';
		}
		
		if ( '' == $content ) {
			return;
		}
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_customer_note">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_account_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		$link = get_permalink( wc_get_page_id( 'myaccount' ) );
		
		ob_start();
		?><a href="<?php echo $link; ?>"><?php echo $link; ?></a><?php
		$content = ob_get_clean();
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_account_link">' . $content . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_reset_password_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		$query_args = array();
		
		if ( isset( $ec_template_args['reset_key'] ) && isset( $ec_template_args['user_login'] ) ) {
			$query_args = array(
				'key' => $ec_template_args['reset_key'],
				'login' => rawurlencode( $ec_template_args['user_login'] )
			);
		}
		
		$link = esc_url_raw( add_query_arg(
			$query_args,
			wc_get_endpoint_url( 'lost-password', '', get_permalink( wc_get_page_id( 'myaccount' ) ) )
		) );
		
		ob_start();
		?><a href="<?php echo $link; ?>"><?php _e( 'Click here to reset your password', 'email-control' ); ?></a><?php
		$content = ob_get_clean();
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_reset_password_link">' . $content . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_user_password( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		// Check if this shortcode can be used here
		if ( isset( $ec_template_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
		
		$content = '';
		
		// Check if necessary template args exits
		if ( isset( $ec_template_args['user_pass'] ) ) {
			$content = esc_html( $ec_template_args['user_pass'] );
		}
		elseif ( isset( $_REQUEST["ec_email_template"] ) ) {
			$content = '#%ZiZi$%#kL#';
		}
		
		if ( '' == $content ) {
			return;
		}
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_user_password">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_pay_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		// Check if this shortcode can be used here
		if ( !isset( $ec_template_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
		
		// Check if necessary template args exits
		$link = esc_url_raw( $ec_template_args['order']->get_checkout_payment_url() );
		if ( ! $link ) return;
		
		ob_start();
		?><a href="<?php echo $link; ?>"><?php _e( 'Pay now', 'email-control' ) ?></a><?php
		$content = ob_get_clean();
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_pay_link">' . $content . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_login_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		$link = get_permalink( wc_get_page_id( 'myaccount' ) );
		
		ob_start();
		?><a href="<?php echo $link; ?>"><?php echo $link; ?></a><?php
		$content = ob_get_clean();
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_login_link">' . $content . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_site_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		ob_start();
		?><a href="<?php echo esc_url_raw( get_site_url() ) ?>"><?php echo get_bloginfo( 'name' ); ?></a><?php
		$content = ob_get_clean();
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_site_link">' . $content . '</span>';
		}
		
		return $content;
	}
	
	public static function ec_site_name( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		//Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_template_args;
		self::ec_normalize_template_args();
		
		$content = get_bloginfo( 'name' );
		
		//Add Container (optional).
		if ( self::check_display( $shortcode_args, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_site_name">' . $content . '</span>';
		}
		
		return $content;
	}
	
}



/**
 * Get Settings
 *
 * Gets modified array of uniqe-ified keys for optionally specific selected settings.
 *
 * @global array $ec_email_templates	Registered templates
 * @param string $template_id			Optional. Template id of settings to get
 * @param string $group			Optional. Template type eg New Order
 */
if ( !function_exists('ec_get_settings') ) {
	function ec_get_settings( $template_id = false, $filter_args = array() ) {
		
		global $ec_email_templates;
		
		//Set default filtred array of templates, incase get_settings isn't specific about an id
		$ec_email_templates_filtered = $ec_email_templates;
		
		
		$settings_all = array();
		
		
		if ( $template_id ) {
			if ( isset( $ec_email_templates[$template_id] ) ) {
				// assign just the settings that id is looking for
				$ec_email_templates_filtered = array();
				$ec_email_templates_filtered[$template_id] = $ec_email_templates[$template_id];
			}
			else{
				//if nothing is found base on template_id then return nothing
				return false;
			}
		}
		
		foreach ( $ec_email_templates_filtered as $ec_email_templates_filtered_key => $ec_email_templates_filtered_value ) {
			
			// Only do anything if there are acutally settings been set by thte template
			if ( isset( $ec_email_templates_filtered_value["settings"] ) && is_array($ec_email_templates_filtered_value["settings"]) ) {
				
				$settings = $ec_email_templates_filtered_value["settings"];
				
				// Filter out anything specified in the filter args array - eg Email Types - "new_order"
				foreach ( $filter_args as $filter_key => $filter_value ) {
					
					$filtered_settings = array();
					
					foreach ($settings as $setting_key => $setting_args ) {
						
						if ( isset( $setting_args[$filter_key] ) && $setting_args[$filter_key] == $filter_value ) // || $setting_args["group"] == 'all'
							$filtered_settings[] = $setting_args;
					}
					
					$settings = $filtered_settings;
					
				}
				
				
				
				// Rename ID's to make them more unique. eg heading becomes ec_deluxe_new_order_heading
				$renamed_id_settings = array();
				foreach ($settings as $setting_key => $setting_args) {
					
					// Don't id change on sections
					if ( $setting_args["type"] != "section" ) {
						
						$template_id_part = $ec_email_templates_filtered_key . "_";
						//$settings_group_part = ( $setting_args["group"] != 'all' ) ?  $setting_args["group"] . "_" : "" ;
						$settings_group_part = $setting_args["group"] . "_";
						
						$setting_args["id"] = "ec_" . $template_id_part . $settings_group_part . $setting_args["id"];
					}
					$renamed_id_settings[ $setting_args["id"] ] = $setting_args;
					
				}
				
				$settings_all = array_merge( $settings_all, $renamed_id_settings );
			}
			
		}
		
		
		if ( count($settings_all) )
			return $settings_all;
		else
			return false;
		
		
	}
}