<?php
/**
 * The template for displaying all pages.
 */

get_header(); ?>

<!-- Content -->
<div id="Content">
	<div class="Wrapper">
		
		<div class="content">
               			<div class="image">
								<?php $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large'); ?>
								<a class="fancybox" href="<?php echo $large_image_url[0] ?>" title="<?php the_title_attribute(); ?>">
									<?php 
										if( $has_meta ):
											the_post_thumbnail(); 
										else:
											the_post_thumbnail('post_no_meta');
										endif;
									?>
								</a>
							</div>
				
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; ?>

<div class="share">
							<span class='st_sharethis_hcount' displayText='ShareThis'></span>
							<span class='st_facebook_hcount' displayText='Facebook'></span>
							<span class='st_twitter_hcount' displayText='Tweet'></span>
							<span class='st_linkedin_hcount' displayText='LinkedIn'></span>
							<span class='st_email_hcount' displayText='Email'></span>
						</div>

		</div>	
		
	</div>
</div>

<?php get_footer(); ?>