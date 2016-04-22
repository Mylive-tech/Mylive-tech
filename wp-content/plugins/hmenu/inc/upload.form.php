<!-- START | STYLES -->
<style type="text/css">
	.hero_form_row_full{ font-family:Arial, Helvetica, sans-serif; color:#999; font-size:12px; }
	body{ margin:0; padding:0; }
	input[type=file]{
		display:none;
	}
	/* color buttons */
	.green_button{ background-color:#A7CF7F; color:#FFF; }
	.green_button:hover{ background-color:#87C04E; }
	.hero_button_auto {
		padding: 7px 10px;
		text-align: center;
		cursor: pointer;
		display: table;
		float: left;
		margin: 0 10px 0 0;
		text-transform: capitalize;
	}
	.rounded_3 {
		border-radius: 3px;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
	}
	.size_14 {
		font-size: 14px;
	}
</style>
<?php 
#SECURITY CHECK
	require_once('frame_sec.check.php');
	if(isset($secure_tag) && $secure_tag){ //secure (display content)
	
	#IF FILE DATA EXISTS
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		
		#DIRECTORY NAME
		$directory_name = '../_tmp_fonts/';
		
		#VARS
		$file_name = $_FILES['hero_font_file']['name'];
		
		#FILE TYPES
		$file_mimes = array(
			'application/zip',
			'application/x-zip',
			'application/x-zip-compressed',
			'application/octet-stream',
			'application/x-compress',
			'application/x-compressed',
			'multipart/x-zip',			 			
			'application/rar',
			'application/x-rar',
			'application/x-rar-compressed'
		);	
		
		#CHECK TO SEE IF FILE EXISTS
		if(in_array($_FILES['hero_font_file']['type'], $file_mimes)){
			
			#CREATE THE TEMP DIRECTORY
			if(!is_dir($directory_name)){
				mkdir($directory_name);
			}			
			
			#MOVE FILE TO TEMP FOLDER
			$file_name = sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),mt_rand(0, 0x0fff) | 0x4000,mt_rand(0, 0x3fff) | 0x8000,mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
   			$file = $directory_name . $file_name . '.zip';
    		move_uploaded_file($_FILES['hero_font_file']['tmp_name'], $file);			
			
			echo '
				<script type="text/javascript">
					window.parent.process_font_pack(\'process_complete\');
				</script>
			';
			
		}else{
			echo '
				<script type="text/javascript">
					window.parent.error_font_process();
				</script>
			';
		}
	}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">
	jQuery(function(){
		jQuery('.file-upload-btn a').off().on('click', function(){
			jQuery('#hero_font_file').trigger('click').off().on('change', function(){
				jQuery('#hero_font_file_form').trigger('submit');
			});
		});
	});
</script>
<form name="hero_font_file_form" id="hero_font_file_form" enctype="multipart/form-data" method="post">
    <div class="hero_form_row_full">
        <label for=""></label>
        <input name="hero_font_file" id="hero_font_file"  type="file" value="" />
        <div class="file-upload-btn"><a class="hero_button_auto green_button rounded_3 size_14">Select font pack</a></div>
    </div>       
</form>
<?php 
	}
?>