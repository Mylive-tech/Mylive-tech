<?php
/**
 * The Blog Sidebar containing the widget area.
 *

 */
?>
	
<div id="secondary" class="widget-area">
	
	<?php if ( ! dynamic_sidebar ( 'sidebar-blog' ) ):
		doover_nosidebar();				
	endif; ?>

</div>