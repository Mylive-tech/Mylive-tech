<?php
/**
 * Display links to active WooCommerce compatible/specific themes settings' pages
 *
 * @package    WooCommerce Admin Bar Addition
 * @subpackage Theme Support
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
 * Get current stylesheet name logic - compatible up to WordPress 3.4+!
 *
 * @since  2.3.0
 *
 * @global mixed $stylesheet
 */
global $stylesheet;

if ( function_exists( 'wp_get_theme' ) ) {			// First, check for WP 3.4+ function wp_get_theme()

	$wcaba_stylesheet_name = wp_get_theme( $stylesheet );

} elseif ( function_exists( 'get_current_theme' ) ) {		// Otherwise fall back to prior WP 3.4 default get_current_theme()

	$wcaba_stylesheet_name = get_current_theme();

} // end if


/**
 * "Theme Settings" String for all Themes/Child Themes
 *
 * @since 2.2.0
 */
$wcaba_themesettings = '&nbsp;' . __( 'Theme Settings', 'woocommerce-admin-bar-addition' );


/**
 * Display link to active WooThemes theme settings page (free & premium, by WooThemes)
 *
 * @since 1.0.0
 */
if ( function_exists ( 'woo_version_init' ) && current_user_can( 'manage_options' ) ) {

	$menu_items[ 'woothemes-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => __( 'WooThemes Settings', 'woocommerce-admin-bar-addition' ),
		'href'   => admin_url( 'admin.php?page=woothemes' ),
		'meta'   => array( 'target' => '', 'title' => __( 'WooThemes Settings', 'woocommerce-admin-bar-addition' ) )
	);

}  // end-if WooThemes


/**
 * Display link to active Genesis Framework theme settings page (premium, by StudioPress)
 *
 * @since 2.1.0
 */
if ( ! function_exists( 'ddw_gtbe_admin_bar_menu' ) && defined( 'GENESIS_SETTINGS_FIELD' ) && current_user_can( 'edit_theme_options' ) ) {

	$menu_items[ 'genesis-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Genesis' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=genesis' ),
		'meta'   => array( 'target' => '', 'title' => 'Genesis' . $wcaba_themesettings )
	);

}  // end-if Genesis


/**
 * Display link to active Abundance theme settings page (premium, by Kriesi at ThemeForest)
 *
 * @since 1.1.0
 */
if ( ( $wcaba_stylesheet_name == 'Abundance' || get_template() == 'abundance' )
	&& current_user_can( 'manage_options' )
) {

	$menu_items[ 'abundance-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Abundance' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=avia' ),
		'meta'   => array( 'target' => '', 'title' => 'Abundance' . $wcaba_themesettings )
	);

}  // end-if Abundance


/**
 * Display link to active Propulsion theme settings page (premium, by Kriesi at ThemeForest)
 *
 * @since 1.5
 */
if ( ( $wcaba_stylesheet_name == 'Propulsion' || get_template() == 'propulsion' )
	&& current_user_can( 'manage_options' )
) {

	$menu_items[ 'propulsion-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Propulsion' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=avia' ),
		'meta'   => array( 'target' => '', 'title' => 'Propulsion' . $wcaba_themesettings )
	);

}  // end-if Propulsion


/**
 * Display link to active Flashlight theme settings page (premium, by Kriesi at ThemeForest)
 *
 * @since 1.5.0
 */
if ( ( $wcaba_stylesheet_name == 'Flashlight' || get_template() == 'flashlight' )
	&& current_user_can( 'manage_options' )
) {

	$menu_items[ 'flashlight-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Flashlight' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=avia' ),
		'meta'   => array( 'target' => '', 'title' => 'Flashlight' . $wcaba_themesettings )
	);

}  // end-if Flashlight


/**
 * Display link to active WP Sharp theme settings page (premium, by PrimaThemes at ThemeForest)
 *
 * @since 1.5.0
 */
if ( ( $wcaba_stylesheet_name == 'WP Sharp' || get_template() == 'wpsharp' )
	&& ( current_user_can( 'edit_theme_options' ) || current_user_can( 'manage_options' ) )
) {

	$menu_items[ 'wpsharp-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'WP Sharp' . $wcaba_themesettings,
		'href'   => admin_url( 'themes.php?page=primathemes' ),
		'meta'   => array( 'target' => '', 'title' => 'WP Sharp' . $wcaba_themesettings )
	);

}  // end-if WP Sharp


/**
 * Display link to active WP Flexishop 2 theme settings page (premium, by PrimaThemes at ThemeForest)
 *
 * @since 2.3.0
 */
if ( ( $wcaba_stylesheet_name == 'WP FlexiShop Two' || get_template() == 'wpflexishoptwo' )
	&& ( current_user_can( 'edit_theme_options' ) || current_user_can( 'manage_options' ) )
) {

	$menu_items[ 'wpflexishoptwo-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'WP Flexishop 2' . $wcaba_themesettings,
		'href'   => admin_url( 'themes.php?page=primathemes' ),
		'meta'   => array( 'target' => '', 'title' => 'WP Flexishop 2' . $wcaba_themesettings )
	);

}  // end-if WP Flexishop 2


/**
 * Display link to active Clean Cut eCommerce theme settings page (premium, by Obox Design)
 *
 * @since 1.6.0
 */
if ( ( $wcaba_stylesheet_name == 'Clean Cut eCommerce' || $wcaba_stylesheet_name == 'Clean Cut'	|| get_template() == 'cleancut' )
	&& ( current_user_can( 'manage_options' ) || current_user_can( 'edit_themes' ) )
) {

	$menu_items[ 'cleancut-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Clean Cut eCommerce' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=functions.php' ),
		'meta'   => array( 'target' => '', 'title' => 'Clean Cut eCommerce' . $wcaba_themesettings )
	);

}  // end-if Clean Cut eCommerce


/**
 * Display link to active Wardrobe theme settings page (premium, by ColorLabs & Company)
 *
 * @since 1.7.0
 */
if ( ( $wcaba_stylesheet_name == 'Wardrobe'	|| get_template() == 'wardrobe' )
	&& current_user_can( 'edit_theme_options' )
) {

	$menu_items[ 'wardrobe-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Wardrobe' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=colabsthemes_options' ),
		'meta'   => array( 'target' => '', 'title' => 'Wardrobe' . $wcaba_themesettings )
	);

}  // end-if Wardrobe


/**
 * Display link to active Handmade eCommerce theme settings page (premium, by Obox Design)
 *
 * @since 1.8.0
 */
if ( ( $wcaba_stylesheet_name == 'Handmade' || $wcaba_stylesheet_name == 'Handmade eCommerce' || get_template() == 'handmade' )
	&& ( current_user_can( 'manage_options' ) || current_user_can( 'edit_themes' ) )
) {

	$menu_items[ 'handmade-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Handmade eCommerce' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=functions.php' ),
		'meta'   => array( 'target' => '', 'title' => 'Handmade eCommerce' . $wcaba_themesettings )
	);

}  // end-if Handmade eCommerce


/**
 * Display link to active TeeShirt Lite theme settings page (free, by WooCommerceThemes.me)
 *
 * @since 2.1.0
 */
if ( ( $wcaba_stylesheet_name == 'TeeShirt Lite' || get_template() == 'teeshirtlite' )
	&& current_user_can( 'edit_theme_options' )
) {

	$menu_items[ 'teeshirtlite-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'TeeShirt Lite' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=wct' ),
		'meta'   => array( 'target' => '', 'title' => 'TeeShirt Lite' . $wcaba_themesettings )
	);

}  // end-if TeeShirt Lite


/**
 * Display link to active Sommerce theme settings page (premium, by Sara_p at ThemeForest)
 *
 * @since 2.1.0
 */
if ( ( $wcaba_stylesheet_name == 'Sommerce' || get_template() == 'sommerce' || defined( 'YIW_OPTIONS_DB' ) )
	&& current_user_can( 'edit_theme_options' )
) {

	$menu_items[ 'sommerce-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Sommerce' . $wcaba_themesettings,
		'href'   => admin_url( 'themes.php?page=yiw_panel' ),
		'meta'   => array( 'target' => '', 'title' => 'Sommerce' . $wcaba_themesettings )
	);

}  // end-if Sommerce


/**
 * Display link to active Maya Shop theme settings page (premium, by Sara_p at ThemeForest)
 *
 * @since 2.2.0
 */
if ( ( $wcaba_stylesheet_name == 'Maya Shop' || ( get_template() == 'maya' || get_template() == 'maya-shop' ) )
	&& current_user_can( 'edit_theme_options' )
) {

	$menu_items[ 'mayashop-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Maya Shop' . $wcaba_themesettings,
		'href'   => admin_url( 'themes.php?page=yiw_panel' ),
		'meta'   => array( 'target' => '', 'title' => 'Maya Shop' . $wcaba_themesettings )
	);

}  // end-if Maya Shop


/**
 * Display link to active Sneakers Addict theme settings page (premium, by ColorLabs & Company)
 *
 * @since 2.1.0
 */
if ( ( $wcaba_stylesheet_name == 'Sneakers Addict' || get_template() == 'sneakersaddict' || get_template() == 'sneakers-addict' )
	&& current_user_can( 'edit_theme_options' )
) {

	$menu_items[ 'sneakersaddict-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Sneakers Addict' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=colabsthemes_options' ),
		'meta'   => array( 'target' => '', 'title' => 'Sneakers Addict' . $wcaba_themesettings )
	);

}  // end-if Sneakers Addict


/**
 * Display link to active Eureka theme settings page (premium, by ColorLabs & Company)
 *
 * @since 2.1.0
 */
if ( ( $wcaba_stylesheet_name == 'Eureka' || get_template() == 'eureka' )
	&& current_user_can( 'edit_theme_options' )
) {

	$menu_items[ 'eureka-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Eureka' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=colabsthemes_options' ),
		'meta'   => array( 'target' => '', 'title' => 'Eureka' . $wcaba_themesettings )
	);

}  // end-if Eureka


/**
 * Display link to active ShopDock theme settings page (premium, by Themify)
 *
 * @since 2.1.0
 */
if ( ( $wcaba_stylesheet_name == 'ShopDock' || get_template() == 'shopdock' )
	&& current_user_can( 'manage_options' )
) {

	$menu_items[ 'shopdock-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'ShopDock' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=themify' ),
		'meta'   => array( 'target' => '', 'title' => 'ShopDock' . $wcaba_themesettings )
	);

}  // end-if ShopDock


/**
 * Display link to active mag.Press eCommerce theme settings page (premium, by Obox Design)
 *
 * @since 2.3.0
 */
if ( ( $wcaba_stylesheet_name == 'mag.Press' || $wcaba_stylesheet_name == 'mag.Press eCommerce' || get_template() == 'magpress' )
	&& ( current_user_can( 'mange_options' ) || current_user_can( 'edit_themes' ) )
) {

	$menu_items[ 'magpress-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'mag.Press eCommerce' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=functions.php' ),
		'meta'   => array( 'target' => '', 'title' => 'mag.Press eCommerce' . $wcaba_themesettings )
	);

}  // end-if mag.Press eCommerce


/**
 * Display link to active Kiosk eCommerce theme settings page (premium, by Obox Design)
 *
 * @since 2.5.0
 */
if ( ( $wcaba_stylesheet_name == 'Kiosk' || get_template() == 'kiosk' )
	&& ( current_user_can( 'mange_options' ) || current_user_can( 'edit_themes' ) )
) {

	$menu_items[ 'kiosk-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Kiosk' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=functions.php' ),
		'meta'   => array( 'target' => '', 'title' => 'Kiosk' . $wcaba_themesettings )
	);

}  // end-if Kiosk eCommerce


/**
 * Display link to active Gigawatt eCommerce theme settings page (premium, by Obox Design)
 *
 * @since 2.5.0
 */
if ( ( $wcaba_stylesheet_name == 'Gigawatt' || $wcaba_stylesheet_name == 'Gigawatt eCommerce' || get_template() == 'gigawatt' )
	&& ( current_user_can( 'mange_options' ) || current_user_can( 'edit_themes' ) )
) {

	$menu_items[ 'gigawatt-settings' ] = array(
		'parent' => $woocommercebar,
		'title'  => 'Gigawatt eCommerce' . $wcaba_themesettings,
		'href'   => admin_url( 'admin.php?page=functions.php' ),
		'meta'   => array( 'target' => '', 'title' => 'Gigawatt eCommerce' . $wcaba_themesettings )
	);

}  // end-if Gigawatt eCommerce