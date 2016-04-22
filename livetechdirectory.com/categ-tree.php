<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 

/**
 * #########################################################################################################
 * # Project:     PHP Link Directory - Link exchange directory @ http://www.phplinkdirectory.com/
 * # Module:      [AJAX Category Selection] @ http://www.frozenminds.com/phpld-ajax-categories.html
 * # Author:      Constantin Bejenaru aKa Boby @ http://www.frozenminds.com/
 * # Language:    AJAX (Based on Prototype and Scriptaculous libraries)
 * # License:     MIT (Copyright (c) 2006 Constantin Bejenaru - http://www.frozenminds.com)
 * # Version:     1.1
 * # Notice:      Please maintain this section
 * #########################################################################################################
 **/

require_once 'include/config.php';
require_once 'include/settings.php';
require_once 'include/tables.php';
require_once 'include/validation_functions.php';


require_once 'include/adodb_extender.php';
require_once 'libs/intsmarty/intsmarty.class.php';

//Connect to database
$db = ADONewConnection(DB_DRIVER);
if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
{
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   //Load extenders to count executions
   $db->fnExecute = 'CountExecs';
   $db->fnCacheExecute = 'CountCachedExecs';
}
else
{
   define('ERROR', 'ERROR_DB_CONNECT');
   exit('ERROR :: Could not connect to database server!');
}

$tpl = &get_tpl();

function build_tree($id=0)
{
   global $db, $tables;
   static $categs = array ();

   $categs = $db->CacheGetAll("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = ".$db->qstr($id)." AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

   return $categs;
}

//Determin category ID to build tree on
$categID = trim (htmlspecialchars ($_GET['categID']));
$categID = (!empty ($categID) && preg_match ('`^[\d]+$`', $categID) ? intval ($categID) : 0);
$categID = ($categID > 0 ? $categID : 0);

$submit = $_REQUEST['submit'];

//Determin action
$action = (!empty ($_REQUEST['action']) ? trim (htmlspecialchars ($_REQUEST['action'])) : 'categtree');

$error = 0;

switch ($action)
{
   case 'titleupdate' :
      if ($categID < 1)
         $output .= _L('Please select a category!');
      else {
      	 $closed_l = $db->CacheGetOne("SELECT `CLOSED_TO_LINKS` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($categID));
      	 if ($submit == "link" && $closed_l == 1) {
      		 $output .= _L('Please select a category!');
      		 $categID = 0;
      	 } else	
         	$output .= getCategoryTitleByID($categID);
      }	
      $tpl->assign('CategoryTitle', $output);
      break;

   case 'categtree' :
   default :
      //Get categories
      $categoryList = build_tree($categID);

      //Determine parent ID of current category for going one step back/up
      $parentID = $db->CacheGetOne("SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($categID));
      $parentID = (!empty ($parentID) ? $parentID : 0);
      $tpl->assign('parentID', $parentID);

      if (!empty ($categoryList))
      {
         //Check for subcategories
         foreach ($categoryList as $key => $category)
         {
            $countSubcats = $db->CacheGetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = ".$db->qstr($category['ID'])." AND `STATUS` = '2' AND `SYMBOLIC` != '1'");
            $categoryList[$key]['SUBCATEGS'] = (!empty ($countSubcats) ? 1 : 0);
         }
         $tpl->assign('categoryList', $categoryList);
      }
      else
      {
         $error = 1;
      }

      //Free Memory
      unset ($categoryList);
      break;
}

$tpl->assign('categID', $categID);
$tpl->assign('action', $action);

$tpl->assign('error_cat_tree', $error);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('views/_shared/category_tree.tpl');
flush();
?>