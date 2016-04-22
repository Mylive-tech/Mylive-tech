<?php
	$editable = !in_array($post->post_status, array('active', 'finished'));
	if(isset($_GET['showstats']) && $_GET['showstats']) $editable = false;
?>
<?php if($editable) :?>

	<span class="spinner" id="colorschema-ajax-loading"></span>
	<p><label><input name="mymail_data[embed_images]" id="mymail_data_embed_images" value="1" type="checkbox" <?php echo (isset($this->post_data['embed_images'])) ? (($this->post_data['embed_images']) ? 'checked' : '') :  (mymail_option('embed_images') ? 'checked' : '') ?> <?php echo ($editable) ? 'disabled' : '' ?>> <?php _e('Embed Images', 'mymail') ?></label></p>
	<label><?php _e('Colors', 'mymail'); ?></label> <a class="savecolorschema"><?php _e('save this schema', 'mymail') ?></a>

	<?php

		$html = $this->templateobj->get(true);
		$colors = array();
		preg_match_all('/#[a-fA-F0-9]{6}/', $html, $hits);
		$original_colors = array_keys(array_count_values($hits[0]));
		$original_names = array();

		foreach($original_colors as $i => $color){
			preg_match('/'.$color.'\/\*([^*]+)\*\//', $html, $x);
			$original_names[$i] = isset($x[1]) ? $x[1] : '';
		}
	?>
	<ul class="colors<?php if(count(array_count_values($original_names)) > 1) echo ' has-labels'; ?>" data-original-colors='<?php echo json_encode($original_colors) ?>'>
	<?php


		$html = $post->post_content;

		if(!empty($html) && isset($this->post_data['template']) && $this->post_data['template'] == $this->get_template() && $this->post_data['file'] == $this->get_file()){
			preg_match_all('/#[a-fA-F0-9]{6}/', $html, $hits);
			$current_colors = array_keys(array_count_values($hits[0]));
		}else{
			$current_colors = $original_colors;
		}
		foreach($current_colors as $i => $color){
			$value = strtoupper($color);
			$colors[] = $value;


		?>
		<li class="mymail-color" id="mymail-color-<?php echo strtolower(substr($value,1)) ?>"><label title="<?php echo isset($original_names[$i]) ? $original_names[$i] : '' ?>"><?php echo isset($original_names[$i]) ? $original_names[$i] : '' ?></label><input type="text" class="form-input-tip color" name="mymail_data[newsletter_color][<?php echo $color?>]"  value="<?php echo $value ?>" data-value="<?php echo $value ?>" data-default-color="<?php echo $value ?>"> <a class="default-value mymail-icon" href="#" tabindex="-1"></a></li>
		<?php
		}
	?>
	</ul>
	<div class="clear"></div>
	<p>
	<label><?php _e('Colors Schemas', 'mymail'); ?></label>
	<?php
		$customcolors = get_option('mymail_colors');
		if(isset($customcolors[$this->get_template()])) :
	?>
	<a class="colorschema-delete-all"><?php _e('delete all custom schemas', 'mymail') ?></a>
	<?php endif; ?>
	</p>
	<ul class="colorschema" title="<?php _e('original', 'mymail')?>">
	<?php
		$original_colors_temp = array();
		foreach($original_colors as $i => $color){
			$color = strtolower($color);
			$original_colors_temp[] = $color;
		?>
		<li class="colorschema-field" title="<?php echo isset($original_names[$i]) ? $original_names[$i] : '' ?>" data-hex="<?php echo $color?>" style="background-color:<?php echo $color?>"></li>
		<?php
		}
	?>
	</ul>
	<?php
	if(strtolower(implode('',$original_colors_temp)) != strtolower(implode('',$current_colors))) :?>
	<ul class="colorschema" title="<?php _e('current', 'mymail')?>">
	<?php
		foreach($colors as $i => $color){
		?>
		<li class="colorschema-field" title="<?php echo isset($original_names[$i]) ? $original_names[$i] : '' ?>" data-hex="<?php echo strtolower($color)?>" style="background-color:<?php echo $color?>"></li>
		<?php
		}
	?>
	</ul>
	<?php
	endif;

	if(isset($customcolors[$this->get_template()])){
		foreach($customcolors[$this->get_template()] as $hash => $colorschema){
		?>
		<ul class="colorschema custom" data-hash="<?php echo $hash?>">
		<?php
			foreach($colorschema as $i => $color){
			?>
			<li class="colorschema-field" title="<?php echo isset($original_names[$i]) ? $original_names[$i] : '' ?>" data-hex="<?php echo strtolower($color)?>" style="background-color:<?php echo $color?>"></li>
			<?php
			}
		?>
			<li class="colorschema-delete-field"><a class="colorschema-delete">&#10005;</a></li>
		</ul>
		<?php
		}
	}

	?>
	<hr>
	<label><?php _e('Background', 'mymail') ?></label><br>
	<?php
		$value = (isset($this->post_data['background']) && $this->post_data['template'] == $this->get_template()) ? $this->post_data['background'] : '';
	?>
	<input type="hidden" id="mymail_background" name="mymail_data[background]" value="<?php echo $value ?>">
	<ul class="backgrounds">
		<li><a style="background-image:<?php echo ($value == 'none' || empty($value)) ? 'none' : 'url('.$value.')'?>"></a>
		<?php

			$custombgs = MYMAIL_UPLOAD_DIR.'/backgrounds';
			$custombgsuri = MYMAIL_UPLOAD_URI.'/backgrounds';

			if(!is_dir($custombgs)) wp_mkdir_p($custombgs);

			if($files = list_files($custombgs)){

		?>
			<ul data-base="<?php echo $custombgsuri?>">
		<?php
			sort($files);
			foreach($files as $file){
				if(!in_array(strrchr($file, '.'), array('.png', '.gif', '.jpg', '.jpeg'))) continue;
				$value = (isset($this->post_data['background'])) ? $this->post_data['background'] : false;
				$file = str_replace($custombgs,'', $file);
				?>
				<li><a title="<?php echo basename($file);?>" data-file="<?php echo $file?>" style="background-image:url(<?php echo $custombgsuri.$file?>)"<?php if($custombgsuri.$file == $value) echo ' class="active"';?>>&nbsp;</a></li>
				<?php
			}
			?>
			</ul>
		<?php
			}
		?>
			<ul data-base="<?php echo MYMAIL_URI?>">
				<li><a title="<?php _e('none', 'mymail') ?>" data-file="" <?php if(!$value) echo ' class="active"';?>><?php _e('none', 'mymail') ?></a></li>
		<?php
			$files = list_files(MYMAIL_DIR . 'assets/img/bg');
			sort($files);
			foreach($files as $file){
				if(!in_array(strrchr($file, '.'), array('.png', '.gif', '.jpg', '.jpeg'))) continue;
				$value = (isset($this->post_data['background'])) ? $this->post_data['background'] : false;
				$file = str_replace(MYMAIL_DIR,'', $file);
				?>
				<li><a title="<?php echo basename($file);?>" data-file="<?php echo $file?>" style="background-image:url(<?php echo MYMAIL_URI.$file?>)"<?php if(MYMAIL_URI.$file == $value) echo ' class="active"';?>>&nbsp;</a></li>
				<?php
			}
		?>
			</ul>
		</li>

	</ul>
	<p class="howto tiny"><?php _e('background images are not displayed on all clients!', 'mymail') ?></p>

<?php else : ?>

	<p><?php if($this->post_data['embed_images']){?>&#10004;<?php }else{ ?>&#10005;<?php }?> <?php _e('Embedded Images', 'mymail') ?></p>
	<label><?php _e('Colors Schema', 'mymail') ?></label><br>
	<ul class="colorschema finished">
	<?php
		$colors = $this->post_data['colors'];
		foreach($colors as $color){
		?>
		<li data-hex="<?php echo $color?>" style="background-color:<?php echo $color?>"></li>
		<?php
		}
	?>
	</ul>
	<?php if($this->post_data['background']){
		$file = $this->post_data['background'];
	?>
	<hr>
	<label><?php _e('Background', 'mymail') ?></label><br>
	<ul class="backgrounds finished">
		<li><a title="<?php echo basename($file);?>" style="background-image:url(<?php echo $file?>)"></a></li>
	</ul>
	<?php } ?>

<?php endif; ?>
