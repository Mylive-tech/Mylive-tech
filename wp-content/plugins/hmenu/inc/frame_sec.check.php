<?php

	#FRAME SECURITY CHECK
	if(isset($_GET['st'])){//check for security tag
		$file = '../frame_sec/'. $_GET['st'];
		if(is_file($file) && intval(time() - filemtime($file)) < 1200){ //check that tag exists and is less than 60 seconds old
			//valid
			$secure_tag = true;
		}else{
			echo '
				<script type="text/javascript">
					window.parent.show_security_tag_timeout_error();
				</script>
			';
		}
	}