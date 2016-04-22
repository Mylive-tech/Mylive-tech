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
 
$charsetList = array (
   'ASMO-708'           => _L('Arabic (ASMO 708)') . ' - ' . 'ASMO-708',
   'DOS-720'            => _L('Arabic (DOS)') . ' - ' . 'DOS-720',
   'iso-8859-6'         => _L('Arabic (ISO)') . ' - ' . 'iso-8859-6',
   'x-mac-arabic'       => _L('Arabic (Mac)') . ' - ' . 'x-mac-arabic',
   'windows-1256'       => _L('Arabic (Windows)') . ' - ' . 'windows-1256',
   'ibm775'             => _L('Baltic (DOS)') . ' - ' . 'ibm775',
   'iso-8859-4'         => _L('Baltic (ISO)') . ' - ' . 'iso-8859-4',
   'windows-1257'       => _L('Baltic (Windows)') . ' - ' . 'windows-1257',
   'ibm852'             => _L('Central European (DOS)') . ' - ' . 'ibm852',
   'iso-8859-2'         => _L('Central European (ISO)') . ' - ' . 'iso-8859-2',
   'x-mac-ce'           => _L('Central European (Mac)') . ' - ' . 'x-mac-ce',
   'windows-1250'       => _L('Central European (Windows)') . ' - ' . 'windows-1250',
   'EUC-CN'             => _L('Chinese Simplified (EUC)') . ' - ' . 'EUC-CN',
   'gb2312'             => _L('Chinese Simplified (GB2312)') . ' - ' . 'gb2312',
   'hz-gb-2312'         => _L('Chinese Simplified (HZ)') . ' - ' . 'hz-gb-2312',
   'x-mac-chinesesimp'  => _L('Chinese Simplified (Mac)') . ' - ' . 'x-mac-chinesesimp',
   'big5'               => _L('Chinese Traditional (Big5)') . ' - ' . 'big5',
   'x-Chinese-CNS'      => _L('Chinese Traditional (CNS)') . ' - ' . 'x-Chinese-CNS',
   'x-Chinese-Eten'     => _L('Chinese Traditional (Eten)') . ' - ' . 'x-Chinese-Eten',
   'x-mac-chinesetrad'  => _L('Chinese Traditional (Mac)') . ' - ' . 'x-mac-chinesetrad',
   'cp866'              => _L('Cyrillic (DOS)') . ' - ' . 'cp866',
   'iso-8859-5'         => _L('Cyrillic (ISO)') . ' - ' . 'iso-8859-5',
   'koi8-r'             => _L('Cyrillic (KOI8-R)') . ' - ' . 'koi8-r',
   'koi8-u'             => _L('Cyrillic (KOI8-U)') . ' - ' . 'koi8-u',
   'x-mac-cyrillic'     => _L('Cyrillic (Mac)') . ' - ' . 'x-mac-cyrillic',
   'windows-1251'       => _L('Cyrillic (Windows)') . ' - ' . 'windows-1251',
   'x-Europa'           => _L('Europa') . ' - ' . 'x-Europa',
   'x-IA5-German'       => _L('German (IA5)') . ' - ' . 'x-IA5-German',
   'ibm737'             => _L('Greek (DOS)') . ' - ' . 'ibm737',
   'iso-8859-7'         => _L('Greek (ISO)') . ' - ' . 'iso-8859-7',
   'x-mac-greek'        => _L('Greek (Mac)') . ' - ' . 'x-mac-greek',
   'windows-1253'       => _L('Greek (Windows)') . ' - ' . 'windows-1253',
   'ibm869'             => _L('Greek, Modern (DOS)') . ' - ' . 'ibm869',
   'DOS-862'            => _L('Hebrew (DOS)') . ' - ' . 'DOS-862',
   'iso-8859-8-i'       => _L('Hebrew (ISO-Logical)') . ' - ' . 'iso-8859-8-i',
   'iso-8859-8'         => _L('Hebrew (ISO-Visual)') . ' - ' . 'iso-8859-8',
   'x-mac-hebrew'       => _L('Hebrew (Mac)') . ' - ' . 'x-mac-hebrew',
   'windows-1255'       => _L('Hebrew (Windows)') . ' - ' . 'windows-1255',
   'ibm861'             => _L('Icelandic (DOS)') . ' - ' . 'ibm861',
   'x-mac-icelandic'    => _L('Icelandic (Mac)') . ' - ' . 'x-mac-icelandic',
   'x-iscii-as'         => _L('ISCII Assamese') . ' - ' . 'x-iscii-as',
   'x-iscii-be'         => _L('ISCII Bengali') . ' - ' . 'x-iscii-be',
   'x-iscii-de'         => _L('ISCII Devanagari') . ' - ' . 'x-iscii-de',
   'x-iscii-gu'         => _L('ISCII Gujarathi') . ' - ' . 'x-iscii-gu',
   'x-iscii-ka'         => _L('ISCII Kannada') . ' - ' . 'x-iscii-ka',
   'x-iscii-ma'         => _L('ISCII Malayalam') . ' - ' . 'x-iscii-ma',
   'x-iscii-or'         => _L('ISCII Oriya') . ' - ' . 'x-iscii-or',
   'x-iscii-pa'         => _L('ISCII Panjabi') . ' - ' . 'x-iscii-pa',
   'x-iscii-ta'         => _L('ISCII Tamil') . ' - ' . 'x-iscii-ta',
   'x-iscii-te'         => _L('ISCII Telugu') . ' - ' . 'x-iscii-te',
   'euc-jp'             => _L('Japanese (EUC)') . ' - ' . 'euc-jp',
   'iso-2022-jp'        => _L('Japanese (JIS)') . ' - ' . 'iso-2022-jp',
   'csISO2022JP'        => _L('Japanese (JIS-Allow 1 byte Kana)') . ' - ' . 'csISO2022JP',
   'x-mac-japanese'     => _L('Japanese (Mac)') . ' - ' . 'x-mac-japanese',
   'shift_jis'          => _L('Japanese (Shift-JIS)') . ' - ' . 'shift_jis',
   'ks_c_5601-1987'     => _L('Korean') . ' - ' . 'ks_c_5601-1987',
   'euc-kr'             => _L('Korean (EUC)') . ' - ' . 'euc-kr',
   'iso-2022-kr'        => _L('Korean (ISO)') . ' - ' . 'iso-2022-kr',
   'Johab'              => _L('Korean (Johab)') . ' - ' . 'Johab',
   'x-mac-korean'       => _L('Korean (Mac)') . ' - ' . 'x-mac-korean',
   'iso-8859-3'         => _L('Latin 3 (ISO)') . ' - ' . 'iso-8859-3',
   'iso-8859-15'        => _L('Latin 9 (ISO)') . ' - ' . 'iso-8859-15',
   'x-IA5-Norwegian'    => _L('Norwegian (IA5)') . ' - ' . 'x-IA5-Norwegian',
   'IBM437'             => _L('OEM United States') . ' - ' . 'IBM437',
   'x-IA5-Swedish'      => _L('Swedish (IA5)') . ' - ' . 'x-IA5-Swedish',
   'windows-874'        => _L('Thai (Windows)') . ' - ' . 'windows-874',
   'ibm857'             => _L('Turkish (DOS)') . ' - ' . 'ibm857',
   'iso-8859-9'         => _L('Turkish (ISO)') . ' - ' . 'iso-8859-9',
   'x-mac-turkish'      => _L('Turkish (Mac)') . ' - ' . 'x-mac-turkish',
   'windows-1254'       => _L('Turkish (Windows)') . ' - ' . 'windows-1254',
   'unicode'            => _L('Unicode') . ' - ' . 'unicode',
   'unicodeFFFE'        => _L('Unicode (Big-Endian)') . ' - ' . 'unicodeFFFE',
   'utf-7'              => _L('Unicode (UTF-7)') . ' - ' . 'utf-7',
   'utf-8'              => _L('Unicode (UTF-8)') . ' - ' . 'utf-8',
   'us-ascii'           => _L('US-ASCII') . ' - ' . 'us-ascii',
   'windows-1258'       => _L('Vietnamese (Windows)') . ' - ' . 'windows-1258',
   'ibm850'             => _L('Western European (DOS)') . ' - ' . 'ibm850',
   'x-IA5'              => _L('Western European (IA5)') . ' - ' . 'x-IA5',
   'iso-8859-1'         => _L('Western European (ISO)') . ' - ' . 'iso-8859-1',
   'macintosh'          => _L('Western European (Mac)') . ' - ' . 'macintosh',
   'Windows-1252'       => _L('Western European (Windows)') . ' - ' . 'Windows-1252',
);
?>