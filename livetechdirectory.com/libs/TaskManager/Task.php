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
 # Copyright (C) 2004-2011 NetCreated, Inc. (http://www.netcreated.com/)
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
 # @copyright      2004-2011 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.0 Codename Transformer
 # ################################################################################
 */
abstract class Task {
	public $description; 					// Task Description
	public $settings = array();		// Task Settings from DB.PLD_TASK_CONFIG table
	public $db;
	public $id; 										// Task ID from DB.PLD_TASK table
	
	public $type = 'TASK';					// Task Type, TASK - Task that procceed Links, ACTION - single action that will be done one by one

	public $status;
	public $action_status;
	public $total_num;						// Total Task queue items number
	public $done_num;						// Done items number
	public $init_done_num;				// Initialy done items number (needed for restarting Task)
	public $run_freq = 0;					// Runing frequency in seconds between normal TaskFactory Task Call. 0 - every time
	public $load_freq = 6;					// Reload frequency type after all items was procceed
	public $last_run;							// Last Task Action Timestamp
	public function __construct($db) {		global $tables, $db;
   	$this->db = $db;
   	
   	// Load task settings
   	$task_name = get_class($this);
   	$task_info = $this->db->GetRow("SELECT `ID`, `TOTAL_NUM`, `DONE_NUM`, `LAST_RUN`, `LOAD_FREQ`, `STATUS`, `ACTION_STATUS` FROM `{$tables['task']['name']}` WHERE `NAME` = '{$task_name}'");
   	if ($task_info) {
   		$this->id  		 					= $task_info['ID'];
   		$this->status 					= $task_info['STATUS'];
   		$this->action_status 		= $task_info['ACTION_STATUS'];
   		$this->done_num 			= $task_info['DONE_NUM'] ? $task_info['DONE_NUM'] : 0;
   		$this->init_done_num 	= $this->done_num;
   		$this->total_num 				= $task_info['TOTAL_NUM'] ? $task_info['TOTAL_NUM'] : 0;
   		$this->last_run 				= $task_info['LAST_RUN'] ? $task_info['LAST_RUN'] : 0;
   		$this->load_freq 				= $task_info['LOAD_FREQ'] ? $task_info['LOAD_FREQ'] : 0;
   	}
   	
   	if (!$this->total_num) {
   		$this->init_total_num();
   	}

      $this->settings = $this->db->GetAssoc("SELECT `ID`, `VALUE` FROM `{$tables['task_settings']['name']}` WHERE `TASK_ID` = '{$this->id}'");
      if(empty($this->settings))$this->settings = get_task_settings();
	}
	
	final function log_run() {
		global $tables;
		$this->db->Execute("UPDATE `{$tables['task']['name']}` SET `LAST_RUN` = NOW() WHERE `ID` = '{$this->id}'");
		if ($this->type == 'ACTION')
			$this->db->Execute("UPDATE `{$tables['task']['name']}` SET `ACTION_STATUS` = '3' WHERE `ID` = '{$this->id}'");
	}

	public function init_total_num() {   	global $tables;
   	$task_name = get_class($this);
   	$this->total_num = $this->get_total_num();
   	$this->db->Execute("UPDATE `{$tables['task']['name']}` SET `TOTAL_NUM` = '{$this->total_num}' WHERE `NAME` = '{$task_name}'");
	}

	public function save_task_results() {   	global $tables;
   	
   	$done_num = $this->db->GetOne("SELECT `DONE_NUM` FROM `{$tables['task']['name']}` WHERE `ID` = '{$this->id}'");
   	
   	if ($this->init_done_num == $done_num) {
   		$this->db->Execute("UPDATE `{$tables['task']['name']}` SET `DONE_NUM` = '{$this->done_num}' WHERE `ID` = '{$this->id}'");
   	}
   	
   	if ($this->type == 'ACTION') {
   		$this->db->Execute("UPDATE `{$tables['task']['name']}` SET `ACTION_STATUS` = '{$this->action_status}' WHERE `ID` = '{$this->id}'");	
   	}

	}

	public function save_settings() {   	global $tables;
		$this->db->Execute("DELETE FROM `{$tables['task_settings']['name']}` WHERE `TASK_ID` = '{$this->id}' AND `TYPE` = 'INTERNAL'");
		foreach ($this->settings as $set_id => $set_val) {
			$this->db->Execute("INSERT INTO `{$tables['task_settings']['name']}` (`TASK_ID`, `ID`, `VALUE`, `TYPE`) VALUES ('{$this->id}', '{$set_id}', '{$set_val}', 'INTERNAL')");
			if ($this->db->ErrorMsg())
				var_dump($this->db->ErrorMsg());
		}
	}

   public function __destruct() {
		// Save task settings
		//$this->save_settings(); // @todo
		$this->save_task_results();
	}
	
	abstract function do_task();
	
	public function install() {}
	
	public function init_load() {}



}


?>