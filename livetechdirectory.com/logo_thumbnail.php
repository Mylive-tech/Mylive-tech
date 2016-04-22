<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 ini_set('memory_limit','30M');
error_reporting(E_ALL);
if($_GET['pic']){
    $path = $_GET['pic'];
    $img = new img($path);
    if(!empty($_GET['width']) and empty($_GET['height'])) {
        $img->resize($_GET['width'], $_GET['width'], true);
    }elseif(!empty($_GET['width']) and !empty($_GET['height'])) {
        $img->getHeight($_GET['width'], $_GET['width'], true);
    }
    else {
        $img->resize();
    }
    $img->show();
}

class img {

    var $image = '';
    var $temp = '';
    var $ext = '';
    var $fullPath = null;
    var $fullSizeFolder = '';
    var $cacheFolder = 'uploads/thumb';
    var $filename = null;

    function isCached($width)
    {
        if (file_exists($this->cacheFolder.'/'.$this->filename.'/'.$width.'.'.$this->ext)) {
            return true;
        } else {
            return false;
        }
    }

    public function saveCache($width, $image)
    {
        if (!file_exists($this->cacheFolder.'/'.$this->filename)) {
            mkdir($this->cacheFolder.'/'.$this->filename, 0777);
        }
        $filePath = $this->cacheFolder.'/'.$this->filename.'/'.$width.'.'.$this->ext;
        switch($this->ext){
            case 'gif':
                ImageGIF($this->image, $filePath);
                break;

            case 'png':
                ImagePNG($this->image, $filePath);
                break;

            case 'jpg':
            case 'jpeg':
            case 'JPG':
                ImageJPEG($this->image, $filePath);
                break;
        }
    }

    function readSource($path = null)
    {
        if (is_null($path)) {
            $path = $this->fullPath;
        }
        switch($this->ext){
            case 'gif':
                $this->image = ImageCreateFromGIF($path);
                break;

            case 'png':
                $this->image = ImageCreateFromPNG($path);
                imagesavealpha($this->image, true);
                break;

            case 'jpg':
            case 'JPG':
            case 'jpeg':
                $this->image = ImageCreateFromJPEG($path);
                break;

        }
    }

    function __construct($sourceFile){

        $sourceFile = dirname(__FILE__).$sourceFile;
        $pathinfo = pathinfo($sourceFile);
        if(!file_exists($sourceFile)){

            $sourceFile = 'uploads/default.gif';
        }
        $this->fullPath = $sourceFile;
        $this->ext = $pathinfo['extension'];
        $this->filename = $pathinfo['filename'];
        return;
    }

    function resize($width = null, $height = null, $aspectradio = true){
        if ($this->isCached($width)) {
            $this->readSource($this->cacheFolder.'/'.$this->filename.'/'.$width.'.'.$this->ext);
            return;
        }
        $this->readSource();
        ini_set('memory_limit','20M');
        $o_wd = imagesx($this->image);
        $o_ht = imagesy($this->image);
        if (is_null($width)) {
            $width = $o_wd;
        }
        if (is_null($height)) {
            $height = $o_ht;
        }
        //var_dump($width, $height, $o_ht);
        if($o_wd<$width || $o_ht<$height) return;
        if(isset($aspectradio)&&$aspectradio) {
            $w = round($o_wd * $height / $o_ht);
            $h = round($o_ht * $width / $o_wd);
            if(($height-$h)<($width-$w)){
                $width =& $w;
            } else {
                $height =& $h;
            }
        }

        $this->temp = imageCreateTrueColor($width,$height);

        $background = imagecolorallocatealpha($this->temp, 255, 255, 255, 127);
        imagecolortransparent($this->temp, $background);

        imagealphablending($this->temp, false);
        imagesavealpha($this->temp,true);
        $transparent = imagecolorallocatealpha($this->temp, 255, 255, 255, 127);
        imagefilledrectangle($this->temp, 0, 0, $width, $width, $transparent);
        imagecolorallocatealpha($this->temp, 255, 255, 255, 127);

        imageCopyResampled($this->temp, $this->image,0, 0, 0, 0, $width, $height, $o_wd, $o_ht);
        $this->sync();
        $this->saveCache($width, $this->image);
        return;
    }

    
    function getHeight($width = 40, $height = 40, $aspectradio = true){
        $this->readSource();
        ini_set('memory_limit','20M');
        $o_wd = imagesx($this->image);
        $o_ht = imagesy($this->image);
        if($o_wd<$width || $o_ht<$height) return;
        if(isset($aspectradio)&&$aspectradio) {
            $w = round($o_wd * $height / $o_ht);
            $h = round($o_ht * $width / $o_wd);
            if(($height-$h)<($width-$w)){
                $width =& $w;
            } else {
                $height =& $h;
            }
        }
        echo $height."px";
    }
    
    function sync(){
        $this->image =& $this->temp;
        unset($this->temp);
        $this->temp = '';
        return;
    }

    function show(){
        $this->_sendHeader();
        switch($this->ext){
            case 'gif':
                ImageGIF($this->image);
                break;

            case 'png':
                ImagePNG($this->image);
                break;

            case 'jpg':
            case 'jpeg':
            case 'JPG':
                ImageJPEG($this->image);
                break;
        }
        return;
    }

    function _sendHeader(){
        switch($this->ext){
            case 'gif':
                header('Content-Type: image/gif');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;

            case 'jpg':
            case 'jpeg':
            case 'JPG':
                header('Content-Type: image/jpeg');
                break;
        }
    }

    function errorHandler(){
        //echo "error";
        exit();
    }

    function store($file){

        switch($this->ext){
            case 'gif':
                ImageGIF($this->image,$file);
                break;

            case 'jpg':
            case 'jpeg':
            case 'JPG':
                ImageJPEG($this->image,$file);;
                break;
        }

        return;
    }

    function watermark($pngImage, $left = 0, $top = 0){
        ImageAlphaBlending($this->image, true);
        $layer = ImageCreateFromPNG($pngImage);
        $logoW = ImageSX($layer);
        $logoH = ImageSY($layer);
        ImageCopy($this->image, $layer, $left, $top, 0, 0, $logoW, $logoH);
    }
}
