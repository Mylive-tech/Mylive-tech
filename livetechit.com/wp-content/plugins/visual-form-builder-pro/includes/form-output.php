<?php
$vfb_post = $vfb_design = $vfb_design_theme = '';
// Run Create User add-on
if ( class_exists( 'VFB_Pro_Create_Post' ) )
	$vfb_post = new VFB_Pro_Create_Post();

global $wpdb;

// Get global settings
$vfb_settings 	= get_option( 'vfb-settings' );

// Settings - Prepend Confirmation
$settings_prepend_confirm	= isset( $vfb_settings['prepend-confirm'] ) ? true : false;

// Settings - Place Address labels above fields
$settings_address_labels	= isset( $vfb_settings['address-labels'] ) ? false : true;

// Extract shortcode attributes, set defaults
extract( shortcode_atts( array(
	'id' => ''
	), $atts )
);

// Add JavaScript files to the front-end, only once
if ( !$this->add_scripts )
	$this->scripts();

// Get form id.  Allows use of [vfb id=1] or [vfb 1]
$form_id = ( isset( $id ) && !empty( $id ) ) ? (int) $id : key( $atts );

// If form is submitted, show success message, otherwise the form
if ( isset( $_POST['vfb-submit'] ) && isset( $_POST['form_id'] ) && $_POST['form_id'] == $form_id ) {
	$output = $this->confirmation();
	if ( !apply_filters( 'vfb_prepend_confirmation', $settings_prepend_confirm, $form_id ) )
		return;
}

// Only display published forms (except in Preview mode)
$form_status = !defined( 'VFB_PRO_PREVIEW' ) ? ' AND form_status = "publish"' : '';

// Get forms
$order = sanitize_sql_orderby( 'form_id DESC' );
$form  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->form_table_name WHERE form_id = %d {$form_status} ORDER BY $order", $form_id ) );

// Return if no form found
if ( !$form )
	return;

// Get fields
$order_fields   = sanitize_sql_orderby( 'field_sequence ASC' );
$fields         = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE form_id = %d ORDER BY $order_fields", $form_id ) );

// Page count
$page_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( field_type ) + 1 FROM $this->field_table_name WHERE form_id = %d AND field_type = 'page-break';", $form_id ) );

// Entries count
$entries_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $this->entries_table_name WHERE form_id = %d", $form_id ) );

// Conditional rules
$rules = $this->get_conditional_fields( $form_id );

// Setup default variables needed
$count = 1;
$page_num = 0;
$page = $total_page = $verification = $recaptcha = '';
$open_fieldset = $open_section = $open_page = false;
$submit = 'Submit';

// Scheduling variables
$entries_allowed 	= !empty( $form->form_entries_allowed ) ? $form->form_entries_allowed : false;
$entries_schedule 	= maybe_unserialize( $form->form_entries_schedule );
$current_time	 	= current_time( 'timestamp' );
$schedule_start		= ( isset( $entries_schedule['start'] ) ) ? strtotime( $entries_schedule['start'] ) : false;
$schedule_end		= ( isset( $entries_schedule['end'] ) ) ? strtotime( $entries_schedule['end'] ) : false;
$unique_entry		= absint( $form->form_unique_entry );

$submissions_off = apply_filters( 'vfb_submissions_off_message', sprintf( '<p class="vfb-entries-allowed">%s</p>', __( 'Sorry, but this form is no longer accepting submissions.', 'visual-form-builder-pro' ) ), $entries_count, $entries_allowed );
$only_uniques 	 = apply_filters( 'vfb_submissions_unique_message', sprintf( '<p class="vfb-entries-allowed">%s</p>', __( 'Sorry, this form only accepts one submission per user.', 'visual-form-builder-pro' ) ) );

// Find matching IPs, if option is on
if ( $unique_entry )
	$matching_ips = $wpdb->get_var( $wpdb->prepare( "SELECT ip_address FROM $this->entries_table_name WHERE form_id = %d AND ip_address = %s", $form_id, esc_html( $_SERVER['REMOTE_ADDR'] ) ) );


// Check for # entries allowed and hide form, if needed
if ( $entries_allowed && ( $entries_count >= $entries_allowed ) && !defined( 'VFB_PRO_PREVIEW' ) ) :
	$output = $submissions_off;
elseif ( $schedule_start && ( $current_time <= $schedule_start ) && !defined( 'VFB_PRO_PREVIEW' ) ) :
	$output = $submissions_off;
elseif ( $schedule_end && ( $current_time >= $schedule_end ) && !defined( 'VFB_PRO_PREVIEW' ) ) :
	$output = $submissions_off;
// Only unique IPs allowed
elseif ( $unique_entry && $matching_ips && !defined( 'VFB_PRO_PREVIEW' ) ) :
	$output = $only_uniques;
// Display form as normal
else :

	// Setup Form Design theme CSS class
	if ( class_exists( 'VFB_Pro_Form_Designer' ) ) :
		$vfb_design = new VFB_Pro_Form_Designer();

		$vfb_get_theme = $vfb_design->get_theme( $form_id );

		if ( $vfb_get_theme )
			$vfb_design_theme = esc_attr( "vfb-form-design-theme-$vfb_get_theme" );
	endif;

	// Label alignment
	$label_alignment = ( $form->form_label_alignment !== '' ) ? esc_attr( " $form->form_label_alignment" ) : '';

	// Settings - Remove default SPAM Verification
	$settings_form_verification = isset( $vfb_settings['spam-verification'] ) ? false : $form->form_verification;

	// Set a default for displaying the verification section
	$display_verification = apply_filters( 'vfb_display_verification', $settings_form_verification );

	// Allow the default action to be hooked into
	$action = apply_filters( 'vfb_form_action', '', $form_id );

	// Allow the default enctype to be hooked into
	$enctype = apply_filters( 'vfb_form_enctype', 'multipart/form-data', $form_id );

	// Start form container
	$output .= sprintf( '<div id="vfb-form-%d" class="visual-form-builder-container">', $form_id );

	// Filter for additional output before a form
	$output .= apply_filters( 'vfb_before_form_output', '', $form_id );

	$output .= sprintf(
		'<form id="%1$s-%2$d" class="visual-form-builder %3$s %4$s %5$s" method="post" enctype="%6$s" action="%7$s">
		<input type="hidden" name="form_id" value="%8$d" />',
		esc_html( $form->form_key ),
		$form_id,
		"vfb-form-$form_id",
		$vfb_design_theme,
		$label_alignment,
		$enctype,
		esc_url( $action ),
		absint( $form->form_id )
	);

	// Output Payments add-on
	$output .= $this->payments_output( $form_id );

	foreach ( $fields as $field ) :
		$field_id		= absint( $field->field_id );
		$field_type 	= esc_html( $field->field_type );
		$field_name		= stripslashes( $field->field_name );
		$required_span 	= ( !empty( $field->field_required ) && $field->field_required === 'yes' ) ? ' <span class="vfb-required-asterisk">*</span>' : '';
		$required 		= ( !empty( $field->field_required ) && $field->field_required === 'yes' ) ? esc_attr( ' required' ) : '';
		$validation 	= ( !empty( $field->field_validation ) ) ? esc_attr( " $field->field_validation" ) : '';
		$css 			= ( !empty( $field->field_css ) ) ? stripslashes( str_replace( '&quot;', "'", esc_attr( " $field->field_css" ) ) ) : '';
		$id_attr 		= "vfb-{$field_id}";
		$size			= ( !empty( $field->field_size ) ) ? esc_attr( " vfb-$field->field_size" ) : '';
		$layout 		= ( !empty( $field->field_layout ) ) ? esc_attr( " vfb-$field->field_layout" ) : '';
		$default 		= ( !empty( $field->field_default ) ) ? wp_specialchars_decode( esc_html( stripslashes( $field->field_default ) ), ENT_QUOTES ) : '';
		$conditional	= ( !empty( $field->field_rule ) ) ? esc_attr( ' vfb-conditional' ) : '';
		$description	= ( !empty( $field->field_description ) ) ? wp_specialchars_decode( esc_html( stripslashes( $field->field_description ) ), ENT_QUOTES ) : '';

		// Allow field name to be filtered
		$field_name = apply_filters( 'vfb_field_name', $field_name, $field_id, $form_id );

		// Allow field description to be filtered
		$description = apply_filters( 'vfb_field_desc', $description, $field_id, $form_id );

		// Allow default to be filtered
		$default = apply_filters( 'vfb_field_default', $default, $field_type, $field_id, $form_id );

		$conditional_show = '';
		if ( $field->field_rule ) :
			$field_rule = unserialize( $field->field_rule );
			$conditional_show = ( 'show' == $field_rule['conditional_show'] ) ? esc_attr( ' vfb-conditional-hide' ) : '';
		endif;

		// Close each section
		if ( $open_section == true ) :
			// If this field's parent does NOT equal our section ID
			if ( $sec_id && $sec_id !== absint( $field->field_parent ) ) :
				$output .= '</div><div class="vfb-clear"></div>';
				$open_section = false;
			endif;
		endif;

		// Force an initial fieldset and display an error message to strongly encourage user to add one
		if ( $count === 1 && $field_type !== 'fieldset' ) :
			$output .= sprintf( '<fieldset class="vfb-fieldset"><div class="vfb-legend" style="background-color:#FFEBE8;border:1px solid #CC0000;"><h3>%1$s</h3><p style="color:black;">%2$s</p></div><ul class="section section-%3$d">',
			__( 'Oops! Missing Fieldset', 'visual-form-builder-pro' ),
			__( 'If you are seeing this message, it means you need to <strong>add a Fieldset to the beginning of your form</strong>. Your form may not function or display properly without one.', 'visual-form-builder-pro' ),
			$count
			);

			$count++;
		endif;

		// Fieldset
		if ( $field_type == 'fieldset' ) :
			// Close each fieldset
			if ( $open_fieldset == true )
				$output .= '</ul>&nbsp;</fieldset>';

			if ( $open_page == true && $page !== '' )
				$open_page = false;

			// Only display Legend if field name is not blank
			$legend = !empty( $field_name ) ? sprintf( '<div class="vfb-legend"><h3>%s</h3></div>', $field_name ) : '&nbsp;';

			$output .= sprintf(
				'<fieldset class="vfb-fieldset vfb-fieldset-%1$d %2$s %3$s %4$s %5$s %6$s" id="item-%7$s">%8$s<ul class="vfb-section vfb-section-%1$d">',
				$count,
				esc_attr( $field->field_key ),
				$css,
				$page,
				$conditional,
				$conditional_show,
				$id_attr,
				$legend
			);

			$open_fieldset = true;
			$count++;

		// Section
		elseif ( $field_type == 'section' ) :

			$output .= sprintf(
				'<div id="item-%1$s" class="vfb-section-div %2$s %3$s %4$s"><h4>%5$s</h4>',
				$id_attr,
				$css,
				$conditional,
				$conditional_show,
				$field_name
			);

			// Save section ID for future comparison
			$sec_id = $field_id;
			$open_section = true;

		// Page Break
		elseif ( $field_type == 'page-break' ) :
			$page_num += 1;

			$total_page = sprintf( '<span class="vfb-page-counter">%1$d / %2$d</span>', $page_num, $page_count );

			$output .= sprintf(
				'<li class="vfb-item vfb-item-%1$s" id="item-%2$s"><a href="#" id="page-%3$d" class="vfb-page-next%4$s">%5$s</a> %6$s</li>',
				$field_type,
				$id_attr,
				$page_num,
				$css,
				$field_name,
				$total_page
			);

			$page = " vfb-page page-$page_num";
			$open_page = true;

		// Everything else
		elseif ( !in_array( $field_type, array( 'verification', 'secret', 'submit' ) ) ) :

			$columns_choice = ( !empty( $field->field_size ) && in_array( $field_type, array( 'radio', 'checkbox' ) ) ) ? esc_attr( " vfb-$field->field_size" ) : '';

			if ( $field_type !== 'hidden' ) :

				// Don't add for attribute for certain form items
				$for = !in_array( $field_type, array( 'checkbox', 'radio', 'name', 'time', 'address', 'instructions' ) ) ? ' for="%6$s"' : '';

				$output .= sprintf(
					'<li class="vfb-item vfb-item-%1$s %2$s %3$s %4$s %5$s" id="item-%6$s"><label' . $for . ' class="vfb-desc">%7$s %8$s</label>',
					$field_type,
					$columns_choice,
					$layout,
					$conditional,
					$conditional_show,
					$id_attr,
					$field_name,
					$required_span
				);
			endif;

		// Verification and Secret
		elseif ( in_array( $field_type, array( 'verification', 'secret' ) ) ) :
			// Verification
			if ( $field_type == 'verification' ) :
				$verification .= sprintf(
					'<fieldset class="vfb-fieldset vfb-fieldset-%1$d %2$s %3$s %4$s" id="item-%5$s"><div class="vfb-legend"><h3>%6$s</h3></div><ul class="vfb-section vfb-section-%1$d">',
					$count,
					esc_attr( $field->field_key ),
					$css,
					$page,
					$id_attr,
					$field_name
				);
			endif;

			// Secret
			if ( $field_type == 'secret' ) :
				// Default logged in values
				$logged_in_display = $logged_in_value = '';

				// If the user is logged in, fill the field in for them
				if ( is_user_logged_in() ) :
					// Hide the secret field if logged in
					$logged_in_display = 'style="display:none;"';
					$logged_in_value = 14;

					// Get logged in user details
					$user = wp_get_current_user();
					$user_identity = ! empty( $user->ID ) ? $user->display_name : '';

					// Display a message for logged in users
					$logged_in_as = sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. Verification not required.', 'visual-form-builder-pro' ), admin_url( 'profile.php' ), $user_identity );
					$logged_in_as = apply_filters( 'vfb_str_logged_in_as', $logged_in_as, $form_id );

					$verification .= sprintf(
						'<li class="vfb-item" id="%1$s">%2$s</li>',
						$id_attr,
						$logged_in_as
					);
				else :
					$recaptcha_setting	= maybe_unserialize( $field->field_options );
					$use_recaptcha 		= isset( $recaptcha_setting['setting'] ) ? $recaptcha_setting['setting'] : false;

					if ( $use_recaptcha ) :
						if ( !function_exists( 'recaptcha_get_html' ) )
	                        require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'libraries/recaptchalib.php' );

	                    $vfb_settings  = get_option( 'vfb-settings' );
	                    $public_key    = $vfb_settings['recaptcha-public-key'];

						if ( empty( $public_key ) ) :
							$recaptcha .= __( 'reCAPTCHA Public Key not found. Both Public and Private keys must be set in order for reCAPTCHA to function.', 'visual-form-builder-pro' );
						else :
		                    $is_ssl = !empty( $_SERVER['HTTPS'] );
							$recaptcha_theme  = apply_filters( 'vfb_recaptcha_theme', 'white', $form_id );
							$recaptcha_lang   = apply_filters( 'vfb_recaptcha_lang', 'en', $form_id );
							$recaptcha_opts   = apply_filters( 'vfb_recaptcha_opts', '', $form_id );

							$recaptcha .= sprintf(
								'<script type="text/javascript">var RecaptchaOptions = {theme : "%1$s", lang : "%2$s"%3$s}</script>',
								$recaptcha_theme,
								$recaptcha_lang,
								$recaptcha_opts
							);
		                    $recaptcha .= recaptcha_get_html( $public_key, null, $is_ssl );
						endif;
                    endif;

				endif;

				$verification .= sprintf(
					'<li class="vfb-item vfb-item-%1$s" %2$s><label for="item-%3$s" class="vfb-desc">%4$s%5$s</label>',
					$field_type,
					$logged_in_display,
					$id_attr,
					$field_name,
					$required_span
				);

				// Set variable for testing if required is Yes/No
				$verification .= ( empty( $required ) ) ? '<input type="hidden" name="_vfb-required-secret" value="0" />' : '';

				// Set hidden secret to matching input
				$verification .= sprintf( '<input type="hidden" name="_vfb-secret" value="vfb-%d" />', $field_id );

				$validation = '{digits:true,maxlength:2,minlength:2}';

				if ( $recaptcha ) :
					$verification_item = $recaptcha;
				else :
				$verification_item = sprintf(
					'<input type="text" name="vfb-%1$d" id="item-%2$s" value="%3$s" class="vfb-text %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					$logged_in_value,
					$size,
					$required,
					$validation,
					$css
				);
				endif;

				$verification .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $verification_item, $description ) : $verification_item;

			endif;
		endif;

		switch ( $field_type ) :
			case 'text' :
			case 'email' :
			case 'url' :
			case 'currency' :
			case 'number' :
			case 'phone' :
			case 'ip-address' :

				// HTML5 types
				if ( in_array( $field_type, array( 'email', 'url' ) ) )
					$type = esc_attr( $field_type );
				elseif ( 'phone' == $field_type )
					$type = 'tel';
				else
					$type = 'text';

				$form_item = sprintf(
					'<input type="%8$s" name="vfb-%1$d" id="%2$s" value="%3$s" class="vfb-text %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$validation,
					$css,
					$type
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $form_item, $description ) : $form_item;

				break;

			case 'textarea' :

				$options = maybe_unserialize( $field->field_options );
				$min = ( isset( $options['min'] ) ) ? $options['min'] : '';
				$max = ( isset( $options['max'] ) ) ? $options['max'] : '';

				$words = $words_display = '';

				// Initial count if default set
				$word_count = str_word_count( $default );

				// Setup word count messages
				$words_message = array(
					'range'	=> sprintf( __( 'Must be between %s and %s words. Total words: %s', 'visual-form-builder-pro' ), "<strong>$min</strong>", "<strong>$max</strong>", "<strong class='vfb-word-count-total'>$word_count</strong>" ),
					'max'	=> sprintf( __( 'Maximum words allowed: %s. Total words: %s', 'visual-form-builder-pro' ), "<strong>$max</strong>", "<strong class='vfb-word-count-total'>$word_count</strong>" ),
					'min'	=> sprintf( __( 'Minimum words allowed: %s. Total words: %s', 'visual-form-builder-pro' ), "<strong>$min</strong>", "<strong class='vfb-word-count-total'>$word_count</strong>" )
				);

				$words_message = apply_filters( 'vfb_word_count_message', $words_message, $min, $max, $word_count, $form_id );

				// If Min and Max words are set, use Range words
				if ( !empty( $min ) && !empty( $max ) ) {
					$words = ' {rangeWords:[' . $min . ',' . $max . ']} vfb-textarea-word-count';
					$words_display = "<label class='vfb-word-count'>{$words_message['range']}</label>";
				}
				// If Min is empty and Max is set, use Max words
				elseif ( empty( $min ) && !empty( $max ) ) {
					$words = ' {maxWords:[' . $max . ']} vfb-textarea-word-count';
					$words_display = "<label class='vfb-word-count'>{$words_message['max']}</label>";
				}
				// If Min is set and Max is empty, use Min words
				elseif ( !empty( $min ) && empty( $max ) ) {
					$words = ' {minWords:[' . $min . ']} vfb-textarea-word-count';
					$words_display = "<label class='vfb-word-count'>{$words_message['min']}</label>";
				}

				$form_item = sprintf(
					'<textarea name="vfb-%1$d" id="%2$s" class="vfb-textarea %4$s %5$s %6$s %7$s">%3$s</textarea>',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$css,
					$words
				);

				$output .= '<div>';

				$output .= ( !empty( $description ) ) ? "<span class='vfb-span'><label>$description</label></span>$form_item $words_display" : $form_item . $words_display;

				$output .= '</div>';

				break;

			case 'select' :
				$field_options = maybe_unserialize( $field->field_options );

				$options = '';

				// Loop through each option and output
				foreach ( $field_options as $option => $value ) {
					$value = $label = trim( stripslashes( $value ) );

					$label = apply_filters( 'vfb_option_name', $label, $option, $field_id, $form_id );

					$options .= sprintf(
						'<option value="%1$s"%3$s>%2$s</option>',
						esc_attr( $value ),
						wp_specialchars_decode( $label ),
						selected( $default, ++$option, 0 )
					);
				}

				$form_item = sprintf(
					'<select name="vfb-%1$d" id="%2$s" class="vfb-select %3$s %4$s %5$s">%6$s</select>',
					$field_id,
					$id_attr,
					$size,
					$required,
					$css,
					$options
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span"><label>%2$s</label></span>%1$s', $form_item, $description ) : $form_item;

				break;

			case 'radio' :

				$field_options = maybe_unserialize( $field->field_options );

				$options = $other = '';

				// Loop through each option and output
				foreach ( $field_options as $option => $value ) {
					$option++;

					$value = $label = trim( stripslashes( $value ) );

					$label = apply_filters( 'vfb_option_name', $label, $option, $field_id, $form_id );

					$options .= sprintf(
						'<span class="vfb-span"><input type="radio" name="vfb-%1$d" id="%2$s-%3$d" value="%6$s" class="vfb-radio %4$s %5$s"%8$s /><label for="%2$s-%3$d" class="vfb-choice">%7$s</label></span>',
						$field_id,
						$id_attr,
						$option,
						$required,
						$css,
						esc_attr( $value ),
						wp_specialchars_decode( $label ),
						checked( $default, $option, 0 )
					);
				}

				// Get 'Allow Other'
				$field_options_other = maybe_unserialize( $field->field_options_other );

				// Display 'Other' field
				if ( isset( $field_options_other['setting'] ) && 1 == $field_options_other['setting'] ) {
					$option++;

					$value = $label = trim( stripslashes( $field_options_other['other'] ) );

					$label = apply_filters( 'vfb_option_name', $label, $option, $field_id, $form_id );

					$other .= sprintf(
						'<span class="vfb-span">
						<input type="radio" name="vfb-%1$d" id="%2$s-%3$d" value="%6$s" class="vfb-radio %4$s %5$s"%8$s />
						<label for="%2$s-%3$d" class="vfb-choice">%7$s</label>
						<input type="text" name="vfb-%1$d-other" id="%2$s-%3$d" value="" class="vfb-text vfb-other">
						</span>',
						$field_id,
						$id_attr,
						$option,
						$required,
						$css,
						esc_attr( $value ),
						wp_specialchars_decode( $value ),
						checked( $default, $option, 0 )
					);
				}


				$form_item = $options . $other;

				$output .= '<div>';

				$output .= ( !empty( $description ) ) ? sprintf( '<span><label>%2$s</label></span>%1$s', $form_item, $description ) : $form_item;

				$output .= '<div style="clear:both"></div></div>';

				break;

			case 'checkbox' :

				$field_options = maybe_unserialize( $field->field_options );

				$options = '';

				// Loop through each option and output
				foreach ( $field_options as $option => $value ) {

					$value = $label = trim( stripslashes( $value ) );

					$label = apply_filters( 'vfb_option_name', $label, $option, $field_id, $form_id );

					$options .= sprintf(
						'<span class="vfb-span"><input type="checkbox" name="vfb-%1$d[]" id="%2$s-%3$d" value="%6$s" class="vfb-checkbox %4$s %5$s"%8$s /><label for="%2$s-%3$d" class="vfb-choice">%7$s</label></span>',
						$field_id,
						$id_attr,
						$option,
						$required,
						$css,
						esc_attr( $value ),
						wp_specialchars_decode( $label ),
						checked( $default, ++$option, 0 )
					);
				}

				$form_item = $options;

				$output .= '<div>';

				$output .= ( !empty( $description ) ) ? sprintf( '<span><label>%2$s</label></span>%1$s', $form_item, $description ) : $form_item;

				$output .= '<div style="clear:both"></div></div>';

				break;

			case 'address' :

				$address = '';

				$address_parts = array(
				    'address'    => array(
				    	'label'    => __( 'Street Address', 'visual-form-builder-pro' ),
				    	'layout'   => 'full'
				    ),
				    'address-2'  => array(
				    	'label'    => __( 'Apt, Suite, Bldg. (optional)', 'visual-form-builder-pro' ),
				    	'layout'   => 'full'
				    ),
				    'city'       => array(
				    	'label'    => __( 'City', 'visual-form-builder-pro' ),
				    	'layout'   => 'left'
				    ),
				    'state'      => array(
				    	'label'    => __( 'State / Province / Region', 'visual-form-builder-pro' ),
				    	'layout'   => 'right'
				    ),
				    'zip'        => array(
				    	'label'    => __( 'Postal / Zip Code', 'visual-form-builder-pro' ),
				    	'layout'   => 'left'
				    ),
				    'country'    => array(
				    	'label'    => __( 'Country', 'visual-form-builder-pro' ),
				    	'layout'   => 'right'
				    )
				);

				$address_parts = apply_filters( 'vfb_address_labels', $address_parts, $form_id );

				$label_placement = apply_filters( 'vfb_address_labels_placement', $settings_address_labels, $form_id );

				$placement_bottom = ( $label_placement ) ? '<label for="%2$s-%4$s">%5$s</label>' : '';
				$placement_top    = ( !$label_placement ) ? '<label for="%2$s-%4$s">%5$s</label>' : '';

				foreach ( $address_parts as $parts => $part ) :

					// Make sure the second address line is not required
					$addr_required = ( 'address-2' !== $parts ) ? $required : '';

					if ( 'country' == $parts ) :

						$options = '';

						foreach ( $this->countries as $country ) {
							$options .= sprintf( '<option value="%1$s"%2$s>%1$s</option>', $country, selected( $default, $country, 0 ) );
						}

						$address .= sprintf(
							'<span class="vfb-%3$s">' . $placement_top . '<select name="vfb-%1$d[%4$s]" class="vfb-select %7$s %8$s" id="%2$s-%4$s">%6$s</select>' . $placement_bottom . '</span>',
							$field_id,
							$id_attr,
							esc_attr( $part['layout'] ),
							esc_attr( $parts ),
							esc_html( $part['label'] ),
							$options,
							$addr_required,
							$css
						);

					else :

						$address .= sprintf(
							'<span class="vfb-%3$s">' . $placement_top . '<input type="text" name="vfb-%1$d[%4$s]" id="%2$s-%4$s" maxlength="150" class="vfb-text vfb-medium %7$s %8$s" />' . $placement_bottom . '</span>',
							$field_id,
							$id_attr,
							esc_attr( $part['layout'] ),
							esc_attr( $parts ),
							esc_html( $part['label'] ),
							$size,
							$addr_required,
							$css
						);

					endif;

				endforeach;

				$output .= '<div>';

				$output .= !empty( $description ) ? "<span class='vfb-span'><label>$description</label></span>$address" : $address;

				$output .= '</div>';

				break;

			case 'date' :
				// Load jQuery UI datepicker library
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'vfb-datepicker-i18n' );

				$options = maybe_unserialize( $field->field_options );
				$dateFormat = ( $options ) ? $options['dateFormat'] : '';

				$form_item = sprintf(
					'<input type="text" name="vfb-%1$d" id="%2$s" value="%3$s" class="vfb-text vfb-date-picker %4$s %5$s %6$s" data-dp-dateFormat="%7$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$css,
					$dateFormat
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $form_item, $description ) : $form_item;

				break;

			case 'time' :

				$hour = $minute = $ampm = '';

				// Get the time format (12 or 24)
				$time_format = str_replace( 'time-', '', $validation );

				$time_format 	= apply_filters( 'vfb_time_format', $time_format, $form_id );
				$total_mins 	= apply_filters( 'vfb_time_min_total', 55, $form_id );
				$min_interval 	= apply_filters( 'vfb_time_min_interval', 5, $form_id );

				// Set whether we start with 0 or 1 and how many total hours
				$hour_start = ( $time_format == '12' ) ? 1 : 0;
				$hour_total = ( $time_format == '12' ) ? 12 : 23;

				// Hour
				for ( $i = $hour_start; $i <= $hour_total; $i++ ) {
					$hour .= sprintf( '<option value="%1$02d">%1$02d</option>', $i );
				}

				// Minute
				for ( $i = 0; $i <= $total_mins; $i += $min_interval ) {
					$minute .= sprintf( '<option value="%1$02d">%1$02d</option>', $i );
				}

				// AM/PM
				if ( $time_format == '12' ) {
					$ampm = sprintf(
						'<span class="vfb-time"><select name="vfb-%1$d[ampm]" id="%2$s-ampm" class="vfb-select %5$s %6$s"><option value="AM">AM</option><option value="PM">PM</option></select><label for="%2$s-ampm">AM/PM</label></span>',
						$field_id,
						$id_attr,
						$hour,
						$minute,
						$required,
						$css
					 );
				}

				$form_item = sprintf(
					'<span class="vfb-time"><select name="vfb-%1$d[hour]" id="%2$s-hour" class="vfb-select %5$s %6$s">%3$s</select><label for="%2$s-hour">HH</label></span>' .
					'<span class="vfb-time"><select name="vfb-%1$d[min]" id="%2$s-min" class="vfb-select %5$s %6$s">%4$s</select><label for="%2$s-min">MM</label></span>' .
					'%7$s',
					$field_id,
					$id_attr,
					$hour,
					$minute,
					$required,
					$css,
					$ampm
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span"><label>%2$s</label></span>%1$s', $form_item, $description ) : $form_item;

				$output .= '<div class="clear"></div>';

				break;

			case 'html' :
				//Load CKEditor library
				wp_enqueue_script( 'vfb-ckeditor' );

				$form_item = sprintf(
					'<textarea name="vfb-%1$d" id="%2$s" class="vfb-textarea ckeditor %4$s %5$s %6$s">%3$s</textarea>',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$css
				);

				$output .= '<div>';

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span"><label>%2$s</label></span>%1$s', $form_item, $description ) : $form_item;

				$output .= '</div>';

				break;

			case 'file-upload' :

				$options = maybe_unserialize( $field->field_options );
				$ext     = apply_filters( 'vfb_upload_file_ext', $options[0], $field_id, $form_id );
				$accept  = !empty( $ext ) ? " {accept:'$ext'}" : '';

				$form_item = sprintf(
					'<input type="file" name="vfb-%1$d" id="%2$s" value="%3$s" class="vfb-text %4$s %5$s %6$s %7$s %8$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$validation,
					$css,
					$accept
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $form_item, $description ) : $form_item;

				break;

			case 'instructions' :

				$output .= $description;

				break;

			case 'name' :

				$first = $last = $title = $suffix = '';

				$format = maybe_unserialize( $field->field_options );

				// Setup word count messages
				$labels = array(
					'title'    => __( 'Title', 'visual-form-builder-pro' ),
					'first'    => __( 'First', 'visual-form-builder-pro' ),
					'last'     => __( 'Last', 'visual-form-builder-pro' ),
					'suffix'   => __( 'Suffix', 'visual-form-builder-pro' ),
				);

				$labels = apply_filters( 'vfb_name_labels', $labels, $form_id );

				// If default value set, separate by space and load into First/Last
				if ( !empty( $default ) )
					list( $first, $last ) = explode( ' ', $default, 2 );

				if ( 'normal' == $format[0] ) :
					$form_item = sprintf(
						'<span class="vfb-name-normal">
						<input type="text" name="vfb-%1$d[first]" id="%2$s-first" value="%3$s" class="vfb-text %5$s %6$s %7$s" /><label for="%2$s-first">%8$s</label>
						</span>
						<span class="vfb-name-normal">
						<input type="text" name="vfb-%1$d[last]" id="%2$s-last" value="%4$s" class="vfb-text %5$s %6$s %7$s" /><label for="%2$s-last">%9$s</label>
						</span>',
						$field_id,
						$id_attr,
						esc_attr( $first ),
						esc_attr( $last ),
						$required,
						$validation,
						$css,
						esc_html( $labels['first'] ),
						esc_html( $labels['last'] )
					);
				else :
					$form_item = sprintf(
						'<span class="vfb-name-extras">
						<input type="text" name="vfb-%1$d[title]" id="%2$s-title" value="" class="vfb-text %5$s %6$s %7$s" size="4" /><label for="%2$s-title">%8$s</label>
						</span>
						<span class="vfb-name-extras">
						<input type="text" name="vfb-%1$d[first]" id="%2$s-first" value="%3$s" class="vfb-text %5$s %6$s %7$s" size="14" /><label for="%2$s-first">%9$s</label>
						</span>
						<span class="vfb-name-extras">
						<input type="text" name="vfb-%1$d[last]" id="%2$s-last" value="%4$s" class="vfb-text %5$s %6$s %7$s" size="14" /><label for="%2$s-last">%10$s</label>
						</span>
						<span class="vfb-name-extras">
						<input type="text" name="vfb-%1$d[suffix]" id="%2$s-suffix" value="" class="vfb-text %5$s %6$s %7$s" size="3" /><label for="%2$s-suffix">%11$s</label>
						</span>',
						$field_id,
						$id_attr,
						esc_attr( $first ),
						esc_attr( $last ),
						$required,
						$validation,
						$css,
						esc_html( $labels['title'] ),
						esc_html( $labels['first'] ),
						esc_html( $labels['last'] ),
						esc_html( $labels['suffix'] )
					);
				endif;

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span"><label>%2$s</label></span>%1$s', $form_item, $description ) : $form_item;

				break;

			case 'username' :

				$form_item = sprintf(
					'<input type="text" name="vfb-%1$d" id="%2$s" value="%3$s" class="vfb-text vfbUsername %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$validation,
					$css
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $form_item, $description ) : $form_item;

				break;

			case 'password' :

				$password_meter = sprintf(
					'<div class="password-meter"><div class="password-meter-message">%1$s</div></div>',
					apply_filters( 'vfb_str_pass_strength', __( 'Password Strength', 'visual-form-builder-pro' ) )
				);

				$form_item = sprintf(
					'<input type="password" name="vfb-%1$d" id="%2$s" value="%3$s" class="vfb-text password %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$validation,
					$css
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $form_item, $description, $password_meter ) : $form_item . $password_meter;

				break;

			case 'hidden' :
				$val = '';

				$current_user = wp_get_current_user();

				// If the options field isn't empty, unserialize and build array
				if ( !empty( $field->field_options ) ) :
					if ( is_serialized( $field->field_options ) )
						$opts_vals = unserialize( $field->field_options );

						switch ( $opts_vals[0] ) :
							case 'form_id' :
								$val = $form_id;
								break;
							case 'form_title' :
								$val = stripslashes( $form->form_title );
								break;
							case 'ip' :
								$val = $_SERVER['REMOTE_ADDR'];
								break;
							case 'uid' :
								$val = uniqid();
								break;
							case 'sequential-num' :
								$seq_num_opt = "vfb-hidden-sequential-num-{$form_id}-{$field_id}";
								$seq_num     = get_option( $seq_num_opt );
								$seq_start   = apply_filters( 'vfb_sequential_num_start', 1000, $field_id, $form_id );
								$seq_step    = apply_filters( 'vfb_sequential_num_step', 1, $field_id, $form_id );

								if ( !$seq_num ) {
									add_option( $seq_num_opt, $seq_start );
									$val = $seq_start;
								}
								else
									$val = $seq_num + $seq_step;

								break;
							case 'post_id' :
								$val = get_the_id();
								break;
							case 'post_title' :
								$val = get_the_title();
								break;
							case 'post_url' :
								$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : substr( $_SERVER['PHP_SELF'], 1 );
								$val = site_url( wp_unslash( $request_uri ) );
								break;
							case 'current_user_id' :
								$val = $current_user instanceof WP_User ? $current_user->ID : '';
								break;
							case 'current_user_name' :
								$val = $current_user instanceof WP_User ? $current_user->display_name : '';
								break;
							case 'current_user_username' :
								$val = $current_user instanceof WP_User ? $current_user->user_login : '';
								break;
							case 'current_user_email' :
								$val = $current_user instanceof WP_User ? $current_user->user_email : '';
								break;
							case 'custom' :
								$val = ( !empty( $default ) ) ? $default : trim( stripslashes( $opts_vals[1] ) );
								break;
						endswitch;
				endif;

				$output .= sprintf(
					'<input type="hidden" name="vfb-%1$d" id="%2$s" value="%3$s" class="vfb-text %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					esc_attr( $val ),
					$size,
					$required,
					$validation,
					$css
				);

				break;

			case 'color-picker' :
				// Load farbtastic library
				wp_enqueue_script( 'farbtastic-js' );

				$color_picker = sprintf(
					'<div id="vfb-colorPicker-%1$d" class="colorPicker"></div>',
					$field_id
				);

				$form_item = sprintf(
					'<input type="text" name="vfb-%1$d" id="%2$s" value="#%3$s" class="vfb-text vfb-color-picker %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$validation,
					$css
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>%3$s', $form_item, $description, $color_picker ) : $form_item . $color_picker;

				break;

			case 'autocomplete' :
				// Load jQuery UI autocomplete library
				wp_enqueue_script( 'jquery-ui-autocomplete' );

				$form_item = sprintf(
					'<input type="text" name="vfb-%1$d" id="%2$s" value="%3$s" class="vfb-text auto %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$validation,
					$css
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $form_item, $description ) : $form_item;

				break;

			case 'min' :
			case 'max' :

				$options = maybe_unserialize( $field->field_options );

				$form_item = sprintf(
					'<input type="text" name="vfb-%1$d" id="%2$s" value="%3$s" %9$s=%8$s class="vfb-text %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$validation,
					$css,
					esc_attr( $options[0] ),
					$field_type
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $form_item, $description ) : $form_item;

				break;

			case 'range' :

				$options = maybe_unserialize( $field->field_options );

				$min = esc_attr( $options[0] );
				$max = esc_attr( $options[1] );

				$form_item = sprintf(
					'<input type="text" name="vfb-%1$d" id="%2$s" value="%3$s" %9$s=%8$s class="vfb-text {range:[%8$s,%9$s]} %4$s %5$s %6$s %7$s" />',
					$field_id,
					$id_attr,
					$default,
					$size,
					$required,
					$validation,
					$css,
					$min,
					$max
				);

				$output .= ( !empty( $description ) ) ? sprintf( '<span class="vfb-span">%1$s<label>%2$s</label></span>', $form_item, $description ) : $form_item;

				break;

			case 'rating' :

				$options = maybe_unserialize( $field->field_options );

				$negative = esc_attr( $options['negative'] );
				$positive = esc_attr( $options['positive'] );
				$scale    = esc_attr( $options['scale'] );

				$field_options = '<table class="vfb-table"><tr><th>&nbsp;</th>';

				// Scale
				for ( $i = 1; $i <= $scale; $i++ ) {
					$field_options .= sprintf(
						'<th align="center"><label for="%1$s-%2$d"><strong>%2$d</strong></label></th>',
						$id_attr,
						$i
					);
				}

				// Negative
				$field_options .= '<th>&nbsp;</th></tr><tr>';
				$field_options .= sprintf(
					'<td><label for="%1$s-1">%2$s</label></td>',
					$id_attr,
					$negative
				);

				// Radio inputs
				for ( $i = 1; $i <= $scale; $i++ ) {
					$field_options .= sprintf(
						'<td align="center"><input type="radio" name="vfb-%1$d" id="%2$s-%3$d" value="%6$s" class="vfb-radio %4$s %5$s" /></td>',
						$field_id,
						$id_attr,
						$i,
						$required,
						$css,
						esc_attr( $i )
					);
				}

				// Positive
				$field_options .= sprintf(
					'<td><label for="%1$s-%2$d">%3$s</label></td>',
					$id_attr,
					$scale,
					$positive
				);
				$field_options .= '</tr></table>';

				$form_item = $field_options;

				$output .= ( !empty( $description ) ) ? sprintf( '<label>%2$s</label><span class="vfb-span">%1$s</span>', $form_item, $description ) : $form_item;

				break;

			case 'likert' :

				$options = maybe_unserialize( $field->field_options );

				$rows = !empty( $options['rows'] ) ? explode( "\n", $options['rows'] ) : array( __( 'No rows found!', 'visual-form-builder-pro' ) );
				$cols = !empty( $options['cols'] ) ? explode( "\n", $options['cols'] ) : array( __( 'No columns found!', 'visual-form-builder-pro' ) );

				$field_options = sprintf( '<table class="vfb-table vfb-likert vfb-likert-cols-%d"><tr class="vfb-likert-head"><th>&nbsp;</th>', count( $cols ) );

				foreach ( $cols as $key => $col ) {
					$field_options .= sprintf(
						'<th align="center"><label>%3$s</label></th>',
						$id_attr,
						$key,
						$col
					);
				}

				$field_options .= '</tr>';

				foreach ( $rows as $key => $row ) {

					$field_options .= sprintf(
						'<tr class="vfb-likert-row"><th><label>%3$s</label></th>',
						$id_attr,
						$key,
						esc_attr( trim( $row ) )
					);

					foreach ( $cols as $key => $col ) {
						$field_options .= sprintf(
							'<td align="center"><input type="radio" name="vfb-%1$d[%7$s]" id="%2$s-%3$d" value="%6$s" class="vfb-radio vfb-likert-option %4$s %5$s" /></td>',
							$field_id,
							$id_attr,
							$key,
							$required,
							$css,
							esc_attr( trim( $col ) ),
							esc_attr( trim( $row ) )
						);
					}

					$field_options .= '</tr>';
				}

				$field_options .= '</tr></table>';

				$form_item = $field_options;

				$output .= ( !empty( $description ) ) ? sprintf( '<label>%2$s</label><span class="vfb-span">%1$s</span>', $form_item, $description ) : $form_item;

				break;

			case 'submit' :

				$submit = sprintf(
					'<li class="vfb-item vfb-item-submit" id="item-%2$s">
					<input type="submit" name="vfb-submit" id="%2$s" value="%3$s" class="vfb-submit %4$s" />
					</li>',
					$field_id,
					$id_attr,
					wp_specialchars_decode( esc_html( $field_name ), ENT_QUOTES ),
					$css
				);

				$output .= ( false == $display_verification ) ? $submit : '';

				break;

			default:
				echo '';

				// Output Create Post items
				if ( class_exists( 'VFB_Pro_Create_Post' ) && method_exists( $vfb_post, 'form_output' ) ) {
					$create_post_vars = array(
						'field_type'	=> $field_type,
						'field_id'		=> $field_id,
						'id_attr'		=> $id_attr,
						'default'		=> $default,
						'size'			=> $size,
						'required'		=> $required,
						'validation'	=> $validation,
						'css'			=> $css,
						'description'	=> $description,
						'options'		=> $field->field_options
					);

					$output .= $vfb_post->form_output( $create_post_vars );
				}

				break;

		endswitch;

		if ( isset( $option ) )
			unset( $option );

		// Closing </li>
		$output .= ( !in_array( $field_type , array( 'verification', 'secret', 'submit', 'fieldset', 'section', 'hidden', 'page-break' ) ) ) ? "</li>\n" : '';

	endforeach;

	// Close user-added fields
	$output .= '</ul>&nbsp;</fieldset>';

	if ( $total_page !== '' )
		$total_page = '<span class="vfb-page-counter">' . $page_count . ' / ' . $page_count . '</span>';

	// Make sure the verification displays even if they have not updated their form
	if ( $verification == '' ) :
		$verification = sprintf(
			'<fieldset class="vfb-fieldset vfb-verification">
			<div class="vfb-legend"><h3>%1$s</h3></div>
			<ul class="vfb-section vfb-section-%2$d">
			<li class="vfb-item vfb-item-text">
			<label for="vfb-secret" class="vfb-desc">%3$s<span class="vfb-span">*</span></label>
			<div><input type="text" name="vfb-secret" id="vfb-secret" class="vfb-text vfb-medium" /></div>
			</li>',
			__( 'Verification' , 'visual-form-builder-pro' ),
			$count,
			__( 'Please enter any two digits with <strong>no</strong> spaces (Example: 12)' , 'visual-form-builder-pro' )
		);
	endif;

	// Display the SPAM verification
	if ( true == $display_verification ) :
		// Output our security test
		$output .= $verification .
		'<li style="display:none;"><label>' .
		 __( 'This box is for spam protection - <strong>please leave it blank</strong>' , 'visual-form-builder-pro' ) .
		 '</label><div><input name="vfb-spam" /></div></li>' .
		 $submit . $total_page .
		 '</ul>
		 &nbsp;
		 </fieldset>';
	endif;

	$output .= wp_referer_field( false );

	// Close the form out
	$output .= '</form>';

	// Filter for additional output after a form
	$output .= apply_filters( 'vfb_after_form_output', '', $form_id );

	// Close form container
	$output .= '</div> <!-- .visual-form-builder-container -->';

	// Force tags to balance
	force_balance_tags( $output );

	// Output the conditional rules
	if ( $rules )
		wp_localize_script( 'visual-form-builder-validation', 'VfbRules', array( 'rules' => htmlentities( $rules ) ) );

endif;
