<?php
/*
Plugin Name: MyMail - MU Cron
Plugin URI: https://mymail.newsletter-plugin.com
Plugin Slug: myMail/myMail.php
Description: copy this file in the mu-plugins folder of your WordPress installation if you have problems with the real cron service. Check the new cron URL from the settings page, tab "Cron"
Version: 1.0
Author: revaxarts.com
Author URI: https://revaxarts.com
*/


/*
 * copy this file in the mu-plugins folder of your WordPress installation if you have problems with the real cron service
 * check the new cron URL from the settings page, tab "Cron"
 *
 * Read more: http://codex.wordpress.org/Must_Use_Plugins
 *
 * Since 2.0.16
 */

define('MYMAIL_MU_CRON', true);

add_action( 'wp_ajax_mymail_cron_worker', 'mymail_cron_worker' );
add_action( 'wp_ajax_nopriv_mymail_cron_worker', 'mymail_cron_worker' );

function mymail_cron_worker() {

	if (!defined('MYMAIL_VERSION')) wp_die('activate plugin!');

	$secret = isset($_GET['secret']) ? $_GET['secret'] : false;

	define('MYMAIL_CRON_SECRET', $secret);

	include MYMAIL_DIR . 'cron.php';
	exit();

}
