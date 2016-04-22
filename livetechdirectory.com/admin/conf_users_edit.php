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

if ($_REQUEST['action'])
{
   list ($action, $id) = explode(':', $_REQUEST['action']);

   $action = strtoupper (trim ($action));
   $id     = ($id < 0  ? 0 : intval ($id));

   $tpl->assign('action', strtoupper ($action));
}

if($_SESSION['phpld']['adminpanel']['is_admin'])
   $tpl->assign('admin_user', array (0 => ('Regular User'), 1 => _L('Administrator'), 2 => _L('Editor'), 3 => _L('Super Editor')));
else
   $tpl->assign('admin_user', array (0 => ('Regular User'), 2 => _L('Editor')));

$tpl->assign('yes_no', array (0 => _L('No'), 1 => _L('Yes')));

switch($action)
{
   case 'C' :
      if (isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);
      break;
   case 'A' :
      if ($db->Execute("UPDATE `{$tables['user']['name']}` SET `ACTIVE` = 1 WHERE `ID` = ".$db->qstr($id)))
      {
         if(isset($_SESSION['return']))
            http_custom_redirect($_SESSION['return']);
      }
      else
         $tpl->assign('sql_error', $db->ErrorMsg());
      break;
   case 'D' :
      if ($db->Execute("DELETE FROM `{$tables['user']['name']}` WHERE `ID`= ".$db->qstr($id)))
      {
         if ($db->Execute("DELETE FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = ".$db->qstr($id)))
         {
            if (isset ($_SESSION['return']))
               http_custom_redirect($_SESSION['return']);
         }
         else
            $tpl->assign('sql_error', $db->ErrorMsg());
      }
      else
         $tpl->assign('sql_error', $db->ErrorMsg());
      break;
   case 'E' :
      if (empty ($_POST['submit']))
         $data = $db->GetRow("SELECT * FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($id));
   case 'N' :
   default :
   	  //RALUCA: JQuery validation related
		$validators = array(
			'rules' => array(
				'LOGIN' => array(
							'required' => true,
							'minlength' => USER_LOGIN_MIN_LENGTH,
		   					'maxlength' => USER_LOGIN_MAX_LENGTH,
							'remote' => array(
									'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
									'type'=> "post",
					        		'data'=> array (
					        			'action' => "isRegistrationUsername",
					        			'table'  => "user",
					        			'field'  => "LOGIN",
									'id'     => $id 
					        		)
		   					)
						),
				'NAME' => array(
							'required' => true,
							'minlength' => USER_NAME_MIN_LENGTH,
		   					'maxlength' => USER_NAME_MAX_LENGTH
						),
				'LANGUAGE' => array(
							'required' => true
						),
				'EMAIL' => array(
							'required' => true,
							'email' => true,
							'remote' => array(
									'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
									'type'=> "post",
                                                                        'data'=> array (
							        	'action' => "isRegistrationEmail",
							        	'table' => "user",
							        	'field' => "EMAIL",
                                                                        'exclude_id'     => $id
                                                                        )
					        )
						),
				'PASSWORDC' => array(
                                                    'equalTo' => "#PASSWORD"
						),
				'NAME' => array(
							'required' => true,
							'minlength' => USER_NAME_MIN_LENGTH,
		   					'maxlength' => USER_NAME_MAX_LENGTH
						),
				
			),
			'messages' => array(
				'EMAIL'=> array(
					'remote'  	=>_L("This email is banned.")
				),
				'LOGIN'=> array(
					'remote'  	=> _L("This is not a valid username.")
				),
				'CAPTCHA'=> array(
					'remote'  	=> _L("Incorrect code.")
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
   		
   	
      if(empty($_POST['submit']))
      {
         if ($action == 'N')
            $data = array ();
            
      	$tpl->assign('submit_session', registerAdminSubmitSession());
      }
      else
      {
      	checkAdminSubmitSession(clean_string($_POST['submit_session']));
      	$tpl->assign('submit_session', registerAdminSubmitSession());
         $data = get_table_data('user');

         if (!isset($data['SUBMIT_NOTIF']) || $data['LEVEL'] == 0)
            $data['SUBMIT_NOTIF'] = 0;

         if (!isset($data['PAYMENT_NOTIF']) || $data['LEVEL'] == 0)
            $data['PAYMENT_NOTIF'] = 0;

         $data['ACTIVE'] = 1;
         if (isset($_REQUEST['cemail'])){
			$data['EMAIL_CONFIRMED'] = 1;
			}
         $data['PASSWORDC'] = $_REQUEST['PASSWORDC'];
         
         //RALUCA: JQuery validation related - server side.
       $validator = new Validator($validators);
       $validator_res = $validator->validate($_POST);
       //RALUCA: end of JQuery validation related - server side.

       if (empty($validator_res))
       {
            unset ($data['PASSWORDC']);
            if (empty ($id))
               $id = $db->GenID($tables['user']['name'].'_SEQ');

            $data['ID'] = $id;

            $unhashedPassword = (!empty ($data['PASSWORD']) ? $data['PASSWORD'] : '');

            if ($action == 'E')
            {
               if (empty($data['PASSWORD']))
                  $data['PASSWORD'] = $db->GetOne("SELECT `PASSWORD` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($id));
               else
                  $data['PASSWORD'] = encrypt_password($data['PASSWORD']);
            }
            else
               $data['PASSWORD']    = encrypt_password($data['PASSWORD']);

            if ($db->Replace($tables['user']['name'], $data, 'ID', true) > 0)
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

               update_link_owner($id);
               $tpl->assign('posted', true);

               //Free memory
               unset ($emailTpl);
               
               if ($data['LEVEL'] == 2)
                  http_custom_redirect('conf_user_permissions.php?action=N:0&u='.$id);
               else
               {
                  if ($action == 'N')
                     $data = array ();
                  else
                     if (isset ($_SESSION['return']))
                        http_custom_redirect($_SESSION['return']);
               }
            }
            else
            {
               $tpl->assign('sql_error', $db->ErrorMsg());
            }

            //Remove unhashed password (if still available)
            unset ($unhashedPassword);
         }
      }
      break;
}

$tpl->assign('languages', select_lang('../lang/'));
$tpl->assign($data);
$tpl->assign('action', $action);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_users_edit.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Compress output for faster loading
if (COMPRESS_OUTPUT == 1)
   $tpl->load_filter('output', 'CompressOutput');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>