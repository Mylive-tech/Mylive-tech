<?php

	#UPDATE CLASS
	class hmenu_class_update extends hmenu_backend{
		
		#CLASS VARS
		private $plugin_dir;
		private $frontend_directory = "/_frontend_files/";
		private $menu_id;
		
		#CONSTRUCT
		public function __construct($plugin_dir){
			$this->plugin_dir = $plugin_dir;
		}
		
		#DELETE MENU
		public function delete_menu(){
			
			#GLOBALS
			global $hmenu_helper, $wpdb;
			
			#POST VARIABLES
			$this->menu_id = $_POST['id'];
			
			#UPDATE MENU OBJECT
			$wpdb->query("
				UPDATE
					`". $wpdb->base_prefix ."hmenu_menu`
				SET
					`deleted` = 1
				WHERE
					`menuId` = ". $this->menu_id .";
			");
			
			#MENU HTML DIRECTORY
			$menu_directory = '_menu_' . $this->menu_id . '/';
			
			#REMOVE DELETED DIRECTORY
			if (is_dir($this->plugin_dir . $this->frontend_directory . $menu_directory)) {				
				$this->clean_up($this->plugin_dir . $this->frontend_directory . $menu_directory);								
			}
			
			#SUCCESS			
			echo json_encode(array('status' => true, 'menu_id' => $this->menu_id));	
			exit;
			
		}		
		
		#REMOVE DIRECTORY AND CONTENTS
		private function clean_up($dir){
			$it = new RecursiveDirectoryIterator($dir);
			$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			foreach($files as $file){
				if($file->getFilename() === '.' || $file->getFilename() === '..'){
					continue;
				}
				if($file->isDir()){
					rmdir($file->getRealPath());
				}else{
					unlink($file->getRealPath());
				}
			}
			rmdir($dir);
		}
		
		#INSERT MENU
		public function update_object(){
			
			#GLOBALS
			global $hmenu_helper, $wpdb;
			
			#GET POST DATA
			$save_type = $_POST['save'];
			$json_object = $_POST['obj'];
			
			#SET POST DATA
			$menu = $json_object['menu'];
			
			if($save_type != 'navigation_structure'){
				
				$main_styles = $json_object['main_styles'][0];	
				$dropdown_styles = $json_object['dropdown_styles'][0];
				$mega_styles = $json_object['mega_styles'][0];
				$mobile_styles = $json_object['mobile_styles'][0];
				$search_styles = $json_object['search_styles'][0];
				$mega_font_styles = $json_object['mega_font_styles'];
				$all_menus = isset($json_object['all_menus']) ? $json_object['all_menus'] : array();			
				$social_items = isset($json_object['social_items']) ? $json_object['social_items'] : array();
				
			} else {
				
				$nav_items_temp = isset($json_object['nav_items']) ? $json_object['nav_items'] : array();
				
				#RESET ARRAY
				$nav_items = array();
				if(isset($json_object['nav_items'])){
					foreach($nav_items_temp as $key => $item){
						$nav_items[(int) $item['order']] = $item;
					}
					ksort($nav_items);
				}
			
			}
			
			
				
			#UPDATE MENU OBJECT		
			if($menu['status'] == 1){
				
				if($save_type != 'navigation_structure'){
					
				////////////////////////////////////////////////////////////////////////////////////////////////
				/////////////// SAVE DEFAULT
				////////////////////////////////////////////////////////////////////////////////////////////////
					
					#UPDATE EACH MENU WITH THE CORRECT LOCATION
					if(isset($all_menus)){
						foreach($all_menus as $menu_item){
							#UPDATE MENU OBJECT
							if($menu['menuId'] != $menu_item['menuId']){
								$wpdb->query("
									UPDATE
										`". $wpdb->base_prefix ."hmenu_menu`
									SET
										`overwrite` = '". $menu_item['overwrite'] ."'
									WHERE
										`menuId` = ". $menu_item['menuId'] .";
								");
							}
						}
					}
					
					#UPDATE MENU OBJECT
					$wpdb->query("
						UPDATE
							`". $wpdb->base_prefix ."hmenu_menu`
						SET
							`name` = '".$menu['name']."',
							`autoLink` = ".$menu['autoLink'].",
							`leftItems` = '".$menu['leftItems']."',
							`centerItems` = '".$menu['centerItems']."',
							`rightItems` = '".$menu['rightItems']."',
							`customLink` = '".$menu['customLink']."',
							`overwrite` = '".$menu['overwrite']."'
						WHERE
							`menuId` = ". $menu['menuId'] .";
					");
					#UPDATE MAIN STYLES /// '".$main_styles['name']."',
					$wpdb->query("
						UPDATE
							`". $wpdb->base_prefix ."hmenu_main_styles`
						SET
							`logo` = ".$main_styles['logo'].",
							`logoUrl` = '".$main_styles['logoUrl']."',
							`logoLink` = '".$main_styles['logoLink']."',
							`logoAlt` = '".$main_styles['logoAlt']."',
							`logoLinkTarget` = '".$main_styles['logoLinkTarget']."',
							`logoHeight` = '".$main_styles['logoHeight']."',
							`mobileLogo` = ".$main_styles['mobileLogo'].",
							`mobileLogoUrl` = '".$main_styles['mobileLogoUrl']."',
							`mobileLogoHeight` = '".$main_styles['mobileLogoHeight']."',
							`search` = ".$main_styles['search'].",
							`menu` = ".$main_styles['menu'].",
							`social` = ".$main_styles['social'].",
							`cart` = ".$main_styles['cart'].",
							`menuBarDimentions` = '".$main_styles['menuBarDimentions']."',
							`menuBarWidth` = '".$main_styles['menuBarWidth']."',
							`menuBarHeight` = '".$main_styles['menuBarHeight']."',
							`navBarDimentions` = '".$main_styles['navBarDimentions']."',
							`navBarWidth` = '".$main_styles['navBarWidth']."',
							`border` = '".$main_styles['border']."',
							`borderColor` = '".$main_styles['borderColor']."',
							`borderTransparency` = '".$main_styles['borderTransparency']."',
							`borderType` = '".$main_styles['borderType']."',
							`borderRadius` = '".$main_styles['borderRadius']."',
							`shadow` = '".$main_styles['shadow']."',
							`shadowRadius` = '".$main_styles['shadowRadius']."',
							`shadowColor` = '".$main_styles['shadowColor']."',
							`shadowTransparency` = '".$main_styles['shadowTransparency']."',
							`bgMenuStartColor` = '".$main_styles['bgMenuStartColor']."',
							`bgMenuGradient` = '".$main_styles['bgMenuGradient']."',
							`bgMenuEndColor` = '".$main_styles['bgMenuEndColor']."',
							`bgMenuGradientPath` = '".$main_styles['bgMenuGradientPath']."',
							`bgMenuTransparency` = '".$main_styles['bgMenuTransparency']."',
							`bgHoverStartColor` = '".$main_styles['bgHoverStartColor']."',
							`bgHoverType` = '".$main_styles['bgHoverType']."',
							`bgHoverGradient` = '".$main_styles['bgHoverGradient']."',
							`bgHoverEndColor` = '".$main_styles['bgHoverEndColor']."',
							`bgHoverGradientPath` = '".$main_styles['bgHoverGradientPath']."',
							`bgHoverTransparency` = '".$main_styles['bgHoverTransparency']."',
							`paddingLeft` = '".$main_styles['paddingLeft']."',
							`paddingRight` = '".$main_styles['paddingRight']."',
							`orientation` = '".$main_styles['orientation']."',
							`verticalWidth` = '".$main_styles['verticalWidth']."',
							`animation` = '".$main_styles['animation']."',
							`animationDuration` = '".$main_styles['animationDuration']."',
							`animationTrigger` = '".$main_styles['animationTrigger']."',
							`animationTimeout` = '".$main_styles['animationTimeout']."',
							`sticky` = '".$main_styles['sticky']."',
							`stickyLogoActive` = '".$main_styles['stickyLogoActive']."',
							`stickyUrl` = '".$main_styles['stickyUrl']."',
							`stickyActivate` = '".$main_styles['stickyActivate']."',
							`stickyHeight` = '".$main_styles['stickyHeight']."',						
							`stickyFontColor` = '".$main_styles['stickyFontColor']."',
							`stickyFontHoverColor` = '".$main_styles['stickyFontHoverColor']."',
							`stickyFontSize` = '".$main_styles['stickyFontSize']."',
							`stickyFontSizing` = '".$main_styles['stickyFontSizing']."',
							`stickyFontWeight` = '".$main_styles['stickyFontWeight']."',
							`stickyFontHoverDecoration` = '".$main_styles['stickyFontHoverDecoration']."',						
							`bgStickyStart` = '".$main_styles['bgStickyStart']."',
							`bgStickyEnd` = '".$main_styles['bgStickyEnd']."',
							`stickyTransparency` = '".$main_styles['stickyTransparency']."',
							`devider` = '".$main_styles['devider']."',
							`deviderTransparency` = '".$main_styles['deviderTransparency']."',
							`deviderColor` = '".$main_styles['deviderColor']."',
							`deviderSizing` = '".$main_styles['deviderSizing']."',
							`groupDevider` = '".$main_styles['groupDevider']."',
							`groupTransparency` = '".$main_styles['groupTransparency']."',
							`groupColor` = '".$main_styles['groupColor']."',
							`groupSizing` = '".$main_styles['groupSizing']."',
							`responsiveLabel` = '".$main_styles['responsiveLabel']."',
							`icons` = '".$main_styles['icons']."',
							`iconsColor` = '".$main_styles['iconsColor']."',
							`arrows` = '".$main_styles['arrows']."',
							`arrowTransparency` = '".$main_styles['arrowTransparency']."',
							`arrowColor` = '".$main_styles['arrowColor']."',
							`fontFamily` = '".$main_styles['fontFamily']."',
							`fontColor` = '".$main_styles['fontColor']."',
							`fontHoverColor` = '".$main_styles['fontHoverColor']."',
							`fontSize` = '".$main_styles['fontSize']."',
							`fontSizing` = '".$main_styles['fontSizing']."',
							`fontWeight` = '".$main_styles['fontWeight']."',
							`fontDecoration` = '".$main_styles['fontDecoration']."',
							`fontHoverDecoration` = '".$main_styles['fontHoverDecoration']."',
							`zindex` = '".$main_styles['zindex']."',
							`preset` = '".$main_styles['preset']."',
							`presetSlug` = '".$main_styles['presetSlug']."',
							`iconProductSize` = '".$main_styles['iconProductSize']."',
							`iconProductColor` = '".$main_styles['iconProductColor']."',
							`iconProductHoverColor` = '".$main_styles['iconProductHoverColor']."',
							`siteResponsive` = '".$main_styles['siteResponsive']."',
							`siteResponsiveOne` = '".$main_styles['siteResponsiveOne']."',
							`siteResponsiveTwo` = '".$main_styles['siteResponsiveTwo']."',
							`siteResponsiveThree` = '".$main_styles['siteResponsiveThree']."',
							`logoPaddingLeft` = '".$main_styles['logoPaddingLeft']."',
							`mobileLogoPaddingLeft` = '".$main_styles['mobileLogoPaddingLeft']."',
							`stickyLogoPaddingLeft` = '".$main_styles['stickyLogoPaddingLeft']."',
							`bgMainImage` = '".$main_styles['bgMainImage']."',
							`bgMainImageUrl` = '".$main_styles['bgMainImageUrl']."',
							`bgMainImagePosition` = '".$main_styles['bgMainImagePosition']."',
							`bgMainImageRepeat` = '".$main_styles['bgMainImageRepeat']."',
							`customCss` = '".$main_styles['customCss']."'							
						WHERE
							`mainStyleId` = ". $main_styles['mainStyleId'] .";
					");
						
					#UPDATE DROP DOWN STYLES
					$wpdb->query("
						UPDATE
							`". $wpdb->base_prefix ."hmenu_dropdown_styles`
						SET
							`widthType` = '".$dropdown_styles['widthType']."',
							`width` = '".$dropdown_styles['width']."',
							`padding` = '".$dropdown_styles['padding']."',  
							`border` = '".$dropdown_styles['border']."',
							`borderColor` = '".$dropdown_styles['borderColor']."',
							`borderTransparency` = '".$dropdown_styles['borderTransparency']."',
							`borderType` = '".$dropdown_styles['borderType']."',
							`borderRadius` = '".$dropdown_styles['borderRadius']."',	  
							`shadow` = '".$dropdown_styles['shadow']."',
							`shadowRadius` = '".$dropdown_styles['shadowRadius']."',
							`shadowColor` = '".$dropdown_styles['shadowColor']."',	
							`shadowTransparency` = '".$dropdown_styles['shadowTransparency']."',  
							`bgDropStartColor` = '".$dropdown_styles['bgDropStartColor']."',
							`bgDropGradient` = '".$dropdown_styles['bgDropGradient']."',
							`bgDropEndColor` = '".$dropdown_styles['bgDropEndColor']."',
							`bgDropGradientPath` = '".$dropdown_styles['bgDropGradientPath']."',
							`bgDropTransparency` = '".$dropdown_styles['bgDropTransparency']."', 
							`bgHoverStartColor` = '".$dropdown_styles['bgHoverStartColor']."', 
							`bgHoverGradient` = '".$dropdown_styles['bgHoverGradient']."',
							`bgHoverEndColor` = '".$dropdown_styles['bgHoverEndColor']."', 
							`bgHoverGradientPath` = '".$dropdown_styles['bgHoverGradientPath']."',
							`bgHoverTransparency` = '".$dropdown_styles['bgHoverTransparency']."', 		  
							`arrows` = '".$dropdown_styles['arrows']."', 
							`arrowTransparency` = '".$dropdown_styles['arrowTransparency']."', 
							`arrowColor` = '".$dropdown_styles['arrowColor']."', 		  
							`devider` = '".$dropdown_styles['devider']."', 
							`deviderTransparency` = '".$dropdown_styles['deviderTransparency']."', 
							`deviderColor` = '".$dropdown_styles['deviderColor']."', 		  
							`fontFamily` = '".$dropdown_styles['fontFamily']."', 
							`fontColor` = '".$dropdown_styles['fontColor']."', 
							`fontHoverColor` = '".$dropdown_styles['fontHoverColor']."', 
							`fontSize` = '".$dropdown_styles['fontSize']."',
							`fontSizing` = '".$dropdown_styles['fontSizing']."', 
							`fontWeight` = '".$dropdown_styles['fontWeight']."', 
							`fontDecoration` = '".$dropdown_styles['fontDecoration']."', 
							`fontHoverDecoration` = '".$dropdown_styles['fontHoverDecoration']."'
						WHERE
							`dropStyleId` = ". $dropdown_styles['dropStyleId'] .";
					");
					
					#UPDATE MEGA STYLES
					$wpdb->query("
						UPDATE
							`". $wpdb->base_prefix ."hmenu_mega_styles`
						SET
							`widthType` = '".$mega_styles['widthType']."',
							`width` = '".$mega_styles['width']."',
							`padding` = '".$mega_styles['padding']."',  
							`border` = '".$mega_styles['border']."',
							`borderColor` = '".$mega_styles['borderColor']."',
							`borderTransparency` = '".$mega_styles['borderTransparency']."',
							`borderType` = '".$mega_styles['borderType']."',
							`borderRadius` = '".$mega_styles['borderRadius']."',	  
							`shadow` = '".$mega_styles['shadow']."',
							`shadowRadius` = '".$mega_styles['shadowRadius']."',
							`shadowColor` = '".$mega_styles['shadowColor']."',	
							`shadowTransparency` = '".$mega_styles['shadowTransparency']."',  
							`bgDropStartColor` = '".$mega_styles['bgDropStartColor']."',
							`bgDropGradient` = '".$mega_styles['bgDropGradient']."',
							`bgDropEndColor` = '".$mega_styles['bgDropEndColor']."',
							`bgDropGradientPath` = '".$mega_styles['bgDropGradientPath']."',
							`bgDropTransparency` = '".$mega_styles['bgDropTransparency']."', 
							`bgHoverStartColor` = '".$mega_styles['bgHoverStartColor']."', 
							`bgHoverGradient` = '".$mega_styles['bgHoverGradient']."',
							`bgHoverEndColor` = '".$mega_styles['bgHoverEndColor']."', 
							`bgHoverGradientPath` = '".$mega_styles['bgHoverGradientPath']."',
							`bgHoverTransparency` = '".$mega_styles['bgHoverTransparency']."', 		  
							`arrows` = '".$mega_styles['arrows']."', 
							`arrowTransparency` = '".$mega_styles['arrowTransparency']."', 
							`arrowColor` = '".$mega_styles['arrowColor']."', 		  
							`devider` = '".$mega_styles['devider']."', 
							`deviderTransparency` = '".$mega_styles['deviderTransparency']."', 
							`deviderColor` = '".$mega_styles['deviderColor']."', 
							`fontHoverColor` = '".$mega_styles['fontHoverColor']."', 
							`fontHoverDecoration` = '".$mega_styles['fontHoverDecoration']."'
						WHERE
							`megaStyleId` = ". $mega_styles['megaStyleId'] .";
					");
					
					#UPDATE MOBILE STYLES
					$wpdb->query("
						UPDATE
							`". $wpdb->base_prefix ."hmenu_mobile_styles`
						SET
							`bgBarStartColor` = '".$mobile_styles['bgBarStartColor']."', 
							`bgBarGradient` = '".$mobile_styles['bgBarGradient']."', 
							`bgBarEndColor` = '".$mobile_styles['bgBarEndColor']."', 
							`bgBarGradientPath` = '".$mobile_styles['bgBarGradientPath']."', 
							`bgBarTransparency` = '".$mobile_styles['bgBarTransparency']."', 
							`fontBarFamily` = '".$mobile_styles['fontBarFamily']."', 
							`fontBarColor` = '".$mobile_styles['fontBarColor']."', 
							`fontBarHoverColor` = '".$mobile_styles['fontBarHoverColor']."', 
							`fontBarSize` = '".$mobile_styles['fontBarSize']."', 
							`fontBarSizing` = '".$mobile_styles['fontBarSizing']."', 
							`fontBarWeight` = '".$mobile_styles['fontBarWeight']."', 
							`bgMenuStartColor` = '".$mobile_styles['bgMenuStartColor']."', 
							`bgMenuGradient` = '".$mobile_styles['bgMenuGradient']."', 
							`bgMenuEndColor` = '".$mobile_styles['bgMenuEndColor']."', 
							`bgMenuGradientPath` = '".$mobile_styles['bgMenuGradientPath']."', 
							`bgMenuTransparency` = '".$mobile_styles['bgMenuTransparency']."', 
							`bgHoverStartColor` = '".$mobile_styles['bgHoverStartColor']."', 
							`bgHoverGradient` = '".$mobile_styles['bgHoverGradient']."', 
							`bgHoverEndColor` = '".$mobile_styles['bgHoverEndColor']."', 
							`bgHoverGradientPath` = '".$mobile_styles['bgHoverGradientPath']."', 
							`bgHoverTransparency` = '".$mobile_styles['bgHoverTransparency']."', 
							`fontMobileFamily` = '".$mobile_styles['fontMobileFamily']."', 
							`fontMobileColor` = '".$mobile_styles['fontMobileColor']."', 
							`fontMobileHoverColor` = '".$mobile_styles['fontMobileHoverColor']."', 
							`fontMobileSize` = '".$mobile_styles['fontMobileSize']."', 
							`fontMobileSizing` = '".$mobile_styles['fontMobileSizing']."', 
							`fontMobileWeight` = '".$mobile_styles['fontMobileWeight']."', 
							`fontTabletFamily` = '".$mobile_styles['fontTabletFamily']."', 
							`fontTabletColor` = '".$mobile_styles['fontTabletColor']."', 
							`fontTabletHoverColor` = '".$mobile_styles['fontTabletHoverColor']."', 
							`fontTabletSize` = '".$mobile_styles['fontTabletSize']."', 
							`fontTabletSizing` = '".$mobile_styles['fontTabletSizing']."', 
							`fontTabletWeight` = '".$mobile_styles['fontTabletWeight']."', 
							`paddingLeft` = '".$mobile_styles['paddingLeft']."', 
							`paddingRight` = '".$mobile_styles['paddingRight']."'
						WHERE
							`mobileStyleId` = ". $mobile_styles['mobileStyleId'] .";
					");
					
					#UPDATE MEGA FONT STYLES
					foreach(array_keys($mega_font_styles) as $key){
						$wpdb->query("
							UPDATE
								`". $wpdb->base_prefix ."hmenu_mega_font_styles`
							SET
								`type` = '".$mega_font_styles[$key]['type']."',
								`fontFamily` = '".$mega_font_styles[$key]['fontFamily']."',
								`fontColor` = '".$mega_font_styles[$key]['fontColor']."',
								`fontSize` = '".$mega_font_styles[$key]['fontSize']."',
								`fontSizing` = '".$mega_font_styles[$key]['fontSizing']."',
								`fontWeight` = '".$mega_font_styles[$key]['fontWeight']."'
							WHERE
								`megaFontId` = ". $mega_font_styles[$key]['megaFontId'] .";
						");
					}	
					
					#UPDATE SEARCH STYLES
					$wpdb->query("
						UPDATE
							`". $wpdb->base_prefix ."hmenu_search`
						SET
							`type` = '".$search_styles['type']."',
							`icon` = '".$search_styles['icon']."',
							`label` = '".$search_styles['label']."',  
							`iconColor` = '".$search_styles['iconColor']."',
							`iconHoverColor` = '".$search_styles['iconHoverColor']."',
							`iconSize` = '".$search_styles['iconSize']."',
							`animation` = '".$search_styles['animation']."',
							`placement` = '".$search_styles['placement']."',	  
							`padding` = '".$search_styles['padding']."',
							`width` = '".$search_styles['width']."',
							`height` = '".$search_styles['height']."',	  
							`fontFamily` = '".$search_styles['fontFamily']."',
							`fontColor` = '".$search_styles['fontColor']."',
							`fontSize` = '".$search_styles['fontSize']."',
							`fontSizing` = '".$search_styles['fontSizing']."',
							`fontWeight` = '".$search_styles['fontWeight']."', 
							`border` = '".$search_styles['border']."', 
							`borderColor` = '".$search_styles['borderColor']."',
							`borderTransparency` = '".$search_styles['borderTransparency']."', 
							`borderRadius` = '".$search_styles['borderRadius']."',
							`backgroundColor` = '".$search_styles['backgroundColor']."',
							`placeholder` = '".$search_styles['placeholder']."'									
						WHERE
							`searchId` = ". $search_styles['searchId'] .";
					");
					
					#UPDATE SOCIAL
					foreach(array_keys($social_items) as $key){
						
						if($social_items[$key]['new'] != 1){
							$wpdb->query("
								UPDATE
									`". $wpdb->base_prefix ."hmenu_social`
								SET
									`socialId` = '".$social_items[$key]['socialId']."',
									`menuId` = '".$social_items[$key]['menuId']."',
									`name` = '".$social_items[$key]['name']."',
									`icon` = '".$social_items[$key]['icon']."',
									`iconContent` = '".$social_items[$key]['iconContent']."',
									`iconSize` = '".$social_items[$key]['iconSize']."',
									`iconColor` = '".$social_items[$key]['iconColor']."',
									`iconHoverColor` = '".$social_items[$key]['iconHoverColor']."',
									`link` = '".$social_items[$key]['link']."',
									`target` = '".$social_items[$key]['target']."',
									`order` = '".$social_items[$key]['order']."',
									`deleted` = '".$social_items[$key]['deleted']."'
								WHERE
									`socialId` = ". $social_items[$key]['socialId'] .";
							");	
						} else {
							#DATA: SOCIAL
							$wpdb->query(
								"
									INSERT INTO `". $wpdb->base_prefix ."hmenu_social` 
									(
										`menuId`,
										`name`,
										`icon`,
										`iconContent`,
										`iconSize`,
										`iconColor`,
										`iconHoverColor`,
										`link`,
										`target`,
										`order`,
										`deleted`
									) VALUES (
										'".$social_items[$key]['menuId']."',
										'".$social_items[$key]['name']."',									
										'".$social_items[$key]['icon']."',									
										'".$social_items[$key]['iconContent']."',									
										'".$social_items[$key]['iconSize']."',									
										'".$social_items[$key]['iconColor']."',									
										'".$social_items[$key]['iconHoverColor']."',									
										'".$social_items[$key]['link']."',									
										'".$social_items[$key]['target']."',									
										'".$social_items[$key]['order']."',									
										'".$social_items[$key]['deleted']."'
									)
								"				
							);						
						}
						
					}
					
				} else {
				
				////////////////////////////////////////////////////////////////////////////////////////////////
				/////////////// SAVE NAVIGATION ITEMS
				////////////////////////////////////////////////////////////////////////////////////////////////
				
					#VARIABLE FOR NAV ITEMS CHECK
					$previous_id_array = array();
					$true = '';
					
					#UPDATE NAV ITEMS
					foreach(array_keys($nav_items) as $key){
						
						#SET PARENTING
						$current_lvl = intval($nav_items[$key]['level']);
												
						if($current_lvl == 0){
							$the_parent = 0;
						} else {
							#REVERSE LOOP TO FIND PREVIOUS EQUAL
							for($e = $key; $e >= 0; $e--){
								$prev_lvl_check = intval($nav_items[$e]['level']);
								if($current_lvl > $prev_lvl_check){
									if(intval($nav_items[$e]['new']) != 1){
										$the_parent = intval($nav_items[$e]['navItemId']);
										break;
									} else {
										$the_parent = intval($previous_id_array[$e]); //intval($previous_id_array[$e])
										break;
									}
								}
							}
						}
						
						if($nav_items[$key]['new'] != 1){
							$wpdb->query("
								UPDATE
									`". $wpdb->base_prefix ."hmenu_nav_items`
								SET
									`navItemId` = '".$nav_items[$key]['navItemId']."',
									`parentNavId` = '".$the_parent."',
									`postId` = '".$nav_items[$key]['postId']."',
									`title` = '".$nav_items[$key]['title']."',
									`active` = '".$nav_items[$key]['active']."',
									`activeMobile` = '".$nav_items[$key]['activeMobile']."',
									`name` = '".$nav_items[$key]['name']."',
									`icon` = '".$nav_items[$key]['icon']."',
									`iconContent` = '".$nav_items[$key]['iconContent']."',
									`iconColor` = '".$nav_items[$key]['iconColor']."',
									`iconSize` = '".$nav_items[$key]['iconSize']."',
									`link` = '".$nav_items[$key]['link']."',
									`target` = '".$nav_items[$key]['target']."',
									`order` = '".$nav_items[$key]['order']."',
									`type` = '".$nav_items[$key]['type']."',
									`level` = '".$nav_items[$key]['level']."',
									`deleted` = '".$nav_items[$key]['deleted']."',
									`method` = '".$nav_items[$key]['method']."',
									`methodReference` = '".$nav_items[$key]['methodReference']."'
								WHERE
									`navItemId` = ". $nav_items[$key]['navItemId'] .";
							");
							
							#PUSH IDS INTO ARRAY
							array_push($previous_id_array, $nav_items[$key]['navItemId']);
							
							#MEGA
							if(!empty($nav_items[$key]['mega_menus'])){	
								$wpdb->query("
									UPDATE
										`". $wpdb->base_prefix ."hmenu_mega_menu`
									SET
										`name` = '".$nav_items[$key]['mega_menus'][0]['name']."',
										`layout` = '".$nav_items[$key]['mega_menus'][0]['layout']."',
										`background` = '".$nav_items[$key]['mega_menus'][0]['background']."',
										`backgroundUrl` = '".$nav_items[$key]['mega_menus'][0]['backgroundUrl']."',
										`backgroundPosition` = '".$nav_items[$key]['mega_menus'][0]['backgroundPosition']."'
									WHERE
										`megaMenuId` = ". $nav_items[$key]['mega_menus'][0]['megaMenuId'] .";
								");
							};
							
							#MEGA STUFF
							if(!empty($nav_items[$key]['mega_menus'][0]['mega_stuff'])){	
								
								foreach(array_keys($nav_items[$key]['mega_menus'][0]['mega_stuff']) as $stuff_key){
									
									switch($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['type']){ #POST
										
										#POST
										case 'post':
											
											#NEW
											if($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['new'] == 1){
												
												#INSERT NEW CONTENT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key], 'post', $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['megaMenuId']);										
																						
											} else {
												
												#UPDATE OLD CONTENT
												$wpdb->query("
													UPDATE
														`". $wpdb->base_prefix ."hmenu_mega_blog`
													SET
														`termId` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['termId'] ."',
														`numberPosts` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['numberPosts'] ."',
														`heading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['heading'] ."',
														`headingUnderline` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['headingUnderline'] ."',
														`headingAllow` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['headingAllow'] ."',
														`description` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['description'] ."',
														`descriptionCount` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['descriptionCount'] ."',
														`featuredImage` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['featuredImage'] ."',
														`featuredSize` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['featuredSize'] ."',
														`placement` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['placement'] ."',
														`type` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['type'] ."',
														`target` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['target'] ."',
														`deleted` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['deleted'] ."'
													WHERE
														`megaBlogId` = ". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['id'].";
												");
												
											}
											
										break;
										
										#TEXT
										case 'text':
											
											#NEW
											if($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['new'] == 1){
												
												#INSERT NEW CONTENT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key], 'text', $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['megaMenuId']);
												
											} else {
												
												#UPDATE OLD CONTENT
												$wpdb->query("
													UPDATE
														`". $wpdb->base_prefix ."hmenu_mega_content`
													SET
														`heading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['heading'] ."',
														`headingUnderline` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['headingUnderline'] ."',
														`text` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['text'] ."',	
														`textCount` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['textCount'] ."',
														`textAlignment` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['textAlignment'] ."',	
														`paddingTop` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['paddingTop'] ."',
														`paddingBottom` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['paddingBottom'] ."',
														`placement` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['placement'] ."',
														`type` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['type'] ."',							
														`deleted` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['deleted'] ."'											
													WHERE
														`contentId` = ". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['id'] .";
												");
											
											};
											
										break;
										
										#CONTACT
										case 'contact':
											
											#NEW
											if($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['new'] == 1){
												
												#INSERT NEW CONTENT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key], 'contact', $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['megaMenuId']);
												
											} else {
												
												#UPDATE OLD CONTENT
												$wpdb->query("
													UPDATE
														`". $wpdb->base_prefix ."hmenu_mega_contact`
													SET
														`heading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['heading'] ."',
														`headingUnderline` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['headingUnderline'] ."',
														`html` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['html'] ."',	
														`formHtml` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['formHtml'] ."',
														`shortcode` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['shortcode'] ."',	
														`formShortcode` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['formShortcode'] ."',
														`placement` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['placement'] ."',
														`type` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['type'] ."',							
														`deleted` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['deleted'] ."'											
													WHERE
														`contactId` = ". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['id'] .";
												");
											
											};
											
										break;
										
										#MAP
										case 'map':
											
											#NEW
											if($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['new'] == 1){
												
												#INSERT NEW CONTENT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key], 'map', $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['megaMenuId']);
												
											} else {
												
												#UPDATE OLD CONTENT
												$wpdb->query("
													UPDATE
														`". $wpdb->base_prefix ."hmenu_mega_map`
													SET
														`heading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['heading'] ."',
														`headingUnderline` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['headingUnderline'] ."',
														`map` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['map'] ."',	
														`mapHtml` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mapHtml'] ."',
														`shortcode` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['shortcode'] ."',	
														`mapShortcode` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mapShortcode'] ."',
														`placement` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['placement'] ."',
														`type` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['type'] ."',							
														`deleted` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['deleted'] ."'											
													WHERE
														`mapId` = ". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['id'] .";
												");
											
											};
											
										break;
										
										#IMAGES
										case 'images':
											
											#NEW
											if($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['new'] == 1){
												
												#INSERT NEW CONTENT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key], 'images', $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['megaMenuId']);
												
											} else {
												
												#UPDATE OLD CONTENT
												$wpdb->query("
													UPDATE
														`". $wpdb->base_prefix ."hmenu_mega_image`
													SET
														`heading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['heading'] ."',
														`headingUnderline` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['headingUnderline'] ."',
														`text` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['text'] ."',	
														`url` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['url'] ."',
														`image` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['image'] ."',	
														`imageHeading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['imageHeading'] ."',	
														`displayType` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['displayType'] ."',
														`placement` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['placement'] ."',
														`type` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['type'] ."',							
														`deleted` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['deleted'] ."'											
													WHERE
														`imageId` = ". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['id'] .";
												");
											
											};
											
										break;
										
										#WOO PRODUCTS
										case 'woo':
											
											#NEW
											if($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['new'] == 1){
												
												#INSERT NEW PRODUCT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key], 'woo', $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['megaMenuId']);
												
											} else {
												
												#UPDATE OLD CONTENT
												$wpdb->query("
													UPDATE
														`". $wpdb->base_prefix ."hmenu_mega_product`
													SET
														`heading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['heading'] ."',
														`headingUnderline` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['headingUnderline'] ."',
														`icon` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['icon'] ."',	
														`description` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['description'] ."',
														`placement` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['placement'] ."',
														`productCategory` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['productCategory'] ."',
														`productToDisplay` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['productToDisplay'] ."',
														`productHeading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['productHeading'] ."',
														`productPrice` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['productPrice'] ."',
														`productDescription` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['productDescription'] ."',
														`productImage` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['productImage'] ."',
														`productLink` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['productLink'] ."',
														`productTarget` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['productTarget'] ."',
														`type` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['type'] ."',							
														`deleted` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['deleted'] ."'											
													WHERE
														`productId` = ". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['id'] .";
												");
											
											};
											
										break;
										
										#TEXT
										case 'list':
											
											#NEW
											if($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['new'] == 1){
												
												#INSERT NEW CONTENT
												$list_item_id = $this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key], 'list', $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['megaMenuId']);
												
												#ADD LIST ITEMS IF EXIST
												if(!empty($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'])){
													
													foreach(array_keys($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items']) as $new_list_key){
														
														#INSERT NEW CONTENT
														$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key], 'list_items', $list_item_id);
														
													}
													
												}
												
											} else {
												
												#UPDATE OLD CONTENT
												$wpdb->query("
													UPDATE
														`". $wpdb->base_prefix ."hmenu_mega_list`
													SET
														`heading` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['heading'] ."',
														`headingUnderline` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['headingUnderline'] ."',
														`text` = NULL,	
														`textCount` = NULL,
														`textAlignment` = NULL,	
														`paddingTop` = NULL,
														`paddingBottom` = NULL,
														`placement` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['placement'] ."',
														`type` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['type'] ."',							
														`deleted` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['deleted'] ."'											
													WHERE
														`listId` = ". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['id'] .";
												");
												
												#ADD LIST ITEMS IF EXIST
												if(!empty($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'])){
													
													foreach(array_keys($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items']) as $new_list_key){
														
														if($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['new'] == 1){
															
															#INSERT NEW CONTENT
															$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key], 'list_items', $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['id']);
																												
														} else {
															
															#UPDATE OLD LIST ITEMS
															$wpdb->query("
																UPDATE
																	`". $wpdb->base_prefix ."hmenu_mega_list_items`
																SET
																	`name` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['name'] ."',
																	`type` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['type'] ."',
																	`postId` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['postId'] ."',	
																	`termId` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['termId'] ."',
																	`taxonomy` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['taxonomy'] ."',	
																	`alt` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['alt'] ."',
																	`url` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['url'] ."',
																	`target` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['target'] ."',
																	`icon` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['icon'] ."',							
																	`iconContent` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['iconContent'] ."',	
																	`desc` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['desc'] ."',							
																	`description` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['description'] ."',						
																	`iconSize` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['iconSize'] ."',							
																	`iconColor` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['iconColor'] ."',							
																	`order` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['order'] ."',							
																	`deleted` = '". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['deleted'] ."'											
																WHERE
																	`listItemId` = ". $nav_items[$key]['mega_menus'][0]['mega_stuff'][$stuff_key]['mega_list_items'][$new_list_key]['listItemId'] .";
															");
															
														}
														
													}
													
												}
											
											};
											
										break;
										
									} 
									
								};
															
							};
							
					
							#DELETE 
							if(!empty($nav_items[$key]['mega_menus'][0]['deleted_items'])){	
								foreach(array_keys($nav_items[$key]['mega_menus'][0]['deleted_items']) as $delete_key){
									
									#BLOG
									if($nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['type'] == 'post'){
										$wpdb->query("
											UPDATE
												`". $wpdb->base_prefix ."hmenu_mega_blog`
											SET							
												`deleted` = '". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['deleted'] ."'											
											WHERE
												`megaBlogId` = ". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['id'] .";
										");								
									};
									
									#CONTENT
									if($nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['type'] == 'text'){
										$wpdb->query("
											UPDATE
												`". $wpdb->base_prefix ."hmenu_mega_content`
											SET							
												`deleted` = '". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['deleted'] ."'											
											WHERE
												`contentId` = ". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['id'] .";
										");
									};
									
									#CONTACT
									if($nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['type'] == 'contact'){
										$wpdb->query("
											UPDATE
												`". $wpdb->base_prefix ."hmenu_mega_contact`
											SET							
												`deleted` = '". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['deleted'] ."'											
											WHERE
												`contactId` = ". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['id'] .";
										");
									};
									
									#MAP
									if($nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['type'] == 'map'){
										$wpdb->query("
											UPDATE
												`". $wpdb->base_prefix ."hmenu_mega_map`
											SET							
												`deleted` = '". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['deleted'] ."'											
											WHERE
												`mapId` = ". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['id'] .";
										");
									};
									
									#IMAGES
									if($nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['type'] == 'images'){
										$wpdb->query("
											UPDATE
												`". $wpdb->base_prefix ."hmenu_mega_image`
											SET							
												`deleted` = '". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['deleted'] ."'											
											WHERE
												`imageId` = ". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['id'] .";
										");
									};
									
									#PRODUCT
									if($nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['type'] == 'woo'){
										$wpdb->query("
											UPDATE
												`". $wpdb->base_prefix ."hmenu_mega_product`
											SET							
												`deleted` = '". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['deleted'] ."'											
											WHERE
												`productId` = ". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['id'] .";
										");
									};
									
									#LIST
									if($nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['type'] == 'list'){
										$wpdb->query("
											UPDATE
												`". $wpdb->base_prefix ."hmenu_mega_list`
											SET							
												`deleted` = '". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['deleted'] ."'											
											WHERE
												`listId` = ". $nav_items[$key]['mega_menus'][0]['deleted_items'][$delete_key]['id'] .";
										");
									};
																			
								};
								
							};
							
							
						} else {
							
							#SET PARENTING
							$current_lvl = intval($nav_items[$key]['level']);
													
							if($current_lvl == 0){
								$the_parent = 0;
							} else {
								#REVERSE LOOP TO FIND PREVIOUS EQUAL
								for($e = $key; $e >= 0; $e--){
									$prev_lvl_check = intval($nav_items[$e]['level']);
									if($current_lvl > $prev_lvl_check){
										if(intval($nav_items[$e]['new']) != 1){
											$the_parent = intval($nav_items[$e]['navItemId']);
											break;
										} else {
											$the_parent = intval($previous_id_array[$e]); //intval($previous_id_array[$e])
											break;
										}
									}
								}
							}
												
							$wpdb->query(
								"
									INSERT INTO `". $wpdb->base_prefix ."hmenu_nav_items` 
									(
										`menuId`,
										`parentNavId`,
										`postId`,
										`name`,
										`title`,
										`active`,
										`activeMobile`,
										`icon`,
										`iconContent`,
										`iconSize`,
										`iconColor`,
										`link`,
										`order`,
										`target`,
										`type`,
										`level`,
										`deleted`,
										`method`,
										`methodReference`
									) VALUES (
										'".$menu['menuId']."', 
										'".$the_parent."',
										'".$nav_items[$key]['postId']."',
										'".$nav_items[$key]['name']."',									
										'".$nav_items[$key]['title']."',
										".$nav_items[$key]['active'].", 
										".$nav_items[$key]['activeMobile'].",
										".$nav_items[$key]['icon'].",								
										'".$nav_items[$key]['iconContent']."',						
										'".$nav_items[$key]['iconSize']."',						
										'".$nav_items[$key]['iconColor']."',						
										'".$nav_items[$key]['link']."',
										'".$nav_items[$key]['order']."',						
										'".$nav_items[$key]['target']."',						
										'".$nav_items[$key]['type']."',						
										'".$nav_items[$key]['level']."',						
										'".$nav_items[$key]['deleted']."',
										".$nav_items[$key]['method'].", 
										'".$nav_items[$key]['methodReference']."'
									)
								"				
							);	
							
							#ID OF LAST NAV ITEM
							$nav_item_id = $wpdb->insert_id;
							
							#PUSH IDS INTO ARRAY						
							array_push($previous_id_array, intval($nav_item_id));
							
							if(!empty($nav_items[$key]['mega_menus'])){							
								
								#DATA: MEGA
								$wpdb->query(
									"
										INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_menu` 
										(
											`navItemId`,
											`name`,
											`layout`,
											`background`,
											`backgroundUrl`,
											`backgroundPosition`
										) VALUES (
											'".$nav_item_id."',
											'".$nav_items[$key]['mega_menus'][0]['name']."',									
											'".$nav_items[$key]['mega_menus'][0]['layout']."',									
											'".$nav_items[$key]['mega_menus'][0]['background']."',									
											'".$nav_items[$key]['mega_menus'][0]['backgroundUrl']."',									
											'".$nav_items[$key]['mega_menus'][0]['backgroundPosition']."'
										)
									"				
								);
								
								#ID OF LAST MEGA ITEM
								$mega_item_id = $wpdb->insert_id;
								
								if(!empty($nav_items[$key]['mega_menus'][0]['mega_stuff'])){
																	
									foreach(array_keys($nav_items[$key]['mega_menus'][0]['mega_stuff']) as $new_key){
										
										switch($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key]['type']){
											
											case 'post':
												#INSERT NEW BLOG/POST
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key], 'post', $mega_item_id);
											break;
											
											case 'text':
												#INSERT NEW CONTENT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key], 'text', $mega_item_id);
											break;
											
											case 'contact':
												#INSERT NEW CONTACT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key], 'contact', $mega_item_id);
											break;
											
											case 'map':
												#INSERT NEW MAP
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key], 'map', $mega_item_id);
											break;
											
											case 'images':
												#INSERT NEW IMAGE
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key], 'images', $mega_item_id);
											break;
											
											case 'woo':
												#INSERT NEW PRODUCT
												$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key], 'woo', $mega_item_id);											
											break;
											
											case 'list':
												#INSERT NEW LIST
												$list_item_id = $this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key], 'list', $mega_item_id);
												
												#ADD LIST ITEMS IF EXIST
												if(!empty($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key]['mega_list_items'])){
													
													foreach(array_keys($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key]['mega_list_items']) as $new_list_key){
														
														#INSERT NEW CONTENT
														$this->insert_col_data($nav_items[$key]['mega_menus'][0]['mega_stuff'][$new_key]['mega_list_items'][$new_list_key], 'list_items', $list_item_id);
														
													}
													
												}
												
											break;
											
										}
										
									}
									
								};
														
							}
													
						}
						
					}
					
				}
				
			}		
			
			#SUCCESS			
			echo json_encode(array('status' => true, 'menu_id' => $menu['menuId']));	
			exit;
			
		}
		
		#INSERT COL CONTENT		
		public function insert_col_data($obj, $type, $related_id){
			
			#GLOBALS
			global $hmenu_helper, $wpdb;
			
			#CHECK THE TYPE OF CONTENT
			switch($type){
				
				#POST / BLOG
				case 'post':
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_blog` 
							(
								`megaMenuId`,
								`termId`,
								`numberPosts`,
								`heading`,
								`headingUnderline`,
								`headingAllow`,
								`description`,
								`descriptionCount`,
								`featuredImage`,
								`featuredSize`,
								`placement`,
								`type`,
								`target`,
								`content`
							) VALUES (
								'".$related_id."', 
								'".$obj['termId']."',
								'".$obj['numberPosts']."',									
								'".$obj['heading']."',
								'".$obj['headingUnderline']."', 
								'".$obj['headingAllow']."',
								'".$obj['description']."',								
								'".$obj['descriptionCount']."',						
								'".$obj['featuredImage']."',						
								'".$obj['featuredSize']."',						
								'".$obj['placement']."',
								'".$obj['type']."',						
								'".$obj['target']."',						
								'".$obj['content']."'
							)
						"				
					);
				break;
				
				#TEXT
				case 'text':
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_content` 
							(
								`megaMenuId`,
								`heading`,
								`headingUnderline`,
								`text`,
								`textCount`,
								`textAlignment`,
								`paddingTop`,
								`paddingBottom`,
								`placement`,
								`type`
							) VALUES (
								'".$related_id."', 
								'".$obj['heading']."',
								'".$obj['headingUnderline']."', 
								'".$obj['text']."',
								'".$obj['textCount']."',								
								'".$obj['textAlignment']."',						
								'".$obj['paddingTop']."',						
								'".$obj['paddingBottom']."',						
								'".$obj['placement']."',
								'".$obj['type']."'
							)
						"				
					);	
				break;	
				
				#CONTACT
				case 'contact':
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_contact` 
							(
								`megaMenuId`,
								`heading`,
								`headingUnderline`,
								`html`,
								`formHtml`,
								`shortcode`,
								`formShortcode`,
								`placement`,
								`type`
							) VALUES (
								'".$related_id."', 
								'".$obj['heading']."',
								'".$obj['headingUnderline']."', 
								'".$obj['html']."',
								'".$obj['formHtml']."',								
								'".$obj['shortcode']."',						
								'".$obj['formShortcode']."',							
								'".$obj['placement']."',
								'".$obj['type']."'
							)
						"				
					);	
				break;	
				
				#MAP
				case 'map':
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_map` 
							(
								`megaMenuId`,
								`heading`,
								`headingUnderline`,
								`map`,
								`mapHtml`,
								`shortcode`,
								`mapShortcode`,
								`placement`,
								`type`
							) VALUES (
								'".$related_id."', 
								'".$obj['heading']."',
								'".$obj['headingUnderline']."', 
								'".$obj['map']."',
								'".$obj['mapHtml']."',								
								'".$obj['shortcode']."',						
								'".$obj['mapShortcode']."',							
								'".$obj['placement']."',
								'".$obj['type']."'
							)
						"				
					);	
				break;	
				
				#IMAGES
				case 'images':
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_image` 
							(
								`megaMenuId`,
								`heading`,
								`headingUnderline`,
								`text`,
								`url`,
								`image`,
								`imageHeading`,
								`displayType`,
								`placement`,
								`type`
							) VALUES (
								'".$related_id."', 
								'".$obj['heading']."',
								'".$obj['headingUnderline']."', 
								'".$obj['text']."',
								'".$obj['url']."',								
								'".$obj['image']."',
								'".$obj['imageHeading']."',						
								'".$obj['displayType']."',							
								'".$obj['placement']."',
								'".$obj['type']."'
							)
						"				
					);	
				break;	
				
				#PRODUCT
				case 'woo':
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_product` 
							(
								`megaMenuId`,
								`heading`,
								`headingUnderline`,
								`icon`,
								`description`,
								`placement`,
								`productCategory`,
								`productToDisplay`,
								`productHeading`,
								`productPrice`,
								`productDescription`,
								`productImage`,
								`productLink`,
								`productTarget`,
								`type`
							) VALUES (
								'".$related_id."', 
								'".$obj['heading']."',
								'".$obj['headingUnderline']."', 
								'".$obj['icon']."',
								'".$obj['description']."',								
								'".$obj['placement']."',						
								'".$obj['productCategory']."',
								'".$obj['productToDisplay']."',							
								'".$obj['productHeading']."',
								'".$obj['productPrice']."',
								'".$obj['productDescription']."',
								'".$obj['productImage']."',
								'link',
								'".$obj['productTarget']."',
								'".$obj['type']."'
							)
						"				
					);	
				break;	
				
				#TEXT
				case 'list':
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_list` 
							(
								`megaMenuId`,
								`heading`,
								`headingUnderline`,
								`text`,
								`textCount`,
								`textAlignment`,
								`paddingTop`,
								`paddingBottom`,
								`placement`,
								`type`
							) VALUES (
								'".$related_id."', 
								'".$obj['heading']."',
								'".$obj['headingUnderline']."', 
								NULL,
								NULL,								
								NULL,						
								NULL,						
								NULL,						
								'".$obj['placement']."',
								'".$obj['type']."'
							)
						"				
					);	
					return $wpdb->insert_id;;
				break;	
				
				case 'list_items':
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_mega_list_items` 
							(
								`listId`,
								`name`,
								`type`,
								`postId`,
								`termId`,
								`taxonomy`,
								`alt`,
								`url`,
								`target`,
								`icon`,
								`iconContent`,
								`desc`,
								`description`,
								`iconSize`,
								`iconColor`,																
								`order`,															
								`deleted`
							) VALUES (
								'".$related_id."', 
								'".$obj['name']."',									
								'".$obj['type']."',	
								'".$obj['postId']."',	
								'".$obj['termId']."',	
								'".$obj['taxonomy']."',							
								'".$obj['alt']."',						
								'".$obj['url']."',						
								'".$obj['target']."',						
								'".$obj['icon']."',
								'".$obj['iconContent']."',
								'".$obj['desc']."',
								'".$obj['description']."',						
								'".$obj['iconSize']."',						
								'".$obj['iconColor']."',						
								'".$obj['order']."',
								'".$obj['deleted']."'																
							)
						"				
					);
				break;
				
			};
			
		}
			
		
	}