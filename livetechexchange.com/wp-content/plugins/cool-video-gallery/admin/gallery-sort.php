<?php 
/**
 * Section to sort videos in a gallery
 * @author Praveen Rajan
 */
?>
<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
	die('You are not allowed to call this page directly.'); 

CvgCore::upgrade_plugin();

//Loads WP default scripts
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-mouse');
wp_enqueue_script('jquery-ui-sortable');

wp_enqueue_style('jquery.ui.all', trailingslashit(WP_PLUGIN_URL . '/' . dirname(dirname(plugin_basename(__FILE__)))) . 'css/jquery.ui.all.css', 'jquery');
?>

<script type="text/javascript">
jQuery(function() {
	jQuery( "#sortable" ).sortable({
		placeholder: "ui-state-highlight",
		cursor:  "crosshair",
		opacity: 0.6,
		update: function(event, ui) { 
   			var result = jQuery('#sortable').sortable('toArray');
   			jQuery('#sortOrder').val('');
   			jQuery('#sortOrder').val(result); 
			}
	});
	jQuery( "#sortable" ).disableSelection();
		  
});
</script>	
<?php

if (isset ($_POST['updateSortOrder']))  {
	
	// wp_nonce_field('cvg_gallery_sort_nonce','cvg_gallery_sort_nonce_csrf');
	if ( check_admin_referer( 'cvg_gallery_sort_nonce', 'cvg_gallery_sort_nonce_csrf' ) ) {
		global $wpdb;
	    $sub_name_videos = 'cvg_videos';
	    $table_videos = $wpdb->prefix . $sub_name_videos;
		        
		$sortArray = explode(',', $_POST['sortOrder']);
	
		if (is_array($sortArray)){ 
			$sortindex = 1;
			foreach($sortArray as $pid) {		
				$wpdb->query("UPDATE $table_videos SET sortorder = '$sortindex' WHERE pid = $pid");
				$sortindex++;
			}
			CvgCore::xml_playlist($_GET['gid']);
			CvgCore::show_video_message(__('Sort order updated successfully!'));
		} 
	}
}
$gid = $_GET['gid'];
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'sortorder';

$cool_video_gallery = new CoolVideoGallery();
//Section if no gallery is selected.
if(!isset($gid)) { 
	?>
	<div class="wrap">
		<div class="icon32" id="icon-video"><br></div>
		<h2>Sort Gallery Videos</h2>
		<div class="clear"></div>
		<div class="versions">
	    	<p>
				Choose your gallery at <a class="button rbutton" href="<?php echo admin_url('admin.php?page=cvg-gallery-manage');?>"><?php _e('Manage Gallery') ?></a>
			</p>
			<br class="clear" />
		</div> 
		<?php 	CvgCore::show_video_error( __('Please select a gallery to sort videos') ); ?>
	</div> 
<?php 	
}else {
	
	$options = get_option('cvg_settings');
				
	$gallery = videoDB::find_gallery($gid);
	$title = __('Gallery to sort: '. $gallery->name);
	
	if (!$gallery)  
		CvgCore::show_video_error(__('Gallery not found.', 'nggallery'));
	
	if ($gallery) { 
			
			$videolist = videoDB::get_gallery($gid, true, $orderBy, 'asc');
			$act_author_user = get_userdata( (int) $gallery->author );
			$base_url = admin_url('admin.php?page=cvg-gallery-manage&gid=' . $_GET['gid'] . '&order=') ;
			?>
						
			<div class="wrap">
				<div class="icon32" id="icon-video"><br></div>
				<h2><?php echo esc_html( $title ); ?></h2>
				<div class="clear" style="min-height:10px;"></div>
				
				<form id="updatevideos" method="POST" action="<?php echo $base_url; ?>" accept-charset="utf-8">
				
					<?php wp_nonce_field('cvg_gallery_sort_nonce','cvg_gallery_sort_nonce_csrf'); ?>
					<div class="tablenav">
						<div class="alignleft actions">
							<a class="button" href="<?php echo admin_url('admin.php?page=cvg-gallery-manage&gid=' . $_GET['gid']); ?>"><?php _e('Back to Gallery'); ?></a>
							<input type="submit" name="updateSortOrder" class="button-primary action"  value="<?php _e('Update Sort Order');?>" />
						</div>
					</div>	
					
					<ul class="subsubsub">
						<li><?php _e('Sort By') ?> :</li>
						<li><a href="<?php echo $base_url . 'pid'; ?>" <?php if ($orderBy == 'pid') echo 'class="current"'; ?>><?php _e('Video ID') ?></a> |</li>
						<li><a href="<?php echo $base_url . 'filename'; ?>"  <?php if ($orderBy == 'filename') echo 'class="current"'; ?>><?php _e('Video Name') ?></a> |</li>
						<li><a href="<?php echo $base_url . 'videodate'; ?>"  <?php if ($orderBy == 'videodate') echo 'class="current"'; ?>><?php _e('Video Date') ?></a></li>
					</ul>
					<div class="clear"></div>
					
					<?php
						if($videolist) {
							
							$options = get_option('cvg_settings');
							$thumb_width = $options['cvg_preview_width'];
							$thumb_height = $options['cvg_preview_height'];
							?>
							<style>
								.ui-state-highlight { width: <?php echo ($thumb_width + 10)?>px; height: <?php echo ($thumb_height + 30) ?>px; }
							</style>
							<?php 
							echo '<ul id="sortable">';
							$pid_list = '';
							foreach($videolist as $video) {
								$pid = $video->pid;
								
								$video_name = $video->filename;
								
								if($video->video_type == $cool_video_gallery->video_type_upload) {
										
									$video_thumb_filename = $video->thumb_filename;
									$video_thumb_url = site_url() . '/' .  $video->path . '/thumbs/' . $video_thumb_filename;
									
									if(!file_exists(ABSPATH . $video->path . '/thumbs/' .$video->thumb_filename))
										$video_thumb_url  = WP_CONTENT_URL .  '/plugins/' . dirname(dirname( plugin_basename(__FILE__))) . '/images/default_video.png';
								}else if($video->video_type == $cool_video_gallery->video_type_youtube){
										
									$video_thumb_url =  $video->thumb_filename;
								}else if($video->video_type == $cool_video_gallery->video_type_media){
									
									$video_thumb_filename = $video->thumb_filename;
									$video_thumb_url = site_url() . '/' .  $video->path . '/thumbs/' . $video_thumb_filename;
									
									if(!file_exists(ABSPATH . $video->path . '/thumbs/' .$video->thumb_filename))
										$video_thumb_url  = WP_CONTENT_URL .  '/plugins/' . dirname(dirname( plugin_basename(__FILE__))) . '/images/default_video.png';
								}else {
									
									$video_thumb_filename = $video->thumb_filename;
									$video_thumb_url = site_url() . '/' .  $video->path . '/thumbs/' . $video_thumb_filename;
									
									if(!file_exists(ABSPATH . $video->path . '/thumbs/' .$video->thumb_filename))
										$video_thumb_url  = WP_CONTENT_URL .  '/plugins/' . dirname(dirname( plugin_basename(__FILE__))) . '/images/default_video.png';
								}  
								
								$output =  '<div style="float:left;border:1px SOLID #CCCCCC;padding:5px;"><img src="' ;
								$output .= $video_thumb_url; 
								$output .=  '" style="width:' . $thumb_width . 'px;height:' . $thumb_height . 'px;" alt="preview"/></div>';
								$video_title = (strlen($video->video_title) > 15) ? substr($video->video_title, 0, 15) . '...' : $video->video_title;
								$output .= '<div class="clear"></div><div style="text-align:center;width:'. $thumb_width .'px;">'. $video_title  . '</div>';
								echo '<li class="ui-state-default" id="' . $pid . '">';
								echo $output;
								echo '</li>';
								$pid_list .= $pid . ',';
							}
							echo '</ul>';
						} else {
						}
						$pid_list = substr($pid_list, 0, (strlen($pid_list) - 1));
					?>	
			
				<input type="hidden" value="<?php echo $pid_list;?>" name="sortOrder" id="sortOrder" />
			</form>
		</div>
		
	<?php } ?>	
<?php } ?>