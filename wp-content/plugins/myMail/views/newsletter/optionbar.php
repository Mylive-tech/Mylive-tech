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
	</ul>
	<ul class="alignright">
		<li><a class="mymail-icon dfw" title="<?php _e('Distraction-free edit mode', 'mymail') ?>">&nbsp;</a></li>
		<?php if($templates && current_user_can('mymail_save_template')) : ?>
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
							<li><?php if(!$single): ?><a class="template"><?php echo $data['name'] ?><i class="version"><?php echo $data['version']; ?></i></a><?php endif; ?>
								<ul <?php if($this->get_template() == $slug) echo ' style="display:block"'?>>
						<?php
							foreach($all_files[$slug] as $name => $data){
								$value = $slug.'/'.$name;
							?>
								<li><a class="file<?php if($current == $value) echo ' active';?>" <?php if($current != $value) echo 'href="//'.add_query_arg( array( 'template' => $slug, 'file' => $name, 'message' => 2), $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]).'"';?>><?php echo $data['label']; ?></a></li>
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
