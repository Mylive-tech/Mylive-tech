<?php

/**

 * Plugin Name: Recover Abandoned Cart

 * Plugin URI:

 * Description: Recover Abandoned Cart is a WooCommerce Extension Plugin which will help you Recover the Abandoned Carts and bring more sales.

 * Version: 8.1

 * Author: Fantastic Plugins

 * Author URI:

 */

/*

  Copyright 2014 Fantastic Plugins. All Rights Reserved.

  This Software should not be used or changed without the permission

  of Fantastic Plugins.

 */



require_once 'inc/fp_rac_counter.php';

require_once 'inc/fp_rac_coupon.php';



class RecoverAbandonCart {



    public static function fprac_check_woo_active() {

        $woocommerce = "woocommerce/woocommerce.php";

        $recoverabandoncart = "rac/recoverabandoncart.php";

        if (!is_plugin_active($woocommerce)) {

            deactivate_plugins($recoverabandoncart);

        }

    }



    public static function fprac_header_problems() {

        ob_start();

    }



    public static function fprac_settings_tabs($tabs) {

        $tabs['fpracgenral'] = __('General Settings', 'recoverabandoncart');

        $tabs['fpracemail'] = __('Email Settings', 'recoverabandoncart');

        $tabs['fpractable'] = __('Cart List', 'recoverabandoncart');

        $tabs['fpracupdate'] = __('Check Previous Orders', 'recoverabandoncart');

        $tabs['fpraccoupon'] = __('Coupon In Mail', 'recoverabandoncart');

        $tabs['fpracmailog'] = __('Mail Log', 'recoverabandoncart');

        $tabs['fpracdebug'] = __('Troubleshoot', 'recoverabandoncart');

        $tabs['fpracreport'] = __('Reports', 'recoverabandoncart');

        $tabs['fpracshortocde'] = __('Shortcodes', 'recoverabandoncart');

        return $tabs;

    }



    public static function fprac_access_woo_script($array_screens) {

        $newscreenids = get_current_screen();

        $array_screens[] = $newscreenids->id;

        return $array_screens;

    }



    public static function fprac_admin_submenu() {

        add_submenu_page('woocommerce', 'Recover Abandoned Cart', 'Recover Abandoned Cart', 'manage_woocommerce', 'fprac_slug', array('RecoverAbandonCart', 'fprac_admin_settings'));

    }



    public static function fprac_admin_settings() {

        global $woocommerce, $woocommerce_settings, $current_section, $current_tab;

        $tabs = "";

        do_action('woocommerce_fprac_settings_start');

        $current_tab = ( empty($_GET['tab']) ) ? 'fpracgenral' : sanitize_text_field(urldecode($_GET['tab']));

        $current_section = ( empty($_REQUEST['section']) ) ? '' : sanitize_text_field(urldecode($_REQUEST['section']));

        if (!empty($_POST['save'])) {

            if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'woocommerce-settings'))

                die(__('Action failed. Please refresh the page and retry.', 'woocommercecustomtext'));



            if (!$current_section) {

//include_once('settings/settings-save.php');

                switch ($current_tab) {

                    default :

                        if (isset($woocommerce_settings[$current_tab]))

                            woocommerce_update_options($woocommerce_settings[$current_tab]);



// Trigger action for tab

                        do_action('woocommerce_update_options_' . $current_tab);

                        break;

                }



                do_action('woocommerce_update_options');



// Handle Colour Settings

                if ($current_tab == 'general' && get_option('woocommerce_frontend_css') == 'yes') {



                }

            } else {

// Save section onlys

                do_action('woocommerce_update_options_' . $current_tab . '_' . $current_section);

            }



// Clear any unwanted data

// $woocommerce->clear_product_transients();

            delete_transient('woocommerce_cache_excluded_uris');

// Redirect back to the settings page

            $redirect = esc_url_raw(add_query_arg(array('saved' => 'true')));

//  $redirect .= add_query_arg('noheader', 'true');



            if (isset($_POST['subtab'])) {

                wp_safe_redirect($redirect);

                exit;

            }

        }

// Get any returned messages

        $error = ( empty($_GET['wc_error']) ) ? '' : urldecode(stripslashes($_GET['wc_error']));

        $message = ( empty($_GET['wc_message']) ) ? '' : urldecode(stripslashes($_GET['wc_message']));



        if ($error || $message) {



            if ($error) {

                echo '<div id="message" class="error fade"><p><strong>' . esc_html($error) . '</strong></p></div>';

            } else {

                echo '<div id="message" class="updated fade"><p><strong>' . esc_html($message) . '</strong></p></div>';

            }

        } elseif (!empty($_GET['saved'])) {



            echo '<div id="message" class="updated fade"><p><strong>' . __('Your settings have been saved.', 'recoverabandoncart') . '</strong></p></div>';

        } elseif (!empty($_GET['resetted'])) {

            echo '<div id="message" class="updated fade"><p><strong>' . __('Your settings have been Restored.', 'recoverabandoncart') . '</strong></p></div>';

        }

        ?>

        <div class="wrap woocommerce">

            <form method="post" id="mainform" action="" enctype="multipart/form-data">

                <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper">

                    <?php

                    $tabs = apply_filters('woocommerce_fprac_settings_tabs_array', $tabs);

                    foreach ($tabs as $name => $label)

                        echo '<a href="' . admin_url('admin.php?page=fprac_slug&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';



                    do_action('woocommerce_fprac_settings_tabs');

                    ?>

                </h2>



                <?php

                switch ($current_tab) {

                    case "fpractable":

                        RecoverAbandonCart::fp_rac_adandoncart_admin_display();

                        break;

                    case "fpracemail":

                        if (!isset($_GET['rac_new_email']) && !isset($_GET['rac_edit_email']) && !isset($_GET['rac_send_email'])) {

                            do_action('woocommerce_fprac_settings_tabs_' . $current_tab); // @deprecated hook

                            do_action('woocommerce_fprac_settings_' . $current_tab);

                            ?>

                            <p class="submit" style="margin-left: 25px;">

                                <?php if (!isset($GLOBALS['hide_save_button'])) : ?>

                                    <input name="save" class="button-primary" type="submit" value="<?php _e('Save', 'recoverabandoncart'); ?>" />

                                <?php endif; ?>

                                <input type="hidden" name="subtab" id="last_tab" />

                                <?php wp_nonce_field('woocommerce-settings'); ?>

                            </p>

                            <?php

                        }

                        //email template lists



                        global $wpdb;

                        $table_name = $wpdb->prefix . 'rac_templates_email';

                        $templates = $wpdb->get_results("SELECT * FROM $table_name", OBJECT);



                        if (isset($_GET['rac_new_email'])) {

                            $editor_id = "rac_email_template_new";

                            //$content = get_option('rac_email_template');

                            $settings = array('textarea_name' => 'rac_email_template_new');

                            $admin_url = admin_url('admin.php');

                            $template_list_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail'), $admin_url));

                            $content = "Hi {rac.firstname}, <br><br>You have not completed your purchase.<br><br>Product Information{rac.Productinfo}.<br><br>Use the following link to make the purchase.<br>{rac.cartlink} <br><br> Thanks";

                            echo '<table class="widefat"><tr><td>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.cartlink} to insert the Cart Link in the mail</strong></span></td></tr>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.firstname} to insert Reciever First Name in the mail</strong></span></td></tr>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.lastname} to insert Receiver Last Name in the mail</strong></span></td></tr>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.Productinfo} to insert Product Information in the mail</strong></span></td></tr>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.coupon} to insert Coupon Code in the mail</strong></span></td></tr><tr><td>';

                            echo '<tr><td>' . __('Template Name', 'recoverabandoncart') . ': </td><td><input type="text" name="rac_template_name" id="rac_template_name"></td></tr>';

                            echo '<tr><td>' . __('Template Status', 'recoverabandoncart') . ':</td><td> <select name="rac_template_status" id="rac_template_status">

                                <option value="ACTIVE">Activated</option>

                                <option value="NOTACTIVE">Deactivated</option>

                                </select></td></tr>';

                            echo '<tr><td>' . __('Email Sender Option', 'recoverabandoncart') . ': </td><td><input type="radio" name="rac_sender_opt" id="rac_sender_woo" value="woo" class="rac_sender_opt">woocommerce <input type="radio" name="rac_sender_opt" id="rac_sender_local" value="local" class="rac_sender_opt">local</td></tr>';

                            echo '<tr class="rac_local_senders"><td>' . __('From Name', 'recoverabandoncart') . ': </td><td><input type="text" name="rac_from_name"  id="rac_from_name"></td></tr>';

                            echo '<tr class="rac_local_senders"><td>' . __('From Email', 'recoverabandoncart') . ': </td><td><input type="text" name="rac_from_email"  id="rac_from_email"></td></tr>';

                            echo '<tr><td>' . __('Subject', 'recoverabandoncart') . ':</td><td> <input type="text" name="rac_subject" id="rac_subject"></td></tr>';

                            echo '<tr><td>' . __('Duration to Send Mail After Abandoned Cart', 'recoverabandoncart') . ':<select name="rac_duration_type" id="rac_duration_type">

                                <option value="minutes">Minutes</option>

                                <option value="hours">Hours</option>

                                <option value="days">Days</option

                                </select></td>';

                            echo '<td><span><input type="text" name="rac_mail_duration" id="rac_duration"></span></td></tr>';

                            echo '<tr><td>' . __('Cart Link Anchor Text', 'recoverabandoncart') . ': </td><td><input type="text" name="rac_anchor_text" value="Cart Link" id="rac_anchor_text"></td></tr>';

                            echo '<tr><td> ' . __('Message', 'recoverabandoncart') . ':</td>';

                            echo '<td>';

                            wp_editor($content, $editor_id, $settings);

                            echo '</td></tr>';

                            echo '<tr><td><input type="button" name="rac_save_new_template" class="button button-primary button-large" id="rac_save_new_template" value="Save">&nbsp;';

                            echo '<a href="' . $template_list_url . '"><input type="button" class="button" name="returntolist" value="Return to Mail Templates"></a>&nbsp;';

                            echo '</td></tr>';

                            echo '</table>';

                            ?>

                            <script>

                                function get_tinymce_content() {

                                    if (jQuery("#wp-rac_email_template_new-wrap").hasClass("tmce-active")) {

                                        //rac_email_template_new

                                        return tinyMCE.get('rac_email_template_new').getContent();

                                        //return tinyMCE.activeEditor.getContent();

                                    } else {

                                        return jQuery("#rac_email_template_new").val();

                                    }

                                }

                                jQuery(document).ready(function () {

                                    jQuery("#rac_template_name").val("Default");

                                    jQuery("#rac_from_name").val("Admin");

                                    jQuery("#rac_sender_woo").attr("checked", "checked");

                                    jQuery(".rac_sender_opt").change(function () {

                                        if (jQuery("#rac_sender_woo").is(":checked")) {

                                            jQuery(".rac_local_senders").css("display", "none");

                                        } else {

                                            jQuery(".rac_local_senders").css("display", "table-row");

                                        }

                                    });

                                    jQuery("#rac_subject").val("Recover Abandon Cart");

                                    jQuery("#rac_from_email").val("<?php echo get_option('admin_email'); ?>");

                                    jQuery("#rac_duration_type").val("days");

                                    jQuery("#rac_template_status").val("ACTIVE");

                                    jQuery("#rac_duration").val("1");

                                    jQuery("#rac_email_template_new").val("Hi {rac.firstname}, <br><br>You have not completed your purchase.<br><br>Use the following link to make the purchase.<br>{rac.cartlink} <br><br> Thanks");

                                    jQuery("#rac_duration_type").change(function () {

                                        jQuery("span#rac_duration").html(jQuery("#rac_duration_type").val());

                                    });

                                    jQuery("#rac_save_new_template").click(function () {

                                        jQuery(this).prop("disabled", true);

                                        var rac_template_name = jQuery("#rac_template_name").val();

                                        var rac_template_status = jQuery("#rac_template_status").val();

                                        var rac_sender_option = jQuery("input:radio[name=rac_sender_opt]:checked").val();

                                        var rac_from_name = jQuery("#rac_from_name").val();

                                        var rac_from_email = jQuery("#rac_from_email").val();

                                        var rac_subject = jQuery("#rac_subject").val();

                                        var rac_anchor_text = jQuery("#rac_anchor_text").val();



                                        var rac_message = get_tinymce_content();

                                        var rac_duration_type = jQuery("#rac_duration_type").val();

                                        var rac_mail_duration = jQuery("span #rac_duration").val();

                                        console.log(jQuery("#rac_email_template_new").val());



                                        var data = {

                                            action: "rac_new_template",

                                            rac_sender_option: rac_sender_option,

                                            rac_template_name: rac_template_name,

                                            rac_template_status: rac_template_status,

                                            rac_from_name: rac_from_name,

                                            rac_from_email: rac_from_email,

                                            rac_subject: rac_subject,

                                            rac_anchor_text: rac_anchor_text,

                                            rac_message: rac_message,

                                            rac_duration_type: rac_duration_type,

                                            rac_mail_duration: rac_mail_duration

                                        };



                                        jQuery.ajax({

                                            type: "POST",

                                            url: ajaxurl,

                                            data: data

                                        }).done(function (response) {

                                            jQuery("#rac_save_new_template").prop("disabled", false);

                                            window.location.replace("<?php echo $template_list_url; ?>");

                                        });

                                        console.log(data);

                                    });

                                });</script>

                            <style>

                                .rac_local_senders{

                                    display:none;

                                }

                            </style>

                            <?php

                        } else if (isset($_GET['rac_edit_email'])) {

                            $template_id = $_GET['rac_edit_email'];

                            $edit_templates = $wpdb->get_results("SELECT * FROM $table_name WHERE id=$template_id", OBJECT);

                            $edit_templates = $edit_templates[0];

                            $admin_url = admin_url('admin.php');

                            $template_list_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail'), $admin_url));

                            $editor_id = "rac_email_template_edit";

                            $content = $edit_templates->message;

                            $settings = array('textarea_name' => 'rac_email_template_edit');

                            echo '<table class="widefat"><tr><td>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.cartlink} to insert the Cart Link in the mail</strong></span></td></tr>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.firstname} to insert Reciever First Name in the mail</strong></span></td></tr>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.lastname} to insert Reciever Last Name in the mail</strong></span></td></tr>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.Productinfo} to insert Product Information in the mail</strong></span></td></tr>';

                            echo '<tr><td colspan="2"><span><strong>Use {rac.coupon} to insert Coupon Code in the mail</strong></span></td></tr><tr><td>';

                            echo __('Template Name', 'recoverabandoncart') . ':</td>';

                            echo '<td><input type="text" name="rac_template_name" id="rac_template_name" value="' . $edit_templates->template_name . '"></td></tr>';

                            $template_active = selected($edit_templates->status, 'ACTIVE', false);

                            $template_not_active = selected($edit_templates->status, 'NOTACTIVE', false);

                            echo '<tr><td>' . __('Template Status', 'recoverabandoncart') . ':</td><td> <select name="rac_template_status" id="rac_template_status">

                                <option value="ACTIVE" ' . $template_active . '>Activated</option>

                                <option value="NOTACTIVE" ' . $template_not_active . '>Deactivated</option>

                                </select></td></tr>';

                            $woo_selected = checked($edit_templates->sender_opt, 'woo', false);

                            $local_selected = checked($edit_templates->sender_opt, 'local', false);

                            echo '<tr><td>' . __('Email Sender Option', 'recoverabandoncart') . ': </td><td><input type="radio" name="rac_sender_opt" id="rac_sender_woo" value="woo" ' . $woo_selected . ' class="rac_sender_opt">woocommerce

                                <input type="radio" name="rac_sender_opt" id="rac_sender_local" value="local" ' . $local_selected . ' class="rac_sender_opt">local</td></tr>';

                            echo '<tr class="rac_local_senders"><td>' . __('From Name', 'recoverabandoncart') . ':</td>';

                            echo '<td><input type="text" name="rac_from_name" id="rac_from_name" value="' . $edit_templates->from_name . '"></td></tr>';

                            echo '<tr class="rac_local_senders"><td>' . __('From Email', 'recoverabandoncart') . ':</td>';

                            echo '<td><input type="text" name="rac_from_email" id="rac_from_email" value="' . $edit_templates->from_email . '"></td></tr>';

                            echo '<tr><td>' . __('Subject', 'recoverabandoncart') . ':</td>';

                            echo '<td><input type="text" name="rac_subject" id="rac_subject" value="' . $edit_templates->subject . '"></td></tr>';

                            $duration_type = $edit_templates->sending_type;

                            echo '<tr><td>' . __('Send Mail Duration', 'recoverabandoncart') . ':<select name="rac_duration_type" id="rac_duration_type">

                                <option value="minutes" ' . selected($duration_type, "minutes", false) . '>Minutes</option>

                                <option value="hours" ' . selected($duration_type, "hours", false) . '>Hours</option>

                                <option value="days" ' . selected($duration_type, "days", false) . '>Days</option

                                </select>';

                            echo '</td><td><span><input type="text" name="rac_mail_duration" id="rac_duration" value="' . $edit_templates->sending_duration . '"></span></td></tr>';

                            echo '<tr><td>' . __('Cart Link Anchor Text', 'recoverabandoncart') . ': </td><td><input type="text" name="rac_anchor_text" id="rac_anchor_text" value="' . $edit_templates->anchor_text . '"></td></tr>';

                            echo '<tr><td> ' . __('Message', 'recoverabandoncart') . ':</td>';

                            echo '<td>';

                            wp_editor($content, $editor_id, $settings);

                            echo '</td></tr>';

                            echo '<tr><td>';

                            echo '<input type="button" class="button button-primary button-large" name="rac_save_new_template" id="rac_save_new_template" value="' . __('Save Changes', 'recoverabandoncart') . '">&nbsp;';

                            echo '<a href="' . $template_list_url . '"><input type="button" class="button" name="returntolist" value="' . __('Return to Mail Templates', 'recoverabandoncart') . '"></a>&nbsp;';

                            echo '</td></tr>';

                            echo '<tr><td>';

                            echo '<div id="rac_mail_result" style="display:none"> Settings Saved</div>';

                            echo '</td></tr>';

                            echo '</table>';

                            echo '<script>

function get_tinymce_content() {

                                    if (jQuery("#wp-rac_email_template_edit-wrap").hasClass("tmce-active")) {

                                        return tinyMCE.get("rac_email_template_edit").getContent();

                                    } else {

                                        return jQuery("#rac_email_template_edit").val();

                                    }

                                }

jQuery(document).ready(function(){

                                jQuery("#rac_duration_type").change(function(){

                                     jQuery("span#rac_duration").html(jQuery("#rac_duration_type").val());

                                });

                                //normal ready event

                                   if(jQuery("#rac_sender_woo").is(":checked")){

                                jQuery(".rac_local_senders").css("display","none");

                                }else{

                                jQuery(".rac_local_senders").css("display","table-row");

                                }



                                    jQuery(".rac_sender_opt").change(function(){

                                if(jQuery("#rac_sender_woo").is(":checked")){

                                jQuery(".rac_local_senders").css("display","none");

                                }else{

                                jQuery(".rac_local_senders").css("display","table-row");

                                }

                                });

                                jQuery("#rac_save_new_template").click(function(){

                                 jQuery(this).prop("disabled",true);

                                var rac_template_name = jQuery("#rac_template_name").val();

                                 var rac_template_status = jQuery("#rac_template_status").val();

                                var rac_sender_option = jQuery("input:radio[name=rac_sender_opt]:checked").val();

                                var rac_from_name = jQuery("#rac_from_name").val();

                                 var rac_from_email = jQuery("#rac_from_email").val();

                                 var rac_subject = jQuery("#rac_subject").val();

                                 var rac_anchor_text = jQuery("#rac_anchor_text").val();

                                 var rac_message = get_tinymce_content();

                                 var rac_duration_type = jQuery("#rac_duration_type").val();

                                 var rac_mail_duration = jQuery("span #rac_duration").val();

                                 var rac_template_id = ' . $template_id . '

                                console.log(jQuery("#rac_email_template_edit").val());





                                var data = {

                                action:"rac_edit_template",

                                rac_sender_option:rac_sender_option,

                                rac_template_name:rac_template_name,

                                rac_template_status:rac_template_status,

                                rac_from_name:rac_from_name,

                                rac_from_email:rac_from_email,

                                rac_subject:rac_subject,

                                rac_anchor_text:rac_anchor_text,

                                rac_message:rac_message,

                                rac_duration_type:rac_duration_type,

                                rac_mail_duration:rac_mail_duration,

                                rac_template_id:rac_template_id

                                };



                                jQuery.ajax({

                                type:"POST",

                                url:ajaxurl,

                                data:data

                                }).done(function(response){

                                 jQuery("#rac_save_new_template").prop("disabled",false);

                                 jQuery("#rac_mail_result").css("display","block");

                                });

                                console.log(data);

                                });

                                });</script>

                                ';

                        } else if (isset($_GET['rac_send_email'])) {

                            ?>

                            <table class="widefat">

                                <tr>

                                    <td><?php _e('Load Message from existing Template'); ?></td>

                                    <td><select id="rac_load_mail">

                                            <?php

                                            foreach ($templates as $key => $each_template) {

                                                if ($key == 0) {

                                                    $template_name = $each_template->template_name . '( #' . $each_template->id . ')';

                                                    echo '<option value=' . $each_template->id . ' selected>' . $template_name . '</option>';

                                                } else {

                                                    $template_name = $each_template->template_name . '( #' . $each_template->id . ')';

                                                    echo '<option value=' . $each_template->id . '>' . $template_name . '</option>';

                                                }

                                            }

                                            ?></select></td>

                                </tr>

                                <tr>

                                    <td><?php _e('Email Sender Option', 'recoverabandoncart'); ?>: </td>

                                    <td>

                                        <input type="radio" name="rac_sender_opt" id="rac_sender_woo" value="woo" <?php checked('woo', $templates[0]->sender_opt); ?>  class="rac_sender_opt">woocommerce

                                        <input type="radio" name="rac_sender_opt" id="rac_sender_local" value="local" <?php checked('local', $templates[0]->sender_opt); ?>  class="rac_sender_opt">local

                                    </td>

                                </tr>

                                <tr class="rac_local_senders">

                                    <td> <?php _e('From Name', 'recoverabandoncart'); ?>:</td>

                                    <td><input type="text" name="rac_from_name" id="rac_from_name" value="<?php echo $templates[0]->from_name; ?>"></td>

                                </tr>

                                <tr class="rac_local_senders">

                                    <td><?php _e('From Email', 'recoverabandoncart'); ?>:</td>

                                    <td><input type="text" name="rac_from_email" id="rac_from_email" value="<?php echo $templates[0]->from_email; ?>"></td>

                                </tr>

                                <tr>

                                    <td>Subject:</td>

                                    <td><input type="text" id="rac_mail_subject" name="rac_manual_mail_subject" value="<?php echo $templates[0]->subject; ?>"></td>

                                </tr>

                                <tr>

                                    <td>Cart Link Anchor Text:</td>

                                    <td><input type="text" id="rac_anchor_text" name="rac_anchor_text" value="<?php echo $templates[0]->anchor_text; ?>"></td>

                                </tr>

                                <tr>

                                    <td><?php _e('Message', 'recoverabandoncart'); ?>:</td>

                                    <?php

                                    $content = $templates[0]->message;

                                    $editor_id = "rac_manual_mail";

                                    $settings = array('textarea_name' => 'rac_manual_mail');

                                    ?>

                                    <td><?php wp_editor($content, $editor_id, $settings); ?></td>

                                </tr>

                                <tr>

                                    <td>

                                        <input type="hidden" name="rac_cart_row_ids" id="rac_cart_row_ids" value="<?php echo $_GET['rac_send_email']; ?>">

                                    </td>

                                </tr>

                                <tr>

                                    <td><input type="button" class="button-primary" name="rac_mail" id="rac_mail" value="Send Mail Now"> <span id="rac_mail_result" style="display: none;">Mail Sent Successfully</span></td>

                                </tr>

                            </table>

                            <script type="text/javascript">

                                function set_tinymce_content(value) {

                                    if (jQuery("#wp-rac_manual_mail-wrap").hasClass("tmce-active")) {

                                        return tinyMCE.activeEditor.setContent(value);

                                    } else {

                                        return jQuery("#rac_manual_mail").val(value);

                                    }

                                }

                                function get_tinymce_content_value() {

                                    if (jQuery("#wp-rac_manual_mail-wrap").hasClass("tmce-active")) {

                                        return tinyMCE.get('rac_manual_mail').getContent();

                                        //return tinyMCE.activeEditor.getContent();

                                    } else {

                                        return jQuery("#rac_manual_mail").val();

                                    }

                                }

                                jQuery(document).ready(function () {

                                    var template_id;

                                    template_id = jQuery('#rac_load_mail').val();

                                    jQuery('#rac_load_mail').change(function () {

                                        if (jQuery('#rac_load_mail').val() != 'no') {

                                            console.log(jQuery('#rac_load_mail').val());

                                            var row_id = jQuery('#rac_load_mail').val();

                                            var data = {

                                                action: 'rac_load_mail_message',

                                                row_id: row_id

                                            }

                                            jQuery.post(ajaxurl, data,

                                                    function (response) {

                                                        //alert(response);

                                                        var template = JSON.parse(response);

                                                        console.log(template.message);



                                                        set_tinymce_content(template.message);

                                                        console.log(jQuery('#rac_manual_mail').val());

                                                        jQuery("input[name=rac_sender_opt][value=" + template.mail_send_opt + "]").attr('checked', true);

                                                        jQuery("#rac_from_name").val(template.from_name);

                                                        jQuery("#rac_from_email").val(template.from_email);

                                                        jQuery("#rac_mail_subject").val(template.subject);

                                                        jQuery("#rac_anchor_text").val(template.cart_link_text);

                                                        template_id = row_id;

                                                    });

                                        }

                                    });

                                    //event for sender opt

                                    if (jQuery('#rac_sender_woo').is(':checked'))

                                    {

                                        jQuery('.rac_local_senders').hide();

                                    } else {

                                        jQuery('.rac_local_senders').show();

                                    }

                                    jQuery('input[name=rac_sender_opt]').change(function () {

                                        if (jQuery('#rac_sender_woo').is(':checked'))

                                        {

                                            jQuery('.rac_local_senders').hide();

                                        } else {

                                            jQuery('.rac_local_senders').show();

                                        }

                                    });

                                    jQuery('#rac_mail').click(function () {

                                        jQuery("#rac_mail").prop("disabled", true);

                                        var rac_message = get_tinymce_content_value();

                                        var data = {

                                            action: 'rac_manual_mail_ajax',

                                            rac_mail_row_ids: jQuery('#rac_cart_row_ids').val(),

                                            rac_sender_option: jQuery('input[name=rac_sender_opt]:radio:checked').val(),

                                            rac_anchor_text: jQuery('#rac_anchor_text').val(),

                                            rac_message: rac_message,

                                            rac_from_name: jQuery('#rac_from_name').val(),

                                            rac_from_email: jQuery('#rac_from_email').val(),

                                            rac_mail_subject: jQuery('#rac_mail_subject').val(),

                                            template_id: template_id,

                                        }

                                        console.log(data);



                                        jQuery.post(ajaxurl, data,

                                                function (response) {

                                                    jQuery("#rac_mail").prop("disabled", false);

                                                    jQuery("#rac_mail_result").css("display", "inline-block");

                                                    //alert(response);

                                                    //jQuery('#rac_manual_mail').val(response);

                                                    // tinyMCE.get('rac_manual_mail').setContent(response);

                                                    // console.log(jQuery('#rac_manual_mail').val());

                                                });



                                    });



                                });</script>

                            <?php

                        } else {

                            $admin_url = admin_url('admin.php');

                            $new_template_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail', 'rac_new_email' => 'template'), $admin_url));

                            $edit_template_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail', 'rac_edit_email' => 'template'), $admin_url));

                            echo '<a href=' . $new_template_url . '>';

                            echo '<input type="button" name="rac_new_email_template" id="rac_new_email_template" class="button" value="New Template">';

                            echo '</a>';

                            echo '&nbsp<span><select id="rac_pagination">';

                            for ($k = 1; $k <= 20; $k++) {



                                if ($k == 10) {

                                    echo '<option value="' . $k . '" selected="selected">' . $k . '</option>';

                                } else {

                                    echo '<option value="' . $k . '">' . $k . '</option>';

                                }

                            }

                            echo '</select></span>';

                            echo '&nbsp<label>Search</label><input type="text" name="rac_temp_search" id="rac_temp_search">';



                            echo '<table class="rac_email_template_table table" data-page-size="10" data-filter="#rac_temp_search" data-filter-minimum="1">

	<thead>

		<tr>

			<th data-type="numeric">' . __('ID', 'recoverabandoncart') . '</th>

			<th>' . __('Template Name', 'recoverabandoncart') . '</th>

			<th>' . __('From Name', 'recoverabandoncart') . '</th>

                        <th>' . __('From Email', 'recoverabandoncart') . '</th>

                        <th>' . __('Subject', 'recoverabandoncart') . '</th>

                        <th data-hide="phone">' . __('Message', 'recoverabandoncart') . '</th>

                        <th>' . __('Status', 'recoverabandoncart') . '</th>

		</tr>

	</thead>';

                            foreach ($templates as $each_template) {

                                echo '<tr><td>';

                                echo $each_template->id;

                                $edit_template_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail', 'rac_edit_email' => $each_template->id), $admin_url));

                                echo '&nbsp;<span><a href="' . $edit_template_url . '">' . __('Edit', 'recoverabandoncart') . ' </a></span>&nbsp; <span><a href="" class="rac_delete" data-id="' . $each_template->id . '">' . __('Delete', 'recoverabandoncart') . '</a></span>';

                                echo '</td><td>';

                                echo $each_template->template_name;

                                echo '</td><td>';

                                if ("local" == $each_template->sender_opt) {

                                    echo $each_template->from_name;

                                    echo '</td><td>';

                                    echo $each_template->from_email;

                                } else {

                                    echo get_option('woocommerce_email_from_name');

                                    echo '</td><td>';

                                    echo get_option('woocommerce_email_from_address');

                                }

                                echo '</td><td>';

                                echo $each_template->subject;

                                echo '</td><td>';

                                $message = strip_tags($each_template->message);

                                if (strlen($message) > 80) {

                                    echo substr($message, 0, 80);

                                    echo '.....';

                                } else {

                                    echo $message;

                                }

                                echo '</td>';

                                echo '<td>';

                                $mail_id = $each_template->id;

                                $status = $each_template->status;

                                if ($status == 'ACTIVE') {

                                    echo ' <a href="#" class="button rac_mail_active" data-racmailid="' . $mail_id . '" data-currentstate="ACTIVE">Deactivate</a>';

                                } else {

                                    echo ' <a href="#" class="button rac_mail_active" data-racmailid="' . $mail_id . '" data-currentstate="NOTACTIVE">Activate</a>';

                                }

                                echo '</td></tr>';

                            }

                            echo '</tbody>

            <tfoot>

		<tr>

			<td colspan="7">

				<div class="pagination pagination-centered hide-if-no-paging"></div>

			</td>

		</tr>

	</tfoot></table>';

                        }







                        break;

                    case "fpracupdate":

                        echo '<table class="form-table"><tr>

                            <th>Add WC Order which are <td><p><input type="checkbox" name="order_status[]" value="on-hold">on-hold</p>

                            <p><input type="checkbox" name="order_status[]" value="pending">Pending</p>

                            <p><input type="checkbox" name="order_status[]" value="failed" checked>Failed</p>

                            <p><input type="checkbox" name="order_status[]" value="cancelled">Cancelled</p></td>

                            </tr><tr>

                            <th>With</th><td><select id="order_time">

                            <option value="all">All time</option>

                            <option value="specific">Specific</option>

                            </td>

                            </tr>

                            <tr id="specific_row">

                            <th>Specific Time</th>

                            <td>From <input type="text" name="from_date" id="from_time" class="rac_date"> To <input type="text" id="to_time" name="to_date" class="rac_date"></td>

                            </tr>

                            <tr>

                          <td><input type="button" class="button button-primary" name="update_order" id="update_order" value="Check for Abandoned Cart"></td>

                          <td><p id="update_response"></p></td>

                            </tr>

                            </table>';

                        //ajax call

                        echo '<script>jQuery(document).ready(function(){

                              jQuery("#specific_row").css("display","none");

                            jQuery("#order_time").change(function(){

                            if(jQuery(this).val() == "specific"){

                            jQuery("#specific_row").css("display","table-row");

                            }else{

                            jQuery("#specific_row").css("display","none");

                            }

                            });

                            jQuery("#update_order").click(function(){

                            jQuery("#update_order").prop("disabled",true);

                            var rac_order_status = Array();

                            jQuery(\'input[name="order_status[]"]:checked\').each(function(index){

                            rac_order_status.push(jQuery(this).val());

                            });

                            var order_time = jQuery("#order_time").val();

                            var from_time = jQuery("#from_time").val();

                            var to_time = jQuery("#to_time").val();

                            var data = {

                                action:"rac_add_old_order",

                                rac_order_status:rac_order_status,

                                rac_order_time:order_time,

                                rac_from_time:from_time,

                                rac_to_time:to_time,

                                };

                                console.log(data);

                                  jQuery.ajax({

                                type:"POST",

                                url:ajaxurl,

                                data:data

                                }).done(function(response){

                                jQuery("#update_response").text(response);

                                 jQuery("#update_order").prop("disabled",false);

                                });

                            });





                            });</script>';

                        break;

                    case "fpracmailog":

                        RecoverAbandonCart::fp_rac_mail_logs_display();

                        break;

                    case "fpraccoupon":

                        do_action('woocommerce_fprac_settings_tabs_' . $current_tab); // @deprecated hook

                        do_action('woocommerce_fprac_settings_' . $current_tab);

                        ?>

                        <p>Use {rac.coupon} to include a coupon code in mail</p>

                        <span class="submit" style="margin-left: 25px;">

                            <?php if (!isset($GLOBALS['hide_save_button'])) : ?>

                                <input name="save" class="button-primary" style="margin-top:15px;" type="submit" value="<?php _e('Save', 'recoverabandoncart'); ?>" />

                            <?php endif; ?>

                            <input type="hidden" name="subtab" id="last_tab" />

                            <?php wp_nonce_field('woocommerce-settings'); ?>

                        </span>



                        <?php

                        break;

                    case "fpracdebug":

                        do_action('woocommerce_fprac_settings_tabs_' . $current_tab); // @deprecated hook

                        do_action('woocommerce_fprac_settings_' . $current_tab);

                        ?>

                        <h3>Test Mail</h3>

                        <table class="form-table">

                            <tr>

                                <th>Test Mail Format</th>

                                <td>

                                    <select name="rac_test_mail_format" id="rac_test_mail_format">

                                        <option value="1">Plain Text</option>

                                        <option value="2">HTML</option>

                                    </select>

                                </td>

                            </tr>



                            <tr>

                                <th>Send Test Email to </th>

                                <td><input type="text" id="testemailto" name="testemailto" value="">

                                    <input type="button" id="senttestmail" class="button button-primary" value="Send Test Email"></td>

                            </tr>

                            <tr>

                                <td colspan="2"><p id="test_mail_result" style="display:none;"></p></td>

                            </tr>

                        </table>

                        <script type="text/javascript">

                            jQuery(document).ready(function () {

                                jQuery("#senttestmail").click(function () {

                                    var data = {

                                        action: "rac_send_test_mail",

                                        rac_test_mail_to: jQuery("#testemailto").val(),

                                        rac_plain_or_html: jQuery('#rac_test_mail_format').val(),

                                    };

                                    console.log(data);

                                    var cur_button = jQuery(this);

                                    jQuery(this).prop("disabled", true);

                                    jQuery.ajax({

                                        type: "POST",

                                        url: ajaxurl,

                                        data: data

                                    }).done(function (response) {

                                        jQuery("#test_mail_result").css("display", "block");

                                        if (response == "sent") {

                                            jQuery("#test_mail_result").html("Mail has been Sent, but this doesn't mean mail will be delivered Successfully. Check Wordpress Codex for More info on Mail.");

                                        } else {

                                            jQuery("#test_mail_result").html("Mail not Sent.");

                                        }

                                        //jQuery("#update_response").text(response);

                                        cur_button.prop("disabled", false);

                                    });

                                });

                            });



                        </script>

                        <h3>Cron Schedules</h3>

                        <table class="widefat">

                            <thead>

                                <tr>

                                    <th>Mail Job hook</th>

                                    <th>Next Mail job</th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr>

                                    <td>

                                        rac_cron_job

                                    </td>

                                    <td>

                                        <?php

                                        if (wp_next_scheduled('rac_cron_job')) {

                                            echo date(get_option('date_format'), wp_next_scheduled('rac_cron_job')) . ' / ' . date(get_option('time_format'), wp_next_scheduled('rac_cron_job'));

                                        } else {

                                            echo "Cron is not set";

                                        }

                                        ?>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                        <span class="submit" style="margin-left: 25px;">

                            <?php if (!isset($GLOBALS['hide_save_button'])) : ?>

                                <input name="save" class="button-primary" style="margin-top:15px;" type="submit" value="<?php _e('Save', 'recoverabandoncart'); ?>" />

                            <?php endif; ?>

                            <input type="hidden" name="subtab" id="last_tab" />

                            <?php wp_nonce_field('woocommerce-settings'); ?>

                        </span>



                        <?php

                        break;

                    case "fpracreport" :

                        RecoverAbandonCart::fp_rac_reports();

                        break;

                    case "fpracshortocde":

                        RecoverAbandonCart::fp_rac_shortcodes_info();

                        break;

                    default :

                        do_action('woocommerce_fprac_settings_tabs_' . $current_tab); // @deprecated hook

                        do_action('woocommerce_fprac_settings_' . $current_tab);

                        $admin_url = admin_url('admin.php');

                        $reset_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'rac_reset' => 'reset'), $admin_url));

                        echo '<input class="button-secondary" id="rac_reset" type="button" name="rac_reset" value="Reset">';

                        echo '<script type="text/javascript">

                                       jQuery(document).ready(function(){

                                       jQuery("#rac_reset").click(function(){

                                       window.location.replace("' . $reset_url . '");

                                       });

                                       jQuery("#rac_admin_cart_recovered_noti").change(function(){

                                       if(jQuery(this).is(":checked")){

                                       jQuery(".admin_notification").parent().parent().show();

                                       jQuery(".admin_notifi_sender_opt").closest("tr").show();

                                       }else{

                                        jQuery(".admin_notification").parent().parent().hide();

                                        jQuery(".admin_notifi_sender_opt").closest("tr").hide();

                                       }

                                       //sender option should be refereshed as it is inside this

                                       var sender_opt = jQuery("[name=\'rac_recovered_sender_opt\']:checked").val();

                                       console.log(sender_opt);

                                       if(sender_opt == "woo"){

                                       jQuery(".local_senders").parent().parent().hide();

                                       }else{

                                        jQuery(".local_senders").parent().parent().show();

                                       }

                                       });

                                       jQuery("[name=\'rac_recovered_sender_opt\']").change(function(){

                                       var sender_opt = jQuery("[name=\'rac_recovered_sender_opt\']:checked").val();

                                       if(sender_opt == "woo"){

                                       jQuery(".local_senders").parent().parent().hide();

                                       }else{

                                        jQuery(".local_senders").parent().parent().show();

                                       }

                                       });

                                       //on ready event

                                       var sender_opt = jQuery("[name=\'rac_recovered_sender_opt\']:checked").val();

                                       console.log(sender_opt);

                                       if(sender_opt == "woo"){

                                       jQuery(".local_senders").parent().parent().hide();

                                       }else{

                                        jQuery(".local_senders").parent().parent().show();

                                       }

                                       //enable notification event

                                       if(jQuery("#rac_admin_cart_recovered_noti").is(":checked")){

                                       jQuery(".admin_notification").parent().parent().show();

                                       jQuery(".admin_notifi_sender_opt").closest("tr").show();

                                       }else{

                                        jQuery(".admin_notification").parent().parent().hide();

                                        jQuery(".admin_notifi_sender_opt").closest("tr").hide();

                                       }

                                       });</script>';

                        ?>

                        <span class="submit" style="margin-left: 25px;">

                            <?php if (!isset($GLOBALS['hide_save_button'])) : ?>

                                <input name="save" class="button-primary" type="submit" value="<?php _e('Save', 'recoverabandoncart'); ?>" />

                            <?php endif; ?>

                            <input type="hidden" name="subtab" id="last_tab" />

                            <?php wp_nonce_field('woocommerce-settings'); ?>

                        </span>

                        <?php

                        break;

                }

                ?>





            </form>

        </div>



        <script type="text/javascript">



            jQuery(document).ready(function ()

            {

                if (jQuery('#rac_remove_carts').is(":checked")) {



                    jQuery('#rac_remove_new').parent().parent().parent().parent().css("display", "table-row");

                    jQuery('#rac_remove_abandon').parent().parent().parent().parent().css("display", "table-row");



                }

                else {

                    jQuery('#rac_remove_new').parent().parent().parent().parent().css("display", "none");

                    jQuery('#rac_remove_abandon').parent().parent().parent().parent().css("display", "none");

                }



                jQuery('#rac_remove_carts').change(function ()

                {



                    if (this.checked) {





                        jQuery('#rac_remove_new').parent().parent().parent().parent().css("display", "table-row");

                        jQuery('#rac_remove_abandon').parent().parent().parent().parent().css("display", "table-row");



                    }

                    else {

                        jQuery('#rac_remove_new').parent().parent().parent().parent().css("display", "none");

                        jQuery('#rac_remove_abandon').parent().parent().parent().parent().css("display", "none");

                    }



                });



            });





        </script>





        <?php

    }



    public static function fp_rac_menu_options_general() {

        $admin_mail = get_option('admin_email');

        return apply_filters('woocommerce_fpwcctsingle_settings', array(

            array(

                'name' => __('Time Settings', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_time_settings',

                'clone_id' => '',

            ),

            array(

                'name' => __('Abandon Cart Time Type for Members', 'recoverabandoncart'),

                'desc' => __('Please Select wether the time should be in Minutes/Hours/Days for Members', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_abandon_cart_time_type',

                'css' => 'min-width:150px;',

                'type' => 'select',

                'desc_tip' => true,

                'options' => array('minutes' => 'Minutes', 'hours' => 'Hours', 'days' => 'Days'),

                'std' => 'hours',

                'default' => 'hours',

                'clone_id' => 'rac_abandon_cart_time_type',

            ),

            array(

                'name' => __('Abandon Cart Time for Members', 'recoverabandoncart'),

                'desc' => __('Please Enter time after which the cart should be considered as abandon for Members', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_abandon_cart_time',

                'css' => 'min-width:150px;',

                'type' => 'text',

                'desc_tip' => true,

                'std' => '1',

                'default' => '1',

                'clone_id' => 'rac_abandon_cart_time',

            ),

            array(

                'name' => __('Abandon Cart Time Type for Guest', 'recoverabandoncart'),

                'desc' => __('Please Select wether the time should be in Minutes/Hours/Days for Guest', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_abandon_cart_time_type_guest',

                'css' => 'min-width:150px;',

                'type' => 'select',

                'desc_tip' => true,

                'options' => array('minutes' => 'Minutes', 'hours' => 'Hours', 'days' => 'Days'),

                'std' => 'hours',

                'default' => 'hours',

                'clone_id' => 'rac_abandon_cart_time_type_guest',

            ),

            array(

                'name' => __('Abandon Cart Time for Guest', 'woocommercecustomtext'),

                'desc' => __('Please Enter time after which the cart should be considered as abandon for guest', 'woocommercecustomtext'),

                'tip' => '',

                'id' => 'rac_abandon_cart_time_guest',

                'css' => 'min-width:150px;',

                'type' => 'text',

                'desc_tip' => true,

                'std' => '1',

                'default' => '1',

                'clone_id' => 'rac_abandon_cart_time_guest',

            ),

            array('type' => 'sectionend', 'id' => 'rac_time_settings'), //Time Settings END

            array(

                'name' => __('Mail Cron Settings', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_cron_settings',

                'clone_id' => '',

            ),

            array(

                'name' => __('Mail Cron Time Type', 'recoverabandoncart'),

                'desc' => __('Please Select wether the time should be in Minutes/Hours/Days', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_abandon_cart_cron_type',

                'css' => 'min-width:150px;',

                'type' => 'select',

                'desc_tip' => true,

                'options' => array('minutes' => 'Minutes', 'hours' => 'Hours', 'days' => 'Days'),

                'std' => 'hours',

                'default' => 'hours',

                'clone_id' => 'rac_abandon_cart_cron_type',

            ),

            array(

                'name' => __('Mail Cron Time', 'recoverabandoncart'),

                'desc' => __('Please Enter time after which Email cron job should run', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_abandon_cron_time',

                'css' => 'min-width:150px;',

                'type' => 'text',

                'desc_tip' => true,

                'std' => '12',

                'default' => '12',

                'clone_id' => 'rac_abandon_cron_time',

            ),

            array('type' => 'sectionend', 'id' => 'rac_cron_settings'), //Cron Settings END

            array(

                'name' => __('Notification Settings', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_notification_settings',

                'clone_id' => '',

            ),

            array(

                'name' => __('Enable Email Notification for Admin when Cart is Recovered', 'recoverabandoncart'),

                'desc' => __(''),

                'id' => 'rac_admin_cart_recovered_noti',

                'std' => 'no',

                'default' => 'no',

                'type' => 'checkbox',

                'newids' => 'rac_admin_cart_recovered_noti',

            ),

            array(

                'name' => __('Admin Email ID', 'recoverabandoncart'),

                'desc' => __(''),

                'id' => 'rac_admin_email',

                'std' => $admin_mail,

                'default' => $admin_mail,

                'type' => 'text',

                'newids' => 'rac_admin_email',

                'class' => 'admin_notification'

            ),

            array(

                'name' => __('Notification Sender Option', 'recoverabandoncart'),

                'desc' => __(''),

                'id' => 'rac_recovered_sender_opt',

                'std' => "woo",

                'default' => "woo",

                'type' => 'radio',

                'newids' => 'rac_recovered_sender_opt',

                'class' => 'admin_sender_opt',

                'options' => array('woo' => 'WooCommerce', 'local' => 'Local'),

                'class' => 'admin_notifi_sender_opt'

            ),

            array(

                'name' => __('Notification From Name', 'recoverabandoncart'),

                'desc' => __(''),

                'id' => 'rac_recovered_from_name',

                'std' => "",

                'default' => "",

                'type' => 'text',

                'newids' => 'rac_recovered_from_name',

                'class' => 'local_senders admin_notification'

            ),

            array(

                'name' => __('Notification From Email', 'recoverabandoncart'),

                'desc' => __(''),

                'id' => 'rac_recovered_from_email',

                'std' => "",

                'default' => "",

                'type' => 'text',

                'newids' => 'rac_recovered_from_email',

                'class' => 'local_senders admin_notification'

            ),

            array(

                'name' => __('Notification Email Subject', 'recoverabandoncart'),

                'desc' => __(''),

                'id' => 'rac_recovered_email_subject',

                'std' => "A cart has been Recovered",

                'default' => "A cart has been Recovered",

                'type' => 'text',

                'newids' => 'rac_recovered_email_subject',

                'class' => 'admin_notification'

            ),

            array(

                'name' => __('Notification Email Message', 'recoverabandoncart'),

                'desc' => __(''),

                'css' => 'min-height:250px;min-width:400px;',

                'id' => 'rac_recovered_email_message',

                'std' => "A cart has been Recovered. Here is the order ID {rac.recovered_order_id} for Reference and Line Items is here {rac.order_line_items}.",

                'default' => "A cart has been Recovered. Here is the order ID {rac.recovered_order_id} for Reference and Line Items is here {rac.order_line_items}.",

                'type' => 'textarea',

                'newids' => 'rac_recovered_email_message',

                'class' => 'admin_notification'

            ),

            array('type' => 'sectionend', 'id' => 'rac_notification_settings'), //Notification Settings END

            array(

                'name' => __('Carts List Settings', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_cartlist_settings',

                'clone_id' => '',

            ),

            array(

                'name' => __('Remove NEW and ABANDON Carts Previously by same Users', 'recoverabandoncart'),

                'desc' => __('Enabling this option will remove New and Abandon Carts by same Users', 'recoverabandoncart'),

                'type' => 'checkbox',

                'default' => 'yes',

                'std' => 'yes',

                'id' => 'rac_remove_carts',

                'clone_id' => 'rac_remove_carts',

            ),

            array(

                'name' => __('Remove Carts with "NEW" Status', 'recoverabandoncart'),

                'desc' => __('Enabling this option will remove New Carts by same Users', 'recoverabandoncart'),

                'type' => 'checkbox',

                'default' => 'yes',

                'std' => 'yes',

                'id' => 'rac_remove_new',

                'clone_id' => 'rac_remove_new',

            ),

            array(

                'name' => __('Remove Carts with "ABANDON" Status', 'recoverabandoncart'),

                'desc' => __('Enabling this option will remove Abandon Carts by same Users', 'recoverabandoncart'),

                'type' => 'checkbox',

                'default' => 'yes',

                'std' => 'yes',

                'id' => 'rac_remove_abandon',

                'clone_id' => 'rac_remove_abandon',

            ),

            array('type' => 'sectionend', 'id' => 'rac_cartlist_settings'), //Carts List Settings END

            array(

                'name' => __('Guest Cart Settings', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_guestcart_settings',

                'clone_id' => '',

            ),

            array(

                'name' => __('Remove Guest Cart when the Order Status Changes to Pending', 'recoverabandoncart'),

                'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Pending'),

                'id' => 'rac_guest_abadon_type_pending',

                'std' => 'no',

                'default' => 'no',

                'type' => 'checkbox',

                'newids' => 'rac_guest_abadon_type_pending',

            ),

            array(

                'name' => __('Remove Guest Cart when the Order Status Changes to Failed', 'recoverabandoncart'),

                'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Failed'),

                'id' => 'rac_guest_abadon_type_failed',

                'std' => 'no',

                'default' => 'no',

                'type' => 'checkbox',

                'newids' => 'rac_guest_abadon_type_failed',

            ),

            array(

                'name' => __('Remove Guest Cart when the Order Status Changes to On-Hold', 'recoverabandoncart'),

                'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become On-Hold'),

                'id' => 'rac_guest_abadon_type_on-hold',

                'std' => 'no',

                'default' => 'no',

                'type' => 'checkbox',

                'newids' => 'rac_guest_abadon_type_on-hold',

            ),

            array(

                'name' => __('Remove Guest Cart when the Order Status Changes to Processing', 'recoverabandoncart'),

                'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Processing'),

                'id' => 'rac_guest_abadon_type_processing',

                'std' => 'yes',

                'default' => 'yes',

                'type' => 'checkbox',

                'newids' => 'rac_guest_abadon_type_processing',

            ),

            array(

                'name' => __('Remove Guest Cart when the Order Status Changes to Completed', 'recoverabandoncart'),

                'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Completed'),

                'id' => 'rac_guest_abadon_type_completed',

                'std' => 'yes',

                'default' => 'yes',

                'type' => 'checkbox',

                'newids' => 'rac_guest_abadon_type_completed',

            ),

            array(

                'name' => __('Remove Guest Cart when the Order Status Changes to Refunded', 'recoverabandoncart'),

                'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Refunded'),

                'id' => 'rac_guest_abadon_type_refunded',

                'std' => 'no',

                'default' => 'no',

                'type' => 'checkbox',

                'newids' => 'rac_guest_abadon_type_refunded',

            ),

            array(

                'name' => __('Remove Guest Cart when the Order Status Changes to Cancelled', 'recoverabandoncart'),

                'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Cancelled'),

                'id' => 'rac_guest_abadon_type_cancelled',

                'std' => 'no',

                'default' => 'no',

                'type' => 'checkbox',

                'newids' => 'rac_guest_abadon_type_cancelled',

            ),

            array('type' => 'sectionend', 'id' => 'rac_guestcart_settings'), //Cart Abadoned Guest Settings END

        ));

    }



    public static function fprac_default_settings() {

        global $woocommerce;

        foreach (RecoverAbandonCart::fp_rac_menu_options_general() as $setting)

            if (isset($setting['id']) && isset($setting['std'])) {

// var_dump($setting);

                add_option($setting['id'], $setting['std']);

            }

        foreach (RecoverAbandonCart::fp_rac_menu_options_email() as $setting)

            if (isset($setting['id']) && isset($setting['std'])) {

// var_dump($setting);

                add_option($setting['id'], $setting['std']);

            }

        foreach (RecoverAbandonCart::fp_rac_menu_options_troubleshoot() as $setting)

            if (isset($setting['id']) && isset($setting['std'])) {

// var_dump($setting);

                add_option($setting['id'], $setting['std']);

            }

        foreach (RecoverAbandonCart::fp_rac_menu_options_coupon_gen() as $setting)

            if (isset($setting['id']) && isset($setting['std'])) {

// var_dump($setting);

                add_option($setting['id'], $setting['std']);

            }

    }



    public static function fp_rac_menu_options_email() {

        global $woocommerce;



        global $wp_roles;

        foreach ($wp_roles->role_names as $key => $value) {

            $userrole[] = $key;

            $username[] = $value;

        }



        $user_role = array_combine((array) $userrole, (array) $username);



        return apply_filters('woocommerce_fpracemail_settings', array(

            array(

                'name' => __('Email Settings', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_email_gen_settings',

                'clone_id' => '',

            ),

            array(

                'name' => __('Use Plain RTF Email', 'recoverabandoncart'),

                'desc' => __('Enabling this option will send mail in Plain RTF', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_email_use_temp_plain',

                'css' => '',

                'type' => 'checkbox',

                'desc_tip' => true,

                'std' => 'no',

                'default' => 'no',

                'clone_id' => 'rac_email_use_temp_plain',

            ),

            array(

                'name' => __('Send Email to Members', 'recoverabandoncart'),

                'desc' => __('Enabling this option will send mail to Members only', 'recoverabandoncart'),

                'type' => 'checkbox',

                'default' => 'yes',

                'std' => 'yes',

                'id' => 'rac_email_use_members',

                'clone_id' => 'rac_email_use_members',

            ),

            array(

                'name' => __('Send Email to Guests', 'recoverabandoncart'),

                'desc' => __('Enabling this option will send mail to Guests only', 'recoverabandoncart'),

                'type' => 'checkbox',

                'default' => 'yes',

                'std' => 'yes',

                'id' => 'rac_email_use_guests',

                'clone_id' => 'rac_email_use_guests',

            ),

            array(

                'name' => __('Custom Exclude Settings', 'recoverabandoncart'),

                'desc' => __('Selected User Roles, Names and Email ID To Stop Mail Sending', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'custom_exclude',

                'css' => 'min-width:153px',

                'type' => 'select',

                'options' => array('user_role' => 'User Role', 'name' => 'Name', 'mail_id' => 'Mail ID'),

                'std' => 'user_role',

                'default' => 'user_role',

                'clone_id' => 'custom_exclude',

                'desc_tip' => true,

            ),

            array(

                'name' => __('Select User Role', 'recoverabandoncart'),

                'desc' => __('Type User Role First Three Characters Only', 'recoverabandoncart'),

                'id' => 'custom_user_role',

                'css' => 'min-width:150px',

                'type' => 'multiselect',

                'std' => '',

                'options' => $user_role,

                'clone_id' => 'custom_user_role',

                'desc_tip' => true,

            ),

            array(

                'name' => __('User Name Selected', 'recoverabandoncart'),

                'desc' => __('Type User Name First Three Characters Only', 'recoverabandoncart'),

                'id' => 'custom_user_name_select',

                'css' => 'min-width:400px',

                'std' => '',

                'type' => 'rac_exclude_users_list',

                'clone_id' => 'custom_user_name_select',

                'desc_tip' => true,

            ),

            array(

                'name' => __('Custom Mail ID Selected', 'recoverabandoncart'),

                'desc' => __('Enter Mail ID per line which will be excluded to receive a mail from Recover Abandon Cart', 'recoverabandoncart'),

                'id' => 'custom_mailid_edit',

                'clone_id' => 'custom_mailid_edit',

                'type' => 'textarea',

                'css' => 'min-width:500px;min-height:200px',

                'std' => '',

                'desc_tip' => true,

            ),

            array('type' => 'sectionend', 'id' => 'rac_email_gen_settings'), //Email Settings END

            array(

                'name' => __('Email Template Cart Link Settings', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_cart_link_customization',

            ),

            array(

                'name' => __('Cart Link', 'recoverabandoncart'),

                'desc' => __('Customize the Cart Link in Email Template', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_cart_link_options',

                'css' => '',

                'type' => 'select',

                'desc_tip' => true,

                'std' => '1',

                'default' => '1',

                'options' => array(

                    '1' => __('Hyperlink', 'recoverabandoncart'),

                    '2' => __('URL', 'recoverabandoncart'),

                    '3' => __('Button', 'recoverabandoncart'),

                ),

                'clone_id' => 'rac_cart_link_options',

            ),

            array(

                'name' => __('Button Background Color', 'recoverabandoncart'),

                'desc' => __('Customize Button Background Color in Email', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_cart_button_bg_color',

                'class' => 'color racbutton',

                'css' => '',

                'type' => 'text',

                'desc_tip' => true,

                'std' => '000091',

                'default' => '000091',

                'clone_id' => 'rac_cart_button_bg_color',

            ),

            array(

                'name' => __('Button Link Color', 'recoverabandoncart'),

                'desc' => __('Customize Button Link Color', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_cart_button_link_color',

                'class' => 'color racbutton',

                'css' => '',

                'type' => 'text',

                'desc_tip' => true,

                'std' => 'ffffff',

                'default' => 'ffffff',

                'clone_id' => 'rac_cart_button_link_color',

            ),

            array(

                'name' => __('Link Color', 'recoverabandoncart'),

                'desc' => __('Customize Link Color in Email Template', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_email_link_color',

                'class' => 'color raclink',

                'css' => '',

                'type' => 'text',

                'desc_tip' => true,

                'std' => '1919FF',

                'default' => '1919FF',

                'clone_id' => 'rac_email_link_color',

            ),

            array('type' => 'sectionend', 'id' => 'rac_cart_link_customization'),

            array(

                'name' => __('Unsubscribe Settings', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_email_unsubscription',

                'clone_id' => '',

            ),

            array(

                'name' => __('Unsubscription Link in Email', 'recoverabandoncart'),

                'desc' => __('Enable', 'recoverabandoncart'),

                'id' => 'fp_unsubscription_link_in_email',

                'clone_id' => 'fp_unsubscription_link_in_email',

                'type' => 'checkbox',

                'default' => 'no',

                'std' => 'no',

                'desc_tip' => '',

            ),

            array(

                'name' => __("Unsubscription Link Text", 'recoverabandoncart'),

                'desc' => __('Enter Unsubscription Link Text in Footer', 'recoverabandoncart'),

                'id' => 'fp_unsubscription_footer_link_text',

                'clone_id' => 'fp_unsubscription_footer_link_text',

                'type' => 'text',

                'default' => 'Unsubscribe',

                'std' => 'Unsubscribe',

                'desc_tip' => true,

            ),

            array(

                'name' => __('Unsubscription Message', 'recoverabandoncart'),

                'desc' => __('Enter Unsubscription Message which is visible in Email Footer', 'recoverabandoncart'),

                'id' => 'fp_unsubscription_footer_message',

                'clone_id' => 'fp_unsubscription_footer_message',

                'type' => 'textarea',

                'default' => 'You can {rac_unsubscribe} to stop Receiving Abandon Cart Mail from {rac_site}',

                'std' => 'You can {rac_unsubscribe} to stop Receiving Abandon Cart Mail from {rac_site}',

                'desc_tip' => true,

            ),

            array('type' => 'sectionend', 'id' => 'rac_email_unsubscription'),

            array(

                'name' => __('Customize Caption and Visibility for Product Info in Email Template', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => 'Following Customization options works with the shortcode {rac.Productinfo} in Email Template',

                'id' => 'rac_customize_caption_in_product_info',

                'clone_id' => '',

            ),

            array(

                'name' => __('Customize Product Name Caption in Email Template', 'recoverabandoncart'),

                'desc' => __('Customize Product Name Caption in Email Template', 'recoverabandoncart'),

                'type' => 'text',

                'default' => 'Product Name',

                'std' => 'Product Name',

                'id' => 'rac_product_info_product_name',

                'clone_id' => 'rac_product_info_product_name',

            ),

            array(

                'name' => __('Customize Product Image Caption in Email Template', 'recoverabandoncart'),

                'desc' => __('Customize Product Image Caption in Email Template', 'recoverabandoncart'),

                'type' => 'text',

                'default' => 'Product Image',

                'std' => 'Product Image',

                'id' => 'rac_product_info_product_image',

                'clone_id' => 'rac_product_info_product_image',

            ),

            array(

                'name' => __('Customize Product Price Caption in Email Template', 'recoverabandoncart'),

                'desc' => __('Customize Product Price Caption in Email Template', 'recoverabandoncart'),

                'type' => 'text',

                'default' => 'Product Price',

                'std' => 'Product Price',

                'id' => 'rac_product_info_product_price',

                'clone_id' => 'rac_product_info_product_price',

            ),

            array(

                'name' => __('Hide Product Name Column in Product Info Shortcode', 'recoverabandoncart'),

                'desc' => __('Hide Product Name Column in Product Info Shortcode', 'recoverabandoncart'),

                'type' => 'checkbox',

                'default' => 'no',

                'std' => 'no',

                'id' => 'rac_hide_product_name_product_info_shortcode',

                'clone_id' => 'rac_hide_product_name_product_info_shortcode',

            ),

            array(

                'name' => __("Hide Product Image Column in Product Info Shortcode", 'recoverabandoncart'),

                'desc' => __('Hide Product Image Column in Product Info Shortcode', 'recoverabandoncart'),

                'type' => 'checkbox',

                'default' => 'no',

                'std' => 'no',

                'id' => 'rac_hide_product_image_product_info_shortcode',

                'clone_id' => 'rac_hide_product_image_product_info_shortcode',

            ),

            array(

                'name' => __('Hide Product Price Column in Product Info Shortcode', 'recoverabandoncart'),

                'desc' => __('Hide Product Price Column in Product Info Shortcode', 'recoverabandoncart'),

                'type' => 'checkbox',

                'default' => 'no',

                'std' => 'no',

                'id' => 'rac_hide_product_price_product_info_shortcode',

                'clone_id' => 'rac_hide_product_price_product_info_shortcode',

            ),

            array('type' => 'sectionend', 'id' => 'rac_email_gen_settings'),

        ));

    }



    public static function fp_rac_menu_options_troubleshoot() {

        $defaultval = "webmaster@" . $_SERVER['SERVER_NAME'];

        return apply_filters('woocommerce_fpwcctsingle_settings', array(

            array(

                'name' => __('Troubleshooting', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_troubleshoot',

                'clone_id' => '',

            ),

            array(

                'name' => __('Use Mail Function', 'recoverabandoncart'),

                'desc' => __('Please Select which mail function to use while sending notification', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_trouble_mail',

                'css' => 'min-width:150px;',

                'type' => 'select',

                'desc_tip' => true,

                'options' => array('mail' => 'mail()', 'wp_mail' => 'wp_mail()'),

                'std' => 'wp_mail',

                'default' => 'wp_mail',

                'clone_id' => 'rac_trouble_mail',

            ),

            array(

                'name' => __('Use Mail Troubleshoot', 'recoverabandoncart'),

                'desc' => __('Please select this option to check whether select troubleshoot option from Mail Sending ', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_webmaster_mail',

                'css' => 'min-width:150px;',

                'type' => 'select',

                'desc_tip' => true,

                'options' => array('webmaster1' => 'Enable', 'webmaster2' => 'Disable'),

                'std' => 'webmaster2',

                'default' => 'webmaster2',

                'clone_id' => 'rac_webmaster_mail',

            ),

            array(

                'name' => __('Use Email as Fifth Parameter', 'recoverabandoncart'),

                'desc' => __(''),

                'id' => 'rac_textarea_mail',

                'std' => $defaultval,

                'default' => $defaultval,

                'type' => 'text',

                'newids' => 'rac_textarea_mail',

                'class' => ''

            ),

            array('type' => 'sectionend', 'id' => 'rac_troubleshoot'), //Time Settings END

        ));

    }



    public static function fp_rac_troubleshoot_mailsend() {

        global $woocommerce;

        if (isset($_GET['tab'])) {



            if ($_GET['tab'] == 'fpracdebug') {

                ?>



                <script type="text/javascript">



                    jQuery(document).ready(function () {

                <?php if ((float) $woocommerce->version > (float) ('2.2.0')) { ?>

                            var troubleemail = jQuery('#rac_trouble_mail').val();

                            if (troubleemail === 'mail') {

                                jQuery('.prependedrc').remove();

                                jQuery('#rac_trouble_mail').parent().append('<span class="prependedrc">For WooCommerce 2.3 or higher version mail() function will not load the woocommerce default template. This option will be deprecated </span>');

                            } else {

                                jQuery('.prependedrc').remove();

                            }

                            jQuery('#rac_trouble_mail').change(function () {

                                if (jQuery(this).val() === 'mail') {

                                    jQuery('.prependedrc').remove();

                                    jQuery('#rac_trouble_mail').parent().append('<span class="prependedrc">For WooCommerce 2.3 or higher version mail() function will not load the woocommerce default template. This option will be deprecated </span>');

                                } else {

                                    jQuery('.prependedrc').remove();

                                }

                            });



                <?php } ?>



                        if (jQuery('#rac_webmaster_mail').val() == 'webmaster1') {

                            jQuery("#rac_textarea_mail").parent().parent().show();



                        }

                        else {

                            //Hide text box here

                            jQuery("#rac_textarea_mail").parent().parent().hide();

                        }

                        jQuery("#rac_webmaster_mail").change(function () {

                            if (jQuery(this).val() == 'webmaster1') {

                                jQuery("#rac_textarea_mail").parent().parent().show();



                            }

                            else {

                                //Hide text box here

                                jQuery("#rac_textarea_mail").parent().parent().hide();

                            }

                        });

                    });

                </script>

                <?php

            }

        }

    }



    public static function rac_selected_users_exclude_option() {

        global $woocommerce;

        ?>

        <script type="text/javascript">

        <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>

                jQuery(function () {

                    jQuery('select.custom_user_name_select').ajaxChosen({

                        method: 'GET',

                        url: '<?php echo admin_url('admin-ajax.php'); ?>',

                        dataType: 'json',

                        afterTypeDelay: 100,

                        data: {

                            action: 'woocommerce_json_search_customers',

                            security: '<?php echo wp_create_nonce("search-customers"); ?>'

                        }

                    }, function (data) {

                        var terms = {};



                        jQuery.each(data, function (i, val) {

                            terms[i] = val;

                        });

                        return terms;

                    });

                });

        <?php } ?>

        </script>





        <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>

            <tr valign="top">

                <th class="titledesc" scope="row">

                    <label for="custom_user_name_select"><?php _e('User Name Selected', 'recoverabandoncart'); ?></label>

                </th>

                <td>

                    <select name="custom_user_name_select[]" multiple="multiple" id="custom_user_name_select" class="short custom_user_name_select">

                        <?php

                        $json_ids = array();

                        $getuser = get_option('custom_user_name_select');

                        if ($getuser != "") {

                            $listofuser = $getuser;

                            if (!is_array($listofuser)) {

                                $userids = array_filter(array_map('absint', (array) explode(',', $listofuser)));

                            } else {

                                $userids = $listofuser;

                            }



                            foreach ($userids as $userid) {

                                $user = get_user_by('id', $userid);

                                ?>

                                <option value="<?php echo $userid; ?>" selected="selected"><?php echo esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email); ?></option>

                                <?php

                            }

                        }

                        ?>

                    </select>

                </td>

            </tr>

        <?php } else { ?>

            <tr valign="top">

                <th class="titledesc" scope="row">

                    <label for="custom_user_name_select"><?php _e('User Name Selected', 'recoverabandoncart'); ?></label>

                </th>

                <td>

                    <input type="hidden" class="wc-customer-search" name="custom_user_name_select" id="custom_user_name_select" data-multiple="true" data-placeholder="<?php _e('Search for a customer&hellip;', 'recoverabandoncart'); ?>" data-selected="<?php

                    $json_ids = array();

                    $getuser = get_option('custom_user_name_select');

                    if ($getuser != "") {

                        $listofuser = $getuser;

                        if (!is_array($listofuser)) {

                            $userids = array_filter(array_map('absint', (array) explode(',', $listofuser)));

                        } else {

                            $userids = $listofuser;

                        }



                        foreach ($userids as $userid) {

                            $user = get_user_by('id', $userid);

                            $json_ids[$user->ID] = esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email);

                        }echo esc_attr(json_encode($json_ids));

                    }

                    ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" data-allow_clear="true" />

                </td>

            </tr>

            <?php

        }

    }



    public static function fp_rac_menu_options_coupon_gen() {

        $categorylist = array();

        $categoryname = array();

        $categoryid = array();

        $particularcategory = get_terms('product_cat');

        if (!is_wp_error($particularcategory)) {

            if (!empty($particularcategory)) {

                if (is_array($particularcategory)) {

                    foreach ($particularcategory as $category) {

                        $categoryname[] = $category->name;

                        $categoryid[] = $category->term_id;

                    }

                }

                $categorylist = array_combine((array) $categoryid, (array) $categoryname);

            }

        }

        return apply_filters('woocommerce_fpraccoupon_settings', array(

            array(

                'name' => __('Coupon Code', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_coupon',

                'clone_id' => '',

            ),

            array(

                'name' => __('Prefix Text of Coupon Code', 'recoverabandoncart'),

                'desc' => __('Select Prefix Text in Coupon Code', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_prefix_coupon',

                'css' => '',

                'desc_tip' => true,

                'type' => 'select',

                'options' => array(

                    '1' => __('Default', 'recoverabandoncart'),

                    '2' => __('Custom', 'recoverabandoncart'),

                ),

                'std' => '1',

                'default' => '1',

                'clone_id' => 'rac_prefix_coupon',

            ),

            array(

                'name' => __('Custom Prefix Text of Coupon Code', 'recoverabandoncart'),

                'desc' => __('Enter Custom Prefix Text for Coupon Code', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_manual_prefix_coupon_code',

                'css' => 'rac_manual_prefix',

                'desc_tip' => true,

                'type' => 'text',

                'std' => '',

                'default' => '',

                'clone_id' => 'rac_manual_prefix_coupon_code',

            ),

            array(

                'name' => __('Type of Discount', 'recoverabandoncart'),

                'desc' => __('Please Select which type of dicount should be applied', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_coupon_type',

                'css' => 'min-width:150px;',

                'type' => 'select',

                'desc_tip' => true,

                'options' => array('fixed_cart' => 'Amount', 'percent' => 'Percentage'),

                'std' => 'fixed_cart',

                'default' => 'fixed_cart',

                'clone_id' => 'rac_coupon_type',

            ),

            array(

                'name' => __('Value', 'recoverabandoncart'),

                'desc' => __('Enter value to reduce in currency or % based on the Type of Discount Selected without any Symbols'),

                'tip' => '',

                'desc_tip' => true,

                'id' => 'rac_coupon_value',

                'std' => "",

                'default' => "",

                'type' => 'text',

                'newids' => 'rac_coupon_value',

                'class' => ''

            ),

            array(

                'name' => __('Validity in Days', 'recoverabandoncart'),

                'desc' => __('Enter a value(days in number) for how long the coupon Should be Active'),

                'desc_tip' => true,

                'id' => 'rac_coupon_validity',

                'std' => "7",

                'default' => "7",

                'type' => 'text',

                'newids' => 'rac_coupon_validity',

                'class' => ''

            ),

            array(

                'name' => __('Minimum Amount for Coupon Usage', 'recoverabandoncart'),

                'id' => 'rac_minimum_spend',

                'std' => '',

                'default' => '',

                'type' => 'text',

                'newids' => 'rac_minimum_spend',

                'class' => '',

            ),

            array(

                'name' => __('Maximum Amount for Coupon Usage', 'recoverabandoncart'),

                'id' => 'rac_maximum_spend',

                'std' => '',

                'default' => '',

                'type' => 'text',

                'newids' => 'rac_maximum_spend',

                'class' => '',

            ),

            array(

                'type' => 'rac_coupon_include_products',

            ),

            array(

                'type' => 'rac_coupon_exclude_products',

            ),

            array(

                'name' => __('Select Category', 'recoverabandoncart'),

                'desc' => __('Select Category to enable redeeming', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_select_category_to_enable_redeeming',

                'class' => 'rac_select_category_to_enable_redeeming',

                'css' => 'min-width:350px',

                'std' => '',

                'type' => 'multiselect',

                'newids' => 'rac_select_category_to_enable_redeeming',

                'options' => $categorylist,

                'desc_tip' => true,

            ),

            array(

                'name' => __('Exclude Category', 'recoverabandoncart'),

                'desc' => __('Exclude Category to enable redeeming', 'recoverabandoncart'),

                'tip' => '',

                'id' => 'rac_exclude_category_to_enable_redeeming',

                'class' => 'rac_exclude_category_to_enable_redeeming',

                'css' => 'min-width:350px',

                'std' => '',

                'type' => 'multiselect',

                'newids' => 'rac_exclude_category_to_enable_redeeming',

                'options' => $categorylist,

                'desc_tip' => true,

            ),

            array('type' => 'sectionend', 'id' => 'rac_coupon'), //Coupon Settings END

            array(

                'name' => __('Coupon Code Deletion', 'recoverabandoncart'),

                'type' => 'title',

                'desc' => '',

                'id' => 'rac_coupon_deletion',

                'clone_id' => '',

            ),

            array(

                'name' => __('Delete Coupons after Used', 'recoverabandoncart'),

                'desc' => __('Delete Coupons which are automatically created by Recover Abandoned Cart that are Used'),

                'id' => 'rac_delete_coupon_after_use',

                'std' => 'no',

                'default' => 'no',

                'type' => 'checkbox',

                'newids' => 'rac_delete_coupon_after_use',

            ),

            array(

                'name' => __('Delete Coupons after Expired', 'recoverabandoncart'),

                'desc' => __('Delete Coupons which are automatically created by Recover Abandoned Cart that are Expired'),

                'id' => 'rac_delete_coupon_expired',

                'std' => 'no',

                'default' => 'no',

                'type' => 'checkbox',

                'newids' => 'rac_delete_coupon_expired',

            ),

            array('type' => 'sectionend', 'id' => 'rac_coupon_deletion'), //Coupon Settings END

        ));

    }



    public static function fp_rac_admin_setting_general() {

        woocommerce_admin_fields(RecoverAbandonCart::fp_rac_menu_options_general());

    }



    public static function fp_rac_update_options_general() {

        woocommerce_update_options(RecoverAbandonCart::fp_rac_menu_options_general());

    }



    public static function fp_rac_admin_setting_troubleshoot() {

        woocommerce_admin_fields(RecoverAbandonCart::fp_rac_menu_options_troubleshoot());

    }



    public static function fp_rac_update_options_troubleshoot() {

        woocommerce_update_options(RecoverAbandonCart::fp_rac_menu_options_troubleshoot());

    }



    public static function fp_rac_admin_setting_email() {

        woocommerce_admin_fields(RecoverAbandonCart::fp_rac_menu_options_email());

    }



    public static function fp_rac_update_options_email() {

        woocommerce_update_options(RecoverAbandonCart::fp_rac_menu_options_email());

    }



    public static function fp_rac_admin_setting_coupon() {

        woocommerce_admin_fields(self::fp_rac_menu_options_coupon_gen());

    }



    public static function fp_rac_update_options_coupon() {

        woocommerce_update_options(self::fp_rac_menu_options_coupon_gen());

    }



    public static function fp_rac_create_table() {

        global $wpdb;

        $currentdbversion = '2.1';

        $olddbversion = get_option('rac_db_version');

        if (!$olddbversion) {

            add_option('rac_db_version', '1.0.0');

        }

        if ($currentdbversion != get_option('rac_db_version')) {

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $table_name = $wpdb->prefix . 'rac_abandoncart';

            $sql = "CREATE TABLE $table_name(

             id int(9) NOT NULL AUTO_INCREMENT,

             cart_details LONGTEXT NOT NULL,

             user_id LONGTEXT NOT NULL,

              email_id VARCHAR(255),

             cart_abandon_time BIGINT NOT NULL,

             cart_status LONGTEXT NOT NULL,

             mail_template_id LONGTEXT,

             ip_address LONGTEXT,

             link_status LONGTEXT,

             sending_status VARCHAR(15) NOT NULL DEFAULT 'SEND',

             wpml_lang VARCHAR(10),

             placed_order VARCHAR(20),

             completed VARCHAR(20),

              UNIQUE KEY id (id)

            )DEFAULT CHARACTER SET utf8;";





            dbDelta($sql);

//Email Template Table

            $table_name_email = $wpdb->prefix . 'rac_templates_email';

            $sql_email = "CREATE TABLE $table_name_email(

             id int(9) NOT NULL AUTO_INCREMENT,

             template_name LONGTEXT NOT NULL,

             sender_opt VARCHAR(10) NOT NULL DEFAULT 'woo',

             from_name LONGTEXT NOT NULL,

             from_email LONGTEXT NOT NULL,

             subject LONGTEXT NOT NULL,

             anchor_text LONGTEXT NOT NULL,

             message LONGTEXT NOT NULL,

             sending_type VARCHAR(20) NOT NULL,

             sending_duration BIGINT NOT NULL,

             status VARCHAR(10) NOT NULL DEFAULT 'ACTIVE',



              UNIQUE KEY id (id)

            )DEFAULT CHARACTER SET utf8;";

            dbDelta($sql_email);



//Email Logs

            $table_name_logs = $wpdb->prefix . 'rac_email_logs';

            $sql_email_logs = "CREATE TABLE $table_name_logs(

             id int(9) NOT NULL AUTO_INCREMENT,

             email_id LONGTEXT NOT NULL,

             date_time LONGTEXT NOT NULL,

             rac_cart_id LONGTEXT NOT NULL,

             template_used LONGTEXT NOT NULL,

              UNIQUE KEY id (id)

            )DEFAULT CHARACTER SET utf8;";

            dbDelta($sql_email_logs);



            $email_temp_check = $wpdb->get_results("SELECT * FROM $table_name_email", OBJECT);

            if (empty($email_temp_check)) {

                $wpdb->insert($table_name_email, array('template_name' => 'Default',

                    'sender_opt' => 'woo',

                    'from_name' => 'Admin',

                    'from_email' => get_option('admin_email'),

                    'subject' => 'Recovering Abandon Cart',

                    'anchor_text' => 'Cart Link',

                    'message' => 'Hi {rac.firstname},Product info {rac.Productinfo}, <br><br>You have not completed your purchase.<br><br>Use the following link to make the purchase.<br>{rac.cartlink}<br><br> Thanks',

                    'sending_type' => 'days',

                    'sending_duration' => '1'));

            }

//altering table like below in order to avoid problem with previous table

            $wpdb->query("ALTER TABLE $table_name change mail_sent_to ip_address LONGTEXT");

            $wpdb->query("ALTER TABLE $table_name ADD sending_status varchar(15) NOT NULL DEFAULT 'SEND' AFTER link_status");

            $wpdb->query("ALTER TABLE $table_name ADD email_id VARCHAR(255) AFTER user_id");

            $wpdb->query("ALTER TABLE $table_name ADD wpml_lang varchar(10) NOT NULL DEFAULT 'en' AFTER sending_status");

//altering for email templates

            $wpdb->query("ALTER TABLE $table_name_email ADD sender_opt varchar(10) NOT NULL DEFAULT 'woo' AFTER template_name");

            $wpdb->query("ALTER TABLE $table_name_email ADD status varchar(10) NOT NULL DEFAULT 'ACTIVE' AFTER sending_duration");

            $wpdb->query("ALTER TABLE $table_name_email ADD anchor_text LONGTEXT NOT NULL");



            $wpdb->query("ALTER TABLE $table_name ADD old_status LONGTEXT NOT NULL");



//update default values to old rows

            $wpdb->query("UPDATE $table_name_email SET anchor_text='Cart Link' WHERE anchor_text=''");

            update_option('rac_db_version', $currentdbversion);

        }

    }



    public static function fp_rac_create_new_email_template() {

        if (isset($_POST['rac_template_name'])) {

            global $wpdb;

            $table_name_email = $wpdb->prefix . 'rac_templates_email';

            $wpdb->insert($table_name_email, array('template_name' => stripslashes($_POST['rac_template_name']),

                'status' => stripslashes($_POST['rac_template_status']),

                'sender_opt' => stripslashes($_POST['rac_sender_option']),

                'from_name' => stripslashes($_POST['rac_from_name']),

                'from_email' => stripslashes($_POST['rac_from_email']),

                'subject' => stripslashes($_POST['rac_subject']),

                'anchor_text' => stripslashes($_POST['rac_anchor_text']),

                'message' => stripslashes($_POST['rac_message']),

                'sending_type' => stripslashes($_POST['rac_duration_type']),

                'sending_duration' => stripslashes($_POST['rac_mail_duration']))

            );

        }

        echo $wpdb->insert_id;

        exit();

    }



    public static function fp_rac_edit_email_template() {

        if (isset($_POST['rac_template_id'])) {

            $template_id = $_POST['rac_template_id'];

            global $wpdb;

            $table_name_email = $wpdb->prefix . 'rac_templates_email';

            $wpdb->update($table_name_email, array('template_name' => stripslashes($_POST['rac_template_name']),

                'status' => stripslashes($_POST['rac_template_status']),

                'sender_opt' => stripslashes($_POST['rac_sender_option']),

                'from_name' => stripslashes($_POST['rac_from_name']),

                'from_email' => stripslashes($_POST['rac_from_email']),

                'subject' => stripslashes($_POST['rac_subject']),

                'anchor_text' => stripslashes($_POST['rac_anchor_text']),

                'message' => stripslashes($_POST['rac_message']),

                'sending_type' => stripslashes($_POST['rac_duration_type']),

                'sending_duration' => stripslashes($_POST['rac_mail_duration'])), array('id' => $template_id));

        }

        exit();

    }



    public static function fp_rac_delete_email_template() {

        if (isset($_POST['row_id'])) {

            global $wpdb;

            $row_id = $_POST['row_id'];

            $table_name_email = $wpdb->prefix . 'rac_templates_email';

            $wpdb->delete($table_name_email, array('id' => $row_id));

//removing registered WPML strings

            if (function_exists('icl_unregister_string')) {

                icl_unregister_string('RAC', 'rac_template_' . $row_id . '_message');

                icl_unregister_string('RAC', 'rac_template_' . $row_id . '_subject');

            }

        }

        exit();

    }



    public static function fp_rac_insert_entry() {

        global $wpdb;

        $table_name = $wpdb->prefix . 'rac_abandoncart';

        $current_time = current_time('timestamp');



        if (function_exists('icl_register_string')) {

            $currentuser_lang = isset($_SESSION['wpml_globalcart_language']) ? $_SESSION['wpml_globalcart_language'] : ICL_LANGUAGE_CODE;

        } else {

            $currentuser_lang = 'en';

        }



        if (is_user_logged_in()) {



            $user_id = get_current_user_id();

            $user_details = get_userdata($user_id);

            $user_email = $user_details->user_email;



            $last_cart = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $user_id and cart_status NOT IN('trash') ORDER BY id DESC LIMIT 1", OBJECT);

            if (!empty($last_cart)) {

                $last_cart = $last_cart[0];

            }

            $cart_persistent = (get_user_meta($user_id, '_woocommerce_persistent_cart'));

            if (!empty($cart_persistent[0]['cart'])) {

                $cart_content = maybe_serialize(get_user_meta($user_id, '_woocommerce_persistent_cart'));

                $cut_off_time = get_option('rac_abandon_cart_time');

                if (get_option('rac_abandon_cart_time_type') == 'minutes') {

                    $cut_off_time = $cut_off_time * 60;

                } else if (get_option('rac_abandon_cart_time_type') == 'hours') {

                    $cut_off_time = $cut_off_time * 3600;

                } else if (get_option('rac_abandon_cart_time_type') == 'days') {

                    $cut_off_time = $cut_off_time * 86400;

                }

                if (!empty($last_cart)) {

                    $cut_off_time = $last_cart->cart_abandon_time + $cut_off_time;

                }

//$query = "INSERT INTO $table_name(cart_details,user_id) VALUES($cart_content,$user_id);";

//$wpdb->query($wpdb->prepare("INSERT INTO $table_name(cart_details,user_id) VALUES($cart_content,$user_id)"));

                if ($current_time > $cut_off_time) {

//  if ($last_cart[$last_cart_key]->cart_details != $cart_content) {

                    if (isset($_GET['abandon_cart'])) {

//do nothing. Since this cart is from mail

                    } else {



                        if (!empty($last_cart)) {

                            $wpdb->update($table_name, array('cart_status' => 'ABANDON'), array('id' => $last_cart->id));

                            FPRacCounter::rac_do_abandoned_count();

                        }



                        if (get_option('rac_remove_carts') == 'yes') {





                            if (get_option('rac_remove_new') == 'yes') {



                                $wpdb->delete($table_name, array('email_id' => $user_email, 'cart_status' => 'NEW'));

                            }



                            if (get_option('rac_remove_abandon') == 'yes') {



                                $wpdb->delete($table_name, array('email_id' => $user_email, 'cart_status' => 'ABANDON'));

                            }

                        }





                        $wpdb->insert($table_name, array('cart_details' => $cart_content, 'user_id' => $user_id, 'email_id' => $user_email, 'cart_abandon_time' => $current_time, 'cart_status' => 'NEW', 'wpml_lang' => $currentuser_lang));

                    }

                } else { //Update the cart details if less than or equal to cut off time

                    if (!empty($last_cart)) {

                        $wpdb->update($table_name, array('cart_details' => $cart_content, 'cart_abandon_time' => $current_time), array('id' => $last_cart->id));

                    }

                }

            }

// FOR ALL USER STATUS - - UPDATE ONLY

//Members

            $status_new_list = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status='NEW' AND user_id != '0'", OBJECT);

            $cut_off_time = get_option('rac_abandon_cart_time');

            if (get_option('rac_abandon_cart_time_type') == 'minutes') {

                $cut_off_time = $cut_off_time * 60;

            } else if (get_option('rac_abandon_cart_time_type') == 'hours') {

                $cut_off_time = $cut_off_time * 3600;

            } else if (get_option('rac_abandon_cart_time_type') == 'days') {

                $cut_off_time = $cut_off_time * 86400;

            }

            foreach ($status_new_list as $status_new) {

                $cut_off_time = $cut_off_time + $status_new->cart_abandon_time;

                if ($current_time > $cut_off_time) {

                    $wpdb->update($table_name, array('cart_status' => 'ABANDON'), array('id' => $status_new->id));

                    FPRacCounter::rac_do_abandoned_count();

                }

            }

//Guest

            $status_new_list = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status='NEW' AND user_id='0'", OBJECT);

            $cut_off_time = get_option('rac_abandon_cart_time_guest');

            if (get_option('rac_abandon_cart_time_type_guest') == 'minutes') {

                $cut_off_time = $cut_off_time * 60;

            } else if (get_option('rac_abandon_cart_time_type_guest') == 'hours') {

                $cut_off_time = $cut_off_time * 3600;

            } else if (get_option('rac_abandon_cart_time_type_guest') == 'days') {

                $cut_off_time = $cut_off_time * 86400;

            }

            foreach ($status_new_list as $status_new) {

                $cut_off_time = $cut_off_time + $status_new->cart_abandon_time;

                if ($current_time > $cut_off_time) {

                    $wpdb->update($table_name, array('cart_status' => 'ABANDON'), array('id' => $status_new->id));

                    FPRacCounter::rac_do_abandoned_count();

                }

            }

// FOR ALL USER STATUS - UPDATE ONLY END

        } else {

// FOR ALL USER STATUS - UPDATE ONLY

//Members

            $status_new_list = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status='NEW' AND user_id!='0'", OBJECT);

            $cut_off_time = get_option('rac_abandon_cart_time');

            if (get_option('rac_abandon_cart_time_type') == 'minutes') {

                $cut_off_time = $cut_off_time * 60;

            } else if (get_option('rac_abandon_cart_time_type') == 'hours') {

                $cut_off_time = $cut_off_time * 3600;

            } else if (get_option('rac_abandon_cart_time_type') == 'days') {

                $cut_off_time = $cut_off_time * 86400;

            }

            foreach ($status_new_list as $status_new) {

                $cut_off_time = $cut_off_time + $status_new->cart_abandon_time;

                if ($current_time > $cut_off_time) {

                    $wpdb->update($table_name, array('cart_status' => 'ABANDON'), array('id' => $status_new->id));

                    FPRacCounter::rac_do_abandoned_count();

                }

            }

//guest

            $status_new_list = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status='NEW' AND user_id='0'", OBJECT);

            $cut_off_time = get_option('rac_abandon_cart_time_guest');

            if (get_option('rac_abandon_cart_time_type_guest') == 'minutes') {

                $cut_off_time = $cut_off_time * 60;

            } else if (get_option('rac_abandon_cart_time_type_guest') == 'hours') {

                $cut_off_time = $cut_off_time * 3600;

            } else if (get_option('rac_abandon_cart_time_type_guest') == 'days') {

                $cut_off_time = $cut_off_time * 86400;

            }

            foreach ($status_new_list as $status_new) {

                $cut_off_time = $cut_off_time + $status_new->cart_abandon_time;

                if ($current_time > $cut_off_time) {

                    $wpdb->update($table_name, array('cart_status' => 'ABANDON'), array('id' => $status_new->id));

                    FPRacCounter::rac_do_abandoned_count();

                }

            }

// FOR ALL USER STATUS - UPDATE ONLY END

        }

    }



    public static function fp_rac_add_abandon_cart() {

        global $woocommerce;

//only perform recover from member mail

        if (isset($_GET['abandon_cart']) && !isset($_GET['guest']) && !isset($_GET['checkout']) && !isset($_GET['old_order'])) {

            $abandon_cart_id = $_GET['abandon_cart'];

            $email_template_id = $_GET['email_template'];

            global $wpdb;

            $table_name = $wpdb->prefix . 'rac_abandoncart';

            $last_cart = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $abandon_cart_id and cart_status NOT IN('trash') ", OBJECT);

            end($last_cart);

            $last_cart_key = key($last_cart);

            if (isset($last_cart_key)) {

                $user_details = maybe_unserialize($last_cart[$last_cart_key]->cart_details);



                foreach ($user_details as $cart) {

                    $cart_details = $cart['cart'];

                }

                if (function_exists('WC')) {

                    WC()->session->cart = $cart_details;

                } else {

                    $woocommerce->session->cart = $cart_details;

                }

                setcookie("rac_cart_id", $abandon_cart_id, time() + 3600, "/");

                if (!empty($last_cart[$last_cart_key]->link_status)) {

                    $email_template_ids_db = maybe_unserialize($last_cart[$last_cart_key]->link_status);

                    if (!in_array($email_template_id, (array) $email_template_ids_db)) { //check for id duplication

                        $email_template_ids_db[] = $email_template_id;

                        $email_template_id_final = $email_template_ids_db;

                    }

                    $email_template_id_final = $email_template_ids_db;

                } else {

                    $email_template_id_final = array($email_template_id);

                }

                $email_template_id_final = maybe_serialize($email_template_id_final);

                $wpdb->update($table_name, array('link_status' => $email_template_id_final), array('id' => $abandon_cart_id));

                FPRacCounter::rac_do_linkc_count();

            }

        }

    }



    public static function check_is_member_or_guest($to) {

        $get_user_by_email = get_user_by('email', $to);

        if ($get_user_by_email) {

            return true;

        } else {

            return false;

        }

    }



    public static function rac_return_user_id($memberemail) {

        $get_user_by_email = get_user_by('email', $memberemail);

        return $get_user_by_email->ID;

    }



//Footer Record

    public static function unsubscribed_user_from_rac_mail() {

        if (isset($_GET['email']) && isset($_GET['action']) && isset($_GET['_mynonce'])) {

            if (wp_verify_nonce($_GET['_mynonce'], $_GET['email'])) {

                $to = $_GET['email'];

//echo "Google";

                $check = RecoverAbandonCart::check_is_member_or_guest($_GET['email']);



                if ($check) {

// For Member

                    $member_userid = RecoverAbandonCart::rac_return_user_id($to);

                    update_user_meta($member_userid, 'fp_rac_mail_unsubscribed', 'yes');

                } else {

// For Guest

                    $old_array = array_filter(array_unique((array) get_option('fp_rac_mail_unsubscribed')));

// if (in_array($to, (array) get_option('teste'))) {

                    $listofemails = (array) $to;

                    $merge_arrays = array_merge($listofemails, $old_array);

                    update_option('fp_rac_mail_unsubscribed', $merge_arrays);

                }

                wp_safe_redirect(site_url());

                exit();

            } else {

                wp_safe_redirect(site_url());

                exit();

            }

        }

    }



    public static function rac_footer_email_customization($message) {



        global $to;



        if (get_option('fp_unsubscription_link_in_email') == 'yes') {

            $site_url = site_url();

            $site_name = get_bloginfo('name'); // Site Name

            $unsublink = esc_url(add_query_arg(array('email' => $to, 'action' => 'unsubscribe', '_mynonce' => wp_create_nonce("$to")), $site_url));

            $footer_link_text = get_option('fp_unsubscription_footer_link_text');

            $footer_message = get_option('fp_unsubscription_footer_message');

            $find_shortcode = array('{rac_unsubscribe}', '{rac_site}');

            $unsublink = '<a href="' . $unsublink . '">' . $footer_link_text . '</a>';

            $replace_shortcode = array($unsublink, $site_name);

            $footer_message = str_replace($find_shortcode, $replace_shortcode, $footer_message);

            return $footer_message;

        } else {

            return $message;

        }

    }



    public static function response_unsubscribe_option_myaccount() {

        if (isset($_POST['getcurrentuser']) && isset($_POST['dataclicked'])) {

            $userid = $_POST['getcurrentuser'];

            $dataclicked = $_POST['dataclicked'];



            if ($dataclicked == 'false') {

                update_user_meta($userid, 'fp_rac_mail_unsubscribed', 'yes');

                echo "1";

            } else {

                delete_user_meta($userid, 'fp_rac_mail_unsubscribed');

                echo "2";

            }

            exit();

        }

    }



    public static function add_undo_unsubscribe_option_myaccount() {

        ?><script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#fp_rac_unsubscribe_option').click(function () {
                    // alert(jQuery(this).is(':checked'));
                    var getcurrentuser = "<?php echo get_current_user_id(); ?>";
                    var dataclicked = jQuery(this).is(':checked') ? 'false' : 'true';
                    var data = {
                        action: 'fp_rac_undo_unsubscribe',
                        getcurrentuser: getcurrentuser,
                        dataclicked: dataclicked
                };
                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
                    function (response) {
                        response = response.replace(/\s/g, '');
                        if (response === '1') {
                            alert("Successfully Unsubscribed...");
                        } else {
                            alert("Successfully Subscribed...");
                        }
                    });
                });
            });
        </script>

        <h3><?php _e('Notifications About Unpaid Cart', 'recoverabandoncart'); ?></h3>

        <p><input type="checkbox" name="fp_rac_unsubscribe_option" id="fp_rac_unsubscribe_option" value="yes" <?php checked("yes", get_user_meta(get_current_user_id(), 'fp_rac_mail_unsubscribed', true)); ?>/>    <?php _e('Unsubscribe from receiving email about unpaid cart items.', 'recoverabandoncart'); ?></p>

        <?php

    }



    public static function fp_rac_guest_cart_recover() {

        global $wpdb;

        global $woocommerce;

        if (isset($_GET['guest'])) {

            $email_template_id_final = '';

            $abandon_cart_id = $_GET['abandon_cart'];

            $email_template_id = $_GET['email_template'];

            $table_name = $wpdb->prefix . 'rac_abandoncart';

            $last_cart = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $abandon_cart_id", OBJECT);



            $last_cart_key = key($last_cart);

            $expected_object = maybe_unserialize($last_cart[$last_cart_key]->cart_details);



            if (is_object($expected_object)) {

//For Object Recover Abandon Cart

                $cart_details = $expected_object->get_items();



                foreach ($cart_details as $products) {

                    $product = get_product($products['product_id']);

                    if (!empty($products['variation_id'])) {

                        $variations = array();

                        foreach ($products['item_meta'] as $meta_name => $meta_value) {

                            $attributes = $product->get_variation_attributes();

                            $lower_case = array_change_key_case($attributes, CASE_LOWER);

                            if (!empty($lower_case[$meta_name])) {

                                if (!is_null($lower_case[$meta_name])) {

                                    $value_true = in_array(strtolower($meta_value[0]), array_map('strtolower', $lower_case[$meta_name]));

                                } else {

                                    $value_true = false;

                                }

                            }



                            if (in_array(strtolower($meta_name), array_map('strtolower', array_keys($attributes))) && $value_true) {

                                $variations[$meta_name] = $meta_value[0];

                            }

                        }



                        $products['qty'][0];

                        $woocommerce->cart->add_to_cart($products['product_id'], $products['qty'][0], $products['variation_id'], array_filter($variations));

// }

                    } else {

                        $woocommerce->cart->add_to_cart($products['product_id'], $products['qty']);

                    }

                }

                setcookie("rac_cart_id", $abandon_cart_id, time() + 3600, "/");



                if (!empty($last_cart[$last_cart_key]->link_status)) {

                    $email_template_ids_db = maybe_unserialize($last_cart[$last_cart_key]->link_status);

                    if (!in_array($email_template_id, (array) $email_template_ids_db)) { //check for id duplication

                        $email_template_ids_db[] = $email_template_id;

                        $email_template_id_final = $email_template_ids_db;

                    }

                } else {

                    $email_template_id_final = array($email_template_id);

                }

            } elseif (is_array($expected_object)) {



                $expected_object = maybe_unserialize($last_cart[$last_cart_key]->cart_details);

                $cart_details = $expected_object;

                unset($cart_details['visitor_mail']);

                unset($cart_details['first_name']);

                unset($cart_details['last_name']);

                unset($cart_details['visitor_phone']);

                foreach ($cart_details as $products) {



                    if (!empty($products['variation_id'])) {

                        $variations = array();



                        foreach ($products['variation'] as $attr_name => $attr_val) {

                            $var_name = explode("attribute_", $attr_name);



                            $variations[$var_name[0]] = $attr_val;

                        }



                        $woocommerce->cart->add_to_cart($products['product_id'], $products['quantity'], $products['variation_id'], $variations);

                    } else {

                        $woocommerce->cart->add_to_cart($products['product_id'], $products['quantity']);

                    }

                }

                setcookie("rac_cart_id", $abandon_cart_id, time() + 3600, "/");

                if (!empty($last_cart[$last_cart_key]->link_status)) {

                    $email_template_ids_db = maybe_unserialize($last_cart[$last_cart_key]->link_status);

                    if (!in_array($email_template_id, (array) $email_template_ids_db)) { //check for id duplication

                        $email_template_ids_db[] = $email_template_id;

                        $email_template_id_final = $email_template_ids_db;

                    }

                } else {

                    $email_template_id_final = array($email_template_id);

                }

            }



            $email_template_id_final = maybe_serialize($email_template_id_final);

            $wpdb->update($table_name, array('link_status' => $email_template_id_final), array('id' => $abandon_cart_id));

            FPRacCounter::rac_do_linkc_count();

        }

    }



    public static function recover_old_order_rac() {

// old order made as abandoned by update button

        if (isset($_GET['old_order'])) {

            $abandon_cart_id = $_GET['abandon_cart'];

            $email_template_id = $_GET['email_template'];

            global $wpdb;

            global $woocommerce;

            $table_name = $wpdb->prefix . 'rac_abandoncart';

            $last_cart = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $abandon_cart_id", OBJECT);

            end($last_cart);

            $last_cart_key = key($last_cart);

            $expected_object = maybe_unserialize($last_cart[$last_cart_key]->cart_details);

            if (is_object($expected_object)) {

                $cart_details = $expected_object->get_items();

                foreach ($cart_details as $products) {

                    $product = get_product($products['product_id']);

//     if ($product->product_type == 'variation') {

                    if (!empty($products['variation_id'])) {

                        $variations = array();

                        foreach ($products['item_meta'] as $meta_name => $meta_value) {



                            $attributes = $product->get_variation_attributes();

                            $lower_case = array_change_key_case($attributes, CASE_LOWER);

                            if (!is_null($lower_case[$meta_name])) {

                                $value_true = in_array(strtolower($meta_value[0]), array_map('strtolower', $lower_case[$meta_name]));

                            } else {

                                $value_true = false;

                            }

                            if (in_array(strtolower($meta_name), array_map('strtolower', array_keys($attributes))) && $value_true) {

                                $variations[$meta_name] = $meta_value[0];

                            }

                        }

                        $woocommerce->cart->add_to_cart($products['product_id'], $products['qty'], $products['variation_id'], $variations);

// }

                    } else {

                        $woocommerce->cart->add_to_cart($products['product_id'], $products['qty']);

                    }

                }

            }

            setcookie("rac_cart_id", $abandon_cart_id, time() + 3600, "/");

            if (!empty($last_cart[$last_cart_key]->link_status)) {

                $email_template_ids_db = maybe_unserialize($last_cart[$last_cart_key]->link_status);

                if (!in_array($email_template_id, (array) $email_template_ids_db)) { //check for id duplication

                    $email_template_ids_db[] = $email_template_id;

                    $email_template_id_final = $email_template_ids_db;

                }

            } else {

                $email_template_id_final = array($email_template_id);

            }

            $email_template_id_final = maybe_serialize($email_template_id_final);

            $wpdb->update($table_name, array('link_status' => $email_template_id_final), array('id' => $abandon_cart_id));

            FPRacCounter::rac_do_linkc_count();

        }

    }



    public static function fp_rac_adandoncart_admin_display() {

        global $wpdb;

        $table_name = $wpdb->prefix . 'rac_abandoncart';

        $count_trashed = 0;

        $new = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status IN('trash')", ARRAY_A);

        $count_trashed = count($new);

        $new1 = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status NOT IN('trash')", ARRAY_A);

        $count_all = count($new1);



        if (!isset($_GET['section'])) {



            $abandon_cart_list = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status NOT IN('trash')", OBJECT);

        } else {

            if ($_GET['section'] == 'trash') {

                $abandon_cart_list = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status IN('trash')", OBJECT);

            }

        }

        echo '&nbsp<span><select id="rac_pagination_cart">';

        for ($k = 1; $k <= 20; $k++) {



            if ($k == 10) {

                echo '<option value="' . $k . '" selected="selected">' . $k . '</option>';

            } else {

                echo '<option value="' . $k . '">' . $k . '</option>';

            }

        }

        echo '</select></span>';



        echo '&nbsp<label>Search</label><input type="text" name="rac_temp_search" id="rac_temp_search">';

        ?>

        <br>

        <br>

        <a href="<?php echo esc_url_raw(remove_query_arg('section', get_permalink())); ?>"><?php _e("All ", 'recoverabandoncart'); ?></a><?php echo "($count_all)"; ?>

        |

        <a href="<?php echo esc_url_raw(add_query_arg('section', 'trash', get_permalink())); ?>"><?php _e("Trash ", 'recoverabandoncart'); ?></a><?php echo "($count_trashed)"; ?>

        <br>

        <?php

        echo '<table class="rac_email_template_table_abandon table" data-page-size="10" data-filter="#rac_temp_search" data-filter-minimum="1">';

        echo '<thead>

		<tr>

			<th data-type="numeric">' . __('ID', 'recoverabandoncart') . '</th>

			<th data-hide="phone">' . __('Cart Details', 'recoverabandoncart') . '</th>

			<th>' . __('UserName / First Last Name', 'recoverabandoncart') . '</th>

                              <th>' . __('Email ID / Phone Number', 'recoverabandoncart') . '</th>

                            <th>' . __('Abandoned Date/Time', 'recoverabandoncart') . '</th>

                        <th>' . __('Status', 'recoverabandoncart') . '</th>

                        <th>' . __('Email Template / Email Status / Cart Link in Email', 'recoverabandoncart') . '</th>

                            <th>' . __('Recovered Order ID', 'recoverabandoncart') . '</th>

                                  <th >' . __('Payment Status', 'recoverabandoncart') . '</th>

                                      <th>' . __('Mail Sending', 'recoverabandoncart') . '</th>';



        $main_trash_data = 'trash';

        // Check the Status

        if (!isset($_GET['section'])) {

            $main_trash_data = 'trash';

        } else {

            if ($_GET['section'] == 'trash') {

                $main_trash_data = 'permanent';

            }

        }



        echo '<th class="rac_small_col" data-sort-ignore="true"><a href="#" id="rac_sel">' . __('Select All', 'recoverabandoncart') . '</a>&nbsp/&nbsp <a href="#" id="rac_desel">' . __('Deselct All', 'recoverabandoncart') . '</a>&nbsp'

        . '<a href="#" class="rac_selected_del button" data-deletion=' . $main_trash_data . '>' . __('Delete Selected', 'recoverabandoncart') . '</a>&nbsp';

        if (!isset($_GET['section'])) {

            echo '<a href="#" id="rac_selected_mail" class="button button-primary">' . __('Send Mail for Selected', 'recoverabandoncart') . '</a>';

        } else {

            ?>

            <a href="#" class="rac_selected_del button" data-deletion="restore"><?php echo "Restore Selected"; ?></a>

            <?php

        }

        echo '</th>

		</tr>

	</thead>';

        ?>

        <tbody>

            <?php

            foreach ($abandon_cart_list as $each_list) {

                ?>

                <tr>

                    <td><?php echo $each_list->id; ?></td>

                    <!--<td><?php $each_list->cart_details; ?></td> -->

                    <td><?php

                        $cart_array = maybe_unserialize($each_list->cart_details);

                        if (is_array($cart_array) && is_null($each_list->ip_address)) {

                            foreach ($cart_array as $cart) {

                                foreach ($cart as $inside) {

                                    foreach ($inside as $product) {

                                        echo get_the_title($product['product_id']) . '<br>';

                                    }

                                }

                            }

                        } elseif (is_array($cart_array)) {

                            //for cart captured at checkout(GUEST)

                            $first_name = $cart_array['first_name'];

                            $last_name = $cart_array['last_name'];

                            $guest_first_last = " / $first_name $last_name";



                            unset($cart_array['visitor_mail']);

                            unset($cart_array['first_name']);

                            unset($cart_array['last_name']);

                            if (isset($cart_array['visitor_phone'])) {

                                unset($cart_array['visitor_phone']);

                            }

                            foreach ($cart_array as $product) {

                                echo get_the_title($product['product_id']) . '<br>';

                            }

                        } elseif (is_object($cart_array)) { // For Guest

                            //var_dump($cart_array);

                            foreach ($cart_array->get_items() as $item) {

                                echo $item['name'] . '<br>';

                            }

                            $guest_first_last = " / $cart_array->billing_first_name $cart_array->billing_last_name";

                        }

                        ?></td>

                    <td><?php

                        $user_info = get_userdata($each_list->user_id);

                        if (is_object($user_info)) {

                            echo $user_info->user_login;

                            echo " / $user_info->user_firstname $user_info->user_lastname";

                        } elseif ($each_list->user_id == '0') {

                            echo 'Guest';

                            echo $guest_first_last;

                        } elseif ($each_list->user_id == 'old_order') {

                            $old_order_cart_ob = maybe_unserialize($each_list->cart_details);

                            $user_inf = get_userdata($old_order_cart_ob->user_id);

                            if (is_object($user_inf)) {

                                echo $user_inf->user_login;

                                echo " / $user_inf->user_firstname $user_inf->user_lastname";

                            } else {

                                echo 'Guest';

                                echo $guest_first_last;

                            }

                        }

                        ?></td>

                    <td>

                        <?php

                        if (0 == $each_list->user_id) {

                            $details = maybe_unserialize($each_list->cart_details);

                            if (is_object($details)) {

                                echo $details->billing_email; // Order Object. Works for both old order and rac captured order

                                echo ' / ' . $details->billing_phone;

                            } elseif (is_array($details)) {

                                echo $details['visitor_mail']; //checkout order

                                echo " / ";

                                if (isset($details['visitor_phone'])) {

                                    echo $details['visitor_phone'];

                                } else {

                                    echo '-';

                                }

                            }

                        } else {

                            $user_infor = get_userdata($each_list->user_id);

                            if (is_object($user_infor)) {

                                echo $user_infor->user_email;

                                echo ' / ' . $user_infor->billing_phone;

                            }

                        }

                        ?>

                    </td>

                    <td>

                        <?php echo date(get_option('date_format'), $each_list->cart_abandon_time) . '/' . date(get_option('time_format'), $each_list->cart_abandon_time); ?>

                    </td>

                    <td><?php echo $each_list->cart_status == 'trash' ? 'Trashed' : $each_list->cart_status; ?></td>

                    <td>

                        <?php

                        $mail_sent = maybe_unserialize($each_list->mail_template_id);

                        $email_table_name_clicked = $wpdb->prefix . 'rac_templates_email';

                        $email_template_all = $wpdb->get_results("SELECT * FROM $email_table_name_clicked");

                        foreach ($email_template_all as $check_all_email_temp) {

                            echo $check_all_email_temp->template_name;

                            //Mail Sent

                            if (!is_null($mail_sent)) {

                                if (in_array($check_all_email_temp->id, (array) $mail_sent)) {

                                    echo ' / Email Sent';

                                } else {

                                    echo ' / Email Not Sent';

                                }

                            } else {

                                echo ' / Email Not Sent';

                            }

                            //Mail Sent END

                            //Link Clicked

                            if (!empty($each_list->link_status)) {

                                $mails_clicked = maybe_unserialize($each_list->link_status);

                                if (in_array($check_all_email_temp->id, (array) $mails_clicked)) {

                                    //  echo $check_all_email_temp->template_name;

                                    echo ' / Cart Link Clicked';

                                    echo '<br>';

                                } else {

                                    // echo $check_all_email_temp->template_name;

                                    echo ' / Cart Link Not Clicked';

                                    echo '<br>';

                                }

                            } else {

                                echo ' / Cart Link Not Clicked';

                                echo '<br>';

                            }

                            //Link Clicked END

                        }

                        ?>

                    </td>

                    <td><?php echo (!is_null($each_list->placed_order) ? ' #' . $each_list->placed_order . '' : 'Not Yet'); ?></td>

                    <td><?php echo (!empty($each_list->completed) ? 'Completed' : 'Not Yet'); ?></td>

                    <td>

                        <?php

                        if ($each_list->cart_status != 'trash') {

                            if (empty($each_list->completed)) {



                                //check if order completed,if completed don't show mail sending button'

                                ?>



                                <a href="#" class="button rac_mail_sending" data-racmoptid="<?php echo $each_list->id; ?>" data-currentsate="<?php echo $each_list->sending_status == 'SEND' ? 'SEND' : 'DONT' ?>"><?php

                                    if ($each_list->sending_status == 'SEND') {

                                        echo 'Stop Mailing';

                                    } else {

                                        echo 'Start Mailing';

                                    }

                                    echo "</a>";

                                } else {

                                    echo 'Recovered';

                                }

                            } else {

                                echo "Trashed";

                            }

                            ?></td>

                    <td class="bis_mas_small_col">

                        <input type="checkbox" class="rac_checkboxes" data-racid="<?php echo $each_list->id; ?>"/>

                        <a href="#" class="button rac_check_indi" data-deletion="<?php echo $main_trash_data; ?>" data-racdelid="<?php echo $each_list->id; ?>"><?php echo $each_list->cart_status != 'trash' ? "Delete this Row" : "Delete Permanently"; ?></a>

                        <?php if ($each_list->cart_status == 'trash') { ?>

                            <a href="#" class="button rac_check_indi" data-deletion="restore" data-racdelid="<?php echo $each_list->id; ?>"><?php _e('Restore', 'recoverabandoncart'); ?></a>

                        <?php } ?>

                    </td>

                </tr>

                <?php

            }

            echo '</tbody>

            <tfoot>

		<tr>

			<td colspan="11">

				<div class="pagination pagination-centered hide-if-no-paging"></div>

			</td>

		</tr>

	</tfoot></table><style>.footable > tbody > tr > td,.footable > thead > tr > th, .footable > thead > tr > td{text-align:center;}</style>';

            ?>

        <script type="text/javascript">

            jQuery(document).ready(function () {

                //Manual Mail redirection

                jQuery('#rac_selected_mail').click(function (e) {

                    e.preventDefault();

                    var selection_for_mail = new Array();

                    jQuery('.rac_checkboxes').each(function (num) {

                        if (jQuery(this).prop('checked')) {

                            selection_for_mail.push(jQuery(this).data('racid'));

                        }

                    });

                    // console.log(jQuery('.bis_mas_checkboxes'));

                    console.log(selection_for_mail);

                    var url_without_data = "<?php echo esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail'), admin_url('admin.php'))); ?>";

                    var url_data = url_without_data + "&rac_send_email=" + selection_for_mail;

                    console.log(url_without_data);

                    console.log(url_data);

                    if (selection_for_mail.length > 0) {

                        window.location.replace(url_data);

                    } else {

                        alert("Select a row to mail");

                    }



                    //window.location.replace("<?php echo add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail', 'rac_send_email' => 'template'), admin_url('admin.php')); ?>");



                });

            });

        </script>



        <?php

    }



    public static function fp_rac_mail_logs_display() {

        global $wpdb;

        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

        $email_template_table = $wpdb->prefix . 'rac_templates_email';

        $rac_log_list = $wpdb->get_results("SELECT * FROM $table_name_logs", OBJECT);

//var_dump($rac_log_list);

        echo '&nbsp<span><select id="rac_pagination_logs">';

        for ($k = 1; $k <= 20; $k++) {



            if ($k == 10) {

                echo '<option value="' . $k . '" selected="selected">' . $k . '</option>';

            } else {

                echo '<option value="' . $k . '">' . $k . '</option>';

            }

        }

        '</select></span>';



        echo '&nbsp<label>Search</label><input type="text" name="rac_temp_search" id="rac_temp_search">';



        echo '<table class="rac_email_logs_table table" data-page-size="10" data-filter="#rac_temp_search" data-filter-minimum="1">';

        echo '<thead>

		<tr>

                <th>' . __('S.No', 'recoverabandoncart') . '</th>

			<th>' . __('Email Id', 'recoverabandoncart') . '</th>

                        <th>' . __('Date/Time', 'recoverabandoncart') . '</th>

			<th>' . __('Template Used', 'recoverabandoncart') . '</th>

                        <th>' . __('Abandoned Cart ID', 'recoverabandoncart') . '</th>

                        <th class="rac_small_col" data-sort-ignore="true"><a href="#" id="rac_sel">' . __('Select All', 'recoverabandoncart') . '</a>&nbsp/&nbsp <a href="#" id="rac_desel">' . __('Deselct All', 'recoverabandoncart') . '</a>&nbsp<a href="#" id="rac_selected_del_log" class="button">' . __('Delete Selected', 'recoverabandoncart') . '</a></th>

		</tr>

	</thead><tbody>';

        $srno = 1;

        foreach ($rac_log_list as $each_log) {

            echo "<tr>";

            echo "<td>";

            echo $srno;

            echo "</td>";

            echo "<td>";

            echo $each_log->email_id;

            echo "</td>";

            echo "<td>";

            echo date(get_option('date_format'), $each_log->date_time) . '/' . date(get_option('time_format'), $each_log->date_time);

            echo "</td>";

            echo "<td>";

            $template_id = $each_log->template_used;

            $manual_mail = strpos($template_id, "Manual");

            if ($manual_mail !== false) {

                $template_id = explode("-", $template_id);

                $template_name = $wpdb->get_results("SELECT template_name FROM $email_template_table WHERE id=$template_id[0]", OBJECT);

                echo $template_name[0]->template_name . '(#' . $each_log->template_used . ')';

            } else {

                $template_name = $wpdb->get_results("SELECT template_name FROM $email_template_table WHERE id=$template_id", OBJECT);

                echo $template_name[0]->template_name;

            }



            echo "</td>";

            echo "<td>";

            echo $each_log->rac_cart_id;

            echo "</td>";

            ?>

            <td class="bis_mas_small_col">

                <input type="checkbox" class="rac_checkboxes" data-raclogid="<?php echo $each_log->id; ?>"/>

                <a href="#" class="button rac_check_indi" data-raclogdelid="<?php echo $each_log->id; ?>">Delete this Row</a>



            </td>

            <?php

            $srno++; //for serial number

        }

        echo '</tbody>

            <tfoot>

		<tr>

			<td colspan="6">

				<div class="pagination pagination-centered hide-if-no-paging"></div>

			</td>

		</tr>

	</tfoot></table><style>.footable > tbody > tr > td,.footable > thead > tr > th, .footable > thead > tr > td{text-align:center;}</style>';

    }



    public static function fp_rac_admin_scritps() {

        wp_register_style('footable_css', plugins_url('/css/footable.core.css', __FILE__));



        wp_enqueue_style('footable_css');

        wp_register_style('footablestand_css', plugins_url('/css/footable.standalone.css', __FILE__));

        wp_enqueue_style('footablestand_css');



        wp_enqueue_script('footable', plugins_url('/js/footable.js', __FILE__), array('jquery'));

        wp_enqueue_script('footable_sorting', plugins_url('/js/footable.sort.js', __FILE__), array('jquery'));

        wp_enqueue_script('footable_paginate', plugins_url('/js/footable.paginate.js', __FILE__), array('jquery'));

        wp_enqueue_script('footable_filter', plugins_url('/js/footable.filter.js', __FILE__), array('jquery'));

        wp_enqueue_script('footable_initialize', plugins_url('/js/footable_initialize.js', __FILE__), array('jquery'));



        wp_enqueue_script('racjscolorpicker', plugins_url('/jscolor/jscolor.js', __FILE__), array('jquery'));

//datepicker

        wp_enqueue_style('jquery_smoothness_ui', plugins_url('/css/jquery_smoothness_ui.css', __FILE__));

        wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_script('date_picker_initialize', plugins_url('/js/rac_datepicker.js', __FILE__), array('jquery', 'jquery-ui-datepicker'));

    }



//Check Additional More Function to cross check whatever order contain cart products

    public static function fp_rac_check_cart_list_manual_recovery($order_id, $orderplaced) {



        global $wpdb;

        $order = new WC_Order($order_id);

        $billing_email = $order->billing_email;



        $table_name = $wpdb->prefix . 'rac_abandoncart';

//Gather Results



        $results = $wpdb->get_results("SELECT * FROM $table_name WHERE `email_id`='$billing_email' and `cart_status`='ABANDON'", ARRAY_A);



        if (!empty($results)) {

            foreach ($results as $key => $value) {

//var_dump($value['id']);

                $rac_order = $value['id'];

                if ($orderplaced == '1') {

                    $wpdb->update($table_name, array('placed_order' => $order_id), array('id' => $rac_order));

                }

                if ($orderplaced == '2') {

                    $wpdb->update($table_name, array('completed' => 'completed'), array('id' => $rac_order));

                    $wpdb->update($table_name, array('cart_status' => 'RECOVERED'), array('id' => $rac_order));



// update_post_meta($order_id, 'rac_order_placed', $row_id);

//counter

                    FPRacCounter::rac_do_recovered_count();

                    RecoverAbandonCart::fp_rac_mail_admin_cart_recovered($order_id); //mailing admin on order recover

                }

            }

        }

    }



//Updating for recovered cart which placed order

    public static function fp_rac_cookies_for_cart_recover($order_id) {

        if (isset($_COOKIE['rac_cart_id'])) {

            $row_id = $_COOKIE['rac_cart_id'];

            global $wpdb;

            $abandon_cart_table = $wpdb->prefix . 'rac_abandoncart';

            $wpdb->update($abandon_cart_table, array('placed_order' => $order_id), array('id' => $row_id));

            update_post_meta($order_id, 'rac_order_placed', $row_id);

//counter

            FPRacCounter::rac_do_recovered_count();

            RecoverAbandonCart::fp_rac_mail_admin_cart_recovered($order_id); //mailing admin on order recover

        } else {

            $order_placed = '1';

            self::fp_rac_check_cart_list_manual_recovery($order_id, $order_placed);

        }

    }



    public static function fp_rac_mail_admin_cart_recovered($order_id) {

        if (get_option('rac_admin_cart_recovered_noti') == "yes") {

            $to = get_option('rac_admin_email');

            $subject = get_option('rac_recovered_email_subject');

            $message = get_option('rac_recovered_email_message');

            $headers = "MIME-Version: 1.0\r\n";

            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            if (get_option('rac_recovered_sender_opt') == "woo") {

                $headers .= FPRacCron::rac_formatted_from_address_woocommerce();

                $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";

            } else {

                $headers .= FPRacCron::rac_formatted_from_address_local(get_option('rac_recovered_from_name'), get_option('rac_recovered_from_email'));

                $headers .= "Reply-To: " . get_option('rac_recovered_from_name') . " <" . get_option('rac_recovered_from_email') . ">\r\n";

            }

            $message = str_replace('{rac.recovered_order_id}', $order_id, $message); //replacing shortcode for order id

            ob_start();

            $order = new WC_Order($order_id);

            ?>

            <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">

                <thead>

                    <tr>

                        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Product', 'woocommerce'); ?></th>

                        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'woocommerce'); ?></th>

                        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Price', 'woocommerce'); ?></th>

                    </tr>

                </thead>

                <tbody>

                    <?php echo $order->email_order_items_table(false, true); ?>

                </tbody>

                <tfoot>

                    <?php

                    if ($totals = $order->get_order_item_totals()) {

                        $i = 0;

                        foreach ($totals as $total) {

                            $i++;

                            ?><tr>

                                <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>

                                <td style="text-align:left; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>

                            </tr><?php

                        }

                    }

                    ?>

                </tfoot>

            </table>



            <?php

            $newdata = ob_get_clean();



            ob_start();

            $message = str_replace('{rac.order_line_items}', $newdata, $message);

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

            if ('wp_mail' == get_option('rac_trouble_mail')) {

                FPRacCron::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers);

            } else {

                FPRacCron::rac_send_mail($to, $subject, $woo_temp_msg, $headers);

            }

        }

    }



    public static function fp_rac_check_order_status($order_id) {

        global $wpdb;

        $table_name = $wpdb->prefix . 'rac_abandoncart';

        $rac_order = get_post_meta($order_id, 'rac_order_placed', true);

        if (!empty($rac_order)) {

            $wpdb->update($table_name, array('completed' => 'completed'), array('id' => $rac_order));

            $wpdb->update($table_name, array('cart_status' => 'RECOVERED'), array('id' => $rac_order));

        }

        $order_placed = '2';

        self::fp_rac_check_cart_list_manual_recovery($order_id, $order_placed);

    }



    public static function rac_translate_file() {

        load_plugin_textdomain('recoverabandoncart', false, dirname(plugin_basename(__FILE__)) . '/languages');

    }



    public static function fp_rac_insert_guest_entry($order_id) {

        if (!is_user_logged_in()) {

            global $wpdb;

            $table_name = $wpdb->prefix . 'rac_abandoncart';

            if (function_exists('icl_register_string')) {

                $currentuser_lang = isset($_SESSION['wpml_globalcart_language']) ? $_SESSION['wpml_globalcart_language'] : ICL_LANGUAGE_CODE;

            } else {

                $currentuser_lang = 'en';

            }

            if (!isset($_COOKIE['rac_cart_id'])) { // We can remove cookie check if we want

                $order = new WC_Order($order_id);



                $user_email = $order->billing_email;



                if (get_option('rac_remove_carts') == 'yes') {





                    if (get_option('rac_remove_new') == 'yes') {



                        $wpdb->delete($table_name, array('email_id' => $user_email, 'cart_status' => 'NEW'));

                    }



                    if (get_option('rac_remove_abandon') == 'yes') {



                        $wpdb->delete($table_name, array('email_id' => $user_email, 'cart_status' => 'ABANDON'));

                    }

                }





                $check_cart = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id=0 ORDER BY id DESC LIMIT 1", OBJECT);

// var_dump($check_cart[0]);

                $db_cart_content = maybe_unserialize($check_cart[0]->cart_details);

//   var_dump($db_cart_content);

                if (empty($db_cart_content)) {// IF no previous entry make a new

                    $user_id = "0";

                    $current_time = current_time('timestamp');

                    $cart_content = maybe_serialize($order);

                    $wpdb->insert($table_name, array('cart_details' => $cart_content, 'user_id' => $user_id, 'email_id' => $user_email, 'cart_abandon_time' => $current_time, 'cart_status' => 'NEW', 'wpml_lang' => $currentuser_lang));

                    update_post_meta($order->id, 'guest_cart', 'yes');

                } else {

                    if (is_object($db_cart_content)) {

                        if ($db_cart_content->id != $order->id) { // don't allow if they refresh again || if already exist

                            $current_time = current_time('timestamp');

// $order_products = $order->get_items();

                            $cart_content = maybe_serialize($order);

                            $user_id = "0";

// var_dump($user_id);

                            $wpdb->insert($table_name, array('cart_details' => $cart_content, 'user_id' => $user_id, 'email_id' => $user_email, 'cart_abandon_time' => $current_time, 'cart_status' => 'NEW', 'wpml_lang' => $currentuser_lang));

                            update_post_meta($order->id, 'guest_cart', 'yes');

                        }

                    } else {

//create after checkout cart

                        $current_time = current_time('timestamp');

                        $cart_content = maybe_serialize($order);

                        $user_id = "0";

                        $wpdb->insert($table_name, array('cart_details' => $cart_content, 'user_id' => $user_id, 'email_id' => $user_email, 'cart_abandon_time' => $current_time, 'cart_status' => 'NEW', 'wpml_lang' => $currentuser_lang));

                        update_post_meta($order->id, 'guest_cart', 'yes');

                    }

                }



//Check cookies for deleting cart captured from checkout

//Delete only if it is not recoverd from mail

                if (isset($_COOKIE['rac_checkout_entry'])) {

                    $delete_id = $_COOKIE['rac_checkout_entry'];

                    $wpdb->delete($table_name, array('id' => $delete_id));

//delete entry

                }

            }

        }

    }



    public static function fp_rac_order_status_guest($order_id, $old, $new_status) {

        global $wpdb;

        $table_name = $wpdb->prefix . 'rac_abandoncart';

        $check_guest_cart = get_post_meta($order_id, 'guest_cart', true);

        if ($check_guest_cart == 'yes') {

            if (get_option('rac_guest_abadon_type_processing') == 'yes') { //option selected by user

                if ($new_status == 'processing') {

                    $get_list = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='0'", OBJECT);

                    foreach ($get_list as $each_entry) {

                        $expected_object = maybe_unserialize($each_entry->cart_details);

                        if (is_object($expected_object)) {

                            if ($expected_object->id == $order_id) {

                                $wpdb->delete($table_name, array('id' => $each_entry->id));

                            }

                        }

                    }

                }

            } if (get_option('rac_guest_abadon_type_completed') == 'yes') {//option selected by user

                if ($new_status == 'completed') {

                    $get_list = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='0'", OBJECT);

                    foreach ($get_list as $each_entry) {

                        $expected_object = maybe_unserialize($each_entry->cart_details);

                        if (is_object($expected_object)) {

                            if ($expected_object->id == $order_id) {

                                $wpdb->delete($table_name, array('id' => $each_entry->id));

                            }

                        }

                    }

                }

            }

//rac_guest_abadon_type_pending

            if (get_option('rac_guest_abadon_type_pending') == 'yes') {//option selected by user

                if ($new_status == 'pending') {

                    $get_list = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='0'", OBJECT);

                    foreach ($get_list as $each_entry) {

                        $expected_object = maybe_unserialize($each_entry->cart_details);

                        if (is_object($expected_object)) {

                            if ($expected_object->id == $order_id) {

                                $wpdb->delete($table_name, array('id' => $each_entry->id));

                            }

                        }

                    }

                }

            }

//failed rac_guest_abadon_type_failed

            if (get_option('rac_guest_abadon_type_failed') == 'yes') {//option selected by user

                if ($new_status == 'failed') {

                    $get_list = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='0'", OBJECT);

                    foreach ($get_list as $each_entry) {

                        $expected_object = maybe_unserialize($each_entry->cart_details);

                        if (is_object($expected_object)) {

                            if ($expected_object->id == $order_id) {

                                $wpdb->delete($table_name, array('id' => $each_entry->id));

                            }

                        }

                    }

                }

            }

//on-hold  rac_guest_abadon_type_on-hold

            if (get_option('rac_guest_abadon_type_on-hold') == 'yes') {//option selected by user

                if ($new_status == 'on-hold') {

                    $get_list = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='0'", OBJECT);

                    foreach ($get_list as $each_entry) {

                        $expected_object = maybe_unserialize($each_entry->cart_details);

                        if (is_object($expected_object)) {

                            if ($expected_object->id == $order_id) {

                                $wpdb->delete($table_name, array('id' => $each_entry->id));

                            }

                        }

                    }

                }

            }

//refunded rac_guest_abadon_type_refunded

            if (get_option('rac_guest_abadon_type_refunded') == 'yes') {//option selected by user

                if ($new_status == 'refunded') {

                    $get_list = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='0'", OBJECT);

                    foreach ($get_list as $each_entry) {

                        $expected_object = maybe_unserialize($each_entry->cart_details);

                        if (is_object($expected_object)) {

                            if ($expected_object->id == $order_id) {

                                $wpdb->delete($table_name, array('id' => $each_entry->id));

                            }

                        }

                    }

                }

            }



// rac_guest_abadon_type_cancelled

            if (get_option('rac_guest_abadon_type_cancelled') == 'yes') {//option selected by user

                if ($new_status == 'cancelled') {

                    $get_list = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='0'", OBJECT);

                    foreach ($get_list as $each_entry) {

                        $expected_object = maybe_unserialize($each_entry->cart_details);

                        if (is_object($expected_object)) {

                            if ($expected_object->id == $order_id) {

                                $wpdb->delete($table_name, array('id' => $each_entry->id));

                            }

                        }

                    }

                }

            }

        }

    }



    public static function fp_rac_checkout_script() {

        if (!is_user_logged_in()) {

            echo '<script type = "text/javascript">

                  jQuery(document).ready(function(){

                     jQuery("#billing_email").on("keyup keypress change",function() {

                         var fp_rac_mail = this . value;

                         var atpos=fp_rac_mail.indexOf("@");

                         var dotpos=fp_rac_mail.lastIndexOf(".");

                if (atpos<1 || dotpos<atpos+2 || dotpos+2>=fp_rac_mail.length)

                        {

                            console.log("Not a valid e-mail address");

                            //return false;

                       }

                    else{

                         console . log(fp_rac_mail);

                             var fp_rac_first_name = jQuery("#billing_first_name").val();

                             var fp_rac_last_name = jQuery("#billing_last_name").val();

                             var fp_rac_phone = jQuery("#billing_phone").val();

                      var data = {

              action:"rac_preadd_guest",

              rac_email:fp_rac_mail,

              rac_first_name:fp_rac_first_name,

              rac_last_name:fp_rac_last_name,

              rac_phone:fp_rac_phone

              }

     jQuery.post("' . admin_url("admin-ajax.php") . '", data,

                            function(response) {

                                //alert(response);

                                console.log(response);



                            });

  }



        });

        });

                </script>';

        }

    }



    public static function fp_rac_guest_entry_checkout_ajax() {

        global $woocommerce;

        if (!is_user_logged_in()) {

            if (!isset($_COOKIE['rac_cart_id'])) { //means they didn't come mail

                if (function_exists('icl_register_string')) {

                    $currentuser_lang = isset($_SESSION['wpml_globalcart_language']) ? $_SESSION['wpml_globalcart_language'] : ICL_LANGUAGE_CODE;

                } else {

                    $currentuser_lang = 'en';

                }

                $visitor_mail = $_POST['rac_email'];

                $visitor_first_name = $_POST['rac_first_name'];

                $visitor_last_name = $_POST['rac_last_name'];

                $visitor_phone = $_POST['rac_phone'];

                $ip_address = $_SERVER["REMOTE_ADDR"];

                if (function_exists('WC')) {

                    $visitor_cart = WC()->cart->get_cart();

                } else {

                    $visitor_cart = $woocommerce->cart->get_cart();

                }

                $visitor_details = $visitor_cart;

                $visitor_details['visitor_mail'] = $visitor_mail;

                $visitor_details['first_name'] = $visitor_first_name;

                $visitor_details['last_name'] = $visitor_last_name;

                $visitor_details['visitor_phone'] = $visitor_phone;

                global $wpdb;

                $table_name = $wpdb->prefix . 'rac_abandoncart';

                $cart_content = maybe_serialize($visitor_details);

                $user_id = "000";

                $current_time = current_time('timestamp');

                if (get_option('rac_remove_carts') == 'yes') {





                    if (get_option('rac_remove_new') == 'yes') {



                        $wpdb->delete($table_name, array('email_id' => $visitor_mail, 'cart_status' => 'NEW'));

                    }



                    if (get_option('rac_remove_abandon') == 'yes') {



                        $wpdb->delete($table_name, array('email_id' => $visitor_mail, 'cart_status' => 'ABANDON'));

                    }

                }



//check for duplication

                $check_ip = $wpdb->get_results("SELECT * FROM $table_name WHERE ip_address ='$ip_address' AND cart_status='NEW'");

                if (!is_null(@$check_ip[0]->id) && !empty($check_ip[0]->id)) {//update

                    $wpdb->update($table_name, array('cart_details' => $cart_content, 'user_id' => $user_id, 'email_id' => $visitor_mail), array('id' => $check_ip[0]->id));

                } else {//Insert New entry

                    $wpdb->insert($table_name, array('cart_details' => $cart_content, 'user_id' => $user_id, 'email_id' => $visitor_mail, 'cart_abandon_time' => $current_time, 'cart_status' => 'NEW', 'ip_address' => $ip_address, 'wpml_lang' => $currentuser_lang));

                    setcookie("rac_checkout_entry", $wpdb->insert_id, time() + 3600, "/");

                }

// echo $wpdb->insert_id;

            }

        }

        exit();

    }



    public static function delete_all_rac_list() {

        global $wpdb;

        $table_name = $wpdb->prefix . 'rac_abandoncart';

        $rows_to_delete = $_POST['listids'];

        $status = $_POST['deletion'];

        foreach ($rows_to_delete as $row_id) {

            if ($status == 'trash') {

                $select_rows = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status NOT IN('trash') and id=$row_id", OBJECT);

                foreach ($select_rows as $each_row) {

                    $cart_status_old = $each_row->cart_status;

                    $wpdb->update($table_name, array('old_status' => $cart_status_old), array('id' => $row_id));

                }

                $wpdb->update($table_name, array('cart_status' => 'trash'), array('id' => $row_id));

            } elseif ($status == 'restore') {



                $select_rows = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status IN('trash') and id=$row_id", OBJECT);

                if (!empty($select_rows)) {

                    foreach ($select_rows as $each_row) {

                        $cart_status_old = $each_row->cart_status;

                        $wpdb->update($table_name, array('cart_status' => $each_row->old_status), array('id' => $row_id));

                    }

                }

                //$wpdb->update($table_name, array('cart_status' => ''), array('id' => $row_id));

            } else {

                $wpdb->delete($table_name, array('id' => $row_id));

            }

        }

        echo "1";

        exit();

    }



    public static function delete_individual_rac_list() {

        global $wpdb;

        $table_name = $wpdb->prefix . 'rac_abandoncart';

        $row_id = $_POST['row_id'];

        $status = $_POST['deletion'];



        if ($status == 'trash') {

            $select_rows = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status NOT IN('trash') and id=$row_id", OBJECT);

            foreach ($select_rows as $each_row) {

                $cart_status_old = $each_row->cart_status;

                $wpdb->update($table_name, array('old_status' => $cart_status_old), array('id' => $row_id));

            }

            $wpdb->update($table_name, array('cart_status' => 'trash'), array('id' => $row_id));

        } elseif ($status == 'restore') {

            $select_rows = $wpdb->get_results("SELECT * FROM $table_name WHERE cart_status IN('trash') and id=$row_id", OBJECT);

            foreach ($select_rows as $each_row) {

                $cart_status_old = $each_row->old_status;

                $wpdb->update($table_name, array('cart_status' => $cart_status_old), array('id' => $row_id));

            }

        } else {

            $wpdb->delete($table_name, array('id' => $row_id));

        }

        echo "1";

        exit();

    }



    public static function delete_all_rac_log() {

        global $wpdb;

        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

        $rows_to_delete = $_POST['listids'];

        foreach ($rows_to_delete as $row_id) {

            $wpdb->delete($table_name_logs, array('id' => $row_id));

        }

        exit();

    }



    public static function delete_individual_rac_log() {

        global $wpdb;

        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

        $row_id = $_POST['row_id'];

        $wpdb->delete($table_name_logs, array('id' => $row_id));

        exit();

    }



    public static function remove_member_acart_on_orderplaced($order_id) {

        if (is_user_logged_in()) {

            global $wpdb;

            $table_name = $wpdb->prefix . 'rac_abandoncart';

            $order = new WC_Order($order_id);

            $user_id = $order->user_id;

            if (!empty($user_id)) { // order by members

                $part_user_acart = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='$user_id' AND cart_status='NEW'", OBJECT);

                if (!empty($part_user_acart)) {

                    foreach ($part_user_acart as $each_entry) {

                        $stored_cart = maybe_unserialize($each_entry->cart_details);



                        foreach ($stored_cart as $cart_details) {

                            if (count($cart_details['cart']) == count($order->get_items())) {

                                $order_item_product_ids = array();

                                $rac_cart_product_ids = array();

                                foreach ($cart_details['cart'] as $product) {

                                    $rac_cart_product_ids[] = $product['product_id'];

                                }

                                foreach ($order->get_items() as $items) {



                                    $order_item_product_ids[] = $items['product_id'];

                                }

                                $check_array = array_diff($rac_cart_product_ids, $order_item_product_ids);

                                if (empty($check_array)) {



                                    $wpdb->delete($table_name, array('id' => $each_entry->id));

                                }

                            }

                        }

                    }

                }

            }

        }

    }



    public static function fp_rac_settings_link($links) {

        $setting_page_link = '<a href="admin.php?page=fprac_slug">Settings</a>';

        array_unshift($links, $setting_page_link);

        return $links;

    }



    public static function fp_rac_reset_general() {

        if (isset($_GET['rac_reset'])) {

            $admin_url = admin_url('admin.php');

            $reset_true_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracgenral', 'resetted' => 'true'), $admin_url));

            update_option('rac_abandon_cart_time_type', 'hours');

            update_option('rac_abandon_cart_time', '1');

            update_option('rac_abandon_cart_time_type_guest', 'hours');

            update_option('rac_abandon_cart_time_guest', '1');

            update_option('rac_abandon_cart_cron_type', 'hours');

            update_option('rac_abandon_cron_time', '12');

            update_option('rac_admin_cart_recovered_noti', 'no');

            update_option('admin_notifi_sender_opt', 'woo');

            update_option('rac_recovered_email_subject', 'A cart has been Recovered');

            update_option('rac_recovered_email_message', 'A cart has been Recovered. Here is the order ID {rac.recovered_order_id} for Reference.');

            wp_redirect($reset_true_url);

            exit;

        }

    }



    public static function fp_rac_add_old_order_byupdate() {

        if (isset($_POST['rac_order_status'])) {

            $looking_order_status = $_POST['rac_order_status'];

            $from_time_array = explode("/", $_POST['rac_from_time']);

            $to_time_array = explode("/", $_POST['rac_to_time']);

            global $wpdb;

            $table_name = $wpdb->prefix . 'rac_abandoncart';

            global $woocommerce;

            if ("all" != $_POST['rac_order_time']) {

                $date_query = array(

                    array(

                        'after' => array(

                            'year' => $from_time_array[2],

                            'month' => $from_time_array[0],

                            'day' => $from_time_array[1],

                        ),

                        'before' => array(

                            'year' => $to_time_array[2],

                            'month' => $to_time_array[0],

                            'day' => $to_time_array[1],

                        ),

                        'inclusive' => true,

                    ),

                );

            }



            if ("all" == $_POST['rac_order_time']) {

                $args = array("post_type" => "shop_order", "posts_per_page" => -1, "post_status" => function_exists('wc_get_order_statuses') == true ? array_keys(wc_get_order_statuses()) : 'any');

            } else {

                $args = array("post_type" => "shop_order", "posts_per_page" => -1, 'date_query' => $date_query, "post_status" => function_exists('wc_get_order_statuses') == true ? array_keys(wc_get_order_statuses()) : 'any');

            }

            $udated_count = 0;

            $the_query = get_posts($args);

            foreach ($the_query as $each_query) {

                $order = new WC_Order($each_query->ID);





                if ($order->user_id != '') {



                    $user_details = get_userdata($order->user_id);

                    $user_email = $order->billing_email;

                } else {

                    $user_email = $order->billing_email;

                }

                if (get_option('rac_remove_carts') == 'yes') {





                    if (get_option('rac_remove_new') == 'yes') {



                        $wpdb->delete($table_name, array('email_id' => $user_email, 'cart_status' => 'NEW'));

                    }



                    if (get_option('rac_remove_abandon') == 'yes') {



                        $wpdb->delete($table_name, array('email_id' => $user_email, 'cart_status' => 'ABANDON'));

                    }

                }





                $rac_order_place = get_post_meta($order->id, "rac_order_placed", true);

                $guest_cart = get_post_meta($order->id, "guest_cart", true);





                if (empty($rac_order_place) && empty($guest_cart)) {



//check to, not importing order whic are recovered and captured on place order

                    if (in_array($order->status, (array) $looking_order_status)) {



//echo "google";

// delete_post_meta($order->id, 'old_order_updated');

                        $already_added = get_post_meta($order->id, "old_order_updated", true);

//  var_dump($already_added);



                        if (empty($already_added)) {



                            $cart_details = maybe_serialize($order);

                            $user_id = "old_order";

                            $order_modified_time = strtotime($order->modified_date); //convert as unix timestamp, so it can be used in comparing even though it is dead old

                            if ($wpdb->insert($table_name, array('cart_details' => $cart_details, 'user_id' => $user_id, 'email_id' => $user_email, 'cart_abandon_time' => $order_modified_time, 'cart_status' => 'ABANDON'), array('%s', '%s', '%d', '%s'))) {

                                update_post_meta($order->id, "old_order_updated", "yes"); // this makes sure for no duplication

                                $udated_count++;

                            }

                        }

                    }

                }

            }

            echo "$udated_count orders are added to cart list";

        }

        exit();

    }



    public static function set_mail_sending_opt() {

        if (isset($_POST['row_id'])) {

            global $wpdb;

            $table_name = $wpdb->prefix . 'rac_abandoncart';

            $requesting_state = $_POST['current_state'] == 'SEND' ? 'DONT' : 'SEND';

            $wpdb->update($table_name, array('sending_status' => $requesting_state), array('id' => $_POST['row_id']));

            echo $requesting_state;

        }

        exit();

    }



    public static function set_email_template_status() {

        if (isset($_POST['row_id'])) {

            global $wpdb;

            $table_name_email = $wpdb->prefix . 'rac_templates_email';

            $requesting_state = $_POST['status'] == 'ACTIVE' ? 'NOTACTIVE' : 'ACTIVE';

            $wpdb->update($table_name_email, array('status' => $requesting_state), array('id' => $_POST['row_id']));

            echo $requesting_state;

        }

        exit();

    }



    public static function rac_locate_template($template, $template_name, $template_path) {

        global $woocommerce;



        $default_template = $template;

        if (!$template_path) {

            $template_path = $woocommerce->template_url;

        }



        if ("woocommerce" == get_option('rac_email_use_temp_plain')) {

// default

            if (!$default_path) {

                if (function_exists('WC')) {

                    $default_path = WC()->plugin_path() . '/templates/';

                } else {

                    $default_path = $woocommerce->plugin_path() . '/templates/';

                }

            }



            $template = $default_path . $template_name;

        } elseif ("theme" == get_option('rac_email_use_temp_plain')) {

//theme

            $template = locate_template(

                    array(

                        $template_path . $template_name,

                        $template_name

                    )

            );

// default

            if (!$template) {

                $template = $default_template;

            }

        }



// Return what we found

        return $template;

    }



    public static function rac_load_mail_message() {

        if (isset($_POST['row_id'])) {

            global $wpdb;

            $row_id = $_POST['row_id'];

            $table_name = $wpdb->prefix . 'rac_templates_email';

            $templates = $wpdb->get_results("SELECT * FROM $table_name WHERE id=$row_id", OBJECT);

// echo $templates[0]->message;

            $template_send = array("mail_send_opt" => $templates[0]->sender_opt,

                "from_name" => $templates[0]->from_name,

                "from_email" => $templates[0]->from_email,

                "subject" => $templates[0]->subject,

                "message" => $templates[0]->message,

                "cart_link_text" => $templates[0]->anchor_text,

            );

            echo json_encode($template_send);

        }

        exit();

    }



    public static function add_styles_in_general_tab() {

        if (isset($_GET['tab'])) {

            if ($_GET['tab'] == 'fpracemail') {

                ?>

                <script type="text/javascript">

                    jQuery(document).ready(function () {



                        if (jQuery('#rac_cart_link_options').val() === '3') {

                            jQuery('.racbutton').parent().parent().show();

                            jQuery('.raclink').parent().parent().hide();

                        } else if (jQuery('#rac_cart_link_options').val() === '2') {

                            jQuery('.raclink').parent().parent().hide();

                            jQuery('.racbutton').parent().parent().hide();

                        } else {

                            jQuery('.racbutton').parent().parent().hide();

                            jQuery('.raclink').parent().parent().show();

                        }



                        jQuery('#rac_cart_link_options').change(function () {

                            if (jQuery(this).val() === '3') {

                                jQuery('.racbutton').parent().parent().show();

                            } else if (jQuery(this).val() === '2') {

                                jQuery('.racbutton').parent().parent().hide();

                                jQuery('.raclink').parent().parent().hide();

                            } else {

                                jQuery('.racbutton').parent().parent().hide();

                                jQuery('.raclink').parent().parent().show();

                            }

                        });

                    });

                </script>

                <?php

            }



            if ($_GET['tab'] == 'fpraccoupon') {

                ?>

                <script type="text/javascript">

                    jQuery(document).ready(function () {

                        if (jQuery('#rac_prefix_coupon').val() === '1') {

                            jQuery('#rac_manual_prefix_coupon_code').parent().parent().hide();

                        } else {

                            jQuery('#rac_manual_prefix_coupon_code').parent().parent().show();

                        }



                        jQuery('#rac_prefix_coupon').change(function () {

                            if (jQuery(this).val() === '1') {

                                jQuery('#rac_manual_prefix_coupon_code').parent().parent().hide();

                            } else {

                                jQuery('#rac_manual_prefix_coupon_code').parent().parent().show();

                            }

                        });

                    });

                </script>

                <?php

            }

        }

    }



    public static function rac_cart_link_button_mode($cartlink, $cart_text) {

        ob_start();

        ?>

        <table cellspacing="0" cellpadding="0">

            <tr>

                <td align="center" bgcolor="#<?php echo get_option('rac_cart_button_bg_color'); ?>" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block; padding:0px 10px 0px 10px;">

                    <a href="<?php echo $cartlink; ?>" style="text-decoration: none; width:100%; display:inline-block;line-height:40px;"><span style="color: #<?php echo get_option('rac_cart_button_link_color'); ?>"><?php echo $cart_text; ?></span></a>

                </td>

            </tr>

        </table>

        <?php

        $results = ob_get_clean();

        return $results;

    }



    public static function shortcode_in_subject($firstname, $lastname, $subject) {

        $find_array = array('{rac.firstname}', '{rac.lastname}');

        $replace_array = array($firstname, $lastname);

        $subject = str_replace($find_array, $replace_array, $subject);

        return $subject;

    }



    /*

     * Function to save the selected products to exclude

     */



    public static function save_product_to_exclude() {

        update_option('rac_exclude_products_in_coupon', $_POST['rac_exclude_products_in_coupon']);

    }



    /*

     * Function to save select products to include

     */



    public static function save_product_to_include() {

        update_option('rac_include_products_in_coupon', $_POST['rac_include_products_in_coupon']);

    }



    /*

     * Function to select products to exclude

     */



    public static function rac_select_product_to_exclude() {



        global $woocommerce;

        if ((float) $woocommerce->version > (float) ('2.2.0')) {

            ?>

            <tr valign="top">

                <th class="titledesc" scope="row">

                    <label for="rac_exclude_products_in_coupon"><?php _e('Exclude Products from using Coupon', 'recoverabandoncart'); ?></label>

                </th>

                <td class="forminp forminp-select">

                    <input type="hidden" class="wc-product-search" style="width: 350%;" id="rac_exclude_products_in_coupon"  name="rac_exclude_products_in_coupon" data-placeholder="<?php _e('Search for a product&hellip;', 'recoverabandoncart'); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="<?php

                    $json_ids = array();

                    if (get_option('rac_exclude_products_in_coupon') != "") {

                        $list_of_produts = get_option('rac_exclude_products_in_coupon');

                        $product_ids = array_filter(array_map('absint', (array) explode(',', get_option('rac_exclude_products_in_coupon'))));



                        foreach ($product_ids as $product_id) {

                            $product = wc_get_product($product_id);

                            $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());

                        } echo esc_attr(json_encode($json_ids));

                    }

                    ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" />



                    <script type="text/javascript">

                        jQuery(function () {

                            jQuery('.rac_exclude_category_to_enable_redeeming').select2();

                        });

                    </script>

                </td>

            </tr>

        <?php } else { ?>

            <tr valign="top">

                <th class="titledesc" scope="row">

                    <label for="rac_exclude_products_in_coupon"><?php _e('Exclude Products from using Coupon', 'recoverabandoncart'); ?></label>

                </th>

                <td class="forminp forminp-select">

                    <select multiple name="rac_exclude_products_in_coupon" style='width:350px;' id='rac_exclude_products_in_coupon' class="rac_exclude_products_in_coupon rac_include_exclude_products_coupon">

                        <?php

                        $selected_products_exclude = array_filter((array) get_option('rac_exclude_products_in_coupon'));

                        if ($selected_products_exclude != "") {

                            if (!empty($selected_products_exclude)) {

                                $list_of_produts = (array) get_option('rac_exclude_products_in_coupon');

                                foreach ($list_of_produts as $rs_free_id) {

                                    echo '<option value="' . $rs_free_id . '" ';

                                    selected(1, 1);

                                    echo '>' . ' #' . $rs_free_id . ' &ndash; ' . get_the_title($rs_free_id);

                                }

                            }

                        } else {

                            ?>

                            <option value=""></option>

                            <?php

                        }

                        ?>

                    </select>

                    <script type="text/javascript">

                        jQuery(function () {

                            jQuery('.rac_exclude_category_to_enable_redeeming').chosen();

                        });

                    </script>

                </td>

            </tr>

            <?php

        }

    }



    /*

     * Function to select products to include

     */



    public static function rac_select_product_to_include() {



        global $woocommerce;

        if ((float) $woocommerce->version > (float) ('2.2.0')) {

            ?>

            <tr valign="top">

                <th class="titledesc" scope="row">

                    <label for="rac_include_products_in_coupon"><?php _e('Products', 'recoverabandoncart'); ?></label>

                </th>

                <td class="forminp forminp-select">

                    <input type="hidden" class="wc-product-search" style="width: 350%;" id="rac_include_products_in_coupon"  name="rac_include_products_in_coupon" data-placeholder="<?php _e('Search for a product&hellip;', 'recoverabandoncart'); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="<?php

                    $json_ids = array();

                    if (get_option('rac_include_products_in_coupon') != "") {

                        $list_of_produts = get_option('rac_include_products_in_coupon');

                        $product_ids = array_filter(array_map('absint', (array) explode(',', get_option('rac_include_products_in_coupon'))));



                        foreach ($product_ids as $product_id) {

                            $product = wc_get_product($product_id);

                            $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());

                        } echo esc_attr(json_encode($json_ids));

                    }

                    ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" />



                    <script type="text/javascript">

                        jQuery(function () {

                            jQuery('.rac_select_category_to_enable_redeeming').select2();

                        });

                    </script>

                </td>

            </tr>

        <?php } else { ?>

            <tr valign="top">

                <th class="titledesc" scope="row">

                    <label for="rac_include_products_in_coupon"><?php _e('Products', 'recoverabandoncart'); ?></label>

                </th>

                <td class="forminp forminp-select">

                    <select multiple name="rac_include_products_in_coupon" style='width:350px;' id='rac_include_products_in_coupon' class="rac_include_products_in_coupon rac_include_exclude_products_coupon">

                        <?php

                        $selected_products_include = array_filter((array) get_option('rac_include_products_in_coupon'));

                        if ($selected_products_include != "") {

                            if (!empty($selected_products_include)) {

                                $list_of_produts = (array) get_option('rac_include_products_in_coupon');

                                foreach ($list_of_produts as $rs_free_id) {

                                    echo '<option value="' . $rs_free_id . '" ';

                                    selected(1, 1);

                                    echo '>' . ' #' . $rs_free_id . ' &ndash; ' . get_the_title($rs_free_id);

                                }

                            }

                        } else {

                            ?>

                            <option value=""></option>

                            <?php

                        }

                        ?>

                    </select>



                    <script type="text/javascript">

                        jQuery(function () {

                            jQuery('.rac_select_category_to_enable_redeeming').chosen();

                        });

                    </script>

                </td>

            </tr>

            <?php

        }

    }



    public static function add_script_on_coupon_tab() {

        if (isset($_GET['tab'])) {

            if ($_GET['tab'] == 'fpraccoupon') {

                echo self::rac_common_ajax_function_to_select_products('rac_include_exclude_products_coupon');

            }

        }

    }



    public static function rac_common_ajax_function_to_select_products($ajaxid) {

        global $woocommerce;

        ob_start();

        ?>

        <script type="text/javascript">

        <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>

                jQuery(function () {

                    jQuery("select.<?php echo $ajaxid; ?>").ajaxChosen({

                        method: 'GET',

                        url: '<?php echo admin_url('admin-ajax.php'); ?>',

                        dataType: 'json',

                        afterTypeDelay: 100,

                        data: {

                            action: 'woocommerce_json_search_products_and_variations',

                            security: '<?php echo wp_create_nonce("search-products"); ?>'

                        }

                    }, function (data) {

                        var terms = {};



                        jQuery.each(data, function (i, val) {

                            terms[i] = val;

                        });

                        return terms;

                    });

                });

        <?php } ?>

        </script>

        <?php

        $getcontent = ob_get_clean();

        return $getcontent;

    }



    public static function rac_send_manual_mail() {

        global $wpdb, $woocommerce, $to;

        $table_name = $wpdb->prefix . 'rac_abandoncart';

// $emailtemplate_table_name = $wpdb->prefix . 'rac_templates_email';

        $abandancart_table_name = $wpdb->prefix . 'rac_abandoncart';

        $sender_option_post = stripslashes($_POST['rac_sender_option']);

        $from_name_post = stripslashes($_POST['rac_from_name']);

        $from_email_post = stripslashes($_POST['rac_from_email']);

        $message_post = stripslashes($_POST['rac_message']);

        $subject_post = stripslashes($_POST['rac_mail_subject']);

        $anchor_text_post = stripslashes($_POST['rac_anchor_text']);

        $mail_row_ids = stripslashes($_POST['rac_mail_row_ids']);

        $row_id_array = explode(',', $mail_row_ids);

        $mail_template_id_post = isset($_POST['template_id']) ? $_POST['template_id'] : '';

        $table_name_email = $wpdb->prefix . 'rac_templates_email';

        ?>

        <style type="text/css">

            table {

                border-collapse: separate;

                border-spacing: 0;

                color: #4a4a4d;

                font: 14px/1.4 "Helvetica Neue", Helvetica, Arial, sans-serif;

            }

            th,

            td {

                padding: 10px 15px;

                vertical-align: middle;

            }

            thead {

                background: #395870;

                background: linear-gradient(#49708f, #293f50);

                color: #fff;

                font-size: 11px;

                text-transform: uppercase;

            }

            th:first-child {

                border-top-left-radius: 5px;

                text-align: left;

            }

            th:last-child {

                border-top-right-radius: 5px;

            }

            tbody tr:nth-child(even) {

                background: #f0f0f2;

            }

            td {

                border-bottom: 1px solid #cecfd5;

                border-right: 1px solid #cecfd5;

            }

            td:first-child {

                border-left: 1px solid #cecfd5;

            }

            .book-title {

                color: #395870;

                display: block;

            }

            .text-offset {

                color: #7c7c80;

                font-size: 12px;

            }

            .item-stock,

            .item-qty {

                text-align: center;

            }

            .item-price {

                text-align: right;

            }

            .item-multiple {

                display: block;

            }

            tfoot {

                text-align: right;

            }

            tfoot tr:last-child {

                background: #f0f0f2;

                color: #395870;

                font-weight: bold;

            }

            tfoot tr:last-child td:first-child {

                border-bottom-left-radius: 5px;

            }

            tfoot tr:last-child td:last-child {

                border-bottom-right-radius: 5px;

            }



        </style>

        <?php

//$mail_temp_row = $wpdb->get_results("SELECT * FROM $table_name_email WHERE id=$template_id_post", OBJECT);

        $headers = "MIME-Version: 1.0\r\n";

        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        if ($sender_option_post == 'local') {

            $headers .= FPRacCron::rac_formatted_from_address_local($from_name_post, $from_email_post);

            $headers .= "Reply-To: " . $from_name_post . " <" . $from_email_post . ">\r\n";

        } else {

            $headers .= FPRacCron::rac_formatted_from_address_woocommerce();

            $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";

        }

        foreach ($row_id_array as $row_id) {

            $cart_row = $wpdb->get_results("SELECT * FROM $table_name WHERE id=$row_id", OBJECT);

//echo $cart_row[0]->user_id;

//For Member



            $cart_array = maybe_unserialize($cart_row[0]->cart_details);



            $tablecheckproduct = "<table style='width:100%;border:1px solid #eee;'><thead><tr>";

            if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {

                $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_name', $cart_row[0]->wpml_lang, get_option('rac_product_info_product_name')) . "</th>";

            }

            if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {

                $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_image', $cart_row[0]->wpml_lang, get_option('rac_product_info_product_image')) . "</th>";

            }

            if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {

                $tablecheckproduct .= "<th style='text-align:left;border:1px solid #eee;padding:12px' scope='col'>" . fp_get_wpml_text('rac_template_product_price', $cart_row[0]->wpml_lang, get_option('rac_product_info_product_price')) . "</th>";

            }

            $tablecheckproduct .= "</tr></thead><tbody>";



            if (is_array($cart_array)) {

                $i = 1;

                foreach ($cart_array as $cart) {

                    if (is_array($cart)) {

                        foreach ($cart as $inside) {

                            if (is_array($inside)) {

                                foreach ($inside as $product) {

                                    if ($cart_row[0]->user_id != '0' && $cart_row[0]->user_id != 'old_order') {

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



                                            $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . FPRacCron::get_rac_formatprice($product['variation_id'] == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";

                                        }



                                        $tablecheckproduct .= "</tr>";

                                    }

                                }

                            }

                        }

                    } else {



                        if ($i == '3') {

                            $get_array_keys = array_keys($cart_array);

                            $product_id = $cart_array[$get_array_keys[0]]['product_id'];

                            $variation_id = $cart_array[$get_array_keys[0]]['variation_id'];

                            if ((float) $woocommerce->version <= (float) ('2.0.20')) {

                                $objectproduct = get_product($product_id);

                                $objectproductvariable = get_product($variation_id);

                            } else {

                                $objectproduct = new WC_Product($product_id);

                                $objectproductvariable = new WC_Product_Variation($variation_id);

                            }

                            $tablecheckproduct .= "<tr>";

                            if (get_option('rac_hide_product_name_product_info_shortcode') != 'yes') {

                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_title($product_id) . "</td>";

                            }

                            if (get_option('rac_hide_product_image_product_info_shortcode') != 'yes') {

                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . get_the_post_thumbnail($product_id, array(90, 90)) . "</td>";

                            }

                            if (get_option('rac_hide_product_price_product_info_shortcode') != 'yes') {

                                $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . FPRacCron::get_rac_formatprice($variation_id == '' ? $objectproduct->get_price() : $objectproductvariable->get_price()) . "</td>";

                            }

                            $tablecheckproduct .= "</tr>";

                        }

                        $i++;

                    }

                }

            } elseif (is_object($cart_array)) {

                $order = new WC_Order($cart_array->id);

//  if ($order->user_id != '') {

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



                        $tablecheckproduct .= "<td style='text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px'>" . FPRacCron::get_rac_formatprice($products['line_total']) . "</td>";

                    }



                    $tablecheckproduct .= "</tr>";

                }

//}

            }

            $tablecheckproduct .= "</table>";





            if ($cart_row[0]->user_id != '0' && $cart_row[0]->user_id != 'old_order') {

//echo 'member';

                $sent_mail_templates = maybe_unserialize($cart_row[0]->mail_template_id);

                if (!is_array($sent_mail_templates)) {

                    $sent_mail_templates = array(); // to avoid mail sent/not sent problem for serialization on store

                }





                $current_time = current_time('timestamp');

                @$cart_url = WC_Cart::get_cart_url();

                $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $cart_row[0]->id, 'email_template' => $mail_template_id_post), $cart_url));



                if (get_option('rac_cart_link_options') == '1') {

                    $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post) . '</a>';

                } elseif (get_option('rac_cart_link_options') == '2') {

                    $url_to_click = $url_to_click;

                } else {

                    $cart_text = fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post);

                    $url_to_click = self::rac_cart_link_button_mode($url_to_click, $cart_text);

                }



//$url_to_click = '<a href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post) . '</a>';



                $user = get_userdata($cart_row[0]->user_id);

                $to = $user->user_email;

                $firstname = $user->user_firstname;

                $lastname = $user->user_lastname;

                $subject = self::shortcode_in_subject($firstname, $lastname, $subject_post);







                $message = str_replace('{rac.cartlink}', $url_to_click, $message_post);

                $message = str_replace('{rac.firstname}', $firstname, $message);

                $message = str_replace('{rac.lastname}', $lastname, $message);



                $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                if (strpos($message, "{rac.coupon}")) {

                    $coupon_code = FPRacCoupon::rac_create_coupon($user->user_email, $cart_row[0]->cart_abandon_time);

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



                if ('wp_mail' == get_option('rac_trouble_mail')) {

                    if (FPRacCron::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {

//wp_mail($to, $subject, $message);

                        $sent_mail_templates[] = $mail_template_id_post;

                        $store_template_id = maybe_serialize($sent_mail_templates);

                        $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $cart_row[0]->id));

//add to mail log

                        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

                        $template_used = $mail_template_id_post . '- Manual';

                        $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $cart_row[0]->id, "template_used" => $template_used));

                        FPRacCounter::rac_do_mail_count();

                    }

                } else {

                    if (FPRacCron::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {

//wp_mail($to, $subject, $message);

                        $sent_mail_templates[] = $mail_template_id_post;

                        $store_template_id = maybe_serialize($sent_mail_templates);

                        $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $cart_row[0]->id));

//add to mail log

                        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

                        $template_used = $mail_template_id_post . '- Manual';

                        $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $cart_row[0]->id, "template_used" => $template_used));

                        FPRacCounter::rac_do_mail_count();

                    }

                }

            }

//End Member

//FOR Guest at place order

            if ($cart_row[0]->user_id === '0' && is_null($cart_row[0]->ip_address)) {

// echo 'guest';

                $sent_mail_templates = maybe_unserialize($cart_row[0]->mail_template_id);

                if (!is_array($sent_mail_templates)) {

                    $sent_mail_templates = array(); // to avoid mail sent/not sent problem for serialization on store

                }

                $current_time = current_time('timestamp');

                @$cart_url = WC_Cart::get_cart_url();

                $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $cart_row[0]->id, 'email_template' => $mail_template_id_post, 'guest' => 'yes'), $cart_url));

//$url_to_click = '<a href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post) . '</a>';



                if (get_option('rac_cart_link_options') == '1') {

                    $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '" href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post) . '</a>';

                } elseif (get_option('rac_cart_link_options') == '2') {

                    $url_to_click = $url_to_click;

                } else {

                    $cart_text = fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post);

                    $url_to_click = self::rac_cart_link_button_mode($url_to_click, $cart_text);

                }



//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE

                $order_object = maybe_unserialize($cart_row[0]->cart_details);

                $to = $order_object->billing_email;



                $firstname = $order_object->billing_first_name;

                $lastname = $order_object->billing_last_name;



                $subject = self::shortcode_in_subject($firstname, $lastname, $subject_post);

                $message = str_replace('{rac.cartlink}', $url_to_click, $message_post);

                $message = str_replace('{rac.firstname}', $firstname, $message);

                $message = str_replace('{rac.lastname}', $lastname, $message);

                $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                if (strpos($message, "{rac.coupon}")) {

                    $coupon_code = FPRacCoupon::rac_create_coupon($order_object->billing_email, $cart_row[0]->cart_abandon_time);

                    $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code

                }

                $message = do_shortcode($message); //shortcode feature

                add_filter('woocommerce_email_footer_text', array('RecoverAbandonCart', 'rac_footer_email_customization'));

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



                if ('wp_mail' == get_option('rac_trouble_mail')) {

                    if (FPRacCron::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {

// wp_mail($to, $subject, $message);

                        $sent_mail_templates[] = $mail_template_id_post;

                        $store_template_id = maybe_serialize($sent_mail_templates);

                        $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $cart_row[0]->id));

//add to mail log

                        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

                        $template_used = $mail_template_id_post . '- Manual';

                        $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $cart_row[0]->id, "template_used" => $template_used));

                        FPRacCounter::rac_do_mail_count();

                    }

                } else {

                    if (FPRacCron::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {

// wp_mail($to, $subject, $message);

                        $sent_mail_templates[] = $mail_template_id_post;

                        $store_template_id = maybe_serialize($store_template_id);

                        $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $each_cart->id));

//add to mail log

                        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

                        $template_used = $mail_template_id_post . '- Manual';

                        $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $each_cart->id, "template_used" => $template_used));

                        FPRacCounter::rac_do_mail_count();

                    }

                }

            }

//END Guest

//GUEST Checkout

            if ($cart_row[0]->user_id == '0' && !is_null($cart_row[0]->ip_address)) {

// echo 'checkout';

                $sent_mail_templates = maybe_unserialize($each_cart->mail_template_id);

                if (!is_array($sent_mail_templates)) {

                    $sent_mail_templates = array(); // to avoid mail sent/not sent problem for serialization on store

                }

                $current_time = current_time('timestamp');

                @$cart_url = WC_Cart::get_cart_url();

                $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $cart_row[0]->id, 'email_template' => $mail_template_id_post, 'guest' => 'yes', 'checkout' => 'yes'), $cart_url));





                if (get_option('rac_cart_link_options') == '1') {

                    $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post) . '</a>';

                } elseif (get_option('rac_cart_link_options') == '2') {

                    $url_to_click = $url_to_click;

                } else {

                    $cart_text = fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post);

                    $url_to_click = self::rac_cart_link_button_mode($url_to_click, $cart_text);

                }



//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE

                $order_object = maybe_unserialize($cart_row[0]->cart_details);

                $to = $order_object['visitor_mail'];



                $firstname = $order_object['first_name'];

                $lastname = $order_object['last_name'];

                $message = str_replace('{rac.cartlink}', $url_to_click, $message_post);

                $subject = self::shortcode_in_subject($firstname, $lastname, $subject_post);

                $message = str_replace('{rac.firstname}', $firstname, $message);

                $message = str_replace('{rac.lastname}', $lastname, $message);

                $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                if (strpos($message, "{rac.coupon}")) {

                    $coupon_code = FPRacCoupon::rac_create_coupon($order_object['visitor_mail'], $cart_row[0]->cart_abandon_time);

                    $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code

                }

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



                if ('wp_mail' == get_option('rac_trouble_mail')) {

                    if (FPRacCron::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {

// wp_mail($to, $subject, $message);

                        $sent_mail_templates[] = $mail_template_id_post;

                        $store_template_id = maybe_serialize($sent_mail_templates);

                        $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $cart_row[0]->id));

//add to mail log

                        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

                        $template_used = $mail_template_id_post . '- Manual';

                        $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $cart_row[0]->id, "template_used" => $template_used));

                        FPRacCounter::rac_do_mail_count();

                    }

                } else {

                    if (FPRacCron::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {

// wp_mail($to, $subject, $message);

                        $sent_mail_templates[] = $emails->id;

                        $store_template_id = maybe_serialize($sent_mail_templates);

                        $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $cart_row[0]->id));

//add to mail log

                        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

                        $template_used = $mail_template_id_post . '- Manual';

                        $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $cart_row[0]->id, "template_used" => $template_used));

                        FPRacCounter::rac_do_mail_count();

                    }

                }

            }

//END Checkout

//Order Updated

            if ($cart_row[0]->user_id == 'old_order' && is_null($cart_row[0]->ip_address)) {

// echo 'order';

                $sent_mail_templates = maybe_unserialize($each_cart->mail_template_id);

                $current_time = current_time('timestamp');

                @$cart_url = WC_Cart::get_cart_url();

                $url_to_click = esc_url_raw(add_query_arg(array('abandon_cart' => $cart_row[0]->id, 'email_template' => $mail_template_id_post, 'old_order' => 'yes'), $cart_url));

//$url_to_click = '<a href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post) . '</a>';



                if (get_option('rac_cart_link_options') == '1') {

                    $url_to_click = '<a style="color:' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post) . '</a>';

                } elseif (get_option('rac_cart_link_options') == '2') {

                    $url_to_click = $url_to_click;

                } else {

                    $cart_text = fp_get_wpml_text('rac_template_' . $mail_template_id_post . '_anchor_text', $cart_row[0]->wpml_lang, $anchor_text_post);

                    $url_to_click = self::rac_cart_link_button_mode($url_to_click, $cart_text);

                }





//  $user = get_userdata($each_cart->user_id); NOT APPLICABLE

                $order_object = maybe_unserialize($cart_row[0]->cart_details);

                $to = $order_object->billing_email;



                $firstname = $order_object->billing_first_name;

                $lastname = $order_object->billing_last_name;

                $message = str_replace('{rac.cartlink}', $url_to_click, $message_post);

                $subject = self::shortcode_in_subject($firstname, $lastname, $subject_post);

                $message = str_replace('{rac.firstname}', $firstname, $message);

                $message = str_replace('{rac.lastname}', $lastname, $message);

                $message = str_replace('{rac.Productinfo}', $tablecheckproduct, $message);

                if (strpos($message, "{rac.coupon}")) {

                    $coupon_code = FPRacCoupon::rac_create_coupon($order_object->billing_email, $cart_row[0]->cart_abandon_time);

                    $message = str_replace('{rac.coupon}', $coupon_code, $message); //replacing shortcode with coupon code

                }

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

                if ('wp_mail' == get_option('rac_trouble_mail')) {

                    if (FPRacCron::rac_send_wp_mail($to, $subject, $woo_temp_msg, $headers)) {

// wp_mail($to, $subject, $message);

                        $sent_mail_templates[] = $mail_template_id_post;

                        $store_template_id = maybe_serialize($sent_mail_templates);

                        $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $cart_row[0]->id));

//add to mail log

                        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

                        $template_used = $mail_template_id_post . '- Manual';

                        $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $cart_row[0]->id, "template_used" => $template_used));

                        FPRacCounter::rac_do_mail_count();

                    }

                } else {

                    if (FPRacCron::rac_send_mail($to, $subject, $woo_temp_msg, $headers)) {

// wp_mail($to, $subject, $message);

                        $sent_mail_templates[] = $mail_template_id_post;

                        $store_template_id = maybe_serialize($sent_mail_templates);

                        $wpdb->update($abandancart_table_name, array('mail_template_id' => $store_template_id), array('id' => $cart_row[0]->id));

//add to mail log

                        $table_name_logs = $wpdb->prefix . 'rac_email_logs';

                        $template_used = $mail_template_id_post . '- Manual';

                        $wpdb->insert($table_name_logs, array("email_id" => $to, "date_time" => $current_time, "rac_cart_id" => $cart_row[0]->id, "template_used" => $template_used));

                        FPRacCounter::rac_do_mail_count();

                    }

                }

            }

// var_dump($cart_row[0]->user_id);

        }



        exit();

    }



    public static function rac_send_test_mail() {

        $to = $_POST['rac_test_mail_to'];

        $plain_or_html = $_POST['rac_plain_or_html'] == '1' ? "plain" : "html";



        $subject = "Test E-Mail";







        $message = "This is a test E-Mail to Make sure E-Mail are sent successfully from your site.";



        if ($plain_or_html == 'html') {

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

            $message = ob_get_clean();

        } else {

            $message = $message;

        }



        $headers = "MIME-Version: 1.0\r\n";

        $headers .= FPRacCron::rac_formatted_from_address_woocommerce();

        $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";

        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        if ('wp_mail' == get_option('rac_trouble_mail')) {

            if (FPRacCron::rac_send_wp_mail_test($to, $subject, $message, $headers)) {

                echo "sent";

            }

        } else {

            if (FPRacCron::rac_send_mail_test($to, $subject, $message, $headers)) {

                echo "sent";

            }

        }



        exit();

    }



    public static function fp_rac_reports() {

        ?>

        <table class="rac_reports form-table">

            <tr>

                <th>

                    Number of Abandoned Carts Captured

                </th>

                <td>

                    <?php

                    if (get_option('rac_abandoned_count')) {

                        echo get_option('rac_abandoned_count');

                    } else {// if it is boolean false then there is no value. so give 0

                        echo "0";

                    };

                    ?>

                </td>

            </tr>

            <tr>

                <th>

                    Number of total Emails Sent

                </th>

                <td>

                    <?php

                    if (get_option('rac_mail_count')) {

                        echo get_option('rac_mail_count');

                    } else {

                        echo "0";

                    }

                    ?>

                </td>

            </tr>

            <tr>

                <th>

                    Number of total Email links clicked

                </th>

                <td>

                    <?php

                    if (get_option('rac_link_count')) {

                        echo get_option('rac_link_count');

                    } else {

                        echo "0";

                    }

                    ?>

                </td>

            </tr>

            <tr>

                <th>

                    Number of Carts Recovered

                </th>

                <td>

                    <?php

                    if (get_option('rac_recovered_count')) {

                        echo get_option('rac_recovered_count');

                    } else {

                        echo "0";

                    }

                    ?>

                </td>

            </tr>

        </table>

        <style type="text/css">.rac_reports {

                width:50%;

                background-color:white;

                border:2px solid #21759b;

                border-collapse:unset;

                border-top: 4px solid #21759b;

                margin-top: 20px !important;



            }

            .rac_reports th{

                padding: 20px;

            }

        </style>

        <?php

    }



    public static function fp_rac_shortcodes_info() {

        $shortcodes_info = array(

            "{rac.cartlink}" => array("mail" => "Abandoned Cart Mail",

                "usage" => "Abandoned Cart can be loaded using this link from mail"

            ),

            "{rac.firstname}" => array("mail" => "Abandoned Cart Mail",

                "usage" => "Shows Receiver First Name"),

            "{rac.lastname}" => array("mail" => "Abandoned Cart Mail",

                "usage" => "Shows Receiver Last Name"),

            "{rac.recovered_order_id}" => array("mail" => "Admin Order Recovered Notification Mail",

                "usage" => "Order ID can be inserted in the admin notification mail for Reference"),

            "{rac.order_line_items}" => array("mail" => "Admin Order Line Items in Recovered Notification Mail",

                "usage" => "Order Line Items will be displayed in Admin Notification Mail for Information"),

            "{rac.Productinfo}" => array("mail" => "Abandoned Cart Mail",

                "usage" => "Shows Product Information Name Image Amount "),

            "{rac.coupon}" => array("mail" => "Abandoned Cart Mail",

                "usage" => "Copon code will be generated automatically and included in the mail with a Coupon options based on the settings from 'Coupon In Mail' tab"));

        ?>

        <table class="rac_shortcodes_info">

            <thead>

                <tr>

                    <th>

                        Shortcode

                    </th>

                    <th>

                        Context where Shortcode is valid

                    </th>

                    <th>

                        Purpose

                    </th>

                </tr>

            </thead>

            <?php foreach ($shortcodes_info as $shortcode => $s_info) { ?>

                <tr>

                    <td>

                        <?php echo $shortcode; ?>

                    </td>

                    <td>

                        <?php echo $s_info['mail']; ?>

                    </td>

                    <td>

                        <?php echo $s_info['usage']; ?>

                    </td>

                </tr>

            <?php } ?>

        </table>

        <style type="text/css">

            .rac_shortcodes_info{

                margin-top:20px;

            }

        </style>

        <?php

    }



    public static function get_user_role_jquery() {

        global $woocommerce;

        ?>

        <script type="text/javascript">

            jQuery(function () {

        <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>



                    jQuery("#custom_user_role").chosen();

                    jQuery("#custom_user_name_select").chosen();





        <?php } else {

            ?>



                    jQuery("#custom_user_role").select2();

                    //jQuery("#custom_user_name_select").select2();



        <?php }

        ?>



                var getselectedvalue = jQuery('#custom_exclude').val() || [];



                if (getselectedvalue === 'user_role') {

                    jQuery('#custom_user_role').parent().parent().css("display", "table-row");



                }

                else {

                    jQuery('#custom_user_role').parent().parent().css("display", "none");



                }



                jQuery('#custom_exclude').change(function () {



                    if (jQuery(this).val() === 'user_role') {



                        jQuery('#custom_user_role').parent().parent().css("display", "table-row");



                    }

                    else {

                        jQuery('#custom_user_role').parent().parent().css("display", "none");



                    }





                });



                var getselecteddvalue = jQuery('#custom_exclude').val() || [];



                if (getselecteddvalue === 'name') {

                    jQuery('#custom_user_name_select').parent().parent().css("display", "table-row");



                } else {

                    jQuery('#custom_user_name_select').parent().parent().css("display", "none");



                }



                jQuery('#custom_exclude').change(function () {



                    if (jQuery(this).val() === 'name') {



                        jQuery('#custom_user_name_select').parent().parent().css("display", "table-row");

                    } else {



                        jQuery('#custom_user_name_select').parent().parent().css("display", "none");



                    }



                });



                var getselecteddvalue = jQuery('#custom_exclude').val() || [];



                if (getselecteddvalue === 'mail_id') {

                    jQuery('#custom_mailid_edit').parent().parent().css("display", "table-row");



                } else {

                    jQuery('#custom_mailid_edit').parent().parent().css("display", "none");



                }



                jQuery('#custom_exclude').change(function () {



                    if (jQuery(this).val() === 'mail_id') {

                        jQuery('#custom_mailid_edit').parent().parent().css("display", "table-row");



                    } else {



                        jQuery('#custom_mailid_edit').parent().parent().css("display", "none");



                    }



                });



            });





        </script>

        <?php

    }



}



//add_action('wp_head', array('RecoverAbandonCart', 'wow'));

if (isset($_GET['tab'])) {

    if ($_GET['tab'] == 'fpracemail') {

        add_action('admin_head', array('RecoverAbandonCart', 'get_user_role_jquery'));

    }

}

add_action('admin_init', array('RecoverAbandonCart', 'fprac_check_woo_active'));

add_action('admin_menu', array('RecoverAbandonCart', 'fprac_admin_submenu'));

add_action('admin_init', array('RecoverAbandonCart', 'fp_rac_reset_general'));



$fp_rac = plugin_basename(__FILE__);

add_filter("plugin_action_links_$fp_rac", array('RecoverAbandonCart', 'fp_rac_settings_link'));

//if (get_option('rac_email_use_temp_plain') != 'plain') {

//    add_filter('woocommerce_locate_template', array('RecoverAbandonCart', 'rac_locate_template'), 10, 3);

//}



add_filter('woocommerce_fprac_settings_tabs_array', array('RecoverAbandonCart', 'fprac_settings_tabs'));

if (isset($_GET['page'])) {

    if ($_GET['page'] == 'fprac_slug') {

        add_filter('woocommerce_screen_ids', array('RecoverAbandonCart', 'fprac_access_woo_script'), 9, 1);

    }

}





add_action('admin_head', array('RecoverAbandonCart', 'add_script_on_coupon_tab'));

add_action('woocommerce_update_options_fpraccoupon', array('RecoverAbandonCart', 'save_product_to_exclude'));

add_action('woocommerce_update_options_fpraccoupon', array('RecoverAbandonCart', 'save_product_to_include'));

add_action('woocommerce_admin_field_rac_coupon_exclude_products', array('RecoverAbandonCart', 'rac_select_product_to_exclude'));

add_action('woocommerce_admin_field_rac_coupon_include_products', array('RecoverAbandonCart', 'rac_select_product_to_include'));



add_action('init', array('RecoverAbandonCart', 'fprac_header_problems'));

add_action('woocommerce_fprac_settings_tabs_fpracgenral', array('RecoverAbandonCart', 'fp_rac_admin_setting_general'));

add_action('woocommerce_update_options_fpracgenral', array('RecoverAbandonCart', 'fp_rac_update_options_general'));

add_action('woocommerce_fprac_settings_tabs_fpracdebug', array('RecoverAbandonCart', 'fp_rac_admin_setting_troubleshoot'));

add_action('woocommerce_update_options_fpracdebug', array('RecoverAbandonCart', 'fp_rac_update_options_troubleshoot'));

add_action('woocommerce_fprac_settings_tabs_fpracemail', array('RecoverAbandonCart', 'fp_rac_admin_setting_email'));

add_action('woocommerce_update_options_fpracemail', array('RecoverAbandonCart', 'fp_rac_update_options_email'));

add_action('woocommerce_fprac_settings_tabs_fpraccoupon', array('RecoverAbandonCart', 'fp_rac_admin_setting_coupon'));

add_action('woocommerce_update_options_fpraccoupon', array('RecoverAbandonCart', 'fp_rac_update_options_coupon'));

add_action('woocommerce_admin_field_rac_exclude_users_list', array('RecoverAbandonCart', 'rac_selected_users_exclude_option'));



//add_action('wp_head', array('RecoverAbandonCart', 'fp_rac_insert_entry'));

add_action('woocommerce_cart_updated', array('RecoverAbandonCart', 'fp_rac_insert_entry'));

add_action('woocommerce_init', array('RecoverAbandonCart', 'fp_rac_add_abandon_cart'));

add_action('admin_enqueue_scripts', array('RecoverAbandonCart', 'fp_rac_admin_scritps'));

add_action('wp_ajax_rac_new_template', array('RecoverAbandonCart', 'fp_rac_create_new_email_template'));

add_action('wp_ajax_rac_edit_template', array('RecoverAbandonCart', 'fp_rac_edit_email_template'));

add_action('wp_ajax_rac_delete_email_template', array('RecoverAbandonCart', 'fp_rac_delete_email_template'));



add_action('wp_ajax_deletecartlist', array('RecoverAbandonCart', 'delete_all_rac_list'));

add_action('wp_ajax_rac_delete_individual_list', array('RecoverAbandonCart', 'delete_individual_rac_list'));

add_action('wp_ajax_deletemaillog', array('RecoverAbandonCart', 'delete_all_rac_log'));

add_action('wp_ajax_rac_delete_individual_log', array('RecoverAbandonCart', 'delete_individual_rac_log'));

add_action('wp_ajax_rac_start_stop_mail', array('RecoverAbandonCart', 'set_mail_sending_opt'));

add_action('wp_ajax_rac_email_template_status', array('RecoverAbandonCart', 'set_email_template_status'));

add_action('wp_ajax_rac_load_mail_message', array('RecoverAbandonCart', 'rac_load_mail_message'));

add_action('wp_ajax_rac_manual_mail_ajax', array('RecoverAbandonCart', 'rac_send_manual_mail'));

add_action('wp_ajax_rac_send_test_mail', array('RecoverAbandonCart', 'rac_send_test_mail'));



add_action('woocommerce_checkout_order_processed', array('RecoverAbandonCart', 'fp_rac_cookies_for_cart_recover'));

add_action('woocommerce_order_status_completed', array('RecoverAbandonCart', 'fp_rac_check_order_status'));

add_action('woocommerce_order_status_processing', array('RecoverAbandonCart', 'fp_rac_check_order_status'));



add_action('woocommerce_checkout_order_processed', array('RecoverAbandonCart', 'fp_rac_insert_guest_entry'));

//add_action('woocommerce_order_status_completed', array('RecoverAbandonCart', 'fp_rac_order_status_guest'));

//

//

//add_action('woocommerce_order_status_completed', array('RecoverAbandonCart', 'fp_rac_order_status_guest'));

add_action('woocommerce_order_status_changed', array('RecoverAbandonCart', 'fp_rac_order_status_guest'), 10, 3);

add_action('plugins_loaded', array('RecoverAbandonCart', 'rac_translate_file'));

require_once 'inc/fp_rac_cron.php';

register_activation_hook(__FILE__, array('RecoverAbandonCart', 'fp_rac_create_table'));

register_activation_hook(__FILE__, array('RecoverAbandonCart', 'fprac_default_settings'));

register_activation_hook(__FILE__, array('FPRacCron', 'fp_rac_cron_job_setting'));

register_activation_hook(__FILE__, array('RecoverAbandonCart', 'fprac_header_problems'));



add_action('rac_cron_job', array('FPRacCron', 'fp_rac_cron_job_mailing'));

add_filter('cron_schedules', array('FPRacCron', 'fp_rac_add_x_hourly'));

add_action('update_option_rac_abandon_cart_cron_type', array('FPRacCron', 'fp_rac_cron_job_setting_savings'));

add_action('update_option_rac_abandon_cron_time', array('FPRacCron', 'fp_rac_cron_job_setting_savings'));



//add_action('wp_head', array('FPRacCron', 'fp_rac_cron_job_mailing'));



add_action('wp_head', array('RecoverAbandonCart', 'fp_rac_guest_cart_recover'));

add_action('wp_head', array('RecoverAbandonCart', 'recover_old_order_rac'));

add_action('wp_ajax_rac_add_old_order', array('RecoverAbandonCart', 'fp_rac_add_old_order_byupdate'));





add_action('wp_head', array('RecoverAbandonCart', 'fp_rac_checkout_script'));





add_action('wp_ajax_nopriv_rac_preadd_guest', array('RecoverAbandonCart', 'fp_rac_guest_entry_checkout_ajax'));



//add_action('woocommerce_payment_complete', array('RecoverAbandonCart', 'remove_member_acart_on_orderplaced'));

add_action('woocommerce_checkout_order_processed', array('RecoverAbandonCart', 'remove_member_acart_on_orderplaced'));



add_action('admin_head', array('RecoverAbandonCart', 'fp_rac_troubleshoot_mailsend'));



add_action('wp_head', array('RecoverAbandonCart', 'unsubscribed_user_from_rac_mail'));



add_action('woocommerce_before_my_account', array('RecoverAbandonCart', 'add_undo_unsubscribe_option_myaccount'));



add_action('wp_ajax_fp_rac_undo_unsubscribe', array('RecoverAbandonCart', 'response_unsubscribe_option_myaccount'));





add_action('admin_head', array('RecoverAbandonCart', 'add_styles_in_general_tab'));

//For Deletion of coupon code

require_once 'inc/fp_rac_coupon_deletion.php';

//For Backword Compatibility

require_once 'inc/rac_settings_backward_compatibility.php';



//For WPML

function fp_get_wpml_text($option_name, $language, $message) {

    if (function_exists('icl_register_string')) {

        if ($language == 'en') {

            return $message;

        } else {

            global $wpdb;

            $context = 'RAC';



            $res = $wpdb->get_results($wpdb->prepare("

            SELECT s.name, s.value, t.value AS translation_value, t.status

            FROM  {$wpdb->prefix}icl_strings s

            LEFT JOIN {$wpdb->prefix}icl_string_translations t ON s.id = t.string_id

            WHERE s.context = %s

                AND (t.language = %s OR t.language IS NULL)

            ", $context, $language), ARRAY_A);

            foreach ($res as $each_entry) {

                if ($each_entry['name'] == $option_name) {

                    if ($each_entry['status'] == '1') {

                        $translated = $each_entry['translation_value'];

                    } else {

                        $translated = $each_entry['value'];

                    }

                }

            }

            return $translated;

        }

    } else {

        return $message;

    }

}



function rac_register_template_for_wpml() {



    if (function_exists('icl_register_string')) {



        global $wpdb;

        $context = 'RAC';

        $template_table = $wpdb->prefix . 'rac_templates_email';

        $re = $wpdb->get_results("SELECT * FROM $template_table");

        foreach ($re as $each_template) {

            $name_msg = 'rac_template_' . $each_template->id . '_message';

            $value_msg = $each_template->message;

            icl_register_string($context, $name_msg, $value_msg); //for registering message

            $name_sub = 'rac_template_' . $each_template->id . '_subject';

            $value_sub = $each_template->subject;

            icl_register_string($context, $name_sub, $value_sub); //for registering subject



            $name_anchortext = 'rac_template_' . $each_template->id . '_anchor_text';

            $getvalue_anchortext = $each_template->anchor_text;

            icl_register_string($context, $name_anchortext, $getvalue_anchortext);



            $productname = 'rac_template_product_name';

            $getvalue_productname = get_option('rac_product_info_product_name');

            icl_register_string($context, $productname, $getvalue_productname);



            $productimage = 'rac_template_product_image';

            $getvalue_productimage = get_option('rac_product_info_product_image');

            icl_register_string($context, $productimage, $getvalue_productimage);



            $productprice = 'rac_template_product_price';

            $getvalue_productprice = get_option('rac_product_info_product_price');

            icl_register_string($context, $productprice, $getvalue_productprice);

        }

    }

//var_dump($re);

}



add_action('admin_init', 'rac_register_template_for_wpml');

