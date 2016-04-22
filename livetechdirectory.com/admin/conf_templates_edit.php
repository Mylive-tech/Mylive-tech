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
require_once 'code/Layout.php';
$sidebar_types = array('%'=>'%','px'=>'px');
 $tpl->assign('sidebar_types',  array('%'=>'%','px'=>'px'));
$layoutType = new Phpld_Layout();

$layoutType->readLayout('templates/' . TEMPLATE);
$xml = $layoutType->getXML();



$layouts['options'] = array(); //$xml->layout->field->option;
$layouts['coloroptions'] = array();
$layouts['zones'] = array();
$layouts['label'] = (string)$xml->layout->field->attributes()->label;
$layouts['sidebar1'] = (int) $xml->sidebars->sidebar[0]->attributes()->width;
$layouts['sidebar2'] = (int) $xml->sidebars->sidebar[1]->attributes()->width;
$layouts['sidebar1_type'] = $xml->sidebars->sidebar[0]->field->attributes()->default;
$layouts['sidebar2_type'] = $xml->sidebars->sidebar[1]->field->attributes()->default;
$layouts['submit']['value'] = (string)$xml->submit->attributes()->default;
$layouts['submit']['label'] = (string)$xml->submit->attributes()->label;
$layouts['color']['value'] = (string)$xml->color->field->attributes()->default;
$layouts['color']['label'] = (string)$xml->color->field->attributes()->label;
//$layouts['font']['value'] = $xml->font->field->attributes()->default;
//$layouts['font']['label'] = $xml->font->field->attributes()->label;
$layouts['widgetheading']['value'] = (int)$xml->widgetheading->attributes()->default;
$layouts['widgetheading']['label'] = (string)$xml->widgetheading->attributes()->label;
$layouts['titleheading']['value'] = (int)$xml->titleheading->attributes()->default;
$layouts['titleheading']['label'] = (string)$xml->titleheading->attributes()->label;

$k = 0;
foreach ($xml->layout->field->children() as $key => $option) {
    $layouts['options'][$k]['key'] = (int) $xml->layout->field->option[$k]->attributes()->value;
    $layouts['options'][$k]['value'] = $option;
    $layouts['options'][$k]['image'] = DIRECTORY_ROOT.'/templates/' . TEMPLATE . '/images/styles/' . $layouts['options'][$k]['key'] . '.jpg';
    $k++;
}
$k = 0;
foreach ($xml->zones->children() as $key => $zone) {
    $layouts['zones'][$k]['name'] = $xml->zones->zone[$k]->attributes()->name;
    $layouts['zones'][$k]['label'] = ucwords(str_replace("_", " ", strtolower((string)$xml->zones->zone[$k]->attributes()->name)));
    $layouts['zones'][$k]['value'] = (int) $xml->zones->zone[$k]->attributes()->layout;
    $k++;
}

$k = 0;
foreach ($xml->color->field->children() as $key => $option) {
    $layouts['coloroptions'][$k]['key'] = (string)$xml->color->field->option[$k]->attributes()->value;
    $layouts['coloroptions'][$k]['value'] = $option;
    $k++;
}

$k = 0;
foreach ($xml->headings->children() as $key => $heading) {
    $layouts['heading'][$k]['label'] = ucwords(str_replace("_", " ", strtolower((string)$xml->headings->heading[$k]->attributes()->name)));
    $layouts['heading'][$k]['value'] = (int) $xml->headings->heading[$k]->attributes()->value;
    $k++;
}

$available_templates = get_templates('../templates/');

$current_template = array();
foreach ($available_templates as $key => $template) {
    if (strtolower(TEMPLATE) == strtolower($template['theme_path'])) {
        $current_template = $template;
        unset($available_templates[$key], $template);
    }
}
unset($available_templates);

if ($_REQUEST['action'] == 'edit' && !empty($_REQUEST['filename'])) {
    if (strchr($_REQUEST['filename'], "/")) {
        $filename = trim(substr($_REQUEST['filename'], strrpos($_REQUEST['filename'], '/') + 1));
    } else {
        $filename = trim($_REQUEST['filename']);
    }

    $tpl->assign('file_name', $filename);

    $extension = substr($filename, strrpos($filename, '.') + 1);

    if ($extension == "css") {
        $subpath = "style/";
    } else {
        $subpath = "";
    }

    $file_path = INSTALL_PATH . 'templates/' . TEMPLATE . '/' . $subpath . $filename;

    $_REQUEST['file_content'] = (!empty($_REQUEST['file_content']) ? $_REQUEST['file_content'] : filecontent($file_path));

    if ($_REQUEST['submit'] == 'Save') {

        if ($extension != "css" && $extension != "tpl") {
            $file_saved = false;
        } else {
            if (!write_to_file($file_path, $_REQUEST['file_content']))
                $file_saved = false;
            else {
                //Clear the entire cache
                $tpl->clear_all_cache();

                //Clear all compiled template files
                $tpl->clear_compiled_tpl();

                $file_saved = true;
            }
        }
        $tpl->assign('file_saved', $file_saved);

        //Redirecting
        $url = DOC_ROOT . "/conf_templates_edit.php?";
        $title_msg = (!$file_saved ? _L('An error occured while saving.') : _L('File saved'));
        $status = (!$file_saved ? 0 : 1);

        if ($status == 0)
            $url .= 'r=1&file_saved=' . $status;
        else
            $url .= 'file_saved=' . $status . '&action=edit&filename=' . trim($_REQUEST['filename']);

        $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, '', $status);
        $tpl->assign('redirect', $redirect);
    }
    $tpl->assign('edit_screen', true);
}elseif ($_POST['settings'] == 'save') {

    $layoutType = $_POST['layoutType'];
    $sidebar1 = $_POST['sidebar1'];
    $sidebar2 = $_POST['sidebar2'];
    $sidebar1_type = $_POST['sidebar1_type'];
    $sidebar2_type = $_POST['sidebar2_type'];
    $color = $_POST['color'];
    $font = $_POST['font'];
    $widgetheading = $_POST['widgetheading'];
    $titleheading = $_POST['titleheading'];
    $submitwidgets = $_POST['submitwidgets'];
    $k = 0;
    foreach ($_POST['zone'] as $key => $value) {
        $xml->zones->zone[$k]->attributes()->name = $key;
        $xml->zones->zone[$k]->attributes()->layout = $value;
        $k++;
    }

    //$xml->layout->field->attributes()->default = $layoutType;
    $xml->color->field->attributes()->default = $color;
    $xml->submit->attributes()->default = $submitwidgets;
    $xml->widgetheading->attributes()->default = $widgetheading;
    $xml->titleheading->attributes()->default = $titleheading;
    $xml->sidebars->sidebar[0]->attributes()->width = $sidebar1;
    $xml->sidebars->sidebar[1]->attributes()->width = $sidebar2;
    $xml->sidebars->sidebar[0]->field->attributes()->default = $sidebar1_type;
    $xml->sidebars->sidebar[1]->field->attributes()->default = $sidebar2_type;
    $status = 1;
    if ($xml->saveXML(INSTALL_PATH . 'templates/' . TEMPLATE . '/template.xml')) {
        //$layouts['selected'] = (int) $xml->layout->field->attributes()->default;
        $layouts['color']['value'] = $xml->color->field->attributes()->default;
        $layouts['color']['label'] = $xml->color->field->attributes()->label;
        $layouts['submit']['value'] = $xml->submit->attributes()->default;
        $layouts['submit']['label'] = $xml->submit->attributes()->label;
        $layouts['sidebar1'] = (int) $xml->sidebars->sidebar[0]->attributes()->width;
        $layouts['sidebar2'] = (int) $xml->sidebars->sidebar[1]->attributes()->width;
	$layouts['sidebar1_type'] = $xml->sidebars->sidebar[0]->field->attributes()->default;
        $layouts['sidebar2_type'] = $xml->sidebars->sidebar[1]->field->attributes()->default;
        $layouts['widgetheading']['value'] = (int)$xml->widgetheading->attributes()->default;
        $layouts['widgetheading']['label'] = $xml->widgetheading->attributes()->label;
        $layouts['titleheading']['value'] = (int)$xml->titleheading->attributes()->default;
        $layouts['titleheading']['label'] = $xml->titleheading->attributes()->label;
        $k = 0;
        foreach ($xml->zones->children() as $key => $zone) {
            $layouts['zones'][$k]['name'] = $xml->zones->zone[$k]->attributes()->name;
            $layouts['zones'][$k]['label'] = ucwords(str_replace("_", " ", strtolower((string)$xml->zones->zone[$k]->attributes())));
            $layouts['zones'][$k]['value'] = (int) $xml->zones->zone[$k]->attributes()->layout;
            $k++;
        }

        $status = 1;
        $title_msg = _L('Saving template settings');
    } else {
        $status = 0;
        $title_msg = _L('An error occured while saving. template.xml file not writeable');
    }

    $url = DOC_ROOT . "/conf_templates_edit.php?";
    $url .= 'r=1';

    $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, $title_msg, '', $status);
} else {
    $css_files = get_template_files(INSTALL_PATH . 'templates/' . TEMPLATE . '/style/', 'css');
    $tpl_files = get_template_files(INSTALL_PATH . 'templates/' . TEMPLATE . '/', 'css,tpl');
    $template_files = array_merge($css_files, $tpl_files);
    //Free memory
    unset($css_files, $tpl_files);
}

function get_template_files($dirname = INSTALL_PATH, $extension = "css,tpl") {
    $extension = str_replace(" ", "", $extension);
    $ext = explode(",", $extension);
    $output = array();
    if ($handle = @ opendir($dirname)) {
        while (false !== ($file = @ readdir($handle)))
            for ($i = 0; $i < sizeof($ext); $i++)
                if (strstr($file, "." . $ext[$i]) && !empty($ext[$i]) && strpos($file, "~") === false)
                    $output[] = array('name' => $file, 'path' => $dirname . $file, 'permission' => file_status($dirname . $file));
        @ closedir($handle);
    }
    unset($extension, $ext, $handle, $dirname, $file);
    return $output;
}

if (isset($file_saved))
    $tpl->assign('file_saved', $file_saved);

if ($template_files)
    $tpl->assign('template_files', $template_files);

$tpl->assign('current_template', $current_template);

$tpl->assign($_REQUEST);

//Check if preview thumbnails can be created
if (!extension_loaded('gd')) {
    //No GD library available => no preview
    $showPreview = 0;
    $thumbType = 0;
} else {
    if (!function_exists('gd_info')) {
        //GD info cannot be gathered => no preview
        $showPreview = 0;
        $thumbType = 0;
    } else {
        //Get GD info
        $gGdInfo = gd_info();

        if ($gGdInfo['PNG Support'] == 1) {
            //PNG support available [best]
            $showPreview = 1;
            $thumbType = 3;
        } elseif ($gGdInfo['JPG Support'] == 1) {
            //JPG support available [good]
            $showPreview = 1;
            $thumbType = 2;
        } elseif ($gGdInfo['GIF Create Support'] == 1) {
            //GIF support available [it's ok]
            $showPreview = 1;
            $thumbType = 1;
        } else {
            //No PNG, JPG or GIF support => no preview
            $showPreview = 0;
            $thumbType = 0;
        }
    }
}

$tpl->assign('showPreview', $showPreview);
$tpl->assign('thumbType', $thumbType);
$tpl->assign('layout', $layouts);

$content = $tpl->fetch(ADMIN_TEMPLATE . '/conf_templates_edit.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE . '/main.tpl');
?>