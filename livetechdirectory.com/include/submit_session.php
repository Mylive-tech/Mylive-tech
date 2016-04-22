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
 if (!defined ('IN_PHPLD'))
{
   die("!! ERROR !! You are not allowed to run this script!");
}

/**
 * Create submit session name
 * @param  none
 * @return string Session name
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function generateSessionHash()
{
   global $client_info;

   return md5 ((isset ($client_info['IP']) ? $client_info['IP'] : $_SERVER['REMOTE_ADDR']) . $_SERVER['SERVER_NAME'] . $_SERVER['DOCUMENT_ROOT'] . (defined ('SECRET_SESSION_PASSWORD') ? SECRET_SESSION_PASSWORD : 'phpLD'));
}

/**
 * Create submit session, unique hash and DB entry
 * @param  none
 * @return string bool/integer "1" on success, "0" on error
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function generateSubmitSession()
{
   global $db, $tables;

   //Generate session variable name to store submit hash
   $sessionHash = generateSessionHash();

   if (!empty ($sessionHash))
   {
      //Generate unique submit session hash
      $secretSubmitHash = md5 (uniqid (mt_rand (), true));

      $_SESSION[$sessionHash] = $secretSubmitHash;

      //Check if session is created
      if (!isset ($_SESSION[$sessionHash]) || empty ($_SESSION[$sessionHash]))
      {
         //Session variable does not exist or is empty
         return 0;
      }

      //Make DB entry
      $dbEntry = $db->Execute("INSERT INTO `{$tables['submit_verification']['name']}` (`SESSION` , `SUBMITHASH`, `CREATED`) VALUES (".$db->qstr($sessionHash).", ".$db->qstr($secretSubmitHash).", ".(defined ('TIMENOW') && strlen ('TIMENOW') > 0 ? $db->qstr(TIMENOW) : $db->qstr(time())).")");

      //Check if the entry to the DB was made
      if ($dbEntry === false)
      {
         //Error when writing to DB
         return 0;
      }

      unset ($secretSubmitHash, $dbEntry);
   }
   else
   {
      //Could not generate session name
      return 0;
   }

   unset ($sessionHash);

   //Everyting OK
   return 1;
}

/**
 * Validate submit session
 * @param none
 * @return bool/integer  "0" (zero) on error or invalid phrase, "1" (one) on success
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function validate_submit_session()
{
   global $db, $tables;

   //Clean DB table of older entries (10 minutes = 600 seconds = 60 sec * 10)
   //This will increase validation and keep DB table small
   $db->Execute("DELETE FROM `{$tables['submit_verification']['name']}` WHERE (".(defined ('TIMENOW') && strlen ('TIMENOW') > 0 ? $db->qstr(TIMENOW) : $db->qstr(time()))." - `CREATED` > '60' * '10')");

   //Generate session variable
   $sessionHash = generateSessionHash();

   //Check if session variable exists
   if (!isset ($_SESSION[$sessionHash]) || empty ($_SESSION[$sessionHash]))
   {
      //Session variable does not exist, or is empty
      //Failed validation
      return 0;
   }

   //Validation will clean DB entry
   if ($db->Execute("DELETE FROM `{$tables['submit_verification']['name']}` WHERE `SESSION` = ".$db->qstr($sessionHash)." AND `SUBMITHASH` = ".$db->qstr($_SESSION[$sessionHash])))
   {
      return ($db->Affected_Rows() > 0 ? 1 : 0);
   }
   else
   {
      //Error in SQL query
      //Failed validation
      return 0;
   }

   return 0;
}

function registerAdminSubmitSession() {
	global $client_info;
	
	$script_name = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);
	
	$session_hash = (isset ($client_info['IP']) ? $client_info['IP'] : $_SERVER['REMOTE_ADDR']) . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . (defined ('SECRET_SESSION_PASSWORD') ? SECRET_SESSION_PASSWORD : 'phpLD').uniqid(mt_rand(), true);

	$session_hash = md5($session_hash);

	$_SESSION[$session_hash] = md5(uniqid(mt_rand(), true));	
	
	return $session_hash;
}

function checkAdminSubmitSession($session_hash, $goto_unauthorized = true) {
	$session_valid = validateAdminSubmitSession($session_hash);

	if ($goto_unauthorized == true && $session_valid !== 1) {
		die("Unauthorized request");
	}
	
	return $session_valid;
}

function validateAdminSubmitSession($session_hash) {
	// Check if request was made from the same script
	$referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
	$http_host = $_SERVER['HTTP_HOST'];
	
	// Exclude port from referer host
	if (strpos($http_host, ':')) {
		$http_host = substr($http_host, 0, strpos($http_host, ':'));
	}
	if (isset($referer_host) && $referer_host !== $http_host) {
		return 0;
	}
	
	if (isset($_SESSION[$session_hash]) && !empty($_SESSION[$session_hash])) {
		$_SESSION[$session_hash] = null;
		unset($_SESSION[$session_hash]);
		return 1;	
	}
	
	return 0;
}

?>