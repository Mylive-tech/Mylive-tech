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

//RALUCA: JQuery validation related
$validators = array(
	'rules' => array(
		'BAN_IP' => array(
			'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
		        		'data'=> array (
		        			'action' => "isValidIP",
		        			'table'  => "banlist",
		        			'field'  => "BAN_IP"
		        		)
		    )
		),
		'BAN_DOMAIN' => array(
			'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
		        		'data'=> array (
		        			'action' => "isValidBanDomain",
		        			'table' => "banlist",
		        			'field' => "BAN_DOMAIN"
		        		)
		    )
		),
		'BAN_EMAIL' => array(
			'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
		        		'data'=> array (
		        			'action' => "isValidBanEmail",
		        			'table' => "banlist",
		        			'field' => "BAN_EMAIL"
		        		)
		    )
		),
		'BAN_WORD' => array(
			'remote' => array(
					'url' => DIRECTORY_ROOT . "/include/validation_functions.php",
					'type'=> "post",
		        		'data'=> array (
		        			'action' => "isUniqueValue",
		        			'table'  => "banlist",
		        			'field'  => "BAN_WORD"
		        		)
		    )
		)
	),
	'messages' => array(
		'BAN_IP'=> array(
			'remote'  	=>_L("Wrong IP: either format is invalid or IP has already been banned.")
		),
		'BAN_DOMAIN'=> array(
			'remote'  	=>_L("Wrong domain: either format is invalid or domain has already been banned.")
		),
		'BAN_EMAIL' => array(
			'remote'  	=>_L("Wrong email: either format is invalid or email has already been banned.")
		),
		'BAN_WORD' => array(
			'remote'  	=>_L("Wrong ban word: has already been banned.")
		)
	)
);


$vld = json_custom_encode($validators);
$tpl->assign('validators', $vld);

$validator = new Validator($validators);
//RALUCA: end of JQuery validation related

if (empty ($_POST['submit'])) {
} else {
  
	//RALUCA: JQuery validation related - server side.
   $validator = new Validator($validators);
   $validator_res = $validator->validate($_POST);
   //RALUCA: end of JQuery validation related - server side.
   
   if (empty($validator_res))
   {
      $confirmation = 0;

      if (!empty ($_POST['BAN_IP']))
      {
          if ($db->AutoExecute($tables['banlist']['name'], array ('BAN_IP' => $_POST['BAN_IP']), 'INSERT', false))
               $confirmation++;
      }

      if ($_POST['UNBAN_IP'] > 0)
      {
         if ($db->Execute("DELETE FROM `{$tables['banlist']['name']}` WHERE `ID` = ".$db->qstr($_POST['UNBAN_IP'])." LIMIT 1"))
            $confirmation++;
      }

      if (!empty ($_POST['BAN_DOMAIN']))
      {
         if ($db->AutoExecute($tables['banlist']['name'], array ('BAN_DOMAIN' => $_POST['BAN_DOMAIN']), 'INSERT', false))
               $confirmation++;
      }

      if ($_POST['UNBAN_DOMAIN'] > 0)
      {
         if ($db->Execute("DELETE FROM `{$tables['banlist']['name']}` WHERE `ID` = ".$db->qstr($_POST['UNBAN_DOMAIN'])." LIMIT 1"))
            $confirmation++;
      }

      if (!empty ($_POST['BAN_EMAIL']))
      {
          if ($db->AutoExecute($tables['banlist']['name'], array ('BAN_EMAIL' => $_POST['BAN_EMAIL']), 'INSERT', false))
               $confirmation++;
      }

      if ($_POST['UNBAN_EMAIL'] > 0)
      {
         if ($db->Execute("DELETE FROM `{$tables['banlist']['name']}` WHERE `ID` = ".$db->qstr($_POST['UNBAN_EMAIL'])." LIMIT 1"))
            $confirmation++;
      }

      if (!empty ($_POST['BAN_WORD']))
      {
         //Clean whitespace
         $_POST['BAN_WORD'] = strip_white_space($_POST['BAN_WORD']);
         $_POST['BAN_WORD'] = strtolower ($_POST['BAN_WORD']);

         //Make an array of words to ban
         $banWordsArray = explode (' ', $_POST['BAN_WORD']);

         //Keep each word only once
         $banWordsArray = array_unique ($banWordsArray);

         if (is_array ($banWordsArray) && !empty ($banWordsArray))
         {
            foreach ($banWordsArray as $key => $word)
            {
               //Count entries of same word
               $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['banlist']['name']}` WHERE `BAN_WORD` = ".$db->qstr($word));

               //Skip word if available
               if ($count < 1)
               {
                  if ($db->AutoExecute($tables['banlist']['name'], array ('BAN_WORD' => $word), 'INSERT', false))
                     $confirmation++;
               }

               //Free memory
               unset ($banWordsArray[$key], $word, $count);
            }
         }

      }

      if ($_POST['UNBAN_WORD'] > 0)
      {
         if ($db->Execute("DELETE FROM `{$tables['banlist']['name']}` WHERE `ID` = ".$db->qstr($_POST['UNBAN_WORD'])." LIMIT 1"))
            $confirmation++;
      }

      if ($confirmation > 0)
      {
         $tpl->assign('posted', true);
         unset ($_POST);
      }
   } else {
   	//should i just break here?
   }
}

//Get all banned IP addresses out of DB and store it into an array
function list_IP()
{
   global $db, $tables;

   $result = $db->GetAssoc("SELECT `ID`, `BAN_IP` FROM `{$tables['banlist']['name']}` WHERE `BAN_IP` IS NOT NULL");

   if (!is_array ($result))
      $result = array ();

   $result[0] = _L('Select an IP to unban');

   ksort($result);

   return $result;
}

//Get all banned domains out of DB and store it into an array
function list_DOMAIN()
{
   global $db, $tables;

   $result = $db->GetAssoc("SELECT `ID`, `BAN_DOMAIN` FROM `{$tables['banlist']['name']}` WHERE `BAN_DOMAIN` IS NOT NULL");

   if (!is_array ($result))
      $result = array ();

   $result[0] = _L('Select a domain to unban');

   ksort($result);

   return $result;
}

//Get all banned email addresses out of DB and store it into an array
function list_EMAIL()
{
   global $db, $tables;

   $result = $db->GetAssoc("SELECT `ID`, `BAN_EMAIL` FROM `{$tables['banlist']['name']}` WHERE `BAN_EMAIL` IS NOT NULL");

   if (!is_array ($result))
      $result = array ();

   $result[0] = _L('Select email address to unban');

   ksort($result);

   return $result;
}

function list_WORD()
{
   global $db, $tables;

   $result = $db->GetAssoc("SELECT `ID`, `BAN_WORD` FROM `{$tables['banlist']['name']}` WHERE `BAN_WORD` IS NOT NULL");

   if (!is_array ($result))
      $result = array ();

   $result[0] = _L('Select a word to unban');

   ksort($result);

   return $result;
}

$wildcardIpTxt = _L('You may use a * as a wildcard for increased flexibility. For example, if you enter 123.123.12*, many IPs will be banned including: 123.123.12.9, 123.123.128.200, 123.123.121.189. To use a wildcard you have to enter at least the first two blocks and the dot, for example: 123.123.*');
$wildcardIpTxt = str_replace ('*', '<span class="important">*</span>', $wildcardIpTxt);
$tpl->assign('wildcardIpTxt', $wildcardIpTxt);

$wildcardDomainTxt = _L('You may use a * as a wildcard for increased flexibility. For example if you enter *.domain.com, all subdomains are banned. Another option is to ban *domain.com, this will ban domain.com, all it\'s subdomains like subdomain.domain.com but also unwanted domains like mydomain.com. We strongly suggest to ban in this case the top level domain (domain.com) and all it\'s subdomains (*.domain.com) separately.');
$wildcardDomainTxt = str_replace ('*', '<span class="important">*</span>', $wildcardDomainTxt);
$tpl->assign('wildcardDomainTxt', $wildcardDomainTxt);

$wildcardEmailTxt = str_replace (array ('#FULL_EMAIL_EXAMPLE#', '#PARTIAL_EMAIL_EXAMPLE#'), array ('<code>email@domain.com</code>', '<code>@domain.com</code>'), _L('You may ban any email addresses from registering and submitting. Ban the complete email address (#FULL_EMAIL_EXAMPLE#), or ban a partial email address (#PARTIAL_EMAIL_EXAMPLE#).'));
$tpl->assign('wildcardEmailTxt', $wildcardEmailTxt);

$tpl->assign($_POST);

$tpl->assign('IP_List', list_IP());
$tpl->assign('Domain_List', list_DOMAIN());
$tpl->assign('Email_List', list_EMAIL());
$tpl->assign('WORD_List', list_WORD());

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_bancontrol.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>