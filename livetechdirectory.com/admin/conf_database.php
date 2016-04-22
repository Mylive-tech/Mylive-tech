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
require_once '../libs/pclzip/pclzip.lib.php';
require_once '../libs/adodb/tohtml.inc.php';

//Disable any caching by the browser
disable_browser_cache();

//Clear compiled template file
$tpl->clear_compiled_tpl('admin/conf_database.tpl');

//Clear unneeded sessions
if (empty ($_POST['db-backup-submit']) && empty ($_POST['submitAddfields']) && empty ($_POST['sql-query-submit']))
{
   if (isset($_SESSION['SmartyPaginate']))
      unset ($_SESSION['SmartyPaginate']);
   if (isset($_SESSION['values']))
      unset ($_SESSION['values']);
}

//Get table list and status informations
$tblStatusSQL = "SHOW TABLE STATUS FROM ".DB_NAME." LIKE '%".TABLE_PREFIX."%'";
$tableInfoList = $db->GetAssoc($tblStatusSQL);

if (is_array ($tableInfoList) && !empty ($tableInfoList))
{
   $tpl->assign('tableInfoList', $tableInfoList);
}
else
{
   unset ($tableInfoList);
}

//Run database operations
$operationAction = (!empty ($_REQUEST['action']) ? strtolower (trim ($_REQUEST['action'])) : '');

if (!empty ($operationAction) && isset ($tableInfoList))
{
   //Get table name
   $_REQUEST['tbl'] = (isset ($_REQUEST['tbl']) ? trim (urldecode ($_REQUEST['tbl'])) : '');
   $opTable = (!empty ($_REQUEST['tbl']) && array_key_exists ($_REQUEST['tbl'], $tableInfoList) ? $_REQUEST['tbl'] : '');

   if (!empty ($opTable))
   {
      switch ($operationAction)
      {
         case 'optimize' :
            //Optimize table
            $db->Execute("OPTIMIZE TABLE ".DB_NAME.".".$opTable);
            break;
         case 'flush' :
            //Flush table
            $db->Execute("FLUSH TABLE ".DB_NAME.".".$opTable);
            break;
         case 'repair' :
            //Repair table
            $db->Execute("REPAIR TABLE ".DB_NAME.".".$opTable);
            break;
         default       :
            break;
      }
   }
}

$default_file_name = "database_backup_".date ("Y-m-d");
$default_folder    = INSTALL_PATH."backup/"; /* !!include the slash at the end!! */

$_POST['backup_file']   = (isset ($_POST['backup_file'])   && !empty ($_POST['backup_file']))   ? $_POST['backup_file']   : $default_file_name;
$_POST['backup_folder'] = (isset ($_POST['backup_folder']) && !empty ($_POST['backup_folder'])) ? $_POST['backup_folder'] : $default_folder;
$_POST['compression']   = (isset ($_POST['compression'])   && !empty ($_POST['compression']))   ? $_POST['compression']   : 'sql';
$db_backup_file_name   .= $_POST['backup_file'].".sql"; //add filename and file extension

   if (!file_exists ($_POST['backup_folder']))
      if (@ mkdir ($_POST['backup_folder']))
         @ chmod ($_POST['backup_folder'], 0666);

   $backup_folder_validation = folder_status($_POST['backup_folder']);
   $tpl->assign('backup_folder_validation', $backup_folder_validation);

      $db_backup_disk_file = $_POST['backup_folder'].$db_backup_file_name;
      if (true == $backup_folder_validation)
      {
         @ touch ($db_backup_disk_file);
         @ chmod ($db_backup_disk_file, 0666);
         $backup_file_validation = file_status($db_backup_disk_file);
         $tpl->assign('backup_file_validation', $backup_file_validation);
         if($backup_file_validation == true)
            $try_create_backup = true;
         else
            $try_create_backup = false;
      }

if (!isset ($_POST['backup_to_email']) || empty ($_POST['backup_to_email']))
{
   $_POST['backup_to_email'] = $db->GetOne("SELECT `EMAIL` FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' AND `ACTIVE` = '1' ".(!empty ($_SESSION['phpld']['adminpanel']['id']) ? ' AND `ID` = '.$db->qstr($_SESSION['phpld']['adminpanel']['id']) : '')." ORDER BY `ID` ASC");
}

//RALUCA: JQuery validation related
$validators_conf_backup = array(
	'rules' => array(
		'backup_folder' => array(
			'required' => true
		),
		'backup_file' => array(
			'required' => true
		),
		'backup_options' => array(
			'required' => true
		),
		'backup_to_email' => array(
			'email' => true
		),
		'compression' => array(
			'required' => true
		)
	)
);

$vld_conf_backup = json_custom_encode($validators_conf_backup);
$tpl->assign('validators_conf_backup', $vld_conf_backup);

$validator_conf_backup = new Validator($validators_conf_backup);
//RALUCA: end of JQuery validation related

if (empty ($_POST['db-backup-submit'])) {
} else {

	//RALUCA: JQuery validation related - server side.
   $validator_conf_backup = new Validator($validators_conf_backup);
   $validator_conf_backup_res = $validator_conf_backup->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_conf_backup_res))
   {
      $backup_content = db_backup();

      if (!write_to_file($db_backup_disk_file, $backup_content))
         $backup_created = false;
      else
      {
         $backup_created = true;
         @ chmod ($db_backup_disk_file, 0755);
         //unset ($_POST, $_REQUEST);
      }
      unset ($backup_content);

      if (isset ($_POST['compression']) && $_POST['compression'] == 'zip')
      {
         $archive = new PclZip($db_backup_disk_file.".zip");
         $archive->create($db_backup_disk_file, PCLZIP_OPT_REMOVE_ALL_PATH);
         @ unlink ($db_backup_disk_file);
         $db_backup_disk_file .= ".zip";
         $db_backup_file_name .= ".zip";
         @ chmod ($db_backup_disk_file, 0755);
      }

      $tpl->assign('backup_created', $backup_created);

      switch (strtolower ($_REQUEST['backup_options']))
      {
         case 'download' :
            //Prevent errors and header errors
            @ error_reporting (E_ERROR | E_WARNING | E_PARSE);
            //Disable any caching by the browser
            disable_browser_cache();
            @ header ('Content-Description: File Transfer');
            @ header ('Content-Type: application/octet-stream');
            @ header ('Content-Length: ' . @ filesize ($db_backup_disk_file));
            @ header ("Content-Disposition: attachment; filename={$db_backup_file_name}");
            @ readfile ($db_backup_disk_file);
            @ unlink ($db_backup_disk_file);
            break;

         case 'email' :
            $tpl->assign('request_email', true);
            $mail = get_emailer_admin();
            $mail->Body    = _L('Your database backup was sent to you via attachment!');
            $mail->Subject = _L('Database backup');
            $mail->AddAddress($_POST['backup_to_email']);
            $mail->AddAttachment($db_backup_disk_file, $db_backup_file_name);
            if(!$mail->Send())
               $tpl->assign('backup_emailed', false);
            else
               $tpl->assign('backup_emailed', true);

            @ unlink ($db_backup_disk_file);
            break;

         default :
            //Nothing to do,just fallback
            break;
      }

      $tpl->assign('show_backup_results', true);
   }
}

//RALUCA: JQuery validation related
$validators_query_db = array(
	'rules' => array(
		'sql_query' => array(
			'required' => true
		)
	)
);

$vld_query_db = json_custom_encode($validators_query_db);
$tpl->assign('validators_query_db', $vld_query_db);

$validator_query_db = new Validator($validators_query_db);
//RALUCA: end of JQuery validation related

if (empty ($_POST['sql-query-submit'])) {
} else {
	
	//RALUCA: JQuery validation related - server side.
   $validator_query_db = new Validator($validators_query_db);
   $validator_query_db_res = $validator_query_db->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_query_db_res))
   {
      $_REQUEST['sql_query'] = (!empty ($_REQUEST['sql_query']) ? trim (urldecode ($_REQUEST['sql_query'])) : '');

      //Execute user query
      $rs = $db->Execute($_REQUEST['sql_query']);

      if (!$rs)
      {
         $tpl->assign('sql_error_msg', $db->ErrorMsg());
         $tpl->assign('sql_error_nr', $db->ErrorNo());
      }
      else
      {
         $tpl->assign('show_query_results', true);

         $affectedRows = $db->Affected_Rows();
         $tpl->assign('affected_rows', $affectedRows);

         if ($affectedRows < 50)
         {
            $rs2html = rs2html($rs, false, false, true, false);
            $tpl->assign('rs2html', $rs2html);
         }
         //unset ($_POST, $_REQUEST);
      }
   }
}

$dbtableList = array (
                  $tables['link']['name']            => _L('Link table'),
                  $tables['link_review']['name']     => _L('Link review table'),
                  $tables['category']['name']        => _L('Category table'),
                  $tables['user']['name']            => _L('User table'),
                  $tables['user_permission']['name'] => _L('User permissions table'),
                  $tables['payment']['name']         => _L('Payment table'),
                  $tables['email']['name']           => _L('Email table'),
                  $tables['email_tpl']['name']       => _L('Email templates table'),
                  $tables['banlist']['name']         => _L('Banlist table'),
                  $tables['hitcount']['name']        => _L('Hit counter table')
            );
$tpl->assign('dbtableList', $dbtableList);


$fieldTypeList = array (
                'TEXT'       => _L('TEXT'),
                'CHAR'       => _L('CHAR'),
                'VARCHAR'    => _L('VARCHAR') . ' - ' . _L('Short text'),
                'BLOB'       => _L('BLOB (= binary large object)') . ' - ' . _L('if possible use TEXT'),

                'INT'        => _L('INT'). ' - ' ._L('Numeric values only'),
                'TINYINT'    => _L('TINYINT') . ' - ' . _L('Suitable storing numeric booleans (0 or 1)'),

                'TIMESTAMP'  => _L('TIMESTAMP') . ' - '. _L('YYYY-MM-DD HH:MM:SS'),
                'DATETIME'   => _L('DATETIME'),
                'DATE'       => _L('DATE'),
                'TIME'       => _L('TIME'),
                'YEAR'       => _L('YEAR'),

                'ENUM'       => _L('ENUM'),
                'SET'        => _L('SET'),
                'BOOL'       => _L('BOOL')
             );
$tpl->assign('fieldTypeList', $fieldTypeList);

$fieldNullList = array (0 => _L('Not NULL'), 1 => _L('NULL'));
$tpl->assign('fieldNullList', $fieldNullList);


//RALUCA: JQuery validation related
$validators_addfields = array(
	'rules' => array(
		'dbtable' => array(
			'required' => true
		),
		'dbfieldname' => array(
			'required' => true
		),
		'dbfieldtype' => array(
			'required' => true
		),
		'dbfieldlength' => array(
			'digits' => true
		),
		'dbfielddefault' => array(
			'required' => true
		)
	)
);

$vld_addfields = json_custom_encode($validators_addfields);
$tpl->assign('validators_addfields', $vld_addfields);

$validator_addfields = new Validator($validators_addfields);
//RALUCA: end of JQuery validation related


if (empty ($_POST['submitAddfields'])) {
}
else
{
	//RALUCA: JQuery validation related - server side.
   $validator_addfields = new Validator($validators_addfields);
   $validator_addfields_res = $validator_addfields->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_query_db_res))
   {
      //Get info for new DB field and correct some values
      $noFieldLength = array ('BOOL', 'SET', 'ENUM', 'TIME', 'DATE', 'DATETIME');

      $data = array ();
      $data['dbtable']        = (!empty ($_POST['dbtable']) ? trim (urldecode ($_POST['dbtable'])) : '');
      $data['dbfieldname']    = (!empty ($_POST['dbfieldname']) ? trim (urldecode ($_POST['dbfieldname'])) : '');
      $data['dbfieldtype']    = (!empty ($_POST['dbfieldtype']) ? trim (urldecode ($_POST['dbfieldtype'])) : '');
      $data['dbfieldlength']  = (!empty ($_POST['dbfieldlength']) && preg_match ('`^[\d]+$`', $_POST['dbfieldlength']) && !in_array ($data['dbfieldtype'], $noFieldLength) ? intval (trim ($_POST['dbfieldlength'])) : '');
      $data['dbfieldnull']    = (isset ($_POST['dbfieldnull']) && $_POST['dbfieldnull'] == 1 ? 1 : 0);
      $data['dbfielddefault'] = (isset ($_POST['dbfielddefault']) ? trim (urldecode ($_POST['dbfielddefault'])) : '');

      //Build SQL command for adding field
      $addfieldSQL = "ALTER TABLE `{$data['dbtable']}` ADD `{$data['dbfieldname']}` {$data['dbfieldtype']}".(!empty ($data['dbfieldlength']) ? "(".$data['dbfieldlength'].")" : '').($data['dbfieldnull'] == 1 ? " NULL" : " NOT NULL").(0 < strlen ($data['dbfielddefault']) ? " DEFAULT ".$db->qstr($data['dbfielddefault']) : '');

      $tpl->assign('addfieldSQL', $addfieldSQL);

      //Try creating new field
      if ($db->Execute($addfieldSQL))
      {
         $tpl->assign('addfieldstatus', 1);

         $tplExample = '<label for="'.$data['dbfieldname'].'">{l}'._L('Add your information').'{/l}</label>'."\n";
         $tplExample .= '<input type="text" id="'.$data['dbfieldname'].'" name="'.$data['dbfieldname'].'" value="{$'.$data['dbfieldname'].'|escape|trim}" />';

         $tpl->assign('tplExample', $tplExample);
         $tpl->assign('previewDbFieldName', $data['dbfieldname']);

         //unset ($_POST, $_REQUEST, $data);
         unset ($data);
      }
      else
      {
         //An error occured
         $tpl->assign('addfieldstatus', 0);
         $tpl->assign('sql_error', $db->ErrorMsg());
      }
   }
   else
   {
      $tpl->assign($_POST);
   }
}

/**
 * Check if a MySQL field is numeric or not
 */
function check_mysql_numeric_field($field_type)
{
   $field_type = strtolower ($field_type);
   if( (0 === strpos ($field_type, 'tinyint')) ||
       (0 === strpos ($field_type, 'smallint')) ||
       (0 === strpos ($field_type, 'mediumint')) ||
       (0 === strpos ($field_type, 'int')) ||
       (0 === strpos ($field_type, 'bigint')) )
         return 1;
       else
         return 0;
}

/**
 * Create a full backup of phpLD's database tables
 */
function db_backup()
{
   /* Highly inspired by phpMyAdmin */

   global $db, $tables;

   /*Get phpLD tables out of DB. We don't use the global $tables var, because we want the *_SEQ tables too*/
   $tables = $db->GetCol("SHOW TABLES FROM `".DB_NAME."` LIKE '".TABLE_PREFIX."%'");

   /* Header information */
   $output = "";
   $output .= "# --------------------------------------------------------\n";
   $output .= "# PHP Link Directory SQL Dump\n";
   $output .= "# http://www.phplinkdirectory.com/\n";
   $output .= "#\n";
   $output .= "# Generated: ".date("l j. F Y H:i T")."\n";
   $output .= "# Hostname: ".DB_HOST."\n";
   $output .= "# Database: ".DB_NAME."\n";
   $output .= "# --------------------------------------------------------\n";
   $output .= "\n\n\n\n";

   foreach ($tables as $table_key => $table_name)
   {
      $table_structure = $db->GetAssoc("DESCRIBE {$table_name}");

      /*Drop table*/
      $output .= "# --------------------------------------------------------\n";
      $output .= "# Drop any existing table `{$table_name}`\n";
      $output .= "# --------------------------------------------------------\n";
      $output .= "DROP TABLE IF EXISTS `{$table_name}`;";
      $output .= "\n\n";
      /*Create table*/
      $table_sql_create = $db->GetRow("SHOW CREATE TABLE `{$table_name}`");
      $table_status = $db->GetRow("SHOW TABLE STATUS LIKE ".$db->qstr($table_name));
      $auto_increment = (isset ($table_status['Auto_increment']) ? " AUTO_INCREMENT=".$table_status['Auto_increment'] : '');
      $output .= "# --------------------------------------------------------\n";
      $output .= "# Table structure for table `{$table_name}`\n";
      $output .= "# --------------------------------------------------------\n";
      $output .= $table_sql_create['Create Table'].$auto_increment." ;";
      $output .= "\n\n";
      /* Table entries */
      $output .= "# --------------------------------------------------------\n";
      $output .= "# Data contents of table `{$table_name}`\n";
      $output .= "# --------------------------------------------------------\n";
      $output .= "\n\n";

      $row_start = 0;
      $row_increment = 100;
      do
      {
         tweak_memory_limit(64);

         $table_data = $db->GetAll("SELECT * FROM `{$table_name}` LIMIT {$row_start}, {$row_increment}");

         $search = array("\x00", "\x0a", "\x0d", "\x1a");
         $replace = array('\0', '\n', '\r', '\Z');

         if ($table_data)
         {
            foreach ($table_data as $k => $row)
            {
               $output .= "INSERT INTO `{$table_name}` (";

               $key_values = array();
               $values = array();
               foreach ($row as $key => $value)
               {
                  $key_values[] = "`{$key}`";
                  if (is_null ($value))
                     $values[] = 'NULL';
                  elseif(check_mysql_numeric_field($table_structure[$key]['Type']))
                     $values[] = trim ($value);
                  else
                     $values[] = "'".trim (str_replace ($search, $replace, sql_addslashes($value)))."'";
               }
               $output .= implode (', ', $key_values) . " ) VALUES (" . implode (', ', $values) . ");\n";
            }
            $row_start += $row_increment;
         }
      } while (count ($table_data) > 0);
   }
   $output = trim ($output);

   return $output;
}

$tpl->assign('backup_disk_file', $db_backup_disk_file);

$tpl->assign($_POST);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_database.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

//Clear compiled template file
$tpl->clear_compiled_tpl('admin/conf_database.tpl');
?>