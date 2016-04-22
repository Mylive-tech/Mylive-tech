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

//Determine columns
$columns = array ('ID', 'NAME', 'DESCRIPTION', 'PRICE', 'STATUS', 'ACTION');

$stats = array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'));

$where = '';

if (!empty( $_GET['iSortCol_0'] ) ) {
	$sOrder = "ORDER BY  ";
	for ( $i=0 ; $i<mysql_real_escape_string( $_GET['iSortingCols'] ) ; $i++ ) {
		$sOrder .= $columns[mysql_real_escape_string( $_GET['iSortCol_'.$i] )]." ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
	}
	$sOrder = substr_replace( $sOrder, "", -2 );
}

/* Searching */

if ( $_GET['sSearch'] != "" ) {
	$search_text = mysql_real_escape_string( $_GET['sSearch'] );
	$where .= "WHERE ({$tables['link_type']['name']}.NAME LIKE '%{$search_text}%'";
}

$display_length = mysql_real_escape_string($_GET['iDisplayLength']);
$display_start = mysql_real_escape_string($_GET['iDisplayStart']);

$links_per_page = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

$links_per_page = ($display_length > 0) ? $display_length : $links_per_page;
$current_item = ($display_start > 0) ? $display_start : '0';

if ($_SESSION['phpld']['adminpanel']['is_admin'])
{
   $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link_type']['name']}` {$where}");

   $sql = "SELECT SQL_CALC_FOUND_ROWS `ID`, `NAME`, `STATUS`, `DESCRIPTION`, `PRICE` FROM `{$tables['link_type']['name']}` {$where} {$sOrder}";
   
   $rs = $db->SelectLimit($sql, $links_per_page, $current_item);

   $filtered_total = $db->GetOne("SELECT FOUND_ROWS()");
   
   if ($rs === false)
      $list = array ();
   else
      $list = $rs->GetAssoc(true);
}

$data = array(
		'sEcho' 								=> intval($_GET['sEcho']),
		'iTotalRecords' 					=> $list_total,
		'iTotalDisplayRecords' 	=> $filtered_total,
		'aaData'								=> $list
);

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
				case 'DESCRIPTION':
					$val = clean_string_paranoia($val);
					break;
			}
			$data .= ($col_name != 'ID') ? '"'.stripStrForDT($val).'",' : '';
		}
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