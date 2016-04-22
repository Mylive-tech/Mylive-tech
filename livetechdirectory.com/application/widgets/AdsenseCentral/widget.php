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
# Copyright (C) 2004-2009 NetCreated, Inc. (http://www.netcreated.com/)
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
#      on all pages of your directory in you purchased the $25 version
#      of the software.
#
# License holders are entitled to upgrades to the 3.4 branch of the software
# as they are made available at http://www.phplinkdirectory.com/
#
# In some cases, license holders may be required to agree to changes
# in the software license before receiving updates to the software.
# **********************************************************************
#
# For questions, help, comments, discussion, etc., please join the
# PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
#
# @link           http://www.phplinkdirectory.com/
# @copyright      2004-2009 NetCreated, Inc. (http://www.netcreated.com/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
# @version        5.0 Codename Transformer
# ################################################################################
*/
class Widget_AdsenseCentral extends Phpld_Widget {
	function getContent() {
		global $db;
		global $tables;
//die('asf');
		$set = $this->getFrontSettings();

		switch ($set['TYPE']) {
			case "Banner":
				$width = 468;
				$height = 60; 
			break;
			case "Large Rectangle":
				$width = 336;
				$height = 280; 
			break;	
		}

        $this->tpl->assign('set', $set);
		$this->tpl->assign('TYPE', $set['TYPE']);
		$this->tpl->assign('WIDTH', $width);
		$this->tpl->assign('HEIGHT', $height);
		return $this->tpl->fetch('content.tpl');
	}
}

?>