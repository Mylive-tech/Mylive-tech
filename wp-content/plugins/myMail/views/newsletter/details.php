<?php

	$editable = !in_array($post->post_status, array('active', 'finished'));
	if(isset($_GET['showstats']) && $_GET['showstats']) $editable = false;

	$timeformat = get_option('date_format').' '.get_option('time_format');
	$timeoffset = mymail('helper')->gmt_offset(true);

?>


<?php if($editable) :?>
<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row"><?php _e('Subject', 'mymail') ?></th>
			<td><input type="text" class="widefat" value="<?php echo esc_attr($this->post_data['subject']); ?>" name="mymail_data[subject]" id="mymail_subject" aria-label="<?php esc_attr_e('Subject', 'mymail') ?>"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Preheader', 'mymail') ?></th>
			<td><input type="text" class="widefat" value="<?php echo esc_attr($this->post_data['preheader']); ?>" name="mymail_data[preheader]" id="mymail_preheader" aria-label="<?php esc_attr_e('Preheader', 'mymail') ?>"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('From Name', 'mymail') ?> <a class="default-value mymail-icon" data-for="mymail_from-name" data-value="<?php echo esc_attr(mymail_option('from_name')) ?>" title="<?php _e('restore default', 'mymail') ?>"></a></th>
			<td><input type="text" class="widefat" value="<?php echo esc_attr($this->post_data['from_name']); ?>" name="mymail_data[from_name]" id="mymail_from-name" aria-label="<?php esc_attr_e('From Name', 'mymail') ?>"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('From Email', 'mymail') ?> <a class="default-value mymail-icon" data-for="mymail_from" data-value="<?php echo esc_attr(mymail_option('from')) ?>" title="<?php _e('restore default', 'mymail') ?>"></a></th>
			<td><input type="email" class="widefat" value="<?php echo esc_attr($this->post_data['from_email']); ?>" name="mymail_data[from_email]" id="mymail_from" aria-label="<?php esc_attr_e('From Email', 'mymail') ?>"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('reply-to email', 'mymail') ?> <a class="default-value mymail-icon" data-for="mymail_reply_to" data-value="<?php echo esc_attr(mymail_option('reply_to')) ?>" title="<?php _e('restore default', 'mymail') ?>"></a></th>
			<td><input type="email" class="widefat" value="<?php echo esc_attr($this->post_data['reply_to']); ?>" name="mymail_data[reply_to]" id="mymail_reply_to" aria-label="<?php esc_attr_e('reply-to email', 'mymail') ?>"></td>
		</tr>
	 </tbody>
</table>
<input type="hidden" value="<?php echo esc_attr($this->get_template()) ?>" name="mymail_data[template]" id="mymail_template_name">
<input type="hidden" value="<?php echo esc_attr($this->get_file()) ?>" name="mymail_data[file]" id="mymail_template_file">


<?php else : ?>
<?php
	$totals = $post->post_status != 'autoresponder' ? $this->get_totals($post->ID) : $this->get_sent($post->ID);
	$sent = $this->get_sent($post->ID);

	$errors = $this->get_errors($post->ID);

	$opens = $this->get_opens($post->ID);
	$opens_total = $this->get_opens($post->ID, true);
	$clicks = $this->get_clicks($post->ID);
	$clicks_total = $this->get_clicks($post->ID, true);
	$unsubscribes = $this->get_unsubscribes($post->ID);
	$bounces = $this->get_bounces($post->ID);

?>

<table>
	<tr><th width="16.666%"><?php _e('Subject', 'mymail') ?></th><td><strong><?php echo $this->post_data['subject'];?></strong></td></tr>
<?php if($post->post_status != 'autoresponder') :?>
	<tr><th><?php _e('Date', 'mymail') ?></th><td><?php echo date($timeformat,  $this->post_data['timestamp']+$timeoffset);
	if($post->post_status == 'finished') :
		echo ' &ndash; '.date($timeformat, $this->post_data['finished']+$timeoffset);
		echo ' ('.sprintf(__('took %s', 'mymail'), human_time_diff( $this->post_data['timestamp'], $this->post_data['finished'] )).')';
	endif;
	?>
	</td></tr>
<?php endif; ?>
	<tr><th><?php _e('Preheader', 'mymail') ?></th><td><?php echo $this->post_data['preheader'] ? $this->post_data['preheader'] : '<span class="description">'.__('no preheader', 'mymail').'</span>' ?></td></tr>
</table>


<ul id="stats">
	<li class="receivers">
		<label class="recipients-limit"><span class="verybold hb-sent"><?php echo number_format_i18n($sent); ?></span> <?php echo ($post->post_status == 'autoresponder') ? __('sent', 'mymail') : _n('receiver', 'receivers', $sent, 'mymail') ?></label>
	</li>
	<li>
		<div id="stats_open" class="piechart" data-percent="<?php echo $this->get_open_rate($post->ID)*100 ?>"><span>0</span>%</div>
		<label class="show-open"><span class="verybold hb-opens"><?php echo number_format_i18n($opens); ?></span> <?php echo _n('opened', 'opens', $opens, 'mymail') ?></label>
	</li>
	<li>
		<div id="stats_click" class="piechart" data-percent="<?php echo $this->get_click_rate($post->ID)*100 ?>"><span>0</span>%</div>
		<label class="show-click"><span class="verybold hb-clicks"><?php echo number_format_i18n($clicks); ?></span> <?php echo _n('click', 'clicks', $clicks, 'mymail') ?></label>
	</li>
	<li>
		<div id="stats_unsubscribes" class="piechart" data-percent="<?php echo $this->get_unsubscribe_rate($post->ID)*100 ?>"><span>0</span>%</div>
		<label class="show-unsubscribes"><span class="verybold hb-unsubs"><?php echo number_format_i18n($unsubscribes); ?></span> <?php echo _n('unsubscribe', 'unsubscribes', $unsubscribes, 'mymail') ?></label>
	</li>
	<li>
		<div id="stats_bounces" class="piechart" data-percent="<?php echo $this->get_bounce_rate($post->ID)*100 ?>"><span>0</span>%</div>
		<label class="show-bounces"><span class="verybold hb-bounces"><?php echo number_format_i18n($bounces); ?></span> <?php echo _n('bounce', 'bounces', $bounces, 'mymail') ?></label>
	</li>
</ul>

<table>

	<tr><th><?php ($post->post_status == 'autoresponder') ?  _e('Total Sent', 'mymail') : _e('Total Recipients', 'mymail') ?></th><td class="nopadding"> <span class="big hb-totals"><?php echo number_format_i18n($totals); ?></span>
	<?php if(!in_array($post->post_status, array('finished', 'autoresponder'))) echo '(<span class="hb-sent">'.number_format_i18n($sent).'</span> '.__('sent', 'mymail').')'; ?>
	<?php if(!empty($sent)) :?>
		<a href="#" id="show_recipients" class="alignright mymail-icon showdetails"><?php _e('details' ,'mymail') ?></a>
		<span class="spinner" id="recipients-ajax-loading"></span><div class="ajax-list" id="recipients-list"></div>
	<?php endif; ?>
	</td></tr>
<?php if(!empty($errors)) :?>
	<tr><th><?php _e('Total Errors', 'mymail') ?></th><td class="nopadding"> <span class="big hb-errors"><?php echo number_format_i18n($errors) ?></span>
	<?php if(!empty($errors)) :?>
		<a href="#" id="show_errors" class="alignright mymail-icon showdetails"><?php _e('details' ,'mymail') ?></a>
		<span class="spinner" id="error-ajax-loading"></span><div class="ajax-list" id="error-list"></div>
	<?php endif; ?>
	</td></tr>
<?php endif; ?>
	<tr><th><?php _e('Total Clicks', 'mymail') ?></th><td class="nopadding"> <span class="big hb-clicks_total"><?php echo number_format_i18n($clicks_total) ?></span>
	<?php if(!empty($clicks_total)) :?>
		<a href="#" id="show_clicks" class="alignright mymail-icon showdetails"><?php _e('details' ,'mymail') ?></a>
		<span class="spinner" id="clicks-ajax-loading"></span><div class="ajax-list" id="clicks-list"></div>
	<?php endif; ?>
	</td></tr>
	<?php if($environment = $this->get_environment($post->ID)) :
			$types = array(
				'desktop' => __('Desktop', 'mymail'),
				'mobile' => __('Mobile', 'mymail'),
				'webmail' => __('Web Client', 'mymail'),
			);
	?>
	<tr class="environment"><th><?php _e('Environment', 'mymail') ?></th><td class="nopadding">
		<?php foreach($environment as $type => $data) { ?>
		<label><span class="big"><span class="hb-<?php echo $type ?>"><?php echo round($data['percentage']*100, 2) ?>%</span> <span class="mymail-icon client-<?php echo $type ?>"></span></span> <?php echo isset($types[$type]) ? $types[$type] : __('unknown', 'mymail'); ?></label>
		<?php } ?>
		<a href="#" id="show_environment" class="alignright mymail-icon showdetails"><?php _e('details' ,'mymail') ?></a>
		<span class="spinner" id="environment-ajax-loading"></span><div class="ajax-list" id="environment-list"></div>
	</td></tr>
	<?php endif; ?>
	<?php if($geo_data = $this->get_geo_data($post->ID)) :

			$unknown_cities = array();
			$countrycodes = array();

			foreach($geo_data as $countrycode => $data){
				$x = wp_list_pluck($data, 3);
				if($x) $countrycodes[$countrycode] = array_sum($x);
				if($data[0][3]) $unknown_cities[$countrycode] = $data[0][3];
			}

			arsort($countrycodes);
			$total = array_sum($countrycodes);

	?>
	<tr class="geolocation"><th><?php _e('Geo Location', 'mymail') ?></th><td class="nopadding">
		<span class="hb-geo_location">
		<?php

		$i = 0;

		foreach($countrycodes as $countrycode => $count) { ?>
		<label title="<?php echo mymail('geo')->code2Country($countrycode) ?>"><span class="big"><span class="mymail-flag-24 flag-<?php echo strtolower($countrycode) ?>"></span> <?php echo round($count/$opens*100, 2) ?>%</span></label>
		<?php
			if(++$i >= 5) break;
		} ?>
		</span>
		<a href="#" id="show_geolocation" class="alignright mymail-icon showdetails"><?php _e('details' ,'mymail') ?></a>
		<span class="spinner" id="geolocation-ajax-loading"></span>
		</td></tr><tr><td colspan="2" class="nopadding">
		<div class="ajax-list countries" id="geolocation-list"></div>
	</td></tr>

	<?php endif; ?>

</table>

<br class="clear">
<?php endif; ?>
<input type="hidden" value="<?php echo !$editable?>" id="mymail_disabled" readonly>
