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
 
class Widget_LatestListingComments extends Phpld_Widget {

	function getContent() {
	
		$set = $this->getFrontSettings();
    	
		$this->tpl->assign("TITLE", $set['TITLE']);
    	
	     	$this->tpl->assign('latest_com', $this->getComs());
		return $this->tpl->fetch('content.tpl');
	}
    function getComs() {
        global $db;
        global $tables;

       
        //end of article permission realted
         $set = $this->getFrontSettings();

        if (intval($set['NO_OF_COMMENTS']) > 0) {
			$limit = intval($set['NO_OF_COMMENTS']);
		} else {
			$limit = 10;
		}
		
		$qry = "SELECT lc.*, l.*, DATE_FORMAT(lc.DATE_ADDED, '%b %d') AS `DAT` 
								FROM `{$tables['comment']['name']}` lc LEFT JOIN `{$tables['link']['name']}` l
								ON (lc.`ITEM_ID` = l.`ID`) 
								WHERE (lc.`STATUS` = '2' AND lc.`TYPE` = '1')
								ORDER BY lc.`DATE_ADDED` DESC LIMIT 0, ".$limit;
		if ($set['CACHE_RESULTS'] == "Yes") {
			$latest_com = $db->CacheGetAll($qry);
		} else {
			$latest_com = $db->GetAll($qry);
		}
        if (isset ($latest_com)) {
            $latest_com = addCategPathToLinks($latest_com);
        }

        $collection = new Phpld_Model_Collection('Model_Link_Entity');

        $collection->setElements($latest_com);

        return $collection;
    }
	
}
		

?>