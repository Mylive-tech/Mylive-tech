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
<?php if($editable) : ?>
<div id="optionbar" class="optionbar">
	<ul class="alignleft">
		<li class="no-border-left"><a class="mymail-icon undo disabled" title="<?php _e('undo', 'mymail') ?>">&nbsp;</a></li>
		<li><a class="mymail-icon redo disabled" title="<?php _e('redo', 'mymail') ?>">&nbsp;</a></li>
		<?php if(!empty($modules)) : ?>
		<li><a class="mymail-icon clear-modules" title="<?php _e('remove modules', 'mymail') ?>">&nbsp;</a></li>
		<?php endif; ?>
		<?php if(current_user_can('mymail_see_codeview')) :?>
		<li><a class="mymail-icon code" title="<?php _e('toggle HTML/code view', 'mymail') ?>">&nbsp;</a></li>
		<?php endif; ?>
		<?php if(current_user_can('mymail_change_plaintext')) :?>
		<li><a class="mymail-icon plaintext" title="<?php _e('toggle HTML/Plain-Text view', 'mymail') ?>">&nbsp;</a></li>
		<?php endif; ?>
		<li class="no-border-right"><a class="mymail-icon preview" title="<?php _e('preview', 'mymail') ?>">&nbsp;</a></li>
		<?php if($templates && current_user_can('mymail_save_template')) : ?>
	</ul>
	<ul class="alignright">
		<li class=""><a class="mymail-icon save-template" title="<?php _e('save template', 'mymail') ?>">&nbsp;</a>
			<div class="dropdown">
				<div class="ddarrow"></div>
				<div class="inner">
					<h4><?php _e('Save Template', 'mymail') ?></h4>
					<p>
						<label><?php _e('Name', 'mymail'); ?><br><input type="text" class="widefat" id="new_template_name" placeholder="<?php _e('template name', 'mymail'); ?>" value="<?php echo ($this->get_file() != 'index.html' ? $all_files[$this->get_template()][$this->get_file()]['label'] : ''); ?>"></label>
						<?php if(!empty($modules)) : ?>
						<label><input type="checkbox" id="new_template_modules" checked> <?php _e('include modules', 'mymail'); ?></label>
						<?php endif; ?>
						<label><input type="checkbox" id="new_template_overwrite"> <?php _e('overwrite if exists', 'mymail'); ?></label>
					</p>
					<p class="foot">
						<span class="spinner" id="new_template-ajax-loading"></span>
						<button class="button-primary save-template"><?php _e('Save', 'mymail'); ?></button>
					</p>
				</div>
			</div>
		</li>
		<?php endif; ?>
		<?php if($templates && current_user_can('mymail_change_template')) :
				$single = count($templates) == 1;
		?>
		<li class="current_template <?php if($single) echo 'single';?>"><span class="change_template" title="<?php echo sprintf(__('Your currently working with %s', 'mymail'), '&quot;'.$all_files[$this->get_template()][$this->get_file()]['label'].'&quot;' ); ?>"><?php echo $all_files[$this->get_template()][$this->get_file()]['label']; ?></span>
			<div class="dropdown">
				<div class="ddarrow"></div>
				<div class="inner">
					<h4><?php _e('Change Template', 'mymail') ?></h4>
					<ul>
						<?php
						$current = $this->get_template().'/'.$this->get_file();
						foreach($templates as $slug => $data){
						?>
							<li><?php if(!$single): ?><a class="template"><?php echo $data['name']?></a><?php endif; ?>
								<ul <?php if($this->get_template() == $slug) echo ' style="display:block"'?>>
						<?php
							foreach($all_files[$slug] as $name => $data){
								$value = $slug.'/'.$name;
							?>
								<li><a class="file<?php if($current == $value) echo ' active';?>" <?php if($current != $value) echo 'href="//'.add_query_arg( array( 'template' => $slug, 'file' => $name, 'message' => 2), $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]).'"';?>><?php echo $data['label']?></a></li>
							<?php
							}
							?>
								</ul>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</li>
		<?php endif; ?>
	</ul>

</div>
<div id="editbar">
	<a class="cancel top-cancel" href="#">&#10005;</a>
	<h2></h2> <span class="spinner" id="editbar-ajax-loading"></span>

	<div class="type single">
		<div class="clearfix clear alignright">
			<a class="replace-image" href="#"><?php _e('replace with image', 'mymail') ?></a>
		</div>
		<input type="text" class="input live widefat" value="">
		<div class="clear clearfix">
			<a href="#" class="single-link-content"><?php _e('convert to link', 'mymail'); ?></a>
		</div>
		<div id="single-link">
			<div class="clearfix">
					<label class="block"><div class="left"><?php _e('Link', 'mymail') ?></div><div class="right"><input type="text" class="input singlelink" value="" placeholder="<?php _e('insert URL', 'mymail'); ?>"></div></label>
			</div>
			<div class="link-wrap">
				<div class="postlist">
				</div>
			</div>
		</div>
	</div>

	<div class="type btn">

		<div id="button-type-bar" class="nav-tab-wrapper hide-if-no-js">
			<a class="nav-tab nav-tab-active" href="#image_button"><?php _e('Image Button', 'mymail'); ?></a>
			<a class="nav-tab" href="#text_button" data-type="dynamic"><?php _e('Text Button', 'mymail'); ?></a>
		</div>
		<div id="image_button" class="tab">
		<?php $this->templateobj->buttons( ); ?>
		<div class="clearfix">
				<label class="block"><div class="left"><?php _e('Alt Text', 'mymail') ?></div><div class="right"><input type="text" class="input buttonalt" value="" placeholder="<?php _e('image description', 'mymail'); ?>"></div></label>
		</div>
		</div>
		<div id="text_button" class="tab" style="display:none">
		<div class="clearfix">
				<label class="block"><div class="left"><?php _e('Button Label', 'mymail') ?></div><div class="right"><input type="text" class="input buttonlabel" value="" placeholder="<?php _e('button label', 'mymail'); ?>"></div></label>
		</div>
		</div>

		<div class="clearfix">
				<label class="block"><div class="left"><?php _e('Link Button', 'mymail') ?><span class="description">(<?php _e('required', 'mymail') ?>)</span></div><div class="right"><input type="text" class="input buttonlink" value="" placeholder="<?php _e('insert URL', 'mymail'); ?>"></div></label>
		</div>
		<div class="link-wrap">
			<div class="postlist">
			</div>
		</div>
		<?php
	?>
	</div>

	<div class="type multi">
<?php

function mymail_quicktags_settings($qtInit, $editor_id){
	$qtInit['buttons'] = 'strong,em,link,block,del,img,ul,ol,li,spell,close';
	return $qtInit;
}
add_filter('quicktags_settings', 'mymail_quicktags_settings', 99, 2);

wp_editor('', 'mymail-editor', array(
	'wpautop' => false,
	'remove_linebreaks' => false,
	'media_buttons' => false,
	'textarea_rows' => 18,
	'teeny' => false,
	'quicktags' => true,
	'editor_height' => 295,
	'tinymce' => array(
		'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,|,undo,redo,|,link,unlink,|,removeformat',
		'theme_advanced_buttons2' => '',
		'theme_advanced_buttons3' => '',
		'toolbar1' => 'bold,italic,underline,strikethrough,|,bullist,numlist,|,alignleft,aligncenter,alignright,alignjustify,|,forecolor,|,undo,redo,|,link,unlink,|,removeformat',
		'toolbar2'=> '',
		'toolbar3'=> '',
		'apply_source_formatting' => true,
		'content_css' => MYMAIL_URI . 'assets/css/tinymce-style.css?v='.MYMAIL_VERSION,
	)
));
?>
	</div>

	<div class="type img">
		<div class="imagecontentwrap">
			<div class="left">
				<p><?php _e('Size', 'mymail' ); ?>: <input type="number" class="imagewidth">&times;<input type="number" class="imageheight">px</p>
				<div class="imagewrap">
				<img src="" alt="" class="imagepreview">
				</div>
			</div>
			<div class="right">
				<p>
					<label><input type="text" class="widefat" id="image-search" placeholder="<?php _e('search for images', 'mymail' ); ?>..." ></label>
				</p>
				<div class="imagelist">
				</div>
				<p>
					<a class="button button-small add_image"><?php ((!function_exists( 'wp_enqueue_media' )) ? _e('Upload', 'mymail') : _e('Media Manager', 'mymail'))?></a>
					<a class="button button-small reload"><?php _e('Reload', 'mymail') ?></a>
					<a class="button button-small add_image_url"><?php _e('Insert from URL', 'mymail') ?></a>
				</p>
			</div>
		<br class="clear">
		</div>
		<p class="clearfix">
			<div class="imageurl-popup">
				<label class="block"><div class="left"><?php _e('Image URL', 'mymail') ?></div><div class="right"><input type="text" class="input imageurl" value="" placeholder="http://example.com/image.jpg"></div></label>
			</div>
				<label class="block"><div class="left"><?php _e('Alt Text', 'mymail') ?></div><div class="right"><input type="text" class="input imagealt" value="" placeholder="<?php _e('image description', 'mymail'); ?>"></div></label>
				<label class="block"><div class="left"><?php _e('Link image to the this URL', 'mymail') ?></div><div class="right"><input type="text" class="input imagelink" value="" placeholder="<?php _e('insert URL', 'mymail'); ?>"></div></label>
		</p>
		<br class="clear">
	</div>

	<div class="type auto">

		<div id="embedoption-bar" class="nav-tab-wrapper hide-if-no-js">
			<a class="nav-tab nav-tab-active" href="#static_embed_options" data-type="static"><?php _e('static', 'mymail'); ?></a>
			<a class="nav-tab" href="#dynamic_embed_options" data-type="dynamic"><?php _e('dynamic', 'mymail'); ?></a>
			<a class="nav-tab" href="#rss_embed_options" data-type="rss"><?php _e('RSS', 'mymail'); ?></a>
		</div>

		<div id="static_embed_options" class="tab">
			<p class="editbarinfo"><?php _e('Select a post', 'mymail') ?></p>
			<p class="alignleft">
				<label title="<?php _e('use the excerpt if exists otherwise use the content', 'mymail'); ?>"><input type="radio" name="embed_options_content" class="embed_options_content" value="excerpt" checked> <?php _e('excerpt', 'mymail'); ?> </label>
				<label title="<?php _e('use the content', 'mymail'); ?>"><input type="radio" name="embed_options_content" class="embed_options_content" value="content"> <?php _e('full content', 'mymail'); ?> </label>
			</p>
			<p id="post_type_select" class="alignright">
			<?php
				$pts = get_post_types( array( 'public' => true ), 'objects' );
				foreach($pts as $pt => $data){
					if(in_array($pt, array('attachment', 'newsletter'))) continue;
			?>
			<label><input type="checkbox" name="post_types[]" value="<?php echo $pt ?>" <?php checked($pt == 'post', true); ?>> <?php echo $data->labels->name ?> </label>
			<?php
				}
			?>
			</p>
			<p>
				<label><input type="text" class="widefat" id="post-search" placeholder="<?php _e('search for posts', 'mymail' ); ?>..." ></label>
			</p>
			<div class="postlist">
			</div>
		</div>

		<div id="dynamic_embed_options" class="clear tab" style="display:none;">
			<div class="right">
			<h4>&hellip;</h4>
			</div>
			<div class="left">
			<p>

			<?php
			$content = '<select id="dynamic_embed_options_content"><option value="excerpt">'.__('the excerpt', 'mymail').'</option><option value="content">'.__('the full content', 'mymail').'</option></select>';

			$relative = '<select id="dynamic_embed_options_relative" class="check-for-posts">';
			$relativenames = array(
				-1 => __('the latest', 'mymail'),
				-2 => __('the second latest', 'mymail'),
				-3 => __('the third latest', 'mymail'),
				-4 => __('the fourth latest', 'mymail'),
				-5 => __('the fifth latest', 'mymail'),
				-6 => __('the sixth latest', 'mymail'),
				-7 => __('the seventh latest', 'mymail'),
				-8 => __('the eighth latest', 'mymail'),
				-9 => __('the ninth latest', 'mymail'),
				-10 => __('the tenth latest', 'mymail'),
				-11 => __('the eleventh latest', 'mymail'),
				-12 => __('the twelfth latest', 'mymail'),
			);

			foreach($relativenames as $key => $name){
				$relative .= '<option value="'.$key.'">'.$name.'</option>';
			}

			$relative .= '</select>';
			$post_types = '<select id="dynamic_embed_options_post_type">';
			foreach($pts as $pt => $data){
				if(in_array($pt, array('attachment', 'newsletter'))) continue;
				$post_types .= '<option value="'.$pt.'">'.$data->labels->singular_name.'</option>';
			}
			$post_types .= '</select>';

			echo sprintf(_x('Insert %1$s of %2$s %3$s', 'Insert [excerpt] of [latest] [post]','mymail'), $content, $relative, $post_types); ?>

			</p>
			<div id="dynamic_embed_options_cats"></div>
			</div>
			<p class="description clear"><?php _e('dynamic content get replaced with the proper content as soon as the campaign get send. Check the quick preview to see the current status of dynamic elements', 'mymail'); ?></p>
		</div>

		<div id="rss_embed_options" class="tab">

			<div id="rss_input">
			<p>
				<?php _e('Enter feed URL', 'mymail') ?><br>
				<label><input type="text" id="rss_url" class="widefat" placeholder="http://example.com/feed.xml" value=""></label>
			</p>
				<ul id="recent_feeds">
			<?php if($recent_feeds = get_option('mymail_recent_feeds')) :
					echo '<li><strong>'.__('Recent Feeds', 'mymail').'</strong></li>';
				foreach($recent_feeds as $title => $url){
					echo '<li><a href="'.$url.'">'.$title.'</a></li>';
				}
			endif; ?>
				</ul>
			</div>

			<div id="rss_more" style="display:none;">
				<div class="alignright"><a href="#" class="rss_change"><?php _e('change', 'mymail'); ?></a></div>
				<div class="rss_info"></div>
				<p class="editbarinfo clear">&nbsp;</p>
				<p class="alignleft">
					<label title="<?php _e('use the excerpt if exists otherwise use the content', 'mymail'); ?>"><input type="radio" name="embed_options_content_rss" class="embed_options_content_rss" value="excerpt" checked> <?php _e('excerpt', 'mymail'); ?> </label>
					<label title="<?php _e('use the content', 'mymail'); ?>"><input type="radio" name="embed_options_content_rss" class="embed_options_content_rss" value="content"> <?php _e('full content', 'mymail'); ?> </label>
				</p>
				<div class="postlist">
				</div>
			</div>
		</div>

	</div>
	<div class="type codeview">
		<textarea id="module-codeview-textarea" autocomplete="off"></textarea>
	</div>

	<div class="buttons clearfix">
		<button class="button button-primary button-large save"><?php _e('Save', 'mymail') ?></button>
		<button class="button button-large cancel"><?php _e('Cancel', 'mymail') ?></button>
		<label class="highdpi-checkbox" title="<?php _e('use HighDPI/Retina ready images if available', 'mymail'); ?>"><input type="checkbox" class="highdpi"> <?php _e('HighDPI/Retina ready', 'mymail'); ?></label>
		<a class="remove mymail-icon" title="<?php _e('remove element', 'mymail') ?>"></a>
	</div>
	<input type="hidden" class="factor" value="1">
</div>
<div id="mymail_type_preview"></div>
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
<iframe id="mymail_iframe" src="<?php echo admin_url('admin-ajax.php?action=mymail_get_template&id='.$post->ID.'&template='.$this->get_template().'&file='.$this->get_file().'&_wpnonce='.wp_create_nonce('mymail_nonce').'&editorstyle='.($editable).'&nocache='.time())?>" width="100%" height="1000" scrolling="no" frameborder="0"></iframe>
<div id="mymail_campaign_preview" style="display:none;"><div class="mymail_campaign_preview device-full">
	<div class="device-list optionbar">
		<ul>
			<li><a data-size="full" class="mymail-icon device-full">&nbsp;</a></li>
			<li><a data-size="320x480" class="mymail-icon device-320x480">&nbsp;</a></li>
			<li><a data-size="480x320" class="mymail-icon device-480x320">&nbsp;</a></li>
		</ul>
	</div>
	<div class="device-wrap">
		<div class="preview-frame">
		<iframe id="mymail_campaign_preview_iframe" src="" width="100%" scrolling="auto" frameborder="0"></iframe>
		</div>
	</div>
	<p class="device-info"><?php _e('Your email may look different on mobile devices', 'mymail'); ?></p>
</div></div>
<textarea id="content" class="hidden" autocomplete="off" name="content" ><?php echo $post->post_content ?></textarea>
<textarea id="modules" class="hidden" autocomplete="off"><?php echo $modules ?></textarea>
<textarea id="head" name="mymail_data[head]" class="hidden" autocomplete="off"><?php echo isset($this->post_data['head']) ? $this->post_data['head'] : $this->templateobj->get_head(); ?></textarea>
