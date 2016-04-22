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

if ($_REQUEST['action']) {
	list($type, $param, $val, $val2) = split(':', $_REQUEST['action']);
	$action = $_REQUEST['action'];
	$field_name = $_REQUEST['field_name'];
	$categ_id	= $_REQUEST['category_id'];
} else {
	http_custom_redirect('index.php');
	exit;
}


switch ($action) {
	case 'isFieldExists':
		$result = isFieldExists($field_name);
		break;
	
	case 'isUniqueValue':
		$result = isUniqueValue('link', $field_name);
		break;
		
	case 'isUniqueUrl':
		$result = isUniqueUrl($field_name, $categ_id);
		break;
		
	case 'isURLOnline':
		$result = isURLOnline($field_name);
		break;
		
	case 'isRecprOnline':
		$result = isRecprOnline($field_name);
		break;
		
	case 'isBannedEmail':
		$result = isBannedEmail($field_name);
		break;
		
	case 'isDomainBanned':
		$result = isDomainBanned($field_name);
		break;
		
	case 'isCaptchaValid':
		$result = isCaptchaValid();
		break;

}


echo $result;

?>