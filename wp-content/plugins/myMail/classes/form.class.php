<?php if (!defined('ABSPATH')) die('not allowed');

class mymail_form {

	private $values = array();
	private $scheme = 'http';
	private $object = array(
		'userdata' => array(),
		'lists' => array(),
		'errors' => array(),
	);
	private $lists = array();
	private $message = '';

	private $form = NULL;
	private $campaignID = NULL;
	private $cache = true;
	private $honeypot = true;
	private $hash = NULL;
	private $profile = false;
	private $preview = false;
	private $ajax = true;
	private $embed_style = true;
	private $form_endpoint = 'subscribe';
	private $classes = array('mymail-form', 'mymail-form-submit');

	private $redirect = false;
	private $referer = true;
	private $extern = false;
	private $editlink = false;

	static $add_script = false;
	static $add_style = false;

	public function __construct( ) {
		$this->scheme =  is_ssl() ? 'https' : 'http' ;
		$this->honeypot = !is_admin();
		$this->form = new StdClass();
	}

	public function set_error($message, $field = '_') {

		$this->object['errors'][$field] = (string) $message;
	}

	public function set_success($message, $field = '_') {

		$this->object['success'][$field] = (string) $message;
	}

	public function id($id) {

		$this->ID = $id;
		$this->form = mymail('forms')->get($this->ID, true, true);
		if(!$this->form) $this->form = $this->form = mymail('forms')->get(1, true, true);
		$this->ajax();
		return $this;
	}

	public function is_preview($bool = true) {

		$this->preview = !!$bool;
	}

	public function ajax($bool = true) {

		$this->ajax = !!$bool;
		($bool) ? $this->add_class('mymail-ajax-form') : $this->remove_class('mymail-ajax-form');
	}

	public function add_class($class) {

		$this->classes[] = esc_attr($class);
		$this->classes = array_unique($this->classes);
	}

	public function remove_class($class) {

		if(($key = array_search($class, $this->classes)) !== false)
			unset($this->classes[$key]);
	}

	public function redirect($value) {

		$this->redirect = $value;
	}

	public function editlink($bool = true) {

		$this->editlink = !!$bool;
	}

	public function __call($key, $args) {

		$value = empty($args) ? true : $args[0];

		if(isset($this->form->{$key})){
			$this->form->{$key} = $value;
		}else{
			$this->{$key} = $value;
		}

	}

	public function __get($key) {

		if(isset($this->form->{$key})) return $this->form->{$key};
		return NULL;
	}

	public function render($echo = true) {

		if(!$this->form) return;

		add_action('wp_footer', array( &$this, 'print_script'));
		add_action('admin_footer', array( &$this, 'print_script'));

		if($this->unsubscribe) return $this->unsubscribe_form();

		if($this->prefill){

			$current_user = wp_get_current_user();
			if($current_user->ID != 0){
				$this->object['userdata']['email'] = $current_user->user_email;
				$this->object['userdata']['firstname'] = get_user_meta( $current_user->ID, 'first_name', true );
				$this->object['userdata']['lastname'] = get_user_meta( $current_user->ID, 'last_name', true );
				if (!$this->object['userdata']['firstname'])
				$this->object['userdata']['firstname'] = $current_user->display_name;
				$this->cache(false);
			}
		}
		if($this->profile){
			if($subscriber = mymail('subscribers')->get_by_hash($this->hash, true)){
				$this->object['userdata'] = (array) $subscriber;
			}
		}

		if(isset($_GET['userdata'])){
			$this->object['userdata'] = wp_parse_args( $this->object['userdata'], $_GET['userdata'] );
		}

		if(isset($_GET['mymail_error'])){ //for non ajax request

			$transient = 'mymail_error_'.esc_attr($_GET['mymail_error']);
			$data = get_transient($transient);
			if($data){
				$this->object['userdata'] = $data['userdata'];
				$this->object['errors'] = $data['errors'];
				$this->object['lists'] = $data['lists'];
				$this->has_errors(!!count($this->object['errors']));
				delete_transient($transient);
			}

		}

		$html = '';
		//$html .= '<!-- Begin MyMail Form -->'."\n";
		$html .= '%%STYLES%%';

		$html .= '<form action="%%FORMACTION%%" method="post" class="mymail-form-%%FORMID%% %%CLASSES%%">';
		$html .= '%%INFOS%%';
		$html .= '%%HIDDENFIELDS%%';


		$customfields = mymail()->get_custom_fields();
		$inline = $this->form->inline;
		$asterisk = $this->form->asterisk;

		$fields = array();

		foreach($this->form->fields as $field){

			$required = $field->required;

			$label = !empty($field->name) ? $field->name : mymail_text($field->field_id);
			$esc_label = esc_attr(strip_tags($label));

			$value = (isset($this->object['userdata'][$field->field_id])
						? esc_attr($this->object['userdata'][$field->field_id])
						: '');

			$class = (isset($this->object['errors'][$field->field_id]) ? ' error' : '');

			switch($field->field_id){

				case 'email':

					$fields['email'] = '<div class="mymail-wrapper mymail-email-wrapper'.$class.'">';
					if(!$inline) $fields['email'] .= '<label for="mymail-email-'.$this->ID.'">'.$label.' '.($asterisk ? '<span class="mymail-required">*</span>' : '').'</label>';
					$fields['email'] .= '<input id="mymail-email-'.$this->ID.'" name="email" type="email" value="'.$value.'"'.($inline ? ' placeholder="'.$esc_label.($asterisk ? ' *' : '').'"' : '').' class="input mymail-email mymail-required" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'" spellcheck="false">';
					$fields['email'] .= '</div>';

				break;

				case 'firstname':
				case 'lastname':

					$fields[$field->field_id] = '<div class="mymail-wrapper mymail-'.$field->field_id.'-wrapper'.$class.'">';
					if(!$inline) $fields[$field->field_id] .= '<label for="mymail-'.$field->field_id.'-'.$this->ID.'">'.$label.($required && $asterisk ? ' <span class="mymail-required">*</span>' : '').'</label>';
					$fields[$field->field_id] .= '<input id="mymail-'.$field->field_id.'-'.$this->ID.'" name="'.$field->field_id.'" type="text" value="'.$value.'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-'.$field->field_id.''.($required ? ' mymail-required' : '').'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
					$fields[$field->field_id] .= '</div>';

				break;

				//custom fields
				default:

					if(!isset($customfields[$field->field_id])) break;
					$data = $customfields[$field->field_id];

					//$label = isset($form->labels[$field->field_id]) ? $form->labels[$field->field_id] : $data['name'];
					//$esc_label = esc_attr(strip_tags($label));

					$fields[$field->field_id] = '<div class="mymail-wrapper mymail-'.$field->field_id.'-wrapper'.$class.'">';

					$showlabel = !$inline;

					switch($data['type']){
						case 'dropdown':case 'radio': $showlabel = true; break;
						case 'checkbox': $showlabel = false; break;
					}

					if($showlabel){
						$fields[$field->field_id] .= '<label for="mymail-'.$field->field_id.'-'.$this->ID.'">'.$label;
						if ($required && $asterisk) $fields[$field->field_id] .= ' <span class="mymail-required">*</span>';
						$fields[$field->field_id] .= '</label>';
					}

				switch($data['type']){

					case 'dropdown':

						$fields[$field->field_id] .= '<select id="mymail-'.$field->field_id.'-'.$this->ID.'" name="'.$field->field_id.'" class="input mymail-'.$field->field_id.''.($required ? ' mymail-required' : '').'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
					foreach($data['values'] as $v){
						if(!isset($data['default'])) $data['default'] = $value;
						$fields[$field->field_id] .= '<option value="'.$v.'" '.selected($data['default'], $v, false).'>'.$v.'</option>';
					}
						$fields[$field->field_id] .= '</select>';
						break;

					case 'radio':

						$fields[$field->field_id] .= '<ul class="mymail-list">';
						$i = 0;
						foreach($data['values'] as $v){
							if(!isset($data['default'])) $data['default'] = $value;
							$fields[$field->field_id] .= '<li><label><input id="mymail-'.$field->field_id.'-'.$this->ID.'-'.($i++).'" name="'.$field->field_id.'" type="radio" value="'.$v.'" class="radio mymail-'.$field->field_id.''.($required ? ' mymail-required' : '').'" '.checked($data['default'], $v, false).' aria-label="'.$v.'"> '.$v.'</label></li>';

						}
						$fields[$field->field_id] .= '</ul>';
						break;

					case 'checkbox':

						$fields[$field->field_id] .= '<label for="mymail-'.$field->field_id.'-'.$this->ID.'">';
						$fields[$field->field_id] .= '<input type="hidden" name="'.$field->field_id.'" value="0"><input id="mymail-'.$field->field_id.'-'.$this->ID.'" name="'.$field->field_id.'" type="checkbox" value="1" '.checked($value || (!$value && isset($data['default'])), true, false).' class="mymail-'.$field->field_id.''.($required ? ' mymail-required' : '').'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'"> ';
						$fields[$field->field_id] .= ' '.$label;
						if ($required && $asterisk) $fields[$field->field_id] .= ' <span class="mymail-required">*</span>';
						$fields[$field->field_id] .= '</label>';

						break;

					case 'date':

						$fields[$field->field_id] .= '<input id="mymail-'.$field->field_id.'-'.$this->ID.'" name="'.$field->field_id.'" type="text" value="'.$value.'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input input-date datepicker mymail-'.$field->field_id.''.($required ? ' mymail-required' : '').'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';

						break;

					case 'textarea':

						$fields[$field->field_id] .= '<textarea id="mymail-'.$field->field_id.'-'.$this->ID.'" name="'.$field->field_id.'"'.($inline ? ' placeholder="'.$label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-'.$field->field_id.''.($required ? ' mymail-required' : '').'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$label.'">'.esc_textarea($value).'</textarea>';

						break;

					default:

						$fields[$field->field_id] .= '<input id="mymail-'.$field->field_id.'-'.$this->ID.'" name="'.$field->field_id.'" type="text" value="'.$value.'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-'.$field->field_id.''.($required ? ' mymail-required' : '').'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';

				}

				$fields[$field->field_id] .= '</div>';

			}

		}


		if ($this->form->userschoice ){
			$lists = mymail('forms')->get_lists($this->ID);

			if(!empty($lists)){

				if($this->profile){
					$userlists = mymail('subscribers')->get_lists($this->object['userdata']['ID'], true);
				}

				$fields['lists'] = '<div class="mymail-wrapper mymail-lists-wrapper'.$class.'"><label>'.mymail_text('lists', __('Lists', 'mymail')).'</label>';

				if ($this->form->dropdown) {
					$fields['lists'] .= '<select name="lists[]" class="input mymail-lists-dropdown">';
					foreach ($lists as $list) {
						$selected = !empty($this->object['errors']) && in_array($list->ID, $this->object['lists']);

						$fields['lists'] .= '<option value="'.$list->ID.'"'.selected( $selected, true, false ).'> '.$list->name.'</option>';
					}
					$fields['lists'] .= '</select>';
				}else{
					$fields['lists'] .= '<ul class="mymail-list">';
					foreach ($lists as $i => $list) {
					$checked = (empty($this->object['errors']) && $this->form->precheck)
					|| (!empty($this->object['errors']) && in_array($list->ID, $this->object['lists']))
					|| ($this->form->precheck && $this->preview);

					if($this->profile) $checked = in_array($list->ID, $userlists);

						$fields['lists'] .= '<li><label title="'.esc_attr($list->description).'"><input type="hidden" name="lists['.$i.']" value="0"><input class="mymail-list mymail-list-'.$list->slug.'" type="checkbox" name="lists['.$i.']" value="'.$list->ID.'" '.checked( $checked, true, false ).' aria-label="'.esc_attr($list->name).'"> '.$list->name;
						if($list->description)
							$fields['lists'] .= ' <span class="mymail-list-description mymail-list-description-'.$list->ID.'">'.$list->description.'</span>';
						$fields['lists'] .= '</label></li>';

					}
					$fields['lists'] .= '</ul>';
				}

				$fields['lists'] .= '</div>';
			}

		}


		$buttonlabel = esc_attr(strip_tags($this->form->submit));


		$fields['_submit'] = '<div class="mymail-wrapper mymail-submit-wrapper form-submit"><input name="submit" type="submit" value="'.$buttonlabel.'" class="submit-button button" aria-label="'.$buttonlabel.'"></div>';

		//if($cache) set_transient( $transient, $fields );

		$fields = apply_filters('mymail_form_fields', $fields, $this->ID, $this->form);

		if($this->honeypot){
			$position = rand(count($fields), 0)-1;
			$fields = array_slice($fields, 0, $position, true) +
			array('_honeypot' => '<label style="position:absolute;top:-99999px;'.(is_rtl() ? 'right' : 'left').':-99999px;z-index:-99;"><input name="n_'.wp_create_nonce( 'honeypot' ).'_email" type="email" tabindex="-1" autocomplete="off"></label>') +
			array_slice($fields, $position, NULL, true);
		}

		$html .= '<div class="mymail-form-fields">';
		$html .= "\n".implode("\n", $fields)."\n";
		$html .= '</div>'."\n";

		$html .= '</form>'."\n";

		if($this->editlink && current_user_can('mymail_edit_forms')){
			$html .= '<a class="form-edit-link" href="'.admin_url('edit.php?post_type=newsletter&page=mymail_forms&ID='.$this->form->ID).'">'.__('edit form', 'mymail').'</a>';
		}

		//$html .= '<!-- End MyMail Form -->';

		$html = str_replace('%%FORMACTION%%', $this->get_form_action($this->profile ? 'mymail_profile_submit' : 'mymail_form_submit'), $html);
		$html = str_replace('%%CLASSES%%', esc_attr(implode(' ', $this->classes)), $html);
		$html = str_replace('%%FORMID%%', $this->ID, $html);
		$html = str_replace('%%STYLES%%', $this->get_styles(), $html);
		$html = str_replace('%%HIDDENFIELDS%%', $this->get_hidden_fields(), $html);
		$html = str_replace('%%INFOS%%', $this->get_info(), $html);

		$html = apply_filters('mymail_form', $html, $this->ID, $this->form);

		if(!$echo) return $html;

		echo $html;

	}

	private function get_styles() {

		$html = '';
		if(!self::$add_style){
			ob_start();
				$this->print_style($this->embed_style);
				$html .= ob_get_contents();
			ob_end_clean();

		}
		if(isset($this->form->stylesheet) && !empty($this->form->stylesheet))
			$html .= '<style type="text/css" media="screen" class="mymail-custom-form-css">'."\n".$this->strip_css($this->form->stylesheet).'</style>'."\n";

		return $html;
	}

	private function get_hidden_fields() {

		global $pagenow;

		$html = '';

		$redirect = esc_url(home_url(remove_query_arg(array('mymail_error', 'mymail_success'), $_SERVER['REQUEST_URI'])));
		$referer = $pagenow == 'form.php' ? (isset($_GET['referer']) ? $_GET['referer'] : 'extern') : $redirect;

		if($this->redirect)
			$html .= '<input name="_redirect" type="hidden" value="'.esc_attr(is_string($this->redirect) ? $this->redirect : $redirect).'">'."\n";

		if($this->referer)
			$html .= '<input name="_referer" type="hidden" value="'.esc_attr(is_string($this->referer) ? $this->referer : $referer).'">'."\n";

		if($this->hash)
			$html .= '<input name="_hash" type="hidden" value="'.esc_attr($this->hash).'">'."\n";

		$html .= '<input name="formid" type="hidden" value="'.$this->ID.'">'."\n";

		return $html;
	}

	private function get_info() {

		$html = '';

		if(!empty($this->object['success'])):
		$html .= '<div class="mymail-form-info success">';
		$html .= $this->get_message('success');
		$html .= $this->message;
		$html .= '</div>'."\n";
		endif;
		if(!empty($this->object['errors'])):
		$html .= '<div class="mymail-form-info error">';
		$html .= $this->get_message();
		$html .= '</div>'."\n";
		endif;

		return $html;

	}

	public function is_profile($bool = true) {


		if($bool){

			$this->profile = true;
			$this->form_endpoint = 'update';
			$this->form->submit = mymail_text('profilebutton', __('Update Profile', 'mymail'));
			$this->add_class('is-profile');
			$this->set_hash();

		}else{

			$this->profile = false;
			$this->remove_class('is-profile');
			$this->hash = NULL;
			$this->form_endpoint = 'subscribe';

		}

	}

	public function campaign_id($ID) {
		$this->campaignID = intval($ID);
	}

	public function is_unsubscribe($bool = true) {

		if($bool){

			$this->form_endpoint = 'unsubscribe';
			$this->unsubscribe = true;
			$this->set_hash();

		}else{

			$this->form_endpoint = 'subscribe';
			$this->unsubscribe = false;
			$this->hash = NULL;

		}

	}

	private function set_hash($hash = NULL) {
		$this->hash = is_null($hash) ? $this->get_hash() : $hash;
	}

	private function get_hash() {

		if(isset($_COOKIE['mymail'])) return $_COOKIE['mymail'];
		if(!$this->unsubscribe && is_user_logged_in() && ($subscriber = mymail('subscribers')->get_by_wpid(get_current_user_id()))){
			return $subscriber->hash;
		}

		return NULL;

	}

	private function unsubscribe_form() {

		$campaign = $this->campaignID ? mymail('campaigns')->get($this->campaignID) : NULL;

		$subscriber = $this->hash ? mymail('subscribers')->get_by_hash($this->hash) : NULL;

		$single_opt_out = mymail_option('single_opt_out');

		$infoclass = '';

		//instant unsubscribe
		if($subscriber && $single_opt_out){

			if(mymail('subscribers')->unsubscribe($subscriber->ID, $this->campaignID)){
				$infoclass = ' success';
				$this->message = '<p>'.mymail_text('unsubscribe').'</p>';
				$this->form_endpoint = 'resubscribe';
			}else{
				$infoclass = ' error';
				$this->message = '<p>'.mymail_text('unsubscribeerror').'</p>';
			}


		}

		$html = '';

		$html .= $this->get_styles();

		$action = 'mymail_form_unsubscribe';

		$html .= '<form action="'.$this->get_form_action($action).'" method="post" class="mymail-form mymail-form-submit mymail-ajax-form" id="mymail-form-unsubscribe">'."\n";
		$html .= '<div class="mymail-form-info '.$infoclass.'">';
		//$html .= $this->get_message();
		$html .= $this->message;
		$html .= '</div>';
		$html .= '<input name="hash" type="hidden" value="'.$this->hash.'">';
		$html .= '<input name="campaign" type="hidden" value="'.$this->campaignID.'">';
		$html .= '<div class="mymail-form-fields">';
		if(!$this->hash){

			$html .= '<div class="mymail-wrapper mymail-email-wrapper"><label for="mymail-email">'.mymail_text('email', __('Email', 'mymail')).' <span class="mymail-required">*</span></label>';
			$html .= '<input id="mymail-email" class="input mymail-email mymail-required" name="email" type="email" value=""></div>';

		}
		if($subscriber && $single_opt_out){
		}else{
			$buttontext = mymail_text('unsubscribebutton', __('Unsubscribe', 'mymail'));
			$html .= '<div class="mymail-wrapper mymail-submit-wrapper form-submit"><input name="submit" type="submit" value="'.$buttontext.'" class="submit-button button"></div>';
			$html .= '</div>';
		}
		$html .= '</form>';

		return apply_filters('mymail_unsubscribe_form', $html, $this->campaignID);
	}


	public function submit( ) {

		global $wp;

		$_BASE = $_POST;

		if(empty($_BASE)){
			wp_die('no data');
		};

		$submissiontype = isset($wp->query_vars['_mymail']) ? $wp->query_vars['_mymail'] : NULL;

		if(!$submissiontype){
			wp_die('wrong submissiontype');
		};

		if($this->honeypot){
			$honeypotnonce = wp_create_nonce('honeypot');
			$honeypot = isset($_BASE['n_'.$honeypotnonce.'_email']) ? $_BASE['n_'.$honeypotnonce.'_email'] : NULL;

			if(!empty($honeypot)) die(1);
		}

		$baselink = get_permalink( mymail_option('homepage') );
		if(!$baselink) $baselink = site_url();

		$referer = isset($_BASE['_referer']) ? $_BASE['_referer'] : $baselink;
		if($referer == 'extern' || isset($_GET['_extern'])) $referer = esc_url($_SERVER['HTTP_REFERER']);

		$now = time();

		$this->id(isset($_BASE['formid']) ? intval($_BASE['formid']) : 1);

		$double_opt_in = $this->form->doubleoptin;
		$overwrite = $this->form->overwrite;

		$customfields = mymail()->get_custom_fields();

		$formdata = isset($_BASE['userdata']) ? $_BASE['userdata'] : $_BASE;
		$formdata = apply_filters('mymail_pre_submit', $formdata, $this->form );

		foreach ($this->form->fields as $field_id => $field){

			$type = isset($customfields[$field_id]) ? $customfields[$field_id]['type'] : 'textfield';

			$value = isset($formdata[$field_id]) ? $formdata[$field_id] : '';

			$this->object['userdata'][$field_id] = ($type == 'textarea' ? stripslashes($value) : sanitize_text_field($value));

			if (($field_id == 'email' && !mymail_is_email(trim($this->object['userdata'][$field_id])))
				|| (!$this->object['userdata'][$field_id] && in_array($field_id, $this->form->required))) {
				$this->object['errors'][$field_id] = $field->error_msg;
			}

		}

		$this->object['userdata']['email'] = trim($this->object['userdata']['email']);

		$this->object['lists'] = $this->form->userschoice
			? (isset($_POST['lists'])
				? (array) $_POST['lists'] : array())
			: $this->form->lists;


		//to hook into the system
		$this->object = apply_filters('mymail_submit', $this->object);
		$this->object = apply_filters('mymail_submit_'.$this->ID, $this->object);

		if ($this->valid()) {

			$email = $this->object['userdata']['email'];

			$entry = wp_parse_args(array(
				'lang' => mymail_get_lang(),
			), $this->object['userdata']);

			switch($submissiontype){

				case 'subscribe':

					$entry = wp_parse_args(array(
						'signup' => $now,
						'confirm' => $double_opt_in ? 0 : $now,
						'status' => $double_opt_in ? 0 : 1,
						'lang' => mymail_get_lang(),
						'referer' => $referer,
						'form' => $this->ID,
					), $this->object['userdata']);

					if($overwrite && $subscriber = mymail('subscribers')->get_by_mail($entry['email'])){
						$entry = wp_parse_args(array(
							//change status if other than pending, subscribed or unsubscribed
							'status' => $subscriber->status >= 3 ? 2 : $subscriber->status,
							'ID' => $subscriber->ID,
						), $entry);

						$subscriber_id = mymail('subscribers')->update($entry, true, true);
						$message = $entry['status'] == 0 ? mymail_text('confirmation') : mymail_text('profile_update');

					}else{

						$subscriber_id = mymail('subscribers')->add($entry);
						$message = $double_opt_in ? mymail_text('confirmation') : mymail_text('success');

					}

					break;

				case 'update':

					$this->set_hash();
					$subscriber = mymail('subscribers')->get_by_hash($this->hash, true);
					$entry = wp_parse_args(array(
						//change status if other than pending, subscribed or unsubscribed
						'status' => $subscriber->status >= 3 ? 2 : $subscriber->status,
						'ID' => $subscriber->ID,
					), $entry);

					$subscriber_id = mymail('subscribers')->update($entry, true, true);

					$message = $entry['status'] == 0 ? mymail_text('confirmation') : mymail_text('profile_update');

					break;
			}

			if(is_wp_error( $subscriber_id )){

				$error_code = $subscriber_id->get_error_code();

				switch ($error_code) {

					case 'email_exists':

					if($exists = mymail('subscribers')->get_by_mail($this->object['userdata']['email'])){

						$this->object['errors']['email'] = mymail_text('already_registered');

						if($exists->status == 0){
							$this->object['errors']['confirmation'] = mymail_text('new_confirmation_sent');
							mymail('subscribers')->send_confirmations($exists->ID, true, true);

						}else if($exists->status == 1){

						//change status to "pending" if user is other than subscribed
						}else if($exists->status != 1){
							if($double_opt_in){
								$this->object['errors']['confirmation'] = mymail_text('new_confirmation_sent');
								mymail('subscribers')->change_status($exists->ID, 0, true);
								mymail('subscribers')->send_confirmations($exists->ID, true, true);
							}else{
								mymail('subscribers')->change_status($exists->ID, 1, true);
							}
						}

						mymail('subscribers')->assign_lists($exists->ID, $this->object['lists'], $submissiontype == 'update');

					}

					break;

					default:

						$field = isset($entry[$error_code]) ? $error_code : '_';
						$this->object['errors'][$field] = $subscriber_id->get_error_message();

					break;

				}

			}else{

				mymail('subscribers')->assign_lists($subscriber_id, $this->object['lists'], $submissiontype == 'update');

				$target = add_query_arg(array(
					'subscribe' => ''
				), $baselink);

			}

			$this->object = apply_filters('mymail_post_submit', $this->object);
			$this->object = apply_filters('mymail_post_submit_'.$this->ID, $this->object);


			if($this->valid()){
				$return = array(
					'success' => true,
					'html' => '<p>'.$message.'</p>'
				);
			}else{
				$return = array(
					'success' => false,
					'fields' => $this->object['errors'],
					'html' => '<p>'.$this->get_message('errors', true).'</p>',
				);
			}

			if($this->form->redirect) $return = wp_parse_args(array('redirect' => $this->form->redirect), $return);


		//an error occurred
		} else {

			$return = array(
				'success' => false,
				'fields' => $this->object['errors'],
				'html' => $this->get_message()
			);

		}

		//ajax request
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) :

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;

		endif;


		if($this->is_extern()){

			if(!$return['success']){
				wp_die($return['html'].'<a href="javascript:history.back()">'.__('Go back', 'mymail').'</a>');
				exit;
			}

			$target = isset($return['redirect']) ? $return['redirect'] : esc_url($_SERVER['HTTP_REFERER']);

		}else{

			if(!$return['success']){
				wp_die($return['html'].'<a href="javascript:history.back()">'.__('Go back', 'mymail').'</a>');
				exit;
			}

			$target = isset($return['redirect']) ? $return['redirect'] : esc_url($_SERVER['HTTP_REFERER']);

		}

		wp_redirect($target);
		exit;

	}


	public function update( ) {

		$baselink = get_permalink( mymail_option('homepage') );
		if(!$baselink) $baselink = site_url();

		$_BASE = $_POST;

		if(empty($_BASE)){
			wp_die('no data');
		};

		$referer = isset($_BASE['_referer']) ? $_BASE['_referer'] : $baselink;
		$redirect = isset($_BASE['_redirect']) ? $_BASE['_redirect'] : $baselink;

		$now = time();

		$form_id = mymail_option('profile_form', 0);
		$form = mymail('forms')->get($form_id);

		$customfields = mymail()->get_custom_fields();
		$subscriber = mymail('subscribers')->get_by_hash($_BASE['hash'], true);

		foreach ($form->fields as $field){

			$value = esc_attr(isset($_BASE[$field->field_id])
						? $_BASE[$field->field_id]
						: (isset($_BASE['userdata'][$field->field_id]) ? $_BASE['userdata'][$field->field_id] : ''));


			$this->object['userdata'][$field->field_id] = ($type == 'textarea' ? stripslashes($value) : sanitize_text_field($value));

			if (($field->field_id == 'email' && !mymail_is_email(trim($this->object['userdata'][$field->field_id]))) || (!$this->object['userdata'][$field->field_id] && in_array($field->field_id, $form->required))) {
				$this->object['errors'][$field->field_id] = mymail_text($field->field_id, isset($customfields[$field->field_id]['name']) ? $customfields[$field->field_id]['name'] : $field->name);
			}

		}

		$this->object['userdata']['email'] = trim($this->object['userdata']['email']);

		$this->object['userdata'] = $this->object['userdata'];

		$this->object['lists'] = isset($_BASE['lists']) ? (array) $_BASE['lists'] : array();

		$this->object = apply_filters('mymail_submit', $this->object);
		$this->object = apply_filters('mymail_submit_'.$form_id, $this->object);

		$this->object['userdata']['ID'] = $subscriber->ID;

		if ($this->valid()) {
			$email = $this->object['userdata']['email'];

			$this->object['userdata']['updated'] = $now;

			//change status if other than pending, subscribed or unsubscribed
			if($subscriber->status >= 3)
				$this->object['userdata']['status'] = 2;

			$subscriber_id = mymail('subscribers')->update($this->object['userdata'], true, true);

			if(is_wp_error( $subscriber_id )){

				$this->object['errors']['confirmation'] = $subscriber_id->get_error_message();

			}else{

				if(isset($form->userschoice))
					mymail('subscribers')->assign_lists($subscriber_id, $this->object['lists'], true);

				$target = add_query_arg(array(
					'subscribe' => ''
				), $baselink);

			}

			$this->object = apply_filters('mymail_post_submit', $this->object);
			$this->object = apply_filters('mymail_post_submit_'.$form_id, $this->object);

			//redirect if no ajax request
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {


				$target = (!empty($form->redirect))
					? $form->redirect
					: add_query_arg(array('mymail_success' => $double_opt_in+1), $redirect);


				wp_redirect(apply_filters('mymail_profile_update_target', $target, $form_id));
				exit;

			} else {

				if($this->valid()){
					$return = array(
						'success' => true,
						'html' => '<p>'.mymail_text('profile_update').'</p>'
					);
				}else{
					$return = array(
						'success' => false,
						'fields' => $this->object['errors'],
						'html' => '<p>'.$this->get_message('errors', true).'</p>',
					);
				}

				@header( 'Content-type: application/json' );
				echo json_encode($return);
				exit;

			}

			//redirect if no ajax request or extern
			return $target;

		//an error occurred
		} else {

			$return = array(
				'success' => false,
				'fields' => $this->object['errors'],
				'html' => $this->get_message()
			);

			//stop if no ajax request
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

				wp_die($return['html'].'<a href="javascript:history.back()">'.__('Go back', 'mymail').'</a>');
				exit;

			}

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;

		}

	}

	public function unsubscribe( ) {

		$return['success'] = false;

		$_BASE = $_POST;

		if(empty($_BASE)){
			wp_die('no data');
		};

		$campaign_id = !empty($_BASE['campaign']) ? intval($_BASE['campaign']) : NULL;

		if(isset($_BASE['email'])){
			$return['success'] = mymail('subscribers')->unsubscribe_by_mail($_BASE['email'], $campaign_id);
		}else if(isset($_BASE['hash'])){
			$return['success'] = mymail('subscribers')->unsubscribe_by_hash($_BASE['hash'], $campaign_id);
		}else{
			//wp_redirect(mymail()->get_unsubscribe_link());
			//exit;
		}

		//redirect if no ajax request
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

			$return['html'] = $return['success']
				? mymail_text('unsubscribe')
				: (empty($_POST['email'])
					? mymail_text('enter_email')
					: mymail_text('unsubscribeerror'));

			@header( 'Content-type: application/json' );
			echo json_encode($return);
			exit;

		}else{

			if($return['success']){
				wp_die($return['html'].'<a href="javascript:history.back()">'.mymail_text('unsubscribe').'</a>');
			}else{
				wp_die($return['html'].'<a href="javascript:history.back()">'.(empty($_POST['email']) ? mymail_text('enter_email')	: mymail_text('unsubscribeerror')).'</a>');
			}
			exit;

		}

	}

	public function strip_css($css) {
		$css = strip_tags($css);
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		$css = trim(str_replace(array("\r\n", "\r", "\n", "\t", '   ', '  '), '', $css));
		$css = str_replace(' {', '{', $css);
		$css = str_replace('{ ', '{', $css);
		$css = str_replace(' }', '}', $css);
		$css = str_replace('}', '}'."\n", $css);
		return $css;
	}


	private function get_form_action($action = '') {

		$is_permalink = mymail('helper')->using_permalinks();

		$prefix = !mymail_option('got_url_rewrite') ? '/index.php' : '/';

		return $is_permalink
			? home_url($prefix.'/mymail/'.$this->form_endpoint)
			: add_query_arg(array('action' => $action), admin_url('admin-ajax.php', $this->scheme));

	}


	private function get_message($type = 'errors', $simple = false) {

		$html = '';
		if (!empty($this->object[$type])) {
			if(!$simple && $type == 'errors') $html .= '<p>'.mymaiL_text('error').'</p>';
			$html .= '<ul>';
			foreach ($this->object[$type] as $field => $name) {
				$html .= '<li>'.apply_filters('mymail_error_output_'.$field, $name, $this->object).'</li>';
			}
			$html .= '</ul>';
		}

		return $html;
	}


	private function is_extern() {
		return (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != parse_url(home_url(), PHP_URL_HOST));
	}

	private function valid() {
		return empty($this->object['errors']);
	}

	static function print_script() {

		if ( self::$add_script )
			return;

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script('mymail-form', MYMAIL_URI . 'assets/js/form'.$suffix.'.js', apply_filters('mymail_no_jquery', array('jquery')), MYMAIL_VERSION, true);
		wp_register_script('mymail-form-placeholder', MYMAIL_URI . 'assets/js/placeholder-fix'.$suffix.'.js', apply_filters('mymail_no_jquery', array('jquery')), MYMAIL_VERSION, true);

		global $is_IE;

		if ( $is_IE ){
			wp_print_scripts('jquery');
			echo '<!--[if lte IE 9]>';
			wp_print_scripts('mymail-form-placeholder');
			echo '<![endif]-->';
		}

		wp_print_scripts('mymail-form');

		self::$add_script = true;


	}

	static function print_style($embed = true) {

		if ( self::$add_style )
			return;

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style('mymail-form-default', MYMAIL_URI . 'assets/css/form-default-style'.$suffix.'.css', array(), MYMAIL_VERSION);

		($embed)
			? mymail('helper')->wp_print_embedded_styles('mymail-form-default')
			: wp_print_styles('mymail-form-default');

		self::$add_style = true;


	}

	//deprecated methods
	public function get($form_id = 0) {

		_deprecated_function( __FUNCTION__, '2.1', "mymail('forms')->get()" );

		$return = mymail('helper')->object_to_array(mymail('forms')->get($form_id));

		$return['id'] = $return['ID'];

		return $return;

	}

	public function get_all($option = NULL) {

		_deprecated_function( __FUNCTION__, '2.1', "mymail('forms')->get_all()" );

		$forms = mymail('helper')->object_to_array(mymail('forms')->get_all());
		foreach($forms as $i => $form){
			$forms[$i]['id'] = $form['ID'];
		}

		return $forms;

	}

	public function set($form_id = 0, $key, $value) {

		_deprecated_function( __FUNCTION__, '2.1', "mymail('forms')->update_field()" );

		$return = mymail('forms')->update_field($form_id, $key, $value);

		$return['id'] = $return['ID'];

		return $return;

	}

	public function assign_list($form_id, $list_id) {

		_deprecated_function( __FUNCTION__, '2.1', "mymail('forms')->assign_lists()" );

		$return = mymail('forms')->assign_lists($form_id, $list_id);

		$return['id'] = $return['ID'];

		return $return;

	}

	public function unassign_list($form_id, $list_id) {

		_deprecated_function( __FUNCTION__, '2.1', "mymail('forms')->unassign_lists()" );

		$return = mymail('forms')->unassign_lists($form_id, $list_id);

		$return['id'] = $return['ID'];

		return $return;

	}

}
