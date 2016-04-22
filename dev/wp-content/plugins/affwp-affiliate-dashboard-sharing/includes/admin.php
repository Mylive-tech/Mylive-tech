<?php

class AffiliateWP_Affiliate_Dashboard_Sharing_Admin {

	public function __construct() {
		add_filter( 'affwp_settings_tabs',           array( $this, 'setting_tab'       ) );
		add_action( 'admin_init',                    array( $this, 'register_settings' ) );
	}

	public function setting_tab( $tabs ) {
		$tabs['dashboard_sharing'] = __( 'Dashboard Sharing', 'affwp-affiliate-dashboard-sharing' );
		return $tabs;
	}

	public function register_settings() {

		add_settings_section(
			'affwp_settings_dashboard_sharing',
			__return_null(),
			'__return_false',
			'affwp_settings_dashboard_sharing'
		);

		add_settings_field(
			'affwp_settings[dashboard_sharing]',
			__( 'Twitter Text', 'affwp-affiliate-dashboard-sharing' ),
			array( $this, 'sharing_text' ),
			'affwp_settings_dashboard_sharing',
			'affwp_settings_dashboard_sharing'
		);

	}

	/**
	 * Sharing text
	 * @return string Text to share
	 */
	public function sharing_text() {
		$options 		= get_option( 'affwp_settings' );
		$options 		= isset( $options['dashboard_sharing'] ) ? $options['dashboard_sharing'] : '';
		$sharing_text 	= isset( $options['sharing_text'] ) ? $options['sharing_text'] : '';

	?>
		<input class="large-text" id="affwp_settings[dashboard_sharing][sharing_text]" name="affwp_settings[dashboard_sharing][sharing_text]" type="text" value="<?php echo esc_attr( $sharing_text ); ?>" />
		<label class="description" for="affwp_settings[dashboard_sharing][sharing_text]">
			<?php _e( 'The default text that will show when an affiliate shares to Twitter. Leave blank to use Site Title.', 'affwp-affiliate-dashboard-sharing' ); ?>
			</label>	

<?php }

}
new AffiliateWP_Affiliate_Dashboard_Sharing_Admin;