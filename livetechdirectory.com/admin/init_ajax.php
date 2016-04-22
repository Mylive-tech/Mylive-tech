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



/**
 * Define some application wide constants to increase security.
 * By checking them in included files, further execution can be blocked for unauthorized access.
 */
define ('IN_PHPLD'      , true); //For all files
define ('IN_PHPLD_ADMIN', true); //Only for admin files


//Detect web-server software
define ('IS_APACHE', ( strstr ($_SERVER['SERVER_SOFTWARE'], 'Apache') || strstr ($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') ) ? 1 : 0);
define ('IS_IIS'   , strstr ($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0);

require_once '../include/version.php';
require_once '../include/config.php';
require_once 'include/settings.php';
require_once 'include/tables.php';
require_once 'include/functions.php';
require_once 'libs/adodb/adodb.inc.php';

if (get_magic_quotes_gpc()){
 $_GET = custom_stripslashes($_GET);
 $_POST = custom_stripslashes($_POST);
 $_COOKIE = custom_stripslashes($_COOKIE);
 $_REQUEST = custom_stripslashes($_REQUEST);
}

tweak_time_limit(0);

tweak_memory_limit(100);

session_start();

//Define full path on server
define ('SERVER_DOC_ROOT', dirname (__file__));//example:/var/www/html/admin
define ('SERVER_DIRECTORY_ROOT', dirname (dirname (__file__)));//example:/var/www/html

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

if (DEBUG === 1)
{
   set_log('admin_log.txt');
}
//Define current time
if (defined ('SERVER_OFFSET_TIME'))
{
   //Calculate with offset time
   define ('TIMENOW', time() + (SERVER_OFFSET_TIME * 60 * 60));
}
else
{
   //Offset time was not defined, use current time
   define ('TIMENOW', time());
}
$phpldSettings['TIMENOW'] = TIMENOW;
//Add custom database library extender
require_once 'include/adodb_extender.php';
//Path to cache directory
//You might want to set this outside your document root
$db_cache_dir     = SERVER_DIRECTORY_ROOT . '/temp/adodb/';
$db_cache_timeout = (defined ('DB_CACHE_TIMEOUT') ? DB_CACHE_TIMEOUT : 3600);

if (DB_CACHING == '0')
{
	 $db->CacheFlush();
}

//do NOT use DB caching if "register_globals" is ON
if (DB_CACHING && !ini_get('register_globals'))
{
   if (is_dir ($db_cache_dir) && is_writeable ($db_cache_dir))
   {
      //Define database cache directory
      $ADODB_CACHE_DIR = $db_cache_dir;

      //Define cache timeout
      $db->cacheSecs = (int)$db_cache_timeout;
   }
}
//Define session ID
define ('PLD_SESSION_ID', session_id());
//Send character set header
@ header ('Content-type: text/html; charset='.(defined ('CHARSET') ? CHARSET : 'utf-8'));
//Load input filter
require_once 'libs/inputfilter/class.inputfilter_php4.php';

define ('DOC_ROOT', substr ($_SERVER["SCRIPT_NAME"], 0, strrpos ($_SERVER["SCRIPT_NAME"], '/')));


//Check if admin or editor is accessing the page
//Else redirect to login page
if (empty ($_SESSION['phpld']['adminpanel']['id']))
{
   $f = $_SERVER['SCRIPT_NAME'];
   if (($p = strrpos ($f, '/')) !== false)
      $f = substr ($f, $p + 1);

   if ($f != 'login.php')
   {
      if (empty ($_SESSION['return']))
         $_SESSION['return'] = request_uri();

      http_custom_redirect('login.php');
      exit();
   }
}

require_once 'include/constants.php';

$f = $_SERVER['SCRIPT_NAME'];
if (($ptmp = strrpos ($f, '/')) !== false)
   $f = substr ($f, $ptmp + 1);

$current_script = $f;

//Disallow access to the page if it's not allowed for editors
if (!$_SESSION['phpld']['adminpanel']['is_admin'])
{
   //List of pages editors are allowed to view
   $editorAllowed = array ('login.php',
                           'index.php',
                           'dir_categs.php',
                           'dir_categs_edit.php',
                           'dir_links.php',
   			   				'dir_pages.php',
   			   				'dir_pages_edit.php',
                           'dir_reviewed_links.php',
                           'dir_review_links_edit.php',
                           'dir_links_edit.php',
                           'dir_approve_links.php',
                           'conf_profile.php',
                           'link_details.php',
                           'categ_details.php',
                           'categ_link_options.php',
                           'article_list.php',
                           'article_edit.php',
                           'article_details.php',
                           'unauthorized.php'
                     );
   if (!in_array ($current_script, $editorAllowed))
   {
      //Editor is on unallowed page, block access
      http_custom_redirect("unauthorized.php");
      exit();
   }
}

$featured_where      = "(`FEATURED` = 1 AND (`EXPIRY_DATE` > ".$db->DBTimeStamp(time())." OR `EXPIRY_DATE` IS NULL))";
$featured_where_join = "({$tables['link']['name']}.FEATURED = '1' AND ({$tables['link']['name']}.EXPIRY_DATE > ".$db->DBTimeStamp(time())." OR {$tables['link']['name']}.EXPIRY_DATE IS NULL))";

//need next one for admin link listing, where both regular and featured are now listed together
$expired_where_join = "NOT({$tables['link']['name']}.EXPIRY_DATE > ".$db->DBTimeStamp(time())." OR {$tables['link']['name']}.EXPIRY_DATE IS NULL)";

?>