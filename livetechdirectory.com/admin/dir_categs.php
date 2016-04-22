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

$columns = array ('ID' => _L('ID'), 'TITLE'=> _L('Title'), 'TITLE_URL'=> _L('Title Url'), 'PARENT'=> _L('Parent'), 'URL'=> _L('Symbolic'), 'STATUS'=> _L('Status'), 'HITS'=> _L('Hits'), 'DATE_ADDED'=> _L('Date Added'), 'ACTION'=> _L('Action'), 'PARENT_ID'=> _L('Parent ID'));

$tpl->assign('columns', $columns);

if ($_REQUEST['action'] == "update_RSS") {
	$result = saveRSS_feeds();// ? "RSS feed updated." : "There was an error updating the RSS feeds";
	$url = DOC_ROOT."/dir_categs.php?r=1";
	
	//http_custom_redirect($url);
	$tpl->assign('succesMsg', 'RSS feeds updated');
	$tpl->assign('errMsg', 'There was a problem updating the RSS feeds');
	$tpl->assign('err', $result);
	
}

if ($_REQUEST['action'] == "rebuild_cache" ) : //Load just cache rebuilding and not complete page

   $start_query     = (!empty ($_REQUEST['start']) ? intval ($_REQUEST['start']) : 0);
    
   $total_processed = (!empty ($_REQUEST['total_processed']) ? intval ($_REQUEST['total_processed']) : 0);
   
   $total           = (!empty ($_REQUEST['total']) ? intval ($_REQUEST['total']) : 0);

   $processes = 0;

   //Recalculate total links to be processed
   //in case it could be not determined from the URL
   if (empty ($total))
      $total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}`");

   $categIDs = $db->GetAll("SELECT `ID`, `DATE_ADDED` FROM `{$tables['category']['name']}` ORDER BY `ID` ASC LIMIT {$start_query}, {$range}");

   foreach($categIDs as $key => $categ)
   {
      $linksCount = $db->getOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE CATEGORY_ID = ".$categ['ID']);
      $data = array (
                  'CACHE_TITLE' => trim (buildCategUrlTitle($categ['ID'])) ,
                  'CACHE_URL'   => trim (buildCategUrl($categ['ID']))      ,
                  'DATE_ADDED'  => $categ['DATE_ADDED'],
                  'LINK_COUNT'  => $linksCount,
               );

      $where = " `ID` = ".$db->qstr($categ['ID']);

      if ($db->AutoExecute($tables['category']['name'], $data, 'UPDATE', $where))
         $processes++;

      $total_processed++;

      //Free Memory
      unset ($categIDs[$key], $categ, $data, $where);
   }

   // Building redirect
   $next_start_query = $start_query + $range;
   
   if ($next_start_query <= $total)
   {
       
      $url  = DOC_ROOT."/dir_categs.php?action=rebuild_cache";
      $url .= "&range={$range}";
      $url .= "&start={$next_start_query}";
      $url .= "&total_processed={$total_processed}";
      $url .= "&total={$total}";

      $build_type = _L('Rebuild Category Cache');

      $title_msg  = $build_type;

      $cust_msg   = '<p>';
      $cust_msg  .= $build_type;
      $cust_msg  .= '</p>';

      $cust_msg  .= '<p>'._L('Starting at link').': <span class="important">'.$start_query.'</span></p>';
      $cust_msg  .= '<p>'._L('Stopping at link').': <span class="important">'.$next_start_query.'</span></p>';
      $cust_msg  .= '<p>'._L('Queries performed (this session/total sessions)').': <span class="important">'.$processes.'/'.$total_processed.'</span></p>';
      $cust_msg  .= '<p>'._L('Total queries to process').': <span class="important">'.$total.'</span></p>';
      $cust_msg  .= '<p>'._L('Number of processes per cycle').': <span class="important">'.$range.'</span></p>';

      $cust_msg  .= '<p class="notice">'._L('Depending on the size of information to be processed, this action can take some time.').'</p>';

      $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg);
      $tpl->assign('redirect', $redirect);
   }
   else
   {
      $url = DOC_ROOT."/dir_categs.php?r=1";
      $title_msg = _L('Category cache rebuild status').': '._L('Complete');
      $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
      $tpl->assign('redirect', $redirect);
   }

else : //Regular Category page

$error = 0;

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

//Build category tree
if (AJAX_CAT_SELECTION_METHOD == 0)
{
   $tpl->assign('categs', get_categs_tree());
}

//Load and run multiple link actions
require_once 'categ_multi_action.php';

if (empty ($_POST['submit'])) {
//   SmartyValidate :: connect($tpl);
//
//   SmartyValidate :: register_form('rebuild_cache', true);
//   SmartyValidate :: register_validator("v_range", "range", "isNumber", false, false, 'trim', 'rebuild_cache');
} else {
//   SmartyValidate :: connect($tpl);
//   if (SmartyValidate :: is_valid($_POST, 'rebuild_cache') && !empty ($_POST['submit'])) {
      $total    = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}`");
      $url      = DOC_ROOT."/dir_categs.php?action=rebuild_cache&start=0&range={$_POST['range']}&total={$total}";

      $cust_msg = '<p class="notice">'._L('Depending on the size of information to be processed, this action can take some time.').'</p>';

      $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, '', $cust_msg);
      $tpl->assign('redirect', $redirect);
//   }
}

$where = '';
if (isset ($_REQUEST['parent'])) {
   $_REQUEST['parent'] = urldecode ($_REQUEST['parent']);

   //Clean multiple spaces, commas, etc
   $pattern  = array ( '/\s/', '`[,]+`', '`^[,]*`', '`[,]+$`' );
   $replace  = array ( ''    , ','     , ''       , ''        );
   $_REQUEST['parent'] = preg_replace ($pattern, $replace, $_REQUEST['parent']);

   $tpl->assign('parent', $_REQUEST['parent']);
}

if (isset ($_REQUEST['category'])) {
   $_REQUEST['category'] = urldecode ($_REQUEST['category']);

   //Clean multiple spaces, commas, etc
   $pattern  = array ( '/\s/', '`[,]+`', '`^[,]*`', '`[,]+$`' );
   $replace  = array ( ''    , ','     , ''       , ''        );
   $_REQUEST['category'] = preg_replace ($pattern, $replace, $_REQUEST['category']);

   $category = (!empty ($_REQUEST['category']) ? $_REQUEST['category'] : '');
   $tpl->assign('category', $category);
}

if (isset ($_REQUEST['status']) && preg_match ('`^[\d]+$`', $_REQUEST['status']))
{
   $_REQUEST['status'] = intval ($_REQUEST['status']);
   $tpl->assign('status', $_REQUEST['status']);
}

$search       = (!empty ($_REQUEST['search']) ? $_REQUEST['search'] : '');
$tpl->assign('search', $search);


endif;

$tpl->assign('range', $range);
$tpl->assign('error', $error);
$tpl->assign('action', (!empty ($_REQUEST['action']) ? $_REQUEST['action'] : ''));


$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_categs_dt.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>
