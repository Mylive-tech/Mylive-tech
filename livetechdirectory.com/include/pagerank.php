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
//Convert a string to a 32-bit integer
function StrToNum($Str, $Check, $Magic)
{
   $Int32Unit = 4294967296;  // 2^32

   $length = strlen ($Str);
   for ($i = 0; $i < $length; $i++)
   {
      $Check *= $Magic;
      /*
         If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31),
         the result of converting to integer is undefined
         refer to http://www.php.net/manual/en/language.types.integer.php
      */
      if ($Check >= $Int32Unit)
      {
         $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
         //If the check less than -2^31
         $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
      }

      $Check += ord($Str{$i});
   }

   return $Check;
}

//Genearate a hash for an URL
function HashURL($String)
{
   $Check1 = StrToNum($String, 0x1505, 0x21);
   $Check2 = StrToNum($String, 0, 0x1003F);

   $Check1 >>= 2;
   $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
   $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
   $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);

   $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
   $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );

   return ($T1 | $T2);
}

//Genearate a checksum for the hash string
function CheckHash($Hashnum)
{
   $CheckByte = 0;
   $Flag = 0;

   $HashStr = sprintf ('%u', $Hashnum) ;
   $length = strlen ($HashStr);

   for ($i = $length - 1;  $i >= 0;  $i --)
   {
      $Re = $HashStr{$i};
      if (1 === ($Flag % 2))
      {
         $Re += $Re;
         $Re = (int)($Re / 10) + ($Re % 10);
      }

      $CheckByte += $Re;
      $Flag ++;
   }

   $CheckByte %= 10;

   if (0 !== $CheckByte)
   {
      $CheckByte = 10 - $CheckByte;

      if (1 === ($Flag % 2) )
      {
         if (1 === ($CheckByte % 2))
         {
            $CheckByte += 9;
         }

         $CheckByte >>= 1;
      }
   }

   return '7' . $CheckByte . $HashStr;
}

//Return -1 if no page rank was found
function get_page_rank($url)
{
   //Define multiple Google domains to check PR at
   $googleDomains = 'toolbarqueries.google.com';

   //Choose a random Google domain in order to prevent abuse
   $useGoogleHost = $googleDomains; //$googleDomains[mt_rand (0, count ($googleDomains) - 1)];

   //Wheter to use another connection type
   //Auto modified if other options (ie: cURL) are not available
   $useOtherConn = 0;

   //Number of seconds to wait whilst trying to connect
   $timeout = 10;

   //Make sure URL is not encoded
   $url = urldecode ($url);

   //Remove all kind of white-space chars, ecept the basic space
   $search = array ( "\n",   //*NIX
                     "\r",   //Mac
                     "\r\n", //Windows
                     "\t",   //Tab
                     "\x0B", //Vertical Tab
                     "\0"    //NULL BYTE
            );
   $url = str_replace ($search, '', $url);
   $url = trim ($url);

   if (empty ($url))
   {
      //URL is empty
      //return N/A
      return -1;
   }

   //Correct URL value
   if (!preg_match ('#^http[s]?:\/\/#i', $url))
   {
      $url = 'http://' . $url;
   }

   //Check if "allow_url_fopen" is enabled
   $allowUrlFopen = (function_exists ('ini_get') && ini_get ('allow_url_fopen') ? 1 : 0);

   //Determine checksum
   $checksum = CheckHash(HashURL($url));

   $queryString = '/tbr?client=navclient-auto&ch='.$checksum.'&features=Rank:&q=info:'.$url;

   //Define Pagerank as -1 (N/A)
   $pagerank = -1;

   //String that will contain the PR content by Google
   $responseContent = '';

   //Try to use "file_get_contents" (best method)
   //According to PHP manual: http://www.php.net/manual/en/function.file-get-contents.php
   //file_get_contents() is the preferred way to read the contents of a file into a string.
   //It will use memory mapping techniques if supported by your OS to enhance performance.
   if (function_exists ('file_get_contents') && $allowUrlFopen == 1)
   {
      $responseContent = @ file_get_contents ('http://'.$useGoogleHost.$queryString);

      if (empty ($responseContent))
      {
         //FALSE or empty response content
         $useOtherConn = 1;
      }
      else
      {
         //Content received, make sure to stop here
         $useOtherConn = 0;
      }
   }
   else
   {
      //"file_get_contents" not available
      //Try cURL or "fsockopen"
      $useOtherConn = 1;
   }

   //Use cURL if available for a second best performance
   if (function_exists ('curl_init') && $useOtherConn == 1)
   {
      //Initialize a cURL session
      $cResource = @ curl_init();

      //Set cURL options
      @ curl_setopt ($cResource, CURLOPT_URL, 'http://'.$useGoogleHost.$queryString);
      @ curl_setopt ($cResource, CURLOPT_RETURNTRANSFER, 1);
      @ curl_setopt ($cResource, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
      @ curl_setopt ($cResource, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) Gecko/20021204');
      @ curl_setopt ($cResource, CURLOPT_CONNECTTIMEOUT, $timeout);
      @ curl_setopt ($cResource, CURLOPT_TIMEOUT, $timeout);

      //Get content
      $responseContent = trim (@ curl_exec ($cResource));
      @ curl_close ($cResource);

      if (empty ($responseContent)) //FALSE or empty response content
      {
         //An error occured or no content available
         //Give Pagerank another, try last method "fsockopen"
         $useOtherConn = 1;
      }
      else
      {
         //Content received, make sure to stop here
         $useOtherConn = 0;
      }
   }

   if ($useOtherConn == 1)
   {
      //Open socket connection
      $fp = @ fsockopen ($useGoogleHost, 80, $errno, $errstr, $timeout);
      if (!$fp)
      {
         echo "{$errstr} ({$errno})<br />\n";
      }
      else
      {
         $out  = "GET {$queryString} HTTP/1.1\r\n" ;
         $out .= "Host: {$useGoogleHost}\r\n" ;
         $out .= "Connection: Close\r\n\r\n" ;
         @ fwrite ($fp, $out);

         @ stream_set_timeout ($fp, $timeout);

         while (!feof ($fp))
         {
            $responseContent .= @ fgets ($fp, 1024);
         }

         @ fclose ($fp);
      }
   }

   //Search for Pagerank in response code
   if (preg_match ('#Rank_([\d]+):([\d]+):([\d]+)#si', $responseContent, $matches))
   {
      $pagerank = (isset ($matches[3]) ? trim ($matches[3]) : $pagerank);
   }

   //Allow numeric value with a minus
   $pagerank = (preg_match ('#^[-]?[\d]+$#', $pagerank) ? $pagerank : -1);
   $pagerank = ($pagerank < 0 ? -1 : intval ($pagerank));//Not lower than "-1"

   //Pagerank cannot be higher than 10 (just in case errors occur)
   $pagerank = ($pagerank > 10 ? 10 : $pagerank);

   return $pagerank;
}
?>