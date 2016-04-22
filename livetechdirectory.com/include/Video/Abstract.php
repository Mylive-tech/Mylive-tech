<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	

abstract class Video_Abstract
{

    /**
     * Converts video
     *
     * @abstract
     * @param $source           Path to video which should be converted
     * @param $destination      Where to save converted video
     * @param $convertTo        Output video type
     * @param null $convertFrom What from to convert
     *
     * @return bool
     */
    public abstract function convert($source, $destination, $convertTo, $size, $convertFrom = null);

    /**
     * Creates a thumbnail for video
     *
     * @abstract
     * @param $pathToVideo Path to file, which from humbnail should e created
     * @param $destination Where to save thumb
     * @param $width
     * @param null $height Not mandatory, ratio will be used to calculate it if not set
     */
    public abstract function createThumbnail($pathToVideo, $destination, $size, $interval);
}
