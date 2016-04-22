<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Phpld_Controller_Action extends Phpld_Controller_Abstract{

    public function render()
    {
        $this->view->assign('currentAction', $this->_router->getAction());
        $this->view->assign('currentController', $this->_router->getController());

        $layoutType = Phpld_Layout::getCurrent();

        $this->view->assign('titleheading', $layoutType->getTitleHeading());



        $viewScript = $this->getViewScript();
        $content = $this->view->fetch('views/'.$viewScript);
        $this->view->assign('phpldApp', Phpld_App::getInstance());        
        
        
        
        $layoutType->setWidgths();

        if ($layoutType->isTemplateOptionEnabled('background')) {
            $this->view->assign('background_pattern', $layoutType->getBackgroundPattern());
            $this->view->assign('background_color', $layoutType->getBackgroundColor());

        }

        if ($layoutType->isTemplateOptionEnabled('fonts')) {
            $this->view->assign('header_font', $layoutType->getHeaderFont());
            $this->view->assign('content_font', $layoutType->getContentFont());
            $this->view->assign('site_name_font', $layoutType->getSiteNameFont());
            $this->view->assign('font_faces', $layoutType->getFontFaces());
        }

        $this->layout->assign('logo_styles', $layoutType->getLogoStyles());
        $this->layout->assign('logo_width', $layoutType->getLogoWidth());

        $this->layout->assign('phpldThemeStyles', $this->view->fetch('views/_shared/_placeholders/themeStyles.tpl'));
	
	

        $content = $layoutType->getLayout($content);

	
	$this->layout->assign('widget_zones', get_widget_zones());
	$this->layout->assign('widget_list', $layoutType->getWidgets());
	$layout_settings = $layoutType->getLayoutSettings();
	$this->layout->assign('sidebar1', $layout_settings["sidebar"][0]);
	$this->layout->assign('sidebar2', $layout_settings["sidebar"][1]);
	$this->layout->assign('widgetheading', $layout_settings["widgetheading"]['selected']);
	$this->layout->assign('layout_type', $layout_settings["layout"]['selected']);

	
	Phpld_View::assignStylesAndScripts();
        $this->layout->assign('color', $layoutType->getColor());
        $this->layout->assign('content', $content);


        return $this->layout->fetch('layouts/'.$this->_layoutName.'.'.$this->_viewSuffix);
    }

    protected function isPost()
    {
        return !empty($_POST);
    }

    protected function getParam($var, $default = null)
    {
        if (isset($_REQUEST[$var])) {
            return $_REQUEST[$var];
        } else {
            return $default;
        }
    }
}