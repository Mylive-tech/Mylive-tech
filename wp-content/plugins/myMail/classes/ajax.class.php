<?php if (!defined('ABSPATH')) die('not allowed');

class mymail_ajax {

	private $methods = array(

			'remove_notice',

			//edit screen
			'get_template',
			'get_plaintext',
			'create_new_template',
			'set_preview',
			'get_preview',
			'toggle_codeview',
			'send_test',
			'check_spam_score',
			'get_totals',
			'save_color_schema',
			'delete_color_schema',
			'delete_color_schema_all',
			'get_recipients',
			'get_recipients_page',
			'get_recipient_detail',
			'get_clicks',
			'get_errors',
			'get_environment',
			'get_geolocation',
			'get_post_term_dropdown',
			'check_for_posts',
			'create_image',
			'image_placeholder',
			'get_post_list',
			'get_post',

			'get_file_list',
			'get_template_html',
			'set_template_html',
			'remove_template',

			'notice_dismiss',
			'notice_dismiss_all',

			//settings
			'load_geo_data',
			'get_fallback_images',
			'bounce_test',
			'bounce_test_check',
			'get_system_info',
			'get_gravatar',
			'check_email',

			'sync_all_subscriber',
			'sync_all_wp_user',

			'create_list',
			'get_create_list_count',

			'editor_image_upload_handler',
			'template_upload_handler',

			//dashboard
			'get_dashboard_camp',
			'get_dashboard_chart',
		);

		private $methods_no_priv = array(
			'forward_message',
			'subscribe',
			'update',
			'unsubscribe',
			'form_submit',
			'profile_submit',
			'form_unsubscribe',
			'form_css',
		);


	public function __construct() {

		if(defined('DOING_AJAX') && DOING_AJAX)
			add_action('plugins_loaded', array( &$this, 'init'));


	}

	public function add_ajax_nonce() {
		wp_nonce_field('mymail_nonce', 'mymail_nonce', false);
	}

	public function init() {

		foreach($this->methods as $method){

			add_action('wp_ajax_mymail_'.$method, array( &$this, 'call_method'));

		}

		foreach($this->methods_no_priv as $method){

			add_action('wp_ajax_mymail_'.$method, array( &$this, 'call_method'));
			add_action('wp_ajax_nopriv_mymail_'.$method, array( &$this, 'call_method'));

		}

	}


	public function call_method() {

		$method_name = str_replace(array('wp_ajax_mymail_', 'wp_ajax_nopriv_mymail_'), '', current_filter());
		$args = func_get_args();

		if(method_exists( $this, $method_name)){
			call_user_func_array(array( $this,  $method_name), $args);
		}else{
			die("Method $method does not exist!");
		}

	}


	private function json_return($return) {

		@header( 'Content-type: application/json' );
		echo json_encode($return);
		exit;

	}


	public function form_css() {
		mymail()->form_css();
	}

	private function submit() {

		global $wp;

		$wp->query_vars['_mymail'] = 'subscribe';

		mymail('form')->submit();

	}

	private function update() {

		mymail('form')->update();

	}


	private function unsubscribe() {

		mymail('form')->unsubscribe();

	}

	private function form_submit() {

		$this->submit();
	}

	private function profile_submit() {

		$this->update();
	}


	private function form_unsubscribe() {

		$this->unsubscribe();

	}


	private function get_plaintext() {

		$this->ajax_nonce('not allowed');

		$html = isset($_POST['html']) ? $_POST['html'] : '';

		$html = mymail()->sanitize_content($html);

		$html = mymail()->plain_text($html);

		echo $html;

		exit;

	}


	private function get_template() {

		$this->ajax_nonce('<script type="text/javascript">if(parent.window)parent.location.reload();</script>');

		@error_reporting(0);

		$id = intval($_GET['id']);
		$template = $_GET['template'];
		$file = isset($_GET['file']) ? $_GET['file'] : 'index.html';
		$editorstyle = ($_GET['editorstyle'] == '1');
		$meta = mymail('campaigns')->meta($id);
		$head = isset($meta['head']) ? $meta['head'] : NULL;

		mymail('campaigns')->set_template($template, $file, true);

		if(!isset($meta['file'])) $meta['file'] = 'index.html';

		//template has been changed
		if (!isset($meta['template']) || $template != $meta['template'] || $file != $meta['file']) {
			$html = mymail('campaigns')->get_template_by_slug($template, $file, false, $editorstyle);
		} else {
			$html = mymail('campaigns')->get_template_by_id($id, $file, false, $editorstyle);
		}

		if (!$editorstyle) {
			$revision = isset($_REQUEST['revision']) ? (int) $_REQUEST['revision'] : false;
			$campaign = get_post($id);
			$subject = isset($_REQUEST['subject']) ? esc_attr($_REQUEST['subject']) : isset($meta['subject']) ? esc_attr($meta['subject']) : '';

			$current_user = wp_get_current_user();

			if ($revision) {
				$revision = get_post($revision);
				$html = mymail()->sanitize_content($revision->post_content, NULL, $head);
			}

			$placeholder = mymail('placeholder', $html);

			$placeholder->do_conditions(false);

			$placeholder->set_campaign($campaign->ID);

			$unsubscribelink = mymail()->get_unsubscribe_link($campaign->ID);
			$forwardlink = mymail()->get_forward_link($campaign->ID, $current_user->user_email);
			$profilelink = mymail()->get_profile_link($campaign->ID, '');

			$placeholder->add(array(
				'issue' => 0,
				'subject' => $subject,
				'webversion' => '<a href="{webversionlink}">' . mymail_text('webversion') . '</a>',
				'webversionlink' => get_permalink($campaign->ID),
				'unsub' => '<a href="{unsublink}">' . mymail_text('unsubscribelink') . '</a>',
				'unsublink' => $unsubscribelink,
				'forward' => '<a href="{forwardlink}">' . mymail_text('forward') . '</a>',
				'forwardlink' => $forwardlink,
				'profile' => '<a href="{profilelink}">' . mymail_text('profile') . '</a>',
				'profilelink' => $profilelink,
				'email' => '<a href="">{emailaddress}</a>',
				'emailaddress' => $current_user->user_email,
			));

			if ( 0 != $current_user->ID ) {
				$firstname = ($current_user->user_firstname) ? $current_user->user_firstname : $current_user->display_name;

				$placeholder->add(array(
					'firstname' => $firstname,
					'lastname' => $current_user->user_lastname,
					'fullname' => trim($firstname.' '.$current_user->user_lastname),
				));
			}

			$placeholder->share_service(get_permalink($campaign->ID), $campaign->post_title);

			$html = $placeholder->get_content(false);
			$html = str_replace('</head>', '<link rel="stylesheet" id="template-style" href="'.MYMAIL_URI.'assets/css/template-style.css?ver='.MYMAIL_VERSION.'" type="text/css" media="all"></head>', $html);
		}

		$replace = apply_filters('mymail_get_template_replace', array(
			'http://dummy.newsletter-plugin.com' => 'https://dummy.newsletter-plugin.com',
		));

		$html = strtr($html, $replace);
		echo $html;

		exit;

	}


	private function create_new_template() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$this->ajax_filesystem( );

		$content = mymail()->sanitize_content($_POST['content'], NULL, (isset($_POST['head']) ? $_POST['head'] : NULL));

		$name = esc_attr($_POST['name']);
		$template = esc_attr($_POST['template']);
		$modules = 	!!($_POST['modules'] === 'true');
		$overwrite = !!($_POST['overwrite'] === 'true');

		$t = mymail('template', $template);
		$filename = $t->create_new($name, $content, $modules, $overwrite);

		if($return['success'] = $filename !== false) $return['url'] = add_query_arg(array( 'template' => $template, 'file' => $filename, 'message' => 3 ), wp_get_referer());
		if(!$return['success']) $return['msg'] = __('Unable to save template!', 'mymail');

		$this->json_return($return);


	}


	private function toggle_codeview() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$head = isset($_POST['head']) ? ($_POST['head']) : NULL;

		$return['content'] = mymail()->sanitize_content($_POST['content'], NULL, $head);

		$this->json_return($return);

	}


	private function set_preview() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$content = isset($_POST['content']) ? ($_POST['content']) : '';
		$ID = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$subject = isset($_POST['subject']) ? esc_attr($_POST['subject']) : '';
		$issue = isset($_POST['issue']) ? intval($_POST['issue']) : 1;
		$head = isset($_POST['head']) ? ($_POST['head']) : NULL;
		$userid = isset($_POST['userid']) ? intval($_POST['userid']) : NULL;

		$html = mymail()->sanitize_content($content, true, $head);

		$placeholder = mymail('placeholder', $html);

		$placeholder->set_campaign($ID);

		$unsubscribelink = mymail()->get_unsubscribe_link($ID);
		$forwardlink = mymail()->get_forward_link($ID, '');
		$profilelink = mymail()->get_profile_link($ID, '');

		$current_user = wp_get_current_user();

		if( !$userid ) {
			if($subscriber = mymail('subscribers')->get_by_wpid($current_user->ID, true)){
				$userid = $subscriber->ID;
			}
		}

		if ( $userid ) {

			if($subscriber = mymail('subscribers')->get($userid, true)){

				$unsubscribelink = mymail()->get_unsubscribe_link($ID);
				$forwardlink = mymail()->get_forward_link($ID, $subscriber->email);
				$profilelink = mymail()->get_profile_link($ID, $subscriber->hash);

				$userdata = mymail('subscribers')->get_custom_field($subscriber->ID);

				$placeholder->set_subscriber($subscriber->ID);
				$placeholder->add($userdata);

				$names = array(
					'firstname' => $subscriber->firstname,
					'lastname' => $subscriber->lastname,
					'fullname' => $subscriber->fullname,
				);

			}else{

				$unsubscribelink = mymail()->get_unsubscribe_link($ID);
				$forwardlink = mymail()->get_forward_link($ID, $to[0]);
				$profilelink = mymail()->get_profile_link($ID, '');

				$firstname = ($current_user->user_firstname) ? $current_user->user_firstname : $current_user->display_name;
				$names = array(
					'firstname' => $firstname,
					'lastname' => $current_user->user_lastname,
					'fullname' => trim($firstname.' '.$current_user->user_lastname),
				);
			}

			$placeholder->add($names);

		}

		$placeholder->add(array(
			'issue' => $issue,
			'subject' => $subject,
			'webversion' => '<a href="{webversionlink}">' . mymail_text('webversion') . '</a>',
			'webversionlink' => get_permalink($ID),
			'unsub' => '<a href="{unsublink}">' . mymail_text('unsubscribelink') . '</a>',
			'unsublink' => $unsubscribelink,
			'forward' => '<a href="{forwardlink}">' . mymail_text('forward') . '</a>',
			'forwardlink' => $forwardlink,
			'profile' => '<a href="{profilelink}">' . mymail_text('profile') . '</a>',
			'profilelink' => $profilelink,
			'email' => '<a href="">{emailaddress}</a>',
			'emailaddress' => $current_user->user_email,
		));

		$placeholder->share_service('{webversionlink}', esc_attr($_POST['subject']));
		$content = $placeholder->get_content();

		$content = str_replace('@media only screen and (max-device-width:', '@media only screen and (max-width:', $content);

		$hash = md5($content);

		//cache preview for 60 seconds
		set_transient( 'mymail_p_'.$hash, $content, 60 );

		$placeholder->set_content($subject);
		$return['subject'] = $placeholder->get_content();
		$return['hash'] = $hash;
		$return['nonce'] = wp_create_nonce( 'mymail_nonce' );
		$return['success'] = true;

		$this->json_return($return);

	}


	private function get_preview() {

		$this->ajax_nonce('not allowed');

		$hash = $_GET['hash'];

		$content = get_transient( 'mymail_p_'.$hash );

		if(empty($content)) $content = 'error';

		echo $content;
		exit;
	}


	private function send_test() {

		$return['success'] = true;

		$this->ajax_nonce(json_encode($return));

		if (isset($_POST['test'])) {

			$basic = !!($_POST['basic'] === 'true');

			$to = esc_attr($_POST['to']);

			$n = mymail('notification');
			$n->debug();
			$n->to($to);
			$n->template('test');
			$n->requeue(false);

			$return['success'] = $n->add();

			$mail = $n->mail;

			global $mymail_error_log;

			$return['log'] = $mymail_error_log;

		} else {

			$receivers = explode(',',esc_attr($_POST['to']));

			$subject = stripslashes($_POST['subject']);
			$from = esc_attr($_POST['from']);
			$from_name = stripslashes(esc_attr($_POST['from_name']));
			$reply_to = esc_attr($_POST['reply_to']);
			$embed_images = !!($_POST['embed_images'] === 'true');
			$head = isset($_POST['head']) ? ($_POST['head']) : NULL;
			$content = $_POST['content'];
			$preheader = esc_attr($_POST['preheader']);
			$bouncemail = mymail_option('bounce');

			$autoplain = !($_POST['autoplain'] === 'true');
			$plaintext = $_POST['plaintext'];

			if(function_exists( 'wp_encode_emoji' )){
				$subject = wp_encode_emoji($subject);
				$preheader = wp_encode_emoji($preheader);
				$from_name = wp_encode_emoji($from_name);
			}

			$ID = intval($_POST['ID']);
			$issue = isset($_POST['issue']) ? intval($_POST['issue']) : 1;

			$unsubscribe_homepage = (get_page( mymail_option('homepage') )) ? get_permalink(mymail_option('homepage')) : get_bloginfo('url');
			$unsubscribe_homepage = apply_filters('mymail_unsubscribe_link', $unsubscribe_homepage);

			$campaign_permalink = get_permalink($ID);

			$replace_links = true;
			$spam_test = isset($_POST['spamtest']);


			foreach ($receivers as $to) {

				$current_user = NULL;
				$names = NULL;

				$mail = mymail('mail');

				$mail->to = $to;
				$mail->subject = $subject;
				$mail->from = $from;
				$mail->from_name = $from_name;
				$mail->reply_to = $reply_to;
				$mail->bouncemail = $bouncemail;
				$mail->embed_images = $embed_images;
				$mail->hash = str_repeat('0', 32);

				$the_content = mymail()->sanitize_content($content, NULL, $head);

				$placeholder = mymail('placeholder', $the_content);

				$mail->set_campaign($ID);
				$placeholder->set_campaign($ID);


				$unsubscribelink = mymail()->get_unsubscribe_link($ID);
				$forwardlink = mymail()->get_forward_link($ID, $to);

				//check for subscriber by mail
				$subscriber = mymail('subscribers')->get_by_mail($to, true);
				if(!$subscriber){

					$current_user = wp_get_current_user();

					//check subscriber by wp user
					$subscriber = mymail('subscribers')->get_by_wpid($current_user->ID, true);

				}

				if($subscriber){

					$profilelink = mymail()->get_profile_link($ID, $subscriber->hash);

					$userdata = mymail('subscribers')->get_custom_field($subscriber->ID);

					$placeholder->set_subscriber($subscriber->ID);
					$placeholder->add($userdata);

					$names = array(
						'firstname' => $subscriber->firstname,
						'lastname' => $subscriber->lastname,
						'fullname' => $subscriber->fullname,
					);

					$mail->set_subscriber($subscriber->ID);
					$placeholder->set_subscriber($subscriber->ID);

				}else if($current_user){

					$profilelink = mymail()->get_profile_link($ID, '');

					$firstname = ($current_user->user_firstname) ? $current_user->user_firstname : $current_user->display_name;
					$names = array(
						'firstname' => $firstname,
						'lastname' => $current_user->user_lastname,
						'fullname' => trim($firstname.' '.$current_user->user_lastname),
					);
				}else{
					//no subscriber found for data
				}

				if($names) $placeholder->add($names);

				$placeholder->add(array(
					'issue' => $issue,
					'subject' => $subject,
					'preheader' => $preheader,
					'webversion' => '<a href="{webversionlink}">' . mymail_text('webversion') . '</a>',
					'webversionlink' => $campaign_permalink,
					'unsub' => '<a href="{unsublink}">' . mymail_text('unsubscribelink') . '</a>',
					'unsublink' => $unsubscribelink,
					'forward' => '<a href="{forwardlink}">' . mymail_text('forward') . '</a>',
					'forwardlink' => $forwardlink,
					'profile' => '<a href="{profilelink}">' . mymail_text('profile') . '</a>',
					'profilelink' => $profilelink,
					'email' => '<a href="">{emailaddress}</a>',
					'emailaddress' => $to[0],
				));

				$placeholder->share_service($campaign_permalink, $subject);

				$the_content = $placeholder->get_content();

					//replace links with fake hash to prevent tracking
				if($replace_links)
					$the_content = mymail()->replace_links($the_content, $mail->hash, $ID);

				$mail->content = $the_content;

				if($autoplain){
					$placeholder->set_content(esc_textarea($plaintext));
					$mail->plaintext = mymail()->plain_text($placeholder->get_content(), true);
				}

				$placeholder->set_content($mail->subject);
				$mail->subject = $placeholder->get_content();

				$mail->add_tracking_image = false;
				$mail->prepare_content();

				if ($spam_test){

					if ( false === ( $count = get_transient( '_mymail_spam_score_count' ) ) ) {

						$count = 0;
						set_transient( '_mymail_spam_score_count', $count, 3600 );
					}

					if($count < 10){

						$id = uniqid();
						$mail->to = apply_filters('mymail_spam_score_mail', 'mymail-'.$id.'@check.newsletter-plugin.com');
						$return['success'] = $return['success'] && $mail->send();
						$return['id'] = $id;
						update_option('_transient__mymail_spam_score_count', ++$count);

					}else{

						$return['msg'] = __('You can only perform 10 test within an hour. Please try again later!', 'mymail');

					}

				}else{

					$return['success'] = $return['success'] && $mail->send();
				}

				$mail->close();
			}

		}

		if(!isset($return['msg']))
			$return['msg'] = ($return['success'])
							? __('Message sent. Check your inbox!', 'mymail')
							: __('Couldn\'t send message. Check your settings!', 'mymail').'<strong>'.$mail->get_errors('br').'</strong>';

		if(isset($return['log']))
			$return['msg'] .= '<br>'.__('Check your console for more info.', 'mymail');


		$this->json_return($return);


	}

	private function check_spam_score() {

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$id = isset($_POST['ID']) ? $_POST['ID'] : false;

		if($id){

			if(false === ($return = apply_filters( 'mymail_check_spam_score', false, $id ))){

				$response = wp_remote_get('http://check.newsletter-plugin.com/'.$id, array(
					'sslverify' => false,
					'timeout' => 10
				));

				if( is_wp_error( $response ) ) {
					$return['msg'] = $response->get_error_message();
				}else if( $response['response']['code'] == 200){
					$body = json_decode(wp_remote_retrieve_body( $response ));
					//$return['report'] = $body->report;
					$return['score'] = $body->score;
				}else if( $response['response']['code'] == 503){
					$return['abort'] = true;
					$body = json_decode(wp_remote_retrieve_body( $response ));
					$return['msg'] = $body->msg;
				}else{
					$return['abort'] = false;
					$body = json_decode(wp_remote_retrieve_body( $response ));
					$return['msg'] = $body->msg;
				}

			}


		}

		$this->json_return($return);

	}

	private function get_totals() {

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$lists = ($_POST['ignore_lists'] == 'true') ? false : (isset($_POST['lists']) ? $_POST['lists'] : array());
		$conditions = isset($_POST['conditions']) ? $_POST['conditions'] : false;
		$operator = isset($_POST['operator']) ? $_POST['operator'] : false;

		$return['success'] = true;
		$return['total'] = mymail('campaigns')->get_totals_by_lists($lists, array( 'operator' => $operator, 'conditions' => $conditions ));;
		$return['totalformated'] = number_format_i18n( $return['total'] );

		$this->json_return($return);

	}


	private function save_color_schema() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$colors = get_option('mymail_colors');
		$hash = md5(implode('', $_POST['colors']));

		if (!isset($colors[$_POST['template']])) $colors[$_POST['template']] = array();
		$colors[$_POST['template']][$hash] = $_POST['colors'];

		$return['html'] = '<ul class="colorschema custom" data-hash="' . $hash . '">';
		foreach ($_POST['colors'] as $color) {
			$return['html'] .= '<li class="colorschema-field" data-hex="' . $color . '" style="background-color:' . $color . '"></li>';
		}
		$return['html'] .= '<li class="colorschema-delete-field"><a class="colorschema-delete">&#10005;</a></li></ul>';

		$return['success'] = update_option('mymail_colors', $colors);

		$this->json_return($return);

	}

	private function delete_color_schema() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$colors = get_option('mymail_colors');

		$template = esc_attr($_POST['template']);

		if (!isset($colors[$template])) $colors[$template] = array();

		if(isset($colors[$template][$_POST['hash']])) unset($colors[$template][$_POST['hash']]);

		if(empty($colors[$template])) unset($colors[$template]);

		$return['success'] = update_option('mymail_colors', $colors);

		$this->json_return($return);


	}

	private function delete_color_schema_all() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$colors = get_option('mymail_colors');

		$template = esc_attr($_POST['template']);

		if(isset($colors[$template])) unset($colors[$template]);

		$return['success'] = update_option('mymail_colors', $colors);

		$this->json_return($return);


	}


	private function get_clicks() {

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$campaign_ID = (int) $_POST['id'];

		$clicked_links = mymail('campaigns')->get_clicked_links($campaign_ID);
		$clicks_total = mymail('campaigns')->get_clicks($campaign_ID, true);

		$return['html'] = '<table class="wp-list-table widefat"><tbody>';

		$i = 1;
		foreach($clicked_links as $link => $indexes){
			foreach($indexes as $index => $counts){
				$return['html'] .= '<tr '.(!($i%2) ? ' class="alternate"' : '').'><td>'.sprintf( _n( '%s click', '%s clicks', $counts['total'], 'mymail'), $counts['total']).' '.($counts['total'] != $counts['clicks'] ? '<span class="count">('.sprintf( __( '%s unique', 'mymail'), $counts['clicks']).')</span>' : '').'</td><td>'. round(($counts['total']/$clicks_total*100),2).'%</td><td><a href="'.$link.'" class="external clicked-link">'.$link.'</a></td></tr>';
				$i++;
			}
		}

		$return['html'] .= '</tbody>';
		$return['html'] .= '</table>';

		$this->json_return($return);


	}


	private function get_errors() {

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$timeformat = get_option('date_format').' '.get_option('time_format');
		$timeoffset = mymail('helper')->gmt_offset(true);

		$campaign_ID = (int) $_POST['id'];

		$errors = mymail('campaigns')->get_error_list($campaign_ID);

		$return['html'] = '<table class="wp-list-table widefat"><tbody>';

		foreach($errors as $i => $data){
			$return['html'] .= '<tr '.(!($i%2) ? ' class="alternate"' : '').'><td class="textright">'.($i+1).'</td><td><a href="edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$data->ID.'">'.$data->email.'</a></td><td><span class="red">'.$data->errormsg.'</span></td><td>'.date($timeformat, $data->timestamp+$timeoffset).'</td></tr>';
		}

		$return['html'] .= '</tbody>';
		$return['html'] .= '</table>';

		$this->json_return($return);


	}


	private function get_environment() {

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$campaign_ID = (int) $_POST['id'];

		$clients = mymail('campaigns')->get_clients($campaign_ID);

		$return['html'] = '<table class="wp-list-table widefat"><tbody>';

		$i = 1;
		foreach($clients as $client){
			$return['html'] .= '<tr '.(!($i%2) ? ' class="alternate"' : '').'><td class="client-type"><span class="mymail-icon client-'.$client['type'].'"></span></td><td>'.$client['name'].' '.$client['version'].'</td><td>'.round($client['percentage']*100,2).' % <span class="count">('.$client['count'].' '._n('opened', 'opens', $client['count'], 'mymail').')</span></td></tr>';
			$i++;
		}

		$return['html'] .= '</tbody>';
		$return['html'] .= '</table>';

		$this->json_return($return);


	}


	private function get_geolocation() {

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$campaign_ID = (int) $_POST['id'];

		$geo_data = mymail('campaigns')->get_geo_data($campaign_ID);
		$totalopens = mymail('campaigns')->get_opens($campaign_ID);


		$unknown_cities = array();
		$countrycodes = array();

		foreach($geo_data as $countrycode => $data){
			$x = wp_list_pluck($data, 3);
			if($x) $countrycodes[$countrycode] = array_sum($x);
			if($data[0][3]) $unknown_cities[$countrycode] = $data[0][3];
		}

		arsort($countrycodes);
		$total = array_sum($countrycodes);

		$return['geodata'] = $geo_data;
		$return['unknown_cities'] = $unknown_cities;
		$return['countrydata'] = array(array('code', __('Country', 'mymail'), __('opens', 'mymail') ));

		foreach($geo_data as $country => $cities){
			$opens = 0;
			foreach($cities as $city){
				$opens += $city[3];
			}
			$return['countrydata'][] = array($country, mymail('geo')->code2Country($country), $opens);
		}


		$return['html'] = '<div id="countries_wrap"><a class="zoomout button mymail-icon" title="'.__('back to world view', 'mymail').'">&nbsp;</a><div id="countries_map"></div><div id="mapinfo"></div><div id="countries_table"><table class="wp-list-table widefat">
			<tbody>';

			$i = 0;
			$unknown = $totalopens-$total;

			foreach($countrycodes as $countrycode => $count){
				$data = $geo_data[$countrycode];
				$return['html'] .= '<tr data-code="'.$countrycode.'" id="country-row-'.$countrycode.'" class="'.((!($i%2)) ? " alternate" : '').'"><td width="20"><span class="mymail-flag-24 flag-'.strtolower($countrycode).'"></span></td><td width="100%"><span class="country">'.mymail('geo')->code2Country($countrycode).'</span> <span class="count">('.round($count/$totalopens*100, 2).'%)</span></td><td class="textright">'.number_format_i18n($count).'</td></tr>';
				$i++;
			}

			if($unknown) :
			$return['html'] .= '<tr data-code="-" id="country-row-unknown" class="'.((!($i%2)) ? " alternate" : '').'"><td width="20"><span class="mymail-flag-24 flag-unknown"></span></td><td width="100%">'.__('unknown', 'mymail').' <span class="count">('.round($unknown/$totalopens*100, 2).'%)</span></td><td class="textright">'.number_format_i18n($unknown).'</td></tr>';
			endif;

		$return['html'] .= '</tbody></table></div>';

		$this->json_return($return);


	}


	private function get_recipients() {

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$campaign_ID = (int) $_POST['id'];

		$parts = !empty($_POST['types']) ? explode(',', $_POST['types']) : array('unopen', 'opens', 'clicks', 'unsubs', 'bounces');
		$orderby = !empty($_POST['orderby']) ? $_POST['orderby'] : 'sent';
		$order = !isset($_POST['order']) || $_POST['order'] == 'ASC' ? 'ASC' : 'DESC';

		$return['html'] = '<table class="wp-list-table widefat"><tbody>';

		$return['html'] = mymail('campaigns')->get_recipients_part($campaign_ID, $parts, 0, $orderby, $order);

		$return['html'] .= '</tbody>';
		$return['html'] .= '</table>';

		$this->json_return($return);


	}


	private function get_recipients_page() {

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$campaign_ID = (int) $_POST['id'];
		$page = (int) $_POST['page'];

		$parts = !empty($_POST['types']) ? explode(',', $_POST['types']) : array('unopen', 'opens', 'clicks', 'unsubs', 'bounces');
		$orderby = !empty($_POST['orderby']) ? $_POST['orderby'] : 'sent';
		$order = !isset($_POST['order']) || $_POST['order'] == 'ASC' ? 'ASC' : 'DESC';

		$return['html'] = mymail('campaigns')->get_recipients_part($campaign_ID, $parts, $page, $orderby, $order);
		$return['success'] = true;

		$this->json_return($return);


	}


	private function get_recipient_detail(){

		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$subscriber_id = intval($_POST['id']);
		$campaign_id = intval($_POST['campaignid']);

		$return['html'] = mymail('subscribers')->get_recipient_detail($subscriber_id, $campaign_id);
		$return['success'] = !!$return['html'];

		$this->json_return($return);

	}


	private function create_image() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		if(isset($_POST['id'])){

			$id = intval($_POST['id']);
			$src = isset($_POST['src']) ? ($_POST['src']) : NULL;
			$crop = isset($_POST['crop']) ? ($_POST['crop'] == 'true') : false;
			$width = isset($_POST['width']) ? intval($_POST['width']) : NULL;
			$height = isset($_POST['height']) ? intval($_POST['height']) : NULL;

			$return['success'] = !!($return['image'] = mymail('helper')->create_image($id, $src, $width, $height, $crop));
		}

		$this->json_return($return);

	}

	private function image_placeholder(){

		$width = !empty($_GET['w']) ? intval($_GET['w']) : 600;
		$height = !empty($_GET['h']) ? intval($_GET['h']) : round($width/1.6);
		$tag = isset($_GET['tag']) ? ''.$_GET['tag'].'' : '';

		$text = '{'.$tag.'}';
		$font_size = max(11,round($width/strlen($text)));
		//$font = MYMAIL_DIR . 'assets/font/OpenSans-Regular.ttf';
		$font = MYMAIL_DIR . 'assets/font/FredokaOne-Regular.ttf';

		$im = imagecreatetruecolor($width, $height);

		$bg = imagecolorallocate($im, 43, 179, 231);
		$font_color = imagecolorallocate($im, 255, 255, 255);

		imagefilledrectangle($im, 0, 0, $width, $height, $bg);

		if(function_exists( 'imagettftext' )){

			$bbox = imagettfbbox($font_size, 0, $font, $text);

			$center_x = $width / 2 - (abs($bbox[4] - $bbox[0]) / 2);
			$center_y = $height / 2;

			imagettftext($im, $font_size, 0, $center_x, $center_y, $font_color, $font, $text);

		}else{

			$font_size = 5;

			$fw = imagefontwidth($font_size);
			$fh = imagefontheight($font_size);
			$l = strlen($text);
			$tw = $l * $fw;

			$center_x = ($width - $tw)/2;
			$center_y = ($height - $font_size)/2;

			imagestring($im, $font_size, $center_x, $center_y, $text, $font_color);

		}

		header( 'Expires: Thu, 31 Dec 2050 23:59:59 GMT' );
		header( 'Cache-Control: max-age=3600, must-revalidate' );
		header( 'Pragma: cache' );
		header( 'Content-Type: image/gif' );

		imagegif($im);

		imagedestroy($im);

	}


	private function get_post_list() {

		$return['success'] = false;

		global $wp_post_statuses;
		$this->ajax_nonce(json_encode($return));

		$offset = intval($_POST['offset']);
		$post_count = mymail_option('post_count', 30);
		$search = $_POST['search'];

		if (in_array($_POST['type'], array( 'post', 'attachment' ))) {

			$post_type = esc_attr($_POST['type']);
			$current_id = isset($_POST['id']) ? intval($_POST['id']) : NULL;

			$defaults = array(
				'post_type' => $post_type,
				'numberposts' => $post_count,
				'suppress_filters' => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'offset' => $offset,
				'orderby' => 'post_date',
				'order' => 'DESC',
				'exclude' => $current_id
			);

			if($search)
				$defaults['s'] = $search;

			if($post_type == 'post'){
				parse_str($_POST['posttypes']);

				$post_types = isset($post_types) ? (array) $post_types : array(-1);

				$args = wp_parse_args(array(
					'post_type' => $post_types,
					'post_status' => array('publish', 'future', 'draft'),
				), $defaults);

				$post_counts = 0;
				foreach ($post_types as $type) {
					$counts = wp_count_posts($type);
					$post_counts += $counts->publish+$counts->future;
				}


			}else{

				$args = wp_parse_args(array(
					'post_status' => 'inherit',
					'post_mime_type' => ($post_type == 'attachment') ? array('image/jpeg', 'image/gif', 'image/png', 'image/tiff', 'image/bmp') : null,
				), $defaults);

				$post_counts = wp_count_posts($post_type);
				$post_counts = $post_counts->inherit;

			}

			$return['success'] = true;
			$return['itemcount'] = isset($_POST['itemcount']) ? $_POST['itemcount'] : array();

			$posts = get_posts($args);

			if($current_id && ($current = get_post($current_id))){

				array_unshift($posts, $current);
				$post_counts++;

			}else{

				$args['exclude'] = NULL;

			}

			$relativenames = array(
				-1 => __('last %s', 'mymail'),
				-2 => __('second last %s', 'mymail'),
				-3 => __('third last %s', 'mymail'),
				-4 => __('fourth last %s', 'mymail'),
				-5 => __('fifth last %s', 'mymail'),
				-6 => __('sixth last %s', 'mymail'),
				-7 => __('seventh last %s', 'mymail'),
				-8 => __('eighth last %s', 'mymail'),
				-9 => __('ninth last %s', 'mymail'),
				-10 => __('tenth last %s', 'mymail'),
				-11 => __('eleventh last %s', 'mymail'),
				-12 => __('twelfth last %s', 'mymail'),
			);

			$posts_lefts = max(0, $post_counts-$offset-$post_count);

			if ($post_counts) {
				$html = '';
				if ($_POST['type'] == 'post') {

					$pts = get_post_types( array( ), 'objects' );

					foreach ($posts as $post) {
						if(!isset($return['itemcount'][$post->post_type])) $return['itemcount'][$post->post_type] = 0;
						$relative = (--$return['itemcount'][$post->post_type]);
						$hasthumb = !!($thumbid = get_post_thumbnail_id($post->ID));
						$html .= '<li data-id="' . $post->ID . '" data-name="' . esc_attr($post->post_title) . '" class="status-' . $post->post_status . '';
						if($current_id == $post->ID) $html .= ' selected';
						($hasthumb)
							? $html .= ' has-thumb" data-thumbid="' . $thumbid . '"'
							: $html .= '"';
						$html .= ' data-link="' . get_permalink($post->ID) . '" data-type="' .$post->post_type. '" data-relative="' .$relative. '">';
						($hasthumb)
							? $html .= get_the_post_thumbnail($post->ID, array(48, 48))
							: $html .= '<div class="no-feature"></div>';
						$html .= '<span class="post-type">' . $pts[$post->post_type]->labels->singular_name . '</span>';
						$html .= '<strong>' . $post->post_title . ''.($post->post_status != 'publish' ? ' <em class="post-status wp-ui-highlight">'.$wp_post_statuses[$post->post_status]->label.'</em>' : '').'</strong>';
						$html .= '<span class="excerpt">' . trim(wp_trim_words(strip_shortcodes($post->post_content), 25)) . '</span>';
						$html .= '<span class="date">' . date_i18n(get_option('date_format'), strtotime($post->post_date)) . '</span>';
						$html .= '</li>';
					}
				} else if ($_POST['type'] == 'attachment') {

					foreach ($posts as $post) {
						$image = wp_get_attachment_image_src($post->ID, 'large');
						$title = $post->post_title ? $post->post_title : ($post->post_excerpt ? $post->post_excerpt : basename($image[0]));
						$html .= '<li data-id="' . $post->ID . '" data-name="' . esc_attr($title) . '" data-src="' . esc_attr($image[0]) . '" data-asp="' . ($image[2] ? str_replace(',', '.', $image[1]/$image[2]) : '') . '"';
						if($current_id == $post->ID) $html .= ' class="selected"';
						$html .= ">";
						$image = wp_get_attachment_image_src($post->ID, 'medium');
						$html .= '<a style="background-image:url(' . $image[0] . ')"><span class="caption" title="' . esc_attr($title) . '">' . esc_html($title) . '</span></a>';
						$html .= '</li>';
					}

				}

				if($posts_lefts)
				$html .= '<li><a class="load-more-posts" data-offset="'.($offset+$post_count).'" data-type="'.$_POST['type'].'"><span>'.sprintf(__('load more entries (%s left)', 'mymail'), number_format_i18n($posts_lefts)).'</span></a></li>';

				$return['html'] = $html;
			} else {
				$return['html'] = '<li><span class="norows">'. __('no entries found', 'mymail') .'</span></li>';
			}

		}else if($_POST['type'] == 'link'){

			$post_type = esc_attr($_POST['type']);

			$args = array();

			$post_counts = mymail('helper')->link_query( array(
				'post_status' => array('publish', 'finished', 'queued', 'paused'),
			), true );

			$posts_lefts = max(0, $post_counts-$offset-$post_count);

			$results = mymail('helper')->link_query( array(
				'offset' => $offset,
				'posts_per_page' => $post_count,
				'post_status' => array('publish', 'finished', 'queued', 'paused'),
			) );

			$return['success'] = true;

			if ( isset( $results ) ){
				$html = '';
				foreach($results as $entry){
					$hasthumb = !!($thumbid = get_post_thumbnail_id($entry['ID']));
					$html .= '<li data-id="' . $entry['ID'] . '" data-name="' . $entry['title'] . '"';
					if ($hasthumb)
						$html .= ' data-thumbid="' . $thumbid . '" class="has-thumb"';
					$html .= ' data-link="' . $entry['permalink'] . '">';
					($hasthumb)
						? $html .= get_the_post_thumbnail($entry['ID'], array(48, 48))
						: $html .= '<div class="no-feature"></div>';
					$html .= '<strong>' . $entry['title'] . '</strong>';
					$html .= '<span class="link">' . $entry['permalink'] . '</span>';
					$html .= '<span class="info">' . $entry['info'] . '</span>';
					$html .= '</li>';
				}
				if($posts_lefts)
				$html .= '<li><a class="load-more-posts" data-offset="'.($offset+$post_count).'" data-type="'.$post_type.'"><span>'.sprintf(__('load more entries (%s left)', 'mymail'), number_format_i18n($posts_lefts)).'</span></a></li>';

				$return['html'] = $html;

			}else{
				$return['html'] = '<li><span class="norows">'. __('no entries found', 'mymail') .'</span></li>';
			}

		}else if($_POST['type'] == '_rss'){

			$url = esc_url($_POST['url']);

			include_once( ABSPATH . WPINC . '/feed.php' );

			$rss = fetch_feed( $url );

			if ( ! is_wp_error( $rss ) ) {

				$return['success'] = true;

				$maxitems = $rss->get_item_quantity( $post_count );

				$rss_items = $rss->get_items( $offset, $maxitems );
				$post_counts = count($rss->get_items( $offset ));

				$posts_lefts = max(0, $post_counts-$offset-$post_count);

				$html = '';

				foreach($rss_items as $i => $item){
					$relative = 0;
					preg_match_all('/<img[^>]*src="(.*?(?:\.png|\.jpg|\.gif))"[^>]*>/i', $item->get_content(), $images);
					$hasthumb = false;
					if(!empty($images[0])){
						$hasthumb = $images[1][0];
					}
					$html .= '<li data-id="' . $url.'#'.($i+$offset) . '" data-name="' . $item->get_title() . '"';
					if ($hasthumb)
						$html .= ' data-thumbid="' . $hasthumb . '" class="has-thumb"';
					$html .= ' data-link="' . $item->get_permalink() . '" data-type="rss-item" data-relative="' .$relative. '">';
					($hasthumb)
						? $html .= '<div class="feature" style="background-image:url(' . $hasthumb . ')"></div>'
						: $html .= '<div class="no-feature"></div>';
					$html .= '<strong>' . $item->get_title() . '</strong>';
					$html .= '<span>' . trim(wp_trim_words(strip_shortcodes($item->get_description()), 18)) . '</span>';
					$html .= '<span>' . date_i18n(get_option('date_format'), strtotime($item->get_date())) . '</span>';
					$html .= '</li>';
				}

				if($posts_lefts)
				$html .= '<li><a class="load-more-posts" data-offset="'.($offset+$post_count).'" data-type="_rss"><span>'.sprintf(__('load more entries (%s left)', 'mymail'), number_format_i18n($posts_lefts)).'</span></a></li>';

				$return['html'] = $html;

				$return['rssinfo'] = array(
					'copyright' => $rss->get_copyright() ? $rss->get_copyright() : sprintf(__('All rights reserved %s', 'mymail'), '<a href="'.$rss->get_link().'" class="external">'.$rss->get_title().'</a>'),
					'title' => $rss->get_title(),
					'description' => $rss->get_description(),
				);

				$recent_feeds = get_option('mymail_recent_feeds', array());
				$recent_feeds = array_reverse($recent_feeds);
				$recent_feeds[$rss->get_title()] = $url;
				$recent_feeds = array_reverse($recent_feeds);
				$recent_feeds = array_slice($recent_feeds, 0, 5);
				update_option('mymail_recent_feeds', $recent_feeds);

			} else {

				$return['success'] = true;
				$return['html'] = '<li><span class="norows">'. $rss->get_error_message() .'</span></li>';
			}

		}

		$this->json_return($return);

	}



	private function get_post() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$strip_shortcodes = apply_filters( 'mymail_strip_shortcodes', true );

		if(is_numeric($_POST['id'])){
			$post = get_post(intval($_POST['id']));

			if ($post) {
				if(!$post->post_excerpt){
					if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
						$content = explode($matches[0], $post->post_content, 2);
						$post->post_excerpt = trim($content[0]);
					}
				}

				$post->post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );
				$return['success'] = true;
				$return['title'] = $post->post_title;
				$return['link'] = get_permalink($post->ID);
				$return['content'] = wpautop($post->post_content, false);
				$return['excerpt'] = (!empty($post->post_excerpt) ? $post->post_excerpt : wp_trim_words($post->post_content));

				$return['content'] = ($strip_shortcodes) ? strip_shortcodes( $return['content'] ) : do_shortcode( $return['content'] );
				$return['excerpt'] = ($strip_shortcodes) ? strip_shortcodes( $return['excerpt'] ) : do_shortcode( $return['excerpt'] );


				if (has_post_thumbnail($post->ID))
					$return['image'] = array(
						'id' => get_post_thumbnail_id($post->ID),
						'name' => $post->post_title
					);

				$return['content'] = str_replace('<img ', '<img editable ', $return['content']);
			}

		}else{

			$url = explode('#', esc_url($_POST['id']));
			$id = intval(array_pop($url));
			$url = implode('#', $url);
			$rss = fetch_feed( $url );

			if ( ! is_wp_error( $rss ) ) {

				$item = $rss->get_item( $id );

				$content = mymail()->sanitize_content($item->get_content());
				$excerpt = mymail()->sanitize_content($item->get_description());

				$return['success'] = true;
				$return['title'] = $item->get_title();
				$return['link'] = $item->get_permalink();
				$return['content'] = strip_shortcodes(wpautop($content, false));
				$return['excerpt'] = strip_shortcodes(wpautop($excerpt ? $excerpt : substr($content, 0, strpos($content, '<!--more-->')), false));

				preg_match_all('/<img[^>]*src="(.*?(?:\.png|\.jpg|\.gif))"[^>]*>/i', $content, $images);
				if(!empty($images[0])){
					$return['image'] = array(
						'src' => $images[1][0],
						'name' => $item->get_title(),
					);
					//remove that image
					$return['content'] = str_replace($images[0][0], '', $return['content']);
					$return['excerpt'] = str_replace($images[0][0], '', $return['excerpt']);
				}

				preg_match_all('/<img[^>]*src="[^"]+(\.feedsportal|\.feedburner)[^"]*"[^>]*>/i', $return['content'], $remove_images);
				if(!empty($remove_images[0])){
					foreach($remove_images as $i => $remove_image){
						$return['content'] = str_replace($remove_images[0][$i], '', $return['content']);
					}
				}
				$return['content'] = preg_replace(array('/class=".*?"/', '/id=".*?"/', '/style=".*?"/'), '', $return['content']);
				$return['content'] = str_replace('<img ', '<img editable ', $return['content']);
				$return['excerpt'] = preg_replace(array('/class=".*?"/', '/id=".*?"/', '/style=".*?"/'), '', $return['excerpt']);

			}
		}

		$this->json_return($return);

	}

	private function check_for_posts() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$return['success'] = true;

		$post_type = sanitize_key($_POST['post_type']);
		$relative = intval($_POST['relative']);
		$offset = $relative+1;
		$term_ids = isset($_POST['extra']) ? (array) $_POST['extra'] : array();

		$post = mymail()->get_last_post($offset, $post_type, $term_ids, true);
		$is_post = !!$post;

		$return['title'] = $is_post
			?  '<a href="post.php?post='.$post->ID.'&action=edit" class="external">#'.$post->ID.' &ndash; '.($post->post_title ? $post->post_title : __('no title', 'mymail')).'</a>'
			: __('no match for your selection!', 'mymail').' <a href="post-new.php?post_type='.$post_type.'" class="external">'.__('create a new one', 'mymail').'</a>?';

		$options = $relative.(!empty($term_ids) ? ';'.implode(';',$term_ids) : '');

		$return['pattern'] = apply_filters('mymail_auto_tag', array(
			'title' => '{'.$post_type.'_title:'.$options.'}',
			'alt' => '{'.$post_type.'_title:'.$options.'}',
			'content' => '{'.$post_type.'_content:'.$options.'}',
			'excerpt' => '{'.$post_type.'_excerpt:'.$options.'}',
			'link' => '{'.$post_type.'_link:'.$options.'}',
			'image' => '{'.$post_type.'_image:'.$options.'}',
		), $post_type, $options, $post );

		$return['pattern']['tag'] = '{'.$post_type.':'.$options.'}';

		$this->json_return($return);

	}


	private function get_post_term_dropdown() {
		$return['success'] = false;

		$this->ajax_nonce(json_encode($return));

		$post_type = $_POST['posttype'];
		$labels = isset($_POST['labels']) ? ($_POST['labels'] == 'true') : false;
		$names = isset($_POST['names']) ? $_POST['names'] : false;


		$return['html'] = '<div class="dynamic_embed_options_taxonomies">'.mymail('helper')->get_post_term_dropdown($post_type, $labels, $names).'</div>';
		$return['success'] = true;

		$this->json_return($return);

	}




	private function forward_message() {
		$return['success'] = false;

		parse_str($_POST['data'], $data);

		if (!wp_verify_nonce($data['_wpnonce'], $data['url'])) {
			die(json_encode($return));
		}

		if (empty($data['message']) || !mymail_is_email($data['receiver']) || !mymail_is_email($data['sender']) || empty($data['sendername'])) {

			$return['msg'] = __('Please fill out all fields correctly!', 'mymail');

			$this->json_return($return);

		}

		$mail = mymail('mail');
		$mail->to = esc_attr($data['receiver']);
		$mail->subject = esc_attr('[' . get_bloginfo('name') . '] ' . sprintf(__('%s is forwarding an email to you!', 'mymail'), $data['sendername']));
		//$mail->from = esc_attr($data['sender']);
		$mail->from = mymail_option('from');
		$mail->from_name = sprintf(_x('%1$s via %2$s', 'user forwarded via website', 'mymail'), $data['sendername'],  get_bloginfo('name'));

		$message = nl2br($data['message']) . "<br><br>" . $data['url'];

		$replace = array(
			'notification' => sprintf(__('%1$s is forwarding this mail to you via %2$s', 'mymail'), $data['sendername'].' (<a href="mailto:'.esc_attr($data['sender']).'">'.esc_attr($data['sender']).'</a>)', '<a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a>' )
		);

		$return['success'] = $mail->send_notification( $message, $mail->subject, $replace );

		$return['msg'] = ($return['success']) ? __('Your message was sent successfully!', 'mymail') : __('Sorry, we couldn\'t deliver your message. Please try again later!', 'mymail');

		$this->json_return($return);

	}

	private function remove_notice(){

		$return['success'] = false;

		global $mymail_notices;

		if($mymail_notices = get_option( 'mymail_notices' )){

			if(isset($_GET['id']) && isset($mymail_notices[$_GET['id']])){

				unset($mymail_notices[$_GET['id']]);

				update_option( 'mymail_notices', $mymail_notices );

			}

			$return['success'] = true;

		}

		$this->json_return($return);


	}


	private function ajax_nonce($return = NULL, $nonce = 'mymail_nonce') {
		if (!wp_verify_nonce($_REQUEST['_wpnonce'], $nonce)) {
			if (is_string($return)) {
				wp_die($return);
			}else {
				die($return);
			}
		}

	}

	private function get_file_list( ) {
		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		$return['slug'] = $_POST['slug'];

		$return['files'] = mymail('templates')->get_files($return['slug']);

		if(count($return['files'])){
			$return['success'] = true;
			$return['base'] = trailingslashit(mymail('templates')->get_url()).$return['slug'];
			foreach($return['files'] as $file => $data){
				$return['files'][$file]['screenshot'] = mymail('templates')->get_screenshot( $return['slug'], $file);
			}
		}

		$this->json_return($return);
	}

	private function get_template_html( ) {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		$return['slug'] = dirname($_POST['href']);
		$return['file'] = basename($_POST['href']);
		$file = mymail('templates')->get_path() .'/'.$return['slug'].'/'.$return['file'];

		$return['files'] = mymail('templates')->get_files($return['slug'], true);

		if(file_exists($file)){
			$return['success'] = !!$return['html'] = @file_get_contents($file);
		}

		$this->json_return($return);
	}

	private function set_template_html( ) {
		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		$this->ajax_filesystem( );


		$return['slug'] = esc_attr($_POST['slug']);
		$return['file'] = esc_attr($_POST['file']);
		$new = !empty($_POST['name']);

		$name = $new ? esc_attr($_POST['name']) : $return['file'];
		$content = stripslashes($_POST['content']);
		$filename = false;

		if($new){
			$data = mymail('templates')->get_template_data($content);

			$content = preg_replace('#^(\s)?<!--(.*)-->\n(\s)?#sUm', '', $content);

			$filename = mymail('template')->create_new($name, $content);

		}else{

			global $wp_filesystem;
			mymail_require_filesystem();
			$path = mymail('templates')->get_path();
			$file = $path .'/'.$return['slug'].'/'.$return['file'];

			$content = mymail()->sanitize_content($content);

			if ($wp_filesystem->put_contents( $file, $content, FS_CHMOD_FILE)) {
				$filename = $file;
			}

		}

		if($filename){
			$file = basename($filename);
			if($new)
				$return['newfile'] = $file;
			$return['msg'] = __('File has been saved!', 'mymail');
			$return['success'] = true;
			wp_remote_get( mymail('templates')->get_screenshot($return['slug'], $file) );
		}else{
			$return['msg'] = __('Not able to save file!', 'mymail');
		}

		$this->json_return($return);
	}

	private function remove_template( ) {
		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		$path = mymail('templates')->get_path();

		$file = $path .'/'.esc_attr($_POST['file']);

		if(file_exists($file) && current_user_can('mymail_delete_templates')){
			mymail_require_filesystem();

			global $wp_filesystem;

			$return['success'] = $wp_filesystem->delete( $file );
		}

		$this->json_return($return);
	}

	private function notice_dismiss( ) {
		$return['success'] = true;

		if(isset($_POST['id'])) mymail_remove_notice($_POST['id']);


		$this->json_return($return);
	}

	private function notice_dismiss_all( ) {
		$return['success'] = true;

		update_option( 'mymail_notices', array() );

		$this->json_return($return);
	}

	private function ajax_filesystem() {
		if('ftpext' == get_filesystem_method() && !defined('FTP_HOST') && !defined('FTP_USER') && !defined('FTP_PASS')){
			$return['msg'] = __('WordPress is not able to access to your filesystem!', 'mymail');
			$return['msg'] .= "\n".sprintf(__('Please add following lines to the wp-config.php %s', 'mymail'), "\n\ndefine('FTP_HOST', 'your-ftp-host');\ndefine('FTP_USER', 'your-ftp-user');\ndefine('FTP_PASS', 'your-ftp-password');\n");
			$return['success'] = false;

			$this->json_return($return);
		}

	}
	private function load_geo_data(){
		$return['success'] = false;

		$type = esc_attr($_POST['type']);


		if($type == 'country'){

			require_once MYMAIL_DIR . 'classes/libs/Ip2Country.php';
			$ip2Country = new Ip2Country();

			$result = $ip2Country->renew(true);
			if(is_wp_error($result)){
				$return['msg'] = __('Couldn\'t load Country DB', 'mymail').' ['.$result->get_error_message().']';
			}else{
				$return['success'] = true;
				$return['msg'] = __('Country DB successfully loaded!', 'mymail');
				$return['path'] = $result;
				$return['buttontext'] = __('Update Country Database', 'mymail');
				mymail_update_option('countries_db', $result);
			}

		}else if($type == 'city'){
			require_once MYMAIL_DIR . 'classes/libs/Ip2City.php';
			$ip2City = new Ip2City();

			$result = $ip2City->renew(true);
			if(is_wp_error($result)){
				$return['msg'] = __('Couldn\'t load City DB', 'mymail').' ['.$result->get_error_message().']';
			}else{
				$return['success'] = true;
				$return['msg'] = __('City DB successfully loaded!', 'mymail');
				$return['path'] = $result;
				$return['buttontext'] = __('Update City Database', 'mymail');
				mymail_update_option('cities_db', $result);
			}
		}else{
			$return['msg'] = 'not allowed';
		}

		$this->json_return($return);

	}

	private function sync_all_subscriber(){

		$return['success'] = false;
		$limit = 100;
		$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

		$return['count'] = mymail('subscribers')->sync_all_subscriber($limit, $offset);
		$return['success'] = true;
		$return['offset'] = $limit+$offset;

		$this->json_return($return);

	}

	private function sync_all_wp_user(){

		$return['success'] = false;
		$limit = 100;
		$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

		$return['count'] = mymail('subscribers')->sync_all_wp_user($limit, $offset);
		$return['success'] = true;
		$return['offset'] = $limit+$offset;

		$this->json_return($return);


	}

	private function bounce_test(){

		$return['success'] = false;

		$identifier = 'mymail_bonuce_test_'.md5(uniqid());

		$return['identifier'] = $identifier;

		$mail = mymail('mail');
		$mail->to = mymail_option('bounce');
		$mail->subject = 'MyMail Bounce Test Mail';

		$replace = array('preheader' => 'You can delete this message!', 'notification' => 'This message was sent from your WordPress blog to test your bounce server. You can delete this message!');

		$return['success'] = $mail->send_notification( $identifier, $mail->subject, $replace );

		$this->json_return($return);

	}

	private function bounce_test_check(){

		$return['success'] = false;
		$return['msg'] = '';

		$passes = intval($_POST['passes']);
		$identifier = $_POST['identifier'];

		if(!mymail_option('bounce_active')){
			$return['complete'] = true;
			$this->json_return($return);
		}

		$server = mymail_option('bounce_server');
		$user = mymail_option('bounce_user');
		$pwd = mymail_option('bounce_pwd');

		if (!$server || !$user || !$pwd){
			$return['complete'] = true;
			$this->json_return($return);
		}

		if(mymail_option('bounce_ssl')) $server = 'ssl://'.$server;

		require_once ABSPATH . WPINC . '/class-pop3.php';
		$pop3 = new POP3();

		if (!$pop3->connect($server, mymail_option('bounce_port', 110)) || !$pop3->user($user)){
			$return['complete'] = true;
			$return['msg'] = __('Unable to connect to bounce server! Please check your settings.', 'mymail');
			echo json_encode($return);
			exit;
		}

		$return['success'] = true;
		$count = $pop3->pass($pwd);
		$return['msg'] = __('checking for new messages', 'mymail').str_repeat('.', $passes);

		if($passes > 20){
			$return['complete'] = true;
			$return['msg'] = __('Unable to get test message! Please check your settings.', 'mymail');
		}

		if (false === $count || 0 === $count){
			if(0 === $count) $pop3->quit();
			$this->json_return($return);
		}

		for ($i = 1; $i <= $count; $i++) {
			$message = $pop3->get($i);

			if(!$message) continue;

			$message = implode($message);

			if(strpos($message, $identifier)){
				$pop3->delete($i);
				$pop3->quit();
				$return['complete'] = true;
				$return['msg'] = __('Your bounce server is good!', 'mymail');
				$this->json_return($return);
			} else {
				$pop3->reset();
			}

		}

		$pop3->quit();

		$this->json_return($return);

	}

	private function get_system_info(){

		global $mymail_error_log;

		$return['success'] = false;
		$return['msg'] = 'You have no permission to access the stats';

		$this->ajax_nonce( json_encode( $return ) );

		if (!current_user_can( 'manage_options' )){
			$this->json_return($return);
		}

		$space = 30;
		$infos = mymail('settings')->get_system_info($space);
		$output = "### Begin System Info ###\n\n";
		$output .= "## Please include this information when posting support requests ##\n\n";

		foreach($infos as $name => $value){
			if($value == '--'){
				$output .= "\n";
				continue;
			}
			$output .= $name.str_repeat(' ', $space-strlen($name)).$value."\n";
		}

		$output .= "### End System Info ###\n";

		$return['msg'] = $output;
		$return['log'] = $mymail_error_log;

		$this->json_return($return);

	}


	private function get_gravatar() {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		$email = esc_attr($_POST['email']);

		$return['success'] = true;
		$return['url'] = mymail('subscribers')->get_gravatar_uri($email, 400);

		$this->json_return($return);


	}


	private function check_email() {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		$email = esc_attr($_POST['email']);

		$subscriber = mymail('subscribers')->get_by_mail($email);
		$return['exists'] = !!$subscriber && $subscriber->ID != (int) $_POST['id'];
		$return['success'] = true;

		$this->json_return($return);


	}

	private function create_list() {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		$name = stripslashes($_POST['name']);
		$campaign_id = intval($_POST['id']);
		$listtype = $_POST['listtype'];

		$return['success'] = mymail('campaigns')->create_list_from_option($name, $campaign_id, $listtype);
		$return['msg'] = $return['success'] ? __('List has been created', 'mymail') : __("Couldn't create List", 'mymail');

		$this->json_return($return);


	}

	private function get_create_list_count() {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		$campaign_id = intval($_POST['id']);
		$listtype = esc_attr($_POST['listtype']);

		$return['count'] = mymail('campaigns')->create_list_from_option('', $campaign_id, $listtype, true);
		$return['success'] = true;

		$this->json_return($return);


	}

	private function editor_image_upload_handler() {

		global $wpdb;

		$safe_mode = @ini_get('safe_mode');
		$memory_limit = @ini_get('memory_limit');
		$max_execution_time = @ini_get('max_execution_time');

		$return['success'] = false;

		if(!$safe_mode){
			@set_time_limit(0);

			if(intval($max_execution_time) < 300){
				@ini_set( 'max_execution_time', 300 );
			}
			if(intval($memory_limit) < 256){
				@ini_set( 'memory_limit', '256M' );
			}
		}

		if(isset($_FILES['async-upload'])){

			if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

			$width = intval($_POST['width']);
			$height = intval($_POST['height']);
			$factor = intval($_POST['factor']);

			$wp_upload_dir = wp_upload_dir();
			$image = false;

			$filename = $_FILES['async-upload']['name'];

			if(file_exists($wp_upload_dir['path'] . '/' . $filename) &&
				md5_file($_FILES['async-upload']['tmp_name']) == md5_file($wp_upload_dir['path'] . '/' . $filename)){

				$url = $wp_upload_dir['url']. '/' . $filename;
				if($attach_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND guid = %s;", $url ) )){
					$image = mymail('helper')->create_image($attach_id, NULL, $width, NULL, false);
				}


			}

			if(!$image){

				$result = wp_handle_upload( $_FILES['async-upload'], array(
					'test_form' => false,
					'mimes' => array(
						'jpeg' => 'image/jpeg',
						'jpg' => 'image/jpeg',
						'png' => 'image/png',
						'tiff' => 'image/tiff',
						'tif' => 'image/tiff',
						'gif' => 'image/gif',
					)
				));

				$filename = basename( $result['file'] );
				$filetype = wp_check_filetype( $filename, null );

				//don't add to library if alt key is pressed
				$add_to_library = !($_POST['altKey'] == 'true');

				if($add_to_library){

					$post_id = isset($_POST['ID']) ? intval($_POST['ID']) : 0;

					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . $filename,
						'post_mime_type' => $filetype['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
						'post_content'   => '',
						'post_status'    => 'inherit',
						'post_parent'    => $post_id
					);
					$attach_id = wp_insert_attachment( $attachment, $result['file'] );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $result['file'] );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					$image = mymail('helper')->create_image($attach_id, NULL, $width, NULL, false);

				}else{

					$image = mymail('helper')->create_image(NULL, $result['file'], $width, NULL, false);

				}

			}

			if($image){
				$return['image'] = $image;
				$return['success'] = true;
			}

		}

		if(isset($return)){

			$this->json_return($return);

		}

	}


	private function template_upload_handler() {

		global $wpdb;

		if(!current_user_can('mymail_upload_templates')) die('not allowed');

		$safe_mode = @ini_get('safe_mode');
		$memory_limit = @ini_get('memory_limit');
		$max_execution_time = @ini_get('max_execution_time');

		$return['success'] = false;

		if(!$safe_mode){
			@set_time_limit(0);

			if(intval($max_execution_time) < 300){
				@ini_set( 'max_execution_time', 300 );
			}
			if(intval($memory_limit) < 256){
				@ini_set( 'memory_limit', '256M' );
			}
		}

		if(isset($_FILES['async-upload'])){

			if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

			// $result = wp_handle_upload( $_FILES['async-upload'], array(
			// 	'mimes' => array('zip' => 'multipart/x-zip'),
			// ));
			$result = wp_handle_upload( $_FILES['async-upload'], array(
				'test_form' => false,
				'mimes' => array('zip' => 'multipart/x-zip')
			));

			if(isset($result['error'])){

				$return['error'] = $result['error'];

			}else{

				$result = mymail('templates')->unzip_template($result['file']);

				if(is_wp_error($result)){

					$return['error'] = $result->get_error_message();

				}else{

					mymail_notice(sprintf(__('Template %s has been uploaded', 'mymail'), '"'.$result['name'].' '.$result['version'].'"'), 'updated', true);
					$return['success'] = true;
				}
			}

		}

		if(isset($return)){

			$this->json_return($return);

		}

	}

	private function get_dashboard_camp( ) {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if(isset($_POST['id'])){

			$camp_id = intval($_POST['id']);

			if($campaign = mymail('campaigns')->get($camp_id)){
				$campaign_data = array(
					'name' => $campaign->post_title,
					'active' => $campaign->post_status == 'active',
					'ID' => $campaign->ID,
					'totals' => mymail('campaigns')->get_totals($camp_id),
					'sent' => mymail('campaigns')->get_sent($camp_id),
					'openrate' => mymail('campaigns')->get_open_rate($camp_id),
					'clickrate' => mymail('campaigns')->get_click_rate($camp_id),
					'bouncerate' => mymail('campaigns')->get_bounce_rate($camp_id),
					'unsubscriberate' => mymail('campaigns')->get_unsubscribe_rate($camp_id),
				);

				$return['camp'] = $campaign_data;
				$return['success'] = true;

			}

		}


		$this->json_return($return);
	}

	private function get_dashboard_chart( ) {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );
		$range = isset($_POST['range']) ? $_POST['range'] : '7 days';
		$return['chart'] = mymail('stats')->get_dashboard($range);

		$return['success'] = true;

		$this->json_return($return);
	}


}
