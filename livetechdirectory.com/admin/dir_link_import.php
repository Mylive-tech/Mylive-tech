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

switch ($_REQUEST['act']) {
    case 'download_sample':
        $type = $_GET['type'];        
        $submitItem = $db->getRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE ID=".$type);
        $submitItems = getSubmitItems($type);

        $csv = array();
        foreach ($submitItems as $item) {
            $csv[] = $item['FIELD_NAME'];
        }
        if ($type==4)
            $csv[] = "IMAGE";
        
        $csv = implode(';',$csv);

        Header('Content-Description: File Transfer');
        Header('Content-Type: text/csv');
        Header('Content-Disposition: attachment; filename="' .$submitItem['NAME']. '.csv"');
        Header('Content-Transfer-Encoding: Binary');
        Header('Content-Length: '.strlen($csv));
        Header('Cache-Control: private');

        echo $csv;
        exit();
        break;

    case 'import':
        $type = $_POST['type'];
        $category = $_POST['CATEGORY_ID'];
        $submitItem = $db->getRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE ID=".$type);
        $submitItems = getSubmitItems($type);
        $linkModel = new Model_Link();

        $data = array(
            'CATEGORY_ID' => $category,
            'LINK_TYPE' => $type,
            'STATUS' => 2,
            'DATE_ADDED' => date ('Y-m-d H:i:s'),
			'RATING' => 0,
			'VOTES' => 0,
			'COMMENT_COUNT' => 0
        );
        $map = array();
        $first = false;
        if ($_FILES['IMPORT_FILE']['error'] == UPLOAD_ERR_OK) {
            try {
                $file = fopen($_FILES['IMPORT_FILE']['tmp_name'], 'r');

                $result = false;
                while (($row = fgetcsv($file, 3000, $_REQUEST['delimiter'])) !== false) {
                    if (!$first) {
                        $first = true;
                        $map = $row;
                        continue;
                    }
                    if (!empty($map)) {
                        foreach ($map as $key=>$field) {
                            $data[$field] = $row[$key];
                        }
                        $result = db_replace('link', $data, 'ID');
                    }
                    
                    if ($result == 2) {
                        // Update URL
                        $id = $db->Insert_ID();
                        //var_dump($id, $data['TITLE'], $linkModel->seoUrl($data['TITLE'], $id));
                        $seoUrl = $linkModel->seoUrl($data, $id);
                        $db->execute('UPDATE '.$tables['link']['name'].' SET `CACHE_URL` = "'.$seoUrl.'" WHERE ID = '.$id);

                        // Save video image
                        if (Model_Link_Entity::TYPE_VIDEO == $type) {
                            $oembed = Model_Link_Handler_Oembed::getInstance();
                            $provider = $oembed->getProvider($data['URL']);

                            $args = array('width' => 640, 'height' => 390);
                            $photoData = $oembed->fetch($provider, $data['URL'], $args);
                            if ($photoData) {
                                $photoData->type = 'photo';
                                $photoData->width = 200;

                                $ext = strtolower(end(explode('.', $photoData->thumbnail_url)));
                                $name = $id . "_1." . $ext;
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
                                    $db->Execute("UPDATE `{$tables['link']['name']}` SET `IMAGE` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($id));
                                    $data['IMAGE'] = $name;
                                    unlink($filename);
                                } else {
                                    print "Error saving file";
                                }
                            }
                        }
                        elseif (Model_Link_Entity::TYPE_PICTURE == $type) {                            
                            $url = $data['IMAGE'];
                            $install_path = str_replace('\\', '/', INSTALL_PATH);
                            $dir = $install_path . 'uploads/';
                            $filePath = $dir . basename($url);

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
                            $name = $id . "_2" . "." . $path['extension'];
                            
                            resizeImg($filePath, $install_path . 'uploads/' . $name, 400, 400);
                            resizeImg($filePath, $install_path . 'uploads/thumb/' . $name, 150, 150);
                            
                            $db->Execute("UPDATE `{$tables['link']['name']}` SET `IMAGE` = " . $db->qstr($name) . " WHERE `ID`=" . $db->qstr($id));
                            $data['IMAGE'] = $name;
                            unlink($filePath);
                        }
                    }
                }
                $url       = DOC_ROOT."/dir_link_import.php";
                $title_msg = _L('Links imported');
                $status    = 1;
                $cust_msg = '';
                $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
                $tpl->assign('redirect', $redirect);
            } catch (Exception $e) {
                $url       = DOC_ROOT."/dir_link_import.php";
                $title_msg = $e->getMessage();
                $status    = 1;
                $cust_msg = '';
                $redirect  = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, $cust_msg, 1);
                $tpl->assign('redirect', $redirect);
            }
        }

        break;
}

if (ADMIN_CAT_SELECTION_METHOD == 0 || $link_type_details[$linktypeid]['MULTIPLE_CATEGORIES'] != '') {
    $categs = get_categs_tree();
    $tpl->assign('categs', $categs);
}

$linkTypes = $db->getAll("SELECT * FROM `{$tables['link_type']['name']}` ORDER BY ID");
foreach ($linkTypes as $type) {
    $linkTypesSel[$type['ID']] = $type['NAME'];
}
$delimiters = array(
    ',' => 'Comma',
    ';' => 'Semicolon',
    "\t" => 'Tab',
    '|' => 'Pipe',
    ':' => 'Colon'
);
$tpl->assign('delimiters', $delimiters);
$tpl->assign('link_types', $linkTypes);
$tpl->assign('link_types_sel', $linkTypesSel);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_link_import.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

function getSubmitItems($type)
{
    global $db, $tables;
    $submitItems = $db->getAll(
        "SELECT si.* FROM `{$tables['submit_item']['name']}` si
            INNER JOIN `{$tables['submit_item_status']['name']}` sus ON si.ID=sus.ITEM_ID
            WHERE sus.STATUS = 2 AND sus.LINK_TYPE_ID = $type AND si.TYPE IN ('STR', 'CAT', 'TXT')
            ORDER BY si.ORDER_ID
            "
    );

    $submitItemsRes = array();
    foreach ($submitItems as $key=>$val) {
        if ($val['FIELD_NAME'] == 'CATEGORY_ID') {
            continue;
        }
        $submitItemsRes[$val['FIELD_NAME']] = $val;
    }

    return $submitItemsRes;
}

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>
