<?php

		require_once MYMAIL_DIR . 'classes/templates.class.php';
		$t = new mymail_templates();
		$templates = $t->get_templates();

if($camps = mymail_get_finished_campaigns(array( 'posts_per_page' => 10, 'post_status' => array('finished', 'active')))){


		$now = time();
		$timeformat = get_option('date_format').' '.get_option('time_format');
		$timeoffset = mymail('helper')->gmt_offset(true);

		$campaign = $camps[0];

		$campaign_data = mymail('campaigns')->meta( $campaign->ID );

		$totals = mymail('campaigns')->get_totals($campaign->ID);
		$errors = mymail('campaigns')->get_errors($campaign->ID);
		$sent = mymail('campaigns')->get_sent($campaign->ID);
		$opens = mymail('campaigns')->get_opens($campaign->ID);
		$open_totals = mymail('campaigns')->get_opens($campaign->ID, true);
		$clicks = mymail('campaigns')->get_clicks($campaign->ID);
		$click_totals = mymail('campaigns')->get_clicks($campaign->ID, true);
		$bounces = mymail('campaigns')->get_bounces($campaign->ID);
		$unsubscribes = mymail('campaigns')->get_unsubscribes($campaign->ID);



	?>
<div class="stats table_content <?php if($campaign->post_status == 'active') echo "isactive";?>" id="stats_cont">
	<h4>
<?php _e('Recent Campaign', 'mymail') ?>: <a id="camp_name" href="post.php?post=<?php echo $campaign->ID ?>&action=edit" title="<?php _e('edit', 'mymail')?>"><?php echo $campaign->post_title ?></a>
	<select id="mymail-campaign-select">
<?php foreach($camps as $camp){ ?>
		<option <?php selected($camp->ID, $campaign->ID ); ?> value="<?php echo $camp->ID ?>"><?php echo $camp->post_title ?></option>
<?php }?>
	</select>
	</h4>
	<p>
	</p>

<table id="stats">
	<tr><th width="60"><?php echo ($campaign->post_status == 'active') ? __('sent', 'mymail') : __('total', 'mymail'); ?></th><th><?php _e('opens', 'mymail') ?></th><th><?php echo _n("clicks", "clicks", 2, 'mymail') ?></th><th><?php echo _x('unsubscribes', 'count of', 'mymail') ?></th><th><?php _e('bounces', 'mymail') ?></th></tr>
	<tr>
	<td align="center"><span class="verybold" id="stats_total"><?php echo $sent ?></span></td>
	<td width="100" align="center">
	<div id="stats_open" class="piechart" data-percent="<?php echo mymail('campaigns')->get_open_rate($campaign->ID)*100 ?>"><span>0</span>%</div>
	</td>
	<td width="100" align="center">
	<div id="stats_clicks" class="piechart" data-percent="<?php echo mymail('campaigns')->get_click_rate($campaign->ID)*100 ?>"><span>0</span>%</div>
	</td>
	<td width="100" align="center">
	<div id="stats_unsubscribes" class="piechart" data-percent="<?php echo mymail('campaigns')->get_unsubscribe_rate($campaign->ID)*100 ?>"><span>0</span>%</div>
	</td>
	<td width="100" align="center">
	<div id="stats_bounces" class="piechart" data-percent="<?php echo mymail('campaigns')->get_bounce_rate($campaign->ID)*100 ?>"><span>0</span>%</div>
	</tr>
</table>


<div class="campaign-info"><p><?php _e('This Campaign is currently progressing', 'mymail')?></p></div>
		<?php foreach($camps as $campaign){
			$campaign_data = mymail('campaigns')->meta( $campaign->ID );

			$campaign_data = array(
				'totals' => mymail('campaigns')->get_totals($campaign->ID),
				'errors' => mymail('campaigns')->get_errors($campaign->ID),
				'sent' => mymail('campaigns')->get_sent($campaign->ID),
				'opens' => mymail('campaigns')->get_opens($campaign->ID),
				'open_totals' => mymail('campaigns')->get_opens($campaign->ID, true),
				'clicks' => mymail('campaigns')->get_clicks($campaign->ID),
				'click_totals' => mymail('campaigns')->get_clicks($campaign->ID, true),
				'bounces' => mymail('campaigns')->get_bounces($campaign->ID),
				'unsubscribes' => mymail('campaigns')->get_unsubscribes($campaign->ID),
			);

		?>
<div class="camp" data-id="<?php echo $campaign->ID ?>" data-active="<?php echo ($campaign->post_status == 'active') ?>" data-name="<?php echo $campaign->post_title ?>" data-data='<?php echo json_encode($campaign_data) ?>'></div>
		<?php }?>
</div>
<?php }?>

<br class="clear">
	<?php
	$counts = get_option('mymail_subscribers_count', array());

	$actions = mymail('actions')->get_chronological_actions('days', strtotime('-7 days'), false);

	if(!empty($actions)) :

		$data = array("['".__('Date', 'mymail')."','".__('Signups', 'mymail')."','".__('Opens', 'mymail')."','".__('Clicks', 'mymail')."','".__('Unsubscribes', 'mymail')."','".__('Bounces', 'mymail')."']");
		foreach($actions as $timestamp => $action){
			$data[] = "['".__(date('D', $timestamp))."', ".$action['signups'].",".$action['opens'].",".$action['clicks'].",".$action['unsubscribes'].",".$action['bounces']."]";
		}

		?>

		<script type="text/javascript">var mymailL10n = mymailL10n || {}; mymailL10n.data = [<?php echo implode(',',$data) ?>];</script>

	<div id="dashboard_chart">
	</div>
	<ul class="legend">
		<li class="signups"><span></span> <?php _e('Signups', 'mymail') ?></li>
		<li class="opens"><span></span> <?php _e('Opens', 'mymail') ?></li>
		<li class="clicks"><span></span> <?php _e('Clicks', 'mymail') ?></li>
		<li class="unsub"><span></span> <?php _e('Unsubscribes', 'mymail') ?></li>
		<li class="bounces"><span></span> <?php _e('Bounces', 'mymail') ?></li>
	</ul>
	<br class="clear">
<?php endif;?>
<div class="versions">
	<?php
	if(current_user_can('update_plugins') && !is_plugin_active_for_network(MYMAIL_SLUG)){
		$plugins = get_site_transient('update_plugins');
		if(isset($plugins->response[MYMAIL_SLUG]) && version_compare( $plugins->response[MYMAIL_SLUG]->new_version, MYMAIL_VERSION, '>' ) ) {
	?>
	<a href="update.php?action=upgrade-plugin&plugin=<?php echo urlencode(MYMAIL_SLUG);?>&_wpnonce=<?php echo wp_create_nonce('upgrade-plugin_' . MYMAIL_SLUG)?>" class="button button-small button-primray"><?php printf( __('Update to %s', 'mymail'), $plugins->response[MYMAIL_SLUG]->new_version ? $plugins->response[MYMAIL_SLUG]->new_version : __( 'Latest', 'mymail' ) )?></a>
	<?php
		}
	}
	?>
	<span id="wp-version-message">MyMail <?php echo MYMAIL_VERSION ?></span>
	<br class="clear">
</div>
