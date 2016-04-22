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

//Set defaul (un)serializing encoding
$serializer_encoding = 'base64';

$start = 0;
$processes = 0;

$finished = (isset ($_REQUEST['finish']) && $_REQUEST['finish'] == 1 ? 1 : 0);
$tpl->assign('finished', $finished);
$expired_recpr = (isset ($_REQUEST['expired']) && $_REQUEST['expired'] > 0 ? intval ($_REQUEST['expired']) : 0);
$tpl->assign('expired_recpr', $expired_recpr);

if (isset ($_REQUEST['action']) && $_REQUEST['action'] == 'validate')
{
   $VALIDATE_LINKS    = (isset ($_REQUEST['VALIDATE_LINKS'])    && $_REQUEST['VALIDATE_LINKS']    == 1 ? 1 : 0);
   $VALIDATE_RECPR    = (isset ($_REQUEST['VALIDATE_RECPR'])    && $_REQUEST['VALIDATE_RECPR']    == 1 ? 1 : 0);
   $OwnerNotification = (isset ($_REQUEST['OwnerNotification']) && $_REQUEST['OwnerNotification'] == 1 ? 1 : 0);
 
   $IL = (isset ($_REQUEST['IL']) ? simple_unserialize($_REQUEST['IL'], $serializer_encoding) : array () );
   $AL = (isset ($_REQUEST['AL']) ? simple_unserialize($_REQUEST['AL'], $serializer_encoding) : array () );
   $IR = (isset ($_REQUEST['IR']) ? simple_unserialize($_REQUEST['IR'], $serializer_encoding) : array () );
   $AR = (isset ($_REQUEST['AR']) ? simple_unserialize($_REQUEST['AR'], $serializer_encoding) : array () );

   $categ           = (preg_match ('`^[\d]+$`', $_REQUEST['categ']) && $_REQUEST['categ'] > 0 ? intval ($_REQUEST['categ']) : 0);
   $range           = (preg_match ('`^[\d]+$`', $_REQUEST['range']) && $_REQUEST['range'] > 0 ? intval ($_REQUEST['range']) : 200);
   $delay           = (preg_match ('`^[\d]+$`', $_REQUEST['delay']) && $_REQUEST['delay'] > 0 ? intval ($_REQUEST['delay']) : 1);
   $start_query     = (!empty ($_REQUEST['start']) ? intval ($_REQUEST['start']) : 0);
   $total_processed = (!empty ($_REQUEST['total_processed']) ? intval ($_REQUEST['total_processed']) : 0);
   $total           = (!empty ($_REQUEST['total']) ? intval ($_REQUEST['total']) : 0);

   //Recalculate total links to be processed
   //in case it could be not determined from the URL
   if (empty ($total))
   {
      $where = '1';
      if ($categ > 0)
         $where = "`CATEGORY_ID` = ".$db->qstr($categ);

      //Calculate total links to be processed
      $total  = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE {$where}");
      
      //RALUCA: 4.1 related
      $total += $db->GetOne("SELECT COUNT(*) FROM `{$tables['additional_category']['name']}` WHERE {$where}");
      //RALUCA: end of 4.1 related
   }

   if ($VALIDATE_LINKS == 1 || $VALIDATE_RECPR == 1)
      $start = 1;
}
else
{
   if (empty ($_POST['submit']))
   {
      $VALIDATE_LINKS = 1;
      $VALIDATE_RECPR = 1;
	  $OwnerNotification = 1;
      $IL = array (1, 2);
      $AL = array ();
      if (RECPR_NOFOLLOW  > 0)
         $IR = array ();
      else
         $IR = array (1, 2);

      $AR    = array ();

      $range = 20;
   }
   else
   {
      $VALIDATE_LINKS    = (isset ($_POST['VALIDATE_LINKS'])    && $_POST['VALIDATE_LINKS']    == 1 ? 1 : 0);
      $VALIDATE_RECPR    = (isset ($_POST['VALIDATE_RECPR'])    && $_POST['VALIDATE_RECPR']    == 1 ? 1 : 0);
      $OwnerNotification = (isset ($_POST['send_notification']) && $_POST['send_notification'] == 1 ? 1 : 0);
	
      $IL = (isset ($_POST['IL']) ? $_POST['IL'] : array () );
      $AL = (isset ($_POST['AL']) ? $_POST['AL'] : array () );
      $IR = (isset ($_POST['IR']) ? $_POST['IR'] : array () );
      $AR = (isset ($_POST['AR']) ? $_POST['AR'] : array () );

      $categ = (preg_match ('`^[\d]+$`', $_POST['CATEGORY_ID']) && $_POST['CATEGORY_ID'] > 0 ? intval ($_POST['CATEGORY_ID']) : 0);
      $range = (preg_match ('`^[\d]+$`', $_POST['range']) && $_POST['range'] > 0 ? intval ($_POST['range']) : 200);
      $delay = (preg_match ('`^[\d]+$`', $_POST['delay']) && $_POST['delay'] > 0 ? intval ($_POST['delay']) : 1);

      if ($VALIDATE_LINKS == 0 && $VALIDATE_RECPR == 0)
         $tpl->assign('error', 1);
      else
      {
         $where = '1';
         if ($categ > 0)
            $where = "`CATEGORY_ID` = ".$db->qstr($categ);

         //Reset reciprocal expired field for links
         $db->Execute("UPDATE `{$tables['link']['name']}` SET `RECPR_EXPIRED` = '0' WHERE {$where}");

         //RALUCA: 4.1 related
         $db->Execute("UPDATE `{$tables['link']['name']}` SET `RECPR_EXPIRED` = '0'
                        WHERE `ID` IN (SELECT `LINK_ID` FROM `{$tables['additional_category']['name']}` WHERE {$where})");
         //RALUCA: end of 4.1 related

         //Calculate total links to be processed
         $total_links = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE {$where}");

         //RALUCA: 4.1 related
         $total_links += $db->GetOne("SELECT COUNT(*) FROM `{$tables['additional_category']['name']}` WHERE {$where}");
         //RALUCA: end of 4.1 related


         $url  = "dir_validate.php?action=validate";
         $url .= "&VALIDATE_LINKS={$VALIDATE_LINKS}&VALIDATE_RECPR={$VALIDATE_RECPR}&OwnerNotification={$OwnerNotification}";
         $url .= "&IL=".simple_serialize($IL, $serializer_encoding);
         $url .= "&AL=".simple_serialize($AL, $serializer_encoding);
         $url .= "&IR=".simple_serialize($IR, $serializer_encoding);
         $url .= "&AR=".simple_serialize($AR, $serializer_encoding);
         $url .= "&categ={$categ}";
         $url .= "&range={$range}";
         $url .= "&start=0";
         $url .= "&total_processed=0";
         $url .= "&total={$total_links}";
         $url .= "&delay={$delay}";

         $cust_msg = _L('Depending on the amount of information to be processed, this action can take some time.');
         $redirect = javascript_redirect($url, ADMIN_REDIRECT_TIMEOUT, '', $cust_msg);
         $tpl->assign('redirect', $redirect);
		 $tpl->assign( 'OwnerNotification', $OwnerNotification);
      }
   }
}

$tpl->assign('VALIDATE_LINKS', $VALIDATE_LINKS);
$tpl->assign('VALIDATE_RECPR', $VALIDATE_RECPR);
$tpl->assign('start', $start);

if ($start != 1)
{
	$tpl->assign('IL', $IL);
	$tpl->assign('AL', $AL);
	$tpl->assign('IR', $IR);
	$tpl->assign('AR', $AR);
   $tpl->assign('range', $range);

	$stat_inactive = array (1 => _L('Pending') , 2 => _L('Active') );
	$stat_active   = array (0 => _L('Inactive'), 1 => _L('Pending'));

	$tpl->assign('stat_inactive', $stat_inactive);
	$tpl->assign('stat_active'  , $stat_active);

   if (AJAX_CAT_SELECTION_METHOD == 0)
   {
      $categs = get_categs_tree();
      $categs[0] = '[All]';
      $tpl->assign('categs', $categs);
   }

   $tpl->assign('send_notif_options', array (0 => _L('No'), 1 => _L('Yes')) );
   $tpl->assign('send_notification', 0);
   $tpl->assign('delay', 10);

	$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_validate.tpl');
	$tpl->assign('content', $content);

	echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
}
else
{
   //tweak_memory_limit(32);

   $count_expired = 0;

   $tpl->assign('valid', array (0 => _L('Broken'), 1 => _L('Unknown'), 2 => _L('Valid'),));
   $columns = array ('URL' => _L('URL'));

   if ($VALIDATE_LINKS)
      $columns['VALID'] = _L('Link Valid');

   if ($VALIDATE_RECPR)
      $columns['RECPR_VALID'] = _L('Recpr. Valid');

   if ($VALIDATE_LINKS)
      $columns['RESPONSE'] = _L('Link Response');

   if ($VALIDATE_RECPR)
      $columns['RECPR_RESPONSE'] = _L('Recpr. Response');

   $tpl->assign('columns', $columns);
   $content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_validate.tpl');
   $tpl->assign('content', $content);
   $page = $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
   $page = explode ('<!--Progressbar-->', $page);

   echo $page[0];
   flush();
   
   $where = '1';
   if ($categ > 0) {
      $where = "`CATEGORY_ID` = ".$db->qstr($categ);
   }

   $rs = $db->Execute("SELECT `ID`, `URL`, `RECPR_URL`, `STATUS`, `VALID`, `RECPR_REQUIRED` FROM `{$tables['link']['name']}` WHERE {$where}
                            OR `ID` IN (SELECT `LINK_ID` FROM `{$tables['additional_category']['name']}` WHERE {$where}) 
                            ORDER BY `ID` ASC LIMIT {$start_query}, {$range}");
   
   $list = $rs->GetAssoc(true);

   $loopsize = $total;
   $percent_per_loop = 100 / $loopsize;
   $percent_last = 0;

   foreach ($list as $id => $val)
   {
      $Status = array ();
      $Status['id']  = $id;
      $Status['old'] = $list[$id]['STATUS'];

		if ($VALIDATE_LINKS)
      {
                    if (!isset($val['URL'])) {
                        $valid = 2;
                    } else {
                        list ($valid, $errstr) = validate_link($val['URL']);
                    }
			$data = array ();
			$data['ID']   = $id;
			$val['VALID'] = $data['VALID'] = $valid;
			$data['LAST_CHECKED'] = gmdate ('Y-m-d H:i:s');
			if ($valid == 0 && ((in_array (1, $IL) && $val['STATUS'] == 1) || (in_array (2, $IL) && $val['STATUS'] == 2)))
				$data['STATUS'] = $Status['link'] = 0;

			if ($valid == 2 && ((in_array (0, $AL) && $val['STATUS'] == 0) || (in_array (1, $AL) && $val['STATUS'] == 1)))
				$data['STATUS'] = 2;

			$db->Replace($tables['link']['name'], $data, 'ID', true);

			$tpl->assign('link_valid', $valid);
			$tpl->assign('errstr', $errstr);
		}

		if ($VALIDATE_RECPR)
                {
                        $link = getFullLinkInfo($id);
					
                        $submit_item = $db->GetRow("SELECT * FROM `{$tables['submit_item_status']['name']}`
                            WHERE `LINK_TYPE_ID` = ".$db->qstr($link['LINK_TYPE'])." AND `ITEM_ID` = '9'");
						
                        if ($submit_item['STATUS'] == 2) {
        			$recpr_valid = check_recpr_link($val);

					
                        } else {
                            $recpr_valid = 2;
									
                        }
			$data = array ();
			$data['ID'] = $id;
			$data['RECPR_VALID'] = ($recpr_valid > 0 ? 2 : 0);
			$data['RECPR_LAST_CHECKED'] = gmdate ('Y-m-d H:i:s');
			if ($recpr_valid < 1 && ((in_array (1, $IR) && $val['STATUS'] == 1) || (in_array (2, $IR) && $val['STATUS'] == 2)))
                        {
	
			$data['STATUS'] = $Status['recpr'] = 0;
            $data['RECPR_EXPIRED'] = 1;
            $count_expired++;
			  if ($OwnerNotification == 1)
		    send_expired_notifications($id);
			
         }
		  
		 
		 
		if ($val['VALID'] > 0 && $recpr_valid >0 && ((in_array (0, $AR) && $val['STATUS'] == 0) ||  (in_array (1, $AR) && $val['STATUS'] == 1)))
				$data['STATUS'] = 2;

			$db->Replace($tables['link']['name'], $data, 'ID', true);

			$tpl->assign('recpr_valid', ($val['RECPR_URL'] ? $recpr_valid : -2));
		}

      $Status['new'] = $db->GetOne("SELECT `STATUS` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));

      //Send notification to link owner if something has changed
      if ($OwnerNotification == 1 && $Status['new'] != $Status['old'])
         send_status_notifications($id);
	
		// Progress bar update BEGIN
		$percent_now = round ($total_processed * $percent_per_loop);
		$difference = $percent_now - $percent_last;
		$tpl->assign('percent_last', $percent_last);
		$tpl->assign('difference', $difference);
		$tpl->assign('url', $val['URL']);
		$tpl->assign('recpr_url', $val['RECPR_URL']);
		$tpl->assign('row', $total_processed);
		$percent_last = $percent_now;
		echo $tpl->fetch(ADMIN_TEMPLATE.'/dir_validate_prog.tpl');
		flush();
		// Progress and progress bar update END
		$total_processed++;

      // Free memory
      unset ($val, $list[$id]);
   }
   echo $page[1];
   flush();

   // Need redirect
   $redirectMe = 1;
}

if ($redirectMe == 1)
{
   // Building redirect
   $next_start_query = $start_query + $range;

   $nextButton = 0;

   if ($next_start_query <= $total)
   {
      $url  = DOC_ROOT."/dir_validate.php?action=validate";
      $url .= "&VALIDATE_LINKS={$VALIDATE_LINKS}&VALIDATE_RECPR={$VALIDATE_RECPR}&OwnerNotification={$OwnerNotification}";
      $url .= "&IL=".simple_serialize($IL, $serializer_encoding);
      $url .= "&AL=".simple_serialize($AL, $serializer_encoding);
      $url .= "&IR=".simple_serialize($IR, $serializer_encoding);
      $url .= "&AR=".simple_serialize($AR, $serializer_encoding);
      $url .= "&categ={$categ}";
      $url .= "&range={$range}";
      $url .= "&start={$next_start_query}";
      $url .= "&total_processed={$total_processed}";
      $url .= "&total={$total}";
      $url .= "&delay={$delay}";

      $build_type = _L('Validating links.');

      $title_msg  = $build_type;

      $cust_msg   = '<p align="left">';
      $cust_msg  .= $build_type;
      $cust_msg  .= '<br />';

      $cust_msg  .= _L('Starting at link').': <strong>'.$start_query.'</strong><br />';
      $cust_msg  .= _L('Stopping at link').': <strong>'.$next_start_query.'</strong><br />';
      $cust_msg  .= _L('Queries performed (this session/total sessions)').': <strong>'.$processes.'/'.$total_processed.'</strong><br />';
      $cust_msg  .= _L('Total queries to process').': <strong>'.$total.'</strong><br />';
      $cust_msg  .= _L('Number of processes per cycle').': <strong>'.$range.'</strong><br />';
      $cust_msg  .= '</p>';

      $cust_msg  .= '<br /><br />'._L('Depending on the size of information to be processed, this action can take some time.');

      $redirect = javascript_redirect($url, $delay, $title_msg, $cust_msg);
      $tpl->assign('redirect', $redirect);
   }
   else
   {
      $url = DOC_ROOT."/dir_validate.php?r=1&finish=1&expired={$count_expired}";
      $title_msg = _L('Link validation status').': <strong>'._L('Complete').'</strong>';
      $redirect  = javascript_redirect($url, 0, $title_msg, $cust_msg);
      $tpl->assign('redirect', $redirect);
   }

   $tpl->assign('nextButton', $nextButton);
   $tpl->assign('nextUrl', $url);
   echo $page[0];
   flush;
}
?>