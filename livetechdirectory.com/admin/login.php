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

//Run input filter, request variables should be safe now
require_once 'include/io_filter.php';

// Disable any caching by the browser
disable_browser_cache();

$error = 0;
$errorMsg = '';

//Determine action
$action = (!empty ($_REQUEST['action']) ? clean_string_paranoia($_REQUEST['action']) : 'default');
$tpl->assign('action', $action);

switch (strtolower ($action))
{
   case 'sendpassword' :
      
		//RALUCA: JQuery validation related
		$validators = array(
			'rules' => array(
				'LOGIN' => array(
					'required' => true
				),
				'EMAIL' => array(
					'required' => true,
					'email' => true,
					'remote' => array(
						'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
						'type'=> "post",
				        'data'=> array (
				        	'action' => "isBannedEmail",
				        	'table' => "user",
				        	'field' => "EMAIL"
				        )
				       )
				) 
			),
			'messages' => array(
				'EMAIL'=> array(
					'remote'  	=>_L("This email is banned.")
				)
			)
		);
	
	$vld = json_custom_encode($validators);
	$tpl->assign('validators', $vld);
	
	$validator = new Validator($validators);
	//RALUCA: end of JQuery validation related
   	
   	  if (empty ($_POST['submit'])) {
      } else {
         $data = get_table_data('user');
         
         //Take care of white-space chars
         $data = filter_white_space($data);
         
         //RALUCA: JQuery validation related - server side.
	   	 $validator = new Validator($validators);
	   	 $validator_res = $validator->validate($_POST);
	   	 //RALUCA: end of JQuery validation related - server side.
		   
		 if (empty($validator_res))
		   {
            $user = $db->GetRow("SELECT `ID`, `LOGIN`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `LOGIN` LIKE ".$db->qstr($data['LOGIN'])." AND `EMAIL` LIKE ".$db->qstr($data['EMAIL'])." AND `LEVEL` IN ('1', '3') AND `ACTIVE` = '1'");

            if (is_array ($user) && !empty ($user))
            {
               $data['ID'] = $user['ID'];

               $new_password         = create_password(8);
               $data['CONFIRM']      = create_password(6);
               $data['NEW_PASSWORD'] = encrypt_password($new_password);
               unset ($data['user'], $data['email']);

                  $dbUpdate = $db->Replace($tables['user']['name'], $data, 'ID', true);

                  if ($dbUpdate == 1)
                  {
                     $user['PASSWORD'] = $new_password;

                     $passwordRecoverUrl = (substr (SITE_URL, -1) == "/" ? SITE_URL : SITE_URL."/");
                     $passwordRecoverUrl .= "admin/login.php?action=confirm&uid={$user['ID']}&key={$data['CONFIRM']}";

                     $loginURL = (substr (SITE_URL, -1, 1) == '/' ? SITE_URL : SITE_URL.'/') . 'admin/login.php';

                     //Get email template
                     $emailTpl = get_email_template('NTF_USER_PASSWORD_TPL');

                     //if email tpl is available
                     if (is_array ($emailTpl) && !empty ($emailTpl))
                     {
                        $mail = get_emailer_admin();
                        $mail->AddAddress($user['EMAIL'], $user['NAME']);

                        //Add email subject
                        $emailSubject = $emailTpl['SUBJECT'];
                        $emailSubject = str_replace ('{PASSWORD_RECOVER_URL}', $passwordRecoverUrl, $emailSubject);
                        $emailSubject = str_replace ('{USER_LOGIN_PAGE}', $loginURL, $emailSubject);
                        $emailSubject = replace_email_vars($emailSubject, $user, 6);
                        $emailSubject = replace_email_vars($emailSubject, $user, 5);
                        $mail->Subject = trim ($emailSubject);

                        //Add email body
                        $emailBody = $emailTpl['BODY'];
                        $emailBody = str_replace ('{PASSWORD_RECOVER_URL}', $passwordRecoverUrl, $emailBody);
                        $emailBody = str_replace ('{USER_LOGIN_PAGE}', $loginURL, $emailBody);
                        $emailBody = replace_email_vars($emailBody, $user, 6);
                        $emailBody = replace_email_vars($emailBody, $user, 5);
                        $mail->Body = trim ($emailBody);

                        //Send email
                        if (!$mail->Send())
                        {
                           //Error occured while sending email
                           $error++;
                           $tpl->assign('error', $error);
                           $errorMsg = $mail->ErrorInfo;
                           $tpl->assign('errorMsg', $errorMsg);
                        }
                        else
                        {
                           //Email sent successfully
                           $tpl->assign('email_status', 1);
                        }

                        //Clear all addresses (and attachments)
                        $mail->ClearAddresses();
                        $mail->ClearAttachments();

                        //Free memory
                        unset ($user, $data, $mail, $emailTpl, $emailSubject, $emailBody, $passwordRecoverUrl, $loginURL, $new_password);
                     }
                  }

                  //Free memory
                  unset ($data, $new_password);
               }
               else
               {
                  $error++;
                  $tpl->assign('error', $error);
                  $errorMsg = _L('The details you supplied do not match any registered user.');
                  $tpl->assign('errorMsg', $errorMsg);

                  unset ($data, $_POST, $_GET, $_REQUEST);
               }

               if (isset ($user))
               {
                  unset ($user);
               }
         }
         else
         {
         	$errorMsg = _L('Some validation went wrong, please try again.');
            $tpl->assign('errorMsg', $errorMsg);
            $tpl->assign($_POST);
            unset ($data);
         }
      }

      if (isset ($data))
      {
         $tpl->assign($data);
         unset ($data);
      }
      break;

   case 'confirm' :
      if (empty ($_SESSION['phpld']['admin']['id']))
      {
         $user_id     = (!empty ($_REQUEST['uid']) ? clean_string_paranoia($_REQUEST['uid']) : '');
         $confirm_key = (!empty ($_REQUEST['key']) ? clean_string_paranoia($_REQUEST['key']) : '');

         if (!empty ($user_id) && !empty ($confirm_key))
         {
            $data = $db->GetRow("SELECT `ID`, `NEW_PASSWORD` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($user_id)." AND `CONFIRM` = ".$db->qstr($confirm_key));

            if (is_array ($data) && !empty ($data))
            {
               $data['PASSWORD']     = $data['NEW_PASSWORD'];
               $data['NEW_PASSWORD'] = '';
               $data['CONFIRM']      = '';

               $where = " `ID` = ".$db->qstr($user_id);

               if ($db->AutoExecute($tables['user']['name'], $data, 'UPDATE', $where))
               {
                  $tpl->assign('password_recovered', 1);
               }
               else
               {
                  $error++;
                  $tpl->assign('error', $error);
                  $errorMsg = $db->ErrorMsg();
                  $tpl->assign('errorMsg', $errorMsg);

                  $tpl->assign('password_recovered', 0);
               }
            }
            else
            {
               unset ($data, $_POST, $_GET, $_REQUEST, $user_id, $confirm_key);
               die ("!! ERROR !!\n<br />\n<br />\nSeems like an attack! Your access has been blocked.\n");
            }
         }
      }
      break;

   case 'default' :
   default        :
   		//RALUCA: JQuery validation related
		$validators = array(
			'rules' => array(
				'user' => array(
					'required' => true
				),
				'pass' => array(
					'required' => true
				),
			)
		);
		$vld = json_custom_encode($validators);
		$tpl->assign('validators', $vld);
		
		$validator = new Validator($validators);
		//RALUCA: end of JQuery validation related
      if (empty ($_POST['submit'])) {
      } else {
      	          $_POST['user'] = strip_white_space($_POST['user']);
         $_POST['pass'] = strip_white_space($_POST['pass']);

         //RALUCA: JQuery validation related - server side.
	   	 $validator = new Validator($validators);
	   	 $validator_res = $validator->validate($_POST);
	   	 //RALUCA: end of JQuery validation related - server side.
		   
		 if (empty($validator_res)) {
            $sql = "SELECT `ID`, `NAME`, `LEVEL`, `LANGUAGE` FROM `{$tables['user']['name']}` WHERE `LOGIN` = ".$db->qstr($_POST['user'])." AND `PASSWORD` = ".$db->qstr(encrypt_password($_POST['pass']))." AND `LEVEL` IN ('1', '3') AND `ACTIVE` = '1'";

            $row = $db->GetRow($sql);
            if (!empty ($row['ID']))
            {
               //Create new phpLD user session if none exists
               //Don't break web-users login data
               if (!isset ($_SESSION['phpld']))
               {
                  $_SESSION['phpld'] = array();
               }

               //Create admin panel user session
               $_SESSION['phpld']['adminpanel'] = array();

               $_SESSION['user_language'] = $_SESSION['phpld']['adminpanel']['language'] = $row['LANGUAGE'];
               
               
               //delete expired links & articles (not paid though) on admin login
               //links
               $HaveExpiredEmail_sql = "DELETE FROM `{$tables['link']['name']}`
                                WHERE `OWNER_EMAIL_CONFIRMED` = '0'
                                AND DATE_ADD(`DATE_ADDED`, INTERVAL ".WAIT_FOR_EMAIL_CONF." DAY) <= now()
                                AND `PAYED` <> '1'";
  
    		   $HaveExpiredEmail = $db->Execute($HaveExpiredEmail_sql);
    		   //end of delete
    		  
               
               // Update last login
		         
		  $data['LAST_LOGIN']     = $timenow;
		  $where = " `ID` = ".$row['ID'];
          $db->AutoExecute($tables['user']['name'], $data, 'UPDATE', $where);
               // get permissions for this editor
               if ($row['LEVEL'] == 3)
               {
                  $user_permission             = "";
                  $user_grant_permission       = "";
                  $user_permission_array       = array ();
                  $user_grant_permission_array = array ();
                  get_editor_permission($row['ID']);
//                  $_SESSION['phpld']['adminpanel']['permission']             = $user_permission;
				  $_SESSION['phpld']['adminpanel']['permission']             = " 1 ";
                  $_SESSION['phpld']['adminpanel']['grant_permission']       = $user_grant_permission;
                  $_SESSION['phpld']['adminpanel']['permission_array']       = $user_permission_array;
                  $_SESSION['phpld']['adminpanel']['grant_permission_array'] = $user_grant_permission_array;
               }
               if ($row['LEVEL'] == 1 || $row['LEVEL'] == 3 || count ($user_permission_array) > 0)
               {
                  $_SESSION['phpld']['adminpanel']['id']       = $row['ID'];
                  $_SESSION['phpld']['adminpanel']['name']     = $row['NAME'];
                  $_SESSION['phpld']['adminpanel']['is_admin'] = (($row['LEVEL'] == 1) ? 1 : 0);
                  $_SESSION['phpld']['adminpanel']['level'] = $row['LEVEL'];
                  $_SESSION['phpld']['adminpanel']['rights'] = user_needs_approval($row['ID'],0);

                  if (!preg_match ('`(admin|install)/(.*)\.php$`', $_SESSION['return']))
                     unset ($_SESSION['return']);

                  if ($_SESSION['return'])
                  {
                     @ header ("Location: ".$_SESSION['return']);
                     unset ($_SESSION['return']);
                  }
                  else
                     @ header ("Location: index.php");

                  exit ();
               }
               else
                  $tpl->assign('no_permission', true);
            }
            else
               $tpl->assign('failed', true);
         } else {
         	$tpl->assign('invalid', true);
         }
      }
      break;
}



//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/login.tpl');
?>