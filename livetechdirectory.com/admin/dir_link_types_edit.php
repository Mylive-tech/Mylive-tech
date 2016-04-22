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

$error = 0;

if (empty($_REQUEST['submit']) && !empty($_SERVER['HTTP_REFERER'])) {
    $_SESSION['return'] = $_SERVER['HTTP_REFERER'];
}

if (isset($_SESSION['wid_message'])) {
    $tpl->assign('wid_message', $_SESSION['wid_message']);
    unset($_SESSION['wid_message']);
}

if (isset($_SESSION['wid_error'])) {
    $tpl->assign('wid_error', $_SESSION['wid_error']);
    unset($_SESSION['wid_error']);
}

$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval(PAGER_GROUPINGS) : 20);
$LinksPerPage = (LINKS_PER_PAGE && LINKS_PER_PAGE > 0 ? intval(LINKS_PER_PAGE) : 10);

$where = '';

$columns = array('NAME' => _L('Name'), 'STATUS' => _L('Status'), 'ACTION' => _L('Action'), 'ORDER_ID' => _L('Quick Move'));
$tpl->assign('columns', $columns);

$tpl->assign('col_count', count($columns));

if ($_REQUEST['action']) {
    list ($action, $id) = explode(':', $_REQUEST['action']);

    $action = strtoupper(trim($action));
    $tpl->assign('action', strtoupper($action));
}

//Correct value for ID
$id = (isset($id) ? intval($id) : 0);
$id = ($id < 0 ? 0 : $id);
$tpl->assign('id', $id);

$tpl->assign('stats', array_reverse(array(0 => _L('Inactive'), 2 => _L('Active')), true));

$currentTemplate = $db->GetOne("SELECT `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` = 'TEMPLATE'");
$commonTemplatePath = INSTALL_PATH . 'templates/Core/DefaultFrontend/views/_listings';
$templatePath = INSTALL_PATH . 'templates/' . $currentTemplate . '/views/_listings';


// Common template
$commonLists = array('' => 'Please select...');
if ($handle = opendir($commonTemplatePath . '/list')) {
    while (false !== ($entry = readdir($handle))) {
        $info = pathinfo($entry);
        if ($entry != "." && $entry != ".." && $info['extension'] == 'tpl') {
            $commonLists[$entry] = _L($entry);
        }
    }
    closedir($handle);
}

$tpl->assign('lists', $commonLists);

$commonDetails = array('' => 'Please select...');
if ($handle = opendir($commonTemplatePath . '/details')) {
    while (false !== ($entry = readdir($handle))) {
        $info = pathinfo($entry);
        if ($entry != "." && $entry != ".." && $info['extension'] == 'tpl') {
            $commonDetails[$entry] = _L($entry);
        }
    }
    closedir($handle);
}

// Template specific
$lists = array('' => 'Please select...');
if ($handle = opendir($templatePath . '/list')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $lists[$entry] = _L($entry);
        }
    }
    closedir($handle);
}

$details = array('' => 'Please select...');
if ($handle = opendir($templatePath . '/details')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $details[$entry] = _L($entry);
        }
    }
    closedir($handle);
}

$tpl->assign('lists', array_merge($commonLists, $lists));
$tpl->assign('details', array_merge($commonDetails, $details));
$tpl->assign('payment_um', array_reverse($payment_um, true));

switch ($action) {
    case 'R':
        $ltype = $db->GetRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$id}' AND `CORE_LINK`=0");
        if ($ltype['ID']) {
            $db->Execute("DELETE FROM `{$tables['submit_item_status']['name']}` WHERE `LINK_TYPE_ID` = '{$id}'");
            $db->Execute("DELETE FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$id}'");
            $db->Execute("DELETE FROM `{$tables['additional_category']['name']}`
            WHERE `TYPE` = '1' AND `LINK_ID` IN (SELECT `ID` FROM `{$tables['link']['name']}` WHERE `LINK_TYPE` = " . $db->qstr($id) . ")");
            $db->Execute("DELETE FROM `{$tables['comment']['name']}`
            WHERE `TYPE` = '1' AND `ITEM_ID` IN (SELECT `ID` FROM `{$tables['link']['name']}` WHERE `LINK_TYPE` = " . $db->qstr($id) . ")");
            $db->Execute("DELETE FROM  FROM `{$tables['link']['name']}` WHERE `LINK_TYPE` = " . $db->qstr($id));
            $db->Execute("DELETE FROM  FROM `{$tables['link_review']['name']}` WHERE `LINK_TYPE` = " . $db->qstr($id));
            $db->Execute("UPDATE `{$tables['link_type']['name']}` SET `ORDER_ID` = `ORDER_ID`-1 WHERE `ORDER_ID` > " . $ltype['ORDER_ID']);
        }
    case 'S':
        list ($t, $t, $status) = explode(':', $_REQUEST['action']);
        $db->Execute("UPDATE `{$tables['link_type']['name']}` SET `STATUS` = '{$status}' WHERE `ID` = '{$id}'");
        http_custom_redirect(DOC_ROOT . '/dir_link_types.php');
        break;
    case 'U':
        $old_pos = $db->GetOne("SELECT `ORDER_ID` FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$id}'");
        $upper = $db->GetOne("SELECT MAX(`ORDER_ID`) FROM `{$tables['link_type']['name']}` WHERE `ORDER_ID`<" . $db->qstr($old_pos));
        if ($upper !== null) {
            $db->Execute("UPDATE `{$tables['link_type']['name']}` SET `ORDER_ID` = '{$old_pos}' WHERE `ORDER_ID` = '{$upper}'");
            $db->Execute("UPDATE `{$tables['link_type']['name']}` SET `ORDER_ID` = '{$upper}'   WHERE `ID` = '{$id}'");
        }
        http_custom_redirect(DOC_ROOT . '/dir_link_types.php');
        break;
    case 'D':
        $old_pos = $db->GetOne("SELECT `ORDER_ID` FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$id}'");
        $lower = $db->GetOne("SELECT MIN(`ORDER_ID`) FROM `{$tables['link_type']['name']}` WHERE `ORDER_ID`>" . $db->qstr($old_pos));
        if ($lower !== null) {
            $db->Execute("UPDATE `{$tables['link_type']['name']}` SET `ORDER_ID` = '{$old_pos}' WHERE `ORDER_ID` = '{$lower}'");
            $db->Execute("UPDATE `{$tables['link_type']['name']}` SET `ORDER_ID` = '{$lower}' WHERE `ID` = '{$id}'");
        }
        http_custom_redirect(DOC_ROOT . '/dir_link_types.php');
        break;
    case 'E':
        if (empty($_REQUEST['submit']))
            $data = $db->GetRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE `ID` = " . $db->qstr($id));

    case 'N':
    default :
        //RALUCA: JQuery validation related
        $validators = array(
            'rules' => array(
                'NAME' => array(
                    'required' => true,
                   
                ),
                'PRICE' => array(
                    'remote' => array(
                        'url' => "../include/validation_functions.php",
                        'type' => "post",
                        'data' => array(
                            'action' => "isPaypalSet",
                            'table' => "link_type",
                            'field' => "PRICE"
                        )
                    )
                ),
                'DESCRIPTION' => array(
                    'minlength' => DESCRIPTION_MIN_LENGTH,
                    'maxlength' => DESCRIPTION_MAX_LENGTH
                )
            ),
            'messages' => array(
                'NAME' => array(
                    'remote' => _L("Name is not valid: most likely, not unique.")
                ),
                'PRICE' => array(
                    'remote' => _L("Your PAYPAL ACCOUNT has not been filled in. Please set your PAYPAL ACCOUNT ") . "<a href='" . $DOC_ROOT . "/conf_settings.php?c=9&r=1'>" . _L("here") . "</a>."
                )
            )
        );

        $vld = json_custom_encode($validators);
        $tpl->assign('validators', $vld);

        $validator = new Validator($validators);
        //RALUCA: end of JQuery validation related

        if (empty($_POST['submit'])) {
            $tpl->assign('submit_session', registerAdminSubmitSession());
        } else {
            checkAdminSubmitSession(clean_string($_POST['submit_session']));
            $tpl->assign('submit_session', registerAdminSubmitSession());
            $data = get_table_data('link_type');

            //RALUCA: JQuery validation related - server side.
            $validator = new Validator($validators);
            $validator_res = $validator->validate($_POST);
            //RALUCA: end of JQuery validation related - server side.

            if (empty($validator_res)) {
                $data['ID'] = $id;
                $data['NAME'] = $_REQUEST['NAME'];
                $data['DESCRIPTION'] = $_REQUEST['DESCRIPTION'];
                $data['STATUS'] = $_REQUEST['STATUS'];
                $data['FEATURED'] = $_REQUEST['FEATURED'];
                $data['PAGERANK_MIN'] = $_REQUEST['PAGERANK_MIN'];
                $data['REQUIRE_APPROVAL'] = $_REQUEST['REQUIRE_APPROVAL'];
                //$data['PRICE']               = $_REQUEST['PRICE'];
                $data['PAY_UM'] = $_REQUEST['PAY_UM'];
                $data['NOFOLLOW'] = $_REQUEST['NOFOLLOW'];
                $data['SHOW_META'] = $_REQUEST['SHOW_META'];
                $data['DEFAULT_THUMBNAIL_GRID'] = $_REQUEST['THUMBNAIL_GRID'];
                $data['DEFAULT_THUMBNAIL_LIST'] = $_REQUEST['THUMBNAIL_LIST'];
                $data['COUNT_IMAGES'] = intval($_REQUEST['COUNT_IMAGES']);
                $pattern = array('!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '-');
                $data['PRICE'] = str_replace($pattern, '', $_REQUEST['PRICE']);

				/**
				 * <uncomment by="ALEXANDRU PISARENCO"
				 * reason="Providing null strings as integers failed the update operation">
				 */
				if (empty($_REQUEST['MULTIPLE_CATEGORIES'])) {
					unset($data['MULTIPLE_CATEGORIES']);
				}
				if (empty($_REQUEST['DEEP_LINKS'])) {
					unset($data['DEEP_LINKS']);
				}
				/**
				 * </uncomment>
				 */

				if ($action == 'N') {
                    $last_order_id = $db->GetOne("SELECT MAX(ORDER_ID) FROM `{$tables['link_type']['name']}`");
                    $data['ORDER_ID'] = $last_order_id + 1;
                }

                /*
                 * albert:
                 * image temporarly removed
                 * should create the thumb somehow nicer, at least make a function for this - here, just call the function
                 * 
                  // Link Type Image handling
                  if (!empty($_FILES['IMG']['name'])) {
                  $img = $_FILES['IMG']['name'];
                  $ext = substr($img, strrpos($img, '.'));
                  //make sure this directory is writable!
                  //the new width of the resized image, in pixels.
                  $img_thumb_width_small = 50; // thumb max width
                  $img_thumb_width = 200; // image max width
                  $imgname = $id;

                  // create thumb
                  $file_type = $_FILES['IMG']['type'];
                  $file_name = $_FILES['IMG']['name'];
                  $file_size = $_FILES['IMG']['size'];
                  $file_tmp = $_FILES['IMG']['tmp_name'];
                  $ThumbWidth = $img_thumb_width;
                  $ThumbWidthSmall = $img_thumb_width_small;

                  $getExt = explode ('.', $file_name);
                  $file_ext = $getExt[count($getExt)-1];

                  if ($file_size) {
                  if ($file_type == "image/pjpeg" || $file_type == "image/jpeg") {
                  $new_img = imagecreatefromjpeg($file_tmp);
                  $new_img2 = imagecreatefromjpeg($file_tmp);
                  }elseif ($file_type == "image/x-png" || $file_type == "image/png") {
                  $new_img = imagecreatefrompng($file_tmp);
                  $new_img2 = imagecreatefrompng($file_tmp);
                  } elseif($file_type == "image/gif") {
                  $new_img = imagecreatefromgif($file_tmp);
                  $new_img2 = imagecreatefromgif($file_tmp);
                  }

                  //list the width and height and keep the height ratio.
                  list($width, $height) = getimagesize($file_tmp);
                  //calculate the image ratio
                  $imgratio=$width/$height;
                  if ($imgratio>1){
                  $newwidth = $ThumbWidth;
                  $newheight = $ThumbWidth/$imgratio;
                  $newwidth2 = $ThumbWidthSmall;
                  $newheight2 = $ThumbWidthSmall/$imgratio;
                  }else{
                  $newheight = $ThumbWidth;
                  $newwidth = $ThumbWidth*$imgratio;
                  $newwidth2 = $ThumbWidthSmall;
                  $newheight2 = $ThumbWidthSmall/$imgratio;
                  }

                  //function for resize image.
                  if (function_exists(imagecreatetruecolor)){
                  $resized_img = imagecreatetruecolor($newwidth,$newheight);
                  $resized_img2 = imagecreatetruecolor($newwidth2,$newheight2);
                  }else{
                  die("Error: Please make sure you have GD library ver 2+");
                  }

                  //the resizing is going on here!
                  imagealphablending($resized_img, false);
                  imagecopyresized($resized_img, $new_img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                  imagealphablending($resized_img2, false);
                  imagecopyresized($resized_img2, $new_img2, 0, 0, 0, 0, $newwidth2, $newheight2, $width, $height);
                  //finally, save the images
                  if (file_exists(INSTALL_PATH.'/images/link_type/'.$imgname.'.'.$file_ext)) {
                  unlink(INSTALL_PATH.'/images/link_type/'.$imgname.'.'.$file_ext);
                  unlink(INSTALL_PATH.'/images/link_type/thumb/'.$imgname.'.'.$file_ext);
                  }

                  echo INSTALL_PATH.'/images/link_type/'.$imgname.'.'.$file_ext;

                  if($file_type == "image/pjpeg" || $file_type == "image/jpeg"){
                  ImageJpeg($resized_img, INSTALL_PATH.'/images/link_type/'.$imgname.'.'.$file_ext);
                  ImageJpeg($resized_img2, INSTALL_PATH.'/images/link_type/thumb/'.$imgname.'.'.$file_ext);
                  }elseif($file_type == "image/x-png" || $file_type == "image/png"){
                  imagesavealpha($resized_img, true);
                  imagesavealpha($resized_img2, true);
                  ImagePng($resized_img, INSTALL_PATH.'/images/link_type/'.$imgname.'.'.$file_ext);
                  ImagePng($resized_img2, INSTALL_PATH.'/images/link_type/thumb/'.$imgname.'.'.$file_ext);
                  }elseif($file_type == "image/gif"){
                  ImageGIf($resized_img, INSTALL_PATH.'/images/link_type/'.$imgname.'.'.$file_ext);
                  ImageGIf($resized_img2, INSTALL_PATH.'/images/link_type/thumb/'.$imgname.'.'.$file_ext);
                  }

                  ImageDestroy ($resized_img);
                  ImageDestroy ($new_img);
                  ImageDestroy ($resized_img2);
                  ImageDestroy ($new_img2);
                  }
                  $data['IMG'] = SITE_URL.'images/link_type/'.$imgname.'.'.$file_ext;
                  $data['IMGTN'] = SITE_URL.'images/link_type/thumb/'.$imgname.'.'.$file_ext;
                  }
                 */
                if (db_replace('link_type', $data, 'ID') > 0) {
                    
                    
                    if ($action == 'N') {
                        $last_id = $db->GetOne("SELECT MAX(ID) FROM `{$tables['link_type']['name']}`");
                        $regular_items = $db->GetAll("SELECT ID, STATUS FROM `{$tables['submit_item']['name']}`");                        
                        foreach ($regular_items as $item) {
                            $item_data['ITEM_ID'] = $item['ID'];
                            $item_data['LINK_TYPE_ID'] = $last_id;
                            //$item_data['STATUS'] = $item['STATUS'];
                            $item_data['STATUS'] = 2; //active
//         				db_replace('submit_item_status', $item_data, 'ID');
                           $db->Execute("INSERT INTO `{$tables['submit_item_status']['name']}` (`ITEM_ID`, `LINK_TYPE_ID`, `STATUS`) 
                                            VALUES (" . $db->qstr($item_data['ITEM_ID']) . "," . $db->qstr($item_data['LINK_TYPE_ID']) . "," . $db->qstr($item_data['STATUS']) . ")");
                        }
                    } else {
                        //mass update on link type editing
                        //featured
                        $db->Execute("UPDATE `{$tables['link']['name']}` SET `FEATURED` = " . $db->qstr($data['FEATURED']) . " WHERE `LINK_TYPE` = " . $db->qstr($data['ID']));
                        $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `FEATURED` = " . $db->qstr($data['FEATURED']) . " WHERE `LINK_TYPE` = " . $db->qstr($data['ID']));
                        //nofollow
                        $db->Execute("UPDATE `{$tables['link']['name']}` SET `NOFOLLOW` = " . $db->qstr($data['NOFOLLOW']) . " WHERE `LINK_TYPE` = " . $db->qstr($data['ID']));
                        $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `NOFOLLOW` = " . $db->qstr($data['NOFOLLOW']) . " WHERE `LINK_TYPE` = " . $db->qstr($data['ID']));
                        //end of mass update
                        //deleting the additional links/categories over the allowed number of additional links/categories per link type

                        $aux_cats = $db->GetAssoc("SELECT c.LINK_ID, COUNT(c.LINK_ID) AS LINKS FROM `{$tables['additional_category']['name']}` c,
                                `{$tables['link']['name']}` l
                                WHERE c.LINK_ID = l.ID
                                AND l.LINK_TYPE = " . $db->qstr($data['ID']) . "
                                GROUP BY c.LINK_ID");

                        $aux_cats_reviewed = $db->GetAssoc("SELECT c.LINK_ID, COUNT(c.LINK_ID) AS LINKS FROM `{$tables['additional_category_review']['name']}` c,
                                `{$tables['link']['name']}` l
                                WHERE c.LINK_ID = l.ID
                                AND l.LINK_TYPE = " . $db->qstr($data['ID']) . "
                                GROUP BY c.LINK_ID");

                        $aux_links = $db->GetAssoc("SELECT c.LINK_ID, COUNT(c.LINK_ID) AS LINKS FROM `{$tables['additional_link']['name']}` c,
                                `{$tables['link']['name']}` l
                                WHERE c.LINK_ID = l.ID
                                AND l.LINK_TYPE = " . $db->qstr($data['ID']) . "
                                GROUP BY c.LINK_ID");

                        $aux_links_reviewed = $db->GetAssoc("SELECT c.LINK_ID, COUNT(c.LINK_ID) AS LINKS FROM `{$tables['additional_link_review']['name']}` c,
                                `{$tables['link']['name']}` l
                                WHERE c.LINK_ID = l.ID
                                AND l.LINK_TYPE = " . $db->qstr($data['ID']) . "
                                GROUP BY c.LINK_ID");
                        $i = 0;
                        $sql = '';
                        foreach ($aux_links as $k => $v) {
                            if ($v > $data['DEEP_LINKS']) {
                                $diff = $v - $data['DEEP_LINKS'];
                                $sql .= " DELETE FROM `{$tables['additional_link']['name']}` WHERE `LINK_ID` =" . $db->qstr($k) . " LIMIT " . $diff . ";";
                                $i++;
                            }
                            if ($i == 50) {
                                $db->Execute($sql);
                                $i = 0;
                                $sql = '';
                            }
                        }
                        $i = 0;
                        $sql = '';
                        foreach ($aux_cats as $k => $v) {
                            if ($v > $data['MULTIPLE_CATEGORIES'] - 1) {
                                $diff = $v - $data['MULTIPLE_CATEGORIES'] + 1;
                                $sql .= " DELETE FROM `{$tables['additional_category']['name']}` WHERE `LINK_ID` =" . $db->qstr($k) . " LIMIT " . $diff . ";";
                                $i++;
                            }
                            if ($i == 50) {
                                $db->Execute($sql);
                                $i = 0;
                                $sql = '';
                            }
                        }
                        $i = 0;
                        $sql = '';
                        foreach ($aux_links_reviewed as $k => $v) {
                            if ($v > $data['DEEP_LINKS']) {
                                $diff = $v - $data['DEEP_LINKS'];
                                $sql .= " DELETE FROM `{$tables['additional_link_review']['name']}` WHERE `LINK_ID` =" . $db->qstr($k) . " LIMIT " . $diff . ";";
                                $i++;
                            }
                            if ($i == 50) {
                                $db->Execute($sql);
                                $i = 0;
                                $sql = '';
                            }
                        }
                        $i = 0;
                        $sql = '';
                        foreach ($aux_cats_reviewed as $k => $v) {
                            if ($v > $data['MULTIPLE_CATEGORIES'] - 1) {
                                $diff = $v - $data['MULTIPLE_CATEGORIES'] + 1;
                                $sql .= " DELETE FROM `{$tables['additional_category_review']['name']}` WHERE `LINK_ID` =" . $db->qstr($k) . " LIMIT " . $diff . ";";
                                $i++;
                            }
                            if ($i == 50) {
                                $db->Execute($sql);
                                $i = 0;
                                $sql = '';
                            }
                        }
                    }
                    $tpl->assign("posted", true);
                }
            }
            if (empty($error)) {
                http_custom_redirect(DOC_ROOT . '/dir_link_types.php');
            }
        }

        $tpl->assign($data);

        break;
}

//$tpl->assign($_POST);

$content = $tpl->fetch(ADMIN_TEMPLATE . '/dir_link_types_edit.tpl');

$tpl->assign('error', $error);

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');
?>
