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

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

if (strpos($_SESSION['return'], 'dir_pages.php') === false ) {
    unset($_SESSION['return']);
}

//RALUCA: JQuery validation related
$validators = array(
	'rules' => array(
		'META_DESCRIPTION' => array(
			'minlength' => META_DESCRIPTION_MIN_LENGTH,
			'maxlength' => META_DESCRIPTION_MAX_LENGTH
		)
	)
);

$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related
   
if ($_REQUEST['action'])
{
   list ($action, $id, $val) = explode(':', $_REQUEST['action']);

   $action = strtoupper (trim ($action));
   $id     = ($id < 0  ? 0 : intval ($id));
   $val    = ($val < 0 ? 0 : intval ($val));

   $tpl->assign('action', strtoupper ($action));
}

//If editor, check if he/she is allowed to take an action on current link
if (!$_SESSION['phpld']['adminpanel']['is_admin'])
{
	if (($_SESSION['phpld']['adminpanel']['rights']['addPage'] != 1 && $action == 'N') 
	|| ($_SESSION['phpld']['adminpanel']['rights']['editPage'] != 1 && ($action == 'E' || $action == 'M'))
	|| ($_SESSION['phpld']['adminpanel']['rights']['delPage'] != 1 && ($action == 'D'))
	|| !($_REQUEST['action']))
   {
      //Editor is on unallowed page, block access
      http_custom_redirect("unauthorized.php");
      exit();
   }
}

$tpl->assign('status' , array (0 => _L('Inactive') , 2 => _L('Active'),));
$tpl->assign('privacy', array (0 => _L('All users'), 1 => _L('Registered users'),));


switch ($action)

{

   case 'D' : //Delete

      $ActionStatus = $db->Execute("DELETE FROM `{$tables['page']['name']}` WHERE `ID` = ".$db->qstr($id));

      if (!$error && isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);

      break;

   case 'S' : //Status

      $ActionUrgency = $db->Execute("UPDATE `{$tables['page']['name']}` SET `STATUS` = '$val' WHERE `ID` = ".$db->qstr($id));

      if (!$error && isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);

      break;

   case 'E' : //Edit

      if (empty ($_REQUEST['submit']))
      {
         $data = $db->GetRow("SELECT * FROM `{$tables['page']['name']}` WHERE `ID` = ".$db->qstr($id));
      }





   case 'N' : //New
   default :
   	 		
 //Determine length of meta description field
      $MetaDescriptionLimit = (isset ($data['META_DESCRIPTION']) && strlen (trim ($data['META_DESCRIPTION'])) > 0 ? META_DESCRIPTION_MAX_LENGTH - strlen (trim ($data['META_DESCRIPTION'])) : META_DESCRIPTION_MAX_LENGTH);
      $tpl->assign('MetaDescriptionLimit', $MetaDescriptionLimit);
      if (empty ($_POST['submit'])) {
      	$tpl->assign('submit_session', registerAdminSubmitSession());
      } else {
       checkAdminSubmitSession(clean_string($_POST['submit_session']));
       $tpl->assign('submit_session', registerAdminSubmitSession());
      	
       $data = get_table_data('page');

       $data['ID']            = $id;
       $data['DATE_MODIFIED'] = date("Y-m-d H:i:s");

       if ($action == "N") {
           $data['STATUS']     = 2;
       }
	   $data['SHOW_IN_MENU']         = $_POST['SHOW_IN_MENU']       == '1' ? '1' : '0';
       // Write values to DB
       
	   //RALUCA: JQuery validation related - server side.
	   $validator = new Validator($validators);
	   $validator_res = $validator->validate($_POST);
	   //RALUCA: end of JQuery validation related - server side.
	   
	   if (empty($validator_res))
	   {
           $pageModel = new Model_Page();
           if (isset($data['ID']) && !empty($data['ID'])) {
               $data['SEO_NAME'] = $pageModel->seoUrl($data['NAME'], $data['ID']);
           }
           if (($replaceResult = db_replace('page', $data, 'ID')) > 0)
           {
               if ($replaceResult == 2) {
                   $id = $db->Insert_ID();
                   $seoUrl = $pageModel->seoUrl($data['NAME'], $id);
                   $db->execute('UPDATE PLD_PAGE SET `SEO_NAME` = "'.$seoUrl.'" WHERE ID = '.$id);
               }

              $tpl->assign('posted', true);

              if (isset ($_SESSION['return']))
                 http_custom_redirect($_SESSION['return']);
           }
           else {
              $tpl->assign('sql_error', $db->ErrorMsg());
           }
	   }
   }


    $tpl->assign($data);
    
	 $inline_widgets = $db->GetAll("SELECT `ID`, `NAME` FROM `{$tables['inline_widget']['name']}` WHERE `STATUS` = '1'");
	 $tpl->assign('inline_widgets', $inline_widgets);
    $content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_pages_edit.tpl');

    break;

}


$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

?>
