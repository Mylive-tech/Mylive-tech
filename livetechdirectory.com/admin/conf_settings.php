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
require_once 'admin/conf_options.php';

//RALUCA: JQuery validation related
$validators = array(
	'rules' => array(
	)
);

$_REQUEST['c'] = (!empty ($_REQUEST['c']) && $_REQUEST['c'] > 0 ? intval ($_REQUEST['c']) : 1);

foreach ($conf as $i => $row) {
	if ($row['CONFIG_GROUP'] == $_REQUEST['c']) {
		if ($row['TYPE'] == 'STR' || $row['TYPE'] == 'PAS' || $row['TYPE'] == 'LOG' || $row['TYPE'] == 'LKP') {
			if ($row['REQUIRED'] == 1) {
            	$validators['rules'][$row['ID']]['required'] = true;
			}
		}
		if ($row['TYPE'] == 'EML') {
			$validators['rules'][$row['ID']]['email'] = true;
		}
		if ($row['TYPE'] == 'URL') {
			$validators['rules'][$row['ID']]['url'] = true;
		}
		if ($row['TYPE'] == 'INT') {
			$validators['rules'][$row['ID']]['digits'] = true;
		}
		if ($row['TYPE'] == 'NUM') {
                    if ($row['ID'] == 'ARTICLE_PAY_NORMAL' || $row['ID'] == 'ARTICLE_PAY_FEATURED') {
                        $validators['rules'][$row['ID']]['remote'] = array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
						'type'=> "post",
						'data'=> array (
							'action' => "isPaypalSet",
							'table' => "config",
							'field' => $row['ID']
							)
				);
                        $validators['messages'][$row['ID']]['remote'] = _L("Your PAYPAL ACCOUNT has not been filled in. Please set your PAYPAL ACCOUNT ")."<a href='".$DOC_ROOT."/conf_settings.php?c=9&r=1'>". _L("here")."</a>.";
                        
                    } else {
                        $validators['rules'][$row['ID']]['remote'] = array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
						'type'=> "post",
						'data'=> array (
							'action' => "isNumber",
							'table' => "config",
							'field' => $row['ID']
							)
				);
                    }
                  }

	}
}	 

$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related


$_REQUEST['c'] = (!empty ($_REQUEST['c']) && $_REQUEST['c'] > 0 ? intval ($_REQUEST['c']) : 1);

if (empty ($_REQUEST['submit']))
{
	$tpl->assign('submit_session', registerAdminSubmitSession());
	if (isset($_SESSION['show_cache_msg']) && $_SESSION['show_cache_msg'] == 1) {
		$tpl->assign('show_cache_msg', 1);
		unset($_SESSION['show_cache_msg']);
	}
	
	$sql = "SELECT `ID`, `VALUE` FROM `{$tables['config']['name']}`";
	$conf_vals = $db->GetAssoc($sql);

	foreach ($conf as $k => $row)
		if ($conf[$k]['CONFIG_GROUP'] != $_REQUEST['c'])
			unset ($conf[$k]);
		else
			$conf[$k]['VALUE'] = $conf_vals[$row['ID']];
}
else
{
	checkAdminSubmitSession(clean_string($_POST['submit_session']));
	if (ENABLE_PAGERANK == 0)
		$_REQUEST['SHOW_PAGERANK'] = 0;

	if ($_REQUEST['SHOW_PAGERANK'] == 0 && $_REQUEST['DEFAULT_SORT'] == "P")
		$_REQUEST['DEFAULT_SORT'] = 'H';

    //ADDED FOR LINK PROBLEM
		
 	if (!empty ($_REQUEST['SITE_URL'])) {
      //Strip whitespace
      $_REQUEST['SITE_URL'] = strip_white_space($_REQUEST['SITE_URL'], '');


      //Add "http://" if missing
      if (strlen ($_REQUEST['SITE_URL']) > 0 && !preg_match ('#^http[s]?:\/\/#i', $_REQUEST['SITE_URL']))
         $_REQUEST['SITE_URL'] = "http://" . $_REQUEST['SITE_URL'];


      //Add trailing slash "/" if missing
      $_REQUEST['SITE_URL'] = (substr ($_REQUEST['SITE_URL'], -1) != '/' ? $_REQUEST['SITE_URL'] . '/' : $_REQUEST['SITE_URL']);
    }
		
    //END OF ADDED FOR LINK PROBLEM
		
	foreach ($conf as $i => $row)
		if ($conf[$i]['CONFIG_GROUP'] != $_REQUEST['c'])
			unset ($conf[$i]);
      else
         $conf[$i]['VALUE'] = $_REQUEST[$row['ID']];

   //RALUCA: JQuery validation related - server side.
   $validator = new Validator($validators);
   $validator_res = $validator->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_res))
   {
		$posted = true;
		if (!DEMO)
		{
         $errors   = 0;
         $cust_msg = '';
			foreach ($conf as $row)
         {
         	
         	         	
            $posted = $db->AutoExecute($tables['config']['name'], $row, 'UPDATE', '`ID` = '.$db->qstr($row['ID']));
         	if ($row['ID'] == 'ENABLE_REWRITE') {
         		$_SESSION['show_cache_msg'] = 1;	
         	}	
         	if (!$posted)
            	{
            		
               $errors++;
               $cust_msg = $db->ErrorMsg();
					break;
            }
         }

         
			$tpl->assign('posted', $posted);

         //Redirecting to drop Smarty errors and have live preview of captcha
         $url       = DOC_ROOT."/conf_settings.php?c={$_REQUEST['c']}&r=1";
         $title_msg = ($errors > 0 ? _L('An error occured while saving.') : _L('Settings updated.'));
         $status    = ($errors > 0 ? -1 : 0);
         $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, $status);
         $tpl->assign('redirect', $redirect);
		}
	} else {
		$url       = DOC_ROOT."/conf_settings.php?c={$_REQUEST['c']}&r=1";
		$errors = 1;
		$title_msg = _L('Something went wrong with the form validation, please check and try again.');
		$status    = ($errors > 0 ? -1 : 0);
        $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, $status);
        $tpl->assign('redirect', $redirect);
	}
}

foreach ($conf as $i => $val)
{
	if ($conf[$i]['TYPE']=='LKP' && is_string ($conf[$i]['OPTIONS']))
	{
		$rs = $db->Execute($conf[$i]['OPTIONS']);
		$conf[$i]['OPTIONS'] = array ('0' => '<'._L('Select an option').'>') + $rs->GetAssoc();
	}
}

$tpl->assign('conf_group', $_REQUEST['c']);
$tpl->assign('conf', $conf);
$tpl->assign('conf_categs', $conf_categs);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_settings.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

//Clear compiled template file
$tpl->clear_compiled_tpl('admin/conf_settings.tpl');
?>