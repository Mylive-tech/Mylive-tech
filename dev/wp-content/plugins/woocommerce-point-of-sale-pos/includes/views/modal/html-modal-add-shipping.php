<div class="overlay_order_popup full" id="add_shipping_overlay_popup" >
    <div class="overlay_order_popup_wrapper">
        <div class="media-frame-title">
            <h1><?php _e('Add Shipping', 'wc_point_of_sale'); ?></h1>
            <div class="clear"></div>
            <span class="close_popup"></span>
        </div>
        <div class="media-frame-wrap">
            <div class="two_cols">
                <div class="box_content col-1">
                <h3> <?php _e( 'Shipping method', 'wc_point_of_sale' ); ?> </h3>
                <table id="custom_shipping_table" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th class="shipping_title">
                                <?php _e('Title', 'wc_point_of_sale'); ?>
                            </th>
                            <th class="shipping_method">
                                <?php _e('Method', 'wc_point_of_sale'); ?>
                            </th>
                            <th class="shipping_price">
                                <?php _e('Price ('.get_woocommerce_currency_symbol().')', 'wc_point_of_sale'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="shipping_title"><input type="text" id="custom_shipping_title"></td>
                            <td class="shipping_method">
                               <select id="custom_shipping_method">
                                    <optgroup label="<?php _e( 'Shipping Method', 'woocommerce' ); ?>">
                                        <option value=""><?php _e( 'N/A', 'woocommerce' ); ?></option>
                                        <?php
                                            $shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();

                                            foreach ( $shipping_methods as $method ) {
                                                $current_method = $method->id;

                                                echo '<option value="' . esc_attr( $current_method ) . '" >' . esc_html( $method->get_title() ) . '</option>';
                                            }
                                        ?>
                                        <option value=""><?php _e( 'Other', 'woocommerce' ); ?></option>
                                    </optgroup>
                                </select>
                            </td>
                            <td class="shipping_price"><input type="text" id="custom_shipping_price"></td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <?php
                WC()->customer   = new WC_Customer();
                WC()->cart       = new WC_Cart();
                WC()->checkout   = new WC_Checkout();
                $countries       = new WC_Countries();
                $countries_arr   = $countries->get_allowed_countries();
                $state_arr       = $countries->get_allowed_country_states();
                $enable_shipping = get_option('woocommerce_calc_shipping');
                $checkout = new WC_Checkout();            
                ?>
                <div class="col-2">
                    <div class="custom-shipping-fields">
                        <h3> <?php _e( 'Shipping Address', 'woocommerce' ); ?> </h3>
                        <div>
                            <form onsubmit="return false;" id="update_customer_shipping_fields">
                            <input type="hidden" name="customer_details_id" id="custom_customer_details_id">
                            <?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

                            <?php foreach ( $checkout->checkout_fields['shipping'] as $key => $field ) : ?>

                                <?php woocommerce_form_field( 'custom_'.$key, $field, '' ); ?>

                            <?php endforeach; ?>

                            <?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="media-frame-footer">
            <button class="button button-primary wp-button-large alignright" id="add_custom_shipping"><?php _e('Add Shipping', 'wc_point_of_sale'); ?></button>
        </div>
    </div>
</div>