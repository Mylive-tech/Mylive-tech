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

$submitAction = (!empty ($_POST['submitAction']) ? strtolower ($_POST['submitAction']) : '');

$submitAction = str_replace(" ", "", $submitAction);

/*if (empty($submitAction))
	exit;
*/

$link_type_id = intval($_POST['tid']);

$links_id = explode(',', $_POST['multiselect_links']);
$selectedLinks = (!empty ($links_id) && is_array ($links_id)) ? $links_id : '';

switch ($submitAction) {
         
   case 'active'   :
   case 'pending'  :
   case 'inactive' :

   default :
      $newStatus = -1;
      if ($submitAction == 'active') $newStatus = 2;
      elseif ($submitAction == 'pending') $newStatus = 1;
      elseif ($submitAction == 'inactive') $newStatus = 0;
		$debug = $submitAction.'--';
      if (!empty ($selectedLinks) && in_array ($newStatus, $validLinkStatus))
      {
         foreach ($selectedLinks as $key => $linkID)
         {
            $ActionStatus = $db->Execute("UPDATE `{$tables['submit_item_status']['name']}` SET `STATUS` = '{$newStatus}' WHERE `ITEM_ID` = '{$linkID}' AND `LINK_TYPE_ID` = '{$link_type_id}'");
         }
      }
      break;
}

//$debug = $links_id;

$status = ($ActionStatus == 1) ? '1' : 'error:'.$debug;

// Edit in place require callback new data
if ($status == '1' && $eip) 
	echo $callback_data;
else
	echo $status;

?>