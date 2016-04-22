<?php
/**
 * @class 	  WC_POS_Pro_Gateways
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

  class WC_POS_Pro_Gateways {

    private $stripe_gateways = array(
       'stripe',  // WC_Stripe: http://www.woothemes.com/products/stripe/
       'Striper', // Stripe: https://wordpress.org/plugins/striper/
       's4wc'     // S4WC_Gateway: https://wordpress.org/plugins/stripe-for-woocommerce/
    );

    private $publishable_key;

    /**
     * Constructor
     */
    public function __construct() {
      add_filter( 'woocommerce_pos_load_gateway', array($this, 'load_gateway'), 20, 1 );

      // @todo: refactor this
      $gateways = WC_Payment_Gateways::instance()->payment_gateways;
      foreach($gateways as $gateway){
        // enqueue stripe.js for $stripe_gateways
        if( in_array($gateway->id, $this->stripe_gateways) ){
          $this->publishable_key = $this->stripe_publishable_key( $gateway );
          add_filter( 'woocommerce_pos_enqueue_footer_js', array( $this, 'stripe_js' ) );
        }
      }
    }

    /**
     * @param WC_Payment_Gateway $gateway
     * @return WC_Payment_Gateway $gateway
     */
    public function load_gateway(WC_Payment_Gateway $gateway){

      // disable Stripe checkout modal
      if( $gateway->id == 'stripe' ){
        $gateway->stripe_checkout = false;
      }

      $gateway->pos = true;
      return $gateway;
    }

    /**
     * @param $scripts
     * @return array
     */
    public function stripe_js ( $scripts ) {
      $stripe_js = array(
        'stripe-js' => '<script src="https://js.stripe.com/v2/"></script>',
        'stripe-key' => '<script type="text/javascript">Stripe.setPublishableKey("'. $this->publishable_key .'");</script>'
      );
      return $stripe_js + $scripts;
    }

    private function stripe_publishable_key(WC_Payment_Gateway $gateway){

      // woocommerce stripe
      if( isset( $gateway->publishable_key ) )
        return $gateway->publishable_key;

      // s4wc
      if( array_key_exists('s4wc', $GLOBALS)  )
        return $GLOBALS['s4wc']->settings['publishable_key'];

      // striper
      if( $gateway->id == 'Striper' && isset( $gateway->settings['test_publishable_key'] ) && isset( $gateway->settings['live_publishable_key'] ) ){
        $settings = get_option( 'woocommerce_Striper_settings' );
        return isset( $settings['debug'] ) && $settings['debug'] == 'yes' ?
          $gateway->settings['test_publishable_key'] :
          $gateway->settings['live_publishable_key'];
      }

    }

  }