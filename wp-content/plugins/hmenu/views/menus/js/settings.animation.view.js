//SETTINGS.INTEGRATION VIEW

//load
jQuery(function(){
	//functions
	set_settings();	
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
});

//set settings
function set_settings(){	
	
	//page variables
	var m_animation = global_menu_obj.main_styles[0].animation;
	var m_animation_duration = global_menu_obj.main_styles[0].animationDuration;
	var m_animation_trigger = global_menu_obj.main_styles[0].animationTrigger;
	var m_animation_timeout = global_menu_obj.main_styles[0].animationTimeout;
	
	//ANIMATION SETTINGS
	///////////////////////////////////////////////////
	
	//animation type
	if(m_animation){
		jQuery('#animation option').each(function(index, element) {
           if(jQuery(this).val() == m_animation){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//animation duration
	if(m_animation_duration){
		jQuery('#animationDuration').val(m_animation_duration);
	}
	
	//animation trigger
	if(m_animation_trigger){
		jQuery('#animationTrigger option').each(function(index, element) {
           if(jQuery(this).val() == m_animation_trigger){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//animation timeout
	if(m_animation_timeout){
		jQuery('#animationTimeout').val(m_animation_timeout);
	}
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
	
}

//enable update settings
function enable_update_settings(){	

	//all the controls
	var control_animation = jQuery('#animation');
	var control_animationDuration = jQuery('#animationDuration');
	var control_animationTrigger = jQuery('#animationTrigger');
	var control_animationTimeout = jQuery('#animationTimeout');		
	
	//change: animation type
	jQuery('.animation .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_animation).trigger('change');		
	});	
	jQuery(control_animation).on('change', function(){
		global_menu_obj.main_styles[0].animation = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	//change: animation duration
	jQuery(control_animationDuration).on('change keyup', function(){
		global_menu_obj.main_styles[0].animationDuration = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: animation type
	jQuery('.animationTrigger .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_animationTrigger).trigger('change');		
	});	
	jQuery(control_animationTrigger).on('change', function(){
		global_menu_obj.main_styles[0].animationTrigger = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	//change: animation duration
	jQuery(control_animationTimeout).on('change keyup', function(){
		global_menu_obj.main_styles[0].animationTimeout = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
}