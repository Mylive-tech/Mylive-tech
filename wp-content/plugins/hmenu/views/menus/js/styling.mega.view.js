//STYLING.MEGA VIEW

//load
jQuery(function(){	
	//functions
	var fonts = [
		'fontFamily_0',
		'fontFamily_1',
		'fontFamily_2',
		'fontFamily_3'
	];
	populate_fonts(fonts);
	//set data
	set_mega_styles();	
	find_toggle_elements();
	find_small_toggle_elements();
	find_image_toggle();
	goto_main_nav();
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
	bind_ref_listener();
});

//mega style reference
function bind_ref_listener(){
	jQuery('.toggle_visual').off().on('click', function(){
		var div_height =  jQuery(this).attr('data-height');
		
		if(!jQuery(this).attr('data-toggle') || jQuery(this).attr('data-toggle') == 'close'){
			jQuery('#'+jQuery(this).attr('data-to-toggle')).stop().animate({
				'height': div_height
			}, 500);
			jQuery(this).attr('data-toggle', 'open');
		} else {
			jQuery(this).attr('data-toggle', 'close');
			jQuery('#'+jQuery(this).attr('data-to-toggle')).stop().animate({
				'height': 0
			}, 500);
		}
		
	});
}

//add more button
function goto_main_nav(){
	//load core view
	jQuery('#goto_main_nav').on('click', function(){	
		reload_sub_view('menu_sub_main_navicons', 'menus/','styling.main');
	});
}

//set dropdown mega styles
function set_mega_styles(){
	
	//page variables	
	var ms_width_type = global_menu_obj.mega_styles[0].widthType;
	var ms_width = global_menu_obj.mega_styles[0].width;
	
	var ms_padding = global_menu_obj.mega_styles[0].padding;
	
	var ms_border = global_menu_obj.mega_styles[0].border;
	var ms_border_color = global_menu_obj.mega_styles[0].borderColor;
	var ms_border_transparency = global_menu_obj.mega_styles[0].borderTransparency;
	var ms_border_type = global_menu_obj.mega_styles[0].borderType;
	var ms_border_radius = global_menu_obj.mega_styles[0].borderRadius;
	
	var ms_shadow = global_menu_obj.mega_styles[0].shadow;
	var ms_shadow_radius = global_menu_obj.mega_styles[0].shadowRadius;
	var ms_shadow_color = global_menu_obj.mega_styles[0].shadowColor;
	var ms_shadow_transparency = global_menu_obj.mega_styles[0].shadowTransparency;
	
	var ms_drop_start_color = global_menu_obj.mega_styles[0].bgDropStartColor;
	var ms_drop_gradient = global_menu_obj.mega_styles[0].bgDropGradient;
	var ms_drop_end_color = global_menu_obj.mega_styles[0].bgDropEndColor;
	var ms_drop_gradient_path = global_menu_obj.mega_styles[0].bgDropGradientPath;
	var ms_drop_transparency = global_menu_obj.mega_styles[0].bgDropTransparency;
	
	var ms_hover_start_color = global_menu_obj.mega_styles[0].bgHoverStartColor;
	var ms_hover_gradient = global_menu_obj.mega_styles[0].bgHoverGradient;
	var ms_hover_end_color = global_menu_obj.mega_styles[0].bgHoverEndColor;
	var ms_hover_gradient_path = global_menu_obj.mega_styles[0].bgHoverGradientPath;
	var ms_hover_transparency = global_menu_obj.mega_styles[0].bgHoverTransparency;
	
	var ms_arrows = global_menu_obj.mega_styles[0].arrows;
	var ms_arrows_transparency = global_menu_obj.mega_styles[0].arrowTransparency;
	var ms_arrow_color = global_menu_obj.mega_styles[0].arrowColor;
	
	var ms_devider = global_menu_obj.mega_styles[0].devider;
	var ms_devider_transparency = global_menu_obj.mega_styles[0].deviderTransparency;
	var ms_devider_color = global_menu_obj.mega_styles[0].deviderColor;
	
	var ms_font_hover_color = global_menu_obj.mega_styles[0].fontHoverColor;
	var ms_font_hover_decoration = global_menu_obj.mega_styles[0].fontHoverDecoration;
	
	//head
	var ms_font_family_head = global_menu_obj.mega_font_styles[0].fontFamily;
	var ms_font_color_head = global_menu_obj.mega_font_styles[0].fontColor;
	var ms_font_size_head = global_menu_obj.mega_font_styles[0].fontSize;
	var ms_font_sizing_head = global_menu_obj.mega_font_styles[0].fontSizing;
	var ms_font_weight_head = global_menu_obj.mega_font_styles[0].fontWeight;
	
	//body
	var ms_font_family_body = global_menu_obj.mega_font_styles[1].fontFamily;
	var ms_font_color_body = global_menu_obj.mega_font_styles[1].fontColor;
	var ms_font_size_body = global_menu_obj.mega_font_styles[1].fontSize;
	var ms_font_sizing_body = global_menu_obj.mega_font_styles[1].fontSizing;
	var ms_font_weight_body = global_menu_obj.mega_font_styles[1].fontWeight;
	
	//list
	var ms_font_family_list = global_menu_obj.mega_font_styles[2].fontFamily;
	var ms_font_color_list = global_menu_obj.mega_font_styles[2].fontColor;
	var ms_font_size_list = global_menu_obj.mega_font_styles[2].fontSize;
	var ms_font_sizing_list = global_menu_obj.mega_font_styles[2].fontSizing;
	var ms_font_weight_list = global_menu_obj.mega_font_styles[2].fontWeight;
	
	//descriptions
	var ms_font_family_desc = global_menu_obj.mega_font_styles[3].fontFamily;
	var ms_font_color_desc = global_menu_obj.mega_font_styles[3].fontColor;
	var ms_font_size_desc = global_menu_obj.mega_font_styles[3].fontSize;
	var ms_font_sizing_desc = global_menu_obj.mega_font_styles[3].fontSizing;
	var ms_font_weight_desc = global_menu_obj.mega_font_styles[3].fontWeight;
	
	//BACKGROUND COLOR
	///////////////////////////////////////////////////
	
	//set drop down gradient
	if(ms_drop_gradient){
		if(jQuery('#bgDropGradient').val() == ms_drop_gradient){ 
			jQuery('#bgDropGradient').attr('checked', 'checked');
		}
	}
	
	//set drop down color
	if(ms_drop_start_color){
		jQuery('#bgDropStartColor').val(ms_drop_start_color);
	}
	
	//set drop down end color
	if(ms_drop_end_color){
		jQuery('#bgDropEndColor').val(ms_drop_end_color);
	}
	
	//set drop down gradient path
	if(ms_drop_gradient_path){
		jQuery('#bgDropGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == ms_drop_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down transparency
	if(ms_drop_transparency){
		jQuery('#bgDropTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_drop_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down gradient
	if(ms_hover_gradient){
		if(jQuery('#bgHoverGradient').val() == ms_hover_gradient){ 
			jQuery('#bgHoverGradient').attr('checked', 'checked');
		}
	}
	
	//set drop down color
	if(ms_hover_start_color){
		jQuery('#bgHoverStartColor').val(ms_hover_start_color);
	}
	
	//set drop down end color
	if(ms_hover_end_color){
		jQuery('#bgHoverEndColor').val(ms_hover_end_color);
	}
	
	//set drop down gradient path
	if(ms_hover_gradient_path){
		jQuery('#bgHoverGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == ms_hover_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down transparency
	if(ms_hover_transparency){
		jQuery('#bgHoverTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_hover_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//FONTS
	///////////////////////////////////////////////////
		
	//set hover font color
	if(ms_font_hover_color){
		jQuery('#fontHoverColor').val(ms_font_hover_color);
	}	
	
	//set hover decoration
	if(ms_font_hover_decoration){
		jQuery('#fontHoverDecoration option').each(function(index, element) {
           if(jQuery(this).val() == ms_font_hover_decoration){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//FONTS - done abit differently on mega, because of the amount of font styles, im looping through each one and setting them according to the correct index
	jQuery(global_menu_obj.mega_font_styles).each(function(index, element) {
        
		//set vars
		var fam = global_menu_obj.mega_font_styles[index].fontFamily;
		var color = global_menu_obj.mega_font_styles[index].fontColor;
		var size = global_menu_obj.mega_font_styles[index].fontSize;
		var sizing = global_menu_obj.mega_font_styles[index].fontSizing;
		var weight = global_menu_obj.mega_font_styles[index].fontWeight;
		
		//set font family
		if(fam){
			jQuery('#fontFamily_'+index+' option').each(function(idx, el) {
			   if(jQuery(this).val() == fam){
				   jQuery(this).attr('selected', 'selected')
			   }
			});
		}
		
		update_select_component(jQuery('#fontFamily_'+index));
		
		//set font color
		if(color){
			jQuery('#fontColor_'+index).val(color);
		}	
		
		//set font size
		if(size){
			jQuery('#fontSize_'+index).val(size);
		}
		
		//set font sizing
		if(sizing){
			jQuery('#fontSizing_'+index+' option').each(function(index, element) {
			   if(jQuery(this).val() == sizing){
				   jQuery(this).attr('selected', 'selected')
			   }
			});
		}
		
		//set font weight
		if(weight){
			jQuery('#fontWeight_'+index+' option').each(function(index, element) {
			   if(jQuery(this).val() == weight){
				   jQuery(this).attr('selected', 'selected')
			   }
			});
		}
		
    });
	
	//DROPWDOWN WIDTHS
	///////////////////////////////////////////////////
	
	//set menu dimensions
	if(ms_width_type){
		if(jQuery('#ms_width_type_custom').val() == ms_width_type){ 
			jQuery('#ms_width_type_custom').attr('checked', 'checked');
		}
		if(jQuery('#ms_width_type_widest').val() == ms_width_type){ 
			jQuery('#ms_width_type_widest').attr('checked', 'checked');
		}
	}
	
	//set custom width
	if(ms_width){
		jQuery('#width').val(ms_width);
	}
	
	//PADDING
	///////////////////////////////////////////////////
		
	//set padding
	if(ms_padding){
		var padding_array = new Array();
		padding_array = ms_padding.split(',');
		jQuery('.ms_padding').each(function(index, element) {
            jQuery(this).val(padding_array[index]);
        });
	}
	
	//SHADOW
	///////////////////////////////////////////////////
		
	//set shadow
	if(ms_shadow){
		if(jQuery('#shadow').val() == ms_shadow){ 
			jQuery('#shadow').attr('checked', 'checked');
		}
	}
	
	//set radius
	if(ms_shadow_radius){
		var shadow_array = new Array();
		shadow_array = ms_shadow_radius.split(',');
		jQuery('.ms_shadow_radius').each(function(index, element) {
            jQuery(this).val(shadow_array[index]);
        });
	}
	
	//shadow color
	if(ms_shadow_color){
		jQuery('#shadowColor').val(ms_shadow_color);
	}
	
	//set drop down transparency
	if(ms_shadow_transparency){
		jQuery('#shadowTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_shadow_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//BORDER
	///////////////////////////////////////////////////
	
	//set border
	if(ms_border){
		if(jQuery('#border').val() == ms_border){ 
			jQuery('#border').attr('checked', 'checked');
		}
	}
	
	//set hover transparency
	if(ms_border_transparency){
		jQuery('#borderTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_border_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set hover menu color
	if(ms_border_color){
		jQuery('#borderColor').val(ms_border_color);
	}
	
	//set border type
	if(ms_border_type){
		jQuery('#borderType option').each(function(index, element) {
           if(jQuery(this).val() == ms_border_type){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set hover menu color
	if(ms_border_radius){
		var radius_array = new Array();
		radius_array = ms_border_radius.split(',');
		jQuery('.ms_border_radius').each(function(index, element) {
            jQuery(this).val(radius_array[index]);
        });
	}
	
	//ARROWS
	///////////////////////////////////////////////////
	
	//set arrows
	if(ms_arrows){
		if(jQuery('#arrows').val() == ms_arrows){ 
			jQuery('#arrows').attr('checked', 'checked');
		}
	}
	
	//arrow tranparency
	if(ms_arrows_transparency){
		jQuery('#arrowTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_arrows_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//arrow color
	if(ms_arrow_color){
		jQuery('#arrowColor').val(ms_arrow_color);
	}
	
	//DEVIDERS
	///////////////////////////////////////////////////
	
	//set devider
	if(ms_devider){
		if(jQuery('#devider').val() == ms_devider){ 
			jQuery('#devider').attr('checked', 'checked');
		}
	}
	
	//devider tranparency
	if(ms_devider_transparency){
		jQuery('#deviderTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_devider_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//arrow color
	if(ms_devider_color){
		jQuery('#deviderColor').val(ms_devider_color);
	}
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
}

//enable update settings
function enable_update_settings(){
	
	var control_width_custom = jQuery('#ms_width_type_custom');
	var control_width_widest = jQuery('#ms_width_type_widest');
	var control_width = jQuery('#width');
	
	//change: full
	jQuery(control_width_custom).on('change', function(){
		global_menu_obj.mega_styles[0].widthType = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: fixed
	jQuery(control_width_widest).on('change', function(){
		global_menu_obj.mega_styles[0].widthType = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: width
	jQuery(control_width).on('change keyup', function(){
		global_menu_obj.mega_styles[0].width = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_bgDropGradient = jQuery('#bgDropGradient');
	var control_bgDropStartColor = jQuery('#bgDropStartColor');
	var control_bgDropEndColor = jQuery('#bgDropEndColor');
	var control_bgDropGradientPath = jQuery('#bgDropGradientPath');
	var control_bgDropTransparency = jQuery('#bgDropTransparency');
	
	//change: gradient bg
	jQuery(control_bgDropGradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.mega_styles[0].bgDropGradient = jQuery(this).val() : global_menu_obj.mega_styles[0].bgDropGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_bgDropStartColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].bgDropStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_bgDropEndColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].bgDropEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgDropGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgDropGradientPath).trigger('change');		
	});	
	jQuery(control_bgDropGradientPath).on('change', function(){
		global_menu_obj.mega_styles[0].bgDropGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgDropTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgDropTransparency).trigger('change');		
	});		
	jQuery(control_bgDropTransparency).on('change', function(){
		global_menu_obj.mega_styles[0].bgDropTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_bgHoverGradient = jQuery('#bgHoverGradient');
	var control_bgHoverStartColor = jQuery('#bgHoverStartColor');
	var control_bgHoverEndColor = jQuery('#bgHoverEndColor');
	var control_bgHoverGradientPath = jQuery('#bgHoverGradientPath');
	var control_bgHoverTransparency = jQuery('#bgHoverTransparency');
	
	//change: gradient bg
	jQuery(control_bgHoverGradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.mega_styles[0].bgHoverGradient = jQuery(this).val() : global_menu_obj.mega_styles[0].bgHoverGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_bgHoverStartColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].bgHoverStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_bgHoverEndColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].bgHoverEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgHoverGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgHoverGradientPath).trigger('change');		
	});	
	jQuery(control_bgHoverGradientPath).on('change', function(){
		global_menu_obj.mega_styles[0].bgHoverGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgHoverTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgHoverTransparency).trigger('change');		
	});		
	jQuery(control_bgHoverTransparency).on('change', function(){
		global_menu_obj.mega_styles[0].bgHoverTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_fontHoverColor = jQuery('#fontHoverColor');
	var control_fontHoverDecoration = jQuery('#fontHoverDecoration');
	
	//change: hover font color
	jQuery(control_fontHoverColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].fontHoverColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font hover decoration
	jQuery('.fontHoverDecoration .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontHoverDecoration).trigger('change');		
	});	
	jQuery(control_fontHoverDecoration).on('change', function(){
		global_menu_obj.mega_styles[0].fontHoverDecoration = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//FONTS - done abit differently on mega, because of the amount of font styles, im looping through each one and setting them according to the correct index
	jQuery(global_menu_obj.mega_font_styles).each(function(index, element) {
        
		//set vars
		var control_fam = jQuery('#fontFamily_'+index);
		var control_color = jQuery('#fontColor_'+index);
		var control_size = jQuery('#fontSize_'+index);
		var control_sizing = jQuery('#fontSizing_'+index);
		var control_weight = jQuery('#fontWeight_'+index);
		
		//change: font family
		jQuery('.fontFamily_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
			jQuery(control_fam).trigger('change');		
		});	
		jQuery(control_fam).on('change', function(){
			global_menu_obj.mega_font_styles[index].fontFamily = jQuery(this).children('option:selected').val();
			flag_save_required('save_clicked');
		});	
		
		//change: font weight
		jQuery('.fontWeight_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
			jQuery(control_weight).trigger('change');		
		});	
		jQuery(control_weight).on('change', function(){
			global_menu_obj.mega_font_styles[index].fontWeight = jQuery(this).children('option:selected').val();
			flag_save_required('save_clicked');
		});	
		
		//change: font size
		jQuery(control_size).on('change keyup', function(){
			global_menu_obj.mega_font_styles[index].fontSize = jQuery(this).val();
			flag_save_required('save_clicked');
		});
		
		//change: font sizing
		jQuery('.fontSizing_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
			jQuery(control_sizing).trigger('change');		
		});	
		jQuery(control_sizing).on('change', function(){
			global_menu_obj.mega_font_styles[index].fontSizing = jQuery(this).children('option:selected').val();
			flag_save_required('save_clicked');
		});
		
		//change: font color
		jQuery(control_color).on('change keyup', function(){
			global_menu_obj.mega_font_styles[index].fontColor = jQuery(this).val();
			flag_save_required('save_clicked');
		});
		
    });
	
	//change: shadow radius
	jQuery('.ms_padding').on('change keyup', function(){
		var the_padding = '';
		jQuery('.ms_padding').each(function(index, element) {
			if(jQuery(this).val()){
				the_padding += jQuery(this).val() + ',';
			} else {
				the_padding += 0 + ',';
			}
        });		
		global_menu_obj.mega_styles[0].padding = the_padding.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
	var control_shadow = jQuery('#shadow');	
	var control_shadow_distance = jQuery('#shadow_distance');
	var control_shadow_blur = jQuery('#shadow_blur');
	var control_shadowColor = jQuery('#shadowColor');
	var control_shadowTransparency = jQuery('#shadowTransparency');
	
	//change: shadow enable
	jQuery(control_shadow).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.mega_styles[0].shadow = jQuery(this).val() : global_menu_obj.mega_styles[0].shadow = 0;
		flag_save_required('save_clicked');
	});
	
	//change: shadow radius
	jQuery('.ms_shadow_radius').on('change keyup', function(){
		var the_shadow_radius = '';
		jQuery('.ms_shadow_radius').each(function(index, element) {
			if(jQuery(this).val()){
				the_shadow_radius += jQuery(this).val() + ',';
			} else {
				the_shadow_radius += 0 + ',';
			}
        });		
		global_menu_obj.mega_styles[0].shadowRadius = the_shadow_radius.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
	//change: shadow color
	jQuery(control_shadowColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].shadowColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: border transparency
	jQuery('.shadowTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_shadowTransparency).trigger('change');		
	});	
	jQuery(control_shadowTransparency).on('change', function(){
		global_menu_obj.mega_styles[0].shadowTransparency = jQuery(this).children('option:selected').val();
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
		jQuery(this).prop('checked') ? global_menu_obj.mega_styles[0].border = jQuery(this).val() : global_menu_obj.mega_styles[0].border = 0;
		flag_save_required('save_clicked');
	});
	
	//change: border transparency
	jQuery('.borderTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_borderTransparency).trigger('change');		
	});	
	jQuery(control_borderTransparency).on('change', function(){
		global_menu_obj.mega_styles[0].borderTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: border color
	jQuery(control_borderColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].borderColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: border type
	jQuery('.borderType .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_borderType).trigger('change');		
	});	
	jQuery(control_borderType).on('change', function(){
		global_menu_obj.mega_styles[0].borderType = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: border radius
	jQuery('.ms_border_radius').on('change keyup', function(){
		var the_border_radius = '';
		jQuery('.ms_border_radius').each(function(index, element) {
			if(jQuery(this).val()){
				the_border_radius += jQuery(this).val() + ',';
			} else {
				the_border_radius += 0 + ',';
			}
        });		
		global_menu_obj.mega_styles[0].borderRadius = the_border_radius.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
	var control_arrows = jQuery('#arrows');
	var control_arrowTransparency = jQuery('#arrowTransparency');
	var control_arrowColor = jQuery('#arrowColor');
	
	//change: enable arrows
	jQuery(control_arrows).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.mega_styles[0].arrows = jQuery(this).val() : global_menu_obj.mega_styles[0].arrows = 0;
		flag_save_required('save_clicked');
	});
	
	//change: arrow transparency
	jQuery('.arrowTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_arrowTransparency).trigger('change');		
	});	
	jQuery(control_arrowTransparency).on('change', function(){
		global_menu_obj.mega_styles[0].arrowTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: sticky color arrow
	jQuery(control_arrowColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].arrowColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
	var control_devider = jQuery('#devider');
	var control_deviderTransparency = jQuery('#deviderTransparency');
	var control_deviderColor = jQuery('#deviderColor');
	
	//change: enable devider
	jQuery(control_devider).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.mega_styles[0].devider = jQuery(this).val() : global_menu_obj.mega_styles[0].devider = 0;
		flag_save_required('save_clicked');
	});
	
	//change: devider transparency
	jQuery('.deviderTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_deviderTransparency).trigger('change');		
	});	
	jQuery(control_deviderTransparency).on('change', function(){
		global_menu_obj.mega_styles[0].deviderTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: sticky color arrow
	jQuery(control_deviderColor).on('change keyup', function(){
		global_menu_obj.mega_styles[0].deviderColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
}