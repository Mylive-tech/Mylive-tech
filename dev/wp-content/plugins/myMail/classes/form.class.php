<?php if (!defined('ABSPATH')) die('not allowed');

class mymail_form {

	private $values = array();
	private $scheme = 'http';
	private $object = array(
		'userdata' => array(),
		'lists' => array(),
		'errors' => array(),
	);
	private $errors = array();
	private $lists = array();
	private $message = '';
	static $add_script = false;

	public function __construct( ) {
		$this->scheme =  is_ssl() ? 'https' : 'http' ;
	}

	public function get($form_id = 0) {
		$forms = $this->get_all();

		return isset($forms[$form_id]) ? $forms[$form_id] : $forms[0];
	}

	public function get_all($option = NULL) {
		$forms = mymail_option('forms', array(array(
			'name' => __('Default Form', 'mymail'),
			'profile_form' => true,
			'order' => array(
				'email', 'firstname', 'lastname',
			),
			'required' => array(
				'email'
			)
		)));

		return (is_null($option) ? $forms : wp_list_pluck( $forms, $option ));

	}

	public function set($form_id = 0, $key, $value) {

		$forms = $this->get_all();

		if(!isset($forms[$form_id])) return false;

		$forms[$form_id][$key] = $value;

		return mymail_update_option('forms', $forms);
	}

	public function assign_list($form_id, $list_id) {

		$form = $this->get($form_id);

		if(!isset($form['lists'])) $form['lists'] = array();

		if(!in_array($list_id, $form['lists'])){
			array_push($form['lists'], $list_id);
			$this->set($form_id, 'lists', $form['lists']);
		}

	}

	public function unassign_list($form_id, $list_id) {

		$form = $this->get($form_id);

		if(empty($form['lists'])) return;

		if(($key = array_search($list_id, $form['lists'])) !== false) {
			unset($form['lists'][$key]);
			$this->set($form_id, 'lists', $form['lists']);
		}

	}


	public function form($form_id = 0, $tabindex = 100, $classes = '') {

		self::$add_script = true;
		add_action('wp_footer', array( &$this, 'print_script'));
		add_action('admin_footer', array( &$this, 'print_script'));

		global $mymail_form_tabstop, $pagenow;

		$tabindex = $mymail_form_tabstop ? $mymail_form_tabstop : $tabindex;

		$cache = true;
		$msg_id = 0;
		$forms = mymail_option('forms');
		$is_settings_page = is_admin() && $pagenow == 'options-general.php';

		$form_id = (isset($forms[$form_id])) ? (int) $form_id : 0;
		$form = $forms[$form_id];

		if(isset($form['prefill']) && !$is_settings_page){

			$current_user = wp_get_current_user();
			if($current_user->ID != 0){
				$this->object['userdata']['email'] = $current_user->user_email;
				$this->object['userdata']['firstname'] = get_user_meta( $current_user->ID, 'first_name', true );
				$this->object['userdata']['lastname'] = get_user_meta( $current_user->ID, 'last_name', true );
				if (!$this->object['userdata']['firstname']) $this->object['userdata']['firstname'] = $current_user->display_name;
				$cache = false;

			}
		}

		if(isset($_GET['mymail_error']) && ($_GET['id'] == $form_id || isset($_GET['extern'])) && !$is_settings_page){

			$transient = 'mymail_error_'.esc_attr($_GET['mymail_error']);
			$data = get_transient($transient);
			if($data){
				$this->object['userdata'] = $data['userdata'];
				$this->object['errors'] = $data['errors'];
				$this->object['lists'] = $data['lists'];

				$cache = false;
				delete_transient($transient);
			}
		}

		if(isset($_GET['mymail_success']) && ($_GET['id'] == $form_id || isset($_GET['extern'])) && !$is_settings_page){

			$msg_id = intval($_GET['mymail_success']);

			if($msg_id == 1){
				$this->message = '<p>'.mymail_text('success').'</p>';
			}else if($msg_id == 2){
				$this->message = '<p>'.mymail_text('confirmation').'</p>';
			}

			$cache = false;
		}

		$nonce = wp_create_nonce('mymail_nonce');
		$transient = 'mymail_form'.$form_id;

		$html = '';

		$customfields = mymail()->get_custom_fields();

		$inline = isset($form['inline']);
		$asterisk = isset($form['asterisk']);

		$html .= '<form action="'.admin_url('admin-ajax.php', $this->scheme).'" method="post" class="mymail-form mymail-form-submit mymail-form-'.$form_id.' '.(mymail_option('ajax_form') && !$is_settings_page ? 'mymail-ajax-form ' : '').''.esc_attr($classes).'">';

		$html .= '<div class="mymail-form-info '.(!empty($this->object['errors']) ? 'error' :'success').'"'.(!empty($this->object['errors']) || !empty($this->message) ? ' style="display:block"' : '').'>';
		$html .= $this->get_error_html();
		$html .= $this->message;
		$html .= '</div>';
		if(!$is_settings_page){

			$redirect = remove_query_arg(array('mymail_error', 'mymail_success'), $_SERVER['REQUEST_URI']);
			global $pagenow;

			$referer = $pagenow == 'form.php' ? (isset($_GET['referer']) ? $_GET['referer'] : 'extern') : $redirect;

			$html .= '<input name="_wpnonce" type="hidden" value="'.$nonce.'">';
			$html .= '<input name="_redirect" type="hidden" value="'.esc_attr($redirect).'">';
			$html .= '<input name="_referer" type="hidden" value="'.esc_attr($referer).'">';

		}else{
			$html .= '<input name="_extern" type="hidden" value="1">';
		}
		$html .= '<input name="action" type="hidden" value="mymail_form_submit">';
		$html .= '<input name="formid" type="hidden" value="'.$form_id.'">';

		if ( false === ( $fields = get_transient( $transient ) ) || !$cache ) {

			$fields = array();

			foreach($form['order'] as $field){

				$required = in_array($field, $form['required']);

				$label = isset($form['labels'][$field]) ? $form['labels'][$field] : mymail_text($field);
				$esc_label = esc_attr(strip_tags($label));

				switch($field){


					case 'email':

						$fields['email'] = '<div class="mymail-wrapper mymail-email-wrapper'.(isset($this->object['errors']['email']) ? ' error' : '').'">';
						if(!$inline) $fields['email'] .= '<label for="mymail-email-'.$form_id.'">'.$label.' '.($asterisk ? '<span class="required">*</span>' : '').'</label>';
						$fields['email'] .= '<input id="mymail-email-'.$form_id.'" name="userdata[email]" type="text" value="'.(isset($this->object['userdata']['email']) ? $this->object['userdata']['email'] : '').'"'.($inline ? ' placeholder="'.$esc_label.($asterisk ? ' *' : '').'"' : '').' class="input mymail-email required" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
						$fields['email'] .= '</div>';

					break;


					case 'firstname':

						$fields['firstname'] = '<div class="mymail-wrapper mymail-firstname-wrapper'.(isset($this->object['errors']['firstname']) ? ' error' : '').'">';
						if(!$inline) $fields['firstname'] .= '<label for="mymail-firstname-'.$form_id.'">'.$label.($required && $asterisk ? ' <span class="required">*</span>' : '').'</label>';
						$fields['firstname'] .= '<input id="mymail-firstname-'.$form_id.'" name="userdata[firstname]" type="text" value="'.(isset($this->object['userdata']['firstname']) ? $this->object['userdata']['firstname'] : '').'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-firstname'.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
						$fields['firstname'] .= '</div>';

					break;

					case 'lastname':

						$fields['lastname'] = '<div class="mymail-wrapper mymail-lastname-wrapper'.(isset($this->object['errors']['lastname']) ? ' error' : '').'">';
						if(!$inline) $fields['lastname'] .= '<label for="mymail-lastname-'.$form_id.'">'.$label.($required && $asterisk ? ' <span class="required">*</span>' : '').'</label>';
						$fields['lastname'] .= '<input id="mymail-lastname-'.$form_id.'" name="userdata[lastname]" type="text" value="'.(isset($this->object['userdata']['lastname']) ? $this->object['userdata']['lastname'] : '').'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-lastname'.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
						$fields['lastname'] .= '</div>';

					break;

					//custom fields
					default:

					if(!isset($customfields[$field])) break;
					$data = $customfields[$field];

					$label = isset($form['labels'][$field]) ? $form['labels'][$field] : $data['name'];
					$esc_label = esc_attr(strip_tags($label));

					$fields[$field] = '<div class="mymail-wrapper mymail-'.$field.'-wrapper'.(isset($this->object['errors'][$field]) ? ' error' : '').'">';

					$showlabel = !$inline;

					switch($data['type']){

						case 'dropdown':
						case 'radio': $showlabel = true;
							break;

						case 'checkbox': $showlabel = false;
							break;
					}

					if($showlabel){

						$fields[$field] .= '<label for="mymail-'.$field.'-'.$form_id.'">'.$label;
						if ($required && $asterisk) $fields[$field] .= ' <span class="required">*</span>';
						$fields[$field] .= '</label>';

					}


					switch($data['type']){

						case 'dropdown':

							$fields[$field] .= '<select id="mymail-'.$field.'-'.$form_id.'" name="userdata['.$field.']" class="input mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
						foreach($data['values'] as $v){
							if(!isset($data['default'])) $data['default'] = false;
							$fields[$field] .= '<option value="'.$v.'" '.(isset($data['default']) ? selected($data['default'], (isset($this->object['userdata'][$field]) ? $this->object['userdata'][$field] : $v), false) : '').'>'.$v.'</option>';
						}
							$fields[$field] .= '</select>';
							break;

						case 'radio':

						$fields[$field] .= '<ul class="mymail-list">';
						$i = 0;
						foreach($data['values'] as $v){
							$fields[$field] .= '<li><label><input id="mymail-'.$field.'-'.$form_id.'-'.($i++).'" name="userdata['.$field.']" type="radio" value="'.$v.'" class="radio mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" '.(isset($data['default']) ? checked($data['default'], (isset($this->object['userdata'][$field]) ? $this->object['userdata'][$field] : $v), false) : '').' aria-label="'.$v.'"> '.$v.'</label></li>';
						}
						$fields[$field] .= '</ul>';
							break;

						case 'checkbox':

							$fields[$field] .= '<label for="mymail-'.$field.'-'.$form_id.'">';
							$fields[$field] .= '<input id="mymail-'.$field.'-'.$form_id.'" name="userdata['.$field.']" type="checkbox" value="1" '.((isset($this->object['userdata'][$field]) || isset($data['default']) ) ? ' checked' : '').' class="mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'"> ';
							$fields[$field] .= ' '.$label;
							if ($required && $asterisk) $fields[$field] .= ' <span class="required">*</span>';
							$fields[$field] .= '</label>';

							break;

						case 'date':

							$fields[$field] .= '<input id="mymail-'.$field.'-'.$form_id.'" name="userdata['.$field.']" type="text" value="'.(isset($this->object['userdata'][$field]) ? $this->object['userdata'][$field] : '').'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input input-date datepicker mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';

							break;
						default:
							$fields[$field] .= '<input id="mymail-'.$field.'-'.$form_id.'" name="userdata['.$field.']" type="text" value="'.(isset($this->object['userdata'][$field]) ? $this->object['userdata'][$field] : '').'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
					}

					$fields[$field] .= '</div>';

				}

			}


			if (isset($form['userschoice']) && $form['userschoice']) {
				$fields['lists'] = '<div class="mymail-wrapper mymail-lists-wrapper"><label>'.mymail_text('lists', __('Lists', 'mymail')).'</label>';

				$lists = mymail('lists')->get();

				if (isset($form['dropdown']) && $form['dropdown']) {
					$fields['lists'] .= '<br><select name="lists[]">';
					foreach ($lists as $list) {
						$selected = !empty($this->object['errors']) && in_array($list->ID, $this->object['lists']);

						if (in_array($list->ID, $form['lists'])) $fields['lists'] .= '<option value="'.$list->ID.'"'.selected( $selected, true, false ).'> '.$list->name.'</option>';
					}
					$fields['lists'] .= '</select>';
				}else{
					$fields['lists'] .= '<ul class="mymail-list">';
					foreach ($lists as $list) {
						if (in_array($list->ID, $form['lists'])){

							$checked = (empty($this->object['errors']) && isset($form['precheck'])) || (!empty($this->object['errors']) && in_array($list->ID, $this->object['lists']));

							$fields['lists'] .= '<li><label title="'.$list->description.'"><input class="mymail-list mymail-list-'.$list->slug.'" type="checkbox" name="lists[]" value="'.$list->ID.'" '.checked( $checked, true, false ).' aria-label="'.esc_attr($list->name).'"> '.$list->name;
							if(!empty($list->description)) $fields['lists'] .= ' <span class="mymail-list-description mymail-list-description-'.$list->slug.'">'.$list->description.'</span>';
							$fields['lists'] .= '</label></li>';
						}
					}
					$fields['lists'] .= '</ul>';
				}

				$fields['lists'] .= '</div>';
			}

			$label = esc_attr(strip_tags(!empty($form['submitbutton']) ? $form['submitbutton'] : mymail_text('submitbutton', __('Subscribe', 'mymail'))));

			$fields['_submit'] = '<div class="mymail-wrapper mymail-submit-wrapper form-submit"><input name="submit" type="submit" value="'.$label.'" class="submit-button button" tabindex="'.($tabindex++).'" aria-label="'.$label.'"><span class="mymail-loader"></span></div>';

			if($cache) set_transient( $transient, $fields );

		}

		//global
		$mymail_form_tabstop = $tabindex;

		$fields = apply_filters('mymail_form_fields', $fields, $form_id, $form);

		// if(false){
		// 	$position = rand(count($fields), 0)-1;
		// 	$fields = array_slice($fields, 0, $position, true) +
		// 	array('_honeypot' => '<label><input name="n_'.wp_create_nonce( 'honeypot' ).'"></label>') +
		// 	array_slice($fields, $position, NULL, true);
		// }

		$html .= "\n".implode("\n", $fields)."\n";

		$html .= '</form>';


		return apply_filters('mymail_form', $html, $form_id, $form);
	}



	public function profile($hash = NULL, $tabindex = 100, $classes = '') {

		add_action('wp_footer', array( &$this, 'print_script'));

		self::$add_script = true;

		global $mymail_form_tabstop;
		$tabindex = $mymail_form_tabstop ? $mymail_form_tabstop : $tabindex;


		if(isset($_GET['mymail_error'])){

			$transient = 'mymail_error_'.esc_attr($_GET['mymail_error']);
			$data = get_transient($transient);
			if($data){
				$this->object['userdata'] = $data['userdata'];
				$this->object['errors'] = $data['errors'];
				$this->object['lists'] = $data['lists'];

				delete_transient($transient);
			}
		}

		if(isset($_GET['mymail_success'])){

			$msg_id = intval($_GET['mymail_success']);

			if($msg_id == 1){
				$this->message = '<p>'.mymail_text('profile_update').'</p>';
			}

		}

		$userhash = isset($_COOKIE['mymail']) ? $_COOKIE['mymail'] : NULL;
		$nonce = wp_create_nonce('mymail_nonce');

		if(!$userhash){
			if(is_user_logged_in() && ($subscriber = mymail('subscribers')->get_by_wpid(get_current_user_id()))){
				$userhash = $subscriber->hash;
				$hash = md5($nonce.$userhash);
			}
		}

		if(!$userhash && $hash != md5($nonce.$userhash)){
			_e('Session expired! Please click the link in your email again!', 'mymail');
			return;
		}

		$subscriber = mymail('subscribers')->get_by_hash($userhash, true);

		$html = '';

		$customfields = mymail()->get_custom_fields();
		$customfields_names = mymail()->get_custom_fields(true);
		$form_id = mymail_option('profile_form', 0);
		$form = $this->get($form_id);

		$inline = isset($form['inline']);
		$asterisk = isset($form['asterisk']);

		$html .= '<form action="'.admin_url('admin-ajax.php', $this->scheme).'" method="post" class="mymail-form mymail-form-submit mymail-form-'.$form_id.' '.(mymail_option('ajax_form') ? 'mymail-ajax-form ' : '').''.esc_attr($classes).'">';

		$html .= '<div class="mymail-form-info '.(!empty($this->object['errors']) ? 'error' :'success').'"'.(!empty($this->object['errors']) || !empty($this->message) ? ' style="display:block"' : '').'>';
		$html .= $this->get_error_html();
		$html .= $this->message;
		$html .= '</div>';
		$html .= '<input name="_wpnonce" type="hidden" value="'.$nonce.'">';

		$referer = remove_query_arg(array('mymail_error', 'mymail_success'), $_SERVER['REQUEST_URI']);
		$html .= '<input name="_redirect" type="hidden" value="'.esc_attr($referer).'">';
		$html .= '<input name="_referer" type="hidden" value="'.esc_attr($referer).'">';

		$html .= '<input name="action" type="hidden" value="mymail_profile_submit">';
		$html .= '<input name="hash" type="hidden" value="'.$userhash.'">';

		$fields = array();


		foreach($form['order'] as $field){

			$required = in_array($field, $form['required']);

			$label = isset($form['labels'][$field]) ? $form['labels'][$field] : mymail_text($field);
			$esc_label = esc_attr(strip_tags($label));

			switch($field){


				case 'email':

					$fields['email'] = '<div class="mymail-wrapper mymail-email-wrapper'.(isset($this->object['errors']['email']) ? ' error' : '').'">';
					if(!$inline) $fields['email'] .= '<label for="mymail-email-'.$form_id.'">'.$label.' '.($asterisk ? '<span class="required">*</span>' : '').'</label>';
					$fields['email'] .= '<input id="mymail-email-'.$form_id.'" name="userdata[email]" type="text" value="'.$subscriber->email.'"'.($inline ? ' placeholder="'.$esc_label.($asterisk ? ' *' : '').'"' : '').' class="input mymail-email required" tabindex="'.($tabindex++).'" aria-required="true" aria-label="'.$esc_label.'">';
					$fields['email'] .= '</div>';

				break;


				case 'firstname':

					$fields['firstname'] = '<div class="mymail-wrapper mymail-firstname-wrapper'.(isset($this->object['errors']['firstname']) ? ' error' : '').'">';
					if(!$inline) $fields['firstname'] .= '<label for="mymail-firstname-'.$form_id.'">'.$label.($required && $asterisk ? ' <span class="required">*</span>' : '').'</label>';
					$fields['firstname'] .= '<input id="mymail-firstname-'.$form_id.'" name="userdata[firstname]" type="text" value="'.$subscriber->firstname.'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-firstname'.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
					$fields['firstname'] .= '</div>';

				break;

				case 'lastname':

					$fields['lastname'] = '<div class="mymail-wrapper mymail-lastname-wrapper'.(isset($this->object['errors']['lastname']) ? ' error' : '').'">';
					if(!$inline) $fields['lastname'] .= '<label for="mymail-lastname-'.$form_id.'">'.$label.($required && $asterisk ? ' <span class="required">*</span>' : '').'</label>';
					$fields['lastname'] .= '<input id="mymail-lastname-'.$form_id.'" name="userdata[lastname]" type="text" value="'.$subscriber->lastname.'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-lastname'.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
					$fields['lastname'] .= '</div>';

				break;

				//custom fields
				default:

				$data = $customfields[$field];

				$label = isset($form['labels'][$field]) ? $form['labels'][$field] : $data['name'];
				$esc_label = esc_attr(strip_tags($label));

				$fields[$field] = '<div class="mymail-wrapper mymail-'.$field.'-wrapper'.(isset($this->object['errors'][$field]) ? ' error' : '').'">';

				$showlabel = !$inline;

				switch($data['type']){

					case 'dropdown':
					case 'radio': $showlabel = true;
						break;

					case 'checkbox': $showlabel = false;
						break;
				}

				if($showlabel){

					$fields[$field] .= '<label for="mymail-'.$field.'-'.$form_id.'">'.$label;
					if ($required && $asterisk) $fields[$field] .= ' <span class="required">*</span>';
					$fields[$field] .= '</label>';

				}


				switch($data['type']){

					case 'dropdown':

						$fields[$field] .= '<select id="mymail-'.$field.'-'.$form_id.'" name="userdata['.$field.']" class="input mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'">';
					foreach($data['values'] as $v){
						if(!isset($data['default'])) $data['default'] = false;
						$fields[$field] .= '<option value="'.$v.'" '.(isset($data['default']) ? selected($subscriber->{$field}, $v, false) : '').'>'.$v.'</option>';
					}
						$fields[$field] .= '</select>';
						break;

					case 'radio':

					$fields[$field] .= '<ul class="mymail-list">';
					$i = 0;
					foreach($data['values'] as $v){
						$fields[$field] .= '<li><label><input id="mymail-'.$field.'-'.$form_id.'-'.($i++).'" name="userdata['.$field.']" type="radio" value="'.$v.'" class="radio mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" '.(isset($data['default']) ? checked($subscriber->{$field}, $v, false) : '').'> '.$v.'</label></li>';
					}
					$fields[$field] .= '</ul>';
						break;

					case 'checkbox':

						$fields[$field] .= '<label for="mymail-'.$field.'-'.$form_id.'">';
						$fields[$field] .= '<input id="mymail-'.$field.'-'.$form_id.'" name="userdata['.$field.']" type="checkbox" value="1" '.(checked($subscriber->{$field}, true, false)).' class="mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'"> ';
						$fields[$field] .= ' '.$label;
						if ($required && $asterisk) $fields[$field] .= ' <span class="required">*</span>';
						$fields[$field] .= '</label>';

						break;

					case 'date':

						$fields[$field] .= '<input id="mymail-'.$field.'-'.$form_id.'" name="userdata['.$field.']" type="text" value="'.$subscriber->{$field}.'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input input-date datepicker mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';

						break;

					default:
						$fields[$field] .= '<input id="mymail-'.$field.'-'.$form_id.'" name="userdata['.$field.']" type="text" value="'.$subscriber->{$field}.'"'.($inline ? ' placeholder="'.$esc_label.($required && $asterisk ? ' *' : '').'"' : '').' class="input mymail-'.$field.''.($required ? ' required' : '').'" tabindex="'.($tabindex++).'" aria-required="'.($required ? 'true' : 'false').'" aria-label="'.$esc_label.'">';
				}

				$fields[$field] .= '</div>';

				}




		}

		if (isset($form['userschoice']) && $form['userschoice']) {
			$fields['lists'] = '<div class="mymail-wrapper mymail-lists-wrapper"><label>'.mymail_text('lists', __('Lists', 'mymail')).'</label>';

			$lists = mymail('lists')->get();
			$userlists = mymail('subscribers')->get_lists($subscriber->ID, true);

			if (isset($form['dropdown']) && $form['dropdown']) {
				$fields['lists'] .= '<br><select name="lists[]">';
				foreach ($lists as $list) {

					$selected = !empty($this->object['errors']) && in_array($list->ID, $userlists);

					if (in_array($list->ID, $form['lists'])) $fields['lists'] .= '<option value="'.$list->ID.'"'.selected( $selected, true, false ).'> '.$list->name.'</option>';
				}
				$fields['lists'] .= '</select>';
			}else{
				$fields['lists'] .= '<ul class="mymail-list">';
				foreach ($lists as $list) {
					if (in_array($list->ID, $form['lists'])){

						$checked = empty($this->object['errors']) && in_array($list->ID, $userlists);

						$fields['lists'] .= '<li><label title="'.$list->description.'"><input class="mymail-list mymail-list-'.$list->slug.'" type="checkbox" name="lists[]" value="'.$list->ID.'" '.checked( $checked, true, false ).'> '.$list->name;
						if(!empty($list->description)) $fields['lists'] .= ' <span class="mymail-list-description mymail-list-description-'.$list->slug.'">'.$list->description.'</span>';
						$fields['lists'] .= '</label></li>';
					}
				}
				$fields['lists'] .= '</ul>';
			}

			$fields['lists'] .= '</div>';
		}

		$label = esc_attr(strip_tags(mymail_text('profilebutton', __('Update Profile', 'mymail'))));

		$fields['_submit'] = '<div class="mymail-wrapper mymail-submit-wrapper form-submit"><input name="submit" type="submit" value="'.$label.'" class="submit-button button" tabindex="'.($tabindex++).'" aria-label="'.$label.'"><span class="mymail-loader"></span></div>';

		//global
		$mymail_form_tabstop = $tabindex;

		$fields = apply_filters('mymail_profile_fields', $fields, 0, $form);

		$html .= "\n".implode("\n", $fields)."\n";

		$html .= '</form>';

		return apply_filters('mymail_profile', $html, $form);
	}


	public function unsubscribe_form($hash = '', $campaignid = '', $tabindex = 100, $classes = '') {

		add_action('wp_footer', array( &$this, 'print_script'));

		self::$add_script = true;

		global $mymail_form_tabstop;
		$tabindex = $mymail_form_tabstop ? $mymail_form_tabstop : $tabindex;

		$msg_id = 0;

		$campaign = mymail('campaigns')->get($campaignid);

		$campaignid = (!$campaign) ? NULL : $campaign->ID;

		if(isset($_GET['mymail_success'])){
			$msg_id = intval($_GET['mymail_success']);

			if($msg_id == 1){
				$this->message = '<p>'.mymail_text('unsubscribe').'</p>';
			}else if($msg_id == 2){
				$this->message = '<p>'.mymail_text('unsubscribeerror').'</p>';
			}
		}

		if(!empty($hash)){
			if(!mymail('subscribers')->get_by_hash($hash)){
				$hash = '';
			}
		}

		$html = '';

		$html .= '<form action="'.admin_url('admin-ajax.php', $this->scheme).'" method="post" class="mymail-form mymail-form-unsubscribe '.(mymail_option('ajax_form') ? 'mymail-ajax-form ' : '').''.$classes.'" id="mymail-form-unsubscribe">';
		$html .= '<div class="mymail-form-info '.($msg_id == 2 ? 'error' :'success').'"'.(!empty($this->object['errors']) || !empty($this->message) ? ' style="display:block"' : '').'>';
		$html .= $this->get_error_html();
		$html .= $this->message;
		$html .= '</div>';
		$html .= '<input name="_wpnonce" type="hidden" value="'.wp_create_nonce('mymail_nonce').'">';
		$html .= '<input name="_redirect" type="hidden" value="'.esc_attr($_SERVER['REQUEST_URI']).'">';
		$html .= '<input name="_referer" type="hidden" value="'.esc_attr($_SERVER['REQUEST_URI']).'">';
		$html .= '<input name="hash" type="hidden" value="'.$hash.'">';
		$html .= '<input name="campaign" type="hidden" value="'.$campaignid.'">';
		$html .= '<input name="action" type="hidden" value="mymail_form_unsubscribe">';
		if(empty($hash)){

			$html .= '<div class="mymail-wrapper mymail-email-wrapper"><label for="mymail-email">'.mymail_text('email', __('Email', 'mymail')).' <span class="required">*</span></label>';
			$html .= '<input id="mymail-email" class="input mymail-email required" name="email" type="text" value="" tabindex="'.($tabindex++).'"></div>';

		}
		$html .= '<div class="mymail-wrapper mymail-submit-wrapper form-submit"><input name="submit" type="submit" value="'.mymail_text('unsubscribebutton', __('Unsubscribe', 'mymail')).'" class="submit-button button" tabindex="'.($tabindex++).'"><span class="mymail-loader"></span></div>';
		$html .= '</form>';

		//global
		$mymail_form_tabstop = $tabindex;

		return apply_filters('mymail_unsubscribe_form', $html, $campaignid);
	}


	public function handle_submission( ) {

		// $honeypotnonce = wp_create_nonce('honeypot');
		// $honeypot = isset($_POST['n_'.$honeypotnonce]) ? $_POST['n_'.$honeypotnonce] : NULL;

		// if(!empty($honeypot)) die(0);

		$baselink = get_permalink( mymail_option('homepage') );
		if(!$baselink) $baselink = site_url();

		$referer = isset($_POST['_referer']) ? $_POST['_referer'] : $baselink;
		$redirect = isset($_POST['_redirect']) ? $_POST['_redirect'] : $baselink;

		$now = time();

		$form_id = isset($_POST['formid']) ? intval($_POST['formid']) : 0;
		$form = $this->get($form_id);

		$double_opt_in = $form['double_opt_in'];

		$customfields = mymail()->get_custom_fields();

		foreach ($form['order'] as $field){

			$this->object['userdata'][$field] = isset($_POST['userdata'][$field]) ? esc_attr($_POST['userdata'][$field]) : '';

			if (($field == 'email' && !mymail_is_email(trim($this->object['userdata'][$field]))) || (!$this->object['userdata'][$field] && in_array($field, $form['required']))) {
				$this->object['errors'][$field] = mymail_text($field, isset($customfields[$field]['name']) ? $customfields[$field]['name'] : $field);
			}

		}

		$this->object['userdata']['email'] = trim($this->object['userdata']['email']);

		$this->object['lists'] = isset($form['userschoice']) ? (isset($_POST['lists']) ? (array) $_POST['lists'] : array()) : (isset($form['lists']) ? $form['lists'] : array());

		//to hook into the system
		$this->object = apply_filters('mymail_submit', $this->object);
		$this->object = apply_filters('mymail_submit_'.$form_id, $this->object);

		if ($this->valid()) {
			$email = $this->object['userdata']['email'];

			$subscriber_id = mymail('subscribers')->add(wp_parse_args(array(
				'signup' => $now,
				'confirm' => $double_opt_in ? 0 : $now,
				'status' => $double_opt_in ? 0 : 1,
				'lang' => mymail_get_lang(),
				'referer' => $referer,
				'form' => $form_id,
			), $this->object['userdata']));

			if(is_wp_error( $subscriber_id )){

				if($subscriber_id->get_error_code() == 'email_exists'){

					if($exists = mymail('subscribers')->get_by_mail($this->object['userdata']['email'])){

						$this->object['errors']['email'] = __('You are already registered', 'mymail');

						if($exists->status == 0){
							$this->object['errors']['confirmation'] = __('A new confirmation message has been sent', 'mymail');
							mymail('subscribers')->send_confirmations($exists->ID, true, true);

						}else if($exists->status == 1){

						//change status to "pending" if user is other than subscribed
						}else if($exists->status != 1){
							if($double_opt_in){
								$this->object['errors']['confirmation'] = __('A new confirmation message has been sent', 'mymail');
								mymail('subscribers')->change_status($exists->ID, 0, true);
								mymail('subscribers')->send_confirmations($exists->ID, true, true);
							}else{
								mymail('subscribers')->change_status($exists->ID, 1, true);
							}
						}

						mymail('subscribers')->assign_lists($exists->ID, $this->object['lists']);

					}

				}

			}else{

				mymail('subscribers')->assign_lists($subscriber_id, $this->object['lists']);

				$target = add_query_arg(array(
					'subscribe' => ''
				), $baselink);

			}

			$this->object = apply_filters('mymail_post_submit', $this->object);
			$this->object = apply_filters('mymail_post_submit_'.$form_id, $this->object);

			//redirect if no ajax request oder extern
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['_extern'])) {


				$target = (!empty($form['redirect']))
					? $form['redirect']
					: add_query_arg(array('mymail_success' => $double_opt_in+1, 'id' => $form_id, 'extern' => isset($_POST['_extern'])), $redirect);

				wp_redirect(apply_filters('mymail_subscribe_target', $target, $form_id));

				exit();

			} else {

				if($this->valid()){
					$return = array(
						'success' => true,
						'html' => '<p>'.(($double_opt_in) ? mymail_text('confirmation') : mymail_text('success')).'</p>'
					);
				}else{
					$return = array(
						'success' => false,
						'fields' => $this->object['errors'],
						'html' => '<p>'.$this->get_error_html(true).'</p>',
					);
				}

				if(!empty($form['redirect'])) $return = wp_parse_args(array('redirect' => $form['redirect']), $return);

				return $return;

			}


			//redirect if no ajax request oder extern

			return $target;

		//an error occurred
		} else {

			//redirect if no ajax request oder extern
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['_extern'])) {

				$hash = md5(serialize($this->object));
				set_transient( 'mymail_error_'.$hash, $this->object );
				$target = add_query_arg(array('mymail_error' => $hash, 'id' => $form_id, 'extern' => isset($_POST['_extern'])), $redirect);
				wp_redirect($target);
			}

			return array(
				'success' => false,
				'fields' => $this->object['errors'],
				'html' => $this->get_error_html()
			);
		}

	}


	public function profile_update( ) {

		$baselink = get_permalink( mymail_option('homepage') );
		if(!$baselink) $baselink = site_url();

		$referer = isset($_POST['_referer']) ? $_POST['_referer'] : $baselink;
		$redirect = isset($_POST['_redirect']) ? $_POST['_redirect'] : $baselink;

		$now = time();

		$form_id = mymail_option('profile_form', 0);
		$form = $this->get($form_id);

		$customfields = mymail()->get_custom_fields();
		$subscriber = mymail('subscribers')->get_by_hash($_POST['hash'], true);

		foreach ($form['order'] as $field){

			$this->object['userdata'][$field] = isset($_POST['userdata'][$field]) ? esc_attr($_POST['userdata'][$field]) : '';

			if (($field == 'email' && !mymail_is_email(trim($this->object['userdata'][$field]))) || (!$this->object['userdata'][$field] && in_array($field, $form['required']))) {
				$this->object['errors'][$field] = mymail_text($field, isset($customfields[$field]['name']) ? $customfields[$field]['name'] : $field);
			}

		}

		$this->object['userdata']['email'] = trim($this->object['userdata']['email']);

		$this->object['userdata'] = $this->object['userdata'];

		$this->object['lists'] = isset($_POST['lists']) ? (array) $_POST['lists'] : array();

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

				if(isset($form['userschoice']))
					mymail('subscribers')->assign_lists($subscriber_id, $this->object['lists'], true);

				$target = add_query_arg(array(
					'subscribe' => ''
				), $baselink);

			}

			$this->object = apply_filters('mymail_post_submit', $this->object);
			$this->object = apply_filters('mymail_post_submit_'.$form_id, $this->object);

			//redirect if no ajax request oder extern
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['_extern'])) {


				$target = (!empty($form['redirect']))
					? $form['redirect']
					: add_query_arg(array('mymail_success' => $double_opt_in+1, 'extern' => isset($_POST['_extern'])), $redirect);

				wp_redirect(apply_filters('mymail_profile_update_target', $target, $form_id));

				exit();

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
						'html' => '<p>'.$this->get_error_html(true).'</p>',
					);
				}

				return $return;

			}


			//redirect if no ajax request oder extern
			return $target;

		//an error occurred
		} else {

			//redirect if no ajax request oder extern
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['_extern'])) {

				$hash = md5(serialize($this->object));
				set_transient( 'mymail_error_'.$hash, $this->object );
				$target = add_query_arg(array('mymail_error' => $hash, 'extern' => isset($_POST['_extern'])), $redirect);
				wp_redirect($target);
			}

			return array(
				'success' => false,
				'fields' => $this->object['errors'],
				'html' => $this->get_error_html()
			);
		}

	}


	private function get_error_html($simple = false) {

		$html = '';
		if (!empty($this->object['errors'])) {
			if(!$simple) $html .= '<p>'.mymaiL_text('error').'</p>';
			$html .= '<ul>';
			foreach ($this->object['errors'] as $field => $name) {
				$html .= '<li>'.apply_filters('mymail_error_output_'.$field, $name, $this->object).'</li>';
			}
			$html .= '</ul>';
		}

		return $html;
	}


	private function valid() {
		return empty($this->object['errors']);
	}

	static function print_script() {

		if ( !self::$add_script )
			return;

		wp_register_script('mymail-form', MYMAIL_URI . 'assets/js/form.js', apply_filters('mymail_no_jquery', array('jquery')), MYMAIL_VERSION, true);
		wp_register_script('mymail-form-placeholder', MYMAIL_URI . 'assets/js/placeholder-fix.js', apply_filters('mymail_no_jquery', array('jquery')), MYMAIL_VERSION, true);

		global $is_IE;
		if ( $is_IE ){
			wp_print_scripts('jquery');
			echo '<!--[if lte IE 9]>';
			wp_print_scripts('mymail-form-placeholder');
			echo '<![endif]-->';
		}

		if(mymail_option('ajax_form')) wp_print_scripts('mymail-form');


	}


}


?>
