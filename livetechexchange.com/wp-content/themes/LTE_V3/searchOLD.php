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
            <?php
//exclude pages from the loop
if (is_search() && ($post->post_type=='page')) continue; ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
		
					<?php if( has_post_thumbnail() ) : ?>
						<div class="image">
							<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<?php the_post_thumbnail(); ?>
							</a>
						</div>
					<?php endif; ?>
					
					<div class="desc<?php if( has_post_thumbnail() ) echo " desc_has_thumbnail";?>">
						<h3><?php the_title(); ?></h3>						
						<?php the_excerpt(); ?>
						<a href="<?php the_permalink(); ?>" class="button button-small button-small-gray rs"><span><?php _e('See details','doover'); ?></span></a>
					</div>
					
				</div>
				<hr>
	
			<?php endwhile; ?>
			
			<?php doover_pagination(); ?>
			
		</div>
		
		<!-- Sidebar -->
		<div class="sidebar">
			<?php get_sidebar( 'blog' ); ?>		
		</div>
		
	</div>
</div>

<?php get_footer(); ?>