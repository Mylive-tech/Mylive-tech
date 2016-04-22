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
 
class Widget_LatestListingsList extends Phpld_Widget_LinksList {

    function getContent() {

        $set = $this->getFrontSettings();

        $this->tpl->assign("TITLE", $set['TITLE']);

        $this->tpl->assign('LISTINGS', $this->renderLinks());

        return $this->tpl->fetch('content.tpl');
    }

    function getLinks() {
        global $db;
        global $tables;


        $categoryModel = new Model_Category();
        $permissions = $categoryModel->getPermissions();
        //end of article permission realted
        $email_conf = '';
        if (EMAIL_CONFIRMATION == 1) {
            $email_conf = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
        }

        $set = $this->getFrontSettings();

        if (intval($set['NO_OF_LINKS']) > 0) {
            $limit = intval($set['NO_OF_LINKS']);
        } else {
            $limit = 10;
        }

        $listingTypeWhere = null;
        $listingType = $this->_getListingType();

        if (!is_null($listingType)) {
            $listingTypeWhere = 'AND LINK_TYPE = '.$listingType;
        }
        $sql = "SELECT * FROM `{$tables['link']['name']}`
										WHERE (`STATUS` = 2 OR {$permissions['permission_links_arts']}) {$email_conf} {$listingTypeWhere}
										ORDER BY ID DESC LIMIT 0,".$limit;

        if ($set['CACHE_RESULTS'] == "Yes") {
            $latestlinks = $db->CacheGetAll($sql);
        } else {
            $latestlinks = $db->GetAll($sql);
        }
        if (isset ($latestlinks)) {
            $latestlinks= addCategPathToLinks($latestlinks);
        }
        $collection = new Phpld_Model_Collection('Model_Link_Entity');
        
        
        $collection->setElements($latestlinks);


        return $collection;
    }

    protected function _getAdditionalSettings() {
        return array();
    }
}