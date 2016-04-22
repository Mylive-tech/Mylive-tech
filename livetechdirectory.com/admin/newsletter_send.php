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

if (empty ($_POST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
	$_SESSION['return'] = $_SERVER['HTTP_REFERER'];

if (empty($_REQUEST['submit'])) {
	$tpl->assign('submit_session', registerAdminSubmitSession());
} else {
	checkAdminSubmitSession(clean_string($_POST['submit_session']));
	$tmpl = $db->GetRow("SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($_POST['NEWSL_TPL_ID']));
    $subject = replace_email_vars($tmpl['SUBJECT'], $data);
	$body 	= replace_email_vars($tmpl['BODY'], $data);
	$use_html = $_REQUEST['USE_HTML'];
    if (isset($_REQUEST['TEST_EMAIL']) && $_REQUEST['TEST_EMAIL'] != '') {
        $test_email = $_REQUEST['TEST_EMAIL'];
        $rs = $db->Execute ("DELETE FROM `{$tables['newsletter_queue']['name']}`");
        $rs = $db->Execute("INSERT INTO `{$tables['newsletter_queue']['name']}` (`EMAIL`, `TEMPLATE_ID`, `STATUS`, `USE_HTML`)
			   VALUES ('{$test_email}', ".$db->qstr($_POST['NEWSL_TPL_ID']).", '0', ".$use_html.")");
    } else {
        $rs = $db->Execute("DELETE FROM `{$tables['newsletter_queue']['name']}`");
   	$sqlSubscribers = "SELECT DISTINCT OWNER_EMAIL FROM `{$tables['link']['name']}` WHERE `OWNER_EMAIL` IS NOT NULL AND `OWNER_EMAIL` <> '' AND OWNER_NEWSLETTER_ALLOW = 1";
        $subscribers = $db->GetAll($sqlSubscribers);
        for ($i = 0; $i<count($subscribers); $i++) {
            $rs = $db->Execute("INSERT INTO `{$tables['newsletter_queue']['name']}` (`EMAIL`, `TEMPLATE_ID`, `STATUS`, `USE_HTML`)
			       VALUES ('{$subscribers[$i]['OWNER_EMAIL']}', ".$db->qstr($_POST['NEWSL_TPL_ID']).", '0', ".$use_html.")");
        }
    }
    $tpl->assign('submit_session', registerAdminSubmitSession());
    $tpl->assign('posted', true);
}


$rs   = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '1'");
$tpls = $rs->GetAssoc();
$tpl->assign('tpls', $tpls);


// Mail queue statistics
$stats =  array();
$stats['PENDING'] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['newsletter_queue']['name']}` WHERE `STATUS` = '0'");
$stats['SENT'] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['newsletter_queue']['name']}` WHERE `STATUS` = '1'");
$stats['FAILED'] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['newsletter_queue']['name']}` WHERE `STATUS` = '2'");
$stats['TOTAL'] = $stats['PENDING'] + $stats['SENT'] + $stats['FAILED'];

$tpl->assign('stats', $stats);

$tpl->assign('yes_no', array("0" => "No", "1" => "Yes"));

$content = $tpl->fetch(ADMIN_TEMPLATE.'/newsletter_send.tpl');

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

?>