<?php if (!defined('ABSPATH')) die('not allowed');

if( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class MyMail_Lists_Table extends WP_List_Table {


	public function __construct() {

		parent::__construct( array(
			'singular'  => __( 'List', 'mymail' ),	 //singular name of the listed records
			'plural'	=> __( 'Lists', 'mymail' ),   //plural name of the listed records
			'ajax'	  => false		//does this table support ajax?
		));

		add_action( 'admin_footer', array( &$this, 'script' ) );

	}

	public function get_views() {


		$counts = mymail('lists')->get_list_count();
		$link = 'edit.php?post_type=newsletter&page=mymail_lists';

		$views = array('view-all' => '<a href="'.$link.'">'.__('All', 'mymail').' <span class="count">('.number_format_i18n($counts).')</span></a>');

		return $views;
	}

	public function no_items() {
		echo __( 'No list found', 'mymail').'.';
		if(current_user_can( 'mymail_add_lists' ))
			echo ' <a href="edit.php?post_type=newsletter&page=mymail_lists&new">'.__('add New', 'mymail').'</a>';
	}

	public function search_box($text, $input_id) {

		if(!count($this->items) && !isset($_GET['s'])) return;

?>
	<form id="searchform" action method="get">
	<?php if(isset($_GET['post_type'])) : ?><input type="hidden" name="post_type" value="<?php echo esc_attr($_GET['post_type']) ?>"><?php endif; ?>
	<?php if(isset($_GET['page'])) : ?><input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']) ?>"><?php endif; ?>
	<?php if(isset($_GET['paged'])) : ?><input type="hidden" name="_paged" value="<?php echo esc_attr($_GET['paged']) ?>"><?php endif; ?>
	<p class="search-box">
		<label class="screen-reader-text" for="sa-search-input"><?php echo $text; ?></label>
		<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php if(isset($_GET['s'])) echo esc_attr($_GET['s']) ?>">
		<input type="submit" name="" id="search-submit" class="button" value="<?php echo esc_attr($text); ?>">
	</p>
	</form>
<?php
	}

	public function filter_box() {


	}

	public function script() {
	}

	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'name':
				return (($item->parent_id) ? '&nbsp;┗ ' : '').'<a class="name" href="edit.php?post_type=newsletter&page=mymail_lists&ID='.$item->ID.'">'.$item->{ $column_name }.'</a>';

			case 'description':
				return $item->{ $column_name };

			case 'updated':
			case 'added':
				$timestring = date_i18n(get_option('date_format').' '.get_option('time_format'), $item->{ $column_name }+mymail('helper')->gmt_offset(true));
				return $timestring;

			case 'subscribers':
				return '<a href="'.add_query_arg(array('lists' => array($item->ID)), 'edit.php?post_type=newsletter&page=mymail_subscribers').'">'.number_format_i18n(mymail('lists')->get_member_count($item->ID, 1)).'</a>';


			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}


	public function get_sortable_columns() {
		$sortable_columns = array(
			'name'  => array('name', false),
			'description'  => array('description', false),
			'subscribers'  => array('subscribers', false),
			'added'  => array('updated', false),

		);
		return $sortable_columns;
	}


	public function get_columns() {
		return mymail('lists')->get_columns();
	}


	public function get_bulk_actions() {
		$actions = array(
			'delete'	=> __('Delete', 'mymail'),
			'delete_subscribers'	=> __('Delete with subscribers', 'mymail'),
			'subscribe'	=> __('Subscribe subscribers', 'mymail'),
			'unsubscribe'	=> __('Unsubscribe subscribers', 'mymail'),
			'merge'	=> __('Merge selected Lists', 'mymail'),
			'send_campaign'	=> __('Send new Campaign', 'mymail'),
		);
		return $actions;
	}


	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="lists[]" value="%s" />', $item->ID
		);
	}


	public function view_switcher($current_mode){
		return '';
	}

	public function prepare_items($domain = NULL, $post_id = NULL) {

		global $wpdb;
		$screen = get_current_screen();
		$columns = $this->get_columns();
		$hidden = get_hidden_columns($screen);
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		/* -- Preparing your query -- */

		$sql = "SELECT CASE WHEN a.parent_id = 0 THEN a.ID*10 ELSE a.parent_id*10+1 END AS _sort, a.*";

		$sql .= " FROM {$wpdb->prefix}mymail_lists AS a";

		// $sql .= " LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON a.ID = ab.list_id";

		// $sql .= " LEFT JOIN {$wpdb->prefix}mymail_lists AS c ON a.ID = c.parent_id";

		$sql .= ' WHERE 1=1';

		if(isset($_GET['s'])){
			$search = esc_sql($_GET['s']);
			$search = explode(' ', $search);
		}

		$sql .= " GROUP BY a.ID";

		$orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'name';
		$order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'ASC';

		if(!empty($orderby) & !empty($order)){ $sql.=' ORDER BY _sort ASC, '.$orderby.' '.$order; }

		$totalitems = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mymail_lists");

		//How many to display per page?
		$perpage = 50;
		//Which page is this?

		$paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		//How many pages do we have in total?
		$totalpages = ceil($totalitems/$perpage);
		//adjust the query to take pagination into account
		if(!empty($paged) && !empty($perpage)){
			$offset=($paged-1)*$perpage;
		}

		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );

		if(isset($offset)){
			$sql .= " LIMIT $offset, $perpage";
		}

		if(isset($allitems)){
			$this->items = isset($offset) && isset($perpage) ? array_slice($allitems, (int)$offset, (int)$perpage): $allitems;
		}else{
			$this->items = $wpdb->get_results($sql);
		}

	}




}
