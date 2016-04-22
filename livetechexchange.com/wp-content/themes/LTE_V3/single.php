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
		
					<?php 
						$posts_meta = doover_get_option( "posts_meta" );
						if( $posts_meta['time'] || $posts_meta['categories'] || $posts_meta['comments'] ){
							$has_meta = true;
						} else {
							$has_meta = false;
						}
					?>
					
					<?php if( $has_meta ): ?>
						
						<div class="meta">
						
							<?php if( $posts_meta['time'] ): ?>
							<div class="date">
								<span class="day"><?php printf( '%1$s', get_the_time('j') ); ?></span>
								<span class="month"><?php printf('%1$s', get_the_time('F') ); ?></span>
								<span class="year"><?php printf( '%1$s', get_the_time('Y') ); ?></span>
							</div>
							<?php endif; ?>
							
							<?php if( $posts_meta['categories'] ): ?>
							<div class="category">
								<span class="label"><?php _e('Category:','doover') ?></span><br />
								<?php echo( get_the_category_list('<br />') ); ?>
							</div>
							<?php endif; ?>
							
							<?php if( $posts_meta['comments'] ): ?>
							<div class="comments">
								<?php _e('Comments:','doover') ?> <?php comments_popup_link( 0, _x( '1', 'comments number', 'doover' ), _x( '%', 'comments number', 'doover' ) ); ?>
							</div>	
							<?php endif; ?>
							
						</div>
					
					<?php endif; ?>	
		
					<?php if( $has_meta ): ?><div class="desc"><?php else:?><div class="desc desc_no_meta"><?php endif;?>	
		
						<h3><?php the_title(); ?></h3>
						
						<?php if( has_post_thumbnail() ) : ?>
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
						<?php endif; ?>
						
						<?php the_content( false ); ?>
						<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages:', 'doover').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>		
						
						<div class="share">
							<span class='st_sharethis_hcount' displayText='ShareThis'></span>
							<span class='st_facebook_hcount' displayText='Facebook'></span>
							<span class='st_twitter_hcount' displayText='Tweet'></span>
							<span class='st_linkedin_hcount' displayText='LinkedIn'></span>
							<span class='st_email_hcount' displayText='Email'></span>
						</div>
						
					</div>

					<?php comments_template( '', true ); ?>
					
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