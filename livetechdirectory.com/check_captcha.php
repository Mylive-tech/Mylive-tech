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
 
require_once 'include/version.php';
require_once 'include/config.php';
require_once 'include/client_info.php';
require_once 'include/settings.php';
require_once 'include/tables.php';
require_once 'include/functions.php';
require_once 'include/functions_validate.php';
require_once 'include/dirdb.php';
//require_once 'include/functions_imgverif.php';
session_start();
require_once 'libs/adodb/adodb.inc.php';

//Add custom database library extender
require_once 'include/adodb_extender.php';
//Connect to database
$db = ADONewConnection(DB_DRIVER);
if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
{
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

  // $setCharset = $db->Execute ("SET NAMES 'utf8'");
  // $setCharset = $db->Execute ("SET CHARACTER SET utf8");
   
   //Load extenders to count executions
   $db->fnExecute = 'CountExecs';
   $db->fnCacheExecute = 'CountCachedExecs';

   $phpldSettings = read_config($db);
}
else
{
   define('ERROR', 'ERROR_DB_CONNECT');
   exit('ERROR :: Could not connect to database server!');
}

if (!empty($_SESSION['imagehash']) && !empty($_REQUEST['CAPTCHA'])) {
	$result = $db->GetOne("SELECT CREATED FROM `{$tables['img_verification']['name']}` WHERE `IMGHASH` = '{$_SESSION['imagehash']}' AND `IMGPHRASE` = '{$_REQUEST['CAPTCHA']}'");
	$result = $result ? 'true' : 'false';
} else {
	$result = 'false';
}


echo $result;

?>