<?php
/**
 * Functions and definitions.
 *
 * PLEASE DON'T MODIFY THIS FILE.
 * Use the provided child theme for all your modifications.
 *
 * For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Framework
 * @subpackage G1_Theme03
 * @since G1_Theme03 1.0.0
 */
function content($limit) {
  $content = explode(' ', get_the_content(), $limit);
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }	
  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content); 
  $content = str_replace(']]>', ']]&gt;', $content);
  return $content;
} 

/* add share this code 
if (function_exists('st_makeEntries')) :
add_shortcode('sharethis', 'st_makeEntries');
endif; */
 
/*******************************************checkout form*************************************/ 
add_filter("woocommerce_checkout_fields", "order_fields");

function order_fields($fields) {

    $order = array(
	    "billing_country",
        "billing_first_name", 
        "billing_last_name", 
		"billing_address_1", 
        "billing_address_2",
        "billing_company",       
		"billing_city", 
        "billing_postcode",
		"billing_state", 
        "billing_email", 
        "billing_phone"

    );
	
	//////////////////////// add shipping fields
	
	$order_ship = array(
	    "shipping_country",
        "shipping_first_name", 
        "shipping_last_name", 
		"shipping_address_1", 
        "shipping_address_2",
        "shipping_company",       
		"shipping_city", 
        "shipping_postcode",
		"shipping_state"

    );
	
	
    foreach($order as $field)
    {
        $ordered_fields[$field] = $fields["billing"][$field];
    }
	
	foreach($order_ship as $fieldd)
    {
        $ordered_fields_ship[$fieldd] = $fields["shipping"][$fieldd];
    }

    $fields["billing"] = $ordered_fields;
	$fields["shipping"] = $ordered_fields_ship;
	
    return $fields;

}
/*********************************************************************************/
// Hook in
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

// Our hooked in function - $address_fields is passed via the filter!
function custom_override_default_address_fields( $address_fields ) {
     $address_fields['address_1']['label'] = 'Address 1';
     $address_fields['address_2']['label'] = 'Address 2';

     return $address_fields;
}
/**********************************on sale product******************************/ 

function woocommerce_sale_products( $atts ){
global $woocommerce_loop;
 
	extract( shortcode_atts( array(
	'per_page' => '12',
	'columns' => '4',
	'orderby' => 'title',
	'order' => 'asc'
	), $atts ) );
	 
	$args = array(
	'post_type' => 'product',
	'post_status' => 'publish',
	'ignore_sticky_posts' => 1,
	'orderby' => $orderby,
	'order' => $order,
	'posts_per_page' => $per_page,
	'meta_query' => array(
	array(
	'key' => '_visibility',
	'value' => array('catalog', 'visible'),
	'compare' => 'IN'
	),
	array(
	'key' => '_sale_price',
	'value' => 0,
	'compare' => '>',
	'type' => 'NUMERIC'
	)
	)
	);
	 
	ob_start();
	 
	$products = new WP_Query( $args );
	 
	$woocommerce_loop['columns'] = $columns;
	 
	if ( $products->have_posts() ) : ?>
	 
	<div class="image_carousel" id="thirdshopbox">
    <div id="foo2" class="ilc_ps_hidden">
	 
	<?php 
	$totalcatnum= 1; 
	while ( $products->have_posts() ) : $products->the_post(); 
	global $product, $woocommerce_loop;
    $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') ); 
    $price = get_post_meta( get_the_ID(), '_regular_price', true);
    $sale = get_post_meta( get_the_ID(), '_sale_price', true); 
	?>
	<div id="foo_content"> 
   
   <?php if($url!='') { ?>
  <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"> <img src="<?php echo $url; ?>"  height="150" width="150" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" />
   <?php }else{ ?>
   <img src="<?php echo get_template_directory_uri(); ?>/images/no-imge.png" height="150" width="150" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" />
   <?php }?>
   <h5 class="g1-h4"><?php the_title(); ?></h5></a>
   <?php  if ( $price_html = $product->get_price_html() ) : ?>
   <span class="ilc_ps_price"><?php echo $price_html; ?></span>
   <?php  endif; ?>
   </div>
	
	
	 
	<?php $totalcatnum++; endwhile; // end of the loop. ?>
	 
	</div>
   <div class="clearfix"></div>
   <div class="ilc_ps_nav ">
    <?php if($totalcatnum > 4) { ?>
    <a class="ilc_ps_prev ilc_ps_arrow" href="#"><</a>
    <a class="ilc_ps_next ilc_ps_arrow" href="#">></a>
    <?php } ?>
    </div>
   </div>
   
   <script type="text/javascript">
jQuery(window).load(function(){
					if( jQuery("#foo2 div").length > 4 ){
					 
						jQuery("#foo2").carouFredSel({ responsive: true, 
							width: "100%",
							height: "250",
							items: {
								
							visible: {
									min: 1,
									max: 4
							},
							width: 160,
						minimum: 4,
								height: "auto"
							},
							scroll: {
								items: 4,
								pauseOnHover: true,
								wipe: true
							},
							auto: {
								play: false,
								pauseDuration: 1
							},
							prev: {
								button: "#foo2_prev",
								key: "left"
							},
							next: {
								button: "#foo2_next",
								key: "right"
							},
							onCreate : function(items, sizes){
								jQuery("#foo2").css({
									"height": "auto",
									"visibility": "visible",
								});
							}
						});
					}
					
				
					
				});
				
			
				
</script>
	 
	<?php endif;
	 
	wp_reset_query();
	 
	return ob_get_clean();
}
 
add_shortcode('sale_product', 'woocommerce_sale_products');



 
/********title for next and previous post********/ 

add_filter('next_post_link','add_title_to_next_post_link');
function add_title_to_next_post_link($link) {
global $post;
	$post = get_post($post_id);
	$next_post = get_next_post();
	$title = $next_post->post_title;
	$link = str_replace("rel=", " title='".$title."' rel", $link);
 return $link;
}

add_filter('previous_post_link','add_title_to_previous_post_link');
function add_title_to_previous_post_link($link) {
global $post;
	$post = get_post($post_id);
	$previous_post = get_previous_post();
	$title = $previous_post->post_title;
	$link = str_replace("rel=", " title='".$title."' rel", $link);
  return $link;
}




//disable heartbeat

function my_deregister_heartbeat() {
	global $pagenow;
	if ( 'post.php' != $pagenow && 'post-new.php' != $pagenow ) {
		wp_deregister_script('heartbeat');
		wp_register_script('heartbeat', false);
	}
}
add_action( 'admin_enqueue_scripts', 'my_deregister_heartbeat' );

//end

 
 
/**** infinite scroll theme support *****/ 
add_theme_support( 'infinite-scroll', array(
    'container' => 'content',
    'footer' => 'page',
) ); 

function new_full_testimonial($atts) {

global $add_styles, $add_pagination;
	$add_styles = true;
	$add_pagination = true;
	
	extract(shortcode_atts(array('category' => ''), $atts));
	
	if ($category != '') { 
		$term = get_term_by('id', $category, 'testimonial-category');
		$term_id = $term->term_id;
		$term_taxonomy = $term->taxonomy;
		$term_slug = $term->slug;
	} else { 
		$term_taxonomy = '';
		$term_slug = '';
	}
	
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		$term_taxonomy 		=> $term_slug,
		'post_type' 		=> 'testimonial', 
		'posts_per_page' 	=> 3,
		'paged'             => $paged,
		'orderby'         	=> 'post_date',
		'order'				=> 'DESC',
		'post_status'     	=> 'publish'
	);
	
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$posts_array  = $wp_query->query($args);
	
	$display .= '<div class="testimonials_container">';
	
	foreach($posts_array as $post) {
					
		// Add custom fields
		$selected_extended_posts = array();
		$custom = get_post_custom($post->ID);
			foreach(array('client_name', 'client_photo', 'email', 'company_website', 'company_name') as $field) {
				if(isset($custom[$field])){
					$post->$field = $custom[$field][0];
				}
			}
			
		$selected_extended_posts[] = $post;		
		$testimonial = $post;
		
		$display .= '<div class="result">';
		$display .= gct_single_testimonial($testimonial);
		$display .= '</div>';
	}
	
	
	  $display .= '</div>';
	
	 //$display.= $wp_query->max_num_pages;
	
	 if ( $wp_query->max_num_pages > 1 ) :
	
	 $display .= '<div class="pagi">'.kriesi_pagination($wp_query->max_num_pages).'</div>';
	 
	 endif;
	 
	 //$display .= '<div id="more">Loading More Content</div>';
     //$display .= '<div id="no-more">No More Content</div>';
	 
	 
	 
	
	$temp = null;
	
	return $display;
}
add_shortcode('full-testimonials', 'new_full_testimonial');
/*********adding shortcode to display full testimonial******/
 
function text_excerpt_length($text){
return 500;
}
add_filter('excerpt_length', 'text_excerpt_length');


/***************************custom pagination start here***********************************/ 
function kriesi_pagination($pages = '', $range = 2)
{  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   

     if(1 != $pages)
     {
       echo "<div class='pagination'>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
         echo "</div>\n";
     }
}


/*********************************custom pagination end here*************************************/

/********************************** custom infinite scroller *************************************/

function custom_infinite_scroll_js() {
	if( is_page(504) ) { ?>
	<script>
	var infinite_scroll = {
		loading: {
			img: "http://199.101.49.90/~mylive5/playground/wp-content/uploads/gif-load.gif",
			msgText: "",
			finishedMsg: "<?php _e( 'No More Testimonial.', 'custom' ); ?>"
		},
		"nextSelector":".pagination a:first",
		"navSelector":".pagination",
		"itemSelector":".result",
		"contentSelector":".testimonials_container"
	};
	jQuery( infinite_scroll.contentSelector ).infinitescroll( infinite_scroll );
	</script>
	<?php
	}
}
add_action( 'wp_footer', 'custom_infinite_scroll_js',100 );


/************************************ end here ***************************************/





 
// filter for price
// Using Old Woocommerce 2.0 variable price format
add_filter( 'woocommerce_variable_sale_price_html', 'wc_wc20_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_wc20_variation_price_format', 10, 2 );
function wc_wc20_variation_price_format( $price, $product ) {
// Main Price
$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
$price = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
// Sale Price
$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
sort( $prices );
$saleprice = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
if ( $price !== $saleprice ) {
$price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
}
return $price;
}

//Increase heartbeat duration

add_filter( 'heartbeat_send', 'my_heartbeat_settings' );
function my_heartbeat_settings( $response ) {
	if ( $_POST['interval'] != 60 ) {
		$response['heartbeat_interval'] = 60;
	}
	return $response;
}

//end heartbeat


// removing sidebar to adjuct product page
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar',10); 

//Remove related products
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Prevent direct script access
if ( !defined('ABSPATH') )
	die ( 'No direct script access allowed' );

// Define paths to common folders
define( 'G1_LIB_DIR',    	trailingslashit( get_template_directory() ) .  'lib' );
define( 'G1_LIB_URI',    	trailingslashit( get_template_directory_uri() ) .  'lib' );

define( 'G1_FRAMEWORK_DIR', trailingslashit( get_template_directory() ) . 'g1-framework' );
define( 'G1_FRAMEWORK_URI', trailingslashit( get_template_directory_uri() ) . 'g1-framework' );

/**
 * Enable translation (i18n)
 */
function g1_init_localization_before_theme() {
    $dir = trailingslashit( get_template_directory() );

    if (!load_child_theme_textdomain( 'g1_theme', get_stylesheet_directory().'/languages' )) {
        load_theme_textdomain( 'g1_theme', $dir . 'languages' );
    }

    $locale = get_locale();
    $locale_file = $dir . "languages/$locale.php";
    if ( is_readable( $locale_file ) )
        require_once( $locale_file );
}

g1_init_localization_before_theme();

require_once( G1_FRAMEWORK_DIR . '/g1-framework.php' );

require_once( G1_LIB_DIR . '/theme-dependencies.php' );
require_once( G1_LIB_DIR . '/theme-functions.php' );
require_once( G1_LIB_DIR . '/theme-features.php' );
require_once( G1_LIB_DIR . '/g1-precontent/g1-precontent.php' );
require_once( G1_LIB_DIR . '/g1-sliders/g1-sliders.php' );
require_once( G1_LIB_DIR . '/g1-pages/g1-pages.php' );
require_once( G1_LIB_DIR . '/g1-posts/g1-posts.php' );

// Include plugin.php file so we can use the is_plugin_active() function
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/* Do you want to disable the WooCommerce module completely?
 *
 * Just copy the below line to the functions.php file from your child theme:
 * define( 'G1_WOOCOMMERCE_MODULE', false );
 */
if ( is_plugin_active('woocommerce/woocommerce.php') ) {
    require_once( G1_LIB_DIR . '/g1-woocommerce/g1-woocommerce.php' );
}

/* Do you want to disable the Twitter module completely?
 *
 * Just copy the below line to the functions.php file from your child theme:
 * define( 'G1_TWITTER_MODULE', false );
 */
define( 'G1_TWITTER_MODULE', true );
if ( G1_TWITTER_MODULE ) {
    require_once( G1_LIB_DIR . '/g1-twitter/g1-twitter.php' );
}

/* Do you want to disable the GMap module completely?
 *
 * Just copy the below line to the functions.php file from your child theme:
 * define( 'G1_GMAP_MODULE', false );
 */
define( 'G1_GMAP_MODULE', true );
if ( G1_GMAP_MODULE ) {
    require_once( G1_LIB_DIR . '/g1-gmap/g1-gmap.php' );
}

/* Do you want to disable the Mailchimp module completely?
 *
 * Just copy the below line to the functions.php file from your child theme:
 * define( 'G1_MAILCHIMP_MODULE', false );
 */
define( 'G1_MAILCHIMP_MODULE', true );
if ( G1_MAILCHIMP_MODULE ) {
    require_once( G1_LIB_DIR . '/g1-mailchimp/g1-mailchimp.php' );
}

/* Do you want to disable the Maintenance module completely?
 *
 * Just copy the below line to the functions.php file from your child theme:
 * define( 'G1_MAINTENANCE_MODULE', false );
 */
define( 'G1_MAINTENANCE_MODULE', true );
if ( G1_MAINTENANCE_MODULE ) {
    require_once( G1_LIB_DIR . '/g1-maintenance/g1-maintenance.php' );
}

/* Do you want to disable the Contact Form module completely?
 *
 * Just copy the below line to the functions.php file from your child theme:
 * define( 'G1_TWITTER_MODULE', false );
 */
define( 'G1_CONTACT_FORM_MODULE', true );
if ( G1_CONTACT_FORM_MODULE ) {
    require_once( G1_LIB_DIR . '/g1-contact-form/g1-contact-form.php' );
}



/* Do you want to disable the Work module completely?
 *
 * Just copy the below line to the functions.php file from your child theme:
 * define( 'G1_WORKS_MODULE', false );
 */
define( 'G1_WORKS_MODULE', true );
if ( G1_WORKS_MODULE ) {
    require_once( G1_LIB_DIR . '/g1-works/g1-works.php' );
}

/* Do you want to disable the Simple_Slider module completely?
 *
 * Just copy the below line to the functions.php file from your child theme:
 * define( 'G1_SIMPLE_SLIDER_MODULE', false );
 */
define( 'G1_SIMPLE_SLIDERS_MODULE', true );
if ( G1_SIMPLE_SLIDERS_MODULE ) {
    require_once( G1_LIB_DIR . '/g1-simple-sliders/g1-simple-sliders.php' );
}

require_once( G1_LIB_DIR . '/g1-relations/g1-relations.php' );
require_once( G1_LIB_DIR . '/theme-options.php' );
require_once( G1_LIB_DIR . '/theme-fonts.php' );

// Set standard content width
if ( ! isset( $content_width ) ) $content_width = 686;

/**/
/**/
/**/
/**/
/*DUPLICATING PRICE TABLES*/
/**/
/**/
/**/
/*
 * Function creates post duplicate as a draft and redirects then to the edit post screen
 */
function rd_duplicate_post_as_draft(){
	global $wpdb;
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
		wp_die('No post to duplicate has been supplied!');
	}
 
	/*
	 * get the original post id
	 */
	$post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
	/*
	 * and all the original post data then
	 */
	$post = get_post( $post_id );
 
	/*
	 * if you don't want current user to be the new post author,
	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	 */
	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;
 
	/*
	 * if post data exists, create the post duplicate
	 */
	if (isset( $post ) && $post != null) {
 
		/*
		 * new post data array
		 */
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);
 
		/*
		 * insert the post by wp_insert_post() function
		 */
		$new_post_id = wp_insert_post( $args );
 
		/*
		 * get all current post terms ad set them to the new post draft
		 */
		$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}
 
		/*
		 * duplicate all post meta
		 */
		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
		if (count($post_meta_infos)!=0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}
 
 
		/*
		 * finally, redirect to the edit post screen for the new draft
		 */
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	} else {
		wp_die('Post creation failed, could not find original post: ' . $post_id);
	}
}
add_action( 'admin_action_rd_duplicate_post_as_draft', 'rd_duplicate_post_as_draft' );
 
/*
 * Add the duplicate link to action list for post_row_actions
 */
function rd_duplicate_post_link( $actions, $post ) {
	if (current_user_can('edit_posts')) {
		$actions['duplicate'] = '<a href="admin.php?action=rd_duplicate_post_as_draft&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
	}
	return $actions;
}
 
add_filter('pricetable_row_actions', 'rd_duplicate_post_link', 10, 2);

// Limit tag cloud widget to 20 items and order by count
 
add_filter('WC_Widget_Product_Tag_Cloud','set_number_tags');
function set_number_tags($args) {
$args = array('number'    => 5, 'orderby'    => 'count', 'smallest' => 8, 'largest' => 18,);
return $args;
}


/** Remove Cancel Subscription Button */
function eg_remove_my_subscriptions_button( $actions, $subscriptions ) {
 
	foreach ( $actions as $subscription_key => $action_buttons ) {
		foreach ( $action_buttons as $action => $button ) {
			switch ( $action ) {
//				case 'change_payment_method':	// Hide "Change Payment Method" button?
//				case 'change_address':		// Hide "Change Address" button?
//				case 'switch':			// Hide "Switch Subscription" button?
//				case 'renew':			// Hide "Renew" button on a cancelled subscription?
//				case 'pay':			// Hide "Pay" button on subscriptions that are "on-hold" as they require payment?
//				case 'reactivate':		// Hide "Reactive" button on subscriptions that are "on-hold"?
			case 'cancel':			// Hide "Cancel" button on subscriptions that are "active" or "on-hold"?
					unset( $actions[ $subscription_key ][ $action ] );
					break;
				default: 
					error_log( '-- $action = ' . print_r( $action, true ) );
					break;
			}
		}
	}
 
	return $actions;
}
add_filter( 'woocommerce_my_account_my_subscriptions_actions', 'eg_remove_my_subscriptions_button', 100, 2 );

// Custom login screen
function custom_login_css() {
echo '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri().'/css/livetech-login-styles.css" />';
}
add_action('login_head', 'custom_login_css');

function custom_fonts() {
echo '<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,700" rel="stylesheet" type="text/css">';
}
add_action('login_head', 'custom_fonts');
add_filter( 'login_headerurl', 'custom_login_header_url' );
function custom_login_header_url($url) {
return  'http://www.mylive-tech.com' ;
}

// add taxonomy term of products category to body classes
function woo_custom_taxonomy_in_body_class( $classes ){
if( is_singular( 'product' ) )
{
$custom_terms = get_the_terms(0, 'product_cat');
if ($custom_terms) {
foreach ($custom_terms as $custom_term) {
$classes[] = 'product_cat_' . $custom_term->slug;
}
}
}
return $classes;
}
add_filter( 'body_class', 'woo_custom_taxonomy_in_body_class' );


// Date Picker
add_action( 'wp_enqueue_scripts', 'custom_enqueue_datepicker' );
 
function custom_enqueue_datepicker() {
	// Optional - enqueue styles
	wp_enqueue_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css', false, '1.0', false );
	
	// Enqueue Home text scroller
	wp_enqueue_script(  'lt-home000', get_stylesheet_directory_uri() . '/js/modernizr.custom.js', array( 'jquery', 'jquery-modernizr' ), '1.0', false );
	//wp_enqueue_script(  'lt-home111', get_stylesheet_directory_uri() . '/js/cbpScroller.js', false, '1.0', true );
	//wp_enqueue_script(  'lt-home222', get_stylesheet_directory_uri() . '/js/classie.js', false, '1.0', true );
	
}

/* limit excerpt */
function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/* Display selective search results */
function searchfilter($query) {
    if ($query->is_search && !is_admin() ) {
        $query->set('post_type',array('post','page','product'));
    }

return $query;
}

add_filter('pre_get_posts','searchfilter');

// Move WooCommerce price
remove_action(  'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10  );
add_action(  'woocommerce_single_product_summary', 'woocommerce_template_single_price', 30  );


/*
 * Automatically redirect to current page after user logout WordPress.
 */
function get_current_logout( $logout_url ){
  if ( !is_admin() ) {
    $logout_url = add_query_arg('redirect_to', urlencode(( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), $logout_url);
  }
 
  return $logout_url;
}
 
add_filter('logout_url', 'get_current_logout');
//add_filter('login_url', 'get_current_logout');

/** Auto-Generate ALT tag for images */
function image_alt_tag($content)
{global $post;preg_match_all('/<img (.*?)\/>/', $content, $images);
if(!is_null($images)) {foreach($images[1] as $index => $value)
{if(!preg_match('/alt=/', $value)){
$new_img = str_replace('<img', '<img alt="'.get_the_title().'"', $images[0][$index]);
$content = str_replace($images[0][$index], $new_img, $content);}}}
return $content;
}
add_filter('the_content', 'image_alt_tag', 99999);


/*position of resilts & sorting*/
remove_action( 'woocommerce_before_shop_loop', 
               'woocommerce_result_count', 20 );
    add_action( 'woocommerce_after_shop_loop', 
            'woocommerce_result_count', 5 );
remove_action( 'woocommerce_before_shop_loop', 
               'woocommerce_catalog_ordering', 30 );
    add_action( 'woocommerce_after_shop_loop', 
            'woocommerce_catalog_ordering', 5 );
			


//Adding Registration fields to the form 

add_filter( 'register_form', 'adding_custom_registration_fields', 30 );
function adding_custom_registration_fields( ) {

	//lets make the field required so that i can show you how to validate it later;
	echo '<p class="form-row form-row-first"><label for="reg_firstname">'.__('First Name', 'woocommerce').' <span class="required">*</span></label>
<input type="text" class="input-text" name="firstname" id="reg_firstname" size="30" value="'.esc_attr($_POST['firstname']).'" /></p>';
echo '<p class="form-row form-row-last"><label for="reg_lastname">'.__('Last Name', 'woocommerce').' <span class="required">*</span></label>
<input type="text" class="input-text" name="lastname" id="reg_lastname" size="30" value="'.esc_attr($_POST['lastname']).'" /></p>';
}

//default sorting

if ( is_product_category() ) {
  
  if ( is_product_category( 'lynk-voip-phone-service' ) ) {
    echo 'Hi! Take a look at our sweet tshirts below.';
  } elseif ( is_product_category( 'games' ) ) {
    echo 'Hi! Hungry for some gaming?';
  } else {
    echo 'Hi! Check our our products below.';
  }
 
}


//Validation registration form  after submission using the filter registration_errors
add_filter('registration_errors', 'registration_errors_validation', 10,3);
function registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
		global $woocommerce;
		extract($_POST); // extracting $_POST into separate variables
		if($firstname == '' || $lastname =='' ) {
			$woocommerce->add_error( __( 'Please, fill in all the required fields.', 'woocommerce' ) );
		}
		return $reg_errors;
}

//Updating use meta after registration successful registration
add_action('woocommerce_created_customer','adding_extra_reg_fields');

function adding_extra_reg_fields($user_id) {
	extract($_POST);
	update_user_meta($user_id, 'first_name', $firstname);
		update_user_meta($user_id, 'last_name', $lastname);
		
	update_user_meta($user_id, 'billing_first_name', $firstname);
	update_user_meta($user_id, 'billing_last_name', $lastname);
	
	update_user_meta($user_id, 'shipping_first_name', $firstname);
	update_user_meta($user_id, 'shipping_last_name', $lastname);
	
}


/* Woocommerce Prev Next product links*/
function next_post_link_product($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
    adjacent_post_link_product($format, $link, $in_same_cat, $excluded_categories, false);
}

function previous_post_link_product($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
    adjacent_post_link_product($format, $link, $in_same_cat, $excluded_categories, true);
}

function adjacent_post_link_product( $format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true ) {
    if ( $previous && is_attachment() )
        $post = get_post( get_post()->post_parent );
    else
        $post = get_adjacent_post_product( $in_same_cat, $excluded_categories, $previous );

    if ( ! $post ) {
        $output = '';
    } else {
        $title = $post->post_title;

        if ( empty( $post->post_title ) )
            $title = $previous ? __( 'Previous Post' ) : __( 'Next Post' );

        $title = apply_filters( 'the_title', $title, $post->ID );
        $date = mysql2date( get_option( 'date_format' ), $post->post_date );
        $rel = $previous ? 'prev' : 'next';

        $string = '<a href="' . get_permalink( $post ) . '" rel="'.$rel.'">';
        $inlink = str_replace( '%title', $title, $link );
        $inlink = str_replace( '%date', $date, $inlink );
        $inlink = $string . $inlink . '</a>';

        $output = str_replace( '%link', $inlink, $format );
    }

    $adjacent = $previous ? 'previous' : 'next';

    echo apply_filters( "{$adjacent}_post_link", $output, $format, $link, $post );
}

function get_adjacent_post_product( $in_same_cat = false, $excluded_categories = '', $previous = true ) {
    global $wpdb;

    if ( ! $post = get_post() )
        return null;

    $current_post_date = $post->post_date;

    $join = '';
    $posts_in_ex_cats_sql = '';
    if ( $in_same_cat || ! empty( $excluded_categories ) ) {
        $join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

        if ( $in_same_cat ) {
            if ( ! is_object_in_taxonomy( $post->post_type, 'product_cat' ) )
                return '';
            $cat_array = wp_get_object_terms($post->ID, 'product_cat', array('fields' => 'ids'));
            if ( ! $cat_array || is_wp_error( $cat_array ) )
                return '';
            $join .= " AND tt.taxonomy = 'product_cat' AND tt.term_id IN (" . implode(',', $cat_array) . ")";
        }

        $posts_in_ex_cats_sql = "AND tt.taxonomy = 'product_cat'";
        if ( ! empty( $excluded_categories ) ) {
            if ( ! is_array( $excluded_categories ) ) {
                // back-compat, $excluded_categories used to be IDs separated by " and "
                if ( strpos( $excluded_categories, ' and ' ) !== false ) {
                    _deprecated_argument( __FUNCTION__, '3.3', sprintf( __( 'Use commas instead of %s to separate excluded categories.' ), "'and'" ) );
                    $excluded_categories = explode( ' and ', $excluded_categories );
                } else {
                    $excluded_categories = explode( ',', $excluded_categories );
                }
            }

            $excluded_categories = array_map( 'intval', $excluded_categories );

            if ( ! empty( $cat_array ) ) {
                $excluded_categories = array_diff($excluded_categories, $cat_array);
                $posts_in_ex_cats_sql = '';
            }

            if ( !empty($excluded_categories) ) {
                $posts_in_ex_cats_sql = " AND tt.taxonomy = 'product_cat' AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
            }
        }
    }

    $adjacent = $previous ? 'previous' : 'next';
    $op = $previous ? '<' : '>';
    $order = $previous ? 'DESC' : 'ASC';

    $join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
    $where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
    $sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

    $query = "SELECT p.id FROM $wpdb->posts AS p $join $where $sort";
    $query_key = 'adjacent_post_' . md5($query);
    $result = wp_cache_get($query_key, 'counts');
    if ( false !== $result ) {
        if ( $result )
            $result = get_post( $result );
        return $result;
    }

    $result = $wpdb->get_var( $query );
    if ( null === $result )
        $result = '';

    wp_cache_set($query_key, $result, 'counts');

    if ( $result )
        $result = get_post( $result );

    return $result;
}



/**/

/* Recently viewed products */

function rc_woocommerce_recently_viewed_products( $atts, $content = null ) {

	// Get shortcode parameters
	extract(shortcode_atts(array(
		"per_page" => '5'
	), $atts));

	// Get WooCommerce Global
	global $woocommerce;

	// Get recently viewed product cookies data
	$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
	$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

	// If no data, quit
	if ( empty( $viewed_products ) )
		return __( 'You have not viewed any product yet!', 'rc_wc_rvp' );

	// Create the object
	ob_start();

	// Get products per page
	if( !isset( $per_page ) ? $number = 5 : $number = $per_page )

	// Create query arguments array
    $query_args = array(
    				'posts_per_page' => $number, 
    				'no_found_rows'  => 1, 
    				'post_status'    => 'publish', 
    				'post_type'      => 'product', 
    				'post__in'       => $viewed_products, 
    				'orderby'        => 'rand'
    				);

	// Add meta_query to query args
	$query_args['meta_query'] = array();

    // Check products stock status
    $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();

	// Create a new query
	$r = new WP_Query($query_args);

	// If query return results
	if ( $r->have_posts() ) {

		$content = '<ul class="rc_wc_rvp_product_list_widget">';

		// Start the loop
		while ( $r->have_posts()) {
			$r->the_post();
			global $product;

			$content .= '<li>
				<a href="' . get_permalink() . '">
					' . ( has_post_thumbnail() ? get_the_post_thumbnail( $r->post->ID, 'shop_thumbnail' ) : woocommerce_placeholder_img( 'shop_thumbnail' ) ) . ' ' . get_the_title() . '
				</a> ' . $product->get_price_html() . '
			</li>';
		}

		$content .= '</ul>';

	}

	// Get clean object
	$content .= ob_get_clean();
	
	// Return whole content
	return $content;
}

// Register the shortcode
add_shortcode("woocommerce_recently_viewed_products", "rc_woocommerce_recently_viewed_products");

// jQuery & other scripts
// Create the my_scripts_method function
function livetech_scripts() {
    // Deregister the built-in version of jQuery
    wp_deregister_script('jquery');
    // Register a CDN hosted version. If browsing on a secure connection, use HTTPS.
    wp_register_script('jquery', 'http://code.jquery.com/jquery-1.8.0.min.js', false, null, false);
    // Activate the jQuery script
    wp_enqueue_script('jquery');
    // Register your javascript file
    wp_register_script('client-js', get_template_directory_uri() . '/js/jquery.flexisel.js', false, null, true);
    // Activate your javascript file
    wp_enqueue_script('client-js');

	
}
// Tell WordPress to run the my_scripts_method function
add_action('wp_enqueue_scripts', 'livetech_scripts');



// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
 
function woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;
	ob_start();
	?>
	<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>"><?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
	<?php
	
	$fragments['a.cart-contents'] = ob_get_clean();
	
	return $fragments;
	
}


/* PLEASE
 * Don't add any code below here, use the child theme for all your modifications
 */