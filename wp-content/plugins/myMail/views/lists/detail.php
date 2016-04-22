<?php

	$id = isset($_GET['ID']) ? intval($_GET['ID']) : NULL;

	$is_new = isset($_GET['new']);

	if(!$is_new){
		if(!($list = $this->get($id, NULL, true))){
			wp_die('<h2>'.__('This list does not exist or has been deleted!', 'mymail').'</h2>');
		}

	}else{

		if(!current_user_can('mymail_add_subscribers' )) wp_die(__("You don't have the right permission to add new lists", 'mymail'));

		$list = $this->get_empty();
		if(isset($_POST['mymail_data'])){

			$list = (object) wp_parse_args( $_POST['mymail_data'], (array) $list );

		}
	}

	$timeformat = get_option('date_format').' '.get_option('time_format');
	$timeoffset = mymail('helper')->gmt_offset(true);

	$now = time();

	$tabindex = 1;

?>
<div class="wrap<?php if($is_new) echo ' new'?>">
<form id="subscriber_form" action="edit.php?post_type=newsletter&page=mymail_lists<?php echo ($is_new) ? '&new' : '&ID='.$id ?>" method="post">
<input type="hidden" id="ID" name="mymail_data[ID]" value="<?php echo $list->ID ?>">
<?php wp_nonce_field( 'mymail_nonce' ); ?>
<div id="icon-edit" class="icon32"></div>
<div style="height:0px; width:0px; overflow:hidden;"><input type="submit" name="save" value="1"></div>
<div id="icon-edit" class="icon32"></div>
<h2>
<?php

if($is_new){
	_e('Add new List', 'mymail');
}else{
	if($list->parent_id && $parent = $this->get($list->parent_id)){
		echo '<div class="parent_list"><strong><a href="edit.php?post_type=newsletter&page=mymail_lists&ID='.$parent->ID.'">'.$parent->name.'</a></strong> &rsaquo; </div>';
	}
	echo sprintf(__('Edit List %s', 'mymail'), '<strong>'.$list->name.'</strong>');
?>
<?php if(current_user_can('mymail_add_subscribers' )) : ?>
<a href="edit.php?post_type=newsletter&page=mymail_lists&new" class="add-new-h2"><?php _e( 'add New', 'mymail' ); ?></a>
<?php endif; ?>

<?php

} ?>
<span class="alignright">
	<?php if(!$is_new && current_user_can('mymail_delete_lists')) : ?>
		<input type="submit" name="delete" class="button button-large" value="<?php _e('delete List', 'mymail' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this list?', 'mymail' ); ?>');">
	<?php endif; ?>
	<?php if(!$is_new && current_user_can('mymail_delete_lists') && current_user_can('mymail_delete_subscribers')) : ?>
		<input type="submit" name="delete_subscribers" class="button button-large" value="<?php _e('delete List with Subscribers', 'mymail' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this list with all subscribers?', 'mymail' ); ?>');">
	<?php endif; ?>
	<input type="submit" name="save" class="button button-primary button-large" value="<?php _e('Save', 'mymail' ); ?>">
</span>
</h2>


<table class="form-table">
	<tr>
		<th scope="row"><h3><?php _e('Name' ,'mymail') ?></h3></th>
		<td>
			<h3 class="detail">
				<ul class="click-to-edit">
					<li><?php echo esc_attr( $list->name ); ?>&nbsp;</li>
					<li><input id="name" class="widefat" type="text" name="mymail_data[name]" value="<?php echo esc_attr( $list->name ); ?>" placeholder="<?php _e('Name of the List', 'mymail') ?>"></li>
				</ul>
			</h3>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Description' ,'mymail') ?></th>
		<td>
			<div class="detail">
				<ul class="click-to-edit">
					<li><?php echo $list->description ? esc_attr( $list->description ) : '<span class="description">'.__('no description', 'mymail' ).'</span>'; ?></li>
					<li><textarea id="description" class="widefat" type="text" name="mymail_data[description]"><?php echo esc_textarea( $list->description ); ?></textarea></li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Subscribers' ,'mymail') ?></th>
		<td>
			<?php echo '<a href="'.add_query_arg(array('lists' => array($list->ID)), 'edit.php?post_type=newsletter&page=mymail_subscribers').'">'.sprintf(_n('%s Subscriber', '%s Subscribers', $list->subscribers, 'mymail'), '<strong>'.number_format_i18n($list->subscribers).'</strong>').'</a>'; ?>
		</td>
	</tr>
</table>

<?php
if(!$is_new) :

	$actions = mymail('actions')->get_by_list($list->ID, NULL, true);

	$sent = $actions['sent'];
	$opens = $actions['opens'];
	$clicks = $actions['clicks'];
	$unsubscribes = $actions['unsubscribes'];
	$bounces = $actions['bounces'];

	$openrate = ($sent) ? $opens/$sent*100 : 0;
	$clickrate = ($opens) ? $clicks/$opens*100 : 0;
	$unsubscriberate = ($opens) ? $unsubscribes/$opens*100 : 0;
	$bouncerate = ($sent) ? $bounces/$sent*100 : 0;

?>
	<div class="stats-wrap">
		<table id="stats">
			<tr>
			<td><span class="verybold"><?php echo number_format_i18n($sent) ?></span> <?php echo _n('Mail sent', 'Mails sent', $sent, 'mymail')?></td>
			<td width="60">
				<div id="stats_open" class="piechart" data-percent="<?php echo $openrate ?>"><span>0</span>%</div>
			</td><td><span class="verybold"></span> <?php _e('open rate', 'mymail') ?></td>
			<td width="60">
				<div id="stats_click" class="piechart" data-percent="<?php echo $clickrate ?>"><span>0</span>%</div>
			</td><td><span class="verybold"></span> <?php _e('click rate', 'mymail') ?></td>
			<td width="60">
				<div id="stats_unsub" class="piechart" data-percent="<?php echo $unsubscriberate ?>"><span>0</span>%</div>
			</td><td><span class="verybold"></span> <?php _e('unsubscribe rate', 'mymail') ?></td>
			<td width="60">
				<div id="stats_bounce" class="piechart" data-percent="<?php echo $bouncerate ?>"><span>0</span>%</div>
			</td><td><span class="verybold"></span> <?php _e('bounce rate', 'mymail') ?></td>
			</tr>
		</table>
	</div>

	<div class="activity-wrap">
		<?php

			if($activities = $this->get_activity($list->ID)) :

			?>
		<h3><?php _e('Activity', 'mymail' ); ?></h3>
			<table class="wp-list-table widefat">
				<thead>
					<tr><th><?php _e('Date', 'mymail' ); ?></th><th></th><th><?php _e('Action', 'mymail' ); ?></th><th><?php _e('Campaign', 'mymail' ); ?></th><th></th></tr>
				</thead>
				<tbody>
			<?php foreach($activities as $i => $activity) {?>
					<tr class="<?php if(!($i%2)) echo ' alternate'; ?>">
						<td><?php echo ($now-$activity->timestamp < 3600 ? sprintf( __('%s ago', 'mymail'), human_time_diff($now, $activity->timestamp)) : date($timeformat, $activity->timestamp+$timeoffset)); ?></td>
						<td><?php
							switch ($activity->type) {
								case 1:
									echo '<span class="mymail-icon icon-mm-progress"></span></td><td>';
									echo sprintf(__('Campaign %s has start sending', 'mymail'), '<a href="'.admin_url('post.php?post='.$activity->campaign_id.'&action=edit').'">'.$activity->campaign_title.'</a>');
									break;
								case 2:
									echo '<span class="mymail-icon icon-mm-open"></span></td><td>';
									echo sprintf(__('First open in Campaign %s', 'mymail'), '<a href="'.admin_url('post.php?post='.$activity->campaign_id.'&action=edit').'">'.$activity->campaign_title.'</a>');
									break;
								case 3:
									echo '<span class="mymail-icon icon-mm-click"></span></td><td>';
									echo sprintf(__('%1$s in Campaign %2$s clicked', 'mymail'), '<a href="'.$activity->link.'">'.__('Link', 'mymail').'</a>', '<a href="'.admin_url('post.php?post='.$activity->campaign_id.'&action=edit').'">'.$activity->campaign_title.'</a>');
									break;
								case 4:
									echo '<span class="mymail-icon icon-mm-unsubscribe"></span></td><td>';
									echo __('First subscription canceled', 'mymail');
									break;
								case 5:
									echo '<span class="mymail-icon icon-mm-bounce"></span></td><td>';
									echo sprintf(__('Soft bounce (%d tries)', 'mymail'), $activity->count);
									break;
								case 6:
									echo '<span class="mymail-icon icon-mm-bounce hard"></span></td><td>';
									echo __('Hard bounce', 'mymail');
									break;

								default:
									echo '</td><td>';
									break;
							}

						?></td>
						<td><a href="<?php echo admin_url('post.php?post='.$activity->campaign_id.'&action=edit') ?>"><?php echo $activity->campaign_title ?></a></td>
						<td width="50%">
						<?php if($activity->link) : ?>
							<a href="<?php echo $activity->link ?>"><?php echo $activity->link ?></a>
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


<?php endif; ?>
</form>
</div>
