<?php
/**
 * Additional Customer Details
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="top_content_container">
			
			<?php ec_special_title( __("Customer details", 'email-control'), array("border_position" => "center", "text_position" => "center") ); ?>

			<?php foreach ( $fields as $field ) : ?>
			    <p><strong><?php echo wp_kses_post( $field['label'] ); ?>:</strong> <span class="text"><?php echo wp_kses_post( $field['value'] ); ?></span></p>
			<?php endforeach; ?>
		
		</td>
	</tr>
</table>
