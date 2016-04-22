
				var slide_toggle = true;
				//script
				jQuery(function(){	
					//remove borders
					hmenu_enable_remove_borders();
					//bind search animation
					hmenu_bind_search();
					//enable dropdown script
					if(getWidth() > 767){
						//enable main menu switch	
						hmenu_enable_dropdown_animation('hover');
					} else { 
						//enable mobile switch	
						hmenu_enable_dropdown_animation('click');
					}
					//scroll
					hmenu_bind_scroll_listener();
					//resize
					hmenu_bind_resize();
				});
				
				/* window resize */
				var resize_time_var;
				var check_width = jQuery(window).width(), check_height = jQuery(window).height();
				if(jQuery(window).width() != check_width && jQuery(window).height() != check_height){
					jQuery(window).on('resize', function(){
						//enable dropdown script
						if(getWidth() > 768){
							//enable main menu switch	
							hmenu_enable_dropdown_animation('hover');
						} else { 
							//enable mobile switch	
							hmenu_enable_dropdown_animation('click');
						}
						//resize lightbox holder
						hmenu_resize();
						hmenu_get_offset();
						clearTimeout(resize_time_var);
						resize_time_var = setTimeout(function(){
							hmenu_get_offset();
						},500);
					});
				};
				
				//remove border
				function hmenu_enable_remove_borders(){
					
					//check the list items and remove first or last occurance of borders	
					jQuery('.hmenu_sub ul').each(function(index, element) {
						jQuery(this).children('li').last().addClass('hmenu_no_bottom_border');	
					});
					
					//nav item last border removed
					jQuery('.hmenu_navigation_holder > ul').each(function(index, element) {
						jQuery(this).children('li').last().children('.hmenu_item_devider').css({
							opacity:0
						});	
					});
					
					//section deviders
					jQuery('.hmenu_inner_holder > div').each(function(index, element) {
						jQuery(this).children('.hmenu_grp_devider').last().remove();	
					});
					
				}
								
				//bind search animations
				function hmenu_bind_search(){
					
					jQuery('.hmenu_trigger_search').off().on('click', function(){
						jQuery(this).parent('form').children('.hmenu_search_submit').trigger('click');
					});
					
					hmenu_bind_search_animation();
					
				}
				
				function hmenu_resize(){
					//lightbox
					jQuery('.hmenu_search_lightbox_input').css({
						height:jQuery(window).height()+'px'
					});
				}
				
				//search animation
				function hmenu_bind_search_animation(){
					
					hmenu_resize();
					
					jQuery('.hmenu_search_slide .hmenu_trigger_lightbox').off().on('click', function(){
						
						var the_link = jQuery(this).attr('data-link');
						var the_id = jQuery(this).attr('data-id');
						
						//set css
						jQuery('#'+the_link).css({
							display:'table'
						});	
						jQuery('#'+the_link).animate({
							opacity: 1
						}, 500, function(){
							jQuery('.hmenu_search_'+the_id).focus();
							//close
							jQuery('#'+the_link+' .hmenu_search_lightbox_close').off().on('click', function(){
								jQuery('#'+the_link).animate({
									opacity: 0
								}, 500, function(){
									jQuery('#'+the_link).css({
										display:'none'
									});	
								});
							});
						});					
						
					});
					
					//slide full
					jQuery('.hmenu_search_full .hmenu_trigger_full').off().on('click', function(){
						
						var the_link = jQuery(this).attr('data-link');
						var the_height = jQuery(this).attr('data-height');
						var the_id = jQuery(this).attr('data-id');
						var this_element = jQuery(this);
						
						if(!jQuery(this_element).attr('data-search-toggle') || jQuery(this_element).attr('data-search-toggle') == 'close'){	
							jQuery(this_element).attr('data-search-toggle', 'open');			
							//open	
							jQuery('#'+the_link).stop().animate({
								opacity: 1,
								height: the_height+'px'
							}, 200);			
						} 
						
						jQuery('.hmenu_search_'+the_id).focus();
						
						jQuery('.hmenu_search_'+the_id).focusout(function() {
							jQuery(this_element).attr('data-search-toggle', 'close');
							//close
							jQuery('#'+the_link).stop().animate({
								opacity: 0,
								height: 0
							}, 200);														
						})
						
					});
					
				}
				
				//dropdown animation
				function hmenu_enable_dropdown_animation(hmenu_event){
					
					if(hmenu_event == 'hover'){	
						//reset
						jQuery('.hmenu_submenu').css({
							'opacity': 0,
							'visibility': 'hidden',
							'height': 'auto'
						});
						jQuery('.hmenu_navigation_holder ul').each(function(index, element) {        
							
							jQuery(this).children('li').each(function(index, element) {            
								
								jQuery(this).off().on(
									{
										mouseenter: function(){
											
											if(jQuery(this).find('> .hmenu_submenu').length > 0){
												var sub_menu = jQuery(this).find('> .hmenu_submenu');
												//animate menu
												jQuery(this).addClass('hmenu_main_active');
												jQuery(sub_menu).css({ 
													'display': 'table-cell',
													'visibility':'visible'
												});
												
							jQuery(sub_menu).stop().animate({
								opacity: 1
							}, 1000);
						
											};
											if(jQuery(sub_menu).hasClass('hmenu_mega_sub')){
												var the_height = jQuery(sub_menu).height();
												var the_pad_top = jQuery(sub_menu).children('.hmenu_mega_inner').css('padding-top');
													var replace_top = the_pad_top.replace('px', '');
												var the_pad_bot = jQuery(sub_menu).children('.hmenu_mega_inner').css('padding-bottom');
													var replace_bot = the_pad_bot.replace('px', '');
												var final_height = the_height - (parseInt(replace_top)+parseInt(replace_bot));
												jQuery(sub_menu).children('.hmenu_mega_inner').children('div').last().children('.hmenu_col_devider').hide();
												jQuery(sub_menu).children('.hmenu_mega_inner').children('div').each(function(index, element) {
													jQuery(this).children('.hmenu_col_devider').css({
														'height':final_height+'px'
													});
												});
											}
										},
										mouseleave: function(){
											if(jQuery(this).find('> .hmenu_submenu').length > 0){
												var sub_menu = jQuery(this).find('> .hmenu_submenu');
												//animate menu
												jQuery(this).removeClass('hmenu_main_active');
												jQuery(sub_menu).stop().animate({
													opacity: 0
												}, 100, function(){
													jQuery(this).css({
														'visibility': 'hidden'
													});
												});
											};
										}
									}
								);	
								
							});		
						});	
					} else if(hmenu_event == 'click') {
						
						//reset
						jQuery('.hmenu_submenu').css({
							'opacity': 0,
							'display': 'block',
							'visibility': 'visible',
							'height': 0
						});
						
						jQuery('.hmenu_navigation_holder ul').each(function(index, element) {     
							jQuery(this).children('li').each(function(index, element) {  
								jQuery(this).off();
							});
						});
						
						var the_ul_height = jQuery('.hmenu_navigation_holder').children('ul').height();
						
						jQuery('.hmenu_navigation_holder').each(function(){
							
							var the_parent = jQuery(this).parents('.hmenu_inner_holder');
							
							jQuery(the_parent).children('.hmenu_right').children('.hmenu_toggle_holder').off().on('click', function(){		
							
								if(!jQuery(this).attr('data-toggle') || jQuery(this).attr('data-toggle') == 'close'){	
									jQuery(this).attr('data-toggle', 'open');			
									//open	
									jQuery(the_parent).children('div').children('.hmenu_navigation_holder').hide().slideDown( 'slow', function() {
										
									});					
								} else if(jQuery(this).attr('data-toggle') == 'open'){
									jQuery(this).attr('data-toggle', 'close');
									//close
									jQuery(the_parent).children('div').children('.hmenu_navigation_holder').css({ 'display':'block'});
									jQuery(the_parent).children('div').children('.hmenu_navigation_holder').slideUp( 'slow', function() {
										jQuery(this).css({ 'display':'none'});
									});					
								}
								
							});
							
						});
						
						var item_height = jQuery('.hmenu_navigation_holder > ul > li').first().height();
						
						jQuery('.hmenu_mobile_menu_toggle').remove();
						
						//add toggle div to menu
						jQuery('.icon_hero_default_thin_e600').each(function(index, element) {
							jQuery(this).parent('a').parent('li').append('<div class="hmenu_mobile_menu_toggle" data-toggle="close"></div>');
						});
						jQuery('.icon_hero_default_thin_e602').each(function(index, element) {
							jQuery(this).parent('a').parent('li').append('<div class="hmenu_mobile_menu_toggle" data-toggle="close"></div>');
						});
						
						if(jQuery('.hmenu_mobile_menu_toggle').length > 0){
							jQuery('.hmenu_mobile_menu_toggle').off().on('click', function(event){
								
								var current_toggle = jQuery(this);
								
								if(jQuery(this).parent('li').parent('ul').hasClass('hmenu_full_hover') && jQuery(this).attr('data-toggle') != 'open'){
									//close any open menu items
									jQuery('.hmenu_navigation_holder ul > li').each(function(index, element) {
									   if(jQuery(this).children('.hmenu_mobile_menu_toggle').attr('data-toggle') == 'open'){
											jQuery(this).children('.hmenu_mobile_menu_toggle').attr('data-toggle', 'close');
											//close
											jQuery(this).children('.hmenu_mobile_menu_toggle').prev().css({ 'display':'block'});				
											jQuery(this).children('.hmenu_mobile_menu_toggle').prev().animate({
												opacity: 0,
												height: 0
											}, 200);
										}	
									});	
								}
								
								if(!jQuery(this).attr('data-toggle') || jQuery(this).attr('data-toggle') == 'close'){
										
									jQuery(this).attr('data-toggle', 'open');			
									
									//open	
									if(jQuery(this).prev().hasClass('hmenu_mega_sub')){
										var the_height = jQuery(this).prev().children('.hmenu_mega_inner').height();
									} else {
										var the_height = jQuery(this).prev().children('ul').height();
									}
									
									jQuery(this).prev().animate({
										opacity: 1,
										height: the_height
									}, 200, function(){
										jQuery(this).css({ 'display':'table', 'height':'auto'});
									});	
											
								} else if(jQuery(this).attr('data-toggle') == 'open'){
									
									jQuery(this).attr('data-toggle', 'close');
									
									//close
									jQuery(this).prev().css({ 'display':'block'});
									
									jQuery(this).prev().animate({
										opacity: 0,
										height: 0
									}, 200);	
												
								}
								
							});
							
						}	
											
					}
					
				}
				
				//bind home scroll listener
				function hmenu_bind_resize(){
					var mobile_res = 768;
					var current_width = jQuery( window ).width();
					jQuery( window ).resize(function() {
						current_width = jQuery( window ).width();
						if(current_width < mobile_res){
							hmenu_remove_class('remove');
						} else {
							hmenu_remove_class('reset');
							hmenu_bind_scroll_listener();
						}
					});
					if(current_width < mobile_res){
						hmenu_remove_class('remove');
					} else {
						hmenu_remove_class('reset');
					}
				}
				
				//bind remove and add classes
				function hmenu_remove_class(todo){
					if(todo == 'remove'){
						jQuery('.hmenu_submenu').find('.icon_hero_default_thin_e602').addClass('icon_hero_default_thin_e600').removeClass('icon_hero_default_thin_e602');
					} else{
						jQuery('.hmenu_submenu').find('.icon_hero_default_thin_e600').addClass('icon_hero_default_thin_e602').removeClass('icon_hero_default_thin_e600');
					}					
				}
				
				//bind home scroll listener
				function hmenu_bind_scroll_listener(){
						
					//variables
					var sticky_menu = jQuery('.hmenu_load_menu').find('[data-sticky="yes"]');						
					var sticky_height = parseInt(sticky_menu.attr('data-height'));						
					var sticky_activate = parseInt(sticky_menu.attr('data-activate'));						
					var body_top = jQuery(document).scrollTop();						
					var menu_id = jQuery(sticky_menu).parent('.hmenu_load_menu').attr('data-menu-id');
					
					//show menu
					jQuery('.hmenu_load_menu').removeAttr('style');	
					
					//check current state
					if(body_top >= sticky_activate){
						hmenu_bind_sticky(sticky_menu, sticky_height, sticky_activate, body_top, menu_id);
					} else {
						hmenu_bind_sticky(sticky_menu, sticky_height, sticky_activate, body_top, menu_id);
					}
					
					//scroll trigger			
					jQuery(window).scroll(function(){
						body_top = jQuery(document).scrollTop();		
						hmenu_bind_sticky(sticky_menu, sticky_height, sticky_activate, body_top, menu_id);
						//hmenu_get_offset();
					});
						
				}
				
				//bind sticky
				function hmenu_bind_sticky(sticky_menu, sticky_height, sticky_activate, body_top, menu_id){
					
					//get window width
					var window_width = jQuery(window).width();
					
					if(window_width > 768){
						//activate switch
						if(body_top >= sticky_activate){
							
										
							//add class
							jQuery(sticky_menu).parent('.hmenu_load_menu').addClass('hmenu_is_sticky ' + 'hmenu_sticky_' + menu_id);
							if(slide_toggle){
								jQuery(sticky_menu).parent('.hmenu_load_menu').css({
									'position': 'fixed',
									'top':'-'+sticky_height+'px'
								});
								jQuery(sticky_menu).parent('.hmenu_load_menu').animate({
									'top':'0px'
								}, 200);
								slide_toggle = false;
							}
						} else {
							slide_toggle = true;	
				
							//remove class
							jQuery(sticky_menu).parent('.hmenu_load_menu').removeClass('hmenu_is_sticky ' + 'hmenu_sticky_' + menu_id);	
							jQuery(sticky_menu).parent('.hmenu_load_menu').removeAttr('style');							
						}
					}					
				}
				
			