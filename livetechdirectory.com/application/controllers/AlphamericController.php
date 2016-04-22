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
 # Copyright (C) 2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
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
 # @copyright      2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.1.0 Phoenix Release
 # ################################################################################
 */	
class AlphamericController extends PhpldfrontController
{
    public function indexAction()
    {
	$db = Phpld_Db::getInstance()->getAdapter();
	$tables = Phpld_Db::getInstance()->getTables();
	$linkModel = new Model_Link();
        $paginator = new Phpld_Paginator();
	$sorter = new Phpld_Sorter();
        $limit = $paginator->getLimit();
	$letter = $this->getParam('id');
	
	
	if (EMAIL_CONFIRMATION == 1) {
            $email_conf = " AND `{$tables['link']['name']}`.`OWNER_EMAIL_CONFIRMED` = '1' ";
        }
	$expire_where = "AND (`{$tables['link']['name']}`.`EXPIRY_DATE` >= ".$db->DBDate(time())." OR `{$tables['link']['name']}`.`EXPIRY_DATE` IS NULL)";
        
	
	if ($letter == '0-9') 
	     $listingWhere = "`{$tables['link']['name']}`.`TITLE` LIKE '0%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '1%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '2%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '3%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '4%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '5%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '6%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '7%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '8%' OR
				`{$tables['link']['name']}`.`TITLE` LIKE '9%' AND
				`{$tables['link']['name']}`.`STATUS` = '2' {$email_conf} {$expire_where} ";
	else
	    $listingWhere = "`{$tables['link']['name']}`.`TITLE` LIKE '$letter%' AND `{$tables['link']['name']}`.`STATUS` = '2' ";
	
	
	
	$links = $linkModel->getLinks($listingWhere,"TITLE", $paginator->getOffset(), $paginator->getPerPage());
	

	if ($links->countWithoutLimit() > 0) {
            $this->_breadcrumbs->add(_L('Alphameric'));
            $paginator->assign($this->view, $links->countWithoutLimit());
            $this->view->assign('links', $links);
        }
	 $this->view->assign('letter', $letter);
    }
}
