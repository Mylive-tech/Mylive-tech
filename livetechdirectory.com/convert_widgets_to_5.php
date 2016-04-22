<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 require_once 'init.php';

/*

Replace PLD_WIDGET_ZONES

Add PLD_WIDGET_ACTIVATED.OPTIONS column

Convert widget settings (I have a script for this)

Remove PLD_WIDGET_SETTINGS table

Replace in PLD_WIDGET_ZONES MAIN_NOT_ON_HOMEPAGE on ANY_OTHER_PAGE

Add CategorySubcategories and CategoryListings widgets to PLD_WIDGET_ACTIVATED for CATEGORY_PAGE Zone to render category page

*/

if ($_REQUEST['confirm'] == 1) {
    /*
     * Update PLD_WIDGET_ZONES
     */

    $sql = "
    DROP TABLE `PLD_WIDGET_ZONES`;
    CREATE TABLE `PLD_WIDGET_ZONES` (
      `NAME` varchar(255) NOT NULL,
      `TYPE` varchar(255) NOT NULL,
      `CONTROLLER` varchar(50) DEFAULT NULL COMMENT 'MVC Controller',
      `ACTION` varchar(50) DEFAULT NULL COMMENT 'MVC Controller action',
      PRIMARY KEY (`NAME`)
    ) ENGINE=MyISAM COMMENT='Stores zones for widget applications';";

    $db->Execute($sql);

    $sql = '
    INSERT INTO PLD_WIDGET_ZONES VALUES
    ("LEFT_COLUMN","VERTICAL","",""),
    ("RIGHT_COLUMN","VERTICAL","",""),
    ("HOMEPAGE","CENTRAL","index","index"),
    ("ANY_OTHER_PAGE","CENTRAL","",""),
    ("LINK_DETAIL","CENTRAL","details","index"),
    ("CATEGORY_PAGE","CENTRAL","category","index")
    ';

    $db->Execute($sql);
    echo "PLD_WIDGET_ZONES updated <br />\n";

    /*
     * Alter  PLD_WIDGET_ACTIVATED table
     */
    $sql = "ALTER TABLE `PLD_WIDGET_ACTIVATED` ADD COLUMN `OPTIONS` text  DEFAULT NULL AFTER `ORDER_ID`;";
    $db->Execute($sql);
    echo "PLD_WIDGET_ACTIVATED updated <br />\n";

    /*
     * Convert old widgets settings
     */

    $activeWidgets = $db->getAll("SELECT NAME FROM `{$tables['widget']['name']}`");

    foreach ($activeWidgets as $activeWidget) {
        $widgetSettings = $db->getAll("SELECT * FROM  PLD_WIDGET_SETTINGS WHERE WIDGET_NAME = '".$activeWidget['NAME']."'");
        if ($widgetSettings != false) {
            $newSetings = array();
            foreach ($widgetSettings as $setting) {
                $newSetings[$setting['IDENTIFIER']] = $setting['SETTING_VALUE'];
            }
            $db->Execute("UPDATE `{$tables['widget_activated']['name']}`
            SET OPTIONS = '".serialize($newSetings)."'
            WHERE NAME = '".$activeWidget['NAME']."'");
        }
    }
    echo "Widgets options converted <br />\n";

    /*
     * Update Zones for activated widgets
     */

    $sql = "UPDATE PLD_WIDGET_ACTIVATED SET ZONE = 'ANY_OTHER_PAGE' WHERE ZONE = 'MAIN_NOT_ON_HOMEPAGE';";
    $db->Execute($sql);
    $sql = "UPDATE PLD_WIDGET_ACTIVATED SET ZONE = 'MAIN_ON_HOMEPAGE' WHERE ZONE = 'HOMEPAGE';";
    $db->Execute($sql);
    $sql = "DELETE FROM PLD_WIDGET_ACTIVATED WHERE ZONE = 'ARTICLE_DETAIL';";
    $db->Execute($sql);

    echo "Active widget zones updated <br />\n";

    /*
     * Add category display widgets
     */

    $sql = "INSERT INTO PLD_WIDGET_ACTIVATED (NAME, ZONE, ORDER_ID, OPTIONS, ACTIVE) VALUES
    ('CategoryListings', 'CATEGORY_PAGE', 3, NULL, 1),
    ('AllCategories', 'HOMEPAGE', 1, NULL, 1),
    ('CategorySubcategories', 'CATEGORY_PAGE', 2, NULL, 1)";
    $db->Execute($sql);

    echo "Category display widgets added <br />\n";



} else {
    ?>
    <h2>Make sure you have backed up "PLD_WIDGET_ZONES", "PLD_WIDGET_ACTIVATED", "PLD_WIDGET_SETTINGS",  tables.</h2>
        <a href="?confirm=1">Start conversion</a>
    <?php
}
