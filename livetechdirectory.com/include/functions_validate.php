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
 * Check if client is banned
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function check_if_banned()
{
   global $tpl;

   if (is_banned_ip() == 1)
   {
       http_custom_redirect(DOC_ROOT.'/banned-massage');
      //echo $tpl->fetch('banned_submit.tpl');
      exit();
   }
}

/**
 * Check if category is closed to link submission
 */
function check_if_closed_to_links($catId)
{
   global $tpl;
   global $db, $tables;
   $closed_l = $db->GetOne("SELECT `CLOSED_TO_LINKS` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($catId));

   
   if ($closed_l == 1) {
      return false;
   }
   else
      return true;

}

/**
 * Check if client IP address is banned
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function is_banned_ip($ip='')
{
   global $db, $tables, $client_info;

   $ip = clean_string_paranoia($ip);

   if (empty ($ip))
   {
      //No IP address provided, determine it now
      $clientIP = (isset ($client_info['IP']) && !empty ($client_info['IP']) ? clean_string_paranoia($client_info['IP']) : $_SERVER['REMOTE_ADDR']);
   }

   if (empty ($clientIP))
   {
      //Could not determine client IP address
      return 0;
   }

   //Get banned IP addresses
   $sql = "SELECT DISTINCT `BAN_IP` FROM `{$tables['banlist']['name']}` WHERE `BAN_IP` IS NOT NULL";
   $addresses = $db->GetCol($sql);

   //Check once more if anything was banned already
   if ($addresses === false || !is_array ($addresses) || empty ($addresses))
   {
      return false;
   }

   //Loop through each banned IP
   foreach ($addresses as $key => $bannedIp)
   {
      //Last part might contain a wildcard "*"
      $regex = str_replace ('\*', '(.*)', preg_quote ($bannedIp, '#'));

      if (preg_match ('#^'.$regex.'#U', $clientIP))
      {
         //IP address matched, it's banned
         return 1;
      }

      unset ($addresses[$key]);
   }
   unset ($addresses, $sql, $clientIP);

   return 0;
}

/**
 * Check if domain/subdomain is banned ("subdomain.domain.com" as well as "domain.com")
 * @param string URL to check it's domain if banned
 * @return boolean/integer (1 = banned, 0 = not banned)
 */
function is_banned_domain($url='')
{
   global $db, $tables;

   //Take only domain out of full URL
   $domain = parseDomain(clean_string_paranoia($url));

   //Don't need URL anymore
   unset ($url);

   if (empty ($domain))
      return 0;

   //Get banned domains
   $sql = "SELECT DISTINCT `BAN_DOMAIN` FROM `{$tables['banlist']['name']}` WHERE `BAN_DOMAIN` IS NOT NULL";
   $domainList = $db->GetCol($sql);

   //Check once more if anything was banned already
   if ($domainList === false || !is_array ($domainList) || empty ($domainList))
   {
      return 0;
   }

   //Loop through each banned domain
   foreach ($domainList as $key => $bannedDomain)
   {
      //Take care of wildcards
      $regex = str_replace ('\*', '(.*)', preg_quote ($bannedDomain, '#'));

      //Make sure domain matches exactly
      //Wildcard is the only option to change this
      if (preg_match ('#^'.$regex.'$#i', $domain))
      {
         //Domain matched, it's banned
         return 1;
      }

      unset ($domainList[$key]);
   }
   unset ($domainList, $sql, $domain);

   return 0;
}

/**
 * Check if email address is banned ("email@domain.com" as well as "@domain.com")
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function is_banned_email($email='')
{
   global $db, $tables;

   $email = clean_string_paranoia($email);

   if (empty ($email))
      return 0;

   //Get banned email addresses
   $sql = "SELECT DISTINCT `BAN_EMAIL` FROM `{$tables['banlist']['name']}` WHERE `BAN_EMAIL` IS NOT NULL";
   $addresses = $db->GetCol($sql);

   //Check once more if anything was banned already
   if ($addresses === false || !is_array ($addresses) || empty ($addresses))
   {
      return 0;
   }

   //Loop through each banned email
   foreach ($addresses as $key => $bannedEmail)
   {
      if (is_valid_email($bannedemail))
	   {
	      //Email format valid, make sure email matches exactly
         $regex = '^' . preg_quote ($bannedEmail, '#') . '$';
		}
		else
		{
         //Partial email format will match from the end
			$regex = preg_quote ($bannedEmail, '#') . '$';
		}

      if (preg_match ('#'.$regex.'#i', $email))
      {
         //Email address matched, it's banned
			return 1;
		}

      unset ($addresses[$key]);
   }
   unset ($addresses, $sql, $email);

   return 0;
}

/**
 * Check if word is banned
 * @param array Complete submition data
 * @param array Fields to check for banned words
 * @return boolean/integer 1=banned, 0=not banned
 * @author Radi Naydenov / <radi@naydenov.info> (http://www.naydenov.info)
 */
function if_word_is_banned($data=null, $fields=null)
{
   global $db, $tables, $tpl;

   //Parameter is array?
   if (!is_array ($data) || empty ($data))
      return 0;

   //Define fields to check for banned words
   if (!is_array ($fields))
   {
      $fields = array ('TITLE', 'DESCRIPTION', 'URL', 'META_DESCRIPTION');
   }

   //Get banned words
   $sql = "SELECT DISTINCT `BAN_WORD` FROM `{$tables['banlist']['name']}` WHERE `BAN_WORD` IS NOT NULL";
   $words = $db->GetCol($sql);

   //Check once more if anything was banned already
   if ($words === false || !is_array ($words) || empty ($words))
   {
      return 0;
   }

   //Loop through each banned word
   foreach ($words as $key => $bannedWord)
   {
      //Create regexp
      $regex = preg_quote ($bannedWord, '#');

      foreach ($fields as $fKey => $fieldName)
      {
         //if (isset ($data[$fieldName]) && preg_match("#\b{$regex}\b#i", $data[$fieldName]))
		 if (isset ($data[$fieldName]) && preg_match("#\b{$regex}\b#i", $data[$fieldName]) || isset ($data[$fieldName]) && strpos($data[$fieldName],$bannedWord) !== false )
         {
            //Word matched in current field
            //it's banned
            return 1;
         }
      }

      unset ($words[$key], $bannedWord, $regex);
   }

   return 0;
}

/**
 * Check if email address is banned [SmartyValidate]
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function validate_is_banned_email($value, $empty, &$params, &$form)
{
   return (is_banned_email($value) == 0 ? true : false);
}

/**
 * Check for valid email address format (email@domain.com)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function is_valid_email($email='')
{
	return preg_match ('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s\'"<>]+\.+[a-z]{2,7}))$#si', $email);
}

/**
 * Check file status, if readable, writable...
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function file_status($filename)
{
   if (!isset ($filename) || empty ($filename))
      return false;

   // check if file exists
   if (!file_exists ($filename))
      return _L("File does not exist!");
   elseif (!is_readable ($filename))
      return _L("File is not readable!"); // File not readable
   elseif (!is_writable ($filename))
      return _L("File is not writeable! Try \"chmod 777 filename\"."); // File not writable

   if (!$file = @fopen($filename, "r"))
      return _L("File could not be opened!"); // File cannot be opened

   @fclose($file); // Close file

   return true;
}

/**
 * Check folder status, if readable, writable...
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function folder_status($foldername)
{
   if (!isset($foldername) || empty($foldername))
      return false;

   if (!file_exists($foldername))
      return _L("Folder does not exist!"); // check if file exists
   elseif (!is_readable($foldername))
      return _L("Folder is not readable!");// check if readable
   elseif (!is_writable($foldername))
      return _L("Folder is not writeable! Try \"chmod 777 foldername\"."); // check if writable
   else
      return true;
}

function validate_link($url)
{
   $ret = get_url($url, URL_HEADERS);

   return array ($ret['status'] ? 2 : 0, ($ret['status'] || $ret['code']) ? $ret['response'] : $ret['error']);
}

function validate_captcha($value, $empty, &$params, &$form)
{
   require_once 'functions_imgverif.php';

   return (verify_captcha_hash($form[$params['field2']], $value) ? true : false);
}

function validate_url_online($value, $empty, &$params, &$form)
{
   $ret = get_url($value, URL_HEADERS);

   return ($ret['status'] ? true : false);

   return 1;
}

function validate_recpr_link($value, $empty, &$params, &$form)
{
   global $tpl;

   if ($empty && empty ($value))
      return 1;

   $ret = check_recpr_link($form);
   if (empty ($ret))
      return 0;

   return 1;
}

/**
 * Check if reciprocal link is available on an internet homepage
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 *
 * @param array
 * @return 1 = success, 0 and -1 error
 */
function check_recpr_link($data)
{
   //Check for reciprocal link field
   if (!isset ($data['RECPR_URL']))
   {
      //No reciprocal link available
      //Reciprocal link validation failed
      return -1;
   }

   $data['RECPR_URL'] = trim ($data['RECPR_URL']);

   //Check if reciprocal link is not empty
   if (empty ($data['RECPR_URL']))
   {
      //Reciprocal link field empty
      //Reciprocal link validation failed
      return -1;
   }
if(SAME_DOMAIN_RECPR == 1){
//Check if submitted URL is from the same domain as the recpr URL
$submitDomain = parseDomain(!empty ($data['URL']) ? $data['URL'] : '');
$recprDomain  = parseDomain(!empty ($data['RECPR_URL']) ? $data['RECPR_URL'] : '');
if ($submitDomain != $recprDomain)
{
   //Submitted URL is not of the same domain as the recpr URL
   return 0;
}
unset ($submitDomain, $recprDomain);
}
   //Load HTTP request class
   require_once 'libs/request/request.class.php';

   $fallBack = false;

   //If HTTP request class exists,
   //use it over the default content browser
   if (class_exists ('http_request'))
   {
      //Define request options
      $options = array();
      //Default http request version ("HTTP/1.1" or "HTTP/1.0")
      $options['httpVersion']          = 'HTTP/1.1';
      //Type of HTTP request method (GET, POST, etc)
      //Currently only GET is fully supported
      $options['method']               = 'GET';
      //User agent to masquerade as
      $options['userAgent']            = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0';
      //Maximum number of seconds to allow for connection
      $options['connTimeout']          = 10;
      //Maximum number of seconds to allow request functions to execute/read
      $options['readTimeout']          = 10;
      //Whether to allow redirects (done on 3xx status codes)
      $options['allowRedirects']       = true;
      //Maximum redirects allowed (works only if "allowRedirects" is TRUE)
      $options['maxRedirects']         = 5;
      //Define request methods in order they should be tried out
      //NULL or empty array will reset to defaults

      //-->I am not sure if "file_get_contents" could timeout
      //-->on neverending redirects or how to set these options
      $options['requestMethodsOrder']  = array ( 'curl_request'      => 'curl_request', //use cURL (fast and reliable)
                                                 'file_get_contents' => 'file_get_contents', //use "file_get_contents()" fast
                                                 'request'           => 'request', //use basic content request
                                                 );

      //Initialize HTTP request class
      $http_request = new http_request($data['RECPR_URL'], $options);

      if ($http_request === false)
      {
         //Could not fetch URL or and error occured
         //Reciprocal link validation failed
         return -1;
      }
      else
      {
         //Get Content
         $responseContent = $http_request->getResponseBody();
         $responseContent = (is_string ($responseContent) && !empty ($responseContent) ? $responseContent : '');

         //If no response content, fall back to "get_url()"
         if (empty ($responseContent))
         {
            $fallBack = true;
         }

         $responseCode = ($http_request->getResponseCode() ? $http_request->getResponseCode() : 0);

         $responseHeaders = $http_request->getResponseHeader();
      }
   }
   else
   {
      $fallBack = true;
   }

   if ($fallBack)
   {
      //HTTP request class is not available, or failed
      $return = get_url($data['RECPR_URL'], URL_CONTENT);

      //Get response status/code
      $responseStr = (is_string ($return['response']) && !empty ($return['response']) ? $return['response'] : '');

      //Extract response code (should be numeric)
      preg_match ("#^(HTTP[\s]*/[\d\.\d]+[\s]*)([\d]+)#i", $responseStr, $matches);

      //Check for matches
      if (isset ($matches[2]) && !empty ($matches[2]))
      {
         //Make sure response code is an integer value
         $responseCode = intval ($matches[2]);
      }
      else
      {
         //Could not extract response code
         $responseCode = 0;
      }

      //Get response headers
      $responseHeaders = (isset ($return['headers']) && is_array ($return['headers']) ? $return['headers'] : array());
      //Provide also lowercase response headers
      foreach ($responseHeaders as $headerName => $headerValue)
      {
         $responseHeaders[strtolower($headerName)] = $headerValue;
      }

      //Get Content
      $getContent = (is_string ($return['content']) && !empty ($return['content']) ? $return['content'] : '');

      //Get correct content, optionally decode content
      if (isset ($responseHeaders['Content-Encoding']))
      {
         //Decode any content-encoding (gzip or deflate) if needed
         switch (strtolower ($responseHeaders['Content-Encoding']))
         {
            //Handle gzip encoding
            case 'gzip':
               $responseContent = gz_decode($getContent);

               //If error occured, try again with a very simple decoder
               if ($responseContent === false || empty ($responseContent))
               {
                  $responseContent = decode_gzip($getContent);
               }
               break;

            //Handle deflate encoding
            case 'deflate':
               $responseContent = decode_deflate($getContent);
               break;

            //No or undetermined content encoding
            default:
               $responseContent = $getContent;
               break;
         }
      }
      else
      {
         //Content was not encoded or encoding was not determined
         //Use as is
         $responseContent = $getContent;
      }

      unset ($getContent, $return);
   }

   //Check whether the response code is successful
   //If response code was not determined, this step is skipped
   if (defined ('RECPR_NEED_200_OK') && RECPR_NEED_200_OK == 1 && $responseCode > 0)
   {
      $resType = floor ($responseCode / 100);

      //Define success code prefixes
      //1XX, 2XX, etc
      $successResTypes = array (1, 2);

      //Success codes: 1XX, 2XX
      //Not sure about 3XX
      if (!in_array ($resType, $successResTypes))
      {
         return 0;
      }

      unset ($successResTypes);
   }


   //Make content idiot proof
   //Remove all kind of white-space chars
   $responseContent = strip_white_space($responseContent);

   if (empty ($responseContent))
   {
      //Could not fetch URL
      //Possible library missing or an error occured
      //Reciprocal link validation failed
      return -1;
   }

   if (defined ('RECPR_CHECK_NOFOLLOW') && RECPR_CHECK_NOFOLLOW == 1)
   {
      //Define array where META tags are stored
      $metaTags = array();

      //Define META tags that allow and use "nofollow",
      //Don't need other tags
      $robotTags = array(  'robots'    , //All robots/crawlers/SEs
                           'googlebot' , //Google crawler
                           /*'msnbot'*/
                   );

      //Get META tags
      preg_match_all ('/<[\s]*meta[\s]*(name|http-equiv)[\s]*=[\s]*["\']?([^>"\']*)["\']?[\s]*content[\s]*=[\s]*["\']?([^>"\']*)["\']?[\s]*[\/]?[\s]*>/si', $responseContent, $matches);

      //Check if META tags available
      if (!empty ($matches) && is_array ($matches) && count ($matches[2]) == count ($matches[3]))
      {
         //Loop through each found META tag
         foreach ($matches[2] as $key => $value)
         {
            //Determine META tag
            $value = clean_string_paranoia($value);

            //Keep only "robots" META tags
            if (in_array (strtolower ($value), $robotTags))
            {
               $metaTags[$value] = clean_string_paranoia($matches[3][$key]);
            }
         }
      }

      if (is_array ($metaTags) && !empty ($metaTags))
      {
         //Loop through each found "robots" tags
         foreach ($metaTags as $tagName => $tagValue)
         {
            //Check for global "nofollow" attributes
            if (preg_match ('#nofollow#Ui', $tagValue))
            {
               //Found the "nofollow" attribute in META tags,
               //Reciprocal link validation failed
               return 0;
            }
         }

         //Free memory
         unset ($metaTags, $tagName, $tagValue);
      }
   }

   //Define URL to look for
   $recprURL = (defined ('RECPR_CHECK_DOMAIN') && RECPR_CHECK_DOMAIN != 1 ? DEFAULT_RECPR_URL : parseDomain(DEFAULT_RECPR_URL));

   //Look for reciprocal in any link tag
   preg_match_all ("`<(\s*)a([^>]*)".trim($recprURL)."([^>]*)>(.*)<\/a>`Ui", $responseContent, $matches);

   //First check if matches array was created
   if (!is_array ($matches) || empty ($matches))
   {
      //No URL matches
      //Reciprocal link validation failed
      return 0;
   }

   //Set internal pointer of array
   //to its first element
   reset ($matches);

   //Check if URL exists
   if (!is_array ($matches[key ($matches)]) || empty ($matches[key ($matches)]))
   {
      //URL was not found
      //Reciprocal link validation failed
      return 0;
   }

   //Check for "nofollow" attribute in link tag
   if (defined ('RECPR_CHECK_NOFOLLOW') && RECPR_CHECK_NOFOLLOW == 1)
   {
      foreach ($matches as $m => $match)
      {
         foreach ($match as $key => $value)
         {
            //If anything matching <rel="nofollow"> is found, validation fails
            if (preg_match ('`rel[\s]*=[\s]*("|\')?[\s]*nofollow[\s]*("|\')`Ui', $value))
            {
               //Found the "nofollow" attribute,
               //Reciprocal link validation failed
               return 0;
            }

            unset ($match[$key]);
         }
         unset ($matches[$m]);
      }
   }

   //Free memory
   unset ($matches, $responseContent, $responseHeaders);

   return 1;
}

function check_allowed_feat($CategID=0)
{
   global $db, $tables;

   if (empty ($CategID))
      return 0;

   return 1;
}

function validate_unique($value, $empty, &$params, &$form)
{
   return check_unique($params['field2'], $params['field'], $value, $params['field3'], $params['field4'], $form[$params['field4']], $params['field5'], $params['field6']);
}

function validateUrlUnique($value, $empty, &$params, &$form)
{
   return checkUrlUnique($params['field2'], $params['field'], $value, $params['field3'], $params['field4'], $form[$params['field4']], $params['field5'], $params['field6']);
}

function checkUrlUnique($table, $field, $value, $exclude_id = NULL, $parent_field = NULL, $parent_value = NULL, $exclude_from_field = NULL, $exclude_value = NULL)
{
   global $tables, $db;

   if (empty ($value))
      return 0;

   $sql = "SELECT `URL` FROM `".$tables[$table]['name']."` WHERE `".$field."` LIKE ".$db->qstr('%'.$value.'%');
   if (strlen ($exclude_id) > 0)
      $sql .= " AND `ID` != ".$db->qstr($exclude_id);

   if (!empty ($parent_field))
      $sql .= " AND `".$parent_field."` = ".$db->qstr($parent_value);

   if (!empty ($exclude_from_field) && !empty ($exclude_value))
      $sql .= " AND `".$exclude_from_field."` != ".$db->qstr($exclude_value);

   //Retrieve simmilar URLs from DB
   $simmilarURLs = $db->GetCol($sql);

   if (!is_array ($simmilarURLs) || empty ($simmilarURLs))
   {
      //No simmilar URLs found
      return 1;
   }
   else
   {
      //Loop through each simmilar URL and compare
      foreach ($simmilarURLs as $key => $dbURL)
      {
         //Get only domain
         $dbURL = parseDomain($dbURL);

         //Check if domains match
         if (preg_match ('#^'.$value.'$#i', $dbURL))
         {
            //Domains matched
            return 0;
         }

         //Free some memory
         unset ($simmilarURLs[$key], $dbURL);
      }
   }

   return 1;
}

function check_unique($table, $field, $value, $exclude_id = NULL, $parent_field = NULL, $parent_value = NULL, $exclude_from_field = NULL, $exclude_value = NULL)
{
   global $tables, $db;
   $sql = "SELECT COUNT(*) FROM `".$tables[$table]['name']."` WHERE `".$field."` = ".$db->qstr($value);
   if (strlen ($exclude_id) > 0)
      $sql .= " AND `ID` != ".$db->qstr($exclude_id);

   if (!empty ($parent_field))
      $sql .= " AND `".$parent_field."` = ".$db->qstr($parent_value);

   if (!empty ($exclude_from_field) && !empty ($exclude_value))
      $sql .= " AND `".$exclude_from_field."` != ".$db->qstr($exclude_value);

   $c = $db->GetOne($sql);

   return ($c == 0 ? true : false);
}

function validate_symbolic_unique($value, $empty, &$params, &$form)
{
   global $tpl, $tables, $db, $id;
   $sql = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `SYMBOLIC_ID` = ".$db->qstr($value)." AND `PARENT_ID` = " .$db->qstr($form['PARENT_ID']);
   if ($id > 0)
      $sql .= " AND ID <> ".$id;

   $c = $db->GetOne($sql);

   return ($c == 0 ? true : false);
}

function validate_symbolic_parent($value, $empty, &$params, &$form)
{
   global $tpl, $tables, $db;
   $sql = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($value)." AND `PARENT_ID` = ".$db->qstr($form['PARENT_ID']);
   $c = $db->GetOne($sql);

   return ($c == 0 ? true : false);
}

/**
 * Validate domain/subdomain if banned ("subdomain.domain.com" as well as "domain.com")
 * SmartyValidate
 */
function validate_isBannedDomain($value, $empty, &$params, &$form)
{
   return is_banned_domain($value) ? false : true;
}

function validate_not_equal($value, $empty, &$params, &$form)
{
   return $value != $params['field2'];
}

function validate_not_equal_var($value, $empty, &$params, &$form)
{
   return $value != $form[$params['field2']];
}

function validate_restricted_ip($value, $empty, &$params, &$form)
{
   return (!preg_match ("#^(127\.0\.0\.1|10\.|172\.16\.|192\.168\.)#", $value) ? true : false);
}

function validate_ip($value, $empty, &$params, &$form)
{
   return (preg_match ('`^\d([0-9]{1,3}).\d([0-9]{1,3}).\d([0-9]{1,3}).\d([0-9]{1,3})$`', $value) ? true : false);
}

function validate_banIp($value, $empty, &$params, &$form)
{
   return (preg_match ('`^([0-9]{1,3})\.([0-9]{1,3})\.([\d\.*]+)$`', $value) ? true : false);
}

/**
 * Domain name RegExp http://www.regexlib.com/REDetails.aspx?regexp_id=391
 */
function validate_domain($value, $empty, &$params, &$form)
{
   return (preg_match ('`^([a-zA-Z0-9\-]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$`', $value) ? true : false);
}

/**
 * Domain name RegExp http://www.regexlib.com/REDetails.aspx?regexp_id=391
 * Allows also wildcard "*"
 * !! To be used in admin panel only !!
 */
function validate_toBanDomain($value, $empty, &$params, &$form)
{
   //Allow wildcard only as prefix
   return (preg_match ('`^([a-zA-Z0-9\-*]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$`', $value) ? true : false);

   //Paranoic, allows wildcard everywhere
   /*
   return (preg_match ('`^([a-zA-Z0-9\-*]([a-zA-Z0-9\-*]{0,61}[a-zA-Z0-9*])?\.)+[a-zA-Z*]{2,6}$`', $value) ? true : false);
   */
}

function validate_toBanEmail($value, $empty, &$params, &$form)
{
   return (preg_match ('`^.*\@(\[?)[a-zA-Z0-9\.\-]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?)$`', $value) ? true : false);
}

function validate_email_and_add_link($value, $empty, &$params, &$form)
{
   global $tpl, $tables, $db, $id;

   if ($form['TPL_TYPE'] !=3)
      return true;

   if(!empty ($id))
         return true;
   else {
      $sql = "SELECT `ID` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '3'";
      $c = $db->GetOne($sql);
      if ($c)
         return false;
      else
         return true;
   }
}

function validate_not_sub_category($value, $empty, &$params, &$form)
{
   global $tpl, $tables, $db, $u;

   $category = $value;

   $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($form['CATEGORY_ID']);
   $category = $db->GetOne($sql);

   if ($category != 0)
   {
      $count_sql = "SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = ".$db->qstr($u). " AND (`CATEGORY_ID` = ".$db->qstr($category);

      while ($category != 0)
      {
         $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($category);
         $category = $db->GetOne($sql);
         if ($category != 0)
            $count_sql .= " OR `CATEGORY_ID` = ".$db->qstr($category);
      }
      $count_sql .= ")";
      $c = $db->GetOne($count_sql);
   }
   else
      $c = 0;

   return ($c == 0 ? true : false);
}

//author avatar related

function validate_isImageSize($value, $empty, &$params, &$formvars) {

    // If no params
    if (!isset($params['field2']) or !isset($params['field3'])) {
        trigger_error("SmartyValidate: [isImageSize] one of the parameters is missing.");
        return false;
    }
    
    $_field = $params['field'];
    
    // Nothing in the form
    if(!isset($_FILES[$_field])) return false;
    // No file uploaded
    if($_FILES[$_field]['error'] == 4) return $empty;
    
    $_maxWidth = $params['field2'];
    $_maxHeight = $params['field3'];
    
    // Get image dimensions
    list($_width, $_height) = getimagesize($_FILES[$_field]['tmp_name']);
    
    if ($_width > $_maxWidth or $_height > $_maxHeight) return false;
    
    return true;
}

//end
?>