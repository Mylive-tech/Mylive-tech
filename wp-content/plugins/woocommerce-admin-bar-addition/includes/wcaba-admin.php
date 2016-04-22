<?php
/**
 * Helper functions for the admin - plugin links.
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Admin
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
 * Add "WooCommerce Settings" link to plugin page.
 *
 * @since  2.5.0
 *
 * @param  $wcaba_links
 *
 * @return strings String of WooCommerce Settings admin link.
 */
function ddw_wcaba_settings_page_link( $wcaba_links ) {

	/** WooCommerce Settings Admin link */
	$wcaba_settings_link = sprintf(
		'<a href="%s" title="%s">%s</a>',
		admin_url( 'admin.php?page=woocommerce_settings' ),
		esc_html__( 'Go to the WooCommerce settings page', 'woocommerce-admin-bar-addition' ),
		esc_attr__( 'WooCommerce Settings', 'woocommerce-admin-bar-addition' )
	);

	/** Set the order of the links */
	array_unshift( $wcaba_links, $wcaba_settings_link );

	/** Display plugin settings links */
	return apply_filters( 'wcaba_filter_settings_page_link', $wcaba_links );

}  // end of function ddw_wcaba_settings_page_link


add_filter( 'plugin_row_meta', 'ddw_wcaba_plugin_links', 10, 2 );
/**
 * Add various support links to plugin page
 *
 * @since  1.0.0
 *
 * @param  $wcaba_links
 * @param  $wcaba_file
 */
function ddw_wcaba_plugin_links( $wcaba_links, $wcaba_file ) {

	/** Capability check */
	if ( ! current_user_can( 'install_plugins' ) ) {

		return $wcaba_links;

	}  // end if

	/** List additional links only for this plugin */
	if ( $wcaba_file == WCABA_PLUGIN_BASEDIR . 'woocommerce-admin-bar-addition.php' ) {

		$wcaba_info = (array) ddw_wcaba_info_values();

		$wcaba_links[] = '<a href="' . esc_url( $wcaba_info[ 'url_wporg_faq' ] ) . '" target="_new" title="' . __( 'FAQ', 'woocommerce-admin-bar-addition' ) . '">' . __( 'FAQ', 'woocommerce-admin-bar-addition' ) . '</a>';

		$wcaba_links[] = '<a href="' . esc_url( $wcaba_info[ 'url_wporg_forum' ] ) . '" target="_new" title="' . __( 'Support', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Support', 'woocommerce-admin-bar-addition' ) . '</a>';

		$wcaba_links[] = '<a href="' . esc_url( $wcaba_info[ 'url_snippets' ] ) . '" target="_new" title="' . __( 'Code Snippets for Customization', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Code Snippets', 'woocommerce-admin-bar-addition' ) . '</a>';

		$wcaba_links[] = '<a href="' . esc_url( $wcaba_info[ 'url_translate' ] ) . '" target="_new" title="' . __( 'Translations', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Translations', 'woocommerce-admin-bar-addition' ) . '</a>';

		$wcaba_links[] = '<a href="' . esc_url( $wcaba_info[ 'url_donate' ] ) . '" target="_new" title="' . __( 'Donate', 'woocommerce-admin-bar-addition' ) . '"><strong>' . __( 'Donate', 'woocommerce-admin-bar-addition' ) . '</strong></a>';

	}  // end if

	/** Output the links */
	return apply_filters( 'wcaba_filter_plugin_links', $wcaba_links );

}  // end of function ddw_wcaba_plugin_links


// 2.1.0+:
add_action( 'load-woocommerce_page_wc-settings', 'ddw_wcaba_woocommerce_help', 15 );
add_action( 'load-toplevel_page_wc-settings', 'ddw_wcaba_woocommerce_help', 15 );
add_action( 'load-woocommerce_page_wc-status', 'ddw_wcaba_woocommerce_help', 15 );
add_action( 'load-woocommerce_page_wc-reports', 'ddw_wcaba_woocommerce_help', 15 );
add_action( 'load-woocommerce_page_wc-addons', 'ddw_wcaba_woocommerce_help', 15 );
// 2.0.x:
add_action( 'load-woocommerce_page_woocommerce_settings', 'ddw_wcaba_woocommerce_help', 15 );
add_action( 'load-toplevel_page_woocommerce', 'ddw_wcaba_woocommerce_help', 15 );
add_action( 'load-woocommerce_page_woocommerce_status', 'ddw_wcaba_woocommerce_help', 15 );
add_action( 'load-woocommerce_page_woocommerce_reports', 'ddw_wcaba_woocommerce_help', 15 );
/**
 * Load plugin help tab after core help tabs on WooCommerce admin pages.
 *
 * @since  2.5.0
 *
 * @global mixed $wcaba_woocommerce_screen
 */
function ddw_wcaba_woocommerce_help() {

	global $wcaba_woocommerce_screen;

	$wcaba_woocommerce_screen = get_current_screen();

	/** Display help tabs only for WordPress 3.3 or higher */
	if( ! class_exists( 'WP_Screen' )
		|| ! $wcaba_woocommerce_screen
		|| ( defined( 'WCABA_DISPLAY' ) && ! WCABA_DISPLAY )
	) {

		return;

	}  // end if

	/** Add the help tab */
	$wcaba_woocommerce_screen->add_help_tab( array(
		'id'       => 'wcaba-woocommerce-help',
		'title'    => __( 'WooCommerce Admin Bar Addition', 'woocommerce-admin-bar-addition' ),
		'callback' => apply_filters( 'wcaba_filter_help_tab_content', 'ddw_wcaba_help_tab_content' ),
	) );

	/** Add help sidebar */
	if ( 'woocommerce' != $_GET[ 'page' ] && 'woocommerce_reports' != $_GET[ 'page' ] ) {

		$wcaba_woocommerce_screen->set_help_sidebar( ddw_wcaba_help_sidebar_content_extra() . ddw_wcaba_help_sidebar_content()
		);

	}  // end if page hook check

}  // end of function ddw_wcaba_woocommerce_help


/**
 * Create and display plugin help tab content
 *
 * @since 1.7.0
 *
 * @uses  ddw_wcaba_info_values()
 * @uses  ddw_wcaba_plugin_get_data()
 */
function ddw_wcaba_help_tab_content() {

	$wcaba_info = (array) ddw_wcaba_info_values();

	echo '<h3>' . __( 'Plugin', 'woocommerce-admin-bar-addition' ) . ': ' . __( 'WooCommerce Admin Bar Addition', 'woocommerce-admin-bar-addition' ) . ' <small>v' . esc_attr( ddw_wcaba_plugin_get_data( 'Version' ) ) . '</small></h3>';

	echo '<ul>' . 
			'<li><a href="' . esc_url( $wcaba_info[ 'url_suggestions' ] ) . '" target="_new" title="' . __( 'Suggest new resource items, themes or plugins for support', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Suggest new resource items, themes or plugins for support', 'woocommerce-admin-bar-addition' ) . '</a></li>' .
			'<li><a href="' . esc_url( $wcaba_info[ 'url_snippets' ] ) . '" target="_new" title="' . __( 'Code snippets for customizing &amp; branding', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Code snippets for customizing &amp; branding', 'woocommerce-admin-bar-addition' ) . '</a></li>';

		echo '<li><em>' . __( 'Other, recommended WooCommerce plugins', 'woocommerce-admin-bar-addition' ) . '</em>:';

			/** Optional: recommended plugins */
			if ( $wcaba_info[ 'is_german' ]
				&& ( ! defined( 'WCDE_EXTENSIONS_DIR' ) || ! defined( 'WCDE_PLUGIN_BASEDIR' ) )
			) {

				echo '<br />&raquo; <a href="' . esc_url( $wcaba_info[ 'de_plugin' ] ) . '" target="_new" title="WooCommerce Deutsch (de_DE)">WooCommerce Deutsch (de_DE)</a> &mdash; erweitert das WooCommerce Shop-Plugin mit komplett übersetzten deutschen Sprachdateien - in SIE- und DU-Version (formell/ informell)';

			}  // end if

			if ( $wcaba_info[ 'is_dutch' ] && ! class_exists( 'WooCommerceNL' ) ) {

				echo '<br />&raquo; <a href="' . esc_url( $wcaba_info[ 'nl_plugin' ] ) . '" target="_new" title="WooCommerce (nl)">WooCommerce (nl)</a> &mdash; verlengt de WooCommerce plugin met de Nederlandse taal';

			}  // end if

			if ( $wcaba_info[ 'is_spanish' ] && ! class_exists( 'WooCommerceESPlugin' ) ) {

				echo '<br />&raquo; <a href="' . esc_url( $wcaba_info[ 'es_plugin' ] ) . '" target="_new" title="WooCommerce (es)">WooCommerce (es)</a> &mdash; vEste plugin incluye el WooCommerce Traducción y algunos plugins que realza.';

			}  // end if

			if ( $wcaba_info[ 'is_czech' ] && ! function_exists( 'wcc_load_textdomain' ) ) {

				echo '<br />&raquo; <a href="' . esc_url( $wcaba_info[ 'cz_plugin' ] ) . '" target="_new" title="WooCommerce čeština">WooCommerce čeština</a> &mdash; Přeloží plugin WooCommerce do češtiny. ';

			}  // end if

			if ( ! class_exists( 'WooCommerce_Delivery_Notes' ) ) {

				echo '<br />&raquo; <a href="http://wordpress.org/plugins/woocommerce-delivery-notes/" target="_new" title="WooCommerce Print Invoices & Delivery Notes">WooCommerce Print Invoices & Delivery Notes</a> &mdash; ' . __( ' print order invoices & delivery notes - you can add company/shop info as well as personal notes & policies to print pages', 'woocommerce-admin-bar-addition' );

			}  // end if

			if ( ! class_exists( 'woocommerce_custom_status' ) ) {

				echo '<br />&raquo; <a href="http://wordpress.org/plugins/woocommerce-custom-statuses/" target="_new" title="WooCommerce Custom Status">WooCommerce Custom Status</a> &mdash; ' . __( 'ability to add custom order statuses', 'woocommerce-admin-bar-addition' );

			}  // end if

		echo '<br />&raquo; <a href="http://ddwb.me/wccc" target="_new" title="' . __( 'More premium plugins/extensions at CodeCanyon Marketplace', 'woocommerce-admin-bar-addition' ) . ' &hellip;">' . __( 'More premium plugins/extensions at CodeCanyon Marketplace', 'woocommerce-admin-bar-addition' ) . ' &hellip;</a>' .
		'<br />&raquo; <a href="http://wordpress.org/plugins/search.php?q=woocommerce" target="_new" title="' . __( 'More free plugins/extensions at WP.org', 'woocommerce-admin-bar-addition' ) . ' &hellip;">' . __( 'More free plugins/extensions at WP.org', 'woocommerce-admin-bar-addition' ) . ' &hellip;</a></li>' .
		'</ul>';

	/** Set first release year */
	$release_first_year = ( '' != $wcaba_info[ 'first_release' ] && date( 'Y' ) != $wcaba_info[ 'first_release' ] ) ? $wcaba_info[ 'first_release' ] . '&#x02013;' : '';

	echo '<p><strong>' . __( 'Important plugin links:', 'woocommerce-admin-bar-addition' ) . '</strong>' . 
		'<br /><a href="' . esc_url( $wcaba_info[ 'url_plugin' ] ) . '" target="_new" title="' . __( 'Plugin Website', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Plugin Website', 'woocommerce-admin-bar-addition' ) . '</a> | <a href="' . esc_url( $wcaba_info[ 'url_wporg_faq' ] ) . '" target="_new" title="' . __( 'FAQ', 'woocommerce-admin-bar-addition' ) . '">' . __( 'FAQ', 'woocommerce-admin-bar-addition' ) . '</a> | <a href="' . esc_url( $wcaba_info[ 'url_wporg_forum' ] ) . '" target="_new" title="' . __( 'Support', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Support', 'woocommerce-admin-bar-addition' ) . '</a> | <a href="' . esc_url( $wcaba_info[ 'url_snippets' ] ) . '" target="_new" title="' . __( 'Code Snippets for Customization', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Code Snippets', 'woocommerce-admin-bar-addition' ) . '</a> | <a href="' . esc_url( $wcaba_info[ 'url_translate' ] ) . '" target="_new" title="' . __( 'Translations', 'woocommerce-admin-bar-addition' ) . '">' . __( 'Translations', 'woocommerce-admin-bar-addition' ) . '</a> | <a href="' . esc_url( $wcaba_info[ 'url_donate' ] ) . '" target="_new" title="' . __( 'Donate', 'woocommerce-admin-bar-addition' ) . '"><strong>' . __( 'Donate', 'woocommerce-admin-bar-addition' ) . '</strong></a></p>';

	echo '<p><a href="http://www.opensource.org/licenses/gpl-license.php" target="_new" title="' . esc_attr( $wcaba_info[ 'license' ] ). '">' . esc_attr( $wcaba_info[ 'license' ] ). '</a> &#x000A9; ' . $release_first_year . date( 'Y' ) . ' <a href="' . esc_url( ddw_wcaba_plugin_get_data( 'AuthorURI' ) ) . '" target="_new" title="' . esc_attr__( ddw_wcaba_plugin_get_data( 'Author' ) ) . '">' . esc_attr__( ddw_wcaba_plugin_get_data( 'Author' ) ) . '</a></p>';

}  // end of function ddw_wcaba_help_tab_content


/**
 * Helper function for returning the Help Sidebar content.
 *
 * @since  2.6.0
 *
 * @uses   ddw_wcaba_plugin_get_data()
 * @uses   ddw_wcaba_info_values
 *
 * @return string HTML content for help sidebar.
 */
function ddw_wcaba_help_sidebar_content() {

	$wcaba_info = (array) ddw_wcaba_info_values();

	$wcaba_help_sidebar_content = '<p><strong>' . __( 'More about the plugin author', 'woocommerce-admin-bar-addition' ) . '</strong></p>' .
			'<p>' . __( 'Social:', 'woocommerce-admin-bar-addition' ) . '<br /><a href="http://twitter.com/deckerweb" target="_blank" title="@ Twitter">Twitter</a> | <a href="http://www.facebook.com/deckerweb.service" target="_blank" title="@ Facebook">Facebook</a> | <a href="http://deckerweb.de/gplus" target="_blank" title="@ Google+">Google+</a> | <a href="' . esc_url( ddw_wcaba_plugin_get_data( 'AuthorURI' ) ) . '" target="_blank" title="@ deckerweb.de">deckerweb</a></p>' .
			'<p><a href="' . esc_url( $wcaba_info[ 'url_wporg_profile' ] ) . '" target="_blank" title="@ WordPress.org">@ WordPress.org</a></p>';

	return apply_filters( 'wcaba_filter_help_sidebar_content', $wcaba_help_sidebar_content );

}  // end of function ddw_wcaba_help_sidebar_content


/**
 * Helper function for returning the Help Sidebar content - extra for plugin setting page.
 *
 * @since  2.6.0
 *
 * @uses   ddw_wcaba_info_values
 *
 * @return string Extra HTML content for help sidebar.
 */
function ddw_wcaba_help_sidebar_content_extra() {

	$wcaba_info = (array) ddw_wcaba_info_values();

	$wcaba_help_sidebar_content_extra = '<p><strong>' . __( 'Actions', 'woocommerce-admin-bar-addition' ) . '</strong></p>' .
		'<p>&rarr; <a href="' . esc_url( $wcaba_info[ 'url_wporg_forum' ] ) . '" target="_new">' . __( 'Support Forum', 'woocommerce-admin-bar-addition' ) . '</a></p>' .
		'<p style="margin-top: -5px;">&rarr; <a href="' . esc_url( $wcaba_info[ 'url_donate' ] ) . '" target="_new">' . __( 'Donate', 'woocommerce-admin-bar-addition' ) . '</a></p><br />';

	return apply_filters( 'wcaba_filter_help_sidebar_content_extra', $wcaba_help_sidebar_content_extra );

}  // end of function ddw_wcaba_help_sidebar_content_extra