//SETTINGS.INTEGRATION VIEW

//load
jQuery(function(){
	//functions
	load_locations();
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
	jQuery('.hmenu_integrate_pages').html('[hmenu id='+global_menu_obj.menu.menuId+']');
	jQuery('.hmenu_integrate_do').html('&lt;?php echo do_shortcode( "[hmenu id='+global_menu_obj.menu.menuId+']" ); ?&gt;');
});

//set settings
function set_settings(){	
	
	//page variables	
	var m_location = global_menu_obj.menu.overwrite;
	
	//set location
	if(m_location){
		jQuery('#menu_location option').each(function(index, element) {
           	if(jQuery(this).val() == m_location){
			   	jQuery(this).attr('selected', 'selected')
		   	}
        });
	}
	
	//switch components
	switch_components();
	
	//enable update settings
	enable_update_settings();
	
}

//enable update settings
function enable_update_settings(){	
	
	//all the controls
	var control_menu_location = jQuery('#menu_location');
	var the_status = true;
	
	//change: location
	jQuery('.menu_location .hero_dropdown .hero_drop_row').on('click', function(){
		jQuery(control_menu_location).trigger('change');		
	});	
	jQuery(control_menu_location).on('change', function(){		
		global_menu_obj.menu.overwrite = jQuery(this).children('option:selected').val();
		//check if any other menu has same location
		jQuery(global_menu_obj.all_menus).each(function(index, element) {
			if(element.overwrite == global_menu_obj.menu.overwrite && global_menu_obj.menu.menuId != element.menuId){
				//clear previous overwrite
				global_menu_obj.all_menus[index].overwrite = '';		
				
				var error_message = 'Caution: You have just replaced ' + global_menu_obj.all_menus[index].name + ' with ' + global_menu_obj.menu.name + ' at the following location: ' + global_menu_obj.menu.overwrite;
				
				jQuery('.hmenu_site_location_inner').html(error_message);
						
				var the_height = jQuery('.hmenu_site_location_inner').height() + 30;
				jQuery('.hmenu_site_location').animate({
					height:the_height
				},100);	
				
			} else {
				//any
			}
        });
		flag_save_required('save_clicked');
	});
	
}

//

//set settings
function load_locations(){	

	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_load_locations'
		},
		dataType: "json"
	}).done(function(data){			
		if(data){			
			load_location_html(data);
		}
	}).fail(function(){
		 //page error
	});

}

function load_location_html(data){
	var option_html = '';
	jQuery(data.locations).each(function(index, element) {
        option_html += '<option value="'+element.location+'">'+element.location+'</option>';
    });
	jQuery('#menu_location').append(option_html);
	set_settings();	
	update_select_component(jQuery('#menu_location'));	
}










