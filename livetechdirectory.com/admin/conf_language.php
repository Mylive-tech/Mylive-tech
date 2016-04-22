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

//tweak_memory_limit(16);

$error = 0;
$errorMsg = '';

$action = (isset ($_REQUEST['action']) ? $_REQUEST['action'] : '');
$tpl->assign('action', $action);

switch (strtolower ($action))
{
   //Edit a language file using a second master language
   case 'edit' :
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $getLang1 = $_POST['lang'];
		}else{
		 $getLang1 = $_GET['lang'];
		}

      $tpl->assign('getLang1', $getLang1);
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $getLang2 = $_POST['lang2'];
		}else{
		 $getLang2 = $_GET['lang2'];
		}
      $tpl->assign('getLang2', $getLang2);


      if ($getLang1 == $getLang2)
      {
         $error++;
         $tpl->assign('error', $error);
         $tpl->assign('errorMsg', _L('Languages must be different!'));

         $content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_language.tpl');
         $tpl->assign('content', $content);

         //Clean whitespace
         $tpl->load_filter('output', 'trimwhitespace');

         //Make output
         echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
         exit();
      }

      $lang1Filename = "../lang/{$getLang1}.php";
      $lang2Filename = "../lang/{$getLang2}.php";

      //Load lang1
      $lang1 = array();
      if (file_exists ($lang1Filename))
      {
         @ include ($lang1Filename);
         if (is_array ($__LANG))
         {
            $lang1 = $__LANG;
            unset($__LANG);
         }
      }
      $tpl->assign('lang1', $lang1);

      //Load lang2
      $lang2 = array();
      if (file_exists ($lang2Filename))
      {
         @ include ($lang2Filename);
         if (is_array ($__LANG))
         {
            $lang2 = $__LANG;
            unset($__LANG);
         }
      }
      $tpl->assign('lang2', $lang2);

      break;

   //Edit phrases of language files
   case "simpleedit":
	   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $getLang = $_POST['lang'];
		}else{
		 $getLang = $_GET['lang'];
		}
     
      $tpl->assign('getLang', $getLang);

      $tpl->assign('hash', '');

      $langFilename = "../lang/{$getLang}.php";

      $langInfo = language_file_data(INSTALL_PATH."/lang/{$getLang}.php");
      $tpl->assign('langInfo', $langInfo);

      //Load lang
      $lang = array();
      if (file_exists ($langFilename))
      {
         @ include ($langFilename);
         if (is_array ($__LANG))
         {
            $lang = $__LANG;
            unset($__LANG);
         }
      }
      $tpl->assign('lang', $lang);
      break;

   //Add a phrase to a language file
   case "addphrase":
     	   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $getLang = $_POST['lang'];
		}else{
		 $getLang = $_GET['lang'];
		}
      $tpl->assign('getLang', $getLang);
//echo $getlang;
      $tpl->assign('hash', '');

      $langFilename = "../lang/{$getLang}.php";

      $langInfo = language_file_data(INSTALL_PATH."/lang/{$getLang}.php");
      $tpl->assign('langInfo', $langInfo);
//print_r($langInfo);
      //Load lang
      $lang = array();
      if (file_exists ($langFilename))
      {
          include ($langFilename);
         if (is_array ($__LANG))
         {
            $lang = $__LANG;
            unset($__LANG);
         }
      }
	//  print_r($lang);
      $tpl->assign('lang', $lang);
      break;

   default :
      $languages = select_lang();
      $tpl->assign('languages', $languages);

      $notWriteAble = array();

      foreach ($languages as $langPrefix => $lang)
      {
         if (!is_writeable ("../lang/{$langPrefix}.php"))
         {
            $notWriteAble[] = $langPrefix;
         }
      }

      if (is_array ($notWriteAble) && !empty ($notWriteAble))
      {
         $error++;
         $errorMsg  = _L('Following language files are not writeable and cannot be edited').': ';

         foreach ($notWriteAble as $langPrefix)
            $errorMsg .= '/lang/'.$langPrefix.'.php, ';

      }

      break;
}

$tpl->assign('error', $error);
$tpl->assign('errorMsg', $errorMsg);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_language.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>