/*
	FRONT-END SCRIPT
	notes: loaded everytime shortcode is intercepted (available on website front-end)
*/
document.addEventListener("click", function(){
});

//activate the menu
function hmenu_activate_menu(menu_id, current_url){
	
	hmenu_get_offset();
	
	var add = jQuery(".add_to_cart_button");
	add.bind('click', function(){
		jQuery( document ).on('ajaxSuccess', function() {
			get_product_count();
		});
	});
	
	hmenu_add_files(menu_id);
	get_product_count();
	set_active_state(current_url);
	
}

function set_active_state(current_url){
	
	jQuery('ul.hmenu_navigation_root > li ').each(function(index, element) {		
		var root_item = jQuery(this);		
		jQuery(this).find('a').each(function(index, element) {		  
		   var a_url = jQuery(this).attr('href');		   
		   if(a_url == current_url){
			   jQuery(root_item).addClass('hmenu_active_nav');
		   }		   
		});		
	});
}

function get_product_count(){
	//check the menu status	
	hmenu_get_offset();
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_get_count'
		},
		dataType: "json"
	}).done(function(data){
		hmenu_get_offset();
		if(data.status == 'active'){
			if(data.count == 0){
				jQuery('.hmenu_cart_num_color').hide();
				jQuery('.hmenu_cart_num_color').parents('a').attr('href', data.url);		
			} else {
				jQuery('.hmenu_cart_num_color').html(data.count).css({'display':'table'});
				jQuery('.hmenu_cart_num_color').parents('a').attr('href', data.url);
				hmenu_animate_bubble();
				jQuery( document ).off('ajaxSuccess');
			}
		} else {
			jQuery('.hmenu_product_toggle_display').remove();
			//remove last grp border - this was added to accomodate the chance of the cart icon being active but woocommerce is not active
			//hmenu_enable_remove_borders();
		}
		hmenu_get_offset();
	}).fail(function(){
		 //page error
	});
	
}

//animate bubble
function hmenu_animate_bubble(){
	
	jQuery('.hmenu_cart_num_color').effect( "bounce", "slow" );
	
}


var load_count = 0;
var load_icon_count = 0;

//add css and js files to document
function hmenu_add_files(menu_id){
	
	hmenu_get_offset();
	
	if(load_count <= 0){
		//load google maps API
		var hmenu_js_file = hmenu_url+"_frontend_files/_js_files/hero_script.js";
		
		//load google maps API
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = hmenu_js_file;
		document.body.appendChild(script);
	}
	
	hmenu_get_offset();
	
	load_count++;
	
}

//get center offset
function hmenu_get_offset(){
	jQuery('.hmenu_inner_holder').each(function(index, element) {
		
		//main
		var the_width = jQuery(this).width();
		var the_holder_left = jQuery(this).position();
		
		//left 
		var the_position_left = jQuery(this).children('.hmenu_left').position();
		var the_width_left = jQuery(this).children('.hmenu_left').width();
		
		//center 
		var the_position_center = jQuery(this).children('.hmenu_center').position();
		var the_width_center = jQuery(this).children('.hmenu_center').width();
		
		//right 
		var the_position_right = jQuery(this).children('.hmenu_right').position();
		var the_width_right = jQuery(this).children('.hmenu_right').width();
		
		var left_start = the_width_left;					
		var right_start = the_position_right.left;
		
		var new_center = (right_start - left_start) / 2 + left_start;
		var new_negative_margin = the_width_center / 2;
		
		var new_center_pos = jQuery(this).children('.hmenu_center').position();
		
		//set position of center div
		jQuery(jQuery(this).children('.hmenu_center')).css({
			'left':new_center+'px',
			'margin-left':'-'+new_negative_margin+'px',
			'display':'table'
		});
		
		var mobile_label_width = jQuery(jQuery(this).children('.menu_responsive_label')).width();
		//set position of center div
		jQuery(jQuery(this).children('.menu_responsive_label')).css({			
			'margin-left':'-'+(mobile_label_width/2)+'px'
		});
		
		//set mega size in center div
		jQuery(jQuery(this).children('.hmenu_center').find('.hmenu_mega_sub')).css({
			'width':the_width,
			'left':"0px",
			'margin-left':"-"+(new_center_pos.left - new_negative_margin)+"px"
		});
		
	});					
}

//load post html
function load_posts_html(type, obj, div){
	
	var posts = obj.posts;
	var settings = obj.settings;
	
	var post_html = '';
	
	if(posts.length > 0){
		
		if(settings[0].heading){
			if(settings[0].heading_underline > 0){
				post_html += '<h2 class="hmenu_mega_bottom_border">';
			} else {
				post_html += '<h2>';
			}
				post_html += settings[0].heading;
			post_html += '</h2>';
		}
		
		jQuery(posts).each(function(index, element) {
			
			post_html += '<a href="'+element.url+'" target="'+settings[0].target+'" class="hmenu_post_item">';
				if(element.image && settings[0].image_allow > 0){
					post_html += '<div class="hmenu_post_img" style="background-image:url('+element.image+');"></div>';
				}
				post_html += '<div class="hmenu_post_content">';
				if(settings[0].heading_allow > 0){
					post_html += '<h3>'+element.title+'</h3>';
				}
				if(settings[0].description > 0){
					post_html += '<span>'+element.content+'</span>';
				}
				post_html += '</div>';
			post_html += '</a>'; 
			
		});
		
		jQuery(div).children('div.hmenu_col_load').html(post_html);
		
	}
	
}

//load text/content html
function load_text_html(type, obj, div){
	
	var settings = obj.settings;
	
	var text_html = '';
	
	if(settings.length > 0){
		
		if(settings[0].heading){
			if(settings[0].heading_underline > 0){
				text_html += '<h2 class="hmenu_mega_bottom_border">';
			} else {
				text_html += '<h2>';
			}
				text_html += settings[0].heading;
			text_html += '</h2>';
		}
		
		if(settings[0].content.length > 0 && settings[0].content != ''){
			text_html += '<div class="hmenu_text_item" style="padding-top:'+settings[0].padding_top+'; padding-bottom:'+settings[0].padding_bottom+'">';
				text_html += settings[0].content;
			text_html += '</div>';
		}
		
		jQuery(div).children('div.hmenu_col_load').html(text_html);
		
	}
	
}

//load text/content html
function load_list_html(type, obj, div){
	
	var items = obj.items;
	var settings = obj.settings;
	var list_html = '';
	
	if(items.length > 0){
				
		//HEADING
		if(settings[0].heading){
			if(settings[0].heading_underline > 0){
				list_html += '<h2 class="hmenu_mega_bottom_border">';
			} else {
				list_html += '<h2>';
			}
				list_html += settings[0].heading;
			list_html += '</h2>';
		}
		
		//DESCRIPTION
		if(settings[0].text.length > 0 && settings[0].text != ''){
			list_html += '<div class="hmenu_list_body_text" style="padding-top:'+settings[0].padding_top+'; padding-bottom:'+settings[0].padding_bottom+'" style="text-align:'+settings[0].alignment+'">';
				list_html += settings[0].text;
			list_html += '</div>';
		}
		
		//LIST ITEMS
		jQuery(items).each(function(index, element) {
			
			//ICON
			var icon = '';
			if(element.icon > 0){
				icon = element.icon_content;
			}
			
			list_html += '<a href="'+element.url+'" target="'+element.target+'" id="hmenu_list_item_uni_'+element.id+'" class="hmenu_list_item hmenu_item_'+index+' '+icon+'">';
				list_html += '<div class="hmenu_list_content">';
					list_html += '<h3>'+element.title+'</h3>';
					if(element.desc > 0){
						list_html += '<span>'+element.description+'</span>';
					}
				list_html += '</div>';
			list_html += '</a>';
				
		});
		
		jQuery(div).children('div.hmenu_col_load').html(list_html);
		
		//LIST ITEMS
		jQuery(items).each(function(index, element) {
			//HTML	 
			jQuery('.hmenu_item_'+index).css({
				'color':element.icon_color,
				'font-size':element.icon_size
			});
		});
		
	}
	
}

//load contact html
function load_contact_html(type, obj, div){
	
	var settings = obj.settings;
	
	var contact_html = '';
	
	if(settings.length > 0){
		
		if(settings[0].heading){
			if(settings[0].heading_underline > 0){
				contact_html += '<h2 class="hmenu_mega_bottom_border">';
			} else {
				contact_html += '<h2>';
			}
				contact_html += settings[0].heading;
			contact_html += '</h2>';
		}
		
		if(settings[0].html > 0){
			contact_html += '<div class="hmenu_text_item">';
				contact_html += settings[0].form_html;
			contact_html += '</div>';
		} else if(settings[0].shortcode > 0){
			contact_html += '<div class="hmenu_text_item">';
				contact_html += settings[0].form_shortcode;
			contact_html += '</div>';
		}
		
		jQuery(div).children('div.hmenu_col_load').html(contact_html);
		
		if(settings[0].shortcode > 0){
			var contact_obj_html = jQuery('#hmenu_contact_plugin_'+settings[0].form_id).detach();
			jQuery('.hmenu_contact_plugin_'+settings[0].map_id).children('.hmenu_inner_col').children('.hmenu_text_item').html(contact_obj_html);
		}
		
	}
	
}

//load map html
function load_map_html(type, obj, div){
	
	var settings = obj.settings;
	
	var map_html = '';
	
	if(settings.length > 0){
		
		if(settings[0].heading){
			if(settings[0].heading_underline > 0){
				map_html += '<h2 class="hmenu_mega_bottom_border">';
			} else {
				map_html += '<h2>';
			}
				map_html += settings[0].heading;
			map_html += '</h2>';
		}
		
		if(settings[0].map > 0){
			map_html += '<div class="hmenu_text_item">';
				map_html += settings[0].map_html;
			map_html += '</div>';
		} else if(settings[0].shortcode > 0){
			map_html += '<div class="hmenu_text_item">';			
			map_html += '</div>';
		}
		
		jQuery(div).children('div.hmenu_col_load').html(map_html);
		
		if(settings[0].shortcode > 0){
			var map_obj_html = jQuery('#hmenu_map_plugin_'+settings[0].map_id).detach();
			jQuery('.hmenu_map_plugin_'+settings[0].map_id).children('.hmenu_inner_col').children('.hmenu_text_item').html(map_obj_html);
		}
		
	}
	
}

//load image html
function load_image_html(type, obj, div){
	
	var settings = obj.settings;
	
	var image_html = '';
	
	if(settings.length > 0){
		
		if(settings[0].heading){
			if(settings[0].heading_underline > 0){
				image_html += '<h2 class="hmenu_mega_bottom_border">';
			} else {
				image_html += '<h2>';
			}
				image_html += settings[0].heading;
			image_html += '</h2>';
		}
		//<!--<div class="hmenu_image_heading">Latest Model</div>-->
		image_html += '<a href="'+settings[0].url+'" class="hmenu_image_holder hmenu_layout_'+settings[0].layout+'">';
			image_html += '<div class="hmenu_image_inner">';
				if(settings[0].layout != 'three' && settings[0].image_heading != ''){
					image_html += '<div class="hmenu_image_heading">'+settings[0].image_heading+'</div>';
				}
				if(settings[0].layout == 'three'){
					image_html += '<div class="hmenu_image" style="background-image:url('+settings[0].image+'); height:280px;"></div>';
				} else {
					image_html += '<div class="hmenu_image" style="background-image:url('+settings[0].image+')"></div>';
				}
				image_html += '<div class="hmenu_image_desc_wrap">';
					image_html += '<div class="hmenu_image_desc">';
						if(settings[0].layout == 'three' && settings[0].image_heading != ''){
							image_html += '<div class="hmenu_image_heading">'+settings[0].image_heading+'</div>';
						}
						image_html += settings[0].text;
					image_html += '</div>';
				image_html += '</div>';
			image_html += '</div>';
		image_html += '</a>';
		
		jQuery(div).children('div.hmenu_col_load').html(image_html);
		
	}
	
}

//get screen width
function getWidth(){
	var windowWidth = 0;
	if(typeof(window.innerWidth) == 'number'){
		windowWidth = window.innerWidth;
	}else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)){
		windowWidth = document.documentElement.clientWidth;
	}else if(document.body && (document.body.clientWidth || document.body.clientHeight)){
		windowWidth = document.body.clientWidth;
	}
	return windowWidth;
}

//get screen height
function getHeight(){
	windowHeight = 0;
	if(typeof(window.innerWidth) == 'number'){
		windowHeight = window.innerHeight;
	}else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)){
		windowHeight = document.documentElement.clientHeight;
	}else if(document.body && (document.body.clientWidth || document.body.clientHeight)){
		windowHeight = document.body.clientHeight;
	}
	return windowHeight;
}

