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

$id = strtolower(( isset ($_REQUEST['id']) ? trim ($_REQUEST['id']) : (isset ($_REQUEST['ID']) ? trim ($_REQUEST['ID']) : 0) ));

$id = preg_replace ('`(id[_]?)`', '', $id);
$id = (preg_match ('`^[\d]+$`', $id) ? intval ($id) : 0);
if ($id == 0){
echo 'DOS attempt blocked by PLD Protection';
die();
}
if(USE_COOKIES == '1'){

if ($id)
{
   if (empty ($_COOKIE['HitsLinks_'.$id]))
   {
      $db->Execute("UPDATE `{$tables['link']['name']}` SET `HITS` = `HITS` + 1 WHERE `ID` = ".$db->qstr($id));
      $cookie_name = 'HitsLinks_'.$id;
      if(defined(LIMIT_HITS_TIME))
	 $cookie_time = (LIMIT_HITS_TIME * 3600);
      else
	  $cookie_time = 3600;
      setcookie ($cookie_name, 'link_id='.$id.'&ip='.$client_info['IP'], time() + $cookie_time);
   }

   unset ($cookie_name, $cookie_time);
}
}
else{
	if ($id)
{

   $HitInfo = $db->GetRow("SELECT * FROM `{$tables['hitcount']['name']}` WHERE `LINK_ID` = ".$db->qstr($id)." AND `IP` = ".$db->qstr($client_info['IP']));
   if (!empty ($HitInfo))
   {
      $current_time = date ('Y/m/d H:i:s');
      $SecondsDiff  = dateTimeDifference($current_time, $HitInfo['LAST_HIT']);
      $Diff         = second2hour($SecondsDiff);

      if ($Diff > LIMIT_HITS_TIME)
      {
         $db->Execute("UPDATE `{$tables['link']['name']}` SET `HITS` = `HITS` + 1 WHERE `ID` = ".$db->qstr($id));
         $where = '`ID` = '.$db->qstr($HitInfo['ID']);
         $HitInfo['LAST_HIT'] = $current_time;
         $db->AutoExecute($tables['hitcount']['name'], $HitInfo, 'UPDATE', $where);
      }
   }
   else
   {
      $db->Execute("UPDATE `{$tables['link']['name']}` SET `HITS` = `HITS` + 1 WHERE `ID` = ".$db->qstr($id));
      $HitInfo['LINK_ID'] = $id;
      $HitInfo['IP']      = $client_info['IP'];
      $db->AutoExecute($tables['hitcount']['name'], $HitInfo, 'INSERT', false);
   }

   unset ($HitInfo, $current_time, $Diff, $SecondsDiff);
}
}

?>