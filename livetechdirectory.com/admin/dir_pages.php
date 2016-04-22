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

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];


$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

$where = '';

//Determine category ID
//Keep backwards compatibility with urlvariable "c"
$category = (isset ($_REQUEST['category']) ? $_REQUEST['category'] : (isset ($_REQUEST['c']) ? $_REQUEST['c'] : ''));


if (isset ($_REQUEST['status']) && preg_match ('`^[\d]+$`', $_REQUEST['status']))
{
   $_REQUEST['status'] = intval ($_REQUEST['status']);
   $where .= " AND {$tables['page']['name']}.STATUS = ".$db->qstr($_REQUEST['status']);
}

////Search by ID
//$searchByID = (!empty ($_REQUEST['searchbyid']) && preg_match ('`^[\d]+$`', $_REQUEST['searchbyid']) ? intval ($_REQUEST['searchbyid']) : '');
//$tpl->assign('searchbyid', $searchByID);
//if (!empty ($searchByID))
//{
//   $where .= " AND {$tables['page']['name']}.ID LIKE ".$db->qstr('%'.$searchByID.'%');
//   $_REQUEST['searchbyid'] = $searchByID;
//}

//Determine columns
$columns = array ('ID' => _L('ID'), 'NAME' => _L('Name'), 'SEO_NAME' => _L('Link'), 'STATUS' => _L('Status'), 'PLACEMENT' => _L('Order'), 'PRIVACY' => _L('Privacy'), 'DATE_ADDED' => _L('Date Added'), 'ACTION' => _L('Action'));
$tpl->assign('columns', $columns);

//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);
$tpl->assign('columnURLs', $columnURLs);

$tpl->assign('col_count', count ($columns) + 3);

$tpl->assign('stats', array (0 => _L('Inactive'), 2 => _L('Active')));
$tpl->assign('privacy', array (0 => _L('All Users'), 1 => _L('Registered Users')));

//Define sorting order

if (defined ('SORT_FIELD') && SORT_FIELD != '' && SORT_FIELD != 'ID')
{
   $orderBy .= ""."{$tables['page']['name']}.".SORT_FIELD." ".SORT_ORDER;
}
else
{
   $orderBy = "{$tables['page']['name']}.ID DESC";
}




// Determine current index
$page         = ceil ($current_item / $LinksPerPage); // Determine page
$limit        = ' LIMIT '.($current_item <= 1 ? '0' : $current_item).', '.$LinksPerPage;

require_once '../include/search.php';
$search_preferences = array ();
$search_preferences['Select_Options']  = array ();
$search_preferences['Where_Options']   = array ();
$search_preferences['Order_Options']   = array ();
$search_preferences['Search_Location'] = array ( "{$tables['page']['name']}.NAME",
                                                 "{$tables['page']['name']}.CONTENT",
                                                 "{$tables['page']['name']}.ID"
                                                 );
//$search_preferences['BooleanSearchActive'] = 0;

$LinksResults = search($search_preferences);
$search       = (!empty ($LinksResults['search']) ? $LinksResults['search'] : '');
$tpl->assign('search', $search);

//No IF admin clause,
//moderators are allowed to access images
$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['page']['name']}` WHERE 1 ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '')." {$where}");
$tpl->assign('list_total', $list_total);
$sql = "SELECT * " . (! empty ($LinksResults['Select_Relevancy']) && ! empty ($LinksResults['search']) ? ", " . $LinksResults['Select_Relevancy'] : '') . " FROM `{$tables['page']['name']}` WHERE 1 " . (! empty ($LinksResults['Search_Query']) && ! empty ($LinksResults['search']) ? " AND " . $LinksResults['Search_Query'] : '') . (! empty ($LinksResults['Relevancy_Tuning']) && ! empty ($LinksResults['search']) ? $LinksResults['Relevancy_Tuning'] : '') . " {$where} ORDER BY " . (! empty ($LinksResults['Relevancy_Order']) && ! empty ($LinksResults['search']) ? $LinksResults['Relevancy_Order'] . ", " : '') . " {$orderBy}";

$rs = $db->SelectLimit($sql, $LinksPerPage, ($current_item <= 1 ? '0' : $current_item - 1));

if ($rs === false)
   $list = array ();
else
   $list = $rs->GetAssoc(true);

unset ($sql, $rs);

$tpl->assign('list', $list);
$cid = get_category($_SERVER['REQUEST_URI']);


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

$min_keyword_length_comment = _L("Type in the keyword(s) or page id you're searching for.");
$min_keyword_length         = (!empty ($LinksResults['Min_Word_Length']) ? $LinksResults['Min_Word_Length'] : '4');
$min_keyword_length_comment = str_replace ('#MIN_WORD_LENGTH#', $min_keyword_length, $min_keyword_length_comment);
$tpl->assign('min_keyword_length_comment', $min_keyword_length_comment);


$tpl->assign($_POST);
$tpl->assign('error'    , $error);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_pages_dt.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>
