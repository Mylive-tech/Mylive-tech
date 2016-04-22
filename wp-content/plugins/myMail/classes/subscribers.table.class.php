<?php if (!defined('ABSPATH')) die('not allowed');

if( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class MyMail_Subscribers_Table extends WP_List_Table {


	public function __construct() {

		parent::__construct( array(
			'singular'  => __( 'Subscriber', 'mymail' ),	 //singular name of the listed records
			'plural'	=> __( 'Subscribers', 'mymail' ),   //plural name of the listed records
			'ajax'	  => false		//does this table support ajax?
		));

		add_action( 'admin_footer', array( &$this, 'script' ) );
		add_filter('manage_newsletter_page_mymail_subscribers_columns', array(  &$this, 'get_columns'));

	}

	public function get_views() {


		$counts = mymail('subscribers')->get_count_by_status();
		$statuses = mymail('subscribers')->get_status();
		$statuses_nice = mymail('subscribers')->get_status(NULL, true);
		$link = 'edit.php?post_type=newsletter&page=mymail_subscribers';

		$views = array('view-all' => '<a href="'.$link.'">'.__('All', 'mymail').' <span class="count">('.number_format_i18n(array_sum($counts)).')</span></a>');

		foreach($counts as $id => $count){
			$views['view-'.$statuses[$id]] = '<a href="'.add_query_arg(array('status' => $id), $link).'">'.$statuses_nice[$id].' <span class="count">('.number_format_i18n($count).')</span></a>';
		}

		return $views;
	}

	public function script() {
	}

	public function no_items() {

		$status = isset($_GET['status']) ? intval($_GET['status']) : NULL;

		switch($status){
			case '0': //pending
				_e('No pending subscribers found', 'mymail');
			break;
			case '2': //unsubscribed
				_e('No unsubscribed subscribers found', 'mymail');
			break;
			case '3': //hardbounced
				_e('No hardbounced subscribers found', 'mymail');
			break;
			case '4': //error
				_e('No subscriber with delivery errors found', 'mymail');
			break;
			default:
				_e( 'No subscribers found', 'mymail');

		}

		if(current_user_can( 'mymail_add_subscribers' ))
			echo ' <a href="edit.php?post_type=newsletter&page=mymail_subscribers&new">'.__('add New', 'mymail').'</a>';
	}

	public function search_box($text, $input_id) {

		if(!count($this->items) && !isset($_GET['s'])) return;

?>
	<form id="searchform" action method="get">
	<?php if(isset($_GET['post_type'])) : ?><input type="hidden" name="post_type" value="<?php echo esc_attr($_GET['post_type']) ?>"><?php endif; ?>
	<?php if(isset($_GET['page'])) : ?><input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']) ?>"><?php endif; ?>
	<?php if(isset($_GET['paged'])) : ?><input type="hidden" name="_paged" value="<?php echo esc_attr($_GET['paged']) ?>"><?php endif; ?>
	<?php if(isset($_GET['status'])) : ?><input type="hidden" name="status" value="<?php echo esc_attr($_GET['status']) ?>"><?php endif; ?>
	<?php if(isset($_GET['lists'])) :
		foreach(array_filter($_GET['lists'], 'is_numeric') as $list_id) {?>
		<input type="hidden" name="lists[]" value="<?php echo $list_id ?>">
	<?php } endif; ?>
	<p class="search-box">
		<label class="screen-reader-text" for="sa-search-input"><?php echo $text; ?></label>
		<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php if(isset($_GET['s'])) echo esc_attr($_GET['s'])?>">
		<input type="submit" name="" id="search-submit" class="button" value="<?php echo esc_attr($text); ?>">
	</p>
	</form>
<?php
	}

	public function get_columns() {
		return mymail('subscribers')->get_columns();
	}

	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'avatar':
				return '<a href="'.admin_url('edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$item->ID).'"><span class="mymail-avatar-40'.($item->wp_id ? ' wp-user' : '').'" style="background-image:url('.mymail('subscribers')->get_gravatar_uri($item->email, 80).')"></span></a>';

			case 'name':

				if($item->fullname){
					$html = '<a class="name" href="'.admin_url('edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$item->ID).'">'.$item->fullname.'</a><br><a class="email" href="'.admin_url('edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$item->ID).'" title="'.$item->{'email'}.'">'.$item->{'email'}.'</a>';
				}else{
					$html = '<a class="name" href="'.admin_url('edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$item->ID).'" title="'.$item->{'email'}.'">'.$item->{'email'}.'</a>';
				}

				$stars = (round($item->rating/10, 2)*50);
				$full = floor($stars);
				$half = round($stars-$full);
				$empty = 5-$full-$half;
				return $html.'<div class="userrating" title="'.($item->rating*100).'%">'.str_repeat('<span class="mymail-icon icon-mm-star"></span>', $full)
						.str_repeat('<span class="mymail-icon icon-mm-star-half"></span>', $half)
						.str_repeat('<span class="mymail-icon icon-mm-star-empty"></span>', $empty).'</div>';

			case 'lists':

				$lists = mymail('subscribers')->get_lists($item->ID);

				$elements = array();

				foreach($lists as $i => $list){
					$elements[] = '<a href="edit.php?post_type=newsletter&page=mymail_lists&ID='.$list->ID.'" title="'.$list->description.'">'.$list->name.'</a>';
				}
				return implode(', ', $elements);

			case 'emails':

				return number_format_i18n(mymail('subscribers')->get_sent($item->ID, true));

			case 'status':

				return '<span class="nowrap tiny">'.mymail('subscribers')->get_status($item->{ $column_name }, true).'</span>';

			case 'signup':
				$timestring = (!$item->{ $column_name }) ? __('unknown', 'mymail') : date_i18n(get_option('date_format').' '.get_option('time_format'), $item->{ $column_name }+mymail('helper')->gmt_offset(true));
				return $timestring;


			default:
				$custom_fields = mymail()->get_custom_fields();
				if(in_array($column_name, array_keys($custom_fields))){
					switch($custom_fields[$column_name]['type']){
						case 'checkbox':
							return $item->{ '_'.$column_name } ? '&#10004;' : '&#10005;';
							break;
						case 'date':
							return $item->{ '_'.$column_name } ? date_i18n(get_option('date_format'), strtotime($item->{ '_'.$column_name })): '';
							break;
						default:
							return $item->{ '_'.$column_name };
					}
				}
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}


	public function get_sortable_columns() {
		$sortable_columns = array(
			'name'  => array('name', false),
			'status'  => array('status', false),
			'signup'  => array('signup', false),

		);
		$custom_fields = mymail()->get_custom_fields();
		foreach($custom_fields as $key => $field){
			$sortable_columns[$key] = array($key, false);
		}
		return $sortable_columns;
	}


	public function get_bulk_actions() {
		$actions = array(
			'delete'	=> __('Delete', 'mymail'),
			'send_campaign'	=> __('Send new Campaign', 'mymail'),
			'confirmation'	=> __('Resend Confirmation', 'mymail'),
		);

		if(!current_user_can('mymail_delete_subscribers')) unset($actions['delete']);

		return $actions;
	}

	public function bulk_actions( $which = '' ) {

		ob_start();
		parent::bulk_actions( $which );
		$actions = ob_get_contents();
		ob_end_clean();

		$status =	'<option value="pending">└ '.__('pending', 'mymail').'</option>';
		$status .=	'<option value="subscribe">└ '.__('subscribed', 'mymail').'</option>';
		$status .=	'<option value="unsubscribe">└ '.__('unsubscribed', 'mymail').'</option>';

		$actions = str_replace('</select>', '<optgroup label="'.__('change status', 'mymail').'">'.$status.'</optgroup></select>', $actions);

		$lists = mymail('lists')->get();

		if(empty($lists)){
			echo $actions;
			return;
		}

		$add = '';
		$remove = '';
		foreach($lists as $list){
			$add .= '<option value="add_list_'.$list->ID.'">'.($list->parent_id ? '&nbsp;' : '').'└ '.$list->name.'</option>';
			$remove .= '<option value="remove_list_'.$list->ID.'">'.($list->parent_id ? '&nbsp;' : '').'└ '.$list->name.'</option>';
		}

		echo str_replace('</select>', '<optgroup label="'.__('add to list', 'mymail').'">'.$add.'</optgroup><optgroup label="'.__('remove from list', 'mymail').'">'.$remove.'</optgroup></select>', $actions);
	}


	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="subscribers[]" value="%s" />', $item->ID
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

		$custom_field_names = mymail()->get_custom_fields(true);

		$lists = isset($_GET['lists']) ? array_filter($_GET['lists'], 'is_numeric') : array();

		$extrasql = '';

		$sql = "SELECT a.*, firstname.meta_value AS firstname, lastname.meta_value AS lastname, TRIM(CONCAT(IFNULL(firstname.meta_value, ''), ' ', IFNULL(lastname.meta_value, ''))) AS fullname";
		foreach($custom_field_names as $i => $name){
			$sql .= ", meta_$i.meta_value AS '_$name'";
		}

		if(!empty($lists)) $sql .= ", lists.list_id AS list_id";

		$sql .= " FROM {$wpdb->prefix}mymail_subscribers as a";

		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS firstname ON firstname.subscriber_id = a.ID AND firstname.meta_key = 'firstname'";
		$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS lastname ON lastname.subscriber_id = a.ID AND lastname.meta_key = 'lastname'";

		foreach($custom_field_names as $i => $name){
			$sql .= " LEFT JOIN {$wpdb->prefix}mymail_subscriber_fields AS meta_$i ON meta_$i.subscriber_id = a.ID AND meta_$i.meta_key = '$name'";
		}

		if(!empty($lists)) $extrasql .= " LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS lists ON a.ID = lists.subscriber_id";
		//$sql .= " LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON a.ID = ab.subscriber_id LEFT JOIN {$wpdb->prefix}mymail_lists as c on c.id = ab.list_id";

		$extrasql .= ' WHERE 1';

		if(isset($_GET['status'])){
			$extrasql .= " AND a.status = ".intval($_GET['status']);
		}
		if(!empty($lists)) $extrasql .= " AND lists.list_id IN (".implode(',', $lists).")";

		if(isset($_GET['s'])){
			$search = trim(addcslashes(esc_sql($_GET['s']), '%_'));
			$search = explode(' ', $search);

			$extrasql .= " AND (";
			$terms = array();
			foreach($search as $term){

				if(substr($term, 0,1) == '-'){
					$term = substr($term,1);
					$operator = 'AND';
					$like = 'NOT LIKE';
					$end = '(1=1)';
				}else{
					$operator = 'OR';
					$like = 'LIKE';
					$end = '(1=0)';
				}

				$termsql = " ( ";
				$termsql .= " (a.email $like '%".$term."%') $operator ";
				$termsql .= " (a.hash $like '".$term."') $operator ";
				$termsql .= " (firstname.meta_value $like '%".$term."%') $operator ";
				$termsql .= " (lastname.meta_value $like '%".$term."%') $operator ";
				foreach($custom_field_names as $i => $name){
					$termsql .= " (meta_$i.meta_value $like '%".$term."%') $operator ";
				}
				$termsql .= " $end )";

				$terms[] = $termsql;

			}

			$extrasql .= implode(' AND ', $terms) .')';

		}


		$sql .= $extrasql;

		$orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'ID';
		$order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'DESC';
		switch($orderby){
			case 'name':
			case 'lastname':
				$orderby = 'lastname.meta_value '.$order.', firstname.meta_value';
				break;
			case 'firstname':
				$orderby = 'firstname.meta_value '.$order.', lastname.meta_value';
				break;
			default:
				$orderby = (false !== ($found = array_search($orderby, $custom_field_names))) ? 'meta_'.$found.'.meta_value' : 'a.'.$orderby;
		}

		if(!empty($orderby) & !empty($order))
			$sql .= ' ORDER BY '.$orderby.' '.$order.', ID '.$order;

		//How many to display per page?
		$perpage = (int) get_user_option( 'mymail_subscribers_per_page' );
		if(!$perpage) $perpage = 50;
		//Which page is this?

		$paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }

		if(!isset($_GET['s'])){
			$totalitems = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mymail_subscribers AS a".$extrasql);
		}else{
			$allitems = $wpdb->get_results($sql);
			$totalitems = count($allitems);
		}

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

		//cache the lists for the current subscribers
		if($current_ids = wp_list_pluck( $this->items, 'ID' )){

			//mymail('actions')->get_by_subscriber($current_ids);

			$sql = "SELECT ab.subscriber_id, a.* FROM {$wpdb->prefix}mymail_lists AS a LEFT JOIN {$wpdb->prefix}mymail_lists_subscribers AS ab ON a.ID = ab.list_id WHERE ab.subscriber_id IN (".implode(',', $current_ids).")";

			$cache = array();
			$lists = $wpdb->get_results($sql);
			foreach($lists as $list){
				if(!isset($cache[$list->subscriber_id])) $cache[$list->subscriber_id] = array();
				$cache[$list->subscriber_id][] = $list;
			}

			mymail_cache_set( 'subscribers_lists', $cache );
		}

	}




}
