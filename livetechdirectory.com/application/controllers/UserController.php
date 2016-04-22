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
class UserController extends PhpldfrontController
{
    public function loginAction()
    {
        if ($this->isPost()) {
            $_POST['user'] = strip_white_space($_POST['user']);
            $_POST['pass'] = strip_white_space($_POST['pass']);

            $validators = array(
                'rules' => array(
                    'user' => array(
                        'required' => true
                    ),
                    'pass' => array(
                        'required' => true
                    )
                )
            );

            $validator = new Validator($validators);
            $validator_res = $validator->validate($_POST);


            if (empty($validator_res))
            {
                if (Model_CurrentUser::getInstance()->login($_POST['user'], $_POST['pass'])) {
                    if (isset($_SESSION['prev_page'])) {
                        http_custom_redirect($_SESSION['prev_page']);
                    } else {
                        http_custom_redirect(DOC_ROOT."/user");
                    }
                } else {
                    $this->fm()->error('Invalid ID or password. Please try again.');
                    http_custom_redirect(DOC_ROOT."/login");
                }
            }
            else {
                $this->fm()->error('Invalid ID or password. Please try again.');
                http_custom_redirect(DOC_ROOT."/login");
            }
        }
    }

    public function logoutAction()
    {
        Model_CurrentUser::getInstance()->logout();
        http_custom_redirect(DOC_ROOT.'/');
    }

    public function indexAction()
    {

    }

    public function profileAction()
    {
        Phpld_View::addJavascript(DOC_ROOT.'/javascripts/formtool/formtool.js');
        $this->view->assign('languages', select_lang('lang/'));

        if (!Model_CurrentUser::getInstance()->isLoggedIn()) {
            http_custom_redirect(DOC_ROOT.'/login');
        }

        $excludedValidators = array('LOGIN');
        if (empty($_POST['PASSWORD']) && empty($_POST['PASSWORDC'])) {
            array_push($excludedValidators, 'PASSWORDC');
        }
        $validators = $this->_getProfileValidators($excludedValidators);

        $vld = json_custom_encode($validators);
        $validator = new Validator($validators);
        $user = Model_CurrentUser::getInstance();
        $userData = $user->loadData();
        $res = $validator->validate($_POST);

        if ($this->isPost()) {
            if (empty($res)) {
                $password = $this->getParam('PASSWORDC');
                $data = array(
                    'NAME' => $this->getParam('NAME'),
                    'LANGUAGE' => $this->getParam('LANGUAGE'),
                    'EMAIL' => $this->getParam('EMAIL'),

                );
                if (!empty($password)) {
                    $data['PASSWORD'] = encrypt_password($password);
                }

                if (ALLOW_AUTHOR_INFO) {
                    
                    $data['WEBSITE_NAME'] = $this->getParam('WEBSITE_NAME');
                    $data['WEBSITE'] = $this->getParam('WEBSITE');
                    $data['INFO'] = $this->getParam('INFO');
                    $data['ANONYMOUS'] = $this->getParam('ANONYMOUS');
                }

                $user->update($data);

                if (!empty($password)) {
                    $emailTpl = get_email_template('NTF_USER_DETAILS_TPL');

                    $data['PASSWORD'] = $password;

                    $data['LOGIN'] = $userData['LOGIN'];

                    $mail = get_emailer_admin();

                    //Add email subject
                    $emailSubject = $emailTpl['SUBJECT'];
                    $emailSubject = replace_email_vars($emailSubject, $data, 5);
                    $mail->Subject = trim($emailSubject);

                    //Add owner email address
                    $mail->AddAddress($data['EMAIL'], $data['NAME']);

                    //Add email body
                    $emailBody = $emailTpl['BODY'];
                    $emailBody = replace_email_vars($emailBody, $data, 5);

                    $mail->Body = trim($emailBody);

                    //Send email
                    if (!$mail->Send()) {
                        $this->fm()->error($mail->ErrorInfo);
                    }
                }
                $this->fm()->success(_L('Profile saved'));
            } else {
                foreach ($res as $messages) {
                    foreach ($messages as $message) {
                        $this->fm()->error($message);
                    }
                }
            }
            http_custom_redirect(DOC_ROOT . '/user/profile');
        }

        $INFO_limit = (isset($data['INFO']) && strlen(trim($data['INFO'])) > 0 ? 255 - strlen(trim($data['INFO'])) : '255');

        $this->view->assign('InfoLimit', $INFO_limit);
        $this->view->assign('yesno', array("1" => "Yes", "0" => "No"));
        $this->layout->assign('validators', $vld);
        $this->view->assign('user', $user->loadData());
    }


    public function submissionsAction()
    {
        $modelCategory = new Model_Category();
        $user = Model_CurrentUser::getInstance();

        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        $permissions = $modelCategory->getPermissions($user->getId());

        $sorter = new Phpld_Sorter();

        if (EMAIL_CONFIRMATION == 1) {
            $email_conf = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
        }

        $user_where = 'AND OWNER_ID = '.$user->getId();

        $expire_where = "AND (`EXPIRY_DATE` >= ".$db->DBDate(time())." OR `EXPIRY_DATE` IS NULL)";
        $featLinksCollection = new Phpld_Model_Collection(Model_Link_Entity);
        $linksCollection = new Phpld_Model_Collection(Model_Link_Entity);

        $query = "SELECT *, l.ID AS ID, l.DESCRIPTION AS DESCRIPTION FROM `{$tables['link']['name']}` l,`{$tables['link_type']['name']}` t WHERE l.LINK_TYPE=t.ID and (l.`STATUS` = '2' OR {$permissions['permission_links_arts']}) AND t.`FEATURED` = '1' AND  t.`STATUS` = '2' {$email_conf} {$expire_where} {$user_where} ORDER BY l.`EXPIRY_DATE` DESC";
        $feat_links = $db->CacheGetAll($query);
        $featLinksCollection->setElements($feat_links);
	$featLinksCollection->setCountWithoutLimit($db->getOne("SELECT FOUND_ROWS() as count"));

        $query = "SELECT *, l.ID AS ID, l.DESCRIPTION AS DESCRIPTION FROM `{$tables['link']['name']}` l,`{$tables['link_type']['name']}` t WHERE  l.LINK_TYPE=t.ID and (l.`STATUS` = '2' OR {$permissions['permission_links_arts']}) {$email_conf} AND t.`FEATURED` = '0' {$expire_where} {$user_where} ORDER BY ".$sorter->getOrder();
       
	$links = $db->CacheGetAll($query);
        $linksCollection->setElements($links);
        $linksCollection->setCountWithoutLimit($db->getOne("SELECT FOUND_ROWS() as count"));

        $this->view->assign('feat_links', $featLinksCollection);
        $this->view->assign('links', $linksCollection);
    }

    public function registerAction()
    {
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $LANGUAGE = 'en';
        $this->view->assign('languages', select_lang('lang/'));
        $this->view->assign('LANGUAGE', LANGUAGE);

        $validators = $this->_getProfileValidators();
//var_dump($validators);die();


        $validators['rules']['PASSWORD'] = array(
            'required' => true,
            'minlength' => USER_PASSWORD_MIN_LENGTH,
            'maxlength' => USER_PASSWORD_MAX_LENGTH
        );

        if (VISUAL_CONFIRM == 1) {
            require_once 'include/functions_imgverif.php';
            $imagehash = fetch_captcha_hash();
            $this->view->assign('imagehash', $imagehash);

            $validators['rules']['CAPTCHA'] = array(
                'required' => true,
                'remote' => array(
                    'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                    'type' => "post",
                    'data' => array(
                        'action' => "isCaptchaValid",
                        'table' => "img_verification",
                        'field' => "CAPTCHA",
                        'IMAGEHASH' => $imagehash
                    )
                )
            );

        }

        $validator = new Validator($validators);
        $user = new Model_User();
        if ($this->isPost()) {
            $res = $validator->validate($_POST);
            //do the math
            if (VISUAL_CONFIRM == 2) {
                if (!isset($_SESSION['DO_MATH_N1']) || !isset($_SESSION['DO_MATH_N2']))
                    $res['DO_MATH'] = 'Not a legitimate submit';
                if ($_REQUEST['DO_MATH'] != $_SESSION['DO_MATH_N1'] + $_SESSION['DO_MATH_N2'])
                    $res['DO_MATH'] = 'Please Check Your Math Below';
            }
// end do math  
            if (empty($res)) {
				//generate e-mail activation key
				$key=substr(md5(encrypt_password($this->getParam('PASSWORD'))),2,6);

                $data = array(
                    'ID' => $db->GenID($tables['user']['name'].'_SEQ'),
					'LOGIN' => $this->getParam('LOGIN'),
                    'NAME' => $this->getParam('LOGIN'),
                    'LANGUAGE' => $this->getParam('LANGUAGE'),
                    'EMAIL' => $this->getParam('EMAIL'),
                    'PASSWORD' => encrypt_password($this->getParam('PASSWORD')),
                    'ANONYMOUS' => $this->getParam('ANONYMOUS'),
					'CONFIRM' => $key,
					'REGISTRATION_DATE' => time(),
					'SUBMIT_NOTIF' => 0,
					'PAYMENT_NOTIF' => 0,
                    'ACTIVE' => (EMAIL_CONFIRMATION == true ? 0 : 1),
					'EMAIL_CONFIRMED' => 0

                );

                if (ALLOW_AUTHOR_INFO) {
                    $data['WEBSITE_NAME'] = $this->getParam('WEBSITE_NAME');
                    $data['WEBSITE'] = $this->getParam('WEBSITE');
                    $data['INFO'] = $this->getParam('INFO');
                    $data['ANONYMOUS'] = $this->getParam('ANONYMOUS');
                }

                $res = $user->insert($data);
				if($res)
    {
   	if(EMAIL_CONFIRMATION == 1){
   				  $userData=$user->getUserByLogin($this->getParam('LOGIN'))->getOptions();
   				 
$emailTpl = get_email_template('NTF_USER_ACTIVATION_TPL');
   		$mail = get_emailer_admin();
   		$mail->AddAddress($data['EMAIL'], $data['NAME']);
   		//Add email subject
   		$emailSubject = $emailTpl['SUBJECT'];
   		$emailSubject = replace_email_vars($emailSubject, $data, 5);
   		$mail->Subject = trim($emailSubject);

   		//Add email body
   		$emailBody = $emailTpl['BODY'];
   		$emailBody = str_replace(
   					 array(
   						 '{USER_ACTIVATION_URL}'
   					 ),
   					 array(
   				 SITE_URL."user/activate/?uid={$userData['ID']}&key=$key"
   					 ),
   					 $emailBody);
   		$emailBody = replace_email_vars($emailBody, $userData, 5);
   		$mail->Body = trim ($emailBody);

   				 //Send email
   				 if (!$mail->Send())
   				 {
   					 //Error occured while sending email
   					 $this->fm()->error('Could not deliver confirmation e-mail. Cause: <br/>'.$mail->ErrorInfo);
   					 http_custom_redirect(DOC_ROOT . '/user/register');
   				 }
   				 else
   				 {
   					 //Email sent successfully
   					 http_custom_redirect(DOC_ROOT . '/user/thankyou?email_send=1');
   				 }

   				 //Clear all addresses (and attachments)
   				 $mail->ClearAddresses();
   				 $mail->ClearAttachments();
   			 	}
   			 	else
   			 	{
   				 //Email sent successfully
   				 http_custom_redirect(DOC_ROOT . '/user/thankyou');
   			 	}
   			 }
				
            } else {
                //var_dump($res);die();
                foreach ($res as $field=>$error) {
                    $this->fm()->error($field.': '.$error);
                }
                http_custom_redirect(DOC_ROOT . '/user/register');
            }
        }

        $INFO_limit = (isset($data['INFO']) && strlen(trim($data['INFO'])) > 0 ? 255 - strlen(trim($data['INFO'])) : '255');
        
        // do the math
        if (VISUAL_CONFIRM == 2) {
            $n1 = rand(1, 9);
            $n2 = rand(1, 9);
            $hash = do_math($n1, $n2);
            $_SESSION['DO_MATH_N1'] = $n1;
            $_SESSION['DO_MATH_N2'] = $n2;
        
            $this->view->assign('DO_MATH_N1', $n1);
            $this->view->assign('DO_MATH_N2', $n2);
        }
// end do math  
        $this->view->assign('InfoLimit', $INFO_limit);
        $this->view->assign('yesno', array("1" => "Yes", "0" => "No"));
        $this->layout->assign('errors', $res);
    }

    public function thankyouAction()
    {

    }

    public function confirmedAction()
    {

    }

	public function activateAction()
	{
		$uid=intval($_REQUEST['uid']);
		$key=preg_replace('/[^\da-f]/','',strtolower($_REQUEST['key']));
		if(strlen($key)!=6)
		{
			$this->view->assign("failure", 1);
			return;
		}
		$user = new Model_User();
		$result = $user->activate($uid,$key);

		if($result)
		{
			$this->view->assign("success", 1);
		}
		else
		{
			$this->view->assign("failure", 1);
		}
	}

    public function sendpasswordAction()
    {
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        //RALUCA: JQuery validation related
        $validators = array(
            'rules' => array(
                'LOGIN' => array(
                    'required' => true,
                    'minlength' => USER_LOGIN_MIN_LENGTH,
                    'maxlength' => USER_LOGIN_MAX_LENGTH,
                    'remote' => array(
                        'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                        'type' => "post",
                        'data' => array(
                            'action' => "isUsername",
                            'table' => "user",
                            'field' => "LOGIN"
                        )
                    )
                ),
                'EMAIL' => array(
                    'email' => true,
                    'required' => true,
                    'remote' => array(
                        'url' =>  DIRECTORY_ROOT . "/include/validation_functions.php",
                        'type' => "post",
                        'data' => array(
                            'action' => "isBannedEmail",
                            'table' => "user",
                            'field' => "EMAIL"
                        )
                    )
                )
            ),
            'messages' => array(
                'EMAIL' => _L("This email is already in our database or banned."),
                'LOGIN' => _L("This is not a valid username."),
                'CAPTCHA' => _L("Incorrect code.")
            )
        );

        if (VISUAL_CONFIRM == 1) {
            require_once 'include/functions_imgverif.php';
            $imagehash = fetch_captcha_hash();
            $this->view->assign('imagehash', $imagehash);

            $validators['rules']['CAPTCHA'] = array(
                'required' => true,
                'remote' => array(
                    'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                    'type' => "post",
                    'data' => array(
                        'action' => "isCaptchaValid",
                        'table' => "img_verification",
                        'field' => "CAPTCHA",
                        'IMAGEHASH' => $imagehash
                    )
                )
            );
        }
        
        if (VISUAL_CONFIRM == 2) {
            $validators['rules']['DO_MATH'] = array(
                'required' => true
            );
        }
        
        $vld = json_custom_encode($validators);
        $this->view->assign('validators', $vld);

        if ($this->isPost()) {
            $data = get_table_data('user');

            //Take care of white-space chars
            $data = filter_white_space($data);

            //RALUCA: JQuery validation related - server side.
            $validator = new Validator($validators);
            $validator_res = $validator->validate($_POST);
            //RALUCA: end of JQuery validation related - server side.
            
            if (VISUAL_CONFIRM == 2) {
                if (!isset($_SESSION['DO_MATH_N1']) || !isset($_SESSION['DO_MATH_N2']))
                    $validator_res['DO_MATH'] = 'Not a legitimate submit';
                if ($_REQUEST['DO_MATH'] != $_SESSION['DO_MATH_N1'] + $_SESSION['DO_MATH_N2'])
                    $validator_res['DO_MATH'] = 'Please Check Your Math Below';
            }

            if (empty($validator_res)) {
                $user = $db->GetRow("SELECT `ID`, `LOGIN`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `LOGIN` LIKE " . $db->qstr($data['LOGIN']) . " AND `EMAIL` LIKE " . $db->qstr($data['EMAIL']) . " AND `ACTIVE` = '1'");

                if (is_array($user) && !empty($user)) {
                    $data['ID'] = $user['ID'];

                    $new_password         = create_password(8);
                    $data['CONFIRM']      = create_password(6);
                    $data['NEW_PASSWORD'] = encrypt_password($new_password);
                    unset ($data['user'], $data['email']);
                    $dbUpdate = $db->Replace($tables['user']['name'], $data, 'ID', true);

                    if ($dbUpdate == 1)
                    {
                        $user['PASSWORD'] = $new_password;
                        $passwordRecoverUrl = (substr(SITE_URL, -1) == "/" ? SITE_URL : SITE_URL . "/");
                        $passwordRecoverUrl .= "user/recovered?uid={$user['ID']}&key={$data['CONFIRM']}";

                        $loginURL = (substr(SITE_URL, -1, 1) == '/' ? SITE_URL : SITE_URL . '/') . 'login.php';


                        //Get email template
                        $emailTpl = get_email_template('NTF_USER_PASSWORD_TPL');

                        //if email tpl is available
                        if (is_array($emailTpl) && !empty($emailTpl)) {
                            $mail = get_emailer_admin();
                            $mail->AddAddress($user['EMAIL'], $user['NAME']);

                            //Add email subject
                            $emailSubject = $emailTpl['SUBJECT'];
                            $emailSubject = str_replace('{PASSWORD_RECOVER_URL}', $passwordRecoverUrl, $emailSubject);
                            $emailSubject = str_replace('{USER_LOGIN_PAGE}', $loginURL, $emailSubject);
                            $emailSubject = replace_email_vars($emailSubject, $user, 6);
                            $emailSubject = replace_email_vars($emailSubject, $user, 5);
                            $mail->Subject = trim($emailSubject);

                            //Add email body
                            $emailBody = $emailTpl['BODY'];
                            $emailBody = str_replace('{PASSWORD_RECOVER_URL}', $passwordRecoverUrl, $emailBody);
                            $emailBody = str_replace('{USER_LOGIN_PAGE}', $loginURL, $emailBody);
                            $emailBody = replace_email_vars($emailBody, $user, 6);
                            $emailBody = replace_email_vars($emailBody, $user, 5);
                            $mail->Body = trim($emailBody);

                            //Send email
                            if (!$mail->Send()) {
                                $this->fm()->error($mail->ErrorInfo);
                            } else {
                            }
                            $this->view->assign('email_status', 1);

                            //Clear all addresses (and attachments)
                            $mail->ClearAddresses();
                            $mail->ClearAttachments();

                            $this->fm()->success('Check your email for recovery instructions');

                            //Free memory
                            unset($user, $data, $mail, $emailTpl, $emailSubject, $emailBody, $passwordRecoverUrl, $loginURL, $new_password);
                        }
                    }

                    //Free memory
                    unset($data, $new_password);
                } else {
                    $this->fm()->error(_L('The details you supplied do not match any registered user.'));
                    unset($data, $_POST, $_GET, $_REQUEST);
                }

                if (isset($user)) {
                    unset($user);
                }
            } else {
                $this->fm()->formValidation($validator_res);
                $this->view->assign($_POST);
                unset($data);
            }
        }
        
        // do the math
	if (VISUAL_CONFIRM == 2) {
	    $n1 = rand(1, 9);
	    $n2 = rand(1, 9);
	    $hash = do_math($n1, $n2);
	    $_SESSION['DO_MATH_N1'] = $n1;
	    $_SESSION['DO_MATH_N2'] = $n2;

	    $this->view->assign('DO_MATH_N1', $n1);
	    $this->view->assign('DO_MATH_N2', $n2);
            
             $validators['rules']['DO_MATH'] = array(
                'required' => true
            );
	}
        // end do math 

        if (isset($data)) {
            $this->view->assign($data);
            unset($data);
        }
    }

    public function recoveredAction()
    {
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        if (!Model_CurrentUser::getInstance()->isLoggedIn()) {
            $user_id = (!empty($_REQUEST['uid']) ? clean_string_paranoia($_REQUEST['uid']) : 0);
            $confirm_key = (!empty($_REQUEST['key']) ? clean_string_paranoia($_REQUEST['key']) : 0);

            if (!empty($user_id) && !empty($confirm_key)) {
                $data = $db->GetRow("SELECT * FROM `{$tables['user']['name']}` WHERE `ID` = " . $db->qstr($user_id) . " AND `CONFIRM` = " . $db->qstr($confirm_key));
                if (!empty($data)) {
                    $data['PASSWORD'] = $data['NEW_PASSWORD'];
                    $data['NEW_PASSWORD'] = '';
                    $data['CONFIRM'] = '';

                    $where = " `ID` = " . $db->qstr($user_id);

                    if ($db->AutoExecute($tables['user']['name'], $data, 'UPDATE', $where)) {
                        $this->fm()->success(_L('You can now login with your new password.'));
                    } else {
                        $this->fm()->error(_L('An error occured while saving your new password.'));
                    }
                }
            }
        }
    }

    protected function _getProfileValidators($exclude = null)
    {
        $validators = array(
            'rules' => array(
                'LOGIN' => array(
                    'required' => true,
                    'minlength' => USER_NAME_MIN_LENGTH,
                    'maxlength' => USER_NAME_MAX_LENGTH,
                    'remote' => array(
                        'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                        'type' => "post",
                        'data' => array(
                            'action' => "isRegistrationUsername",
                            'table' => "user",
                            'field' => "LOGIN"
                        )
                    )
                ),
                'LANGUAGE' => array(
                    'required' => true
                ),
                'EMAIL' => array(
                    'required' => true,
                    'email' => true,
                    'remote' => array(
                        'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                        'type' => "post",
                        'data' => array(
                            'action' => "isRegistrationEmail",
                            'table' => "user",
                            'field' => "EMAIL"
                        )
                    )
                ),
                'PASSWORDC' => array(
                    'required' => true,
                    'equalTo' => "#PASSWORD"
                ),
            ),
             'messages' => array(
                'EMAIL' =>  _L("This email is already in our database or banned."),
                'LOGIN' =>  _L("This is not a valid username."),
                'CAPTCHA' =>  _L("Incorrect code."),
                'PASSWORDC' =>  _L("Passwords do not match.")
            )
        );
        $validators['rules']['URL'] = array(
            'remote' => array(
                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                'type' => "post",
                'data' => array(
                    'action' => "isDomainBanned",
                    'table' => "link",
                    'field' => "url"
                )
            )
        );
        $validators['rules']['INFO'] = array(
            'minlength' => 0,
            'maxlength' => 255
        );
        if (!is_null($exclude) && is_array($exclude)) {
            foreach ($exclude as $validator) {
                unset($validators['rules'][$validator]);
            }
        }

        return $validators;
    }

}