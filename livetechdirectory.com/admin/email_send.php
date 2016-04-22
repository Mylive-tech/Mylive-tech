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
require_once 'email_admin.php';

if (empty ($_POST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
	$_SESSION['return'] = $_SERVER['HTTP_REFERER'];

//RALUCA: JQuery validation related
$validators = array(
	'rules' => array(
		'TITLE' => array(
			'required' => true
		),
		'EMAIL' => array(
			'required' => true,
			'email' => true,
			'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
		        		'data'=> array (
		        			'action' => "isCheckedEmail",
		        			'table' => "link",
		        			'field' => "EMAIL"
		        		)
		    )
		),
		'URL' => array(
			'url' => true
		),
	)
);
$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);
$validator = new Validator($validators);
//RALUCA: end of JQuery validation related	
	
if (empty ($_POST['submit'])) {
} else {
	$data = get_table_data('email');
	$data['DATE_SENT'] = gmdate ('Y-m-d H:i:s');

   if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
      $data['URL'] = "http://".$data['URL'];

   //RALUCA: JQuery validation related - server side.
   $validator = new Validator($validators);
   $validator_res = $validator->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_res))
   {
		$tmpl = $db->GetRow("SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($_POST['EMAIL_TPL_ID']));
		$mail = get_emailer();
		$mail->Body    = replace_email_vars($tmpl['BODY'], $data);;
		$mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data);
		$mail->AddAddress($data['EMAIL'], $data['NAME']);
		if (!DEMO)
			$sent = $mail->Send();
        else
			$sent = true;

		if ($sent)
		{
			$id = $db->GenID($tables['email']['name'].'_SEQ');
			$data['ID'] = $id;
			if ($db->Replace($tables['email']['name'], $data, 'ID', true) > 0)
			{
				$tpl->assign('posted', true);
				$tpl->assign('sent', $data);
				$data = array();
			}
         else
				$tpl->assign('sql_error', $db->ErrorMsg());
		}
        else
			$tpl->assign('send_error', true);
	}
}
$rs   = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '1'");
$tpls = $rs->GetAssoc();
$tpl->assign('tpls', $tpls);
$tpl->assign($data);
$tpl->assign('EMAIL_TPL_ID', $_POST['EMAIL_TPL_ID']);
$tpl->assign('IGNORE', $_POST['IGNORE']);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/email_send.tpl');

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

function check_email($value, $empty, &$params, &$form) {
	global $db, $tpl, $tables;

	$rs = $db->Execute("SELECT `ID`, `TITLE`, `URL` FROM `{$tables['link']['name']}` WHERE `URL` = ".$db->qstr($form['URL'])." OR `TITLE` = ".$db->qstr($form['TITLE']));
	$err['dir'] = array ();
	while (!$rs->EOF)
   {
		if (strcasecmp ($rs->Fields('URL'),$form['URL'])==0)
			$err['dir'][] = 'URL';
		if (strcasecmp ($rs->Fields('TITLE'),$form['TITLE'])==0)
			$err['dir'][] = 'TITLE';

		$rs->MoveNext();
	}
	$rs = $db->Execute("SELECT * FROM `{$tables['email']['name']}` WHERE `URL` = ".$db->qstr($form['URL'])." OR `TITLE` = ".$db->qstr($form['TITLE'])." OR `EMAIL` = ".$db->qstr($form['EMAIL']));
	$err['email'] = array();
	while(!$rs->EOF)
   {
		$row = array ('EMAIL' => htmlentities (format_email($rs->Fields('EMAIL'), $rs->Fields('NAME'))),
					 'TITLE' => $rs->Fields('TITLE'),
					 'URL' => $rs->Fields('URL'),
                     'DATE' => $rs->Fields('DATE_SENT'));
		if (strcasecmp ($rs->Fields('EMAIL'), $form['EMAIL']) == 0)
			$row['TYPE'] = 'EMAIL';

		if (strcasecmp ($rs->Fields('URL'), $form['URL']) == 0)
			$row['TYPE'] = 'URL';

		if (strcasecmp ($rs->Fields('TITLE'), $form['TITLE']) == 0 )
			$row['TYPE'] = 'TITLE';

		$err['email'][] = $row;
		$rs->MoveNext();
	}
	if (count ($err['dir']) > 0 || count($err['email']) > 0) {
		$tpl->assign('email_send_errors', $err);
		return $_POST['IGNORE'];
	}
	return 1;
}
?>