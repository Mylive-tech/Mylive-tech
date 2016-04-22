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

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

//Build category tree
if (AJAX_CAT_SELECTION_METHOD == 0)
{
   $tpl->assign('categs', get_categs_tree());
}

//Load and run multiple comment actions
require_once 'link_comment_multi_action.php';

$tpl->assign('stats'  , array (0       => _L('Inactive'), 1             => _L('Pending')    , 2        => _L('Active')));

//Determine columns
$columns = array ('TITLE' => _L('Link Title') , 'COMMENT' => _L('Comment'), 'STATUS' => _L('Status'), 'USER_NAME' => _L('Username'), 'IPADDRESS' => _L('IP'), 'DATE_ADDED' => _L('Date Added'));
$tpl->assign('columns', $columns);

//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);
$tpl->assign('columnURLs', $columnURLs);

$where = " AND {$tables['comment']['name']}.`TYPE` = 1";

$orderBy = ' ORDER BY ';
if (defined('SORT_FIELD') && SORT_FIELD != '')
{
   $orderBy .= (SORT_FIELD == "TITLE" ? "{$tables['comment']['name']}.ID" : "{$tables['comment']['name']}.".SORT_FIELD)." ".SORT_ORDER;
}

// Determine current index
$page         = ceil ($current_item / LINKS_PER_PAGE); // Determine page
$limit        = ' LIMIT '.($current_item <= 1 ? '0' : $current_item).', '.LINKS_PER_PAGE;

$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['comment']['name']}` WHERE `STATUS` = ".$db->qstr(1)." {$where}");
$tpl->assign('list_total', $list_total);

$sql = "SELECT {$tables['comment']['name']}.*, {$tables['link']['name']}.TITLE FROM `{$tables['comment']['name']}` LEFT JOIN `{$tables['link']['name']}` ON ({$tables['comment']['name']}.ITEM_ID = {$tables['link']['name']}.ID)  WHERE {$tables['comment']['name']}.STATUS = '1' ".$where.$orderBy;

$rs = $db->SelectLimit($sql, LINKS_PER_PAGE, ($current_item <= 1 ? '0' : $current_item - 1));

if ($rs === false)
   $list = array ();
else
   $list = $rs->GetAssoc(true);

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

$tpl->assign('error', $error);
$tpl->assign('list', $list);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_approve_link_comments.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>