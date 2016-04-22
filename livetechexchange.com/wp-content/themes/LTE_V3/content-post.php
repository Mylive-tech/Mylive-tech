<?php
/**
 * The template for displaying content in the single.php template
 *

 */
?>

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
				<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute(); ?>">
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
		
		<?php the_excerpt(); ?>			
		
		<footer>
		
			<?php if( $posts_meta['tags'] && ( $tag_list = get_the_tag_list( '',', ' ) ) ): ?>
				<p class="tags"><span><?php _e('Tags','doover') ?>:</span> <?php echo( $tag_list ); ?></p>
			<?php endif; ?>
			
			<a href="<?php the_permalink(); ?>" class="button button-small button-small-gray rs"><span><?php _e('Read more','doover'); ?></span></a>	
		</footer>
		
	</div>
	
</div>

<?php 
global $wp_query;
if ( 
		( doover_get_option( "posts_per_page", 10, true ) != $wp_query->current_post+1 ) &&
		! ( 
			( $wp_query->max_num_pages == $wp_query->query_vars['paged'] ) &&
			( ( ( $wp_query->found_posts-1 ) % $wp_query->query_vars['posts_per_page'] ) == $wp_query->current_post )
		)
	) {			
	echo "<hr />";		
}
?>