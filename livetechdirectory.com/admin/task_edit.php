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

require_once 'libs/TaskManager/Task.php';


$load_freq = array(0 => 'Every Cron Job Start', 1 => 'Every Day', 2 => 'After 2 Days', 3 => 'After 3 Days', 4 => 'After Week', 5 => 'After 2 Weeks', 6 => 'After Month');

$task_status = array(1 => 'Inactive',  2 => 'Active');


if ($_REQUEST['a']) {
	
$action_info = explode(':', $_REQUEST['a']);

$action = strtoupper($action_info[0]);
	$id = intval($action_info[1]);

}

$link_types = $db->GetAssoc("SELECT `ID`, `NAME` FROM `{$tables['link_type']['name']}` WHERE `STATUS` = '2' ORDER BY `ORDER_ID` ASC");

$task_info = $db->GetRow("SELECT * FROM `{$tables['task']['name']}` WHERE `ID` = '{$id}'");

switch ($action) {

	case 'E':
		if (!empty($_REQUEST['submit'])) {
			$settings = array();
			$settings['ID'] = $id;
			$settings['LOAD_FREQ'] = $_REQUEST['LOAD_FREQ'];
			$settings['STATUS'] = $_REQUEST['STATUS'];
			$res = db_replace('task', $settings, 'ID');
			//$task_settings = $db->GetAll("SELECT ID, AVAILABLE FROM `{$tables['task_settings']['name']}` WHERE `TASK_ID` = '{$id}'");
			$task_settings = get_task_settings($task_info['NAME']);
			foreach ($task_settings as $set_id => $set) {
				$setting_db = $db->GetRow("SELECT *, ID AS FIELD_NAME FROM `{$tables['task_settings']['name']}` WHERE `TASK_ID` = '{$task_info['ID']}' and NAME = '{$set['NAME']}'");
				if(!empty($setting_db))
					$set['ID'] = $setting_db['ID'];
				$set['TASK_ID'] = $settings['ID'];
				if (strtoupper($set['AVAILABLE']) == 'CAT')
					$set['VALUE'] = clean_string($_REQUEST['CATEGORY_ID']);
				else
					$set['VALUE'] = clean_string($_REQUEST[$set['ID']]);
				if (!empty($set['VALUE'])) {
					$res = db_replace('task_settings', $set, array('ID','TASK_ID'));
					//$db->Execute("UPDATE `{$tables['task_settings']['name']}` SET `VALUE` = ".$db->qstr($set_val)." WHERE `TASK_ID` = '{$id}' AND `ID` = '{$set['ID']}'");
				}
			}
			$tpl->assign('result', $res);
		}
		break;
}

$task_info = $db->GetRow("SELECT * FROM `{$tables['task']['name']}` WHERE `ID` = '{$id}'");
$settings = get_task_settings($task_info['NAME']);

foreach ($settings as $setting_id => $setting) {
	unset($setting_db);
	$setting_db 	= $db->GetRow("SELECT *, ID AS FIELD_NAME FROM `{$tables['task_settings']['name']}` WHERE `TASK_ID` = '{$id}' and NAME = '{$setting['NAME']}'");

	if(!empty($setting_db))
		$settings[$setting_id] = $setting_db;
	else
		$settings[$setting_id]['ID'] =  $settings[$setting_id]['ID'];

	if (!empty($setting['AVAILABLE']) && strpos($setting['AVAILABLE'], ',') > 0) {
		$options = explode(',', $setting['AVAILABLE']);
		if (is_array($options)) {
			array_walk($options, 'format_item');
			$settings[$setting_id]['OPTIONS'] = $options;
		}
	}
}

$categs = get_categs_tree();
$tpl->assign('categs', $categs);

$tpl->assign('link_types', $link_types);

$tpl->assign('task_info', $task_info);
$tpl->assign('sets', $settings);	

$tpl->assign('load_freq', $load_freq);
$tpl->assign('task_status', $task_status);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/task_edit.tpl');
$tpl->assign('content', $content);

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

function format_item(&$value) {
    $value = trim($value);
    $value = ucfirst($value);
}
?>