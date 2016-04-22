<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
class Model_Link extends Phpld_Model_Abstract
{
    protected $_entityClass = 'Model_Link_Entity';

    protected $_modelTable = 'link';

    /**
     * Resolves $query parameter type and tries to load listing by ID or CACHE_URL fields
     *
     * @param  string$query
     * @return mixed
     */
    public function getLink($query)
    {
        if (is_numeric($query)) {
            $link = $this->getLinkById($query);
        } else {
            $link = $this->getLinkByUrl($query);
        }
	
	
	if ($link['OWNER_ID'] && ALLOW_AUTHOR_INFO == 1) {
	    
		$data = $this->_db->GetRow("SELECT * FROM `".$this->_tables['user']['name']."` WHERE `ACTIVE` = '1' AND `ID` = ".$link['OWNER_ID']);
		
		$link['ANONYMOUS']    = $data['ANONYMOUS'];
		if ($link['ANONYMOUS'] == 0 || ALLOW_ANONYMOUS == 0) {
			$link['NAME']     = "<a href=\"".DOC_ROOT."/";
			if (ENABLE_REWRITE) {
				$link['NAME']  .= "authors/author-".$link['OWNER_ID'].".html";
			} else {
				$link['NAME']  .= "author.php?id=".$link['OWNER_ID'];
			}
			if(!empty($data['NAME']))
			    $link['NAME']  .= "\" title=\"".htmlspecialchars($data['NAME'])."\">".htmlspecialchars($data['NAME'])."</a>";
			else
			    $link['NAME']  .= "\" title=\"".htmlspecialchars($data['LOGIN'])."\">".htmlspecialchars($data['LOGIN'])."</a>";
				$link['WEBSITE']    = $data['WEBSITE'];
				$link['INFO']       = $data['INFO'];
			} else {
				$link['NAME']       = "Anonymous";
				$link['ANONYMOUS']  = 1;
			}
	}
	
	return $link;
    
	
    }

    public function getLinkByUrl($url)
    {
        $query = 'SELECT *, PLD_LINK.ID as ID, PLD_LINK.DESCRIPTION AS DESCRIPTION
        FROM '.$this->_tables['link']['name'].'
        INNER JOIN PLD_LINK_TYPE ON PLD_LINK_TYPE.ID = PLD_LINK.LINK_TYPE
        WHERE '.$this->_tables['link']['name'].'.CACHE_URL = "'.$url.'"';

        $row = $this->_db->getRow($query);
        return $this->entity($row);
    }

    public function getLinkById($idLink)
    {
        $query = 'SELECT *, PLD_LINK.ID as ID, PLD_LINK.DESCRIPTION AS DESCRIPTION
        FROM '.$this->_tables['link']['name'].'
        INNER JOIN PLD_LINK_TYPE ON PLD_LINK_TYPE.ID = PLD_LINK.LINK_TYPE
        WHERE '.$this->_tables['link']['name'].'.ID = '.$idLink;

        $row = $this->_db->getRow($query);
        return $this->entity($row);
    }

    /**
     * @param $where
     * @param null $order
     * @param int $offset
     * @param null $count
     * @param string $columns
     * @param null $join
     * @return Phpld_Model_Collection
     */
    public function getLinks($where, $order = null, $offset = 0, $count = null, $columns = '*', $join = null)
    {
        $query = "SELECT SQL_CALC_FOUND_ROWS {$columns}, PLD_LINK.ID as ID, PLD_LINK.DESCRIPTION AS DESCRIPTION
        FROM `{$this->_tables['link']['name']}`";
        $query .= " INNER JOIN PLD_LINK_TYPE ON PLD_LINK_TYPE.ID = PLD_LINK.LINK_TYPE";
        $query .= " WHERE {$where}";
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

        $linksCollection = new Phpld_Model_Collection('Model_Link_Entity', $count);
        $linksCollection->setElements($links);
        return $linksCollection;
    }

    public function getSubmitItems($idType)
    {
        $query = "
            SELECT *
            FROM `{$this->_tables['submit_item']['name']}` AS si
                LEFT JOIN `{$this->_tables['submit_item_status']['name']}` AS sis
                ON (si.ID = sis.ITEM_ID)
            WHERE sis.`STATUS` = '2'
                AND sis.`IS_DETAIL` = '1'
                AND sis.`LINK_TYPE_ID` = ".$this->_db->qstr($idType)."
            ORDER BY si.`ORDER_ID` ASC
        ";

        $submit_items = $this->_db->GetAll($query);

        return $submit_items;
    }

    public function getTags($idLink)
    {
        $query = 'SELECT '.$this->_tables['tags']['name'].'.* FROM '.$this->_tables['tags']['name'].'
        INNER JOIN '.$this->_tables['tags_links']['name'].' ON '.$this->_tables['tags_links']['name'].'.TAG_ID = '.$this->_tables['tags']['name'].'.ID
        WHERE '.$this->_tables['tags_links']['name'].'.LINK_ID = '.$idLink.' AND STATUS = '.Model_Tag_Entity::STATUS_ACTIVE;
//        die($query);
        $tags = $this->_db->getAll($query);

        $linksCollection = new Phpld_Model_Collection('Model_Tag_Entity');
        $linksCollection->setElements($tags);
        return $linksCollection;
    }

    public function getCategories($idLink)
    {
     
      $category_id = $this->_db->GetOne("SELECT `CATEGORY_ID`  FROM `{$this->_tables['link']['name']}` WHERE `ID` = ".$this->_db->qstr($idLink));
      
      $categs_list = array();
      $add_categs = $this->_db->GetAll("SELECT `CATEGORY_ID`  FROM `{$this->_tables['additional_category']['name']}` WHERE `LINK_ID` = ".$this->_db->qstr($idLink));
      foreach ($add_categs as $add_categ)
	    $categs_list[] = $add_categ['CATEGORY_ID'];
	
	
	array_push($categs_list,$category_id);
	
	$query = 'SELECT DISTINCT  pc.* FROM `'.$this->_tables['category']['name'].'` pc
           WHERE 1 AND ID IN('.implode(',',$categs_list).') ';
	
	
        /*$query = 'SELECT DISTINCT  pc.* FROM `'.$this->_tables['category']['name'].'` pc
            INNER JOIN `'.$this->_tables['link']['name'].'` pl ON pl.ID = '.$idLink.'
            LEFT JOIN `'.$this->_tables['additional_category']['name'].'` plc ON  pc.ID = plc.CATEGORY_ID
            LEFT JOIN `'.$this->_tables['category']['name'].'` pl2 ON pl2.ID = plc.CATEGORY_ID
            WHERE pc.ID = pl.CATEGORY_ID OR plc.LINK_ID = '.$idLink;*/
        $collection = new Phpld_Model_Collection(Model_Category_Entity);
        $collection->setElements($this->_db->getAll($query));
       return $collection;
    }


    public function getMainCategory($idLink)
    {
        $query = 'SELECT pc.* FROM PLD_CATEGORY pc
            INNER JOIN PLD_LINK pl ON pl.CATEGORY_ID = pc.ID
            WHERE pl.ID = '.$idLink;
        $categoryModel = new Model_Category();
        $entity = $categoryModel->entity($this->_db->getRow($query));
        return $entity;
    }

    public function seoUrl($data, $id = null)
    {
        $url = makeUrlAlias($data['TITLE']);
        $url = seo_rewrite($url);
        if (!is_null($id)) {
            $url .= '-'.$id;
        }
        return $url;
    }

    public function getByLocation($state = null, $city = null, $idCategory = null)
    {
        if (is_null($state) && is_null($city)) {
            return null;
        }
        $where = array();
        if (!is_null($state)) {
            $where[] = 'STATE = "'.$state.'"';
        }
        if (!is_null($city)) {
            $where[] = 'CITY = "'.$city.'"';
        }
        if (!is_null($idCategory)) {
            $where[] = 'CATEGORY_ID = "'.$idCategory.'"';
        }

        return $this->getLinks(implode(' AND ', $where));
    }

public function getSeoLinkType()
    {
        $query = 'SELECT * FROM PLD_CONFIG WHERE ID="SEO_SHOW_LINK_TYPE"';
        $result = $this->_db->getAll($query);
        if(!empty($result)){
               $data = $result[0]['VALUE'];
        }
        
        return $data;
    }
    
    public function getSeoUrlExtension()
    {
        $query = 'SELECT * FROM PLD_CONFIG WHERE ID="SEO_URL_EXTENSION"';
        $result = $this->_db->getAll($query);
        if(!empty($result)){
               $data = $result[0]['VALUE'];
        }
        
        return $data;
    }
    
    public function getSeoCategoryName()
    {
        $query = 'SELECT * FROM PLD_CONFIG WHERE ID="SEO_CATEGORY_NAME"';
        $result = $this->_db->getAll($query);
        if(!empty($result)){
               $data = $result[0]['VALUE'];
        }
        
        return $data;
    }
    
    public function getLinkTypeById($idLink)
    {
        $query = 'SELECT PLT.NAME
        FROM '.$this->_tables['link']['name'].'
        INNER JOIN PLD_LINK_TYPE AS PLT ON PLT.ID = PLD_LINK.LINK_TYPE
        WHERE '.$this->_tables['link']['name'].'.ID = '.$idLink;

        $row = $this->_db->getRow($query);
        if(!empty($row)){
               $data = $row['NAME'];
        }
        
        return $data;
    }

}