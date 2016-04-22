<?php
	global $mymail;

	$currentpage = isset($_GET['tab']) ? $_GET['tab'] : 'import';
	$currentstep = isset($_GET['step']) ? intval($_GET['step']) : 1;

?>
<div class="wrap mymail-manage">
<div class="icon32" id="icon-edit"><br></div>
<h2 class="nav-tab-wrapper">

	<?php if(current_user_can('mymail_import_subscribers')) : ?>
	<a class="nav-tab <?php echo ('import' == $currentpage) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mymail_subscriber-manage&tab=import"><?php _e('Import', 'mymail')?></a>
	<?php endif;?>

	<?php if(current_user_can('mymail_export_subscribers')) : ?>
	<a class="nav-tab <?php echo ('export' == $currentpage) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mymail_subscriber-manage&tab=export"><?php _e('Export', 'mymail')?></a>
	<?php endif;?>

	<?php if(current_user_can('mymail_bulk_delete_subscribers')) : ?>
	<a class="nav-tab <?php echo ('delete' == $currentpage) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mymail_subscriber-manage&tab=delete"><?php _e('Delete', 'mymail')?></a>
	<?php endif;?>

</h2>
<div class="stuffbox">
<?php wp_nonce_field( 'mymail_nonce', 'mymail_nonce', false ); ?>

<?php if('import' == $currentpage && current_user_can('mymail_import_subscribers')) : ?>


	<div class="step1">
		<div class="step1-body">
			<div class="upload-method">
				<h2><?php _e('Upload', 'mymail'); ?></h2>
				<p class="description"><?php _e('upload you subscribers as comma-separated list (CSV)', 'mymail'); ?></p>
				<form enctype="multipart/form-data" method="post" action="<?php echo admin_url('admin-ajax.php?action=mymail_import_subscribers_upload_handler'); ?>">

				<?php mymail('manage')->media_upload_form(); ?>

				</form>
				<br>
			</div>
			<div class="upload-method-or">
				<?php _e('or', 'mymail'); ?>
			</div>
			<div class="upload-method">
				<h2><?php _e('Paste', 'mymail'); ?></h2>
				<p class="description"><?php _e('Copy and paste from your spreadsheet app', 'mymail'); ?></p>
				<textarea id="paste-import" class="widefat" rows="13" placeholder="<?php _e('paste your list here', 'mymail'); ?>">
justin.case@<?php echo $_SERVER['HTTP_HOST']?>; Justin; Case; Custom;
john.doe@<?php echo $_SERVER['HTTP_HOST']?>; John; Doe
jane.roe@<?php echo $_SERVER['HTTP_HOST']?>; Jane; Roe
				</textarea>
			</div>

		</div>
		<div class="clear"></div>
		<h2 class="import-status">&nbsp;</h2>
	</div>

	<div class="step2">
		<h2 class="import-status"></h2>
		<div class="step2-body"></div>
	</div>

<?php if(current_user_can('mymail_import_wordpress_users' )) : ?>

	<div id="wordpress-users">
		<h2><?php _e('WordPress Users', 'mymail'); ?></h2>
		<form id="import_wordpress" method="post">
			<?php

			global $wp_roles;
			$roles = $wp_roles->get_names();

			if(!empty($roles)) :?>
			<div id="wordpress-user-roles">
				<h4><?php _e('Import WordPress users with following roles', 'mymail'); ?></h4>
				<p><label><input type="checkbox" class="wordpress-users-toggle" checked> <?php _e('toggle all', 'mymail'); ?></label></p>
				<ul>
				<?php
				$i = 0;
				foreach($roles as $role => $name){
					if(!($i%8) && $i) echo '</ul><ul>';
					?>
					<li><label><input type="checkbox" name="roles[]" value="<?php echo $role ?>" checked> <?php echo $name ?></label></li>
					<?php
					$i++;
				}
				?>
				</ul>
				<ul>
					<li><label><input type="checkbox" name="no_role" value="1" checked> <?php _e('users without a role', 'mymail'); ?></label></li>
				</ul>
			</div>
			<div id="wordpress-user-meta">
				<?php
					$meta_values = mymail('helper')->get_wpuser_meta_fields();
				?>
				<h4><?php _e('Use following meta values', 'mymail'); ?></h4>
				<p><label><input type="checkbox" class="wordpress-users-toggle"> <?php _e('toggle all', 'mymail'); ?></label></p>
				<ul>
				<?php
				foreach($meta_values as $i => $meta_value){
					if(!($i%8) && $i) echo '</ul><ul>';
					?>
					<li><label><input type="checkbox" name="meta_values[]" value="<?php echo $meta_value ?>"> <?php echo $meta_value ?></label></li>
					<?php
				}
				?>
				</ul>
			</div>
			<?php endif;?>
			<div class="clearfix clear">
				<input type="submit" class="button button-primary button-large" value="<?php _e('Next Step', 'mymail'); ?> →">
			</div>
		</form>
	</div>

<?php endif; ?>


<?php do_action( 'mymail_import_tab' ); ?>


<?php elseif('export' == $currentpage && current_user_can('mymail_export_subscribers')) :?>

		<h2 class="export-status"><?php _e('Export Subscribers', 'mymail') ?></h2>
			<?php
			global $wpdb;

			$lists = mymail('lists')->get(NULL, false);
			$no_list = mymail('lists')->count(false);

			if(!empty($lists)) : ?>

		<div class="step1">
			<form method="post" id="export-subscribers">
			<?php wp_nonce_field('mymail_nonce'); ?>
			<h3>
			<?php _e('which are in one of these lists', 'mymail'); ?>:
			</h3>
			<ul>
			<?php
			mymail('lists')->print_it(NULL, false, 'lists', __('total', 'mymail'), true);
			?>
			</ul>

			<?php if($no_list) :?>
			<ul>
				<li><label><input type="checkbox" name="nolists" value="1" checked> <?php echo __('subscribers not assigned to a list', 'mymail').' <span class="count">('.number_format_i18n($no_list).' '.__('total', 'mymail').')</span>'?></label></li>
			</ul>
			<?php endif; ?>
			<h3>
			<?php _e('and have one of these statuses', 'mymail'); ?>:<br>
			</h3>
			<p>
				<?php foreach(mymail('subscribers')->get_status(NULL, true) as $i => $name ){ ?>
				<label><input type="checkbox" name="status[]" value="<?php echo $i ?>" checked> <?php echo $name; ?> </label>
				<?php } ?>
			</p>
			<p>
				<label><input type="checkbox" name="header" value="1"> <?php _e('include header', 'mymail'); ?> </label>
			</p>
			<p>
				<label><?php _e('Date Format', 'mymail') ?>:
				<select name="dateformat">
					<option value="0">timestamp - (<?php echo current_time('timestamp') ?>)</option>
					<option value="<?php $d = get_option('date_format').' '.get_option('time_format'); echo $d ?>">
					<?php echo $d.' - ('.date($d, current_time('timestamp')).')'; ?>
					</option>
					<option value="<?php $d = get_option('date_format'); echo $d ?>">
					<?php echo $d.' - ('.date($d, current_time('timestamp')).')'; ?>
					</option>
					<option value="<?php $d = 'Y-d-m H:i:s'; echo $d ?>">
					<?php echo $d.' - ('.date($d, current_time('timestamp')).')'; ?>
					</option>
					<option value="<?php $d = 'Y-d-m'; echo $d ?>">
					<?php echo $d.' - ('.date($d, current_time('timestamp')).')'; ?>
					</option>
				</select>
				</label>
			</p>
			<p>
				<label><?php _e('Output Format', 'mymail') ?>:
				<select name="outputformat">
					<option value="csv" selected><?php _e('CSV', 'mymail'); ?></option>
					<option value="html" ><?php _e('HTML', 'mymail'); ?></option>
				</select>
				</label>
			</p>
			<p>
				<label><?php _e('CharSet', 'mymail') ?>:
				<?php $charsets = array(
					'UTF-8' => 'Unicode 8',
					'ISO-8859-1' => 'Western European',
					'ISO-8859-2' => 'Central European',
					'ISO-8859-3' => 'South European',
					'ISO-8859-4' => 'North European',
					'ISO-8859-5' => 'Latin/Cyrillic',
					'ISO-8859-6' => 'Latin/Arabic',
					'ISO-8859-7' => 'Latin/Greek',
					'ISO-8859-8' => 'Latin/Hebrew',
					'ISO-8859-9' => 'Turkish',
					'ISO-8859-10' => 'Nordic',
					'ISO-8859-11' => 'Latin/Thai',
					'ISO-8859-13' => 'Baltic Rim',
					'ISO-8859-14' => 'Celtic',
					'ISO-8859-15' => 'Western European revision',
					'ISO-8859-16' => 'South-Eastern European',
				)?>
				<select name="encoding">
					<?php foreach( $charsets as $code => $region ){ ?>
					<option value="<?php echo $code; ?>"><?php echo $code; ?> - <?php echo $region; ?></option>
					<?php }?>
				</select>
				</label>
			</p>
			<p>
				<label><?php _e('MySQL Server Performance', 'mymail') ?>:
				<select name="performance" class="performance">
					<option value="1000"><?php _e('low', 'mymail'); ?></option>
					<option value="5000" selected><?php _e('normal', 'mymail'); ?></option>
					<option value="10000"><?php _e('high', 'mymail'); ?></option>
					<option value="20000"><?php _e('super high', 'mymail'); ?></option>
					<option value="50000"><?php _e('super extreme high', 'mymail'); ?></option>
				</select>
				</label>
			</p>
			<h3>
			<?php _e('Define order and included columns', 'mymail'); ?>:<br>
			</h3>
			<?php

			$columns = array(
				'email' => mymail_text('email'),
				'hash' => __('Hash', 'mymail'),
				'firstname' => mymail_text('firstname'),
				'lastname' => mymail_text('lastname'),
			);

			$customfields = mymail()->get_custom_fields();

			$meta = array(
				'status' => __('Status', 'mymail'),
				'statuscode' => __('Statuscode', 'mymail'),
				'added' => __('Added', 'mymail'),
				'updated' => __('Updated', 'mymail'),
				//'ip' => __('IP Address', 'mymail'),
				'signup' => __('Signup Date', 'mymail'),
				'ip_signup' => __('Signup IP', 'mymail'),
				'confirm' => __('Confirm Date', 'mymail'),
				'ip_confirm' => __('Confirm IP', 'mymail'),
			);
			?>
			<ul class="export-order">
				<li><input type="checkbox" name="column[]" value="_number"> #</li>
			<?php foreach( $columns as $id => $name ){ ?>
				<li><input type="checkbox" name="column[]" value="<?php echo $id ?>" checked> <?php echo $name ?></li>
			<?php } ?>
			<?php foreach( $customfields as $id => $data ){ ?>
				<li><input type="checkbox" name="column[]" value="<?php echo $id ?>" checked> <?php echo $data['name'] ?></li>
			<?php } ?>
				<li><input type="checkbox" name="column[]" value="_listnames" checked> <?php echo __('Listnames', 'mymail') ?></li>
			<?php foreach( $meta as $id => $name ){ ?>
				<li><input type="checkbox" name="column[]" value="<?php echo $id ?>"> <?php echo $name ?></li>
			<?php } ?>
			</ul>
			<p>
				<input class="button button-large button-primary" type="submit" value="<?php _e('Download Subscribers', 'mymail') ?>" />
			</p>
			</form>
		</div>

		<div class="step2">
			<div class="step2-body"></div>
		</div>

	<?php else : ?>

		<p><?php _e('no subscriber found', 'mymail'); ?></p>

	<?php endif;?>

<?php elseif('delete' == $currentpage && current_user_can('mymail_bulk_delete_subscribers')) :?>

			<h2 class="delete-status"><?php _e('Delete Subscribers', 'mymail') ?></h2>
			<?php
			global $wpdb;

			$lists = mymail('lists')->get(NULL, false);

			$no_list = mymail('lists')->count(false);

			if(!empty($lists) || $no_list) : ?>

			<div class="step1">
				<form method="post" id="delete-subscribers">
				<?php wp_nonce_field('mymail_nonce'); ?>

				<?php if(!empty($lists)) :?>
				<h3>
				<?php _e('which are in one of these lists', 'mymail'); ?>:
				</h3>
				<ul>
				<?php

				mymail('lists')->print_it(NULL, false, 'lists', __('total', 'mymail'));

				?>
				</ul>
				<?php endif; ?>

				<?php if($no_list) :?>
				<ul>
					<li><label><input type="checkbox" name="nolists" value="1"> <?php echo __('subscribers not assigned to a list', 'mymail').' <span class="count">('.number_format_i18n($no_list).' '.__('total', 'mymail').')</span>'?></label></li>
				</ul>
				<?php endif; ?>
				<h3>
				<?php _e('and have one of these statuses', 'mymail'); ?>:<br>
				</h3>
				<p>
					<?php foreach(mymail('subscribers')->get_status(NULL, true) as $i => $name ){ ?>
					<label><input type="checkbox" name="status[]" value="<?php echo $i ?>" checked> <?php echo $name; ?> </label>
					<?php } ?>
				</p>
				<p>
					<label><input type="checkbox" name="remove_lists" value="1"> <?php _e('remove selected lists', 'mymail'); ?> </label>
				</p>
				<p>
					<label><input type="checkbox" name="remove_actions" value="1" checked> <?php _e('remove all actions from affected users', 'mymail'); ?> </label>
				</p>
				<p>
					<input class="button button-large button-primary" type="submit" value="<?php _e('Delete Subscribers permanently', 'mymail') ?>" />
				</p>
				</form>
			</div>
	<?php else : ?>
		<p><?php _e('no subscriber found', 'mymail'); ?></p>

	<?php endif;?>

<?php else : ?>

	<h2><?php _e('You do not have sufficient permissions to access this page.', 'mymail') ?></h2>

<?php endif;?>

	<div id="progress" class="progress"><span class="bar" style="width:0%"><span></span></span></div>

</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
