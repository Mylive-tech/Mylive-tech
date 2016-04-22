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
//register_globals turned ON is a major security hole
//we'll unset ALL variables requested via URL
$register_globals = trim(ini_get('register_globals'));
 if (!empty($register_globals) && strtolower($register_globals) != 'off') {
   //Get request variables
   $getRequest = array_keys ($_REQUEST);
   //Loop through each variable
   foreach ($getRequest as $var)
   {
      //Test if variable was declared
      if ($_REQUEST[$var] === $var)
      {
         //Set value of the variable to NULL,
         //just in case unset does not work
         $var = null;

         //Unset variable
         unset($var);
      }
   }
   unset ($getRequest);
}

//if (!get_magic_quotes_gpc()){ 
// $_GET = array_map('addslashes', $_GET); 
// $_POST = array_map('addslashes', $_POST); 
// $_COOKIE = array_map('addslashes', $_COOKIE); 
// $_REQUEST = array_map('addslashes', $_REQUEST); 
//}




require_once '../include/config.php';
require_once 'include/settings.php';
require_once 'include/tables.php';
require_once 'include/functions.php';
require_once 'libs/adodb/adodb.inc.php';

//Run input filter, request variables should be safe now
require_once 'include/io_filter.php';

if (get_magic_quotes_gpc()){ 
 $_GET = custom_stripslashes($_GET); 
 $_POST = custom_stripslashes($_POST); 
 $_COOKIE = custom_stripslashes($_COOKIE); 
 $_REQUEST = custom_stripslashes($_REQUEST); 
}


session_start();

//Connect to database
$db = ADONewConnection(DB_DRIVER);
if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
{
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $phpldSettings = read_config($db);
}
else
{
   define ('ERROR', 'ERROR_DB_CONNECT');
   exit ('ERROR :: Could not connect to database server!');
}

$action = (!empty ($_REQUEST['action']) ? clean_string_paranoia($_REQUEST['action']) : 'default');

//$action = $_REQUEST['action'];

$result = '0';

switch ($action) {
	case 'check_login':
		$login = strip_white_space($_REQUEST['user']);
		$pass = strip_white_space($_REQUEST['password']);
		
		if (!empty($login) && !empty($pass)) {
			$sql = "SELECT `ID`, `NAME`, `LEVEL`, `LANGUAGE` FROM `{$tables['user']['name']}` WHERE `LOGIN` = ".$db->qstr($login)." AND `PASSWORD` = ".$db->qstr(encrypt_password($pass))." AND `LEVEL` IN ('1', '3') AND `ACTIVE` = '1'";
			$row = $db->GetRow($sql);
			if (!empty ($row['ID'])) 
				$result = '1';
		}
		
		break;
}

echo $result;

?>