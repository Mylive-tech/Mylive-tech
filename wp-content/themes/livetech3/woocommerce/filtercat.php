<?php
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
		 
 
		do_action('woocommerce_before_main_content');
	?>

		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
            <header class="archive-header">
			    <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
            </header>

		<?php endif; ?>

		<?php do_action( 'woocommerce_archive_description' ); ?>

		<?php if ( have_posts() ) : ?>
    

			<?php
				/**
				 * woocommerce_before_shop_loop hook
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
				
				//do_action( 'woocommerce_pagination' );
			?>
		
		

			<?php woocommerce_product_loop_start(); ?>
			
			   
			
            
             
				<?php woocommerce_product_subcategories(); ?>
				
				<div style="clear:both;"></div>
				
				<?php 
				
				$orderby = $_GET['orderby']; 
				
				?>
				
				<li style="width:97%; margin:15px 0; float:left;">
				<form class="woocommerce-ordering prodpage" method="get">
	            <select name="orderby" class="orderby" style="width:100%; letter-spacing:0;">
		        <?php
			$catalog_orderby = apply_filters( 'woocommerce_catalog_orderby', array(
				'menu_order' => __( 'Default sorting', 'woocommerce' ),
				'popularity' => __( 'Sort by popularity', 'woocommerce' ),
				'rating'     => __( 'Sort by average rating', 'woocommerce' ),
				'date'       => __( 'Sort by newness', 'woocommerce' ),
				'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
				'price-desc' => __( 'Sort by price: high to low', 'woocommerce' )
			) );

			if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
				unset( $catalog_orderby['rating'] );

			foreach ( $catalog_orderby as $id => $name )
				echo '<option value="' . esc_attr( $id ) . '" ' . selected( $orderby, $id, false ) . '>' . esc_attr( $name ) . '</option>';
		?>
	</select>
	<?php
		// Keep query string vars intact
		foreach ( $_GET as $key => $val ) {
			if ( 'orderby' === $key || 'submit' === $key )
				continue;
			
			if ( is_array( $val ) ) {
				foreach( $val as $innerVal ) {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
				}
			
			} else {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
			}
		}
	?>
</form>
				
				</li>
				
				<div style="clear:both;"></div>
				
				
                
                <!--<h3 class="lt-products"> Products </h3>-->   

				<?php 
				 //echo $GLOBALS['wp_query']->request;
				 
				remove_all_filters('posts_orderby'); 
	            $query_args = array('post_type' => 'product', 'orderby' => $orderBY,'order' => $order, 'posts_per_page' => 100, 'post_status' => 'publish', 'meta_key' => $meta_key,               'meta_query' => array('relation' => 'AND', array('meta_key' => $meta_key)), 'tax_query' => array(array('taxonomy' => 'product_cat','field' => 'id','terms' => $child->term_id))); 
				
				query_posts($query_args);
				while ( have_posts() ) : the_post(); ?>

					<?php woocommerce_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php woocommerce_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action('woocommerce_after_main_content');
	?>
	
<?php if ( is_active_sidebar( 'woocommerce' ) ) : ?>	

  <div role="complementary" class="g1-sidebar widget-area" id="secondary">
	<div class="g1-inner">
	<?php
		/**
		 * woocommerce_sidebar hook
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		//do_action('woocommerce_sidebar');
		
		 
			dynamic_sidebar('woocommerce');

		
           
	?>
	</div>
	</div>
	
	
	
	<?php endif; ?>