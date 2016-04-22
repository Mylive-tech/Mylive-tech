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

$tpl->assign('link_type_str', $link_type_str);

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];


if ($_REQUEST['action'])
{
   list($action, $id, $val) = explode(':', $_REQUEST['action']);

   $action = strtoupper (trim ($action));

   $val    = ($val < 0 ? 0 : intval ($val));
   $tpl->assign('action', strtoupper ($action));
}

//If editor, check if he/she is allowed to take an action on current link
if (!$_SESSION['phpld']['adminpanel']['is_admin'] && isset ($id))
{
   //Get categ ID of requested link
   $categID = $db->GetOne("SELECT `CATEGORY_ID` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));

   if (!in_array ($categID, $_SESSION['phpld']['adminpanel']['permission_array']))
   {
      //Editor is on unallowed page, block access
      http_custom_redirect("unauthorized.php");
      exit();
   }
}

//Correct value for ID
$id = (isset ($id) ? intval ($id) : 0);
$id = ($id < 0 ? 0 : $id);

$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active') ));
$tpl->assign('econfirm', array (0 => _L('No'), 1 => _L('Yes'),));

$linktypeid = 0;
if (isset($_REQUEST['LINK_TYPE']) && !empty($_REQUEST['LINK_TYPE'])) {
    $linktypeid = intval($_REQUEST['LINK_TYPE']);
} elseif (isset($id) && ($id > 0)) {
	$linktypeid = $db->GetOne("SELECT LINK_TYPE FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
}

$link_types = $db->GetAssoc("SELECT `ID`, `NAME`, `FEATURED` FROM `{$tables['link_type']['name']}` WHERE `STATUS` = '2' ORDER BY `ORDER_ID` ASC");
foreach ($link_types as $link_type_id => $link_type) {
        if ($linktypeid == 0) {
            $linktypeid = $link_type_id;
        }
        $link_types[$link_type_id]['FIELDS'] = $db->GetAssoc("SELECT submit_item.ID, submit_item.FIELD_NAME FROM `{$tables['submit_item']['name']}` AS submit_item, `{$tables['submit_item_status']['name']}` AS item_status WHERE item_status.LINK_TYPE_ID = '{$link_type_id}' AND item_status.ITEM_ID = submit_item.ID AND item_status.STATUS = '2' AND submit_item.IS_DEFAULT = '0'");
}
$tpl->assign('linktypeid', $linktypeid);
$tpl->assign('link_types', $link_types);

$link_type_details = $db->GetAssoc("SELECT * FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$linktypeid}'");
$tpl->assign('link_type_details', $link_type_details[$linktypeid]);

$submit_items = $db->GetAll("
	SELECT submit_item.*, item_status.STATUS, item_status.LINK_TYPE_ID 
	FROM `PLD_SUBMIT_ITEM` AS submit_item, `PLD_SUBMIT_ITEM_STATUS` AS item_status 
	WHERE item_status.ITEM_ID = submit_item.ID 
		AND item_status.LINK_TYPE_ID = '{$linktypeid}'
		AND item_status.STATUS = '2' 
	ORDER BY `ORDER_ID` ASC
");
$tpl->assign('submit_items', $submit_items);



//RALUCA: JQuery validation related
$submit_items_vld = get_submit_items_validators($linktypeid);

$validators = array(
	'rules' => array(
		'TITLE' => array(
			'required' => true,
			'minlength' => TITLE_MIN_LENGTH,
			'maxlength' => TITLE_MAX_LENGTH,
			'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
		        		'data'=> array (
		        			'action' => "isTitle",
		        			'table'  => "link",
		        			'field'  => "TITLE",
							'id'     => $id
		        		)
		    )
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
		'EXPIRY_DATE' => array(
                        'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
					'data'=> array (
						'action' => "isDate",
						'table' => "link",
						'field' => "EXPIRY_DATE"
						)
			)
		),
        'HITS' => array(
                        'number' => true
        ),
		'PAGERANK' => array(
                    'min' => -1,
                    'max' => 10
        ),
		'META_KEYWORDS' => array(
                    'minlength' => META_KEYWORDS_MIN_LENGTH,
                    'maxlength' => META_KEYWORDS_MAX_LENGTH
		),
		'META_DESCRIPTION' => array(
					'minlength' => META_DESCRIPTION_MIN_LENGTH,
					'maxlength' => META_DESCRIPTION_MAX_LENGTH
		)
	),
	'messages' => array(
		'CATEGORY_ID'=> array(
			'remote'  	=>_L("Please select a category.")
		),
		'TITLE'=> array(
			'remote'  	=>_L("Title is not valid: most likely, not unique in parent category.")
		)
	)
);

foreach ($submit_items_vld as $k => $v) {
	// TODO? maybe this should just check if the submit item is not default?
	switch ($k) {
		case 'TITLE':
		case 'CATEGORY_ID':
		case 'EXPIRY_DATE':
                case 'HITS':
		case 'PAGERANK':
		case 'META_DESCRIPTION':
			break;
		default:
			$validators['rules'][$k] = $v;
	}
}
$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related

switch ($action)
{
	case 'A' :
      $ActionStatus = SetNewReviewedLinkStatus($id, 2);
      $error = ($ActionStatus['status'] == 1 ? false : true);

      if ($ActionStatus['status'] != 1)
         $tpl->assign('sql_error', $ActionStatus['errorMsg']);

      if (!$error && isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);
		break;
	case 'R' :
      $ActionStatus = RemoveLinkReview($id);
      $error = ($ActionStatus['status'] == 1 ? false : true);

      if ($ActionStatus['status'] != 1)
         $tpl->assign('sql_error', $ActionStatus['errorMsg']);

      if (!$error && isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);
		break;
   case 'D' :
      $ActionStatus = RemoveLink($id);
      $error = ($ActionStatus['status'] == 1 ? false : true);

      if ($ActionStatus['status'] != 1)
         $tpl->assign('sql_error', $ActionStatus['errorMsg']);

      if (!$error && isset ($_SESSION['return']))
         http_custom_redirect($_SESSION['return']);
      break;
	case 'E' :
		if (empty ($_REQUEST['submit']))
		{
		  $data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
		  $revied_changes = $db->GetRow("SELECT * FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
		  unset ($revied_changes['LINK_ID']);
		  if (!empty ($revied_changes))
		     $data = array_merge($data, $revied_changes);
		}

	default :
      //Determine length of description field
      $DescriptionLimit = (isset ($data['DESCRIPTION']) && strlen (trim ($data['DESCRIPTION'])) > 0 ? DESCRIPTION_MAX_LENGTH - strlen (trim ($data['DESCRIPTION'])) : DESCRIPTION_MAX_LENGTH);
      $tpl->assign('DescriptionLimit', $DescriptionLimit);
      //Determine length of meta description field
      $MetaDescriptionLimit = (isset ($data['META_DESCRIPTION']) && strlen (trim ($data['META_DESCRIPTION'])) > 0 ? META_DESCRIPTION_MAX_LENGTH - strlen (trim ($data['META_DESCRIPTION'])) : META_DESCRIPTION_MAX_LENGTH);
      $tpl->assign('MetaDescriptionLimit', $MetaDescriptionLimit);

      // Get list of registered users
      $ActiveUsersList = $db->GetAssoc("SELECT `ID`, CONCAT(`LOGIN`, '  (', `NAME`, ' / ', `EMAIL`, ')') AS `USER` FROM `{$tables['user']['name']}` WHERE `ACTIVE` = '1'");
      $ActiveUsersList[0] = _L('Select username');
      ksort ($ActiveUsersList);
      $tpl->assign('ActiveUsersList', $ActiveUsersList);

     if (CAT_SELECTION_METHOD == 0 || $link_type_details['MULTIPLE_CATEGORIES'] != '')
	{
         $categs = get_categs_tree();
         $tpl->assign('categs', $categs);
      }

      if ((empty ($_REQUEST['submit'])))
      {
      }
      else
      {
	 $data = get_table_data('link');
	 if ($action == 'N')
	    {
	       $data['IPADDRESS']      = $client_info['IP'];
	       if (!empty ($client_info['HOSTNAME']))
		  $data['DOMAIN']      = $client_info['HOSTNAME'];
	       $data['VALID']         = 1;
	       $data['RECPR_VALID']   = 1;
	       $data['DATE_ADDED']    = gmdate ('Y-m-d H:i:s');
	       $data['DATE_MODIFIED'] = gmdate ('Y-m-d H:i:s');
	    }
	 $data['FEATURED'] = $_REQUEST['FEATURED'] == '1'?'1':'0';

	 $data['NOFOLLOW'] = $_REQUEST['NOFOLLOW'] == '1'?'1':'0';
	 $data['RECPR_REQUIRED'] = $_REQUEST['RECPR_REQUIRED'] == '1'?'1':'0';
         if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
            $data['URL'] = "http://".$data['URL'];

         if (strlen (trim ($data['RECPR_URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['RECPR_URL']))
            $data['RECPR_URL'] = "http://".$data['RECPR_URL'];

            $data['EXPIRY_DATE'] = trim ($data['EXPIRY_DATE']);

	 if (empty ($data['EXPIRY_DATE']))
	    $data['EXPIRY_DATE'] = '';
         else
         {
	    if (strtotime ($data['EXPIRY_DATE']) != -1)
	       $data['EXPIRY_DATE'] = date ('Y-m-d H:i:s', (strtotime ($data['EXPIRY_DATE'])));
	 }

       //RALUCA: JQuery validation related - server side.
       $validator = new Validator($validators);
       $validator_res = $validator->validate($_POST);
       //RALUCA: end of JQuery validation related - server side.


       if (empty($validator_res))
       {

	 if (empty ($id))
	    $id = $db->GenID($tables['link']['name'].'_SEQ');

            if ($data['FEATURED'] == '1')
            {
               $AllowedFeat = check_allowed_feat($data['CATEGORY_ID']);
               $tpl->assign('AllowedFeat', $AllowedFeat);
            }

	    if ($data['OWNER_ID'] > 0)
	    {
               $user_details = $db->GetRow("SELECT `LOGIN`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($data['OWNER_ID']));
               if (!empty ($user_details))
               {
                  if (!empty ($user_details['NAME']))
                     $data['OWNER_NAME'] = $user_details['NAME'];

                  if (!empty ($user_details['EMAIL']))
                     $data['OWNER_EMAIL'] = $user_details['EMAIL'];
               }
               unset ($user_details);
	    }
	    else
	       unset ($data['OWNER_ID']);

            if (ENABLE_PAGERANK == 1)
            {
               $data['PAGERANK'] = trim ($data['PAGERANK']);
               if (strlen ($data['PAGERANK']) == 0)
               {
                  require_once 'include/pagerank.php';
                  $data['PAGERANK'] = get_page_rank($data['URL']);

                  if (!empty($data['RECPR_URL']))
                     $data['RECPR_PAGERANK'] = get_page_rank($data['RECPR_URL']);
               }
            }
            $data['HITS'] = ($data['HITS'] < 1 ? 0 : intval ($data['HITS']));

	    $data['ID']   = $id;
	    if (!isset ($data['RECPR_REQUIRED']))
	       $data['RECPR_REQUIRED'] = 0;

	    if (db_replace('link', $data, 'ID') > 0)
	    {
	       
	        // Start Additional Links section
                    $db->Execute("DELETE FROM `{$tables['additional_link']['name']}` WHERE `LINK_ID`=$id");
                    if (isset($_REQUEST['ADD_LINK_URL']) && is_array($_REQUEST['ADD_LINK_URL'])) {
                        for ($i = 0; $i < count($_REQUEST['ADD_LINK_URL']); $i++) {
                            $add_link_url = trim($_REQUEST['ADD_LINK_URL'][$i]);
                            if (!empty($add_link_url)) {
                                $add_link_url = (substr($add_link_url, 0, 7) != 'http://' ? 'http://' . $add_link_url : $add_link_url);
                                $add_link_data['URL'] = $add_link_url;

                                $add_link_data['LINK_ID'] = $id;
                                $add_link_data['TITLE'] = $_REQUEST['ADD_LINK_TITLE'][$i] ? $_REQUEST['ADD_LINK_TITLE'][$i] : $_REQUEST['ADD_LINK_URL'][$i];
                                $res = db_replace('additional_link', $add_link_data, 'ID');
                            }
                        }
                    }
		    
		     $db->Execute("DELETE FROM `{$tables['additional_category']['name']}` WHERE `LINK_ID`=$id");

                    if (isset($_REQUEST['ADD_CATEGORY_ID']) && is_array($_REQUEST['ADD_CATEGORY_ID'])) {
                        for ($i = 0; $i < count($_REQUEST['ADD_CATEGORY_ID']); $i++) {
                            $add_cat_id = trim($_REQUEST['ADD_CATEGORY_ID'][$i]);
                            if (!empty($add_cat_id)) {
                                $add_cat_data['LINK_ID'] = $id;
                                $add_cat_data['CATEGORY_ID'] = $_REQUEST['ADD_CATEGORY_ID'][$i];
                                db_replace('additional_category', $add_cat_data);
                            }
                        }
                    }
	      
	       $linkModel = new Model_Link();
	       $id = $data['ID'];
	       $seoUrl = $linkModel->seoUrl($data, $id);
	       $db->execute("UPDATE `{$tables['link']['name']}` SET `CACHE_URL` = '".$seoUrl."' WHERE ID = ".$id);
				 
				 
	       $tpl->assign('posted', true);
               send_status_notifications($id, true, false);
               $db->Execute("DELETE FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
               $db->Execute("DELETE FROM `{$tables['additional_category_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
               $db->Execute("DELETE FROM `{$tables['additional_link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
               if (isset ($_SESSION['return']))
                  http_custom_redirect($_SESSION['return']);
	    }
            else
	       $tpl->assign('sql_error', $db->ErrorMsg());
	       
	    }
	    
	 }
	 $add_links = $db->GetAll("SELECT * FROM `{$tables['additional_link_review']['name']}` WHERE `LINK_ID` = '{$id}'");
        $add_categs = $db->GetAll("SELECT * FROM `{$tables['additional_category_review']['name']}` WHERE `LINK_ID` = '{$id}'");
        $tpl->assign("add_links", $add_links);
        $tpl->assign("add_categs", $add_categs);
        $imagegroupname = null;
        $group_image_details = null;
        foreach ($submit_items as $key_id => $submit_item) {
            if ($submit_item['TYPE'] == "IMAGEGROUP") {
                $imagegroupname = $submit_item['FIELD_NAME'];
            }
        }
        if($imagegroupname!=null)
            $group_image_details = getLinkImages($data[$imagegroupname]);
	 $tpl->assign('group_image_details', $group_image_details);
      $tpl->assign($data);
     $tpl->assign('LINK_ID', $id);
      $tpl->assign('data', $data);
      $content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_review_links_edit.tpl');
      break;
}
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>
