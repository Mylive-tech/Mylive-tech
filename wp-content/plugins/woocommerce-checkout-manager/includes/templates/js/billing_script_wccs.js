// Javascript for adding new field
jQuery(document).ready( function() {

	/**
	 * Credits to the Advanced Custom Fields plugin for this code
	 */

	// Update Order Numbers
	function billing_update_order_numbers(div) {
		count = parseInt(jQuery('.billing-wccs-table').children('tbody').children('tr.billing-wccs-row').length);

		div.children('tbody').children('tr.billing-wccs-row').each(function(i) {

		jQuery(this).children('td.billing-wccs-order').html(i+1);

			for ( var x = 0; x < count; x++ ) {
			jQuery(this).children('td.more_toggler1,td.billing-wccs-order-hidden').find('[name]').each(function(){
				var billing_name = jQuery(this).attr('name').replace('['+x+']','[' + i + ']');
				jQuery(this).attr('name', billing_name);
			});
            
            jQuery(this).children('td.billing-wccs-order-hidden').find('[value]').each(function(){
    			var billing_name = jQuery(this).attr('value').replace(jQuery(this).val(), i+1);
				jQuery(this).attr('value', billing_name);
			});
            
			}
		});
	}
	
	// Make Sortable
	function billing_make_sortable(div){
		var billing_fixHelper = function(e, ui) {
			ui.children().each(function() {
				jQuery(this).width(jQuery(this).width());
			});
			return ui;
		};

		div.children('tbody').unbind('sortable').sortable({
			update: function(event, ui){
				billing_update_order_numbers(div);
			},
			handle: 'td.billing-wccs-order',
			helper: billing_fixHelper
		});
	}

	var billingdiv = jQuery('.billing-wccs-table'),
		billing_row_count = billingdiv.children('tbody').children('tr.billing-wccs-row').length;

	// Make the table sortable
	billing_make_sortable(billingdiv);
	
	// Add button
	jQuery('#billing-wccs-add-button').live('click', function(){

		var billingdiv = jQuery('.billing-wccs-table'),			
			billing_row_count = billingdiv.children('tbody').children('tr.billing-wccs-row').length,
			billing_new_field = billingdiv.children('tbody').children('tr.billing-wccs-clone').clone(false); // Create and add the new field

		billing_new_field.attr( 'class', 'billing-wccs-row' );

		// Update names
		billing_new_field.find('[name]').each(function(){
			var billing_count = parseInt(billing_row_count);
			var billing_name = jQuery(this).attr('name').replace('[999]','[' + billing_count + ']');
			jQuery(this).attr('name', billing_name);
		});

	  
		billing_new_field.find('[value]').each(function(){
			var billing_count = parseInt(billing_row_count);
			var billing_name = jQuery(this).attr('value').replace('999', billing_count + 1);
			jQuery(this).attr('value', billing_name);
		});
		


		// Add row
		billingdiv.children('tbody').append(billing_new_field); 
		billing_update_order_numbers(billingdiv);

		// There is now 1 more row
		billing_row_count ++;

		return false;	
	});

	// Remove button
	jQuery('.billing-wccs-table .billing-wccs-remove-button').live('click', function(){
		var billingdiv = jQuery('.billing-wccs-table'),
			tr = jQuery(this).closest('tr');

		tr.animate({'left' : '50px', 'opacity' : 0}, 250, function(){
			tr.remove();
			billing_update_order_numbers(billingdiv);
		});

		return false;
	});
});