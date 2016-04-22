<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
abstract class Phpld_Model_Abstract
{
    /**
     * @var ADODB_mysq
     */
    protected $_db;

    protected $_entityClass = 'Phpld_Model_Entity';

    protected $_modelTable = null;

    /**
     * @var array
     */
    protected $_tables;


    public function __construct()
    {
        $this->_db = Phpld_Db::getInstance()->getAdapter();
        $this->_tables = Phpld_Db::getInstance()->getTables();
    }

    public function entity($data)
    {
        $class = $this->_entityClass;

        if (empty($data)) {
            return null;
        }
        $entity = new $class($data, $this);
        return $entity;
    }

    public function update($data, $where)
    {
        return $this->_db->AutoExecute($this->_tables[$this->_modelTable]['name'], $data, 'UPDATE', $where);
    }

    public function insert($data)
    {
        return $this->_db->AutoExecute($this->_tables[$this->_modelTable]['name'], $data, 'INSERT');
    }
}