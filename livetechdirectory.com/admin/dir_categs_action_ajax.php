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

$categs_id = explode(',', $_POST['multiselect_categs']);
$selectedCategs = (!empty ($categs_id) && is_array ($categs_id)) ? $categs_id : '';

switch ($submitAction) {
	case 'changeparent':
         $parentID = (isset ($_POST['parent_id']) && preg_match ('`^[\d]+$`', $_POST['parent_id']) ? intval ($_POST['parent_id']) : '');
         $debug = $parentID;
         if (!empty ($selectedCategs) && $parentID !== false)
         {
            foreach ($selectedCategs as $key => $categID)
            {
               $ActionStatus = categChangeParent($categID, $parentID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);
            }
         } else {
         	$debug = "No parent selected.";
         }

         //Parent category auto-selection back to "Top"
         $parentID = 0;
         break;
         
	case 'remove':
		 if (!empty ($selectedCategs)) {
            foreach ($selectedCategs as $key => $categID)
            {
               $ActionStatus = RemoveCategory($categID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);
            }
         }
		break;
         
   case 'removecomplete' :
         if (!empty ($selectedCategs))
         {
            foreach ($selectedCategs as $key => $categID)
            {
               $ActionStatus = RemoveCategoryAndContent($categID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);
            }
         }
      break;

   case 'title':
   		$eip = true;
   		$id = $_POST['id'];
   		$value = $_POST['value'];
   		$ActionStatus['status'] = $db->Execute("UPDATE `{$tables['category']['name']}` SET `TITLE` = '{$value}' WHERE `ID` = ".$db->qstr($id));
   		$callback_data = $value;
   	break;
   
   case 'hits':
   		$eip = true;
   		$id = $_POST['id'];
   		$value = $_POST['value'];
   		$ActionStatus['status'] = $db->Execute("UPDATE `{$tables['category']['name']}` SET `HITS` = '{$value}' WHERE `ID` = ".$db->qstr($id));
   		$callback_data = $value;
   	break;
         
   case 'active'   :
   case 'inactive' :

   default :
      $newStatus = -1;
      if ($submitAction == 'active') $newStatus = 2;
      elseif ($submitAction == 'inactive') $newStatus = 0;
      if (!empty ($selectedCategs) && in_array ($newStatus, $validCategStatus))
      {
         foreach ($selectedCategs as $key => $categID)
         {
            $ActionStatus = SetNewCategoryStatus($categID, $newStatus, 0);
         }
      }
      break;
}

$status = ($ActionStatus['status'] == 1) ? '1' : ' ';

// Edit in place require callback new data
if ($status == '1' && $eip) 
	echo $callback_data;
else
	echo $status;

?>