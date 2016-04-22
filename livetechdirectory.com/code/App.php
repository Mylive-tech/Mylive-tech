<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 class Phpld_App {

    /**
     * @var $path
     */
    protected static $_instance = null;

    /**
     * @var Phpld_Router_Abstract
     */
    protected $_router = null;

    /**
     * @var Phpld_Controller_Abstract
     */
    protected $_controller = null;

    protected $_config = null;

    protected $_db = null;

    /**
     * @static
     * @return Phpld_App
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Phpld_App();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->_config = read_config(Phpld_Db::getInstance()->getAdapter());
    }

    public function getParam($param)
    {
        if (!isset($this->_config[$param])) {
            return null;
        }
        return $this->_config[$param];
    }

    public function dispatch()
    {
        // Set Default Router
        $this->_resolveRouter();
        $this->_resolveController();
    }

    protected function _resolveRouter()
    {
        $router = new Phpld_Router_Request();
        $router->route();
        $this->_router = $router;
    }

    /**
     * @return Phpld_Router_Abstract
     */
    public function getRouter()
    {
        return $this->_router;
    }

    protected function _resolveController()
    {

        $controllerName = $this->_router->getController();

        $controllerName = ucwords($controllerName).'Controller';
        $controller = new $controllerName();
        $controller->init();
        $this->_controller = $controller;
    }

    protected function _setErrorController()
    {
        $router = new Phpld_Router_Request();
        // try to find such category
        $categoryModel = new Model_Category();

        $category = $categoryModel->getCategoryByUri();
        if (!is_null($category)) {
            $router->setAction('index');
            $router->setController('category');
        } else {
            header("HTTP/1.0 404 Not Found");
            $router->setAction('notfound');
            $router->setController('error');
        }
        $this->_router = $router;
        $this->_resolveController();
    }

    public function run()
    {
        try {
            $this->dispatch();

            $actionName = $this->_router->getAction();
            $actionName .= 'Action';
            $this->_controller->_preDispatch();
            $this->_controller->$actionName();
            $content = $this->_controller->render();
            $this->_controller->_postDispatch();
        } catch (Exception $e) {
			if(DEBUG)
			{
				echo "<pre>";
				print_r($e);
				echo "</pre>";exit();
			}
            $this->_setErrorController();
            $actionName = $this->_router->getAction();
            $actionName .= 'Action';
            $this->_controller->_preDispatch();
            $this->_controller->$actionName();
            $content = $this->_controller->render();
            $this->_controller->_postDispatch();
        }

        echo $content;
    }
}