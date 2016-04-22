<?php
/**
 * The Template for displaying all single posts.
 *

 */
get_header(); ?>

<!-- Content -->
<div id="Content" class="with_aside right_submenu">
	<div class="Wrapper">
	
		<div class="content">
		
			<?php while ( have_posts() ) : the_post(); ?>
			
				<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
					
					<div class="desc">
						
						<h3><?php the_title(); ?></h3>						
						<?php the_content( false ); ?>		
							
					</div>
					
				</div>
	
			<?php endwhile; // end of the loop. ?>

		</div>
		
		<!-- Sidebar -->
		<div class="sidebar">
			<?php get_sidebar( 'blog' ); ?>
		</div>
		
	</div>
</div>

<?php get_footer(); ?>