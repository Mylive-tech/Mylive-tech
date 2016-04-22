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

if(isset($_POST['submit']))
{
	$tpl->assign("action", "update");
	$tpl->assign("tables", json_encode(array_keys($tables)));
}
elseif(isset($_POST['table']))
{
	$message="";
	$status=0;

	$result = Upgrade($tables[$_POST['table']]);
	$message = $result['message'];
	$status = $result['status'];

	echo json_encode(array("message"=>$message, "status"=>$status));
	exit();
}
else
{
	$tpl->assign("action","default");
}

$content = $tpl->fetch(ADMIN_TEMPLATE.'/db_update.tpl');
$tpl->assign('content', $content);
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');


function Upgrade($table)
{
	$message="";
	$status=0;
	global $db;

	//Define table options
	$tableoptions = false;
	if (isset($table['options']) && is_array($table['options']) && !empty($table['options'])) {
		$tableoptions = $table['options'];
	}

	//Drop all previous indexes
	$message.='Dropping existing indexes';
	$ListIndex = $db->GetAll("SHOW INDEX FROM `{$table['name']}`");
	if (is_array($ListIndex) && !empty($ListIndex)) {
		foreach ($ListIndex as $index_key => $index) {
			//Keep primary keys
			if ($index['Key_name'] != 'PRIMARY')
				$db->Execute("DROP INDEX `{$index['Key_name']}` ON `{$table['name']}`");
			unset($index, $ListIndex[$index_key]);
		}
	}

	//Modify fields
	$fields=array();
	foreach ($table['fields'] as $field_name => $field_def)
		$fields[] = $field_name . ' ' . $field_def;

	$message.='<br/>Modifying fields, adding new fields';
	$dict = NewDataDictionary($db);
	$sql_array = $dict->ChangeTableSQL($table['name'], implode(',', $fields), $tableoptions);
	$created = $dict->ExecuteSQLArray($sql_array,false);
	if ($created != 2)
	{
		$message .= 'Error type:'.$created.'<br/>';
		$message .= 'Error code:'.$db->ErrorNo().'<br/>';
		$message .= $db->ErrorMsg().'<br/>';
		$status = -1;
		return array('message'=>$message, 'status'=>$status);
	}

	//Create indexes
	$message.='<br/>Create Indexes';
	if (isset($table['indexes']) && is_array($table['indexes']))
	{
		$indexes_existing = $db->MetaIndexes($table['name']);
		foreach ($table['indexes'] as $index_name => $index_def)
		{
			$index_name = $table['name'] . '_' . $index_name . '_IDX';
			$index_opts = array();
			if (is_array($index_def))
			{
				$index_fields = $index_def[0];
				$index_opts = explode(' ', $index_def[1]);
			}
			else
				$index_fields = $index_def;

			if (array_key_exists($index_name, $indexes_existing) || array_key_exists(strtolower($index_name), $indexes_existing))
				if ($sql_array = $dict->CreateIndexSQL($index_name, $table['name'], $index_fields, array_merge($index_opts, array('DROP'))))
					$dict->ExecuteSQLArray($sql_array);

			$created = 0;
			if ($sql_array = $dict->CreateIndexSQL($index_name, $table['name'], $index_fields, $index_opts))
				$created = $dict->ExecuteSQLArray($sql_array);

			if ($created != 2)
			{
				$message .= $db->ErrorMsg().'<br/>';
				$status = -1;
				return array('message'=>$message, 'status'=>$status);
			}

			unset($sql_array, $index_name, $index_opts, $index_fields, $index_def, $index_name);
		}
	}

	return array('status'=>$status, 'message'=>$message);
}
