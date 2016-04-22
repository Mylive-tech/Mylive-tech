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

/**
 * Render menu element with partial
 *
 * @param array                    $menu
 * @param MKLib_View $partial
 * @param string                   $resultHtml
 */
function renderLevelsMenu($menu, $view, $level = 0, $resultHtml = '')
{
    $currLevel = $level++;
    foreach ($menu as $key => $menuItem) {
        $menuItem['LEVEL'] = $currLevel;
        $view->assign('page', $menuItem);
        $resultHtml .= $view->fetch(ADMIN_TEMPLATE.'/conf_menu_item.tpl');

        if (isset($menuItem['pages'])) {
            $resultHtml = renderLevelsMenu($menuItem['pages'], $view, $level, $resultHtml);
        } else {
        }
    }
    return $resultHtml;
}


$rss = array();
if(ENABLE_RSS and (!empty($search) or $category['ID'] > 0  or $list)) {
    $str = '';
    if (!empty($search)) {
      $str .= 'search=' . urlencode($search);
    } elseif ($list) {
      $str .= 'list=' . $list;
    } else {
      $str .= 'c=' . $category['ID'];
    }
    if ($sort) {
      $str .= '&amp;s=' . $sort;
    }
    if ($p) {
      $str .= '&amp;p=' . $p;
    }
    $rss = array('LABEL'=>_L('RSS'), 'URL'=> DOC_ROOT . '/rss?' . $str);
}
$pages = array(
    array('LABEL'=>_L('Links Latest'), 'URL'=>'latest'),
    array('LABEL'=>_L('Top Hits'), 'URL'=>'top'),
    array('LABEL'=>_L('Contact'), 'URL'=>'contact'),     
);
if(!empty($rss)){
  $pages[] = $rss;  
}
if (isset($phpldSettings['DISABLE_SUBMIT']) AND $phpldSettings['DISABLE_SUBMIT'] == 0) {
  array_unshift($pages, array('LABEL'=>_L('Submit Link'), 'URL'=>'submit'));    
}
if (!empty($_POST)) {

    $action = $_POST['action'];

    switch ($action) {
        case 'addPage':
            $data = array();
            $data['LABEL'] = $_POST['LABEL'];
            $data['URL'] = $_POST['URL'];
            $data['ORDER_ID'] = 99;

            $db->Replace($tables['menu_items']['name'], $data, 'ID', true);
            //die(mysql_error());
            http_custom_redirect('conf_menu.php');
            break;

        case 'saveMenuItem':
            $data = array();
            $data['LABEL'] = $_POST['LABEL'];
            $data['URL'] = $_POST['URL'];
            $data['ID'] = $_POST['ID'];

            $db->Replace($tables['menu_items']['name'], $data, 'ID', true);
            http_custom_redirect('conf_menu.php');
            break;

        case 'saveMenuLevels':
            $ids = $_POST['ids'];
            $order = 0;

            foreach ($ids as $page) {

                $data = array();
                $data['ID'] = $page['id'];
                $data['PARENT'] = $page['idParent'];
                $data['ORDER_ID'] = $order++;

                $db->Replace($tables['menu_items']['name'], $data, 'ID', true);
            }
            break;

        case 'removePage':
            $id = $_POST['idPage'];
            $db->Execute('DELETE FROM '.$tables['menu_items']['name'].' WHERE ID = '.$id);
            break;
    }
}

$exclude_submit_item = '';
if (isset($phpldSettings['DISABLE_SUBMIT']) AND $phpldSettings['DISABLE_SUBMIT'] == 1) {
  $exclude_submit_item = ' WHERE URL<>"submit" ';
}
$menuPages = $db->GetAll('SELECT * FROM '.$tables['menu_items']['name'].' '.$exclude_submit_item.' ORDER BY `ORDER_ID`');

$menuPages = renderLevelsMenu(buildMenuTree($menuPages), $tpl);


$tpl->assign('pages', $pages);
$tpl->assign('menuPages', $menuPages);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_menu.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>
