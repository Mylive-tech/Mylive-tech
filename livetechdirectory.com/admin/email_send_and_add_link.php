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
require_once 'email_admin.php';

$script_root = substr ($_SERVER["SCRIPT_NAME"], 0, strrpos ($_SERVER["SCRIPT_NAME"], '/'));
define ('DOC_ROOT', substr ($script_root, 0, strrpos ($script_root, '/')));

if (empty ($_POST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
{
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];
}

//Determine length of description field
$DescriptionLimit = (isset ($data['DESCRIPTION']) && strlen (trim ($data['DESCRIPTION'])) > 0 ? DESCRIPTION_MAX_LENGTH - strlen (trim ($data['DESCRIPTION'])) : DESCRIPTION_MAX_LENGTH);
$tpl->assign('DescriptionLimit', $DescriptionLimit);

//Determine length of meta description field
$MetaDescriptionLimit = (isset ($data['META_DESCRIPTION']) && strlen (trim ($data['META_DESCRIPTION'])) > 0 ? DESCRIPTION_MAX_LENGTH - strlen (trim ($data['META_DESCRIPTION'])) : DESCRIPTION_MAX_LENGTH);
$tpl->assign('MetaDescriptionLimit', $MetaDescriptionLimit);

//RALUCA: JQuery validation related
$validators = array(
	'rules' => array(
		'TITLE' => array(
			'required' => true
		),
		'EMAIL' => array(
			'required' => true,
			'email' => true,
			'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
		        		'data'=> array (
		        			'action' => "isCheckedEmail",
		        			'table' => "link",
		        			'field' => "EMAIL"
		        		)
		    )
		),
		'URL' => array(
			'url' => true
		),
		'CATEGORY_ID' => array(
			'required' => true,
			'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
					'data'=> array (
						'action' => "isNotTopCat",
						'table' => "category",
						'field' => "CATEGORY_ID"
						)
			)
		),
		'DESCRIPTION' => array(
			'minlength' => DESCRIPTION_MIN_LENGTH,
			'maxlength' => DESCRIPTION_MAX_LENGTH,
		),
		'META_DESCRIPTION' => array(
			'minlength' => DESCRIPTION_MIN_LENGTH,
			'maxlength' => DESCRIPTION_MAX_LENGTH,
		),
		'META_KEYWORDS' => array(
			'minlength' => '0',
			'maxlength' => '1024',
		)
	),
	'messages' => array(
		'CATEGORY_ID'=> array(
			'remote'  	=>_L("Please select a category.")
		)
	)
);
$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);
$validator = new Validator($validators);
//RALUCA: end of JQuery validation related

if (empty ($_POST['submit'])) {
} else {
	$data              = get_table_data('email');
	$data['DATE_SENT'] = gmdate ('Y-m-d H:i:s');

   if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
      $data['URL'] = "http://".$data['URL'];

	$full_data = $data;
	$full_data['CATEGORY_ID'] = $_REQUEST['CATEGORY_ID'];
	$full_data['DESCRIPTION'] = $_REQUEST['DESCRIPTION'];
	$full_data['META_DESCRIPTION'] = $_REQUEST['META_DESCRIPTION'];
	$full_data['META_KEYWORDS']    = $_REQUEST['META_KEYWORDS'];

	//RALUCA: JQuery validation related - server side.
   $validator = new Validator($validators);
   $validator_res = $validator->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_res))
   {
		// Generate Link ID first
		$link_id = $db->GenID($tables['link']['name'].'_SEQ');
		$email_data = $full_data;
		$email_data['ADD_RECIPROCAL_URL'] = "http://" . $_SERVER['HTTP_HOST'] . DIRECTORY_ROOT . "/add_reciprocal.php?id=" . $link_id;

        //Determine category of saved link
        $category_cache = $db->GetOne("SELECT `CACHE_URL` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($full_data['CATEGORY_ID']));
        $category_cache = (!empty ($category_cache) ? trim ($category_cache) : buildCategUrl($full_data['CATEGORY_ID']));
    
        $email_data['LINK_URL'] = "http://" . $_SERVER['HTTP_HOST'] . DIRECTORY_ROOT . "/". $category_cache;

		$tmpl = $db->GetRow("SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($_REQUEST['EMAIL_TPL_ID']));
		$mail = get_emailer();
		$mail->Body = replace_email_vars($tmpl['BODY'], $email_data);
		$mail->Subject = replace_email_vars($tmpl['SUBJECT'], $email_data);
		$mail->AddAddress($email_data['EMAIL'], $email_data['NAME']);
		if (!DEMO)
			$sent = $mail->Send();
        else
			$sent = true;

		if ($sent)
		{
			$id = $db->GenID($tables['email']['name'].'_SEQ');
			$data['ID'] = $id;
			if ($db->Replace($tables['email']['name'], $data, 'ID', true) > 0)
			{
				// Save to Links table
				$link_data                   = get_table_data('link');
//				$link_data['RECPR_REQUIRED'] = REQUIRE_RECIPROCAL;
				$link_data['STATUS']         = 2;
				$link_data['OWNER_NAME']     = $data['NAME'];
				$link_data['OWNER_EMAIL']    = $data['EMAIL'];
				$link_data['DATE_ADDED']     = gmdate('Y-m-d H:i:s');
				$link_data['DATE_MODIFIED']  = gmdate('Y-m-d H:i:s');
            /*
				$link_data['META_DESCRIPTION'] = $full_data['META_DESCRIPTION'];
	         $link_data['META_KEYWORDS']    = $full_data['META_KEYWORDS'];
            */
				if (ENABLE_PAGERANK)
                {
					require_once 'include/pagerank.php';
					$link_data['PAGERANK'] = get_page_rank($link_data['URL']);
				}
				$link_data['ID'] = $link_id;
				if ($db->Replace($tables['link']['name'], $link_data, 'ID', true) > 0)
				{
					$category = $db->GetOne("SELECT `TITLE` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `ID` = ".$db->qstr($full_data['CATEGORY_ID']));
					$full_data['CATEGORY'] = $category;
					$tpl->assign('posted', true);
					$tpl->assign('sent', $full_data);

               unset ($data, $full_data, $category, $link_data);
					$data = array();
				}
				else
					$tpl->assign('sql_error', $db->ErrorMsg());
			}
			else
				$tpl->assign('sql_error', $db->ErrorMsg());
		}
		else
			$tpl->assign('send_error', true);
	}
}

$rs = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '3'");
$tpls = $rs->GetAssoc();
$tpl->assign('tpls', $tpls);
$tpl->assign($full_data);
$tpl->assign('EMAIL_TPL_ID', $_REQUEST['EMAIL_TPL_ID']);
$tpl->assign('IGNORE'      , $_REQUEST['IGNORE']);

if (AJAX_CAT_SELECTION_METHOD == 0)
{
   $categs = get_categs_tree();
   $tpl->assign('categs', $categs);
}

$content = $tpl->fetch(ADMIN_TEMPLATE.'/email_send_and_add_link.tpl');

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

function check_email($value, $empty, & $params, & $form) {
	global $db, $tpl, $tables;
	$rs = $db->Execute("SELECT `ID`, `TITLE`, `URL` FROM `{$tables['link']['name']}` WHERE `URL` = ".$db->qstr($form['URL'])." OR TITLE = ".$db->qstr($form['TITLE']));
	$err['dir'] = array();
	while(!$rs->EOF) {
		if(strcasecmp($rs->Fields('URL'),$form['URL']) == 0) {
			$err['dir'][] = 'URL';
		}
		if(strcasecmp($rs->Fields('TITLE'),$form['TITLE']) == 0) {
			$err['dir'][] = 'TITLE';
		}
		$rs->MoveNext();
	}
	$rs = $db->Execute("SELECT * FROM `{$tables['email']['name']}` WHERE `URL` = ".$db->qstr($form['URL'])." OR `TITLE` = ".$db->qstr($form['TITLE'])." OR `EMAIL` = ".$db->qstr($form['EMAIL']));
	$err['email'] = array();
	while(!$rs->EOF) {
		$row = array('EMAIL'     => htmlentities(format_email($rs->Fields('EMAIL'), $rs->Fields('NAME'))),
					    'TITLE'     => $rs->Fields('TITLE'),
					    'URL'       => $rs->Fields('URL'),
                   'DATE'      => $rs->Fields('DATE_SENT'));
		if(strcasecmp($rs->Fields('EMAIL'),$form['EMAIL']) == 0) {
			$row['TYPE'] = 'EMAIL';
		}
		if(strcasecmp($rs->Fields('URL'),$form['URL']) == 0) {
			$row['TYPE'] = 'URL';
		}
		if(strcasecmp($rs->Fields('TITLE'),$form['TITLE']) == 0) {
			$row['TYPE'] = 'TITLE';
		}
		$err['email'][] = $row;
		$rs->MoveNext();
	}
	if(count($err['dir']) > 0 || count($err['email']) > 0) {
		$tpl->assign('email_send_errors', $err);
		return $_REQUEST['IGNORE'];
	}
	return 1;
}
?>