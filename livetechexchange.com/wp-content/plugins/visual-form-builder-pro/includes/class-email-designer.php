<?php
/**
 * Class that builds our Entries table
 *
 * @since 1.2
 */
class VisualFormBuilder_Pro_Designer {
	/**
	 * field_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $field_table_name;

	/**
	 * form_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $form_table_name;

	/**
	 * entries_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $entries_table_name;

	public function __construct(){
		global $wpdb;

		// Setup global database table names
		$this->field_table_name 	= $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name 		= $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name 	= $wpdb->prefix . 'vfb_pro_entries';

		add_action( 'admin_init', array( &$this, 'design_options' ) );
	}

	public function design_options() {
		global $wpdb;

		// Query to get all forms
		$order = sanitize_sql_orderby( 'form_id ASC' );
		$where = apply_filters( 'vfb_pre_get_forms_emaildesign', '' );
		$forms = $wpdb->get_results( "SELECT * FROM $this->form_table_name WHERE 1=1 $where ORDER BY $order" );

		if ( !$forms ) :
			echo '<div class="vfb-form-alpha-list"><h3 id="vfb-no-forms">You currently do not have any forms.  Click on the <a href="' . esc_url( admin_url( 'admin.php?page=vfb-add-new' ) ) . '">New Form</a> button to get started.</h3></div>';

		else :

		$form_nav_selected_id = ( isset( $_REQUEST['form_id'] ) ) ? $_REQUEST['form_id'] : $forms[0]->form_id;

		// Loop through each form and assign a form id, if any
		?>

        <form method="post" id="design-switcher">
            <label for="form_id"><strong><?php _e( 'Select email to design:', 'visual-form-builder-pro' ); ?></strong></label>
            <select name="form_id" id="form_id">
		<?php
		foreach ( $forms as $form ) :
			if ( $form_nav_selected_id == $form->form_id ) :

				$email_design = unserialize( $form->form_email_design );

				$format 				= ( !empty( $email_design['format'] ) ) ? stripslashes( $email_design['format'] ) : 'html';
				$link_love 				= ( !empty( $email_design['link_love'] ) ) ? stripslashes( $email_design['link_love'] ) : 'yes';
				$footer_text 			= ( !empty( $email_design['footer_text'] ) ) ? stripslashes( $email_design['footer_text'] ) : '';
				$background_color 		= ( !empty( $email_design['background_color'] ) ) ? stripslashes( $email_design['background_color'] ) : '#eeeeee';
				$header_text 			= ( !empty( $email_design['header_text'] ) ) ? stripslashes( $email_design['header_text'] ) : $form->form_email_subject;
				$header_image 			= ( !empty( $email_design['header_image'] ) ) ? stripslashes( $email_design['header_image'] ) : '';
				$header_color 			= ( !empty( $email_design['header_color'] ) ) ? stripslashes( $email_design['header_color'] ) : '#810202';
				$header_text_color 		= ( !empty( $email_design['header_text_color'] ) ) ? stripslashes( $email_design['header_text_color'] ) : '#ffffff';
				$fieldset_color 		= ( !empty( $email_design['fieldset_color'] ) ) ? stripslashes( $email_design['fieldset_color'] ) : '#680606';
				$section_color 			= ( !empty( $email_design['section_color'] ) ) ? stripslashes( $email_design['section_color'] ) : '#5C6266';
				$section_text_color 	= ( !empty( $email_design['section_text_color'] ) ) ? stripslashes( $email_design['section_text_color'] ) : '#ffffff';
				$text_color 			= ( !empty( $email_design['text_color'] ) ) ? stripslashes( $email_design['text_color'] ) : '#333333';
				$link_color 			= ( !empty( $email_design['link_color'] ) ) ? stripslashes( $email_design['link_color'] ) : '#1b8be0';
				$row_color 				= ( !empty( $email_design['row_color'] ) ) ? stripslashes( $email_design['row_color'] ) : '#ffffff';
				$row_alt_color 			= ( !empty( $email_design['row_alt_color'] ) ) ? stripslashes( $email_design['row_alt_color'] ) : '#eeeeee';
				$border_color 			= ( !empty( $email_design['border_color'] ) ) ? stripslashes( $email_design['border_color'] ) : '#cccccc';
				$footer_color 			= ( !empty( $email_design['footer_color'] ) ) ? stripslashes( $email_design['footer_color'] ) : '#333333';
				$footer_text_color 		= ( !empty( $email_design['footer_text_color'] ) ) ? stripslashes( $email_design['footer_text_color'] ) : '#ffffff';
				$font_family			= ( !empty( $email_design['font_family'] ) ) ? stripslashes( $email_design['font_family'] ) : 'Arial';
				$header_font_size 		= ( !empty( $email_design['header_font_size'] ) ) ? stripslashes( $email_design['header_font_size'] ) : 32;
				$fieldset_font_size 	= ( !empty( $email_design['fieldset_font_size'] ) ) ? stripslashes( $email_design['fieldset_font_size'] ) : 20;
				$section_font_size 		= ( !empty( $email_design['section_font_size'] ) ) ? stripslashes( $email_design['section_font_size'] ) : 15;
				$text_font_size 		= ( !empty( $email_design['text_font_size'] ) ) ? stripslashes( $email_design['text_font_size'] ) : 13;
				$footer_font_size 		= ( !empty( $email_design['footer_font_size'] ) ) ? stripslashes( $email_design['footer_font_size'] ) : 11;

			endif;

			echo sprintf( '<option value="%1$d" %2$s id="%3$s">%1$d - %4$s</option>',
				$form->form_id,
				selected( $form->form_id, $form_nav_selected_id, 0 ),
				$form->form_key,
				stripslashes( $form->form_title )
			);
		endforeach;
?>
		</select>
        <?php submit_button( __( 'Select', 'visual-form-builder-pro' ), 'secondary', 'submit', false ); ?>
        </form>

	<div id="vfb-email-designer">
		<form id="email-design" method="post" enctype="multipart/form-data">
        	<input name="action" type="hidden" value="email_design" />
			<input name="form_id" type="hidden" value="<?php echo $form_nav_selected_id; ?>" />
            <?php wp_nonce_field( 'update-design-' . $form_nav_selected_id ); ?>

			<h3><?php _e( 'Colors', 'visual-form-builder-pro' ); ?></h3>
			<table class="form-table">
				  <?php if ( isset( $header_image ) && $header_image !== '' ) : ?>
                      <tr valign="top">
                        <th scope="row"><?php _e( 'Preview', 'visual-form-builder-pro' ); ?></th>
                        <?php @list( $width, $height, $type, $attr ) = getimagesize( $header_image ); ?>
                        <td><img <?php echo $attr; ?> src="<?php echo $header_image; ?>" /></td>
                      </tr>
                  <?php endif; ?>
                  <tr valign="top">
                    <th scope="row"><?php _e( 'Header Image', 'visual-form-builder-pro' ); ?></th>
                    <td>
                    	<input type="file" name="header_image" value="" />
                    	<input type="submit" value="Upload" class="button" id="upload-header-image" name="submit">

                    	<?php if ( isset( $header_image ) && $header_image !== '' ) : ?>
                    		<a href="<?php echo esc_url( wp_nonce_url( admin_url('admin.php?page=vfb-email-design&amp;action=email_delete_header&amp;form=' . $form_nav_selected_id ), 'delete-header-img-' . $form_nav_selected_id ) ); ?>" style="color:red;"><?php _e( 'Remove Header Image', 'visual-form-builder-pro' ); ?></a>
                    	<?php endif; ?>

                    	<p class="description"><?php _e( 'Images of exactly <strong>600 x 137</strong> pixels will be used as-is.', 'visual-form-builder-pro' ); ?></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-header-text"><?php _e( 'Header Text', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="header_text" id="vfb-header-text" value="<?php echo $header_text; ?>" />
                    	<p class="description"><?php _e( 'Form Subject is used by default. To reset to Form Subject, leave Header Text blank.', 'visual-form-builder-pro' ); ?></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-background-color"><?php _e( 'Body Background Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="background_color" id="vfb-background-color" class="vfb-color-picker" value="<?php echo $background_color; ?>" data-default-color="#eeeeee" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-header-color"><?php _e( 'Header Background Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="header_color" id="vfb-header-color" class="vfb-color-picker" value="<?php echo $header_color; ?>" data-default-color="#810202" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-header-text-color"><?php _e( 'Header Text Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="header_text_color" id="vfb-header-text-color" class="vfb-color-picker" value="<?php echo $header_text_color; ?>" data-default-color="#ffffff" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-fieldset-color"><?php _e( 'Fieldset Text Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="fieldset_color" id="vfb-fieldset-color" class="vfb-color-picker" value="<?php echo $fieldset_color; ?>" data-default-color="#680606" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-section-color"><?php _e( 'Section Background Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="section_color" id="vfb-section-color" class="vfb-color-picker" value="<?php echo $section_color; ?>" data-default-color="#5c6266" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-section-text-color"><?php _e( 'Section Text Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="section_text_color" id="vfb-section-text-color" class="vfb-color-picker" value="<?php echo $section_text_color; ?>" data-default-color="#ffffff" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-text-color"><?php _e( 'Text Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="text_color" id="vfb-text-color" class="vfb-color-picker" value="<?php echo $text_color; ?>" data-default-color="#333333" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-link-color"><?php _e( 'Link Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="link_color" id="vfb-link-color" class="vfb-color-picker" value="<?php echo $link_color; ?>" data-default-color="#1b8be0" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-row-color"><?php _e( 'Row Background Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="row_color" id="vfb-row-color" class="vfb-color-picker" value="<?php echo $row_color; ?>" data-default-color="#ffffff" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-row-alt-color"><?php _e( 'Alternate Row Background Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="row_alt_color" id="vfb-row-alt-color" class="vfb-color-picker" value="<?php echo $row_alt_color; ?>" data-default-color="#eeeeee" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-border-color"><?php _e( 'Row Border Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="border_color" id="vfb-border-color" class="vfb-color-picker" value="<?php echo $border_color; ?>" data-default-color="#cccccc" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-footer-color"><?php _e( 'Footer Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="footer_color" id="vfb-footer-color" class="vfb-color-picker" value="<?php echo $footer_color; ?>" data-default-color="#333333" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="vfb-footer-text-color"><?php _e( 'Footer Text Color', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="footer_text_color" id="vfb-footer-text-color" class="vfb-color-picker" value="<?php echo $footer_text_color; ?>" data-default-color="#ffffff" />
                    </td>
                  </tr>
			 </table>

			 <h3><?php _e( 'Fonts', 'visual-form-builder-pro' ); ?></h3>
			 <table class="form-table">
			  <tr valign="top">
			  	<th scope="row"><label for="vfb-font-family"><?php _e( 'Font Family', 'visual-form-builder-pro' ); ?></label></th>
				<td>
					<select name="font_family" id="vfb-font-family">
						<option value="Arial"<?php selected( $font_family, 'Arial' ); ?>>Arial</option>
						<option value="Georgia"<?php selected( $font_family, 'Georgia' ); ?>>Georgia</option>
						<option value="Helvetica"<?php selected( $font_family, 'Helvetica' ); ?>>Helvetica</option>
						<option value="Tahoma"<?php selected( $font_family, 'Tahoma' ); ?>>Tahoma</option>
						<option value="Verdana"<?php selected( $font_family, 'Verdana' ); ?>>Verdana</option>
					</select>
				</td>
			  </tr>
			  <tr valign="top">
			  	<th scope="row"><label for="vfb-header-font-size"><?php _e( 'Header Font Size', 'visual-form-builder-pro' ); ?></label></th>
				<td>
					<select name="header_font_size" id="vfb-header-font-size">
                        <?php $this->font_size_helper( $header_font_size ); ?>
					</select> px
				</td>
			  </tr>
			  <tr valign="top">
			  	<th scope="row"><label for="vfb-fieldset-font-size"><?php _e( 'Fieldset Font Size', 'visual-form-builder-pro' ); ?></label></th>
				<td>
					<select name="fieldset_font_size" id="vfb-fieldset-font-size">
                        <?php $this->font_size_helper( $fieldset_font_size ); ?>
					</select> px
				</td>
			  </tr>
			  <tr valign="top">
			  	<th scope="row"><label for="vfb-section-font-size"><?php _e( 'Section Font Size', 'visual-form-builder-pro' ); ?></label></th>
				<td>
					<select name="section_font_size" id="vfb-section-font-size">
					  <?php $this->font_size_helper( $section_font_size ); ?>
					</select> px
				</td>
			  </tr>
			  <tr valign="top">
			  	<th scope="row"><label for="vfb-text-font-size"><?php _e( 'Text Font Size', 'visual-form-builder-pro' ); ?></label></th>
				<td>
					<select name="text_font_size" id="vfb-text-font-size">
                        <?php $this->font_size_helper( $text_font_size ); ?>
					</select> px
				</td>
			  </tr>
              <tr valign="top">
			  	<th scope="row"><label for="vfb-footer-font-size"><?php _e( 'Footer Font Size', 'visual-form-builder-pro' ); ?></label></th>
				<td>
					<select name="footer_font_size" id="vfb-footer-font-size">
                        <?php $this->font_size_helper( $footer_font_size ); ?>
					</select> px
				</td>
			  </tr>
			</table>

            <h3><?php _e( 'Settings', 'visual-form-builder-pro' ); ?></h3>
			 <table class="form-table">
			  <tr valign="top">
			  	<th scope="row"><?php _e( 'Email Format', 'visual-form-builder-pro' ); ?></th>
				<td>
                	<?php $format = ( $format == '' ) ? 'html' : $format; ?>
                	<label for="vfb-format-html">
                		<input id="vfb-format-html" type="radio" name="format" value="html" <?php checked( $format, 'html' ); ?>  /> HTML
                	</label>
                	<br>
                    <label for="vfb-format-text">
                    	<input id="vfb-format-text" type="radio" name="format" value="text" <?php checked( $format, 'text' ); ?> /> Plain Text
                    </label>
                </td>
              </tr>
              <tr valign="top">
			  	<th scope="row"><?php _e( 'Link back to Visual Form Builder?', 'visual-form-builder-pro' ); ?></th>
				<td>
                	<?php $link_love = ( $link_love == '' ) ? 'yes' : $link_love; ?>
                	<label for="vfb-link-love-yes">
                		<input id="vfb-link-love-yes" type="radio" name="link_love" value="yes" <?php checked( $link_love, 'yes' ); ?>  /> Yes
                	</label>
                	<br>
                	<label for="vfb-link-love-no">
                    	<input id="vfb-link-love-no" type="radio" name="link_love" value="no" <?php checked( $link_love, 'no' ); ?> /> No
                	</label>
                </td>
              </tr>
              <tr valign="top">
                    <th scope="row"><label for="vfb-footer-text"><?php _e( 'Additional Footer Text', 'visual-form-builder-pro' ); ?></label></th>
                    <td>
                    	<input type="text" name="footer_text" id="vfb-footer-text" class="regular-text" value="<?php echo $footer_text; ?>" />
                    </td>
                  </tr>
             </table>
		<?php submit_button( __( 'Save Changes', 'visual-form-builder-pro' ) ); ?>
		</form>
	</div>
	<div id="vfb-email-design-preview">
		<h2><?php _e( 'Email Preview', 'visual-form-builder-pro' ); ?></h2>
		<p><?php _e( 'Save options to view your recent changes.', 'visual-form-builder-pro' ); ?></p>
		<iframe src="<?php echo plugins_url( 'visual-form-builder-pro' ); ?>/email-preview.php?form=<?php echo $form_nav_selected_id; ?>" width="100%" height="600" ></iframe>
	</div>
<?php
		endif;
	}

	public function font_size_helper( $field_name ) {
?>
        <option value="8"<?php selected( $field_name, 8 ); ?>>8</option>
        <option value="9"<?php selected( $field_name, 9 ); ?>>9</option>
        <option value="10"<?php selected( $field_name, 10 ); ?>>10</option>
        <option value="11"<?php selected( $field_name, 11 ); ?>>11</option>
        <option value="12"<?php selected( $field_name, 12 ); ?>>12</option>
        <option value="13"<?php selected( $field_name, 13 ); ?>>13</option>
        <option value="14"<?php selected( $field_name, 14 ); ?>>14</option>
        <option value="15"<?php selected( $field_name, 15 ); ?>>15</option>
        <option value="16"<?php selected( $field_name, 16 ); ?>>16</option>
        <option value="18"<?php selected( $field_name, 18 ); ?>>18</option>
        <option value="20"<?php selected( $field_name, 20 ); ?>>20</option>
        <option value="24"<?php selected( $field_name, 24 ); ?>>24</option>
        <option value="28"<?php selected( $field_name, 28 ); ?>>28</option>
        <option value="32"<?php selected( $field_name, 32 ); ?>>32</option>
        <option value="36"<?php selected( $field_name, 36 ); ?>>36</option>
<?php
	}
}
