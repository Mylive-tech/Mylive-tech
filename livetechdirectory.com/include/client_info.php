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
 * Retrieve client information and save it into an array
 */
$client_info = array ();

/**
 * Regular expression for a valid IP address
 */
$IP_EXPRESSION = "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/";


/**
 * Get IP address of current visitor
 */
$client_info['IP'] = (!empty ($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty ($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : @ getenv ('REMOTE_ADDR'));


/**
 * Get alternate IP address of current visitor,
 * like IP address behind a proxy
 */
if (isset ($_SERVER['HTTP_CLIENT_IP']))
{
   $client_info['IP_BEHIND_PROXY'] = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (isset ($_SERVER['HTTP_X_FORWARDED_FOR']))
{
   /**
    * This gathered IP address is probably behind a proxy
    * Do not rely on this and it doesn't make sense to block the whole proxy
    */
   preg_match_all ($IP_EXPRESSION.'s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches);


   /**
    * IP's defined by RFC 1918 are not saved
    * http://rfc.net/rfc1918.html
    */
   foreach ($matches[0] as $IP)
   {
      if (!preg_match ("/^(10|172\.16|192\.168)\./", $IP))
      {
         $client_info['IP_BEHIND_PROXY'] = $IP;
         break;
      }
   }
}
else
{
   $client_info['IP_BEHIND_PROXY'] = '';
}


/**
 * Get the internet host name corresponding to a given IP address
 */
$client_info['HOSTNAME'] = @ gethostbyaddr ($client_info['IP']);


/**
 * Contents of the User-Agent: header from the current request, if there is one
 */
$client_info['USER_AGENT'] = (!empty ($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');


/**
 * The address of the page (if any) which referred the user agent to the current page
 */
/*$client_info['query_from'] = $_SERVER['HTTP_REFERER'];*/


/**
 *The port being used on the user's machine to communicate with the web server
 */
$client_info['PORT'] = (!empty ($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '');


/**
 * The URI which was given in order to access this page
 */
if ($_SERVER['REQUEST_URI'] || $_ENV['REQUEST_URI'])
{
   $client_info['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_ENV['REQUEST_URI'];
}
else
{
   if ($_SERVER['PATH_INFO'] || $_ENV['PATH_INFO'])
      $client_info['REQUEST_URI'] = ($_SERVER['PATH_INFO'] ? $_SERVER['PATH_INFO'] : $_ENV['PATH_INFO']);
   elseif ($_SERVER['REDIRECT_URL'] || $_ENV['REDIRECT_URL'])
      $client_info['REQUEST_URI'] = ($_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL'] : $_ENV['REDIRECT_URL']);
   else
      $client_info['REQUEST_URI'] = ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_ENV['PHP_SELF']);

   if ($_SERVER['QUERY_STRING'] || $_ENV['QUERY_STRING'])
      $client_info['REQUEST_URI'] .= '?'.($_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_ENV['QUERY_STRING']);
}

/**
 * The query string, if any, via which the page was accessed
 */
$client_info['QUERY_STRING'] = str_replace ('&amp;amp;', '&amp;', urldecode ($_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : @ getenv ('QUERY_STRING')));


/**
 * Clean and repair client information
 */
$client_info['IP']              = preg_replace ("{$IP_EXPRESSION}", "\\1.\\2.\\3.\\4", $client_info['IP']);
$client_info['IP_BEHIND_PROXY'] = preg_replace ("{$IP_EXPRESSION}", "\\1.\\2.\\3.\\4", $client_info['IP_BEHIND_PROXY']);
$client_info['REQUEST_URI']     = str_replace ('&amp;', '&', $client_info['REQUEST_URI']);
$client_info['QUERY_STRING']    = str_replace ('&amp;', '&', $client_info['QUERY_STRING']);

?>