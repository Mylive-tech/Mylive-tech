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

if (empty($_REQUEST['submit']) && !empty($_SERVER['HTTP_REFERER']))
    $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

if (strpos($_SESSION['return'], 'dir_approve_links.php') === false && strpos($_SESSION['return'], 'dir_links.php') === false && strpos($_SESSION['return'], 'dir_review_links.php') === false && strpos($_SESSION['return'], 'conf_payment.php') === false) {
    unset($_SESSION['return']);
}

if ($_REQUEST['action']) {
    list ($action, $id) = explode(':', $_REQUEST['action']);
    
    
    $action = strtoupper(trim($action));

    //$val = ($val < 0 ? 0 : intval($val));
    $tpl->assign('action', strtoupper($action));
}
$tagsModel = new Model_Tag();
//If editor, check if he/she is allowed to take an action on current link
if (!$_SESSION['phpld']['adminpanel']['is_admin']) {
    //Get categ ID of requested link
    $categID = $db->GetOne("SELECT `CATEGORY_ID` FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($id));

//   if (!in_array ($categID, $_SESSION['phpld']['adminpanel']['permission_array']))
    if (($_SESSION['phpld']['adminpanel']['rights']['addLink'] != 1 && $action == 'N')
            || ($_SESSION['phpld']['adminpanel']['rights']['editLink'] != 1 && ($action == 'E' || $action == 'M'))
            || ($_SESSION['phpld']['adminpanel']['rights']['delLink'] != 1 && ($action == 'D'))
            || !($action)) {
        //Editor is on unallowed page, block access
        http_custom_redirect("unauthorized.php");
        exit();
    }
}
$oembed = Model_Link_Handler_Oembed::getInstance();

//Correct value for ID
$id = (isset($id) ? intval($id) : 0);
$id = ($id < 0 ? 0 : $id);

$tpl->assign('stats', array(0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'),));
$tpl->assign('econfirm', array(0 => _L('No'), 1 => _L('Yes'),));

$linktypeid = 0;
if (isset($_REQUEST['LINK_TYPE']) && !empty($_REQUEST['LINK_TYPE'])) {
    $linktypeid = intval($_REQUEST['LINK_TYPE']);
} elseif (isset($id) && ($id > 0)) {
    $linktypeid = $db->GetOne("SELECT LINK_TYPE FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($id));
}

$link_types = $db->GetAssoc("SELECT `ID`, `NAME`, `FEATURED`, `COUNT_IMAGES` FROM `{$tables['link_type']['name']}` WHERE `STATUS` = '2' ORDER BY `ORDER_ID` ASC");
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

$sqlSubmitItems = "
	SELECT submit_item.*, item_status.STATUS, item_status.LINK_TYPE_ID 
	FROM `{$tables['submit_item']['name']}` AS submit_item, `{$tables['submit_item_status']['name']}` AS item_status 
	WHERE item_status.ITEM_ID = submit_item.ID 
		AND item_status.LINK_TYPE_ID = '{$linktypeid}'
		AND item_status.STATUS = '2' 
	ORDER BY `ORDER_ID` ASC
";
$submit_items = $db->GetAll($sqlSubmitItems);

foreach ($submit_items as $submit_item_id => $submit_item) {
    if ($submit_item['TYPE'] == 'DROPDOWN' || $submit_item['TYPE'] == 'MULTICHECKBOX') {
        $submit_items[$submit_item_id]['OPTIONS'] = get_submit_item_list($submit_item['ID']);
    }
}

$tpl->assign("submit_items", $submit_items);

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
                'type' => "post",
                'data' => array(
                    'action' => "isTitle",
                    'table' => "link",
                    'field' => "TITLE",
                    'id' => $id
                )
            )
        ),
        'CATEGORY_ID' => array(
            'required' => true,
            'remote' => array(
                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                'type' => "post",
                'data' => array(
                    'action' => "isNotTopCat",
                    'table' => "category",
                    'field' => "CATEGORY_ID"
                )
            )
        ),
        'EXPIRY_DATE' => array(
            'remote' => array(
                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                'type' => "post",
                'data' => array(
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
        ),
        'IMAGE_SUBMIT' => array(
            'remote' => array(
                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                'type' => "post",
                'data' => array(
                    'action' => "urlIsImage",
                    'table' => "",
                    'field' => "IMAGE_SUBMIT"
                )
            )
        )
    ),
    'messages' => array(
        'CATEGORY_ID' => array(
            'remote' => _L("Please select a category.")
        ),
        'TITLE' => array(
            'remote' => _L("Title is not valid: most likely, not unique in parent category.")
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
        case 'IMAGE_SUBMIT':
            break;
        default:
            $validators['rules'][$k] = $v;
            foreach ($v as $kk => $vv) {
                if (($vv['data']['action'] == 'isURLOnline')) {
                    $validators['rules'][$k][$kk]['data']['id'] = $id;
                }
            }
    }
}
$vld = json_custom_encode($validators);
//var_dump($validators, $vld);
//die();
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related
switch ($action) {
    case 'S' : //Set Status
        $ActionStatus = SetNewLinkStatus($id, $val);
        $error = ($ActionStatus['status'] == 1 ? false : true);

        if ($ActionStatus['status'] != 1)
            $tpl->assign('sql_error', $ActionStatus['errorMsg']);

        if (!$error && isset($_SESSION['return']))
            http_custom_redirect($_SESSION['return']);
        break;
    case 'A' : //Approve
        $ActionStatus = SetNewLinkStatus($id, 2, 0);
        $error = ($ActionStatus['status'] == 1 ? false : true);

        if ($ActionStatus['status'] != 1)
            $tpl->assign('sql_error', $ActionStatus['errorMsg']);

        if (!$error && isset($_SESSION['return']))
            http_custom_redirect($_SESSION['return']);
        break;
    case 'D' : //Delete
        $ActionStatus = RemoveLink($id);
        $error = ($ActionStatus['status'] == 1 ? false : true);

        if ($ActionStatus['status'] != 1)
            $tpl->assign('sql_error', $ActionStatus['errorMsg']);

        if (!$error && isset($_SESSION['return']))
            http_custom_redirect($_SESSION['return']);
        break;
    case 'E' : //Edit

        if (empty($_REQUEST['submit'])) {
            $data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($id));
        }

    case 'N' : //New
    default :
        // Get list of registered users
        $linkModel = new Model_Link();
        $ActiveUsersList = $db->GetAssoc("SELECT `ID`, CONCAT(`LOGIN`, '  (', `NAME`, ' / ', `EMAIL`, ')') AS `USER` FROM `{$tables['user']['name']}` WHERE `ACTIVE` = '1'");
        $ActiveUsersList[0] = _L('Select username');
        ksort($ActiveUsersList);
        $tpl->assign('ActiveUsersList', $ActiveUsersList);

        if (empty($data['LINK_TYPE'])) {
            $data['LINK_TYPE'] = $linktypeid;
        }

        if ($action == 'N') {
            $data['STATUS'] = 2;
        }

        if (ADMIN_CAT_SELECTION_METHOD == 0 || $link_type_details[$linktypeid]['MULTIPLE_CATEGORIES'] != '') {
            $categs = get_categs_tree();
            $tpl->assign('categs', $categs);
        }
        if (empty($_POST['submit'])) {
            $tpl->assign('submit_session', registerAdminSubmitSession());
        } else {
            checkAdminSubmitSession(clean_string($_POST['submit_session']));
            $tpl->assign('submit_session', registerAdminSubmitSession());

            $old_data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID`=" . $db->qstr($id));
            //$_REQUEST['']
            $data = get_table_data('link');
            $data['LINK_TYPE'] = $linktypeid;
            
            if ($action == 'N') {
                $data['IPADDRESS'] = $client_info['IP'];
                if (!empty($client_info['HOSTNAME']))
                    $data['DOMAIN'] = $client_info['HOSTNAME'];

                $data['VALID'] = 1;
                $data['RECPR_VALID'] = 1;

                $data['OWNER_EMAIL_CONFIRMED'] = 1;

                $data['DATE_ADDED'] = gmdate('Y-m-d H:i:s');
                $data['DATE_MODIFIED'] = gmdate('Y-m-d H:i:s');
            }

            $data['NOFOLLOW'] = $link_type_details[$linktypeid]['NOFOLLOW'] == '1' ? '1' : '0';
            $data['RECPR_REQUIRED'] = $_POST['RECPR_REQUIRED'] == '1' ? '1' : '0';



            if (trim($data['EXPIRY_DATE']) == '')
                $data['EXPIRY_DATE'] = '';
            else
            if (strtotime($data['EXPIRY_DATE']) != -1)
                $data['EXPIRY_DATE'] = date('Y-m-d H:i:s', (strtotime($data['EXPIRY_DATE'])));

            $data['FEATURED'] = $link_type_details[$linktypeid]['FEATURED'] == '1' ? '1' : '0';

            //If editor, check if he/she is allowed to take an action on current category
            if (!$_SESSION['phpld']['adminpanel']['is_admin'] &&
                    !empty($data['CATEGORY_ID']) &&
                    (($_SESSION['phpld']['adminpanel']['rights']['addLink'] != 1 && $action == 'N') || ($_SESSION['phpld']['adminpanel']['rights']['editLink'] != 1 && ($action == 'E' || $action == 'M')))
            ) {
                //Editor is on unallowed page, block access
                http_custom_redirect("unauthorized.php");
                exit();
            }

            //RALUCA: JQuery validation related - server side.
            $validator = new Validator($validators);
            $validator = new Validator($validators);
            $validate_arr = $_POST;
	    foreach($_FILES as $k => $v)
		 $validate_arr[$k] = $v['name'];

            $validator_res = $validator->validate($validate_arr);
            //RALUCA: end of JQuery validation related - server side.

            if (empty($validator_res)) {
                if (empty($id)) {
                   // $id = $db->Insert_ID();
				   $id = $db->GenID($tables['link']['name'] . '_SEQ');
                }

                if ($data['FEATURED'] == '1') {
                    $AllowedFeat = check_allowed_feat($data['CATEGORY_ID']);
                    $tpl->assign('AllowedFeat', $AllowedFeat);
                }

                if ($data['OWNER_ID'] > 0) {
                    $user_details = $db->GetRow("SELECT `LOGIN`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ID` = " . $db->qstr($data['OWNER_ID']));
                    if (!empty($user_details)) {
                        if (!empty($user_details['NAME']))
                            $data['OWNER_NAME'] = $user_details['NAME'];

                        if (!empty($user_details['EMAIL']))
                            $data['OWNER_EMAIL'] = $user_details['EMAIL'];
                    }
                    unset($user_details);
                }
                else
                    unset($data['OWNER_ID']);

                if ($data['OWNER_ID'] == 0 && empty($data['OWNER_NAME'])) {
                    $admin_details = $db->GetRow("SELECT `ID`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' AND `ACTIVE` = '1' LIMIT 1");
                    $data['OWNER_NAME'] = $admin_details['NAME'];
                    $data['OWNER_EMAIL'] = $admin_details['EMAIL'];
                    $data['OWNER_ID'] = $admin_details['ID'];
                }

                if (ENABLE_PAGERANK == 1) {
                    $data['PAGERANK'] = trim($data['PAGERANK']);
                    if (strlen($data['PAGERANK']) == 0) {
                        require_once 'include/pagerank.php';
                        $data['PAGERANK'] = get_page_rank($data['URL']);

                        if (!empty($data['RECPR_URL']))
                            $data['RECPR_PAGERANK'] = get_page_rank($data['RECPR_URL']);
                    }
                }
                $data['HITS'] = ($data['HITS'] < 1 ? 0 : intval($data['HITS']));

                $data['ID'] = $id;
                if (!isset($data['RECPR_REQUIRED']))
                    $data['RECPR_REQUIRED'] = 0;

                // Additional Submit Items
                //var_dump($submit_items);die();
                foreach ($submit_items as $key_id => $submit_item) {
                    if ($submit_item['TYPE'] == 'IMAGE') {
                        $si_images[$key_id] = $submit_item;
                    } elseif ($submit_item['TYPE'] == 'TAGS') {
                        $si_tags[$key_id] = $submit_item;
                    } elseif ($submit_item['TYPE'] == 'FILE') {
                        $si_files[$key_id] = $submit_item;
                    } elseif ($submit_item['TYPE'] == 'VIDEO') {
                        $si_videos[$key_id] = $submit_item;
                    } elseif ($submit_item['TYPE'] == 'MULTICHECKBOX') {
                        $items = $_REQUEST[$submit_item['FIELD_NAME']];
                        if (!empty($items) && is_array($items)) {
                            $data[$submit_item['FIELD_NAME']] = implode(',',$items);
                        }

                    } else {
                        $data[$submit_item['FIELD_NAME']] = $_REQUEST[$submit_item['FIELD_NAME']];
                    }
                }

                if (strlen(trim($data['URL'])) > 0 && !preg_match('#^http[s]?:\/\/#i', $data['URL']))
                    $data['URL'] = "http://" . $data['URL'];

                if (strlen(trim($data['RECPR_URL'])) > 0 && !preg_match('#^http[s]?:\/\/#i', $data['RECPR_URL']))
                    $data['RECPR_URL'] = "http://" . $data['RECPR_URL'];

                // Image Groups releated
                foreach ($submit_items as $key_id => $submit_item) {
                    if ($submit_item['TYPE'] == "IMAGEGROUP") {
                        $image_groups[] = $submit_item['FIELD_NAME'];
                    }
                }
                if ($image_groups) {
                    $image_groups_data = $db->GetRow("SELECT " . implode(', ', $image_groups) . " FROM `{$tables['link']['name']}` WHERE `ID` = '{$data['ID']}'");
                    foreach ($image_groups as $image_group_k => $image_group_v) {
                        $data[$image_group_v] = $image_groups_data[$image_group_v];
                    }
                }
                // End Image Groups releated

                if (isset($data['ID']) && !empty($data['ID'])) {
                    $data['CACHE_URL'] = $linkModel->seoUrl($data, $data['ID']);
                }



                if (isset($si_tags) && is_array($si_tags)) {
                    foreach ($si_tags as $field) {
                        $finalTags = array();
                        $tags = explode(',', $_POST[$field['FIELD_NAME']]);
                        if (is_string($tags)) {
                            $tags = array($tags);
                        }

                        $db->Execute('DELETE FROM '.$tables['tags_links']['name'].' WHERE LINK_ID = '.$id);
                        // sunmit items: tags
                        foreach ($tags as $tag) {
                            $idTag = $tagsModel->addTag($tag);
                            db_replace('tags_links', array('LINK_ID'=>$id, 'TAG_ID'=>$idTag), 'ID');
                            $finalTags[] = $idTag;
                        }

                        $data[$field['FIELD_NAME']] = implode(',', $finalTags);
                    }
                }

             
                    $coords = geocodeAddress($data['COUNTRY'], $data['STATE'], $data['CITY'], $data['ADDRESS'], $data['ZIP']);

                    $data['LAT'] = $coords['lat'];
                    $data['LON'] = $coords['lng'];
              

                if (($replaceResult = db_replace('link', $data, 'ID')) > 0) {
                    // If it was insert
                    if ($replaceResult == 2) {
                        $id = $db->Insert_ID();
                        $seoUrl = $linkModel->seoUrl($data, $id);
                        $db->execute('UPDATE PLD_LINK SET `CACHE_URL` = "'.$seoUrl.'" WHERE ID = '.$id);
                        $data['ID'] = $id;
                    }
                    if (Model_Link_Entity::TYPE_VIDEO == $linktypeid) {
                        $provider = $oembed->getProvider($data['URL']);

                        $args = array('width' => 640, 'height' => 390);
                        $photoData = $oembed->fetch($provider, $data['URL'], $args);
                        if ($photoData) {
                            $photoData->type = 'photo';
                            $photoData->width = 200;

                            //$ext = strtolower(end(explode('.', )));
							$ext1 = explode('.', $photoData->thumbnail_url);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $name = $data['ID'] . "_1." . $ext;
                            $path = str_replace('\\', '/', INSTALL_PATH);
                            if (file_exists($path . 'uploads/' . $name)) {
                                unlink($path . 'uploads/' . $name);
                            }
                            if (file_exists($path . '/uploads/thumb/' . $name)) {
                                unlink($path . '/uploads/thumb/' . $name);
                            }

                            
                            $content = file_get_contents($photoData->thumbnail_url);

                            $filename = $path . 'temp/templates/' . $name;
                            $result = @file_put_contents($filename, $content);
                            if ($result) {
                                resizeImg($filename, $path . 'uploads/' . $name, 400, 400);
                                resizeImg($filename, $path . 'uploads/thumb/' . $name, 200, 200);
                                $db->Execute("UPDATE `{$tables['link']['name']}` SET `IMAGE` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($data['ID']));
                                $data['IMAGE'] = $name;
                                unlink($filename);
                            } else {
                                print "Error saving file";
                            }
                        }
                    }

                    $tpl->assign('posted', true);

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
                    // End Additional Links section

                    //submit items: images
                    foreach ($si_images as $key_id => $image) {
                        //var_dump($_POST[$image['FIELD_NAME'].'_SUBMIT']);die();
                        if (!empty($_FILES[$image['FIELD_NAME']]['name'])) {
                            //$ext = strtolower(end(explode('.', )));
							$ext1 = explode('.', $_FILES[$image['FIELD_NAME']]['name']);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $name = $data['ID'] . "_" . $key_id . "." . $ext;

                            if (file_exists(INSTALL_PATH . '/uploads/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/' . $name);
                            }
                            if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/thumb/' . $name);
                            }
                            resizeImg($_FILES[$image['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/' . $name, 400, 400);
                            resizeImg($_FILES[$image['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/thumb/' . $name, 150, 150);

                            $db->Execute("UPDATE `{$tables['link']['name']}` SET `" . $image['FIELD_NAME'] . "` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($data['ID']));
                            $data[$image['FIELD_NAME']] = $name;
                        } elseif (
                                !empty($_POST[$image['FIELD_NAME'].'_SUBMIT'])
                                && url_is_image($_POST[$image['FIELD_NAME'].'_SUBMIT'])
                            ) {
                            $url = $_POST[$image['FIELD_NAME'].'_SUBMIT'];
                            $dir = INSTALL_PATH . 'uploads/';
                            
                            if (pathinfo($url,PATHINFO_EXTENSION))
                                $filePath = $dir . basename($url);
                            else { // url not always will containg the extenstion
                                $mime = getMimeFromUrl($url);
                                $ext = getImageExtensionByMime($mime);
                                $filePath = $dir.create_password(20).'.'.$ext; 
                            }
                            
                            //var_dump($filePath, $url);die();
                            $lfile = fopen($filePath, "w");

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
                            curl_setopt($ch, CURLOPT_FILE, $lfile);
                            curl_exec($ch);

                            fclose($lfile);
                            curl_close($ch);

                            $path = pathinfo($filePath);
//                            //var_dump($path, $filePath);die();
                            $name = $data['ID'] . "_" . $key_id . "." . $path['extension'];

                            resizeImg($filePath, INSTALL_PATH . 'uploads/' . $name, 400, 400);
                            resizeImg($filePath, INSTALL_PATH . 'uploads/thumb/' . $name, 150, 150);

                            $db->Execute("UPDATE `{$tables['link']['name']}` SET `" . $image['FIELD_NAME'] . "` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($data['ID']));
                            $data[$image['FIELD_NAME']] = $name;
                            //var_dump($data);die();
                        } else {

                            $data[$image['FIELD_NAME']] = $old_data[$image['FIELD_NAME']];
                            //var_dump($data, $old_data[$image['FIELD_NAME']]);die();
                        }
                    }

                    //end of submit items: images
                    ////submit items: files
                    foreach ($si_files as $key_id => $file) {
                        if (!empty($_FILES[$file['FIELD_NAME']]['name'])) {
                            $allowed = array("pdf", "xls", "xlsx", "doc", "docx", "zip", "rar", "txt", "rtf", "csv");
                           // $ext = strtolower(end(explode('.', )));
						   $ext1 = explode('.', $_FILES[$file['FIELD_NAME']]['name']);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $name = $data['ID'] . "_" . $key_id . "." . $ext;

                            if (file_exists(INSTALL_PATH . '/uploads/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/' . $name);
                            }
                            if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/thumb/' . $name);
                            }
                            if (in_array($ext, $allowed)) {
                                if (move_uploaded_file($_FILES[$file['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/' . $name)) {
                                    echo "[dsa]";
                                    $db->Execute("UPDATE `{$tables['link']['name']}` SET `" . $file['FIELD_NAME'] . "` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($data['ID']));
                                    $data[$file['FIELD_NAME']] = $name;
                                }
                            }
                        } else {
                            $data[$file['FIELD_NAME']] = $old_data[$file['FIELD_NAME']];
                        }
                    }
                    //end of submit items: files

                    foreach ($si_videos as $key => $file) {
                        if (!empty($_FILES[$file['FIELD_NAME']]['name'])) {
                            $allowed = array("avi", "wmv", "mov", "mpg");
                            //$ext = strtolower(end(explode('.', )));
							$ext1 = explode('.', $_FILES[$file['FIELD_NAME']]['name']);
								$ext = end($ext1);
								$ext = strtolower($ext);

                            $convertTo = 'flv';
                            $name = $data['ID'] . "_" . $key_id . "." . $ext;
                            $destName = $data['ID'] . "_" . $key_id . "." . $convertTo;

                            // Unlink existing file and thmb if exists
                            if (file_exists(INSTALL_PATH . '/uploads/' . $destName)) {
                                unlink(INSTALL_PATH . '/uploads/' . $destName);
                            }
                            if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $destName . '.jpg')) {
                                unlink(INSTALL_PATH . '/uploads/thumb/' . $destName . '.jpg');
                            }
                            if (in_array($ext, $allowed)) {
                                thumbnailVideo($_FILES[$file['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/thumb/' . $destName . '.jpg', '128x96', 1);
                                convertVideo($_FILES[$file['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/' . $destName, $convertTo, '704x576');
                                $db->Execute("UPDATE `{$tables['link']['name']}` SET `" . $file['FIELD_NAME'] . "` = " . $db->qstr($destName) . " WHERE `ID`=" . $db->qstr($data['ID']));
                                $data[$file['FIELD_NAME']] = $destName;
                            }
                        } else {
                            $data[$file['FIELD_NAME']] = $old_data[$file['FIELD_NAME']];
                        }
                    }

                    // Start Additional Categs section
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
                    // End Additional Categs section
                    // handle the submit item image groups.
                    foreach ($submit_items as $key_id => $submit_item) {
                        if ($submit_item['TYPE'] == "IMAGEGROUP") {
                            $imagegroupname = $submit_item['FIELD_NAME'];
                            $imagegroupid = $_REQUEST[$imagegroupname];
                            $groupdata = $_SESSION['imagegroups'][$imagegroupid];
                            $img_group = $db->GetOne("SELECT `{$imagegroupname}` FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($data['ID']));

                            if (!$img_group) {
                                //create a new group id for this link
                                $db->Execute("INSERT INTO `{$tables['imagegroup']['name']}` (DATE_MODIFIED, DATE_ADDED) VALUES (now(),now())");
                                $img_group =
                                $new_group_id = $new_group_id = $db->GetOne("SELECT MAX(`GROUPID`) FROM `{$tables['imagegroup']['name']}`");
                                $db->Execute("UPDATE `{$tables['link']['name']}` SET `" . $submit_item['FIELD_NAME'] . "` = " . $db->qstr($new_group_id) . " WHERE `ID`=" . $db->qstr($data['ID']));
                            }

                            foreach ($groupdata as $image) {
                                //$ext = strtolower(end(explode('.', $image)));
								$ext1 = explode('.', $image);
								$ext = end($ext1);
								$ext = strtolower($ext);
                                $name = $img_group . "_" . uniqid() . "." . $ext;
                                if (file_exists(INSTALL_PATH . '/uploads/' . $name)) {
                                    unlink(INSTALL_PATH . '/uploads/' . $name);
                                }
                                if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $name)) {
                                    unlink(INSTALL_PATH . '/uploads/thumb/' . $name);
                                }
                                error_log("saving $image to " . INSTALL_PATH . "uploads/" . $name);
                                resizeImg($image, INSTALL_PATH . 'uploads/' . $name, 400, 400);
                                resizeImg($image, INSTALL_PATH . 'uploads/thumb/' . $name, 150, 150);
                                $db->Execute("INSERT INTO `{$tables['imagegroupfile']['name']}` (GROUPID,IMAGE) VALUES (" . $db->qstr($img_group) . "," . $db->qstr($name) . ")");
                            }
                            $dir = INSTALL_PATH . 'uploads/tmp/';
                            unlinkRecursive($dir);
                        }
                    }
                    // end of image groups

                    if ($action == 'N') {
                        $cid = $data['CATEGORY_ID'];
                        $data = array();
                        $data['STATUS'] = 2;
                        $data['CATEGORY_ID'] = $cid;
                        $data['LINK_TYPE'] = $linktypeid;
                    } elseif ($action == 'E') {
                        if (isset($_REQUEST['semail'])) {
                            send_status_notificationse($id);
                        }
                        $data['LINK_TYPE'] = $linktypeid;
                    } else {
                        send_status_notifications($id);
                        if (isset($_SESSION['return']))
                            http_custom_redirect($_SESSION['return']);
                    }
                } else
                    $tpl->assign('sql_error', $db->ErrorMsg());
                @unlink($data['LINK_IMG']);
                @unlink($data['LINK_IMGTN']);
            }
        }
        $add_links = $db->GetAll("SELECT * FROM `{$tables['additional_link']['name']}` WHERE `LINK_ID` = '{$id}'");
        $add_categs = $db->GetAll("SELECT * FROM `{$tables['additional_category']['name']}` WHERE `LINK_ID` = '{$id}'");
        $tpl->assign("add_links", $add_links);
        $tpl->assign("add_categs", $add_categs);
        if ($action == 'N')
            $data['OWNER_EMAIL_CONFIRMED'] = true;
        $imagegroupname = null;
        $group_image_details = null;
        foreach ($submit_items as $key_id => $submit_item) {
            if ($submit_item['TYPE'] == "IMAGEGROUP") {
                $imagegroupname = $submit_item['FIELD_NAME'];
            }
        }
        if($imagegroupname!=null)
            $group_image_details = getLinkImages($data[$imagegroupname]);
        if ($submit_item['TYPE'] == 'TAGS') {
            $tpl->assign('allTags', $tagsModel->getAllTags());

        }

        $tpl->assign('group_image_details', $group_image_details);

        $tpl->assign("data", $data);
        $content = $tpl->fetch(ADMIN_TEMPLATE . '/dir_links_edit.tpl');
        break;
}


$tpl->assign('content', $content);

//Clean whitespace
//$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');
?>
