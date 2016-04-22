<?php if (!defined('ABSPATH')) die('not allowed');

if( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class MyMail_Forms_Table extends WP_List_Table {


	public function __construct() {

		parent::__construct( array(
			'singular'  => __( 'Form', 'mymail' ),	 //singular name of the listed records
			'plural'	=> __( 'Forms', 'mymail' ),   //plural name of the listed records
			'ajax'	  => false		//does this table support ajax?
		));

		add_action( 'admin_footer', array( &$this, 'script' ) );

	}

	public function get_views() {


		$counts = mymail('forms')->get_count();
		$link = 'edit.php?post_type=newsletter&page=mymail_forms';

		$views = array('view-all' => '<a href="'.$link.'">'.__('All', 'mymail').' <span class="count">('.number_format_i18n($counts).')</span></a>');

		return $views;
	}

	public function script() {
	}

	public function no_items() {

		_e( 'No forms found', 'mymail');

		if(current_user_can( 'mymail_add_forms' ))
			echo ' <a href="edit.php?post_type=newsletter&page=mymail_forms&new">'.__('add New', 'mymail').'</a>';
	}

	public function search_box($text, $input_id) {

		if(!count($this->items) && !isset($_GET['s'])) return;

?>
	<form id="searchform" action method="get">
	<?php if(isset($_GET['post_type'])) : ?><input type="hidden" name="post_type" value="<?php echo esc_attr( $_GET['post_type']) ?>"><?php endif; ?>
	<?php if(isset($_GET['page'])) : ?><input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page']) ?>"><?php endif; ?>
	<?php if(isset($_GET['paged'])) : ?><input type="hidden" name="_paged" value="<?php echo esc_attr( $_GET['paged']) ?>"><?php endif; ?>
	<p class="search-box">
		<label class="screen-reader-text" for="sa-search-input"><?php echo esc_attr($text); ?></label>
		<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php if(isset($_GET['s'])) echo esc_attr( $_GET['s'])?>">
		<input type="submit" name="" id="search-submit" class="button" value="<?php echo esc_attr($text); ?>">
	</p>
	</form>
<?php
	}

	public function get_columns() {
		return mymail('forms')->get_columns();
	}

	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'name':

				echo '<a class="name" href="'.admin_url('edit.php?post_type=newsletter&page=mymail_forms&ID='.$item->ID).'" title="'.$item->name.'">'.($item->name ? $item->name : '<span class="grey">'.__('undefined', 'mymail').'</span>').'</a> <strong>(#'.$item->ID.')</strong>';

				if(mymail_option('profile_form', 0) == $item->ID) echo '<span class="dashicons-before dashicons-admin-users" title="'.__('This form is used for user profile updates', 'mymail').'"></span>';

				echo '<div class="row-actions">';
				$actions = array();

				$actions['fields'] = '<a class="" href="?post_type=newsletter&page=mymail_forms&ID='.$item->ID.'&tab=structure" title="' . __('change structure', 'mymail') . '">' . __('Fields', 'mymail') . '</a>';
				$actions['design'] = '<a class="" href="?post_type=newsletter&page=mymail_forms&ID='.$item->ID.'&tab=design" title="' . __('change design', 'mymail') . '">' . __('Design', 'mymail') . '</a>';
				$actions['settings'] = '<a class="" href="?post_type=newsletter&page=mymail_forms&ID='.$item->ID.'&tab=settings" title="' . __('change settings', 'mymail') . '">' . __('Settings', 'mymail') . '</a>';
				$actions['use'] = '<a class="" href="?post_type=newsletter&page=mymail_forms&ID='.$item->ID.'&tab=use" title="' . __('use your form', 'mymail') . '">' . __('Use it!', 'mymail') . '</a>';

				echo implode(' | ', $actions);
				echo '</div>';

				break;

			case 'shortcode':

				return '<input type="text" class="tiny code" value="[newsletter_signup_form id='.$item->ID.']" onclick="jQuery(this).select()" readonly>';

				break;


			case 'fields':

				$fields = mymail('forms')->get_fields($item->ID );
				$fieldarray = array();
				foreach ($fields as $field) {
					$fieldarray[] = '<span class="tiny">'.$field->name.'</span>';
				}
				echo implode(', ', $fieldarray);

				break;


			case 'lists':

				$lists = mymail('forms')->get_lists($item->ID);
				$listarray = array();
				foreach ($lists as $list) {
					$listarray[] = '<a href="edit.php?post_type=newsletter&page=mymail_lists&ID='.$list->ID.'">'.$list->name.'</a>';
				}

				echo implode(', ', $listarray);

				break;

			case 'occurrence':

				if($occurrence = mymail('forms')->get_occurrence($item->ID)){
					if(!empty($occurrence['posts'])){
						$occurrencs = array();
						foreach ($occurrence['posts'] as $post_id => $title) {
							$occurrencs[] = '<a href="'.get_permalink($post_id).'">'.$title.'</a>';
						}
					}
					if(!empty($occurrencs)) echo implode(', ', $occurrencs).'<br>';
					if(!empty($occurrence['widgets'])){
						$count = count($occurrence['widgets']);
						echo '<a href="widgets.php">'.sprintf(_n( '%d Widget', '%d Widgets', $count, 'mymail'), $count).'</a>';
					}
				}else{
					echo '<em class="tiny">'.__('unknown', 'mymail').'</em>';
				}

				break;

			case 'preview':

				return mymail('forms')->get_subscribe_button($item->ID, array(
					'showcount' => false,
					'label' => __('Preview', 'mymail'),
				));

			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}


	public function get_sortable_columns() {
		$sortable_columns = array(
			'name'  => array('name', false),
		);
		return $sortable_columns;
	}


	public function get_bulk_actions() {
		$actions = array(
			'delete'	=> __('Delete', 'mymail'),
		);

		if(!current_user_can('mymail_delete_forms')) unset($actions['delete']);

		return $actions;
	}

	public function bulk_actions( $which = '' ) {

		parent::bulk_actions( $which );

	}


	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="forms[]" value="%s" />', $item->ID
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

		$extrasql = '';

		$sql = "SELECT a.*";

		$sql .= " FROM {$wpdb->prefix}mymail_forms as a";

		$extrasql .= ' WHERE 1';

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
				$termsql .= " (a.name $like '%".$term."%') $operator ";
				$termsql .= " (a.description $like '%".$term."%') $operator ";
				$termsql .= " $end )";

				$terms[] = $termsql;

			}

			$extrasql .= implode(' AND ', $terms) .')';

		}

		$sql .= $extrasql;

		$orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'ID';
		$order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'ASC';

		if(!empty($orderby) & !empty($order))
			$sql .= ' ORDER BY '.$orderby.' '.$order;

		//How many to display per page?
		$perpage = (int) get_user_option( 'mymail_forms_per_page' );
		if(!$perpage) $perpage = 10;
		//Which page is this?

		$paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }

		if(!isset($_GET['s'])){
			$totalitems = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mymail_forms AS a".$extrasql);
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

	}




}
