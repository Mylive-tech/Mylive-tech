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
//error_reporting(E_ALL);
//Load Snoopy class that simulates a browser
require_once 'libs/snoopy/Snoopy.class.php';

$error = 0;

//What output to make,
//form or import results
$showImport = 0;

$urlSI = $db->GetAll("SELECT * FROM `{$tables['submit_item']['name']}` WHERE `FIELD_NAME` = 'URL'");
$descriptionSI = $db->GetAll("SELECT * FROM `{$tables['submit_item']['name']}` WHERE `FIELD_NAME` = 'DESCRIPTION'");

if (count($urlSI) > 0) {
    $tpl->assign('has_url', 1);
}
if (count($descriptionSI) > 0) {
    $tpl->assign('has_description', 1);
}

$link_types = $db->GetAssoc("SELECT `ID`, `NAME`, `FEATURED` FROM `{$tables['link_type']['name']}` WHERE `STATUS` = '2' ORDER BY `ORDER_ID` ASC");
$tpl->assign('link_types', $link_types);

$action = (!empty ($_REQUEST['action']) ? $_REQUEST['action'] : 'default');
$action = strtolower (trim ($action));

//Define Google languages
$langChoice = array ('lang_ar'     => 'Arabic', 'lang_bg'     => 'Bulgarian', 'lang_ca'     => 'Catalan', 'lang_zh-CN'  => 'Chinese (Simplified)',
                     'lang_zh-TW'  => 'Chinese (Traditional)', 'lang_hr'     => 'Croation', 'lang_cs'     => 'Czech', 'lang_da'     => 'Danish',
                     'lang_nl'     => 'Dutch', 'lang_en'      => 'English', 'lang_et'     => 'Estonian', 'lang_fi'     => 'Finnish',
                     'lang_fr'     => 'French', 'lang_de'     => 'German', 'lang_el'     => 'Greek', 'lang_iw'		=>'Hebrew',
		     'lang_hu'     =>'Hungarian', 'lang_is'   => 'Icelandic', 'lang_id' => 'Indonesian', 'lang_it' =>'Italian', 'lang_ja' => 'Japanese',
		     'lang_ko' => 'Korean', 'lang_lv' => 'Latvian', 'lang_lt' => 'Lithuanian', 'lang_no' => 'Norwegian',
		     'lang_pl' => 'Polish', 'lang_pt' => 'Portuguese', 'lang_ro' => 'Romanian', 'lang_ru' => 'Russian',
		     'lang_sr' => 'Serbian', 'lang_sk' => 'Slovak', 'lang_sl' => 'Slovenian', 'lang_es' => 'Spanish',
		     'lang_sv' => 'Swedish', 'lang_tr' => 'Turkish'
                  );
$tpl->assign('langChoice', $langChoice);

//Define results count
$googleResultsCount = array (
                        '10'  => '10 '._L('Results'),
                        '20'  => '20 '._L('Results'),
                        '30'  => '30 '._L('Results'),
                        '50'  => '50 '._L('Results')
                        
                     );
$tpl->assign('googleResultsCount', $googleResultsCount);

//Define Occurrences
$occurrences = array (
                  'any'    => _L('anywhere in the page'),
                  'title'  => _L('in the title of the page'),
                  'body'   => _L('in the text of the page'),
                  'url'    => _L('in the URL of the page'),
                  'links'  => _L('in links to the page')
               );
$tpl->assign('occurrences', $occurrences);

$categID = (isset ($_POST['CATEGORY_ID']) && $_POST['CATEGORY_ID'] > 0 ? intval ($_POST['CATEGORY_ID']) : 0);
$tpl->assign('CATEGORY_ID', $categID);

$linkType = (isset ($_POST['LINK_TYPE']) && $_POST['LINK_TYPE'] > 0 ? intval ($_POST['LINK_TYPE']) : 0);
$tpl->assign('LINK_TYPE', $linkType);


if (isset ($_POST['import']) && isset ($_POST['toimport'])) {
   //Show import page
   $showImport = 1;

   $importErrors  = 0;
   $importSuccess = 0;
   $importSkipped = 0;

   //Was a category selected?
   if ($categID > 0 && $linkType >0) {
      if (ENABLE_PAGERANK == 1) {
         require_once 'include/pagerank.php';
      }

      $currentDate = gmdate ('Y-m-d H:i:s');

      foreach ($_POST['toimport'] as $key) {
         $data = array();
         if (isset($_POST['URL']) && !empty($_POST['URL'])) {
             $url = $_POST['URL'][$key];

             //Correct URL
             if (strlen (trim ($url)) > 0 && !preg_match ('#^http[s]?:\/\/#i', $url))
                $url = "http://".$url;

             //Check first if URL is already available
             $isUnique = checkUrlUnique('link', 'URL', $url, NULL, 'CATEGORY_ID', $categID);
         } else {
             $isUnique = 1;
         }
         if ($isUnique == 1) {
            //Get a new ID
            $id = $db->GenID($tables['link']['name'].'_SEQ');
            $data['ID'] = (!empty ($id) ? intval ($id) : '');

            //Get imported infos
            $data['TITLE'] = $_POST['TITLE'][$key];
            $data['URL']   = $url; //Already clean
            $data['DESCRIPTION'] = $_POST['DESCRIPTION'][$key];
            $data['OWNER_NAME'] = $_POST['OWNER_NAME'][$key];
            $data['OWNER_EMAIL'] = $_POST['OWNER_EMAIL'][$key];

            //Add more options
            $data['CATEGORY_ID'] = $categID;
            $data['LINK_TYPE'] = $linkType;
            $data['STATUS'] = 2;
            $data['VALID']  = 1;
            $data['HITS']   = 0;
            $data['LAST_CHECKED']   = $currentDate;
            $data['DATE_ADDED']     = $currentDate;
            $data['DATE_MODIFIED']  = $currentDate;

            //Calculate PageRank
            if (ENABLE_PAGERANK == 1) {
               $data['PAGERANK'] = get_page_rank($data['URL']);
            }
            $data['PAGERANK'] = (empty ($data['PAGERANK']) || $data['PAGERANK'] < -1 ? -1 : intval ($data['PAGERANK']));
            $data['PAGERANK'] = (empty ($data['PAGERANK']) || $data['PAGERANK'] > 10 ? 10 : intval ($data['PAGERANK']));

            $saveStatus = $db->Replace($tables['link']['name'], $data, 'ID', true);

            if (!$saveStatus)
               $importErrors++;
            else
               $importSuccess++;
         } else {
            $importSkipped++;
         }
		
         
         //empty link_to_import table
         $db->Execute("DELETE FROM {$tables['link_to_import']['name']}");

         //Free memory
         unset ($data, $isUnique, $url);
      }

      $tpl->assign('importErrors'  , $importErrors);
      $tpl->assign('importSuccess' , $importSuccess);
      $tpl->assign('importSkipped' , $importSkipped);
   } elseif ($categID <= 0) {
      //No or wrong category selected
      $error++;
      $errorMsg = _L('Please select a category');
      
      if (AJAX_CAT_SELECTION_METHOD == 0) {
         $categs = get_categs_tree();
         $tpl->assign('categs', $categs);
	  }
	
	  $importResults  = $db->GetAll("SELECT * FROM {$tables['link_to_import']['name']}");
	
	  $tpl->assign('importResults', $importResults);
   } elseif ($linkType <= 0) {
      //No or wrong linktype selected
      $error++;
      $errorMsg = _L('Please select a link type');

      if (AJAX_CAT_SELECTION_METHOD == 0) {
         $categs = get_categs_tree();
         $tpl->assign('categs', $categs);
      }

      $importResults  = $db->GetAll("SELECT * FROM {$tables['link_to_import']['name']}");
      $tpl->assign('importResults', $importResults);
   }

} else {
	if ($error > 0 && $_REQUEST['start'] > 0) {
	   $start = (isset ($_REQUEST['prevStart']) && preg_match ('`^[\d]+$`', $_REQUEST['prevStart']) ? intval ($_REQUEST['prevStart']) : 0);
	} else {
	   $start = (isset ($_REQUEST['start']) && preg_match ('`^[\d]+$`', $_REQUEST['start']) ? intval ($_REQUEST['start']) : 0);
	}
	$tpl->assign('start', $start);
	
	switch ($action) {
	   case 'google'  :
	   	  if (isset($_REQUEST['num']) && ($_SESSION['imported']!=1)) {	
	      	if ($errorMsg == '') {
	      		// reffers to lines 171 - 174
	      		$tpl->assign("show_me", 1);
	      	}
	      	$_SESSION['imported'] = 1;
	   	  } else {
	   	  	$_SESSION['imported'] = 0;
	   	  }
	      $showImport = 1;
	
	      if (AJAX_CAT_SELECTION_METHOD == 0) {
	         $categs = get_categs_tree();
	         $tpl->assign('categs', $categs);
	      }
	
	      $importResults  = $db->GetAll("SELECT * FROM {$tables['link_to_import']['name']}");
	
		  $tpl->assign('importResults', $importResults);
	
	      break;
	
	   //Import from DMOZ
	   case 'dmoz'  :
	      $showImport = 1;
	      if (AJAX_CAT_SELECTION_METHOD == 0) {
	         $categs = get_categs_tree();
	         $tpl->assign('categs', $categs);
	      }
	
	      $importResults = array();
	      //Get import settings
	      $ddata = array();
		  $ddata['impdomain']= '0';
	      $ddata['impdomain'] = (!empty ($_REQUEST['impdomain']) ? 1 : 0);
	      $nextStart = $start + $gdata['num'];
	      $tpl->assign('nextStart', $nextStart);
	
	      //Build Dmoz search URL
	      $dmozAddress = (isset ($_REQUEST['dmozurl']) ? trim ($_REQUEST['dmozurl']) : '');
	      $tpl->assign('dmozAddress', $dmozAddress);
	
	      //Get Google results
	      if (class_exists ('Snoopy')) {
	         //Initialize Snoopy class
	         $snoopy = new Snoopy;
	
	         //Define User Agent we masquerade as
	         $snoopy->agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0';
	
	         //Define HTTP accept types
	         $snoopy->accept = 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,image/jpeg,image/gif,*/*;q=0.5';
	
	         //Fetch content of the web page
	         $fetchResults = $snoopy->fetch($dmozAddress);
	
	         if ($fetchResults === false) {
	            //Could not fetch URL
	            //Possible library missing or an error occured
	            //Reciprocal link validation failed
	            $responseContent = '';
	         }
	
	         //Get Content
	         $responseContent = (is_string ($snoopy->results) && !empty ($snoopy->results) ? $snoopy->results : '');
	      } else {
	         //Use simple results fetching
	         $handle = @ fopen ($dmozAddress, 'r');
	         $responseContent = '';
	
	         while (!feof ($handle)) {
	            $responseContent .= @ fread ($handle, 8192);
	         }
	
	         @ fclose ($handle);
	      }

	      if (empty ($responseContent)) {
	         //Error while fetching results page
	         $error++;
	         $errorMsg = _L('Could not fetch Dmoz results page').'!';
	      } else {
	         $cleanContent = explode ('ul class="directory-url" style="margin-left:0;"', $responseContent);
	         $resultParts  = explode ("<li>", $cleanContent[1]);
	         //Remove junk
	         if (isset ($resultParts[0]))
	            unset ($resultParts[0]);
	
	         $i = 0;
	         foreach ($resultParts as $key => $result) {
	            //Get URL
	            $u1 = explode ('<a href="', $result);
	            $u2 = explode ('">', $u1[1]);
	            $importResults[$i]['URL'] = un_escape(clean_string($u2[0]));
	
	            $domainName = parseDomain($importResults[$i]['URL']);
	
	            $importResults[$i]['URL'] = ($ddata['impdomain'] == 1 ? parseDomain($importResults[$i]['URL'], 1) : $importResults[$i]['URL']);
	
	            //Correct URL
	            if (strlen (trim ($importResults[$i]['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $importResults[$i]['URL']))
	               $importResults[$i]['URL'] = "http://".$importResults[$i]['URL'];
	
	            //Get title
	            $t1 = explode ('">', $result);
	            $t2 = explode ('</a>', $t1[1]);
	            $importResults[$i]['TITLE'] = un_escape(clean_string($t2[0]));
	
	            //Get description
	            $d1 = explode (' - ', $result);
			    $d2 = explode ('</li>', $d1[1]);
			    $importResults[$i]['DESCRIPTION'] = un_escape(clean_string($d2[0]));
	
	            //Build author name [domain name??]
	            $importResults[$i]['OWNER_NAME'] = $domainName;
	
	            //Build author email [webmaster@domain]
	            $importResults[$i]['OWNER_EMAIL'] = 'webmaster@'.$domainName;
	
	            $i++;
	         }
	         $tpl->assign('importResults', $importResults);
	         $tpl->assign('for_dmoz', 1);
	      }
	      break;
	
	   case 'default' :
	   default        :

            //RALUCA: JQuery validation related
            $validators = array(
                'rules' => array(
                        'as_q' => array(
                                'required' => true,
                        )
                )
            );
            $vld = json_custom_encode($validators);
            $tpl->assign('validators', $vld);

            $validator = new Validator($validators);
            //RALUCA: end of JQuery validation related

	   if (empty ($_POST['submit'])) {
	   	  //empty link_to_import table
	      $db->Execute("DELETE FROM {$tables['link_to_import']['name']}");
	      
	   	  //Define default crawl values
	      $gdata = array();
	      $gdata['lr']       = 'lang_en';
	      
	      $gdata['num']        = 10;
	      $gdata['as_occt']    = 'any';
	      $gdata['firstlevel'] = 1;
	      $gdata['impdomain']  = 1;
	      $gdata['safesearch'] = 1;
	   } else {
	      $gdata = array();
	      $gdata['lr']       = (isset ($_POST['lr']) && array_key_exists ($_POST['lr'], $langChoice) ? $_POST['lr'] : 'lang_en');
	      $gdata['as_q']       = (!empty ($_POST['as_q']) ? clean_string($_POST['as_q']) : '');
	      $gdata['as_eq']      = (!empty ($_POST['as_eq']) ? clean_string($_POST['as_eq']) : '');
	      $gdata['num']        = (array_key_exists ($_POST['num'], $googleResultsCount) ? $_POST['num'] : 10);
	      $gdata['as_occt']    = (array_key_exists ($_POST['as_occt'], $occurrences) ? $_POST['as_occt'] : 'any');
	      $gdata['firstlevel'] = (isset ($_POST['firstlevel']) ? 1 : 0);
	      $gdata['impdomain']  = (isset ($_POST['impdomain']) ? 1 : 0);
	      $gdata['safesearch'] = (isset ($_POST['safesearch']) ? 1 : 0);
	      //Clean multiple white spaces
	      $gdata['as_q'] = preg_replace ('#[\s]+#i', ' ', $gdata['as_q']);
	      $gdata['as_q'] = clean_string($_REQUEST['as_q']);
	      $gdata['as_eq'] = preg_replace ('#[\s]+#i', ' ', $gdata['as_eq']);

	      //RALUCA: JQuery validation related - server side.
               $validator = new Validator($validators);
               $validator_res = $validator->validate($_POST);
               //RALUCA: end of JQuery validation related - server side.
               if (empty($validator_res))
               {
	         $redirectURL = DOC_ROOT.'/spider.php?action=google&num='.$gdata['num'].'&as_q='.$gdata['as_q'].'&lr='.$gdata['lr'];
	         //Build URL variables [encoded]
	         foreach ($gdata as $key => $value)
	            $redirectURL .= '&amp;'.$key.'='.urlencode ($value);
		
			 $_SESSION['imported'] = 0;
	         http_custom_redirect($redirectURL);
	      }
	   } 
	   
	   $tpl->assign('gdata', $gdata);
	   break;
	}
}

$tpl->assign('action', $action);
$tpl->assign('error', $error);
if (!empty ($errorMsg))
   $tpl->assign('errorMsg', $errorMsg);

$tpl->assign('showImport', $showImport);


$content = $tpl->fetch(ADMIN_TEMPLATE.'/spider.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

if (!function_exists ('un_escape')) {
   /**
    * Un-Escape a string
    * @param  string Escaped string
    * @return string Unescaped string
    */
   function un_escape($string='') {
      $trans_tbl = get_html_translation_table (HTML_ENTITIES);
      $trans_tbl = array_flip ($trans_tbl);
      return strtr ($string, $trans_tbl);
   }
}
?>