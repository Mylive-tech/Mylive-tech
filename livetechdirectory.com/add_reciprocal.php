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

$id = ( isset ($_REQUEST['id']) ? trim ($_REQUEST['id']) : (isset ($_REQUEST['ID']) ? trim ($_REQUEST['ID']) : 0) );

$id = preg_replace ('`(id[_]?)`', '', $id);
$id = (preg_match ('`^[\d]+$`', $id) ? intval ($id) : 0);

if (empty ($_REQUEST['submit']))
{
	if (!empty ($_SERVER['HTTP_REFERER']))
		$_SESSION['return'] = $_SERVER['HTTP_REFERER'];

	if (!empty ($id))
   {
		if ($data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id)))
      {
			if (empty ($data['RECPR_URL']))
         {
				$_SESSION['cid'] = $data['CATEGORY_ID'];
				$validators = array(
                                        'rules' => array(
                                                'RECPR_URL' => array(
                                                        'required' => true,
                                                        'remote' => array(
                                                            'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                                                            'type'=> "post",
                                                            'data'=> array (
                                                                    'action' => "isRecprOnline",
                                                                    'table'  => "link",
                                                                    'field'  => "RECPR_URL"
                                                            )
                                                        )
                                                 )
                                           )
                                   );
                                $vld = json_custom_encode($validators);
                                $tpl->assign('validators', $vld);

                                $validator = new Validator($validators);
			}
         else
				$tpl->assign('link_id_error', 'Reciprocal link is already defined for this link.');
		}
      else
			$tpl->assign('link_id_error', 'Please ensure that the URL is complete.');
	}
   else
		$tpl->assign('link_id_error', 'Please ensure that the URL is complete.');
}
else
{
	if ($data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id)))
   {
		$data['IPADDRESS']      = $client_info['IP'];
      if (!empty ($client_info['HOSTNAME']))
         $data['DOMAIN']      = $client_info['HOSTNAME'];

		$data['RECPR_URL'] = $_REQUEST['RECPR_URL'];
		$data['VALID'] = 1;
		if ($data['RECPR_REQUIRED'])
      {
			$data['RECPR_VALID'] = 1;
			$data['RECPR_LAST_CHECKED'] = gmdate ('Y-m-d H:i:s');
		}
		$data['LAST_CHECKED']  = gmdate ('Y-m-d H:i:s');
		//$data['DATE_ADDED']    = gmdate ('Y-m-d H:i:s');
		unset ($data['EXPIRY_DATE']);
		$data['DATE_MODIFIED'] = gmdate ('Y-m-d H:i:s');
		if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
         $data['URL'] = "http://".$data['URL'];

      if (strlen (trim ($data['RECPR_URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['RECPR_URL']))
         $data['RECPR_URL'] = "http://".$data['RECPR_URL'];

                $validator = new Validator($validators);
        	$validator_res = $validator->validate($_POST);
		if (empty($validator_res) && !empty ($id))
			if ($db->Replace($tables['link']['name'], $data, 'ID', true) > 0)
				$tpl->assign('posted', true);
         else
				$tpl->assign('sql_error', $db->ErrorMsg());
	}
   else
		$tpl->assign('sql_error', $db->ErrorMsg());
}

$path = get_path($_SESSION['cid']);
$path[] = array ('ID' => '0', 'TITLE' => _L('Add Reciprocal Link for ' .$data['TITLE']), 'TITLE_URL' => '', 'DESCRIPTION' => _L('Rate A Link'));
$tpl->assign('path', $path);
$tpl->assign($data);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Compress output for faster loading
if (COMPRESS_OUTPUT == 1)
   $tpl->load_filter('output', 'CompressOutput');

//Make output
echo $tpl->fetch('add_reciprocal.tpl');
?>