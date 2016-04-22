//STYLING.STANDARD VIEW

//load
jQuery(function(){
	//functions
	var fonts = [
		'fontFamily'
	];
	populate_fonts(fonts);
	//set data
	set_main_styles();	
	find_toggle_elements();
	find_small_toggle_elements();
	find_image_toggle();
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
});

//set dropdown standard styles
function set_main_styles(){
	
	//page variables	
	var ds_width_type = global_menu_obj.dropdown_styles[0].widthType;
	var ds_width = global_menu_obj.dropdown_styles[0].width;
	
	var ds_padding = global_menu_obj.dropdown_styles[0].padding;
	
	var ds_border = global_menu_obj.dropdown_styles[0].border;
	var ds_border_color = global_menu_obj.dropdown_styles[0].borderColor;
	var ds_border_transparency = global_menu_obj.dropdown_styles[0].borderTransparency;
	var ds_border_type = global_menu_obj.dropdown_styles[0].borderType;
	var ds_border_radius = global_menu_obj.dropdown_styles[0].borderRadius;
	
	var ds_shadow = global_menu_obj.dropdown_styles[0].shadow;
	var ds_shadow_radius = global_menu_obj.dropdown_styles[0].shadowRadius;
	var ds_shadow_color = global_menu_obj.dropdown_styles[0].shadowColor;
	var ds_shadow_transparency = global_menu_obj.dropdown_styles[0].shadowTransparency;
	
	var ds_drop_start_color = global_menu_obj.dropdown_styles[0].bgDropStartColor;
	var ds_drop_gradient = global_menu_obj.dropdown_styles[0].bgDropGradient;
	var ds_drop_end_color = global_menu_obj.dropdown_styles[0].bgDropEndColor;
	var ds_drop_gradient_path = global_menu_obj.dropdown_styles[0].bgDropGradientPath;
	var ds_drop_transparency = global_menu_obj.dropdown_styles[0].bgDropTransparency;
	
	var ds_hover_start_color = global_menu_obj.dropdown_styles[0].bgHoverStartColor;
	var ds_hover_gradient = global_menu_obj.dropdown_styles[0].bgHoverGradient;
	var ds_hover_end_color = global_menu_obj.dropdown_styles[0].bgHoverEndColor;
	var ds_hover_gradient_path = global_menu_obj.dropdown_styles[0].bgHoverGradientPath;
	var ds_hover_transparency = global_menu_obj.dropdown_styles[0].bgHoverTransparency;
	
	var ds_arrows = global_menu_obj.dropdown_styles[0].arrows;
	var ds_arrows_transparency = global_menu_obj.dropdown_styles[0].arrowTransparency;
	var ds_arrow_color = global_menu_obj.dropdown_styles[0].arrowColor;
	
	var ds_devider = global_menu_obj.dropdown_styles[0].devider;
	var ds_devider_transparency = global_menu_obj.dropdown_styles[0].deviderTransparency;
	var ds_devider_color = global_menu_obj.dropdown_styles[0].deviderColor;
	
	var ds_font_family = global_menu_obj.dropdown_styles[0].fontFamily;
	var ds_font_color = global_menu_obj.dropdown_styles[0].fontColor;
	var ds_font_hover_color = global_menu_obj.dropdown_styles[0].fontHoverColor;
	var ds_font_size = global_menu_obj.dropdown_styles[0].fontSize;
	var ds_font_sizing = global_menu_obj.dropdown_styles[0].fontSizing;
	var ds_font_weight = global_menu_obj.dropdown_styles[0].fontWeight;
	var ds_font_decoration = global_menu_obj.dropdown_styles[0].fontDecoration;
	//var ds_font_hover_decoration = global_menu_obj.dropdown_styles[0].fontHoverDecoration;
	
	//BACKGROUND COLOR
	///////////////////////////////////////////////////
	
	//set drop down gradient
	if(ds_drop_gradient){
		if(jQuery('#bgDropGradient').val() == ds_drop_gradient){ 
			jQuery('#bgDropGradient').attr('checked', 'checked');
		}
	}
	
	//set drop down color
	if(ds_drop_start_color){
		jQuery('#bgDropStartColor').val(ds_drop_start_color);
	}
	
	//set drop down end color
	if(ds_drop_end_color){
		jQuery('#bgDropEndColor').val(ds_drop_end_color);
	}
	
	//set drop down gradient path
	if(ds_drop_gradient_path){
		jQuery('#bgDropGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == ds_drop_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down transparency
	if(ds_drop_transparency){
		jQuery('#bgDropTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ds_drop_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down gradient
	if(ds_hover_gradient){
		if(jQuery('#bgHoverGradient').val() == ds_hover_gradient){ 
			jQuery('#bgHoverGradient').attr('checked', 'checked');
		}
	}
	
	//set drop down color
	if(ds_hover_start_color){
		jQuery('#bgHoverStartColor').val(ds_hover_start_color);
	}
	
	//set drop down end color
	if(ds_hover_end_color){
		jQuery('#bgHoverEndColor').val(ds_hover_end_color);
	}
	
	//set drop down gradient path
	if(ds_hover_gradient_path){
		jQuery('#bgHoverGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == ds_hover_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down transparency
	if(ds_hover_transparency){
		jQuery('#bgHoverTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ds_hover_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//FONTS
	///////////////////////////////////////////////////
	
	//set font family
	if(ds_font_family){
		jQuery('#fontFamily option').each(function(index, element) {
           if(jQuery(this).val() == ds_font_family){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	update_select_component(jQuery('#fontFamily'));
	
	//set font color
	if(ds_font_color){
		jQuery('#fontColor').val(ds_font_color);
	}
	
	//set hover font color
	if(ds_font_hover_color){
		jQuery('#fontHoverColor').val(ds_font_hover_color);
	}
	
	//set font size
	if(ds_font_size){
		jQuery('#fontSize').val(ds_font_size);
	}
	
	//set font sizing
	if(ds_font_sizing){
		jQuery('#fontSizing option').each(function(index, element) {
           if(jQuery(this).val() == ds_font_sizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set font weight
	if(ds_font_weight){
		jQuery('#fontWeight option').each(function(index, element) {
           if(jQuery(this).val() == ds_font_weight){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//DROPWDOWN WIDTHS
	///////////////////////////////////////////////////
	
	//set menu dimensions
	if(ds_width_type){
		if(jQuery('#ds_width_type_custom').val() == ds_width_type){ 
			jQuery('#ds_width_type_custom').attr('checked', 'checked');
		}
		if(jQuery('#ds_width_type_widest').val() == ds_width_type){ 
			jQuery('#ds_width_type_widest').attr('checked', 'checked');
		}
	}
	
	//set custom width
	if(ds_width){
		jQuery('#width').val(ds_width);
	}
	
	//PADDING
	///////////////////////////////////////////////////
		
	//set padding
	if(ds_padding){
		var padding_array = new Array();
		padding_array = ds_padding.split(',');
		jQuery('.ds_padding').each(function(index, element) {
            jQuery(this).val(padding_array[index]);
        });
	}
	
	//SHADOW
	///////////////////////////////////////////////////
		
	//set shadow
	if(ds_shadow){
		if(jQuery('#shadow').val() == ds_shadow){ 
			jQuery('#shadow').attr('checked', 'checked');
		}
	}
	
	//set radius
	if(ds_shadow_radius){
		var shadow_array = new Array();
		shadow_array = ds_shadow_radius.split(',');
		jQuery('.ds_shadow_radius').each(function(index, element) {
            jQuery(this).val(shadow_array[index]);
        });
	}
	
	//set transparency
	if(ds_shadow_transparency){
		jQuery('#shadowTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ds_shadow_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//shadow color
	if(ds_shadow_color){
		jQuery('#shadowColor').val(ds_shadow_color);
	}
	
	//BORDER
	///////////////////////////////////////////////////
	
	//set border
	if(ds_border){
		if(jQuery('#border').val() == ds_border){ 
			jQuery('#border').attr('checked', 'checked');
		}
	}
	
	//set hover transparency
	if(ds_border_transparency){
		jQuery('#borderTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ds_border_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set hover menu color
	if(ds_border_color){
		jQuery('#borderColor').val(ds_border_color);
	}
	
	//set border type
	if(ds_border_type){
		jQuery('#borderType option').each(function(index, element) {
           if(jQuery(this).val() == ds_border_type){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set hover menu color
	if(ds_border_radius){
		var radius_array = new Array();
		radius_array = ds_border_radius.split(',');
		jQuery('.ds_border_radius').each(function(index, element) {
            jQuery(this).val(radius_array[index]);
        });
	}
	
	//ARROWS
	///////////////////////////////////////////////////
	
	//set arrows
	if(ds_arrows){
		if(jQuery('#arrows').val() == ds_arrows){ 
			jQuery('#arrows').attr('checked', 'checked');
		}
	}
	
	//arrow tranparency
	if(ds_arrows_transparency){
		jQuery('#arrowTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ds_arrows_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//arrow color
	if(ds_arrow_color){
		jQuery('#arrowColor').val(ds_arrow_color);
	}
	
	//DEVIDERS
	///////////////////////////////////////////////////
	
	//set devider
	if(ds_devider){
		if(jQuery('#devider').val() == ds_devider){ 
			jQuery('#devider').attr('checked', 'checked');
		}
	}
	
	//devider tranparency
	if(ds_devider_transparency){
		jQuery('#deviderTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ds_devider_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//arrow color
	if(ds_devider_color){
		jQuery('#deviderColor').val(ds_devider_color);
	}
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
}

//enable update settings
function enable_update_settings(){
	
	var control_width_custom = jQuery('#ds_width_type_custom');
	var control_width_widest = jQuery('#ds_width_type_widest');
	var control_width = jQuery('#width');
	
	//change: full
	jQuery(control_width_custom).on('change', function(){
		global_menu_obj.dropdown_styles[0].widthType = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: fixed
	jQuery(control_width_widest).on('change', function(){
		global_menu_obj.dropdown_styles[0].widthType = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: width
	jQuery(control_width).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].width = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	
	var control_bgDropGradient = jQuery('#bgDropGradient');
	var control_bgDropStartColor = jQuery('#bgDropStartColor');
	var control_bgDropEndColor = jQuery('#bgDropEndColor');
	var control_bgDropGradientPath = jQuery('#bgDropGradientPath');
	var control_bgDropTransparency = jQuery('#bgDropTransparency');
	
	//change: gradient bg
	jQuery(control_bgDropGradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.dropdown_styles[0].bgDropGradient = jQuery(this).val() : global_menu_obj.dropdown_styles[0].bgDropGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_bgDropStartColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].bgDropStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_bgDropEndColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].bgDropEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgDropGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgDropGradientPath).trigger('change');		
	});	
	jQuery(control_bgDropGradientPath).on('change', function(){
		global_menu_obj.dropdown_styles[0].bgDropGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgDropTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgDropTransparency).trigger('change');		
	});		
	jQuery(control_bgDropTransparency).on('change', function(){
		global_menu_obj.dropdown_styles[0].bgDropTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_bgHoverGradient = jQuery('#bgHoverGradient');
	var control_bgHoverStartColor = jQuery('#bgHoverStartColor');
	var control_bgHoverEndColor = jQuery('#bgHoverEndColor');
	var control_bgHoverGradientPath = jQuery('#bgHoverGradientPath');
	var control_bgHoverTransparency = jQuery('#bgHoverTransparency');
	
	//change: gradient bg
	jQuery(control_bgHoverGradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.dropdown_styles[0].bgHoverGradient = jQuery(this).val() : global_menu_obj.dropdown_styles[0].bgHoverGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_bgHoverStartColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].bgHoverStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_bgHoverEndColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].bgHoverEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgHoverGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgHoverGradientPath).trigger('change');		
	});	
	jQuery(control_bgHoverGradientPath).on('change', function(){
		global_menu_obj.dropdown_styles[0].bgHoverGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgHoverTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgHoverTransparency).trigger('change');		
	});		
	jQuery(control_bgHoverTransparency).on('change', function(){
		global_menu_obj.dropdown_styles[0].bgHoverTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_fontFamily = jQuery('#fontFamily');
	var control_fontWeight = jQuery('#fontWeight');
	var control_fontSize = jQuery('#fontSize');
	var control_fontSizing = jQuery('#fontSizing');
	var control_fontColor = jQuery('#fontColor');
	var control_fontHoverColor = jQuery('#fontHoverColor');
	//var control_fontHoverDecoration = jQuery('#fontHoverDecoration');
	
	//change: font family
	jQuery('.fontFamily .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontFamily).trigger('change');		
	});	
	jQuery(control_fontFamily).on('change', function(){
		global_menu_obj.dropdown_styles[0].fontFamily = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font weight
	jQuery('.fontWeight .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontWeight).trigger('change');		
	});	
	jQuery(control_fontWeight).on('change', function(){
		global_menu_obj.dropdown_styles[0].fontWeight = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font size
	jQuery(control_fontSize).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].fontSize = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font sizing
	jQuery('.fontSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontSizing).trigger('change');		
	});	
	jQuery(control_fontSizing).on('change', function(){
		global_menu_obj.dropdown_styles[0].fontSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: font color
	jQuery(control_fontColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].fontColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: hover font color
	jQuery(control_fontHoverColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].fontHoverColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: shadow radius
	jQuery('.ds_padding').on('change keyup', function(){
		var the_padding = '';
		jQuery('.ds_padding').each(function(index, element) {
			if(jQuery(this).val()){
				the_padding += jQuery(this).val() + ',';
			} else {
				the_padding += 0 + ',';
			}
        });		
		global_menu_obj.dropdown_styles[0].padding = the_padding.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
	var control_shadow = jQuery('#shadow');	
	var control_shadow_distance = jQuery('#shadow_distance');
	var control_shadow_blur = jQuery('#shadow_blur');
	var control_shadowColor = jQuery('#shadowColor');
	var control_shadowTransparency = jQuery('#shadowTransparency');
	
	//change: shadow enable
	jQuery(control_shadow).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.dropdown_styles[0].shadow = jQuery(this).val() : global_menu_obj.dropdown_styles[0].shadow = 0;
		flag_save_required('save_clicked');
	});
	
	//change: shadow radius
	jQuery('.ds_shadow_radius').on('change keyup', function(){
		var the_shadow_radius = '';
		jQuery('.ds_shadow_radius').each(function(index, element) {
			if(jQuery(this).val()){
				the_shadow_radius += jQuery(this).val() + ',';
			} else {
				the_shadow_radius += 0 + ',';
			}
        });		
		global_menu_obj.dropdown_styles[0].shadowRadius = the_shadow_radius.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
	//change: shadow color
	jQuery(control_shadowColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].shadowColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: shadow transparency
	jQuery('.shadowTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_shadowTransparency).trigger('change');		
	});	
	jQuery(control_shadowTransparency).on('change', function(){
		global_menu_obj.dropdown_styles[0].shadowTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	var control_border = jQuery('#border');
	var control_borderTransparency = jQuery('#borderTransparency');
	var control_borderColor = jQuery('#borderColor');	
	var control_borderType = jQuery('#borderType');	
	var control_border_radius_top = jQuery('#border_radius_top');
	var control_border_radius_top_right = jQuery('#border_radius_top_right');
	var control_border_radius_bottom_right = jQuery('#border_radius_bottom_right');
	var control_border_radius_bottom_left = jQuery('#border_radius_bottom_left');
	
	//change: border enable
	jQuery(control_border).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.dropdown_styles[0].border = jQuery(this).val() : global_menu_obj.dropdown_styles[0].border = 0;
		flag_save_required('save_clicked');
	});
	
	//change: border transparency
	jQuery('.borderTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_borderTransparency).trigger('change');		
	});	
	jQuery(control_borderTransparency).on('change', function(){
		global_menu_obj.dropdown_styles[0].borderTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: border color
	jQuery(control_borderColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].borderColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: border type
	jQuery('.borderType .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_borderType).trigger('change');		
	});	
	jQuery(control_borderType).on('change', function(){
		global_menu_obj.dropdown_styles[0].borderType = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: border radius
	jQuery('.ds_border_radius').on('change keyup', function(){
		var the_border_radius = '';
		jQuery('.ds_border_radius').each(function(index, element) {
			if(jQuery(this).val()){
				the_border_radius += jQuery(this).val() + ',';
			} else {
				the_border_radius += 0 + ',';
			}
        });		
		global_menu_obj.dropdown_styles[0].borderRadius = the_border_radius.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
	var control_arrows = jQuery('#arrows');
	var control_arrowTransparency = jQuery('#arrowTransparency');
	var control_arrowColor = jQuery('#arrowColor');
	
	//change: enable arrows
	jQuery(control_arrows).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.dropdown_styles[0].arrows = jQuery(this).val() : global_menu_obj.dropdown_styles[0].arrows = 0;
		flag_save_required('save_clicked');
	});
	
	//change: arrow transparency
	jQuery('.arrowTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_arrowTransparency).trigger('change');		
	});	
	jQuery(control_arrowTransparency).on('change', function(){
		global_menu_obj.dropdown_styles[0].arrowTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: sticky color arrow
	jQuery(control_arrowColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].arrowColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
	var control_devider = jQuery('#devider');
	var control_deviderTransparency = jQuery('#deviderTransparency');
	var control_deviderColor = jQuery('#deviderColor');
	
	//change: enable devider
	jQuery(control_devider).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.dropdown_styles[0].devider = jQuery(this).val() : global_menu_obj.dropdown_styles[0].devider = 0;
		flag_save_required('save_clicked');
	});
	
	//change: devider transparency
	jQuery('.deviderTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_deviderTransparency).trigger('change');		
	});	
	jQuery(control_deviderTransparency).on('change', function(){
		global_menu_obj.dropdown_styles[0].deviderTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: sticky color arrow
	jQuery(control_deviderColor).on('change keyup', function(){
		global_menu_obj.dropdown_styles[0].deviderColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
}