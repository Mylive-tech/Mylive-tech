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

$submitAction   = (!empty ($_POST['submitAction']) ? strtolower ($_POST['submitAction']) : '');
$selectedComments = (!empty ($_POST['multiselect_checkbox']) && is_array ($_POST['multiselect_checkbox']) ? $_POST['multiselect_checkbox'] : array());

if (!empty ($submitAction))
{
   switch ($submitAction)
   {
      case 'remove' :
         if (!empty ($selectedComments))
         {
            foreach ($selectedComments as $key => $id)
            {
               $ActionStatus = RemoveLinkComment($id);
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

         if (!empty ($selectedComments) && in_array ($newStatus, $validCategStatus))
         {
            foreach ($selectedComments as $key => $id)
            {
               $ActionStatus = SetNewLinkCommentStatus($id, $newStatus);
               $thisError = ($ActionStatus['status'] == 1 ? 0 : 1);

               if ($ActionStatus['status'] != 1)
               {
                  $error++;
                  $tpl->assign('sql_error', $ActionStatus['errorMsg']);
               }
            }
         }
         break;
   }
   unset ($_POST['submitAction'], $submitAction);

   if (!$error && isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);
}
?>