<?php
/**
 * The Template Part for displaying the Preheader Theme Area.
 * 
 * The preheader is a collapsible, widget-ready theme area above the header.
 *
 * For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package     G1_Framework
 * @subpackage  G1_Theme01
 * @since       G1_Theme01 1.0.0
 */

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php 
	$g1_mapping = array(
		'1/1'	=> 'g1-max',
		'1/2'	=> 'g1-one-half',
		'1/3'	=> 'g1-one-third',
		'1/4'	=> 'g1-one-fourth',
		'3/4'	=> 'g1-three-fourth',
	);

	$g1_composition = g1_get_theme_option( 'ta_preheader', 'composition', '1/3+1/3+1/3' );
    $g1_composition = 'none' === $g1_composition ? '' : $g1_composition;

	$g1_rows = strlen( $g1_composition ) ? explode( '_', $g1_composition ) : array();
    $g1_index = 1;
?>
<?php
    /* Executes a custom hook.
     * If you want to add some content before the g1-preheader,
     * hook into 'g1_preheader_before' action.
     */
    do_action( 'g1_preheader_before' );
?>

	<!-- BEGIN #g1-preheader -->
	<aside id="g1-preheader">
        <div class="g1-layout-inner">
            <?php
                /* Executes a custom hook.
                 * If you want to add some content before the g1-preheader-widget-area,
                 * hook into 'g1_preheader_begin' action.
                 */
                do_action( 'g1_preheader_begin' );
            ?>

            <!-- BEGIN #g1-preheader-bar -->
            <div id="g1-preheader-bar" class="g1-meta">
                <?php if ( has_nav_menu( 'secondary_nav' ) ): ?>
                <nav id="g1-secondary-nav">
                    <a id="g1-secondary-nav-switch" href="#"></a>
                    <?php
                    wp_nav_menu( array(
                        'theme_location'	=> 'secondary_nav',
                        'container'			=> '',
                        'menu_id'			=> 'g1-secondary-nav-menu',
                        'menu_class'		=> null,
                        'depth'				=> 0
                    ));
                    ?>
                </nav>
                <?php endif; ?>

                <?php
                    // WPML language selector
                    do_action( 'icl_language_selector' );
                ?>

                <?php if ( 'none' !== g1_get_theme_option( 'ta_preheader', 'searchform' ) && !is_404() ): ?>
                <div class="g1-searchbox">
                    <a class="g1-searchbox__switch" href="#" title="Click here to search">
                        <div class="g1-searchbox__arrow"></div>
                        <strong></strong>
                    </a>
                    <?php get_search_form(); ?>
                    
                </div>
                <?php endif; ?>

                <?php
                // Render feeds
                if ( shortcode_exists( 'g1_social_icons') ) {
                    $g1_social_icons_size = g1_get_theme_option( 'ta_preheader', 'g1_social_icons' );
                    if ( is_numeric( $g1_social_icons_size ) ) {
                        $g1_social_icons_size = intval( $g1_social_icons_size );
                        echo do_shortcode('[g1_social_icons template="list-horizontal" size="'. $g1_social_icons_size . '" hide="label, caption"]');
                    }
                }
                ?>
                
                <ul class="g1-grid numbrs">
                <li class="g1-column g1-one-fourth g1-valign-top mltfblike toll-free"><iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fmylivetech&amp;width=200&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=21&amp;appId=715319921835642" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe></li>
                <li class="g1-column g1-one-fourth g1-valign-top searchsearch" style="color:white"><?php include (TEMPLATEPATH . '/searchform.php'); ?></li>
    			<li class="g1-column g1-one-fourth g1-valign-top lt-woo-top lt-tollfree barleft" style="color:white;font-size:13px;min-height:26px"> Call us: 1 (888) 361-8511
                </li>
                <li class="g1-column g1-one-fourth g1-valign-top lt-woo-top lt-more-tab" style="min-height:26px">
<?php
	if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    echo 'Welcome, ' . $current_user->user_firstname . '!';
} else {
    echo 'Welcome, visitor!';
}
	?> | <?php if ( is_user_logged_in() ) { ?>
 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>"><?php _e('My Account','woothemes'); ?></a> | <?php
if ( is_user_logged_in() ) {
    echo '<a href="'. wp_logout_url() .'">Logout</a>';
}
?>
 <?php } 
 else { ?>
 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Register','woothemes'); ?>"><?php _e('Login / Register','woothemes'); ?></a>
 <?php } ?>
 
</li> 
              </ul>             
                
            </div>
            <!-- END #g1-preheader-bar -->

            <?php if ( count( $g1_rows ) ): ?>
            <!-- BEGIN #g1-preheader-widget-area -->
            <div id="g1-preheader-widget-area">
            
            <!--
            <ul class="g1-grid numbrs">
                
                <li class="g1-column g1-one-third g1-valign-top toll-free"><select name="lt-phone-numbers" id="lt-top-phone-numbers">
  <option style="text-align:left;">Call Us 1 (888) 361-8511</option>
  <option style="text-align:left;">Miami, FL: 1(786) 975-2146</option>
  <option style="text-align:left;">Ft. Lauderdale, FL: 1(954) 482-4464</option>
  <option style="text-align:left;">Atlanta, Georgia: 1(404) 799-9035</option>
  <option style="text-align:left;">Toronto, Canada: 1(416) 900-1260</option>
  <option style="text-align:left;">UK, London: +44 020-3051-6419</option>
  <option style="text-align:left;">Brasil, São Paulo: +55 12-3212-5101</option>
  <option style="text-align:left;">Australia: +61 284 171 012</option>
</select>

</li>
<li class="g1-column g1-one-third g1-valign-top toll-free searchform1">
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

</li>
<li class="g1-column g1-one-third g1-valign-top lt-woo-top">
<?php
	if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    echo 'Welcome, ' . $current_user->user_firstname . '!';
} else {
    echo 'Welcome, visitor!';
}
	?> | <?php if ( is_user_logged_in() ) { ?>
 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>"><?php _e('My Account','woothemes'); ?></a> | <?php
if ( is_user_logged_in() ) {
    echo '<a href="'. wp_logout_url() .'">Logout</a>';
}
?>
 <?php } 
 else { ?>
 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Register','woothemes'); ?>"><?php _e('Login / Register','woothemes'); ?></a>
 <?php } ?>
</li>
    
              </ul> -->
            
                <?php foreach( $g1_rows as $g1_row ): ?>
                <div class="g1-grid">
                    <?php
                        $g1_columns = strlen( $g1_row ) ? explode( '+', $g1_row ) : array();
                    ?>
                    <?php foreach( $g1_columns as $g1_column ): ?>
                        <div class="g1-column <?php echo $g1_mapping[ $g1_column ]?>">
                            <?php g1_sidebar_render( 'preheader-' . ( $g1_index++ ) ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- END #g1-preheader-widget-area -->
            <?php endif; ?>

            <?php
                /* Executes a custom hook.
                 * If you want to add some content after the g1-preheader-widget-area,
                 * hook into 'g1_preheader_end' action.
                 */
                do_action( 'g1_preheader_end' );
            ?>
        </div><!-- .g1-inner -->

        <?php get_template_part( 'template-parts/g1_background', 'preheader' ); ?>
	</aside>
	<!-- END #g1-preheader -->	
	
	<?php 
		/* Executes a custom hook.
		 * If you want to add some content after the g1-preheader,
		 * hook into 'g1_preheader_after' action.
		 */	
		do_action( 'g1_preheader_after' );
	?>