<?php
/**
 * Adds multi-store functionality
 *
 * @class 	  WC_POS_Pro_Stores
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Stores {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Custom post type
		$this->register_cpt();

		// Login form
    if( $this->is_pos_login() ){
      add_action( 'login_form', array( $this, 'login_form' ) );
      add_action( 'login_enqueue_scripts', array ( $this, 'login_enqueue_scripts' ) );
    }

    // grab store select
    add_filter( 'authenticate', array( $this, 'authenticate' ), 30, 3 );

    //
		add_action( 'woocommerce_pos_template_redirect', array( $this, 'pos_template' ) );

    // print template
    add_filter( 'woocommerce_pos_locate_template', array( $this, 'locate_template' ), 10, 2 );

    // params
    add_filter( 'woocommerce_pos_params', array( $this, 'params' ) );

  }

  /**
   * returns true for POS login
   * @return bool
   */
  private function is_pos_login(){
    return isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'wp-login.php' &&
      isset( $_GET['pos'] ) && $_GET['pos'] == 1;
  }

	/**
	 * Register Stores custom post type
	 */
	public function register_cpt() {

		// Stores
		$labels = array(
			'name'               => __( 'Stores', 'woocommerce-pos-pro' ),
			'singular_name'      => __( 'Store', 'woocommerce-pos-pro' ),
			'menu_name'          => __( 'Stores', 'woocommerce-pos-pro' ),
			'add_new'            => __( 'Add New Store', 'woocommerce-pos-pro' ),
			'add_new_item'       => __( 'Add New Store', 'woocommerce-pos-pro' ),
			'new_item'           => __( 'New Store', 'woocommerce-pos-pro' ),
			'edit_item'          => __( 'Edit Store', 'woocommerce-pos-pro' ),
			'view_item'          => __( 'View Store', 'woocommerce-pos-pro' ),
			'all_items'          => __( 'Stores', 'woocommerce-pos-pro' ),
			'search_items'       => __( 'Search Stores', 'woocommerce-pos-pro' ),
			'parent_item_colon'  => __( 'Parent Store:', 'woocommerce-pos-pro' ),
			'not_found'          => __( 'No Stores found.', 'woocommerce-pos-pro' ),
			'not_found_in_trash' => __( 'No Stores found in Trash.', 'woocommerce-pos-pro' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'page',
			'has_archive'        => true,
			'hierarchical'       => true,
			'supports'           => array(
				'title',
				// 'editor',
				'page-attributes',
				'thumbnail'
			)
		);
		register_post_type( 'stores', $args );
	}

	/**
	 * Store select
	 */
	public function login_form() {
		$stores = get_pages( array( 'post_type' => 'stores' ) );

    if( count($stores) == 1 && isset( $stores[0]->ID ) ) :
      echo '<input type="hidden" name="store" value="'. $stores[0]->ID .'">';

    elseif( count($stores) > 1 ) :
			echo '<p><label for="store">'. __( 'Store', 'woocommerce-pos-pro' ) .'<br>'.
				wp_dropdown_pages( array(
				  'post_type' => 'stores',
				  'name' => 'store',
				  'id' => 'store',
				  'sort_column' => 'menu_order',
				  'echo' => 0
				))
				.'</label></p>';
		endif;

    // pos_login flag
    echo '<input type="hidden" name="pos_login" value="1">';
	}

	/**
	 * Login form css
	 */
	public function login_enqueue_scripts() {
		$style = '<style type="text/css">#store {width:100%;} .login form select {margin:2px 6px 16px 0;}</style>';
		echo $style;
	}

	/**
	 * Update the store id for logged in user
	 *
	 * @param $user
	 * @param $username
	 * @param $password
	 * @return mixed
	 */
	public function authenticate( $user, $username, $password ) {
		if( is_wp_error($user) || !isset($user->ID) )
			return $user;

    if( isset( $_POST['pos_login'] ) && isset( $_POST['store'] ) ) {
      update_user_option( $user->ID, 'woocommerce_pos_store', $_POST['store'] );
    }

		return $user;
	}

	/**
	 *
	 */
	public function pos_template() {
		if( isset( $_GET['store'] ) ) {
			update_user_option( get_current_user_id(), 'woocommerce_pos_store', $_GET['store'] );
		}
	}

  /**
   * Locate the Pro print template
   *
   * @param $template
   * @param $path
   * @return string
   */
  public function locate_template($template, $path){
    $default = WC_POS_PLUGIN_PATH. 'includes/views/' . $path;
    if( $path == 'print/receipt.php' && $template == $default){
      $template = WC_POS_PRO_PLUGIN_PATH. 'includes/views/print/receipt.php';
    }
    return $template;
  }

  /**
   * Store info for use by receipt template
   *
   * @param $params
   * @return mixed
   */
  public function params($params){

    $store_id = get_user_option( 'woocommerce_pos_store' );
    if( !$store_id ){
      return $params;
    }

    $params['store'] = array(
      'name'        => esc_html( get_the_title( $store_id ) ),
      'address'     => array(
        'address_1' => esc_html( get_post_meta($store_id, '_address_1', true) ),
        'address_2' => esc_html( get_post_meta($store_id, '_address_2', true) ),
        'city'      => esc_html( get_post_meta($store_id, '_city', true) ),
        'state'     => esc_html( get_post_meta($store_id, '_state', true) ),
        'postcode'  => esc_html( get_post_meta($store_id, '_postcode', true) ),
        'country'   => esc_html( get_post_meta($store_id, '_country', true) )
      ),
      'url'         => esc_html( get_post_meta($store_id, '_url', true) ),
      'phone'       => esc_html( get_post_meta($store_id, '_phone', true) ),
      'email'       => esc_html( get_post_meta($store_id, '_email', true) ),
      'notes'       => get_post_meta($store_id, '_personal_notes', true),
      'policies'    => get_post_meta($store_id, '_policies_conditions', true),
      'footer'      => get_post_meta($store_id, '_footer_imprint', true)
    );

    if( has_post_thumbnail( $store_id ) ){
      $logo = wp_get_attachment_image_src( get_post_meta($store_id, '_thumbnail_id', true), 'large' );
      $params['store']['logo'] = $logo[0];
    }

    $hours = get_post_meta($store_id, '_opening_hours', true);
    if($hours && is_array($hours)){
      $params['store']['hours'] = array_intersect_key($hours, array_fill(0, 6, ''));
      $params['store']['hours_note'] = get_post_meta($store_id, '_opening_hours_notes', true);
    }

    return $params;
  }

} 