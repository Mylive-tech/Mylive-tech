//DASHBOARD VIEW CORE
var global_menus;

//load
jQuery(function(){
	populate_dashboard_containers(); //populate dashboard containers
	bind_dash_btn_listeners(); //bind dashboard button listeners
	execute_menu_load();//execute menu load	
});

//populate dashboard containers
function populate_dashboard_containers(){
	jQuery('#plugin_version').html(plugin_version);
	jQuery('#plugin_last_update').html(plugin_last_updated);
	jQuery('#plugin_release_date').html(plugin_first_release);
	jQuery('#plugin_title').html(plugin_friendly_name);
	jQuery('#plugin_description').html(plugin_friendly_description);
}

//bind dashboard button listeners
function bind_dash_btn_listeners(){
	jQuery('#add_new_example_element_btn').on('click', function(){
		//alert('You clicked the add button');
	});
}

//bind dashboard button listeners
function execute_menu_load(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_load_menus'
		},
		dataType: "json"
	}).done(function(data){
		if(data.menus.length !== 0){
			build_menu_html(data);
		} else {			
			build_error_html();
		}
		enable_delete();
	}).fail(function(){
	});
}

//setup menu list html
function build_menu_html(data){	
	var list_html = '';
	jQuery(data.menus).each(function(index, element) {
		var json_object = {"menuId":element.menuId};
        list_html += '<div class="hero_col_12 hero_menu_row" id="hero_row_item_'+element.menuId+'">';
			list_html += '<div class="hero_col_4 hmenu_dash_name" data-json="'+ encodeURIComponent(JSON.stringify(json_object)) +'" onclick="load_sidebar_dropdown_view(jQuery(this),\'dropdown_menus\',\'load_edit\');"><span>'+element.name+'</span></div>';
			list_html += '<div class="hero_col_4"><span><input class="hero_ctc hero_red" style="width:100%;" onclick="jQuery(this).select();" type="text" value="[hmenu id='+ element.menuId +']" readonly></span></div>';
			list_html += '<div class="hero_col_4">';
				list_html += '<div class="hero_edits rounded_20">';
					list_html += '<div class="hero_edit_item" data-json="'+ encodeURIComponent(JSON.stringify(json_object)) +'" onclick="load_sidebar_dropdown_view(jQuery(this),\'dropdown_menus\',\'load_edit\');" style="background-image:url('+plugin_url+'/assets/images/admin/edit_icon.png)"></div>';
					list_html += '<div class="hero_edit_item hero_delete_menu" data-id="'+element.menuId+'" style="background-image:url('+plugin_url+'/assets/images/admin/delete_icon.png)"></div>';
				list_html += '</div>';
			list_html += '</div>';
		list_html += '</div>';
    });
	jQuery('.hero_misc_load').html(list_html);
}

function enable_delete(){
	
	//get the delete click
	jQuery('.hero_delete_menu').off().on('click', function(){
		
		//current item data		
		var menu_id = jQuery(this).data('id');
		
		if(window.confirm('Are you sure you want to delete the menu? Once you click "OK" all the magic you created will no longer be available!')){
				
			//delete menu
			jQuery.ajax({
				url: ajax_url,
				type: "POST",
				data: {
					'action': 'hmenu_run_delete_menu',
					'id': menu_id
				},
				dataType: "json"
			}).done(function(data){
				jQuery('#hero_row_item_'+data.menu_id).remove();
				jQuery('#sub_item_row_'+data.menu_id).remove();
				if(jQuery('.hero_menu_row').length == 0){
					build_error_html();
				}
			}).fail(function(){
			});	
				
		} else {
			//do nothing
		}
		
	});
}

//setup menu list html
function build_error_html(){	
	var status_html = '';
	status_html += '<span class="hero_error_label">No menus have been found.</span>';	    
	jQuery('.hero_misc_load').html(status_html);
}

