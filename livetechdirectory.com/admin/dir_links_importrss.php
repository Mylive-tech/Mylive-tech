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
require_once 'include/rss_parser.php';

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
	$_SESSION['return'] = $_SERVER['HTTP_REFERER'];

$cid = $_REQUEST['c'];
$tpl->assign('cid',$cid);
$tpl->assign('path',get_path($cid));

//RALUCA: JQuery validation related
$validators = array(
	'rules' => array(
		'rss_url' => array(
			'url' => true
		)
	)
);

$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related

if (empty ($_POST['submit'])){
} else {
   if (strlen (trim ($_REQUEST['rss_url'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $_REQUEST['rss_url']))
      $_REQUEST['rss_url'] = "http://".$_REQUEST['rss_url'];

	$tpl->assign('rss_url', $_REQUEST['rss_url']);
	
   //RALUCA: JQuery validation related - server side.
   $validator = new Validator($validators);
   $validator_res = $validator->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_res))
   {
		$rss        = new rssParser();
		$rss_result = $rss->parse($_REQUEST['rss_url']);
		if($rss_result !== true)
			$tpl->assign('error', $rss_result);

		$tpl->assign('link_count', count($rss->items));
		$links = array();
		if (count($rss->items) > 0)
		{
			foreach ($rss->items as $item)
				$links[] = add_link($cid,$item['link'], $item['title'], $item['description'], $_POST['status']);

			$tpl->assign('list', $links);
			$tpl->assign('columns', array ('TITLE' => _L('Title'), 'URL' => _L('URL'), 'ERROR' => _L('Result')));
		}
	}
}

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_links_importrss.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');

// Function to add link data to database
function add_link($cid,$link, $title = 'N/A', $desc = 'N/A', $status = '2')
{
	global $db, $tables;
	$data                   = array ();
	$data['TITLE']          = $title;
	$data['DESCRIPTION']    = $desc;
	$data['CATEGORY_ID']    = $cid;
	$data['URL']            = $link;
	$data['RECPR_REQUIRED'] = 0;
	$data['STATUS']         = $status;
	$error                  = array ();
	if (!check_unique('link', 'TITLE', $title, NULL, 'CATEGORY_ID', $cid))
		$error['TITLE'] = true;


        $cu = check_unique('link', 'URL', $link, NULL, 'CATEGORY_ID', $cid);


	if (!$cu)
		$error['URL'] = true;

	if (count ($error) > 0)
	{
		$data['ERROR'] = $error;
		return $data;
	}

	$data['IPADDRESS']      = $client_info['IP'];
   if (!empty ($client_info['HOSTNAME']))
      $data['DOMAIN']      = $client_info['HOSTNAME'];

	$data['VALID']         = 1;
	$data['LAST_CHECKED']  = gmdate('Y-m-d H:i:s');
	$data['DATE_ADDED']    = gmdate('Y-m-d H:i:s');
	$data['DATE_MODIFIED'] = gmdate('Y-m-d H:i:s');
   if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
      $data['URL'] = "http://".$data['URL'];

	if (ENABLE_PAGERANK)
	{
		require_once '../include/pagerank.php';
		$data['PAGERANK'] = get_page_rank($data['URL']);
	}
	$id = $db->GenID($tables['link']['name'].'_SEQ');
	$data['ID'] = $id;

	if ($db->Replace($tables['link']['name'], $data, 'ID', true) == 0)
		$error['SQL'] = true;

	$data['ERROR'] = $error;
	return $data;
}
?>