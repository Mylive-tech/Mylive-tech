<?php
/**
 * Menu functions.
 *

 */


/* ---------------------------------------------------------------------------
 * Registers a menu location to use with navigation menus.
 * --------------------------------------------------------------------------- */
register_nav_menu('primary', __('Main menu','doover') );


/* ---------------------------------------------------------------------------
 * Main menu
 * --------------------------------------------------------------------------- */
function doover_wp_nav_menu() 
{
	$args = array( 
		'container' => 'nav',
		'container_id' => 'navigation', 
		'fallback_cb' => 'doover_wp_page_menu', 
		'theme_location' => 'primary',
		'depth' => 3
	);
	
	wp_nav_menu( $args ); 
}

function doover_wp_page_menu() 
{
	$args = array(
		'title_li' => '0',
		'sort_column' => 'menu_order',
		'depth' => 3
	);

	echo '<nav id="navigation">'."\n";
	echo '<ul>'."\n";
	wp_list_pages($args); 
	echo '</ul>'."\n";
	echo '</nav>'."\n";
}


/* ---------------------------------------------------------------------------
 * Mega menu
 * @since 1.2
 * --------------------------------------------------------------------------- */
function doover_megamenu()
{
	$mm_args = array(
		'hierarchical' => 0,
		'post_type' => 'megamenu',
		'order' => 'ASC',
		'orderby' => 'menu_order',
		'posts_per_page' => -1,
	);	
	$mm_pages = get_posts($mm_args);
	
	if( is_array( $mm_pages ) ){
		
		echo '<nav id="megamenu">'."\n";
		echo '<ul>'."\n";
		
		foreach( $mm_pages as $mm_page ){
			echo '<li>'."\n";
			
			$link = get_post_meta($mm_page->ID, 'doover_mm_link', true);
			if( $link ) {
				echo '<a href="'. $link .'">';
			} else {
				echo '<a>';
			}
			
			echo $mm_page->post_title .'</a>'."\n";
			if( $mm_page->post_content ) echo '<ul>'.  do_shortcode($mm_page->post_content) .'</ul>'."\n";
			echo '</li>'."\n";
		}
		
		echo '</ul>'."\n";
		echo '</nav>'."\n";
		
	}
}

function doover_megamenu_dropdown()
{
	$mm_args = array(
		'hierarchical' => 0,
		'post_type' => 'megamenu',
		'order' => 'ASC',
		'orderby' => 'menu_order',
		'posts_per_page' => -1,
	);	
	$mm_pages = get_posts($mm_args);
	
	if( is_array( $mm_pages ) ){
		
		echo '<nav id="responsive_navigation">'."\n";
		echo '<select class="menu dropdown-menu">'."\n";
		
		foreach( $mm_pages as $mm_page ){
			$link = get_post_meta($mm_page->ID, 'doover_mm_link', true);
			echo '<option value="'. $link .'">'. $mm_page->post_title .'</option>'."\n";
		}
		
		echo '</select>'."\n";
		echo '</nav>'."\n";
		
	}
}


?>