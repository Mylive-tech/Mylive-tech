<?php

class VisualFormBuilder_Pro_Options_Helper {

	private static $instance = null;

	/**
	 * field_name function.
	 *
	 * @access public
	 * @param mixed $field_id
	 * @param mixed $value
	 * @param mixed $size 'wide' or 'thin'
	 * @return void
	 */
	public function field_name( $field_id, $value, $size ) {
	?>
		<!-- Name -->
		<p class="description description-<?php echo $size; ?>">
			<label for="edit-form-item-name-<?php echo $field_id; ?>">
				<?php _e( 'Name' , 'visual-form-builder-pro'); ?>
	            <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Name', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( "A field's name is the most visible and direct way to describe what that field is for.", 'visual-form-builder-pro' ); ?>">(?)</span>
	        	<br />
				<input type="text" value="<?php echo stripslashes( esc_attr( $value ) ); ?>" name="field_name-<?php echo $field_id; ?>" class="widefat" id="edit-form-item-name-<?php echo $field_id; ?>" maxlength="255" />
			</label>
		</p>
	<?php
	}

	/**
	 * field_desc function.
	 *
	 * @access public
	 * @param mixed $field_id
	 * @param mixed $value
	 * @param mixed $size 'wide' or 'thin'
	 * @return void
	 */
	public function field_desc( $field_id, $value, $size ) {
	?>
		<!-- Description -->
		<p class="description description-wide">
			<label for="edit-form-item-description-<?php echo $field_id; ?>">
				<?php _e( 'Description' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Description', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'A description is an optional piece of text that further explains the meaning of this field. Descriptions are displayed below the field. HTML tags are allowed.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
            	<textarea name="field_description-<?php echo $field_id; ?>" class="widefat edit-menu-item-description" cols="20" rows="3" id="edit-form-item-description-<?php echo $field_id; ?>"><?php echo stripslashes( $value ); ?></textarea>
			</label>
		</p>
	<?php
	}

	/**
	 * css_classes function.
	 *
	 * @access public
	 * @param mixed $field_id
	 * @param mixed $value
	 * @param mixed $size 'wide' or 'thin'
	 * @return void
	 */
	public function css_classes( $field_id, $value, $size ) {
	?>
		<!-- CSS Classes -->
		<p class="description description-<?php echo $size; ?>">
	        <label for="edit-form-item-css-<?php echo $field_id; ?>">
	            <?php _e( 'CSS Classes' , 'visual-form-builder-pro'); ?>
	            <span class="vfb-tooltip" rel="<?php esc_attr_e( 'For each field, you can insert your own CSS class names which can be used in your own stylesheets.', 'visual-form-builder-pro' ); ?>" title="<?php esc_attr_e( 'About CSS Classes', 'visual-form-builder-pro' ); ?>">(?)</span>
	            <br />
	            <input type="text" value="<?php echo stripslashes( esc_attr( $value ) ); ?>" name="field_css-<?php echo $field_id; ?>" class="widefat" id="edit-form-item-css-<?php echo $field_id; ?>" />
	        </label>
	    </p>
	<?php
	}

	/**
	 * field_layout function.
	 *
	 * @access public
	 * @param mixed $field_id
	 * @param mixed $value
	 * @param mixed $size 'wide' or 'thin'
	 * @return void
	 */
	public function field_layout( $field_id, $value, $size ) {
	?>
		<!-- !Field Layout -->
		<p class="description description-<?php echo $size; ?>">
			<label for="edit-form-item-layout">
				<?php _e( 'Field Layout' , 'visual-form-builder-pro'); ?>
	            <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Field Layout', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Used to create advanced layouts. Align fields side by side in various configurations.', 'visual-form-builder-pro' ); ?>">(?)</span>
	        <br />
				<select name="field_layout-<?php echo $field_id; ?>" class="widefat" id="edit-form-item-layout-<?php echo $field_id; ?>">
					<option value="" <?php selected( $value, '' ); ?>><?php _e( 'Default' , 'visual-form-builder-pro'); ?></option>
	                <optgroup label="------------">
	                <option value="left-half" <?php selected( $value, 'left-half' ); ?>><?php _e( 'Left Half' , 'visual-form-builder-pro'); ?></option>
	                <option value="right-half" <?php selected( $value, 'right-half' ); ?>><?php _e( 'Right Half' , 'visual-form-builder-pro'); ?></option>
	                </optgroup>
	                <optgroup label="------------">
					<option value="left-third" <?php selected( $value, 'left-third' ); ?>><?php _e( 'Left Third' , 'visual-form-builder-pro'); ?></option>
	                <option value="middle-third" <?php selected( $value, 'middle-third' ); ?>><?php _e( 'Middle Third' , 'visual-form-builder-pro'); ?></option>
	                <option value="right-third" <?php selected( $value, 'right-third' ); ?>><?php _e( 'Right Third' , 'visual-form-builder-pro'); ?></option>
	                </optgroup>
	                <optgroup label="------------">
	                <option value="left-two-thirds" <?php selected( $value, 'left-two-thirds' ); ?>><?php _e( 'Left Two Thirds' , 'visual-form-builder-pro'); ?></option>
	                <option value="right-two-thirds" <?php selected( $value, 'right-two-thirds' ); ?>><?php _e( 'Right Two Thirds' , 'visual-form-builder-pro'); ?></option>
	                </optgroup>
	                <?php apply_filters( 'vfb_admin_field_layout', $value ); ?>
				</select>
			</label>
		</p>
	<?php
	}

	/**
	 * options_layout function.
	 *
	 * @access public
	 * @param mixed $field_id
	 * @param mixed $value
	 * @param mixed $size 'wide' or 'thin'
	 * @param mixed $type
	 * @return void
	 */
	public function options_layout( $field_id, $value, $size, $type ) {
	?>
		<!-- Options Layout -->
		<p class="description description-<?php echo $size; ?>">
			<label for="edit-form-item-size">
				<?php _e( 'Options Layout' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Options Layout', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Control the layout of radio buttons or checkboxes.  By default, options are arranged in One Column.', 'visual-form-builder-pro' ); ?>">(?)</span>
        		<br />
				<select name="field_size-<?php echo $field_id; ?>" class="widefat" id="edit-form-item-size-<?php echo $field_id; ?>"<?php echo ( $type == 'time' ) ? ' disabled="disabled"' : ''; ?>>
					<option value="" <?php selected( $value, '' ); ?>><?php _e( 'One Column' , 'visual-form-builder-pro'); ?></option>
                    <option value="two-column" <?php selected( $value, 'two-column' ); ?>><?php _e( 'Two Columns' , 'visual-form-builder-pro'); ?></option>
					<option value="three-column" <?php selected( $value, 'three-column' ); ?>><?php _e( 'Three Columns' , 'visual-form-builder-pro'); ?></option>
                    <option value="auto-column" <?php selected( $value, 'auto-column' ); ?>><?php _e( 'Auto Width' , 'visual-form-builder-pro'); ?></option>
				</select>
			</label>
		</p>
	<?php
	}

	/**
	 * field_size function.
	 *
	 * @access public
	 * @param mixed $field_id
	 * @param mixed $value
	 * @param mixed $size 'wide' or 'thin'
	 * @return void
	 */
	public function field_size( $field_id, $value, $size ) {
	?>
		<!-- Size -->
		<p class="description description-<?php echo $size; ?>">
			<label for="edit-form-item-size">
				<?php _e( 'Size' , 'visual-form-builder-pro'); ?>
	            <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Size', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Control the size of the field.  By default, all fields are set to Medium.', 'visual-form-builder-pro' ); ?>">(?)</span>
	    		<br />
				<select name="field_size-<?php echo $field_id; ?>" class="widefat" id="edit-form-item-size-<?php echo $field_id; ?>">
					<option value="small" <?php selected( $value, 'small' ); ?>><?php _e( 'Small' , 'visual-form-builder-pro'); ?></option>
	                <option value="medium" <?php selected( $value, 'medium' ); ?>><?php _e( 'Medium' , 'visual-form-builder-pro'); ?></option>
					<option value="large" <?php selected( $value, 'large' ); ?>><?php _e( 'Large' , 'visual-form-builder-pro'); ?></option>
					<?php apply_filters( 'vfb_admin_field_size', $value ); ?>
				</select>
			</label>
		</p>
	<?php
	}

	/**
	 * Helper function to get the class object. If instance is already set, return it.
	 * Else create the object and return it.
	 *
	 * @since 1.0.0
	 *
	 * @return object $instance Return the class instance
	 */
	public static function get_instance() {

		if ( null == self::$instance )
            self::$instance = new self;

        return self::$instance;

	}
}

// Instantiate the class
$vfb_pro_options_helper = VisualFormBuilder_Pro_Options_Helper::get_instance();