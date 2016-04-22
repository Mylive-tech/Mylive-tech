<?php

$recipient_id = affiliate_wp_stripe()->get_recipient_id( affwp_get_affiliate_id() );

if( ! empty( $_REQUEST['error'] ) ) {

	$error = array();
	$error['type']    = sanitize_text_field( urldecode( $_REQUEST['error'] ) );
	$error['message'] = sanitize_text_field( stripslashes( urldecode( $_REQUEST['message'] ) ) );

} else if( $recipient_id ) {
	Stripe::setApiKey( affiliate_wp_stripe()->get_secret_key() );
	try {
		$recipient = Stripe_Recipient::retrieve( $recipient_id );
		$card      = $recipient->cards->retrieve( $recipient->default_card );
	} catch ( Exception $e ) {
		$body  = $e->getJsonBody();
		$error = $body['error'];
	}
}

if( ! empty( $error ) ) : ?>

<div class="affwp-errors"><p class="affwp-error"><strong><?php echo $error['type']; ?></strong>: <?php echo $error['message']; ?></p></div>

<?php endif; ?>

<?php if( ! empty( $card ) ) : ?>

<p id="affwp_current_card">
	<label><?php _e( 'Your current card details:', 'affwp-stripe-payouts' ); ?></label>
	<span class="affwp-card-line affwp-description"><?php printf( __( 'Type: %s', 'affwp-stripe-payouts' ), $card->brand ); ?></span>
	<span class="affwp-card-line affwp-description"><?php printf( __( 'Last Four: %s', 'affwp-stripe-payouts' ), $card->last4 ); ?></span>
	<span class="affwp-card-line affwp-description"><?php printf( __( 'Expires: %s / %s', 'affwp-stripe-payouts' ), $card->exp_month, $card->exp_year ); ?></span>
</p>
<?php endif; ?>
<fieldset id="affwp_payout_method_debit">
	<p id="affwp-card-number-wrap">
		<label for="card_number" class="affwp-label">
			<?php _e( 'Card Number', 'affwp-stripe-payouts' ); ?>
		</label>
		<span class="affwp-description"><?php _e( 'The (typically) 16 digits on the front of your debit card.', 'affwp-stripe-payouts' ); ?></span>
		<input type="text" autocomplete="off" id="card_number" class="card-number affwp-input required" placeholder="<?php _e( 'Card number', 'affwp-stripe-payouts' ); ?>" />
	</p>
	<p id="affwp-card-cvc-wrap">
		<label for="card_cvc" class="affwp-label">
			<?php _e( 'CVC', 'affwp-stripe-payouts' ); ?>
		</label>
		<span class="affwp-description"><?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'affwp-stripe-payouts' ); ?></span>
		<input type="text" size="4" autocomplete="off" id="card_cvc" class="card-cvc affwp-input required" placeholder="<?php _e( 'Security code', 'affwp-stripe-payouts' ); ?>" />
	</p>
	<p id="affwp-card-name-wrap">
		<label for="card_name" class="affwp-label">
			<?php _e( 'Name on the Card', 'affwp-stripe-payouts' ); ?>
		</label>
		<span class="affwp-description"><?php _e( 'The name printed on the front of your debit card.', 'affwp-stripe-payouts' ); ?></span>
		<input type="text" autocomplete="off" id="card_name" name="card_name" class="card-name affwp-input required" placeholder="<?php _e( 'Card name', 'affwp-stripe-payouts' ); ?>" />
	</p>
	<p id="affwp-card-expiration-wrap">
		<label for="card_exp_month" class="affwp-label">
			<?php _e( 'Expiration (MM/YY)', 'affwp-stripe-payouts' ); ?>
		</label>
		<span class="affwp-description"><?php _e( 'The date your debit card expires, typically on the front of the card.', 'affwp-stripe-payouts' ); ?></span>
		<select id="card_exp_month" class="card-expiry-month affwp-select affwp-select-small required">
			<?php for( $i = 1; $i <= 12; $i++ ) { echo '<option value="' . $i . '">' . sprintf ('%02d', $i ) . '</option>'; } ?>
		</select>
		<span class="exp-divider"> / </span>
		<select id="card_exp_year" class="card-expiry-year affwp-select affwp-select-small required">
			<?php for( $year = date( 'Y' ); $year <= date('Y') + 10; $year++ ) : ?>
				<option value="<?php echo esc_attr( $year ); ?>"><?php echo substr( $year, 2 ); ?></option>
			<?php endfor; ?>
		</select>
	</p>
	<p id="affwp-type-wrap">
		<label for="card_type" class="affwp-label">
			<?php _e( 'Type', 'affwp-stripe-payouts' ); ?>
		</label>
		<span class="affwp-description"><?php _e( 'Does this card belong to an Individual or a Corporation?', 'affwp-stripe-payouts' ); ?></span>
		<select id="recipient_type" name="recipient_type" class="card-type affwp-select affwp-select-small required">
			<option value="individual"><?php _e( 'Individual', 'affwp-stripe-payouts' ); ?></option>
			<option value="corporation"><?php _e( 'Corporation', 'affwp-stripe-payouts' ); ?></option>
		</select>
	</p>
	<p id="affwp-tax-id-wrap">
		<label for="tax_id" class="affwp-label">
			<?php _e( 'Tax ID', 'affwp-stripe-payouts' ); ?>
		</label>
		<span class="affwp-description"><?php _e( 'Your tax ID. Enter a Social security number for individuals or EIN for corporations.', 'affwp-stripe-payouts' ); ?></span>
		<input type="text" autocomplete="off" id="tax_id" name="tax_id" class="tax-id affwp-input required" placeholder="<?php _e( 'Tax ID', 'affwp-stripe-payouts' ); ?>" />
	</p>
	<div class="affwp-stripe-errors"><!--placeholder for errors from Stripe--></div>
</fieldset>