<?php
/**
 * Section to uninstall plugin
 * @author Praveen Rajan
 */
?>
<?php

	if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
		die('You are not allowed to call this page directly.'); 

	$title = __('CVG Uninstall');

	CvgCore::upgrade_plugin();

	if(isset($_POST['uninstallplugin'])) {
		
		// wp_nonce_field('cvg_plugin_uninstall_nonce','cvg_plugin_uninstall_nonce_csrf');
		if ( check_admin_referer( 'cvg_plugin_uninstall_nonce', 'cvg_plugin_uninstall_nonce_csrf' ) ) {
			CoolVideoGallery::cvg_uninstall();
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			deactivate_plugins(dirname(dirname(__FILE__)) . '/cool-video-gallery.php' );
			?>
			<script type="text/javascript">
				location.href= "<?php  echo admin_url('plugins.php');?>";</script>
			<?php
		}
	}
?>
<style type="text/css">
	#dashboard-widgets a {
		text-decoration: none;
	}
</style>
<div class="wrap">
	<div class="icon32" id="icon-video">
		<br>
	</div>
	<h2><?php  echo esc_html($title);?></h2>
	<div id="dashboard-widgets-wrap">
		<form method="post" action="<?php  echo admin_url('admin.php?page=cvg-plugin-uninstall');?>">
			<div id="dashboard-widgets" class="metabox-holder">
				<div style="width: 100%;" class="postbox-container">
					<div class="meta-box-sortables ui-sortable" id="left-sortables"  style="min-height:0px;">
						<div class="postbox" id="server_settings" >
							<div class="inside" style="margin:10px;color:red;">
								<b>Note:</b> For future use, please backup all your data including database before you uninstall this plugin.
							</div>
							
							<?php wp_nonce_field('cvg_plugin_uninstall_nonce','cvg_plugin_uninstall_nonce_csrf'); ?>
							<div class="submit" style="padding-left:15px;">
								<input type="submit" class="button-primary action" name="uninstallplugin" value="<?php _e("Uninstall CVG");?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>