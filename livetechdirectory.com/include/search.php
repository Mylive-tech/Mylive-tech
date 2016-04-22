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
 
//Define search page to print (search form, results page, etc)
//Default none
/*
$printSearchPage = false;
$this->view->assign('printSearchPage', $printSearchPage);
*/

//Define search types

$searchTemplates = array ( 'advanced'
                           //add more
                        );
$searchTemplate = (in_array (strtolower ($_REQUEST['type']), $searchTemplates) ? strtolower ($_REQUEST['type']) : '');

//Get search ID if available
$searchid = (isset ($_REQUEST['searchid']) && preg_match ('`^[\d]+$`', $_REQUEST['searchid']) ? intval ($_REQUEST['searchid']) : 0);
if (empty ($searchid))
{
   //No or no numeric search ID
   unset ($searchid);
}

if ($searchid)
{
   //Print search results page
   $printSearchPage = 'results';
   $this->view->assign('printSearchPage', $printSearchPage);

   
   //Disable any caching by the browser
   disable_browser_cache();

   //If current user is banned, show a custom error message
   //and stop the rest of the script
   check_if_banned();

   //Define dummy variable to store if results available or not
   $have_search_results = 0;

   //Get search preferences
   $getSearchPrefs = $db->GetRow("SELECT * FROM `{$tables['search']['name']}` WHERE `SEARCHID` = ".$db->qstr($searchid));
   if (!is_array ($getSearchPrefs) || empty ($getSearchPrefs))
   {
      //No valid search ID,
      //No results found
      $have_search_results = 0;
      $this->view->assign('have_search_results', $have_search_results);

      //Send nofollow,noindex headers
      $this->view->assign('MetaRobots', 'nofollow, noindex');

      $this->view->assign('error'   , $error);
      $this->view->assign('errorMsg', $errorMsg);

      //Clean whitespace
      $this->view->load_filter('output', 'trimwhitespace');

      //Make output
      echo $this->view->fetch('search.tpl');
      exit();
   }
   else
   {
      //Search preferences available
      $db->Execute("UPDATE `{$tables['search']['name']}` SET `COMPLETED` = '1' WHERE `SEARCHID` = ".$db->qstr($searchid));

      //Build path
      $path   = array ();
      $path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME)        , 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
      $path[] = array ('ID' => '0', 'TITLE' => _L('Search Results') , 'TITLE_URL' => ''      , 'DESCRIPTION' => _L('Search Results'));
      $this->view->assign('path', $path);

      //Determine if boolean searches possible (need for sort by relevancy)
      $booleanPossible = (defined ('BOOLEAN_SEARCH_ACTIVE') && BOOLEAN_SEARCH_ACTIVE && version_compare (mysql_get_server_info(), '4.0.1', '>=') == 1);
      if (!$booleanPossible && strtolower ($searchPrefs['sortby']) == 'relevancy')
      {
         //Remove sorting by relevancy,
         //Boolean searches not possible
         unset ($searchPrefs['sortby']);
      }

      //Remove paging from URL
      $pattern = array ("/page-(\d+)\.htm[l]?/", '`([?]|[&])(p=)+[\d]*`', '`([?]|[&])(cat_page=)+[\d]*`', '`([?]|[&])(article_page=)+[\d]*`');
      $searchRequestURL = preg_replace ($pattern, '', request_uri());

      //Define a counter to track *all* found results
      //Used only to display error message
      $countSearchResults = 0;

      //Extract search preferences
      $searchPrefs = array();
      foreach ($getSearchPrefs as $field => $value)
      {
         if ($field == 'SEARCHTERMS' || $field == 'DISPLAYTERMS')
         {
            //Need to unserialize
            $termsArray = unserialize ($value);
            if (is_array ($termsArray))
            {
               foreach ($termsArray as $termKey => $termValue)
               {
                  $searchPrefs[strtolower($termKey)] = $termValue;
                  unset ($termsArray[$termKey], $termValue);
               }
            }
         }
         else
         {
            $searchPrefs[strtolower($field)] = $value;
         }
         unset ($getSearchPrefs[$field], $value);
      }

      //Don't add a WHERE option for category ID's if root/top is selected
      $incateg = array();
      if (!in_array (0, $searchPrefs['categchoice']) && !empty ($searchPrefs['categchoice']))
      {
         //Add all selected categories
         $incateg = $searchPrefs['categchoice'];

         //Add all subcategories if this is the case
         if ($searchPrefs['childcategs'])
         {
            foreach ($incateg as $cKey => $cID)
            {
               $subcategs = get_sub_categories($cID);
               if (is_array ($subcategs) && !empty ($subcategs))
               {
                  $incateg = array_merge ($incateg, $subcategs);
               }
               unset ($subcategs, $cKey, $cID);
            }
         }

         //Use unique category IDs
         $incateg = array_unique ($incateg);
         //Sort categories in ascending order
         sort ($incateg);
      }

      $this->view->assign('searchquery', $searchPrefs['query']);


      ##################################################
      ################# CATEGORY SEARCH ################
      ##################################################
      if (in_array ('categories', $searchPrefs['location']))
      {
         //Build category search preferences
         $search_preferences = array();
         //Add search query
         $search_preferences['search'] = $searchPrefs['query'];
         $search_preferences['Select_Options']  = array ( '*' );
         //Define WHERE options
         $search_preferences['Where_Options']   = array ("`STATUS` = '2'",
                                                         "`CACHE_TITLE` IS NOT NULL" ,
                                                         "`CACHE_URL` IS NOT NULL"
                                                         );
         $search_preferences['Where_Options'][] = '`HITS` '.($searchPrefs['hitless'] == 0 ? '>=' : '<').' '.$db->qstr($searchPrefs['hitlimit']);
         if (!empty ($incateg))
         {
            //Get categories from selected parent categories
            $search_preferences['Where_Options'][] = "`PARENT_ID` IN ('".implode ("', '", $incateg)."')";
         }


         $search_preferences['Search_Location'] = array ('TITLE');
         if (!$searchPrefs['titleonly'])
         {
            $search_preferences['Search_Location'][] = 'DESCRIPTION';
            $search_preferences['Search_Location'][] = 'META_DESCRIPTION';
            $search_preferences['Search_Location'][] = 'META_KEYWORDS';
         }

         $search_preferences['From_Table']      = $tables['category']['name'];

         switch (strtolower ($searchPrefs['sortby']))
         {
            case 'title' :
               $search_preferences['Order_Options'][] = '`TITLE` ' . $searchPrefs['sortorder'];
               break;
            case 'hits' :
               $search_preferences['Order_Options'][] = '`HITS` ' . $searchPrefs['sortorder'];
               break;
            case 'category' :
               $search_preferences['Order_Options'][] = '`PARENT_ID` ' . $searchPrefs['sortorder'];
               break;
         }

         //Use boolean search if possible
         $search_preferences['BooleanSearchActive'] = $booleanPossible;

         //Build search
         $CategorySearchResults = search($search_preferences);

         if (empty ($CategorySearchResults['errors']['empty_search']))
         {
            //Define category search results preferences
            $num_categs_per_page = 5;
            $categs_page_limit   = 3;

            //Count category search results
            $CountCategResults = $db->GetOne($CategorySearchResults['SQL_Count_All']);
            $CountCategResults = (empty ($CountCategResults) || $CountCategResults < 0 ? 0 : intval ($CountCategResults));

            if (!empty ($CountCategResults))
            {
               //Increment results counter
               $countSearchResults += $CountCategResults;

               $start_query = (!empty($_REQUEST['cat_page']) && preg_match('#^[\d]+$#', $_REQUEST['cat_page']) ? intval ($_REQUEST['cat_page']) : 1);

               //Get categories search results
               $ListCategs = $db->GetAll($CategorySearchResults['SQL_Query']." LIMIT ".($start_query <= 1 ? '0' : $start_query).", {$num_categs_per_page}");

               //Start category paging
               SmartyPaginate :: connect('CategoryPaging'); //Connect Paging
               if ($CountCategResults < 2)
               {
                  SmartyPaginate :: disconnect('CategoryPaging');
                  SmartyPaginate :: reset     ('CategoryPaging');
               }

               SmartyPaginate :: setPrevText    ('['._L('...less') .']' , 'CategoryPaging');
               SmartyPaginate :: setNextText    ('['._L('more...').']'  , 'CategoryPaging');
               SmartyPaginate :: setFirstText   ('['._L('First').']'    , 'CategoryPaging');
               SmartyPaginate :: setLastText    ('['._L('Last').']'     , 'CategoryPaging');
               SmartyPaginate :: setTotal       ($CountCategResults     , 'CategoryPaging');
               SmartyPaginate :: setUrlVar      ('cat_page'             , 'CategoryPaging');
               SmartyPaginate :: setUrl         ($searchRequestURL      , 'CategoryPaging');
               SmartyPaginate :: setCurrentItem ($start_query           , 'CategoryPaging');
               SmartyPaginate :: setLimit       ($num_categs_per_page   , 'CategoryPaging');
               SmartyPaginate :: setPageLimit   ($categs_page_limit     , 'CategoryPaging');
               SmartyPaginate :: assign         ($this->view                   , 'CategoryPaging', 'CategoryPaging');

               $this->view->assign('list_cat', $ListCategs);
               if (!empty ($ListCategs))
               {
                  $search_cat = $this->view->fetch('category_search.tpl');
                  $this->view->assign('search_category', $search_cat);
               }
            }
         }

         //Free memory
         unset ($CategorySearchResults, $search_preferences, $ListCategs, $categs_page_limit, $num_categs_per_page, $start_query, $search_cat);
      }//End category search



      ##################################################
      ################### LINK SEARCH ##################
      ##################################################
      if (in_array ('links', $searchPrefs['location']))
      {
         //Build links search preferences
         $search_preferences = array();
         //Add search query
         $search_preferences['search'] = $searchPrefs['query'];
         $search_preferences['Select_Options']  = array ( '*' );

         //Define WHERE options
         $search_preferences['Where_Options']   = array ("`STATUS` = '2'",
                                                         "(`EXPIRY_DATE` >= ".$db->DBDate(time())." OR `EXPIRY_DATE` IS NULL)"
                                                         );
         $search_preferences['Where_Options'][] = '`HITS` '.($searchPrefs['hitless'] == 0 ? '>=' : '<').' '.$db->qstr($searchPrefs['hitlimit']);
         if (!empty ($incateg))
         {
            //Get links from selected categories
            $search_preferences['Where_Options'][] = "`CATEGORY_ID` IN ('".implode ("', '", $incateg)."')";
         }


         $search_preferences['Search_Location'] = array ('TITLE');
         if (!$searchPrefs['titleonly'])
         {
            $search_preferences['Search_Location'][] = 'URL';
            $search_preferences['Search_Location'][] = 'DESCRIPTION';
            $search_preferences['Search_Location'][] = 'META_DESCRIPTION';
            $search_preferences['Search_Location'][] = 'META_KEYWORDS';
		    $search_preferences['Search_Location'][] = 'ADDRESS';
			$search_preferences['Search_Location'][] = 'CITY';
			$search_preferences['Search_Location'][] = 'STATE';
			$search_preferences['Search_Location'][] = 'ZIP';
         }

         $search_preferences['From_Table']      = $tables['link']['name'];

         $search_preferences['Order_Options']   = array ('`FEATURED` DESC');

         switch (strtolower ($searchPrefs['sortby']))
         {
            case 'title' :
               $search_preferences['Order_Options'][] = '`TITLE` ' . $searchPrefs['sortorder'];
               break;
            case 'hits' :
               $search_preferences['Order_Options'][] = '`HITS` ' . $searchPrefs['sortorder'];
               break;
            case 'category' :
               $search_preferences['Order_Options'][] = '`CATEGORY_ID` ' . $searchPrefs['sortorder'];
               break;
         }

         //Use boolean search if possible
         $search_preferences['BooleanSearchActive'] = $booleanPossible;

         //Build search
         $LinksResults = search($search_preferences);

         if (empty ($LinksResults['errors']['empty_search']))
         {
            $PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
            $LinksPerPage   = (PAGER_LPP       && PAGER_LPP       > 0 ? intval (PAGER_LPP)       : 10);

            //Determine paging
            $current_item = (!empty ($_REQUEST['p']) && preg_match ('`^[\d]+$`', $_REQUEST['p']) ? intval ($_REQUEST['p']) : 1);
            $current_item--;
            //Determine page
            $page         = ceil ($current_item / PAGER_LPP);

            $page = ($page < 1 ? 1 : $page); // Check again for negative page

            //Build limit query
            $limit = ' LIMIT '.($current_item <= 1 ? '0' : $current_item).', '.PAGER_LPP;

            //Count link search results
            $CountLinkResults = $db->GetOne($LinksResults['SQL_Count_All']);
            $CountLinkResults = (empty ($CountLinkResults) || $CountLinkResults < 0 ? 0 : intval ($CountLinkResults));

            if (!empty ($CountLinkResults))
            {
               //Increment results counter
               $countSearchResults += $CountLinkResults;

               //Get link search results
               $links = $db->GetAll($LinksResults['SQL_Query'].$limit);

               //Add cached category URL and title for regular links
               $links = addCategPathToLinks($links);

               //Start link paging
               SmartyPaginate :: connect('MainPaging'); //Connect Paging
               if ($CountLinkResults < 2)
               {
                  SmartyPaginate :: disconnect('MainPaging');
                  SmartyPaginate :: reset     ('MainPaging');
               }

               SmartyPaginate :: setPrevText    ('Previous'              , 'MainPaging');
               SmartyPaginate :: setNextText    ('Next'                  , 'MainPaging');
               SmartyPaginate :: setFirstText   ('First'                 , 'MainPaging');
               SmartyPaginate :: setLastText    ('Last'                  , 'MainPaging');
               SmartyPaginate :: setTotal       ($CountLinkResults       , 'MainPaging');
               SmartyPaginate :: setUrlVar      ('p'                     , 'MainPaging');
               SmartyPaginate :: setUrl         ($searchRequestURL       , 'MainPaging');
               SmartyPaginate :: setCurrentItem ($current_item + 1       , 'MainPaging');
               SmartyPaginate :: setLimit       ($LinksPerPage           , 'MainPaging');
               SmartyPaginate :: setPageLimit   ($PagerGroupings         , 'MainPaging');
               SmartyPaginate :: assign         ($this->view                    , 'MainPaging', 'MainPaging');

              // Links Rewrite
             $link = new Model_Link();
             foreach($links as $k => $v)
$links[$k]['SEO_URL'] = $link->getLinkById($v['ID'])->getUrl();             
$this->view->assign('links', $links);
            }
         }

         //Free memory
         unset ($LinksResults, $search_preferences, $LinksPerPage, $PagerGroupings, $current_item);
      }//End article search


      //Prepare for output
      $have_search_results = ($countSearchResults > 0 ? 1 : 0);
      $this->view->assign('have_search_results', $have_search_results);

      //Send nofollow,noindex headers
      $this->view->assign('MetaRobots', 'nofollow, noindex');

      $this->view->assign('error'   , $error);
      $this->view->assign('errorMsg', $errorMsg);

      //Clean whitespace
      $this->view->load_filter('output', 'trimwhitespace');

  
      
      //Make output
      echo $this->view->fetch('search.tpl');
      exit();
   }
}//IF searchid

if ($searchTemplate == 'advanced')
{ //Draw the advanced search page and save preferences
   require_once('init.php');

   //Print search form page
   $printSearchPage = 'form';
   $this->view->assign('printSearchPage', $printSearchPage);

   //Disable any caching by the browser
   disable_browser_cache();

   //If current user is banned, show a custom error message
   //and stop the rest of the script
   check_if_banned();

   //Build path
   $path   = array ();
   $path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME), 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
   $path[] = array ('ID' => '0', 'TITLE' => _L('Search') , 'TITLE_URL' => ''      , 'DESCRIPTION' => _L('Search Directory'));
   $this->view->assign('path', $path);

   //Search in title only select options
   $titleonlyOptions = array ( 0 => _L('Search all fields'), //Search title, URL, description, etc.
                               1 => _L('Search Titles Only') //Search only in title
                              );
   $this->view->assign('titleonlyOptions', $titleonlyOptions);

   $locationOptions = array ( 'links'        => _L('Links').' ('._L('Featured links first').')' ,
                              'articles'     => _L('Articles'),
                              'categories'   => _L('Categories')
                            );
   $this->view->assign('locationOptions', $locationOptions);

   //Hitlimits
   $hitlessOptions = array (  0 => _L('At Least'),
                              1 => _L('At Most')
                           );
   $this->view->assign('hitlessOptions', $hitlessOptions);

   //Results sorting
   $sortbyOptions = array ();
   if (version_compare (mysql_get_server_info(), '4.0.1', '>=') == 1)
   {
      $sortbyOptions['relevancy'] = _L('Relevancy');//Relevancy for Boolean search only
   }
   $sortbyOptions['title']    = _L('Title');
   $sortbyOptions['hits']     = _L('Hits');
   $sortbyOptions['category'] = _L('Category'); //Category ID, or parent category ID


   $this->view->assign('sortbyOptions', $sortbyOptions);
   $sortorderOptions = array (   'desc' => _L('in descending order'),
                                 'asc'  => _L('in ascending order')
                              );
   $this->view->assign('sortorderOptions', $sortorderOptions);

   if (CAT_SELECTION_METHOD != 2) {
      //Search in categories
      $categchoiceOptions = get_regular_categs_tree(0);
      $categchoiceOptions[0] = _L('Search all open categories');
      $this->view->assign('categchoiceOptions', $categchoiceOptions);
   } else {
      $load_Javascript = 1;
      $this->view->assign('load_Javascript', $load_Javascript);
   }

   //RALUCA: JQuery validation related
	$validators = array(
		'rules' => array(
			'search' => array(
				'required' => true
			),
			'titleonly' => array(
				'required' => true
			),
			'hitless' => array(
				'required' => true
			),
			'hitlimit' => array(
				'required' => true,
				'remote' => array(
									'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
									'type'=> "post",
						        		'data'=> array (
						        			'action' => "isInt",
						        			'table' => "article",
						        			'field' => "hitlimit"
						        		)
			)
			),
			'sortby' => array(
				'required' => true
			),
			'sortorder' => array(
				'required' => true
			),
			'childcategs' => array(
				'required' => true
			)
		),
		'messages' => array(
			'hitlimit'=> array(
				'remote'  	=>_L("Incorrect hit limit.")
			)
		)
	);
	
	$vld = json_custom_encode($validators);
	$this->view->assign('validators', $vld);
	
	$validator = new Validator($validators);
	//RALUCA: end of JQuery validation related   
   
   if (empty ($_POST['submit']))
   {
      //Set default search options
      $data = array();
      $data['titleonly']      = 0; //Search all fields by default
      $data['location']       = array ('links', 'articles', 'categories'); //Search in all (links,articles,categs) by default
      $data['hitless']        = 0; //Search results of at least X hits
      $data['hitlimit']       = 0; //Search results of 0 (zero) hitlimit
      $data['sortby']         = 'relevancy'; //Sort results by relevancy
      $data['sortorder']      = 'desc'; //Sort results in descending order
      $data['categchoice']    = 0; //Search in all categories by default
      $data['childcategs']    = 1; //Search in all subcategories by default
   }
   else
   {
      $data = array ();
      $data['search']    = (!empty ($_POST['search']) ? clean_search_query($_POST['search']) : '');
      $data['titleonly'] = (!empty ($_POST['titleonly']) && array_key_exists ($_POST['titleonly'], $titleonlyOptions) ? intval ($_POST['titleonly']): 0);
      
      $data['location'] = (is_array ($_POST['location']) && !empty ($_POST['location']) ? $_POST['location'] : '');
      //Keep only correct values of search location
      if (is_array ($data['location']))
      {
         foreach ($data['location'] as $key => $loc)
         {
            if (!array_key_exists ($loc, $locationOptions))
               unset ($data['location'][$key]);
         }

         if (empty ($data['location']))
            $data['location'] = '';
      }

      $data['hitless']  = (!empty ($_POST['hitless']) && array_key_exists ($_POST['hitless'], $hitlessOptions) ? intval ($_POST['hitless']): 0);
      $data['hitlimit'] = (preg_match ('`^[\d]+$`', $_POST['hitlimit']) ? intval ($_POST['hitlimit']) : 0);

      $data['sortby']  = (!empty ($_POST['sortby']) && array_key_exists ($_POST['sortby'], $sortbyOptions) ? $_POST['sortby'] : 'relevancy');
      $data['sortorder']  = (!empty ($_POST['sortorder']) && array_key_exists ($_POST['sortorder'], $sortorderOptions) ? $_POST['sortorder'] : 'desc');

      $data['categchoice'] = (is_array ($_POST['categchoice']) ? $_POST['categchoice'] : array());
      if (empty ($data['categchoice']))
      {
         //No categories selected,
         //search in all of them
         $data['categchoice'][0] = 0;
      }
      $data['categchoice'] = array_unique ($data['categchoice']);//Remove duplicate entries
      $data['childcategs'] = (!empty ($_POST['childcategs']) ? 1 : 0);

	   //RALUCA: JQuery validation related - server side.
	   $validator = new Validator($validators);
	   $validator_res = $validator->validate($_POST);
	   //RALUCA: end of JQuery validation related - server side.
	   
	   if (empty($validator_res))
	   {
         //Build database entries
         $dbdata = array();
         $dbdata['USERID'] = (isset ($_SESSION['phpld']['user']['id']) ? intval ($_SESSION['phpld']['user']['id']) : 0);
         $dbdata['IPADDRESS'] = $client_info['IP'];
         $dbdata['QUERY'] = $data['search'];
         $dbdata['SORTBY'] = $data['sortby'];
         $dbdata['SORTORDER'] = strtoupper ($data['sortorder']);
         $dbdata['SEARCHTIME']  = gmdate ('Y-m-d H:i:s');

         $searchterms = array();
         $searchterms['titleonly']   = $data['titleonly'];
         $searchterms['location']    = $data['location'];
         $searchterms['hitless']     = $data['hitless'];
         $searchterms['hitlimit']    = $data['hitlimit'];
         $searchterms['categchoice'] = $data['categchoice'];
         $searchterms['childcategs'] = $data['childcategs'];
         $dbdata['SEARCHTERMS'] = serialize ($searchterms);

         $displayterms = array();
         $displayterms['sortby']    = $data['sortby'];
         $displayterms['sortorder'] = $data['sortorder'];
         $dbdata['DISPLAYTERMS'] = serialize ($displayterms);

         $dbdata['COMPLETED'] = 0;

         unset ($searchterms, $displayterms);

         //Save search preferences into database
         $saveSearch = $db->Replace($tables['search']['name'], $dbdata, 'SEARCHID', true);

         
         //Get insert ID
         $searchid = mysql_insert_id();
         $searchid = (!empty ($searchid) ? intval ($searchid) : 0);

         if ($newCatId < 1)
         {
         	//"mysql_insert_id()" failed, try to get ID via MySQL query
            $mysql_id = $db->GetOne("SELECT MAX(SEARCHID) AS `maxid` FROM `{$tables['search']['name']}`");
            $searchid = intval ($mysql_id);
         }

         if (!empty ($saveSearch))
         {
            //Redirect to search results
            http_custom_redirect(DOC_ROOT."/search.php?searchid={$searchid}");
         }
         else
         {
            //Print error that occured while saving
            $error = 1;
            $errorMsg = $db->ErrorMsg();
         }
      }
   }

   $this->view->assign($data);
   $this->view->assign('error'   , $error);
   $this->view->assign('errorMsg', $errorMsg);

   //Clean whitespace
   $this->view->load_filter('output', 'trimwhitespace');

   //Make output
   //echo $this->view->fetch('search.tpl');
   //exit();
}//IF type=advanced

?>