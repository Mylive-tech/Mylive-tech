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
$columns = array ('ID', 'TITLE' , 'COUNT' , 'STATUS' , 'ACTION');

$stats = array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'));
$yes_no = array (1 => _L('Yes'), 0 => _L('No'));

$where = '1=1';


if (isset ($_REQUEST['status']) && preg_match ('`^[\d]+$`', $_REQUEST['status']))
{
    $_REQUEST['status'] = intval ($_REQUEST['status']);
    $where .= " AND {$tables['tags']['name']}.STATUS = ".$db->qstr($_REQUEST['status']);
    $tpl->assign('status', $_REQUEST['status']);
}

$links_per_page = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

if ( isset( $_GET['iSortCol_0'] ) ) {
    $sOrder = "ORDER BY  ";
    for ( $i=0 ; $i<mysql_real_escape_string( $_GET['iSortingCols'] ) ; $i++ ) {
        $sOrder .= $columns[mysql_real_escape_string( $_GET['iSortCol_'.$i] )]." ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
    }
    $sOrder = substr_replace( $sOrder, "", -2 );
}

/* Searching */

if ( $_GET['sSearch'] != "" ) {
    $search_text = mysql_real_escape_string( $_GET['sSearch'] );
    $where .= "AND ( {$tables['tags']['name']}.URL LIKE '%{$search_text}%' OR ".
        "{$tables['tags']['name']}.TITLE LIKE '%{$search_text}%' OR ";
}

$display_length = mysql_real_escape_string($_GET['iDisplayLength']);
$display_start = mysql_real_escape_string($_GET['iDisplayStart']);

$links_per_page = ($display_length > 0) ? $display_length : $links_per_page;
$current_item = ($display_start > 0) ? $display_start : '0';


$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['tags']['name']}` WHERE {$where}");

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$tables['tags']['name']}` WHERE  {$where}  {$sOrder}";
//die($sql);

$rs = $db->SelectLimit($sql, $links_per_page, $current_item);

$filtered_total = $db->GetOne("SELECT FOUND_ROWS()");

if ($rs === false) {

    $list = array ();
} else {
    $list = $rs->GetAssoc(true);
}

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
                case 'ACTION':
                    //$val = "<a href=\\\"".DOC_ROOT."/dir_links_edit.php?action=E:{$link_id}\\\" title=\\\"Edit Link: {$link['TITLE']}\\\" class=\\\"edit_dt\\\"><span>Edit</span></a><a href=\\\"".DOC_ROOT."/dir_links_edit.php?action=D:{$link_id}\\\" title=\\\"Remove Link: {$link['TITLE']}\\\" class=\\\"delete_dt\\\" ><span>Delete</span></a>";
                    //$val = '1';
                    $val = $link['ID'];
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