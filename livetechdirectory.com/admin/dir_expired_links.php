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
 

require_once 'init.php';

$error   = 0;
$expired = 0;

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

//Build category tree
if (AJAX_CAT_SELECTION_METHOD == 0)
{
   $tpl->assign('categs', get_categs_tree());
}

//Load and run multiple link actions
require_once 'link_multi_action.php';

$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

$where = '';

//Determine link ID
$linkID = (isset ($_REQUEST['linkid']) ? $_REQUEST['linkid'] : '');

//Build where clause for link ID
if (!empty ($linkID))
{
   $linkID = urldecode ($linkID);

   //Clean multiple spaces, commas, etc
   $pattern  = array ( '/\s/', '`[,]+`', '`^[,]*`', '`[,]+$`' );
   $replace  = array ( ''    , ','     , ''       , ''        );
   $linkID = preg_replace ($pattern, $replace, $linkID);

   $linkIDArray = explode (',', $linkID);
   $linkIDArray = array_unique ($linkIDArray);

   //If editor, remove links he/she is not allowed to view
   if (!$_SESSION['phpld']['adminpanel']['is_admin'])
   {
      //Loop through each category
      foreach ($linkIDArray as $key => $lid)
      {
         $cID = $db->GetOne("SELECT `CATEGORY_ID` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($lid));
         if (!in_array ($cID, $_SESSION['phpld']['adminpanel']['permission_array']))
         {
            //Current link category is not in editors permissions,
            //Remove from list
            unset ($linkIDArray[$key]);
         }
      }
   }

   if (!empty ($linkIDArray))
   {
      $where .= " AND {$tables['link']['name']}.ID IN ('".implode ("', '", $linkIDArray)."')";

      $_REQUEST['linkid'] = implode (',', $linkIDArray);

      $linkid = (!empty ($_REQUEST['linkid']) ? $_REQUEST['linkid'] : '');
      $tpl->assign('linkid', $linkid);
   }
}

//Determine category ID
//Keep backwards compatibility with urlvariable "c"
$category = (isset ($_REQUEST['category']) ? $_REQUEST['category'] : (isset ($_REQUEST['c']) ? $_REQUEST['c'] : ''));

//Build where clause for category ID
if (!empty ($category))
{
   $category = urldecode ($category);

   //Clean multiple spaces, commas, etc
   $pattern  = array ( '/\s/', '`[,]+`', '`^[,]*`', '`[,]+$`' );
   $replace  = array ( ''    , ','     , ''       , ''        );
   $category = preg_replace ($pattern, $replace, $category);

   $categsArray = explode (',', $category);
   $categsArray = array_unique ($categsArray);

   //If editor, remove links he/she is not allowed to view
   if (!$_SESSION['phpld']['adminpanel']['is_admin'])
   {
      //Loop through each category
      foreach ($categsArray as $key => $cID)
      {
         if (!in_array ($cID, $_SESSION['phpld']['adminpanel']['permission_array']))
         {
            //Current category is not in editors permissions,
            //Remove from list
            unset ($categsArray[$key]);
         }
      }
   }

   if (!empty ($categsArray))
   {
      $where .= " AND {$tables['link']['name']}.CATEGORY_ID IN ('".implode ("', '", $categsArray)."')";
      
      $_REQUEST['category'] = implode (',', $categsArray);

      $category = (!empty ($_REQUEST['category']) ? $_REQUEST['category'] : '');
      $tpl->assign('category', $category);
   }
}
else
{
	$expired_where_join1 = "NOT({$tables['link']['name']}.EXPIRY_DATE <= ".$db->DBTimeStamp(time())." AND {$tables['link']['name']}.EXPIRY_DATE IS NOT NULL)";
   $where .= ' AND '.($_REQUEST['f'] == '1' ? '' : 'NOT ').$expired_where_join1;
}

if (!empty ($_REQUEST['expired']))
{
   $expired = 1;
   $where .= " AND {$tables['link']['name']}.RECPR_EXPIRED = '1'";
}
//
////Search by ID
//$searchByID = (!empty ($_REQUEST['searchbyid']) && preg_match ('`^[\d]+$`', $_REQUEST['searchbyid']) ? intval ($_REQUEST['searchbyid']) : '');
//$tpl->assign('searchbyid', $searchByID);
//if (!empty ($searchByID))
//{
//   $where .= " AND {$tables['link']['name']}.ID LIKE ".$db->qstr('%'.$searchByID.'%');
//   $_REQUEST['searchbyid'] = $searchByID;
//}


if (isset ($_REQUEST['status']) && preg_match ('`^[\d]+$`', $_REQUEST['status']))
{
   $_REQUEST['status'] = intval ($_REQUEST['status']);
   $where .= " AND {$tables['link']['name']}.STATUS = ".$db->qstr($_REQUEST['status']);
}

$tpl->assign('featured', $_REQUEST['f'] == 1 ? 1 : 0);
$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active')));
$tpl->assign('valid', array (0 => _L('Broken')  , 1 => _L('Unknown'), 2 => _L('Ok'),));

//Determine columns
$columns = array ('TITLE' => _L('Title'), 'URL' => _L('URL'), 'CATEGORY' => _L('Category'), 'STATUS' => _L('Status'), 'PAGERANK' => _L('PR'), 'HITS' => _L('Hits'), 'DATE_ADDED' => _L('Date Added'), 'OWNER_EMAIL_CONFIRMED'=>_L('Mail Confirmed'));
if (PAY_ENABLE == 1)
{
   $columns = array_merge ($columns, array ('LINK_TYPE' => _L('Type')));
   $tpl->assign('link_type_str', $link_type_str);
}
$tpl->assign('columns', $columns);

//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);
$tpl->assign('columnURLs', $columnURLs);

$tpl->assign('col_count', count ($columns) + 3);

$orderBy = "{$tables['link']['name']}.FEATURED DESC";

if (defined ('SORT_FIELD') && SORT_FIELD != '')
   $orderBy .= ", ". (SORT_FIELD == "CATEGORY" ? "{$tables['category']['name']}.TITLE" : "{$tables['link']['name']}.".SORT_FIELD)." ".SORT_ORDER;

$left_join_categ = " LEFT JOIN `{$tables['category']['name']}` ON ({$tables['link']['name']}.CATEGORY_ID = {$tables['category']['name']}.ID) ";

// Determine current index
$page         = ceil ($current_item / $LinksPerPage); // Determine page
$limit        = ' LIMIT '.($current_item <= 1 ? '0' : $current_item).', '.$LinksPerPage;

require_once '../include/search.php';
$search_preferences = array ();
$search_preferences['Select_Options']  = array ();
$search_preferences['Where_Options']   = array ("(`EXPIRY_DATE` < ".$db->DBDate(time())." AND `EXPIRY_DATE` IS NOT NULL)");
$search_preferences['Order_Options']   = array ();
$search_preferences['Search_Location'] = array ( "{$tables['link']['name']}.URL" ,
                                                 "{$tables['link']['name']}.TITLE",
                                                 "{$tables['link']['name']}.DESCRIPTION",
                                                 "{$tables['link']['name']}.ID"
                                                 );
//$search_preferences['BooleanSearchActive'] = 0;
                                                 
$LinksResults = search($search_preferences);

$search       = (!empty ($LinksResults['search']) ? $LinksResults['search'] : '');
$tpl->assign('search', $search);

if ($_SESSION['phpld']['adminpanel']['is_admin'])
{
   $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE 1 ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '')." {$where}");
   
   $tpl->assign('list_total', $list_total);
   
   $sql = "SELECT {$tables['link']['name']}.*, ".$db->IfNull("{$tables['category']['name']}.TITLE", "'Top'")." AS `CATEGORY` ".(!empty ($LinksResults['Select_Relevancy']) ? ", ".$LinksResults['Select_Relevancy'] : '')." FROM `{$tables['link']['name']}` LEFT JOIN `{$tables['category']['name']}` ON ({$tables['link']['name']}.CATEGORY_ID = {$tables['category']['name']}.ID) WHERE 1 ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '').(!empty ($LinksResults['Relevancy_Tuning']) ? $LinksResults['Relevancy_Tuning'] : '')." {$where} ORDER BY ".(!empty ($LinksResults['Relevancy_Order']) ? $LinksResults['Relevancy_Order'].", " : '')." {$orderBy}";
   
   $rs = $db->SelectLimit($sql, $LinksPerPage, ($current_item <= 1 ? '0' : $current_item - 1));

   if ($rs === false)
      $list = array ();
   else
      $list = $rs->GetAssoc(true);
}
else
{
   $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` {$left_join_categ} WHERE 1 ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '')." {$where} AND 1 ");

   $sql = "SELECT {$tables['link']['name']}.*, {$tables['category']['name']}.TITLE AS `CATEGORY` ".(!empty ($LinksResults['Select_Relevancy']) ? ", ".$LinksResults['Select_Relevancy'] : '')." FROM `{$tables['link']['name']}` {$left_join_categ} WHERE 1 ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '').(!empty ($LinksResults['Relevancy_Tuning']) ? $LinksResults['Relevancy_Tuning'] : '')." {$where} AND 1 ORDER BY ".(!empty ($LinksResults['Relevancy_Order']) ? $LinksResults['Relevancy_Order'].", " : '')." {$orderBy}";

   $rs = $db->SelectLimit($sql, $LinksPerPage, ($current_item <= 1 ? '0' : $current_item - 1));

   if ($rs === false)
      $list = array ();
   else
      $list = $rs->GetAssoc(true);
}
unset ($sql, $rs);

$tpl->assign('list', $list);
$cid = get_category($_SERVER['REQUEST_URI']);
if ($cid == 0)
{
   $rss_link = false;
}
else
{
   $rss_link = true;
   $tpl->assign('rsscategory', $cid);
}

// Start Paging
SmartyPaginate :: connect(); // Connect Paging
SmartyPaginate :: setPageLimit($LinksPerPage); // Set default number of page groupings

// Build Paging
if ($page < 2)
{
   SmartyPaginate :: disconnect();
   SmartyPaginate :: reset     ();
}

$list_total     = (!empty ($list_total) && $list_total >= 0 ? intval ($list_total) : 0);

SmartyPaginate :: setPrevText    ('Previous'             );
SmartyPaginate :: setNextText    ('Next'                 );
SmartyPaginate :: setFirstText   ('First'                );
SmartyPaginate :: setLastText    ('Last'                 );
SmartyPaginate :: setTotal       ($list_total            );
SmartyPaginate :: setUrlVar      ('p'                    );
SmartyPaginate :: setUrl         ($_SERVER['REQUEST_URI']);
SmartyPaginate :: setCurrentItem ($current_item          );
SmartyPaginate :: setLimit       ($LinksPerPage          );
SmartyPaginate :: setPageLimit   ($PagerGroupings        );
SmartyPaginate :: assign         ($tpl                   );

unset ($list_total, $PagerGroupings, $LinksPerPage);

$min_keyword_length_comment = _L("Type in the keyword(s) or link id you're searching for.");
$min_keyword_length         = (!empty ($LinksResults['Min_Word_Length']) ? $LinksResults['Min_Word_Length'] : '4');
$min_keyword_length_comment = str_replace ('#MIN_WORD_LENGTH#', $min_keyword_length, $min_keyword_length_comment);
$tpl->assign('min_keyword_length_comment', $min_keyword_length_comment);

$feat_link = ($_REQUEST['f'] == '1' ? 1 : 0);
$categ     = (!empty ($_REQUEST['c']) ? $_REQUEST['c'] : 0);

$HaveExpiredRecpr_sql = "SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `RECPR_EXPIRED` = '1'".($_REQUEST['f'] == 1 ? " AND `FEATURED` = '1'" : '').(!empty ($_REQUEST['c']) ? " AND `CATEGORY_ID` = ".$db->qstr($_REQUEST['c']) : '');
$HaveExpiredRecpr = $db->GetOne($HaveExpiredRecpr_sql);
$tpl->assign('HaveExpiredRecpr', $HaveExpiredRecpr);

//check if have links expired (according to email confirmation)
if (EMAIL_CONFIRMATION == 1) {
    $HaveExpiredEmail_sql = "SELECT COUNT(*) FROM `{$tables['link']['name']}`
                                WHERE `OWNER_EMAIL_CONFIRMED` = '0'
                                AND DATE_ADD(`DATE_ADDED`, INTERVAL ".WAIT_FOR_EMAIL_CONF." DAY) <= now()";
  
    $HaveExpiredEmail = $db->GetOne($HaveExpiredEmail_sql);
    $tpl->assign('HaveExpiredEmail', $HaveExpiredEmail);
}

$tpl->assign($_POST);
$tpl->assign('error'    , $error);
$tpl->assign('expired'  , $expired);
$tpl->assign('feat_link', $feat_link);
$tpl->assign('categ'    , $categ);
$tpl->assign('rss_link' , $rss_link);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_expired_links.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>