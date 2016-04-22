<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Phpld_Router_Request extends Phpld_Router_Abstract
{
    protected function _resolveParams(){
        if (isset($_GET['controller'])) {
            $this->_controller = $_GET['controller'];
        } else {
            $this->_controller = 'index';
        }

        if (isset($_GET['action'])) {
            $this->_action = $_GET['action'];
        } else {
            $this->_action = 'index';
        }
        //var_dump('SUBDIR:', $_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'], DOC_ROOT);die();
        if (
            $_SERVER['REQUEST_URI'] !== '/index.php' &&
            !empty($_SERVER['REQUEST_URI']) &&
            $_SERVER['REQUEST_URI'] != DOC_ROOT.'/' &&
            $this->_action == 'index' &&
            $this->_controller == 'index'
        ) {
            throw new Phpld_Exception_NotFound();
        }
    }
}