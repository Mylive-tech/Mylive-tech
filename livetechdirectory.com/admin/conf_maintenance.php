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

// Disable any caching by the browser
disable_browser_cache();
$range = (!empty($_REQUEST['range']) ? intval($_REQUEST['range']) : 500);
$range = ($range > 0 ? $range : 1 );

if ($_REQUEST['action'] == 'clean_all_temp') {
    $tpl->assign('clean_temp_msg', 1);

    //Clear the entire cache
    $tpl->clear_all_cache();

    //Clear all compiled template files
    $tpl->clear_compiled_tpl();
} elseif ($_REQUEST['action'] == 'build_meta') {
    $start_query = (!empty($_REQUEST['start']) ? intval($_REQUEST['start']) : 0);
    $total_processed = (!empty($_REQUEST['total_processed']) ? intval($_REQUEST['total_processed']) : 0);
    $type = (!empty($_REQUEST['type']) ? trim($_REQUEST['type']) : 'category');
    //$type            = ($type == 'category'                   ? 'category'                            : 'link');
    $meta_overwrite = (isset($_POST['meta_overwrite']) && $_POST['meta_overwrite'] == 1 ? 1 : 0);

    $total = (!empty($_REQUEST['total']) ? intval($_REQUEST['total']) : 0);
    if (empty($total))
        $total = $db->GetOne("SELECT COUNT(*) FROM " . $tables[$type]['name']);

    if ($type == 'category') {
        $sql = "SELECT `ID`, `TITLE`, `DESCRIPTION`, `META_KEYWORDS`, `META_DESCRIPTION` FROM `{$tables['category']['name']}` LIMIT {$start_query}, {$range}";
        $current_DB = $db->GetAll($sql);

        $processes = 0;

        if (is_array($current_DB) && !empty($current_DB)) {
            foreach ($current_DB as $key => $category) {
                //Build META keywords
                $category['META_KEYWORDS'] = (!empty($category['META_KEYWORDS']) ? $category['META_KEYWORDS'] : meta_keyw_by_subcat($category['ID']));

                $category['META_KEYWORDS'] = (!empty($category['META_KEYWORDS']) ? $category['META_KEYWORDS'] : $category['TITLE'] . ',' . DEFAULT_META_KEYWORDS);

                $category['META_KEYWORDS'] = (!empty($category['META_KEYWORDS']) ? clean_meta_keywords($category['META_KEYWORDS']) : '');

                //Build META description
                $category['META_DESCRIPTION'] = (!empty($category['META_DESCRIPTION']) ? $category['META_DESCRIPTION'] : $category['DESCRIPTION']);

                //Remove empty fields to set them as NULL
                if (empty($category['META_DESCRIPTION']))
                    unset($category['META_DESCRIPTION']);
                if (empty($category['META_KEYWORDS']))
                    unset($category['META_KEYWORDS']);
                if (empty($category['META_DESCRIPTION']))
                    unset($category['META_DESCRIPTION']);

                $where = " `ID` = " . $db->qstr($category['ID']);

                if ($db->AutoExecute($tables['category']['name'], $category, 'UPDATE', $where))
                    $processes++;

                unset($category, $current_DB[$key]);
            }
        }
        unset($current_DB);
    }
    elseif ($type == 'link') {
        $sql = "SELECT `ID`, `TITLE`, `DESCRIPTION`, `CATEGORY_ID`, `OWNER_NAME`, `META_KEYWORDS`, `META_DESCRIPTION` FROM `{$tables['link']['name']}` LIMIT {$start_query}, {$range}";

        $current_DB = $db->GetAll($sql);

        $processes = 0;

        if (is_array($current_DB) && !empty($current_DB)) {
            foreach ($current_DB as $key => $link) {
                //Build META keywords
                $link['META_KEYWORDS'] = (!empty($link['META_KEYWORDS']) ? $link['META_KEYWORDS'] : meta_keyw_link($link['ID']));

                $link['META_KEYWORDS'] = (!empty($link['META_KEYWORDS']) ? clean_meta_keywords($link['META_KEYWORDS']) : '');

                //Build META description
                $link['META_DESCRIPTION'] = (!empty($link['META_DESCRIPTION']) ? $link['META_DESCRIPTION'] : $link['DESCRIPTION']);

                $where = " `ID` = " . $db->qstr($link['ID']);

                if ($db->AutoExecute($tables['link']['name'], $link, 'UPDATE', $where))
                    $processes++;

                unset($link, $current_DB[$key]);
            }
        }
        unset($current_DB);
    }
    $next_start_query = $start_query + $range;
    $total_processed += $processes;

    if ($next_start_query <= $total) {
        $url = DOC_ROOT . "/conf_maintenance.php?r=1&action=build_meta&type={$type}&start={$next_start_query}&range={$range}&total_processed={$total_processed}&total={$total}";

        $build_type = _L('Building ##TYPE## META information.');
        $build_type = str_replace('##TYPE##', ucfirst(trim($type)), $build_type);

        $title_msg = $build_type;

        $cust_msg = '<p>';
        $cust_msg .= $build_type;
        $cust_msg .= '</p>';

        $cust_msg .= '<p>' . _L('Starting at database field') . ': <span class="important">' . $start_query . '</span></p>';
        $cust_msg .= '<p>' . _L('Stopping at database field') . ': <span class="important">' . $next_start_query . '</span></p>';
        $cust_msg .= '<p>' . _L('Queries performed (this session/total sessions)') . ': <span class="important">' . $processes . '/' . $total_processed . '</span></p>';
        $cust_msg .= '<p>' . _L('Total queries to process') . ': <span class="important">' . $total . '</span></p>';
        $cust_msg .= '<p>' . _L('Number of processes per cycle') . ': <span class="important">' . $range . '</span></p>';

        $cust_msg .= '<p class="notice">' . _L('Depending on the size of information to be processed, this action can take some time.') . '</p>';

        $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg);
        $tpl->assign('redirect', $redirect);
    }

    $next_start_query = $start_query + $range;
    $total_processed += $processes;

    if ($next_start_query <= $total) {
        $url = DOC_ROOT . "/conf_maintenance.php?r=1&action=build_meta&type={$type}&start={$next_start_query}&range={$range}&total_processed={$total_processed}&total={$total}";

        $build_type = _L('Building ##TYPE## META information.');
        $build_type = str_replace('##TYPE##', ucfirst(trim($type)), $build_type);

        $title_msg = $build_type;

        $cust_msg = '<p>';
        $cust_msg .= $build_type;
        $cust_msg .= '</p>';

        $cust_msg .= '<p>' . _L('Starting at database field') . ': <span class="important">' . $start_query . '</span></p>';
        $cust_msg .= '<p>' . _L('Stopping at database field') . ': <span class="important">' . $next_start_query . '</span></p>';
        $cust_msg .= '<p>' . _L('Queries performed (this session/total sessions)') . ': <span class="important">' . $processes . '/' . $total_processed . '</span></p>';
        $cust_msg .= '<p>' . _L('Total queries to process') . ': <span class="important">' . $total . '</span></p>';
        $cust_msg .= '<p>' . _L('Number of processes per cycle') . ': <span class="important">' . $range . '</span></p>';

        $cust_msg .= '<p class="notice">' . _L('Depending on the size of information to be processed, this action can take some time.') . '</p>';

        $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg);
        $tpl->assign('redirect', $redirect);
    } else {
        $url = DOC_ROOT . "/conf_maintenance.php?r=1";
        $title_msg = _L('META tags build status') . ': ' . _L('Complete');
        $cust_msg = _L('META tags build status') . ': ' . _L('Complete');
        $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
        $tpl->assign('redirect', $redirect);
    }
}

//RALUCA: JQuery validation related
$validators = array(
    'rules' => array(
        'range' => array(
            'required' => true,
            'remote' => array(
                'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
                'type' => "post",
                'data' => array(
                    'action' => "isNumber",
                    'table' => "banlist",
                    'field' => "range"
                )
            )
        )
    ),
    'messages' => array(
        'range' => array(
            'remote' => _L("Wrong range: should be a number.")
        )
    )
);

$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related
if (empty($_POST['meta-build-submit'])) {
} else {
    //RALUCA: JQuery validation related - server side.
    $validator = new Validator($validators);
    $validator_res = $validator->validate($_POST);
    //RALUCA: end of JQuery validation related - server side.

    if (empty($validator_res)) {
        $type = (!empty($_POST['meta_options']) ? trim($_POST['meta_options']) : 'category');
        //$type           = ($type == 'category'                   ? 'category'                        : 'link');
        $meta_overwrite = (isset($_POST['meta_overwrite']) && $_POST['meta_overwrite'] == 1 ? 1 : 0);
        $total = $db->GetOne("SELECT COUNT(*) FROM " . $tables[$type]['name']);

        if ($meta_overwrite == 1)
            $db->Execute("UPDATE " . $tables[$type]['name'] . " SET `META_KEYWORDS` = NULL, `META_DESCRIPTION` = NULL");

        $url = DOC_ROOT . "/conf_maintenance.php?r=1&action=build_meta&type={$type}&start=0&range={$range}&total={$total}";

        $cust_msg = _L('Depending on the size of information to be processed, this action can take some time.');

        $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, '', $cust_msg);
        $tpl->assign('redirect', $redirect);
    }
}


$tpl->assign('range', $range);
$tpl->assign('build_meta_options_radios', array(
    'category' => _L('Build META tags for category pages.'),
    'link' => _L('Build META tags for detail link pages.')
));
$tpl->assign('build_meta_options_checked', 'category');

$tpl->assign('meta_overwrite_radios', array(
    0 => _L('Empty Fields'),
    1 => _L('All fields') . ' (' . _L('Notice: overwrites all fields!') . ')'
));
$tpl->assign('meta_overwrite_checked', 0);

$tpl->assign($_POST);

$content = $tpl->fetch(ADMIN_TEMPLATE . '/conf_maintenance.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');

function meta_keyw_link($id=0) {
    global $db, $tables;

    if (empty($id))
        return false;

    $output = '';
    $lind_info = $db->GetRow("SELECT {$tables['link']['name']}.TITLE, {$tables['category']['name']}.TITLE AS `CATEGORY_TITLE`, {$tables['link']['name']}.OWNER_NAME FROM `{$tables['link']['name']}` LEFT JOIN `{$tables['category']['name']}` ON ({$tables['link']['name']}.CATEGORY_ID = {$tables['category']['name']}.ID) WHERE {$tables['link']['name']}.ID = " . $db->qstr($id));

    if (is_array($lind_info) && !empty($lind_info)) {
        $output = implode(',', $lind_info);
    }

    return trim($output);
}

function meta_keyw_by_subcat($id=0) {
    global $db, $tables;

    if (empty($id))
        return false;

    $output = '';
    $subcategs = $db->GetAssoc("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($id) . " ORDER BY `TITLE`");

    if (is_array($subcategs) && !empty($subcategs)) {
        $output = implode(',', $subcategs);
    }

    return trim($output);
}
?>