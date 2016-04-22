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
$categoryModel = new Model_Category();
if (empty($_REQUEST['submit']) && !empty($_SERVER['HTTP_REFERER']))
    $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

$tpl->assign('ENABLE_REWRITE', ENABLE_REWRITE);

$error = 0;
$errorMsg = '';

if ($_REQUEST['action']) {
    list ($action, $id) = explode(':', $_REQUEST['action']);

    $action = strtoupper(trim($action));
    $tpl->assign('action', strtoupper($action));
}

//RALUCA: JQuery validation related
$validators = array(
    'rules' => array(),
    'messages' => array(
        'TITLE_URL' => array(
            'remote' => _L("Title is not valid: most likely, not unique in parent category or contains problematic characters.")
        ),
        'TITLE' => array(
            'remote' => _L("Title is not valid: most likely, not unique in parent category.")
        ),
        'SYMBOLIC_ID' => array(
            'remote' => _L("ID not provided or not valid in parent category."),
            'min' => _L("Please select a category.")
        )
    )
);

if ($_REQUEST['s'] != '1') {
    $validators['rules']['TITLE'] = array(
        'required' => true,
        'minlength' => CAT_TITLE_MIN_LENGTH,
        'maxlength' => CAT_TITLE_MAX_LENGTH,
       
    );
    $validators['rules']['DESCRIPTION'] = array(
        'minlength' => CAT_DESCRIPTION_MIN_LENGTH,
        'maxlength' => CAT_DESCRIPTION_MAX_LENGTH
    );
    $validators['rules']['META_DESCRIPTION'] = array(
        'minlength' => META_DESCRIPTION_MIN_LENGTH,
        'maxlength' => META_DESCRIPTION_MAX_LENGTH
    );
//	$validators['rules']['META_KEYWORDS'] = array(
//		'minlength' => META_KEYWORDS_MIN_LENGTH,
//		'maxlength' => META_KEYWORDS_MAX_LENGTH
//	);
   
/*
if (ENABLE_REWRITE == 1) {
        $validators['rules']['TITLE_URL'] = array(
            'remote' => array(
                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                'type' => "post",
                'data' => array(
                    'action' => "isCategTitleUrl",
                    'table' => "category",
                    'field' => "TITLE_URL",
                    'id' => $id
                )
            )
        );
    }
} else {
    $validators['rules']['SYMBOLIC_ID'] = array(
        'min' => '1',
        'remote' => array(
            'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
            'type' => "post",
            'data' => array(
                'action' => "isSymbolicValid",
                'table' => "category",
                'field' => "SYMBOLIC_ID",
                'id' => $id
        ))
    );
	*/
}

$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related
//If editor, check if he/she is allowed to take an action on current category
if (!$_SESSION['phpld']['adminpanel']['is_admin']) {
//   if (!in_array ($id, $_SESSION['phpld']['adminpanel']['permission_array']))
    if (($_SESSION['phpld']['adminpanel']['rights']['addCat'] != 1 && $action == 'N')
            || ($_SESSION['phpld']['adminpanel']['rights']['editCat'] != 1 && ($action == 'E' || $action == 'M'))
            || ($_SESSION['phpld']['adminpanel']['rights']['delCat'] != 1 && ($action == 'D'))
            || !($_REQUEST['action'])) {
        //Editor is on unallowed page, block access
        http_custom_redirect("unauthorized.php");
        exit();
    }
}

$link_types[0] = 'Please select';
$link_types += $db->GetAssoc("SELECT `ID`, `NAME` FROM `{$tables['link_type']['name']}` WHERE `STATUS` = '2' ORDER BY `ORDER_ID` ASC");

$tpl->assign('link_types', $link_types);

//Correct value for ID
$id = (isset($id) ? intval($id) : 0);
$id = ($id < 0 ? 0 : $id);

$currentCategory = $db->GetRow("SELECT C.*, " . $db->IfNull('P.TITLE', "'Top'") . " AS `PARENT` FROM `{$tables['category']['name']}` AS `C` LEFT OUTER JOIN `{$tables['category']['name']}` AS `P` ON (C.PARENT_ID = P.ID) WHERE C.ID = " . $db->qstr($id) . " LIMIT 1");
$tpl->assign('currentCategory', $currentCategory);


$tpl->assign('stats', array(0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'),));
$tpl->assign('symbolic', $_REQUEST['s'] == '1');

switch ($action) {
    case 'C' :
        if (isset($_SESSION['return']))
            http_custom_redirect($_SESSION['return']);

        break;
    case 'A' :
        $ActionStatus = SetNewCategoryStatus($id, 2);
        $error = ($ActionStatus['status'] == 1 ? false : true);

        if ($ActionStatus['status'] != 1)
            $tpl->assign('sql_error', $ActionStatus['errorMsg']);

        if (!$error && isset($_SESSION['return']))
            http_custom_redirect($_SESSION['return']);
        break;
    case 'D' :
        $error = 0;

        if (isset($_REQUEST['cancel']) && !empty($_REQUEST['cancel'])) {
            //If canceled, redirect to category list
            http_custom_redirect(DOC_ROOT . '/dir_categs.php?r=1');
        } else {
            //Count links and subcategories of this category
            $count_links = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` = " . $db->qstr($id));
            $count_categs = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id));

            if ($count_links > 0 || $count_categs > 0) {
                //Get action
                $DO = (isset($_REQUEST['do']) ? strtolower(trim($_REQUEST['do'])) : 'move');

                if (isset($_REQUEST['delete']) && !empty($_REQUEST['delete'])) {
                    switch ($DO) {
                        //All subcategories, links and articles of this category are removed
                        case 'delete' :
                            if (!$db->Execute("DELETE FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` = " . $db->qstr($id))) {
                                $error++;
                                $tpl->assign('sql_error', $db->ErrorMsg());
                            }
                            if (!$db->Execute("DELETE FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id))) {
                                $error++;
                                $tpl->assign('sql_error', $db->ErrorMsg());
                            }
                            break;
                        //Subcategories and links are moved first to another category
                        case 'move' :
                            //Determine category where we should move everything
                            $newCategID = (isset($_REQUEST['CATEGORY_ID']) ? intval($_REQUEST['CATEGORY_ID']) : '-1');

                            //Check if removing category is not the same as where everything has to be moved
                            if ($newCategID >= 0 && $newCategID != $id) {
                                //Move links
                                if (!$db->Execute("UPDATE `{$tables['link']['name']}` SET `CATEGORY_ID` = " . $db->qstr($newCategID) . " WHERE `CATEGORY_ID` = " . $db->qstr($id))) {
                                    $error++;
                                    $tpl->assign('sql_error', $db->ErrorMsg());
                                }
                                //Move subcategories
                                if (!$db->Execute("UPDATE `{$tables['category']['name']}` SET `PARENT_ID` = " . $db->qstr($newCategID) . " WHERE `PARENT_ID` = " . $db->qstr($id))) {
                                    $error++;
                                    $tpl->assign('sql_error', $db->ErrorMsg());
                                }
                            } else {
                                $error++;
                                $tpl->assign('errorMsg', _L('Invalid or same category selected!'));
                            }

                            break;

                        default :
                            break;
                    }

                    if ($error == 0) {
                        $count_links = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` = " . $db->qstr($id));
                        $count_categs = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id));
                    }
                }
            }

            if ($count_links == 0 && $count_categs == 0 && $error == 0) {
                $rm = $db->Execute("DELETE FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($id));
                if ($rm) {
                    http_custom_redirect(DOC_ROOT . '/dir_categs.php?r=1');
                } else {
                    $error++;
                    $tpl->assign('sql_error', $db->ErrorMsg());
                }
            }
        }

        if (AJAX_CAT_SELECTION_METHOD == 0) {
            $categs = get_categs_tree();
            $tpl->assign('categs', $categs);
        }

        $tpl->assign('id', $id);
        $tpl->assign('error', $error);
        $tpl->assign('do', $DO);
        $tpl->assign('count_links', $count_links);
        $tpl->assign('count_categs', $count_categs);
        break;

    case 'M' :
        $quickAdded = 0;
        $multicategs = (isset($_POST['multicategs']) ? clean_str_white_space($_POST['multicategs']) : '');

        if (!empty($_REQUEST['submit']) && !empty($multicategs)) {
            $multiCategsArray = explode("\n", $multicategs);

            if (is_array($multiCategsArray) && !empty($multiCategsArray)) {
                $currDate = gmdate('Y-m-d H:i:s');

                //Loop through each category
                foreach ($multiCategsArray as $key => $singleCateg) {
                    //Clean each category title
                    $singleCateg = $multiCategsArray[$key] = strip_white_space($singleCateg);
                    $checkSQL = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `TITLE` LIKE " . $db->qstr($singleCateg) . " AND `PARENT_ID` = " . $db->qstr($id);

                    if (!empty($singleCateg) && $db->GetOne($checkSQL) < 1) {
                        $data = array();

                        //Generate ID
                        $data['ID'] = intval($db->GenID($tables['category']['name'] . '_SEQ'));

                        //Add parent ID
                        $data['PARENT_ID'] = $id;

                        $data['TITLE'] = $singleCateg;

                     //Build URL
					 if(empty($data['TITLE_URL'])){
							$data['TITLE_URL'] = preg_replace('/\s/', '_', $data['TITLE']);
						   }
						   else{
							$data['TITLE_URL'] = preg_replace('/\s/', '_', $data['TITLE_URL']);
						    $data['TITLE_URL'] = str_replace('__', '_', $data['TITLE_URL']);
						   }


                        //Build cache

                        $data['CACHE_TITLE'] = trim(buildCategUrlTitle($data['ID']));
                        $data['CACHE_URL'] = $categoryModel->seoUrl($data['ID']);//trim(buildCategUrl($data['ID']));

                        //Add current date
                        $data['DATE_ADDED'] = $currDate;

                        //Make category active
                        $data['STATUS'] = 2;

                        if (!$db->Replace($tables['category']['name'], $data, 'ID', true) > 0) {
                            $error++;
                            $tpl->assign('sql_error', $db->ErrorMsg());
                        } else {
                            $quickAdded++;
                            unset($multiCategsArray[$key]);
                        }
                    } else {
                        unset($multiCategsArray[$key]);
                    }
                }

                //Refresh editor permissions
                if (!$_SESSION['phpld']['adminpanel']['is_admin']) {
                    $user_permission = "";
                    $user_grant_permission = "";
                    $user_permission_array = array();
                    $user_grant_permission_array = array();

                    get_editor_permission($_SESSION['phpld']['adminpanel']['id']);
                    $_SESSION['phpld']['adminpanel']['permission'] = $user_permission;
                    $_SESSION['phpld']['adminpanel']['grant_permission'] = $user_grant_permission;
                    $_SESSION['phpld']['adminpanel']['permission_array'] = $user_permission_array;
                    $_SESSION['phpld']['adminpanel']['grant_permission_array'] = $user_grant_permission_array;
                }

                $multicategs = implode("\n", $multiCategsArray);
            }
        }

        $tpl->assign('multicategs', $multicategs);
        $tpl->assign('quickAdded', $quickAdded);
    case 'E' :
        if (empty($_REQUEST['submit']))
            $data = $db->GetRow("SELECT * FROM `{$tables['category']['name']}` WHERE `ID` = " . $db->qstr($id));

        $data['LINK_TYPES'] = get_categ_link_types($id);

    case 'N' :
    default :
        $SORT_ORDER = '1000';
        $tpl->assign('SORT_ORDER', $SORT_ORDER);
        //Determine length of description field
        $DescriptionLimit = (isset($data['DESCRIPTION']) && strlen(trim($data['DESCRIPTION'])) > 0 ? CAT_DESCRIPTION_MAX_LENGTH - strlen(trim($data['DESCRIPTION'])) : CAT_DESCRIPTION_MAX_LENGTH);
        $tpl->assign('DescriptionLimit', $DescriptionLimit);
        //Determine length of meta description field
        $MetaDescriptionLimit = (isset($data['META_DESCRIPTION']) && strlen(trim($data['META_DESCRIPTION'])) > 0 ? META_DESCRIPTION_MAX_LENGTH - strlen(trim($data['META_DESCRIPTION'])) : META_DESCRIPTION_MAX_LENGTH);
        $tpl->assign('MetaDescriptionLimit', $MetaDescriptionLimit);

        if (empty($_REQUEST['submit'])) {
            if ($action == 'N') {
                $data = array();
                $data['STATUS'] = 2;
            }
            $tpl->assign('submit_session', registerAdminSubmitSession());
        } else {
            checkAdminSubmitSession(clean_string($_POST['submit_session']));
            $tpl->assign('submit_session', registerAdminSubmitSession());
            $data = get_table_data('category');

            $data['SYMBOLIC'] = (isset($_REQUEST['s']) && $_REQUEST['s'] == 1 ? 1 : 0);

		if(empty($data['TITLE_URL'])){
				$data['TITLE_URL'] = preg_replace('/\s/', '_', $data['TITLE']);
			   }
			   else{
				$data['TITLE_URL'] = preg_replace('/\s/', '_', $data['TITLE_URL']);				 
			    $data['TITLE_URL'] = str_replace('__', '_', $data['TITLE_URL']);
			   }



            //If editor, check if he/she is allowed to take an action on current category
            if (!$_SESSION['phpld']['adminpanel']['is_admin'] &&
                    !empty($data['PARENT_ID']) &&
                    (($_SESSION['phpld']['adminpanel']['rights']['addCat'] != 1 && $action == 'N') || ($_SESSION['phpld']['adminpanel']['rights']['editCat'] != 1 && ($action == 'E' || $action == 'M')))
            ) {
                //Editor is on unallowed page, block access
                http_custom_redirect("unauthorized.php");
                exit();
            }

            //RALUCA: JQuery validation related - server side.
            $validator = new Validator($validators);
            $validator_res = $validator->validate($_POST);
            //RALUCA: end of JQuery validation related - server side.


            if (empty($validator_res)) {
                if ($action == 'N')
                    $data['DATE_ADDED'] = gmdate('Y-m-d H:i:s');

                if (empty($id))
                    $id = $db->GenID($tables['category']['name'] . '_SEQ');

                $data['ID'] = intval($id);

                $data['CACHE_TITLE'] = trim(buildCategUrlTitle($id));
                $data['CACHE_URL'] = $categoryModel->seoUrl($id);
                if ($data['COLS'] == '') {
                    $data['COLS'] = null;
                    $cache_data['COLS'] = null;
                }
                if ($db->Replace($tables['category']['name'], $data, 'ID', true) > 0) {
                    saveRSS_feeds($data['ID']);
                    //Build CACHE
                    $cache_data = array();
                    $cache_data['ID'] = $data['ID'];

                    $cache_data['CACHE_TITLE'] = trim(buildCategUrlTitle($id));
                    $db->Replace($tables['category']['name'], $cache_data, 'ID', true);

                    $cache_data = array();
                    $cache_data['ID'] = $data['ID'];
                    $cache_data['CACHE_URL'] = $categoryModel->seoUrl($id);
                    $db->Replace($tables['category']['name'], $cache_data, 'ID', true);

                    // Start Additional Categs section
                    $db->Execute("DELETE FROM `{$tables['category_link_type']['name']}` WHERE `CATEGORY_ID` = '{$data['ID']}'");

                    if (isset($_REQUEST['LINK_TYPES']) && is_array($_REQUEST['LINK_TYPES'])) {
                        for ($i = 0; $i < count($_REQUEST['LINK_TYPES']); $i++) {
                            $add_cat_id = trim($_REQUEST['LINK_TYPES'][$i]);
                            if (!empty($add_cat_id) && ($add_cat_id > 0)) {
                                $add_cat_data['LINK_TYPE'] = $add_cat_id;
                                $add_cat_data['CATEGORY_ID'] = $data['ID'];
                                db_replace('category_link_type', $add_cat_data);
                                /* if ($db->ErrorMsg())
                                  var_dump($db->ErrorMsg()); */
                            }
                        }
                    }
                    // End Additional Categs section

                    if (ENABLE_REWRITE) {
                        //Re-build cache for subcategories,
                        //only if URL rewriting is enabled
                        $subcategs = get_sub_categories($data['ID']);
                        if (is_array($subcategs) && !empty($subcategs)) {
                            //Loop through each subcategory
                            foreach ($subcategs as $sKey => $subcatID) {
                                //Build cache
                                $subcat_cache_data = array();
                                $subcat_cache_data['ID'] = $subcatID;
                                $subcat_cache_data['CACHE_TITLE'] = trim(buildCategUrlTitle($subcatID));
                                $subcat_cache_data['CACHE_URL'] = $categoryModel->seoUrl($subcat_cache_data['TITLE'], $subcatID);

                                //Write new cache
                                $db->Replace($tables['category']['name'], $subcat_cache_data, 'ID', true);

                                //Free memory
                                unset($subcat_cache_data[$sKey], $subcatID);
                            }
                        }
                        unset($subcategs);
                    }
                    unset($cache_data);

                    // Refresh editor permissions
                    if (!$_SESSION['phpld']['adminpanel']['is_admin']) {
                        $user_permission = "";
                        $user_grant_permission = "";
                        $user_permission_array = array();
                        $user_grant_permission_array = array();

                        get_editor_permission($_SESSION['phpld']['adminpanel']['id']);
                        $_SESSION['phpld']['adminpanel']['permission'] = $user_permission;
                        $_SESSION['phpld']['adminpanel']['grant_permission'] = $user_grant_permission;
                        $_SESSION['phpld']['adminpanel']['permission_array'] = $user_permission_array;
                        $_SESSION['phpld']['adminpanel']['grant_permission_array'] = $user_grant_permission_array;
                    }

                    $tpl->assign('posted', true);
                    if ($action == 'N') {
                        $oldStatus = $data['STATUS'];
                        $data = array();
                        $data['STATUS'] = $oldStatus;
                        unset($oldStatus);
                    } else
                    if (isset($_SESSION['return']))
                        http_custom_redirect($_SESSION['return']);
                }
                else
                    $tpl->assign('sql_error', $db->ErrorMsg());
            } else {
                $tpl->assign('errorMsg', _L('Validation error: Please check your input data and try again.'));
            }
        }

        if (AJAX_CAT_SELECTION_METHOD == 0 || $_REQUEST['s'] == 1) {
            if ($action == 'N') {
                $categs = get_categs_tree();
            } else {
                $categs = get_categs_tree_backend(0, intval($id));
            }
            $tpl->assign('categs', $categs);
        }
        break;
}

$tpl->assign('error', $error);
$tpl->assign('errorMsg', $errorMsg);

$tpl->assign($data);

$content = $tpl->fetch(ADMIN_TEMPLATE . '/dir_categs_edit.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');
?>
