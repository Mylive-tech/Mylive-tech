<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 class Phpld_Sorter
{
    protected $_field = null;

    protected $_order = null;

    protected $_columns = array ('P' => 'PAGERANK', 'H' => 'HITS', 'A' => 'TITLE', 'D' => 'DATE_ADDED');

    protected $_orders = array ('P' => 'DESC'    , 'H' => 'DESC', 'A' => 'ASC'  , 'D' => 'DESC'  );

    protected function _resolveField()
    {
        if (isset($_REQUEST['s'])) {
            $sort = $_REQUEST['s'];
        } else {
            $sort = DEFAULT_SORT;
        }
        return $this->_columns[$sort];
    }

    protected function _resolveOrder()
    {
        if (isset($_REQUEST['s'])) {
            $sort = $_REQUEST['s'];
        } else {
            $sort = DEFAULT_SORT;
        }
        return $this->_orders[$sort];
    }

    public function setField($val)
    {
        $this->_field = $val;
    }

    public function setOrder($val)
    {
        $this->_order = $val;
    }

    public function setColumns($val)
    {
        $this->_columns = $val;
    }

    public function setOrders($val)
    {
        $this->_orders = $val;
    }

    public function getOrder()
    {
        $this->_field = $this->_resolveField();
        $this->_order = $this->_resolveOrder();

        return $this->_field.' '.$this->_order;
    }

    public function __toString()
    {
        return $this->getOrder();
    }
}