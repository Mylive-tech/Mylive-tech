<?php

class FPRacCoupon {

    public static function rac_create_coupon($email, $timestamp) {

        $getdatas = (array) get_option('rac_coupon_for_user');
        if (isset($getdatas[$email])) {
            $getcoupon = $getdatas[$email]['coupon_code'];
            $coupon_object = new WC_Coupon($getcoupon);
            if (($coupon_object->expiry_date && current_time('timestamp') <= $coupon_object->expiry_date) && ($coupon_object->exists) && ($coupon_object->usage_limit > 0 && $coupon_object->usage_count < $coupon_object->usage_limit)) {
                return $getcoupon;
            } else {
                $array_email_update = 'update';
                return self::create_new_coupon($email, $timestamp, $array_email_update);
            }
        } else {
            $array_email_update = 'new';
            return self::create_new_coupon($email, $timestamp, $array_email_update);
        }
    }

    public static function create_new_coupon($email, $timestamp, $array_email_update) {

        if (get_option('rac_prefix_coupon') == '1') {
            $afterexplode = explode('@', $email);
            $email_letters = $afterexplode[0];
            $coupon_code = $email_letters . $timestamp;
        } else {
            $manual_prefix = get_option('rac_manual_prefix_coupon_code');
            $coupon_code = $manual_prefix . $timestamp;
        }
        $amount = get_option('rac_coupon_value');
        $discount_type = get_option('rac_coupon_type');
        $time_now = time();
        $validity_time = get_option('rac_coupon_validity') * 24 * 60 * 60;
        $expire_time = $time_now + $validity_time;
        $expire_date = date("Y-m-d", $expire_time); //formating expire date

        $coupon = array(
            'post_title' => $coupon_code,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );

        $new_coupon_id = wp_insert_post($coupon);

        $minimum_amount = get_option('rac_minimum_spend');
        $maximum_amount = get_option('rac_maximum_spend');


        if (!is_array(get_option('rac_include_products_in_coupon'))) {
            $allowproducts = explode(',', get_option('rac_include_products_in_coupon'));
        } else {
            $allowproducts = get_option('rac_include_products_in_coupon');
        }

        if (!is_array(get_option('rac_exclude_products_in_coupon'))) {
            $excluded_products = explode(',', get_option('rac_exclude_products_in_coupon'));
        } else {
            $excluded_products = explode(',', get_option('rac_exclude_products_in_coupon'));
        }

        if (!is_array(get_option('rac_select_category_to_enable_redeeming'))) {
            $allowcategory = explode(',', get_option('rac_select_category_to_enable_redeeming'));
        } else {
            $allowcategory = (array) get_option('rac_select_category_to_enable_redeeming');
        }

        if (!is_array(get_option('rac_exclude_category_to_enable_redeeming'))) {
            $excludecategory = explode(',', get_option('rac_exclude_category_to_enable_redeeming'));
        } else {
            $excludecategory = (array) get_option('rac_exclude_category_to_enable_redeeming');
        }

        update_post_meta($new_coupon_id, 'discount_type', $discount_type);
        update_post_meta($new_coupon_id, 'coupon_amount', $amount);
        update_post_meta($new_coupon_id, 'individual_use', 'no');
        update_post_meta($new_coupon_id, 'product_ids', array_filter(array_map('intval', $allowproducts)));
        update_post_meta($new_coupon_id, 'exclude_product_ids', array_filter(array_map('intval', $excluded_products)));
        update_post_meta($new_coupon_id, 'product_categories', array_filter(array_map('intval', $allowcategory)));
        update_post_meta($new_coupon_id, 'exclude_product_categories', array_filter(array_map('intval', $excludecategory)));
        update_post_meta($new_coupon_id, 'usage_limit', '1'); //this is must to avoid multiple usage
        update_post_meta($new_coupon_id, 'expiry_date', $expire_date);
        update_post_meta($new_coupon_id, 'apply_before_tax', 'yes');
        update_post_meta($new_coupon_id, 'free_shipping', 'no');
        update_post_meta($new_coupon_id, 'minimum_amount', $minimum_amount);
        update_post_meta($new_coupon_id, 'maximum_amount', $maximum_amount);

        $get_previous_data = (array) get_option('rac_coupon_for_user');
        if ($array_email_update == 'update') {
            $get_previous_data[$email]['coupon_code'] = $coupon_code;
            update_option('rac_coupon_for_user', (array) $get_previous_data);
        } else {
            // For New Coupon Creation
            $newdata = array();
            $newdata[$email] = array('coupon_code' => $coupon_code);
            $merge_data = array_merge((array) $get_previous_data, (array) $newdata);
            $array_unique_filter = array_filter(array_unique((array) $merge_data));
            update_option('rac_coupon_for_user', $array_unique_filter);
        }

        if (update_post_meta($new_coupon_id, 'coupon_by_rac', 'yes')) {
            return $coupon_code;
        }
    }

}
