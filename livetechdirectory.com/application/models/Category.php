<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	

class Model_Category extends Phpld_Model_Abstract {

    protected $_entityClass = 'Model_Category_Entity';
    protected $_modelTable = 'category';

    /**
     * @param null $idCategory
     * @param null $idUser
     * @return Phpld_Model_Collection
     */
    public function getCategories($idCategory = null, $idUser = null) {
        if (is_null($idCategory)) {
            $idCategory = 0;
        }
        $permissions = $this->getPermissions($idUser);
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$this->_tables['category']['name']}` WHERE (`STATUS` = '2' OR {$permissions['permission_cats']} OR {$permissions['permission_cats_parents']}) AND `PARENT_ID` = $idCategory ORDER BY `" . CATEG_FIELD_SORT . "` " . CATEG_FIELD_SORT_ORDER;
        $rs = $this->_db->cacheGetAll($query);
        $count = $this->_db->getOne("SELECT FOUND_ROWS() as count");
        $categsCollection = new Phpld_Model_Collection($this->_entityClass, $count);

        foreach ($rs as $row) {
            // Its "faster" then use subqueries
            $cur_cat_id = ($row['SYMBOLIC'] == 1) ? $row['SYMBOLIC_ID'] : $row['ID'];
            $additional_links = '';
            $add_links = $this->_db->GetAll("SELECT `LINK_ID`  FROM `{$this->_tables['additional_category']['name']}` WHERE `CATEGORY_ID` = " . $this->_db->qstr($cur_cat_id));
            foreach ($add_links as $add_link)
                $additional_links[] = $add_link['LINK_ID'];
            if (is_array($additional_links))
                $additional_links = implode(",", $additional_links);
            $additional_links = !$additional_links ? '0' : $additional_links;
            if ($idCategory == 0 && CATS_PREVIEW > 0) {
                $rs2 = $this->_db->CacheSelectLimit("SELECT * FROM `{$this->_tables['category']['name']}` WHERE (`STATUS` = '2' OR {$permissions['permission_cats']} OR {$permissions['permission_cats_parents']}) AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) AND `PARENT_ID` = " . $this->_db->qstr($row['ID']) . " ORDER BY `" . SUBCATEG_FIELD_SORT . "` " . SUBCATEG_FIELD_SORT_ORDER . ", `TITLE` ", CATS_PREVIEW);
                foreach ($rs2->GetRows() as $subcategory) {
                    $row['SUBCATS'][] = $this->entity($subcategory);
                }
                $rs2->Close();
            }

            if ($row['SYMBOLIC'] == 1) {
                $row['ID'] = $row['SYMBOLIC_ID'];
                $tempcat = $this->_db->CacheGetRow("SELECT * FROM `{$this->_tables['category']['name']}` WHERE `ID` = " . $this->_db->qstr($row['SYMBOLIC_ID']));
                if (empty($row['TITLE']))
                    $row['TITLE'] = $tempcat['TITLE'];

                $row['TITLE'] = '@' . $row['TITLE'];
            }
            $categsCollection->append($row);
        }

        return $categsCollection;
    }

    /**
     * @param $idCategory
     * @return Model_Category_Entity
     */
    public function getCategory($idCategory) {
        $row = $this->_db->getRow('SELECT * FROM ' . $this->_tables['category']['name'] . ' WHERE ID = ' . $this->_db->qstr($idCategory));
        return $this->entity($row);
    }

    function getCategoryByUri($uri = null) {
        if (is_null($uri)) {
            $uri = substr($_SERVER['REQUEST_URI'], strlen(DOC_ROOT)+1);
	        $uri= urldecode($uri);
        }

        if (($pos = strpos($uri, '?')) != false) {
			 $uri= urldecode($uri);
            $uri = substr($uri, 0, $pos);
        }
        $prmissions = $this->getPermissions();
        $category = $this->_db->CacheGetRow("SELECT * FROM `{$this->_tables['category']['name']}` WHERE (`STATUS` = '2' OR {$prmissions['permission_cats']} OR {$prmissions['permission_cats_parents']}) AND `CACHE_URL` = " . $this->_db->qstr($uri));
        if (empty($category)) {
            return null;
        } else {
            return $this->entity($category);
        }
    }

    /**
     * @param null $idUser
     * @return array
     */
    public function getPermissions($idUser = null) {
        // Determine user ID to search for, but only if valid user
        if (!is_null($idUser)) {
            $userLevel = get_user_level($idUser);
            if ($userLevel == 1 || $userLevel == 3) {
                $permission_links_arts = "1 ";
                $permission_cats = "1 ";
                $permission_cats_parents = "1 ";

                $uid = (!empty($_REQUEST['uid']) && preg_match('`^[\d]+$`', $_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0);
            } else {
                $uid = (!empty($_REQUEST['uid']) && preg_match('`^[\d]+$`', $_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0);

                $permissions = Model_CurrentUser::getInstance()->getPermissions();
                $permission_links_arts = "`CATEGORY_ID` IN (";
                $permission_cats = "`ID` IN (";
                $permission_cats_parents = "`PARENT_ID` IN (";
                if (count($permissions) > 0) {
                    for ($i = 0; $i < count($permissions); $i++) {

                        if ($i < count($permissions) - 1) {
                            $permission_links_arts .= "'" . $permissions[$i]['CATEGORY_ID'] . "', ";
                            $permission_cats .= "'" . $permissions[$i]['CATEGORY_ID'] . "', ";
                            $permission_cats_parents .= "'" . $permissions[$i]['CATEGORY_ID'] . "', ";
                        } else {
                            $permission_links_arts .= "'" . $permissions[$i]['CATEGORY_ID'] . "') ";
                            $permission_cats .= "'" . $permissions[$i]['CATEGORY_ID'] . "') ";
                            $permission_cats_parents .= "'" . $permissions[$i]['CATEGORY_ID'] . "') ";
                        }
                    }
                } else {
                    $permission_links_arts = "0 ";
                    $permission_cats = "0 ";
                    $permission_cats_parents = "0 ";
                }
            }

            if ($uid > 0 && $uid == $idUser) {
                $user_where = " AND `OWNER_ID` = " . $this->_db->qstr($uid);
            } else
                unset($uid);
        } else {
            $permission_links_arts = "0 ";
            $permission_cats = "0 ";
            $permission_cats_parents = "0 ";
        }

        return array(
            'permission_links_arts' => $permission_links_arts,
            'permission_cats' => $permission_cats,
            'permission_cats_parents' => $permission_cats_parents,
        );
    }

    /**
     * @param $idCategory
     * @param bool $featured
     * @param int $offset
     * @param null $count
     * @param null $sort
     * @return Phpld_Model_Collection
     */
    public function getLinks($idCategory, $where = false, $offset = 0, $count = null, $sort = null) {
              $sort_cols = array( 'P' => 'PAGERANK', 
                            'H' => 'HITS', 
                            'A' => 'TITLE', 
                            'D' => 'DATE_ADDED', 
                            'F' => $this->_tables['link']['name'].'.FEATURED DESC, '.$this->_tables['link_type']['name'].'.ORDER_ID');
        $sort_ord = array(  'P' => 'DESC', 
                            'H' => 'DESC', 
                            'A' => 'ASC', 
                            'D' => 'DESC', 
                            'F' => 'ASC');

            $sort = DEFAULT_SORT;
      

        if (!is_null($count)) {
            $limit = 'LIMIT ' . $offset . ', ' . $count;
        } else {
            $limit = null;
        }

        // Query for not exprired links
        $expire_where = "AND (`EXPIRY_DATE` >= NOW() OR `EXPIRY_DATE` IS NULL)";

        $permissions = $this->getPermissions();
        $email_conf = '';
        if (EMAIL_CONFIRMATION == 1) {
            $email_conf = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
        }
	
	$additional_links = '';
            $add_links = $this->_db->GetAll("SELECT `LINK_ID`  FROM `{$this->_tables['additional_category']['name']}` WHERE `CATEGORY_ID` = " . $this->_db->qstr($idCategory));
            foreach ($add_links as $add_link)
                $additional_links[] = $add_link['LINK_ID'];
            if (is_array($additional_links))
                $additional_links = implode(",", $additional_links);
            $additional_links = !$additional_links ? '0' : $additional_links;

              $query = "SELECT SQL_CALC_FOUND_ROWS *, PLD_LINK.DESCRIPTION as DESCRIPTION,
            PLD_LINK.ID as ID
            FROM `{$this->_tables['link']['name']}`
            INNER JOIN PLD_LINK_TYPE ON PLD_LINK_TYPE.ID = PLD_LINK.LINK_TYPE
            WHERE (PLD_LINK.`STATUS` = '2' OR {$permissions['permission_links_arts']}) {$email_conf} AND (`CATEGORY_ID` = " . $this->_db->qstr($idCategory) . " OR `{$this->_tables['link']['name']}`.`ID` IN ({$additional_links})) {$expire_where} {$where}
            ORDER BY {$sort_cols['F']}, {$sort_cols[$sort]} {$sort_ord[$sort]} {$limit}";

        $links = $this->_db->getAll($query);

        $count = $this->_db->getOne("SELECT FOUND_ROWS() as count");

        $collection = new Phpld_Model_Collection('Model_Link_Entity', $count);
        $collection->setElements($links);
        return $collection;
    }

    public function seoUrl($id)
    {
        $url = buildCategUrl($id);
        return $url;
    }
    
    public function logHit($idCategory)
    {
        $HitInfo = $this->_db->GetRow("SELECT * FROM `{$this->_tables['hitcount']['name']}` WHERE `CATEGORY_ID` = ".$this->_db->qstr($idCategory)." AND `IP` = ".$this->_db->qstr($_SERVER['REMOTE_ADDR']));

        if (!empty ($HitInfo))
        {
            $current_time = date ('Y-m-d H:i:s');
            $SecondsDiff  = dateTimeDifference($current_time, $HitInfo['LAST_HIT']);
            $Diff         = second2hour($SecondsDiff);

            if ($Diff > LIMIT_HITS_TIME)
            {
                $this->_db->Execute("UPDATE `{$this->_tables['category']['name']}` SET `HITS` = `HITS` + 1 WHERE `ID` = ".$this->_db->qstr($idCategory));
                $where = '`ID` = '.$this->_db->qstr($HitInfo['ID']);
                $HitInfo['LAST_HIT'] = $current_time;
                $this->_db->AutoExecute($this->_tables['hitcount']['name'], $HitInfo, 'UPDATE', $where);
            }
        }
        else
        {
            $this->_db->Execute("UPDATE `{$this->_tables['category']['name']}` SET `HITS` = `HITS` + 1 WHERE `ID` = ".$this->_db->qstr($idCategory));
            $HitInfo['CATEGORY_ID'] = $idCategory;
            $HitInfo['IP']         = $_SERVER['REMOTE_ARRD'];
            $this->_db->AutoExecute($this->_tables['hitcount']['name'], $HitInfo, 'INSERT', false);
        }
    }

    public function getByLocation($state, $city, $idUser = null)
    {
        $permissions = $this->getPermissions($idUser);
        $query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT {$this->_tables['category']['name']}.* FROM `{$this->_tables['category']['name']}`
         INNER JOIN {$this->_tables['link']['name']} ON {$this->_tables['link']['name']}.CATEGORY_ID = {$this->_tables['category']['name']}.ID
        WHERE STATE = '$state' AND CITY = '$city' AND ({$this->_tables['category']['name']}.`STATUS` = '2' OR {$permissions['permission_cats']} OR {$permissions['permission_cats_parents']}) AND `PARENT_ID` = 0 ORDER BY {$this->_tables['category']['name']}.`" . CATEG_FIELD_SORT . "` " . CATEG_FIELD_SORT_ORDER;

        $rs = $this->_db->cacheGetAll($query);
        $count = $this->_db->getOne("SELECT FOUND_ROWS() as count");
        $categsCollection = new Phpld_Model_Collection($this->_entityClass, $count);

        foreach ($rs as $row) {
            // Its "faster" then use subqueries
            $cur_cat_id = ($row['SYMBOLIC'] == 1) ? $row['SYMBOLIC_ID'] : $row['ID'];
            $additional_links = '';
            $add_links = $this->_db->GetAll("SELECT `LINK_ID`  FROM `{$this->_tables['additional_category']['name']}` WHERE `CATEGORY_ID` = " . $this->_db->qstr($cur_cat_id));
            foreach ($add_links as $add_link)
                $additional_links[] = $add_link['LINK_ID'];
            if (is_array($additional_links))
                $additional_links = implode(",", $additional_links);
            $additional_links = !$additional_links ? '0' : $additional_links;
            if ($idCategory == 0 && CATS_PREVIEW > 0) {
                $rs2 = $this->_db->CacheSelectLimit("SELECT * FROM `{$this->_tables['category']['name']}` WHERE (`STATUS` = '2' OR {$permissions['permission_cats']} OR {$permissions['permission_cats_parents']}) AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) AND `PARENT_ID` = " . $this->_db->qstr($row['ID']) . " ORDER BY `" . SUBCATEG_FIELD_SORT . "` " . SUBCATEG_FIELD_SORT_ORDER . ", {$this->_tables['category']['name']}.`TITLE` ", CATS_PREVIEW);
                foreach ($rs2->GetRows() as $subcategory) {
                    $row['SUBCATS'][] = $this->entity($subcategory);
                }
                $rs2->Close();
            }

            if ($row['SYMBOLIC'] == 1) {
                $row['ID'] = $row['SYMBOLIC_ID'];
                $tempcat = $this->_db->CacheGetRow("SELECT * FROM `{$this->_tables['category']['name']}` WHERE `ID` = " . $this->_db->qstr($row['SYMBOLIC_ID']));
                if (empty($row['TITLE']))
                    $row['TITLE'] = $tempcat['TITLE'];

                $row['TITLE'] = '@' . $row['TITLE'];
            }
            $categsCollection->append($row);
        }

        return $categsCollection;
    }
}