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
 * Template Function Overrides
 *
 * @since 3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'woocommerce_firstdata_payment_fields' ) ) {

	/**
	 * Pluggable function to render the First Data checkout page payment fields
	 * form
	 *
	 * @since 3.0
	 * @param WC_Gateway_FirstData $wc_gateway_firstdata firstdata gateway class
	 */
	function woocommerce_firstdata_payment_fields( $wc_gateway_firstdata ) {

		// safely display the description, if there is one
		if ( $wc_gateway_firstdata->get_description() )
			echo '<p>' . wp_kses_post( $wc_gateway_firstdata->get_description() ) . '</p>';

		$card_defaults = array(
			'account-number' => '',
			'exp-month'      => '',
			'exp-year'       => '',
			'cvv'            => '',
		);

		// for the demo environment, display a notice and supply a default test card
		if ( 'demo' == $wc_gateway_firstdata->get_environment() ) {
			echo '<p>' . __( 'TEST MODE ENABLED', 'woocommerce-gateway-firstdata' ) . '</p>';

			$card_defaults = array(
				'account-number' => '4111111111111111',
				'exp-month'      => '1',
				'exp-year'       => date( 'Y' ) + 1,
				'cvv'            => '123',
			);
		}

		// tokenization is allowed if tokenization is enabled on the gateway
		$tokenization_allowed = $wc_gateway_firstdata->tokenization_enabled();

		// on the pay page there is no way of creating an account, so disallow tokenization for guest customers
		if ( $tokenization_allowed && is_checkout_pay_page() && ! is_user_logged_in() ) {
			$tokenization_allowed = false;
		}

		$tokens = array();
		if ( $tokenization_allowed && is_user_logged_in() ) {
			$tokens = $wc_gateway_firstdata->get_credit_card_tokens( get_current_user_id() );
		}

		// load the credit card form template file
		woocommerce_get_template(
			'checkout/firstdata-payment-fields.php',
			array(
				'tokens'               => $tokens,
				'card_defaults'        => $card_defaults,
				'tokenization_allowed' => $tokenization_allowed,
				'tokenization_forced'  => $wc_gateway_firstdata->tokenization_forced(),
			),
			'',
			wc_firstdata()->get_plugin_path() . '/templates/'
		);

		ob_start();
		?>
			// checkout page
			if ( $( 'form.checkout' ).length ) {
				$( 'body' ).bind( 'updated_checkout', function() {
					handleSavedCards();
				} );
			} else {
				handleSavedCards();
			}

			function handleSavedCards() {

				$( 'input[name=firstdata-token]:radio' ).change( function() {

					var savedCreditCardSelected = $( 'input[name=firstdata-token]:radio:checked' ).val(),
						$newCardSection = $('div.firstdata-new-card-form');

					// if no cards are marked as active (e.g. if a bank account is selected as the active payment method)
					// or a saved card is selected, hide the credit card form
					if ( savedCreditCardSelected ) {
						$newCardSection.slideUp( 200 );
					} else {
						// use new card
						$newCardSection.slideDown( 200 );
					}
				} ).change();

				// display the 'save payment method' option for guest checkouts if the 'create account' option is checked
				//  but only hide the input if there is a 'create account' checkbox (some themes just display the password)
				$( 'input#createaccount' ).change( function() {

					var $parentRow = $( 'input#firstdata-tokenize-card' ).closest( 'p.form-row' );

					if ( $( this ).is( ':checked' ) ) {
						$parentRow.slideDown();
						$parentRow.next().show();
					} else {
						$parentRow.hide();
						$parentRow.next().hide();
					}

				} ).change();
			}
		<?php
		wc_enqueue_js( ob_get_clean() );
	}
}



if ( ! function_exists( 'woocommerce_firstdata_show_my_payment_methods' ) ) {

	/**
	 * Pluggable function to render the First Data tokenized cards on the My Account
	 * page
	 *
	 * @since 3.0
	 * @param WC_Gateway_FirstData $wc_gateway_firstdata firstdata gateway class
	 */
	function woocommerce_firstdata_show_my_payment_methods( $wc_gateway_firstdata ) {

		$user_id = get_current_user_id();

		// get available saved payment methods
		$tokens = $wc_gateway_firstdata->get_credit_card_tokens( $user_id );

		// load the My Account - My Cards template file
		woocommerce_get_template(
			'myaccount/firstdata-my-cards.php',
			array(
				'tokens'               => $tokens,
				'wc_gateway_firstdata' => $wc_gateway_firstdata,
			),
			'',
			wc_firstdata()->get_plugin_path() . '/templates/'
		);

		// Add confirm javascript when deleting cards
		ob_start();
		?>
			$( 'a.wc-firstdata-delete-payment-method' ).click( function( e ) {
				if ( ! confirm( '<?php _e( 'Are you sure you want to delete this payment method?', 'woocommerce-gateway-firstdata' ); ?>') ) {
					e.preventDefault();
				}
			} );
		<?php
		wc_enqueue_js( ob_get_clean() );
	}

}
