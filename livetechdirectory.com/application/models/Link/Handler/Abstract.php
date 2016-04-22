<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
abstract class Model_Link_Handler_Abstract {

    /**
     * @var Model_Link_Entity
     */
    protected $_entity;

    public function __construct(Model_Link_Entity $entity)
    {
        $this->_entity = $entity;
    }

    abstract public function _assignPlaceholders($purpose, $style="list");
}