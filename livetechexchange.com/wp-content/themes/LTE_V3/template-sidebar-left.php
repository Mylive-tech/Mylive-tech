<?php
/**
 * Template Name: Left Sidebar Template
 *

 */

get_header(); ?>

<!-- Content -->
<div id="Content" class="with_aside left_submenu">
	<div class="Wrapper">
	
		<div class="sidebar">	
			<?php get_sidebar(); ?>	
		</div>
	
		<div class="content">
		
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; ?>
		
		</div>
		
	</div>
</div>

<?php get_footer(); ?>