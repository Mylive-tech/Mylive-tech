<?php
/**
 * Theme widhets.
 *

 */

/* ---------------------------------------------------------------------------
 * Delete unuseful widgets
 * --------------------------------------------------------------------------- */
function doover_unregister_widget()
{
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_RSS');
}
add_action('widgets_init', 'doover_unregister_widget');


/* ---------------------------------------------------------------------------
 * New widgets
 * --------------------------------------------------------------------------- */
function doover_register_widget()
{
	register_widget('Doover_Menu_Widget');
}
add_action('widgets_init','doover_register_widget');


/* ---------------------------------------------------------------------------
 * Add custom sidebars
 * --------------------------------------------------------------------------- */
function doover_register_sidebars() {

	
	// siebars ---------------------------------------------------------------------------
	$sidebars = array(
		'blog' => __('Blog','doover'),
		'page' => __('Page','doover'),
		'homepage' => __('Homepage','doover'),
//		'portfolio' => __('Portfolio','doover'),
	);
	
	foreach ($sidebars as $key => $sidebar)
	{	
		register_sidebar( array (
			'name' => $sidebar .' '. __('sidebar','doover'),
			'id' => 'sidebar-'. $key,
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>",
			'before_title' => '<h5 class="widget-title">',
			'after_title' => '</h5>',
		));
	}
	
	// footer areas ---------------------------------------------------------------------------
	for ($i = 1; $i <= 3; $i++)
	{
		register_sidebar(array(
			'name' => __('Footer area','doover') .' #'.$i,
			'id' => 'footer-area-'.$i,
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>",
			'before_title' => '<h5 class="widget-title">',
			'after_title' => '</h5>',
		));
	}

}
add_action( 'widgets_init', 'doover_register_sidebars' );

?>