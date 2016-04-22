//LAYOUT.STRUCTURE VIEW

var global_mega_html;
var global_menu_html;

//load
jQuery(function(){
	enable_toggle();
	enable_sorting();
	get_nav();
	bind_nav_item_listener();
	get_pages();	
	execute_global_font_load();
	enable_icon_select();
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
});

//bind listeners
function bind_nav_item_listener(){
	//mega
	jQuery('.add_mega_to_navigation').bind('click', function(){
		insert_mega_menu();
	});	
	jQuery("#mega_menu_form input").keypress(function(event) {
		if (event.which == 13) {
			event.preventDefault();
			insert_mega_menu();
		}
	});	
	//custom
	jQuery('.add_custom_to_navigation').bind('click', function(){
		insert_custom_menu();
	});	
	jQuery("#custom_menu_form input").keypress(function(event) {
		if (event.which == 13) {
			event.preventDefault();
			insert_custom_menu();
		}
	});	
	//custom
	jQuery('.add_custom_method_to_navigation').bind('click', function(){
		insert_custom_method_menu();
	});	
	jQuery("#custom_method_menu_form input").keypress(function(event) {
		if (event.which == 13) {
			event.preventDefault();
			insert_custom_method_menu();
		}
	});	
}

//insert mega menu
function insert_mega_menu(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_load_validate_mega',
			'form_data': jQuery('#mega_menu_form').serialize()
		},
		dataType: "json"
	}).done(function(data){
		if(data.status){
			show_message('success', 'Mega Item Added', 'Your mega menu item was added successfully.');
			insert_menu(data.object.name, 'mega', '');
			//clear input
			jQuery('#mega_menu_name').val('');
			jQuery('#mega_menu_name').removeClass('has-error');
	  	}else{
			//highlight errors
			jQuery.each(data.object, function(index,value){
				if(!value){
					jQuery('#'+ index).addClass('has-error');
				} else {
					jQuery('#'+ index).removeClass('has-error');
				}
			})
			show_message('error', 'Error', 'Your menu was not added.');
		}
	}).fail(function(){
		//page error
	});
}

//insert custom menu
function insert_custom_menu(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_load_validate_custom',
			'form_data': jQuery('#custom_menu_form').serialize()
		},
		dataType: "json"
	}).done(function(data){
		if(data.status){
			show_message('success', 'Custom Item Added', 'Your custom link was successfully added.');
			insert_menu(data.object.name, 'custom', '', jQuery('#custom_url').val());
			//clear input
			jQuery('#custom_menu_form input').val('');
			jQuery('#custom_menu_form input').removeClass('has-error');
	  	}else{
			//highlight errors
			jQuery.each(data.object, function(index,value){
				if(!value){
					jQuery('#'+ index).addClass('has-error');
				} else {
					jQuery('#'+ index).removeClass('has-error');
				}
			})
			show_message('error', 'Error', 'Your custom link was not added.');
		}
	}).fail(function(){
		//page error
	});
}

//insert custom method menu
function insert_custom_method_menu(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_load_validate_custom_method',
			'form_data': jQuery('#custom_method_menu_form').serialize()
		},
		dataType: "json"
	}).done(function(data){
		if(data.status){
			show_message('success', 'Custom Item Added', 'Your custom link was successfully added.');
			insert_menu(data.object.name, 'method', '', jQuery('#custom_method').val());
			//clear input
			jQuery('#custom_method_menu_form input').val('');
			jQuery('#custom_method_menu_form input').removeClass('has-error');
	  	}else{
			//highlight errors
			jQuery.each(data.object, function(index,value){
				if(!value){
					jQuery('#'+ index).addClass('has-error');
				} else {
					jQuery('#'+ index).removeClass('has-error');
				}
			})
			show_message('error', 'Error', 'Your custom link was not added.');
		}
	}).fail(function(){
		//page error
	});
}

//list item global object
var global_list_item_data = [];

var global_categories;

//get pages
function get_pages(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_load_pages'
		},
		dataType: "json"
	}).done(function(data){
		load_pages(data.pages);
		load_categories(data.categories);
		global_categories = data.categories;
		enable_add_button();			
		//switch components
		switch_components();
		//set list item data
		global_list_item_data = data;
		jQuery('.trigger_pages').trigger('click');
	}).fail(function(){
		//error
	});
}

//load pages
function load_pages(data){
	var page_rows = '';
	jQuery(data).each(function(index, element) {
        page_rows += '<div class="nav_item_row" data-id="">'; 
			page_rows += '<div class="nav_check">';
				page_rows += '<input type="checkbox" data-type="tickbox" data-size="sml" id="nav_page_'+index+'" data-nav-type="basic" data-nav-name="'+element.title+'" name="nav_page_'+index+'" value="'+element.id+'">';
			page_rows += '</div> ';
			page_rows += '<div class="nav_label">';
				page_rows += '<label for="nav_page_'+index+'" class="size_11">'+element.title+'</label>';
			page_rows += '</div>';
		page_rows += '</div>';
    });	
	jQuery('#nav_pages ' + '.load_nav_misc').html(page_rows);	
}

//load pages
function load_categories(data){
	var page_rows = '';
	jQuery(data).each(function(index, element) {
        page_rows += '<div class="nav_item_row" data-id="">'; 
			page_rows += '<div class="nav_check">';
				page_rows += '<input type="checkbox" data-type="tickbox" data-size="sml" id="nav_cat_'+index+'" data-nav-type="category" data-nav-taxonomy="'+element.taxonomy+'" data-nav-name="'+element.title+'" name="nav_cat_'+index+'" value="'+element.id+'">';
			page_rows += '</div>';
			page_rows += '<div class="nav_label">';
				page_rows += '<label for="nav_cat_'+index+'" class="size_11">'+element.title+'</label>';
			page_rows += '</div>';
		page_rows += '</div>';
    });	
	jQuery('#nav_categories ' + '.load_nav_misc').html(page_rows);	
}

//load post type and categories
function load_post_types(data){
	var page_block = '';
	jQuery(data).each(function(index, element) {
		page_block += '<li class="hero_list_sort_item">';
			page_block += '<div class="hero_item_wrap">';
				page_block += '<div class="hero_item_bar hero_bar_grey">';
					page_block += '<div class="hero_item_toggle" data-nav-toggle="close"></div>';
					page_block += '<div class="hero_item_heading size_14 hero_white">'+element.label+'</div>';					
				page_block += '</div>';
				page_block += '<div class="hero_col_12 hero_item_content">';
					page_block += '<div class="hero_sub">';
						page_block += '<div class="hero_sidebar_content">';
							page_block += '<div class="load_nav_misc">';
								page_block += '<!-- LOAD POST TYPE POSTS -->';
								page_block += load_post_type_posts(element.posts);
							page_block += '</div>';
							page_block += '<div class="hero_sidebar_button add_to_navigation rounded_3 hero_white">Add to menu</div>';
						page_block += '</div>';
					page_block += '</div>';
				page_block += '</div>';
			page_block += '</div>';
		page_block += '</li>';
		
		page_block += load_type_categories(element.type_categories)
		
    });	
	
	jQuery('.hero_sidebar_list').append(page_block);
	
	enable_toggle();
	
}

//load post types
function load_post_type_posts(data){
	var html_row = '';
	jQuery(data).each(function(index, element) {
        html_row += '<div class="nav_item_row" data-id="">'; 
			html_row += '<div class="nav_check">';
				html_row += '<input type="checkbox" data-type="tickbox" data-size="sml" id="nav_post_type_'+element.post_type+index+'" data-nav-type="basic" data-nav-name="'+element.post_title+'" name="nav_post_type_'+index+'" value="'+element.ID+'">';
			html_row += '</div> ';
			html_row += '<div class="nav_label">';
				html_row += '<label for="nav_post_type_'+index+'" class="size_11">'+element.post_title+'</label>';
			html_row += '</div>';
		html_row += '</div>';
    });	
	return(html_row);	
}

//load post type categories
function load_type_categories(data){
	var page_block = '';
	jQuery(data).each(function(index, element) {
		
		page_block += '<li class="hero_list_sort_item">';
			page_block += '<div class="hero_item_wrap">';
				page_block += '<div class="hero_item_bar hero_bar_grey">';
					page_block += '<div class="hero_item_toggle" data-nav-toggle="close"></div>';
					page_block += '<div class="hero_item_heading size_14 hero_white">'+element.label+'</div>';					
				page_block += '</div>';
				page_block += '<div class="hero_col_12 hero_item_content">';
					page_block += '<div class="hero_sub">';
						page_block += '<div class="hero_sidebar_content">';
							page_block += '<div class="load_nav_misc">';
								page_block += '<!-- LOAD POST TYPE POSTS -->';
								page_block += load_post_type_terms(element.terms);
							page_block += '</div>';
							page_block += '<div class="hero_sidebar_button add_to_navigation rounded_3 hero_white">Add to menu</div>';
						page_block += '</div>';
					page_block += '</div>';
				page_block += '</div>';
			page_block += '</div>';
		page_block += '</li>';
		
    });
	return(page_block);	
}

//load post type terms
function load_post_type_terms(data){
	var html_row = '';
	jQuery(data).each(function(index, element) {
        html_row += '<div class="nav_item_row" data-id="">'; 
			html_row += '<div class="nav_check">';
				html_row += '<input type="checkbox" data-type="tickbox" data-size="sml" id="cat_'+element.slug+'" data-nav-type="category" data-nav-taxonomy="'+element.taxonomy+'" data-nav-name="'+element.name+'" name="cat_'+element.slug+'" value="'+element.term_id+'">';
			html_row += '</div> ';
			html_row += '<div class="nav_label">';
				html_row += '<label for="cat_'+element.slug+'" class="size_11">'+element.name+'</label>';
			html_row += '</div>';
		html_row += '</div>';
    });	
	return(html_row);	
}

//enable add button
function enable_add_button(){
	jQuery('.add_to_navigation').off().on('click', function(){
		jQuery(this).parent('.hero_sidebar_content').children('.load_nav_misc').find('input').each(function(index, element) {
            if(jQuery(this).is(':checked')){				
				insert_menu(jQuery(this).data('nav-name'), jQuery(this).data('nav-type'), jQuery(this).val());
				jQuery(this).trigger('click');
			} else {
				//nothing
			}
        });
	});
}

//inser new menu
function insert_menu(name, type, post_id, url){			
		
		var data_index = global_menu_obj.nav_items.length;
		
		//setup default array for new item	
		var json_item = '{"title":"", "postId":"'+post_id+'", "icon":"0", "iconContent":"e98e", "iconColor":"#888888", "iconSize":"medium", "active":"1", "activeMobile":"0", "level":"0", "link":"'+url+'", "name":"'+name+'", "new":"1", "status":"0", "parentNavId":"0", "type":"'+type+'", "target":"_self", "deleted":"0", "order":"'+data_index+'", "mega_menus": [], "method":"0", "methodReference":"'+url+'"}';
		var new_menu_item = JSON.parse(json_item);	
		
		//push items into global object
		global_menu_obj.nav_items.push(new_menu_item);
		
		//if mega
		if(type == 'mega'){
			var json_mega_item = '{"name":"'+name+'", "layout":"4,4,4", "background":"0", "backgroundUrl":"", "backgroundPosition":"bottom right", "navItemId":"", "new":1, "status":0, "mega_stuff": [], "deleted_items": []}';
			var new_menu_mega_item = JSON.parse(json_mega_item);				
			global_menu_obj.nav_items[data_index].mega_menus.push(new_menu_mega_item);
		}
		
		//random data
		var id = Math.floor(((10000 * Math.random()) * 10000) + 1);
		
		//preload html
		preload_html(type, data_index, id, 0, 0);	
		
		//check items
		check_items();	
		
		//flagsave
		flag_save_required('save_clicked',{"status": true});		
		
		//refresh
		jQuery( ".sort" ).sortable( "refresh" );
	
}

//toggle items
function enable_toggle(){
	
	jQuery('.hero_accordion').each(function(index, element) {
		
		var the_primay_acc = jQuery(this);
        
		jQuery(this).children('li').children('.hero_item_wrap').children('.hero_item_bar').children('.hero_item_toggle ').off().on('click', function(){
			
			//close the icon panel
			disable_icon_select();
			
			var count_open = 0;
			
			var the_item_height = jQuery(this).parent('.hero_item_bar').parent('.hero_item_wrap').children('.hero_item_content').height() + 45;
			
			if(!jQuery(this).attr('data-nav-toggle') || jQuery(this).attr('data-nav-toggle') == 'close'){
				//check which ones are open
				jQuery(the_primay_acc).find(".hero_item_toggle").each(function(index, element) {
                   if(jQuery(this).attr('data-nav-toggle') == 'open'){
						//close	
						jQuery(this).attr('data-nav-toggle', 'close');
						jQuery(this).removeClass('hero_menu_open');
						//close
						jQuery(this).parent('.hero_item_bar').parent('.hero_item_wrap').css({
							'display': 'block',
							'overflow': 'hidden'
						});	
						jQuery(this).parent('.hero_item_bar').parent('.hero_item_wrap').animate({
							'height': '40px'
						}, 200);
						count_open++;
					}	
                });		
				jQuery(this).attr('data-nav-toggle', 'open');
				jQuery(this).addClass('hero_menu_open');
				//open	
				jQuery(this).parent('.hero_item_bar').parent('.hero_item_wrap').animate({
					'height': the_item_height + 'px'
				}, 200, function(){
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
				jQuery(this).parent('.hero_item_bar').parent('.hero_item_wrap').css({
					'display': 'block',
					'overflow': 'hidden'
				});	
				jQuery(this).parent('.hero_item_bar').parent('.hero_item_wrap').animate({
					'height': '40px'
				}, 200);
			}
			
		});	
		
    });
	
	//edit button click
	jQuery('.hero_button_edit').off().on('click', function(){
		jQuery(this).parents('.hero_item_bar').children('.hero_item_toggle').trigger('click');
	});
	
	//title click
	jQuery('.hero_item_heading').off().on('click', function(){
		jQuery(this).parents('.hero_item_bar').children('.hero_item_toggle').trigger('click');
	});
	
}

//sorting
// use handle: '.handle' to identify dragable element
//global variables
var prev_data_lvl;
var prev_data_id;
var prev_data_parent;

var next_data_lvl;
var next_data_id;
var next_data_parent;

var prev_allow_sub;

var new_level = 0;

var current_placement;
var start_placement;
var diff;

//cancel: ".hero_mega_menu",
//sorting
function enable_sorting(){
	
	var depth_limit = 5;
	
	var elements_array = [];
	var clean_array = [];	
	
	//offset fix for sortable items
	var minus_by;
	jQuery(window).scroll(function(){
		minus_by = jQuery(document).scrollTop() - (jQuery(document).scrollTop() * 2) + 20;
		jQuery( ".sort" ).sortable( { cursorAt: { top:minus_by } } );
	})
	
	jQuery(".sort").sortable({
		placeholder: "placeholder",
		revert: false,	
		forcePlaceholderSize: true,	
		items: "li:not(.ui-state-disabled)",
		cancel: ".not_sortable",
		cursorAt: { top:minus_by },
		start: function(event, ui){
			
			jQuery( ".sort" ).sortable( "refresh" );
			
			//offset fix for sortable items
			jQuery(window).scroll(function(){
				minus_by = jQuery(document).scrollTop() - (jQuery(document).scrollTop() * 2) + 20;
				jQuery( ".sort" ).sortable( { cursorAt: { top:minus_by } } );
			})
			minus_by = jQuery(document).scrollTop() - (jQuery(document).scrollTop() * 2) + 20;
			jQuery( ".sort" ).sortable( { cursorAt: { top:minus_by } } );
			
			jQuery( ".sort" ).sortable( "refresh" );
			
			//Check to see if there are any transfer items and bring them with
			get_transfer_items(jQuery(ui.item).attr('data-id'));
			start_placement = jQuery(ui.item).attr('data-level')
			
			jQuery( ".sort" ).sortable( "refresh" );
						
			//if currently dragging MEGA MENU - mega menu can only be a parent, it can never be a sub of anything ever, unless we make changes to layout CSS/JQUERY!
			if(jQuery(ui.item).attr('data-menu-type') == 'mega'){
				
				//add a group tag to all elements
				var count = 0;
				
				jQuery('.hero_sort_item').each(function(index, element) {
					//add not sortable to parent if has sub
					if(jQuery(this).next().attr('data-level') > 0){
						jQuery(this).addClass('ui-state-disabled');					
					}					
					//make all sub not sortable
					if(jQuery(this).attr('data-level') > 0){
						jQuery(this).addClass('ui-state-disabled');
					} 
					
				});
				
				jQuery('.hero_sort_item').each(function(index, element) {
					
					if(jQuery(this).attr('data-level')==0 && jQuery(this).next().attr('data-level') != 0 && typeof(jQuery(this).next().attr('data-level')) != 'undefined'){
						count++;
						jQuery(this).attr('data-group-val', 'group_'+count);
					}
					
					if(jQuery(this).attr('data-level')!=0){
						jQuery(this).attr('data-group-val', 'group_'+count);
					}
													
				});
				
				jQuery('.hero_sort_item').each(function(index, element) {
					if(jQuery(this).attr('data-group-val')){
						elements_array.push(jQuery(this).attr('data-group-val'));
					}
				});
				
				clean_array = jQuery.unique(elements_array);
				
				//wrap the groups
				jQuery(clean_array).each(function(index, element) {
					jQuery("ul").find("[data-group-val='"+element+"']").wrapAll( "<li class='hero_group_item' />");
				});
				
			}
			
		},
		handle: '.hero_item_drag',
		sort: function(event, ui){	
		
			jQuery( ".sort" ).sortable( "refresh" );	
			
			//previous div
			var prev_div = jQuery('.placeholder').prev();
						
			//next div
			var next_div = jQuery('.placeholder').next();
			
			//current magrin left
			var current_margin_left = jQuery('.ui-sortable-helper').css('margin-left');
			
			//item being dragged position
			var current_position =  jQuery('.ui-sortable-helper').position().left + parseInt(current_margin_left);		
				
			jQuery('.placeholder').attr('data-link-id', jQuery(ui.item).attr('data-id'));
			
			//check if previous item is being dragged, then exclude
			if(jQuery(prev_div).attr('data-id') == jQuery('.placeholder').attr('data-link-id')){				
				prev_div = jQuery("ul").find("[data-id='"+jQuery(prev_div).attr('data-id')+"']").prev();
			} else if(jQuery(next_div).attr('data-id') == jQuery('.placeholder').attr('data-link-id')){				
				next_div = jQuery("ul").find("[data-id='"+jQuery(next_div).attr('data-id')+"']").next();
			} else {
				//continue
			}
			
			//previous data
			prev_data_lvl = parseInt(jQuery(prev_div).attr('data-level'));
			prev_data_id = parseInt(jQuery(prev_div).attr('data-id'));
			prev_data_parent = parseInt(jQuery(prev_div).attr('data-parent'));
			
			//next data
			next_data_lvl = parseInt(jQuery(next_div).attr('data-level'));
			next_data_id = parseInt(jQuery(next_div).attr('data-id'));
			next_data_parent = parseInt(jQuery(next_div).attr('data-parent'));
			
			prev_allow_sub = jQuery(prev_div).attr('data-allow-sub');
			
			if(jQuery(ui.item).attr('data-menu-type') == 'mega'){
				prev_allow_sub = 'no';
			}
			//check where the item can move to
			var return_movement = enable_movement(
				prev_data_lvl,
				prev_data_id,
				prev_data_parent,
				next_data_lvl,
				next_data_id,
				next_data_parent,
				current_position,
				ui.item,
				prev_allow_sub
			);
			
			var allow = return_movement[0].allow;
			var end = return_movement[0].end;
			var increment = 20;
			var increment_by = 0;
			
			current_placement = jQuery(ui.item).attr('data-level');
			jQuery('.placeholder').attr('id', 'hero_margin_left_'+allow);
			jQuery('.placeholder').attr('data-level', allow);
			
			var increment_array = [];
			
			for(var i = allow; i <= end; i++){
				increment_by = increment * i;
				increment_array.push(increment_by);
			}
			
			var count = 0;
			
			for(var e = allow; e <= end; e++){							
				count++;				
				if(increment_array.length < 1){
					jQuery('.placeholder').attr('id', 'hero_margin_left_'+allow);	
					jQuery('.placeholder').attr('data-level', allow); 		
				} else {
					if(allow >= 1){
						if(current_position >= increment_array[e]){
							jQuery('.placeholder').attr('id', 'hero_margin_left_'+(count+1));
							jQuery('.placeholder').attr('data-level', (count+1));	
						}
					} else {
						if(current_position >= increment_array[e]){
							jQuery('.placeholder').attr('id', 'hero_margin_left_'+e);
							jQuery('.placeholder').attr('data-level', e);
						}
					}
				}
			}
			
		},
		beforeStop: function(event, ui){
			if(jQuery(ui.item).attr('data-menu-type') == 'mega'){
				jQuery(clean_array).each(function(index, element) {
					jQuery(".hero_group_item").find("[data-group-val='"+element+"']").unwrap();
				});
			}
			//set id - this adds the margining on the nav item
			jQuery(ui.item).attr('id',jQuery('.placeholder').attr('id')); 
			//set data level
			jQuery(ui.item).attr('data-level', parseInt(jQuery('.placeholder').attr('data-level')));
			//set parenting
			if(prev_data_lvl == parseInt(jQuery('.placeholder').attr('data-level'))){
				jQuery(ui.item).attr('data-parent', prev_data_parent);
			} else if(parseInt(jQuery('.placeholder').attr('data-level')) == 0){
				jQuery(ui.item).attr('data-parent', 0);	
			} else {
				same_sub = jQuery("ul").find("[data-id='"+prev_data_parent+"']").attr('data-parent');
				same_sub_level = jQuery("ul").find("[data-id='"+prev_data_parent+"']").attr('data-level');
				if(same_sub_level == parseInt(jQuery('.placeholder').attr('data-level'))){
					//find the same item and its parent
					jQuery(ui.item).attr('data-parent', same_sub);
				} else {
					jQuery(ui.item).attr('data-parent', prev_data_id);
				}
			}	
			current_placement = parseInt(jQuery('.placeholder').attr('data-level'));	
		},
		stop: function(event, ui){
			var the_id = jQuery(ui.item).attr('id');
			unpack_items(jQuery(ui.item).attr('data-id'), prev_data_lvl, prev_data_id, current_placement, start_placement);
			jQuery(".hero_sort_item").each(function(index, element) {
                jQuery(this).removeClass('not_sortable');
				jQuery(this).removeClass('ui-state-disabled');
            });
			//remove class to make them not sortable
			jQuery('.hero_sort_item').each(function(index, element) {
				jQuery(this).removeAttr('data-group-val');
			});
			//On sort tag the current item being dragged
			set_order();
			set_parents();
			jQuery( ".sort" ).sortable( "refresh" );
		}		
	});	
	
}

//unpack items
function unpack_items(current_item_id, prev_data_lvl, prev_data_id, current_placement, start_placement){
	
	diff = current_placement - start_placement;
	
	jQuery.fn.reverseChildren = function() {
		return this.each(function(){
			var $this = jQuery(this);
			$this.children().each(function(){
				$this.prepend(this);
			});
		});
	};
	
	var level_array = [];
	
	jQuery('.sort').find("[data-id='" + current_item_id + "'] .transfer_items li").each(function(index, element) {
        jQuery('.hero_dummy').append(jQuery(this));
		level_array.push(jQuery(this).attr('data-level'));
    });
	
	jQuery('.hero_dummy').reverseChildren();
	
	jQuery('.hero_dummy li').each(function(index, element) {
		var item_level = parseInt(jQuery(this).attr('data-level')) + diff;
        jQuery('.sort').find("[data-id='" + current_item_id + "']").after(jQuery(this));
		jQuery(this).attr('data-level', item_level);
		jQuery(this).attr('id', 'hero_margin_left_'+item_level);
    });
	
	jQuery('.transfer_items').hide();
	
}

//get transfer items
function get_transfer_items(current_item_id){	
	//find all sub elements
	jQuery('.sort').find("[data-parent='" + current_item_id + "']").each(function(index, element) {
		jQuery(this).appendTo(jQuery('ul').find("[data-id='" + current_item_id + "']").children('.transfer_items').show());
		jQuery(this).addClass('ui-state-disabled');
		jQuery('ul').find("[data-id='" + current_item_id + "']").addClass('not_sortable');		
		if(jQuery('.sort').find("[data-parent='" + jQuery(this).attr('data-id') + "']")){
			jQuery(this).addClass('ui-state-disabled');
			get_transfer_items(jQuery(this).attr('data-id'));	
		};		
    });	
}

//enable movement
function enable_movement(prev_data_lvl, prev_data_id, prev_data_parent, next_data_lvl, next_data_id, next_data_parent, current_position, current_item, prev_allow_sub){
	
	//settings array
	var settings_array = [{
		allow: '',
		end: ''
	}];
	
	//if allow sub is yes or no, do the following
	if(prev_allow_sub == 'yes'){
		if(prev_data_id && next_data_parent == prev_data_id){ //check to see if im between a parent and sub item
			var move = parseInt(prev_data_lvl) + 1;
			settings_array[0].allow = next_data_lvl;
			settings_array[0].end = next_data_lvl;
		} else if(prev_data_parent && next_data_parent == prev_data_parent){ //check to see if current item is the only sub
			var move = parseInt(prev_data_lvl) + 1;
			settings_array[0].allow = prev_data_lvl;
			settings_array[0].end = move;
		} else if(!next_data_parent){ // if at the bottom
			var move = parseInt(prev_data_lvl) + 1;
			settings_array[0].allow = 0;
			for(var i = 0; i <= move; i++){
				settings_array[0].end = i;
			}			
		} else if(!prev_data_parent){ // if at the very top of the sorting
			settings_array[0].allow = 0;
			settings_array[0].end = 0;	
		} else if((prev_data_parent && next_data_parent != prev_data_parent) && (prev_data_lvl > next_data_lvl)){
			var move = parseInt(prev_data_lvl) + 1;
			settings_array[0].allow = next_data_lvl;
			settings_array[0].end = move;	
		} else {
		}		
	} else {
		settings_array[0].allow = 0;
		settings_array[0].end = 0;	
	}
	
	return settings_array;
	
}

//set order
function set_parents(){
	//set positions of new order
	jQuery('.hero_sort_item').each(function(index, element) {
		var the_index = jQuery(this).data('index');
		var the_parent_val = jQuery(this).attr('data-parent');
		var the_level_val = jQuery(this).attr('data-level');
		global_menu_obj.nav_items[the_index].parentNavId = the_parent_val;
		global_menu_obj.nav_items[the_index].level = the_level_val;
    });
	flag_save_required('save_clicked',{"status": true});
}

//set order
function set_order(){
	//set positions of new order
	jQuery('.hero_sort_item').each(function(index, element) {
		var the_index = jQuery(this).data('index');
		global_menu_obj.nav_items[the_index].order = index;
    });
	flag_save_required('save_clicked',{"status": true});
}

//get nav
function get_nav(){
	global_menu_obj.nav_items.sort(sort_items_array);
	jQuery(global_menu_obj.nav_items).each(function(index, element) { 	
		if(element.deleted != 1){
			var mega_id = 0;
			if(element.mega_menus.length != 0){
				mega_id = element.mega_menus[0].megaMenuId
			}
			preload_html(element.type, index, element.navItemId, element.parentNavId, element.level, mega_id);
		}
    });
	//check items
	check_items();
}

//check items
function check_items(){	
	if(jQuery('.main_holder li').length > 0){
		jQuery('.hmenu_nav_error').hide();
	} else {
		jQuery('.hmenu_nav_error').show();
	}
}

//preload html
function preload_html(type, index, id, parent, level, mega_id){
	//check if its a basic item or mega menu      
	type == 'mega' ? the_url = plugin_url + 'views/menus/html_snippets/mega_item.php' : the_url = plugin_url + 'views/menus/html_snippets/menu_item.php' 
	//load the html
	jQuery.ajax({
		url: the_url,
		data: {
			index: index,
			navItemId: id,
			parentId: parent,
			lvl: level,
			url: plugin_url,
			the_type: type,
			megaMenuId: mega_id
		},
        async: false,
		dataType: "html"
	}).done(function(data){	
		//append html
		jQuery('.sort').append(data);
		enable(index);
	}).fail(function(){
		 //page error
	});		
}

//enable
function enable(index){
	enable_toggle();
	enable_delete();
	set_structure_data(index);
	enable_icon_select();	
}

var changed_items = 0;

function enable_delete(){
	
	//get the delete click
	jQuery('.hero_button_delete').off().on('click', function(){
		
		//current item data
		
		var current_main_index = jQuery(this).data('main-index');
		var current_parent_id = global_menu_obj.nav_items[current_main_index].parentNavId;
		var current_item_id = jQuery(this).data('item-id');
		var current_level = global_menu_obj.nav_items[current_main_index].level;
		var html_index = jQuery(this).parents('.hero_sort_item').index();
		
		//get the next div data of the item currently being deleted
		
		var next_level = jQuery(this).parents('.hero_sort_item').next('.hero_sort_item').data('level');
		var data_id = jQuery(this).parents('.hero_sort_item').next('.hero_sort_item').data('id');
		
		if(window.confirm('Are you sure you want to delete the nav item?')){
			//set object
			global_menu_obj.nav_items[current_main_index].deleted = 1;
			jQuery(this).parents('.hero_sort_item').remove();
			get_sub_items(current_main_index, current_parent_id, current_item_id, current_level, html_index);
			flag_save_required('save_clicked',{"status": true});
			check_items();
		} else {
			
		}
		
	});
}



//get all the sub items of the one being deleted
function get_sub_items(current_main_index, current_parent_id, current_item_id, current_level, html_index){
	
	jQuery.each(jQuery('.hero_sort_item').slice(html_index), function(index, element){		
		if(jQuery(this).attr('data-level') > current_level){
			
			//variables
			var item_level = jQuery(this).attr('data-level');
			var item_id = jQuery(this).attr('data-id');
			var item_index = jQuery(this).attr('data-index');
			var item_parent_id = jQuery(this).attr('data-parent');
			
			//change html data
			jQuery(this).attr('data-level', (item_level-1));
			jQuery(this).attr('id', 'hero_margin_left_' + (item_level-1));
			
			var prev_item_level = jQuery(this).prev('.hero_sort_item').attr('data-level');
			
			//if level is equal to 1, then it is going to change to 0 which means it has no parent
			if(item_level == 1){
				jQuery(this).attr('data-parent', 0);
				global_menu_obj.nav_items[item_index].parentNavId = 0;
			} else if(item_level > 1){
				
				var prev_item_id = jQuery(this).prev('.hero_sort_item').attr('data-id');
				jQuery(this).attr('data-parent', prev_item_id);
				global_menu_obj.nav_items[item_index].parentNavId = prev_item_id;
				
				var the_value = item_level - 1;
				
				if(the_value == prev_item_level){
					jQuery(this).attr('data-parent', current_parent_id);
					global_menu_obj.nav_items[item_index].parentNavId = current_parent_id;
				}
				
			} 
			//change json obejct
			global_menu_obj.nav_items[item_index].level = (item_level-1);
			
		} else {
			return ( jQuery(this).attr('data-level') === 0 );
		}
	});
		
}

//setup data
function set_structure_data(index){
	
	//set data for each nav item
	if(global_menu_obj.nav_items[index].type == 'mega'){ 
		
		//set name
		jQuery('#mega_heading_'+index).html(global_menu_obj.nav_items[index].name);
		jQuery('#mega_name_'+index).val(global_menu_obj.nav_items[index].name);
		jQuery('#item_type_'+index).html(global_menu_obj.nav_items[index].type);
		jQuery('#mega_title_'+index).val(global_menu_obj.nav_items[index].title);		
		jQuery('#mega_alt_'+index).val(global_menu_obj.nav_items[index].title);
		jQuery('#item_order_'+index).val(global_menu_obj.nav_items[index].order);
		
		//parent and leveling
		jQuery('#item_parent_'+index).val(global_menu_obj.nav_items[index].parentNavId);
		jQuery('#item_level_'+index).val(global_menu_obj.nav_items[index].level);
		
		//get col content | used for the start up
		var col_array = global_menu_obj.nav_items[index].mega_menus[0].mega_stuff;
		
		//set layout
		jQuery('#mega_layout_'+index).val(global_menu_obj.nav_items[index].mega_menus[0].layout);
		jQuery('#hero_options_'+index+' .hero_option_items div').each(function(idx, ele) {
            if(jQuery(this).data('layout') == global_menu_obj.nav_items[index].mega_menus[0].layout){
				jQuery('#hero_options_'+index+' .hero_selected_layout ').attr('id', jQuery(this).data('id'));
				generate_cols(jQuery(this).data('layout'), index, col_array);
			}
        });
		
		if(jQuery('#mega_icon_'+index).val() == global_menu_obj.nav_items[index].icon){ 
			jQuery('#mega_icon_'+index).attr('checked', 'checked');
		}		
		jQuery('#mega_icon_content_'+index).val(global_menu_obj.nav_items[index].iconContent);
		jQuery('.the_icon_'+index).children('#hero_inner_icon').attr('class', global_menu_obj.nav_items[index].iconContent);
		jQuery('#mega_icon_size_'+index+' option').each(function(idx, el) {
		   if(jQuery(this).val() == global_menu_obj.nav_items[index].iconSize){
			   jQuery(this).attr('selected', 'selected')
		   }
		});
		jQuery('#mega_icon_color_'+index).val(global_menu_obj.nav_items[index].iconColor);
		jQuery('.the_icon_'+index).css({
			color:global_menu_obj.nav_items[index].iconColor
		});
		
		//active item
		if(jQuery('#mega_nav_active_'+index).val() == global_menu_obj.nav_items[index].active){ 
			jQuery('#mega_nav_active_'+index).attr('checked', 'checked');
		}
		
		//mobile active
		if(jQuery('#mega_mobile_active_'+index).val() == global_menu_obj.nav_items[index].activeMobile){ 
			jQuery('#mega_mobile_active_'+index).attr('checked', 'checked');
		}
		
		//background
		if(jQuery('#mega_background_'+index).val() == global_menu_obj.nav_items[index].mega_menus[0].background){ 
			jQuery('#mega_background_'+index).attr('checked', 'checked');
		}
		
		jQuery('#mega_background_url_'+index).val(global_menu_obj.nav_items[index].mega_menus[0].backgroundUrl);
		
		jQuery('#mega_background_position_'+index+' option').each(function(idx, el) {
		   if(jQuery(this).val() == global_menu_obj.nav_items[index].mega_menus[0].backgroundPosition){
			   jQuery(this).attr('selected', 'selected')
		   }
		});
		
		//execute mega menu
		mega_menu_execute(index);
		
	} else {
		jQuery('#ni_heading_'+index).html(global_menu_obj.nav_items[index].name);
		jQuery('#ni_name_'+index).val(global_menu_obj.nav_items[index].name);
		jQuery('#item_type_'+index).html(global_menu_obj.nav_items[index].type);
		jQuery('#ni_alt_'+index).val(global_menu_obj.nav_items[index].title);
		jQuery('#ni_url_'+index).val(global_menu_obj.nav_items[index].link);
		jQuery('#item_order_'+index).val(global_menu_obj.nav_items[index].order);
		
		//parent and leveling
		jQuery('#item_parent_'+index).val(global_menu_obj.nav_items[index].parentNavId);
		jQuery('#item_level_'+index).val(global_menu_obj.nav_items[index].level);
		
		jQuery('#ni_target_'+index+' option').each(function(idx, el) {
		   if(jQuery(this).val() == global_menu_obj.nav_items[index].target){
			   jQuery(this).attr('selected', 'selected')
		   }
		});
		if(jQuery('#ni_icon_'+index).val() == global_menu_obj.nav_items[index].icon){ 
			jQuery('#ni_icon_'+index).attr('checked', 'checked');
		}		
		jQuery('#ni_icon_content_'+index).val(global_menu_obj.nav_items[index].iconContent);
		jQuery('.the_icon_'+index).children('#hero_inner_icon').attr('class', global_menu_obj.nav_items[index].iconContent);
		jQuery('#ni_icon_size_'+index+' option').each(function(idx, el) {
		   if(jQuery(this).val() == global_menu_obj.nav_items[index].iconSize){
			   jQuery(this).attr('selected', 'selected')
		   }
		});
		jQuery('#ni_icon_color_'+index).val(global_menu_obj.nav_items[index].iconColor);
		jQuery('.the_icon_'+index).css({
			color:global_menu_obj.nav_items[index].iconColor
		});
		if(jQuery('#ni_event_'+index).val() == global_menu_obj.nav_items[index].method){ 
			jQuery('#ni_event_'+index).attr('checked', 'checked');
		}		
		jQuery('#ni_event_function_'+index).val(global_menu_obj.nav_items[index].methodReference);
		if(jQuery('#ni_nav_active_'+index).val() == global_menu_obj.nav_items[index].active){ 
			jQuery('#ni_nav_active_'+index).attr('checked', 'checked');
		}
		if(jQuery('#ni_mobile_active_'+index).val() == global_menu_obj.nav_items[index].activeMobile){ 
			jQuery('#ni_mobile_active_'+index).attr('checked', 'checked');
		}
	}
	
	//switch components
	switch_components();
	
	//bind_field_convert();
	find_small_toggle_elements();
	
	//enable update
	enable_update_settings(index);
}

//enable update settings
function enable_update_settings(index){
	
	if(global_menu_obj.nav_items[index].type == 'mega'){ //mega	
		//change: name
		jQuery('#mega_name_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].name = jQuery(this).val();
			global_menu_obj.nav_items[index].mega_menus[0].name = jQuery(this).val();
			jQuery('#mega_heading_'+index).html(global_menu_obj.nav_items[index].name);
			flag_save_required('save_clicked',{"status": true});
		});	
		//change: alt/title
		jQuery('#mega_alt_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].title = jQuery(this).val();
			flag_save_required('save_clicked',{"status": true});
		});
		//change: layout
		jQuery('#mega_layout_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].mega_menus[0].layout = jQuery(this).val();
			flag_save_required('save_clicked',{"status": true});
		});
		//change: icon enable
		jQuery('#mega_icon_'+index).on('change', function(){
			jQuery(this).prop('checked') ? global_menu_obj.nav_items[index].icon = jQuery(this).val() : global_menu_obj.nav_items[index].icon = 0;
			flag_save_required('save_clicked',{"status": true});
		});
		//change: icon content
		jQuery('#mega_icon_content_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].iconContent = jQuery(this).val();
			jQuery('.the_icon_'+index).children('#hero_inner_icon').attr('class', jQuery(this).val());
			flag_save_required('save_clicked',{"status": true});
		});
		//change: icon size
		jQuery('.mega_icon_size_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
			jQuery('#ni_icon_size_'+index).trigger('change');		
		});	
		jQuery('#mega_icon_size_'+index).on('change', function(){
			global_menu_obj.nav_items[index].iconSize = jQuery(this).children('option:selected').val();
			flag_save_required('save_clicked',{"status": true});
		});	
		//change: icon color
		jQuery('#mega_icon_color_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].iconColor = jQuery(this).val();
			flag_save_required('save_clicked',{"status": true});
			jQuery('.the_icon_'+index).css({
				color:jQuery(this).val()
			});
		});
		//change: active
		jQuery('#mega_nav_active_'+index).on('change', function(){
			jQuery(this).prop('checked') ? global_menu_obj.nav_items[index].active = jQuery(this).val() : global_menu_obj.nav_items[index].active = 0;
			flag_save_required('save_clicked',{"status": true});
		});
		//change: active mobile
		jQuery('#mega_mobile_active_'+index).on('change', function(){
			jQuery(this).prop('checked') ? global_menu_obj.nav_items[index].activeMobile = jQuery(this).val() : global_menu_obj.nav_items[index].activeMobile = 0;
			flag_save_required('save_clicked',{"status": true});
		});
		
		//change: background
		jQuery('#mega_background_'+index).on('change', function(){
			jQuery(this).prop('checked') ? global_menu_obj.nav_items[index].mega_menus[0].background = jQuery(this).val() : global_menu_obj.nav_items[index].mega_menus[0].background = 0;
			flag_save_required('save_clicked',{"status": true});
		});
		
		//change: background url
		jQuery('#mega_background_url_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].mega_menus[0].backgroundUrl = jQuery(this).val();
			flag_save_required('save_clicked',{"status": true});
		});
		
		//change: background position
		jQuery('.mega_background_position_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
			jQuery('#mega_background_position_'+index).trigger('change');		
		});	
		jQuery('#mega_background_position_'+index).on('change', function(){
			global_menu_obj.nav_items[index].mega_menus[0].backgroundPosition = jQuery(this).children('option:selected').val();
			flag_save_required('save_clicked',{"status": true});
		});	
		
		
	} else { //basic
		//change: name
		jQuery('#ni_name_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].name = jQuery(this).val();
			jQuery('#ni_heading_'+index).html(global_menu_obj.nav_items[index].name);
			flag_save_required('save_clicked',{"status": true});
		});
		//change: alt/title
		jQuery('#ni_alt_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].title = jQuery(this).val();
			flag_save_required('save_clicked',{"status": true});
		});
		//change: url
		jQuery('#ni_url_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].link = jQuery(this).val();
			flag_save_required('save_clicked',{"status": true});
		});
		//change: target
		jQuery('.ni_target_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
			jQuery('#ni_target_'+index).trigger('change');		
		});	
		jQuery('#ni_target_'+index).on('change', function(){
			global_menu_obj.nav_items[index].target = jQuery(this).children('option:selected').val();
			flag_save_required('save_clicked',{"status": true});
		});	
		//change: icon enable
		jQuery('#ni_icon_'+index).on('change', function(){
			jQuery(this).prop('checked') ? global_menu_obj.nav_items[index].icon = jQuery(this).val() : global_menu_obj.nav_items[index].icon = 0;
			flag_save_required('save_clicked',{"status": true});
		});
		//change: icon content
		jQuery('#ni_icon_content_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].iconContent = jQuery(this).val();
			jQuery('.the_icon_'+index).children('#hero_inner_icon').attr('class', jQuery(this).val());
			flag_save_required('save_clicked',{"status": true});
		});
		//change: icon size
		jQuery('.ni_icon_size_'+index+' .hero_dropdown .hero_drop_row').on('click', function(){
			jQuery('#ni_icon_size_'+index).trigger('change');		
		});	
		jQuery('#ni_icon_size_'+index).on('change', function(){
			global_menu_obj.nav_items[index].iconSize = jQuery(this).children('option:selected').val();
			flag_save_required('save_clicked',{"status": true});
		});	
		//change: icon color
		jQuery('#ni_icon_color_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].iconColor = jQuery(this).val();
			flag_save_required('save_clicked',{"status": true});
			jQuery('.the_icon_'+index).css({
				color:jQuery(this).val()
			});
		});
		//change: method
		jQuery('#ni_event_'+index).on('change', function(){
			jQuery(this).prop('checked') ? global_menu_obj.nav_items[index].method = jQuery(this).val() : global_menu_obj.nav_items[index].method = 0;
			flag_save_required('save_clicked',{"status": true});
		});
		//change: method reference
		jQuery('#ni_event_function_'+index).on('change keyup', function(){
			global_menu_obj.nav_items[index].methodReference = jQuery(this).val();
			flag_save_required('save_clicked',{"status": true});
		});
		//change: active
		jQuery('#ni_nav_active_'+index).on('change', function(){
			jQuery(this).prop('checked') ? global_menu_obj.nav_items[index].active = jQuery(this).val() : global_menu_obj.nav_items[index].active = 0;
			flag_save_required('save_clicked',{"status": true});
		});
		//change: active mobile
		jQuery('#ni_mobile_active_'+index).on('change', function(){
			jQuery(this).prop('checked') ? global_menu_obj.nav_items[index].activeMobile = jQuery(this).val() : global_menu_obj.nav_items[index].activeMobile = 0;
			flag_save_required('save_clicked',{"status": true});
		});
	}	
}

//add mega menu
function mega_menu_execute(main_index){
	
	//reset the column borders
	reset_borders(main_index);
	
	var element = jQuery('#hero_options_'+main_index);
	
	jQuery('#hero_options_'+main_index).children('.hero_selected_layout ').off().on('click', function(){
		if(!jQuery(this).attr('data-open') || jQuery(this).attr('data-open') == 'close'){
			jQuery(this).attr('data-open', 'open');
			//animate
			jQuery(this).parent('.hero_layout_options').children('.hero_option_items').each(function(idx, ele) {
				jQuery(ele).animate({
					opacity:1,
					width:500
				}, 300);
			});				
		} else if(jQuery(this).attr('data-open') == 'open'){
			jQuery(this).attr('data-open', 'close');
			//animate
			jQuery(this).parent('.hero_layout_options').children('.hero_option_items').each(function(idx, ele) {
				jQuery(this).animate({
					opacity:0,
					width:0
				}, 1000);
			});				
		}
	});
	
	enable_layout_clicks(element, main_index);
    	
}

//enable layout clicks
function enable_layout_clicks(ele, main_index){
	
	var col_array = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff;
	
	var array_length = 0;
	
	if(col_array != null){
		array_length = col_array.length;
	}
	
	jQuery(ele).children('.hero_option_items').children('div').each(function(index, element) {
		
		if(array_length < jQuery(this).data('cols') || array_length == jQuery(this).data('cols')){
			
			jQuery(this).off().on('click', function(){
				
				jQuery(ele).children('.hero_option_items').children('input').val(jQuery(this).data('layout'));
				jQuery(ele).children('.hero_option_items').children('input').trigger('change');
				jQuery(ele).children('.hero_selected_layout').attr('id', jQuery(this).data('id'));
				jQuery(ele).children('.hero_selected_layout').trigger('click');
				
				//reset index placement position because of layout change
				var col_data_length = col_array.length;
				var layout_length = jQuery(this).data('cols');
				
				if(col_data_length <= layout_length){
					col_array.sort( sort_col_array ); 
					jQuery(col_array).each(function(index, element) {
						global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[index].placement = index;
					});
				}
				
				//regenerate cols
				generate_cols(jQuery(this).data('layout'), jQuery(this).data('idx'), col_array);
				
			});
			      
		} else {
			
			jQuery(this).css({ opacity: 0.3 });			
			//jQuery(this).attr('data-tooltip', 'To select this layout, remove one of the current content blocks.');
			jQuery(this).addClass('disable_option');
			
		}
		
    });
	
}

//reset borders
function reset_borders(index){
	jQuery('.the_playground_'+index+' .mega_col_holder > div').each(function(index, element) {
		jQuery(this).addClass('hero_border_right');
	});
	jQuery('.the_playground_'+index+' .mega_col_holder > div').last().removeClass('hero_border_right');
}

//sort array
function sort_col_array( a, b ) {
    return a.placement - b.placement;
}

//sort nav items
function sort_items_array( a, b ) {
    return a.order - b.order;
}

//find the correct one
function the_lookup(array, prop, value) {
    for (var i = 0, len = array.length; i < len; i++){
        if (array[i][prop] === value) return array[i];
	}
}

//find the correct one
function the_index_lookup(array, prop, value) {
    for (var i = 0, len = array.length; i < len; i++){
        if (array[i][prop] === value) return i;
	}
}

//enable cols
function generate_cols(layout, main_index, col_array){
	
	//clean out HTML
	jQuery('.mega_cols_'+main_index).html('');	
	
	if(typeof(col_array) !== 'undefined'){
		col_array.sort( sort_col_array ); 
	}
	
	layout = layout + ',';
	
	var layout_split = layout.split(',');
	var the_split_array = [];
	
	jQuery(layout_split).each(function(index, element) {
		if(element != ''){
			the_split_array.push(element);
		}
	});
	
	var col_html = '';
	
	jQuery(the_split_array).each(function(idx, element) {
		
		if(typeof(col_array) !== 'undefined'){
			var the_object = the_lookup(col_array, 'placement', idx);		
			if(the_object && the_object.deleted != 1 ){
				col_html += '<div class="mega_col_item hero_col_'+element+'" data-iscontent="yes" id="unique_id_'+idx+"_"+main_index+'" data-col-position="'+idx+'" data-main-index="'+main_index+'">';
					col_html += '<div class="hero_col_wrapper" data-identifier="is_content" id="wrapper_'+idx+"_"+main_index+'" data-placement="'+the_object.placement+'" data-index="'+the_index_lookup(col_array, 'placement', the_object.placement)+'">';
						col_html += '<div class="hero_content_available size_11 hero_bluegrey">'+load_col_placeholder(the_object.type)+the_object.heading+'</div>';
						col_html += '<div class="hero_edits rounded_20">';
							col_html += '<div class="hero_edit_item hero_edit_col" data-current-layout="'+layout+'" data-index="'+the_index_lookup(col_array, 'placement', the_object.placement)+'" data-main-index="'+main_index+'" data-popup="'+the_object.type+'" data-placement="'+the_object.placement+'" data-megaid="'+the_object.megaMenuId+'"></div>';
							col_html += '<div class="hero_edit_item hero_delete_col" data-current-layout="'+layout+'" data-index="'+the_index_lookup(col_array, 'placement', the_object.placement)+'" data-main-index="'+main_index+'"></div>';
							col_html += '<div class="hero_edit_item hero_button_drag hero_col_handle" data-current-layout="'+layout+'" data-index="'+the_index_lookup(col_array, 'placement', the_object.placement)+'"></div>';
						col_html += '</div>';
					col_html += '</div>';
				col_html += '</div>';
			} else {		
				col_html += '<div class="mega_col_item hero_col_'+element+'" data-iscontent="no" id="unique_id_'+idx+"_"+main_index+'" data-col-position="'+idx+'" data-main-index="'+main_index+'">';
					col_html += '<div class="hero_col_wrapper" data-identifier="add_content" id="wrapper_'+idx+"_"+main_index+'">';
						col_html += '<div class="btn_add_content rounded_30 size_11" data-placement="'+idx+'" data-index="'+main_index+'">Add Content</div>';
					col_html += '</div>';
				col_html += '</div>';		
			}
		} else {
			col_html += '<div class="mega_col_item hero_col_'+element+'" data-iscontent="no" id="unique_id_'+idx+"_"+main_index+'" data-col-position="'+idx+'" data-main-index="'+main_index+'">';
				col_html += '<div class="hero_col_wrapper" data-identifier="add_content" id="wrapper_'+idx+"_"+main_index+'">';
					col_html += '<div class="btn_add_content rounded_30 size_11" data-placement="'+idx+'" data-index="'+main_index+'">Add Content</div>';
				col_html += '</div>';
			col_html += '</div>';	
		}
		
    });
	
	jQuery('.mega_cols_'+main_index).html(col_html);
	
	reset_borders(main_index);
	
	enable_content_panel();
	
	enable_col_editing(layout, main_index);
		
}

//enable col editing
function enable_col_editing(layout, main_index){
	
	//edit column
	jQuery('.hero_edit_col').off().on('click', function(){
		
		var the_popup_html = jQuery(this).data('popup');
		var the_index = jQuery(this).data('index');
		var the_main_index = jQuery(this).data('main-index');
		var the_placement = jQuery(this).data('placement');
		var the_megaid = jQuery(this).data('megaid');
		var the_type = jQuery(this).data('popup');
		
		launch_hero_popup(
			'menus/html_snippets/'+the_popup_html+'.html',
			'popup_load',
			'popup_update',
			'popup_cancel',
			{placement: the_placement, id: the_megaid, index: the_main_index, content_index: the_index, info: the_type, new_info: 'no'} 	//update
		);
		
	});
	
	//icon edit click
	jQuery('.hero_content_available').off().on('click', function(){
		jQuery(this).parents('.hero_col_wrapper').children('.hero_edits').children('.hero_edit_col').trigger('click');
	});
	
	//delete column
	jQuery('.hero_delete_col').off().on('click', function(){
		
		var the_main_index = jQuery(this).data('main-index');
		var the_index = jQuery(this).data('index');
		var the_layout = jQuery(this).data('current-layout');
		
		//confirm delete
		if(window.confirm('Are you sure you want to delete?')){
						
			//set item to deleted
			global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff[the_index].deleted = 1;
			
			//new array
			jQuery(global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff).each(function(index, element) {
                if(element.deleted != 1){
					//keep items
				} else {
					global_menu_obj.nav_items[the_main_index].mega_menus[0].deleted_items.push(global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff[index]);
					global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff.splice(index, 1);
					flag_save_required('save_clicked',{"status": true});
				}
            });
			
			var col_array = global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff;
			
			//reload cols
			generate_cols(the_layout, the_main_index, col_array);
			
			open_close_options(the_main_index);
			
		} else {
			//do nothing
		};		
	});
	
	//variables for switch
	var start_col_id;
	var end_col_id;
	var col_start_position;
	var col_end_position;
	var item_being_replaced;
	var current_placement;
	var div_to_replace;
	var the_main_index;
	var col_item_index;
	var col_replaced_index;
	var containment_id;
	var layout;
	
	//sort the content within the cols
	jQuery( ".mega_col_item" ).sortable({
		connectWith: ".mega_col_item",
		placeholder: 'col_placeholder',
		start: function(event, ui){
			
			jQuery(ui.item).css({
				opacity:0.5
			});
			//start col
			start_col_id = jQuery('.col_placeholder').parents('.mega_col_item').attr('id');
			//start position
			col_start_position = jQuery('.col_placeholder').parents('.mega_col_item').attr('data-col-position');
			//main index
			the_main_index = jQuery('.col_placeholder').parents('.mega_col_item').attr('data-main-index');
			//current placement - this indentifies where in the json object the data is for the current item being dragged
			current_placement = jQuery('.col_placeholder').parents('.mega_col_item').children('.hero_col_wrapper').attr('data-placement');
			//the data index
			col_item_index = jQuery('.col_placeholder').parents('.mega_col_item').children('.hero_col_wrapper').attr('data-index');
			
		},
		handle: '.hero_col_handle',
		change: function(event, ui){
			
			//previous div
			var prev_col_item = jQuery('.col_placeholder').prev();									
			
			//next div
			var next_col_item = jQuery('.col_placeholder').next();
						
			//if prev undefined
			if(typeof(prev_col_item.attr('id')) !== 'undefined'){
				div_to_replace = prev_col_item.attr('id');
				col_replaced_index = prev_col_item.attr('data-index');
			}
			
			//if next undefined
			if(typeof(next_col_item.attr('id')) !== 'undefined'){
				div_to_replace = next_col_item.attr('id');
				col_replaced_index = next_col_item.attr('data-index');
			}	
			
		},
		beforeStop: function(event, ui){
			//end col
			end_col_id = jQuery('.col_placeholder').parents('.mega_col_item').attr('id');	
			//end position
			col_end_position = jQuery('.col_placeholder').parents('.mega_col_item').attr('data-col-position');		
		},
		stop: function(event, ui){
			jQuery(ui.item).css({
				opacity:1
			});	
			//set layout
			layout = jQuery(ui.item).children('.hero_edits').children('.hero_button_drag').attr('data-current-layout');
			//switch content
			switch_col_content(the_main_index, start_col_id, end_col_id, div_to_replace, col_start_position, col_end_position, layout, current_placement, col_item_index, col_replaced_index);				
		}
		
    });
	
	//set the containment for the sortable content
	jQuery('.mega_col_item').each(function(){
		jQuery(this).sortable({
			containment: jQuery(this).parent()
		});
	});
	
}

//switch cols
function switch_col_content(the_main_index, start_col_id, end_col_id, to_replace, col_start_position, col_end_position, layout, current_placement, col_item_index, col_replaced_index){
	if(start_col_id == end_col_id){
		//do nothing because item wasnt moved to a new position
	} else if(start_col_id != end_col_id){
		jQuery('#'+start_col_id).html(jQuery('.mega_col_holder').find('#'+to_replace));
		//flash to show something changed
		jQuery('#'+end_col_id).animate({
			'background-color': '#A8CE83',
			'top': '-5px'
		}, 200, function(){
			jQuery('#'+end_col_id).animate({
				'background-color': '#F5F5F5',
				'top': '0px'
			}, 200);
		});
		change_col_data(the_main_index, col_start_position, col_end_position, layout, current_placement, col_item_index, col_replaced_index);
		flag_save_required('save_clicked',{"status": true});
	}	
	
}

//change data in json object with new positions
function change_col_data(the_main_index, col_start_position, col_end_position, layout, current_placement, col_item_index, col_replaced_index){
	
	//change the placement of item being dragged
	global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff[col_item_index].placement = parseInt(col_end_position);
	
	//change the placement of item being replaced
	if(global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff[col_replaced_index]){
		global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff[col_replaced_index].placement = parseInt(col_start_position);
	}
	
	var col_array = global_menu_obj.nav_items[the_main_index].mega_menus[0].mega_stuff;			
	
	//reload cols
	generate_cols(layout, the_main_index, col_array);
	
}

//load placeholder
function load_col_placeholder(type){
	
	var return_html = '';
		
	switch(type){
		
		case 'post':		
		return_html += '<div class="hero_option_one">Posts<div class="hero_mega_option_image"></div></div>';
		break;
		
		case 'text':
		return_html += '<div class="hero_option_two">Text<div class="hero_mega_option_image"></div></div>';
		break;
		
		case 'list':
		return_html += '<div class="hero_option_three">List<div class="hero_mega_option_image"></div></div>';
		break;
		
		case 'contact':
		return_html += '<div class="hero_option_four">Contact/HTML<div class="hero_mega_option_image"></div></div>';
		break;
		
		case 'woo':
		return_html += '<div class="hero_option_five">Products<div class="hero_mega_option_image"></div></div>';
		break;
		
		case 'slider':
		return_html += '<div class="hero_option_six">Slider<div class="hero_mega_option_image"></div></div>';
		break;
		
		case 'map':
		return_html += '<div class="hero_option_seven">Map<div class="hero_mega_option_image"></div></div>';
		break;
		
		case 'images':
		return_html += '<div class="hero_option_eight">Images<div class="hero_mega_option_image"></div></div>';
		break;
		
	}	
	
	return return_html;
	
}

//content injections
function add_json_content(placement, mega_id, main_index, type){
	
	//variables
	var json_item = '';	
	
	//check the type
	switch(type){
		
		case 'post':
			//text json
			json_item = '{"megaMenuId":'+mega_id+',"termId":0, "numberPosts":3, "heading":"Post Heading", "headingUnderline":1, "headingAllow":1, "description":1, "descriptionCount":150, "featuredImage":1, "featuredSize":"small", "placement":'+placement+', "type":"'+type+'", "target":"_self", "content":"Some Content", "new":1, "deleted":0}';			
		break;
		
		case 'text':
			//post json
			json_item = '{"megaMenuId":'+mega_id+', "heading":"Text Heading", "text":"Some Text", "textCount":150, "textAlignment":"default", "headingUnderline":1, "paddingTop":5, "paddingBottom":5, "placement":'+placement+', "type":"'+type+'", "new":1, "deleted":0}';			
		break;
		
		case 'list':
			//post json
			json_item = '{"megaMenuId":'+mega_id+', "heading":"List Heading", "text":"Some Text", "textCount":150, "textAlignment":"default", "headingUnderline":1, "paddingTop":5, "paddingBottom":5, "placement":'+placement+', "type":"'+type+'", "new":1, "deleted":0, "mega_list_items":[]}';			
		break;
		
		case 'contact':
			//post json
			json_item = '{"megaMenuId":'+mega_id+', "heading":"Heading", "headingUnderline":1, "html":0, "form_html":"<p>html</p>", "shortcode":1, "form_shortcode":"[shortcode example=1]", "placement":'+placement+', "type":"'+type+'", "new":1, "deleted":0}';	
		break;
		
		case 'woo':
			//post json
			json_item = '{"megaMenuId":'+mega_id+', "heading":"Image Heading", "headingUnderline":1, "icon":"icon", "description":"desc", "productCategory":"all", "productToDisplay":4, "productHeading":1, "productPrice":1, "productDescription":0, "productImage":1, "productTarget":"_self", "placement":'+placement+', "type":"'+type+'", "new":1, "deleted":0}';			
		break;
		
		case 'slider':
			//post json
			json_item = '{"element":"value"}';			
		break;
		
		case 'map':
			//post json
			json_item = '{"megaMenuId":'+mega_id+', "heading":"Map Heading", "headingUnderline":1, "map":1, "map_html":"<p>html</p>", "shortcode":0, "map_shortcode":"[shortcode example=1]", "placement":'+placement+', "type":"'+type+'", "new":1, "deleted":0}';	
		break;
		
		case 'images':
			//post json
			json_item = '{"megaMenuId":'+mega_id+', "heading":"Image Heading", "headingUnderline":1, "text":"", "url":"#", "image":"", "displayType":"one", "placement":'+placement+', "type":"'+type+'", "new":1, "deleted":0}';	
		break;
		
	}
	
	var new_mega_col_item = JSON.parse(json_item);	
	
	global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff.push(new_mega_col_item);
	
	return global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff.length;
	
}

//enable mega item popups
function enable_option_clicks(placement){
	jQuery('.hero_menu_col_options ul li').off().on('click', function(){
		var the_popup_html = jQuery(this).data('popup');
		
		var col_placement = placement;
		var mega_id = jQuery(this).data('mega-id');
		var main_index = jQuery(this).data('main-index');
		var type = jQuery(this).data('popup');
		
		if(!mega_id){
			mega_id = 0;
		}
		
		var the_index = add_json_content(col_placement, mega_id, main_index, type) - 1;
		
		launch_hero_popup(
			'menus/html_snippets/'+the_popup_html+'.html',
			'popup_load',
			'popup_update',
			'popup_cancel',
			{placement: col_placement, id: mega_id, index: main_index, content_index: the_index, info: type, new_info: 'yes'}
		);
	});
}

//enable the content selector
function enable_content_panel(){
	jQuery('.btn_add_content').on('click', function(){
		jQuery('.options_'+jQuery(this).data('index')).fadeIn(200);
		jQuery('.options_'+jQuery(this).data('index')).attr('data-placement', jQuery(this).data('placement'));
		enable_option_clicks(jQuery(this).data('placement'));
		enable_close_panel(jQuery(this).data('index'));
	});
}

function enable_close_panel(index){
	jQuery('.hero_close_options').on('click', function(){
		jQuery('.options_'+index).fadeOut(200);
	});
}

//popup load function
function popup_load(data){
	
	//set popup data
	set_popup_data(data.index, data.content_index, data.info);
		
	//bind uploader
	bind_uploader(data.index, data.content_index);
	
}

//file uploader
function bind_uploader(index, content){
	
	var file_frame;
	
	var main_index = index;
	var content_index = content;
	
	jQuery('.media_uploader').off().on('click', function( event ){
	
		var the_input_value = jQuery('#'+jQuery(this).data('link'))
	
		event.preventDefault();
		
		if ( file_frame ) {
			file_frame.open();
			return;
		}
		
		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery( this ).data('title'),
			button: {
				text: jQuery( this ).data('text'),
			},
			multiple: false
		});
		
		file_frame.on( 'select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();
			the_input_value.val(attachment.sizes.full.url)
			jQuery(the_input_value).trigger('change');
		});
		
		file_frame.open();
		
	});
}


//set popup data
function set_popup_data(main_index, content_index, type){
	
	switch(type){
		case 'post':
			
			//variables
			var post_heading = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].heading;
			var post_heading_underline = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].headingUnderline;
			var post_category = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].termId;
			var post_numberofposts = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].numberPosts;
			var post_heading_enable = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].headingAllow;
			var post_description_enable = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].description;
			var post_char_count = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].descriptionCount;
			var post_featured_image = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].featuredImage;
			var post_featured_size = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].featuredSize;
			var post_target = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].target;
						
			//set heading
			if(post_heading){
				jQuery('#heading').val(post_heading);
			}
			
			//set heading underline
			if(post_heading_underline){
				if(jQuery('#headingUnderline').val() == post_heading_underline){ 
					jQuery('#headingUnderline').attr('checked', 'checked');
				}
			}
			
			//load categories into select
			jQuery(global_categories).each(function(index, element) {
                jQuery('#termId').append('<option value="'+element.cat_id+'">'+element.title+'</option>');
            });
			
			//load categories into select
			if(post_category){
				jQuery('#termId option').each(function(index, element) {
				   if(jQuery(this).val() == post_category){
					   jQuery(this).attr('selected', 'selected')
				   }
				});
			}
			
			//set heading
			if(post_numberofposts){
				jQuery('#numberPosts').val(post_numberofposts);
			}
			
			//set post heading allow
			if(post_heading_enable){
				if(jQuery('#headingAllow').val() == post_heading_enable){ 
					jQuery('#headingAllow').attr('checked', 'checked');
				}
			}
			
			//set post description allow
			if(post_description_enable){
				if(jQuery('#description').val() == post_description_enable){ 
					jQuery('#description').attr('checked', 'checked');
				}
			}
			
			//character count
			if(post_char_count){
				jQuery('#descriptionCount').val(post_char_count);
			}
			
			//featured image
			if(post_featured_image){
				if(jQuery('#featuredImage').val() == post_featured_image){ 
					jQuery('#featuredImage').attr('checked', 'checked');
				}
			}
			
			//featured size
			if(post_featured_size){
				jQuery('#featuredSize option').each(function(index, element) {
				   if(jQuery(this).val() == post_featured_size){
					   jQuery(this).attr('selected', 'selected')
				   }
				});
			}
			
			//featured size
			if(post_target){
				jQuery('#target option').each(function(index, element) {
				   if(jQuery(this).val() == post_target){
					   jQuery(this).attr('selected', 'selected')
				   }
				});
			}
			
		break;
		case 'text':
		
			//variables
			var text_heading = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].heading;			
			var text_heading_underline = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].headingUnderline;
			var text_text = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].text;
			var text_count = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].textCount;
			var text_alignment = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].textAlignment;
			var text_top = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].paddingTop;
			var text_bottom = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].paddingBottom;
			
			//set heading
			if(text_heading){
				jQuery('#heading').val(text_heading);
			}
			
			//set heading underline
			if(text_heading_underline){
				if(jQuery('#headingUnderline').val() == text_heading_underline){ 
					jQuery('#headingUnderline').attr('checked', 'checked');
				}
			}
			
			//set heading
			if(text_text){
				jQuery('#text').val(text_text);
			}
			
			//text count
			if(text_count){
				jQuery('#textCount').val(text_count);
			}
			
			//alignment
			if(text_alignment){
				jQuery('#textAlignment option').each(function(index, element) {
				   if(jQuery(this).val() == text_alignment){
					   jQuery(this).attr('selected', 'selected')
				   }
				});
			}
			
			//text count
			if(text_top){
				jQuery('#paddingTop').val(text_top);
			}
			
			//text count
			if(text_bottom){
				jQuery('#paddingBottom').val(text_bottom);
			}
						
		break;
		case 'list':
			
			//variables
			var list_heading = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].heading;			
			var list_heading_underline = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].headingUnderline;
			var list_text = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].text;
			var list_count = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].textCount;
			var list_alignment = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].textAlignment;
			var list_top = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].paddingTop;
			var list_bottom = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].paddingBottom;
						
			//enable list item select
			get_list_items(main_index, content_index);
			enable_list_items(main_index, content_index);
			
			//set heading
			if(list_heading){
				jQuery('#heading').val(list_heading);
			}
			
			//set heading underline
			if(list_heading_underline){
				if(jQuery('#headingUnderline').val() == list_heading_underline){ 
					jQuery('#headingUnderline').attr('checked', 'checked');
				}
			}
			
			//set heading
			if(list_text){
				jQuery('#text').val(list_text);
			}
			
			//text count
			if(list_count){
				jQuery('#textCount').val(list_count);
			}
			
			//alignment
			if(list_alignment){
				jQuery('#textAlignment option').each(function(index, element) {
				   if(jQuery(this).val() == list_alignment){
					   jQuery(this).attr('selected', 'selected')
				   }
				});
			}
			
			//text count
			if(list_top){
				jQuery('#paddingTop').val(list_top);
			}
			
			//text count
			if(list_bottom){
				jQuery('#paddingBottom').val(list_bottom);
			}
			
		break;
		case 'contact':
		
			//variables			
			var contact_heading = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].heading;			
			var contact_heading_underline = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].headingUnderline;
			var contact_html = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].html;
			var contact_form_html = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].formHtml;
			var contact_shortcode = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].shortcode;
			var contact_form_shortcode = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].formShortcode;
			
			//set heading
			if(contact_heading){
				jQuery('#heading').val(contact_heading);
			}
			
			//set heading underline
			if(contact_heading_underline){
				if(jQuery('#headingUnderline').val() == contact_heading_underline){ 
					jQuery('#headingUnderline').attr('checked', 'checked');
				}
			}
			
			//set html on off
			if(contact_html){
				if(jQuery('#html_switch').val() == contact_html){ 
					jQuery('#html_switch').attr('checked', 'checked');
				}
			}
			
			//set html
			if(contact_form_html){
				jQuery('#form_html').val(contact_form_html);
			}
			
			//set shortcode on off
			if(contact_shortcode){
				if(jQuery('#shortcode_switch').val() == contact_shortcode){ 
					jQuery('#shortcode_switch').attr('checked', 'checked');
				}
			}
			
			//set shortcode
			if(contact_form_shortcode){
				jQuery('#shortcode').val(contact_form_shortcode);
			}
			
		break;
		case 'woo':
			
			//variables			
			var prod_heading = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].heading;			
			var prod_heading_underline = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].headingUnderline;
			var prod_title = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].productHeading;
			var prod_desc = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].productDescription;
			var prod_price = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].productPrice;
			var prod_image = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].productImage;
			var prod_number = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].productToDisplay;
			var prod_category = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].productCategory;
			
			//set heading
			if(prod_heading){
				jQuery('#heading').val(prod_heading);
			}
			
			//set heading underline
			if(prod_heading_underline){
				if(jQuery('#headingUnderline').val() == prod_heading_underline){ 
					jQuery('#headingUnderline').attr('checked', 'checked');
				}
			}
			
			//set title
			if(prod_title){
				if(jQuery('#product_title').val() == prod_title){ 
					jQuery('#product_title').attr('checked', 'checked');
				}
			}
			
			//set description
			if(prod_desc){
				if(jQuery('#product_desc').val() == prod_desc){ 
					jQuery('#product_desc').attr('checked', 'checked');
				}
			}
			
			//set price
			if(prod_price){
				if(jQuery('#product_price').val() == prod_price){ 
					jQuery('#product_price').attr('checked', 'checked');
				}
			}
			
			//set image
			if(prod_image){
				if(jQuery('#product_image').val() == prod_image){ 
					jQuery('#product_image').attr('checked', 'checked');
				}
			}
			
			//number of products
			if(prod_number){
				jQuery('#numberProducts option').each(function(index, element) {
				   if(jQuery(this).val() == parseInt(prod_number)){
					   jQuery(this).attr('selected', 'selected')
				   }
				});
			}
			
			//category
			if(prod_category){
				jQuery('#categoryProduct option').each(function(index, element) {
				   if(jQuery(this).val() == prod_category){
					   jQuery(this).attr('selected', 'selected')
				   }
				});
			}
			
		break;
		case 'map':
			
			//variables			
			var map_heading = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].heading;			
			var map_heading_underline = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].headingUnderline;
			var map_map = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].map;
			var map_map_html = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mapHtml;
			var map_shortcode = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].shortcode;
			var map_map_shortcode = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mapShortcode;
			
			//set heading
			if(map_heading){
				jQuery('#heading').val(map_heading);
			}
			
			//set heading underline
			if(map_heading_underline){
				if(jQuery('#headingUnderline').val() == map_heading_underline){ 
					jQuery('#headingUnderline').attr('checked', 'checked');
				}
			}
			
			//set html on off
			if(map_map){
				if(jQuery('#html_switch').val() == map_map){ 
					jQuery('#html_switch').attr('checked', 'checked');
				}
			}
			
			//set html
			if(map_map_html){
				jQuery('#form_html').val(map_map_html);
			}
			
			//set shortcode on off
			if(map_shortcode){
				if(jQuery('#shortcode_switch').val() == map_shortcode){ 
					jQuery('#shortcode_switch').attr('checked', 'checked');
				}
			}
			
			//set shortcode
			if(map_map_shortcode){
				jQuery('#shortcode').val(map_map_shortcode);
			}
			
		break;
		case 'images':
			
			//variables			
			var image_heading = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].heading;			
			var image_heading_underline = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].headingUnderline;
			var image_url = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].url;
			var image_heading_two = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].imageHeading;
			var image_img = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].image;
			var image_text = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].text;
			var image_display = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].displayType;
			
			//set heading
			if(image_heading){
				jQuery('#heading').val(image_heading);
			}
			
			//set heading underline
			if(image_heading_underline){
				if(jQuery('#headingUnderline').val() == image_heading_underline){ 
					jQuery('#headingUnderline').attr('checked', 'checked');
				}
			}
			
			//set url
			if(image_url){
				jQuery('#url').val(image_url);
			}
			
			//set image location url
			if(image_img){
				jQuery('#hero_mega_image').val(image_img);
				jQuery('.hero_overview_img').css('background-image', 'url('+image_img+')');
			} else {
				jQuery('.hero_overview_img').hide();
			}
			
			//set image heading
			if(image_heading_two){
				jQuery('#imageHeading').val(image_heading_two);
			}
			
			//change: image
			jQuery('#hero_mega_image').on('change', function(){
				jQuery('.hero_overview_img').show();
				jQuery('.hero_overview_img').css('background-image', 'url('+jQuery('#hero_mega_image').val()+')');
			});
			
			//set description
			if(image_text){
				jQuery('#text').val(image_text);
			}	
			
			//set background hover type
			if(image_display){
				if(jQuery('#type_one').val() == image_display){ 
					jQuery('#type_one').attr('checked', 'checked');
				}
				if(jQuery('#type_two').val() == image_display){ 
					jQuery('#type_two').attr('checked', 'checked');
				}
				if(jQuery('#type_three').val() == image_display){ 
					jQuery('#type_three').attr('checked', 'checked');
				}
			}
			
			find_image_toggle();	
			
		break;
	}
	
}

//enable the list items select
function enable_list_items(main_index, content_index){
	
	//populate list item dropdowns	
	var select_type_html = '';
	
	jQuery.each(global_list_item_data, function(key, value) {	
		//add
		if(key != 'post_types'){
			switch(key){
				case 'pages':
					select_type_html += '<option value="'+key+'" data-type="post">'+key+'</option>';
				break
				case 'categories':
					select_type_html += '<option value="'+key+'" data-type="category">'+key+'</option>';
				break
			}			
		} else {			
			jQuery(global_list_item_data[key]).each(function(index, element) {
                
				select_type_html += '<option value="'+element.name+'" data-type="post" data-index="'+index+'" data-main-source="'+key+'">'+element.label+'</option>';
				
				if(element.type_categories){
					jQuery(element.type_categories).each(function(index, element_cat) {
						
						select_type_html += '<option value="'+element_cat.name+'" data-type="category" data-index="'+index+'" data-main-source="'+key+'">'+element_cat.label+'</option>';
					
					});
				}
				
            });
		}		
	});
	
	select_type_html += '<option value="custom" data-type="custom">Custom Item</option>';
	
	jQuery('#listType').append(select_type_html);

	update_select_component(jQuery('#listType'));
	
	//trigger the first item in select box
	jQuery('.listType .hero_dropdown .hero_drop_row:first').trigger('click');
	jQuery('.listType .hero_dropdown').hide();				
	
	//get the select value of the type dropdown
	jQuery('#listType option').each(function(index, element) {		
		
		if (jQuery(this).is(':selected')) {			
			
			var select_link_html = '';	
			
			jQuery(global_list_item_data[jQuery(this).val()]).each(function(index, element) {
				
				//variables
				var post_id = element.id;
				
				select_link_html += '<option data-key="pages" value="'+index+'" data-id="'+post_id+'">'+element.title+'</option>';
				
			});	
			
			jQuery('#listItem').append(select_link_html);			
			update_select_component(jQuery('#listItem'));	
					
		}
				
	});
	
	//enable the change
	//change: target
	jQuery('.listType .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery('#listType').trigger('change');		
	});	
	
	jQuery('#listType').on('change', function(){
		
		jQuery('.listItem_check').show();
		
		//clear html
		jQuery('#listItem').html('');
		jQuery('.listItem .hero_dropdown .hero_drop_row').html('');
		
		var select_link_html = '';
		
		var the_source = jQuery(this).find("option:selected").data('main-source');
		var the_type = jQuery(this).find("option:selected").data('type');
		var the_cat_index = jQuery(this).find("option:selected").data('index');
			
		if(!the_source){
			if(the_type == 'post'){
				jQuery(global_list_item_data[jQuery(this).val()]).each(function(index, element) {
					
					//variables
					var post_id = element.id;
					
					select_link_html += '<option data-key="pages" value="'+index+'" data-id="'+post_id+'">'+element.title+'</option>';
					
				});	
			} else if(the_type == 'category'){
				jQuery(global_list_item_data[jQuery(this).val()]).each(function(index, element) {
					
					//variables
					var term_id = element.cat_id;
					var taxonomy = element.taxonomy;
					
					select_link_html += '<option data-key="categories" value="'+index+'" data-id="'+term_id+'" data-taxonomy="'+taxonomy+'">'+element.title+'</option>';
					
				});
			} else if(the_type == 'custom'){
				jQuery('.listItem_check').hide();
			}
		} else {
			if(the_type == 'post'){
				jQuery(global_list_item_data[the_source][0].posts).each(function(index, element) {
					
					//variables
					var post_id = element.ID;
					
					select_link_html += '<option value="'+index+'" data-key="posts" data-id="'+post_id+'">'+element.post_title+'</option>';
					
				});	
			} else {
				jQuery(global_list_item_data[the_source][0].type_categories[the_cat_index].terms).each(function(index, element) {
					
					//variables
					var term_id = global_list_item_data[the_source][0].type_categories[the_cat_index].terms[index].term_id;
					var taxonomy = global_list_item_data[the_source][0].type_categories[the_cat_index].terms[index].taxonomy;
					
					select_link_html += '<option value="'+index+'" data-key="type_categories" data-id="'+term_id+'" data-taxonomy="'+taxonomy+'">'+element.cat_name+'</option>';
					
				});	
			}
		}	
			
		jQuery('#listItem').append(select_link_html);			
		update_select_component(jQuery('#listItem'));
			
	});	
	
	//enable the add button
	enable_add_list_item(main_index, content_index);
	
}

//enable add list item button
function enable_add_list_item(main_index, content_index){
	
	//add item
	jQuery('.add_list_item').off().on('click', function(){
		
		var the_key;
		var the_index;		
		var the_sub_index;
		var the_sub_key;
		
		var post_id = 0;
		var term_id = 0;
		var taxonomy = '_na';
		
		//get the select value of the Type dropdown
		jQuery('#listType option').each(function(index, element) {
			if (jQuery(this).is(':selected')) {
				var the_source = jQuery(this).data('main-source');
				if(!the_source){
					the_key = jQuery(this).val();
				} else {
					the_key = 'post_types';
					the_index = jQuery(this).data('index');
				}				
			}
		});
	
		//get the select value of the Item dropdown
		jQuery('#listItem option').each(function(index, element) {
			if (jQuery(this).is(':selected')) {
				var the_key = jQuery(this).data('key');
				if(the_key == 'pages'){
					the_index = jQuery(this).val();
				} else if(the_key == 'categories') {
					the_index = jQuery(this).val();
				} else if(the_key == 'posts') {
					the_sub_index = jQuery(this).val();
					the_sub_key = the_key;
				} else if(the_key == 'type_categories') {
					the_sub_index = jQuery(this).val();
					the_sub_key = the_key;
				}
			}
		});
		
		//variables
		var list_title;
		var list_link;
		
		//some variables for the json
		switch(the_key){
			case 'pages':
				list_title = global_list_item_data[the_key][the_index].title;
				list_link = global_list_item_data[the_key][the_index].perma;
				post_id = global_list_item_data[the_key][the_index].id;
			break;
			case 'categories':
				list_title = global_list_item_data[the_key][the_index].title;
				list_link = global_list_item_data[the_key][the_index].link;
				term_id = global_list_item_data[the_key][the_index].cat_id;
				taxonomy = global_list_item_data[the_key][the_index].taxonomy;
			break;
			case 'custom':
				list_title = 'My Title';
				list_link = '#';
			break;
			case 'post_types':
				if(the_sub_key == 'posts'){
					list_title = global_list_item_data[the_key][the_index][the_sub_key][the_sub_index].post_title;
					list_link = global_list_item_data[the_key][the_index][the_sub_key][the_sub_index].guid;
					post_id = global_list_item_data[the_key][the_index][the_sub_key][the_sub_index].ID;
				} else {
					list_title = global_list_item_data[the_key][the_index][the_sub_key][0]['terms'][the_sub_index].name;
					list_link = global_list_item_data[the_key][the_index][the_sub_key][0]['terms'][the_sub_index].taxonomy;
					term_id = global_list_item_data[the_key][the_index][the_sub_key][0]['terms'][the_sub_index].term_id;
					taxonomy = global_list_item_data[the_key][the_index][the_sub_key][0]['terms'][the_sub_index].taxonomy;
				}
			break;
		}
		
		var list_item_index = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items.length; 
		
		//list item json
		json_list_item = '{"listId":0, "name":"'+list_title+'", "type":"'+the_key+'", "alt":"alt", "url":"'+list_link+'", "postId":"'+post_id+'", "termId":"'+term_id+'", "taxonomy":"'+taxonomy+'", "target":"_self", "icon":0, "desc":1, "description":"Add your content.", "iconSize":"12", "iconColor":"#CCC", "order":'+list_item_index+', "new":1, "deleted":0}';			
			
		var new_list_item = JSON.parse(json_list_item);	
		
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items.push(new_list_item);
		
		add_new_list_item(main_index, content_index, list_item_index);
	
	});
	
}

//get list items
function get_list_items(main_index, content_index){
	//console
	if(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items.sort(sort_items_array);
		jQuery(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items).each(function(index, element) { 	
			if(element.deleted != 1){			
				add_new_list_item(main_index, content_index, index);
			}
		});
	};
}

//add list items
function add_new_list_item(main_index, content_index, list_item_index){
	
	//check if its a basic item or mega menu      
	var the_url = plugin_url + 'views/menus/html_snippets/list_item.php';
	//load the html
	jQuery.ajax({
		url: the_url,
		data: {
			index: list_item_index,
			listId: 0,
			mainIndex: main_index,
			contentIndex: content_index,
			url: plugin_url
		},
        async: false,
		dataType: "html"
	}).done(function(data){	
		//append html
		jQuery('.load_icon_list').append(data);
		enable_icon_select();
		set_list_item_data(main_index, content_index, list_item_index);	
		enable_list_sorting(main_index, content_index, list_item_index);
	}).fail(function(){
		 //page error
	});	
	
}

//enable list item sorting
function enable_list_sorting(main_index, content_index, list_item_index){
	
	//sort
	jQuery(".load_icon_list").sortable({
		placeholder: "list_placeholder",
		revert: false,	
		forcePlaceholderSize: true,		
		handle: '.hero_item_drag',
		stop: function(){
			set_list_order(main_index, content_index, list_item_index);
		}
	});	
	
}

//set order
function set_list_order(main_index, content_index, list_item_index){
	
	//set positions of new order
	jQuery('.load_icon_list li').each(function(index, element) {
		var the_index = jQuery(this).data('index');
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[the_index].order = index;
    });
	
	flag_save_required('save_clicked',{"status": true});
	
}

//setup data
function set_list_item_data(main_index, content_index, list_item_index){
	
	//delete column
	jQuery('.hero_delete_list').off().on('click', function(){
		
		var main_idx = jQuery(this).data('main-index');
		var content_idx = jQuery(this).data('content-index');
		var list_item_idx = jQuery(this).data('id');
		
		//confirm delete
		if(window.confirm('Are you sure you want to delete?')){
						
			//set item to deleted
			global_menu_obj.nav_items[main_idx].mega_menus[0].mega_stuff[content_idx].mega_list_items[list_item_idx].deleted = 1;
			
			jQuery(this).parents('.hero_list_sort_item').remove();
			
		} else {
			//do nothing
		};		
	});
	
	//set name
	jQuery('#list_heading_'+list_item_index).html(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].name);	
	jQuery('#list_name_'+list_item_index).val(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].name);
	jQuery('#list_alt_'+list_item_index).val(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].alt);		
	jQuery('#list_url_'+list_item_index).val(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].url);
	jQuery('#list_item_order_'+list_item_index).val(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].order);
	
	//set type label
	
	var post_id = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].postId;
	var tax = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].taxonomy;
	var type = global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].type;
	
	if(post_id != 0){
		jQuery('.the_list_input_'+list_item_index).hide();
		jQuery('#item_type_'+list_item_index).html('POST');	
	} else {
		if(type != 'custom'){			
			jQuery('.the_list_input_'+list_item_index).hide();
			jQuery('#item_type_'+list_item_index).html('CATEGORY: ' + tax);	
		} else {
			jQuery('.the_list_input_'+list_item_index).show();
			jQuery('#item_type_'+list_item_index).html('CUSTOM');
		}
	}
	
	jQuery('#list_target_'+list_item_index+' option').each(function(idx, el) {
	   if(jQuery(this).val() == global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].target){
		   jQuery(this).attr('selected', 'selected')
	   }
	});
	
	if(jQuery('#list_icon_'+list_item_index).val() == global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].icon){ 
		jQuery('#list_icon_'+list_item_index).attr('checked', 'checked');
	}
	
	if(typeof(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconContent) !== 'undefined'){
		jQuery('#list_icon_content_'+list_item_index).val(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconContent);
	} else {
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconContent = null;
	}
	jQuery('.the_list_icon_'+list_item_index).children('#hero_inner_icon').attr('class', global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconContent);
		
	jQuery('#list_icon_size_'+list_item_index+' option').each(function(idx, el) {
	   if(jQuery(this).val() == global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconSize){
		   jQuery(this).attr('selected', 'selected')
	   }
	});
	
	if(jQuery('#list_desc_'+list_item_index).val() == global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].desc){ 
		jQuery('#list_desc_'+list_item_index).attr('checked', 'checked');
	}
	
	jQuery('#list_description_'+list_item_index).html(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].description);	
	
	//jQuery('#list_icon_color_'+list_item_index).val(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconColor);
		
	//switch components
	switch_components();
	
	//bind_field_convert();
	find_small_toggle_elements();
	
	//enable update
	enable_list_update_settings(main_index, content_index, list_item_index);
	
	enable_toggle();
	
}

//enable update for list items
function enable_list_update_settings(main_index, content_index, list_item_index){
	
	//change: name
	jQuery('#list_name_'+list_item_index).on('change keyup', function(){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].name = jQuery(this).val();
		jQuery('#list_heading_'+list_item_index).html(global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].name);
		flag_save_required('save_clicked',{"status": true});
	});
	//change: alt/title
	jQuery('#list_alt_'+list_item_index).on('change keyup', function(){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].alt = jQuery(this).val();
		flag_save_required('save_clicked',{"status": true});
	});
	//change: url
	jQuery('#list_url_'+list_item_index).on('change keyup', function(){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].url = jQuery(this).val();
		flag_save_required('save_clicked',{"status": true});
	});
	//change: target
	jQuery('.list_target_'+list_item_index+' .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery('#list_target_'+list_item_index).trigger('change');		
	});	
	jQuery('#list_target_'+list_item_index).on('change', function(){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].target = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked',{"status": true});
	});	
	//change: icon enable
	jQuery('#list_icon_'+list_item_index).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].icon = jQuery(this).val() : global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].icon = 0;
		flag_save_required('save_clicked',{"status": true});
	});
	//change: icon content
	jQuery('#list_icon_content_'+list_item_index).on('change keyup', function(){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconContent = jQuery(this).val();
		jQuery('.the_list_icon_'+list_item_index).children('#hero_inner_icon').attr('class', jQuery(this).val());
		flag_save_required('save_clicked',{"status": true});
	});
	//change: icon size
	jQuery('.list_icon_size_'+list_item_index+' .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery('#list_icon_size_'+list_item_index).trigger('change');		
	});	
	jQuery('#list_icon_size_'+list_item_index).on('change', function(){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconSize = jQuery(this).children('option:selected').val();
		flag_save_required('save_clicked',{"status": true});
	});	
	/*//change: icon color
	jQuery('#list_icon_color_'+list_item_index).on('change keyup', function(){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].iconColor = jQuery(this).val();
		flag_save_required('save_clicked',{"status": true});
	});*/
	//change: description enable
	jQuery('#list_desc_'+list_item_index).on('change', function(){
		jQuery(this).prop('checked') ? global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].desc = jQuery(this).val() : global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].desc = 0;
		flag_save_required('save_clicked',{"status": true});
	});
	//change: desription
	jQuery('#list_description_'+list_item_index).on('change keyup', function(){
		global_menu_obj.nav_items[main_index].mega_menus[0].mega_stuff[content_index].mega_list_items[list_item_index].description = jQuery(this).val();
		flag_save_required('save_clicked',{"status": true});
	});
	
}

//enable/disable options
function open_close_options(main_index){
	
	var col_content_length = jQuery('.mega_cols_'+main_index).find('[data-iscontent="yes"]').length;
	
	var ele = jQuery('#hero_options_'+main_index);
	
	jQuery('#hero_options_'+main_index).children('.hero_option_items').children('div').each(function(index, element) {
        
		if(col_content_length <= jQuery(this).data('cols')){
			
			jQuery(this).css({ opacity: 1 });
			jQuery(this).removeAttr('data-tooltip');
			jQuery(this).removeClass('disable_option');
			
			enable_layout_clicks(ele, main_index);
			
		} else {
			jQuery(this).unbind('click');
			jQuery(this).css({ opacity: 0.3 });			
			//jQuery(this).attr('data-tooltip', 'To select this layout, remove one of the current content blocks.');
			jQuery(this).addClass('disable_option');
			//jQuery(this).addClass('hplugin-tooltip');
		}
		
    });
		
}

//save popup function
function popup_update(data){
	
	var col_array = global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff;	
	var layout = global_menu_obj.nav_items[data.index].mega_menus[0].layout;
	
	switch(data.info){
		case 'post':
			//update
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].heading = jQuery('#heading').val();			
			jQuery('#headingUnderline').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = parseInt(jQuery('#headingUnderline').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].termId = parseInt(jQuery('#termId').children('option:selected').val());			
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].numberPosts = parseInt(jQuery('#numberPosts').val());
			jQuery('#headingAllow').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingAllow = parseInt(jQuery('#headingAllow').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingAllow = 0;			
			jQuery('#description').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].description = parseInt(jQuery('#description').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].description = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].descriptionCount = parseInt(jQuery('#descriptionCount').val());
			jQuery('#featuredImage').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].featuredImage = parseInt(jQuery('#featuredImage').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].featuredImage = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].featuredSize = jQuery('#featuredSize').val();
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].target = jQuery('#target').val();			
		break;
		case 'text':
			//update			
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].heading = jQuery('#heading').val();	
			jQuery('#headingUnderline').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = parseInt(jQuery('#headingUnderline').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].text = jQuery('#text').val();	
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].textCount = parseInt(jQuery('#textCount').val());
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].textAlignment = jQuery('#textAlignment').children('option:selected').val();
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].paddingTop = jQuery('#paddingTop').val();
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].paddingBottom = jQuery('#paddingBottom').val();
		break;
		case 'list':
			//update			
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].heading = jQuery('#heading').val();	
			jQuery('#headingUnderline').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = parseInt(jQuery('#headingUnderline').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].text = jQuery('#text').val();	
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].textCount = parseInt(jQuery('#textCount').val());
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].textAlignment = jQuery('#textAlignment').children('option:selected').val();
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].paddingTop = jQuery('#paddingTop').val();
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].paddingBottom = jQuery('#paddingBottom').val();
		break;
		case 'contact':			
			//update			
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].heading = jQuery('#heading').val();	
			jQuery('#headingUnderline').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = parseInt(jQuery('#headingUnderline').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = 0;
			jQuery('#html_switch').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].html = parseInt(jQuery('#html_switch').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].html = 0;				
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].formHtml = jQuery('#form_html').val();			
			jQuery('#shortcode_switch').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].shortcode = parseInt(jQuery('#shortcode_switch').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].shortcode = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].formShortcode = jQuery('#shortcode').val();
		break;
		case 'woo':
			//update			
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].heading = jQuery('#heading').val();	
			jQuery('#headingUnderline').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = parseInt(jQuery('#headingUnderline').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = 0;
			jQuery('#product_title').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productHeading = parseInt(jQuery('#product_title').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productHeading = 0;
			jQuery('#product_desc').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productDescription = parseInt(jQuery('#product_desc').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productDescription = 0;
			jQuery('#product_price').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productPrice = parseInt(jQuery('#product_price').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productPrice = 0;
			jQuery('#product_image').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productImage = parseInt(jQuery('#product_image').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productImage = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productToDisplay = jQuery('#numberProducts').children('option:selected').val();
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].productCategory = jQuery('#categoryProduct').children('option:selected').val();			
		break;
		case 'slider':
			//nothing			
		break;
		case 'map':
			//update		
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].heading = jQuery('#heading').val();	
			jQuery('#headingUnderline').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = parseInt(jQuery('#headingUnderline').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = 0;
			jQuery('#html_switch').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].map = parseInt(jQuery('#html_switch').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].map = 0;				
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].mapHtml = jQuery('#form_html').val();
			jQuery('#shortcode_switch').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].shortcode = parseInt(jQuery('#shortcode_switch').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].shortcode = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].mapShortcode = jQuery('#shortcode').val();
		break;
		case 'images':
			//update		
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].heading = jQuery('#heading').val();	
			jQuery('#headingUnderline').prop('checked') ? global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = parseInt(jQuery('#headingUnderline').val()) : global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].headingUnderline = 0;
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].url = jQuery('#url').val();	
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].imageHeading = jQuery('#imageHeading').val();	
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].image = jQuery('#hero_mega_image').val();			
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].text = jQuery('#text').val();
			
			var type_one = jQuery('#type_one');
			var type_two = jQuery('#type_two');
			var type_three = jQuery('#type_three');			
			
			if(jQuery(type_one).prop('checked')){
				global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].displayType = jQuery(type_one).val();
			}
			if(jQuery(type_two).prop('checked')){
				global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].displayType = jQuery(type_two).val();
			}
			if(jQuery(type_three).prop('checked')){
				global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff[data.content_index].displayType = jQuery(type_three).val();
			}
		
		break;
	}
	
	//fade out options
	jQuery('.options_'+data.index).fadeOut(200);
	
	//generate cols
	generate_cols(layout, data.index, col_array);
	
	//open/close layout options
	open_close_options(data.index);
	
	//flag save required
	flag_save_required('save_clicked',{"status": true});
	
}

//cancel popup function
function popup_cancel(data){
	
	//variables
	var layout = global_menu_obj.nav_items[data.index].mega_menus[0].layout;
	var the_current_length = global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff.length;
	
	//switch
	switch(data.new_info){
		
		//if the content already exists
		case 'no':
			//don't apple any changes
		break;
		
		//if the content is new
		case 'yes':
			//remove the newly creaed json node in the array
			global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff.splice(data.content_index, 1);
		break;
		
	}
	
	var col_array = global_menu_obj.nav_items[data.index].mega_menus[0].mega_stuff;	
	
	//generate cols
	generate_cols(layout, data.index, col_array);
	
	//fade out options
	jQuery('.options_'+data.index).fadeOut(200);
	
	//flag save required
	flag_save_required('save_clicked',{"status": true});
	
}