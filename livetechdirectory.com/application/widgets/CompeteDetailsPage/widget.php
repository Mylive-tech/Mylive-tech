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

 # Copyright (C) 2004-2012 NetCreated, Inc. (http://www.netcreated.com/)

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

 # @copyright      2004-2012 NetCreated, Inc. (http://www.netcreated.com/)

 # @projectManager David DuVal <david@david-duval.com>

 # @package        PHPLinkDirectory

 # @version        5.0 Codename Transformer

 # ################################################################################

 */

 

class Widget_CompeteDetailsPage extends Phpld_Widget {



	function getContent() {

		global $db;
		global $tables;

		$set = $this->getFrontSettings();   

		/*if (intval($set['API_KEY']) > 0) {
			$apikey = $set['API_KEY'];
		} else {
			$apikey = 0;
		}*/
		
		$apikey = $set['API_KEY'];				

		if( $apikey != "") {

			// What is LID? Its not defined
			// $id = LID;						

			$idLink = $_REQUEST['idLink'];
			$linkModel = new Model_Link();
			$link = $linkModel->getLink($idLink);						
			
			$query = "SELECT * FROM " . $tables['link']['name'] . " WHERE ID = " . $db->qstr($link['ID']);				
			
			$data = $db->GetRow($query);

			$url = $data['URL'];

			$host = parseDomain($url);

			$url_parts = urlencode($host);
			
			# Compete API was changed, we can get only rank value now
			
			/*$uri = "http://api.compete.com/fast-cgi/MI?d=$url_parts&ver=3&apikey=$apikey";
			$compete = "http://snapshot.compete.com/$url_parts";			

			$api = file_get_contents($uri);			

			if($api != "") {

				$start = strpos($api,"<rank");

				$rank = substr($api, $start, strpos($api,"</rank>")-$start);

				if($rank != "") {

					$start = strpos($rank, "<val>");

					if($start = $start + 5) {

						$ranking = substr($rank, $start,strpos($rank,"</val>")-$start);

					}

					$start = strpos($rank, "<icon>");

					if($start = $start + 6 ) {

						$icon = substr($rank, $start, strpos($rank,"</icon>")-$start);

					}

				}

				$start = strpos($api, "<metrics");

				$metrics = substr($api,$start,strpos($api, "</metrics>")-$start);

				if($metrics != "") {

					$start = strpos($metrics,"<count>");

					if($start = $start + 7) {

						$count = substr($metrics, $start, strpos($metrics,"</count>")-$start);

					}
				}
			}*/
			
			$metric = 'rank';
			$uri = 'http://apps.compete.com/sites/' . $url_parts . '/trended/' . $metric . '/?apikey=' . $apikey;
			$response = file_get_contents($uri);
			$data = json_decode($response, true);
			$data = $data['data']['trends']['rank'];
			$array_length = count($data);
			$last_data = $data[$array_length-1];
			
			$compete = "http://snapshot.compete.com/" . $url_parts;
			
			$this->tpl->assign('ranking', $last_data['value']);			
			$this->tpl->assign('compete', $compete);	
		}

		/*$this->tpl->assign('icon', $icon);

		$this->tpl->assign('count', $count);

		$this->tpl->assign('compete', $compete);

		$this->tpl->assign('ranking', $ranking);

		$this->tpl->assign('compete', $compete);*/

		$this->tpl->assign('url_parts', $url_parts);

		if ($set['DISPLAY_IN_BOX'] == "No") {

			$this->tpl->assign('show_title', 1);

		}
		$this->tpl->assign("SET_TITLE", $set['TITLE']);
		return $this->tpl->fetch('content.tpl');

	}
}

?>