<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Stripe_Payouts_Admin {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {
		add_filter( 'affwp_settings_tabs',          array( $this, 'setting_tab' ) );
		add_filter( 'affwp_settings',               array( $this, 'settings'    ) );
		add_filter( 'affwp_settings_sanitize_text', array( $this, 'trim' ), 10, 2 );
	}

	/**
	 * Register the new settings tab
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function setting_tab( $tabs ) {
		$tabs['stripe'] = __( 'Stripe Payouts', 'affwp-stripe-payouts' );
		return $tabs;
	}

	/**
	 * Register the settings for our Stripe Payouts tab
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function settings( $settings ) {

		$settings['stripe'] = array(
			'stripe_test_mode' => array(
				'name' => __( 'Test Mode', 'affwp-stripe-payouts' ),
				'desc' => __( 'Check this box if you would like to use Stripe Payouts in Test Mode', 'affwp-stripe-payouts' ),
				'type' => 'checkbox'
			),
			'stripe_test_secret_key' => array(
				'name' => __( 'Test Secret Key', 'affwp-stripe-payouts' ),
				'desc' => __( 'Enter your Test Secret Key', 'affwp-stripe-payouts' ),
				'type' => 'text'
			),
			'stripe_test_publishable_key' => array(
				'name' => __( 'Test Publishable Key', 'affwp-stripe-payouts' ),
				'desc' => __( 'Enter your Test Publishable Key', 'affwp-stripe-payouts' ),
				'type' => 'text'
			),
			'stripe_live_secret_key' => array(
				'name' => __( 'Live Secret Key', 'affwp-stripe-payouts' ),
				'desc' => __( 'Enter your Live Secret Key', 'affwp-stripe-payouts' ),
				'type' => 'text'
			),
			'stripe_live_publishable_key' => array(
				'name' => __( 'Live Publishable Key', 'affwp-stripe-payouts' ),
				'desc' => __( 'Enter your Live Publishable Key', 'affwp-stripe-payouts' ),
				'type' => 'text'
			),
		);

		return $settings;
	}

	/**
	 * Trim whitespace from keys
	 *
	 * @access public
	 * @since 1.0.3
	 * @return string
	 */
	public function trim( $input, $key ) {

		switch( $key ) {
			case 'stripe_test_secret_key' :
			case 'stripe_test_publishable_key' :
			case 'stripe_live_secret_key' :
			case 'stripe_live_publishable_key' :
				$input = trim( $input );
				break;
		}

		return $input;
	}

}
new AffiliateWP_Stripe_Payouts_Admin;