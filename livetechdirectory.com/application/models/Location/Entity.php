<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Model_Location_Entity extends Phpld_Model_Entity
{
    protected $_urlPrefix = 'location';

    public function getUrl($prefix = true)
    {
        $href = ($prefix ? FRONT_DOC_ROOT : '').'/'.$this->_urlPrefix.'/'.$this['CITY'].','.$this['STATE'];
        return $href;
    }
}