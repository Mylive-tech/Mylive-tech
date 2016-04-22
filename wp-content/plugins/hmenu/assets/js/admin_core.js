/*
	BACK-END CORE
	notes: loaded on every plugin back-end view (available to this plugin only)
*/

//GLOBALS
//admin core
var menu_object;
var menu_config;
var menu_icon_path = 'assets/icons/';
var save_required = false;
var header_loaded_watch = false;
var header_loaded_timer;
var cur_sub_view;



//CORE EVENT LISTENERS
//load
jQuery(function(){
	maintain_panel_structure(); //maintain panel structure
	load_json_menu_object(); //load the json menu object
	delegate_sidebar_dropdown_menu_animation(); //delegate sidebar dropdown menu animation
	maintain_popup_size_position(); //maintain popup size and position
});
//window resize
jQuery(window).resize(function(){
	maintain_panel_structure(); //maintain panel structure
	maintain_popup_size_position(); //maintain popup size and position
});
//unload
jQuery(window).on('beforeunload', function(){
	return check_save_required();
});



//PANEL
//maintain panel structure
function maintain_panel_structure(){
	//get window dimensions
	var window_width = jQuery(window).width();
	var window_height = jQuery(window).height();
	//get wordpress core dimensions
	var adminmenu_width = jQuery('#adminmenu').width();
	var wpadminbar_height = jQuery('#wpadminbar').height();
	//hero_admin
	jQuery('.hero_admin').css({
		'min-height': (window_height - wpadminbar_height) +'px'
	});
}



//MENU
//load the json menu object
function load_json_menu_object(){
	jQuery.getJSON(plugin_url +'menu/menu_object.js', function(data){
		//attach to global menu object(s)
		menu_object = data.menu.structure;
		menu_config = data.menu.config;
		//check menu config
		check_menu_configuration();
		//iterate the menu object
		iterate_menu_object();
	});
}
//check menu configuration
function check_menu_configuration(){
	//check if development more is active
	if(menu_config.development_mode){
		//auto-generate views based on menu structure
		jQuery.ajax({
			url: ajax_url,
			type: "POST",
			data: {
				'action': plugin_name +'_autoGenerateViews',
				'menu_object': menu_object
			},
			dataType: "json"
		});
	}
}
//iterate the menu object and construct the menu(s)
function iterate_menu_object(){
	var first_item = true;
	jQuery.each(menu_object, function(key,val){
		//add root item to sidebar
		add_root_item_to_sidebar(key,first_item);
		first_item = false;
	});
	//prepopulate sidebar elements
	if(typeof prepopulate_sidebar_elements == 'function'){
		prepopulate_sidebar_elements();
	}
}
//add root item to sidebar
function add_root_item_to_sidebar(key,first_item){
	//get menu item
	var menu_item = menu_object[key];
	//construct menu item html
	var item_html = '';
	switch(menu_item.type){
		//link
		case 'link':
			if(menu_item.show_in_sidebar){
				item_html += '<div id="'+ menu_item.id +'" class="hero_sidebar_item" onclick="load_core_view('+ key +', \''+ menu_item.id +'\', \''+ menu_item.title +'\', \''+ menu_item.viewpath +'\',undefined,undefined, '+ menu_item.header.auto_generate +', '+ menu_item.header.show_save +');">';
					item_html += '<div class="hero_sidebar_parent">';
						item_html += '<div class="hero_sidebar_icon" style="background-image:url('+ plugin_url + menu_icon_path + menu_item.icon +'.png)"></div>';
						item_html += '<div class="hero_sidebar_label">'+ menu_item.title +'</div>';
					item_html += '</div>';
				item_html += '</div>';
			}
		break;
		//dropdown
		case 'dropdown':
			item_html += '<div id="'+ menu_item.id +'" class="hero_sidebar_item" data-visible="hidden">';
				item_html += '<div class="hero_sidebar_parent hero_sidebar_dropdown_item">';
					item_html += '<div class="hero_sidebar_icon" style="background-image:url('+ plugin_url + menu_icon_path + menu_item.icon +'.png)"></div>';
					item_html += '<div class="hero_sidebar_label">'+ menu_item.title +'</div>';
					item_html += '<div class="_dropdown_arrow hero_arrow_open"></div>';
				item_html += '</div>';
				item_html += '<div class="hero_sub">';
					//add submenu items
					jQuery.each(menu_item.submenu, function(key,val){
						switch(val.type){
							//holder
							case 'holder':
								item_html += '<ul class="'+ val.id +'">';
								item_html += '</ul>';
							break;
							//button
							case 'button':
								item_html += '<div class="hero_sidebar_button rounded_3 hero_white" id="'+ val.id +'">';
									item_html += val.title;
								item_html += '</div>';
							break;
						}
					});
				item_html += '</div>';
			item_html += '</div>';
			item_html += '';
			item_html += '';
			item_html += '';
			item_html += '';
		break;
		//button	
		case 'button':
			item_html += '<div class="hero_sidebar_button rounded_3 hero_white" id="'+ menu_item.id +'">';
				item_html += menu_item.title;
			item_html += '</div>';
		break;
	}
	//append sidebar content
	jQuery('.hero_sidebar .hero_sidebar_nav').append(item_html);
	//preselect first item view
	if(first_item){
		load_core_view(key, menu_item.id, menu_item.title, menu_item.viewpath, undefined, undefined, menu_item.header.auto_generate, menu_item.header.show_save);
	}
}
//delegate sidebar dropdown menu animation
function delegate_sidebar_dropdown_menu_animation(){
	jQuery('.hero_main').delegate('.hero_sidebar_dropdown_item', 'click', function(){
		//get height of menu item
		var sidebar_item_height = jQuery('.hero_sidebar_item').height();
		//check if open
		if(jQuery(this).parent().data('visible') == 'hidden'){ //show
			//get content height
			var sub_item_height = jQuery(this).parent().children('.hero_sub').height();
			jQuery(this).parent().stop().animate({
				'height': (sidebar_item_height + sub_item_height) +'px'
			},500, function(){
				jQuery(this).children('.hero_sidebar_parent').children('._dropdown_arrow').removeClass('hero_arrow_open').addClass('hero_arrow_close');
			}).data('visible','visible');
		}else{ //hide
			jQuery(this).parent().stop().animate({
				'height': sidebar_item_height +'px'
			},500, function(){
				jQuery(this).children('.hero_sidebar_parent').children('._dropdown_arrow').removeClass('hero_arrow_close').addClass('hero_arrow_open');
			}).data('visible','hidden');
		}
	});
}



//CORE VIEW
//load core view
var last_loaded_core_view;
function load_core_view(key, id, title, viewpath, json, callback, header, show_save, dropdown_id){	

	//close all dropdown menu(s)
	jQuery('.hero_arrow_close').each(function(){
		if(jQuery(this).parent().parent().attr('id') != dropdown_id){
			jQuery(this).parent().trigger('click');
		}
	});

	//check if loacked
	if(last_loaded_core_view != id){
		//lock core view
		lock_core_view_reload(id);
		var check = check_save_required();
		header_loaded_watch = false;
		var allow_nav = true;
		if(save_required){
			var allow_nav = confirm('Please note that you have unsaved data. If you leave this page, you will lose all changes that took place after your last save. Click OK to leave this page and CANCEL to stay on this page.');
		}
		if(allow_nav){
			cur_sub_view = undefined;
			//reset save required
			remove_save_required();
			//empty core view
			jQuery('.hero_admin').empty();
			//append loader
			jQuery('.hero_admin').append('<div class="loader"><div>loading '+ title.toLowerCase() +' core...</div></div>');
			//set active class
			jQuery('.hero_sidebar_item').removeClass('hero_main_active');
			jQuery('#'+ id).addClass('hero_main_active');
			jQuery('.hero_sub ul li').removeClass('active_sidebar_elem');
			//load core
			jQuery('.hero_admin').load(core_view_path +'views/'+ viewpath +'index.php?p='+ core_view_path +'&v='+ core_view_path +'views/'+ viewpath, function(){
				if(typeof callback !== 'undefined' && callback !== 'undefined' && typeof json !== 'undefined' && json !== 'undefined'){
					eval(""+ callback +"(extract_json_object('"+ json +"'));");
				}
				if(header){
					generate_view_header(key, show_save);
				}
				if(menu_object[key].auto_load_subview){
					load_view_submenu(key,0);
				}
				//switch components
				switch_components();
				//remove loader
				jQuery('.hero_admin .loader').remove();
			});
		}
	}
}
//load core view manually
function manual_load_core_view(id, json_object, callback){
	//get menu key
	var key;
	jQuery.each(menu_object, function(idx,val){
		if(val.id == id){
			key = idx;
			return false;
		}
	});
	//load core view
	load_core_view(key, menu_object[key].id, menu_object[key].title, menu_object[key].viewpath, encodeURIComponent(JSON.stringify(json_object)), callback, menu_object[key].header.auto_generate, menu_object[key].header.show_save);
}
//lock core view reload (reload blocked)
function lock_core_view_reload(id){
	last_loaded_core_view = id;
}
//unlock core view reload (reload allowed)
function unlock_core_view_reload(){
	last_loaded_core_view = undefined;
}
//json extractor
function extract_json_object(json){
	if(json !== 'undefined'){
		return JSON.parse(decodeURIComponent(json));
	}
	return false;
}
//load view manually
function manual_load_view(core_view_id){
	clearTimeout(header_loaded_timer);
	jQuery('.hero_viewport').append('<div class="loader"><div>loading view...</div></div>');
	if(header_loaded_watch){
		//get menu key
		var key;
		jQuery.each(menu_object, function(idx,val){
			if(val.id == core_view_id){
				key = idx;
				return false;
			}
		});
		header_loaded_watch = false;
		load_view_submenu(key,0);
	}else{
		header_loaded_timer = setTimeout(function(){
			manual_load_view(core_view_id);
		},100);
	}
}



//SUB-VIEWS
//load subview
var view_load_lock = false;
function load_sub_view(id, viewpath, view){
	if(view != cur_sub_view){
		cur_sub_view = view;
		//trigger navigation event
		trigger_hplugin_event('view-nav');
		//fade out view
		jQuery('.hero_viewport').fadeOut(100, function(){
			//empty view
			jQuery(this).empty();
			//append loader and fade in
			jQuery('.hero_viewport').append('<div class="loader"><div>loading view...</div></div>').fadeIn(200);
			//load core
			jQuery('.hero_viewport').load(plugin_url +'views/'+ viewpath + view +'.view.php?vp='+ core_view_path +'views/'+ viewpath, function(){
				//switch components if required
				jQuery.each(menu_object, function(key,val){
					if(val.viewpath == viewpath){
						jQuery.each(val.views, function(key,val){
							jQuery.each(val.submenu, function(key,val){
								if(val.auto_load_components){
									switch_components();
									return false;
								}
								return false;	
							});
						});
					}
				});
				//remove loader
				jQuery('.hero_viewport .loader').remove();
			});
			jQuery('#hero_submenu_nav li').removeClass('top_sub_active');
			jQuery('#link_'+ id).addClass('top_sub_active');
		});
	}
}
function reload_sub_view(id, viewpath,view){
	cur_sub_view = view;
	//fade out view
	jQuery('.hero_viewport').fadeOut(100, function(){
		//empty view
		jQuery(this).empty();
		//append loader and fade in
		jQuery('.hero_viewport').append('<div class="loader"><div>loading view...</div></div>').fadeIn(200);
		//load core
		jQuery('.hero_viewport').load(plugin_url +'views/'+ viewpath + view +'.view.php?vp='+ core_view_path +'views/'+ viewpath, function(){
			//switch components if required
			jQuery.each(menu_object, function(key,val){
				if(val.viewpath == viewpath){
					jQuery.each(val.views, function(key,val){
						jQuery.each(val.submenu, function(key,val){
							if(val.auto_load_components){
								switch_components();
								return false;
							}
							return false;	
						});
					});
				}
			});
			//remove loader
			jQuery('.hero_viewport .loader').remove();
		});
		jQuery('#hero_submenu_nav li').removeClass('top_sub_active');
		jQuery('#link_'+ id).addClass('top_sub_active');
	});
}



//HEADERS
//generate view header
function generate_view_header(key, show_save){
	header_loaded_watch = false;
	var header_html = '';
	header_html += '<div class="hero_top">';
		header_html += '<div class="hero_top_menu">';
		jQuery.each(menu_object[key].views, function(idx,val){
			header_html += '<div class="hero_top_main" data-view="label" id="'+ val.id +'_btn" onclick="load_view_submenu('+ key +', '+ idx +');">'; //menu here -> hero_top_active
				header_html += '<div class="hero_top_icon" style="background-image:url('+ plugin_url +'assets/icons/'+ val.icon +'.png)"></div>';
				header_html += '<div class="hero_top_label">'+ val.title +'</div>';
				header_html += '<div class="hero_active_arrow"></div>';
			header_html += '</div>';
			first_item = false;
		});
		header_html += '</div>';
		header_html += '<div class="hero_top_info">';
			header_html += '<div class="hero_dark size_12" id="hero_header_label"></div>';
			header_html += '<div class="hero_white size_20" id="hero_header_title"></div>';
		header_html += '</div>';
		header_html += '<div class="hero_top_status">';
		if(show_save){
			var disabled = 'hero_btn_disable';
			if(save_required){
				disabled = '';
			}
			header_html += '<div class="hero_button rounded_3 '+ disabled +' save_button">SAVE</div>';
		}
		header_html += '</div>';
	header_html += '</div>';
	header_html += '<div class="hero_top_sub_nav size_12 hero_white">';
		header_html += '<ul id="hero_submenu_nav">';
		header_html += '</ul>';
	header_html += '</div>';
	jQuery('.hero_viewport').before(header_html);
	header_loaded_watch = true;
	set_current_header_label(menu_object[key].header.header_label, menu_object[key].header.header_title);
}
//set header label and title
function set_current_header_label(label, title){
	jQuery('#hero_header_label').html(label.toUpperCase());
	jQuery('#hero_header_title').html(title.toUpperCase());
}
//load view submenu
function load_view_submenu(key,idx){
	jQuery('#hero_submenu_nav').empty();
	var first_item = true;
	//remove active state from current
	jQuery('.hero_top_main').removeClass('hero_top_active');
	jQuery.each(menu_object[key].views[idx].submenu, function(index,val){
		jQuery('#hero_submenu_nav').append('<li id="link_'+ val.id +'" onclick="load_sub_view(\''+ val.id +'\', \''+ menu_object[key].viewpath +'\',\''+ val.view +'\');">'+ val.title +'</li>');
		if(first_item){
			//load first view
			load_sub_view(val.id, menu_object[key].viewpath +'',val.view);
		}
		first_item = false;
	});
	jQuery('#'+ menu_object[key].views[idx].id+'_btn').addClass('hero_top_active');
}



//SIDEBAR
//add sidbar element
function add_sidebar_element(dropdown_id, elem_id, title, json, callback){
	jQuery('#'+ dropdown_id +' .hero_sub ul').append('<li id="sub_item_row_'+ elem_id +'" data-json="'+ encodeURIComponent(JSON.stringify(json)) +'" onclick="load_sidebar_dropdown_view(jQuery(this),\''+ dropdown_id +'\',\''+ callback +'\');">'+ title +'</li>');
}
//load sidebar dropdown view
function load_sidebar_dropdown_view(id, dropdown_id, callback){
	//open dropdown menu
	if(jQuery('#'+ dropdown_id).children('.hero_sidebar_parent').children('._dropdown_arrow').hasClass('hero_arrow_open')){
		jQuery('#'+ dropdown_id).children('.hero_sidebar_parent').trigger('click');
	}
	//get menu key
	var key;
	jQuery.each(menu_object, function(idx,val){
		if(val.id == dropdown_id){
			key = idx;
			return false;
		}
	});
	//load core view
	load_core_view(key, menu_object[key].id, menu_object[key].title, menu_object[key].viewpath, id.data('json'), callback, menu_object[key].header.auto_generate, menu_object[key].header.show_save, dropdown_id);
}



//SAVE MANAGEMENT
//check save required
function check_save_required(){
	if(save_required){
		return 'Please note that you have unsaved data. If you leave this page, you will lose all changes that took place after your last save.';
	}
}
//flag save required
function flag_save_required(callback, json){
	json = encodeURIComponent(JSON.stringify(json));
	//flag required
	save_required = true;
	//update button to red
	jQuery('.save_button').removeClass('hero_btn_disable');
	//reset save delegate
	jQuery('.hero_admin').off('click','.save_button');
	//bind event listener
	jQuery('.hero_admin').on('click','.save_button', function(){
		remove_save_required();
		if(typeof callback !== 'undefined'){
			eval(""+ callback +"(extract_json_object('"+ json +"'));");
		}
	});
}
//remove save required
function remove_save_required(){
	//flag not required
	save_required = false;
	//reset save delegate
	jQuery('.hero_admin').off('click','.save_button');
	//update button to red
	jQuery('.save_button').addClass('hero_btn_disable');
}



//CORE CONSOLE LOGGING WRAPPER
//console.log core replacement
function console_log(msg){
	if(menu_config.development_mode){
		console.log(msg);
	}
}



//CORE MESSAGING SYSTEM
//show message
var message_id = 0;
function show_message(type, title, message){
	message_id++;
	var message_html = '<div id="hero_message_'+ message_id +'" class="hero_'+ type +' rounded_3">';
    		message_html += '<h5 class="size_14">'+ title +'</h5>';
       		message_html += '<span class="size_12">'+ message +'</span>';
	    message_html += '</div>';
	jQuery('.hero_message_status').append(message_html);
	jQuery('#hero_message_'+ message_id).animate({
		'opacity': 1,
		'margin-bottom': 10 +'px'
	},700, function(){
		jQuery(this).delay(4000).animate({
			'opacity': 0,
			'margin-bottom': 20 +'px'
		},700, function(){
			jQuery(this).remove();
		});
	});
}



//CORE POPUP MANAGEMENT
//maintain popup size and position
function maintain_popup_size_position(){ 
	var hero_width = jQuery(window).width();
	var hero_height = jQuery(window).height();
	var popup_resize_height = (hero_height - 200);
	var popup_inner_height = (popup_resize_height - 50);
	var popup_top_margin_offset = (popup_inner_height / 2);
	jQuery('.hero_popup_resize').css({
		'height': popup_resize_height +'px',
		'margin-top': '-'+ popup_top_margin_offset +'px'
	});
	jQuery('.hero_popup_inner').css({
		'height': popup_inner_height +'px'
	});
	jQuery('.hero_popup_main').css({
		'height': hero_height +'px'
	});
}
//launch popup
function launch_hero_popup(path_to_html,load_method,update_method,cancel_method,json){
	//clean JSON
	json = encodeURIComponent(JSON.stringify(json));
	//load content
	jQuery('.hero_popup_inner').load(core_view_path +'views/'+ path_to_html, function(){
		//call load method if set
		if(load_method != null){
			eval(""+ load_method +"(extract_json_object('"+ json +"'));");
		}
		//bind update method
		jQuery('.hero_popup_update_btn').off().on('click', function(){
			if(update_method != null){
				eval(""+ update_method +"(extract_json_object('"+ json +"'));");
			}
			hide_hero_popup();
		});
		//bind cancel method
		jQuery('.hero_popup_cancel_btn').off().on('click', function(){
			if(cancel_method != null){
				eval(""+ cancel_method +"(extract_json_object('"+ json +"'));");
			}
			hide_hero_popup();
		});
		//re-call component binding (component_manager.js)
		bind_field_convert();
		//show popup
		show_hero_popup();
	});
}
//show popup
function show_hero_popup(){
	jQuery('.hero_popup_main').fadeIn(300);
}
//hide popup
function hide_hero_popup(){
	jQuery('.hero_popup_main').fadeOut(300, function(){
		jQuery('.hero_popup_inner').empty();
		jQuery('.hero_popup_update_btn').off();
		jQuery('.hero_popup_cancel_btn').off();
	});
}



//CORE CUSTOM EVENT SYSTEM
//trigger system-wide events that can be bound to
function trigger_hplugin_event(evt){
	jQuery('.hero_viewport').trigger(evt);
}
//event subscribe
function hplugin_event_subscribe(evt,callback,json){
	//clean JSON
	json = encodeURIComponent(JSON.stringify(json));
	jQuery('.hero_viewport').on(evt, function(){
		eval(""+ callback +"(extract_json_object('"+ json +"'));");
	});
}
//event subscribe once
function hplugin_event_subscribe_once(evt,callback,json){
	//clean JSON
	json = encodeURIComponent(JSON.stringify(json));
	jQuery('.hero_viewport').on(evt, function(){
		hplugin_event_unsubscribe(evt);
		eval(""+ callback +"(extract_json_object('"+ json +"'));");
	});
}
//event unsubscribe
function hplugin_event_unsubscribe(evt){
	jQuery('.hero_viewport').off(evt);
}


//LOAD IFRAME SECURELY
//load iframe
var iframe_src;
var iframe_height;
var iframe_container;
function load_secure_iframe(src, height, container){
	iframe_src = src;
	iframe_height = height;
	iframe_container = container;
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': plugin_name + '_get_security_code'
		},
		dataType: "json"
	}).done(function(token){
		//load iframe
		jQuery(container).empty().append('<iframe frameborder="0" height="'+ height +'" width="100%" scrolling="no" src="'+ plugin_url + src +'?st='+ token +'"></iframe>');
	});
}
//show security tag timeout error
function show_security_tag_timeout_error(){
	if(typeof iframe_src !== 'undefined' && typeof iframe_height !== 'undefined' && typeof iframe_container !== 'undefined'){
		load_secure_iframe(iframe_src, iframe_height, iframe_container);
	}
	show_message("error", "Security Token", "The security token has timed out. Please try again.");
}


//CONVERTERS
//hex to rgb
function hexToRgb(hex){
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? parseInt(result[1], 16) +','+ parseInt(result[2], 16) +',' + parseInt(result[3], 16) : null;
}