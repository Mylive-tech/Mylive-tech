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

$links_id = explode(',', $_POST['multiselect_links']);
$selectedLinks = (!empty ($links_id) && is_array ($links_id)) ? $links_id : '';

switch ($submitAction) {
	case 'changecategory':
         $categID = (isset ($_POST['category_id']) && preg_match ('`^[\d]+$`', $_POST['category_id']) ? intval ($_POST['category_id']) : '');

         if (!empty ($selectedLinks) && $categID >= 0)
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus = linkChangeCateg($linkID, $categID);
            }
         }

         //Category auto-selection back to "Top"
         $categID = 0;
         break;
         
	case 'spamlink':
		 if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus1 = banDomain($linkID);
               $ActionStatus2 = banLinkSubmitIP($linkID);
               $ActionStatus3 = removeLink($linkID);
               if ($ActionStatus1['status'])
               	$ActionStatus['status'] = 1;
               else
               	$ActionStatus['status'] = 0;
            }
         }
		break;
		
	case 'remove' :
         if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus = RemoveLink($linkID);
            }
         }
         break;
         
   case 'featured' :
      if (!empty ($selectedLinks))
      {
         foreach ($selectedLinks as $key => $linkID)
         {
            $ActionStatus = MakeFeaturedLink($linkID);
         }
      }
      break;

   case 'regular' :
      if (!empty ($selectedLinks))
      {
         foreach ($selectedLinks as $key => $linkID)
         {
            $ActionStatus = MakeRegularLink($linkID);
         }
      }
      break;
      
   case 'title':
   		$eip = true;
   		$id = $_POST['id'];
   		$value = $_POST['value'];
   		$ActionStatus['status'] = $db->Execute("UPDATE `{$tables['link']['name']}` SET `TITLE` = '{$value}' WHERE `ID` = ".$db->qstr($id));
   		$callback_data = $value;
   	break;
   	
   case 'pr':
   		$eip = true;
   		$id = $_POST['id'];
   		$value = $_POST['value'];
   		$ActionStatus['status'] = $db->Execute("UPDATE `{$tables['link']['name']}` SET `PAGERANK` = '{$value}' WHERE `ID` = ".$db->qstr($id));
   		$callback_data = $value;
   	break;
   
   case 'hits':
   		$eip = true;
   		$id = $_POST['id'];
   		$value = $_POST['value'];
   		$ActionStatus['status'] = $db->Execute("UPDATE `{$tables['link']['name']}` SET `HITS` = '{$value}' WHERE `ID` = ".$db->qstr($id));
   		$callback_data = $value;
   	break;
         
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
            $ActionStatus = SetNewLinkStatus($linkID, $newStatus, 0);
         }
      }
      break;
}

//$debug = $links_id;

$status = ($ActionStatus['status'] == 1) ? '1' : 'error:'.$debug;

// Edit in place require callback new data
if ($status == '1' && $eip) { 
	echo $callback_data;
} else {
	echo $status;
}

?>