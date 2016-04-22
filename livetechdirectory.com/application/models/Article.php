<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
class Model_Article extends Phpld_Model_Abstract
{
    protected $_entityClass = 'Model_Article_Entity';

    protected $_modelTable = 'article';

    public function getArticle($idArticle)
    {
        $row = $this->_db->getRow('SELECT * FROM '.$this->_tables['article']['name'].' WHERE ID = '.$idArticle);
        return $this->entity($row);
    }
}