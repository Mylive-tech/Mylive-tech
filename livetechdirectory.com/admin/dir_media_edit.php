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
  # @package        PHPLinkDirectory Article Edition
  # @version        2.0.0
  # ################################################################################
 */
require_once 'init.php';

$error = 0;

if (empty($_REQUEST['submit']) && !empty($_SERVER['HTTP_REFERER'])) {
    $_SESSION['return'] = $_SERVER['HTTP_REFERER'];
}

if (isset($_SESSION['wid_message'])) {
    $tpl->assign('wid_message', $_SESSION['wid_message']);
    unset($_SESSION['wid_message']);
}

if (isset($_SESSION['wid_error'])) {
    $tpl->assign('wid_error', $_SESSION['wid_error']);
    unset($_SESSION['wid_error']);
}




if ($_REQUEST['action']) {
    list ($action, $id) = explode(':', $_REQUEST['action']);

    $action = strtoupper(trim($action));
    $tpl->assign('action', strtoupper($action));
}

//Correct value for ID
$id = (isset($id) ? intval($id) : 0);
$id = ($id < 0 ? 0 : $id);
$tpl->assign('id', $id);



switch ($action) {
    case 'D':
        $media_data = $db->GetRow("SELECT * FROM `{$tables['media_manager_items']['name']}` WHERE `ID` = '{$id}'");
        if (!empty($media_data)) {
            unlink(dirname(__file__) . "/../uploads/media/" . $media_data['USER_ID'] . '/' . $media_data['FILE_NAME']);
            $db->Execute("DELETE FROM `{$tables['media_manager_items']['name']}` WHERE `ID` = '{$id}'");
        }
        http_custom_redirect(DOC_ROOT . "/dir_media.php");
        break;


    case 'E':
        if (empty($_REQUEST['submit']))
            $data = $db->GetRow("SELECT * FROM `{$tables['media_manager_items']['name']}` WHERE `ID` = " . $db->qstr($id));
    case 'N':
    default :
        if (!empty($_POST['submit'])) {
            if (!empty($id))
                $old_data = $db->GetRow("SELECT * FROM `{$tables['media_manager_items']['name']}` WHERE `ID` = " . $db->qstr($id));
            $data = get_table_data('media_manager_items');
            //RALUCA: JQuery validation related - server side.
            if ($id > 0)
                $data['ID'] = $id;
            else
                $data['ID'] = $db->GenID($tables['media_manager_items']['name'] . '_SEQ');

            $data['FILE_NAME'] = '';

            ////submit items: files

            if (!empty($_FILES['FILE']['name'])) {
                if ($data['TYPE'] == 'image')
                    $allowed = array("jpg", "jpeg", "png", "gif", "bmp");
                //$ext = strtolower(end(explode('.', )));
				$ext1 = explode('.', $_FILES['FILE']['name']);
								$ext = end($ext1);
								$ext = strtolower($ext);
                $name = $_FILES['FILE']['name'];
                if (empty($data['USER_ID']))
                    $data['USER_ID'] = '0';
                if (!is_dir((INSTALL_PATH . 'uploads/media/' . $data['USER_ID']))) {
                    mkdir(INSTALL_PATH . 'uploads/media');
                }
                if (!is_dir((INSTALL_PATH . 'uploads/media/' . $data['USER_ID']))) {
                    if (mkdir(INSTALL_PATH . 'uploads/media/' . $data['USER_ID'],0777, true)) {
                    } else {
                        print "Directory create Error: " . INSTALL_PATH . 'uploads/media/' . $data['USER_ID'];
                    }
                }

                if (file_exists(INSTALL_PATH . 'uploads/media/' . $data['USER_ID'] . '/' . $name)) {
                    unlink(INSTALL_PATH . 'uploads/media/' . $data['USER_ID'] . '/' . $name);
                }

                if (in_array($ext, $allowed)) {
                    if (move_uploaded_file($_FILES['FILE']['tmp_name'], INSTALL_PATH . 'uploads/media/' . $data['USER_ID'] . '/' . $name)) {
                        $data['FILE_NAME'] = $name;
                    }
                }
            }
            if ($data['FILE_NAME'] != $old_data['FILE_NAME'] && !empty($old_data['FILE_NAME']) && !empty($data['FILE_NAME']))
                unlink(INSTALL_PATH . 'uploads/media/' . $old_data['USER_ID'] . '/' . $old_data['FILE_NAME']);
            if (!empty($old_data['FILE_NAME']) && empty($data['FILE_NAME']) && $data['USER_ID'] != $old_data['USER_ID']) {
                if (rename(INSTALL_PATH . 'uploads/media/' . $old_data['USER_ID'] . '/' . $old_data['FILE_NAME'], INSTALL_PATH . 'uploads/media/' . $data['USER_ID'] . '/' . $old_data['FILE_NAME']))
                    $data['FILE_NAME'] = $old_data['FILE_NAME'];
            }

            if (db_replace('media_manager_items', $data, 'ID') > 0) {
                $tpl->assign("posted", true);
            } else {
                var_dump($db->ErrorMsg());
            }
        }
        $tpl->assign($data);
        break;
}

// Get list of registered users
$ActiveUsersList = $db->GetAssoc("SELECT `ID`, CONCAT(`LOGIN`, '  (', `NAME`, ' / ', `EMAIL`, ')') AS `USER` FROM `{$tables['user']['name']}` WHERE `ACTIVE` = '1' ORDER BY USER");

// ksort ($ActiveUsersList);
$tpl->assign('ActiveUsersList', $ActiveUsersList);
if (empty($data['USER_ID']))
    $tpl->assign('USER_ID', '1');

$content = $tpl->fetch(ADMIN_TEMPLATE . '/dir_media_edit.tpl');

$tpl->assign('error', $error);

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');
?>