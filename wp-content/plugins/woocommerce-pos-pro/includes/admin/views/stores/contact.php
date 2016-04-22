<?php
/**
 * Contact Details meta box
 */
?>

<div class="clearfix">
	<div class="form-group">
		<label for="pos_url"><?php /* translators: wordpress */ _e( 'Web Address' ) ?></label>
		<input name="pos_url" id="pos_url" type="text" value="<?php echo get_post_meta( $post->ID, '_url', true ); ?>" class="">
	</div>
	<div class="form-group">
		<label for="pos_phone"><?php /* translators: woocommerce */ _e( 'Telephone', 'woocommerce' ) ?></label>
		<input name="pos_phone" id="pos_phone" type="text" value="<?php echo get_post_meta( $post->ID, '_phone', true ); ?>" class="">
	</div>
	<div class="form-group">
		<label for="pos_email"><?php /* translators: wordpress */ _e( 'Email' ) ?></label>
		<input name="pos_email" id="pos_email" type="text" value="<?php echo get_post_meta( $post->ID, '_email', true ); ?>" class="">
	</div>
</div>