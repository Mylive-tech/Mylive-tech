<?php
/**
 * Template Name: Homepage Template
 * Description: The Homepage Template
 */

get_header(); ?>

<!-- Content -->
<div id="Content">
	<div class="Wrapper">
	
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'page' ); ?>
		<?php endwhile; ?>
		
        <div class="share">
							<span class='st_sharethis_hcount' displayText='ShareThis'></span>
							<span class='st_facebook_hcount' displayText='Facebook'></span>
							<span class='st_twitter_hcount' displayText='Tweet'></span>
							<span class='st_linkedin_hcount' displayText='LinkedIn'></span>
                            <span class='st_pinterest_hcount' displayText='Pinterest'></span>
							<span class='st_email_hcount' displayText='Email'></span>
                            <div class="fb-like" data-href="http://facebook.com/livetechexchange" data-send="true" data-width="300" data-show-faces="true"></div>
						</div>
	</div>
</div>

<?php get_footer(); ?>