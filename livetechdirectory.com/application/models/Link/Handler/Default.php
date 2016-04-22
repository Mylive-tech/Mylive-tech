<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
class Model_Link_Handler_Default extends Model_Link_Handler_Abstract
{
    public function _assignPlaceholders($purpose, $style="list")
    {
        $view = Phpld_View::getView();

        $view->assign('LISTING_CATEGORIES_LIST', $this->_entity->getCategories());
        $view->assign('SUBMIT_ITEMS', $this->_entity->getSubmitItems());
        $view->assign('LISTING_SUBMIT_ITEMS', $view->fetch('views/_listings/_placeholders/submitItems.tpl'));
        $view->assign('LISTING_URL_TITLE', $view->fetch('views/_listings/_placeholders/listingUrlTitle.tpl'));
		$view->assign('LISTING_ARTICLE_TITLE', $view->fetch('views/_listings/_placeholders/listingArticleTitle.tpl'));
        $view->assign('LISTING_URL', $view->fetch('views/_listings/_placeholders/listingUrl.tpl'));
		$view->assign('WEBPAGE_URL', $view->fetch('views/_listings/_placeholders/webpageUrl.tpl'));
        switch ($style) {
            case Model_Link_Entity::STYLE_LIST:
                $this->_entity['THUMB_WIDTH'] = ($this->_entity['THUMBNAIL_WIDTH_LIST']?$this->_entity['THUMBNAIL_WIDTH_LIST']:$this->_entity['DEFAULT_THUMBNAIL_LIST']);
                $view->assign('LISTING_THUMBNAIL', $view->fetch('views/_listings/_placeholders/listingThumbnailList.tpl'));
                break;

            case Model_Link_Entity::STYLE_GRID:
                $this->_entity['THUMB_WIDTH'] = ($this->_entity['THUMBNAIL_WIDTH_GRID']?$this->_entity['THUMBNAIL_WIDTH_GRID']:$this->_entity['DEFAULT_THUMBNAIL_GRID']);
                $view->assign('LISTING_THUMBNAIL', $view->fetch('views/_listings/_placeholders/listingThumbnailGrid.tpl'));
                break;

        }

        $view->assign('LISTING_IMAGE', $view->fetch('views/_listings/_placeholders/listingImage.tpl'));
        $view->assign('LISTING_IMAGE_WITH_URL', $view->fetch('views/_listings/_placeholders/listingImageWithUrl.tpl'));
        $view->assign('LISTING_CATEGORIES', $view->fetch('views/_listings/_placeholders/categories.tpl'));
		$view->assign('LISTING_CATEGORIES_DETAILS', $view->fetch('views/_listings/_placeholders/categories2.tpl'));
        $view->assign('READ_MORE_LINK', $view->fetch('views/_listings/_placeholders/readMoreLink.tpl'));
        $view->assign('LISTING_STATS', $view->fetch('views/_listings/_placeholders/listingStats.tpl'));
        $view->assign('ADDRESS', $view->fetch('views/_listings/_placeholders/address.tpl'));
        $view->assign('ANNOUNCE', $view->fetch('views/_listings/_placeholders/announce.tpl'));
        //if (GMAP_ENABLE) {
            $view->assign('GOOGLE_MAP', $view->fetch('views/_listings/_placeholders/googleMap.tpl'));
        //}
		if (CONTACT_LISTING){
			$view->assign('LISTING_CONTACT_LISTING', $view->fetch('views/_listings/_placeholders/contactListing.tpl'));
		}
        if (SHOW_PAGERANK) {
            $view->assign('PAGERANK', $view->fetch('views/_listings/_placeholders/pagerank.tpl'));
        }
        if (LINK_RATING) {
            $view->assign('LISTING_RATING', $view->fetch('views/_listings/_placeholders/listingDetailsRating.tpl'));
        }
        if (LINK_COMMENT) {
            $view->assign('LISTING_COMMENTS', $view->fetch('views/_listings/_placeholders/listingComments.tpl'));
        }
        if (LINK_TELL_FRIEND) {
            $view->assign('LISTING_TELL_FRIEND', $view->fetch('views/_listings/_placeholders/tellFriend.tpl'));
        }
	
	if (ALLOW_AUTHOR_INFO) {
            $view->assign('LISTING_AUTHOR_INFO', $view->fetch('views/_listings/_placeholders/authorInfo.tpl'));
        }
    }

}