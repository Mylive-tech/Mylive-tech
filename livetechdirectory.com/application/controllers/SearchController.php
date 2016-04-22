<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
/**
 # ################################################################################
 # Project:   PHP Link Directory
 #
 # **********************************************************************
 # Copyright (C) 2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 #
 # This software is for use only to those who have purchased a license.
 # A license must be purchased for EACH installation of the software.
 #
 # By using the software you agree to the terms:
 #
 #    - You may not redistribute, sell or otherwise share this software
 #      in whole or in part without the consent of the the ownership
 #      of PHP Link Directory. Please contact david@david-duval.com
 #      if you need more information.
 #
 #    - You agree to retain a link back to http://www.phplinkdirectory.com/
 #      on all pages of your directory if you purchased any of our "link back" 
 #      versions of the software.
 #
 #
 # In some cases, license holders may be required to agree to changes
 # in the software license before receiving updates to the software.
 # **********************************************************************
 #
 # For questions, help, comments, discussion, etc., please join the
 # PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
 #
 # @link           http://www.phplinkdirectory.com/
 # @copyright      2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.1.0 Phoenix Release
 # ################################################################################
 */
class SearchController extends PhpldfrontController
{
    public function indexAction()
    {
        //require_once INSTALL_PATH.'/search_.php';
        $sorter = new Phpld_Sorter();
        $categoryModel = new Model_Category();
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $location = $this->getParam('location');

        $paginator = new Phpld_Paginator();
        $limit = $paginator->getLimit();

        $permissions = $categoryModel->getPermissions();
        $search_preferences = array();


        if (!is_null($location)) {
            if (strpos($location, ',') !== false) {
                // "City, State" Format recognized
                $address = explode(',', $location);
                $search_preferences['Where_Options'][] = ' AND CITY = "'.trim($address[0]).'" ';
                $search_preferences['Where_Options'][] = ' AND STATE = "'.trim($address[1]).'"';
            }
        }

        //$search_preferences['BooleanSearchActive'] = 1; // Default to regular search
        $search_preferences['Select_Options']  = array ( '*' );

        $submit_items = $db->GetAll("SELECT * FROM `{$tables['submit_item']['name']}` AS si
            LEFT JOIN `{$tables['submit_item_status']['name']}` AS sis
            ON (si.ID = sis.ITEM_ID)
            WHERE sis.`STATUS` = '2' AND si.`IS_DETAIL` = '1'
            ORDER BY si.`ORDER_ID` ASC");

        $link_types_si = array();
        for($i=0; $i<count($submit_items); $i++) {
            $link_types_si[$submit_items[$i]['LINK_TYPE_ID']][] = $submit_items[$i]['FIELD_NAME'];
        }

        $search_preferences['Where_Options'][] =  "(`STATUS` = '2' OR {$permissions['permission_links_arts']})";
        $search_preferences['Where_Options'][] =  "(`EXPIRY_DATE` >= ".$db->DBDate(time())." OR `EXPIRY_DATE` IS NULL)";

        if (EMAIL_CONFIRMATION != 1) {
            $search_preferences['Where_Options'][] = "`OWNER_EMAIL_CONFIRMED` = '1'";
        }

        $search_preferences['Order_Options']   = array ($sorter->getOrder());

        $search_preferences['Search_Location'] = array ( 'TITLE'             ,
            'ANNOUNCE'      ,
            'DESCRIPTION'      ,
            'META_DESCRIPTION',
            'META_KEYWORDS',
            'ADDRESS',
            'CITY',
            'STATE',
            'ZIP'
        );


        if (!empty($link_types_si)) {
            //$search_preferences['Search_Location_Link_Type'] = $link_types_si;
        }

        $search_preferences['From_Table']= $tables['link']['name'];
        //$search_preferences['BooleanSearchActive'] = 0;
        $LinksResults = search($search_preferences);
        $count = $db->GetOne($LinksResults['SQL_Count_All']);
        if (!empty ($count)) {
            $links = $db->GetAll($LinksResults['SQL_Query'].$limit);
        }

        if ($count > 0) {
            $linksCollection = new Phpld_Model_Collection('Model_Link_Entity', $count);
            $linksCollection->setElements($links);

            $this->_breadcrumbs->add(_L('Search'));
            $paginator->assign($this->layout, $linksCollection->countWithoutLimit());
            $this->view->assign('links', $linksCollection);
        }
        $this->view->assign('totalCount', $count);
        $this->view->assign('searchquery', $searchQuery);
        $this->view->assign('searchcountry', $searchQueryCountry);
    }

	public function ajaxSearchLinksAction()
	{
		global $count;
		$db = Phpld_Db::getInstance()->getAdapter();
		$tables=Phpld_Db::getInstance()->getTables();

		$query=$_REQUEST['linkQuery'];
		$limit=intval($_REQUEST['page_limit']);

		if(!empty($query['loc']))
			$locQuery = " AND ".$this->getLocQuery($query['loc']);
		$linkQuery = " AND ".$this->getLinkQuery($query['q']);

		$db_result = $db->GetAll("SELECT DISTINCT * FROM `{$tables['link']['name']}` WHERE
            `STATUS` = 2 {$linkQuery}{$locQuery}");

		$collection = new Phpld_Model_Collection('Model_Link_Entity', $count);
		$collection->setElements($db_result);
		$result=array();
		foreach ($collection as $listing) {
			$result[] = array(
				'title' => $listing['TITLE'],
				'id'    => $listing['TITLE'],
				'url'   => $listing->getUrl(),
				'description' => substr(trim(strip_tags($listing['DESCRIPTION'])), 0, 200)
			);
		}

		$result = array('result' => $result);

		echo json_encode($result);
		exit();
	}

public function basicAction(){ 	 
 $db 	  =   Phpld_Db::getInstance()->getAdapter();
 $tables =   Phpld_Db::getInstance()->getTables();

  /* Sanitize data array */
 $query = array(
                 'TITLE'=> filter_var($_REQUEST['search'], FILTER_SANITIZE_STRING),
                 'URL'=> filter_var($_REQUEST['search'], FILTER_SANITIZE_URL),
/* Comment Out this if you want additional fields to serach*/       	 
//                        	'DESCRIPTION'   => filter_var($_REQUEST['search'], FILTER_SANITIZE_STRING),
//                        	'ZIP'       	=> filter_var($_REQUEST['search'], FILTER_SANITIZE_NUMBER_INT)
    	);   	 
 foreach($query as $k => $v ){
    if(!empty($v)){
         $str    	= strtolower($v);
   	  $str    	= str_replace(array('\\','\'','%'), array('\\\\','\\\'','\\%'), $str);
         $strQuery[] = "LOWER({$k}) LIKE '%{$str}%'";
         $searchQuery[] = $v;
        }
   }  	 
    /* set your query search here */
    if(!empty($strQuery)){
        	$basicLinkQuery = (string) "AND (".implode(" OR ", $strQuery) .")";
        	$db_result  	= $db->GetAll("SELECT DISTINCT * FROM {$tables['link']['name']} WHERE STATUS = 2 $basicLinkQuery");                                       	 
        	$count      	= $db->GetOne("SELECT COUNT(*) FROM {$tables['link']['name']} WHERE STATUS = 2 $basicLinkQuery");
    	}else{
        	$basicLinkQuery = "";
        	$db_result  	= array();                                       	 
        	$count      	= 0;
    	}
   	 
    	$collection = new Phpld_Model_Collection('Model_Link_Entity', $count);
    	$collection->setElements($db_result);
   	 
    	$searchQuery = implode(',', $searchQuery);
   	 
    	/* Set the vars array to template*/
   	 
    	$this->view->assign('links', $collection);
    	$this->view->assign('totalCount', $count);
    	$this->view->assign('searchquery', htmlspecialchars($searchQuery));
   	 
  	}

	public function ajaxSearchAddressAction()
	{
		global $count;
		$db = Phpld_Db::getInstance()->getAdapter();
		$tables=Phpld_Db::getInstance()->getTables();
		$query=$_REQUEST['locQuery'];
		$limit=intval($_REQUEST['page_limit']);

		$locQuery = " AND ".$this->getLocQuery($query['loc']);

		//uncomment if need to show only locations of links matching search terms
		/*if(!empty($query['q']))
			$linkQuery = " AND ".$this->getLinkQuery($query['q']);*/

		$query="SELECT DISTINCT `CITY`, `STATE`, `COUNTRY`, `ZIP` FROM `{$tables['link']['name']}` WHERE
            `STATUS` = 2 {$linkQuery}{$locQuery} LIMIT {$limit}";
		//echo $query; exit();
		$db_result = $db->GetAll($query);

		$result=array();
		foreach ($db_result as $value) {
			$line = '';
			if(!empty($value['CITY'])){
				$line =  $value['CITY'];
			}

			if(!empty($value['STATE'])){
				$line .= ", ".$value['STATE'];
			}

			if(!empty($value['COUNTRY'])){
				$line .= ", ".$value['COUNTRY'];
			}
			if(!empty($value['ZIP'])){
				$line .= ", ".$value['ZIP'];
			}
			$result[] = array('title' => $line, 'id' => $line, 'url' => '#');
		}
		$result = array('result' => $result);

		echo json_encode($result);
		exit();
	}

	private function getLinkQuery($str)
	{
		$str=strtolower($str);
		$str=str_replace(array('\\','\'','%'), array('\\\\','\\\'','\\%'), $str);
		return "(LOWER(`TITLE`) LIKE '%{$str}%' OR
            LOWER(`DESCRIPTION`) LIKE '{$str}%' OR
            LOWER(`CITY`) LIKE '%{$str}%' OR
            LOWER(`STATE`) LIKE '%{$str}%' OR
            LOWER(`COUNTRY`) LIKE '%{$str}%' OR
            LOWER(`ZIP`) LIKE '%{$str}%' OR
            LOWER(`ADDRESS`) LIKE '%{$str}%')";
	}

	private function getLocQuery($str)
	{
		$str=strtolower($str);
		$str=str_replace(array('\\','\'','%'), array('\\\\','\\\'','\\%'), $str);
		return "(lower(concat(coalesce(`CITY`,''), ', ', coalesce(`STATE`, ''), ', ', coalesce(`COUNTRY`,''), ', ', coalesce(`ZIP`, ''))) LIKE '%{$str}%')";
	}
}