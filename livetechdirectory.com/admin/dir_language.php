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

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER'])) {
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];
}

if (isset($_SESSION['wid_message'])) {
	$tpl->assign('wid_message', $_SESSION['wid_message']);
	unset($_SESSION['wid_message']);
}

if (isset($_SESSION['wid_error'])) {
	$tpl->assign('wid_error', $_SESSION['wid_error']);
	unset($_SESSION['wid_error']);
}

if ($_REQUEST['search']) {
	$search_sql = " WHERE `VALUE` LIKE('%{$_REQUEST['search']}%') OR `ID` = '{$_REQUEST['search']}'";
	$tpl->assign('search', $_REQUEST['search']);
}

$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

$where = '';

$columns = array ('ID' => _L('ID'), 'LANG' => _L('Language'), 'VALUE' => _L('Text'), 'ACTION'=>_L('Action'));
$tpl->assign('columns', $columns);

$tpl->assign('col_count', count($columns));

//Correct value for ID
$id = (isset ($id) ? intval ($id) : 0);
$id = ($id < 0 ? 0 : $id);

$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['lang']['name']}` {$search_sql}");

$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

// Determine current index
$page         = ceil ($current_item / $LinksPerPage); // Determine page
$limit        = ' LIMIT '.($current_item <= 1 ? '0' : $current_item).', '.$LinksPerPage;

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

$search_sql = $search_sql ? $search_sql." AND `LANG` = '".FRONTEND_LANG."'" : " WHERE `LANG` = '".FRONTEND_LANG."'";

$list = $db->GetAll("SELECT * FROM `{$tables['lang']['name']}` {$search_sql} {$limit}");

$tpl->assign('list', $list);

$langs = select_lang('../lang/');
$tpl->assign('languages', $langs);
	
$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_language.tpl');

$tpl->assign('error'    , $error);

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>