<?php
/**
 * The Template Part for displaying the footer.
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
?>
            </div>
            <!-- END #g1-content-area -->
            <?php
                /* Executes a custom hook.
                 * If you want to add some content after the g1-content-area,
                 * hook into 'g1_content_end' action.
                 */
                do_action( 'g1_content_end' );
            ?>
        </div>

        <?php get_template_part( 'template-parts/g1_background', 'content' ); ?>
	</div>
	<!-- END #g1-content -->	

	<?php 
		/* Executes a custom hook.
		 * If you want to add some content after the g1-content,
		 * hook into 'g1_content_after' action.
		 */	
		do_action( 'g1_content_after' );
	?>

    <?php
        // For the SEO purposes the preheader is placed here
        get_template_part( 'g1_preheader' );
    ?>


	<?php get_template_part( 'g1_prefooter' ); ?>
	
	<?php 
		/* Executes a custom hook.
		 * If you want to add some content before the g1-footer,
		 * hook into 'g1_footer_before' action.
		 */	
		do_action( 'g1_footer_before' );
	?>
	
	<!-- BEGIN #g1-footer -->
	<footer id="g1-footer" role="contentinfo">
            <?php
                /* Executes a custom hook.
                 * If you want to add some content before the g1-footer-area,
                 * hook into 'g1_footer_begin' action.
                 */
                do_action( 'g1_footer_begin' );
            ?>

            <!-- BEGIN #g1-footer-area -->
            <div id="g1-footer-area" class="g1-layout-inner">
                <nav id="g1-footer-nav">
                    <?php
                        if ( has_nav_menu( 'footer_nav' ) ) {
                            $footer_nav = array(
                                'theme_location'	=> 'footer_nav',
                                'container'			=> '',
                                'menu_id'			=> 'g1-footer-nav-menu',
                                'menu_class'		=> '',
                                'depth'				=> 1
                            );
                            wp_nav_menu($footer_nav);
                        } else {
                            $helpmode = G1_Helpmode(
                                'empty_footer_navigation',
                                __( 'Empty Footer Navigation', 'g1_theme' ),
                                '<p>' . sprintf( __( 'You should <a href="%s">assign a menu to the Footer Navigation Theme Location</a>', 'g1_theme' ), network_admin_url( 'nav-menus.php' ) ) . '</p>'
                            );
                            $helpmode->render();
                        }
                     ?>
                </nav>
                <p id="g1-footer-text"><?php echo g1_get_theme_option( 'general', 'footer_text', '' ); ?></p>
            </div>
            <!-- END #g1-footer-area -->

            <?php
                /* Executes a custom hook.
                 * If you want to add some content after the g1-footer-area,
                 * hook into 'g1_footer_end' action.
                 */
                do_action( 'g1_footer_end' );
            ?>

        <?php get_template_part( 'template-parts/g1_background', 'footer' ); ?>
	</footer>
	<!-- END #g1-footer -->

    <?php if ( 'none' !== g1_get_theme_option( 'general', 'scroll_to_top', 'standard' ) ): ?>
        <a href="#page" id="g1-back-to-top"><?php _e( 'Back to Top', 'g1_theme' ); ?></a>
    <?php endif; ?>
	
	<?php 
		/* Executes a custom hook.
		 * If you want to add some content after the g1-footer,
		 * hook into 'g1_footer_after' action.
		 */	
		do_action( 'g1_footer_after' );
	?>
</div>
<!-- END #page -->
<script>
( function( $ ) {
    $( document.body ).on( 'post-load', function () {
        // New posts have been added to the page.
    } );
} )( jQuery );
</script>
<?php wp_footer(); ?>
 <!-- BEGIN SUPPORT SUITE TAG - DO NOT EDIT! --><div id="proactivechatcontainer5ssj1hdd06"></div><div id="swifttagcontainer5ssj1hdd06" style="display: none;"><div id="swifttagdatacontainer5ssj1hdd06"></div></div> <script type="text/javascript">var swiftscriptelem5ssj1hdd06=document.createElement("script");swiftscriptelem5ssj1hdd06.type="text/javascript";var swiftrandom = Math.floor(Math.random()*1001); var swiftuniqueid = "5ssj1hdd06"; var swifttagurl5ssj1hdd06="http://support.mylive-tech.com/visitor/index.php?/LiveChat/HTML/Monitoring/cHJvbXB0dHlwZT1jaGF0JnVuaXF1ZWlkPTVzc2oxaGRkMDYmdmVyc2lvbj00LjY2LjImcHJvZHVjdD1mdXNpb24mY3VzdG9tb25saW5lPSZjdXN0b21vZmZsaW5lPSZjdXN0b21hd2F5PSZjdXN0b21iYWNrc2hvcnRseT0KMGY3MmRiMzQxZTIxM2UyMjFkNGZmN2FkZGFjZTRlYTRiYjRhZWUwOQ==";setTimeout("swiftscriptelem5ssj1hdd06.src=swifttagurl5ssj1hdd06;document.getElementById('swifttagcontainer5ssj1hdd06').appendChild(swiftscriptelem5ssj1hdd06);",1);</script><!-- END SUPPORT SUITE TAG - DO NOT EDIT! -->
</body>
</html>