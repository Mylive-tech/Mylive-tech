// Javascript for adding new field
jQuery(document).ready( function() {

	/**
	 * Credits to the Advanced Custom Fields plugin for this code
	 */

	// Update Order Numbers
	function shipping_update_order_numbers(div) {
		count = parseInt(jQuery('.shipping-wccs-table').children('tbody').children('tr.shipping-wccs-row').length);

		div.children('tbody').children('tr.shipping-wccs-row').each(function(i) {

			jQuery(this).children('td.shipping-wccs-order').html(i+1);

			for ( var x = 0; x < count; x++ ) {
			jQuery(this).children('td.more_toggler1,td.shipping-wccs-order-hidden').find('[name]').each(function(){
				var shipping_name = jQuery(this).attr('name').replace('['+x+']','[' + i + ']');
				jQuery(this).attr('name', shipping_name);
			});
            
             jQuery(this).children('td.shipping-wccs-order-hidden').find('[value]').each(function(){
        		var shipping_name = jQuery(this).attr('value').replace(jQuery(this).val(), i+1);
				jQuery(this).attr('value', shipping_name);
			});
            
			}
		});
	}
	
	// Make Sortable
	function shipping_make_sortable(div){
		var shipping_fixHelper = function(e, ui) {
			ui.children().each(function() {
				jQuery(this).width(jQuery(this).width());
			});
			return ui;
		};

		div.children('tbody').unbind('sortable').sortable({
			update: function(event, ui){
				shipping_update_order_numbers(div);
			},
			handle: 'td.shipping-wccs-order',
			helper: shipping_fixHelper
		});
	}

	var shippingdiv = jQuery('.shipping-wccs-table'),
		shipping_row_count = shippingdiv.children('tbody').children('tr.shipping-wccs-row').length;

	// Make the table sortable
	shipping_make_sortable(shippingdiv);
	
	// Add button
	jQuery('#shipping-wccs-add-button').live('click', function(){

		var shippingdiv = jQuery('.shipping-wccs-table'),			
			shipping_row_count = shippingdiv.children('tbody').children('tr.shipping-wccs-row').length,
			shipping_new_field = shippingdiv.children('tbody').children('tr.shipping-wccs-clone').clone(false); // Create and add the new field

		shipping_new_field.attr( 'class', 'shipping-wccs-row' );

		// Update names
		shipping_new_field.find('[name]').each(function(){
			var shipping_count = parseInt(shipping_row_count);
			var shipping_name = jQuery(this).attr('name').replace('[999]','[' + shipping_count + ']');
			jQuery(this).attr('name', shipping_name);
		});

	  
		shipping_new_field.find('[value]').each(function(){
			var shipping_count = parseInt(shipping_row_count);
			var shipping_name = jQuery(this).attr('value').replace('999', shipping_count + 1);
			jQuery(this).attr('value', shipping_name);
		});
		

		// Add row
		shippingdiv.children('tbody').append(shipping_new_field); 
		shipping_update_order_numbers(shippingdiv);

		// There is now 1 more row
		shipping_row_count ++;

		return false;	
	});

	// Remove button
	jQuery('.shipping-wccs-table .shipping-wccs-remove-button').live('click', function(){
		var shippingdiv = jQuery('.shipping-wccs-table'),
			tr = jQuery(this).closest('tr');

		tr.animate({'left' : '50px', 'opacity' : 0}, 250, function(){
			tr.remove();
			shipping_update_order_numbers(shippingdiv);
		});

		return false;
	});
});