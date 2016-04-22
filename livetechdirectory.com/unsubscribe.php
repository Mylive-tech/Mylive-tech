<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 

/*
 *
 */
require ('init.php');

if (!isset($_REQUEST['e']) || $_REQUEST['e'] == '')
    die;
?>
<html>
    <head>
        <meta http-equiv="refresh" content="5; URL=<?php echo SITE_URL; ?>">
        <style type="text/css">
            body {font-size: 13px; color: #333333; font-family: Arial CE}
            p {margin: 0; padding: 0}
        </style>
    <head>
    <body>
        <?php
        $data = array(
            'OWNER_NEWSLETTER_ALLOW' => 0
        );
        $where = "MD5(OWNER_EMAIL) = '" . $_REQUEST['e'] . "'";
        $sqlUnsubscribe = "UPDATE " . $tables['link']['name'] . " SET OWNER_NEWSLETTER_ALLOW = 0 WHERE MD5(OWNER_EMAIL) = " . $_REQUEST['e'];
        $unsubscribe = $db->AutoExecute($tables['link']['name'], $data, 'UPDATE', $where);
        ?>
        <p><b>You have been unsubscribe with <?php echo $unsubscribe ? 'success' : 'failure'; ?>!</b></p>
        <p>If your browser don't redirect you after 5 seconds please click <a href="<?php echo SITE_URL; ?>">here</a>.</p>
    </body>
</html>
