<?php
/**
 * Theme post types.
 *

 */

 
/* ---------------------------------------------------------------------------
 * Create new post type: Slides
 * --------------------------------------------------------------------------- */
function doover_post_type_slides() 
{
	$slider_item_slug = 'slider-item';
	
	$labels = array(
		'name' => __('Slides','doover'),
		'singular_name' => __('Slide','doover'),
		'add_new' => __('Add New','doover'),
		'add_new_item' => __('Add New Slide','doover'),
		'edit_item' => __('Edit Slide','doover'),
		'new_item' => __('New Slide','doover'),
		'view_item' => __('View Slide','doover'),
		'search_items' => __('Search Slides','doover'),
		'not_found' =>  __('No slides found','doover'),
		'not_found_in_trash' => __('No slides found in Trash','doover'), 
		'parent_item_colon' => ''
	  );	
	  
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'rewrite' => array( 'slug' => $slider_item_slug, 'with_front'=>true ),
		'supports' => array( 'title', 'thumbnail', 'excerpt', 'page-attributes' ),
	  ); 
	  
	  register_post_type( 'slide' ,$args);
}
add_action( 'init', 'doover_post_type_slides' );


/* ---------------------------------------------------------------------------
 * Slider edit columns
 * --------------------------------------------------------------------------- */
function doover_edit_columns_slide($columns)
{
	$newcolumns = array(
		"cb" => "<input type=\"checkbox\" />",
		"slider_thumbnail" => __('Thumbnail','doover'),
		"title" => 'Title',
		"slider_order" => __('Order','doover'),
	);
	$columns= array_merge($newcolumns, $columns);	
	
	return $columns;
}
add_filter("manage_edit-slide_columns", "doover_edit_columns_slide");  


/* ---------------------------------------------------------------------------
 * Slider custom columns
 * --------------------------------------------------------------------------- */
function doover_custom_columns_slide($column)
{
	global $post;
	switch ($column)
	{
		case "slider_thumbnail":
			if ( has_post_thumbnail() ) { the_post_thumbnail('list_thumbnail'); }
			break;	
		case "slider_order":
			echo $post->menu_order;
			break;	
	}
}
add_action("manage_posts_custom_column",  "doover_custom_columns_slide"); 


/* ---------------------------------------------------------------------------
 * Create new post type: Portfolio
 * --------------------------------------------------------------------------- */
function doover_post_type_portfolio() 
{
	$portfolio_item_slug = doover_get_option( 'portfolio_item_slug', 'portfolio-item', true );
	
	$labels = array(
		'name' => __('Portfolio','doover'),
		'singular_name' => __('Portfolio item','doover'),
		'add_new' => __('Add New','doover'),
		'add_new_item' => __('Add New Portfolio item','doover'),
		'edit_item' => __('Edit Portfolio item','doover'),
		'new_item' => __('New Portfolio item','doover'),
		'view_item' => __('View Portfolio item','doover'),
		'search_items' => __('Search Portfolio items','doover'),
		'not_found' =>  __('No portfolio items found','doover'),
		'not_found_in_trash' => __('No portfolio items found in Trash','doover'), 
		'parent_item_colon' => ''
	  );
		
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'rewrite' => array( 'slug' => $portfolio_item_slug, 'with_front'=>true ),
		'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
	); 
	  
	register_post_type( 'portfolio' ,$args);
	  
	register_taxonomy('portfolio-types','portfolio',array(
		'hierarchical' => true, 
		'label' =>  __('Portfolio categories','doover'), 
		'singular_label' =>  __('Portfolio category','doover'), 
		'rewrite' => true,
		'query_var' => true
	));
}
add_action( 'init', 'doover_post_type_portfolio' );


/* ---------------------------------------------------------------------------
 * Portfolio edit columns
 * --------------------------------------------------------------------------- */
function doover_edit_columns_portfolio($columns)
{
	$newcolumns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => 'Title',
		"portfolio_types" => __('Categories','doover'),
		"portfolio_order" =>  __('Order','doover'),
	);
	$columns= array_merge($newcolumns, $columns);	
	
	return $columns;
}
add_filter("manage_edit-portfolio_columns", "doover_edit_columns_portfolio");  


/* ---------------------------------------------------------------------------
 * Portfolio custom columns
 * --------------------------------------------------------------------------- */
function doover_custom_columns_portfolio($column)
{
	global $post;
	switch ($column)
	{
		case "portfolio_types":
			echo get_the_term_list($post->ID, 'portfolio-types', '', ', ','');
			break;
		case "portfolio_order":
			echo $post->menu_order;
			break;		
	}
}
add_action("manage_posts_custom_column",  "doover_custom_columns_portfolio"); 


/* ---------------------------------------------------------------------------
 * Potrfolio's categories pagination FIX
 * --------------------------------------------------------------------------- */
function doover_option_posts_per_page( $value ) {
	if ( is_tax( 'portfolio-types') ) {
        $posts_per_page =  doover_get_option( 'portfolio_per_page', 10, true );
    } else {
        $posts_per_page = doover_get_option( 'posts_per_page', 10, true );
    }
    return $posts_per_page;
}

function doover_posts_per_page() {
    add_filter( 'option_posts_per_page', 'doover_option_posts_per_page' ); 
}
add_action( 'init', 'doover_posts_per_page', 0);


/* ---------------------------------------------------------------------------
 * Create new post type: Mega menu
 * @since 1.2
 * --------------------------------------------------------------------------- */
function doover_post_type_megamenu() 
{
	$megamenu_item_slug = 'megamenu-item';
	
	$labels = array(
		'name' => __('Mega Menu','doover'),
		'singular_name' => __('Mega Menu item','doover'),
		'add_new' => __('Add New','doover'),
		'add_new_item' => __('Add New Item','doover'),
		'edit_item' => __('Edit Item','doover'),
		'new_item' => __('New Item','doover'),
		'view_item' => __('View Item','doover'),
		'search_items' => __('Search Items','doover'),
		'not_found' =>  __('No items found','doover'),
		'not_found_in_trash' => __('No items found in Trash','doover'), 
		'parent_item_colon' => ''
	  );	
	  
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'rewrite' => array( 'slug' => $megamenu_item_slug, 'with_front'=>true ),
		'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
	  ); 
	  
	  register_post_type( 'megamenu' ,$args);
}
add_action( 'init', 'doover_post_type_megamenu' );


/* ---------------------------------------------------------------------------
 * Mega menu edit columns
 * @since 1.2
 * --------------------------------------------------------------------------- */
function doover_edit_columns_megamenu($columns)
{
	$newcolumns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => 'Title',
		"megamenu_order" => __('Order','doover'),
	);
	$columns= array_merge($newcolumns, $columns);	
	
	return $columns;
}
add_filter("manage_edit-megamenu_columns", "doover_edit_columns_megamenu");  


/* ---------------------------------------------------------------------------
 * Mega menu custom columns
 * @since 1.2
 * --------------------------------------------------------------------------- */
function doover_custom_columns_megamenu($column)
{
	global $post;
	switch ($column)
	{
		case "megamenu_order":
			echo $post->menu_order;
			break;	
	}
}
add_action("manage_posts_custom_column",  "doover_custom_columns_megamenu"); 

?>