<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
class Model_Page extends Phpld_Model_Abstract
{
    protected $_entityClass = 'Model_Page_Entity';

    protected $_modelTable = 'page';


    /**
     * Resolves $query parameter type and tries to load listing by ID or CACHE_URL fields
     *
     * @param  string $query
     * @return mixed
     */
    public function getPage($query)
    {
        if (is_numeric($query)) {
            return $this->getPageById($query);
        } else {
            return $this->getPageByUrl($query);
        }
    }

    public function getPageByUrl($url)
    {
        $query = 'SELECT *
        FROM '.$this->_tables['page']['name'].'
        WHERE '.$this->_tables['page']['name'].'.SEO_NAME = "'.$url.'"';

        $row = $this->_db->getRow($query);
        return $this->entity($row);
    }

    public function getPageById($idLink)
    {
        $query = 'SELECT *
        FROM '.$this->_tables['page']['name'].'
        WHERE '.$this->_tables['page']['name'].'.ID = '.$idLink;

        $row = $this->_db->getRow($query);
        return $this->entity($row);
    }
//
//    public function getPages($where, $order = null, $offset = 0, $count = null, $columns = '*', $join = null)
//    {
//        $query = "SELECT SQL_CALC_FOUND_ROWS {$columns}, PLD_LINK.ID as ID, PLD_LINK.DESCRIPTION AS DESCRIPTION
//        FROM `{$this->_tables['link']['name']}`";
//        $query .= " INNER JOIN PLD_LINK_TYPE ON PLD_LINK_TYPE.ID = PLD_LINK.LINK_TYPE";
//        $query .= " WHERE {$where}";
//        if (!is_null($join)) {
//            $query .= $join;
//        }
//
//        if (!is_null($order)) {
//            $query .= " ORDER BY ".$order;
//        }
//
//        if (!is_null($count)) {
//            $query .= " LIMIT {$offset}, {$count}";
//        }
//
//        $links = $this->_db->getAll($query);
//        $count = $this->_db->getOne("SELECT FOUND_ROWS() as count");
//
//        $linksCollection = new Phpld_Model_Collection('Model_Link_Entity', $count);
//        $linksCollection->setElements($links);
//        return $linksCollection;
//    }

    public function seoUrl($title, $id = null)
    {
        $url = makeUrlAlias($title);
        $url = seo_rewrite($url);
        if (!is_null($id)) {
            $url .= '-'.$id;
        }
        return $url;
    }
}