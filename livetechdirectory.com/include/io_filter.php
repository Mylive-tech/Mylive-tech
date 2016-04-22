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
 * Clean all request variables of HTML and Javascript code, PHP code is by default removed.
 * This will prevent malicious code and XSS attacks from execution.
 *
 * !! ATTENTION !! ALL $_REQUEST, $_GET and $_POST variables are cleaned!
 *                 To allow some HTML tags, please use the phpLD admin panel!
 */


//Check if InputFilter class exists,
//otherwise do nothing
if (class_exists ('InputFilter'))
{
   if (defined ('ALLOW_HTML') && ALLOW_HTML == 1 && defined ('ALLOWED_HTML_TAGS') && strlen ('ALLOWED_HTML_TAGS') > 0)
   {
      //Clean up tag list
      $allowedTags = clean_string_paranoia(ALLOWED_HTML_TAGS);

      //Remove multiple commas, commast at begin and end of sting, multiple spaces
      $allowedTags = preg_replace (array ('#^[,]*#i', '#[,]*$#i', '#[,]+#i', '#[\s]#'), array ('', '', ',', ''), $allowedTags);

      if (empty ($allowedTags))
      {
         //No allowed tags found
         $tagsArray = array ();
      }
      else
      {
         //Create unique array of allowed tags
         $tagsArray = array_unique (explode (',', $allowedTags));
      }

      unset ($allowedTags);
   }
   else
   {
      //No allowed tags
      $tagsArray = array ();
   }
   

if (defined ('ALLOW_ATTR') && ALLOW_ATTR == 1 && defined ('ALLOWED_ATTR_TAGS') && strlen ('ALLOWED_ATTR_TAGS') > 0)
   {
      //Clean up tag list
      $allowedAttr = clean_string_paranoia(ALLOWED_ATTR_TAGS);

      //Remove multiple commas, commast at begin and end of sting, multiple spaces
      $allowedAttr = preg_replace (array ('#^[,]*#i', '#[,]*$#i', '#[,]+#i', '#[\s]#'), array ('', '', ',', ''), $allowedAttr);

      if (empty ($allowedAttr))
      {
         //No allowed tags found
         $attrArray = array ();
      }
      else
      {
         //Create unique array of allowed tags
         $attrArray = array_unique (explode (',', $allowedAttr));
      }

      unset ($allowedAttr);
   }
   else
   {
        //Attributes are not allowed
   $attrArray = array ();
   }


   //0 = remove ALL BUT defined tags
   //1 = remove ONLY defined tags
   $tagsMethod = 0;

   //0 = remove ALL BUT defined attributes
   //1 = remove ONLY defined attributes
   $attrMethod = 0;

   //1 = remove all identified problem tags (default)
   //0 = turn this feature off
   $xssAutostrip = 1;

   //InputFilter class exists and options are defined, go and initialize
   $inputFilter = new InputFilter($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAutostrip);

   //Clean $_REQUEST
   if (isset ($_REQUEST)) {
	  foreach($_REQUEST as $key=>$value) {	
  		    $_REQUEST[$key] = $inputFilter->process($_REQUEST[$key]);
   	  }
   }

   //Clean $_GET
   if (isset ($_GET)) {
   	foreach($_GET as $key=>$value){	
   			$_GET[$key] = $inputFilter->process($_GET[$key]);
   	}
   }

   //Clean $_POST
   if (isset ($_POST)) {
   		foreach($_POST as $key=>$value){	
   				$_POST[$key] = $inputFilter->process($_POST[$key]);
   		}
   }


   
   //Free memory
   unset ($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAutostrip);
}
?>