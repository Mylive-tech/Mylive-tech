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

//if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
//   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

$where = '';

if ($_SESSION['phpld']['adminpanel']['is_admin']) {
    $wd = Phpld_Widget::factory(array('NAME'=>$_REQUEST['id'], 'ID'=>''));
   $list = pick_widget_zones($_REQUEST['id'], $wd);
   //$wd = new Widget($_REQUEST['id'], '');
   if (!empty ($_REQUEST['submit'])) {
   	$set = array();
		$editID = 0;	$lastZone = '';
	foreach ($_POST['zones'] as $key=>$zone) {
		$editID = $wd->install($zone);			
		$reditID=	$db->getOne('SELECT MAX(ID) FROM '.$tables['widget_activated']['name'].'');
		$lastZone = $zone;
	}

	$_SESSION['wid_message'] = "Changes saved.";					
	http_custom_redirect(DOC_ROOT.'/dir_widgets_edit.php?action=E:'.$reditID.'&returnTo='.$lastZone);
    //http_custom_redirect(DOC_ROOT.'/dir_widgets.php');			
   } elseif (!empty ($_REQUEST['cancel'])) {
	   	$set = array();
		$wd->uninstall();
		$_SESSION['wid_message'] = "Changes saved.";
		if (isset ($_SESSION['return']))
	        http_custom_redirect($_SESSION['return']);
	   } 
}

$tpl->assign('widname', $_REQUEST['id']);

$tpl->assign('list', $list);


$tpl->assign($_POST);
$tpl->assign('error'    , $error);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_widgets_pick_zones.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>