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
 
class Widget_TagCloud extends Phpld_Widget {

	function getContent() {
		global $db;
		global $tables;

		$set = $this->getFrontSettings();
		$min_font_size = 9;
		$max_font_size = 20;
		$limit = $set['MAXIMUM'];
		
		if ($set['LINKS'] == "Yes" && $set['CATEGORIES'] == "Yes") {
			if (REQUIRE_REGISTERED_USER == 0) {
				$owner = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
			} else {
				$owner = "";
			}
			$terms = $db->GetAll("SELECT `TITLE`, SUM(`HITS`) AS `HITS` FROM 
								(SELECT `TITLE`, `HITS` FROM `{$tables['link']['name']}` 
									WHERE `STATUS` = '2' ".$owner." GROUP BY (`TITLE`) 
								UNION
								  SELECT `TITLE`, `HITS` FROM `{$tables['category']['name']}` 
									WHERE `STATUS` = '2' GROUP BY (`TITLE`)
								)  derived
								GROUP BY `TITLE` 
								ORDER BY `HITS` DESC LIMIT 0, $limit"); 	
			$min_qty = $terms[count($terms)-1]['HITS'];
			$max_qty = $terms[0]['HITS'];	
		} elseif ($set['LINKS'] == "Yes") {
			if (REQUIRE_REGISTERED_USER == 0) {
				$owner = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
			} else {
				$owner = "";
			}
			$terms = $db->GetAll("SELECT `TITLE`, `HITS` FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' ".$owner." ORDER BY `HITS` DESC LIMIT 0, $limit");
			$min_qty = $terms[count($terms)-1]['HITS'];
			$max_qty = $terms[0]['HITS'];		
			
		} elseif ($set['CATEGORIES'] == "Yes") {
			$terms = $db->GetAll("SELECT `TITLE`, `HITS` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' ORDER BY `HITS` DESC LIMIT 0, $limit");
			$min_qty = $terms[count($terms)-1]['HITS'];
			$max_qty = $terms[0]['HITS'];
		} else {
			$empty = 1;
		}
		$new_terms = array();
		for ($i=0; $i<count($terms); $i++) {
			if(count(trim($terms[$i]['TITLE'])) == 0){
			} else {
				$k = count($new_terms);
				if ($k<$limit) {
					$words = explode(" ", $terms[$i]['TITLE']);
					for ($j=0; $j<count($words); $j++) {
						if ($new_terms[$words[$j]] == '') {
							$new_terms[$words[$j]] = $terms[$i]['HITS'];
						} else {
							$new_terms[$words[$j]] = $terms[$i]['HITS'] + $new_terms[$words[$j]];
						}
					}
				}
			}
		}
		$terms = array();
		$terms = $new_terms;
        // find the range of values
        $spread = $max_qty - $min_qty;
        if ($spread == 0) {
                $spread = 1;
        }
		$terms = $this->custom_shuffle($terms);
		$cloud_html = '';
		$cloud_tags = array(); // create an array to hold tag code
		foreach ($terms as $k=>$v) {
			$size = $min_font_size + ($v - $min_qty+1) * ($max_font_size - $min_font_size)/$spread;
			$cloud_tags[] = '<a class ="boxSponsoredA" style="font-size: '.floor($size).'px'.'" class="tag_cloud" href="'.DOC_ROOT.'/search.php?search='.$k.'" title="'.$k.'">'.htmlspecialchars(stripslashes($k)).'</a>';
		}	

		$cloud_html = join(" ", $cloud_tags) . " ";
		
		$this->tpl->assign("CLOUD", $cloud_html);
		$this->tpl->assign("TITLE", $set['TITLE']);

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