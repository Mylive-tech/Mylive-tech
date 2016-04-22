<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
require_once 'Abstract.php';

class Video_Ffmpeg extends Video_Abstract
{

    public function convert($source, $destination, $convertTo, $size, $convertFrom = null)
    {
        try {
            $cmd = 'ffmpeg -i '.$source.' -sameq -ar 22050 -b 500000 -f '.$convertTo.' -s '.$size.' '.$destination.' >> /dev/null 2>&1 &';
            exec($cmd);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createThumbnail($pathToVideo, $destination, $size, $interval)
    {
        try {
            $output = null;
            $cmd = 'ffmpeg -i '.$pathToVideo.' -f image2 -vframes 1 -r 1 -ss '.$interval.' -s '.$size.' '.$destination.' 2>&1';
            exec($cmd, $output);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
