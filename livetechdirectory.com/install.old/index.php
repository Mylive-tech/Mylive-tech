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


require_once '../include/config.php';
require_once 'include/version.php';
require_once 'include/functions.php';
require_once 'install/config.php';
require_once 'libs/intsmarty/intsmarty.class.php';
require_once 'libs/adodb/adodb.inc.php';
require_once '../Validator.class.php';

//The problem: we need to use js form.submit() function.
//But, all the forms have an <input name="submit"> which is used to test if the form was submitted: if (!empty($_REQUEST['submit'])) { ... }
//These inputs conflicts with the .submit() method. The browsers will raise a js error instead of submitting the form.
//The workaround: all the <input name="submit"> are renamed to <input name="whatever">.
//<input type="hidden" name="formSubmitted" value="1" /> - added to all forms

if (!empty($_REQUEST['formSubmitted']) && empty($_REQUEST['back'])) {
    $_REQUEST['submit'] = $_REQUEST['formSubmitted'];
}
if (!empty($_POST['formSubmitted']) && empty($_POST['back'])) {
    $_POST['submit'] = $_POST['formSubmitted'];
}
if (!empty($_GET['formSubmitted']) && empty($_GET['back'])) {
    $_GET['submit'] = $_GET['formSubmitted'];
}
//----------------------------------------------------------------------------------------------------
//Detect web-server software
define('IS_APACHE', ( strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') || strstr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') ) ? 1 : 0);
define('IS_IIS', strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0);

$curUrlOld = curPageURL();
$curUrl = $curUrlOld;

if (!strpos($curUrl, 'index.php')) {
    if (!strpos($curUrl, "install/")) {
        $curUrl .= "/index.php?step=1";
    } else {
        $curUrl .= "index.php?step=1";
    }
}

if ($curUrl != $curUrlOld)
    http_custom_redirect($curUrl);

$fn = INSTALL_PATH . 'temp/templates';
if (!is_writable($fn))
    @ chmod($fn, 0777);

if (!is_writable($fn)) {
    exit("<strong>The installer cannot start!</strong>\n
         <br />\n
         Please make sure that the folder <code>" . $fn . "</code> is writeable by the user the webserver runs under.");
}

//Create dummy variable to test if session variables are supported
session_start();
$_SESSION['sessionTest'] = 1;
session_write_close();

session_start();

$step = (!empty($_REQUEST['step']) && preg_match('`^[\d]+$`', $_REQUEST['step']) ? intval($_REQUEST['step']) : 1);
$step = ($step < 1 || $step > 7 ? 1 : $step); //Do not allow more/less steps than default
$language = (!empty($_SESSION['language']) ? $_SESSION['language'] : 'en');
$clear_all = 0;

//Determine core templates folder
$core_tpl = (defined('CORE_TEMPLATES') && CORE_TEMPLATES != '' ? CORE_TEMPLATES : 'Core');

if (!is_dir('../templates/' . $core_tpl) || !is_dir('../templates/' . $core_tpl . '/install'))
    exit("<strong>The installer cannot find it's template files!</strong>\n
         <br />
         Please make sure that the folders <code>templates/{$core_tpl}/</code> and <code>templates/{$core_tpl}/install/</code> are available and readable by the user the webserver runs under.");

$tpl = new IntSmarty($language);
$tpl->template_dir = '../templates/' . $core_tpl;
$tpl->compile_dir = '../temp/templates';
$tpl->cache_dir = '../temp/cache';
$tpl->compile_check = false;

$path = request_uri();
$path_parts = pathinfo($path);



$path_parts['dirname'] = preg_replace('`/install[\.]*`i', '', $path_parts['dirname']);

define('DOC_ROOT', $path_parts['dirname']);
define('INSTALL_ROOT', DOC_ROOT . '/install');

define('TEMPLATE_PATH', 'templates/admin');
define('FULL_TEMPLATE_PATH', DOC_ROOT . '/templates/admin');


if (!$_SESSION['nologin'] && !isset($_SESSION['phpld']['adminpanel']['id']) && defined('DB_DRIVER') && defined('DB_HOST')) {
	$db = ADONewConnection(DB_DRIVER);
	$db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $check = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' AND `ACTIVE` = '1'");

    if (!empty($check)) {
        $_SESSION['return'] = DOC_ROOT . '/install/index.php';
        http_custom_redirect(DOC_ROOT . '/admin/login.php');
    }
    else
        $_SESSION['nologin'] = true;
}
else
    $_SESSION['nologin'] = true;

//Assign fatal error tracker
$fatal = 0;

switch ($step) {
    //SELECT LANGUAGE
    case 1 :
        $tpl->assign('languages', select_lang());
        $tpl->assign('btn_next', 1);
        $tpl->assign('title', _L('Select Language'));
         $tpl->assign('install', $_SESSION['install']);

        if (empty($_POST['submit'])) {
            //Clear the entire template cache
            $tpl->clear_all_cache();

            //Clear all compiled template files
            $tpl->clear_compiled_tpl();
        } elseif (!empty($_POST['submit']) && !empty($_POST['language'])) {
            //Store language preferences
            $_SESSION['language'] = (!empty($_POST['language']) ? $_POST['language'] : 'en');
            
            //Store Installation type
            $_SESSION['install'] = (!empty($_POST['install']) ? $_POST['install'] : 'new');

            //Redirect to next step
            $step++;
            http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
        }
        $tpl->assign($_POST);
        break;

    //CHECK REQUIREMENTS
    case 2 :
        //Check requirements
        $requirements = check_requirements();

        //Check for important requirements
        foreach ($requirements as $key => $req) {
            if (isset($req['fatal']) && $req['fatal']) {
                //Found at least one fatal error
                $fatal = 1;
            }
        }

        $tpl->assign('req', $requirements);
        if ($fatal != 1) {
            $tpl->assign('btn_next', 1);
        }
        $tpl->assign('btn_back', 1);
        $tpl->assign('fatal', $fatal);
        $tpl->assign('title', _L('Welcome'));

        if (!empty($_POST['submit'])) {
            $step++;
            http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
        } elseif (!empty($_POST['back'])) {
            $step--;
            http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
        }

        break;

    //INSTALL/UPDATE DATABASE & CONFIGURATION
    case 3 :

        //This step needs more resources
        //Try increase them
        tweak_memory_limit(64); //64Mb memory
        tweak_time_limit(450); //7.5 minutes

        $tpl->assign('db_drivers', $db_drivers);
        $tpl->assign('btn_next', 1);
        $tpl->assign('btn_back', 1);
        $tpl->assignLang('title', _L('Database Settings'));

        if (empty($_POST['submit']) && empty($_POST['back'])) {
            $_SESSION['values'] = array('db_driver' => 'mysql');
            $_SESSION['values']['db_driver'] = 'mysql';
            $_SESSION['values']['db_host'] = (defined('DB_HOST') ? DB_HOST : '');
            $_SESSION['values']['db_name'] = (defined('DB_NAME') ? DB_NAME : '');
            $_SESSION['values']['db_user'] = (defined('DB_USER') ? DB_USER : '');
            $_SESSION['values']['db_password'] = (defined('DB_PASSWORD') ? DB_PASSWORD : '');
        } elseif (!empty($_POST['submit'])) {
            $valid = (!empty($_POST['db_host']) && !empty($_POST['db_name']) && !empty($_POST['db_user'])) ? true : false;
            if ($valid) {
                $db_details = array();
                $db_details['db_driver'] = 'mysql';
                $db_details['db_host'] = (!empty($_POST['db_host']) ? $_POST['db_host'] : '');
                $db_details['db_name'] = (!empty($_POST['db_name']) ? $_POST['db_name'] : '');
                $db_details['db_user'] = (!empty($_POST['db_user']) ? $_POST['db_user'] : '');
                $db_details['db_password'] = (!empty($_POST['db_password']) ? $_POST['db_password'] : null);
                $db_details['language'] = (!empty($_SESSION['language']) ? $_SESSION['language'] : 'en');

                if (install_db($db_details)) {

                    upgrade_user_table($db_details);
                    install_language($db_details);
                    $step++;
                    http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
                }
            }
            else
                $tpl->assign($_POST);
        }
        elseif (!empty($_POST['back'])) {
            $step--;
            http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
        }

        $tpl->assign($_SESSION['values']);

        break;

    case 4:
        
        if($_SESSION['install'] != 'upgrate')
            http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . ++$step);

        $db = ADONewConnection(DB_DRIVER);
        if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
        {
            $errors = false;
            $dict = NewDataDictionary($db);
            // Get existing types
            $link_types = $db->GetAll("SELECT NAME,ID FROM `{$tables['link_type']['name']}`");
            $new_link_types = $tables['link_type']['data'];
            $tpl->assign('link_types', $link_types);
            $tpl->assign('new_link_types', $new_link_types);

            // Create new types
            if (!empty($_POST['submit'])) {
                $types = $_POST['TYPE'];
                // Rename old table
                $db->Execute('DROP TABLE '.$tables['link_type']['name'].'_BACKUP IF EXISTS;');
                $db->Execute('ALTER TABLE '.$tables['link_type']['name'].' RENAME TO '.$tables['link_type']['name'].'_BACKUP;');
                $fields = array();

                foreach ($tables['link_type']['fields'] as $field_name => $field_def) {
                    $fields[] = $field_name . ' ' . $field_def;
                }

                // Create new one
                $sql_array = $dict->ChangeTableSQL($tables['link_type']['name'], implode(',', $fields), $tables['link_type']['options']);
                $tableCreated = $dict->ExecuteSQLArray($sql_array);
                if ($tableCreated != 2) {
                    $errors = true;
                    $db->Execute('ALTER TABLE '.$tables['link_type']['name'].'_BACKUP RENAME TO '.$tables['link_type']['name'].';');
                    $tpl->assign('form_error', 'INSTALL_ERROR_CREATE');
                    $tpl->assign('sql_error', $db->ErrorMsg());
                } else {
                    foreach ($tables['link_type']['data'] as $row) {
                        $db->AutoExecute($tables['link_type']['name'], $row, 'INSERT', false, true, false);
                    }

                    $oldNewMap = array();

                    // We have to create new map with LINK_TYPE ID outside of usual range, as we can overwrite rows,
                    // that was just updated
                    foreach ($types as $old=>$new) {
                        $newId = rand(300, 10000);
                        $oldNewMap[] = array('old'=>$new, 'new'=>$newId);
                        $db->Execute('UPDATE '.$tables['link']['name'].' SET LINK_TYPE = '.$newId.' WHERE LINK_TYPE = '.$old);
                     //   var_dump('UPDATE '.$tables['link']['name'].' SET LINK_TYPE = '.$newId.' WHERE LINK_TYPE = '.$old);
                    }

                    foreach ($oldNewMap as $map) {
                        $db->Execute('UPDATE '.$tables['link']['name'].' SET LINK_TYPE = '.$map['old'].' WHERE LINK_TYPE = '.$map['new']);
                      //  var_dump('UPDATE '.$tables['link']['name'].' SET LINK_TYPE = '.$map['old'].' WHERE LINK_TYPE = '.$map['new']);
                    }
                }


                // Remap existing links
//                var_dump($types);die();
                if ($errors == false) {
                    http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . ++$step);
                }
            } elseif (!empty($_POST['back'])) {
                http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . --$step);
            }
        }
        $tpl->assign('btn_next', 1);
        $tpl->assign('btn_back', 1);
        $tpl->assignLang('title', _L('Map old link types to new ones'));

        break;

    //POPULATE DATABASE
    case 5 :
        //This step needs more resources
        //Try increase them
        tweak_memory_limit(64); //64Mb memory
        tweak_time_limit(450); //7.5 minutes

        $tpl->assign('db_drivers', $db_drivers);
        $tpl->assign('btn_next', 1);
        $tpl->assign('btn_back', 1);
        $tpl->assignLang('title', _L('Populate Database'));

        if (empty($_POST['submit']) && empty($_POST['back'])) {

            //Default values
            $populate_config = 1;
            $tpl->assign('populate_config', $populate_config);

            $populate_emailtpl = 1;
            $tpl->assign('populate_emailtpl', $populate_emailtpl);

            $associate_emailtpl = 0;
            $tpl->assign('associate_emailtpl', $associate_emailtpl);

        } elseif (!empty($_POST['submit'])) {
            $errors = 0;

            $populate_config = 1; //always
            $tpl->assign('populate_config', $populate_config);

            $populate_emailtpl = (isset($_POST['populate_emailtpl']) ? 1 : 0);
            $tpl->assign('populate_emailtpl', $populate_emailtpl);

            $associate_emailtpl = (isset($_POST['associate_emailtpl']) ? 1 : 0);
            $tpl->assign('associate_emailtpl', $associate_emailtpl);

            //Connect to database

            $db = ADONewConnection(DB_DRIVER);

            if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
            {

                $db->SetFetchMode(ADODB_FETCH_ASSOC);

                //Connect to database
                $over40 = 0;

                $exist = $db->MetaTables('TABLES');

                foreach ($exist as $k => $v) {

                    $exist[$k] = strtolower($v);

                }

                if (in_array(strtolower($tables['submit_item']['name']), $exist)) {

                    $content = $db->GetAll("SELECT * FROM `{$tables['submit_item']['name']}`");

                    if (count($content) > 0) {

                        $over40 = 1;


                        //Insert Additional Submit items from tables data array

                        $submit_fields_names = $db->GetAssoc("SELECT FIELD_NAME,FIELD_NAME as FIELD FROM `{$tables['submit_item']['name']}`");
                        $link_types = $db->GetAssoc("SELECT NAME,ID FROM `{$tables['link_type']['name']}`");
                        $validators_list = $db->GetAssoc("SELECT ID,NAME FROM `{$tables['validator']['name']}`");

                        foreach($tables['submit_item']['data'] as $k => $v)
                        {
                            unset($submit_item_id);
                            if(!in_array($v['FIELD_NAME'],$submit_fields_names))
                            {
                                $v['ID']='';
                                //Insert Additional Submit items
                                $db->AutoExecute($tables['submit_item']['name'], $v, 'INSERT', false, true, false);
                                $submit_item_id = mysql_insert_id();
                                //Insert  Status of Additional Submit items
                                foreach($link_types as $type_key => $type_val)
                                {
                                    $staus_row = array('ITEM_ID' => $submit_item_id, 'LINK_TYPE_ID' => $type_val, 'STATUS' => '0');
                                    $db->AutoExecute($tables['submit_item_status']['name'], $staus_row, 'INSERT', false, true, false);
                                }
                            }
                        }

                        //Insert Additional Validators
                        foreach($tables['validator']['data'] as $vld_key => $vld_val)
                        {
                            unset($submit_item_id);
                            if(!in_array($vld_val['NAME'],$validators_list))
                            {
                                $vld_val['ID'] = '';
                                $db->AutoExecute($tables['validator']['name'], $vld_val, 'INSERT', false, true, false);
                            }
                        }

                    }
                }


                //delete data from hitcount table for speed purposes
                $result = $db->Execute("DELETE FROM `{$tables['hitcount']['name']}` WHERE DATE_ADD(`LAST_HIT`, INTERVAL 24 HOUR) <now()");
                if (is_array($tables['submit_item_status']['data']) && !empty($tables['submit_item_status']['data']) && $over40 != 1) {
                    $executed = $db->Execute("DELETE FROM `{$tables['submit_item_status']['name']}`");
                    foreach ($tables['submit_item_status']['data'] as $row) {
                        $db->AutoExecute($tables['submit_item_status']['name'], $row, 'INSERT', false, true, false);
                        unset($row);
                    }
                }
                if (is_array($tables['submit_item']['data']) && !empty($tables['submit_item']['data']) && $over40 != 1) {
                    $executed = $db->Execute("DELETE FROM `{$tables['submit_item']['name']}`");
                    foreach ($tables['submit_item']['data'] as $row) {
                        $db->AutoExecute($tables['submit_item']['name'], $row, 'INSERT', false, true, false);
                        unset($row);
                    }
                }
                if (is_array($tables['link_type']['data']) && !empty($tables['link_type']['data']) && $over40 != 1) {
                    $executed = $db->Execute("DELETE FROM `{$tables['link_type']['name']}`");
                    foreach ($tables['link_type']['data'] as $row) {
                        $db->AutoExecute($tables['link_type']['name'], $row, 'INSERT', false, true, false);
                        unset($row);
                    }
                }
                if (is_array($tables['validator']['data']) && !empty($tables['validator']['data']) && $over40 != 1) {
                    $executed = $db->Execute("DELETE FROM `{$tables['validator']['name']}`");
                    foreach ($tables['validator']['data'] as $row) {
                        $db->AutoExecute($tables['validator']['name'], $row, 'INSERT', false, true, false);
                        unset($row);
                    }
                }
                if (is_array($tables['submit_item_validator']['data']) && !empty($tables['submit_item_validator']['data']) && $over40 != 1) {
                    $executed = $db->Execute("DELETE FROM `{$tables['submit_item_validator']['name']}`");
                    foreach ($tables['submit_item_validator']['data'] as $row) {
                        $db->AutoExecute($tables['submit_item_validator']['name'], $row, 'INSERT', false, true, false);
                        unset($row);
                    }
                }
                if (is_array($tables['user_default_actions']['data']) && !empty($tables['user_default_actions']['data'])) {
                    $executed = $db->Execute("DELETE FROM `{$tables['user_default_actions']['name']}`");
                    foreach ($tables['user_default_actions']['data'] as $row) {
                        if (!$db->AutoExecute($tables['user_default_actions']['name'], $row, 'INSERT', false, true, false)) {
                            $errors++;
                            $tpl->assign('form_error', 'INSTALL_ERROR_CREATE');
                            $tpl->assign('sql_error', $db->ErrorMsg());
                        }
                        unset($row);
                    }
                }
                if (is_array($tables['widget_zone_types']['data']) && !empty($tables['widget_zone_types']['data'])) {
                    $executed = $db->Execute("DELETE FROM `{$tables['widget_zone_types']['name']}`");
                    foreach ($tables['widget_zone_types']['data'] as $row) {
                        if (!$db->AutoExecute($tables['widget_zone_types']['name'], $row, 'INSERT', false, true, false)) {
                            $errors++;
                            $tpl->assign('form_error', 'INSTALL_ERROR_CREATE');
                            $tpl->assign('sql_error', $db->ErrorMsg());
                        }
                        unset($row);
                    }
                }
                if (is_array($tables['widget_activated']['data']) && !empty($tables['widget_activated']['data'])) {
                    foreach ($tables['widget_activated']['data'] as $row) {
                        $already = $db->GetRow("SELECT * FROM `{$tables['widget_activated']['name']}`
                                    WHERE `NAME` = " . $db->qstr($row['NAME']) . " AND `ZONE` = " . $db->qstr($row['ZONE']));
                        if ($already['NAME'] == '') {
                            $db->AutoExecute($tables['widget_activated']['name'], $row, 'INSERT', false, true, false);
                        }
                        unset($row);
                    }
                }
                if (is_array($tables['widget']['data']) && !empty($tables['widget']['data'])) {
                    foreach ($tables['widget']['data'] as $row) {
                        $already = $db->GetRow("SELECT * FROM `{$tables['widget']['name']}`
                                    WHERE `NAME` = " . $db->qstr($row['NAME']) . " AND `TYPE` = " . $db->qstr($row['TYPE']));
                        if ($already['NAME'] == '') {
                            $db->AutoExecute($tables['widget']['name'], $row, 'INSERT', false, true, false);
                        }
                        unset($row);
                    }
                }
                if (is_array($tables['widget_zones']['data']) && !empty($tables['widget_zones']['data'])) {
                    $executed = $db->Execute("DELETE FROM `{$tables['widget_zones']['name']}`");
                    foreach ($tables['widget_zones']['data'] as $row) {
                        if (!$db->AutoExecute($tables['widget_zones']['name'], $row, 'INSERT', false, true, false)) {
                            $errors++;
                            $tpl->assign('form_error', 'INSTALL_ERROR_CREATE');
                            $tpl->assign('sql_error', $db->ErrorMsg());
                        }
                        unset($row);
                    }
                }
                if ($populate_config) {
                    if (is_array($tables['config']['data']) && !empty($tables['config']['data'])) {
                        foreach ($tables['config']['data'] as $row) {
                            $sql = "SELECT `ID` FROM `{$tables['config']['name']}` WHERE `ID` = '{$row['ID']}'";
                            $rs = $db->SelectLimit($sql, 1);
                            if ($rs && $rs->EOF) {
                                if (!$db->AutoExecute($tables['config']['name'], $row, 'INSERT', false, true, false)) {
                                    $errors++;
                                    $tpl->assign('form_error', 'INSTALL_ERROR_CREATE');
                                    $tpl->assign('sql_error', $db->ErrorMsg());
                                }
                            }
                            unset($rs, $row, $sql);
                        }
//                  mysql_free_result();
                    }
                    if (is_array($tables['menu_items']['data']) && !empty($tables['menu_items']['data'])) {
                        $sql = "SELECT `ID` FROM `{$tables['menu_items']['name']}` ";
                        $rs = $db->GetAll($sql);
                        if(empty($rs)){
                            foreach ($tables['menu_items']['data'] as $row) {

                                if (!$db->AutoExecute($tables['menu_items']['name'], $row, 'INSERT', false, true, false)) {
                                    $errors++;
                                    $tpl->assign('form_error', 'INSTALL_ERROR_CREATE');
                                    $tpl->assign('sql_error', $db->ErrorMsg());

                                }

                            }
                        }
                        unset($rs, $row, $sql);
//                  mysql_free_result();
                    }

                    //Force updating/removing some config variables
                    $sql_array = array();
                    $sql_array[] = "DELETE FROM `{$tables['config']['name']}` WHERE `ID` = " . $db->qstr('ENABLE_ID');

                    $sql_array[] = "UPDATE `{$tables['config']['name']}` SET `VALUE` = " . $db->qstr(CURRENT_VERSION) . " WHERE `ID` = 'VERSION' LIMIT 1";
                    $sql_array[] = "UPDATE `{$tables['config']['name']}` SET `VALUE` = 'Allure' WHERE `ID` = 'TEMPLATE' LIMIT 1";
                    $sql_array[] = "UPDATE `{$tables['config']['name']}` SET `VALUE` = '" . $rand_pass . "' WHERE `ID` = 'SECRET_SESSION_PASSWORD' LIMIT 1";
                    $sql_array[] = "UPDATE `{$tables['config']['name']}` SET `VALUE` = 'DefaultAdmin' WHERE `ID` = 'ADMIN_TEMPLATE' LIMIT 1";
                    $sql_array[] = "UPDATE `{$tables['link_type']['name']}` SET `PRICE`= (SELECT `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` = 'PAY_NORMAL') WHERE `ID`='2'";
                    $sql_array[] = "UPDATE `{$tables['link_type']['name']}` SET `PRICE`= (SELECT `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` = 'PAY_FEATURED') WHERE `ID`='4'";
//             $sql_array[] = "UPDATE `{$tables['link_type']['name']}` SET `PRICE`= (SELECT `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` = 'PAY_RECPR') WHERE `ID`='3'";

                    foreach ($sql_array as $key => $sql) {

                        $executed = $db->Execute($sql);

                        unset ($sql_array[$key], $sql);

                    }

                    unset ($sql_array);

                }
                //IF populate_config

                if ($populate_emailtpl) {
                    require_once 'include/email_templates.php';

                    if (is_array ($constant_email_tpl) && !empty ($constant_email_tpl)) {
                        foreach ($constant_email_tpl as $tplType => $template)
                        {
                            $existingEmailTplID = $db->GetOne("SELECT `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` LIKE ".$db->qstr($tplType));
                            $checkSQL = "SELECT COUNT(*) FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($existingEmailTplID);

                            if (empty ($existingEmailTplID) || $db->GetOne($checkSQL) < 1)
                            {
                                //Get ID
                                $id = $db->GenID($tables['email_tpl']['name'].'_SEQ');
                                $template['ID'] = $id;

                                //Add to DB
                                $db->Replace($tables['email_tpl']['name'], $template, 'ID', true);

                                if ($associate_emailtpl)
                                {
                                    $executed = $db->Execute("UPDATE `{$tables['config']['name']}` SET `VALUE` = ".$db->qstr($id)." WHERE `ID` LIKE ".$db->qstr($tplType)." LIMIT 1");
                                }

                                unset ($tplType, $template, $id);
                            }

                            unset ($existingEmailTplID, $checkSQL);
                        }
                    }
                }


                //Optimize database tables [default]
                $toOptimizeTables = $db->GetCol("SHOW TABLES FROM `" . DB_NAME . "` LIKE '" . TABLE_PREFIX . "%'");
                foreach ($toOptimizeTables as $tKey => $tableName) {
                    $db->Execute("OPTIMIZE TABLE " . DB_NAME . "." . $tableName);
                    unset($toOptimizeTables[$tKey], $tableName);
                }
                unset($toOptimizeTables);
            } else {
                $errors++;
                $tpl->assign('form_error', 'INSTALL_ERROR_CONNECT');
                $tpl->assign('sql_error', $db->ErrorMsg());
            }

            if ($errors == 0) {
                $step++;
                http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
            }
        } elseif (!empty($_POST['back'])) {
            $step--;
            if($_SESSION['install'] != 'upgrate')
                $step--;
            
            http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
        }

        break;

    //SAVE ADMINISTRATOR
    case 6 :
        $tpl->assign('btn_next', 1);
        $tpl->assign('btn_back', 1);
        $tpl->assignLang('title', _L('Administrative User'));

        $validators = array(
            'rules' => array(
                'admin_user' => array(
                    adminuser => true
                ),
                'admin_password' => array(
                    minlength => 6,
                    maxlength => 25
                ),
                'admin_passwordc' => array(
                    password_match => true
                )
            ),
            'messages' => array(
            )
        );
        $url = $_SERVER["SERVER_NAME"];

        $tpl->assign('domain',$url);


        if (empty($_POST['submit']) && empty($_POST['back'])) {
            $db = ADONewConnection(DB_DRIVER);
            if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)) {
                $sql = "SELECT `LOGIN`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' AND `ACTIVE` = '1'";
                $admin_update = $db->GetRow($sql);

                if (empty($_SESSION['values']) || !is_array($_SESSION['values']))
                    $_SESSION['values'] = array();
                $_SESSION['values']['admin_user'] = (!empty($admin_update['LOGIN']) ? $admin_update['LOGIN'] : '');
                $_SESSION['values']['admin_name'] = (!empty($admin_update['NAME']) ? $admin_update['NAME'] : '');
                $_SESSION['values']['admin_email'] = (!empty($admin_update['EMAIL']) ? $admin_update['EMAIL'] : '');
            }
        }
        elseif (!empty($_POST['submit'])) {
            $valid = (!empty($_POST['admin_user']) &&
                ($_POST['admin_password'] == $_POST['admin_passwordc'])) ? true : false;
            if ($valid) {
                $admin_details = array();
                $admin_details['admin_user'] = $_POST['admin_user'];
                $admin_details['admin_name'] = $_POST['admin_name'];
                $admin_details['admin_password'] = $_POST['admin_password'];
                $admin_details['admin_email'] = $_POST['admin_email'];
                $admin_details['paypal'] = $_POST['paypal'];
                $admin_details['domain'] = $_POST['domain'];

                if (create_admin($admin_details)) {
                    //set the default contact email to be the admin email
                    $sql = "UPDATE `{$tables['config']['name']}` SET `VALUE` = '" . $admin_details['admin_email'] . "' WHERE `ID` = 'SITE_CONTACT_EMAIL'";
                    $db->Execute($sql);
                    $sql2 = "UPDATE `{$tables['config']['name']}` SET `VALUE` = '" . $admin_details['paypal'] . "' WHERE `ID` = 'PAYPAL_ACCOUNT'";
                    $db->Execute($sql2);
                    $sql3 = "UPDATE `{$tables['config']['name']}` SET `VALUE` = '" . $admin_details['domain'] . "' WHERE `ID` = 'SITE_URL'";
                    $db->Execute($sql3);
                    $step++;
                    http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
                }
            }
        } elseif (!empty($_POST['back'])) {
            $step--;
            http_custom_redirect(INSTALL_ROOT . '/index.php?step=' . $step);
        }
        $tpl->assign($_SESSION['values']);

        break;

    //THANK YOU MESSAGE AND CLEAR ALL TEMPORARY DATA
    case 7 : 
		$p = request_uri();
      $tpl->assign('btn_finish', 1);
      $tpl->assignLang('title', _L('Installation Finished'));

      if (empty ($_POST['complete']))
      {
         $tpl->assign('goto', 'dir');
      }
      if (!empty ($_POST['complete']))
      {
         $goto = (isset ($_POST['goto']) ? trim ($_POST['goto']) : 'dir');
         $tpl->assign('goto', $goto);

		 $db = ADONewConnection(DB_DRIVER);
		 if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)) {
         	$db->CacheFlush();
		 }
         
         switch (strtolower ($goto))
         {
            case 'admin'     :
               //Clear the entire template cache
               $tpl->clear_all_cache();

               //Clear all compiled template files
               $tpl->clear_compiled_tpl();

               // Remove all stored information
               @ session_unset();
               @ session_destroy();
               if (isset ($_SESSION))
                  unset ($_SESSION);

               //Redirect to admin panel
               http_custom_redirect(DOC_ROOT.'/admin/login.php');
               break;
            case 'install'   :
               //Redirect back to installer [step 1]
               http_custom_redirect(INSTALL_ROOT.'/index.php?step=1');
               break;
            case 'dir'       :
            case 'directory' :
            default          :

               //Clear the entire template cache
               $tpl->clear_all_cache();

               //Clear all compiled template files
               $tpl->clear_compiled_tpl();

               // Remove all stored information
               @ session_unset();
               @ session_destroy();
               if (isset ($_SESSION))
                  unset ($_SESSION);

               //Redirect to directory homepage
               http_custom_redirect(DOC_ROOT.'/');
               break;
         }
      }
}

$tpl->assign($_POST);
$tpl->assign('language', $language);
$tpl->assign('errors', $errors);
$tpl->assign('messages', $messages);

$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$tpl->assign('VERSION', CURRENT_VERSION);
$tpl->assign('step', $step);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

echo $tpl->fetch('install/main.tpl');

if ($clear_all == 1) {
    //Clear the entire template cache
    $tpl->clear_all_cache();

    //Clear all compiled template files
    $tpl->clear_compiled_tpl();

    // Remove all stored information
    @ session_unset();
    @ session_destroy();
    if (isset($_SESSION))
        unset($_SESSION);
}

function check_requirements() {
    $requirements = array();
    #PHP Vesion
    $requiredVersion = '5.3';
    $result = array('req' => str_replace('#VERSION#', $requiredVersion, _L('PHP Version &gt;= #VERSION#')));
    $result['ok'] = @ version_compare(@ phpversion(), $requiredVersion, '>=');
    $result['txt'] = '(' . @ phpversion() . ')';
    if (!$result['ok'])
        $result['txt'] .= _L('phpLinkDirectory may not work. Please upgrade!');

    $requirements[] = $result;

    #Server API
    $result = array('req' => _L('Server API'));
    //$result['ok'] = php_sapi_name() != 'cgi'; //No more CGI warnings
    $result['ok'] = 1;
    if ($result['ok'])
        $result['txt'] = '(' . php_sapi_name() . ')';
    else
        $result['txt'] = _L('CGI mode is likely to have problems.');

    $requirements[] = $result;

    #GD support
    $result = array('req' => _L('GD Support (for visual confirmations)'));
    $result['ok'] = extension_loaded('gd');
    if ($result['ok']) {
        ob_start();
        @ phpinfo(8);
        $module_info = @ ob_get_contents();
        @ ob_end_clean();
        if (preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches))
            $result['txt'] = '(' . $matches[1] . ')';
        unset($module_info, $matches);
    }
    else
        $result['txt'] = _L('Visual confirmation functionality will not be available.');

    $requirements[] = $result;

    #Session variables available
    $result = array('req' => _L('Session variables available'));
    $result['ok'] = $_SESSION['sessionTest'];
    if ($result['ok']) {
        $result['txt'] = _L('OK!');
    } else {
        $result['fatal'] = true;
        $result['txt'] = _L('Session variables are not available or not properly configured.');
    }

    $requirements[] = $result;

    #Session Save Path writable?
    $result = array('req' => _L('Session Save Path writable?'));
    $sspath = @ ini_get('session.save_path');
    if (preg_match("`.+;(.*)`", $sspath, $matches)) {
        $sspath = $matches[1];
        unset($matches);
    }

    if (!$sspath) {
        $result['ok'] = false;
        $result['txt'] = _L('Warning: ') . '<span class="item">session.save_path (' . $sspath . ')</span>' . _L(' is not set.');
    } elseif (is_dir($sspath) && is_writable($sspath)) {
        $result['ok'] = true;
        $result['txt'] = _L('OK!') . ' <span class="item">(' . $sspath . ')</span>';
    } else {
        $result['ok'] = false;
        $result['txt'] = _L('Warning: ') . '<span class="item">' . $sspath . '</span>' . _L(' not existing or not writable.');
        $result['txt'] = str_replace('##sspath##', $sspath, $r['txt']);
    }
    $requirements[] = $result;

    #MySQL Support
    $result = array('req' => _L('MySQL Support'));
    $result['ok'] = function_exists('mysql_connect');
    if (!$result['ok']) {
        $result['txt'] = _L('Not available.');
        $result['fatal'] = true;
    } else {
        $mysql_version = @ mysql_get_server_info();
        if (empty($mysql_version)) {
            @ ob_start();
            @ phpinfo(8);
            $module_info = @ ob_get_contents();
            @ ob_end_clean();
            if (preg_match("/\bClient\s+API\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches))
                $mysql_version = $matches[1];
        }
        $result['txt'] = _L('OK!') . ' (' . (!empty($mysql_version) ? trim($mysql_version) : _L('Unknown MySQL server version')) . ')';
    }
    $requirements[] = $result;

    #./include/config.php writable?
    $result = array('req' => _L('./include/config.php writable?'));
    $fn = INSTALL_PATH . 'include/config.php';
    if (!is_writable($fn))
        @ chmod($fn, 0777);

    $result['ok'] = is_writable($fn);
    if (!$result['ok']) {
        $result['txt'] = _L('Fatal: ' . INSTALL_PATH . 'include/config.php is not writable, installation cannot continue.');
        $result['fatal'] = true;
    } else {
        $result['txt'] = _L('OK!');
    }
    $requirements[] = $result;

    #./temp writable?
    $result = array('req' => _L('./temp writable?'));
    $fn = INSTALL_PATH . '/temp';
    if (!is_writable($fn))
        @chmod($fn, 0777);

    $result['ok'] = is_writable($fn);
    if (!$result['ok']) {
        $result['txt'] = _L('Fatal: ' . INSTALL_PATH . 'temp is not writable, installation cannot continue.');
        $result['fatal'] = true;
    } else {
        $result['txt'] = _L('OK!');
    }
    $requirements[] = $result;

    #./temp/templates writable?
    $result = array('req' => _L('./temp/templates writable?'));
    $fn = INSTALL_PATH . 'temp/templates';
    if (!is_writable($fn))
        @ chmod($fn, 0777);

    $result['ok'] = is_writable($fn);
    if (!$result['ok']) {
        $result['txt'] = _L('Fatal: ' . INSTALL_PATH . 'temp/templates is not writable, installation cannot continue.');
        $result['fatal'] = true;
    } else {
        $result['txt'] = _L('OK!');
    }
    $requirements[] = $result;

    return $requirements;
}

function install_language($db_details) {
    global $tables;

    $db = ADONewConnection($db_details['db_driver']);
    if ($db->Connect($db_details['db_host'], $db_details['db_user'], $db_details['db_password'], $db_details['db_name'])) {
        include INSTALL_PATH . "/lang/{$db_details['language']}.php";
        foreach ($__LANG as $hash => $value)
            $db->Execute("INSERT INTO `{$tables['lang']['name']}` (`ID`, `LANG`, `HASH`, `VALUE`) VALUES ('', 'ESSA', '{$hash}', " . $db->qstr($value) . ")");
    }
}

function upgrade_user_table($db_details) {
    global $tpl, $tables;

    $db = ADONewConnection($db_details['db_driver']);
    if ($db->Connect($db_details['db_host'], $db_details['db_user'], $db_details['db_password'], $db_details['db_name'])) {

        $user_data = $db->GetAll("SELECT * FROM `{$tables['user']['name']}`");
        $reg_date = gmdate('Y-m-d H:i:s');

        foreach ($user_data as $user) {
            if (isset($user['ADMIN']))
                $user['LEVEL'] = ($user['ADMIN'] == 1 ? 1 : 2);

            if (!preg_match('`^(\{sha1\}|\{md5\})(.+)$`', $user['PASSWORD']))
                $user['PASSWORD'] = encrypt_password($user['PASSWORD']);

            if (!isset($user['REGISTRATION_DATE']) || empty($user['REGISTRATION_DATE']) || preg_match('`^0000([-]?)00([-]?)00([\s]?)00([:]?)00([:]?)00$`', $user['REGISTRATION_DATE']))
                $user['REGISTRATION_DATE'] = $reg_date;

            $user['ACTIVE'] = 1;
            $user['EMAIL_CONFIRMED'] = 1;
            $where = " `ID` = " . $db->qstr($user['ID']);
            if (!$db->AutoExecute($tables['user']['name'], $user, 'UPDATE', $where)) {
                $tpl->assign('form_error', 'SQL_ERROR_ADMIN');
                $tpl->assign('sql_error', $db->ErrorMsg());

                return 0;
            }
        }

        $db->Execute("ALTER TABLE `{$tables['user']['name']}` DROP `ADMIN`");
        unset($user_data, $_SESSION['user_backup']);
        return 1;
    }
    else
        return 0;
}

function install_db($db_details) {
    global $tpl;

    if (!is_array($db_details) || empty($db_details)) {
        $tpl->assign('form_error', _L('Could not process input data.'));

        return 0;
    }

    $ret = update_config('include/config.php', array('LANGUAGE' => $db_details['language'], 'DB_DRIVER' => $db_details['db_driver'], 'DB_HOST' => $db_details['db_host'], 'DB_NAME' => $db_details['db_name'], 'DB_USER' => $db_details['db_user'], 'DB_PASSWORD' => $db_details['db_password']));

    if ($ret !== true) {
        $tpl->assign('form_error', $ret);
        return 0;
    }
    $ret = create_db($db_details['db_driver'], $db_details['db_host'], $db_details['db_name'], $db_details['db_user'], $db_details['db_password']);


    if (!$ret[0]) {
        // Database creation error
        $tpl->assign('form_error', $ret[1]);
        $tpl->assign('sql_error', $ret[2]);
        return 0;
    } else {
        // Database was created/updated
        $tpl->assign('message', $ret[1]);
        return 1;
    }
}

function create_admin($admin_details) {
    global $tpl, $db, $tables;

    $db = ADONewConnection(DB_DRIVER);
    if (!$db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
        return false;

    $db->SetFetchMode(ADODB_FETCH_ASSOC);

    $sql = "SELECT * FROM `{$tables['user']['name']}` WHERE `LOGIN` = " . $db->qstr($admin_details['admin_user']) . " LIMIT 1";
    $result = $db->GetRow($sql);

    $max_user_id = $db->GetOne("SELECT MAX(`ID`) FROM `{$tables['user']['name']}`");
    $max_user_id = (empty($max_user_id) ? 1 : $max_user_id + 1);

    $data = (!empty($result) && is_array($result) ? $result : get_table_data('user'));
    $data['LOGIN'] = $admin_details['admin_user'];
    $data['NAME'] = $admin_details['admin_name'];
    $data['PASSWORD'] = encrypt_password($admin_details['admin_password']);
    $data['EMAIL'] = $admin_details['admin_email'];
    $data['SUBMIT_NOTIF'] = ($data['SUBMIT_NOTIF'] == 0 ? 0 : 1);
    $data['PAYMENT_NOTIF'] = ($data['PAYMENT_NOTIF'] == 0 ? 0 : 1);
    $data['LEVEL'] = 1;
    $data['ACTIVE'] = 1;
    $data['EMAIL_CONFIRMED'] = 1;
    
    $data['LANGUAGE'] =  (!empty($_SESSION['L']) ? $_SESSION['language'] : 'en');
    
    if (!isset($data['REGISTRATION_DATE']) || empty($data['REGISTRATION_DATE']) || preg_match('`^0000([-]?)00([-]?)00([\s]?)00([:]?)00([:]?)00$`', $data['REGISTRATION_DATE']))
        $data['REGISTRATION_DATE'] = $reg_date;

    if (empty($result) || !is_array($result)) {
        $mode = "INSERT";
        $where = false;
        $data['ID'] = $db->GenID($tables['user']['name'] . '_SEQ', $max_user_id);
    } else {
        $mode = "UPDATE";
        $where = " `ID` = " . $db->qstr($data['ID']);
        /* Create a new sequence to cater for upgrading installation */
        $db->CreateSequence($tables['user']['name'] . '_SEQ', $max_user_id);
    }

    if (!$db->AutoExecute($tables['user']['name'], $data, $mode, $where)) {
        $tpl->assign('form_error', 'SQL_ERROR_ADMIN');
        $tpl->assign('sql_error', $db->ErrorMsg());

        return false;
    }

    return 1;
}

/**
 * creates or updates the database structure based on the structure defined in tables.php
 *
 * @param string $db_type database type
 * @param string $db_host database host
 * @param string $db_name dabase name
 * @param string $db_user database login
 * @param string $db_password database password
 * @return int 0 if succesfull, error code otherwise
 */
function create_db($db_type, $db_host, $db_name, $db_user, $db_password) {
    global $tables;

    $db = ADONewConnection($db_type);
    $db_created = 0;
    if (!$db->Connect($db_host, $db_user, $db_password))
        return array(false, 'INSTALL_ERROR_CONNECT', $db->ErrorMsg());

    $db = ADONewConnection($db_type);
    if (!$db->Connect($db_host, $db_user, $db_password, $db_name)) {
        $db = ADONewConnection($db_type);
        if ($db->Connect($db_host, $db_user, $db_password)) {
            $dict = NewDataDictionary($db);
            $sql_array = $dict->CreateDatabase($db_name);
            if ($sql_array)
                $db_created = $dict->ExecuteSQLArray($sql_array);
        }
        if ($db_created != 2)
            return array(false, 'INSTALL_ERROR_CREATE_DB', $db->ErrorMsg());

        $db->SelectDB($db_name);
    }

    //Get storage engines
    $getStorageEngines = $db->GetAssoc("SHOW STORAGE ENGINES");

    //Save supported storage engines
    $storageEngines = array();
    foreach ($getStorageEngines as $engineType => $engine) {
        $engine['Support'] = strtolower(trim($engine['Support']));

        if ($engine['Support'] == 'yes' || $engine['Support'] == 1) {
            //Engine supported, save for later checks
            $storageEngines[] = $engineType;
        }

        unset($getStorageEngines[$engineType], $engineType, $engine);
    }
    unset($getStorageEngines);

    $tables_existing = $db->MetaTables('TABLES');
    $dict = NewDataDictionary($db);

    //Set storage engine to MyISAM by default
    //Used because some configs create InnoDB as default,
    //and we need fulltext indexes
    $db->Execute("SET storage_engine=MyISAM");

    foreach ($tables as $table_key => $table) {
        $table_name = $table['name'];

        //Define table options
        $tableoptions = false;
        if (isset($table['options']) && is_array($table['options']) && !empty($table['options'])) {
            $tableoptions = $table['options'];
        }

        $alterEngine = 0;

        //Drop all previous indexes
        $ListIndex = $db->GetAll("SHOW INDEX FROM `{$table_name}`");
        if (is_array($ListIndex) && !empty($ListIndex)) {
            foreach ($ListIndex as $index_key => $index) {
                //Keep primary keys
                if ($index['Key_name'] != 'PRIMARY')
                    $db->Execute("DROP INDEX `{$index['Key_name']}` ON `{$table_name}`");

                unset($index, $ListIndex[$index_key]);
            }
        }
//      mysql_free_result();
        //Create/Update tables
        if (is_array($table['fields'])) {
            $fields = array();

            $exist = $db->MetaTables('TABLES');
            if (in_array("pld_submit_item", $exist) || in_array("PLD_SUBMIT_ITEM", $exist)) {
                $submit_items = $db->GetAssoc("SELECT `ID`,`FIELD_NAME`  FROM `{$tables['submit_item']['name']}`");
            }

            if (($table_key == 'link' || $table_key == 'link_review') && is_array($submit_items)) {
                $basic = array('ID', 'LINK_ID', 'CATEGORY_ID', 'RECPR_REQUIRED', 'STATUS', 'VALID', 'RATING', 'VOTES',
                    'COMMENT_COUNT', 'RECPR_VALID', 'OWNER_ID', 'OWNER_NAME', 'OWNER_EMAIL', 'OWNER_NOTIF',
                    'OWNER_EMAIL_CONFIRMED', 'DATE_MODIFIED', 'DATE_ADDED', 'HITS', 'LAST_CHECKED',
                    'RECPR_LAST_CHECKED', 'PAGERANK', 'INLINKS', 'ALEXARANK', 'COMPETERANK', 'RECPR_PAGERANK',
                    'FEATURED_MAIN', 'FEATURED', 'EXPIRY_DATE', 'NOFOLLOW', 'PAYED', 'LINK_TYPE', 'IPADDRESS',
                    'DOMAIN', 'OTHER_INFO', 'MARK_REMOVE',
                    'CACHE_URL', 'ANNOUNCE', 'RECPR_EXPIRED', 'THUMBNAIL_WIDTH', 'THUMBNAIL_WIDTH', 'THUMBNAIL_HEIGHT', 'VIDEO_CACHE');
                $exclude = '';
                foreach ($table['fields'] as $field_name => $field_def) {
                    if ((in_array($field_name, $submit_items)) || (in_array($field_name, $basic))) {
                        $fields[] = $field_name . ' ' . $field_def;
                        $exclude .= "'" . $field_name . "',";
                    }
                }
                $exclude = rtrim($exclude, ",");
                $submit_items_remaining = $db->GetAssoc("SELECT `FIELD_NAME`, `TYPE` FROM `{$tables['submit_item']['name']}` WHERE `FIELD_NAME` NOT IN (" . $exclude . ")");

                foreach ($submit_items_remaining as $k => $v) {
                    if ($v == 'TXT') {
                        $fields[] = $k . "  X2 NULL";
                    } else {
                        $fields[] = $k . "  C(255) NULL";
                    }

                }
            } else {
                foreach ($table['fields'] as $field_name => $field_def)
                    $fields[] = $field_name . ' ' . $field_def;

            }

            $created = 0;

            if ($sql_array = $dict->ChangeTableSQL($table_name, implode(',', $fields), $tableoptions)) {
                /*
                  if (($table_key == 'link' || $table_key == 'link_review') && is_array($submit_items)) {
                  foreach ($table['fields'] as $field_name => $field_def) {
                  if (!in_array ($field_name, $submit_items)) {
                  if (!in_array($field_name, $basic))
                  $db->Execute("ALTER TABLE `{$tables[$table_key]['name']}` DROP COLUMN `".$field_name."`");
                  }
                  }
                  }
                 */

                //If table storage engine is not as in definded options, make sure to alter it
                $getTblStatus = $db->GetRow("SHOW TABLE STATUS LIKE " . $db->qstr($table_name));

                if (is_array($getTblStatus) && !empty($getTblStatus) && !empty($tableoptions)) {
                    //Get storage engine from DB
                    $dbTblOptionEngine = (!empty($getTblStatus['Engine']) ? trim($getTblStatus['Engine']) : '');

                    //Get defined phpLD storage engine for table
                    $getFileTblOptionEngine = (isset($tableoptions['mysql']) ? trim($tableoptions['mysql']) : '');
                    //Get value of TYPE or ENGINE
                    preg_match('#(TYPE|ENGINE)[=]([\w]+)[\s]#i', $getFileTblOptionEngine, $matches);
                    $fileTblOptionEngine = (isset($matches[2]) ? trim($matches[2]) : '');
                    unset($getFileTblOptionEngine, $matches);

                    //Check if we need to make changes to storage engine
                    if (!empty($fileTblOptionEngine) && strtolower($dbTblOptionEngine) != strtolower($fileTblOptionEngine)) {
                        //Search case INsensitive if defined engines is supported by database
                        foreach ($storageEngines as $engine) {
                            if (strtolower($engine) != strtolower($fileTblOptionEngine)) {
                                //Storage engine supported, alter table
                                $alterEngine = 1;
                                break;
                            }
                        }

                        if ($alterEngine == 1) {
                            //Make sure again that table storage engine is as defined
                            $sql_array[] = "ALTER TABLE `{$table_name}` TYPE={$fileTblOptionEngine}";
                        }
                    }

                    unset($dbTblOptionEngine, $fileTblOptionEngine);
                }

                //Free memory
                unset($getTblStatus, $alterEngine);

                //Execute SQL queries
                $created = $dict->ExecuteSQLArray($sql_array);
            }

            if ($created != 2)
                return array(false, 'INSTALL_ERROR_CREATE', 'Table '.$table['name'].': '.$db->ErrorMsg());
        }
        mysql_free_result();
        //Add table indexes
        if (isset($table['indexes']) && is_array($table['indexes'])) {
            $indexes_existing = $db->MetaIndexes($table_name);
            foreach ($table['indexes'] as $index_name => $index_def) {
                $index_name = $table_name . '_' . $index_name . '_IDX';
                $index_opts = array();
                if (is_array($index_def)) {
                    $index_fields = $index_def[0];
                    $index_opts = explode(' ', $index_def[1]);
                }
                else
                    $index_fields = $index_def;

                if (array_key_exists($index_name, $indexes_existing) || array_key_exists(strtolower($index_name), $indexes_existing))
                    if ($sql_array = $dict->CreateIndexSQL($index_name, $table_name, $index_fields, array_merge($index_opts, array('DROP'))))
                        $dict->ExecuteSQLArray($sql_array);

                $created = 0;
                if ($sql_array = $dict->CreateIndexSQL($index_name, $table_name, $index_fields, $index_opts))
                    $created = $dict->ExecuteSQLArray($sql_array);

                if ($created != 2)
                    return array(false, 'INSTALL_ERROR_CREATE', 'Table '.$table['name'].': '.$db->ErrorMsg());

                unset($sql_array, $index_name, $index_opts, $index_fields, $index_def, $index_name);
            }
        }
//      mysql_free_result();

        unset($tableoptions);
    }

    unset($storageEngines);

    //make email confirmed

    $db->Execute("UPDATE " . $tables['link']['name'] . " SET `OWNER_EMAIL_CONFIRMED`='1'");

    $db->Execute("UPDATE " . $tables['link_review']['name'] . " SET `OWNER_EMAIL_CONFIRMED`='1'");

    //make links of link type "none", normal
    $db->Execute("UPDATE " . $tables['link']['name'] . " SET `LINK_TYPE`='1' WHERE `LINK_TYPE` = '0'");
    $db->Execute("UPDATE " . $tables['link_review']['name'] . " SET `LINK_TYPE`='1' WHERE `LINK_TYPE` = '0'");

    $db->Execute("UPDATE " . $tables['link']['name'] . " SET `LINK_TYPE`='2' WHERE `LINK_TYPE` = '3'");
    $db->Execute("UPDATE " . $tables['link_review']['name'] . " SET `LINK_TYPE`='2' WHERE `LINK_TYPE` = '3'");

    //make editors super editors
    $db->Execute("UPDATE " . $tables['user']['name'] . " SET `LEVEL`='3' WHERE `LEVEL`='2'");


    $db->CacheFlush();

//   mysql_free_result();
    return array(true, $db_created == 0 ? 'INSTALL_DB_UPDATED' : 'INSTALL_DB_CREATED');
}

function update_config($file_name, $values) {
    if (!INSTALL_PATH . file_exists($file_name))
        return 'CONFIG_NOT_FOUND';

    if (!is_writable(INSTALL_PATH . $file_name))
        return 'CONFIG_NOT_WRITABLE';

    $file = @ file_get_contents(INSTALL_PATH . $file_name);
    $vals = '';
    foreach ($values as $key => $val) {
        if (!preg_match("`define\s*\(\s*(?:'|\")$key(?:'|\")\s*,\s*(?:'|\")?.*(?:'|\")?\s*\);`Um", $file))
            $vals .= "define('$key', '$val');\n";
        else
            $file = preg_replace("`define\s*\(\s*(?:'|\")$key(?:'|\")\s*,\s*(?:'|\")?.*(?:'|\")?\s*\);`Um", "define('$key', '$val');", $file);
    }

    $insert_point = strrpos($file, '?>');
    if ($insert_point !== false)
        $file = substr($file, 0, $insert_point) . $vals . substr($file, $insert_point);

    $f = @ fopen(INSTALL_PATH . $file_name, 'w');
    @ fwrite($f, $file);
    @ fclose($f);
    return true;
}

?>