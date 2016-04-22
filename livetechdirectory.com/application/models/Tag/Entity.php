<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Model_Tag_Entity extends Phpld_Model_Entity
{
    const STATUS_BANNED  = 0;
    const STATUS_PENDING = 1;
    const STATUS_ACTIVE  = 2;

    protected $_modelClass = 'Model_Tag';

    protected $_urlPrefix = 'tag';


    public function getUrl($prefix = true)
    {
        $href = FRONT_DOC_ROOT.'/'.($prefix == true ? $this->_urlPrefix : '').'/'.$this['CACHE_URL'];
        return $href;
    }

    public function getLinks($where = false, $offset = 0, $count = null, $sort = null)
    {
        return $this->_model->getLinks($this['ID'], $where, $offset, $count, $sort);
    }

}