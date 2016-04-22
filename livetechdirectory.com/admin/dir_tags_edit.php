<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 require_once 'init.php';

if ($_REQUEST['action']) {
    list ($action, $id) = explode(':', $_REQUEST['action']);
    $action = strtoupper(trim($action));
    $tpl->assign('action', strtoupper($action));
}

$status = $_REQUEST['status'];
$validators = array(
    'rules' => array(
        'TITLE' => array(
            'required' => true,
            'minlength' => 2,
            'maxlength' => 50
        ),
    )
);
$tagModel = new Model_Tag();
switch ($action) {
    default:
    case 'N' : //New
        if (!empty ($_POST)) {

            $tagModel->addTag($_POST['TITLE'], $_POST['STATUS']);

            $url       = DOC_ROOT."/dir_tags_edit.php?action=N";
            $title_msg = _L('Tag added');
            $status    = 1;
            $cust_msg = '';
            $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
            $tpl->assign('redirect', $redirect);
        }
        break;

    case 'E' : //Edit
        $db->getRow();
        if (!empty ($_POST)) {

            $data = array(
                'ID' => $id,
                'TITLE' => $_POST['TITLE'],
                'STATUS' => $_POST['STATUS'],
            );

            $replaceResult = db_replace('tags', $data, 'ID');

            $url       = DOC_ROOT."/dir_tags_edit.php?action=E:".$id;
            $title_msg = _L('Tag saved');

            $cust_msg = '';
            $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
            $tpl->assign('redirect', $redirect);
        } else {
            $data = $db->GetRow("SELECT * FROM `{$tables['tags']['name']}` WHERE `ID` = " . $db->qstr($id));
            $tpl->assign('data', $data);
        }
        break;
    case 'D' : //Edit
        $db->execute("DELETE FROM `{$tables['tags']['name']}` WHERE ID = $id");
        $db->execute("DELETE FROM `{$tables['tags_links']['name']}` WHERE TAG_ID = $id");

        $url       = $_SERVER['HTTP_REFERER'];
        $title_msg = _L('Tag deleted');

        $cust_msg = '';
        $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
        $tpl->assign('redirect', $redirect);

        break;
}
$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);
$content = $tpl->fetch(ADMIN_TEMPLATE . '/dir_tags_edit.tpl');
$tpl->assign('content', $content);
$tpl->assign('validators', $vld);
//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');
