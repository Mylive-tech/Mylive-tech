jQuery( document ).ready( function($) {

	$.fn.init_addon_totals = function() {

		$(this).on( 'keyup change', '.product-addon input, .product-addon textarea', function() {

			if ( $(this).attr('maxlength') > 0 ) {

				var value = $(this).val();
				var remaining = $(this).attr('maxlength') - value.length;

				$(this).next('.chars_remaining').find('span').text( remaining );
			}

		} );

		$(this).find(' .addon-custom, .addon-custom-textarea' ).each( function() {

			if ( $(this).attr('maxlength') > 0 ) {

				$(this).after('<small class="chars_remaining"><span>' + $(this).attr('maxlength') + '</span> ' + woocommerce_addons_params.i18n_remaining + '</small>' );

			}

		} );

		$(this).on( 'change', '.product-addon input, .product-addon textarea, .product-addon select, input.qty', function() {

			$(this).trigger( 'woocommerce-product-addons-update' );
		} );

		$(this).on( 'found_variation', function( event, variation ) {

			var $variation_form = $(this);
			var $totals         = $variation_form.find( '#product-addons-total' );

			if ( typeof( variation.display_price ) !== 'undefined' ) {

				$totals.data( 'price', variation.display_price );

			} else if ( $( variation.price_html ).find('.amount:last').size() ) {

				product_price = $( variation.price_html ).find('.amount:last').text();
				product_price = product_price.replace( woocommerce_addons_params.currency_format_symbol, '' );
				product_price = product_price.replace( woocommerce_addons_params.currency_format_thousand_sep, '' );
				product_price = product_price.replace( woocommerce_addons_params.currency_format_decimal_sep, '.' );
				product_price = product_price.replace(/[^0-9\.]/g, '');
				product_price = parseFloat( product_price );

				$totals.data( 'price', product_price );
			}

			$variation_form.trigger( 'woocommerce-product-addons-update' );
		} );

		$(this).on( 'woocommerce-product-addons-update', function() {

			var total         = 0;
			var $cart         = $(this);
			var $totals       = $cart.find( '#product-addons-total' );
			var product_price = $totals.data( 'price' );
			var product_type  = $totals.data( 'type' );

			// Move totals
			if ( product_type == 'variable' || product_type == 'variable-subscription' ) {
				$cart.find('.single_variation').after( $totals );
			}

			$cart.find( '.addon' ).each( function() {
				var addon_cost = 0;

				if ( $(this).is('.addon-custom-price') ) {
					addon_cost = $(this).val();
				} else if ( $(this).is('.addon-input_multiplier') ) {
					if( isNaN( $(this).val() ) || $(this).val() == "" ) { // Number inputs return blank when invalid
						$(this).val('');
						$(this).closest('p').find('.addon-alert').show();
					} else {
						if( $(this).val() != "" ){
							$(this).val( Math.ceil( $(this).val() ) );
						}
						$(this).closest('p').find('.addon-alert').hide();
					}
					addon_cost = $(this).data('price') * $(this).val();
				} else if ( $(this).is('.addon-checkbox, .addon-radio') ) {
					if ( $(this).is(':checked') )
						addon_cost = $(this).data('price');
				} else if ( $(this).is('.addon-select') ) {
					if ( $(this).val() )
						addon_cost = $(this).find('option:selected').data('price');
				} else {
					if ( $(this).val() )
						addon_cost = $(this).data('price');
				}

				if ( ! addon_cost )
					addon_cost = 0;

				total = parseFloat( total ) + parseFloat( addon_cost );
			} );

			if ( $cart.find('input.qty').size() ) {
				var qty = parseFloat( $cart.find('input.qty').val() );
			} else {
				var qty = 1;
			}

			if ( total > 0 && qty > 0 ) {

				total = parseFloat( total * qty );

				var formatted_addon_total = accounting.formatMoney( total, {
					symbol 		: woocommerce_addons_params.currency_format_symbol,
					decimal 	: woocommerce_addons_params.currency_format_decimal_sep,
					thousand	: woocommerce_addons_params.currency_format_thousand_sep,
					precision 	: woocommerce_addons_params.currency_format_num_decimals,
					format		: woocommerce_addons_params.currency_format
				} );

				if ( product_price ) {

					product_total_price = parseFloat( product_price * qty );

					var formatted_grand_total = accounting.formatMoney( product_total_price + total, {
						symbol 		: woocommerce_addons_params.currency_format_symbol,
						decimal 	: woocommerce_addons_params.currency_format_decimal_sep,
						thousand	: woocommerce_addons_params.currency_format_thousand_sep,
						precision 	: woocommerce_addons_params.currency_format_num_decimals,
						format		: woocommerce_addons_params.currency_format
					} );

				}

				var subscription_details = false;

				if ( $('.single_variation_wrap .subscription-details').length ) {
					subscription_details = $('.single_variation_wrap .subscription-details').clone().wrap('<p>').parent().html();
				} else if ( $('.product-type-subscription .subscription-details').length ) {
					subscription_details = $('.product-type-subscription .subscription-details').clone().wrap('<p>').parent().html();
				}

				if ( subscription_details ) {
					formatted_addon_total += subscription_details;
					if ( formatted_grand_total ) {
						formatted_grand_total += subscription_details;
					}
				}

				html = '<dl class="product-addon-totals"><dt>' + woocommerce_addons_params.i18n_addon_total + '</dt><dd><strong><span class="amount">' + formatted_addon_total + '</span></strong></dd>';

				if ( formatted_grand_total && '1' == $totals.data( 'show-grand-total' ) ) {
					html = html + '<dt>' + woocommerce_addons_params.i18n_grand_total + '</dt><dd><strong><span class="amount">' + formatted_grand_total + '</span></strong></dd>';
				}

				html = html + '</dl>';

				$totals.html( html );

			} else {
				$totals.empty();
			}

			$( 'body' ).trigger( 'updated_addons' );

		} );

		$(this).find( '.addon-custom, .addon-custom-textarea, .product-addon input, .product-addon textarea, .product-addon select, input.qty' ).change();

		// When default variation exists, 'found_variation' must be triggered
		$(this).find( '.variations select' ).change();
	}

	// Quick view
	$( 'body' ).on( 'quick-view-displayed', function() {
		$(this).find( '.cart:not(.cart_group)' ).each( function() {
			$(this).init_addon_totals();
		} );
	} );

	// Composites
	$( 'body .component' ).on( 'wc-composite-component-loaded', function() {
		$(this).find( '.cart' ).each( function() {
			$(this).init_addon_totals();
		} );
	} );

	// Initialize
	$( 'body' ).find( '.cart:not(.cart_group)' ).each( function() {
		$(this).init_addon_totals();
	} );

} );
