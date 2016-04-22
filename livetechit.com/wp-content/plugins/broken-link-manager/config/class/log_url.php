<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class wblm_List_Table extends WP_List_Table {
	var $url_data = array();        
        
    function __construct(){
	    global $status, $page, $wpdb;
	        parent::__construct( array(
	            'singular'  => __( 'url', 'wblm' ),     //singular name of the listed records
	            'plural'    => __( 'urls', 'wblm' ),   //plural name of the listed records
	            'ajax'      => false        //does this table support ajax?
	    ) );
	    add_action( 'admin_head', array( &$this, 'admin_header' ) );            
    }

	function admin_header() {
	    $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
	    if( 'wblm-log' != $page )
	    return;
	    echo '<style type="text/css">';
	    echo '.wp-list-table .column-id { width: 30px; }';
	    echo '.wp-list-table .column-date { width: 95px; }';
	    echo '.wp-list-table .column-referer { width: auto; }';
	    echo '.wp-list-table .column-user { width: auto;}';
	    echo '.wp-list-table .column-ip { width: 130px;}';
	    echo '.wp-list-table .column-http_statu { width: 90px;}';
	    echo '</style>';
	}

	function no_items() {
		_e( 'No urls found, dude.' );
	}

function column_default( $item, $column_name ) {
	switch( $column_name ) {
		case 'id':
	    case 'date':
	    case 'referer':
	    case 'useragent':
		case 'ip':
		case 'http_statu':
			return $item[ $column_name ];
		default:
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	}
}

	function column_date($item){
		return date('d.m.Y H:i:s', strtotime($item['date']));
	}

	function column_referer($item){
		if ($item['referer'] != 'Direct'){
			return '<a href="'.$item['referer'].'" target="_blank">'.$item['referer'].'</a>';
		}else{
			return $item['referer'];
		}
	}

	function column_ip($item){
		return '<a href="http://whois.arin.net/rest/ip/'.$item['ip'].'" target="_blank">'.$item['ip'].'</a>';
	}

	function column_http_statu($item){
		if ($item['http_statu'] == 301){
			$http_statu = '<span class="text-success"> 301 </span>';
		}elseif($item['http_statu'] == 404){
			$http_statu = '<span class="text-danger"> 404 </span>';
		}else{
			$http_statu = '<span class="text-warning"> Unknown </span>';
		}
		return $http_statu;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
		  	'id'  => array('id',false),
		    'date'  => array('date',false),
		    'referer' => array('referer',false),
		    'useragent'   => array('useragent',false),
		    'ip'   => array('ip',false),
		    'http_statu'   => array('http_statu',false),
		);
		return $sortable_columns;
	}

	function get_columns(){
		$columns = array(
			'cb'			=> '<input type="checkbox" />',
			'date'			=> __( 'Date', 'wblm' ),
			'referer'		=> __( 'Referer', 'wblm' ),
			'useragent'		=> __( 'User Agent', 'wblm' ),
			'ip'			=> __( 'IP', 'wblm' ),
			'http_statu'	=> __( 'Statu', 'wblm' ),
		);
		return $columns;
	}
	
	function usort_reorder( $a, $b ) {
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_sql_orderby($_GET['orderby']) : 'date';
		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? sanitize_text_field($_GET['order']) : 'asc';
		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	function column_referer_url($item){
		$actions = array(
			'edit'		=> sprintf('<a href="'. admin_url("admin.php?page=%s&url=%s") .'">Edit</a>','wblm-edit-url',$item['id']),  
			'delete'    => sprintf('<a href="'. admin_url("admin.php?page=%s&delURL=%s&url=%s") .'" onClick="return confirm(\'Are you sure you want to delete?\');">Delete</a>',$_REQUEST['page'],'on',$item['id']),
			'log'		=> sprintf('<a href="'. admin_url("admin.php?page=%s&url=%s") .'">Log</a>','wblm-log',$item['id']), 
			'Waybackmachine'    => sprintf('<a href="http://web.archive.org/web/*/%s" target="_blank">Waybackmachine</a>',$item['old_url']), 
		);
		return sprintf('%1$s %2$s', $item['referer'], $this->row_actions($actions) );
	}

	function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Delete'
		);
		return $actions;
	}

	function column_cb($item) {
		return sprintf('<input type="checkbox" name="log[]" value="%s" />', $item['id']);    
	}


	function process_bulk_action() {
		$url = null;
		global $wpdb;
		        
		if( 'delete'===$this->current_action() ) {
			foreach($_POST['log'] as $log) {
				if(is_numeric($log) && $log!=null){
					$wpdb->delete(TABLE_WBLM_LOG, array( 'id' => $log ), array( '%d' ));
				}
			}
		}
	}

	function prepare_items($url='',$search=''){
		global $wpdb;
		 
		$table_name = $wpdb->prefix . 'wblm_log';
		$per_page = 100;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		 
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action(); 
		
		$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
		$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? sanitize_sql_orderby($_REQUEST['orderby']) : 'id';
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? sanitize_text_field($_REQUEST['order']) : 'desc';
		
		
		if($search){    
			$search = trim($search);
			$searchFilterUrl = "and (`referer` LIKE '%%$search%%' OR `useragent` LIKE '%%$search%%' OR `ip` LIKE '%%$search%%')";
			$searchFilterAll = "where `referer` LIKE '%%$search%%' OR `useragent` LIKE '%%$search%%' OR `ip` LIKE '%%$search%%'";
		}else{
			$searchFilterUrl = null;
			$searchFilterAll = null;
		}
		
		if($url){
			$url = trim($url);
			$search = sanitize_text_field($search);
			$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name where `url` = '$url' $searchFilterUrl ");
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `url` = %d and (`referer` LIKE '%%%s%%' OR `useragent` LIKE '%%%s%%' OR `ip` LIKE '%%%s%%') ORDER BY $orderby $order LIMIT %d OFFSET %d", $url, $search, $search, $search, $per_page, $paged), ARRAY_A);
		}else{
			$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name $searchFilterAll");
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name where `referer` LIKE '%%%s%%' OR `useragent` LIKE '%%%s%%' OR `ip` LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $search, $search, $search, $per_page, $paged), ARRAY_A);
		}
		
		//print_r($this->items);
		
		 
		$this->set_pagination_args(array(
			'total_items'	=> $total_items,
			'per_page'		=> $per_page,
			'total_pages'	=> ceil($total_items / $per_page),
		));
	}
}//Class
?>