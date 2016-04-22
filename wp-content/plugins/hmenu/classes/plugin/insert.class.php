<?php

	#INSERT CLASS
	class hmenu_class_insert extends hmenu_backend{
		
		#INSERT MENU
		public function insert_menu(){
			
			#GLOBALS
			global $hmenu_helper, $wpdb;
			
			#GET POST DATA
			$post_data = $_POST['form_data'];
		   	$form_data = array();
		   	parse_str($post_data, $form_data);			
			
			#VALIDATE INSERT DATA
			$status = true;
			if($hmenu_helper->checkString($form_data['add_new_menu'])){
				$menu_name = true;
			}else{
				$menu_name = false;
				$status = false;
			}
			
			#CHECK STATUS
			if($status){		
				#INSERT MENU
				$wpdb->query(
					"
						INSERT INTO `". $wpdb->base_prefix ."hmenu_menu` 
						( 
							`name`,							
							`autoLink`,
							`leftItems`,
							`centerItems`,
							`rightItems`,
							`customLink`,
							`overwrite` 
						) VALUES (
							'".$form_data['add_new_menu']."',							
							'auto',
							'logo',
							'',
							'main,search,social,product',
							'custom',
							''
						)
					"
				);		
				$result = $wpdb->get_results("SELECT * FROM ". $wpdb->base_prefix ."hmenu_menu WHERE deleted = '0' ORDER BY created DESC LIMIT 1");				
				echo json_encode(array('status' => true, 'object' => $wpdb->insert_id));		
				#RUN SQL TO INSERT ALL DEFAILT DATA
				$this->menu_sql_inject($wpdb->insert_id);
				exit;				
			}
			
			#ERROR
			echo json_encode(array('status' => false, 'object' => array(			
				'add_new_menu' => $menu_name				
			)));			
			exit;
			
		}
		
		#SQL INJECT FOR MENU DEFAULTS
		public function menu_sql_inject($menu_id){
			
			#GLOBALS
			global $hmenu_helper, $wpdb;
			
			#DATA: MAIN STYLES
			$wpdb->query(
				"
					INSERT INTO `". $wpdb->base_prefix ."hmenu_main_styles` 
					(
						`menuId`,
						`logo`,
						`logoUrl`,
						`logoLink`,
						`logoAlt`,
						`logoLinkTarget`,
						`logoHeight`,
						`mobileLogo`,
						`mobileLogoUrl`,
						`mobileLogoHeight`,
						`search`,
						`menu`,
						`social`,
						`cart`,
						`menuBarDimentions`,
						`menuBarWidth`,
						`menuBarHeight`,
						`navBarDimentions`,
						`navBarWidth`,
						`border`,
						`borderColor`,
						`borderTransparency`,
						`borderType`,
						`borderRadius`,
						`shadow`,
						`shadowRadius`,
						`shadowColor`,
						`shadowTransparency`,
						`bgMenuStartColor`,
						`bgMenuGradient`,
						`bgMenuEndColor`,
						`bgMenuGradientPath`,
						`bgMenuTransparency`,
						`bgHoverStartColor`,
						`bgHoverType`,
						`bgHoverGradient`,
						`bgHoverEndColor`,
						`bgHoverGradientPath`,
						`bgHoverTransparency`,
						`paddingLeft`,
						`paddingRight`,
						`orientation`,
						`verticalWidth`,
						`animation`,
						`animationDuration`,
						`animationTrigger`,
						`animationTimeout`,
						`sticky`,
						`stickyLogoActive`,
						`stickyUrl`,
						`stickyActivate`,
						`stickyHeight`,						
						`stickyFontColor`,
						`stickyFontHoverColor`,
						`stickyFontSize`,
						`stickyFontSizing`,
						`stickyFontWeight`,
						`stickyFontHoverDecoration`,						
						`bgStickyStart`,
						`bgStickyEnd`,
						`stickyTransparency`,
						`devider`,
						`deviderTransparency`,
						`deviderColor`,
						`deviderSizing`,
						`groupDevider`,
						`groupTransparency`,
						`groupColor`,
						`groupSizing`,	
						`responsiveLabel`,
						`icons`,
						`iconsColor`,
						`arrows`,
						`arrowTransparency`,
						`arrowColor`,
						`fontFamily`,
						`fontColor`,
						`fontHoverColor`,
						`fontSize`,
						`fontSizing`,
						`fontWeight`,
						`fontDecoration`,
						`fontHoverDecoration`,
						`zindex`,
						`preset`,
						`presetSlug`,
						`iconProductSize`,
						`iconProductColor`,
						`iconProductHoverColor`,
						`siteResponsive`,
						`siteResponsiveOne`,
						`siteResponsiveTwo`,
						`siteResponsiveThree`,
						`logoPaddingLeft`,
						`mobileLogoPaddingLeft`,
						`stickyLogoPaddingLeft`,
						`bgMainImage`,
						`bgMainImageUrl`,
						`bgMainImagePosition`,
						`bgMainImageRepeat`,
						`customCss`
					) VALUES (
						'".$menu_id."',
						'1',
						'',
						'',
						'',
						'_self',
						'80',
						'0',
						'',
						'80',
						'0',
						'1',
						'1',
						'0',
						'full',
						'1200',
						'80',
						'fixed',
						'1100',
						'0',
						'#FFF',
						'0.5',
						'border-bottom',
						'5,5,5,5',
						'0',
						'10,40',
						'#000000',						
						'0.5',
						'#FFFFFF',
						'0',
						'#F0F0F0',
						'vertical',
						'1',
						'#F0F0F0',
						'background',
						'0',
						'#F0F0F0',
						'vertical',
						'1',
						'10',
						'10',
						'horizontal',
						'200',
						'fade',
						'1000',
						'click',
						'300',
						'0',
						'0',
						'',
						'80',
						'60',						
						'#888888',
						'#888888',
						'12',
						'px',
						'normal',
						'underline',						
						'#888888',
						'#888888',
						'1',						
						'0',
						'1',
						'#F0F0F0',
						'small',						
						'0',
						'1',
						'#F0F0F0',
						'small',
						'Menu',
						'1',
						'#000',
						'0',
						'0.5',
						'#888888',
						'Open Sans',
						'#888888',
						'#888888',
						'12',
						'px',
						'normal',
						'none',
						'underline',
						'9999',
						'0',
						'hero_red',
						'medium',
						'#888888',
						'#DDDDDD',
						'1',
						'768',
						'992',
						'1200',
						'0',
						'10',
						'10',
						'0',
						'',
						'left',
						'repeat',
						''
					)
				"
			);				
			
			#DATA: DROPDOWN STYLES
			$wpdb->query(
				"
					INSERT INTO `". $wpdb->base_prefix ."hmenu_dropdown_styles` 
					(
						`menuId`,
						`widthType`,
						`width`,
						`padding`,
						`border`,
						`borderColor` ,
						`borderTransparency`, 
						`borderType`, 
						`borderRadius`, 
						`shadow`,
						`shadowRadius`,
						`shadowColor`,
						`shadowTransparency`,
						`bgDropStartColor`,
						`bgDropGradient` ,
						`bgDropEndColor`,
						`bgDropGradientPath`,
						`bgDropTransparency`,
						`bgHoverStartColor`,
						`bgHoverGradient`,
						`bgHoverEndColor`,
						`bgHoverGradientPath`,
						`bgHoverTransparency`,	  
						`arrows`,
						`arrowTransparency`,
						`arrowColor`,		  
						`devider`,
						`deviderTransparency`,
						`deviderColor`,		  
						`fontFamily`,
						`fontColor`,
						`fontHoverColor`,
						`fontSize`,
						`fontSizing`,
						`fontWeight`,
						`fontDecoration`,
						`fontHoverDecoration`	
					) VALUES (
						'".$menu_id."', 
						'custom', 
						'200', 
						'10,5,10,5', 
						'0', 
						'#FFF', 
						'1',
						'border-bottom', 
						'5,5,5,5', 
						'0', 
						'10,40', 
						'#000000',
						'0.5',
						'#F0F0F0',  
						'0',  
						'#F0F0F0', 
						'vertical', 
						'1',  
						'#888888',  
						'0',  
						'#888888', 
						'vertical', 
						'1', 
						'0',
						'0.5',
						'#888888',
						'0',
						'0.5',
						'#FFF',
						'Open Sans',
						'#888888',
						'#FFFFFF',
						'12',
						'px',
						'normal',
						'underline',
						'none'
					)
				"
			);
			
			#DATA: MEGA STYLES
			$wpdb->query(
				"
					INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_styles` 
					(
						`menuId`,
						`widthType`,
						`width`,
						`padding`,
						`border`,
						`borderColor` ,
						`borderTransparency`,
						`borderType`, 
						`borderRadius`, 
						`shadow`,
						`shadowRadius`,
						`shadowColor`,
						`shadowTransparency`,
						`bgDropStartColor`,
						`bgDropGradient` ,
						`bgDropEndColor`,
						`bgDropGradientPath`,
						`bgDropTransparency`,
						`bgHoverStartColor`,
						`bgHoverGradient`,
						`bgHoverEndColor`,
						`bgHoverGradientPath`,
						`bgHoverTransparency`,	  
						`arrows`,
						`arrowTransparency`,
						`arrowColor`,		  
						`devider`,
						`deviderTransparency`,
						`deviderColor`,	
						`fontHoverColor`,
						`fontHoverDecoration`	
					) VALUES (
						'".$menu_id."', 
						'custom', 
						'1100', 
						'15,5,15,5', 
						'0', 
						'#FFFFFF', 
						'1', 
						'border-bottom',
						'5,5,5,5', 
						'0', 
						'10,40', 
						'#000000',
						'0.5', 
						'#F0F0F0',  
						'0',  
						'#F0F0F0', 
						'vertical', 
						'1',  
						'#888888',  
						'0',  
						'#888888', 
						'vertical', 
						'1', 
						'0',
						'0.5',
						'#FFFFFF',
						'0',
						'0.5',
						'#888888',						
						'#FFFFFF',						
						'underline'
					)
				"
			);	
			
			#DATA: MOBILE STYLES
			$wpdb->query(
				"
					INSERT INTO `". $wpdb->base_prefix ."hmenu_mobile_styles` 
					(
						`menuId`,
						`bgBarStartColor`,
						`bgBarGradient`,
						`bgBarEndColor`,
						`bgBarGradientPath`,
						`bgBarTransparency`,
						`fontBarFamily`,
						`fontBarColor`,
						`fontBarHoverColor`,
						`fontBarSize`,
						`fontBarSizing`,
						`fontBarWeight`,
						`bgMenuStartColor`,
						`bgMenuGradient`,
						`bgMenuEndColor`,
						`bgMenuGradientPath`,
						`bgMenuTransparency`,
						`bgHoverStartColor`,
						`bgHoverGradient`,
						`bgHoverEndColor`,
						`bgHoverGradientPath`,
						`bgHoverTransparency`,
						`fontMobileFamily`,
						`fontMobileColor`,
						`fontMobileHoverColor`,
						`fontMobileSize`,
						`fontMobileSizing`,
						`fontMobileWeight`,
						`fontTabletFamily`,
						`fontTabletColor`,
						`fontTabletHoverColor`,
						`fontTabletSize`,
						`fontTabletSizing`,
						`fontTabletWeight`,
						`paddingLeft`,
						`paddingRight`
					) VALUES (
						'".$menu_id."', 						
						'#FFFFFF',
						'0',
						'#F0F0F0',
						'vertical',
						'1',												
						'Open Sans',
						'#888888',
						'#FFFFFF',
						'12',
						'px',
						'normal',						
						'#FFFFFF',
						'0',
						'#F0F0F0',
						'vertical',
						'1',						
						'#FFFFFF',
						'0',
						'#F0F0F0',
						'vertical',
						'1',						
						'Open Sans',
						'#888888',
						'#FFFFFF',
						'12',
						'px',
						'normal',						
						'Open Sans',
						'#888888',
						'#FFFFFF',
						'12',
						'px',
						'normal',						
						'10',
						'10'						
					)
				"
			);
			
			#DATA: SEARCH STYLES
			$wpdb->query(
				"
					INSERT INTO `". $wpdb->base_prefix ."hmenu_search` 
					(
						`menuId`,
						`type`,
						`icon`,
						`label`,
						`iconColor`,
						`iconHoverColor`,
						`iconSize`,
						`animation`, 
						`placement`, 
						`padding`,
						`width`,
						`height`,
						`fontFamily`,
						`fontColor` ,
						`fontSize`,
						`fontSizing`,
						`fontWeight`,
						`border`,
						`borderColor`,
						`borderTransparency`,
						`borderRadius`,
						`backgroundColor`,
						`placeholder`
					) VALUES (
						'".$menu_id."', 
						'slide', 
						'none', 
						'search', 
						'#CCCCCC', 
						'#333333',
						'medium',
						'slide', 
						'place', 
						'5,5,5,5', 
						'120', 
						'40', 
						'Open Sans',  
						'#888888',  
						'30', 
						'px', 
						'normal',						
						'0',
						'#FFFFFF',
						'0.5', 
						'2,2,2,2',
						'#FFFFFF',
						'Search'
					)
				"
			);	
			
			#ID OF LASTEST MEGA STYLE ENTRY
			$mega_style_id = $wpdb->insert_id;
			
			#DATA: MEGA FONT STYLES
			$font_styling_mega = array( 
				"heading" => array(
					"type" => "heading", "font_family" => "Open Sans", "font_color" => "#222222", "font_size" => "20", "font_sizing" => "px", "font_weight" => "normal" 
				),
				"body" => array(
					"type" => "body", "font_family" => "Open Sans", "font_color" => "#888888", "font_size" => "12", "font_sizing" => "px", "font_weight" => "normal" 
				),
				"list" => array(
					"type" => "list", "font_family" => "Open Sans", "font_color" => "#222222", "font_size" => "14", "font_sizing" => "px", "font_weight" => "normal" 
				),
				"description" => array(
					"type" => "description", "font_family" => "Open Sans", "font_color" => "#888888", "font_size" => "12", "font_sizing" => "px", "font_weight" => "normal" 
				)
			);
			
			foreach($font_styling_mega as $font){	
				//preload data						
				$wpdb->query(
					"
						INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_font_styles` 
						(
							`megaStyleId`,
							`type`,
							`fontFamily`,
							`fontColor`,
							`fontSize`,
							`fontSizing`,
							`fontWeight`
						) VALUES (
							'".$mega_style_id."', 
							'".$font['type']."',
							'".$font['font_family']."',
							'".$font['font_color']."',
							'".$font['font_size']."',
							'".$font['font_sizing']."', 
							'".$font['font_weight']."'
						)
					"
				);	
			}
		}	
	}