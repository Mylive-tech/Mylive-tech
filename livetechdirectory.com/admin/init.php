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
//if (ini_get ('register_globals'))
//{
//   //Get request variables
//   $getRequest = array_keys ($_REQUEST);
//   //Loop through each variable
//   foreach ($getRequest as $var)
//   {
//      //Test if variable was declared
//      if ($_REQUEST[$var] === $$var)
//      {
//         //Unset variable
//         unset($$var);
//      }
//   }
//   unset ($getRequest);
//}
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

//if (!get_magic_quotes_gpc()){ 
// $_GET = array_map('addslashes', $_GET); 
// $_POST = array_map('addslashes', $_POST); 
// $_COOKIE = array_map('addslashes', $_COOKIE); 
// $_REQUEST = array_map('addslashes', $_REQUEST); 
//}





/**
 * Define some application wide constants to increase security.
 * By checking them in included files, further execution can be blocked for unauthorized access.
 */
define('IN_PHPLD', true); //For all files
define('IN_PHPLD_ADMIN', true); //Only for admin files
//Detect web-server software
define('IS_APACHE', ( strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') || strstr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') ) ? 1 : 0);
define('IS_IIS', strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0);

require_once '../include/version.php';
require_once '../include/config.php';

if (DEBUG) {
    require_once 'include/debug.php';
}

require_once '../include/client_info.php';
require_once 'include/settings.php';
require_once 'include/tables.php';
require_once 'include/functions.php';
require_once 'include/validation_functions.php';
require_once 'include/submit_session.php';
require_once 'Validator.class.php';
require_once 'include/functions_validate.php';
require_once 'include/dirdb.php';
require_once 'libs/intsmarty/intsmarty.class.php';
require_once 'libs/smarty/SmartyPaginate.class.php';
spl_autoload_register('phpldAutoload');
if (DB_CACHING == '1') {
    require_once 'libs/adodb/adodb.inc.php';
} else {
    require_once 'libs/adodb/ncadodb.inc.php';
}
require_once 'admin/dirdb_admin.php';


if (get_magic_quotes_gpc()) {
    $_GET = custom_stripslashes($_GET);
    $_POST = custom_stripslashes($_POST);
    $_COOKIE = custom_stripslashes($_COOKIE);
    $_REQUEST = custom_stripslashes($_REQUEST);
}

tweak_time_limit(0);

session_start();

//Define full path on server
define('SERVER_DOC_ROOT', dirname(__file__)); //example:/var/www/html/admin
define('SERVER_DIRECTORY_ROOT', dirname(dirname(__file__))); //example:/var/www/html
//Connect to database
$db = ADONewConnection(DB_DRIVER);
Phpld_Db::factory($db, $tables);
if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)) {
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

    //  $setCharset = $db->Execute ("SET NAMES 'utf8'");
    // $setCharset = $db->Execute ("SET CHARACTER SET utf8");

    $phpldSettings = read_config($db);
} else {
    define('ERROR', 'ERROR_DB_CONNECT');
    exit('ERROR :: Could not connect to database server!');
}

if (DEBUG === 1) {
    set_log('admin_log.txt');
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
//Add custom database library extender
require_once 'include/adodb_extender.php';
//Path to cache directory
//You might want to set this outside your document root
$db_cache_dir = SERVER_DIRECTORY_ROOT . '/temp/adodb/';
$db_cache_timeout = (defined('DB_CACHE_TIMEOUT') ? DB_CACHE_TIMEOUT : 3600);

if (DB_CACHING == '0') {
    $db->CacheFlush();
}

//do NOT use DB caching if "register_globals" is ON
if (DB_CACHING && !ini_get('register_globals')) {
    if (is_dir($db_cache_dir) && is_writeable($db_cache_dir)) {
        //Define database cache directory
        $ADODB_CACHE_DIR = $db_cache_dir;

        //Define cache timeout
        $db->cacheSecs = (int) $db_cache_timeout;
    }
}
//Define session ID
define('PLD_SESSION_ID', session_id());
//Send character set header
@ header('Content-type: text/html; charset=' . (defined('CHARSET') ? CHARSET : 'utf-8'));
//Load input filter
require_once 'libs/inputfilter/class.inputfilter_php4.php';

define('DOC_ROOT', substr($_SERVER["SCRIPT_NAME"], 0, strrpos($_SERVER["SCRIPT_NAME"], '/')));

define('FRONT_DOC_ROOT', substr($_SERVER["SCRIPT_NAME"], 0, strrpos($_SERVER["SCRIPT_NAME"], 'admin')));

//Determine core templates folder
$core_tpl = (defined('CORE_TEMPLATES') && CORE_TEMPLATES != '' ? CORE_TEMPLATES : 'Core');
$tpl = get_tpl($core_tpl);
$tpl->assign('date_format', '%D %H:%M:%S');


//widget related//
//$wd = new Phpld_Widget();

$doc_root = DOC_ROOT;
$tpl->assign('doc_root', $doc_root);

$tpl->assign('requestUri', $_SERVER['REQUEST_URI']);

$directory_root = preg_replace('`[\/]?admin[\/]?$`', '', DOC_ROOT);
define('DIRECTORY_ROOT', $directory_root);

define('TEMPLATE_ROOT', DIRECTORY_ROOT . "/templates/Core/" . ADMIN_TEMPLATE);

//Check if admin or editor is accessing the page
//Else redirect to login page
$p = null;
if (empty($_SESSION['phpld']['adminpanel']['id'])) {
    $f = $_SERVER['SCRIPT_NAME'];
    if (($p = strrpos($f, '/')) !== false)
        $f = substr($f, $p + 1);

    if ($f != 'login.php') {
        if (empty($_SESSION['return']))
            $_SESSION['return'] = request_uri();

        http_custom_redirect('login.php');
    }
}

//Determine category
$CategoryID = (!empty($_REQUEST['c']) && preg_match('`^[\d]+$`', $_REQUEST['c']) ? intval($_REQUEST['c']) :
                (!empty($_SERVER['HTTP_REFERER']) ? get_category($_SERVER['HTTP_REFERER']) : 0));
$CategoryID = ($CategoryID > 0 ? $CategoryID : 0); //Make sure the category ID is valid
$CategoryTitle = getCategoryTitleByID($CategoryID);
$tpl->assign('CategoryTitle', $CategoryTitle);

//Determine if admin and ajax categories (editors have regular category list)
if ($_SESSION['phpld']['adminpanel']['is_admin'] == 1 && ADMIN_CAT_SELECTION_METHOD == 1)
    define('AJAX_CAT_SELECTION_METHOD', 1);
else
    define('AJAX_CAT_SELECTION_METHOD', 0);

require_once 'include/constants.php';

//Valid status values for links, categories and articles
$validLinkStatus = array(0, 1, 2);
$validCategStatus = array(0, 1, 2);
$validArticleStatus = array(0, 1, 2);

$featured_where = "(`FEATURED` = 1 AND (`EXPIRY_DATE` > " . $db->DBTimeStamp(time()) . " OR `EXPIRY_DATE` IS NULL))";
$featured_where_join = "({$tables['link']['name']}.FEATURED = '1' AND ({$tables['link']['name']}.EXPIRY_DATE > " . $db->DBTimeStamp(time()) . " OR {$tables['link']['name']}.EXPIRY_DATE IS NULL))";

//need next one for admin link listing, where both regular and featured are now listed together
$expired_where_join = "NOT({$tables['link']['name']}.EXPIRY_DATE > " . $db->DBTimeStamp(time()) . " OR {$tables['link']['name']}.EXPIRY_DATE IS NULL)";


//Check if featured, reviewed links, reviewed articles and/or payments are available for menu link
$featLinksItem = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `FEATURED` = '1'");
$reviewedLinksItem = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link_review']['name']}`");
$expiredLinksItem = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `EXPIRY_DATE` <= " . $db->DBTimeStamp(time()) . " AND `EXPIRY_DATE` IS NOT NULL");
$inactiveLinksItem = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '0'");
$paymentItem = $db->GetOne("SELECT COUNT(*) FROM `{$tables['payment']['name']}`");

$layout = new Phpld_Layout();
$layout->readLayout();

if ($_SESSION['phpld']['adminpanel']['is_admin']) {
    $menu = array(
        'index' => array("label" => _L('Home'), "class" => 'Home'),
        'cat' => array(
            'class' => 'Categories',
            'label' => _L('Categories'),
            'menu' => array(
                'categs' => array('label' => _L('Categories'), 'url' => DOC_ROOT . '/dir_categs.php'),
            )
        ),
        'lnks' => array(
            'label' => _L('Links'),
            'class' => 'Links',
            'menu' => array(
                'links' => array('label' => _L('Active Links'), 'url' => DOC_ROOT . '/dir_links.php?status=2'),
                'exp_links' => array('label' => _L('Expired Links'), 'url' => DOC_ROOT . '/dir_expired_links.php', 'disabled' => ($expiredLinksItem > 0 ? 0 : 1)),
                'inact_links' => array('label' => _L('Inactive Links'), 'url' => DOC_ROOT . '/dir_links.php?status=0', 'disabled' => ($inactiveLinksItem > 0 ? 0 : 1)),
                // 'ftr_links'       => array ('label' => _L('Featured Links')    , 'url' => DOC_ROOT.'/dir_links.php?f=1', 'disabled' => (FTR_ENABLE == 1 || $featLinksItem > 0 ? 0 : 1)),
                'approve_links' => array('label' => _L('Approve Links'), 'url' => DOC_ROOT . '/dir_links.php?status=1'),
                'reviewed_links' => array('label' => _L('Reviewed Links'), 'url' => DOC_ROOT . '/dir_reviewed_links.php', 'disabled' => ($reviewedLinksItem > 0 ? 0 : 1)),
                'payment' => array('label' => _L('Link Payments'), 'url' => DOC_ROOT . '/conf_payment.php', 'disabled' => (PAY_ENABLE == 1 || $paymentItem > 0 ? 0 : 1)),
                'validate' => array('label' => _L('Validate Links'), 'url' => DOC_ROOT . '/dir_validate.php'),
                'link_comments' => array('label' => _L('Link Comments'), 'url' => DOC_ROOT . '/dir_link_comments.php'),
                'approve_link_comments' => array('label' => _L('Approve Link Comments'), 'url' => DOC_ROOT . '/dir_approve_link_comments.php'),
                'import_listings' => array('label' => _L('Import Links'), 'url' => DOC_ROOT . '/dir_link_import.php'),
            )
        ),
//        'tags' => array(
//            'label' => _L('Tags'),
//            'class' => 'Links',
//            'menu' => array(
//                'active_tags' => array('label' => _L('Active Tags'), 'url' => DOC_ROOT . '/dir_tags.php?status=2'),
//                'pending_tags' => array('label' => _L('Pending Tags'), 'url' => DOC_ROOT . '/dir_tags.php?status=1'),
//                'banned_tags' => array('label' => _L('Banned Tags'), 'url' => DOC_ROOT . '/dir_tags.php?status=0'),
//            )
//        ),
        'widgets' => array(
            'label' => _L('Widgets'),
            'class' => 'Widgets',
            'menu' => array(
                'list' => array('label' => _L('Available Widgets'), 'url' => DOC_ROOT . '/dir_widgets.php'),
                'list_zones' => array('label' => _L('Zones'), 'url' => DOC_ROOT . '/dir_widget_zones.php'),
                'listad' => array('label' => _L('Inline Widgets List'), 'url' => DOC_ROOT . '/dir_inline_widgets.php'),
                'newad' => array('label' => _L('New Inline Widget'), 'url' => DOC_ROOT . '/dir_inline_widget_edit.php?action=N'),
                'list_howto' => array('label' => _L('How To Use Widgets'), 'url' => DOC_ROOT . '/dir_widget_howto.php')
            )
        ),
        'pages' => array(
            'label' => _L('Pages'),
            'class' => 'Pages',
            'menu' => array(
                array('label' => _L('New Page'), 'url' => DOC_ROOT . '/dir_pages_edit.php?action=N'),
                array('label' => _L('List Pages'), 'url' => DOC_ROOT . '/dir_pages.php'),
            ),
        ),
        'link_types' => array(
            'label' => _L('Link Types'),
            'class' => 'Link Types',
            'menu' => array(
                'list' => array('label' => _L('Available Link Types'), 'url' => DOC_ROOT . '/dir_link_types.php'),
                'new_type' => array('label' => _L('New Link Type'), 'url' => DOC_ROOT . '/dir_link_types_edit.php?action=N'),
                'list_submit_items' => array('label' => _L('Available Submit Items'), 'url' => DOC_ROOT . '/dir_submit_items.php'),
                'new_submit_item' => array('label' => _L('New Submit Item'), 'url' => DOC_ROOT . '/dir_submit_items_edit.php?action=N'),
            )
        ),
        'users' => array(
            'label' => _L('User Management'),
            'class' => 'User Management',
            'menu' => array(
                'users' => array('label' => _L('Edit Users'), 'url' => DOC_ROOT . '/conf_users.php'),
                 array('label' => _L('Admin Users'), 'url' => DOC_ROOT . '/conf_users.php?level=1')
            )
        ),
        'conf' => array(
            'label' => _L('System'),
            'class' => 'System',
            'menu' => array(                                
                // 'admintemplates' => array ('label' => _L('Admin Template Manager') , 'url' => DOC_ROOT.'/conf_admin_templates.php')   ,                
                // 'language_edit'  => array ('label' => _L('Edit Language File')   , 'url' => DOC_ROOT.'/dir_language.php')    ,                                        
                'bancontrol' => array('label' => _L('Ban Control'), 'url' => DOC_ROOT . '/conf_bancontrol.php'),
                'rebuildcounts'    =>  array ('label' => _L('Rebuild All Counts')        , 'url' => DOC_ROOT.'/calculate_counts.php'),
				'rebuildlinkurls'    =>  array ('label' => _L('Rebuild link Url Cache')        , 'url' => DOC_ROOT.'/update_link_urls.php'),
                'maintenance' => array('label' => _L('Maintenance'), 'url' => DOC_ROOT . '/conf_maintenance.php'), 	
                'language' => array('label' => _L('Language Editor'), 'url' => DOC_ROOT . '/conf_language.php'),
                'sitemap' => array('label' => _L('Sitemap'), 'url' => DOC_ROOT . '/conf_sitemap.php'),
                'database' => array('label' => _L('Database'), 'url' => DOC_ROOT . '/conf_database.php'),
                'task_manager' => array('label' => _L('Task Manager'), 'url' => DOC_ROOT . '/task_manager.php'),	

                'cache' => array('label' => _L('Clean Cache Now'), 'url' => DOC_ROOT . '/cache_clean.php', 'disabled' => (DB_CACHING == 1 ? 0 : 1)),
                'spider' => array('label' => _L('Spider'), 'url' => DOC_ROOT . '/spider.php'),
				'dbupdate' => array('label' => _L('Database Update'), 'url' => DOC_ROOT . '/db_update.php'),
                //'mobile_templates' => array('label' => _L('Mobile Template Manager'), 'url' => DOC_ROOT . '/conf_mobile_templates.php'),
            )
        ),

        'theme' => array(
            'label' => _L('Theme'),
            'class' => 'Theme',
            'menu' =>
            array_merge(array(
                'edit_current_template' => array('label' => _L('Edit Current Template'), 'url' => DOC_ROOT . '/conf_templates_edit.php'),
                'edit_mobile_template' => array('label' => _L('Edit Mobile Template'), 'url' => DOC_ROOT . '/conf_mobile_templates_edit.php', 'disabled' => (USE_MOBILE_SITE == 1 ? 0 : 1)),
                'templates' => array('label' => _L('Template Manager'), 'url' => DOC_ROOT . '/conf_templates.php'),
                'mobile_templates' => array('label' => _L('Mobile Template Manager'), 'url' => DOC_ROOT . '/conf_mobile_templates.php', 'disabled' => (USE_MOBILE_SITE == 1 ? 0 : 1)), 
            ), $layout->getAdminMenuItems())
        ),

        'settings' => array(
            'label' => _L('Settings'),
            'class' => 'Settings',
            'menu' => array(
                array('label' => _L('Site'), 'url' => DOC_ROOT . '/conf_settings.php?c=1'),
                array('label' => _L('Directory'), 'url' => DOC_ROOT . '/conf_settings.php?c=2'),
                array('label' => _L('Menu'), 'url' => DOC_ROOT . '/conf_menu.php'),
                array('label' => _L('Admin Area'), 'url' => DOC_ROOT . '/conf_settings.php?c=7'),
                array('label' => _L('Payment Settings'), 'url' => DOC_ROOT . '/conf_settings.php?c=9'),
                array('label' => _L('Link Submit'), 'url' => DOC_ROOT . '/conf_settings.php?c=3'),
                array('label' => _L('Featured Links'), 'url' => DOC_ROOT . '/conf_settings.php?c=8'),
                array('label' => _L('Reciprocal Link Setup'), 'url' => DOC_ROOT . '/conf_settings.php?c=12'),
                array('label' => _L('Link Comments'), 'url' => DOC_ROOT . '/conf_settings.php?c=20'),
                array('label' => _L('Link Ratings'), 'url' => DOC_ROOT . '/conf_settings.php?c=21'),
                array('label' => _L('Author Info'), 'url' => DOC_ROOT . '/conf_settings.php?c=18'),
                array('label' => _L('Email Config'), 'url' => DOC_ROOT . '/conf_settings.php?c=5'),
                array('label' => _L('Email Template Setup'), 'url' => DOC_ROOT . '/conf_settings.php?c=6'),
                array('label' => _L('Search Engine Optimization'), 'url' => DOC_ROOT . '/conf_settings.php?c=4'),
                array('label' => _L('Image Verification'), 'url' => DOC_ROOT . '/conf_settings.php?c=10'),
                array('label' => _L('Form Fields'), 'url' => DOC_ROOT . '/conf_settings.php?c=11'),
                array('label' => _L('Security'), 'url' => DOC_ROOT . '/conf_settings.php?c=13'),
                array('label' => _L('Cache Settings'), 'url' => DOC_ROOT . '/conf_settings.php?c=15'),
            //array('label' => _L('Paypal Integration')          , 'url' => DOC_ROOT.'/conf_settings.php?c=15'),
            ),
        ),
        'email' => array(
            'label' => _L('Emailer'),
            'class' => 'Emailer',
            'menu'  => array (
			 'message'        => array ('label' => _L('Edit Email Templates')   , 'url' => DOC_ROOT.'/email_message.php')    ,
              // 'send'               => array ('label' => _L('Send Email')             , 'url' => DOC_ROOT.'/email_send.php'),
               'send_and_add_link'  => array ('label' => _L('Send Email and Add Link'), 'url' => DOC_ROOT.'/email_send_and_add_link.php'),
               'send_newsletter'    => array ('label' => _L('Send Newsletter')        , 'url' => DOC_ROOT.'/newsletter_send.php'),
             //  'sent_view'          => array ('label' => _L('Sent Emails Report')     , 'url' => DOC_ROOT.'/email_sent_view.php'),
               //'import'             => array ('label' => _L('Import Email Messages')  , 'url' => DOC_ROOT.'/email_import.php'),
             //  'export'             => array ('label' => _L('Export Email Messages')  , 'url' => DOC_ROOT.'/email_export.php'),
               )
            ),
		 'help' => array (
               'label'   => _L('Help'),
               'class'   => 'Help',
               'menu'    => array (
               'peer'      => array ('label' => _L('Peer to Peer')   , 'url' => 'http://www.phplinkdirectory.com/forum/forumdisplay.php?f=6' , 'target' => '_blank'),
			   'ticket'      => array ('label' => _L('Support Tickets') , 'url' => 'http://www.phplinkdirectory.com/jobs/Support_Tickets/', 'target' => '_blank') ,
                  )
               ),
      'media' => array(
            'label' => _L('Media Manager'),
            'class' => 'Media',
            'menu' => array(
                'media_list' => array('label' => _L('Media List'), 'url' => DOC_ROOT . '/dir_media.php'),
                'newmedia' => array('label' => _L('New Media'), 'url' => DOC_ROOT . '/dir_media_edit.php?action=N'),
            )
      ),
      'logout' => array('label' => _L('Logout'), 'class' => 'Logout')
   );
}
else
{
      $menu = array (
      'index' => array('label' => _L('Home'), 'class' => 'Home'));
	  if ($_SESSION['phpld']['adminpanel']['rights']['addLink'] == 1) { 
	    $menu ['cat'] = array (
            'label' => _L('Categories'),
            'class' => 'Cateogries',
            'menu' => array(
                'categs' => array('label' => _L('Categories'), 'url' => DOC_ROOT . '/dir_categs.php'),
            )
        );
        $menu['lnks'] = array(
            'label' => _L('Links'),
            'class' => 'Links',
            'menu' => array(
                'links' => array('label' => _L('Links'), 'url' => DOC_ROOT . '/dir_links.php'),
                // 'ftr_links'       => array ('label' => _L('Featured Links')    , 'url' => DOC_ROOT.'/dir_links.php?f=1', 'disabled' => (FTR_ENABLE == 1 || $featLinksItem > 0 ? 0 : 1)),
                'approve_links' => array('label' => _L('Approve Links'), 'url' => DOC_ROOT . '/dir_approve_links.php'),
                'reviewed_links' => array('label' => _L('Reviewed Links'), 'url' => DOC_ROOT . '/dir_reviewed_links.php', 'disabled' => ($reviewedLinksItem > 0 ? 0 : 1)),
                'validate' => array('label' => _L('Validate Links'), 'url' => DOC_ROOT . '/dir_validate.php'),
            )
        );
    } else {
        $menu ['cat'] = array(
            'label' => _L('Categories'),
            'class' => 'Categories',
            'menu' => array(
                'categs' => array('label' => _L('Categories'), 'url' => DOC_ROOT . '/dir_categs.php'),
            )
        );
        $menu['lnks'] = array(
            'label' => _L('Links'),
            'class' => 'Links',
            'menu' => array(
                'links' => array('label' => _L('Links'), 'url' => DOC_ROOT . '/dir_links.php'),
            )
        );
    }
    if ($_SESSION['phpld']['adminpanel']['rights']['addPage'] == 1) {
        $menu['pages'] = array(
            'label' => _L('Pages'),
            'class' => 'Pages',
            'menu' => array(
                array('label' => _L('List Pages'), 'url' => DOC_ROOT . '/dir_pages.php'),
                array('label' => _L('New Page'), 'url' => DOC_ROOT . '/dir_pages_edit.php?action=N'),
            ),
        );
    } else {
        $menu['pages'] = array(
            'label' => _L('Pages'),
            'class' => 'Pages',
            'menu' => array(
                array('label' => _L('List Pages'), 'url' => DOC_ROOT . '/dir_pages.php'),
            ),
        );
    }
    $menu['conf'] = array(
        'label' => _L('System'),
        'class' => 'System',
        'menu' => array(
            'profile' => array('label' => _L('Profile'), 'url' => DOC_ROOT . '/conf_profile.php'),
        )
    );
    $menu['logout'] = array('label' => _L('Logout'), 'class' => 'Logout');
}

$conf_categs = array(
    '1' => _L('Site'),
    '2' => _L('Categories'),
    '3' => _L('Link Submit'),
    '4' => _L('Search Engine Optimization'),
    '5' => _L('Emailer'),
    '6' => _L('Notifications'),
    '7' => _L('Admin Area'),
    '8' => _L('Featured Links'),
    '9' => _L('Payment'),
    '10' => _L('Image Verification'),
    '11' => _L('Form Fields') . ' (<em>' . _L('Use with caution') . '</em>)',
    '12' => _L('Reciprocal Link Setup'),
    '13' => _L('Security'),
    '15' => _L('Caching'),
    '18' => _L('Author Info'),
    '20' => _L('Link Comments'),
    '21' => _L('Link Ratings'),
        //'15' => _L('Paypal Integration')        ,
);
$tpl->assign('menu', $menu);
$menu_elements = count($menu);
$tpl->assign('menu_elements', $menu_elements);

$user_details = $db->GetRow("SELECT `ID`, `LOGIN`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ID` = " . $db->qstr($_SESSION['phpld']['adminpanel']['id']) . " AND `LEVEL` IN ('1', '2') AND `ACTIVE` = '1'");
$tpl->assign('user_details', $user_details);

//Define current version again (backwards compatibility)
$tpl->assign('VERSION', CURRENT_VERSION);

//Array usually for radio buttons with answers yes/no
$tpl->assign('yes_no', array(1 => _L('Yes'), 0 => _L('No')));
$tpl->assign('no_yes', array(0 => _L('No'), 1 => _L('Yes')));

$f = $_SERVER['SCRIPT_NAME'];
if (($ptmp = strrpos($f, '/')) !== false)
    $f = substr($f, $ptmp + 1);

$current_script = $f;
if (($ptmp = strrpos($f, '.')) !== false)
    $f = substr($f, 0, $ptmp);

define('SCRIPT_NAME', $f);

$ptmp = explode('_', $f, 2);

if (count($ptmp) > 1)
    $t = $menu[$p[0]]['menu'][$ptmp[1]];
else
    $t = $menu[$ptmp[0]]['label'];

if ($t != 'Home')
    $tpl->assign('title', $t);

//Determine current paging item
$current_item = (!empty($_REQUEST['p']) && preg_match('`^[\d]+$`', $_REQUEST['p']) ? intval($_REQUEST['p']) : 1);

//Remove paging from request URI
$_SERVER['REQUEST_URI'] = preg_replace('`([?]|[&])(p=)+[\d]*`', '', request_uri());

$resetSort = 0;
//if r=1 is passed via URL, all sorting is reset
if (isset($_REQUEST['r']) && $_REQUEST['r'] == 1) {
    $resetSort = 0;
}

//Define sort fields
if (isset($_REQUEST['sort']) && !empty($_REQUEST['sort']) && $_REQUEST['r'] != 1) {
    define('SORT_FIELD', $_REQUEST['sort']);
    $tpl->assign('SORT_FIELD', SORT_FIELD);
} else {
    define('SORT_FIELD', 'ID');
    $tpl->assign('SORT_FIELD', SORT_FIELD);
}

//Define sort order
if (isset($_REQUEST['sort_order']) && !empty($_REQUEST['sort_order'])) {
    define('SORT_ORDER', $_REQUEST['sort_order']);
    $tpl->assign('SORT_ORDER', SORT_ORDER);
    define('REVERSE_SORT_ORDER', SORT_ORDER == 'ASC' ? 'DESC' : 'ASC');
    $tpl->assign('REVERSE_SORT_ORDER', REVERSE_SORT_ORDER);

    $tpl->assign('requestOrder', 1);
} else {
    define('SORT_ORDER', 'DESC');
    $tpl->assign('SORT_ORDER', SORT_ORDER);
    define('REVERSE_SORT_ORDER', 'ASC');
    $tpl->assign('REVERSE_SORT_ORDER', REVERSE_SORT_ORDER);

    $tpl->assign('requestOrder', 0);
}


//Disallow access to the page if it's not allowed for editors
if (!$_SESSION['phpld']['adminpanel']['is_admin']) {
    //List of pages editors are allowed to view
    $editorAllowed = array('login.php',
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
        'unauthorized.php',
        // Ajax scripts for datatables
        'dir_categs_ajax.php',
        'dir_links_ajax.php',
        'dir_articles_ajax.php'
    );
    if (!in_array($current_script, $editorAllowed)) {
        //Editor is on unallowed page, block access
        http_custom_redirect("unauthorized.php");
        exit();
    }
}

//Check if using RTE (Rich Text Editor)
$useRTE = (defined('ADMIN_USE_RTE') ? ADMIN_USE_RTE : 0);
$tpl->assign('useRTE', $useRTE);
$tpl->assign("rights", $_SESSION['phpld']['adminpanel']['rights']);

//The problem: we need to use js form.submit() function.
//But, all the forms have an <input name="submit"> which is used to test if the form was submitted: if (!empty($_REQUEST['submit'])) { ... }
//These inputs conflicts with the .submit() method. The browsers will raise a js error instead of submitting the form.
//The workaround: all the <input name="submit"> are renamed to <input name="whatever">.
//<input type="hidden" name="formSubmitted" value="1" /> - added to all forms
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