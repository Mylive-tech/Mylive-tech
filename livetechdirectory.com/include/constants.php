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
 
$email_tpl_types = array ( '1' => _L('Emailer'), '2' => _L('Link Owner Notif.'), '3' => _L('Email and Add Link'), '4' => _L('Invoices'), '5' => _L('User profile details'), '6' => _L('Password recover'));

$payment_um = array ( '1' => _L('Month'),'2' => _L('Trimester'), '3' => _L('Semester'), '4' => _L('Year'), '5' => _L('Unlimited'));

$HtmlCurrencyCode = array (
                        'USD' => "&#036;",
                        'EUR' => "&#8364;",
                        'GBP' => "&#163;",
                        'CAD' => "&#036;",
                        'AUD' => "&#036;",
                        'JPY' => "&#165;"
                     );

$CurrencyTemp = (defined ('PAY_CURRENCY_CODE') && strlen (PAY_CURRENCY_CODE) > 0 ? PAY_CURRENCY_CODE : 'USD');
define ('HTML_CURRENCY_CODE', $HtmlCurrencyCode[$CurrencyTemp]);

$link_type_int   = array ( 'none' => 0, 'free' => 1, 'normal' => 2, 'reciprocal' => 3, 'featured' => 4);
$link_type_str   = array ( 0 => _L('None'), 1 => _L('Free'), 2 => _L('Normal'), 3 => _L('Reciprocal'), 4 => _L('Featured'));
$notif_msg       = array (
	'submit' => array (
		'SUBJECT' => 'New link submitted at {MY_SITE_URL}',
		"BODY" => "Title: {LINK_TITLE}\n" .
				  "URL: {LINK_URL}\n" .
				  "PageRank: {LINK_PAGERANK}\n" .
				  "Description:\n {LINK_DESCRIPTION}\n" .
				  "Owner Name: {LINK_OWNER_NAME}\n" .
				  "Owner Email: {LINK_OWNER_EMAIL}\n"
				 
	),

	'payment' => array (
		'SUBJECT' => 'New {PAYMENT_SUCCESS} payment at {MY_SITE_URL}',
		"BODY" => "Link Title: {LINK_TITLE}\n" .
				  "Link URL: {LINK_URL}\n" .
				  "Payer Name: {PAYMENT_NAME}\n" .
				  "Payer Email: {PAYMENT_EMAIL}\n" .
				  "Unit price: {PAYMENT_AMOUNT}\n" .
				  "Quantity: {PAYMENT_QUANTITY}\n" .
				  "Amount to be paid: {PAYMENT_TOTAL}\n" .
				  "Amount paid: {PAYMENT_PAYED_TOTAL}\n"
	),
	
);

$validation_messages = array(
		'required' => _L("This field is required."),
		'remote' => _L("Please fix this field."),
		'email' => _L("Invalid email address format."),
		'url' => _L("Invalid URL."),
		'date' => _L("Invalid date format."),
		'dateISO' => _L("Please enter a valid date (ISO)."),
		'dateDE' => _L("Bitte geben Sie ein gültiges Datum ein."),
		'number' => _L("Required numeric field."),
		'numberDE' => _L("Bitte geben Sie eine Nummer ein."),
		'digits' => _L("Required integer field."),
		'creditcard' => _L("Please enter a valid credit card number."),
		'equalTo' => _L("Please enter the same value again."),
		'accept' => _L("Please enter a value with a valid extension."),
		'maxlength' => _L("Please enter no more than {0} characters."),
		'minlength' => _L("Please enter at least {0} characters."),
		'rangelength' => _L("This field must have minimum {0} characters and maximum {1} characters.."),
		'range' => _L("Please enter a value between {0} and {1}."),
		'max' => _L("Please enter a value less than or equal to {0}."),
		'min' => _L("Please enter a value greater than or equal to {0}.")
);

?>