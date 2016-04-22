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
if (!defined('INSTALL_PATH')) {
    define('INSTALL_PATH', substr(__file__, 0, -33));
}

require_once INSTALL_PATH . '/include/pagerank.php';

if (!defined('IN_PHPLD_ADMIN')) {
    require_once (INSTALL_PATH . '/init.php');
} else {
    require_once(INSTALL_PATH . '/admin/init.php');
}
require_once 'functions.php';
$action = null;
$field = null;
$value = null;
$table = null;
$result = '';
if (isset($_REQUEST['action']))
    $action = $_REQUEST['action'];
if (isset($_REQUEST['field']))
    $field = $_REQUEST['field'];
if (isset($_REQUEST[$field]))
    $value = $_REQUEST[$field];
if (isset($_REQUEST['table']))
    $table = $_REQUEST['table'];

if (!empty($action)) {
    $result = server_side($action, $table, $field, $value);
}
//var_dump($result);
if (is_bool($result)) {
    echo ($result == true ? 'true' : false);
} else {
    echo $result;
}

function server_side($action, $table, $field, $value) {
    $act = "validator_" . $action;
    if (is_callable($act)) {
        return $act($table, $field, $value);

    }
}

function validator_isTitle($table, $field, $value) {
   if (isset($_REQUEST['parent_id'])) {
        $cat_id = intval($_REQUEST['parent_id']);
        if ($cat_id == 0) {
            return true;
        }
    }
    return isUniqueValue($table, $field);
}

function validator_isTitleUrl($table, $field, $value) {
    return (preg_match('!^[\w_-]*$!', $value));
}

function validator_isNotTopCat($table, $field, $value) {
    $value = intval($value);
    if ($value > 0) {
        return true;
    }
    return false;
}

function validator_isPasswordConfirmation($table, $field, $value) {
    return true;

//	removed temporarly, $_REQUEST['PASSWORD'] is always empty
//	if (isset($_REQUEST['PASSWORD'])) {
//		if ($value == $_REQUEST['PASSWORD']) {
//			return true;
//		}
//	}
//	return false;
}

function validator_isBannedEmail($table, $field, $value) {
    return isBannedEmail($value);
}

function validator_isArticleType($table, $field, $value) {
    if (ARTICLE_PAY_ENABLE) {
        if (empty($value)) {
            return false;
        }
    }
    if (ARTICLE_PAY_ENABLE == 1 && PAYPAL_ACCOUNT != '') {
        $price = array();
        if (ARTICLE_PAY_FEATURED > 0)
            $price['featured'] = ARTICLE_PAY_FEATURED;

        if (ARTICLE_PAY_NORMAL > 0) {
            $price['normal'] = ARTICLE_PAY_NORMAL;
            if (ARTICLE_PAY_ENABLE_FREE)
                $price['free'] = 0;
        }
    }
    if (isset($_SESSION['phpld']['user']['id'])) {
        $rights = user_needs_approval($_SESSION['phpld']['user']['id'], $_REQUEST['CATEGORY_ID']);
    }
    if ((isset($_REQUEST['CATEGORY_ID']) && $rights['addArt'] == 1)
            || (has_rights_on_all_cats($_SESSION['phpld']['user']['level']))) {
        $dont_show_captch = 1;
        if (($_SESSION['phpld']['user']['level'] == 1) || ($_SESSION['phpld']['user']['level'] == 3)) {
            $dont_show_pay = 1;
        }
    }
    if (count($price) > 0 && $dont_show_pay != 1) {
        if (empty($value)) {
            return false;
        }
    }
    return true;
}

function validator_isSymbolicValid($table, $field, $value) {
    global $db, $tables;

    if ($_REQUEST['PARENT_ID'] == '') {
        $parent_id = 0;
    } else {
        $parent_id = $_REQUEST['PARENT_ID'];
    }

    if ($value == '') {
        $value = 0;
    }

    if ($value == $parent_id) {
        return false;
    }

    if (isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
        $id_cond = " AND `ID` !=" . $db->qstr($_REQUEST['id']) . " ";
    }

    $sql = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `SYMBOLIC_ID` = " . $db->qstr($value) . " AND
            `PARENT_ID` = " . $db->qstr($parent_id) . " " . $id_cond;
    $c = $db->GetOne($sql);
    if ($c != 0) {
        return false;
    }
    $sql = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($value) . "
            AND `PARENT_ID` = " . $db->qstr($parent_id) . " " . $id_cond;
    $c = $db->GetOne($sql);

    if ($c != 0) {
        return false;
    }
    return true;
}

function validator_isCaptchaValid($table, $field, $value) {
    if (isset($_SESSION['phpld']['user']['id'])) {
        $rights = user_needs_approval($_SESSION['phpld']['user']['id'], $_REQUEST['CATEGORY_ID']);
    }
    if ((isset($_REQUEST['CATEGORY_ID']) && $rights['addArt'] == 1)
            || (has_rights_on_all_cats($_SESSION['phpld']['user']['level']))) {
        $dont_show_captch = 1;
    }
    if ($dont_show_captch != 1) {
        return isCaptchaValid();
    }
    return true;
}

function validator_isFieldExists($table, $field, $value) {
    return isFieldExists($field);
}

function validator_isUniqueValue($table, $field, $value) {
    return isUniqueValue($table, $field);
}

function validator_isUniqueRegistration($table, $field, $value) {
    return isUniqueRegistration($table, $field);
}

function validator_isUniqueUrl($table, $field, $value) {
    $categ_id = $_REQUEST['category_id'];
    return isUniqueUrlSite($field, $table);
}

function validator_isUniqueUrlDomain($table, $field, $value) {
    $categ_id = $_REQUEST['category_id'];
    return isUniqueUrlDomain($field, $table);
}

function validator_isURLOnline($table, $field, $value) {
    global $db, $tables;

    return isURLOnline($field);
}

function validator_urlIsImage($table, $field, $value) {
    //global $db, $tables;

    if (isset($_REQUEST[$field]) && $_REQUEST[$field]) {
        $result = url_is_image($_REQUEST[$field]);
        return $result;
    }
    else
        return true;
}

function validator_isRecprOnline($table, $field, $value) {
    return isRecprOnline($field);
}

function validator_isDomainBanned($table, $field, $value) {
    return isDomainBanned($field);
}

function validator_isUsername($table, $field, $value) {
    return isUsername($value);
}

function validator_isInt($table, $field, $value) {
    return isInt($value);
}

function validator_isDate($table, $field, $value) {
    if (strlen($value) == 0) {
        return true;
    }
    $_ret = strtotime($value);
    if ($_ret != -1 && $_ret !== false)
        return true;
    return false;
}

function validator_isNumber($table, $field, $value) {
    if (strlen($value) == 0)
        return true;

    if (preg_match('!^\d+(\.\d+)?$!', $value)) {
        return true;
    } else {
        return false;
    }
}

function validator_isAlphaNumeric($table, $field, $value) {
    if (strlen($value) == 0)
        return true;

    if (ctype_alnum($value)) {
        return true;
    } else {
        return false;
    }
}

function validator_isPaypalSet($table, $field, $value) {
    if (trim($value) == 0 || trim($value) == '') {
        return true;
    }
    if (PAYPAL_ACCOUNT != '') {
        return true;
    }
    return false;
}

function validator_isIP($table, $field, $value) {
    if (preg_match('`^\d([0-9]{1,3}).\d([0-9]{1,3}).\d([0-9]{1,3}).\d([0-9]{1,3})$`', $value)) {
        return true;
    } else {
        return false;
    }
}

function validator_isNotRestrictedIP($table, $field, $value) {
    if (!preg_match("#^(127\.0\.0\.1|10\.|172\.16\.|192\.168\.)#", $value)) {
        return true;
    } else {
        return false;
    }
}

function validator_isDomain($table, $field, $value) {
    //Allow wildcard only as prefix
    if (preg_match('`^([a-zA-Z0-9\-*]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$`', $value)) {
        return true;
    } else {
        return '2';
    }
}

function validator_isValidIP($table, $field, $value) {
    if (empty($value)) {
        return true;
    }
    if (validator_isIP($table, $field, $value) == '0') {
        return false;
    }
    if (validator_isNotRestrictedIP($table, $field, $value) == '0') {
        return false;
    }
    if (validator_isUniqueValue($table, $field, $value) == '0') {
        return false;
    }
    return true;
}

function validator_isValidBanDomain($table, $field, $value) {
    if (validator_isDomain($table, $field, $value) == '0') {
        return false;
    }
    if (validator_isUniqueValue($table, $field, $value) == '0') {
        return false;
    }
    return true;
}

function validator_isValidBanEmail($table, $field, $value) {
    if (validator_isBannedEmail($table, $field, $value) == '0') {
        return false;
    }
    if (validator_isUniqueValue($table, $field, $value) == '0') {
        return false;
    }
    return true;
}

function validator_isCategTitleUrl($table, $field, $value) {
    if (validator_isTitleUrl($table, $field, $value) == '0') {
        return false;
    }
    if (validator_isUniqueValue($table, $field, $value) == '0') {
        return false;
    }
    return true;
}

function validator_isRegistrationEmail($table, $field, $value) {
    if (validator_isBannedEmail($table, $field, $value) == '0') {
        return false;
    }
    if (validator_isUniqueRegistration($table, $field, $value) == '0') {
        return false;
    }
    return true;
}

function validator_isRegistrationUsername($table, $field, $value) {
    if (validator_isUsername($table, $field, $value) == '0') {
        return false;
    }
    if (validator_isUniqueValue($table, $field, $value) == '0') {
        return false;
    }
    return true;
}

function validator_isEmailAndAddLinkValid($table, $field, $value) {
    global $db, $tables;

    if ($value != 3)
        return true;

    if (!empty($id))
        return true;
    else {
        $sql = "SELECT `ID` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '3'";
        $c = $db->GetOne($sql);
        if ($c)
            return false;
        else
            return true;
    }
}

function validator_isURL($table, $field, $value) {
    if (strlen($value) == 0)
        return true;
    if (!preg_match('#^http[s]?:\/\/#i', $value))
        $value = "http://" . $value;
    if (preg_match('/^(http(s?):\/\/|ftp:\/\/{1})(([a-zA-Z0-9_\-]+\.){1,})\w{1,}(\S+)$/i', $value)) {
        return true;
    } else {
        return false;
    }
}

function validator_isImageUpload($table, $field, $value) {
    $allowedExtensions = array("bmp", "jpg", "jpeg", "gif", "png");
    if ($_FILES[$field]['tmp_name'] > '') {
        if (!in_array(end(explode(".", strtolower($_FILES[$field]['name']))), $allowedExtensions)) {
            return false;
        }
    }
    return true;
}

function validator_isFileUpload($table, $field, $value) {
    $allowedExtensions = array("txt", "csv", "htm", "html", "xml",
        "css", "doc", "docx", "xls", "xlsx", "rtf", "ppt", "pdf", "zip");
    if ($_FILES[$field]['tmp_name'] > '') {
        if (!in_array(end(explode(".", strtolower($_FILES[$field]['name']))), $allowedExtensions)) {
            return false;
        }
    }
    return true;
}

function validator_isVideoUpload($table, $field, $value) {
    $allowedExtensions = array("avi", "wmv", "mov", "mpg");
    if ($_FILES[$field]['tmp_name'] > '') {
        if (!in_array(end(explode(".", strtolower($_FILES[$field]['name']))), $allowedExtensions)) {
            return false;
        }
    }
    return true;
}

function validator_isCategoryTitle($table, $field, $value) {
    global $db, $tables;
    $exclude_id = intval($_REQUEST['id']);
    $parent_id = intval($_REQUEST['PARENT_ID']);
    $exclude_sql = (!empty($exclude_id) || $exclude_id == 0) ? " AND `ID` != '{$exclude_id}'" : '';
    $exclude_sql .= (!empty($exclude_id) || $exclude_id == 0) ? " AND `PARENT_ID` = '{$parent_id}'" : '';
    $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `{$field}` = '{$_REQUEST[$field]}' {$exclude_sql}");
    $result = ($count > 0) ? '0' : '1';
    return $result;
}

function validator_isCheckedEmail($table, $field, $value) {
    global $db, $tables, $tpl;
    $rs = $db->Execute("SELECT `ID`, `TITLE`, `URL` FROM `{$tables['link']['name']}`
                            WHERE `URL` = " . $db->qstr($_REQUEST['URL']) . "
                            OR `TITLE` = " . $db->qstr($_REQUEST['TITLE']));
    $err['dir'] = array();
    while (!$rs->EOF) {
        if (strcasecmp($rs->Fields('URL'), $_REQUEST['URL']) == 0)
            $err['dir'][] = 'URL';
        if (strcasecmp($rs->Fields('TITLE'), $_REQUEST['TITLE']) == 0)
            $err['dir'][] = 'TITLE';

        $rs->MoveNext();
    }
    $rs = $db->Execute("SELECT * FROM `{$tables['email']['name']}`
                                                    WHERE `URL` = " . $db->qstr($_REQUEST['URL']) . "
                                                    OR `TITLE` = " . $db->qstr($_REQUEST['TITLE']) . "
                                                    OR `EMAIL` = " . $db->qstr($_REQUEST['EMAIL']));
    $err['email'] = array();
    while (!$rs->EOF) {
        $row = array('EMAIL' => htmlentities(format_email($rs->Fields('EMAIL'), $rs->Fields('NAME'))),
            'TITLE' => $rs->Fields('TITLE'),
            'URL' => $rs->Fields('URL'),
            'DATE' => $rs->Fields('DATE_SENT'));
        if (strcasecmp($rs->Fields('EMAIL'), $_REQUEST['EMAIL']) == 0)
            $row['TYPE'] = 'EMAIL';
        if (strcasecmp($rs->Fields('URL'), $_REQUEST['URL']) == 0)
            $row['TYPE'] = 'URL';
        if (strcasecmp($rs->Fields('TITLE'), $_REQUEST['TITLE']) == 0)
            $row['TYPE'] = 'TITLE';

        $err['email'][] = $row;
        $rs->MoveNext();
    }
    if (count($err['dir']) > 0 || count($err['email']) > 0) {
        $tpl->assign('email_send_errors', $err);
        return "'" . $_REQUEST['IGNORE'] . "'";
    }
    return true;
}


function validator_isWidgetZoneName($table, $field, $value) {
    $id = $_REQUEST['id'];
    $value = $_REQUEST[$field];
    global $db, $tables;
  
    $count = $db->GetOne("SELECT COUNT(*) FROM {$tables[$table]['name']} WHERE {$field} = ".$db->qstr($value));
  
   if($id == 'null' && $count >= 1)
    	return false;
    elseif ($count > 1)
	return false;
    else
	return true;
}

?>