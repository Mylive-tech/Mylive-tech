<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty compress output for faster loading times
 *
 * File:     outputfilter.CompressOutput.php<br>
 * Type:     outputfilter<br>
 * Name:     CompressOutput<br>
 * Date:     Apr 23, 2006<br>
 * Purpose:  Return a compressed version of the input data
 *           compatible with the output of the gzip program.
 *           This will lead to faster loading times but also
 *           to lower traffic.
 * Install:  Drop into the plugin directory, call
 *           <code>$smarty->load_filter('output','CompressOutput');</code>
 *           from application.
 * @author   Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * @author   Based on the gzip output plugin of Mr. Joscha Feth, joscha@feth.com
 * @version  0.1
 * @param string
 * @param Smarty
 */

function smarty_outputfilter_CompressOutput($source, &$smarty)
{
   //Array of user agents that do not understand compressed output
   $poorUserAgent   = array ();
   //Very strange, but the W3C validator cannot handle compressed output
   $poorUserAgent[] = 'W3C_Validator';

   $userAgentDisabled = 0;
   if (is_array ($poorUserAgent) && !empty ($poorUserAgent))
   {
      foreach ($poorUserAgent as $key => $userAgent)
      {
         if (stristr ($_SERVER['HTTP_USER_AGENT'], $userAgent) !== false)
            $userAgentDisabled = 1;
      }
   }

   //Check if gzip encoding is supported by application, server and client
	if (!headers_sent() && !$smarty->caching && !$smarty->debugging && extension_loaded('zlib') && strpos($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip') >= 0 && $userAgentDisabled == 0)
	{
      //Determine compression level
      $level = (COMPRESSION_LEVEL && COMPRESSION_LEVEL != '' && COMPRESSION_LEVEL != 'default' ? COMPRESSION_LEVEL : 6);
      $level = ($level < 0 ? 0 : $level);
      $level = ($level > 9 ? 9 : $level);
      $level = intval ($level);

      //Return a compressed version of the input data compatible with the output of the gzip program.
      $source = gzencode ($source, $level);

      //Send headers to report compressed output
      @header("Content-Encoding: gzip");
      @header("Vary: Accept-Encoding");
      @header("Content-Length: ".strlen ($source));
	}

	return $source;
}
?>