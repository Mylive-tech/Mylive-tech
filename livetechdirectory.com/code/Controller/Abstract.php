<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
abstract class Phpld_Controller_Abstract
{
    /**
     * @var Smarty
     */
    protected $view;

    /**
     * @var Smarty
     */
    protected $layout = null;
    protected $_layoutName = 'default';

    /**
     * @var Phpld_Router_Abstract
     */
    protected $_router;

    protected $_viewSuffix = 'tpl';

    abstract public function render();

    public function __construct()
    {
        $this->_router = Phpld_App::getInstance()->getRouter();
        $this->_initView();
        $this->_initLayout();
    }
    /**
     * Get viw script file path
     */
    public function getViewScript()
    {
        $action = $this->_router->getAction();
        $controller = $this->_router->getController();

        $viewScript = $controller.DIRECTORY_SEPARATOR.$action.'.'.$this->_viewSuffix;
        return $viewScript;
    }

    protected function _initView()
    {
        $this->view = Phpld_View::getView();
    }

    protected function _initLayout()
    {
        $this->layout = Phpld_View::getLayout();
    }

    public function _preDispatch(){

    }

    public function _postDispatch(){

    }

    public function init(){

    }
}