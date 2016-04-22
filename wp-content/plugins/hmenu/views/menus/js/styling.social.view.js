//STYLING.SOCIAL VIEW

//load
jQuery(function(){
	//functions
	execute_font_load('social');
	add_more();
	enable_sorting();
	get_social_items();
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
});

//add more button
function add_more(){
	//load core view
	jQuery('#add_more_btn').on('click', function(){	
		reload_sub_view('menu_sub_icons', 'menus/','styling.icons');
	});
}

//load icon messages
function load_icon_message(msg){
	jQuery('.icons_load_here').html(msg);
}

//load icons click
function load_icons_click(){
	var the_options = jQuery('.icon_social_set_select .hero_dropdown .hero_drop_row');
	jQuery(the_options).each(function(index, element) {
        jQuery(this).on('click', function(){
			load_icons(jQuery(this).data('value'), 'social');
		});
    });
}

//load select
function load_select(obj){
	
	var select_html = ''
	
	jQuery(obj).each(function(index, element) {	
		//build styles
        select_html += '<option value="'+index+'">'+element.fontPackName+'</option>';
    });
	
	jQuery('#icon_social_set_select').append(select_html);
	
	update_select_component(jQuery('#icon_social_set_select'));
	
}

//sorting
function enable_sorting(){
	//sort
	jQuery(".hero_icon_sort_holder").sortable({
		placeholder: "list_placeholder",
		revert: false,	
		forcePlaceholderSize: true,		
		handle: '.hero_item_drag',
		stop: function(){
			set_order();
		}
	});	
}

//set order
function set_order(){
	//set positions of new order
	jQuery('.hero_list_sort_item').each(function(index, element) {
		var the_index = jQuery(this).data('index');
		global_menu_obj.social_items[the_index].order = index;
    });
	flag_save_required('save_clicked',{"status_social": true});
}

//inser new menu
function insert_menu_icon(){
	
	//add menu click
	jQuery('.icon_item').on('dblclick', function(){
		var icon_data_class = jQuery(this).data('class');
		
		if(jQuery('#'+icon_data_class).length > 0){
			show_message('error', 'Error Message', 'The social icon you want to add already exists within the selected social icons.');
		} else {
			//social json
			var the_length = global_menu_obj.social_items.length;
			json_item = '{"menuId":'+global_menu_obj.menu.menuId+', "name":"Social Heading", "icon":1, "iconContent":"'+icon_data_class+'", "iconSize":"small", "iconColor":"#888888", "iconHoverColor":"#DDDDDD", "link":"#", "target":"_blank", "new":1, "deleted":0, "order":'+the_length+'}';
			set_order();	
			var new_social_item = JSON.parse(json_item);
			global_menu_obj.social_items.push(new_social_item);
			var the_new_index = global_menu_obj.social_items.length - 1;
			preload_social_html(the_new_index, icon_data_class);			
			flag_save_required('save_clicked',{"status_social": true});
		}			
	});			
}

//sort social items
function sort_items_array( a, b ) {
    return a.order - b.order;
}

//get social items
function get_social_items(){
	//console
	global_menu_obj.social_items.sort(sort_items_array);
	jQuery(global_menu_obj.social_items).each(function(index, element) { 	
		if(element.deleted != 1){			
			preload_social_html(index, element.iconContent);
		}
    });
}

//preload html
function preload_social_html(index, icon){
	
	//get social html     
	the_url = plugin_url + 'views/menus/html_snippets/social_item.php' 
	
	//load the html
	jQuery.ajax({
		url: the_url,
		data: {
			index: index,
			url: plugin_url,
			class: icon
		},
        async: false,
		dataType: "html"
	}).done(function(data){	
		//append html
		jQuery('.hero_icon_sort_holder').append(data);		
		enable(index);
	}).fail(function(){
		 //page error
	});		
}

//enable
function enable(index){
	enable_toggle();
	enable_delete();
	set_social_data(index);
}

//setup social data
function set_social_data(index){
		
	jQuery('#social_heading_'+index).html(global_menu_obj.social_items[index].name);
	jQuery('#social_name_'+index).val(global_menu_obj.social_items[index].name);
	jQuery('#social_url_'+index).val(global_menu_obj.social_items[index].link);
	jQuery('#social_item_order_'+index).val(global_menu_obj.social_items[index].order);
	
	jQuery('#social_target_'+index+' option').each(function(idx, el) {
	   if(jQuery(this).val() == global_menu_obj.social_items[index].target){
		   jQuery(this).attr('selected', 'selected')
	   }
	});		
	jQuery('#social_icon_content_'+index).val(global_menu_obj.social_items[index].iconContent);
	jQuery('.hero_social_icon_display_'+index).children('#inner_icon').attr('class', global_menu_obj.social_items[index].iconContent);
	jQuery('#social_icon_size_'+index+' option').each(function(idx, el) {
	   if(jQuery(this).val() == global_menu_obj.social_items[index].iconSize){
		   jQuery(this).attr('selected', 'selected')
	   }
	});
	jQuery('#social_icon_color_'+index).val(global_menu_obj.social_items[index].iconColor);
	jQuery('#social_icon_hover_color_'+index).val(global_menu_obj.social_items[index].iconHoverColor);
		
	
	//switch components
	switch_components();
	
	//enable update
	enable_update_settings(index);
	
}

//enable update settings
function enable_update_settings(index){
	
	//change: name
	jQuery('#social_name_'+index).on('change keyup', function(){
		global_menu_obj.social_items[index].name = jQuery(this).val();
		jQuery('#social_heading_'+index).html(global_menu_obj.social_items[index].name);
		flag_save_required('save_clicked',{"status_social": true});
	});
	//change: url
	jQuery('#social_url_'+index).on('change keyup', function(){
		global_menu_obj.social_items[index].link = jQuery(this).val();
		flag_save_required('save_clicked',{"status_social": true});
	});
	//change: target
	jQuery('.social_target_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery('#social_target_'+index).trigger('change');		
	});	
	jQuery('#social_target_'+index).on('change', function(){
		global_menu_obj.social_items[index].target = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked',{"status_social": true});
	});		
	//change: icon content
	jQuery('#social_icon_content_'+index).on('change keyup', function(){
		global_menu_obj.social_items[index].iconContent = jQuery(this).val();		
		flag_save_required('save_clicked',{"status_social": true});
	});
	//change: icon size
	jQuery('.social_icon_size_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery('#social_icon_size_'+index).trigger('change');		
	});	
	jQuery('#social_icon_size_'+index).on('change', function(){
		global_menu_obj.social_items[index].iconSize = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked',{"status_social": true});
	});	
	//change: icon color
	jQuery('#social_icon_color_'+index).on('change keyup', function(){
		global_menu_obj.social_items[index].iconColor = jQuery(this).val();
		flag_save_required('save_clicked',{"status_social": true});
	});
	//change: icon hover color
	jQuery('#social_icon_hover_color_'+index).on('change keyup', function(){
		global_menu_obj.social_items[index].iconHoverColor = jQuery(this).val();
		flag_save_required('save_clicked',{"status_social": true});
	});
	
}

function enable_delete(){
	
	//get the delete click
	jQuery('.hero_button_delete').off().on('click', function(){
		
		//current item data		
		var current_index = jQuery(this).data('index');
		
		if(window.confirm('Are you sure you want to delete the social item?')){
			
			//set object
			global_menu_obj.social_items[current_index].deleted = 1;
			jQuery(this).parents('.hero_list_sort_item').remove();
			flag_save_required('save_clicked',{"status_social": true});
		
		} else {
			
		}
		
	});
}


//toggle items
function enable_toggle(){
	
	jQuery('.hero_item_bar').children('.hero_item_toggle ').off().on('click', function(){
		
		//close the icon panel
		disable_icon_select();
		
		var count_open = 0;
		
		var the_item_height = jQuery(this).parents('.hero_item_wrap').children('.hero_item_content').height() + 55;
		
		if(!jQuery(this).attr('data-nav-toggle') || jQuery(this).attr('data-nav-toggle') == 'close'){
			//check which ones are open
			jQuery('.hero_item_toggle ').each(function(index, element) {
				if(jQuery(this).attr('data-nav-toggle') == 'open'){
					//close	
					jQuery(this).attr('data-nav-toggle', 'close');
					jQuery(this).removeClass('hero_menu_open');
					//close
					jQuery(this).parents('.hero_item_wrap').css({
						'display': 'block',
						'overflow': 'hidden'
					});	
					jQuery(this).parents('.hero_item_wrap').animate({
						'height': '40px'
					});
					count_open++;
				}			
			});			
			jQuery(this).attr('data-nav-toggle', 'open');
			jQuery(this).addClass('hero_menu_open');
			//open	
			jQuery(this).parents('.hero_item_wrap').animate({
				'height': the_item_height + 'px'
			}, function(){
				jQuery(this).css({
					'display': 'table',
					'overflow': 'visible',
					'height': 'auto'
				});
			});		
			
		} else if(jQuery(this).attr('data-nav-toggle') == 'open'){
			jQuery(this).attr('data-nav-toggle', 'close');
			jQuery(this).removeClass('hero_menu_open');
			//close
			jQuery(this).parents('.hero_item_wrap').css({
				'display': 'block',
				'overflow': 'hidden'
			});	
			jQuery(this).parents('.hero_item_wrap').animate({
				'height': '40px'
			});
		}
		
	});	
	
	//edit click
	jQuery('.hero_button_edit').off().on('click', function(){
		jQuery(this).parents('.hero_item_bar').children('.hero_item_toggle').trigger('click');
	});
	
	//title click
	jQuery('.hero_item_heading').off().on('click', function(){
		jQuery(this).parents('.hero_item_bar').children('.hero_item_toggle').trigger('click');
	});
	
}