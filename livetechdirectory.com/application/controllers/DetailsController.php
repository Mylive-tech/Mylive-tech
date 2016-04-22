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
class DetailsController extends PhpldfrontController
{
    public function indexAction()
    {
        $idLink = $this->getParam('idLink');
        $linkModel = new Model_Link();
        $categoryModel = new Model_Category();
        $link = $linkModel->getLink($idLink);
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        if (is_null($link)) {
            throw new Phpld_Exception_NotFound();
        }

        if (VISUAL_CONFIRM_LINK_COMMENTS == 1 && VISUAL_CONFIRM == 1) {
            require_once 'include/functions_imgverif.php';
            $imagehash = fetch_captcha_hash();
            $this->view->assign('imagehash', $imagehash);
            unset ($imagehash);
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

     if($link['CATEGORY_ID'] > 0)
    {
        $category = $categoryModel->getCategory($link['CATEGORY_ID']);
        $parent = $category->getParent();
        if (!is_null($parent)) {
            $this->_breadcrumbs->add($parent['TITLE'], $parent->getUrl());
        }
        $this->_breadcrumbs->add($category['TITLE'], $category->getUrl());    
        $link['CATEGORY_URL'] = $category->getUrl();
        $link['CATEGORY_TITLE'] = $category['TITLE'];
    }
        $this->_breadcrumbs->add($link['TITLE']);

        $comments = $db->GetAll("SELECT * FROM `{$tables['comment']['name']}` WHERE `TYPE` = '1' AND `STATUS` = '2' AND `ITEM_ID` = ".$db->qstr($link['ID']));
 // Check for expiry for RATINGS
        $prevTime = time() - LINK_RATING_TIME;
        $date = date("Y-m-d H:i:s", $prevTime);
		$IPADDRESS = $_SERVER['REMOTE_ADDR'];
        $rating = $db->GetOne("SELECT COUNT(*) FROM `{$tables['linkrating']['name']}` WHERE `LINK_ID` = ".$db->qstr($link['ID'])." AND `IPADDRESS` ='$IPADDRESS' AND `DATE_ADDED` > '$date'");
        if($rating >= 1) {
            $this->view->assign('rating_disabled', 'disabled');
        }
        $this->setTitle(!empty($link['PAGE_TITLE']) ? $link['PAGE_TITLE'] : $link['TITLE']);
        $this->setMeta('keywords', $link['META_KEYWORDS']);
        $this->setMeta('description', $link['META_DESCRIPTION']);
        $this->view->assign('group_image_details', getLinkImages($link['IMAGEGROUP']));
		$add_links = $db->GetAll("SELECT * FROM `{$tables['additional_link']['name']}` WHERE `LINK_ID` = ".$db->qstr($link['ID']));
        $this->view->assign("add_links", $add_links);
        $this->view->assign('comments', $comments);
		$RATING                   = $link['RATING'] / $link['VOTES'];
        $link['RATING']           = substr($RATING, 0, 3);
        $this->view->assign('LINK', $link);
    }

    public function commentAction()
    {
        $idLink = $this->getParam('idLink');
        $linkModel = new Model_Link();
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $link = $linkModel->getLink($idLink);

        $user = Model_CurrentUser::getInstance();
        $userData = $user->loadData();

        if ($this->isPost()) {
            if (REQUIRE_REGISTERED_USER_LINK_COMMENT == 1) {
                if (!Model_CurrentUser::getInstance()->isLoggedIn()) {
                    http_custom_redirect(DOC_ROOT.'/login');
                }
            }
            $data['CATEGORY_ID'] = $link['CATEGORY_ID'];

            $data['rights'] =  user_needs_approval($_SESSION['phpld']['user']['id'], $data['CATEGORY_ID']);
            // Assign values
            $cdata['ITEM_ID']   = $link['ID'];
            //        var_dump($action[1]);
            // Type: 1 - Links Comments, 2 - Articles Comments
            $cdata['TYPE']          = '1';
            $cdata['USER_ID']       = $user->getId();
            $cdata['USER_NAME']     = $userData['NAME'];
            $cdata['COMMENT']       = $this->getParam('comment');
            $cdata['DATE_ADDED']    = date("Y-m-d H:i:s");
            $cdata['IPADDRESS']     = $_SERVER['REMOTE_ADDR'];
            $this->view->assign('COMMENT', $cdata['COMMENT']);
            if (!$user->isLoggedIn()) {
                $count = '0';
                $cdata['USER_ID']    = '0';
            } else {
                // Count Approved Comments for the user
                $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['comment']['name']}` WHERE `TYPE` = '1' AND `STATUS` = '2' AND `USER_ID` = ".$db->qstr($cdata[USER_ID]));
            }
            if (empty($_SESSION['phpld']['user']['name'])) {
                $cdata['USER_NAME'] = 'Guest';
            }
            if ($count < AUTO_APPROVE_LINK_COMMENTS || AUTO_APPROVE_LINK_COMMENTS_ONOFF == 0) {
                $cdata['STATUS'] = 1;
                $this->view->assign('needs_approval_msg', 1);
            } else
                $cdata['STATUS'] = 2;
            if ($data['rights']['addLink'] == 1 || $data['rights']['editLink'] == 1 || $data['rights']['delLink'] == 1) {
                $cdata['STATUS'] = 2;
            }
            if(!empty($_POST['comment'])) {
		 
                // Image Verification
                if (VISUAL_CONFIRM_LINK_COMMENTS == 1 && VISUAL_CONFIRM == 1) {
                    if ($data['rights']['addLink'] == 1 || $data['rights']['editLink'] == 1 || $data['rights']['delLink'] == 1) {
                        $imgCheck = 1;
                    } else {
                        $imgCheck = $db->GetOne("SELECT COUNT(*) FROM `{$tables['img_verification']['name']}` WHERE `IMGHASH` = ".$db->qstr($_POST[IMAGEHASH])." AND `IMGPHRASE` = ".$db->qstr($_POST[CAPTCHA]));
                    }
                    if ($imgCheck == 1) {
                        $db->Execute("UPDATE `{$tables['link']['name']}` SET `COMMENT_COUNT` = `COMMENT_COUNT` + 1 WHERE `ID` = ".$db->qstr($cdata['ITEM_ID'])) or die(mysql_error());

                        if (db_replace('comment', $cdata, 'ID')) {
                            $_SESSION['comm_posted'] = 1;
                        }
                        $this->fm()->success(_L('Your comment submitted'));
                        http_custom_redirect($_SERVER['HTTP_REFERER']);

                    } else {
                        $this->fm()->error(_L('Enter correct captcha code'));
                        http_custom_redirect($_SERVER['HTTP_REFERER']);
                    }
                } elseif (VISUAL_CONFIRM_LINK_COMMENTS == 1 && VISUAL_CONFIRM == 2) {
		  
		    if (!isset($_SESSION['DO_MATH_N1']) || !isset($_SESSION['DO_MATH_N2'])){
			$this->fm()->error(_L('Not a legitimate submit'));
			http_custom_redirect($_SERVER['HTTP_REFERER']);                    
		    }
		    if ($_REQUEST['DO_MATH'] != $_SESSION['DO_MATH_N1'] + $_SESSION['DO_MATH_N2']){
			$this->fm()->error(_L('Please Check Your Math Below'));
			http_custom_redirect($_SERVER['HTTP_REFERER']);  
		    }
		    
		    $db->Execute("UPDATE `{$tables['link']['name']}` SET `COMMENT_COUNT` = `COMMENT_COUNT` + 1 WHERE `ID` = ".$db->qstr($cdata['ITEM_ID'])) or die(mysql_error());

                    if (db_replace('comment', $cdata, 'ID')) {
                        $_SESSION['comm_posted'] = 1;
                    }
                    $this->fm()->success(_L('Your comment submitted'));
                    http_custom_redirect($_SERVER['HTTP_REFERER']);
		
		} else {
                    // Write to DB
                    $db->Execute("UPDATE `{$tables['link']['name']}` SET `COMMENT_COUNT` = `COMMENT_COUNT` + 1 WHERE `ID` = ".$db->qstr($cdata['ITEM_ID'])) or die(mysql_error());
                    db_replace('comment', $cdata, 'ID');
                    $this->fm()->success(_L('Your comment submitted'));
                    http_custom_redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                if (!empty($_POST['submit'])) {
                    $this->fm()->error(_L('Some error occured'));
                    http_custom_redirect($_SERVER['HTTP_REFERER']);
                }
            }
        }
    }

    public function rateAction()
    {
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $idLink = $this->getParam('idLink');

        if (REQUIRE_REGISTERED_USER_LINK_RATING == 1 && !Model_CurrentUser::getInstance()->isLoggedIn()) {
            http_custom_redirect(DOC_ROOT.'/login');
        }

        if ($this->isPost()) {
            $ratingValue = $this->getParam('RATING');
            $db->Execute("UPDATE `{$tables['link']['name']}` SET `RATING` = `RATING` + {$ratingValue}, `VOTES` = `VOTES` + 1 WHERE `ID` = ".$db->qstr($idLink)) or die(mysql_error());
            $rating['LINK_ID']  = $idLink;
            $rating['IPADDRESS'] = $_SERVER['REMOTE_ADDR'];
            db_replace('linkrating', $rating, 'LINK_ID');
            $this->fm()->success(_L('Your vote counted'));
            http_custom_redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function tellfriendAction()
    {
        $client_info = array();
        require_once 'include/client_info.php';
        require_once 'include/functions_imgverif.php';
        $imagehash = fetch_captcha_hash();
        $this->view->assign('imagehash', $imagehash);
        //RALUCA: JQuery validation related
        $validators = array(
            'rules' => array(
                'MESSAGE' => array(
                    'required' => true
                ),
                'EMAIL' => array(
                    'required' => true,
                    'email' => true
                ),
                'FRIEND_EMAIL' => array(
                    'required' => true,
                    'email' => true
                )
            ),
            'messages' => array(
                'CAPTCHA'=> array(
                    'remote'  	=> _L("Incorrect code.")
                )
            )
        );
	
	 

        if (VISUAL_CONFIRM == 1) {
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
        if (VISUAL_CONFIRM == 2) {
            $validators['rules']['DO_MATH'] = array(
                'required' => true
            );
	    
	   

        }

        $vld = json_custom_encode($validators);
        $this->view->assign('validators', $vld);

        $validator = new Validator($validators);
        //RALUCA: end of JQuery validation related

        if (isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
            $linkModel = new Model_Link();
            $link = $linkModel->getLinkById($_REQUEST['id']);
            $MESSAGE = "Hi,
I found this at ".SITE_NAME." and I thought you would be interested: ";

            $MESSAGE .= $link->getUrl();
            $this->view->assign('MESSAGE', $MESSAGE);
        }

        //If current user is banned, show a custom error message
        //and stop the rest of the script
        check_if_banned();
        if (BOT_CHECK == 1)
        check_botscout($client_info['IP'],$_REQUEST['EMAIL'],BOT_KEY);
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
                try {
                    $data = array ();
                    $data['EMAIL']   = (!empty ($_REQUEST['EMAIL'])   ? strip_white_space ($_REQUEST['EMAIL'])   : '');
                    $data['FRIEND_EMAIL']   = (!empty ($_REQUEST['FRIEND_EMAIL'])   ? strip_white_space ($_REQUEST['FRIEND_EMAIL'])   : '');
                    $data['MESSAGE'] = (!empty ($_REQUEST['MESSAGE']) ? strip_white_space ($_REQUEST['MESSAGE']) : '');

                    //Initialize emailer
                    require_once 'libs/phpmailer/class.phpmailer.php';
                    require_once 'libs/phpmailer/class.phpmailercustom.php';

                    $mail            = new PHPMailerCustom();
                    $mail->PluginDir = 'libs/phpmailer/';

                    //Load email method
                    $mail->Mailer    = EMAIL_METHOD;

                    $mail->Subject = SITE_NAME." Recommendation";

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
                    $mail->From     = $data['EMAIL'];
                    $mail->FromName = $data['EMAIL'];
                    $mail->AddAddress($data['FRIEND_EMAIL']);
                    $mail->Body = $data['MESSAGE'];

                    //Send email
                    if (!$mail->Send())
                    {
                        throw new Exception($mail->ErrorInfo);
                    }

                    //Clear all addresses (and attachments) for next loop
                    $mail->ClearAddresses();
                    $mail->ClearAttachments();
                    $this->fm()->success('Contact message sent');
                } catch (Exception $e) {
                    $this->fm()->error('An error occured while sending contact email');
                    $this->fm()->error($e->getMessage());
                }
            } else {
               
                foreach ($res as $field=>$error) {
                    $this->fm()->error($field.': '.$error);
                }
            }
            http_custom_redirect($_SERVER['HTTP_REFERER']);
        }
	if (VISUAL_CONFIRM == 2) {
	    $n1 = rand(1, 9);
	    $n2 = rand(1, 9);
	    $hash = do_math($n1, $n2);
	    $_SESSION['DO_MATH_N1'] = $n1;
	    $_SESSION['DO_MATH_N2'] = $n2;

	    $this->view->assign('DO_MATH_N1', $n1);
	    $this->view->assign('DO_MATH_N2', $n2);
	}
    }
	public function contactlistingAction()
    {
        $client_info = array();
        require_once 'include/client_info.php';
        require_once 'include/functions_imgverif.php';
        $imagehash = fetch_captcha_hash();
        $this->view->assign('imagehash', $imagehash);
        //RALUCA: JQuery validation related
        $validators = array(
            'rules' => array(
                'MESSAGE' => array(
                    'required' => true
                ),
                'EMAIL' => array(
                    'required' => true,
                    'email' => true
                ),
                'NAME' => array(
                    'required' => true      
                )
            ),
            'messages' => array(
                'CAPTCHA'=> array(
                    'remote'  	=> _L("Incorrect code.")
                )
            )
        );

        if (VISUAL_CONFIRM == 1) {
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
        if (VISUAL_CONFIRM == 2) {
            $validators['rules']['DO_MATH'] = array(
                'required' => true
            );

        }

        $vld = json_custom_encode($validators);
        $this->view->assign('validators', $vld);

        $validator = new Validator($validators);
        //RALUCA: end of JQuery validation related

       
      $linkModel = new Model_Link();
	  $link = $linkModel->getLinkById($_REQUEST['id']);
	    $MESSAGE = "I saw your profile on ".SITE_NAME.". \nI'd love to schedule a time for us to talk.\n Please reply to this email if you are interested.\n";

            $MESSAGE .= $link->getUrl()."\n\n";
            $this->view->assign('MESSAGE', $MESSAGE);
	  $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
          
        if (is_null($link)) {
            throw new Phpld_Exception_NotFound();
        }
	

$this->_breadcrumbs->add('contact '.$link['TITLE']);
        //If current user is banned, show a custom error message
        //and stop the rest of the script
        check_if_banned();
        if (BOT_CHECK == 1)
        check_botscout($client_info['IP'],$_REQUEST['EMAIL'],BOT_KEY);
        //Make an additional check if client is allowed to post/submit
        //[Spam] protection
        require_once 'include/check_post_rules.php';
        $post_rules_unauthorized = check_post_rules($_POST);
        if ($this->isPost()) {
            $res = $validator->validate($_POST);
            if (empty($res)) {
                try {
                    $data = array ();
                    $data['EMAIL']   = (!empty ($_REQUEST['EMAIL'])   ? strip_white_space ($_REQUEST['EMAIL'])   : '');
                    $data['NAME']   = (!empty ($_REQUEST['NAME'])   ? strip_white_space ($_REQUEST['NAME'])   : '');
                    $data['MESSAGE'] = (!empty ($_REQUEST['MESSAGE']) ? $_REQUEST['MESSAGE'] : '');
		    $data['PHONE'] = (!empty ($_REQUEST['PHONE']) ? strip_white_space ($_REQUEST['PHONE']) : '');
		 //  $data['SUBJECT'] = (!empty ($_REQUEST['SUBJECT']) ? strip_white_space ($_REQUEST['SUBJECT']) : '');
					$data['MESSAGE'] .= "\n Contact Number: ". $data['PHONE'];
                    //Initialize emailer
                    require_once 'libs/phpmailer/class.phpmailer.php';
                    require_once 'libs/phpmailer/class.phpmailercustom.php';

                    $mail            = new PHPMailerCustom();
                    $mail->PluginDir = 'libs/phpmailer/';

                    //Load email method
                    $mail->Mailer    = EMAIL_METHOD;

                    $mail->Subject ="I saw your profile at ".SITE_NAME;

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
                    $mail->From     = $data['EMAIL'];
                    $mail->FromName = $data['NAME'];
                    $mail->AddAddress($link['OWNER_EMAIL'], $link['OWNER_NAME']);
                    $mail->Body = $data['MESSAGE'];
					
					

                    //Send email
                    if (!$mail->Send())
                    {
                        throw new Exception($mail->ErrorInfo);
                    }

                    //Clear all addresses (and attachments) for next loop
                    $mail->ClearAddresses();
                    $mail->ClearAttachments();
                    $this->fm()->success('Contact message sent');
				
                } catch (Exception $e) {
                    $this->fm()->error('An error occured while sending contact email');
                    $this->fm()->error($e->getMessage());
                }
            } else {
                $this->fm()->error('An error occured while sending contact email');
            }
			 // $this->fm()->success('Contact message sent');
            http_custom_redirect($link->getUrl());
        }
    }
}