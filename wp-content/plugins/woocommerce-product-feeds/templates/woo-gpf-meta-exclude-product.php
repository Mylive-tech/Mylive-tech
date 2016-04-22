<p>
	<input type="checkbox" id="woocommerce_gpf_excluded" name="_woocommerce_gpf_data[exclude_product]" {checked}>
	<label for="woocommerce_gpf_excluded">{hide_product_text}</label>
</p>
<script type="text/javascript">
	jQuery('#woocommerce_gpf_excluded').change(function() {
		if ( jQuery( '#woocommerce_gpf_excluded' ).is( ':checked' ) ) {
			jQuery( '#woocommerce_gpf_options' ).slideUp( 'fast' );
		} else {
			jQuery( '#woocommerce_gpf_options' ).slideDown( 'fast' );
		}
	});
	jQuery( '#woocommerce_gpf_excluded' ).change();
</script>