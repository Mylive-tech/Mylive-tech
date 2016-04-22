<?php

/** Start the engine */

require_once( TEMPLATEPATH . '/lib/init.php' );



/** Child theme (do not remove) */

define( 'CHILD_THEME_NAME', 'Enterprise Theme' );

define( 'CHILD_THEME_URL', 'http://www.studiopress.com/themes/enterprise' );



$content_width = apply_filters( 'content_width', 600, 420, 900 );



/** Add new featured image sizes */

add_image_size('mini', 65, 65, TRUE);

add_image_size('homepage', 270, 80, TRUE);

add_image_size('slideshow', 600, 235, TRUE);



/** Add support for custom background */

add_custom_background();



/** Add support for custom header */

add_theme_support( 'genesis-custom-header', array( 'width' => 960, 'height' => 120, 'textcolor' => '333', 'admin_header_callback' => 'enterprise_admin_style' ) );



/**

 * Register a custom admin callback to display the custom header preview with the

 * same style as is shown on the front end.

 *

 */

function enterprise_admin_style() {



	$headimg = sprintf( '.appearance_page_custom-header #headimg { background: url(%s) no-repeat; font-family: Open Sans, arial, serif; min-height: %spx; }', get_header_image(), HEADER_IMAGE_HEIGHT );

	$h1 = sprintf( '#headimg h1, #headimg h1 a { color: #%s; font-size: 36px; font-weight: normal; line-height: 42px; margin: 25px 0 0; text-decoration: none; }', esc_html( get_header_textcolor() ) );

	$desc = sprintf( '#headimg #desc { color: #%s; }', esc_html( get_header_textcolor() ) );



	printf( '<style type="text/css">%1$s %2$s %3$s</style>', $headimg, $h1, $desc );



}


add_theme_support( 'genesis-structural-wraps', array( 'inner' ) );

/** Add div below footer in Genesis Child Theme */
add_action('genesis_after', 'live_tech_footer_toolbar');
function live_tech_footer_toolbar() { ?>
<div id="footer-toolbar"> 
<ul id="toolbar-inner"> 
<li class="ft1"> <a href="#" onclick="window.open('http://support.mylive-tech.com/visitor/index.php?_m=livesupport&_a=startclientchat&sessionid=yrph78uldkjpq9mkvf3jwaeca5hghb5p&proactive=0&departmentid=0&randno=19&fullname=&email=', 'newwindow', 'width=500, height=350'); return false;" title="Chat Now with a Live-Tech Agent"> Live-Chat with Support </a> </li>
<li class="ft2"><a href="http://www.mylive-tech.com/connect.html" title="Start A Remote Session" target="_blank"> Start A Remote Session </a> </li>
<li class="ft3"> <a href="http://support.mylive-tech.com/index.php?_m=tickets&_a=submit" title="Submit A Ticket" target="_blank">Submit A Ticket </a></li>
<li class="ft4"><a href="http://support.mylive-tech.com/" title="Support Suite" target="_blank">Support Suite</a></li>
<li class="ft5"><a href="http://support.mylive-tech.com/index.php?_m=knowledgebase&_a=view" title="Read Our Knowledge Base" target="_blank">Read Knowledge Base</a></li>
</ul>
<a class="back-to-top" href="#top">Back to Top</a>
</div>
<?php 
}


/** Add support for 3-column footer widgets */

add_theme_support( 'genesis-footer-widgets', 3 );



/** Register widget areas */

genesis_register_sidebar( array(

	'id'			=> 'home-top-1',

	'name'			=> __( 'Home Top #1', 'enterprise' ),

	'description'	=> __( 'This is home top #1 section.', 'enterprise' ),

) );

genesis_register_sidebar( array(

	'id'			=> 'home-top-2',

	'name'			=> __( 'Home Top #2', 'enterprise' ),

	'description'	=> __( 'This is home top #2 section.', 'enterprise' ),

) );

genesis_register_sidebar( array(

	'id'			=> 'home-middle-1',

	'name'			=> __( 'Home Middle #1', 'enterprise' ),

	'description'	=> __( 'This is home middle #1 section.', 'enterprise' ),

) );

genesis_register_sidebar( array(

	'id'			=> 'home-middle-2',

	'name'			=> __( 'Home Middle #2', 'enterprise' ),

	'description'	=> __( 'This is home middle #2 section.', 'enterprise' ),

) );

genesis_register_sidebar( array(

	'id'			=> 'home-middle-3',

	'name'			=> __( 'Home Middle #3', 'enterprise' ),

	'description'	=> __( 'This is home middle #3 section.', 'enterprise' ),

) );
