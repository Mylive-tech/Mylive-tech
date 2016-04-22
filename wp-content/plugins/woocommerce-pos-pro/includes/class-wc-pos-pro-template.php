<?php

/**
 * POS Pro Template
 *
 * @class    WC_POS_Pro_Template
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_Template {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_pos_templates', array( $this, 'templates' ) );
    add_filter( 'woocommerce_pos_enqueue_head_css', array( $this, 'enqueue_head_css' ) );
    add_filter( 'woocommerce_pos_enqueue_footer_js', array( $this, 'enqueue_footer_js' ) );
  }

  /**
   * @param $styles
   * @return mixed
   */
  public function enqueue_head_css( $styles ) {
		$styles['pro-css'] = WC_POS_PRO_PLUGIN_URL .'assets/css/pro.min.css?ver='. WC_POS_PRO_VERSION;
		return $styles;
	}

  /**
   * @param $scripts
   * @return array|bool
   */
  public function enqueue_footer_js( $scripts ) {
    $build = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'build' : 'min';
    $script = WC_POS_PRO_PLUGIN_URL .'assets/js/app.'. $build .'.js?ver='. WC_POS_PRO_VERSION;
    return $this->array_insert_after( 'app', $scripts, 'pro', $script );
  }

  /**
   * TODO: woocommerce_admin_* filters will only fire in admin
   * TODO: perhaps make pos think it's wp admin?
   * TODO: or perhaps use woocommerce_billing_fields filter
   * @param string $type
   * @return mixed|void
   */
  static public function customer_fields($type = 'billing'){

    if($type == 'billing'){
      return apply_filters( 'woocommerce_admin_billing_fields', array(
        'first_name' => array('label' => /* translators: woocommerce */ __( 'First Name', 'woocommerce' )),
        'last_name'  => array('label' => /* translators: woocommerce */ __( 'Last Name', 'woocommerce' )),
        'company'    => array(
          'label'   => /* translators: woocommerce */ __( 'Company', 'woocommerce' ),
          'colspan' => 2
        ),
        'address_1'  => array('label' => /* translators: woocommerce */ __( 'Address 1', 'woocommerce' )),
        'address_2'  => array('label' => /* translators: woocommerce */ __( 'Address 2', 'woocommerce' )),
        'city'       => array('label' => /* translators: woocommerce */ __( 'City', 'woocommerce' )),
        'postcode'   => array('label' => /* translators: woocommerce */ __( 'Postcode', 'woocommerce' )),
        'country'    => array('label' => /* translators: woocommerce */ __( 'Country', 'woocommerce' )),
        'state'      => array('label' => /* translators: woocommerce */ __( 'State/County', 'woocommerce' )),
        'email'      => array('label' => /* translators: woocommerce */ __( 'Email', 'woocommerce' )),
        'phone'      => array('label' => /* translators: woocommerce */ __( 'Phone', 'woocommerce' )),
      ));
    }

    if($type == 'shipping'){
      return apply_filters( 'woocommerce_admin_shipping_fields', array(
        'first_name' => array('label' => /* translators: woocommerce */ __( 'First Name', 'woocommerce' )),
        'last_name'  => array('label' => /* translators: woocommerce */ __( 'Last Name', 'woocommerce' )),
        'company'    => array(
          'label'   => /* translators: woocommerce */ __( 'Company', 'woocommerce' ),
          'colspan' => 2
        ),
        'address_1'  => array('label' => /* translators: woocommerce */ __( 'Address 1', 'woocommerce' )),
        'address_2'  => array('label' => /* translators: woocommerce */ __( 'Address 2', 'woocommerce' )),
        'city'       => array('label' => /* translators: woocommerce */ __( 'City', 'woocommerce' )),
        'postcode'   => array('label' => /* translators: woocommerce */ __( 'Postcode', 'woocommerce' )),
        'country'    => array('label' => /* translators: woocommerce */ __( 'Country', 'woocommerce' )),
        'state'      => array('label' => /* translators: woocommerce */ __( 'State/County', 'woocommerce' )),
      ));
    }

  }

  /**
   * Returns the partials directory
   * @return string
   */
  static public function get_template_dir(){
    return realpath( WC_POS_PRO_PLUGIN_PATH . 'includes/views' );
  }

  /**
   * @param $templates
   * @return mixed
   */
  public function templates($templates){
    $pro_templates = WC_POS_Template::create_templates_array( self::get_template_dir() );
    return array_replace_recursive( $templates, $pro_templates );
  }

  /*
   * Inserts a new key/value before the key in the array.
   *
   * @param $key
   *   The key to insert before.
   * @param $array
   *   An array to insert in to.
   * @param $new_key
   *   The key to insert.
   * @param $new_value
   *   An value to insert.
   *
   * @return
   *   The new array if the key exists, FALSE otherwise.
   *
   * @see array_insert_after()
   */
  private function array_insert_before($key, array &$array, $new_key, $new_value){
    if ( array_key_exists( $key, $array ) ) {
      $new = array();
      foreach ($array as $k => $value) {
        if ($k === $key) {
          $new[$new_key] = $new_value;
        }
        $new[$k] = $value;
      }
      return $new;
    }
    return FALSE;
  }

  /*
   * Inserts a new key/value after the key in the array.
   *
   * @param $key
   *   The key to insert after.
   * @param $array
   *   An array to insert in to.
   * @param $new_key
   *   The key to insert.
   * @param $new_value
   *   An value to insert.
   *
   * @return
   *   The new array if the key exists, FALSE otherwise.
   *
   * @see array_insert_before()
   */
  private function array_insert_after($key, array &$array, $new_key, $new_value) {
    if (array_key_exists($key, $array)) {
      $new = array();
      foreach ($array as $k => $value) {
        $new[$k] = $value;
        if ($k === $key) {
          $new[$new_key] = $new_value;
        }
      }
      return $new;
    }
    return FALSE;
  }

}