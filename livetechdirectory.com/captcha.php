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
 
require_once 'init.php';

if (!defined ('IN_PHPLD'))
{
   die("!! ERROR !! You are not allowed to run this script!");
}

require_once 'libs/captcha/captcha.class.php';

@ error_reporting (E_ERROR | E_WARNING | E_PARSE);

$settings = array ();

/* Really Simple captcha generation
 * Use if no TTF and GD font is supported
 */
$settings['simple_captcha'] = (CAPTCHA_SIMPLE ? 1 : 0);

/* Absolute path to folder with fonts
 * With trailing slash!
 */
$settings['Fonts_Folder'] = 'libs/captcha/fonts/';

/* The minimum size a character should have */
$settings['minsize'] = 30;

/* The maximum size a character should have */
$settings['maxsize'] = 30;

/* The maximum degrees of an angle a character should be rotated.
 * A value of 20 means a random rotation between -20 and 20.
 */
$settings['angle'] = 45;

/* The background color of the image in HTML code
 * Default is "random"
 * Available options: - "random"
 *                    - "gradient"
 *                    - "56B100", "#F36100", "#6B6E4B" or whatever color you like
 */
$settings['background_color'] = '#FFFFFF';

/* The image type
 * Default is "png" but "jpeg" and "gif" are also supported
 */
$settings['image_type'] = 'png';

/* Distorsion level of the image
 *
 * !! DO NOT CHANGE, IT AUTOMATICALLY USES THE VALUE SELECTED IN THE ADMIN AREA !!
 */
$settings['image_distorsion_level'] = (CAPTCHA_DISTORTION_LEVEL ? CAPTCHA_DISTORTION_LEVEL : 1);

$imagehash = (!empty ($_REQUEST['imagehash']) ? preg_replace ('[\s]', '', $_REQUEST['imagehash']) : '');


if (empty ($imagehash) || strlen ($imagehash) != 32)
{
   exit ('No or invalid imagehash available!');
}
elseif (!$phrase = $db->GetOne("SELECT `IMGPHRASE` FROM `{$tables['img_verification']['name']}` WHERE `IMGHASH` = ".$db->qstr($imagehash)." AND `VIEWED` = '0'"))
{
   exit ('Could not fetch image phrase!');
}
else
{
   $db->GetOne("UPDATE `{$tables['img_verification']['name']}` SET `VIEWED` = '1' WHERE `IMGHASH` = ".$db->qstr($imagehash)." AND `VIEWED` = '0'");

   if ($db->Affected_Rows() == 0)
   {
      //Image was viewed by someone else
      exit ('Error, please reload page!');
   }
}
$error_level = error_reporting();
error_reporting($error_level ^ E_WARNING);
//Initialize CAPTCHA class
$captcha = new CAPTCHA($settings);
$captcha->phrase = $phrase;
$captcha->create_CAPTCHA();
?>