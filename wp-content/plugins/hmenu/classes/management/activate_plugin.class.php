<?php
	
	#ACTIVATE PLUGIN
	class hmenu_activate{
		
		#CLASS VARS
		private $plugin_name;
		private $plugin_version;
		private $plugin_old_version;
		private $plugin_dir;
		
		#CONSTRUCT
		public function __construct($plugin_name,$plugin_version,$plugin_dir){
			//define class vars
			$this->plugin_name = $plugin_name;
			$this->plugin_version = $plugin_version;
			$this->plugin_dir = $plugin_dir;
			//update check
			$this->update_check();
			
		}
		
		#CHECK FOR UPGRADE
		private function update_check(){
			global $wpdb;
			$plugin_lookup = $wpdb->get_results("SELECT * FROM `". $wpdb->base_prefix ."hplugin_root` WHERE `plugin_name` = '". $this->plugin_name ."';");
			if($plugin_lookup){
				$this->plugin_old_version = $plugin_lookup[0]->plugin_version;
				if(version_compare($this->plugin_old_version,$this->plugin_version,'<')){
					$update = new hmenu_update_plugin($this->plugin_name,$this->plugin_version,$this->plugin_old_version,$this->plugin_dir);
					$update->update_plugin();
				}
			}
		}
		
		#ACTIVATE
		private function activate(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			//create the hplugin_root table if it doesn't exist
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hplugin_root` (
					`hplugin_id` int(11) NOT NULL AUTO_INCREMENT,
					`plugin_name` varchar(45) NOT NULL,
					`plugin_version` varchar(10) NOT NULL,
					`plugin_uuid` varchar(36) NOT NULL,
					`date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					`last_modified` datetime DEFAULT NULL,
					`active` tinyint(1) NOT NULL DEFAULT '1',
					`deleted` tinyint(1) NOT NULL DEFAULT '0',
					PRIMARY KEY (`hplugin_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;
				";
				dbDelta($sql_create);
				$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hplugin_root`;
				";
				$wpdb->query($sql_drop);
				$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hplugin_root`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hplugin_root`
				FOR EACH ROW SET NEW.last_modified = NOW();
			";
			dbDelta($sql_create);
			//check if plugin exists in hplugin_root table
			$plugin_lookup = $wpdb->get_results("SELECT * FROM `". $wpdb->base_prefix ."hplugin_root` WHERE `plugin_name` = '". $this->plugin_name ."';");
			if(!$plugin_lookup){ //add if not exists
				$wpdb->query("INSERT INTO `". $wpdb->base_prefix ."hplugin_root` (`plugin_name`,`plugin_version`,`plugin_uuid`) VALUES('". $this->plugin_name ."','". $this->plugin_version ."','". $hmenu_helper->genGUID() ."');");
			}else{ //ensure that deleted = 0
				$wpdb->query("UPDATE `". $wpdb->base_prefix ."hplugin_root` SET `deleted` = 0, `active` = 1 WHERE `plugin_name` = '". $this->plugin_name ."';");
			}
		}
		
		#ACTIVATE MENU TABLE
		private function activate_menu(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_menu` (
				  `menuId` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(255) NOT NULL,
				  `autoLink` tinyint(1) NOT NULL DEFAULT '0',
				  `leftItems` varchar(30) DEFAULT NULL,				  
				  `centerItems` varchar(30) DEFAULT NULL,				  
				  `rightItems` varchar(30) DEFAULT NULL,
				  `customLink` varchar(255) DEFAULT NULL,
				  `overwrite` varchar(45) NOT NULL DEFAULT 'primary',
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`menuId`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";		
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_menu`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_menu`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_menu`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}
		
		#ACTIVATE PRESETS
		private function activate_presets(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_presets` (
				  `presetId` int(11) NOT NULL AUTO_INCREMENT,
				  `presetName` varchar(45) DEFAULT NULL,
				  `bgColor` varchar(45) DEFAULT NULL,
				  `hoverColor` varchar(45) DEFAULT NULL,
				  `textColor` varchar(45) DEFAULT NULL,
				  `iconColor` varchar(45) DEFAULT NULL,
				  `slug` varchar(50) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`presetId`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_presets`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_presets`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_presets`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}	
		
		#ACTIVATE MENU MAIN STYLES TABLE
		private function activate_main_styles(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql	
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_main_styles` (
				  `mainStyleId` int(11) NOT NULL AUTO_INCREMENT,
				  `menuId` int(11) NOT NULL,				  
				  `logo` tinyint(1) DEFAULT NULL,
				  `logoUrl` varchar(255) DEFAULT NULL,
				  `logoLink` varchar(255) DEFAULT NULL,
				  `logoAlt` varchar(255) DEFAULT NULL,
				  `logoLinkTarget` varchar(20) NOT NULL DEFAULT '_self',
				  `logoHeight` varchar(10) DEFAULT NULL,
				  `mobileLogo` tinyint(1) DEFAULT NULL,
				  `mobileLogoUrl` varchar(255) DEFAULT NULL,
				  `mobileLogoHeight` varchar(10) DEFAULT NULL,
				  `search` tinyint(1) DEFAULT NULL,
				  `menu` tinyint(1) DEFAULT NULL,
				  `social` tinyint(1) DEFAULT NULL,
				  `cart` tinyint(1) DEFAULT NULL,				  
				  `menuBarDimentions` varchar(11) DEFAULT NULL,				  
				  `menuBarWidth` varchar(11) DEFAULT NULL,	
				  `menuBarHeight` varchar(11) DEFAULT NULL,
				  `navBarDimentions` varchar(11) DEFAULT NULL,				  
				  `navBarWidth` varchar(11) DEFAULT NULL,
				  `border` tinyint(1) DEFAULT NULL,
				  `borderColor` varchar(11) DEFAULT NULL,
				  `borderTransparency` decimal(2,1) DEFAULT NULL,
				  `borderType` varchar(40) DEFAULT NULL,
				  `borderRadius` varchar(100) DEFAULT NULL,
				  `shadow` tinyint(1) DEFAULT NULL,
				  `shadowRadius` varchar(20) DEFAULT NULL,
				  `shadowColor` varchar(11) DEFAULT NULL,
				  `shadowTransparency` decimal(2,1) DEFAULT NULL,
				  `bgMenuStartColor` varchar(11) DEFAULT NULL,
				  `bgMenuGradient` tinyint(1) DEFAULT NULL,
				  `bgMenuEndColor` varchar(11) DEFAULT NULL,
				  `bgMenuGradientPath` varchar(11) DEFAULT NULL,
				  `bgMenuTransparency` decimal(2,1) DEFAULT NULL,
				  `bgHoverStartColor` varchar(11) DEFAULT NULL,
				  `bgHoverType` varchar(11) DEFAULT NULL,
				  `bgHoverGradient` tinyint(1) DEFAULT NULL,
				  `bgHoverEndColor` varchar(11) DEFAULT NULL,
				  `bgHoverGradientPath` varchar(11) DEFAULT NULL,
				  `bgHoverTransparency` decimal(2,1) DEFAULT NULL,
				  `paddingLeft` varchar(11) DEFAULT NULL,
				  `paddingRight` varchar(11) DEFAULT NULL,
				  `orientation` varchar(11) DEFAULT NULL,
				  `verticalWidth` varchar(11) DEFAULT NULL,
				  `animation` varchar(45) DEFAULT NULL,
				  `animationDuration` int(11) DEFAULT NULL,
				  `animationTrigger` varchar(11) DEFAULT NULL,
				  `animationTimeout` varchar(11) DEFAULT NULL,
				  `sticky` tinyint(1) DEFAULT NULL,
				  `stickyLogoActive` tinyint(1) DEFAULT NULL,
				  `stickyUrl` varchar(150) DEFAULT NULL,
				  `stickyActivate` varchar(11) DEFAULT NULL,
				  `stickyHeight` varchar(11) DEFAULT NULL,				  
				  `stickyFontColor` varchar(11) DEFAULT NULL,
				  `stickyFontHoverColor` varchar(45) DEFAULT NULL,
				  `stickyFontSize` varchar(11) DEFAULT NULL,
				  `stickyFontSizing` varchar(11) DEFAULT NULL,
				  `stickyFontWeight` varchar(11) DEFAULT NULL,
				  `stickyFontHoverDecoration` varchar(25) DEFAULT NULL,				  
				  `bgStickyStart` varchar(11) DEFAULT NULL,
				  `bgStickyEnd` varchar(11) DEFAULT NULL,
				  `stickyTransparency` decimal(2,1) DEFAULT NULL,
				  `devider` tinyint(1) DEFAULT NULL,
				  `deviderTransparency` decimal(2,1) DEFAULT NULL,
				  `deviderColor` varchar(11) DEFAULT NULL,
				  `deviderSizing` varchar(11) DEFAULT NULL,				  
				  `groupDevider` tinyint(1) DEFAULT NULL,	
				  `groupTransparency` decimal(2,1) DEFAULT NULL,
				  `groupColor` varchar(11) DEFAULT NULL,
				  `groupSizing` varchar(11) DEFAULT NULL,
				  `responsiveLabel` varchar(100) DEFAULT NULL,
				  `icons` tinyint(1) DEFAULT NULL,
				  `iconsColor` varchar(11) DEFAULT NULL,
				  `arrows` tinyint(1) DEFAULT NULL,
				  `arrowTransparency` decimal(2,1) DEFAULT NULL,
				  `arrowColor` varchar(11) DEFAULT NULL,
				  `fontFamily` varchar(45) DEFAULT NULL,
				  `fontColor` varchar(11) DEFAULT NULL,
				  `fontHoverColor` varchar(45) DEFAULT NULL,
				  `fontSize` varchar(11) DEFAULT NULL,
				  `fontSizing` varchar(11) DEFAULT NULL,
				  `fontWeight` varchar(11) DEFAULT NULL,
				  `fontDecoration` varchar(25) DEFAULT NULL,
				  `fontHoverDecoration` varchar(25) DEFAULT NULL,
				  `zindex` varchar(25) DEFAULT NULL,
				  `preset` tinyint(1) DEFAULT NULL,
				  `presetSlug` varchar(50) DEFAULT NULL,
				  `iconProductSize` varchar(11) DEFAULT NULL,
				  `iconProductColor` varchar(20) DEFAULT NULL,
				  `iconProductHoverColor` varchar(45) DEFAULT NULL,
				  `siteResponsive` tinyint(1) DEFAULT NULL,
				  `siteResponsiveOne` varchar(11) DEFAULT NULL,
				  `siteResponsiveTwo` varchar(11) DEFAULT NULL,
				  `siteResponsiveThree` varchar(11) DEFAULT NULL,				  
				  `logoPaddingLeft` varchar(11) NOT NULL DEFAULT '0',
				  `mobileLogoPaddingLeft` varchar(11) NOT NULL DEFAULT '10',
				  `stickyLogoPaddingLeft` varchar(11) NOT NULL DEFAULT '10',				  
				  `bgMainImage` tinyint(1) DEFAULT NULL,
				  `bgMainImageUrl` varchar(255) DEFAULT NULL,
				  `bgMainImagePosition` varchar(50) DEFAULT NULL,
				  `bgMainImageRepeat` varchar(50) DEFAULT NULL,	
				  `customCss` blob DEFAULT NULL,			  			  
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`mainStyleId`),
				  KEY `hmenu_main_styles_menuId_".$contraint_uid."_FK_idx` (`menuId`),
				  CONSTRAINT `hmenu_main_styles_menuId_".$contraint_uid."_FK` FOREIGN KEY (`menuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_menu` (`menuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;								
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_main_styles`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_main_styles`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_main_styles`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}
		
		#ACTIVATE MENU DROPDOWN STYLES TABLE
		private function activate_dropdown_styles(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql	
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_dropdown_styles` (
				  `dropStyleId` int(11) NOT NULL AUTO_INCREMENT,
				  `menuId` int(11) NOT NULL,
				  `widthType` varchar(20) DEFAULT NULL,
				  `width` varchar(11) DEFAULT NULL,
				  `padding` varchar(60) DEFAULT NULL,				  
				  `border` tinyint(1) DEFAULT NULL,
				  `borderColor` varchar(11) DEFAULT NULL,
				  `borderTransparency` decimal(2,1) DEFAULT NULL,
				  `borderType` varchar(40) DEFAULT NULL,
				  `borderRadius` varchar(20) DEFAULT NULL,				  
				  `shadow` tinyint(1) DEFAULT NULL,
				  `shadowRadius` varchar(20) DEFAULT NULL,
				  `shadowColor` varchar(11) DEFAULT NULL,	
				  `shadowTransparency` decimal(2,1) DEFAULT NULL,			  
				  `bgDropStartColor` varchar(11) DEFAULT NULL,
				  `bgDropGradient` tinyint(1) DEFAULT NULL,
				  `bgDropEndColor` varchar(11) DEFAULT NULL,
				  `bgDropGradientPath` varchar(11) DEFAULT NULL,
				  `bgDropTransparency` decimal(2,1) DEFAULT NULL,
				  `bgHoverStartColor` varchar(11) DEFAULT NULL,
				  `bgHoverGradient` tinyint(1) DEFAULT NULL,
				  `bgHoverEndColor` varchar(11) DEFAULT NULL,
				  `bgHoverGradientPath` varchar(11) DEFAULT NULL,
				  `bgHoverTransparency` decimal(2,1) DEFAULT NULL,				  
				  `arrows` tinyint(1) DEFAULT NULL,
				  `arrowTransparency` decimal(2,1) DEFAULT NULL,
				  `arrowColor` varchar(11) DEFAULT NULL,				  
				  `devider` tinyint(1) DEFAULT NULL,
				  `deviderTransparency` decimal(2,1) DEFAULT NULL,
				  `deviderColor` varchar(11) DEFAULT NULL,				  
				  `fontFamily` varchar(45) DEFAULT NULL,
				  `fontColor` varchar(11) DEFAULT NULL,
				  `fontHoverColor` varchar(45) DEFAULT NULL,
				  `fontSize` varchar(11) DEFAULT NULL,
				  `fontSizing` varchar(11) DEFAULT NULL,
				  `fontWeight` varchar(11) DEFAULT NULL,
				  `fontDecoration` varchar(25) DEFAULT NULL,
				  `fontHoverDecoration` varchar(25) DEFAULT NULL,				  
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`dropStyleId`),
				  KEY `hmenu_dropdown_styles_menuId_".$contraint_uid."_FK_idx` (`menuId`),
				  CONSTRAINT `hmenu_dropdown_styles_menuId_".$contraint_uid."_FK` FOREIGN KEY (`menuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_menu` (`menuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;	
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_dropdown_styles`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_dropdown_styles`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_dropdown_styles`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}
		
		#ACTIVATE MENU MEGA STYLES TABLE
		private function activate_mega_styles(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_styles` (
				  `megaStyleId` int(11) NOT NULL AUTO_INCREMENT,
				  `menuId` int(11) NOT NULL,
				  `widthType` varchar(20) DEFAULT NULL,
				  `width` varchar(11) DEFAULT NULL,
				  `padding` varchar(60) DEFAULT NULL,				  
				  `border` tinyint(1) DEFAULT NULL,
				  `borderColor` varchar(11) DEFAULT NULL,
				  `borderTransparency` decimal(2,1) DEFAULT NULL,
				  `borderType` varchar(40) DEFAULT NULL,
				  `borderRadius` varchar(20) DEFAULT NULL,				  
				  `shadow` tinyint(1) DEFAULT NULL,
				  `shadowRadius` varchar(20) DEFAULT NULL,
				  `shadowColor` varchar(11) DEFAULT NULL,	
				  `shadowTransparency` decimal(2,1) DEFAULT NULL,			  
				  `bgDropStartColor` varchar(11) DEFAULT NULL,
				  `bgDropGradient` tinyint(1) DEFAULT NULL,
				  `bgDropEndColor` varchar(11) DEFAULT NULL,
				  `bgDropGradientPath` varchar(11) DEFAULT NULL,
				  `bgDropTransparency` decimal(2,1) DEFAULT NULL,
				  `bgHoverStartColor` varchar(11) DEFAULT NULL,
				  `bgHoverGradient` tinyint(1) DEFAULT NULL,
				  `bgHoverEndColor` varchar(11) DEFAULT NULL,
				  `bgHoverGradientPath` varchar(11) DEFAULT NULL,
				  `bgHoverTransparency` decimal(2,1) DEFAULT NULL,				  
				  `arrows` tinyint(1) DEFAULT NULL,
				  `arrowTransparency` decimal(2,1) DEFAULT NULL,
				  `arrowColor` varchar(11) DEFAULT NULL,				  
				  `devider` tinyint(1) DEFAULT NULL,
				  `deviderTransparency` decimal(2,1) DEFAULT NULL,
				  `deviderColor` varchar(11) DEFAULT NULL,	
				  `fontHoverColor` varchar(45) DEFAULT NULL,
				  `fontHoverDecoration` varchar(25) DEFAULT NULL,		
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`megaStyleId`),
				  KEY `hmenu_mega_styles_menuId_".$contraint_uid."_FK_idx` (`menuId`),
				  CONSTRAINT `hmenu_mega_styles_menuId_".$contraint_uid."_FK` FOREIGN KEY (`menuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_menu` (`menuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;			
			";		
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_styles`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_styles`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_styles`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";	
			dbDelta($sql_create);
		}
		
		#ACTIVATE MENU MOBILE STYLES TABLE
		private function activate_mobile_styles(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
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
		}
		
		#ACTIVATE MENU MEGA FONT STYLES TABLE
		private function activate_mega_font_styles(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_font_styles` (
				  `megaFontId` int(11) NOT NULL AUTO_INCREMENT,
				  `megaStyleId` int(11) NOT NULL,
				  `type` varchar(20) DEFAULT NULL,
				  `fontFamily` varchar(45) DEFAULT NULL,
				  `fontColor` varchar(11) DEFAULT NULL,
				  `fontSize` varchar(11) DEFAULT NULL,
				  `fontSizing` varchar(11) DEFAULT NULL,
				  `fontWeight` varchar(11) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`megaFontId`),
				  KEY `hmenu_mega_font_styles_megaStyleId_".$contraint_uid."_FK_idx` (`megaStyleId`),
				  CONSTRAINT `hmenu_mega_font_styles_megaStyleId_".$contraint_uid."_FK` FOREIGN KEY (`megaStyleId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_styles` (`megaStyleId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;				
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_font_styles`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_font_styles`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_font_styles`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";	
			dbDelta($sql_create);
		}
		
		#ACTIVATE MENU CART TABLE
		private function activate_cart(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql	
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_cart` (
				  `cartId` int(11) NOT NULL AUTO_INCREMENT,
				  `menuId` int(11) NOT NULL,
				  `type` varchar(10) DEFAULT NULL,
				  `icon` varchar(10) DEFAULT NULL,
				  `iconColor` varchar(11) DEFAULT NULL,
				  `iconHoverColor` varchar(11) DEFAULT NULL,
				  `link` varchar(120) DEFAULT NULL,
				  `target` varchar(10) DEFAULT NULL,
				  `placement` varchar(10) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`cartId`),
				  KEY `hmenu_cart_menuId_".$contraint_uid."_FK_idx` (`menuId`),
				  CONSTRAINT `hmenu_cart_menuId_".$contraint_uid."_FK` FOREIGN KEY (`menuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_menu` (`menuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";	
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_cart`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_cart`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_cart`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";			
			dbDelta($sql_create);
		}
		
		#ACTIVATE MENU SEARCH TABLE
		private function activate_search(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql	
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_search` (
				  `searchId` int(11) NOT NULL AUTO_INCREMENT,
				  `menuId` int(11) NOT NULL,
				  `type` varchar(10) DEFAULT NULL,
				  `icon` varchar(11) DEFAULT NULL,
				  `label` varchar(45) DEFAULT NULL,
				  `iconColor` varchar(11) DEFAULT NULL,
				  `iconHoverColor` varchar(11) DEFAULT NULL,
				  `iconSize` varchar(11) DEFAULT NULL,
				  `animation` varchar(45) DEFAULT NULL,
				  `placement` varchar(10) DEFAULT NULL,
				  `padding` varchar(60) DEFAULT NULL,
				  `width` varchar(11) DEFAULT NULL,
				  `height` varchar(11) DEFAULT NULL,					
				  `fontFamily` varchar(45) DEFAULT NULL,
				  `fontColor` varchar(11) DEFAULT NULL,
				  `fontSize` varchar(11) DEFAULT NULL,
				  `fontSizing` varchar(11) DEFAULT NULL,
				  `fontWeight` varchar(11) DEFAULT NULL,						
				  `border` tinyint(1) DEFAULT NULL,
				  `borderColor` varchar(11) DEFAULT NULL,
				  `borderTransparency` decimal(2,1) DEFAULT NULL,
				  `borderRadius` varchar(20) DEFAULT NULL,	
				  `backgroundColor` varchar(11) DEFAULT NULL,	
				  `placeholder` varchar(255) DEFAULT NULL,				
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`searchId`),
				  KEY `hmenu_search_menuId_".$contraint_uid."_FK_idx` (`menuId`),
				  CONSTRAINT `hmenu_search_menuId_".$contraint_uid."_FK` FOREIGN KEY (`menuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_menu` (`menuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_search`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_search`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_search`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";	
			dbDelta($sql_create);
		}
		
		#ACTIVATE MENU SOCIAL TABLE
		private function activate_social(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql	
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_social` (
				  `socialId` int(11) NOT NULL AUTO_INCREMENT,
				  `menuId` int(11) NOT NULL,
				  `name` varchar(45) DEFAULT NULL,
				  `icon` varchar(45) DEFAULT NULL,
				  `iconContent` varchar(100) DEFAULT NULL,
				  `iconSize` varchar(11) DEFAULT NULL,
				  `iconColor` varchar(20) DEFAULT NULL,
				  `iconHoverColor` varchar(45) DEFAULT NULL,
				  `link` varchar(45) DEFAULT NULL,
				  `target` varchar(45) DEFAULT NULL,
				  `order` int(11) NOT NULL DEFAULT '0',
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`socialId`),
				  KEY `hmenu_social_menuId_".$contraint_uid."_FK_idx` (`menuId`),
				  CONSTRAINT `hmenu_social_menuId_".$contraint_uid."_FK` FOREIGN KEY (`menuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_menu` (`menuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_social`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_social`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_social`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";	
			dbDelta($sql_create);
		}
		
		#ACTIVATE MENU NAV ITEMS TABLE
		private function activate_nav_items(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_nav_items` (
				  `navItemId` int(11) NOT NULL AUTO_INCREMENT,
				  `menuId` int(11) NOT NULL,
				  `parentNavId` int(11) NOT NULL DEFAULT '0',
				  `postId` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(150) NOT NULL DEFAULT 'item',
				  `title` varchar(150) NOT NULL DEFAULT 'item',
				  `active` tinyint(1) DEFAULT NULL,
				  `activeMobile` tinyint(1) DEFAULT NULL,
				  `icon` tinyint(1) DEFAULT NULL,
				  `iconContent` varchar(100) DEFAULT NULL,
				  `iconSize` varchar(11) DEFAULT NULL,
				  `iconColor` varchar(20) DEFAULT NULL,
				  `link` varchar(255) DEFAULT NULL,
				  `order` int(11) NOT NULL DEFAULT '0',
				  `target` varchar(10) DEFAULT NULL,
				  `type` varchar(20) DEFAULT NULL,
				  `level` varchar(10) DEFAULT NULL,
				  `method` tinyint(1) NOT NULL DEFAULT '0',
				  `methodReference` varchar(255) NOT NULL DEFAULT 'functionName',
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`navItemId`),
				  KEY `hmenu_nav_items_menuId_".$contraint_uid."_FK_idx` (`menuId`),
				  CONSTRAINT `hmenu_nav_items_menuId_".$contraint_uid."_FK` FOREIGN KEY (`menuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_menu` (`menuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_nav_items`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_nav_items`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_nav_items`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";	
			dbDelta($sql_create);
		}		
		
		#ACTIVATE MENU MEGA TABLE
		private function activate_mega_menu(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_menu` (
				  `megaMenuId` int(11) NOT NULL AUTO_INCREMENT,
				  `navItemId` int(11) NOT NULL,
				  `name` varchar(45) NOT NULL DEFAULT 'mega',
				  `layout` varchar(20) DEFAULT NULL,
				  `background` tinyint(1) DEFAULT NULL,
				  `backgroundUrl` varchar(255) DEFAULT NULL,
				  `backgroundPosition` varchar(50) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`megaMenuId`),
				  KEY `hmenu_mega_menu_navItemId_".$contraint_uid."_FK_idx` (`navItemId`),
				  CONSTRAINT `hmenu_mega_menu_navItemId_".$contraint_uid."_FK` FOREIGN KEY (`navItemId`) REFERENCES `". $wpdb->base_prefix ."hmenu_nav_items` (`navItemId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_menu`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_menu`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_menu`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";	
			dbDelta($sql_create);
		}		
		
		#ACTIVATE MENU MEGA BLOG TABLE
		private function activate_mega_blog(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_blog` (
				  `megaBlogId` int(11) NOT NULL AUTO_INCREMENT,
				  `megaMenuId` int(11) NOT NULL,
				  `termId` int(11) DEFAULT NULL,
				  `numberPosts` int(11) DEFAULT NULL,
				  `heading` varchar(255) DEFAULT NULL,
				  `headingUnderline` tinyint(1) DEFAULT NULL,
				  `headingAllow` tinyint(1) DEFAULT NULL,
				  `description` tinyint(1) DEFAULT NULL,
				  `descriptionCount` int(11) DEFAULT '150',
				  `featuredImage` tinyint(1) DEFAULT NULL,
				  `featuredSize` varchar(10) DEFAULT NULL,
				  `placement` varchar(10) DEFAULT '0',
				  `type` varchar(10) DEFAULT 'post',
				  `target` varchar(10) DEFAULT '_self',
				  `content` blob DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`megaBlogId`),
				  KEY `hmenu_mega_blog_megaMenuId_".$contraint_uid."_FK_idx` (`megaMenuId`),
				  CONSTRAINT `hmenu_mega_blog_megaMenuId_".$contraint_uid."_FK` FOREIGN KEY (`megaMenuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_menu` (`megaMenuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;				
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_blog`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_blog`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_blog`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}		
		
		#ACTIVATE MENU MEGA CONTACT TABLE
		private function activate_mega_contact(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_contact` (
				  `contactId` int(11) NOT NULL AUTO_INCREMENT,
				  `megaMenuId` int(11) NOT NULL,
				  `heading` varchar(45) DEFAULT NULL,
				  `headingUnderline` tinyint(1) DEFAULT NULL,
				  `html` tinyint(1) DEFAULT NULL,
				  `formHtml` blob DEFAULT NULL,
				  `shortcode` tinyint(1) DEFAULT NULL,
				  `formShortcode` varchar(250) DEFAULT NULL,
				  `placement` varchar(10) DEFAULT NULL,
				  `type` varchar(10) DEFAULT 'contact',
				  `sendToEmail` varchar(120) DEFAULT NULL,
				  `sendUserEmail` varchar(120) DEFAULT NULL,
				  `sendBccEmail` varchar(120) DEFAULT NULL,
				  `sendCcEmail` varchar(120) DEFAULT NULL,
				  `theme` varchar(20) DEFAULT NULL,
				  `labels` tinyint(1) NOT NULL DEFAULT '0',
				  `image` varchar(120) DEFAULT NULL,
				  `footerContent` varchar(120) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`contactId`),
				  KEY `hmenu_mega_contact_megaMenuId_".$contraint_uid."_FK_idx` (`megaMenuId`),
				  CONSTRAINT `hmenu_mega_contact_megaMenuId_".$contraint_uid."_FK` FOREIGN KEY (`megaMenuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_menu` (`megaMenuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_contact`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_contact`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_contact`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}		
		
		#ACTIVATE MENU MEGA CONTACT FIELDS TABLE
		private function activate_mega_contact_fields(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_contact_fields` (
				  `fieldId` int(11) NOT NULL AUTO_INCREMENT,
				  `contactId` int(11) NOT NULL,
				  `type` varchar(10) DEFAULT NULL,
				  `required` tinyint(1) NOT NULL DEFAULT '0',
				  `fontFamily` varchar(45) DEFAULT NULL,
				  `fontColor` varchar(11) DEFAULT NULL,
				  `fontHoverColor` varchar(11) DEFAULT NULL,
				  `fontSize` varchar(11) DEFAULT '12',
				  `fontWeight` varchar(11) DEFAULT NULL,
				  `placeholderText` varchar(45) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`fieldId`),
				  KEY `hmenu_mega_contact_fields_contactId_".$contraint_uid."_FK_idx` (`contactId`),
				  CONSTRAINT `hmenu_mega_contact_fields_contactId_".$contraint_uid."_FK` FOREIGN KEY (`contactId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_contact` (`contactId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_contact_fields`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_contact_fields`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_contact_fields`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}	
		
		#ACTIVATE MENU MEGA CONTENT TABLE
		private function activate_mega_content(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql	
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_content` (
				  `contentId` int(11) NOT NULL AUTO_INCREMENT,
				  `megaMenuId` int(11) NOT NULL,
				  `heading` varchar(250) DEFAULT NULL,
				  `headingUnderline` tinyint(1) DEFAULT NULL,
				  `text` blob DEFAULT NULL,
				  `textCount` int(11) DEFAULT '150',
				  `textAlignment` varchar(11) DEFAULT NULL,
				  `paddingTop` varchar(11) DEFAULT NULL,
				  `paddingBottom` varchar(11) DEFAULT NULL,
				  `placement` varchar(10) DEFAULT '0',
				  `type` varchar(10) DEFAULT 'content',
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`contentId`),
				  KEY `hmenu_mega_content_megaMenuId_".$contraint_uid."_FK_idx` (`megaMenuId`),
				  CONSTRAINT `hmenu_mega_content_megaMenuId_".$contraint_uid."_FK` FOREIGN KEY (`megaMenuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_menu` (`megaMenuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;				
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_content`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_content`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_content`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}		
		
		#ACTIVATE MENU MEGA IMAGE
		private function activate_mega_image(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_image` (
				  `imageId` int(11) NOT NULL AUTO_INCREMENT,
				  `megaMenuId` int(11) NOT NULL,
				  `heading` varchar(255) DEFAULT NULL,
				  `headingUnderline` tinyint(1) DEFAULT NULL,
				  `placement` varchar(10) DEFAULT NULL,
				  `text` varchar(255) DEFAULT NULL,
				  `url` varchar(255) DEFAULT NULL,
				  `image` varchar(255) DEFAULT NULL,
				  `imageHeading` varchar(255) DEFAULT NULL,
				  `displayType` varchar(10) DEFAULT NULL,
				  `type` varchar(10) DEFAULT 'images',
				  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`imageId`),
				  KEY `hmenu_mega_image_megaMenuId_".$contraint_uid."_FK_idx` (`megaMenuId`),
				  CONSTRAINT `hmenu_mega_image_megaMenuId_".$contraint_uid."_FK` FOREIGN KEY (`megaMenuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_menu` (`megaMenuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_image`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_image`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_image`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}		
		
		#ACTIVATE MENU MEGA LIST TABLE
		private function activate_mega_list(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_list` (
				  `listId` int(11) NOT NULL AUTO_INCREMENT,
				  `megaMenuId` int(11) NOT NULL,
				  `heading` varchar(255) DEFAULT NULL,
				  `headingUnderline` tinyint(1) DEFAULT NULL,				  			  
				  `text` blob DEFAULT NULL,
				  `textCount` int(11) DEFAULT '150',
				  `textAlignment` varchar(11) DEFAULT NULL,
				  `paddingTop` varchar(11) DEFAULT NULL,
				  `paddingBottom` varchar(11) DEFAULT NULL,	
				  `type` varchar(10) DEFAULT 'list',			  
				  `placement` varchar(10) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`listId`),
				  KEY `hmenu_mega_list_megaMenuId_".$contraint_uid."_FK_idx` (`megaMenuId`),
				  CONSTRAINT `hmenu_mega_list_megaMenuId_".$contraint_uid."_FK` FOREIGN KEY (`megaMenuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_menu` (`megaMenuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;			
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_list`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_list`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_list`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}		
		
		#ACTIVATE MENU MEGA LIST ITEMS TABLE
		private function activate_mega_list_items(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_list_items` (
				  `listItemId` int(11) NOT NULL AUTO_INCREMENT,
				  `listId` int(11) NOT NULL,
				  `name` varchar(45) DEFAULT NULL,
				  `type` varchar(10) DEFAULT NULL,
				  `postId` int(11) DEFAULT NULL,
				  `termId` int(11) DEFAULT NULL,
				  `taxonomy` varchar(255) DEFAULT NULL,
				  `alt` varchar(255) DEFAULT NULL,
				  `url` varchar(255) DEFAULT NULL,
				  `target` varchar(10) DEFAULT NULL,
				  `icon` tinyint(1) DEFAULT NULL,
				  `iconContent` varchar(100) DEFAULT NULL,
				  `iconSize` varchar(11) DEFAULT NULL,
				  `iconColor` varchar(20) DEFAULT NULL,
				  `order` int(11) NOT NULL DEFAULT '0',
				  `desc` tinyint(1) DEFAULT NULL,
				  `description` blob DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`listItemId`),
				  KEY `hmenu_mega_list_items_listId_".$contraint_uid."_FK_idx` (`listId`),
				  CONSTRAINT `hmenu_mega_list_items_listId_".$contraint_uid."_FK` FOREIGN KEY (`listId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_list` (`listId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;					
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_list_items`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_list_items`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_list_items`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}		
		
		#ACTIVATE MENU MEGA MAP TABLE
		private function activate_mega_map(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_map` (
				  `mapId` int(11) NOT NULL AUTO_INCREMENT,
				  `megaMenuId` int(11) NOT NULL,
				  `heading` varchar(255) DEFAULT NULL,
				  `headingUnderline` tinyint(1) DEFAULT NULL,
				  `map` tinyint(1) DEFAULT NULL,
				  `mapHtml` blob DEFAULT NULL,
				  `shortcode` tinyint(1) DEFAULT NULL,
				  `mapShortcode` varchar(255) DEFAULT NULL,
				  `placement` varchar(10) DEFAULT NULL,
				  `type` varchar(10) DEFAULT 'map',
				  `description` varchar(255) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`mapId`),
				  KEY `hmenu_mega_map_megaMenuId_".$contraint_uid."_FK_idx` (`megaMenuId`),
				  CONSTRAINT `hmenu_mega_map_megaMenuId_".$contraint_uid."_FK` FOREIGN KEY (`megaMenuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_menu` (`megaMenuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;					
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_map`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_map`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_map`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}	
		
		#ACTIVATE MENU MEGA PRODUCT TABLE
		private function activate_mega_product(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_mega_product` (
				  `productId` int(11) NOT NULL AUTO_INCREMENT,
				  `megaMenuId` int(11) NOT NULL,
				  `heading` varchar(45) DEFAULT NULL,
				  `headingUnderline` tinyint(1) DEFAULT NULL,
				  `icon` varchar(10) DEFAULT NULL,
				  `description` varchar(120) DEFAULT NULL,
				  `placement` varchar(10) DEFAULT NULL,
				  `productCategory` varchar(10) DEFAULT NULL,
				  `productToDisplay` varchar(50) DEFAULT NULL,
				  `productHeading` tinyint(1) NOT NULL DEFAULT '0',
				  `productPrice` tinyint(1) NOT NULL DEFAULT '0',
				  `productDescription` tinyint(1) NOT NULL DEFAULT '0',
				  `productImage` tinyint(1) NOT NULL DEFAULT '0',
				  `productLink` varchar(120) DEFAULT NULL,
				  `productTarget` varchar(10) DEFAULT NULL,
				  `type` varchar(10) DEFAULT 'woo',
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`productId`),
				  KEY `hmenu_mega_product_megaMenuId_".$contraint_uid."_FK_idx` (`megaMenuId`),
				  CONSTRAINT `hmenu_mega_product_megaMenuId_".$contraint_uid."_FK` FOREIGN KEY (`megaMenuId`) REFERENCES `". $wpdb->base_prefix ."hmenu_mega_menu` (`megaMenuId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_mega_product`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_mega_product`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_mega_product`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}
		
		#ACTIVATE FONT PACK
		private function activate_font(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_font_pack` (
				  `fontId` int(11) NOT NULL AUTO_INCREMENT,
				  `fontName` varchar(45) DEFAULT NULL,
				  `fontPackType` varchar(45) DEFAULT NULL,
				  `fontPackName` varchar(45) DEFAULT NULL,
				  `fontEot` blob DEFAULT NULL,
				  `fontWoff` blob DEFAULT NULL,
				  `fontWoff2` blob DEFAULT NULL,
				  `fontTtf` blob DEFAULT NULL,
				  `fontSvg` blob DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`fontId`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;			
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_font_pack`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_font_pack`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_font_pack`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}	
		
		#ACTIVATE FONT PACK
		private function activate_font_icons(){
			//access globals
			global $wpdb;
			global $hmenu_helper;
			$contraint_uid = date('Hidmy');		
			//sql
			$sql_create = "
				CREATE TABLE IF NOT EXISTS `". $wpdb->base_prefix ."hmenu_font_icons` (
				  `iconId` int(11) NOT NULL AUTO_INCREMENT,
				  `fontId` int(11) NOT NULL,
				  `iconContent` varchar(100) DEFAULT NULL,
				  `iconPosition` varchar(100) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `lastModified` datetime DEFAULT NULL,
				  `deleted` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`iconId`),
				  KEY `hmenu_font_icons_fontId_".$contraint_uid."_FK_idx` (`fontId`),
				  CONSTRAINT `hmenu_font_icons_fontId_".$contraint_uid."_FK` FOREIGN KEY (`fontId`) REFERENCES `". $wpdb->base_prefix ."hmenu_font_pack` (`fontId`) ON DELETE NO ACTION ON UPDATE NO ACTION
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;	
			";			
			dbDelta($sql_create);
			$sql_drop = "
				DROP TRIGGER IF EXISTS `". $wpdb->base_prefix ."hmenu_font_icons`;
			";
			$wpdb->query($sql_drop);
			$sql_create = "
				CREATE TRIGGER `". $wpdb->base_prefix ."hmenu_font_icons`
				BEFORE UPDATE ON `". $wpdb->base_prefix ."hmenu_font_icons`
				FOR EACH ROW SET NEW.lastModified = NOW();	
			";
			dbDelta($sql_create);
		}	
		
		private function preload_color_sets(){
			//access globals
			global $wpdb;
			//data array #E05258
			$presets = array( 
				"set_1" => array(
					"name" => "White Grey", "color_one" => "#FFFFFF", "color_two" => "#F5F5F5",	"color_three" => "#888888",	"color_four" => "#888888", "slug" => "hero_white_grey"
				),
				"set_2" => array(
					"name" => "Light Grey", "color_one" => "#F5F5F5", "color_two" => "#CDD2D7",	"color_three" => "#888888",	"color_four" => "#FFFFFF", "slug" => "hero_light_grey"
				),
				"set_3" => array(
					"name" => "Faded Olive", "color_one" => "#95A5A5", "color_two" => "#7E8C8D",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_faded_olive"
				),
				"set_4" => array(
					"name" => "Dark Blue", "color_one" => "#33485F", "color_two" => "#2C3E51",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_dark_blue"
				),
				"set_5" => array(
					"name" => "Dark Grey", "color_one" => "#464F54", "color_two" => "#383F44",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_dark_grey"
				),
				"set_6" => array(
					"name" => "Dark Night", "color_one" => "#373B44", "color_two" => "#20232C",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_dark_night"
				),
				"set_7" => array(
					"name" => "Brown", "color_one" => "#7A6F5D", "color_two" => "#615745",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_brown"
				),
				"set_8" => array(
					"name" => "Light Olive", "color_one" => "#DDE0CF", "color_two" => "#C1C5B4",	"color_three" => "#7D7F73",	"color_four" => "#FFFFFF", "slug" => "hero_light_olive"
				),
				"set_9" => array(
					"name" => "Faded Pink", "color_one" => "#F4BFAF", "color_two" => "#D8A495",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_faded_pink"
				),
				"set_10" => array(
					"name" => "Faded Red", "color_one" => "#F86863", "color_two" => "#E05258",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_faded_red"
				),
				"set_11" => array(
					"name" => "Hero Red", "color_one" => "#FF4E50", "color_two" => "#E33B3D",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_hero_red"
				),
				"set_12" => array(
					"name" => "Red Fire", "color_one" => "#EA4B36", "color_two" => "#C33824",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_red_fire"
				),
				"set_13" => array(
					"name" => "Faded Yellow", "color_one" => "#FDF2C5", "color_two" => "#E1D6AA",	"color_three" => "#B3AB88",	"color_four" => "#FFFFFF", "slug" => "hero_faded_yellow"
				),
				"set_14" => array(
					"name" => "Faded Orange", "color_one" => "#F7A541", "color_two" => "#DA8B2B",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_faded_orange"
				),
				"set_15" => array(
					"name" => "Autumn", "color_one" => "#FFBE40", "color_two" => "#E3A42E",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_autumn"
				),
				"set_16" => array(
					"name" => "Orange", "color_one" => "#FC913A", "color_two" => "#E28133",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_orange"
				),
				"set_17" => array(
					"name" => "Pumpkin", "color_one" => "#E97E06", "color_two" => "#D45400",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_pumpkin"
				),
				"set_18" => array(
					"name" => "Bright Green", "color_one" => "#1DCE6C", "color_two" => "#1AAF5E",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_bright_green"
				),
				"set_19" => array(
					"name" => "Pastel Green", "color_one" => "#00BD9B", "color_two" => "#00A186",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_pastel_green"
				),
				"set_20" => array(
					"name" => "Light Green", "color_one" => "#B6D8C0", "color_two" => "#9DBEA7",	"color_three" => "#809C89",	"color_four" => "#FFFFFF", "slug" => "hero_light_green"
				),
				"set_21" => array(
					"name" => "Green Olive", "color_one" => "#93BBB5", "color_two" => "#85A8A3",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_green_olive"
				),
				"set_22" => array(
					"name" => "Washed Green", "color_one" => "#83AF9B", "color_two" => "#6E9784",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_washed_green"
				),
				"set_23" => array(
					"name" => "Turquoise", "color_one" => "#3FB8AF", "color_two" => "#2C9F97",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_turquoise"
				),
				"set_24" => array(
					"name" => "Light Green 2", "color_one" => "#9FD6D2", "color_two" => "#86BBB7",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_light_green_2"
				),
				"set_25" => array(
					"name" => "Light Green 3", "color_one" => "#BDD6D2", "color_two" => "#A3BBB6",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_light_green_3"
				),
				"set_26" => array(
					"name" => "Bright Blue", "color_one" => "#15C7EF", "color_two" => "#11B5D9",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_bright_blue"
				),
				"set_27" => array(
					"name" => "Blue", "color_one" => "#477FBF", "color_two" => "#3267A5",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_blue"
				),
				"set_28" => array(
					"name" => "Green Sea", "color_one" => "#16C1C8", "color_two" => "#00A6AD",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_green_sea"
				),
				"set_29" => array(
					"name" => "Green2", "color_one" => "#9AD9D2", "color_two" => "#88C1BA",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_green2"
				),
				"set_30" => array(
					"name" => "Stormy Sea", "color_one" => "#73A8AF", "color_two" => "#63949A",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_stormy_sea"
				),
				"set_31" => array(
					"name" => "Grey Blue", "color_one" => "#B8D2DC", "color_two" => "#A6BEC8",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_grey_blue"
				),
				"set_32" => array(
					"name" => "Light Purple", "color_one" => "#A1BCE3", "color_two" => "#92AACF",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_light_purple"
				),
				"set_33" => array(
					"name" => "Rose", "color_one" => "#D58FA7", "color_two" => "#BA7D92",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_rose"
				),
				"set_34" => array(
					"name" => "Shock Me", "color_one" => "#FE4365", "color_two" => "#E23151",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_shock_me"
				),
				"set_35" => array(
					"name" => "Bright Pink", "color_one" => "#FF3D7F", "color_two" => "#D7326B",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_bright_pink"
				),
				"set_36" => array(
					"name" => "Bright Pink 2", "color_one" => "#FC0284", "color_two" => "#CF006B",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_bright_pink_2"
				),
				"set_37" => array(
					"name" => "Purple", "color_one" => "#AB0768", "color_two" => "#8F0455",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_purple"
				),
				"set_38" => array(
					"name" => "Faded Purple", "color_one" => "#D9ABFF", "color_two" => "#BF93E3",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_faded_purple"
				),
				"set_39" => array(
					"name" => "Violet", "color_one" => "#9C54B7", "color_two" => "#903FAF",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_violet"
				),
				"set_40" => array(
					"name" => "Rich Red", "color_one" => "#AB3E5B", "color_two" => "#932C48",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_rich_red"
				),
				"set_41" => array(
					"name" => "Muddy", "color_one" => "#5C323E", "color_two" => "#48202C",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_muddy"
				),
				"set_42" => array(
					"name" => "Mixed1", "color_one" => "#373A44", "color_two" => "#E19089",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed1"
				),
				"set_43" => array(
					"name" => "Mixed2", "color_one" => "#584D47", "color_two" => "#E3CDBD",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed2"
				),
				"set_44" => array(
					"name" => "Mixed3", "color_one" => "#95A39D", "color_two" => "#E6BDB3",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed3"
				),
				"set_45" => array(
					"name" => "Mixed4", "color_one" => "#C9D2D1", "color_two" => "#373A44",	"color_three" => "#373A44",	"color_four" => "#FFFFFF", "slug" => "hero_mixed4"
				),
				"set_46" => array(
					"name" => "Mixed5", "color_one" => "#AB0768", "color_two" => "#070743",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed5"
				),
				"set_47" => array(
					"name" => "Mixed6", "color_one" => "#97BEA1", "color_two" => "#543459",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed6"
				),
				"set_48" => array(
					"name" => "Mixed7", "color_one" => "#A2A9AF", "color_two" => "#CFBDBF",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed7"
				),
				"set_49" => array(
					"name" => "Mixed8", "color_one" => "#F20544", "color_two" => "#590219",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed8"
				),
				"set_50" => array(
					"name" => "Mixed9", "color_one" => "#FC8F94", "color_two" => "#7F8C9D",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed9"
				),
				"set_51" => array(
					"name" => "Mixed10", "color_one" => "#2C3E51", "color_two" => "#F2C500",	"color_three" => "#FFFFFF",	"color_four" => "#2C3E51", "slug" => "hero_mixed10"
				),
				"set_52" => array(
					"name" => "Mixed11", "color_one" => "#242C36", "color_two" => "#C82D35",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed11"
				),
				"set_53" => array(
					"name" => "Mixed12", "color_one" => "#C82D35", "color_two" => "#242C36",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed12"
				),
				"set_54" => array(
					"name" => "Mixed13", "color_one" => "#6BBBD6", "color_two" => "#323A45",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed13"
				),
				"set_55" => array(
					"name" => "Mixed14", "color_one" => "#323A45", "color_two" => "#6BBBD6",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed14"
				),
				"set_56" => array(
					"name" => "Mixed15", "color_one" => "#FE6B6B", "color_two" => "#50575C",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed15"
				),
				"set_57" => array(
					"name" => "Mixed16", "color_one" => "#CDC4C5", "color_two" => "#534B58",	"color_three" => "#FFFFFF",	"color_four" => "#534B58", "slug" => "hero_mixed16"
				),
				"set_58" => array(
					"name" => "Mixed17", "color_one" => "#1C1C1C", "color_two" => "#E5DB2C",	"color_three" => "#FFFFFF",	"color_four" => "#1C1C1C", "slug" => "hero_mixed17"
				),
				"set_59" => array(
					"name" => "Mixed18", "color_one" => "#C97E90", "color_two" => "#526C8F",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed18"
				),
				"set_60" => array(
					"name" => "Mixed19", "color_one" => "#766A9A", "color_two" => "#85CBB2",	"color_three" => "#FFFFFF",	"color_four" => "#FFFFFF", "slug" => "hero_mixed19"
				)
			);
			foreach($presets as $set){				
				//check if font already exists
				$result_check = $wpdb->get_row("SELECT * FROM ". $wpdb->base_prefix ."hmenu_presets WHERE presetName = '".$set['name']."'");
				if($result_check != null){
					//dont add anything
				} else {
					//preload data
					$wpdb->query(
						"
							INSERT INTO `". $wpdb->base_prefix ."hmenu_presets` 
							(
								`presetName`,
								`bgColor`,
								`hoverColor`,
								`textColor`,
								`iconColor`,
								`slug`
							) VALUES (
								'".$set['name']."', 
								'".$set['color_one']."', 
								'".$set['color_two']."', 
								'".$set['color_three']."', 
								'".$set['color_four']."',
								'".$set['slug']."'
							)
						"
					);	
				}
			}			
		}		
				
		#SETUP PLUGIN
		public function setup_plugin(){
			//activate plugin
			$this->activate();
			//create plugin tables
			$this->activate_menu();	
			$this->activate_main_styles();
			$this->activate_dropdown_styles();	
			$this->activate_mega_styles();	
			$this->activate_mobile_styles();	
			$this->activate_mega_font_styles();	
			$this->activate_cart();	
			$this->activate_nav_items();	
			$this->activate_mega_menu();
			$this->activate_mega_blog();
			$this->activate_mega_contact();
			$this->activate_mega_contact_fields();
			$this->activate_mega_content();
			$this->activate_mega_image();
			$this->activate_mega_list();
			$this->activate_mega_list_items();
			$this->activate_mega_map();
			$this->activate_mega_product();
			$this->activate_search();
			$this->activate_social();
			$this->activate_font();
			$this->activate_font_icons();
			$this->activate_presets();
			//preload data
			$this->preload_color_sets();
		}
		
	}