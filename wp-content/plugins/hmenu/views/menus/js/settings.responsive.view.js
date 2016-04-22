//SETTINGS.ADVANCED VIEW

//load
jQuery(function(){
	//functions
	set_advanced();	
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
	find_toggle_elements();
	jQuery('#siteResponsiveOne').on('blur', function(){
		check_media_inputs();
	});
	jQuery('#siteResponsiveTwo').on('blur', function(){
		check_media_inputs();
	});
	jQuery('#siteResponsiveThree').on('blur', function(){
		check_media_inputs();
	});
});

//set settings
function set_advanced(){	
	
	//page variables
	var m_responsive_label = global_menu_obj.main_styles[0].responsiveLabel;	
	var m_site_responsive = global_menu_obj.main_styles[0].siteResponsive;
	var m_site_responsive_one = global_menu_obj.main_styles[0].siteResponsiveOne;
	var m_site_responsive_two = global_menu_obj.main_styles[0].siteResponsiveTwo;
	var m_site_responsive_three = global_menu_obj.main_styles[0].siteResponsiveThree;
	
	//RESPONSIVE SETTINGS
	///////////////////////////////////////////////////
	
	//responsive label
	if(m_responsive_label){
		jQuery('#responsiveLabel').val(m_responsive_label);
	}
	
	//is site responsive
	if(m_site_responsive){
		if(jQuery('#siteResponsive').val() == m_site_responsive){ 
			jQuery('#siteResponsive').attr('checked', 'checked');
		}
	}
	
	//responsive one
	if(m_site_responsive_one){
		jQuery('#siteResponsiveOne').val(m_site_responsive_one);
	}
	
	//responsive two
	if(m_site_responsive_two){
		jQuery('#siteResponsiveTwo').val(m_site_responsive_two);
	}
	
	//responsive three
	if(m_site_responsive_three){
		jQuery('#siteResponsiveThree').val(m_site_responsive_three);
	}
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
	
}

//enable update settings
function enable_update_settings(){	
	
	//all the controls
	var control_responsiveLabel = jQuery('#responsiveLabel');
	var control_site_responsive = jQuery('#siteResponsive');
	var control_site_responsive_one = jQuery('#siteResponsiveOne');
	var control_site_responsive_two = jQuery('#siteResponsiveTwo');
	var control_site_responsive_three = jQuery('#siteResponsiveThree');
	var reset_btn = jQuery('.reset_to_defaults');
	
	//change: responsive label
	jQuery(control_responsiveLabel).on('change keyup', function(){
		global_menu_obj.main_styles[0].responsiveLabel = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	//change: site responsive YES - NO
	jQuery(control_site_responsive).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].siteResponsive = jQuery(this).val() : global_menu_obj.main_styles[0].siteResponsive = 0;
		flag_save_required('save_clicked');
	});	
	jQuery(reset_btn).on('click', function(){
		control_site_responsive_one.val(768);
		control_site_responsive_one.trigger('change');
		control_site_responsive_two.val(992);
		control_site_responsive_two.trigger('change');
		control_site_responsive_three.val(1200);
		control_site_responsive_three.trigger('change');
		flag_save_required('save_clicked');
	});
	//change: responsive site one
	jQuery(control_site_responsive_one).on('change keyup', function(){
		global_menu_obj.main_styles[0].siteResponsiveOne = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	//change: responsive site two
	jQuery(control_site_responsive_two).on('change keyup', function(){
		global_menu_obj.main_styles[0].siteResponsiveTwo = jQuery(this).val();
		flag_save_required('save_clicked');
	});
		//change: responsive site three
	jQuery(control_site_responsive_three).on('change keyup', function(){
		global_menu_obj.main_styles[0].siteResponsiveThree = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
}