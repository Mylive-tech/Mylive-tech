	<?php

	$now = time();

	$editable = !in_array($post->post_status, array('active', 'finished'));
	if(isset($_GET['showstats']) && $_GET['showstats']) $editable = false;

	$is_autoresponder = $post->post_status == 'autoresponder';

	$timestamp = (!empty($this->post_data['timestamp'])) ? $this->post_data['timestamp'] : $now+(60*mymail_option('send_offset'));

	$timestamp = (!$this->post_data['active']) ? max($now+(60*mymail_option('send_offset')),$timestamp) : $timestamp;

	$timeformat = get_option('date_format').' '.get_option('time_format');
	$timeoffset = mymail('helper')->gmt_offset(true);

	$current_user = wp_get_current_user();

	$sent = $this->get_sent($post->ID);

?>
<?php if($editable) : ?>

<?php if(current_user_can('mymail_edit_autoresponders')) :?>
<ul class="category-tabs">
	<li class="<?php if(!$is_autoresponder) echo 'tabs';?>"><a href="#regular-campaign"><?php _e('Regular Campaign', 'mymail'); ?></a></li>
	<li class="<?php if($is_autoresponder) echo 'tabs';?>"><a href="#autoresponder"><?php _e('Auto Responder', 'mymail'); ?></a></li>
</ul>
<div id="regular-campaign" class="tabs-panel"<?php if($is_autoresponder) echo ' style="display:none"';?>>
<?php endif; ?>
<p class="howto" title="<?php echo date($timeformat, $now)?>">
<?php

	echo sprintf( __("Server time: %s %s" ,'mymail'), '<span title="'.date($timeformat, $now+$timeoffset).'">'.date('Y-m-d', $now+$timeoffset).'</span>', '<span class="time" data-timestamp="'.($now+$timeoffset).'">'.date('H:i:s', $now+$timeoffset).'</span>'  );

	elseif ($post->post_status == 'finished') :

	echo sprintf( __("This campaign has been sent on %s. You cannot edit it anymore" ,'mymail'), '<strong>'.date($timeformat, $this->post_data['finished']+$timeoffset).'</strong>' );

	endif;

?>
</p>
<?php if($editable) : ?>
<label>
	<input name="mymail_data[active]" id="mymail_data_active" value="1" type="checkbox" <?php echo ($this->post_data['active'] && !$is_autoresponder) ? 'checked' : '' ?> <?php echo (!$editable) ? ' disabled' : '' ?>> <?php _e('send this campaign', 'mymail') ?>
</label>

<div class="active_wrap <?php if($this->post_data['timezone']) echo ' timezone-enabled'; if($this->post_data['active'] && !$is_autoresponder) echo ' disabled'; ?>">
	<div class="active_overlay"></div>
	<?php echo sprintf( _x('on %1$s @ %2$s','send campaign "on" (date) "at" (time)', 'mymail'),
		'<input name="mymail_data[date]" class="datepicker deliverydate inactive" type="text" value="'.date('Y-m-d', $timestamp+$timeoffset).'" maxlength="10" readonly'.(((!$this->post_data['active'] && !$is_autoresponder) || $editable) ? ' disabled' : '').'>',
		'<input name="mymail_data[time]" maxlength="5" class="deliverytime inactive" type="text" value="'.date('H:i', $timestamp+$timeoffset).'" '.(((!$this->post_data['active'] && !$is_autoresponder) || !$editable) ? ' disabled' : '').'> <span class="utcoffset">'.(($timeoffset > 0) ? 'UTC + '.($timeoffset/3600) : '').'</span>'
	);
	if(mymail_option('trackcities')) :
	?>
	<br><label title="<?php _e('Send this campaign based on the subscribers timezone if known', 'mymail') ?>"><input type="checkbox" class="timezone" name="mymail_data[timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php _e('Use Subscribers timezone', 'mymail') ?></label>
	<?php endif; ?>
</div>
<?php if($sent && !$is_autoresponder):

	$totals = $this->get_totals($post->ID);
	$p = round($this->get_sent_rate($post->ID)*100);

?>
	<p>
		<div class="progress paused"><span class="bar" style="width:<?php echo $p ?>%"></span><span>&nbsp;<?php echo sprintf(__('%1$s of %2$s sent', 'mymail'), number_format_i18n($sent), number_format_i18n($totals))?></span><var><?php echo $p ?>%</var></div>
	</p>
<?php endif;?>

<?php if(current_user_can('mymail_edit_autoresponders')) : ?>
</div>
<div id="autoresponder" class="tabs-panel"<?php if(!$is_autoresponder) echo ' style="display:none"';?>>
	<?php
		$autoresponderdata = wp_parse_args($this->post_data['autoresponder'], array(
			'operator' => '',
			'action' => 'mymail_subscriber_insert',
			'unit' => '',
			'before_after' => 1,
			'userunit' => 'day',
			'uservalue' => '',
			'userexactdate' => false,
			'timestamp' => $now,
			'endtimestamp' => $now,
			'weekdays' => array(),
			'post_type' => 'post',
			'time_post_type' => 'post',
			'time_post_count' => 1,
			'post_count' => 0,
			'post_count_status' => 0,
			'issue' => 1,
			'interval' => 1,
			'time_frame' => 'day',
			'timezone' => false,
			'hook' => '',
			'priority' => 10,
			'once' => false,
			'followup_action' => 1,
		));

		include_once MYMAIL_DIR . 'includes/autoresponder.php';
	?>
	<label>
		<input name="mymail_data[active_autoresponder]" id="mymail_data_autoresponder_active" value="1" type="checkbox" <?php checked(($this->post_data['active'] && $is_autoresponder), true) ?> <?php echo (!$editable) ? ' disabled' : '' ?>> <?php _e('send this auto responder', 'mymail') ?>
	</label>

	<div id="autoresponder_wrap" class="autoresponder-<?php echo $autoresponderdata['action'] ?>">
		<div class="autoresponder_active_wrap<?php  if($this->post_data['active'] && $is_autoresponder) echo ' disabled'; ?>">
			<div class="autoresponder_active_overlay"></div>
		<p class="autoresponder_time">
		<input type="text" class="small-text" name="mymail_data[autoresponder][amount]" value="<?php echo isset($autoresponderdata['amount']) ? $autoresponderdata['amount'] : 1?>">

			<select name="mymail_data[autoresponder][unit]">
			<?php
				foreach($mymail_autoresponder_info['units'] as $value => $name){
					echo '<option value="'.$value.'"'.selected($autoresponderdata['unit'], $value, false).'>'.$name.'</option>';
				}
			?>
			</select>
				<span class="autoresponder_after"><?php _e('after', 'mymail'); ?></span>
				<span class="autoresponder_before"><?php _e('before', 'mymail'); ?></span>
				<select class="autoresponder_before_after" name="mymail_data[autoresponder][before_after]">
					<option value="1" <?php selected($autoresponderdata['before_after'], 1); ?>><?php _e('after', 'mymail'); ?></option>
					<option value="-1" <?php selected($autoresponderdata['before_after'], -1); ?>><?php _e('before', 'mymail'); ?></option>
				</select>
		</p>
		<p>
		<select class="widefat" name="mymail_data[autoresponder][action]" id="mymail_autoresponder_action">
		<?php
			foreach($mymail_autoresponder_info['actions'] as $id => $action){
				echo '<option value="'.$id.'"'.selected($autoresponderdata['action'], $id, false).'>'.$action['label'].'</option>';
			}
		?>
		</select>
		</p>

		<div class="mymail_autoresponder_more autoresponderfield-mymail_subscriber_insert autoresponderfield-mymail_subscriber_unsubscribed">
			<p>
			<span class="mymail_autoresponder_more autoresponderfield-mymail_subscriber_insert">
			<?php _e('only for subscribers who signed up', 'mymail'); ?>
			</span>
			<span class="mymail_autoresponder_more autoresponderfield-mymail_subscriber_unsubscribed">
			<?php _e('only for subscribers who canceled their subscription', 'mymail'); ?>
			</span>
			<?php
				_e('after', 'mymail');
				$timestamp = $this->post_data['timestamp'] ? $this->post_data['timestamp'] : $now;

				echo sprintf( _x('%1$s @ %2$s','send campaign "on" (date) "at" (time)', 'mymail'),
		'<input name="mymail_data[autoresponder_signup_date]" class="datepicker deliverydate inactive nolimit" type="text" value="'.date('Y-m-d', $timestamp+$timeoffset).'" maxlength="10" readonly>',
		'<input name="mymail_data[autoresponder_signup_time]" maxlength="5" class="deliverytime inactive" type="text" value="'.date('H:i', $timestamp+$timeoffset).'"> <span class="utcoffset">UTC '.($timeoffset ? '+' : '').($timeoffset/3600).'</span>');
				?>

			</p>
		</div>
		<div class="mymail_autoresponder_more autoresponderfield-mymail_subscriber_unsubscribed">
			<p class="description">
			<?php _e('Keep in mind it is bad practice to send campaigns after subscribers opt-out so use this option for "Thank you" messages or surveys.', 'mymail'); ?>
			</p>
		</div>

			<?php $pts = get_post_types( array( 'public' => true ), 'object' ); ?>

		<div class="mymail_autoresponder_more autoresponderfield-mymail_post_published">
			<p>
			<?php
				$count = '<input type="number" name="mymail_data[autoresponder][post_count]" class="small-text" value="'.$autoresponderdata['post_count'].'">';
				$type = '<select id="autoresponder-post_type" name="mymail_data[autoresponder][post_type]">';
					foreach($pts as $pt => $data){
						if(in_array($pt, array('attachment', 'newsletter'))) continue;
						$type .= '<option value="'.$pt.'"'.selected($autoresponderdata['post_type'], $pt, false).'>'.$data->labels->singular_name.'</option>';
					}
				$type .= '</select>';
				echo sprintf( __('create a new campaign every time a new %s has been published', 'mymail'), $type );
			?>
			</p>
			<p>
			<?php
				if(mymail_option('trackcities')) :
				?>
				<label title="<?php _e('Send this campaign based on the subscribers timezone if known', 'mymail') ?>"><input type="checkbox" class="autoresponder-timezone" name="mymail_data[autoresponder][post_published_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php _e('Use Subscribers timezone', 'mymail') ?></label>
				<?php endif; ?>
			</p>

			<div id="autoresponderfield-mymail_post_published_advanced">
				<div id="autoresponder-taxonomies">
				<?php
				$taxes = mymail('helper')->get_post_term_dropdown($autoresponderdata['post_type'], false, true, isset($autoresponderdata['terms']) ? $autoresponderdata['terms'] : array());
				if($taxes){
					echo sprintf(__('only if in %s', 'mymail'), $taxes);
				}
				?>
				</div>
				<p>
				<?php
					echo sprintf( _n('always skip %s release', 'always skip %s releases', $autoresponderdata['post_count'], 'mymail'), $count );
					?>
				</p>
			</div>
		</div>

		<div class="mymail_autoresponder_more autoresponderfield-mymail_autoresponder_timebased<?php if(($this->post_data['timezone'])) echo ' timezone-enabled';?>">
			<p>
			<?php
				$timestamp = $this->post_data['timestamp'] ? $this->post_data['timestamp'] : $now;

				$interval = '<br><input type="number" name="mymail_data[autoresponder][interval]" class="small-text" value="'.$autoresponderdata['interval'].'">';
				$time_frame = '<select name="mymail_data[autoresponder][time_frame]">';
				$values = array(
					'hour' => __('hour(s)', 'mymail'),
					'day' => __('day(s)', 'mymail'),
					'week' => __('week(s)', 'mymail'),
					'month' => __('month(s)', 'mymail'),
				);
				foreach($values as $i => $value){
					$time_frame .= '<option value="'.$i.'"'.selected($autoresponderdata['time_frame'], $i, false).'>'.$value.'</option>';
				}
				$time_frame .= '</select>';
				echo sprintf( _x('create a new campaign every %1$s%2$s', 'every [x] [timeframe] starting [startdate]', 'mymail'), $interval, $time_frame );
			?>
			</p>
			<?php
				echo '<h4>'.__('next schedule', 'mymail').'</h4>';
			?>
			<p><?php echo sprintf( _x('on %1$s @ %2$s','send campaign "on" (date) "at" (time)', 'mymail'),
		'<input name="mymail_data[autoresponder_date]" class="datepicker deliverydate inactive" type="text" value="'.date('Y-m-d', $timestamp+$timeoffset).'" maxlength="10" readonly>',
		'<input name="mymail_data[autoresponder_time]" maxlength="5" class="deliverytime inactive" type="text" value="'.date('H:i', $timestamp+$timeoffset).'"> <span class="utcoffset">UTC '.($timeoffset ? '+' : '').($timeoffset/3600).'</span>');

				$autoresponderdata['endschedule'] = isset($autoresponderdata['endschedule']);

		if(mymail_option('trackcities')) :
		?>
		<label title="<?php _e('Send this campaign based on the subscribers timezone if known', 'mymail') ?>"><input type="checkbox" class="autoresponder-timezone" name="mymail_data[autoresponder][timebased_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php _e('Use Subscribers timezone', 'mymail') ?></label>
		<?php endif; ?>
			</p>
			<p>
				<label><input type="checkbox" name="mymail_data[autoresponder][endschedule]" class="mymail_autoresponder_timebased-end-schedule" <?php checked($autoresponderdata['endschedule']); ?> value="1"> <?php _e('end schedule', 'mymail'); ?></label>
				<div class="mymail_autoresponder_timebased-end-schedule-field" <?php if(!$autoresponderdata['endschedule']) echo ' style="display:none"';?>>
<?php
				$timestamp = max($timestamp, $autoresponderdata['endtimestamp']);

				echo sprintf( _x('on %1$s @ %2$s','send campaign "on" (date) "at" (time)', 'mymail'),
		'<input name="mymail_data[autoresponder_enddate]" class="datepicker deliverydate inactive" type="text" value="'.date('Y-m-d', $timestamp+$timeoffset).'" maxlength="10" readonly>',
		'<input name="mymail_data[autoresponder_endtime]" maxlength="5" class="deliverytime inactive" type="text" value="'.date('H:i', $timestamp+$timeoffset).'"> <span class="utcoffset">UTC '.($timeoffset ? '+' : '').($timeoffset/3600).'</span>');
				?>
				<span class="description"><?php _e('set an end date for your campaign', 'mymail'); ?></span>
				</div>

			</p>
			<p>
			<?php

				global $wp_locale;

				echo __('send campaigns only on these weekdays', 'mymail').'<br>';
				$start_at = get_option('start_of_week');

				for($i = $start_at; $i < 7+$start_at; $i++){
					$j = $i;
					if(!isset($wp_locale->weekday[$j])) $j = $j-7;
					echo '<label title="'.$wp_locale->weekday[$j].'" class="weekday"><input name="mymail_data[autoresponder][weekdays][]" type="checkbox" value="'.$j.'" '.checked((in_array($j, $autoresponderdata['weekdays']) || !$autoresponderdata['weekdays']), true, false).'><small>'.substr($wp_locale->weekday[$j],0,2).' </small></label>';
				}
			?>
			</p>

			<label><input type="checkbox" name="mymail_data[autoresponder][time_conditions]" id="time_extra" value="1" <?php checked(isset($autoresponderdata['time_conditions'])) ?>> <?php _e('only if', 'mymail'); ?></label>

			<div id="autoresponderfield-mymail_timebased_advanced"<?php if(!isset($autoresponderdata['time_conditions'])) echo ' style="display:none"';?>>
				<p>
				<?php
					$count = '<input type="number" name="mymail_data[autoresponder][time_post_count]" class="small-text" value="'.$autoresponderdata['time_post_count'].'">';
					$type = '<select id="autoresponder-post_type_time" name="mymail_data[autoresponder][time_post_type]">';
						foreach($pts as $pt => $data){
							if(in_array($pt, array('attachment', 'newsletter'))) continue;
							$type .= '<option value="'.$pt.'"'.selected($autoresponderdata['time_post_type'], $pt, false).'>'.$data->labels->name.'</option>';
						}
					$type .= '</select><br>';
					echo sprintf( __('%1$s %2$s have been published', 'mymail'), $count, $type );
				?>
				</p>
			</div>
		</div>

		<div class="mymail_autoresponder_more autoresponderfield-mymail_post_published autoresponderfield-mymail_autoresponder_timebased">

				<p>
				<?php
					$issue = '<input type="number" id="mymail_autoresponder_issue" name="mymail_data[autoresponder][issue]" class="small-text" value="'.$autoresponderdata['issue'].'">';
					echo sprintf( __('Next issue: %s', 'mymail'), $issue );
				?>
				</p>
				<p class="description">
				<?php
					echo sprintf( __('Use the %s tag to display the current issue in the campaign', 'mymail'), '<code>{issue}</code>' );
				?>
				</p>
		</div>

		<div class="mymail_autoresponder_more autoresponderfield-mymail_post_published <?php if(isset($autoresponderdata['time_conditions'])) echo ' autoresponderfield-mymail_autoresponder_timebased';?>">
				<p class="description">
				<?php
					$post_type = ($autoresponderdata['action'] == 'mymail_autoresponder_timebased')
						? $autoresponderdata['time_post_type']
						: $autoresponderdata['post_type'];

					echo sprintf( _n('%1$s matching %2$s has been published', '%1$s matching %2$s have been published', $autoresponderdata['post_count_status'], 'mymail'), '<strong>'.$autoresponderdata['post_count_status'].'</strong>', '<strong><a href="edit.php?post_type='.$post_type.'">'._n($pts[$post_type]->labels->singular_name, $pts[$post_type]->labels->name, $autoresponderdata['post_count_status']).'</a></strong>');
					?>
				<br><label><input type="checkbox" name="post_count_status_reset" value="1"> <?php _e('reset counter', 'mymail'); ?></label>
				</p>
				<input type="hidden" name="mymail_data[autoresponder][post_count_status]" value="<?php echo $autoresponderdata['post_count_status'] ?>">

		</div>

		<div class="mymail_autoresponder_more autoresponderfield-mymail_autoresponder_usertime">
			<p class='asdasds'>
			<?php

				if($customfields = mymail()->get_custom_date_fields()){

					$amount = '<input type="number" class="small-text" name="mymail_data[autoresponder][useramount]" value="'.(isset($autoresponderdata['useramount']) ? $autoresponderdata['useramount'] : 1).'">';

					$unit = '<select name="mymail_data[autoresponder][userunit]">';
					$values = array(
						'day' => __('day(s)', 'mymail'),
						'week' => __('week(s)', 'mymail'),
						'month' => __('month(s)', 'mymail'),
						'year' => __('year(s)', 'mymail'),
					);
					foreach($values as $key => $value){
						$unit .= '<option value="'.$key.'"'.selected($autoresponderdata['userunit'], $key, false).'>'.$value.'</option>';
					}
					$unit .= '</select>';

					$uservalue = '<select name="mymail_data[autoresponder][uservalue]">';
					$uservalue .= '<option value="-1">--</option>';
					foreach($customfields as $key => $data){
						$uservalue .= '<option value="'.$key.'"'.selected($autoresponderdata['uservalue'], $key, false).'>'.$data['name'].'</option>';
					}
					$uservalue .= '</select>';
				?>
			</p>
			<p id="userexactdate">
				<label><input type="radio" class="userexactdate" name="mymail_data[autoresponder][userexactdate]" value="0" <?php checked(!$autoresponderdata['userexactdate']) ?>>
				<span <?php if($autoresponderdata['userexactdate']) echo ' class="disabled"' ?>><?php echo sprintf( __('every %1$s %2$s', 'mymail'), $amount, $unit) ?></span></label><br>
				<label><input type="radio" class="userexactdate" name="mymail_data[autoresponder][userexactdate]" value="1" <?php checked($autoresponderdata['userexactdate']) ?>>
				<span <?php if(!$autoresponderdata['userexactdate']) echo ' class="disabled"' ?>><?php _e('on the exact date', 'mymail'); ?></span></label>
			</p>
			<p>
				<?php
					echo sprintf( __('of the users %1$s value', 'mymail'), $uservalue);
				}else{

					_e('No custom date fields found!', 'mymail' );
					if(current_user_can('manage_options')){
						echo '<br><a href="options-general.php?page=newsletter-settings&settings-updated=true#subscribers">'.__('add new fields', 'mymail').'</a>';
					}
				}
			?>
			</p><p>
			<?php
				if(mymail_option('trackcities')) :
				?>
				<label title="<?php _e('Send this campaign based on the subscribers timezone if known', 'mymail') ?>"><input type="checkbox" class="autoresponder-timezone" name="mymail_data[autoresponder][usertime_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php _e('Use Subscribers timezone', 'mymail') ?></label>
				<?php endif; ?>

			</p>
			<p><label>
				<input type="checkbox" name="mymail_data[autoresponder][usertime_once]" value="1" <?php checked($autoresponderdata['once']) ?>> <?php _e("send campaign only once", 'mymail' ); ?>
			</label></p>
		</div>
		<div class="mymail_autoresponder_more autoresponderfield-mymail_autoresponder_followup">
			<?php if($all_campaigns = $this->get_campaigns(array('post__not_in' => array($post->ID)))):

				//bypass post_status sort limitation
				$all_campaings_stati = wp_list_pluck($all_campaigns, 'post_status' );
				asort($all_campaings_stati);

			?>
			<p>
				<select name="mymail_data[autoresponder][followup_action]">
					<option value="1" <?php selected( $autoresponderdata['followup_action'], 1 ); ?>><?php _e('has been sent', 'mymail' ); ?></option>
					<option value="2" <?php selected( $autoresponderdata['followup_action'], 2 ); ?>><?php _e('has been opened', 'mymail' ); ?></option>
					<option value="3" <?php selected( $autoresponderdata['followup_action'], 3 ); ?>><?php _e('has been clicked', 'mymail' ); ?></option>
				</select>
			</p><fieldset>
				<label><?php _e('Campaign', 'mymail' ); ?>
				<select name="parent_id" id="parent_id" class="widefat">
				<option value="0">--</option>
			<?php

				global $wp_post_statuses;
				$status = '';
				foreach($all_campaings_stati as $i => $c){
					$c = $all_campaigns[$i];
					if($status != $c->post_status){
						if($status) echo '</optgroup>';
						echo '<optgroup label="'.$wp_post_statuses[$c->post_status]->label.'">';
						$status = $c->post_status;
					}
					?><option value="<?php echo $c->ID ?>" <?php selected($post->post_parent, $c->ID); ?>><?php echo $c->post_title ? $c->post_title : '['.__('no title', 'mymail').']' ?></option><?php
				}
			 ?>
				</optgroup></select></label>
			</fieldset>

			<?php else: ?>
			<p><?php _e('No campaigns available', 'mymail' ); ?></p>
			<?php endif; ?>
		</div>
		<div class="mymail_autoresponder_more autoresponderfield-mymail_autoresponder_hook">
			<p><label>
			<?php _e('Hook used to trigger campaign', 'mymail' ); ?> (<abbr title="<?php esc_attr_e('use `do_action("hook_name")`, or `do_action("hook_name", $subscriber_id)` to trigger this campaign', 'mymail' ); ?>">?</abbr>)
				<input type="text" class="widefat code" name="mymail_data[autoresponder][hook]" value="<?php echo $autoresponderdata['hook'] ?>" placeholder="hook_name">
			</label></p>
			<p><label>
			<?php _e('Priority', 'mymail' );?>:
				<select name="mymail_data[autoresponder][priority]">
					<option value="5" <?php selected( $autoresponderdata['priority'], 5 ); ?>><?php _e('High', 'mymail' ); ?></option>
					<option value="10" <?php selected( $autoresponderdata['priority'], 10 ); ?>><?php _e('Normal', 'mymail' ); ?></option>
					<option value="15" <?php selected( $autoresponderdata['priority'], 15 ); ?>><?php _e('Low', 'mymail' ); ?></option>
				</select>
			</label></p>
			<p><label>
				<input type="checkbox" name="mymail_data[autoresponder][hook_once]" value="1" <?php checked($autoresponderdata['once']) ?>> <?php _e("send campaign only once", 'mymail' ); ?>
			</label></p>
		</div>


		<?php do_action('mymail_autoresponder_more') ?>


	</div>
	</div>
	</div>
<?php endif;?>
<p>
	<input type="text" value="<?php echo esc_attr($current_user->user_email) ?>" autocomplete="off" id="mymail_testmail" class="widefat" aria-label="<?php esc_attr_e( 'Send Test', 'mymail' ); ?>">
	<button type="button" class="button mymail_spamscore" title="<?php _e('check your spam score', 'mymail'); ?> (beta)">Spam Score</button>
	<span class="spinner" id="delivery-ajax-loading"></span>
	<input type="button" value="<?php _e('Send Test', 'mymail') ?>" class="button mymail_sendtest">

	<div id="spam_score_progress">
	<div class="progress"><span class="bar" style="width:1%"></span></div>
	<div class="score"></div>
	</div>


</p>


<?php elseif ($post->post_status == 'active') : ?>
	<p>
	<?php echo sprintf( __('This campaign has been started on %1$s, %2$s ago', 'mymail'), '<br><strong>'.date($timeformat, $this->post_data['timestamp']+$timeoffset), human_time_diff($now, $this->post_data['timestamp']).'</strong>'); ?>
	</p>
<?php if($sent && !$is_autoresponder) :

	$totals = $this->get_totals($post->ID);

	$p = round($this->get_sent_rate($post->ID)*100);
?>
	<div class="progress"><span class="bar" style="width:<?php echo $p ?>%"></span><span>&nbsp;<?php echo sprintf(__('%1$s of %2$s sent', 'mymail'), number_format_i18n($sent), number_format_i18n($totals))?></span><var><?php echo $p ?>%</var></div>

	<?php if($p) : ?>
	<p>
	<?php
		$timepast = $now - $this->post_data['timestamp'];
		$timeleft = human_time_diff($now+(100 - $p) * ($timepast / $p));
		echo sprintf(__('finished in approx. %s', 'mymail'), '<strong>'.$timeleft.'</strong>');
	?>
	</p>
	<?php endif; ?>
<?php endif; ?>
<?php elseif ($is_autoresponder) : ?>
	<p>
	<?php echo sprintf(__('You have to %s to change the delivery settings', 'mymail'), '<a href="post.php?post='.$post_id.'&action=edit">'.__('switch to the edit mode', 'mymail').'</a>'); ?>
	</p>
<?php elseif ($post->post_status != 'finished'): ?>
<?php $totals = $this->get_totals($post->ID);

	$p = round($this->get_sent_rate($post->ID)*100);
?>
	<div class="progress paused"><span class="bar" style="width:<?php echo $p ?>%"></span><span>&nbsp;<?php echo sprintf(__('%1$s of %2$s sent', 'mymail'), number_format_i18n($sent), number_format_i18n($totals))?></span><var><?php echo $p ?>%</var></div>
<?php endif;

	if($this->post_data['parent_id']) :
		if(current_user_can('edit_newsletter', $post->ID) && current_user_can('edit_others_newsletters', $this->post_data['parent_id'])) :
	 ?>
			<p><?php echo sprintf(__('This campaign is based on an %s', 'mymail'), '<a href="post.php?post='.$this->post_data['parent_id'].'&action=edit&showstats=1">'.__('auto responder campaign', 'mymail').'</a>'); ?>
			</p>
	<?php
		endif;

	endif;
?>
<input type="hidden" id="mymail_is_autoresponder" name="mymail_data[is_autoresponder]" value="<?php echo $is_autoresponder?>">
