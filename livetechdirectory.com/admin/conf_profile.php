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

//RALUCA: JQuery validation related
$validators = array(
	'rules' => array(
		'NAME' => array(
			'required' => true,
			'minlength' => USER_NAME_MIN_LENGTH,
			'maxlength' => USER_NAME_MAX_LENGTH
		),
		'LANGUAGE' => array(
			'required' => true
		),
		'PASSWORD' => array(
			'required' => true,
			'minlength' => USER_PASSWORD_MIN_LENGTH,
			'maxlength' => USER_PASSWORD_MAX_LENGTH
		),
		'PASSWORDC' => array(
			'equalTo' => "#PASSWORD"
		),
		'EMAIL' => array(
			'required' => true,
			'email' => true
		)
	),
	'messages' => array(
		'EMAIL'=> array(
			'remote'  	=>_L("This email is banned: either valid or not unique.")
		),
		'PASSWORDC'=> array(
			'remote'  	=> _L("Passwords do not match.")
		)
	)
);


$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related

if (empty ($_REQUEST['submit']))
{
   $sql = "SELECT * FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($_SESSION['phpld']['adminpanel']['id']);
   $row = $db->GetRow($sql);
   $tpl->assign($row);
   $tpl->assign('submit_session', registerAdminSubmitSession());
}
else
{
	checkAdminSubmitSession(clean_string($_POST['submit_session']));
	$tpl->assign('submit_session', registerAdminSubmitSession());
	
   $data = get_table_data('user');

   $error = 0;
   $errorMsg = '';

   if (!isset ($data['SUBMIT_NOTIF']))
      $data['SUBMIT_NOTIF'] = 0;

   if (!isset ($data['PAYMENT_NOTIF']))
      $data['PAYMENT_NOTIF'] = 0;

   $data['ID']        = $_SESSION['phpld']['adminpanel']['id'];
   $data['PASSWORDC'] = $_REQUEST['PASSWORDC'];

   //RALUCA: JQuery validation related - server side.
   $validator = new Validator($validators);
   $validator_res = $validator->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_res))
   {
      unset ($data['PASSWORDC']);

      $unhashedPassword = (!empty ($data['PASSWORD']) ? $data['PASSWORD'] : '');

      if (empty ($data['PASSWORD']))
         $data['PASSWORD'] = $db->GetOne("SELECT `PASSWORD` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($data['ID']));
      else
         $data['PASSWORD'] = encrypt_password($data['PASSWORD']);

      $mode = "UPDATE";
      $where = " `ID` = ".$db->qstr($data['ID']);

      if (!DEMO && $db->AutoExecute($tables['user']['name'], $data, $mode, $where) > 0)
      {
         //Send user settings confirmation email

         //Get email template
         $emailTpl = get_email_template('NTF_USER_DETAILS_TPL');

         //if password changed on edit and an email tpl is available
         if (!empty ($unhashedPassword) && is_array ($emailTpl) && !empty ($emailTpl))
         {
            //Get user data ($data is not complete)
            $pdata = $db->GetRow("SELECT * FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($data['ID']));

            //Send unhashed password
            $pdata['PASSWORD'] = $unhashedPassword;

            $mail = get_emailer_admin();

            //Add email subject
            $emailSubject = $emailTpl['SUBJECT'];
            $emailSubject = replace_email_vars($emailSubject, $pdata, 5);
            $mail->Subject = trim ($emailSubject);

            //Add owner email address
            $mail->AddAddress($data['EMAIL'], $pdata['NAME']);

            //Add email body
            $emailBody = $emailTpl['BODY'];
            $emailBody = replace_email_vars($emailBody, $pdata, 5);
            $mail->Body = trim ($emailBody);

            //Send email
            if (!$mail->Send())
            {
               $error++;
               $tpl->assign('error', $error);
               $errorMsg = $mail->ErrorInfo;
               $tpl->assign('errorMsg', $errorMsg);
            }

            //Clear all addresses (and attachments) for next loop
            $mail->ClearAddresses();
            $mail->ClearAttachments();

            //Free memory
            unset ($pdata, $unhashedPassword, $mail, $emailBody, $emailSubject);
         }

         $_SESSION['user_language'] = $data['LANGUAGE'];

         $tpl->assign('posted', true);
         update_link_owner($data['ID']);

         //Free memory
         unset ($emailTpl);
      }

      //Remove unhashed password (if still available)
      unset ($unhashedPassword);

   } else {
   	$tpl->assign('error', 1);
   	$tpl->assign('errorMsg', _L('Form validation went wrong: please check the fields and try again'));
   }
}
$langs = select_lang('../lang/');
$tpl->assign('languages', $langs);
$tpl->assign($data);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_profile.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>