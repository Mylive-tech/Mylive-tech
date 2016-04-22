//STYLING.STANDARD VIEW

//load
jQuery(function(){
	//functions
	set_main_styles();	
	find_toggle_elements();	
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
});

//set dropdown standard styles
function set_main_styles(){
	
	var ps_cart = global_menu_obj.main_styles[0].cart;
	var ps_icon_size = global_menu_obj.main_styles[0].iconProductSize;
	var ps_icon_color = global_menu_obj.main_styles[0].iconProductColor;
	var ps_icon_hover_color = global_menu_obj.main_styles[0].iconProductHoverColor;
	
	//DEVIDERS
	///////////////////////////////////////////////////
	
	//set cart
	if(ps_cart){
		if(jQuery('#cart').val() == ps_cart){ 
			jQuery('#cart').attr('checked', 'checked');
		}
	}
	
	//devider tranparency
	if(ps_icon_size){
		jQuery('#iconProductSize option').each(function(index, element) {
           if(jQuery(this).val() == ps_icon_size){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//color
	if(ps_icon_color){
		jQuery('#iconProductColor').val(ps_icon_color);
	}
	
	//hover color
	if(ps_icon_hover_color){
		jQuery('#iconProductHoverColor').val(ps_icon_hover_color);
	}
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
	
}

//enable update settings
function enable_update_settings(){	
	
	var control_cart = jQuery('#cart');
	var control_iconSize = jQuery('#iconProductSize');
	var control_iconColor = jQuery('#iconProductColor');
	var control_iconHoverColor = jQuery('#iconProductHoverColor');
	
	//change: enable cart
	jQuery(control_cart).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].cart = jQuery(this).val() : global_menu_obj.main_styles[0].cart = 0;
		flag_save_required('save_clicked');
	});
	
	//change: size
	jQuery('.iconSize .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_iconSize).trigger('change');		
	});	
	jQuery(control_iconSize).on('change', function(){
		global_menu_obj.main_styles[0].iconProductSize = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: color
	jQuery(control_iconColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].iconProductColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
	//change: hover color
	jQuery(control_iconHoverColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].iconProductHoverColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
}