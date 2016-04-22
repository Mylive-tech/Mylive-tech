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
 * Count all database executions
 *
 * @param object $db The connection object
 * @param string $sql SQL query
 * @param array $inputarray Input array
 * @return null
 */
function &CountExecs($db, $sql, $inputarray)
{
   global $EXECS;

   //echo "<p>{$sql}</p>";

	if (!is_array ($inputarray))
	  $EXECS++;
	elseif (is_array (reset ($inputarray)))
	  $EXECS += sizeof ($inputarray);
	else
	  $EXECS++;

	//Return a value by reference
	$null = null;
	return $null;
}

/**
 * Count all cached database executions
 *
 * @param object $db The connection object
 * @param integer $secs2cache Seconds to cache data
 * @param string $sql SQL query
 * @param array $inputarray Input array
 * @return null
 */
function CountCachedExecs($db, $secs2cache, $sql, $inputarray)
{
   global $CACHED;
   $CACHED++;
}




/**
 * Extend AdoDB's cache feature
 *
 */
class phpLDAdoCache extends ADOConnection
{
   var $countErrors = 0;

   /**
    * Remove expired database cache files (adodb_*.cache)
    * Automatically runs daily CRON job to clear all cache.
    *
    * @param string $cacheDir AdoDB cache folder
    * @return bool TRUE on success, FALSE on error
    */
 public static function phpld_ExpiredCacheFlush($cacheDir='')
   {
      global $ADODB_CACHE_DIR;

      //Define directory where cache files are stored
      //Use default AdoDB cache dir variable or alternatively a parameter
      $directory = (!empty ($ADODB_CACHE_DIR) ? $ADODB_CACHE_DIR : $cacheDir);
      if (empty ($directory))
      {
         return false;
      }
      else
      {
         //Strip trailing slash
         if ('/' == substr ($directory, -1, 1))
         {
            $directory = substr ($directory, 0, -1);
         }
      }

      //Try daily cron job to flush complete cache
      if (phpLDAdoCache::_phpld_cronFlush(TIMENOW))
      {
         //Complete cache flushed
         return true;
      }

/**
 * DEACTIVATED as it times out on high traffic sites and is rather unefficient

      //Clear only expired cache files
      phpLDAdoCache::_phpld_ExpiredCacheFlush($directory);

      //Clear file status cache
      clearstatcache();
*/

      return true;
   }

   /**
    * Run daily CRON job to flush entire DB cache
    *
    * @access private
    * @param integer $timenow Current UNIX timestamp of current
    * @return bool TRUE if cache was flushed, FALSE if not
    */
 public static function _phpld_cronFlush($timenow=0)
   {
      global $db, $tables, $phpldSettings;

      if  ($phpldSettings['DB_CACHING'] == 0)
      {
         //CRON job disabled
         return false;
      }

      if (empty ($timenow))
      {
         $timenow = TIMENOW;
      }
      $timenow = (int)$timenow;

      if (mktime (date ('G', $timenow), 0, 0, date('m', $timenow), date('d', $timenow), date('Y', $timenow)) > mktime ($phpldSettings['CRON_ADOCACHEFLUSH_HOUR'], 0, 0, date('m', $phpldSettings['CRON_ADOCLEARCACHE_LASTRUN']), date('d', $phpldSettings['CRON_ADOCLEARCACHE_LASTRUN'])+1, date('Y', $phpldSettings['CRON_ADOCLEARCACHE_LASTRUN'])))
      {
         //Clear all database cache
         $db->CacheFlush();

         //Update last run date of flush cache
         $updated = $db->Execute("UPDATE `{$tables['config']['name']}` SET `VALUE` = ".$db->qstr($timenow)." WHERE `ID` = 'CRON_ADOCLEARCACHE_LASTRUN'");

         return true;
      }

      return false;
   }

   /**
    * Private function to walk cache dir, check files and eventually remove them if expired
    *
    * @access private
    * @param string $path Start path of folder where AdoDB stores cache files
    */
   public static function _phpld_ExpiredCacheFlush($path)
   {
      global $db;

      $dh = @ opendir ($path);

      if (false === $dh)
      {
         return false;
      }

      while ($file = readdir ($dh))
      {
         if ('.' == $file || '..' == $file)
         {
            //it's not a file nor a directory
            continue;
         }

         if (is_dir ($path.'/'.$file))
         {
            //walk deeper as this is a directory
            phpLDAdoCache::_phpld_ExpiredCacheFlush($path.'/'.$file);
            $rmdir = @ rmdir ($path.'/'.$file);
         }
         elseif (is_file ($path.'/'.$file))
         {
            //it's a file, check last modified date
            $lastmod = @ filemtime ($path.'/'.$file);

            //Delete expired cache file
            if (TIMENOW - $lastmod >= $db->cacheSecs)
               @ unlink ($path.'/'.$file);

            phpLDAdoCache::_phpld_ExpiredCacheFlush($path);
         }
      }

      @ closedir($dh);
   }
}
?>