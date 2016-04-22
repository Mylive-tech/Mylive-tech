<?php
/**
 * Display links to active extensions specific settings' pages: Pronamic iDEAL.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2011-2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.7.0
 */

/**
 * Prevent direct access to this file.
 *
 * @since 2.6.0
 */
if ( ! defined( 'WPINC' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Pronamic iDEAL settings pages (free, by Pronamic)
 *
 * @since 1.7.0
 */

	$menu_items[ 'extpideal' ] = array(
		'parent' => $extensions,
		'title'  => __( 'Pronamic iDEAL Payment Gateway', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=pronamic_ideal' ),
		'meta'   => array(
			'target' => '',
			'title'  => __( 'Pronamic iDEAL Payment Gateway', 'woocommerce-admin-bar-addition' )
		)
	);

	/** Check for Payments */
	if ( current_user_can( 'pronamic_ideal_payments' ) ) {
		$menu_items[ 'ext-pideal-payments' ] = array(
			'parent' => $extpideal,
			'title'  => __( 'Payments', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=pronamic_ideal_payments' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Payments', 'woocommerce-admin-bar-addition' )
			)
		);
	}  // end-if cap check

	/** Check for Settings */
	if ( current_user_can( 'pronamic_ideal_settings' ) ) {

		$menu_items[ 'ext-pideal-settings' ] = array(
			'parent' => $extpideal,
			'title'  => __( 'Settings', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=pronamic_ideal_settings' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Settings', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end-if cap check

	/** Check for Pages Generator */
	if ( current_user_can( 'pronamic_ideal_pages_generator' ) ) {

		$menu_items[ 'ext-pideal-pages-generator' ] = array(
			'parent' => $extpideal,
			'title'  => __( 'Pages Generator', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=pronamic_ideal_pages_generator' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Pages Generator', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end-if cap check

	/** Check for Variants */
	if ( current_user_can( 'pronamic_ideal_variants' ) ) {

		$menu_items[ 'ext-pideal-variants' ] = array(
			'parent' => $extpideal,
			'title'  => __( 'Variants', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=pronamic_ideal_variants' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Variants', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end-if cap check

	/** Check for Documentation */
	if ( current_user_can( 'pronamic_ideal_documentation' ) ) {

		$menu_items[ 'ext-pideal-documentation' ] = array(
			'parent' => $extpideal,
			'title'  => __( 'Documentation', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=pronamic_ideal_documentation' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Documentation', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end-if cap check