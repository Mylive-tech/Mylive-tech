<?php if (!defined('ABSPATH')) die('not allowed');

class mymail_campaigns {

	private $defaultTemplate = 'mymail';
	private $template;
	private $templatefile;
	private $templateobj;

	public function __construct() {

		add_action('plugins_loaded', array( &$this, 'init'));
		add_action('init', array( &$this, 'register_post_type'));
		add_action('init', array( &$this, 'register_post_status'));

		if($hooks = get_option('mymail_hooks', false)){

			add_action('mymail_autoresponder_hook', array( &$this, 'autoresponder_hook'), 10, 2);

			foreach ( (array) $hooks as $campaign_id => $hook) {
				if($hook) add_action( $hook, array( &$this, 'autoresponder_hook_'.$campaign_id ), 10, 5);
			}

		}

	}

	public function init() {

		add_filter('transition_post_status', array( &$this, 'check_for_autoresponder'), 10, 3);
		add_action('mymail_finish_campaign', array( &$this, 'remove_revisions'));

		if(is_admin()){

			add_action('paused_to_trash', array( &$this, 'paused_to_trash' ));
			add_action('active_to_trash', array( &$this, 'active_to_trash' ));
			add_action('queued_to_trash', array( &$this, 'queued_to_trash' ));
			add_action('finished_to_trash', array( &$this, 'finished_to_trash' ));
			add_action('trash_to_paused', array( &$this, 'trash_to_paused' ), 999);

			add_action('admin_menu', array( &$this, 'remove_meta_boxs'));
			add_action('admin_menu', array( &$this, 'autoresponder_menu'), 20);

			add_action('save_post', array( &$this, 'save_campaign'), 10, 3);
			add_filter('wp_insert_post_data', array( &$this, 'wp_insert_post_data'), 1, 2);
			add_filter('post_updated_messages', array( &$this, 'updated_messages'));

			add_filter('after_delete_post', array( &$this, 'delete_campaign'));

			add_filter('pre_post_content', array( &$this, 'remove_kses'));

			add_filter('heartbeat_received', array( &$this, 'heartbeat'), 9, 2);


			global $pagenow, $wp_version;

			switch( $pagenow ){

				case 'edit.php':
					add_action('wp_loaded', array( &$this, 'edit_hook'));
					add_action('admin_enqueue_scripts', array( &$this, 'edit_assets'), 10, 1);
					break;

				case 'post-new.php':
					add_action('wp_loaded', array( &$this, 'post_new_hook'));
					add_action('admin_enqueue_scripts', array( &$this, 'post_edit_assets'), 10, 1);
					break;

				case 'post.php':
					add_action('pre_get_posts', array( &$this, 'post_hook'));
					add_action('admin_enqueue_scripts', array( &$this, 'post_edit_assets'), 10, 1);
					break;

				case 'revision.php':
					if(version_compare($wp_version, '3.6', '<'))
						add_filter('_wp_post_revision_field_post_content', array( &$this, 'revision_field_post_content'), 10, 2);
					break;

			}
		}


	}

	public function __call($func, $args) {

		if(substr($func, 0, 18) == 'autoresponder_hook'){

			$campaign_id = intval(substr($func, 19));

			$subscribers = isset($args[0]) ? $args[0] : NULL;

			return do_action('mymail_autoresponder_hook', $campaign_id, $subscribers);
		}

	}

	public function autoresponder_hook($campaign_id, $subscriber_ids = NULL) {

		$meta = $this->meta($campaign_id);

		if(!$meta['active'] || $meta['autoresponder']['action'] != 'mymail_autoresponder_hook') return;

		$all_subscribers = $this->get_subscribers($campaign_id, NULL, true, (bool) $meta['autoresponder']['once']);

		$subscribers = empty($subscriber_ids)
		 	? $all_subscribers
		 	: array_values(array_intersect($all_subscribers, is_array($subscriber_ids) ? $subscriber_ids : array($subscriber_ids)));

		$timestamp = strtotime('+ '.$meta['autoresponder']['amount'].' '.$meta['autoresponder']['unit']);

		$priority = $meta['autoresponder']['priority'];

		//mymail('queue')->remove($campaign_id, $subscribers);
		mymail('queue')->bulk_add($campaign_id, $subscribers, $timestamp, $priority, false, false, true);

	}


	/*----------------------------------------------------------------------*/
	/* Custom Post Type
	/*----------------------------------------------------------------------*/


	public function register_post_type() {

		register_post_type('newsletter', array(

			'labels' => array(
				'name'					=> __( 'Campaigns', 'mymail' ),
				'singular_name'			=> __( 'Campaign', 'mymail' ),
				'add_new'				=> __( 'New Campaign', 'mymail' ),
				'add_new_item'			=> __( 'Create a new Campaign', 'mymail' ),
				'edit_item'				=> __( 'Edit Campaign', 'mymail' ),
				'new_item'				=> __( 'New Campaign', 'mymail' ),
				'all_items'				=> __( 'All Campaigns', 'mymail' ),
				'view_item'				=> __( 'View Newsletter', 'mymail' ),
				'search_items'			=> __( 'Search Campaigns', 'mymail' ),
				'not_found'				=> __( 'No Campaign found', 'mymail' ),
				'not_found_in_trash'	=> __( 'No Campaign found in Trash', 'mymail' ),
				'parent_item_colon'		=> '',
				'menu_name'				=> __( 'Newsletter', 'mymail' ),
				'filter_items_list'     => __( 'Filter Newsletter list', 'mymail' ),
				'items_list_navigation' => __( 'Newsletter list navigation', 'mymail' ),
				'items_list'            => __( 'Newsletter list', 'mymail' ),

			),

			'public' => true,
			'can_export' => true,
			'menu_icon' => function_exists('wp_admin_bar_sidebar_toggle') ? 'dashicons-mymail' : MYMAIL_URI . 'assets/img/icons/menu_2x.png',
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'exclude_from_search' => true,
			'capability_type' => 'newsletter',
			'map_meta_cap' => true,
			//'menu_position' => 30,
			'has_archive' => mymail_option('hasarchive', false) ? mymail_option('archive_slug', false) : false,
			'hierarchical' => isset($_GET['post_status']) && $_GET['post_status'] == 'autoresponder',
			'rewrite' => array( 'with_front' => false, 'slug' => mymail_option('slug', 'newsletter') ),
			'supports' => array(
				'title',
				'revisions'
			),
			'register_meta_box_cb' => array( &$this, 'meta_boxes')

		));

	}



	public function register_post_status() {

		register_post_status('paused', array(
			'label' => __('Paused', 'mymail'),
			'public' => true,
			'label_count' => _n_noop(__('Paused', 'mymail') . ' <span class="count">(%s)</span>', __('Paused', 'mymail') . ' <span class="count">(%s)</span>')
		));

		register_post_status('active', array(
			'label' => __('Active', 'mymail'),
			'public' => true,
			'label_count' => _n_noop(__('Active', 'mymail') . ' <span class="count">(%s)</span>', __('Active', 'mymail') . ' <span class="count">(%s)</span>')
		));

		register_post_status('queued', array(
			'label' => __('Queued', 'mymail'),
			'public' => true,
			'label_count' => _n_noop(__('Queued', 'mymail') . ' <span class="count">(%s)</span>', __('Queued', 'mymail') . ' <span class="count">(%s)</span>')
		));

		register_post_status('finished', array(
			'label' => __('Finished', 'mymail'),
			'public' => true,
			'label_count' => _n_noop(__('Finished', 'mymail') . ' <span class="count">(%s)</span>', __('Finished', 'mymail') . ' <span class="count">(%s)</span>')
		));

		register_post_status('autoresponder', array(
			'label' => __('Autoresponder', 'mymail'),
			'public' => !is_admin(),
			'exclude_from_search' => true,
			'show_in_admin_all_list' => false,
			'label_count' => _n_noop(__('Autoresponder', 'mymail') . ' <span class="count">(%s)</span>', __('Autoresponders', 'mymail') . ' <span class="count">(%s)</span>')
		));

	}



	/*----------------------------------------------------------------------*/
	/* Meta Boxes
	/*----------------------------------------------------------------------*/


	public function meta_boxes() {

		global $post;
		add_meta_box('mymail_details', __('Details', 'mymail'), array( &$this, 'newsletter_details'), 'newsletter', 'normal', 'high');
		add_meta_box('mymail_template', (!in_array($post->post_status, array('active', 'finished')) && !isset($_GET['showstats'])) ? __('Template', 'mymail') : __('Clickmap', 'mymail'), array( &$this, 'newsletter_template'), 'newsletter', 'normal', 'high');
		add_meta_box('mymail_submitdiv', __('Save', 'mymail'), array( &$this, 'newsletter_submit'), 'newsletter', 'side', 'high');
		add_meta_box('mymail_delivery', __('Delivery', 'mymail'), array( &$this, 'newsletter_delivery'), 'newsletter', 'side', 'high');
		add_meta_box('mymail_receivers', __('Receivers', 'mymail'), array( &$this, 'newsletter_receivers'), 'newsletter', 'side', 'low');
		add_meta_box('mymail_options', __('Options', 'mymail'), array( &$this, 'newsletter_options'), 'newsletter', 'side', 'low');

	}

	public function remove_meta_boxs() {
		remove_meta_box('submitdiv', 'newsletter', 'core');
	}

	public function autoresponder_menu() {

		global $submenu;

		if(current_user_can('edit_newsletters')){
			$submenu['edit.php?post_type=newsletter'][] = array(
				__('Autoresponder', 'mymail'),
				'mymail_edit_autoresponders',
				'edit.php?post_status=autoresponder&post_type=newsletter',
			);
		}

	}


	public function newsletter_details() {
		global $post;
		global $post_id;

		include MYMAIL_DIR . 'views/newsletter/details.php';
	}


	public function newsletter_template() {
		global $post;
		global $post_id;
		include MYMAIL_DIR . 'views/newsletter/template.php';
	}


	public function newsletter_delivery() {
		global $post;
		global $post_id;
		include MYMAIL_DIR . 'views/newsletter/delivery.php';
	}


	public function newsletter_receivers() {
		global $post;
		global $post_id;
		include MYMAIL_DIR . 'views/newsletter/receivers.php';
	}


	public function newsletter_options() {
		global $post;
		global $post_id;
		include MYMAIL_DIR . 'views/newsletter/options.php';
	}


	public function newsletter_submit($post) {
		global $action;
		$post_type = $post->post_type;
		$post_type_object = get_post_type_object($post_type);
		$can_publish = current_user_can($post_type_object->cap->publish_posts);
		include MYMAIL_DIR . 'views/newsletter/submit.php';
	}

	// HOOKS

	public function edit_hook() {

		if (isset($_GET['post_type']) && 'newsletter' == $_GET['post_type']) {

				//duplicate campaign
			if (isset($_GET['duplicate'])) {
				if (wp_verify_nonce($_GET['_wpnonce'], 'mymail_nonce')) {
					$id = intval($_GET['duplicate']);
					$id = $this->duplicate($id);
				}

				//pause campaign
			} else if (isset($_GET['pause'])) {
					if (wp_verify_nonce($_GET['_wpnonce'], 'mymail_nonce')) {
						$id = intval($_GET['pause']);
						$this->pause($id);
					}

				//continue/start campaign
			} else if (isset($_GET['start'])) {
					if (wp_verify_nonce($_GET['_wpnonce'], 'mymail_nonce')) {
						$id = intval($_GET['start']);
						$this->start($id);
					}
				//finish campaign
			} else if (isset($_GET['finish'])) {
					if (wp_verify_nonce($_GET['_wpnonce'], 'mymail_nonce')) {
						$id = intval($_GET['finish']);
						$this->finish($id);
					}
				//activate autoresponder
			} else if (isset($_GET['activate'])) {
					if (wp_verify_nonce($_GET['_wpnonce'], 'mymail_nonce')) {
						$id = intval($_GET['activate']);
						$this->activate($id);
					}

				//deactivate autoresponder
			} else if (isset($_GET['deactivate'])) {
				if (wp_verify_nonce($_GET['_wpnonce'], 'mymail_nonce')) {
						$id = intval($_GET['deactivate']);
						$this->deactivate($id);

				}

			} else if (isset($_GET['flushjobs'])) {
				if (wp_verify_nonce($_GET['_wpnonce'], 'mymail_nonce')) {
					if ($id = $this->flushjobs(intval($_GET['flushjobs']))) {
						$status = (isset($_GET['post_status'])) ? '&post_status='.$_GET['post_status'] : '';
						(isset($_GET['edit'])) ? wp_redirect('post.php?post=' . $id . '&action=edit') : wp_redirect('edit.php?post_type=newsletter'.$status);
					}
				}

			}

			if(isset($id) && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
				$status = (isset($_GET['post_status'])) ? '&post_status='.$_GET['post_status'] : '';
				(isset($_GET['edit']))
					? wp_redirect('post.php?post=' . $id . '&action=edit')
				 	: wp_redirect('edit.php?post_type=newsletter'.$status);
				exit;
			}

			add_filter('wp', array( &$this, 'preload'));
			add_filter('the_excerpt', '__return_false');
			add_filter('post_row_actions', array( &$this, 'quick_edit_btns'), 10, 2);
			add_filter('page_row_actions', array( &$this, 'quick_edit_btns'), 10, 2);
			add_filter('bulk_actions-edit-newsletter', array( &$this, 'bulk_actions'));
			add_filter('manage_edit-newsletter_columns', array( &$this, 'columns'));
			add_filter('manage_newsletter_posts_custom_column', array( &$this, "columns_content"));
			add_filter('manage_edit-newsletter_sortable_columns', array( &$this, "columns_sortable"));
			add_filter('parse_query', array( &$this, "columns_sortable_helper"));

		}

	}


	public function post_hook() {

		global $post;
		//only on edit old newsletter and save
		if (isset($post) && 'newsletter' == $post->post_type) {

			add_filter('enter_title_here', array( &$this, "title"));

			add_action('dbx_post_sidebar', array( mymail('ajax'), 'add_ajax_nonce'));

			$this->post_data = $this->meta($post->ID);

			add_action('submitpost_box', array( &$this, 'notice'));

			if (isset($_GET['template'])) {
				$file = (isset($_GET['file'])) ? $_GET['file'] : 'index.html';
				if(isset($this->post_data['head'])) unset($this->post_data['head']);

				//$this->templateobj = mymail('template', $_GET['template'], $file);

				$this->set_template($_GET['template'], $file, true);
			} else if (isset($this->post_data['template'])) {

				//$this->templateobj = mymail('template', $this->post_data['template'], $this->post_data['file']);

				$this->set_template($this->post_data['template'], $this->post_data['file']);
			} else {

				//$this->templateobj = mymail('template', mymail_option('default_template'), $this->post_data['file']);
				$this->set_template(mymail_option('default_template'), $this->post_data['file']);

			}

		}
	}


	public function post_new_hook() {

		if (isset($_GET['post_type']) && 'newsletter' == $_GET['post_type']) {

			add_filter("enter_title_here", array( &$this, "title"));

			add_action('dbx_post_sidebar', array( mymail('ajax'), 'add_ajax_nonce'));

			$this->post_data = $this->empty_meta();

			if (isset($_GET['template'])) {
				$file = (isset($_GET['file'])) ? $_GET['file'] : 'index.html';
				if(isset($this->post_data['head'])) unset($this->post_data['head']);

				//$this->templateobj = mymail('template', $file, $_GET['template']);
				$this->set_template($_GET['template'], $file, true);
			} else {

				//$this->templateobj = mymail('template', $this->post_data['file'],  mymail_option('default_template'));

				$this->set_template(mymail_option('default_template'));
			}

		}
	}


	public function preload($query) {

		global $wp_query;
		$ids = wp_list_pluck($wp_query->posts, 'ID');
		if(empty($ids)) return;

		//preload meta from the displayed campaigns
		$meta = $this->meta($ids);
		mymail('actions')->get_by_campaign($ids);

	}

	public function notice() {

		global $post;


		switch ($post->post_status) {
			case 'finished':
				$timeformat = get_option('date_format') . ' ' . get_option('time_format');
				$timeoffset = mymail('helper')->gmt_offset(true);
				$msg = sprintf(__('This Campaign was sent on %s', 'mymail'), '<span class="nowrap">'.date($timeformat, $this->meta($post->ID, 'finished')+$timeoffset).'</span>');
				break;
			case 'queued':
				$msg = __('This Campaign is currently in the queue', 'mymail');
				break;
			case 'active':
				$msg = __('This Campaign is currently progressing', 'mymail');
				break;
			case 'paused':
				$msg = __('This Campaign has been paused', 'mymail');
				break;
		}

		if (!isset($msg))
			return false;

		echo '<div class="updated inline"><p><strong>' . $msg .'</strong></p></div>';

	}

	public function updated_messages($messages) {

		global $post_id, $post;

		if ($post->post_type != 'newsletter') return $messages;

		$messages[] = 'No subject!';

		$messages['newsletter'] = array(
			0 => '',
			1 => sprintf(__('Campaign updated. %s', 'mymail'), '<a href="' . esc_url(get_permalink($post_id)) . '">' . __('View Newsletter', 'mymail') . '</a>'),
			2 => sprintf(__('Template changed. %1$s', 'mymail'), '<a href="' . remove_query_arg('message', wp_get_referer()) . '">' . __('Go back', 'mymail') . '</a>'),
			3 => __('Template saved', 'mymail'),
			4 => __('Campaign updated.', 'mymail'),
			5 => isset($_GET['revision']) ? sprintf(__('Campaign restored to revision from %s', 'mymail'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
			6 => sprintf(__('Campaign published. %s', 'mymail'), '<a href="' . esc_url(get_permalink($post_id)) . '">' . __('View Newsletter', 'mymail') . '</a>'),
			7 => __('Campaign saved.', 'mymail'),
			8 => sprintf(__('Campaign submitted. %s', 'mymail'), '<a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post_id))) . '">' . __('Preview Newsletter', 'mymail') . '</a>'),
			9 => __('Campaign scheduled.', 'mymail'),
			10 => __('Campaign draft updated.', 'mymail')
		);

		return $messages;
	}


	public function columns($columns) {

		global $post;
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => __("Name", 'mymail'),
			"status" => __("Status", 'mymail'),
			"total" => __("Total", 'mymail'),
			"open" => __("Open", 'mymail'),
			"click" => __("Clicks", 'mymail'),
			"unsubs" => __("Unsubscribes", 'mymail'),
			"bounces" => __("Bounces", 'mymail'),
			"date" => __("Date", 'mymail')
		);
		return $columns;
	}


	public function columns_sortable($columns) {

		$columns['status'] = 'status';

		return $columns;

	}

	public function columns_sortable_helper($query) {

		$qv = $query->query_vars;

		if(isset($qv['post_type']) && $qv['post_type'] == 'newsletter' && isset($qv['orderby'])){

			switch($qv['orderby']){

				case 'status':
					add_filter( 'posts_orderby', array( &$this, 'columns_orderby_status'));
					break;

			}
		}

	}

	public function columns_orderby_status($orderby) {

		return str_replace('posts.post_date', 'posts.post_status', $orderby);

	}

	public function get_columns_content($column) {

		ob_start();

		$this->columns_content($column);

		$output = ob_get_contents();

		ob_end_clean();

		return $output;
	}

	public function columns_content($column) {

		global $post, $wpdb, $wp_post_statuses;

		$error = ini_get("error_reporting");
		error_reporting(E_ERROR);

		$now = time();
		$timeformat = get_option('date_format') . ' ' . get_option('time_format');

		$meta = $this->meta($post->ID);

		global $pagenow;

		$totals = $this->get_totals($post->ID);
		$errors = $this->get_errors($post->ID);
		$sent = $this->get_sent($post->ID);
		$sent_total = $this->get_sent($post->ID, true);

		$opens = $this->get_opens($post->ID);
		$open_totals = $this->get_opens($post->ID, true);
		$clicks = $this->get_clicks($post->ID);
		$click_totals = $this->get_clicks($post->ID, true);
		$bounces = $this->get_bounces($post->ID);
		$unsubscribes = $this->get_unsubscribes($post->ID);


		switch ($column) {

		case "status":

			$timestamp = isset($meta['timestamp']) ? $meta['timestamp'] : $now;
			$timeoffset = mymail('helper')->gmt_offset(true);

			if (!in_array($post->post_status, array('pending', 'auto-draft'))) {

				//status is finished if this isset, even if the campaign is running;
				$status = isset($campaign['finished']) ? 'finished' : $post->post_status;

				switch ($status) {
				case 'paused':
					echo '<span class="mymail-icon paused"></span> ';
					echo (!$sent) ? $wp_post_statuses['paused']->label : __('Paused', 'mymail');
					if ($totals) {
						if($sent){
							$p = round($sent / $totals * 100);
							echo "<br><div class='campaign-progress'><span class='bar' style='width:" . $p . "%'></span><span>&nbsp;" . sprintf(__('%1$s of %2$s sent', 'mymail'), number_format_i18n($sent), number_format_i18n($totals))."</span><var>$p%</var></div>";
						}
					} elseif(is_null($totals)) {
					} else {
						echo '<br><span class="mymail-icon no-receiver"></span> ' . __('no receivers!', 'mymail');
					}
					break;
				case 'active':
					if ($totals) {
						echo '<span class="mymail-icon progressing"></span> ' . ($sent == $totals ? __('completing job', 'mymail') :  __('progressing', 'mymail') ). '&hellip;'.($meta['timezone'] ? ' <span class="timezonebased"  title="'.__('This campaign is based on subscribers timezone and problably will take up to 24 hours', 'mymail').'">24h</span>' : '');
						$p = $totals ? round($sent / $totals * 100) : 0;
						echo "<br><div class='campaign-progress'><span class='bar' style='width:" . $p . "%'></span><span>&nbsp;" . sprintf(__('%1$s of %2$s sent', 'mymail'), number_format_i18n($sent), number_format_i18n($totals))."</span><var>$p%</var></div>";
					} elseif(is_null($totals)) {
					} else {
						echo '<span class="mymail-icon no-receiver"></span> ' . __('no receivers!', 'mymail');
					}
					break;
				case 'queued':
					echo '<span class="mymail-icon queued"></span> ';
					if($meta['timezone'] && $timestamp-$now < 86400) :
						$sub = $this->get_unsent_subscribers($post->ID, array(1), true);
						$timestamp = min(mymail('subscribers')->get_timeoffset_timestamps($sub, $timestamp));
					endif;
					echo sprintf(__('starts in %s', 'mymail'), ($timestamp-$now > 60) ? human_time_diff($timestamp) : __('less than a minute', 'mymail'));
					echo ($meta['timezone'] ? ' <span class="timezonebased"  title="'.__('This campaign is based on subscribers timezone and problably will take up to 24 hours', 'mymail').'">24h</span>' : '');
					echo "<br><span class='nonessential'>(" . date($timeformat, $timestamp+$timeoffset) . ')</span>';
					break;
				case 'finished':
					echo '<span class="mymail-icon finished"></span> ' . __('Finished', 'mymail');
					echo "<br><span class='nonessential'>(" . date($timeformat, $meta['finished']+$timeoffset) . ')</span>';
					break;
				case 'draft':
					echo '<span class="mymail-icon draft"></span> ' . $wp_post_statuses['draft']->label;
					break;
				case 'trash':
					echo $wp_post_statuses['trash']->label;
					break;
				case "autoresponder":

					$is_active = $meta['active'];
					$active = $is_active ? 'active' : 'inactive';

					include MYMAIL_DIR . 'includes/autoresponder.php';

					$autoresponder = $meta['autoresponder'];

					echo '<span class="mymail-icon '.$active.'"></span> '.($is_active ? __('active', 'mymail') : __('inactive', 'mymail'));
					echo '<br>';

					echo '<span class="autoresponder-'.$active.'">';

					$time_frame_names = array(
						'hour' => __('hour(s)', 'mymail'),
						'day' => __('day(s)', 'mymail'),
						'week' => __('week(s)', 'mymail'),
						'month' => __('month(s)', 'mymail'),
						'year' => __('year(s)', 'mymail'),
					);

					if('mymail_autoresponder_timebased' == $autoresponder['action']){

						$pts = get_post_types( array( 'public' => true ), 'object' );

						if($meta['timestamp'] && $meta['timestamp']-$now < 0)
							mymail('queue')->autoresponder_timebased( $post->ID );

						echo sprintf(__('send every %1$s %2$s', 'mymail'), '<strong>'.$autoresponder['interval'].'</strong>', '<strong>'.$time_frame_names[$autoresponder['time_frame']].'</strong>');
						if($meta['timestamp']){
							echo '<br>'.sprintf(__('next campaign in %s', 'mymail'), '<strong title="'.date($timeformat, $meta['timestamp']+$timeoffset).'">'.human_time_diff($meta['timestamp']).'</strong>');
							echo ' - '.sprintf( '#%s', '<strong title="'.sprintf( __('Next issue: %s', 'mymail'), '#'.$autoresponder['issue']).'">'.$autoresponder['issue'].'</strong>');
							if(isset($autoresponder['time_conditions'])){
								if($posts_required = max(0, ($autoresponder['time_post_count']-$autoresponder['post_count_status'])))
									echo '<br>'.sprintf(__('requires %1$s more %2$s', 'mymail'), ' <strong>'.$posts_required.'</strong>', ' <strong>'.$pts[$autoresponder['time_post_type']]->labels->name.'</strong>');
							}
						}

						if(isset($autoresponder['endtimestamp']))
							echo '<br>'.sprintf(__('until %s', 'mymail'), ' <strong>'.date($timeformat, $autoresponder['endtimestamp']+$timeoffset).'</strong>');
						if(count($autoresponder['weekdays']) < 7){
							global $wp_locale;
							$start_at = get_option('start_of_week');
							$days = array();
							for($i = $start_at; $i < 7+$start_at; $i++){
								$j = $i;
								if(!isset($wp_locale->weekday[$j])) $j = $j-7;
								if(in_array($j, $autoresponder['weekdays'])) $days[] = '<span title="'.$wp_locale->weekday[$j].'">'.substr($wp_locale->weekday[$j],0,2).'</span>';
							}

							echo '<br>'.sprintf(_x('only on %s', 'only one [weekdays]', 'mymail'), ' <strong>'.implode(', ', $days).'</strong>'
								);
						}

					}else if('mymail_autoresponder_usertime' == $autoresponder['action']){

						$datefields = mymail()->get_custom_date_fields();

						echo sprintf(__('send every %1$s %2$s', 'mymail'), '<strong>'.$autoresponder['useramount'].'</strong>', '<strong>'.$time_frame_names[$autoresponder['userunit']].'</strong>');

						echo ' '.sprintf(__('based on the users %1$s value', 'mymail'), ' <strong>'.$datefields[$autoresponder['uservalue']]['name'].'</strong>');

					}else if('mymail_autoresponder_followup' == $autoresponder['action']){

						if($campaign = $this->get($post->post_parent)){
							$types = array(
								1 => __('has been sent', 'mymail' ),
								2 => __('has been opened', 'mymail' ),
								3 => __('has been clicked', 'mymail' ),
							);
							echo sprintf(__('send %1$s %2$s %3$s', 'mymail'), ($autoresponder['amount'] ? '<strong>'.$autoresponder['amount'].'</strong> '.$mymail_autoresponder_info['units'][$autoresponder['unit']] : __('immediately', 'mymail')), __('after', 'mymail'), ' <strong><a href="post.php?post='.$campaign->ID.'&action=edit">'.$campaign->post_title.'</a></strong> '.$types[$autoresponder['followup_action']]);

						}else{
							echo '<br><span class="mymail-icon warning"></span> '.__('Campaign does not exist', 'mymail');
						}

					}else{

						echo sprintf(__('send %1$s %2$s %3$s', 'mymail'), ($autoresponder['amount'] ? '<strong>'.$autoresponder['amount'].'</strong> '.$mymail_autoresponder_info['units'][$autoresponder['unit']] : __('immediately', 'mymail')), __('after', 'mymail'), ' <strong>'.$mymail_autoresponder_info['actions'][$autoresponder['action']]['label'].'</strong>');

					}

					if(!$meta['ignore_lists']){
						$lists = $this->get_lists($post->ID);

						if(!empty($lists)){
							echo '<br>'.__('assigned lists', 'mymail').':<br>';
							foreach ($lists as $i => $list) {
								echo '<strong class="nowrap"><a href="edit.php?post_type=newsletter&page=mymail_lists&ID='.$list->ID.'">'.$list->name.'</a></strong>';
								if($i+1 < count($lists)) echo ', ';
							}
						}else{
							echo '<br><span class="mymail-icon warning"></span> '.__('no lists selected', 'mymail');
						}
					}

					if($meta['list_conditions']){
						$fields = array(
							'email' => mymail_text('email'),
							'firstname' => mymail_text('firstname'),
							'lastname' => mymail_text('lastname'),
							'ip' => __('IP Address', 'mymail'),
							'signup' => __('Signup Date', 'mymail'),
							'ip_signup' => __('Signup IP', 'mymail'),
							'confirm' => __('Confirm Date', 'mymail'),
							'ip_confirm' => __('Confirm IP', 'mymail'),
							'rating' => __('Rating', 'mymail'),
						);

						$customfields = mymail()->get_custom_fields();

						foreach ($customfields as $field => $data) {
							$fields[$field] = $data['name'];
						}

						echo '<br>'.__('only if', 'mymail').'<br>';

						$conditions = array();
						$operators = array(
							'is' => __('is', 'mymail'),
							'is_not' => __('is not', 'mymail'),
							'contains' => __('contains', 'mymail'),
							'contains_not' => __('contains not', 'mymail'),
							'begin_with' => __('begins with', 'mymail'),
							'end_with' => __('ends with', 'mymail'),
							'is_greater' => __('is greater', 'mymail'),
							'is_smaller' => __('is smaller', 'mymail'),
							'pattern' => __('match regex pattern', 'mymail'),
							'not_pattern' => __('does not match regex pattern', 'mymail'),
						);

						foreach($meta['list_conditions']['conditions'] as $i => $condition){
							if(!isset($fields[$condition['field']])){
								echo '<span class="mymail-icon warning"></span> '.sprintf(__('%s is missing!', 'mymail'), '"'.$condition['field'].'"').'<br>';
								continue;
							}
							$conditions[] = '<strong>'.$fields[$condition['field']].'</strong> '.$operators[$condition['operator']].' "<strong>'.$condition['value'].'</strong>"';
						}

						echo implode('<br>'.__(strtolower($meta['list_conditions']['operator']), 'mymail').' ', $conditions);

					}
					echo '</span>';


					if ( (current_user_can('mymail_edit_autoresponders') && (get_current_user_id() == $post->post_author || current_user_can('mymail_edit_others_autoresponders') ) ) ) {
						echo '<div class="row-actions">';
						$actions = array();
							if ($active != 'active') {
								$actions['activate'] = '<a class="start live-action" href="?post_type=newsletter&activate=' . $post->ID . (isset($_GET['post_status']) ? '&post_status='.$_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce('mymail_nonce') . '" title="' . __('activate', 'mymail') . '">' . __('activate', 'mymail') . '</a>&nbsp;';
							} else {
								$actions['deactivate'] = '<a class="start live-action" href="?post_type=newsletter&deactivate=' . $post->ID . (isset($_GET['post_status']) ? '&post_status='.$_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce('mymail_nonce') . '" title="' . __('deactivate', 'mymail') . '">' . __('deactivate', 'mymail') . '</a>&nbsp;';
							}
						echo implode(' | ', $actions);
						echo '</div>';
					}

				break;
				}
			} else {
				$status = get_post_status_object($post->post_status);
				echo $status->label;
			}
			if ((current_user_can('publish_newsletters') && get_current_user_id() == $post->post_author) || current_user_can('edit_others_newsletters')) {
				echo '<div class="row-actions">';
				$actions = array();
				if ($post->post_status == 'queued') {
					$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&start=' . $post->ID . (isset($_GET['post_status']) ? '&post_status='.$_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce('mymail_nonce') . '" title="' . __('Start Campaign now', 'mymail') . '">' . __('Start now', 'mymail') . '</a>&nbsp;';
				}
				if (in_array($post->post_status, array('active', 'queued')) && $status != 'finished') {
					$actions['pause'] = '<a class="pause live-action" href="?post_type=newsletter&pause=' . $post->ID . (isset($_GET['post_status']) ? '&post_status='.$_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce('mymail_nonce') . '" title="' . __('Pause Campaign', 'mymail') . '">' . __('Pause', 'mymail') . '</a>&nbsp;';
				} else if ($post->post_status == 'paused' && $totals) {
						if (!empty($meta['timestamp'])) {
							$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&start=' . $post->ID . (isset($_GET['post_status']) ? '&post_status='.$_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce('mymail_nonce') . '" title="' . __('Resume Campaign', 'mymail') . '">' . __('Resume', 'mymail') . '</a>&nbsp;';
						} else {
							$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&start=' . $post->ID . (isset($_GET['post_status']) ? '&post_status='.$_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce('mymail_nonce') . '" title="' . __('Start Campaign', 'mymail') . '">' . __('Start', 'mymail') . '</a>&nbsp;';
						}
				}
				if (in_array($post->post_status, array('active', 'paused'))) {
					$actions['finish'] = '<a class="finish live-action" href="?post_type=newsletter&finish=' . $post->ID . (isset($_GET['post_status']) ? '&post_status='.$_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce('mymail_nonce') . '" title="' . __('Finish Campaign', 'mymail') . '">' . __('Finish', 'mymail') . '</a>&nbsp;';
				}
				echo implode(' | ', $actions);
				echo '</div>';
			}
			break;

		case "total":

			if('finished' == $post->post_status){
				echo number_format_i18n($sent);
			}elseif('autoresponder' == $post->post_status){
				echo number_format_i18n($sent_total);
			}else{
				echo number_format_i18n($totals);
			}

			if(!empty($errors)) echo '&nbsp;(<a href="edit.php?post_type=newsletter&page=mymail_subscribers&status=4" class="errors" title="'.sprintf(__('%d emails have not been sent', 'mymail'), $errors).'">+'.$errors.'</a>)';
			break;

		case "open":
			if (in_array($post->post_status, array('finished', 'active', 'paused', 'autoresponder'))) {
				echo '<span class="s-opens">'.number_format_i18n($opens) . '</span>/<span class="tiny s-sent">' . number_format_i18n($sent) . '</span>';
				$rate = round(mymail('campaigns')->get_open_rate($post->ID) * 100, 2);
				echo "<br><span title='" . sprintf(__('%s of sent', 'mymail'), $rate.'%') . "' class='nonessential'>";
				echo ' (' . $rate. '%)';
				echo "</span>";
			} else {
				echo '&ndash;';
			}
			break;

		case "click":
			if (in_array($post->post_status, array('finished', 'active', 'paused', 'autoresponder'))) {
				$rate = round(mymail('campaigns')->get_click_rate($post->ID) * 100, 2);
				$rate_a = round(mymail('campaigns')->get_adjusted_click_rate($post->ID) * 100, 2);
				echo number_format_i18n($clicks);
				if($rate){
					echo "<br><span class='nonessential'>(<span title='" . sprintf(__('%s of sent', 'mymail'), $rate.'%') . "'>";
					echo '' . $rate. '%';
					echo "</span>|";
					echo "<span title='" . sprintf(__('%s of opens', 'mymail'), $rate_a.'%') . "'>";
					echo '' . $rate_a. '%';
					echo "</span>)</span>";
				}else{
					echo "<br><span title='" . sprintf(__('%s of sent', 'mymail'), $rate.'%') . "' class='nonessential'>";
					echo ' (' . $rate. '%)';
					echo "</span>";
				}
			}else {
				echo '&ndash;';
			}
			break;

		case "unsubs":
			if (in_array($post->post_status, array('finished', 'active', 'paused', 'autoresponder'))) {
				$rate = round(mymail('campaigns')->get_unsubscribe_rate($post->ID) * 100, 2);
				$rate_a = round(mymail('campaigns')->get_adjusted_unsubscribe_rate($post->ID) * 100, 2);
				echo number_format_i18n($unsubscribes);
				if($rate){
					echo "<br><span class='nonessential'>(<span title='" . sprintf(__('%s of sent', 'mymail'), $rate.'%') . "'>";
					echo '' . $rate. '%';
					echo "</span>|";
					echo "<span title='" . sprintf(__('%s of opens', 'mymail'), $rate_a.'%') . "'>";
					echo '' . $rate_a. '%';
					echo "</span>)</span>";
				}else{
					echo "<br><span title='" . sprintf(__('%s of sent', 'mymail'), $rate.'%') . "' class='nonessential'>";
					echo ' (' . $rate. '%)';
					echo "</span>";
				}
			}else {
				echo '&ndash;';
			}
			break;

		case "bounces":
			if (in_array($post->post_status, array('finished', 'active', 'paused', 'autoresponder'))) {
				$rate = round(mymail('campaigns')->get_bounce_rate($post->ID) * 100, 2);
				echo number_format_i18n($bounces);
				echo "<br><span title='" . sprintf(__('%s of totals', 'mymail'), $rate.'%') . "' class='nonessential'>";
				echo ' (' . $rate. '%)';
				echo "</span>";
			}else {
				echo '&ndash;';
			}
			break;

		}
		error_reporting($error);
	}



	public function bulk_actions($actions) {

		unset($actions['edit']);

		$actions['resume'] = __('Resume', 'mymail');
		return $actions;
	}

	public function quick_edit_btns($actions, $campaign) {

		if ($campaign->post_type != 'newsletter')
			return $actions;

		if (!in_array($campaign->post_status, array('pending', 'auto-draft', 'trash', 'draft'))){

			if (current_user_can('duplicate_newsletters') && current_user_can('duplicate_others_newsletters', $campaign->ID))
				$actions['duplicate'] = '<a class="duplicate" href="?post_type=newsletter&duplicate=' . $campaign->ID . (isset($_GET['post_status']) ? '&post_status='.$_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce('mymail_nonce') . '" title="' . sprintf( __('Duplicate Campaign %s', 'mymail'), '“'.$campaign->post_title.'”' ) . '">' . __('Duplicate', 'mymail') . '</a>';

			if ((current_user_can('publish_newsletters') && get_current_user_id() == $campaign->post_author) || current_user_can('edit_others_newsletters'))
			$actions['statistics'] = '<a class="statistics" href="post.php?post='.$campaign->ID.'&action=edit&showstats=1" title="' . sprintf( __('See stats of Campaign %s', 'mymail'), '“'.$campaign->post_title.'”' ) . '">' . __('Statistics', 'mymail') . '</a>';

			if($parent_id = $this->meta($campaign->ID, 'parent_id')){
				$actions['autoresponder_link'] = '<a class="edit_base" href="post.php?post='.$parent_id.'&action=edit">'.__('Edit base', 'mymail').'</a>';
			}


		}
		return array_intersect_key($actions, array_flip(array('edit','trash','view','statistics','duplicate', 'autoresponder_link')));
	}




	public function title($title) {
		return __('Enter Campaign Title here', 'mymail');
	}


	public function paused_to_trash($campaign) {
		set_transient( 'mymail_before_trash_status_'.$campaign->ID, 'paused' );
	}


	public function active_to_trash($campaign) {
		set_transient( 'mymail_before_trash_status_'.$campaign->ID, 'active' );
	}


	public function queued_to_trash($campaign) {
		set_transient( 'mymail_before_trash_status_'.$campaign->ID, 'queued' );
	}


	public function finished_to_trash($campaign) {
		set_transient( 'mymail_before_trash_status_'.$campaign->ID, 'finished' );
	}


	public function trash_to_paused($campaign) {

		$oldstatus = get_transient( 'mymail_before_trash_status_'.$campaign->ID, 'paused' );

		if ($campaign->post_status != $oldstatus) $this->change_status($campaign, $oldstatus, true);

	}


	public function edit_assets(){

		$screen = get_current_screen();

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if($screen->id != 'edit-newsletter') return;

		wp_enqueue_script('mymail-overview', MYMAIL_URI . 'assets/js/overview-script'.$suffix.'.js', array(), MYMAIL_VERSION, true);

		wp_enqueue_style('mymail-overview', MYMAIL_URI . 'assets/css/overview-style'.$suffix.'.css', array(), MYMAIL_VERSION);

		wp_localize_script('mymail-overview', 'mymailL10n', array(
			'finish_campaign' => __('Do you really like to finish this campaign?', 'mymail'),
		));
	}

	public function post_edit_assets(){

		global $post, $wp_locale;

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if (!isset($post) || $post->post_type != 'newsletter') return;

		wp_enqueue_script('mymail-script', MYMAIL_URI . 'assets/js/newsletter-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION, true);

		if (in_array($post->post_status, array('active', 'finished')) || isset($_GET['showstats'])){

			wp_enqueue_script('google-jsapi', 'https://www.google.com/jsapi');

			wp_enqueue_script('easy-pie-chart', MYMAIL_URI . 'assets/js/libs/easy-pie-chart'.$suffix.'.js', array('jquery'), MYMAIL_VERSION, true);

			wp_enqueue_style('easy-pie-chart', MYMAIL_URI . 'assets/css/libs/easy-pie-chart'.$suffix.'.css', array(), MYMAIL_VERSION);

		}else{

			if($post->post_status == 'autoresponder'){
				wp_enqueue_script('google-jsapi', 'https://www.google.com/jsapi');
				wp_enqueue_script('easy-pie-chart', MYMAIL_URI . 'assets/js/libs/easy-pie-chart'.$suffix.'.js', array('jquery'), MYMAIL_VERSION, true);
				wp_enqueue_style('easy-pie-chart', MYMAIL_URI . 'assets/css/libs/easy-pie-chart'.$suffix.'.css', array(), MYMAIL_VERSION);
			}

			wp_enqueue_style('mymail-codemirror', MYMAIL_URI . 'assets/css/libs/codemirror'.$suffix.'.css', array(), MYMAIL_VERSION);

			if (user_can_richedit()) wp_enqueue_script('editor');

			wp_enqueue_style('jquery-ui-style', MYMAIL_URI . 'assets/css/libs/jquery-ui'.$suffix.'.css', array(), MYMAIL_VERSION);
			wp_enqueue_style('jquery-datepicker', MYMAIL_URI . 'assets/css/datepicker'.$suffix.'.css', array(), MYMAIL_VERSION);

			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('jquery-ui-draggable');

			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');

			wp_enqueue_media();

		}

		wp_enqueue_style( 'mymail-flags', MYMAIL_URI . 'assets/css/flags'.$suffix.'.css', array( ), MYMAIL_VERSION );

		wp_enqueue_style('mymail-editor-style', MYMAIL_URI . 'assets/css/editor-style'.$suffix.'.css', array(), MYMAIL_VERSION);

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_localize_script('mymail-script', 'mymailL10n', array(
			'loading' => __('loading', 'mymail'),
			'add' => __('add', 'mymail'),
			'or' => __('or', 'mymail'),
			'move_module_up' => __('Move module up', 'mymail'),
			'move_module_down' => __('Move module down', 'mymail'),
			'duplicate_module' => __('Duplicate module', 'mymail'),
			'remove_module' => __('remove module', 'mymail'),
			'remove_all_modules' => __('Do you really like to remove all modules?', 'mymail'),
			'add_module' => __('Add Module', 'mymail'),
			'codeview' => __('Codeview', 'mymail'),
			'module_label' => __('Name of the module (click to edit)', 'mymail'),
			'edit' => __('Edit', 'mymail'),
			'click_to_edit' => __('Click to edit %s', 'mymail'),
			'click_to_add' => __('Click to add %s', 'mymail'),
			'auto' => _x('Auto', 'for the autoimporter', 'mymail'),
			'add_button' => __('add button', 'mymail'),
			'add_s' => __('add %s', 'mymail'),
			'remove_s' => __('remove %s', 'mymail'),
			'curr_selected' => __('Currently selected', 'mymail'),
			'remove_btn' => __('An empty link will remove this button! Continue?', 'mymail'),
			'preview_for' => __('Preview for %s', 'mymail'),
			'preview' => __('Preview', 'mymail'),
			'read_more' => __('Read more', 'mymail'),
			'invalid_image' => __('%s does not contain a valid image', 'mymail'),
			'enter_list_name' => __('Enter name of the list', 'mymail'),
			'create_list' => _x('%s of %s', '[recipientstype] of [campaignname]', 'mymail'),

			'next' => __('next', 'mymail'),
			'prev' => __('prev', 'mymail'),
			'start_of_week' => get_option('start_of_week'),
			'day_names' => $wp_locale->weekday,
			'day_names_min' => array_values($wp_locale->weekday_abbrev),
			'month_names' => array_values($wp_locale->month),
			'delete_colorschema' => __('Delete this color schema?', 'mymail'),
			'delete_colorschema_all' => __('Do you really like to delete all custom color schemas for this template?', 'mymail'),
			'yourscore' => __('%s out of 10', 'mymail'),
			'yourscores' => array(
				__('This mail will hardly see any inbox!', 'mymail'),
				__('You have to make it better!', 'mymail'),
				__('Many inboxes will refuse this mail!', 'mymail'),
				__('Not bad at all. Improve it further!', 'mymail'),
				__('Almost perfect!', 'mymail'),
				__('Great! Your campaign is ready to send!', 'mymail'),
			),
			'undisteps' => mymail_option('undosteps', 10),
			'statuschanged' => __('The status of this campaign has changed. Please reload the page or %s', 'mymail'),
			'click_here' => __('click here', 'mymail'),
			'check_console' => __('Check the JS console for more info!', 'mymail'),
		));

		wp_localize_script('mymail-script', 'mymaildata', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'url' => MYMAIL_URI,
			'codeview' => current_user_can('mymail_see_codeview'),
			'datefields' => array_merge(array('added', 'updated', 'signup', 'confirm'), mymail()->get_custom_date_fields(true)),
		));

		wp_enqueue_style('mymail-style', MYMAIL_URI . 'assets/css/newsletter-style'.$suffix.'.css', array(), MYMAIL_VERSION);

	}


	/*----------------------------------------------------------------------*/
	/* Save Methods
	/*----------------------------------------------------------------------*/


	public function remove_kses($content) {

		global $post;

		if (isset($post) && $post->post_type == 'newsletter')
			kses_remove_filters();

		return $content;


	}
	public function wp_insert_post_data($post, $postarr) {

		if(!isset($post)) return $post;

		if($post['post_type'] != 'newsletter') return $post;

		$is_autosave = wp_is_post_autosave($postarr['ID']);

		if ($is_autosave && get_post_type( $is_autosave ) != 'newsletter') return $post;

		if($is_autosave && isset($_POST['data']['mymaildata'])){

			parse_str($_POST['data']['mymaildata'], $postdata);
			$postdata = $postdata['mymail_data'];

		}else if(isset($_POST['mymail_data'])){

			$postdata = $_POST['mymail_data'];

		}else{

			$postdata = $this->meta($postarr['ID']);
		}

		//sanitize the content and remove all content filters
		$post['post_content'] = mymail()->sanitize_content($post['post_content'], NULL, $postdata['head']);

		$post['post_excerpt'] = !empty($postdata['autoplaintext'])
			? mymail()->plain_text($post['post_content'])
			: $post['post_excerpt'];

		if (!in_array($post['post_status'], array('pending', 'draft', 'auto-draft', 'trash'))) {

			if ($post['post_status'] == 'publish')
				$post['post_status'] = 'paused';

			$post['post_status'] = isset($_POST['mymail_data']['active']) ? 'queued' : $post['post_status'];

		}

		if($post['post_status'] == 'autoresponder' && $postdata['autoresponder']['action'] != 'mymail_autoresponder_followup')
			$post['post_parent'] = 0;

		return $post;

	}


	public function save_campaign($post_id, $post, $update = NULL) {


		if (!isset($post) || $post->post_type != 'newsletter') return $post;

		$is_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;

		if($is_autosave && isset($_POST['data']['mymaildata'])){

			parse_str($_POST['data']['mymaildata'], $postdata);
			$postdata = $postdata['mymail_data'];

		}else if(isset($_POST['mymail_data'])){

			$postdata = $_POST['mymail_data'];

		}else{
			return $post;
		}


		$timeoffset = mymail('helper')->gmt_offset(true);
		$now = time();

		//activate kses filter
		kses_init_filters();

		$meta = $this->meta($post_id);

		if(isset($postdata)){

			if(function_exists( 'wp_encode_emoji' )){
				$postdata['subject'] = wp_encode_emoji($postdata['subject']);
				$postdata['preheader'] = wp_encode_emoji($postdata['preheader']);
				$postdata['from_name'] = wp_encode_emoji($postdata['from_name']);
			}

			$meta['subject'] = $postdata['subject'];
			$meta['preheader'] = $postdata['preheader'];
			$meta['template'] = $postdata['template'];
			$meta['file'] = $postdata['file'];
			$meta['lists'] = isset($postdata['lists']) ? (array) $postdata['lists'] : array();
			$meta['ignore_lists'] = isset($postdata['ignore_lists']) && $postdata['ignore_lists'];
			$meta['from_name'] = $postdata['from_name'];
			$meta['from_email'] = $postdata['from_email'];
			$meta['reply_to'] = $postdata['reply_to'];
			$meta['timezone'] = isset($postdata['timezone']) && $postdata['timezone'];

			if (isset($postdata['newsletter_color']))
				$meta['colors'] = $postdata['newsletter_color'];

				//$meta['version'] = isset($postdata['version']) ? $postdata['version'] : NULL;
			$meta['background'] = $postdata['background'];

			$meta['embed_images'] = isset($postdata['embed_images']);

			$meta['head'] = $postdata['head'];

			$is_autoresponder = !!$postdata['is_autoresponder'];

			$autoresponder = $postdata['autoresponder'];

			$post->post_parent = 0;
			$post->post_password = isset($_POST['use_pwd']) ? $_POST['post_password'] : '';

			/*
			preg_match_all('#<img([^>]+)?data-id="(\d+)"(.*)?>#', $post->post_content, $used_image_ids);
			$used_image_ids = $used_image_ids[2];
			*/

			if($is_autoresponder){

				if ($post->post_status != 'autoresponder' && !$is_autosave) {
					$this->change_status($post, 'autoresponder');
					$post->post_status = 'autoresponder';
				}

				$meta['active'] = isset($postdata['active_autoresponder']) && current_user_can('publish_newsletters');
				$autoresponder['amount'] = max(0, floatval($autoresponder['amount']));

				// if(isset($autoresponder['terms'])){

				// 	foreach($autoresponder['terms'] as $taxonomy => $term_ids){
				// 		$autoresponder['terms'][$taxonomy] = array_unique($term_ids);
				// 		if(in_array('-1', $term_ids)) $autoresponder['terms'][$taxonomy] = array('-1');
				// 	}

				// }

				if(in_array($autoresponder['action'], array('mymail_subscriber_insert', 'mymail_subscriber_unsubscribed'))){
					unset($autoresponder['terms']);

					$localtime = strtotime($postdata['autoresponder_signup_date'] . ' ' . $postdata['autoresponder_signup_time']);
					$meta['timestamp'] = $localtime-$timeoffset;

				}else if('mymail_post_published' == $autoresponder['action']){

				}else{
					unset($autoresponder['terms']);
				}

				if('mymail_autoresponder_timebased' == $autoresponder['action']){

					$autoresponder['interval'] = max(1, intval($autoresponder['interval']));
					$meta['timezone'] = isset($autoresponder['timebased_timezone']);

					$localtime = strtotime($postdata['autoresponder_date'] . ' ' . $postdata['autoresponder_time']);

					$autoresponder['weekdays'] = (isset($autoresponder['weekdays'])
						? $autoresponder['weekdays']
						: array(date('w', $localtime)));


					$localtime = mymail('helper')->get_next_date_in_future($localtime-$timeoffset, 0, $autoresponder['time_frame'], $autoresponder['weekdays']);

					$meta['timestamp'] = $localtime;

					if(isset($autoresponder['endschedule'])){

						$localtime = strtotime($postdata['autoresponder_enddate'] . ' ' . $postdata['autoresponder_endtime']);
						$autoresponder['endtimestamp'] = max($meta['timestamp'], $localtime-$timeoffset);

					}

				}else if('mymail_autoresponder_followup' == $autoresponder['action']){


				}else if('mymail_autoresponder_usertime' == $autoresponder['action']){

					$meta['timezone'] = isset($autoresponder['usertime_timezone']);
					$autoresponder['once'] = isset($autoresponder['usertime_once']);

				}else if('mymail_autoresponder_hook' == $autoresponder['action']){

					$hooks = get_option('mymail_hooks', array());
					$hooks[$post->ID] = $autoresponder['hook'];
					if(!$meta['active']) unset($hooks[$post->ID]);
					update_option('mymail_hooks', $hooks);
					$autoresponder['once'] = isset($autoresponder['hook_once']);

				}else{

					$meta['timezone'] = isset($autoresponder['post_published_timezone']);

				}

				if(isset($_POST['post_count_status_reset'])) $autoresponder['post_count_status'] = 0;

				$meta['autoresponder'] = $autoresponder;


			}else{ //no autoresponder


				if ($post->post_status == 'autoresponder' && !$is_autosave) {
					$meta['active'] = false;
					$this->change_status($post, 'paused');
					$post->post_status = 'paused';
				}else{
					$meta['active'] = isset($postdata['active']);
				}

				$localtime = strtotime($postdata['date'] . ' ' . $postdata['time']);

				if((isset($postdata) && empty($meta['timestamp'])) || $meta['active']){
					//save in UTC
					$meta['timestamp'] = max($now, $localtime-$timeoffset);
				}

				// if (isset($_POST['send_now'])) {
				// 	$meta['timestamp'] = $now;
				// 	$meta['active'] = true;
				// 	$post->post_status = 'queued';
				// }

				//set status to 'active' if time is in the past
				if (!$is_autosave && $post->post_status == 'queued' && $now - $meta['timestamp'] >= 0) {
					$this->change_status($post, 'active');
					$post->post_status = 'active';

				//set status to 'queued' if time is in the future
				}else if (!$is_autosave && $post->post_status == 'active' && $now - $meta['timestamp'] < 0) {
					$this->change_status($post, 'queued');
					$post->post_status = 'queued';
				}

				$meta['autoresponder'] = NULL;


			}

			mymail_remove_notice('camp_error_'.$post_id);


		}


		if (isset($postdata['list_conditions'])){

			$meta['list_conditions'] = $postdata['list'];

		}else{

			$meta['list_conditions'] = '';

		}

		$meta['autoplaintext'] = isset($postdata['autoplaintext']);

		if (isset($meta['active_autoresponder']) && $meta['active_autoresponder']) {
			if(isset($postdata)){
				if(!$meta['timestamp']) $meta['timestamp'] = max($now, strtotime($postdata['date'] . ' ' . $postdata['time']));
			}
		}

		//always inactive if autosave
		if($is_autosave) $meta['active'] = false;

		$this->update_meta( $post_id, $meta );

		if(!$is_autosave){

			mymail('queue')->clear( $post_id );

			//if post is published, active or queued and campaign start within the next 60 minutes
			if (in_array($post->post_status, array('active', 'queued', 'autoresponder')) && $now - $meta['timestamp'] > -3600) {

				mymail('cron')->update();

			}
			if (in_array($post->post_status, array('autoresponder'))) {

				switch($autoresponder['action']){
					case 'mymail_autoresponder_usertime':
						mymail('queue')->autoresponder_usertime( $post_id );
						break;
					case 'mymail_autoresponder_timebased':
						mymail('queue')->autoresponder_timebased( $post_id );
						break;
					default:
						mymail('queue')->autoresponder( $post_id );

				}
				//do_action('mymail_update_queue');
			}

		}

		//make permalinks work correctly
		//flush_rewrite_rules();

	}

	public function meta($id, $key = NULL) {

		global $wpdb;

		$cache_key = 'campaign_meta';

		$meta = mymail_cache_get( $cache_key );
		if(!$meta) $meta = array();

		if(is_numeric($id)){

			if(isset($meta[$id])){
				if(is_null($key)) return $meta[$id];
				return isset($meta[$id][$key]) ? $meta[$id][$key] : NULL;
			}

			$ids = array($id);
		}elseif(is_array($id)){

			$ids = $id;
		}

		$defaults = $this->empty_meta();

		if(is_null($id) && is_null($key)) return $defaults;

		$sql = "SELECT post_id AS ID, REPLACE(meta_key, '_mymail_', '') AS meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE '_mymail_%'";

		if(isset($ids)) $sql .= " AND post_id IN (".implode(',', array_filter($ids, 'is_numeric')).")";

		$result = $wpdb->get_results($sql);

		foreach($result as $metadata){
			if(!isset($meta[$metadata->ID])) $meta[$metadata->ID] = $defaults;
			$meta[$metadata->ID][$metadata->meta_key] = $metadata->meta_value;
			//$meta = $row;
			//$lists = explode('|', $row['lists'] );
			//array_shift($lists);
			//$meta[$metadata->ID]['lists'] = $lists;
			if(!empty($meta[$metadata->ID]['lists']))
				$meta[$metadata->ID]['lists'] = maybe_unserialize( $meta[$metadata->ID]['lists'] );

			if(!empty($meta[$metadata->ID]['colors']))
				$meta[$metadata->ID]['colors'] = maybe_unserialize( $meta[$metadata->ID]['colors'] );

			if(!empty($meta[$metadata->ID]['autoresponder']))
				$meta[$metadata->ID]['autoresponder'] = maybe_unserialize( $meta[$metadata->ID]['autoresponder'] );

			if(!empty($meta[$metadata->ID]['list_conditions']))
				$meta[$metadata->ID]['list_conditions'] = maybe_unserialize( $meta[$metadata->ID]['list_conditions'] );

		}

		mymail_cache_set( $cache_key, $meta );

		if(is_null($id) && is_null($key)) return $meta;

		if(is_array($id) && is_null($key)) return $meta;

		if(is_array($id)) return wp_list_pluck( $meta, $key );

		if(is_null($key)) return isset($meta[$id]) ? $meta[$id] : NULL;

		if(is_null($id)) return wp_list_pluck( $meta, $key );

		return isset($meta[$id]) && isset($meta[$id][$key]) ? $meta[$id][$key] : NULL;

	}


	public function update_meta($id, $key, $value = NULL) {

		$cache_key = 'campaign_meta';
		$meta = mymail_cache_get( $cache_key );
		if(!$meta) $meta = array();

		if(is_array($key)){
			$_meta = (array) $key;
		}else{
			$_meta = array($key => $value);
		}

		foreach ($_meta as $k => $v) {
													//allowed NULL values
			if($v == '' && !in_array($k, array('timezone', 'embed_images', 'ignore_lists', 'autoplaintext'))){
				delete_post_meta($id, '_mymail_'.$k);
			}else{
				update_post_meta($id, '_mymail_'.$k, $v);
			}

		}

		if(isset($meta[$id])){
			unset($meta[$id]);
			mymail_cache_set( $cache_key, $meta );
		}

		return true;

	}

	private function empty_meta($id = NULL, $key = NULL) {
		return array(
			'parent_id' => NULL,
			'timestamp' => NULL,
			'finished' => NULL,
			'active' => NULL,
			'timezone' => mymail_option('timezone'),
			'sent' => NULL,
			'error' => NULL,
			'from_name' => mymail_option('from_name'),
			'from_email' => mymail_option('from'),
			'reply_to' => mymail_option('reply_to'),
			'subject' => NULL,
			'preheader' => NULL,
			'template' => NULL,
			'file' => NULL,
			'lists' => NULL,
			'ignore_lists' => NULL,
			'autoresponder' => NULL,
			'list_conditions' => NULL,
			'head' => NULL,
			'background' => NULL,
			'colors' => NULL,
			'embed_images' => mymail_option('embed_images'),
			'autoplaintext' => true
		);
	}



	public function pause($id) {
		if (!current_user_can('publish_newsletters')) {
			wp_die( __('You are not allowed to pause campaigns.', 'mymail'));
		}
		$post = get_post($id);

		$meta = $this->meta($id);

		$meta['active'] = false;

		$this->update_meta($id, $meta);

		if($this->change_status($post, 'paused')){
			do_action('mymail_campaign_pause', $id);
			return true;
		}else{
			return false;
		}
	}


	public function start($id) {
		if (!current_user_can('publish_newsletters')) {
			wp_die( __('You are not allowed to start campaigns.', 'mymail'));
		}

		$now = time();

		$post = get_post($id);
		$meta = $this->meta($id);
		if(!$this->get_totals($id)) return false;

		$meta['active'] = true;

		if (empty($meta['timestamp']) || $post->post_status == 'queued') {
			$meta['timestamp'] = $now;
		}

		$status = ($now - $meta['timestamp'] < 0) ? 'queued' : 'active';

		$this->update_meta($id, $meta);

		if($this->change_status($post, $status)){
			do_action('mymail_campaign_start', $id);
			mymail_remove_notice('camp_error_'.$id);
			return true;

		}

		return false;

	}

	public function finish($id, $check = true) {
		if ($check && !current_user_can('publish_newsletters')) {
			wp_die( __('You are not allowed to finish campaigns.', 'mymail'));
		}

		$post = get_post($id);

		if(!in_array($post->post_status, array('active', 'queued', 'paused'))) return;

		$meta = $this->meta($id);
		$meta['totals'] = $this->get_totals($id);
		$meta['sent'] = $this->get_sent($id);
		$meta['errors'] = $this->get_errors($id);
		$meta['finished'] = time();

		$placeholder = mymail('placeholder');

		$placeholder->do_conditions(false);

		$placeholder->clear_placeholder();

		$placeholder->set_content($post->post_title);
		$post->post_title = $placeholder->get_content(false, array(), true);

		$placeholder->set_content($post->post_content);
		$post->post_content = $placeholder->get_content(false, array(), true);

		$placeholder->set_content($meta['subject']);
		$meta['subject'] = $placeholder->get_content(false, array(), true);

		$placeholder->set_content($meta['preheader']);
		$meta['preheader'] = $placeholder->get_content(false, array(), true);

		$placeholder->set_content($meta['from_name']);
		$meta['from_name'] = $placeholder->get_content(false, array(), true);

		remove_action('save_post', array( &$this, 'save_campaign'), 10, 3);
		kses_remove_filters();

		wp_update_post(array(
			'ID' => $id,
			'post_title' => $post->post_title,
			'post_content' => $post->post_content,

		));

		kses_init_filters();
		add_action('save_post', array( &$this, 'save_campaign'), 10, 3);

		$this->update_meta($id, $meta);

		$this->change_status($post, 'finished');

		$parent_id = $this->meta($id, 'parent_id');

		if($parent_id = $this->meta($id, 'parent_id')){
			$parent_sent = $this->meta($parent_id, 'sent');
			$parent_errors = $this->meta($parent_id, 'errors');

			$this->update_meta($parent_id, 'sent', $parent_sent+$sent);
			$this->update_meta($parent_id, 'errors', $parent_errors+$errors);
		}


		do_action('mymail_finish_campaign', $id);

		mymail('queue')->remove($id);

		mymail_remove_notice('camp_error_'.$id);

		return true;

	}

	public function duplicate($id) {

		$post = get_post($id);

		if (!current_user_can('duplicate_newsletters') || !current_user_can('duplicate_others_newsletters', $post->ID)) {
			wp_die( __('You are not allowed to duplicate campaigns.', 'mymail'));
		}

		$lists = $this->get_lists($post->ID, true);
		$meta = $this->meta($post->ID);

		$meta['active'] = $meta['date'] = $meta['time'] = $meta['timestamp'] = $meta['parent_id'] = $meta['finished'] = $meta['sent'] = $meta['error'] = NULL;

		unset($post->ID);
		unset($post->guid);
		unset($post->post_name);
		unset($post->post_author);
		unset($post->post_date);
		unset($post->post_date_gmt);
		unset($post->post_modified);
		unset($post->post_modified_gmt);

		if (preg_match('# \((\d+)\)$#', $post->post_title, $hits)) {
			$post->post_title = trim(preg_replace('#(.*) \(\d+\)$#', '$1 (' . (++$hits[1]) . ')', $post->post_title));
		} else if ($post->post_title) {
				$post->post_title .= ' (2)';
			}
		if($post->post_status == 'autoresponder'){
			$meta['autoresponder']['issue'] = 1;
			$meta['autoresponder']['post_count_status'] = 0;
		}else{
			$post->post_status = 'draft';
		}

		kses_remove_filters();
		$new_id = wp_insert_post($post);
		kses_init_filters();

		if ($new_id) {

			$this->update_meta($new_id, $meta);
			$this->add_lists($new_id, $lists);

			do_action('mymail_campaign_duplicate', $id, $new_id);

			return $new_id;
		}

		return false;
	}


	public function activate($id) {

		$this->update_meta($id, 'active', true);

		return true;
	}

	public function deactivate($id) {

		$this->update_meta($id, 'active', false);

		return true;
	}

	public function flushjobs($id, $silence = false) {

		$crons = get_option('cron');

		$count = 0;

		foreach($crons as $timestamp => $hook){
			if(!is_array($hook)) continue;
			foreach($hook as $hash => $args){
				if($hash == 'mymail_autoresponder'){
					foreach($args as $job){
						if(isset($job['args']['campaign_id']) && $job['args']['campaign_id'] == $id){
							$count++;
						}
						unset($crons[$timestamp]);
					}
				}
			}
		}

		update_option('cron', $crons);

		if(!$silence && $count) mymail_notice(sprintf(_n( '%d job deleted', '%d jobs deleted', $count, 'mymail'), $count), NULL, true, 'autorespond-deleted');

		return true;
	}


	public function autoresponder_to_campaign($id, $delay = 0, $issue = '') {

		$post = get_post($id);
		if($post->post_status != 'autoresponder') return false;
		$id = $post->ID;

		$now = time();
		$timeoffset = mymail('helper')->gmt_offset(true);

		$lists = $this->get_lists($post->ID, true);
		$meta = $this->meta($post->ID);

		$meta['autoresponder'] = $meta['sent'] = $meta['errors'] = $meta['finished'] = NULL;

		$meta['active'] = true;

		$meta['timestamp'] = max($now, $now+$delay);

		unset($post->ID);
		unset($post->guid);
		unset($post->post_name);
		unset($post->post_date);
		unset($post->post_date_gmt);
		unset($post->post_modified);
		unset($post->post_modified_gmt);

		$post->post_status = $meta['timestamp'] <= $now ? 'active' : 'queued';

		$placeholder = mymail('placeholder');

		$placeholder->do_conditions(false);

		$placeholder->clear_placeholder();
		$placeholder->add(array('issue' => $issue));

		$placeholder->set_content($post->post_title);
		$post->post_title = $placeholder->get_content(false);

		$placeholder->set_content($post->post_content);
		$post->post_content = $placeholder->get_content(false, array(), true);

		$placeholder->set_content($meta['subject']);
		$meta['subject'] = $placeholder->get_content(false, array(), true);

		$placeholder->set_content($meta['preheader']);
		$meta['preheader'] = $placeholder->get_content(false, array(), true);

		$placeholder->set_content($meta['from_name']);
		$meta['from_name'] = $placeholder->get_content(false, array(), true);

		remove_action('save_post', array( &$this, 'save_campaign'), 10, 3);
		kses_remove_filters();

		$new_id = wp_insert_post($post);

		kses_init_filters();
		add_action('save_post', array( &$this, 'save_campaign'), 10, 3);

		$meta['parent_id'] = $id;

		if ($new_id) {

			$this->update_meta($new_id, $meta);
			$this->add_lists($new_id, $lists);

			return $new_id;
		}

		return false;
	}



	public function delete_campaign($id) {

		global $wpdb;

		//remove actions, queue and subscriber meta
		$wpdb->query($wpdb->prepare("DELETE a FROM {$wpdb->prefix}mymail_actions AS a WHERE a.campaign_id = %d", $id));
		$wpdb->query($wpdb->prepare("DELETE a FROM {$wpdb->prefix}mymail_queue AS a WHERE a.campaign_id = %d", $id));
		$wpdb->query($wpdb->prepare("DELETE a FROM {$wpdb->prefix}mymail_subscriber_meta AS a WHERE a.campaign_id = %d", $id));

		//unassign existing parents
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_value = %d AND meta_key = '_mymail_parent_id'", $id));

	}



	public function get($id = NULL) {

		if(is_null($id) || is_array($id)) return $this->get_campaigns($id);
		$campaign = get_post($id);

		return ($campaign && $campaign->post_type == 'newsletter') ?  $campaign : false;

	}


	public function get_lists($id, $ids_only = false) {

		$list_ids = $this->meta($id, 'lists');

		if($ids_only) return $list_ids;

		return mymail('lists')->get($list_ids);

	}

	public function add_lists($id, $list_ids, $clear = false) {

		if(!is_array($list_ids)) $list_ids = array($list_ids);

		if(!$clear) $list_ids = wp_parse_args($list_ids, $this->meta($id, 'lists'));

		return $this->update_meta($id, 'lists', $list_ids);

	}


	public function get_active( $args = '' ) {
		$defaults = array(
			'post_status' => 'active',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns ( $args );
	}


	public function get_paused( $args = '' ) {
		$defaults = array(
			'post_status' => 'paused',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns ( $args );
	}


	public function get_queued( $args = '' ) {
		$defaults = array(
			'post_status' => 'queued',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns ( $args );
	}


	public function get_drafted( $args = '' ) {
		$defaults = array(
			'post_status' => 'draft',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns ( $args );
	}


	public function get_finished( $args = '' ) {
		$defaults = array(
			'post_status' => 'finished',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns ( $args );
	}


	public function get_pending( $args = '' ) {
		$defaults = array(
			'post_status' => 'pending',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns ( $args );
	}

	public function get_autoresponder( $args = '' ) {
		$defaults = array(
			'post_status' => 'autoresponder',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns ( $args );
	}


	public function get_campaigns( $args = '' ) {

		$defaults = array(
			'post_type' => 'newsletter',
			'post_status' => array('active', 'paused', 'queued', 'draft', 'finished', 'pending', 'autoresponder'),
			'orderby' => 'modified',
			'order' => 'DESC',
			'posts_per_page' => -1,
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);
		$args = wp_parse_args( $args, $defaults );

		$campaigns = get_posts( $args );

		return $campaigns;
	}


	public function get_subscribers($id = NULL, $statuses = NULL, $return_ids = false, $ignore_sent = false, $ignore_queue = false, $limit = NULL, $offset = 0, $returnsql = false){

		if($this->meta($id, 'ignore_lists')){
			$lists = false;
		}else{
			$lists = $this->meta($id, 'lists');

			if(empty($lists) && !$returnsql) return $return_ids ? array() : 0;
		}

		$conditions = $this->meta($id, 'list_conditions');

		return $this->get_subscribers_by_lists($lists, $conditions, $statuses, $return_ids, $ignore_sent ? $id : false, $ignore_queue ? $id : false, $limit, $offset, $returnsql);
	}


	public function get_subscribers_by_lists($lists = false, $conditions = NULL, $statuses = NULL, $return_ids = false, $ignore_sent = false, $ignore_queue = false, $limit = NULL, $offset = 0, $returnsql = false){

		global $wpdb;

		$cache_key = 'get_subscriber_by_lists';
		$key = md5(serialize(array($lists,$conditions,$statuses,$return_ids,$ignore_sent,$ignore_queue,$limit,$offset,$returnsql)));

		$subscribers = mymail_cache_get( $cache_key );

		if(!$subscribers) $subscribers = array();

		if (isset($subscribers[$key]) ) return $subscribers[$key];

		if(is_null($statuses)) $statuses = array(1);

		$sql = "SELECT ".($return_ids ? 'a.ID' : 'COUNT(DISTINCT a.ID)')." FROM {$wpdb->prefix}mymail_subscribers AS a";

		if($lists !== false)
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON a.ID = ab.subscriber_id";

		if($ignore_sent)
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = ".intval($ignore_sent)." AND b.type = 1";

		if($ignore_queue)
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_queue AS c ON a.ID = c.subscriber_id AND c.campaign_id = ".intval($ignore_queue);

		$sql .= $this->get_sql_join_by_condition($conditions);

		$sql .= " WHERE 1";

		if($lists !== false){
			//unassigned members if NULL
			if(is_array($lists)) $lists = array_filter($lists, 'is_numeric');

			$sql .= (is_null($lists)) ? " AND ab.list_id IS NULL" : (empty($lists) ? " AND ab.list_id = 0" : " AND ab.list_id IN(".implode(',', $lists).")");
		}

		if(is_array($statuses))
			$sql .= " AND a.status IN (".implode(',', array_filter($statuses, 'is_numeric')).")";

		if($ignore_sent)
			$sql .= " AND b.subscriber_id IS NULL";

		if($ignore_queue)
			$sql .= " AND c.subscriber_id IS NULL";

		$sql .= $this->get_sql_by_condition($conditions);

		if($return_ids){
			$sql .= " GROUP BY a.ID ORDER BY a.ID ASC";

			if(!is_null($limit)) $sql .= " LIMIT ".intval($offset).", ".intval($limit);

		}

		if($returnsql) return $sql;

		$subscribers[$key] = $return_ids ? $wpdb->get_col($sql) : $wpdb->get_var($sql);

		mymail_cache_set( $cache_key, $subscribers );

		return $subscribers[$key];

	}

	public function get_unsent_subscribers($id = NULL, $statuses = NULL, $return_ids = false, $ignore_queue = false, $limit = NULL, $offset = 0){
		return $this->get_subscribers($id, $statuses, $return_ids, true, $ignore_queue, $limit, $offset);
	}

	public function get_sent_subscribers($id = NULL){

		global $wpdb;

		if(false === ($sent_subscribers = mymail_cache_get( 'sent_subscribers' ))){

			$sql = "SELECT a.campaign_id, a.subscriber_id FROM {$wpdb->prefix}mymail_actions AS a WHERE type = 1 ORDER BY a.timestamp ASC";

			$result = $wpdb->get_results($sql);

			$sent_subscribers = array();

			foreach($result as $row){
				if(!isset($sent_subscribers[$row->campaign_id])) $sent_subscribers[$row->campaign_id] = array();
				$sent_subscribers[$row->campaign_id][] = $row->subscriber_id;
			}

			mymail_cache_set( 'sent_subscribers', $sent_subscribers );


		}

		return (is_null($id)) ? $sent_subscribers : (isset($sent_subscribers[$id]) ? $sent_subscribers[$id] : 0 );


	}

	public function get_links($id = NULL, $unique = true){

		global $wpdb;

		$campaign = $this->get($id);
		if(!$campaign) return array();

		$content = $campaign->post_content;

		preg_match_all("/(href)=[\"'](.*)[\"']/Ui", $content, $urls);
		//preg_match_all('@((https?://)([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@',$content,$urls);

		$urls = !empty($urls[2]) ? ($urls[2]) : array();

		return $unique ? array_values(array_unique($urls)) : $urls;

	}


	public function get_totals($id = NULL, $unsubscribes = true, $bounces = false){

		$campaign = $this->get($id);
		if(!$campaign) return 0;
		if(in_array($campaign->post_status, array('finished'))){
			return $this->get_sent($id, false);
		}
		$subscribers_count = $this->get_subscribers($id);

		if($unsubscribes) $subscribers_count += $this->get_unsubscribes($id);
		if($bounces) $subscribers_count += $this->get_bounces($id);

		return $subscribers_count;

	}

	public function get_totals_by_lists($lists = false, $conditions = NULL, $statuses = NULL){

		$subscribers_count = $this->get_subscribers_by_lists($lists, $conditions, $statuses);

		return $subscribers_count;

		return count($subscribers);

	}

	public function get_sent($id = NULL, $total = false){

		return $this->get_action('sent', $id, $total);

	}

	public function get_sent_rate($id = NULL, $total = false){

		$totals = $this->get_totals($id, $total);
		if(!$totals) return 0;

		$sent = $this->get_sent($id, $total);

		return $sent / $totals;

	}

	public function get_errors($id = NULL, $total = false){

		return $this->get_action('errors', $id, $total);

	}

	public function get_error_rate($id = NULL, $total = false){

		$sent = $this->get_sent($id, $total);
		if(!$sent) return 0;

		$errors = $this->get_errors($id, $total);

		return $errors / $sent;

	}

	public function get_opens($id = NULL, $total = false){

		return $this->get_action('opens', $id, $total);

	}

	public function get_open_rate($id = NULL, $total = false){

		$sent = $this->get_sent($id, $total);
		if(!$sent) return 0;

		$opens = $this->get_opens($id, $total);

		return $opens / $sent;

	}

	public function get_clicks($id = NULL, $total = false){

		return $this->get_action('clicks', $id, $total);

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

	public function get_unsubscribes($id = NULL){

		return $this->get_action('unsubscribes', $id);

	}

	public function get_unsubscribe_rate($id = NULL, $total = false){

		$sent = $this->get_sent($id, $total);
		if(!$sent) return 0;

		$unsubscribes = $this->get_unsubscribes($id, $total);

		return $unsubscribes / $sent;

	}

	public function get_adjusted_unsubscribe_rate($id = NULL, $total = false){

		$open = $this->get_opens($id, $total);
		if(!$open) return 0;

		$unsubscribes = $this->get_unsubscribes($id, $total);

		return $unsubscribes / $open;

	}

	public function get_bounces($id = NULL){

		return $this->get_action('bounces', $id);

	}

	public function get_bounce_rate($id = NULL){

		$totals = $this->get_totals($id);
		if(!$totals) return 0;

		$bounces = $this->get_bounces($id);

		return $bounces / ($totals+$bounces);

	}

	private function get_action($action, $id = NULL, $total = false){

		return mymail('actions')->get_by_campaign($id, $action.($total ? '_total' : '' ));

	}

	public function get_clicked_links($id = NULL){

		return mymail('actions')->get_clicked_links($id);

	}

	public function get_error_list($id = NULL){

		return mymail('actions')->get_error_list($id);

	}

	public function get_clients($id = NULL){

		return mymail('actions')->get_clients($id);

	}

	public function get_environment($id = NULL){

		return mymail('actions')->get_environment($id);

	}


	public function get_geo_data_country($id){
	}

	public function get_geo_data_city($id){
	}

	public function get_geo_data($id){

		global $wpdb;

		$sql = "SELECT COUNT(DISTINCT a.subscriber_id) AS count, a.meta_value AS geo, b.meta_value AS coords FROM {$wpdb->prefix}mymail_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id AND b.meta_key = 'coords' WHERE a.meta_key = 'geo' AND a.campaign_id = %d AND a.meta_value != '|' GROUP BY a.meta_value ORDER BY count DESC";

		$result = $wpdb->get_results($wpdb->prepare($sql, $id));

		$geo_data = array();

		foreach($result as $row){
			$geo = explode('|', $row->geo);

			if(!isset($geo_data[$geo[0]]))
				$geo_data[$geo[0]] = array(0 => array(0,0,'',0,''));

			if(!$row->coords){
				$geo_data[$geo[0]][0][3]++;

			}else{
				$coords = $row->coords ? explode(',', $row->coords): array(0,0);

				$geo_data[$geo[0]][] = array(
					floatval($coords[0]),
					floatval($coords[1]),
					$geo[1],
					intval($row->count),
					$row->count.' '._n('opened', 'opens', $row->count, 'mymail'),
				);
			}
		}

		return $geo_data;

	}


	public function get_sql_join_by_condition($conditions) {

		global $wpdb;

		$joins = array();
		$sql = '';

		if(empty($conditions['conditions']) || !is_array($conditions)) return $sql;

		$custom_fields = mymail()->get_custom_fields(true);
		$custom_fields = wp_parse_args( array('firstname', 'lastname'), (array) $custom_fields);
		$meta_fields = array('form', 'referer');

		$wp_user_meta = wp_parse_args(array('wp_user_level', 'wp_capabilities'), mymail('helper')->get_wpuser_meta_fields());

		foreach($conditions['conditions'] as $options){

			$field = esc_sql($options['field']);

			if(in_array($field, $custom_fields)){

				$joins[] = "LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS `field_$field` ON `field_$field`.subscriber_id = a.ID AND `field_$field`.meta_key = '$field'";

			}else if(in_array($field, $wp_user_meta)){
				$joins[] = "LEFT JOIN {$wpdb->usermeta} AS `meta_wp_$field` ON `meta_wp_$field`.user_id = a.wp_id AND `meta_wp_$field`.meta_key = '".str_replace('wp_', $wpdb->prefix, $field)."'";

			}else if(in_array($field, $meta_fields)){

				$joins[] = "LEFT JOIN {$wpdb->prefix}mymail_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = a.ID AND `meta_$field`.meta_key = '$field'";
			}

		}

		if(!empty($joins)) $sql = " ".implode(' ', array_unique($joins));

		return $sql;
	}


	public function get_sql_by_condition($conditions, $tablealias = 'a') {

		global $wpdb;

		$cond = array();
		$sql = '';

		if(empty($conditions['conditions']) || !is_array($conditions)) return $sql;

		$wp_user_meta = array_merge(array('wp_user_level', 'wp_capabilities'), mymail('helper')->get_wpuser_meta_fields());

		$custom_fields = mymail()->get_custom_fields(true);
		$custom_fields = array_merge( array('firstname', 'lastname'), $custom_fields);
		$meta_fields = array('form', 'referer');
		$custom_date_fields = mymail()->get_custom_date_fields(true);
		$timefields = array('added', 'updated', 'signup', 'confirm');

		foreach($conditions['conditions'] as $options){

			$field = esc_sql($options['field']);
			$value = esc_sql(stripslashes($options['value']));
			$extra = '';

			switch ($field) {
				case 'rating':
					$value = str_replace(',', '.', $value);
					if(strpos($value, '%') !== false || $value > 5){
						$value = floatval($value)/100;
					}else if($value >= 1){
						$value = floatval($value)*0.2;
					}
					break;
			}

			switch($options['operator']){
				case 'is':
				case 'is_not':

					if(in_array($field, $custom_date_fields)){
						$c = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
					}else if(in_array($field, $timefields)){
						$c = "STR_TO_DATE(FROM_UNIXTIME($tablealias.$field),'%Y-%m-%d')";
					}else if(in_array($field, $custom_fields)){
						$c = "`field_$field`.meta_value";
					}else if(in_array($field, $meta_fields)){
						$c = "`meta_$field`.meta_value";
					}else if(in_array($field, $wp_user_meta)){
						$c = "`meta_wp_$field`.meta_value";
						if($field == 'wp_capabilities'){
							$value = "s:".strlen($value).":\"".strtolower($value)."\";b:1;";
							$cond[] = "`meta_wp_$field`.meta_value ".($options['operator'] == 'is' ? 'LIKE' : 'NOT LIKE')." '%$value%'";
							break;
						}
					}else{
						$c = "$tablealias.$field";
					}

					$c .= " ".($options['operator'] == 'is' ? '=' : '!=')." '$value'";

					$cond[] = $c;
				break;

				case 'contains':
				case 'contains_not':
					if($field == 'wp_capabilities'){
						$value = "'a:%".strtolower($value)."%'";
					}else{
						$value = "'%$value%'";
					}
					if(in_array($field, $custom_fields)){
						$c = "`field_$field`.meta_value";
					}else if(in_array($field, $meta_fields)){
						$c = "`meta_$field`.meta_value";
					}else if(in_array($field, $wp_user_meta)){
						$c = "`meta_wp_$field`.meta_value";
					}else{
						$c = "$tablealias.$field";
					}

					$c .= " ".($options['operator'] == 'contains' ? 'LIKE' : 'NOT LIKE')." $value";

					$cond[] = $c;
				break;

				case 'begin_with':
					if($field == 'wp_capabilities'){
						$value = "'%\"".strtolower($value)."%'";
					}else{
						$value = "'$value%'";
					}
					if(in_array($field, $custom_fields)){
						$c = "`field_$field`.meta_value";
					}else if(in_array($field, $meta_fields)){
						$c = "`meta_$field`.meta_value";
					}else if(in_array($field, $wp_user_meta)){
						$c = "`meta_wp_$field`.meta_value";
					}else{
						$c = "$tablealias.$field";
					}

					$c .= " LIKE $value";

					$cond[] = $c;
				break;

				case 'end_with':
					if($field == 'wp_capabilities'){
						$value = "'%".strtolower($value)."\"%'";
					}else{
						$value = "'%$value'";
					}

					if(in_array($field, $custom_fields)){
						$c = "`field_$field`.meta_value";
					}else if(in_array($field, $meta_fields)){
						$c = "`meta_$field`.meta_value";
					}else if(in_array($field, $wp_user_meta)){
						$c = "`meta_wp_$field`.meta_value";
					}else{
						$c = "$tablealias.$field";
					}

					$c .= " LIKE $value";

					$cond[] = $c;
				break;

				case 'is_greater_equal':
				case 'is_smaller_equal':
						$extra = '=';
				case 'is_greater':
				case 'is_smaller':

					if(in_array($field, $custom_date_fields)){
						$c = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
						$value = "'$value'";
					}else if(in_array($field, $timefields)){
						$c = "STR_TO_DATE(FROM_UNIXTIME($tablealias.$field),'%Y-%m-%d')";
						$value = "'$value'";
					}else if(in_array($field, $custom_fields)){
						$c = "`field_$field`.meta_value";
						$value = is_numeric($value) ? floatval($value) : "'$value'";
					}else if(in_array($field, $meta_fields)){
						$c = "`meta_$field`.meta_value";
						$value = is_numeric($value) ? floatval($value) : "'$value'";
					}else if(in_array($field, $wp_user_meta)){
						$c = "`meta_wp_$field`.meta_value";
						if($field == 'wp_capabilities')
							$value = "'NOTPOSSIBLE'";
					}else{
						$c = "$tablealias.$field";
						$value = floatval($value);
					}

					$c .= " ".($options['operator'] == 'is_greater' || $options['operator'] == 'is_greater_equal' ? '>'.$extra : '<'.$extra)." $value";



					$cond[] = $c;
				break;

				case 'not_pattern':
					$extra = 'NOT ';
				case 'pattern':

					if(in_array($field, $custom_date_fields)){
						$c = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
					}else if(in_array($field, $timefields)){
						$c = "STR_TO_DATE(FROM_UNIXTIME($tablealias.$field),'%Y-%m-%d')";
					}else if(in_array($field, $custom_fields)){
						$c = "`field_$field`.meta_value";
					}else if(in_array($field, $meta_fields)){
						$c = "`meta_$field`.meta_value";
					}else{
						$c = "$tablealias.$field";
						if($field == 'wp_capabilities'){
							$value = "'NOTPOSSIBLE'";
							break;
						}
					}
					if($value == '') $value = '.';

					$c .= " ".$extra."REGEXP '$value'";

					$cond[] = $c;
				break;

			}

		}

		if(!empty($cond)) $sql .= " AND (".implode(' '.$conditions['operator'].' ', $cond).")";
		return $sql;
	}

	public function create_list_from_option($name, $campaign_id, $option = 'open', $countonly = false) {

		global $wpdb;

		if(!current_user_can('mymail_edit_lists')) return false;

		$campaign = $this->get($campaign_id);

		if(!$campaign || $campaign->post_status == 'autoresponder') return false;

		switch($option){
			case 'sent';
			case 'not_sent';

				$sql = $wpdb->prepare("SELECT a.ID FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d WHERE b.campaign_id IS NOT NULL AND b.type = 1 GROUP BY a.ID", $campaign->ID);

				break;
			case 'open':
			case 'not_open':

				$sql = $wpdb->prepare("SELECT a.ID FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d WHERE b.campaign_id IS NOT NULL AND b.type = 2 GROUP BY a.ID", $campaign->ID);

				break;
			case 'click':

				$sql = $wpdb->prepare("SELECT a.ID FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d WHERE b.campaign_id IS NOT NULL AND b.type = 3 GROUP BY a.ID", $campaign->ID);

				break;
			case 'open_not_click':

				$sql = $wpdb->prepare("SELECT a.ID, c.type FROM {$wpdb->prefix}mymail_subscribers AS a LEFT JOIN {$wpdb->prefix}mymail_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d LEFT JOIN {$wpdb->prefix}mymail_actions AS c ON a.ID = c.subscriber_id AND c.campaign_id = %d WHERE b.campaign_id IS NOT NULL AND b.type = 2 OR c.type = 3 GROUP BY a.ID HAVING c.type != 3", $campaign->ID, $campaign->ID);

				break;
			default:
				$sql .= " WHERE 1";
				break;
			}

		$subscribers = $wpdb->get_col($sql);

		if(in_array($option, array('not_sent', 'not_open'))){
			$all = $this->get_subscribers($campaign->ID, NULL, true);
			$subscribers = array_values(array_diff($all, $subscribers));
		}

		if($countonly) return count($subscribers);

		$options = array(
			'sent' => __('who have received', 'mymail'),
			'not_sent' => __('who have not received', 'mymail'),
			'open' => __('who have opened', 'mymail'),
			'open_not_click' => __('who have opened but not clicked', 'mymail'),
			'click' => __('who have opened and clicked', 'mymail'),
			'not_open' => __('who have not opened', 'mymail'),
		);

		$list = mymail('lists')->add_segment(array(
			'name' => $name,
			'description'=> sprintf(_x('A segment of all %1$s of %2$s', 'segment of all [recipients] from campaign [campaign]', 'mymail'), $options[$option], '"'.$campaign->post_title.'"'),
			'slug' => 'segment-'.$option.'-of-'.$campaign->ID,
		), true, $subscribers);

		return true;

	}


	public function get_recipients_part($campaign_id, $parts = array('unopen', 'opens', 'clicks', 'unsubs', 'bounces'), $page = 0, $orderby = 'sent', $order = 'ASC'  ) {

		global $wpdb;

		$return = '';

		$limit = 1000;
		$offset = intval($page)*$limit;

		$fields = array(
			'ID' => __('ID', 'mymail'),
			'email' => mymail_text('email'),
			'status' => __('Status', 'mymail'),
			'firstname' => mymail_text('firstname'),
			'lastname' => mymail_text('lastname'),
			'sent' => __('Sent date', 'mymail'),
			'open' => __('Open date', 'mymail'),
			'open_count' => __('Open Count', 'mymail'),
			'clicks' => __('Clicks', 'mymail'),
			'click_count' => __('Click Count', 'mymail'),
			'unsubs' => __('Unsubscribes', 'mymail'),
			'bounces' => __('Bounces', 'mymail'),
		);

		if(!in_array($orderby, array_keys($fields))) $orderby = 'sent';
		if(!in_array($order, array('ASC', 'DESC'))) $order = 'ASC';

		$sql = $this->get_recipients_part_sql($campaign_id, $parts);
		$sql .= " ORDER BY $orderby $order";
		$sql .= " LIMIT $offset, $limit";

		$subscribers = $wpdb->get_results( $sql );

		$count = 0;

		$timeformat = get_option('date_format') . ' ' . get_option('time_format');
		$timeoffset = mymail('helper')->gmt_offset(true);

		$subscribers_count = count($subscribers);

		$unopen = in_array('unopen', $parts);
		$opens = in_array('opens', $parts);
		$clicks = in_array('clicks', $parts);
		$unsubs = in_array('unsubs', $parts);
		$bounces = in_array('bounces', $parts);

		if(!$offset){
			$return .= '<div class="ajax-list-header filter-list"><label>'.__('Filter', 'mymail').': </label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-unopen" value="unopen" '.checked($unopen, true, false).'> '.__('unopens', 'mymail').' </label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-open" value="opens" '.checked($opens, true, false).'> '.__('opens', 'mymail').' </label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-click" value="clicks"'.checked($clicks, true, false).'> '.__('clicks', 'mymail').' </label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-unsubscribes" value="unsubs"'.checked($unsubs, true, false).'> '.__('unsubscribes', 'mymail').'</label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-bounces" value="bounces"'.checked($bounces, true, false).'> '.__('bounces', 'mymail').' </label> ';
			$return .= '<label>'.__('order by', 'mymail').' ';
			$return .= '<select class="recipients-order">';
			foreach($fields as $field => $name){
				$return .= '<option value="'.$field.'" '.selected($field, $orderby, false).'>'.$name.'</option>';
			}
			$return .= '</select></label>';
			$return .= '<a title="'.__('order direction', 'mymail').'" class="recipients-order mymail-icon '.($order == 'ASC' ? 'asc' : 'desc').'"></a>';
			$return .= '</div>';
		}

		if(!$offset) $return .= '<table class="wp-list-table widefat recipients-list"><tbody>';


		foreach($subscribers as $i => $subscriber){
		//for($i = 0; $i < $subscribers_count; $i++){

			//$campaigndata = maybe_unserialize($subscriber->meta);
			//if(!isset($campaigndata[$campaign_ID])) continue;
			$name = trim($subscriber->firstname.' '.$subscriber->lastname);

			$return .= '<tr '.(!($i%2) ? ' class="alternate" ' : '').'>';
			$return .= '<td class="textright">'.($count+$offset+1).'</td><td><a class="show-receiver-detail" data-id="' . $subscriber->ID . '">' . ($name ? $name .' &ndash; ' : '') .$subscriber->email . '</a></td>';
			$return .= '<td title="'.__('sent', 'mymail').'">' . ($subscriber->sent ? str_replace(' ', '&nbsp;', date($timeformat, $subscriber->sent+$timeoffset)) : '&ndash;') . '</td>';
			$return .= '<td>' . ($subscriber->open_count ? '<span title="'.__('has opened', 'mymail').'" class="mymail-icon icon-mm-open"></span>' : '<span title="'.__('has not opened yet', 'mymail').'" class="mymail-icon icon-mm-unopen"></span>') . '</td>';
			$return .= '<td>' . ($subscriber->click_count_total ? sprintf( _n( '%s click', '%s clicks', $subscriber->click_count_total, 'mymail'), $subscriber->click_count_total) : '') . '</td>';
			$return .= '<td>' . ($subscriber->unsubs ? '<span title="'.__('has unsubscribed', 'mymail').'" class="mymail-icon icon-mm-unsubscribe"></span>' : '') . '</td>';
			$return .= '<td>';
			$return .= ($subscriber->bounce_count ? '<span class="bounce-indicator mymail-icon icon-mm-bounce ' . ($subscriber->status == 3 ? 'hard' : 'soft' ) . '" title="' . sprintf(_n('%s bounce', '%s bounces', $subscriber->bounce_count, 'mymail'), $subscriber->bounce_count) . '"></span>' : '');
			$return .= ($subscriber->status == 4) ? '<span class="bounce-indicator mymail-icon icon-mm-bounce" title="' .__('an error occurred while sending to this receiver', 'mymail'). '">E</span>' : '';
			$return .= '</td>';
			$return .= '</tr>';
			$return .= '<tr id="receiver-detail-'. $subscriber->ID .'" class="receiver-detail'.(!($i%2) ? '  alternate' : '').'">';
			$return .= '<td></td><td colspan="6">';
			$return .= '<div class="receiver-detail-body"></div>';
			$return .= '</td>';
			$return .= '</tr>';

			$count++;

		}

		if($count && $limit == $subscribers_count) $return .= '<tr '.($i%2 ? ' class="alternate" ' : '').'><td colspan="7"><a class="load-more-receivers button aligncenter" data-page="'.($page+1).'" data-types="'.implode(',', $parts).'" data-order="'.$order.'" data-orderby="'.$orderby.'">'. __('load more recipients from this campaign', 'mymail').'</a>'.'<span class="spinner"></span></td></tr>';


		if(!$offset) $return .= '</tbody></table>';
		return $return;

	}

	public function get_recipients_part_sql($campaign_id, $parts = array('unopen', 'opens', 'clicks', 'unsubs', 'bounces')) {

		global $wpdb;

		$unopen = in_array('unopen', $parts);
		$opens = in_array('opens', $parts);
		$clicks = in_array('clicks', $parts);
		$unsubs = in_array('unsubs', $parts);
		$bounces = in_array('bounces', $parts);

		$sql = "SELECT a.ID, a.email, a.hash, a.status, firstname.meta_value AS firstname, lastname.meta_value AS lastname";
		$sql .= ", sent.timestamp AS sent, sent.count AS sent_count";
		$sql .= ", open.timestamp AS open, COUNT(open.count) AS open_count";
		$sql .= ", click.timestamp AS clicks, COUNT(click.count) AS click_count, SUM(click.count) AS click_count_total";
		$sql .= ", unsub.timestamp AS unsubs, unsub.count AS unsub_count";
		$sql .= ", bounce.timestamp AS bounces, bounce.count AS bounce_count";

		$sql .= " FROM {$wpdb->prefix}mymail_subscribers AS a";

		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS firstname ON a.ID = firstname.subscriber_id AND firstname.meta_key = 'firstname'";
		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS lastname ON a.ID = lastname.subscriber_id AND lastname.meta_key = 'lastname'";

		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_actions AS sent ON a.ID = sent.subscriber_id AND sent.type = 1";
		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_actions AS open ON a.ID = open.subscriber_id AND open.type = 2 AND open.campaign_id = sent.campaign_id";
		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_actions AS click ON a.ID = click.subscriber_id AND click.type = 3 AND click.campaign_id = sent.campaign_id";
		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_actions AS unsub ON a.ID = unsub.subscriber_id AND unsub.type = 4 AND unsub.campaign_id = sent.campaign_id";
		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_actions AS bounce ON a.ID = bounce.subscriber_id AND bounce.type IN (5,6) AND bounce.campaign_id = sent.campaign_id";

		$sql .= " WHERE sent.campaign_id = %d";

		$extra = array();

		if($unopen) $extra[] = "open.timestamp IS NULL";
		if($opens) $extra[] = "open.timestamp IS NOT NULL";
		if($clicks) $extra[] = "click.timestamp IS NOT NULL";
		if($unsubs) $extra[] = "unsub.timestamp IS NOT NULL";
		if($bounces) $extra[] = "bounce.timestamp IS NOT NULL";

		if(!empty($extra)) $sql .= " AND (".implode(' OR ', $extra).")";

		$sql .= " GROUP BY a.ID";

		return $wpdb->prepare($sql, $campaign_id);

	}

	public function heartbeat( $response, $data ) {

		global $post;

		if(isset($data['wp_autosave']) && $data['wp_autosave']['post_type'] == 'newsletter')
			kses_remove_filters();

		if(!isset($data['mymail'])) return $response;

		$return = array();

		switch($data['mymail']['page']){

			case 'overview':

				if(!isset($_POST['data']['mymail']['ids'])) break;
				$ids = array_filter($_POST['data']['mymail']['ids'], 'is_numeric');
				$return = array_fill_keys($ids, NULL);

				foreach($ids as $id){

					$post = $this->get($id);
					if(!$post) continue;
					$meta = $this->meta($id);
					$totals = $this->get_totals($id);
					$sent = $this->get_sent($id);

					$return[$id] = array(
						'status' => $post->post_status,
						'total' => $totals,
						'sent' => $sent,
						'sent_formatted' => "&nbsp;" . sprintf(__('%1$s of %2$s sent', 'mymail'), number_format_i18n($sent), number_format_i18n($totals)),
						'column-status' => $this->get_columns_content('status'),
						'column-total' => $this->get_columns_content('total'),
						'column-open' => $this->get_columns_content('open'),
						'column-click' => $this->get_columns_content('click'),
						'column-unsubs' => $this->get_columns_content('unsubs'),
						'column-bounces' => $this->get_columns_content('bounces'),
					);

				}
				break;

			case 'edit':

				$id = intval($_POST['data']['mymail']['id']);

				$post = $this->get($id);
				if(!$post) break;

				$meta = $this->meta($id);
				$totals = $this->get_totals($id);
				$sent = $this->get_sent($id);
				$opens = $this->get_opens($id);
				$clicks = $this->get_clicks($id);
				$clicks_total = $this->get_clicks($id, true);
				$unsubs = $this->get_unsubscribes($id);
				$bounces = $this->get_bounces($id);
				$open_rate = round($this->get_open_rate($id)*100, 2);
				$click_rate = round($this->get_click_rate($id)*100, 2);
				$bounce_rate = round($this->get_bounce_rate($id)*100, 2);
				$unsubscribe_rate = round($this->get_unsubscribe_rate($id)*100, 2);

				$environment = $this->get_environment($id);

				$geolocation = '';

				if($geo_data = $this->get_geo_data($post->ID)) :

					$unknown_cities = array();
					$countrycodes = array();

					foreach($geo_data as $countrycode => $data){
						$x = wp_list_pluck($data, 3);
						if($x) $countrycodes[$countrycode] = array_sum($x);
						if($data[0][3]) $unknown_cities[$countrycode] = $data[0][3];
					}

					arsort($countrycodes);
					$total = array_sum($countrycodes);

					$i = 0;
					$geolocation = '';

					foreach($countrycodes as $countrycode => $count) {

				$geolocation .= '<label title="'.mymail('geo')->code2Country($countrycode).'"><span class="big"><span class="mymail-flag-24 flag-'.strtolower($countrycode).'"></span> '.round($count/$opens*100, 2).'%</span></label> ';
						if(++$i >= 5) break;
					}

				endif;

				$return[$id] = array(
					'status' => $post->post_status,
					'total' => $post->post_type == 'autoresponder' ? $sent : $totals,
					'sent' => $sent,
					'opens' => $opens,
					'clicks' => $clicks,
					'clicks_total' => $clicks_total,
					'unsubs' => $unsubs,
					'bounces' => $bounces,
					'open_rate' => $open_rate,
					'click_rate' => $click_rate,
					'unsub_rate' => $unsubscribe_rate,
					'bounce_rate' => $bounce_rate,
					'total_f' => number_format_i18n($totals),
					'sent_f' => number_format_i18n($sent),
					'opens_f' => number_format_i18n($opens),
					'clicks_f' => number_format_i18n($clicks),
					'clicks_total_f' => number_format_i18n($clicks_total),
					'unsubs_f' => number_format_i18n($unsubs),
					'bounces_f' => number_format_i18n($bounces),
					'environment' => $environment,
					'clickbadges' => array(
						'total' => $this->get_clicks($id, true),
						'clicks' => $this->get_clicked_links($id),
					),
					'sent_formatted' => "&nbsp;" . sprintf(__('%1$s of %2$s sent', 'mymail'), number_format_i18n($sent), number_format_i18n($totals)),
					'geo_location' => $geolocation,

				);

				break;
		}

		$response['mymail'] = $return;

		//check for missing cron
		mymail('cron')->check();
		//maybe change status
		mymail('queue')->update_status();

		return $response;
	}


	public function send_to_subscriber( $campaign_id, $subscriber_id, $track = true, $force = false, $log = false ) {

		$data = mymail('subscribers')->get($subscriber_id, true);

		$userdata = mymail('subscribers')->get_userdata($data);
		$metadata = mymail('subscribers')->get_metadata($data, $userdata);

		$metadata->campaign_id = $campaign_id;

		$result = $this->send( $metadata, $userdata, $track, $force, $log );

		return $result;

	}

	public function send( $metadata, $userdata, $track = true, $force = false, $log = true ) {

		global $wpdb;

		$campaign = $this->get($metadata->campaign_id);

		if(!$campaign || $campaign->post_type != 'newsletter') return new WP_Error('wrong_post_type', __('wrong post type', 'mymail'));

		if(!in_array($metadata->status, array(0,1,2)) && !$force) return new WP_Error('user_unsubscribed', __('User has not subscribed', 'mymail'));;

		$campaign_meta = $this->meta($metadata->campaign_id);

		$mail = mymail('mail');

		//stop if send limit is reached
		if($mail->sentlimitreached) return new WP_Error('sendlimit_reached', sprintf(__('Sent limit of %1$s reached! You have to wait %2$s before you can send more mails!', 'mymail'), mymail_option('send_limit'), human_time_diff(get_option('_transient_timeout__mymail_send_period_timeout'))));

		$to = $metadata->email;

		//$metadata->hash = str_repeat('1', 32);

		$mail->to = $to;
		$mail->subject = $campaign_meta['subject'];
		$mail->from = $campaign_meta['from_email'];
		$mail->from_name = $campaign_meta['from_name'];
		$mail->reply_to = $campaign_meta['reply_to'];
		$mail->bouncemail = mymail_option('bounce');
		$mail->preheader = $campaign_meta['preheader'];
		$mail->embed_images = $campaign_meta['embed_images'];

		$mail->add_tracking_image = $track;
		$mail->hash = $metadata->hash;

		$content = mymail()->sanitize_content($campaign->post_content, NULL, $campaign_meta['head']);

		$placeholder = mymail('placeholder', $content);

		$mail->set_campaign($campaign->ID);
		$mail->set_subscriber($metadata->ID);
		$placeholder->set_campaign($campaign->ID);
		$placeholder->set_subscriber($metadata->ID);

		$unsubscribelink = mymail()->get_unsubscribe_link($metadata->campaign_id);
		$forwardlink = mymail()->get_forward_link($metadata->campaign_id, $metadata->email);
		$profilelink = mymail()->get_profile_link($metadata->campaign_id, $metadata->hash);

		$placeholder->add(array(
			'preheader' => $campaign_meta['preheader'],
			'subject' => $campaign_meta['subject'],
			'webversion' => '<a href="{webversionlink}">' . mymail_text('webversion') . '</a>',
			'webversionlink' => get_permalink($campaign->ID),
			'unsub' => '<a href="{unsublink}">' . mymail_text('unsubscribelink') . '</a>',
			'unsublink' => $unsubscribelink,
			'forward' => '<a href="{forwardlink}">' . mymail_text('forward') . '</a>',
			'forwardlink' => $forwardlink,
			'profile' => '<a href="{profilelink}">' . mymail_text('profile') . '</a>',
			'profilelink' => $profilelink,
			'email' => '<a href="">{emailaddress}</a>'
		));

		$placeholder->add(wp_parse_args(array(
			'emailaddress' => $metadata->email
		), (array) $userdata));

		$placeholder->share_service(get_permalink($campaign->ID), $campaign->post_title);

		$content = $placeholder->get_content();

		if($track){

			//replace links
			$content = mymail()->replace_links($content, $metadata->hash,  $campaign->ID);

		}


		$mail->content = $content;

		if(!$campaign_meta['autoplaintext']){
			$placeholder->set_content($campaign->post_excerpt);
			$mail->plaintext = mymail()->plain_text($placeholder->get_content(), true);
		}

		$MID = mymail_option('ID');

		$mail->add_header('X-MyMail', $metadata->hash);
		$mail->add_header('X-MyMail-Campaign', $campaign->ID);
		$mail->add_header('X-MyMail-ID', $MID);
		$mail->add_header('List-Unsubscribe', '<'.$unsubscribelink.'>');

		$placeholder->set_content($mail->subject);
		$mail->subject = $placeholder->get_content();

		$mail->prepare_content();

		//return new WP_Error('user_error', 'test error');
		$result = $mail->send();

		if($result && !is_wp_error( $result )){
			if($log) do_action('mymail_send', $metadata->ID, $campaign->ID);
			return true;
		}

		if(is_wp_error( $result )) return $result;

		if($mail->is_user_error()){
			if($log) do_action('mymail_subscriber_error', $metadata->ID, $campaign->ID, $mail->last_error->getMessage());
			return new WP_Error('user_error', $mail->last_error->getMessage());
		}

		if($mail->last_error){
			if($log) do_action('mymail_campaign_error', $metadata->ID, $campaign->ID, $mail->last_error->getMessage());
			return new WP_Error('error', $mail->last_error->getMessage());
		}

		return new WP_Error('unknown', __('unknown', 'mymail'));


	}


	public function change_status($post, $new_status, $silent = false) {
		if (!$post)
			return false;

		if ($post->post_status == $new_status)
			return true;

		$old_status = $post->post_status;

		global $wpdb;

		if ($wpdb->update($wpdb->posts, array('post_status' => $new_status), array('ID' => $post->ID))) {
			if (!$silent) wp_transition_post_status($new_status, $old_status, $post);
			return true;
		}

		return false;

	}


	public function check_for_autoresponder($new_status, $old_status, $post){

		if($new_status == $old_status) return;

		if('publish' != $new_status) return;

		if('newsletter' == $post->post_type) return;

		if(get_post_meta( $post->ID, 'mymail_ignore', true )) return;

		$now = time();

		$campaigns = $this->get_autoresponder();

		if(empty($campaigns)) return;

		//delete cache;
		mymail_cache_delete('get_last_post');

		foreach($campaigns as $campaign){

			if(!$this->meta($campaign->ID, 'active')) continue;
			$meta = $this->meta($campaign->ID, 'autoresponder');

			if('mymail_post_published' == $meta['action']){

				if($meta['post_type'] != $post->post_type) continue;

				//if post count is reached
				if(!(++$meta['post_count_status']%($meta['post_count']+1))){

					if(isset($meta['terms'])){

						$pass = true;

						foreach($meta['terms'] as $taxonomy => $term_ids){
							//ignore "any taxonomy"
							if($term_ids[0] == '-1') continue;

							$post_terms = get_the_terms ( $post->ID, $taxonomy );

							//no post_terms set but required => give up (not passed)
							if(!$post_terms){
								$pass = false;
								break;
							}

							$pass = $pass && !!count(array_intersect(wp_list_pluck($post_terms, 'term_id'), $term_ids));


						}

						if(!$pass) continue;
					}


					$integer = floor($meta['amount']);
					$decimal = $meta['amount']-$integer;

					$send_offset = (strtotime('+'.$integer.' '.$meta['unit'], 0)+(strtotime('+1 '.$meta['unit'], 0)*$decimal));

					if($new_id = $this->autoresponder_to_campaign($campaign->ID, $send_offset, $meta['issue']++)){

						$new_campaign = $this->get($new_id);

						mymail_notice(sprintf(__('New campaign %1$s has been created and is going to be sent in %2$s.', 'mymail'), '<strong>"<a href="post.php?post='.$new_campaign->ID.'&action=edit">'.$new_campaign->post_title.'</a>"</strong>', '<strong>'.human_time_diff($now+$send_offset).'</strong>').' <a href="edit.php?post_type=newsletter&pause=' .$new_campaign->ID. '&_wpnonce=' .wp_create_nonce('mymail_nonce'). '">'.__('Pause campaign', 'mymail').'</a>', 'error', true);

						do_action('mymail_autoresponder_post_published', $campaign->ID, $new_id);

					}
				}

				$this->update_meta($campaign->ID, 'autoresponder', $meta);

			}else if('mymail_autoresponder_timebased' == $meta['action']){

				if($meta['time_post_type'] != $post->post_type) continue;

				if(!isset($meta['time_conditions'])) continue;

				$meta['post_count_status']++;

				$this->update_meta($campaign->ID, 'autoresponder', $meta);

				mymail('queue')->autoresponder_timebased( $campaign->ID, true );

			}
		}

	}


	public function set_template($slug, $file = 'index.html', $verify = false) {

		if ($verify) {

			if (!is_dir(mymail('templates')->path . '/'.  $slug)) {
				$slug = mymail_option('default_template', $this->defaultTemplate);
			}
			if (!file_exists(mymail('templates')->path. '/' . $slug . '/' . $file)) {
				$file = 'index.html';
			}
		}

		$this->template = $slug;
		$this->templatefile = $file;

		$this->templateobj = mymail('template', $slug, $file);

	}


	public function get_template() {
		return $this->template;
	}

	public function get_file() {
		return (!empty($this->templatefile)) ? $this->templatefile : 'index.html';
	}


	public function get_template_by_id($id, $file, $modules = true, $editorstyle = false) {
		$post = get_post($id);
		//must be a newsletter and have a content
		if ($post->post_type == 'newsletter' && !empty($post->post_content)) {
			$html = $post->post_content;

			if ($editorstyle) $html = str_replace('</head>', $this->iframe_script_styles().'</head>', $html);

			$html = str_replace(' !DOCTYPE', '!DOCTYPE', $html);

			//error_reporting(E_ALL);
			if(strpos($html, 'data-editable')){

				$templateobj = mymail('template');
				$x = $templateobj->new_template_language($html);
				$html = $x->saveHTML();

			}

		} else if ($post->post_type == 'newsletter') {

			$html = $this->get_template_by_slug($this->get_template(), $file, $modules, $editorstyle);

		} else {

			$html = '';

		}


		return $html;

	}


	public function get_template_by_slug($slug, $file = 'index.html', $modules = true, $editorstyle = false) {

		$template = mymail('template', $slug, $file);
		$html = $template->get($modules, true);

		if ($editorstyle) $html = str_replace('</head>', $this->iframe_script_styles().'</head>', $html);

		return $html;

	}

	private function iframe_script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style('mymail-icons', MYMAIL_URI . 'assets/css/icons'.$suffix.'.css', array(), MYMAIL_VERSION);
		wp_register_style('mymail-editor-style', MYMAIL_URI . 'assets/css/editor-style'.$suffix.'.css', array('mymail-icons'), MYMAIL_VERSION);
		wp_register_script('mymail-editor-script', MYMAIL_URI . 'assets/js/editor-script'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);

		wp_localize_script('mymail-editor-script', 'mymaildata', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'url' => MYMAIL_URI,
			'_wpnonce' => wp_create_nonce( 'mymail_nonce' ),
			'plupload' => array(
				'runtimes' => 'html5,flash',
				'browse_button' => 'mymail-editorimage-upload-button',
				//'container' => 'plupload-upload-ui',
				//'drop_element' => 'drag-drop-area',
				'file_data_name' => 'async-upload',
				'multiple_queues' => true,
				'max_file_size' => wp_max_upload_size() . 'b',
				'url' => admin_url('admin-ajax.php'),
				'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				'filters' => array( array('title' => __( 'Image files', 'mymail' ), 'extensions' => 'jpg,gif,png') ),
				'multipart' => true,
				'urlstream_upload' => true,
				'multipart_params' => array(
					"action" => "mymail_editor_image_upload_handler",
					"ID" => isset($_GET['id']) ? intval($_GET['id']) : NULL,
					"_wpnonce" => wp_create_nonce('mymail_nonce'),
				),
				'multi_selection' => false
			)
		));
		wp_localize_script('mymail-editor-script', 'mymailL10n', array(
			'ready' => __('ready!', 'mymail'),
			'error' => __('error!', 'mymail'),
			'error_occurs' => __('An error occurs while uploading', 'mymail'),
			'unsupported_format' => __('Unsupported file format', 'mymail'),
		));

		ob_start();

		wp_print_styles('mymail-icons');
		wp_print_styles('mymail-editor-style');
		wp_print_scripts('jquery');
		wp_print_scripts('jquery-ui-draggable');
		wp_print_scripts('jquery-ui-droppable');
		wp_print_scripts('jquery-ui-sortable');
		wp_print_scripts('jquery-touch-punch');
		wp_print_scripts('plupload-all');
		wp_print_scripts('mymail-editor-script');

		$script_styles = ob_get_contents();


		ob_end_clean();

		return $script_styles;

	}

	public function revision_field_post_content($content, $field) {

		global $post, $mymail_revisionnow;

		if ($post->post_type != 'newsletter')
			return $content;

		$data = get_post_meta($post->ID, 'mymail-data', true);
		$ids = (isset($_REQUEST['revision'])) ? array(
			(int) $_REQUEST['revision']
		) : array(
			(int) $_REQUEST['left'],
			(int) $_REQUEST['right']
		);

?>
		<tr id="revision-field-<?php echo $field; ?>-preview">
			<th scope="row"><h2>
			<?php

		if (!$mymail_revisionnow && isset($_REQUEST['left'])) {
			printf(__('Older: %s', 'mymail'), wp_post_revision_title(get_post($_REQUEST['left'])));
		} else if ($mymail_revisionnow && isset($_REQUEST['right'])) {
				printf(__('Newer: %s', 'mymail'), wp_post_revision_title(get_post($_REQUEST['left'])));
			} else {
			_e('Preview', 'mymail');
		}
		$mymail_revisionnow = (!$mymail_revisionnow) ? $ids[0] : (isset($ids[1]) ? $ids[1] : $mymail_revisionnow);

?>
			</h2></th>
			<td><iframe id="mymail_iframe" src="<?php echo admin_url('admin-ajax.php?action=mymail_get_template&id=' . $post->ID . '&revision=' . $mymail_revisionnow . '&template=&_wpnonce=' . wp_create_nonce('mymail_nonce') . '&editorstyle=0&nocache=' . time()); ?>" width="50%" height="640" scrolling="auto" frameborder="0"></iframe></td>
		</tr>
		<?php

		$head = isset($data['head']) ? $data['head'] : NULL;

		return mymail()->sanitize_content($content, NULL, $head);
	}

	public function remove_revisions($post_id) {

		if (!$post_id)
			return false;

		global $wpdb;

		$wpdb->query($wpdb->prepare("DELETE a FROM $wpdb->posts AS a WHERE a.post_type = '%s' AND a.post_parent = %d", 'revision', $post_id));
	}


	private function replace_colors($content) {
		//replace the colors
		global $post_id;
		global $post;

		$html = $this->templateobj->get(true);
		$colors = array();
		preg_match_all('/#[a-fA-F0-9]{6}/', $html, $hits);
		$original_colors = array_unique($hits[0]);
		$html = $post->post_content;

		if(!empty($html) && isset($this->post_data['template']) && $this->post_data['template'] == $this->get_template() && $this->post_data['file'] == $this->get_file()){
			preg_match_all('/#[a-fA-F0-9]{6}/', $html, $hits);
			$current_colors = array_unique($hits[0]);
		}else{
			$current_colors = $original_colors;
		}

		if (isset($this->post_data) && isset($this->post_data['newsletter_color'])) {

			$search = $replace = array();
			foreach ($this->post_data['newsletter_color'] as $from => $to) {

				$to = array_shift($current_colors);
				if ($from == $to)
					continue;
				$search[] = $from;
				$replace[] = $to;
			}
			$content = str_replace($search, $replace, $content);
		}

		return $content;

	}


}
