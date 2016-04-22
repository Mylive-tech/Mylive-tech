<?php
/**
 * The Template Part for displaying the "No results" message, when there are no posts in the loop.
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
<p class="no-results">
    <?php _e( 'Sorry, but no results were found. Need help? Call us toll free on 1-888-361-8511 or <a href="http://199.101.49.90/~mylive5/mylive-tech.com/contact-us/"> contact us online </a>', 'g1_theme' ); ?>
</p>

<div class="g1-divider g1-divider--none g1-divider--icon " id="g1-divider-3"><span><i class="icon-info-sign"></i></span></div>

<h2 style="text-align: center;">Looking for something? We're here to help </h2>
<h4 style="text-align: center;"> Call us toll-free on 1 (888) 361-8511 or choose from below </h4>

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



