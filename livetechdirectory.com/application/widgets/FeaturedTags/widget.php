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
 
class Widget_FeaturedTags extends Phpld_Widget {

	function getContent() {
		$set = $this->getFrontSettings();

        $tagsModel = new Model_Tag();
        $tags = $tagsModel->getAllTags(false, 'STATUS = '.Model_Tag_Entity::STATUS_ACTIVE.' AND ID IN ('.$set['TAGS'].')');

		$this->tpl->assign("TITLE", $set['TITLE']);
		$this->tpl->assign("TAGS", $tags);

		return $this->tpl->fetch('content.tpl');
	}

    public function  getSettingsForm()
    {
        $tagsModel = new Model_Tag();
        $allTags = $tagsModel->getAllTags();
        $tags = array();
        foreach ($allTags as $tag) {
            $tags[] = '{id:'.$tag['ID'].', text:"'.$tag['TITLE'].'"}';
        }
        $jsCode = 'jQuery(document).ready(function(){
            jQuery("#TAGS").select2(
                                    {
                                        tags: [
                                        '.implode(',', $tags).'
                                        ]

                                    }
                            );
                        });
                    ';

        $this->addJavascriptCode($jsCode);
        $this->addJavascript(FRONT_DOC_ROOT.'javascripts/select2/select2.js');
        $this->addStylesheet(FRONT_DOC_ROOT.'javascripts/select2/select2.css');
        return parent::getSettingsForm();
    }


    function saveSettings($set) {
        global $db;
        global $tables;

        $tags = $_REQUEST['TAGS'];
        $tags = explode(',',$tags);
        if (is_string($tags)) {
            $tags = array($tags);
        }
        foreach ($tags as $key => $tag) {
            if (!is_numeric($tag)) {
                unset($tags[$key]);
            }
        }

        $settings = array();
        foreach ($set as $setting) {
            $settings[$setting['IDENTIFIER']] = $setting['SETTING_VALUE'];
        }
        try {
            $settings['TAGS'] = implode(',',$tags);
            //var_dump($settings);die();
            $db->Execute("UPDATE `{$tables['widget_activated']['name']}` SET OPTIONS = '" . serialize($settings) . "' WHERE ID = " . $this->id);
            return true;
        } catch (Exception $e) {
            return false;
        }

    }
}
