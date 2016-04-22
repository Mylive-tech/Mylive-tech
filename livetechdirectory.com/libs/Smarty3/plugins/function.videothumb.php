<?php

function smarty_function_videothumb($params, &$smarty) {

    $id = trim(isset($params['id']) ? $params['id'] : '');
    $id = str_replace('#', '', $id);

    $listing = $params['listing'];

    $thumbfolder = SERVER_DOC_ROOT . "/temp/thumbs/";
    $picture = $thumbfolder . $id . ".jpg";
//$url =  trim(isset($params['url']) ? $params['url']:'');
    $tcache = trim(isset($params['tcache']) ? $params['tcache'] : 259200);
    $timemade = filemtime($picture);
    if (time() - $tcache > $timemade) {
        unlink($picture);
    }
    //echo $picture;
    if (!file_exists($picture) || filesize($picture) == 0) {

        //$imgurl = "http://open.thumbshots.org/image.pxf?url=".$url; 
        //$imgurl = "http://img.youtube.com/vi/" . $id . "/1.jpg";
        //var_dump($link_data);

        $image_info = $listing->getHandler()->getVideoThumbUrl($listing['URL']);
        //var_dump($image_info);
        $imgurl = $image_info['THUMB_URL'];
        $video_type = $image_info['VIDEO_TYPE'];

        if ($video_type == 'DM') {
            $picture = str_replace('.png', '.jpg', $picture);
        }

        /* $ch = curl_init();  
          curl_setopt ($ch, CURLOPT_URL, $imgurl);
          curl_setopt ($ch, CURLOPT_HEADER, 0);
          curl_setopt($ch, CURLOPT_TIMEOUT, 15);
          ob_start();
          curl_exec ($ch);
          curl_close ($ch);
          $string = ob_get_contents();
          ob_end_clean(); */

        $string = file_get_contents($imgurl);

        $content_len = strlen($string);

        if (!$string == "" && $content_len > "200") {
            $f = fopen($picture, 'w+');
            fwrite($f, $string);
            fclose($f);
        }

        /* if (file_get_contents($picture) !== $string) {
          $f = fopen($picture, 'w+');
          fwrite($f, $string);
          fclose($f);
          } */

        resizeImg($picture, $picture, 180, 140);

        //$im = ImageCreateFromPNG($picture);
        //imagepng($im);
    }

    if ($listing['VIDEO_THUMB'])
        echo "<img src='" . $listing['VIDEO_THUMB'] . "' class='flexible bordered float-left' width='" . $listing['THUMB_WIDTH'] . "' />";
    elseif ($listing['MAIN_THUMB'])
        echo "<img src='" . DOC_ROOT . "/uploads/thumb/{$listing['MAIN_THUMB']}' class='flexible bordered float-left' />";
    else if (file_exists($picture))
        echo "<img src='" . DOC_ROOT . "/temp/thumbs/" . $id . ".jpg'  class='flexible bordered float-left' />";
    else
        echo "<img src='" . DOC_ROOT . "/images/play-video.jpg' class='flexible bordered float-left' width='150'  />";
}

?>