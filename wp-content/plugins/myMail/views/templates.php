<?php

	$t = mymail('templates');

	$templates = $t->get_templates();
	$mymail_templates = $t->get_mymail_templates();
	$licensecodes = $t->get_license();

	$notice = false;#
	if($updates = $t->get_updates()){
		echo '<div class="update-nag below-h2"><p>'.sprintf( _n( '%d Update available', '%d Updates available', $updates, 'mymail'), $updates).'</p></div>';
	}
?>
<div class="wrap">
<div id="mymail_templates">
<?php
	$default = mymail_option('default_template', 'mymail');
	if(!isset($templates[$default])){
		$default = 'mymail';
		mymail_update_option('default_template', 'mymail');
		$notice[] = sprintf(__('Template %s is missing or broken. Reset to default', 'mymail'), '"'.$default.'"');

		//mymail template is missing => redownload it
		if(!isset($templates[$default])){
			$t->renew_default_template();
			$templates = $t->get_templates();
		}
	}
	$template = $templates[$default];


	if(!isset($_GET['more'])) :
?>

<ul>
<li id="templateeditor">
	<h3></h3>
	<input type="hidden" id="slug">
	<input type="hidden" id="file">

		<div class="inner">
			<div class="template-file-selector">
				<label><?php _e('Select template file', 'mymail') ?>:</label> <span></span>
			</div>
			<div class="edit-buttons">
				<span class="spinner template-ajax-loading"></span>
				<span class="message"></span>
				<button class="button-primary save"><?php _e('Save', 'mymail')?></button>
				<button class="button saveas"><?php _e('Save as', 'mymail')?>&hellip;</button> <?php _e('or', 'mymail') ?>
				<a class="cancel" href="#"><?php _e('Cancel', 'mymail')?></a>
			</div>
				<textarea class="editor"></textarea>
			<div class="edit-buttons">
				<span class="message"></span>
				<span class="spinner template-ajax-loading"></span>
				<button class="button-primary save"><?php _e('Save', 'mymail')?></button>
				<button class="button saveas"><?php _e('Save as', 'mymail')?>&hellip;</button> <?php _e('or', 'mymail') ?>
				<a class="cancel" href="#"><?php _e('Cancel', 'mymail')?></a>
			</div>
		</div>
	<br class="clear">
</li>
</ul>
<div id="icon-edit" class="icon32"></div>
<h2><?php _e('Templates', 'mymail') ?> <a href="edit.php?post_type=newsletter&page=mymail_templates&more" class="add-new-h2"> <?php _e('more Templates', 'mymail'); ?> </a></h2>
<?php
wp_nonce_field('mymail_nonce');
if($notice){
	foreach($notice as $note){?>
<div class="updated below-h2"><p><?php echo $note ?></p></div>
<?php }
}?>
<ul id="installed-templates">

<?php
	$i = 0;
	unset($templates[$default]);

	$new = isset($_GET['new']) && isset($templates[$_GET['new']]) ? esc_attr($_GET['new']) : NULL;

	if($new){
		$new_template = $templates[$new];
		unset($templates[$new]);
		$templates = array($new => $new_template) + $templates;
	}
	$templates = array($default => $template) + $templates;

	foreach($templates as $slug => $data){

		$update = isset($mymail_templates[$slug]) && $mymail_templates[$slug]['update'] && current_user_can('mymail_update_templates');
		$licensecode = isset($licensecodes[$slug]) ? $licensecodes[$slug] : '';
		$envato_item_id = isset($mymail_templates[$slug]['envato_item_id']) ? $mymail_templates[$slug]['envato_item_id'] : NULL;

		$is_free = isset($mymail_templates[$slug]) && isset($mymail_templates[$slug]['is_free']);

		?>
		<li class="available-template<?php if($update ) echo ' update'; ?><?php if($default == $slug) echo ' is-default'; ?><?php if($new == $slug) echo ' is-new'; ?>" id="template-<?php echo $slug ?>" name="mymail_template_<?php echo $i ?>" data-id="<?php echo $i++?>">
			<?php if(isset($updates[$slug])){?>
				<span class="update-badge"><?php echo $updates[$slug]?></span>
			<?php }?>
			<div class="screenshot">
				<img alt="<?php _e('Screenshot', 'mymail'); ?>" src="<?php echo $t->get_screenshot($slug) ?>" width="300" height="225" title="<?php echo $data['name'].' '.$data['version']?> <?php _e('by', 'mymail'); ?> <?php echo $data['author']?>">
				<a class="thickbox-preview" href="<?php echo $t->url .'/' .$slug .'/index.html'?>" data-slug="<?php echo $slug ?>"><?php _e( 'Preview', 'mymail' ); ?></a>
				<a class="" href="<?php echo admin_url('post-new.php?post_type=newsletter&template='.$slug.'')?>" data-slug="<?php echo $slug ?>"><?php _e( 'Start new Campaign', 'mymail' ); ?></a>
			</div>
			<div class="meta">
			<h3><?php echo $data['name'] ?> <span class="version"><?php echo $data['version'] ?></span>
				<?php
				if($update) :
					if(empty($licensecode) && !$is_free) : ?>

						<?php if($envato_item_id) : ?>
						<a title="<?php _e('update via Envato', 'mymail' ); ?>" class="update envato-activate button button-primary button-small alignright" href="<?php echo add_query_arg(array('auth' => wp_create_nonce( 'envato-activate' ), 'item_id' => $mymail_templates[$slug]['envato_item_id'], 'slug' => $slug, 'returnto' => urlencode(admin_url('edit.php?post_type=newsletter&page=mymail_templates'))), $mymail_templates[$slug]['endpoint']) ?>" data-slug="<?php echo $slug ?>"><?php echo sprintf(__('Update to %s', 'mymail'), $mymail_templates[$slug]['new_version']); ?></a>
						<?php else : ?>
						<a title="<?php _e('activate with licensecode', 'mymail' ); ?>" class="activate button button-primary button-small alignright" href="edit.php?post_type=newsletter&page=mymail_templates&action=license&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('license-'.$slug)?>" data-license="<?php echo $licensecode ?>" data-slug="<?php echo $slug ?>"><?php _e('Activate', 'mymail'); ?></a>
						<?php endif; ?>

					<?php else :?>

						<a title="<?php _e('update template', 'mymail' ); ?>" class="update button button-primary button-small alignright" href="edit.php?post_type=newsletter&page=mymail_templates&action=update&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('download-'.$slug)?>" data-license="<?php echo $licensecode ?>" data-slug="<?php echo $slug ?>"><?php echo sprintf(__('Update to %s', 'mymail'), $mymail_templates[$slug]['new_version']); ?></a>

					<?php endif; ?>
				<?php endif; ?>
			</h3>
			<div> <?php _e('by', 'mymail'); ?> <?php if(!empty($data['author_uri'])) : ?><a href="<?php echo $data['author_uri']?>"><?php echo $data['author']?></a><?php else : ?> <?php echo $data['author']?><?php endif; ?></div>
			</div>
			<?php if(!empty($data['description'])) : ?>
				<p class="description"><?php echo $data['description']?></p>
			<?php elseif(!empty($mymail_templates[$slug]['description'])) : ?>
				<p class="description"><?php echo $mymail_templates[$slug]['description']?></p>
			<?php endif; ?>
			<div class="licensecode">
				<form action="edit.php?post_type=newsletter&page=mymail_templates" method="get">
				<input type="hidden" name="post_type" value="newsletter">
				<input type="hidden" name="page" value="mymail_templates">
				<input type="hidden" name="more" value="1">
				<input type="hidden" name="action" value="license">
				<input type="hidden" name="template" value="<?php echo $slug ?>">
				<input type="text" name="license" class="widefat license" value="" placeholder="<?php _e('Enter Licensecode', 'mymail') ?>">
				<?php wp_nonce_field( 'license-'.$slug, '_wpnonce', false ); ?>
				<input type="submit" class="button save-license" value="<?php _e('save', 'mymail') ?>">
				</form>
			</div>
			<div class="action-links">
				<ul>
					<?php if($default != $slug) : ?>
					<li><a title="Set &quot;<?php echo $data['name'] ?>&quot; as default" class="activatelink button" href="edit.php?post_type=newsletter&amp;page=mymail_templates&amp;action=activate&amp;template=<?php echo $slug ?>&amp;_wpnonce=<?php echo wp_create_nonce('activate-'.$slug)?>"><?php _e('Use as default', 'mymail'); ?></a></li>
					<?php endif; ?>
				 	<?php if(current_user_can('mymail_edit_templates')) :
						$writeable = is_writeable($t->path .'/'.$slug .'/index.html');
				 	?>

					<li><a title="<?php echo esc_attr(sprintf('Edit %s', '"'.$data['name'].'"')) ?>" class="edit <?php echo (!$writeable ? 'disabled' : '')?> button" data-slug="<?php echo esc_attr($slug) ?>" href="<?php echo $slug .'/index.html'?>" <?php if(!$writeable) :?>onclick="alert('<?php _e('This file is not writeable! Please change the file permission', 'mymail'); ?>');return false;"<?php endif; ?>><?php _e('Edit HTML', 'mymail') ?></a></li>
					<?php endif; ?>
				</ul>
				<?php if($slug != mymail_option('default_template') && current_user_can('mymail_delete_templates')) { ?>
					<div class="delete-theme">
						<a data-name="<?php echo esc_attr($data['name']) ?>" href="edit.php?post_type=newsletter&amp;page=mymail_templates&amp;action=delete&amp;template=<?php echo $slug ?>&amp;_wpnonce=<?php echo wp_create_nonce('delete-'.$slug)?>" class="submitdelete deletion"><?php _e('Delete', 'mymail' ); ?></a>
					</div>
			<?php }?>
			</div>
			<div class="loader"></div>
		</li>
		<?php
	}
		if(current_user_can('mymail_upload_templates')) :
		?>
		<li class="upload-field"><?php $t->media_upload_form(); ?></li>
		<?php

		endif;
?>
</ul>

<?php else:  ?>

<div id="icon-edit" class="icon32"></div>
<h2><?php _e('more Templates', 'mymail') ?> <a href="edit.php?post_type=newsletter&page=mymail_templates" class="add-new-h2"> <?php _e('back to overview', 'mymail'); ?> </a></h2>

<ul id="available-templates">
<?php

	if(empty($mymail_templates)) :

		echo '<div class="error below-h2"><p>'.__('Seems there was a problem getting the list of templates', 'mymail').'</p></div>';

	else :

	$existing = @array_intersect_assoc($mymail_templates, $templates);
	$others = @array_diff_assoc($mymail_templates, $existing);

	$mymail_templates = $existing + $others;

	foreach($mymail_templates as $slug => $data){

		$licensecode = isset($licensecodes[$slug]) ? $licensecodes[$slug] : '';

		?>
		<li class="available-template<?php if($data['update'] ) echo ' update'; ?><?php if(!empty($data['is_feature'])) echo ' is-feature'; ?><?php if(!empty($data['is_free'])) echo ' is-free'; ?>" id="template-<?php echo $slug ?>" data-id="<?php echo $slug?>">
			<a class="external screenshot" title="<?php echo $data['name'].' '.$data['new_version'].' '.__('by', 'mymail').' '.$data['author'] ?>" <?php echo !empty($data['uri']) ? 'href="'.esc_url(add_query_arg(array( 'utm_source' => 'MyMail+Templates+Page' ), $data['uri'] )).'" ' : '' ?> data-slug="<?php echo $slug ?>">
				<img alt="" src="<?php echo $data['image']?>" width="300" height="225">
			</a>
			<div class="meta">
			<h3><?php echo $data['name'] ?> <span class="version"><?php echo $data['new_version'] ?> <span class="installed-version">(<?php _e('your version', 'mymail' ); ?>: <?php echo $data['version'] ?>)</span></span></h3><div> <?php _e('by', 'mymail'); ?> <?php if(!empty($data['author_profile'])) : ?><a href="<?php echo $data['author_profile']?>"><?php echo $data['author']?></a><?php else : ?> <?php echo $data['author']?><?php endif; ?></div>
			</div>
			<div class="description">
			<?php if(isset($data['description'])) : ?><p><?php echo $data['description']?></p><?php endif; ?>
			</div>
			<div class="licensecode">
				<form action="edit.php?post_type=newsletter&page=mymail_templates&more" method="get">
				<input type="hidden" name="post_type" = value="newsletter">
				<input type="hidden" name="page" value="mymail_templates">
				<input type="hidden" name="more" value="1">
				<input type="hidden" name="action" value="license">
				<input type="hidden" name="template" value="<?php echo $slug ?>">
				<input type="text" name="license" class="widefat license" value="" placeholder="<?php _e('Enter Licensecode', 'mymail') ?>">
				<?php wp_nonce_field( 'license-'.$slug, '_wpnonce', false ); ?>
				<input type="submit" class="button save-license" value="<?php _e('save', 'mymail') ?>">
				</form>
			</div>
			<div class="action-links">
				<ul>
					<?php if(!empty($data['is_free']) || !empty($licensecode)) : ?>

						<?php  if(empty($data['is_free'])) : ?>
						<li><a title="<?php _e('activate with licensecode', 'mymail' ); ?>" class="activate button" href="edit.php?post_type=newsletter&page=mymail_templates&action=license&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('license-'.$slug)?>" data-slug="<?php echo $slug ?>" data-license="<?php echo $licensecode ?>"> <?php _e('Change Code', 'mymail'); ?></a></li>
						<?php endif; ?>
						<?php if(in_array($slug, array_keys($templates))) : ?>
							<li class="alignright"><a title="<?php _e('update template', 'mymail' ); ?>" class="update button button-primary" href="edit.php?post_type=newsletter&page=mymail_templates&action=update&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('download-'.$slug)?>"><?php if($data['update'] && $updates){ echo sprintf(__('Update to %s', 'mymail'), $data['new_version']); } else { _e('Download', 'mymail'); }?></a></li>
						<?php else : ?>
							<li class="alignright"><a title="<?php _e('download template', 'mymail' ); ?>" class="download button button-primary" href="edit.php?post_type=newsletter&page=mymail_templates&action=download&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('download-'.$slug)?>"><?php _e('Download', 'mymail'); ?></a></li>

						<?php endif; ?>

					<?php elseif(isset($data['uri'])) : ?>

						<?php if(isset($data['envato_item_id'])) : ?>
							<?php if(!isset($templates[$slug])) : ?>
							<li><a title="<?php _e('download via Envato', 'mymail' ); ?>" class="envato-activate button" href="<?php echo add_query_arg(array('auth' => wp_create_nonce( 'envato-activate' ), 'item_id' => $data['envato_item_id'], 'slug' => $slug, 'returnto' => urlencode(admin_url('edit.php?post_type=newsletter&page=mymail_templates'))), $data['endpoint']) ?>" data-slug="<?php echo $slug ?>"><?php _e('Download', 'mymail'); ?></a></li>
							<?php endif; ?>
						<?php else : ?>
						<li><a title="<?php _e('activate with licensecode', 'mymail' ); ?>" class="activate button" href="edit.php?post_type=newsletter&page=mymail_templates&action=license&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('license-'.$slug)?>" data-slug="<?php echo $slug ?>"><?php _e('Activate', 'mymail'); ?></a></li>
						<?php endif; ?>

						<?php if(isset($data['envato_item_id']) && isset($templates[$slug])) : ?>
						<li class="alignright"><a title="<?php _e('activate on Envato', 'mymail' ); ?>" class="envato-activate update button button-primary" href="<?php echo add_query_arg(array('auth' => wp_create_nonce( 'envato-activate' ), 'item_id' => $data['envato_item_id'], 'slug' => $slug, 'returnto' => urlencode(admin_url('edit.php?post_type=newsletter&page=mymail_templates'))), $data['endpoint']) ?>" data-slug="<?php echo $slug ?>"><?php if($data['update'] && $updates){ echo sprintf(__('Update to %s', 'mymail'), $data['new_version']); } else { _e('Download', 'mymail'); }?></a></li>
						<?php else : ?>
						<li class="alignright"><a class="external purchase button button-primary" href="<?php echo esc_url(add_query_arg(array( 'utm_source' => 'MyMail+Templates+Page' ), $data['uri'] )); ?>"><?php _e('get this template', 'mymail'); ?></a></li>
						<?php endif; ?>

					<?php endif; ?>
				</ul>
			</div>
			<div class="loader"></div>
		</li>
		<?php
	}


	endif;

?>
</ul>
<?php endif; ?>
<div id="thickboxbox">
	<ul class="thickbox-filelist"></ul>
	<iframe class="thickbox-iframe" src=""></iframe>
</div>
<div id="ajax-response"></div>
<br class="clear">
</div>
