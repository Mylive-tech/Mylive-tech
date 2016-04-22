<?php

	$editable = !in_array($post->post_status, array('active', 'finished'));
	if(isset($_GET['showstats']) && $_GET['showstats']) $editable = false;

	$modules = $this->replace_colors($this->templateobj->get_modules_html());

	$templates = mymail('templates')->get_templates();
	$all_files = mymail('templates')->get_all_files();

	//templateswitcher was used
	if(isset($_GET['template']) && current_user_can('mymail_change_template')){
		$this->set_template($_GET['template'], $this->get_file() , true);
	//saved campaign
	}else if(isset($this->details['template'])){
		$this->set_template($this->details['template'], $this->get_file(), true);
	}

?>
<div id="template-wrap" class="load <?php if($editable && !!get_user_setting('mymailshowmodules', 1) && !empty($modules)) echo ' show-modules'; if($editable && !empty($modules)) echo ' has-modules' ?>">

<?php if($editable) : ?>

	<?php include 'optionbar.php' ?>
	<?php include 'editbar.php' ?>

<?php

	else :

	$stats['total'] = $this->get_clicks($post->ID, true);
	$stats['clicks'] = $this->get_clicked_links($post->ID);

?>
	<div id="mymail_click_stats" data-stats='<?php echo json_encode($stats);?>'></div>
	<div id="clickmap-stats">
		<div class="piechart" data-percent="0" data-size="60" data-line-width="8" data-animate="500"><span>0</span>%</div>
		<p><strong class="link"></strong></p>
		<p><?php _e('Clicks', 'mymail' ); ?>: <strong class="clicks">0</strong><br><?php _e('Total', 'mymail' ); ?>: <strong class="total">0</strong></p>
	</div>
	<textarea id="content" name="content" class="hidden" autocomplete="off"><?php echo $post->post_content ?></textarea>
	<textarea id="excerpt" name="excerpt" class="hidden" autocomplete="off"><?php echo $post->post_excerpt ?></textarea>
<?php
	endif;
?>

	<div id="plain-text-wrap">
		<?php $autoplaintext = !isset($this->post_data['autoplaintext']) || $this->post_data['autoplaintext']?>
			<p><label><input type="checkbox" id="plaintext" name="mymail_data[autoplaintext]" value="1" <?php checked( $autoplaintext ); ?>> <?php _e('Create the plain text version based on the HTML version of the campaign', 'mymail'); ?></label> <a class="alignright button button-small button-primary"><?php _e('get text from HTML version' , 'mymail'); ?></a></p>

			<textarea id="excerpt" name="excerpt" class="<?php if($autoplaintext) echo ' disabled' ?>" autocomplete="off" <?php disabled($autoplaintext); ?>><?php echo $post->post_excerpt ?></textarea>
	</div>

	<div id="html-wrap">
		<?php if($editable && !empty($modules)) :
			$module_list = $this->templateobj->get_module_list();
			$screenshots = $this->templateobj->get_module_screenshots();
			$has_screenshots = !empty($screenshots);
			$screenshot_modules_folder = MYMAIL_UPLOAD_DIR.'/screenshots/'.$this->get_template().'/modules/';
			$screenshot_modules_folder_uri = MYMAIL_UPLOAD_URI.'/screenshots/'.$this->get_template().'/modules/';
		?>
		<div id="module-selector">
			<a class="toggle-modules mymail-btn mymail-icon" title="<?php _e('Modules', 'mymail') ?>"></a>
			<div class="inner">
				<ul>
				<?php foreach ($module_list as $i => $module) {

					if($has_screenshots && file_exists($screenshot_modules_folder.$screenshots[$i]))
						$has_screenshots = getimagesize($screenshot_modules_folder.$screenshots[$i]);

					echo '<li data-id="'.$i.'" draggable="true"><a class="mymail-btn addmodule '.($has_screenshots ? 'has-screenshot" style="background-image:url('.$screenshot_modules_folder_uri.$screenshots[$i].');height:'.floor($has_screenshots[1]/2).'px;' : '' ).'" title="'.esc_attr(sprintf(__('Click to add %s', 'mymail') , '"'.$module.'"')).'" data-id="'.$i.'">'.esc_html($module).'</a></li>';
				} ?>
				</ul>
			</div>
		</div>
		<?php endif; ?>

		<div id="iframe-wrap">
			<iframe id="mymail_iframe" src="<?php echo admin_url('admin-ajax.php?action=mymail_get_template&id='.$post->ID.'&template='.$this->get_template().'&file='.$this->get_file().'&_wpnonce='.wp_create_nonce('mymail_nonce').'&editorstyle='.($editable).'&nocache='.time())?>" width="100%" height="500" scrolling="no" frameborder="0"></iframe>
		</div>
	</div>

</div>

<div id="mymail_campaign_preview" style="display:none;">
	<div class="device-wrap">
		<div class="device desktop">
			<div class="desktop-header">
				<div class="desktop-header-bar"><i></i><i></i><i></i></div>
				<div class="desktop-header-info"><u></u><i></i><i></i></div>
			</div>
				<div class="desktop-body">
					<div class="preview-body">
						<iframe class="mymail-preview-iframe desktop" src="" width="100%" scrolling="auto" frameborder="0"></iframe>
					</div>
				</div>
		</div>
		<div class="device mobile">
				<div class="mobile-header"><u></u><i></i></div>
				<div class="mobile-body">
					<div class="preview-body">
						<iframe class="mymail-preview-iframe mobile" src="" width="100%" scrolling="auto" frameborder="0"></iframe>
					</div>
				</div>
				<div class="mobile-footer"><i></i></div>
		<p class="device-info"><?php _e('Your email may look different on mobile devices', 'mymail'); ?></p>
		</div>
	</div>
</div>
<textarea id="content" class="hidden" autocomplete="off" name="content" ><?php echo $post->post_content ?></textarea>
<textarea id="modules" class="hidden" autocomplete="off"><?php echo $modules ?></textarea>
<textarea id="head" name="mymail_data[head]" class="hidden" autocomplete="off"><?php echo isset($this->post_data['head']) ? $this->post_data['head'] : $this->templateobj->get_head(); ?></textarea>
