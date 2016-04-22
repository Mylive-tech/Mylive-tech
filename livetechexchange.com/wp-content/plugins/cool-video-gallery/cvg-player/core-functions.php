<?php
/**
 * Class for options of video player
 * @author Praveen Rajan
 *
 */

 if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
	die('You are not allowed to call this page directly.'); 
 
class CVGPlayer extends CoolVideoGallery {
	
	/**
	 * Function to get list of all player skins from folder
	 * @param $path - folder path of skins
	 * @param $match - extention of file
	 * @param $prematch 
	 * @param $revsort - order of sort
	 * @return array of skins
	 * @author Praveen Rajan
	 */
	function get_dir_skin($path, $match = "", $prematch = "", $revsort = true){
		
		$handle = opendir($path);
		$list = array();

		while (false !== ($file = readdir($handle))){
			if ($match != ""){
				if (substr($file, strlen($file) - strlen($match)) == $match){
					if ($prematch != ""){
						if (substr($file, 0, strlen($prematch)) == $prematch){
							$list[count($list)] = substr($file, strlen($prematch), strlen($file) - (strlen($match) + strlen($prematch)));
						}
					}else{
						$list[count($list)] = substr($file, 0, strlen($file) - strlen($match));
					}
				}
			}else{
				if ($prematch != ""){
					if (substr($file, 0, strlen($prematch)) == $prematch){
						$list[count($list)] = substr($file, strlen($prematch), strlen($file) - strlen($prematch));
					}
				}else{
					$list[count($list)] = $file;
				}
			}
		}
		if ($revsort){
			rsort($list);
		}else{
			sort($list);
		}
		return $list;
	}
}
?>