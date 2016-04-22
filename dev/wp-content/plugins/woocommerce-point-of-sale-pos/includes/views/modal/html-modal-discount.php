<div class="overlay_order_popup" id="overlay_order_discount">
    <div  class="overlay_order_popup_wrapper">
    <div id="order_discount_popup">
        <div class="media-frame-menu">
            <div class="media-menu">
                <a href="#discount_tab" class="active discount_modal"><?php _e('Discount', 'woocommerce'); ?></a>
                <a href="#coupon_tab" class="coupon_modal"><?php _e('Coupon', 'woocommerce'); ?></a>
            </div>                
        </div>
        <div class="media-frame-title">
            <h1><?php _e('Discount', 'wc_point_of_sale'); ?></h1>
            <span class="close_popup"></span>
        </div>
        <div id="discount_tab" class="discount_section popup_section" style="display: block;">
            <div class="media-frame-wrap">

                    <input type="hidden" id="order_discount_prev" value="<?php echo ($order->get_total_discount() > 0 ) ? $order->get_total_discount() : ''; ?>">

                    <div id="inline_order_discount"></div>

                    <input type="hidden" id="order_discount_symbol" value="currency_symbol">
                    <p><?php _e('This is the total discount (% or '.get_woocommerce_currency_symbol().') applied after tax.', 'wc_point_of_sale'); ?></p>

            </div>
            <div class="media-frame-footer">
                <button class="button" type="button" id="clear_order_discount"><?php _e('Clear', 'wc_point_of_sale'); ?></button>
                <button class="button button-primary" type="button" id="save_order_discount"><?php _e('Add Discount', 'wc_point_of_sale'); ?></button>
            </div>
        </div>

        <div id="coupon_tab" class="discount_section popup_section">
            <div class="media-frame-wrap">
                <input id="coupon_code" class="input-text" type="text" placeholder="<?php _e('Coupon code', 'wc_point_of_sale'); ?>" value="" name="coupon_code">
                <button class="button" type="button" name="apply_coupon" id="apply_coupon_btn"><?php _e('Apply Coupon', 'wc_point_of_sale'); ?></button>

                <div class="messages"></div>
            </div>
            <div class="media-frame-footer">
                <button class="button back_to_sale" type="button"><?php _e('Back', 'wc_point_of_sale'); ?></button>
            </div>

        </div>

    </div>
    </div>
</div>