<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
/**
 *
 * 1. Get all articles
 *
 * 2. Go through all articles and for each iteration:
 *
 *    a. Insert it to LINKS table and get new ID
 *    b. Get Old articles data from rating and review and insert it to LINKS related tables with this new ID
 *    c. Update the link_seq_count table to correct number
 */

require_once 'init.php';


if ($_REQUEST['confirm'] == 1) {
    $articles = $db->getAll('SELECT P.`TITLE`, P.`DESCRIPTION` as ANNOUNCE, P.`ARTICLE` as DESCRIPTION, P.`CATEGORY_ID`,
    P.`STATUS`, P.`RATING`, P.`VOTES`, P.`HITS`, P.`VALID`, P.`COMMENT_COUNT`, P.`OWNER_ID`,
    P.`OWNER_NAME`, P.`OWNER_EMAIL`, P.`OWNER_NOTIF`, P.`OWNER_EMAIL_CONFIRMED`, P.`DATE_MODIFIED`,
    P.`DATE_ADDED`, P.`IPADDRESS`, P.`META_KEYWORDS`, P.`META_DESCRIPTION`
    FROM PLD_ARTICLE P');
    //die(mysql_error());
    //var_dump($articles);die();
    $linkModel = new Model_Link();
    $i = 0;
    foreach ($articles as $article) {
        //var_dump($article);die();
        $article['LINK_TYPE'] = Model_Link_Entity::TYPE_ARTICLE;
        $oldId = $article['ID'];
        $result = db_replace('link', $article, 'ID');
        if ($result == 2) {
            $id = $db->Insert_ID();

            $seoUrl = $linkModel->seoUrl($article, $id);
            $db->execute('UPDATE '.$tables['link']['name'].' SET `CACHE_URL` = "'.$seoUrl.'" WHERE ID = '.$id);
            $db->execute("INSERT INTO PLD_LINK_RATING (LINK_ID, IPADDRESS, DATE_ADDED)
            SELECT * PLD_ARTICLE ($id, IPADDRESS, DATE_ADDED)");
//            $db->execute("INSERT INTO PLD_LINK_RATING (LINK_ID, IPADDRESS, DATE_ADDED)
//            SELECT * PLD_ARTICLE ($id, IPADDRESS, DATE_ADDED)");
            $i++;
        }
    }

    echo "$i Articles imported <br />\n";  
	$seqcount2 	= $db->GetRow("SELECT `ID` FROM  `PLD_LINK` ORDER BY  `PLD_LINK`.`ID` DESC Limit 1");
	$seqcount = ($seqcount2['ID'] +1);
	$db->Execute("UPDATE `PLD_LINK_SEQ` SET `id` =" .$seqcount);

	 echo "Link Sequence count updated <br />\n";  
} else {
    ?>
<h2>Make sure you have backed up "PLD_ARTICLE" and "PLD_ARTICLE_RATING" tables.</h2>
<a href="?confirm=1">Start conversion</a>
<?php
}