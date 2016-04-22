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

if ($_REQUEST['action'])
{
   list ($action, $id, $val) = explode(':', $_REQUEST['action']);

   $action = strtoupper (trim ($action));
   $id     = ($id < 0  ? 0 : intval ($id));

   $tpl->assign('action', strtoupper ($action));
}

//Check if warn form was submitted and change action
if (isset ($_REQUEST['warn']) && $_REQUEST['warn'] == 1)
{
   if (!empty ($_REQUEST['CATEGORY_ID']))
   {
      if (!empty ($_REQUEST['submit']))
      {
         $action = 'A';
         $id     = intval ($_REQUEST['CATEGORY_ID']);
         $tpl->assign('action', $action);
      }
      elseif (!empty ($_REQUEST['cancel']))
      {
         $action = 'C';
         $id     = intval ($_REQUEST['CATEGORY_ID']);
         $tpl->assign('action', $action);
      }
   }
}

unset ($u);
$u = (isset ($_REQUEST['u']) && !empty ($_REQUEST['u']) ? intval ($_REQUEST['u']) : '');
$tpl->assign('u', $u);

if (!empty ($u))
   $where = " WHERE `USER_ID` = ".$db->qstr($u);
else
   $where = '';

$tpl->assign('ENABLE_REWRITE', ENABLE_REWRITE);
$tpl->assign('admin_user'         , array (0          => ('Regular User'), 1               => _L('Administrator'), 2 => _L('Editor')));

$columns = array ('CATEGORY' => _L('Category')  , 'CATEGORY_PATH' => _L('Category Path'));
$tpl->assign('columns', $columns);

//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);
$tpl->assign('columnURLs', $columnURLs);

$tpl->assign('col_count', count ($columns) + 2);

$tpl->assign('user_detail_columns', array ('LOGIN'    => _L('LOGIN')     , 'NAME'          => _L('NAME')));

if (defined('SORT_FIELD'))
	$orderBy = ' ORDER BY C.TITLE '.SORT_ORDER;

	
	
switch ($action)
{
	case 'A' :
			delete_child_categories();
			$data['ID']          = $id;
			$data['USER_ID']     = $u;
			$data['CATEGORY_ID'] = $id;
			$id = $db->GenID($tables['user_permission']['name'].'_SEQ');
			if (db_replace('user_permission', $data, 'ID') > 0)
				$tpl->assign('posted', 'Permission granted.');
         else
				$tpl->assign('sql_error', $db->ErrorMsg());

			break;
	case 'C' :
			$tpl->assign('CATEGORY_ID', $id);
			break;
	case 'D' :
			if ($db->Execute("DELETE FROM `{$tables['user_permission']['name']}` WHERE `ID` = ".$db->qstr($id)))
			{
			    $tpl->assign('posted', 'Permission removed.');
				break;
			}
			else
				$tpl->assign('sql_error', $db->ErrorMsg());

	case 'N' :
	default :
                //RALUCA: JQuery validation related
                $validators = array(
                        'rules' => array(
                                'CATEGORY_ID' => array(
                                        'required' => true,
                                        'remote' => array(
                                                        'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                                                        'type'=> "post",
                                                        'data'=> array (
                                                                'action' => "isNotTopCat",
                                                                'table' => "category",
                                                                'field' => "CATEGORY_ID"
                                                                )
                                        )
                                )
                        ),
                        'messages' => array(
                                'CATEGORY_ID'=> array(
                                        'remote'  	=>_L("Please select a category.")
                                )
                        )
                );
                $vld = json_custom_encode($validators);
                $tpl->assign('validators', $vld);

                $validator = new Validator($validators);
                //RALUCA: end of JQuery validation related
		if (empty ($_POST['submit']))
		{
		} else {
                        $data = get_table_data('user_permission');
			$data['USER_ID'] = $u;

		       //RALUCA: JQuery validation related - server side.
                       $validator = new Validator($validators);
                       $validator_res = $validator->validate($_POST);
                       //RALUCA: end of JQuery validation related - server side.

                       if (empty($validator_res))
                       {
                
			    // Check if category is parent to existing categories.
				$child_categories = find_child_categories();

				if ($child_categories > 0)
				{
					$tpl->assign('CHILD_CATEGORIES', $child_categories);
					$tpl->assign('WARN', true);
					$category = $db->GetOne("SELECT `TITLE` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($data['CATEGORY_ID']));
					$tpl->assign('CATEGORY', $category);
				}
            else
            {		$id = $db->GenID($tables['user_permission']['name'].'_SEQ');
					$data['ID'] = $id;
					if (db_replace('user_permission', $data, 'ID') > 0)
						$tpl->assign('posted', 'Permission granted.');
               else
						$tpl->assign('sql_error', $db->ErrorMsg());
				}
			}
			$tpl->assign('CATEGORY_ID', $data['CATEGORY_ID']);
		}
}
if ($_SESSION['phpld']['adminpanel']['is_admin'])
{
	$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` {$where}");
	$page = get_page($list_total);
	$tpl->assign('list_limit', LINKS_PER_PAGE);
	$tpl->assign('list_total', $list_total);
	$sql = "SELECT U.*, ".$db->IfNull('C.TITLE', "'Top'")." AS CATEGORY FROM `{$tables['user_permission']['name']}` AS U LEFT OUTER JOIN `{$tables['category']['name']}` AS C ON U.CATEGORY_ID = C.ID {$where} {$orderBy}";
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
	$sql = "SELECT U.*, ".$db->IfNull('C.TITLE', "'Top'")." AS CATEGORY FROM {$tables['user_permission']['name']} AS U LEFT OUTER JOIN {$tables['category']['name']} AS C ON U.CATEGORY_ID = C.ID {$where} AND (".str_replace("ID","U.CATEGORY_ID",$_SESSION['phpld']['adminpanel']['grant_permission']).") {$orderBy}";
	$rs = $db->SelectLimit($sql , LINKS_PER_PAGE, LINKS_PER_PAGE * ($page -1));
	$list = $rs->GetAssoc(true);
	// Go through each link to get category path
	foreach($list as $category => $category_row)
		$list[$category]['CATEGORY_PATH'] = get_path($list[$category]['CATEGORY_ID']);

	$tpl->assign('list', $list);
}

$categs = get_grant_categs_tree();
$tpl->assign('categs', $categs);

$user_detail = $db->GetRow("SELECT `LOGIN`, `NAME` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($u));
$tpl->assign('user_detail', $user_detail);

$permsTitleMsg = _L('Permissions for #USER_LOGIN# - #USER_NAME#');
$permsTitleMsg = str_replace(array ('#USER_LOGIN#', '#USER_NAME#'), array ($user_detail['LOGIN'], $user_detail['NAME']), $permsTitleMsg);
$tpl->assign('permsTitleMsg', $permsTitleMsg);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_user_permissions.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
//echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

http_custom_redirect(DOC_ROOT.'/conf_users_actions.php?action=E:'.$u);

?>