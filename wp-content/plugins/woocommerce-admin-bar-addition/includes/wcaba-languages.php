<?php
/**
 * Various helper functions.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Functions
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2011-2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.0.0
 */

/**
 * Prevent direct access to this file.
 *
 * @since 2.6.0
 */
if ( ! defined( 'WPINC' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}

$wcaba_info = (array) ddw_wcaba_info_values();

/** Display these links only for these locales: de_DE, de_AT, de_CH, de_LU */
if ( ( defined( 'WCABA_DE_DISPLAY' ) && WCABA_DE_DISPLAY )
	&& ddw_wcaba_detect_locales( 'de_DE', 'de_AT', 'de_CH', 'de_LU', 'gsw' )
) {

	/** WooCommerce German Extension plugin */
	if ( class_exists( 'Woocommerce_German_Market' ) ) {

		$menu_items[ 'wcgermany' ] = array(
			'parent' => $settings,
			'title'  => __( 'Preferences DE', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=preferences_de' ),
			'meta'   => array(
				'target' => '',
				'title'  => _x( 'Preferences DE', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
			)
		);

		$menu_items[ 'preferences-de' ] = array(
			'parent' => $extensions,
			'title'  => __( 'Preferences DE', 'woocommerce-admin-bar-addition' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_settings&tab=preferences_de' ),
			'meta'   => array(
				'target' => '',
				'title'  => _x( 'Preferences DE', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
			)
		);

	}  // end-if German Extension plugin

	/** German language plugin - only if DE plugin not active */
	if ( ! defined( 'WCDE_EXTENSIONS_DIR' ) || ! class_exists( 'WooCommerce_de_DE' ) ) {

		$menu_items[ 'wclanguages-de' ] = array(
			'parent' => $wcgroup,
			'title'  => __( 'German language plugin', 'woocommerce-admin-bar-addition' ),
			'href'   => esc_url( $wcaba_info[ 'de_plugin' ] ),
			'meta'   => array( 'title' => _x( 'German language plugin for WooCommerce - with complete translations and formal/informal version support!', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' ) )
		);

	}  // end-if German language plugin

	/** Downloadable German language packs */
	$menu_items[ 'languages-de' ] = array(
		'parent' => $wcgroup,
		'title'  => __( 'German language files', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_info[ 'de_files' ] ),
		'meta'   => array( 'title' => _x( 'German language files', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' ) )
	);

}  // end if German locale


/** Display links to language plugin only for this locale: nl, nl_NL - and when NL plugin not active */
if ( ( defined( 'WCABA_NL_DISPLAY' ) && WCABA_NL_DISPLAY )
	&& ( ddw_wcaba_detect_locales( 'nl', 'nl_NL' ) && ! class_exists( 'WooCommerceNL' ) )
) {

	$menu_items[ 'wclanguages-nl' ] = array(
		'parent' => $wcgroup,
		'title'  => __( 'Dutch language plugin', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_info[ 'nl_plugin' ] ),
		'meta'   => array( 'title' => _x( 'Dutch language plugin for WooCommerce - with complete translations!', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' ) )
	);

}  // end if Dutch locale


/** Display links to language plugin only for this locale: es_ES, es_PE - and when ES plugin not active */
if ( ( defined( 'WCABA_ES_DISPLAY' ) && WCABA_ES_DISPLAY )
	&& ( ddw_wcaba_detect_locales( 'es_ES', 'es_PE' ) && ! class_exists( 'WooCommerceESPlugin' ) )
) {

	$menu_items[ 'wclanguages-es' ] = array(
		'parent' => $wcgroup,
		'title'  => __( 'Spanish language plugin', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_info[ 'es_plugin' ] ),
		'meta'   => array( 'title' => _x( 'Spanish language plugin for WooCommerce - with complete translations!', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' ) )
	);

}  // end if Spanish locale


/** Display links to language plugin only for this locale: cs_CZ - and when CZ plugin not active */
if ( ( defined( 'WCABA_CZ_DISPLAY' ) && WCABA_CZ_DISPLAY )
	&& ( ddw_wcaba_detect_locales( 'cs_CZ' ) && ! function_exists( 'wcc_load_textdomain' ) )
) {

	$menu_items[ 'wclanguages-cz' ] = array(
		'parent' => $wcgroup,
		'title'  => __( 'Czech language plugin', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_info[ 'cz_plugin' ] ),
		'meta'   => array( 'title' => _x( 'Czech language plugin for WooCommerce - with complete translations!', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' ) )
	);

}  // end if Czech locale