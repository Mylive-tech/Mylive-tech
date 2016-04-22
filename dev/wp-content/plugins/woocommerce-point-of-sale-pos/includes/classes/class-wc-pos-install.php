<?php
/**
 * Installation related functions and actions.
 *
 * @category Admin
 * @package  WoocommercePointOfSale/Classes
 * @version  2.4.15
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * WoocommercePointOfSale_Install Class
 */
class WoocommercePointOfSale_Install {

  /**
   * Hook in tabs.
   */
  public static function init() {

    add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
    if (function_exists('is_multisite') && is_multisite()) {
        add_action( 'wpmu_new_blog', array( __CLASS__, 'new_blog'), 10, 6); 
    }

    if(class_exists('SitePress')) {
        $settings = get_option( 'icl_sitepress_settings' );
        if($settings['urls']['directory_for_default_language'] == 1){
            add_action( 'generate_rewrite_rules', array( __CLASS__, 'create_rewrite_rules_wpml'), 9);
        }else{
            add_filter('rewrite_rules_array', array( __CLASS__, 'create_rewrite_rules'), 11, 1);
        }
    }else{
        add_filter('rewrite_rules_array', array( __CLASS__, 'create_rewrite_rules'), 11, 1);
    }    
    add_action( 'init', array( __CLASS__, 'on_rewrite_rule') );
    add_filter('query_vars',array( __CLASS__, 'add_query_vars'));
    add_action( 'parse_request', array(  __CLASS__, 'parse_request' ) );
    add_filter('admin_init', array( __CLASS__, 'flush_rewrite_rules'));
    add_filter( 'plugin_action_links_' . plugin_basename( WC_POS_PLUGIN_FILE ), array( __CLASS__, 'plugin_action_links' ) );
    add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );

    add_filter( 'woocommerce_prevent_admin_access', array( __CLASS__, 'prevent_admin_access' ), 10, 2 );

  }
  public static function check_version(){
      $installed_ver = get_option("wc_pos_db_version");
      if ($installed_ver != WC_POS_VERSION) {
          self::install();   
      }
  }

  public static function new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    global $wpdb;
    $pos_path = basename( dirname(  WC_POS_PLUGIN_FILE  ) );
    if (is_plugin_active_for_network($pos_path.'/woocommerce-point-of-sale.php')) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        self::install();
        switch_to_blog($old_blog);
    }
  }
  public static function install()
  {
    self::create_tables();
    self::create_product();
    self::create_roles();
  }

  private static function create_tables() {
        global $wpdb;
        
        $wpdb->hide_errors();
        #$wpdb->show_errors();
        $installed_ver = get_option("wc_pos_db_version");

        if ($installed_ver != WC_POS_VERSION) {

            $collate = '';
            if ($wpdb->has_cap('collation')) {
                if (!empty($wpdb->charset))
                    $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
                if (!empty($wpdb->collate))
                    $collate .= " COLLATE $wpdb->collate";
            }

            // initial install
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $table_name = $wpdb->prefix . "wc_poin_of_sale_outlets";
            $sql = "CREATE TABLE $table_name (
            ID        bigint(20) NOT NULL AUTO_INCREMENT,
            name      text NOT NULL,
            contact   text DEFAULT '' NOT NULL,
            social    text DEFAULT '' NOT NULL,
            PRIMARY KEY  (ID)
    )" . $collate;
            dbDelta($sql);

            $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
            $sql = "CREATE TABLE $table_name (
            ID        bigint(20) NOT NULL AUTO_INCREMENT,
            name      varchar(255) NOT NULL,
            slug      varchar(255) NOT NULL,
            detail    text DEFAULT '' NOT NULL,
            outlet    int(20) DEFAULT 0 NOT NULL,
            default_customer int(20) DEFAULT 0 NOT NULL,
            order_id  int(20) DEFAULT 0 NOT NULL,
            settings   text DEFAULT '' NOT NULL,
            _edit_last    int(20) DEFAULT 0 NOT NULL,
            opened timestamp NOT NULL DEFAULT current_timestamp,
            closed timestamp NOT NULL,
            PRIMARY KEY  (ID)
    )" . $collate;
            dbDelta($sql);


            $table_name = $wpdb->prefix . "wc_poin_of_sale_tiles";
            $sql = "CREATE TABLE $table_name (
            ID          bigint(20) NOT NULL AUTO_INCREMENT,
            grid_id     bigint(20) NOT NULL,
            product_id  bigint(20) NOT NULL,
            style       varchar(100) DEFAULT 'image' NOT NULL,
            colour      varchar(6) DEFAULT '000000' NOT NULL,
            background  varchar(6) DEFAULT 'ffffff' NOT NULL,
            default_selection  bigint(20) NOT NULL,
            order_position     bigint(20) NOT NULL,
            PRIMARY KEY  (ID)
    )" . $collate;
            dbDelta($sql);

            $table_name = $wpdb->prefix . "wc_poin_of_sale_grids";
            $sql = "CREATE TABLE $table_name (
            ID        bigint(20) NOT NULL AUTO_INCREMENT,
            name      varchar(255) NOT NULL,
            label     varchar(255) NOT NULL,
            sort_order     varchar(255) DEFAULT 'name' NOT NULL,
            PRIMARY KEY  (ID)
    )" . $collate;
            dbDelta($sql);



            $table_name = $wpdb->prefix . "wc_poin_of_sale_receipts";
            $sql = "CREATE TABLE $table_name (
            ID          bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) DEFAULT '' NOT NULL,
            print_outlet_address varchar(255) DEFAULT '' NOT NULL,
            print_outlet_contact_details varchar(255) DEFAULT '' NOT NULL,
            telephone_label text DEFAULT '' NOT NULL,
            fax_label text DEFAULT '' NOT NULL,
            email_label text DEFAULT '' NOT NULL,
            website_label text DEFAULT '' NOT NULL,
            receipt_title text DEFAULT '' NOT NULL,
            order_number_label text DEFAULT '' NOT NULL,
            order_date_label text DEFAULT '' NOT NULL,
            order_date_format text DEFAULT '' NOT NULL,
            print_order_time varchar(255) DEFAULT '' NOT NULL,
            print_server varchar(255) DEFAULT '' NOT NULL,
            served_by_label text DEFAULT '' NOT NULL,
            tax_label text DEFAULT '' NOT NULL,
            total_label text DEFAULT '' NOT NULL,
            payment_label text DEFAULT '' NOT NULL,
            print_number_items text DEFAULT '' NOT NULL,
            items_label text DEFAULT '' NOT NULL,
            print_barcode varchar(255) DEFAULT '' NOT NULL,
            print_tax_number varchar(255) DEFAULT '' NOT NULL,
            tax_number_label text DEFAULT '' NOT NULL,
            header_text text DEFAULT '' NOT NULL,
            footer_text text DEFAULT '' NOT NULL,
            logo text DEFAULT '' NOT NULL,
            text_size enum( 'nomal', 'small', 'large' ) DEFAULT 'nomal',
            title_position enum( 'left', 'center', 'right' ) DEFAULT 'left',
            logo_size enum( 'nomal', 'small', 'large' ) DEFAULT 'nomal',
            logo_position enum( 'left', 'center', 'right' ) DEFAULT 'left',
            contact_position enum( 'left', 'center', 'right' ) DEFAULT 'left',
            tax_number_position enum( 'left', 'center', 'right' ) DEFAULT 'left',
            PRIMARY KEY  (ID)
    )" . $collate;
            dbDelta($sql);



            if (get_option("wc_pos_db_version")) {
                update_option("wc_pos_db_version", WC_POS_VERSION);
            } else {
                add_option("wc_pos_db_version", WC_POS_VERSION);
            }
        }
  }
  /**
   * Create roles and capabilities
   */
  public static function create_roles() {
    
    global $wp_roles;

    if ( ! class_exists( 'WP_Roles' ) ) {
      return;
    }

    if ( ! isset( $wp_roles ) ) {
      $wp_roles = new WP_Roles();
    }

    // Cashier role
    add_role( 'cashier', __( 'Cashier', 'wc_point_of_sale' ), array(
      'read'            => true,
      'edit_posts'      => false,
      'delete_posts'    => false,
      'list_users'      => false
    ) );

    // POS manager role
    add_role( 'pos_manager', __( 'POS Manager', 'wc_point_of_sale' ), array(
      'read'                   => true,
      'edit_users'             => true,
      'edit_posts'             => true,
      'delete_posts'           => true,
      'unfiltered_html'        => true,
      'upload_files'           => true,
      'list_users'             => true
    ) );

    $capabilities = self::get_core_capabilities();

    foreach ( $capabilities as $cap_group ) {
      foreach ( $cap_group as $cap ) {
        $wp_roles->add_cap( 'pos_manager', $cap );
        $wp_roles->add_cap( 'administrator', $cap );
      }
    }
    foreach ( $capabilities['cashier'] as $cap ) {
      $wp_roles->add_cap( 'cashier', $cap );
    }

    $shop_manager = $wp_roles->get_role('shop_manager');

      
    foreach ( $shop_manager->capabilities as $cap => $status ) {
      if($status == true )
        $wp_roles->add_cap( 'pos_manager', $cap );
    }
  }

  /**
   * Get capabilities for POS - these are assigned to admin/POS Manager/Cashier during installation or reset
   *
   * @return array
   */
   private static function get_core_capabilities() {
    $capabilities = array();

    $capabilities['cashier'] = array(
      'view_register',
      'read_private_products',
      'read_private_shop_coupons'
    );
    $capabilities['manager'] = array(
      'manage_wc_point_of_sale',
      'view_woocommerce_reports',
    );
    return $capabilities;
  }

  public static function create_product() {
      global $wpdb;

      $old_product = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_title = 'POS custom product' ");
      if($old_product)
          $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'product' AND post_title = 'POS custom product' ");

      $option_name = 'wc_pos_custom_product_id';
      $need_create = false;

      if ( $pr_id = (int)get_option($option_name) ) {
          $result = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = 'pos_custom_product' AND post_status = 'publish' AND ID={$pr_id}");
          if(!$result)
              $need_create = true;
      } else {
          $need_create = true;
      }
      if($need_create){
          $new_product = array(
              'post_title'   => 'POS custom product',
              'post_status'  => 'publish',
              'post_type'    => 'pos_custom_product',
              'post_excerpt' => '',
              'post_content' => '',
              'post_author'  => get_current_user_id(),
          );

          // Attempts to create the new product
          $id = (int)wp_insert_post( $new_product, true );

          $regular_price = wc_format_decimal( 10);
          update_post_meta( $id, '_regular_price', $regular_price );
          update_post_meta( $id, '_price', $regular_price );
          update_post_meta( $id, '_visibility', 'hidden' );

          $product_type = wc_clean( 'simple' );
          wp_set_object_terms( $id, $product_type, 'product_type' );

          update_option($option_name, $id);
      }
  }

  public static function activate($networkwide) {
    $GLOBALS['wp_rewrite'] = new WP_Rewrite();
    global $wp_rewrite, $wpdb;
    self::flush_rewrite_rules();   
    if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function for each blog id
        if ($networkwide) {
            $old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                self::install();
            }
            switch_to_blog($old_blog);
            return;
        }   
    }          
    self::install();
  }

  /**
   * Show action links on the plugin screen.
   *
   * @param mixed $links Plugin Action links
   * @return  array
   */
  public static function plugin_action_links( $links ) {
    $action_links = array(
      'settings' => '<a href="' . admin_url( 'admin.php?page=wc_pos_settings' ) . '" title="' . esc_attr( __( 'View POS Settings', 'wc_point_of_sale' ) ) . '">' . __( 'Settings', 'wc_point_of_sale' ) . '</a>',
    );

    return array_merge( $action_links, $links );
  }

  /**
   * Show row meta on the plugin screen.
   *
   * @param mixed $links Plugin Row Meta
   * @param mixed $file  Plugin Base file
   * @return  array
   */
  public static function plugin_row_meta( $links, $file ) {
    if ( $file == plugin_basename( WC_POS_PLUGIN_FILE ) ) {
      $row_meta = array(
        'docs'    => '<a href="' . esc_url( apply_filters( 'wc_pos_docs_url', 'http://actualityextensions.com/documentation/woocommerce-point-of-sale/' ) ) . '" title="' . esc_attr( __( 'View POS Documentation', 'wc_point_of_sale' ) ) . '">' . __( 'Docs', 'wc_point_of_sale' ) . '</a>',
        'support' => '<a href="' . esc_url( apply_filters( 'wc_crm_docs_url', 'https://actualityextensions.zendesk.com/hc/en-us/' ) ) . '" title="' . esc_attr( __( 'Visit Support Help Desk', 'wc_point_of_sale' ) ) . '">' . __( 'Support', 'wc_point_of_sale' ) . '</a>',
      );

      return array_merge( $links, $row_meta );
    }

    return (array) $links;
  }


  public static function flush_rewrite_rules() {
      global $wp_rewrite;
      $wp_rewrite->flush_rules();
  }

  public static function create_rewrite_rules($rules) {
      global $wp_rewrite;
      $newRule = array('^point-of-sale/([^/]+)/([^/]+)/?$' => 'index.php?page=wc_pos_registers&action=view&outlet=$matches[1]&reg=$matches[2]');
      $newRules = $newRule + $rules;
      return $newRules;
  }
  public static function create_rewrite_rules_wpml() {
      global $wp_rewrite;
      $newRule = array('point-of-sale/([^/]+)/([^/]+)/?$' => 'index.php?page=wc_pos_registers&action=view&outlet=$matches[1]&reg=$matches[2]');

      $wp_rewrite->rules = $newRule + $wp_rewrite->rules;
  }
  public static function on_rewrite_rule(){        
      add_rewrite_rule('^point-of-sale/([^/]+)/([^/]+)/?$','index.php?page=wc_pos_registers&action=view&outlet=$matches[1]&reg=$matches[2]','top');
  }
  public static function add_query_vars( $public_query_vars ) {
      $public_query_vars[] = 'page';
      $public_query_vars[] = 'action';
      $public_query_vars[] = 'outlet';
      $public_query_vars[] = 'reg';
      return $public_query_vars;
  }
  public static function parse_request( $wp ) {
    if( isset( $wp->query_vars['page'] ) && $wp->query_vars['page'] == 'wc_pos_registers' && isset( $wp->query_vars['action'] ) && $wp->query_vars['action'] == 'view' ) {
        WC_POS()->is_pos = true;
    }
  }

  public static function prevent_admin_access($prevent_access)
  {
    if ( current_user_can( 'view_register' ) ) {
      $prevent_access = false;
    }
    return $prevent_access;
  }

  /**
   * remove_roles function.
   */
  public static function remove_roles() {
    global $wp_roles;

    if ( ! class_exists( 'WP_Roles' ) ) {
      return;
    }

    if ( ! isset( $wp_roles ) ) {
      $wp_roles = new WP_Roles();
    }

    $capabilities = array(
      'view_register',
      'manage_wc_point_of_sale'
    );
    foreach ( $capabilities as $cap ) {
      $wp_roles->remove_cap( 'cashier', $cap );
      $wp_roles->remove_cap( 'pos_manager', $cap );
      $wp_roles->remove_cap( 'shop_manager', $cap );
      $wp_roles->remove_cap( 'administrator', $cap );
    }

    remove_role( 'pos_manager' );
    remove_role( 'cashier' );
  }

}