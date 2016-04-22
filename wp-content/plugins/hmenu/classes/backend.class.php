<?php
	
	#PLUGIN BACK-END MANAGEMENT
	class hmenu_backend{
		
		#CLASS VARS
		private $plugin_dir;
		
		#CONSTRUCT
		public function __construct($plugin_dir){
			$this->plugin_dir = $plugin_dir;
		}
		
		#VALIDATE MEGA MENU INSERT
		public function validate_mega(){
			
			#GLOBALS
			global $hmenu_helper, $wpdb;
			
			#GET POST DATA
			$post_data = $_POST['form_data'];
		   	$form_data = array();
		   	parse_str($post_data, $form_data);			
			
			#VALIDATE INSERT DATA
			$status = true;
			if($hmenu_helper->checkString($form_data['mega_menu_name'])){
				$mega_name = true;
			}else{
				$mega_name = false;
				$status = false;
			}
			
			#CHECK STATUS
			if($status){							
				echo json_encode(array('status' => true, 'object' => array(			
					'name' => $form_data['mega_menu_name']				
				)));
				exit;
			}
			#ERROR
			echo json_encode(array('status' => false, 'object' => array(			
				'mega_menu_name' => $mega_name				
			)));			
			exit;
			
		}
		
		#VALIDATE CUSTOM LINKS NAV ITEM
		public function validate_custom(){
			
			#GLOBALS
			global $hmenu_helper, $wpdb;
			
			#GET POST DATA
			$post_data = $_POST['form_data'];
		   	$form_data = array();
		   	parse_str($post_data, $form_data);			
			
			#VALIDATE INSERT DATA
			$status = true;
			if($hmenu_helper->checkString($form_data['custom_name'])){
				$custom_name = true;
			}else{
				$custom_name = false;
				$status = false;
			}
			
			if($hmenu_helper->checkString($form_data['custom_url'])){
				$custom_url = true;
			}else{
				$custom_url = false;
				$status = false;
			}
			
			#CHECK STATUS
			if($status){							
				echo json_encode(array('status' => true, 'object' => array(			
					'name' => $form_data['custom_name']				
				)));
				exit;
			}
			#ERROR
			echo json_encode(array('status' => false, 'object' => array(			
				'custom_name' => $custom_name,
				'custom_url' => $custom_url			
			)));			
			exit;
			
		}
		
		#VALIDATE CUSTOM LINKS NAV ITEM
		public function validate_custom_method(){
			
			#GLOBALS
			global $hmenu_helper, $wpdb;
			
			#GET POST DATA
			$post_data = $_POST['form_data'];
		   	$form_data = array();
		   	parse_str($post_data, $form_data);			
			
			#VALIDATE INSERT DATA
			$status = true;
			if($hmenu_helper->checkString($form_data['custom_method_name'])){
				$custom_method_name = true;
			}else{
				$custom_method_name = false;
				$status = false;
			}
			
			if($hmenu_helper->checkString($form_data['custom_method'])){
				$custom_method = true;
			}else{
				$custom_method = false;
				$status = false;
			}
			
			#CHECK STATUS
			if($status){							
				echo json_encode(array('status' => true, 'object' => array(			
					'name' => $form_data['custom_method_name']				
				)));
				exit;
			}
			#ERROR
			echo json_encode(array('status' => false, 'object' => array(			
				'custom_method_name' => $custom_method_name,
				'custom_method' => $custom_method			
			)));			
			exit;
			
		}
		
		#SEARCH ARRAY
		public function search_array($array,$id,$val){
			
			foreach($array as $key => $item){
				if($item[$id] === $val){
					return $key;
				}
			}
			return false;
			
		}
		
		#CONVERT TO INT
		public function convert_int($str){
			return $str +0;
		}
		
		#GET FRONTEND FONTS
		public function get_frontend_fonts(){
			
			global $wpdb;
			
			#RESULT
			$result = $wpdb->get_results("
				SELECT
					`f`.`fontId`,
					`f`.`fontName`					
				FROM
					`". $wpdb->base_prefix ."hmenu_font_pack` AS `f`
				WHERE
					`f`.`deleted` = 0
				ORDER BY
					`f`.`fontId` DESC;
			");
			
			return $result;
			
		}
		
		#GET FONTS
		public function get_fonts($type = NULL, $js = true){
			
			global $wpdb;
			
			#GET POST DATA
			$icons_to_fetch = $type != NULL ? $type : $_POST['icons'];
			
			$sql_and = '';
			
			if($icons_to_fetch == 'social'){
				$sql_string = "
							WHERE 
								`f`.`fontPackType` = '".$icons_to_fetch."'
							AND
								`f`.`deleted` = 0
							";
			} else {
				$sql_string = "
							WHERE
								`f`.`deleted` = 0
							";
			}
						
			$result = $wpdb->get_results("
				SELECT
					`f`.`fontId`,
					`f`.`fontName`,
					`f`.`fontPackType`,
					`f`.`fontPackName`,
					`f`.`fontEot`,
					`f`.`fontWoff`,					
					`f`.`fontWoff2`,
					`f`.`fontTtf`,
					`f`.`fontSvg`,
					`ic`.`iconId`,
					`ic`.`iconContent`,
					`ic`.`iconPosition`
				FROM
					`". $wpdb->base_prefix ."hmenu_font_pack` AS `f`
					LEFT JOIN `". $wpdb->base_prefix ."hmenu_font_icons` AS `ic` ON(`ic`.`fontId` = `f`.`fontId` AND `ic`.`deleted` = '0')
				".$sql_string."
				ORDER BY
					`f`.`fontId` DESC;
			");

			#CREATE OBJECT
			$font_object = array(
				'font' => array()				
			);
			
			if($result){
				
				foreach($result as $font){
					
					$key = $this->search_array($font_object['font'],'fontId',$font->fontId);
					
					if(!is_numeric($key)){
						#create new slides entry and add the first element
						array_push($font_object['font'], array(
							'fontId' => $font->fontId,
							'fontName' => $font->fontName,
							'fontPackName' => $font->fontPackName,
							'fontEot' => $font->fontEot,
							'fontWoff' => $font->fontWoff,							
							'fontWoff2' => $font->fontWoff2,
							'fontTtf' => $font->fontTtf,
							'fontSvg' => $font->fontSvg,
							'icons' => array(
								array(
									'iconId' => $font->iconId,
									'iconContent' => $font->iconContent,
									'iconPosition' => $font->iconPosition
								)
							)
						));
					}else{
						#ADD ICON TO ICONS NODE
						array_push($font_object['font'][$key]['icons'],array(
							'iconId' => $font->iconId,
							'iconContent' => $font->iconContent,
							'iconPosition' => $font->iconPosition
						));
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
			
			if($icons_to_fetch == 'social'){
				#CREATE ALL FILES FOR FONT VIEWING
				$fonts = $wpdb->get_results("SELECT * FROM ". $wpdb->base_prefix ."hmenu_font_pack WHERE fontPackType = 'social' AND deleted = '0'");
			} else {
				#CREATE ALL FILES FOR FONT VIEWING
				$fonts = $wpdb->get_results("SELECT * FROM ". $wpdb->base_prefix ."hmenu_font_pack WHERE deleted = '0'");
			}
			
			
			$font_build_object = array(
				'font' => array()				
			);
			
			#THE FONTS DIRECTORY > THIS WILL HOUSE ALL THE FONT FILES FOR DISPLAY ON THE BACKEND PREVIEW
			$fonts_directory = $this->plugin_dir .'/_fonts/';
			
			if($fonts){
				//CREATE THOSE MAGICAL FOLDERS
				if (!is_dir($fonts_directory)) {
					mkdir($fonts_directory, 0777, true);
				}	
				
				$counter = 0;
					
				foreach($fonts as $font){
					//array_push($font_build_object['font'], $font);
					
					$key = $this->search_array($font_object['font'],'fontId',$font->fontId);
					
					$font_name = $font->fontName;
					$eot = $font->fontEot;
					$woff = $font->fontWoff;
					$woff2 = $font->fontWoff2;
					$ttf = $font->fontTtf;
					$svg = $font->fontSvg;
					
					#CREATE FILE FOR EOT
					if (!is_dir($fonts_directory . $font_name)) {
						mkdir($fonts_directory . $font_name, 0777, true);
					}	
					
					#CREATE FONT FILE
					if($eot){
						$decoded_eot = base64_decode($eot);
						$encoded_eot = $eot;
						$eot_file = fopen($fonts_directory . $font_name . '/' . $font_name . '.eot', "w");
						fwrite($eot_file, $decoded_eot);
					}
					if($woff){
						$decoded_woff = base64_decode($woff);
						$encoded_woff = $woff;
						$woff_file = fopen($fonts_directory . $font_name . '/' . $font_name . '.woff', "w");
						fwrite($woff_file, $decoded_woff);
					}
					if($woff2){
						$decoded_woff2 = base64_decode($woff2);
						$encoded_woff2 = $woff2;
						$woff_file2 = fopen($fonts_directory . $font_name . '/' . $font_name . '.woff2', "w");
						fwrite($woff_file2, $decoded_woff2);
					}
					if($ttf){
						$decoded_ttf = base64_decode($ttf);
						$encoded_ttf = $ttf;
						$ttf_file = fopen($fonts_directory . $font_name . '/' . $font_name . '.ttf', "w");
						fwrite($ttf_file, $decoded_ttf);
					}
					if($svg){
						$decoded_svg = base64_decode($svg);
						$encoded_svg = $svg;
						$svg_file = fopen($fonts_directory . $font_name . '/' . $font_name . '.svg', "w");
						fwrite($svg_file, $decoded_svg);
					}
										
					//CREAT FONT MAIN STYLESHEET
					
					$css_file = fopen($fonts_directory . $font_name . '.css', "w");
					
					$css_font_face_code .= "@font-face {";
					  $css_font_face_code .= "font-family: '".$font_name."';";
					  $css_font_face_code .= "font-style: normal;";
					  $css_font_face_code .= "font-weight: normal;";
					  $css_font_face_code .= "src: url(".$font_name . '/' . $font_name.".eot);";						  
					$css_font_face_code .= "}";
					
					$css_font_face_code .= "@font-face {";
					  $css_font_face_code .= "font-family: '".$font_name."';";
					  $css_font_face_code .= "font-style: normal;";
					  $css_font_face_code .= "font-weight: normal;";
					  $css_font_face_code .= "src: url(data:application/x-font-woff;charset=utf-8;base64,".$encoded_woff.") format('woff');";
					$css_font_face_code .= "}";	
					
					$css_font_face_code .= "@font-face {";
					  $css_font_face_code .= "font-family: '".$font_name."';";
					  $css_font_face_code .= "font-style: normal;";
					  $css_font_face_code .= "font-weight: normal;";
					  $css_font_face_code .= "src: url(".$font_name . '/' . $font_name.".woff2) format('woff2');";						  
					$css_font_face_code .= "}";	
					
					$css_font_face_code .= "@font-face {";
					  $css_font_face_code .= "font-family: '".$font_name."';";
					  $css_font_face_code .= "font-style: normal;";
					  $css_font_face_code .= "font-weight: normal;";
					  $css_font_face_code .= "src: url(".$font_name . '/' . $font_name.".ttf) format('truetype');";						  
					$css_font_face_code .= "}";
					
					$css_font_face_code .= "@font-face {";
					  $css_font_face_code .= "font-family: '".$font_name."';";
					  $css_font_face_code .= "font-style: normal;";
					  $css_font_face_code .= "font-weight: normal;";
					  $css_font_face_code .= "src: url(".$font_name . '/' . $font_name.".svg#".$font_name.") format('svg');";						  
					$css_font_face_code .= "}";				
					
					#ICON STYTLES
					
					foreach($font_object['font'][$key]['icons'] as $icon){
						$css_font_face_code .= ".icon_".$font_name."_".$icon['iconContent'].":".$icon['iconPosition']." {";
							$css_font_face_code .= "font-family:'".$font_name."';";
							$css_font_face_code .= "content: '\\".$icon['iconContent']."';";
							$css_font_face_code .= "font-size: 30px;";
							$css_font_face_code .= "text-align: center;";
							$css_font_face_code .= "margin: 0 5px;";
							$css_font_face_code .= "line-height: 40px;";
							$css_font_face_code .= "text-rendering: auto;";
							$css_font_face_code .= "-webkit-font-smoothing: antialiased;";
							$css_font_face_code .= "-moz-osx-font-smoothing: grayscale;";
							$css_font_face_code .= "transform: translate(0, 0);";
						$css_font_face_code .= "}";
					}
					
					fwrite($css_file, $css_font_face_code);
					
					$counter++;
				}
			}
			
			
			#SOMETHING WENT WRONG
			if($js){
				echo json_encode($font_object);			
				exit();			
			}else{
				return true;
			}

		}
		
		#INSERT FONT
		public function insert_font($json_obj, $fonts){
			
			global $wpdb;	
			
			$status = false;		
					
			foreach($json_obj as $obj){
				
				#CHECK TO SEE IF PACK ALREADY EXISTS
				$check_pack = $wpdb->get_row("SELECT * FROM ". $wpdb->base_prefix ."hmenu_font_pack WHERE fontName = '".$obj[0]->fontName."' AND deleted = '0'");
				
				#IF PACK WAS NOT FOUND
				if(!$check_pack){
					
					$status = true;
					
					#INSERT	PACK
					$wpdb->query("INSERT INTO `". $wpdb->base_prefix ."hmenu_font_pack` ( `fontName` , `fontPackType` , `fontPackName` , `fontEot` , `fontWoff`, `fontWoff2` , `fontTtf` , `fontSvg` ) VALUES ('".$obj[0]->fontName."', '".$obj[0]->fontPackType."', '".$obj[0]->fontPackName."', '".$fonts['eot']."', '".$fonts['woff']."', '".$fonts['woff2']."', '".$fonts['ttf']."', '".$fonts['svg']."' )");
					$font_pack_entry_id = $wpdb->insert_id;
					
					foreach($obj[0]->icons as $icon){
						
						#INSERT	ICONS
						$wpdb->query("INSERT INTO `". $wpdb->base_prefix ."hmenu_font_icons` ( `fontId`, `iconPosition`, `iconContent` ) VALUES ('".$font_pack_entry_id."', '".$icon->position."', '".$icon->iconContent."' )");
						
					}
									
				} else {
					
					#UPDATE
					$wpdb->query("
					UPDATE
							`". $wpdb->base_prefix ."hmenu_font_pack`
						SET
							`fontEot` = '".$fonts['eot']."',
							`fontWoff` = '".$fonts['woff']."',
							`fontWoff2` = '".$fonts['woff2']."',
							`fontTtf` = '".$fonts['ttf']."',
							`fontSvg` = '".$fonts['svg']."'
						WHERE
							`fontId` = ". $check_pack->fontId .";
					");
					
				}
				
			}
					
			return ($status);			
			
		}
		
	}