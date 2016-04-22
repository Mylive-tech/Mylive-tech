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

/*
        Written and contributed by
        Alex Stapleton,
        Andy Doctorow,
        Tarakan,
        Bill Zeller,
        Vijay "Cyberax" Bhatter
        traB
    This code is released into the public domain
*/
define ('GOOGLE_MAGIC', 0xE6359A60);

//unsigned shift right
function zeroFill($a, $b)
{
    $z = hexdec (80000000);
        if ($z & $a)
        {
            $a = ($a>>1);
            $a &= (~$z);
            $a |= 0x40000000;
            $a = ($a>>($b-1));
        }
        else
        {
            $a = ($a>>$b);
        }
        return $a;
}


function mix($a,$b,$c) {
  $a -= $b; $a -= $c; $a ^= (zeroFill($c,13));
  $b -= $c; $b -= $a; $b ^= ($a<<8);
  $c -= $a; $c -= $b; $c ^= (zeroFill($b,13));
  $a -= $b; $a -= $c; $a ^= (zeroFill($c,12));
  $b -= $c; $b -= $a; $b ^= ($a<<16);
  $c -= $a; $c -= $b; $c ^= (zeroFill($b,5));
  $a -= $b; $a -= $c; $a ^= (zeroFill($c,3));
  $b -= $c; $b -= $a; $b ^= ($a<<10);
  $c -= $a; $c -= $b; $c ^= (zeroFill($b,15));

  return array($a,$b,$c);
}

function GoogleCH($url, $length=null, $init=GOOGLE_MAGIC) {
    if(is_null($length)) {
        $length = sizeof($url);
    }
    $a = $b = 0x9E3779B9;
    $c = $init;
    $k = 0;
    $len = $length;
    while($len >= 12) {
        $a += ($url[$k+0] +($url[$k+1]<<8) +($url[$k+2]<<16) +($url[$k+3]<<24));
        $b += ($url[$k+4] +($url[$k+5]<<8) +($url[$k+6]<<16) +($url[$k+7]<<24));
        $c += ($url[$k+8] +($url[$k+9]<<8) +($url[$k+10]<<16)+($url[$k+11]<<24));
        $mix = mix($a,$b,$c);
        $a = $mix[0]; $b = $mix[1]; $c = $mix[2];
        $k += 12;
        $len -= 12;
    }

    $c += $length;
    switch($len)              /* all the case statements fall through */
    {
        case 11: $c+=($url[$k+10]<<24);
        case 10: $c+=($url[$k+9]<<16);
        case 9 : $c+=($url[$k+8]<<8);
          /* the first byte of c is reserved for the length */
        case 8 : $b+=($url[$k+7]<<24);
        case 7 : $b+=($url[$k+6]<<16);
        case 6 : $b+=($url[$k+5]<<8);
        case 5 : $b+=($url[$k+4]);
        case 4 : $a+=($url[$k+3]<<24);
        case 3 : $a+=($url[$k+2]<<16);
        case 2 : $a+=($url[$k+1]<<8);
        case 1 : $a+=($url[$k+0]);
         /* case 0: nothing left to add */
    }
    $mix = mix($a,$b,$c);
    /*-------------------------------------------- report the result */
    return $mix[2];
}

//converts a string into an array of integers containing the numeric value of the char
function strord($string) {
    for($i=0;$i<strlen($string);$i++) {
        $result[$i] = ord($string{$i});
    }
    return $result;
}


// converts an array of 32 bit integers into an array with 8 bit values. Equivalent to (BYTE *)arr32

function c32to8bit($arr32) {
    for($i=0;$i<count($arr32);$i++) {
        for ($bitOrder=$i*4;$bitOrder<=$i*4+3;$bitOrder++) {
            $arr8[$bitOrder]=$arr32[$i]&255;
            $arr32[$i]=zeroFill($arr32[$i], 8);
        }
    }
    return $arr8;
}

function GoogleCHNew($ch){
	$ch=sprintf("%u", $ch);
	$ch = ((($ch/7) << 2) | (((int)fmod($ch,13))&7));
	$prbuf = array();
	$prbuf[0] = $ch;
	for($i = 1; $i < 20; $i++) {
      $prbuf[$i] = $prbuf[$i-1]-9;
	}
	$ch = GoogleCH(c32to8bit($prbuf), 80);
	return sprintf("%u", $ch);
}

function get_page_rank($url){
	$url = preg_replace('/\?.*$/','?',$url);
	$reqgr = "info:".$url;
    $reqgre = "info:".urlencode($url);
    $gch = GoogleCH(strord($reqgr));
    $gch = "6".GoogleCHNew($gch);
    $querystring = "http://toolbarqueries.google.com/search?client=navclient-auto&ch=".$gch."&ie=UTF-8&oe=UTF-8&features=Rank:FVN&q=".$reqgre;
    $patern = '/^http:/';
    $patern2 = '/^http:\/\/.*google\..*\/(search|images|groups|news).*/';
    $patern3 = '/^http:\/\/localhost.*/';
    $patern4 = '/^http:\/\/(127\.|10\.|172\.16|192\.168).*/'; //local ip
    if(!preg_match($patern, $url) || preg_match($patern2, $url) ||
       preg_match($patern3, $url) || preg_match($patern4, $url)){
       	return -1;
    }else{
    	//$ch = curl_init($querystring);
    	//curl_setopt($ch, CURLOPT_URL, $querystring);
    	//curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; GoogleToolbar 2.0.114-big; Windows XP 5.1)");
    	//curl_exec($ch);

    	$res = get_url($querystring, URL_CONTENT, "", array(), "Mozilla/4.0 (compatible; GoogleToolbar 2.0.114-big; Windows XP 5.1)");
    	if(preg_match('/Rank_.*?:.*?:(\d+)/i', $res['content'], $m)){
    		return $m[1];
    	}else{
    		return -1;
    	}

    }
}
?>