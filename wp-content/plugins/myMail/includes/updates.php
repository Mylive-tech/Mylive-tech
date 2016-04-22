<?php
/*
This runs if an update was done.
*/

global $mymail_options, $mymail_texts;

$mymail_options = get_option( 'mymail_options', array() );
$mymail_texts = get_option( 'mymail_texts', array() );

$new_version = MYMAIL_VERSION;

$texts = isset($mymail_options['text']) && !empty($mymail_options['text']) ? $mymail_options['text'] : $mymail_texts;
$show_update_notice = false;


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

		$mymail_options['send_limit'] = 10000;
		$mymail_options['send_period'] = 24;
		$mymail_options['ajax_form'] = true;

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
		$mymail_options['post_count'] = 30;
		mymail_notice('[1.3.0] Track your visitors cities! Activate the option on the <a href="options-general.php?page=newsletter-settings#general">settings page</a>');

		$texts['forward'] = __('forward to a friend', 'mymail');


	case '1.3.0':

		$mymail_options['frontpage_pagination'] = true;
		$mymail_options['basicmethod'] = 'sendmail';
		$mymail_options['deliverymethod'] = (mymail_option('smtp')) ? 'smtp' : 'simple';
		$mymail_options['bounce_active'] = (mymail_option('bounce_server') && mymail_option('bounce_user') && mymail_option('bounce_pwd'));

		$mymail_options['spf_domain'] = $mymail_options['dkim_domain'];
		$mymail_options['send_offset'] = $mymail_options['send_delay'];
		$mymail_options['send_delay'] = 0;
		$mymail_options['smtp_timeout'] = 10;


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
		$forms = $mymail_options['forms'];
		$mymail_options['forms'] = array();
		foreach($forms as $form){
			$form['prefill'] = true;
			$mymail_options['forms'][] = $form;
		}

		mymail_notice('[1.3.3] New capability: "manage subscribers". Please check the <a href="options-general.php?page=newsletter-settings#capabilities">capabilities settings page</a>');
	case '1.3.3':
	case '1.3.3.1':
	case '1.3.3.2':

		$mymail_options['subscription_resend_count'] = 2;
		$mymail_options['subscription_resend_time'] = 48;


	case '1.3.4':
		$mymail_options['sendmail_path'] = '/usr/sbin/sendmail';
	case '1.3.4.1':
	case '1.3.4.2':
	case '1.3.4.3':

		$forms = $mymail_options['forms'];
		$customfields = mymail_option('custom_field', array());

		$mymail_options['forms'] = array();
		foreach($forms as $form){
			$order = array('email');
			if(isset($mymail_options['firstname'])) $order[] = 'firstname';
			if(isset($mymail_options['lastname'])) $order[] = 'lastname';
			$required = array('email');
			if(isset($mymail_options['require_firstname'])) $required[] = 'firstname';
			if(isset($mymail_options['require_lastname'])) $required[] = 'lastname';

			foreach($customfields as $field => $data){
				if(isset($data['ask'])) $order[] = $field;
				if(isset($data['required'])) $required[] = $field;
			}
			$form['order'] = $order;
			$form['required'] = $required;
			$mymail_options['forms'][] = $form;
		}

	case '1.3.4.4':
	case '1.3.4.5':
	case '1.3.5':
	case '1.3.6':
	case '1.3.6.1':

		add_action('shutdown', array($mymail_templates, 'renew_default_template'));

	case '1.4.0':
	case '1.4.0.1':

		$lists = isset($mymail_options['newusers']) ? $mymail_options['newusers'] : array();
		$mymail_options['register_other_lists'] = $mymail_options['register_comment_form_lists'] = $mymail_options['register_signup_lists'] = $lists;
		$mymail_options['register_comment_form_status'] = array('1', '0');
		if(!empty($lists)) $mymail_options['register_other'] = true;

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

		$mymail_options['charset'] = 'UTF-8';
		$mymail_options['encoding'] = '8bit';

		$forms = $mymail_options['forms'];

		$mymail_options['forms'] = array();
		foreach($forms as $form){
			$form['asterisk'] = true;
			$mymail_options['forms'][] = $form;
		}

	case '1.5.4':
	case '1.5.4.1':
	case '1.5.5':
	case '1.5.5.1':
	case '1.5.6':
	case '1.5.7':
	case '1.5.7.1':
		$forms = $mymail_options['forms'];

		$mymail_options['forms'] = array();
		foreach($forms as $form){
			$form['submitbutton'] = mymail_text('submitbutton');
			$mymail_options['forms'][] = $form;
		}

	case '1.5.8':
		$forms = $mymail_options['forms'];

		$mymail_options['forms'] = array();
		foreach($forms as $form){
			if(is_numeric($form['submitbutton'])) $form['submitbutton'] = '';
			$mymail_options['forms'][] = $form;
		}

	case '1.5.8.1':
	case '1.6.0':
		$mymail_options['slug'] = 'newsletter';

	case '1.6.1':
		if(!isset($mymail_options['slug'])) $mymail_options['slug'] = 'newsletter';


	case '1.6.2':
	case '1.6.2.1':
	case '1.6.2.2':

		//just a random ID for better bounces
		$mymail_options['ID'] = md5(uniqid());
		$mymail_options['bounce_check'] = 5;
		$mymail_options['bounce_delay'] = 60;

	case '1.6.3':
	case '1.6.3.1':
	case '1.6.4':
	case '1.6.4.1':
	case '1.6.4.2':
		$forms = $mymail_options['forms'];

		$mymail_options['forms'] = array();
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
			$mymail_options['forms'][] = $form;
		}

		mymail_notice('[1.6.5] Double-Opt-In options are now form specific. Please <a href="edit.php?post_type=newsletter&page=mymail_forms">check your forms</a> if everything has been converted correctly!', '', false, 'update165');

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
				mymail_notice('Autoresponders have been disabled cause of some internal change. Please <a href="edit.php?post_status=autoresponder&post_type=newsletter&mymail_remove_notice=autorespondersdisabled">update them to reactivate them</a>', '', false, 'autorespondersdisabled');

			}
		}



	case '2.0 beta 2':

	case '2.0 beta 2.1':
	case '2.0 beta 3':

		$mymail_options['autoupdate'] = 'minor';

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

		$mymail_options['pause_campaigns'] = true;
	case '2.0.8':
	case '2.0.9':

		$mymail_options['slugs'] = array(
			'confirm' => 'confirm',
			'subscribe' => 'subscribe',
			'unsubscribe' => 'unsubscribe',
			'profile' => 'profile'
		);

		$mymail_options['_flush_rewrite_rules'] = true;
	case '2.0.10':
	case '2.0.11':
	case '2.0.12':
		$mymail_options['_flush_rewrite_rules'] = true;
	case '2.0.13':

		$forms = $mymail_options['forms'];
		$optin = isset($forms[0]) && isset($forms[0]['double_opt_in']);
		$mymail_options['register_comment_form_confirmation'] = $optin;
		$mymail_options['register_signup_confirmation'] = $optin;

	case '2.0.14':

		global $wp_roles;

		if($wp_roles){
			$roles = $wp_roles->get_names();
			$mymail_options['register_other_roles'] = array_keys($roles);
		}

	case '2.0.15':
	case '2.0.16':
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
	case '2.0.28':
	case '2.0.29':
	case '2.0.30':
	case '2.0.31':
	case '2.0.32':
	case '2.0.33':
	case '2.0.34':

		mymail_notice('Please clear your cache if you are using page cache on your site', '', false, 'mymailpagecache');
		$mymail_options['welcome'] = true;

	case '2.1Beta1':
	case '2.1Beta2':
	case '2.1Beta3':
	case '2.1Beta4':
	case '2.1Beta5':
	case '2.1Beta6':
	case '2.1Beta7':
	case '2.1Beta8':
	case '2.1Beta9':
	case '2.1Beta10':
	case '2.1Beta11':
	case '2.1Beta12':
	case '2.1Beta13':
	case '2.1Beta14':
	case '2.1Beta15':
	case '2.1Beta16':
	case '2.1Beta17':
	case '2.1Beta18':


	case '2.1':


	case '2.1.1':

		if($mymail_options['php_mailer']) $mymail_options['php_mailer'] = '5.2.14';
		$mymail_options['archive_slug'] = $mymail_options['slug'];
		$mymail_options['archive_types'] = array('finished','active');
		$mymail_options['module_thumbnails'] = true;
		$mymail_options['_flush_rewrite_rules'] = true;

	case '2.1.2':
	case '2.1.3':
	case '2.1.4':
	case '2.1.5':
	case '2.1.6':

		$mymail_options['got_url_rewrite'] = mymail('helper')->got_url_rewrite();

	case '2.1.7':
	case '2.1.8':

		$mymail_options['_flush_rewrite_rules'] = true;

	case '2.1.9':

		$defaults = mymail('settings')->get_default_texts();

		$texts = wp_parse_args($texts, $defaults);

		$t = mymail('translations')->get_translation_data();

		if(!empty( $t ))
			mymail_notice('<strong>'.sprintf('An important change to localizations in MyMail has been made. <a href="%s">read more</a>', 'https://mymail.newsletter-plugin.com/translations-in-mymail/').'</strong>', '', false, 'mymailtranslation');

		unset($mymail_options['texts']);
		$show_update_notice = true;

	default:

}


update_option('mymail_version_old', $old_version);

}


//do stuff every update

$mymail_texts = $texts;

//update options
update_option('mymail_options', $mymail_options);
//update texts
update_option('mymail_texts', $mymail_texts);

//update caps
mymail('settings')->update_capabilities();

//update db structure
mymail()->dbstructure();

//clear cache
mymail_clear_cache('', true);
//mymail_update_option('welcome', true);

delete_option('updatecenter_plugins');

if($old_version && $show_update_notice)
	mymail_notice(array(
		'key' => 'update_info',
		'cb' => 'mymail_update_notice'
	));

