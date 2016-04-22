<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
if ($skipmsg) {
    $a = &new $ec($code, $mode, $options, $userinfo);
} else {
    $a = &new $ec($message, $code, $mode, $options, $userinfo);
}
?>