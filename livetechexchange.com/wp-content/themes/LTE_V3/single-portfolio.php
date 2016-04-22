<?php
/**
 * The Template for displaying all single posts.
 *

 */
get_header(); ?>

<!-- Content -->
<div id="Content">
	<div class="Wrapper">
	
		<div class="content">
		
			<div class="portfolio portfolio_1_col ?>">
			 	
			 	<?php while ( have_posts() ) : the_post(); ?>
			 	
			 		<div id="portfolio-item-<?php the_ID(); ?>" class="item">							
						
						<?php if( has_post_thumbnail() ) : ?>
							<div class="photo">
								<?php $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large'); ?>
								<a class="fancybox" href="<?php echo $large_image_url[0] ?>" title="<?php the_title_attribute(); ?>">
									<div class="overlay"></div><span class="ico"></span>
									<?php the_post_thumbnail( 'portfolio_1_col' ); ?>
								</a>
							</div>
						<?php endif; ?>

						<div class="desc<?php if( ! has_post_thumbnail() ) echo " desc_has_thumbnail";?>">
							<h5><?php the_title(); ?></h5>
							<?php the_content( false ); ?>
						</div>
						
					</div>
			 		
				<?php endwhile; ?>
				
			</div>
		</div>
		
	</div>
</div>

<?php get_footer(); ?>