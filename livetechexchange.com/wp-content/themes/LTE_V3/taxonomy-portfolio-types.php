<?php
/**
 * Taxanomy Portfolio Types
 *
 * @package Doover
 * @author Muffin Group
 */

get_header(); ?>

<!-- Content -->
<div id="Content">
	<div class="Wrapper">

		<div class="content">
				
			<!-- Select category -->
			<?php
			$menu_args = array(
				'taxonomy' => 'portfolio-types',
				'orderby' => 'name',
				'show_count' => 1, // 1 for yes, 0 for no
				'hierarchical' => 1, // 1 for yes, 0 for no
				'hide_empty' => 0, // 1 for yes, 0 for no
				'title_li' => '',
				'depth' => 1,
				'walker' => new New_Walker_Category()
			);
			?>
			
			<div class="select_category">
				<h5><?php _e('Select category:','doover'); ?></h5>
				<ul>
					<?php
						$portfolio_page_id = doover_get_option( 'portfolio_page' );
					
						if( get_the_ID() == $portfolio_page_id ) {
							$current_class = ' class="current-cat"';
						} else {
							$current_class = "";
						}
					
						if( $portfolio_page_id ) {
							echo '<li'.$current_class.'><a href="'.get_page_link( $portfolio_page_id ).'"><span>';
							_e('All','doover');
							echo '</span></a></li>';
						}
					
						wp_list_categories( $menu_args ); 
					?>
				</ul>
			</div>
					
			<?php 	
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$posts_per_page = doover_get_option( 'portfolio_per_page', 10 );
			
			$args = array( 
				'post_type' => 'portfolio',
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'taxonomy' => 'portfolio-types',
				'ignore_sticky_posts' =>1,
				'orderby' => 'menu_order',
				'order' => 'ASC',
			);
			
			global $query_string;
			parse_str( $query_string, $qstring_array );
			$query_args = array_merge( $args,$qstring_array );
			
			$temp = $wp_query;
			$wp_query = null;
			$wp_query = new WP_Query();
			$wp_query->query( $query_args );
			?>

			<?php if( $wp_query->have_posts() ) :  ?>
			
			
				<?php //get_template_part('loops/loop', 'portfolio-'.$loop.''); ?>			
			 	
			 	<?php $portfolio_style = doover_get_option( 'portfolio_style', 'portfolio_1_col', true ); ?>
			 	<div class="portfolio <?php echo $portfolio_style; ?>">
				 	<?php while( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
				 	
				 		<div id="portfolio-item-<?php the_ID(); ?>" class="item">							
							
							<?php if( has_post_thumbnail() ) : ?>
								<div class="photo">
									<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<div class="overlay"></div><span class="ico"></span>
										<?php the_post_thumbnail( $portfolio_style ); ?>
									</a>
								</div>
							<?php endif; ?>
	
							<div class="desc<?php if( ! has_post_thumbnail() ) echo " desc_has_thumbnail";?>">
								<h5><?php the_title(); ?></h5>
								
								<?php the_excerpt(); ?>
								
								<a href="<?php the_permalink(); ?>" class="button button-small button-small-gray rs"><span><?php _e('See details','doover'); ?></span></a>
							</div>
							
						</div>
				 		
					<?php endwhile; ?>
				</div>	 

				<?php doover_pagination(); ?>

			<?php else : ?>
			
				<div class="no_posts">
					<h3><?php _e('Not Found','doover'); ?></h3>
					<p><?php _e('No portfolio items found.','doover'); ?></p>
				</div>
			
			<?php endif; wp_reset_query(); $wp_query = $temp; ?>
		
		</div>
			
	</div>
</div>

<?php get_footer(); ?>