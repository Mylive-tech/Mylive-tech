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
require ('init.php');

error_reporting(E_ERROR);

// Little bit tricky for first beta
$template_id = $db->GetRow("SELECT `TEMPLATE_ID` FROM `{$tables['newsletter_queue']['name']}` LIMIT 0, 1");
$tmpl = $db->GetRow("SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = " . $db->qstr($template_id['TEMPLATE_ID']));

$subject = replace_email_vars($tmpl['SUBJECT'], $data);
$body = replace_email_vars($tmpl['BODY'], $data);

if ($subject && $body) {
    $subscribers = $db->GetAll("SELECT * FROM `{$tables['newsletter_queue']['name']}` WHERE `STATUS`='0' LIMIT 0,3");

    for ($i = 0; $i < count($subscribers); $i++) {

        $mail = get_emailer();
	if($subscribers[$i]['USE_HTML'] == '1')
	     $mail->IsHTML(true);
        $mail->From = SITE_CONTACT_EMAIL;
        $mail->FromName = SITE_NAME;
	
	$data_user = $db->GetRow("SELECT * FROM `{$tables['user']['name']}` where `EMAIL` = '".$subscribers[$i]['EMAIL']."'");
	if(!empty($data_user['ID']))
	    $where_user = " OR OWNER_ID  = '".$data_user['ID']."'";
	$data_link = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` where OWNER_EMAIL  = '".$subscribers[$i]['EMAIL']."' {$where_user} ");
	
	$subject = replace_email_vars($tmpl['SUBJECT'], $data_link,1);
	$body = replace_email_vars($tmpl['BODY'], $data_link,1);
	
	$subject = replace_email_vars($subject, $data_link,2);
	$body = replace_email_vars($body, $data_link,2);
	
	$subject = replace_email_vars($subject, $data_user,5);
	$body = replace_email_vars($body, $data_user,5);
	
	
	
        $mail->Subject = $subject;
	if($subscribers[$i]['USE_HTML'] == '1')
	      $mail->Body = $body . '<p style="font-size: 12px; color: #969696;">If you want to unsubscribe from future mailings, please visit <a href="' . SITE_URL . 'unsubscribe.php?e=' . md5($subscribers[$i]['EMAIL']) . '">' . SITE_URL . 'unsubscribe.php?e=' . md5($subscribers[$i]['EMAIL']) . '</a></p>';
        else
	    $mail->Body = $body . '
	    If you want to unsubscribe from future mailings, please visit ' . SITE_URL . 'unsubscribe.php?e=' . md5($subscribers[$i]['EMAIL']) . '';
       
	$mail->AddAddress($subscribers[$i]['EMAIL']);

        //Send email
        if (!$mail->Send()) {
            $db->Execute("UPDATE `{$tables['newsletter_queue']['name']}` SET `STATUS`='2' WHERE `EMAIL`='" . $subscribers[$i]['EMAIL'] . "'");
        } else {
            $db->Execute("UPDATE `{$tables['newsletter_queue']['name']}` SET `STATUS`='1' WHERE `EMAIL`='" . $subscribers[$i]['EMAIL'] . "'");
        }


        //Clear all addresses (and attachments) for next loop
        $mail->ClearAddresses();
        $mail->ClearAttachments();

        //Free memory
        unset($mail, $bodyMsg, $_POST, $_REQUEST, $_GET);
    }
}
?>