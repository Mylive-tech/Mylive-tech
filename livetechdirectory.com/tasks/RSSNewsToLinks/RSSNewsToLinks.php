<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	

class RSSNewsToLinks extends Task {

	public $description 	   = 'Connect RSS Feed with Links';
	public $type						= 'ACTION';
	public $run_freq			= 0;


	public function __construct($db) {
   	parent::__construct($db);
   	
	}
	
	public function get_total_num() {
   	return -1;
	}

	
	public function do_task() {   	global $db, $tables;
   	
   	// Needed to avoid simplepie deprecated notices
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
   	require_once 'libs/rss/simplepie.inc';
   	
		$url = $this->settings['RSS_URL'];
   	
   	$feed = new SimplePie();

   	$feed->set_feed_url($url);
   	
   	$feed->init();
   	
   	$feed_items = $feed->get_items();
		$items = array();

		foreach($feed_items as $item) {
			$items[$i]['TITLE'] = $item->get_title();
			$items[$i]['DESCRIPTION'] = $item->get_description();
			$items[$i]['URL'] = $item->get_permalink();
			$items[$i]['DATE'] = $item->get_date("Y-m-d H:i:s");
			
			$i++;
		}
		
		if ( count($items) > 0) {
			if (strtoupper($this->settings['RSS_KEEP_NEW']) == 'YES') {
				$db->Execute	("DELETE FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` = '{$this->settings['RSS_CATEGORY']}'");
			}
			$i = 0;
			foreach ($items as $item_id => $item) {
				$data = get_table_data('link');
				$link_id = $db->GetOne("SELECT `ID` FROM `{$tables['link']['name']}` WHERE `TITLE` = '{$item['TITLE']}'");
				$data['ID']							= ($link_id > 0) ? $link_id : null;
				$data['TITLE'] 					= $item['TITLE'];
				$data['DESCRIPTION']  = $item['DESCRIPTION'];
				$data['URL']						= $item['URL'];
				$data['DATE_ADDED']	=	$item['DATE'];
				$data['STATUS']				= 2;
				$data['CATEGORY_ID'] = $this->settings['RSS_CATEGORY'];
				$data['LINK_TYPE']			= $this->settings['RSS_LINK_TYPE'];
				$result = $db->Replace($tables['link']['name'], $data, 'ID', true);
				$i++;
				if ($i == $this->settings['RSS_LINKS_NUM']) {
					break;	
				}
				if ($db->ErrorMsg())
					var_dump($db->ErrorMsg());
			}
		}
   	
   	return $result;

	}




}

?>