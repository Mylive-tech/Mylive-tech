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

$action = $_REQUEST['act'];

$phpldSettings = read_config($db);
$additionalFonts = array();
if (isset($phpldSettings['UPLOADED_FONTS'])) {
    $additionalFonts = unserialize($phpldSettings['UPLOADED_FONTS']);
}

switch ($action) {
    default:
        $defaultFonts = array(
            'Arial'=>'Arial',
            'Arial Black'=>'Arial Black',
            'Comic Sans MS'=>'Comic Sans MS',
            'Courier New'=>'Courier New',
            'Georgia'=>'Georgia',
            'Impact'=>'Impact',
            'Tahoma'=>'Tahoma',
            'Times New Roman'=>'Times New Roman',
            'Trebuchet MS'=>'Trebuchet MS',
            'Verdana'=>'Verdana',
        );
        if (!empty($_POST)) {
            // Set header font
            $data['ID']       = 'HEADER_FONT';
            $data['VALUE']    = $_POST['HEADER_FONT'];

            $phpldSettings['HEADER_FONT'] = $_POST['HEADER_FONT'];
            $db->Replace($tables['config']['name'], $data, 'ID', true);

            // Set content font
            $data['ID']       = 'CONTENT_FONT';
            $data['VALUE']    = $_POST['CONTENT_FONT'];

            $phpldSettings['CONTENT_FONT'] = $_POST['CONTENT_FONT'];
            $db->Replace($tables['config']['name'], $data, 'ID', true);


            // Set site name font
            $data['ID']       = 'SITENAME_FONT';
            $data['VALUE']    = $_POST['SITENAME_FONT'];

            $phpldSettings['SITENAME_FONT'] = $_POST['SITENAME_FONT'];
            $db->Replace($tables['config']['name'], $data, 'ID', true);

            $url       = DOC_ROOT."/conf_fonts.php";
            $title_msg = _L('Fonts options saved');
            $status    = 1;
            $cust_msg = '';
            $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
            $tpl->assign('redirect', $redirect);

        }


        $fonts = array_merge($defaultFonts, $additionalFonts);
        $tpl->assign('fonts', $fonts);
        break;

        case 'upload_font':

        if (!empty($_POST)) {
            if ($_FILES['font']['error'] == UPLOAD_ERR_OK) {
                $path = INSTALL_PATH.'uploads/'.$_FILES['font']['name'];


                if (move_uploaded_file($_FILES['font']['tmp_name'], $path)) {
                    $additionalFonts[$_FILES['font']['name']] = $_POST['font_name'];

                    $serialized = serialize($additionalFonts);
                    // Set site name font
                    $data['ID']       = 'UPLOADED_FONTS';
                    $data['VALUE']    = $serialized;

                    $db->Replace($tables['config']['name'], $data, 'ID', true);

                    $url       = DOC_ROOT."/conf_fonts.php";
                    $title_msg = _L('Fonts options saved');
                    $status    = 1;
                    $cust_msg = '';
                    $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
                    $tpl->assign('redirect', $redirect);

                }
            }
        }

    break;

}

$tpl->assign('currentHeaderFont', $phpldSettings['HEADER_FONT']);
$tpl->assign('currentContentFont', $phpldSettings['CONTENT_FONT']);
$tpl->assign('currentSitenameFont', $phpldSettings['SITENAME_FONT']);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_fonts.tpl');
$tpl->assign('content', $content);
//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>