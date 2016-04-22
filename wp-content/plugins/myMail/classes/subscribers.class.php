<?php if (!defined('ABSPATH')) die('not allowed');


class mymail_subscribers {

	//allowed keys in meta table
	private $meta_keys = array('bounce','geo','coords','client','clienttype','clientversion','lang','ip','confirmation','error','referer','timeoffset','form');
	private $single_meta_keys = array('ip','lang','timeoffset','form');

	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init' ) );

	}

	public function init() {

		add_action('mymail_cron', array( &$this, 'send_confirmations'));
		add_action('mymail_subscriber_subscribed', array( &$this, 'remove_pending_confirmations'));

		add_action('user_register', array( &$this, 'wp_id'));
		add_action('profile_update', array( &$this, 'sync_wp_user'));
		add_action('update_user_meta', array( &$this, 'sync_wp_user_meta'), 10, 4);

		add_action('user_register' , array( &$this, 'user_register' ) );
		add_action('register_form' , array( &$this, 'register_form' ) );
		add_action('deleted_user' , array( &$this, 'delete_subscriber_from_wpuser' ), 10, 2 );

		add_action('comment_form_logged_in_after' , array( &$this, 'comment_form_checkbox' ) );
		add_action('comment_form_after_fields' , array( &$this, 'comment_form_checkbox' ) );
		add_action('comment_post' , array( &$this, 'comment_post' ), 10, 2 );
		add_action('wp_set_comment_status' , array( &$this, 'wp_set_comment_status' ), 10, 2 );

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 20 );

		if (is_admin()) {

			add_filter( 'set-screen-option', array( &$this, 'save_screen_options' ), 10, 3);
			add_action( 'show_user_profile', array( &$this, 'edit_user_profile' ), 9, 1);
			add_action( 'edit_user_profile', array( &$this, 'edit_user_profile' ), 9, 1);

		} else {

		}

	}

	public function admin_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', __( 'Subscribers', 'mymail' ), __( 'Subscribers', 'mymail' ), 'mymail_edit_subscribers', 'mymail_subscribers', array( &$this, 'view_subscribers' )  );

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

			global $wp_locale;

			wp_enqueue_script('easy-pie-chart', MYMAIL_URI . 'assets/js/libs/easy-pie-chart'.$suffix.'.js', array('jquery'), MYMAIL_VERSION, true);
			wp_enqueue_style('easy-pie-chart', MYMAIL_URI . 'assets/css/libs/easy-pie-chart'.$suffix.'.css', array(), MYMAIL_VERSION);

			wp_enqueue_style('jquery-ui-style', MYMAIL_URI . 'assets/css/libs/jquery-ui'.$suffix.'.css', array(), MYMAIL_VERSION);
			wp_enqueue_style('jquery-datepicker', MYMAIL_URI . 'assets/css/datepicker'.$suffix.'.css', array(), MYMAIL_VERSION);

			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-datepicker');

			wp_enqueue_style('mymail-flags', MYMAIL_URI . 'assets/css/flags'.$suffix.'.css', array(), MYMAIL_VERSION );

			wp_enqueue_script('mymail-subscriber-detail', MYMAIL_URI . 'assets/js/subscriber-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION, true);

			wp_enqueue_style('mymail-subscriber-detail', MYMAIL_URI . 'assets/css/subscriber-style'.$suffix.'.css', array(), MYMAIL_VERSION);
			wp_localize_script('mymail-subscriber-detail', 'mymailL10n', array(
				'next' => __('next', 'mymail'),
				'prev' => __('prev', 'mymail'),
				'start_of_week' => get_option('start_of_week'),
				'day_names' => $wp_locale->weekday,
				'day_names_min' => array_values($wp_locale->weekday_abbrev),
				'month_names' => array_values($wp_locale->month),
				'invalid_email' => __( "this isn't a valid email address!", 'mymail' ),
				'email_exists' => __( "this email address already exists!", 'mymail' ),
			) );

		else :

			wp_enqueue_style('mymail-subscribers-table', MYMAIL_URI . 'assets/css/subscribers-table-style'.$suffix.'.css', array(), MYMAIL_VERSION);

		endif;

	}

	public function get_columns() {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'avatar' => '',
			'name' => __( 'Name', 'mymail' ),
		);
		$custom_fields = mymail()->get_custom_fields();
		foreach($custom_fields as $key => $field){
			$columns[$key] = $field['name'];
		}

		$columns['lists'] = __( 'Lists', 'mymail' );
		$columns['emails'] = __( 'emails', 'mymail' );
		$columns['status'] = __( 'Status', 'mymail' );
		$columns['signup'] = __( 'Subscribed', 'mymail' );

		return $columns;
	}

	public function bulk_actions( ) {

		if(empty($_POST)) return;

		if(empty($_POST['subscribers'])) return;

		if ( isset( $_POST['action'] ) && -1 != $_POST['action'] )
			$action = $_POST['action'];

		if ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] )
			$action = $_POST['action2'];

		$redirect = add_query_arg($_GET);

		switch($action){

			case 'delete':
				if(current_user_can('mymail_delete_subscribers')){

					$success = $this->remove($_POST['subscribers']);
					if(is_wp_error($success)){
						mymail_notice(sprintf(__('There was an error while deleting subscribers: %s', 'mymail'), $success->get_error_message()), 'error', true);

					}else if($success){
						mymail_notice(sprintf(__('%d Subscribers have been removed', 'mymail'), count($_POST['subscribers'])), 'error', true);
					}

					wp_redirect( $redirect );
					exit;

				}
				break;

			case 'subscribe':
				if($count = $this->change_status($_POST['subscribers'], 1)){
					mymail_notice(sprintf(__('%d Subscribers have been subscribed', 'mymail'), $count), 'updated', true);
					wp_redirect( $redirect );
					exit;
				}
				break;

			case 'unsubscribe':
				if($count = $this->change_status($_POST['subscribers'], 2)){
					mymail_notice(sprintf(__('%d Subscribers have been unsubscribed', 'mymail'), $count), 'error', true);
					wp_redirect( $redirect );
					exit;
				}
				break;

			case 'pending':
				if($count = $this->change_status($_POST['subscribers'], 0)){
					mymail_notice(sprintf(__('%d Subscribers have been set to pending', 'mymail'), $count), 'updated', true);
					wp_redirect( $redirect );
					exit;
				}
				break;

			case 'send_campaign':
				$listid = mymail('lists')->add_segment();

				if($this->assign_lists($_POST['subscribers'], $listid)){
					mymail_notice(sprintf(__('%d Subscribers have been assigned to a new list', 'mymail'), count($_POST['subscribers'])), 'error', true);
					wp_redirect( 'post-new.php?post_type=newsletter&lists%5B%5D='.$listid );
					exit;
				}
				break;

			case 'confirmation':

				if($count = $this->send_confirmations($_POST['subscribers'], true, true)){
					mymail_notice(sprintf(__('Confirmations sent to %d pending subscribers', 'mymail'), $count), 'error', true);
					wp_redirect( $redirect );
					exit;
				}
				break;

			default:

				if(preg_match('#^add_list_(\d+)#', $action, $match)){
					if($list = mymail('lists')->get($match[1])){
						$this->assign_lists($_POST['subscribers'], $list->ID);
						mymail_notice(sprintf(__('%d Subscribers added to list %s', 'mymail'), count($_POST['subscribers']), '"<a href="edit.php?post_type=newsletter&page=mymail_lists&ID='.$list->ID.'">'.$list->name.'</a>"'), 'updated', true);
						wp_redirect( $redirect );
						exit;
					}
				}else if(preg_match('#^remove_list_(\d+)#', $action, $match)){
					if($list = mymail('lists')->get($match[1])){
						$this->unassign_lists($_POST['subscribers'], $list->ID);
						mymail_notice(sprintf(__('%d Subscribers removed from list %s', 'mymail'), count($_POST['subscribers']), '"<a href="edit.php?post_type=newsletter&page=mymail_lists&ID='.$list->ID.'">'.$list->name.'</a>"'), 'updated', true);
						wp_redirect( $redirect );
						exit;
					}
				}

				break;

		}

	}

	public function edit_entry( ) {

		if(isset($_GET['new']) && isset($_GET['wp_user'])){

			$user_id = intval($_GET['wp_user']);

			$subscriber_id = $this->add_from_wp_user($user_id, array('status' => 1, 'referer' => 'wpuser', 'ip' => false));

			if(is_wp_error($subscriber_id)){

				mymail_notice(__($subscriber_id->get_error_message(), 'mymail'), 'error', true);
				wp_redirect( 'edit.php?post_type=newsletter&page=mymail_subscribers' );

			}else{

				mymail_notice(__('Subscriber added', 'mymail'), 'updated', true);
				do_action( 'mymail_subscriber_save', $subscriber_id );
				wp_redirect( 'edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber_id );
				exit;

			}

		}
		if(isset($_POST['mymail_data'])){

			if(isset($_POST['save'])) :

				parse_str($_POST['_wp_http_referer'], $urlparams);

				$is_new = isset($urlparams['new']);

				if($is_new && !current_user_can('mymail_add_subscribers'))
					wp_die(__('You are not allowed to add subscribers!', 'mymail'));

				if(!$is_new && !current_user_can('mymail_edit_subscribers'))
					wp_die(__('You are not allowed to edit subscribers!', 'mymail'));


				$empty = $this->get_empty();

				//sanitize input;
				$entry = (object) stripslashes_deep(array_intersect_key($_POST['mymail_data'], (array) $empty));

				if($is_new){
					$entry->referer = 'backend';
					$entry->confirm = 0;
					$entry->ip = false;
				}

				$subscriber_id = $is_new
					? $this->add($entry)
					: $this->update($entry);

				if(is_wp_error($subscriber_id)){

					switch($subscriber_id->get_error_code()){
						case 'email_exists':
							$subscriber = $this->get_by_mail($entry->email);

							$msg = sprintf(__('%1$s already exists. %2$s', 'mymail'), '<strong>&quot;'.$subscriber->email.'&quot;</strong>', '<a href="edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber->ID.'">'.__('Edit this user', 'mymail').'</a>');
							break;
						default:
							$msg = $subscriber_id->get_error_message();
					}

					mymail_notice($msg, 'error', true);

				}else{

					$subscriber = $this->get($subscriber_id, true);

					if($subscriber->status == 0)
						$this->update_meta( $subscriber->ID, 0, 'confirmation', 0 );

					if(isset($_POST['mymail_lists']))
						$this->assign_lists($subscriber->ID, array_filter($_POST['mymail_lists'], 'is_numeric'), true);

					mymail_notice($is_new ? __('Subscriber added', 'mymail') : __('Subscriber saved', 'mymail'), 'updated', true);
					do_action( 'mymail_subscriber_save', $subscriber->ID );
					wp_redirect( 'edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber->ID );
					exit;
				}

			elseif(isset($_POST['delete'])) :

				if(!current_user_can('mymail_delete_subscribers'))
					wp_die(__('You are not allowed to delete subscribers!', 'mymail'));

				if($subscriber = $this->get(intval($_POST['mymail_data']['ID']), true)){
					$success = $this->remove($subscriber->ID);
					if(!$success){
						mymail_notice(__('There was an error while deleting subscribers!', 'mymail'), 'error', true);

					}else{
						mymail_notice(sprintf(__('Subscriber %s has been removed', 'mymail'), '<strong>&quot;'.$subscriber->email.'&quot;</strong>'), 'error', true);
						do_action( 'mymail_subscriber_delete', $subscriber->ID, $subscriber->email );
					}

					wp_redirect( 'edit.php?post_type=newsletter&page=mymail_subscribers' );
					exit;

				};

			elseif(isset($_POST['confirmation'])) :

				if($subscriber = $this->get(intval($_POST['mymail_data']['ID']), true)){
					if($this->send_confirmations($subscriber->ID, true, true)){
						mymail_notice(__('Confirmation has been sent', 'mymail'), 'updated', true);
					}
					wp_redirect( 'edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber->ID );
					exit;
				};

			endif;

		}

		if(isset($_GET['resendcampaign'])){
			if(!current_user_can('publish_newsletters') || !wp_verify_nonce($_GET['_wpnonce'], 'mymail-resend-campaign'))
				wp_die(__('You are not allowed to publish campaigns!', 'mymail'));

		 	$subscriber_id = isset($_GET['ID']) ? intval($_GET['ID']) : NULL;
		 	$campaign_id = isset($_GET['campaign_id']) ? intval($_GET['campaign_id']) : NULL;

			if($subscriber_id && $campaign_id && $subscriber = $this->get($subscriber_id, true)){

				$campaign = mymail('campaigns')->get($campaign_id);

				if($campaign && mymail('campaigns')->send_to_subscriber( $campaign_id, $subscriber_id, true, true)){
					mymail_notice(sprintf(__('Campaign %s has been sent', 'mymail'), '<strong>&quot;'.$campaign->post_title.'&quot;</strong>'), 'updated', true);
					wp_redirect( 'edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber_id );
					exit;
				}
			}
		}
	}

	public function sync_all_subscriber( $limit = NULL, $offset = 0 ){

		global $wpdb;

		$this->wp_id();

		$sql = "SELECT ID FROM {$wpdb->prefix}mymail_subscribers WHERE wp_id != 0";

		if(!is_null($limit))
			$sql .= " LIMIT ".intval($offset).", ".intval($limit);

		$subscriber_ids = $wpdb->get_col($sql);

		foreach($subscriber_ids as $subscriber_id){
			$this->sync_subscriber($subscriber_id);
		}

		return count($subscriber_ids);

	}

	public function sync_subscriber( $subscriber_id ) {

		//Subscriber => WP User

		if(!mymail_option('sync')) return;

		$synclist = mymail_option('synclist', array());

		if(empty($synclist)) return;

		$subscriber = $this->get($subscriber_id, true);

		if(!$subscriber->wp_id) return;

		$usertable_fields = array('user_login', 'user_nicename', 'user_email', 'user_url', 'display_name');

		$userdata = array();
		$usermeta = array();

		foreach($synclist as $field => $meta){

			if(in_array($meta, $usertable_fields)){
				$userdata[$meta] = $subscriber->{$field};
			}else{
				$usermeta[$meta] = $subscriber->{$field};
			}
		}

		remove_action('profile_update', array( &$this, 'sync_wp_user'));
		remove_action('update_user_meta', array( &$this, 'sync_wp_user_meta'), 10, 4);

		if(!empty($userdata)){
			wp_update_user( wp_parse_args(array('ID' => $subscriber->wp_id), $userdata) );
		}
		if(!empty($usermeta)){
			foreach($usermeta as $key => $value){
				update_user_meta( $subscriber->wp_id, $key, $value );
			}
		}

		add_action('profile_update', array( &$this, 'sync_wp_user'));
		add_action('update_user_meta', array( &$this, 'sync_wp_user_meta'), 10, 4);


		return true;

	}

	public function sync_all_wp_user( $limit = NULL, $offset = 0 ){

		global $wpdb;

		$this->wp_id();

		$sql = "SELECT wp_id AS ID FROM {$wpdb->prefix}mymail_subscribers WHERE wp_id != 0";
		if(!is_null($limit)){
			$sql .= " LIMIT ".intval($offset).", ".intval($limit);
		}

		$user_ids = $wpdb->get_col($sql);

		foreach($user_ids as $user_id){
			$this->sync_wp_user($user_id);
		}

		return count($user_ids);

	}

	public function sync_wp_user( $user_id ){

		//WP User => Subscriber

		if(!mymail_option('sync')) return;

		$synclist = mymail_option('synclist', array());

		if(empty($synclist)) return;

		$user = get_user_by( 'id', $user_id );

		if(!$user) return;

		$subscriber = $this->get_by_wpid($user_id);

		if(!$subscriber) return;

		$userdata = array();

		foreach($synclist as $field => $meta){
			if(isset($user->data->{$meta})){
				$userdata[$field] = $user->data->{$meta};
			}else{
				$userdata[$field] = get_user_meta( $user_id, $meta, true );
			}
		}

		return $this->update( wp_parse_args(array('ID' => $subscriber->ID), $userdata), true, true);

	}

	public function sync_wp_user_meta( $meta_id, $user_id, $meta_key, $meta_value ){

		if(!mymail_option('sync')) return;

		$synclist = mymail_option('synclist', array());

		if(!in_array($meta_key, $synclist)) return;

		$subscriber = $this->get_by_wpid($user_id);

		if(!$subscriber) return;

		$key = array_search($meta_key, $synclist);

		if(is_array($meta_value)) $meta_value = end($meta_value);

		$this->add_custom_value($subscriber->ID, $key, (string) $meta_value);

	}

	public function view_subscribers( ) {

		if(isset($_GET['ID']) || isset($_GET['new'])) :

			include MYMAIL_DIR . 'views/subscribers/detail.php';

		else :

			include MYMAIL_DIR . 'views/subscribers/overview.php';

		endif;


	}

	public function output_referer($id){

		$subscriber = $this->get($id);
		if(!$subscriber) return;

		$meta = (object) $this->meta($subscriber->ID);
		$timeformat = get_option('date_format').' '.get_option('time_format');
		$timeoffset = mymail('helper')->gmt_offset(true);

		if($meta->referer) :

			switch($meta->referer){
				case 'import': ?>
		<strong><?php _e('via', 'mymail' ); ?></strong> <span><?php echo sprintf(__('import on %s', 'mymail'), date($timeformat, $subscriber->added+$timeoffset)) ?></span>
				<?php break;
				case 'wpuser':
				case '/wp-admin/user-new.php':
				case '/wp-login.php?action=register': ?>
		<strong><?php _e('via', 'mymail' ); ?></strong> <span><?php echo sprintf(__('WP user on %s', 'mymail'), date($timeformat, $subscriber->added+$timeoffset)) ?></span>
				<?php break;
				case 'backend': ?>
		<strong><?php _e('via', 'mymail' ); ?></strong> <span><?php echo sprintf(__('Backend on %s', 'mymail'), date($timeformat, $subscriber->added+$timeoffset)) ?></span>
				<?php break;
				case 'extern': ?>
		<strong><?php _e('via', 'mymail' ); ?></strong> <span><?php echo sprintf(__('an extern form on %s', 'mymail'), date($timeformat, $subscriber->added+$timeoffset)) ?></span>
				<?php break;
				case '/': ?>
		<strong><?php _e('via', 'mymail' ); ?></strong> <span><?php echo sprintf(__('Homepage on %s', 'mymail'), date($timeformat, $subscriber->added+$timeoffset)) ?></span>
				<?php break;
				default:
		if(preg_match('#^wpcomment_(\d+)#', $meta->referer, $match)) :
				$comment = get_comment($match[1]); ?>

		<strong><?php _e('via', 'mymail' ); ?></strong> <span><?php echo sprintf(__('%1$s on %2$s', 'mymail'), '<a href="'.get_permalink($comment->comment_post_ID ).'#comment-'.$comment->comment_ID.'">'.__( 'Comment', 'mymail' ).'</a>', date($timeformat, $subscriber->added+$timeoffset)) ?></span>
		<?php elseif(preg_match('#^https?://#', $meta->referer, $match)) : ?>

		<strong><?php _e('via', 'mymail' ); ?></strong> <a href="<?php echo $meta->referer ?>"><?php echo $meta->referer ?></a>

		<?php else : ?>

		<strong><?php _e('via', 'mymail' ); ?></strong> <?php echo $meta->referer ?>

		<?php endif; ?>
				<?php break;
			}
		if(isset($meta->form)):
			$form = mymail('forms')->get($meta->form, false, false);
		?>
		<br><strong><?php _e('Form', 'mymail') ?> #<?php echo $form->ID ?>:</strong> <a href="<?php echo admin_url('edit.php?post_type=newsletter&page=mymail_forms&ID='.$form->ID) ?>"><?php echo $form->name ?></a>
		<?php endif;

		endif;

	}

	public function edit_user_profile( $user ) {

		include MYMAIL_DIR . 'views/subscribers/user_edit.php';
	}

	public function screen_options( ) {

		require_once MYMAIL_DIR . 'classes/subscribers.table.class.php';

		$screen = get_current_screen();

		add_screen_option( 'per_page', array(
			'label' => __('Subscribers', 'mymail'),
			'default' => 50,
			'option' => 'mymail_subscribers_per_page'
		));

	}

	public function save_screen_options($status, $option, $value) {

		if ( 'mymail_subscribers_per_page' == $option ) return $value;

		return $status;

	}

	public function admin_notices( ) {

		if ( isset( $_GET['post_type'] ) && 'subscriber' == $_GET['post_type'] && isset( $_GET['post_status'] ) && 'error' == $_GET['post_status'] ) {

			echo '<div class="error"><p><strong><a href="'.add_query_arg(array('convert_errors' => 1, 'post_status' => 'subscribed', '_wpnonce' => wp_create_nonce('mymail_nonce'))).'">'.__('convert all subscribers with error status back to subscribed', 'mymail').'</a></strong></p></div>';
		}
	}

	public function remove_unassigned_meta() {

		global $wpdb;

		$wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_subscriber_fields AS a {$wpdb->prefix}mymail_subscribers AS s ON a.subscriber_id = s.ID WHERE s.ID IS NULL");
		$wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_subscriber_meta AS a {$wpdb->prefix}mymail_subscribers AS s ON a.subscriber_id = s.ID WHERE s.ID IS NULL");
		$wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_queue AS a {$wpdb->prefix}mymail_subscribers AS s ON a.subscriber_id = s.ID WHERE s.ID IS NULL");
		$wpdb->query("DELETE a FROM {$wpdb->prefix}mymail_lists_subscribers AS a {$wpdb->prefix}mymail_subscribers AS s ON a.subscriber_id = s.ID WHERE s.ID IS NULL");

	}

	public function update($entry, $overwrite = true, $merge = false, $subscriber_notification = false) {

		global $wpdb;

		$entry = (array) $entry;

		$bulkimport = defined('MYMAIL_DO_BULKIMPORT') && MYMAIL_DO_BULKIMPORT;

		if(isset($entry['email'])) $entry['email'] = trim(strtolower($entry['email']));
		if(isset($entry['email']) && !mymail_is_email( $entry['email'] )) return new WP_Error('invalid_email', __('invalid email address', 'mymail'));

		$field_names = array('ID'=>'%d','hash'=>'%s','email'=>'%s','status'=>'%d','added'=>'%d','signup'=>'%d','confirm'=>'%d','updated'=>'%d','ip_signup'=>'%s','ip_confirm'=>'%s','wp_id'=>'%d','rating'=>'%d');

		$now = time();

		$data = array();
		$meta = array();
		$customfields = array();

		$entry = apply_filters('mymail_verify_subscriber', $entry);
		if(is_wp_error( $entry )){
			return $entry;
		}else if($entry === false){
			return new WP_Error('not_verified', __('Subscriber failed verification', 'mymail'));
		}

		foreach($entry as $key => $value){
			if(isset($field_names[$key])){
				if($key == 'status' && $value == '-1') continue;
				$data[$key] = $value;
			}else if(in_array($key, $this->meta_keys)){
				$meta[$key] = $value;
			}else{
				$customfields[$key] = $value;
			}
		}

		ksort($data);
		ksort($customfields);
		ksort($field_names);

		$usedfields = array_keys( $data );

		$sql = "INSERT INTO {$wpdb->prefix}mymail_subscribers (".implode(', ', $usedfields).")";

		$sql .= " VALUES (".implode(', ', array_values(array_intersect_key($field_names, array_flip($usedfields)))).")";

		if($overwrite){
			$sql .= " ON DUPLICATE KEY UPDATE updated = $now";
			foreach($usedfields as $field)
					$sql .= ", $field = values($field)";
		}

		$sql = $wpdb->prepare($sql, $data);


		$wpdb->suppress_errors();

		if(false !== $wpdb->query($sql)){

			if(!$bulkimport) mymail_cache_delete( 'subscribers' );

			$subscriber_id = !empty($wpdb->insert_id) ? $wpdb->insert_id : intval($data['ID']);

			if(isset($meta['ip']) && $meta['ip'] && 'unknown' !== ($geo = mymail_ip2City($meta['ip']))){

				$meta['geo'] = $geo->country_code.'|'.$geo->city;
				if($geo->city){
					$meta['coords'] = floatval($geo->latitude).','.floatval($geo->longitude);
					$meta['timeoffset'] = intval($geo->timeoffset);
				}

			}

			$this->add_custom_value($subscriber_id, $customfields, NULL, !$merge);
			$this->update_meta($subscriber_id, 0, $meta);

			//not on bulk import
			if(!$bulkimport){

				if(isset($data['wp_id'])){
					$this->sync_wp_user($data['wp_id']);
				}else{
					$this->wp_id($subscriber_id);
					$this->sync_subscriber($subscriber_id);
				}

				if(isset($data['status'])){
					if($data['status'] == 0) $this->send_confirmations($subscriber_id, true);
					if($data['status'] == 1 && $subscriber_notification) $this->subscriber_notification($subscriber_id);
				}


			}

			do_action('mymail_update_subscriber', $subscriber_id);

			return $subscriber_id;

		}else{

			return new WP_Error('email_exists', $wpdb->last_error);
		}

	}

	public function add($entry, $overwrite = false, $merge = false) {

		$now = time();

		$entry = is_string($entry) ? (object) array('email' => $entry) : (object) $entry;

		$entry = (array) $entry;

		$entry = wp_parse_args( $entry, array(
			'hash' => $this->hash($entry['email']),
			'added' => $now,
			'signup' => $now,
			'updated' => $now,
			'ip' => NULL,
			'referer' => $_SERVER['REQUEST_URI'],
		));

		if(isset($entry['status']) && $entry['status'] == -1){
			unset($entry['status']);
		}else if(!isset($entry['status'])){
			$entry['status'] = 1;
		}

		if(!isset($entry['confirm']))
			$entry['confirm'] = (isset($entry['status']) && $entry['status'] == 1) ? $now : NULL;

		if(mymail_option('track_users') && $entry['ip'] !== false){

			$ip = mymail_get_ip();

			$entry = wp_parse_args( $entry, array(
				'ip' => $ip,
				'ip_signup' => $ip,
				'ip_confirm' => (isset($entry['status']) && $entry['status'] == 1) ? $ip : NULL,
			));

		}

		return $this->update($entry, $overwrite, $merge, true);

	}

	public function merge($entry) {

		$subscriber_id = $this->add($entry);

		//user exists
		if(is_wp_error( $subscriber_id )){

			$subscriber_id = $this->update($entry, true, true);

		}

		return $subscriber_id;

	}

	public function add_from_wp_user($user_id, $userdata = array()) {

		$user = get_userdata($user_id);
		if(!$user) new WP_Error('no_user', __('User does not exist!', 'mymail'));
		$email = $user->data->user_email;

		$subscriber_exists = $this->get_by_mail($email);

		if($subscriber_exists) new WP_Error('subscriber_exists', __('Subscriber already exists', 'mymail'));

		$first_name = get_user_meta( $user_id, 'first_name', true );
		$last_name = get_user_meta( $user_id, 'last_name', true );

		if (!$first_name) $first_name = $user->data->display_name;

		if(!isset($userdata['status'])){
			$form = mymail('forms')->get(1);

			$userdata['status'] = ($form->doubleoptin && mymail_option('register_other_confirmation')) ? 0 : 1;
		}

		$userdata = wp_parse_args($userdata, array(
			'email' => $email,
			'wp_id' => $user_id,
			'firstname' => $first_name,
			'lastname' => $last_name,
		));

		$subscriber_id = $this->add($userdata, true);

		return $subscriber_id;

	}

	public function add_custom_value($subscriber_id, $key, $value = NULL, $clear = false) {

		global $wpdb;

		$fields = !is_array($key) ? array($key => $value) : $key;

		if(!($count = count($fields))) return true;

		if($clear)
		 	$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}mymail_subscriber_fields WHERE subscriber_id = %d", $subscriber_id));

		$sql = "INSERT INTO {$wpdb->prefix}mymail_subscriber_fields
		(subscriber_id, meta_key, meta_value) VALUES ";

		$inserts = array();
		$british_date_format = get_option('date_format') == 'd/m/Y';

		$customfields = mymail()->get_custom_fields();

		foreach($fields as $key => $value){
			if(isset($customfields[$key]) && $customfields[$key]['type'] == 'date' && $value){
				if($british_date_format){
					if(preg_match('#(\d{1,2})/(\d{1,2})/(\d{2,4})#', $value, $d)) $value = $d[3].'-'.$d[2].'-'.$d[1];
				}
				$timestamp = is_numeric($value) ? strtotime('@'.$value) : strtotime(''.$value);
				$value = $timestamp !== false ? date('Y-m-d', $timestamp) : date('Y-m-d', strtotime($value));
			}

			if($value != '') $inserts[] = $wpdb->prepare('(%d, %s, %s)', $subscriber_id, $key, $value);
		}

		if(empty($inserts)) return true;

		$sql .= implode(', ', $inserts);

		$sql .= " ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id), meta_key = values(meta_key), meta_value = values(meta_value)";

		return false !== $wpdb->query($sql);

	}

	public function assign_lists($subscriber_ids, $lists, $remove_old = false) {

		global $wpdb;

		$subscriber_ids = !is_array($subscriber_ids) ? array(intval($subscriber_ids)) : array_filter($subscriber_ids, 'is_numeric');
		if(!is_array($lists)) $lists = array(intval($lists));

		if($remove_old) $this->unassign_lists($subscriber_ids, NULL, $lists);

		return mymail('lists')->assign_subscribers($lists, $subscriber_ids);

	}

	public function unassign_lists($subscriber_ids, $lists = NULL, $not_list = NULL) {

		global $wpdb;

		$subscriber_ids = !is_array($subscriber_ids) ? array(intval($subscriber_ids)) : array_filter($subscriber_ids, 'is_numeric');

		$sql = "DELETE FROM {$wpdb->prefix}mymail_lists_subscribers WHERE subscriber_id IN (".implode(', ', $subscriber_ids).")";

		if(!is_null($lists) && !empty($lists)){
			if(!is_array($lists)) $lists = array($lists);
			$sql .= " AND list_id IN (".implode(', ', array_filter($lists, 'is_numeric')).")";
		}
		if(!is_null($not_list) && !empty($not_list)){
			if(!is_array($not_list)) $not_list = array($not_list);
			$sql .= " AND list_id NOT IN (".implode(', ', array_filter($not_list, 'is_numeric')).")";
		}

		if(false !== $wpdb->query($sql)){
			do_action('mymail_unassign_lists', $subscriber_ids, $lists, $not_list );

			return true;
		}

		return false;

	}

	public function remove($subscriber_ids) {

		global $wpdb;

		$subscriber_ids = !is_array($subscriber_ids) ? array(intval($subscriber_ids)) : array_filter($subscriber_ids, 'is_numeric');

		if($delete_wp_user = (mymail_option('delete_wp_user') && current_user_can('delete_users'))){
			$current_user_id = get_current_user_id();
			$wp_ids_to_delete = $wpdb->get_col($wpdb->prepare("SELECT wp_id FROM {$wpdb->prefix}mymail_subscribers WHERE wp_id != 0 AND ID IN (".implode(',', $subscriber_ids).") AND wp_id != %d", $current_user_id));
		}

		//delete from subscribers, lists_subscribers, subscriber_fields, subscriber_meta, queue

		$sql = "DELETE a,b,c,d,e,f FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS b ON ( a.ID = b.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS c ON ( a.ID = c.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_actions AS d ON ( a.ID = d.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS e ON ( a.ID = e.subscriber_id ) LEFT JOIN {$wpdb->prefix}mymail_queue AS f ON ( a.ID = f.subscriber_id ) WHERE a.ID IN (".implode(',', $subscriber_ids).")";

		if($success = (false !== $wpdb->query($sql))){
			if($wpdb->last_error) return new WP_Error('db_error', $wpdb->last_error);
			if($delete_wp_user){

				remove_action('deleted_user' , array( &$this, 'delete_subscriber_from_wpuser' ), 10, 2 );

				foreach ($wp_ids_to_delete as $wp_id) {
					$user = new WP_User( $wp_id );
					if(!in_array($user->roles[0], array('administrator')))
						wp_delete_user( $wp_id );
				}

				add_action('deleted_user' , array( &$this, 'delete_subscriber_from_wpuser' ), 10, 2 );
			}

		}

		return $success;

	}

	public function delete_subscriber_from_wpuser($user_id) {

		$subscriber = $this->get_by_wpid($user_id);
		if(!$subscriber) return;

		if(mymail_option('delete_wp_subscriber')){
			$this->remove($subscriber->ID);
		}else{
			$this->update(array('ID' => $subscriber->ID, 'wp_id' => 0));
		}

	}

	public function meta($id, $key = NULL, $campaign_id = 0) {

		global $wpdb;

		if(false === ($meta = mymail_cache_get( 'subscriber_meta_'.$id.$campaign_id ))){

			$default = array_fill_keys($this->meta_keys, NULL);
			//$default['timeoffset'] = NULL;

			$sql = "SELECT a.* FROM {$wpdb->prefix}mymail_subscriber_meta AS a WHERE a.subscriber_id = %d AND a.campaign_id = %d";

			$result = $wpdb->get_results($wpdb->prepare($sql, $id, $campaign_id));
			$meta = array();

			foreach($result as $row){
				if(!isset($meta[$row->subscriber_id])) $meta[$row->subscriber_id] = $default;

				$meta[$row->subscriber_id][$row->meta_key] = $row->meta_value;
			}

			mymail_cache_add( 'subscriber_meta_'.$id.$campaign_id, $meta );

		}

		if(is_null($key)) return isset($meta[$id]) ? $meta[$id] : $default;

		return isset($meta[$id]) && isset($meta[$id][$key]) ? $meta[$id][$key] : (isset($default[$key]) ? $default[$key] : NULL);

	}

	public function update_meta($id, $campaign_id = 0, $key, $value = NULL) {

		global $wpdb;

		$meta = is_array($key) ? (array) $key : array($key => $value);

		if(empty($meta)) return true;

		$oldmeta = $this->meta($id);

		$insert = array_intersect_key( (array) $meta, array_flip( $this->meta_keys ));

		$sql = "INSERT INTO {$wpdb->prefix}mymail_subscriber_meta (subscriber_id, campaign_id, meta_key, meta_value)";

		$sql .= " VALUES ";

		$inserts = array();

		foreach($insert as $key => $value){
			//new value is empty and old value is NOT empty
			if(!$value && $oldmeta[$key]){
				//delete that row
				$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}mymail_subscriber_meta WHERE subscriber_id = %d AND meta_key = %s", $id, $key));
				continue;
			}
			$single_meta = in_array($key, $this->single_meta_keys);
			if(!$single_meta)
				$inserts[] = $wpdb->prepare("(%d, %d, %s, %s)", $id, $campaign_id, $key, $value);

			if($campaign_id || $single_meta)
				$inserts[] = $wpdb->prepare("(%d, %d, %s, %s)", $id, 0, $key, $value);

			$oldmeta[$id][$key] = $value;
		}

		$sql .= implode(', ', $inserts);

		$sql .= $wpdb->prepare(" ON DUPLICATE KEY UPDATE subscriber_id = %d, campaign_id = values(campaign_id), meta_key = values(meta_key), meta_value = values(meta_value)", $id);

		if(empty($inserts) || false !== $wpdb->query($sql)){

			mymail_cache_delete( 'subscriber_meta_'.$id.$campaign_id );

			return true;

		}

		return false;

	}

	public function update_rate($ids) {

		global $wpdb;

		if(!is_array($ids)) $ids = array($ids);

		foreach ($ids as $id) {
			$actions = mymail('actions')->get_by_subscriber($id, NULL, false, true);
			$rate = 0.25;
			if($this->get_sent($id)){
				$openrate = $this->get_open_rate($id);
				$aclickrate = $this->get_adjusted_click_rate($id);
				$rate = max($rate, ($openrate+$aclickrate)/2);

				if($actions['softbounces']) $rate -= ($actions['softbounces']/20);
				if($actions['bounces']) $rate -= ($actions['bounces']/5);
				if($actions['unsubscribes']) $rate -= 0.3;


				$rate = max(0.1, $rate);

			}
			$sql = "UPDATE {$wpdb->prefix}mymail_subscribers AS a SET a.rating = %f WHERE a.ID = %d AND a.rating != %f";
			$wpdb->query($wpdb->prepare($sql, $rate, $id, $rate));

		}

		return;

	}

	public function wp_id($ids = NULL) {

		global $wpdb;

		if(!is_null($ids) && !is_array($ids)) $ids = array($ids);

		$listids = isset($d['lists']) ? array_filter($d['lists'], 'is_numeric') : array();

		$sql = "UPDATE {$wpdb->prefix}mymail_subscribers AS a INNER JOIN {$wpdb->users} AS b ON a.email = b.user_email";
		if(is_array($ids)) $sql .= " AND (a.ID IN (".implode(',', array_filter($ids, 'is_numeric')).") OR b.ID IN (".implode(',', array_filter($ids, 'is_numeric'))."))";
		$sql .= " SET a.wp_id = b.ID";

		if(false !== $wpdb->query($sql))
			return $wpdb->rows_affected;

		return false;

	}

	public function get_count($formated = false, $round = 1, $status = 1){

		$count = $this->get_count_by_status($status);

		if($round > 1) $count = ceil($count/$round)*$round;

		if($formated === 'kilo'){
			$count = ($count > 10000) ? (round($count/1000, 1)).'K' : number_format_i18n( $count );
		}else if($formated){
			$count = number_format_i18n( $count );
		}

		return $count;

	}

	public function get_count_by_status($status = NULL){

		global $wpdb;

		if(false === ($counts = mymail_cache_get( 'get_count_by_status' ))){

			$sql = "SELECT status, COUNT( * ) AS count FROM {$wpdb->prefix}mymail_subscribers GROUP BY status";

			$result = $wpdb->get_results($sql);
			$counts = array();

			foreach($result as $row){
				$counts[$row->status] = $row->count;
			}

			mymail_cache_add( 'get_count_by_status', $counts );

		}

		if(is_string($status)) $status = $this->get_status_by_name($status);

				//return all
		return (is_null($status)) ? $counts :
				//only defined ones (array)
				(is_array($status) ? array_intersect_key($counts, array_flip($status)) :
				//only a specific if set
				(isset($counts[$status]) ? $counts[$status] : 0 ));

	}

	public function get_totals($status = NULL) {

		$statuses = !is_null($status) ? (!is_array($status) ? array($status) : $status) : NULL;

		$counts = $this->get_count(false, 1, $statuses);

		return is_array($counts) ? array_sum($counts) : $counts;

	}

	public function get_sent($id, $total = false) {

		return mymail('actions')->get_by_subscriber($id, 'sent'.($total ? '_total' : '' ), true);

	}

	public function get_sent_campaigns($id, $ids_only = false) {
		global $wpdb;

		$sql = "SELECT p.post_title AS campaign_title, a.* FROM {$wpdb->prefix}mymail_actions AS a LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id WHERE a.subscriber_id = %d AND a.type = 1";

		$campaigns = $wpdb->get_results($wpdb->prepare($sql, $id));

		return $ids_only ? wp_list_pluck( $campaigns, 'campaign_id' ) : $campaigns;

	}

	public function get_opens($id, $total = false) {

		return mymail('actions')->get_by_subscriber($id, 'opens'.($total ? '_total' : '' ), true);

	}

	public function get_opened_campaigns($id, $ids_only = false) {

		global $wpdb;

		$sql = "SELECT p.post_title AS campaign_title, a.* FROM {$wpdb->prefix}mymail_actions AS a LEFT JOIN {$wpdb->postmeta} AS c ON c.post_id = a.campaign_id AND c.meta_key = '_mymail_autoresponder' LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id WHERE a.subscriber_id = %d AND c.meta_value = '' AND a.type = 2";

		$campaigns = $wpdb->get_results($wpdb->prepare($sql, $id));

		return $ids_only ? wp_list_pluck( $campaigns, 'campaign_id' ) : $campaigns;

	}

	public function get_open_rate($id = NULL, $total = false){

		$sent = $this->get_sent($id, $total);
		if(!$sent) return 0;

		$opens = $this->get_opens($id);

		return $opens / $sent;

	}

	public function get_clicks($id, $total = false) {

		return mymail('actions')->get_by_subscriber($id, 'clicks'.($total ? '_total' : '' ), true);

	}

	public function get_click_rate($id = NULL, $total = false){

		$sent = $this->get_sent($id, $total);
		if(!$sent) return 0;

		$clicks = $this->get_clicks($id, $total);

		return $clicks / $sent;

	}

	public function get_adjusted_click_rate($id = NULL, $total = false){

		$open = $this->get_opens($id, $total);
		if(!$open) return 0;

		$clicks = $this->get_clicks($id, $total);

		return $clicks / $open;

	}

	public function get_clicked_campaigns($id, $ids_only = false) {

		global $wpdb;

		$sql = "SELECT p.post_title AS campaign_title, a.* FROM {$wpdb->prefix}mymail_actions AS a LEFT JOIN {$wpdb->postmeta} AS c ON c.post_id = a.campaign_id AND c.meta_key = '_mymail_autoresponder' LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id WHERE a.subscriber_id = %d AND c.meta_value = '' AND a.type = 3";

		$campaigns = $wpdb->get_results($wpdb->prepare($sql, $id));

		return $ids_only ? wp_list_pluck( $campaigns, 'campaign_id' ) : $campaigns;

	}

	public function get_activity($id, $limit = NULL, $exclude = NULL) {

		return mymail('actions')->get_activity(NULL, $id, $limit, $exclude);

	}

	public function get_clients($id) {

		global $wpdb;

		$sql = "SELECT COUNT(a.meta_value) AS count, a.meta_value AS name, b.meta_value AS type FROM {$wpdb->prefix}mymail_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id WHERE a.meta_key = 'client' AND b.meta_key = 'clienttype' AND a.subscriber_id = %d AND a.campaign_id != 0 GROUP BY a.meta_value ORDER BY count DESC";

		$result = $wpdb->get_results($wpdb->prepare($sql, $id));

		$total = !empty($result) ? array_sum(wp_list_pluck($result, 'count' )) : 0;

		foreach($result as $i => $row){
			$result[$i] = array(
				'name' => $row->name,
				'type' => $row->type,
				'count' => $row->count,
				'percentage' => $row->count/$total,
			);
		}

		return $result;

	}

	public function open_time($id) {
		return $this->compare($id, 1, 2);
	}

	public function click_time($id, $since_open = true) {
		return $this->compare($id, $since_open ? 2 : 1, 3);
	}

	public function compare($id, $actionA, $actionB) {

		global $wpdb;

		$sql = "SELECT (b.timestamp - a.timestamp) AS time FROM {$wpdb->prefix}mymail_actions AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id WHERE a.type = %d AND b.type = %d AND a.subscriber_id = %d GROUP BY a.subscriber_id, a.campaign_id ORDER BY a.timestamp ASC, b.timestamp ASC";

		$times = $wpdb->get_col($wpdb->prepare($sql, $actionA, $actionB, $id));
		if(empty($times)) return false;

		$average = array_sum($times) / count($times);

		return $average;

	}

	public function get_lists($id, $ids_only = false) {

		global $wpdb;

		$cache = mymail_cache_get( 'subscribers_lists' );
		if(isset($cache[$id])) return $cache[$id];

		$sql = "SELECT a.* FROM {$wpdb->prefix}mymail_lists AS a LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON a.ID = ab.list_id WHERE ab.subscriber_id = %d";

		$lists = $wpdb->get_results($wpdb->prepare($sql, $id));

		return $ids_only ? wp_list_pluck( $lists, 'ID' ) : $lists;

	}

	public function get_unassigned() {

		global $wpdb;

		$custom_fields = mymail()->get_custom_fields(true);

		$sql = "SELECT a.".implode(', a.', $fields).", ab.list_id";

		foreach($custom_fields as $i => $name){
			$sql .= ", meta_$i.meta_value AS '$name'";
		}

		$sql .= " FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN ({$wpdb->prefix}mymail_lists AS b INNER JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON b.ID = ab.list_id) ON a.ID = ab.subscriber_id";

		foreach($custom_fields as $i => $name){
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS meta_$i ON meta_$i.subscriber_id = a.ID AND meta_$i.meta_key = '$name'";
		}

		$sql .= " WHERE a.status IN (".implode(',', $stati).") AND ab.list_id IN (".implode(',', $listids).") GROUP BY ab.list_id, a.ID LIMIT $offset, $limit";

		return $wpdb->get_results($sql);

	}

	public function unsubscribe($id, $campaign_id = NULL, $logit = true) {

		return $this->unsubscribe_by_type('id', $id, $campaign_id, $logit);

	}

	public function unsubscribe_by_hash($hash, $campaign_id = NULL, $logit = true) {

		return $this->unsubscribe_by_type('hash', $hash, $campaign_id, $logit);

	}

	public function unsubscribe_by_mail($email, $campaign_id = NULL, $logit = true) {

		return $this->unsubscribe_by_type('email', $email, $campaign_id, $logit);

	}

	private function unsubscribe_by_type($type, $value, $campaign_id = NULL, $logit = true) {

		global $wpdb;

		switch($type){
			case 'id':
			$subscriber = $this->get(intval($value), false);
				break;
			case 'hash':
			$subscriber = $this->get_by_hash($value, false);
				break;
			case 'email':
			$subscriber = $this->get_by_mail($value, false);
				break;
		}

		if(!$subscriber) return false;

		if($subscriber->status == 2) return true;

		if($this->update(array(
				'ID' => $subscriber->ID,
				'status' => 2
			))){

			do_action('mymail_unsubscribe', $subscriber->ID, $campaign_id);

			$this->subscriber_unsubscribe_notification($subscriber->ID);

			return true;

		}

		return false;

	}

	public function subscriber_notification($id, $timestamp = NULL){

		if(defined('MYMAIL_DO_BULKIMPORT') && MYMAIL_DO_BULKIMPORT) return false;

		if(!mymail_option('subscriber_notification') || !mymail_option('subscriber_notification_receviers')) return false;

		$subscriber = $this->get($id);
		if(!$subscriber) return;

		if(!$timestamp) $timestamp = time();

		return mymail('notification')->add($timestamp, array(
			'template' => 'new_subscriber',
			'subscriber_id' => $subscriber->ID
		));


	}

	public function subscriber_unsubscribe_notification($id, $timestamp = NULL){

		if(defined('MYMAIL_DO_BULKIMPORT') && MYMAIL_DO_BULKIMPORT) return false;

		if(!mymail_option('unsubscribe_notification') || !mymail_option('unsubscribe_notification_receviers')) return false;

		$subscriber = $this->get($id);
		if(!$subscriber) return;

		if(!$timestamp) $timestamp = time();

		return mymail('notification')->add($timestamp, array(
			'template' => 'unsubscribe',
			'subscriber_id' => $subscriber->ID
		));


	}

	public function send_confirmations($ids = NULL, $force = false, $now = false) {

		global $wpdb;

		//get the very first form ID
		$fallback_form_id = (int) $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}mymail_forms ORDER BY ID ASC LIMIT 1");

		//get all pending subscribers which are not queued already
		$sql = "SELECT a.ID, a.signup, IFNULL( b.meta_value, 0 ) AS try, f.resend, f.resend_count, f.resend_time, IFNULL( f.ID, $fallback_form_id ) AS form_id FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS b ON a.ID = b.subscriber_id AND b.meta_key = 'confirmation' LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS c ON a.ID = c.subscriber_id AND c.meta_key = 'form' LEFT JOIN {$wpdb->prefix}mymail_queue AS d ON a.ID = d.subscriber_id AND d.campaign_id = 0 LEFT JOIN {$wpdb->prefix}mymail_forms AS f ON f.ID = IFNULL( c.meta_value, $fallback_form_id ) WHERE a.status = 0 AND ( d.subscriber_id IS NULL OR d.sent != 0 )";

		if(!$force) $sql .= " AND f.resend = 1 AND IFNULL( b.meta_value, 0 ) <= f.resend_count";

		if(!is_null($ids)){
			$ids = !is_array($ids) ? array(intval($ids)) : array_filter($ids, 'is_numeric');
			$sql .= " AND a.ID IN (".implode(',', $ids).")";
		}

		$entries = $wpdb->get_results($sql);

		$count = 0;

		foreach($entries as $entry){

			$timestamp = $now ? time() : max(time(), $entry->signup)+($entry->resend_time*3600*$entry->try);

			if(mymail('notification')->add($timestamp, array(
				'subscriber_id' => $entry->ID,
				'template' => 'confirmation',
				'form' => $entry->form_id,
			))){
				$this->update_meta($entry->ID, 0, 'confirmation', ++$entry->try);
				$count++;
			}

		}

		return $count;

	}

	public function remove_pending_confirmations($subscriber_id = NULL){

		global $wpdb;

		//delete confirmation option and all pending confirmations
		$sql = "DELETE a,b FROM {$wpdb->prefix}mymail_queue AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND b.meta_key = 'confirmation' WHERE a.campaign_id = 0 AND a.options LIKE '%s:8:\"template\";s:12:\"confirmation\";%'";

		if(!is_null($subscriber_id)) $sql .= " AND a.subscriber_id = ".intval($subscriber_id);

		return false !== $wpdb->query($sql);
	}

	public function get($ID = null, $custom_fields = false) {

		global $wpdb;

		if(is_numeric($ID))	return $this->get_by_type('ID', $ID, $custom_fields);

		$args = wp_parse_args( $ID, array(
			'limit' => 0,
			'offset' => 100,
			'orderby' => 'ID',
			'order' => 'DESC',
		));

		extract($args);

		$custom_field_names = $custom_fields ? mymail()->get_custom_fields(true): array();

		$sql = "SELECT a.*";

		if($custom_fields) $sql .= ", firstname.meta_value AS firstname, lastname.meta_value AS lastname, TRIM(CONCAT(IFNULL(firstname.meta_value, ''), ' ', IFNULL(lastname.meta_value, ''))) as fullname";

		foreach($custom_field_names as $i => $name){
			$sql .= ", meta_$i.meta_value AS '$name'";
		}

		$sql .= " FROM {$wpdb->prefix}mymail_subscribers as a";
		if($custom_fields){
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS firstname ON firstname.subscriber_id = a.ID AND firstname.meta_key = 'firstname'";
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS lastname ON lastname.subscriber_id = a.ID AND lastname.meta_key = 'lastname'";
		}

		foreach($custom_field_names as $i => $name){
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS meta_$i ON meta_$i.subscriber_id = a.ID AND meta_$i.meta_key = '$name'";
		}

		$sql .= " WHERE 1 ORDER BY $orderby $order LIMIT $offset, $limit";

		$result = $wpdb->get_results($sql);
		foreach ($result as $i => $subscriber) {
			$result[$i]->ID = (int) $subscriber->ID;
			$result[$i]->wp_id = (int) $subscriber->wp_id;
			$result[$i]->status = (int) $subscriber->status;
			$result[$i]->added = (int) $subscriber->added;
			$result[$i]->updated = (int) $subscriber->updated;
			$result[$i]->signup = (int) $subscriber->signup;
			$result[$i]->confirm = (int) $subscriber->confirm;
			$result[$i]->rating = (float) $subscriber->rating;
		}

		return $result;

	}

	public function get_by_mail($mail, $custom_fields = false) {

		return $this->get_by_type('email', $mail, $custom_fields);

	}

	public function get_by_hash($hash, $custom_fields = false) {

		return $this->get_by_type('hash', $hash, $custom_fields);
	}

	public function get_by_wpid($wpid, $custom_fields = false) {

		return $this->get_by_type('wp_id', $wpid, $custom_fields);
	}

	private function get_by_type($type, $value, $custom_fields = false) {

		global $wpdb;

		$custom_field_names = $custom_fields ? mymail()->get_custom_fields(true): array();

		$cache = array();
		if($type == 'ID'){
			$value = intval($value);
			$cache = mymail_cache_get( 'subscribers' );
			if(isset($cache[$value])) return $cache[$value];
		}else{
			$value = esc_sql($value);
		}

		$sql = "SELECT a.*";

		if($custom_fields) $sql .= ", firstname.meta_value AS firstname, lastname.meta_value AS lastname, TRIM(CONCAT(IFNULL(firstname.meta_value, ''), ' ', IFNULL(lastname.meta_value, ''))) as fullname";

		foreach($custom_field_names as $i => $name){
			$sql .= ", meta_$i.meta_value AS '$name'";
		}

		$sql .= " FROM {$wpdb->prefix}mymail_subscribers as a";
		if($custom_fields){
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS firstname ON firstname.subscriber_id = a.ID AND firstname.meta_key = 'firstname'";
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS lastname ON lastname.subscriber_id = a.ID AND lastname.meta_key = 'lastname'";
		}

		foreach($custom_field_names as $i => $name){
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS meta_$i ON meta_$i.subscriber_id = a.ID AND meta_$i.meta_key = '$name'";
		}

		$sql .= " WHERE a.$type = '".($value)."' LIMIT 0, 1";

		if(!($subscriber = $wpdb->get_row($sql))) return false;

		$subscriber->ID = (int) $subscriber->ID;
		$subscriber->wp_id = (int) $subscriber->wp_id;
		$subscriber->status = (int) $subscriber->status;
		$subscriber->added = (int) $subscriber->added;
		$subscriber->updated = (int) $subscriber->updated;
		$subscriber->signup = (int) $subscriber->signup;
		$subscriber->confirm = (int) $subscriber->confirm;
		$subscriber->rating = (float) $subscriber->rating;

		$cache[$subscriber->ID] = $subscriber;

		if($custom_fields) mymail_cache_set( 'subscribers', $cache );

		return $subscriber;

	}

	public function get_custom_field($subscriber_id, $custom_fields = NULL) {

		$subscriber = $this->get($subscriber_id, true);

		if(is_null($custom_fields)){
			$custom_fields = mymail()->get_custom_fields(true);
		}else if(!is_array($custom_fields)){
			return isset($subscriber->{$custom_fields}) ? $subscriber->{$custom_fields} : NULL;
		}

		return array_intersect_key( (array) $subscriber, array_flip( array_keys( array_flip( $custom_fields ) ) ) );

	}

	public function get_empty($custom_fields = false) {

		global $wpdb;

		$fields = wp_parse_args(array(
			'firstname', 'lastname', 'fullname'
		), $wpdb->get_col("DESCRIBE {$wpdb->prefix}mymail_subscribers"));

		if(!$custom_fields){
			$fields = wp_parse_args( mymail()->get_custom_fields(true), $fields );
		}

		$subscriber = (object) array_fill_keys(array_values($fields), NULL);

		$subscriber->status = 1;

		return $subscriber;

	}

	public function get_confirm_link($id, $form_id = NULL) {

		$subscriber = $this->get($id);
		if(!$subscriber) return '';

		if(is_null($form_id)) $form_id = '';

		$baselink = get_permalink( mymail_option('homepage') );

		$slugs = mymail_option('slugs');
		$slug = isset($slugs['confirm']) ? $slugs['confirm'] : 'confirm';

		$link = (mymail('helper')->using_permalinks())
			? trailingslashit($baselink).trailingslashit( $slug.'/'.$subscriber->hash.'/'.$form_id )
			: add_query_arg(array(
			'confirm' => '',
			'k' => $subscriber->hash,
			'f' => $form_id,
		), $baselink);

		return $link;

	}

	public function get_recipient_detail($id, $campaign_id){

		$subscriber = $this->get($id, true);

		$timeformat = get_option('date_format') . ' ' . get_option('time_format');
		$timeoffset = mymail('helper')->gmt_offset(true);

		$actions = (object) mymail('actions')->get_campaign_actions($campaign_id, $id, NULL, false);

		$meta = $this->meta($id, NULL, $campaign_id);

		$return['success'] = true;

		$html = '<div class="user_image" title="'.__('Source', 'mymail') .': Gravatar.com" data-email="'.$subscriber->email.'" style="background-image:url('.$this->get_gravatar_uri($subscriber->email, 240).')"></div>';

		$html .= '<div class="receiver-detail-data">';
		$html .= '<h4>'.($subscriber->fullname ? $subscriber->fullname : $subscriber->email).' <a href="edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber->ID.'">'.__('edit', 'mymail').'</a></h4>';
		$html .= '<ul>';

		$html .= '<li><label>'.__('sent', 'mymail').':</label> '.($actions->sent ? date($timeformat, $actions->sent+$timeoffset).', '.sprintf(__('%s ago', 'mymail'), human_time_diff($actions->sent)).($actions->sent_total > 1 ? ' <span class="count">('.sprintf(__('%d times', 'mymail'), $actions->sent_total).')</span>' : '') : __('not yet', 'mymail'));
		$html .= '<li><label>'.__('opens', 'mymail').':</label> '.($actions->opens ? date($timeformat, $actions->opens+$timeoffset).', '.sprintf(__('%s ago', 'mymail'), human_time_diff($actions->opens)).($actions->opens_total > 1 ? ' <span class="count">('.sprintf(__('%d times', 'mymail'), $actions->opens_total).')</span>' : '') : __('not yet', 'mymail'));

		$html .= '<p class="meta"><label>&nbsp;</label>';
		if($meta['client']) $html .= sprintf(__('with %s', 'mymail'), '<strong>'.$meta['client'].'</strong>');

		if($meta['geo']) :
			$geo = explode('|', $meta['geo']);
			if($geo[1]) $html .= ' '.sprintf(_x('in %s, %s', 'in [city], [country]', 'mymail'), '<strong>'.$geo[1].'</strong>', '<span class="mymail-flag flag-'.strtolower($geo[0]).'"></span> <strong>'.$geo[0].'</strong>');
		endif;
		$html .= '</p>';

		$html .= '</li>';
		if($actions->bounces) $html .= '<li><label class="red">'.sprintf( _n( '%s soft bounce', '%s soft bounces', $actions->softbounces_total, 'mymail'), $actions->softbounces_total).'</label> <strong class="red">'.sprintf(__('Hard bounced at %s', 'mymail'), date($timeformat, $actions->bounces+$timeoffset).', '.sprintf(__('%s ago', 'mymail'), human_time_diff($actions->bounces))).'</strong> </li>';

		if($actions->clicks){
			$html .= '<li><ul>';
			foreach($actions->clicks as $link => $count){
				$html .= '<li class=""><a href="'.$link.'" class="external clicked-link">'.$link.'</a> <span class="count">('.sprintf( _n( '%s click', '%s clicks', intval($count), 'mymail'), $count).')</span></li>';
			}
			$html .= '</ul></li>';
		}


		$html .= '</ul>';
		$html .= '</div>';

		return $html;

	}


	/*----------------------------------------------------------------------*/
	/* Plugin Activation / Deactivation
	/*----------------------------------------------------------------------*/



	public function on_activate($new) {

		if($new){
			$subscriber_id = $this->add_from_wp_user(get_current_user_id(), array(
				'status' => 1,
				'referer' => 'backend',
			));
		}

	}


	public function user_register($user_id) {

		//for third party plugins
		if(!apply_filters( 'mymail_user_register', true )) return;

		$is_register = isset($_POST['wp-submit']);

		if($is_register){

			if(!mymail_option('register_signup')) return false;
			//stop if not option
			if(!isset($_POST['mymail_user_newsletter_signup'])) return false;

			$status = mymail_option('register_signup_confirmation') ? 0 : 1;

			$referer = 'wp-login.php';

		}else{

			if(!mymail_option('register_other')) return false;

			$status = mymail_option('register_other_confirmation') ? 0 : 1;

			$roles = mymail_option('register_other_roles', array());

			$pass = false;

			foreach ($roles as $role) {
				if(user_can( $user_id, $role )){
					$pass = true;
					break;
				}
			}

			if(!$pass) return;

			$referer = NULL;

		}

		$subscriber_id = $this->add_from_wp_user($user_id, array(
			'status' => $status,
			'referer' => apply_filters('mymail_user_register_referer', $referer),
		));

		if(is_wp_error($subscriber_id)){

			return false;

		}else{

			$lists = $is_register ? mymail_option('register_signup_lists', array()) : mymail_option('register_other_lists', array());

			if(!empty($lists)) $this->assign_lists($subscriber_id, $lists);

			return true;
		}

	}

	public function register_form( ) {

		if(!mymail_option('register_signup')) return;

	?><p><label for="mymail_user_newsletter_signup"><input name="mymail_user_newsletter_signup" type="checkbox" id="mymail_user_newsletter_signup" value="1" <?php checked( mymail_option('register_signup_checked') ); ?> /> <?php echo mymail_text('newsletter_signup'); ?></label><br><br></p><?php
	}

	public function comment_form_checkbox( ) {

		if(!mymail_option('register_comment_form')) return;

		$commenter = wp_get_current_commenter();

		if(!empty($commenter['comment_author_email']) && $this->get_by_mail($commenter['comment_author_email'])) return;

		if(is_user_logged_in() && $this->get_by_wpid(get_current_user_id())) return;

		$field = '<p class="comment-form-newsletter-signup">';
		$field .= '<label for="mymail_newsletter_signup"><input name="newsletter_signup" type="checkbox" id="mymail_newsletter_signup" value="1" '.checked( mymail_option('register_comment_form_checked'), true, false ).'/> '.mymail_text('newsletter_signup').'</label>';
		$field .= '</p>';

		echo apply_filters( "comment_form_field_newsletter_signup", $field ) . "\n";

	}

	public function comment_post( $comment_id, $comment_approved ) {

		if(!mymail_option('register_comment_form')) return false;

		if(isset($_POST['newsletter_signup'])){

			if(in_array($comment_approved.'', mymail_option('register_comment_form_status', array()))){

				$comment = get_comment($comment_id);

				if($comment && !$this->get_by_mail($comment->comment_author_email)){

					$lists = apply_filters('mymail_comment_post_lists',
						mymail_option('register_comment_form_lists', array()),
					$comment, $comment_approved );

					$userdata = apply_filters('mymail_comment_post_userdata', array(
						'email' => $comment->comment_author_email,
						'status' => mymail_option('register_comment_form_confirmation') ? 0 : 1,
						'firstname' => $comment->comment_author,
						'referer' => 'wpcomment_'.$comment->comment_ID,
					), $comment, $comment_approved );

					$subscriber_id = $this->add($userdata);

					if($subscriber_id && !is_wp_error($subscriber_id) && !empty($lists)){
						$this->assign_lists($subscriber_id, $lists);
					}

				}

			}else if(!in_array($comment_approved.'', array('1', 'approve'))){
				add_comment_meta( $comment_id, 'newsletter_signup', true, true );
			}
		}

	}

	public function wp_set_comment_status( $comment_id, $comment_status ) {

		if(!mymail_option('register_comment_form') || !in_array($comment_status.'', array('1', 'approve'))) return false;

		if(get_comment_meta( $comment_id, 'newsletter_signup', true )){

			$comment = get_comment($comment_id);

			if(!$this->get_by_mail($comment->comment_author_email)){

				$lists = apply_filters('mymail_comment_post_lists',
					mymail_option('register_comment_form_lists', array()),
				$comment, $comment_approved );

				$userdata = apply_filters('mymail_comment_post_userdata', array(
					'email' => $comment->comment_author_email,
					'status' => mymail_option('register_comment_form_confirmation') ? 0 : 1,
					'firstname' => $comment->comment_author,
					'referer' => 'wpcomment_'.$comment->comment_ID,
					'signup' => strtotime($comment->comment_date_gmt),
				), $comment, $comment_approved );

				$subscriber_id = $this->add($userdata);

				if($subscriber_id && !is_wp_error($subscriber_id) && !empty($lists)){
					$this->assign_lists($subscriber_id, $lists);
				}

				delete_comment_meta( $comment_id, 'newsletter_signup' );

			}

		}

	}


	/*----------------------------------------------------------------------*/
	/*
	/*----------------------------------------------------------------------*/


	public function bounce($subscriber_id, $campaign_id, $hard = false, $status = NULL) {

		global $wpdb;

		$subscriber = $this->get($subscriber_id, false);
		if(!$subscriber) return false;
		$campaign = mymail('campaigns')->get($campaign_id);
		if(!$campaign) $campaign_id = 0;

		if($hard){

			if($this->change_status($subscriber->ID, $this->get_status_by_name('hardbounced'))){
				do_action('mymail_bounce', $subscriber->ID, $campaign_id, true, $status);
				if($status) $this->update_meta( $subscriber->ID, $campaign_id, 'bounce', $status );
				return true;
			}

			return false;
		}

		//soft bounce
		$bounce_attempts = mymail_option('bounce_attempts');

		//check if bounce limit has been reached => hardbounce
		if($bounce_attempts == 1 || $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mymail_actions WHERE type = 5 AND subscriber_id = %d AND campaign_id = %d AND count >= %d LIMIT 1", $subscriber->ID, $campaign_id, $bounce_attempts))){

			return $this->bounce($subscriber->ID, $campaign_id, true, $status);

		}

		//softbounce
		do_action('mymail_bounce', $subscriber->ID, $campaign_id, false, $status);
		if($status) $this->update_meta( $subscriber->ID, $campaign_id, 'bounce', $status );

		return true;

	}

	public function get_sent_mails($id) {

		global $wpdb;

		if(false === ($counts = mymail_cache_get( 'get_sent_mails' ))){

			$sql = "SELECT status, COUNT( * ) AS count FROM {$wpdb->prefix}mymail_subscribers GROUP BY status";

			$result = $wpdb->get_results($sql);
			$counts = array();

			foreach($result as $row){
				$counts[$row->status] = $row->count;
			}

			mymail_cache_add( 'get_sent_mails', $counts );
		}

		return (is_null($id)) ? $counts : (isset($counts[$id]) ? $counts[$id] : 0 );

	}

	public function get_gravatar($id, $size = 120) {

		$subscriber = $this->get($id);
		return $this->get_gravatar_uri($subscriber->email, $size);

	}

	public function get_gravatar_uri($email, $size = 120) {

		$email = strtolower( trim( $email ) );
		$hash = md5( $email );
		//create a number from 01 to 09 based on the email address
		$id = '0'.(round(abs(crc32 ( $hash ))%9)+1);
		$default = (is_ssl() ? 'https:' : 'http:'). '//mymailapp.github.io/user/user'.$id.'.gif';

		$image = get_avatar( $email, $size, $default );
		preg_match("#src='([^']+)'#", $image, $match);

		if(!empty($match[1])){
			$url = $match[1];
		}else{
			$url = (is_ssl() ? 'https' : 'http')."://". ($id%2). "gravatar.com/avatar/" . $hash . "?d=" . urlencode( $default ) . "&s=".$size;
		}
		return $url;
	}

	public function get_status_by_name($name){

		$statuses = $this->get_status();
		$found = array_search($name, $statuses);

		if($found === false){
			$statuses = $this->get_status(NULL, true);
			$found = array_search($name, $statuses);
		}

		return $found;
	}

	public function get_status($id = NULL, $nice = false){

		if($nice){
			$statuses = array(
				__('pending', 'mymail'),
				__('subscribed', 'mymail'),
				__('unsubscribed', 'mymail'),
				__('hardbounced', 'mymail'),
				__('error', 'mymail')
			);

		}else{
			$statuses = array(
				'pending',
				'subscribed',
				'unsubscribed',
				'hardbounced',
				'error'
			);
		}

		return is_null($id) ? $statuses : (isset($statuses[$id]) ? $statuses[$id] : false);
	}

	public function get_userdata($data){

		$custom_field_names = mymail()->get_custom_fields(true);

		$userdatafields = wp_parse_args((array) $custom_field_names, array('firstname', 'lastname', 'fullname'));

		return (object) array_intersect_key((array) $data, array_flip( $userdatafields ));

	}

	public function get_metadata($data, $userdata){

		return (object) array_intersect_key( (array) $data, array_flip( array_keys( array_diff_key( (array) $data,  (array) $userdata ) ) ) );

	}

	public function get_timeoffset_timestamps($subscriber_ids, $timestamps = NULL){

		global $wpdb;

		if(!is_array($subscriber_ids)) $subscriber_ids = array($subscriber_ids);
		if(is_null($timestamps)) $timestamps = time();

		$subscriber_ids = array_filter($subscriber_ids, 'is_numeric');
		$timeoffset = mymail('helper')->gmt_offset(true);

		if(empty($subscriber_ids)) return array();

		if(is_array($timestamps)){
			$sql = "SELECT a.ID, IF(b.meta_value IS NULL,0,(-b.meta_value*3600+$timeoffset)) AS offset FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS b ON a.ID = b.subscriber_id AND b.meta_key = 'timeoffset' WHERE a.ID IN (".implode(',', $subscriber_ids).") ORDER BY a.ID ASC";

			$result = $wpdb->get_results($sql);

			$return = array();

			foreach($subscriber_ids as $i => $subscriber){
				if($result[$i]->ID == $subscriber){
					$return[$i] = $timestamps[$i]+$result[$i]->offset;
				}else{
					$return[$i] = $timestamps[$i];
				}
			}
		}else if(is_numeric($timestamps)){

			$chunks = array_chunk($subscriber_ids, 2000);
			$return = array();

			foreach($chunks as $subscriber_id_chunk){

				$sql = "SELECT IF(b.meta_value IS NULL,0,(-b.meta_value*3600+$timeoffset))+$timestamps AS timestamps FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS b ON a.ID = b.subscriber_id AND b.meta_key = 'timeoffset' WHERE a.ID IN (".implode(',', $subscriber_id_chunk).") ORDER BY a.ID ASC";

				$result = $wpdb->get_col($sql);

				$return = array_merge($return, $result);

			}
		}else{
			return array();
		}

		return $return;

	}

	public function change_status($subscriber_ids, $new_status, $silent = false) {

		global $wpdb;

		$subscriber_ids = !is_array($subscriber_ids) ? array(intval($subscriber_ids)) : array_filter($subscriber_ids, 'is_numeric');

		$count = 0;

		foreach($subscriber_ids as $subscriber_id){

			$subscriber = $this->get($subscriber_id);

			if(!is_numeric($new_status)) $new_status = $this->get_status_by_name($new_status);

			if ($subscriber->status == $new_status){
				$count++;
				continue;
			}

			$old_status = $subscriber->status;

			if (false !== $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}mymail_subscribers SET status = %d WHERE ID = %d", $new_status, $subscriber->ID))){
				if (!$silent) do_action('mymail_subscriber_change_status', $new_status, $old_status, $subscriber);
				$count++;
				continue;
			}

		}

		return $count;

	}

	private function hash( $str ) {

		for ($i = 0; $i < 10; $i++) {
			$str = sha1( $str );
		}
		return md5($str.mymail_option('ID',''));

	}

}
