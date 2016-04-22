//STYLING.MAIN VIEW

//load
jQuery(function(){
	//functions
	get_presets();
	//populate fonts
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

//global variables
var ms_presetSlug;

//setup set main styles form data
function set_main_styles(){
	
	//page variables	
	var m_menu_logo = global_menu_obj.main_styles[0].logo;
	var m_menu_logo_url = global_menu_obj.main_styles[0].logoUrl;
	var m_menu_logo_height = global_menu_obj.main_styles[0].logoHeight;
	
	var m_menu_mobile_logo = global_menu_obj.main_styles[0].mobileLogo;
	var m_menu_mobile_logo_url = global_menu_obj.main_styles[0].mobileLogoUrl;
	var m_menu_mobile_logo_height = global_menu_obj.main_styles[0].mobileLogoHeight;
	
	var m_menu_search = global_menu_obj.main_styles[0].search;
	var m_menu_menu = global_menu_obj.main_styles[0].menu;
	var m_menu_social = global_menu_obj.main_styles[0].social;
	var m_menu_cart = global_menu_obj.main_styles[0].cart;	
	var ms_menu_bar_dimentions = global_menu_obj.main_styles[0].menuBarDimentions;
	var ms_menu_bar_width = global_menu_obj.main_styles[0].menuBarWidth;
	var ms_menu_bar_height = global_menu_obj.main_styles[0].menuBarHeight;
	var ms_nav_bar_dimentions = global_menu_obj.main_styles[0].navBarDimentions;
	var ms_nav_bar_width = global_menu_obj.main_styles[0].navBarWidth;
	var ms_border = global_menu_obj.main_styles[0].border;
	var ms_border_color = global_menu_obj.main_styles[0].borderColor;
	var ms_border_trasparency = global_menu_obj.main_styles[0].borderTransparency;	
	var ms_border_type = global_menu_obj.main_styles[0].borderType;
	var ms_border_radius = global_menu_obj.main_styles[0].borderRadius;
	var ms_shadow = global_menu_obj.main_styles[0].shadow;
	var ms_shadow_radius = global_menu_obj.main_styles[0].shadowRadius;
	var ms_shadow_color = global_menu_obj.main_styles[0].shadowColor;
	var ms_shadow_transparency = global_menu_obj.main_styles[0].shadowTransparency;
	var ms_menu_start_color = global_menu_obj.main_styles[0].bgMenuStartColor;
	var ms_menu_gradient = global_menu_obj.main_styles[0].bgMenuGradient;
	var ms_menu_end_color = global_menu_obj.main_styles[0].bgMenuEndColor;
	var ms_menu_gradient_path = global_menu_obj.main_styles[0].bgMenuGradientPath;
	var ms_menu_transparency = global_menu_obj.main_styles[0].bgMenuTransparency;
	var ms_hover_start_color = global_menu_obj.main_styles[0].bgHoverStartColor;
	var ms_hover_type = global_menu_obj.main_styles[0].bgHoverType;
	var ms_hover_gradient = global_menu_obj.main_styles[0].bgHoverGradient;
	var ms_hover_end_color = global_menu_obj.main_styles[0].bgHoverEndColor;
	var ms_hover_gradient_path = global_menu_obj.main_styles[0].bgHoverGradientPath;
	var ms_hover_transparency = global_menu_obj.main_styles[0].bgHoverTransparency;
	var ms_padding_left = global_menu_obj.main_styles[0].paddingLeft;
	var ms_padding_right = global_menu_obj.main_styles[0].paddingRight;
	var ms_orientation = global_menu_obj.main_styles[0].orientation;
	var ms_vertical_width = global_menu_obj.main_styles[0].verticalWidth;
	var ms_animation = global_menu_obj.main_styles[0].animation;
	var ms_animation_duration = global_menu_obj.main_styles[0].animationDuration;
	var ms_animation_trigger = global_menu_obj.main_styles[0].animationTrigger;
	var ms_animation_timeout = global_menu_obj.main_styles[0].animationTimeout;
	var ms_sticky = global_menu_obj.main_styles[0].sticky;
	var ms_sticky_logo_active = global_menu_obj.main_styles[0].stickyLogoActive;
	var ms_sticky_logo_url = global_menu_obj.main_styles[0].stickyUrl;	
	var ms_sticky_activate = global_menu_obj.main_styles[0].stickyActivate;
	var ms_sticky_height = global_menu_obj.main_styles[0].stickyHeight;
	var ms_sticky_start = global_menu_obj.main_styles[0].bgStickyStart;
	var ms_sticky_end = global_menu_obj.main_styles[0].bgStickyEnd;
	var ms_sticky_transparency = global_menu_obj.main_styles[0].stickyTransparency;	
	var ms_sticky_font_color = global_menu_obj.main_styles[0].stickyFontColor;
	var ms_sticky_font_hover_color = global_menu_obj.main_styles[0].stickyFontHoverColor;
	var ms_sticky_font_size = global_menu_obj.main_styles[0].stickyFontSize;
	var ms_sticky_font_sizing = global_menu_obj.main_styles[0].stickyFontSizing;
	var ms_sticky_font_weight = global_menu_obj.main_styles[0].stickyFontWeight;
	//var ms_sticky_font_hover_decoration = global_menu_obj.main_styles[0].stickyFontHoverDecoration;	
	
	var ms_devider = global_menu_obj.main_styles[0].devider;
	var ms_devider_transparency = global_menu_obj.main_styles[0].deviderTransparency;
	var ms_devider_color = global_menu_obj.main_styles[0].deviderColor;
	var ms_devider_sizing = global_menu_obj.main_styles[0].deviderSizing;
	
	var ms_group_devider = global_menu_obj.main_styles[0].groupDevider;	
	var ms_group_transparency = global_menu_obj.main_styles[0].groupTransparency;
	var ms_group_color = global_menu_obj.main_styles[0].groupColor;
	var ms_group_sizing = global_menu_obj.main_styles[0].groupSizing;
		
	var ms_responsive = global_menu_obj.main_styles[0].responsive;
	var ms_responsive_transform = global_menu_obj.main_styles[0].responsiveTransform;
	var ms_responsive_label = global_menu_obj.main_styles[0].responsiveLabel;
	var ms_icons = global_menu_obj.main_styles[0].icons;
	var ms_icons_color = global_menu_obj.main_styles[0].iconsColor;
	var ms_arrows = global_menu_obj.main_styles[0].arrows;
	var ms_arrow_transparency = global_menu_obj.main_styles[0].arrowTransparency;
	var ms_arrow_color = global_menu_obj.main_styles[0].arrowColor;
	var ms_font_family = global_menu_obj.main_styles[0].fontFamily;
	var ms_font_color = global_menu_obj.main_styles[0].fontColor;
	var ms_font_hover_color = global_menu_obj.main_styles[0].fontHoverColor;
	var ms_font_size = global_menu_obj.main_styles[0].fontSize;
	var ms_font_sizing = global_menu_obj.main_styles[0].fontSizing;
	var ms_font_weight = global_menu_obj.main_styles[0].fontWeight;
	var ms_font_decoration = global_menu_obj.main_styles[0].fontDecoration;
	//var ms_font_hover_decoration = global_menu_obj.main_styles[0].fontHoverDecoration;
	var ms_zindex = global_menu_obj.main_styles[0].zindex;
	var ms_preset = global_menu_obj.main_styles[0].preset;
	
	//set global variable
	ms_presetSlug = global_menu_obj.main_styles[0].presetSlug;
	
	//logo padding left
	var ms_logo_padding_left = global_menu_obj.main_styles[0].logoPaddingLeft;
	var ms_logo_mobile_padding_left = global_menu_obj.main_styles[0].mobileLogoPaddingLeft;
	var ms_logo_sticky_padding_left = global_menu_obj.main_styles[0].stickyLogoPaddingLeft;
	
	var ms_bg_main_image = global_menu_obj.main_styles[0].bgMainImage;	
	var ms_bg_main_image_url = global_menu_obj.main_styles[0].bgMainImageUrl;
	var ms_bg_main_image_position = global_menu_obj.main_styles[0].bgMainImagePosition;
	var ms_bg_main_image_repeat = global_menu_obj.main_styles[0].bgMainImageRepeat;
	
	//logo url settings
	var ms_logo_link = global_menu_obj.main_styles[0].logoLink;
	var ms_logo_alt = global_menu_obj.main_styles[0].logoAlt;
	var ms_logo_target = global_menu_obj.main_styles[0].logoLinkTarget;
	
	//MENU BAR DIMENTIONS
	///////////////////////////////////////////////////
	
	//set menu dimensions
	if(ms_menu_bar_dimentions){
		if(jQuery('#menu_dimension_full').val() == ms_menu_bar_dimentions){ 
			jQuery('#menu_dimension_full').attr('checked', 'checked');	
			switch_class('full');
			jQuery('#menuBarWidth').prop('disabled', true).addClass('hmenu_disable_input');
		}
		if(jQuery('#menu_dimension_fixed').val() == ms_menu_bar_dimentions){ 
			jQuery('#menu_dimension_fixed').attr('checked', 'checked');	
			switch_class('fixed');
			jQuery('#menuBarWidth').prop('disabled', false).removeClass('hmenu_disable_input');
		}
	}
	
	//set menu width
	if(ms_menu_bar_width){
		jQuery('#menuBarWidth').val(ms_menu_bar_width);
	}
	
	//set menu height
	if(ms_menu_bar_height){
		jQuery('#menuBarHeight').val(ms_menu_bar_height);
	}
	
	//NAV DIMENTIONS
	///////////////////////////////////////////////////
	
	//set nav dimensions
	if(ms_nav_bar_dimentions){
		if(jQuery('#nav_dimension_full').val() == ms_nav_bar_dimentions){ 
			jQuery('#nav_dimension_full').attr('checked', 'checked');
			jQuery('#navBarWidth').prop('disabled', true).addClass('hmenu_disable_input');
		}
		if(jQuery('#nav_dimension_fixed').val() == ms_nav_bar_dimentions){ 
			jQuery('#nav_dimension_fixed').attr('checked', 'checked');
			jQuery('#navBarWidth').prop('disabled', false).removeClass('hmenu_disable_input');
		}
	}
	
	//set nav height
	if(ms_menu_bar_height){
		jQuery('#navBarWidth').val(ms_nav_bar_width);
	}
	
	//PRESETS
	///////////////////////////////////////////////////
	
	//set preset
	if(ms_preset){
		if(jQuery('#preset').val() == ms_preset){ 
			jQuery('#preset').attr('checked', 'checked');
		}
	}
	
	//BACKGROUND COLOR
	///////////////////////////////////////////////////
	
	//set menu gradient
	if(ms_menu_gradient){
		if(jQuery('#bgMenuGradient').val() == ms_menu_gradient){ 
			jQuery('#bgMenuGradient').attr('checked', 'checked');
		}
	}
	
	//set menu color
	if(ms_menu_start_color){
		jQuery('#bgMenuStartColor').val(ms_menu_start_color);
	}
	
	//set menu end color
	if(ms_menu_end_color){
		jQuery('#bgMenuEndColor').val(ms_menu_end_color);
	}
	
	//set gradient path
	if(ms_menu_gradient_path){
		jQuery('#bgMenuGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == ms_menu_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set transparency
	if(ms_menu_transparency){
		jQuery('#bgMenuTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_menu_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set bg image
	if(ms_bg_main_image){
		if(jQuery('#bgMainImage').val() == ms_bg_main_image){ 
			jQuery('#bgMainImage').attr('checked', 'checked');			
		}
	}
	
	//set bg image logo url
	if(ms_bg_main_image_url){
		jQuery('#bgMainImageUrl').val(ms_bg_main_image_url);
		jQuery('.hero_main_background').css('background-image', 'url('+ms_bg_main_image_url+')');
	}
	
	//set bg image position
	if(ms_bg_main_image_position){
		jQuery('#bgMainImagePosition option').each(function(index, element) {
           if(jQuery(this).val() == ms_bg_main_image_position){
			   jQuery(this).attr('selected', 'selected');
		   }
        });
	}
	
	//set bg image repeat
	if(ms_bg_main_image_repeat){
		jQuery('#bgMainImageRepeat option').each(function(index, element) {
           if(jQuery(this).val() == ms_bg_main_image_repeat){
			   jQuery(this).attr('selected', 'selected');
		   }
        });
	}
	
	//BACKGROUND HOVER COLOR
	///////////////////////////////////////////////////
	
	//set background hover type
	if(ms_hover_type){
		if(jQuery('#nav_hover_background').val() == ms_hover_type){ 
			jQuery('#nav_hover_background').attr('checked', 'checked');
			show_hide_type(true, 'Hover background color | Gradients', 'This will be the menu background hover color.');
		}
		if(jQuery('#nav_hover_underline').val() == ms_hover_type){ 
			jQuery('#nav_hover_underline').attr('checked', 'checked');
			show_hide_type(false, 'Hover underline color', 'This will be the color of the underline.');
		}
		if(jQuery('#nav_hover_border').val() == ms_hover_type){ 
			jQuery('#nav_hover_border').attr('checked', 'checked');
			show_hide_type(false, 'Hover border color', 'This will be the color of the border.');
		}
	}
	
	//set menu gradient
	if(ms_hover_gradient){
		if(jQuery('#bgHoverGradient').val() == ms_hover_gradient){ 
			jQuery('#bgHoverGradient').attr('checked', 'checked');
		}
	}
	
	//set hover menu color
	if(ms_hover_start_color){
		jQuery('#bgHoverStartColor').val(ms_hover_start_color);
	}
	
	//set hover color end
	if(ms_hover_end_color){
		jQuery('#bgHoverEndColor').val(ms_hover_end_color);
	}
	
	//set hover gradient path
	if(ms_hover_gradient_path){
		jQuery('#bgHoverGradientPath option').each(function(index, element) {
           if(jQuery(this).val() == ms_hover_gradient_path){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set hover transparency
	if(ms_hover_transparency){
		jQuery('#bgHoverTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_hover_transparency){
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
	if(ms_border_trasparency){
		jQuery('#borderTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_border_trasparency){
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
	
	//set radius
	if(ms_border_radius){
		var radius_array = new Array();
		radius_array = ms_border_radius.split(',');
		jQuery('.ms_border_radius').each(function(index, element) {
            jQuery(this).val(radius_array[index]);
        });
	}
	
	//FONTS
	///////////////////////////////////////////////////
	
	//set font family
	if(ms_font_family){
		jQuery('#fontFamily option').each(function(index, element) {
           if(jQuery(this).val() == ms_font_family){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	update_select_component(jQuery('#fontFamily'));
	
	//set font color
	if(ms_font_color){
		jQuery('#fontColor').val(ms_font_color);
	}
	
	//set hover font color
	if(ms_font_hover_color){
		jQuery('#fontHoverColor').val(ms_font_hover_color);
	}
	
	//set font size
	if(ms_font_size){
		jQuery('#fontSize').val(ms_font_size);
	}
	
	//set font sizing
	if(ms_font_sizing){
		jQuery('#fontSizing option').each(function(index, element) {
           if(jQuery(this).val() == ms_font_sizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set font weight
	if(ms_font_weight){
		jQuery('#fontWeight option').each(function(index, element) {
           if(jQuery(this).val() == ms_font_weight){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//LOGO PADDING
	///////////////////////////////////////////////////
	
	//set logo padding left
	if(ms_logo_padding_left){
		jQuery('#logoPaddingLeft').val(ms_logo_padding_left);
	}
	
	//set mobile logo padding left
	if(ms_logo_mobile_padding_left){
		jQuery('#mobileLogoPaddingLeft').val(ms_logo_mobile_padding_left);
	}
	
	//set sticky logo padding left
	if(ms_logo_sticky_padding_left){
		jQuery('#stickyLogoPaddingLeft').val(ms_logo_mobile_padding_left);
	}
	
	//LOGO URL
	///////////////////////////////////////////////////
	
	//logo link
	if(ms_logo_link){
		jQuery('#logoLink').val(ms_logo_link);
	}
	
	//logo alt
	if(ms_logo_alt){
		jQuery('#logoAlt').val(ms_logo_alt);
	}
	
	//logo link target
	if(ms_logo_target){
		jQuery('#logoLinkTarget option').each(function(index, element) {
           if(jQuery(this).val() == ms_logo_target){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//MAIN LOGO
	///////////////////////////////////////////////////
	
	//set logo
	if(m_menu_logo){
		if(jQuery('#logo').val() == m_menu_logo){ 
			jQuery('#logo').attr('checked', 'checked');			
		}
	}
	
	//set logo url
	if(m_menu_logo_url){
		jQuery('#logoUrl').val(m_menu_logo_url);
		jQuery('.hero_main_logo').css('background-image', 'url('+m_menu_logo_url+')');
	}
	
	//set logo height %
	if(m_menu_logo_height){
		jQuery('#logoHeight').val(m_menu_logo_height);
	}
	
	//MOBILE LOGO
	///////////////////////////////////////////////////
	
	//set mobile logo
	if(m_menu_mobile_logo){
		if(jQuery('#mobileLogo').val() == m_menu_mobile_logo){ 
			jQuery('#mobileLogo').attr('checked', 'checked');			
		}
	}
	
	//set mobile logo url
	if(m_menu_mobile_logo_url){
		jQuery('#mobileLogoUrl').val(m_menu_mobile_logo_url);
		jQuery('.hero_mobile_logo').css('background-image', 'url('+m_menu_mobile_logo_url+')');
	}
	
	//set mobile logo height %
	if(m_menu_mobile_logo_height){
		jQuery('#mobileLogoHeight').val(m_menu_mobile_logo_height);
	}
	
	//PADDING
	///////////////////////////////////////////////////
	
	//set padding left
	if(ms_padding_left){
		jQuery('#paddingLeft').val(ms_padding_left);
	}
	
	//set padding right
	if(ms_padding_right){
		jQuery('#paddingRight').val(ms_padding_right);
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
	
	//set transparency
	if(ms_shadow_transparency){
		jQuery('#shadowTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_shadow_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//shadow color
	if(ms_shadow_color){
		jQuery('#shadowColor').val(ms_shadow_color);
	}
	
	//STICKY MENU
	///////////////////////////////////////////////////
	
	//set sticky
	if(ms_sticky){
		if(jQuery('#sticky').val() == ms_sticky){ 
			jQuery('#sticky').attr('checked', 'checked');
		}
	}
	
	//set sticky logo active
	if(ms_sticky_logo_active){
		if(jQuery('#stickyLogoActive').val() == ms_sticky_logo_active){ 
			jQuery('#stickyLogoActive').attr('checked', 'checked');
		}
	}
	
	//sticky URL
	if(ms_sticky_logo_url){
		jQuery('#stickyUrl').val(ms_sticky_logo_url);
		jQuery('.hero_main_sticky_logo').css('background-image', 'url('+ms_sticky_logo_url+')');
	}
	
	//sticky activate
	if(ms_sticky_activate){
		jQuery('#stickyActivate').val(ms_sticky_activate);
	}
	
	//sticky menu height
	if(ms_sticky_height){
		jQuery('#stickyHeight').val(ms_sticky_height);
	}
	
	//sticky color
	if(ms_sticky_start){
		jQuery('#bgStickyStart').val(ms_sticky_start);
	}
	
	//arrow tranparency
	if(ms_sticky_transparency){
		jQuery('#stickyTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_sticky_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
	//set font color
	if(ms_sticky_font_color){
		jQuery('#stickyFontColor').val(ms_sticky_font_color);
	}
	
	//set hover font color
	if(ms_sticky_font_hover_color){
		jQuery('#stickyFontHoverColor').val(ms_sticky_font_hover_color);
	}
	
	//set font size
	if(ms_sticky_font_size){
		jQuery('#stickyFontSize').val(ms_sticky_font_size);
	}
	
	//set font sizing
	if(ms_sticky_font_sizing){
		jQuery('#stickyFontSizing option').each(function(index, element) {
           if(jQuery(this).val() == ms_sticky_font_sizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//set font weight
	if(ms_sticky_font_weight){
		jQuery('#stickyFontWeight option').each(function(index, element) {
           if(jQuery(this).val() == ms_sticky_font_weight){
			   jQuery(this).attr('selected', 'selected')
		   }
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
	if(ms_arrow_transparency){
		jQuery('#arrowTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_arrow_transparency){
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
	
	//devider tranparency
	
	if(ms_devider){
		if(jQuery('#devider').val() == ms_devider){ 
			jQuery('#devider').attr('checked', 'checked');
		}
	}	
	
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
	
	//devider sizing
	if(ms_devider_sizing){
		jQuery('#deviderSizing option').each(function(index, element) {
           if(jQuery(this).val() == ms_devider_sizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//devider tranparency
	
	if(ms_group_devider){
		if(jQuery('#groupDevider').val() == ms_group_devider){ 
			jQuery('#groupDevider').attr('checked', 'checked');
		}
	}	
	
	if(ms_group_transparency){
		jQuery('#groupTransparency option').each(function(index, element) {
           if(jQuery(this).val() == ms_group_transparency){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//arrow color
	if(ms_group_color){
		jQuery('#groupColor').val(ms_group_color);
	}
	
	//devider sizing
	if(ms_group_sizing){
		jQuery('#groupSizing option').each(function(index, element) {
           if(jQuery(this).val() == ms_group_sizing){
			   jQuery(this).attr('selected', 'selected')
		   }
        });
	}
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
	
}

//switch the classes
function switch_class(type){
	if(type == 'full'){
		jQuery('#nav_change_one').removeClass('nav_full_two');
		jQuery('#nav_change_two').removeClass('nav_fixed_two');
		jQuery('#nav_change_one').addClass('nav_full_width');
		jQuery('#nav_change_two').addClass('nav_fixed_width');
	} else {
		jQuery('#nav_change_one').removeClass('nav_full_width');
		jQuery('#nav_change_two').removeClass('nav_fixed_width');
		jQuery('#nav_change_one').addClass('nav_full_two');
		jQuery('#nav_change_two').addClass('nav_fixed_two');
	}
}

//show hide the type of contentn displayed for hover type
function show_hide_type(status, text, subtext){
	
	//change text
	jQuery('.hero_main_text').html(text);
	//change sub text
	jQuery('.hero_sub_text').html(subtext);
	
	if(status){		
		jQuery('.hero_main_gradient_toggle').show();
		if(global_menu_obj.main_styles[0].bgHoverGradient > 0){
			jQuery('.hero_main_gradient_display').show();
		}
	} else {
		jQuery('.hero_main_gradient_toggle').hide();		
		jQuery('.hero_main_gradient_display').attr('style', 'display: none !important');
	}
	
}

//setup layout and order page with all its data
function enable_update_settings(){
	
	//all the controls	
	var control_menu_dimension_full = jQuery('#menu_dimension_full');
	var control_menu_dimension_fixed = jQuery('#menu_dimension_fixed');
	
	//change: full
	jQuery(control_menu_dimension_full).on('change', function(){
		global_menu_obj.main_styles[0].menuBarDimentions = jQuery(this).val();
		switch_class(jQuery(this).val());
		jQuery('#menuBarWidth').attr('disabled', true).addClass('hmenu_disable_input');
		flag_save_required('save_clicked');
	});
	//change: fixed
	jQuery(control_menu_dimension_fixed).on('change', function(){
		global_menu_obj.main_styles[0].menuBarDimentions = jQuery(this).val();
		switch_class(jQuery(this).val());
		jQuery('#menuBarWidth').attr('disabled', false).removeClass('hmenu_disable_input');
		flag_save_required('save_clicked');
	});
	
	var control_menuBarWidth = jQuery('#menuBarWidth');
	var control_menuBarHeight = jQuery('#menuBarHeight');
	
	//change: width
	jQuery(control_menuBarWidth).on('change keyup', function(){
		global_menu_obj.main_styles[0].menuBarWidth = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: height
	jQuery(control_menuBarHeight).on('change keyup', function(){
		global_menu_obj.main_styles[0].menuBarHeight = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_nav_dimension_full = jQuery('#nav_dimension_full');
	var control_nav_dimension_fixed = jQuery('#nav_dimension_fixed');
	
	//change: nav bar full
	jQuery(control_nav_dimension_full).on('change', function(){
		global_menu_obj.main_styles[0].navBarDimentions = jQuery(this).val();
		jQuery('#navBarWidth').prop('disabled', true).addClass('hmenu_disable_input');
		flag_save_required('save_clicked');
	});
	
	//change: nav bar fixed
	jQuery(control_nav_dimension_fixed).on('change', function(){
		global_menu_obj.main_styles[0].navBarDimentions = jQuery(this).val();
		jQuery('#navBarWidth').prop('disabled', false).removeClass('hmenu_disable_input');
		flag_save_required('save_clicked');
	});
	
	var control_navBarWidth = jQuery('#navBarWidth');
	
	//change: nav bar width
	jQuery(control_navBarWidth).on('change keyup', function(){
		global_menu_obj.main_styles[0].navBarWidth = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_preset = jQuery('#preset');
	
	//change: preset enable
	jQuery(control_preset).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].preset = jQuery(this).val() : global_menu_obj.main_styles[0].preset = 0;
		flag_save_required('save_clicked');
	});
	
	var control_bgMenuGradient = jQuery('#bgMenuGradient');
	var control_bgMenuStartColor = jQuery('#bgMenuStartColor');
	var control_bgMenuEndColor = jQuery('#bgMenuEndColor');
	var control_bgMenuGradientPath = jQuery('#bgMenuGradientPath');
	var control_bgMenuTransparency = jQuery('#bgMenuTransparency');
	
	//change: gradient bg
	jQuery(control_bgMenuGradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].bgMenuGradient = jQuery(this).val() : global_menu_obj.main_styles[0].bgMenuGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_bgMenuStartColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].bgMenuStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_bgMenuEndColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].bgMenuEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgMenuGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgMenuGradientPath).trigger('change');		
	});	
	jQuery(control_bgMenuGradientPath).on('change', function(){
		global_menu_obj.main_styles[0].bgMenuGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgMenuTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgMenuTransparency).trigger('change');		
	});		
	jQuery(control_bgMenuTransparency).on('change', function(){
		global_menu_obj.main_styles[0].bgMenuTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_nav_hover_background = jQuery('#nav_hover_background');
	var control_nav_hover_underline = jQuery('#nav_hover_underline');
	var control_nav_hover_border = jQuery('#nav_hover_border');
	
	//change: bg
	jQuery(control_nav_hover_background).on('change', function(){
		global_menu_obj.main_styles[0].bgHoverType = jQuery(this).val();
		show_hide_type(true, 'Hover background color | Gradients', 'This will be the menu background hover color.');
		flag_save_required('save_clicked');
	});
	//change: underline
	jQuery(control_nav_hover_underline).on('change', function(){
		global_menu_obj.main_styles[0].bgHoverType = jQuery(this).val();
		show_hide_type(false, 'Hover underline color', 'This will be the color of the underline.');
		flag_save_required('save_clicked');
	});
	//change: border
	jQuery(control_nav_hover_border).on('change', function(){
		global_menu_obj.main_styles[0].bgHoverType = jQuery(this).val();
		show_hide_type(false, 'Hover border color', 'This will be the color of the border.');
		flag_save_required('save_clicked');
	});
	
	
	var control_bgHoverGradient = jQuery('#bgHoverGradient');
	var control_bgHoverStartColor = jQuery('#bgHoverStartColor');
	var control_bgHoverEndColor = jQuery('#bgHoverEndColor');
	var control_bgHoverGradientPath = jQuery('#bgHoverGradientPath');
	var control_bgHoverTransparency = jQuery('#bgHoverTransparency');
	
	//change: gradient bg
	jQuery(control_bgHoverGradient).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].bgHoverGradient = jQuery(this).val() : global_menu_obj.main_styles[0].bgHoverGradient = 0;
		flag_save_required('save_clicked');
	});
	
	//change: start color
	jQuery(control_bgHoverStartColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].bgHoverStartColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: end color
	jQuery(control_bgHoverEndColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].bgHoverEndColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: gradient path
	jQuery('.bgHoverGradientPath .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgHoverGradientPath).trigger('change');		
	});	
	jQuery(control_bgHoverGradientPath).on('change', function(){
		global_menu_obj.main_styles[0].bgHoverGradientPath = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});		
	
	//change: gradient path
	jQuery('.bgHoverTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_bgHoverTransparency).trigger('change');		
	});		
	jQuery(control_bgHoverTransparency).on('change', function(){
		global_menu_obj.main_styles[0].bgHoverTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_main_bg_image = jQuery('#bgMainImage');
	var control_main_bg_url = jQuery('#bgMainImageUrl');
	var control_main_bg_position = jQuery('#bgMainImagePosition');
	var control_main_bg_repeat = jQuery('#bgMainImageRepeat');
	
	//change: main image enable
	jQuery(control_main_bg_image).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].bgMainImage = jQuery(this).val() : global_menu_obj.main_styles[0].bgMainImage = 0;
		flag_save_required('save_clicked');
	});
	
	//change: main image URL
	jQuery(control_main_bg_url).on('change keyup', function(){
		global_menu_obj.main_styles[0].bgMainImageUrl = jQuery(this).val();
		jQuery('.hero_main_background').css('background-image', 'url('+jQuery(this).val()+')');
		flag_save_required('save_clicked');
	});
	
	//change: main image position
	jQuery('.bgMainImagePosition .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_main_bg_position).trigger('change');		
	});	
	jQuery(control_main_bg_position).on('change', function(){
		global_menu_obj.main_styles[0].bgMainImagePosition = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: main image repeat
	jQuery('.bgMainImageRepeat .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_main_bg_repeat).trigger('change');		
	});	
	jQuery(control_main_bg_repeat).on('change', function(){
		global_menu_obj.main_styles[0].bgMainImageRepeat = jQuery(this).children('option:selected').val();
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
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].border = jQuery(this).val() : global_menu_obj.main_styles[0].border = 0;
		flag_save_required('save_clicked');
	});
	
	//change: border transparency
	jQuery('.borderTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_borderTransparency).trigger('change');		
	});	
	jQuery(control_borderTransparency).on('change', function(){
		global_menu_obj.main_styles[0].borderTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: border color
	jQuery(control_borderColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].borderColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: border type
	jQuery('.borderType .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_borderType).trigger('change');		
	});	
	jQuery(control_borderType).on('change', function(){
		global_menu_obj.main_styles[0].borderType = jQuery(this).children('option:selected').val();
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
		global_menu_obj.main_styles[0].borderRadius = the_border_radius.slice(0, -1);
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
		global_menu_obj.main_styles[0].fontFamily = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font weight
	jQuery('.fontWeight .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontWeight).trigger('change');		
	});	
	jQuery(control_fontWeight).on('change', function(){
		global_menu_obj.main_styles[0].fontWeight = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font size
	jQuery(control_fontSize).on('change keyup', function(){
		global_menu_obj.main_styles[0].fontSize = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font sizing
	jQuery('.fontSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_fontSizing).trigger('change');		
	});	
	jQuery(control_fontSizing).on('change', function(){
		global_menu_obj.main_styles[0].fontSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: font color
	jQuery(control_fontColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].fontColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: hover font color
	jQuery(control_fontHoverColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].fontHoverColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_logo_padding_left = jQuery('#logoPaddingLeft');
	var control_logo_mobile_padding_left = jQuery('#mobileLogoPaddingLeft');
	var control_logo_sticky_padding_left = jQuery('#stickyLogoPaddingLeft');
	
	//change: logo padding left
	jQuery(control_logo_padding_left).on('change keyup', function(){
		global_menu_obj.main_styles[0].logoPaddingLeft = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: logo mobile padding left
	jQuery(control_logo_mobile_padding_left).on('change keyup', function(){
		global_menu_obj.main_styles[0].mobileLogoPaddingLeft = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: logo sticky padding left
	jQuery(control_logo_sticky_padding_left).on('change keyup', function(){
		global_menu_obj.main_styles[0].stickyLogoPaddingLeft = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_logo_link = jQuery('#logoLink');
	var control_logo_alt = jQuery('#logoAlt');
	var control_logo_target = jQuery('#logoLinkTarget');
	
	//change: logo link
	jQuery(control_logo_link).on('change keyup', function(){
		global_menu_obj.main_styles[0].logoLink = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: logo alt
	jQuery(control_logo_alt).on('change keyup', function(){
		global_menu_obj.main_styles[0].logoAlt = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: logo link target
	jQuery('.logoLinkTarget .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_logo_target).trigger('change');		
	});		
	jQuery(control_logo_target).on('change', function(){
		global_menu_obj.main_styles[0].logoLinkTarget = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	var control_logo = jQuery('#logo');
	var control_logoUrl = jQuery('#logoUrl');
	var control_logoHeight = jQuery('#logoHeight');
	
	//change: logo enable
	jQuery(control_logo).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].logo = jQuery(this).val() : global_menu_obj.main_styles[0].logo = 0;
		flag_save_required('save_clicked');
	});
	
	//change: logo URL
	jQuery(control_logoUrl).on('change keyup', function(){
		global_menu_obj.main_styles[0].logoUrl = jQuery(this).val();
		jQuery('.hero_main_logo').css('background-image', 'url('+jQuery(this).val()+')');
		flag_save_required('save_clicked');
	});
	
	//change: logo Height
	jQuery(control_logoHeight).on('change keyup', function(){
		global_menu_obj.main_styles[0].logoHeight = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_mobile_logo = jQuery('#mobileLogo');
	var control_mobile_logoUrl = jQuery('#mobileLogoUrl');
	var control_mobile_logoHeight = jQuery('#mobileLogoHeight');
	
	//change: logo mobile enable
	jQuery(control_mobile_logo).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].mobileLogo = jQuery(this).val() : global_menu_obj.main_styles[0].mobileLogo = 0;
		flag_save_required('save_clicked');
	});
	
	//change: logo mobile URL
	jQuery(control_mobile_logoUrl).on('change keyup', function(){
		global_menu_obj.main_styles[0].mobileLogoUrl = jQuery(this).val();
		jQuery('.hero_mobile_logo').css('background-image', 'url('+jQuery(this).val()+')');
		flag_save_required('save_clicked');
	});
	
	//change: logo mobile Height
	jQuery(control_mobile_logoHeight).on('change keyup', function(){
		global_menu_obj.main_styles[0].mobileLogoHeight = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
	var control_paddingLeft = jQuery('#paddingLeft');
	var control_paddingRight = jQuery('#paddingRight');
	
	//change: padding left
	jQuery(control_paddingLeft).on('change keyup', function(){
		global_menu_obj.main_styles[0].paddingLeft = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: padding right
	jQuery(control_paddingRight).on('change keyup', function(){
		global_menu_obj.main_styles[0].paddingRight = jQuery(this).val();
		flag_save_required('save_clicked');
	});
		
	var control_shadow = jQuery('#shadow');	
	var control_shadow_distance = jQuery('#shadow_distance');
	var control_shadow_blur = jQuery('#shadow_blur');
	var control_shadowColor = jQuery('#shadowColor');
	var control_shadowTransparency = jQuery('#shadowTransparency');
	
	//change: shadow enable
	jQuery(control_shadow).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].shadow = jQuery(this).val() : global_menu_obj.main_styles[0].shadow = 0;
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
		global_menu_obj.main_styles[0].shadowRadius = the_shadow_radius.slice(0, -1);
		flag_save_required('save_clicked');
	});
	
	//change: shadow color
	jQuery(control_shadowColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].shadowColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: shadow transparency
	jQuery('.shadowTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_shadowTransparency).trigger('change');		
	});	
	jQuery(control_shadowTransparency).on('change', function(){
		global_menu_obj.main_styles[0].shadowTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	var control_sticky = jQuery('#sticky');
	var control_stickyHeight = jQuery('#stickyHeight');
	var control_stickyActivate = jQuery('#stickyActivate');
	var control_stickyTransparency = jQuery('#stickyTransparency');
	var control_bgStickyStart = jQuery('#bgStickyStart');
	var control_stickyLogoActive = jQuery('#stickyLogoActive');
	var control_stickyUrl = jQuery('#stickyUrl');
	
	//change: enable sticky
	jQuery(control_sticky).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].sticky = jQuery(this).val() : global_menu_obj.main_styles[0].sticky = 0;
		flag_save_required('save_clicked');
	});
	
	//change: sticky height
	jQuery(control_stickyHeight).on('change keyup', function(){
		global_menu_obj.main_styles[0].stickyHeight = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: sticky activate
	jQuery(control_stickyActivate).on('change keyup', function(){
		global_menu_obj.main_styles[0].stickyActivate = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: sticky transparency
	jQuery('.stickyTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_stickyTransparency).trigger('change');		
	});	
	jQuery(control_stickyTransparency).on('change', function(){
		global_menu_obj.main_styles[0].stickyTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: sticky bg
	jQuery(control_bgStickyStart).on('change keyup', function(){
		global_menu_obj.main_styles[0].bgStickyStart = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: enable logo sticky
	jQuery(control_stickyLogoActive).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].stickyLogoActive = jQuery(this).val() : global_menu_obj.main_styles[0].stickyLogoActive = 0;
		flag_save_required('save_clicked');
	});
	
	//change: sticky url
	jQuery(control_stickyUrl).on('change keyup', function(){
		global_menu_obj.main_styles[0].stickyUrl = jQuery(this).val();
		jQuery('.hero_main_sticky_logo').css('background-image', 'url('+jQuery(this).val()+')');
		flag_save_required('save_clicked');
	});
	
	var control_sticky_fontWeight = jQuery('#stickyFontWeight');
	var control_sticky_fontSize = jQuery('#stickyFontSize');
	var control_sticky_fontSizing = jQuery('#stickyFontSizing');
	var control_sticky_fontColor = jQuery('#stickyFontColor');
	var control_sticky_fontHoverColor = jQuery('#stickyFontHoverColor');
	//var control_sticky_fontHoverDecoration = jQuery('#stickyFontHoverDecoration');	
	
	//change: font weight
	jQuery('.stickyFontWeight .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_sticky_fontWeight).trigger('change');		
	});	
	jQuery(control_sticky_fontWeight).on('change', function(){
		global_menu_obj.main_styles[0].stickyFontWeight = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});	
	
	//change: font size
	jQuery(control_sticky_fontSize).on('change keyup', function(){
		global_menu_obj.main_styles[0].stickyFontSize = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: font sizing
	jQuery('.stickyFontSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_sticky_fontSizing).trigger('change');		
	});	
	jQuery(control_sticky_fontSizing).on('change', function(){
		global_menu_obj.main_styles[0].stickyFontSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: font color
	jQuery(control_sticky_fontColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].stickyFontColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	//change: hover font color
	jQuery(control_sticky_fontHoverColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].stickyFontHoverColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
	var control_arrows = jQuery('#arrows');
	var control_arrowTransparency = jQuery('#arrowTransparency');
	var control_arrowColor = jQuery('#arrowColor');
	
	//change: enable arrows
	jQuery(control_arrows).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].arrows = jQuery(this).val() : global_menu_obj.main_styles[0].arrows = 0;
		flag_save_required('save_clicked');
	});
	
	//change: arrow transparency
	jQuery('.arrowTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_arrowTransparency).trigger('change');		
	});	
	jQuery(control_arrowTransparency).on('change', function(){
		global_menu_obj.main_styles[0].arrowTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: sticky color arrow
	jQuery(control_arrowColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].arrowColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
	var control_devider = jQuery('#devider');
	var control_deviderTransparency = jQuery('#deviderTransparency');
	var control_deviderColor = jQuery('#deviderColor');
	var control_deviderSizing = jQuery('#deviderSizing');
	
	//change: enable line devider
	jQuery(control_devider).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].devider = jQuery(this).val() : global_menu_obj.main_styles[0].devider = 0;
		flag_save_required('save_clicked');
	});
	
	//change: devider transparency
	jQuery('.deviderTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_deviderTransparency).trigger('change');		
	});	
	jQuery(control_deviderTransparency).on('change', function(){
		global_menu_obj.main_styles[0].deviderTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: devider color
	jQuery(control_deviderColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].deviderColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
	//change: devider sizing
	jQuery('.deviderSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_deviderSizing).trigger('change');		
	});	
	jQuery(control_deviderSizing).on('change', function(){
		global_menu_obj.main_styles[0].deviderSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	var control_groupDevider = jQuery('#groupDevider');
	var control_groupTransparency = jQuery('#groupTransparency');
	var control_groupColor = jQuery('#groupColor');
	var control_groupSizing = jQuery('#groupSizing');
	
	//change: enable group devider
	jQuery(control_groupDevider).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].groupDevider = jQuery(this).val() : global_menu_obj.main_styles[0].groupDevider = 0;
		flag_save_required('save_clicked');
	});
	
	//change: devider transparency
	jQuery('.deviderTransparency .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_groupTransparency).trigger('change');		
	});	
	jQuery(control_groupTransparency).on('change', function(){
		global_menu_obj.main_styles[0].groupTransparency = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
	//change: devider color
	jQuery(control_groupColor).on('change keyup', function(){
		global_menu_obj.main_styles[0].groupColor = jQuery(this).val();
		flag_save_required('save_clicked');
	});	
	
	//change: devider sizing
	jQuery('.groupSizing .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_groupSizing).trigger('change');		
	});	
	jQuery(control_groupSizing).on('change', function(){
		global_menu_obj.main_styles[0].groupSizing = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked');
	});
	
}

//get presets
function get_presets(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_load_presets'
		},
		dataType: "json"
	}).done(function(data){
		preset_html(data);
	}).fail(function(){
		//page error
	});
}

//preset html
function preset_html(data){
	var html = '';
	jQuery(data).each(function(index, element) {
    	html += '<div class="hero_preset_color rounded_20" data-slug="'+element.slug+'" data-name="'+element.presetName+'" data-preset="'+element.presetId+'" data-color-one="'+element.bgColor+'" data-color-two="'+element.hoverColor+'" data-color-font="'+element.textColor+'" data-color-icon="'+element.iconColor+'">';                            	
			html += '<div class="hero_preset_one rounded_left_20"></div>';  
			html += '<div class="hero_preset_two rounded_right_20"></div>';  
		html += '</div>';  
    });
	jQuery('.hero_preset_holder').html(html);
	set_presets();
}

//set presets
function set_presets(){
	
	//show colors
	jQuery('.hero_preset_color').each(function(index, element) {
		if(jQuery(this).data('slug') == ms_presetSlug){
			jQuery(this).addClass('hero_preset_active');
			jQuery('.hero_preset_selection').html(jQuery(this).data('name'));
		} else {
			jQuery(this).removeClass('hero_preset_active');
		}
        var color_one = jQuery(this).data('color-one');
        var color_two = jQuery(this).data('color-two');
		jQuery(this).children('.hero_preset_one').css({
			'background-color':color_one
		});
		jQuery(this).children('.hero_preset_two').css({
			'background-color':color_two
		});
    });
	
	//click
	jQuery('.hero_preset_color').off().on('click', function(){
		global_menu_obj.main_styles[0].presetSlug = jQuery(this).data('slug');
		ms_presetSlug = jQuery(this).data('slug');
		flag_save_required('save_clicked');
		change_colors(jQuery(this).data('color-one'),jQuery(this).data('color-two'),jQuery(this).data('color-font'),jQuery(this).data('color-icon'));
		set_presets();
	});
	
}

//change colors
function change_colors(primary_color, secondary_color, third_color, forth_color){
	
	//set main navigation styles
	global_menu_obj.main_styles[0].bgMenuStartColor = primary_color;
		jQuery('#bgMenuStartColor').val(primary_color);
		jQuery('#bgMenuStartColor').trigger('change');
	
	global_menu_obj.main_styles[0].bgMenuEndColor = secondary_color;
		jQuery('#bgMenuEndColor').val(secondary_color);
		jQuery('#bgMenuEndColor').trigger('change');
	
	global_menu_obj.main_styles[0].bgHoverStartColor = secondary_color;
		jQuery('#bgHoverStartColor').val(secondary_color);
		jQuery('#bgHoverStartColor').trigger('change');
	
	global_menu_obj.main_styles[0].bgHoverEndColor = primary_color;
		jQuery('#bgHoverEndColor').val(primary_color);
		jQuery('#bgHoverEndColor').trigger('change');
		
	global_menu_obj.main_styles[0].fontColor = third_color;
		jQuery('#fontColor').val(third_color);
		jQuery('#fontColor').trigger('change');
		
	global_menu_obj.main_styles[0].arrowColor = third_color;
		jQuery('#arrowColor').val(third_color);
		jQuery('#arrowColor').trigger('change');
		
	global_menu_obj.main_styles[0].deviderColor = third_color;
		jQuery('#deviderColor').val(third_color);
		jQuery('#deviderColor').trigger('change');
	
	global_menu_obj.main_styles[0].groupColor = third_color;
		jQuery('#groupColor').val(third_color);
		jQuery('#groupColor').trigger('change');
		
	global_menu_obj.main_styles[0].fontHoverColor = forth_color;
		jQuery('#fontHoverColor').val(forth_color);
		jQuery('#fontHoverColor').trigger('change');
	
	global_menu_obj.main_styles[0].bgStickyStart = secondary_color;
		jQuery('#bgStickyStart').val(secondary_color);
		jQuery('#bgStickyStart').trigger('change');
		
	global_menu_obj.main_styles[0].stickyFontColor = third_color;
		jQuery('#stickyFontColor').val(third_color);
		jQuery('#stickyFontColor').trigger('change');
		
	global_menu_obj.main_styles[0].stickyFontHoverColor = third_color;
		jQuery('#stickyFontHoverColor').val(third_color);
		jQuery('#stickyFontHoverColor').trigger('change');
		
	//set drop down styles
	global_menu_obj.dropdown_styles[0].bgDropStartColor = primary_color;
	global_menu_obj.dropdown_styles[0].bgDropEndColor = secondary_color;
	global_menu_obj.dropdown_styles[0].bgHoverStartColor = secondary_color;
	global_menu_obj.dropdown_styles[0].bgHoverEndColor = primary_color;
	global_menu_obj.dropdown_styles[0].fontColor = third_color;
	global_menu_obj.dropdown_styles[0].fontHoverColor = forth_color;
	global_menu_obj.dropdown_styles[0].arrowColor = third_color;
	global_menu_obj.dropdown_styles[0].deviderColor = third_color;	
	
	//set mega styles
	global_menu_obj.mega_styles[0].bgDropStartColor = primary_color;
	global_menu_obj.mega_styles[0].bgDropEndColor = secondary_color;
	global_menu_obj.mega_styles[0].bgHoverStartColor = secondary_color;
	global_menu_obj.mega_styles[0].bgHoverEndColor = primary_color;
	global_menu_obj.mega_font_styles[0].fontColor = third_color;
	global_menu_obj.mega_font_styles[1].fontColor = forth_color;
	global_menu_obj.mega_font_styles[2].fontColor = third_color;
	global_menu_obj.mega_font_styles[3].fontColor = forth_color;
	global_menu_obj.mega_styles[0].deviderColor = third_color;
	global_menu_obj.mega_styles[0].borderColor = third_color;
	global_menu_obj.mega_styles[0].fontHoverColor = third_color;
	
	//set search styles
	global_menu_obj.search_styles[0].fontColor = third_color; 
	global_menu_obj.search_styles[0].borderColor = third_color; 
	
	//product styles
	global_menu_obj.main_styles[0].iconProductColor = third_color;
	global_menu_obj.main_styles[0].iconProductHoverColor = forth_color;
	
	
}
