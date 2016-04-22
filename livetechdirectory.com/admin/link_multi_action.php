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
/**
 * Allow to be executed only from admin area!!
 */

if (!defined ('IN_PHPLD_ADMIN'))
{
   die("!! ERROR !! You are not allowed to run this script!");
}

$submitAction = (!empty ($_POST['submitAction']) ? strtolower ($_POST['submitAction']) : '');

$submitAction = str_replace(" ", "", $submitAction);
$selectedLinks = (!empty ($_POST['multiselect_checkbox']) && is_array ($_POST['multiselect_checkbox']) ? $_POST['multiselect_checkbox'] : array());

if (!empty ($submitAction))
{
   switch ($submitAction)
   {
      case 'changecategory':
         $categID = (isset ($_POST['CATEGORY_ID']) && preg_match ('`^[\d]+$`', $_POST['CATEGORY_ID']) ? intval ($_POST['CATEGORY_ID']) : '');

         if (!empty ($selectedLinks) && $categID >= 0)
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus = linkChangeCateg($linkID, $categID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);

               if ($ActionStatus['status'] != 1)
               {
                  $error++;
                  $tpl->assign('sql_error', $ActionStatus['errorMsg']);
               }
            }
         }

         //Category auto-selection back to "Top"
         $categID = 0;
         break;
      case 'bandomain':
         if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus = banDomain($linkID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);

               if ($ActionStatus['status'] != 1)
               {
                  $error++;
                  $tpl->assign('sql_error', $ActionStatus['errorMsg']);
               }
            }
         }
         break;
      case 'banip':
         if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus = banLinkSubmitIP($linkID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);

               if ($ActionStatus['status'] != 1)
               {
                  $error++;
                  $tpl->assign('sql_error', $ActionStatus['errorMsg']);
               }
            }
         }
         break;
      case 'spamlink':
          if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus1 = banDomain($linkID);
               $ActionStatus2 = banLinkSubmitIP($linkID);
               $ActionStatus3 = removeLink($linkID);
               
               $thisError1 = ($ActionStatus1['status'] == 1 ? 0 : 1);
               $thisError2 = ($ActionStatus2['status'] == 1 ? 0 : 1);
               $thisError3 = ($ActionStatus3['status'] == 1 ? 0 : 1);
               
               if (($thisError1 == 0) || ($thisError2 == 0) || ($thisError3 == 0)) {
                   $thisError = 0;
               } else {
                   $thisError = 1;
               }
               if (($ActionStatus1['status'] != 1) || ($ActionStatus2['status'] != 1) || ($ActionStatus3['status'] != 1))
               {
                  $error++;
                  $tpl->assign('sql_error', $ActionStatus1['errorMsg']." - ".$ActionStatus2['errorMsg']." - ".$ActionStatus3['errorMsg']);
               }
            }
         }
         break;
      case 'expired' :
         if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               send_expired_notifications($linkID);
            }
         }
         break;
      case 'remove' :
         if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus = RemoveLink($linkID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);

               if ($ActionStatus['status'] != 1)
               {
                  $error++;
                  $tpl->assign('sql_error', $ActionStatus['errorMsg']);
               }
            }
         }
         break;

      case 'featured' :
         if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus = MakeFeaturedLink($linkID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);

               if ($ActionStatus['status'] != 1)
               {
                  $error++;
                  $tpl->assign('sql_error', $ActionStatus['errorMsg']);
               }
            }
         }
         break;

      case 'regular' :
         if (!empty ($selectedLinks))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
               $ActionStatus = MakeRegularLink($linkID);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);

               if ($ActionStatus['status'] != 1)
               {
                  $error++;
                  $tpl->assign('sql_error', $ActionStatus['errorMsg']);
               }
            }
         }
         break;

      case 'active'   :
      case 'pending'  :
      case 'inactive' :

      default :
         $newStatus = -1;
         if ($submitAction == 'active') $newStatus = 2;
         elseif ($submitAction == 'pending') $newStatus = 1;
         elseif ($submitAction == 'inactive') $newStatus = 0;

         if (!empty ($selectedLinks) && in_array ($newStatus, $validLinkStatus))
         {
            foreach ($selectedLinks as $key => $linkID)
            {
              $ActionStatus = SetNewLinkStatus($linkID, $newStatus, 0);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);

               if ($ActionStatus['status'] != 1)
               {
                  echo $error++;
                  $tpl->assign('sql_error', $ActionStatus['errorMsg']);
               }
            }
         }
     
                   
         break;
   }
   unset ($_POST['submitAction'], $submitAction);
   if (!$error && isset ($_SESSION['return'])) {
       http_custom_redirect($_SESSION['return']);
   }
}
?>