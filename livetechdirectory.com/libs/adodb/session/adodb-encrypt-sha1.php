<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
if (!defined('ADODB_SESSION')) die();

include_once ADODB_SESSION . '/crypt.inc.php';


/**

 */

class ADODB_Encrypt_SHA1 {

	function write($data, $key) 
	{
		$sha1crypt = new SHA1Crypt();
		return $sha1crypt->encrypt($data, $key);

	}


	function read($data, $key) 
	{
		$sha1crypt = new SHA1Crypt();
		return $sha1crypt->decrypt($data, $key);

	}
}



return 1;
?>