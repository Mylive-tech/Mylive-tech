<?php
/**
 * @package Set email sender
 * @version 0.1
 */
/*
Plugin Name: Set email sender
Plugin URI: https://citynetwork.se/
Description: This plugin makes Wordpress set the sender address of en email correctly.
Author: Johan Hedberg, City Network Hosting AB
Version: 0.1
Author URI: https://citynetwork.se/
*/

function cn_wpplug_setemailsender($params) {
        if(filter_var($params->Sender, FILTER_VALIDATE_EMAIL) !== true) {
                $params->Sender = $params->From;
        }
}
add_action('phpmailer_init', 'cn_wpplug_setemailsender');

