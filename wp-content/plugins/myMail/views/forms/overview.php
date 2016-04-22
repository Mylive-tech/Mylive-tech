<div class="wrap">
<div id="icon-edit" class="icon32"></div>
<h2><?php _e('Forms', 'mymail') ?>
<?php if(current_user_can('mymail_add_forms' )) : ?>
<a href="edit.php?post_type=newsletter&page=mymail_forms&new" class="add-new-h2"><?php _e( 'add New', 'mymail' ); ?></a>
<?php endif; ?>
<?php if(isset($_GET['s']) && !empty($_GET['s'])) : ?>
	<span class="subtitle"><?php printf(__('Search result for %s', 'mymail'), '&quot;'.esc_html($_GET['s']).'&quot;') ?></span>
	<?php endif; ?>
</h2>
<?php

		$table = new MyMail_Forms_Table();

		$table->prepare_items();
		$table->search_box(__('Search Forms', 'mymail'), 's');
		$table->views();
?><form method="post" action=""><?php
		$table->display();
?></form><?php

?>
</div>
