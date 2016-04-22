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
function check_tasks() {
		
		
		global $db, $tables;
		$i = 0;
		$tasks_dir = INSTALL_PATH."tasks/";
		   	if (is_dir($tasks_dir)) {
			   	if ($dh = opendir($tasks_dir)) {
					      	while (($file = readdir($dh)) !== false) {
					         	if ($file != "." && $file != ".." && is_dir($tasks_dir.$file)) {
							            	$t[$i] = $file;
							            	$i++;
						           }
						}
						closedir($dh);
				}
		}
		asort($t);

		//Remove Tasks/Actions From Database
		$db_t = $db->GetAssoc("SELECT `ID`,`NAME` FROM `{$tables['task']['name']}` ");
		foreach($db_t as $k => $v)
				{
					if(!in_array($v,$t))
					{
						$db_t = $db->Execute("DELETE  FROM `{$tables['task']['name']}`  WHERE ID = ".$db->qstr($k));
						$db_t = $db->Execute("DELETE  FROM `{$tables['task_settings']['name']}`  WHERE TASK_ID = ".$db->qstr($k));
					}
				}

	  
		foreach ($t as $k => $v) {
		$class_name 	= $v;
		$class_fname = "{$tasks_dir}{$class_name}/{$class_name}.php";
		if (file_exists($class_fname)) {
       		
         		require_once($class_fname);
         		if (class_exists($class_name)) {
         			$task_obj = new $class_name($db);
         			$task_type = (strtoupper($task_obj->type) == 'TASK') ? 1 : 2;
         			$task_id = $db->GetOne("SELECT `ID` FROM `{$tables['task']['name']}` WHERE `NAME` = '{$class_name}'");
  						// If not exists add new one to DB
  						if (!$task_id) {
  							$db->Execute("INSERT INTO `{$tables['task']['name']}`
  											(`ID`, `NAME`, `TYPE`, `DESCRIPTION`, `LOAD_FREQ`, `STATUS`)
  							  		VALUES
  							 				('', '{$class_name}', '{$task_type}', '{$task_obj->description}', '{$task_obj->load_freq}', '1')
  							 		");
  							$task_id = $db->GetOne("SELECT `ID` FROM `{$tables['task']['name']}` WHERE `NAME` = '{$class_name}'");
  						}
						else
						{
						
						 //if exists Check Settings List
						 $db_settings = $db->GetAssoc("SELECT NAME, VALUE FROM `{$tables['task_settings']['name']}` WHERE `TASK_ID` = '{$task_id}'");
						 $db_t = $db->Execute("DELETE  FROM `{$tables['task_settings']['name']}`  WHERE TASK_ID = ".$db->qstr($task_id));			
						 
						}
						$settings = get_task_settings($class_name);
  						if (count($settings) > 0) {
  								// Save settings to DB
								foreach ($settings as $setting) {
										
								        if(!empty($db_settings[$setting['NAME']]))
										$setting['VALUE'] = $db_settings[$setting['NAME']];
  									$db->Execute("INSERT INTO `{$tables['task_settings']['name']}` (`TASK_ID`, `ID`, `NAME`, `DESCRIPTION`, `AVAILABLE`, `VALUE`, `TYPE`) VALUES ('{$task_id}', '{$setting['ID']}',  '{$setting['NAME']}', '{$setting['DESCRIPTION']}', '{$setting['AVAILABLE']}', '{$setting['VALUE']}', 'EXTERNAL')");	
  								}
  						}
         		}
         	} 
       	}
}
check_tasks();


$reload_freq = array(0 => 'Every Cron Job Start', 1 => 'Every Day', 2 => 'After 2 Days', 3 => 'After 3 Days', 4 => 'After Week', 5 => 'After 2 Weeks', 6 => 'After Month');

if ($_REQUEST['a']) {
	$action_info = explode(':', $_REQUEST['a']);
	$action = strtoupper($action_info[0]);
	$id = intval($action_info[1]);
}


$queue_items_done = $db->GetOne("SELECT SUM(DONE_NUM) FROM `{$tables['task']['name']}` WHERE `STATUS` = '2' AND `TYPE` = '1'");

$queue_items_total = $db->GetOne("SELECT SUM(TOTAL_NUM) FROM `{$tables['task']['name']}` WHERE `STATUS` = '2' AND `TYPE` = '1'");


$stats['QUEUE_ITEMS_NUM'] = $queue_items_total - $queue_items_done;
$stats['TOTAL_PROCESSED'] = $queue_items_done;

$stats['QUEUE_ITEMS_NUM'] = $stats['QUEUE_ITEMS_NUM'] ? $stats['QUEUE_ITEMS_NUM'] : 0;

$stats['TOTAL_PROCESSED'] = $stats['TOTAL_PROCESSED'] ? $stats['TOTAL_PROCESSED'] : 0;




$tasks = $db->GetAll("SELECT * FROM `{$tables['task']['name']}` WHERE `TYPE` = 1 ORDER BY `NAME` ASC");

$actions = $db->GetAll("SELECT * FROM `{$tables['task']['name']}` WHERE `TYPE` = 2 ORDER BY `NAME` ASC");

foreach ($tasks as $task_id => $task) {
	// Calculate completed percentage value
	$tasks[$task_id]['DONE_PERCENTS'] = (int)(($task['DONE_NUM'] / $task['TOTAL_NUM']) * 100);
}

$tpl->assign('task_status', array(1 => 'Inactive', 2 => 'Active'));

$tpl->assign('reload_freq', $reload_freq);
$tpl->assign('tasks', $tasks);
$tpl->assign('actions', $actions);
$tpl->assign('stats', $stats);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/task_manager.tpl');
$tpl->assign('content', $content);

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

?>