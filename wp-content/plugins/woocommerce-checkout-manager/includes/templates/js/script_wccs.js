jQuery(document).ready(function() {

	jQuery(function () {

		jQuery(".show_hide2").click(function() {
			jQuery('.widefat th div span',this).toggleClass('current_opener');
			jQuery(this).next().toggle();
			if( jQuery('.slidingDiv2').length > 1) {
				jQuery('.slidingDiv2 :vissible').hide();
				jQuery(this).next().show();
			}
		});

	});

});

jQuery(document).ready(function(){

	jQuery(".hide_stuff_change_tog").click(function(){
		jQuery(".hide_stuff_change_tog span").toggleClass('current_opener');
		jQuery(".hide_stuff_change").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery("a.nav-tab.general-tab").click(function(){
		jQuery("a.nav-tab.billing-tab,a.nav-tab.shipping-tab,a.nav-tab.additional-tab").removeClass('nav-tab-active');
		jQuery(".address_fields_class,.checkout_notice_class,.custom_css_class,.order_notes_class,.switches_class,.advanced_class").removeClass('current');  
		jQuery(this).addClass('nav-tab-active');
		jQuery(".upload_class").addClass('current');
		jQuery("#content-nav-right").addClass('general-vibe');
		jQuery(".save-billing, .save-shipping, .billing-semi,.shipping-semi,.additional-semi,.colorpicker-semi").hide();
		jQuery("#content-nav-right").width( '76%' );
		jQuery(".save-additional, #main-nav-left,.upload_files").show();
	});

	jQuery("a.nav-tab.billing-tab").click(function(){
		jQuery("a.nav-tab.general-tab,a.nav-tab.shipping-tab,a.nav-tab.additional-tab").removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active');
		jQuery("#content-nav-right").removeClass('general-vibe');
		jQuery(".save-additional, .save-shipping, .general-semi,.shipping-semi,.additional-semi,#main-nav-left,.upload_files,.colorpicker-semi").hide();
		jQuery(".save-billing, .billing-semi").show();
	});

	jQuery("a.nav-tab.shipping-tab").click(function(){
		jQuery("a.nav-tab.general-tab,a.nav-tab.billing-tab,a.nav-tab.additional-tab").removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active');
		jQuery("#content-nav-right").removeClass('general-vibe');
		jQuery(".save-additional, .save-billing, .general-semi,.billing-semi,.additional-semi,#main-nav-left,.upload_files,.colorpicker-semi").hide();
		jQuery(".save-shipping, .shipping-semi").show();
	});

	jQuery("a.nav-tab.additional-tab").click(function(){
		jQuery("a.nav-tab.general-tab,a.nav-tab.billing-tab,a.nav-tab.shipping-tab").removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active');
		jQuery("#content-nav-right").removeClass('general-vibe');
		jQuery(".save-billing, .save-shipping, .general-semi,.billing-semi,.shipping-semi,.upload_files,#main-nav-left").hide();
		jQuery("#content-nav-right").width( '100%' );
		jQuery(".save-additional, .additional-semi").show();
	});

	jQuery(".upload_class").click(function(){
		jQuery(".address_fields_class,.checkout_notice_class,.switches_class,.order_notes_class,.custom_css_class,.advanced_class").removeClass('current');  
		jQuery(this).addClass('current');  
		jQuery(".address_fields,.checkout_notices,.switches,.custom_css,.order_notes,.advanced").hide();  
		jQuery(".upload_files").show();    
	});

	jQuery(".address_fields_class").click(function(){
		jQuery(".checkout_notice_class,.upload_class,.switches_class,.order_notes_class,.custom_css_class,.advanced_class").removeClass('current');  
		jQuery(this).addClass('current');  
		jQuery(".checkout_notices,.switches,.upload_files,.custom_css,.order_notes,.advanced").hide();    
		jQuery(".address_fields").show();    
	});

	jQuery(".checkout_notice_class").click(function(){
		jQuery(".address_fields_class,.upload_class,.switches_class,.order_notes_class,.custom_css_class,.advanced_class").removeClass('current');  
		jQuery(this).addClass('current'); 
		jQuery(".address_fields,.upload_files,.switches,.custom_css,.order_notes,.advanced").hide();     
		jQuery(".checkout_notices").show();    
	});

	jQuery(".switches_class").click(function(){
		jQuery(".address_fields_class,.checkout_notice_class,.upload_class,.order_notes_class,.custom_css_class,.advanced_class").removeClass('current');  
		jQuery(this).addClass('current'); 
		jQuery(".address_fields,.checkout_notices,.upload_files,.custom_css,.order_notes,.advanced").hide();     
		jQuery(".switches").show();    
	});

	jQuery(".order_notes_class").click(function(){
		jQuery(".address_fields_class,.checkout_notice_class,.upload_class,.switches_class,.custom_css_class,.advanced_class").removeClass('current');  
		jQuery(this).addClass('current'); 
		jQuery(".address_fields,.checkout_notices,.upload_files,.switches,.custom_css,.advanced").hide();     
		jQuery(".order_notes").show();    
	});

	jQuery(".custom_css_class").click(function(){
		jQuery(".address_fields_class,.checkout_notice_class,.upload_class,.switches_class,.order_notes_class,.advanced_class").removeClass('current');  
		jQuery(this).addClass('current'); 
		jQuery(".address_fields,.checkout_notices,.upload_files,.switches,.order_notes,.advanced").hide();     
		jQuery(".custom_css").show();    
	});

	jQuery(".advanced_class").click(function(){
		jQuery(".address_fields_class,.checkout_notice_class,.upload_class,.switches_class,.order_notes_class,.advanced_class,.custom_css_class").removeClass('current');  
		jQuery(this).addClass('current'); 
		jQuery(".address_fields,.checkout_notices,.upload_files,.switches,.order_notes,.custom_css").hide();     
		jQuery(".advanced").show();    
	});

});

jQuery(document).ready(function(){

	jQuery("th.daoo").click(function(){
		jQuery("th.daoo").toggleClass('current_opener');
		jQuery(".hide_stuff_days").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery("th.add_amount").click(function(){
		jQuery("th.add_amount").toggleClass('current_opener');
		jQuery(".add_amount_field").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery("th.apply_tick").click(function(){
		jQuery("th.apply_tick").toggleClass('current_opener');
		jQuery(".condition_tick").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery(".more_toggler").click(function(){
		jQuery(".more_toggler span").toggleClass('current_opener');
		jQuery(".more_toggler1").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery(".filter_field_tog").click(function(){
		jQuery(".filter_field_tog span").toggleClass('current_opener');
		jQuery(".filter_field").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery(".more_toggler1a").click(function(){
		jQuery(".more_toggler1a span").toggleClass('current_opener');
		jQuery(".more_toggler1c").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery(".hide_stuff_color_tog").click(function(){
		jQuery(".hide_stuff_color_tog span").toggleClass('current_opener');
		jQuery(".hide_stuff_color").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery(".hide_stuff_tog").click(function(){
		jQuery(".hide_stuff_tog span").toggleClass('current_opener');
		jQuery(".hide_stuff_op").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery(".hide_stuff_time_tog").click(function(){
		jQuery(".hide_stuff_time_tog span").toggleClass('current_opener');
		jQuery(".hide_stuff_time").slideToggle(0);
	});

});

jQuery(document).ready(function(){

	jQuery(".hide_stuff_togcheck").click(function(){
		jQuery(".hide_stuff_togcheck span").toggleClass('current_opener');
		jQuery(".hide_stuff_opcheck").slideToggle(0);
	});

});

// Javascript for adding new field
jQuery(document).ready( function() {

	// Update Order Numbers
	function update_order_numbers(div) {

		count = parseInt(jQuery('.wccs-table').children('tbody').children('tr.wccs-row').length);

		div.children('tbody').children('tr.wccs-row').each(function(i) {
			jQuery(this).children('td.wccs-order').html(i+1);
			for ( var x = 0; x < count; x++ ) {
				jQuery(this).children('td.more_toggler1,td.wccs-order-hidden').find('[name]').each(function(){
					var name = jQuery(this).attr('name').replace('['+x+']','[' + i + ']');
					jQuery(this).attr('name', name);
				});
	            
				jQuery(this).children('td.wccs-order-hidden').find('[value]').each(function(){
					var name = jQuery(this).attr('value').replace(jQuery(this).val(), i+1);
					jQuery(this).attr('value', name);
				});  
			}
		});

	}
	
	// Make Sortable
	function make_sortable(div){

		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				jQuery(this).width(jQuery(this).width());
			});
			return ui;
		};

		div.children('tbody').unbind('sortable').sortable({
			update: function(event, ui){
				update_order_numbers(div);
			},
			handle: 'td.wccs-order',
			helper: fixHelper
		});

	}

	var div = jQuery('.wccs-table'),
	row_count = div.children('tbody').children('tr.wccs-row').length;

	// Make the table sortable
	make_sortable(div);
	
	// Add button
	jQuery('#wccs-add-button').live('click', function(){

		var div = jQuery('.wccs-table'),			
		row_count = div.children('tbody').children('tr.wccs-row').length,
		new_field = div.children('tbody').children('tr.wccs-clone').clone(false); // Create and add the new field

		new_field.attr( 'class', 'wccs-row' );

		// Update names
		new_field.find('[name]').each(function(){
			var count = parseInt(row_count);
			var name = jQuery(this).attr('name').replace('[999]','[' + count + ']');
			jQuery(this).attr('name', name);
		});

		new_field.find('[value]').each(function(){
			var count = parseInt(row_count);
			var name = jQuery(this).attr('value').replace('999', count + 1);
			jQuery(this).attr('value', name);
		});

		// Add row
		div.children('tbody').append(new_field); 
		update_order_numbers(div);

		// There is now 1 more row
		row_count ++;

		return false;

	});

	// Remove button
	jQuery('.wccs-table .wccs-remove-button').live('click', function(){

		var div = jQuery('.wccs-table');
		tr = jQuery(this).closest('tr');
		tr.animate({'left' : '50px', 'opacity' : 0}, 250, function(){
			tr.remove();
			update_order_numbers(div);
		});
		return false;

	});

});