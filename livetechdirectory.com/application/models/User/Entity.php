<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Model_User_Entity extends Phpld_Model_Entity
{
    public function getUrl()
    {
        $href = DOC_ROOT.'/user/'.$this['ID'];
        return $href;
    }

    public function __toString()
    {
        if ($this['ANONYMOUS'] && ALLOW_ANONYMOUS) {
            return 'Anonymous';
        } else {
            return '<a href="'.$this->getUrl().'">'.$this['NAME'].'</a>';
        }
    }
}