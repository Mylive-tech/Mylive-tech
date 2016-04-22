//LAYOUT.EDIT VIEW

//global vars

//load
jQuery(function(){
	set_layout_order();
	find_toggle_elements();
	enable_horizontal_drag();
	position_center();
	set_widths();
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);		
});

//setup layout and order page with all its data
function set_layout_order(){
	
	//page variables	
	var m_orientation = global_menu_obj.main_styles[0].orientation;	
	var m_left_items = global_menu_obj.menu.leftItems;
	var m_center_items = global_menu_obj.menu.centerItems;
	var m_right_items = global_menu_obj.menu.rightItems;
	var m_logo = global_menu_obj.main_styles[0].logo;
	var m_search = global_menu_obj.main_styles[0].search;
	var m_arrows = global_menu_obj.main_styles[0].arrows;
	var m_devider = global_menu_obj.main_styles[0].devider;
	var m_group_devider = global_menu_obj.main_styles[0].groupDevider;	
	var m_menu = global_menu_obj.main_styles[0].menu;
	var m_social = global_menu_obj.main_styles[0].social;
	var m_cart = global_menu_obj.main_styles[0].cart;	
	var m_zindex = global_menu_obj.main_styles[0].zindex;
	
	//MENU SETTINGS
	///////////////////////////////////////////////////
	
	//set orientation
	if(m_orientation){
		if(jQuery('#orientation_horizontal').val() == m_orientation){ 
			jQuery('#orientation_horizontal').attr('checked', 'checked');
		}
		if(jQuery('#orientation_vertical').val() == m_orientation){ 
			jQuery('#orientation_vertical').attr('checked', 'checked');
		}
	}
	
	//set logo
	if(m_logo){
		if(jQuery('#logo').val() == m_logo){ 
			jQuery('#logo').attr('checked', 'checked');
		}
	}
	
	//set search
	if(m_search){
		if(jQuery('#search').val() == m_search){ 
			jQuery('#search').attr('checked', 'checked');
		}
	}
	
	//set arrows
	if(m_arrows){
		if(jQuery('#arrows').val() == m_arrows){ 
			jQuery('#arrows').attr('checked', 'checked');
		}
	}
	
	//set devider
	if(m_devider){
		if(jQuery('#devider').val() == m_devider){ 
			jQuery('#devider').attr('checked', 'checked');
		}
	}
	
	//set group devider
	if(m_group_devider){
		if(jQuery('#groupDevider').val() == m_group_devider){ 
			jQuery('#groupDevider').attr('checked', 'checked');
		}
	}
	
	//set menu
	if(m_menu){
		if(jQuery('#menu').val() == m_menu){ 
			jQuery('#menu').attr('checked', 'checked');
		}
	}
	
	//set social
	if(m_social){
		if(jQuery('#social').val() == m_social){ 
			jQuery('#social').attr('checked', 'checked');
		}
	}
	
	//set cart
	if(m_cart){
		if(jQuery('#cart').val() == m_cart){ 
			jQuery('#cart').attr('checked', 'checked');
		}
	}
	
	//LAYOUT AND ORDER
	///////////////////////////////////////////////////
	
	if(m_left_items){
		var the_items = m_left_items.split(",");
		var the_item = '';
		jQuery(the_items).each(function(index, element) {
            the_item += return_item_html(element);
        });
		jQuery('.hero_position_left').html(the_item);
	}
	
	if(m_center_items){
		var the_items = m_center_items.split(",");
		var the_item = '';
		jQuery(the_items).each(function(index, element) {
            the_item += return_item_html(element);
        });
		jQuery('.hero_position_center').html(the_item);
	}
	
	if(m_right_items){
		var the_items = m_right_items.split(",");
		var the_item = '';
		jQuery(the_items).each(function(index, element) {
            the_item += return_item_html(element);
        });
		jQuery('.hero_position_right').html(the_item);
	}
	
	//set logo
	if(m_logo){
		if(jQuery('#logo').val() == m_logo){ 
			jQuery('.hero_sandbox_logo').show();
			jQuery('.hero_sandbox_logo').addClass('hero_is_active');
		}
	}
	
	//set search
	if(m_search){
		if(jQuery('#search').val() == m_search){ 
			jQuery('.hero_sandbox_search').show();
			jQuery('.hero_sandbox_search').addClass('hero_is_active');
		}
	}
	
	//set menu
	if(m_menu){
		if(jQuery('#menu').val() == m_menu){ 
			jQuery('.hero_sandbox_main').show();
			jQuery('.hero_sandbox_main').addClass('hero_is_active');
		}
	}
	
	//set social
	if(m_social){
		if(jQuery('#social').val() == m_social){ 
			jQuery('.hero_sandbox_social').show();
			jQuery('.hero_sandbox_social').addClass('hero_is_active');
		}
	}
	
	//set cart
	if(m_cart){
		if(jQuery('#cart').val() == m_cart){ 
			jQuery('.hero_sandbox_product').show();
			jQuery('.hero_sandbox_product').addClass('hero_is_active');
		}
	}
	
	//ZINDEX
	///////////////////////////////////////////////////
	
	//z-index
	if(m_zindex){
		jQuery('#zindex').val(m_zindex);
	}
	
	//switch components
	switch_components();
	
	//enable update settings, this enables the ability to change all data in the object
	enable_update_settings();
	
}

//setup layout and order page with all its data
function enable_update_settings(){
	
	//all the controls
	var control_horizontal = jQuery('#orientation_horizontal');
	var control_vertical = jQuery('#orientation_vertical');
	var control_logo = jQuery('#logo');
	var control_search = jQuery('#search');
	var control_arrows = jQuery('#arrows');
	var control_groupDevider = jQuery('#groupDevider');
	var control_menu = jQuery('#menu');
	var control_social = jQuery('#social');
	var control_cart = jQuery('#cart');
	var control_deviders = jQuery('#devider');
	var control_menu_location = jQuery('#menu_location');
	var control_zindex = jQuery('#zindex');
	
	//change: horizontal
	jQuery(control_horizontal).on('change', function(){
		global_menu_obj.main_styles[0].orientation = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	//change: vertical
	jQuery(control_vertical).on('change', function(){
		global_menu_obj.main_styles[0].orientation = jQuery(this).val();
		flag_save_required('save_clicked');
	});		
	//change: logo
	jQuery(control_logo).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].logo = jQuery(this).val() : global_menu_obj.main_styles[0].logo = 0;
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_logo').show() : jQuery('.hero_sandbox_logo').hide();
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_logo').addClass('hero_is_active') : jQuery('.hero_sandbox_logo').removeClass('hero_is_active');	
		set_widths();	
		position_center();	
		flag_save_required('save_clicked');
	});
	//change: search
	jQuery(control_search).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].search = jQuery(this).val() : global_menu_obj.main_styles[0].search = 0;
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_search').show() : jQuery('.hero_sandbox_search').hide();	
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_search').addClass('hero_is_active') : jQuery('.hero_sandbox_search').removeClass('hero_is_active');
		set_widths();
		position_center();
		flag_save_required('save_clicked');
	});
	//change: arrows
	jQuery(control_arrows).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].arrows = jQuery(this).val() : global_menu_obj.main_styles[0].arrows = 0;
		flag_save_required('save_clicked');
	});
	//change: group devider
	jQuery(control_groupDevider).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].groupDevider = jQuery(this).val() : global_menu_obj.main_styles[0].groupDevider = 0;
		flag_save_required('save_clicked');
	});
	//change: menu
	jQuery(control_menu).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].menu = jQuery(this).val() : global_menu_obj.main_styles[0].menu = 0;
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_main').show() : jQuery('.hero_sandbox_main').hide();	
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_main').addClass('hero_is_active') : jQuery('.hero_sandbox_main').removeClass('hero_is_active');
		set_widths();
		position_center();
		flag_save_required('save_clicked');
	});
	//change: social
	jQuery(control_social).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].social = jQuery(this).val() : global_menu_obj.main_styles[0].social = 0;
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_social').show() : jQuery('.hero_sandbox_social').hide();	
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_social').addClass('hero_is_active') : jQuery('.hero_sandbox_social').removeClass('hero_is_active');
		set_widths();
		position_center();
		flag_save_required('save_clicked');
	});
	//change: cart
	jQuery(control_cart).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].cart = jQuery(this).val() : global_menu_obj.main_styles[0].cart = 0;
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_product').show() : jQuery('.hero_sandbox_product').hide();
		jQuery(this).prop('checked') ? jQuery('.hero_sandbox_product').addClass('hero_is_active') : jQuery('.hero_sandbox_product').removeClass('hero_is_active');
		set_widths();
		position_center();
		flag_save_required('save_clicked');
	});
	//change: devider
	jQuery(control_deviders).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.main_styles[0].devider = jQuery(this).val() : global_menu_obj.main_styles[0].devider = 0;
		flag_save_required('save_clicked');
	});		
	//change: zindex
	jQuery(control_zindex).on('change keyup', function(){
		global_menu_obj.main_styles[0].zindex = jQuery(this).val();
		flag_save_required('save_clicked');
	});
	
}

//return html
function return_item_html(element){
	var item_html = '';
	switch(element){
		case 'logo':
			item_html += '<li class="hero_sandbox_logo" data-id="'+element+'">Logo</li>';
		break;
		case 'main':
			item_html += '<li class="hero_sandbox_main" data-id="'+element+'">Main Navigation</li>';
		break;
		case 'product':
			item_html += '<li class="hero_sandbox_product" data-id="'+element+'">Cart</li>';
		break;
		case 'search':
			item_html += '<li class="hero_sandbox_search" data-id="'+element+'">Search</li>';
		break;
		case 'social':
			item_html += '<li class="hero_sandbox_social" data-id="'+element+'">Social</li>';
		break;
		default:
			item_html += '<li class="hero_sandbox_standard" data-id="'+element+'">'+element+'</li>';
	}	
	return(item_html);
}

//example
function enable_horizontal_drag(){
	
	jQuery(".hero_main_sandbox ul").sortable({
		placeholder: "hero_nav_place",
		revert: true,
		connectWith: ".connect_nav_items",
		sort: function(event, ui){
			//ui.placeholder.width(ui.item.width());
			ui.placeholder.html('<div class="herp_place_arrow"></div>');
		},
		change: function(event, ui){
			set_widths();
		},
		stop: function(event, ui){			
			position_center();
			set_widths();
			check_div_positions();
		}
	});
	
	jQuery( ".hero_main_sandbox" ).disableSelection();
	
}

//check the div positions
function check_div_positions(){
	var hero_position_left = jQuery('.hero_position_left');
	var hero_position_center = jQuery('.hero_position_center');
	var hero_position_right = jQuery('.hero_position_right');
	var left = '';
	var center = '';
	var right = '';
	jQuery(hero_position_left.children('li')).each(function(index, element) {
        left += jQuery(this).data('id') + ",";
    });
	jQuery(hero_position_center.children('li')).each(function(index, element) {
        center += jQuery(this).data('id') + ",";
    });
	jQuery(hero_position_right.children('li')).each(function(index, element) {
        right += jQuery(this).data('id') + ",";
    });
	global_menu_obj.menu.leftItems = left.slice(0, -1);
	global_menu_obj.menu.centerItems = center.slice(0, -1);
	global_menu_obj.menu.rightItems = right.slice(0, -1);
	flag_save_required('save_clicked');
}

//reposition center div
function position_center(){
	var center_holder_width = 0;
	var margin_left = 0;
	jQuery('.hero_position_center li').each(function(index, element) {
		if(jQuery(this).hasClass('hero_is_active') || jQuery(this).hasClass('hero_nav_place')){
			center_holder_width = center_holder_width + jQuery(this).width();
		}
    });
	margin_left = center_holder_width / 2;
	jQuery('.hero_position_center').animate({
		'margin-left': '-' + margin_left
	}, 500);
}

//set widths
function set_widths(){
	jQuery('.hero_main_sandbox ul').each(function(index, element) {
		var container_width = 0;
		jQuery(this).children('li').each(function(index, element) {
			if(jQuery(this).hasClass('hero_is_active') || jQuery(this).hasClass('hero_nav_place')){
				container_width = container_width + jQuery(this).width();
			}
		});	
		jQuery(this).css({
			'width': container_width + 'px'
		});	
    });
}