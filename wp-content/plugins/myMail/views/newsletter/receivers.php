<?php
	$editable = !in_array($post->post_status, array('active', 'finished'));

	if(isset($_GET['showstats']) && $_GET['showstats']) $editable = false;

	$listdata = wp_parse_args($this->post_data['list_conditions'], array('operator' => 'OR'));
	$ignore_lists = isset($this->post_data['ignore_lists']) ? !!$this->post_data['ignore_lists'] : false;

 if($editable):

 	$total = $this->get_totals($post->ID);

	?>
	<ul class="category-tabs">
		<li class="tabs"><?php _e('send campaign to these lists', 'mymail'); ?></li>
	</ul>
	<div class="categorydivs tabs-panel">

		<div id="list-checkboxes" <?php if($ignore_lists) echo ' style="display:none"' ?>>
		<label><input type="checkbox" id="all_lists"> <?php _e('toggle all', 'mymail'); ?></label>
			<div>
				<ul class="default">
					<?php
						$checked = wp_parse_args( isset($_GET['lists']) ? $_GET['lists'] : array(), $this->post_data['lists'] );

						mymail('lists')->print_it(NULL, NULL, 'mymail_data[lists]', true, $checked);

					 ?>
				</ul>

			</div>
		</div>
		<ul>
			<li><label><input id="ignore_lists" type="checkbox" name="mymail_data[ignore_lists]" value="1" <?php checked($ignore_lists) ?>> <?php _e("List doesn't matter", 'mymail'); ?> </label></li>
		</ul>
		<label><input type="checkbox" name="mymail_data[list_conditions]" id="list_extra" value="1" <?php checked(isset($listdata['conditions'])) ?>> <?php _e('only if', 'mymail'); ?></label>
		<div id="mymail_list_advanced" <?php if(!isset($listdata['conditions'])) { echo 'style="display:none"';}?>>
			<p>
			<select id="mymail_list_operator" class="widefat" name="mymail_data[list][operator]">
				<option value="OR"<?php selected($listdata['operator'], 'OR')?> title="<?php _e('or', 'mymail'); ?>"><?php _e('one of the conditions is true', 'mymail'); ?></option>
				<option value="AND"<?php selected($listdata['operator'], 'AND')?> title="<?php _e('and', 'mymail'); ?>"><?php _e('all of the conditions are true', 'mymail'); ?></option>
			</select>
			</p>
			<div id="mymail_list_conditions" class="operator-is-<?php echo $listdata['operator']?>">
			<?php

				if(!isset($listdata['conditions'])) $listdata['conditions'] = array(
					array(
						'field' => '',
						'operator' => '',
						'value' => '',
					)
				);

				$fields = array(
					'email' => mymail_text('email'),
					'firstname' => mymail_text('firstname'),
					'lastname' => mymail_text('lastname'),
					'rating' => __('Rating', 'mymail'),
				);

				$customfields = mymail()->get_custom_fields();

				$meta = array(
					'form' => __('Form ID', 'mymail'),
					'referer' => __('Referer', 'mymail'),
					'ip' => __('IP Address', 'mymail'),
					'signup' => __('Signup Date', 'mymail'),
					'ip_signup' => __('Signup IP', 'mymail'),
					'confirm' => __('Confirm Date', 'mymail'),
					'ip_confirm' => __('Confirm IP', 'mymail'),
				);

				$wp_meta = wp_parse_args(mymail('helper')->get_wpuser_meta_fields(), array(
					'wp_capabilities' => __('User Role', 'mymail'),
					'wp_user_level' => __('User Level', 'mymail'),
				));

				$operators = array(
					'is' => __('is', 'mymail'),
					'is_not' => __('is not', 'mymail'),
					'contains' => __('contains', 'mymail'),
					'contains_not' => __('contains not', 'mymail'),
					'begin_with' => __('begins with', 'mymail'),
					'end_with' => __('ends with', 'mymail'),
					'is_greater' => __('is greater', 'mymail'),
					'is_smaller' => __('is smaller', 'mymail'),
					'is_greater_equal' => __('is greater or equal', 'mymail'),
					'is_smaller_equal' => __('is smaller or equal', 'mymail'),
					'pattern' => __('match regex pattern', 'mymail'),
					'not_pattern' => __('does not match regex pattern', 'mymail'),
				);

				foreach($listdata['conditions'] as $i => $condition){
					if(!isset($condition['field'])) $condition['field'] = '';
					if(!isset($condition['operator'])) $condition['operator'] = '';
					?>
			<div class="mymail_list_condition" id="mymail_list_condition_<?php echo $i;?>">
				<div class="mymail-list-operator">
					<span class="operator-and"><?php _e('and', 'mymail' ); ?></span>
					<span class="operator-or"><?php _e('or', 'mymail' ); ?></span>
				</div>
				<div><a class="remove-condition" title="<?php _e('remove condition', 'mymail'); ?>">&#10005;</a></div>
				<select name="mymail_data[list][conditions][<?php echo $i;?>][field]" class="condition-field">
				<?php foreach( $fields as $value => $name ){
					echo '<option value="'.$value.'"'.selected($condition['field'], $value, false).'>'.$name.'</option>';
				}?>
				<optgroup label="<?php _e('Custom Fields', 'mymail' ); ?>">
				<?php foreach( $customfields as $value => $customfield ){
					echo '<option value="'.$value.'"'.selected($condition['field'], $value, false).'>'.$customfield['name'].'</option>';
				}?>
				</optgroup>
				<optgroup label="<?php _e('Meta Data', 'mymail' ); ?>">
				<?php foreach( $meta as $value => $name ){
					echo '<option value="'.$value.'"'.selected($condition['field'], $value, false).'>'.$name.'</option>';
				}?>
				</optgroup>
				<optgroup label="<?php _e('WordPress User Meta', 'mymail' ); ?>">
				<?php foreach( $wp_meta as $value => $name ){
					if(is_integer($value)) $value = $name;
					echo '<option value="'.$value.'"'.selected($condition['field'], $value, false).'>'.$name.'</option>';
				}?>
				</optgroup>
				</select>
				<select name="mymail_data[list][conditions][<?php echo $i;?>][operator]">
				<?php foreach( $operators as $value => $name ){
					echo '<option value="'.$value.'"'.selected($condition['operator'], $value, false).'>'.$name.'</option>';
				}?>
				</select><br>
				<input type="text" class="widefat" name="mymail_data[list][conditions][<?php echo $i;?>][value]" value="<?php echo esc_attr($condition['value']) ?>">
			</div>
					<?php
				}
			?>
			</div>
			 <a class="add-condition" title="<?php _e('add condition', 'mymail'); ?>"><?php _e('add condition', 'mymail'); ?></a>
	 	</div>
	</div>
		<p class="totals"><?php _e('Total receivers', 'mymail'); ?>: <span id="mymail_total"><?php echo number_format_i18n($total) ?></span></p>

<?php else :

		?><div><p class="lists"><?php

		$meta = $this->meta($post->ID);

		if($meta['ignore_lists']) :

			_e("Any List", 'mymail');

		else :

			$lists = $this->get_lists($post->ID);

			if(!empty($lists)) :

				echo __('Lists', 'mymail').':<br>';
				foreach($lists as $list){
					echo ' <strong><a href="edit.php?post_type=newsletter&page=mymail_lists&ID='.$list->ID.'">'.$list->name.'</a></strong>';
				}

			else :

				_e('no lists selected', 'mymail');

			endif;

		endif;
		?></p><?php
		if(isset($listdata['conditions'])){
			$fields = array(
				'email' => mymail_text('email'),
				'firstname' => mymail_text('firstname'),
				'lastname' => mymail_text('lastname'),
			);

			$customfields = mymail()->get_custom_fields();

			foreach ($customfields as $field => $data) {
				$fields[$field] = $data['name'];
			}

			$meta = array(
				'form' => __('Form ID', 'mymail'),
				'referer' => __('Referer', 'mymail'),
				'ip' => __('IP Address', 'mymail'),
				'signup' => __('Signup Date', 'mymail'),
				'ip_signup' => __('Signup IP', 'mymail'),
				'confirm' => __('Confirm Date', 'mymail'),
				'ip_confirm' => __('Confirm IP', 'mymail'),
				'rating' => __('Rating', 'mymail'),
			);

			foreach ($meta as $field => $name) {
				$fields[$field] = $name;
			}

			echo '<p>'.__('only if', 'mymail').'<br>';

			$conditions = array();
			$operators = array(
				'is' => __('is', 'mymail'),
				'is_not' => __('is not', 'mymail'),
				'contains' => __('contains', 'mymail'),
				'contains_not' => __('contains not', 'mymail'),
				'begin_with' => __('begins with', 'mymail'),
				'end_with' => __('ends with', 'mymail'),
				'is_greater' => __('is greater', 'mymail'),
				'is_smaller' => __('is smaller', 'mymail'),
				'is_greater_equal' => __('is greater or equal', 'mymail'),
				'is_smaller_equal' => __('is smaller or equal', 'mymail'),
				'pattern' => __('match regex pattern', 'mymail'),
				'not_pattern' => __('does not match regex pattern', 'mymail'),
			);

			foreach($listdata['conditions'] as $condition){
				if(!isset($fields[$condition['field']])){
					echo '<span class="mymail-icon warning"></span> '.sprintf(__('%s is missing!', 'mymail'), '"'.$condition['field'].'"').'<br>';
					continue;
				}
				$conditions[] = '<strong>'.$fields[$condition['field']].'</strong> '.$operators[$condition['operator']].' "<strong>'.$condition['value'].'</strong>"';
			}

			echo implode('<br>'.__(strtolower($listdata['operator']), 'mymail').' ', $conditions).'</p>';

		}
		?></div><?php

	if($post->post_status != 'autoresponder' && current_user_can('mymail_edit_lists')) :
		?>

		<a class="create-new-list button" href="#"><?php _e('create new list', 'mymail');?></a>
		<div class="create-new-list-wrap">
		<h4><?php _e('create a new list with all', 'mymail');?></h4>
		<p>
		<select class="create-list-type">
		<?php
		$options = array(
			'sent' => __('who have received', 'mymail'),
			'not_sent' => __('who have not received', 'mymail'),
			'open' => __('who have opened', 'mymail'),
			'open_not_click' => __('who have opened but not clicked', 'mymail'),
			'click' => __('who have opened and clicked', 'mymail'),
			'not_open' => __('who have not opened', 'mymail'),
		);
		foreach($options as $id => $option){ ?>
			<option value="<?php echo $id?>"><?php echo $option ?></option>
		<?php } ?>

		</select>
		</p>
		<p><a class="create-list button"><?php _e('create list', 'mymail'); ?></a>
		</p>
		<p class="totals"><?php _e('Total receivers', 'mymail'); ?>: <span id="mymail_total">-</span></p>
		</div>
		<?php
	 endif;
 endif;?>
