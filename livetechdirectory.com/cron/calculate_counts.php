<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 require_once '../init.php';

if ($_GET['token'] == 'changemenow') {
      $categIDs = $db->GetAll("SELECT * FROM `{$tables['category']['name']}` ORDER BY `ID` ASC ");

   foreach ($categIDs as $topCat) {
		$additional_links = array();
		$add_links = $db->GetAll("SELECT `LINK_ID`  FROM `{$tables['additional_category']['name']}` WHERE `CATEGORY_ID` = ".$db->qstr($topCat['ID']));
		foreach ($add_links as $add_link)
		    $additional_links[] = $add_link['LINK_ID'];
		$additional_links = implode(",", $additional_links);
		$additional_links = !$additional_links ? '0' : $additional_links;
		$count    = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE (`STATUS` = '2') AND (`CATEGORY_ID` = ".$db->qstr($topCat['ID'])." OR `ID` IN ({$additional_links}))");
		$categs_count =  $db->getOne("SELECT COUNT(*) AS cnt FROM `{$tables['category']['name']}` WHERE PARENT_ID = ".$topCat['ID']);
		$all_count = $count +  $categs_count;
		
		
	$db->execute("UPDATE `{$tables['category']['name']}` SET `COUNT` = ".$all_count." WHERE ID = ".$topCat['ID']);
        
        $db->execute("UPDATE `{$tables['category']['name']}` SET `LINK_COUNT` = ".$count." WHERE ID = ".$topCat['ID']);
}
}