<div class="wrap">
<div id="icon-edit" class="icon32"></div>
<h2><?php _e('Subscribers', 'mymail') ?>
<?php if(current_user_can('mymail_add_subscribers' )) : ?>
<a href="edit.php?post_type=newsletter&page=mymail_subscribers&new" class="add-new-h2"><?php _e( 'add New', 'mymail' ); ?></a>
<?php endif; ?>
<?php if(current_user_can('mymail_import_subscribers' )) : ?>
<a href="edit.php?post_type=newsletter&page=mymail_subscriber-manage&tab=import" class="add-new-h2"><?php _e( 'import', 'mymail' ); ?></a>
<?php endif; ?>
<?php if(current_user_can('mymail_export_subscribers' )) : ?>
<a href="edit.php?post_type=newsletter&page=mymail_subscriber-manage&tab=export" class="add-new-h2"><?php _e( 'export', 'mymail' ); ?></a>
<?php endif; ?>
<?php if(isset($_GET['s']) && !empty($_GET['s'])) : ?>
	<span class="subtitle"><?php printf(__('Search result for %s', 'mymail'), '&quot;'.esc_html($_GET['s']).'&quot;') ?></span>
	<?php endif; ?>
</h2>
<?php

		$table = new MyMail_Subscribers_Table();

		$table->prepare_items();
		$table->search_box(__('Search Subscribers', 'mymail'), 's');
		$table->views();
?><form method="post" action=""><?php
		$table->display();
?></form><?php

?>
</div>
