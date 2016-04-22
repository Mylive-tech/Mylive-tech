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

if ($_REQUEST['action'])
{
   list ($action, $id, $val) = explode(':', $_REQUEST['action']);

   $action = strtoupper (trim ($action));
   $id     = ($id < 0  ? 0 : intval ($id));
   $val    = ($val < 0 ? 0 : intval ($val));

   $tpl->assign('action', strtoupper ($action));
}

$error = 0;
$errorMsg = '';
$_SESSION['return'] = 'conf_payment.php';
switch ($action)
{
   case 'D' : //Delete
      $ActionStatus = RemovePayment($id);
      $error = ($ActionStatus['status'] == 1 ? false : true);
      $tpl->assign('error', $error);

      if ($ActionStatus['status'] != 1)
         $tpl->assign('sql_error', $ActionStatus['errorMsg']);

      break;

   case 'S' : //New payment status
      $ActionStatus = SetNewPaymentStatus($id, $val);

      $error = ($ActionStatus['status'] == 1 ? false : true);
      $tpl->assign('error', $error);

      if ($ActionStatus['status'] != 1)
         $tpl->assign('sql_error', $ActionStatus['errorMsg']);

      break;

   case 'I' : // Invoices
      $countInvoices = 0;

      //Get Pending payments
      $sql = "SELECT * FROM `{$tables['payment']['name']}` WHERE `CONFIRMED` = '-1' AND `LINK_ID` <> ''";
      $payments = $db->GetAll($sql);

      if (is_array ($payments) && !empty ($payments))
      {
         $emailTpl = get_email_template('NTF_INVOICE_TPL');

         if (!is_array ($emailTpl) || empty ($emailTpl))
         {
            $error++;
            $tpl->assign('error', $error);
            $errorMsg = _L('No email template defined for sending invoices.');
            $tpl->assign('errorMsg', $errorMsg);
         }
         else
         {

            $mail = get_emailer_admin();

            $linkErrors = 0;

            foreach ($payments as $key => $pdata)
            {
               //Get link informations
               $linkSql = "SELECT {$tables['link']['name']}.*, ".$db->IfNull("{$tables['category']['name']}.TITLE", "'Top'")." AS `CATEGORY` FROM `{$tables['link']['name']}` LEFT JOIN `{$tables['category']['name']}` ON ({$tables['link']['name']}.CATEGORY_ID = {$tables['category']['name']}.ID) WHERE {$tables['link']['name']}.ID = ".$db->qstr($pdata['LINK_ID']);

               $ldata = $db->GetRow($linkSql);
               if (!is_array ($ldata) || empty ($ldata))
               {
                  $linkErrors++;
               }
               else
               {
                  $udata = $db->GetRow("SELECT * FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($ldata['OWNER_ID']));
               	
                  //Add email subject
                  $emailSubject = $emailTpl['SUBJECT'];
                  $emailSubject = replace_email_vars($emailSubject, $ldata, 2);
                  $emailSubject = replace_email_vars($emailSubject, $pdata, 3);
                  $emailSubject = replace_email_vars($emailSubject, $udata, 5);
                  $mail->Subject = trim ($emailSubject);

                  //Add owner email address
                  $mail->AddAddress($ldata['OWNER_EMAIL'], $ldata['OWNER_NAME']);

                  //Add email body
                  $emailBody = $emailTpl['BODY'];
                  $emailBody = replace_email_vars($emailBody, $ldata, 2);
                  $emailBody = replace_email_vars($emailBody, $pdata, 3);
                  $emailBody = replace_email_vars($emailBody, $udata, 5);
                  $mail->Body = trim ($emailBody);

                  //Send email
                  if (!$mail->Send())
                  {
                     $error++;
                     $tpl->assign('error', $error);
                     $errorMsg = $mail->ErrorInfo;
                     $tpl->assign('errorMsg', $errorMsg);
                  }
                  else
                  {
                     $countInvoices++;
                  }

                  //Clear all addresses (and attachments) for next loop
                  $mail->ClearAddresses();
                  $mail->ClearAttachments();
               }
            }

            if ($linkErrors > 0)
            {
               $error = $error + $linkErrors;
               $tpl->assign('error', $error);
               $errorMsg = str_replace ('#COUNT#', $linkErrors, _L('Could not determine details for #COUNT# links.')).' '._L('No invoices sent for them.');
               $tpl->assign('errorMsg', $errorMsg);
            }

            $tpl->assign('invoicesSent', 1);
            $tpl->assign('countInvoices', $countInvoices);
         }
      }
      else
      {
         $error++;
         $tpl->assign('error', $error);
         $errorMsg = _L('No uncleared payments available to send invoices.');
         $tpl->assign('errorMsg', $errorMsg);
      }

      break;

   default :
      break;
}

$PagerGroupings = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
$LinksPerPage   = (LINKS_PER_PAGE  && LINKS_PER_PAGE  > 0 ? intval (LINKS_PER_PAGE)  : 10);


$columns = array (
            'TITLE'          => _L('Link Title')    ,
            'LINK_TYPE'      => _L('Link Type')     ,
            'STATUS'         => _L('Link Status')   ,
            'P_UM'           => _L('Unit')          ,
			//'P_SUBSCRIBED' => _L('Subscribed')      ,
            'P_QUANTITY'     => _L('Quantity')      ,
            'P_AMOUNT'       => _L('Price')         ,
            'P_TOTAL'        => _L('Total')         ,
            'P_CONFIRMED'    => _L('Payment Status'),
            'P_PAYED_TOTAL'  => _L('Paid')         ,
            'P_PAY_DATE'     => _L('Date')          ,
            'P_CONFIRM_DATE' => _L('Pay Date')      ,
			'P_PAYMENT_TYPE' => _L('Pay Type')      ,
         );

$tpl->assign('columns', $columns);

//Determine column sorting URLs
$columnURLs = GetColSortUrls($columns, $current_item);
$tpl->assign('columnURLs', $columnURLs);

$tpl->assign('col_count', count ($columns) + 2);

// Determine current index
$page         = ceil ($current_item / $LinksPerPage); // Determine page
$limit        = ' LIMIT '.($current_item <= 1 ? '0' : $current_item).', '.$LinksPerPage;

$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['payment']['name']}` WHERE `LINK_ID` <> ''");
$tpl->assign('list_total', $list_total);

$orderBy = " ORDER BY ID ASC";
if (defined ('SORT_FIELD') && SORT_FIELD != '')
   $orderBy = " ORDER BY `".SORT_FIELD."` ".SORT_ORDER;

$pfields = '';

foreach ($tables['payment']['fields'] as $f => $v)
   $pfields .= "P.$f AS `P_$f`, ";

$sql = "
	SELECT {$pfields} L.*, LT.NAME AS LINK_TYPE, ".$db->IfNull('C.TITLE', "'Top'")." AS `CATEGORY` 
	FROM `{$tables['payment']['name']}` P LEFT JOIN `{$tables['link']['name']}` L ON P.LINK_ID = L.ID 
		LEFT JOIN `{$tables['category']['name']}` AS C ON L.CATEGORY_ID = C.ID 
		LEFT JOIN `{$tables['link_type']['name']}` AS LT ON L.LINK_TYPE = LT.ID
	WHERE P.LINK_ID <> '' {$orderBy}
";

$rs = $db->SelectLimit($sql, $LinksPerPage, ($current_item <= 1 ? '0' : $current_item - 1));

if ($rs === false)
   $list = array ();
else
   $list = $rs->GetAssoc(true);

$tpl->assign('list'         , $list);
$tpl->assign('payment_um'   , $payment_um);
$tpl->assign('stats'        , array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active')));
$tpl->assign('paystatus'    , array ('0' => _L('Uncleared'), '1' => _L('Pending'), '2' => _L('Paid'), '3' => _L('Cancelled')));

// Start Paging
SmartyPaginate :: connect(); // Connect Paging
SmartyPaginate :: setPageLimit($LinksPerPage); // Set default number of page groupings

// Build Paging
if ($page < 2)
{
   SmartyPaginate :: disconnect();
   SmartyPaginate :: reset     ();
}

$list_total     = (!empty ($list_total) && $list_total >= 0 ? intval ($list_total) + 1 : 0);

SmartyPaginate :: setPrevText    ('Previous'             );
SmartyPaginate :: setNextText    ('Next'                 );
SmartyPaginate :: setFirstText   ('First'                );
SmartyPaginate :: setLastText    ('Last'                 );
SmartyPaginate :: setTotal       ($list_total            );
SmartyPaginate :: setUrlVar      ('p'                    );
SmartyPaginate :: setUrl         ($_SERVER['REQUEST_URI']);
SmartyPaginate :: setCurrentItem ($current_item          );
SmartyPaginate :: setLimit       ($LinksPerPage          );
SmartyPaginate :: setPageLimit   ($PagerGroupings        );
SmartyPaginate :: assign         ($tpl                   );

unset ($list_total, $PagerGroupings, $LinksPerPage);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/conf_payment.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>