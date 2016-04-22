<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

global $wpdb, $wp_roles;

if (is_network_admin() && is_multisite()) {

	$old_blog = $wpdb->blogid;
	$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

}else{

	$blogids = array(false);

}

foreach ($blogids as $blog_id) {

	if($blog_id) switch_to_blog( $blog_id );

	$mymail_options = get_option( 'mymail_options' );
	//stop if data should be kept
	if(!isset($mymail_options['remove_data'])) continue;

	$path = WP_PLUGIN_DIR . '/' . dirname( WP_UNINSTALL_PLUGIN );

	require $path . '/includes/capability.php';

	$roles = array_keys($wp_roles->roles);
	$mymail_capabilities = array_keys($mymail_capabilities);

	foreach($roles as $role){
		$capabilities = $wp_roles->roles[$role]['capabilities'];
		foreach($capabilities as $capability => $has){
			if(in_array($capability, $mymail_capabilities))
				$wp_roles->remove_cap( $role, $capability);
		}

	}

	$campaigns = get_posts( array(
		'posts_per_page' => -1,
		'post_type' => 'newsletter',
		'post_status' => 'any'
	));

	if (is_array($campaigns)) {
		foreach ($campaigns as $campaign) {
			wp_delete_post( $campaign->ID, true);
		}
	}

	//remove all options
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_mymail_%'");
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout_mymail_%'");
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient__mymail_%'");
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout__mymail_%'");
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout__mymail_%'");
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE 'mymail_%'");
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` = 'mymail'");

	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_actions");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_links");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_lists");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_lists_subscribers");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_queue");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_subscribers");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_subscriber_fields");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_subscriber_meta");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_forms");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_forms_lists");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mymail_form_fields");

	//optimize DB
	$wpdb->query("OPTIMIZE TABLE `$wpdb->options`");

	//remove folder in the upload directory
	global $wp_filesystem;
	$upload_folder = wp_upload_dir();

	if($wp_filesystem)
		$wp_filesystem->delete(trailingslashit( $upload_folder['basedir'] ) . 'myMail', true);

}

if($blog_id) switch_to_blog($old_blog);
