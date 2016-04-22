<?php
/**
 * The template for /sitemap.
 *
 *
 * @package livetechv5
 */

get_header(); ?>

<div  class="page-wrapper">
<div class="row">

	
<div id="content" class="large-12 columns sitemap" role="main">
<div class="row">
	<div class="small-12 large-8 columns">
		<h3>Store</h3>
		<?php wp_nav_menu( array(
		    'menu' => 'Shop Menu'
		) );
		?>
	</div>
	<div class="small-12 large-4 columns" style="border-left: 1px solid #ccc;">
		<h3>Resources</h3>
		<?php wp_nav_menu( array(
		    'menu' => 'Footer Special Links'
		) );
		?>
		<?php wp_nav_menu( array(
		    'menu' => 'LT-Nav'
		) );
		?>
	</div>
</div>

</div><!-- #content -->

</div><!-- .row -->
</div><!-- .page-wrapper -->


<?php get_footer(); ?>