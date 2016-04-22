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



class Widget_AlexaGraphDetailPage extends Phpld_Widget {



	function getContent() {			

		global $db;
		global $tables;				

		$set = $this->getFrontSettings();	    
		
		// What is LID? Its not defined
		// $id = LID;				

		$idLink = $_REQUEST['idLink'];
		$linkModel = new Model_Link();        
        $link = $linkModel->getLink($idLink);				
		
		# TODO: Something is wrong with cache, need to check and fix it later
		
		#$data =  $db->CacheGetRow("SELECT *, DATE_FORMAT(DATE_ADDED, '%M %d, %Y %r') AS `DAT` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($link['ID']));
		
		$query = "SELECT * FROM " . $tables['link']['name'] . " WHERE ID = " . $db->qstr($link['ID']);				
		
		$data = $db->GetRow($query);				

		$url = $data['URL'];

		$this->tpl->assign('url', $url);
		
		if ($set['DISPLAY_IN_BOX'] == "No") {
			$this->tpl->assign('show_title', 1);
		}

		$this->tpl->assign("SET_TITLE", $set['TITLE']);

		return $this->tpl->fetch('content.tpl');
	}

}

?>