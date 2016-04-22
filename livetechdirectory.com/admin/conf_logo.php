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

$action = $_GET['act'];

switch ($action) {
    default:
        $phpldSettings = read_config($db);

        $logoSize = getimagesize(INSTALL_PATH.$phpldSettings['FRONT_LOGO']);
        $defaultLogoOptions = array(
            'marginTopValue' => 0,
            'marginRightValue' => 0,
            'marginBottomValue' => 0,
            'marginLeftValue' => 0,
            'widthValue' => $logoSize[0],
        );

        if (!empty($_POST)) {
            if ($_FILES['logo']['error'] == UPLOAD_ERR_OK) {

                $path = INSTALL_PATH.'uploads/'.$_FILES['logo']['name'];
                //die($path);

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $path)) {
                    $data['ID']       = 'FRONT_LOGO';
                    $data['VALUE']    = 'uploads/'.$_FILES['logo']['name'];

                    $phpldSettings['FRONT_LOGO'] = 'uploads/'.$_FILES['logo']['name'];
                    $db->Replace($tables['config']['name'], $data, 'ID', true);

                    $data['ID']       = 'FRONT_LOGO_OPTIONS';
                    $data['VALUE']    = $defaultLogoOptions;

                }
            } else {

                $data['ID']       = 'FRONT_LOGO_OPTIONS';
                $data['VALUE']    = serialize(array_merge($defaultLogoOptions, $_POST['logo_options']));
            }

            $db->Replace($tables['config']['name'], $data, 'ID', true);

            $url       = DOC_ROOT."/conf_logo.php";
            $title_msg = _L('Logo options saved');
            $status    = 1;
            $cust_msg = '';
            $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
            $tpl->assign('redirect', $redirect);
        }

        $tpl->assign('currentLogo', $phpldSettings['FRONT_LOGO']);
        if (isset($phpldSettings['FRONT_LOGO_OPTIONS']) && !empty($phpldSettings['FRONT_LOGO_OPTIONS'])) {
            $tpl->assign('logoOptions', unserialize($phpldSettings['FRONT_LOGO_OPTIONS']));
        } else {
            $tpl->assign('logoOptions',$defaultLogoOptions);
        }
    break;

    case 'clear':

        $db->Execute('DELETE FROM '.$tables['config']['name'].' WHERE ID = "FRONT_LOGO" OR ID = "FRONT_LOGO_OPTIONS"');
        http_custom_redirect(DOC_ROOT."/conf_logo.php");
        break;
}
$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_logo.tpl');
$tpl->assign('content', $content);

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>