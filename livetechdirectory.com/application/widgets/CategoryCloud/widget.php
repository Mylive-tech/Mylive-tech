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
  # Copyright (C) 2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
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
  # @copyright      2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
  # @projectManager David DuVal <david@david-duval.com>
  # @package        PHPLinkDirectory
  # @version        5.0 Codename Transformer
  # ################################################################################
 */
class Widget_CategoryCloud extends Phpld_Widget {

    function getContent() {
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();

        $set = $this->getFrontSettings();
        $min_font_size = 9;
        $max_font_size = 20;
        $limit = $set['MAXIMUM'];

        $terms = $db->GetAll("SELECT `TITLE`, `HITS`, `CACHE_URL` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' ORDER BY `PARENT_ID` ASC LIMIT 0, $limit");
        $min_qty = $terms[count($terms) - 1]['HITS'];
        $max_qty = $terms[0]['HITS'];

        // find the range of values
        $spread = $max_qty - $min_qty;
        if ($spread == 0) {
            $spread = 1;
        }
        $terms = $this->custom_shuffle($terms);
        $cloud_html = '';
        $cloud_tags = array(); // create an array to hold tag code
        $collection = new Phpld_Model_Collection('Model_Category_Entity');
        $collection->setElements($terms);
        foreach ($collection as $category) {
            $size = $min_font_size + ($category['HITS'] - $min_qty + 1) * ($max_font_size - $min_font_size) / $spread;
            $cloud_tags[] = '<a style="font-size: ' . floor($size) . 'px' . '" class="tag_cloud" href="' . $category->getUrl() . '" title="' . $category['TITLE'] . '">' . $category['TITLE'] . '</a>';
        }

        $cloud_html = join(" ", $cloud_tags) . " ";

        $this->tpl->assign("CLOUD", $cloud_html);

        return $this->tpl->fetch('content.tpl');
    }

    function custom_shuffle($my_array = array()) {
        $copy = array();
        while (count($my_array)) {
            // takes a rand array elements by its key
            $element = array_rand($my_array);
            // assign the array and its value to an another array
            $copy[$element] = $my_array[$element];
            //delete the element from source array
            unset($my_array[$element]);
        }
        return $copy;
    }

}

?>