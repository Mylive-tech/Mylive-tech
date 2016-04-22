<?php
/**
 * Update to 0.4
 * - update license options
 *
 * @version   0.4
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$activated  = get_option('woocommerce_pos_pro_activated');
$instance   = get_option('woocommerce_pos_pro_instance');
$license    = get_option('woocommerce_pos_pro');

if($activated === 'Activated'){
  $data = array(
    'instance'  => $instance,
    'activated' => true,
    'key'       => isset($license['key']) ? $license['key'] : '',
    'email'     => isset($license['activation_email']) ? $license['activation_email'] : '',
  );

  delete_option( 'woocommerce_pos_pro_activated' );
  delete_option( 'woocommerce_pos_pro_instance' );
  delete_option( 'woocommerce_pos_pro' );
}