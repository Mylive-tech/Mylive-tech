<?php 
/**
 * Section to generate video sitemap
 * @author Praveen Rajan
 */

 if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
	die('You are not allowed to call this page directly.'); 

$title = __('CVG Google XML Sitemap Generator');

CvgCore::upgrade_plugin();

?>
<style type="text/css">
#dashboard-widgets a {
	text-decoration:none;
}
</style>
<div class="wrap">
	<div class="icon32" id="icon-video"><br></div>
	<h2><?php echo esc_html( $title ); ?></h2>
	<?php 

		if(isset($_POST['generatexml'])) {
			
			// wp_nonce_field('cvg_video_sitemap_nonce','cvg_video_sitemap_nonce_csrf');
			if ( check_admin_referer( 'cvg_video_sitemap_nonce', 'cvg_video_sitemap_nonce_csrf' ) ) {
				CvgCore::xml_sitemap();
			}
		}
	?>
	<div id="dashboard-widgets-wrap">
	<form method="post" action="<?php echo admin_url('admin.php?page=cvg-video-sitemap'); ?>">
		<div id="dashboard-widgets" class="metabox-holder">
			<div style="width: 100%;" class="postbox-container">
				<div class="meta-box-sortables ui-sortable" id="left-sortables"  style="min-height:0px;">
					
					<div class="postbox" id="server_settings" >	
						<div class="inside" style="margin:10px;">
							Generate your Google XML Video Sitemap here <i style="font-size:10px;"><b>(FFMPEG Library support required)</b></i>. 
						</div>
						<?php wp_nonce_field('cvg_video_sitemap_nonce','cvg_video_sitemap_nonce_csrf'); ?>
						<div class="submit" style="padding-left:15px;">
							<input type="submit" class="button-primary action" name="generatexml" value="<?php _e("Generate Video Sitemap"); ?>" />
						</div>
					</div>
				</div>	
			</div>
		</div>
	</form>	
	</div>
</div>