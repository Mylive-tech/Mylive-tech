<?php
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

error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE); //

ini_set('display_errors', '0'); 
error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT);


/**
 * Add our installation path to the include_path
 */
define ('INSTALL_PATH', substr (__file__, 0, -18));

if(!defined ('PATH_SEPARATOR'))
   define ('PATH_SEPARATOR', strtoupper (substr (PHP_OS, 0, 3)) == 'WIN' ? ';' : ':');
set_include_path(get_include_path() . PATH_SEPARATOR . INSTALL_PATH);
//ini_set ('include_path', ini_get ('include_path').PATH_SEPARATOR.INSTALL_PATH);
define ('TABLE_PREFIX','PLD_');
define ('ADODB_ASSOC_CASE', 1);
require_once 'include/tables.php';
define ('DEBUG', false);
define ('DEMO', false);

define('LANGUAGE', 'en');
define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'mylivete_ltdir');
define('DB_USER', 'mylivete_ltdir');
define('DB_PASSWORD', '$tqrhK-De-ew');
?>