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
class Widget_VectorMap extends Phpld_Widget {

    protected $_types = array(
        'USA' => array(
            'id'  => 'us_en',
            'file' => 'jquery-jvectormap-us-en.js'
        ),
        'Europe' => array(
            'id'  => 'europe_en',
            'file' => 'jquery-jvectormap-europe-en.js'
        ),
        'World' => array(
            'id'  => 'world_en',
            'file' => 'jquery-jvectormap-world-en.js'
        )
    );

    function getContent() {
        //var_dump($this->tpl->get_template_vars());die();
        Phpld_View::addJavascript(DOC_ROOT.'/javascripts/jvectormap/jquery-jvectormap.js');
        return $this->tpl->fetch('content.tpl');
    }

    public function getConfig() {
        $config = parent::getConfig();
        $additionalConfig = $this->_getAdditionalSettings();
        //var_dump($additionalConfig);
        if (!is_null($additionalConfig)) {
            $config['SETTINGS'] = array_merge($config['SETTINGS'], $additionalConfig);
        }

        return $config;
    }

    protected function _getAdditionalSettings() {
        $settings = $this->getFrontSettings();
        //var_dump($settings['MAP_URLS']);die();
        $regions = $this->getMapRegions();
        $categs = get_categs_tree();
        $categsPrepared = '';
        foreach ($categs as $id =>$categ) {
            $categsPrepared .= $id.':'.$categ.',';
        }
        $categsPrepared = substr($categsPrepared, 0, -1);
        foreach ($regions as $region=>$name) {
            $config[] = array(
                    'NAME' => $name,
                    'IDENTIFIER' => 'MAP_URLS['.$settings['MAP_TYPE'].']['.$region.']',
                    'VALUE' => $settings['MAP_URLS'][$settings['MAP_TYPE']][$region],
                    'ALLOWED' => $categsPrepared,
            );
        }
        return $config;
    }

    public function getMapSettings($field)
    {
        $settings = $this->getFrontSettings();
        //var_dump($settings);die();
        return $this->_types[$settings['MAP_TYPE']][$field];
    }

    public function getMapRegions()
    {
        $file = $this->getMapSettings('file');
        //die(INSTALL_PATH.'javascripts/vectormap/maps/'.$file);
        $content = file_get_contents(INSTALL_PATH.'javascripts/jvectormap/maps/'.$file);
        $content = substr($content, strpos($content, '"paths"'));
        $content = substr($content, 0, strpos($content, ', "height"'));
        $content = '{'.$content.'}';
        $data = json_decode($content);
        //var_dump($data->paths);
        $regions = array();
        foreach ($data->paths as $key=>$path) {
            $regions[$key] = $path->name;
        }
        asort($regions);

        return $regions;
    }

    public function getUrlMapJson()
    {
        global $db;
        global $tables;

        $map = $this->getUrlMap();
        $query = "SELECT * FROM `{$tables['category']['name']}` WHERE (`STATUS` = '2')";
        $categsCollection = new Phpld_Model_Collection('Model_Category_Entity');
        $rs = $db->cacheGetAll($query);
        $categsCollection->setElements($rs);

        $categoriesArr = array();
        //var_dump($categories->toArray());
        foreach ($categsCollection as $category) {
            $categoriesArr[$category['ID']] = $category->getUrl();
        }

        foreach ($map as $id=>$val) {
            $map[$id] = $categoriesArr[$val];
        }
        //var_dump($categoriesArr, $map);die();
        return json_encode($map);
    }

    public function getUrlMap()
    {
        $settings = $this->getFrontSettings();
        return $settings['MAP_URLS'][$settings['MAP_TYPE']];
    }

    function saveSettings($set) {
        global $db;
        global $tables;

        $settings = array();
        foreach ($set as $setting) {
            $settings[$setting['IDENTIFIER']] = $setting['SETTING_VALUE'];
        }
        try {
            $settings['MAP_URLS'] = $_POST['MAP_URLS'];
            //var_dump($settings);die();
            $db->Execute("UPDATE `{$tables['widget_activated']['name']}` SET OPTIONS = '" . serialize($settings) . "' WHERE ID = " . $this->id);
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    function getSettingsForm() {
        global $tpl;

        $settings = $this->getSettings();
        $storedOptions = $this->getFrontSettings();
//        var_dump($settings);die();
        foreach ($this->getUrlMap() as $region => $value) {
            $storedOptions['MAP_URLS['.$storedOptions['MAP_TYPE'].']['.$region.']'] = $value;
        }

        foreach ($settings as $id => $setting) {
            $settings[$id]['VALUE'] = $storedOptions[$setting['IDENTIFIER']];
        }

        foreach ($settings as $i => $setting) {
            $settings[$i]['ALLOWED'] = explode(",", $settings[$i]['ALLOWED']);
        }

        $tpl->assign('list', $settings);

        return $tpl->fetch('DefaultAdmin/dir_widgets_edit.tpl');
    }
}

?>