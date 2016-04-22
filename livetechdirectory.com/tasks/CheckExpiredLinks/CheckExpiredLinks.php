<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	

class CheckExpiredLinks extends Task {

	public $description 	   = 'Check Expired Links';
	public $run_freq			= 0;


	public function __construct($db) {
   	parent::__construct($db);
   	
	}
	
	public function get_total_num() {
		global $tables;
   	$total_num = $this->db->GetOne("SELECT COUNT(`ID`) FROM `{$tables['link']['name']}` WHERE `EXPIRY_DATE` <= NOW() AND `STATUS` = '2'");

   	return $total_num;
	}

	
	public function do_task() {   	global $db, $tables;
   	$count_links = $this->settings['EXPIRED_LINKS_PER_ITERATION'];
	if(!is_int($count_links))
		$count_links = 1;
   	$link = $db->GetRow("SELECT ID, OWNER_ID FROM `{$tables['link']['name']}` WHERE `EXPIRY_DATE` <= NOW()  AND `STATUS` = '2' LIMIT {$this->done_num}, {$count_links}");
   	
   	if ($this->settings['EXPIRED_STATUS'] == 'Inactive')
   		$new_status = 0;
   	else
   		$new_status = 1; 
   	
   	$result = $db->Execute("UPDATE `{$tables['link']['name']}` SET `STATUS` = '{$new_status}' WHERE `ID` = '{$link['ID']}'");
   	
   	if (strtoupper($this->settings['SEND_OWNER_NOTIFICATION']) == 'YES') {
   		send_expired_link_notification($link['ID']);
   	}
   	
   	$this->done_num+=$count_links;
   	
   	return $result;

	}




}

?>