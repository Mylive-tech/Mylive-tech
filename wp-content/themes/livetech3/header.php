<?php

if ( substr_count( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) ) {
    ob_start( "ob_gzhandler" );
}
else {
    ob_start();
}

error_reporting(0);
/**
 * The Header for our theme.
 *
 * For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Framework
 * @subpackage G1_Theme03
 * @since G1_Theme03 1.0.0
 */

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );

?><!DOCTYPE html>
<!--[if IE 7]>
<html class="no-js lt-ie10 lt-ie9 lt-ie8" id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie10 lt-ie9" id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 9]>
<html class="no-js lt-ie10" id="ie9" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !IE]><!-->
<html class="no-js" <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title><?php wp_title( '', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <?php wp_head(); ?>
	<link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/custom-css/custom-style.css" />
	
	
	<?php include (TEMPLATEPATH . '/headerstyle.php' ); ?>
    
    
</head>
<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
<div id="page">
    <div id="g1-top">
	<?php 
		/* Executes a custom hook.
		 * If you want to add some content before the g1-header, hook into 'g1_header_before' action.
		 */	
		do_action( 'g1_header_before' );
		get_template_part( 'g1_preheader' );
	?>
    <?php
        // For the SEO purposes the preheader is placed here
        
    ?>

	<!-- BEGIN #g1-header -->
    <div id="g1-header-waypoint">
    
    
    
	<div id="g1-header" role="banner">
        <div class="g1-layout-inner">
            <?php
                /* Executes a custom hook.
                 * If you want to add some content before the g1-primary-bar, hook into 'g1_header_begin' action.
                 */
                do_action( 'g1_header_begin' );
            ?>

            <div id="g1-primary-bar">
                <?php G1_Theme()->render_site_id(); ?>

                <!-- BEGIN #g1-primary-nav -->
                <nav id="g1-primary-nav" class="g1-nav--<?php echo sanitize_html_class( g1_get_theme_option('ta_header', 'primary_nav_style', 'none') ); ?> g1-nav--collapsed">
                   <span class="menu-placeholder">Main Menu</span> <a id="g1-primary-nav-switch" href="#"><?php echo __('', 'g1_theme')?></a>
                    <?php
                        if ( has_nav_menu( 'primary_nav' ) ) {
                            wp_nav_menu( array(
                                'theme_location'	=> 'primary_nav',
                                'container'			=> '',
                                'menu_class'        => '',
                                'menu_id'			=> 'g1-primary-nav-menu',
                                'depth'				=> 0,
                                'walker'            => new G1_Extended_Walker_Nav_Menu(array(
                                    'with_description' => true,
                                    'with_icon' => true,
                                )),
                            ));
                        } else {
                            $helpmode = G1_Helpmode(
                                'empty_primary_nvation',
                                __( 'Empty Primary Navigation', 'g1_theme' ),
                                '<p>' . sprintf( __( 'You should <a href="%s">assign a menu to the Primary Navigation Theme Location</a>', 'g1_theme' ), network_admin_url( 'nav-menus.php' ) ) . '</p>'
                            );
                            $helpmode->render();
                        }
                    ?>

                    <?php if ( apply_filters( 'g1_header_woocommerce_minicart', is_plugin_active('woocommerce/woocommerce.php') ) ): ?>
                    <div class="g1-cartbox">
                        <a class="g1-cartbox__switch" href="#">
                            <div class="g1-cartbox__arrow"></div>
                            <span><i class="icon-shopping-cart"></i><?php global $woocommerce; ?>
                            <?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?></span>
                            
                        </a>
                        

                        <div class="g1-cartbox__box">
                            <div class="g1-inner woocommerce">
                                <?php
                                    $g1_instance = array(
                                        'title' => '',
                                        'number' => 1
                                    );
                                    $g1_args = array(
                                        'title' => '',
                                        'before_widget' => '',
                                        'after_widget' => '',
                                        'before_title' => '<div class="g1-cartbox__title">',
                                        'after_title' => '</div>',
                                    );
                                    $g1_widget = new WC_Widget_Cart();
                                    $g1_widget->number = $g1_instance['number'];
                                    $g1_widget->widget( $g1_args, $g1_instance );
                                ?>
                                <p class="g1-cartbox__empty"><?php _e( 'No products in the cart.', 'woocommerce' ); ?></p>
                                <p> <?php if ( is_user_logged_in() ) { ?>
 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>"><?php _e('My Account','woothemes'); ?></a>
 <?php } 
 else { ?>
 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Register','woothemes'); ?>"><?php _e('Login / Register','woothemes'); ?></a>
 <?php } ?></p>
                            </div>

                        </div>
                    </div>
                    <?php endif; ?>



                </nav>
                <!-- END #g1-primary-nav -->
                
                
            </div><!-- END #g1-primary-bar -->
            

            <?php
                /* Executes a custom hook.
                 * If you want to add some content after the g1-primary-bar, hook into 'g1_header_end' action.
                 */
                do_action( 'g1_header_end' );
				
				if(is_front_page()) {
            ?>
            
   <?php } ?>
            
            

		</div>

        <?php //get_template_part( 'template-parts/g1_background', 'header' ); ?>
		<div class="g1-background"></div>
		<?php
 if(is_woocommerce()) {	
	 
	 $taxars =  array('include'=>array(47,38,30,29,37,83));
	 $product_terms = get_terms('product_cat',$taxars);
	 
	 //print($product_terms);
	 if ( !empty( $product_terms ) && !is_wp_error( $product_terms ) ){
	 $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
?>


<div class="maincatnav">
<div class="nav-container">
  <div>
  
    <label class="responsive_menu" for="responsive_menu"> <span>Shop Menu</span> </label>
    <input id="responsive_menu" type="checkbox">
    <ul class="menu">
       <li> <a href="<?php echo $shop_page_url; ?>"><i class="icon-shopping-cart"></i>&nbsp;<strong>Shop</strong>&nbsp;&nbsp;&nbsp;<i class="icon-rightnav"></i></a> </li>
	   
	  <?php 
		foreach ( $product_terms as $term ) { 
		
		if($term->term_id=='47')
		$tername = 'Support Services';
		
		if($term->term_id=='38')
		$tername = 'Cloud Services';
		
		if($term->term_id=='30')
		$tername = 'Design & Hosting';
		
		if($term->term_id=='29')
		$tername = 'Lynk VoIP';
		
		if($term->term_id=='37')
		$tername = 'Hardware';
		
		if($term->term_id=='83')
		$tername = 'Software';
		 
		?> 
	   
      <li class="dropdown"><a href="#" ><?php echo $tername; ?></a>
		 <?php 
		 $args = array(
        'parent' => $term->term_id,
        'hide_empty' => 0,
        'hierarchical' => true,
        'depth'  => 1,
        );
	    $termchildren = get_terms('product_cat', $args );      // get category children
		if($termchildren) {
		?>
        <ul >
		<?php foreach($termchildren as $termchild) { ?>
          
           <li><a href="<?php echo get_term_link($termchild); ?>"><?php echo $termchild->name; ?></a>
		   <?php
				 $argsanother = array( 'parent' => $termchild->term_id,'hide_empty' => 0, 'hierarchical' => false, 'depth'  => 3,);
				 $termchildrensecodlevel = get_terms('product_cat', $argsanother ); 
				 
				 if($termchildrensecodlevel) {	
				 ?>
                 <ul>
			      <?php				 
				  foreach($termchildrensecodlevel as $secondchild) {			
				  ?>
                   <li><a href="<?php echo get_term_link($secondchild); ?>"><?php echo $secondchild->name; ?></a></li>
				  <?php } ?>
                  </ul>
			<?php } ?>
          </li>
		  
		  <?php } ?>
		  
        </ul>
		<?php } ?>
      </li>
	  
	  <?php } ?>
    
    </ul>
  </div>
</div>

</div>
	
	<?php 
	} 
 } 
?>
	</div>
    </div>
	<!-- END #g1-header -->	

	<?php 
		/* Executes a custom hook.
		 * If you want to add some content after the g1-header, hook into 'g1_header_after' action.
		 */	
		do_action( 'g1_header_after' );
	?>
	
	<?php 
		/* Executes a custom hook.
		 * If you want to add some content before the g1-content, hook into 'g1_content_before' action.
		 */	
		do_action( 'g1_content_before' );
			
	 ?>
	
	<?php get_template_part( 'g1_precontent' ); ?>


      <div class="g1-background"></div>
    </div>

	<!-- BEGIN #g1-content -->
	<div id="g1-content">
    
    <div id="lt-cp" style="display:none">
    <div class="lt-cp-btn"><img src="<?php bloginfo ('template_url')?>/css/lt-logo-cp.png" alt="CP" /></div>
    <ul class="lt-cp-main-tabs">
      <li class="lt-cp-btn2"> <a href="javascript:"> <i style="#icon-1.g1-icon { color: #ffffff; background-color: red; border-color: red; }" class="icon-chevron-left g1-icon g1-icon--solid g1-icon--small  " id="icon-1"></i></a></li>
                    	<li >  <a title="Live Chat With Agent" onclick="window.open('http://support.mylive-tech.com/visitor/index.php?_m=livesupport&_a=startclientchat&sessionid=yrph78uldkjpq9mkvf3jwaeca5hghb5p&proactive=0&departmentid=0&randno=19&fullname=&email=', 'newwindow', 'width=500, height=350'); return false;" href="#" target="_blank"><i class="icon-user"></i> Live&nbsp;Chat</a></li>
						<li ><a title="Start A Remote Session" href="http://199.101.49.90/~mylive5/mylive-tech.com/connect/"><i class="icon-desktop"></i> Remote Session</a></li>
						<li ><a title="Submit A Ticket" href="http://support.mylive-tech.com/index.php?_m=tickets&amp;_a=submit" target="_blank"><i class="icon-envelope-alt"></i> Submit A Ticket</a></li>
						<li><a title="Live-Tech Support Suite" href="http://support.mylive-tech.com/" target="_blank"><i class="icon-livetech-suite"></i> Support Suite</a></li>
						<li ><a title="Read Knowledge Base Articles" href="http://support.mylive-tech.com/index.php?_m=knowledgebase&amp;_a=view" target="_blank"><i class="icon-folder-open"></i> Knowledge Base</a></li>
    </ul>
  </div>
    
     <script type="text/javascript">
   $(function() {
    $.fn.ToggleSlide = function() {
        return this.each(function() {
            //$(this).css('position', 'absolute'); 
            if(parseInt($(this).css('left')) < -30) {
                $(this).animate({ 'left' : '-30px' }, 300, function() {
                    //$(this).css('position', 'relative');
                });
            }
            else {
                $(this).animate({ 'left' : '-715px' }, 300, function() {
                    //$(this).css('position', 'relative');
                });
            }
        });
    };
    
	$('#lt-cp').ToggleSlide();
    $(".lt-cp-btn").bind("click", function() {
        $('#lt-cp').ToggleSlide();
    });
	
	$(".lt-cp-btn2").bind("click", function() {
        $('#lt-cp').ToggleSlide();
    });
	
});
    </script>
    
    
   <!-- <div class="lt-hidden"><ul id="lt-cust-support-links">
   <li class="linx linx-close"> <a href="javascript:" id="slidex1"> <i style="#icon-1.g1-icon { color: #ffffff; background-color: red; border-color: red; }" class="icon-chevron-left g1-icon g1-icon--solid g1-icon--small  " id="icon-1"></i></li></a>
                    	<li class="linx">  <a title="Live Chat With Agent" onclick="window.open('http://support.mylive-tech.com/visitor/index.php?_m=livesupport&_a=startclientchat&sessionid=yrph78uldkjpq9mkvf3jwaeca5hghb5p&proactive=0&departmentid=0&randno=19&fullname=&email=', 'newwindow', 'width=500, height=350'); return false;" href="#" target="_blank"><i class="icon-user"></i> Live&nbsp;Chat</a></li>
						<li class="linx"><a title="Start A Remote Session" href="http://199.101.49.90/~mylive5/mylive-tech.com/connect/"><i class="icon-desktop"></i> Remote Session</a></li>
						<li class="linx"><a title="Submit A Ticket" href="http://support.mylive-tech.com/index.php?_m=tickets&amp;_a=submit" target="_blank"><i class="icon-envelope-alt"></i> Submit A Ticket</a></li>
						<li class="linx"><a title="Live-Tech Support Suite" href="http://support.mylive-tech.com/" target="_blank"><i class="icon-livetech-suite"></i> Support Suite</a></li>
						<li class="linx"><a title="Read Knowledge Base Articles" href="http://support.mylive-tech.com/index.php?_m=knowledgebase&amp;_a=view" target="_blank"><i class="icon-folder-open"></i> Knowledge Base</a></li>
                        <li class="linx-right"><a href="javascript:" id="slidex"><!--<i style="#icon-1.g1-icon { color: #ffffff; background-color: #0099cc; border-color: #0099cc; }" class="icon-user g1-icon g1-icon--solid g1-icon--small g1-icon--circle " id="icon-1"></i><img src="<?php echo get_template_directory_uri(); ?>/css/lt-logo-cp.png" alt="Live-Tech" width="113" height="50"></a></li>
                        <!--<li class="linx-logo"><img src="<?php echo get_template_directory_uri(); ?>/css/lt-logo-symbol-only.png" alt="Live-Tech" width="69" height="53"></li> 
                       
					</ul> </div> -->

<!--
<script>
$(document).ready(function(){
    $('#slidex').click(function(){
    var hidden = $('.lt-hidden');
    if (hidden.hasClass('visible')){
        hidden.animate({"left":"-61.60%"}, "slow").removeClass('visible');
    } else {
        hidden.animate({"left":"-41px"}, "slow").addClass('visible');
    }
    });
});

</script>

<script>
$(document).ready(function(){
    $('#slidex1').click(function(){
    var hidden = $('.lt-hidden');
    if (hidden.hasClass('visible')){
        hidden.animate({"left":"-61.60%"}, "slow").removeClass('visible');
    } else {
        hidden.animate({"left":"-41px"}, "slow").addClass('visible');
    }
    });
});

</script>
    -->
    
    <!-- Clickable Nav 
		<div class="click-nav">
			<ul id="lt-cust-support">
				<li>
					<a id="clickrbutton" title="Customer Portal">Customer <br>Portal</a>
					<ul id="lt-cust-support-links">
                    	<li>  <a title="Live Chat With Agent" onclick="window.open('http://support.mylive-tech.com/visitor/index.php?_m=livesupport&_a=startclientchat&sessionid=yrph78uldkjpq9mkvf3jwaeca5hghb5p&proactive=0&departmentid=0&randno=19&fullname=&email=', 'newwindow', 'width=500, height=350'); return false;" href="#" target="_blank"><i class="icon-user"></i> Live&nbsp;Chat</a></li>
						<li><a title="Start A Remote Session" href="http://199.101.49.90/~mylive5/mylive-tech.com/connect/"><i class="icon-desktop"></i> Remote Session</a></li>
						<li><a title="Submit A Ticket" href="http://support.mylive-tech.com/index.php?_m=tickets&amp;_a=submit" target="_blank"><i class="icon-envelope-alt"></i> Submit A Ticket</a></li>
						<li><a title="Live-Tech Support Suite" href="http://support.mylive-tech.com/" target="_blank"><i class="icon-livetech-suite"></i> Support Suite</a></li>
						<li><a title="Read Knowledge Base Articles" href="http://support.mylive-tech.com/index.php?_m=knowledgebase&amp;_a=view" target="_blank"><i class="icon-folder-open"></i> Knowledge Base</a></li>
					</ul>
				</li>
			</ul>
		</div>
        <script>
		$("#clickrbutton").click(function () {
    // Set the effect type
    var effect = 'slide';
    // Set the options for the effect type chosen
    var options = { direction: 'left' };
    // Set the duration (default: 400 milliseconds)
    var duration = 600;
    $('.click-nav').toggle(effect, options, duration);
});
		</script>
		Clickable Nav -->
    
        <div class="g1-layout-inner">
        <!--<div class="loading-page"><img src="<?php echo get_template_directory_uri(); ?>/images/live-tech-logo-loading.png" alt="Live-Tech Logo" width="117" height="89"><br>Loading</div>-->
            <?php
                /* Executes a custom hook.
                 * If you want to add some content before the g1-content-area, hook into 'g1_content_begin' action.
                 */
                do_action( 'g1_content_begin' );
            ?>
            <div id="g1-content-area">