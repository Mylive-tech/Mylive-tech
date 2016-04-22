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
 
/**
 * Usefull function allowed to be executed only from admin area!!
 */

if (!defined ('IN_PHPLD_ADMIN'))
{
   die("!! ERROR !! You are not allowed to run this script!");
}

//Define some error messages for email templates
$email_sent_errors_EMAIL = _L('An email was already sent to this recipient on #DATE# with link URL #URL# and link title #TITLE#');
$tpl->assign('email_sent_errors_EMAIL', $email_sent_errors_EMAIL);

$email_sent_errors_URL = _L('An email with the same link URL was already sent to #EMAIL# on #DATE# with link title #TITLE#');
$tpl->assign('email_sent_errors_URL', $email_sent_errors_URL);

$email_sent_errors_TITLE = _L('An email with the same link title was already sent to #EMAIL# on #DATE# with link URL #URL#');
$tpl->assign('email_sent_errors_TITLE', $email_sent_errors_TITLE);
?>