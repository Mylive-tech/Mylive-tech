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
 
//Make additional spam protection checks

function check_post_rules($ressource='', $tplpath='', $returnVal=false)
{
   global $tpl;

   if (isset ($ressource) && is_array ($ressource) && !empty ($ressource))
   {
      //Check if submitter is using an user-agent
      if (defined ('ALLOW_EMPTY_USERAGENT') && ALLOW_EMPTY_USERAGENT != 1)
      {
         //Determine user-agent
         $userAgent = (isset ($_SERVER['HTTP_USER_AGENT']) && !empty ($_SERVER['HTTP_USER_AGENT']) ? filter_white_space($_SERVER['HTTP_USER_AGENT']) : '');

         if (empty ($userAgent))
         {
            //No user-agent available,
            //further access blocked

            unset ($_POST, $_GET, $_REQUEST);

            //Provide a reason why access was unautorised
            $reason = _L('You have no or an invalid useragent').'!';

            if ($returnVal)
            {
               return gotoUnauthorized($reason, $tplpath.'unauthorized.tpl', true);
            }
            else
            {
               gotoUnauthorized($reason, $tplpath.'unauthorized.tpl', false);
               exit();
            }
         }
      }

      //Check if submission is comming from
      //the current server or somewhere else
      if (defined ('ALLOW_FOREIGN_REFERER') && ALLOW_FOREIGN_REFERER != 1)
      {
         //Determine server hostname
         $serverHostTemp = (isset ($_SERVER['SERVER_NAME']) && !empty ($_SERVER['SERVER_NAME']) ? trim ($_SERVER['SERVER_NAME']) : (isset ($_SERVER['HTTP_HOST']) && !empty ($_SERVER['HTTP_HOST']) ? trim ($_SERVER['HTTP_HOST']) : ''));
         //Get only domain
         //(usually not needed but server configs are not always correct)
         $serverHost     = trim (parseDomain($serverHostTemp));

         if (empty ($serverHost))
         {
            //Could not determine server hostname,
            //usually if it's an IP address
            $serverPath = parseURL($serverHostTemp);
            $serverHost = (!empty ($serverPath['path']) ? $serverPath['path'] : $serverHostTemp);

            unset ($serverPath);
         }

         //Determine page where post came from
         $refererHostTemp = (isset ($_SERVER['HTTP_REFERER']) && !empty ($_SERVER['HTTP_REFERER']) ? trim ($_SERVER['HTTP_REFERER']) : '');
         $refererHost     = parseDomain($refererHostTemp);

         $pattern     = array ('`^http[s]?:`', '`^ftp:`', '`^mailto:`', '`^www\.`', '`^\.`', '`\.$`', '`[^\w\d-\.]`');
         $serverHost  = preg_replace ($pattern, '', $serverHost);
         $refererHost = preg_replace ($pattern, '', $refererHost);

         //Check if hostnames are identical
         if (!empty ($serverHost) && !empty ($refererHost) && $serverHost != $refererHost)
         {
            //Hostnames do not match,
            //Submission is not allowed!

            //Provide a reason why access was unautorised
            $reason = _L('You are now allowed to submit using foreign pages or scripts').'!';

            if ($returnVal)
            {
               return gotoUnauthorized($reason, $tplpath.'unauthorized.tpl', true);
            }
            else
            {
               gotoUnauthorized($reason, $tplpath.'unauthorized.tpl', false);
               exit();
            }
         }

         unset ($serverHost, $serverHostTemp, $refererHost, $refererHostTemp);
      }
   }

   unset ($ressource, $tplpath, $returnVal);
   return false;
}
?>