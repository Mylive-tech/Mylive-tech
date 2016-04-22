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
abstract class PhpldfrontController extends Phpld_Controller_Action {

    /**
     * @var Phpld_Breadcrumbs
     */
    protected $_breadcrumbs = null;

    /**
     * @var Phpld_FlashMessenger
     */
    protected $_flashMessenger = null;

    public function init() {
        if (Model_CurrentUser::getInstance()->isLoggedIn()) {
            //...from session we take its level

            $this->view->assign('user_level', Model_CurrentUser::getInstance()->getLevel());
            $this->layout->assign('user_level', Model_CurrentUser::getInstance()->getLevel());
            $this->view->assign('regular_user_details', Model_CurrentUser::getInstance()->loadData());
            $this->layout->assign('regular_user_details', Model_CurrentUser::getInstance()->loadData());
        }

        if (
                Phpld_App::getInstance()->getRouter()->getController() == 'index' &&
                Phpld_App::getInstance()->getRouter()->getAction() == 'index'
        ) {
            $this->layout->assign('isMainPage', true);
            $this->view->assign('isMainPage', true);
        } else {
            $this->layout->assign('isMainPage', false);
            $this->view->assign('isMainPage', false);
        }

        $this->view->assign('ratings_on', LINK_RATING);
        $this->view->assign('comments_on', LINK_COMMENT);
        $this->view->assign('tell_friend_on', LINK_TELL_FRIEND);
        $this->_breadcrumbs = new Phpld_Breadcrumbs();
        $this->_flashMessenger = new Phpld_FlashMessenger();

        $this->_setupMetaData();
    }

    protected function _setupMetaData() {
        $this->layout->assign('VERSION', CURRENT_VERSION);
        $this->view->assign('VERSION', CURRENT_VERSION);

        $this->setMeta('robots');
        $this->setMeta('keywords');
        $this->setMeta('description');
        $this->setMeta('author');
        $this->setMeta('copyright');
    }

    protected function setMeta($tag, $value = null) {
        $default = null;
        switch ($tag) {
            case 'keywords':
                $default = DEFAULT_META_KEYWORDS;
                break;

            case 'description':
                $default = DEFAULT_META_DESCRIPTION;
                break;

            case 'author':
                $default = DEFAULT_META_AUTHOR;
                break;

            case 'copyright':
                $default = DEFAULT_META_COPYRIGHT;
                break;

            case 'robots':
                $default = DEFAULT_META_ROBOTS;
                break;
        }
        if (empty($value)) {
            $value = $default;
        }

        $this->layout->assign('Meta' . ucwords($tag), $value);
    }

    protected function setTitle($title, $rewrite = false) {
        if (!$rewrite) {
            $vars = $this->layout->get_template_vars();
            $title = $vars['PAGE_TITLE'] . (!empty($vars['PAGE_TITLE']) ? ' - ' : '') . $title;
        }

        $this->layout->assign('PAGE_TITLE', $title);
    }

    public function bc($label, $url = null) {
        $this->_breadcrumbs->add($label, $url);
    }

    public function render() {
        $this->_assignPlaceholders();

        return parent::render();
    }

    protected function _assignPlaceholders() {
        $vars = $this->layout->get_template_vars();       

        $this->layout->assign('SITE_NAME', SITE_NAME);
        $this->view->assign('SITE_NAME', SITE_NAME);
        $this->view->assign('LOGO_OPTIONS', Phpld_Layout::getCurrent()->getLogoOptions());
        $this->view->assign('LOGO_STYLES', Phpld_Layout::getCurrent()->getLogoStyles());       
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $phpldSettings = read_config($db);
        $exclude_submit_item = '';
        if (isset($phpldSettings['DISABLE_SUBMIT']) AND $phpldSettings['DISABLE_SUBMIT'] == 1) {
            $exclude_submit_item = ' WHERE URL<>"submit" ';
        }
        $menuPages = $db->GetAll('SELECT * FROM '.$tables['menu_items']['name'].' '.$exclude_submit_item.' ORDER BY `ORDER_ID`');
        $menu = buildMenuTree($menuPages);       
        $this->view->assign('menuList', $menu);
        $this->layout->assign('MAIN_MENU', $this->view->fetch('views/_shared/_placeholders/mainMenu.tpl')); 
		$this->layout->assign('RSS', $this->view->fetch('views/_shared/_placeholders/rss.tpl')); 
        $this->layout->assign('HEADER_LOGO', $this->view->fetch('views/_shared/_placeholders/headerLogo.tpl'));
        $this->layout->assign('USER_PANEL', $this->view->fetch('views/_shared/_placeholders/userPanel.tpl'));

        if (SECOND_SEARCH_FIELD) {
            $this->layout->assign('HEADER_SEARCH_FORM', $this->view->fetch('views/_shared/_placeholders/headerSearchFormBussines.tpl'));
        } else {
            $this->layout->assign('HEADER_SEARCH_FORM', $this->view->fetch('views/_shared/_placeholders/headerSearchForm.tpl'));
        }
        $this->layout->assign('SITE_LOGO', $this->view->fetch('views/_shared/_placeholders/siteLogo.tpl'));
        $this->_breadcrumbs->assign($this->layout);
        $this->_flashMessenger->assign($this->layout);

        $this->layout->assign('LINK_CLICK_TRACKER_CODE', $this->view->fetch('views/_shared/clickTrackerCode.tpl'));

        if (!isset($vars['PAGE_TITLE']) || empty($vars['PAGE_TITLE'])) {
            $this->layout->assign('PAGE_TITLE', DIRECTORY_TITLE);
        }
    }

    /**
     * @return Phpld_FlashMessenger
     */
    public function fm() {
        return $this->_flashMessenger;
    }

    public function _preDispatch() {
        $this->_breadcrumbs->add(SITE_NAME, SITE_URL);
    }

    public function addJavascript($href)
    {
        Phpld_View::addJavascript($href);
    }

    public function addStylesheet($href)
    {
        Phpld_View::addStylesheet($href);
    }
}