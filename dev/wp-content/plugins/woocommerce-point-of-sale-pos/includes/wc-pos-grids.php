<?php
/**
 * Logic related to displaying Grids page.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale
 * @since    0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function wc_point_of_sale_add_options_grids() {

  $option = 'per_page';
  $args = array(
    'label' => __( 'Layouts', 'wc_point_of_sale' ),
    'default' => 10,
    'option' => 'grids_per_page'
  );
  add_screen_option( $option, $args );

  WC_POS()->grids_table();

}

add_filter('set-screen-option', 'wc_point_of_sale_set_grids_options', 10, 3);
function wc_point_of_sale_set_grids_options($status, $option, $value) {
    if ( 'grids_per_page' == $option ) return $value;
    return $status;
}

add_action( 'admin_init', 'wc_point_of_sale_actions_grids' );
function wc_point_of_sale_actions_grids() {
    if(isset($_GET['page']) && $_GET['page'] != WC_POS()->id_grids) return;

    if(  isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']) ) {
        WC_POS()->grid()->delete_grids();
    }
    else if ( isset($_POST['action2']) &&  $_POST['action2'] == 'delete' && isset($_POST['id']) && !empty($_POST['id']) )  {
        WC_POS()->grid()->delete_grids();
    }
}
  /**
   * Init the grids page
   */
  function wc_point_of_sale_render_grids() {
      WC_POS()->grid()->output();
  }
  /**
  * Get grid taxonomies.
  *
  * @return object
  */
  function wc_point_of_sale_get_grids() {
  global $wpdb;
  $grid_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wc_poin_of_sale_grids ORDER BY name ASC" );
  return apply_filters( 'wc_poin_of_sale_grids', $grid_taxonomies );
  }

  /**
  * Return the bool(ture) if product is already in grid.
  *
  * @return bool
  */
  function product_in_grid($product_id, $grid_id) {
    global $wpdb;
    $sql = "SELECT * FROM " . $wpdb->prefix . "wc_poin_of_sale_tiles WHERE product_id = $product_id AND grid_id = $grid_id";
              
    $grids = $wpdb->get_results( $sql );

    if(!empty($grids)) return true;
    else return false;
  }

  /**
  * Get grid ids for product.
  *
  * @return array
  */
  function wc_point_of_sale_get_grids_for_product($product_id) {
  global $wpdb;
  $grid_taxonomies = $wpdb->get_results( "SELECT grid_id FROM " . $wpdb->prefix . "wc_poin_of_sale_tiles WHERE product_id= $product_id GROUP BY grid_id" );
  $grid = array();
  if(!empty($grid_taxonomies)){
    foreach ($grid_taxonomies as $key => $value) {
      $grid[] = (int)$value->grid_id;
    }
  }
  return $grid;
  }
  /**
  * Get grid name for product.
  *
  * @return array
  */
  function wc_point_of_sale_get_grids_names_for_product($product_id) {
  	global $wpdb;
    $sql = "SELECT grids.ID, grids.name FROM {$wpdb->prefix}wc_poin_of_sale_grids as grids 
              LEFT JOIN {$wpdb->prefix}wc_poin_of_sale_tiles as tiles ON(tiles.grid_id = grids.ID )
              WHERE tiles.product_id= {$product_id}";
              
  	$grid_taxonomies = $wpdb->get_results( $sql );
    $grid = array();
    if(!empty($grid_taxonomies)){
      foreach ($grid_taxonomies as $key => $value) {
        $grid[(int)$value->ID] = (string)$value->name;
      }
    }
	return $grid;
  }
  /**
   * Get a product attributes name.
   *
   * @param mixed $name
   * @return string
   */
  function wc_point_of_sale_grid_name( $name ) {
    return wc_sanitize_taxonomy_name( $name );
  }
  /**
 * Checks that the grid name exists.
 *
 *
 * @since 3.0.0
 *
 * @uses $wp_taxonomies
 *
 * @param string $name Name of grid object
 * @return bool Whether the grid exists.
 */
function wc_point_of_sale_grid_exists( $label='' ) {
  global $wpdb;
  $result = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wc_poin_of_sale_grids WHERE label ='$label'" );
  if($result)
    return true;
  else
    return false;
}


function wc_point_of_sale_get_tiles($id_grid=0) {

  global $wpdb;

  if($id_grid){
    $grid_t = $wpdb->prefix . "wc_poin_of_sale_grids";
    $sort   = $wpdb->get_var("SELECT sort_order FROM $grid_t WHERE ID = {$id_grid} LIMIT 1");
    $join = '';
    $orderby = '';
    switch ($sort) {
      case 'name':
          $join    .= " LEFT JOIN {$wpdb->posts} prod ON (prod.ID = tiles_t.product_id)";
          $orderby = ' ORDER BY prod.post_title ASC';
          break;
        default:
          $orderby = ' ORDER BY order_position ASC';
          break;
    }
    $tiles = $wpdb->get_results( "SELECT tiles_t.* FROM " . $wpdb->prefix . "wc_poin_of_sale_tiles as tiles_t
      {$join}
      WHERE grid_id = $id_grid {$orderby}" );
  }
  else{
    $tiles = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wc_poin_of_sale_tiles LEFT OUTER JOIN ".$wpdb->prefix."posts ON ".$wpdb->prefix."wc_poin_of_sale_tiles.product_id= ".$wpdb->prefix."posts.ID" );
  }

  return $tiles;
}
