//STYLING.ICONS VIEW

//vars
var font_obj;

//load
jQuery(function(){
	//functions
	load_iframe();
	execute_font_load('icons');
	set_current_header_label('Currently Editing:',global_menu_obj.menu.name);
});

//add browse trigger
function add_browse_trigger(){
	//load core view
	jQuery('#add_more_btn').on('click', function(){	
		jQuery('#iframe').contents().find('input[type=file]').trigger('click');
	});
}

//load iframe for pack upload
function load_iframe(){
	load_secure_iframe('inc/upload.form.php', 100, '.file_uploader');
}

//upload process complete
function process_complete(response){
	if(response){
		show_message('success', 'Font Pack Added', 'The new font pack was successfully added and ready for use.');
	} else {
		show_message('error', 'Error Message', 'The font pack you are trying to upload already exists.');
	}
	//load core view
	reload_sub_view('menu_sub_icons', 'menus/','styling.icons');
}

//error message for failed upload
function error_font_process(){
	show_message('error', 'Error Message', 'Something went wrong, please try again.');
}

//load file name
function load_file_name(status){
	jQuery('.file_upload_status').append(status);
	if(status){
		show_message('success', 'Font Pack Added', 'The new font pack was successfully added and ready for use.');
	} else {
		show_message('error', 'Error Message', 'The font pack you are trying to upload already exists.');
	}
	//load core view
	reload_sub_view('menu_sub_icons', 'menus/','styling.icons');
}

//load icon messages
function load_icon_message(msg){
	jQuery('.icons_load_here').html(msg);
}

//load icons click
function load_icons_click(){
	var the_options = jQuery('.icon_set_select .hero_dropdown .hero_drop_row');
	jQuery(the_options).each(function(index, element) {
        jQuery(this).on('click', function(){
			load_icons(jQuery(this).data('value'), 'icons');
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
	
	jQuery('#icon_set_select').append(select_html);
	
	update_select_component(jQuery('#icon_set_select'));
	
}


