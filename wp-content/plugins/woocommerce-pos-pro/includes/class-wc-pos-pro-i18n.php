<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that its ready for translation.
 *
 * @class 	  WC_POS_Pro_i18n
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_i18n {

  /**
   * Constructor
   */
  public function __construct() {
    $this->load_plugin_textdomain();
    add_filter( 'woocommerce_pos_language_packs_upgrade', array( $this, 'language_packs_upgrade' ), 10, 4 );
    add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download' ), 10, 3 );
    add_filter( 'woocommerce_pos_i18n', array( $this, 'payload' ) );
  }

  /**
   * Load the plugin text domain for translation.
   */
  public function load_plugin_textdomain() {

    $locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-pos-pro' );
    $dir = trailingslashit( WP_LANG_DIR );

    load_textdomain( 'woocommerce-pos-pro', $dir . 'woocommerce-pos/woocommerce-pos-pro-' . $locale . '.mo' );
    load_textdomain( 'woocommerce-pos-pro', $dir . 'plugins/woocommerce-pos-pro-' . $locale . '.mo' );

    // admin translations
    if ( is_admin() ) {
      load_textdomain( 'woocommerce-pos-pro', $dir . 'woocommerce-pos/woocommerce-pos-pro-admin-' . $locale . '.mo' );
      load_textdomain( 'woocommerce-pos-pro', $dir . 'plugins/woocommerce-pos-pro-admin-' . $locale . '.mo' );
    }

  }

  public function language_packs_upgrade( $transient, $response, $url, $force = false ) {
    $locale = get_locale();
    if ( !isset( $response->pro_locales->$locale ) ) {
      return $transient;
    }

    // compare
    $new = strtotime( $response->pro_locales->$locale );
    $options = get_option( 'woocommerce_pos_pro_language_packs' );

    if ( isset( $options[ $locale ] ) && $options[ $locale ] >= $new && !$force ) {
      return $transient;
    }

    // update required
    $transient->translations[] = array(
      'type'       => 'plugin',
      'slug'       => 'woocommerce-pos-pro',
      'language'   => $locale,
      'version'    => WC_POS_PRO_VERSION,
      'updated'    => date( 'Y-m-d H:i:s', $new ),
      'package'    => $url . 'packages/woocommerce-pos-pro-' . $locale . '.zip',
      'autoupdate' => 1
    );

    return $transient;
  }

  /**
   * Update the database with new language pack date
   * TODO: is there no later hook for translation install?
   *
   * @param $reply
   * @param $package
   * @param $upgrader
   *
   * @return mixed
   */
  public function upgrader_pre_download( $reply, $package, $upgrader ) {

    if ( isset( $upgrader->skin->language_update )
      && $upgrader->skin->language_update->slug == 'woocommerce-pos-pro'
    ) {

      $options = get_option( 'woocommerce_pos_pro_language_packs', array() );
      $locale = get_locale();
      $options[ $locale ] = current_time( 'timestamp' );
      update_option( 'woocommerce_pos_pro_language_packs', $options );
    }

    return $reply;
  }

  /**
   * @param $translations
   * @return array
   */
  static public function payload( $translations ) {

    // common

    // frontend
    if ( is_pos() ) {
      $translations[ 'titles' ][ 'edit-customer' ] = __( 'Edit Customer', 'woocommerce-pos-pro' );
      $translations[ 'titles' ][ 'add-customer' ] = __( 'Add New Customer', 'woocommerce-pos-pro' );
      /* translators: woocommerce */
      $translations[ 'messages' ][ 'no-orders' ] = __( 'No Orders found', 'woocommerce' );
      /* translators: woocommerce */
      $translations[ 'messages' ][ 'no-coupons' ] = __( 'No Coupons found', 'woocommerce' );

    } // admin
    else {
      /* translators: wordpress */
      $translations[ 'buttons' ][ 'activate' ] = __( 'Activate' );
      /* translators: wordpress */
      $translations[ 'buttons' ][ 'deactivate' ] = __( 'Deactivate' );

    }

    return $translations;
  }

}