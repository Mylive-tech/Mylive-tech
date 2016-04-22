<?php
$gt = ">";
$lt = "<";
if ((isset($_SERVER["SCRIPT_FILENAME"]) && strlen($_SERVER["SCRIPT_FILENAME"]) > strlen(basename(__FILE__)) && substr(__FILE__, -1 * strlen($_SERVER["SCRIPT_FILENAME"])) == substr($_SERVER["SCRIPT_FILENAME"], -1 * strlen(__FILE__))) || !defined("GOTMLS_plugin_path"))
	die("{$lt}!DOCTYPE html{$gt}{$lt}html{$gt}{$lt}head{$gt}{$lt}title{$gt}403 Forbidden{$lt}/title{$gt}{$lt}/head{$gt}{$lt}body{$gt}{$lt}h1{$gt}Forbidden{$lt}/h1{$gt}
{$lt}p{$gt}You don't have permission to access this directory.{$lt}/p{$gt}{$lt}/body{$gt}{$lt}/html{$gt}")); // Display the Forbidden Error to anyone trying to browse the quarantine without permission!
		?>