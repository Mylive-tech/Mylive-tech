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
 # Copyright (C) 2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
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

//Determine columns
$columns = array ('ID', 'TITLE' , 'CATEGORY' , 'STATUS' , 'PAGERANK', 'HITS', 'DATE_ADDED', 'LINK_TYPE', 'ACTION', 'LINK_TYPE_ID', 'URL');

$stats = array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'));
$valid = array (0 => _L('Broken')  , 1 => _L('Unknown'), 2 => _L('Ok'),);
$yes_no = array (1 => _L('Yes'), 0 => _L('No'));
$link_types = $db->GetAssoc("SELECT `ID`, `NAME` FROM `{$tables['link_type']['name']}` ORDER BY `ORDER_ID` ASC");

$where = array();

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

//   //If editor, remove links he/she is not allowed to view
//   if (!$_SESSION['phpld']['adminpanel']['is_admin'])
//   {
      //Loop through each category
//      foreach ($linkIDArray as $key => $lid)
//      {
//         $cID = $db->GetOne("SELECT `CATEGORY_ID` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($lid));
//         if (!in_array ($cID, $_SESSION['phpld']['adminpanel']['permission_array']))
//         {
//            //Current link category is not in editors permissions,
//            //Remove from list
//            unset ($linkIDArray[$key]);
//         }
//      }
//   }

   if (!empty ($linkIDArray))
   {
      $where[] = "{$tables['link']['name']}.ID IN ('".implode ("', '", $linkIDArray)."')";

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
//   if (!$_SESSION['phpld']['adminpanel']['is_admin'])
//   {
//      //Loop through each category
//      foreach ($categsArray as $key => $cID)
//      {
//         if (!in_array ($cID, $_SESSION['phpld']['adminpanel']['permission_array']))
//         {
//            //Current category is not in editors permissions,
//            //Remove from list
//            unset ($categsArray[$key]);
//         }
//      }
//   }

   if (!empty ($categsArray))
   {
      $where[] = "{$tables['link']['name']}.CATEGORY_ID IN ('".implode ("', '", $categsArray)."')";
      
      $_REQUEST['category'] = implode (',', $categsArray);

      $category = (!empty ($_REQUEST['category']) ? $_REQUEST['category'] : '');
      $tpl->assign('category', $category);
   }
}
else
{
   $where[] = ($_REQUEST['f'] == '1' ? '' : 'NOT ').$expired_where_join;
}

if (!empty ($_REQUEST['expired']))
{
   $expired = 1;
   $where[] = "{$tables['link']['name']}.RECPR_EXPIRED = '1'";
   $tpl->assign('expired', 1);
}

if (isset ($_REQUEST['status']) && preg_match ('`^[\d]+$`', $_REQUEST['status']))
{
   $_REQUEST['status'] = intval ($_REQUEST['status']);
   $where[] = "{$tables['link']['name']}.STATUS = ".$db->qstr($_REQUEST['status']);
   $tpl->assign('status', $_REQUEST['status']);
}


if (isset ($_REQUEST['link_type']) && preg_match ('`^[\d]+$`', $_REQUEST['link_type']))
{
   $_REQUEST['link_type'] = intval ($_REQUEST['link_type']);
   $where[] = "{$tables['link']['name']}.LINK_TYPE = ".$db->qstr($_REQUEST['link_type']);
   $tpl->assign('link_type', $_REQUEST['link_type']);
}


if (isset ($_REQUEST['owner_id']) && preg_match ('`^[\d]+$`', $_REQUEST['owner_id']))
{
   $_REQUEST['owner_id'] = intval ($_REQUEST['owner_id']);
   $where[] = "{$tables['link']['name']}.OWNER_ID = ".$db->qstr($_REQUEST['owner_id']);
   $tpl->assign('owner_id', $_REQUEST['owner_id']);
}

if(SHOW_CONFIRMED_ONLY == 1 && EMAIL_CONFIRMATION==1)
$where[] = "{$tables['link']['name']}.OWNER_EMAIL_CONFIRMED = 1";

$links_per_page = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

$tpl->assign('featured', $_REQUEST['f'] == 1 ? 1 : 0);

if ( isset( $_GET['iSortCol_0'] ) ) {
	$sOrder = "ORDER BY  ";
	for ( $i=0 ; $i<mysql_real_escape_string( $_GET['iSortingCols'] ) ; $i++ ) {
		$sOrder .= $columns[mysql_real_escape_string( $_GET['iSortCol_'.$i] )]." ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
	}
	$sOrder = substr_replace( $sOrder, "", -2 );
}

//$orderBy = "{$tables['link']['name']}.FEATURED DESC";

//if (defined ('SORT_FIELD') && SORT_FIELD != '')
//   $orderBy .= ", ". (SORT_FIELD == "CATEGORY" ? "{$tables['category']['name']}.TITLE" : "{$tables['link']['name']}.".SORT_FIELD)." ".SORT_ORDER;

/* Searching */

if ( $_GET['sSearch'] != "" ) {
	$search_text = mysql_real_escape_string( $_GET['sSearch'] );
	$where[] = "( {$tables['link']['name']}.URL LIKE '%{$search_text}%' OR ".
		                	"{$tables['link']['name']}.TITLE LIKE '%{$search_text}%' OR ".
		               	"{$tables['link']['name']}.DESCRIPTION LIKE '%{$search_text}%' OR ".
		               	"{$tables['link']['name']}.ID LIKE '%{$search_text}%') ";
}

$display_length = mysql_real_escape_string($_GET['iDisplayLength']);
$display_start = mysql_real_escape_string($_GET['iDisplayStart']);

$links_per_page = ($display_length > 0) ? $display_length : $links_per_page;
$current_item = ($display_start > 0) ? $display_start : '0';


//$left_join_categ = " LEFT JOIN `{$tables['category']['name']}` ON ({$tables['link']['name']}.CATEGORY_ID = {$tables['category']['name']}.ID) ";

//if ($_SESSION['phpld']['adminpanel']['is_admin'])
//{
    $where = implode(' AND ', $where);
   $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '')." {$where}");

   $sql = "SELECT SQL_CALC_FOUND_ROWS {$tables['link']['name']}.*, ".$db->IfNull("{$tables['category']['name']}.TITLE", "'Top'")." AS `CATEGORY` ".(!empty ($LinksResults['Select_Relevancy']) ? ", ".$LinksResults['Select_Relevancy'] : '')." FROM `{$tables['link']['name']}` LEFT JOIN `{$tables['category']['name']}` ON ({$tables['link']['name']}.CATEGORY_ID = {$tables['category']['name']}.ID) WHERE ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '').(!empty ($LinksResults['Relevancy_Tuning']) ? $LinksResults['Relevancy_Tuning'] : '')." {$where}  {$sOrder}";
   $rs = $db->SelectLimit($sql, $links_per_page, $current_item);
   
   $filtered_total = $db->GetOne("SELECT FOUND_ROWS()");
   
   if ($rs === false)
      $list = array ();
   else
      $list = $rs->GetAssoc(true);
//}
//else
//{
//   $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` {$left_join_categ} WHERE 1 ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '')." {$where} AND 1 ");
//
//   $sql = "SELECT {$tables['link']['name']}.*, {$tables['category']['name']}.TITLE AS `CATEGORY` ".(!empty ($LinksResults['Select_Relevancy']) ? ", ".$LinksResults['Select_Relevancy'] : '')." FROM `{$tables['link']['name']}` {$left_join_categ} WHERE 1 ".(!empty ($LinksResults['Search_Query']) ? " AND ".$LinksResults['Search_Query'] : '').(!empty ($LinksResults['Relevancy_Tuning']) ? $LinksResults['Relevancy_Tuning'] : '')." {$where} AND 1 ORDER BY ".(!empty ($LinksResults['Relevancy_Order']) ? $LinksResults['Relevancy_Order'].", " : '')." {$orderBy}";
//	
//   $rs = $db->SelectLimit($sql, $LinksPerPage, ($current_item <= 1 ? '0' : $current_item - 1));
//
//   if ($rs === false)
//      $list = array ();
//   else
//      $list = $rs->GetAssoc(true);
//}
//unset ($sql, $rs);

//$cid = get_category($_SERVER['REQUEST_URI']);
//if ($cid == 0)
//{
//   $rss_link = false;
//}
//else
//{
//   $rss_link = true;
//   $tpl->assign('rsscategory', $cid);
//}


//$feat_link = ($_REQUEST['f'] == '1' ? 1 : 0);
//$categ     = (!empty ($_REQUEST['c']) ? $_REQUEST['c'] : 0);

//$HaveExpiredRecpr_sql = "SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `RECPR_EXPIRED` = '1'".($_REQUEST['f'] == 1 ? " AND `FEATURED` = '1'" : '').(!empty ($_REQUEST['c']) ? " AND `CATEGORY_ID` = ".$db->qstr($_REQUEST['c']) : '');
//$HaveExpiredRecpr = $db->GetOne($HaveExpiredRecpr_sql);
//$tpl->assign('HaveExpiredRecpr', $HaveExpiredRecpr);

//check if have links expired (according to email confirmation)
//if (EMAIL_CONFIRMATION == 1) {
//    $HaveExpiredEmail_sql = "SELECT COUNT(*) FROM `{$tables['link']['name']}`
//                                WHERE `OWNER_EMAIL_CONFIRMED` = '0'
//                                AND DATE_ADD(`DATE_ADDED`, INTERVAL ".WAIT_FOR_EMAIL_CONF." DAY) <= now()";
//  
//    $HaveExpiredEmail = $db->GetOne($HaveExpiredEmail_sql);
//    $tpl->assign('HaveExpiredEmail', $HaveExpiredEmail);
//}

$json = link_json($list, $columns, $list_total, $filtered_total);

echo $json;

function link_json($links, $columns, $total_count, $display_count) {
	global $db, $tables, $yes_no, $link_types, $stats;
	$data = '';
	foreach ($links as $link_id => $link) {
		// First element should be a link ID that we will set as tr id
		$data .= '["'.$link_id.'",';
		foreach ($columns as $col_id => $col_name) {
			$val = $link[$col_name];
			switch ($col_name) {
				case 'STATUS':
					$val = $stats[$val];
					break;
				case 'LINK_TYPE':
					$val = '<a href="'.DOC_ROOT.'/dir_links.php?status=2&link_type='.$val.'">'.$link_types[$val].'</a>';
					break;
				 case 'LINK_TYPE_ID':
					$val = $link['LINK_TYPE'];
					break;
				case 'PAGERANK':
					$val = ($val < 0) ? 'N/A' : $val;
					break; 
				case 'DATE_ADDED':
					$val = strftime("%m-%d-%y", strtotime($link['DATE_ADDED']));
					break;
				case 'OWNER_EMAIL_CONFIRMED':
					$val = $yes_no[$val];
					break;
				case 'ACTION':
					//$val = "<a href=\\\"".DOC_ROOT."/dir_links_edit.php?action=E:{$link_id}\\\" title=\\\"Edit Link: {$link['TITLE']}\\\" class=\\\"edit_dt\\\"><span>Edit</span></a><a href=\\\"".DOC_ROOT."/dir_links_edit.php?action=D:{$link_id}\\\" title=\\\"Remove Link: {$link['TITLE']}\\\" class=\\\"delete_dt\\\" ><span>Delete</span></a>";
					//$val = '1';
					$val = $link['CATEGORY_ID'];
				    break;
				case 'URL':
					$val =  $val;
					break; 
			}
			$data .= ($col_name != 'ID') ? '"'.stripStrForDT($val).'",' : '';
		}
		//$data .= '"'.$link['CATEGORY_ID'].'""';
		$data = substr($data, 0, -1);
		$data .= '],';
	}
	$data = substr($data, 0, -1);
	$result = '{
    							"sEcho": '.intval($_GET['sEcho']).',
    							"iTotalRecords": '.$total_count.',
    							"iTotalDisplayRecords": '.$display_count.',
    							"aaData": [
    											'.$data.'
    							]
    						}';
    						
    return $result;
}
?>