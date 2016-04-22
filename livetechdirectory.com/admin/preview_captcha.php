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
@ error_reporting (E_ERROR | E_WARNING | E_PARSE);

require_once '../libs/captcha/captcha.class.php';
require_once '../include/functions_imgverif.php';

$distorsion_level = (!empty ($_GET['level']) && is_numeric ($_GET['level'])) ? intval ($_GET['level']) : 4;
if($distorsion_level < 1)
   $distorsion_level = 1;
elseif($distorsion_level > 5)
   $distorsion_level = 5;

$simple_captcha = (is_numeric ($_GET['simple']) ? intval ($_GET['simple']) : 0);

$settings = array ();

/* Really Simple captcha generation
 * Use if no TTF and GD font is supported
 */
$settings['simple_captcha'] = $simple_captcha;

/* Absolute path to folder with fonts
 * With trailing slash!
 */
$settings['Fonts_Folder'] = '../libs/captcha/fonts/';

/* The minimum size a character should have */
$settings['minsize'] = 30;

/* The maximum size a character should have */
$settings['maxsize'] = 40;

/* The maximum degrees of an angle a character should be rotated.
 * A value of 20 means a random rotation between -20 and 20.
 */
$settings['angle'] = 30;

/* The background color of the image in HTML code
 * Default is "random"
 * Available options: - "random"
 *                    - "gradient"
 *                    - "56B100", "#F36100", "#6B6E4B" or whatever color you like
 */
$settings['background_color'] = 'random';

/* The image type
 * Default is "png" but "jpeg" and "gif" are also supported
 */
$settings['image_type'] = 'png';

/* Distorsion level of the image
 * !!! Level is automatically selected from the admin area configuration !!!
 */
$settings['image_distorsion_level'] = $distorsion_level;

$phrase_length = (defined ('CAPTCHA_PHRASE_LENGTH') && preg_match ('`^[\d]+$`', CAPTCHA_PHRASE_LENGTH) && CAPTCHA_PHRASE_LENGTH > 3 ? intval (CAPTCHA_PHRASE_LENGTH) : 5);
$phrase_length = ($phrase_length > 8 ? 8 : $phrase_length);

$captcha = &new CAPTCHA($settings);
$captcha->phrase = create_captcha_phrase($phrase_length);
$captcha->create_CAPTCHA();
?>