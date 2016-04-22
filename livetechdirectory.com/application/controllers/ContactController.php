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
class ContactController extends PhpldfrontController
{
    public function indexAction()
    {
        $this->bc(_L('Contact'));
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $validators = array(
            'rules' => array(
                'NAME' => array(
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
                            'field' => "OWNER_EMAIL"
                        ))
                ),
                'SUBJECT' => array(
                    'required' => true
                ),
                'MESSAGE' => array(
                    'required' => true
                )
            ),
            'messages' => array(
                'EMAIL'=> array(
                    'remote'  	=>_L("This email is banned.")
                ),
                'CAPTCHA'=> array(
                    'remote'  	=> _L("Incorrect code.")
                )
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
                    'type'=> "post",
                    'data'=> array (
                        'action'    => "isCaptchaValid",
                        'table'     => "img_verification",
                        'field'     => "CAPTCHA",
                        'IMAGEHASH' => $imagehash
                    )
                )
            );
        }


        $vld = json_custom_encode($validators);
        //var_dump($vld);die();
        $this->layout->assign('validators', $vld);
        $validator = new Validator($validators);

        check_if_banned();
        if (BOT_CHECK == 1) {
            check_botscout($client_info['IP'],$_REQUEST['EMAIL'],BOT_KEY);
        }
        //Make an additional check if client is allowed to post/submit
        //[Spam] protection
        require_once 'include/check_post_rules.php';
        $post_rules_unauthorized = check_post_rules($_POST);


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
                $data = array ();
                $data['NAME']    = $this->getParam('NAME');
                $data['EMAIL']   = $this->getParam('EMAIL');
                $data['SUBJECT'] = $this->getParam('SUBJECT');
                $data['MESSAGE'] = $this->getParam('MESSAGE');

                $fields = array ('NAME', 'SUBJECT', 'MESSAGE');
                $hasBannedWords = if_word_is_banned($data, $fields);
                if ($hasBannedWords == 1)
                {
                    //Invalid submission,
                    //Block access
                    unset ($data);
                    $reason = _L('The administrator of this link directory, has banned words from your submition.');
                    gotoUnauthorized($reason);
                }

                //Initialize emailer
                require_once 'libs/phpmailer/class.phpmailer.php';
                require_once 'libs/phpmailer/class.phpmailercustom.php';

                $mail            = new PHPMailerCustom();
                $mail->PluginDir = 'libs/phpmailer/';

                //Load email method
                $mail->Mailer    = EMAIL_METHOD;
                switch (EMAIL_METHOD)
                {
                    case 'smtp' :
                        $mail->Host = EMAIL_SERVER;
                        if (strlen (EMAIL_USER) > 0)
                        {
                            $mail->SMTPAuth = true;
                            $mail->Username = EMAIL_USER;
                            $mail->Password = EMAIL_PASS;
                        }
                        break;
                    case 'sendmail' :
                        $mail->Sendmail = EMAIL_SENDMAIL;
                        break;
                    default :
                        break;
                }

                //Add sender
               if(EMAIL_METHOD == 'smtp')
                    $mail->From     = EMAIL_USER;
                else
                    $mail->From     = $data['EMAIL'];
                $mail->FromName = $data['NAME'];

                //Add receiver
                if (defined ('SITE_CONTACT_EMAIL') && strlen (SITE_CONTACT_EMAIL) > 0)
                {
                    $mail->AddAddress(SITE_CONTACT_EMAIL);
                }
                else
                {
                    $sql = "SELECT `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' LIMIT 1";
                    $admin = $db->GetRow($sql);
                    $mail->AddAddress($admin['EMAIL'], $admin['NAME']);

                    unset ($admin);
                }

                //Add subject
                $mail->Subject = $data['SUBJECT'];

                //Add body message
                $bodyMsg  = '';
                $bodyMsg .= "******************************\n";
                $bodyMsg .= _L('Subject').': '.$data['SUBJECT']."\n";
                $bodyMsg .= _L('Name')   .': '.$data['NAME']."\n";
                $bodyMsg .= _L('Email')  .': '.$data['EMAIL']."\n";
                $bodyMsg .= _L('Date')   .': '.gmdate ('Y-m-d H:i:s')."\n";
                $bodyMsg .= _L('IP')     .': '.(!empty ($client_info['IP']) ? $client_info['IP'] : _L('Unknown'))."\n";
                $bodyMsg .= "******************************\n\n";
                $bodyMsg .= trim ($data['MESSAGE']);

                $mail->Body = $bodyMsg;

                //Send email
                if (!$mail->Send())
                {
                    $this->fm()->error($mail->ErrorInfo);
                }
                else
                {
                    $this->fm()->success(_l('Contact message sent'));
                }
                $this->view->assign($data);
            } else {
                $this->fm()->formValidation($res);
                $this->view->assign($_POST);
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
        }
// end do math 
    
    }
}