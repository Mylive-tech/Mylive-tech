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
class IndexController extends PhpldfrontController
{
    public function indexAction()
    {
        $this->view->assign("MAINCONTENT", MAINCONTENT);
    }

    public function latestAction()
    {
        $categoryModel = new Model_Category();
        $linksModel = new Model_Link();
        $permissions = $categoryModel->getPermissions(Model_CurrentUser::getInstance()->getId());

        $expire_where = "AND (`EXPIRY_DATE` >= NOW() OR `EXPIRY_DATE` IS NULL)";

        $this->_breadcrumbs->add(_L('Latest Listings'));
        $email_conf = '';
        if (EMAIL_CONFIRMATION == 1) {
            $email_conf = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
        }
        $links = $linksModel->getLinks("(`PLD_LINK`.`STATUS` = '2' OR {$permissions['permission_links_arts']}) {$email_conf} {$expire_where}", "`DATE_ADDED` DESC", 0, LINKS_TOP);
        $this->view->assign('links', $links);
        $this->layout->assign('PAGE_TITLE', 'Link Stream - ' . DIRECTORY_TITLE);
    }

    public function topAction()
    {
        $categoryModel = new Model_Category();
        $linksModel = new Model_Link();
        $permissions = $categoryModel->getPermissions(Model_CurrentUser::getInstance()->getId());
        $email_conf = '';
        if (EMAIL_CONFIRMATION == 1) {
            $email_conf = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
        }
        $expire_where = "AND (`EXPIRY_DATE` >= NOW() OR `EXPIRY_DATE` IS NULL)";

        $this->_breadcrumbs->add(_L('Top Listings'));

        $links = $linksModel->getLinks(" (`PLD_LINK`.`STATUS` = '2' OR {$permissions['permission_links_arts']}) {$email_conf} {$expire_where}", "`HITS` DESC", 0, LINKS_TOP);
        $this->view->assign('links', $links);
        $this->layout->assign('PAGE_TITLE', 'Top Hits - ' . DIRECTORY_TITLE);
    }

    public function rulesAction()
    {
        $this->bc('Submission Rules');
        $this->view->assign('rules', SUBMIT_TERMS);
    }

    public function unauthorizedAction()
    {

    }

    public function bannedAction()
    {
        $this->setTitle(_l('Not authorized To Submit Link'));
    }

	public function rssAction()
	{
		$categoryModel = new Model_Category();
		$linksModel = new Model_Link();

		$order="`DATE_ADDED`";
		$page=1;
		$categoryFilter="";

		if(isset($_REQUEST['search']))
		{

		}
		elseif(isset($_REQUEST['list']))
		{
			switch ($_REQUEST['list'])
			{
				case 'latest':
					$order='`DATE_ADDED`';
					$this->view->assign('title', ' - Latest Links');
					break;
				case 'top':
					$order='`HITS`';
					$this->view->assign('title', ' - Top Hits');
					break;
				default:
					http_custom_redirect(DOC_ROOT.'/rss?c=0');
					break;
			}
		}
		elseif(isset($_REQUEST['p']) && intval($_REQUEST['p'])>0) //page
		{
			$page=intval($_REQUEST['p']);
		}
		elseif(isset($_REQUEST['c'])) //category
		{
			$id=intval($_REQUEST['c']);
			$categoryFilter="AND `CATEGORY_ID`=$id";
			$path = get_path($id);
			$title="";
			for ($i = 1; $i < count ($path); $i++)
			{
				if ($i > 1)
					$title .= ' > ';
				$title .= $path[$i]['TITLE'];
			}
			$this->view->assign('title', $title);
		}
		elseif(isset($_REQUEST['search']))
		{
			$search_preferences = array();
			$search_preferences['Select_Options']  = array ( '*' );
			$search_preferences['Where_Options']   = array ( "`STATUS` = '2'"            ,
				"(`EXPIRY_DATE` >= CURRENT_DATE OR `EXPIRY_DATE` IS NULL)"
			);
			$search_preferences['Order_Options']   = array ( '`'.$order.'` DESC' );
			$search_preferences['Search_Location'] = array ( 'URL' ,
				'TITLE',
				'DESCRIPTION'
			);

			$links = search($search_preferences);

			$this->view->assign('title', ' - Search results');
			$this->view->assign('description', ' Search results for: '.htmlspecialchars($_REQUEST['search']));
		}

		if(!isset($_REQUEST['search']))
		{
			$permissions = $categoryModel->getPermissions(Model_CurrentUser::getInstance()->getId());

			$expire_where = "AND (`EXPIRY_DATE` >= NOW() OR `EXPIRY_DATE` IS NULL)";

			$email_conf = '';
			if (EMAIL_CONFIRMATION == 1) {
				$email_conf = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
			}
			$links = $linksModel->getLinks("(`PLD_LINK`.`STATUS` = '2' OR {$permissions['permission_links_arts']}) {$categoryFilter} {$email_conf} {$expire_where}", "$order DESC", ($page-1)*LINKS_TOP, LINKS_TOP);
		}
	
		$this->view->assign('links', $links);
		$this->view->assign('url', "http://".$_SERVER['SERVER_NAME'].DOC_ROOT);


		header('Content-type: application/xml');

		//Clean whitespace
		//$this->view->load_filter('output', 'trimwhitespace');
		$viewScript = $this->getViewScript();
		echo $this->view->fetch('views/'.$viewScript);
		exit();
	}


    public function confirmemailAction()
    {
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        // Disable any caching by the browser
        disable_browser_cache();

        if (isset($_REQUEST['uid'])) {
            $result = $db->Execute("UPDATE `{$tables['user']['name']}` SET `EMAIL_CONFIRMED` = '1' WHERE `ID`=".$db->qstr($_REQUEST['uid'])."");

            http_custom_redirect(DOC_ROOT."/user/confirmed");

        } elseif (isset($_REQUEST['lid'])) {
            $result = $db->Execute("UPDATE `{$tables['link']['name']}` SET `OWNER_EMAIL_CONFIRMED` = '1' WHERE `ID`=".$db->qstr($_REQUEST['lid'])."");

            http_custom_redirect(DOC_ROOT."/submit/confirmed");
        }
    }
}