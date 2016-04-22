<?php

	$id = isset($_GET['ID']) ? intval($_GET['ID']) : NULL;

	$is_new = isset($_GET['new']);

	if(!$is_new){
		if(!($subscriber = $this->get($id, true))){
			wp_die('<h2>'.__('This user does not exist or has been deleted!', 'mymail').'</h2>');
		}
		$meta = (object) $this->meta($subscriber->ID);
		$nicename = (!empty($subscriber->fullname) ? $subscriber->fullname : $subscriber->email);

	}else{

		if(!current_user_can('mymail_add_subscribers' )) wp_die(__("You don't have the right permission to add new subscribers", 'mymail'));

		$subscriber = $this->get_empty();
		if(isset($_POST['mymail_data'])){

			$subscriber = (object) wp_parse_args( $_POST['mymail_data'], (array) $subscriber );

		}

	}

	$customfields = mymail()->get_custom_fields();

	$timeformat = get_option('date_format').' '.get_option('time_format');
	$timeoffset = mymail('helper')->gmt_offset(true);

	$now = time();

	$tabindex = 1;

?>
<div class="wrap<?php echo ($is_new) ? ' new' : ' status-'.$subscriber->status ?>">
<form id="subscriber_form" action="edit.php?post_type=newsletter&page=mymail_subscribers<?php echo ($is_new) ? '&new' : '&ID='.$id ?>" method="post">
<input type="hidden" id="ID" name="mymail_data[ID]" value="<?php echo $subscriber->ID ?>">
<?php wp_nonce_field( 'mymail_nonce' ); ?>
<div id="icon-edit" class="icon32"></div>
<div style="height:0px; width:0px; overflow:hidden;"><input type="submit" name="save" value="1"></div>
<div id="icon-edit" class="icon32"></div>
<h2>
<?php

if($is_new){
	_e('Add new Subscriber', 'mymail');
}else{
	echo sprintf(__('Edit %s', 'mymail'), '<strong>'.$nicename.'</strong>');
	if($subscriber->status == 4){
		echo '<div class="error"><p>'.sprintf(__('This subscriber has caused a sending error: %s', 'mymail'), '<strong>'.($meta->error ? $meta->error : __('unknown', 'mymail')).'</strong>').'</p></div>';
	}
?>
<?php if(current_user_can('mymail_add_subscribers' )) : ?>
<a href="edit.php?post_type=newsletter&page=mymail_subscribers&new" class="add-new-h2"><?php _e( 'add New', 'mymail' ); ?></a>
<?php endif; ?>
<?php if($subscriber->wp_id) : ?><a href="user-edit.php?user_id=<?php echo $subscriber->wp_id ?>" class="add-new-h2"><?php _e( 'goto WordPress User profile', 'mymail' ); ?></a><?php endif; ?>
<?php } ?>
	<span class="alignright">
		<?php if(!$is_new && $subscriber->status == 0) : ?>
			<input type="submit" name="confirmation" class="button button-large" value="<?php _e('resend confirmation', 'mymail' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to resend the confirmation?', 'mymail' ); ?>');">
		<?php endif; ?>
		<?php if(!$is_new && current_user_can('mymail_delete_subscribers')) : ?>
			<input type="submit" name="delete" class="button button-large" value="<?php _e('delete Subscriber', 'mymail' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this subscriber?', 'mymail' ); ?>');">
		<?php endif; ?>
		<input type="submit" name="save" class="button button-primary button-large" value="<?php _e('Save', 'mymail' ); ?>">
	</span>
</h2>


<table class="form-table">
	<tr>
		<td scope="row" class="avatar-wrap">
			<div class="avatar<?php if($subscriber->wp_id) echo ' wp-user' ?>" title="<?php _e('Source', 'mymail') ?>: Gravatar.com" style="background-image:url(<?php echo $this->get_gravatar_uri($subscriber->email, 400); ?>)"></div>
			<p class="info"><?php _e('Source', 'mymail') ?>: <a href="http://gravatar.com">Gravatar.com</a></p>
			<?php if(!$is_new) :?>

			<h4 title="<?php _e('The user rating is based on different factors like open rate, click rate and bounces', 'mymail') ?>"><?php _e('User Rating', 'mymail' ); ?>:<br />
			<?php
				$stars = (round($subscriber->rating/10, 2)*50);
				$full = floor($stars);
				$half = round($stars-$full);
				$empty = 5-$full-$half;
			?>
			<?php echo str_repeat('<span class="mymail-icon icon-mm-star"></span>', $full)
						.str_repeat('<span class="mymail-icon icon-mm-star-half"></span>', $half)
						.str_repeat('<span class="mymail-icon icon-mm-star-empty"></span>', $empty) ?>
			</h4>

		<?php endif; ?>

		</td>
		<td class="user-info">
			<h3 class="detail">
				<ul class="click-to-edit type-email">
					<li><?php echo esc_attr( $subscriber->email ); ?>&nbsp;</li>
					<li><input id="email" class="" type="email" name="mymail_data[email]" value="<?php echo esc_attr( $subscriber->email ); ?>" placeholder="<?php echo mymail_text('email') ?>"></li>
				</ul>
				<code title="<?php echo sprintf(__('use %1$s as placeholder tag to replace it with %2$s', 'mymail'), '{emailaddress}', '&quot;'.$subscriber->email.'&quot;' )?>">{emailaddress}</code>
			</h3>
			<div class="detail">
				<label for="mymail_firstname" class="label-type-name"><?php _e('Name', 'mymail' ); ?>:</label>
				<ul class="click-to-edit type-name">
					<li><?php echo esc_attr( $subscriber->fullname ); ?>&nbsp;</li>
					<li>
				<input id="mymail_firstname" type="text" name="mymail_data[firstname]" value="<?php echo esc_attr( $subscriber->firstname ); ?>" placeholder="<?php echo mymail_text('firstname') ?>">
				<input id="mymail_lastname" class="" type="text" name="mymail_data[lastname]"value="<?php echo esc_attr( $subscriber->lastname ); ?>" placeholder="<?php echo mymail_text('lastname') ?>">
					</li>
				</ul>
				<code title="<?php echo sprintf(__('use %1$s as placeholder tag to replace it with %2$s', 'mymail'), '{fullname}', '&quot;'.$subscriber->fullname.'&quot;' )?>">{fullname}</code>
				<code title="<?php echo sprintf(__('use %1$s as placeholder tag to replace it with %2$s', 'mymail'), '{lastname}', '&quot;'.$subscriber->lastname.'&quot;' )?>">{lastname}</code>
				<code title="<?php echo sprintf(__('use %1$s as placeholder tag to replace it with %2$s', 'mymail'), '{firstname}', '&quot;'.$subscriber->firstname.'&quot;' )?>">{firstname}</code>
			</div>
			<div class="detail">
				<label for="mymail_status"><?php _e('Status', 'mymail' ); ?>:</label>
				<ul class="click-to-edit type-status">
					<li><?php echo $this->get_status($subscriber->status, true)?>&nbsp;</li>
					<li><div class="statuses">
						<select name="mymail_data[status]" id="mymail_status">
						<?php $statuses = $this->get_status(NULL, true);
							foreach($statuses as $id => $status){
							if($id == 4 && $subscriber->status != 4) continue;
						?>
						<option value="<?php echo intval($id) ?>" <?php selected($id, $subscriber->status) ?> ><?php echo $status ?></option>
						<?php } ?>
						</select>
						<span class="description info"><?php _e('choosing "pending" as status will force a confirmation message to the subscriber', 'mymail' ); ?></span>
						</div>
					</li>
				</ul>
			</div>
			<?php if(!$is_new) :?>
			<div class="info">
				<strong><?php _e('subscribed at', 'mymail' ); ?>:</strong> <?php echo $subscriber->signup
					? date($timeformat, $subscriber->signup+$timeoffset).', '.sprintf( __('%s ago', 'mymail'), human_time_diff($now, $subscriber->signup))
					: __('unknown', 'mymail') ?>

				<div><?php $this->output_referer($subscriber->ID); ?></div>

				<a class="show-more-info alignright"><?php _e('more', 'mymail' ); ?></a>
				<ul class="more-info">
					<li><strong><?php _e('confirmed at', 'mymail' ); ?>:</strong> <?php echo $subscriber->confirm
						? date($timeformat, $subscriber->confirm+$timeoffset).', '.sprintf( __('%s ago', 'mymail'), human_time_diff($now, $subscriber->confirm)).($subscriber->ip_confirm ? ' '.sprintf( __('with IP %s', 'mymail'), $subscriber->ip_confirm ) : '')
						: __('unknown', 'mymail') ?>
					</li>
					<li><strong><?php _e('latest known IP', 'mymail' ); ?>:</strong> <?php echo $meta->ip
						? $meta->ip
						: __('unknown', 'mymail') ?>
					</li>
				</ul>
			</div>
			<div class="info">
				<strong><?php _e('latest updated', 'mymail'); ?>:</strong> <?php echo $subscriber->updated
					? date($timeformat, $subscriber->updated+$timeoffset).', '.sprintf( __('%s ago', 'mymail'), human_time_diff($now, $subscriber->updated))
					: __('never', 'mymail') ?>
			</div>
			<?php endif; ?>
			<div class="custom-field-wrap">
			<?php
			if($customfields) :
				foreach($customfields as $field => $data){
			?>
			<div class="detail">
				<label for="mymail_data_<?php echo $field?>" class="label-type-<?php echo $data['type'] ?>"><?php echo $data['name']?>:</label>
					<code title="<?php echo sprintf(__('use %1$s as placeholder tag to replace it with %2$s', 'mymail'), '{'.$field.'}', '&quot;'.$subscriber->{$field}.'&quot;' )?>">{<?php echo $field?>}</code>
				<ul class="click-to-edit type-<?php echo $data['type'] ?>">
			<?php
					switch($data['type']){

						case 'dropdown':
						?>
					<li><?php echo $subscriber->{$field} ? $subscriber->{$field} : __('nothing selected', 'mymail') ?></li>
					<li><select id="mymail_data_<?php echo $field?>" name="mymail_data[<?php echo $field?>]">
						<?php foreach($data['values'] as $v){?>
							<option value="<?php echo esc_attr($v) ?>" <?php selected((!empty($subscriber->{$field})) ? $subscriber->{$field} : $data['default'], $v) ?>><?php echo $v ?></option>
						<?php } ?>
					</select></li>
						<?php
							break;

						case 'radio':
						?>
						<li><?php echo $subscriber->{$field} ?></li>
						<li><ul>
						<?php
							$i = 0;
							foreach($data['values'] as $v){ ?>
							<li><label for="mymail_data_<?php echo $field ?>_<?php echo $i++ ?>"><input type="radio" id="mymail_data_<?php echo $field ?>_<?php echo $i++ ?>" name="mymail_data[<?php echo $field?>]" value="<?php echo esc_attr($v) ?>" <?php checked($subscriber->{$field}, $v) ?>> <?php echo $v ?> </label></li>
						<?php }
						?>
						</ul>
						</li>
						<?php
							break;

						case 'checkbox':
			 ?>
					<li> <?php echo $subscriber->{$field} ? __('yes', 'mymail') : __('no', 'mymail') ?></li>
					<li><label for="mymail_data_<?php echo $field ?>" class="label-type-checkbox"><input type="checkbox" id="mymail_data_<?php echo $field ?>" name="mymail_data[<?php echo $field?>]" value="1" <?php checked($subscriber->{$field}, true) ?>> <?php echo $data['name'] ?> </label>
						</li>
						<?php
							break;

						case 'date':
						?>
					<li><?php echo $subscriber->{$field} ? '<p>'.date(get_option('date_format'), strtotime($subscriber->{$field})).'</p>' : $subscriber->{$field}.'&nbsp;'; ?></li>
					<li><input type="text" id="mymail_data_<?php echo $field ?>" name="mymail_data[<?php echo $field?>]" value="<?php echo esc_attr($subscriber->{$field}); ?>" class="regular-text input datepicker"></li>
						<?php
							break;

						case 'textarea':
						?>
					<li><?php echo $subscriber->{$field} ? '<p>'.nl2br(strip_tags($subscriber->{$field})).'</p>' : $subscriber->{$field}.'&nbsp;'; ?></li>
					<li><textarea id="mymail_data_<?php echo $field ?>" name="mymail_data[<?php echo $field?>]" class="regular-text input"><?php echo esc_textarea($subscriber->{$field}); ?></textarea></li>
						<?php
							break;

						default:
						?>
					<li><?php echo $subscriber->{$field} ? '<p>'.$subscriber->{$field}.'</p>' : $subscriber->{$field}.'&nbsp;'; ?></li>
					<li><input type="text" id="mymail_data_<?php echo $field ?>" name="mymail_data[<?php echo $field?>]" value="<?php echo esc_attr($subscriber->{$field}); ?>" class="regular-text input"></li>
						<?php
					}
					?>

				</ul>
			</div>

			<?php
				}
			endif; ?>

			</div>
			<div class="detail v-top">
				<label><?php _e('Lists', 'mymail' ); ?>:</label>
				<ul class="click-to-edit type-list">
				<li>
				<?php
					if($lists = $this->get_lists($subscriber->ID)) :
						foreach($lists as $i => $list){
							echo '<span title="'.$list->description.'">'.$list->name.'</span>';
						}
					else:

						echo '<span class="description">'.__('User has not been assigned to a list', 'mymail').'</span>';

					endif;
				?>
				</li>
				<li> <?php

					$checked = wp_list_pluck( $lists, 'ID' );
					mymail('lists')->print_it(NULL, NULL, 'mymail_lists', false, $checked);
				?>
				</li>
				</ul>
			</div>
		</td>
		<td class="user-meta" align="right">
			<?php if(!$is_new) : ?>
				<?php if($meta->coords) : $geo = explode('|', $meta->geo); ?>
				<div class="map zoomable">
					<img src="//maps.googleapis.com/maps/api/staticmap?markers=<?php echo $meta->coords ?>&zoom=<?php echo ($geo[1]) ? '5' : '3' ?>&size=300x250&visual_refresh=true&sensor=false&scale=2&language=<?php echo get_locale() ?>" width="300" heigth="250">
				</div>
				<p class="alignright">
					<?php  if($geo[1]) echo __('from', 'mymail').sprintf(' %s, %s', '<strong><a href="https://www.google.com/maps/@'.$meta->coords.',11z" class="external">'.$geo[1].'</a></strong>', '<span class="mymail-flag-24 flag-'.strtolower($geo[0]).'"></span> '.mymail('geo')->code2Country($geo[0]));
					 ?>
				<?php elseif($meta->geo) : ?>
				<?php $geo = explode('|', $meta->geo);?>
				<div class="map">
					<img src="//maps.googleapis.com/maps/api/staticmap?center=<?php echo mymail('geo')->code2Country($geo[0]) ?>&zoom=3&size=300x250&visual_refresh=true&sensor=false&scale=2&language=<?php echo get_locale() ?>" width="300" heigth="250">
				</div>
				<p class="alignright">
					<?php  echo __('from', 'mymail'). ' <span class="mymail-flag-24 flag-'.strtolower($geo[0]).'"></span> '.mymail('geo')->code2Country($geo[0]);
					 ?>
				<?php endif; ?>
					<?php if(!is_null($meta->timeoffset)) : $t = time()+($meta->timeoffset*3600)?>
					<?php echo '<br>'.__('Local Time', 'mymail').': <span title="'.date($timeformat, $t).'">'.date(get_option('time_format'), $t).'</span>'; ?>
					<?php echo '<br>UTC '.($meta->timeoffset < 0 ? '' : '+').$meta->timeoffset ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php

if(!$is_new) :

	$sent = $this->get_sent($subscriber->ID);
	$openrate = $this->get_open_rate($subscriber->ID);
	$clickrate = $this->get_click_rate($subscriber->ID);
	$aclickrate = $this->get_adjusted_click_rate($subscriber->ID);

?>
	<div class="stats-wrap">
		<table id="stats">
			<tr>
			<td><span class="verybold"><?php echo $sent ?></span> <?php echo _n('Campaign sent', 'Campaigns sent', $sent, 'mymail')?></td>
			<td width="60">
			<div id="stats_open" class="piechart" data-percent="<?php echo $openrate*100 ?>"><span>0</span>%</div>
			</td>
			<td><span class="verybold"></span> <?php _e('open rate', 'mymail') ?></td>
			<td width="60">
			<div id="stats_click" class="piechart" data-percent="<?php echo $clickrate*100 ?>"><span>0</span>%</div>
			</td>
			<td><span class="verybold"></span> <?php _e('click rate', 'mymail') ?></td>
			<td width="60">
			<div id="stats_click" class="piechart" data-percent="<?php echo $aclickrate*100 ?>"><span>0</span>%</div>
			</td>
			<td><span class="verybold"></span> <?php _e('adjusted click rate', 'mymail') ?></td>
			</tr>
		</table>
	</div>

	<?php if($clients = $this->get_clients($subscriber->ID)) : ?>
	<div class="clients-wrap">
		<?php

			$mostpopular = array_shift($clients);

		?>

		<h3><?php _e('Most popular client', 'mymail') ?>: <span class="mymail-icon client-<?php echo $mostpopular['type'] ?>"></span><?php echo $mostpopular['name'].' <span class="count">('.round($mostpopular['percentage']*100, 2).'%)</span> ' ?></h3>

	<?php if(!empty($clients)) : ?>
		<p><?php _e('Other used clients', 'mymail') ?>:
		<?php
		 foreach ($clients as $client) {
		 	echo '<span class="mymail-icon client-'.$client['type'].'"></span> <strong>'.$client['name'].'</strong> <span class="count">('.round($client['percentage']*100, 2).'%)</span>, ';
		 }
		?>

		</p>
	<?php endif; ?>

	</div>
	<?php endif; ?>
	<div class="activity-wrap">
		<?php

			if($activities = $this->get_activity($subscriber->ID)) :

				$open_time = $this->open_time($subscriber->ID);
				$click_time = $this->click_time($subscriber->ID);

			?>
		<h3><?php _e('Activity', 'mymail' ); ?></h3>
			<p>
			<?php if($open_time) : ?>
			<?php echo sprintf(__('%1$s needs about %2$s to open a campaign', 'mymail'), ($subscriber->fullname ? $subscriber->fullname : __('User', 'mymail')), '<strong>'.human_time_diff($now+$open_time).'</strong>');?> <?php if($click_time) echo sprintf(__('and %1$s to click a link', 'mymail'), '<strong>'.human_time_diff($now+$click_time).'</strong>');?>
			<?php else :
				_e('User has never opened a campaign', 'mymail');
				endif; ?>
			</p>
			<table class="wp-list-table widefat activities">
				<thead>
					<tr><th><?php _e('Date', 'mymail' ); ?></th><th></th><th><?php _e('Action', 'mymail' ); ?></th><th><?php _e('Campaign', 'mymail' ); ?></th><th></th></tr>
				</thead>
				<tbody>
			<?php foreach($activities as $i => $activity) { ?>
					<tr class="<?php if(!($i%2)) echo ' alternate'; ?>">
						<td><?php echo ($now-$activity->timestamp < 3600 ? sprintf( __('%s ago', 'mymail'), human_time_diff($now, $activity->timestamp)) : date($timeformat, $activity->timestamp+$timeoffset)); ?></td>
						<td><?php
							switch ($activity->type) {
								case 1:
									echo '<span class="mymail-icon icon-mm-progress"></span></td><td>';
									echo sprintf(__('Campaign %s has been sent', 'mymail'), '<a href="'.admin_url('post.php?post='.$activity->campaign_id.'&action=edit').'">'.$activity->campaign_title.'</a>');
									break;
								case 2:
									echo '<span class="mymail-icon icon-mm-open"></span></td><td>';
									echo sprintf(__('opened Campaign %s', 'mymail'), '<a href="'.admin_url('post.php?post='.$activity->campaign_id.'&action=edit').'">'.$activity->campaign_title.'</a>');
									break;
								case 3:
									echo '<span class="mymail-icon icon-mm-click"></span></td><td>';
									echo sprintf(__('clicked %1$s in Campaign %2$s', 'mymail'), '<a href="'.$activity->link.'">'.__('a link', 'mymail').'</a>', '<a href="'.admin_url('post.php?post='.$activity->campaign_id.'&action=edit').'">'.$activity->campaign_title.'</a>');
									break;
								case 4:
									echo '<span class="mymail-icon icon-mm-unsubscribe"></span></td><td>';
									echo __('unsubscribed your newsletter', 'mymail');
									break;
								case 5:
									echo '<span class="mymail-icon icon-mm-bounce"></span></td><td>';
									echo sprintf(__('Soft bounce (%d tries)', 'mymail'), $activity->count);

									break;
								case 6:
									echo '<span class="mymail-icon icon-mm-bounce hard"></span></td><td>';
									echo __('Hard bounce', 'mymail');
									break;
								case 7:
									echo '<span class="mymail-icon icon-mm-error"></span></td><td>';
									echo __('Error', 'mymail');
									break;

								default:
									echo '</td><td>';
									break;
							}

						?></td>
						<td><a href="<?php echo admin_url('post.php?post='.$activity->campaign_id.'&action=edit') ?>"><?php echo $activity->campaign_title ?></a></td>
						<td width="50%">
						<?php if($activity->campaign_status == 'trash') : ?>
							<?php _e('campaign deleted', 'mymail' ); ?>
						<?php elseif($activity->type == 1 && current_user_can( 'publish_newsletters' )) : ?>
							<a href="<?php echo add_query_arg(array('resendcampaign' => 1, '_wpnonce' => wp_create_nonce('mymail-resend-campaign'), 'campaign_id' => $activity->campaign_id)); ?>" class="button button-small" onclick="return confirm('<?php echo sprintf(esc_attr__( 'Do you really like to resend campaign %1$s to %2$s?', 'mymail' ), "\\n\'".$activity->campaign_title."\'", "\'".$nicename."\'"); ?>');">
							<?php _e('resend this campaign', 'mymail' ); ?>
							</a>
						<?php elseif($activity->link && $activity->type == 3) : ?>
							<a href="<?php echo $activity->link ?>"><?php echo $activity->link ?></a>
						<?php elseif(($activity->type == 5 || $activity->type == 6)
								&& $bouncestatus = $this->meta($subscriber->ID, 'bounce', $activity->campaign_id)) :
								$message = mymail('helper')->get_bounce_message($bouncestatus);?>
							<p class="bounce-message"><span title="<?php echo esc_attr( $message['descr'] ); ?>"><code>[<?php echo $bouncestatus ?>]</code> <strong><?php echo $message['title']; ?></strong></span></p>
						<?php elseif($activity->error && $activity->type == 7) : ?>
							<strong class="red"><?php echo $activity->error ?></strong>
						<?php endif; ?>
						</td>
					</tr>
			<?php } ?>
				</tbody>
			</table>
		<?php
			else :
		?>
		<p class="description"><?php _e('no activity yet', 'mymail' ); ?></p>
		<?php
			endif;
		?>
	</div>

<?php endif; //!is_new ?>
</form>
</div>
