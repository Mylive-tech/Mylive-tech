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
 
$constant_email_tpl = array();

//Submit Notification
$constant_email_tpl['NTF_SUBMIT_TPL'] = array();
$constant_email_tpl['NTF_SUBMIT_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_SUBMIT_TPL']['TITLE']    = 'Link Request Pending';
$constant_email_tpl['NTF_SUBMIT_TPL']['SUBJECT']  = 'Your Link Request at {MY_SITE_NAME}';
$constant_email_tpl['NTF_SUBMIT_TPL']['BODY']     = 'Dear {LINK_OWNER_NAME},

Thank you for your request for inclusion in our directory {MY_SITE_NAME}.

The link you requested for {LINK_TITLE} is currently under review and in pending status.
All links will be manually reviewed before acceptance. Once accepted, you will recieve an email with your link information.

Thank You,
Directory Staff

{MY_SITE_URL}';

//Link Approve Notification
$constant_email_tpl['NTF_APPROVE_TPL'] = array();
$constant_email_tpl['NTF_APPROVE_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_APPROVE_TPL']['TITLE']    = 'Link Approved';
$constant_email_tpl['NTF_APPROVE_TPL']['SUBJECT']  = 'Approved Link Submission at {MY_SITE_NAME}';
$constant_email_tpl['NTF_APPROVE_TPL']['BODY']     = 'Congratulations!

{LINK_TITLE} has been accepted into the Directory {MY_SITE_NAME}.

Here are the details of your link:
Title: {LINK_TITLE}
Your URL: {LINK_URL}
Description: {LINK_DESCRIPTION}

Your submission is appreciated, and we hope you will tell others about the directory, as well as submit more sites in the future.

Thank You!
Directory Staff

{MY_SITE_URL}';

//Link Reject Notification
$constant_email_tpl['NTF_REJECT_TPL'] = array();
$constant_email_tpl['NTF_REJECT_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_REJECT_TPL']['TITLE']    = 'Link Rejected';
$constant_email_tpl['NTF_REJECT_TPL']['SUBJECT']  = 'Your link has been rejected at {MY_SITE_NAME}';
$constant_email_tpl['NTF_REJECT_TPL']['BODY']     = 'Dear {LINK_OWNER_NAME},

We are sorry! We could not approve your link {LINK_TITLE} in the directory. Please read our submission guidelines or resubmit your site later.

Thank you,
Directory Staff

{MY_SITE_URL}';

//Reviewed Submit Notification
$constant_email_tpl['NTF_REVIEW_SUBMIT_TPL'] = array();
$constant_email_tpl['NTF_REVIEW_SUBMIT_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_REVIEW_SUBMIT_TPL']['TITLE']    = 'Link Review Pending';
$constant_email_tpl['NTF_REVIEW_SUBMIT_TPL']['SUBJECT']  = 'Your Link Review at {MY_SITE_NAME}';
$constant_email_tpl['NTF_REVIEW_SUBMIT_TPL']['BODY']     = 'Dear {LINK_OWNER_NAME},

Thank you for your link review!

The link you reviewed for {LINK_TITLE} is currently in pending status.
Once accepted, you will recieve an email with your link information.

Thank You,
Directory Staff

{MY_SITE_URL}';

//Reviewed Link Approve Notification
$constant_email_tpl['NTF_REVIEW_APPROVE_TPL'] = array();
$constant_email_tpl['NTF_REVIEW_APPROVE_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_REVIEW_APPROVE_TPL']['TITLE']    = 'Link Review Approved';
$constant_email_tpl['NTF_REVIEW_APPROVE_TPL']['SUBJECT']  = 'Approved Link Review at {MY_SITE_NAME}';
$constant_email_tpl['NTF_REVIEW_APPROVE_TPL']['BODY']     = 'Congratulations!

Your link review for {LINK_TITLE} has been accepted into the Directory {MY_SITE_NAME}.

Here are the details of your link:
Title: {LINK_TITLE}
Your URL: {LINK_URL}
Description: {LINK_DESCRIPTION}

Your submission is appreciated, and we hope you will tell others about the directory, as well as submit more sites in the future.

Thank You!
Directory Staff

{MY_SITE_URL}';

//Reviewed Link Reject Notification
$constant_email_tpl['NTF_REVIEW_REJECT_TPL'] = array();
$constant_email_tpl['NTF_REVIEW_REJECT_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_REVIEW_REJECT_TPL']['TITLE']    = 'Link Review Rejected';
$constant_email_tpl['NTF_REVIEW_REJECT_TPL']['SUBJECT']  = 'Your link review has been rejected at {MY_SITE_NAME}';
$constant_email_tpl['NTF_REVIEW_REJECT_TPL']['BODY']     = 'Dear {LINK_OWNER_NAME},

We are sorry! We could not approve your link review for {LINK_TITLE} in the directory. Please read our submission guidelines or resubmit your review later.

Thank you,
Directory Staff

{MY_SITE_URL}';

//Payment notifications
$constant_email_tpl['NTF_PAYMENT_TPL'] = array();
$constant_email_tpl['NTF_PAYMENT_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_PAYMENT_TPL']['TITLE']    = 'Payment Received';
$constant_email_tpl['NTF_PAYMENT_TPL']['SUBJECT']  = 'Payment Received at {MY_SITE_NAME}';
$constant_email_tpl['NTF_PAYMENT_TPL']['BODY']     = 'Hello {USER_NAME},

Thank you for your request for submission to the directory. Your payment for the link submission has been processed and received. Please allow up to 48 hours for your link to be approved and added.


Thank you,
Directory Staff

{MY_SITE_URL}';

//Invoices
$constant_email_tpl['NTF_INVOICE_TPL'] = array();
$constant_email_tpl['NTF_INVOICE_TPL']['TPL_TYPE'] = 4;
$constant_email_tpl['NTF_INVOICE_TPL']['TITLE']    = 'Invoices';
$constant_email_tpl['NTF_INVOICE_TPL']['SUBJECT']  = '{MY_SITE_NAME} Payment';
$constant_email_tpl['NTF_INVOICE_TPL']['BODY']     = 'Hello {LINK_OWNER_NAME},

Please complete your ${PAYMENT_TOTAL} payment to finish submiting your link {LINK_TITLE}

If you want your information to be visible, please complete the payment by visiting the folowing link:
{LINK_PAYMENT_URL}


You have submitted the following link:
Title: {LINK_TITLE}
URL: {LINK_URL}
Description: {LINK_DESCRIPTION}


Regards,
Directory Staff

{MY_SITE_URL}';

//Profile update
$constant_email_tpl['NTF_USER_DETAILS_TPL'] = array();
$constant_email_tpl['NTF_USER_DETAILS_TPL']['TPL_TYPE'] = 5;
$constant_email_tpl['NTF_USER_DETAILS_TPL']['TITLE']    = 'Profile Update';
$constant_email_tpl['NTF_USER_DETAILS_TPL']['SUBJECT']  = '{MY_SITE_NAME} Profile Update';
$constant_email_tpl['NTF_USER_DETAILS_TPL']['BODY']     = 'Hello {USER_NAME},

Here are your login details for {MY_SITE_NAME}:

Login: {USER_LOGIN}
Password: {USER_PASSWORD}

Once you log into {MY_SITE_NAME} at {USER_LOGIN_PAGE}, you can change your password.

Regards,
Directory Staff

{MY_SITE_URL}';

//Profile activation
$constant_email_tpl['NTF_USER_ACTIVATION_TPL'] = array();
$constant_email_tpl['NTF_USER_ACTIVATION_TPL']['TPL_TYPE'] = 5;
$constant_email_tpl['NTF_USER_ACTIVATION_TPL']['TITLE']    = 'Profile Activation';
$constant_email_tpl['NTF_USER_ACTIVATION_TPL']['SUBJECT']  = '{MY_SITE_NAME} Profile Activation';
$constant_email_tpl['NTF_USER_ACTIVATION_TPL']['BODY']     = 'Hello {USER_NAME},

The user account "{USER_NAME}" was created on {MY_SITE_URL}. To confirm your e-mail and to activate your profile, please follow the following URL:
{USER_ACTIVATION_URL}

If you did not create an account on this website, please ignore this e-mail.

Regards,
Directory Staff

{MY_SITE_URL}';

//Password recovery
$constant_email_tpl['NTF_USER_PASSWORD_TPL'] = array();
$constant_email_tpl['NTF_USER_PASSWORD_TPL']['TPL_TYPE'] = 6;
$constant_email_tpl['NTF_USER_PASSWORD_TPL']['TITLE']    = 'Password Recovery';
$constant_email_tpl['NTF_USER_PASSWORD_TPL']['SUBJECT']  = '{MY_SITE_NAME} Password Recovery';
$constant_email_tpl['NTF_USER_PASSWORD_TPL']['BODY']     = 'Hello {USER_NAME},

You are receiving this email because you have (or someone pretending to be you has) requested a new password be sent for your account on {MY_SITE_NAME}. If you did not request this email then please ignore it, if you keep receiving it please contact the directory administrator.

To use the new password you need to activate it. To do this click the link provided below:
{PASSWORD_RECOVER_URL}

Login: {USER_LOGIN}
Password: {USER_PASSWORD}

Once you log into {MY_SITE_NAME} at {USER_LOGIN_PAGE}, you can change your password.

Regards,
Directory Staff

{MY_SITE_URL}';

//Link Inactivated due to no reciprocal Notification
$constant_email_tpl['NTF_EXPIRED_TPL'] = array();
$constant_email_tpl['NTF_EXPIRED_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_EXPIRED_TPL']['TITLE']    = 'Link Inactivated';
$constant_email_tpl['NTF_EXPIRED_TPL']['SUBJECT']  = 'Your link has been inactivated at {MY_SITE_NAME}';
$constant_email_tpl['NTF_EXPIRED_TPL']['BODY']     = 'Dear {LINK_OWNER_NAME},

We are sorry! We could not validate the reciprocal link back to our site so we have been forced to disable your listing {LINK_URL} .

Thank you,
Directory Staff

{MY_SITE_URL}';

//Link Inactivated due to expiry Notification
$constant_email_tpl['NTF_EXPIRED_LINK_TPL'] = array();
$constant_email_tpl['NTF_EXPIRED_LINK_TPL']['TPL_TYPE'] = 2;
$constant_email_tpl['NTF_EXPIRED_LINK_TPL']['TITLE']    = 'Link Expired';
$constant_email_tpl['NTF_EXPIRED_LINK_TPL']['SUBJECT']  = 'Your link has been inactivated at {MY_SITE_NAME}';
$constant_email_tpl['NTF_EXPIRED_LINK_TPL']['BODY']     = 'Dear {LINK_OWNER_NAME},

Your listing {LINK_URL} has expired at {MY_SITE_NAME}.


Thank you,
Directory Staff

{MY_SITE_URL}';

//Send email and add link template
$constant_email_tpl['NTF_SEND_EMAIL_ADD_LINK_TPL'] = array();
$constant_email_tpl['NTF_SEND_EMAIL_ADD_LINK_TPL']['TPL_TYPE'] = 3;
$constant_email_tpl['NTF_SEND_EMAIL_ADD_LINK_TPL']['TITLE']    = 'Send email and add link';
$constant_email_tpl['NTF_SEND_EMAIL_ADD_LINK_TPL']['SUBJECT']  = 'Your Link at {MY_SITE_NAME}';
$constant_email_tpl['NTF_SEND_EMAIL_ADD_LINK_TPL']['BODY']     = 'Dear {EMAIL_NAME},

{EMAIL_TITLE} has been added into the Directory {MY_SITE_NAME}.

Here are the details of your link:
Title: {EMAIL_TITLE}
Your URL: {EMAIL_URL}
Description: {EMAIL_DESCRIPTION}

Thank You!
Directory Staff

{MY_SITE_URL}';

//send email default template
$constant_email_tpl['NTF_SEND_EMAIL'] = array();
$constant_email_tpl['NTF_SEND_EMAIL']['TPL_TYPE'] = 1;
$constant_email_tpl['NTF_SEND_EMAIL']['TITLE']    = 'Send email';
$constant_email_tpl['NTF_SEND_EMAIL']['SUBJECT']  = 'Your Link at {MY_SITE_NAME}';
$constant_email_tpl['NTF_SEND_EMAIL']['BODY']     = 'Dear {EMAIL_NAME},

Define a message here

Some variables you might use:
Link Title: {EMAIL_TITLE}
Your URL: {EMAIL_URL}

Thank You!
Directory Staff

{MY_SITE_URL}';

?>