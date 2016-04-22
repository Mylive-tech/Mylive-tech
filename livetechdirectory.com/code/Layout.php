<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 

 
 
 
 
	 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of templateLayout
 *
 * @author User
 */
class Phpld_Layout {

    //put your code here
    protected $optionForLayout;
    protected $widgets;
    protected $settings;
    protected $xml = null;
    protected $zone;

    const ZONE_LEFT = 'LEFT_COLUMN';
    const ZONE_RIGHT = 'RIGHT_COLUMN';

    const COLUMNS_LEFT = 3;
    const COLUMNS_RIGHT = 4;

    const LAYOUT_1_COLUMN = 1;
    const LAYOUT_2_COLUMN_LEFT = 2;
    const LAYOUT_2_COLUMN_RIGHT = 3;
    const LAYOUT_3_COLUMN_CENTER = 4;
    const LAYOUT_3_COLUMN_RIGHT = 5;
    const LAYOUT_3_COLUMN_LEFT = 6;
    const LAYOUT_CUSTOM = "custom";
    const MOBILE_LAYOUT = "mobile";

    /**
     * @var Phpld_Layout
     */
    private static $_instance = null;

    public static function getCurrent()
    {
        if (is_null(self::$_instance)) {
	    $is_mobile_user = is_mobile_user();
	    
            self::$_instance = new self();
            if (!$is_mobile_user) {
             self::$_instance->readLayout(TEMPLATE_PATH);
	    } else {
             self::$_instance->readLayout(MOBILE_TEMPLATE_PATH); 
	    }
	   
        }
        return self::$_instance;
    }

    public function __construct() {
        
    }

    public function setWidgths() {
	$is_mobile_user = is_mobile_user();
	$cetralZone = $this->_resolveCentralZone();
	if (!$is_mobile_user) 
	    $widgets = $this->getWidgets();
	else
	    $widgets = $this->getMobileWidgets(); 
	
	$this->widgets= $widgets;
	
//        $this->widgets['LEFT_COLUMN'] = $widgets['LEFT_COLUMN'];
//        $this->widgets['RIGHT_COLUMN'] = $widgets['RIGHT_COLUMN'];
//        $this->widgetsCentral = $widgets[$cetralZone];
        $this->zone = $cetralZone;
    }

    /**
     * Get full page layout content
     *
     * @param $pageContent Page content, rendered by action
     * @return string
     */
    public function getLayout($pageContent) {

        $layouts = array(self::ZONE_LEFT => false, self::ZONE_RIGHT => false);
        $side1 = "";
        $side2 = "";
        $main = "";

        //var_dump($this->zone);die();
        $this->settings['layout']['selected'] = $this->settings['zone'][$this->zone]['value'];
	
	if($this->settings['zone'][$this->zone]['value'] == 0)
	    $this->settings['layout']['selected'] = LAYOUT_CUSTOM;
	$is_mobile_user = is_mobile_user();
	if ($is_mobile_user) 
	    $this->settings['layout']['selected'] = self::MOBILE_LAYOUT;
	
        $controller = Phpld_App::getInstance()->getRouter()->getController();
        if ($controller == 'submit' and !$this->settings['submit']['selected']) {
            $this->settings['layout']['selected'] = 1;
        }
//        var_dump($this->settings);die();
        switch ($this->settings['layout']['selected']) {
            case self::LAYOUT_1_COLUMN:
                $layouts = array(
                    self::ZONE_LEFT => array(
                        'enabled'=>false
                    ),
                    self::ZONE_RIGHT => array(
                        'enabled'=>false
                    ),
                );
                $main .= 'margin-left:10px;margin-right:10px;';
                break;
            case self::LAYOUT_2_COLUMN_LEFT:
                $layouts = array(
                    self::ZONE_LEFT => array(
                        'enabled'=>true, 'style'=>'width:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'],'class'=>'phpld-col1'
                    ),
                    self::ZONE_RIGHT => array(
                        'enabled'=>false, 'style'=>'width:0;','class'=>'phpld-col2'
                    ),
                );
                if (count($this->widgets['LEFT_COLUMN']) > 0) {
                    $main .= 'margin-right:10px;margin-left:' . ($this->settings['sidebar'][0]['width']) . $this->settings['sidebar'][0]['type'];
                } else {
                    $main .= 'margin-right:10px;margin-left:10px';
                }
                break;
            case self::LAYOUT_2_COLUMN_RIGHT:
                $layouts = array(
                    self::ZONE_LEFT => array(
                        'enabled'=>false, 'style'=>'width:0;','class'=>'phpld-col1'
                    ),
                    self::ZONE_RIGHT => array(
                        'enabled'=>true, 'style'=>'float:right;width:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][1]['type'],'class'=>'phpld-col2'
                    ),
                );

                if (count($this->widgets['RIGHT_COLUMN']) > 0) {
                    $main .= 'margin-left:10px;margin-right:' . ($this->settings['sidebar'][1]['width']) . $this->settings['sidebar'][0]['type'] . ";";
                } else {
                    $main .= 'margin-right:10px;margin-left:10px';
                }
                break;
            case self::LAYOUT_3_COLUMN_CENTER:
                $layouts = array(
                    self::ZONE_LEFT => array(
                        'enabled'=>true, 'style'=>'width:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'],'class'=>'phpld-col1'
                    ),
                    self::ZONE_RIGHT => array(
                        'enabled'=>true, 'style'=>'width:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][1]['type'],'class'=>'phpld-col2'
                    ),
                );

                if (count($this->widgets['LEFT_COLUMN']) > 0) {
                    $main .= 'margin-left:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'] . ";";
                } else {
                    $main .= 'margin-left:10px;';
                }
                if (count($this->widgets['RIGHT_COLUMN']) > 0) {
                    $main .= 'margin-right:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][1]['type'] . ";";
                } else {
                    $main .= 'margin-right:10px;';
                }
                break;
            case self::LAYOUT_3_COLUMN_RIGHT:
                $layouts = array(
                    self::ZONE_RIGHT => array(
                        'enabled'=>true, 'style'=>'float:right;','class'=>'phpld-col2'
                    ),
                    self::ZONE_LEFT => array(
                        'enabled'=>true, 'style'=>'float:right;','class'=>'phpld-col1'
                    ),
                );
                $main = '';
                if (count($this->widgets['LEFT_COLUMN']) > 0 and count($this->widgets['RIGHT_COLUMN']) > 0) {
                    $layouts[self::ZONE_LEFT]['style'] .= 'width:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'] . ';';
                    $layouts[self::ZONE_RIGHT]['style'] .= 'width:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][1]['type'] . ";margin-right:0%;";
                    $main .= 'margin-left:10px;margin-right:' . ($this->settings['sidebar'][0]['width'] + $this->settings['sidebar'][1]['width']) . $this->settings['sidebar'][0]['type'] . ";";
                } elseif (count($this->widgets['LEFT_COLUMN']) > 0) {
                    $main .= 'margin-left:10px;margin-right:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'] . ";";
                    $layouts[self::ZONE_LEFT]['style'] .= 'width:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'];
                    $layouts[self::ZONE_RIGHT]['style'] .= 'width:0' . $this->settings['sidebar'][1]['type'] . ";";
                } elseif (count($this->widgets['RIGHT_COLUMN']) > 0) {
                    $main .= 'margin-left:10px;margin-right:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][1]['type'] . ";";
                    $layouts[self::ZONE_LEFT]['style'] .= 'width:0' . $this->settings['sidebar'][0]['type'] . ";";
                    $layouts[self::ZONE_RIGHT]['style'] .= 'width:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][0]['type'];
                }

                break;
            case self::LAYOUT_3_COLUMN_LEFT:
                $layouts = array(
                    self::ZONE_LEFT => array(
                        'enabled'=>true, 'style'=>'float:left;','class'=>'phpld-col1'
                    ),
                    self::ZONE_RIGHT => array(
                        'enabled'=>true, 'style'=>'float:left;','class'=>'phpld-col2'
                    ),
                );

                $main = '';
                if (count($this->widgets['LEFT_COLUMN']) > 0 and count($this->widgets['RIGHT_COLUMN']) > 0) {
                    $layouts[self::ZONE_LEFT]['style'] .= 'width:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'] . ';';
                    $layouts[self::ZONE_RIGHT]['style'] .= 'width:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][1]['type'] . ";margin-right:0%;";
                    $main .= 'margin-right:10px;margin-left:' . ($this->settings['sidebar'][0]['width'] + $this->settings['sidebar'][1]['width']) . $this->settings['sidebar'][0]['type'] . ";";
                } elseif (count($this->widgets['LEFT_COLUMN']) > 0) {
                    $main .= 'margin-right:10px;margin-left:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'] . ";";
                    $layouts[self::ZONE_LEFT]['style'] .= 'width:' . $this->settings['sidebar'][0]['width'] . $this->settings['sidebar'][0]['type'];
                    $layouts[self::ZONE_RIGHT]['style'] .= 'width:0' . $this->settings['sidebar'][1]['type'] . ";";
                } elseif (count($this->widgets['RIGHT_COLUMN']) > 0) {
                    $main .= 'margin-right:10px;margin-left:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][1]['type'] . ";";
                    $layouts[self::ZONE_LEFT]['style'] .= 'width:0' . $this->settings['sidebar'][0]['type'] . ";";
                    $layouts[self::ZONE_RIGHT]['style'] .= 'width:' . $this->settings['sidebar'][1]['width'] . $this->settings['sidebar'][0]['type'];
                }

                break;
	    case self::LAYOUT_CUSTOM :
		return $pageContent;
	    break;
	    case self::MOBILE_LAYOUT :
		$layout = '';
		 if (count($this->widgets[$this->_resolveCentralZone()]) > 0) {
		    foreach ($this->widgets[$this->_resolveCentralZone()] as $widget) {
			// Replace main content placeholder widget with actual content
			if ($widget['NAME'] == 'MainContent') {
			    $widget['CONTENT'] = $pageContent;
			    $widget['NAME'] = '';
			}
			$layout .= $this->getWidgetClass($widget);
			}
		    }
		return $layout;
             break;
        }
        return $this->TemplateContent($pageContent, $layouts, $side1, $side2, $main);
    }

    public function TemplateContent($pageContent, $cols = array(self::ZONE_LEFT => false, self::ZONE_RIGHT => false), $side1 = '', $side2 = '', $main = '') {
        $layout = '';

       foreach ($cols as $zone => $config) {
            if ($config['enabled'] and count($this->widgets[$zone])) {
                $layout .= '<div class="'.$config['class'].'" style="' . $config['style'] . '">';
                $layout .= '<div class="phpld-cbox">';
                //$layout .= $this->tpl->fetch('views/_shared/left_side.tpl');
                foreach ($this->widgets[$zone] as $widget) {
                    $layout .= $this->getWidgetClass($widget);
                }

                $layout .= '</div>';
                $layout .= '</div>';
            } else {
                $this->settings['sidebar'][0]['width'] = 0;
            }
        }
//        if ($cols[self::ZONE_LEFT] and count($this->widgets['LEFT_COLUMN'])) {
//            $layout .= '<div class="phpld-col1" style="' . $side1 . '">';
//            $layout .= '<div class="phpld-cbox">';
//            //$layout .= $this->tpl->fetch('views/_shared/left_side.tpl');
//            foreach ($this->widgets['LEFT_COLUMN'] as $widget) {
//                $layout .= $this->getWidgetClass($widget);
//            }
//
//            $layout .= '</div>';
//            $layout .= '</div>';
//        } else {
//            $this->settings['sidebar'][0]['width'] = 0;
//        }
//        //var_dump($cols['2']);die();
//        if ($cols[self::ZONE_RIGHT] and count($this->widgets['RIGHT_COLUMN']) > 0) {
//            $layout .= '<div class="phpld-col2" style="' . $side2 . '">';
//            $layout .= '<div class="phpld-cbox">';
//            foreach ($this->widgets['RIGHT_COLUMN'] as $widget) {
//                $layout .= $this->getWidgetClass($widget);
//            }
//            //$layout .= $this->tpl->fetch('views/_shared/right_side.tpl');
//            $layout .= '</div>';
//            $layout .= '</div>';
//        } else {
//            $this->settings['sidebar'][1]['width'] = 0;
//        }
        $layout .= '<div class="phpld-col3"  style="' . $main . '">';
        $layout .= '<div class="phpld-cbox">';


        if (count($this->widgets[$this->_resolveCentralZone()]) > 0) {
            foreach ($this->widgets[$this->_resolveCentralZone()] as $widget) {
                // Replace main content placeholde widget with actual content
                if ($widget['NAME'] == 'MainContent') {
                    $widget['CONTENT'] = $pageContent;
                    $widget['NAME'] = '';
                }
                $layout .= $this->getWidgetClass($widget);
            }
        }

        $layout .= '</div>';
        $layout .= '</div>';


        return $layout;
    }

    public function getWidgetClass( $widget) {
        $widgetBox = '';

        if (isset($widget['CONTENT']) and trim($widget['CONTENT']) != '') {
            $view = Phpld_View::getView();
            $view->assign('ID', $widget['ID']);
            $view->assign('SHOW_TITLE', (!isset($widget['SETTINGS']['SHOW_TITLE']) or strtolower($widget['SETTINGS']['SHOW_TITLE']) == 'yes'));
	    $view->assign('DISPLAY_IN_BOX', (!isset($widget['SETTINGS']['DISPLAY_IN_BOX']) or strtolower($widget['SETTINGS']['DISPLAY_IN_BOX']) == 'yes'));
            $view->assign('WIDGET_HEADING', $this->settings['widgetheading']['selected']);
            $view->assign('TITLE', $widget['SETTINGS']['TITLE']);
            $view->assign('CONTENT', $widget['CONTENT']);
	    $view->assign('NAME', $widget['NAME']);		
            $widgetBox = $view->fetch('views/_shared/widget.tpl');
        }
        return $widgetBox;
    }

    public function readLayout($path = null) {
        if (is_null($path)) {
            $path = $this->getDefaultPath();
        } else {
            $path = INSTALL_PATH.$path;
        }

        $this->settings = array();
        $xml = $this->getXML($path);

        if (!$xml) {
            return $this->settings;
        }

        //$this->settings['layout']['selected'] = (int) $xml->layout->field->attributes()->default;
       if(!empty($xml->color))
	    $this->settings['color']['selected'] = (string)$xml->color->field->attributes()->default;
	if($xml->submit->attributes())
	    $this->settings['submit']['selected'] = (int) $xml->submit->attributes()->default;
	if($xml->widgetheading->attributes())
	    $this->settings['widgetheading']['selected'] = (int) $xml->widgetheading->attributes()->default;
	
	if($xml->titleheading->attributes())
	    $this->settings['titleheading']['selected'] = (int) $xml->titleheading->attributes()->default;
	if(!empty($xml->sidebars->sidebar[0]))
	    {
		$this->settings['sidebar'][0]['width'] = (int) $xml->sidebars->sidebar[0]->attributes()->width;
		$this->settings['sidebar'][0]['type'] = $xml->sidebars->sidebar[0]->field->attributes()->default;
	    }
	if(!empty($xml->sidebars->sidebar[1]))
	    {
		$this->settings['sidebar'][1]['width'] = (int) $xml->sidebars->sidebar[1]->attributes()->width;
		$this->settings['sidebar'][1]['type'] = $xml->sidebars->sidebar[1]->field->attributes()->default;
	    }
        $k = 0;

        foreach ($xml->zones->children() as $key => $zone) {
            $name = (string)$xml->zones->zone[$k]->attributes()->name;
            $this->settings['zone'][$name]['name'] = $name;
            $this->settings['zone'][$name]['value'] = (int) $xml->zones->zone[$k]->attributes()->layout;
            $k++;
        }

        return $this->settings;
    }
    
     public function getLayoutSettings($path = null) {
	 return $this->settings;
     }

    protected function _resolveCentralZone() {
        $controller = Phpld_App::getInstance()->getRouter()->getController();
        $action = Phpld_App::getInstance()->getRouter()->getAction();

        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $query = 'SELECT NAME FROM ' . $tables['widget_zones']['name'] . ' WHERE CONTROLLER = "' . $controller . '" AND ACTION = "' . $action . '"';

        $zoneName = $db->getOne($query);
        if (is_null($zoneName)) {
            $zoneName = 'ANY_OTHER_PAGE';
        }
        return $zoneName;
    }

    public function getColor() {
        return $this->xml->color->field->attributes()->default;
    }

    public function getHeaderFont() {
        $font = Phpld_App::getInstance()->getParam('HEADER_FONT');
        $font = $this->resolveFontFace($font);
        return (!is_null($font) ? $font : 'inherit');
    }

    public function getContentFont() {
        $font = Phpld_App::getInstance()->getParam('CONTENT_FONT');
        $font = $this->resolveFontFace($font);
        return (!is_null($font) ? $font : 'inherit');
    }

    public function getSiteNameFont() {
        $font = Phpld_App::getInstance()->getParam('SITENAME_FONT');
        $font = $this->resolveFontFace($font);
        return (!is_null($font) ? $font : 'inherit');
    }

    public function getBackgroundColor() {
        return $this->xml->currentBackground['color'];
    }

    public function getBackgroundPattern() {
        return $this->xml->currentBackground['filename'];
    }

    public function getTitleHeading() {
        return (int) $this->xml->titleheading->attributes()->default;
    }

    public function getWidgets() {

        return get_active_widgets();
    }
    
    public function getMobileWidgets() {
        return get_mobile_widgets($this->_resolveCentralZone(),$this->xml);
    }

    /**
     * @param null $path
     * @return SimpleXMLElement
     */
    public function getXML($path = null) {
        if (is_null($path)) {
            $path = $this->getDefaultPath();
        }

        if (is_null($this->xml)) {
            $xml = $xml = simplexml_load_file($path . "/template.xml");
            $this->xml = $xml;
        }
        return $this->xml;
    }

    public function getAdminMenuItems()
    {
        $menu['logo'] = array('label' => _L('Logo'), 'url' => DOC_ROOT . '/conf_logo.php');
        if ($this->isTemplateOptionEnabled('fonts')) {
            $menu['fonts'] = array('label' => _L('Fonts'), 'url' => DOC_ROOT . '/conf_fonts.php');
        }
        if ($this->isTemplateOptionEnabled('background')) {
            $menu['background'] = array('label' => _L('Background'), 'url' => DOC_ROOT . '/conf_background.php');
        }

        return $menu;
    }

    protected function getDefaultPath()
    {
        return INSTALL_PATH . 'templates/' . TEMPLATE;
    }

    /**
     * Check $feature var. against <options> node in template's template.xml
     * @param $feature
     */
    public function isTemplateOptionEnabled($feature)
    {
        $xml = $this->getXML();

        //Select only enabled options
        $enabled = $xml->xpath('/settings/options/option[@enabled="1"][@name="'.$feature.'"]');
        return !empty($enabled);
    }

    public function resolveFontFace($font)
    {
        $info = pathinfo($font);
        if (!isset($info['extension'])) {
            return $font;
        } else {
            $fonts = unserialize(Phpld_App::getInstance()->getParam('UPLOADED_FONTS'));
            return $fonts[$font];
        }
    }

    public function getFontFaces()
    {
        $fonts = Phpld_App::getInstance()->getParam('UPLOADED_FONTS');
        if (is_null($fonts)) {
            return null;
        }
        $fonts = unserialize($fonts);
        $fontsFace = '';
        foreach ($fonts as $file=>$family) {
            $fontsFace .= '@font-face {
                font-family: "'.$family.'";';
            $info = pathinfo($file);
            switch ($info['extension']) {
                case 'ttf':
                    $fontsFace .= 'src: url("'.FRONT_DOC_ROOT.'/uploads/'.$file.'") format("truetype");';
                    break;
                case 'otf':
                case 'eot':
                    $fontsFace .= 'src: url("'.FRONT_DOC_ROOT.'/uploads/'.$file.'");';
                    break;
                case 'woff':
                    $fontsFace .= 'src: url("'.FRONT_DOC_ROOT.'/uploads/'.$file.'") format("woff");';
                    break;
            }
            $fontsFace .= '}';
        }
        return $fontsFace;
    }

    public function getLogoOptions()
    {
        $options = Phpld_App::getInstance()->getParam('FRONT_LOGO_OPTIONS');
        if (is_null($options)) {
            return null;
        }
        return unserialize($options);
    }

    public function getLogoStyles()
    {
        $options = $this->getLogoOptions();
        if (is_null($options)) {
            return null;
        }
        return 'margin: '.$options['marginTopValue'].'px '.$options['marginRightValue'].'px '.$options['marginBottomValue'].'px '.$options['marginLeftValue'].'px;';
    }
    public function getLogoWidth()
    {
        $options = Phpld_App::getInstance()->getParam('FRONT_LOGO_OPTIONS');
        if (is_null($options)) {
            return null;
        }
        return $options['widthValue'];
    }
}
