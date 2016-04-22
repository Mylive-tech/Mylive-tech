<?php
/*
Plugin Name: MyMail - Email Newsletter Plugin for WordPress
Plugin URI: https://mymail.newsletter-plugin.com
Plugin Slug: myMail/myMail.php
Description: advanced Newsletter Plugin for WordPress. Create, Send and Track your Newsletter Campaigns
Version: 2.1.11
Author: revaxarts.com
Author URI: https://revaxarts.com
Text Domain: mymail
*/

define('MYMAIL_VERSION', '2.1.11');
define('MYMAIL_DBVERSION', 20160125);
define('MYMAIL_DIR', plugin_dir_path( __FILE__ ));
define('MYMAIL_URI', plugin_dir_url( __FILE__ ));
define('MYMAIL_FILE', __FILE__);
define('MYMAIL_SLUG', 'myMail/myMail.php');

$upload_folder = wp_upload_dir();

define('MYMAIL_UPLOAD_DIR', trailingslashit( $upload_folder['basedir'] ) . 'myMail');
define('MYMAIL_UPLOAD_URI', trailingslashit( $upload_folder['baseurl'] ) . 'myMail');

if(!file_exists(MYMAIL_DIR . 'includes/functions.php')) return;

require_once MYMAIL_DIR . 'includes/functions.php';
require_once MYMAIL_DIR . 'classes/mymail.class.php';

global $mymail_options, $mymail, $mymail_tags, $mymail_mystyles;

$mymail_options = get_option( 'mymail_options', array() );

$mymail = new mymail();

if(!$mymail->wp_mail && mymail_option('system_mail') == 1){

	function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
		return mymail()->wp_mail($to, $subject, $message, $headers, $attachments);
	}

}

if(!class_exists('UpdateCenterPlugin'))
	require_once MYMAIL_DIR . 'classes/UpdateCenterPlugin.php';

UpdateCenterPlugin::add(array(
	'licensecode' => mymail_option('purchasecode'),
	'remote_url' => 'https://update.revaxarts.com/',
	'plugin' => MYMAIL_SLUG,
	'autoupdate' => mymail_option('autoupdate', true),
));
