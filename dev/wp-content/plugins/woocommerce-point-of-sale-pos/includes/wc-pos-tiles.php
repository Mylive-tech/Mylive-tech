<?php
/**
 * Logic related to displaying Tiles page.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale
 * @since    0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function wc_point_of_sale_add_options_tiles() {
  $option = 'per_page';
  $args = array(
    'label' => __( 'Tiles', 'wc_point_of_sale' ),
    'default' => 10,
    'option' => 'tiles_per_page'
  );
  add_screen_option( $option, $args );

  WC_POS()->tiles_table();

}
add_filter('set-screen-option', 'wc_point_of_sale_set_tiles_options', 10, 3);
function wc_point_of_sale_set_tiles_options($status, $option, $value) {
    if ( 'tiles_per_page' == $option ) return $value;
    return $status;
}
add_action( 'admin_init', 'wc_point_of_sale_actions_tiles' );

function wc_point_of_sale_actions_tiles() {
    if(isset($_GET['page']) && $_GET['page'] != WC_POS()->id_tiles) return;

    if( isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']) && !empty($_GET['id']) ) {
        WC_POS()->tile()->delete_tiles($_GET['id']);
    }
    else if(  isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']) ) {
        WC_POS()->tile()->delete_tiles($_POST['id']);
    }
    else if ( isset($_POST['action2']) &&  $_POST['action2'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']) )  {
        WC_POS()->tile()->delete_tiles($_POST['id']);
    }
    else if(isset($_POST['action']) && $_POST['action'] == 'wc_pos_edit_update_tiles' && isset($_POST['id']) && !empty($_POST['id']) ){
        WC_POS()->tile()->update_tile();
    }
}
  /**
   * Init the tiles page
   */
function wc_point_of_sale_render_tiles() {

      if(isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == 'edit' && $_GET['id'] != '')
        WC_POS()->tile()->display_edit_form($_GET['id']);
      else
        WC_POS()->tile()->output();


}
 /**
   * wc_point_of_sale_tile_record : this function are used fetch data single record table wc_poin_of_sale_grids
   * @param : int id pass gird id
   * return :array single record
   */
function wc_point_of_sale_tile_record($grid_id = null){

  global $wpdb;

  $grid_record_set = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wc_poin_of_sale_grids WHERE ID = " .$grid_id );
  return $grid_record_set;
}
function wc_point_of_sale_get_all_grids($grid_id = null){
	global $wpdb;
	$grid_record_set = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wc_poin_of_sale_grids WHERE ID != " .$grid_id );
	return $grid_record_set;
}

 /**
   * wc_point_of_sale_tiles_product_exists : checking data record exits
   * @param : int grid_id , int product_id
   * return :array single record
   */
function wc_point_of_sale_tiles_product_exists( $grid_id = null , $product_id = null, $default_selection = null, $tile_id = 0){
	global $wpdb;
  $filter = '';
  if($default_selection){
    $filter = "AND default_selection = $default_selection";
  }
  if(!$tile_id)
    $grid_record_set = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wc_poin_of_sale_tiles WHERE grid_id = " .$grid_id." $filter AND product_id =".$product_id );
  else
    $grid_record_set = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wc_poin_of_sale_tiles WHERE grid_id = " .$grid_id." $filter AND product_id =".$product_id." AND ID <> ".$tile_id );
	return $grid_record_set;

}

function the_grid_layout_cycle($grid, $ret = false){
  
  $is_all = false;
  if($grid == 'all'){
    $is_all = true;
    $grid = (object)array(
      'ID' => 1,
      'name' => __('All products', 'wc_point_of_sale' ),
      );
    global $wpdb;
    $default_order = get_option('wc_pos_default_tile_orderby');
    if(empty($default_order) || !$default_order){
      $default_order = 'menu_order';
    }
    $sql = '';
    switch ($default_order) {
      case 'popularity':
        $sql = "SELECT product.ID as ID, meta.meta_value+0 as popularity FROM {$wpdb->posts} product
        LEFT JOIN {$wpdb->postmeta} meta ON ( product.ID = meta.post_id AND meta.meta_key = 'total_sales' )
        WHERE post_type = 'product' AND post_status = 'publish' 
        ORDER BY meta.meta_value+0 DESC, product.post_date DESC";
        break; 
      case 'price':
        $sql = "SELECT product.ID as ID FROM {$wpdb->posts} product
        LEFT JOIN {$wpdb->postmeta} meta ON ( product.ID = meta.post_id AND meta.meta_key = '_price' )
        WHERE post_type = 'product' AND post_status = 'publish' 
        ORDER BY meta.meta_value+0 ASC, product.ID ASC";
        break;
      case 'price-desc':
        $sql = "SELECT product.ID as ID FROM {$wpdb->posts} product
        LEFT JOIN {$wpdb->postmeta} meta ON ( product.ID = meta.post_id AND meta.meta_key = '_price' )
        WHERE post_type = 'product' AND post_status = 'publish' 
        ORDER BY meta.meta_value+0 DESC, product.ID DESC";
        break;
      case 'rating':
        $sql = "SELECT {$wpdb->posts}.ID as ID, AVG( $wpdb->commentmeta.meta_value ) as average_rating  FROM {$wpdb->posts}
        LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
        LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
        WHERE post_type = 'product' AND post_status = 'publish' 
        GROUP BY $wpdb->posts.ID
        ORDER BY average_rating DESC, $wpdb->posts.post_date DESC";        
        break;      
      case 'date':
        $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish' ORDER BY post_date DESC ";
        break;      
      default:
        $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish' ORDER BY {$default_order}, post_title ASC ";
        break;
    }
    $tiles = $wpdb->get_results($sql);    
  }else{
    $tiles = wc_point_of_sale_get_tiles($grid->ID);
  }  
  if($ret === true)
    return $tiles;

  if ( $tiles ) :
      $grid_name = $grid->name;
      pos_grid_layout_cycle($tiles, $grid_name, $is_all);
  endif;
}

function pos_grid_layout_cycle($tiles, $grid_name, $is_all = false)
{
	$i = 0;
  $t = 0;
  $js = array();
  if( count($tiles) > 100){
    $tiles = array_slice($tiles,0, 100);
    //var_dump(count($tiles)); die;
  }
  $hide_text = get_option('wc_pos_hide_text_on_tiles');
  $hide_text = $hide_text == 'yes' ? true : false;
  foreach ($tiles as $tile) : 
    if($is_all === true){
      $tile->product_id = $tile->ID;
      $tile->default_selection = '';
      $tile->style = 'image';
      $tile->color = '';
      $tile->background = '';
    }
      $product              = get_product( $tile->product_id );
      $available_variations = array();
      if( $product && $product->is_type( 'variable' )){
          $available_variations = $product->get_available_variations();
      }
      if($tile->default_selection ){

          $product_variation = get_product( $tile->default_selection );

          $variation_data = array();

          if( !empty( $available_variations ) && $product_variation){
              $product_id = $tile->default_selection;
          }else{
              continue;
          }
      }else{
          $product_id = $tile->product_id;
      }

      $i++;
      $t++;
      if($t == 1) {
        if($is_all === true )
          echo '<div><table data-title="' . ucfirst($grid_name) . '"><tbody>';
        else
          echo '<div><table data-title="' . ucfirst($grid_name) . ' ' . __( 'Layout', 'wc_point_of_sale') . '"><tbody>';
      }
      if($i == 1) echo '<tr>';
      if($tile->style == 'image'){
          $image = '';
          $size = 'shop_thumbnail';
          if ( has_post_thumbnail( $product_id ) ) {
            $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($product_id), $size );
            $image = $thumbnail[0];
          } elseif ( ( $parent_id = wp_get_post_parent_id( $product_id ) ) && has_post_thumbnail( $parent_id ) ) {
            $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($parent_id), $size );
            $image = $thumbnail[0];
          } else {
            $image = wc_placeholder_img_src();
          }
          if(!$image || $image == NULL) $image = wc_placeholder_img_src();
            
          if( !empty($_SERVER['HTTPS']) && !empty($image) && strpos($image, 'https://') === false ){ 
            $image = str_replace('http://', 'https://', $image); 
          }
          $tr_style = "background: url('" . $image . "') 50% 20% no-repeat; background-size: auto 55px; background-color: #fff; vertical-align: bottom;";
          if($hide_text){
            $tr_style = "background: url('" . $image . "') center center no-repeat; background-size: auto 70px; background-color: #fff; vertical-align: bottom;";
          }
          ?>
          <td id="title_<?php echo $tile->ID ?>" style="<?php echo $tr_style; ?>" class="title_product add_grid_tile">
            <a style="color: #222222; margin-bottom: 5px; display: block; font-weight: normal; font-size: 12px;" data-id="<?php echo $tile->product_id; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>"><?php echo !$hide_text ? get_the_title($tile->product_id) : '' ;?></a>
          <?php
        }else{ ?>
          <td id="title_<?php echo $tile->ID ?>" style="background: #<?php echo $tile->background;?>; " class="title_product add_grid_tile">
           <a style="color: #<?php echo $tile->colour;?>;" data-id="<?php echo $tile->product_id; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
              <?php echo get_the_title($tile->product_id) ;?>
          </a>
          <?php 
        } 
        $id = $tile->product_id;

        if( $product && $product->is_type( 'variable' ) ){ 

          $attributes = $product->get_variation_attributes();

          if($tile->default_selection){
            $variation_data = array();
            if ( $available_variations ){
              foreach ($available_variations as $attribute) {
                if ((int)$attribute['variation_id'] == (int)$tile->default_selection && isset($attribute['attributes'])) {
                  $variation_data= $attribute['attributes'];
                }
              }
            }
          
          }
          
          ?>
          <div class="hidden">
          <?php foreach ( $attributes as $attribute_name => $options ) : ?>
            <?php
            if ( isset( $variation_data[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) {
              $selected = $variation_data[ 'attribute_' . sanitize_title( $attribute_name ) ];
            } else {
              $selected = '';
            }
            ?>
            <div data-taxonomy="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>" data-label="<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>" data-slug="<?php echo ucwords( str_replace( 'pa_', '', $attribute_name ) ); ?>">
            <?php
            wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
            ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php                          
        }
        ?>
      </td>
    <?php
      if($i == 5) {
          echo '</tr>';
          $i = 0;

          if($t == 25) {
              $t = 0;
              echo '</tbody></table></div>';
          }
      };

  endforeach;
  if($i != 0){
      $j = $i+1;
      for ($j; $j<=5; $j++) :
          ?>
          <td></td>
          <?php
          if($j == 5) echo '</tr>';
      endfor;
      echo '</tbody></table></div>';
  }else{
      if($t != 0) {
                $t = 0;
                echo '</tbody></table></div>';
            }
    }
}
function the_grid_category_layout_cycle(){
  $terms = get_terms( 'product_cat');
  $arc_display = get_option( 'woocommerce_category_archive_display');
  $i     = 0;
  $js    = array();
  $size  = 'shop_thumbnail';
  echo '<div><ul>';
      if ( $terms ) :
      	$relationships = pos_term_relationships();
          foreach ($terms as $term) : 
              $i++;

                $thumbnail_id = absint( get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true ) );
                if ( $thumbnail_id ) {
                  $thumbnail = wp_get_attachment_image_src( $thumbnail_id, $size );
                  $image     = $thumbnail[0];
                } else {
                  $image = wc_placeholder_img_src();
                }
                if(!$image || $image == NULL) $image = wc_placeholder_img_src();
                
                ?>
                <li  data-catid="<?php echo $term->term_id; ?>" id="category_<?php echo $term->term_id ?>" style="background-image: url('<?php echo $image; ?>'); " class="title_category open_category category_cycle" data-title="<?php echo $term->name; ?>">
                  <a>
                  <?php echo $term->name ;?>
                  </a>
                <?php
                    ?>
                </li>
              <?php
          endforeach;
      endif;
      $products = the_grid_layout_cycle('all', true);
      if($products){
        foreach ($products as $value) {
          pos_product_grid_cycle($value->ID);
        }
      }
      echo '</ul></div>';
}
function pos_product_grid_cycle($product_id)
{
  $hide_text = get_option('wc_pos_hide_text_on_tiles');
  $hide_text = $hide_text == 'yes' ? true : false;

  $image = '';
  $size = 'shop_thumbnail';

  $product              = get_product( $product_id );
  $available_variations = array();
  $attributes = array();
  if( $product && $product->is_type( 'variable' )){
      $available_variations = $product->get_available_variations();
      $attributes = $product->get_variation_attributes();
  }

  if ( has_post_thumbnail( $product_id ) ) {
    $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($product_id), $size );
    $image = $thumbnail[0];
  } elseif ( ( $parent_id = wp_get_post_parent_id( $product_id ) ) && has_post_thumbnail( $parent_id ) ) {
    $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($parent_id), $size );
    $image = $thumbnail[0];
  } else {
    $image = wc_placeholder_img_src();
  }
  if(!$image || $image == NULL) $image = wc_placeholder_img_src();

   $tr_style = "background: url('" . $image . "') 50% 20% no-repeat; background-size: auto 55px;";
    if($hide_text){
      $tr_style = "background: url('" . $image . "') center center no-repeat; background-size: auto 70px;";
    }
  
  ?>
  <li id="product_<?php echo $product_id ?>" style="<?php echo $tr_style; ?>" class="title_product add_grid_tile category_cycle">
    <a style="color: #222222; margin-bottom: 5px; display: block; font-weight: normal; font-size: 12px;" data-id="<?php echo $product_id; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>"><?php echo !$hide_text ? get_the_title($product_id) : '' ;?></a>
    <?php if($attributes) : ?>
      <div class="hidden">
      <?php 
      foreach ( $attributes as $attribute_name => $options ) : ?>
        <?php
        if ( isset( $variation_data[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) {
          $selected = $variation_data[ 'attribute_' . sanitize_title( $attribute_name ) ];
        } else {
          $selected = '';
        }
        ?>
        <div data-taxonomy="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>" data-label="<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>" data-slug="<?php echo ucwords( str_replace( 'pa_', '', $attribute_name ) ); ?>">
        <?php
        wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
        ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php
    endif;
    ?>
  </li>
  <?php
}

function pos_get_all_products()
{
  global $wpdb;
  $query    = "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish' ORDER BY post_title ASC";
  $products = $wpdb->get_results($query);
  return $products;
}

function get_last_position_of_tile($grid_id = 0){
  global $wpdb;
  $table_name = $wpdb->prefix . 'wc_poin_of_sale_tiles';
  return $wpdb->get_row("SELECT MAX(order_position) AS `max` FROM $table_name WHERE grid_id=$grid_id");
}