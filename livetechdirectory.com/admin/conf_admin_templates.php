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

$available_templates = get_templates('../templates/Core/', 'install');

$current_template_path = (defined ('ADMIN_TEMPLATE') && ADMIN_TEMPLATE != '' ? ADMIN_TEMPLATE : 'DefaultAdmin');
$current_template      = array ();
foreach ($available_templates as $key => $template)
{
   if (strtolower ($current_template_path) == strtolower ($template['theme_path']))
   {
      $current_template = $template;
      unset ($available_templates[$key], $template);
   }
}

if ($_REQUEST['action'] == 'activate')
{
   $activate_template = urldecode (trim ($_REQUEST['template'] ));
   $enable_template   = array ();
   $template_valid    = 0;
   $status            = 0;

   foreach ($available_templates as $key => $template)
   {
      if (strtolower ($activate_template) == strtolower ($template['theme_path']))
      {
         $enable_template = $template;
         $template_valid  = 1;
      }
   }

   if ($template_valid == 1)
   {
      $data             = array ();
      $data['ID']       = 'ADMIN_TEMPLATE';
      $data['VALUE']    = $enable_template['theme_path'];
      if ($db->Replace($tables['config']['name'], $data, 'ID', true))
      {
         //Clear the entire cache
         $tpl->clear_all_cache();

         //Clear all compiled template files
         $tpl->clear_compiled_tpl();

         $title_msg = _L('Settings updated.');
         $cust_msg  = '<p align="left">'._L('Selected template').': <strong>'.$enable_template['theme_name'].'</strong></p>';
         $status    = 1;
      }
      else
      {
         $title_msg = _L('An error occured while saving.');
         $cust_msg  = $db->ErrorMsg();
         $status    = -1;
      }
   }
   else
   {
      $title_msg = _L('Selected template not valid!');
      $cust_msg  = '<p align="left"><strong>'._L('Please select a valid template.').'</strong></p>';
      $status    = -1;
   }

   $url       = DOC_ROOT."/conf_admin_templates.php?r=1";
   $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, $status);
   $tpl->assign('redirect', $redirect);
}

//Check if preview thumbnails can be created
if (!extension_loaded ('gd'))
{
   //No GD library available => no preview
   $showPreview = 0;
   $thumbType = 0;
}
else
{
   if (!function_exists ('gd_info'))
   {
      //GD info cannot be gathered => no preview
      $showPreview = 0;
      $thumbType   = 0;
   }
   else
   {
      //Get GD info
      $gGdInfo = gd_info();

      if ($gGdInfo['PNG Support'] == 1)
      {
         //PNG support available [best]
         $showPreview = 1;
         $thumbType   = 3;
      }
      elseif ($gGdInfo['JPG Support'] == 1)
      {
         //JPG support available [good]
         $showPreview = 1;
         $thumbType   = 2;
      }
      elseif ($gGdInfo['GIF Create Support'] == 1)
      {
         //GIF support available [it's ok]
         $showPreview = 1;
         $thumbType   = 1;
      }
      else
      {
         //No PNG, JPG or GIF support => no preview
         $showPreview = 0;
         $thumbType   = 0;
      }
   }
}

$tpl->assign('showPreview', $showPreview);
$tpl->assign('thumbType'  , $thumbType);

$tpl->assign('available_templates', $available_templates);
$tpl->assign('current_template'     , $current_template);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_admin_templates.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>