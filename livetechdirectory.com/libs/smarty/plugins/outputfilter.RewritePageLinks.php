<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty rewrite paging links outputfilter plugin for PHP Link Directory
 *
 * File:     outputfilter.RewritePageLinks.php
 * Type:     outputfilter
 * Name:     RewritePageLinks
 * Date:     Apr 23, 2006
 * Purpose:  Rewrite paging links in PHP Link Directory
 *           from "?p=x" to "page-2.html"
 * Install:  Drop into the plugin directory, call
 *           <code>$smarty->load_filter('output','RewritePageLinks');</code>
 *           from application.
 * @author   Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 * @author   DCB
 * @version  0.2
 * @param string
 * @param Smarty
 */

function smarty_outputfilter_RewritePageLinks($source, &$smarty)
{
	if (!isset($_REQUEST['search'])) { //no modrw for search
//		$matchesSort = array();
		if (preg_match_all('!"(\S*)&amp;p=(\d+)"!', $source, $matchesSort) !== 0) {
			// pagination + sort
			$urls      = $matchesSort[1];
			//$sort      = $matchesSort[2];
			$page_nums = $matchesSort[2];

			//Populate URLs to be replaced with
			for($i = 0; $i < count ($urls); $i++) {
				$temp_url      = trim($urls[$i],'"'); //remove quotes and slashes
				$page_nums[$i] = '"'.$temp_url.'page-'.ceil($page_nums[$i] / PAGER_LPP).'.html' . '?s=' . $sort[$i] . '"'; //put the quotes again
			}
			$source = str_replace($matchesSort[0], $page_nums, $source);
		} else {
			// just pagination, no sort 
			$matches   = array ();
			preg_match_all ('!"(\S*)\?p=(\d+)"!', $source, $matches); //Take care for double quotes
			$urls      = $matches[1];
			$page_nums = $matches[2];
			//Populate URLs to be replaced with
			for($i = 0; $i < count ($urls); $i++) {
				$temp_url      = trim ($urls[$i],'"'); //remove quotes and slashes
				$page_nums[$i] = '"'.$temp_url.'page-'.ceil ($page_nums[$i] / PAGER_LPP).'.html"'; //put the quotes again
			}
			$source = str_replace($matches[0], $page_nums, $source);
		}
	}
	return $source;
}
?>