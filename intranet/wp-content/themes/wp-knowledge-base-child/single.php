<?php
/**
 * The Template for displaying all single posts.
 *
 * @package iPanelThemes Knowledgebase
 */

get_header(); ?>

	<div id="primary" class="content-area col-md-12">
		<main id="main" class="site-main" role="main">

	<?php get_sidebar(); ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php ipt_kb_like_article(); ?>
			
			<?php ipt_kb_content_nav( 'nav-below' ); ?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
