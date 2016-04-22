<div class="wrap">
<div id="icon-edit" class="icon32"></div>
<h2><?php _e('Lists', 'mymail') ?>
<?php if(current_user_can('mymail_add_lists' )) : ?>
<a href="edit.php?post_type=newsletter&page=mymail_lists&new" class="add-new-h2"><?php _e( 'add New', 'mymail' ); ?></a>
<?php endif; ?>
</h2>
<?php

		require_once MYMAIL_DIR . 'classes/lists.table.class.php';
		$table = new MyMail_Lists_Table();

		$table->prepare_items();
		//$table->search_box(__('Search Lists', 'mymail'), 's');
		$table->views();
?><form method="post" action=""><?php
		$table->display();
?></form><?php

?>
</div>
