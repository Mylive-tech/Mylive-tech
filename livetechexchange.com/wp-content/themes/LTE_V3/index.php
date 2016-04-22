<?php
/**
 * The main template file.
 *

 */
get_header(); ?>

<!-- Content -->
<div id="Content" class="with_aside right_submenu">
	<div class="Wrapper">
	
		<div class="content">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', get_post_type() ); ?>
			<?php endwhile; ?>
			
			<?php 
				if(function_exists( 'doover_pagination' )):
					doover_pagination();
				else:
				?>
					<div class="nav-next"><?php next_posts_link(__('&larr; Older Entries', 'framework')) ?></div>
					<div class="nav-previous"><?php previous_posts_link(__('Newer Entries &rarr;', 'framework')) ?></div>
				<?php
				endif;
			?>
			
		</div>
		
		<!-- Sidebar -->
		<div class="sidebar">
			<?php get_sidebar( 'blog' ); ?>
		</div>
		
	</div>
</div>

<?php get_footer(); ?>