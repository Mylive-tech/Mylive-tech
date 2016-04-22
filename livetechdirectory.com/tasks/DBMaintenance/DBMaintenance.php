<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	

class DBMaintenance extends Task {

	public $description 	   = 'phpLD DB Maintenance';
	public $run_freq			= 0;												// No delay before each call
	public $load_freq			= 4; 												// Once per day
	public $type						= 'ACTION';


	public function __construct($db) {
   	parent::__construct($db);
	}
	
	public function get_total_num() {
   	$total_num = -1;
   	return $total_num;
	}

	
	public function do_task() {   	global $db, $tables;
   	
   	// IMAGE_VERIFICATION Table Maintenance
   	$expiry_ts = strtotime("-".$this->settings['IM_VER_EXP']." days", time());
   	
   	$db->Execute("DELETE FROM `{$tables['img_verification']['name']}` WHERE `CREATED` >= '{$expiry_ts}'");
   	
   	// Optimize table after cleaning
   	$db->Execute("OPTIMIZE TABLE `{$tables['img_verification']['name']}`");
	
	
	
	// HITs Table Maintenance
   	$expiry_ts = date('Y-m-d H:i:s', strtotime("-".LIMIT_HITS_TIME." hours", time())); 
   	
   	$db->Execute("DELETE FROM `{$tables['hits']['name']}` WHERE `LAST_HIT` >= '{$expiry_ts}'");
   	
   	// Optimize table after cleaning
   	$db->Execute("OPTIMIZE TABLE `{$tables['hits']['name']}`");
   	
   	// Calculate SEQ Tables
   	$links_status 		= $db->GetRow("SELECT `ID` FROM  `PLD_LINK` ORDER BY  `PLD_LINK`.`ID` DESC Limit 1");
   	$categs_status  = $db->GetRow("SELECT `ID` FROM  `PLD_CATEGORY` ORDER BY  `PLD_CATEGORY`.`ID` DESC Limit 1");
   	$users_status  	= $db->GetRow("SELECT `ID` FROM  `PLD_USER` ORDER BY  `PLD_USER`.`ID` DESC Limit 1");
   	
   	$links_auto_inc 		= ($links_status['ID'] +1);
   	$categs_auto_inc = ($categs_status['ID'] +1);
   	$users_auto_inc = ($users_status['ID'] +1);

	$db->Execute("UPDATE `PLD_LINK_SEQ` SET `id` =" .$links_auto_inc);
	$db->Execute("UPDATE `PLD_CATEGORY_SEQ` SET `id` =" .$categs_auto_inc);
   	$db->Execute("UPDATE `PLD_USER_SEQ` SET `id` =" .$users_auto_inc);
   	
   	// Optimize main tables
   	$db->Execute("OPTIMIZE TABLE `{$tables['link']['name']}`");
   	$db->Execute("OPTIMIZE TABLE `{$tables['category']['name']}`");
   	$db->Execute("OPTIMIZE TABLE `{$tables['user']['name']}`");
   	
   	//  Update main CACHE table
   	
	}




}

?>