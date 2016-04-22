<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	 
	
/**
 # ################################################################################
 # Project:   PHP Link Directory
 #
 # **********************************************************************
 # Copyright (C) 2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
 #
 # This software is for use only to those who have purchased a license.
 # A license must be purchased for EACH installation of the software.
 #
 # By using the software you agree to the terms:
 #
 #    - You may not redistribute, sell or otherwise share this software
 #      in whole or in part without the consent of the the ownership
 #      of PHP Link Directory. Please contact david@david-duval.com
 #      if you need more information.
 #
 #    - You agree to retain a link back to http://www.phplinkdirectory.com/
 #      on all pages of your directory if you purchased any of our "link back" 
 #      versions of the software.
 #
 #
 # In some cases, license holders may be required to agree to changes
 # in the software license before receiving updates to the software.
 # **********************************************************************
 #
 # For questions, help, comments, discussion, etc., please join the
 # PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
 #
 # @link           http://www.phplinkdirectory.com/
 # @copyright      2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.0 Codename Transformer
 # ################################################################################
 */
 class Widget_PopularCategories extends Phpld_Widget {

	function getContent() {
		$db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $categoryModel = new Model_Category();
	    
		$set = $this->getFrontSettings();
	    
		if (intval($set['NO_OF_CATEGORIES']) > 0) {
			$limit = intval($set['NO_OF_CATEGORIES']);
		} else {
			$limit = 10;
		}

        $permissions = $categoryModel->getPermissions(Model_CurrentUser::getInstance()->getId());
		if ($set['CACHE_RESULTS'] == "Yes") {
			$sql = "SELECT * FROM `{$tables['category']['name']}`
				WHERE (`STATUS` = '2' OR {$permissions['permission_cats']})
				ORDER BY `HITS` DESC LIMIT 0, ".$limit;
		} else {
			$sql = "SELECT * FROM `{$tables['category']['name']}`
				WHERE (`STATUS` = '2' OR {$permissions['permission_cats']})
				ORDER BY `HITS` DESC LIMIT 0, ".$limit;
		}

        $popularCategs = $db->GetAll($sql);
        $categsCollection = new Phpld_Model_Collection(Model_Category_Entity);
        $categsCollection->setElements($popularCategs);

		$this->tpl->assign('popularCategs', $categsCollection);
		
		if ($set['DISPLAY_IN_BOX'] == "No") {
			$this->tpl->assign('show_title', 1);
		}
		$this->tpl->assign("TITLE", $set['TITLE']);
						
		return $this->tpl->fetch('content.tpl');
	}
}

?>