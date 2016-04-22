<?php
	
	#UPDATE PLUGIN
	class hmenu_update_plugin{
		
		#CLASS VARS
		private $plugin_name;
		private $plugin_version;
		private $plugin_old_version;
		private $plugin_dir;
		
		#CONSTRUCT
		public function __construct($plugin_name,$plugin_version,$plugin_old_version,$plugin_dir){
			//set class vars
			$this->plugin_name = $plugin_name;
			$this->plugin_version = $plugin_version;
			$this->plugin_old_version = $plugin_old_version;
			$this->plugin_dir = $plugin_dir;
		}
		
		#TEARDOWN PLUGIN
		public function update_plugin(){
			
			//access globals
			global $wpdb;			
			global $hmenu_helper;
			
			//update plugin tables
			
			// < 0.1.7
			if(version_compare($this->plugin_old_version .'', '0.1.7', "<")){
				//update main styles table
				$wpdb->query("ALTER TABLE `". $wpdb->base_prefix ."hmenu_main_styles` 
					ADD COLUMN `logoPaddingLeft` VARCHAR(11) NOT NULL DEFAULT '0',
					ADD COLUMN `mobileLogoPaddingLeft` VARCHAR(11) NOT NULL DEFAULT '10',
					ADD COLUMN `stickyLogoPaddingLeft` VARCHAR(11) NOT NULL DEFAULT '10';
				");				
			}
			
			// < 1.1.23
			if(version_compare($this->plugin_old_version .'', '1.1.23', "<")){
				//update nav items table
				$wpdb->query("ALTER TABLE `". $wpdb->base_prefix ."hmenu_nav_items` 
					ADD COLUMN `method` TINYINT(1) NOT NULL DEFAULT '0',
					ADD COLUMN `methodReference` VARCHAR(255) NOT NULL DEFAULT 'functionName';
				");				
			}
			
			// < 1.2.3
			if(version_compare($this->plugin_old_version .'', '1.2.3', "<")){
				//update main styles table
				$wpdb->query("ALTER TABLE `". $wpdb->base_prefix ."hmenu_main_styles` 
					ADD COLUMN `bgMainImage` TINYINT(1) NOT NULL DEFAULT '0',
					ADD COLUMN `bgMainImageUrl` VARCHAR(255) NOT NULL DEFAULT '',
					ADD COLUMN `bgMainImagePosition` VARCHAR(50) NOT NULL DEFAULT 'left',
					ADD COLUMN `bgMainImageRepeat` VARCHAR(50) NOT NULL DEFAULT 'repeat';
				");				
			}
			
			// < 1.2.16
			if(version_compare($this->plugin_old_version .'', '1.2.16', "<")){
				//update font pack table
				$wpdb->query("ALTER TABLE `". $wpdb->base_prefix ."hmenu_font_pack` 
					ADD COLUMN `fontWoff2` BLOB DEFAULT NULL
				");				
			}
			
			// < 1.3.10
			if(version_compare($this->plugin_old_version .'', '1.3.10', "<")){
				//update main styles table
				$wpdb->query("ALTER TABLE `". $wpdb->base_prefix ."hmenu_main_styles`
					ADD COLUMN `logoLink` VARCHAR(255) DEFAULT NULL,
					ADD COLUMN `logoLinkTarget` VARCHAR(20) NOT NULL DEFAULT '_self';
				");				
			}
			
			// < 1.3.12
			if(version_compare($this->plugin_old_version .'', '1.3.12', "<")){
				//settings
				$contraint_uid = date('Hidmy');		
				//sql	
				$sql_create = "
					CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mobile_styles` (
					  `mobileStyleId` int(11) NOT NULL AUTO_INCREMENT,
					  `menuId` int(11) NOT NULL,
					  `bgBarStartColor` varchar(11) DEFAULT NULL,
					  `bgBarGradient` tinyint(1) DEFAULT NULL,
					  `bgBarEndColor` varchar(11) DEFAULT NULL,
					  `bgBarGradientPath` varchar(11) DEFAULT NULL,
					  `bgBarTransparency` decimal(2,1) DEFAULT NULL,
					  `fontBarFamily` varchar(45) DEFAULT NULL,
					  `fontBarColor` varchar(11) DEFAULT NULL,
					  `fontBarHoverColor` varchar(45) DEFAULT NULL,
					  `fontBarSize` varchar(11) DEFAULT NULL,
					  `fontBarSizing` varchar(11) DEFAULT NULL,
					  `fontBarWeight` varchar(11) DEFAULT NULL,
					  `bgMenuStartColor` varchar(11) DEFAULT NULL,
					  `bgMenuGradient` tinyint(1) DEFAULT NULL,
					  `bgMenuEndColor` varchar(11) DEFAULT NULL,
					  `bgMenuGradientPath` varchar(11) DEFAULT NULL,
					  `bgMenuTransparency` decimal(2,1) DEFAULT NULL,
					  `bgHoverStartColor` varchar(11) DEFAULT NULL,
					  `bgHoverGradient` tinyint(1) DEFAULT NULL,
					  `bgHoverEndColor` varchar(11) DEFAULT NULL,
					  `bgHoverGradientPath` varchar(11) DEFAULT NULL,
					  `bgHoverTransparency` decimal(2,1) DEFAULT NULL,
					  `fontMobileFamily` varchar(45) DEFAULT NULL,
					  `fontMobileColor` varchar(11) DEFAULT NULL,
					  `fontMobileHoverColor` varchar(45) DEFAULT NULL,
					  `fontMobileSize` varchar(11) DEFAULT NULL,
					  `fontMobileSizing` varchar(11) DEFAULT NULL,
					  `fontMobileWeight` varchar(11) DEFAULT NULL,
					  `fontTabletFamily` varchar(45) DEFAULT NULL,
					  `fontTabletColor` varchar(11) DEFAULT NULL,
					  `fontTabletHoverColor` varchar(45) DEFAULT NULL,
					  `fontTabletSize` varchar(11) DEFAULT NULL,
					  `fontTabletSizing` varchar(11) DEFAULT NULL,
					  `fontTabletWeight` varchar(11) DEFAULT NULL,
					  `paddingLeft` varchar(60) DEFAULT NULL,
					  `paddingRight` varchar(60) DEFAULT NULL,
					  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `lastModified` datetime DEFAULT NULL,
					  `deleted` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`mobileStyleId`),
					  KEY `hmenu_mobile_styles_menuId_".$contraint_uid."_FK_idx` (`menuId`),
					  CONSTRAINT `hmenu_mobile_styles_menuId_".$contraint_uid."_FK` FOREIGN KEY (`menuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_menu` (`menuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;	
				";			
				dbDelta($sql_create);
				$sql_drop = "
					DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mobile_styles`;
				";
				$wpdb->query($sql_drop);
				$sql_create = "
					CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mobile_styles`
					BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mobile_styles`
					FOR EACH ROW SET NEW.lastModified = NOW();	
				";
				dbDelta($sql_create);
				
				//INSERT DATA
				$result = $wpdb->get_results("
					SELECT
						*
					FROM
						`". $wpdb->base_prefix ."hmenu_menu` `hm`
						INNER JOIN `". $wpdb->base_prefix ."hmenu_main_styles` `hms` ON(`hms`.`menuId` = `hm`.`menuId`)
					WHERE
						`hm`.`deleted` = '0'
					ORDER BY
						`hm`.`created` DESC;
				");
				
				if($result){
					foreach($result as $menu){
						$this->insert_mobile_defaults(intval($menu->menuId), $menu);
					}
				}
				
			}
			
			// < 1.4.0
			if(version_compare($this->plugin_old_version .'', '1.4.0', "<")){
				//update main styles table
				$wpdb->query("ALTER TABLE `". $wpdb->base_prefix ."hmenu_main_styles`
					ADD COLUMN `logoAlt` VARCHAR(255) DEFAULT NULL;
				");				
			}
			
			// < 1.4.2
			if(version_compare($this->plugin_old_version .'', '1.4.2', "<")){
				//update main styles table
				$wpdb->query("ALTER TABLE `". $wpdb->base_prefix ."hmenu_main_styles` 
					ADD COLUMN `customCss` BLOB DEFAULT NULL
				");	
				//update search styles table
				$wpdb->query("ALTER TABLE `". $wpdb->base_prefix ."hmenu_search` 
					ADD COLUMN `placeholder` varchar(255) DEFAULT NULL
				");				
			}
			
			//generate
			if($GLOBALS['wp_rewrite']){
				$this->process_generate();
			}else{
				add_action('wp_loaded', array(&$this, 'process_generate'));
			}
			
			//mark the upgrade as successful
			$this->mark_update_complete();
			
		}
		
		#INERT MOBILE DEFAULTS
		public function insert_mobile_defaults($menu_id, $obj){
			
			global $wpdb;
			
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
						'".$obj->bgMenuStartColor."',
						'".$obj->bgMenuGradient."',
						'".$obj->bgMenuEndColor."',
						'".$obj->bgMenuGradientPath."',
						'".$obj->bgMenuTransparency."',																	
						'".$obj->fontFamily."',
						'".$obj->fontColor."',
						'".$obj->fontHoverColor."',
						'".$obj->fontSize."',
						'".$obj->fontSizing."',
						'".$obj->fontWeight."',												
						'".$obj->bgMenuStartColor."',
						'".$obj->bgMenuGradient."',
						'".$obj->bgMenuEndColor."',
						'".$obj->bgMenuGradientPath."',
						'".$obj->bgMenuTransparency."',												
						'".$obj->bgHoverStartColor."',
						'".$obj->bgHoverGradient."',
						'".$obj->bgHoverEndColor."',
						'".$obj->bgHoverGradientPath."',
						'".$obj->bgHoverTransparency."',											
						'".$obj->fontFamily."',
						'".$obj->fontColor."',
						'".$obj->fontHoverColor."',
						'".$obj->fontSize."',
						'".$obj->fontSizing."',
						'".$obj->fontWeight."',												
						'".$obj->fontFamily."',
						'".$obj->fontColor."',
						'".$obj->fontHoverColor."',
						'".$obj->fontSize."',
						'".$obj->fontSizing."',
						'".$obj->fontWeight."',												
						'".$obj->paddingLeft."',
						'".$obj->paddingRight."'						
					)
				"
			);
			
		}
		
		#RE-GENERATE THE MENU FILES
		public function process_generate(){
			
			#GLOBALS
			global $wpdb;
			
			$result = $wpdb->get_results("SELECT * FROM ". $wpdb->base_prefix ."hmenu_menu WHERE deleted = '0' ORDER BY created DESC");
	
			if($result){
				$backend = new hmenu_backend($this->plugin_dir);
				$get = new hmenu_class_get($this->plugin_dir);
				$generate = new hmenu_class_generate($this->plugin_dir);
				foreach($result as $menu){
					$menu_object = $get->get_main_menu_object(intval($menu->menuId),false);
					$backend->get_fonts('icons',false);
					$generate->generate_files($menu_object,false);					
				}
			}
			
			return true;
		}
		
		#MARK UPDATE COMPLETE
		private function mark_update_complete(){
			//access globals
			global $wpdb;
			//once updates are complete, mark the plugin version in the DB
			$wpdb->query("UPDATE `". $wpdb->base_prefix ."hplugin_root` SET `plugin_version` = '". $this->plugin_version ."' WHERE `plugin_name` = '". $this->plugin_name ."';");
		}
		
	}