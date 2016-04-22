/**
# ################################################################################
# Project:   PHP Link Directory: Version 3.1
#
# **********************************************************************
# Copyright (C) 2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
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
#      on all pages of your directory in you purchased the $25 version
#      of the software.
#
# License holders are entitled to upgrades to the 3.4 branch of the software
# as they are made available at ttp://www.phplinkdirectory.com/
#
# In some cases, license holders may be required to agree to changes
# in the software license before receiving updates to the software.
# **********************************************************************
#
# For questions, help, comments, discussion, etc., please join the
# PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
#
# @link           http://www.phplinkdirectory.com/
# @copyright      2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
# @version        3.1 (=3.0.7)
# ################################################################################
*/


Event.observe(window, 'load', initAdminStyle, false);

function initAdminStyle()
{
   //Highlight all success messages
   allSuccessNodes = document.getElementsByClassName('success');
   for (i = 0; i < allSuccessNodes.length; i++)
   {
      new Effect.Highlight(allSuccessNodes[i],{startcolor:'#009900', endcolor:'#ffffff', duration:1.5});
   }

   /*
   //Highlight all warning messages
   allWarningNodes = document.getElementsByClassName('warning');
   for (i = 0; i < allWarningNodes.length; i++)
   {
      new Effect.Highlight(allWarningNodes[i],{startcolor:'#821517', endcolor:'#ffffff', duration:1.5});
   }
   */

   //Highlight all error messages
   allErrorNodes = document.getElementsByClassName('error');
   for (i = 0; i < allErrorNodes.length; i++)
   {
      new Effect.Highlight(allErrorNodes[i],{startcolor:'#821517', endcolor:'#ffffff', duration:1.5});
   }

   //Highlight all form error messages
   allFormErrorNodes = document.getElementsByClassName('errForm');
   for (i = 0; i < allFormErrorNodes.length; i++)
   {
      new Effect.Highlight(allFormErrorNodes[i],{startcolor:'#821517', endcolor:'#ffffff', duration:1.5});
   }

   return false;
}

function togglesearchbyid()
{
   if ($('searchbyid'))
   {
      if ($('searchbyid').className == '')
      {
         $('searchitemid').value = '';
         $('searchbyid').className = 'hidden';
         $('searchinput').focus();
         new Effect.Highlight('searchinput');
      }
      else
      {
         $('searchbyid').className = '';
         $('searchitemid').focus();
         new Effect.Highlight('searchitemid');
      }
   }

   return false;
}