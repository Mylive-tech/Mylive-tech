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

$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (MAILS_PER_PAGE  && MAILS_PER_PAGE  > 0 ? intval (MAILS_PER_PAGE)  : 10);

$columns = array ('TITLE' => _L('Title'), 'URL' => _L('URL'), 'NAME' => _L('Name'), 'EMAIL' => _L('Email'), 'DATE_SENT' => _L('Date Sent'));
$tpl->assign('columns', $columns);

$tpl->assign('col_count', count ($columns) + 3);

//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);

if (isset ($_REQUEST['filter']) || isset ($_REQUEST['email']))
{
   $SESSION['email_view_sd'] = $sd = mktime (0 , 0 , 0 , $_REQUEST['SDMonth'], $_REQUEST['SDDay'], $_REQUEST['SDYear']);
   $SESSION['email_view_ed'] = $ed = mktime (23, 59, 59, $_REQUEST['EDMonth'], $_REQUEST['EDDay'], $_REQUEST['EDYear']);

   $filterParams = array ('SDMonth', 'SDDay', 'SDYear', 'EDMonth', 'EDDay', 'EDYear');
   foreach ($columnURLs as $key => $column)
   {
      foreach ($filterParams as $filterKey => $param)
      {
         $columnURLs[$key] .= (!empty ($_REQUEST[$param]) ? "&amp;{$param}=".trim (urlencode (urldecode ($_REQUEST[$param]))) : '');
      }
      $columnURLs[$key] .= "&amp;filter=1";
   }
}

$tpl->assign('columnURLs', $columnURLs);

if (isset ($SESSION['email_view_sd']) && isset ($SESSION['email_view_ed']))
{
	$where = "WHERE `DATE_SENT` BETWEEN ".$db->DBTimeStamp($sd)." AND ".$db->DBTimeStamp($ed);
	$tpl->assign('SD', $SESSION['email_view_sd']);
	$tpl->assign('ED', $SESSION['email_view_ed']);
}

if (isset ($_REQUEST['email']))
{
   $sql = "SELECT * FROM `{$tables['email']['name']}` {$where} {$orderBy}";
	$rs = $db->Execute($sql);
	$list = $rs->GetAssoc(true);
	$tpl->assign('list', $list);
	echo $tpl->fetch(ADMIN_TEMPLATE.'/email_sent_rpt_txt.tpl');
	exit();
}

// Determine current index
$current_item = (!empty ($_REQUEST['p']) && preg_match ('`^[\d]+$`', $_REQUEST['p']) ? intval ($_REQUEST['p']) : 1);
$page         = ceil ($current_item / $LinksPerPage); // Determine page
$limit        = ' LIMIT '.($current_item <= 1 ? '0' : $current_item).', '.$LinksPerPage;

$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['email']['name']}` {$where}");
$tpl->assign('list_total', $list_total);

$sql = "SELECT * FROM `{$tables['email']['name']}` {$where} {$orderBy}";
$rs = $db->SelectLimit($sql, $LinksPerPage, ($current_item <= 1 ? '0' : $current_item));

if ($rs === false)
   $list = array ();
else
   $list = $rs->GetAssoc(true);

$tpl->assign('list', $list);

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

$content = $tpl->fetch(ADMIN_TEMPLATE.'/email_sent_view.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>