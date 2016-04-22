<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Stripe_Payouts_Affiliates_Admin {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'affwp_edit_affiliate_bottom', array( $this, 'receipient_id_field' ) );
		add_action( 'affwp_update_affiliate', array( $this, 'update_affiliate' ), 0 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Outputs the recipient ID field in the Edit Affiliate screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function receipient_id_field( $affiliate  ) {

		$receipient_id = affiliate_wp_stripe()->get_recipient_id( $affiliate->affiliate_id );

?>
		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="stripe_recipient_id"><?php _e( 'Stripe Recipient ID', 'affwp-stripe-payouts' ); ?></label>
				</th>

				<td>
					<input class="regular-text" type="text" name="stripe_recipient_id" id="stripe_recipient_id" value="<?php echo esc_attr( $receipient_id ); ?>"/>
					<p class="description"><?php _e( 'Affiliate\'s Recipient ID in your <a href="https://dashboard.stripe.com/recipients">Stripe account</a>.', 'affwp-stripe-payouts' ); ?></p>
				</td>

			</tr>

		</table>

<?php		
	}

	/**
	 * Saves the recipient ID when an affiliate is updated
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function update_affiliate( $data ) {

		if ( empty( $data['affiliate_id'] ) ) {
			return false;
		}

		if( ! current_user_can( 'manage_affiliates' ) ) {
			return;
		}

		$receipient_id = ! empty( $data['stripe_recipient_id'] ) ? sanitize_text_field( $data['stripe_recipient_id'] ) : '';

		update_user_meta( affwp_get_affiliate_user_id( $data['affiliate_id'] ), 'affwp_stripe_recipient_id', $receipient_id );
	}

	public function admin_notices() {

	}

}
new AffiliateWP_Stripe_Payouts_Affiliates_Admin;