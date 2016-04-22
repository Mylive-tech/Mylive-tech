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

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];


if ($_REQUEST['action'])
{
   list($action, $id, $val) = explode(':', $_REQUEST['action']);

   $action = strtoupper (trim ($action));

   $val    = ($val < 0 ? 0 : intval ($val));
   $tpl->assign('action', strtoupper ($action));
}

//If editor, check if he/she is allowed to take an action on current article
if (!$_SESSION['phpld']['adminpanel']['is_admin'] && isset ($id))
{
   //Get categ ID of requested article

   if (!in_array ($categID, $_SESSION['phpld']['adminpanel']['permission_array']))
   {
      //Editor is on unallowed page, block access
      http_custom_redirect("unauthorized.php");
      exit();
   }
}

//Correct value for ID
$id = (isset ($id) ? intval ($id) : 0);
$id = ($id < 0 ? 0 : $id);

$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active') ));

switch ($action)
{
	case 'A' :
      $ActionStatus = SetNewLinkCommentStatus($id, 2);
      $error = ($ActionStatus['status'] == 1 ? false : true);

      if ($ActionStatus['status'] != 1)
         $tpl->assign('sql_error', $ActionStatus['errorMsg']);

      if (!$error && isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);
		break;
   case 'D' :
      $ActionStatus = RemoveLinkComment($id);
      $error = ($ActionStatus['status'] == 1 ? false : true);

      if ($ActionStatus['status'] != 1)
         $tpl->assign('sql_error', $ActionStatus['errorMsg']);

      if (!$error && isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);
      break;
}
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>
