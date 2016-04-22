<!-- Add New Customer Popup box -->
<div class="overlay_order_popup" id="overlay_order_customer" style="display: block; visibility: hidden;">
    <div id="order_customer_popup" class="woocommerce">
            <?php
            WC()->customer   = new WC_Customer();
            WC()->cart       = new WC_Cart();
            WC()->checkout   = new WC_Checkout();
            $countries       = new WC_Countries();
            $countries_arr   = $countries->get_allowed_countries();
            $state_arr       = $countries->get_allowed_country_states();
            $enable_shipping = get_option('woocommerce_calc_shipping');

            ?>
            <form id="add_wc_pos_customer" class="validate" action="" method="post" autocomplete="off">
            <div class="media-frame-title">
                <h1>Customer Details</h1>
            </div>
            <div id="customer_details" class="col3-set <?php if($enable_shipping != 'yes') echo 'two_cols'; ?>">
                <input type="hidden" id="customer_details_id" name="customer_details_id">
                <div id="error_in_customer">
                    <p></p>
                </div>
                <div class="col-1">
                    <div class="woocommerce-billing-fields">
                            <h3><?php _e( 'Billing Details', 'woocommerce' ); ?></h3>
                        

                        <?php 
                        $checkout = new WC_Checkout();
                        do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

                        <?php foreach ( $checkout->checkout_fields['billing'] as $key => $field ) : ?>

                            <?php woocommerce_form_field( $key, $field, '' ); ?>

                        <?php endforeach; ?>

                        <?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>
                        <p class="form-row form-row-wide create-account">
                            <input class="input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce' ); ?></label>
                        </p>
                    </div>
                    
                </div>
                <div class="col-2">
                    <div class="woocommerce-shipping-fields">

                            <?php
                                if ( empty( $_POST ) ) {

                                    $ship_to_different_address = get_option( 'woocommerce_ship_to_billing' ) === 'no' ? 1 : 0;
                                    $ship_to_different_address = apply_filters( 'woocommerce_ship_to_different_address_checked', $ship_to_different_address );

                                } else {

                                    $ship_to_different_address = $checkout->get_value( 'ship_to_different_address' );

                                }
                            ?>

                            <h3 id="ship-to-different-address">
	                            <?php _e( 'Shipping Details', 'woocommerce' ); ?>
                            </h3>
                                <label for="ship-to-different-address-checkbox" class="checkbox"></label>
                                <input id="ship-to-different-address-checkbox" class="input-checkbox" <?php checked( $ship_to_different_address, 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" />
                            <div class="shipping_address">
                                <p class="billing-same-as-shipping">
                                    <a id="billing-same-as-shipping" class="tips billing-same-as-shipping" data-tip="<?php _e( 'Copy from billing', 'woocommerce' ); ?>"></a>
                                </p>

                                <?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

                                <?php foreach ( $checkout->checkout_fields['shipping'] as $key => $field ) : ?>

                                    <?php woocommerce_form_field( $key, $field, '' ); ?>

                                <?php endforeach; ?>

                                <?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

                            </div>
                    </div>
                </div>
                <div class="col-3">

                    <?php if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) ) : ?>
                        
                        <div class="woocommerce-additional-fields">

                        <?php if ( ! WC()->cart->needs_shipping() || WC()->cart->ship_to_billing_address_only() ) : ?>

                            <h3><?php _e( 'Additional Information', 'woocommerce' ); ?></h3>

                        <?php endif; ?>

                        <?php foreach ( $checkout->checkout_fields['order'] as $key => $field ) : if( $key == 'order_comments') continue; ?>

                            <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

                        <?php endforeach; ?>

                        </div>

                    <?php endif; ?>
                    
                </div>                    
                <div class="clear"></div>
                
            </div>
            <div class="customer-save">
                <input type="button" name="submit" id="save_customer" class="button button-primary" value="Save Customer">
            </div>
            </form>

            <div class="clear"></div>

        <span class="close_popup"></span>
    </div>
</div>