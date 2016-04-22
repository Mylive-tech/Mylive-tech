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
# Copyright (C) 2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
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
# @copyright      2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
# @version        5.0 Codename Transformer
# ################################################################################
 */
class Widget_ListingsMap extends Phpld_Widget {


    function getContent() {
        //var_dump($this->tpl->get_template_vars());die();
        Phpld_View::addJavascript('http://maps.google.com/maps/api/js?sensor=false&libraries=adsense');
        Phpld_View::addJavascript(DOC_ROOT.'/javascripts/gmaps/listingsMap.js');
	Phpld_View::addJavascript('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js');
        $listings = $this->getListings();
//var_dump($listings->count());die();
        $markers = array();
        foreach ($listings as $listing) {
            $markers[] = $this->createMarker($listing);
        }

        $this->tpl->assign('markers', json_encode($markers));
        $this->tpl->assign('default_item', json_encode($markers[0]));
        return $this->tpl->fetch('content.tpl');
    }

    public function getListings()
    {
        $listingsModel = new Model_Link();

        $links = $listingsModel->getLinks('LINK_TYPE = '.Model_Link_Entity::TYPE_BUSINESS  . ' AND `PLD_LINK`.`STATUS` = 2 ', 'PLD_LINK.ID ASC', 0, 50);

        return $links;
    }

    public function getFrontSettings()
    {
        $settings = parent::getFrontSettings();
        if (!$settings['ZOOM']) {
            $settings['ZOOM'] = 1;
        }
        if (!$settings['LAT']) {
            $settings['LAT'] = 0;
        }
        if (!$settings['LON']) {
            $settings['LON'] = 0;
        }

        return $settings;
    }

    public function  getSettingsForm()
    {
        $this->addJavascript('http://maps.google.com/maps/api/js?sensor=false&libraries=adsense');
        $settings = $this->getFrontSettings();

        $jsCode = 'jQuery(document).ready(function(){
            //jQuery("#LAT").parents("tr").hide();
            //jQuery("#LON").parents("tr").hide();
            jQuery("#LAT").parents("tr").before("<tr><td class=\"label required\"><label>Default position:</label></td><td><div id=\"map\"></div><p class=\"limitDesc\">Select default map position and zoom level</p></td></tr>");
            //jQuery("#ZOOM").parents("tr").hide();

            var mapOptions = {
                "zoom": '.$settings['ZOOM'].',
                "map": "map",
                "center": new google.maps.LatLng('.$settings['LAT'].', '.$settings['LON'].'),
                "mapTypeId": google.maps.MapTypeId.ROADMAP,
                "scaleControl": true,
                "center_changed" : function(){
                    var center = map.getCenter();
                    jQuery("#LAT").val(center.lat());
                    jQuery("#LON").val(center.lng());
                },
                "zoom_changed" : function(){
                    jQuery("#ZOOM").val(map.getZoom());
                }
            }

            map = new google.maps.Map(document.getElementById(mapOptions.map), mapOptions);

            jQuery("#map").height("300px");
        });';

        $this->addJavascriptCode($jsCode);
        return parent::getSettingsForm();
    }

    function createMarker($item) {
        $icon = FRONT_DOC_ROOT.'/images/map_icons/'.rand(1, 4).'.png';

        $info_title = (strlen($item['TITLE']) > 20) ? substr($item['TITLE'], 0, 20).'...' : $item['TITLE'];

        $title_link = $item->getUrl();
        $address_info = '';

        if ($item['COUNTRY']) {
            $address_info = $item['COUNTRY'];
        }

        if ($item['CITY']) {
            $address_info .= $item['CITY'];
        }

        if ($item['STATE']) {
            $address_info .= ", {$item['STATE']}";
        }

        $address_info .= " {$item['ZIP']}";
        $image = ($item['IMAGE'] ? "<img width=\"80\" height=\"53\" style=\"width: 80px; height: 53px; float:left;\" class=\"map_listing_thumb\" src=\"".DOC_ROOT."/thumbnail.php?pic={$item['IMAGE']}&width=100\" border=\"0\" />" : '');
        $marker = array(
            'name' 			=> $item['TITLE'],
            'location' 		=> $address_info,
            'lat'           => $item['LAT'],
            'lon'           => $item['LON'],
            'address'               => $address_info,
            'message' 	=> "<div class=\"map_infowindow\">".$image."<a href=\"{$title_link}\" class=\"marker_link\" style=\"text-decoration:underline;\">{$info_title}</a><br /><div class=\"marker_link_descr\">{$item['ADDRESS']}<br />{$address_info}</div></div>",
            'icon'				=> $icon
        );

        return $marker;
    }
}

?>