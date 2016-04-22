<?php
function smarty_function_video($params, &$smarty)
{ 
	$vid= trim(isset($params['vid']) ? $params['vid']:'');
  $provider= trim(isset($params['provider']) ? $params['provider']:'');
  if ($vid=="") return; 
  if ($provider=="") return; 
  
	// YouTube
	if ($provider=="youtube") {
	echo '<object type="application/x-shockwave-flash" style="width:319px; height:263px" data="http://www.youtube.com/v/'.$vid.'">
        <param name="movie" value="http://www.youtube.com/v/'.$vid.'" /></object>';
	}
	
	// AOL
	elseif ($provider == "aol") {
	echo '<object width="319" height="263"><param name="wmode" value="opaque" /><param name="movie" value="http://uncutvideo.aol.com/v7.0017/en-US/uc_videoplayer.swf" /><param name="FlashVars" value="aID=1'.$vid.'&site=http://uncutvideo.aol.com/"/><embed src="http://uncutvideo.aol.com/v7.0017/en-US/uc_videoplayer.swf" wmode="opaque" FlashVars="aID=1'.$vid.'&site=http://uncutvideo.aol.com/" width="319" height="263" type="application/x-shockwave-flash"></embed></object>';
	}
	
	// google
	elseif ($provider == "google") {
	$vid = str_replace("!", "", $params['vid']);
	echo '<embed style="width:319px; height:263px;" id="VideoPlayback" type="application/x-shockwave-flash" 
	      src="http://video.google.com/googleplayer.swf?docId='.$vid.'&hl=en" flashvars=""> </embed><br>';
	}
	// ClipShack
	elseif ($provider == "clipshack") {
	echo '<embed src="http://clipshack.com/player.swf?key='.$vid.'" width="430" height="370" wmode="transparent"></embed>';
	}
	
	
	
	

	else {
	echo $provider." - Video provider is not reconized";
	}
}
?>

