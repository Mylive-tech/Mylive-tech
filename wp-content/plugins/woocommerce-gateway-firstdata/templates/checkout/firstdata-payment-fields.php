<?php
/**
 * WooCommerce First Data
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce First Data to newer
 * versions in the future. If you wish to customize WooCommerce First Data for your
 * needs please refer to http://docs.woothemes.com/document/firstdata/
 *
 * @package     WC-First-Data/Templates
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * The Checkout page First Data credit card form
 *
 * @param array $tokens optional array of credit card tokens
 * @param array $card_defaults optional card defaults to pre-populate the form fields
 * @param boolean $tokenization_allowed true if tokenization is allowed (enabled in gateway and customer logged in), false otherwise
 *
 * @version 3.0
 * @since 3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<style type="text/css">#payment ul.payment_methods li label[for='payment_method_firstdata'] img:nth-child(n+2) { margin-left:1px; }</style>
<fieldset>
	<?php
	if ( $tokens ) : ?>
		<p class="form-row form-row-wide">
			<a class="button" style="float:right;" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>#woocommerce-firstdata-my-payment-methods"><?php echo wp_kses_post( apply_filters( 'wc_firstdata_manage_my_cards', __( "Manage My Cards", 'woocommerce-gateway-firstdata' ) ) ); ?></a>
			<?php foreach( $tokens as $token => $card ) : ?>
				<input type="radio" id="firstdata-token-<?php echo esc_attr( $token ); ?>" name="firstdata-token" style="width:auto;" value="<?php echo esc_attr( $token ); ?>" <?php checked( $card['active'] ); ?>/>
				<label style="display:inline;" for="firstdata-token-<?php echo esc_attr( $token ); ?>"><?php printf( __( '%s ending in %s (expires %s)', 'woocommerce-gateway-firstdata' ), esc_html( $card['type'] ), esc_html( $card['last_four'] ), esc_html( substr( $card['exp_date'], 0, 2 ) . '/' . substr( $card['exp_date'], -2 ) ) ); ?></label><br />
			<?php endforeach; ?>
			<input type="radio" id="firstdata-use-new-card" name="firstdata-token" style="width:auto;" value="" /> <label style="display:inline;" for="firstdata-use-new-card"><?php echo __( 'Use a new credit card', 'woocommerce-gateway-firstdata' ); ?></label>
		</p>
		<div class="clear"></div>
	<?php endif; ?>

	<div class="firstdata-new-card-form" <?php echo ( $tokens ? 'style="display:none;"' : '' ); ?>>
		<p class="form-row form-row-first">
			<label for="firstdata-account-number"><?php esc_html_e( 'Credit Card Number', 'woocommerce-gateway-firstdata'); ?> <span class="required">*</span></label>
			<input type="text" class="input-text" id="firstdata-account-number" name="firstdata-account-number" maxlength="19" autocomplete="off" value="<?php echo esc_attr( $card_defaults['account-number'] ); ?>" />
		</p>

		<p class="form-row form-row-last">
			<label for="firstdata-exp-month"><?php esc_html_e( 'Expiration Date', 'woocommerce-gateway-firstdata' ); ?> <span class="required">*</span></label>
			<select name="firstdata-exp-month" id="firstdata-exp-month" class="woocommerce-select woocommerce-cc-month" style="width:auto;">
				<option value=""><?php esc_html_e( 'Month', 'woocommerce-gateway-firstdata' ) ?></option>
				<?php foreach ( range( 1, 12 ) as $month ) : ?>
					<option value="<?php printf( '%02d', $month ) ?>" <?php selected( $card_defaults['exp-month'], $month ); ?>><?php printf( '%02d', $month ) ?></option>
				<?php endforeach; ?>
			</select>
			<select name="firstdata-exp-year" id="firstdata-exp-year" class="woocommerce-select woocommerce-cc-year" style="width:auto;">
				<option value=""><?php esc_html_e( 'Year', 'woocommerce-gateway-firstdata' ) ?></option>
				<?php foreach ( range( date( 'Y' ), date( 'Y' ) + 10 ) as $year ) : ?>
					<option value="<?php echo $year ?>" <?php selected( $card_defaults['exp-year'], $year ); ?>><?php echo $year ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<div class="clear"></div>

		<p class="form-row form-row-wide">
			<label for="firstdata-cvv"><?php esc_html_e( 'Card Security Code', 'woocommerce-gateway-firstdata' ) ?> <span class="required">*</span></label>
			<input type="text" class="input-text" id="firstdata-cvv" name="firstdata-cvv" maxlength="4" style="width:60px" autocomplete="off" value="<?php echo esc_attr( $card_defaults['cvv'] ); ?>" />
		</p>
		<div class="clear"></div>

		<?php
		if ( $tokenization_allowed || $tokenization_forced ) :
			if ( $tokenization_forced ) :
				?>
				<input name="firstdata-tokenize-card" id="firstdata-tokenize-card" type="hidden" value="true" />
				<?php
			else:
				?>
				<p class="form-row">
					<input name="firstdata-tokenize-card" id="firstdata-tokenize-card" type="checkbox" value="true" style="width:auto;" />
					<label for="firstdata-tokenize-card" style="display:inline;"><?php echo wp_kses_post( apply_filters( 'wc_firstdata_tokenize_card_text', __( 'Securely Save Card to Account', 'woocommerce-gateway-firstdata' ) ) ); ?></label>
				</p>
				<div class="clear"></div>
				<?php
			endif;
		endif;
		?>
	</div>
</fieldset>
