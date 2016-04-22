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
   
$error = 0;
$errorMsg = '';
if ($_REQUEST['action'])
{
   list ($action, $id, $tid) = explode(':', $_REQUEST['action']);
   if ($action == 'N')
   	$tid = $id;
   else
		$tid = $tid ? $tid : $_REQUEST['LINK_TYPE_ID'];
   $action = strtoupper (trim ($action));
   $tpl->assign('action', strtoupper($action));
   //$tpl->assign('action', $_REQUEST['action']);
}

//Correct value for ID
$id = (isset ($id) ? intval ($id) : 0);
$id = ($id < 0 ? 0 : $id);

$tpl->assign('id', $id);

$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Active'),));
$tpl->assign('types', array ('STR' => _L('Char Field'), 'TXT' => _L('Text Area'), 'BOOL' => _L('Yes/No'), 'DROPDOWN' => _L('Dropdown'), 'CAT' => _L('Category'),'VIDEO' => _L('Video Upload'), 'FILE' => _L('File Upload'), 'IMAGE' => _L('Image Upload'), 'IMAGEGROUP' => _L('Images Group'), 'MULTICHECKBOX'=>_L('Multiple Checkbox'), 'TAGS'=>_L('Tags')));
$fields_names = array('TITLE', 'DESCRIPTION', 'URL', 'ADDRESS', 'CITY', 'STATE', 'ZIP', 'PHONE_NUMBER', 'CATEGORY_ID', 'RECPR_URL', 'OWNER_NAME', 'OWNER_EMAIL', 'META_KEYWORDS', 'META_DESCRIPTION');
$tpl->assign('fields_names', $fields_names);

switch ($action) {
   case 'S':
		list($a, $id, $tid, $status) = explode(':', $_REQUEST['action']);
      $db->Execute("UPDATE `{$tables['submit_item_status']['name']}` SET `STATUS` = '{$status}' WHERE `ITEM_ID` = '{$id}' AND `LINK_TYPE_ID` = '{$tid}'");
      break;
   case 'U':
      $old_pos = $db->GetOne("SELECT `ORDER_ID` FROM `{$tables['submit_item']['name']}` WHERE `ID` = '{$id}'");
      $upper   = $db->GetOne("SELECT MAX(`ORDER_ID`) FROM `{$tables['submit_item']['name']}` WHERE `ORDER_ID`<".$db->qstr($old_pos));
      if ($upper !== null) {
          $db->Execute("UPDATE `{$tables['submit_item']['name']}` SET `ORDER_ID` = '{$old_pos}' WHERE `ORDER_ID` = '{$upper}'");
          $db->Execute("UPDATE `{$tables['submit_item']['name']}` SET `ORDER_ID` = '{$upper}'   WHERE `ID` = '{$id}'");
      }
      http_custom_redirect(DOC_ROOT.'/dir_submit_items.php');
      break;
   case 'D':
      $old_pos = $db->GetOne("SELECT `ORDER_ID` FROM `{$tables['submit_item']['name']}` WHERE `ID` = '{$id}'");
      $lower   = $db->GetOne("SELECT MIN(`ORDER_ID`) FROM `{$tables['submit_item']['name']}` WHERE `ORDER_ID`>".$db->qstr($old_pos));
      if ($lower !== null) {
          $db->Execute("UPDATE `{$tables['submit_item']['name']}` SET `ORDER_ID` = '{$old_pos}' WHERE `ORDER_ID` = '{$lower}'");
          $db->Execute("UPDATE `{$tables['submit_item']['name']}` SET `ORDER_ID` = '{$lower}'   WHERE `ID` = '{$id}'");
      }
      http_custom_redirect(DOC_ROOT.'/dir_submit_items.php');
      break;
   case 'R':
      $db->Execute("DELETE FROM `{$tables['submit_item']['name']}` WHERE `ID` = '{$id}'");
	  $db->Execute("DELETE FROM `{$tables['submit_item_status']['name']}` WHERE `ITEM_ID` = '{$id}'");
	  $db->Execute("DELETE FROM `{$tables['submit_item_validator']['name']}` WHERE `ITEM_ID` = '{$id}'");
      $db->Execute("ALTER TABLE `{$tables['link']['name']}` DROP `{$old['FIELD_NAME']}`");
      $db->Execute("ALTER TABLE `{$tables['link_review']['name']}` DROP `{$old['FIELD_NAME']}`");
      http_custom_redirect(DOC_ROOT.'/dir_submit_items.php');
      break;
   case 'E':
//	  if (empty ($_REQUEST['submit']))
//            {
             $data = $db->GetRow("SELECT * FROM `{$tables['submit_item']['name']}`  WHERE ID = ".$db->qstr($id));
             $data['DROPDOWN_VALUE'] = $db->GetOne("SELECT `VALUE` FROM `{$tables['submit_item_value']['name']}` WHERE `ITEM_ID` = ".$db->qstr($id));
             $data['MULTICHECKBOX_VALUE'] = $db->GetOne("SELECT `VALUE` FROM `{$tables['submit_item_value']['name']}` WHERE `ITEM_ID` = ".$db->qstr($id));
             $special_fields = array('TITLE', 'CATEGORY_ID', 'META_KEYWORDS', 'META_DESCRIPTION', 'RECPR_URL', 'ADDRESS', 'CITY', 'STATE', 'ZIP');
             if (in_array($data['FIELD_NAME'],$special_fields)) {
                 $tpl->assign('special_field', 1);
                 $special_field = 1;
             }
//            }
   case 'N':
   default:
                if ($special_field == 1) {
                    $validators = array();
                } else {
		//RALUCA: JQuery validation related
                $validators = array(
			'rules' => array(
				'NAME' => array(
					'required' => true,
					'remote' => array(
							'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
							'type'=> "post",
				        		'data'=> array (
				        			'action' => "isUniqueValue",
				        			'table' => "submit_item",
				        			'field' => "NAME",
				        			'id' => $id
				        		)
				    )
				),
				'FIELD_NAME' => array(
					'required' => true,
					'remote' => array(
							'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
							'type'=> "post",
				        		'data'=> array (
				        			'action' => "isUniqueValue",
				        			'table' => "submit_item",
				        			'field' => "FIELD_NAME",
				        			'id' => $id
				        		)
				    )
				)
			),
			'messages' => array(
				
				'NAME'=> array(
					'remote'  	=>_L("Name is not valid: most likely, not unique.")
				),
				'FIELD_NAME'=> array(
					'remote'  	=>_L("Field name is not valid: most likely, not unique.")
				)			
			)
		);
                }
		$vld = json_custom_encode($validators);
		$tpl->assign('form_validators', $vld);
	
		$validator = new Validator($validators);
		//RALUCA: end of JQuery validation related
   	
      if (empty ($_POST['submit'])) {
      	$tpl->assign('submit_session', registerAdminSubmitSession());
      } else {
      	checkAdminSubmitSession(clean_string($_POST['submit_session']));
      	$tpl->assign('submit_session', registerAdminSubmitSession());
      	
         $data = get_table_data('submit_item');
	       //RALUCA: JQuery validation related - server side.
		   $validator = new Validator($validators);
		   $validator_res = $validator->validate($_POST);
		   //RALUCA: end of JQuery validation related - server side.
		   
	   if (empty($validator_res)) {
         	$data['ID'] = ($action == 'N') ? null : $id;
         	$data['NAME'] = $_REQUEST['NAME'];
         	$data['FIELD_NAME'] = preg_replace('`[^\w_-]`', '_', $_REQUEST['FIELD_NAME']);
         	$data['TYPE'] = $_REQUEST['TYPE'];
         	$data['DESCRIPTION'] = $_REQUEST['DESCRIPTION'];
         	//$data['STATUS'] = null;
         	if ($action == 'N') {
         		$last_order_id = $db->GetOne("SELECT MAX(ORDER_ID) FROM `{$tables['submit_item']['name']}`");
         		$data['ORDER_ID'] = $last_order_id + 1;
         	}
         	$is_field_exists = $db->GetRow("SELECT * FROM `{$tables['submit_item']['name']}` WHERE `ID` = '{$data['ID']}'");
         	
         	switch ($data['TYPE']) {
                case 'MULTICHECKBOX':
                    $field_type = 'VARCHAR(500)';
                     break;
         		case 'TXT':
         			$field_type = 'LONGTEXT';
         			break;
         		case 'BOOL':
         			$field_type = 'TINYINT(4)';
         			break;
         		case 'CAT':
         			$field_type = 'INT(11)';
         			break;
         		case 'FILE':
				case 'VIDEO':
         		case 'IMAGE':
         		case 'STR':
         		default:
         			$field_type = 'VARCHAR(255)';
         	}
         
         	if (!$is_field_exists) {
         		$db->Execute("ALTER TABLE `{$tables['link']['name']}` ADD `{$data['FIELD_NAME']}` {$field_type} NULL");
         		$db->Execute("ALTER TABLE `{$tables['link_review']['name']}` ADD `{$data['FIELD_NAME']}` {$field_type} NULL");
         	} else {
         		$db->Execute("ALTER TABLE `{$tables['link']['name']}` CHANGE `{$is_field_exists['FIELD_NAME']}` `{$data['FIELD_NAME']}` {$field_type} NULL" );
         		$db->Execute("ALTER TABLE `{$tables['link_review']['name']}` CHANGE `{$is_field_exists['FIELD_NAME']}` `{$data['FIELD_NAME']}` {$field_type} NULL" );
         	}
         	
         	if (db_replace('submit_item', $data, 'ID') > 0)
         	{
         		$tpl->assign("posted", true);

					if ($action !== 'N') {
						$db->Execute("DELETE FROM `{$tables['submit_item_validator']['name']}` WHERE `ITEM_ID`=$id");
         		} else {
         			$id = $db->GetOne("SELECT MAX(ID) FROM `{$tables['submit_item']['name']}`");
         		}
	         	$ltypes = $db->GetAll("SELECT * FROM `{$tables['link_type']['name']}`");
	         	
	         	if ($_REQUEST['DROPDOWN_VALUE']) {
	         		$data['DROPDOWN_VALUE'] = $_REQUEST['DROPDOWN_VALUE'];
                    $submit_item_value = array('ITEM_ID' => $id, 'VALUE' => $_REQUEST['DROPDOWN_VALUE']);
                    db_replace('submit_item_value', $submit_item_value, 'ITEM_ID');
                }

	         	if ($_REQUEST['MULTICHECKBOX_VALUE']) {
	         		$data['MULTICHECKBOX_VALUE'] = $_REQUEST['MULTICHECKBOX_VALUE'];
                    $submit_item_value = array('ITEM_ID' => $id, 'VALUE' => $_REQUEST['MULTICHECKBOX_VALUE']);
                    db_replace('submit_item_value', $submit_item_value, 'ITEM_ID');
                }

	         	if (!$is_field_exists) { //add status only for new fields
             		for ($i=0; $i<count($ltypes); $i++) {
    					$db->Execute("INSERT INTO `{$tables['submit_item_status']['name']}` (ITEM_ID, LINK_TYPE_ID, STATUS) VALUES (".$id.",".$db->qstr($ltypes[$i]['ID']).", '0')");
             		}
	         	}
                        
                if (isset($_REQUEST['VALIDATORS']) && is_array($_REQUEST['VALIDATORS'])) {
               		foreach ($_REQUEST['VALIDATORS'] as $validator_id) {
                            if ($validator_id !== 'none') {
                                    $db->Execute("INSERT INTO {$tables['submit_item_validator']['name']} (`ID`, `ITEM_ID`, `VALIDATOR_ID`) VALUES ('', '{$id}', '{$validator_id}')");
                            }
                     }

                }

                if (isset($_REQUEST['ADV_VALIDATOR']) && !empty($_REQUEST['ADV_VALIDATOR'])) {
               		if ($REQUEST['ADV_VALIDATOR'] !== '') {
                                    $db->Execute("INSERT INTO {$tables['submit_item_validator']['name']} (`ID`, `ITEM_ID`, `VALIDATOR_ID`) VALUES ('', '{$id}', '{$_REQUEST['ADV_VALIDATOR']}')");
                        }
                }

            }
         }
      
      }
      $tpl->assign($data);
      $tpl->assign('tid', $tid);
      

   	  $item_v = $db->GetAll("
		SELECT iv.VALIDATOR_ID AS VALIDATOR_ID 
		FROM `{$tables['submit_item_validator']['name']}` AS iv, `{$tables['validator']['name']}` AS v 
		WHERE iv.ITEM_ID = ".$db->qstr($id)." 
			AND v.ID = iv.VALIDATOR_ID 
		ORDER BY iv.ID
	  ");
      for ($i=0; $i<count($item_v); $i++) {
      	$item_validators[$i] = $item_v[$i]['VALIDATOR_ID'];
      }
      for ($i=0; $i<count($_POST['VALIDATORS']); $i++) {
      	if (!in_array($_POST['VALIDATORS'][$i],$item_validators)) {
      		$item_validators[] = $_POST['VALIDATORS'][$i];
      	}
      }
	  
      if (!in_array($_POST['ADV_VALIDATOR'],$item_validators) && $_POST['ADV_VALIDATOR'] != '') {
      	$item_validators[] = $_POST['ADV_VALIDATOR'];
      }
      $validators = $db->GetAll("SELECT * FROM `{$tables['validator']['name']}`");
      for ($i=0; $i<count($validators); $i++) {
      	if (in_array($validators[$i]['ID'], $item_validators)) {
      		$validators[$i]['SELECTED'] = '1';
      	}
      }
      $tpl->assign('validators', $validators);
      
      $content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_submit_items_edit.tpl');
      break;

}

$tpl->assign("content", $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

?>