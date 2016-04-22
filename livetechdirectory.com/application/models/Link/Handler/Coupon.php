<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	

	

class Model_Link_Handler_Coupon extends Model_Link_Handler_Default {

    public function _assignPlaceholders($purpose, $style = "grid") {
        $view = Phpld_View::getView();

        parent::_assignPlaceholders($purpose, $style);

        if (LINK_RATING) {
            $view->assign('LISTING_RATING', $view->fetch('views/_listings/_placeholders/couponRating.tpl'));
        }
    }
}