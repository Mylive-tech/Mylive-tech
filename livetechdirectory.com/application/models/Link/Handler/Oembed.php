<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	

/*
  oembed-php - A simple and lightweight PHP library to implement oEmbed support

  Copyright (C) 2010 Fabian Pimminger

  This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

  You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.
 */

class Model_Link_Handler_Oembed {

    protected $providers = array();
    protected static $_instance;
    
    public static function getInstance()
    {
        
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    function __construct() {

        $this->providers["|http://(www\.)?youtube.com/watch.*|i"] = "http://www.youtube.com/oembed";
        $this->providers["|http://(www\.)?youtube.com/.*|i"] = "http://www.youtube.com/oembed";
        $this->providers["|http://(www\.)?youtu.be/.*|i"] = "http://www.youtube.com/oembed";
        $this->providers["|http://(www\.)?flickr.com/.*|i"] = "http://www.flickr.com/services/oembed/";
        $this->providers["|http://(www\.)?vimeo.com/.*|i"] = "http://vimeo.com/api/oembed.json";
        $this->providers["|http://(www\.)?viddler.com/.*|i"] = "http://lab.viddler.com/services/oembed/";
        $this->providers["|http://(www\.)?dailymotion.com/.*|i"] = "http://dailymotion.com/api/oembed/";
        $this->providers["|http://(www\.)?hulu.com/.*|i"] = "http://blip.tv/oembed";
        $this->providers["|http://(www\.)?qik.com/.*|i"] = "http://qik.com/api/oembed";
        $this->providers["|http://(www\.)?revision3.com/.*|i"] = "http://revision3.com/api/oembed";
        $this->providers["|http://(www\.)?scribd.com/.*|i"] = "http://scribd.com/services/oembed";
        $this->providers["|http://(www\.)?wordpress.tv/.*|i"] = "http://wordpress.tv/oembed";
        $this->providers["|http://(www\.)?5min.com/.*|i"] = "http://oohembed.com/oohembed";
        $this->providers["|http://(www\.)?collegehumor.com/.*|i"] = "http://oohembed.com/oohembed";
        $this->providers["|http://(www\.)?thedailyshow.com/.*|i"] = "http://oohembed.com/oohembed";
        $this->providers["|http://(www\.)?funnyordie.com/.*|i"] = "http://oohembed.com/oohembed";
        $this->providers["|http://(www\.)?livejournal.com/.*|i"] = "http://oohembed.com/oohembed";
        $this->providers["|http://(www\.)?metacafe.com/.*|i"] = "http://oohembed.com/oohembed";
        $this->providers["|http://(www\.)?xkcd.com/.*|i"] = "http://oohembed.com/oohembed";
        $this->providers["|http://(www\.)?yfrog.com/.*|i"] = "http://oohembed.com/oohembed";     
    }

    function fetch($provider_url, $content_url, $args = "") {
        $query_string = http_build_query(array('url' => $content_url, 'maxwidth' => $args["width"], 'maxheight' => $args["height"]));

        $query_string_format["json"] = http_build_query(array('format' => "json"));
        $query_string_format["xml"] = http_build_query(array('format' => "xml"));

        // First query the JSON ressource. If this fails, try to query the xml one.

        $result_json = $this->queryProvider($provider_url . "?" . $query_string . "&" . $query_string_format["json"]);

        if ($result_json["success"]) {

            $result = json_decode(trim($result_json["data"]), false);

            if (is_object($result)) {

                return $result;
            } else {

                return false;
            }
        } elseif (function_exists('simplexml_load_string')) {

            $result_xml = $this->queryProvider($provider_url . "?" . $query_string . "&" . $query_string_format["xml"]);

            if ($result_xml["success"]) {

                $result = simplexml_load_string(trim($result_xml["data"]));

                if (is_object($result)) {

                    return $result;
                } else {

                    return false;
                }
            } else {

                return false;
            }
        } else {

            return false;
        }
    }

    function getHtml($url, $args = "") {

        $url = trim($url);

        foreach ($this->providers as $regex => $provider_url) {
            if (preg_match($regex, $url)) {
                $provider = $provider_url;
                break;
            }
        }

        //TODO: DISCOVER

        if ($provider) {

            if ($data = $this->fetch($provider, $url, $args)) {

                return $this->toHtml($data);
            } else {

                return $url;
            }
        } else {

            return $url;
        }
    }

    function getProvider($url){
        $url = trim($url);
        $provider = null;
        foreach ($this->providers as $regex => $provider_url) {
            if (preg_match($regex, $url)) {
                $provider = $provider_url;
                break;
            }
        }
        return $provider;
    }
    
    function toHtml($data) {

        if (is_object($data) || !empty($data->type)) {

            switch ($data->type) {
                case 'photo':
                    if (empty($data->thumbnail_url) || empty($data->width) || empty($data->height)) {
                        return false;
                    } else {

                        $title = (!empty($data->title)) ? $data->title : '';

                        $html = '<img src="' . $this->escapeHTML($this->safeUrl($data->thumbnail_url)) . '" alt="' . $this->escapeHTML($title) . '" width="' . $this->escapeHTML($data->width) . '"  />';
                    }

                    break;

                case 'video':
                case 'rich':
                    $html = (!empty($data->html) ) ? $data->html : false;
                    break;

                case 'link':
                    $html = (!empty($data->title) ) ? '<a href="' . $this->safeUrl($url) . '">' . $this->escapeHTML($data->title) . '</a>' : false;
                    break;

                default:
                    return false;
            }

            return (string) $html;
        } else {

            return false;
        }
    }

    function discover($url) {
        return $result;
    }

    function queryProvider($url) {

        $result = array();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        if ($data = curl_exec($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code >= 200 && $http_code < 300) {
                $result["success"] = true;
                $result["data"] = $data;
                $result["http_code"] = $http_code;
            } else {
                $result["success"] = false;
                $result["http_code"] = $http_code;
                $result["url"] = $url;
            }
        } else {
            $result["success"] = false;
            $result["curl_error_code"] = curl_errno($ch);
        };

        curl_close($ch);

        return $result;
    }

    function safeUrl($url) {
        return (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) ? $url : "";
    }

    function escapeHTML($html) {
        return htmlentities($html, ENT_COMPAT, "ISO-8859-1", false);
    }

    function autoEmbed($content, $args = "") {
        
        if (is_array($args)) {
            $this->autoEmbedArgs = $args;
        } else {
            $this->autoEmbedArgs = array();
        }

        return preg_replace_callback('|^[ \t]*(https?://[^\s"]+)\s*$|im', array(&$this, 'autoEmbedCallback'), $content);
    }

    function autoEmbedCallback($match) {
        return $this->getHTML($match[0], $this->autoEmbedArgs);
    }

}

?>