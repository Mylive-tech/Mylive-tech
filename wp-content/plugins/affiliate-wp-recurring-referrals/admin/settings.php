<?php

class AffiliateWP_Recurring_Admin {

	public function __construct() {
		add_filter( 'affwp_settings_integrations', array( $this, 'register_settings' ) );
	}

	public function register_settings( $settings = array() ) {

		$settings[ 'recurring' ] = array(
			'name' => __( 'Enable Recurring Referrals', 'affiliate-wp-recurrring' ),
			'desc' => __( 'Check this box to enable referral tracking on all subscription payments', 'affiliate-wp-recurrring' ),
			'type' => 'checkbox'
		);

		return $settings;

	}

}
new AffiliateWP_Recurring_Admin;