<?php

/* Continue Shopping Button */
if(isset($flatsome_opt['continue_shopping']) && $flatsome_opt['continue_shopping']){
    function flatsome_continue_shopping(){
     ?> 
     <a class="button-continue-shopping button alt-button small"  href="<?php echo wc_get_page_permalink( 'shop' ); ?>">
        &#8592; <?php echo __( 'Continue Shopping', 'woocommerce' ) ?></a> 
     <?php
    }

    add_action('woocommerce_after_cart_contents', 'flatsome_continue_shopping', 0);
    add_action('woocommerce_thankyou', 'flatsome_continue_shopping');
}



// Add HTML after Cart content
if($flatsome_opt['html_cart_footer']){
    function html_cart_footer(){
        global $flatsome_opt;
        echo do_shortcode($flatsome_opt['html_cart_footer']);
    }
    add_action( 'woocommerce_after_cart', 'html_cart_footer', 0);
}

if(!$flatsome_opt['coupon_checkout']){
    remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
}
