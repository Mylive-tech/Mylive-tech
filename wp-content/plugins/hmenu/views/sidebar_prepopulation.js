//SIDEBAR PRE_POPULATION

//load
/*
	note: this method is required by the core framework and is called when the plugin is initialised.
*/
var add_new_active = true;
var new_menu_added = false;

function prepopulate_sidebar_elements(){
	//get menus
	get_menus();
	//bind buttons
	jQuery('.hero_sidebar').on('click','#dropdown_menu_btn', function(){
		add_new_menu_html();
	});
}

//get menus
function get_menus(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_load_menus'
		},
		dataType: "json"
	}).done(function(data){
		build_sub_menu_html(data);
		if(new_menu_added){
			flash_and_proceed();
		}
	}).fail(function(){
		//page error
		console_log('Menus not found.');
	});
}

//highlight flash for latest menu
function flash_and_proceed(){
	jQuery('#dropdown_menus li').first().animate({
		'background-color': '#A8CE83',
		'color': '#FFF'
	}, 500, function(){
		jQuery(this).animate({
			'background-color': '#F1F1F1',
			'color': '#888888'
		})
	});
	//trigger click on menu item
	jQuery('#dropdown_menus li').first().trigger('click');
}

//bind listeners
function bind_insert_menu_listener(){
	jQuery('.add_new_main_menu').on('click', function(){
		insert_main_menu();
	});	
	jQuery("#insert_menu_form input").keypress(function(event) {
		if (event.which == 13) {
			event.preventDefault();
			insert_main_menu();
		}
	});
}

//insert menu
function insert_main_menu(){
	jQuery('#add_new_menu').blur();
	animate_add_closed();
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_transfer_menu',
			'form_data': jQuery('#insert_menu_form').serialize()
		},
		dataType: "json"
	}).done(function(data){
		if(data.status){
			show_message('success', 'Menu Added', 'Your menu is ready to be used.');
			animate_add_closed();
			add_new_active = true;
			reset_sidebar_height();
			get_menus();
			new_menu_added = true;
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
		console_log('Menus not found.');
	});
}

//reset the sidebar height
function reset_sidebar_height(){
	var new_height = jQuery('#dropdown_menus').height() + 38;
	jQuery('#dropdown_menus').animate({
		'height': new_height + 'px'
	}, 300)
}

//html for the sidebar menu items
function build_sub_menu_html(data){
	//remove all items and reload
	jQuery('.dropdown_submenu_holder li').remove();
	jQuery(data.menus).each(function(index, element) {
		//holder id, title, JSON object, callback
		add_sidebar_element('dropdown_menus', element.menuId, element.name, {"menuId":element.menuId}, 'load_edit');
    });
}

//html input field to add new menu
function add_new_menu_html(){
	var the_html = '';
	if(add_new_active){
		the_html += '<div class="hero_add_new">';
			the_html += '<div class="hero_new_wrap">';
				the_html += '<form id="insert_menu_form">';
					the_html += '<input type="text" data-size="lrg" placeholder="Menu Name" name="add_new_menu" id="add_new_menu">';
					the_html += '<div class="hero_sidebar_button size_11 rounded_3 hero_white add_new_main_menu" id="add_new_menu_btn">Add</div>';
				the_html += '</form>';
			the_html += '</div>';
		the_html += '</div>';
		jQuery(the_html).insertAfter(jQuery('#dropdown_menus'));
		animate_add_open();
		bind_insert_menu_listener();
	} else {
		//dont add again
	}	
	add_new_active = false;
}

//animate open add new
function animate_add_open(){
	jQuery('#add_new_menu').focus();
	jQuery('.hero_add_new').animate({
		'height': '50px'		
	}, 500);			
}

//animate add closed
function animate_add_closed(){
	jQuery('.hero_add_new').animate({
		'height': '0'		
	}, 500, function() {
    	jQuery('.hero_add_new').remove();
  });			
}
