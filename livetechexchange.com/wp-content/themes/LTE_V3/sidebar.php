<?php
/**
 * The Page Sidebar containing the widget area.
 *

 */
?>

<div id="secondary" class="widget-area">

	<?php if ( ! dynamic_sidebar ( 'sidebar-page' ) ):
		doover_nosidebar();				
	endif; ?>

</div>