<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @class 	  WooCommerce_POS_Pro_Activator
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Activator {

	/**
	 * @param $file
	 */
	public function __construct( $file ) {
		register_activation_hook( $file, array( $this, 'activate' ) );
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		add_action( 'plugins_loaded', array( $this, 'run_woocommerce_pos_pro' ), 20 );
	}

  /**
   * Checks for valid install and begins execution of the plugin.
   */
  public function run_woocommerce_pos_pro(){
    $this->version_check();
    if( $this->woocommerce_pos_check() ){
      require_once WC_POS_PRO_PLUGIN_PATH . 'includes/class-wc-pos-pro.php';
      new WC_POS_Pro();
    }
  }

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param $network_wide
	 */
	public function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = $this->get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					$this->single_activate();

					restore_current_blog();
				}

			} else {
				$this->single_activate();
			}

		} else {
			$this->single_activate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @param $blog_id
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		$this->single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 */
	private function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired when the plugin is activated.
	 */
	static public function single_activate() {

	}

  /**
   * Check version number, runs every time
   */
  public function version_check(){
    // next check the POS version number
    $old = get_option( 'woocommerce_pos_pro_db_version' );
    if( version_compare( $old, WC_POS_PRO_VERSION, '<' ) ) {
      $this->db_upgrade( $old, WC_POS_PRO_VERSION );
      update_option( 'woocommerce_pos_pro_db_version', WC_POS_PRO_VERSION );
    }
  }

  /**
   * Upgrade database
   * @param $old
   * @param $current
   */
  public function db_upgrade( $old, $current ) {
    $db_updates = array(
      '0.4' => 'updates/update-0.4.php'
    );
    foreach ( $db_updates as $version => $updater ) {
      if ( version_compare( $version, $old, '>' ) &&
        version_compare( $version, $current, '<=' ) ) {
        include( $updater );
      }
    }
  }

	/**
	 * WooCommerce POS Pro will not load if WooCommerce & WooCommerce POS is not present
	 * - note: WC_POS_Admin_Notices not available
	 */
	private function woocommerce_pos_check() {
		if( class_exists( 'WC_POS' ) )
      return true;

    add_action( 'admin_notices', array( $this, 'woocommerce_pos_alert' ) );
	}

	/**
	 * Admin message - WooCommerce not activated
	 */
	public function woocommerce_pos_alert(){
		echo '<div class="error">
			<p>'. sprintf( __('<strong>WooCommerce POS Pro</strong> requires <a href="%s">WooCommerce POS</a>. Please <a href="%s">install and activate WooCommerce POS</a>', 'woocommerce-pos-pro' ), 'http://wordpress.org/plugins/woocommerce-pos/', admin_url('plugins.php') ) . ' &raquo;</p>
		</div>';
	}

}