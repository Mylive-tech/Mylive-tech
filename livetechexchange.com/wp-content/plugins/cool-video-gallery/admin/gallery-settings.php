<?php 
/**
 * Section to display gallery settings.
 * @author Praveen Rajan
 */
?>
<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
	die('You are not allowed to call this page directly.'); 

$title = __('Video Gallery Settings'); 

CvgCore::upgrade_plugin();
?>
<div class="wrap">
	<div class="icon32" id="icon-video"><br></div>
	<h2><?php echo esc_html( $title ); ?></h2>
	<?php 
	//Section to save gallery settings
	if(isset($_POST['update_Settings'])) {
		
		// wp_nonce_field('cvg_gallery_settings_nonce','cvg_gallery_settings_nonce_csrf');
		if ( check_admin_referer( 'cvg_gallery_settings_nonce', 'cvg_gallery_settings_nonce_csrf' ) ) {
			
			$options['max_cvg_gallery'] = $_POST['max_cvg_gallery'];
			$options['max_vid_gallery'] = $_POST['max_vid_gallery'];
			$options['cvg_preview_height'] = $_POST['cvg_preview_height'];
			$options['cvg_preview_width'] = $_POST['cvg_preview_width'];
			$options['cvg_slideshow']= intval($_POST['cvg_slideshow']) * 1000;
			$options['cvg_description']= $_POST['cvg_description'];
			$options['cvg_ffmpegpath']= $_POST['cvg_ffmpegpath'];
			$options['cvg_navigation_controls'] = $_POST['cvg_navigation_controls'];
			$options['cvg_random_video'] = $_POST['cvg_random_video'];
	
			update_option('cvg_settings', $options);
			
			CvgCore::show_video_message(__('Gallery settings successfully updated.'));
		}
	}
	$options = get_option('cvg_settings');
	?>

	<form method="post" action="<?php echo admin_url('admin.php?page=cvg-gallery-settings'); ?>">
			
			<div style="float:left;width:35%;">
				<h4>Max no. of Galleries listed per page <i style="font-size:10px !important;">(in gallery details dashboard)</i>:</h4>
			</div>
			<div style="float:left;padding-top:6px;">	
				<textarea name="max_cvg_gallery" COLS=10 ROWS=1><?php echo $options['max_cvg_gallery']?></textarea>
			</div>
			
			<div class="clear" ></div>
			<div style="float:left;width:35%;">	
				<h4>Max no. of Videos listed per page <i style="font-size:10px !important;">(in gallery details dashboard)</i>:</h4>
			</div>
			<div style="float:left;width:35%;">
				<textarea name="max_vid_gallery" COLS=10 ROWS=1><?php echo $options['max_vid_gallery']?></textarea>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:35%;">	
				<h4>Width of preview image:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="cvg_preview_width" COLS=10 ROWS=1><?php echo $options['cvg_preview_width']?></textarea>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:35%;">	
				<h4>Height of preview image:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="cvg_preview_height" COLS=10 ROWS=1><?php echo $options['cvg_preview_height']?></textarea>
			</div>
			<div class="clear"></div>	

			<div style="float:left;width:35%;">	
				<h4>Slideshow Speed <i style="font-size:10px !important;">(in Seconds)</i>:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="cvg_slideshow" COLS=10 ROWS=1><?php echo intval($options['cvg_slideshow']) / 1000;?></textarea>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:35%;">	
				<h4>Enable Gallery/Video Description in display:</h4>
			</div>
			<div style="float:left;padding-top:16px;">
				<input type="radio" id="description_yes" value="1" name="cvg_description" <?php echo ($options['cvg_description'] == 1) ? 'checked' : '';?> /><label for="description_yes" style="margin-right: 20px;margin-left: 5px;">Yes</label>
				<input type="radio" id="description_no" value="0" name="cvg_description" <?php echo ($options['cvg_description'] == 0) ? 'checked' : '';?> /><label for="description_no" style="margin-right: 20px;margin-left: 5px;">No</label>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:35%;">	
				<h4>Autohide navigation controls on gallery:</h4>
			</div>
			<div style="float:left;padding-top:16px;">
				<input type="radio" id="navigation_controls_yes" value="1" name="cvg_navigation_controls" <?php echo (isset($options['cvg_navigation_controls']) && $options['cvg_navigation_controls'] == 1) ? 'checked' : '';?> /><label for="navigation_controls_yes" style="margin-right: 20px;margin-left: 5px;">Yes</label>
				<input type="radio" id="navigation_controls_no" value="0" name="cvg_navigation_controls" <?php echo (isset($options['cvg_navigation_controls']) && $options['cvg_navigation_controls'] == 0) ? 'checked' : '';?> /><label for="navigation_controls_no" style="margin-right: 20px;margin-left: 5px;">No</label>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:35%;">	
				<h4>Randomize videos in gallery:</h4>
			</div>
			<div style="float:left;padding-top:16px;">
				<input type="radio" id="random_cvg_yes" value="1" name="cvg_random_video" <?php echo (isset($options['cvg_random_video']) && $options['cvg_random_video'] == 1) ? 'checked' : '';?> /><label for="random_cvg_yes" style="margin-right: 20px;margin-left: 5px;">Yes</label>
				<input type="radio" id="random_cvg_no" value="0" name="cvg_random_video" <?php echo (isset($options['cvg_random_video']) && $options['cvg_random_video'] == 0) ? 'checked' : '';?> /><label for="random_cvg_no" style="margin-right: 20px;margin-left: 5px;">No</label>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:35%;">	
				<h4>FFMPEG library path:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="cvg_ffmpegpath" COLS=60 ROWS=1><?php echo $options['cvg_ffmpegpath']?></textarea>
				<br/>
				<i style="font-size:10px !important;">(HINT: For MAC OSX users: <code>/Applications/ffmpegX.app/Contents/Resources/ffmpeg</code>)</i>
			</div>
			<div class="clear"></div>	
			
			<?php wp_nonce_field('cvg_gallery_settings_nonce','cvg_gallery_settings_nonce_csrf'); ?>
			<div class="submit">
				<input class="button-primary" type="submit" name="update_Settings" value="Save Gallery Settings"  />
			</div>
	</form>		
</div>