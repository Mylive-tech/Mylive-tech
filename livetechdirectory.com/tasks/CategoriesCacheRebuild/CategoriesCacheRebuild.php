<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	

class CategoriesCacheRebuild extends Task {
	public $description = 'Categories Cache Rebuilding';
	
	public $run_freq = 0;


public function __construct($db) {
   parent::__construct($db);
   	
	}
	
public function get_total_num() {
    global $tables;
   	$total_num = $this->db->GetOne("SELECT COUNT(`ID`) FROM `{$tables['category']['name']}` WHERE `STATUS` = '2'");
    return $total_num;
	}

public function do_task() {   	
	global $db, $tables;
	
	$count_links = $this->settings['CACHE_CATEGS_PER_ITERATION'];
	 
	if(!is_numeric($count_links))
		$count_links = 1;

	$categ = $db->GetRow("SELECT `ID`, `DATE_ADDED` FROM `{$tables['category']['name']}` ORDER BY `ID` ASC LIMIT {$this->done_num}, {$count_links}");
  	
   	
	$data['CACHE_TITLE'] = trim (buildCategUrlTitle($categ['ID']));
   	
	$data['CACHE_URL'] = trim (buildCategUrl($categ['ID']));
   	
	$data['DATE_ADDED'] = $categ['DATE_ADDED'];
   	
   
	$result = $db->Execute("UPDATE `{$tables['category']['name']}` SET `CACHE_TITLE` = '{$data['CACHE_TITLE']}', `CACHE_URL` = '{$data['CACHE_URL']}', `DATE_ADDED` = '{$data['DATE_ADDED']}' WHERE `ID` = '{$categ['ID']}'");
   	
   	
	$this->done_num+=$count_links;
   	
	return $result;

	}
}
?>