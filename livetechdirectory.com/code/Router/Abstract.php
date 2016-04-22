<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
abstract class Phpld_Router_Abstract
{
    protected $_controller;

    protected $_action;

    public function route()
    {
        $this->_resolveParams();
    }
    /**
     * @abstract
     *
     */
    abstract protected function _resolveParams();

    public function getController()
    {
        return $this->_controller;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function setController($value)
    {
        $this->_controller = $value;
    }

    public function setAction($value)
    {
        $this->_action = $value;
    }
}