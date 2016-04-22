//TOGGLE MANAGER

//toggle elements
function find_toggle_elements(){
	jQuery('input').each(function(index, element) {
		if(jQuery(this).data('toggler')){
			var the_toggle_section_div = jQuery(this).parents('.hero_section_toggle');
			set_toggle_sections(element, the_toggle_section_div);
		}        
    });
}

//activate the sliding ability
function set_toggle_sections(element, the_toggle_section_div){
	
	if(jQuery(element).is(':checked')){
		open_section(the_toggle_section_div);
	} else {
		close_section(the_toggle_section_div);
	}	
	jQuery(element).on('click', function(){
		if(element.type == 'radio'){
			jQuery('input[name="'+element.name+'"]').each(function(index, element) {	
				if(jQuery(this).is(':checked')){
					open_section(the_toggle_section_div);
				} else {
					close_section(the_toggle_section_div);
				}
			});
		} else if(element.type == 'checkbox') {			
			if(jQuery(this).is(':checked')){
				open_section(the_toggle_section_div);
			} else {			
				close_section(the_toggle_section_div);
			}
		}
	});
}

//open section
function open_section(the_toggle_section_div){
	jQuery(the_toggle_section_div).css({
		'display': 'table',
		'overflow': 'auto'
	});
}

//close section
function close_section(the_toggle_section_div){
	jQuery(the_toggle_section_div).css({
		'display': 'block',
		'overflow': 'hidden',
		'height': '70px'
	});
}

//small toggle elements
function find_small_toggle_elements(){
	jQuery('input').each(function(index, element) {
		if(jQuery(this).data('smltoggler')){
			var the_small_toggle_section_div = jQuery('.'+jQuery(this).data('smltoggler'));
			set_small_toggle_sections(element, the_small_toggle_section_div);
		}        
    });
}

//activate the sliding ability
function set_small_toggle_sections(element, the_small_toggle_section_div){
	
	if(jQuery(element).is(':checked')){
		open_small_section(the_small_toggle_section_div);
	} else {
		close_small_section(the_small_toggle_section_div);
	}	
	jQuery(element).on('click', function(){
		if(element.type == 'radio'){
			jQuery('input[name="'+element.name+'"]').each(function(index, element) {	
				if(jQuery(this).is(':checked')){
					open_small_section(the_small_toggle_section_div);
				} else {
					close_small_section(the_small_toggle_section_div);
				}
			});
		} else if(element.type == 'checkbox') {			
			if(jQuery(this).is(':checked')){
				open_small_section(the_small_toggle_section_div);
			} else {			
				close_small_section(the_small_toggle_section_div);
			}
		}
	});
}

//open small section
function open_small_section(the_small_toggle_section_div){
	jQuery(the_small_toggle_section_div).css({
		'display': 'block'
	});
}

//close small section
function close_small_section(the_small_toggle_section_div){
	jQuery(the_small_toggle_section_div).css({
		'display': 'none'
	});
}

//small toggle elements
function find_image_toggle(){
	jQuery(jQuery('input[data-toggleimage="true"]')).each(function(index, element) {		
		if(jQuery(this).data('toggleimage')){			
			var toggle_image = jQuery('.image_'+jQuery(this).attr('id'));
			toggle_images(element, toggle_image);
		}  
    });
}

//activate the sliding ability
function toggle_images(element, toggle_image){
	
	if(jQuery(element).is(':checked')){
		show_image(jQuery('.image_'+jQuery(element).attr('id')));
	} else {
		hide_image(jQuery('.image_'+jQuery(element).attr('id')));
	}
	
	jQuery(element).on('click', function(){
		jQuery('input[name="'+element.name+'"]').each(function(index, element) {	
			if(jQuery(this).is(':checked')){
				show_image(jQuery('.image_'+jQuery(this).attr('id')));
			} else {
				hide_image(jQuery('.image_'+jQuery(this).attr('id')));
			}
		});
	});
	
}

function show_image(toggle_image){	
	jQuery(toggle_image).animate({
		opacity:1
	});
}

function hide_image(toggle_image){	
	jQuery(toggle_image).animate({
		opacity:0.2
	});
}

