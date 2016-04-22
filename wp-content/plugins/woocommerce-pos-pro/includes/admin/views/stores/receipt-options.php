<?php
/**
 * Receipt Options meta box
 */
?>

<div class="clearfix">
	<div class="form-group">
		<label for="pos_personal_notes"><?php _e( 'Complimentary Close', 'woocommerce-pos-pro' ) ?></label>
		<textarea id="pos_personal_notes" name="pos_personal_notes" placeholder="<?php _e('Personal notes or season greetings, e.g. Thank You for Your Order! or Merry Christmas!', 'woocommerce-pos-pro' ); ?>"><?php echo get_post_meta( $post->ID, '_personal_notes', true ); ?></textarea>
	</div>
	<div class="form-group">
		<label for="pos_policies_conditions"><?php _e( 'Returns Policy', 'woocommerce-pos-pro' ) ?></label>
		<textarea id="pos_policies_conditions" name="pos_policies_conditions" placeholder="<?php _e('Returns policy, terms and conditions', 'woocommerce-pos-pro' ); ?>"><?php echo get_post_meta( $post->ID, '_policies_conditions', true ); ?></textarea>
	</div>
	<div class="form-group">
		<label for="pos_footer_imprint"><?php _e( 'Footer', 'woocommerce-pos-pro' ) ?></label>
		<textarea id="pos_footer_imprint" name="pos_footer_imprint" placeholder="<?php _e('Further footer information, e.g. copyright or branding', 'woocommerce-pos-pro' ); ?>"><?php echo get_post_meta( $post->ID, '_footer_imprint', true ); ?></textarea>
	</div>
</div>