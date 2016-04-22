<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty remove trash chars modifier plugin
 *
 * Type:     modifier<br>
 * Name:     removeTrash<br>
 * Purpose:  remove all unneeded chars
 * @author   Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * @param string
 * @return string
 */
function smarty_modifier_removeTrash($string)
{
   $string = strip_tags ($string); /* No HTML and/or PHP tags */
   $string = str_replace ("\n"    , ""      , $string);  /* *UNIX           */
   $string = str_replace ("\r\n"  , ""      , $string);  /* Windows         */
   $string = str_replace ("\r"    , ""      , $string);  /* Mac             */
   $string = str_replace ("\t"    , ""      , $string);  /* TAB             */
   $string = str_replace ("\0"    , ""      , $string);  /* NULL BYTE       */
   $string = str_replace ("\x0B"  , ""      , $string);  /* Vertical TAB    */
   $string = preg_replace ('/\s+/', ' '     , $string);  /* Multiple spaces */

   return $string;
}

?>
