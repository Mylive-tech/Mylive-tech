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

/**
 * Set filter for plugin's languages directory.
 *
 * @since  2.6.0
 *
 * @return string Path to plugin's languages directory.
 */
function ddw_wcaba_plugin_lang_dir() {

	return apply_filters( 'wcaba_filter_lang_dir', WCABA_PLUGIN_BASEDIR . 'languages' );

}  // end of function ddw_wcaba_plugin_lang_dir


/**
 * Helper function for detecting an array of specific locales.
 *
 * @since 2.6.0
 *
 * @uses  get_locale()
 *
 * @return bool Returns TRUE if locales are currently active, otherwise FALSE.
 */
function ddw_wcaba_detect_locales( $locales ) {
	
	return ( in_array( get_locale(), (array) $locales ) ) ? TRUE : FALSE;

}  // end of function ddw_wcaba_detect_locales


/**
 * Setting internal plugin helper values.
 *
 * @since 2.6.0
 *
 * @uses  ddw_wcaba_detect_locales()
 */
function ddw_wcaba_info_values() {

	$wcaba_info = array(

		'url_translate'     => 'http://translate.wpautobahn.com/projects/genesis-plugins-deckerweb/woocommerce-admin-bar-addition',
		'url_wporg_faq'     => 'http://wordpress.org/plugins/woocommerce-admin-bar-addition/faq/',
		'url_wporg_forum'   => 'http://wordpress.org/support/plugin/woocommerce-admin-bar-addition',
		'url_wporg_profile' => 'http://profiles.wordpress.org/daveshine/',
		'url_snippets'      => 'https://gist.github.com/deckerweb/2173193',
		'url_suggestions'   => 'https://deckerweb.de/twitter',
		'license'           => 'GPL-2.0+',
		'first_release'     => absint( '2011' ),
		'url_donate'        => ddw_wcaba_detect_locales( 'de_DE', 'de_AT', 'de_CH', 'de_LU', 'gsw' ) ? 'http://genesisthemes.de/spenden/' : 'http://genesisthemes.de/en/donate/',
		'url_plugin'        => ddw_wcaba_detect_locales( 'de_DE', 'de_AT', 'de_CH', 'de_LU', 'gsw' ) ? 'http://genesisthemes.de/plugins/woocommerce-admin-bar-addition/' : 'http://genesisthemes.de/en/wp-plugins/woocommerce-admin-bar-addition/',
		'de_plugin'         => 'http://wordpress.org/plugins/woocommerce-de/',
		'de_files'          => 'http://deckerweb.de/sprachdateien/woocommerce-und-extensions/',
		'nl_plugin'         => 'http://wordpress.org/plugins/woocommerce-nl/',
		'es_plugin'         => 'http://wordpress.org/plugins/woocommerce-es/',
		'cz_plugin'         => 'http://wordpress.org/plugins/woocommerce-cestina/',
		'is_german'         => ddw_wcaba_detect_locales( 'de_DE', 'de_AT', 'de_CH', 'de_LU', 'gsw' ) ? TRUE : FALSE,
		'is_dutch'          => ddw_wcaba_detect_locales( 'nl', 'nl_NL' ) ? TRUE : FALSE,
		'is_czech'          => ddw_wcaba_detect_locales( 'cs_CZ' ) ? TRUE : FALSE,
		'is_spanish'        => ddw_wcaba_detect_locales( 'es_ES', 'es_PE' ) ? TRUE : FALSE,

	);  // end of array

	return $wcaba_info;

}  // end of function ddw_wcaba_info_values


/**
 * Setting WooCommerce resources values.
 *
 * @since 2.6.0
 *
 * @uses  get_locale()
 */
function ddw_wcaba_resources_values() {

	$wcaba_resources = array(

		'vtutorials_ytbe'     => apply_filters(
			'wcaba_filter_video_tutorials',
			'http://www.youtube.com/results?search_type=videos&search_query=woocommerce&search_sort=video_date_uploaded'
		),
		'friendfeed'          => 'http://friendfeed.com/woocommerce-news',
		'wporg_search'        => 'http://wordpress.org/plugins/search.php?q=woocommerce',
		'cc_plugins'          => 'http://ddwb.me/wccc',
		'woo_extensions'      => 'http://www.woothemes.com/product-category/woocommerce-extensions/',
		'woo_github'          => 'https://github.com/woothemes/woocommerce/',
		'woo_github_issues'   => 'https://github.com/woothemes/woocommerce/issues',
		'woo_wcdevblog'       => 'http://develop.woothemes.com/woocommerce',
		'woo_uservoice'       => 'http://woo.uservoice.com/forums/133476-woocommerce',
		'woo_hq'              => 'http://www.woothemes.com/woocommerce/',
		'woo_docs_snippets'   => 'http://docs.woothemes.com/documentation/plugins/woocommerce/woocommerce-codex/snippets/',
		'woo_docs_tutorials'  => 'http://docs.woothemes.com/documentation/plugins/woocommerce/woocommerce-codex/extending/',
		'woo_docs_codex'      => 'http://docs.woothemes.com/documentation/plugins/woocommerce/woocommerce-codex/',
		'woo_docs_userguide'  => 'http://docs.woothemes.com/documentation/plugins/woocommerce/getting-started/',
		'woo_docs'            => 'http://docs.woothemes.com/documentation/plugins/woocommerce/',
		'woo_wporg_forum'     => 'http://wordpress.org/support/plugin/woocommerce',
		'woo_account'         => 'https://www.woothemes.com/my-account/',
		'woo_forum_community' => 'http://support.woothemes.com/categories/20075173-community-forum',
		'woo_support_premium' => 'http://www.woothemes.com/support/',

	);  // end of array

	return $wcaba_resources;

}  // end of function ddw_wcaba_resources_values