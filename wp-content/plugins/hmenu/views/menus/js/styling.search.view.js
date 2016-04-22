//STYLING.SEARCH VIEW

//load
jQuery(function(){
	//functions
	//populate fonts
	var fonts = [
		'fontFamily'
	];
	populate_fonts(fonts);
	//set data
	set_search_styles();
	find_toggle_elements();
	find_small_toggle_elements();
	find_image_toggle();
	//set headers
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
});

//set dropdown mega styles
function set_search_styles(){
	
	//page variables	
	var s_active = global_menu_obj.main_styles[0].search;
	var s_type = global_menu_obj.search_styles[0].type;
	var s_icon = global_menu_obj.search_styles[0].icon;
	var s_label = global_menu_obj.search_styles[0].label;
	var s_iconColor = global_menu_obj.search_styles[0].iconColor;
	var s_iconHoverColor = global_menu_obj.search_styles[0].iconHoverColor;
	var s_iconSize = global_menu_obj.search_styles[0].iconSize;
	var s_animation = global_menu_obj.search_styles[0].animation;
	var s_placement = global_menu_obj.search_styles[0].placement;
	var s_padding = global_menu_obj.search_styles[0].padding;
	var s_width = global_menu_obj.search_styles[0].width;
	var s_height = global_menu_obj.search_styles[0].height;
	var s_fontFamily = global_menu_obj.search_styles[0].fontFamily;
	var s_fontColor = global_menu_obj.search_styles[0].fontColor;
	var s_fontSize = global_menu_obj.search_styles[0].fontSize;
	var s_fontSizing = global_menu_obj.search_styles[0].fontSizing;
	var s_fontWeight = global_menu_obj.search_styles[0].fontWeight;
	var s_border = global_menu_obj.search_styles[0].border;
	var s_borderColor = global_menu_obj.search_styles[0].borderColor;
	var s_borderTransparency = global_menu_obj.search_styles[0].borderTransparency;
	var s_borderRadius = global_menu_obj.search_styles[0].borderRadius;
	var s_backgroundColor = global_menu_obj.search_styles[0].backgroundColor;
	var s_placeholder = global_menu_obj.search_styles[0].placeholder;
	
	//ACTIVE
	///////////////////////////////////////////////////
	
	//set active
	if(s_active){
		if(jQuery('#search_active').val() == s_active){ 
			jQuery('#search_active').attr('checked', 'checked');
		}
	}
	
	//TYPE
	///////////////////////////////////////////////////
	
	//set type
	if(s_type){
		if(jQuery('#search_classic').val() == s_type){ 
			jQuery('#search_classic').attr('checked', 'checked');
			jQuery('#search_width').prop('disabled', false).removeClass('hmenu_disable_input');
		}
		if(jQuery('#search_slide').val() == s_type){ 
			jQuery('#search_slide').attr('checked', 'checked');
			jQuery('#search_width').prop('disabled', true).addClass('hmenu_disable_input');
		}
		if(jQuery('#search_full').val() == s_type){ 
			jQuery('#search_full').attr('checked', 'checked');
			jQuery('#search_width').prop('disabled', true).addClass('hmenu_disable_input');
		}
	}
	
	//set placeholder
	if(s_placeholder){
		jQuery('#search_placeholder').val(s_placeholder);
	}
	
	//DIMENSIONS
	///////////////////////////////////////////////////
	
	//set width
	if(s_width){
		jQuery('#search_width').val(s_width);
	}
	
	//set height
	if(s_height){
		jQuery('#search_height').val(s_height);
	}
	
	//set background color
	if(s_backgroundColor){
		jQuery('#backgroundColor').val(s_backgroundColor);
	}
	
	//FONTS
	///////////////////////////////////////////////////
	
	//set font family
	if(s_fontFamily){
		jQuery('#fontFamily option').each(function(index, element) {
           if(jQuery(this).val() == s_fontFamily){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	update_select_component(jQuery('#fontFamily'));
	
	//set font color
	if(s_fontColor){
		jQuery('#fontColor').val(s_fontColor);
	}
	
	//set font size
	if(s_fontSize){
		jQuery('#fontSize').val(s_fontSize);
	}
	
	//set font sizing
	if(s_fontSizing){
		jQuery('#fontSizing option').each(function(index, element) {
           if(jQuery(this).val() == s_fontSizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set font weight
	if(s_fontWeight){
		jQuery('#fontWeight option').each(function(index, element) {
           if(jQuery(this).val() == s_fontWeight){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set font family
	if(s_iconSize){
		jQuery('#iconSize option').each(function(index, element) {
           if(jQuery(this).val() == s_iconSize){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//PADDING
	///////////////////////////////////////////////////
		
	//set padding
	if(s_padding){
		var padding_array = new Array();
		padding_array = s_padding.split(',');
		jQuery('.padding').each(function(index, element) {
            jQuery(this).val(padding_array[index]);
        });
	}
	
	//BORDER
	///////////////////////////////////////////////////
	
	//set border
	if(s_border){
		if(jQuery('#border').val() == s_border){ 
			jQuery('#border').attr('checked', 'checked');
		}
	}
	
	//set hover transparency
	if(s_borderTransparency){
		jQuery('#borderTransparency option').each(function(index, element) {
           if(jQuery(this).val() == s_borderTransparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set hover menu color
	if(s_borderColor){
		jQuery('#borderColor').val(s_borderColor);
	}
	
	//set hover menu color
	if(s_borderRadius){
		var radius_array = new Array();
		radius_array = s_borderRadius.split(',');
		jQuery('.border_radius').each(function(index, element) {
            jQuery(this).val(radius_array[index]);
        });
	}
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
	
}

function enable_update_settings(){
	
	//controls
	var control_search = jQuery('#search_active');
	
	//change: search on/off
	jQuery(control_search).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].search = jQuery(this).val() : global_menu_obj.main_styles[0].search = 0;
		flag_save_required('save_clicked');
	});
	
	var control_placeholder = jQuery('#search_placeholder');
	
	//change: placeholder
	jQuery(control_placeholder).on('change keyup', function(){
		global_menu_obj.search_styles[0].placeholder = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_type_classic = jQuery('#search_classic');
	var control_type_slide = jQuery('#search_slide');
	var control_type_full = jQuery('#search_full');
	
	//change: classic
	jQuery(control_type_classic).on('change', function(){
		global_menu_obj.search_styles[0].type = jQuery(this).val();
		flag_save_required('save_clicked');
		jQuery('#search_width').prop('disabled', false).removeClass('hmenu_disable_input');
	});
	//change: slide
	jQuery(control_type_slide).on('change', function(){
		global_menu_obj.search_styles[0].type = jQuery(this).val();
		flag_save_required('save_clicked');
		jQuery('#search_width').prop('disabled', true).addClass('hmenu_disable_input');
	});
	//change: full
	jQuery(control_type_full).on('change', function(){
		global_menu_obj.search_styles[0].type = jQuery(this).val();
		flag_save_required('save_clicked');
		jQuery('#search_width').prop('disabled', true).addClass('hmenu_disable_input');
	});
	
	var control_width = jQuery('#search_width');
	var control_height = jQuery('#search_height');
	
	//change: width
	jQuery(control_width).on('change keyup', function(){
		global_menu_obj.search_styles[0].width = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: height
	jQuery(control_height).on('change keyup', function(){
		global_menu_obj.search_styles[0].height = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: background color
	var control_backgroundColor = jQuery('#backgroundColor');	
	jQuery(control_backgroundColor).on('change keyup', function(){
		global_menu_obj.search_styles[0].backgroundColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_fontFamily = jQuery('#fontFamily');
	var control_fontWeight = jQuery('#fontWeight');
	var control_fontSize = jQuery('#fontSize');
	var control_fontSizing = jQuery('#fontSizing');
	var control_fontColor = jQuery('#fontColor');
	
	//change: font family
	jQuery('.fontFamily .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontFamily).trigger('change');		
	});	
	jQuery(control_fontFamily).on('change', function(){
		global_menu_obj.search_styles[0].fontFamily = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font weight
	jQuery('.fontWeight .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontWeight).trigger('change');		
	});	
	jQuery(control_fontWeight).on('change', function(){
		global_menu_obj.search_styles[0].fontWeight = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font size
	jQuery(control_fontSize).on('change keyup', function(){
		global_menu_obj.search_styles[0].fontSize = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font sizing
	jQuery('.fontSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontSizing).trigger('change');		
	});	
	jQuery(control_fontSizing).on('change', function(){
		global_menu_obj.search_styles[0].fontSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: font color
	jQuery(control_fontColor).on('change keyup', function(){
		global_menu_obj.search_styles[0].fontColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_iconSize = jQuery('#iconSize');
	
	jQuery('.iconSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_iconSize).trigger('change');		
	});	
	jQuery(control_iconSize).on('change', function(){
		global_menu_obj.search_styles[0].iconSize = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: padding
	jQuery('.padding').on('change keyup', function(){
		var the_padding = '';
		jQuery('.padding').each(function(index, element) {
			if(jQuery(this).val()){
				the_padding += jQuery(this).val() + ',';
			} else {
				the_padding += 0 + ',';
			}
        });		
		global_menu_obj.search_styles[0].padding = the_padding.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
	var control_border = jQuery('#border');
	var control_borderTransparency = jQuery('#borderTransparency');
	var control_borderColor = jQuery('#borderColor');	
	var control_border_radius_top = jQuery('#border_radius_top');
	var control_border_radius_top_right = jQuery('#border_radius_top_right');
	var control_border_radius_bottom_right = jQuery('#border_radius_bottom_right');
	var control_border_radius_bottom_left = jQuery('#border_radius_bottom_left');
	
	//change: border enable
	jQuery(control_border).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.search_styles[0].border = jQuery(this).val() : global_menu_obj.search_styles[0].border = 0;
		flag_save_required('save_clicked');
	});
	
	//change: border transparency
	jQuery('.borderTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_borderTransparency).trigger('change');		
	});	
	jQuery(control_borderTransparency).on('change', function(){
		global_menu_obj.search_styles[0].borderTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: border color
	jQuery(control_borderColor).on('change keyup', function(){
		global_menu_obj.search_styles[0].borderColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: border radius
	jQuery('.border_radius').on('change keyup', function(){
		var the_border_radius = '';
		jQuery('.border_radius').each(function(index, element) {
			if(jQuery(this).val()){
				the_border_radius += jQuery(this).val() + ',';
			} else {
				the_border_radius += 0 + ',';
			}
        });		
		global_menu_obj.search_styles[0].borderRadius = the_border_radius.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
}