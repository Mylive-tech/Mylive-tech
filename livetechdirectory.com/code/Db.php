<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 class Phpld_Db {

    protected static $_instance;

    protected $_db = null;
    protected $_tables = null;

    /**
     * @static
     * @return Phpld_Db
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            throw new Exception('Database not instantiated yet');
        }
        return self::$_instance;
    }

    public static function factory($db, $tables)
    {
        self::$_instance = new self($db, $tables);
    }

    private function __construct($db, $tables)
    {
        $this->_db = $db;
        $this->_tables = $tables;
    }

    /**
     * @return ADODB_mysql
     */
    public function getAdapter()
    {
        return $this->_db;
    }

    public function getTables()
    {
        return $this->_tables;
    }
}