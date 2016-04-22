	<div id="editbar">
		<a class="cancel top-cancel" href="#">&#10005;</a>
		<h4 class="editbar-title"></h4> <span class="spinner" id="editbar-ajax-loading"></span>

		<div class="conditions">
			<span class="condition-labels">
				<span class="condition-if">if</span>
				<span class="condition-elseif">elseif</span>
				<span class="condition-else">else</span>
			</span>
	<?php

			$fields = array(
				'email' => mymail_text('email'),
				'firstname' => mymail_text('firstname'),
				'lastname' => mymail_text('lastname'),
			);

			$customfields = mymail()->get_custom_fields();
			foreach ($customfields as $field => $data) {
				$fields[$field] = $data['name'];
			}
			$operators = array(
				'is' => __('is', 'mymail'),
				'is_not' => __('is not', 'mymail'),
				'contains' => __('contains', 'mymail'),
				'contains_not' => __('contains not', 'mymail'),
				'begin_with' => __('begins with', 'mymail'),
				'end_with' => __('ends with', 'mymail'),
				'is_greater' => __('is greater', 'mymail'),
				'is_smaller' => __('is smaller', 'mymail'),
				'pattern' => __('match regex pattern', 'mymail'),
				'not_pattern' => __('does not match regex pattern', 'mymail'),
			);


	?>
			<select class="condition-fields">
	<?php

			foreach ($fields as $key => $name) {
				echo '<option value="'.$key.'">'.$name.'</option>';
			}

	?>

			</select>
			<select class="condition-operators">
	<?php

			foreach ($operators as $key => $name) {
				echo '<option value="'.$key.'">'.$name.'</option>';
			}

	?>
			</select>
			<input class="condition-value" type="text" value="" class="widefat">
		</div>

		<div class="editbar-types">

		<div class="type single">
			<div class="conditinal-area-wrap">
				<div class="conditinal-area">
					<div class="type-input"><input type="text" class="input live widefat" value=""></div>
				</div>
			</div>
			<div class="clear clearfix">
				<a class="single-link-content" href="#"><?php _e('convert to link', 'mymail'); ?></a> |
				<a class="replace-image" href="#"><?php _e('replace with image', 'mymail') ?></a>
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
				<a class="nav-tab" href="#text_button" data-type="dynamic"><?php _e('Text Button', 'mymail'); ?></a>
				<a class="nav-tab nav-tab-active" href="#image_button"><?php _e('Image Button', 'mymail'); ?></a>
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
					<label class="block"><div class="left"><?php _e('Link Button', 'mymail') ?> <span class="description">(<?php _e('required', 'mymail') ?>)</span></div><div class="right"><input type="text" class="input buttonlink" value="" placeholder="<?php _e('insert URL', 'mymail'); ?>"></div></label>
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
					<p><?php _e('Size', 'mymail' ); ?>: <input type="number" class="imagewidth">&times;<input type="number" class="imageheight">px
					</p>
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
					<input type="hidden" class="input orgimageurl" value="">
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

				<p>
				<?php

				$content = '<select id="dynamic_embed_options_content" class="check-for-posts"><option value="excerpt">'.__('the excerpt', 'mymail').'</option><option value="content">'.__('the full content', 'mymail').'</option></select>';

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

				echo sprintf(_x('Insert %1$s of %2$s %3$s', 'Insert [excerpt] of [latest] [post]','mymail'), $content, $relative, $post_types);
				?>

				</p>
				<div class="right">
					<div class="current-preview">
					<label><?php _e('Current Match', 'mymail') ?></label>
					<h4 class="current-match">&hellip;</h4>
					<div class="current-tag code">&hellip;</div>
					</div>
				</div>
				<div class="left">
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

		</div>

		<div class="buttons clearfix">
			<button class="button button-primary save"><?php _e('Save', 'mymail') ?></button>
			<button class="button cancel"><?php _e('Cancel', 'mymail') ?></button>
			<label class="highdpi-checkbox" title="<?php _e('use HighDPI/Retina ready images if available', 'mymail'); ?>"><input type="checkbox" class="highdpi"> <?php _e('HighDPI/Retina ready', 'mymail'); ?></label>
			<a class="remove mymail-icon" title="<?php _e('remove element', 'mymail') ?>"></a>
		</div>
		<input type="hidden" class="factor" value="1">

	</div>
