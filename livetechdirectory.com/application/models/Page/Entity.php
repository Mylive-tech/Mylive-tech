<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Model_Page_Entity extends Phpld_Model_Entity
{
    protected $_urlPrefix = 'page';

    public function getUrl($prefix = true)
    {
        $href = DOC_ROOT.'/'.($prefix == true ? $this->_urlPrefix.'/' : '').$this['SEO_NAME'];
        return $href;
    }
}