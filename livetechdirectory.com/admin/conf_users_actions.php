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

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
    $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

$tpl->assign('ENABLE_REWRITE', ENABLE_REWRITE);

if ($_REQUEST['action']) {
    list ($action, $id) = explode(':', $_REQUEST['action']);
    
    $action = strtoupper (trim ($action));
    $id     = ($id < 0  ? 0 : intval ($id));
    
    $tpl->assign('action', strtoupper ($action));
}

$tpl->assign('yes_no', array (0 => _L('No'), 1 => _L('Yes')));

$level = get_user_level($id);

$tpl->assign('level', $level);

//default actions from db

$r = $db->GetAll("SELECT * FROM `{$tables['user_default_actions']['name']}` WHERE `LEVEL_ID` = '0' OR `LEVEL_ID` = ".$db->qstr($level)." ORDER BY `LEVEL_ID` ASC");

$default = array();
for ($i=0; $i<count($r); $i++) {
    $default[$r[$i]['LEVEL_ID']][] = $r[$i];
}

//actual user actions from db
$r = $db->GetAll("SELECT * FROM `{$tables['user_default_actions']['name']}` AS `default` INNER JOIN `{$tables['user_actions']['name']}` AS `actual` ON `default`.`ID` = `actual`.`ACTION_ID` WHERE `actual`.`USER_ID` = $db->qstr($id) ORDER BY `default`.`LEVEL_ID` ASC");
$actual = array();
for ($i=0; $i<count($r); $i++) {
    $actual[$r[$i]['ACTION_ID']] = $r[$i]['VALUE'];
}

//
//array of user types
$tpl->assign('user_types', array (0 => ('Regular User'), 1 => _L('Administrator'), 2 => _L('Editor'), 3 => _L('Super Editor')));


switch($action)
{
    case 'C' :
        if (isset ($_SESSION['return']))
             http_custom_redirect($_SESSION['return']);
        break;
    default :
        if(empty($_POST['submit'])){
           $tpl->assign('submit_session', registerAdminSubmitSession());
        } else {
        	  checkAdminSubmitSession(clean_string($_POST['submit_session']));
        	  $tpl->assign('submit_session', registerAdminSubmitSession());
  
            foreach ($default as $key=>$value) {
                for ($i=0; $i<count($value); $i++) {
                    if (isset($_POST[$value[$i]['ID']])) {
                        if (($_POST[$value[$i]['ID']] != $value[$i]['VALUE']) || (isset($actual[$value[$i]['ID']]) && $_POST[$value[$i]['ID']] != $actual[$value[$i]['ID']])) {
                            //setting value
                            //(action value, action id, user id)
                            
                            set_user_action($_POST[$value[$i]['ID']], $value[$i]['ID'], $id);
							//default actions from db
                            
                            $r = $db->GetAll("SELECT * FROM `{$tables['user_default_actions']['name']}` ORDER BY `LEVEL_ID` ASC");
                            $def = array();
                            for ($k=0; $k<count($r); $k++) {
                                $def[$r[$k]['LEVEL_ID']][] = $r[$k];
                            }

                            //actual user actions from db
                            $r = $db->GetAll("SELECT * FROM `{$tables['user_default_actions']['name']}` AS `default` INNER JOIN `{$tables['user_actions']['name']}` AS `actual` ON `default`.`ID` = `actual`.`ACTION_ID` WHERE `actual`.`USER_ID` = $db->qstr($id) ORDER BY `default`.`LEVEL_ID` ASC");
                            $act = array();
                            for ($k=0; $k<count($r); $k++) {
                                $act[$r[$k]['ACTION_ID']] = $r[$k]['VALUE'];
                            }

                        } else {
                            remove_user_action($value[$i]['ID'], $id);
                        }
                    } else {
                        if ($value[$i]['VALUE'] != 0 ||  (isset($actual[$value[$i]['ID']]) && $actual[$value[$i]['ID']]!= 0)) {
                            set_user_action(0, $value[$i]['ID'], $id);
                            //default actions from db
                            $r = $db->GetAll("SELECT * FROM `{$tables['user_default_actions']['name']}` ORDER BY `LEVEL_ID` ASC");
                            $def = array();
                            for ($k=0; $k<count($r); $k++) {
                                $def[$r[$k]['LEVEL_ID']][] = $r[$k];
                            }
                            //actual user actions from db
                            $r = $db->GetAll("SELECT * FROM `{$tables['user_default_actions']['name']}` AS `default` INNER JOIN `{$tables['user_actions']['name']}` AS `actual` ON `default`.`ID` = `actual`.`ACTION_ID` WHERE `actual`.`USER_ID` = $db->qstr($id) ORDER BY `default`.`LEVEL_ID` ASC");
                            $act = array();
                            for ($k=0; $k<count($r); $k++) {
                                $act[$r[$k]['ACTION_ID']] = $r[$k]['VALUE'];
                            }
                        } else {
                            remove_user_action($value[$i]['ID'], $id);
                        }
                    }
                }
            }
            $default = $def;
            
            
            $actual = $act;
        }
    break;
}

$tpl->assign('languages', select_lang('../lang/'));
$tpl->assign('u', $id);


$categs = get_grant_categs_tree();
$tpl->assign('categs', $categs);

if ($_SESSION['phpld']['adminpanel']['is_admin'])
{
    $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` WHERE `USER_ID`=".$db->qstr($id));
    
    $page = get_page($list_total);
    $tpl->assign('list_limit', LINKS_PER_PAGE);
    $tpl->assign('list_total', $list_total);
    $sql = "SELECT U.*, ".$db->IfNull('C.TITLE', "'Top'")." AS CATEGORY FROM `{$tables['user_permission']['name']}` AS U LEFT OUTER JOIN `{$tables['category']['name']}` AS C ON U.CATEGORY_ID = C.ID WHERE `USER_ID`=".$db->qstr($id)." {$orderBy}";
    $rs = $db->SelectLimit($sql , LINKS_PER_PAGE, LINKS_PER_PAGE * ($page -1));
    $list = $rs->GetAssoc(true);
    // Go through each link to get category path
    foreach($list as $category => $category_row)
        $list[$category]['CATEGORY_PATH'] = get_path($list[$category]['CATEGORY_ID']);
        
    $tpl->assign('list', $list);
}
else
{
    $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` {$where} AND (".str_replace("ID","CATEGORY_ID",$_SESSION['phpld']['adminpanel']['grant_permission']).")");
    $page = get_page($list_total);
    $tpl->assign('list_limit', LINKS_PER_PAGE);
    $tpl->assign('list_total', $list_total);
    $sql = "SELECT U.*, ".$db->IfNull('C.TITLE', "'Top'")." AS CATEGORY FROM {$tables['user_permission']['name']} AS U LEFT OUTER JOIN {$tables['category']['name']} AS C ON U.CATEGORY_ID = C.ID WHERE `USER_ID`='".$id."' AND (".str_replace("ID","U.CATEGORY_ID",$_SESSION['phpld']['adminpanel']['grant_permission']).") {$orderBy}";
    $rs = $db->SelectLimit($sql , LINKS_PER_PAGE, LINKS_PER_PAGE * ($page -1));
    $list = $rs->GetAssoc(true);
    // Go through each link to get category path
    foreach($list as $category => $category_row)
        $list[$category]['CATEGORY_PATH'] = get_path($list[$category]['CATEGORY_ID']);

    $tpl->assign('list', $list);
}
$columns = array ('CATEGORY' => _L('Category')  , 'CATEGORY_PATH' => _L('Category Path'));
$tpl->assign('columns', $columns);

///raluca

$tpl->assign('actual', $actual);
$tpl->assign('default', $default);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_users_actions.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Compress output for faster loading
if (COMPRESS_OUTPUT == 1)
   $tpl->load_filter('output', 'CompressOutput');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>