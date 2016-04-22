<?php

/**
 * WP Admin Class
 *
 * @class    WC_POS_Pro_Admin
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_Admin {

  /* @var array Stores admin notices */
  private $notices = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init();
		add_action( 'current_screen', array( $this, 'conditional_init' ), 9, 1 );
  }

	/**
	 * Load admin subclasses
	 */
	public function init() {
    $this->_init();
    new WC_POS_Pro_Admin_AM();
	}

  /**
   * License required
   */
  private function _init(){
    if( !wc_pos_pro_activated() )
      return;

    new WC_POS_Pro_Admin_Stores();
  }

  /**
   * @param $current_screen
   */
  public function conditional_init( $current_screen ) {
    $this->_conditional_init($current_screen);

    if( $current_screen->id == WC_POS_Admin_Settings::$screen_id )
      new WC_POS_Pro_Admin_Settings();

    // Add POS Pro settings to orders pages
    if( $current_screen->id == 'shop_order' || $current_screen->id == 'edit-shop_order'  )
      new WC_POS_Pro_Admin_Orders();

	}

  /**
   * License required
   * @param $current_screen
   */
  private function _conditional_init( $current_screen ){
    if( !wc_pos_pro_activated() )
      return;

    if( $current_screen->id == 'woocommerce_page_wc-reports' )
      new WC_POS_Pro_Admin_Reports();
  }

}