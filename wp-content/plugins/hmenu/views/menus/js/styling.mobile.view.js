//STYLING.MOBILE VIEW

//load
jQuery(function(){
	//functions
	var fonts = [
		'fontBarFamily',
		'fontMobileFamily'
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
	var mb_bar_start_color = global_menu_obj.mobile_styles[0].bgBarStartColor;
	var mb_bar_gradient = global_menu_obj.mobile_styles[0].bgBarGradient;
	var mb_bar_end_color = global_menu_obj.mobile_styles[0].bgBarEndColor;
	var mb_bar_gradient_path = global_menu_obj.mobile_styles[0].bgBarGradientPath;
	var mb_bar_transparency = global_menu_obj.mobile_styles[0].bgBarTransparency;
	
	var mb_bar_font_family = global_menu_obj.mobile_styles[0].fontBarFamily;
	var mb_bar_font_color = global_menu_obj.mobile_styles[0].fontBarColor;
	//var mb_bar_font_hover_color = global_menu_obj.mobile_styles[0].fontBarHoverColor;
	var mb_bar_font_size = global_menu_obj.mobile_styles[0].fontBarSize;
	var mb_bar_font_sizing = global_menu_obj.mobile_styles[0].fontBarSizing;
	var mb_bar_font_weight = global_menu_obj.mobile_styles[0].fontBarWeight;
	
	var mb_menu_start_color = global_menu_obj.mobile_styles[0].bgMenuStartColor;
	var mb_menu_gradient = global_menu_obj.mobile_styles[0].bgMenuGradient;
	var mb_menu_end_color = global_menu_obj.mobile_styles[0].bgMenuEndColor;
	var mb_menu_gradient_path = global_menu_obj.mobile_styles[0].bgMenuGradientPath;
	var mb_menu_transparency = global_menu_obj.mobile_styles[0].bgMenuTransparency;
	
	var mb_menu_hover_start_color = global_menu_obj.mobile_styles[0].bgHoverStartColor;
	var mb_menu_hover_gradient = global_menu_obj.mobile_styles[0].bgHoverGradient;
	var mb_menu_hover_end_color = global_menu_obj.mobile_styles[0].bgHoverEndColor;
	var mb_menu_hover_gradient_path = global_menu_obj.mobile_styles[0].bgHoverGradientPath;
	var mb_menu_hover_transparency = global_menu_obj.mobile_styles[0].bgHoverTransparency;
	
	var mb_mobile_font_family = global_menu_obj.mobile_styles[0].fontMobileFamily;
	var mb_mobile_font_color = global_menu_obj.mobile_styles[0].fontMobileColor;
	var mb_mobile_font_size = global_menu_obj.mobile_styles[0].fontMobileSize;
	var mb_mobile_font_sizing = global_menu_obj.mobile_styles[0].fontMobileSizing;
	var mb_mobile_font_weight = global_menu_obj.mobile_styles[0].fontMobileWeight;
	var mb_mobile_font_hover_color = global_menu_obj.mobile_styles[0].fontMobileHoverColor;
	
	var mb_tablet_font_family = global_menu_obj.mobile_styles[0].fontTabletFamily;
	var mb_tablet_font_color = global_menu_obj.mobile_styles[0].fontTabletColor;
	var mb_tablet_font_size = global_menu_obj.mobile_styles[0].fontTabletSize;
	var mb_tablet_font_sizing = global_menu_obj.mobile_styles[0].fontTabletSizing;
	var mb_tablet_font_weight = global_menu_obj.mobile_styles[0].fontTabletWeight;
	var mb_tablet_font_hover_color = global_menu_obj.mobile_styles[0].fontTabletHoverColor;
	
	var mb_padding_left = global_menu_obj.mobile_styles[0].paddingLeft;
	var mb_padding_right = global_menu_obj.mobile_styles[0].paddingRight;
	
	//MAIN BAR
	///////////////////////////////////////////////////
	
	//set drop down gradient
	if(mb_bar_gradient){
		if(jQuery('#bgBarGradient').val() == mb_bar_gradient){ 
			jQuery('#bgBarGradient').attr('checked', 'checked');
		}
	}
	
	//set drop down color
	if(mb_bar_start_color){
		jQuery('#bgBarStartColor').val(mb_bar_start_color);
	}
	
	//set drop down end color
	if(mb_bar_end_color){
		jQuery('#bgBarEndColor').val(mb_bar_end_color);
	}
	
	//set drop down gradient path
	if(mb_bar_gradient_path){
		jQuery('#bgBarGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == mb_bar_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down transparency
	if(mb_bar_transparency){
		jQuery('#bgBarTransparency option').each(function(index, element) {
           if(jQuery(this).val() == mb_bar_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//FONTS
	///////////////////////////////////////////////////
	
	//set font family
	if(mb_bar_font_family){
		jQuery('#fontBarFamily option').each(function(index, element) {
           if(jQuery(this).val() == mb_bar_font_family){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	update_select_component(jQuery('#fontBarFamily'));
	
	//set font color
	if(mb_bar_font_color){
		jQuery('#fontBarColor').val(mb_bar_font_color);
	}
	
	//set font size
	if(mb_bar_font_size){
		jQuery('#fontBarSize').val(mb_bar_font_size);
	}
	
	//set font sizing
	if(mb_bar_font_sizing){
		jQuery('#fontBarSizing option').each(function(index, element) {
           if(jQuery(this).val() == mb_bar_font_sizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set font weight
	if(mb_bar_font_weight){
		jQuery('#fontBarWeight option').each(function(index, element) {
           if(jQuery(this).val() == mb_bar_font_weight){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//MOBILE MAIN MENU
	///////////////////////////////////////////////////
	
	//set drop down gradient
	if(mb_menu_gradient){
		if(jQuery('#bgMenuGradient').val() == mb_menu_gradient){ 
			jQuery('#bgMenuGradient').attr('checked', 'checked');
		}
	}
	
	//set drop down color
	if(mb_menu_start_color){
		jQuery('#bgMenuStartColor').val(mb_menu_start_color);
	}
	
	//set drop down end color
	if(mb_menu_end_color){
		jQuery('#bgMenuEndColor').val(mb_menu_end_color);
	}
	
	//set drop down gradient path
	if(mb_menu_gradient_path){
		jQuery('#bgMenuGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == mb_menu_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down transparency
	if(mb_menu_transparency){
		jQuery('#bgMenuTransparency option').each(function(index, element) {
           if(jQuery(this).val() == mb_menu_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//MOBILE MAIN MENU HOVER
	///////////////////////////////////////////////////
	
	//set drop down gradient
	if(mb_menu_hover_gradient){
		if(jQuery('#bgHoverGradient').val() == mb_menu_hover_gradient){ 
			jQuery('#bgHoverGradient').attr('checked', 'checked');
		}
	}
	
	//set drop down color
	if(mb_menu_hover_start_color){
		jQuery('#bgHoverStartColor').val(mb_menu_hover_start_color);
	}
	
	//set drop down end color
	if(mb_menu_hover_end_color){
		jQuery('#bgHoverEndColor').val(mb_menu_hover_end_color);
	}
	
	//set drop down gradient path
	if(mb_menu_hover_gradient_path){
		jQuery('#bgHoverGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == mb_menu_hover_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set drop down transparency
	if(mb_menu_hover_transparency){
		jQuery('#bgHoverTransparency option').each(function(index, element) {
           if(jQuery(this).val() == mb_menu_hover_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//MOBILE FONTS
	///////////////////////////////////////////////////
	
	//set font family
	if(mb_mobile_font_family){
		jQuery('#fontMobileFamily option').each(function(index, element) {
           if(jQuery(this).val() == mb_mobile_font_family){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	update_select_component(jQuery('#fontMobileFamily'));
	
	//set font color
	if(mb_mobile_font_color){
		jQuery('#fontMobileColor').val(mb_mobile_font_color);
	}
	
	//set font size
	if(mb_mobile_font_size){
		jQuery('#fontMobileSize').val(mb_mobile_font_size);
	}
	
	//set font sizing
	if(mb_mobile_font_sizing){
		jQuery('#fontMobileSizing option').each(function(index, element) {
           if(jQuery(this).val() == mb_mobile_font_sizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set font weight
	if(mb_mobile_font_weight){
		jQuery('#fontMobileWeight option').each(function(index, element) {
           if(jQuery(this).val() == mb_mobile_font_weight){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set hover font color
	if(mb_mobile_font_hover_color){
		jQuery('#fontMobileHoverColor').val(mb_mobile_font_hover_color);
	}
	
	//TABLET FONTS
	///////////////////////////////////////////////////
	
	//set font family
	if(mb_tablet_font_family){
		jQuery('#fontTabletFamily option').each(function(index, element) {
           if(jQuery(this).val() == mb_tablet_font_family){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	update_select_component(jQuery('#fontTabletFamily'));
	
	//set font color
	if(mb_tablet_font_color){
		jQuery('#fontTabletColor').val(mb_tablet_font_color);
	}
	
	//set font size
	if(mb_tablet_font_size){
		jQuery('#fontTabletSize').val(mb_tablet_font_size);
	}
	
	//set font sizing
	if(mb_tablet_font_sizing){
		jQuery('#fontTabletSizing option').each(function(index, element) {
           if(jQuery(this).val() == mb_tablet_font_sizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set font weight
	if(mb_tablet_font_weight){
		jQuery('#fontTabletWeight option').each(function(index, element) {
           if(jQuery(this).val() == mb_tablet_font_weight){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set hover font color
	if(mb_tablet_font_hover_color){
		jQuery('#fontTabletHoverColor').val(mb_tablet_font_hover_color);
	}
	
	//MOBILE PADDING
	///////////////////////////////////////////////////
	
	//padding
	if(mb_padding_left){
		jQuery('#paddingLeft').val(mb_padding_left);
	}	
	
	if(mb_padding_right){
		jQuery('#paddingRight').val(mb_padding_right);
	}	
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
}

//enable update settings
function enable_update_settings(){
	
	var control_bar_gradient = jQuery('#bgBarGradient');
	var control_bar_start = jQuery('#bgBarStartColor');
	var control_bar_end = jQuery('#bgBarEndColor');
	var control_bar_path = jQuery('#bgBarGradientPath');
	var control_bar_transparency = jQuery('#bgBarTransparency');
	
	//change: gradient bg
	jQuery(control_bar_gradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.mobile_styles[0].bgBarGradient = jQuery(this).val() : global_menu_obj.mobile_styles[0].bgBarGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_bar_start).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].bgBarStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_bar_end).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].bgBarEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgBarGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bar_path).trigger('change');		
	});	
	jQuery(control_bar_path).on('change', function(){
		global_menu_obj.mobile_styles[0].bgBarGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgBarTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bar_transparency).trigger('change');		
	});		
	jQuery(control_bar_transparency).on('change', function(){
		global_menu_obj.mobile_styles[0].bgBarTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_bar_font_family = jQuery('#fontBarFamily');
	var control_bar_font_weight = jQuery('#fontBarWeight');
	var control_bar_font_size = jQuery('#fontBarSize');
	var control_bar_font_sizing = jQuery('#fontBarSizing');
	var control_bar_font_color = jQuery('#fontBarColor');
	
	//change: font family
	jQuery('.fontBarFamily .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bar_font_family).trigger('change');		
	});	
	jQuery(control_bar_font_family).on('change', function(){
		global_menu_obj.mobile_styles[0].fontBarFamily = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font weight
	jQuery('.fontBarWeight .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bar_font_weight).trigger('change');		
	});	
	jQuery(control_bar_font_weight).on('change', function(){
		global_menu_obj.mobile_styles[0].fontBarWeight = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font size
	jQuery(control_bar_font_size).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].fontBarSize = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font sizing
	jQuery('.fontBarSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bar_font_sizing).trigger('change');		
	});	
	jQuery(control_bar_font_sizing).on('change', function(){
		global_menu_obj.mobile_styles[0].fontBarSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: font color
	jQuery(control_bar_font_color).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].fontBarColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_menu_gradient = jQuery('#bgMenuGradient');
	var control_menu_start = jQuery('#bgMenuStartColor');
	var control_menu_end = jQuery('#bgMenuEndColor');
	var control_menu_path = jQuery('#bgMenuGradientPath');
	var control_menu_transparency = jQuery('#bgMenuTransparency');
	
	//change: gradient bg
	jQuery(control_menu_gradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.mobile_styles[0].bgMenuGradient = jQuery(this).val() : global_menu_obj.mobile_styles[0].bgMenuGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_menu_start).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].bgMenuStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_menu_end).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].bgMenuEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgMenuGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_menu_path).trigger('change');		
	});	
	jQuery(control_menu_path).on('change', function(){
		global_menu_obj.mobile_styles[0].bgMenuGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgMenuTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_menu_transparency).trigger('change');		
	});		
	jQuery(control_menu_transparency).on('change', function(){
		global_menu_obj.mobile_styles[0].bgMenuTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_menu_hover_gradient = jQuery('#bgHoverGradient');
	var control_menu_hover_start = jQuery('#bgHoverStartColor');
	var control_menu_hover_end = jQuery('#bgHoverEndColor');
	var control_menu_hover_path = jQuery('#bgHoverGradientPath');
	var control_menu_hover_transparency = jQuery('#bgHoverTransparency');
	
	//change: gradient bg
	jQuery(control_menu_hover_gradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.mobile_styles[0].bgHoverGradient = jQuery(this).val() : global_menu_obj.mobile_styles[0].bgHoverGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_menu_hover_start).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].bgHoverStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_menu_hover_end).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].bgHoverEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgHoverGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_menu_hover_path).trigger('change');		
	});	
	jQuery(control_menu_hover_path).on('change', function(){
		global_menu_obj.mobile_styles[0].bgHoverGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgHoverTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_menu_hover_transparency).trigger('change');		
	});		
	jQuery(control_menu_hover_transparency).on('change', function(){
		global_menu_obj.mobile_styles[0].bgHoverTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_mobile_font_family = jQuery('#fontMobileFamily');
	var control_mobile_font_weight = jQuery('#fontMobileWeight');
	var control_mobile_font_size = jQuery('#fontMobileSize');
	var control_mobile_font_sizing = jQuery('#fontMobileSizing');
	var control_mobile_font_color = jQuery('#fontMobileColor');
	var control_mobile_font_hover_color = jQuery('#fontMobileHoverColor');	
	
	//change: font family
	jQuery('.fontMobileFamily .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_mobile_font_family).trigger('change');		
	});	
	jQuery(control_mobile_font_family).on('change', function(){
		global_menu_obj.mobile_styles[0].fontMobileFamily = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font weight
	jQuery('.fontMobileWeight .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_mobile_font_weight).trigger('change');		
	});	
	jQuery(control_mobile_font_weight).on('change', function(){
		global_menu_obj.mobile_styles[0].fontMobileWeight = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font size
	jQuery(control_mobile_font_size).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].fontMobileSize = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font sizing
	jQuery('.fontMobileSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_mobile_font_sizing).trigger('change');		
	});	
	jQuery(control_mobile_font_sizing).on('change', function(){
		global_menu_obj.mobile_styles[0].fontMobileSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: font color
	jQuery(control_mobile_font_color).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].fontMobileColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font hover color
	jQuery(control_mobile_font_hover_color).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].fontMobileHoverColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_tablet_font_family = jQuery('#fontTabletFamily');
	var control_tablet_font_weight = jQuery('#fontTabletWeight');
	var control_tablet_font_size = jQuery('#fontTabletSize');
	var control_tablet_font_sizing = jQuery('#fontTabletSizing');
	var control_tablet_font_color = jQuery('#fontTabletColor');
	var control_tablet_font_hover_color = jQuery('#fontTabletHoverColor');	
	
	//change: font family
	jQuery('.fontTabletFamily .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_tablet_font_family).trigger('change');		
	});	
	jQuery(control_tablet_font_family).on('change', function(){
		global_menu_obj.mobile_styles[0].fontTabletFamily = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font weight
	jQuery('.fontTabletWeight .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_tablet_font_weight).trigger('change');		
	});	
	jQuery(control_tablet_font_weight).on('change', function(){
		global_menu_obj.mobile_styles[0].fontTabletWeight = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font size
	jQuery(control_tablet_font_size).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].fontTabletSize = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font sizing
	jQuery('.fontTabletSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_tablet_font_sizing).trigger('change');		
	});	
	jQuery(control_tablet_font_sizing).on('change', function(){
		global_menu_obj.mobile_styles[0].fontTabletSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: font color
	jQuery(control_tablet_font_color).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].fontTabletColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font hover color
	jQuery(control_tablet_font_hover_color).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].fontTabletHoverColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_padding_left = jQuery('#paddingLeft');
	var control_padding_right = jQuery('#paddingRight');
	
	//change: font color
	jQuery(control_padding_left).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].paddingLeft = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font color
	jQuery(control_padding_right).on('change keyup', function(){
		global_menu_obj.mobile_styles[0].paddingRight = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
}