<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty transform sizes in human readable format (e.g. 1Kb, 234Mb, 5Gb)
 *
 * Type:     modifier<br>
 * Name:     replace<br>
 * Purpose:  transform sizes in human readable format
 * @author   Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * @param integer
 * @return string
 */
function smarty_modifier_nicesize($size)
{
   //Define storage units
   $units = array ('B', 'kB', 'MB', 'GB', 'TB', 'PB');

   $pos = 0;

   while ($size >= 1024)
   {
      $size /= 1024;
      $pos++;
   }

   if ($size == 0)
   {
      //it's empty
      return '-';
   }
   else
   {
      //Round up
      return round ($size, 2) . ' ' . $units[$pos];
   }
}
?>