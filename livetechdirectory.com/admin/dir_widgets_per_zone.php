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

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER'])) {
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];
}

if (isset($_SESSION['wid_message'])) {
	$tpl->assign('wid_message', $_SESSION['wid_message']);
	unset($_SESSION['wid_message']);
}

if (isset($_SESSION['wid_error'])) {
	$tpl->assign('wid_error', $_SESSION['wid_error']);
	unset($_SESSION['wid_error']);
}

$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);

$where = '';

if (isset($_REQUEST['Z'])) {
	$zone = trim($_REQUEST['Z']);
}

if (isset($_REQUEST['T'])) {
	$type = trim($_REQUEST['T']);
}

$tpl->assign('zone',$zone);
$tpl->assign('type',$type);

$columns = array ('NAME' => _L('Name'),'TITLE' => _L('Title'), 'TYPE' => _L('Zone Type'), 'ACTIVE'=>_L('Active'),'ACTION'=>_L('Action'));
$tpl->assign('columns', $columns);

$tpl->assign('col_count', count($columns));

if ($_SESSION['phpld']['adminpanel']['is_admin'])
{
   $list = get_widgets_per_zone($zone, $type);
   foreach($list as $key=>$value){
       if($value['ID']>0){
           $wd = Phpld_Widget::load($value['ID']);
		   if($wd!==null) {
			   $settings = $wd->getFrontSettings();
			   $list[$key]['TITLE'] = $settings['TITLE'];
		   } else {
			   unset($list[$key]);
			   //$list[$key]['TITLE'] = '';
		   }
       }else{
           $list[$key]['TITLE'] = '';
       }
   }
   $list_total = count($list);
}

$tpl->assign('list', $list);

$tpl->assign($_POST);
$tpl->assign('error'    , $error);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_widgets_per_zone.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>