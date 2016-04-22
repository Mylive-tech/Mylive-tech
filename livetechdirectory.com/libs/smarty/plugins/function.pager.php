<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     pager
 * Purpose:  create a paging output to be able to browse long lists 
 * Version:  1.0
 * Date:     September 29, 2002
 * Install:  Drop into the plugin directory
 * Author:   Peter Dudas <duda at bigfish dot hu>
 * -------------------------------------------------------------
 *
 * example:
 * <{pager rowcount=$LISTDATA.rowcount limit=$LISTDATA.limit txt_first=$L_MORE class_num="fl" class_numon="fl" class_text="fl"}>
 *
 *	CHANGES:		2003.03.14:	positionable prev/next string. can use image instead of text
 *	CHANGES:		2003.03.21:	Bugfix
 *
 *
 */
function smarty_function_pager($params, &$smarty)
{
	/* displays paging links to be able to browse in bit set of records
	@param	mixes	$rowcount		- total number of items to page in between (if array=>numeer of lines)
	@param	int		$limit			- number of items on a page
	@param	string	$posvar			- name of the php variable that contains the position data ($_REQUEST)
	@param	string	$forwardvars		- comma separated list of php variablenames to forward in the links (from $_REQUEST[] !!!)
	@param	string	$txt_first		- on the first page don't print out all pages, just a this text, if set empty prints all page numbers
	@param	string	$img_first		- on the first page don't print out all pages, just a this text, if set empty prints all page numbers
	@param	boolean	$no_first		- print out all the pages, do not start with txt_firts, equals to txt_first set empty
	@param	string	$txt_prev		- script to go to the prev page
	@param	string	$img_prev		- button image to the prev page
	@param	string	$txt_next		- script to go to the next page
	@param	string	$img_next		- button image to go to the next page
	@param	string	$txt_pos		- text position = 'top', 'bottom', 'middle/side'
	@param	string	$class_num		- class for the page numbers <A> tag!
	@param	string	$class_numon	- class for the aktive page!
	@param	string	$class_text		- class for the texts
	@param	string	$separator		- string to put between the 1 2 3 pages (1 separator 2 separator);

	*/
	
	// START INIT
	$posvar = 'pos';
	

	$separator = ' &laquo;&laquo; ';
	$class_text = 'nav';
	$class_num = 'small';
	$class_numon = 'big';

	$txt_pos = 'bottom';					


	
	$txt_prev = 'Previous';					// previous
	$txt_next = 'Next';				// next
	$txt_first = 'More';		// archive, more articles
/*
	$txt_prev = 'Elõzõ';					// previous
	$txt_next = 'Következõ';				// next
	$txt_first = 'További cikkek';		// archive, more articles
*/

	foreach($params as $key=>$value)	{
		$tmps[strtolower($key)] = $value;
		$tmp = strtolower($key);
		if (!(${$tmp} = $value))	{
			${$tmp} = '';
		}
	}	

	// START data check
	$minVars = array('limit');
	foreach($minVars as $tmp)  {
		if (empty($params[$tmp]))	{
			$smarty->trigger_error('plugin "pager": missing or empty parameter: "'.$tmp.'"');
		}
	}

	// END data check
	if ($txt_pos == 'middle')	{
		$txt_pos = 'side';
	}
	if (!in_array($txt_pos, array('side', 'top', 'bottom'))) {
			$smarty->trigger_error('plugin "pager": bad value for : "txt_pos"');
	}

	// if there is no need for paging at all
	if (is_array($rowcount))	{
		$rowcount = count($rowcount);
	}
	if ($rowcount <= $limit)	{
		return '';
	}
	if (!empty($no_first))	{
		unset($txt_first);
	}

	if (!is_array($forwardvars))	{
		$forwardvars = preg_split('/[,;\s]/', $forwardvars, PREG_SPLIT_NO_EMPTY);
	}

	$pos = $_REQUEST[$posvar];
	$pages = array();
	// END INIT

	// remove these vars from the request_uri - only for beauty
	$removeVars = array($posvar, '_rc');
	$url = $_SERVER['REQUEST_URI'];
	foreach($removeVars as $tmp)	{
		$url = preg_replace('/&'.$tmp.'\=[^&]*/', '', $url);
	}

	
	// if there is no position (or 0) prepare the link for the second page
	if ((empty($pos) OR ($pos < 1)) AND ($rowcount > $limit))	{
		if (!empty($firstpos))	{
			$url .= '&'.$posvar.'='.$firstpos;
		} elseif ($pos == -1)	{
			$url .= '&'.$posvar.'=1';
		} else	{
			$url .= '&'.$posvar.'='.$limit;
		}
		foreach ((array)$forwardvars as $tmp)	{
			if (!empty($tmp) AND (!empty($_REQUEST[$tmp])))
				$url .= '&'.$tmp.'='.$_REQUEST[$tmp];
		}
		//$pages[$txt_first] = $url;
		$short['first'] = $url;
	}

	// START create data to print
	if ($rowcount > $limit)  { 
		if ($rowcount < ($limit * 30))	{
			for ($i=1; $i < $rowcount+1; $i+=$limit)	{
				if (($pos+1 >=$i) and ($pos+1 < ($i+$limit)) )		{
					$short['now'] = $i;
				}
				$pages[$i] = $url.'&'.$posvar.'='.($i-1);
			}
		} else { // if there a lot of records to page in beetween
			// far before the actual position to bi steps ($limit*10)
			for ($i=1;$i < ($pos-16*$limit); $i+=10*$limit)	{
				$pages[$i] = $url.'&'.$posvar.'='.($i-1);
			}
			// around the actual position do small steps ($limit)
			for ($tmp=1;($i < $pos+16*$limit) AND ($i < $rowcount+1); $i += $limit)	{
				if (($pos+1 >= $i) and ($pos+1 < ($i+$limit)) )	{
					$short['now'] = $i;
				}
				$pages[$i] = $url.'&'.$posvar.'='.($i-1);
			}
			// over $pos do big steps ($limit*10)
			for ($tmp=1;$i < $rowcount+1; $i += 10*$limit)	{
				$pages[$i] =  $url.'&'.$posvar.'='.($i-1);
			}
		} 
		// elozo - kovetkezo leptetes
		if ($pos >= $limit)	{
			$short['prev'] = $url.'&'.$posvar.'='.($pos - $limit);
		}

		if ( ($pos) < ($rowcount-$limit))	{
			$short['next'] = $url.'&'.$posvar.'='.($pos + $limit);
		} 
	}
	// END preparing the arrays to print


	// START DISPLAY ---------------------------------------------------------------------------
	// all neccesary data are in $pages, and in $short
	if (($pos == 0)	AND ((!empty($txt_first)) OR !empty($img_first))){
		print '<p align="center">';
		print '<a class="'.$class_text.'" href="'.$short['first'].'">';
		
		if (!empty($img_first))	{
			if (preg_match('/\</', $img_first)) {
				// image tag
				print $img_first;
			} else {
				// image url
				print '<img src="'.$img_first.'" border="0" />';
			}
		} else	{
			print $txt_first;
		}
		print '</a></p>'."\n";
	} else	{
		// -----------------------------------------------------------------------
		// START prepare the prev and next string/image, make it a link ....
		if ($pos >= $limit)	{
			$cache['prev'] = '<a class="'.$class_text.'" href="'.$short['prev'].'">';
			if (!empty($img_prev))	{
				if (preg_match('/\</', $img_prev)) {
					// image tag
					$cache['prev'] .= $img_prev;
				} else {
					// image url
					$cache['prev'] .= '<img src="'.$img_prev.'" border="0" />';
				}
			} else	{
				$cache['prev'] .= $txt_prev;
			}
			$cache['prev'] .= '</a> &nbsp; &nbsp; &nbsp; ';
		} else	{
			$cache['prev'] = '';
		}
			//  next
		if ($pos < ($rowcount-$limit))	{
			$cache['next'] = '&nbsp;&nbsp;&nbsp;<a class="'.$class_text.'" href="'.$short['next'].'">';
			if (!empty($img_next))	{
				if (preg_match('/\</', $img_next)) {
					// image tag
					$cache['next'] .= $img_next;
				} else {
					// image url
					$cache['next'] .= '<img src="'.$img_next.'" border="0" />';
				}
			} else	{
				$cache['next'] .= $txt_next;
			}
			$cache['next'] .= '</a>';
		} else {
			$cache['next'] = '';
		}
		// END prepare the prev and next string/image, make it a link ....
		// -----------------------------------------------------------------------
		// START PRININT
		if ($txt_pos == 'top')	{
			print '<p align="center">'.$cache['prev'].$cache['next'].'</p>'."\n";
		}
		print '<p align="center">';
		if ($txt_pos == 'side')	{
			print $cache['prev'];;
		}
		foreach($pages as $num=>$url)	{
				if ($num > $limit)	{
					print ' '.$separator.' ';
				}
				print '<a class="'.(($num == $short['now']) ? $class_numon : $class_num).'" href="'.$url.'">'.$num.'</a>';
		}
		if ($txt_pos == 'side')	{
			print $cache['next'];;
		}
		print '</p>'."\n";
		// END NUMBERS
		// START PREVIOUS, NEXT paging
		if ($txt_pos == 'bottom')	{
			print '<p align="center">'.$cache['prev'].$cache['next'].'</p>'."\n";
		}
		// END PREVIOUS, NEXT paging
	}
	// END DISPLAY
	return '';
}
