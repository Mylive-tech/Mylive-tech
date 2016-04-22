<?php

	$t = mymail('templates');
	$templates = $t->get_templates();
	$other_templates = $t->get_other_templates();

	$notice = false;

?>
<div class="wrap">
<div class="icon32" id="icon-edit"><br></div>
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

		<div class="nav-tab-wrapper">
		</div>
		<div class="inner">
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
<h2><?php _e('Templates', 'mymail') ?> <a href="http://rxa.li/mymailtemplates" class="add-new-h2"> <?php _e('more templates', 'mymail'); ?> </a></h2>
<?php
	if($updates = $t->get_updates()){
		echo '<div class="updated below-h2"><p>'.sprintf( _n( '%d Update available', '%d Updates available', $updates, 'mymail'), $updates).'</p></div>';
	}
wp_nonce_field('mymail_nonce');
if($notice){
	foreach($notice as $note){?>
<div class="updated below-h2"><p><?php echo $note ?></p></div>
<?php }
}?>
<ul id="available-templates">

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

		$update = isset($other_templates[$slug]) && $other_templates[$slug]['update'] && current_user_can('mymail_update_templates');
		$licensecode = isset($other_templates[$slug]) ? $other_templates[$slug]['licensecode'] : '';

		?>
		<li class="available-template<?php if($update ) echo ' update'; ?><?php if($default == $slug) echo ' is-default'; ?><?php if($new == $slug) echo ' is-new'; ?>" id="template-<?php echo $slug ?>" name="mymail_template_<?php echo $i ?>" data-id="<?php echo $i++?>">
			<?php if(isset($updates[$slug])){?>
				<span class="update-badge"><?php echo $updates[$slug]?></span>
			<?php }?>
			<a class="thickbox-preview screenshot" title="<?php echo $data['name'].' '.$data['version']?> <?php _e('by', 'mymail'); ?> <?php echo $data['author']?>" href="<?php echo $t->url .'/' .$slug .'/index.html'?>" data-slug="<?php echo $slug ?>">
				<img alt="<?php _e('Screenshot', 'mymail'); ?>" src="<?php echo $t->get_screenshot($slug)?>" width="300" height="225">
			</a>
			<h3><?php echo $data['name'] ?> <span class="version"><?php echo $data['version'] ?></span>
				<?php if($update){
					if(empty($licensecode)) { ?>
					<a title="<?php _e('activate with licensecode', 'mymail' ); ?>" class="activate button alignright" href="edit.php?post_type=newsletter&page=mymail_templates&action=license&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('license-'.$slug)?>" data-license="<?php echo $licensecode ?>"><?php _e('Activate', 'mymail'); ?></a>
				<?php }else{?>
					<a title="<?php _e('update template', 'mymail' ); ?>" class="update button button-primary alignright" href="edit.php?post_type=newsletter&page=mymail_templates&action=update&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('download-'.$slug)?>" data-license="<?php echo $licensecode ?>"><?php echo sprintf(__('Update to %s', 'mymail'), $other_templates[$slug]['new_version']); ?></a>
				<?php }} ?>

			</h3><div> <?php _e('by', 'mymail'); ?> <?php if(!empty($data['author_uri'])) : ?><a href="<?php echo $data['author_uri']?>"><?php echo $data['author']?></a><?php else : ?> <?php echo $data['author']?><?php endif; ?></div>
			<?php if(isset($data['description'])) : ?><p class="description"><?php echo $data['description']?></p><?php endif; ?>
			<div class="action-links">
				<ul>
					<?php if($default != $slug) : ?>
					<li><a title="Set &quot;<?php echo $data['name'] ?>&quot; as default" class="activatelink button" href="edit.php?post_type=newsletter&amp;page=mymail_templates&amp;action=activate&amp;template=<?php echo $slug?>&amp;_wpnonce=<?php echo wp_create_nonce('activate-'.$slug)?>"><?php _e('Use as default', 'mymail'); ?></a></li>
					<?php endif; ?>
				 	<?php if(current_user_can('mymail_edit_templates')){
						$writeable = is_writeable($t->path .'/'.$slug .'/index.html');
				 	?>

					<li><a title="Edit &quot;<?php echo $data['name'] ?>&quot;" class="edit <?php echo (!$writeable ? 'disabled' : '')?> button" data-slug="<?php echo $slug?>" href="<?php echo $slug .'/index.html'?>" <?php if(!$writeable) :?>onclick="alert('<?php _e('This file is not writeable! Please change the file permission', 'mymail'); ?>');return false;"<?php endif; ?>><?php _e('Edit HTML', 'mymail') ?></a></li>
					<?php }?>
				</ul>
				<?php if($slug != mymail_option('default_template') && current_user_can('mymail_delete_templates')) { ?>
					<div class="delete-theme">
						<a onclick="return confirm(<?php echo "'".esc_html(sprintf(__('You are about to delete this template "%s"', 'mymail'), $data['name']))."'" ?> );" href="edit.php?post_type=newsletter&amp;page=mymail_templates&amp;action=delete&amp;template=<?php echo $slug?>&amp;_wpnonce=<?php echo wp_create_nonce('delete-'.$slug)?>" class="submitdelete deletion">Delete</a>
					</div>
			<?php }?>
			</div>
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

<h2><?php _e('more Templates', 'mymail') ?> <a href="edit.php?post_type=newsletter&page=mymail_templates" class="add-new-h2"> <?php _e('back to overview', 'mymail'); ?> </a></h2>
<ul id="available-templates">
<?php

	if($updates = $t->get_updates()){
		echo '<div class="updated below-h2"><p>'.sprintf( _n( '%d Update available', '%d Updates available', $updates, 'mymail'), $updates).'</p></div>';
	}

	$i = 0;

	foreach($other_templates as $slug => $data){
		?>
		<li class="available-template<?php if($data['update'] ) echo ' update'; ?><?php if(!empty($data['is_feature'])) echo ' is-feature'; ?><?php if(!empty($data['is_free'])) echo ' is-free'; ?>" id="mymail_template_<?php echo $i?>" data-id="<?php echo $i++?>">
			<a class="thickbox-preview screenshot" title="<?php echo $data['name'].' '.$data['version']?> <?php _e('by', 'mymail'); ?> <?php echo $data['author']?>" href="<?php echo $t->url .'/' .$slug .'/index.html'?>" data-slug="<?php echo $slug ?>">
				<img alt="" src="<?php echo $data['image']?>" width="300" height="225">
			</a>
			<h3><?php echo $data['name'] ?> <span class="version"><?php echo $data['new_version'] ?></span></h3><div> <?php _e('by', 'mymail'); ?> <?php if(!empty($data['author_uri'])) : ?><a href="<?php echo $data['author_uri']?>"><?php echo $data['author']?></a><?php else : ?> <?php echo $data['author']?><?php endif; ?></div>
			<?php if(isset($data['description'])) : ?><p class="description"><?php echo $data['description']?></p><?php endif; ?>
			<div class="action-links">
				<ul>
					<?php if(!empty($data['is_free']) || !empty($data['licensecode'])) : ?>
						<?php  if(empty($data['is_free'])) : ?>
						<li><a title="<?php _e('activate with licensecode', 'mymail' ); ?>" class="activate button" href="edit.php?post_type=newsletter&page=mymail_templates&action=license&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('license-'.$slug)?>" data-license="<?php echo $data['licensecode'] ?>"><?php _e('Change Code', 'mymail'); ?></a></li>
						<?php endif; ?>
						<?php if(in_array($slug, array_keys($templates))) : ?>
							<li class="alignright"><a title="<?php _e('update template', 'mymail' ); ?>" class="update button button-primary" href="edit.php?post_type=newsletter&page=mymail_templates&action=update&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('download-'.$slug)?>"><?php if($data['update'] && $updates){ echo sprintf(__('Update to %s', 'mymail'), $data['new_version']); } else { _e('Redownload', 'mymail'); }?></a></li>
						<?php else : ?>
							<li class="alignright"><a title="<?php _e('download template', 'mymail' ); ?>" class="download button button-primary" href="edit.php?post_type=newsletter&page=mymail_templates&action=download&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('download-'.$slug)?>"><?php _e('Download', 'mymail'); ?></a></li>
						<?php endif; ?>
					<?php elseif(isset($data['uri'])) : ?>
					<li><a title="<?php _e('activate with licensecode', 'mymail' ); ?>" class="activate button" href="edit.php?post_type=newsletter&page=mymail_templates&action=license&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce('license-'.$slug)?>"><?php _e('Activate', 'mymail'); ?></a></li>
					<li class="alignright"><a title="" class="purchase button button-primary" href="<?php echo $data['uri'] ?>" target="_blank"><?php _e('get this template', 'mymail'); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</li>
		<?php
	}

?>
</ul>
<?php endif; ?>
<div id="thickboxbox"><div>
<ul class="thickbox-filelist">
</ul>
<iframe class="thickbox-iframe" src=""></iframe></div></div>
<div id="ajax-response"></div>
<br class="clear">
</div>
