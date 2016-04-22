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

$columns = array ('NAME' => _L('Name'), 'STATUS'=>_L('Status'), 'ACTION'=>_L('Action'), 'ORDER_ID' => _L('Quick Move') );
$tpl->assign('columns', $columns);

$tpl->assign('col_count', count($columns));

if ($_REQUEST['action'])
{
   list ($action, $id) = explode(':', $_REQUEST['action']);

   $action = strtoupper (trim ($action));
   $tpl->assign('action', strtoupper ($action));
}

//Correct value for ID
$id = (isset ($id) ? intval ($id) : 0);
$id = ($id < 0 ? 0 : $id);
$tpl->assign('id', $id);

$tpl->assign('stats', array(0 => _L('Inactive'), 1 => _L('Active')));


switch ($action) {
   case 'D':
       $db->Execute("DELETE FROM `{$tables['inline_widget']['name']}` WHERE `ID` = '{$id}'");
       break;
       
   case 'S':
      list ($t, $t, $status) = explode(':', $_REQUEST['action']);
      $db->Execute("UPDATE `{$tables['inline_widget']['name']}` SET `STATUS` = '{$status}' WHERE `ID` = '{$id}'");
      http_custom_redirect(DOC_ROOT.'/dir_inline_widgets.php');
      break;
      
   case 'E':
   	if (empty ($_REQUEST['submit']))
   		$data = $db->GetRow("SELECT * FROM `{$tables['inline_widget']['name']}` WHERE `ID` = ".$db->qstr($id));
   case 'N':
   default :
   		//RALUCA: JQuery validation related
        	$validators = array(
					'rules' => array(
						'NAME' => array(
							'required' => true,
						)
					)
        	);

        $vld = json_custom_encode($validators);
        $tpl->assign('validators', $vld);

        $validator = new Validator($validators);

   		if (!empty ($_POST['submit'])) {
         	$data = get_table_data('inline_widget');
         	
          	 //RALUCA: JQuery validation related - server side.
          	$validator = new Validator($validators);
           	$validator_res = $validator->validate($_POST);
           	
           	if (empty($validator_res)) {
         		$data['ID'] = $id;
         		$data['NAME'] = $_REQUEST['NAME'];
         		$data['TEXT'] = $_REQUEST['TEXT'];
         		$data['STATUS'] = $_REQUEST['STATUS'];
         	
         		if (db_replace('inline_widget', $data, 'ID') > 0) {
         			$tpl->assign("posted", true);
         		} else {
         			var_dump($db->ErrorMsg());
         		}
         	}
         	
      }
      
      $tpl->assign($data);
     
   	break;
}

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_inline_widget_edit.tpl');

$tpl->assign('error'    , $error);

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>