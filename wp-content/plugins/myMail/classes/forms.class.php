<?php if (!defined('ABSPATH')) die('not allowed');


class mymail_forms {

	private $request = null;

	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init' ) );

	}

	public function init() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 20 );

		if (is_admin()) {

		} else {

			add_action( 'mymail_form_header', array( &$this, 'set_form_request' ) );
			add_action( 'mymail_form_head', array( &$this, 'form_head' ) );
			add_action( 'mymail_form_body', array( &$this, 'form_body' ) );
			add_action( 'mymail_form_footer', array( &$this, 'form_footer' ) );

		}

	}

	public function set_form_request() {

		global $pagenow;

		$formpage = $pagenow == 'form.php';

		$this->request = array(
			'is_editable' => isset($_GET['edit']) && wp_verify_nonce( $_GET['edit'], 'mymailiframeform' ),
			'is_embeded' => $formpage && !isset($_GET['iframe']),
			'is_button' => isset($_GET['button']),
			'is_iframe' => $formpage && (isset($_GET['iframe']) && $_GET['iframe'] == 1 && !isset($_GET['button'])),
			'use_style' => ((isset($_GET['style']) && $_GET['style'] == 1) || (isset($_GET['s']) && $_GET['s'] == 1)),
			'form_id' => (isset($_GET['id']) ? intval($_GET['id']) : 1),
			'showcount' => (isset($_GET['showcount']) ? intval($_GET['showcount']) : 0),
			'width' => (isset($_GET['width']) ? $_GET['width'] : 480),
			'buttonstyle' => (isset($_GET['design']) ? $_GET['design'] : 'default'),
			'button_id' => (isset($_GET['button']) ? intval($_GET['button']) : ''),
			'origin' => (isset($_GET['origin']) ? $_GET['origin'] : ''),
			'buttonlabel' => (isset($_GET['label']) ? esc_attr(strip_tags(urldecode($_GET['label']))) : 'Subscribe'),

		);
	}
	public function form_head() {

		extract($this->request);

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style('form-default-style', MYMAIL_URI . 'assets/css/form-default-style'.$suffix.'.css', array(), MYMAIL_VERSION);

		if($use_style){
			wp_register_style('theme-style', get_template_directory_uri().'/style.css', array(), MYMAIL_VERSION);
			wp_print_styles('theme-style');
		}
		if($is_button){

			do_action('mymail_form_head_button');
			$buttonstyle = explode(' ', $buttonstyle);

			wp_register_style('form-button-base-style', MYMAIL_URI . 'assets/css/button-style'.$suffix.'.css', array(), MYMAIL_VERSION);
			wp_register_style('form-button-style', MYMAIL_URI . 'assets/css/button-'.$buttonstyle[0].'-style'.$suffix.'.css', array('form-button-base-style'), MYMAIL_VERSION);
			//wp_print_styles('form-button-style');
			mymail('helper')->wp_print_embedded_styles('form-button-style');


		}elseif($is_editable){

			wp_print_styles('form-default-style');

		}elseif($is_embeded){

			do_action('mymail_form_head_embeded');
			wp_print_styles('form-default-style');

		}elseif($is_iframe){

			do_action('mymail_form_head_iframe');
			wp_register_style('form-iframe-style', MYMAIL_URI . 'assets/css/form-iframe-style'.$suffix.'.css', array('form-default-style'), MYMAIL_VERSION);
			mymail('helper')->wp_print_embedded_styles('form-iframe-style');
			$width = preg_match('#\d+%#', $width) ? intval($width).'%' : intval($width).'px';
			echo '<style type="text/css">.mymail-form-wrap{width:'.$width.'}</style>';

		}else{

			// wp_register_style('mymail-form', MYMAIL_URI . 'assets/css/form'.$suffix.'.css', array(), MYMAIL_VERSION);
			// wp_print_styles('mymail-form');

		}


	}
	public function form_body() {

		extract($this->request);


		if($is_button){

			do_action('mymail_form_body_button');
			include MYMAIL_DIR.'views/forms/button.php';

		}elseif($is_iframe){

			do_action('mymail_form_body_iframe');
			$form = mymail('form')->id($form_id);
			$form->add_class('in-iframe');
			$form->render();

		}elseif($is_editable){

			$form = mymail('form')->id($form_id);
			$form->add_class('embeded');
			$form->prefill(false);
			$form->set_success(__('This is a success info', 'mymail'));
			$form->set_error(__('This is an error message', 'mymail'));
			$form->is_preview();
			$form->render();

		}else{

			$form = mymail('form')->id($form_id);
			$form->add_class('embeded');
			$form->render();

		}

	}

	public function form_footer() {

		extract($this->request);

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script('mymail-form', MYMAIL_URI . 'assets/js/form'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);


		if($is_button){

			do_action('mymail_form_footer_button');
			wp_register_script('form-button-script', MYMAIL_URI . 'assets/js/form-button-script'.$suffix.'.js', array(), MYMAIL_VERSION);
			//wp_localize_script( 'form-button-script', 'MyMailData', $MyMailData);
			mymail('helper')->wp_print_embedded_scripts('form-button-script');
			//wp_print_scripts('form-button-script');

		}elseif($is_editable){

			wp_register_script('mymail-editable-form', MYMAIL_URI . 'assets/js/editable-form-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);
			wp_print_scripts('mymail-editable-form');

		}elseif($is_embeded){

			do_action('mymail_form_footer_embeded');
			wp_print_scripts('mymail-form');

		}elseif($is_iframe){

			do_action('mymail_form_footer_iframe');
			wp_register_script('form-iframe-script', MYMAIL_URI . 'assets/js/form-iframe-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);
			//wp_localize_script('form-iframe-script', 'MyMailData', $MyMailData);
			wp_print_scripts('form-iframe-script');
			wp_print_scripts('mymail-form');

		}else{

			//wp_print_scripts('mymail-form-embeded');

		}

	}

	public function admin_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', __( 'Forms', 'mymail' ), __( 'Forms', 'mymail' ), 'mymail_edit_forms', 'mymail_forms', array( &$this, 'view_forms' )  );

		add_action('load-'.$page, array( &$this, 'script_styles'));

		if(isset($_GET['ID']) || isset($_GET['new'])) :

			add_action('load-'.$page, array( &$this, 'edit_entry'), 99);

		else :

			add_action('load-'.$page, array( &$this, 'bulk_actions'), 99);
			add_action('load-'.$page, array( &$this, 'screen_options'));
			add_filter('manage_'.$page.'_columns', array(  &$this, 'get_columns'));


		endif;

	}

	public function script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if(isset($_GET['ID']) || isset($_GET['new'])) :

			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-touch-punch');

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_style('form-button-style', MYMAIL_URI . 'assets/css/button-style'.$suffix.'.css', array(), MYMAIL_VERSION);

			wp_enqueue_style('form-button-default-style', MYMAIL_URI . 'assets/css/button-default-style'.$suffix.'.css', array('form-button-style'), MYMAIL_VERSION);
			wp_enqueue_style('form-button-wp-style', MYMAIL_URI . 'assets/css/button-wp-style'.$suffix.'.css', array('form-button-style'), MYMAIL_VERSION);
			wp_enqueue_style('form-button-twitter-style', MYMAIL_URI . 'assets/css/button-twitter-style'.$suffix.'.css', array('form-button-style'), MYMAIL_VERSION);
			wp_enqueue_style('form-button-flat-style', MYMAIL_URI . 'assets/css/button-flat-style'.$suffix.'.css', array('form-button-style'), MYMAIL_VERSION);
			wp_enqueue_style('form-button-minimal-style', MYMAIL_URI . 'assets/css/button-minimal-style'.$suffix.'.css', array('form-button-style'), MYMAIL_VERSION);

			wp_enqueue_style('jquery-ui-style', MYMAIL_URI . 'assets/css/libs/jquery-ui'.$suffix.'.css', array(), MYMAIL_VERSION);
			wp_enqueue_style('jquery-datepicker', MYMAIL_URI . 'assets/css/datepicker'.$suffix.'.css', array(), MYMAIL_VERSION);

			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-datepicker');

			wp_enqueue_script('mymail-form-detail', MYMAIL_URI . 'assets/js/form-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);

			wp_enqueue_style('mymail-form-detail', MYMAIL_URI . 'assets/css/form-style'.$suffix.'.css', array(), MYMAIL_VERSION);
			wp_localize_script( 'mymail-form-detail', 'mymailL10n', array(
				'require_save' => __('The changes you made will be lost if you navigate away from this page.', 'mymail'),
				'prev' => __('prev', 'mymail'),
			) );
			wp_localize_script( 'mymail-form-detail', 'mymaildata', array(
				'embedcode' => $this->get_empty_subscribe_button()
			) );

		else :

			wp_enqueue_style('mymail-forms-table', MYMAIL_URI . 'assets/css/forms-table-style'.$suffix.'.css', array(), MYMAIL_VERSION);
			wp_print_styles('mymail-forms-table');

		endif;

	}

	public function get_columns() {
		return $columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => __( 'Name', 'mymail' ),
			'shortcode' => __( 'Shortcode', 'mymail' ),
			'fields' => __( 'Fields', 'mymail' ),
			'lists' => __( 'Lists', 'mymail' ),
			'occurrence' => __( 'Occurrence', 'mymail' ),
			'preview' => '',
		);

	}

	public function bulk_actions( ) {

		if(empty($_POST)) return;

		if(empty($_POST['forms'])) return;

		if ( isset( $_POST['action'] ) && -1 != $_POST['action'] )
			$action = $_POST['action'];

		if ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] )
			$action = $_POST['action2'];

		$redirect = add_query_arg($_GET);

		switch($action){

			case 'delete':
				if(current_user_can('mymail_delete_forms')){

					$success = $this->remove($_POST['forms']);
					if(is_wp_error($success)){
						mymail_notice(sprintf(__('There was an error while deleting forms: %s', 'mymail'), $success->get_error_message()), 'error', true);

					}else if($success){
						mymail_notice(sprintf(__('%d forms have been removed', 'mymail'), count($_POST['forms'])), 'error', true);
					}

					wp_redirect( $redirect );
					exit;

				}
				break;

			default:

				break;

		}

	}

	public function edit_entry( ) {

		if(isset($_POST['mymail_data'])){

			$data = (object) stripslashes_deep($_POST['mymail_data']);
			$redirect = $_POST['_wp_http_referer'];


			if(isset($_POST['save']) || isset($_POST['structure']) || isset($_POST['design']) || isset($_POST['settings'])) :

				parse_str($_POST['_wp_http_referer'], $urlparams);

				$is_profile_form = isset($_POST['profile_form']) && $_POST['profile_form'];

				if(isset($urlparams['new'])){

					$id = $this->add($data);

					if(is_wp_error($id)){
						mymail_notice(sprintf(__('There was an error while adding the form: %s', 'mymail'), $id->get_error_message()), 'error', true);

					}

					$redirect = remove_query_arg('new', add_query_arg(array('tab' => 'design', 'ID' => $id), $redirect));

				}else{

					$id = $this->update($data);

					if(is_wp_error($id)){
						mymail_notice(sprintf(__('There was an error while updating the form: %s', 'mymail'), $id->get_error_message()), 'error', true);

					}

				}

				if(isset($_POST['mymail_structure'])){
					$structure = stripslashes_deep($_POST['mymail_structure']);
					if(!isset($structure['fields']['email'])) $structure['fields']['email'] = mymail_text('email');
					$required = isset($structure['required']) ? array_keys($structure['required']) : array();
					$error_msg = isset($structure['error_msg']) ? (array) $structure['error_msg'] : array();

					$this->update_fields($id, $structure['fields'], $required, $error_msg);

				}

				if(isset($_POST['mymail_design'])){
					$design = stripslashes_deep($_POST['mymail_design']);

					$this->update_style($id, urldecode($design['style']), $design['custom']);

				}

				if($is_profile_form){
					mymail_update_option('profile_form', $id);
				}

				if(isset($data->options)){

					$this->update_options($id, $data->options);

				}

				mymail_notice(isset($urlparams['new']) ? __('Form added', 'mymail') : __('Form updated', 'mymail'), 'updated', true);


			endif;


			if(isset($_POST['design'])) :

				wp_redirect( add_query_arg(array('tab' => 'design'), $redirect) );
				exit;

			elseif(isset($_POST['settings'])) :

				wp_redirect( add_query_arg(array('tab' => 'settings'), $redirect) );
				exit;

			elseif(isset($_POST['structure'])) :

				wp_redirect( add_query_arg(array('tab' => 'structure'), $redirect) );
				exit;

			elseif(isset($_POST['delete'])) :

				if(current_user_can('mymail_delete_forms') && $form = $this->get(intval($_POST['mymail_data']['ID']))){
					$success = $this->remove($form->ID);
					if(is_wp_error($success)){
						mymail_notice(sprintf(__('There was an error while deleting forms: %s', 'mymail'), $success->get_error_message()), 'error', true);

					}else if($success){
						mymail_notice(sprintf(__('Form %s has been removed', 'mymail'), '<strong>&quot;'.$form->name.'&quot;</strong>'), 'error', true);
						do_action( 'mymail_form_delete', $form->ID );
					}

					wp_redirect( 'edit.php?post_type=newsletter&page=mymail_forms' );
					exit;

				};

			endif;


			wp_redirect( $redirect );
			exit;

		}


	}


	public function view_forms( ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if(isset($_GET['ID']) || isset($_GET['new'])) :

			include MYMAIL_DIR . 'views/forms/detail.php';

		else :

			include MYMAIL_DIR . 'views/forms/overview.php';

		endif;

	}

	public function edit_form( $form ) {

		include MYMAIL_DIR . 'views/forms/edit.php';
	}

	public function screen_options( ) {

		require_once MYMAIL_DIR . 'classes/forms.table.class.php';

		$screen = get_current_screen();

		add_screen_option( 'per_page', array(
			'label' => __('Forms', 'mymail'),
			'default' => 10,
			'option' => 'mymail_forms_per_page'
		));

	}

	public function save_screen_options($status, $option, $value) {

		if ( 'mymail_forms_per_page' == $option ) return $value;

		return $status;

	}

	public function update($entry) {

		global $wpdb;

		$data = (array) $entry;

		if(!isset($data['ID']))
			return new WP_Error('id_required', __('updating form requires ID', 'mymail'));

		$now = time();

		$lists = isset($data['lists']) ? $data['lists'] : false;
		unset($data['lists']);

		$wpdb->suppress_errors();

		if(false !== $wpdb->update("{$wpdb->prefix}mymail_forms", $data, array('ID' => $data['ID']))){

			$form_id = intval($data['ID']);

			if($lists) $this->assign_lists($form_id, $lists, true);

			do_action('mymail_update_form', $form_id);

			mymail_clear_cache('form');

			return $form_id;

		}else{

			return new WP_Error('form_exists', $wpdb->last_error);
		}

	}

	public function add($entry) {

		global $wpdb;

		$now = time();

		$entry = is_string($entry) ? (object) array('email' => $entry) : (object) $entry;

		$entry = (array) $entry;

		$entry = wp_parse_args( $entry, array(
			'name' => __('Form', 'mymail'),
			'submit' => mymail_text('submitbutton'),
			'asterisk' => true,
			'userschoice' => false,
			'dropdown' => false,
			'inline' => false,
			'addlists' => false,
			'prefill' => false,
			'style' => '',
			'custom_style' => '',
			'doubleoptin' => true,
			'subject' => __('Please confirm', 'mymail'),
			'headline' => __('Please confirm your Email Address', 'mymail'),
			'content' => sprintf(__("You have to confirm your email address. Please click the link below to confirm. %s", 'mymail'), "\n{link}"),
			'link' => __('Click here to confirm', 'mymail'),
			'resend' => false,
			'resend_count' => 2,
			'resend_time' => 48,
			'template' => 'notification.html',
			'vcard' => false,
			'vcard_content' => $this->get_vcard(),
			'confirmredirect' => '',
			'redirect' => '',
			'added' => $now,
			'updated' => $now,
		));

		$wpdb->suppress_errors();

		if($wpdb->insert("{$wpdb->prefix}mymail_forms", $entry)){

			$form_id = $wpdb->insert_id;

			do_action('mymail_add_form', $form_id);

			return $form_id;

		}else{

			return new WP_Error('form_exists', $wpdb->last_error);
		}

	}


	public function assign_lists($form_ids, $lists, $remove_old = false) {

		global $wpdb;

		if(!is_array($form_ids)) $form_ids = array($form_ids);
		if(!is_array($lists)) $lists = array($lists);

		$now = time();

		$inserts = array();
		foreach ($lists as $list_id) {
			foreach($form_ids as $form_id){
				$inserts[] = "($list_id, $form_id, $now)";
			}
		}

		if(empty($inserts)) return true;

		$chunks = array_chunk($inserts, 200);

		$success = true;

		if($remove_old) $this->unassign_lists($form_ids, NULL, $lists);

		foreach($chunks as $insert){
			$sql = "INSERT INTO {$wpdb->prefix}mymail_forms_lists (list_id, form_id, added) VALUES ";

			$sql .= " ".implode(',', $insert);

			$sql .= " ON DUPLICATE KEY UPDATE list_id = values(list_id), form_id = values(form_id)";

			$success = $success && (false !== $wpdb->query($sql));

		}
		return $success;

	}

	public function unassign_lists($form_ids, $lists = NULL, $not_list = NULL) {

		global $wpdb;

		$form_ids = !is_array($form_ids) ? array(intval($form_ids)) : array_filter($form_ids, 'is_numeric');

		$sql = "DELETE FROM {$wpdb->prefix}mymail_forms_lists WHERE form_id IN (".implode(', ', $form_ids).")";

		if(!is_null($lists) && !empty($lists)){
			if(!is_array($lists)) $lists = array($lists);
			$sql .= " AND list_id IN (".implode(', ', array_filter($lists, 'is_numeric')).")";
		}
		if(!is_null($not_list) && !empty($not_list)){
			if(!is_array($not_list)) $not_list = array($not_list);
			$sql .= " AND list_id NOT IN (".implode(', ', array_filter($not_list, 'is_numeric')).")";
		}

		if(false !== $wpdb->query($sql)){

			do_action('mymail_unassign_form_lists', $form_ids, $lists, $not_list );

			return true;
		}

		return false;

	}

	public function update_field($form_id, $field, $value, $required = NULL, $error_msg = '') {

		return $this->update_fields($form_id, array($field => $value), ($required ? array($field) : array()), array($field => $error_msg));

	}

	public function update_fields($form_id, $fields, $required = array(), $error_msg = array()) {

		global $wpdb;

		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}mymail_form_fields WHERE form_id = %d AND field_id NOT IN ('".implode("', '", array_keys($fields))."')", $form_id));

		$sql = "INSERT INTO {$wpdb->prefix}mymail_form_fields (form_id, field_id, name, error_msg, required, position) VALUES ";

		$entries = array();
		$position = 0;
		foreach ($fields as $field_id => $name) {
			$entries[] = $wpdb->prepare("(%d, %s, %s, %s, %d, %d)", $form_id, $field_id, $name, (isset($error_msg[$field_id]) ? $error_msg[$field_id] : ''), (in_array($field_id, $required) || $field_id == 'email'), $position++);
		}

		$sql .= implode(', ', $entries);
		$sql .= " ON DUPLICATE KEY UPDATE name = values(name), error_msg = values(error_msg), required = values(required), position = values(position)";

		return false !== $wpdb->query($sql);

	}

	public function update_options($form_id, $field, $value = null) {

		global $wpdb;

		$data = is_array($field) ? $field : array($field => $value);

		$sql = $wpdb->prepare("UPDATE {$wpdb->prefix}mymail_forms SET ID = %d", $form_id);

		$entries = array();
		foreach ($data as $col => $value) {
			$sql .= $wpdb->prepare(", `$col` = %s", $value);
		}

		$sql .= $wpdb->prepare(" WHERE ID = %d", $form_id);

		return false !== $wpdb->query($sql);

	}

	public function update_style($form_id, $style, $custom_style = '') {

		global $wpdb;

		if($style == '{}') $style = '';

		$sql = $wpdb->prepare("UPDATE {$wpdb->prefix}mymail_forms SET ID = %d, style = %s, custom_style = %s WHERE ID = %d", $form_id, $style, strip_tags($custom_style), $form_id);

		return false !== $wpdb->query($sql);

	}

	public function remove($form_ids) {

		global $wpdb;

		$form_ids = !is_array($form_ids) ? array(intval($form_ids)) : array_filter($form_ids, 'is_numeric');

		//delete from forms, form_fields

		$sql = "DELETE a,b FROM {$wpdb->prefix}mymail_forms AS a LEFT JOIN {$wpdb->prefix}mymail_form_fields AS b ON ( a.ID = b.form_id ) WHERE a.ID IN (".implode(',', $form_ids).")";

		$success = false !== $wpdb->query($sql);

		if($wpdb->last_error) return new WP_Error('db_error', $wpdb->last_error);

		return $success;

	}

	public function get($ID, $fields = true, $lists = true) {

		global $wpdb;

		if(is_null($ID)){
			$sql = "SELECT a.* FROM {$wpdb->prefix}mymail_forms AS a WHERE 1 ORDER BY ID";

		}else{
			$sql = "SELECT a.* FROM {$wpdb->prefix}mymail_forms AS a WHERE a.ID = %d LIMIT 1";

			$sql = $wpdb->prepare($sql, $ID);
		}

		if(!($forms = $wpdb->get_results($sql))) return false;


		foreach($forms as $i => $form){

			if($fields){
				$forms[$i]->fields = $this->get_fields($forms[$i]->ID);

				$forms[$i]->required = array();
				foreach($forms[$i]->fields as $field){
					if($field->required) $forms[$i]->required[] = $field->field_id;
				}
			}

			if($lists) $forms[$i]->lists = $this->get_lists($forms[$i]->ID, true);

			$forms[$i]->style = ($forms[$i]->style) ? json_decode($forms[$i]->style) : array();
			$forms[$i]->stylesheet = '';
			foreach ($forms[$i]->style as $selectors => $data) {
				$forms[$i]->stylesheet .= '.mymail-form.mymail-form-'.$forms[$i]->ID.' '.$selectors.'{';
				foreach ($data as $key => $value) {
					$forms[$i]->stylesheet .= $key.':'.$value.';';
				}
				$forms[$i]->stylesheet .= '}';
			}
			$forms[$i]->stylesheet .= $forms[$i]->custom_style;
			if(empty($forms[$i]->submit)) $forms[$i]->submit = mymail_text('submitbutton');

		}

		return is_null($ID) ? $forms : $forms[0];

	}

	public function get_all($fields = false, $lists = false) {

		return $this->get(null, $fields, $lists);

	}

	public function get_lists($id, $ids_only = false) {

		global $wpdb;

		$cache = mymail_cache_get( 'forms_lists' );
		if(isset($cache[$id])) return $cache[$id];

		$sql = "SELECT a.* FROM {$wpdb->prefix}mymail_lists AS a LEFT JOIN {$wpdb->prefix}mymail_forms_lists AS ab ON a.ID = ab.list_id WHERE ab.form_id = %d";

		$lists = $wpdb->get_results($wpdb->prepare($sql, $id));

		return $ids_only ? wp_list_pluck( $lists, 'ID' ) : $lists;

	}

	public function get_fields($form_id) {

		global $wpdb;

		$sql = "SELECT a.field_id, a.name, a.error_msg ,a.required FROM {$wpdb->prefix}mymail_form_fields AS a WHERE a.form_id = %d ORDER BY a.position ASC";

		$fields = $wpdb->get_results($wpdb->prepare($sql, $form_id));

		foreach($fields as $i => $field){
			if(empty($field->error_msg)){
				$field->error_msg = ($field->field_id == 'email')
					? __('Email is missing or wrong', 'mymail')
					: sprintf(__('%s is missing', 'mymail'), $field->name);
			}
			unset($fields[$i]);
			$fields[$field->field_id] = $field;
		}

		return $fields;

	}

	public function get_empty() {

		global $wpdb;

		$fields = wp_parse_args(array(
		), $wpdb->get_col("DESCRIBE {$wpdb->prefix}mymail_forms"));

		$form = (object) array_fill_keys(array_values($fields), NULL);

		$form->fields = array();

		return $form;

	}


	public function get_occurrence( $form_id ) {

		global $wpdb;

		$return = array();
		$empty = (object) array('posts' => array(), 'widgets' => array());
		$empty = array('posts' => array());

		if(false === ($occurrence = mymail_cache_get( 'forms_occurrence' ))){

			$occurrence = array();

			$sql = "SELECT ID, post_title AS name, post_content FROM {$wpdb->posts} WHERE post_content LIKE '%[newsletter_signup_form%' AND post_status NOT IN ('inherit')";

			$result = $wpdb->get_results($sql);
			$i = 100;

			foreach($result as $row){
				preg_match_all("#\[newsletter_signup_form((.*)id=\"?(\d+)\"?)?#", $row->post_content, $matches);
				foreach ($matches[3] as $found_form_id) {
					if(!$found_form_id) $found_form_id = 0;
					$occurrence[$found_form_id]['posts'][$row->ID] = $row->name;
				}
			}

			$sql = "SELECT option_id AS ID, option_value FROM {$wpdb->options} WHERE option_name = 'widget_mymail_signup'";
			$result = $wpdb->get_results($sql);

			foreach($result as $row){
				$widgetdata = maybe_unserialize( $row->option_value );
				foreach ($widgetdata as $data) {
					if(!isset($data['form'])) continue;
					$found_form_id = $data['form'];
					$occurrence[$found_form_id]['widgets'][] = $data['title'];
				}
			}

			mymail_cache_add( 'forms_occurrence', $occurrence );

		}

		return isset($occurrence[$form_id]) ? $occurrence[$form_id] : NULL;

	}

	public function get_count( ) {

		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mymail_forms";

		return $wpdb->get_var($sql);

	}

	private function _get_style($style, $selector, $property) {

		echo (isset($style->{$selector}) && isset($style->{$selector}->{$property})) ? $style->{$selector}->{$property} : '';

	}

	public function subscribe_button($form_id = 1, $args = array()) {

		echo $this->get_subscribe_button($form_id, $args);
	}

	public function get_subscribe_button($form_id = 1, $args = array() ) {

		$options = wp_parse_args( $args, array(
			'showcount' => false,
			'design' => 'default',
			'label' => mymail_text('submitbutton'),
			'width' => 480,
			'endpoint' => NULL
		));

		$button_src = apply_filters('mymail_subscribe_button_src', '//mymailapp.github.io/v1/button.js', $options);
		//$button_src = apply_filters('mymail_subscribe_button_src', MYMAIL_URI.'assets/js/button.js', $options);

		$options['endpoint'] = add_query_arg(array(
			'id' => $form_id,
			'iframe' => 1,
		), (is_null($options['endpoint']) ? MYMAIL_URI.'form.php' : $options['endpoint']));

		$html = '<a href="'.$options['endpoint'].'" class="mymail-subscribe-button" data-design="'.esc_attr($options['design']).'" data-showcount="'.($options['showcount'] ? 1 : 0).'" data-width="'.esc_attr($options['width']).'">'.strip_tags($options['label']).'</a>';

		$script = "<script type=\"text/javascript\">!function(my,m,a,i,l){my[l]=my[l]||(function(){l=m.createElement(a);m=m.getElementsByTagName(a)[0];l.async=1;l.src=i;m.parentNode.insertBefore(l,m);return !0}())}(window,document,'script','$button_src','MyMailSubscribe');</script>";

		return $html.$script;
	}


	public function get_empty_subscribe_button( ) {

		$button = $this->get_subscribe_button( 1, array('showcount' => true, 'width' => 999));

		$button = strtr($button, array(
			'?id=1' => '?id=%ID%',
			' data-showcount="1"' => '%SHOWCOUNT%',
			' data-width="999"' => '%WIDTH%',
			' data-design="default"' => '%DESIGN%',
			'>Subscribe<' => '>%LABEL%<',
		));

		return $button;
	}

	public function get_vcard() {
		$current_user = wp_get_current_user();

		$text = 'BEGIN:VCARD'."\n";
		$text .= 'N:Firstname;Lastname;;;'."\n";
		$text .= 'ADR;INTL;PARCEL;WORK:;;StreetName;City;State;123456;Country'."\n";
		$text .= 'EMAIL;INTERNET:'.$current_user->user_email.''."\n";
		$text .= 'ORG:'.get_bloginfo('name').''."\n";
		$text .= 'URL;WORK:'.home_url().''."\n";
		$text .= 'END:VCARD'."\n";
		return $text;
	}

	public function on_activate($new) {

		//create form if non exists
		$forms = $this->get_all();
		if(empty($forms)){
			$form_id = $this->add(array(
				'name' => __('Default Form', 'mymail'),
				'submit' => __('Subscribe', 'mymail')
			));
			$this->update_field($form_id, 'email', __('Email', 'mymail'));
			mymail_update_option('profile_form', $form_id);
		}

	}


}
