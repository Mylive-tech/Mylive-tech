<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @class 	  WooCommerce_POS_Pro_Deactivator
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Deactivator {

	/**
	 * @param $file
	 */
	public function __construct( $file ){
		register_deactivation_hook( $file, array( $this, 'deactivate' ) );
	}

  /**
   * Fired when the plugin is deactivated.
   * @param $network_wide
   */
	public function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = $this->get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					$this->single_deactivate();

					restore_current_blog();

				}

			} else {
				$this->single_deactivate();
			}

		} else {
			$this->single_deactivate();
		}

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
	 * Fired when the plugin is deactivated.
	 */
	public static function single_deactivate() {

	}

}
