<?php

/* ---------------------------------------------------------------------------
 * A unique identifier is defined to store the options in the database.
 * --------------------------------------------------------------------------- */
function optionsframework_option_name() {
	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = 'doover';
	update_option('optionsframework', $optionsframework_settings);
}


/* ---------------------------------------------------------------------------
 * Defines an array of options that will be used to generate the settings page 
 * and be saved in the database.
 * --------------------------------------------------------------------------- */
function optionsframework_options() {
	
	$options = array();
	$imagepath =  get_stylesheet_directory_uri() . '/admin/images/';
	
	$pages_array = array();  
	$pages_obj = get_pages('sort_column=post_title');
	$pages_array[''] = '-- Select --';
	foreach ($pages_obj as $page) {
    	$pages_array[$page->ID] = $page->post_title;
	}

	// General ---------------------------------------------------------------------------
	$options[] = array( "name" => __('General','doover'),
						"type" => "heading");
	
	$view_array = array( "responsive" => "Enable Responsive", "megamenu" => "Enable Mega Menu" );

	$options[] = array( "name" => __('Display options','doover'),
						"desc" => __('<b>Notice:</b> Responsive menu is working only with Mega Menu or WordPress custom menu, please add one in <i>Appearance > Menus</i> and select it for <i>Theme Locations</i> section. <a href="http://en.support.wordpress.com/menus/">http://en.support.wordpress.com/menus/</a>','doover'),
						"id" => "display",
						"type" => "multicheck",
						"options" => $view_array);
	
	$options[] = array( "name" => __('Custom Favicon','doover'),
						"desc" => __('Site favicon.','doover'),
						"id" => "favicon",
						"type" => "upload",
						"std" => '');
					
	$options[] = array( "name" => __('Contact E-mail','doover'),
						"desc" => __('Contact Form will appear only if this field will be filled correctly.',"doover"),
						"id" => "contact_email",
						"std" => get_bloginfo( 'admin_email' ),
						"type" => "text");
	
	$options[] = array( "name" => __('Google Maps Lat, Lng','doover'),
						"desc" => __('Google maps pointer position. You need to separate with comma, ex. "-33.8710, 151.2039". The map will appear only if this field is filled correctly.','doover'),
						"id" => "google_maps_lat_lng",
						"type" => "text");
	
	$options[] = array( "name" => __('Meta','doover'),
						"desc" => __('Description','doover'),
						"id" => "meta_description",
						"std" => get_bloginfo( 'description' ),
						"type" => "text");
	
	$options[] = array( "desc" => __('Keywords','doover'),
						"id" => "meta_keywords",
						"std" => "",
						"type" => "text");
	
	$meta_robots_array = array( "index" => "Index", "follow" => "Follow" );
	$meta_robots_defaults = array( "index" => "1", "follow" => "1" );

	$options[] = array( "name" => __('Meta Robots','doover'),
						"desc" => __('Should the robots index your site or not? Maybe you don`t want to let robots follow all your pages/folders?','doover'),
						"id" => "meta_robots",
						"type" => "multicheck",
						"options" => $meta_robots_array,
						"std" => $meta_robots_defaults);
	
	$options[] = array( "name" => __('Google Analytics','doover'),
						"desc" => __('Paste your Google Analytics code here.','doover'),
						"id" => "google_analytics",
						"type" => "textarea"); 
		
	
	// Header ---------------------------------------------------------------------------
	$options[] = array( "name" => __('Header','doover'),
						"type" => "heading");
	
	$options[] = array( "name" => __('Text Logo','doover'),
						"desc" => __('Use text instead of graphic logo.','doover'),
						"id" => "logo_text_show",
						"type" => "checkbox");
	
	$options[] = array( "name" => __('Custom Text Logo','doover'),
						"desc" => __('Custom site text logo.','doover'),
						"id" => "logo_text",
						"std" => "",
						"class" => "hidden",
						"type" => "text");
	
	$options[] = array( "name" => __('Custom Logo','doover'),
						"desc" => __('Custom site image logo.','doover'),
						"id" => "logo",
						"type" => "upload");
	
	$homepage_style_array = array("slider_offer" => "Slider: Offer", "slider_photos" => "Slider: Photos", "image"=>"Single Image", "simple"=>"Simple");
	
	$options[] = array( "name" => __('Homepage style','doover'),
						"desc" => __('Homepage appearance. Choose the best for you.','doover'),
						"id" => "homepage_style",
						"std" => "simple",
						"type" => "select",
						"class" => "mini",
						"options" => $homepage_style_array);
	
	$options[] = array( "name" => __('Slider configuration','doover'),
						"desc" => __('Milliseconds between slide transitions (0 to disable auto advance, recommended when using the video slider)','doover'),
						"id" => "slider_timeout",
						"std" => "5000",
						"class" => "hidden",
						"type" => "text");
	
	$options[] = array( "desc" => __('Speed of the transition (any valid fx speed value)','doover'),
						"id" => "slider_speed",
						"std" => "500",
						"class" => "hidden",
						"type" => "text");
	
	$options[] = array( "name" => __('Offer slider "Our offer:" text','doover'),
						"id" => "our_offer",
						"std" => array( "prefix"=>"Our", "text"=>"offer:" ),
						"class" => "hidden",
						"type" => "prefix_text");
	
	$options[] = array( "name" => __('Homepage image','doover'),
						"desc" => "",
						"id" => "homepage_image",
						"type" => "upload",
						"class" => "hidden",
						"std" => '');
	
	$options[] = array( "name" => __('Homepage text','doover'),
						"desc" => __('Leave blank if you want very simple homepage.','doover'),
						"id" => "homepage_text",
						"std" => "",
						"class" => "hidden",
						"type" => "text");

	$options[] = array( "name" => __('Subpages header','doover'),
						"desc" => __('"Get in touch" button. Assign page for "Get in touch" button.','doover'),
						"id" => "btn_get_in_touch",
						"type" => "select",
						"class" => "small",
						"options" => $pages_array);
	
	$subpage_array = array( "title" => "Show the Title on subpages.", "breadcrumbs" => "Show the Breadcrumbs on subpages." );
	$subpage_defaults = array( "title" => "1", "breadcrumbs" => "1" );

	$options[] = array( "desc" => __('The Title must be checked if you want to use "Get in touch" button','doover'),
						"id" => "subpage",
						"type" => "multicheck",
						"options" => $subpage_array,
						"std" => $subpage_defaults);
	
	$options[] = array( "name" => __('Facebook URL','doover'),
						"desc" => __('Your Facebook URL.','doover'),
						"id" => "facebook_url",
						"type" => "text");
	
	$options[] = array( "name" => __('Google + URL','doover'),
						"desc" => __('Your Google Plus URL.','doover'),
						"id" => "google_url",
						"type" => "text");
	
	$options[] = array( "name" => __('Twitter URL','doover'),
						"desc" => __('Your Twitter URL.','doover'),
						"id" => "twitter_url",
						"type" => "text");
	
	$options[] = array( "name" => __('YouTube URL','doover'),
						"desc" => __('Your YouTube URL.','doover'),
						"id" => "youtube_url",
						"type" => "text");
	
$options[] = array( "name" => __('LinkedIN URL','doover'),
						"desc" => __('Your LinkedIN Profile URL.','doover'),
						"id" => "linkedin_url",
						"type" => "text");
	
	$options[] = array( "name" => __('RSS','doover'),
						"desc" => __('Show the RSS icon in the header','doover'),
						"id" => "rss",
						"std" => 1,
						"type" => "checkbox");
	
	$options[] = array( "name" => __('Assign page for "Sitemap"','doover'),
						"desc" => __('Sitemap button will appear only if this option is selected.','doover'),
						"id" => "sitemap",
						"type" => "select",
						"class" => "small",
						"options" => $pages_array);
							
	$options[] = array( "name" => __('Call Us','doover'),
						"desc" => __('Contact number with optional country prefix - upper right corner.','doover'),
						"id" => "call_us",
						"std" => "",
						"type" => "prefix_text");
		
	
	// Portfolio ---------------------------------------------------------------------------
	$options[] = array( "name" => __('Portfolio','doover'),
						"type" => "heading");
	
	$portfolio_style_array = array("portfolio_1_col" => "One column", "portfolio_2_cols"=>"Two columns", "portfolio_3_cols"=>"Three columns");
	
	$options[] = array( "name" => __('List style','doover'),
						"desc" => __('Templates for portfolio items list.','doover'),
						"id" => "portfolio_style",
						"std" => "portfolio_1_col",
						"type" => "select",
						"class" => "mini",
						"options" => $portfolio_style_array);	
	
	$options[] = array( "name" => __('Assign page for portfolio','doover'),
						"desc" => __('Choose the best page for portfolio.','doover'),
						"id" => "portfolio_page",
						"type" => "select",
						"class" => "small",
						"options" => $pages_array);
	
	$options[] = array( "name" => __('Single item slug','doover'),
						"desc" => __('Link to single item. <b>Important</b>: Do not use characters not allowed in links. Must be different from the Portfolio site title chosen above, ex. "portfolio-item".','doover'),
						"id" => "portfolio_item_slug",
						"std" => "portfolio-item",
						"type" => "text",
						"class" => "mini");
	
	$options[] = array( "name" => __('Items per page','doover'),
						"desc" => __('Number of portfolio items per page.','doover'),
						"id" => "portfolio_per_page",
						"std" => "10",
						"type" => "text",
						"class" => "tiny");
	
	
	// Posts ---------------------------------------------------------------------------
	$options[] = array( "name" => __('Posts','doover'),
						"type" => "heading");
	
	$options[] = array( "name" => __('Posts per page','doover'),
						"desc" => __('Number of posts per page.','doover'),
						"id" => "posts_per_page",
						"std" => "10",
						"type" => "text",
						"class" => "tiny");
	
	$posts_meta_array = array( "categories" => "Display categories", "comments" => "Display comments", "tags" => "Display tags", "time" => "Display time" );
	$posts_meta_defaults = array( "categories" => "1", "comments" => 1, "tags" => 1, "time" => 1 );

	$options[] = array( "name" => __('Posts meta','doover'),
						"desc" => __('Which meta should be displayed next to posts on the blog.','doover'),
						"id" => "posts_meta",
						"type" => "multicheck",
						"options" => $posts_meta_array,
						"std" => $posts_meta_defaults);
		
	
	// Styles ---------------------------------------------------------------------------
	$options[] = array( "name" => __('Styles','doover'),
						"type" => "heading");
	
	$style_array = array("blue" => "Blue [default]");
	
	$options[] = array( "name" => __('Style','doover'),
						"desc" => __('Color scheme. Choose the best for you.','doover'),
						"id" => "style",
						"std" => "blue",
						"type" => "select",
						"class" => "mini",
						"options" => $style_array);
	
	$options[] = array( "name" => __('Custom header','doover'),
						"desc" => __('Use custom header image and/or colour.','doover'),
						"id" => "custom_header",
						"type" => "checkbox");
	
	$header_bg_defaults = array('color' => '', 'image' => '', 'repeat' => 'none','position' => 'top center');
	$options[] = array( "name" =>  __('Header background','doover'),
						"desc" => __('Change the background CSS.','doover'),
						"id" => "header_bg",
						"class" => "hidden",
						"std" => $header_bg_defaults, 
						"type" => "background");
	
	// Colours
	$options[] = array( 'name' => __('Colours', 'doover'),
						'desc' => __('Content background colour.', 'doover'),
						'id' => 'colour_bg_content',
						'std' => '#FFFFFF',
						'type' => 'color');
	
	$options[] = array( 'desc' => __('Footer background colour.', 'doover'),
						'id' => 'colour_bg_footer',
						'std' => '',
						'type' => 'color');
	
	$options[] = array( 'desc' => __('Footer text colour.', 'doover'),
						'id' => 'colour_footer',
						'std' => '#969696',
						'type' => 'color');
	
	$options[] = array( 'desc' => __('Link colour.', 'doover'),
						'id' => 'colour_link',
						'std' => '#2D70CA',
						'type' => 'color');
	
	$options[] = array( 'desc' => __('Menu hoover background colour.', 'doover'),
						'id' => 'colour_menu_hover',
						'std' => '#2D70CA',
						'type' => 'color');
	// End: Colours
	
	// Typography
	$options[] = array( 'name' => __('Typography', 'doover'),
						'desc' => __('Multiple fonts allows you to use all of those fonts in your page. (But don\'t go overboard; most pages don\'t need very many fonts, and requesting a lot of fonts may make your pages slow to load.)', 'doover'),
						'type' => 'info');
	
	$options[] = array( 'desc' => __('Text font.', 'doover'),
						'id' => 'font_text',
						'std' => array('size' => '13px','face' => 'PT+Sans','style' => 'normal','color' => '#515E6C'),
						'type' => 'typography');
	
	$options[] = array( 'desc' => __('Menu font.', 'doover'),
						'id' => 'font_menu',
						'std' => array('size' => '15px','face' => 'PT+Sans','style' => 'normal','color' => '#FFFFFF'),
						'type' => 'typography');
	
	$options[] = array( 'desc' => __('Header H1 font. Page title.', 'doover'),
						'id' => 'font_h1',
						'std' => array('size' => '70px','face' => 'PT+Sans','style' => 'bold','color' => '#FFFFFF'),
						'type' => 'typography');
	
	$options[] = array( 'desc' => __('Header H2 font.', 'doover'),
						'id' => 'font_h2',
						'std' => array('size' => '52px','face' => 'PT+Sans','style' => 'bold','color' => '#000000'),
						'type' => 'typography');
	
	$options[] = array( 'desc' => __('Header H3 font.', 'doover'),
						'id' => 'font_h3',
						'std' => array('size' => '34px','face' => 'PT+Sans','style' => 'bold','color' => '#093869'),
						'type' => 'typography');
	
	$options[] = array( 'desc' => __('Header H4 font.', 'doover'),
						'id' => 'font_h4',
						'std' => array('size' => '20px','face' => 'PT+Sans','style' => 'normal','color' => '#000000'),
						'type' => 'typography');
	
	$options[] = array( 'desc' => __('Header H5 font.', 'doover'),
						'id' => 'font_h5',
						'std' => array('size' => '17px','face' => 'PT+Sans','style' => 'normal','color' => '#000000'),
						'type' => 'typography');
	
	$options[] = array( 'desc' => __('Header H6 font.', 'doover'),
						'id' => 'font_h6',
						'std' => array('size' => '14px','face' => 'PT+Sans','style' => 'bold','color' => '#000000'),
						'type' => 'typography');	
	// End: Typography
						
	$options[] = array( "name" => __('Custom CSS code','doover'),
						"desc" => __('Paste your custom CSS code here.','doover'),
						"id" => "custom_css",
						"type" => "textarea"); 					

	return $options;
}


/* ---------------------------------------------------------------------------
 * Options framework custom scripts
 * --------------------------------------------------------------------------- */
function optionsframework_custom_scripts() { ?>
<script>
//<![CDATA[
jQuery(document).ready(function() {

	// Logo ------------------------------------------------------------------
	jQuery('#logo_text_show').click(function() {
  		jQuery('#section-logo_text').toggle(500);
  		jQuery('#section-logo').toggle(500);
	});
	
	if (jQuery('#logo_text_show:checked').val() !== undefined) {
		jQuery('#section-logo_text').show();
		jQuery('#section-logo').hide();
	}
	
	// Header ----------------------------------------------------------------
	jQuery('#custom_header').click(function() {
  		jQuery('#section-header_bg').toggle(500);
	});
	
	if (jQuery('#custom_header:checked').val() !== undefined) {
		jQuery('#section-header_bg').show();
	}

	// Homepage --------------------------------------------------------------
	jQuery('#homepage_style').change(function() {
		if( jQuery(this).val() == 'slider_offer' ){
			jQuery('#section-homepage_image').hide(500);
			jQuery('#section-our_offer').show(500);
			jQuery('#section-homepage_text').hide(500);
			jQuery('#section-slider_speed').show(500);
			jQuery('#section-slider_timeout').show(500);
		} else if( jQuery(this).val() == 'image' ){
			jQuery('#section-homepage_image').show(500);
			jQuery('#section-our_offer').hide(500);
			jQuery('#section-homepage_text').hide(500);
			jQuery('#section-slider_speed').hide(500);
			jQuery('#section-slider_timeout').hide(500);
		} else if( jQuery(this).val() == 'simple' ) {
			jQuery('#section-homepage_image').hide(500);
			jQuery('#section-our_offer').hide(500);
			jQuery('#section-homepage_text').show(500);
			jQuery('#section-slider_speed').hide(500);
			jQuery('#section-slider_timeout').hide(500);
		} else {
			jQuery('#section-homepage_image').hide(500);
			jQuery('#section-our_offer').hide(500);
			jQuery('#section-homepage_text').hide(500);
			jQuery('#section-slider_speed').show(500);
			jQuery('#section-slider_timeout').show(500);
		}
	});

	if( jQuery('#homepage_style').val() == 'slider_offer' ) {
		jQuery('#section-our_offer').show();
		jQuery('#section-slider_speed').show();
		jQuery('#section-slider_timeout').show();
	} else if( jQuery('#homepage_style').val() == 'image' ) {
		jQuery('#section-homepage_image').show();
	} else if( jQuery('#homepage_style').val() == 'simple' ) {
		jQuery('#section-homepage_text').show();
	} else {
		jQuery('#section-slider_speed').show();
		jQuery('#section-slider_timeout').show();
	}
	
});
//]]>
</script>
<?php
}
add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');


/* ---------------------------------------------------------------------------
 * Textarea filter fix, scripts allowed
 * --------------------------------------------------------------------------- */
function custom_sanitize_textarea($input) {
    global $allowedposttags;
    $custom_allowedtags["script"] = array(
		"type" => array()
    );
    $custom_allowedtags = array_merge($custom_allowedtags, $allowedposttags);
    $output = wp_kses( $input, $custom_allowedtags);
    return $output;
}
function optionscheck_change_santiziation() {
    remove_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );
    add_filter( 'of_sanitize_textarea', 'custom_sanitize_textarea' );
}
add_action('admin_init','optionscheck_change_santiziation', 100);