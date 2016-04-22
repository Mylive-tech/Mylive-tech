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
 
      $showImport = 1;

      if (AJAX_CAT_SELECTION_METHOD == 0)
      {
         $categs = get_categs_tree();
         $tpl->assign('categs', $categs);
      }

      $importResults = array();

      	$fp = @ fopen ('../me_log.txt','w');
	
		@ fwrite ($fp, "\n\n");
		
		$import = $_REQUEST['import'];
		
		@ fwrite ($fp, $import."\n\n");
		
		//need to format imported string to array
		//string format looks like: title||url||content<end_line>
		
		$aux1 = array();
		$aux2 = array();
		$importResults = array();
		
		$aux1 = explode('[end_line]', $import);
		
		for ($i = 0; $i<count($aux1); $i++) {
			$aux2 = explode('||', $aux1[$i]);
			if ($aux1[$i] != '') {
				
				$importResults[$i]['TITLE'] = un_escape(clean_string($aux2[0]));
				
				$importResults[$i]['URL'] = parseDomain($aux2[2], 1);
				//Correct URL
	            if (strlen (trim ($importResults[$i]['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $importResults[$i]['URL']))
	               $importResults[$i]['URL'] = "http://".$importResults[$i]['URL'];
				
	           	$importResults[$i]['DESCRIPTION'] = un_escape(clean_string($aux2[1]));
	           	
				$importResults[$i]['DOMAIN'] = parseDomain($aux2[2]);
				
				//Build author name [domain name??]
	            $importResults[$i]['OWNER_NAME'] = $importResults[$i]['DOMAIN'];
	            
	            //Build author email [webmaster@domain]
				$importResults[$i]['OWNER_EMAIL'] = 'webmaster@'.$importResults[$i]['DOMAIN'];
				
				$importResults[$i]['ID'] = $i;
				$db->Execute("INSERT INTO `".$tables['link_to_import']['name']."` (`ID`, `TITLE`, `DESCRIPTION`, `URL`, `DOMAIN`, `OWNER_EMAIL`) 
							VALUES ('', ".$db->qstr($importResults[$i]['TITLE']).", ".$db->qstr($importResults[$i]['DESCRIPTION']).",
							".$db->qstr($importResults[$i]['URL']).", ".$db->qstr($importResults[$i]['DOMAIN']).", ".$db->qstr($importResults[$i]['OWNER_EMAIL']).")");
				
			}
		}
		
		fwrite($fp,"--------".count($importResults));
		
		foreach($importResults as $key => $value){
			fwrite($fp,$key."\n\n");
			foreach($value as $k => $v){
				fwrite($fp,$k.": ".$v."\n\n");
			}
			fwrite($fp,"--------");
		}
      
      $tpl->assign('importResults', $importResults);
      
      $content = $tpl->fetch(ADMIN_TEMPLATE.'/spider.tpl');
	
?>