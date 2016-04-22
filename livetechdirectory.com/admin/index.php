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
$currentVersion = (defined ('CURRENT_VERSION') && CURRENT_VERSION > 2 ? CURRENT_VERSION : 5.0);



$url = get_url('http://www.phplinkdirectory.com/current5.php?simpleversion='.round ($currentVersion), URL_CONTENT, $_SERVER['SERVER_NAME'].request_uri());
$latestVersion = (isset ($url['content']) && !empty ($url['content']) ? trim ($url['content']) : 0);


//Check if latest version could be determined
if (!isset ($url['content']) || empty ($url['content']))
{
   //Could not determine latest version
   $version = _L('Could not determine latest version.');
   $tpl->assign('update_available', 1);
}
else
{
   //Determined latest version successfully,
   //compare against current version
   if (version_compare ($latestVersion, $currentVersion) == 1)
   {
      //Update is available
      $version = _L('A new version (##VERSION##) is available.');
      $version = str_replace('##VERSION##', $latestVersion, $version);
      $tpl->assign('update_available', 1);
   }
   else
   {
      //No update available
      $version = _L('Your installation is up to date, no updates are available for your version of PHP Link Directory.');
      $tpl->assign('update_available', 0);
   }
}

//Check phpLD installation for security risks
if (!defined ('BYPASS_SECURITY_WARNINGS') || BYPASS_SECURITY_WARNINGS == 0)
{
   //Define security warnings array
   $security_warnings = array ();

   //Check if installer is still available
   $installer = 'install/index.php';
   if (is_file (INSTALL_PATH.$installer))
   {
      $installer_msg = _L('Installer is still available. This poses a major security risk, please remove ').$installer._L(' file immediately!'.
                          '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').'<a href="http://www.phplinkdirectory.com/kb/Installer_Still_Writable_Error_Message.html" target="_blank">'._L('[more info]').'</a>';
      $security_warnings[]    = str_replace('##INSTALLER##', '<code>'.$installer.'</code>', $installer_msg);
      unset ($installer_msg, $installer);
   }

   //Check if config file is still writeable
   $config_file = 'include/config.php';
   if (is_writable (INSTALL_PATH.$config_file))
      @ chmod ($config_file, 0755);

   if (is_writable (INSTALL_PATH.$config_file))
   {
      $config_msg = _L('The configuration file is still writable by the user the webserver runs under. This poses a security risk, please drop write permissions for ').$config_file.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.phplinkdirectory.com/kb/Configuration_File_Write_Permissions.html" target="_blank">'._L('[more info]').'</a>';
      $security_warnings[]    = str_replace('##CONFIGFILE##', '<code>'.$config_file.'</code>', $config_msg);
      unset ($config_msg, $config_file);
   }

   //check quotes qnd global
   
//   $quotes = ini_get ('magic_quotes_gpc');
   $globals = ini_get('register_globals');
   
   if (!$quotes && $globals) {
      $config_msg = _L('Attention: register_globals = On' );
      $security_warnings[]    = $config_msg;
      unset ($config_msg);
   }
   
   //

   //Security check
   if (!empty ($security_warnings))
      $tpl->assign('security_warnings', $security_warnings);

   //Free memory
   unset ($security_warnings);
}

//Directory warnings
$directory_warnings = array();




//Check if category cache is available
$countCategs = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}`");
$countNoCache = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `CACHE_TITLE` IS NULL OR CHAR_LENGTH(CACHE_TITLE) < 1 OR `CACHE_URL` IS NULL OR CHAR_LENGTH(CACHE_URL) < 1");
$countLinks = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}`");
$countLinkNoCache = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `CACHE_URL` IS NULL OR CHAR_LENGTH(CACHE_URL) < 1 AND `STATUS` = '2'");
if(CATS_COUNT == "1"){
      $countActiveLinks = $db->GetOne("SELECT DISTINCT  COUNT(*)
                     FROM
                        ((SELECT DISTINCT `{$tables['link']['name']}`.ID AS LID, `{$tables['link']['name']}`.CATEGORY_ID, `{$tables['category']['name']}`.TITLE
			FROM `{$tables['link']['name']}`
			LEFT JOIN `{$tables['additional_category']['name']}` ON `{$tables['link']['name']}`.id = LINK_ID
			LEFT JOIN `{$tables['category']['name']}` ON  (`{$tables['category']['name']}`.ID = `{$tables['additional_category']['name']}`.CATEGORY_ID  or `{$tables['category']['name']}`.ID = `{$tables['link']['name']}`.CATEGORY_ID) 
                           WHERE (`{$tables['link']['name']}`.STATUS = 2 AND `{$tables['link']['name']}`.CATEGORY_ID != 0  AND `{$tables['category']['name']}`.TITLE IS NOT NULL ) )
                        UNION
                        (SELECT DISTINCT LINK_ID AS LID, `{$tables['additional_category']['name']}`.CATEGORY_ID, `{$tables['category']['name']}`.TITLE
			FROM `{$tables['link']['name']}`
                        RIGHT JOIN `{$tables['additional_category']['name']}` ON `{$tables['link']['name']}`.id = LINK_ID
			RIGHT JOIN `{$tables['category']['name']}` ON  (`{$tables['category']['name']}`.ID = `{$tables['additional_category']['name']}`.CATEGORY_ID or `{$tables['category']['name']}`.ID = `{$tables['link']['name']}`.CATEGORY_ID)  
                        WHERE `{$tables['link']['name']}`.STATUS = 2    AND `{$tables['link']['name']}`.CATEGORY_ID != 0  AND `{$tables['category']['name']}`.TITLE IS NOT NULL)) AS t1  ");
      
      
      $countNonTopCategs = $db->GetOne("SELECT COUNT(*)
                     FROM `{$tables['category']['name']}` where PARENT_ID != '0'");
      
      $TotalCount = (int)$countActiveLinks + (int)$countNonTopCategs;
                        
      $countSumCategs = $db->GetRow("SELECT SUM(LINK_COUNT) as LK_COUNT,   SUM(COUNT) as TOTAL_COUNT
                     FROM `{$tables['category']['name']}`");
      

      if (!empty ($countActiveLinks) && !empty ($countSumCategs['LK_COUNT']) && $countActiveLinks != $countSumCategs['LK_COUNT'])
      {
         $warning_msg = _L('Your category link count is not complete! ').'<a href="'.DOC_ROOT.'/calculate_counts.php?r=1" title="Start category link count building">'._L('Update category link count').'</a>'._L(' page to do so');
         $directory_warnings[]    = str_replace (array ('##START-COUNT-URL##', '##CATEGS-URL##'), array (DOC_ROOT.'/calculate_counts.php?r=1' , DOC_ROOT.'/dir_categs.php'), $warning_msg);
      }
      
      if (!empty ($TotalCount) && !empty ($countSumCategs['TOTAL_COUNT']) && $TotalCount != $countSumCategs['TOTAL_COUNT'])
      {
         $warning_msg = _L('Your category count is not complete! ').'<a href="'.DOC_ROOT.'/calculate_counts.php?r=1" title="Start category count building">'._L('Update category count').'</a>'._L(' page to do so');
         $directory_warnings[]    = str_replace (array ('##START-COUNT-URL##', '##CATEGS-URL##'), array (DOC_ROOT.'/calculate_counts.php?r=1' , DOC_ROOT.'/dir_categs.php'), $warning_msg);
      }


}

if (!empty ($countNoCache) && !empty ($countCategs) && $countNoCache <= $countCategs)
{
   $warning_msg = _L('Your category cache is not complete! ').'<a href="'.DOC_ROOT.'/dir_categs.php?action=rebuild_cache" title="Start category cache building">'._L('Start').'</a>'._L(' re-building your category cache now, or use the action button found on the ').'<a href="'.DOC_ROOT.'/dir_categs.php" title="Browse categories page">'._L('categories').'</a>'._L(' page to do so.');
   $directory_warnings[]    = str_replace (array ('##START-CACHE-URL##', '##CATEGS-URL##'), array (DOC_ROOT.'/dir_categs.php?action=rebuild_cache' , DOC_ROOT.'/dir_categs.php'), $warning_msg);
}

if (!empty ($countLinkNoCache) && !empty ($countLinks) && $countLinkNoCache <= $countLinks)
{
   $warning_msg = _L('Your link url cache is not complete! Please use the action button found on the ').'<a href="'.DOC_ROOT.'/update_link_urls.php?r=1" title="update link cache url">'._L('update link cache url').'</a>'._L(' page to do so.');
   $directory_warnings[]    = str_replace (array ('##START-CACHE-URL##', '##CATEGS-URL##'), array (DOC_ROOT.'/dir_categs.php?action=rebuild_cache' , DOC_ROOT.'/dir_categs.php'), $warning_msg);
}

//Directory check
if (!empty ($directory_warnings))
   $tpl->assign('directory_warnings', $directory_warnings);

//Free memory
unset ($directory_warnings, $countCategs, $countNoCache, $countLinkNoCache, $countLinks);

//Directory statistics
$stats[0] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` > '1'");
$stats[1] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '1'");
$stats[2] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '0'");

$stats[3] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['comment']['name']}` WHERE `STATUS` = '2' AND `TYPE` = '1'");
$stats[4] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['comment']['name']}` WHERE `STATUS` < '2' AND `TYPE` = '1'");

$stats[8] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['comment']['name']}` WHERE `STATUS` = '2' AND `TYPE` = '2'");
$stats[9] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['comment']['name']}` WHERE `STATUS` < '2' AND `TYPE` = '2'");

$stats[10] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}`");
//$stats[4] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['email']['name']}`");
//$stats[5] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['email_tpl']['name']}`");

//phpLinkDirectory News
if (ENABLE_NEWS)
{
   $url = get_url("http://www.phplinkdirectory.com/news.php", URL_CONTENT);
   if ($url['status'])
   {
      $news = parse_news($url['content']);
      $tpl->assign('news', $news);
   }
}

$tpl->assign('stats', $stats);
$tpl->assign('version', $version);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/index.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Compress output for faster loading
if (COMPRESS_OUTPUT == 1)
   $tpl->load_filter('output', 'CompressOutput');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');



?>