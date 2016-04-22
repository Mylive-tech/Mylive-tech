<?php
/**
 * Display the resource links for the "WooCommerce Group".
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Resources
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
 * Resource links collection.
 *
 * @since 1.0.0
 */
$wcaba_resources = (array) ddw_wcaba_resources_values();

/** Support menu items */
$woogroup_menu_items[ 'wcsupport' ] = array(
	'parent' => $wcgroup,
	'title'  => __( 'WooCommerce Support', 'woocommerce-admin-bar-addition' ),
	'href'   => esc_url( $wcaba_resources[ 'woo_support_premium' ] ),
	'meta'   => array( 'title' => __( 'WooCommerce Support', 'woocommerce-admin-bar-addition' ) )
);

	$woogroup_menu_items[ 'wcpremiumsupport' ] = array(
		'parent' => $support,
		'title'  => __( 'Official Premium Support', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_support_premium' ] ),
		'meta'   => array( 'title' => __( 'Official Premium Support', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wccommunityforum' ] = array(
		'parent' => $support,
		'title'  => __( 'Public Community Forum', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_forum_community' ] ),
		'meta'   => array( 'title' => __( 'Public Community Forum', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcuaccount' ] = array(
		'parent' => $support,
		'title'  => __( 'My Account/ Dashboard', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_account' ] ),
		'meta'   => array( 'title' => __( 'My User Account/ Dashboard at WooThemes.com', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcsupportwporg' ] = array(
		'parent' => $support,
		'title'  => __( 'Free Support Forum (WP.org)', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_wporg_forum' ] ),
		'meta'   => array( 'title' => __( 'Free Support Forum (WP.org)', 'woocommerce-admin-bar-addition' ) )
	);

/** Docs/Codex menu items */
$woogroup_menu_items[ 'wcdocs' ] = array(
	'parent' => $wcgroup,
	'title'  => __( 'Documentation &amp; User Guide', 'woocommerce-admin-bar-addition' ),
	'href'   => esc_url( $wcaba_resources[ 'woo_docs' ] ),
	'meta'   => array( 'title' => __( 'Documentation &amp; User Guide', 'woocommerce-admin-bar-addition' ) )
);

	$woogroup_menu_items[ 'wcdocs-userguide' ] = array(
		'parent' => $wcdocs,
		'title'  => __( 'User Guide', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_docs_userguide' ] ),
		'meta'   => array( 'title' => __( 'User Guide', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcdocs-codex' ] = array(
		'parent' => $wcdocs,
		'title'  => __( 'Codex', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_docs_codex' ] ),
		'meta'   => array( 'title' => __( 'Codex', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcdocs-tutorials' ] = array(
		'parent' => $wcdocs,
		'title'  => __( 'Tutorials', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_docs_tutorials' ] ),
		'meta'   => array( 'title' => __( 'Tutorials', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcdocs-snippets' ] = array(
		'parent' => $wcdocs,
		'title'  => __( 'Snippets', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_docs_snippets' ] ),
		'meta'   => array( 'title' => __( 'Snippets', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcdocs-communityvideos' ] = array(
		'parent' => $wcdocs,
		'title'  => __( 'Community Videos', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'vtutorials_ytbe' ] ),
		'meta'   => array( 'title' => __( 'Community Videos', 'woocommerce-admin-bar-addition' ) )
	);


/** For the Docs/Codex search*/
$wcaba_search_docs = __( 'Search Docs', 'woocommerce-admin-bar-addition' );
$wcaba_go_button = '<input type="submit" value="' . __( 'GO', 'woocommerce-admin-bar-addition' ) . '" class="button button-primary wcaba-search-go" /></form>';

/** Docs/Codex search form */
$woogroup_menu_items[ 'wcdocs-searchform' ] = array(
	'parent' => $wcgroup,
	'title'  => '<form method="get" action="http://docs.woothemes.com/" class=" " target="_blank">
	<input type="hidden" name="post_type" value="document" /><input type="hidden" name="search_in" value="woocommerce" /><input type="text" placeholder="' . $wcaba_search_docs . '" onblur="this.value=(this.value==\'\') ? \'' . $wcaba_search_docs . '\' : this.value;" onfocus="this.value=(this.value==\'' . $wcaba_search_docs . '\') ? \'\' : this.value;" value="' . $wcaba_search_docs . '" name="s" value="' . esc_attr( 'Search Docs', 'woocommerce-admin-bar-addition' ) . '" class="text wcaba-search-input" />' . $wcaba_go_button,
	'href'   => false,
	'meta'   => array(
		'target' => '',
		'title'  => _x( 'Search Docs', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
	)
);

/** WooCommerce HQ menu items */
$woogroup_menu_items[ 'woocommercesites' ] = array(
	'parent' => $wcgroup,
	'title'  => __( 'WooCommerce HQ', 'woocommerce-admin-bar-addition' ),
	'href'   => esc_url( $wcaba_resources[ 'woo_hq' ] ),
	'meta'   => array( 'title' => __( 'WooCommerce HQ', 'woocommerce-admin-bar-addition' ) )
);

	$woogroup_menu_items[ 'wcideas' ] = array(
		'parent' => $woocommercesites,
		'title'  => __( 'Ideas &amp; Feature Requests', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_uservoice' ] ),
		'meta'   => array( 'title' => __( 'Ideas &amp; Feature Requests', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcbugs' ] = array(
		'parent' => $woocommercesites,
		'title'  => __( 'Issues &amp; Bug Reports', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_github_issues' ] ),
		'meta'   => array( 'title' => __( 'Issues &amp; Bug Reports', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcdev' ] = array(
		'parent' => $woocommercesites,
		'title'  => __( 'GitHub Repository Developer Center', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_github' ] ),
		'meta'   => array( 'title' => __( 'GitHub Repository Developer Center', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcdevblog' ] = array(
		'parent' => $woocommercesites,
		'title'  => __( 'Developer Blog &amp; Resources', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_wcdevblog' ] ),
		'meta'   => array( 'title' => __( 'Developer Blog &amp; Resources', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcextensions' ] = array(
		'parent' => $woocommercesites,
		'title'  => __( 'Official WooCommerce Extensions', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'woo_extensions' ] ),
		'meta'   => array( 'title' => __( 'Official WooCommerce Extensions', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcccmarket' ] = array(
		'parent' => $woocommercesites,
		'title'  => __( 'More premium extensions at CodeCanyon', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'cc_plugins' ] ),
		'meta'   => array( 'title' => __( 'More premium plugins/extensions at CodeCanyon Marketplace', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcplugins' ] = array(
		'parent' => $woocommercesites,
		'title'  => __( 'More free plugins/extensions at WP.org', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'wporg_search' ] ),
		'meta'   => array( 'title' => __( 'More free plugins/extensions at WP.org', 'woocommerce-admin-bar-addition' ) )
	);

	$woogroup_menu_items[ 'wcffnews' ] = array(
		'parent' => $woocommercesites,
		'title'  => __( 'WooCommerce News Planet', 'woocommerce-admin-bar-addition' ),
		'href'   => esc_url( $wcaba_resources[ 'friendfeed' ] ),
		'meta'   => array( 'title' => _x( 'WooCommerce News Planet (official and community news via FriendFeed service)', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' ) )
	);