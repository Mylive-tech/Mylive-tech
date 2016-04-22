<?php
/**
 * Pro license settings
 *
 * @class 	  WooCommerce_POS_Pro_Settings_License
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */


class WC_POS_Pro_Admin_Settings_License extends WC_POS_Admin_Settings_Abstract {

  protected static $instance;

	public function __construct() {
		$this->id    = 'license';
		$this->label = __( 'Pro License', 'woocommerce-pos-pro' );

    $this->defaults = array(
      'email' => get_option( 'admin_email' )
    );
	}

  /**
   *
   */
  public function output() {
		include 'views/license.php';
	}

  /**
   * @param array $data
   * @return array|bool|void
   */
  public function set(array $data){
    $email    = isset($data['email']) ? $data['email'] : '' ;
    $key      = isset($data['key']) ? $data['key'] : '' ;
    $am       = new WC_POS_Pro_Admin_AM();
    $response = $am->activate_license($email, $key);

    if( is_wp_error($response) ){
      return $response;
    }

    $data['activated'] = true;
    return $this->_save($data);
  }

  /**
   * @param array $data
   * @return array|bool
   */
  private function _save(array $data){
    $data['updated_at'] = time(); // forces update_option to return true
    $updated = add_option( $this->option_name(), $data, '', 'no' );
    if(!$updated) {
      $updated = update_option( $this->option_name(), $data );
    }
    return $updated ? $data : false;
  }

  /**
   * Deactivate
   *
   * @return bool|mixed|void
   */
  public function delete(){
    $am       = new WC_POS_Pro_Admin_AM();
    $response = $am->deactivate_license();

    if( is_wp_error($response) ){
      delete_option( $this->option_name() );
    }

    return $this->get();
  }

}