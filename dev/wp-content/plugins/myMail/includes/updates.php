<?php
/*
This runs if an update was done.
*/

global $pagenow;
$new_version = MYMAIL_VERSION;


$options = get_option('mymail_options');
$texts = $options['text'];
$is_auto_update = $pagenow == 'wp-cron.php';


if($old_version){

	switch ($old_version) {
	case '1.0':
	case '1.0.1':

		mymail_notice('[1.1.0] Capabilities are now available. Please check the <a href="options-general.php?page=newsletter-settings#capabilities">settings page</a>');
		mymail_notice('[1.1.0] Custom Fields now support dropbox and radio button. Please check the <a href="options-general.php?page=newsletter-settings#subscribers">settings page</a>');

		$texts['firstname'] = __('First Name', 'mymail');
		$texts['lastname'] = __('Last Name', 'mymail');

	case '1.1.0':

		$texts['email'] = __('Email', 'mymail');
		$texts['submitbutton'] = __('Subscribe', 'mymail');
		$texts['unsubscribebutton'] = __('Yes, unsubscribe me', 'mymail');
		$texts['unsubscribelink'] = __('unsubscribe', 'mymail');
		$texts['webversion'] = __('webversion', 'mymail');

	case '1.1.1.1':

		$texts['lists'] = __('Lists', 'mymail');

		mymail_notice('[1.2.0] Auto responders are now available! Please set the <a href="options-general.php?page=newsletter-settings#capabilities">capabilities</a> to get access');

	case '1.2.0':

		$options['send_limit'] = 10000;
		$options['send_period'] = 24;
		$options['ajax_form'] = true;

		$texts['unsubscribeerror'] = __('An error occurred! Please try again later!', 'mymail');

		mymail_notice('[1.2.1] New capabilities available! Please update them in the <a href="options-general.php?page=newsletter-settings#capabilities">settings</a>');

	case '1.2.1':
	case '1.2.1.1':
	case '1.2.1.2':
	case '1.2.1.3':
	case '1.2.1.4':
		mymail_notice('[1.2.2] New capability: "manage capabilities". Please check the <a href="options-general.php?page=newsletter-settings#capabilities">settings page</a>');
	case '1.2.2':
	case '1.2.2.1':
		$options['post_count'] = 30;
		mymail_notice('[1.3.0] Track your visitors cities! Activate the option on the <a href="options-general.php?page=newsletter-settings#general">settings page</a>');

		$texts['forward'] = __('forward to a friend', 'mymail');


	case '1.3.0':

		$options['frontpage_pagination'] = true;
		$options['basicmethod'] = 'sendmail';
		$options['deliverymethod'] = (mymail_option('smtp')) ? 'smtp' : 'simple';
		$options['bounce_active'] = (mymail_option('bounce_server') && mymail_option('bounce_user') && mymail_option('bounce_pwd'));

		$options['spf_domain'] = $options['dkim_domain'];
		$options['send_offset'] = $options['send_delay'];
		$options['send_delay'] = 0;
		$options['smtp_timeout'] = 10;


		mymail_notice('[1.3.1] DKIM is now better supported but you have to check  <a href="options-general.php?page=newsletter-settings#general">settings page</a>');

	case '1.3.1':
	case '1.3.1.1':
	case '1.3.1.2':
	case '1.3.1.3':
	case '1.3.2':
	case '1.3.2.1':
	case '1.3.2.2':
	case '1.3.2.3':
	case '1.3.2.4':

		delete_option('mymail_bulk_imports');
		$forms = $options['forms'];
		$options['forms'] = array();
		foreach($forms as $form){
			$form['prefill'] = true;
			$options['forms'][] = $form;
		}

		mymail_notice('[1.3.3] New capability: "manage subscribers". Please check the <a href="options-general.php?page=newsletter-settings#capabilities">capabilities settings page</a>');
	case '1.3.3':
	case '1.3.3.1':
	case '1.3.3.2':

		$options['subscription_resend_count'] = 2;
		$options['subscription_resend_time'] = 48;


	case '1.3.4':
		$options['sendmail_path'] = '/usr/sbin/sendmail';
	case '1.3.4.1':
	case '1.3.4.2':
	case '1.3.4.3':

		$forms = $options['forms'];
		$customfields = mymail_option('custom_field', array());

		$options['forms'] = array();
		foreach($forms as $form){
			$order = array('email');
			if(isset($options['firstname'])) $order[] = 'firstname';
			if(isset($options['lastname'])) $order[] = 'lastname';
			$required = array('email');
			if(isset($options['require_firstname'])) $required[] = 'firstname';
			if(isset($options['require_lastname'])) $required[] = 'lastname';

			foreach($customfields as $field => $data){
				if(isset($data['ask'])) $order[] = $field;
				if(isset($data['required'])) $required[] = $field;
			}
			$form['order'] = $order;
			$form['required'] = $required;
			$options['forms'][] = $form;
		}

	case '1.3.4.4':
	case '1.3.4.5':
	case '1.3.5':
	case '1.3.6':
	case '1.3.6.1':

		add_action('shutdown', array($mymail_templates, 'renew_default_template'));

	case '1.4.0':
	case '1.4.0.1':

		$lists = isset($options['newusers']) ? $options['newusers'] : array();
		$options['register_other_lists'] = $options['register_comment_form_lists'] = $options['register_signup_lists'] = $lists;
		$options['register_comment_form_status'] = array('1', '0');
		if(!empty($lists)) $options['register_other'] = true;

		$texts['newsletter_signup'] = __('Sign up to our newsletter', 'mymail');

		mymail_notice('[1.4.1] New option for WordPress Users! Please <a href="options-general.php?page=newsletter-settings#subscribers">update your settings</a>!');
		mymail_notice('[1.4.1] New text for newsletter sign up Please <a href="options-general.php?page=newsletter-settings#texts">update your settings</a>!');

	case '1.4.1':
	case '1.5.0':
	case '1.5.1':
	case '1.5.1.1':
	case '1.5.1.2':

		set_transient( 'mymail_dkim_records', array(), 1 );

		mymail_notice('[1.5.2] Since Twitter dropped support for API 1.0 you have to create a new app if you would like to use the <code>{tweet:username}</code> tag. Enter your credentials <a href="options-general.php?page=newsletter-settings#tags">here</a>!');

	case '1.5.2':

		update_option( 'envato_plugins', '' );

	case '1.5.3':
	case '1.5.3.1':
	case '1.5.3.2':

		$options['charset'] = 'UTF-8';
		$options['encoding'] = '8bit';

		$forms = $options['forms'];

		$options['forms'] = array();
		foreach($forms as $form){
			$form['asterisk'] = true;
			$options['forms'][] = $form;
		}

	case '1.5.4':
	case '1.5.4.1':
	case '1.5.5':
	case '1.5.5.1':
	case '1.5.6':
	case '1.5.7':
	case '1.5.7.1':
		$forms = $options['forms'];

		$options['forms'] = array();
		foreach($forms as $form){
			$form['submitbutton'] = mymail_text('submitbutton');
			$options['forms'][] = $form;
		}

	case '1.5.8':
		$forms = $options['forms'];

		$options['forms'] = array();
		foreach($forms as $form){
			if(is_numeric($form['submitbutton'])) $form['submitbutton'] = '';
			$options['forms'][] = $form;
		}

	case '1.5.8.1':
	case '1.6.0':
		$options['slug'] = 'newsletter';

	case '1.6.1':
		if(!isset($options['slug'])) $options['slug'] = 'newsletter';


	case '1.6.2':
	case '1.6.2.1':
	case '1.6.2.2':

		//just a random ID for better bounces
		$options['ID'] = md5(uniqid());
		$options['bounce_check'] = 5;
		$options['bounce_delay'] = 60;

	case '1.6.3':
	case '1.6.3.1':
	case '1.6.4':
	case '1.6.4.1':
	case '1.6.4.2':
		$forms = $options['forms'];

		$options['forms'] = array();
		foreach($forms as $form){
			if(!isset($form['text'])){
				$form['precheck'] = true;
				$form['double_opt_in'] = mymail_option('double_opt_in');
				$form['text'] = mymail_option('text');
				$form['subscription_resend'] = mymail_option('subscription_resend');
				$form['subscription_resend_count'] = mymail_option('subscription_resend_count');
				$form['subscription_resend_time'] = mymail_option('subscription_resend_time');
				$form['vcard'] = mymail_option('vcard');
				$form['vcard_filename'] = mymail_option('vcard_filename');
				$form['vcard_content'] = mymail_option('vcard_content');
			}
			$options['forms'][] = $form;
		}

		mymail_notice('[1.6.5] Double-Opt-In options are now form specific. Please <a href="options-general.php?page=newsletter-settings#forms">check your settings</a> if everything has been converted correctly!', '', false, 'update165');

	case '1.6.5':
	case '1.6.5.1':
	case '1.6.5.2':
	case '1.6.5.3':
	case '1.6.6':
	case '1.6.6.1':
	case '1.6.6.2':
	case '1.6.6.3':

	case '2.0 beta 1':
	case '2.0 beta 1.1':

		$campaigns = mymail('campaigns')->get_autoresponder();

		foreach($campaigns as $campaign){

			$meta = mymail('campaigns')->meta($campaign->ID);

			if($meta['active']){

				mymail('campaigns')->update_meta($campaign->ID, 'active', false);
				mymail_notice('Autoresponders have been disabled cause of some internal change. Please <a href="edit.php?post_status=autoresponder&post_type=newsletter&mymail_remove_notice=mymail_autorespondersdisabled">update them to reactivate them</a>', '', false, 'autorespondersdisabled');

			}
		}



	case '2.0 beta 2':

	case '2.0 beta 2.1':
	case '2.0 beta 3':

		$options['autoupdate'] = 'minor';

	case '2.0RC 1':
	case '2.0RC 2':


		delete_option('envato_plugins');
		delete_option('updatecenter_plugins');

	case '2.0':
	case '2.0.1':
	case '2.0.2':
	case '2.0.3':
	case '2.0.4':
	case '2.0.5':
	case '2.0.6':
	case '2.0.7':

		$options['pause_campaigns'] = true;
	case '2.0.8':
	case '2.0.9':

		$options['slugs'] = array(
			'confirm' => 'confirm',
			'subscribe' => 'subscribe',
			'unsubscribe' => 'unsubscribe',
			'profile' => 'profile'
		);

		$options['_flush_rewrite_rules'] = true;
	case '2.0.10':
	case '2.0.11':
	case '2.0.12':
		$options['_flush_rewrite_rules'] = true;
	case '2.0.13':

		$forms = $options['forms'];
		$optin = isset($forms[0]) && isset($forms[0]['double_opt_in']);
		$options['register_comment_form_confirmation'] = $optin;
		$options['register_signup_confirmation'] = $optin;

	case '2.0.14':

		global $wp_roles;

		if($wp_roles){
			$roles = $wp_roles->get_names();
			$options['register_other_roles'] = array_keys($roles);
		}

	case '2.0.15':
	case '2.0.17':
	case '2.0.18':
	case '2.0.19':
	case '2.0.20':
	case '2.0.21':
	case '2.0.22':
	case '2.0.23':
	case '2.0.24':
	case '2.0.25':
	case '2.0.26':
	case '2.0.27':

		update_option( 'updatecenter_plugins', array() );

	default:

}

update_option('mymail_version_old', $old_version);

}


//do stuff every update

$options['text'] = $texts;

//update options
update_option('mymail_options', $options);

//update caps
mymail('settings')->update_capabilities();

//update db structure
mymail()->dbstructure();

//clear cache
mymail_clear_cache('', true);
//mymail_update_option('welcome', true);


?>
