//STYLING.STANDARD VIEW

//load
jQuery(function(){
	//functions
	set_advanced_styles();	
	//find_toggle_elements();	
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
});

//set dropdown standard styles
function set_advanced_styles(){
	
	var ad_custom_css = global_menu_obj.main_styles[0].customCss;
	
	//CUSTOM CSS
	///////////////////////////////////////////////////
	
	//set custom css
	if(ad_custom_css){
		jQuery('#customCss').val(ad_custom_css);
	}	
		
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
	
}

//enable update settings
function enable_update_settings(){	
	
	var control_custom_css = jQuery('#customCss');
	
	//change: color
	jQuery(control_custom_css).on('change keyup', function(){
		global_menu_obj.main_styles[0].customCss = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
}