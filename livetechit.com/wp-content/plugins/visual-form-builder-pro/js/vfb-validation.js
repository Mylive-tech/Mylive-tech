jQuery(document).ready(function($) {
	// !Validate each form on the page
	$( '.visual-form-builder' ).each( function() {
		$( this ).validate({
			rules: {
				recaptcha_response_field: {
		            required: true
		            /*remote: {
			            url: VfbAjax.ajaxurl,
			            type: 'POST',
			            async: false,
						cache: false,
			            data: {
			            	action: 'visual_form_builder_check_recaptcha',
			                recaptcha_challenge_field: function(){ return $( '#recaptcha_challenge_field' ).val(); },
			                recaptcha_response_field: function(){ return $( '#recaptcha_response_field' ).val(); }
			           }
			        }*/
		        }
			},
			onkeyup: function(element) {
				if ( element.type == 'password' )
					this.element(element);
				else
					return true;
			},
			errorClass : 'vfb-error',
			errorPlacement: function(error, element) {

				if ( ( element.is( ':radio' ) || element.is( ':checkbox' ) ) && !element.hasClass( 'vfb-likert-option' ) )
					error.appendTo( element.parent().parent() );
				else if ( element.is( ':password' ) )
					error.hide();
				else if ( 'recaptcha_response_field' == element.attr( 'id' ) )
					error.insertAfter( '#recaptcha_table' );
				else if ( element.hasClass( 'vfb-likert-option' ) )
					error.insertAfter( element.closest( 'tr' ) );
				else
					error.insertAfter( element );
			}
			/*messages: {
		        recaptcha_response_field: {
		            remote: 'Incorrect captcha answer. Please try again.'
		        }
		    }*/
		});
	});

	// Force bullets to hide, but only if list-style-type isn't set
	$( '.visual-form-builder li:not(.vfb-item-instructions li, .vfb-span li)' ).filter( function(){
		return $( this ).css( 'list-style-type' ) !== 'none';
	}).css( 'list-style', 'none' );

	// !Display jQuery UI date picker
	$( '.vfb-date-picker' ).each( function(){
		var vfb_dateFormat = $( this ).attr( 'data-dp-dateFormat' ) ? $( this ).attr( 'data-dp-dateFormat' ) : 'mm/dd/yy';

		$( this ).datepicker({
			dateFormat: vfb_dateFormat
		});
	});

	// !Disable the Submit button until you page to it
	isSubmitVisible();

	// !Page Break Next buttons
	$( document ).on( 'click', '.vfb-page-next', function( e ){
		e.preventDefault();

		var id = $( this ).attr( 'id' ),
			new_id = parseInt( id.replace(/page-/, '') ),
			form_id = '#' + $( this ).parents( 'form.visual-form-builder' ).attr( 'id' );

		// Check for validation errors before paging
		if ( !$( form_id ).valid() )
			return;

		$( '.page-' + new_id ).fadeIn();

		// Scroll to the next fieldset
		$( 'html, body' ).animate({
			scrollTop: $( '.page-' + new_id ).offset().top - 50
		});

		$( this ).fadeOut();

		// Disable the Submit button until you page to it
		isSubmitVisible();
	});

	// !Create color pickers
	$( '.colorPicker' ).each( function() {
		var field_id = $( this ).attr( 'id' ).match( new RegExp( /(\d+)$/g ), '' );

		$( this ).farbtastic( '#vfb-' + field_id );
	});

	$( '.colorPicker' ).hide();

	// !Display color pickers when focusing in input
	$( '.vfb-color-picker:input' ).focus( function() {
		var field_id = $( this ).attr( 'id' ).match( new RegExp( /(\d+)$/g ), '' );
		$( '#vfb-colorPicker-' + field_id ).show();
	}).blur( function() {
		var field_id = $( this ).attr( 'id' ).match( new RegExp( /(\d+)$/g ), '' );
		$( '#vfb-colorPicker-' + field_id ).hide();
	});

	// !Handle displaying autocomplete data for each field
	$( '.auto' ).each( function(){

		var form_id = $( this ).closest( 'form' ).find( 'input[name="form_id"]' ).val();
		var field_id = $( this ).attr( 'id' ).match( new RegExp( /(\d+)$/g ), '' );

		$( '#' + $( this ).attr( 'id' ) ).autocomplete({
			delay: 200,
			source: function( request, response ){
				$.ajax({
					url: VfbAjax.ajaxurl,
					type: 'GET',
					async: true,
					cache: false,
					dataType: 'json',
					data: {
						action: 'visual_form_builder_autocomplete',
						term: request.term,
						form: form_id,
						field: field_id[0]
					},
					success: function( data ){
						response( $.map( data, function( item ){
							return {
								value: item.value
							};
						}
					));
					}
				});
			}
		});
	});

	// !Only run conditional rules if settings exist
	if( window.VfbRules ) {
		var obj = $.parseJSON( VfbRules.rules );
		var selectors = [];

		vfb_rules( obj );

		$( obj ).each( function(){
			$.each( this.rules, function( i ){
				selectors.push( '[name^=vfb-' + this.field + ']' );
			});
		});

		$( selectors.join(',') ).change( function(){
			vfb_rules( obj );
		});
	}

	// !Checks whether the conditional field option has been checked/selected
	function vfb_is_value_selected( field, value ) {
		var input = $( '[name^=vfb-' + field + ']' );

	    if ( input.length > 0 ) {

	    	for ( var i = 0; i < input.length; i++ ) {
	    		// Use different logic if the fields are checkboxes or radios, compared to selects
	    		if ( $( input[ i ] ).is( '[type=checkbox],[type=radio]' ) ) {
		    		if ( vfb_get_value( $( input[ i ] ).val() ) == value && $( input[ i ] ).is( ':checked' ) )
		    			return true;
	    		}
	    		else if ( $( input[ i ] ).is( 'select' ) ) {
	    			if ( vfb_get_value( $( input[ i ] ).val() ) == value )
	    				return true;
	    		}
	    	}
	    }
	    else {
	    	if ( vfb_get_value( $( '[name^=vfb-' + field + ']' ).val() ) == value )
	    		return true;
	    }

	    return false;
	}

	// !Either returns nothing or an array of values
	function vfb_get_value( val ) {
	    if( !val )
	        return '';

	    var val = val.split( '|' );
	    return val[0];
	}

	// !Handle whether to show/hide the conditional rules
	function vfb_rules( obj ) {
		$( obj ).each( function(){
			var field_id = this.field_id, id_attr = 'item-vfb-' + field_id,
				show = this.conditional_show, rules = this.rules, logic = this.conditional_logic,
				matches = 0, initial_showHide = '';

			// Show or Hide the field initially for the user
			initial_showHide = ( show == 'show' ) ? 'hide' : 'show';

			$.each( rules, function( i ){
				// Strip slashes from JSON
				this.option = this.option.replace( '\\', '' );

		        if( ( this.condition == 'is' && vfb_is_value_selected( this.field, this.option ) ) || ( this.condition == 'isnot' && !vfb_is_value_selected( this.field, this.option ) ) )
		            matches++;
			});

			if ( ( logic == 'all' && matches == rules.length ) || ( logic == 'any' && matches > 0 ) )
				$( '[id=' + id_attr + ']' )[show]();
			else
				$( '[id=' + id_attr + ']' )[initial_showHide]();

			// Disable inputs if they are hidden
			if ( !$( '#' + id_attr ).is( ':visible' ) )
				$( '#' + id_attr + ' :input' ).prop( 'disabled', 'disabled' );
			else
				$( '#' + id_attr + ' :input' ).prop( 'disabled', false );
		});
	}

	// !Word Counter
	$( '.vfb-textarea-word-count' ).keyup( function() {
	    var wordCounts = {}, val = escapeUnicode( this.value ), matches = val.match(/\b/gi), finalCount = 0;

	    wordCounts[ this.id ] = matches ? matches.length / 2 : 0;

	    $.each( wordCounts, function( k, v ) {
	        finalCount += v;
	    });

	    $( this ).parent().find( '.vfb-word-count-total' ).text( finalCount );
	});

	// !Is Submit Button Visible when paging
	function isSubmitVisible() {
		if ( $( '.vfb-page .vfb-item-submit' ).is( ':visible' ) )
			$( '.vfb-page .vfb-submit' ).prop( 'disabled', false );
		else
			$( '.vfb-page .vfb-submit' ).prop( 'disabled', 'disabled' );
	}

	// !Strip HTML from textarea
	function stripHtml( value ) {
		// remove html tags and space chars
		return value.replace(/<.[^<>]*?>/g, ' ').replace(/&nbsp;|&#160;/gi, ' ')
		// remove punctuation
		.replace(/[.(),;:!?%#$'"_+=\/-]*/g,'');
	}

	// !Escape Unicode characters for word count
	function escapeUnicode( str ) {
		return str
			.replace(/[\u00E1\u00E2\u00E4\u00E6\u00E3\u00E5\u0101]+/gi, 'a')
			.replace(/[\u00E7\u0107\u010D]+/gi, 'c')
			.replace(/[\u00E8\u00E9\u00EA\u00EB\u0113\u0117\u0119]+/gi, 'e')
			.replace(/[\u00F4\u00F6\u00F2\u00F3\u0153\u00F8\u014D\u00F5]+/gi, 'o')
			.replace(/[\u00DF\u015B\u0161Ss]+/gi, 's')
			.replace(/[\u00FB\u00FC\u00F9\u00FA\u016B]+/gi, 'u')
			.replace(/[\u00FF]+/gi, 'y')
			.replace(/[\u017E\u017A\u017C]+/gi, 'z');
	}

	// !Username method
	$.validator.addMethod( 'vfbUsername', function( value, element ) {
		var response = true;

		if ( value.length > 0 ) {
			$.ajax({
				url: VfbAjax.ajaxurl,
				type: 'GET',
				async: false,
				cache: false,
				dataType: 'text',
				data: {
					action: 'visual_form_builder_check_username',
					username: value
				},
				success: function( data ){
					// If username exists, return FALSE/valid
					response = ( data == 'true' ) ? true : false;
				}
			});
		}

		return this.optional(element) || response;

		}, $.validator.messages.vfbUsername
	);

	// !Custom validation method to check multiple emails
	$.validator.addMethod( 'phone', function( value, element ) {
		// Strip out all spaces, periods, dashes, parentheses, and plus signs
		value = value.replace(/[\+\s\(\)\.\-\ ]/g, '');

		return this.optional(element) || value.length > 9 &&
			value.match( /^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/ );

		}, $.validator.messages.phone
	);

	$.validator.addMethod( 'ipv4', function( value, element ) {
		return this.optional(element) || /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i.test(value);
		}, $.validator.messages.ipv4
	);

	$.validator.addMethod( 'ipv6', function( value, element ) {
			return this.optional(element) || /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(value);
		}, $.validator.messages.ipv6
	);

	$.validator.addMethod( 'maxWords', function( value, element, params ) {
			return this.optional(element) || stripHtml(escapeUnicode(value)).match(/\b\w+\b/g).length <= params;
		}, $.validator.messages.maxWords
	);

	$.validator.addMethod( 'minWords', function( value, element, params ) {
			return this.optional(element) || stripHtml(escapeUnicode(value)).match(/\b\w+\b/g).length >= params;
		}, $.validator.messages.minWords
	);

	$.validator.addMethod( 'rangeWords', function( value, element, params ) {
			var valueStripped = stripHtml(escapeUnicode(value));
			var regex = /\b\w+\b/g;
			return this.optional(element) || valueStripped.match(regex).length >= params[0] && valueStripped.match(regex).length <= params[1];
		}, $.validator.messages.rangeWords
	);

	$.validator.addMethod( 'alphanumeric', function( value, element ) {
		return this.optional(element) || /^\w+$/i.test(value);
	}, $.validator.messages.alphanumeric );

	$.validator.addMethod( 'lettersonly', function( value, element ) {
		return this.optional(element) || /^[a-z]+$/i.test(value);
	}, $.validator.messages.lettersonly );

	$.validator.addMethod( 'nowhitespace', function( value, element ) {
		return this.optional(element) || /^\S+$/i.test(value);
	}, $.validator.messages.nowhitespace );

	$.validator.addMethod( 'ziprange', function( value, element ) {
		return this.optional(element) || /^90[2-5]\d\{2\}-\d{4}$/.test(value);
	}, $.validator.messages.ziprange );

	$.validator.addMethod( 'zipcodeUS', function( value, element ) {
		return this.optional(element) || /\d{5}-\d{4}$|^\d{5}$/.test(value);
	}, $.validator.messages.zipcodeUS );

	$.validator.addMethod( 'integer', function( value, element ) {
		return this.optional(element) || /^-?\d+$/.test(value);
	}, $.validator.messages.ziprange );
});

/*
 * jQuery validate.password plug-in 1.0
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-validate.password/
 *
 * Copyright (c) 2009 Jörn Zaefferer
 *
 * $Id$
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function($) {

	var LOWER = /[a-z]/,
		UPPER = /[A-Z]/,
		DIGIT = /[0-9]/,
		DIGITS = /[0-9].*[0-9]/,
		SPECIAL = /[^a-zA-Z0-9]/,
		SAME = /^(.)\1+$/;

	function rating(rate, message) {
		return {
			rate: rate,
			messageKey: message
		};
	}

	function uncapitalize(str) {
		return str.substring(0, 1).toLowerCase() + str.substring(1);
	}

	$.validator.passwordRating = function(password, username) {
		if (!password || password.length < 8)
			return rating(0, "too-short");
		if (username && password.toLowerCase().match(username.toLowerCase()))
			return rating(0, "similar-to-username");
		if (SAME.test(password))
			return rating(1, "very-weak");

		var lower = LOWER.test(password),
			upper = UPPER.test(uncapitalize(password)),
			digit = DIGIT.test(password),
			digits = DIGITS.test(password),
			special = SPECIAL.test(password);

		if (lower && upper && digit || lower && digits || upper && digits || special)
			return rating(4, "strong");
		if (lower && upper || lower && digit || upper && digit)
			return rating(3, "good");
		return rating(2, "weak");
	};

	$.validator.passwordRating.messages = {
		"similar-to-username": "Too similar to username",
		"too-short": "Too short",
		"very-weak": "Very weak",
		"weak": "Weak",
		"good": "Good",
		"strong": "Strong"
	};

	$.validator.addMethod("password", function(value, element, usernameField) {


		// use untrimmed value
		var password = element.value,
		// get username for comparison, if specified
			username = $(typeof usernameField != "boolean" ? usernameField : []);

		var rating = $.validator.passwordRating(password, username.val());
		// update message for this field

		var meter = $(".password-meter", element.form);

		meter.removeClass( 'similar-to-username too-short very-weak weak good strong' ).addClass( rating.messageKey ).text( $.validator.passwordRating.messages[rating.messageKey] );

		// Return true if optional and clear the password strength bar
		if ( this.optional( element ) ) {
			meter.removeClass( 'similar-to-username too-short very-weak weak good strong' ).text( 'Password Strength' );
			return true;
		}

		return rating.rate > 2;
	}, "&nbsp;");

	// manually add class rule, to make username param optional
	$.validator.classRuleSettings.password = { password: true };

})(jQuery);