jQuery( function($){
	$( document ).ready( function() {

		jQuery('.chosen-container').width(200);

		jQuery("#wp-admin-bar-my-account .ab-sub-wrapper").css({ visibility:"hidden", display: "block" });

		jQuery('select.ajax_chosen_shop_as_user_search_users')
			.ajaxChosen(
				{
					method:			'GET',
					url: 			woocommerce_shop_as_customer_params.ajax_url,
					dataType: 		'json',
					afterTypeDelay: 100,
					minTermLength: 	1,
					data: {
						action: 	'woocommerce_json_shop_as_customers_search',
						security: 	woocommerce_shop_as_customer_params.nonce
					}
				},
				function (data){
					var terms = {};

					$(".searched-switch-links").html("");

					$.each(data, function (i, val) {
						terms[val.id] = val.label;
						if ( val.id !== undefined ) {
							$(".searched-switch-links").append("<div data-id='"+val.id+"' data-link='"+val.link+"'></div>");
						}
					});

					return terms;
				}
			)
			.on("change", function() {
				var user_id = $(this).val()
				if ( user_id != "") {
					
					var link = $(".searched-switch-links").find("div[data-id='"+user_id+"']").attr("data-link");
					
					// Auto switch to the new user when selected.
					document.location = link;
					
					//$("#wp-admin-bar-search-users").append("<a class='sac-switch-link' href='"+link+"'>Switch</a>");
				}
				else {
					$(".shop-as-customer-switch-button").remove(".sac-switch-link");
				}
			});

		jQuery("#wp-admin-bar-my-account .ab-sub-wrapper").css({ visibility:"", display: "" });

		var button_clicked;

		if ( jQuery('#shop_as_customer_save_order' ).length) {
			jQuery('form.checkout').on( 'click', '#shop_as_customer_save_order', function() {
				button_clicked = '#shop_as_customer_save_order';
			});
		}
		if ( jQuery('#shop_as_customer_place_order').length ) {
			jQuery('form.checkout').on( 'click', '#shop_as_customer_place_order', function() {
				button_clicked = '#shop_as_customer_place_order';
			});
		}
		var payment_methods_dom = jQuery('.payment_methods.methods').clone();

		$('body').bind('checkout_error', function () {

			if ( ! jQuery('.payment_methods.methods.shop_as_customer_methods').length ) {
				payment_methods_dom.appendTo('.payment_methods_wrap').unwrap();
			}
			
			jQuery('.button-block.create-this-order-block').find('input').prop('type', 'submit');
			jQuery('.button-block.pay-order-order-block').find('input').prop('type', 'submit');
		});

		jQuery('form.checkout').bind('checkout_place_order', function() {
			
			if ( button_clicked == "#shop_as_customer_save_order" ) {
				
				if ( 1 > jQuery('.button-block.create-this-order-block .create-this-order-block-input').length ) {
					jQuery('form.checkout').append("<input type='hidden' class='create-this-order-block-input' name='woocommerce_checkout_save_order' value='true' />");
				}

				if ( jQuery('#shop_as_customer_place_order').length ) {
					jQuery('.button-block.pay-order-order-block').find("input").prop("type", "button");
				}
				
				if ( jQuery('.payment_methods.methods').length ) {
					payment_methods_dom = jQuery('.payment_methods.methods').clone();
					jQuery('.payment_methods.methods').addClass('shop_as_customer_methods');
					jQuery('.payment_methods.methods.shop_as_customer_methods').wrap('<ul class="payment_methods_wrap payment_methods methods"></ul>');
					jQuery('.payment_methods.methods.shop_as_customer_methods').remove();
				}
			}
			else if ( button_clicked == "#shop_as_customer_place_order" ) {

				if ( jQuery('#shop_as_customer_save_order').length ) {
					jQuery('form.checkout .create-this-order-block-input').remove();
					jQuery('.button-block.create-this-order-block').find("input").prop("type", "button");
				}
			}
			
			return true;
		});


		//Load TipTip Tootips
		function loadTipTip(){

			jQuery(".create-this-order-info-tooltip").tipTip({
				delay: 10,
				fadeIn: 70,
				fadeOut: 70,
				maxWidth: "290px",
				content: jQuery(".create-this-order-info-tooltip-html").html()
			});

			jQuery(".pay-order-order-info-tooltip").tipTip({
				delay: 10,
				fadeIn: 70,
				fadeOut: 70,
				maxWidth: "290px",
				content: jQuery(".pay-order-order-info-tooltip-html").html()
			});

			jQuery(".send-out-invoice-info-tooltip").tipTip({
				delay: 10,
				fadeIn: 70,
				fadeOut: 70,
				maxWidth: "290px",
				content: jQuery(".send-out-invoice-info-tooltip-html").html()
			});
			jQuery(".switch-back-view-info-tooltip").tipTip({
				delay: 10,
				fadeIn: 70,
				fadeOut: 70,
				maxWidth: "290px",
				content: jQuery(".switch-back-view-info-tooltip-html").html()
			});

		}

		//reload TipTip each time the checkout form is updated
		$('body').bind('updated_checkout', function(){
			loadTipTip();
		})

		//regular load of TipTip
		loadTipTip();

		// Move the Order notice to under the h2 (same method as WC uses)
		$("#shop-as-customer-message").insertAfter('.wrap > h2');

	});


});

