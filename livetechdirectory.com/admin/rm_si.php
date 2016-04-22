<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 

require_once 'init.php';

$id = $_REQUEST['id'];
$lid = $_REQUEST['lid'];

$si = $db->GetRow("SELECT * FROM `{$tables['submit_item']['name']}` WHERE `ID` = ".$db->qstr($id));
$li = getFullLinkInfo($lid);
$db->Execute("UPDATE `{$tables['link']['name']}` SET `".$si['FIELD_NAME']."`='' WHERE `ID`=".$db->qstr($lid));

if (file_exists(INSTALL_PATH.'/uploads/'.$li[$si['FIELD_NAME']])) {
    unlink(INSTALL_PATH.'/uploads/'.$li[$si['FIELD_NAME']]);
}
if (file_exists(INSTALL_PATH.'/uploads/thumb/'.$li[$si['FIELD_NAME']])) {
    unlink(INSTALL_PATH.'/uploads/thumb/'.$li[$si['FIELD_NAME']]);
}

if (isset ($_SESSION['return'])) {
    http_custom_redirect($_SESSION['return']);
} else {
    http_custom_redirect($_SERVER['HTTP_REFERER']);
}
?>
