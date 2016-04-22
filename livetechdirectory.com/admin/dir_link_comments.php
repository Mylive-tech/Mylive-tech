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

$error = 0;
$range = (preg_match ('`^[\d]+$`', $_REQUEST['range']) && $_REQUEST['range'] > 0 ? intval ($_REQUEST['range']) : 200);

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

//Build category tree
if (AJAX_CAT_SELECTION_METHOD == 0)
{
   $tpl->assign('categs', get_categs_tree());
}

//Load and run multiple comment actions
require_once 'link_comment_multi_action.php';

$tpl->assign('stats'  , array (0       => _L('Inactive'), 1           => _L('Pending')  , 2             => _L('Active'),));

//Determine columns
$columns = array ('TITLE' => _L('Link Title') , 'COMMENT' => _L('Comment'), 'STATUS' => _L('Status'), 'USER_NAME' => _L('Username'), 'IPADDRESS' => _L('IP'), 'DATE_ADDED' => _L('Date Added'));
$tpl->assign('columns', $columns);

//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);
$tpl->assign('columnURLs', $columnURLs);

// Determine current index
$page  = ceil ($current_item / LINKS_PER_PAGE); // Determine page
$limit = ' LIMIT '.($current_item <= 1 ? '0' : $current_item).', '.LINKS_PER_PAGE;

$where = " AND {$tables['comment']['name']}.TYPE = '1'";

//Search by ID
$searchByID = (!empty ($_REQUEST['searchbyid']) && preg_match ('`^[\d]+$`', $_REQUEST['searchbyid']) ? intval ($_REQUEST['searchbyid']) : '');
$tpl->assign('searchbyid', $searchByID);
if (!empty ($searchByID))
{
   $where .= " AND {$tables['comment']['name']}.ID LIKE ".$db->qstr('%'.$searchByID.'%');
   $_REQUEST['searchbyid'] = $searchByID;
}

if (isset ($_REQUEST['status']) && preg_match ('`^[\d]+$`', $_REQUEST['status']))
{
   $_REQUEST['status'] = intval ($_REQUEST['status']);
   $where .= " AND {$tables['comment']['name']}.STATUS = ".$db->qstr($_REQUEST['status']);
}

$orderBy = ' ORDER BY ';
if (defined ('SORT_FIELD') && SORT_FIELD != '')
{
   $orderBy .= (SORT_FIELD == "TITLE" ? "{$tables['comment']['name']}.ID" : "{$tables['comment']['name']}.".SORT_FIELD)." ".SORT_ORDER;
}

require_once '../include/search.php';
$search_preferences = array ();
$search_preferences['Select_Options']  = array ();
$search_preferences['Where_Options']   = array (" `TYPE` = 1 ");
$search_preferences['From_Table'] = $tables['comment']['name'];
$search_preferences['Order_Options']   = array ();
$search_preferences['Search_Location'] = array ( "{$tables['link']['name']}.TITLE",
                                                 "{$tables['comment']['name']}.COMMENT",
                                                 "{$tables['comment']['name']}.USER_NAME"
                                                 );
//$search_preferences['BooleanSearchActive'] = 1;

$CommentsResults = search($search_preferences);
$search       = (!empty ($CommentsResults['search']) ? $CommentsResults['search'] : '');
$tpl->assign('search', $search);

if ($_SESSION['phpld']['adminpanel']['is_admin'])
{
   $sql = "SELECT {$tables['comment']['name']}.*, {$tables['link']['name']}.TITLE FROM `{$tables['comment']['name']}` LEFT JOIN `{$tables['link']['name']}` ON ({$tables['comment']['name']}.ITEM_ID = {$tables['link']['name']}.ID) AND {$tables['comment']['name']}.TYPE = '1' WHERE 1 ".(!empty ($CommentsResults['Search_Query']) ? " AND ".$CommentsResults['Search_Query'] : '').(!empty ($CommentsResults['Relevancy_Tuning']) ? $CommentsResults['Relevancy_Tuning'] : '')." {$where} {$orderBy}";
   $sql_count = "SELECT COUNT(*) FROM `{$tables['comment']['name']}` LEFT JOIN `{$tables['link']['name']}` ON ({$tables['comment']['name']}.ITEM_ID = {$tables['link']['name']}.ID) WHERE 1 ".(!empty ($CommentsResults['Search_Query']) ? " AND ".$CommentsResults['Search_Query'] : '').(!empty ($CommentsResults['Relevancy_Tuning']) ? $CommentsResults['Relevancy_Tuning'] : '')." {$where}";

   $rs = $db->SelectLimit($sql, LINKS_PER_PAGE, ($current_item <= 1 ? '0' : $current_item - 1));

   if ($rs === false)
      $list = array ();
   else
      $list = $rs->GetAssoc(true);
}
	//$lc = $db->Execute($sql);
   //$list_total = $lc->RecordCount();
   $list_total = $db->GetOne($sql_count);
   $tpl->assign('list_total', $list_total);
// Start Paging
SmartyPaginate :: connect(); // Connect Paging
SmartyPaginate :: setPageLimit(LINKS_PER_PAGE); // Set default number of page groupings

// Build Paging
if ($page < 2)
{
   SmartyPaginate :: disconnect();
   SmartyPaginate :: reset     ();
}

$list_total     = (!empty ($list_total) && $list_total >= 0      ? intval ($list_total)     : 0);
$PagerGroupings = (PAGER_GROUPINGS      && PAGER_GROUPINGS > 0   ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (LINKS_PER_PAGE       && LINKS_PER_PAGE  > 0   ? intval (LINKS_PER_PAGE)  : 10);

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

$min_keyword_length_comment = _L("Keywords shorter than #MIN_WORD_LENGTH# characters are ignored because of MySQL server settings.");
$min_keyword_length         = (!empty ($CommentsResults['Min_Word_Length']) ? $CommentsResults['Min_Word_Length'] : '4');
$min_keyword_length_comment = str_replace ('#MIN_WORD_LENGTH#', $min_keyword_length, $min_keyword_length_comment);
$tpl->assign('min_keyword_length_comment', $min_keyword_length_comment);

$tpl->assign('list', $list);

$tpl->assign('range', $range);
$tpl->assign('error', $error);
$tpl->assign('action', (!empty ($_REQUEST['action']) ? $_REQUEST['action'] : ''));


$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_link_comments.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>
