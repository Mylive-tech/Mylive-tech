<!-- Payments Popup Box -->
<div class="overlay_order_popup" id="overlay_order_payment">
    <div  class="overlay_order_popup_wrapper">
    <div id="order_payment_popup" class="payment_popup_background_none">
        <div id="payment">
            <div class="media-frame-menu">
                <div class="media-menu">
                <?php
                    WC()->customer = new WC_Customer();
                    WC()->cart = new WC_Cart();

                    delete_user_meta( get_current_user_id(), '_stripe_customer_id' );

                    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
                    $enabled_gateways   = get_option( 'pos_enabled_gateways', array() ); 
                    $title = __('Payment', 'woocommerce');
                    if (!empty($available_gateways)) {
                        current($available_gateways)->set_current();
                        $i = 0;
                        foreach ($available_gateways as $gateway) {
                            if ( !in_array($gateway->id, $enabled_gateways) ) continue;
                            $class = '';
                            if($i == 0){
                                $title = $gateway->get_title();
                                $class = 'active';
                            }
                            $i++;
                            ?>
                            <input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="select_payment_method" name="payment_method" value="<?php echo esc_attr($gateway->id); ?>"  style="display: none;"/>
                            <a href="#<?php echo $gateway->id; ?>" class="<?php echo $class; ?> payment_methods payment_method_<?php echo $gateway->id; ?>" data-bind="<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?></a>
                            <?php
                        }
                    }
                    if( in_array('pos_chip_pin', $enabled_gateways) ){
                        $class = '';
                        if (empty($available_gateways))
                            $class = 'active';
                        ?>
                        <input id="payment_method_pos_chip_pin" type="radio" class="select_payment_method" name="payment_method" value="pos_chip_pin"  style="display: none;"/>
                        <a href="#pos_chip_pin" class="<?php echo $class; ?> payment_methods payment_method_pos_chip_pin" data-bind="pos_chip_pin">
                        <?php _e('Chip & PIN', 'wc_point_of_sale'); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>                
            </div>
            <div class="media-frame-title">
                <h1>
                    <?php echo $title; ?>
                </h1>
                <span class="close_popup"></span>
                <div class="topaytop">
                    <div class="topaytopin">
                        <span class="to-pay-total"><span id="show_total_amt"></span></span></div>
                        <input type="hidden" id="show_total_amt_inp">
                </div>
            </div>
<!--
           <div class="split_payments">
	           <ul>
		           <li><span class="split_payments_success tips" data-tip="Payment Complete"></span><span class="split_payments_title">Cheque</span><span class="split_payments_amount">£1,234.56</span></li>
		           <li><span class="split_payments_remove tips"  data-tip="Remove"></span><span class="split_payments_title">Cash</span><span class="split_payments_amount">£1,234.56</span></li>
	           </ul>
           </div>
-->
            <?php
            if (!empty($available_gateways)) {
                $i = 0;
                foreach ($available_gateways as $gateway) {                    
                ?>
                <div id="<?php echo $gateway->id; ?>" class="popup_section" style="<?php echo $i == 0 ? 'display: block;' : ''; ?>">
                    <div class="media-frame-wrap">
                        <div class="payment_box payment_method_<?php echo $gateway->id; ?>">
                        <?php
                        $i++;
                        if ($gateway->has_fields() || $gateway->get_description() ) {
                            $gateway->payment_fields();                            
                        } else {                        
                            if (!WC()->customer->get_country())
                                $no_gateways_message = __('Please fill in your details above to see available payment methods.', 'woocommerce');
                            else
                                $no_gateways_message = __('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce');

                            echo '<p>' . apply_filters('woocommerce_no_available_payment_methods_message', $no_gateways_message) . '</p>';
                        }
                        if($gateway->id == 'cod'){
                        ?>
                            <table class="tendered_change_cod">
                                <tr>
                                    <th class="amount_tendered"><label><?php _e('Tendered', 'wc_point_of_sale'); ?></label></th>
                                    <td class="amount_tendered"><input name="amount_pay"  id="amount_pay_cod" type="text" class="txtpopamtfild"/></td>
                                </tr>
                                <tr>
                                    <th class="amount_change"><label><?php _e('Change', 'wc_point_of_sale'); ?></label></th>
                                    <td class="amount_change"><input name="amount_change" id="amount_change_cod" type="text" class="txtpopamtfild" readonly="readonly"/></td>
                                </tr>
                            </table>
                            <div id="inline_amount_tendered"></div>
                            <span class="error_amount" style="color: #CC0000;"></span>
                        <?php
                        }
                        ?>
                        </div>
                    </div>
                </div>
                <?php
                } 
            }
            $enabled_gateways   = get_option( 'pos_enabled_gateways', array() );
            if( in_array('pos_chip_pin', $enabled_gateways) ){
                ?>
                <div id="pos_chip_pin" class="popup_section" style="<?php echo empty($available_gateways) ? 'display: block;' : ''; ?>">
                    <div class="media-frame-wrap">
                        <div class="payment_box payment_method_pos_chip_pin">
                            <p><?php _e('Please process the payment using your chip & PIN device. The reference/order number for this order is below.', 'wc_point_of_sale'); ?></p>
                        <table class="chip_and_pin_order_number">
                            <tr>
                                <th><?php _e('Order Number', 'wc_point_of_sale'); ?></th>
                                <td id="pos_chip_pin_order_id"></td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="media-frame-footer">
<!--
	            <div class="amount_remaining">
		            <span class="amount_remaining_title">Remaining</span>
		            <span class="amount_remaining_value">£1,234.56</span>
	            </div>
-->
                <input name="" type="button" class="button back_to_sale" value="<?php _e('Back', 'wc_point_of_sale'); ?>"/>
                <input name="" type="button" class="button-primary go_payment " value="<?php _e('Pay', 'wc_point_of_sale'); ?>"/>
                <div id="payment_switch_wrap">
                  <input type="checkbox" class="payment_switch" value="yes" name="payment_print_receipt" id="payment_switch" data-animate="true" data-label-text="<span class='payment_switch_label'></span>" data-on-text="Yes" data-off-text="No" <?php echo $data['settings']['print_receipt'] ? 'checked="true"' : '';  ?> >
                </div>
            </div>
        </div>
    </div>
    </div>
</div>