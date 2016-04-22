<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	

abstract class Phpld_Widget_LinksList extends Phpld_Widget {

    protected $_defaultListingStyle = 'list';
    protected $_defaultColumnsNumber = 1;

    /**
     * Get links list for widget
     * @abstract
     *
     * @return Phpld_Model_Collection
     */
    abstract public function getLinks();

    function __construct($name, $id, $type = '') {
        parent::__construct($name, $id, $type);

        $style = $this->_getStyle();
        Phpld_View::addJavascript(DOC_ROOT.'/javascripts/jquery/jquery.wookmark.js');
        Phpld_View::addJavascript(DOC_ROOT.'/javascripts/zeroclipboard/ZeroClipboard.js');
        $this->tpl->assign('linkColumns', $this->_getColumns());
        $this->tpl->assign('linkStyle', $style);
    }

    public function getFrontSettings() {
        $frontSettings = parent::getFrontSettings();
        return $frontSettings;
    }

    public function getConfig() {
        $config = parent::getConfig();
        $additionalConfig = $this->_getAdditionalSettings();
        $config['SETTINGS'] = array_merge($config['SETTINGS'], $additionalConfig);
        return $config;
    }

    protected function _getAdditionalSettings() {
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        $query = 'SELECT ID,NAME FROM ' . $tables['link_type']['name'] . ' WHERE STATUS=2';
        $listingTypes = $db->getAll($query);
        $listingTypesAllowed[] = '-- All types --';
        foreach ($listingTypes as $type) {
            $listingTypesAllowed[] = $type['NAME'];
        }
        $config = array(
            array(
                'NAME' => 'STYLE',
                'IDENTIFIER' => 'STYLE',
                'VALUE' => 'Get From Category Settings',
                'ALLOWED' => 'Get From Category Settings,List,Grid',
            ),
            array(
                'NAME' => 'COLUMNS',
                'IDENTIFIER' => 'COLUMNS',
                'VALUE' => 'Get From Category Settings',
                'ALLOWED' => 'Get From Category Settings,1,2,3,4',
            ),
            array(
                'NAME' => 'LISTING TYPE',
                'IDENTIFIER' => 'LISTING_TYPE',
                'VALUE' => 'LIST',
                'ALLOWED' => implode(',', $listingTypesAllowed),
            ),
        );
        return $config;
    }

    protected function _getStyle() {
        $frontSettings = parent::getFrontSettings();
        if (isset($frontSettings['STYLE'])) {
            if ($frontSettings['STYLE'] == 'Get From Category Settings') {
                $category = $this->_getCategory();
                if (empty($category['STYLE'])) {
                    return $this->_defaultListingStyle;
                } else {
                    return $category['STYLE'];
                }
            }
            return strtolower($frontSettings['STYLE']);
        } else {
            return $this->_defaultListingStyle;
        }
    }

    protected function _getColumns() {
        $frontSettings = parent::getFrontSettings();
        if (isset($frontSettings['COLUMNS'])) {
            if ($frontSettings['STYLE'] == 'Get From Category Settings') {
                $category = $this->_getCategory();
                if (empty($category['COLS'])) {
                    return $this->_defaultColumnsNumber;
                } else {
                    return $category['COLS'];
                }
            }
            return strtolower($frontSettings['COLUMNS']);
        } else {
            return $this->_defaultColumnsNumber;
        }
    }

    protected function _getCategory() {
        $categoryModel = new Model_Category();
        $category = $categoryModel->getCategoryByUri();
        return $category;
    }

    protected function renderLinks($links = null) {
        if (is_null($links)) {
            $links = $this->getLinks();
        }
        $view = Phpld_View::getView();
        $view->assign('widgetID', $this->id);
        $view->assign('links', $links);
        $view->assign('linkColumns', $this->_getColumns());
        $view->assign('linkStyle', $this->_getStyle());
        //die($view->template_dir);
        $result = $view->fetch('views/_shared/listingRender.tpl');
        return $result;
    }

    protected function _getListingType() {
        $frontSettings = parent::getFrontSettings();

        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        $query = 'SELECT ID FROM ' . $tables['link_type']['name'] . ' WHERE STATUS=2 AND NAME = "' . $frontSettings['LISTING_TYPE'] . '"';
        $listingType = $db->getOne($query);
        if (!is_null($listingType)) {
            return $listingType;
        } else {
            return null;
        }
    }

}