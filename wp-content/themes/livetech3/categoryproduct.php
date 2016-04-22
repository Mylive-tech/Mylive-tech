<?php
include('../../../wp-config.php');



if($_POST) 
{


////////////////////////////custom function //////////////////////////////////////

function order_by_popularity_post_clauses( ) {
         global $wpdb;
 
         $args['orderby'] = "$wpdb->postmeta.meta_value+0 DESC, $wpdb->posts.post_date DESC";
 
         return $args;
}

function order_by_rating_post_clauses( ) {
         global $wpdb;
 
         $args['fields'] .= ", AVG( $wpdb->commentmeta.meta_value ) as average_rating ";
 
         $args['where'] .= " AND ( $wpdb->commentmeta.meta_key = 'rating' OR $wpdb->commentmeta.meta_key IS null ) ";
 
         $args['join'] .= "
             LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
             LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
         ";
 
         $args['orderby'] = "average_rating DESC, $wpdb->posts.post_date DESC";
 
         $args['groupby'] = "$wpdb->posts.ID";
 
         return $args;
}

///////////////////////////////////////// custom function end here //////////////////////////////////////////
$orderBY = $_POST['order_bylist'];
$selected =  $_POST['order_bylist'];
$product_cat = $_POST['pcat'];


         // Get ordering from query string unless defined
         if ( ! $orderby ) {
             $orderby_value = isset( $orderBY ) ? wc_clean( $orderBY ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
 
             // Get order + orderby args from string
             $orderby_value = explode( '-', $orderby_value );
             $orderby       = esc_attr( $orderby_value[0] );
             $order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
         }
 
         $orderby = strtolower( $orderby );
         $order   = strtoupper( $order );
 
         $args = array();
 
         // default - menu_order
         $orderBY  = 'menu_order title';
         $order    = $order == 'DESC' ? 'DESC' : 'ASC';
         $meta_key = '';
 
         switch ( $orderby ) {
             case 'rand' :
                 $orderBY  = 'rand';
             break;
             case 'date' :
                 $orderBY  = 'date';
                 $order    = $order == 'ASC' ? 'ASC' : 'DESC';
             break;
             case 'price' :
                 $orderBY  = 'meta_value_num';
                 $order    = $order == 'DESC' ? 'DESC' : 'ASC';
                 $meta_key = '_price';
             break;
             case 'popularity' :
                 $args['meta_key'] = 'total_sales';
                 $getotherfield = order_by_popularity_post_clauses();
				 $orderBY = $getotherfield['orderby'];
				 
             break;
             case 'rating' :
                  $res = order_by_rating_post_clauses();
				  $orderBY  = $res['orderby'];
             break;
             case 'title' :
                 $orderBY  = 'title';
                 $order    = $order == 'DESC' ? 'DESC' : 'ASC';
             break;
         }
 
        
//echo $orderBy.'-'.$order.'-'.$meta_key;

 if($product_cat!='') {
 
 $args = array(
    'parent' => $product_cat,
    'hide_empty' => 0,
    'hierarchical' => true,
    'depth'  => 4,
    );
	
 $termchildren = get_terms('product_cat', $args );
 
 if($termchildren) {
 
 ?>

<div id="horizontalTab">
  <ul class="resp-tabs-list">
    <?php 
	global $woo_options, $woocommerce;
	$totalcatnum = 4; 
	foreach ( $termchildren as $child ) 
	{ 
	$thumbnail_id = get_woocommerce_term_meta( $child->term_id, 'thumbnail_id', true );
    $termimage = wp_get_attachment_url( $thumbnail_id ); 
	?>
    <li <?php if($totalcatnum%3==0) { ?> style="border-right: none;" <?php } ?>>
      <?php //if($termimage!='') { woo_image('key=image&src='. $termimage .'&meta='.$child->name.'&width=32&height=32'); } ?>
      <img src="<?php echo $termimage; ?>" height="32" width="32" title="<?php echo $child->name; ?>" alt="<?php echo $child->name; ?>" style="vertical-align:middle" /> <?php echo $child->name; ?></li>
    <?php 
	$totalcatnum++; 
	}  
	?>
  </ul>
  <div class="resp-tabs-container">
    <?php 
	
	 
	 
	 $numofrows = 1;
	 foreach ( $termchildren as $child ) { 
	 
	// echo $orderBy.'-'.$meta_key; die;
	 
	remove_all_filters('posts_orderby'); 
	$query_args = array('post_type' => 'product', 'orderby' => $orderBY,'order' => $order, 'posts_per_page' => 100, 'post_status' => 'publish', 'meta_key' => $meta_key, 
	'meta_query' => array('relation' => 'AND', array('meta_key' => $meta_key)), 'tax_query' => array(array('taxonomy' => 'product_cat','field' => 'id','terms' => $child->term_id)));
	//print_r($query_args); die;
	
	$r = new WP_Query($query_args);
	
	//echo "{$r->request}"; die;
	?>
	
    <div>
      <div class="prodarea">
        <h2 style="padding-top:10px;padding-bottom:10px;border-bottom: thin dotted #ccc; text-align:center;"> <?php echo $child->name; ?></h2>
		<div style="float:left; width:100%;">
        <form method="get" class="woocommerce-ordering catprodselection" >
          <select class="orderby" name="orderby" id="orderby-<?php echo $numofrows; ?>" onChange="showorder(<?php echo $product_cat;?>, <?php echo $numofrows; ?>)">
            <option value="menu_order" <?php if($selected=="menu_order"){ echo 'selected="selected"';}?> >Default sorting</option>
            <option value="popularity" <?php if($selected=="popularity"){ echo 'selected="selected"';}?>>Sort by popularity</option>
            <option  value="rating" <?php if($selected=="rating"){ echo 'selected="selected"';}?>>Sort by average rating</option>
            <option value="date" <?php if($selected=="date"){ echo 'selected="selected"';}?>>Sort by newness</option>
            <option value="price" <?php if($selected=="price"){ echo 'selected="selected"';}?>>Sort by price: low to high</option>
            <option value="price-desc" <?php if($selected=="price-desc"){ echo 'selected="selected"';}?>>Sort by price: high to low</option>
          </select>
        </form>
		</div>
        <ul class="product_list" id="product_list-<?php echo $numofrows; ?>">
		<div id="wait" class="wait" style=" display:none;"><img src='<?php echo get_template_directory_uri(); ?>/images/lt-logo-anim.gif' width="117" height="89" /></div>
          <?php
	$totalcattow = 1;
	while ($r->have_posts()) : $r->the_post(); 
    global $product, $woocommerce_loop;	
	
	 $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') ); 
	 $price = get_post_meta( get_the_ID(), '_regular_price', true);
	 $sale = get_post_meta( get_the_ID(), '_sale_price', true); 
	 
	 // Store loop count we're currently on
	if ( empty( $woocommerce_loop['loop'] ) )
		$woocommerce_loop['loop'] = 0;
	
	// Store column count for displaying the grid
	if ( empty( $woocommerce_loop['columns'] ) )
		$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
	
	// Ensure visibility
	if ( ! $product->is_visible() )
		return;
	
	// Increase loop count
	$woocommerce_loop['loop']++;
	
	// Extra post classes
	$classes = array();
	if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
		$classes[] = 'first';
	if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
		$classes[] = 'last';
	
	
	?>
          <li class="g1-collection__item g1-column g1-one-third g1-valign-top">
            <article <?php post_class( $classes ); ?>>
              <figure class="entry-featured-media"><a class="g1-frame g1-frame--none g1-frame--inherit g1-frame--center" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"> <span class="g1-decorator">
                <?php if($url!='') { ?>
                <img width="125" height="125" class="attachment-shop_catalog wp-post-image" src="<?php echo $url; ?>" title="<?php the_title(); ?>" alt="<?php the_title(); ?>">
                <?php }else{ ?>
                <img src="<?php echo get_template_directory_uri(); ?>/images/no-imge.png" height="125" width="125" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" />
                <?php } ?>
                </span> <span class="g1-indicator g1-indicator-document"></span></a></figure>
              <h3 class="g1-h4">
                <?php the_title(); ?>
              </h3>
              <span class="pricespan">
              <?php 
  if ( $price_html = $product->get_price_html() ) : 
  echo $price_html;
  endif;  
  ?>
              </span> <a class="button" rel="nofollow" href="<?php the_permalink(); ?>" target="_blank">View Detail</a> </article>
          </li>
          <?php $totalcattow++; endwhile;  ?>
        </ul>
      </div>
    </div>
    <?php $numofrows++; } ?>
  </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#horizontalTab").easyResponsiveTabs({
          //  type: 'default', //Types: default, vertical, accordion           
           // width: 'auto', //auto or any width like 600px
           // fit: true,   // 100% fit in a container
            closed: 'accordion', // Start closed if in accordion view
            activate: function(event) { // Callback function if tab is switched
                var $tab = jQuery(this);
                var $info = jQuery('#tabInfo');
                var $name = jQuery('span', $info);

                $name.text($tab.text());

                $info.show();
            }
        });
    });
</script>
<?php
  }





 }else{
 echo 'Error: Product Id Missing';
 }
}
?>
