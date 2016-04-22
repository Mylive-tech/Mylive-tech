Stripe.setPublishableKey( affwp_stripe_vars.publishable_key );

jQuery(document).ready(function($) {

	var $form = $('#affwp-affiliate-dashboard-profile-form');
	var create_token = false;

	$form.on('submit', function(e) {

		$('#affwp-payout-options-wrap input').each(function() {

			var val = $(this).val();

			if( val.length ) {
				create_token = true;
			}

		});

		if( create_token ) {
		
			e.preventDefault();

			// createToken returns immediately - the supplied callback submits the form if there are no errors
			Stripe.card.createToken({
				number: $('.card-number').val(),
				cvc: $('.card-cvc').val(),
				exp_month: $('.card-expiry-month').val(),
				exp_year: $('.card-expiry-year').val()
			}, function( status, response ) {

				if ( response.error ) {

					// Show the errors on the form
					$form.find('.affwp-stripe-errors').addClass('affwp-errors').html('<p class="affwp-error">'+response.error.message+'</p>');
					$form.find('button').prop('disabled', false);

				} else {

					// response contains id and card, which contains additional card details
					var token = response.id;
					// Insert the token into the form so it gets submitted to the server
					$form.append( $('<input type="hidden" name="stripeToken" />').val(token) );

					// and submit
					$form.get(0).submit();
				}

			});

		}

	});

});