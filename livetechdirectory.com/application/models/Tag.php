<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
class Model_Tag extends Phpld_Model_Abstract
{
    protected $_entityClass = 'Model_Tag_Entity';

    protected $_modelTable = 'tags';

    public function addTag($title, $status = Model_Tag_Entity::STATUS_ACTIVE)
    {
        if (is_numeric($title)) {
            // Existing tag
            $idTag = $title;
        } else {
            db_replace($this->_modelTable, array('TITLE'=>$title, 'STATUS'=>$status), 'ID');
            $idTag = $this->_db->Insert_ID();
            db_replace($this->_modelTable, array('CACHE_URL'=>$this->seoUrl($title, $idTag), 'ID'=>$idTag), 'ID');

        }
        return $idTag;
    }

    /**
     * @param bool $includingBanned
     * @param null $where
     * @param null $order
     * @param int $offset
     * @param null $count
     * @param string $columns
     * @param null $join
     * @return Phpld_Model_Collection
     */
    public function getAllTags($includingBanned = false, $where = null, $order = null, $offset = 0, $count = null, $columns = '*', $join = null)
    {
        $whereConditions = array();
        $query = "SELECT SQL_CALC_FOUND_ROWS {$columns}
        FROM `{$this->_tables['tags']['name']}`";
        if (!$includingBanned) {
            $whereConditions[] = 'STATUS <> '.Model_Tag_Entity::STATUS_BANNED;
        }
        if (!is_null($where)) {
            $whereConditions[] = $where;
        }
        if (!empty($whereConditions)) {
            $query .= " WHERE ".implode(' AND ', $whereConditions);
        }

        if (!is_null($join)) {
            $query .= $join;
        }

        if (!is_null($order)) {
            $query .= " ORDER BY ".$order;
        }

        if (!is_null($count)) {
            $query .= " LIMIT {$offset}, {$count}";
        }

        $links = $this->_db->getAll($query);
        $count = $this->_db->getOne("SELECT FOUND_ROWS() as count");

        $linksCollection = new Phpld_Model_Collection('Model_Tag_Entity', $count);
        $linksCollection->setElements($links);
        return $linksCollection;
    }

    public function seoUrl($title, $id = null)
    {
        $url = makeUrlAlias($title);
        $url = seo_rewrite($url);
        if (!is_null($id)) {
            $url .= '-'.$id;
        }
        return $url;
    }

    /**
     * @param null $uri
     * @return Model_Tag_Entity
     */
    function getTagByUri($uri = null) {
        if (is_null($uri)) {
            $uri = substr($_SERVER['REQUEST_URI'], strlen(DOC_ROOT)+1);
            $uri= urldecode($uri);
        }

        $tag = $this->_db->CacheGetRow("SELECT * FROM `{$this->_tables['tags']['name']}` WHERE `CACHE_URL` = " . $this->_db->qstr($uri));
        if (empty($tag)) {
            return null;
        } else {
            return $this->entity($tag);
        }
    }


    /**
     * @param $idCategory
     * @param bool $featured
     * @param int $offset
     * @param null $count
     * @param null $sort
     * @return Phpld_Model_Collection
     */
    public function getLinks($idTag, $where = false, $offset = 0, $count = null, $sort = null) {
        $sort_cols = array('P' => 'PAGERANK', 'H' => 'HITS', 'A' => 'TITLE', 'D' => 'DATE_ADDED', 'F' => $this->_tables['link']['name'].'.FEATURED DESC, '.$this->_tables['link_type']['name'].'.FEATURED');
        $sort_ord = array('P' => 'DESC', 'H' => 'DESC', 'A' => 'ASC', 'D' => 'DESC', 'F' => 'DESC');

        if (is_null($sort)) {
            $sort = DEFAULT_SORT;
        }

        if (!is_null($count)) {
            $limit = 'LIMIT ' . $offset . ', ' . $count;
        } else {
            $limit = null;
        }

        // Query for not exprired links
        $expire_where = "AND (`EXPIRY_DATE` >= NOW() OR `EXPIRY_DATE` IS NULL)";

        $email_conf = '';
        if (EMAIL_CONFIRMATION == 1) {
            $email_conf = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
        }


        $query = "SELECT SQL_CALC_FOUND_ROWS *, PLD_LINK.DESCRIPTION as DESCRIPTION, PLD_LINK.ID as ID
            FROM `{$this->_tables['link']['name']}`
            INNER JOIN PLD_LINK_TYPE ON PLD_LINK_TYPE.ID = PLD_LINK.LINK_TYPE
            INNER JOIN `{$this->_tables['tags_links']['name']}` ON `{$this->_tables['tags_links']['name']}`.LINK_ID = {$this->_tables['link']['name']}.ID
            WHERE `{$this->_tables['tags_links']['name']}`.TAG_ID = $idTag AND  (PLD_LINK.`STATUS` = '2') {$email_conf} {$expire_where} {$where}
            ORDER BY {$sort_cols[$sort]} {$sort_ord[$sort]} {$limit}";
//        die($query);
        $links = $this->_db->getAll($query);

        $count = $this->_db->getOne("SELECT FOUND_ROWS() as count");

        $collection = new Phpld_Model_Collection('Model_Link_Entity', $count);
        $collection->setElements($links);
        return $collection;
    }
}