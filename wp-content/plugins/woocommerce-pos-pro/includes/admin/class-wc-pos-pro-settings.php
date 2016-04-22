<?php
/**
 * POS Pro Settings
 *
 * @class 	  WooCommerce_POS_Pro_Settings
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Admin_Settings {

	/**
	 *
	 */
	public function __construct() {
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

    // add pro capabilities
    add_filter( 'woocommerce_pos_capabilities', array( $this, 'capabilities' ) );

    $this->init();
  }

  /**
   * init subclasses
   */
  private function init(){
    new WC_POS_Pro_Admin_Settings_General();
  }

	/**
	 * Settings scripts
	 */
	public function enqueue_admin_scripts() {

    $build = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'build' : 'min';

		wp_enqueue_script(
			WC_POS_PRO_PLUGIN_NAME . '-admin',
			WC_POS_PRO_PLUGIN_URL . 'assets/js/admin.'. $build .'.js',
			array( WC_POS_PLUGIN_NAME . '-admin-app' ),
			WC_POS_VERSION,
			true
		);

	}

  /**
   * @param $caps
   */
  static public function capabilities($caps){
    $woocaps = array(
      'edit_product',
      'edit_published_products',
      'edit_others_products',
      'edit_users',
      'create_users',
      'read_private_shop_coupons'
    );
    $caps['woo'] = array_merge($caps['woo'], $woocaps);
    return $caps;
  }

} 