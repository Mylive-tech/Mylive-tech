<?php 
/**
 * Section to display gallery overview
 * @author Praveen Rajan
 */
?>
<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
	die('You are not allowed to call this page directly.'); 

CvgCore::upgrade_plugin();

$title = __('Video Gallery Overview');
?>
<script type="text/javascript">
	jQuery(document).ready(function(){ 
		jQuery('#hndle_overview').click(function(){
			if(jQuery('#gallery_overview').attr('class') == 'postbox closed') 
				jQuery('#gallery_overview').removeClass('closed');
			else	
				jQuery('#gallery_overview').addClass('closed');
		});
		jQuery('#hndle_server').click(function(){
			if(jQuery('#server_settings').attr('class') == 'postbox closed') 
				jQuery('#server_settings').removeClass('closed');
			else	
				jQuery('#server_settings').addClass('closed');
		});
	
	});
</script>
<style type="text/css">
#dashboard-widgets a {
	text-decoration:none;
}
</style>
<div class="wrap">
	<div class="icon32" id="icon-video"><br></div>
	<h2><?php echo esc_html( $title ); ?></h2>
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div style="width: 100%;" class="postbox-container">
				<div class="meta-box-sortables ui-sortable" id="left-sortables"  style="min-height:0px;">
					<div class="postbox" >	
						<div class="inside" style="margin:10px;">
							Please mark your valuable ratings at <a href="http://wordpress.org/extend/plugins/cool-video-gallery/" target="_blank" >
							<?php
							for ($i=0; $i < 5; $i++) { 
								?>
								<img src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/star.png" />
								<?php
							}
							?>	
							</a>
						</div>
					</div>
					<div class="postbox" id="gallery_overview">	
						<div title="Click to toggle" class="handlediv"><br></div>
						<h3 id="hndle_overview" class="hndle"><span>Video Gallery Overview</span></h3>
						<div class="inside" style="margin:10px;">
							<?php CvgCore::gallery_overview(); ?>
						</div>
					</div>	
					<div class="postbox" id="server_settings" >	
						<div title="Click to toggle" class="handlediv"><br></div>
						<h3 id="hndle_server" class="hndle"><span>Server Details</span></h3>
						<div class="inside" style="margin:10px;">
						  	<div class="dashboard-widget-content">
					      		<ul class="cvg_settings">
									<?php CvgCore::cvg_serverinfo(); ?>
								</ul>
							</div>	
						</div>
					</div>
				</div>	
			</div>
		</div>
	</div>
</div>