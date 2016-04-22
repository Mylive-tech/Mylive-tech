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
    list ($action, $id, $val) = explode(':', $_REQUEST['action']);

    $action = strtoupper (trim ($action));

    $tpl->assign('action', strtoupper ($action));
}

//Correct value for ID
if (isset ($id)) {
    $id = trim($id);
}

//next one's the zone name
if (isset($val)) {
    $val    = trim($val);
}

if ($id != '') {

    if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
        $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

    $wd = Phpld_Widget::load($id);

    switch ($action)
    {
        case 'A' : //ACTIVATE
            if ($id != 'MainContent') {
                if ($wd->activate($val)) {
                    $_SESSION['wid_message'] = "Changes made.";
                    $tpl->assign('op_status', 1);
                } else {
                    $tpl->assign('op_status', -1);
                }
            }
            if (isset ($_SESSION['return']))
                http_custom_redirect($_SESSION['return']);
            break;
        case 'D' : //DEACTIVATE
            if ($id != 'MainContent') {
                if ($wd->deactivate()) {
                    $_SESSION['wid_message'] = "Changes made.";
                    $tpl->assign('op_status', 1);
                } else {
                    $tpl->assign('op_status', -1);
                }
            }
            if (isset ($_SESSION['return']))
                http_custom_redirect($_SESSION['return']);
            break;
        case 'R' : //REMOVE FROM ZONE
            if ($id != 'MainContent') {
                if ($wd->removeFromZone()) {
                    $_SESSION['wid_message'] = "Changes made.";
                    $tpl->assign('op_status', 1);
                } else {
                    $tpl->assign('op_status', -1);
                }
            }
            if (isset ($_SESSION['return']))
                http_custom_redirect($_SESSION['return']);
            break;
        default :
            break;

    }

} else {
    switch($action) {
        case 'O':
            $widgets = $_REQUEST['ids'];
            $o = 1;
            foreach ($widgets as $widget) {
                $db->Execute("UPDATE `{$tables['widget_activated']['name']}`
							SET `ORDER_ID` = ".$o++."
							WHERE `ID` = ".$db->qstr($widget));
            }
            print_r("sdasd");
            break;
    }
    if (isset ($_SESSION['return']))
        http_custom_redirect($_SESSION['return']);
}
?>
