<?php 
/**
 * Section to display video player settings
 * @author Praveen Rajan
 */
?>
<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
	die('You are not allowed to call this page directly.'); 

?>
<script type="text/javascript">
	
	jQuery(document).ready(function(){
		jQuery("#skin_help_popup").fancybox({
				'titlePosition'		: 'inside',
				'transitionIn'		: 'none',
				'transitionOut'		: 'none'
			});

	});
</script>
<?php 

CvgCore::upgrade_plugin();

$title = __('Video Player Settings');
$plugin_path = WP_CONTENT_DIR.'/plugins/'.plugin_basename( dirname(dirname(__FILE__)) );
$plugin_url = WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(dirname(__FILE__)) );

require_once( $plugin_path . '/cvg-player/core-functions.php');

if(isset($_POST['update_CVGSettings'])){
	
	// wp_nonce_field('cvg_player_settings_nonce','cvg_player_settings_nonce_csrf');
	if ( check_admin_referer( 'cvg_player_settings_nonce', 'cvg_player_settings_nonce_csrf' ) ) {
		
		$options_player = $_POST['options_player'];
		update_option('cvg_player_settings', $options_player);
		echo '<div class="updated"><p><strong>' . __('Options saved.') . '</strong></p></div>';
	}
}

$options_player = get_option('cvg_player_settings');
?>
<div class="wrap">
	<div class="icon32" id="icon-video"><br></div>
	<h2><?php echo esc_html( $title ); ?></h2>
	
	<form method="post" action="<?php echo admin_url('admin.php?page=cvg-player-settings'); ?>">
		<div class="clear" style="min-height:10px;"></div>
		
		<div style="float:left;width:25%;">
			<h4>Width of video player:</h4>
		</div>
		<div style="float:left;padding-top:6px;">	
			<textarea name="options_player[cvgplayer_width]" COLS=10 ROWS=1><?php echo $options_player['cvgplayer_width']?></textarea>
		</div>
		
		<div class="clear" ></div>
		<div style="float:left;width:25%;">	
			<h4>Height of video player:</h4>
		</div>
		<div style="float:left;padding-top:6px;">
			<textarea name="options_player[cvgplayer_height]" COLS=10 ROWS=1><?php echo $options_player['cvgplayer_height']?></textarea>
		</div>
		<div class="clear"></div>
			
		<div style="float:left;width:25%;">	
			<h4>Choose skin for video player:<br/>
				<i style="font-size:10px !important;"><a id="skin_help_popup" href="#skin_help" title="<b>Steps to install new skin to CVG</b>">Learn More</a></i>
			</h4>	
		</div>
		<?php 

		$skins = CVGPlayer::get_dir_skin( dirname(dirname((__FILE__))) . "/cvg-player/skins/", "-skin", "", false);
		
		$option = '<option value="">No Skin</option>';
		foreach ($skins as $value){
			$option .= '<option value="' . $value . '" '; 
			if ($options_player['cvgplayer_skin'] ==  $value ){
				$option .=  'SELECTED >' . $value .'</option>';
			}else{
				$option .=  '>' . $value .'</option>';
			}
		}
		?>
		<div style="float:left;padding-top:15px;">		
			<select name="options_player[cvgplayer_skin]" style="width:120px;">
				<?php echo $option;?>				
			</select>
		</div>	
		<div class="clear"></div>

		<div style="float:left;width:25%;">	
			<h4>Default Volume:</h4>
		</div>			
		<div style="float:left;padding-top:6px;">		
			<textarea name="options_player[cvgplayer_volume]" COLS=10 ROWS=1><?php echo $options_player['cvgplayer_volume']?></textarea>
		</div>
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Video Playlist Width: <br/><i style="font-size:10px !important;">(applicable if playlist location is left/right)</i></h4>
		</div>			
		<div style="float:left;padding-top:6px;">		
			<textarea name="options_player[cvgplayer_playlist_width]" COLS=10 ROWS=1><?php echo $options_player['cvgplayer_playlist_width']?></textarea>
		</div>
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Video Playlist Height: <br/><i style="font-size:10px !important;">(applicable if playlist location is top/bottom)</i></h4>
		</div>			
		<div style="float:left;padding-top:6px;">		
			<textarea name="options_player[cvgplayer_playlist_height]" COLS=10 ROWS=1><?php echo $options_player['cvgplayer_playlist_height']?></textarea>
		</div>
		<div class="clear"></div>

		<div style="float:left;width:25%;">	
			<h4>Autoplay: <br/><i style="font-size:10px !important;">(also enables continuous playback in gallery)</i></h4>
		</div>	
		<div style="float:left;padding-top:6px;">
			<p>
				<label for="autoplay_true"><input type="radio" id="autoplay_true" name="options_player[cvgplayer_autoplay]" value="1" <?php if ($options_player['cvgplayer_autoplay']) { _e('checked="checked"'); }?>/> True</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="autoplay_false"><input type="radio" id="autoplay_false" name="options_player[cvgplayer_autoplay]" value="0" <?php if (!$options_player['cvgplayer_autoplay']) { _e('checked="checked"'); }?>/> False</label>
			</p>
		</div>	
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Mute Volume:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">
			<p>
				<label for="mute_true"><input type="radio" id="mute_true" name="options_player[cvgplayer_mute]" value="1" <?php if ($options_player['cvgplayer_mute']) { _e('checked="checked"'); }?>/> True</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="mute_false"><input type="radio" id="mute_false" name="options_player[cvgplayer_mute]" value="0" <?php if (!$options_player['cvgplayer_mute']) { _e('checked="checked"'); }?>/> False</label>
			</p>
		</div>	
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Auto close on completion: <br/><i style="font-size:10px !important;">(applicable for single video)</i></h4>
		</div>	
		<div style="float:left;padding-top:6px;">
			<p>
				<label for="auto_close_single_true"><input type="radio" id="auto_close_single_true" name="options_player[cvgplayer_auto_close_single]" value="1" <?php if ($options_player['cvgplayer_auto_close_single']) { _e('checked="checked"'); }?>/> True</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="auto_close_single_false"><input type="radio" id="auto_close_single_false" name="options_player[cvgplayer_auto_close_single]" value="0" <?php if (!$options_player['cvgplayer_auto_close_single']) { _e('checked="checked"'); }?>/> False</label>
			</p>
		</div>	
		<div class="clear"></div>
		
		
		<div style="float:left;width:25%;">	
			<h4>Enable share option:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">
			<p>
				<label for="share_option_true"><input type="radio" id="share_option_true" name="options_player[cvgplayer_share_option]" value="1" <?php if ($options_player['cvgplayer_share_option']) { _e('checked="checked"'); }?>/> True</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="share_option_false"><input type="radio" id="share_option_false" name="options_player[cvgplayer_share_option]" value="0" <?php if (!$options_player['cvgplayer_share_option']) { _e('checked="checked"'); }?>/> False</label>
			</p>
		</div>	
		<div class="clear"></div>
		
		
		<div style="float:left;width:25%;">	
			<h4>Control bar Location:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">	
			<p>
				<label for="controlbar_bottom"><input type="radio" id="controlbar_bottom" name="options_player[cvgplayer_controlbar]" value="bottom" <?php if ($options_player['cvgplayer_controlbar'] == "bottom") { _e('checked="checked"'); }?>/> Bottom</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="controlbar_top"><input type="radio" id="controlbar_top" name="options_player[cvgplayer_controlbar]" value="top" <?php if ($options_player['cvgplayer_controlbar'] == "top") { _e('checked="checked"'); }?> style="margin-right: 5px;"/>Top</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="controlbar_over"><input type="radio" id="controlbar_over" name="options_player[cvgplayer_controlbar]" value="over" <?php if ($options_player['cvgplayer_controlbar'] == "over") { _e('checked="checked"'); }?> style="margin-right: 5px;"/>Over</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="controlbar_none"><input type="radio" id="controlbar_none" name="options_player[cvgplayer_controlbar]" value="none" <?php if ($options_player['cvgplayer_controlbar'] == "none") { _e('checked="checked"'); }?> style="margin-right: 5px;"/>None</label>&nbsp;&nbsp;&nbsp;&nbsp;
			</p>
		</div>	
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Video Display:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">	
			<p>
				<label for="display_none"><input type="radio" id="display_none" name="options_player[cvgplayer_stretching]" value="none" <?php if ($options_player['cvgplayer_stretching'] == "none") { _e('checked="checked"'); }?>/> None</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="display_exactfit"><input type="radio" id="display_exactfit" name="options_player[cvgplayer_stretching]" value="exactfit" <?php if ($options_player['cvgplayer_stretching'] == "exactfit") { _e('checked="checked"'); }?>/> Exact fit</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="display_uniform"><input type="radio" id="display_uniform" name="options_player[cvgplayer_stretching]" value="uniform" <?php if ($options_player['cvgplayer_stretching'] == "uniform") { _e('checked="checked"'); }?>/> Uniform</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="display_fill"><input type="radio" id="display_fill" name="options_player[cvgplayer_stretching]" value="fill" <?php if ($options_player['cvgplayer_stretching'] == "fill") { _e('checked="checked"'); }?>/> Fill</label>&nbsp;&nbsp;&nbsp;&nbsp;
			</p>
		</div>	
		
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Playlist Location:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">	
			<p>
				<label for="playlist_top"><input type="radio" id="playlist_top" name="options_player[cvgplayer_playlist]" value="top" <?php if ($options_player['cvgplayer_playlist'] == "top") { _e('checked="checked"'); }?> style="margin-right: 5px;"/>Top</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="playlist_right"><input type="radio" id="playlist_right" name="options_player[cvgplayer_playlist]" value="right" <?php if ($options_player['cvgplayer_playlist'] == "right") { _e('checked="checked"'); }?> style="margin-right: 5px;"/>Right</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="playlist_bottom"><input type="radio" id="playlist_bottom" name="options_player[cvgplayer_playlist]" value="bottom" <?php if ($options_player['cvgplayer_playlist'] == "bottom") { _e('checked="checked"'); }?> style="margin-right: 5px;"/> Bottom</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="playlist_left"><input type="radio" id="playlist_left" name="options_player[cvgplayer_playlist]" value="left" <?php if ($options_player['cvgplayer_playlist'] == "left") { _e('checked="checked"'); }?> style="margin-right: 5px;"/>Left</label>&nbsp;&nbsp;&nbsp;&nbsp;
			</p>
		</div>	
		<div class="clear"></div>
		
		<div class="clear"></div>
		
		<?php wp_nonce_field('cvg_player_settings_nonce','cvg_player_settings_nonce_csrf'); ?>
		<div class="submit">
			<input class="button-primary" type="submit" name="update_CVGSettings" value="Save Player Settings" />
		</div>
	</form>
	
 </div>
 
<div style="display: none;">
		<div id="skin_help" style="width:650px;height:230px;overflow:auto;font-size:13px;">
			As part of plugin feature upgrades, only a few skins are made available. But its easy to upload skin of your choice and that too in a few steps.
			<br/>
			<ul>
				<ol>
					1. Download JW Player Skins from <a target="_blank" href="http://www.longtailvideo.com/addons/skins">http://www.longtailvideo.com/addons/skins</a>.
				</ol>
				<ol>
					2. Extract the zip file for skin to a location in your Desktop and append "-skin" to folder name at the end.
				</ol>
				<ol>
					3. Now locate your WorPress site installation folder in Webserver using an FTP client.
				</ol>
				<ol>
					4. Navigate to the following path <i><b>/wp-content/plugins/cool-video-gallery/cvg-player/skins/</b></i>
				</ol>
				<ol>
					5. Upload the extracted folder to this location in WebServer.
				</ol>
				<ol>
					6. In admin section of WordPress navigate to Video Player Settings Panel of Cool Video Gallery plugin. 
				</ol>
				<ol>
					7. The new JW Player skin will be listed in the drop down.
				</ol>
				<ol>
					8. Enjoy the skins from JW Player together with features of CVG.
				</ol>
			</ul>
			
		</div>
</div>