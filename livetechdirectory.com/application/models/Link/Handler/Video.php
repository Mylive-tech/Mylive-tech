<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	

	

class Model_Link_Handler_Video extends Model_Link_Handler_Default {

    public function _assignPlaceholders($purpose, $style = "grid") {
        $view = Phpld_View::getView();

        parent::_assignPlaceholders($purpose, $style);
        $oembed = new Model_Link_Handler_Oembed();

        $this->_entity['VIDEO_TYPE'] = $this->getVideoType($this->_entity['URL']);
        $this->_entity['VIDEO_ID'] = $this->getVideoId($this->_entity['URL']);

        $video = null;
        $image = null;
        $provider = $oembed->getProvider($this->_entity['URL']);

        $args = array('width' => 640, 'height' => 390);
        if (!empty($this->_entity['VIDEO_CACHE'])) {
            $data = unserialize($this->_entity['VIDEO_CACHE']);
        } else {
            $data = $oembed->fetch($provider, $this->_entity['URL'], $args);
            $tables = Phpld_Db::getInstance()->getTables();
            $db = Phpld_Db::getInstance()->getAdapter();
            $db->Execute('UPDATE '.$tables['link']['name']. ' SET VIDEO_CACHE = '.$db->qstr(serialize($data)).' WHERE ID = '.$this->_entity['ID']);
        }
        if ($data) {
            $photoData = $data;
            $photoData->type = 'photo';
            $photoData->width = 200;
            //$image = $oembed->toHtml($photoData);

            $this->_entity['VIDEO_THUMB'] = $photoData->thumbnail_url;
            $view->assign('VIDEO_THUMB', $photoData->thumbnail_url);

            $videoData = $data;
            $videoData->type = 'video';
            $videoData->width = 640;
            $videoData->height = 390;
			if($videoData->provider_name == 'YouTube'){
			$videoData->html = str_replace('?feature=oembed','?rel=0&autoplay=1&feature=oembed',$videoData->html);
			}
            $video = $oembed->toHtml($videoData);
        }

        $view->assign('LISTING_VIDEO_HTML', $video);
        $view->assign('LISTING_VIDEO_URL_TITLE', $view->fetch('views/_listings/_placeholders/listingVideoUrlTitle.tpl'));  
        $view->assign('LISTING_VIDEO_EMBED', $view->fetch('views/_listings/_placeholders/videoEmbed.tpl'));
        switch ($style) {
            case Model_Link_Entity::STYLE_LIST:

                $this->_entity['THUMB_WIDTH'] = ($this->_entity['THUMBNAIL_WIDTH_LIST']?$this->_entity['THUMBNAIL_WIDTH_LIST']:$this->_entity['DEFAULT_THUMBNAIL_LIST']);
                $view->assign('VIDEO_THUMBNAIL', $view->fetch('views/_listings/_placeholders/listingThumbnailList.tpl'));
                break;

            case Model_Link_Entity::STYLE_GRID:
                $this->_entity['THUMB_WIDTH'] = ($this->_entity['THUMBNAIL_WIDTH_GRID']?$this->_entity['THUMBNAIL_WIDTH_GRID']:$this->_entity['DEFAULT_THUMBNAIL_GRID']);
                $view->assign('VIDEO_THUMBNAIL', $view->fetch('views/_listings/_placeholders/listingThumbnailGrid.tpl'));
                break;
        }
        /*if ($this->_entity['IMAGE'] == null)
            $view->assign('VIDEO_THUMBNAIL', $view->fetch('views/_listings/_placeholders/videoThumbnail.tpl'));
         * 
         */
    }

    public function getVideoId($url) {
        $type = $this->getVideoType($url);

        switch ($type) {
            case 'YOUTUBE':
                preg_match("/.*?v=([a-zA-Z0-9]+)/", $url, $matches);
                if (empty($matches[1]))
                    preg_match("/.*\/([^?]+)/", $url, $matches);
                $result = $matches[1];
                break;

            case 'VIMEO':
                preg_match("/vimeo.com\/(\d+)/", $url, $matches);
                $result = $matches[1];
                if (!$this->_entity['THUMB_URL']) {
                    $this->saveVideoThumb($type, $result, $this->_entity['ID']);
                }
                break;

            case 'HULU':
                if (!$this->_entity['THUMB_URL'] || !$this->_entity['EMBED_URL']) {
                    $result = getHuluEmbed($url, $this->_entity['ID']);
                } else {
                    $result = $this->_entity['EMBED_URL'];
                }
                break;

            case 'DM':
                preg_match("/dailymotion.com\/video\/([a-zA-Z0-9]+)_/", $url, $matches);
                $result = $matches[1];
                break;
        }



        return $result;
    }

    public function getHuluEmbed($url, $link_id, $return_thumb = false) {
        global $db, $tables;

        $url = "http://www.hulu.com/api/oembed?format=json&url=" . urlencode($url);

        $json_response = file_get_contents($url);
        $json_data = json_decode($json_response, true);

        if ($json_data) {
            $db->Execute("UPDATE `{$tables['link']['name']}` SET `EMBED_URL` = '{$json_data['embed_url']}', `THUMB_URL` = '{$json_data['thumbnail_url']}' WHERE `ID` = '{$link_id}'");
            if ($return_thumb) {
                return $json_data['thumbnail_url'];
            } else {
                return $json_data['embed_url'];
            }
        }

        return false;
    }

    public function getDmThumb($url, $link_id) {
        global $db, $tables;

        $url = "http://www.dailymotion.com/services/oembed?format=json&url=" . urlencode($url);

        $json_response = file_get_contents($url);
        $json_data = json_decode($json_response, true);

        if ($json_data) {
            $db->Execute("UPDATE `{$tables['link']['name']}` SET `THUMB_URL` = '{$json_data['thumbnail_url']}' WHERE `ID` = '{$link_id}'");
            return $json_data['thumbnail_url'];
        }

        return false;
    }

    public function getVideoThumbUrl($url) {

        $video_type = $this->getVideoType($url);
        $id = $this->getVideoId($url);

        switch ($video_type) {
            case 'YOUTUBE':
                $imgurl = "http://img.youtube.com/vi/" . $id . "/1.jpg";
                break;

            case 'HULU':
                if (!$this->_entity['THUMB_URL'] || !$this->_entity['EMBED_URL']) {
                    $imgurl = $this->getHuluEmbed($url, $this->_entity['ID'], true);
                } else {
                    $imgurl = $this->_entity['THUMB_URL'];
                }
                break;

            case 'VIMEO':
                if (!$this->_entity['THUMB_URL'] || !$this->_entity['EMBED_URL']) {
                    $imgurl = $this->saveVideoThumb($video_type, $this->_entity['ID'], true);
                } else {
                    $imgurl = $this->_entity['THUMB_URL'];
                }
                break;

            case 'DM':
                if (!$this->_entity['THUMB_URL']) {
                    $imgurl = $this->getDmThumb($this->_entity['URL'], $this->_entity['ID']);
                } else {
                    $imgurl = $this->_entity['THUMB_URL'];
                }
                break;
        }

        $result = array('THUMB_URL' => $imgurl,
            'VIDEO_TYPE' => $video_type);

        return $result;
    }

    public function saveVideoThumb($type, $video_id, $link_id) {
        global $db, $tables;

        switch ($type) {
            case 'VIMEO':
                $json_response = file_get_contents("http://vimeo.com/api/oembed.json?url=http%3A//vimeo.com/{$video_id}");
                $json_data = json_decode($json_response);
                $result = $json_data->thumbnail_url;
        }

        if ($result) {
            $db->Execute("UPDATE `{$tables['link']['name']}` SET `THUMB_URL` = '{$result}' WHERE `ID` = '{$link_id}'");
            return $result;
        }
    }

    public function getVideoType($url) {
        if ((strpos($url, 'http://www.hulu.com/') === 0) OR (strpos($url, 'http://hulu.com/') === 0)) {
            $result = 'HULU';
        }

        if ((strpos($url, 'http://www.youtube.com/') === 0) OR (strpos($url, 'http://youtube.com/') === 0) OR (strpos($url, 'http://youtu.be/') === 0)) {
            $result = 'YOUTUBE';
        }

        if ((strpos($url, 'http://www.vimeo.com/') === 0) OR (strpos($url, 'http://vimeo.com/') === 0)) {
            $result = 'VIMEO';
        }

        if ((strpos($url, 'http://www.dailymotion.com/') === 0) OR (strpos($url, 'http://dailymotion.com/') === 0)) {
            $result = 'DM';
        }
        return $result;
    }

    public function getVideoJson($url) {
        $oembedUrls = array(
            'www.youtube.com' => 'http://www.youtube.com/oembed?url=$1&format=json',
            'www.dailymotion.com' => 'http://www.dailymotion.com/api/oembed?url=$1&format=json',
            'www.vimeo.com' => 'http://vimeo.com/api/oembed.xml?url=$1&format=json',
            'www.blip.tv' => 'http://blip.tv/oembed/?url=$1&format=json',
            'www.hulu.com' => 'http://www.hulu.com/api/oembed?url=$1&format=json',
            'www.viddler.com' => 'http://lab.viddler.com/services/oembed/?url=$1&format=json',
            'www.qik.com' => 'http://qik.com/api/oembed?url=$1&format=json',
            'www.revision3.com' => 'http://revision3.com/api/oembed/?url=$1&format=json',
            'www.scribd.com' => 'http://www.scribd.com/services/oembed?url=$1&format=json',
            'www.wordpress.tv' => 'http://wordpress.tv/oembed/?url=$1&format=json',
            'www.5min.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'www.collegehumor.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'www.thedailyshow.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'www.funnyordie.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'www.livejournal.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'www.metacafe.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'www.xkcd.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'www.yfrog.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'youtube.com' => 'http://www.youtube.com/oembed?url=$1&format=json',
            'dailymotion.com' => 'http://www.dailymotion.com/api/oembed?url=$1&format=json',
            'vimeo.com' => 'http://vimeo.com/api/oembed.xml?url=$1&format=json',
            'blip.tv' => 'http://blip.tv/oembed/?url=$1&format=json',
            'hulu.com' => 'http://www.hulu.com/api/oembed?url=$1&format=json',
            'viddler.com' => 'http://lab.viddler.com/services/oembed/?url=$1&format=json',
            'qik.com' => 'http://qik.com/api/oembed?url=$1&format=json',
            'revision3.com' => 'http://revision3.com/api/oembed/?url=$1&format=json',
            'scribd.com' => 'http://www.scribd.com/services/oembed?url=$1&format=json',
            'wordpress.tv' => 'http://wordpress.tv/oembed/?url=$1&format=json',
            '5min.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'collegehumor.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'thedailyshow.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'funnyordie.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'livejournal.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'metacafe.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'xkcd.com' => 'http://www.oohembed.com/oohembed/?url=$1',
            'yfrog.com' => 'http://www.oohembed.com/oohembed/?url=$1'
        );

        $oembedData = null;
        if (!empty($url)) {
            $parts = parse_url($url);
            $host = $parts['host'];
            if (empty($host) || !array_key_exists($host, $oembedUrls)) {
                return $oembedData;
            } else {
                $oembedContents = @file_get_contents(str_replace('$1', $url, $oembedUrls[$host]));
                $oembedData = @json_decode($oembedContents);
            }
        }
        return $oembedData;
    }

}