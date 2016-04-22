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
 * Create phrase to use on image verification
 * @param  integer  Length of phrase
 * @return mixed[string/integer] Phrase of given length
 * @note   "1" (one), "0" (zero), "i", "l", "o", both lower- and uppercase are not used to increase readability
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function create_captcha_phrase($length=5)
{
   $phrase = '';
   $phrase_type = (defined ('CAPTCHA_PHRASE_TYPE') ? CAPTCHA_PHRASE_TYPE : 'alphanumeric');

   switch($phrase_type)
   {
      case "alphabetical":
         $chars = array (
            'a','A','b','B','c','C','d','D','e','E','f','F','g','G','h','H',
            'j','J','k','K','m','M','n','N','p','P','q','Q','r','R','s','S',
            't','T','u','U','v','V','w','W','x','X','y','Y','z','Z'
            );
         break;

      case "numeric":
         $chars = array (
            '2','3','4','5','6','7','8','9',
            );
         break;

      case "alphanumeric":
         default:
         $chars = array (
            '2','3','4','5','6','7','8','9',
            'a','A','b','B','c','C','d','D','e','E','f','F','g','G','h','H',
            'j','J','k','K','m','M','n','N','p','P','q','Q','r','R','s','S',
            't','T','u','U','v','V','w','W','x','X','y','Y','z','Z'
            );
         break;
      }

   //Count chars array elements
   $count = count ($chars) - 1;

   //Shuffle chars array
   shuffle ($chars);

   //Seed a better random number generator
   mt_srand ((double) microtime() * 123456789);

   //Add a random char until max length is reached
   for ($i = 0; $i < $length; $i++)
      $phrase .= $chars[mt_rand (0, $count)];

   unset ($chars, $phrase_type, $length, $count);

   return $phrase;
}


/**
 * Create unique image hash and make DB entry
 * @param  none
 * @return mixed[string/integer] Unique image hash
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function fetch_captcha_hash()
{
   global $db, $tables;

   /* Number of characters the image should include
   * Do not use large numbers, default is 6, but you can set it to 5 or 4
   *
   * !! DO NOT CHANGE, IT AUTOMATICALLY USES THE VALUE SELECTED IN THE ADMIN AREA !!
   */
   $phrase_length = (defined ('CAPTCHA_PHRASE_LENGTH') && preg_match ('`^[\d]+$`', CAPTCHA_PHRASE_LENGTH) && CAPTCHA_PHRASE_LENGTH > 3 ? intval (CAPTCHA_PHRASE_LENGTH) : 5);
   $phrase_length = ($phrase_length > 8 ? 8 : $phrase_length);

   //Generate unique image hash
   $imagehash = md5 (uniqid (mt_rand (), true));

   //Insert into DB
   $db->Execute("INSERT INTO `{$tables['img_verification']['name']}` (`IMGHASH` , `IMGPHRASE`, `CREATED`, `VIEWED`) VALUES (".$db->qstr($imagehash).", ".$db->qstr(create_captcha_phrase($phrase_length)).", ".(defined ('TIMENOW') && strlen ('TIMENOW') > 0 ? $db->qstr(TIMENOW) : $db->qstr(time())).", 0)");

   return $imagehash;
}

/**
 * Validate provided image verification code and additional clean older DB entries
 * @param  mixed[string/integer]  Unique image hash
 * @param  mixed[string/integer]  Image verification code
 * @return integer  "0" (zero) on error or invalid phrase, "1" (one) on success
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function verify_captcha_hash($imagehash='', $imagecode='')
{
   global $db, $tables;

   //Clean DB table of older entries (10 minutes = 600 seconds = 60 sec * 10)
   //This will increase validation and keep DB table small
   $db->Execute("DELETE FROM `{$tables['img_verification']['name']}` WHERE (".(defined ('TIMENOW') && strlen ('TIMENOW') > 0 ? $db->qstr(TIMENOW) : $db->qstr(time()))." - `CREATED` > '60' * '10')");

   //Clean code
   $imagecode = preg_replace ('[\s]', '', $imagecode);

   //Validation will clean DB entry
   if ($db->Execute("DELETE FROM `{$tables['img_verification']['name']}` WHERE `IMGHASH` = ".$db->qstr($imagehash)." AND `IMGPHRASE` = ".$db->qstr($imagecode)))
   {
      return ($db->Affected_Rows() > 0 ? 1 : 0);
   }
   else
   {
      //Error in SQL query
      //Failed validation
      return 0;
   }

   return 0;
}
?>