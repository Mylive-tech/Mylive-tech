<?php if($camps = mymail_get_finished_campaigns(array( 'posts_per_page' => 25, 'post_status' => array('finished', 'active')))) : ?>
<div class="mymail-db-campaings loading">
		<h4>
		<select id="mymail-campaign-select">
		<?php foreach($camps as $camp){ ?>
			<option value="<?php echo $camp->ID ?>"><?php echo $camp->post_title ?></option>
		<?php }?>
		</select>
		<?php _e('Campaign', 'mymail') ?>: <a id="camp_name" href="#" title="<?php _e('edit', 'mymail')?>"></a>
		</h4>
	<div class="mymail-db-wrap">
		<ul class="campaign-charts">
			<li><div id="stats_total"></div></li>
			<li><div id="stats_open" class="piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div id="stats_clicks" class="piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div id="stats_unsubscribes" class="piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div id="stats_bounces" class="piechart" data-percent="0"><span>0</span>%</div></li>
		</ul>
		<ul class="labels">
			<li><label><?php _e('total', 'mymail'); ?></label></li>
			<li><label><?php _e('opens', 'mymail'); ?></label></li>
			<li><label><?php _e('clicks', 'mymail'); ?></label></li>
			<li><label><?php _e('unsubscribes', 'mymail'); ?></label></li>
			<li><label><?php _e('bounces', 'mymail'); ?></label></li>
		</ul>
	</div>
 	<span class="loader"></span>
</div>
<?php else : ?>
<div class="mymail-welcome-panel">
		<h4><?php _e('Woha! Seems you havn\'t sent any campaign yet!', 'mymail' ); ?></h4>
		<ul>
			<li><a href="post-new.php?post_type=newsletter" class="mymail-create-campaign"><?php _e('Create a new Campaign', 'mymail') ?></a></li>
			<li><a href="edit.php?post_type=newsletter" class="mymail-check-campaigns"><?php _e('Check out existing Campaigns', 'mymail') ?></a></li>
		</ul>
	</div>
<?php endif; ?>
<?php if($subscribers = mymail('subscribers')->get_totals()) : ?>
<div class="mymail-db-subscribers loading">
		<h4><?php _e('Subscriber Grows', 'mymail') ?>
		<select id="mymail-subscriber-range">
			<option value="7 days"><?php _e('7 days', 'mymail' ); ?></option>
			<option value="30 days"><?php _e('30 days', 'mymail' ); ?></option>
			<option value="3 month"><?php _e('3 month', 'mymail' ); ?></option>
			<option value="1 year"><?php _e('1 year', 'mymail' ); ?></option>
		</select>
		</h4>
	<div class="mymail-db-wrap">
		<div id="subscriber-chart-wrap">
		<canvas class="subscriber-charts" id="subscriber-chart"></canvas>
		</div>
		<div id="mymail-chart-tooltip">
			<u></u><i></i>
			<div></div>
		</div>
 	</div>
 	<span class="loader"></span>
 </div>
<?php else : ?>
<div class="mymail-welcome-panel">
		<h4><?php _e('You have no subscribers yet!', 'mymail' ); ?></h4>
		<ul>
			<li><a href="edit.php?post_type=newsletter&page=mymail_subscribers&new" class="mymail-add-subscriber"><?php _e('Add a single Subscriber', 'mymail') ?></a></li>
			<li><a href="edit.php?post_type=newsletter&page=mymail_subscriber-manage" class="mymail-import-subscribers"><?php _e('Import your existing Subscribers', 'mymail') ?></a></li>
			<li><a href="edit.php?post_type=newsletter&page=mymail_forms&new" class="mymail-add-form"><?php _e('Create a Form to engage new Subscribers', 'mymail') ?></a></li>
		</ul>
	</div>
<?php endif; ?>


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
</div><?php wp_nonce_field( 'mymail_nonce', 'mymail_dashboard_nonce', false ); ?>
