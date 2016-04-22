<?php
/**
 * Template Name: Sitemap
 */

get_header(); ?>

<!-- Content -->
<div id="Content">
	<div class="Wrapper">
	
		<div class="content">
		
			<?php if ( have_posts() ) : the_post(); ?>
				<div <?php post_class() ?> id="post-<?php the_ID(); ?>">	
					<?php the_content(); ?>
					<ul>
					    <?php wp_list_pages("title_li="); ?>
					</ul>
				</div>
			<?php else: ?>
				<ul>
				    <?php wp_list_pages("title_li="); ?>
				</ul>					
			<?php endif; ?>
			
		</div>
		
	</div>
</div>

<?php get_footer(); ?>