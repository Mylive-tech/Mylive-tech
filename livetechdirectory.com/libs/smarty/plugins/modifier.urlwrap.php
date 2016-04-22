<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty urlwrap modifier plugin
 *
 * Type:     modifier
 * Name:     urlwrap
 * Purpose:  Break a long URL in two shorter parts and remove "http", "www" and other unnecessary things
 *           http://www.domain.net/file.php?foo=bar&got=milk&longurl=1&id=xyz
 *             ==>
 *           domain.net...&id=xyz
 *
 * @author Constantin Bejenaru <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * @param string  The URL to be processed
 * @param integer Length of the first part
 * @param integer Length of the last part
 * @param string  The character(s) that break the URL
 * @param boolean If the URL should be cleaned of "http://", "ftp://", "www.", etc.
 * @return string
 */
function smarty_modifier_urlwrap($string, $firstPart=15, $lastPart=15, $break="... ", $clean=true)
{
   //Make sure we get correct params and do a basic clean
   $tmpString = trim ($string);
   $lastPart  = intval ($lastPart);

   //Remove some things
   if ($clean)
   {
      $pattern   = array ('`^http[s]?://`i', '`^ftp://`i', '`^mailto:`i', /*'`^www.`i',*/ '`[/]?$`');
      $replace   = array (''               , ''          , ''           , /*''        ,*/ ''       );
      $tmpString = preg_replace ($pattern, $replace, $tmpString);
   }

   if (strlen ($tmpString) > $firstPart + $lastPart + strlen ($break))
   {
      //Break URL into parts
      $tmpString = (substr ($tmpString, 0, $firstPart) . $break . substr ($tmpString, -$lastPart));
   }

   //Return short URL
   return $tmpString;
}
?>