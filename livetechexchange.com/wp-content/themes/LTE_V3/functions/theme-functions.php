<?php
/**
 * Theme functions.
 */


/* ---------------------------------------------------------------------------
 * Theme support
 * --------------------------------------------------------------------------- */
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support('automatic-feed-links');
//	add_editor_style( '/css/style-editor.css' );
}

//Remove VFB Pro jquery UI
wp_dequeue_style( 'http://www.livetechexchange.com/wp-content/plugins/visual-form-builder-pro/css/smoothness/jquery-ui-1.10.3.min.css' );


/*-----------------------------------------------------------------------------------*/
/* Post Thumbnails
/*-----------------------------------------------------------------------------------*/
if ( function_exists( 'add_theme_support' ) ) {
	
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 538, 128, true );
	add_image_size( 'post_no_meta', 678, 128, true );
	
	add_image_size( 'list_thumbnail', 50, 50, true );
	add_image_size( 'zoom', 800, 600, false );
	
	add_image_size( 'offer_slider', 530, 370, false );
	add_image_size( 'offer_slider_thumb', 120, 60, false );
	
	add_image_size( 'photo_slider', 950, 315, false );
	add_image_size( 'photo_slider_crop', 950, 315, true );
	
	add_image_size( 'portfolio_1_col', 428, 240, true );
	add_image_size( 'portfolio_2_cols', 428, 240, true );
	add_image_size( 'portfolio_3_cols', 268, 180, true );	
}


/* ---------------------------------------------------------------------------
 * Pagination for Blog and Portfolio
 * --------------------------------------------------------------------------- */
function doover_pagination()
{
	global $paged, $wp_query;

	if( empty( $paged ) ) $paged = 1;
	$prev = $paged - 1;							
	$next = $paged + 1;	

	if( ! $pages = $wp_query->max_num_pages )
	{
		$pages = 1;
	}
	
	if( $pages > 1 )
	{
		echo "<div class='pager'>\n";
		echo "<center>\n";
		
		if( $paged >1 ){
			echo "<a class='button-small button-small-select prev' href='". previous_posts(false) ."'><span>";
			_e('Prev page','doover');
			echo "</span></a>\n";
		}

		for( $i=1; $i <= $pages; $i++ ){
			if( $paged == $i ){
				$class = " active";	
			} else {
				$class = "";
			}
			echo "<a href='".get_pagenum_link($i)."' class='page".$class."'>".$i."</a>\n";
		}
		
		if( $paged < $pages ){
			echo "<a class='button-small button-small-select next' href='". next_posts(0,false) ."'><span>";
			_e('Next page','doover');
			echo "</span></a>\n";
		}

		echo "</center>\n";
		echo "</div>\n";
	}
}


/* ---------------------------------------------------------------------------
 * Remove auto <p> and <br/> from shortcodes
 * --------------------------------------------------------------------------- */
function muffin_remove_autop( $content ) 
{ 
	$content = do_shortcode( shortcode_unautop( $content ) ); 
	$content = preg_replace( '#^<\/p>|^<br\s?\/?>|<p>$|<p>\s*(&nbsp;)?\s*<\/p>#', '', $content );
	$content = preg_replace( '|\n</p>$|', '</p>', $content );
	$content = preg_replace( '#\<br*.?\/\>#is', '', $content );	
	return $content;
}


/* ---------------------------------------------------------------------------
 * No sidebar message for themes with sidebar 
 * --------------------------------------------------------------------------- */
function doover_nosidebar()
{
	echo 'This template supports the sidebar\'s widgets. <a href="'. home_url() .'/wp-admin/widgets.php">Add one</a> or use Default Template.';	
}


/* ---------------------------------------------------------------------------
 * New Walker Category for categories menu
 * --------------------------------------------------------------------------- */
class New_Walker_Category extends Walker_Category {
	function start_el(&$output, $category, $depth, $args) {
		extract($args);

		$cat_name = esc_attr( $category->name );
		$cat_name = apply_filters( 'list_cats', $cat_name, $category );
		
		$link = '<a href="' . esc_attr( get_term_link($category) ) . '" ';
		if ( $use_desc_for_title == 0 || empty($category->description) )
			$link .= 'title="' . esc_attr( sprintf(__('View all posts filed under %s','doover'), $cat_name) ) . '"';
		else
			$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
		$link .= '><span>';
		$link .= $cat_name;

		if ( !empty($show_count) )
			$link .= ' (' . intval($category->count) . ')';
			
		$link .= '</span></a>';

		if ( 'list' == $args['style'] ) {
			$output .= "\t<li";
			$class = 'cat-item cat-item-' . $category->term_id;
			if ( !empty($current_category) ) {
				$_current_category = get_term( $current_category, $category->taxonomy );
				if ( $category->term_id == $current_category )
					$class .=  ' current-cat';
				elseif ( $category->term_id == $_current_category->parent )
					$class .=  ' current-cat-parent';
			}
			$output .=  ' class="' . $class . '"';
			$output .= ">$link\n";
		} else {
			$output .= "\t$link\n";
		}
	}
}


/* ---------------------------------------------------------------------------
 * Current page URL
 * --------------------------------------------------------------------------- */
function curPageURL() {
	$pageURL = 'http';
	if ( key_exists("HTTPS", $_SERVER) && ( $_SERVER["HTTPS"] == "on" ) ){
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}


/* ---------------------------------------------------------------------------
 * Using default value for first table from second table when the value is empty 
 * --------------------------------------------------------------------------- */
function doover_array_defaults( $source, $defaults ){
	if( is_array($source) && is_array($defaults) ){
		foreach( $source as $key => $value ){
			if( !$value ){
				$source[$key] = $defaults[$key];
			}
		}
	}
	
	return $source;
}


/* ---------------------------------------------------------------------------
 * Breadcrumbs
 * --------------------------------------------------------------------------- */
function doover_breadcrumbs() {

	global $post;
	$home = __('Home','doover');
	$homeLink = home_url();

	echo '<ul class="breadcrumbs">';
	echo '<li class="home">' . __('You are here:','doover') . '</li>';
	echo '<li><a href="' . $homeLink . '">' . $home . '</a></li>';

	// Blog Category
	if ( is_category() ) {
		echo '<li class="last"><a href="'. curPageURL() .'">' . __('Archive by category','doover').' "' . single_cat_title('', false) . '"</a></li>';

	// Blog Day
	} elseif ( is_day() ) {
		echo '<li><a href="'. get_year_link(get_the_time('Y')) . '">'. get_the_time('Y') .'</a></li>';
		echo '<li><a href="'. get_month_link(get_the_time('Y'),get_the_time('m')) .'">'. get_the_time('F') .'</a></li>';
		echo '<li class="last"><a href="'. curPageURL() .'">'. get_the_time('d') .'</a></li>';

	// Blog Month
	} elseif ( is_month() ) {
		echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
		echo '<li class="last"><a href="'. curPageURL() .'">'. get_the_time('F') .'</a></li>';

	// Blog Year
	} elseif ( is_year() ) {
		echo '<li class="last"><a href="'. curPageURL() .'">'. get_the_time('Y') .'</a></li>';

	// Single Post
	} elseif ( is_single() && !is_attachment() ) {
		
		// Custom post type
		if ( get_post_type() != 'post' ) {
			$post_type = get_post_type_object(get_post_type());
			$slug = $post_type->rewrite;
			
			if( $slug['slug'] == doover_get_option( 'portfolio_item_slug', 'portfolio-item', true ) && $portfolio_page_id = doover_get_option( 'portfolio_page' ) )
			{
				echo '<li><a href="' . get_page_link( $portfolio_page_id ) . '">'. get_the_title( $portfolio_page_id ) .'</a></li>';
			} else {
				echo '<li><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></li>';
			}
			echo '<li class="last"><a href="' . curPageURL() . '">'. wp_title( '',false ) .'</a></li>';
			
		// Blog post	
		} else {
			$cat = get_the_category(); 
			$cat = $cat[0];
			echo '<li>';
			echo get_category_parents($cat, TRUE, '');
			echo '</li>';
			echo '<li class="last"><a href="' . curPageURL() . '">'. wp_title( '',false ) .'</a></li>';
		}

	// Taxonomy
	} elseif( get_post_taxonomies() ) {

		$post_type = get_post_type_object(get_post_type());
		if( $post_type->name == 'portfolio' && $portfolio_page_id = doover_get_option( 'portfolio_page' ) ) {
			echo '<li><a href="' . get_page_link( $portfolio_page_id ) . '">'. get_the_title( $portfolio_page_id ) .'</a></li>';
		}

		echo '<li class="last"><a href="' . curPageURL() . '">'. wp_title( '',false ) .'</a></li>';

	// Page with parent
	} elseif ( is_page() && $post->post_parent ) {
		$parent_id  = $post->post_parent;
		$breadcrumbs = array();
		while ($parent_id) {
			$page = get_page($parent_id);
			$breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
			$parent_id  = $page->post_parent;
		}
		$breadcrumbs = array_reverse($breadcrumbs);
		foreach ($breadcrumbs as $crumb) echo $crumb;
		
		echo '<li class="last"><a href="' . curPageURL() . '">'. get_the_title() .'</a></li>';

	// Default
	} else {
		echo '<li class="last"><a href="' . curPageURL() . '">'. get_the_title() .'</a></li>';
	}

	echo '</ul>';
}


/*-----------------------------------------------------------------------------------*/
/*	Set Max Content Width
/*-----------------------------------------------------------------------------------*/
if ( ! isset( $content_width ) ) $content_width = 950;

?>