<?php
/**
 * Email Customizer - Helper functions
 *
 * Used globally as tools across the plugin.
 *
 * @since 2.01
 */

/**
 * Register Email Templates.
 *
 * A function for creating or modifying a email templates based on the
 * parameters given. The function will accept an array (second optional
 * parameter), along with a string for the post type name.
 *
 * @since	2.0
 * @date	20-08-2014
 *
 * @global 	array      			$ec_email_templates	List of email templates.
 *
 * @param 	string				$template_id	Email template id, must not exceed 20 characters.
 * @param	array|string		$args {
 *     Array or string of arguments for registering email template.
 * }
 * @return	object|WP_Error		The registered post type object, or an error object.
 */
if ( !function_exists('ec_register_email_template') ) {
	function ec_register_email_template( $template_id, $args ) {
		
		global $ec_email_templates;
		
		if ( !is_array( $ec_email_templates ) )
			$ec_email_templates = array();
		
		$defaults = array(
			'name'                	=> $template_id,
			'description'           => '',
			'settings'           	=> false,
		);
		$args = wp_parse_args( $args, $defaults );
		
		if ( strlen( $template_id ) > 40 ) {
			_doing_it_wrong( __FUNCTION__, __( 'Template IDs cannot exceed 20 characters in length', 'email-control' ) );
			return new WP_Error( 'template_id_too_long', __( 'Template IDs cannot exceed 20 characters in length', 'email-control' ) );
		}

		$ec_email_templates[ $template_id ] = $args;
		
		return $args;
	}
}

/**
 * Apply CSS to content inline.
 *
 * @param string|null $content
 * @param string|null $css
 * @return string
 */
function ec_apply_inline_styles( $content = '', $css = '' ) {
	
	// load EmogrifierEC.
	if ( !class_exists('EmogrifierEC') ) {
		require_once( WC_EMAIL_CONTROL_DIR . '/includes/emogrifier/Emogrifier.php' );
	}
	
	try {
		
		// Apply EmogrifierEC to inline the CSS.
		$emogrifier = new EmogrifierEC();
		$emogrifier->setHtml( $content );
		$emogrifier->setCss( strip_tags( $css ) );
		$content = $emogrifier->emogrify();
	}
	catch ( Exception $e ) {

		$logger = new WC_Logger();
		$logger->add( 'emogrifier', $e->getMessage() );
	}
	
	return $content;
}

/**
 * Backup mb_convert_encoding function
 *
 * backup if php module php_mbstring is not active on server.
 * Simply a backup to avoid errors. User should get module activated.
 *
 * @author cxThemes
 */
if ( !function_exists( 'mb_convert_encoding' ) ) {
	function mb_convert_encoding ( $string, $type = 'HTML-ENTITIES', $encoding = 'utf-8' ) {
		
		//$string = htmlentities( $string, ENT_COMPAT, $encoding, false);
		//return html_entity_decode( $string );
		return $string;
	}
	
	//$string = 'Test:!"$%&/()=ÖÄÜöäü<<';
	//echo mb_convert_encoding($string, 'HTML-ENTITIES', 'utf-8');
	//echo htmlspecialchars_decode( utf8_decode( htmlentities( $string, ENT_COMPAT, 'utf-8', false) ) );
}

/**
 * Get Option - NOT USED.
 *
 * @param  string  $key   the full key of the field ec_supreme_...
 * @param  boolean $autop whether to autop and style the return value.
 * @return string         option, or it's default.
 */

function ec_get_option( $key, $autop = FALSE ) {
	
	$return = '';
	
	// We're in customier preview so just return the posted value.
	if ( isset( $_REQUEST[$key] ) ) {
	
		$return = stripslashes( $_REQUEST[$key] );
	}
	else {
		
		// Get selected template.
		$ec_template_selected = false;
		if ( get_option( 'ec_template' ) ) {
			$ec_template_selected = get_option( 'ec_template' );
		}
		if ( isset( $_REQUEST['ec_email_template'] ) ) {
			$ec_template_selected = $_REQUEST['ec_email_template'];
		}
		
		// Get the ec_key named setttings of the selected template.
		$settings = ec_get_settings( $ec_template_selected );
		
		// Get the default if there is one.
		$default = FALSE;
		if ( isset( $settings[$key]['default'] ) ) $default = $settings[$key]['default'];
		
		$return = get_option( $key, $default );
	}
	
	$return = __( $return, 'email-control' );
	$return = do_shortcode( $return );
	
	// stylise certain content types, eg textarea - NOT IN USE. RATHER RELY ON FIELD TYPE TEXTAREA.
	if ( $autop ) {
		$return = wptexturize( $return );
		$return = wpautop( $return );
	}
	
	// stylise certain content types, eg textarea
	if ( 'textarea' == EC_Settings::get_option_array( $key, 'type' ) ) {
		$return = wptexturize( $return );
		$return = wpautop( $return );
	}
	
	// Return the option.
	return __( $return, 'email-control' );
}

/**
 * Helper function to check if a template will work with the current WooCommerce version.
 *
 * @param    string    $template_id   Template id eg `supreme` to check.
 * @return   boolean
 */
function ec_check_template_version( $template_id ) {
	global $ec_email_templates;
	
	if ( ! isset( $ec_email_templates[$template_id] ) ) return TRUE;
	
	$woocommerce_required_version = ( isset( $ec_email_templates[$template_id]['woocoomerce_required_version'] ) ) ? $ec_email_templates[$template_id]['woocoomerce_required_version'] : WC_EMAIL_CONTROL_REQUIRED_WOOCOMMERCE_VERSION ;
	return version_compare( get_option( 'woocommerce_version' ), $woocommerce_required_version, '>' );
}

?>