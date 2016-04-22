<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 function get_execution_time()
{
    static $microtime_start = null;
    static $prev_time = null;
    if($microtime_start === null)
    {
        $microtime_start = microtime(true);
        $prev_time = $microtime_start;

        $executionTime = '0.0';
        $sinceLastMeasureTime = '0.0';
    } else {
        $executionTime = microtime(true) - $microtime_start;
        $sinceLastMeasureTime = microtime(true) - $prev_time;

        $prev_time = microtime(true);
    }
    return '<small><b>Exec. time:</b>'.$executionTime.'<br /><b>Since last:</b>'.$sinceLastMeasureTime.'</small><br />';
}