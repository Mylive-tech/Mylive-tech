<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 

/**
 * Ajax Request Libraries
 * 
 * Handles all classes for Ajax Request
 * 
 * @see ajaxrequest.php
 * @version 1.0
 * @package Ajaxrequest
 */

/**
 * Link Handler
 * 
 * This class is responsable for link data manipulation according requests from ajaxrequest.php
 * 
 * @see ajaxrequest.php
 * @version 1.0
 *
 */
class linkHandler
{
	function linkHandler()
	{
		
	}
	
	function updateUrl()
	{
		
	}
	
	function updateTitle()
	{
		
	}
	
	function updateDescription()
	{
		
	}
}

/**
 * Article Handler
 * 
 * This class is responsable for article data manipulation according requests from ajaxrequest.php
 *
 */
class articleHandler
{
	var $id = null;
	var $title = '';
	var $description = '';
	var $content = '';
	var $db;
	var $tables;
	
	function articleHandler()
	{
		$this->db = $GLOBALS['db'];
		$this->tables = $GLOBALS['tables'];
		$type = $_REQUEST['type'];
		
		$this->id = intval($_REQUEST['id']);
		
		if ($type == 'a') {
			$this->title = strip_tags($_REQUEST['title']);
			$this->description = $_REQUEST['description'];
			
			$this->updateTitle();
			$this->updateDescription();
			
		} elseif ($type == 'b') {
			$this->content = $_POST['content'];
			$this->updateArticle();
		}
	}
	
	function updateTitle()
	{
		$this->db->Execute("UPDATE `".$this->tables['article']['name']."` SET `TITLE` = '$this->title' WHERE `ID` = '$this->id' LIMIT 1");
	}
	
	function updateDescription()
	{
		$this->db->Execute("UPDATE `".$this->tables['article']['name']."` SET `DESCRIPTION` = '$this->description' WHERE `ID` = '$this->id' LIMIT 1");
	}
	
	function updateArticle()
	{
		$this->db->Execute("UPDATE `".$this->tables['article']['name']."` SET `ARTICLE` = '$this->content' WHERE `ID` = '$this->id' LIMIT 1");
	}
}
?>