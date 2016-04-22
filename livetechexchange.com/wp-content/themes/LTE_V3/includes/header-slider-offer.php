<?php 
$slide_args = array( 
	'post_type' => 'slide',
	'posts_per_page' => -1,
	'order' => 'ASC',
    'orderby' => 'menu_order',
);
$slide_query = new WP_Query();
$slide_query->query( $slide_args );

$slide_post_count = $slide_query->post_count;
?>

<!-- Slider -->
<div id="Offer_slider">
	
	<ul id="os_cycle" class="slides">

		<?php if ($slide_query->have_posts()) : while ($slide_query->have_posts()) : $slide_query->the_post(); ?>
			<li>
			
				<div class="image">
				
					<?php 
						
						if( $video['id'] = get_post_meta($post->ID, 'doover_video_youtube', true) ) {
							$video["width"] = ( get_post_meta($post->ID, 'doover_video_width', true) ) ? get_post_meta($post->ID, 'doover_video_width', true) : 510;
							$video["height"] = ( get_post_meta($post->ID, 'doover_video_height', true) ) ? get_post_meta($post->ID, 'doover_video_height', true) : 350;
							echo '<iframe src="http://www.youtube.com/embed/'. $video['id'] .'?wmode=opaque" width="'. $video["width"] .'" height="'. $video["height"] .'" frameborder="0" style="position:absolute; z-index:3;"></iframe>';
						
						} elseif ( $video['id'] = get_post_meta($post->ID, 'doover_video_vimeo', true) ) {
							$video["width"] = ( get_post_meta($post->ID, 'doover_video_width', true) ) ? get_post_meta($post->ID, 'doover_video_width', true) : 510;
							$video["height"] = ( get_post_meta($post->ID, 'doover_video_height', true) ) ? get_post_meta($post->ID, 'doover_video_height', true) : 350;
							echo '<iframe src="http://player.vimeo.com/video/'. $video['id'] .'" width="'. $video["width"] .'" height="'. $video["height"] .'" frameborder="0" style="position:absolute; z-index:3;"></iframe>';
						
						} else {
							the_post_thumbnail( 'offer_slider' );
						}
						
					?>

					<div class="thumbnail" style="display:none">
						<?php if( $doover_thumbnail = get_post_meta($post->ID, 'doover_thumbnail', true) ): ?>
							<img src="<?php echo $doover_thumbnail;?>" alt="" />
						<?php else: ?>
							<?php the_post_thumbnail( 'offer_slider_thumb' ); ?>
						<?php endif; ?>
					</div>
					
				</div>
				
				<div class="title">
					<div style="width:485px; top:0px; left: 475px; position:absolute; z-index:1;">
						<h2><?php the_title(); ?></h2><span class="slider-subtitle"><?php echo get_post_meta($post->ID, 'doover_subtitle', true); ?></span>
					</div>
				</div>	
					
				<div class="desc">	
					<div style="width:485px; top:100px; left: 475px; position:absolute; z-index:4;">
						<?php the_excerpt(); ?>
						<?php if( $doover_link = get_post_meta($post->ID, 'doover_link', true) ): ?>
							<a href="<?php echo $doover_link; ?>" class="button-big button-big-orange"><span><?php echo get_post_meta($post->ID, 'doover_btn_text', true); ?></span></a>
						<?php endif; ?>
					</div>
				</div>
					
			</li>
		<?php endwhile; endif; wp_reset_query(); ?>
	</ul>			

	<div class="controls">
		<div class="header">
			<?php $our_offer =  doover_get_option( 'our_offer' )?>
			<?php if( $our_offer['prefix'] ):?>
				<h3><?php echo $our_offer['prefix']; ?></h3>
			<?php endif; ?>
			<?php if( $our_offer['text'] ):?>
				<h2><?php echo $our_offer['text']; ?></h2>
			<?php endif; ?>
			
			<?php if($slide_post_count > 5) : ?>
				<a id="prev_arrow" href="#"><?php _e('Previous','doover'); ?></a>
				<a id="next_arrow" href="#"><?php _e('Next','doover'); ?></a>
			<?php endif; ?>
		</div>
		<div class="slider-nav-1">
    <a id="pause_slide1" href="#" title="Toggle Pause / Resume"><img src="<?php bloginfo( 'template_url' ); ?>/css/themes/blue/images/pause-slide.png" width="20px" height="16px" alt="Toggle"></a><a id="next_slide1" href="#" title="Next"><img src="<?php bloginfo( 'template_url' ); ?>/css/themes/blue/images/next-slide.png" width="20px" height="16px" alt="Next"></a>
    </div>
		<!-- #os_pager -->				
	</div>

</div>

<?php wp_reset_query(); ?>