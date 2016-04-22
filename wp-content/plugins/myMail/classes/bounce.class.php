<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_bounce {

	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init' ), 1 );

	}

	public function init() {

		add_action('mymail_cron_worker', array( &$this, 'check'), 1);
		add_action('mymail_check_bounces', array( &$this, 'check'), 99);

	}

	public function bounce_lock($bool = true) {

		set_transient( 'mymail_check_bounces_lock', $bool, mymail_option('bounce_check', 5)*60 );

	}

	public function is_bounce_lock() {

		return get_transient( 'mymail_check_bounces_lock' );

	}

	public function check($force = false) {

		if(!mymail_option('bounce_active')) return false;

		if ( $this->is_bounce_lock() && !$force ) return false;

		$server = mymail_option('bounce_server');
		$user = mymail_option('bounce_user');
		$pwd = mymail_option('bounce_pwd');

		if (!$server || !$user || !$pwd)
			return false;

		//check bounces only every defined minutes
		$this->bounce_lock();

		if(mymail_option('bounce_ssl')) $server = 'ssl://'.$server;


		require_once ABSPATH . WPINC . '/class-pop3.php';
		$pop3 = new POP3();

		if (!$pop3->connect($server, mymail_option('bounce_port', 110)) || !$pop3->user($user))
			return false;

		$count = $pop3->pass($pwd);

		if (0 === $count || false === $count) {
			$pop3->quit();
			return false;
		}

		require_once MYMAIL_DIR . 'classes/libs/bounce/bounce_driver.class.php';

		$bounce_delete = mymail_option('bounce_delete');
		$MID = mymail_option('ID');

		//only max 1000 at once
		$count = min($count, 1000);

		for ($i = 1; $i <= $count; $i++) {
			$message = $pop3->get($i);

			if(!$message){
				if ($bounce_delete) $pop3->delete($i);
				continue;
			}

			$message = implode($message);

			preg_match('#X-MyMail: ([a-f0-9]{32})#i', $message, $hash);
			preg_match('#X-MyMail-Campaign: (\d+)#i', $message, $camp);
			preg_match('#X-MyMail-ID: ([a-f0-9]{32})#i', $message, $ID);

			if(!empty($hash) && !empty($ID)){

				if($ID[1] == $MID){

					$bouncehandler = new Bouncehandler();
					$bounceresult = $bouncehandler->parse_email($message);
					$bounceresult = (object) $bounceresult[0];

					$subscriber = mymail('subscribers')->get_by_hash($hash[1], false);
					$campaign = !empty($camp) ? mymail('campaigns')->get(intval($camp[1])) : NULL;

					if($subscriber){

						$campaign_id = $campaign ? $campaign->ID : 0;
						switch($bounceresult->action){
							case 'success':
								break;

							case 'failed':
								//hardbounce
								mymail('subscribers')->bounce($subscriber->ID, $campaign_id, true, $bounceresult->status);
							break;

							case 'transient':
							default:
								//softbounce
								mymail('subscribers')->bounce($subscriber->ID, $campaign_id, false, $bounceresult->status);

						}
					}

					$pop3->delete($i);

				}else{

					$pop3->reset();

				}

			}else{
				if ($bounce_delete) $pop3->delete($i);
			}

		}

		$pop3->quit();

	}

}
