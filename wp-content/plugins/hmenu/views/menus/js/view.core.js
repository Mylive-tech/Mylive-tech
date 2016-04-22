//MENUS VIEW CORE

var global_menu_obj;
var global_google_fonts;

var global_responsive_check = true;

//load
jQuery(function(){
	bind_insert_menu_listener();
	load_google_fonts();
});

//load google fonts
function load_google_fonts(){
	
	jQuery.ajax({
		url: 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCe3XGw8IKuzIXe7bL6ZQc1xbe3MX5DR-s',
		type: "GET",
		dataType: "json"
	}).done(function(data){	
		global_google_fonts = data.items;
	}).fail(function(){
		//error
	});
	
}

//populate google fonts
function populate_fonts(fonts){
	
	//populate selct boxes according to array above
	jQuery(fonts).each(function(idx, elm) {
       
	    var the_fonts = '';
		
		var default_fonts = ['inherit', 'Arial', 'Verdana', 'Times New Roman', 'Times', 'Trebuchet MS', 'sans-serif', 'serif'];
		
		jQuery(default_fonts).each(function(index, element) {
			the_fonts += "<option value='"+element+"'>"+element+"</option>";		
		});
		
		jQuery(global_google_fonts).each(function(index, element) {
			the_fonts += "<option value='"+element.family+"'>"+element.family+"</option>";			
		});
		
		jQuery('#'+elm).html(the_fonts);
		
		update_select_component(jQuery('#'+elm));
		
    });	
	
}

//bind font load
function execute_font_load(type_to_load){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'icons': type_to_load,
			'action': 'hmenu_load_fonts'
		},
		dataType: "json"
	}).done(function(data){	
		font_obj = data.font;
		if(data){			
			load_styles(font_obj);
			load_select(font_obj);			
			load_icons(0, type_to_load); ///load default
			load_icons_click();
		} else {
			load_icon_message('You dont have any Icons loaded, click add new icons to get started.');
		}
	}).fail(function(){
		 //page error
	});
}

//load icons
function load_icons(idx, type){
	
	var icon_html = '';
	
	jQuery(font_obj[idx].icons).each(function(index, element) {
        icon_html += '<div class="icon_item icon_'+font_obj[idx].fontName+'_'+element.iconContent+' rounded_3" data-class="icon_'+font_obj[idx].fontName+'_'+element.iconContent+'"></div>'
    });	
	
	jQuery('.icons_load_here').html(icon_html);
	
	if(type == 'social'){
		insert_menu_icon();
	}
}

//load edit content
function load_edit(json){
	//get menu object
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'id': json.menuId,
			'action': 'hmenu_load_menu_object'
		},
		dataType: "json"
	}).done(function(data){	
		global_menu_obj = data;
		manual_load_view('dropdown_menus');
		//unlock core view
		unlock_core_view_reload();
		//highlight active
		setTimeout(function(){
			jQuery('.hero_sub #sub_item_row_'+ global_menu_obj.menu.menuId).addClass('active_sidebar_elem');
		},400);
		//generate files
		//generate_files(global_menu_obj);
	}).fail(function(event){
		 //page error		
	});
}

//num only
function num_only(evt){
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if( charCode == 37 || charCode == 45){
		return true;
	} else if(charCode > 31 && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
}

//check media inputs
function check_media_inputs(){
	
	//check responsive inputs
	var input_one = parseInt(jQuery('#siteResponsiveOne').val());
	var input_two = parseInt(jQuery('#siteResponsiveTwo').val());
	var input_three = parseInt(jQuery('#siteResponsiveThree').val());
	
	var reset_height_one = 768;
	var reset_height_two = 992;
	var reset_height_three = 1200;
	
	var error_count = 0;
	var error_message = 'You have an error, please check below: </br>';
	
	if(input_one >= 320 && input_one < input_two){
		jQuery('#siteResponsiveOne').removeClass('has-error');
		reset_height_one = input_one;
	} else {
		error_count++;
		jQuery('#siteResponsiveOne').addClass('has-error');
		error_message += '<span>Please make sure your Mobile resolution is bigger than 320px and smaller than your Tablet resolution.</span>';
	}
	
	if(input_two > input_one && input_two < input_three){
		jQuery('#siteResponsiveTwo').removeClass('has-error');
		reset_height_two = input_two;
	} else {
		error_count++;
		jQuery('#siteResponsiveTwo').addClass('has-error');
		error_message += '<span>Please make sure your Tablet resolution is bigger than your Mobile resolution and smaller than your Large resolution.</span>';
	}
	
	if(input_three > input_two){
		jQuery('#siteResponsiveThree').removeClass('has-error');
		reset_height_three = input_three;
	} else {
		error_count++;
		jQuery('#siteResponsiveThree').addClass('has-error');
		error_message += '<span>Please make sure your Large resolution is bigger than your Tablet resolution.</span>';
	}
	
	var save_status;
	
	if(error_count == 0){
		save_status = true;
		jQuery('.hmenu_site_responsive').animate({
			height:0
		});	
	} else {
		jQuery('.hmenu_site_responsive_inner').html(error_message);		
		var the_height = jQuery('.hmenu_site_responsive_inner').height() + 30;
		jQuery('.hmenu_site_responsive').animate({
			height:the_height
		},100);		
		save_status = false;
	}
	
	global_responsive_check = save_status;
	
	if(!global_responsive_check){
		global_menu_obj.main_styles[0].siteResponsiveOne = 768;
		global_menu_obj.main_styles[0].siteResponsiveTwo = 992;
		global_menu_obj.main_styles[0].siteResponsiveThree = 1200;
	} else {
		global_menu_obj.main_styles[0].siteResponsiveOne = reset_height_one;
		global_menu_obj.main_styles[0].siteResponsiveTwo = reset_height_two;
		global_menu_obj.main_styles[0].siteResponsiveThree = reset_height_three;
	}
	
}

var object_to_save;
var nav_temp_object;
var nav_object;
var sty_object;
	
//save event callback
function save_clicked(json){
	
	//NAV SAVE

	nav_temp_object = '{"menu":[], "nav_items": []}';
	nav_object = JSON.parse(nav_temp_object);
	
	nav_object.menu = global_menu_obj.menu;
	nav_object.nav_items = global_menu_obj.nav_items;
	
	if(json.status){
		jQuery('.hmenu_structure_loader').fadeIn();
	}
	
	//run the save
	process_save(json, nav_object, 'navigation_structure', false);
	
	//DEFAULT SAVE
	sty_object = jQuery.extend(true, {}, global_menu_obj); 
	delete sty_object['nav_items'];
	
	//run the save
	process_save(json, sty_object, 'default', true);
	
}

function process_save(json, object_to_save, save_type, generate){
	
	//send update object
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'obj': object_to_save,
			'action': 'hmenu_send_update_object',
			'save': save_type
		},
		dataType: "json"
	}).done(function(data){					
		if(json.status){
			reload_object(data.menu_id, 'menu_structure');
		}
		if(json.status_social){
			reload_object(data.menu_id, 'menu_social');
		}		
		//generate files
		if(generate){
			generate_files(global_menu_obj);
			show_message('success', 'Menu Saved', 'Saved process complete, menu ready to use.');
		}
	}).fail(function(event){
		 //page error
	});
	
}

//nav save event callback
function reload_object(menu_id, type){
	//get menu object
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'id': menu_id,
			'action': 'hmenu_load_menu_object'
		},
		dataType: "json"
	}).done(function(data){
		if(type == 'menu_structure'){
			global_menu_obj = data;
			jQuery('.sort').html('');
			get_nav();
			switch_components();
			menu_structure_status = false;
			//generate files
			generate_files(global_menu_obj);
			jQuery('.hmenu_structure_loader').fadeOut();
		} else if(type == 'menu_social'){
			global_menu_obj = data;
			jQuery('.hero_icon_sort_holder').html('');
			get_social_items();
			switch_components();
			//generate files
			generate_files(global_menu_obj);
		}
	}).fail(function(event){
		 //page error
	});
}

// FONT LOADING
function execute_global_font_load(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'icons': 'icons',
			'action': 'hmenu_load_fonts'
		},
		dataType: "json"
	}).done(function(data){	
		font_obj = data.font;
		if(data){			
			load_styles(font_obj);
			load_icon_select(font_obj);
			filter_global_icons();
			load_global_icons(0); ///load default
		}
	}).fail(function(){
		 //page error
	});
}

//load styles
function load_styles(obj){
	
	jQuery(obj).each(function(index, element) {	
		
		var font_family = element.fontName;
		
		//check if stylesheet exist
		if (jQuery('#'+font_family).length) {
			//dont add stylesheet again
		} else {
			
			//load google fonts API
			var hmenu_css_font_file = plugin_url+"_fonts/"+font_family+".css";
			
			var css_font_file = document.createElement("link");
			css_font_file.rel = "stylesheet";
			css_font_file.type = "text/css";
			css_font_file.id = font_family;
			css_font_file.href = hmenu_css_font_file;
			document.head.appendChild(css_font_file);
			
		}
		
    });
		
}

//load select
function load_icon_select(obj){
	
	var select_html = '';	
	jQuery(obj).each(function(index, element) {	
		//build styles
        select_html += '<option value="'+index+'">'+element.fontPackName+'</option>';
    });	
	jQuery('#icon_select').append(select_html);	
	update_select_component(jQuery('#icon_select'));
	
}

//load select click to filter the icons
function filter_global_icons(){
	
	var the_options = jQuery('.icon_select .hero_dropdown .hero_drop_row');
	jQuery(the_options).each(function(index, element) {
        jQuery(this).on('click', function(){
			load_global_icons(jQuery(this).data('value'));
		});
    });
	
}

//global
var input_link_id = 0;

//load icons
function load_global_icons(idx){
	
	var icon_html = '';	
	jQuery(font_obj[idx].icons).each(function(index, element) {
        icon_html += '<div class="global_icon_item icon_'+font_obj[idx].fontName+'_'+element.iconContent+' rounded_3" data-content="icon_'+font_obj[idx].fontName+'_'+element.iconContent+'"></div>'
    });			
	jQuery('.icons_load_global').html(icon_html);
	enable_icon_item_select(input_link_id);
		
}

//open panel
function enable_icon_select(){
	
	jQuery('.hero_open_icons').off().on('click', function (){		
		var the_input_link = jQuery(this).data('input-link');		
		if(jQuery(this).attr('data-panel-toggle') == 'close'){				
			jQuery(this).attr('data-panel-toggle', 'open');				
			jQuery('.hero_side_icon_panel').attr('data-input-link', jQuery(this).attr('data-input-link'));			
			jQuery('.hero_side_icon_panel').animate({
				'right': 0
			}, 400);
			input_link_id = jQuery(this).attr('data-input-link')
			enable_icon_item_select	();		
		} else if(jQuery(this).attr('data-panel-toggle') == 'open'){			
			jQuery(this).attr('data-panel-toggle', 'close');			
			jQuery('.hero_side_icon_panel').animate({
				'right': -300
			}, 400);				
		}		
	});
	jQuery('.hero_selected_icon').off().on('click', function (){	
		//trigger
		jQuery('.main_holder').find('[data-load-link='+jQuery(this).attr('data-trigger')+']').trigger('click');
	});
	
}

//set the icon of your nav/list item
function enable_icon_item_select(){	
	jQuery('.global_icon_item ').off().on('click', function(){
		jQuery("#"+input_link_id).val(jQuery(this).data('content'));
		jQuery("#"+input_link_id).trigger('change');
		disable_icon_select();
	});		
}

//close panel
function disable_icon_select(){	
	
	jQuery('.hero_open_icons').attr('data-panel-toggle', 'close');	
	jQuery('.hero_side_icon_panel').animate({
		'right': -300
	}, 400);
			
}

//generate files
function generate_files(global_menu_obj){
	
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'menu_id': global_menu_obj['menu'].menuId,
			'action': 'hmenu_generate'
		},
		dataType: "json"
	}).done(function(data){
		//files generated
	}).fail(function(){
		//console.log(event);
	});
}




















