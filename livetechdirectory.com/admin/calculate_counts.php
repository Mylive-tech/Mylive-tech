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
 # @version        5.0.0 Series. See /include/version.php for actual release number
 # ################################################################################
 */


require_once 'init.php';
$rebuild = isset($_GET['rebuild']);
$range = (preg_match ('`^[\d]+$`', $_REQUEST['range']) && $_REQUEST['range'] > 0 ? intval ($_REQUEST['range']) : 500);



if ($rebuild) { //Load just count rebuilding and not complete page
   $start_query     = (!empty ($_REQUEST['start']) ? intval ($_REQUEST['start']) : 0);
   $total_processed = (!empty ($_REQUEST['total_processed']) ? intval ($_REQUEST['total_processed']) : 0);
   $total           = (!empty ($_REQUEST['total']) ? intval ($_REQUEST['total']) : 0);

   $processes = 0;
   
   //Recalculate total links to be processed
   //in case it could be not determined from the URL
   if (empty ($total))
      $total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}`");

   $categIDs = $db->GetAll("SELECT * FROM `{$tables['category']['name']}` ORDER BY `ID` ASC LIMIT {$start_query}, {$range}");

   
   foreach ($categIDs as $topCat) {
		$additional_links = array();
		$add_links = $db->GetAll("SELECT `LINK_ID`  FROM `{$tables['additional_category']['name']}` WHERE `CATEGORY_ID` = ".$db->qstr($topCat['ID']));
		foreach ($add_links as $add_link)
		    $additional_links[] = $add_link['LINK_ID'];
		$additional_links = implode(",", $additional_links);
		$additional_links = !$additional_links ? '0' : $additional_links;
		$count    = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE (`STATUS` = '2') AND (`CATEGORY_ID` = ".$db->qstr($topCat['ID'])." OR `ID` IN ({$additional_links}))");
		$categs_count =  $db->getOne("SELECT COUNT(*) AS cnt FROM `{$tables['category']['name']}` WHERE PARENT_ID = ".$topCat['ID']);
		$all_count = $count +  $categs_count;
		
		
	$db->execute("UPDATE `{$tables['category']['name']}` SET `COUNT` = ".$all_count." WHERE ID = ".$topCat['ID']);
        
        $db->execute("UPDATE `{$tables['category']['name']}` SET `LINK_COUNT` = ".$count." WHERE ID = ".$topCat['ID']);
	
	$processes++;
	 $total_processed++;
	 
	  unset ($add_links,$additional_links);
   }
   
   

    $tags = $db->getAll('SELECT TAG_ID, COUNT(TAG_ID) as COUNT FROM PLD_TAGS_LINKS GROUP BY TAG_ID');
    foreach ($tags as $tag) {
        //die('UPDATE PLD_TAGS SET `COUNT` = '.$tag['COUNT'].' WHERE ID = '.$tag['TAG_ID']);
        $db->execute('UPDATE PLD_TAGS SET `COUNT` = '.$tag['COUNT'].' WHERE ID = '.$tag['TAG_ID']);
    }

 
   // Building redirect
   $next_start_query = $start_query + $range;
   
   if ($next_start_query <= $total)
   {
       
      $url  = DOC_ROOT."/calculate_counts.php?rebuild=rebuild_counts";
      $url .= "&range={$range}";
      $url .= "&start={$next_start_query}";
      $url .= "&total_processed={$total_processed}";
      $url .= "&total={$total}";

      $build_type = _L('Rebuild  Categories Count');

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
      $url = DOC_ROOT."/calculate_counts.php?r=1";
      $title_msg = _L('Categories Count rebuild status').': '._L('Complete');
      $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
      $tpl->assign('redirect', $redirect);
    }


}

$tpl->assign('rebuild', (!empty ($_REQUEST['rebuild']) ? $_REQUEST['rebuild'] : ''));
$content = $tpl->fetch(ADMIN_TEMPLATE.'/calculate_counts.tpl');
$tpl->assign('content', $content);

echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');