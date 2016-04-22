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

/**
 * Reads the configuration data from the database and sets it as constants
 * @author dcb
 */
function read_config($db) {
    global $tables;

    //Define settings array to export
    $exportSettings = array();

    $sql = "SELECT * FROM `{$tables['config']['name']}`";


    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    $rs = $db->Execute($sql);

    while (!$rs->EOF) {
        if(!defined($rs->Fields('ID'))){
            define($rs->Fields('ID'), $rs->Fields('VALUE'));
        }
        
        $exportSettings[$rs->Fields('ID')] = $rs->Fields('VALUE');

        $rs->MoveNext();
    }
    set_default_recpr();

    return $exportSettings;
}

function set_default_recpr() {
    $default_recpr_link_url = (ENABLE_THREE_WAY == 1 ? THREE_WAY_LINK_URL : SITE_URL );
    $default_recpr_link_title = (ENABLE_THREE_WAY == 1 ? THREE_WAY_LINK_TITLE : SITE_NAME);
    define('DEFAULT_RECPR_URL', $default_recpr_link_url);
    define('DEFAULT_RECPR_TITLE', $default_recpr_link_title);
}

/**
 * Creates a Smarty object and sets default values
 * @author dcb
 */
function get_tpl($force_tpl = '') {
    if (!empty($_SESSION['user_language'])) {
        $language = $_SESSION['user_language'];
    } elseif (defined('LANGUAGE')) {
        $language = LANGUAGE;
        if (empty($language))
            $language = 'en';
    }
    else
        $language = 'en';
    $tpl = new IntSmarty($language);

    $tpl->template_dir = INSTALL_PATH . 'templates/' . $force_tpl;
    
     $is_mobile_user = is_mobile_user();

    if (!$is_mobile_user) {
        $tpl->compile_dir = INSTALL_PATH . 'temp/templates';
        $tpl->cache_dir = INSTALL_PATH . 'temp/cache';
    } else {
        $tpl->compile_dir = INSTALL_PATH . 'temp/templates_mobile';
        $tpl->cache_dir = INSTALL_PATH . 'temp/cache_mobile';
    }

    return $tpl;
}

function get_tplnm($force_tpl = '') {
    if (!empty($_SESSION['user_language'])) {
        $language = $_SESSION['user_language'];
    } elseif (defined('LANGUAGE')) {
        $language = LANGUAGE;
        if (empty($language))
            $language = 'en';
    }
    else
        $language = 'en';
    $tpl = new IntSmarty($language);

    //Determine what template to use
    if (empty($force_tpl))
        determine_templatenm();
    else
        define('TEMPLATE', $force_tpl . '/');
    $tpl->template_dir = INSTALL_PATH . 'templates/' . TEMPLATE;
    $tpl->compile_dir = INSTALL_PATH . 'temp/templates';
    $tpl->cache_dir = INSTALL_PATH . 'temp/cache';

    return $tpl;
}

function get_widget_tpl($wid) {
    if (!empty($_SESSION['user_language'])) {
        $language = $_SESSION['user_language'];
    } elseif (defined('LANGUAGE')) {
        $language = LANGUAGE;
        if (empty($language))
            $language = 'en';
    } else
        $language = 'en';

    $is_mobile_user = is_mobile_user();

    $t = new IntSmarty($language);

    $t->caching = 0;
    $t->compile_id = $t->compile_id . $wid; //compile_id is already used in intsmarty for language settings
    $t->template_dir = INSTALL_PATH . 'widgets/' . $wid . '/templates';

    if (!$is_mobile_user) {
        $t->compile_dir = INSTALL_PATH . 'temp/templates';
        $t->cache_dir = INSTALL_PATH . 'temp/cache';
    } else {
        $t->compile_dir = INSTALL_PATH . 'temp/templates_mobile';
        $t->cache_dir = INSTALL_PATH . 'temp/cache_mobile';
    }

    return $t;
}

/**
 * Get templates and read templates informations
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function get_templates($dirname = 'templates/', $exclude = 'Core', $info_file = 'readme.txt', $screenshot = 'screenshot.png') {
    $pattern = array('`[,]+`', '/\s/', '`[,]$`', '`^[,]`');
    $replace = array(',', '', '', '');

    $dirname = preg_replace($pattern, $replace, $dirname);
    $exclude = preg_replace($pattern, $replace, $exclude);
    $exclude = strtolower($exclude);
    $exclude .= ',.,..';

    $exclude = explode(',', $exclude);

    $template_list = array();

    $handle = @ opendir($dirname);
    if (!$handle) {
        $dirname = '../' . $dirname;
        $handle = @ opendir($dirname);
    }

    if ($handle) {
        $key = 0;
        while (false !== ($dir_content = @ readdir($handle))) {
            if (!in_array(strtolower($dir_content), $exclude) && !empty($dir_content)) {
                if (is_dir($dirname . $dir_content)) {
                    $template_list[$key] = array();
                    $template_list[$key]['theme_path'] = $dir_content;
                    $template_list[$key]['theme_full_path'] = $dirname . $dir_content;
                    $template_list[$key]['theme_info_file'] = $template_list[$key]['theme_full_path'] . '/' . $info_file;
                    $template_list[$key]['theme_screenshot_file'] = (is_file($template_list[$key]['theme_full_path'] . '/' . $screenshot) ? $template_list[$key]['theme_full_path'] . '/' . $screenshot : '');

                    $info_file_content = implode('', file($template_list[$key]['theme_info_file']));

                    preg_match("|Theme Name:(.*)|i", $info_file_content, $theme_name);
                    preg_match("|Theme URI:(.*)|i", $info_file_content, $theme_uri);
                    preg_match("|Description:(.*)|i", $info_file_content, $theme_description);
                    preg_match("|Version:(.*)|i", $info_file_content, $theme_version);
                    preg_match("|Author:(.*)|i", $info_file_content, $theme_author);
                    preg_match("|Author URI:(.*)|i", $info_file_content, $theme_author_uri);

                    $template_list[$key]['theme_name'] = clean_string($theme_name[1]);
                    $template_list[$key]['theme_uri'] = clean_string($theme_uri[1]);
                    $template_list[$key]['theme_description'] = clean_string($theme_description[1]);
                    $template_list[$key]['theme_version'] = clean_string($theme_version[1]);
                    $template_list[$key]['theme_author'] = clean_string($theme_author[1]);
                    $template_list[$key]['theme_author_uri'] = clean_string($theme_author_uri[1]);

                    //Empty memory
                    unset($theme_name, $theme_uri, $theme_description, $theme_version, $theme_author, $theme_author_uri, $info_file_content);

                    $key++;
                }
            }
        }

        @ closedir($handle);
    }
    unset($dir_content, $key, $dirname, $exclude, $info_file, $pattern, $replace);
    return $template_list;
}

function determine_template() {
    $available_templates = get_templates();
    $error_not_found = _L('Could not determine template!');
    $error_none_available = _L('No template(s) available!');

    if (!is_array($available_templates) || empty($available_templates))
        exit($error_none_available);

    $is_mobile_user = is_mobile_user();

    if (!$is_mobile_user) {
        $get_template = (defined('TEMPLATE') && TEMPLATE != '' ? TEMPLATE : 'Allure');
        $get_template = trim($get_template);
    } else {
        $get_template = (defined('MOBILE_TEMPLATE') && MOBILE_TEMPLATE != '' ? MOBILE_TEMPLATE : 'Allure');
        $get_template = trim($get_template);
    }

    $use_template = '';

    foreach ($available_templates as $key => $template) {
        if (strtolower($template['theme_path']) == strtolower($get_template))
            $use_template = trim($template['theme_path']);
    }

    if (empty($use_template))
        exit($error_not_found);

    //Free memory
    unset($error_not_found, $error_none_available, $get_template, $available_templates);

    //Make template path availble system wide
    if (!defined(TEMPLATE)) {
        define('TEMPLATE', $use_template);
    }

    return $use_template;
}

function determine_templatenm() {
    $available_templates = get_templates();
    $error_not_found = _L('Could not determine template!');
    $error_none_available = _L('No template(s) available!');

    if (!is_array($available_templates) || empty($available_templates))
        exit($error_none_available);


    $get_template = (defined('TEMPLATE') && TEMPLATE != '' ? TEMPLATE : 'Transformer');
    $get_template = trim($get_template);

    $use_template = '';

    foreach ($available_templates as $key => $template) {
        if (strtolower($template['theme_path']) == strtolower($get_template))
            $use_template = trim($template['theme_path']);
    }

    if (empty($use_template))
        exit($error_not_found);

    //Free memory
    unset($error_not_found, $error_none_available, $get_template, $available_templates);

    //Make template path availble system wide
    define('TEMPLATE', $use_template);

    return $use_template;
}

/**
 * Shortcut function for string translations
 * @param string $str String to translate
 * @return string translated string
 * @author dcb
 */
function _L($str) {
    global $tpl;
    if (method_exists($tpl, 'translate'))
        return $tpl->translate($str);
    else
        return $str;
}

/**
 * Extracts from the global variable $_REQUEST only the values those keys correspond to table column names
 * @param string $table table name
 * @return array associative array
 * @author dcb
 */
function get_table_data($table) {
    global $db, $tables;

    $data = array();

    //Get table fields
    $dbTblFields = $db->MetaColumnNames($tables[$table]['name'], true);

    if (is_array($dbTblFields) && !empty($dbTblFields)) {

        //New method, used because
        //phpLD can add now DB fields
        foreach ($dbTblFields as $key => $field) {
            if (isset($_REQUEST[$field]))
                $data[$field] = $_REQUEST[$field];
        }
    }
    else {
        //Old method, just as backup if anything goes wrong
        foreach ($tables[$table]['fields'] as $col => $v) {
            if (isset($_REQUEST[$col]))
                $data[$col] = $_REQUEST[$col];
        }
    }

    return $data;
}

function set_user_action($action_value, $action_id, $user_id) {
    global $db, $tables;
    //if action exists, let's get it
    $sql = "SELECT * FROM `{$tables['user_actions']['name']}` WHERE `USER_ID`=" . $db->qstr($user_id) . " 
    			AND `ACTION_ID`=" . $db->qstr($action_id);
    $data = $db->GetOne($sql);
    if ($data['ID'] == '') {
        $sql = "INSERT INTO `{$tables['user_actions']['name']}` VALUES ('', " . $db->qstr($user_id) . ", " . $db->qstr($action_id) . ", 
    		" . $db->qstr($action_value) . ")";
        $db->Execute($sql);
    } else {
        $sql = "UPDATE `{$tables['user_actions']['name']}` SET `VALUE`=" . $db->qstr($action_value) . " 
        	WHERE `USER_ID`=" . $db->qstr($user_id) . " AND `ACTION_ID`=" . $db->qstr($action_id) . "";
        $db->Execute($sql);
    }
}

function remove_user_action($action_id, $user_id) {
    global $db, $tables;
    $sql = "DELETE FROM `{$tables['user_actions']['name']}` WHERE `USER_ID`=" . $db->qstr($user_id) . " AND `ACTION_ID`=" . $db->qstr($action_id) . "";
    $db->Execute($sql);
}

/**
 * ~DEPRECATED~
 * Extract saved configuration for the Captcha Image confirmation
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function get_captcha_info($option) {
    global $db, $tables;
    $option = trim($option);
    $sql = "SELECT `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` = " . $db->qstr($option);

    return $db->GetOne($sql);
}

/**
 * Read language files, and get the language
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function select_lang($dirname = '../lang/') {
    $lang = array();
    $files = array();

    // Get language files of directory
    $extension = "php";
    $extension = str_replace(" ", "", $extension);
    $ext = explode(",", $extension);
    if ($handle = @ opendir($dirname)) {
        while (false !== ($file = readdir($handle)))
            for ($i = 0; $i < sizeof($ext); $i++)
                if (strstr($file, "." . $ext[$i]) && stristr($file, '~') === false && !empty($ext[$i]))
                    $files[] = $file;
        @ closedir($handle);
    }

    //Select needed file info
    foreach ($files as $key => $f) {
        if (is_readable($dirname . $f)) {
            $lang_file_info = language_file_data($dirname . $f);
            $arr_key = substr($f, 0, -4);
            if (isset($lang_file_info['LANGUAGE']) && !empty($lang_file_info['LANGUAGE']))
                $lang[$arr_key] = ucfirst($lang_file_info['LANGUAGE']);
            else
                $lang[$arr_key] = ucfirst(substr($f, 0, -4));
            unset($f);
        }
    }

    unset($file, $files, $dirname, $ext, $extension, $handle, $language, $i, $key, $arr_key, $f);
    natcasesort($lang);

    return $lang;
}

/**
 * Make safe slashes
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function txt_stripslashes($string = '') {
    $string = stripslashes($string);
    $string = preg_replace("/\\\(?!&amp;#|\?#)/", "&#092;", $string);

    return $string;
}

/**
 * Removes special entities
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function un_html_specialchars($string = '') {
    return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES)) + array('&#039;' => '\'', '&nbsp;' => ' '));
}

/**
 * Clean/handle white-space chars or given ressource (array|string)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 *
 * @param  mixed [array|string]
 * @return mixed [array|string]
 */
function filter_white_space($ressource) {
    if (!empty($ressource)) {
        if (is_array($ressource)) {
            foreach ($ressource as $key => $value) {
                if (is_string($value))
                    $ressource[$key] = clean_str_white_space($value);
            }

            return $ressource;
        }
        elseif (is_string($ressource)) {
            $ressource = clean_str_white_space($ressource);
            return $ressource;
        }
    } else {
        return false;
    }
}

/**
 * Clean a string of unneeded white-space chars
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function clean_str_white_space($string = '') {
    //Windows to *nix
    $string = str_replace("\r\n", "\n", $string);
    //Mac to *nix
    $string = str_replace("\r", "\n", $string);
    //TABs
    $string = str_replace("\t", " ", $string);
    //NULL BYTE
    $string = str_replace("\0", "", $string);
    //Vertical TABs
    $string = str_replace("\x0B", "", $string);
    //Multiple white-space chars
    //$string = preg_replace ('#[\s]+#i', ' '       , $string);
    $string = trim($string);

    return $string;
}

/**
 * Clean a string of all white-space chars, except simple space only once
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 *
 * @param string Text to be processed
 * @param string String with which to replace white-space characters
 * @return string clean text
 */
function strip_white_space($string = '', $replace = ' ') {
    //Remove all kind of white-space chars
    $search = array("\n", //*NIX
        "\r", //Mac
        "\r\n", //Windows
        "\t", //Tab
        "\x0B", //Vertical Tab
        "\0"    //NULL BYTE
    );
    $string = str_replace($search, '', $string);

    //Remove multiple white-spaces
    $string = preg_replace('#[\s]+#i', $replace, $string);

    $string = trim($string);
    return $string;
}

/**
 * Clean a string of unneeded chars and code
 * This is really a paranoic cleaner, but better to be paranoic than to cry later
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function clean_string($string = '') {
    $string = strip_tags($string); /* No HTML and/or PHP tags */
    //$string = clean_javascript_tags ($string);
    $string = clean_str_white_space($string);
    $string = trim($string);

    return $string;
}

/**
 * Remove bad and unneeded chars of a string
 * This is really a paranoic cleaner, but better to be paranoic than to cry later
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function clean_string_paranoia($string = '') {
    $string = clean_string($string);
    $string = str_replace("&nbsp; ", "", $string);  /* TAB             */
    $string = str_replace("\n", "", $string);  /*     * NIX            */
    $string = str_replace("\r\n", "", $string);  /* Windows to *nix */
    $string = str_replace("\r", "", $string);  /* Mac to *nix     */
    $string = trim($string);

    return $string;
}

/**
 * Make clean javascript codes
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function safe_javascript_tags($string = '') {
    $string = preg_replace("/javascript/i", "j&#097;v&#097;script", $string);
    $string = preg_replace("/alert/i", "&#097;lert", $string);
    $string = preg_replace("/about:/i", "&#097;bout:", $string);
    $string = preg_replace("/onmouseover/i", "&#111;nmouseover", $string);
    $string = preg_replace("/onclick/i", "&#111;nclick", $string);
    $string = preg_replace("/onload/i", "&#111;nload", $string);
    $string = preg_replace("/onsubmit/i", "&#111;nsubmit", $string);
    $string = preg_replace("/<body/i", "&lt;body", $string);
    $string = preg_replace("/<html/i", "&lt;html", $string);
    $string = preg_replace("/document\./i", "&#100;ocument.", $string);

    return $string;
}

/**
 * Remove javascript codes
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function clean_javascript_tags($string = '') {
    $string = preg_replace("/javascript/i", "", $string);
    $string = preg_replace("/alert/i", "", $string);
    $string = preg_replace("/about:/i", "", $string);
    $string = preg_replace("/onmouseover/i", "", $string);
    $string = preg_replace("/onclick/i", "", $string);
    $string = preg_replace("/onload/i", "", $string);
    $string = preg_replace("/onsubmit/i", "", $string);
    $string = preg_replace("/<body/i", "", $string);
    $string = preg_replace("/<html/i", "", $string);
    $string = preg_replace("/document\./i", "", $string);

    return $string;
}

/**
 * Clean search queries for all unneeded code and chars
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function clean_search_query($string = '') {
    $pattern = array(/* '`([^-+"\s\w])`', */ '`[+]{2,}`', '`[-]{2,}`', '`["]{2,}`');
    $replace = array(/* ''              , */ '+', '-', '"');

    $string = clean_string_paranoia($string);
    $string = preg_replace($pattern, $replace, $string); /* Remove unneded chars */
    $string = stripslashes($string);

    return $string;
}

/**
 * Clean search queries for all unneeded code and chars
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function clean_search_location($string = '') {
    $pattern = array('`([^+\w])`', '`[+]{2,}`');
    $replace = array('', '+');

    $string = clean_string_paranoia($string);
    $string = str_replace("/\s", "+", $string);
    $string = preg_replace($pattern, $replace, $string); /* Remove unneded chars */
    $string = stripslashes($string);

    return $string;
}

/**
 * Clean META keywords and truncate to maximum length allowed (=1024)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function clean_meta_keywords($keywords = '') {
    global $phpldSettings;

    $maxLength = (isset($phpldSettings['META_KEYWORDS_MAX_LENGTH']) ? intval($phpldSettings['META_KEYWORDS_MAX_LENGTH']) : 1024);

    //Remove all kind of white-space chars
    $pattern = array("\n", //*NIX
        "\r", //Mac
        "\r\n", //Windows
        "\t", //Tab
        "\x0B", //Vertical Tab
        "\0"    //NULL BYTE
    );
    $keywords = str_replace($pattern, '', $keywords);

    $keywords = trim($keywords);

    if (strlen($keywords) > $maxLength) { //Shorten if longer than allowed (1024 chars)
        $keywords = substr($keywords, 0, $maxLength);
        $last_pos = strrpos($keywords, ',');
        if ($last_pos > 0)
            $keywords = substr($keywords, 0, $last_pos);
    }

    //Clean multiple spaces, commas, etc
    $pattern = array('/\s+/', '`[,]+[\s]*`', '`[,]+`', '`([,]*[\s]*)$`', '`^[,]*`', '`[,]+$`');
    $replace = array(' ', ',', ',', '', '', '');
    $keywords = preg_replace($pattern, $replace, $keywords);

    if (empty($keywords))
        return '';

    //use lowercase chars only
    $keywords = strtolower($keywords);

    //Build an array of keywords
    $returnKeywords = explode(',', $keywords);

    if (is_array($returnKeywords) && !empty($returnKeywords)) {
        //Keep only unique keywords
        $returnKeywords = array_unique($returnKeywords);

        //Join back array into a string
        $keywords = implode(',', $returnKeywords);
    }

    unset($returnKeywords, $pattern, $replace, $maxLength);

    return trim($keywords);
}

/**
 * Un-Escape a string
 * @param  string Escaped string
 * @return string Unescaped string
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function un_escape($string) {
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}

/**
 * Get information of language filesize
 * Modified WordPress plugins-style
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function language_file_data($lang_file) {
    $lang_file = implode('', file($lang_file));

    preg_match("|Language:(.*)|i", $lang_file, $lang);
    preg_match("|Language File Author:(.*)|i", $lang_file, $author_name);
    preg_match("|Language File Author URL:(.*)|i", $lang_file, $author_URL);

    /* Remove unused vars and clean the information we get */
    unset($lang_file, $lang[0], $author_name[0], $author_URL[0]);
    $lang[1] = clean_string_paranoia($lang[1]);
    $author_name[1] = clean_string_paranoia($author_name[1]);
    $author_URL[1] = clean_string_paranoia($author_URL[1]);

    return array('LANGUAGE' => $lang[1], 'AUTHOR_NAME' => $author_name[1], 'AUTHOR_URL' => $author_URL[1]);
}

/**
 * Encode a password using "sha1" or "md5" depending on PHP version
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function encrypt_password($password = '') {
    if (empty($password))
        return false;

    if (version_compare(phpversion(), "4.3.0", ">=") && function_exists('sha1'))
        return "{sha1}" . sha1($password);
    else
        return "{md5}" . md5($password);
}

/**
 * Read content of file
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function filecontent($file) {
    $handle = @ fopen($file, "r");
    $contents = '';
    if (!$handle)
        return false;
    else
        while (!feof($handle))
            $contents .= @ fread($handle, 8192);

    @ fclose($handle);

    return $contents;
}

/**
 * Open a remote URL
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function remote_fopen($url) {
    if (ini_get('allow_url_fopen')) {
        $fp = @ fopen($url, 'r');
        if (!$fp)
            return false;

        $linea = '';
        while ($remote_read = @ fread($fp, 4096))
            $linea .= $remote_read;

        @ fclose($fp);
        return $linea;
    }
    elseif (function_exists('curl_init')) {
        $handle = @ curl_init();
        @ curl_setopt($handle, CURLOPT_URL, $url);
        @ curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 1);
        @ curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        $buffer = @ curl_exec($handle);
        @ curl_close($handle);
        return $buffer;
    }
    else
        return false;
}

/**
 * Write string to file
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * @param  string  Filename that is going to be written
 * @param  string  String that is going to be written to file
 * @param  boolean TRUE if filepointer is going to be put on first position
 * @param  boolean TRUE if write mode is append, FALSE if regular mode. Overrides the "truncate" parameter.
 */
function write_to_file($filename, $string = '', $truncate = false, $append = false) {
    if ($append == true)
        $truncate = false;

    $mode = ($append == true ? 'a' : 'w');

    // Check if file exists
    if (!file_exists($filename))
        return false;

    // Check if file is writeable
    if (!is_writable($filename))
        @ chmod($filename, 0666); // Try to give writing permissions

    if (!is_writable($filename))
        return false;

    // Open file
    if (!$f = @ fopen($filename, $mode))
        return false;

    if ($truncate == true) {
        // Truncate file
        @ ftruncate($f, '0');
    }

    // Write content to file
    if (@ fwrite($f, $string) === false)
        return false;

    // Close file
    if (!@ fclose($f))
        return false;

    clearstatcache();

    return true;
}

/**
 * Detect web server software (Apache or IIS)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function detect_server_software() {
    //Server detection
    define('IS_APACHE', ( strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') || strstr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') ) ? 1 : 0);
    define('IS_IIS', strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0);
}

/**
 * HTTP Redirect without message
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function http_custom_redirect($url = SITE_URL, $option = '', $timeout = 0, $cust_msg = '', $status = 302) {
    //No NULL chars in URL
    $url = preg_replace('/\0+/', '', $url);
    $url = preg_replace('/(\\\\0)+/', '', $url);

    //No whitespace in URL
    $url = strip_white_space($url);

    //More cleaning
    $url = trim($url);
    $url = str_replace('&amp;', '&', $url);

    $option = strtolower($option);

    $cust_msg = trim($cust_msg);

    if (defined('IS_IIS'))
        $is_IIS = IS_IIS;
    else
        $is_IIS = (strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0);

    if ($is_IIS == 1) {
        //Seems like for Ms. IIS this works better
        $option = 'refresh';
    }

    if ($option == 'refresh') {
        @ header("Refresh: {$timeout};url=" . $url);
    } elseif ($option == 'html') {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo "\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
        echo "<head>\n";
        echo "<title>" . _L('Redirecting...') . "</title>\n";
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        echo '<link rel="stylesheet" type="text/css" href="files/main.css" />';
        echo "\n<meta http-equiv='refresh' content='{$timeout}; url={$url}' />";
        echo "\n</head>\n<body>\n";
        echo "<div class=\"redirect\">{$cust_msg}</div>\n";
        echo "</body>\n</html>";

        exit();
    } elseif ($option == 'javascript') {
        echo javascript_redirect($url, $timeout);
    } else {
        if (php_sapi_name() != 'cgi-fcgi') {
            //IIS and sometimes FastCGI cause problems
            httpstatus($status);
        }
        // @ header ("Location: " . $url);
        @ header("Location: " . $url, TRUE, $status);
    }

    exit();
}

/**
 * Make a JavaScript redirect with custom message
 * @param string  $url       URL where to redirect
 * @param integer $timeout   redirect timeout
 * @param string  $title_msg page and header title of redirect page
 * @param string  $cust_msg  custom message to be displayed on redirect page
 * @param integer $status    status of current work (-1 = Error; 0 = neutral/still working; 1 = OK/finished )
 *
 * @return string           full HTML source of redirect page
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function javascript_redirect($url = '', $timeout = 0, $title_msg = '', $cust_msg = '', $status = 0) {
    $url = trim($url);
    $url = str_replace('&amp;', '&', $url);

    $title_msg = (!empty($title_msg) ? trim($title_msg) : _L('Redirecting...'));
    $cust_msg = trim($cust_msg);
    $status_css_class = ($status == 1 ? 'complete' : ($status == -1 ? 'failed' : 'regular'));

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo "\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
    echo "<head>\n";
    echo "<title>" . strip_tags($title_msg) . "</title>\n";
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    echo '<link rel="stylesheet" type="text/css" href="' . TEMPLATE_ROOT . '/files/style.css" />';
    echo "\n</head>\n<body>\n<div id=\"wrap\" class=\"{$status_css_class}\">";
    echo "<div class=\"redirect\">";

    if ($timeout == 0) {
        echo '<h2 class="redirect-title">' . $title_msg . '</h2>';
        echo '<p class="redirect-url"><a href="' . $url . '" title="' . _L('Click here if your browser does not automatically redirect you.') . '">' . _L('Click here if your browser does not automatically redirect you.') . '</a></p>';
        echo "\n<script type=\"text/javascript\">/* <![CDATA[ */\n";
        echo "window.location=\"{$url}\";";
        echo "\n/* ]]> */</script>\n";
    } else {
        echo "\n<script type=\"text/javascript\">/* <![CDATA[ */\n";
        echo "timeout = " . ($timeout * 10) . ";
      function do_refresh()
      {
         window.status=\"" . $title_msg . "\";
         timerID = setTimeout(\"do_refresh();\", 100);
         if (timeout > 0)
         {
            timeout -= 1;
         }
         else
         {
            clearTimeout(timerID);
            window.status=\"{$title_msg}\";
            window.location=\"{$url}\";
         }
      }
      do_refresh();";
        echo "\n/* ]]> */</script>\n";
        echo '<h2 class="redirect-title">' . $title_msg . '</h2>';
        echo '<p class="redirect-url"><a href="' . $url . '" title="' . _L('Click here if your browser does not automatically redirect you.') . '" onclick="javascript:clearTimeout(timerID);">' . _L('Click here if your browser does not automatically redirect you.') . '</a></p>';
    }

    echo "\n<div class=\"block\">{$cust_msg}</div>\n";
    echo "\n</div>";
    echo "\n</body>";
    echo "\n</html>";

    exit();
}

/**
 * Put each search keyword into an array element
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function make_search_keywords($keywords, $separator = " ") {
    $keywords_array = array($keywords);
    $keywords_array = array_merge($keywords_array, explode(" ", $keywords));
    foreach ($keywords_array as $key => $keyw) {
        $keywords_array[$key] = trim($keyw);
        if (empty($keywords_array[$key]) || strlen($keyw) < 3)
            unset($keywords_array[$key]);
    }

    return array_unique($keywords_array);
}

function get_regular_categs_tree($id = 0) {
    global $db, $tables;
    static $categs = array("0" => "[Top]");
    static $level = 0;
    $level++;
    $rs = $db->CacheExecute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

    while (!$rs->EOF) {
        $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
        get_regular_categs_tree($rs->Fields('ID'));
        $rs->MoveNext();
    }
    $level--;

    return $categs;
}

function get_regular_categs_tree_opentoarticles($id = 0) {
    global $db, $tables;
    static $categs = array("0" => array("val" => "[Top]", "closed" => 1));
    static $level = 0;
    $level++;
    $rs = $db->CacheExecute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

    while (!$rs->EOF) {
        $categs[$rs->Fields('ID')]['val'] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');

        get_regular_categs_tree_opentoarticles($rs->Fields('ID'));
        $rs->MoveNext();
    }
    $level--;

    return $categs;
}

function get_regular_categs_tree_opentolinks($id = 0, $link_type=0) {
    global $db, $tables;

    $lt = $db->GetRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE `ID`=" . $db->qstr($link_type));

    if ($lt['FEATURED'] != 1) {
        $aux = array();
    } else {
        $aux1 = $db->GetAssoc("SELECT c.ID, COUNT(l.ID) AS LINKS FROM `{$tables['category']['name']}` c, `{$tables['link']['name']}` l
            WHERE c.ID = l.CATEGORY_ID
            AND l.FEATURED = 1
            AND l.ID NOT IN (SELECT LINK_ID FROM `{$tables['link_review']['name']}`)
            GROUP BY c.ID");

        $aux2 = $db->GetAssoc("SELECT ac.CATEGORY_ID, COUNT(l.ID) AS LINKS FROM `{$tables['additional_category']['name']}` ac, `{$tables['link']['name']}` l
            WHERE ac.LINK_ID = l.CATEGORY_ID
            AND l.FEATURED = 1
            AND l.ID NOT IN (SELECT LINK_ID FROM `{$tables['link_review']['name']}`)
            GROUP BY ac.LINK_ID");

        $aux = array();

        foreach ($aux1 as $k => $v) {
            if (isset($aux[$k]) && $aux[$k] != '') {
                $aux[$k] += $v;
            } else {
                $aux[$k] = $v;
            }
        }
        foreach ($aux2 as $k => $v) {
            if (isset($aux[$k]) && $aux[$k] != '') {
                $aux[$k] += $v;
            } else {
                $aux[$k] = $v;
            }
        }
    }
    return get_categs_custom($id, $aux);
}

function get_categs_custom($id = 0, $aux=0) {
    global $db, $tables;
    //static $categs = array ("0" => array("val" => "[Top]", "closed" => 1) );
    static $categs = '';
    static $level = 0;
    $level++;
    $rs = $db->CacheExecute("SELECT `ID`, `TITLE`, `CLOSED_TO_LINKS` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

    while (!$rs->EOF) {
        //$categs[$rs->Fields('ID')]['val'] = $rs->Fields('TITLE'); //str_repeat ('|&nbsp;&nbsp;&nbsp;', $level -1).'|___'.$rs->Fields('TITLE');
        //$categs[$rs->Fields('ID')]['closed'] = $rs->Fields('CLOSED_TO_LINKS');
        $categs .= '<li><a id="id_' . $rs->Fields('ID') . '" href="#' . $rs->Fields('ID') . '">' . $rs->Fields('TITLE') . '</a>';
        $rcount = $db->CacheExecute("SELECT COUNT(*) as C FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($rs->Fields('ID')) . " AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' )");
        if ($rcount->Fields('C') > 0) {
            $categs .= '<ul>';
        }
        get_categs_custom($rs->Fields('ID'));
        if ($rcount->Fields('C') > 0) {
            $categs .= '</ul>';
        }
        $categs .= '</li>';
        $rs->MoveNext();
    }
    $level--;
    return $categs;
}

/*
  function get_categs_custom($id = 0, $aux)
  {
  global $db, $tables;
  static $categs = array ("0" => array("val" => "", "closed" => 1) );
  static $level = 0;
  $level++;
  $rs = $db->CacheExecute("SELECT `ID`, `TITLE`, `CLOSED_TO_LINKS` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = ".$db->qstr($id)." AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

  while (!$rs->EOF)
  {
  $categs[$rs->Fields('ID')]['val'] = str_repeat ('', $level -1).''.$rs->Fields('TITLE');
  $categs[$rs->Fields('ID')]['closed'] = $rs->Fields('CLOSED_TO_LINKS');

  if (!empty($aux) && $aux[$rs->Fields('ID')] >= FTR_MAX_LINKS) {
  $categs[$rs->Fields('ID')]['closed'] = 1;
  }

  get_categs_custom($rs->Fields('ID'));
  $rs->MoveNext();
  }
  $level--;

  return $categs;
  } */

function get_categs_tree($id = 0) {
    global $db, $tables;

    static $categs = array();
    //Add TOP category only if editor is allowed
    if ($id == 0) {
//      if ($_SESSION['phpld']['adminpanel']['is_admin'] || in_array($id, $_SESSION['phpld']['adminpanel']['permission_array']))
        $categs[0] = "[Top]";
    }

    static $level = 0;
    $level++;
    $rs = $db->CacheExecute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

    while (!$rs->EOF) {
        if (empty($_SESSION['phpld']['adminpanel']['id']) || $_SESSION['phpld']['adminpanel']['is_admin'])
            $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
        else
//         if(in_array($rs->Fields('ID'), $_SESSION['phpld']['adminpanel']['permission_array']))
            $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
        get_categs_tree($rs->Fields('ID'));
        $rs->MoveNext();
    }
    $level--;

    return $categs;
}

function get_categs_tree_frontend_forlinks($id = 0, $link_type = 0) {
    global $db, $tables;

    static $categs = array();

    static $level = 0;
    $level++;
      
    
    $rs = $db->CacheExecute("SELECT `ID`, `TITLE`, `CLOSED_TO_LINKS` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `STATUS` = '2'  AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

    while (!$rs->EOF) {
        
         if($link_type > 0)
        {
            $add_categ_sql = " `ID` IN (SELECT `LINK_TYPE` FROM `{$tables['category_link_type']['name']}` WHERE `CATEGORY_ID` = '".$rs->Fields('ID')."') AND ";
        
            $link_types = $db->GetAssoc("SELECT * FROM `{$tables['link_type']['name']}` WHERE {$add_categ_sql} `STATUS` = '2'  ORDER BY `ORDER_ID` ASC");
        }
        
        if(empty($link_types) || !empty($link_types[$link_type]))
        {
            $categs[$rs->Fields('ID')]['val'] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
            $categs[$rs->Fields('ID')]['closed'] = $rs->Fields('CLOSED_TO_LINKS');
            get_categs_tree_frontend_forlinks($rs->Fields('ID'), $link_type);
        }
       
        
    
        $rs->MoveNext();
    }
    $level--;

    return $categs;
}

function get_categs_tree_frontend_forarts($id = 0) {
    global $db, $tables;

    static $categs = array();

    if ((isset($_SESSION['phpld']['user']['level'])
            && (($_SESSION['phpld']['user']['level'] == 1) || ($_SESSION['phpld']['user']['level'] == 3)))) {
        
    }

    $opentoarts = "1 ";
    static $level = 0;
    $level++;
    $rs = $db->CacheExecute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `STATUS` = '2' AND " . $opentoarts . " AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

    while (!$rs->EOF) {
        if (empty($_SESSION['phpld']['adminpanel']['id']) || $_SESSION['phpld']['adminpanel']['is_admin'])
            if ((isset($_SESSION['phpld']['user']['level'])
                    && (($_SESSION['phpld']['user']['level'] == 1) || ($_SESSION['phpld']['user']['level'] == 3)))
                    || (isset($_SESSION['phpld']['user']['id']) &&
                    has_rights_on_cat($_SESSION['phpld']['user']['id'], $rs->Fields('ID')))) {
                $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
            } else
            if (in_array($rs->Fields('ID'), $_SESSION['phpld']['adminpanel']['permission_array']))
                $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');

        if ((isset($_SESSION['phpld']['user']['level'])
                && (($_SESSION['phpld']['user']['level'] == 1) || ($_SESSION['phpld']['user']['level'] == 3)))
                || (isset($_SESSION['phpld']['user']['id']) &&
                has_rights_on_cat($_SESSION['phpld']['user']['id'], $rs->Fields('ID')))) {
            get_categs_tree_frontend_forarts($rs->Fields('ID'));
        }

        $rs->MoveNext();
    }
    $level--;

    return $categs;
}

function get_categs_tree_frontend($id = 0, $cur) {
    global $db, $tables;

    static $categs = array();
    //Add TOP category only if editor is allowed
    if ($id == 0) {
        $categs[0] = "[Top]";
    }

    static $level = 0;
    $level++;
    $rs = $db->CacheExecute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `STATUS` = '2' AND `SYMBOLIC` !='1' AND `ID`!=" . $db->qstr($cur) . " ORDER BY `TITLE`");



    while (!$rs->EOF) {
        if (empty($_SESSION['phpld']['adminpanel']['id']) || $_SESSION['phpld']['adminpanel']['is_admin'])
            if ((isset($_SESSION['phpld']['user']['level'])
                    && (($_SESSION['phpld']['user']['level'] == 1) || ($_SESSION['phpld']['user']['level'] == 3)))
                    || (isset($_SESSION['phpld']['user']['id']) &&
                    has_rights_on_cat($_SESSION['phpld']['user']['id'], $rs->Fields('ID')))) {
                $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
            } else
            if (in_array($rs->Fields('ID'), $_SESSION['phpld']['adminpanel']['permission_array']))
                $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
        if ($cur != $rs->Fields('ID')) {
            if ((isset($_SESSION['phpld']['user']['level'])
                    && (($_SESSION['phpld']['user']['level'] == 1) || ($_SESSION['phpld']['user']['level'] == 3)))
                    || (isset($_SESSION['phpld']['user']['id']) &&
                    has_rights_on_cat($_SESSION['phpld']['user']['id'], $rs->Fields('ID')))) {
                get_categs_tree_frontend($rs->Fields('ID'), $cur);
            }
        }
        $rs->MoveNext();
    }
    $level--;

    return $categs;
}

function get_categs_tree_backend($id = 0, $cur) {
    global $db, $tables;

    static $categs = array();
    //Add TOP category only if editor is allowed
    if ($id == 0) {
        $categs[0] = "[Top]";
    }

    static $level = 0;
    $level++;
    $rs = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `STATUS` = '2' AND `SYMBOLIC` !='1' AND `ID`!=" . $db->qstr($cur) . " ORDER BY `TITLE`");

    while (!$rs->EOF) {

        if (empty($_SESSION['phpld']['adminpanel']['id']) || $_SESSION['phpld']['adminpanel']['is_admin'])
            $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
        else
        if (in_array($rs->Fields('ID'), $_SESSION['phpld']['adminpanel']['permission_array']))
            $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
        if ($cur != $rs->Fields('ID')) {
            get_categs_tree_backend($rs->Fields('ID'), $cur);
        }
        $rs->MoveNext();
    }
    $level--;

    return $categs;
}

function get_grant_categs_tree($id = 0) {
    global $db, $tables;

    static $categs = array();
    //Add TOP category only if editor is allowed
    if ($id == 0) {
        if ($_SESSION['phpld']['adminpanel']['is_admin'] || in_array($id, $_SESSION['phpld']['adminpanel']['permission_array']))
            $categs[0] = "[Top]";
    }

    static $level = 0;
    $level++;
    $rs = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " AND `SYMBOLIC` <> 1 ORDER BY `TITLE`");

    while (!$rs->EOF) {
        if ($_SESSION['phpld']['adminpanel']['is_admin'])
            $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');
        else
        if (in_array($rs->Fields('ID'), $_SESSION['phpld']['adminpanel']['grant_permission_array']))
            $categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level - 1) . '|___' . $rs->Fields('TITLE');

        get_grant_categs_tree($rs->Fields('ID'));
        $rs->MoveNext();
    }
    $level--;

    return $categs;
}

/**
 * [DEPRECATED]
 * Use "$client_info['IP']" instead
 *
 * Get IP address of current visitor
 */
function get_client_ip() {
    $ipAddress = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : @ getenv('REMOTE_ADDR'));

    return $ipAddress;
}

function get_page($list_total) {
    if (!isset($_SESSION['p']))
        $_SESSION['p'] = array();

    $page = isset($_REQUEST['p']) ? $_REQUEST['p'] : (isset($_SESSION[SCRIPT_NAME]['p']) ? $_SESSION[SCRIPT_NAME]['p'] : 1);
    if (($page - 1) > ($list_total / LINKS_PER_PAGE))
        $page = floor($list_total / LINKS_PER_PAGE) + 1;

    $_SESSION[SCRIPT_NAME]['p'] = $page;
    $_REQUEST['p'] = $page;
    return $page;
}

function request_uri() {
    if ($_SERVER['REQUEST_URI'])
        return $_SERVER['REQUEST_URI'];

    // IIS with ISAPI_REWRITE
    if ($_SERVER['HTTP_X_REWRITE_URL'])
        return $_SERVER['HTTP_X_REWRITE_URL'];
    $p = $_SERVER['SCRIPT_NAME'];
    if ($_SERVER['QUERY_STRING'])
        $p .= '?' . $_SERVER['QUERY_STRING'];
    return $p;
}

/**
 * Check if current page is valid or invalid and return HTTP status code to send header
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function request_status() {
    global $db, $tables;
    $status = true;

    $output_status_valid = (FORCE_HTTP_STATUS_OK == 1 ? 200 : 0 );
    $output_status_invalid = (FORCE_INVALID_HTTP_STATUS_OK == 1 ? 200 : 404);

    if (isset($_REQUEST['c']))
        $cid = $_REQUEST['c'];

    $path = request_uri();
	    //James url non english edit
   $path= urldecode($path);
    $path = preg_replace('#page-(\d+)\.htm[l](.*)?$#', '', $path);

    if (ENABLE_REWRITE && !isset($cid)) {
        $path = substr($path, strlen(DOC_ROOT) + 1);
        $qp = strpos($path, '?');
        if ($qp !== false)
            $path = substr($path, 0, $qp);

        $path = explode('/', $path);
        foreach ($path as $key => $cat) {
            $cat = trim($cat);
            if (empty($cat) || $cat == 'index.php')
                unset($path[$key]);
        }

        //Search for latest/top links, articles
        $listArrayRegexp = array('latest(-|_)links\.htm[l]?', 'top(-|_)hits\.htm[l]?', 'top(-|_)links\.htm[l]?', 'latest(-|_)articles\.htm[l]?', 'top(-|_)articles\.htm[l]?');
        foreach ($path as $key) {
            foreach ($listArrayRegexp as $regex) {
                if (preg_match('#' . $regex . '#i', $key)) {
                    return $output_status_valid;
                }
            }
        }

        $id = 0;
        foreach ($path as $cat) {
            $sql = "SELECT `ID` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `TITLE_URL` = " . $db->qstr($cat) . " AND `PARENT_ID` = " . $db->qstr($id);
            $id = $db->GetOne($sql);
            if (empty($id))
                $status = false;
        }
    }
    elseif (preg_match('`^[\d]+$`', $cid) && isset($cid)) {
        if ($cid != 0) {
            $id = $db->GetOne("SELECT `ID` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `ID` = " . $db->qstr($cid));
            if (empty($id))
                $status = false;
        }
    }


    return ($status == true ? $output_status_valid : $output_status_invalid);
}

function get_category($uri = NULL) {
    $categoryModel = new Model_Category();
    return $categoryModel->getCategoryByUri($uri);
}

function get_path($id) {
    global $db, $tables;

    $path = array();
    $i = 0;
    while ($id != 0) {
        $row = $db->GetRow("SELECT * FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($id));
        $id = $row['PARENT_ID'];
        $path[] = $row;
        $i++;
    }
    $path[] = array('ID' => '0', 'TITLE' => _L(SITE_NAME), 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);

    return array_reverse($path);
}

define('URL_RESPONSE', 0);
define('URL_HEADERS', 1);
define('URL_CONTENT', 2);

function get_url($url, $what = 0, $referer = "", $cookies = array(), $useragent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)") {
    static $redirect_count = 0;
    $ret = array();
    $ret['status'] = false;
    $timeout = 10;
    $urlArray = parse_url($url);
    if (!$urlArray['port']) {
        if ($urlArray['scheme'] == 'http')
            $urlArray['port'] = 80;
        elseif ($urlArray['scheme'] == 'https')
            $urlArray['port'] = 443;
        elseif ($urlArray['scheme'] == 'ftp')
            $urlArray['port'] = 21;
    }
    if (!$urlArray['path'])
        $urlArray['path'] = '/';

    $errno = "";
    $errstr = "";
    $fp = @ fsockopen($urlArray['host'] . '.', $urlArray['port'], $errno, $errstr, $timeout);
    if ($fp) {
        $request = "GET {$urlArray['path']}";
        if (!empty($urlArray['query']))
            $request .= "?" . $urlArray['query'];

        $request .= " HTTP/1.1\r\n" . "Host: {$urlArray['host']}\r\n" . "User-Agent: {$useragent}\r\n";
        if (!empty($referer)) {
            $request .= "Referer: $referer\r\n";
        }
        $request .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,video/x-mng,image/png,image/jpeg,image/gif;q=0.2,text/css,*/*;q=0.1\r\n" . "Accept-Language: en-us, en;q=0.50\r\n" .
                #"Accept-Encoding: gzip, deflate, compress;q=0.9\r\n".
                //"Accept-Charset: ISO-8859-1, utf-8;q=0.66, *;q=0.66\r\n".
                #"Keep-Alive: 300\r\n".
                "Connection: close\r\n" . "Cache-Control: max-age=0\r\n";
        foreach ($cookies as $k => $v)
            $request .= "Cookie: {$k}={$v}\r\n";

        $request .= "\r\n";
        @ fputs($fp, $request);
        $ret['response'] = fgets($fp);
        if (preg_match("`HTTP/1\.. (.*) (.*)`U", $ret['response'], $parts)) {
            $ret['status'] = $parts[1][0] == '2' || $parts[1][0] == '3';
            $ret['code'] = $parts[1];
            if ($what == URL_RESPONSE || !$ret['status']) {
                @ fclose($fp);
                return $ret;
            }
            $ret['headers'] = array();
            $ret['cookies'] = array();
            while (!feof($fp)) {
                $header = @ fgets($fp, 2048);
                if ($header == "\r\n" || $header == "\n" || $header == "\n\l")
                    break;
                list ($key, $value) = explode(':', $header, 2);
                if (trim($key) == 'Set-Cookie') {
                    $value = trim($value);
                    $p1 = strpos($value, '=');
                    $p2 = strpos($value, ';');
                    $key = substr($value, 0, $p1);
                    $val = substr($value, $p1 + 1, $p2 - $p1 - 1);
                    $ret['cookies'][$key] = $val;
                }
                else
                    $ret['headers'][trim($key)] = trim($value);
            }
            if (($ret['code'] == '301' || $ret['code'] == '302') && !empty($ret['headers']['Location']) && $redirect_count < 20) {
                $redirect_count++;
                @ fclose($fp);
                if (strpos($ret['headers']['Location'], 'http://') === 0 || strpos($ret['headers']['Location'], 'http://'))
                    $redir_url = $ret['headers']['Location'];
                elseif (strpos($ret['headers']['Location'], '/') === 0)
                    $redir_url = $urlArray['scheme'] . "://" . $urlArray['host'] . $ret['headers']['Location'];
                else
                    $redir_url = $urlArray['scheme'] . "://" . $urlArray['host'] . $urlArray['path'] . $ret['headers']['Location'];

                return get_url($redir_url, $what, $url, $ret['cookies']);
            }
            $redirect_count = 0;
            if ($what == URL_HEADERS) {
                @ fclose($fp);
                return $ret;
            }
            $chunked = isset($ret['headers']['Transfer-Encoding']) && ('chunked' == $ret['headers']['Transfer-Encoding']);
            while (!feof($fp)) {
                $data = '';
                if ($chunked) {
                    $line = @ fgets($fp, 128);
                    if (preg_match('/^([0-9a-f]+)/i', $line, $matches)) {
                        $len = hexdec($matches[1]);
                        if (0 == $len)
                            while (!feof($fp))
                                @ fread($fp, 4096);
                        else
                            $data = @ fread($fp, $len);
                    }
                }
                else
                    $data = @ fread($fp, 4096);

                $ret['content'] .= $data;
            }
        }
        else
            $errstr = "Bad Communication";

        @ fclose($fp);
    }
    else { // Occurs when if ($fp) returns false
    }
    $ret['error'] = $errstr;

    return $ret;
}

function parse_news($str) {
    $str = str_replace("\r\n", "\n", $str);
    $str = str_replace("\r", "\n", $str);
    $str = explode("\n", $str);
    $news = array();
    $len = count($str);
    $i = 0;
    while ($i < $len) {
        if ($str[$i] == '') {
            $i++;
            continue;
        }
        $n = array();
        $n['title'] = $str[$i++];
        $n['date'] = $str[$i++];
        while ($i < $len && $str[$i] != '')
            $n['body'] .= $str[$i++] . "\n";
        $news[] = $n;
        $i++;
    }
    return $news;
}

function parse_version($val) {
    preg_match('`(\d+)\.(\d+)\.(\d+)\s*((RC)(\d+))?`', $val, $match);
    $ver = sf("%02d%02d%02d%02d", $match[1], $match[2], $match[3], $match[6]);

    return $ver;
}

if (!function_exists('file_get_contents')) {

    function file_get_contents($fn) {
        $len = filesize($fn);
        if (!$len)
            return false;

        $fp = @ fopen($fn, 'r');
        if ($fp)
            return @ fread($fp, $len);
        else
            return false;
    }

}

function set_log($file) {
    if (!ini_get('safe_mode')) {
        @ ini_set('display_errors', 0);
        @ ini_set('log_errors', 1);
        @ ini_set('error_log', INSTALL_PATH . 'temp/' . $file);
        @ error_reporting(E_ALL ^ E_NOTICE);
    }
}

function replace_email_vars($text, $data, $type = 1) {
    if ($type == 1)
        $prefix = 'EMAIL_';
    elseif ($type == 2)
        $prefix = 'LINK_';
    elseif ($type == 3)
        $prefix = 'PAYMENT_';
    elseif ($type == 5)
        $prefix = 'USER_';

    $paymentURLArt = (substr(SITE_URL, -1, 1) == '/' ? SITE_URL : SITE_URL . '/') . 'article_payment.php?id=' . intval($data['ID']);
    $text = str_replace('{ARTICLE_PAYMENT_URL}', $paymentURLArt, $text);

    if ($type == 1) {
	if(!empty($data['ID'])) {
            if (ENABLE_REWRITE == 1)
                $detailPageUrl = "detail/link-{$data['ID']}.html";
            else
                $detailPageUrl = "detail.php?id={$data['ID']}";

            $listing = new Model_Link();
            $link = $listing->getLinkById($data['ID']);

            if (!is_null($link)) {
                $detailPageUrl = $link->getUrl();
            }

           
            $text = str_replace('{EMAIL_LINK_URL}', $detailPageUrl, $text);

            unset($detailPageUrl);
	}
    }
    elseif ($type == 2) {
        if (!empty($data['ID'])) {
            if (ENABLE_REWRITE == 1)
                $detailPageUrl = "detail/link-{$data['ID']}.html";
            else
                $detailPageUrl = "detail.php?id={$data['ID']}";

            $listing = new Model_Link();
            $link = $listing->getLinkById($data['ID']);

            if (!is_null($link)) {
                $detailPageUrl = $link->getUrl();
            }

            //Build detail page URL
            $detailPageUrl = (substr(SITE_URL, -1, 1) == '/' ? SITE_URL . $detailPageUrl : SITE_URL . '/' . $detailPageUrl);

            $text = str_replace('{LINK_DETAIL_PAGE}', $detailPageUrl, $text);
            $text = str_replace('{EMAIL_LINK_URL}', $detailPageUrl, $text);

            unset($detailPageUrl);
        }
        if (!empty($data['CATEGORY_ID'])) {
            $categData = getCategoryByID($data['CATEGORY_ID']);
            $categData['CACHE_URL'] = (!empty($categData['CACHE_URL']) ? $categData['CACHE_URL'] : buildCategUrl($data['CATEGORY_ID']));
            $categData['CACHE_TITLE'] = (!empty($categData['CACHE_TITLE']) ? $categData['CACHE_TITLE'] : buildCategUrlTitle($data['CATEGORY_ID']));
            $categData['CACHE_URL'] = (substr(SITE_URL, -1, 1) == '/' ? SITE_URL . $categData['CACHE_URL'] : SITE_URL . '/' . $categData['CACHE_URL']);

            $text = str_replace('{LINK_CATEGORY_URL}', $categData['CACHE_URL'], $text);
            $text = str_replace('{LINK_CATEGORY_TITLE}', $categData['CACHE_TITLE'], $text);

            unset($categData);
        }
        if (!empty($data['ID'])) {
            $paymentURL = (substr(SITE_URL, -1, 1) == '/' ? SITE_URL : SITE_URL . '/') . 'payment/?id=' . intval($data['ID']);
            $text = str_replace('{LINK_PAYMENT_URL}', $paymentURL, $text);
        }
    } elseif ($type == 5) {
        $loginURL = (substr(SITE_URL, -1, 1) == '/' ? SITE_URL : SITE_URL . '/') . 'login/';
        $text = str_replace('{USER_LOGIN_PAGE}', $loginURL, $text);
    }
    $text = str_replace('{MY_SITE_NAME}', SITE_NAME, $text);
    $text = str_replace('{MY_SITE_URL}', SITE_URL, $text);
    $text = str_replace('{MY_SITE_DESC}', SITE_DESC, $text);
    $text = str_replace('{LINK_RECIPR_URL}', DEFAULT_RECPR_URL, $text);
    $text = str_replace('{LINK_RECIPR_TITLE}', DEFAULT_RECPR_TITLE, $text);

    foreach ($data as $k => $v) {
        $text = str_replace("{" . $prefix . $k . "}", $v, $text);
    }

    return $text;
}

function get_emailer() {
    global $db, $tables;

    $sql = "SELECT `NAME`, `EMAIL` FROM `" . TABLE_PREFIX . "USER` WHERE `LEVEL` = '1' ORDER BY `REGISTRATION_DATE` ASC LIMIT 1";

    $user_info = $db->GetRow($sql);

    require_once 'libs/phpmailer/class.phpmailer.php';
    require_once 'libs/phpmailer/class.phpmailercustom.php';

    $mail = new PHPMailerCustom();
    $mail->PluginDir = 'libs/phpmailer/';
    $mail->From = $user_info['EMAIL'];
    $mail->FromName = $user_info['NAME'];
    $mail->Mailer = EMAIL_METHOD;

    unset($user_info, $sql);

    switch (EMAIL_METHOD) {
        case 'smtp' :
            $mail->Host = EMAIL_SERVER;
            if (strlen(EMAIL_USER) > 0) {
                $mail->SMTPAuth = true;
                $mail->Username = EMAIL_USER;
                $mail->Password = EMAIL_PASS;
            }
            break;
        case 'sendmail' :
            $mail->Sendmail = EMAIL_SENDMAIL;
            break;
    }
    return $mail;
}

function get_emailer_admin() {
    global $tables, $db;

    $sql = "SELECT `NAME`, `EMAIL` FROM `" . TABLE_PREFIX . "USER` WHERE `LEVEL` = '1' ORDER BY `REGISTRATION_DATE` ASC LIMIT 1";

    $user_info = $db->GetRow($sql);

    require_once 'libs/phpmailer/class.phpmailer.php';
    require_once 'libs/phpmailer/class.phpmailercustom.php';

    $mail = new PHPMailerCustom();
    $mail->PluginDir = 'libs/phpmailer/';
    $mail->From = $user_info['EMAIL'];
    $mail->FromName = $user_info['NAME'];
    $mail->Sender = $user_info['EMAIL'];
    $mail->Mailer = EMAIL_METHOD;

    unset($user_info, $sql);

    switch (EMAIL_METHOD) {
        case 'smtp':
            $mail->Host = EMAIL_SERVER;
            if (strlen(EMAIL_USER) > 0) {
                $mail->SMTPAuth = true;
                $mail->Username = EMAIL_USER;
                $mail->Password = EMAIL_PASS;
            }
            break;
        case 'sendmail':
            $mail->Sendmail = EMAIL_SENDMAIL;
            break;
    }
    return $mail;
}

/**
 * Get email template (title, subject, body) from DB
 * If it does not exists get it "hardcoded" from email templates file
 *
 * @param string Email tpl name, simmilar to ID field from PLD_CONFIG DB table
 * @return array Email template (boolean FALSE on error)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function get_email_template($tpl) {
    global $tables, $db;
    require_once 'include/email_templates.php';

    if (is_numeric($tpl)) {
        $tplID = $tpl;
    } else {
        //Get email template ID
        $tplID = $db->GetOne("SELECT `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` = " . $db->qstr($tpl));
    }

    if (!$tplID) {
        //No email template ID found in config DB table, try default email templates
        if (isset($constant_email_tpl[$tpl]) && is_array($constant_email_tpl[$tpl])) {
            return $constant_email_tpl[$tpl];
        }
    } else {
        //Email template ID found, get email template
        $tplSql = "SELECT `TITLE`, `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = " . $db->qstr($tplID);
        $dbEmailTpl = $db->GetRow($tplSql);

        if (is_array($dbEmailTpl) && !empty($dbEmailTpl)) {
            //Email template found, return it
            return $dbEmailTpl;
        } elseif (isset($constant_email_tpl[$tpl]) && is_array($constant_email_tpl[$tpl])) {
            //No email template found in DB, return default
            return $constant_email_tpl[$tpl];
        }
    }

    //No email template found, not in DB nor in default email templates
    return false;
}

function format_email($address, $name) {
    $name = trim($name);
    $address = trim($address);
    if ($name)
        return "{$name} <{$address}>";

    return $address;
}

function db_replace($table, $data, $keyCol) {
    global $tables, $db;
    foreach ($data as $key => $val) {
        if (substr($tables[$table]['fields'][$key], 0, 1) == 'T')
            $data[$key] = $db->DBDate($val);
        else
            $data[$key] = $db->qstr($val);
    }

    $result = $db->Replace($tables[$table]['name'], $data, $keyCol, false);
	if($db->ErrorNo()!=0 && DEBUG) {
		var_dump(debug_backtrace());
		echo $db->ErrorMsg();
		exit();
	}
	return $result;
}

/**
 * Send submit/reject notifications
 * @param mixed $data if type is array it is considered a link associative array; otherwise it is considered a link id
 * @param boolean $update if change owner notification type
 * @param boolean $RegularLink if notification is for a regular link or a reviewed link
 * @author dcb
 */
function send_status_notifications($data, $update = true, $RegularLink = true, $CustomReject = false) {
    global $db, $tables;
    if (DEMO)
        return;

    if (is_array($data))
        $id = $data['ID'];
    else {
        $id = $data;
        $data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($id));
    }

    if ($data['OWNER_NOTIF'] >= 2 && $RegularLink == true)
        return;
    if ($data['STATUS'] == 0 || $CustomReject == true)
        $tid = ($RegularLink === false ? NTF_REVIEW_REJECT_TPL : NTF_REJECT_TPL);
    elseif ($data['STATUS'] == 2)
        $tid = ($RegularLink === false ? NTF_REVIEW_APPROVE_TPL : NTF_APPROVE_TPL);
    else
        return;

    $tmpl = get_email_template($tid);

    if ($tmpl) {
        $mail = get_emailer();
        $mail->Body = replace_email_vars($tmpl['BODY'], $data, 2);
        $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);
        if ($data['OWNER_EMAIL']) {
            $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
            $sent = $mail->Send();
            if ($update)
                $db->Execute("UPDATE `{$tables['link']['name']}` SET `OWNER_NOTIF` = '2' WHERE `ID` = " . $db->qstr($id));
        }
    }
}

/**
 * Send submit/reject notifications
 * @param mixed $data if type is array it is considered a link associative array; otherwise it is considered a link id
 * @param boolean $update if change owner notification type
 * @param boolean $RegularLink if notification is for a regular link or a reviewed link
 * @author dcb
 */
function send_status_notificationsa($data, $update = true, $RegularArticle = true, $CustomReject = false) {
    global $db, $tables;
    if (DEMO)
        return;

    if (is_array($data))
        $id = $data['ID'];

    if ($data['OWNER_NOTIF'] >= 2 && $RegularArticle == true)
        return;
    if ($data['STATUS'] == 0 || $CustomReject == true)
        $tid = ($RegularArticle === false ? NTF_REVIEWA_REJECT_TPL : NTF_REJECTA_TPL);
    elseif ($data['STATUS'] == 2)
        $tid = ($RegularArticle === false ? NTF_REVIEWA_APPROVE_TPL : NTF_APPROVEA_TPL);
    else
        return;


    $tmpl = get_email_template($tid);
    if ($tmpl) {
        $mail = get_emailer();
        $mail->Body = replace_email_vars($tmpl['BODY'], $data, 7);
        $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 7);
        if ($data['OWNER_EMAIL']) {
            $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
            $sent = $mail->Send();
        }
    }
}

/**
 * Send reciprocal link expiration notifications
 * @param mixed $data if type is array it is considered a link associative array; otherwise it is considered a link id
 * @author dcb
 */
function send_expired_notifications($data) {
    global $db, $tables;

    if (DEMO)
        return;

    if (is_array($data))
        $id = $data['ID'];
    else {
        $id = $data;
        $data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($id));
    }

    $tid = NTF_EXPIRED_TPL;

    $tmpl = get_email_template($tid);
    if ($tmpl) {
        $mail = get_emailer();
        $mail->Body = replace_email_vars($tmpl['BODY'], $data, 2);
        $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);
        if ($data['OWNER_EMAIL']) {
            $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
            $sent = $mail->Send();
        }
    }
}

/**
 * Send  link expiration notifications
 * @param mixed $data if type is array it is considered a link associative array; otherwise it is considered a link id
 * @author 
 */
function send_expired_link_notification($data) {
    global $db, $tables;

    if (DEMO)
        return;

    if (is_array($data))
        $id = $data['ID'];
    else {
        $id = $data;
        $data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($id));
    }

    $tid = NTF_EXPIRED_LINK_TPL;

    $tmpl = get_email_template($tid);
    if ($tmpl) {
        $mail = get_emailer();
        $mail->Body = replace_email_vars($tmpl['BODY'], $data, 2);
        $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);
        if ($data['OWNER_EMAIL']) {
            $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
            $sent = $mail->Send();
        }
    }
}

/**
 * Send submit notifications
 * @param mixed $data if type is array it is considered a link associative array; otherwise it is considered a link id
 * @param boolean $update if change owner notification type
 * @param boolean $RegularLink if notification is for a regular link or a reviewed link
 */
function send_submit_notifications($data, $RegularLink = true) {
    global $db, $tables, $notif_msg;

    if (DEMO)
        return false;

    $EmailTemplate = ($RegularLink === false ? NTF_REVIEW_SUBMIT_TPL : NTF_SUBMIT_TPL);

    $tmpl = get_email_template($EmailTemplate);

    if (($tmpl) && ($data['OWNER_EMAIL'] != '')) {
        $mail = get_emailer_admin();
        $mail->Body = replace_email_vars($tmpl['BODY'], $data, 2);
        $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);
        $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
        $sent = $mail->Send();
    }

    $tmpl = $notif_msg['submit'];

    $admin = $db->GetRow("SELECT `ID`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' AND `ACTIVE` = '1' AND `SUBMIT_NOTIF` = '1' LIMIT 1");

    $where = (!empty($admin['ID']) ? 'AND `ID` != ' . $db->qstr($admin['ID']) : '');

    $users = $db->GetAll("SELECT `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ACTIVE` = '1' AND `SUBMIT_NOTIF` = '1' {$where}");

    $mail = get_emailer_admin();
    $mail->Body = replace_email_vars($tmpl['BODY'], $data, 2);
    $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);

    if (!empty($admin['EMAIL']))
        $mail->AddAddress($admin['EMAIL'], $admin['NAME']);

    if (is_array($users) && !empty($users))
        foreach ($users as $user)
            $mail->AddBCC($user['EMAIL'], $user['NAME']);

    $sent = $mail->Send();
}

function send_submit_notificationsa($data, $RegularArticle = true) {
    global $db, $tables, $notif_msg;

    if (DEMO)
        return false;

    $EmailTemplate = ($RegularArticle === false ? NTF_REVIEWA_SUBMIT_TPL : NTF_SUBMITA_TPL);

    $tmpl = get_email_template($EmailTemplate);

    if ($tmpl) {
        $mail = get_emailer_admin();
        $mail->Body = replace_email_vars($tmpl['BODY'], $data, 7);
        $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 7);
        $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
        $sent = $mail->Send();
    }

    $tmpl = $notif_msg['submitarticle'];

    $admin = $db->GetRow("SELECT `ID`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' AND `ACTIVE` = '1' AND `SUBMIT_NOTIF` = '1' LIMIT 1");

    $where = (!empty($admin['ID']) ? 'AND `ID` != ' . $db->qstr($admin['ID']) : '');

    $users = $db->GetAll("SELECT `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ACTIVE` = '1' AND `SUBMIT_NOTIF` = '1' {$where}");

    $mail = get_emailer_admin();
    $mail->Body = replace_email_vars($tmpl['BODY'], $data, 7);
    $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 7);

    if (!empty($admin['EMAIL']))
        $mail->AddAddress($admin['EMAIL'], $admin['NAME']);

    if (is_array($users) && !empty($users))
        foreach ($users as $user)
            $mail->AddBCC($user['EMAIL'], $user['NAME']);

    $sent = $mail->Send();
}

function send_payment_notifications($pdata, $ldata) {
    global $db, $tables, $notif_msg;

    if (DEMO)
        return false;

    $pdata['SUCCESS'] = $pdata['CONFIRMED'] ? 'successful' : 'failed';

    $tmpl = get_email_template(NTF_PAYMENT_TPL);
    if ($tmpl) {
        $mail = get_emailer_admin();
        $body = replace_email_vars($tmpl['BODY'], $ldata, 2);
        $subject = replace_email_vars($tmpl['SUBJECT'], $ldata, 2);
        $mail->Body = replace_email_vars($body, $pdata, 3);
        $mail->Subject = replace_email_vars($subject, $pdata, 3);
        $mail->AddAddress($ldata['OWNER_EMAIL'], $ldata['OWNER_NAME']);
        $sent = $mail->Send();
    }

    /*if ($ldata['URL'] == '') {
        $tmpl = $notif_msg['payment_article'];
    } else {*/
        $tmpl = $notif_msg['payment'];
    //}

    $admin = $db->GetRow("SELECT `ID`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' AND `ACTIVE` = '1' AND `SUBMIT_NOTIF` = '1' LIMIT 1");

    $where = (!empty($admin['ID']) ? 'AND `ID` != ' . $db->qstr($admin['ID']) : '');

    $users = $db->GetAll("SELECT `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ACTIVE` = '1' AND `PAYMENT_NOTIF` = '1' {$where}");

    $mail = get_emailer();
    $body = replace_email_vars($tmpl['BODY'], $ldata, 2);
    $subject = replace_email_vars($tmpl['SUBJECT'], $ldata, 2);

    $mail->Body = replace_email_vars($body, $pdata, 3);

    $mail->Subject = replace_email_vars($subject, $pdata, 3);




    if (!empty($admin['EMAIL']))
        $mail->AddAddress($admin['EMAIL'], $admin['NAME']);

    if (is_array($users) && !empty($users))
        foreach ($users as $user)
            $mail->AddBCC($user['EMAIL'], $user['NAME']);

    $sent = $mail->Send();
}

function date_addition($timestamp, $months) {
    $d = getdate($timestamp);
    $mon = $months % 12;
    $years = $months / 12;
    $mytime = mktime($d['hours'], $d['minutes'], $d['seconds'], $d['mon'] + $mon, 1, $d['year'] + $years);
    $days = min($d['mday'], date('t', $mytime));
    $mytime += ($days - 1) * 86400;

    return $mytime;
}

function calculate_expiry_date($start, $units, $um) {
    switch ($um) {
        case 1 :
            $mul = 1;
            break;
        case 2 :
            $mul = 3;
            break;
        case 3 :
            $mul = 6;
            break;
        case 4 :
            $mul = 12;
            break;
        default :
            $mul = 0;
            break;
    }
    if ($mul != 0)
        return date_addition($start, $units * $mul);

    return 0;
}

function log_ipn_results($success, $ipn_data, $last_error, $ipn_response, $pid)
{
	// Timestamp
	$text = '['.date ('m/d/Y g:i A').'] - ';

	// Success or failure being logged?
	if ($success){

		$text .= "SUCCESS!\n";
		$success = 2;
	}
	else
	{
		$text .= 'FAIL: '.$last_error."\n";
		$success= -1;
	}
	// Log the POST variables
	$text .= "IPN POST Vars from Paypal:\n";
	$raw   = '';
	foreach ($ipn_data as $key=>$value)
	{
		$text .= "{$key}={$value}, ";
		$raw  .= "{$key}\t{$value}\n";
	}
	$data['email']    = $ipn_data['payer_email'];
	$data['name']     = $ipn_data['last_name'].' '.$ipn_data['first_name'];
	$data['link_id']  = $ipn_data['item_number'];
	$data['quantity'] = $ipn_data['quantity'];#mc_gross
	$data['total']    = $ipn_data['mc_gross'];
	$data['payment_type']    = $ipn_data['payment_type'];#if echeck or instant
	$data['payment_status']    = $ipn_data['payment_status']; #check for status as echeck does not clear
	$txn_type = $ipn_data['txn_type'];
	if($txn_type != "subscr_signup") {
		//update_link_payment($data['link_id'], $data, $success, $raw);
		update_link_payment($_GET['pid'], $data, $success, $raw);
	}
	// Log the response from the paypal server
	$text .= "\nIPN Response from Paypal Server:\n ".$ipn_response;

	// Write to log
	$fp = fopen(INSTALL_PATH.'temp/ipn_log.txt','a');
	fwrite ($fp, $text . "\n\n");
	fclose ($fp);  // close file
}

function update_link_payment($pid, $data, $success, $raw) {
	$db = Phpld_Db::getInstance()->getAdapter();
	$tables = Phpld_Db::getInstance()->getTables();
    $pdata = $db->GetRow("SELECT * FROM `{$tables['payment']['name']}` WHERE `ID` = " . $db->qstr($pid));
    if (!$pdata['ID'])
        return false;
    $pdata['NAME'] = $data['name'];
    $pdata['EMAIL'] = $data['email'];
    if ($data['total'] != '')
        $pdata['PAYED_TOTAL'] = $data['total'];
    if ($data['quantity'] != '')
        $pdata['PAYED_QUANTITY'] = $data['quantity'];
    $pdata['CONFIRMED'] = $success;
    $pdata['CONFIRM_DATE'] = gmdate('Y-m-d H:i:s');
    $pdata['RAW_LOG'] = $raw;
    if ($data['payment_type'] != '')
        $pdata['PAYMENT_TYPE'] = $data['payment_type'];#if echeck or instant
    if ($data['payment_status'] != '')
        $pdata['PAYMENT_STATUS'] = $data['payment_status'];#check for status as echeck does not clear
    $pdata['TOTAL'] = $pdata['TOTAL'];

    if ($pdata['PAYED_TOTAL'] < $pdata['TOTAL']) {
        $pdata['CONFIRMED'] = -1;
    }
    if (preg_match("/Pending/i", $data['payment_status'])) {
        $pdata['CONFIRMED'] = 0;
    }


    db_replace('payment', $pdata, 'ID');
    $ldata = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($pdata['LINK_ID']));
    send_payment_notifications($pdata, $ldata);

    //Take no action if link not found
    if ((empty($ldata['ID'])) && ($pdata['CONFIRMED'] != 2)) {
        return false;
    } else {
        $ldata['PAYED'] = '1';

        // set the expiry_date
        $exp_date = calculate_expiry_date(time(), $pdata['QUANTITY'], $pdata['UM']);
        if ($exp_date != 0) {
            $ldata['EXPIRY_DATE'] = gmdate('Y-m-d H:i:s', $exp_date);
        }

        // update status?
        $needApprov = $db->GetOne("
			SELECT lt.REQUIRE_APPROVAL
			FROM `{$tables['payment']['name']}` p, `{$tables['link']['name']}` l, `{$tables['link_type']['name']}` lt
			WHERE p.ID = '{$pdata['id']}'
				AND p.LINK_ID = l.ID
				AND l.LINK_TYPE = lt.ID
		");

        if ($needApprov == 0) {
            $ldata['STATUS'] = 2;
        }
    }
    db_replace('link', $ldata, 'ID');
}

function sprint_r($val) {
    ob_start();
    print_r($val);
    $ret = ob_get_contents();
    ob_end_clean();

    return $ret;
}

function read_echo($val) {
    ob_start();
    echo $val;
    $ret = ob_get_contents();
    ob_end_clean();

    return $ret;
}

function numeric_entify_utf8($utf8_string) {
    $out = "";
    $ns = strlen($utf8_string);
    for ($nn = 0; $nn < $ns; $nn++) {
        $ch = $utf8_string[$nn];
        $ii = ord($ch);
        //1 7 0bbbbbbb (127)
        if ($ii < 128)
            $out .= $ch;
        //2 11 110bbbbb 10bbbbbb (2047)
        else
        if ($ii >> 5 == 6) {
            $b1 = ($ii & 31);
            $nn++;
            $ch = $utf8_string[$nn];
            $ii = ord($ch);
            $b2 = ($ii & 63);
            $ii = ($b1 * 64) + $b2;
            $ent = sprintf("&#%d;", $ii);
            $out .= $ent;
        }
        //3 16 1110bbbb 10bbbbbb 10bbbbbb
        else
        if ($ii >> 4 == 14) {
            $b1 = ($ii & 31);
            $nn++;
            $ch = $utf8_string[$nn];
            $ii = ord($ch);
            $b2 = ($ii & 63);
            $nn++;
            $ch = $utf8_string[$nn];
            $ii = ord($ch);
            $b3 = ($ii & 63);
            $ii = ((($b1 * 64) + $b2) * 64) + $b3;
            $ent = sprintf("&#%d;", $ii);
            $out .= $ent;
        }
        //4 21 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
        else
        if ($ii >> 3 == 30) {
            $b1 = ($ii & 31);
            $nn++;
            $ch = $utf8_string[$nn];
            $ii = ord($ch);
            $b2 = ($ii & 63);
            $nn++;
            $ch = $utf8_string[$nn];
            $ii = ord($ch);
            $b3 = ($ii & 63);
            $nn++;
            $ch = $utf8_string[$nn];
            $ii = ord($ch);
            $b4 = ($ii & 63);
            $ii = ((((($b1 * 64) + $b2) * 64) + $b3) * 64) + $b4;
            $ent = sprintf("&#%d;", $ii);
            $out .= $ent;
        }
    }

    return $out;
}

function xml_utf8_encode($str) {
    return numeric_entify_utf8(htmlspecialchars($str));
}

/**
 * Add slashes before "'" and "\" characters
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function sql_addslashes($string = '') {
    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace('\'', '\'\'', $string);

    return $string;
}

function find_child_categories() {
    global $tables, $db, $data, $u;

    $child_count = 0;

    $rs = $db->CacheExecute("SELECT `CATEGORY_ID` FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = " . $db->qstr($u));

    while (!$rs->EOF) {
        $row = $rs->FetchRow();

        $category = $row['CATEGORY_ID'];

        while ($category != 0) {
            $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($category);
            $category = $db->GetOne($sql);
            if ($category == $data['CATEGORY_ID']) {
                $child_count++;
                break;
            }
        }
    }
    $rs->Close();

    return $child_count;
}

function delete_child_categories() {
    global $tables, $db, $id, $u;

    $child_count = 0;

    $rs = $db->Execute("SELECT `ID`, `CATEGORY_ID` FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = " . $db->qstr($u));

    while (!$rs->EOF) {
        $row = $rs->FetchRow();

        $category = $row['CATEGORY_ID'];

        while ($category != 0) {
            $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($category);
            $category = $db->GetOne($sql);
            if ($category == $id) {
                $db->Execute("DELETE FROM `{$tables['user_permission']['name']}` WHERE `ID` = " . $db->qstr($row['ID']));
                break;
            }
        }
    }
    $rs->Close();
}

/**
 * Build URL of a category
 * @param  integer  ID of category to build it's URL
 * @return string   Partial URL to be added to document root ("DOC_ROOT/")
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function buildCategUrl($id, $url = '') {
    global $db, $tables;

    //Check if category is not symbolic
    $categInfo = $db->GetRow("SELECT `SYMBOLIC`, `SYMBOLIC_ID` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($id));
    if ($categInfo['SYMBOLIC'] == 1 && !empty($categInfo['SYMBOLIC_ID'])) {
        //Category is symbolic, use it's symbolic id instead
        $id = intval($categInfo['SYMBOLIC_ID']);
    }

    //Build friendly URL for rewriting
    $category = $db->GetRow("SELECT `PARENT_ID`, `TITLE_URL` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($id));

    if (is_array($category) && !empty($category)) {
        $nextUrl = $category['TITLE_URL'] . '/' . $url;
        $url = buildCategUrl($category['PARENT_ID'], $nextUrl);
    }
    return trim($url);
}

/**
 * Build cache title of a category
 * @param  integer  ID of category to build it's cache title
 * @return string   Category title to be added into category cache title
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function buildCategUrlTitle($id = '0', $urlTitle = '') {
    global $db, $tables;

    $sql = "SELECT `PARENT_ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($id);
    $category = $db->GetRow($sql);

    if (is_array($category) && !empty($category)) {
        $nextUrlTitle = $category['TITLE'] . ': ' . $urlTitle;
        $urlTitle = buildCategUrlTitle($category['PARENT_ID'], $nextUrlTitle);
    }

    //Clean URL title
    $urlTitle = preg_replace('`:[\s]*$`', '', $urlTitle);

    return trim($urlTitle);
}

function get_editor_permission($user_id) {
    global $tables, $db, $user_grant_permission, $user_permission, $user_grant_permission_array, $user_permission_array;

    $all_first_iteration = true;
    $first_iteration = true;

    $rs = $db->Execute("SELECT `CATEGORY_ID` FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = " . $db->qstr($user_id));

    while (!$rs->EOF) {
        $row = $rs->FetchRow();
        if ($all_first_iteration) {
            $user_permission = "ID = " . $db->qstr($row['CATEGORY_ID']);
            $all_first_iteration = false;
        }
        else
            $user_permission .= " OR ID = " . $db->qstr($row['CATEGORY_ID']);

        $user_permission_array[] = $row['CATEGORY_ID'];

        $new_sub_categories = get_sub_categories($row['CATEGORY_ID']);

        foreach ($new_sub_categories as $category_id) {
            if ($first_iteration) {
                $user_grant_permission = "ID = " . $db->qstr($category_id);
                $user_permission .= " OR ID = " . $db->qstr($category_id);
                $first_iteration = false;
            } else {
                $user_grant_permission .= " || ID = " . $db->qstr($category_id);
                $user_permission .= " OR ID = " . $db->qstr($category_id);
            }
            $user_permission_array[] = $category_id;
            $user_grant_permission_array[] = $category_id;
        }
    }
    $rs->Close();
}

function get_sub_categories($id) {
    global $db, $tables;

    $categs = array();
    $rs = $db->CacheExecute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " ORDER BY `TITLE`");
    while (!$rs->EOF) {
        $categs[] = $rs->Fields('ID');
        $categs = array_merge($categs, get_sub_categories($rs->Fields('ID')));
        $rs->MoveNext();
    }

    return $categs;
}

/**
 * Determine link type
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function determine_link_type($type = 0) {
    if (!preg_match('`^[\d]+$`', $type))
        return false;

    $type = ($type < 0 || $type > 8 ? 0 : intval($type));

    switch ($type) {
        case 8 :
            $return = 'featured_lt';
            break;
        case 7 :
            $return = 'normal_lt';
            break;

        case 6 :
            $return = 'featured_dl';
            break;
        case 5 :
            $return = 'normal_dl';
            break;
        case 4 :
            $return = 'featured';
            break;
        case 3 :
            $return = 'reciprocal';
            break;
        case 2 :
            $return = 'normal';
            break;
        case 1 :
            $return = 'free';
            break;
        case 0 :
        default :
            $return = 'none';
            break;
    }

    return (!empty($return) ? $return : false);
}

/**
 * Update email address and name for all links
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function update_link_owner($user_id = 0) {
    global $db, $tables;

    if (empty($user_id))
        return 0;
    else {
        $data = $db->GetRow("SELECT `NAME` AS `OWNER_NAME`, `EMAIL` AS `OWNER_EMAIL` FROM `{$tables['user']['name']}` WHERE `ID` = " . $db->qstr($user_id));

        $where = " `OWNER_ID` = " . $db->qstr($user_id);
        if (!$db->AutoExecute($tables['link']['name'], $data, 'UPDATE', $where))
            return 0;
        else {
            if (!$db->AutoExecute($tables['link_review']['name'], $data, 'UPDATE', $where))
                return 0;
            else {
                $where = " `USER_ID` = " . $db->qstr($user_id) . " AND `TYPE` = '1' ";
                if (!$db->AutoExecute($tables['comment']['name'], $data, 'UPDATE', $where))
                    return 0;
            }
        }
    }
    return 1;
}

/**
 * Create password
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function create_password($length = 6) {
    $phrase = "";
    $chars = array(
        "1", "2", "3", "4", "5", "6", "7", "8", "9", "0",
        "a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J",
        "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r", "R", "s", "S", "t", "T",
        "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z"
    );

    $count = count($chars) - 1;

    srand((double) microtime() * 1234567);

    for ($i = 0; $i < $length; $i++)
        $phrase .= $chars[rand(0, $count)];

    return $phrase;
}

function construct_mod_rewrite_path($cat_id) {
    global $tables, $db;

    $category = $cat_id;
    $first_iteration = true;

    while ($category != 0) {
        //Get category title and add into URL
        $parent_row = $db->GetRow("SELECT `TITLE`, `TITLE_URL` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($category));
        if (empty($parent_row['TITLE_URL'])) {
            $parent_row['TITLE_URL'] = preg_replace('`[^\w_-]`', '_', $parent_row['TITLE']);
            $parent_row['TITLE_URL'] = str_replace('__', '_', $parent_row['TITLE_URL']);
        }
        if ($first_iteration) {
            $mod_rewrite_url = $parent_row['TITLE_URL'];
            $first_iteration = false;
        }
        else
            $mod_rewrite_url = $parent_row['TITLE_URL'] . "/" . $mod_rewrite_url;

        $category = $db->GetOne("SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($category));
    }

    $mod_rewrite_url = DOC_ROOT . "/{$mod_rewrite_url}";

    return $mod_rewrite_url;
}

/**
 * Disable any caching by the browser
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * http://www.faqs.org/rfcs/rfc2616
 */
function disable_browser_cache() {
    @ header('Expires: Mon, 14 Oct 2002 05:00:00 GMT');               // Date in the past
    @ header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT'); // Always modified
    @ header('Cache-Control: no-store, no-cache, must-revalidate');   // HTTP 1.1
    @ header('Cache-Control: post-check=0, pre-check=0', false);
    @ header('Pragma: no-cache');                                     // HTTP 1.0
}

/**
 * Send HTTP/1.1 Status header
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6.1.1
 */
function httpstatus($status_code) {
    static $http = array(
100 => "HTTP/1.1 100 Continue",
 101 => "HTTP/1.1 101 Switching Protocols",
 200 => "HTTP/1.1 200 OK",
 201 => "HTTP/1.1 201 Created",
 202 => "HTTP/1.1 202 Accepted",
 203 => "HTTP/1.1 203 Non-Authoritative Information",
 204 => "HTTP/1.1 204 No Content",
 205 => "HTTP/1.1 205 Reset Content",
 206 => "HTTP/1.1 206 Partial Content",
 300 => "HTTP/1.1 300 Multiple Choices",
 301 => "HTTP/1.1 301 Moved Permanently",
 302 => "HTTP/1.1 302 Found",
 303 => "HTTP/1.1 303 See Other",
 304 => "HTTP/1.1 304 Not Modified",
 305 => "HTTP/1.1 305 Use Proxy",
 307 => "HTTP/1.1 307 Temporary Redirect",
 400 => "HTTP/1.1 400 Bad Request",
 401 => "HTTP/1.1 401 Unauthorized",
 402 => "HTTP/1.1 402 Payment Required",
 403 => "HTTP/1.1 403 Forbidden",
 404 => "HTTP/1.1 404 Not Found",
 405 => "HTTP/1.1 405 Method Not Allowed",
 406 => "HTTP/1.1 406 Not Acceptable",
 407 => "HTTP/1.1 407 Proxy Authentication Required",
 408 => "HTTP/1.1 408 Request Time-out",
 409 => "HTTP/1.1 409 Conflict",
 410 => "HTTP/1.1 410 Gone",
 411 => "HTTP/1.1 411 Length Required",
 412 => "HTTP/1.1 412 Precondition Failed",
 413 => "HTTP/1.1 413 Request Entity Too Large",
 414 => "HTTP/1.1 414 Request-URI Too Large",
 415 => "HTTP/1.1 415 Unsupported Media Type",
 416 => "HTTP/1.1 416 Requested range not satisfiable",
 417 => "HTTP/1.1 417 Expectation Failed",
 500 => "HTTP/1.1 500 Internal Server Error",
 501 => "HTTP/1.1 501 Not Implemented",
 502 => "HTTP/1.1 502 Bad Gateway",
 503 => "HTTP/1.1 503 Service Unavailable",
 504 => "HTTP/1.1 504 Gateway Time-out"
    );

    if (!empty($status_code) && array_key_exists($status_code, $http)) {
        if (version_compare(phpversion(), '4.3.0', '>=')) {
            @ header($http[$status_code], true, $status_code);
        } else {
            @ header($http[$status_code], true, $status_code);
        }
    }
}

/**
 * Tweak memory limit of PHP, usually to increase it for havy operations like backups or cache building on huge databases
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function tweak_memory_limit($memory_limit = 16) {
    // Tweak memory limit only if PHP is not in safe mode
    if (!ini_get('safe_mode') && function_exists('ini_set')) {
        $current_memory_limit = preg_replace('`([^\d])`', '', ini_get('memory_limit'));
        $memory_limit = preg_replace('`([^\d])`', '', $memory_limit);
        $memory_limit = (preg_match('`[\d]+`', $memory_limit) && $memory_limit > 4 ? intval($memory_limit) : intval($current_memory_limit));

        @ ini_set('memory_limit', $memory_limit . "M");

        unset($memory_limit, $current_memory_limit);
    }
}

/**
 * Tweak maximum execution time of a script, usually to increase it for time taking operations
 * like backups or cache building on huge databases
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function tweak_time_limit($time_limit = 90) { //zero == unlimited
    // Tweak max execution time only if PHP is not in safe mode
    if (!ini_get('safe_mode') && function_exists('set_time_limit')) {
        $time_limit = preg_replace('`([^\d])`', '', $time_limit);
        $time_limit = (preg_match('`[\d]+`', $time_limit) && $time_limit >= 10 ? intval($time_limit) : ini_get('max_execution_time'));

        @ set_time_limit($time_limit);

        unset($time_limit);
    }
}

/**
 * Calculate difference (in seconds) of two given dates
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function dateTimeDifference($greaterdate, $smallerdate) {
    $greaterdate = str_replace('-', '/', $greaterdate);
    $smallerdate = str_replace('-', '/', $smallerdate);

    $diff = (strtotime($greaterdate) - strtotime($smallerdate));

    return $diff;
}

/**
 * Calculate minutes from seconds
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function second2min($seconds) {
    $minutes = ceil($seconds / 60);

    return $minutes;
}

/**
 * Calculate hours from seconds
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function second2hour($seconds) {
    $hours = ceil(($seconds / 60) / 60);

    return $hours;
}

/**
 * Calculate days from seconds
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function second2day($seconds) {
    //1 day = 86400 secs
    $hours = ceil($seconds / 86400);

    return $hours;
}

/**
 * Calculate weeks from seconds
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function second2week($seconds) {
    //1 week = 604800 secs
    $hours = ceil($seconds / 604800);

    return $hours;
}

/**
 * A simple array serializer to send array values through URLs
 * @param array The array to be serialized
 * @param string String encoding (no value = no encoding, other values are: "gzip" and "base64")
 * @return string The serialized array as string
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function simple_serialize($array = array(), $encoding = '') {
    if (!is_array($array))
        return false;

    $string = serialize($array);
    switch ($encoding) {
        case 'base64' :
            $string = base64_encode($string);
            break;

        case 'gzip':
            $string = gzcompress($string, 6);
            break;
        default:
            break;
    }

    return $string;
}

/**
 * A simple string unserializer to read array values sent through URLs
 * @param string The string to be unserialized
 * @param string String encoding (no value = no encoding, other values are: "gzip" and "base64")
 * @return array The unserialized string as array
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function simple_unserialize($string = '', $encoding = '') {
    if (empty($string))
        return false;

    switch ($encoding) {
        case 'base64' :
            $string = base64_decode($string);
            break;

        case 'gzip':
            $string = gzuncompress($string);
            break;
        default:
            break;
    }
    $array = unserialize($string);

    return $array;
}

/**
 * Parse URL and return its components
 * @param string The URL to be parsed
 * @return array Submitted URL components
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function parseURL($url) {
    $url = trim($url);

    if (empty($url))
        return false;

    return parse_url($url);
}

/**
 * Print sizes in human readable format (e.g. 1Kb, 234Mb, 5Gb)
 * @param  integer Ugly, not formatted size
 * @return string  Nice, human readable size format
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function nicesize($size = '') {
    //Define storage units
    $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');

    $pos = 0;

    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }

    if ($size == 0) {
        //is empty
        return '-';
    } else {
        //Round up
        return round($size, 2) . ' ' . $units[$pos];
    }
}

/**
 * Parse URL and return its domain name
 * @param string  The URL to be parsed
 * @param boolean/integer Keep the www. prefix ()
 * @return string Domain name of URL
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function parseDomain($url, $keepWWW = 0) {
    $url = trim($url);

    if (empty($url))
        return false;

    if (strpos($url, 'http://') !== 0) {
        $url = "http://{$url}";
    }

    $output = parseURL($url);

    if (!isset($output['host']))
        return false;

    $pattern = array();
    //Remove http: and https: prefix
    $pattern[] = '`^http[s]?:`';
    //Remove ftp: prefix
    $pattern[] = '`^ftp:`';
    //Remove mailto: prefix
    $pattern[] = '`^mailto:`';

    if (!$keepWWW) {
        //Optional remove www. prefix
        $pattern[] = '`^www.`';
    }

    //Remove any dots as prefix
    $pattern[] = '`^\.`';
    //Remove any dots as suffix
    $pattern[] = '`\.$`';
    //Remove any unaccepted character
    $pattern[] = '`[^\w\d-\.]`';

    $output['host'] = preg_replace($pattern, '', $output['host']);

    return $output['host'];
}

/**
 * Draw unauthorized page
 * @param string Reasong of unauthorization
 * @param string Template file to load
 * @param bool   If unauthorized page content is returned or printed on screen
 * @return none/string  Page content or nothing because page is printed and script stopped
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function gotoUnauthorized($reason, $tplFile = 'views/index/unauthorized.tpl', $returnVal = false) {
    global $tpl;

    //Remove request variables
    unset($_POST, $_GET, $_REQUEST);

    //Provide a reason why access was unautorised
    $tpl->assign('unauthorizedReason', $reason);

    //Clean whitespace
    $tpl->load_filter('output', 'trimwhitespace');

    //Compress output for faster loading
    if (COMPRESS_OUTPUT == 1)
        $tpl->load_filter('output', 'CompressOutput');
	$tpl->addTemplateDir(INSTALL_PATH.'templates/Core/DefaultFrontend');
    $output = $tpl->fetch($tplFile);

    if ($returnVal) {
        return $output;
    } else {
        echo $output;
        exit();
    }
}

/**
 * Decode a gzip encoded message (when Content-encoding = gzip)
 * Currently requires PHP with zlib support
 * For details on the DEFLATE compression algorithm see (RFC 1951):
 * http://www.faqs.org/rfcs/rfc1951
 *
 * @author Aaron G.
 * @link   http://www.php.net/manual/en/function.gzencode.php#44470
 *
 * @param  string String to be decoded
 * @return string Decoded string
 */
function gz_decode($data) {
    if (!function_exists('gzinflate')) {
        //"gzinflate" function does not exist
        return false;
    }

    $len = strlen($data);

    if ($len < 18 || strcmp(substr($data, 0, 2), "\x1f\x8b")) {
        //Not GZIP format (See RFC 1952)
        return null;
    }

    //Compression method
    $method = ord(substr($data, 2, 1));
    //Flags
    $flags = ord(substr($data, 3, 1));

    if ($flags & 31 != $flags) {
        //Reserved bits are set -- NOT ALLOWED by RFC 1952
        return null;
    }

    //NOTE: $mtime may be negative (PHP integer limitations)
    $mtime = unpack("V", substr($data, 4, 4));
    $mtime = $mtime[1];
    $xfl = substr($data, 8, 1);
    $os = substr($data, 8, 1);
    $headerlen = 10;
    $extralen = 0;
    $extra = "";

    if ($flags & 4) {
        //2-byte length prefixed EXTRA data in header
        if ($len - $headerlen - 2 < 8) {
            //Invalid format
            return false;
        }

        $extralen = unpack("v", substr($data, 8, 2));
        $extralen = $extralen[1];

        if ($len - $headerlen - 2 - $extralen < 8) {
            //Invalid format
            return false;
        }

        $extra = substr($data, 10, $extralen);
        $headerlen += 2 + $extralen;
    }

    $filenamelen = 0;
    $filename = "";

    if ($flags & 8) {
        //C-style string file NAME data in header
        if ($len - $headerlen - 1 < 8) {
            //Invalid format
            return false;
        }

        $filenamelen = strpos(substr($data, 8 + $extralen), chr(0));

        if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
            //Invalid format
            return false;
        }

        $filename = substr($data, $headerlen, $filenamelen);
        $headerlen += $filenamelen + 1;
    }

    $commentlen = 0;
    $comment = "";

    if ($flags & 16) {
        //C-style string COMMENT data in header
        if ($len - $headerlen - 1 < 8) {
            //Invalid format
            return false;
        }

        $commentlen = strpos(substr($data, 8 + $extralen + $filenamelen), chr(0));

        if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
            //Invalid header format
            return false;
        }

        $comment = substr($data, $headerlen, $commentlen);
        $headerlen += $commentlen + 1;
    }

    $headercrc = "";

    if ($flags & 1) {
        //2-bytes (lowest order) of CRC32 on header present
        if ($len - $headerlen - 2 < 8) {
            //Invalid format
            return false;
        }

        $calccrc = crc32(substr($data, 0, $headerlen)) & 0xffff;
        $headercrc = unpack("v", substr($data, $headerlen, 2));
        $headercrc = $headercrc[1];

        if ($headercrc != $calccrc) {
            //Bad header CRC
            return false;
        }

        $headerlen += 2;
    }

    //GZIP FOOTER - These be negative due to PHPs limitations
    $datacrc = unpack("V", substr($data, -8, 4));
    $datacrc = $datacrc[1];
    $isize = unpack("V", substr($data, -4));
    $isize = $isize[1];

    //Perform the decompression:
    $bodylen = $len - $headerlen - 8;

    if ($bodylen < 1) {
        //This should never happen - IMPLEMENTATION BUG!
        return null;
    }

    $body = substr($data, $headerlen, $bodylen);
    $data = "";

    if ($bodylen > 0) {
        switch ($method) {
            case 8:
                //Currently the only supported compression method:
                $data = gzinflate($body);
                break;
            default:
                //Unknown compression method
                return false;
        }
    } else {
        //I'm not sure if zero-byte body content is allowed.
        //Allow it for now...  Do nothing...
    }

    // Verifiy decompressed size and CRC32:
    // NOTE: This may fail with large data sizes depending on how
    //      PHPs integer limitations affect strlen() since $isize
    //      may be negative for large sizes.
    if ($isize != strlen($data) || crc32($data) != $datacrc) {
        //Bad format! Length or CRC doesnt match!
        return false;
    }

    return trim($data);
}

/**
 * Decode a gzip encoded message (when Content-encoding = gzip)
 * Currently requires PHP with zlib support
 * For details on the DEFLATE compression algorithm see (RFC 1951):
 * http://www.faqs.org/rfcs/rfc1951
 *
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 *
 * @param  string String to be decoded
 * @return string Decoded string
 */
function decode_gzip($body) {
    if (function_exists('gzinflate')) {
        $body = substr($body, 10);
        //Inflate a deflated string
        return trim(gzinflate($body));
    } else {
        //Function does not exist
        return false;
    }
}

/**
 * Decode a zlib deflated message (when Content-encoding = deflate)
 * Currently requires PHP with zlib support
 * For details on the ZLIB compression algorithm see (RFC 1950):
 * http://www.faqs.org/rfcs/rfc1950
 *
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 *
 * @param  string String to be decoded
 * @return string Decoded string
 */
function decode_deflate($body) {
    if (function_exists('gzuncompress')) {
        //Uncompress the zlib compressed string
        return trim(gzuncompress($body));
    } else {
        //Function does not exist
        return false;
    }
}

/**
 * [DEBUG] Print an array
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function print_array($array) {
    echo "<div style=\"text-align:left;\"><pre>";
    print_r($array);
    echo "</pre></div>";
}

function send_status_notificationse($data, $update = true, $RegularLink = true, $CustomReject = false) {
    global $db, $tables;
    if (DEMO)
        return;
    if (is_array($data))
        $id = $data['ID'];
    else {
        $id = $data;
        $data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($id));
    }
    if ($data['STATUS'] == 2)
        $tid = ($RegularLink === false ? NTF_REVIEW_APPROVE_TPL : NTF_APPROVE_TPL);
    else
        return;

    $tmpl = get_email_template($tid);
    if ($tmpl) {
        $mail = get_emailer();
        $mail->Body = replace_email_vars($tmpl['BODY'], $data, 2);
        $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);
        if ($data['OWNER_EMAIL']) {
            $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
            $sent = $mail->Send();
            if ($update)
                $db->Execute("UPDATE `{$tables['link']['name']}` SET `OWNER_NOTIF` = '2' WHERE `ID` = " . $db->qstr($id));
        }
    }
}


function getHeader($header) {
    // Try to get it from the $_SERVER array first
    $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
    if (!empty($_SERVER[$temp])) {
        return $_SERVER[$temp];
    }

    // This seems to be the only way to get the Authorization header on
    // Apache
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (!empty($headers[$header])) {
            return $headers[$header];
        }
    }

    return false;
}

function isXmlHttpRequest() {
    return (getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
}

function get_user_level($uid) {
    global $db, $tables;

    $data = $db->cacheGetAll("SELECT * FROM `{$tables['user']['name']}` WHERE `ID`=" . $db->qstr($uid));

    return $data[0]['LEVEL'];
}

function get_user_permissions($uid) {
    global $db, $tables;
    $data = $db->cacheGetAll("SELECT `CATEGORY_ID` FROM `{$tables['user_permission']['name']}` WHERE `USER_ID`=" . $db->qstr($uid));
    return $data;
}

function get_user($id) {
    global $db, $tables;

    $data = $db->cacheGetAll("SELECT * FROM `{$tables['user']['name']}` WHERE `ID`=" . $db->qstr($id));

    return $data;
}

function has_rights_on_cat($uid, $cid) {
    global $db, $tables;
    $cat = getCategoryByID($cid);

    $data = $db->getOne("SELECT COUNT(`ID`) AS c FROM `{$tables['user_permission']['name']}` WHERE `USER_ID`=" . $db->qstr($uid) . " AND `CATEGORY_ID` = " . $db->qstr($cid));


    if ($data['c'] > 0) {
        return 1;
    } else
    if ($cat['PARENT_ID'] > 0)
        return has_rights_on_cat($uid, $cat['PARENT_ID']);
    else {
        return 0;
    }
}

function has_rights_on_all_cats($level) {
    if ($level == 3 || $level == 1) {
        return true;
    } else {
        return false;
    }
}

function user_needs_approval($uid, $cid) {
    global $db, $tables;
    $level = get_user_level($uid);

    $category = getCategoryByID($cid);



    //default actions from db
    $r = $db->CacheGetAll("SELECT * FROM `{$tables['user_default_actions']['name']}` ORDER BY `LEVEL_ID` ASC");
    $default = array();


    for ($i = 0; $i < count($r); $i++) {
        $default[$r[$i]['ID']] = $r[$i];
    }


    //actual user actions
    $r = $db->CacheGetAll("SELECT *
                            FROM `{$tables['user_default_actions']['name']}` AS `default`
                                INNER JOIN `{$tables['user_actions']['name']}` AS `actual`
                                ON `default`.`ID` = `actual`.`ACTION_ID`
                            WHERE `actual`.`USER_ID` = " . $db->qstr($uid) . "
                            ORDER BY `default`.`LEVEL_ID` ASC");
//                                INNER JOIN `{$tables['user_permission']['name']}` AS `perm`
//                                ON `perm`.`USER_ID` = `actual`.`USER_ID`

    $actual = array();


    for ($i = 0; $i < count($r); $i++) {
        $actual[$r[$i]['ACTION_ID']] = $r[$i]['VALUE'];
    }
    $rights = array();



    if ((has_rights_on_cat($uid, $cid) > 0) || (has_rights_on_all_cats($level))) {

        //add link rights go here: has id 4;
        if ($level != 1 && $level != 3) {
            $rights = rights_for_editor($actual, $default);
        } elseif ($level == 3) {
            $rights = rights_for_supereditor($actual, $default);
        } elseif ($level == 1) {

            $rights = rights_for_admin($actual, $default);
        }
    }
    return $rights;
}

function rights_for_editor($actual, $default) {
    if ($actual[4] != '') {
        $rights['addLink'] = $actual[4];
    } else
        $rights['addLink'] = $default[4]['VALUE'];
    if ($actual[5] != '') {
        $rights['editLink'] = $actual[5];
    } else
        $rights['editLink'] = $default[5]['VALUE'];
    if ($actual[6] != '') {
        $rights['delLink'] = $actual[6];
    } else
        $rights['delLink'] = $default[6]['VALUE'];
    if ($actual[7] != '') {
        $rights['addCat'] = $actual[10];
    } else
        $rights['addCat'] = $default[10]['VALUE'];
    //edit category is 11
    if ($actual[11] != '') {
        $rights['editCat'] = $actual[11];
    } else
        $rights['editCat'] = $default[11]['VALUE'];
    if ($actual[12] != '') {
        $rights['delCat'] = $actual[12];
    } else
        $rights['delCat'] = $default[12]['VALUE'];
    //artilcle related
    if ($actual[16] != '') {
        $rights['addArt'] = $actual[16];
    } else
        $rights['addArt'] = $default[16]['VALUE'];
    if ($actual[17] != '') {
        $rights['editArt'] = $actual[17];
    } else
        $rights['editArt'] = $default[17]['VALUE'];
    if ($actual[18] != '') {
        $rights['delArt'] = $actual[18];
    } else
        $rights['delArt'] = $default[18]['VALUE'];
    return $rights;
}

function rights_for_supereditor($actual, $default) {
    if ($actual[19] != '') {
        $rights['addLink'] = $actual[19];
    } else
        $rights['addLink'] = $default[19]['VALUE'];
    if ($actual[20] != '') {
        $rights['editLink'] = $actual[20];
    } else
        $rights['editLink'] = $default[20]['VALUE'];
    if ($actual[21] != '') {
        $rights['delLink'] = $actual[21];
    } else
        $rights['delLink'] = $default[21]['VALUE'];
    if ($actual[22] != '') {
        $rights['addCat'] = $actual[22];
    } else
        $rights['addCat'] = $default[22]['VALUE'];
    if ($actual[23] != '') {
        $rights['editCat'] = $actual[23];
    } else
        $rights['editCat'] = $default[23]['VALUE'];
    if ($actual[24] != '') {
        $rights['delCat'] = $actual[24];
    } else
        $rights['delCat'] = $default[24]['VALUE'];
    //artilcle related
    if ($actual[25] != '') {
        $rights['addArt'] = $actual[25];
    } else
        $rights['addArt'] = $default[25]['VALUE'];
    if ($actual[26] != '') {
        $rights['editArt'] = $actual[26];
    } else
        $rights['editArt'] = $default[26]['VALUE'];
    if ($actual[27] != '') {
        $rights['delArt'] = $actual[27];
    } else
        $rights['delArt'] = $default[27]['VALUE'];
    //page related
    if ($actual[28] != '') {
        $rights['addPage'] = $actual[28];
    } else
        $rights['addPage'] = $default[28]['VALUE'];
    if ($actual[29] != '') {
        $rights['editPage'] = $actual[29];
    } else
        $rights['editPage'] = $default[29]['VALUE'];
    if ($actual[30] != '') {
        $rights['delPage'] = $actual[30];
    } else
        $rights['delPage'] = $default[30]['VALUE'];
    return $rights;
}

function rights_for_admin($actual, $default) {


    if ($actual[37] != '') {
        $rights['addLink'] = $actual[37];
    } else
        $rights['addLink'] = $default[37]['VALUE'];
    if ($actual[38] != '') {
        $rights['editLink'] = $actual[38];
    } else
        $rights['editLink'] = $default[38]['VALUE'];
    if ($actual[39] != '') {
        $rights['delLink'] = $actual[39];
    } else
        $rights['delLink'] = $default[39]['VALUE'];
    if ($actual[40] != '') {
        $rights['addCat'] = $actual[40];
    } else
        $rights['addCat'] = $default[40]['VALUE'];
    if ($actual[41] != '') {
        $rights['editCat'] = $actual[41];
    } else
        $rights['editCat'] = $default[41]['VALUE'];
    if ($actual[42] != '') {
        $rights['delCat'] = $actual[42];
    } else
        $rights['delCat'] = $default[42]['VALUE'];
    //artilcle related
    if ($actual[43] != '') {
        $rights['addArt'] = $actual[43];
    } else
        $rights['addArt'] = $default[43]['VALUE'];
    if ($actual[44] != '') {
        $rights['editArt'] = $actual[44];
    } else
        $rights['editArt'] = $default[44]['VALUE'];
    if ($actual[45] != '') {
        $rights['delArt'] = $actual[45];
    } else
        $rights['delArt'] = $default[45]['VALUE'];
    //page related
    if ($actual[46] != '') {
        $rights['addPage'] = $actual[46];
    } else
        $rights['addPage'] = $default[46]['VALUE'];
    if ($actual[47] != '') {
        $rights['editPage'] = $actual[47];
    } else
        $rights['editPage'] = $default[47]['VALUE'];
    if ($actual[48] != '') {
        $rights['delPage'] = $actual[48];
    } else
        $rights['delPage'] = $default[48]['VALUE'];
    return $rights;
}



function seo_rewrite($text)
{
   	 $t = str_replace ("'", '', $text);
     $t = str_replace (',', '', $t);
	 $t = str_replace ('.', '', $t);
	 $t = str_replace ('©', '', $t);
	 $t = str_replace ('¶', '', $t);
	 $t = str_replace ('?', '', $t);
	 $t = str_replace ('§', '', $t);
	 $t = str_replace (':', '', $t);
	 $t = str_replace ('!', '', $t);
     $t = str_replace(')', '', $t);
     $t = str_replace('/', '', $t);
     $t = str_replace('|', '', $t);
     $t = preg_replace ('/\s/', '-', $t);
     $t = str_replace ('__', '-', $t);
     $t = str_replace ('--', '-', $t);
	 $t = str_replace ('---', '-', $t);
	 $t = str_replace ("'", '', $t);
	 $t = str_replace (',', '', $t);

   return $t;
}

function subscribe_to_commentslc($id) {
    global $db, $tables;

    $data['USER_ID'] = $_SESSION['phpld']['user']['id'];
    $data['COMMENT_ID'] = $db->GetOne("SELECT `ID` FROM `{$tables['comment']['name']}` WHERE `ITEM_ID` = " . $db->qstr($id) . " AND `TYPE` = '1' ORDER BY `ID` DESC LIMIT 1 AND `TYPE` = '1'");
    $data['LINK_ID'] = $id;

    $n = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link_subscribe']['name']}` WHERE `USER_ID` = '{$_SESSION['phpld']['user']['id']}' AND `LINK_ID` = " . $db->qstr($id));

    if ($n < 1)
        db_replace('link_subscribe', $data, 'ID');
}

function send_mail_to_subscriberslc($id) {
    global $db, $tables;

    $article = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($id));
    $comment = $db->GetRow("SELECT * FROM `{$tables['comment']['name']}` WHERE `ITEM_ID` = " . $db->qstr($id) . " AND `TYPE` = '1' ORDER BY `ID` DESC LIMIT 1");
    $users = $db->GetAll("SELECT * FROM `{$tables['link_subscribe']['name']}` WHERE `LINK_ID` = " . $db->qstr($id) . " AND `USER_ID` <> " . $db->qstr($comment['USER_ID']));

    foreach ($users as $k => $v) {
        $udata = $db->GetRow("SELECT * FROM `{$tables['user']['name']}` WHERE `ID` = " . $db->qstr($v['USER_ID']));

        $mail = get_emailer();
        $mail->Body = 'You get this mail because you are subscribed for receiving reviews on ' . $link['TITLE']
                . "\n\n"
                . "-----------------------\n"
                . $comment['COMMENT'] . "\n"
                . '-----------------------'
                . "\n\nlink: " . str_replace('//', '/', SITE_URL . "/detail.php?id=") . $id
                . "\n\n"
                . "Regards,\n" . SITE_NAME;

        $mail->Subject = 'New answers on "' . $article['TITLE'] . '"';
        $mail->isHTML = true;

        if (!empty($udata['EMAIL'])) {
            $mail->AddAddress($udata['EMAIL'], $udata['EMAIL']);
            $sent = $mail->Send();
        }
    }
}

function unsubscribelc($id) {
    global $db, $tables;

    $user_id = $_SESSION['phpld']['user']['id'];

    $db->Execute("DELETE FROM `{$tables['link_subscribe']['name']}` WHERE `LINK_ID` = " . $db->qstr($id) . " AND `USER_ID` = " . $db->qstr($user_id) . "");
}

function has_confirmed_email($uid) {
    global $db, $tables;

    $data = $db->GetAll("SELECT * FROM `{$tables['user']['name']}` WHERE `ID`=" . $db->qstr($uid));

    if ($data[0]['EMAIL_CONFIRMED'] == 1) {
        return true;
    } else {
        return false;
    }
}

function email_is_confirmed($email) {
    global $db, $tables;

    //check user table first
    $result = $db->cacheGetAll("SELECT * FROM `{$tables['user']['name']}` WHERE `EMAIL`=" . $db->qstr($email));

    $conf1 = $result[0]['EMAIL_CONFIRMED'];

    //check link table
    $result = $db->cacheGetAll("SELECT * FROM `{$tables['link']['name']}` WHERE `OWNER_EMAIL`=" . $db->qstr($email));

    $conf2 = $result[0]['OWNER_EMAIL_CONFIRMED'];


    if (($conf1 == 1) || ($conf2 == 1)) {
        return true;
    } else {
        return false;
    }
}

function getRSS_feeds($url) {
    require_once '../libs/rss/simplepie.inc';


    $feed = new SimplePie();

    //$feed->strip_ads(true);

    $feed->set_feed_url($url);
    $feed->init();
    $items = $feed->get_items();
    $feeds = array();
    $i = -1;
    foreach ($items as $item) {
        $i++;
        $feeds[$i]['title'] = $item->get_title();
        $feeds[$i]['description'] = $item->get_description();
        $feeds[$i]['url'] = $item->get_permalink();
        $feeds[$i]['date'] = $item->get_date("Y-m-d H:i:s");
    }
    return $feeds;
}

function saveRSS_feeds($categoryId = '') {
    global $db, $tables;
    $b = true;
    if ($categoryId == '') {
        $categories = $db->GetAll("SELECT ID FROM {$tables['category']['name']}");
        for ($i = 0; $i < count($categories); $i++) {
            $id = $categories[$i]['ID'];
            if (!saveCategoryFeeds($id)) {
                $b = false;
            }
        }
    } else {
        return saveCategoryFeeds($categoryId);
    }
    return $b;
}

function saveCategoryFeeds($categoryId) {
    global $db, $tables;
    $b = true;
    $url = $db->GetOne("SELECT RSS_URL
							FROM {$tables['category']['name']}
							WHERE ID=" . $db->qstr($categoryId));
    if ($url == NULL || $url == '') {
        $db->Execute("DELETE FROM {$tables['news']['name']} WHERE CATEGORY_ID=" . $db->qstr($categoryId));
    }
    if ($url != NULL) {
        $db->Execute("DELETE FROM {$tables['news']['name']} WHERE CATEGORY_ID=" . $db->qstr($categoryId));
        $feeds = getRSS_feeds($url);
        for ($i = 0; $i < count($feeds); $i++) {
            $title = $feeds[$i]['title'];
            $description = $feeds[$i]['description'];
            $url = $feeds[$i]['url'];
            $date = $feeds[$i]['date'];

            if (is_null($db->Execute("INSERT
						INTO {$tables['news']['name']}(TITLE,URL,DESCRIPTION,CATEGORY_ID, DATE_ADDED)
						VALUES(" . $db->qstr($title) . "," . $db->qstr($url) . "," . $db->qstr($description) . "," . $db->qstr($categoryId) . ", " . $db->qstr($date) . ")
			"))) {
                $b = false;
            }
        }
    }
    return $b;
}

function curPageURL() {
    $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
    $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
    $port = ($port) ? ':' . $_SERVER["SERVER_PORT"] : '';
    $url = ($isHTTPS ? 'https://' : 'http://') . $_SERVER["SERVER_NAME"] . $port . $_SERVER["REQUEST_URI"];
    return $url;
}

function recacheCategory($id) {
    global $db, $tables;

    $cat['CACHE_TITLE'] = mysql_escape_string(trim(buildCategUrlTitle($id)));
    $cat['CACHE_URL'] = mysql_escape_string(trim(buildCategUrl($id)));

    $sql = $db->Execute("UPDATE " . $tables['category']['name'] . " SET `CACHE_TITLE`=" . $db->qstr($cat['CACHE_TITLE']) . ", 
    `CACHE_URL`=" . $db->qstr($cat['CACHE_URL']) . " WHERE `ID`=" . $db->qstr($id) . "");

    $scats = get_sub_categories($id);

    for ($i = 0; $i < count($scats); $i++) {
        recacheCategory($scats[$i]);
    }
}

function makeUrlAlias($url, $length = 150) {
    $url = (string) $url;
    if (empty($url)) {
        //nothing to do
        return '';
    }

    $length = (int) $length;
    $length = ($length > 0 ? $length : 1);

    //Define word separator
    $separator = '-';

    //Remove white-space chars
    $search = array(
        "\r\n", //Windows
        "\n", //*NIX
        "\r", //Mac
        "\t", //Tab
        "\x0B", //Vertical Tab
        "\0" //NULL BYTE
    );
    $url = str_replace($search, $separator, $url);

   //List of special chars and their replacements
   $replace = array (

      '&' => 'and' ,
      '\'' => '' ,
      'À' => 'A' ,
      'À' => 'A' ,
      'Á' => 'A' ,
      'Â' => 'A' ,
      'Ã' => 'A' ,
      'Ä' => 'AE' ,
      'Å' => 'A' ,
      'Å' => 'A' ,
      'Æ' => 'AE' ,
      'Ā' => 'A' ,
      'Ą' => 'A' ,
      'Ă' => 'A' ,
      'Ç' => 'C' ,
      'Ć' => 'C' ,
      'Č' => 'C' ,
      'Ĉ' => 'C' ,
      'Ċ' => 'C' ,
      'Ď' => 'D' ,
      'Đ' => 'D' ,
      'È' => 'E' ,
      'É' => 'E' ,
      'Ê' => 'E' ,
      'Ë' => 'E' ,
      'Ē' => 'E' ,
      'Ę' => 'E' ,
      'Ĕ' => 'E' ,
      'Ė' => 'E' ,
      'Ĝ' => 'G' ,
      'Ğ' => 'G' ,
      'Ġ' => 'G' ,
      'Ģ' => 'G' ,
      'Ĥ' => 'H' ,
      'Ħ' => 'H' ,
      'Ì' => 'I' ,
      'Í' => 'I' ,
      'Î' => 'I' ,
      'Î' => 'I' ,
      'Ï' => 'I' ,
      'Ī' => 'I' ,
      'Ĩ' => 'I' ,
      'Ĭ' => 'I' ,
      'Į' => 'I' ,
      'İ' => 'I' ,
      'Ĳ' => 'J' ,
      'Ĵ' => 'J' ,
      'Ķ' => 'K' ,
      'Ľ' => 'K' ,
      'Ĺ' => 'K' ,
      'Ļ' => 'K' ,
      'Ŀ' => 'K' ,
      'Ñ' => 'N' ,
      'Ń' => 'N' ,
      'Ň' => 'N' ,
      'Ņ' => 'N' ,
      'Ŋ' => 'N' ,
      'Ò' => 'O' ,
      'Ó' => 'O' ,
      'Ô' => 'O' ,
      'Õ' => 'O' ,
      'Ö' => 'OE' ,
      'Ø' => 'O' ,
      'Ō' => 'O' ,
      'Ő' => 'O' ,
      'Ŏ' => 'O' ,
      'Œ' => 'OE' ,
      'Ŕ' => 'R' ,
      'Ř' => 'R' ,
      'Ŗ' => 'R' ,
      'Ś' => 'S' ,
      'Ş' => 'S' ,
      'Ŝ' => 'S' ,
      'Ș' => 'S' ,
      'Ť' => 'T' ,
      'Ţ' => 'T' ,
      'Ŧ' => 'T' ,
      'Ț' => 'T' ,
      'Ù' => 'U' ,
      'Ù' => 'U' ,
      'Ú' => 'U' ,
      'Ú' => 'U' ,
      'Û' => 'U' ,
      'Ü' => 'UE' ,
      'Ū' => 'U' ,
      'Ů' => 'U' ,
      'Ű' => 'U' ,
      'Ŭ' => 'U' ,
      'Ũ' => 'U' ,
      'Ų' => 'U' ,
      'Ŵ' => 'W' ,
      'Ŷ' => 'Y' ,
      'Ÿ' => 'Y' ,
      'Ź' => 'Z' ,
      'Ż' => 'Z' ,
      'à' => 'a' ,
      'á' => 'a' ,
      'â' => 'a' ,
      'ã' => 'a' ,
      'ä' => 'ae' ,
      'å' => 'a' ,
      'ā' => 'a' ,
      'ą' => 'a' ,
      'ă' => 'a' ,
      'å' => 'a' ,
      'æ' => 'ae' ,
      'ç' => 'c' ,
      'ć' => 'c' ,
      'č' => 'c' ,
      'ĉ' => 'c' ,
      'ċ' => 'c' ,
      'ď' => 'd' ,
      'đ' => 'd' ,
      'è' => 'e' ,
      'é' => 'e' ,
      'ê' => 'e' ,
      'ë' => 'e' ,
      'ē' => 'e' ,
      'ę' => 'e' ,
      'ĕ' => 'e' ,
      'ė' => 'e' ,
      'ƒ' => 'f' ,
      'ĝ' => 'g' ,
      'ğ' => 'g' ,
      'ġ' => 'g' ,
      'ģ' => 'g' ,
      'ĥ' => 'h' ,
      'ħ' => 'h' ,
      'ì' => 'i' ,
      'í' => 'i' ,
      'î' => 'i' ,
      'ï' => 'i' ,
      'ī' => 'i' ,
      'ĩ' => 'i' ,
      'ĭ' => 'i' ,
      'į' => 'i' ,
      'ı' => 'i' ,
      'ĳ' => 'j' ,
      'ĵ' => 'j' ,
      'ķ' => 'k' ,
      'ĸ' => 'k' ,
      'ł' => 'l' ,
      'ľ' => 'l' ,
      'ĺ' => 'l' ,
      'ļ' => 'l' ,
      'ŀ' => 'l' ,
      'ñ' => 'n' ,
      'ń' => 'n' ,
      'ň' => 'n' ,
      'ņ' => 'n' ,
      'ŉ' => 'n' ,
      'ŋ' => 'n' ,
      'ò' => 'o' ,
      'ó' => 'o' ,
      'ô' => 'o' ,
      'õ' => 'o' ,
      'ö' => 'oe' ,
      'ø' => 'o' ,
      'ō' => 'o' ,
      'ő' => 'o' ,
      'ŏ' => 'o' ,
      'œ' => 'oe' ,
      'ŕ' => 'r' ,
      'ř' => 'r' ,
      'ŗ' => 'r' ,
      'ù' => 'u' ,
      'ú' => 'u' ,
      'û' => 'u' ,
      'ü' => 'ue' ,
      'ū' => 'u' ,
      'ů' => 'u' ,
      'ű' => 'u' ,
      'ŭ' => 'u' ,
      'ũ' => 'u' ,
      'ų' => 'u' ,
      'ŵ' => 'w' ,
      'ÿ' => 'y' ,
      'ŷ' => 'y' ,
      'ż' => 'z' ,
      'ź' => 'z' ,
      'ß' => 'ss' ,
      'ſ' => 's' ,
      'Α' => 'A' ,
      'Ά' => 'A' ,
      'Β' => 'B' ,
      'Γ' => 'G' ,
      'Δ' => 'D' ,
      'Ε' => 'E' ,
      'Έ' => 'E' ,
      'Ζ' => 'Z' ,
      'Η' => 'I' ,
      'Ή' => 'I' ,
      'Θ' => 'TH' ,
      'Ι' => 'I' ,
      'Ί' => 'I' ,
      'Ϊ' => 'I' ,
      'Κ' => 'K' ,
      'Λ' => 'L' ,
      'Μ' => 'M' ,
      'Ν' => 'N' ,
      'Ξ' => 'KS' ,
      'Ο' => 'O' ,
      'Ό' => 'O' ,
      'Π' => 'P' ,
      'Ρ' => 'R' ,
      'Σ' => 'S' ,
      'Τ' => 'T' ,
      'Υ' => 'Y' ,
      'Ύ' => 'Y' ,
      'Ϋ' => 'Y' ,
      'Φ' => 'F' ,
      'Χ' => 'X' ,
      'Ψ' => 'PS' ,
      'Ω' => 'O' ,
      'Ώ' => 'O' ,
      'α' => 'a' ,
      'ά' => 'a' ,
      'β' => 'b' ,
      'γ' => 'g' ,
      'δ' => 'd' ,
      'ε' => 'e' ,
      'έ' => 'e' ,
      'ζ' => 'z' ,
      'η' => 'i' ,
      'ή' => 'i' ,
      'θ' => 'th' ,
      'ι' => 'i' ,
      'ί' => 'i' ,
      'ϊ' => 'i' ,
      'ΐ' => 'i' ,
      'κ' => 'k' ,
      'λ' => 'l' ,
      'μ' => 'm' ,
      'ν' => 'n' ,
      'ξ' => 'ks' ,
      'ο' => 'o' ,
      'ό' => 'o' ,
      'π' => 'p' ,
      'ρ' => 'r' ,
      'σ' => 's' ,
      'τ' => 't' ,
      'υ' => 'y' ,
      'ύ' => 'y' ,
      'ϋ' => 'y' ,
      'ΰ' => 'y' ,
      'φ' => 'f' ,
      'χ' => 'x' ,
      'ψ' => 'ps' ,
      'ω' => 'o' ,
      'ώ' => 'o'
   );
   $url = strtr ($url, $replace);


    //Remove PHP and HTML tags
    $url = strip_tags($url);

    //Remove chars except letters, numbers
//   $url = preg_replace ('#[^A-Za-z0-9]#', $separator, $url);
    //Remove multiple occurences of defined separator
    $url = preg_replace('#[\\' . $separator . ']+#', $separator, $url);

    //Make lowercase
    //$url = mb_strtolower ($url);
    //does not work for < php 4.3.0, replaced with strtolower as below: 

    $url = strtolower($url);

    $url = trim($url);

    //Keep only max length
    $url = mb_substr($url, 0, $length);

    //Trim replacement chars from start & end
    $search = array(
        '#^[^A-Za-z0-9]*#',
        '#[^A-Za-z0-9]*$#'
    );
    //$url = preg_replace ($search, '', $url);
      //James language url edit
$url = strtolower_utf8($url);
 
   return (string) $url;
}

function strtolower_utf8($string){ 
  $convert_to = array( 
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", 
    "v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", 
    "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж", 
    "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы", 
    "ь", "э", "ю", "я" 
  ); 
  $convert_from = array( 
    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", 
    "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", 
    "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", 
    "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ъ", 
    "Ь", "Э", "Ю", "Я" 
  ); 

  return str_replace($convert_from, $convert_to, $string); 
} 

   //James language url edit added function

function get_widgets() {
    global $db, $tables;
    $w = array();
    $wid = array();
    $i = 0;
    if (is_dir(INSTALL_PATH . "/application/widgets/")) {
        if ($dh = opendir(INSTALL_PATH . "/application/widgets/")) {
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != ".." && file_exists(INSTALL_PATH . "/application/widgets/" . $file . "/config.xml")) {
                    $w[$i] = $file;
                    $i++;
                }
            }
            closedir($dh);
        }
        asort($w);
//	    $i = 0;
        foreach ($w as $k => $v) {
            $xml = get_widget_xml($v);
            $ind = count($wid[$xml['ZONETYPE']]);
            $wid[$xml['ZONETYPE']][$ind] = $xml;
            $wid[$xml['ZONETYPE']][$ind]['NAME'] = $v;
            $wids = $db->GetAll("SELECT * FROM `{$tables['widget']['name']}` WHERE `NAME` = " . $db->qstr($wid[$xml['ZONETYPE']][$ind]['NAME']));
            if (count($wids) > 0) {
                $wid[$xml['ZONETYPE']][$ind]['INSTALLED'] = 1;
            } else {
                $wid[$xml['ZONETYPE']][$ind]['INSTALLED'] = 0;
            }
//	        $i++;
        }

        return $wid;
    }
}

function get_mobile_widgets($zone = 'ANY_OTHER_PAGE',$xml) {
    global $db, $tables;
    $widget_list = array();
    
    array_push($widget_list,'MainContent');
    if($xml->mainmenu->attributes()->default == '1')
	array_push($widget_list,'MenuMobile');
    
    if($zone == 'HOMEPAGE'){
	if($xml->categories->attributes()->default == '1')
	    array_push($widget_list,'TopCategoriesMobile');
	if($xml->latestlinks->attributes()->default == '1')
	    array_push($widget_list,'LatestListings');
	
    }
    elseif($zone == 'CATEGORY_PAGE'){
	array_push($widget_list,'CategorySubcategoriesMobile');
	array_push($widget_list,'CategoryListings');
	
    }
   
    foreach($widget_list as $k => $v)
    {
	if (class_exists('Widget_' . $v)) {
                    $classname = 'Widget_' . $v;
                    $w = new $classname($v, $k, '', '');
		    $wid[$zone][$k]['NAME'] = $v;
                    $wid[$zone][$k]['CONTENT'] = $w->render();
                    $wid[$zone][$k]['SETTINGS'] = $w->getFrontSettings();
                }
    }
    
    return $wid;
 }

function get_active_widgets() {
    global $db, $tables;

    $zones = get_widget_front_zones();

    for ($i = 0; $i < count($zones); $i++) {
        $wid[$zones[$i]['NAME']] = $db->GetAll("SELECT * FROM `{$tables['widget_activated']['name']}` WHERE `ZONE` = " . $db->qstr($zones[$i]['NAME']) . " AND ACTIVE = 1 ORDER BY `ORDER_ID` ASC");
        foreach ($wid[$zones[$i]['NAME']] as $k => $v) {
            if ($v['NAME'] != '') {
                if (class_exists('Widget_' . $v['NAME'])) {
                    $classname = 'Widget_' . $v['NAME'];
                    $w = new $classname($v['NAME'], $v['ID'], '', '');
                    $wid[$zones[$i]['NAME']][$k]['CONTENT'] = $w->render();
                    $wid[$zones[$i]['NAME']][$k]['SETTINGS'] = $w->getFrontSettings();
                }
            }
        }
    }
    return $wid;
}

function pick_widget_zones($name, $widget) {
    global $db, $tables;
    $widget = Phpld_Widget::factory(array('NAME'=>$name, 'ID'=>''));
    $widgetConfig = $widget->getConfig();
    $zones = $db->GetAll("SELECT * FROM `{$tables['widget_zones']['name']}` WHERE `TYPE` =" . $db->qstr($widgetConfig['ZONETYPE'])." OR TYPE = 'CUSTOM' ");
    for ($i = 0; $i < count($zones); $i++) {
        $wid = $db->GetAll("SELECT * FROM `{$tables['widget_activated']['name']}` WHERE `ZONE` = " . $db->qstr($zones[$i]['NAME']) . " AND `NAME`=" . $db->qstr($name));
        if (count($wid) > 0) {
            $zones[$i]['ACTIVE'] = 1;
        }
    }

    $widgetConfig = $widget->getConfig();
    if (isset($widgetConfig['ALLOWED_ZONES'])) {
       $allowedZones = explode(',',$widgetConfig['ALLOWED_ZONES']);
 array_push($allowedZones,'CUSTOM');

        $zonesFiltered = array();
        foreach ($zones as $i=>$zone) {
            if (in_array($zone['NAME'], $allowedZones)) {
                $zonesFiltered[] = $zone;
            }
        }
        $zones = $zonesFiltered;
    }

    return $zones;
}

function get_widget_zones() {
    global $db, $tables;
    $w = $db->GetAll("SELECT * FROM `{$tables['widget_zones']['name']}`");

    $wid = array();
    for ($i = 0; $i < count($w); $i++) {
        $wid[$w[$i]['TYPE']][] = $w[$i];
    }

    return $wid;
}

function get_widget_front_zones() {
    global $db, $tables;
    $controller = Phpld_App::getInstance()->getRouter()->getController();
    $action = Phpld_App::getInstance()->getRouter()->getAction();
    $query = "SELECT * FROM `{$tables['widget_zones']['name']}`
    WHERE (CONTROLLER = " . $db->qstr($controller) . " OR CONTROLLER IS NULL) AND
    (ACTION = " . $db->qstr($action) . " OR ACTION IS NULL) ORDER BY ACTION DESC";
    // ORDER BY ACTION is here to make rows with "action" set to be on top
    $w = $db->GetAll($query);
    $centralSet = false;
    foreach ($w as $key=>$zone) {
        if ($centralSet == true && $zone['TYPE'] == 'CENTRAL') {
            unset($w[$key]);
        } elseif ($zone['TYPE'] == 'CENTRAL') {
            $centralSet = true;
        }
    }
    $w = array_values($w);
//    var_dump($w);die();
    return $w;
}

function get_widgets_per_zone($zone, $type) {
    global $db, $tables;
    $wid = $db->GetAll("SELECT *, IFNULL(ACTIVE, 0) as ACTIVE, `ZONE`, `ID`, IFNULL(`ORDER_ID`, 1000) AS `ORDER_ID`
						FROM `{$tables['widget_activated']['name']}`
						WHERE ZONE = " . $db->qstr($zone) . "
						ORDER BY `ORDER_ID` ASC
						");
    return $wid;
}

function get_widget_xml($name) {
    $wid = array();
    if (($xml = file_get_contents(INSTALL_PATH . "/application/widgets/" . $name . "/config.xml")) !== false) {
        $p = xml_parser_create();
        xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        foreach ($index as $k => $v) {
            if (strtoupper($k) != 'NAME' && strtoupper($k) != 'VALUE' && strtoupper($k) != 'INFO' && strtoupper($k) != 'IDENTIFIER' && strtoupper($k) != 'ALLOWED') {
                $wid[strtoupper($k)] = $vals[$v[0]]['value'];
            } else {
                for ($i = 0; $i < count($index[$k]); $i++) {
                    $wid['SETTINGS'][$i][strtoupper($k)] = $vals[$index[$k][$i]]['value'];
                }
            }
        }
    }

    return $wid;
}

function isFieldExists($field_name) {
    global $db, $tables;
    $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['submit_item']['name']}` WHERE `FIELD_NAME` = '{$_REQUEST['field']}'");
    $result = ($count > 0) ? true : false;
    return $result;
}

function isUniqueValue($table, $field_name, $exclude_id = '') {
    global $db, $tables, $validator;
    $exclude_id = intval($_REQUEST['id']);
    $exclude_parent_id = intval($_REQUEST['parent_id']);
    $exclude_sql = (!empty($exclude_id)) ? " AND `ID` != '{$exclude_id}'" : '';
    $exclude_sql .= (!empty($exclude_parent_id)) ? " AND `PARENT_ID` != '{$exclude_parent_id}'" : '';
   
    $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables[$table]['name']}` WHERE `{$field_name}` = '{$_REQUEST[$field_name]}' {$exclude_sql}");
    $result = ($count > 0) ? false : true;
     if($result == false && !empty($validator)){ 
	$validator -> extendMessages(array($field_name => array('remote' => 'This '.$field_name.' already exists in our database and cannot be submitted again.')));
    }
    return $result;
}

function isUniqueRegistration($table, $field_name) {
    global $db, $tables;
    if (isset($_SESSION['phpld']['user']['id']))
        $exclude_id = intval($_SESSION['phpld']['user']['id']);

    $exclude_sql = (!empty($exclude_id)) ? " AND `ID` != '{$exclude_id}'" : '';

    if (isset($_REQUEST['exclude_id']) && !empty($_REQUEST['exclude_id']))
        $exclude_id = intval($_REQUEST['exclude_id']);

    $exclude_sql .= (!empty($exclude_id)) ? " AND `ID` != '{$exclude_id}'" : '';


    $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables[$table]['name']}` WHERE `{$field_name}` = '{$_REQUEST[$field_name]}' {$exclude_sql}");
    $result = ($count > 0) ? false : true;
    return $result;
}

function isUniquePermission($user_id, $category_id) {
    global $db, $tables;
    $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = '{$user_id}' AND `CATEGORY_ID` = '{$category_id}'");
    $result = ($count > 0) ? false : true;
    return $result;
}

function isNotSubCat($user_id, $category_id) {
    global $db, $tables;

    $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($category_id);
    $category = $db->GetOne($sql);

    if ($category != 0) {
        $count_sql = "SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = " . $db->qstr($user_id) . " AND (`CATEGORY_ID` = " . $db->qstr($category);

        while ($category != 0) {
            $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($category);
            $category = $db->GetOne($sql);
            if ($category != 0)
                $count_sql .= " OR `CATEGORY_ID` = " . $db->qstr($category);
        }
        $count_sql .= ")";
        $c = $db->GetOne($count_sql);
    }
    else
        $c = 0;

    return ($c == 0 ? true : false);
}

function isUniqueUrl($field_name, $categ_id) {
    global $db, $tables;
    $add_sql = " AND `CATEGORY_ID` = '{$categ_id}'";
    $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `{$field_name}` = '{$_REQUEST[$field_name]}' {$add_sql}");
    $result = ($count > 0) ? false : true;
    return $result;
}

function isUniqueUrlSite($field_name, $table) {
    global $db, $tables;
    $id = $_REQUEST['id'];
    if ($id != '') {
        $count = $db->GetOne("SELECT COUNT(*) FROM `" . $tables[$table]['name'] . "` WHERE `{$field_name}` = '{$_REQUEST[$field_name]}' AND `ID` != " . $db->qstr($id));
    } else {
        $count = $db->GetOne("SELECT COUNT(*) FROM `" . $tables[$table]['name'] . "` WHERE `{$field_name}` = '{$_REQUEST[$field_name]}' {$add_sql}");
    }
    $result = ($count > 0) ? false : true;
    return $result;
}

function isUniqueUrlDomain($field_name, $table) {
    global $db, $tables;
    $id = $_REQUEST['id'];
    $dd = parseDomain($_REQUEST[$field_name]);

    $count = $db->GetOne("SELECT COUNT(*) FROM `" . $tables[$table]['name'] . "` WHERE `{$field_name}` like '%" . $dd . "%'");

    $result = ($count > 0) ? false : true;
    return $result;
}

function isURLOnline($field_name) {
    $http = array('http://', 'https://');
    $url = str_replace($http, "", $_REQUEST[$field_name]);
    $url = "http://" . $url;
    $res = get_url($url, URL_HEADERS);
    $result = $res['status'] ? true : false;
    return $result;
}

function isRecprOnline($field_name) {
    if (empty($_REQUEST[$field_name])) {
        return 1;
    }
    $data["URL"] = $_REQUEST['URL'];
    $data["RECPR_URL"] = $_REQUEST[$field_name];
    $res = check_recpr_link($data);
    $result = ($res == 1) ? true : false;
    return $result;
}

function isBannedEmail($field_name) {
    $res = is_banned_email($_REQUEST[$field_name]);
    $result = ($res == 0) ? true : false;
    return $result;
}

function isDomainBanned($field_name) {
    $res = is_banned_domain($_REQUEST[$field_name]);
    $result = ($res == 0) ? true : false;
    return $result;
}

function isUsername($value) {
    $check = preg_replace('([a-zA-Z0-9_]+)', "", $value);
    if (empty($check)) {
        return '1';
    } else {
        return '0';
    }
}

function isInt($value) {
    if (strlen($value) == 0)
        return false;

    return preg_match('!^\d+$!', $value);
}

function isSymbolicUnique($id, $symbolic_id, $parent_id) {
    global $tables, $db;
    $sql = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `SYMBOLIC_ID` = " . $db->qstr($symbolic_id) . " AND `PARENT_ID` = " . $db->qstr($parent_id);
    if ($id > 0)
        $sql .= " AND ID <> " . $id;

    $c = $db->GetOne($sql);

    return ($c == 0 ? true : false);
}

function isSymbolicParentValid($id, $parent_id) {
    global $tables, $db;
    $sql = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($id) . " AND `PARENT_ID` = " . $db->qstr($parent_id);
    $c = $db->GetOne($sql);

    return ($c == 0 ? true : false);
}

function isCaptchaValid() {
    global $db, $tables;
    if (!empty($_REQUEST['IMAGEHASH']) && !empty($_REQUEST['CAPTCHA'])) {
        $result = $db->GetOne("SELECT `CREATED` FROM `{$tables['img_verification']['name']}` WHERE `IMGHASH` = '{$_REQUEST['IMAGEHASH']}' AND `IMGPHRASE` = '{$_REQUEST['CAPTCHA']}'");
        $result = $result ? true : false;
    } else {
        $result = '0';
    }
    return $result;
}

function json_custom_encode($res) {
    $result = '';
    if (is_array($res)) {
        $rules_encoded = isset($res['rules'])?json_encode_part($res['rules']):'';;
        $msgs_encoded = isset($res['messages'])?json_encode_part($res['messages']):'';
        $result = '"rules": {' . $rules_encoded . '}, "messages": {' . $msgs_encoded . '}';
        $result = str_replace('"function() { return $(\'#URL\').val(); }"', "function() { return $('#URL').val(); }", $result);
    }
    return $result;
}

function json_encode_part($res) {
    $result = '';
    if (is_array($res))
        foreach ($res as $key => $value) {
            $result .= $key . ': {';
            foreach ($value as $item_key => $item_value) {
                if (($item_key == 'remote') && isset($item_value[0]) && is_array($item_value[0])) {
                    foreach ($item_value as $remote) {
                        $piece = json_encode(array('remote' => $remote));
                        $result .= substr($piece, 1, -1) . ',';
                    }
                } else {
                    $piece = json_encode(array($item_key => $item_value));
                    $result .= substr($piece, 1, -1) . ',';
                }
            }
            $result = substr($result, 0, -1);
            $result .= '},';
        }
    $result = substr($result, 0, -1);
    return $result;
}

function search($search_preferences) {
    global $tables, $db;
    // Initialize search options array
    if (!is_array($search_preferences) || empty($search_preferences))
        $search_preferences = array();

    // Determine fields to search in
    if (!is_array($search_preferences['Select_Options']) || empty($search_preferences['Select_Options']))
        $search_preferences['Select_Options'] = array('*');

    // Determine query options */
    if (!is_array($search_preferences['Where_Options']) || empty($search_preferences['Where_Options']))
        $search_preferences['Where_Options'] = array("`STATUS` = 2", '`ID` > 1');

    // Determine search results order
    if (!is_array($search_preferences['Order_Options']) || empty($search_preferences['Order_Options']))
        $search_preferences['Order_Options'] = array('`ID` ASC');

    // Determine search location
    if (!is_array($search_preferences['Order_Options']) || empty($search_preferences['Order_Options']))
        $search_preferences['Search_Location'] = array('TITLE', 'URL', 'DESCRIPTION');

    // Determine table to search in */
    $search_preferences['From_Table'] = (!empty($search_preferences['From_Table']) ? trim($search_preferences['From_Table']) : $tables['link']['name']);
    if (strpos(strtoupper($search_preferences['From_Table']), 'FROM') !== 0)
        $search_preferences['From_Table'] = ' FROM `' . $search_preferences['From_Table'] . '`';

    // Determine the minimum number of letters for a fulltext search
    //this i commented since i want to search short ids as well
    $Request_MinWordLength = $db->GetAssoc("SHOW VARIABLES LIKE 'ft_min_word_len'");
    //since i'm now searching for id as well, min_word_length more than 4 is not ok anymore. i'm setting it to 1 now
    //$search_preferences['Min_Word_Length'] = ($Request_MinWordLength != false ? $Request_MinWordLength['ft_min_word_len'] : 4);

    $search_preferences['Min_Word_Length'] = ($Request_MinWordLength != false ? $Request_MinWordLength['ft_min_word_len'] : 1);
    unset($Request_MinWordLength);

    // Check if the boolean search mode is suported by MySQL version
    $search_preferences['BooleanSearchPossible'] = version_compare(mysql_get_server_info(), '4.0.1', '>=') == 1;

    $search_preferences['BooleanSearchActive'] = (isset($search_preferences['BooleanSearchActive']) ? $search_preferences['BooleanSearchActive'] : BOOLEAN_SEARCH_ACTIVE);

    // Populate array with words that are removed by default
    $search_preferences['Black_List'] = array('img', 'url', 'quote', 'www', 'http', 'https', 'ftp', 'the', 'is', 'it', 'are', 'if');

    if (empty($search_preferences['search'])) { // Check search words
        if (!empty($_GET['search']))
            $search_preferences['search'] = clean_search_query($_GET['search']);
        elseif (!empty($_POST['search']))
            $search_preferences['search'] = clean_search_query($_POST['search']);
        elseif (!empty($_REQUEST['search']))
            $search_preferences['search'] = clean_search_query($_REQUEST['search']);
        else
            $search_preferences['search'] = '';
    }

    if (empty($search_preferences['searchajax'])) { // Check search words
        if (!empty($_GET['searchajax']))
            $search_preferences['searchajax'] = clean_search_query($_GET['searchajax']);
        elseif (!empty($_POST['search']))
            $search_preferences['searchajax'] = clean_search_query($_POST['searchajax']);
        elseif (!empty($_REQUEST['search']))
            $search_preferences['searchajax'] = clean_search_query($_REQUEST['searchajax']);
        else
            $search_preferences['searchajax'] = '';
    }

    // Check again for search words
    if (empty($search_preferences['search']))
        $search_preferences['errors']['empty_search'] = 1;
    else
        $search_preferences['errors']['empty_search'] = 0;

    // Extract phrase(s) that needs exact matching
    preg_match_all('/(?:^|\s)([+-]?)"([^"]+)"(?:$|\s)/', $search_preferences['search'], $matches, PREG_PATTERN_ORDER);

    // Dummy arrays to store search values
    $Search_Array = $matches[2];
    $Exclude_Words = array();

    // Remove the exact matching phrase(s) and extract the words
    $Keyword_Array = explode(' ', preg_replace('/(?:^|\s)([+-]?)"([^"]+)"(?:$|\s)/', ' ', $search_preferences['search']));

    // Initialize two arrays to store keywords to search for and keywords that need to be excluded
    $search_preferences['Exclude_Words'] = array();
    $search_preferences['Search_Words'] = array();

    // Check for -"common phrase"
    foreach ($matches[1] as $key => $phrase)
        if ($phrase == '-') {
            $phrase = strtolower(trim($Search_Array[$key]));
            if (!empty($phrase) && !in_array($phrase, $search_preferences['Black_List']))
                $Exclude_Words[] = $phrase;
            unset($Search_Array[$key]);
        }

    // Check for -keyword
    foreach ($Keyword_Array as $key => $word) {
        $word = trim($word);
        if (strpos($word, '-') === 0) {
            $word = preg_replace('`([-+"])`', '', $word);
            $word = trim($word);
            if (!empty($word) && !in_array($word, $search_preferences['Black_List']))
                $Exclude_Words[] = $word;
            unset($Keyword_Array[$key]);
        }
    }

    $Search_Array = array_merge($Search_Array, $Keyword_Array);

    unset($Keyword_Array); // Free some memory
    // Clean excluded keywords, remove duplicates and of shorter length than allowed
    foreach ($Exclude_Words as $key => $value) {
        $Exclude_Words[$key] = strtolower(trim($value)); // Remove unneded chars
        $Keyword_Array = explode(' ', $Exclude_Words[$key]);

        foreach ($Keyword_Array as $KeywKey => $keyword) {
            $keyword = trim($keyword);
            // Check current keyword
            if (strlen($keyword) >= $search_preferences['Min_Word_Length'] && !in_array($keyword, $search_preferences['Black_List']))
                $search_preferences['Exclude_Words'][] = $keyword; // Add keyword
            unset($Keyword_Array[$KeywKey]); // Free some memory
        }
        unset($Exclude_Words[$key]); // Free some memory
    }
    $search_preferences['Exclude_Words'] = array_slice(array_unique($search_preferences['Exclude_Words']), 0, 20);
    unset($Exclude_Words, $Keyword_Array); // Free some memory
    // Clean keywords, remove duplicates and of shorter length than allowed
    foreach ($Search_Array as $key => $value) {
        $Search_Array[$key] = strtolower(trim($value)); // Remove unneded chars
        $Keyword_Array = explode(' ', $Search_Array[$key]);

        foreach ($Keyword_Array as $KeywKey => $keyword) {
            $keyword = trim($keyword);
            // Check current keyword
            if (strlen($keyword) >= $search_preferences['Min_Word_Length'] && !in_array($keyword, $search_preferences['Black_List']))
                $search_preferences['Search_Words'][] = $keyword; // Add keyword
            unset($Keyword_Array[$KeywKey]);
        }
        unset($Search_Array[$key]); // Free some memory
    }
    $search_preferences['Search_Words'] = array_slice(array_unique($search_preferences['Search_Words']), 0, 20);
    unset($Search_Array, $Keyword_Array); // Free some memory

    $search_preferences['Relevancy_Order'] = '';
    $search_preferences['Relevancy_Tuning'] = '';
    $search_preferences['Select_Relevancy'] = '';
    $search_preferences['Search_Query'] = '';
    $Count_All_Keywords = count($search_preferences['Search_Words']) + count($search_preferences['Exclude_Words']);
    if ($Count_All_Keywords > 0)
        if ($search_preferences['BooleanSearchPossible'] == 1 && $search_preferences['BooleanSearchActive'] == 1) { // Build boolean search query
            // Build search location
            $location = "";
            foreach ($search_preferences['Search_Location'] as $ThisLocation) {
                $ThisLocation = trim($ThisLocation);
                if (strpos($ThisLocation, '.') === false)
                    $ThisLocation = (!empty($ThisLocation) ? '`' . $ThisLocation . '`' : '');
                $location .= (!empty($ThisLocation) ? $ThisLocation . ', ' : '');
            }

            if (!empty($search_preferences['Search_Location_Link_Type']) && $search_preferences['From_Table'] == ' FROM `PLD_LINK`') {
                $search_preferences['Search_Query'] .= "(";
                foreach ($search_preferences['Search_Location_Link_Type'] as $k => $v) {
                    $search_preferences['Search_Query'] .= "(MATCH ( ";
                    $search_preferences['Search_Query_r'] = '';
                    foreach ($v as $ThisLocation) {
                        $ThisLocation = trim($ThisLocation);
                        if (strpos($ThisLocation, '.') === false)
                            $ThisLocation = (!empty($ThisLocation) ? '`' . $ThisLocation . '`' : '');
                        $search_preferences['Search_Query'] .= $ThisLocation . ",";
                        $search_preferences['Search_Query_r'] .= $ThisLocation . ",";
                    }

                    $search_preferences['Search_Query'] = rtrim($search_preferences['Search_Query'], ",");
                    $search_preferences['Search_Query_r'] = rtrim($search_preferences['Search_Query_r'], ",");
                    $search_preferences['Search_Query'] .= ") AGAINST ('" . $search_preferences['search'] . "'
                                                        IN BOOLEAN MODE) ";
                    $search_preferences['Select_Relevancy'] .= "MATCH (" . $search_preferences['Search_Query_r'] . ") AGAINST ('" . $search_preferences['search'] . "'
                                                        IN BOOLEAN MODE) AS `relevancy_" . $k . "`,";
                    $search_preferences['Search_Query'] .= " AND `LINK_TYPE` = " . $db->qstr($k) . ") OR ";
                    $search_preferences['Relevancy_Order'] .= "`relevancy_" . $k . "` DESC,";
                }
                $search_preferences['Select_Relevancy'] = rtrim($search_preferences['Select_Relevancy'], ",");
                $search_preferences['Select_Options'][] = $search_preferences['Select_Relevancy'];
                $search_preferences['Relevancy_Order'] = rtrim($search_preferences['Relevancy_Order'], ",");
                $search_preferences['Search_Query'] .= '0) ';
            } else {

                $location = preg_replace('`,[\s]*$`', '', $location); // Remove trailing comma and space
                $search_preferences['Search_Query'] = "MATCH (" . $location . ") AGAINST ('";

                $search_preferences['Search_Query'] .= $search_preferences['search'];
                $search_preferences['Search_Query'] .= $search_preferences['searchajax'];

                $search_preferences['Search_Query'] = trim($search_preferences['Search_Query']);
                $search_preferences['Search_Query'] .= "' IN BOOLEAN MODE)";
                $search_preferences['Select_Relevancy'] = $search_preferences['Search_Query'] . ' AS `relevancy`';
                $search_preferences['Select_Options'][] = $search_preferences['Select_Relevancy'];
                $search_preferences['Relevancy_Order'] = '`relevancy` DESC';
                $search_preferences['Relevancy_Tuning'] = ' HAVING `relevancy` > 0.2 ';
            }
        } else { // Build regular search query
            $search_preferences['Search_Query'] = '';

            // Build query for needed keywords
            if (count($search_preferences['Search_Words']) > 0) {
                $search_preferences['Search_Query'] .= "(";
                foreach ($search_preferences['Search_Words'] as $word)
                    foreach ($search_preferences['Search_Location'] as $ThisLocation) {
                        $ThisLocation = trim($ThisLocation);
                        if (strpos($ThisLocation, '.') === false)
                            $ThisLocation = (!empty($ThisLocation) ? '`' . $ThisLocation . '`' : '');
                        $search_preferences['Search_Query'] .= $ThisLocation . " LIKE " . $db->qstr('%' . $word . '%') . " OR ";
                    }
                if (!empty($search_preferences['Search_Location_Link_Type']) && $search_preferences['From_Table'] == ' FROM `PLD_LINK`') {
                    foreach ($search_preferences['Search_Location_Link_Type'] as $k => $v) {
                        $search_preferences['Search_Query'] .= "((";

                        foreach ($v as $ThisLocation) {
                            $ThisLocation = trim($ThisLocation);
                            if (strpos($ThisLocation, '.') === false)
                                $ThisLocation = (!empty($ThisLocation) ? '`' . $ThisLocation . '`' : '');
                            $search_preferences['Search_Query'] .= $ThisLocation . " LIKE " . $db->qstr('%' . $word . '%') . " OR ";
                        }

                        $search_preferences['Search_Query'] .= '0';
                        $search_preferences['Search_Query'] .= ") AND `LINK_TYPE` = " . $db->qstr($k) . ") OR ";
                    }
                }

                $search_preferences['Search_Query'] .= '0';

                /* Remove trailing AND/OR and space */
                $search_preferences['Search_Query'] = preg_replace('`[\s]+(AND|OR){1,1}[\s]*$`', '', $search_preferences['Search_Query']);
                $search_preferences['Search_Query'] .= ') '; // Close search for needed words
            }

            // Build query for excluded keywords
            if (count($search_preferences['Exclude_Words']) > 0) {
                $search_preferences['Search_Query'] .= " AND (";
                foreach ($search_preferences['Exclude_Words'] as $word)
                    foreach ($search_preferences['Search_Location'] as $ThisLocation)
                        $search_preferences['Search_Query'] .= '`' . trim($ThisLocation) . '`' . " NOT LIKE " . $db->qstr('%' . $word . '%') . " AND ";

                // Remove trailing AND/OR and space
                $search_preferences['Search_Query'] = preg_replace('`[\s]+(AND|OR){1,1}[\s]*$`', '', $search_preferences['Search_Query']);
                $search_preferences['Search_Query'] .= ') '; // Close search for excluded words
            }
        }
    $search_preferences['Where_Options'][] = $search_preferences['Search_Query'];

    // Start building the complete query
    $search_preferences['SQL_Query'] = '';



    // Start building the count query
    $search_preferences['SQL_Count_All'] = 'SELECT COUNT(*) ';

    // Build SELECT
    $search_preferences['Search_Select'] = 'SELECT ';
    if (is_array($search_preferences['Select_Options']) && !empty($search_preferences['Select_Options'])) {
        foreach ($search_preferences['Select_Options'] as $SelectOption) {
            $SelectOption = trim($SelectOption);
            $search_preferences['Search_Select'] .= $SelectOption . ', ';
        }
        unset($search_preferences['Select_Options']);
    }
    // Remove trailing comma and space
    $search_preferences['SQL_Query'] .= preg_replace('`,[\s]*$`', '', $search_preferences['Search_Select']);
    //$search_preferences['SQL_Count_All'] .= (!empty($search_preferences['Select_Relevancy']) ? ', '.$search_preferences['Select_Relevancy'] : '');
    unset($search_preferences['Search_Select']);

    // Add table within to search */
    $search_preferences['SQL_Query'] .= $search_preferences['From_Table'];
    $search_preferences['SQL_Count_All'] .= $search_preferences['From_Table'];

    // Build WHERE
    $search_preferences['Search_Where'] = ' WHERE 1 ';
    if (is_array($search_preferences['Where_Options']) && !empty($search_preferences['Where_Options'])) {
        foreach ($search_preferences['Where_Options'] as $WhereOption) {
            $WhereOption = trim($WhereOption);
            if (!empty($WhereOption))
                $search_preferences['Search_Where'] .= ' AND ' . $WhereOption . ' ';
        }
        unset($search_preferences['Where_Options']);
    }

    // Remove trailing 'AND' / 'OR'
    $where = preg_replace('`[\s]+(AND|OR){1,1}[\s]*$`', '', $search_preferences['Search_Where']);
    $search_preferences['SQL_Query'] .= $where . ' ' . trim($search_preferences['Relevancy_Tuning']);
    $search_preferences['SQL_Count_All'] .= $where;
    unset($search_preferences['Search_Where'], $where);

    // Build ORDER BY
    $search_preferences['Search_Order'] = ' ORDER BY ';
    if (is_array($search_preferences['Order_Options']) && !empty($search_preferences['Order_Options'])) {
        $search_preferences['Search_Order'] .= (!empty($search_preferences['Relevancy_Order']) ? $search_preferences['Relevancy_Order'] . ', ' : '');
        foreach ($search_preferences['Order_Options'] as $SearchOrder) {
            $SearchOrder = preg_replace('`,[\s]*$`', '', $SearchOrder);
            $SearchOrder = trim($SearchOrder);
            $search_preferences['Search_Order'] .= trim($SearchOrder) . ', ';
        }
        unset($search_preferences['Order_Options']);
    }
    /* Remove trailing comma and space */
    $search_preferences['SQL_Query'] .= preg_replace('`,[\s]*$`', '', $search_preferences['Search_Order']);
    unset($search_preferences['Search_Order']);

    // Make nice query, remove multiple: spaces, '`' (safe slashes), 'AND', 'OR', ...
    //$pattern = array ( '/(AND[\s])+/', '/(OR[\s])+/', '/\s+/', '/`+/', '`^[^\w\d]`', '`[^`\w\d]$`' );
    $pattern = array('#(AND[\s])+#i', '#(OR[\s])+#i', '#[\s]+#', '#[`]+#', '#^[^\w\d]#i', '#[^`\w\d]$#i');
    $replace = array('AND ', 'OR ', ' ', '`', '', '');
    $search_preferences['SQL_Query'] = preg_replace($pattern, $replace, $search_preferences['SQL_Query']);
    $search_preferences['SQL_Count_All'] = preg_replace($pattern, $replace, $search_preferences['SQL_Count_All']);

    return $search_preferences;
}

function custom_stripslashes($arr) {
    foreach ($arr as $k => $v) {
        if (is_array($v)) {
            $arr[$k] = custom_stripslashes($v);
        } else {
            $arr[$k] = stripslashes($v);
        }
    }
    return $arr;
}

function get_submit_items_validators($ltid) {
    global $db, $tables;

    $vids = $db->GetAll("SELECT DISTINCT `ITEM_ID` FROM `{$tables['submit_item_status']['name']}` WHERE `LINK_TYPE_ID` =" . $db->qstr($ltid) . " AND STATUS = '2'");

    $si_ids = "";
    for ($i = 0; $i < count($vids); $i++) {
        $si_ids .= "'" . $vids[$i]['ITEM_ID'] . "',";
    }
    $si_ids = rtrim($si_ids, ",");

     $sql = "SELECT  `{$tables['submit_item_status']['name']}`.ITEM_ID,
        `{$tables['submit_item_validator']['name']}`. VALIDATOR_ID ,
        `{$tables['submit_item_validator']['name']}`. VALUE ,
        `{$tables['submit_item_status']['name']}`.REQUIRED,
        `{$tables['submit_item']['name']}`.`FIELD_NAME`,
        `{$tables['validator']['name']}`.`NAME` AS `V_NAME`,
        if(`{$tables['submit_item_status']['name']}`.`REQUIRED`=1, 'required', '') as V_REQUIRED,
                `{$tables['validator']['name']}`.`IS_REMOTE`
        FROM `{$tables['submit_item_status']['name']}`
        LEFT JOIN `{$tables['submit_item_validator']['name']}` ON `{$tables['submit_item_status']['name']}`.`ITEM_ID` = `{$tables['submit_item_validator']['name']}`.`ITEM_ID`
        LEFT JOIN `{$tables['validator']['name']}` ON `{$tables['validator']['name']}`.`ID` = `{$tables['submit_item_validator']['name']}`.`VALIDATOR_ID`
        LEFT JOIN `{$tables['submit_item']['name']}` ON `{$tables['submit_item']['name']}`.`ID` = `{$tables['submit_item_status']['name']}`.`ITEM_ID`
         WHERE  `{$tables['submit_item']['name']}`.`ID` IN (" . $si_ids . ") AND LINK_TYPE_ID = ".$ltid;

    $vld = $db->GetAll($sql);

    $validators = array();
    for ($i = 0; $i < count($vld); $i++) {
        if ((bool)$vld[$i]['REQUIRED']) {
                $validators[$vld[$i]['FIELD_NAME']]['required'] = (bool)$vld[$i]['REQUIRED'];
            }
    if ($vld[$i]['IS_REMOTE'] != 0) {
            $validators[$vld[$i]['FIELD_NAME']]['remote'] = array(
                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                'type' => "post",
                'data' => array(
                    'action' => $vld[$i]['V_NAME'],
                    'table' => "link",
                    'field' => $vld[$i]['FIELD_NAME']
                )
            );
            if ($vld[$i]['V_NAME'] == 'isURLOnline') {
                $validators[$vld[$i]['FIELD_NAME']]['remote']['data']['LINK_TYPE'] = $ltid;
            }
        } else if($vld[$i]['V_NAME'] == 'required') {
           if ($vld[$i]['V_REQUIRED'] == 'required') {
              $validators[$vld[$i]['FIELD_NAME']][$vld[$i]['V_REQUIRED']] = true;	
          }
	}else {	   
	   if(!empty($vld[$i]['V_NAME']))
        $validators[$vld[$i]['FIELD_NAME']][$vld[$i]['V_NAME']] = true;         
        }
    }
    return $validators;
}

/**
 * Get full information on link
 * @param integer Link ID
 * @return array  Array with column names and link details
 */
function getFullLinkInfo($id = 0) {
    global $db, $tables;

    //Correct link ID
    $id = ($id < 1 ? 0 : $id);

    if (empty($id)) {
        return false;
    } else {
        //Select full link information along with category title
        $sql = "SELECT {$tables['link']['name']}.*, " . $db->IfNull("{$tables['category']['name']}.TITLE", "'Top'") . " AS `CATEGORY` FROM `{$tables['link']['name']}` LEFT JOIN `{$tables['category']['name']}` ON ({$tables['link']['name']}.CATEGORY_ID = {$tables['category']['name']}.ID) WHERE {$tables['link']['name']}.ID = " . $db->qstr($id) . " LIMIT 1";

        return $db->GetRow($sql); //Will auto return false on error
    }

    //Shouldn't get at this point
    return false;
}

function convertVideo($source, $dest, $convertTo, $size) {
    require_once 'include/Video/Ffmpeg.php';
    $converter = new Video_Ffmpeg();
    return $converter->convert($source, $dest, $convertTo, $size);
}

function thumbnailVideo($source, $dest, $size, $interval) {
    require_once 'include/Video/Ffmpeg.php';
    $converter = new Video_Ffmpeg();
    return $converter->createThumbnail($source, $dest, $size, $interval);
}

function resizeImg($source, $dest, $w, $h) {
    $imagedata = getimagesize($source);
    $imgW = $imagedata[0];
    $imgH = $imagedata[1];

    if (($w < $imgW) || ($h < $imgH)) {
        $w1 = ($h / $imgH) * $imgW;
        $h1 = ($w / $imgW) * $imgH;
        if ($w == 0) {
            $w = $w1;
        } elseif ($h == 0) {
            $h = $h1;
        } elseif ($w <= $w1 && $h1 <= $h) {
            $h = $h1;
        } else {
            $w = $w1;
        }
    } else {
        $w = $imgW;
        $h = $imgH;
    }
    $destAux = ImageCreateTrueColor($w, $h);

    $ext1 = explode('.', $dest);
   $ext = end($ext1);
 
    if ($ext == 'jpg' || $ext == 'jpeg') {
        $img = ImageCreateFromJpeg($source);
        imagecopyResampled($destAux, $img, 0, 0, 0, 0, $w, $h, $imagedata[0], $imagedata[1]);
        ImageJpeg($destAux, $dest, 100);
    } else if ($ext == 'png') {
        $img = ImageCreateFromPng($source);
		imagealphablending($destAux, false);
		imagesavealpha($destAux, true); 
        imagecopyResampled($destAux, $img, 0, 0, 0, 0, $w, $h, $imagedata[0], $imagedata[1]);
        ImagePng($destAux, $dest);
    } else if ($ext == 'gif') {
        $img = @ImageCreateFromGif($source);
		$transparent_index = ImageColorTransparent($img); 
        if($transparent_index!=(-1)) $transparent_color = ImageColorsForIndex($img,$transparent_index);
        if(!empty($transparent_color)) 
        {
        $transparent_new = ImageColorAllocate( $destAux, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue'] );
        $transparent_new_index = ImageColorTransparent( $destAux, $transparent_new );
        ImageFill( $destAux, 0,0, $transparent_new_index ); 
        }
        imagecopyResampled($destAux, $img, 0, 0, 0, 0, $w, $h, $imagedata[0], $imagedata[1]);
        ImageGif($destAux, $dest);
    }
}

/**
 * Strip tags and other characters that might break the JS for jQuery DataTabels
 * @param String $string
 */
function stripStrForDT($string)
{
    $string  = strip_tags($string);
     $string  = trim(htmlentities($string,ENT_QUOTES,'UTF-8'));
    $string = preg_replace( '/[\\x00-\\x1f]/e', '', $string);
    $string = preg_replace( '/\s+/', ' ', $string);
	$string = addslashes($string);
    
   //$allowed = "/[^A-Za-z0-9\\040\\.-\\_\\\\?!]/i"; //Allows letters a-z,digits,space (\\040), hyphen (\\-), underscore (\\_)
   //$string  = preg_replace($allowed,"", $string);
 
    return $string;
}



function get_submit_item_list($id, $values = false) {
    global $db, $tables;

    if (!$values) {
        $values = $db->GetOne("SELECT `VALUE` FROM `{$tables['submit_item_value']['name']}` WHERE `ITEM_ID` = '{$id}'");
    }

    $options = explode(',', $values);

    foreach ($options as $option) {
        $option = trim($option);
    }

    return $options;
}

function is_mobile_user() {
    
    if(USE_MOBILE_SITE != "1")
	return 0;

    require_once 'libs/MobileDetect/Mobile_Detect.php';
    $detect = new Mobile_Detect;
    $deviceType = ($detect->isMobile() ? 1 : '0');
    return $deviceType;

}

function parse_article_units($text) {
    global $db, $tables;

    preg_match_all("/{inline_widget-([0-9]+)}/", $text, $matches);

    $adunits_id = implode(',', $matches[1]);

    $adunits = $db->GetAssoc("SELECT * FROM `{$tables['inline_widget']['name']}` WHERE `ID` IN ({$adunits_id})");

    foreach ($matches[0] as $match_id => $match_val) {
        $matches[0][$match_id] = "/{$match_val}/";
    }

    foreach ($matches[1] as $adunit_id => $adunit_val) {
        $matches[1][$adunit_id] = $adunits[$adunit_val]['TEXT'];
    }

    $result = preg_replace($matches[0], $matches[1], $text);


    return $result;
}

function getLinkImages($group_id) {

    global $db, $tables;
    $images = $db->GetAll("SELECT * FROM `PLD_IMAGEGROUPFILE` WHERE GROUPID = '$group_id'");

    return $images;
}

function get_categ_link_types($id) {
    global $db, $tables;

    $link_types = $db->GetAll("SELECT * FROM `{$tables['category_link_type']['name']}` WHERE `CATEGORY_ID` = " . $db->qstr($id));

    return $link_types;
}

function unlinkRecursive($dir) {
    if (!$dh = @opendir($dir)) {
        return;
    }
    while (false !== ($obj = readdir($dh))) {
        if ($obj == '.' || $obj == '..') {
            continue;
        }

        if (!@unlink($dir . '/' . $obj)) {
            unlinkRecursive($dir . '/' . $obj, true);
        }
    }

    closedir($dh);


    return;
}

function do_math($n1, $n2) {
    return $n1 + $n2 * 7829;
}

function do_math_inverse($full) {
    $n2 = floor($full / 7829);
    $n1 = $full - ($n2 * 7829);
    return array('n1' => $n1, 'n2' => $n2);
}

// based on "Universal" API code for use with the BotScout.com API
// version 1.40 Code by MrMike / LDM 2-2009 
function check_botscout($ip, $email, $apikey) {
    $email = urlencode($email);
    $apiquery = "http://botscout.com/test/?multi&mail=$email&ip=$ip";
    if ($apikey != '') {
        $apiquery = "$apiquery&key=$apikey";
    }
    if (function_exists('file_get_contents')) {
        $returned_data = file_get_contents($apiquery);
    } else {
        $ch = curl_init($apiquery);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $returned_data = curl_exec($ch);
        curl_close($ch);
    }
    $botdata = explode('|', $returned_data);
    if ($botdata[3] > 0 || $botdata[5] > 0) {
        $reason = _L('Your email or ip is listed as a spambot at botscout.com') . '!';
        gotoUnauthorized($reason, $tplpath . 'unauthorized.tpl', false);
        exit();
    }
}

function get_task_settings($task_name) {
    $settings = array();

    $settings_fname = INSTALL_PATH . "tasks/{$task_name }/settings.xml";
    if (file_exists($settings_fname)) {
        if (($xml = file_get_contents($settings_fname)) !== false) {
            $p = xml_parser_create();
            xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($p, $xml, $vals, $index);
            xml_parser_free($p);

            foreach ($index as $k => $v) {
                if (strtoupper($k) != 'TASK' && strtoupper($k) != 'SETTING') {
                    for ($i = 0; $i < count($index[$k]); $i++) {
                        $settings[$i][strtoupper($k)] = $vals[$index[$k][$i]]['value'];
                    }
                }
            }
        }
    }

    return $settings;
}

function removebadsort($url, $varname) {
    list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
    parse_str($qspart, $qsvars);
    $sort_cols = array('P' => 'PAGERANK', 'H' => 'HITS', 'A' => 'TITLE', 'D' => 'DATE_ADDED');
    if (array_key_exists($qsvars[$varname], $sort_cols)) {
        $newqs = http_build_query($qsvars);
        return $urlpart . '?' . $newqs;
    } else {
        unset($qsvars[$varname]);
        return $urlpart;
    }
}


function buildMenuTree($plainList, $parent = 0, &$result = array())
{
    foreach ($plainList as $menuItem) {
        if ($menuItem['PARENT'] == $parent) {
            if ($parent == 0) {
                $result[$menuItem['ID']] = $menuItem;
                $temp = &$result[$menuItem['ID']];
            } else {
                $result['pages'][$menuItem['ID']] = $menuItem;
                $temp = &$result['pages'][$menuItem['ID']];
            }

            buildMenuTree($plainList, $menuItem['ID'], $temp);
        }
    }

    return $result;
}

function phpldAutoload($className) {
    $classParts = explode('_', $className);
    if (count($classParts) > 1) {
        switch ($classParts[0]) {
            case 'Phpld':
                $nameSpace = array_shift($classParts);
                $fileName = array_pop($classParts);
                $path = implode(DIRECTORY_SEPARATOR, $classParts);
                $path = INSTALL_PATH . '/code/' . $path . '/' . $fileName . '.php';
                require_once($path);
                break;
            case 'Model':
                $nameSpace = array_shift($classParts);
                $fileName = array_pop($classParts);
                $path = implode(DIRECTORY_SEPARATOR, $classParts) . '/';
                $path = INSTALL_PATH . '/application/models/' . $path . $fileName . '.php';
                require_once($path);
                break;
            case 'Widget':
                $nameSpace = array_shift($classParts);
                $fileName = array_pop($classParts);
                $path = implode(DIRECTORY_SEPARATOR, $classParts) . '/';
                $path = INSTALL_PATH . '/application/widgets' . $path . $fileName . '/widget.php';
                @include_once($path);
                break;
        }
    } else {
        switch (true) {
            case substr($className, -10) == 'Controller':
                $path = INSTALL_PATH . '/application/controllers/' . $className . '.php';
                require_once($path);
                break;
        }
    }
}

function geocodeAddress($country = null, $state = null, $city = null, $address = null, $zip = null) {
    $address_info = array();

    if (!is_null($country)) {
        $address_info[] = $country;
    }

    if (!is_null($state)) {
        $address_info[] = $state;
    }

    if (!is_null($city)) {
        $address_info[] = $city;
    }

    if (!is_null($address)) {
        $address_info[] = $address;
    }

    if (!is_null($zip)) {
        $address_info[] = $zip;
    }

    return geocode(implode(', ', $address_info));
}

function geocode($address)
{
//    die($address);
   $fullurl = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false";

   $ch = curl_init($fullurl); //changed from get file contents due to Google being all the man
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $string = curl_exec($ch);
   curl_close($ch);
    $json_a = json_decode($string, true); //json decoder
    if (is_null($json_a)) {
        return null;
    }
    return $json_a['results'][0]['geometry']['location'];
}

function getMimeFromFile($filepath) {
    if (function_exists('finfo_open') && function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $filepath);
    }
    else
        return false;
}

function getMimeFromUrl($url) {
    if (!function_exists('get_headers'))
        return null;
       
    $headers = @get_headers($url, 1);
    
    if (!$headers)
        return false;
    
    if (isset($headers['Content-Type']) && is_string($headers['Content-Type']))
        list($mime) = explode(';',$headers['Content-Type']);
    elseif(is_array($headers['Content-Type']) && isset($headers['Content-Type'][0]) && stristr($headers['Content-Type'][0],';'))
        list($mime) = explode(';',$headers['Content-Type'][0]);
    else
        $mime = false;
        
    return $mime;
}

function getImageExtensionByMime($mime) {
    $ext = '';
    switch($mime) {
        case 'image/bmp':
            return 'bmp';
        break;
    
        case 'image/gif':
            return 'gif';    
        break;
    
        case 'image/jpeg':
        case 'image/pjpeg':
            return 'jpg';
        break;
    
        case 'image/png':
        case 'image/x-png':
                return 'png';
        break;
    }
    return '';
}

function url_is_image($url)
{
    if (!function_exists('get_headers'))
        return null;
    
    $mimes = array(
        'image/bmp',
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png'
    );
    
    $headers = @get_headers($url, 1);

    if (!$headers)
        return false;
    
    if (isset($headers['Content-Type']) && is_string($headers['Content-Type']))
        list($mime) = explode(';',$headers['Content-Type']);
    elseif(is_array($headers['Content-Type']) && isset($headers['Content-Type'][0]) && stristr($headers['Content-Type'][0],';'))
        list($mime) = explode(';',$headers['Content-Type'][0]);
    else
        $mime = false;
    
    return in_array($mime,$mimes);
}
?>
