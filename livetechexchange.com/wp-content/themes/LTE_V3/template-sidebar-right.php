<?php
/**
 * Template Name: Right Sidebar Template
 * Description: A Page Template that adds a sidebar to pages
 *

 */

get_header(); ?>

<!-- Content -->
<div id="Content" class="with_aside right_submenu">
	<div class="Wrapper">

		<div class="content">
		
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; ?>   
            
             <div class="share">
							<span class='st_sharethis_hcount' displayText='ShareThis'></span>
							<span class='st_facebook_hcount' displayText='Facebook'></span>
							<span class='st_twitter_hcount' displayText='Tweet'></span>
                            <span class="st_plusone_hcount" displayText='+1 This'></span>
							<span class='st_linkedin_hcount' displayText='LinkedIn'></span>
                            <span class='st_pinterest_hcount' displayText='Pinterest'></span>
							<span class='st_email_hcount' displayText='Email'></span>
                            <div class="fb-like" data-href="http://facebook.com/livetechexchange" data-send="true" data-width="300" data-show-faces="true"></div>
						</div>
                
		</div>
		
		<div class="sidebar">
			<?php get_sidebar(); ?>	
		</div>
	
    </div>
     
</div>

<?php get_footer(); ?>