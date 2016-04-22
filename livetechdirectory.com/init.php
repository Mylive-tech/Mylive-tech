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

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
//Set time zone
@date_default_timezone_set('UTC');

//register_globals turned ON is a major security hole
//we'll unset ALL variables requested via URL
$register_globals = trim(ini_get('register_globals'));
if (!empty($register_globals) && strtolower($register_globals) != 'off') {
    //Get request variables
    $getRequest = array_keys($_REQUEST);
    //Loop through each variable
    foreach ($getRequest as $var) {
        //Test if variable was declared
        if ($_REQUEST[$var] === $var) {
            //Set value of the variable to NULL,
            //just in case unset does not work
            $var = null;

            //Unset variable
            unset($var);
        }
    }
    unset($getRequest);
}

/**
 * Define some application wide constants to increase security.
 * By checking them in included files, further execution can be blocked for unauthorized access.
 */
define('IN_PHPLD', true); //For all files
//Start page generation timer
define('PLD_TIMESTART', microtime());

//Detect web-server software
define('IS_APACHE', ( strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') || strstr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') ) ? 1 : 0);
define('IS_IIS', strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0);

//Get type of interface between web server and PHP
define('SAPI_NAME', php_sapi_name());

require_once 'include/version.php';
require_once 'include/config.php';
require_once 'include/client_info.php';
require_once 'include/settings.php';
require_once 'include/tables.php';
require_once 'include/functions.php';
require_once 'include/validation_functions.php';
require_once 'Validator.class.php';
require_once 'include/functions_validate.php';
require_once 'include/dirdb.php';
spl_autoload_register('phpldAutoload');
session_start();

if (get_magic_quotes_gpc()) {
    $_GET = custom_stripslashes($_GET);
    $_POST = custom_stripslashes($_POST);
    $_COOKIE = custom_stripslashes($_COOKIE);
    $_REQUEST = custom_stripslashes($_REQUEST);
}

//Do not allow prefetching for logged in users (only for guests)
//Google Web Accelerator can display sensitive data
if (isset($_SESSION['phpld']['user']) && $_SESSION['phpld']['user']['id'] > 0 && strpos($_SERVER['HTTP_X_MOZ'], 'prefetch') !== false) {
    if (SAPI_NAME == 'cgi' || SAPI_NAME == 'cgi-fcgi')
        @ header('Status: 403 Forbidden');
    else
        @ header('HTTP/1.1 403 Forbidden');

    //Terminate the current script
    exit('Prefetching is not allowed.');
}

define('SERVER_DOC_ROOT', dirname(__file__)); //example:/var/www/html/admin
define('DOC_ROOT', substr($_SERVER["SCRIPT_NAME"], 0, strrpos($_SERVER["SCRIPT_NAME"], '/')));

define('DIRECTORY_ROOT', substr($_SERVER["SCRIPT_NAME"], 0, strrpos($_SERVER["SCRIPT_NAME"], '/')));

if (!defined('DB_DRIVER')) {
    http_custom_redirect(DOC_ROOT . '/install/index.php');
}

require_once 'libs/Smarty3/Intsmarty.class.php';
require_once 'libs/smarty/SmartyPaginate.class.php';
$cachecheck = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if ($cachecheck) {
    $sql = "SELECT * FROM `PLD_CONFIG` WHERE `ID` = 'DB_CACHING' ";
    mysql_select_db(DB_NAME);
    $dbc = mysql_query($sql);
    $dbcc = mysql_fetch_array($dbc);
    define('DB_CACHING', $dbcc['VALUE']);
} else {
    define('ERROR', 'ERROR_DB_CONNECT');
    exit('ERROR :: Could not connect to database server!');
}
mysql_close($cachecheck);
if (DB_CACHING == '1') {
    require_once 'libs/adodb/adodb.inc.php';
} else {
    require_once 'libs/adodb/ncadodb.inc.php';
}

//Add custom database library extender
require_once 'include/adodb_extender.php';

//Connect to database
$db = ADONewConnection(DB_DRIVER);
Phpld_Db::factory($db, $tables);
if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)) {
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

    // $setCharset = $db->Execute ("SET NAMES 'utf8'");
    // $setCharset = $db->Execute ("SET CHARACTER SET utf8");
    //Load extenders to count executions
    $db->fnExecute = 'CountExecs';
    $db->fnCacheExecute = 'CountCachedExecs';

    $phpldSettings = read_config($db);
} else {
    define('ERROR', 'ERROR_DB_CONNECT');
    exit('ERROR :: Could not connect to database server!');
}

if (DEBUG === 1) {
    set_log('frontend_log.txt');
}

//Define current time
if (defined('SERVER_OFFSET_TIME')) {
    //Calculate with offset time
    define('TIMENOW', time() + (SERVER_OFFSET_TIME * 60 * 60));
} else {
    //Offset time was not defined, use current time
    define('TIMENOW', time());
}
$phpldSettings['TIMENOW'] = TIMENOW;


//Path to cache directory
//You might want to set this outside your document root
$db_cache_dir = SERVER_DOC_ROOT . '/temp/adodb/';
$db_cache_timeout = (defined('DB_CACHE_TIMEOUT') ? DB_CACHE_TIMEOUT : 3600);
//do NOT use DB caching if "register_globals" is ON
if (DB_CACHING == '1' && !ini_get('register_globals')) {
    //Clear all expired cache files
    if (is_dir($db_cache_dir) && is_writeable($db_cache_dir)) {
        //Define database cache directory
        $ADODB_CACHE_DIR = $db_cache_dir;

        //Define cache timeout
        $db->cacheSecs = (int) $db_cache_timeout;

        //Flush database cache
        phpLDAdoCache::phpld_ExpiredCacheFlush($ADODB_CACHE_DIR);

        $db->CacheFlush();
    }
}


//Define session ID
define('PLD_SESSION_ID', session_id());



//Send character set header
@ header('Content-type: text/html; charset=' . (defined('CHARSET') ? CHARSET : 'utf-8'));

if (isset($_SESSION['phpld']['user']['id'])) {
    $user_level = get_user_level($_SESSION['phpld']['user']['id']);
}

if (!isset($_SESSION['phpld']['user']['id']) || (isset($user_level) && ($user_level <= 0))) {
    //Load input filter
    require_once 'libs/inputfilter/class.inputfilter_php5.php';
    //Run input filter, request variables should be safe now
    require_once 'include/io_filter.php';
}

//Initialize template
if (USE_MOBILE_SITE == '1') {
    $tpl = get_tpl();
} else {
    $tpl = get_tplnm();
}


define('TEMPLATE_PATH', 'templates/' . TEMPLATE);
define('MOBILE_TEMPLATE_PATH', 'templates/' . MOBILE_TEMPLATE);
define('FULL_TEMPLATE_PATH', DOC_ROOT . '/templates/' . TEMPLATE);
$phpldSettings['TEMPLATE_PATH'] = TEMPLATE_PATH;
$phpldSettings['FULL_TEMPLATE_PATH'] = FULL_TEMPLATE_PATH;
define('USE_TEMPLATE', TEMPLATE);
////need next one so that when a user logges in, he's redirected to the previous page he was visiting
//if (!strpos($_SERVER['REQUEST_URI'], 'login.php') && !strpos($_SERVER['REQUEST_URI'], 'captcha.php') && !strpos($_SERVER['REQUEST_URI'], 'content.css') && !strpos($_SERVER['REQUEST_URI'], 'inplace')) {
//    $_SESSION['prev_page'] = $_SERVER['REQUEST_URI'];
//}


require_once 'include/constants.php';


// Disallow access to the page if it's not allowed for editors
if (isset($_SESSION['phpld']['user']) && $_SESSION['phpld']['user']['id']) {
    if (preg_match('!^admin/?([a-bA-B](\.php))?$!', $_SESSION['return']))
        $_SESSION['return'] = SITE_URL;
}

$URLcomponents = @ parse_url($_SERVER['REQUEST_URI']);
if (is_array($URLcomponents) && !empty($URLcomponents)) {
    @ parse_str($URLcomponents['query'], $URLvariables);
}


define('FRONT_DOC_ROOT', (substr(SITE_URL, -1) == '/' ? substr(SITE_URL, 0, strlen(SITE_URL)-1) : SITE_URL));
if (!empty($_REQUEST['formSubmitted'])) {
    $_REQUEST['submit'] = $_REQUEST['formSubmitted'];
}
if (!empty($_POST['formSubmitted'])) {
    $_POST['submit'] = $_POST['formSubmitted'];
}
if (!empty($_GET['formSubmitted'])) {
    $_GET['submit'] = $_GET['formSubmitted'];
}
?>