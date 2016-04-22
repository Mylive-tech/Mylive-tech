<?php

/**
 * Global helper functions for WooCommerce POS Pro
 *
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 *
 */

/**
 *
 */
function wc_pos_pro_activated() {
  $options = get_option('woocommerce_pos_settings_license');
  if(isset($options['activated'])){
    return $options['activated'];
  }
  return false;
}