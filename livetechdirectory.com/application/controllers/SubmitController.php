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
class SubmitController extends PhpldfrontController {

    public function indexAction() {
		global $client_info;
        $this->_breadcrumbs->add(_L('Submit Link'));
        $db = Phpld_Db::getInstance()->getAdapter();
        $oembed = Model_Link_Handler_Oembed::getInstance();
        $tables = Phpld_Db::getInstance()->getTables();
        $currentUser = Model_CurrentUser::getInstance()->loadData();
		Phpld_View::addJavascript(DOC_ROOT.'/javascripts/formtool/formtool.js');
        $tagsModel = new Model_Tag();
        if (isset($_REQUEST['linkidreview'])) {
            http_custom_redirect('submit_review.php?linkid=' . $_REQUEST['linkidreview'] . '');
            edit();
        }

        if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'confirmed') {	
            $this->view->assign('confirmed', 1);	
        }

        if (isset($_SESSION['phpld']['user']['level']) && $_SESSION['phpld']['user']['level'] == 1) {
            $this->view->assign('is_admin', 1);
        }

        if (defined('REQUIRE_REGISTERED_USER') && (REQUIRE_REGISTERED_USER == 1) && (EMAIL_CONFIRMATION == 1)) {
            if (isset($_SESSION['phpld']['user']['id'])) {
                $uid = $_SESSION['phpld']['user']['id'];
                if (!has_confirmed_email($uid)) {
                    $reason = _L('You have not confirmed your email address yet') . '! ' . _L('Please check your email for the confirmation link') . '.';
                    gotoUnauthorized($reason);
                }
            }
        }

        if (defined('FORCE_SUBMIT_SESSION') && FORCE_SUBMIT_SESSION == 1) {
            require_once 'include/submit_session.php';
        }

        //Disable any caching by the browser
        disable_browser_cache();
        $CategoryID = (!empty($_REQUEST['c']) && preg_match('`^[\d]+$`', $_REQUEST['c']) ? intval($_REQUEST['c']) :
                        (!empty($_SERVER['HTTP_REFERER']) ? get_category($_SERVER['HTTP_REFERER']) : 0));
        $CategoryID = ($CategoryID > 0 ? $CategoryID : 0); //Make sure the category ID is valid
        if ($CategoryID)
            $CategoryTitle = getCategoryTitleByID($CategoryID);

        if (!empty($CategoryID)) {
            if(!check_if_closed_to_links($CategoryID)){
                $this->fm()->error(_l('Category  closed to submit, choose another one!'));
                return this;
       }
        }
        $this->view->assign('catid', $CategoryID);


        //Generate unique imagehash for visual confirmation
        if (VISUAL_CONFIRM == 1) {
            require_once 'include/functions_imgverif.php';
            $imagehash = fetch_captcha_hash();
            $_SESSION['imagehash'] = $imagehash;
            $this->view->assign('imagehash', $imagehash);
        }


        //----link types, selected link type -----
        $linktypeid = 0;
        if (isset($_REQUEST['LINK_TYPE']) && !empty($_REQUEST['LINK_TYPE'])) {
            $linktypeid = intval($_REQUEST['LINK_TYPE']);
        }

        if (!empty($_REQUEST['linkid']) && !$linktypeid) {
            $linktypeid = $db->GetOne("SELECT `LINK_TYPE` FROM `{$tables['link']['name']}` WHERE `ID` = " . $_REQUEST['linkid']);
            $sql = "SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $_REQUEST['linkid'];
            $data = $db->GetRow($sql);
            http_custom_redirect(DOC_ROOT . '/submit?c=' . $data["CATEGORY_ID"] . '&LINK_TYPE=' . $data["LINK_TYPE"] . '&linkid=' . $_REQUEST["linkid"] . '');
        }

        $add_categ_sql = $CategoryID ? " `ID` IN (SELECT `LINK_TYPE` FROM `{$tables['category_link_type']['name']}` WHERE `CATEGORY_ID` = '{$CategoryID}') AND " : '';
        $link_types = $db->GetAssoc("SELECT * FROM `{$tables['link_type']['name']}` WHERE {$add_categ_sql} `STATUS` = '2'  ORDER BY `ORDER_ID` ASC");
        if (empty($link_types) && $CategoryID > 0) {
            $link_types = $db->GetAssoc("SELECT * FROM `{$tables['link_type']['name']}` WHERE `STATUS` = '2'  ORDER BY `ORDER_ID` ASC");
        }
        foreach ($link_types as $link_type_id => $link_type) {
            $link_types[$link_type_id]['FIELDS'] = $db->GetAssoc("SELECT submit_item.ID, submit_item.FIELD_NAME FROM `{$tables['submit_item']['name']}` AS submit_item, `{$tables['submit_item_status']['name']}` AS item_status WHERE item_status.LINK_TYPE_ID = '{$link_type_id}' AND item_status.ITEM_ID = submit_item.ID AND item_status.STATUS = '2' AND submit_item.IS_DEFAULT = '0'");
        }
        $this->view->assign('linktypeid', $linktypeid);
        $this->view->assign('link_types', $link_types);

        $link_type_details = $db->GetRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$linktypeid}'");
        $video = 0;
        if ($linktypeid) {
            if (trim($link_type_details['LIST_TEMPLATE']) == "video.tpl" or trim($link_type_details['DETAILS_TEMPLATE']) == "video.tpl") {
                $video = 1;
            }
            
        }
        $this->view->assign('video', $video);
        $this->view->assign('link_type_details', $link_type_details);

        $link_price = $link_type_details['PRICE'];
        //----/link types, selected link type ----------------------------------------------------------




        //----prepare form fields -----------------------------------------------------------------------------
        $sqlSubmitItems = "
	SELECT submit_item.*, item_status.STATUS, item_status.LINK_TYPE_ID, item_status.IS_DETAIL, item_status.REQUIRED  
	FROM `{$tables['submit_item']['name']}` AS submit_item, `{$tables['submit_item_status']['name']}` AS item_status 
	WHERE item_status.ITEM_ID = submit_item.ID 
		AND item_status.LINK_TYPE_ID = '{$linktypeid}'
		AND item_status.STATUS = '2' 
	ORDER BY `ORDER_ID` ASC";

        $submit_items = $db->GetAll($sqlSubmitItems);

        foreach ($submit_items as $submit_item_id => $submit_item) {
            if ($submit_item['TYPE'] == 'DROPDOWN' || $submit_item['TYPE'] == 'MULTICHECKBOX') {
                $submit_items[$submit_item_id]['OPTIONS'] = get_submit_item_list($submit_item['ID']);
            }
        }
	
        $this->view->assign("submit_items", $submit_items);
        //----/prepare form fields ----------------------------------------------------------------------------




        //----validation -------------------------------------------------------------------------------
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
                            'id' => (!empty($_REQUEST['linkid']) ? clean_string_paranoia($_REQUEST['linkid']) : 0)
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
                )
            ),
            'messages' => array(
                'CATEGORY_ID' => array(
                    'remote' => _L("Please select a category.")
                ),
                'TITLE' => array(
                    'remote' => _L("Title is not valid: most likely, not unique in parent category.")
                ),
				/*'URL' => array(
					'remote' => _L("URL is offline or unreachable from host.")
				),*/
            )
        );

        if (SUBMIT_USE_RTE === 0) {
            $validators['rules']['DESCRIPTION'] = array(
                'minlength' => DESCRIPTION_MIN_LENGTH,
                'maxlength' => DESCRIPTION_MAX_LENGTH
            );
        }
        if (ENABLE_META_TAGS == 1) {
            $validators['rules']['META_DESCRIPTION'] = array(
                'minlength' => META_DESCRIPTION_MIN_LENGTH,
                'maxlength' => META_DESCRIPTION_MAX_LENGTH
            );
        }
        if (ENABLE_META_TAGS == 1) {
            $validators['rules']['META_KEYWORDS'] = array(
                'minlength' => META_KEYWORDS_MIN_LENGTH,
                'maxlength' => META_KEYWORDS_MAX_LENGTH
            );
        }

        if (VISUAL_CONFIRM == 2) {
            $validators['rules']['DO_MATH'] = array(
                'required' => true
            );
        }

        if (VISUAL_CONFIRM == 1) {
            $validators['rules']['CAPTCHA'] = array(
                'remote' => array(
                    'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                    'type' => "post",
                    'data' => array(
                        'action' => "isCaptchaValid",
                        'table' => "img_verification",
                        'field' => "CAPTCHA",
                        'IMAGEHASH' => $imagehash
                ))
            );
           $validators['messages']['CAPTCHA'] =  array('remote' => _L("Incorrect code."));
        }

        foreach ($submit_items_vld as $k => $v) {
            switch ($k) {
                case 'TITLE':
                case 'CATEGORY_ID':
                case 'EXPIRY_DATE':
                case 'HITS':
                case 'PAGERANK':
                case 'META_DESCRIPTION':
                    break;

                case 'RECPR_URL':

                    if (isset($v['required']) && $v['required'] === true) {
                        $recpr_required = 1;
                        $validators['rules']['RECPR_URL'] = array(
                            'required' => true,
                            'remote' => array(
                                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                                'type' => 'post',
                                'data' => array(
                                    'action' => 'isRecprOnline',
                                    'table' => "link",
                                    'field' => "RECPR_URL",
                                    'URL' => "function() { return $('#URL').val(); }"
                                )
                            )
                        );
                        $validators['messages']['RECPR_URL'] = array('remote' => 'Invalid reciprocal link');
                    } else {
                        $recpr_required = 0;
                    }
                    break;
                default:
                    $validators['rules'][$k] = $v;
                    foreach ($v as $kk => $vv) {
                        if (($vv['data']['action'] == 'isURLOnline')) {
                            $validators['rules'][$k][$kk]['data']['id'] = (!empty($_REQUEST['linkid']) ? clean_string_paranoia($_REQUEST['linkid']) : 0);
                        }
                    }
            }
        }

        $validators['rules']['AGREERULES'] = array('required' => true);
        $vld = json_custom_encode($validators);

        $this->view->assign('valid', $validators);
        $this->view->assign('validators', $vld);
	global $validator;
        $validator = new Validator($validators);
        //----/validation ------------------------------------------------------------------------------


        //----path--------------------------------------------------------------------------------------
        $path = array();
        $path[] = array('ID' => '0', 'TITLE' => _L(SITE_NAME), 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
        $path[] = array('ID' => '0', 'TITLE' => _L('Submit Link'), 'TITLE_URL' => '', 'DESCRIPTION' => _L('Submit a new link to the directory '));
        $this->view->assign('path', $path);
        //----/path-------------------------------------------------------------------------------------


        //Array usually for radio buttons with answers yes/no
        $this->view->assign('yes_no', array(1 => _L('Yes'), 0 => _L('No')));

        //Check if using RTE (Rich Text Editor)
        $useRTE = (defined('SUBMIT_USE_RTE') ? SUBMIT_USE_RTE : 0);
        $this->view->assign('useRTE', $useRTE);

        //If current user is banned, show a custom error message
        //and stop the rest of the script
        check_if_banned();
        if (BOT_CHECK == 1)
            check_botscout($client_info['IP'], $_REQUEST['EMAIL'], BOT_KEY);
        //Make an additional check if client is allowed to post/submit
        //[Spam] protection
        require_once 'include/check_post_rules.php';
        $post_rules_unauthorized = check_post_rules($_POST);

        // Evaluate payment options
        $recpr_required = (in_array('RECPR_URL', $link_types[$_REQUEST['LINK_TYPE']]['FIELDS'])) ? '1' : '0';
        $ShowReciprField = ($recpr_required == 0 ? 0 : 1);

        $link_id = (!empty($_REQUEST['linkid']) ? clean_string_paranoia($_REQUEST['linkid']) : 0);

        $remove_link = 0;
        $review_link = 0;
        $EditRedirect = 0;

        //Determine category
        $CategoryID = (!empty($_REQUEST['c']) && preg_match('`^[\d]+$`', $_REQUEST['c']) ? intval($_REQUEST['c']) :
                        (!empty($_SERVER['HTTP_REFERER']) ? get_category($_SERVER['HTTP_REFERER']) : 0));
        $CategoryID = ($CategoryID > 0 ? $CategoryID : 0); //Make sure the category ID is valid
        if ($CategoryID)
            $CategoryTitle = getCategoryTitleByID($CategoryID);

        if (!empty($CategoryID)) {
           if(!check_if_closed_to_links($CategoryID)){
                $this->fm()->error(_l('Category  closed to submit, choose another one!'));
                return this;
       }
        }
        $this->view->assign('catid', $CategoryID);
        if (!empty($link_id)) {
            $link_id = intval($link_id);
            $add_categs = $db->GetAll("SELECT * FROM `{$tables['additional_category']['name']}` WHERE `LINK_ID` = '{$link_id}'");
            $this->view->assign("add_categs", $add_categs);
            $add_links = $db->GetAll("SELECT * FROM `{$tables['additional_link']['name']}` WHERE `LINK_ID` = '{$link_id}'");
            $this->view->assign("add_links", $add_links);
            if (!empty($_SESSION['phpld']['user']['id'])) {
                $check_user = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($link_id) . " AND `OWNER_ID` = " . $db->qstr($_SESSION['phpld']['user']['id']));

                $check_double_review = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = " . $db->qstr($link_id));
                if ($check_double_review > 0)
		$this->fm()->success(_l('This entry is already marked for review and was not approved yet. New modifications will overwrite older reviews.'));
            
                    //$this->view->assign('double_review', _L('This entry is already marked for review and was not approved yet. New modifications will overwrite older reviews.'));
	    }
            if ($check_user < 1) {
                http_custom_redirect(DOC_ROOT . '/unauthorized');
            }

            $remove_link = 1;
            $review_link = 1;
        }

        $EditUnique = '';
        $MoveToPayment = true;
        $action = 'submit'; //Default action
        if (!empty($_SESSION['phpld']['user']['id']) && $review_link == 1) {
            $action = 'edit';
        }

        if (!empty($_SESSION['phpld']['user']['id'])) {
            $owner_details = $db->GetRow("SELECT `NAME` AS `OWNER_NAME`, `EMAIL` AS `OWNER_EMAIL` FROM `{$tables['user']['name']}` WHERE `ID` = " . $db->qstr($_SESSION['phpld']['user']['id']));
        }

        if ((empty($_POST['submit']) && empty($_POST['edit']))) {
            if (VISUAL_CONFIRM == 2) {
                // do the math
                $n1 = rand(1, 9);
                $n2 = rand(1, 9);
                $hash = do_math($n1, $n2);
                $_SESSION['DO_MATH_N1'] = $n1;
                $_SESSION['DO_MATH_N2'] = $n2;
                // end do the math
            }
            if (defined('FORCE_SUBMIT_SESSION') && FORCE_SUBMIT_SESSION == 1) {
                generateSubmitSession();
            }

            if (!empty($_SERVER['HTTP_REFERER']))
                $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

            if (!empty($_SESSION['phpld']['user']['id']) && !empty($link_id)) {
                $sql = "SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($link_id) . " AND `OWNER_ID` = " . $db->qstr($_SESSION['phpld']['user']['id']);
                $data = $db->GetRow($sql);
                $EditUnique = ':TITLE:' . $data['TITLE'];
            } else {
                $data = array();
                $data['CATEGORY_ID'] = $CategoryID;
            }

            $CategoryTitle = getCategoryTitleByID($data['CATEGORY_ID']);

            if ($action != 'edit') {
                $data['OWNER_NAME'] = (!empty($data['OWNER_NAME']) ? (strlen($data['OWNER_NAME']) > USER_NAME_MAX_LENGTH ? substr($data['OWNER_NAME'], 0, USER_NAME_MAX_LENGTH - 4) . ' ...' : trim($data['OWNER_NAME'])) : $owner_details['OWNER_NAME']);
                $data['OWNER_EMAIL'] = (!empty($data['OWNER_EMAIL']) ? $data['OWNER_EMAIL'] : $owner_details['OWNER_EMAIL']);
            }
            $data['RECPR_REQUIRED'] = $recpr_required;
            $link_type = $_REQUEST['LINK_TYPE'];

            // Clear some varialbles
            if (isset($_SESSION['SmartyPaginate']))
                unset($_SESSION['SmartyPaginate']);
            if (isset($_SESSION['values']))
                unset($_SESSION['values']);

            if ((isset($data['CATEGORY_ID']) && $rights['addLink'] == 1)
                || (has_rights_on_all_cats($_SESSION['phpld']['user']['level'])))
			{
                $dont_show_captch = 1;
                if (($_SESSION['phpld']['user']['level'] == 1) || ($_SESSION['phpld']['user']['level'] == 3)) {
                    $dont_show_pay = 1;
                }
                $this->view->assign('dont_show_captch', $dont_show_captch);
                $this->view->assign('dont_show_pay', $dont_show_pay);
            }
        } else {
            if (isset($_SESSION['link_submitted'])) {
                unset($_SESSION['link_submitted']);
            }

             if(!empty($CATEGORY_ID))
            $CategoryTitle = getCategoryTitleByID($CATEGORY_ID);
        else
            $CategoryTitle = getCategoryTitleByID($_REQUEST['CATEGORY_ID']);

            if (!empty($_SESSION['phpld']['user']['id']) && !empty($_POST['edit'])) {
                $action = 'edit';
            }

            if ($action == 'edit') {
                $data = get_table_data('link_review');
            } else {
                $data = get_table_data('link');
            }

            if (!empty($owner_details)) {
                $data = array_merge($data, $owner_details);
            }

            $data = filter_white_space($data);

            //check if user needs approval
            $rights = array();
            $rights = user_needs_approval($_SESSION['phpld']['user']['id'], $data['CATEGORY_ID']);
            if ($currentUser and $currentUser['ACTIVE']) {
                $data['STATUS'] = '1';
            } elseif ($action == 'submit' && (($rights['addLink'] == 1) || (($link_type_details['REQUIRE_APPROVAL'] == 0) && (empty($link_type_details['PRICE']))))) {
                $data['STATUS'] = '2';
            } else {
                $data['STATUS'] = '1';
            }

            $data['IPADDRESS'] = $client_info['IP'];
            if (!empty($client_info['HOSTNAME']))
                $data['DOMAIN'] = $client_info['HOSTNAME'];

            $data['VALID'] = '1';
            $data['LINK_TYPE'] = $_REQUEST['LINK_TYPE'];
            $data['RECPR_REQUIRED'] = $recpr_required;
            if ($recpr_required) {
                $data['RECPR_VALID'] = '2'; // OK Status
                $data['RECPR_LAST_CHECKED'] = gmdate('Y-m-d H:i:s');
            }

            $data['LAST_CHECKED'] = gmdate('Y-m-d H:i:s');
            $data['DATE_ADDED'] = gmdate('Y-m-d H:i:s');
            $data['DATE_MODIFIED'] = gmdate('Y-m-d H:i:s');

            if (VISUAL_CONFIRM == 1 && !empty($_POST['CAPTCHA'])) {
                $data = array_merge($data, array('CAPTCHA' => $_POST['CAPTCHA'], 'IMAGEHASH' => $_POST['IMAGEHASH']));
            }

            $alreadyFeatured = 0;
            $SecondValidation = 1;

            //On review, check if link is already a featured link
            if ($action == 'edit') {
                $alreadyFeatured = $db->GetOne("SELECT `FEATURED` FROM {$tables['link']['name']} WHERE `ID` = " . $db->qstr($link_id));
            }

            if (PAY_ENABLE == '1' && $_POST['LINK_TYPE'] == 'featured' && !empty($data['CATEGORY_ID']) && $alreadyFeatured == 0) {
                $SecondValidation = $AllowedFeat = check_allowed_feat($data['CATEGORY_ID']);
                $this->view->assign('AllowedFeat', $AllowedFeat);
            }

            //Validate domain, if not banned
            $secondBanCheck = $db->GetOne("SELECT COUNT(*) FROM `{$tables['banlist']['name']}` WHERE `BAN_DOMAIN` LIKE " . $db->qstr(parseDomain($data['URL'])));
            $SecondValidation = ($secondBanCheck > 0 ? 0 : $SecondValidation);
            $PageRank = 1;
            if (LIMIT_PR == '1') {
                require_once 'include/pagerank.php';
                $pr = get_page_rank($data['URL']);
                if ($pr <= PR_MIN) {
                    $PageRank = 0;
                    $this->view->assign('pr_error', 'Sorry Your Site\'s Pagerank is not greater than or equal to ' . PR_MIN);
                }
            }
            $this->view->assign('secondBanCheck', $secondBanCheck);

            //Rules check
            $data['AGREERULES'] = (isset($_POST['AGREERULES']) && $_POST['AGREERULES'] == 'on' ? 0 : 1);
            $data['OWNER_NEWSLETTER_ALLOW'] = (isset($_POST['OWNER_NEWSLETTER_ALLOW']) && $_POST['OWNER_NEWSLETTER_ALLOW'] == 'on' ? 1 : 0);
            $validator = new Validator($validators);
            $validate_arr = $_POST;
	    foreach($_FILES as $k => $v)
		 $validate_arr[$k] = $v['name'];
            $validator_res = $validator->validate($validate_arr);

            if (VISUAL_CONFIRM == 2) {
                //do the math
                if (!isset($_SESSION['DO_MATH_N1']) || !isset($_SESSION['DO_MATH_N2']))
                    $validator_res['DO_MATH'] = 'Not a legitimate submit';
                if ($_REQUEST['DO_MATH'] != $_SESSION['DO_MATH_N1'] + $_SESSION['DO_MATH_N2'])
                    $validator_res['DO_MATH'] = 'Please Check Your Math Below';
                // end do math
            }

            if (empty($validator_res)) {
                if (defined('FORCE_SUBMIT_SESSION') && FORCE_SUBMIT_SESSION == 1) {
                    //Validate unique submit session
                    $submitSessionValidation = validate_submit_session();

                    if ($submitSessionValidation == 0) {
                        //Invalid or expired submission,
                        //Block access
                        unset($data);
                        $reason = _L('Invalid or expired submit session') . '! ' . _L('Please reload submit page and try again') . '.';
                        gotoUnauthorized($reason);
                    }
                }

                //Validate for banned words
                //Pass all submition data as parameter
                $hasBannedWords = if_word_is_banned($data);
                if ($hasBannedWords == 1) {
                    //Invalid or expired submission,
                    //Block access
                    unset($data);
                    $reason = _L('The administrator of this link directory, has banned words from your submition.');
                    gotoUnauthorized($reason);
                }

                if (isset($data['CAPTCHA']))
                    unset($data['CAPTCHA']);
                if (isset($data['IMAGEHASH']))
                    unset($data['IMAGEHASH']);
                if (isset($data['AGREERULES']))
                    unset($data['AGREERULES']);

                if (ENABLE_PAGERANK == 1) {
                    require_once 'include/pagerank.php';
                    $data['PAGERANK'] = get_page_rank($data['URL']);
                    if (!empty($data['RECPR_URL']))
                        $data['RECPR_PAGERANK'] = get_page_rank($data['RECPR_URL']);
                }
			 
                    $coords = geocodeAddress($data['COUNTRY'], $data['STATE'], $data['CITY'], $data['ADDRESS'], $data['ZIP']);

                    $data['LAT'] = $coords['lat'];
                    $data['LON'] = $coords['lng'];
                

                if ($action == 'edit') {
                    $id = $db->GetOne("SELECT `ID` FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = " . $db->qstr($link_id));
                    $id = (!empty($id) ? intval($id) : '');
                } else {
                    $id = $db->GenID($tables['link']['name'] . '_SEQ');
                }

                //email confirmed related
                //if email confirmation not required
                if (EMAIL_CONFIRMATION == 0) {
                    $data['OWNER_EMAIL_CONFIRMED'] = '1';
                } else {
                    //if has rights no longer requiring email confirmation
                    if ($rights['addLink'] == 1) {
                        $data['OWNER_EMAIL_CONFIRMED'] = '1';
                    } elseif (!email_is_confirmed($data['OWNER_EMAIL']) && $data['OWNER_EMAIL'] != '') {
                        //send confirmation email
                        //send only if email has not been confirmed already (exists in link table/user table and is confirmed

                        $data['OWNER_EMAIL_CONFIRMED'] = '0';
                        $mail = get_emailer_admin();

                        //Add email subject
                        $emailSubject = "Email Confirmation";
                        $mail->Subject = trim($emailSubject);

                        //Add owner email address
                        $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);

                        //Add email body
                        $emailBody = "Please confirm email address here: " . SITE_URL . "submit/confirmed?lid=" . $id;
                        $mail->Body = trim($emailBody);

                        //Send email
                        if (!$mail->Send()) {
                            $errorMsg = $mail->ErrorInfo;
                            $this->fm()->error($errorMsg);
                        }

                        //Clear all addresses (and attachments) for next loop
                        $mail->ClearAddresses();
                        $mail->ClearAttachments();

                        //Free memory
                        unset($unhashedPassword, $mail, $emailBody, $emailSubject);

                        $this->fm()->error(_l('The email address you provided needs to be confirmed before the link is listed.').'<br />'._l('Please check your email for the confirmation link.'));
                    } else {
                        $data['OWNER_EMAIL_CONFIRMED'] = '1';
                    }
                }
                //end of email confirmed
                $data['ID'] = (!empty($id) ? intval($id) : '');
		 if ($action != 'edit') {
				$data['RATING'] = empty($data['RATING'])?"":$data['RATING'];
				$data['VOTES'] = empty($data['VOTES'])?"":$data['VOTES'];
				$data['COMMENT_COUNT'] = empty($data['COMMENT_COUNT'])?"":$data['COMMENT_COUNT'];
		 }
                $data['NOFOLLOW'] = $link_types[$_REQUEST['LINK_TYPE']]['NOFOLLOW'];
                $data['FEATURED'] = $link_types[$_REQUEST['LINK_TYPE']]['FEATURED'];

                if (!empty($_SESSION['phpld']['user']['id']))
                    $data['OWNER_ID'] = $_SESSION['phpld']['user']['id'];

                if (isset($_SESSION['phpld']['user']['id'])) {
                    $rights = user_needs_approval($_SESSION['phpld']['user']['id'], $data['CATEGORY_ID']);
                    if ((isset($data['CATEGORY_ID']) && $rights['addLink'] == 1)
                            || (has_rights_on_all_cats($_SESSION['phpld']['user']['level']))) {
                        $data['PAYED'] = 1;
                        $dont_redir_topaypal = 1;
                    }
                }

                $data['OWNER_NOTIF'] = ($price[$link_type] > 0 ? 0 : 1 );
                $data['PAYED'] = ($price[$link_type] > 0 ? 0 : -1);
                $data['MARK_REMOVE'] = (!empty($_POST['MARK_REMOVE']) ? 1 : 0 );
		if ($action != 'edit') {
				$linkModel = new Model_Link();
				$data['CACHE_URL'] = $linkModel->seoUrl($data, $data['ID']);
		}
                //Check again fields and truncate to maximum length,
                //auto-submitters can no more force longer text than allowed
                if ($action != 'edit') {
                    $data['OWNER_NAME'] = (!empty($data['OWNER_NAME']) ? (strlen($data['OWNER_NAME']) > USER_NAME_MAX_LENGTH ? substr($data['OWNER_NAME'], 0, USER_NAME_MAX_LENGTH - 4) . ' ...' : trim($data['OWNER_NAME'])) : '');
                }

                $RegularLink_notif = true;

                if ($action == 'edit') {
                    $OldLinkType = $db->GetOne("SELECT `LINK_TYPE` FROM `{$tables['link']['name']}` WHERE `ID` = " . $db->qstr($link_id));
                    if ($OldLinkType == $data['LINK_TYPE'])
                        $MoveToPayment = false;

                    foreach ($submit_items as $key_id => $submit_item) {
                        if ($submit_item['TYPE'] == 'IMAGE') {
                            $si_images[$key_id] = $submit_item;
                        } elseif ($submit_item['TYPE'] == 'FILE') {
                            $si_files[$key_id] = $submit_item;
                        } elseif ($submit_item['TYPE'] == 'TAGS') {
                            $si_tags[$key_id] = $submit_item;
                        } elseif ($submit_item['TYPE'] == 'VIDEO') {
                            $si_videos[$key_id] = $submit_item;
							} elseif ($submit_item['TYPE'] == 'MULTICHECKBOX') {
                        $items = $_REQUEST[$submit_item['FIELD_NAME']];
                        } else {
                            $data[$submit_item['FIELD_NAME']] = $_REQUEST[$submit_item['FIELD_NAME']];
                        }
                    }
                    if (strlen(trim($data['URL'])) > 0 && !preg_match('#^http[s]?:\/\/#i', $data['URL']))
                        $data['URL'] = "http://" . $data['URL'];

                    if (strlen(trim($data['RECPR_URL'])) > 0 && !preg_match('#^http[s]?:\/\/#i', $data['RECPR_URL']))
                        $data['RECPR_URL'] = "http://" . $data['RECPR_URL'];

                    $data['LINK_ID'] = $payment_id = $link_id;

		    $submit_notification = db_replace('link_review', $data, 'ID');
		    
		    
		    // handle the submit item image groups.
                    foreach ($submit_items as $key_id => $submit_item) {
                        if ($submit_item['TYPE'] == "IMAGEGROUP") {
                            $imagegroupname = $submit_item['FIELD_NAME'];
                            $imagegroupid = $_REQUEST[$imagegroupname];
                            $groupdata = $_SESSION['imagegroups'][$imagegroupid];
                            $new_group_id = $imagegroupid;
                            $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `" . $submit_item['FIELD_NAME'] . "` = " . $db->qstr($new_group_id) . " WHERE `ID`=" . $db->qstr($new_id));
                            foreach ($groupdata as $image) {
                               // $ext = strtolower(end(explode('.', $image)));
								$ext1 = explode('.', $image);
								$ext = end($ext1);
								$ext = strtolower($ext);
                                $name = $new_id . "_" . uniqid() . "." . $ext;
                                if (file_exists(INSTALL_PATH . '/uploads/' . $name)) {
                                    unlink(INSTALL_PATH . '/uploads/' . $name);
                                }
                                if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $name)) {
                                    unlink(INSTALL_PATH . '/uploads/thumb/' . $name);
                                }
                                error_log("saving $image to " . INSTALL_PATH . "uploads/" . $name);
                                resizeImg($image, INSTALL_PATH . 'uploads/' . $name, 400, 400);
                                resizeImg($image, INSTALL_PATH . 'uploads/thumb/' . $name, 150, 150);

                                $db->Execute("INSERT INTO `{$tables['imagegroupfile']['name']}` (GROUPID, IMAGE) VALUES (" . $db->qstr($new_group_id) . "," . $db->qstr($name) . ")");
                            }

                            $dir = INSTALL_PATH . 'uploads/tmp/';
                            unlinkRecursive($dir);
                        }
                    }
		   
                    if ($video) {
                        $provider = $oembed->getProvider($data['URL']);

                        $args = array('width' => 640, 'height' => 390);
                        $photoData = $oembed->fetch($provider, $data['URL'], $args);
                        if ($photoData) {
                            $photoData->type = 'photo';
                            $photoData->width = 200;

                           // $ext = strtolower(end(explode('.', $photoData->thumbnail_url)));
							$ext1 = explode('.',  $photoData->thumbnail_url);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $name = $link_id . "_1." . $ext;

                            if (file_exists(INSTALL_PATH . '/uploads/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/' . $name);
                            }
                            if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/thumb/' . $name);
                            }

                            $path = str_replace('\\', '/', INSTALL_PATH);
                            $content = file_get_contents($photoData->thumbnail_url);

                            $filename = $path . 'temp/templates/' . $name;
                            $result = @file_put_contents($filename, $content);
                            if ($result) {
                                resizeImg($filename, $path . 'uploads/' . $name, 400, 400);
                                resizeImg($filename, $path . 'uploads/thumb/' . $name, 200, 200);
                                $db->Execute("UPDATE `{$tables['link']['name']}` SET `IMAGE` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($link_id));
                                unlink($filename);
                            } else {
                                print "Error saving file";
                            }
                        }
                    }
                    $RegularLink_notif = false;
                    // Start Additional Categories section
                    if (isset($_REQUEST['ADD_CATEGORY_ID']) && is_array($_REQUEST['ADD_CATEGORY_ID']) && ($link_types[$_REQUEST['LINK_TYPE']]['MULTIPLE_CATEGORIES'] > 0)) {
                        $add_cat_data['LINK_ID'] = $data['LINK_ID'];
                        $db->Execute("DELETE FROM `{$tables['additional_category_review']['name']}` WHERE `LINK_ID` = " . $db->qstr($add_cat_data['LINK_ID']));
                        for ($i = 0; $i < $link_types[$_REQUEST['LINK_TYPE']]['MULTIPLE_CATEGORIES'] - 1; $i++) {
                            $add_cat_id = trim($_REQUEST['ADD_CATEGORY_ID'][$i]);
                            if (!empty($add_cat_id)) {
                                $add_cat_data['CATEGORY_ID'] = $_REQUEST['ADD_CATEGORY_ID'][$i];
                                $db->Execute("INSERT INTO `{$tables['additional_category_review']['name']}` (`LINK_ID`, `CATEGORY_ID`)
                                            VALUES (" . $db->qstr($add_cat_data['LINK_ID']) . ", " . $db->qstr($add_cat_data['CATEGORY_ID']) . ")");
                            }
                        }
                    }
                    // End Additional Categories section
                    // Start Additional Links section
                    if (isset($_REQUEST['ADD_LINK_URL']) && is_array($_REQUEST['ADD_LINK_URL']) && ($link_types[$_REQUEST['LINK_TYPE']]['DEEP_LINKS'] > 0)) {
                        $add_link_data['LINK_ID'] = $data['LINK_ID'];
                        $db->Execute("DELETE FROM `{$tables['additional_link_review']['name']}` WHERE `LINK_ID` = " . $db->qstr($add_link_data['LINK_ID']));
                        for ($i = 0; $i < $link_types[$_REQUEST['LINK_TYPE']]['DEEP_LINKS']; $i++) {
                            $add_link_url = trim($_REQUEST['ADD_LINK_URL'][$i]);
                            if (!empty($add_link_url)) {
                                $add_link_url = (substr($add_link_url, 0, 7) != 'http://' ? 'http://' . $add_link_url : $add_link_url);
                                $add_link_data['URL'] = $add_link_url;
                                $add_link_data['TITLE'] = $_REQUEST['ADD_LINK_TITLE'][$i] ? $_REQUEST['ADD_LINK_TITLE'][$i] : $_REQUEST['ADD_LINK_URL'][$i];
                                $db->Execute("INSERT INTO `{$tables['additional_link_review']['name']}` (`LINK_ID`, `TITLE`, `URL`)
                                            VALUES (" . $db->qstr($add_link_data['LINK_ID']) . ", " . $db->qstr($add_link_data['TITLE']) . ", " .
                                        $db->qstr($add_link_data['URL']) . ")");
                            }
                        }
                    }
                    // End Additional Links section
                    //submit items: images
                    foreach ($si_images as $key_id => $image) {
                        if (!empty($_FILES[$image['FIELD_NAME']]['name'])) {
                           // $ext = strtolower(end(explode('.', $_FILES[$image['FIELD_NAME']]['name'])));
							$ext1 = explode('.', $_FILES[$image['FIELD_NAME']]['name']);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $name = $data['LINK_ID'] . "_" . $key_id . "." . $ext;

                            if (file_exists(INSTALL_PATH . '/uploads/reviews/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/reviews/' . $name);
                            }
                            if (file_exists(INSTALL_PATH . '/uploads/reviews/thumb/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/reviews/thumb/' . $name);
                            }
                            resizeImg($_FILES[$image['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/reviews/' . $name, 400, 400);
                            resizeImg($_FILES[$image['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/reviews/thumb/' . $name, 150, 150);
                            $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `" . $image['FIELD_NAME'] . "` = " . $db->qstr($name) . " WHERE `LINK_ID`=" . $db->qstr($data['LINK_ID']));
                        } elseif ($_REQUEST["MARK_REMOVE_" . $image['FIELD_NAME']] == '1') {
                            $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `" . $image['FIELD_NAME'] . "` = '' WHERE `LINK_ID`=" . $db->qstr($data['LINK_ID']));
                        } else {
                            $link = getFullLinkInfo($data['LINK_ID']);
                            if (file_exists(INSTALL_PATH . '/uploads/' . $link[$image['FIELD_NAME']])) {
                                copy(INSTALL_PATH . '/uploads/' . $link[$image['FIELD_NAME']], INSTALL_PATH . '/uploads/reviews/' . $link[$image['FIELD_NAME']]);
                            }
                            if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $link[$image['FIELD_NAME']])) {
                                copy(INSTALL_PATH . '/uploads/thumb/' . $link[$image['FIELD_NAME']], INSTALL_PATH . '/uploads/reviews/thumb/' . $link[$image['FIELD_NAME']]);
                            }

                            $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `" . $image['FIELD_NAME'] . "` = " . $db->qstr($link[$image['FIELD_NAME']]) . " WHERE `LINK_ID`=" . $db->qstr($data['LINK_ID']));
                        }
                    }
                    //end of submit items: images
                    ////submit items: files
                    foreach ($si_files as $key_id => $file) {
                        if (!empty($_FILES[$file['FIELD_NAME']]['name'])) {
                            $allowed = array("pdf", "xls", "xlsx", "doc", "docx", "zip", "rar", "txt", "rtf", "csv");
                            //$ext = strtolower(end(explode('.', $_FILES[$file['FIELD_NAME']]['name'])));
							$ext1 = explode('.', $_FILES[$file['FIELD_NAME']]['name']);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $name = $data['LINK_ID'] . "_" . $key_id . "." . $ext;

                            if (file_exists(INSTALL_PATH . '/uploads/reviews/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/reviews/' . $name);
                            }
                            if (in_array($ext, $allowed)) {
                                if (move_uploaded_file($_FILES[$file['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/reviews/' . $name)) {
                                    $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `" . $file['FIELD_NAME'] . "` = " . $db->qstr($name) . " WHERE `LINK_ID`=" . $db->qstr($data['LINK_ID']));
                                }
                            }
                        } elseif ($_REQUEST["MARK_REMOVE_" . $file['FIELD_NAME']] == '1') {
                            $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `" . $file['FIELD_NAME'] . "` = '' WHERE `LINK_ID`=" . $db->qstr($data['LINK_ID']));
                        } else {
                            $link = getFullLinkInfo($data['LINK_ID']);
                            if (file_exists(INSTALL_PATH . '/uploads/' . $link[$file['FIELD_NAME']])) {
                                copy(INSTALL_PATH . '/uploads/' . $link[$file['FIELD_NAME']], INSTALL_PATH . '/uploads/reviews/' . $link[$file['FIELD_NAME']]);
                            }
                            $db->Execute("UPDATE `{$tables['link_review']['name']}` SET `" . $file['FIELD_NAME'] . "` = " . $db->qstr($link[$file['FIELD_NAME']]) . " WHERE `LINK_ID`=" . $db->qstr($data['LINK_ID']));
                        }
                    }
                    //end of submit items: files
                    //submit items: videos
                    foreach ($si_videos as $key => $file) {
                        if (!empty($_FILES[$file['FIELD_NAME']]['name'])) {
                            $allowed = array("avi", "wmv", "mov", "mpg");
                          //  $ext = strtolower(end(explode('.', $_FILES[$file['FIELD_NAME']]['name'])));
							$ext1 = explode('.', $_FILES[$file['FIELD_NAME']]['name']);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $convertTo = 'flv';
                            $name = $data['LINK_ID'] . "_" . $key_id . "." . $ext;
                            $destName = $data['LINK_ID'] . "_" . $key_id . "." . $convertTo;

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
                        }
                    } //end of submit items: videos


                    if (isset($si_tags) && is_array($si_tags)) {
                        foreach ($si_tags as $field) {
                            $finalTags = array();
                            $tags = explode(',', $_POST[$field['FIELD_NAME']]);
                            if (is_string($tags)) {
                                $tags = array($tags);
                            }


                            // sunmit items: tags
                            foreach ($tags as $tag) {
                                $idTag = $tagsModel->addTag($tag, Model_Tag_Entity::STATUS_PENDING);
                                db_replace('tags_links', array('LINK_ID'=>$data['ID'], 'TAG_ID'=>$idTag), 'ID');
                                $finalTags[] = $idTag;
                            }

                            $data[$field['FIELD_NAME']] = implode(',', $finalTags);
                        }
                    }
                } else {

                    $payment_id = $data['ID'];

                    unset($data['MARK_REMOVE']);
                    // Add additional submit items to the LINK table
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
					$data['CATEGORY_ID'] = intval($_GET['c']);

                    $submit_notification = db_replace('link', $data, 'ID');

                    // Start Additional Links section
                    $new_id = $db->Insert_ID('link');

                    if (isset($si_tags) && is_array($si_tags)) {
                        $tagsData = array();
                        foreach ($si_tags as $field) {
                            $finalTags = array('ID'=>$new_id);
                            $tags = explode(',', $_POST[$field['FIELD_NAME']]);
                            if (is_string($tags)) {
                                $tags = array($tags);
                            }


                            // sunmit items: tags
                            foreach ($tags as $tag) {
                                $idTag = $tagsModel->addTag($tag, Model_Tag_Entity::STATUS_PENDING);
                                db_replace('tags_links', array('LINK_ID'=>$new_id, 'TAG_ID'=>$idTag), 'ID');
                                $finalTags[] = $idTag;
                            }

                            $tagsData[$field['FIELD_NAME']] = implode(',', $finalTags);
                            //var_dump($data[$field['FIELD_NAME']]);die();
                        }
                        db_replace('link', $tagsData, 'ID');
                    }

                    if ($video) {
                        $provider = $oembed->getProvider($data['URL']);

                        $args = array('width' => 640, 'height' => 390);
                        $photoData = $oembed->fetch($provider, $data['URL'], $args);
                        if ($photoData) {
                            $photoData->type = 'photo';
                            $photoData->width = 200;

                           // $ext = strtolower(end(explode('.', $photoData->thumbnail_url)));
							$ext1 = explode('.', $photoData->thumbnail_url);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $name = $new_id . "_1." . $ext;

                            if (file_exists(INSTALL_PATH . '/uploads/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/' . $name);
                            }
                            if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/thumb/' . $name);
                            }

                            $path = str_replace('\\', '/', INSTALL_PATH);
                            $content = file_get_contents($photoData->thumbnail_url);

                            $filename = $path . 'temp/templates/' . $name;
                            $result = @file_put_contents($filename, $content);
                            if ($result) {
                                resizeImg($filename, $path . 'uploads/' . $name, 400, 400);
                                resizeImg($filename, $path . 'uploads/thumb/' . $name, 200, 200);
                                $db->Execute("UPDATE `{$tables['link']['name']}` SET `IMAGE` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($new_id));
                                unlink($filename);
                            } else {
                                print "Error saving file";
                            }
                        }
                    }


                    // handle the submit item image groups.
                    foreach ($submit_items as $key_id => $submit_item) {
                        if ($submit_item['TYPE'] == "IMAGEGROUP") {
                            $imagegroupname = $submit_item['FIELD_NAME'];
                            $imagegroupid = $_REQUEST[$imagegroupname];
                            $groupdata = $_SESSION['imagegroups'][$imagegroupid];
                            $db->Execute("INSERT INTO `{$tables['imagegroup']['name']}` (DATE_MODIFIED, DATE_ADDED) VALUES (now(),now())");
                            $new_group_id = $db->GetOne("SELECT MAX(`GROUPID`) FROM `{$tables['imagegroup']['name']}`");
                            $db->Execute("UPDATE `{$tables['link']['name']}` SET `" . $submit_item['FIELD_NAME'] . "` = " . $db->qstr($new_group_id) . " WHERE `ID`=" . $db->qstr($new_id));
                            foreach ($groupdata as $image) {
                                //$ext = strtolower(end(explode('.', $image);
								$ext1 = explode('.', $image);
								$ext = end($ext1);
								$ext = strtolower($ext);
                                $name = $new_id . "_" . uniqid() . "." . $ext;
                                if (file_exists(INSTALL_PATH . '/uploads/' . $name)) {
                                    unlink(INSTALL_PATH . '/uploads/' . $name);
                                }
                                if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $name)) {
                                    unlink(INSTALL_PATH . '/uploads/thumb/' . $name);
                                }
                                error_log("saving $image to " . INSTALL_PATH . "uploads/" . $name);
                                resizeImg($image, INSTALL_PATH . 'uploads/' . $name, 400, 400);
                                resizeImg($image, INSTALL_PATH . 'uploads/thumb/' . $name, 150, 150);

                                $db->Execute("INSERT INTO `{$tables['imagegroupfile']['name']}` (GROUPID, IMAGE) VALUES (" . $db->qstr($new_group_id) . "," . $db->qstr($name) . ")");
                            }

                            $dir = INSTALL_PATH . 'uploads/tmp/';
                            unlinkRecursive($dir);
                        }
                    }

                    if (isset($_REQUEST['ADD_LINK_URL']) && is_array($_REQUEST['ADD_LINK_URL']) && ($link_types[$_REQUEST['LINK_TYPE']]['DEEP_LINKS'] > 0)) {
                        for ($i = 0; $i < $link_types[$_REQUEST['LINK_TYPE']]['DEEP_LINKS']; $i++) {
                            $add_link_url = trim($_REQUEST['ADD_LINK_URL'][$i]);
                            if (!empty($add_link_url)) {
                                $add_link_url = (substr($add_link_url, 0, 7) != 'http://' ? 'http://' . $add_link_url : $add_link_url);
                                $add_link_data['URL'] = $add_link_url;

                                $add_link_data['LINK_ID'] = $new_id;
                                $add_link_data['TITLE'] = $_REQUEST['ADD_LINK_TITLE'][$i] ? $_REQUEST['ADD_LINK_TITLE'][$i] : $_REQUEST['ADD_LINK_URL'][$i];
                                db_replace('additional_link', $add_link_data, 'ID');
                            }
                        }
                    }
                    // End Additional Links section
                    //submit items: images
          foreach ($si_images as $key_id => $image) {
                        //var_dump($_POST[$image['FIELD_NAME'].'_SUBMIT']);die();
                        if (!empty($_FILES[$image['FIELD_NAME']]['name'])) {
                          //  $ext = strtolower(end(explode('.', $_FILES[$image['FIELD_NAME']]['name'])));
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
                          //  $ext = strtolower(end(explode('.', $_FILES[$file['FIELD_NAME']]['name'])));
							$ext1 = explode('.', $_FILES[$file['FIELD_NAME']]['name']);
								$ext = end($ext1);
								$ext = strtolower($ext);
                            $name = $new_id . "_" . $key_id . "." . $ext;

                            if (file_exists(INSTALL_PATH . '/uploads/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/' . $name);
                            }
                            if (file_exists(INSTALL_PATH . '/uploads/thumb/' . $name)) {
                                unlink(INSTALL_PATH . '/uploads/thumb/' . $name);
                            }
                            if (in_array($ext, $allowed)) {
                                if (move_uploaded_file($_FILES[$file['FIELD_NAME']]['tmp_name'], INSTALL_PATH . 'uploads/' . $name)) {
                                    $db->Execute("UPDATE `{$tables['link']['name']}` SET `" . $file['FIELD_NAME'] . "` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($new_id));
                                }
                            }
                        }
                    }
                    //end of submit items: files
                    //submit items: videos
                    foreach ($si_videos as $key => $file) {
                        if (!empty($_FILES[$file['FIELD_NAME']]['name'])) {
                            $allowed = array("avi", "wmv", "mov", "mpg");
                           // $ext = strtolower(end(explode('.', $_FILES[$file['FIELD_NAME']]['name'])));
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
                        }
                    } //end of submit items: videos
                    // Start Additional Categories section
                    if (isset($_REQUEST['ADD_CATEGORY_ID']) && is_array($_REQUEST['ADD_CATEGORY_ID']) && ($link_types[$_REQUEST['LINK_TYPE']]['MULTIPLE_CATEGORIES'] > 0)) {
                        for ($i = 0; $i < $link_types[$_REQUEST['LINK_TYPE']]['MULTIPLE_CATEGORIES'] - 1; $i++) {
                            $add_cat_id = trim($_REQUEST['ADD_CATEGORY_ID'][$i]);
                            if (!empty($add_cat_id)) {
                                $add_cat_data['LINK_ID'] = $new_id;
                                $add_cat_data['CATEGORY_ID'] = $_REQUEST['ADD_CATEGORY_ID'][$i];
                                $res = db_replace('additional_category', $add_cat_data);
                            }
                        }
                    }
                    // End Additional Categories section
                }
		
                if (!empty($submit_notification)) {
		    if ($link_price > 0){
		    $this->fm()->success(_l('Link submitted').
                        ($data['STATUS'] == 1 ? ' '._l('and awaiting approval') : '')
                    );
		    }else{
                    $this->fm()->success(_l('Link submitted').
                        ($data['STATUS'] == 1 ? ' '._l('and awaiting approval') : '')
                    );
					 }

                    send_submit_notifications($data, $RegularLink_notif);

                    //Remind some fields
                    $reminder = array('CATEGORY_ID', 'OWNER_NAME', 'OWNER_EMAIL');

                    //Loop throught each link field
                    foreach ($data as $field => $value) {
                        //Check if it's not in the reminder fields
                        if (!in_array($field, $reminder)) {
                            //Remove data
                            unset($data[$field]);
                        }
                    }

                    $payment_id = (!empty($payment_id) && preg_match('`^[\d]+$`', $payment_id) ? intval($payment_id) : '');

                    // Payment redirection
                    if ($link_price > 0 && !empty($payment_id) && $MoveToPayment == true && $dont_redir_topaypal != 1) {
                        //Move to payment page
                        //added 6-24-07
                        $_SESSION['PRIVACY'] = '1';
                        //end add
                        http_custom_redirect(DOC_ROOT . "/payment/?id=" . $payment_id . ($action == 'edit' ? '&mode=review' : ''));
                    } else {
                        if (defined('FORCE_SUBMIT_SESSION') && FORCE_SUBMIT_SESSION == 1) {
                            //Submission successfull, generate new submit session
                            generateSubmitSession();
                            $_SESSION['link_submitted'] = 1;
                        }
                        unset($_POST, $_GET, $_REQUEST);
                    }
                    $EditRedirect = 1;
                }
            } else {
				unset($_SESSION["imagegroups"]);
                $this->fm()->formValidation($validator_res);
                $this->view->assign($_POST);
            }
        }

        //Determine length of description field
        $DescriptionLimit = (isset($data['DESCRIPTION']) && strlen(trim($data['DESCRIPTION'])) > 0 ? DESCRIPTION_MAX_LENGTH - strlen(trim($data['DESCRIPTION'])) : DESCRIPTION_MAX_LENGTH);
        $this->view->assign('DescriptionLimit', $DescriptionLimit);
        //Determine length of meta description field
        $MetaDescriptionLimit = (isset($data['META_DESCRIPTION']) && strlen(trim($data['META_DESCRIPTION'])) > 0 ? META_DESCRIPTION_MAX_LENGTH - strlen(trim($data['META_DESCRIPTION'])) : META_DESCRIPTION_MAX_LENGTH);
        $this->view->assign('MetaDescriptionLimit', $MetaDescriptionLimit);
        // Disabled submit reason
        $disable_reason = DISABLE_REASON;
        $this->view->assign('disablereason', $disable_reason);

        if (defined('ALLOW_HTML') && ALLOW_HTML == 1) {
            //Set allowed tags for template to display
            //Clean up tag list
            $allowedTags = clean_string_paranoia(ALLOWED_HTML_TAGS);
            //Remove multiple commas, commast at begin and end of sting, multiple spaces
            $allowedTags = preg_replace(array('#^[,]*#i', '#[,]*$#i', '#[,]+#i', '#[\s]#'), array('', '', ',', ''), $allowedTags);
            $allowedTags = preg_replace("/,/", ', ', $allowedTags);
            $this->view->assign('allowedTags', $allowedTags);
        }
        if (defined('ALLOW_ATTR') && ALLOW_ATTR == 1) {
            //Set allowed tags for template to display
            //Clean up tag list
            $allowedAttr = clean_string_paranoia(ALLOWED_ATTR_TAGS);
            //Remove multiple commas, commast at begin and end of sting, multiple spaces
            $allowedAttr = preg_replace(array('#^[,]*#i', '#[,]*$#i', '#[,]+#i', '#[\s]#'), array('', '', ',', ''), $allowedAttr);
            $allowedAttr = preg_replace("/,/", ', ', $allowedAttr);
            $this->view->assign('allowedAttr', $allowedAttr);
        }

        if (VISUAL_CONFIRM == 1)
            $this->view->assign('captcha_length', CAPTCHA_PHRASE_LENGTH);

        $this->view->assign('remove_link', $remove_link);
        $this->view->assign('review_link', $review_link);

		
        if($link_type_details['MULTIPLE_CATEGORIES'] > 0)
        $categs_tree = get_categs_tree_frontend_forlinks(0,$link_type);
		
		$categs_tree = get_categs_tree_frontend_forlinks(0,$link_type);
            $this->view->assign('categs_tree', $categs_tree);
			
		$payment_um = array ( '1' => _L('Month'),'2' => _L('Trimester'), '3' => _L('Semester'), '4' => _L('Year'), '5' => _L('Unlimited'));
        $this->view->assign('SubscriptionEnabled', $SubscriptionEnabled);
        $this->view->assign('payment_um', $payment_um);

        //Load Javascript libraries
        $load_Javascript = 1;
        $this->view->assign('load_Javascript', $load_Javascript);

        $this->view->assign('CategoryTitle', $CategoryTitle);
        $this->view->assign($data);
        $this->view->assign('data', $data);

        $this->view->assign('LINK_TYPE', $link_type);

        //Clean whitespace
        $this->view->load_filter('output', 'trimwhitespace');

        if ($_SESSION['link_submitted'] == 1) {
            $this->view->assign('posted', true);
            unset($_SESSION['link_submitted']);
        }


        if ((isset($data['CATEGORY_ID']) && $rights['addLink'] == 1)
                || (has_rights_on_all_cats($_SESSION['phpld']['user']['level']))) {
            $dont_show_captch = 1;
            if (($_SESSION['phpld']['user']['level'] == 1) || ($_SESSION['phpld']['user']['level'] == 3)) {
                $dont_show_pay = 1;
            }
            $this->view->assign('dont_show_captch', $dont_show_captch);
            $this->view->assign('dont_show_pay', $dont_show_pay);
        }
        if (VISUAL_CONFIRM == 2) {
            // do the math
            $n1 = $_SESSION['DO_MATH_N1'];
            $n2 = $_SESSION['DO_MATH_N2'];

            $this->view->assign('DO_MATH_N1', $n1);
            $this->view->assign('DO_MATH_N2', $n2);
            // end do math
        }
        $this->view->assign('allTags', $tagsModel->getAllTags(false, 'STATUS = '.Model_Tag_Entity::STATUS_ACTIVE));
        //Make output
	if(!empty($data['ID']) and $review_link == "1")
	{
	    $add_links = $db->GetAll("SELECT * FROM `{$tables['additional_link']['name']}` WHERE `LINK_ID` = '{$data['ID']}'");
	    $add_categs = $db->GetAll("SELECT * FROM `{$tables['additional_category']['name']}` WHERE `LINK_ID` = '{$data['ID']}'");
	    $this->view->assign("add_links", $add_links);
	    $this->view->assign("add_categs", $add_categs);
        
	    $imagegroupname = null;
	    $group_image_details = null;
	    foreach ($submit_items as $key_id => $submit_item) {
		if ($submit_item['TYPE'] == "IMAGEGROUP") {
		    $imagegroupname = $submit_item['FIELD_NAME'];
		}
	    }
	    
	   
	    if($data[$imagegroupname]!=null)
		$group_image_details = getLinkImages($data[$imagegroupname]);
	    
	    $this->view->assign('group_image_details', $group_image_details);
	}
	
    }

    public function confirmedAction() {
		 $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        // Disable any caching by the browser
        disable_browser_cache();

        if (isset($_REQUEST['lid'])) 
            $result = $db->Execute("UPDATE `{$tables['link']['name']}` SET `OWNER_EMAIL_CONFIRMED` = '1' WHERE `ID`=".$db->qstr($_REQUEST['lid'])."");


    }


}