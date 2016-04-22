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
   list ($action, $id) = explode(':', $_REQUEST['action']);

   $action = strtoupper (trim ($action));
   $id     = ($id < 0  ? 0 : intval ($id));

   $tpl->assign('action', strtoupper ($action));
}

//RALUCA: JQuery validation related
$validators = array(
	'rules' => array(
		'TPL_TYPE' => array(
			'required' => true
		),
		'TITLE' => array(
			'required' => true
		),
		'SUBJECT' => array(
			'required' => true
		),
		'BODY' => array(
			'required' => true
		)
	),
	'messages' => array(
		
	)
);

$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related

switch ($action)
{
	case 'D' :
		if ($db->Execute("DELETE FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($id)))
		{
			if (isset ($_SESSION['return']))
				http_custom_redirect($_SESSION['return']);
		}
      else
			$tpl->assign('sql_error', $db->ErrorMsg());
		break;
	case 'E' :
		if (empty ($_REQUEST['submit']))
			$data = $db->GetRow("SELECT * FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($id));
	case 'N' :
	default :
		if ($id)
			$where = "WHERE `ID` != ".$db->qstr($id);

		if (empty ($_POST['submit'])) {
			$tpl->assign('submit_session', registerAdminSubmitSession());
		} else {
			checkAdminSubmitSession(clean_string($_POST['submit_session']));
			$data = get_table_data('email_tpl');
			
			//RALUCA: JQuery validation related - server side.
		   $validator = new Validator($validators);
		   $validator_res = $validator->validate($_POST);
		   //RALUCA: end of JQuery validation related - server side.
		   
		   if (empty($validator_res))
		   {
				if (empty ($id))
					$id = $db->GenID($tables['email_tpl']['name'].'_SEQ');

				$data['ID'] = $id;
				if ($db->Replace($tables['email_tpl']['name'], $data, 'ID', true) > 0)
				{
					$tpl->assign('posted', true);
					if (isset ($_SESSION['return']))
						http_custom_redirect($_SESSION['return']);
				}
            else
            {
					$tpl->assign('sql_error', $db->ErrorMsg());
					echo $db->ErrorMsg();
				}
			}
		}
		$tpl->assign('tpl_types', $email_tpl_types);
		$tpl->assign($data);
		$content = $tpl->fetch(ADMIN_TEMPLATE.'/email_message_edit.tpl');
		break;
}
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>