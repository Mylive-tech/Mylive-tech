<?php

class FPRacCron {

    public static function fp_rac_cron_job_setting() {
        wp_clear_scheduled_hook('rac_cron_job');
        if (wp_next_scheduled('rac_cron_job') == false) {
            wp_schedule_event(time(), 'xhourly', 'rac_cron_job');
        }
    }

//on save clear and set the cron again
    public static function fp_rac_cron_job_setting_savings() {
        wp_clear_scheduled_hook('rac_cron_job');
        if (wp_next_scheduled('rac_cron_job') == false) {
            wp_schedule_event(time(), 'xhourly', 'rac_cron_job');
        }
    }

    public static function fp_rac_add_x_hourly($schedules) {

        $interval = get_option('rac_abandon_cron_time');
        if (get_option('rac_abandon_cart_cron_type') == 'minutes') {
            $interval = $interval * 60;
        } else if (get_option('rac_abandon_cart_cron_type') == 'hours') {
            $interval = $interval * 3600;
        } else if (get_option('rac_abandon_cart_cron_type') == 'days') {
            $interval = $interval * 86400;
        }
        $schedules['xhourly'] = array(
            'interval' => $interval,
            'display' => 'X Hourly'
        );
        return $schedules;
    }

    public static function get_rac_formatprice($price) {
        if (function_exists('woocommerce_price')) {
            return woocommerce_price($price);
        } else {
            if (function_exists('wc_price')) {
                return wc_price($price);
            }
        }
    }

    public static function fp_rac_cron_job_mailing() {
        global $wpdb;
        global $woocommerce;
        global $to;
        $emailtemplate_table_name = $wpdb->prefix . 'rac_templates_email';
        $abandancart_table_name = $wpdb->prefix . 'rac_abandoncart';
        $email_templates = $wpdb->get_results("SELECT * FROM $emailtemplate_table_name"); //all email templates
        $abandon_carts = $wpdb->get_results("SELECT * FROM $abandancart_table_name WHERE cart_status='ABANDON' AND user_id!='0' AND completed IS NULL"); //Selected only cart which are not completed
// For Members

        foreach ($abandon_carts as $each_cart) {
            foreach ($email_templates as $emails) {
                if ($emails->status == "ACTIVE") {


                    $cart_array = maybe_unserialize($each_cart->cart_details);
                    $tablecheckproduct = "<table style='width:100%;border:1px solid #eee;'><thead><tr>";
                    if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                        $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_name', $each_cart->wpml_lang, get_option('rac_product_info_product_name')) . "</th>";
                    }
                    if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                        $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_image', $each_cart->wpml_lang, get_option('rac_product_info_product_image')) . "</th>";
                    }
                    if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                        $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_price', $each_cart->wpml_lang, get_option('rac_product_info_product_price')) . "</th>";
                    }
                    $tablecheckproduct .= "</tr></thead><tbody>";
                    if (is_array($cart_array)) {

                        foreach ($cart_array as $cart) {

                            foreach ($cart as $inside) {

                                foreach ($inside as $product) {
                                    if ((float) $woocommerce->version <= (float) ('2.0.20')) {
                                        $objectproduct = get_product($product['product_id']);
                                        $objectproductvariable = get_product($product['variation_id']);
                                    } else {
                                        $objectproduct = new WC_Product($product['product_id']);
                                        $objectproductvariable = new WC_Product_Variation($product['variation_id']);
                                    }
                                    $tablecheckproduct .= "<tr>";
                                    if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                                        $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_title($product['product_id']) . "</td>";
                                    }
                                    if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                                        $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_post_thumbnail($product['product_id'], array(90, 90)) . "</td>";
                                    }
                                    if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                                        $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . self::get_rac_formatprice($product['variation_id'] == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";
                                    }
                                    $tablecheckproduct .= "</tr>";
                                }
                            }
                        }
                        /* $tablecheckproduct .= "It is from Array";
                          $dump = print_r($cart_array, true);
                          $tablecheckproduct .= $dump; */
                    } elseif (is_object($cart_array)) {
                        $order = new WC_Order($cart_array->id);
                        if ($order->user_id != '') {
                            foreach ($order->get_items() as $products) {
                                if ((float) $woocommerce->version <= (float) ('2.0.20')) {
                                    $objectproduct = get_product($products['product_id']);
                                    $objectproductvariable = get_product($products['variation_id']);
                                } else {
                                    $objectproduct = new WC_Product($products['product_id']);
                                    $objectproductvariable = new WC_Product_Variation($products['variation_id']);
                                }
                                $tablecheckproduct .= "<tr>";
                                if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_title($products['product_id']) . "</td>";
                                }
                                if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_post_thumbnail($products['product_id'], array(90, 90)) . "</td>";
                                }
                                if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . self::get_rac_formatprice($products['variation_id'] == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";
                                }

                                $tablecheckproduct .= "</tr>";
                            }
                        }
                        /*  $tablecheckproduct .= "It is from Object";
                          $tablecheckproduct .= var_dump($cart_array); */
                    }
                    $tablecheckproduct .= "</table>";



                    if (get_option('rac_email_use_members') == 'yes') {


                        if (empty($each_cart->mail_template_id)) { // IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE
                            if ($emails->sending_type == 'hours') {
                                $duration = $emails->sending_duration * 3600;
                            } else if ($emails->sending_type == 'minutes') {
                                $duration = $emails->sending_duration * 60;
                            } else if ($emails->sending_type == 'days') {
                                $duration = $emails->sending_duration * 86400;
                            }
                            //duration is finished
                            $cut_off_time = $each_cart->cart_abandon_time + $duration;
                            $current_time = current_time('timestamp');
                            if ($current_time > $cut_off_time) {
                                //$cart_url = $woocommerce->cart->get_cart_url();
                                //$objectcart = new WC_Cart();
                                @$cart_url = WC_Cart::get_cart_url();

                                $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id), $cart_url));

                                if (get_option('rac_cart_link_options') == '1') {
                                    $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                } elseif (get_option('rac_cart_link_options') == '2') {
                                    $url_to_click = $url_to_click;
                                } else {
                                    $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                    $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                }
                                $user = get_userdata($each_cart->user_id);
                                $to = $user->user_email;
                                $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                $firstname = $user->user_firstname;
                                $lastname = $user->user_lastname;


                                $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                $message = str_replace('{rac.firstname}', $firstname, $message);
                                $message = str_replace('{rac.lastname}', $lastname, $message);
                                $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                                if (strpos($message, "{rac.coupon}")) {
                                    $coupon_code = FPRacCoupon::rac_create_coupon($user->user_email, $each_cart->cart_abandon_time);
                                    $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                }
                                add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                $message = do_shortcode($message); //shortcode feature
                                if (get_option('rac_email_use_temp_plain') != 'yes') {
                                    ob_start();
                                    if (function_exists('wc_get_template')) {
                                        wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                        echo $message;
                                        wc_get_template('emails/email-footer.php');
                                    } else {
                                        woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                        echo $message;
                                        woocommerce_get_template('emails/email-footer.php');
                                    }
                                    $woo_temp_msg = ob_get_clean();
                                } else {
                                    $woo_temp_msg = $message;
                                }
                                $headers = "MIME-Version: 1.0\r\n";
                                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                if ($emails->sender_opt == 'local') {
                                    $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                    $headers .= "Reply-To: " . $emails->from_name . "<" . $emails->from_email . ">\r\n";
                                } else {
                                    $headers .= self::rac_formatted_from_address_woocommerce();
                                    $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";
                                }
                                if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                    if ('wp_mail' == get_option('rac_trouble_mail')) {
                                        if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                            //  wp_mail($to, $subject, $message);
                                            $store_template_id = array($emails->id);
                                            $store_template_id = maybe_serialize($store_template_id);
                                            $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                            //add to mail log
                                            $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                            $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                            FPRacCounter::rac_do_mail_count();
                                        }
                                    } else {
                                        if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                            //  wp_mail($to, $subject, $message);
                                            $store_template_id = array($emails->id);
                                            $store_template_id = maybe_serialize($store_template_id);
                                            $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                            //add to mail log
                                            $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                            $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                            FPRacCounter::rac_do_mail_count();
                                        }
                                    }
                                }
                            }
                        }  // IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE END
                        elseif (!empty($each_cart->mail_template_id)) {
                            $sent_mail_templates = maybe_unserialize($each_cart->mail_template_id);

                            if (!in_array($emails->id, (array) $sent_mail_templates)) {
                                if ($emails->sending_type == 'hours') {
                                    $duration = $emails->sending_duration * 3600;
                                } else if ($emails->sending_type == 'minutes') {
                                    $duration = $emails->sending_duration * 60;
                                } else if ($emails->sending_type == 'days') {
                                    $duration = $emails->sending_duration * 86400;
                                }//duration is finished
                                $cut_off_time = $each_cart->cart_abandon_time + $duration;
                                $current_time = current_time('timestamp');
                                if ($current_time > $cut_off_time) {
                                    @$cart_url = WC_Cart::get_cart_url();
                                    $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id), $cart_url));

                                    if (get_option('rac_cart_link_options') == '1') {
                                        $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                    } elseif (get_option('rac_cart_link_options') == '2') {
                                        $url_to_click = $url_to_click;
                                    } else {
                                        $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                        $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                    }


                                    $user = get_userdata($each_cart->user_id);
                                    $to = $user->user_email;
                                    $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                    $firstname = $user->user_firstname;
                                    $lastname = $user->user_lastname;
                                    $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                    $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                    $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                    $message = str_replace('{rac.firstname}', $firstname, $message);
                                    $message = str_replace('{rac.lastname}', $lastname, $message);
                                    $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                                    if (strpos($message, "{rac.coupon}")) {
                                        $coupon_code = FPRacCoupon::rac_create_coupon($user->user_email, $each_cart->cart_abandon_time);
                                        $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                    }
                                    add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                    $message = do_shortcode($message); //shortcode feature
                                    if (get_option('rac_email_use_temp_plain') != 'yes') {
                                        ob_start();
                                        if (function_exists('wc_get_template')) {
                                            wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                            echo $message;
                                            wc_get_template('emails/email-footer.php');
                                        } else {
                                            woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                            echo $message;
                                            woocommerce_get_template('emails/email-footer.php');
                                        }
                                        $woo_temp_msg = ob_get_clean();
                                    } else {
                                        $woo_temp_msg = $message;
                                    }
                                    $headers = "MIME-Version: 1.0\r\n";
                                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                    if ($emails->sender_opt == 'local') {
                                        $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                        $headers .= "Reply-To: " . $emails->from_name . " <" . $emails->from_email . ">\r\n";
                                    } else {
                                        $headers .= self::rac_formatted_from_address_woocommerce();
                                        $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                    }
                                    if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                        if ('wp_mail' == get_option('rac_trouble_mail')) {

                                            if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                //wp_mail($to, $subject, $message);
                                                $sent_mail_templates[] = $emails->id;
                                                $store_template_id = maybe_serialize($sent_mail_templates);
                                                $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                //add to mail log
                                                $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                FPRacCounter::rac_do_mail_count();
                                            }
                                        } else {
                                            if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                //wp_mail($to, $subject, $message);
                                                $sent_mail_templates[] = $emails->id;
                                                $store_template_id = maybe_serialize($sent_mail_templates);
                                                $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                //add to mail log
                                                $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                FPRacCounter::rac_do_mail_count();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        // FOR GUEST
        if (get_option('rac_email_use_guests') == 'yes') {

            $abandon_carts = $wpdb->get_results("SELECT * FROM $abandancart_table_name WHERE cart_status='ABANDON' AND user_id='0' AND ip_address IS NULL AND completed IS NULL"); //Selected only cart which are not completed


            foreach ($abandon_carts as $each_cart) {

                foreach ($email_templates as $emails) {

                    if ($emails->status == "ACTIVE") {

                        $cart_array = maybe_unserialize($each_cart->cart_details);
                        $tablecheckproduct = "<table style='width:100%;border:1px solid #eee;'><thead><tr>";
                        if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                            $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_name', $each_cart->wpml_lang, get_option('rac_product_info_product_name')) . "</th>";
                        }
                        if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                            $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_image', $each_cart->wpml_lang, get_option('rac_product_info_product_image')) . "</th>";
                        }
                        if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                            $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_price', $each_cart->wpml_lang, get_option('rac_product_info_product_price')) . "</th>";
                        }
                        $tablecheckproduct .= "</tr></thead><tbody>";

                        $order = new WC_Order($cart_array->id);
                        $products = $order->get_items();
                        //var_dump($products);

                        /* $tablecheckproduct .= "It is from Guest0 Object or Array";
                          $dump = print_r($cart_array, true);
                          $tablecheckproduct .= "<pre>";
                          $tablecheckproduct .= $dump;
                          $tablecheckproduct .= "</pre>"; */

                        foreach ($products as $each_product) {
                            if ((float) $woocommerce->version <= (float) ('2.0.20')) {
                                $objectproduct = get_product($each_product['product_id']);
                                $objectproductvariable = get_product($each_product['variation_id']);
                            } else {
                                $objectproduct = new WC_Product($each_product['product_id']);
                                $objectproductvariable = new WC_Product_Variable($each_product['variation_id']);
                            }
                            $tablecheckproduct .= "<tr>";
                            if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_title($each_product['product_id']) . "</td>";
                            }
                            if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_post_thumbnail($each_product['product_id'], array(90, 90)) . "</td>";
                            }
                            if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . self::get_rac_formatprice($each_product['variation_id'] == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";
                            }
                            $tablecheckproduct .= "</tr>";
                        }


                        $tablecheckproduct .= "</table>";


                        if (empty($each_cart->mail_template_id)) { // IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE
                            if ($emails->sending_type == 'hours') {
                                $duration = $emails->sending_duration * 3600;
                            } else if ($emails->sending_type == 'minutes') {
                                $duration = $emails->sending_duration * 60;
                            } else if ($emails->sending_type == 'days') {
                                $duration = $emails->sending_duration * 86400;
                            }//duration is finished
                            $cut_off_time = $each_cart->cart_abandon_time + $duration;
                            $current_time = current_time('timestamp');
                            if ($current_time > $cut_off_time) {
                                @$cart_url = WC_Cart::get_cart_url();


                                $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id, 'guest' => 'yes'), $cart_url));

                                if (get_option('rac_cart_link_options') == '1') {
                                    $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                } elseif (get_option('rac_cart_link_options') == '2') {
                                    $url_to_click = $url_to_click;
                                } else {
                                    $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                    $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                }


//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE
//                                echo "<pre>";
//                                var_dump($each_cart->cart_details);
//                                echo "</pre>";
                                @$order_object = maybe_unserialize($each_cart->cart_details);
                                // $order_objectinfo = maybe_unserialize($productifo->cart_details);






                                $to = $order_object->billing_email;
                                $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                $firstname = $order_object->billing_first_name;
                                $lastname = $order_object->billing_last_name;


                                $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                $message = str_replace('{rac.firstname}', $firstname, $message);
                                $message = str_replace('{rac.lastname}', $lastname, $message);
                                $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);
                                if (strpos($message, "{rac.coupon}")) {
                                    $coupon_code = FPRacCoupon::rac_create_coupon($order_object->billing_email, $each_cart->cart_abandon_time);
                                    $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                }
                                add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                $message = do_shortcode($message); //shortcode feature
                                if (get_option('rac_email_use_temp_plain') != 'yes') {
                                    ob_start();
                                    if (function_exists('wc_get_template')) {
                                        wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                        echo $message;
                                        wc_get_template('emails/email-footer.php');
                                    } else {
                                        woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                        echo $message;
                                        woocommerce_get_template('emails/email-footer.php');
                                    }
                                    $woo_temp_msg = ob_get_clean();
                                } else {
                                    $woo_temp_msg = $message;
                                }
                                $headers = "MIME-Version: 1.0\r\n";
                                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                if ($emails->sender_opt == 'local') {
                                    $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                    $headers .= "Reply-To: " . $emails->from_name . "<" . $emails->from_email . ">\r\n";
                                } else {
                                    $headers .= self::rac_formatted_from_address_woocommerce();
                                    $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                }

                                if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                    if ('wp_mail' == get_option('rac_trouble_mail')) {
                                        if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                            // wp_mail($to, $subject, $message);
                                            $store_template_id = array($emails->id);
                                            $store_template_id = maybe_serialize($store_template_id);
                                            $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                            //add to mail log
                                            $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                            $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                            FPRacCounter::rac_do_mail_count();
                                        }
                                    } else {
                                        if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                            // wp_mail($to, $subject, $message);
                                            $store_template_id = array($emails->id);
                                            $store_template_id = maybe_serialize($store_template_id);
                                            $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                            //add to mail log
                                            $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                            $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                            FPRacCounter::rac_do_mail_count();
                                        }
                                    }
                                }
                            }// IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE END
                            elseif (!empty($each_cart->mail_template_id)) {
                                $sent_mail_templates = maybe_unserialize($each_cart->mail_template_id);

                                if (!in_array($emails->id, (array) $sent_mail_templates)) {
                                    if ($emails->sending_type == 'hours') {
                                        $duration = $emails->sending_duration * 3600;
                                    } else if ($emails->sending_type == 'minutes') {
                                        $duration = $emails->sending_duration * 60;
                                    } else if ($emails->sending_type == 'days') {
                                        $duration = $emails->sending_duration * 86400;
                                    }//duration is finished
                                    $cut_off_time = $each_cart->cart_abandon_time + $duration;
                                    $current_time = current_time('timestamp');
                                    if ($current_time > $cut_off_time) {
                                        @$cart_url = WC_Cart::get_cart_url();
                                        $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id, 'guest' => 'yes'), $cart_url));

                                        if (get_option('rac_cart_link_options') == '1') {
                                            $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                        } elseif (get_option('rac_cart_link_options') == '2') {
                                            $url_to_click = $url_to_click;
                                        } else {
                                            $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                            $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                        }


//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE
                                        $order_object = maybe_unserialize($each_cart->cart_details);



                                        $to = $order_object->billing_email;
                                        $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                        $firstname = $order_object->billing_first_name;
                                        $lastname = $order_object->billing_last_name;
                                        $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                        $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                        $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                        $message = str_replace('{rac.firstname}', $firstname, $message);
                                        $message = str_replace('{rac.lastname}', $lastname, $message);
                                        $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);
                                        if (strpos($message, "{rac.coupon}")) {
                                            $coupon_code = FPRacCoupon::rac_create_coupon($order_object->billing_email, $each_cart->cart_abandon_time);
                                            $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                        }
                                        add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                        $message = do_shortcode($message); //shortcode feature
                                        if (get_option('rac_email_use_temp_plain') != 'yes') {
                                            ob_start();
                                            if (function_exists('wc_get_template')) {
                                                wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                echo $message;
                                                wc_get_template('emails/email-footer.php');
                                            } else {
                                                woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                echo $message;
                                                woocommerce_get_template('emails/email-footer.php');
                                            }
                                            $woo_temp_msg = ob_get_clean();
                                        } else {
                                            $woo_temp_msg = $message;
                                        }
                                        $headers = "MIME-Version: 1.0\r\n";
                                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                        if ($emails->sender_opt == 'local') {
                                            $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                            $headers .= "Reply-To: " . $emails->from_name . "<" . $emails->from_email . ">\r\n";
                                        } else {
                                            $headers .= self::rac_formatted_from_address_woocommerce();
                                            $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                        }
                                        if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                            if ('wp_mail' == get_option('rac_trouble_mail')) {
                                                if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                    // wp_mail($to, $subject, $message);
                                                    $sent_mail_templates[] = $emails->id;
                                                    $store_template_id = maybe_serialize($sent_mail_templates);
                                                    $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                    //add to mail log
                                                    $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                    $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                    FPRacCounter::rac_do_mail_count();
                                                }
                                            } else {
                                                if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                    // wp_mail($to, $subject, $message);
                                                    $store_template_id = array($emails->id);
                                                    $store_template_id = maybe_serialize($store_template_id);
                                                    $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                    //add to mail log
                                                    $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                    $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                    FPRacCounter::rac_do_mail_count();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }


            //FOR Guest Captured in chcekout page
            $abandon_carts = $wpdb->get_results("SELECT * FROM $abandancart_table_name WHERE cart_status='ABANDON' AND user_id='0' AND ip_address IS NOT NULL AND completed IS NULL"); //Selected only cart which are not completed



            foreach ($abandon_carts as $each_cart) {
                foreach ($email_templates as $emails) {

                    $cart_array = maybe_unserialize($each_cart->cart_details);
                    $tablecheckproduct = "<table style='width:100%;border:1px solid #eee;'><thead><tr>";
                    if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                        $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_name', $each_cart->wpml_lang, get_option('rac_product_info_product_name')) . "</th>";
                    }
                    if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                        $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_image', $each_cart->wpml_lang, get_option('rac_product_info_product_image')) . "</th>";
                    }
                    if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                        $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_price', $each_cart->wpml_lang, get_option('rac_product_info_product_price')) . "</th>";
                    }
                    $tablecheckproduct .= "</tr></thead><tbody>";
                    if (is_array($cart_array)) {
                        /* $tablecheckproduct .= "It is from Guest Array";
                          $dump = print_r($cart_array, true);
                          $tablecheckproduct .= "<pre>";
                          $tablecheckproduct .= $dump;
                          $tablecheckproduct .= "</pre>"; */
                        foreach ($cart_array as $cart) {


                            if (is_array($cart)) {
                                if ((float) $woocommerce->version <= (float) ('2.0.20')) {
                                    $objectproduct = get_product($cart['product_id']);
                                    $objectproductvariable = get_product($cart['variation_id']);
                                } else {
                                    $objectproduct = new WC_Product($cart['product_id']);
                                    $objectproductvariable = new WC_Product_Variable($cart['variation_id']);
                                }
                                $tablecheckproduct .= "<tr>";
                                if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_title($cart['product_id']) . "</td>";
                                }
                                if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_post_thumbnail($cart['product_id'], array(90, 90)) . "</td>";
                                }
                                if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . self::get_rac_formatprice($cart['variation_id'] == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";
                                }
                                $tablecheckproduct .= "</tr>";
                            }
                        }
                    } elseif (is_object($cart_array)) {
                        /* $tablecheckproduct .= "It is from Guest1 Object";
                          $dump = print_r($cart_array, true);
                          $tablecheckproduct .= $dump; */
                        $order = new WC_Order($cart_array->id);
//                        if ($order->user_id != '') {
                        foreach ($order->get_items() as $products) {
                            if ((float) $woocommerce->version <= (float) ('2.0.20')) {
                                $objectproduct = get_product($products['product_id']);
                                $objectproductvariable = get_product($products['variation_id']);
                            } else {
                                $objectproduct = new WC_Product($products['product_id']);
                                $objectproductvariable = new WC_Product_Variable($products['variation_id']);
                            }
                            $tablecheckproduct .= "<tr>";
                            if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_title($products['product_id']) . "</td>";
                            }
                            if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_post_thumbnail($products['product_id'], array(90, 90)) . "</td>";
                            }
                            if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . self::get_rac_formatprice($products['variation_id'] == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";
                            }
                            $tablecheckproduct .= "</tr>";
                        }
                        //  }
                    }
                    $tablecheckproduct .= "</table>";
                    if ($emails->status == "ACTIVE") {


                        if (empty($each_cart->mail_template_id)) { // IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE
                            if ($emails->sending_type == 'hours') {
                                $duration = $emails->sending_duration * 3600;
                            } else if ($emails->sending_type == 'minutes') {
                                $duration = $emails->sending_duration * 60;
                            } else if ($emails->sending_type == 'days') {
                                $duration = $emails->sending_duration * 86400;
                            }//duration is finished
                            $cut_off_time = $each_cart->cart_abandon_time + $duration;
                            $current_time = current_time('timestamp');
                            if ($current_time > $cut_off_time) {

                                @$cart_url = WC_Cart::get_cart_url();
                                $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id, 'guest' => 'yes', 'checkout' => 'yes'), $cart_url));

                                if (get_option('rac_cart_link_options') == '1') {
                                    $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                } elseif (get_option('rac_cart_link_options') == '2') {
                                    $url_to_click = $url_to_click;
                                } else {
                                    $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                    $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                }

//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE
                                $order_object = maybe_unserialize($each_cart->cart_details);




                                $to = $order_object['visitor_mail'];
                                $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                $firstname = $order_object['first_name'];
                                $lastname = $order_object['last_name'];
                                $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                $message = str_replace('{rac.firstname}', $firstname, $message);
                                $message = str_replace('{rac.lastname}', $lastname, $message);
                                $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);
                                if (strpos($message, "{rac.coupon}")) {
                                    $coupon_code = FPRacCoupon::rac_create_coupon($order_object['visitor_mail'], $each_cart->cart_abandon_time);
                                    $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                }
                                add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                $message = do_shortcode($message); //shortcode feature
                                if (get_option('rac_email_use_temp_plain') != 'yes') {
                                    ob_start();
                                    if (function_exists('wc_get_template')) {
                                        wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                        echo $message;
                                        wc_get_template('emails/email-footer.php');
                                    } else {
                                        woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                        echo $message;
                                        woocommerce_get_template('emails/email-footer.php');
                                    }
                                    $woo_temp_msg = ob_get_clean();
                                } else {
                                    $woo_temp_msg = $message;
                                }
                                $headers = "MIME-Version: 1.0\r\n";
                                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                if ($emails->sender_opt == 'local') {
                                    $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                    $headers .= "Reply-To: " . $emails->from_name . "<" . $emails->from_email . ">\r\n";
                                } else {
                                    $headers .= self::rac_formatted_from_address_woocommerce();
                                    $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                }
                                if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                    if ('wp_mail' == get_option('rac_trouble_mail')) {
                                        //  var_dump($to,$subject,$woo_temp_msg,$headers);

                                        if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {

                                            // wp_mail($to, $subject, $message);
                                            $store_template_id = array($emails->id);
                                            $store_template_id = maybe_serialize($store_template_id);
                                            $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                            //add to mail log
                                            $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                            $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                            FPRacCounter::rac_do_mail_count();
                                        }
                                    } else {

                                        if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                            // wp_mail($to, $subject, $message);
                                            $store_template_id = array($emails->id);
                                            $store_template_id = maybe_serialize($store_template_id);
                                            $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                            //add to mail log
                                            $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                            $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                            FPRacCounter::rac_do_mail_count();
                                        }
                                    }
                                }
                            }
                        }// IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE END
                        elseif (!empty($each_cart->mail_template_id)) {
                            $sent_mail_templates = maybe_unserialize($each_cart->mail_template_id);

                            if (!in_array($emails->id, (array) $sent_mail_templates)) {
                                if ($emails->sending_type == 'hours') {
                                    $duration = $emails->sending_duration * 3600;
                                } else if ($emails->sending_type == 'minutes') {
                                    $duration = $emails->sending_duration * 60;
                                } else if ($emails->sending_type == 'days') {
                                    $duration = $emails->sending_duration * 86400;
                                }//duration is finished
                                $cut_off_time = $each_cart->cart_abandon_time + $duration;
                                $current_time = current_time('timestamp');
                                if ($current_time > $cut_off_time) {
                                    @$cart_url = WC_Cart::get_cart_url();
                                    $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id, 'guest' => 'yes', 'checkout' => 'yes'), $cart_url));

                                    if (get_option('rac_cart_link_options') == '1') {
                                        $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                    } elseif (get_option('rac_cart_link_options') == '2') {
                                        $url_to_click = $url_to_click;
                                    } else {
                                        $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                        $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                    }

//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE
                                    $order_object = maybe_unserialize($each_cart->cart_details);

                                    $to = $order_object['visitor_mail'];
                                    $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                    $firstname = $order_object['first_name'];
                                    $lastname = $order_object['last_name'];
                                    $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                    $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                    $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                    $message = str_replace('{rac.firstname}', $firstname, $message);
                                    $message = str_replace('{rac.lastname}', $lastname, $message);
                                    $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                                    if (strpos($message, "{rac.coupon}")) {
                                        $coupon_code = FPRacCoupon::rac_create_coupon($order_object['visitor_mail'], $each_cart->cart_abandon_time);
                                        $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                    }
                                    add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                    $message = do_shortcode($message); //shortcode feature
                                    if (get_option('rac_email_use_temp_plain') != 'yes') {
                                        ob_start();
                                        if (function_exists('wc_get_template')) {
                                            wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                            echo $message;
                                            wc_get_template('emails/email-footer.php');
                                        } else {
                                            woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                            echo $message;
                                            woocommerce_get_template('emails/email-footer.php');
                                        }
                                        $woo_temp_msg = ob_get_clean();
                                    } else {
                                        $woo_temp_msg = $message;
                                    }
                                    $headers = "MIME-Version: 1.0\r\n";
                                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                    if ($emails->sender_opt == 'local') {
                                        $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                        $headers .= "Reply-To: " . $emails->from_name . "<" . $emails->from_email . ">\r\n";
                                    } else {
                                        $headers .= self::rac_formatted_from_address_woocommerce();
                                        $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                    }
                                    if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                        if ('wp_mail' == get_option('rac_trouble_mail')) {
                                            if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                // wp_mail($to, $subject, $message);
                                                $sent_mail_templates[] = $emails->id;
                                                $store_template_id = maybe_serialize($sent_mail_templates);
                                                $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                //add to mail log
                                                $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                FPRacCounter::rac_do_mail_count();
                                            }
                                        } else {
                                            if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                // wp_mail($to, $subject, $message);
                                                $sent_mail_templates[] = $emails->id;
                                                $store_template_id = maybe_serialize($sent_mail_templates);
                                                $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                //add to mail log
                                                $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                FPRacCounter::rac_do_mail_count();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        // FOR ORDER UPDATED FROM OLD
        $abandon_carts = $wpdb->get_results("SELECT * FROM $abandancart_table_name WHERE cart_status='ABANDON' AND user_id='old_order' AND ip_address IS NULL AND completed IS NULL"); //Selected only cart which are not completed

        foreach ($abandon_carts as $each_cart) {

            foreach ($email_templates as $emails) {

                $cart_array = maybe_unserialize($each_cart->cart_details);
                $tablecheckproduct = "<table style='width:100%;border:1px solid #eee;'><thead><tr>";
                if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                    $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_name', $each_cart->wpml_lang, get_option('rac_product_info_product_name')) . "</th>";
                }
                if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                    $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_image', $each_cart->wpml_lang, get_option('rac_product_info_product_image')) . "</th>";
                }
                if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                    $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_price', $each_cart->wpml_lang, get_option('rac_product_info_product_price')) . "</th>";
                }
                $tablecheckproduct .= "</tr></thead><tbody>";

                if (is_object($cart_array)) {
                    if (get_option('rac_email_use_members') == 'yes') {

                        /* $tablecheckproduct .= "It is from Member Object";
                          $dump = print_r($cart_array, true);
                          $tablecheckproduct .= $dump; */

                        $order = new WC_Order($cart_array->id);
                        if ($order->user_id != '') {
                            foreach ($order->get_items() as $products) {
                                if ((float) $woocommerce->version <= (float) ('2.0.20')) {
                                    $objectproduct = get_product($products['product_id']);
                                    $objectproductvariable = get_product($products['variation_id']);
                                } else {
                                    $objectproduct = new WC_Product($products['product_id']);
                                    $objectproductvariable = new WC_Product_Variable($products['variation_id']);
                                }
                                $tablecheckproduct .= "<tr>";
                                if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_title($products['product_id']) . "</td>";
                                }
                                if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_post_thumbnail($products['product_id'], array(90, 90)) . "</td>";
                                }

                                if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                                    $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . self::get_rac_formatprice($products['variation_id'] == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";
                                }
                                $tablecheckproduct .= "</tr>";
                            }
                        }


                        //mail
                        if ($emails->status == "ACTIVE") {


                            if (empty($each_cart->mail_template_id)) { // IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE
                                if ($emails->sending_type == 'hours') {
                                    $duration = $emails->sending_duration * 3600;
                                } else if ($emails->sending_type == 'minutes') {
                                    $duration = $emails->sending_duration * 60;
                                } else if ($emails->sending_type == 'days') {
                                    $duration = $emails->sending_duration * 86400;
                                }//duration is finished
                                $cut_off_time = $each_cart->cart_abandon_time + $duration;
                                $current_time = current_time('timestamp');
                                if ($current_time > $cut_off_time) {
                                    @$cart_url = WC_Cart::get_cart_url();
                                    $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id, 'old_order' => 'yes'), $cart_url));

                                    if (get_option('rac_cart_link_options') == '1') {
                                        $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                    } elseif (get_option('rac_cart_link_options') == '2') {
                                        $url_to_click = $url_to_click;
                                    } else {
                                        $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                        $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                    }

                                    //  $user = get_userdata($each_cart->user_id); NOT APPLICABLE
                                    $order_object = maybe_unserialize($each_cart->cart_details);

                                    $to = $order_object->billing_email;
                                    $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                    $firstname = $order_object->billing_first_name;
                                    $lastname = $order_object->billing_last_name;

                                    $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                    $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                    $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                    $message = str_replace('{rac.firstname}', $firstname, $message);
                                    $message = str_replace('{rac.lastname}', $lastname, $message);
                                    $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                                    if (strpos($message, "{rac.coupon}")) {
                                        $coupon_code = FPRacCoupon::rac_create_coupon($order_object->billing_email, $each_cart->cart_abandon_time);
                                        $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                    }
                                    add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                    $message = do_shortcode($message); //shortcode feature
                                    if (get_option('rac_email_use_temp_plain') != 'yes') {
                                        ob_start();
                                        if (function_exists('wc_get_template')) {
                                            wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                            echo $message;
                                            wc_get_template('emails/email-footer.php');
                                        } else {
                                            woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                            echo $message;
                                            woocommerce_get_template('emails/email-footer.php');
                                        }
                                        $woo_temp_msg = ob_get_clean();
                                    } else {
                                        $woo_temp_msg = $message;
                                    }
                                    $headers = "MIME-Version: 1.0\r\n";
                                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                    if ($emails->sender_opt == 'local') {
                                        $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                        $headers .= "Reply-To: " . $emails->from_name . "<" . $emails->from_email . ">\r\n";
                                    } else {
                                        $headers .= self::rac_formatted_from_address_woocommerce();
                                        $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                    }




                                    if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                        if ('wp_mail' == get_option('rac_trouble_mail')) {
                                            if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                // wp_mail($to, $subject, $message);
                                                $store_template_id = array($emails->id);
                                                $store_template_id = maybe_serialize($store_template_id);
                                                $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                //add to mail log
                                                $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                FPRacCounter::rac_do_mail_count();
                                            }
                                        } else {
                                            if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                // wp_mail($to, $subject, $message);
                                                $store_template_id = array($emails->id);
                                                $store_template_id = maybe_serialize($store_template_id);
                                                $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                //add to mail log
                                                $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                FPRacCounter::rac_do_mail_count();
                                            }
                                        }
                                    }
                                }
                            }// IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE END
                            elseif (!empty($each_cart->mail_template_id)) {
                                $sent_mail_templates = maybe_unserialize($each_cart->mail_template_id);

                                if (!in_array($emails->id, (array) $sent_mail_templates)) {
                                    if ($emails->sending_type == 'hours') {
                                        $duration = $emails->sending_duration * 3600;
                                    } else if ($emails->sending_type == 'minutes') {
                                        $duration = $emails->sending_duration * 60;
                                    } else if ($emails->sending_type == 'days') {
                                        $duration = $emails->sending_duration * 86400;
                                    }//duration is finished
                                    $cut_off_time = $each_cart->cart_abandon_time + $duration;
                                    $current_time = current_time('timestamp');
                                    if ($current_time > $cut_off_time) {
                                        @$cart_url = WC_Cart::get_cart_url();
                                        $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id, 'old_order' => 'yes'), $cart_url));

                                        if (get_option('rac_cart_link_options') == '1') {
                                            $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                        } elseif (get_option('rac_cart_link_options') == '2') {
                                            $url_to_click = $url_to_click;
                                        } else {
                                            $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                            $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                        }



//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE
                                        $order_object = maybe_unserialize($each_cart->cart_details);







                                        $to = $order_object->billing_email;
                                        $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                        $firstname = $order_object->billing_first_name;
                                        $lastname = $order_object->billing_last_name;
                                        $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                        $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                        $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                        $message = str_replace('{rac.firstname}', $firstname, $message);
                                        $message = str_replace('{rac.lastname}', $lastname, $message);
                                        $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);
                                        if (strpos($message, "{rac.coupon}")) {
                                            $coupon_code = FPRacCoupon::rac_create_coupon($order_object->billing_email, $each_cart->cart_abandon_time);
                                            $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                        }
                                        add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                        $message = do_shortcode($message); //shortcode feature
                                        if (get_option('rac_email_use_temp_plain') != 'yes') {
                                            ob_start();
                                            if (function_exists('wc_get_template')) {
                                                wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                echo $message;
                                                wc_get_template('emails/email-footer.php');
                                            } else {
                                                woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                echo $message;
                                                woocommerce_get_template('emails/email-footer.php');
                                            }
                                            $woo_temp_msg = ob_get_clean();
                                        } else {
                                            $woo_temp_msg = $message;
                                        }
                                        $headers = "MIME-Version: 1.0\r\n";
                                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                        if ($emails->sender_opt == 'local') {
                                            $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                            $headers .= "Reply-To: " . $emails->from_name . " <" . $emails->from_email . ">\r\n";
                                        } else {
                                            $headers .= self::rac_formatted_from_address_woocommerce();
                                            $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                        }






                                        if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                            if ('wp_mail' == get_option('rac_trouble_mail')) {
                                                if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                    // wp_mail($to, $subject, $message);
                                                    $sent_mail_templates[] = $emails->id;
                                                    $store_template_id = maybe_serialize($sent_mail_templates);
                                                    $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                    //add to mail log
                                                    $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                    $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                    FPRacCounter::rac_do_mail_count();
                                                }
                                            } else {
                                                if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                    // wp_mail($to, $subject, $message);
                                                    $sent_mail_templates[] = $emails->id;
                                                    $store_template_id = maybe_serialize($sent_mail_templates);
                                                    $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                    //add to mail log
                                                    $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                    $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                    FPRacCounter::rac_do_mail_count();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (get_option('rac_email_use_guests') == 'yes') {
                        /*  $tablecheckproduct .= "It is from Guest2 Object";
                          $dump = print_r($cart_array, true);
                          $tablecheckproduct .= $dump; */
                        //  if ($order->user_id == '') {
                        foreach ($order->get_items() as $products) {
                            if ((float) $woocommerce->version <= (float) ('2.0.20')) {
                                $objectproduct = get_product($products['product_id']);
                                $objectproductvariable = get_product($products['variation_id']);
                            } else {
                                $objectproduct = new WC_Product($products['product_id']);
                                $objectproductvariable = new WC_Product_Variable($products['variation_id']);
                            }
                            $tablecheckproduct .= "<tr>";
                            if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_title($products['product_id']) . "</td>";
                            }
                            if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_post_thumbnail($products['product_id'], array(90, 90)) . "</td>";
                            }
                            if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {
                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . self::get_rac_formatprice($products['variation_id'] == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";
                            }
                            $tablecheckproduct .= "</tr>";
                        }
                        // }
                        $tablecheckproduct .= "</table>";

                        //guest mail
                        if ($emails->status == "ACTIVE") {


                            if (empty($each_cart->mail_template_id)) { // IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE
                                if ($emails->sending_type == 'hours') {
                                    $duration = $emails->sending_duration * 3600;
                                } else if ($emails->sending_type == 'minutes') {
                                    $duration = $emails->sending_duration * 60;
                                } else if ($emails->sending_type == 'days') {
                                    $duration = $emails->sending_duration * 86400;
                                }//duration is finished
                                $cut_off_time = $each_cart->cart_abandon_time + $duration;
                                $current_time = current_time('timestamp');
                                if ($current_time > $cut_off_time) {
                                    @$cart_url = WC_Cart::get_cart_url();
                                    $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id, 'old_order' => 'yes'), $cart_url));
                                    if (get_option('rac_cart_link_options') == '1') {
                                        $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                    } elseif (get_option('rac_cart_link_options') == '2') {
                                        $url_to_click = $url_to_click;
                                    } else {
                                        $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                        $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                    }


//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE
                                    $order_object = maybe_unserialize($each_cart->cart_details);






                                    $to = $order_object->billing_email;
                                    $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                    $firstname = $order_object->billing_first_name;
                                    $lastname = $order_object->billing_last_name;

                                    $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                    $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                    $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                    $message = str_replace('{rac.firstname}', $firstname, $message);
                                    $message = str_replace('{rac.lastname}', $lastname, $message);
                                    $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                                    if (strpos($message, "{rac.coupon}")) {
                                        $coupon_code = FPRacCoupon::rac_create_coupon($order_object->billing_email, $each_cart->cart_abandon_time);
                                        $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                    }
                                    add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                    $message = do_shortcode($message); //shortcode feature
                                    if (get_option('rac_email_use_temp_plain') != 'yes') {
                                        ob_start();
                                        if (function_exists('wc_get_template')) {
                                            wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                            echo $message;
                                            wc_get_template('emails/email-footer.php');
                                        } else {
                                            woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                            echo $message;
                                            woocommerce_get_template('emails/email-footer.php');
                                        }
                                        $woo_temp_msg = ob_get_clean();
                                    } else {
                                        $woo_temp_msg = $message;
                                    }
                                    $headers = "MIME-Version: 1.0\r\n";
                                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                    if ($emails->sender_opt == 'local') {
                                        $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                        $headers .= "Reply-To: " . $emails->from_name . "<" . $emails->from_email . ">\r\n";
                                    } else {
                                        $headers .= self::rac_formatted_from_address_woocommerce();
                                        $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                    }




                                    if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                        if ('wp_mail' == get_option('rac_trouble_mail')) {
                                            if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                // wp_mail($to, $subject, $message);
                                                $store_template_id = array($emails->id);
                                                $store_template_id = maybe_serialize($store_template_id);
                                                $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                //add to mail log
                                                $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                FPRacCounter::rac_do_mail_count();
                                            }
                                        } else {
                                            if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                // wp_mail($to, $subject, $message);
                                                $store_template_id = array($emails->id);
                                                $store_template_id = maybe_serialize($store_template_id);
                                                $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                //add to mail log
                                                $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                FPRacCounter::rac_do_mail_count();
                                            }
                                        }
                                    }
                                }
                            }// IF EMPTY IT IS NOT SENT FOR ANY SINGLE TEMPLATE END
                            elseif (!empty($each_cart->mail_template_id)) {
                                $sent_mail_templates = maybe_unserialize($each_cart->mail_template_id);

                                if (!in_array($emails->id, (array) $sent_mail_templates)) {
                                    if ($emails->sending_type == 'hours') {
                                        $duration = $emails->sending_duration * 3600;
                                    } else if ($emails->sending_type == 'minutes') {
                                        $duration = $emails->sending_duration * 60;
                                    } else if ($emails->sending_type == 'days') {
                                        $duration = $emails->sending_duration * 86400;
                                    }//duration is finished
                                    $cut_off_time = $each_cart->cart_abandon_time + $duration;
                                    $current_time = current_time('timestamp');
                                    if ($current_time > $cut_off_time) {
                                        @$cart_url = WC_Cart::get_cart_url();
                                        $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $each_cart->id, 'email_template' => $emails->id, 'old_order' => 'yes'), $cart_url));

                                        if (get_option('rac_cart_link_options') == '1') {
                                            $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text) . '</a>';
                                        } elseif (get_option('rac_cart_link_options') == '2') {
                                            $url_to_click = $url_to_click;
                                        } else {
                                            $cart_Text = fp_get_wpml_text('rac_template_' . $emails->id . '_anchor_text', $each_cart->wpml_lang, $emails->anchor_text);
                                            $url_to_click = RecoverAbandonCart::rac_cart_link_button_mode($url_to_click, $cart_Text);
                                        }


//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE
                                        $order_object = maybe_unserialize($each_cart->cart_details);







                                        $to = $order_object->billing_email;
                                        $subject = fp_get_wpml_text('rac_template_' . $emails->id . '_subject', $each_cart->wpml_lang, $emails->subject);
                                        $firstname = $order_object->billing_first_name;
                                        $lastname = $order_object->billing_last_name;
                                        $message = fp_get_wpml_text('rac_template_' . $emails->id . '_message', $each_cart->wpml_lang, $emails->message);
                                        $message = str_replace('{rac.cartlink}', $url_to_click, $message);
                                        $subject = RecoverAbandonCart::shortcode_in_subject($firstname, $lastname, $subject);
                                        $message = str_replace('{rac.firstname}', $firstname, $message);
                                        $message = str_replace('{rac.lastname}', $lastname, $message);
                                        $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                                        if (strpos($message, "{rac.coupon}")) {
                                            $coupon_code = FPRacCoupon::rac_create_coupon($order_object->billing_email, $each_cart->cart_abandon_time);
                                            $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code
                                        }
                                        add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));
                                        $message = do_shortcode($message); //shortcode feature
                                        if (get_option('rac_email_use_temp_plain') != 'yes') {

                                            ob_start();
                                            if (function_exists('wc_get_template')) {
                                                wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                echo $message;
                                                wc_get_template('emails/email-footer.php');
                                            } else {
                                                woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                echo $message;
                                                woocommerce_get_template('emails/email-footer.php');
                                            }
                                            $woo_temp_msg = ob_get_clean();
                                        } else {
                                            $woo_temp_msg = $message;
                                        }
                                        $headers = "MIME-Version: 1.0\r\n";
                                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                        if ($emails->sender_opt == 'local') {
                                            $headers .= self::rac_formatted_from_address_local($emails->from_name, $emails->from_email);
                                            $headers .= "Reply-To: " . $emails->from_name . " <" . $emails->from_email . ">\r\n";
                                        } else {
                                            $headers .= self::rac_formatted_from_address_woocommerce();
                                            $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . "<" . get_option('woocommerce_email_from_address') . ">\r\n";
                                        }






                                        if ($each_cart->sending_status == 'SEND') {//condition to check start/stop mail sending
                                            if ('wp_mail' == get_option('rac_trouble_mail')) {
                                                if (self::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                    // wp_mail($to, $subject, $message);
                                                    $sent_mail_templates[] = $emails->id;
                                                    $store_template_id = maybe_serialize($sent_mail_templates);
                                                    $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                    //add to mail log
                                                    $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                    $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                    FPRacCounter::rac_do_mail_count();
                                                }
                                            } else {
                                                if (self::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {
                                                    // wp_mail($to, $subject, $message);
                                                    $sent_mail_templates[] = $emails->id;
                                                    $store_template_id = maybe_serialize($sent_mail_templates);
                                                    $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));
                                                    //add to mail log
                                                    $table_name_logs = $wpdb->prefix . 'rac_email_logs';
                                                    $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $emails->id));
                                                    FPRacCounter::rac_do_mail_count();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers) {
        //$get_user_by = get_user_by('email', $to);


        global $woocommerce;
        $getdesiredoption = get_option('custom_exclude');
        if ($getdesiredoption == 'user_role') {
            $userrolenamemailget = get_option('custom_user_role');
            $getuserby = get_user_by('email', $to);
            if ($getuserby) {
                $newto = $getuserby->roles[0];
            } else {
                $newto = $to;
            }
        } elseif ($getdesiredoption == 'name') {
            $userrolenamemailget = get_option('custom_user_name_select');
            $getuserby = get_user_by('email', $to);
            if ($getuserby) {
                $newto = $getuserby->ID;
            } else {
                $newto = $to;
            }
        } else {
            $userrolenamemailget = get_option('custom_mailid_edit');
            $userrolenamemailget = explode("\r\n", $userrolenamemailget);
            $newto = $to;
        }

        $check_member_guest = RecoverAbandonCart::check_is_member_or_guest($newto);
        $proceed = '1';
        if ($check_member_guest) {
            // for member
            $userid = RecoverAbandonCart::rac_return_user_id($newto);
            $status = get_user_meta($userid, 'fp_rac_mail_unsubscribed', true);
            if ($status != 'yes') {
                $proceed = '1';
            } else {
                $proceed = '2';
            }
        } else {
            // for guest
            $needle = $newto;
            if (!in_array($needle, (array) get_option('fp_rac_mail_unsubscribed'))) {
                $proceed = '1';
            } else {
                $proceed = '2';
            }
        }

        if ($proceed == '1') {
            if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                if (!in_array($newto, (array) $userrolenamemailget)) {

                    if (get_option('rac_webmaster_mail') == 'webmaster1') {
                        return wp_mail($to, $subject, $woo_temp_msg, $headers, '-f ' . get_option('rac_textarea_mail'));
                    } else {
                        return wp_mail($to, $subject, $woo_temp_msg, $headers);
                    }
                }
            } else {

                if (!in_array($newto, (array) $userrolenamemailget)) {
                    $mailer = WC()->mailer();
                    $mailer->send($to, $subject, $woo_temp_msg, $headers, '');
                    return "1";
                }
            }
        }
    }

    public static function rac_send_mail($to, $subject, $woo_temp_msg, $headers) {
        global $woocommerce;
        $getdesiredoption = get_option('custom_exclude');
        if ($getdesiredoption == 'user_role') {
            $userrolenamemailget = get_option('custom_user_role');
            $getuserby = get_user_by('email', $to);
            if ($getuserby) {
                $newto = $getuserby->roles[0];
            } else {
                $newto = $to;
            }
        } elseif ($getdesiredoption == 'name') {
            $userrolenamemailget = get_option('custom_user_name_select');
            $getuserby = get_user_by('email', $to);
            if ($getuserby) {
                $newto = $getuserby->ID;
            } else {
                $newto = $to;
            }
        } else {
            $userrolenamemailget = get_option('custom_mailid_edit');
            $userrolenamemailget = explode("\r\n", $userrolenamemailget);
            $newto = $to;
        }



        $check_member_guest = RecoverAbandonCart::check_is_member_or_guest($newto);
        $proceed = '1';
        if ($check_member_guest) {
            // for member
            $userid = RecoverAbandonCart::rac_return_user_id($newto);
            $status = get_user_meta($userid, 'fp_rac_mail_unsubscribed', true);
            if ($status != 'yes') {
                $proceed = '1';
            } else {
                $proceed = '2';
            }
        } else {
            // for guest
            $needle = $newto;
            if (!in_array($needle, (array) get_option('fp_rac_mail_unsubscribed'))) {
                $proceed = '1';
            } else {
                $proceed = '2';
            }
        }
        if ($proceed == '1') {
            if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                if (!in_array($newto, (array) $userrolenamemailget)) {

                    if (get_option('rac_webmaster_mail') == 'webmaster1') {
                        return mail($to, $subject, $woo_temp_msg, $headers, '-f ' . get_option('rac_textarea_mail'));
                    } else {
                        return mail($to, $subject, $woo_temp_msg, $headers);
                    }
                }
            } else {
                if (!in_array($newto, (array) $userrolenamemailget)) {
                    $mailer = WC()->mailer();
                    $mailer->send($to, $subject, $woo_temp_msg, $headers, '');
                    return "1";
                }
            }
        }
    }

    // For Test Mail Function

    public static function rac_send_wp_mail_test($to, $subject, $woo_temp_msg, $headers) {
        global $woocommerce;
        if ((float) $woocommerce->version <= (float) ('2.2.0')) {
            if (get_option('rac_webmaster_mail') == 'webmaster1') {
                return wp_mail($to, $subject, $woo_temp_msg, $headers, '-f ' . get_option('rac_textarea_mail'));
            } else {
                return wp_mail($to, $subject, $woo_temp_msg, $headers);
            }
        } else {
            $mailer = WC()->mailer();
            $mailer->send($to, $subject, $woo_temp_msg, $headers, '');
            return "1";
        }
    }

    public static function rac_send_mail_test($to, $subject, $woo_temp_msg, $headers) {
        global $woocommerce;
        if ((float) $woocommerce->version <= (float) ('2.2.0')) {
            if (get_option('rac_webmaster_mail') == 'webmaster1') {
                return mail($to, $subject, $woo_temp_msg, $headers, '-f ' . get_option('rac_textarea_mail'));
            } else {
                return mail($to, $subject, $woo_temp_msg, $headers);
            }
        } else {
            $mailer = WC()->mailer();
            $mailer->send($to, $subject, $woo_temp_msg, $headers, '');
            return "1";
        }
    }

    public static function rac_formatted_from_address_local($fromname, $fromemail) {
        if (get_option('rac_webmaster_mail') == 'webmaster1') {
            return "From: " . $fromname . " <" . $fromemail . ">" . "-f " . get_option('rac_textarea_mail') . "\r\n";
        } else {
            return "From: " . $fromname . " <" . $fromemail . ">\r\n";
        }
    }

    public static function rac_formatted_from_address_woocommerce() {
        if (get_option('rac_webmaster_mail') == 'webmaster1') {
            return "From: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">" . "-f " . get_option('rac_textarea_mail') . "\r\n";
        } else {
            return "From: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";
        }
    }

}

//add_action('admin_head', array('FPRacCron', 'fp_rac_cron_job_mailing'));