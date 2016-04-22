<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 

	 

    require_once 'include/config.php';
    require_once 'include/settings.php';
    require_once 'include/tables.php';
    require_once 'include/validation_functions.php';

    require_once 'include/adodb_extender.php';
    //require_once 'libs/intsmarty/intsmarty.class.php';

    //Connect to database
    $db = ADONewConnection(DB_DRIVER);
    if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
    {
       $db->SetFetchMode(ADODB_FETCH_ASSOC);

       //Load extenders to count executions
       $db->fnExecute = 'CountExecs';
       $db->fnCacheExecute = 'CountCachedExecs';
    }
    else
    {
       define('ERROR', 'ERROR_DB_CONNECT');
       exit('ERROR :: Could not connect to database server!');
    }

    //Determin category ID to build tree on
    $categID = trim (htmlspecialchars ($_GET['key']));
    $categID = (!empty ($categID) && preg_match ('`^[\d]+$`', $categID) ? intval ($categID) : 0);
    $categID = ($categID > 0 ? $categID : 0);

    $submit = $_REQUEST['submit'];

    //Determin action
    $action = (!empty ($_REQUEST['action']) ? trim (htmlspecialchars ($_REQUEST['action'])) : 'categtree');

    $error = 0;

    switch ($action)
    {
        case 'categtree' :
        {
            //global $db, $tables;
            //static $categs = array ("0" => array("val" => "[Top]", "closed" => 1) );
            $aux    = 0 ;
            $categs = '[ ';
            $level  = 0 ;   $level++;
            $rs = $db->CacheExecute("SELECT `ID`, `TITLE`, `CLOSED_TO_LINKS` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($categID) . " AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' ) ORDER BY `TITLE`");

            while (!$rs->EOF) {
                $categs .= '{ "title": "' . $rs->Fields('TITLE') . '", "key": "' . $rs->Fields('ID') . '"';
                $rcount = $db->CacheExecute("SELECT COUNT(*) as C FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = " . $db->qstr($rs->Fields('ID')) . " AND `STATUS` = '2' AND `SYMBOLIC` <> '1' AND (`URL` IS NULL OR `URL`='' )");
                
                if ($rcount->Fields('C') > 0) {
                    $categs .= ', "isLazy": true },';
                }
                else {
                    $categs .= '},';
                }

                $rs->MoveNext();
            }
            $categs = substr($categs,0,strlen($categs)-1).' ]';
            echo $categs;
        }
        break;
    }
?>
