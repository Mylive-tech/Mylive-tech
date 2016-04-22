<?php if (!defined('ABSPATH')) die('not allowed');

class mymail_dashboard {

	public function __construct( ) {

		add_action('admin_init', array( &$this, 'init'));

	}

	public function init() {

		add_filter('dashboard_glance_items', array( &$this, 'dashboard_glance_items'), 99 );
		add_action('wp_dashboard_setup', array( &$this, 'add_widgets'));

	}

	public function widget() {
		include MYMAIL_DIR . 'views/dashboard.php';
	}


	public function add_widgets() {

		if (!current_user_can('mymail_dashboard_widget')) return;

		wp_add_dashboard_widget('dashboard_mymail', __('Newsletter', 'mymail'), array( &$this, 'widget'));

		add_action('admin_enqueue_scripts', array( &$this, 'script_styles'), 10, 1);

		//reposition the dashboard widget
		global $wp_meta_boxes;

		$row = 0;
		$col = 'side';

		$dashboard['normal'] = $wp_meta_boxes['dashboard']['normal']['core'];
		$dashboard['side'] = isset($wp_meta_boxes['dashboard']['side']['high'])
				? $wp_meta_boxes['dashboard']['side']['high']
				: $wp_meta_boxes['dashboard']['side']['core'];

		$widget = array('dashboard_mymail' => array_pop($dashboard['normal']));

		$sorted_dashboard = (array) array_splice($dashboard[$col], 0, $row, true) + $widget + (array) array_splice($dashboard[$col], $row, 999, true);

		$wp_meta_boxes['dashboard']['normal']['core'] = $dashboard['normal'];
		$wp_meta_boxes['dashboard'][$col]['core'] = $sorted_dashboard;
	}

	public function dashboard_glance_items($elements) {

		$autoresponder = count(mymail_get_autoresponder_campaigns());
		$elements[] = '</ul><br><ul>';



		if($campaigns = count(mymail_get_campaigns()))
			$elements[] = '<a class="mymail-campaigns" href="edit.php?post_type=newsletter">'.number_format_i18n($campaigns-$autoresponder).' '._n( 'Campaign', 'Campaigns', $campaigns-$autoresponder, 'mymail').'</a>';
		if($autoresponder)
			$elements[] = '<a class="mymail-campaigns" href="edit.php?post_status=autoresponder&post_type=newsletter">'.number_format_i18n($autoresponder).' '._n( 'Autoresponder', 'Autoresponders' ,$autoresponder, 'mymail').'</a>';

		if($subscribers = mymail('subscribers')->get_totals(1))
			$elements[] = '<a class="mymail-subscribers" href="edit.php?post_type=newsletter&page=mymail_subscribers">'.number_format_i18n($subscribers).' '._n( 'Subscriber', 'Subscribers' ,$subscribers, 'mymail').'</a>';

		return $elements;
	}


	public function script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script('easy-pie-chart', MYMAIL_URI . 'assets/js/libs/easy-pie-chart'.$suffix.'.js', array('jquery'), MYMAIL_VERSION);
		wp_enqueue_style('easy-pie-chart', MYMAIL_URI . 'assets/css/libs/easy-pie-chart'.$suffix.'.css', array(), MYMAIL_VERSION);
		wp_enqueue_script('mymail-chartjs', MYMAIL_URI . 'assets/js/libs/chart'.$suffix.'.js', array('easy-pie-chart'), MYMAIL_VERSION);
		wp_enqueue_script('mymail-dashboard-script', MYMAIL_URI . 'assets/js/dashboard-script'.$suffix.'.js', array('easy-pie-chart'), MYMAIL_VERSION);
		wp_enqueue_style('mymail-dashboard-style', MYMAIL_URI . 'assets/css/dashboard-style'.$suffix.'.css', array(), MYMAIL_VERSION);

	}


}
