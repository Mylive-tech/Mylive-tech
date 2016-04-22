<div class="entry">
<!--If no results are found-->
	<h1><?php esc_html_e('No Results Found','Divi'); ?></h1>
	
    <h4>Looking for something? We're here to help </h4>
<p>Try refining your search, see more options below or, <strong>Call us toll-free on 1 (888) 361-8511</strong> for more information </p>
</div>

<hr>
<div class="clearfix"> </div>

<div class="g1-grid">
<h3>Browse Live-Tech Products & Services </h3>
                          <table class="sitemap-ul">
    <?php
    $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'meta_query' => array( array('key' => '_visibility','value' => array('catalog', 'visible'))) );
	 $args['meta_key'] = '_price';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'asc'; 
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
<!--End if no results are found-->