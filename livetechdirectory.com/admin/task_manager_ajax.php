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

require_once 'init_ajax.php';

require_once 'libs/TaskManager/Task.php';


$action = @$_REQUEST['action'];

$id = @intval($_REQUEST['id']);


if (($action && $id))
{
	
	
switch ($action) {
	
case 'change_status':
			$new_status = intval($_REQUEST['value']);
	
if ($new_status == 1) {
				
$result = $db->Execute("UPDATE `{$tables['task']['name']}` SET `STATUS` = '1' WHERE `ID` = ".$db->qstr($id));

} else {
				
$result = $db->Execute("UPDATE `{$tables['task']['name']}` SET `STATUS` = '2' WHERE `ID` = ".$db->qstr($id));
			
}
			
break;	
		
	
}
	
	
$result = ($result) ? 1 : 0;
	
	
echo $result;
	
	
exit;

}

?>