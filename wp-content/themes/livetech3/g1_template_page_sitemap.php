<?php
/**
 * Template Name: Page: Sitemap
 */

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php get_header(); ?>
        <div id="primary">
            <div id="content" role="main">
               <?php while ( have_posts() ) : the_post(); ?>

                   <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                       <?php
                       global $post;
                       $elems = G1_Elements()->get();

                       $title = $elems[ 'title' ] ? the_title( '', '', false ) : '';
                       $subtitle = wp_kses_data( get_post_meta( $post->ID, '_g1_subtitle', true ) );
                       ?>

                       <?php if( strlen( $title ) || strlen( $subtitle ) ): ?>
                       <header class="entry-header">
                            <div class="g1-hgroup">
                            <?php if ( strlen( $title ) ): ?>
                                <h1 class="entry-title"><?php echo $title; ?></h1>
                            <?php endif; ?>

                            <?php if ( strlen( $subtitle ) ) : ?>
                                <h3 class="entry-subtitle"><?php echo $subtitle; ?></h3>
                            <?php endif; ?>
                           </div>
                       </header><!-- .entry-header -->
                       <?php endif; ?>

                       <!-- BEGIN .entry-content -->
                       <div class="entry-content">
                           <h2 style="text-align:center"> Pages </h2>
                           <div class="g1-grid">
                           
                           <?php
	$pageArray = explode("</li>",wp_list_pages('title_li=&echo=0&depth=2'));
	$pageCount = count($pageArray) - 1;
	$pageColumns = round($pageCount / 3);
	$twoColumns = round($pageColumns + $pageColumns);
		
        for ($i=0;$i<$pageCount;$i++) {
		if ($i<$pageColumns){
			$pageLeft = $pageLeft.''.$pageArray[$i].'</li>';
		}
	        elseif ($i<$twoColumns) {
			$pageMiddle = $pageMiddle.''.$pageArray[$i].'</li>';
		}  
		elseif ($i>=$twoColumns) {
			$pageRight = $pageRight.''.$pageArray[$i].'</li>';
		}  
	 };
 ?>
<div class="g1-column g1-one-third sitemappages">
<ul class="sitemap-left">
	<?php echo $pageLeft; ?>
</ul>
</div>
<div class="g1-column g1-one-third  sitemappages">
<ul class="middle">
	<?php echo $pageMiddle; ?>
</ul>
</div>
<div class="g1-column g1-one-third  sitemappages">
<ul class="sitemap-right">
	<?php echo $pageRight; ?>
</ul></div>
                               
                           </div><!-- .grid -->

                          <div class="g1-divider g1-divider--none g1-divider--icon " id="g1-divider-1" style="display: block;"><span><i class="icon-shopping-cart"></i></span></div>
                           
                           <div class="g1-grid">
<h2 style="text-align:center"> Live-Tech Products & Services </h2>
                          <table class="sitemap-ul">
    <?php
    $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'meta_query' => array( array('key' => '_visibility','value' => array('catalog', 'visible'))) );
    $loop = new WP_Query( $args );
    $i = 0;?>
    <tr>

    <?php while ( $loop->have_posts() ) : $loop->the_post();
        if($i % 2) echo '</tr><tr>';?>
        <td>
          <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
        </td>

   <?php $i++; endwhile;?>
   </tr>
</table> </div>


 <?php echo do_shortcode( '[divider_top]' ); ?>

                           <?php if ( get_option( 'page_for_posts') ): ?>
                           <h2><a href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>"><?php echo get_the_title( get_option( 'page_for_posts' ) ); ?></a></h2>
                           <?php else: ?>
                           <h2 style="text-align:center"><?php _e( 'Blog & Resources', 'g1_theme' ); ?></h2>
                           <?php endif; ?>

                           <div class="g1-grid">
                               <div class="g1-column g1-one-third">
                                   <h3><?php _e( 'Latest entries', 'g1_theme' ); ?></h3>
                                   <ul>
                                       <?php wp_get_archives( 'type=postbypost&limit=10' ); ?>
                                   </ul>
                               </div>
                               <div class="g1-column g1-one-third">
                                   <h3><?php _e( 'Category Archives', 'g1_theme' ); ?></h3>
                                   <ul>
                                       <?php wp_list_categories( 'title_li=' ); ?>
                                   </ul>
                               </div>
                               <div class="g1-column g1-one-third">
                                   <h3><?php _e( 'Tag Archives', 'g1_theme' ); ?></h3>
                                   <?php wp_tag_cloud(); ?>
                               </div>
                           </div><!-- .grid -->
                           
                           
                          
                          
                            <?php do_action( 'g1_sitemap' ); ?>

                        </div>
                        <!-- END .entry-content -->
                        <footer class="entry-meta">
                            <?php edit_post_link( __( 'Edit', 'g1_theme' ), '<span class="edit-link">', '</span>' ); ?>
                        </footer>
                    </article><!-- #post-<?php the_ID(); ?> -->

                   

                <?php endwhile; ?>

            </div><!-- #content -->
        </div><!-- #primary -->

<?php get_footer(); ?>