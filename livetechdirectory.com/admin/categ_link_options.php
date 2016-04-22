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

//Clear compiled template file
$tpl->clear_compiled_tpl('categ_link_options.tpl');

//Define no errors
$tpl->assign('error', 0);

//Determine category ID
$categID = (isset ($_REQUEST['id']) && preg_match ('`^[\d]+$`', $_REQUEST['id']) ? intval ($_REQUEST['id']) : '');

//Correct category ID
$categID = ($categID < 1 ? '' : $categID);
$tpl->assign('categID', $categID);

//Determine display type
$type = (!empty ($_REQUEST['type']) ? strtolower (trim ($_REQUEST['type'])) : 'default');

//Determine if only content is requested, no extra HTML
//AJAX only
$onlyContent = (isset ($_REQUEST['onlycontent']) && $_REQUEST['onlycontent'] == 1 ? 1 : 0);

if (empty ($categID))
{
   //No link was passed via URL
   $tpl->assign('noCategID', 1);
}
else
{
   //Get full link information
   $sql = "SELECT C.*, ".$db->IfNull('P.TITLE', "'Top'")." AS `PARENT` FROM `{$tables['category']['name']}` AS `C` LEFT OUTER JOIN `{$tables['category']['name']}` AS `P` ON (C.PARENT_ID = P.ID) WHERE C.ID = ".$db->qstr($categID)." LIMIT 1";
   $categInfo = $db->GetRow($sql);

   $categInfo['TOTAL_SUBCATEGS']   = get_sub_categories($categID);
   $categInfo['TOTAL_SUBCATEGS'][] = $categID;


   if (!$categInfo)
   {
      //Invalid category submitted or SQL error occured
      $tpl->assign('error', 1);
      $tpl->assign('sql_error', $db->ErrorMsg());
   }
   else
   {
      $tpl->assign('categInfo', $categInfo);

      switch ($type)
      {
         case 'ajax' :
            //Clean whitespace
            $tpl->load_filter('output', 'trimwhitespace');

            $content = $tpl->fetch(ADMIN_TEMPLATE.'/categ_link_options.tpl');

            if ($onlyContent == 1)
            {
               //Only basic content is shown
               echo $content;
            }
            else
            {
               $tpl->assign('content', $content);

               //Make complete output
               echo $tpl->fetch(ADMIN_TEMPLATE.'/ajax-main.tpl');
            }

            break;
         case 'default' :
         default :
            $content = $tpl->fetch(ADMIN_TEMPLATE.'/categ_link_options.tpl');
            $tpl->assign('content', $content);

            //Clean whitespace
            $tpl->load_filter('output', 'trimwhitespace');

            //Make output
            echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

            break;
      }
   }
}
?>
