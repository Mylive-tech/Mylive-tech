<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 require_once '../init.php';

$listings = $db->getAll('SELECT * FROM PLD_LINK');
$linkModel = new Model_Link();

foreach ($listings as $listing) {
    $seoUrl = $linkModel->seoUrl($listing, $listing['ID']);
    $db->execute('UPDATE PLD_LINK SET `CACHE_URL` = "'.$seoUrl.'" WHERE ID = '.$listing['ID']);
}
