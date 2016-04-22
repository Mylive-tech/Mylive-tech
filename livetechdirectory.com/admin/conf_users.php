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


$tpl->assign('admin_user', array (0       => ('Regular User'), 1      => _L('Administrator'), 2 => _L('Editor'), 3 => _L('Super Editor')));

$columns = array ('ID' => _L('ID'), 'LOGIN' => _L('Login'), 'NAME' => _L('Name'), 'LEVEL' => _L('User Level'),'LAST_LOGIN' => _L('Last Login'), 'SUBMIT_NOTIF' => _L('Submit Notif'), 'PAYMENT_NOTIF' => _L('Payment Notif'), 'EMAIL_CONFIRMED'=>_L('Email Confirmed'), 'ACTION'=>_L('Action'));
$tpl->assign('columns', $columns);

//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);
$tpl->assign('columnURLs', $columnURLs);

$tpl->assign('col_count', count ($columns) + 3);
$tpl->assign('level', $_GET['level']);
$tpl->assign('current_user_id'      , $_SESSION['phpld']['adminpanel']['id']);
$tpl->assign('current_user_is_admin', $_SESSION['phpld']['adminpanel']['is_admin']);

$min_keyword_length_comment = _L("Type in the keyword(s) or user id you're searching for.");
$min_keyword_length         = (!empty ($LinksResults['Min_Word_Length']) ? $LinksResults['Min_Word_Length'] : '4');
$min_keyword_length_comment = str_replace ('#MIN_WORD_LENGTH#', $min_keyword_length, $min_keyword_length_comment);
$tpl->assign('min_keyword_length_comment', $min_keyword_length_comment);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_users_dt.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>