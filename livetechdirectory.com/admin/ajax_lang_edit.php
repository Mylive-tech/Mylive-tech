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

//Get phrase MD5 hash
$hash = (!empty ($_POST['hash']) ? strip_white_space($_POST['hash']) : '');

//Force new MD5 hash?
$forcenewhash = (!empty ($_POST['forcenewhash']) ? 1 : 0);

//Get language
$lang = (!empty ($_POST['lang']) ? strip_white_space($_POST['lang']) : '');

//Get phrase
$content = (!empty ($_POST['content']) ? urldecode ($_POST['content']) : '');

$langFilename = "../lang/{$lang}.php";
$langFilename_fullPath = INSTALL_PATH."/lang/{$lang}.php";

//Load language
$langArray = array();
if (file_exists ($langFilename))
{
   @ include ($langFilename);
   if (is_array ($__LANG))
   {
      $langArray = $__LANG;
      unset($__LANG);
   }
}

if (!empty ($hash) && $forcenewhash == 1)
{
   //Remove old phrase
   unset ($langArray[$hash]);
}

//Generate new MD5 phrase hash if none is available
$hash = (empty ($hash) || $forcenewhash == 1 ? md5 ($content) : $hash);

if (array_key_exists ($hash, $langArray))
{
   $temp = $langArray;
   unset ($langArray);
   $langArray = array();

   foreach ($temp as $phraseHash => $phraseValue)
   {
      if ($phraseHash == $hash)
      {
         //Overwrite with new hash and phrase to array
         $langArray[$hash] = $content;
      }
      else
      {
         //Add all other data
         $langArray[$phraseHash] = $phraseValue;
      }

      unset ($temp[$phraseHash], $phraseValue);
   }
}
else
{
   //Write new hash and phrase to array
   $langArray[$hash] = $content;
}

//Get language info
$langInfo = language_file_data($langFilename_fullPath);

//Build code to write into file
$code  = '<?php'."\n";
$code .= '/**'."\n";
$code .= 'Language:'.(!empty ($langInfo['LANGUAGE']) ? ' '.strip_white_space($langInfo['LANGUAGE']) : '')."\n";
$code .= 'Language File Author:'.(!empty ($langInfo['AUTHOR_NAME']) ? ' '.strip_white_space($langInfo['AUTHOR_NAME']) : '')."\n";
$code .= 'Language File Author URI:'.(!empty ($langInfo['AUTHOR_URL']) ? ' '.strip_white_space($langInfo['AUTHOR_URL']) : '')."\n";
$code .= '*/'."\n\n";

$code .= '$__LANG = '.var_export ($langArray, true).';'."\n".'?>';

//Write to file
$fileUpdated = write_to_file($langFilename_fullPath, $code, true);

//Free memory
unset ($langArray, $code, $langInfo, $langFilename, $langFilename_fullPath);


$files = glob(INSTALL_PATH.'temp/templates/*'); 
foreach($files as $file){ 
    if(is_file($file) && !strpos($file,'.htaccess')){
      unlink($file);
    }
}

//Return value
echo htmlspecialchars ($content, ENT_QUOTES);
?>