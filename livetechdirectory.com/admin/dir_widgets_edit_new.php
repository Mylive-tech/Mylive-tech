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

if (empty($_REQUEST['submit']) && !empty($_SERVER['HTTP_REFERER']))
    $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

if ($_REQUEST['action']) {
    list ($action, $id, $val) = explode(':', $_REQUEST['action']);

    $action = strtoupper(trim($action));

    $val = ($val < 0 ? 0 : intval($val));
    $tpl->assign('action', strtoupper($action));
}

//Correct value for ID
if (isset($id)) {
    $id = trim($id);
}
if ($id != '' && $id != 'MainContent') {

    $wd = Phpld_Widget::load($id);
    switch ($action) {
        case 'I' : //INSTALL
            if ($wd->install()) {
                $tpl->assign('op_status', 1);
                $_SESSION['wid_message'] = "Widget installed successfully.";
                http_custom_redirect("dir_widgets_pick_zones.php?id=" . $id);
            } else {
                $tpl->assign('op_status', -1);
                $_SESSION['wid_error'] = "Widget could not be installed.";
            }
            if (isset($_SESSION['return']))
                http_custom_redirect($_SESSION['return']);
            break;
        case 'U' : //UNINSTALL
            if ($wd->uninstall()) {
                $_SESSION['wid_message'] = "Widget uninstalled successfully.";
                $tpl->assign('op_status', 1);
            } else {
                $_SESSION['wid_error'] = "Widget could not be uninstalled.";
                $tpl->assign('op_status', -1);
            }
            if (isset($_SESSION['return']))
                http_custom_redirect($_SESSION['return']);
            break;
        case 'E' : //Edit
            if (empty($_REQUEST['submit'])) {
                $content = $wd->getSettingsForm();

                $tpl->assign('content', $content);
                //Clean whitespace
                $tpl->load_filter('output', 'trimwhitespace');
                //Make output
                echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');
            } else {
                $data = $wd->GetSettings();
                $to_update = array();
                foreach ($data as $i => $opt) {
                    $data[$i]['SETTING_VALUE'] = $_POST[$data[$i]['IDENTIFIER']];
                    $to_update[] = $data[$i];
                }
                $wd->saveSettings($to_update);
                $_SESSION['wid_message'] = "Widget saved.";
                if (isset($_SESSION['return']))
                    http_custom_redirect($_SESSION['return']);
            }

            break;
        default :
            break;
    }
} else {
    switch ($action) {
        case 'MULTI':
            $type = $_POST['type'];
            if (isset($_POST['install'])) {
                foreach ($_POST as $key => $value) {
                    if (strpos($key, "multi-") !== false) {
                        $widname = 'Widget_' . substr($key, 6);
                        $wd = new $widname($widname, '');
                        $wd->install();
                    }
                }
            } elseif (isset($_POST['uninstall'])) {
                foreach ($_POST as $key => $value) {
                    if (strpos($key, "multi-") !== false) {
                        $widname = 'Widget_' . substr($key, 6);
                        $wd = new $widname($widname, '');
                        $wd->uninstall();
                    }
                }
            } elseif (isset($_POST['hide'])) {
                foreach ($_POST as $key => $value) {
                    if (strpos($key, "multi-") !== false) {
                        $widname = 'Widget_' . substr($key, 6);
                        $wd = new $widname($widname, '');
                        $wd->deactivate($type);
                    }
                }
            } elseif (isset($_POST['show'])) {
                foreach ($_POST as $key => $value) {
                    if (strpos($key, "multi-") !== false) {
                        $widname = 'Widget_' . substr($key, 6);
                        $wd = new $widname($widname, '');
                        $wd->activate($type);
                    }
                }
            }
            $_SESSION['wid_message'] = "Changes made.";
            break;
    }


    if (isset($_SESSION['return']))
        http_custom_redirect($_SESSION['return']);
}
?>
