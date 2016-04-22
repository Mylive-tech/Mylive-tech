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
require_once '../include/config.php';

require_once '../include/functions.php';

require_once '../include/dirdb.php';

require_once '../libs/adodb/adodb.inc.php';

require_once '../libs/TaskManager/Task.php';


ini_set('display_errors', 1);


$server_doc_root =  dirname (__file__);



$db = ADONewConnection(DB_DRIVER);
if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, true)) {
	
	
	
	//Get Task List Without Current Tasks
	$tasks = $db->GetAll("SELECT * FROM  `{$tables['task']['name']}` WHERE
			     ACTION_STATUS != 1 AND STATUS  = 2");
 } 
    foreach($tasks as $k=>$v)
    {
	$curr_time =  gmdate ('Y-m-d H:i:s');
        if(!empty($v['LAST_RUN']))
	{
		//Check Time Interval
		$dayDiff = floor((strtotime($curr_time) - strtotime($v['LAST_RUN']))/(59*60*24));
		$afterMonth = floor(strtotime($curr_time) - strtotime("+1 month",strtotime($v['LAST_RUN'])));
		
		switch($v['LOAD_FREQ'])
		{	
		case '1':
		if($dayDiff  < 1)
			continue 2;
		break;
		case '2':
		if($dayDiff  < 2)
			continue 2;
		break;
		case '3':
		if($dayDiff  < 3)
			continue 2;
		break;
		case '4':
		if($dayDiff  < 7)
			continue 2;
		break;
		case '5':
		if($dayDiff  < 14)
			continue 2;
		break;
		case '6':
		if($afterMonth < 0 )
			continue 2;
		break;
		case '0':
		default:
		}
	}
	$db->Execute("UPDATE `{$tables['task']['name']}`  SET ACTION_STATUS = 1,   WHERE ID =  ".$v['ID']."");
	$db->Execute("UPDATE `{$tables['task']['name']}` SET `LAST_RUN` = '".$curr_time."' WHERE `ID` = ".$v['ID']."");
	$class_name  = $v['NAME'];
	$class_fname = INSTALL_PATH."tasks/{$class_name}/{$class_name}.php";
	
	
	if (file_exists($class_fname)) {
		
         		require_once($class_fname);
         		if (class_exists($class_name)) {
				$task_obj = new $class_name($db);

				$task_obj->do_task();
				$total_num  = $task_obj->get_total_num();
				$done_num  = $task_obj->done_num;
				if($total_num <= $done_num){
					$done_num = 0;
					$task_obj->done_num = 0;
				}
				
			
         		}
         }
	
	$curr_time =  gmdate ('Y-m-d H:i:s');
	$db->Execute("UPDATE `{$tables['task']['name']}`  SET ACTION_STATUS = 3,`LAST_RUN` = '".$curr_time."', `TOTAL_NUM`=".$total_num.", DONE_NUM = ".$done_num." WHERE ID =  ".$v['ID']."");
	
	unset($task_obj,$total_num,$done_num);
    }
    unset($tasks);
    unset($db);

    // Relax the system by sleeping for a little bit
    // iterate also clears statcache
?>