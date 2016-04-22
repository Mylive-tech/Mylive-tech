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

?>