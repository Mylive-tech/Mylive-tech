<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
/*#################################################################*\
|# Licence Number 0JKF-0621-0SE1-0112
|# -------------------------------------------------------------   #|
|# Copyright (c)2013 PHP Link Directory.                           #|
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
$categoryModel = new Model_Category();
if (empty($_REQUEST['submit']) && !empty($_SERVER['HTTP_REFERER']))
    $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

    
$error = 0;
$errorMsg = '';

if ($_REQUEST['action']) {
    list ($action, $id) = explode(':', $_REQUEST['action']);

    $action = strtoupper(trim($action));
    $tpl->assign('action', strtoupper($action));
}

$tpl->assign('zone_types', array('CUSTOM' => 'CUSTOM'));
//RALUCA: JQuery validation related
$validators = array(
    'rules' => array(
        'NAME' => array(
            'required' => true,
            'minlength' => TITLE_MIN_LENGTH,
            'maxlength' => TITLE_MAX_LENGTH,
            'remote' => array(
                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                'type' => "post",
                'data' => array(
                    'action' => "isWidgetZoneName",
		    'table' => "widget_zones",
                    'field' => "NAME",
                    'id' => $id
                )
            )
        )),
    'messages' => array(
        'NAME' => array(
            'remote' => _L("Name is not valid: most likely, not unique.")
        )
    )
);


$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);


switch ($action) {
   
    case 'D' :
        $error = 0;
	    $widget_data = $db->GetRow("SELECT * FROM {$tables['widget_zones']['name']} WHERE NAME = '{$id}'  AND TYPE = 'CUSTOM' ");
	    
	    if($widget_data){
		 $db->Execute("DELETE FROM {$tables['widget_zones']['name']} WHERE NAME = '{$id}'  AND TYPE = 'CUSTOM' ");
		 $db->Execute("DELETE FROM {$tables['widget_activated']['name']} WHERE ZONE = '{$id}' ");
	    }
	   
	    http_custom_redirect(DOC_ROOT . '/dir_widget_zones.php?r=1');
        break;

    case 'E' :
        if (empty($_REQUEST['submit']))
            $data = $db->GetRow("SELECT * FROM `{$tables['widget_zones']['name']}` WHERE `NAME` = " . $db->qstr($id));
	    
	

    case 'N' :
    default :
       
        if (empty($_REQUEST['submit'])) {
            if ($action == 'N') {
                $data = array();
            }
            $tpl->assign('submit_session', registerAdminSubmitSession());
        } else {
            checkAdminSubmitSession(clean_string($_POST['submit_session']));
            $tpl->assign('submit_session', registerAdminSubmitSession());
            $data = get_table_data('widget_zones');
	    $data['NAME'] = trim($data['NAME']);
	    $data['NAME'] = preg_replace('/[^\da-z]/i', '_',$data['NAME']);
	    $data['NAME'] = str_replace('__', '_', $data['NAME']);

            //RALUCA: JQuery validation related - server side.
            $validator = new Validator($validators);
            $validator_res = $validator->validate($_POST);
            //RALUCA: end of JQuery validation related - server side.


            if (empty($validator_res)) {
                
		if(!empty($id)){
		    $db->Execute("UPDATE {$tables['widget_zones']['name']} SET NAME = '{$data['NAME']}' WHERE NAME = '{$id}' ");
		    $db->Execute("UPDATE {$tables['widget_activated']['name']} SET ZONE = '{$data['NAME']}' WHERE ZONE = '{$id}' ");
		}
		
		
		
		    
		   
                if ($db->Replace($tables['widget_zones']['name'], $data, 'NAME', true) > 0)
		{
                   
                    $tpl->assign('posted', true);
                   
                    if (isset($_SESSION['return']))
                        http_custom_redirect($_SESSION['return']);
                }
                else
                    $tpl->assign('sql_error', $db->ErrorMsg());
            } else {
                $tpl->assign('errorMsg', _L('Validation error: Please check your input data and try again.'));
            }
        }

        break;
}

$tpl->assign('error', $error);
$tpl->assign('errorMsg', $errorMsg);

$tpl->assign($data);

$content = $tpl->fetch(ADMIN_TEMPLATE . '/dir_widget_zones_edit.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');
?>
