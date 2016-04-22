<?php
/**
 * The template used for displaying page content in page.php and Page Templates
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class() ?>>	
	<?php the_content(); ?>
</div>