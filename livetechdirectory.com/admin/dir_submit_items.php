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

if ($_REQUEST['type'] && $_REQUEST['type'] != '')
{
   $ltype    = ($_REQUEST['type'] < 0 ? 0 : intval ($_REQUEST['type']));
   $tpl->assign('ltype', strtoupper ($ltype));
}

$search       = (!empty ($_REQUEST['search']) ? $_REQUEST['search'] : '');
$tpl->assign('search', $search);


if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];
   
$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

$where = '';

//Determine columns
if (isset($ltype)) {
	$columns = array ('ID', 'ORDER_ID' => _L('Order ID'), 'NAME' => _L('Name'), 'FIELD_NAME' => _L('Field Name'), 'IS_DETAIL'=>_L('Details Visible'), 'REQUIRED'=>_L('Required'), 'STATUS'=>_L('Status'), );
	$tpl->assign('columns', $columns);
	
} else {
	$columns = array ('ID', 'ORDER_ID' => _L('Order ID'), 'NAME' => _L('Name'), 'FIELD_NAME' => _L('Field Name'), 'ACTION'=>_L('Action'));
	$tpl->assign('columns', $columns);
}
//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);
$tpl->assign('columnURLs', $columnURLs);

$tpl->assign('col_count', count($columns) + 3);


$tpl->assign($_POST);
$tpl->assign('error'    , $error);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_submit_items_dt.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>