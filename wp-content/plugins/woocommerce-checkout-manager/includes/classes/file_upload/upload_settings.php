<?php 

/**
 * WooCommerce Checkout Manager
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
?>

<div class="widefat general-semi upload_files" border="0">

	<div class="section">
		<h3 class="heading"><?php _e('General File Upload', 'woocommerce-checkout-manager'); ?></h3>
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading checkbox">
			<div class="option">
				<input type="checkbox" name="wccs_settings[checkness][enable_file_upload]" value="true"<?php checked( !empty( $options['checkness']['enable_file_upload'] ), true ); ?> /><span></span>
				<div class="info-of"><?php _e('Allow Customers to Upload Files', 'woocommerce-checkout-manager');  ?></div>
			</div>
			<!-- .option -->
		</h3>
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading checkbox">
			<div class="option">
				<input type="checkbox" name="wccs_settings[checkness][cat_file_upload]" value="true"<?php checked( !empty( $options['checkness']['cat_file_upload'] ), true ); ?> /><span></span>
				<div class="info-of">

					<?php _e('Categorize Uploaded Files', 'woocommerce-checkout-manager'); ?> | <span style="cursor: pointer;" class="show_hide2"><a>read more</a></span>
					<span style="display:none;" class="slidingDiv2">
						<br /><br />
						<?php _e('Changes uploaded files location folder from', 'woocommerce-checkout-manager');  ?> <br />
						<strong><?php echo $upload_dir['url']; ?>/</strong> <br />
						<?php _e('to', 'woocommerce-checkout-manager');  ?><br />
						<strong><?php echo $upload_dir['baseurl']; ?>/wooccm_uploads/{order number}/</strong>
					</span>

				</div>
			</div>
			<!-- .option -->
		</h3> 
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading"><?php _e('Upload Title', 'woocommerce-checkout-manager'); ?></h3>
		<div class="option">
			<input type="text" name="wccs_settings[checkness][upload_title]" class="full-width" placeholder="<?php _e( 'Order Uploaded Files', 'woocommerce-checkout-manager' ); ?>" value="<?php echo ( isset( $options['checkness']['upload_title'] ) ? esc_attr( $options['checkness']['upload_title'] ) : '' ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading"><?php _e('Notification E-mail', 'woocommerce-checkout-manager');  ?></h3>
		<div class="option">
			<input type="text" name="wccs_settings[checkness][wooccm_notification_email]" class="full-width" value="<?php echo ( isset( $options['checkness']['wooccm_notification_email'] ) ? sanitize_email( $options['checkness']['wooccm_notification_email'] ) : '' ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading"><?php _e('Products', 'woocommerce-checkout-manager'); ?></h3>
		<div class="info-of"><?php _e('Allow File Upload', 'woocommerce-checkout-manager');  ?></div>
		<div class="option allow">
			<input type="text" name="wccs_settings[checkness][allow_file_upload]" class="full-width" placeholder="<?php _e( 'Enter Product ID(s); Example: 1674, 1423, 1234', 'woocommerce-checkout-manager' ); ?>" value="<?php echo ( !empty($options['checkness']['allow_file_upload']) ? sanitize_text_field( $options['checkness']['allow_file_upload'] ) : '' ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

	<div class="section">
		<div class="info-of"><?php _e('Deny File Upload', 'woocommerce-checkout-manager');  ?></div>
		<div class="option">
			<input type="text" name="wccs_settings[checkness][deny_file_upload]" class="full-width" placeholder="<?php _e( 'Enter Product ID(s); Example: 1674, 1423, 1234', 'woocommerce-checkout-manager' ); ?>" value="<?php echo ( !empty( $options['checkness']['deny_file_upload'] ) ? sanitize_text_field( $options['checkness']['deny_file_upload'] ) : '' ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading"><?php _e('Categories', 'woocommerce-checkout-manager');  ?></h3>
		<div class="info-of"><?php _e('Allow File Upload', 'woocommerce-checkout-manager');  ?></div>
		<div class="option allow">
			<input type="text" name="wccs_settings[checkness][allow_file_upload_cat]" class="full-width" placeholder="<?php _e( 'Enter Category Slug(s); Example: my-cat, flowers_in', 'woocommerce-checkout-manager' ); ?>" value="<?php echo ( !empty( $options['checkness']['allow_file_upload_cat'] ) ? sanitize_text_field( $options['checkness']['allow_file_upload_cat'] ) : '' ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

	<div class="section">
		<div class="info-of"><?php _e('Deny File Upload', 'woocommerce-checkout-manager');  ?></div>
		<div class="option">
			<input type="text" name="wccs_settings[checkness][deny_file_upload_cat]" class="full-width" placeholder="<?php _e( 'Enter Category Slug(s); Example: my-cat, flowers_in', 'woocommerce-checkout-manager' ); ?>" value="<?php echo ( !empty( $options['checkness']['deny_file_upload_cat'] ) ? sanitize_text_field( $options['checkness']['deny_file_upload_cat'] ) : '' ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading"><?php _e('General Alerts', 'woocommerce-checkout-manager');  ?></h3>
		<div class="info-of"><?php _e('Picture Editing Saved', 'woocommerce-checkout-manager');  ?></div>
		<div class="option allow">
			<input type="text" name="wccs_settings[checkness][picture_success]" class="full-width" placeholder="<?php _e( 'Picture Saved', 'woocommerce-checkout-manager' ); ?>" value="<?php echo ( !empty( $options['checkness']['picture_success'] ) ? sanitize_text_field( $options['checkness']['picture_success'] ) : __( 'Picture Saved!', 'woocommerce-checkout-manager' ) ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

	<div class="section">
		<div class="info-of"><?php _e('Deletion confirmation', 'woocommerce-checkout-manager');  ?></div>
		<div class="option">
			<input type="text" name="wccs_settings[checkness][file_delete]" class="full-width" placeholder="<?php _e( 'Delete', 'woocommerce-checkout-manager' ); ?>" value="<?php echo ( !empty( $options['checkness']['file_delete'] ) ? sanitize_text_field( $options['checkness']['file_delete'] ) : __( 'Delete', 'woocommerce-checkout-manager' ) ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

	<div class="section">

		<h3 class="heading"><?php _e('Restrictions', 'woocommerce-checkout-manager');  ?></h3>
		<div class="info-of"><?php _e('File types', 'woocommerce-checkout-manager');  ?></div>

		<div class="option allow">
			<input type="text" name="wccs_settings[checkness][file_types]" class="full-width" placeholder="png,jpeg,gif" value="<?php echo ( !empty( $options['checkness']['file_types'] ) ? sanitize_text_field( $options['checkness']['file_types'] ) : '' ); ?>" />
		</div>
		<!-- .option -->

		<div class="info-of"><?php _e('Number Of Files to Upload', 'woocommerce-checkout-manager');  ?></div>

		<div class="option allow">
			<input type="text" name="wccs_settings[checkness][file_upload_number]" class="full-width" placeholder="4" value="<?php echo ( !empty( $options['checkness']['file_upload_number'] ) ? absint( $options['checkness']['file_upload_number'] ) : '' ); ?>" />
		</div>
		<!-- .option -->

	</div>
	<!-- .section -->

	<div class="section">
		<div class="info-of"><?php _e('Allow Upload for Order Status', 'woocommerce-checkout-manager');  ?></div>
		<div class="option">
			<input type="text" name="wccs_settings[checkness][upload_os]" class="full-width" placeholder="completed" value="<?php echo ( !empty( $options['checkness']['upload_os'] ) ? sanitize_text_field( $options['checkness']['upload_os'] ) : '' ); ?>" />
		</div>
		<!-- .option -->
	</div>
	<!-- .section -->

</div>
<!-- .upload_files -->