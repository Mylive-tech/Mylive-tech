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
class PageController extends PhpldfrontController
{
    public function indexAction()
    {
        $pageModel = new Model_Page();

        $pageName = $this->getParam('name');

        $page = $pageModel->getPage($pageName);

        if (is_null($page)) {
            throw new Phpld_Exception_NotFound();
        }

        $this->setMeta('keywords', $page['META_KEYWORDS']);
        $this->setMeta('description', $page['META_DESCRIPTION']);

        $this->setTitle($page['NAME']);
        $this->bc($page['NAME']);
        // Get status of the page
        if (($page['STATUS'] != 2) && !(Model_CurrentUser::getInstance()->isLoggedIn() && (get_user_level($_SESSION['phpld']['user']['id']) == 1 || get_user_level($_SESSION['phpld']['user']['id']) == 3 )))
            httpstatus('404');

        // Get privacy of page
        if ($page['PRIVACY'] == 1 AND !Model_CurrentUser::getInstance()->isLoggedIn())
            http_custom_redirect(DOC_ROOT .'/login');

        $this->view->assign('PAGE', $page);
    }
}