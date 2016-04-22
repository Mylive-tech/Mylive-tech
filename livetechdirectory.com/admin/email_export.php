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
 # Copyright (C) 2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
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
 # @copyright      2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.1.0 Phoenix Release
 # ################################################################################
 */
 
require_once 'init.php';

if ($_REQUEST['action'])
{
	$data = '';
	$rs = $db->Execute("SELECT * FROM `{$tables['email']['name']}`");
	while(!$rs->EOF)
	{
		$data .= sprintf("%s\t%s\t%s\n", $rs->Fields('TITLE'), $rs->Fields('URL'), $rs->Fields('EMAIL'));
		$rs->MoveNext();
	}
	$length = strlen($data);

   disable_browser_cache(); // Disable any caching by the browser

	@header("Content-type: application/force-download");
	@header("Content-Disposition: attachment; filename=email.csv");
	@header("Accept-Ranges: bytes");
	@header("Content-Length: {$length}");
	echo $data;
}
else
{
	$content = $tpl->fetch(ADMIN_TEMPLATE.'/email_export.tpl');
	$tpl->assign('content', $content);

	//Clean whitespace
   $tpl->load_filter('output', 'trimwhitespace');

   //Make output
	echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
}
?>