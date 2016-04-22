(function($) {
	$( document ).ready( function() {

		// Preview Email General Stuff
		// ----------------------------------------
		
		// Tooltips
		jQuery(".help_tip_new").tipTip({
			'attribute' : 'data-tip',
			'fadeIn' : 300,
			'fadeOut' : 300,
			'delay' : 200,
			'defaultPosition' : "top",
			'edgeOffset' : 3,
			'maxWidth' : "300px"
			//'enter' : function() {
			//	//jQuery("#tiptip_holder").addClass("cx_tip_tip");
			//	jQuery("#tiptip_holder #tiptip_content").addClass('cx_tip_tip');
			//}
			//'keepAlive' : true,
			//'activation' : 'click'
		});
		
		// WooCommerce Order Admin Page
		// ----------------------------------------
		
		// From HTML string
		jQuery('#preview-email-button').click(function(event) {
			
			var val_email_type;
			if ( jQuery("select#ec_order_action" ).val() != "") val_email_type = jQuery( "select#ec_order_action" ).val().replace("send_email_","");
			else val_email_type = "new_order";
			
			var val_email_order = jQuery( "#post_ID" ).val();
			
			var new_src = "";
			new_src += woocommerce_email_control.admin_url;
			new_src += "/admin.php?";
			new_src += "page=woocommerce_email_control";
			new_src += "&";
			new_src += "ec_email_type=" + val_email_type;
			new_src += "&";
			new_src += "ec_email_order=" + val_email_order;
			new_src += "&";
			new_src += "ec_in_popup=true";
			
			email_control_popup(new_src);

			return false;

		});
		
		function email_control_popup(src) {
			
			ec_loading({ backgroundColor: "rgba(0,0,0,0)" });
			
			jQuery.magnificPopup.open({
				items: {
					src:	src,
					type:	"iframe"
				},
				//closeBtnInside: true,
				overflowY: false,
				closeOnBgClick:	true,
				closeMarkup: '<button title="%title%" class="mfp-close button-primary"><i class="mfp-close-icn">&times;</i></button>'
			});
			
		}
		
		jQuery('select#ec_order_action').change(function() {
			
			if ( jQuery(this).val().indexOf("send_email") != -1 ) {
				jQuery('#actions').after( jQuery('#preview-email-row') );
				//jQuery('#preview-email-row').css({display:"block"});
				jQuery('#actions .button.wc-reload').fadeOut(150);
				jQuery('#preview-email-row').slideDown(150);
			}
			else {
				//jQuery('#preview-email-row').css({display:"none"});
				jQuery('#actions .button.wc-reload').fadeIn(150);
				jQuery('#preview-email-row').slideUp(150);
			}
			
		});
		
		jQuery('#send-email').click(function(event) {
			
			var val_email_type;
			if ( jQuery("select#ec_order_action" ).val() != "") val_email_type = jQuery( "select#ec_order_action" ).val().replace("send_email_","");
			else val_email_type = "new_order";
			
			var val_email_type_name;
			if ( jQuery("select#ec_order_action" ).val() != "") val_email_type_name = jQuery( "select#ec_order_action :selected" ).text().replace(/\w\S*/g, function(txt) {return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase(); } ).replace(/(\r\n|\n|\r)/gm,"");
			else val_email_type_name = "New Order";
			
			var val_email_order = jQuery( "#post_ID" ).val();
			var val_billing_email = jQuery("#_billing_email").val();
			
			
			var email_prompt = prompt("Send a " + val_email_type_name + " Email to:", val_billing_email );
			if (email_prompt != null)
				val_billing_email = email_prompt;
			else
				return;

			/*
			var conformation = confirm("Send a '" + val_email_type_name + " Email' to " + val_billing_email );
			if (conformation != true) return;
			*/
			
			ec_loading({ text: "Sending Email" });
			
			
			jQuery.ajax({
				type:		"post",
				dataType:	"json",
				url:		woocommerce_email_control.ajaxurl,
				data: {
					action:				"ec_send_email",
					ec_email_type:	val_email_type,
					ec_email_order:		val_email_order,
					ec_email_addresses:	val_billing_email
					//nonce:			nonce
				},
				success: function( data ) {
					ec_loading_end();
					ec_notify("Email Sent!", {id: "second-thing", size: "medium"});
				},
				error: function(xhr, status, error) {
					ec_loading_end();
					ec_notify("Email sending failed!", {id: "second-thing", size: "medium"});
				}
			});

			return false;
			
		});
		
		/**
		 * Preview Email Main Admin Page
		 */
		
		jQuery('#ec_email_type, #ec_email_order').change(function() {
			reload_preview();
			
			hide_settings_composer();
			show_settings_composer();
			
		});

		reload_preview();
		
		/**
		 * show or hide the Settings Composer Function
		 */
		function show_settings_composer() {
			
			ec_settings_form_show = "#ec_settings_form_" + jQuery('#ec_email_template').val();
			ec_settings_form_sub_show = "#ec_settings_form_sub_" + jQuery('#ec_email_template').val() + "_" + jQuery('#ec_email_type').val();
			ec_settings_form_sub_show_all = "#ec_settings_form_sub_" + jQuery('#ec_email_template').val() + "_all";

			jQuery(".ec_settings_form").hide();
			jQuery(".ec_settings_form_sub").hide();
			jQuery(ec_settings_form_show).show();
			jQuery(ec_settings_form_sub_show).show();
			jQuery(ec_settings_form_sub_show_all).show();

			// Hide the main editing block on change of template
			jQuery(".ec-admin-panel-edit-content").removeClass('ec_active');


			// Show the edit buttin if there are fields showing to edit
			if ( jQuery(ec_settings_form_sub_show).length || jQuery(ec_settings_form_sub_show_all).length ) {
				jQuery("#ec_edit_content_controls").removeClass('disabled');
			}
			else{
				jQuery("#ec_edit_content_controls").addClass('disabled');
			}
			
		}
		
		function hide_settings_composer() {
			
			jQuery("#ec_edit_content_controls").addClass('disabled');
			jQuery(".ec-admin-panel-edit-content").removeClass('ec_active');
			
		}
		
		show_settings_composer();
		
		// Ajax saving of fields
		jQuery("#send_test").on("click", function () {
			
			//Split up the val array
			var val_email_type			= jQuery("#ec_email_type").val();
			var val_email_type_name		= jQuery( "#ec_email_type :selected" ).text().replace(/\w\S*/g, function(txt) {return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase(); } ).replace(/(\r\n|\n|\r)/gm,"");
			var val_email_template 		= jQuery("#ec_email_template").val();
			var val_email_order			= jQuery("#ec_email_order").val();
			var val_billing_email		= jQuery('#ec_email_order:selected').attr('data-order-email');
			var val_testing_email		= jQuery("#ec_send_email").val();
			

			form_data = "";

			form_data	+= '';
			form_data	+= 'action=ec_send_email';

			form_data	+= '&';
			form_data	+= 'ec_email_type=' + val_email_type;

			form_data	+= '&';
			form_data	+= 'ec_email_template=' + val_email_template;

			form_data	+= '&';
			form_data	+= 'ec_email_order=' + val_email_order;

			form_data	+= '&';
			form_data	+= 'ec_email_addresses=' + val_testing_email;

			form_data	+= '&';
			form_data	+= jQuery(".ec_settings_form:visible").serialize();
			
			ec_loading({text: "Sending Email"});

			jQuery.ajax({
				type:		"post",
				dataType:	"json",
				url:		woocommerce_email_control.ajaxurl,
				data:		form_data,
				success: function( data ) {
					
					console.log( data );
					
					ec_loading_end();
					ec_notify("Email Sent!", {id: "second-thing", size: "medium"});
				},
				error: function(xhr, status, error) {
					ec_loading_end();
					ec_notify("Email sending failed!", {id: "second-thing", size: "medium"});
				}
			});

			return false;
		});
		
		// Returns a function, that, as long as it continues to be invoked, will not
		// be triggered. The function will be called after it stops being called for
		// N milliseconds. If `immediate` is passed, trigger the function on the
		// leading edge, instead of the trailing.
		function debounce(func, wait, immediate) {
			var timeout;
			return function() {
				var context = this, args = arguments;
				var later = function() {
					timeout = null;
					if (!immediate) func.apply(context, args);
				};
				var callNow = immediate && !timeout;
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
				if (callNow) func.apply(context, args);
			};
		}

		
		var update_preview = debounce(function() {

			iframe_src = jQuery('#preview-email-template-iframe').attr("src");
			submit_form = jQuery(this).closest("form");

			//console.log( submit_form.attr('class') );

			submit_form.attr("action", iframe_src);
			submit_form.attr("target", "my-iframe");
			submit_form.attr("method", "post");

			submit_form.submit();

			set_edited(this);

			return false;
			
		}, 300);
		
		jQuery('.ec_settings_form .main-controls-element input, .ec_settings_form .main-controls-element textarea').keyup( update_preview );
		jQuery('.ec_settings_form .main-controls-element select').change( update_preview );
		
		var edited = false;
		function set_edited(element) {
			
			jQuery(element).closest("form").find("#save_edit_settings")
			.attr('disabled',false)
			.attr('value', 'Save & Publish');

			edited = true;
		}
		function clear_edited(element) {
			
			jQuery(element).closest("form").find("#save_edit_settings")
			.attr('disabled',true)
			.attr('value', 'Saved');
			
			edited = false;
		}
		
		// Ajax Save Edit Settings
		jQuery(".save_edit_settings").click( function (event) {

			if (edited) {
				var confirm_result = confirm("Are you sure you want save these changes");
				if (!confirm_result)
					return;
				
				element		= this;
				form		= jQuery(this).closest("form");
				form_data	= form.serialize();
				form_data	= "action=save_edit_email&" + form_data;

				jQuery.ajax({
					type:		"post",
					url:		woocommerce_email_control.ajaxurl,
					data:		form_data,
					success:	function( data ) {
						
						clear_edited(element);
						//reload_preview();

					},
					error:		function(xhr, status, error) {
						console.log(xhr, status, error);
					}
				});
				
			}
			
			return false;
		});
		
		// Breifly Hide the Edit Window
		jQuery(".hide_settings").hover(
			function (event) {
				//jQuery(".ec-admin-panel-edit-content").animate({opacity:0}, 100);
				jQuery(".ec-admin-panel-edit-content").addClass('ec_force_hide');
			},
			function (event) {
				//jQuery(".ec-admin-panel-edit-content").animate({opacity:1}, 100);
				jQuery(".ec-admin-panel-edit-content").removeClass('ec_force_hide');
			}
		);
		
		jQuery("#close_edit_settings").click( function(event) {
			
			if (edited) {
				var confirm_result = confirm("Are you sure you want to close without saving");
				
				if (!confirm_result)
					return;
					
				//reload_preview();
				
				//clear_edited(this);
			}
			
			jQuery('.ec-admin-panel-edit-content').removeClass('ec_active');

			window.location.hash = '';

			return false;
		});
		
		// Show Send button only when somone types in the field
		jQuery('#ec_send_email').keyup(function() {
			
			if ( !jQuery.trim( jQuery("#ec_send_email").val() ) ) {
				//close
				jQuery('#send_test').fadeOut()
				.parent(".main-controls-element").removeClass("element-open");
			}
			else{
				//open
				jQuery('#send_test').fadeIn()
				.parent(".main-controls-element").addClass("element-open");
				
				jQuery("#preview-email-template-iframe")[0].contentWindow.ec_set_to_email( jQuery("#ec_send_email").val() );
			}
			
		});
		
		if ( jQuery('#ec_send_email').val() != "" ) {
			
			jQuery('#send_test').fadeIn();
			
		}
		
		// Ajax saving of fields
		jQuery(".header_info_userspecifc").on("change", function () {
			
			//jQuery("#preview-email-template-iframe")[0].contentWindow.ec_toggle_header_info();
			
			ec_toggle_header_info();
			
			var field_name	= jQuery(this).attr("name");
			var field_value	= jQuery(this).val();
			
			if ( jQuery(this).attr('type') == "checkbox" ) {
				if ( jQuery( "input[name='" + jQuery(this).attr('name') + "']" ).length == 1 ) {
					
					if ( jQuery(this).is(":checked") ) field_value = "on";
					else field_value = "off";
										
				}
			}

			jQuery.ajax({
				type:		"post",
				dataType:	"json",
				url:		woocommerce_email_control.ajaxurl,
				data: {
					action:			"save_meta",
					field_name:		field_name,
					field_value:	field_value
					//nonce:		nonce
				},
				success: function( data ) {
					
				},
				error: function(xhr, status, error) {
					
				}
			});
		});
		
		// Ajax saving of options
		function save_option ( option_name, option_value, complete ) {
			
			jQuery.ajax({
				type:		"post",
				url:		woocommerce_email_control.ajaxurl,
				data: {
					action:			"save_option",
					field_name:		option_name,
					field_value:	option_value
					//nonce:		nonce
				},
				success: function( data ) {
					
					if (typeof complete !== 'undefined') complete();
					
				},
				error: function(xhr, status, error) {
					console.log(xhr, status, error);
				}
			});

		}
		
		jQuery("#ec_edit_email").on('click', function(event) {
			
			return false;
			
		});
		
		// Preview Email Template Selector
		// ----------------------------------------
		
		jQuery('#ec_email_template').on("change", function () {
			
			jQuery('#template-commit').css({display:"block"});
			jQuery('#ec_email_template_preview').val( jQuery(this).val() );
			
			reload_preview();
			
			hide_settings_composer();
			
			return false;
			
		});
		
		
		jQuery('#ec_save_email_template').on("click", function () {
			
			var confirm_result = confirm("Are you sure you want to use this template for all future emails sent from your site");
			if (confirm_result) {
				jQuery('#template-commit').css({display:"none"});

				save_option ( "ec_template", jQuery('#ec_email_template_preview').val() /*, function() { reload_preview(); }*/ );

				jQuery('#ec_email_template_active').val( jQuery('#ec_email_template').val() );
				jQuery('#ec_email_template_preview').val("");
				
				show_settings_composer();
			}
			else{
				jQuery('#ec_cancel_email_template').click();
			}

			return false;
		});
		
		jQuery('#ec_cancel_email_template').on("click", function () {
			
			jQuery('#template-commit').css({display:"none"});
			
			jQuery('#ec_email_template').val( jQuery('#ec_email_template_active').val() );
			jQuery('#ec_email_template_preview').val("");
			
			reload_preview();
			
			show_settings_composer();
			
			return false;
			
		});
		
		// Handle Default re-populating
		jQuery('.reset-to-default').on("click", function () {

			jQuery(this).closest(".main-controls-element").find("input, textarea")
			.val( jQuery(this).attr("data-default") )
			.keyup();

			return false;
			
		});
		
		//Initialise Color Pickers
		jQuery('.ec-colorpick').iris({
			change: function(a, b) {
				jQuery(this).css({ backgroundColor: b.color.toString() }).keyup();
			},
			hide: !0,
			border: !0,
		})
		.each(function() {
			jQuery(this).css({ backgroundColor: jQuery(this).val() });
		})
		.click(function() {
			jQuery('.iris-picker').hide();
			jQuery(this).parents('.main-controls-element').find('.iris-picker').show();
		});

		jQuery('body').click(function() {
			jQuery('.iris-picker').hide();
		});

		jQuery('.ec-colorpick').click(function(a) {
			a.stopPropagation();
		});

		
		// Preview Email Upload Image
		// ----------------------------------------
		var custom_uploader;
		jQuery('.upload_image_button').click(function(event) {
			
			this_button	= jQuery(this);
			this_field	= this_button.parent().find('.upload_image');

			event.preventDefault();

			//If the uploader object has already been created, reopen the dialog
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}

			//Extend the wp.media object
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});

			//When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();
				console.log( attachment.url, this_field );
				this_field.val( attachment.url );
				
				this_field.keyup();
				set_edited(this);
				
			});

			//Open the uploader dialog
			custom_uploader.open();

			return false;

		});
		
		// Preview Email Edit Content
		// ----------------------------------------
		jQuery('#ec_edit_content').on("click", function () {
			
			jQuery(".ec-admin-panel-edit-content").addClass('ec_active');

			window.location.hash = 'customize';
			
			return false;
			
		});
		
		// Open all links in the preview in a new tab.
		jQuery('#ec-template .main-content a').attr( 'target', 'wc_email_customizer_window' );
		
		// Dismiss compatability warning in email template.
		jQuery("#ec_approve_preview_button").on('click', function(event) {
			
			var email_id = jQuery(this).data('approve-preview');
			jQuery( parent.document ).find( "#ec_approve_preview" ).val(email_id);
			parent.reload_preview();
			return false;
		});
		
		// Preview Email Preview Template
		// ----------------------------------------
		
		// Open-Close the header info
		jQuery(".hide-icon").on('click', function(event) {

			//jQuery(".header_info_userspecifc").click();
			jQuery( parent.document ).find( ".header_info_userspecifc" ).click();
			
			return false;
		});
		
		function ec_notify(content, options) {
			
			// set up default options
			var defaults = {
				id:				false,
				display_time:	5000,
				size:			"small"
			};
			options = jQuery.extend({}, defaults, options);
			
			
			if ( !jQuery("#cx-notification-holder").length )
				jQuery("body").append( '<div id="cx-notification-holder"></div>' );
			
			var current_element = jQuery(".cx-notification-" + options.id );
			
			if ( current_element.length ) {
				current_element.animate({ "margin-top": - current_element.outerHeight(true) +"px", "top": (current_element.outerHeight(true) / 1.5 ) +"px", opacity: 0 }, { duration:300, complete: function() {
					current_element.remove();
				}});
			}
			
			var new_element = jQuery('<div/>', {
				style: 'display:none;',
				class: "cx-notification cx-notification-" + options.id,
				text: content
			});
			
			jQuery("#cx-notification-holder").append(new_element);
			
			new_element.addClass('cx-notification-' + options.size );
			
			new_element.css({ "top": (new_element.outerHeight(true) / 1.5 ) + "px", opacity:0, marginLeft: - (new_element.outerWidth(true) /2 ) });
			new_element.animate({"top": "0px", opacity:1, display:"block" }, 300);
						
			
			var element_timeout = setTimeout(function() {
				
				new_element.animate({ "margin-top": - new_element.outerHeight(true) +"px", "top": (new_element.outerHeight(true) / 1.5 ) +"px", opacity: 0 }, { duration:300, complete: function() {
					new_element.remove();
				}});
				
			}, options.display_time);
			
		}
		
		// Loading Testing
		if (false) {
			time_interval = 3000;
			setTimeout(function() { /* ec_loading(); */ }, 0 * time_interval);
			setTimeout(function() { /* ec_loading( { text: "Loadski!..." } ); */ }, 1 * time_interval);
			setTimeout(function() { /* ec_loading_end(); */ }, 2 * time_interval);
			
			time_interval = 300;
			setTimeout(function() { /* ec_notify("First thing done!", {id: "first-thing"}); */ }, 0 * time_interval);
			setTimeout(function() { /* ec_notify("Second thing done!", {id: "second-thing", size: "large"}); */ }, 1 * time_interval);
			setTimeout(function() { /* ec_notify("First thing done again!", {id: "first-thing"}); */ }, 2 * time_interval);
			setTimeout(function() { /* ec_notify("Third thing done!", {id: "third-thing"}); */ }, 3 * time_interval);
			setTimeout(function() { /* ec_notify("Fourth thing done!", {id: "fourth-thing", display_time:10000}); */ }, 4 * time_interval);
			setTimeout(function() { /* ec_notify("Fifth thing done!", {id: "fifth-thing", size: "medium"} ); */ }, 5 * time_interval);
			setTimeout(function() { /* ec_notify("Third thing done again!", {id: "third-thing"} ); */ }, 6 * time_interval);
			setTimeout(function() { /* ec_notify("Sixth thing done!", {id: "sixth-thing"} ); */ }, 7 * time_interval);
		}
		
		//Close all the panels to start
		jQuery('.section-inner').slideUp();
		
		//Accordion
		jQuery('.section h3').click(function() {
			
			section = jQuery(this).parent('.section');
			section_inner = jQuery(this).parent('.section').find('.section-inner');
			section_holder = jQuery(this).parent('.section').parent('.ec_settings_form_sub');

			jQuery('.section-inner').not(section_inner).slideUp();
			jQuery('.section').not(section).removeClass('ec-active');
			//document.location.hash = 'customize';

			if ( section.hasClass('ec-active') ) {
				
				section.removeClass('ec-active');
				section_inner.slideUp();

				//document.location.hash = 'customize';
			}
			else{

				section.addClass('ec-active');
				section_inner.slideDown();

				//document.location.hash = 'customize/' + section_holder.attr('id');
			}
						
		});
		
		if ( document.location.hash ) {

			location_array = document.location.hash.split('/');

			if ( location_array[0] == '#customize' ) {
				jQuery('#ec_edit_content').click();
			}
		}
	});
	
	jQuery( window ).load( function() {
		
		if ( jQuery("#ec-template").length ) {
			parent.ec_resize_frames();
		}
		
	});
	
})( jQuery );


function reload_preview() {

	var val_email_type = jQuery("#ec_email_type").val();
	var val_email_template = jQuery("#ec_email_template").val();
	var val_email_order = jQuery("#ec_email_order").val();
	var val_billing_email = jQuery('#ec_email_order option:selected').attr('data-order-email');
	var val_email_template_preview = jQuery("#ec_email_template_preview").val();
	var val_approve_preview = jQuery("#ec_approve_preview").val();

	// Reload the Preview src
	var new_src = "";
	new_src += woocommerce_email_control.admin_url;
	new_src += "/admin.php?";
	new_src += "page=woocommerce_email_control";
	new_src += "&";
	new_src += "ec_render_email=true";
	new_src += "&";
	new_src += "ec_email_template=" + val_email_template;
	new_src += "&";
	new_src += "ec_email_type=" + val_email_type;
	new_src += "&";
	new_src += "ec_email_order=" + val_email_order;

	new_src += "&";
	new_src += "ec_approve_preview=" + val_approve_preview;

	if ( val_email_template_preview ) {
		new_src += "&";
		new_src += "ec_email_template_preview=" + val_email_template_preview;
	}

	if ( jQuery(".pe-in-popup").length ) {
		new_src += "&";
		new_src += "ec_in_popup=true";
	}

	jQuery('#preview-email-template-iframe').attr("src", new_src );



	// Set the Send test Input to Order Email
	// ----------------------------------------
	jQuery("#ec_send_email").val( val_billing_email );


}


function ec_resize_frames() {
	
	if ( jQuery(".pe-in-popup").length ) {

		//jQuery(".main-content").find("iframe").css({ "height" : jQuery(window).height() });
		
		//jQuery( "#preview-email-template-iframe").contents().find("html, body" ).css({ "margin":"0", "padding":"0", "float":"left", "width":"100%" });
		
		jQuery("#preview-email-template-iframe").contents().find( "table" ).first().attr('height','');
		
		//jQuery('body').children("div, span, table").not('.main-content').first().css({padding:"40px"});
		
		jQuery( "#preview-email-template-iframe" ).css({
			"height": jQuery("#preview-email-template-iframe").contents().find(".email-template-preview").outerHeight()
			//"height": jQuery( 'body').outerHeight()
		});
		
		jQuery( parent.document ).find('.mfp-content iframe, .mfp-content').css({
			"height": jQuery(".ec-admin-page").outerHeight(),
			//"width": jQuery( "body" ).outerWidth()
		});
		
		// End the Loading Spinner
		parent.parent.ec_loading_end();

		// Show the Popup
		jQuery( parent.parent.document ).find('.mfp-content').addClass('mfp-show');
	}

	if ( jQuery(".pe-in-admin-page").length ) {

		//jQuery(".main-content").find("iframe").css({ "height" : jQuery(window).height() });

		//jQuery( "#preview-email-template-iframe").contents().find("html, body" ).css({ "margin":"0", "padding":"0", "float":"left", "width":"100%" });

		jQuery( "#preview-email-template-iframe").contents().find(".ec-admin-page table" ).css({ "box-shadow":"0 0 0 10px #F00 inset" });

		jQuery("#preview-email-template-iframe").contents().find( "table" ).first().attr('height','');
		
		//jQuery('body').children("div, span, table").not('.main-content').first().css({padding:"40px"});
		
		new_height = jQuery("#preview-email-template-iframe").contents().find(".email-template-preview").outerHeight();
		if (new_height < jQuery(window).height() ) {
			//Smaller
			new_height = jQuery(window).height();
		}
		
		if ( jQuery( "#preview-email-template-iframe" ).height() < jQuery(window).height() ) {
			//Bigger
			jQuery( "#preview-email-template-iframe" ).css({ height: jQuery(window).height() });
		}

		jQuery( "#preview-email-template-iframe" ).css({
			"height": new_height
			//"height": jQuery( 'body').outerHeight()
		});
		
		jQuery("#preview-email-template-iframe").contents().find( "table" ).first().attr('height','100%');
	}
}


function ec_loading(options) {
			
	// set up default options
	var defaults = {
		id:      false,
		text: "Loading...",
		backgroundColor: "rgba(0,0,0,.3)"
		
	};
	options = jQuery.extend({}, defaults, options);
	
	if ( !jQuery(".cx-loading-holder").length ) {
		jQuery("body").append('<div class="cx-loading-holder" style="display: none; background-color:' + options.backgroundColor + '; "><div class="cx-loading-inner-holder"><div class="cx-loading-graphic"></div><div class="cx-loading-text"></div></div></div>' );
	}
	
	jQuery(".cx-loading-text").append( options.content );
	jQuery(".cx-loading-holder").fadeIn(300);
}


function ec_loading_end() {
	
	jQuery(".cx-loading-holder").fadeOut(300, function() {
		jQuery(this).remove();
	});
}


function ec_toggle_header_info() {
	
	var duration = 300;
	if ( jQuery(".pe-in-popup").length ) duration = 0;
	
	if ( jQuery("#preview-email-template-iframe").contents().find(".header-info").is(":visible") ) {
		jQuery("#preview-email-template-iframe").contents().find(".header-info").slideUp({
			duration: duration,
			complete:function() { ec_resize_frames(); }
		});
		jQuery("#preview-email-template-iframe").contents().find(".hide-icon.hide-up").fadeOut(50);
		jQuery("#preview-email-template-iframe").contents().find(".hide-icon.hide-down").fadeIn(50);
	}
	else {
		jQuery("#preview-email-template-iframe").contents().find(".header-info").slideDown({
			duration: duration,
			complete:function() { ec_resize_frames(); }
		});
		jQuery("#preview-email-template-iframe").contents().find(".hide-icon.hide-up").fadeIn(50);
		jQuery("#preview-email-template-iframe").contents().find(".hide-icon.hide-down").fadeOut(50);
	}
}


function ec_set_to_email(address) {
	
	jQuery(".header-info-meta-block-to-email .meta-value").html( address );
}


jQuery.extend(jQuery.easing,{
	peEaseInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	}
});


