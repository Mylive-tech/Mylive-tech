<?php
global $wpdb, $post;

$required       = isset( $_POST['_vfb-required-secret'] ) && $_POST['_vfb-required-secret'] == '0' ? false : true;
$secret_field   = isset( $_POST['_vfb-secret'] ) ? esc_html( $_POST['_vfb-secret'] ) : '';
$honeypot       = isset( $_POST['vfb-spam'] ) ? esc_html( $_POST['vfb-spam'] ) : '';
$referrer       = isset( $_POST['_wp_http_referer'] ) ? esc_html( $_POST['_wp_http_referer'] ) : false;
$wp_get_referer = wp_get_referer();

// If the verification is set to required, run validation check
if ( true == $required && !empty( $secret_field ) ) :
	// Use to skip the validate_input function for this field
	$novalidate = true;

	if ( !empty( $honeypot ) )
		wp_die( apply_filters( 'vfb_str_security_honeypot', __( 'Security check: hidden spam field should be blank.' , 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );

	if ( !isset( $_POST[ $secret_field ] ) && isset( $_POST['recaptcha_challenge_field'] ) ) {
		if ( !function_exists( 'recaptcha_get_html' ) )
	    	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'libraries/recaptchalib.php' );

		$vfb_settings  = get_option( 'vfb-settings' );
        $private_key   = $vfb_settings['recaptcha-private-key'];

		$resp = recaptcha_check_answer( $private_key,
	        $_SERVER['REMOTE_ADDR'],
	        $_POST['recaptcha_challenge_field'],
	        $_POST['recaptcha_response_field']
	    );

	    if ( !$resp->is_valid )
	    	wp_die( apply_filters( 'vfb_str_security_recaptcha', __( 'Security check: the reCAPTCHA answer was entered incorrectly. Please try again!' , 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );
	} else {
		if ( !is_numeric( $_POST[ $secret_field ] ) || strlen( $_POST[ $secret_field ] ) !== 2 )
			wp_die( apply_filters( 'vfb_str_security_secret', __( 'Security check: failed secret question. Please try again!' , 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );
	}
endif;

// Basic security check before moving any further
if ( !isset( $_POST['vfb-submit'] ) )
	return;

// Get global settings
$vfb_settings 	= get_option( 'vfb-settings' );

// Settings - Disable Email
$settings_send_email	= isset( $vfb_settings['disable-email'] ) ? true : false;

// Settings - Disable Notify Email
$settings_send_email_notify	= isset( $vfb_settings['disable-email-notify'] ) ? true : false;

// Settings - Skip Empty Fields in Email
$settings_skip_empty    = isset( $vfb_settings['skip-empties'] ) ? true : false;

// Settings - Disable saving new entry
$settings_save_entry    = isset( $vfb_settings['save-entry'] ) ? false : true;

// Settings - Disable saving entry's IP address
$settings_save_ip       = isset( $vfb_settings['save-ip'] ) ? '' : esc_html( $_SERVER['REMOTE_ADDR'] );

// Settings - Max Upload Size
$settings_max_upload    = isset( $vfb_settings['max-upload-size'] ) ? $vfb_settings['max-upload-size'] : 25;

// Settings - Spam word sensitivity
$settings_spam_points    = isset( $vfb_settings['spam-points'] ) ? $vfb_settings['spam-points'] : 4;

// Set submitted action to display success message
$this->submitted = true;

// Tells us which form to get from the database
$form_id = absint( $_POST['form_id'] );

$skip_referrer_check = apply_filters( 'vfb_skip_referrer_check', false, $form_id );

// Test if referral URL has been set
if ( !$referrer )
	wp_die( apply_filters( 'vfb_str_security_referal_set', __( 'Security check: referal URL does not appear to be set.' , 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );

// Allow referrer check to be skipped
if ( !$skip_referrer_check ) :
	// Test if the referral URL matches what sent from WordPress
	if ( $wp_get_referer )
		wp_die( apply_filters( 'vfb_str_security_referal_match', __( 'Security check: referal does not match this site.' , 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );
endif;

// Test if it's a known SPAM bot
if ( $this->isBot() )
	wp_die( apply_filters( 'vfb_str_security_isBot', __( 'Security check: looks like you are a SPAM bot. If you think this is an error, please email the site owner.' , 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );

// Query to get current form
$order = sanitize_sql_orderby( 'form_id DESC' );
$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->form_table_name WHERE form_id = %d ORDER BY $order", $form_id ) );

$form_settings = (object) array(
	'form_title' 					=> stripslashes( html_entity_decode( $form->form_title, ENT_QUOTES, 'UTF-8' ) ),
	'form_subject' 					=> stripslashes( html_entity_decode( $form->form_email_subject, ENT_QUOTES, 'UTF-8' ) ),
	'form_to' 						=> is_array( unserialize( $form->form_email_to ) ) ? unserialize( $form->form_email_to ) : explode( ',', unserialize( $form->form_email_to ) ),
	'form_from' 					=> stripslashes( $form->form_email_from ),
	'form_from_name' 				=> stripslashes( $form->form_email_from_name ),
	'form_notification_setting' 	=> stripslashes( $form->form_notification_setting ),
	'form_notification_email_name' 	=> stripslashes( $form->form_notification_email_name ),
	'form_notification_email_from' 	=> stripslashes( $form->form_notification_email_from ),
	'form_notification_subject' 	=> stripslashes( html_entity_decode( $form->form_notification_subject, ENT_QUOTES, 'UTF-8' ) ),
	'form_notification_message' 	=> stripslashes( $form->form_notification_message ),
	'form_notification_entry' 		=> stripslashes( $form->form_notification_entry )
);

// Allow the form settings to be filtered (ex: return $form_settings->'form_title' = 'Hello World';)
$form_settings = (object) apply_filters_ref_array( 'vfb_email_form_settings', array( $form_settings, $form_id ) );

$email_design = unserialize( $form->form_email_design );

// Set email design variables
$email_settings = (object) array(
	'color_scheme' 			=> !empty( $email_design['color_scheme'] ) ? stripslashes( $email_design['color_scheme'] ) : '',
	'format' 				=> !empty( $email_design['format'] ) ? stripslashes( $email_design['format'] ) : 'html',
	'link_love' 			=> !empty( $email_design['link_love'] ) ? stripslashes( $email_design['link_love'] ) : 'yes',
	'footer_text' 			=> !empty( $email_design['footer_text'] ) ? stripslashes( $email_design['footer_text'] ) : '',
	'background_color' 		=> !empty( $email_design['background_color'] ) ? stripslashes( $email_design['background_color'] ) : '#eeeeee',
	'header_text' 			=> !empty( $email_design['header_text'] ) ? stripslashes( $email_design['header_text'] ) : $form_settings->form_subject,
	'header_image' 			=> !empty( $email_design['header_image'] ) ? stripslashes( $email_design['header_image'] ) : '',
	'header_color' 			=> !empty( $email_design['header_color'] ) ? stripslashes( $email_design['header_color'] ) : '#810202',
	'header_text_color' 	=> !empty( $email_design['header_text_color'] ) ? stripslashes( $email_design['header_text_color'] ) : '#ffffff',
	'fieldset_color' 		=> !empty( $email_design['fieldset_color'] ) ? stripslashes( $email_design['fieldset_color'] ) : '#680606',
	'section_color' 		=> !empty( $email_design['section_color'] ) ? stripslashes( $email_design['section_color'] ) : '#5C6266',
	'section_text_color' 	=> !empty( $email_design['section_text_color'] ) ? stripslashes( $email_design['section_text_color'] ) : '#ffffff',
	'text_color' 			=> !empty( $email_design['text_color'] ) ? stripslashes( $email_design['text_color'] ) : '#333333',
	'link_color' 			=> !empty( $email_design['link_color'] ) ? stripslashes( $email_design['link_color'] ) : '#1b8be0',
	'row_color' 			=> !empty( $email_design['row_color'] ) ? stripslashes( $email_design['row_color'] ) : '#ffffff',
	'row_alt_color' 		=> !empty( $email_design['row_alt_color'] ) ? stripslashes( $email_design['row_alt_color'] ) : '#eeeeee',
	'border_color' 			=> !empty( $email_design['border_color'] ) ? stripslashes( $email_design['border_color'] ) : '#cccccc',
	'footer_color' 			=> !empty( $email_design['footer_color'] ) ? stripslashes( $email_design['footer_color'] ) : '#333333',
	'footer_text_color' 	=> !empty( $email_design['footer_text_color'] ) ? stripslashes( $email_design['footer_text_color'] ) : '#ffffff',
	'font_family' 			=> !empty( $email_design['font_family'] ) ? stripslashes( $email_design['font_family'] ) : 'Arial',
	'header_font_size' 		=> !empty( $email_design['header_font_size'] ) ? stripslashes( $email_design['header_font_size'] ) : 32,
	'fieldset_font_size' 	=> !empty( $email_design['fieldset_font_size'] ) ? stripslashes( $email_design['fieldset_font_size'] ) : 20,
	'section_font_size' 	=> !empty( $email_design['section_font_size'] ) ? stripslashes( $email_design['section_font_size'] ) : 15,
	'text_font_size' 		=> !empty( $email_design['text_font_size'] ) ? stripslashes( $email_design['text_font_size'] ) : 13,
	'footer_font_size' 		=> !empty( $email_design['footer_font_size'] ) ? stripslashes( $email_design['footer_font_size'] ) : 11
);

// Allow the email design to be filtered (ex: return $email_settings->'format' = 'html';)
$email_settings = (object) apply_filters_ref_array( 'vfb_email_design_settings', array( $email_settings, $form_id ) );

// Setup default styles for <p> and <a> tags replacements
$p_style = sprintf( 'style="font-size: %1$dpx; font-weight: normal; margin: 14px 0 14px 0; font-family: %2$s; color: %3$s; padding: 0;"', $email_settings->text_font_size, $email_settings->font_family, $email_settings->text_color );
$a_style = sprintf( 'style="font-size: %1$dpx;font-family: %2$s;color: %3$s;"', $email_settings->text_font_size, $email_settings->font_family, $email_settings->link_color );

// Array to store Create Post custom fields
$custom_field = array();

// Set default user ID to 1
$user_id = 1;

// If the user is logged in, fill the field in for them
if ( is_user_logged_in() ) :
	// Get logged in user details
	$user = wp_get_current_user();
	$user_id = !empty( $user->ID ) ? $user->ID : 1;
endif;

// Save form verification for validate_input
$form_verification = $form->form_verification;

// Apply templating to form_subject, form_notification_subject, form_notification_message, email header text
$form_settings->form_subject 				= $this->templating( $form_settings->form_subject );
$form_settings->form_notification_subject 	= $this->templating( $form_settings->form_notification_subject );
$form_settings->form_notification_message 	= $this->templating( $form_settings->form_notification_message );
$email_settings->header_text 				= $this->templating( $email_settings->header_text );

// Sender name field ID
$sender = $form->form_email_from_name_override;

// Sender email field ID
$email = $form->form_email_from_override;

// Notifcation email field ID
$notify = $form->form_notification_email;

$reply_to_name	= $form_settings->form_from_name;
$reply_to_email	= $form_settings->form_from;

// Use field for sender name
if ( !empty( $sender ) && isset( $_POST[ 'vfb-' . $sender ] ) ) {
	$form_settings->form_from_name = wp_kses_data( $_POST[ 'vfb-' . $sender ] );
	$reply_to_name = $form_settings->form_from_name;
}

// Use field for sender email
if ( !empty( $email ) && isset( $_POST[ 'vfb-' . $email ] ) ) {
	$form_settings->form_from = sanitize_email( $_POST[ 'vfb-' . $email ] );
	$reply_to_email = $form_settings->form_from;
}

// Use field for copy email
$copy_email = ( !empty( $notify ) ) ? sanitize_email( $_POST[ 'vfb-' . $notify ] ) : '';

// Query to get all forms
$order = sanitize_sql_orderby( 'field_sequence ASC' );
$fields = $wpdb->get_results( $wpdb->prepare( "SELECT field_id, field_key, field_name, field_description, field_type, field_options, field_options_other, field_parent, field_required, field_rule_setting FROM $this->field_table_name WHERE form_id = %d ORDER BY $order", $form_id ) );

$open_fieldset = false;

// Setup counter for alt rows
$i = $points = 0;

// Setup HTML email vars
$header = $body = $message = $footer = $html_email = $plain_text = $auto_response_email = $attachments = $html_link_love = $plain_text_link_love = $rtl = '';

if ( function_exists( 'is_rtl' ) && is_rtl() )
	$rtl = 'dir="rtl"';

// Prepare the beginning of the content
$header = sprintf(
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html %2$s>
	<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<title>HTML Email</title>
	</head>
	<body style="background-color: %1$s;">
	<table class="bg1" cellspacing="0" border="0" style="background-color: %1$s;" cellpadding="0" width="100%%">
	<tr>
	<td class="vfb-email-center" align="center">
	<table class="bg2" cellspacing="0" border="0" style="background-color: #ffffff;" cellpadding="0" width="600" %2$s>
	<tr>
	<td class="permission" align="center" style="background-color: %1$s;padding: 10px 20px 10px 20px;">&nbsp;</td>
	</tr> <!-- .permission -->',
	$email_settings->background_color,
	$rtl
);

// Use header image, if set
if ( isset( $email_settings->header_image ) && $email_settings->header_image == '' ) :
	$header .= sprintf(
		'<tr>
		<td class="header" align="left" style="background-color: %1$s;padding: 50px 20px 50px 20px;">
		<h1 style="font-family: %2$s;font-size: %3$dpx;font-weight:normal;margin:0;padding:0;color: %4$s;">%5$s</h1>
		</td>
		</tr> <!-- .header -->',
		$email_settings->header_color,
		$email_settings->font_family,
		$email_settings->header_font_size,
		$email_settings->header_text_color,
		$email_settings->header_text
	);
else :
	@list( $width, $height, $type, $attr ) = getimagesize( $email_settings->header_image );

	$header .= sprintf(
		'<tr>
		<td class="header" align="left" style="background-color: %1$s;">
		<img %2$s src="%3$s" alt="%4$s" />
		</td>
		</tr> <!-- .header -->',
		$email_settings->header_color,
		$attr,
		$email_settings->header_image,
		$email_settings->header_text
	);
endif;

$header .= sprintf(
	'<tr class="vfb-email-body-row">
	<td class="vfb-email-body" valign="top" style="background-color: %1$s;padding: 20px 20px 20px 20px;">
	<table class="vfb-email-inner" cellspacing="0" border="0" cellpadding="0" width="100%%" %2$s>
	<tr class="vfb-email-inner-row">
	<td class="vfb-email-inner-cell" align="left" valign="top">' . "\n",
	$email_settings->row_color,
	$rtl
);

// Start setting up plain text email
$plain_text .= "============ {$email_settings->header_text} =============\n";

// Allow empty fields to be skipped
$skip_empties = apply_filters( 'vfb_skip_empty_fields', $settings_skip_empty, $form_id );

// Loop through each form field and build the body of the message
foreach ( $fields as $field ) :
	$alt_row = ( $i % 2 == 0 ) ? 'background-color:' . $email_settings->row_alt_color : '';

	$options_other = $field->field_options_other;

	// Skip empty fields
	if ( $skip_empties ) :
		// Check Checkbox and Radio with values
		if ( isset( $_POST[ 'vfb-' . $field->field_id ] ) && is_array( $_POST[ 'vfb-' . $field->field_id ] ) ) {
			$empty_array = array_filter( $_POST[ 'vfb-' . $field->field_id ], 'strlen' );

			if ( empty( $empty_array ) )
				continue;
		}
		// Check Checkbox and Radio without values
		elseif ( !isset( $_POST[ 'vfb-' . $field->field_id ] ) && in_array( $field->field_type, array( 'checkbox', 'radio' ) ) )
			continue;
		// Check File Uploads
		elseif ( isset( $_FILES[ 'vfb-' . $field->field_id ] ) && $_FILES[ 'vfb-' . $field->field_id ]['size'] == 0 )
			continue;
		// All other inputs
		elseif ( isset( $_POST[ 'vfb-' . $field->field_id ] ) && empty( $_POST[ 'vfb-' . $field->field_id ] ) )
			continue;
		// Check disabled inputs; make sure to output display fields
		elseif ( !isset( $_POST[ 'vfb-' . $field->field_id ] ) && ! in_array( $field->field_type,  array( 'fieldset', 'section', 'instructions', 'file-upload' ) ) )
			continue;

	endif;

	// Handle attachments
	if ( $field->field_type == 'file-upload' ) :
		$value = ( isset( $_FILES[ 'vfb-' . $field->field_id ] ) ) ? $_FILES[ 'vfb-' . $field->field_id ] : '';

		if ( is_array( $value ) && $value['size'] > 0 ) :
			// 25MB is the max size allowed
			$size = apply_filters( 'vfb_max_file_size', $settings_max_upload );
			$max_attach_size = $size * 1048576;

			// Display error if file size has been exceeded
			if ( $value['size'] > $max_attach_size )
				wp_die( sprintf( apply_filters( 'vfb_str_max_file_size', __( "File size exceeds %dMB. Please decrease the file size and try again.", 'visual-form-builder-pro' ) ), $size ), '', array( 'back_link' => true ) );

			// We need to include the file that runs the wp_handle_upload function
			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			// Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
			$uploaded_file = wp_handle_upload( $value, array( 'test_form' => false ) );

			// If the wp_handle_upload call returned a local path for the image
			if ( isset( $uploaded_file['file'] ) ) :
				// Retrieve the file type from the file name. Returns an array with extension and mime type
				$wp_filetype = wp_check_filetype( basename( $uploaded_file['file'] ), null );

				// Return the current upload directory location
				$wp_upload_dir = wp_upload_dir();

				$media_upload = array(
					'guid' 				=> $wp_upload_dir['url'] . '/' . basename( $uploaded_file['file'] ),
					'post_mime_type' 	=> $wp_filetype['type'],
					'post_title' 		=> preg_replace( '/\.[^.]+$/', '', basename( $uploaded_file['file'] ) ),
					'post_content' 		=> '',
					'post_status' 		=> 'inherit'
				);

				// Allow the uploads to be attached to a post/page
				$post_id = apply_filters( 'vfb_upload_attach_post', 0, $form_id );

				// Insert attachment into Media Library and get attachment ID
				$attach_id = wp_insert_attachment( $media_upload, $uploaded_file['file'], $post_id );

				// Include the file that runs wp_generate_attachment_metadata()
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );

				// Setup attachment metadata
				$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );

				// Update the attachment metadata
				wp_update_attachment_metadata( $attach_id, $attach_data );

				$attachments[ 'vfb-' . $field->field_id ] = $uploaded_file['file'];

				$data[] = array(
					'id'              => $field->field_id,
					'slug'            => $field->field_key,
					'name'            => $field->field_name,
					'type'            => $field->field_type,
					'options'         => $field->field_options,
					'options_other'   => $options_other,
					'parent_id'       => $field->field_parent,
					'value'           => $uploaded_file['url']
				);

				$body .= sprintf(
					'<tr class="vfb-email-file-upload">
					<td class="mainbar" align="left" valign="top" width="300" style="%1$s;border-bottom:1px solid %2$s;">
					<p style="font-size: %3$dpx; font-weight: bold; margin: 14px 0 14px 5px; font-family: %4$s; color: %5$s; padding: 0;">%6$s:</p>
					</td> <!-- .mainbar -->
					<td class="mainbar" align="left" valign="top" width="300" style="%1$s;border-bottom:1px solid %2$s;">
					<p style="font-size: %3$dpx; font-weight: normal; margin: 14px 0 14px 0; font-family: %4$s; color: %5$s; padding: 0;">
					<a href="%8$s" style="font-size: 13px; font-weight: normal; font-family: %4$s; color: %7$s;">%8$s</a></p>
					</td> <!-- .mainbar -->
					</tr> <!-- .vfb-email-file-upload -->' . "\n",
					$alt_row,
					$email_settings->border_color,
					$email_settings->text_font_size,
					$email_settings->font_family,
					$email_settings->text_color,
					stripslashes( $field->field_name ),
					$email_settings->link_color,
					$uploaded_file['url']
				);

				$plain_text .= stripslashes( $field->field_name ) . ": {$uploaded_file['url']}\n";

				do_action( 'vfb_after_uploads', $attach_id, $uploaded_file, $field->field_id, $form_id );
			endif;
		else :
			$value = ( isset( $_POST[ 'vfb-' . $field->field_id ] ) ) ? $_POST[ 'vfb-' . $field->field_id ] : '';

			$data[] = array(
				'id'              => $field->field_id,
				'slug'            => $field->field_key,
				'name'            => $field->field_name,
				'type'            => $field->field_type,
				'options'         => $field->field_options,
				'options_other'   => $options_other,
				'parent_id'       => $field->field_parent,
				'value'           => esc_html( $value ),
			);

			$body .= sprintf(
				'<tr class="vfb-email-file-upload">
				<td class="mainbar" align="left" valign="top" width="300" style="%1$s;border-bottom:1px solid %2$s;">
				<p style="font-size: %3$spx; font-weight: bold; margin: 14px 0 14px 5px; font-family: %4$s; color: %5$s; padding: 0;">%6$s:</p>
				</td>
				<td class="mainbar" align="left" valign="top" width="300" style="%1$s;border-bottom:1px solid %2$s;">
				<p style="font-size: %3$spx; font-weight: normal; margin: 14px 0 14px 0; font-family: %4$s; color: %5$s; padding: 0;">%7$s</p>
				</td>
				</tr> <!-- .vfb-email-file-upload -->' . "\n",
				$alt_row,
				$email_settings->border_color,
				$email_settings->text_font_size,
				$email_settings->font_family,
				$email_settings->text_color,
				stripslashes( $field->field_name ),
				$value
			);

			$plain_text .= stripslashes( $field->field_name ) . ": $value\n";
		endif;

		// Increment our alt row counter
		$i++;

	// Everything else
	else :
		$value = ( isset( $_POST[ 'vfb-' . $field->field_id ] ) ) ? $_POST[ 'vfb-' . $field->field_id ] : '';

		// If time field, build proper output
		if ( is_array( $value ) && $field->field_type == 'time' )
			$value = $this->build_array_form_item( $value, $field->field_type );
		// If address field, build proper output
		elseif ( is_array( $value ) && $field->field_type == 'address' )
			$value = $this->build_array_form_item( $value, $field->field_type );
		// If name field, build proper output
		elseif ( is_array( $value ) && $field->field_type == 'name' ) {
			$value = $this->build_array_form_item( $value, $field->field_type );

			if ( !empty( $sender ) && isset( $_POST[ 'vfb-' . $sender ] ) && $sender == $field->field_id ) {
				$form_settings->form_from_name = wp_kses_data( $value );
				$reply_to_name = $form_settings->form_from_name;
			}
		}
		// If name field, build proper output
		elseif ( is_array( $value ) && $field->field_type == 'likert' ) {
			$value = $this->build_array_form_item( $value, $field->field_type );
		}
		// If multiple values, build the list
		elseif ( is_array( $value ) )
			$value = $this->build_array_form_item( $value, $field->field_type );
		elseif ( 'radio' == $field->field_type ) {

			$value = wp_specialchars_decode( stripslashes( esc_html( $value ) ), ENT_QUOTES );

			$radio_other = isset( $_POST[ 'vfb-' . $field->field_id . '-other' ] ) ? $_POST[ 'vfb-' . $field->field_id . '-other' ] : '';

			if ( !empty( $radio_other ) ) :
				// Override selected radio button with this value
				$value = wp_specialchars_decode( stripslashes( esc_html( $radio_other ) ), ENT_QUOTES );

				// Save 'Other' option for Entries Detail
				$options_other = maybe_unserialize( $field->field_options_other );
				$options_other['selected'] = $value;
				$options_other = serialize( $options_other );
			endif;
		}
		// Lastly, handle single values
		else
			$value = wp_specialchars_decode( stripslashes( esc_html( $value ) ), ENT_QUOTES );


		// Spam Words - Exploits
		$exploits = array( 'content-type', 'bcc:', 'cc:', 'document.cookie', 'onclick', 'onload', 'javascript', 'alert' );
		$exploits = apply_filters( 'vfb_spam_words_exploits', $exploits, $form_id );

		// Spam Words - Exploits
		$profanity = array( 'beastial', 'bestial', 'blowjob', 'clit', 'cock', 'cum', 'cunilingus', 'cunillingus', 'cunnilingus', 'cunt', 'ejaculate', 'fag', 'felatio', 'fellatio', 'fuck', 'fuk', 'fuks', 'gangbang', 'gangbanged', 'gangbangs', 'hotsex', 'jism', 'jiz', 'kock', 'kondum', 'kum', 'kunilingus', 'orgasim', 'orgasims', 'orgasm', 'orgasms', 'phonesex', 'phuk', 'phuq', 'porn', 'pussies', 'pussy', 'spunk', 'xxx' );
		$profanity = apply_filters( 'vfb_spam_words_profanity', $profanity, $form_id );

		// Spam Words - Misc
		$spamwords = array( 'viagra', 'phentermine', 'tramadol', 'adipex', 'advai', 'alprazolam', 'ambien', 'ambian', 'amoxicillin', 'antivert', 'blackjack', 'backgammon', 'holdem', 'poker', 'carisoprodol', 'ciara', 'ciprofloxacin', 'debt', 'dating', 'porn' );
		$spamwords = apply_filters( 'vfb_spam_words_misc', $spamwords, $form_id );

		// Add up points for each spam hit
		if ( preg_match( '/(' . implode( '|', $exploits ) . ')/i', $value ) )
			$points += 2;
		elseif ( preg_match( '/(' . implode( '|', $profanity ) . ')/i', $value ) )
			$points += 1;
		elseif ( preg_match( '/(' . implode( '|', $spamwords ) . ')/i', $value ) )
			$points += 1;

		//Sanitize input
		$value = $this->sanitize_input( $value, $field->field_type );

		// Change validation if conditional field has been hidden and disabled
		$is_required = ( !isset( $_POST[ 'vfb-' . $field->field_id ] ) && 'yes' == $field->field_required ) ? 'no' : $field->field_required;

		// Validate input (skip the verification, if user didn't display it)
		$this->validate_input( $value, $field->field_name, $field->field_type, $is_required, $form_verification, $field->field_rule_setting );

		$removed_field_types = array( 'verification', 'secret', 'submit', 'page-break', 'instructions' );

		$removed_field_types = apply_filters( 'vfb_removed_field_types', $removed_field_types, $form_id );

		// Don't add certain fields to the email
		if ( ! in_array( $field->field_type, $removed_field_types ) ) :

			switch ( $field->field_type ) :
				case 'fieldset' :
					// Close each fieldset
					if ( $open_fieldset == true )
						$body .= '</table> <!-- .vfb-email-fieldset -->' . "\n";

					$body .= sprintf(
						'<h2 style="font-size: %1$dpx; font-weight: bold; margin: 10px 0 10px 0; font-family: %2$s; color: %3$s; padding: 0;">%4$s</h2>
						<table class="vfb-email-fieldset" cellspacing="0" border="0" cellpadding="0" width="100%%" %5$s>' . "\n",
						$email_settings->fieldset_font_size,
						$email_settings->font_family,
						$email_settings->fieldset_color,
						stripslashes( $field->field_name ),
						$rtl
					);

					$open_fieldset = true;

					$plain_text .= "\n------------------------------\n" . stripslashes( $field->field_name ) .  "\n------------------------------\n";
				break;

				case 'section' :
					$body .= sprintf(
						'<tr class="vfb-email-section">
						<td colspan="2" style="background-color: %1$s;color: %2$s;">
						<h3 style="font-size: %3$dpx; font-weight: bold; margin: 14px 14px 14px 10px; font-family: %4$s; color: %2$s; padding: 0;">%5$s</h3>
						</td>
						</tr> <!-- .vfb-email-section -->' . "\n",
						$email_settings->section_color,
						$email_settings->section_text_color,
						$email_settings->section_font_size,
						$email_settings->font_family,
						stripslashes( $field->field_name )
					);

					$plain_text .= "*** " . stripslashes( $field->field_name ) . "***\n";
				break;

				case 'instructions' :
					// Use field description instead of a submitted $_POST value
					$display_value = wp_specialchars_decode( esc_html( stripslashes( $field->field_description ) ), ENT_QUOTES );

					$body .= sprintf(
						'<tr class="vfb-email-instructions">
						<td class="mainbar" align="left" valign="top" width="300" style="%1$s;border-bottom:1px solid %2$s;">
						<p style="font-size: %3$dpx; font-weight: bold; margin: 14px 0 14px 5px; font-family: %4$s; color: %5$s; padding: 0;">%6$s:</p>
						</td> <!-- .mainbar -->
						<td class="mainbar" align="left" valign="top" width="300" style="%1$s;border-bottom:1px solid %2$s;">
						<p style="font-size: %3$dpx; font-weight: normal; margin: 14px 0 14px 0; font-family: %4$s; color: %5$s; padding: 0;">%7$s</p>
						</td> <!-- .mainbar -->
						</tr> <!-- .vfb-email-instructions -->' . "\n",
						$alt_row,
						$email_settings->border_color,
						$email_settings->text_font_size,
						$email_settings->font_family,
						$email_settings->text_color,
						stripslashes( $field->field_name ),
						wpautop( $display_value )
					);

					$plain_text .= stripslashes( $field->field_name ) . ": $display_value\n";

					// Increment our alt row counter
					$i++;
				break;

				default :
					$display_value = $value;

					// If Create Post addon installed, use category name not ID in email
					if ( class_exists( 'VFB_Pro_Create_Post' ) && 'post-category' == $field->field_type )
						$display_value = get_the_category_by_ID( $value );

					// Wrap paragraphs and URLs in default styles
					$display_value = preg_replace( '/(<p)/', "$1 $p_style", $display_value );
					$display_value = preg_replace( '/(<a)/', "$1 $a_style", $display_value );

					$body .= sprintf(
						'<tr class="vfb-email-input">
						<td class="mainbar" align="left" valign="top" width="300" style="%1$s;border-bottom:1px solid %2$s;">
						<p style="font-size: %3$dpx; font-weight: bold; margin: 14px 0 14px 5px; font-family: %4$s; color: %5$s; padding: 0;">%6$s:</p>
						</td> <!-- .mainbar -->
						<td class="mainbar" align="left" valign="top" width="300" style="%1$s;border-bottom:1px solid %2$s;">
						<p style="font-size: %3$dpx; font-weight: normal; margin: 14px 0 14px 0; font-family: %4$s; color: %5$s; padding: 0;">%7$s</p>
						</td> <!-- .mainbar -->
						</tr> <!-- .vfb-email-input -->' . "\n",
						$alt_row,
						$email_settings->border_color,
						$email_settings->text_font_size,
						$email_settings->font_family,
						$email_settings->text_color,
						stripslashes( $field->field_name ),
						$display_value
					);

					// Undo wpautop for Textarea in Plain Text email
					if ( 'textarea' == $field->field_type ) {
						$pt_textarea = preg_replace( '/<p[^>]*?>/', '', $value );
						$pt_textarea = str_replace( '</p>', "\n", $pt_textarea );

						$plain_text .= stripslashes( $field->field_name ) . ": $pt_textarea\n";
					}
					else
						$plain_text .=  stripslashes( $field->field_name ) . ": $value\n";

					// Increment our alt row counter
					$i++;
				break;
			endswitch;

		endif;

		// Sequential Order Number
		if ( 'hidden' == $field->field_type && !empty( $value ) ) {
			$seq_num_opt = "vfb-hidden-sequential-num-{$form_id}-" . $field->field_id;
			$seq_num     = get_option( $seq_num_opt );

			if ( $seq_num )
				update_option( $seq_num_opt, $value );
		}

		$data[] = array(
			'id'            => $field->field_id,
			'slug'          => $field->field_key,
			'name'          => $field->field_name,
			'type'          => $field->field_type,
			'options'       => $field->field_options,
			'options_other' => $options_other,
			'parent_id'     => $field->field_parent,
			'value'         => esc_html( $value )
		);
	endif;

	// Setup Create User variables
	if ( $field->field_type == 'email' && !isset( $create_user_email ) )
		$create_user_email = $value;
	elseif ( $field->field_type == 'username' && !isset( $create_user_user ) )
		$create_user_user = $value;
	elseif ( $field->field_type == 'password' && !isset( $create_user_password ) )
		$create_user_password = $value;

	// Setup Create Post variables
	if ( $field->field_type == 'post-title' && !isset( $post_title ) )
		$post_title = $value;
	elseif ( $field->field_type == 'post-content' && !isset( $post_content ) )
		$post_content = $value;
	elseif ( $field->field_type == 'post-excerpt' && !isset( $post_excerpt ) )
		$post_excerpt = $value;
	elseif ( $field->field_type == 'post-category' && !isset( $post_category ) )
		$post_category = $value;
	elseif ( $field->field_type == 'post-tag' && !isset( $post_tag ) )
		$post_tag = $value;
	elseif ( $field->field_type == 'custom-field' && !isset( $custom_field[ $field->field_id ] ) )
		$custom_field[ $field->field_id ] = $value;

	// Setup Akismet variables
	if ( $field->field_type == 'email' && !isset( $comment_author_email ) )
		$comment_author_email = $value;
	elseif ( $field->field_type == 'name' && !isset( $comment_author ) )
		$comment_author = $value;
	elseif ( $field->field_type == 'url' && !isset( $comment_author_url ) )
		$comment_author_url = $value;
	elseif ( $field->field_type == 'textarea' && !isset( $comment_content ) )
		$comment_content = $value;

	// If the user accumulates more than 4 points, it might be spam
	if ( $points > $settings_spam_points )
		wp_die( apply_filters( 'vfb_str_security_points', __( 'Your responses look too much like spam and could not be sent at this time.', 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );
endforeach;

//$comment_author = 'viagra-test-123';
// Create an array based off the Akismet values collected
$akismet_vars = array( 'comment_author_email', 'comment_author', 'comment_author_url', 'comment_content' );
$akismet_data = compact( $akismet_vars );

// Insert additional Akismet data
$akismet_data['comment_type'] = 'contact-form';
$akismet_data['user_ip']      = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
$akismet_data['user_agent']   = $_SERVER['HTTP_USER_AGENT'];
$akismet_data['referrer']     = $_SERVER['HTTP_REFERER'];
$akismet_data['blog']         = get_option( 'home' );

// Run akismet check and set flag to send mail
$is_spam = ( $this->akismet_check( $akismet_data ) ) ? 'spam' : 1;

// Run Create User add-on
if ( class_exists( 'VFB_Pro_Create_User' ) ) {
	$vfb_user = new VFB_Pro_Create_User();

	// Create an array based off the user values collected
	$user_vars = array( 'create_user_user', 'create_user_password', 'create_user_email' );
	$user_data = compact( $user_vars );

	$vfb_user->create_user( $user_data, $form_id );
}

// Run Create Post add-on
if ( class_exists( 'VFB_Pro_Create_Post' ) ) {
	$vfb_post = new VFB_Pro_Create_Post();

	$post_vars = array( 'post_title', 'post_content', 'post_excerpt', 'post_category', 'post_tag', 'custom_field' );
	$post_data = compact( $post_vars );

	if ( isset( $post_title ) )
		$vfb_post->create_post( $post_data, $user_id, $form_id );
}


// Setup our entries data
$entry = array(
	'form_id' 			=> $form_id,
	'user_id'			=> $user_id,
	'data' 				=> serialize( $data ),
	'subject' 			=> $form_settings->form_subject,
	'sender_name' 		=> $form_settings->form_from_name,
	'sender_email' 		=> $form_settings->form_from,
	'emails_to' 		=> serialize( $form_settings->form_to ),
	'date_submitted' 	=> date_i18n( 'Y-m-d G:i:s' ),
	'ip_address' 		=> apply_filters( 'vfb_entries_save_ip', $settings_save_ip ),
	'akismet'			=> maybe_serialize( $akismet_data ),
	'entry_approved'	=> $is_spam
);

// Insert this data into the entries table
if ( apply_filters( 'vfb_entries_save_new', $settings_save_entry, $form_id ) ) :
	$wpdb->insert( $this->entries_table_name, $entry );

	// Save new entry ID
	$this->new_entry_id = $wpdb->insert_id;
endif;

// Setup the link love
if ( empty( $email_settings->link_love ) || $email_settings->link_love == 'yes' ) {
	$html_link_love = sprintf(
		'This email was built and sent using <a href="http://vfbpro.com" style="font-size: %1$dpx; font-family: %2$s;color: %3$s;">Visual Form Builder Pro</a>.',
		$email_settings->footer_font_size,
		$email_settings->font_family,
		$email_settings->link_color
	);

	$plain_text_link_love = "This email was built and sent using\nVisual Form Builder Pro (http://vfbpro.com)";
}

// Close out the content
$footer = sprintf(
	'</table>  <!-- .vfb-email-fieldset-final -->
	</td>  <!-- .vfb-email-inner-cell -->
	</tr>  <!-- .vfb-email-inner-row -->
	</table>  <!-- .vfb-email-inner -->
	</td>  <!-- .vfb-email-body -->
	</tr>  <!-- .vfb-email-body-row -->
	<tr class="vfb-footer-container">
	<td class="footer" height="61" align="left" valign="middle" style="background-color: %1$s; padding: 0 20px 0 20px; height: 61px; vertical-align: middle;">
	<p style="font-size: %2$dpx; font-weight: normal; margin: 0; font-family: %3$s; line-height: 16px; color: %4$s; padding: 0;">%5$s %6$s</p>
	</td>  <!-- .footer -->
	</tr>  <!-- .vfb-footer-container -->
	</table> <!-- .bg2 -->
	</td> <!-- .vfb-email-center -->
	</tr>
	<tr>
	<td class="permission" align="center" style="background-color: %7$s; padding: 20px 20px 20px 20px;">&nbsp;</td>
	</tr> <!-- .permission -->
	</table> <!-- .bg1 -->
	</body>
	</html>',
	$email_settings->footer_color,
	$email_settings->footer_font_size,
	$email_settings->font_family,
	$email_settings->footer_text_color,
	$html_link_love,
	$email_settings->footer_text,
	$email_settings->background_color
);

$plain_text .= "\n- - - - - - - - - - - -\n$plain_text_link_love\n{$email_settings->footer_text}\n";

// Build complete HTML email
$html_email = $header . $body . $footer;

// Decode HTML for message so it outputs properly
$notify_message = ( !empty( $form_settings->form_notification_message ) ) ? wp_specialchars_decode( $form_settings->form_notification_message, ENT_QUOTES ) : '';

// Allow notify message to be filtered
$notify_message = apply_filters( 'vfb_notify_message', $notify_message, $form_id, $this->new_entry_id );

// Initialize header filter vars
$header_from_name  = function_exists( 'mb_encode_mimeheader' ) ? mb_encode_mimeheader( stripslashes( $reply_to_name ) ) : stripslashes( $reply_to_name );
$header_from       = $reply_to_email;

// Set message format and header contenty type
if ( empty( $email_settings->format ) || $email_settings->format == 'html' ) :
	$header_content_type = 'text/html';
	$message = $html_email;

	// Sanitize content for allowed HTML tags
	$notify_message = wp_kses_post( $notify_message );

	// Wrap paragraphs and URLs in default styles for notify message
	$notify_message = preg_replace( '/(<p)/', "$1 $p_style", $notify_message );
	$notify_message = preg_replace( '/(<a)/', "$1 $a_style", $notify_message );

	// Either prepend the notification message to the submitted entry, or send by itself
	if ( !empty( $form_settings->form_notification_entry ) )
		$auto_response_email = $header . $notify_message . $body . $footer;
	else
		$auto_response_email = sprintf( '%1$s<table cellspacing="0" border="0" cellpadding="0" width="100%%"><tr><td colspan="2" class="mainbar" align="left" valign="top" width="600">%2$s</td></tr>%3$s', $header, $notify_message, $footer );
else :
	$header_content_type = 'text/plain';
	$message = $plain_text;

	// Strip all HTML out for plain text
	$notify_message = wp_strip_all_tags( $notify_message );

	// Either prepend the notification message to the submitted entry, or send by itself
	if ( !empty( $form_settings->form_notification_entry ) )
		$auto_response_email = "$notify_message\n\n" . $message;
	else
		$auto_response_email = $notify_message;
endif;

// Wrap lines longer than 70 words to meet email standards
$message = wordwrap( $message, 70 );

// Use a default From name if one has not been set
$from_name = ( empty( $header_from_name ) ) ? 'WordPress' : $header_from_name;

// Use the admin_email as the From email
$from_email = get_site_option( 'admin_email' );

// Get the site domain and get rid of www.
$sitename = strtolower( $_SERVER['SERVER_NAME'] );
if ( substr( $sitename, 0, 4 ) == 'www.' )
	$sitename = substr( $sitename, 4 );

// Get the domain from the admin_email
list( $user, $domain ) = explode( '@', $from_email );

// If site domain and admin_email domain match, use admin_email, otherwise a same domain email must be created
$from_email = ( $sitename == $domain ) ? $from_email : "wordpress@$sitename";

// Settings - Sender Mail Header
$settings_sender_header = isset( $vfb_settings['sender-mail-header'] ) ? $vfb_settings['sender-mail-header'] : $from_email;

// Allow Sender email to be filtered
$from_email = apply_filters( 'vfb_sender_mail_header', $settings_sender_header, $form_id );

$reply_to  = "\"$from_name\" <$header_from>";
$headers[] = "Sender: $from_email";
$headers[] = "From: $reply_to";
$headers[] = "Reply-To: $reply_to";
$headers[] = "Content-Type: $header_content_type; charset=\"" . get_option('blog_charset') . "\"";

// Allow main email headers to be filtered
$headers = apply_filters( 'vfb_email_headers', $headers, $from_name, $header_from, $from_email, $header_content_type, $form_id );

// Allow form subject to be filtered
$form_subject   = apply_filters( 'vfb_form_subject', $form_settings->form_subject, $form_id, $this->new_entry_id );
$form_subject 	= wp_specialchars_decode( $form_subject, ENT_QUOTES );

// Allow notify subject to be filtered
$notify_subject = apply_filters( 'vfb_notify_subject', $form_settings->form_notification_subject, $form_id, $this->new_entry_id );
$notify_subject = wp_specialchars_decode( $notify_subject, ENT_QUOTES );

// Allow attachments to be unattached in main email
$attachments = apply_filters( 'vfb_attachments_email', $attachments, $form_id );

// Sanitize main emails_to
$emails_to = array_map( 'sanitize_email', $form_settings->form_to );

// Email Rule setting
$email_rules_setting = absint( $form->form_email_rule_setting );

// If Email Rules have been set, find matches and send
if ( $email_rules_setting ) :
	$email_rules = maybe_unserialize( $form->form_email_rule );
	$rules = $email_rules['rules'];

	foreach ( $rules as $rule ) :

		$email   = sanitize_email( $rule['email'] );
		$id      = $rule['field'];
		$value   = $rule['option'];
		$data    = isset( $_POST['vfb-' . $id ] ) ? esc_html( $_POST['vfb-' . $id ] ) : '';

		if ( !empty( $data ) && $data == $value )
			wp_mail( $email, $form_subject, $message, $headers, $attachments );

	endforeach;

endif;

do_action_ref_array( 'vfb_override_email_' . $form_id, array( &$emails_to, $form_subject, $message, $headers, $attachments ) );

// If action is set, email function is being overridden
if ( !has_action( 'vfb_override_email_' . $form_id ) ) :
	do_action( 'vfb_before_email', $form_id, $this->new_entry_id );

	// Allow email to be skipped
	$skip_email = apply_filters( 'vfb_send_email', $settings_send_email, $form_id, $is_spam );

	if ( !$skip_email ) :
		// Send the mail
		foreach ( $emails_to as $email ) {
			// Skip sending if empty
			if ( !empty( $email ) )
				wp_mail( $email, $form_subject, $message, $headers, $attachments );
		}
	endif;

	do_action( 'vfb_after_email', $form_id, $this->new_entry_id );
endif;

// Send auto-responder email
if ( !empty( $form_settings->form_notification_setting ) ) :

	$attachments = ( $form_settings->form_notification_entry !== '' ) ? $attachments : '';

	// Allow attachments to be unattached in notification email
	$attachments = apply_filters( 'vfb_attachments_email_notify', $attachments, $form_id );

	// Reset headers for notification email
	$reply_name	  = function_exists( 'mb_encode_mimeheader' ) ? mb_encode_mimeheader( stripslashes( $form_settings->form_notification_email_name ) ) : stripslashes( $form_settings->form_notification_email_name );
	$reply_email  = $form_settings->form_notification_email_from;
	$reply_to     = "\"$reply_name\" <$reply_email>";
	$headers[]    = "Sender: $from_email";
	$headers[]    = "From: $reply_to";
	$headers[]    = "Reply-To: $reply_to";
	$headers[]    = "Content-Type: $header_content_type; charset=\"" . get_option('blog_charset') . "\"";

	$headers = apply_filters( 'vfb_email_notify_headers', $headers, $from_name, $header_from, $from_email, $header_content_type, $form_id );

	do_action_ref_array( 'vfb_override_email_notify_' . $form_id, array( &$copy_email, $notify_subject, $auto_response_email, $headers, $attachments ) );

	// If action is set, email function is being overridden
	if ( !has_action( 'vfb_override_email_notify_' . $form_id ) ) :
		do_action( 'vfb_before_notify_email', $form_id, $this->new_entry_id );

		// Allow notify email to be skipped
		$skip_email_notify = apply_filters( 'vfb_send_notify_email', $settings_send_email_notify, $form_id, $is_spam );

		if ( !$skip_email_notify ) :
			// Skip sending if empty
			if ( !empty( $copy_email ) )
				wp_mail( $copy_email, $notify_subject, $auto_response_email, $headers, $attachments );
		endif;

		do_action( 'vfb_after_notify_email', $form_id, $this->new_entry_id );
	endif;

endif;
