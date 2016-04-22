<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_cron {


	public function __construct() {

		register_activation_hook  ( MYMAIL_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook  ( MYMAIL_FILE, array( &$this, 'deactivate' ) );

		add_action('plugins_loaded', array( &$this, 'init' ), 1 );

	}

	public function init() {

		add_filter('cron_schedules', array( &$this, 'filter_cron_schedules'));
		add_action('mymail_cron', array( &$this, 'hourly_cronjob'));
		add_action('mymail_cron', array( &$this, 'check'));
		add_action('mymail_cron_worker', array( &$this, 'handler'), -1);
		add_action('mymail_cron_worker', array( &$this, 'check'), 99);

		add_action('mymail_campaign_pause', array( &$this, 'update'));
		add_action('mymail_campaign_start', array( &$this, 'update'));
		add_action('mymail_campaign_duplicate', array( &$this, 'update'));

		if (!wp_next_scheduled('mymail_cron')) $this->update(true);

	}


	//Checks for new newsletter in the queue to start new cronjob
	public function hourly_cronjob() {

		//check for bounced emails
		do_action('mymail_check_bounces');

		//send confirmations again
		do_action('mymail_resend_confirmations');

		//checks if homepage is set
		do_action('mymail_check_homepage');

		if(mymail('queue')->size(time()+3600)){
			$this->update();
		}else{
			$this->remove_crons();
		}

		if(version_compare(PHP_VERSION, '5.3') < 0){
			mymail_notice(sprintf('MyMail will soon required PHP version 5.3 and above. Your current version is %s so please update or ask your provider to help you with updating!', '<code>'.PHP_VERSION.'</code>'), 'error', false, 'minphpversion');
		}else{
			mymail_remove_notice('minphpversion');
		}


	}

	public function handler() {

		if (defined('MYMAIL_DOING_CRON') || defined('DOING_AJAX') || defined('DOING_AUTOSAVE') || defined('WP_INSTALLING') || defined('MYMAIL_DO_UPDATE')) return false;

		define('MYMAIL_DOING_CRON', microtime(true));

		register_shutdown_function( array( &$this, 'shutdown_function') );

	}


	public function shutdown_function() {

		if(!defined('MYMAIL_DOING_CRON')) return;

		$error = error_get_last();

		if( !is_null($error) && $error["type"] == 1) {

			mymail_notice('<strong>'.sprintf(__('It seems your last cronjob hasn\'t been finished! Increase the %1$s, add %2$s to your wp-config.php or reduce the %3$s in the settings' , 'mymail'), "'max_execution_time'", '<code>define("WP_MEMORY_LIMIT", "256M");</code>', '<a href="options-general.php?page=newsletter-settings&settings-updated=true#delivery">'.__('Number of mails sent', 'mymail').'</a>').'</strong>', 'error', false, 'cron_unfinished');


		}else{

			mymail_remove_notice('cron_unfinished');

		}

	}


	public function update($hourly_only = false) {

		if (!wp_next_scheduled('mymail_cron')) {

			//main schedule always 5 minutes before full hour
			wp_schedule_event(strtotime('midnight')-300, 'hourly', 'mymail_cron');
			//stop here cause mymail_cron triggers the worker if required
			return true;
		} elseif ($hourly_only) {
			return false;
		}

		//remove the WordPress cron if "normal" cron is used
		if (mymail_option('cron_service') != 'wp_cron') {
			wp_clear_scheduled_hook('mymail_cron_worker');
			return false;
		}

		//add worker only once
		if (!wp_next_scheduled('mymail_cron_worker')) {
			wp_schedule_event(floor(time()/300)*300, 'mymail_cron_interval', 'mymail_cron_worker');
			return true;
		}

		return false;

	}


	// add custom time to cron
	public function filter_cron_schedules($cron_schedules) {

		$cron_schedules['mymail_cron_interval'] = array(
			'interval' => mymail_option('interval', 5) * 60, // seconds
			'display' => 'myMail Cronjob Interval'
		);

		return $cron_schedules;
	}



	public function remove_crons($general = false) {
		wp_clear_scheduled_hook('mymail_cron_worker');
		if ($general)
			wp_clear_scheduled_hook('mymail_cron');
	}

	public function check(){

		global $wpdb;

		$now = time();
		$cron_service = mymail_option('cron_service');

		if($cron_service == 'wp_cron'){

			if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON){
				//mymail_notice(sprintf(__( 'The WordPress Cron is disabled! Please remove the %s constant from your wp-config.php file or switch to a real cron job!', 'mymail'), '<code>DISABLE_WP_CRON</code>'), 'error', false, 'check_cron');
			}
			$this->update();

		}else{

			if(!mymail('queue')->size()) :

				mymail_remove_notice('check_cron');
			else:

				$interval = mymail_option('interval')*60;
				$last_hit = get_option('mymail_cron_lasthit', array(
					'ip' => mymail_get_ip(),
					'timestamp' => $now,
					'oldtimestamp' => $now-$interval,
				));

				//get real delay...
				$real_delay = max($interval, $last_hit['timestamp']-$last_hit['oldtimestamp']);
				$current_delay = $now-$last_hit['timestamp'];

				//mymail_notice($real_delay.' '.$current_delay, 'updated', true, 'check_delay');

				//..and compare it with the interval (3 times) - also something in the queue
				if(($current_delay > $real_delay*3 || !$real_delay && !$current_delay )) :

					mymail_notice(sprintf(__( 'Are your campaigns not sending? You may have to check your %1$s', 'mymail'), '<a href="options-general.php?page=newsletter-settings&mymail_remove_notice=mymail_check_cron#cron"><strong>'.__('cron settings', 'mymail').'</strong></a>'), 'error', false, 'check_cron');

					$this->update();
				else:

					mymail_remove_notice('check_cron');

				endif;

			endif;

		}

	}


	/*----------------------------------------------------------------------*/
	/* Plugin Activation / Deactivation
	/*----------------------------------------------------------------------*/



	public function activate() {

		global $wpdb;

		if (is_network_admin() && is_multisite()) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		}else{

			$blogids = array(false);

		}

		foreach ($blogids as $blog_id) {

			if($blog_id) switch_to_blog( $blog_id );

			$this->update();
		}

		if($blog_id) switch_to_blog($old_blog);

	}


	public function deactivate() {

		global $wpdb;

		if (is_network_admin() && is_multisite()) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		}else{

			$blogids = array(false);

		}

		foreach ($blogids as $blog_id) {

			if($blog_id) switch_to_blog( $blog_id );

			$this->remove_crons(true);
		}

		if($blog_id) switch_to_blog($old_blog);

	}


}
?>
