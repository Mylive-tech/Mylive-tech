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

// Disable any caching by the browser
disable_browser_cache();

//Determine page URL
$pageURL = $db->GetOne("SELECT `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` = ".$db->qstr('SITE_URL'));

if (!preg_match ('#^http[s]?:\/\/#i', $pageURL))
   $pageURL = 'http://'.$pageURL;
if (substr ($pageURL, -1) == "/")
   $pageURL = substr ($pageURL, 0, -1);

define ('PAGE_URL', $pageURL);

//Check for gzip support
if (!function_exists ('gzencode'))
   $gzipSupport = 0;
else
   $gzipSupport = 1;

//Determine action to take
$action = (isset ($_REQUEST['action']) && !empty ($_REQUEST['action']) ? strtolower (trim ($_REQUEST['action'])) : '');
$tpl->assign('action', $action);

//Determine start point for next cycle
$start = (preg_match ('`^[\d]+$`', $_REQUEST['start']) && $_REQUEST['start'] > 0 ? intval ($_REQUEST['start']) : 0);

//Determine range (how many items to process per cycle)
$range = (preg_match ('`^[\d]+$`', $_REQUEST['range']) && $_REQUEST['range'] > 0 ? intval ($_REQUEST['range']) : 200);

//Determine total categs
$totalc = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `STATUS` = '2'");

//Determine total processed items
$processed = (preg_match ('`^[\d]+$`', $_REQUEST['processed']) && $_REQUEST['processed'] > 0 ? intval ($_REQUEST['processed']) : 0);

//Determine or get new current time
$timestamp = (!empty ($_REQUEST['timestamp']) ? trim ($_REQUEST['timestamp']) : TIMENOW);

//Build and determine Google sitemap timeformat
$timeformatList = array ('long' => _L('Long format (with time)') , 'short' => _L('Short format (date only)'));
$timeformat     = (!empty ($_REQUEST['timeformat']) && strtolower ( trim ($_REQUEST['timeformat'])) == 'long' ? 'long' : 'short');

//Determine if paging URLs are created
$paging = (isset ($_REQUEST['paging']) && $_REQUEST['paging'] == 1 ? 1 : 0);

//Build and determine Google sitemap URL last modification date
$lastmodList   = array (
                     'disabled' => _L('Disabled'),
                     'auto'     => _L('Automatically').' ('._L('Default').')',
                     'now'      => _L('Current time')
                  );
$lastmod      = (!empty ($_REQUEST['lastmod']) && array_key_exists ($_REQUEST['lastmod'], $priorityList) ? strtolower ($_REQUEST['lastmod']) : 'auto');

//Build and determine Google sitemap URL priority
$priorityList   = array (
                     '0.0' => '0.0',
                     '0.1' => '0.1',
                     '0.2' => '0.2',
                     '0.3' => '0.3',
                     '0.4' => '0.4',
                     '0.5' => '0.5'.' ('._L('Default').')',
                     '0.6' => '0.6',
                     '0.7' => '0.7',
                     '0.8' => '0.8',
                     '0.9' => '0.9',
                     '1.0' => '1.0',
                     'disabled' => _L('Disabled')
                  );
$priority      = (!empty ($_REQUEST['priority']) && array_key_exists ($_REQUEST['priority'], $priorityList) ? strtolower ($_REQUEST['priority']) : '0.5');

//Build and determine Google sitemap URL change frequency
$changefreqList = array (
                     'disabled' => _L('Disabled'),
                     'always'   => _L('Always'),
                     'hourly'   => _L('Hourly'),
                     'daily'    => _L('Daily'),
                     'weekly'   => _L('Weekly'),
                     'monthly'  => _L('Monthly'),
                     'yearly'   => _L('Yearly'),
                     'never'    => _L('Never')
                  );
$changefreq    = (!empty ($_REQUEST['changefreq']) && array_key_exists ($_REQUEST['changefreq'], $changefreqList) ? strtolower ($_REQUEST['changefreq']) : 'disabled');

//Determine file compression request
$googleCompressFile = (isset ($_REQUEST['googleCompressFile']) && $_REQUEST['googleCompressFile'] == 1 && $gzipSupport == 1 ? 1 : 0);
$yahooCompressFile  = (isset ($_REQUEST['yahooCompressFile'])  && $_REQUEST['yahooCompressFile']  == 1 && $gzipSupport == 1 ? 1 : 0);

//Determine Google Ping request
$pingGoogle = (isset ($_REQUEST['pingGoogle']) && $_REQUEST['pingGoogle'] == 1 ? 1 : 0);

//Determine Google sitemap filename, full server path and URL to file
$googleSitemapFileName = 'sitemap.xml';
$tpl->assign('googleSitemapFileName', $googleSitemapFileName);
if ($googleCompressFile == 1)
{
   $googleCompressedSitemapFileName = (substr ($googleSitemapFileName, -3) !== '.gz' ? $googleSitemapFileName.'.gz' : $googleSitemapFileName);
   $tpl->assign('googleCompressedSitemapFileName', $googleCompressedSitemapFileName);
}

$googleSitemapFile = INSTALL_PATH.$googleSitemapFileName;
$tpl->assign('googleSitemapFile', $googleSitemapFile);
$googleWwwSitemapFile = SITE_URL.$googleSitemapFileName;
$tpl->assign('googleWwwSitemapFile', $googleWwwSitemapFile);
$googleFileValidation = check_file($googleSitemapFile, 'google', 0);
$tpl->assign('googleFileValidation', $googleFileValidation);

if ($googleCompressFile == 1)
{
   $googleCompressedSitemapFile = INSTALL_PATH.$googleCompressedSitemapFileName;
   $tpl->assign('googleCompressedSitemapFile', $googleCompressedSitemapFile);
   $googleWwwCompressedSitemapFile = SITE_URL.$googleCompressedSitemapFileName;
   $tpl->assign('googleWwwCompressedSitemapFile', $googleWwwCompressedSitemapFile);
   $googleCompressedFileValidation = check_file($googleCompressedSitemapFile, 'google', 1);
   $tpl->assign('googleCompressedFileValidation', $googleCompressedFileValidation);
}

//Determine Yahoo! sitemap filename, full server path and URL to file
$yahooSitemapFileName = 'urllist.txt';
$tpl->assign('yahooSitemapFileName', $yahooSitemapFileName);
if ($yahooCompressFile == 1)
{
   $yahooCompressedSitemapFileName = (substr ($yahooSitemapFileName, -3) !== '.gz' ? $yahooSitemapFileName.'.gz' : $yahooSitemapFileName);
   $tpl->assign('yahooCompressedSitemapFileName', $yahooCompressedSitemapFileName);
}

$yahooSitemapFile = INSTALL_PATH.$yahooSitemapFileName;
$tpl->assign('yahooSitemapFile', $yahooSitemapFile);
$yahooWwwSitemapFile = SITE_URL.$yahooSitemapFileName;
$tpl->assign('yahooWwwSitemapFile', $yahooWwwSitemapFile);
$yahooFileValidation = check_file($yahooSitemapFile, 'yahoo', 0);
$tpl->assign('yahooFileValidation', $yahooFileValidation);

if ($yahooCompressFile == 1)
{
   $yahooCompressedSitemapFile = INSTALL_PATH.$yahooCompressedSitemapFileName;
   $tpl->assign('yahooCompressedSitemapFile', $yahooCompressedSitemapFile);
   $yahooWwwCompressedSitemapFile = SITE_URL.$yahooCompressedSitemapFileName;
   $tpl->assign('yahooWwwCompressedSitemapFile', $yahooWwwCompressedSitemapFile);
   $yahooCompressedFileValidation = check_file($yahooCompressedSitemapFile, 'yahoo', 1);
   $tpl->assign('yahooCompressedFileValidation', $yahooCompressedFileValidation);
}

switch ($action)
{
   case 'build'   :
      $createGoogleSitemap = ($googleFileValidation !== false && !is_string ($googleFileValidation) ? 1 : 0);
      $createYahooSitemap  = ($yahooFileValidation  !== false && !is_string ($yahooFileValidation)  ? 1 : 0);

      if ($createGoogleSitemap == 0 && $createYahooSitemap == 0)
      {
         //None of the two files are valid, no sitemap is created
         http_custom_redirect(DOC_ROOT.'/conf_sitemap.php?r=1');
      }

      //Get categories
      $sql = "SELECT `ID`, `CACHE_URL`, `DATE_ADDED` AS `DATE_MODIFIED` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' ORDER BY `ID` ASC LIMIT {$start}, {$range}";
      $categories = $db->GetAll($sql);
      $categCollection = new Phpld_Model_Collection('Model_Category_Entity');
      $categCollection->setElements($categories);


      //Check DB results
      if (!is_array ($categories) || empty ($categories))
      {
         $tpl->assign('error', 2);
         $tpl->assign('sql_error', $db->ErrorMsg());
         $content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_sitemap.tpl');
         $tpl->assign('content', $content);
         //Clean whitespace
         $tpl->load_filter('output', 'trimwhitespace');
         //Make output
         echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
         exit ();
      }
      else
      {
         $thisSessionProcessed = 0;

         if ($start == 0)
         {
            if ($createGoogleSitemap == 1)
            {
               //Write XML header
               //Write XML header
               $XMLheader  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
               $XMLheader .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9  http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
               $XMLheader .= "<!-- Created by PHP Link Directory version ".CURRENT_VERSION." -->\n";

               if (!write_to_file($googleSitemapFile, $XMLheader, true, false))
               {
                  $tpl->assign('error', 1);
                  $tpl->assign('errorMsg', _L('Could not write to file').": {$googleSitemapFile}");
                  $content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_sitemap.tpl');
                  $tpl->assign('content', $content);
                  //Clean whitespace
                  $tpl->load_filter('output', 'trimwhitespace');
                  //Make output
                  echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
                  exit ();
               }
               unset ($XMLheader);
            }

            if ($createYahooSitemap == 1)
            {
               $header = '';

               if (!write_to_file($yahooSitemapFile, '', true, false))
               {
                  $tpl->assign('error', 1);
                  $tpl->assign('errorMsg', _L('Could not write to file').": {$yahooSitemapFile}");
                  $content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_sitemap.tpl');
                  $tpl->assign('content', $content);
                  //Clean whitespace
                  $tpl->load_filter('output', 'trimwhitespace');
                  //Make output
                  echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
                  exit ();
               }
               unset ($header);
            }
         }

         if ($paging == 1)
         {
            //Add paging
            foreach ($categories as $key => $categ)
            {
               //Count links in current category
               $countLinks = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` = ".$db->qstr($categ['ID'])." AND `STATUS` = '2' AND (`EXPIRY_DATE` >= ".$db->DBDate(time())." OR `EXPIRY_DATE` IS NULL) AND `FEATURED` = '0'");
               //Determine how many links are per page
               $linksPerPage = (defined ('PAGER_LPP') && PAGER_LPP > 0 ? intval (PAGER_LPP) : 10);
               //Determine how many pages current category has
               $pages = ceil ($countLinks / $linksPerPage);

               //More than one page
               $categories[$key]['PAGES'] = $pages;
            }
         }

         $googleSitemapString = '';
         $yahooSitemapString  = '';

         //Loop through each category page and write to sitemap file
         foreach ($categCollection as $categ)
         {
            //Build location (for both Google and Yahoo!)
            $buildLoc = PAGE_URL.$categ->sgetUrl();
//die($categ->getUrl());
            if ($createYahooSitemap == 1)
            {
               $yahooSitemapString .= $buildLoc."\n";
            }

            if ($createGoogleSitemap == 1)
            {
               $googleSitemapString .= "\t<url>\n";

               //Build Google location
               $googleSitemapString .= "\t\t<loc>".xml_utf8_encode($buildLoc)."</loc>\n";

               //Build Google last modification date
               switch ($lastmod)
               {
                  case 'auto'     :
                     $buildLastmod = googleDate($categ['DATE_MODIFIED'], $timeformat, true);

                     if (!empty ($buildLastmod))
                        $lastmodXML = "\t\t<lastmod>".xml_utf8_encode($buildLastmod)."</lastmod>\n";

                     break;
                  case 'now'      :
                     $buildLastmod = googleDate($timestamp, $timeformat, false);

                     if (!empty ($buildLastmod))
                        $lastmodXML = "\t\t<lastmod>".xml_utf8_encode($buildLastmod)."</lastmod>\n";

                     break;
                  case 'disabled' :
                  default         :
                     $lastmodXML = '';
                     break;
               }
               $googleSitemapString .= $lastmodXML;

               //Build Google change frequency
               if ($changefreq != 'disabled')
                  $changefreqXML = "\t\t<changefreq>".xml_utf8_encode($changefreq)."</changefreq>\n";
               else
                  $changefreqXML = '';

               $googleSitemapString .= $changefreqXML;

               //Build Google priority
               if ($priority != 'disabled')
                  $priorityXML = "\t\t<priority>".xml_utf8_encode($priority)."</priority>\n";
               else
                  $priorityXML = '';

               $googleSitemapString .= $priorityXML;

               $googleSitemapString .= "\t</url>\n";
            }

            if (isset ($categ['PAGES']) && $categ['PAGES'] > 1)
            {
               //Build each page URL for specific category
               for ($i=1; $i <= $categ['PAGES']; $i++)
               {
                  if (defined ('ENABLE_REWRITE') && ENABLE_REWRITE == 1)
                     $buildPageLoc = $buildLoc."page-{$i}.html";
                  else
                     $buildPageLoc = $buildLoc.((strpos ($buildLoc, '?') === false) ? '?' : '&')."p={$i}";

                  if ($createYahooSitemap == 1)
                     $yahooSitemapString .= $buildPageLoc."\n";

                  if ($createGoogleSitemap == 1)
                  {
                     $googleSitemapString .= "\t<url>\n";
                     //Build Google location
                     $googleSitemapString .= "\t\t<loc>".xml_utf8_encode($buildPageLoc)."</loc>\n";
                     $googleSitemapString .= $lastmodXML;
                     $googleSitemapString .= $changefreqXML;
                     $googleSitemapString .= $priorityXML;
                     $googleSitemapString .= "\t</url>\n";
                  }
               }
            }

            unset ($categories[$key], $lastmodXML, $changefreqXML, $priorityXML);//Free memory

            //Increment processed items value
            $processed++;
            $thisSessionProcessed++;
         }

         //Write to file(s)
         if ($createGoogleSitemap == 1 && !empty ($googleSitemapString))
         {
			  $links = $db->GetAll ("SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' ORDER BY ID ASC LIMIT {$start}, {$range}");

             $listingsCollection = new Phpld_Model_Collection('Model_Link_Entity');
             $listingsCollection->setElements($links);
         //Loop through each category page and write to sitemap file
         foreach ($listingsCollection as $link)
         {
		 $details = PAGE_URL.$link->sgetUrl();
               switch ($lastmod)
               {
                  case 'auto'     :
                     $buildLastmod = googleDate($link['DATE_MODIFIED'], $timeformat, true);

                     if (!empty ($buildLastmod))
                        $lastmodXML = "\t\t<lastmod>".xml_utf8_encode($buildLastmod)."</lastmod>\n";

                     break;
                  case 'now'      :
                     $buildLastmod = googleDate($timestamp, $timeformat, false);

                     if (!empty ($buildLastmod))
                        $lastmodXML = "\t\t<lastmod>".xml_utf8_encode($buildLastmod)."</lastmod>\n";

                     break;
                  case 'disabled' :
                  default         :
                     $lastmodXML = '';
                     break;
               }
               //Build Google change frequency
               if ($changefreq != 'disabled')
                  $changefreqXML = "\t\t<changefreq>".xml_utf8_encode($changefreq)."</changefreq>\n";
               else
                  $changefreqXML = '';
               if ($priority != 'disabled')
                  $priorityXML = "\t\t<priority>".xml_utf8_encode($priority)."</priority>\n";
               else
                  $priorityXML = '';			  
				  		
                  if ($createYahooSitemap == 1) $yahooSitemapString .= $details."\n";
                  if ($createGoogleSitemap == 1)
                  {
                     $googleSitemapString .= "\t<url>\n";
                     //Build Google location
                     $googleSitemapString .= "\t\t<loc>".xml_utf8_encode($details)."</loc>\n";
                     $googleSitemapString .= $lastmodXML;
                     $googleSitemapString .= $changefreqXML;
                     $googleSitemapString .= $priorityXML;
                     $googleSitemapString .= "\t</url>\n";
                  }
				  unset ($details, $links, $lastmodXML, $changefreqXML, $priorityXML);//Free memory
}		 


            if (!write_to_file($googleSitemapFile, $googleSitemapString, false, true))
            {
               $tpl->assign('error', 1);
               $tpl->assign('errorMsg', _L('Could not write to file').": {$googleSitemapFile}");
               $content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_sitemap.tpl');
               $tpl->assign('content', $content);
               //Clean whitespace
               $tpl->load_filter('output', 'trimwhitespace');
               //Make output
               echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
               exit ();
            }
         }
         unset ($googleSitemapString);

         if ($createYahooSitemap == 1 && !empty ($yahooSitemapString))
         {
            if (!write_to_file($yahooSitemapFile, $yahooSitemapString, false, true))
            {
               $tpl->assign('error', 1);
               $tpl->assign('errorMsg', _L('Could not write to file').": {$yahooSitemapFile}");
               $content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_sitemap.tpl');
               $tpl->assign('content', $content);
               //Clean whitespace
               $tpl->load_filter('output', 'trimwhitespace');
               //Make output
               echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
               exit ();
            }
         }
         unset ($yahooSitemapString);

         $nextStart = $start + $range;

         //End reached
         if ($processed >= $totalc || $nextStart >= $totalc)
         {
            if ($createYahooSitemap == 1)
            {
               $yahooSitemapCreated = 1;
               $tpl->assign('yahooSitemapCreated', 1);

               //Check if file is larger than 10MB or compression required (10485760 bytes)
               if ((file_exists ($yahooSitemapFile) && filesize ($yahooSitemapFile) >= 10485760) || $yahooCompressFile == 1)
               {
                  $handle = @ fopen ($yahooSitemapFile, "r");

                  if ($handle !== false)
                  {
                     //GZIP compress content of regular file
                     $yahooCompressedSitemapString =  @ gzencode (fread ($handle, filesize ($yahooSitemapFile)));

                     //Write compressed content to file
                     if (!write_to_file($yahooCompressedSitemapFile, $yahooCompressedSitemapString, true, false))
                     {
                        //An error occured
                        $yahooCompressedSitemapCreated = 0;
                     }
                     else
                     {
                        //Success
                        $yahooCompressedSitemapCreated = 1;
                     }

                     $tpl->assign('yahooCompressedSitemapCreated', $yahooCompressedSitemapCreated);
                     unset ($yahooCompressedSitemapString);

                     @ fclose ($handle);
                  }
               }
            }

            if ($createGoogleSitemap == 1)
            {
               $XMLfooter = '</urlset>';

               //Write XML footer
               if (!write_to_file($googleSitemapFile, $XMLfooter, false, true))
               {
                  $tpl->assign('error', 1);
                  $tpl->assign('errorMsg', _L('Could not write to file').": {$googleSitemapFileName}");
                  $content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_sitemap.tpl');
                  $tpl->assign('content', $content);
                  //Clean whitespace
                  $tpl->load_filter('output', 'trimwhitespace');
                  //Make output
                  echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
                  exit ();
               }
               else
               {
                  $googleSitemapCreated = 1;
                  $tpl->assign('googleSitemapCreated', 1);
               }
               unset ($XMLfooter);

               //Check if file is larger than 10MB or compression required (10485760 bytes)
               if ((file_exists ($googleSitemapFile) && filesize ($googleSitemapFile) >= 10485760) || $googleCompressFile == 1)
               {
                  $handle = @ fopen ($googleSitemapFile, "r");

                  if ($handle !== false)
                  {
                     //GZIP compress content of regular file
                     $googleCompressedSitemapString =  @ gzencode (fread ($handle, filesize ($googleSitemapFile)));

                     //Write compressed content to file
                     if (!write_to_file($googleCompressedSitemapFile, $googleCompressedSitemapString, true, false))
                     {
                        //An error occured
                        $googleCompressedSitemapCreated = 0;
                     }
                     else
                     {
                        //Success
                        $googleCompressedSitemapCreated = 1;
                     }

                     $tpl->assign('googleCompressedSitemapCreated', $googleCompressedSitemapCreated);
                     unset ($googleCompressedSitemapString);

                     @ fclose ($handle);
                  }
               }


               if (isset ($googleSitemapCreated) && $googleSitemapCreated == 1)
               {
                  $tpl->assign('googleSitemapCreated', 1);

                  //Ping Google
                  if ($pingGoogle == 1)
                  {
                     //Build ping URL
                     $pingURL = 'http://www.google.com/webmasters/sitemaps/ping?sitemap='.urlencode ($googleCompressFile == 1 && isset ($googleCompressedSitemapCreated) && $googleCompressedSitemapCreated == 1 ? $googleWwwCompressedSitemapFile : $googleWwwSitemapFile);
                     $pingResult = remote_fopen($pingURL); //Send ping

                     //Get ping result
                     if ($pingResult == null || $pingResult === false)
                     {
                        $pingResult  = 0;
                        $pingMessage = '<a href="'.$pingURL.'" title="'._L('Read Google response in a new window').'" target="_blank">'._L('Could not ping to Google').'</a>';
                     }
                     else
                     {
                        $pingResult  = 1;
                        $pingMessage = '<a href="'.$pingURL.'" title="'._L('Read Google response in a new window').'" target="_blank">'._L('Successfully pinged Google').'</a>';
                     }

                     $tpl->assign('pingResult' , $pingResult);
                     $tpl->assign('pingMessage', $pingMessage);
                  }
               }
            }

            $content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_sitemap.tpl');
            $tpl->assign('content', $content);
            //Clean whitespace
            $tpl->load_filter('output', 'trimwhitespace');
            //Make output
            echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
            exit ();
         }

         //Build redirect URL
         $redirectURL  = DOC_ROOT.'/conf_sitemap.php?action=build';
         $redirectURL .= (isset ($nextStart) ? '&start='.$nextStart : '');
         $redirectURL .= (!empty ($processed) ? '&processed='.$processed : '');
         $redirectURL .= (!empty ($range) ? '&range='.$range : '');
         $redirectURL .= ($pingGoogle == 1     ? '&pingGoogle=1' : '');
         $redirectURL .= ($paging == 1 ? '&paging=1' : '');
         $redirectURL .= (!empty ($timestamp)  ? '&timestamp='.$timestamp : '');
         $redirectURL .= (!empty ($lastmod)    ? '&lastmod='.urlencode ($lastmod) : '');
         $redirectURL .= (!empty ($timeformat) ? '&timeformat='.urlencode ($timeformat) : '');
         $redirectURL .= (!empty ($priority)   ? '&priority='.urlencode ($priority) : '');
         $redirectURL .= (!empty ($changefreq) ? '&changefreq='.urlencode ($changefreq) : '');
         $redirectURL .= ($googleCompressFile == 1 ? '&googleCompressFile=1' : '');
         $redirectURL .= ($yahooCompressFile  == 1 ? '&yahooCompressFile=1'  : '');

         $build_type = _L('Building Sitemaps');

         $cust_msg  .= '<p>'._L('Starting at URL').': <span class="important">'.$start.'</span></p>';
         $cust_msg  .= '<p>'._L('Stopping at URL').': <span class="important">'.$nextStart.'</span></p>';
         $cust_msg  .= '<p>'._L('Queries performed (this session/total sessions)').': <span class="important">'.$thisSessionProcessed.'/'.$processed.'</span></p>';
         $cust_msg  .= '<p>'._L('Total queries to process').': <span class="important">'.$totalc.'</span></p>';
         $cust_msg  .= '<p>'._L('Number of processes per cycle').': <span class="important">'.$range.'</span></p>';

         $cust_msg  .= '<p class="notice">'._L('Depending on the size of information to be processed, this action can take some time.').'</p>';

         $redirect = javascript_redirect($redirectURL, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg);
         $tpl->assign('redirect', $redirect);
      }

      break;

   case 'default' :
   default        :

      //Assign range for cycles
      $tpl->assign('range', $range);
      //Assign Google Ping option
      $tpl->assign('pingGoogle'       , $pingGoogle);
      //Assign compression option
      $tpl->assign('googleCompressFile', $googleCompressFile);
      $tpl->assign('yahooCompressFile' , $yahooCompressFile );
      //Assign options and option lists for Google Sitemap
      $tpl->assign('timestamp'     , $timestamp     );
      $tpl->assign('lastmodList'   , $lastmodList   );
      $tpl->assign('lastmod'       , $lastmod       );
      $tpl->assign('timeformatList', $timeformatList);
      $tpl->assign('timeformat'    , $timeformat    );
      $tpl->assign('priorityList'  , $priorityList  );
      $tpl->assign('priority'      , $priority      );
      $tpl->assign('changefreqList', $changefreqList);
      $tpl->assign('changefreq'    , $changefreq    );
      //Assign Gzip support
      $tpl->assign('gzipSupport'   , $gzipSupport   );
      $tpl->assign('paging'        , $paging        );

      if ($googleFileValidation !== true && $yahooFileValidation !== true)
      {
         //None of the two files are valid, no submit button
         $tpl->assign('nosubmit', 1);
      }

      if (!empty ($_POST['submit']))
      {
         //Build redirect URL
         $redirectURL  = DOC_ROOT.'/conf_sitemap.php?action=build&start=0&processed=0&range='.$range;
         $redirectURL .= ($pingGoogle == 1     ? '&pingGoogle=1' : '');
         $redirectURL .= ($paging == 1         ? '&paging=1' : '');
         $redirectURL .= (!empty ($timestamp)  ? '&timestamp='.$timestamp : '');
         $redirectURL .= (!empty ($lastmod)    ? '&lastmod='.urlencode ($lastmod) : '');
         $redirectURL .= (!empty ($timeformat) ? '&timeformat='.urlencode ($timeformat) : '');
         $redirectURL .= (!empty ($priority)   ? '&priority='.urlencode ($priority) : '');
         $redirectURL .= (!empty ($changefreq) ? '&changefreq='.urlencode ($changefreq) : '');
         $redirectURL .= ($googleCompressFile == 1 ? '&googleCompressFile=1' : '');
         $redirectURL .= ($yahooCompressFile  == 1 ? '&yahooCompressFile=1'  : '');

         //Redirect to action page
         http_custom_redirect($redirectURL);
      }

      break;
}

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_sitemap.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

//Clear compiled template file
$tpl->clear_compiled_tpl('admin/conf_sitemap.tpl');


/**
 * Check if sitemap file is valid (exists, readable, writeable)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * @param  string Unix timestamp or any string date if third parameter is TRUE
 * @param  string Google date format (long/short)
 * @param  boolean TRUE if first paramete is not a Unix timestamp
 */
function googleDate($timestamp='', $timeformat='short', $parseToTimestamp=true)
{
   $timestamp  = trim ($timestamp);
   $timeformat = trim ($timeformat);

   $googleTimestamp = ''; //Unix timestamp (dummy variable)
   $googleDate      = ''; //Google date (returned value)

   //If a date was passed via parameter and not a UNIX timestamp, change it
   if ($parseToTimestamp == true)
   {
      if (($googleTimestamp = strtotime ($timestamp)) === false)
         return ''; //Could not parse date
   }

   //Short date format (Year-Month-Day)
   $googleDate = date ('Y-m-d', $googleTimestamp);

   if ($timeformat == 'long')
   {
      //Long date format, adding time (Hour:Minutes:Seconds)
      $googleDate .= 'T' . date ('H:i:s', $googleTimestamp) . 'Z';
   }

   return $googleDate;
}

/**
 * Check if sitemap file is valid (exists, readable, writeable)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * @param  string Filename to be checked
 * @param  string Specific tests for either Google or Yahoo! sitemap file
 */
function check_file($filename, $engine='google', $compressed=0)
{
   if (!isset ($filename) || empty ($filename))
      return _L('No file to check!');

   $engine = strtolower ($engine);

   if (!file_exists ($filename))
      return _L('File does not exist!');//Check if file exists
   elseif (!is_readable ($filename))
      return _L('File is not readable!');//Check if readable
   elseif (!is_writable ($filename))
      return _L('File is not writeable! Try "chmod 666 filename".');//Check if writable

   if ($compressed == 1)
   {
      //Check for Gzip extension
      if (substr ($filename, -3) !== '.gz')
         return _L('No GZIP extension! Required ".gz"');
   }
   else
   {
      //Check if has valid extension
      if ($engine == 'google' && substr ($filename, -4) !== '.xml')
         return _L('No XML extension! Required ".gz"');
      elseif ($engine == 'yahoo' && substr ($filename, -11) !== 'urllist.txt')
         return _L('No valid Yahoo sitemap name! Required "urllist.txt"');
   }

   //Check if file can be opened
   if (!$file = @ fopen ($filename, 'r'))
      return _L('File could not be opened!');

   @ fclose ($file);

   clearstatcache();

   return true;
}
?>