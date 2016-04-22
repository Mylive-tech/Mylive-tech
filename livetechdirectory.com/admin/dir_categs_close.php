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

$tpl->assign('ENABLE_REWRITE', ENABLE_REWRITE);

$error = 0;
$errorMsg = '';

if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
}

//If editor, check if he/she is allowed to take an action on current category
if (!$_SESSION['phpld']['adminpanel']['is_admin'] && isset ($id))
{
   if (!in_array ($id, $_SESSION['phpld']['adminpanel']['permission_array']))
   {
      //Editor is on unallowed page, block access
      http_custom_redirect("unauthorized.php");
      exit();
   }
}

//Correct value for ID
$id = (isset ($id) ? intval ($id) : 0);
$id = ($id < 0 ? 0 : $id);

$currentCategory = $db->GetRow("SELECT C.*, ".$db->IfNull('P.TITLE', "'Top'")." AS `PARENT` FROM `{$tables['category']['name']}` AS `C` LEFT OUTER JOIN `{$tables['category']['name']}` AS `P` ON (C.PARENT_ID = P.ID) WHERE C.ID = ".$db->qstr($id)." LIMIT 1");

$tpl->assign('currentCategory', $currentCategory);

//If editor, check if he/she is allowed to take an action on current category
if (!$_SESSION['phpld']['adminpanel']['is_admin'] &&
 !empty ($data['PARENT_ID']) &&
 !in_array ($data['PARENT_ID'], $_SESSION['phpld']['adminpanel']['permission_array'])
)
{
//Editor is on unallowed page, block access
http_custom_redirect("unauthorized.php");
exit();
}

if (empty ($_REQUEST['submit']))
	$data = $db->GetRow("SELECT * FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($id));
else {
		
    //$data = get_table_data('category');
    $data = $db->GetRow("SELECT * FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($id));
    
    if (isset($_REQUEST['CLOSED_TO_LINKS']) && ($_REQUEST['CLOSED_TO_LINKS'] == 1)) {
        $data['CLOSED_TO_LINKS'] = 1;
    } else {
        $data['CLOSED_TO_LINKS'] = 0;
    }
		
    if ($db->Replace($tables['category']['name'], $data, 'ID', true) > 0)
    {

       // Refresh editor permissions
        if (!$_SESSION['phpld']['adminpanel']['is_admin'])
        {
        	$user_permission             = "";
        	$user_grant_permission       = "";
        	$user_permission_array       = array();
        	$user_grant_permission_array = array();
        
        	get_editor_permission($_SESSION['phpld']['adminpanel']['id']);
        	$_SESSION['phpld']['adminpanel']['permission']             = $user_permission;
        	$_SESSION['phpld']['adminpanel']['grant_permission']       = $user_grant_permission;
        	$_SESSION['phpld']['adminpanel']['permission_array']       = $user_permission_array;
        	$_SESSION['phpld']['adminpanel']['grant_permission_array'] = $user_grant_permission_array;
        }
        
        $tpl->assign('posted', true);
	}
	else
		$tpl->assign('sql_error', $db->ErrorMsg());
}
$tpl->assign('error', $error);
$tpl->assign('errorMsg', $errorMsg);

$tpl->assign($data);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_categs_close.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>
