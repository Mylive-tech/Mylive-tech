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
 * Get all information of a category by providing it's ID
 * @param integet Category ID
 * @return array An array with all informations of a category
 */
function getCategoryByID($cid=0)
{
   global $db, $tables;

   $cid = (preg_match ('`^[\d]+$`', $cid) ? intval ($cid) : 0);

   if (empty ($cid))
      return false;
   else
      $output = $db->CacheGetRow("SELECT * FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($cid));

   if (!is_array ($output) || empty ($output))
      return false;

   return $output;
}

function getPageByID($id)
{
   global $db, $tables;

   if (empty ($id))
      return false;
   else
      $output = $db->CacheGetRow("SELECT * FROM `{$tables['page']['name']}` WHERE `ID` = ".$db->qstr($id));

   if (!is_array ($output) || empty ($output))
      return false;

   return $output;
}


/**
 * Get title of a category by providing it's ID
 * @param integet Category ID
 * @return string Title of category
 */
function getCategoryTitleByID($cid=0)
{
   global $db, $tables;

   $cid = (preg_match ('`^[\d]+$`', $cid) ? intval ($cid) : 0);

   if (empty ($cid))
      return false;
   else
      $output = $db->CacheGetOne("SELECT `TITLE` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($cid));

   if (empty ($output))
      return false;

   return $output;
}

/**
 * Get all information of a link by providing it's ID
 * @param integet Link ID
 * @return array An array with all informations of a link
 */
function getLinkByID($lid=0)
{
   global $db, $tables;

   $lid = (preg_match ('`^[\d]+$`', $lid) ? intval ($lid) : 0);

   if (empty ($lid))
      return false;
   else
      $output = $db->CacheGetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($lid));

   if (!is_array ($output) || empty ($output))
      return false;

   return $output;
}


function addCategPathToLinks($links)
{
   global $db, $tables;

   if (is_array ($links) && !empty ($links))
   {
      foreach ($links as $key => $link)
      {
         $categCache = $db->CacheGetRow("SELECT `CACHE_TITLE`, `CACHE_URL` FROM {$tables['category']['name']} WHERE `ID` = ".$db->qstr($link['CATEGORY_ID']));

         //Build category URL
         $links[$key]['CATEGORY_URL']   = (!empty ($categCache['CACHE_URL']) ? $categCache['CACHE_URL'] : buildCategUrl($link['CATEGORY_ID']));
         //Build category title
         $links[$key]['CATEGORY_TITLE'] = (!empty ($categCache['CACHE_TITLE']) ? $categCache['CACHE_TITLE'] : buildCategUrlTitle($link['CATEGORY_ID']));
         //Free memory
         unset ($link, $categCache);
      }
   }

   return $links;
}

?>