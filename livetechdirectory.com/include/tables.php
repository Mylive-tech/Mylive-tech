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
 # @version        5.x Phoenix Release
  # ################################################################################
 */
/**
 * Calculate password field length for user table,
 * depending on PHP version for eigther use "sha1" or "md5" hash function
 */
if (version_compare(phpversion(), "4.3.0", ">=") && function_exists('sha1'))
    $PasswFieldLength = 40 + strlen("{sha1}"); /* sha1 hash + {sha1} prefix */
else
    $PasswFieldLength = 32 + strlen("{md5}");  /* md5 hash + {md5} prefix   */

function generatePassword($length = 12) {
    // start with a blank password
    $password = "";

    // define possible characters
    $possible = "0123456789bcdfghjkmnpqrstvwxyz";

    // set up a counter
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {
        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);

        // we don't want this character if it's already in the password
        if (!strstr($password, $char)) {
            $password .= $char;
            $i++;
        }
    }
    // done!
    return $password;
}

$rand_pass = generatePassword(12);

global $tables;
$tables = array();


/**
 * ADOdb Data Dictionary Library for PHP (Full Documentation)
 * http://phplens.com/lens/adodb/docs-datadict.htm
 */
$tables['link'] = array(
    'name' => TABLE_PREFIX . 'LINK',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'TITLE' => 'C(255) NOTNULL',
        'CACHE_URL' => 'C(255) NOTNULL DEFAULT \'\'',
        'ANNOUNCE' => 'X2 NULL',
        'DESCRIPTION' => 'X2 NULL',
        'URL' => 'C(255) NOTNULL',
        'ADDRESS' => 'C(255) NULL',
        'CITY' => 'C(255) NULL',
        'STATE' => 'C(255) NULL',
        'COUNTRY' => 'C(255) NULL',
        'ZIP' => 'C(255) NULL',
        'PHONE_NUMBER' => 'C(255) NULL',
        'FAX_NUMBER' => 'C(255) NULL',
        'CATEGORY_ID' => 'I NOTNULL',
        'RECPR_URL' => 'C(255) NULL',
        'RECPR_REQUIRED' => 'L NOTNULL DEFAULT 0',
        'STATUS' => 'I NOTNULL DEFAULT 0',
        'VALID' => 'L NOTNULL DEFAULT 0',
        'RATING' => 'C(11) NOTNULL',
        'VOTES' => 'C(11) NOTNULL',
        'COMMENT_COUNT' => 'C(11) NOTNULL',
        'RECPR_VALID' => 'L NOTNULL DEFAULT 0',
        'OWNER_ID' => 'I NULL',
        'OWNER_NAME' => 'C(255) NULL',
        'OWNER_EMAIL' => 'C(255) NULL',
        'OWNER_NEWSLETTER_ALLOW' => 'I NOTNULL DEFAULT 1',
        'OWNER_NOTIF' => 'I NOTNULL DEFAULT 0',
        'OWNER_EMAIL_CONFIRMED' => 'I NOTNULL DEFAULT 1',
        'DATE_MODIFIED' => 'T DEFDATE',
        'DATE_ADDED' => 'T DEFDATE',
        'HITS' => 'I NOTNULL DEFAULT 0',
        'LAST_CHECKED' => 'T',
        'RECPR_LAST_CHECKED' => 'T',
        'PAGERANK' => 'I NOTNULL DEFAULT -1',
        'INLINKS' => 'I8 NOTNULL DEFAULT -1',
        'ALEXARANK' => 'I8 NOTNULL DEFAULT -1',
        'COMPETERANK' => 'I8 NOTNULL DEFAULT -1',
        'RECPR_PAGERANK' => 'I NOTNULL DEFAULT -1',
        'FEATURED_MAIN' => 'I NOTNULL DEFAULT 0',
        'FEATURED' => 'I NOTNULL DEFAULT 0',
        'EXPIRY_DATE' => 'T',
        'NOFOLLOW' => 'L NOTNULL DEFAULT 0',
        'PAYED' => 'I NOTNULL DEFAULT -1',
        'LINK_TYPE' => 'I NOTNULL DEFAULT 0',
        'IPADDRESS' => 'C(15) NULL',
        'DOMAIN' => 'C(250) NULL',
        'OTHER_INFO' => 'X NULL',
        'META_KEYWORDS' => 'X NULL',
        'META_DESCRIPTION' => 'X NULL',
        'PAGE_TITLE' => 'X NULL',
        'RECPR_EXPIRED' => 'L NOTNULL DEFAULT 0',
        'PRICE' => 'C(255) NULL',
        'IMAGE' => 'C(255) NULL',
        'FILE' => 'C(255) NULL',
        'VIDEO' => 'C(255) NULL',
        'IMAGEGROUP' => 'C(255) NULL',
        'RELEASE_DATE' => 'C(255) NULL',
        'THUMBNAIL_WIDTH' => 'C(255) NULL',
        'THUMBNAIL_HEIGHT' => 'C(255) NULL',
        'THUMBNAIL_WIDTH_GRID' => 'C(255) NULL',
        'THUMBNAIL_WIDTH_LIST' => 'C(255) NULL',
        'VIDEO_CACHE' => 'X2 NULL',
		'LAT' => 'C(255) NULL',
        'LON' => 'C(255) NULL',
    ),
    'indexes' => array(
        'TITLE' => 'TITLE',
        'DESCRIPTION' => array('DESCRIPTION', 'FULLTEXT'),
        'URL' => 'URL',
        'CATEGORY_ID' => 'CATEGORY_ID',
        'STATUS_CATEGORY_ID' => 'STATUS, CATEGORY_ID',
        'HITS' => 'HITS',
        'FEATURED' => 'FEATURED',
        'EXPIRY_DATE' => 'EXPIRY_DATE',
        'META_KEYWORDS' => array('META_KEYWORDS', 'FULLTEXT'),
        'META_DESCRIPTION' => array('META_DESCRIPTION', 'FULLTEXT')
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the links with informations'"
    )
);

$tables['additional_link'] = array(
    'name' => TABLE_PREFIX . 'ADDITIONAL_LINK',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'LINK_ID' => 'I NULL',
        'TITLE' => 'C(255) NULL',
        'URL' => 'C(255) NULL',
    )
);

$tables['additional_link_review'] = array(
    'name' => TABLE_PREFIX . 'ADDITIONAL_LINK_REVIEW',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'LINK_ID' => 'I NULL',
        'TITLE' => 'C(255) NULL',
        'URL' => 'C(255) NULL',
    )
);

$tables['additional_category'] = array(
    'name' => TABLE_PREFIX . 'ADDITIONAL_CATEGORY',
    'fields' => array(
        'LINK_ID' => 'I NULL',
        'CATEGORY_ID' => 'I NULL',
    )
);

$tables['additional_category_review'] = array(
    'name' => TABLE_PREFIX . 'ADDITIONAL_CATEGORY_REVIEW',
    'fields' => array(
        'LINK_ID' => 'I NULL',
        'CATEGORY_ID' => 'I NULL',
    )
);

$tables['newsletter_queue'] = array(
    'name' => TABLE_PREFIX . 'NEWSLETTER_QUEUE',
    'fields' => array(
        'EMAIL' => 'C(255) NOTNULL',
        'TEMPLATE_ID' => 'I NOTNULL',
        'STATUS' => 'I NOTNULL',
        'USE_HTML' => 'I NOTNULL DEFAULT 0',
    )
);

$tables['link_to_import'] = array(
    'name' => TABLE_PREFIX . 'LINK_TO_IMPORT',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'TITLE' => 'C(255) NOTNULL',
        'DESCRIPTION' => 'X2 NULL',
        'URL' => 'C(255) NOTNULL',
        'OWNER_NAME' => 'C(255) NULL',
        'OWNER_EMAIL' => 'C(255) NULL',
        'DOMAIN_NAME' => 'C(250) NULL'
    ),
    'indexes' => array(
        'TITLE' => 'TITLE',
        'DESCRIPTION' => array('DESCRIPTION', 'FULLTEXT'),
        'URL' => 'URL'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the links with informations'"
    )
);



$tables['submit_item_status'] = array(
    'name' => TABLE_PREFIX . 'SUBMIT_ITEM_STATUS',
    'fields' => array(
        'ITEM_ID' => 'I NOTNULL',
        'LINK_TYPE_ID' => 'I NOTNULL',
        'STATUS' => 'I NOTNULL',
        'REQUIRED' => 'I(1) NOTNULL DEFAULT 0',
        'IS_DETAIL' => 'I NOTNULL DEFAULT 0',
    ),
    'indexes' => array(
        'ITEM_ID' => 'ITEM_ID',
    ),
    'data' => array(
        array('ITEM_ID' => '1', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '2', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '3', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '4', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '5', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '6', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '7', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '8', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '9', 'LINK_TYPE_ID' => '1', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '10', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '11', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '12', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '13', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '14', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '15', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '16', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '17', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '18', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '19', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '20', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '21', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '22', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '23', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '24', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '25', 'LINK_TYPE_ID' => '1', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),

        array('ITEM_ID' => '1', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '2', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '3', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '4', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '5', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '6', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '7', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '8', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '9', 'LINK_TYPE_ID' => '2', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '10', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '11', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '12', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '13', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '14', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '15', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '16', 'LINK_TYPE_ID' => '2', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '17', 'LINK_TYPE_ID' => '2', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '18', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '19', 'LINK_TYPE_ID' => '2', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '20', 'LINK_TYPE_ID' => '2', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '21', 'LINK_TYPE_ID' => '2', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '23', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '24', 'LINK_TYPE_ID' => '2', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '25', 'LINK_TYPE_ID' => '2', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        
        array('ITEM_ID' => '1', 'LINK_TYPE_ID' => '3', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '2', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '3', 'LINK_TYPE_ID' => '3', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '4', 'LINK_TYPE_ID' => '3', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '5', 'LINK_TYPE_ID' => '3', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '6', 'LINK_TYPE_ID' => '3', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '7', 'LINK_TYPE_ID' => '3', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '8', 'LINK_TYPE_ID' => '3', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '9', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '10', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '11', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '12', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '13', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '14', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '15', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '16', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '17', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '18', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '19', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '20', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '21', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '22', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '23', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '24', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '25', 'LINK_TYPE_ID' => '3', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        
        array('ITEM_ID' => '1', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '2', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '3', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '4', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '5', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '6', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '7', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '8', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '9', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '10', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '11', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '12', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '13', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '14', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '15', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '16', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '17', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '18', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '19', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '20', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '21', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '22', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '23', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '24', 'LINK_TYPE_ID' => '4', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '25', 'LINK_TYPE_ID' => '4', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        
        array('ITEM_ID' => '1', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '2', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '3', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '4', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '5', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '6', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '7', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '8', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '9', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '10', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '11', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '12', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '13', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '14', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '15', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '16', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '17', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '18', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '19', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '20', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '21', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '22', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '23', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '24', 'LINK_TYPE_ID' => '5', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '25', 'LINK_TYPE_ID' => '5', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),

        array('ITEM_ID' => '1', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '2', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '3', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '1', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '4', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '5', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '6', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '7', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '8', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '9', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '10', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '11', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '12', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '13', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '14', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '15', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '16', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '17', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '18', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '19', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '20', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '21', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '22', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '23', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '24', 'LINK_TYPE_ID' => '6', 'STATUS' => '2', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        array('ITEM_ID' => '25', 'LINK_TYPE_ID' => '6', 'STATUS' => '0', 'REQUIRED' => '0', 'IS_DETAIL' => '0'),
        
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the submit item statuses with informations'"
    )
);

$tables['submit_item_value'] = array(
    'name' => TABLE_PREFIX . 'SUBMIT_ITEM_VALUE',
    'fields' => array(
        'ITEM_ID' => 'I NOTNULL',
        'VALUE' => 'X2 NULL',
    ),
    'indexes' => array(
        'ITEM_ID' => 'ITEM_ID',
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the submit item values with informations'"
    )
);

$tables['validator'] = array(
    'name' => TABLE_PREFIX . 'VALIDATOR',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'NAME' => 'C(255) NOTNULL',
        'TITLE' => 'C(255) NOTNULL',
        'IS_REMOTE' => 'I NOTNULL DEFAULT 0'
    ),
    'indexes' => array(
        'NAME' => 'NAME',
    ),
    'data' => array(
        array('ID' => '1', 'NAME' => 'required', 'TITLE' => 'Is Not Empty', 'IS_REMOTE' => '0'),
        array('ID' => '2', 'NAME' => 'isUniqueValue', 'TITLE' => 'Is Unique', 'IS_REMOTE' => '1'),
        array('ID' => '3', 'NAME' => 'isDomainBanned', 'TITLE' => 'Is Banned Domain	 ', 'IS_REMOTE' => '1'),
        array('ID' => '4', 'NAME' => 'email', 'TITLE' => 'Is Valid Email', 'IS_REMOTE' => '0'),
        array('ID' => '5', 'NAME' => 'isURL', 'TITLE' => 'Is URL', 'IS_REMOTE' => '1'),
        array('ID' => '6', 'NAME' => 'isURLOnline', 'TITLE' => 'Is Valid Online URL', 'IS_REMOTE' => '1'),
        array('ID' => '7', 'NAME' => 'isRecprOnline', 'TITLE' => 'Is Reciprocal URL Online', 'IS_REMOTE' => '1'),
        array('ID' => '8', 'NAME' => 'isBannedEmail', 'TITLE' => 'Is Banned Email', 'IS_REMOTE' => '1'),
        array('ID' => '9', 'NAME' => 'isDate', 'TITLE' => 'Is Date', 'IS_REMOTE' => '1'),
        array('ID' => '10', 'NAME' => 'isNumber', 'TITLE' => 'Is Number', 'IS_REMOTE' => '1'),
        array('ID' => '11', 'NAME' => 'creditcard', 'TITLE' => 'Is Credit Card', 'IS_REMOTE' => '0'),
        array('ID' => '12', 'NAME' => 'isImageUpload', 'TITLE' => 'Is Image Upload', 'IS_REMOTE' => '1'),
        array('ID' => '13', 'NAME' => 'isFileUpload', 'TITLE' => 'Is File Upload', 'IS_REMOTE' => '1'),
        array('ID' => '14', 'NAME' => 'isVideoUpload', 'TITLE' => 'Is Video Upload', 'IS_REMOTE' => '1'),
        array('ID' => '15', 'NAME' => 'isUniqueUrlDomain', 'TITLE' => 'Is Unique Domain', 'IS_REMOTE' => '1'),
        array('ID' => '16', 'NAME' => 'isAlphaNumeric', 'TITLE' => 'Is Alpha Numeric', 'IS_REMOTE' => '1'),
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the validators with informations'"
    )
);

$tables['submit_item_validator'] = array(
    'name' => TABLE_PREFIX . 'SUBMIT_ITEM_VALIDATOR',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'ITEM_ID' => 'I NOTNULL',
        'VALIDATOR_ID' => 'I NOTNULL',
        'VALUE' => 'I NOTNULL',
    ),
    'indexes' => array(
        'ITEM_ID' => 'ITEM_ID',
    ),
    'data' => array(
        array('ITEM_ID' => '1', 'VALIDATOR_ID' => '1'),
        array('ITEM_ID' => '2', 'VALIDATOR_ID' => '6'),
        array('ITEM_ID' => '2', 'VALIDATOR_ID' => '1'),
        array('ITEM_ID' => '6', 'VALIDATOR_ID' => '4'),
        array('ITEM_ID' => '6', 'VALIDATOR_ID' => '8'),
        array('ITEM_ID' => '9', 'VALIDATOR_ID' => '7'),
        array('ITEM_ID' => '18', 'VALIDATOR_ID' => '12'),
        array('ITEM_ID' => '19', 'VALIDATOR_ID' => '13'),
        array('ITEM_ID' => '97', 'VALIDATOR_ID' => '9'),
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the items validators with informations'"
    )
);

$tables['link_type'] = array(
    'name' => TABLE_PREFIX . 'LINK_TYPE',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'NAME' => 'C(255) NOTNULL',        
        'CORE_LINK' => 'I(1) NOTNULL DEFAULT 0',
        'PAGERANK_MIN' => 'C(255) NULL',
        'REQUIRE_APPROVAL' => 'I NOTNULL DEFAULT 0',
        'PRICE' => 'C(255) NULL',
        'PAY_UM' => 'I NULL',
        'DEEP_LINKS' => 'I NULL DEFAULT 0',
        'MULTIPLE_CATEGORIES' => 'I NULL DEFAULT 0',
        'NOFOLLOW' => 'I NULL DEFAULT 0',
        'STATUS' => 'I NOTNULL DEFAULT 0',
        'FEATURED' => 'I NOTNULL DEFAULT 0',
        'COUNT_IMAGES' => 'I NOTNULL DEFAULT 0',
        'ORDER_ID' => 'I NOTNULL',
        'IMG' => 'C(255) NULL',
        'IMGTN' => 'C(255) NULL',
        'LIST_TEMPLATE' => 'C(50) NULL',
        'DETAILS_TEMPLATE' => 'C(50) NULL',
        'SHOW_META' => 'I NULL  DEFAULT 0',
        'DEFAULT_THUMBNAIL_GRID' => 'C(10) NOTNULL DEFAULT 200',
        'DEFAULT_THUMBNAIL_LIST' => 'C(10) NOTNULL DEFAULT 100',
        'DESCRIPTION' => 'X NULL    '
    ),
    'indexes' => array(
        'NAME' => 'ID',
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the link types with informations'"
    ),
    'data' => array(
        array('ID' => '6', 'NAME' => 'Coupon', 'CORE_LINK'=>'1', 'STATUS' => '2', 'ORDER_ID' => '6', 'DEEP_LINKS' => '0', 'MULTIPLE_CATEGORIES' => '0', 'REQUIRE_APPROVAL' => '1', 'LIST_TEMPLATE' => 'coupon.tpl', 'DETAILS_TEMPLATE' => 'coupon.tpl'),
        array('ID' => '5', 'NAME' => 'Video', 'CORE_LINK'=>'1', 'STATUS' => '2', 'ORDER_ID' => '5', 'DEEP_LINKS' => '0', 'MULTIPLE_CATEGORIES' => '0', 'REQUIRE_APPROVAL' => '1', 'LIST_TEMPLATE' => 'video.tpl', 'DETAILS_TEMPLATE' => 'video.tpl'),
        array('ID' => '4', 'NAME' => 'Picture', 'CORE_LINK'=>'1', 'STATUS' => '2', 'ORDER_ID' => '4', 'DEEP_LINKS' => '0', 'MULTIPLE_CATEGORIES' => '0', 'REQUIRE_APPROVAL' => '1', 'LIST_TEMPLATE' => 'picture.tpl', 'DETAILS_TEMPLATE' => 'picture.tpl'),
        array('ID' => '3', 'NAME' => 'Article', 'CORE_LINK'=>'1', 'STATUS' => '2', 'ORDER_ID' => '3', 'DEEP_LINKS' => '0', 'MULTIPLE_CATEGORIES' => '0', 'REQUIRE_APPROVAL' => '1', 'LIST_TEMPLATE' => 'article.tpl', 'DETAILS_TEMPLATE' => 'article.tpl'),
        array('ID' => '2', 'NAME' => 'Business', 'CORE_LINK'=>'1', 'STATUS' => '2', 'ORDER_ID' => '2', 'DEEP_LINKS' => '0', 'MULTIPLE_CATEGORIES' => '0', 'REQUIRE_APPROVAL' => '1', 'LIST_TEMPLATE' => 'business.tpl', 'DETAILS_TEMPLATE' => 'business.tpl'),
        array('ID' => '1', 'NAME' => 'Link', 'CORE_LINK'=>'1', 'STATUS' => '2', 'ORDER_ID' => '1', 'DEEP_LINKS' => '0', 'MULTIPLE_CATEGORIES' => '0', 'REQUIRE_APPROVAL' => '1', 'LIST_TEMPLATE' => 'default.tpl', 'DETAILS_TEMPLATE' => 'default.tpl')
    )
);

$tables['lang'] = array(
    'name' => TABLE_PREFIX . 'LANGUAGE',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'LANG' => 'C(255) NOTNULL',
        'HASH' => 'C(255) NOTNULL',
        'VALUE' => 'C(255) NOTNULL',
    ),
    'indexes' => array(
        'LANG' => 'LANG',
        'VALUE' => array('VALUE', 'FULLTEXT'),
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all translated messages'"
    )
);

$tables['link_review'] = array(
    'name' => TABLE_PREFIX . 'LINK_REVIEW',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'LINK_ID' => 'I',
        'TITLE' => 'C(255) NOTNULL',
        'DESCRIPTION' => 'X2 NULL',
        'URL' => 'C(255) NOTNULL',
        'ADDRESS' => 'C(255) NULL',
        'CITY' => 'C(255) NULL',
        'STATE' => 'C(255) NULL',
        'COUNTRY' => 'C(255) NULL',
        'ZIP' => 'C(255) NULL',
        'LAT' => 'C(255) NULL',
        'LON' => 'C(255) NULL',
        'PHONE_NUMBER' => 'C(255) NULL',
        'FAX_NUMBER' => 'C(255) NULL',
        'CATEGORY_ID' => 'I NOTNULL',
        'RECPR_URL' => 'C(255) NULL',
        'RECPR_REQUIRED' => 'L NOTNULL DEFAULT 0',
        'STATUS' => 'I NOTNULL DEFAULT 0',
        'VALID' => 'L NOTNULL DEFAULT 0',
        'RECPR_VALID' => 'L NOTNULL DEFAULT 0',
        'OWNER_ID' => 'I NULL',
        'OWNER_NAME' => 'C(255) NULL',
        'OWNER_EMAIL' => 'C(255) NULL',
        'OWNER_NEWSLETTER_ALLOW' => 'I NOTNULL DEFAULT 1',
        'OWNER_NOTIF' => 'I NOTNULL DEFAULT 0',
        'OWNER_EMAIL_CONFIRMED' => 'I NOTNULL DEFAULT 1',
        'DATE_MODIFIED' => 'T DEFDATE',
        'DATE_ADDED' => 'T DEFDATE',
        'LAST_CHECKED' => 'T',
        'RECPR_LAST_CHECKED' => 'T',
        'PAGERANK' => 'I NOTNULL DEFAULT -1',
	'INLINKS' => 'I8 NOTNULL DEFAULT -1',
        'ALEXARANK' => 'I8 NOTNULL DEFAULT -1',
        'COMPETERANK' => 'I8 NOTNULL DEFAULT -1',
        'RECPR_PAGERANK' => 'I NOTNULL DEFAULT -1',
        'FEATURED_MAIN' => 'I NOTNULL DEFAULT 0',
        'FEATURED' => 'I NOTNULL DEFAULT 0',
        'EXPIRY_DATE' => 'T',
        'NOFOLLOW' => 'L NOTNULL DEFAULT 0',
        'PAYED' => 'I NOTNULL DEFAULT -1',
        'LINK_TYPE' => 'I NOTNULL DEFAULT 0',
        'MARK_REMOVE' => 'I NOTNULL DEFAULT 0',
        'IPADDRESS' => 'C(15) NULL',
        'DOMAIN' => 'C(250) NULL',
        'OTHER_INFO' => 'X NULL',
        'META_KEYWORDS' => 'X NULL',
        'META_DESCRIPTION' => 'X NULL',
        'PAGE_TITLE' => 'X NULL',
        'PRICE' => 'C(255) NULL',
        'IMAGE' => 'C(255) NULL',
        'FILE' => 'C(255) NULL',
        'VIDEO' => 'C(255) NULL',
        'IMAGEGROUP' => 'C(255) NULL',
        'RELEASE_DATE' => 'C(255) NULL',
	 'THUMBNAIL_WIDTH' => 'C(255) NULL',
        'THUMBNAIL_HEIGHT' => 'C(255) NULL',
        'THUMBNAIL_WIDTH_GRID' => 'C(255) NULL',
        'THUMBNAIL_WIDTH_LIST' => 'C(255) NULL',
        'VIDEO_CACHE' => 'X2 NULL',
    ),
    'indexes' => array(
        'TITLE' => 'TITLE',
        'DESCRIPTION' => array('DESCRIPTION', 'FULLTEXT'),
        'URL' => 'URL',
        'CATEGORY_ID' => 'CATEGORY_ID',
        'STATUS_CATEGORY_ID' => 'STATUS, CATEGORY_ID',
        'FEATURED' => 'FEATURED',
        'EXPIRY_DATE' => 'EXPIRY_DATE',
        'META_KEYWORDS' => array('META_KEYWORDS', 'FULLTEXT'),
        'META_DESCRIPTION' => array('META_DESCRIPTION', 'FULLTEXT')
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all information of link reviews'"
    )
);


$tables['submit_item'] = array(
    'name' => TABLE_PREFIX . 'SUBMIT_ITEM',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'NAME' => 'C(255) NOTNULL',
        'FIELD_NAME' => 'C(255) NOTNULL',
        'TYPE' => 'C(255) NOTNULL',
        'DESCRIPTION' => 'C(255)',
        'IS_DEFAULT' => 'I NOTNULL DEFAULT 0',
        'STATUS' => 'I NOTNULL DEFAULT 2',
        'ORDER_ID' => 'I NOTNULL DEFAULT 0',
    ),
    'indexes' => array(
        'NAME' => 'NAME',
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the submit items with informations'"
    ),
    'data' => array(
        array('ID' => '1', 'NAME' => 'Title', 'FIELD_NAME' => 'TITLE', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '1'),
        array('ID' => '2', 'NAME' => 'URL', 'FIELD_NAME' => 'URL', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '2'),
        array('ID' => '3', 'NAME' => 'Category', 'FIELD_NAME' => 'CATEGORY_ID', 'TYPE' => 'CAT', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '3'),
        array('ID' => '4', 'NAME' => 'Description', 'FIELD_NAME' => 'DESCRIPTION', 'TYPE' => 'TXT', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '4'),
        array('ID' => '5', 'NAME' => 'Owner Name', 'FIELD_NAME' => 'OWNER_NAME', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '5'),
        array('ID' => '6', 'NAME' => 'Owner Email', 'FIELD_NAME' => 'OWNER_EMAIL', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '6'),
        array('ID' => '7', 'NAME' => 'Meta Keywords', 'FIELD_NAME' => 'META_KEYWORDS', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '7'),
        array('ID' => '8', 'NAME' => 'Meta Description', 'FIELD_NAME' => 'META_DESCRIPTION', 'TYPE' => 'TXT', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '8'),
        array('ID' => '9', 'NAME' => 'Reciprocal Link', 'FIELD_NAME' => 'RECPR_URL', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '9'),
        array('ID' => '10', 'NAME' => 'Address', 'FIELD_NAME' => 'ADDRESS', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '10'),
        array('ID' => '11', 'NAME' => 'City', 'FIELD_NAME' => 'CITY', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '11'),
        array('ID' => '12', 'NAME' => 'State', 'FIELD_NAME' => 'STATE', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '12'),
        array('ID' => '13', 'NAME' => 'Postal Code', 'FIELD_NAME' => 'ZIP', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '13'),
        array('ID' => '14', 'NAME' => 'Phone Number', 'FIELD_NAME' => 'PHONE_NUMBER', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '14'),
        array('ID' => '15', 'NAME' => 'Fax Number', 'FIELD_NAME' => 'FAX_NUMBER', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '15'),
        array('ID' => '16', 'NAME' => 'Country', 'FIELD_NAME' => 'COUNTRY', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '16'),
        array('ID' => '17', 'NAME' => 'Price', 'FIELD_NAME' => 'PRICE', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '17'),
        array('ID' => '18', 'NAME' => 'Image', 'FIELD_NAME' => 'IMAGE', 'TYPE' => 'IMAGE', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '18'),
        array('ID' => '19', 'NAME' => 'File', 'FIELD_NAME' => 'FILE', 'TYPE' => 'FILE', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '19'),
        array('ID' => '20', 'NAME' => 'Release Date', 'FIELD_NAME' => 'RELEASE_DATE', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '20'),
        array('ID' => '21', 'NAME' => 'Video file', 'FIELD_NAME' => 'VIDEO', 'TYPE' => 'VIDEO', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '21'),
        array('ID' => '22', 'NAME' => 'Image Group', 'FIELD_NAME' => 'IMAGEGROUP', 'TYPE' => 'IMAGEGROUP', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '22'),
        array('ID' => '23', 'NAME' => 'Grid Thumbnail Width', 'FIELD_NAME' => 'THUMBNAIL_WIDTH_GRID', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '23'),
        array('ID' => '24', 'NAME' => 'List Thumbnail Width', 'FIELD_NAME' => 'THUMBNAIL_WIDTH_LIST', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '24'),
        array('ID' => '25', 'NAME' => 'Page Title', 'FIELD_NAME' => 'PAGE_TITLE', 'TYPE' => 'STR', 'IS_DEFAULT' => '1', 'STATUS' => '2', 'ORDER_ID' => '8'),
    )
);

$tables['category'] = array(
    'name' => TABLE_PREFIX . 'CATEGORY',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'TITLE' => 'C(255) NOTNULL',
        'CACHE_TITLE' => 'X NULL',
        'TITLE_URL' => 'C(255) NULL',
        'CACHE_URL' => 'X NULL',
        'URL' => 'C(255) NOTNULL',
        'NEW_WINDOW' => 'I NOTNULL DEFAULT 0',
        'DESCRIPTION' => 'X2 NULL',
        'TDESCRIPTION' => 'X2 NULL',
        'CATCONTENT' => 'X2 NULL',
        'PARENT_ID' => 'I NOTNULL',
        'STATUS' => 'I NOTNULL DEFAULT 1',
        'DATE_ADDED' => 'T DEFDATE',
        'HITS' => 'I NOTNULL DEFAULT 0',
        'SYMBOLIC' => 'I NOTNULL DEFAULT 0',
        'SYMBOLIC_ID' => 'I NOTNULL DEFAULT 0',
        'META_KEYWORDS' => 'X NULL',
        'META_DESCRIPTION' => 'X NULL',
        'CLOSED_TO_LINKS' => 'I NOTNULL DEFAULT 0',
        'SORT_ORDER' => 'I NOTNULL DEFAULT 1000',
        'COUNT' => 'I NOTNULL DEFAULT 0',
        'LINK_COUNT' => 'I NOTNULL DEFAULT 0',
        'RSS_URL' => 'X NULL',
        'COLS' => 'I(1) NULL',
        'STYLE' => 'C(11) NULL'
    ),
    'indexes' => array(
        'TITLE' => 'TITLE',
        'TITLE_URL' => 'TITLE_URL',
        'DESCRIPTION' => array('DESCRIPTION', 'FULLTEXT'),
        'PARENT_ID' => 'PARENT_ID',
        'STATUS' => 'STATUS',
        'HITS' => 'HITS',
        'COUNT' => 'COUNT',
        'LINK_COUNT' => 'LINK_COUNT',
        'META_KEYWORDS' => array('META_KEYWORDS', 'FULLTEXT'),
        'META_DESCRIPTION' => array('META_DESCRIPTION', 'FULLTEXT')
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the categories with informations'"
    )
);

$tables['comment'] = array(
    'name' => TABLE_PREFIX . 'COMMENT',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'ITEM_ID' => 'C(11) NOTNULL',
        'USER_ID' => 'C(11) NOTNULL',
        'USER_NAME' => 'C(255) NOTNULL',
        'COMMENT' => 'X2',
        'TYPE' => 'I NOTNULL',
        'DATE_ADDED' => 'T DEFDATE',
        'IPADDRESS' => 'C(15) NULL',
        'STATUS' => 'C(11) NOTNULL',
    ),
);

$tables['link_subscribe'] = array(
    'name' => TABLE_PREFIX . 'LINK_SUBSCRIBE',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'USER_ID' => 'I NOTNULL',
        'ARTICLE_ID' => 'I NOTNULL',
        'COMMENT_ID' => 'I NOTNULL',
        'DATE_ADDED' => 'T DEFDATE',
    ),
);

$tables['linkrating'] = array(
    'name' => TABLE_PREFIX . 'LINK_RATING',
    'fields' => array(
        'LINK_ID' => 'C(11) NOTNULL',
        'IPADDRESS' => 'C(15) NULL',
        'DATE_ADDED' => 'T DEFDATE',
    ),
);

$tables['email'] = array(
    'name' => TABLE_PREFIX . 'EMAIL',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'EMAIL' => 'C(255) NOTNULL',
        'TITLE' => 'C(255) NOTNULL',
        'NAME' => 'C(255)',
        'URL' => 'C(255) NOTNULL',
        'DATE_SENT' => 'T DEFDATE'
    ),
    'options' => array(
        "ENGINE=MyISAM COMMENT='Stores all emailing informations'"
    )
);
$tables['email_tpl'] = array(
    'name' => TABLE_PREFIX . 'EMAIL_TPL',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'TPL_TYPE' => 'I DEFAULT 1',
        'TITLE' => 'C(255) NOTNULL',
        'SUBJECT' => 'C(255) NOTNULL',
        'BODY' => 'B'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all email templates'"
    )
);
$tables['config'] = array(
    'name' => TABLE_PREFIX . 'CONFIG',
    'fields' => array(
        'ID' => 'C(255) KEY',
        'VALUE' => 'X2 NULL'
    ),
    'data' => array(
        array('ID' => 'SITE_NAME', 'VALUE' => 'Site Name'),
        array('ID' => 'SITE_URL', 'VALUE' => 'http://www.yourdomain.com/'),
        array('ID' => 'SITE_DESC', 'VALUE' => 'Site description'),
        array('ID' => 'LINKS_PER_PAGE', 'VALUE' => '20'),
        array('ID' => 'MAILS_PER_PAGE', 'VALUE' => '20'),
        array('ID' => 'BOOLEAN_SEARCH_ACTIVE', 'VALUE' => '0'),
        array('ID' => 'VISUAL_CONFIRM', 'VALUE' => '1'),
        array('ID' => 'SECOND_SEARCH_FIELD', 'VALUE' => '0'),
		array('ID' => 'OPTIONS_SEARCH_FIELD', 'VALUE' => '1'),
		array('ID' => 'DISABLE_CONTACT_FORM', 'VALUE' => '0'),	
        array('ID' => 'CAPTCHA_DISTORTION_LEVEL', 'VALUE' => '2'),
        array('ID' => 'CAPTCHA_PHRASE_LENGTH', 'VALUE' => '5'),
        array('ID' => 'CAPTCHA_PHRASE_TYPE', 'VALUE' => 'alphabetical'),
        array('ID' => 'CAPTCHA_SIMPLE', 'VALUE' => '0'),
        array('ID' => 'REQUIRE_REGISTERED_USER', 'VALUE' => '0'),
        array('ID' => 'SAME_DOMAIN_RECPR', 'VALUE' => '0'),
        array('ID' => 'CATS_PER_ROW', 'VALUE' => '2'),
        array('ID' => 'ENABLE_REWRITE', 'VALUE' => '1'),
        array('ID' => 'VERSION', 'VALUE' => CURRENT_VERSION),
        array('ID' => 'RECPR_NOFOLLOW', 'VALUE' => '1'),
        array('ID' => 'EMAIL_METHOD', 'VALUE' => 'mail'),
        array('ID' => 'EMAIL_SERVER', 'VALUE' => 'localhost'),
        array('ID' => 'EMAIL_USER', 'VALUE' => ''),
        array('ID' => 'EMAIL_PASS', 'VALUE' => ''),
        array('ID' => 'EMAIL_SENDMAIL', 'VALUE' => '/usr/bin/sendmail'),
        array('ID' => 'CATS_PREVIEW', 'VALUE' => '3'),
        array('ID' => 'CATS_COUNT', 'VALUE' => '1'),
        array('ID' => 'DIRECTORY_TITLE', 'VALUE' => 'Directory'),
        array('ID' => 'ENABLE_RSS', 'VALUE' => '1'),
        array('ID' => 'INTERNAL_RSS', 'VALUE' => '0'),
        array('ID' => 'LIMIT_PR', 'VALUE' => '0'),
        array('ID' => 'PR_MIN', 'VALUE' => '1'),
        array('ID' => 'ENABLE_PAGERANK', 'VALUE' => '1'),
        array('ID' => 'DEBUG', 'VALUE' => '0'),
        array('ID' => 'SHOW_PAGERANK', 'VALUE' => '0'),
        array('ID' => 'DEFAULT_SORT', 'VALUE' => 'P'),
        array('ID' => 'ENABLE_NEWS', 'VALUE' => '1'),
        array('ID' => 'ADMIN_LANG', 'VALUE' => 'en'),
        array('ID' => 'FRONTEND_LANG', 'VALUE' => 'en'),
        array('ID' => 'LINKS_TOP', 'VALUE' => '20'),
        array('ID' => 'NTF_SUBMIT_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_APPROVE_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_REJECT_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_SUBMITA_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_APPROVEA_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_REJECTA_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_PAYMENT_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_REVIEW_SUBMIT_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_REVIEW_APPROVE_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_REVIEW_REJECT_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_REVIEWA_SUBMIT_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_REVIEWA_APPROVE_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_REVIEWA_REJECT_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_EXPIRED_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_INVOICE_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_USER_DETAILS_TPL', 'VALUE' => ''),
        array('ID' => 'NTF_USER_PASSWORD_TPL', 'VALUE' => ''),
        array('ID' => 'PAY_ENABLE', 'VALUE' => '0'),
        array('ID' => 'PAY_UM', 'VALUE' => '0'),
        array('ID' => 'PAY_UMF', 'VALUE' => '0'),
        array('ID' => 'PAY_UMN', 'VALUE' => '0'),
        array('ID' => 'PAY_UMR', 'VALUE' => '0'),
        array('ID' => 'PAY_NORMAL', 'VALUE' => '0'),
        array('ID' => 'PAY_FEATURED', 'VALUE' => '0'),
        array('ID' => 'PAY_RECPR', 'VALUE' => '0'),
        array('ID' => 'PAY_ENABLE_FREE', 'VALUE' => '0'),
        array('ID' => 'PAYPAL_ACCOUNT', 'VALUE' => ''),
        array('ID' => 'PAY_CURRENCY_CODE', 'VALUE' => 'USD'),
        array('ID' => 'PAY_ENABLE_SUBSCRIPTION', 'VALUE' => '0'),
        array('ID' => 'PAYPAL_ENABLE', 'VALUE' => '0'),
        # Three way linking
        array('ID' => 'ENABLE_THREE_WAY', 'VALUE' => '0'),
        array('ID' => 'THREE_WAY_LINK_URL', 'VALUE' => 'http://www.third-site.com/'),
        array('ID' => 'THREE_WAY_LINK_TITLE', 'VALUE' => 'Third-Site'),
        # Pager Mod
        array('ID' => 'PAGER_LPP', 'VALUE' => '20'),
        array('ID' => 'PAGER_GROUPINGS', 'VALUE' => '20'),
        # Links open in blank window
        array('ID' => 'ENABLE_BLANK', 'VALUE' => '0'),
        # Hit limiter
        array('ID' => 'LIMIT_HITS_TIME', 'VALUE' => '24'),
        # Output Compression
        array('ID' => 'COMPRESS_OUTPUT', 'VALUE' => '0'),
        array('ID' => 'COMPRESSION_LEVEL', 'VALUE' => '6'),
        # Use Google CDN for jquery and jquery-ui
        array('ID' => 'USE_CDN', 'VALUE' => '1'),
        # Caching
        array('ID' => 'CACHING', 'VALUE' => '0'),
        array('ID' => 'DB_CACHING', 'VALUE' => '0'),
        array('ID' => 'DB_CACHE_TIMEOUT', 'VALUE' => '3600'),
        array('ID' => 'CRON_ADOCACHEFLUSH_HOUR', 'VALUE' => '0'),
        array('ID' => 'CRON_ADOCLEARCACHE_LASTRUN', 'VALUE' => '0'),
        # META Tags
        array('ID' => 'ENABLE_META_TAGS', 'VALUE' => '0'),
        array('ID' => 'DEFAULT_META_KEYWORDS', 'VALUE' => 'PHP Link Directory,submit link,payment,categories'),
        array('ID' => 'DEFAULT_META_DESCRIPTION', 'VALUE' => 'This is the link directory where you can submit the URL to your homepage. Powered by PHP Link Directory.'),
        array('ID' => 'DEFAULT_META_AUTHOR', 'VALUE' => 'Your name / Company name'),
        array('ID' => 'DEFAULT_META_COPYRIGHT', 'VALUE' => 'Copyright (c) 2009 by YOUR NAME. All rights reserved!'),
        array('ID' => 'DEFAULT_META_ROBOTS', 'VALUE' => ''),
        # Form Field Lengths
        array('ID' => 'USER_LOGIN_MIN_LENGTH', 'VALUE' => '4'),
        array('ID' => 'USER_LOGIN_MAX_LENGTH', 'VALUE' => '25'),
        array('ID' => 'USER_NAME_MIN_LENGTH', 'VALUE' => '4'),
        array('ID' => 'USER_NAME_MAX_LENGTH', 'VALUE' => '50'),
        array('ID' => 'USER_PASSWORD_MIN_LENGTH', 'VALUE' => '5'),
        array('ID' => 'USER_PASSWORD_MAX_LENGTH', 'VALUE' => '25'),
        array('ID' => 'TITLE_MIN_LENGTH', 'VALUE' => '3'),
        array('ID' => 'TITLE_MAX_LENGTH', 'VALUE' => '100'),
        array('ID' => 'DESCRIPTION_MIN_LENGTH', 'VALUE' => '0'),
        array('ID' => 'DESCRIPTION_MAX_LENGTH', 'VALUE' => '500'),
        array('ID' => 'CAT_TITLE_MIN_LENGTH', 'VALUE' => '3'),
        array('ID' => 'CAT_TITLE_MAX_LENGTH', 'VALUE' => '100'),
        array('ID' => 'CAT_DESCRIPTION_MIN_LENGTH', 'VALUE' => '0'),
        array('ID' => 'CAT_DESCRIPTION_MAX_LENGTH', 'VALUE' => '500'),
        array('ID' => 'META_DESCRIPTION_MIN_LENGTH', 'VALUE' => '0'),
        array('ID' => 'META_DESCRIPTION_MAX_LENGTH', 'VALUE' => '250'),
        array('ID' => 'META_KEYWORDS_MIN_LENGTH', 'VALUE' => '0'),
        array('ID' => 'META_KEYWORDS_MAX_LENGTH', 'VALUE' => '250'),
        array('ID' => 'FTR_MAX_LINKS', 'VALUE' => '5'),
        # Category order
        array('ID' => 'CATEG_FIELD_SORT', 'VALUE' => 'TITLE'),
        array('ID' => 'CATEG_FIELD_SORT_ORDER', 'VALUE' => 'ASC'),
        array('ID' => 'SUBCATEG_FIELD_SORT', 'VALUE' => 'HITS'),
        array('ID' => 'SUBCATEG_FIELD_SORT_ORDER', 'VALUE' => 'ASC'),
        # Force HTTP status 200 (OK)
        array('ID' => 'FORCE_HTTP_STATUS_OK', 'VALUE' => '0'),
        array('ID' => 'FORCE_INVALID_HTTP_STATUS_OK', 'VALUE' => '0'),
		# New SEO Options
		array('ID' => 'SEO_SHOW_LINK_TYPE', 'VALUE' => '0'),
		array('ID' => 'SEO_URL_EXTENSION', 'VALUE' => ''),
		array('ID' => 'SEO_CATEGORY_NAME', 'VALUE' => '0'),
        # Template
        array('ID' => 'TEMPLATE', 'VALUE' => 'Allure'),
        array('ID' => 'ADMIN_TEMPLATE', 'VALUE' => 'DefaultAdmin'),
        array('ID' => 'CORE_TEMPLATES', 'VALUE' => 'Core'),
        # Automatic redirects
        array('ID' => 'ADMIN_REDIRECT_TIMEOUT', 'VALUE' => 3),
        # Category selection method
        array('ID' => 'CAT_SELECTION_METHOD', 'VALUE' => 0),
        array('ID' => 'ADMIN_CAT_SELECTION_METHOD', 'VALUE' => 0),
        # A basic option to bypass security warning for those uninterested
        array('ID' => 'BYPASS_SECURITY_WARNINGS', 'VALUE' => 0),
        # Request variables cleanup (XSS protection)
        array('ID' => 'ALLOW_HTML', 'VALUE' => 0),
        array('ID' => 'ALLOWED_HTML_TAGS', 'VALUE' => 'b,strong,i,u,strike,span'),
        # Request variables cleanup (XSS protection)
        array('ID' => 'ALLOW_ATTR', 'VALUE' => 0),
        array('ID' => 'ALLOWED_ATTR_TAGS', 'VALUE' => ''),
        # Reciprocal link options
        array('ID' => 'RECPR_NEED_200_OK', 'VALUE' => 0),
        array('ID' => 'RECPR_CHECK_NOFOLLOW', 'VALUE' => 1),
        array('ID' => 'RECPR_CHECK_DOMAIN', 'VALUE' => 1),
        # Spam protection
        array('ID' => 'ALLOW_EMPTY_USERAGENT', 'VALUE' => 0),
        array('ID' => 'ALLOW_FOREIGN_REFERER', 'VALUE' => 0),
        array('ID' => 'FORCE_SUBMIT_SESSION', 'VALUE' => 1),
        array('ID' => 'SECRET_SESSION_PASSWORD', 'VALUE' => $rand_pass),
        array('ID' => 'BOT_KEY', 'VALUE' => ''),
        array('ID' => 'BOT_CHECK', 'VALUE' => 0),
        # Contact email
        array('ID' => 'SITE_CONTACT_EMAIL', 'VALUE' => ''),
        # Rich Text Editor
        array('ID' => 'ADMIN_USE_RTE', 'VALUE' => 'TINYMCE'),
        array('ID' => 'SUBMIT_USE_RTE', 'VALUE' => '0'),
        # Date and Time
        array('ID' => 'SERVER_OFFSET_TIME', 'VALUE' => 0),
        #array ('ID' => 'DATE_FORMAT'              , 'VALUE' => 'F j, Y - G:i:s'             ),
        # Charset
        array('ID' => 'CHARSET', 'VALUE' => 'utf-8'),
        # use cookies to track hits or the database which is slower and intensive on big sites
        array('ID' => 'USE_COOKIES', 'VALUE' => 1),
        # Main Page Text
        array('ID' => 'MAINCONTENT', 'VALUE' => '<b><i>MAIN CONTENT</i></b>.'),
        # Submit Terms Page Text
        array('ID' => 'SUBMIT_TERMS', 'VALUE' => "<p>We make an effort to review as many submissions as possible, but we cannot always accept every submission. When submitting, please provide an accurate and grammatically correct title and description for your link. The title and description should reflect the content that a user would find on your site. Your site should not display popups, or attempt to install any software on the user\'s computer. Your site should be complete with all links working, and with a generous amount of content. If your site is new, please consider making sure it is both complete and worthwhile to a potential user before submitting to the directory. Finally, sites containing content that is not legal in all parts of the World, or is not legally viewed by children may not be accepted. Thanks for doing your part to submit a quality site that users of this directory will love, as this will be good for your site, and our directory. Thanks so much for your quality submission!</p>"),
        # Google Analytics
        array('ID' => 'GOOGLE_ANALYTICS', 'VALUE' => ""),
        #Control Submission On or Off
        array('ID' => 'DISABLE_SUBMIT', 'VALUE' => 0),
        #Submission Disable Reason
        array('ID' => 'DISABLE_REASON', 'VALUE' => 'We will be back in 24 hours.'),
        #Control Google Maps On or Off
        array('ID' => 'GMAP_ENABLE', 'VALUE' => 0),
        #Google Map Key
        array('ID' => 'GMAP_KEY', 'VALUE' => 'You must obtain a key from google. http://code.google.com/apis/maps/signup.html'),
        # LINK Comments
        array('ID' => 'LINK_COMMENT', 'VALUE' => '0'),
        array('ID' => 'REQUIRE_REGISTERED_USER_LINK_COMMENT', 'VALUE' => '1'),
        array('ID' => 'AUTO_APPROVE_LINK_COMMENTS_ONOFF', 'VALUE' => '0'),
        array('ID' => 'VISUAL_CONFIRM_LINK_COMMENTS', 'VALUE' => '1'),
        array('ID' => 'AUTO_APPROVE_LINK_COMMENTS', 'VALUE' => '2'),
        # LINK Contact Listing
        array('ID' => 'CONTACT_LISTING', 'VALUE' => '0'),
		# LINK Tell Friend
        array('ID' => 'LINK_TELL_FRIEND', 'VALUE' => '0'),
        # LINK Ratings
        array('ID' => 'REQUIRE_REGISTERED_USER_LINK_RATING', 'VALUE' => '1'),
        array('ID' => 'LINK_RATING_DISPLAY', 'VALUE' => 'numeric'),
        array('ID' => 'LINK_RATING_TIME', 'VALUE' => '86400'),
        array('ID' => 'LINK_RATING', 'VALUE' => '0'),
        array('ID' => 'LINK_MIN_RATING', 'VALUE' => '1'),
        array('ID' => 'LINK_MIN_VOTES', 'VALUE' => '10'),
        # Author extra info
        array('ID' => 'ALLOW_AUTHOR_INFO', 'VALUE' => '1'),
        array('ID' => 'ALLOW_ANONYMOUS', 'VALUE' => '1'),
//      array ('ID' => 'ALLOW_AVATARS'                   , 'VALUE' => '1'                          ),
        # Email confirmation
        array('ID' => 'EMAIL_CONFIRMATION', 'VALUE' => '0'),
        array('ID' => 'SHOW_CONFIRMED_ONLY', 'VALUE' => '0'),
        array('ID' => 'WAIT_FOR_EMAIL_CONF', 'VALUE' => '5'),
        # Mobile Template
        array('ID' => 'MOBILE_TEMPLATE', 'VALUE' => 'MobileFormat'),
        array('ID' => 'USE_MOBILE_SITE', 'VALUE' => '0'),
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores configuration options of the directory'"
    )
);

$tables['user_default_actions'] = array(
    'name' => TABLE_PREFIX . 'USER_DEFAULT_ACTIONS',
    'fields' => array(
        'ID' => 'I KEY',
        'LEVEL_ID' => 'I NOTNULL',
        'NAME' => 'C(250) NOTNULL',
        'INFO' => 'X',
        'VALUE' => 'I NOTNULL DEFAULT 0'
    ),
    'data' => array(
        #regular user
        array('ID' => '1', 'LEVEL_ID' => '0', 'NAME' => 'Add Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '2', 'LEVEL_ID' => '0', 'NAME' => 'Edit Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '3', 'LEVEL_ID' => '0', 'NAME' => 'Delete Link', 'INFO' => '', 'VALUE' => '1'),
        #editor user
        array('ID' => '4', 'LEVEL_ID' => '2', 'NAME' => 'Add Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '5', 'LEVEL_ID' => '2', 'NAME' => 'Edit Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '6', 'LEVEL_ID' => '2', 'NAME' => 'Delete Link', 'INFO' => '', 'VALUE' => '1'),
        #regular user category
        array('ID' => '7', 'LEVEL_ID' => '0', 'NAME' => 'Add Category', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '8', 'LEVEL_ID' => '0', 'NAME' => 'Edit Category', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '9', 'LEVEL_ID' => '0', 'NAME' => 'Delete Category', 'INFO' => '', 'VALUE' => '0'),
        #editor user
        array('ID' => '10', 'LEVEL_ID' => '2', 'NAME' => 'Add Category', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '11', 'LEVEL_ID' => '2', 'NAME' => 'Edit Category', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '12', 'LEVEL_ID' => '2', 'NAME' => 'Delete Category', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '19', 'LEVEL_ID' => '3', 'NAME' => 'Add Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '20', 'LEVEL_ID' => '3', 'NAME' => 'Edit Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '21', 'LEVEL_ID' => '3', 'NAME' => 'Delete Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '22', 'LEVEL_ID' => '3', 'NAME' => 'Add Category', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '23', 'LEVEL_ID' => '3', 'NAME' => 'Edit Category', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '24', 'LEVEL_ID' => '3', 'NAME' => 'Delete Category', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '28', 'LEVEL_ID' => '3', 'NAME' => 'Add Page', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '29', 'LEVEL_ID' => '3', 'NAME' => 'Edit Page', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '30', 'LEVEL_ID' => '3', 'NAME' => 'Delete Page', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '31', 'LEVEL_ID' => '0', 'NAME' => 'Add Page', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '32', 'LEVEL_ID' => '0', 'NAME' => 'Edit Page', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '33', 'LEVEL_ID' => '0', 'NAME' => 'Delete Page', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '34', 'LEVEL_ID' => '2', 'NAME' => 'Add Page', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '35', 'LEVEL_ID' => '2', 'NAME' => 'Edit Page', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '36', 'LEVEL_ID' => '2', 'NAME' => 'Delete Page', 'INFO' => '', 'VALUE' => '0'),
        array('ID' => '37', 'LEVEL_ID' => '1', 'NAME' => 'Add Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '38', 'LEVEL_ID' => '1', 'NAME' => 'Edit Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '39', 'LEVEL_ID' => '1', 'NAME' => 'Delete Link', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '40', 'LEVEL_ID' => '1', 'NAME' => 'Add Category', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '41', 'LEVEL_ID' => '1', 'NAME' => 'Edit Category', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '42', 'LEVEL_ID' => '1', 'NAME' => 'Delete Category', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '46', 'LEVEL_ID' => '1', 'NAME' => 'Add Page', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '47', 'LEVEL_ID' => '1', 'NAME' => 'Edit Page', 'INFO' => '', 'VALUE' => '1'),
        array('ID' => '48', 'LEVEL_ID' => '1', 'NAME' => 'Delete Page', 'INFO' => '', 'VALUE' => '1'),
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT=''"
    )
);

$tables['user_actions'] = array(
    'name' => TABLE_PREFIX . 'USER_ACTIONS',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'USER_ID' => 'I NOTNULL',
        'ACTION_ID' => 'I NOTNULL',
        'VALUE' => 'I NOTNULL DEFAULT 0'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT=''"
    )
);

$tables['payment'] = array(
    'name' => TABLE_PREFIX . 'PAYMENT',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'LINK_ID' => 'C(15) NOTNULL',
        'NAME' => 'C(255)',
        'EMAIL' => 'C(255)',
        'IPADDRESS' => 'C(15) NOTNULL',
        'AMOUNT' => 'N(8.2) NOTNULL',
        'QUANTITY' => 'I NOTNULL',
        'TOTAL' => 'N(8.2) NOTNULL',
        'PAYED_TOTAL' => 'N(8.2) NOTNULL',
        'PAYED_QUANTITY' => 'I NOTNULL',
        'UM' => 'I NOTNULL',
        'CONFIRMED' => 'I NOTNULL DEFAULT 0',
        'SUBSCRIBED' => 'I NOTNULL DEFAULT 0',
        'PAYMENT_TYPE' => 'C(255)',
        'PAYMENT_STATUS' => 'C(255)',
        'PAY_DATE' => 'T NOTNULL DEFDATE',
        'CONFIRM_DATE' => 'T',
        'RAW_LOG' => 'X2'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all payments with informations'"
    )
);
$tables['page'] = array(
    'name' => TABLE_PREFIX . 'PAGE',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'NAME' => 'C(255) NOTNULL',
        'SEO_NAME' => 'C(255) NOTNULL',
        'CONTENT' => 'X2',
        'STATUS' => 'I NOTNULL DEFAULT 0',
        'PRIVACY' => 'I NOTNULL DEFAULT 0',
        'PLACEMENT' => 'I NOTNULL DEFAULT 0',
        'SHOW_IN_MENU' => 'I NOTNULL DEFAULT 1',
        'DATE_ADDED' => 'T DEFDATE',
        'DATE_MODIFIED' => 'T DEFDATE',
        'META_KEYWORDS' => 'X NULL',
        'META_DESCRIPTION' => 'X NULL'
    ),
    'indexes' => array(
        'NAME' => 'NAME',
        'CONTENT' => array('CONTENT', 'FULLTEXT'),
        'STATUS' => 'STATUS',
        'PRIVACY' => 'PRIVACY',
        'PLACEMENT' => 'PLACEMENT',
        'META_KEYWORDS' => array('META_KEYWORDS', 'FULLTEXT'),
        'META_DESCRIPTION' => array('META_DESCRIPTION', 'FULLTEXT')
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores extra pages information'"
    )
);
$tables['user'] = array(
    'name' => TABLE_PREFIX . 'USER',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'LOGIN' => 'C(100) NOTNULL',
        'NAME' => 'C(255) NOTNULL',
        'PASSWORD' => 'C(' . $PasswFieldLength . ') NOTNULL',
        'LEVEL' => 'L NOTNULL DEFAULT 0',
        'RANK' => 'L NOTNULL DEFAULT 0',
        'ACTIVE' => 'L NOTNULL DEFAULT 0',
        'LAST_LOGIN' => 'T DEFDATE',
        'REGISTRATION_DATE' => 'T DEFDATE',
        'AUTH_IMG' => 'C(255)NULL',
        'AUTH_IMGTN' => 'C(255)NULL',
        'SUBMIT_NOTIF' => 'L NOTNULL DEFAULT 1',
        'PAYMENT_NOTIF' => 'L NOTNULL DEFAULT 1',
        'ADDRESS' => 'C(255) NULL',
        'EMAIL' => 'C(255) NOTNULL',
        'WEBSITE' => 'C(255) NULL',
        'WEBSITE_NAME' => 'C(255) NULL',
        'INFO' => 'C(255) NULL',
        'ANONYMOUS' => 'L NOTNULL DEFAULT 0',
        'LANGUAGE' => 'C(2) NULL',
        'AVATAR' => 'C(100) NULL',
        'ICQ' => 'C(15) NULL',
        'AIM' => 'C(255) NULL',
        'YIM' => 'C(255) NULL',
        'MSN' => 'C(255) NULL',
        'CONFIRM' => 'C(10) NULL',
        'NEW_PASSWORD' => 'C(' . $PasswFieldLength . ') NULL',
        'EMAIL_CONFIRMED' => 'I NOTNULL DEFAULT 1',
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the users with informations'"
    )
);
$tables['user_permission'] = array(
    'name' => TABLE_PREFIX . 'USER_PERMISSION',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'USER_ID' => 'I NOTNULL',
        'CATEGORY_ID' => 'I NOTNULL'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores user permissions'"
    )
);
$tables['banlist'] = array(
    'name' => TABLE_PREFIX . 'BANLIST',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'BAN_IP' => 'C(15) NULL',
        'BAN_DOMAIN' => 'C(64) NULL',
        'BAN_EMAIL' => 'C(255) NULL',
        'BAN_WORD' => 'C(255) NULL'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores banning informations'"
    )
);
$tables['hitcount'] = array(
    'name' => TABLE_PREFIX . 'HITS',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'LINK_ID' => 'C(64) NULL',
        'CATEGORY_ID' => 'C(250) NULL',
        'IP' => 'C(15) NULL',
        'LAST_HIT' => 'T DEFDATE'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Counts hits of categories and links'"
    )
);

$tables['img_verification'] = array(
    'name' => TABLE_PREFIX . 'IMG_VERIFICATION',
    'fields' => array(
        'IMGHASH' => 'C(32) NULL',
        'IMGPHRASE' => 'C(10) NULL',
        'CREATED' => 'I(20) NOTNULL DEFAULT 0',
        'VIEWED' => 'L NOTNULL DEFAULT 0'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores captcha informations for an improved validation'"
    )
);

$tables['submit_verification'] = array(
    'name' => TABLE_PREFIX . 'SUBMIT_VERIFICATION',
    'fields' => array(
        'SESSION' => 'C(32) NULL',
        'SUBMITHASH' => 'C(32) NULL',
        'CREATED' => 'I(20) NOTNULL DEFAULT 0'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores submit sessions for'"
    )
);
$tables['search'] = array(
    'name' => TABLE_PREFIX . 'SEARCH',
    'fields' => array(
        'SEARCHID' => 'I KEY AUTO',
        'USERID' => 'I(10) DEFAULT 0',
        'IPADDRESS' => 'C(15) NULL',
        'QUERY' => 'C(250) NOTNULL',
        'SORTBY' => 'C(200) NULL',
        'SORTORDER' => "C(4) DEFAULT 'DESC'",
        'SEARCHTIME' => 'T DEFDATE',
        'SEARCHTERMS' => 'X DEFAULT NULL',
        'DISPLAYTERMS' => 'X DEFAULT NULL',
        'COMPLETED' => "I(2) DEFAULT '0'"
    )
);

$tables['widget_zone_types'] = array(
    'name' => TABLE_PREFIX . 'WIDGET_ZONE_TYPES',
    'fields' => array(
        'NAME' => 'C(255) KEY '
    ),
    'data' => array(
        array('NAME' => 'VERTICAL'),
        array('NAME' => 'CENTRAL'),
    	array('NAME' => 'CUSTOM')
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores zone types for widget applications'"
    )
);

$tables['widget_zones'] = array(
    'name' => TABLE_PREFIX . 'WIDGET_ZONES',
    'fields' => array(
        'NAME' => 'C(255) KEY ',
        'TYPE' => 'C(255) NOTNULL',
        'CONTROLLER' => 'C(50) NULL',
        'ACTION' => 'C(50) NULL'
    ),
    'data' => array(
        array('NAME' => 'LEFT_COLUMN', 'TYPE' => 'VERTICAL', 'CONTROLLER' => NULL, 'ACTION' => NULL),
        array('NAME' => 'RIGHT_COLUMN', 'TYPE' => 'VERTICAL', 'CONTROLLER' => NULL, 'ACTION' => NULL),
        array('NAME' => 'HOMEPAGE', 'TYPE' => 'CENTRAL', 'CONTROLLER' => 'index', 'ACTION' => 'index'),
        array('NAME' => 'ANY_OTHER_PAGE', 'TYPE' => 'CENTRAL', 'CONTROLLER' => NULL, 'ACTION' => NULL),
        array('NAME' => 'LINK_DETAIL', 'TYPE' => 'CENTRAL', 'CONTROLLER' => 'details', 'ACTION' => 'index'),
        array('NAME' => 'CATEGORY_PAGE', 'TYPE' => 'CENTRAL', 'CONTROLLER' => 'category', 'ACTION' => 'index'),
        array('NAME' => 'LOCATION_PAGE', 'TYPE' => 'CENTRAL', 'CONTROLLER' => 'location', 'ACTION' => 'index'),
        array('NAME' => 'TAGS_PAGE', 'TYPE' => 'CENTRAL', 'CONTROLLER' => 'tag', 'ACTION' => 'index'),
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores zones for widget applications'"
    )
);


$tables['widget'] = array(
    'name' => TABLE_PREFIX . 'WIDGET',
    'fields' => array(
        'NAME' => 'C(255) KEY ',
        'TYPE' => 'C(255) NOTNULL',
        'DESCRIPTION' => 'X NULL'
    ),
    'data' => array(
        array('NAME' => 'MainContent', 'TYPE' => 'CENTRAL', 'DESCRIPTION' => 'This is the main content of the site, it is not editable and cannot be deleted.')
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores installed widgets'"
    )
);


$tables['widget_activated'] = array(
    'name' => TABLE_PREFIX . 'WIDGET_ACTIVATED',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'NAME' => 'C(255) NOTNULL',
        'ZONE' => 'C(255) NOTNULL',
        'ORDER_ID' => 'I(11) NULL',
        'ACTIVE' => 'I(1) NOTNULL DEFAULT 1',
        'OPTIONS' => 'X NULL'
    ),
    'data' => array(
        array('NAME' => 'HomePageContent', 'ZONE' => 'HOMEPAGE', 'ORDER_ID' => 0),
        array('NAME' => 'AllCategories', 'ZONE' => 'HOMEPAGE', 'ORDER_ID' => 0),
        array('NAME' => 'MainContent', 'ZONE' => 'ANY_OTHER_PAGE', 'ORDER_ID' => 0),
        array('NAME' => 'MainContent', 'ZONE' => 'LINK_DETAIL', 'ORDER_ID' => 0),
        array('NAME' => 'MainContent', 'ZONE' => 'CATEGORY_PAGE', 'ORDER_ID' => 0),
        array('NAME' => 'CategoryContent', 'ZONE' => 'CATEGORY_PAGE', 'ORDER_ID' => 1),
        array('NAME' => 'CategorySubcategories', 'ZONE' => 'CATEGORY_PAGE', 'ORDER_ID' => 1),
        array('NAME' => 'CategoryListings', 'ZONE' => 'CATEGORY_PAGE', 'ORDER_ID' => 2),
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores installed & activated widgets and their active zones'"
    )

);

$tables['widget_settings'] = array(
    'name' => TABLE_PREFIX . 'WIDGET_SETTINGS',
    'fields' => array(
        'IDENTIFIER' => 'C(255) NOTNULL',
        'WIDGET_NAME' => 'C(255) NOTNULL',
        'SETTING_NAME' => 'C(255) NOTNULL',
        'SETTING_VALUE' => 'X NULL',
        'SETTING_INFO' => 'X NULL',
        'SETTING_ALLOWED' => 'X NULL'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores installed widgets settings'"
    )
);

$tables['news'] = array(
    'name' => TABLE_PREFIX . 'NEWS',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'TITLE' => 'C(255) NOTNULL',
        'DESCRIPTION' => 'X2 NULL',
        'URL' => 'C(255) NOTNULL',
        'CATEGORY_ID' => 'I NOTNULL',
        'RATING' => 'C(11) NOTNULL',
        'VOTES' => 'C(11) NOTNULL',
        'COMMENT_COUNT' => 'C(11) NOTNULL',
        'OWNER_ID' => 'I NULL',
        'OWNER_NAME' => 'C(255) NULL',
        'OWNER_EMAIL' => 'C(255) NULL',
        'OWNER_NOTIF' => 'I NOTNULL DEFAULT 0',
        'DATE_MODIFIED' => 'T DEFDATE',
        'DATE_ADDED' => 'T DEFDATE',
        'LAST_CHECKED' => 'T',
        'RECPR_LAST_CHECKED' => 'T',
        'PAGERANK' => 'I NOTNULL DEFAULT -1',
        'RECPR_PAGERANK' => 'I NOTNULL DEFAULT -1',
        'FEATURED_MAIN' => 'I NOTNULL DEFAULT 0',
        'FEATURED' => 'I NOTNULL DEFAULT 0',
        'EXPIRY_DATE' => 'T',
        'NOFOLLOW' => 'L NOTNULL DEFAULT 0',
        'DOMAIN' => 'C(250) NULL',
        'OTHER_INFO' => 'X NULL',
        'META_KEYWORDS' => 'X NULL',
        'META_DESCRIPTION' => 'X NULL',
        'RECPR_EXPIRED' => 'L NOTNULL DEFAULT 0'
    ),
    'indexes' => array(
        'TITLE' => 'TITLE',
        'DESCRIPTION' => array('DESCRIPTION', 'FULLTEXT'),
        'URL' => 'URL',
        'CATEGORY_ID' => 'CATEGORY_ID',
        'FEATURED' => 'FEATURED',
        'EXPIRY_DATE' => 'EXPIRY_DATE',
        'META_KEYWORDS' => array('META_KEYWORDS', 'FULLTEXT'),
        'META_DESCRIPTION' => array('META_DESCRIPTION', 'FULLTEXT')
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all the links with informations'"
    )
);

$tables['inline_widget'] = array(
    'name' => TABLE_PREFIX . 'INLINE_WIDGET',
    'fields' => array(
        'ID' => 'I NOTNULL',
        'NAME' => 'C(255) NOTNULL',
        'TEXT' => 'X2 NULL',
        'DATE_ADDED' => 'T DEFDATE',
        'STATUS' => 'I NOTNULL'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all inline widget information'"
    )
);

$tables['imagegroup'] = array(
    'name' => TABLE_PREFIX . 'IMAGEGROUP',
    'fields' => array(
        'GROUPID' => 'I KEY AUTO',
        'DATE_MODIFIED' => 'T DEFDATE',
        'DATE_ADDED' => 'T DEFDATE'
    )
);

$tables['imagegroupfile'] = array(
    'name' => TABLE_PREFIX . 'IMAGEGROUPFILE',
    'fields' => array(
        'IMAGEID' => 'I KEY AUTO',
        'GROUPID' => 'I NOTNULL',
        'IMAGE' => 'C(255) NOTNULL',
    )
);

$tables['media_manager_items'] = array(
    'name' => TABLE_PREFIX . 'MEDIA_MANAGER_ITEMS',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'FILE_NAME' => 'C(255) NOTNULL',
        'USER_ID' => 'I NOTNULL',
        'TYPE' => 'C(255) NOTNULL',
        'DATE_ADDED' => 'T DEFDATE NOTNULL',
    )
);

$tables['category_link_type'] = array(
    'name' => TABLE_PREFIX . 'CATEGORY_LINK_TYPE',
    'fields' => array(
        'CATEGORY_ID' => 'I NULL',
        'LINK_TYPE' => 'I NULL',
    ),
    'indexes' => array(
        'CATEGORY_ID' => 'CATEGORY_ID',
    ),
);

$tables['task'] = array(
    'name' => TABLE_PREFIX . 'TASK',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'NAME' => 'C(255) NOTNULL',
        'TYPE' => 'I NOTNULL DEFAULT 1',
        'DESCRIPTION' => 'X2 NULL',
        'LAST_RUN' => 'T',
        'TOTAL_NUM' => 'I NULL',
        'DONE_NUM' => 'I NULL',
        'LOAD_FREQ' => 'I NOTNULL DEFAULT 0',
        'STATUS' => 'I NOTNULL',
        'ACTION_STATUS' => 'I NULL DEFAULT 0'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all tasks information'"
    )
);

$tables['task_settings'] = array(
    'name' => TABLE_PREFIX . 'TASK_SETTINGS',
    'fields' => array(
        'TASK_ID' => 'I NOTNULL',
        'ID' => 'C(255)',
        'NAME' => 'C(255) NOTNULL',
        'DESCRIPTION' => 'C(255) NULL',
        'AVAILABLE' => 'C(255) NULL',
        'VALUE' => 'X2 NULL',
        'TYPE' => 'C(255) NOTNULL'
    ),
    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores all tasks information'"
    )
);

$tables['menu_items'] = array(
    'name' => TABLE_PREFIX . 'MENU',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'LABEL' => 'C(50) NOTNULL',
        'URL' => 'C(255) NOTNULL',
        'ORDER_ID' => 'I(2) NOTNULL DEFAULT 99',
        'PARENT' => 'I NOTNULL DEFAULT 0'
    ),
    'data' => array(
        array('ID' => '1','LABEL' => 'Submit Link', 'URL' => 'submit{if $category.ID gt 0}?c={$category.ID}{/if}', 'ORDER_ID' => '99', 'PARENT' => '0'),
        array('ID' => '2','LABEL' => 'Latest Links', 'URL' => 'latest', 'ORDER_ID' => '99', 'PARENT' => '0'),
        array('ID' => '3','LABEL' => 'Top Hits', 'URL' => 'top', 'ORDER_ID' => '99', 'PARENT' => '0'),
        array('ID' => '4','LABEL' => 'Contact', 'URL' => 'contact', 'ORDER_ID' => '99', 'PARENT' => '0'),
        array('ID' => '5','LABEL' => 'Rss', 'URL' => 'rss{if $category.ID gt 0}?c={$category.ID}{/if}', 'ORDER_ID' => '99', 'PARENT' => '0'),
    ),

    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Stores site menu'"
    )
);

$tables['tags'] = array(
    'name' => TABLE_PREFIX . 'TAGS',
    'fields' => array(
        'ID' => 'I KEY AUTO',
        'TITLE' => 'C(50) NOTNULL',
        'CNT' => 'I(4) DEFAULT 0',
        'STATUS' => 'I(1) NOTNULL DEFAULT 1'
    ),

    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Tags'"
    )
);

$tables['tags_links'] = array(
    'name' => TABLE_PREFIX . 'TAGS_LINKS',
    'fields' => array(
        'TAG_ID' => 'I KEY AUTO',
        'LINK_ID' => 'I KEY',
    ),

    'options' => array(
        'mysql' => "ENGINE=MyISAM COMMENT='Links tags with links'"
    )
);
?>
