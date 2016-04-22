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
@ header ('Expires: Mon, 14 Oct 2002 05:00:00 GMT');              // Date in the past
@ header ('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT'); // always modified
@ header ('Cache-Control: no-store, no-cache, must-revalidate');  // HTTP 1.1
@ header ('Cache-Control: post-check=0, pre-check=0', false);
@ header ('Pragma: no-cache');                                    // HTTP 1.0

session_start ();

if (isset ($_SESSION['phpld']['adminpanel']))
{
   //Clear session variable,just in case unset does not work
   $_SESSION['phpld']['adminpanel'] = array();
   //Completely destroy user session
   unset ($_SESSION['phpld']['adminpanel']);

   if (empty ($_SESSION['phpld']))
   {
      unset ($_SESSION['phpld']);
   }
}

if (isset ($_COOKIE[session_name ()]))
{
   setcookie (session_name(), '', 0);
}

@ session_write_close ();
//@ session_unset ();
@ session_destroy ();//Fix IE Bug

@ header ('Location: login.php');
exit();
?>