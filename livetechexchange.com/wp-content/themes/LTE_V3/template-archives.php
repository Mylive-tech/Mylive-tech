<?php
/**
 * Template Name: Archives Template
 *
 * @package Doover
 * @author Muffin Group
 */

get_header(); ?>

<!-- Content -->
<div id="Content">
	<div class="Wrapper">
	
		<div class="content">

			<?php if (have_posts()) : the_post();  ?>
				<div class="row clearfix">
					<?php get_template_part( 'content', get_post_type() ); ?>
				</div>
			<?php endif; ?>
			
			<div class="row clearfix">
			
				<div class="col one_fourth">
					<h4><?php _e('Available Pages','doover'); ?></h4>
					<ul>
						<?php wp_list_pages('title_li=&depth=-1' ); ?>
					</ul>
				</div>
				
				<div class="col one_fourth">
					<h4><?php _e('The 20 latest posts','doover'); ?></h4>
					<ul>
						<?php 
							$args = array( 
								'post_type' => array('post'),
								'posts_per_page' => 20
							); 
							$posts_query = new WP_Query( $args );
							while ($posts_query->have_posts()) : $posts_query->the_post();
						?>
						<li><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></li>
						<?php endwhile; wp_reset_query(); ?>
					</ul>
				</div>
				
				<div class="col one_fourth">
					<h4><?php _e('Archives by Subject:','doover'); ?></h4>
					<ul>
					<?php
						$args =  array( 
							'orderby' => 'name',
							'show_count' => 0,
							'hide_empty' => 0,
							'title_li' => '',
							'taxonomy' => 'category'
						); 
						wp_list_categories($args ); 
						?>
					</ul>
				</div>
				
				<div class="col one_fourth last">
					<h4><?php _e('Archives by Month','doover'); ?></h4>
					<ul>
						<?php wp_get_archives('type=monthly'); ?>
					</ul>
				</div>
			
			</div>
			
		</div>
		
	</div>
</div>

<?php get_footer(); ?>