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
abstract class Phpld_Widget {

    protected $name;
    protected $type;
    protected $id;
    protected $tpl;
    protected $editorJavascripts = array();
    protected $editorJsCode = array();
    protected $editorStyles = array();

    public function getId()
    {
        return $this->id;
    }
    /**
     * @static
     * @param array $data array('NAME', 'TYPE', 'TPL')
     * @return Phpld_Widget
     */
    public static function factory($data) {
        if (DEBUG) {
            get_execution_time();
        }

		$widgetPath = INSTALL_PATH.'/application/widgets/' . $data['NAME'] . '/widget.php';
		if(file_exists($widgetPath))
		{
			try
			{
				require_once($widgetPath);
				$className = 'Widget_' . $data['NAME'];
				$idWidget = $data['ID'];
				$widget = new $className($data['NAME'], $idWidget);
				return $widget;
			}
			catch(Exception $ex)
			{
				if(DEBUG)
				{
					var_dump($ex);
				}
				return null;
			}
		}
		if(DEBUG)
		{
			echo "Widget file \"$widgetPath\" not found";
		}
		return null;
    }

    /**
     * @static
     * @param $idWidget
     * @return Phpld_Widget
     */
    public static function load($idWidget) {
        global $db;
        global $tables;

        $widgetData = $db->getRow("SELECT * FROM `{$tables['widget_activated']['name']}` WHERE ID = {$idWidget}");
        if (!$widgetData) {
            $widgetData = array('ID' => 0, 'NAME' => $idWidget);
        }
        return self::factory($widgetData);
    }

    function __construct($name, $id, $type = '') {
        $this->name = $name;
        $this->id = $id;
        $this->type = $type;
        $this->tpl = $this->_getTemplate();
        $this->tpl->assign('date_format', '%D %H:%M:%S');
    }

    function install($zone) {
        global $db;
        global $tables;

        $config = $this->getSettings();
        $options = array();
        foreach ($config as $option) {
            $options[$option['IDENTIFIER']] = $option['VALUE'];
        }
        $maxOrder = $db->getOne('SELECT MAX(ORDER_ID) FROM '.$tables['widget_activated']['name'].' WHERE ZONE = "'.$zone.'"');
        if (is_null($maxOrder)) {
            $maxOrder = 1;
        }

        if ($db->Execute("INSERT INTO `{$tables['widget_activated']['name']}` (`NAME`, `ZONE`, `OPTIONS`, `ORDER_ID`) VALUES (" . $db->qstr($this->name) . ", " . $db->qstr($zone) . ", " . $db->qstr(serialize($options)) . ", ".$maxOrder.")")) {

        //if ($db->Execute("UPDATE `{$tables['widget_activated']['name']}` SET ACTIVE = 1 WHERE `ID` = " . $db->qstr($this->id))) {
            return true;
        } else {
            return false;
        }
    }

    function uninstall() {
        global $db;
        global $tables;

        if ($db->Execute("DELETE FROM `{$tables['widget']['name']}` WHERE `NAME` = " . $db->qstr($this->name) . "")) {
            $db->Execute("DELETE FROM `{$tables['widget_activated']['name']}` WHERE `NAME` = " . $db->qstr($this->name) . "");
            return true;
        } else {
            return false;
        }
    }


    function removeFromZone() {
        global $db;
        global $tables;
        if ($db->Execute("DELETE FROM `{$tables['widget_activated']['name']}` WHERE `ID` = " . $db->qstr($this->id) . "")) {
            return true;
        } else {
            return false;
        }
    }

    function getConfig() {
        if ($dh = opendir(INSTALL_PATH . "/application/widgets/")) {
            if (file_exists(INSTALL_PATH . "application/widgets/" . $this->name . "/config.xml")) {
                $wid = get_widget_xml($this->name);
                $wid['NAME'] = $this->name;
                $wid['SETTINGS'][] = array(
                    'NAME' => 'DISPLAY IN BOX',
                    'IDENTIFIER' => 'DISPLAY_IN_BOX',
                    'VALUE' => 'Yes',
                    'ALLOWED' => 'Yes,No',
                    'INFO' => 'If "Yes", a box encloses the output of the widget on the frontend.',
                );
                $wid['SETTINGS'][] = array(
                    'NAME' => 'SHOW TITLE',
                    'IDENTIFIER' => 'SHOW_TITLE',
                    'VALUE' => 'Yes',
                    'ALLOWED' => 'Yes,No',
                    'INFO' => 'If "No", title will not display in front.',
                );                
            }
            closedir($dh);
        } else {
            return false;
        }
        return $wid;
    }

    function getSettingsForm() {
        global $tpl;

        $settings = $this->getSettings();
        $storedOptions = $this->getFrontSettings();

        foreach ($settings as $id => $setting) {
            $settings[$id]['VALUE'] = $storedOptions[$setting['IDENTIFIER']];
        }

        foreach ($settings as $i => $setting) {
            $settings[$i]['ALLOWED'] = explode(",", $settings[$i]['ALLOWED']);
        }

        $tpl->assign('list', $settings);

		if(file_exists(INSTALL_PATH . "application/widgets/{$this->name}/templates/config_before.tpl")) {
			$tpl->assign('config_before',INSTALL_PATH . "application/widgets/{$this->name}/templates/config_before.tpl");
			$tpl->assign('widget_url',FRONT_DOC_ROOT."application/widgets/" . $this->name . "/");
		}
		if(file_exists(INSTALL_PATH . "application/widgets/{$this->name}/templates/config_after.tpl")) {
			$tpl->assign('config_after',INSTALL_PATH . "application/widgets/{$this->name}/templates/config_after.tpl");
			$tpl->assign('widget_url',FRONT_DOC_ROOT."application/widgets/" . $this->name . "/");
		}
        $this->renderEditorStylesAndJavascripts();
        return $tpl->fetch('DefaultAdmin/dir_widgets_edit.tpl');
    }

    function getSettings() {
        $config = $this->getConfig();

        return $config['SETTINGS'];
    }

    function getFrontSettings() {
        global $db;
        global $tables;

        $set = $db->GetOne("SELECT OPTIONS FROM `{$tables['widget_activated']['name']}` WHERE `ID` = " . $db->qstr($this->id));
        $set = unserialize($set);
        return $set;
    }

    function saveSettings($set) {
        global $db;
        global $tables;
        $settings = array();

        foreach ($set as $key=>$setting) {
            if (is_array($setting)) {
                $settings[$setting['IDENTIFIER']] = $setting['SETTING_VALUE'];
            } else {
                $settings[$key] = $setting;
            }
        }

        try {
            $db->Execute("UPDATE `{$tables['widget_activated']['name']}` SET OPTIONS = " . $db->qstr(serialize($settings)) . " WHERE ID = " . $this->id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function activate() {
        global $db;
        global $tables;

        if ($db->Execute("UPDATE `{$tables['widget_activated']['name']}` SET ACTIVE = 1 WHERE `ID` = " . $db->qstr($this->id))) {
            return true;
        } else {
            return false;
        }
    }

    function deactivate() {
        global $db;
        global $tables;
        if ($db->Execute("UPDATE `{$tables['widget_activated']['name']}` SET ACTIVE = 0 WHERE `ID` = " . $db->qstr($this->id))) {
            return true;
        } else {
            return false;
        }
    }

    protected function _getTemplate() {

        if (!empty($_SESSION['user_language'])) {
            $language = $_SESSION['user_language'];
        } elseif (defined('LANGUAGE')) {
            $language = LANGUAGE;
            if (empty($language)) {
                $language = 'en';
            }
        } else {
            $language = 'en';
        }



        $is_mobile_user = is_mobile_user();

        $t = new IntSmarty($language);

        $t->caching = 0;
        $t->compile_id = $t->compile_id . $this->name; //compile_id is already used in intsmarty for language settings
        if (file_exists(TEMPLATE_PATH . '/widgets/' . $this->name)) {
            $t->template_dir = TEMPLATE_PATH . '/widgets/' . $this->name;
        } else {
            $t->template_dir = INSTALL_PATH . 'application/widgets/' . $this->name . '/templates';
        }
        //die(INSTALL_PATH.'application/widgets/'.$this->name.'/templates');

        if (!$is_mobile_user) {
            $t->compile_dir = INSTALL_PATH . 'temp/templates';
            $t->cache_dir = INSTALL_PATH . 'temp/cache';
        } else {
            $t->compile_dir = INSTALL_PATH . 'temp/templates_mobile';
            $t->cache_dir = INSTALL_PATH . 'temp/cache_mobile';
        }

        $router = Phpld_App::getInstance()->getRouter();
        if (!is_null($router)) {
            $t->assign('currentAction', Phpld_App::getInstance()->getRouter()->getAction());
            $t->assign('currentController', Phpld_App::getInstance()->getRouter()->getController());
        }

        return $t;
    }

    abstract public function getContent();

    public function render()
    {
        $this->tpl->assign('widget', $this);
        $this->tpl->assign('widgetSettings', $this->getFrontSettings());

        $content = $this->getContent();
        if (DEBUG) {
            $content = $this->name.$content.get_execution_time();
        }
        return $content;
    }


    public function addJavascript($href)
    {
        $this->editorJavascripts[] = $href;
    }
    public function addJavascriptCode($code)
    {
        $this->editorJsCode[] = $code;
    }

    public function addStylesheet($href)
    {
        $this->editorStyles[] = $href;
    }

    public function renderEditorStylesAndJavascripts()
    {
        global $tpl;
        $javascripts = '';
        $styles = '';
        $jsCodes = '';
        foreach ($this->editorJsCode as $code) {
            $jsCodes .= '<script type="text/javascript">'.$code.'</script>';
        }
        foreach ($this->editorJavascripts as $href) {
            $javascripts .= '<script type="text/javascript" src="'.$href.'"></script>';
        }
        foreach ($this->editorStyles as $href) {
            $styles .= '<link rel="stylesheet" type="text/css" href="'.$href.'" />';
        }

        $tpl->assign('adminJsCode', $jsCodes);
        $tpl->assign('adminJs', $javascripts);
        $tpl->assign('adminStyles', $styles);
    }
}