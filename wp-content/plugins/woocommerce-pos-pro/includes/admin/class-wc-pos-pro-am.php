<?php

/**
 * API Manager
 *
 * @class     WooCommerce_POS_Pro_AM
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Admin_AM {

  private $domain;          // blog domain name

  // default properties
  private $plugin_name = WC_POS_PRO_PLUGIN_NAME;
  private $plugin_path = WC_POS_PRO_PLUGIN_FILE;
  private $text_domain = WC_POS_PRO_PLUGIN_NAME;
  private $software_id = 'WooCommerce POS Pro';
  private $upgrade_url = 'https://woopos.com.au';
  private $options_key = 'woocommerce_pos_settings_license';
  private $renew_url;
  private $admin_url   = 'admin.php?page=wc_pos_settings#license';
  private $version     = WC_POS_PRO_VERSION;
  private $extra = array();   // Used to send any extra information

  /**
   * @param array $args
   * @throws Exception
   */
  public function __construct( $args = array() ) {

    // init properties
    foreach( $args as $key => $value ) {
    $this->$key = $value;
    }

    if( empty( $this->software_id ) ) {
      return trigger_error('Software ID not set');
    }
    if( empty( $this->upgrade_url ) ) {
      return trigger_error('Upgrade URL not set');
    }
    if( empty( $this->options_key ) ) {
      $this->options_key = sanitize_key( str_replace( ' ', '_', $this->software_id ) ) . '_am';
    }
    if( empty( $this->renew_url ) )   {
      $this->renew_url = rtrim( $this->upgrade_url , '/' ) . '/my-account';
    }

    $this->domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() );

    $this->options = get_option( $this->options_key );

    if( ! isset($this->options['instance']) ){
      $this->setup_options();
    }

    // hooks
    add_action( 'admin_init', array( $this, 'admin_init' ) );
    add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins' ) );
    add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
  }

  /**
   *
   */
  public function admin_init() {
    if( ! isset($this->options['activated']) || ! $this->options['activated'] ){
      if( class_exists('WC_POS_Admin_Notices') ) {
        $message = sprintf( __( 'The %s License Key has not been activated, so the plugin is inactive!', $this->text_domain ), $this->software_id );
        $message .= sprintf( __( ' <a href="%s">Click here</a> to activate the license key and the plugin.', $this->text_domain ), admin_url( $this->admin_url ) );
        WC_POS_Admin_Notices::add( $message );
      }
    }
    $this->check_external_blocking();
  }

  /**
   * Set up API Manager options
   * This only runs if an instance id is not already set
   */
  private function setup_options() {
    // create unique instance id
    $this->options = array(
      'instance'  => wp_generate_password(),
      'key'       => '',
      'email'     => get_option( 'admin_email' ),
      'activated' => false
    );
    update_option( $this->options_key, $this->options );
  }

  /**
   * Check for external blocking constant
   * @return string
   */
  private function check_external_blocking() {
    // show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
    if( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {
      // check if our API endpoint is in the allowed hosts
      $host = parse_url( $this->upgrade_url, PHP_URL_HOST );
      if( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
        if( class_exists('WC_POS_Admin_Notices') ) {
          $message = sprintf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->text_domain ), $this->software_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' );
          WC_POS_Admin_Notices::add( $message );
        }
      }
    }
  }

  /**
   * WordPress is checking plugins for updates
   * @param $transient
   * @return
   */
  public function update_plugins( $transient ) {
   if ( empty( $transient->checked ) )
    return $transient;

    $args = array(
      'request'           => 'pluginupdatecheck',
      'api_key'           => $this->options['key'],
      'activation_email'  => $this->options['email'],
      'extra'             => $this->extra
    );

    $result = $this->server_request($args);

    if( !is_wp_error( $result ) &&
      version_compare( $result->new_version, $this->version, '>' ) ){
      $result->plugin = $this->plugin_path;
      $transient->response[$this->plugin_path] = $result;
    }

   return $transient;
  }

  /**
   * Show plugin information on WordPress Updates page
   * @param $false
   * @param $action
   * @param $a
   * @return object|bool
   */
  public function plugins_api( $false, $action, $a ) {
   if( !isset( $a->slug ) || ($a->slug != $this->plugin_name) )
    return $false;

    $args = array(
      'request'           => 'plugininformation',
      'api_key'           => $this->options['key'],
      'activation_email'  => $this->options['email'],
      'extra'             => $this->extra
    );

    return $this->server_request($args);
  }

  /**
   * @param $email
   * @param $key
   * @return array|mixed|WP_Error
   */
  public function activate_license($email, $key) {
    $args = array(
      'request'     => 'activation',
      'email'       => $email,
      'licence_key' => $key
    );
    return $this->server_request($args);
  }

  /**
   * @return array|mixed|WP_Error
   */
  public function deactivate_license() {
    $args = array(
      'request'     => 'deactivation',
      'email'       => $this->options['email'],
      'licence_key' => $this->options['key']
    );
    $result = $this->server_request($args);

    if(!is_wp_error( $result )){
      $this->setup_options();
    }

    return $result;
  }

//  private function activation_status(){
//    $result = $this->server_request(array('request' => 'status'));
//  }

  /**
   * @param $args
   * @return array|mixed|WP_Error
   */
  private function server_request($args){
    $defaults = array(
      'plugin_name'       => $this->plugin_name,
      'slug'              => $this->plugin_name,
      'product_id'        => $this->software_id,
      'instance'          => $this->options['instance'],
      'software_version'  => $this->version,
      'domain'            => $this->domain,
      'platform'          => $this->domain,
      'timestamp'         => time() // cache breaker
    );
    $args = wp_parse_args( $args, $defaults );
    if( $args['request'] === 'activation' || $args['request'] === 'deactivation' ){
      $api = 'am-software-api';
    } else {
      $api = 'upgrade-api';
    }
    $upgrade_url = add_query_arg( 'wc-api', $api, $this->upgrade_url );
    $target_url = esc_url_raw( $upgrade_url . '&' . http_build_query( $args ) );

    // default wp_remote_get is 5 seconds
    // force to 5 seconds to counter issue with some users where timeout < 5
    $request = wp_remote_get( $target_url, array('timeout' => 5) );

    if( is_wp_error( $request ) ){
      return $request;
    }

    if( wp_remote_retrieve_response_code( $request ) != 200 ){
      return new WP_Error(
        'woocommerce_pos_pro_connection_error',
        __('Unable to connect to the activation server', 'woocommerce-pos-pro'),
        array( 'status' => wp_remote_retrieve_response_code( $request ) )
      );
    }

    return $this->parse_response($request);
  }

  /**
   * @param $request
   * @return array|mixed|WP_Error
   */
  private function parse_response($request){
    $body = trim( wp_remote_retrieve_body( $request ) );

    // json response
    if( strstr(wp_remote_retrieve_header( $request, 'content-type' ),'json') ){
      $result = json_decode( $body );
      if( isset($result->code) ){
        return $this->activation_error_handler($result->code);
      }
      return $result;
    }

    // serialized response
    $result = unserialize($body);
    if( is_object( $result )){
      if( isset($result->errors) ){
        $this->upgrade_error_handler( array_keys( $result->errors ) );
      }
      return $result;
    }

    return new WP_Error(
      'woocommerce_pos_pro_parse_error',
      __('Unable to parse the server response', 'woocommerce-pos-pro'),
      array( 'status' => 502 )
    );
  }

  /**
   *
   * @param $code
   * @return WP_Error
   */
  private function activation_error_handler($code = ''){
    $messages = array(
      '100' => __('Invalid Request', 'woocommerce-pos-pro'),
//      '100' => __('Invalid Email', 'woocommerce-pos-pro'), ?? multiple messages, same code
      '101' => __('Invalid License Key', 'woocommerce-pos-pro'),
      '102' => __('Activation error', 'woocommerce-pos-pro'),
      '103' => __('Exceeded maximum number of activations', 'woocommerce-pos-pro'),
      '104' => __('Invalid Instance ID', 'woocommerce-pos-pro'),
      '105' => __('Invalid License Key', 'woocommerce-pos-pro'),
      '106' => __('Subscription is not active', 'woocommerce-pos-pro'),
    );

    $message = isset($messages[$code]) ? $messages[$code] : __('Invalid Response', 'woocommerce-pos-pro');

    return new WP_Error(
      'woocommerce_pos_pro_activation_error',
      $message,
      array( 'status' => 400 )
    );
  }

  /**
   *
   * @param array $keys
   */
  private function upgrade_error_handler( $keys = array() ){
    if( ! class_exists('WC_POS_Admin_Notices') )
      return;

    $messages = array(
      'no_key'                 => sprintf( __( 'A license key for %s could not be found. Maybe you forgot to enter a license key when setting up %s, or the key was deactivated in your account. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->software_id, $this->renew_url ),
      'exp_license'            => sprintf( __( 'The license key for %s has expired. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'hold_subscription'      => sprintf( __( 'The subscription for %s is on-hold. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'cancelled_subscription' => sprintf( __( 'The subscription for %s has been cancelled. You can renew the subscription from your account <a href="%s" target="_blank">dashboard</a>. A new license key will be emailed to you after your order has been completed.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'exp_subscription'       => sprintf( __( 'The subscription for %s has expired. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'suspended_subscription' => sprintf( __( 'The subscription for %s has been suspended. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'pending_subscription'   => sprintf( __( 'The subscription for %s is still pending. You can check on the status of the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'trash_subscription'     => sprintf( __( 'The subscription for %s has been placed in the trash and will be deleted soon. You can purchase a new subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'no_subscription'        => sprintf( __( 'A subscription for %s could not be found. You can purchase a subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'no_activation'          => sprintf( __( '%s has not been activated. Go to the settings page and enter the license key and license email to activate %s.', $this->text_domain ), $this->software_id, $this->software_id ),
      'download_revoked'       => sprintf( __( 'Download permission for %s has been revoked possibly due to a license key or subscription expiring. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url ),
      'switched_subscription'  => sprintf( __( 'You changed the subscription for %s, so you will need to enter your new API License Key in the settings page. The License Key should have arrived in your email inbox, if not you can get it by logging into your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ), $this->software_id, $this->renew_url )
    );

    foreach( $messages as $key => $message ):
      if( in_array( $key, $keys ) ):
        WC_POS_Admin_Notices::add( $message );
      endif;
    endforeach;
  }

}