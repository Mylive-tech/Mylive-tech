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
<div id="Photo_slider">
	
	<ul id="os_cycle" class="slider">
		<?php if ($slide_query->have_posts()) : while ($slide_query->have_posts()) : $slide_query->the_post(); ?>
			<li>		
			<?php 	

				if( $video['id'] = get_post_meta($post->ID, 'doover_video_youtube', true) ) {
					$video["width"] = ( get_post_meta($post->ID, 'doover_video_width', true) ) ? get_post_meta($post->ID, 'doover_video_width', true) : 950;
					$video["height"] = ( get_post_meta($post->ID, 'doover_video_height', true) ) ? get_post_meta($post->ID, 'doover_video_height', true) : 315;
					echo '<iframe src="http://www.youtube.com/embed/'. $video['id'] .'?wmode=opaque" width="'. $video["width"] .'" height="'. $video["height"] .'" frameborder="0"></iframe>';
				
				} elseif ( $video['id'] = get_post_meta($post->ID, 'doover_video_vimeo', true) ) {
					$video["width"] = ( get_post_meta($post->ID, 'doover_video_width', true) ) ? get_post_meta($post->ID, 'doover_video_width', true) : 950;
					$video["height"] = ( get_post_meta($post->ID, 'doover_video_height', true) ) ? get_post_meta($post->ID, 'doover_video_height', true) : 315;
					echo '<iframe src="http://player.vimeo.com/video/'. $video['id'] .'" width="'. $video["width"] .'" height="'. $video["height"] .'" frameborder="0"></iframe>';
				
				} else {
					the_post_thumbnail( 'photo_slider' );
				}
						
			?>		
			</li>
		<?php endwhile; endif; wp_reset_query(); ?>
	</ul>	
	<ul id="os_pager" class="controls">&nbsp;</ul>		

</div>

<?php wp_reset_query(); ?>