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
 class Widget_Stats extends Phpld_Widget {

	function getContent() {
		global $db;
		global $tables;

		$set = $this->getFrontSettings();
			
		if ($set['ACTIVE_LINKS'] == "Yes") {
			$this->tpl->assign('active_links', 1);
			if ($set['CACHE_RESULTS'] == "Yes") {
				$stats['statActiveLinks']      = $db->CacheGetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` > '1'");	
			} else {
				$stats['statActiveLinks']      = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` > '1'");
			}
		}
		if ($set['PENDING_LINKS'] == "Yes") {
			$this->tpl->assign('pending_links', 1);
			if ($set['CACHE_RESULTS'] = "Yes") {
				$stats['statPendingLinks']     = $db->CacheGetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '1'");
			} else {
				$stats['statPendingLinks']     = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '1'");
			}
		}
		if ($set['TODAY_LINKS'] == "Yes") {
			$this->tpl->assign('today_links', 1);
			if ($set['CACHE_RESULTS'] == "Yes") {
				$stats['statTodaysLinks']      = $db->CacheGetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `DATE_ADDED` LIKE '".date ('Y-m-d', TIMENOW)."%'");
			} else {
				$stats['statTodaysLinks']      = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `DATE_ADDED` LIKE '".date ('Y-m-d', TIMENOW)."%'");
			}
		}
		if ($set['TOTAL_CATEGORIES'] == "Yes") {
			$this->tpl->assign('total_categories', 1);
			if ($set['CACHE_RESULTS'] == "Yes") {
				$stats['statCategories']       = $db->CacheGetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = '0'");
			} else {
				$stats['statCategories']       = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = '0'");	
			}
		}
		if ($set['SUBCATEGORIES'] == "Yes") {
			$this->tpl->assign('subcategories', 1);
			if ($set['CACHE RESULTS'] == "Yes") {
				$stats['statSubCategories']    = $db->CacheGetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` <> '0'");	
			} else {
				$stats['statSubCategories']    = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` <> '0'");
			}
		}
		
		if ($set['DISPLAY_IN_BOX'] == "No") {
			$this->tpl->assign('show_title', 1);
		}
		$this->tpl->assign("TITLE", $set['TITLE']);

		$this->tpl->assign($stats);
		return $this->tpl->fetch('content.tpl');
	}
}

?>