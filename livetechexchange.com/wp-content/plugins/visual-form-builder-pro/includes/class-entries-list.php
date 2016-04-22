<?php
/**
 * Class that builds our Entries table
 *
 * @since 1.2
 */
class VisualFormBuilder_Pro_Entries_List extends WP_List_Table {
	/**
	 * field_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $field_table_name;

	/**
	 * form_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $form_table_name;

	/**
	 * entries_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $entries_table_name;

	/**
	 * errors
	 *
	 * @var mixed
	 * @access public
	 */
	public $errors;

	function __construct(){
		global $status, $page, $wpdb;

		// CSV delimiter
		$this->delimiter = apply_filters( 'vfb_csv_delimiter', ',' );

		// Setup global database table names
		$this->field_table_name   = $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name    = $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name = $wpdb->prefix . 'vfb_pro_entries';

		// Set parent defaults
		parent::__construct( array(
			'singular'  => 'entry',
			'plural'    => 'entries',
			'ajax'      => false
		) );

		// Handle our bulk actions
		$this->process_bulk_action();
	}

	/**
	 * Display column names. We'll handle the Form column separately.
	 *
	 * @since 1.2
	 * @returns $item string Column name
	 */
	function column_default( $item, $column_name ){
		switch ( $column_name ) {
			case 'id':
			case 'subject':
			case 'sender_name':
			case 'sender_email':
			case 'emails_to':
			case 'date':
			case 'ip_address':
			case 'entry_id' :
				return $item[ $column_name ];
		}
	}

	/**
	 * Builds the on:hover links for the Form column
	 *
	 * @since 1.2
	 */
	function column_form( $item ){

		$actions = array();

		// Default Entries view
		if ( !$this->get_entry_status() || 'all' == $this->get_entry_status() ) :

			if ( current_user_can( 'vfb_view_entries' ) )
				$actions['view'] = sprintf( '<a href="?page=%s&action=%s&entry=%s" id="%3$s" class="view-entry">%s</a>', $_REQUEST['page'], 'view', $item['entry_id'], __( 'View', 'visual-form-builder-pro' ) );

			if ( current_user_can( 'vfb_edit_entries' ) )
				$actions['edit'] = sprintf( '<a href="?page=%s&action=%s&entry=%s" id="%3$s" class="view-entry">%s</a>', $_REQUEST['page'], 'edit', $item['entry_id'], __( 'Edit', 'visual-form-builder-pro' ) );

			$actions['spam'] = sprintf( '<a href="?page=%s&action=%s&entry=%s">%s</a>', $_REQUEST['page'], 'spam', $item['entry_id'], __( 'Spam', 'visual-form-builder-pro' ) );
		endif;

		// SPAM'd Entries view
		if ( $this->get_entry_status() && 'spam' == $this->get_entry_status() ) :
			if ( current_user_can( 'vfb_view_entries' ) )
				$actions['view'] = sprintf( '<a href="?page=%s&action=%s&entry=%s" id="%3$s" class="view-entry">%s</a>', $_REQUEST['page'], 'view', $item['entry_id'], __( 'View', 'visual-form-builder-pro' ) );

			$actions['unspam'] = sprintf( '<a href="?page=%s&action=%s&entry=%s">%s</a>', $_REQUEST['page'], 'unspam', $item['entry_id'], __( 'Not Spam', 'visual-form-builder-pro' ) );

			if ( current_user_can( 'vfb_delete_entries' ) )
				$actions['delete'] = sprintf( '<a href="?page=%s&action=%s&entry=%s">%s</a>', $_REQUEST['page'], 'delete', $item['entry_id'], __( 'Delete Permanently', 'visual-form-builder-pro' ) );
		endif;

		// Trashed Entries view
		if ( current_user_can( 'vfb_delete_entries' ) ) :
			if ( !$this->get_entry_status() || 'all' == $this->get_entry_status() )
				$actions['trash'] = sprintf( '<a href="?page=%s&action=%s&entry=%s">%s</a>', $_REQUEST['page'], 'trash', $item['entry_id'], __( 'Trash', 'visual-form-builder-pro' ) );
			elseif ( $this->get_entry_status() && 'trash' == $this->get_entry_status() ) {
				$actions['restore'] = sprintf( '<a href="?page=%s&action=%s&entry=%s">%s</a>', $_REQUEST['page'], 'restore', $item['entry_id'], __( 'Restore', 'visual-form-builder-pro' ) );
				$actions['delete'] = sprintf( '<a href="?page=%s&action=%s&entry=%s">%s</a>', $_REQUEST['page'], 'delete', $item['entry_id'], __( 'Delete Permanently', 'visual-form-builder-pro' ) );
			}
		endif;

		return sprintf( '%1$s %2$s', $item['form'], $this->row_actions( $actions ) );
	}

	/**
	 * Used for checkboxes and bulk editing
	 *
	 * @since 1.2
	 */
	function column_cb( $item ){
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['entry_id'] );
	}

	/**
	 * Builds the actual columns
	 *
	 * @since 1.2
	 */
	function get_columns(){
		$columns = array(
			'cb' 			=> '<input type="checkbox" />', //Render a checkbox instead of text
			'form' 			=> __( 'Form' , 'visual-form-builder-pro'),
			'subject' 		=> __( 'Email Subject' , 'visual-form-builder-pro'),
			'sender_name' 	=> __( 'Sender Name' , 'visual-form-builder-pro'),
			'sender_email' 	=> __( 'Sender Email' , 'visual-form-builder-pro'),
			'emails_to' 	=> __( 'Emailed To' , 'visual-form-builder-pro'),
			'ip_address' 	=> __( 'IP Address' , 'visual-form-builder-pro'),
			'date' 			=> __( 'Date Submitted' , 'visual-form-builder-pro'),
			'entry_id' 		=> __( 'Entry ID' , 'visual-form-builder-pro')
		);

		return $columns;
	}

	/**
	 * A custom function to get the entries and sort them
	 *
	 * @since 1.2
	 * @returns array() $cols SQL results
	 */
	function get_entries( $orderby = 'date', $order = 'ASC', $per_page, $offset = 0, $search = '' ){
		global $wpdb;

		// Set OFFSET for pagination
		$offset = ( $offset > 0 ) ? "OFFSET $offset" : '';

 		switch ( $orderby ) {
			case 'date':
				$order_col = 'date_submitted';
			break;

			case 'form':
				$order_col = 'form_title';
			break;

			case 'subject':
			case 'ip_address':
			case 'sender_name':
			case 'sender_email':
				$order_col = $orderby;
			break;

			case 'entry_id' :
				$order_col = 'entries_id';
			break;
		}

		$where = apply_filters( 'vfb_pre_get_entries', '' );

		// If the form filter dropdown is used
		if ( $this->current_filter_action() )
			$where .= ' AND forms.form_id = ' . $this->current_filter_action();

		// Get the month and year from the dropdown
		$m = isset( $_REQUEST['m'] ) ? (int) $_REQUEST['m'] : 0;

		// If a month/year has been selected, parse out the month/year and build the clause
		if ( $m > 0 ) {
			$year    = substr( $m, 0, 4 );
			$month   = substr( $m, -2 );

			$where .= " AND YEAR(entries.date_submitted) = $year AND MONTH(entries.date_submitted) = $month";
		}

		// Get the month/year from the dropdown
		$today = isset( $_REQUEST['today'] ) ? (int) $_REQUEST['today'] : 0;

		// Parse month/year and build the clause
		if ( $today > 0 )
			$where .= " AND entries.date_submitted >= curdate()";

		// Entries type filter
		$where .= ( $this->get_entry_status() && 'all' !== $this->get_entry_status() ) ? $wpdb->prepare( ' AND entries.entry_approved = %s', $this->get_entry_status() ) : '';

		// Always display approved entries, unless an Entries Type filter is set
		if ( !$this->get_entry_status() || 'all' == $this->get_entry_status() )
			$where .= $wpdb->prepare( ' AND entries.entry_approved = %d', 1 );

		$sql_order = sanitize_sql_orderby( "$order_col $order" );
		$cols = $wpdb->get_results( "SELECT forms.form_title, entries.entries_id, entries.form_id, entries.subject, entries.sender_name, entries.sender_email, entries.emails_to, entries.date_submitted, entries.ip_address FROM $this->form_table_name AS forms INNER JOIN $this->entries_table_name AS entries ON entries.form_id = forms.form_id WHERE 1=1 $where $search ORDER BY $sql_order LIMIT $per_page $offset" );

		return $cols;
	}

	/**
	 * Get the entry status: All, Spam, or Trash
	 *
	 * @since 2.1
	 * @returns string Entry status
	 */
	function get_entry_status() {
		if ( !isset( $_REQUEST['entry_status'] ) )
			return false;

		return esc_html( $_REQUEST['entry_status'] );
	}

	/**
	 * Build the different views for the entries screen
	 *
	 * @since 2.1
	 * @returns array $status_links Status links with counts
	 */
	function get_views() {
		$status_links = array();
		$num_entries = $this->get_entries_count();
		$class = '';
		$link = '?page=vfb-entries';

		$stati = array(
			'all'    => _n_noop( 'All <span class="count">(<span class="pending-count">%s</span>)</span>', 'All <span class="count">(<span class="pending-count">%s</span>)</span>' ),
			'spam'   => _n_noop( 'Spam <span class="count">(<span class="spam-count">%s</span>)</span>', 'Spam <span class="count">(<span class="spam-count">%s</span>)</span>' ),
			'trash'  => _n_noop( 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>', 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>' )
		);

		$total_entries = (int) $num_entries->all;
		$entry_status = isset( $_REQUEST['entry_status'] ) ? $_REQUEST['entry_status'] : 'all';

		foreach ( $stati as $status => $label ) {
			$class = ( $status == $entry_status ) ? ' class="current"' : '';

			if ( !isset( $num_entries->$status ) )
				$num_entries->$status = 10;

			$link = add_query_arg( 'entry_status', $status, $link );

			$status_links[ $status ] = "<li class='$status'><a href='$link'$class>" . sprintf(
				translate_nooped_plural( $label, $num_entries->$status ),
				number_format_i18n( $num_entries->$status )
			) . '</a>';
		}

		return $status_links;
	}

	/**
	 * Get the number of entries for use with entry statuses
	 *
	 * @since 2.1
	 * @returns array $stats Counts of different entry types
	 */
	function get_entries_count() {
		global $wpdb;

		$stats = array();
		$where = apply_filters( 'vfb_pre_get_entries', '' );

		$entries = $wpdb->get_results( "SELECT entries.entry_approved, COUNT( * ) AS num_entries FROM $this->entries_table_name AS entries WHERE 1=1 {$where} GROUP BY entries.entry_approved", ARRAY_A );

		$total = 0;
		$approved = array( '0' => 'moderated', '1' => 'approved', 'spam' => 'spam', 'trash' => 'trash', 'post-trashed' => 'post-trashed');
		foreach ( (array) $entries as $row ) {
			// Don't count trashed toward totals
			if ( 'trash' != $row['entry_approved'] )
				$total += $row['num_entries'];
			if ( isset( $approved[ $row['entry_approved' ] ] ) )
				$stats[ $approved[ $row['entry_approved' ] ] ] = $row['num_entries'];
		}

		$stats['all'] = $total;
		foreach ( $approved as $key ) {
			if ( empty( $stats[ $key ] ) )
				$stats[ $key ] = 0;
		}

		$stats = (object) $stats;

		return $stats;
	}

	/**
	 * Setup which columns are sortable. Default is by Date.
	 *
	 * @since 1.2
	 * @returns array() $sortable_columns Sortable columns
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'id' 			=> array( 'id', false ),
			'form' 			=> array( 'form', false ),
			'subject' 		=> array( 'subject', false ),
			'sender_name' 	=> array( 'sender_name', false ),
			'sender_email' 	=> array( 'sender_email', false ),
			'date' 			=> array( 'date', true ),
			'entry_id'		=> array( 'entry_id', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Define our bulk actions
	 *
	 * @since 1.2
	 * @returns array() $actions Bulk actions
	 */
	function get_bulk_actions() {
		$entry_status = $this->get_entry_status();
		$actions = array();

		// Build the row actions
		if ( current_user_can( 'vfb_delete_entries' ) ) {
			if ( !$this->get_entry_status() || 'all' == $this->get_entry_status() ) {
				$actions['trash'] = __( 'Move to Trash', 'visual-form-builder-pro' );
				$actions['spam'] = __( 'Mark as Spam', 'visual-form-builder-pro' );
			}
			elseif ( $this->get_entry_status() && 'trash' == $this->get_entry_status() ) {
				$actions['restore'] = __( 'Restore', 'visual-form-builder-pro' );
				$actions['delete'] = __( 'Delete Permanently', 'visual-form-builder-pro' );
			}
		}

		if ( $this->get_entry_status() && 'spam' == $this->get_entry_status() ) {
			$actions['unspam'] = __( 'Not Spam', 'visual-form-builder-pro' );

			if ( current_user_can( 'vfb_delete_entries' ) )
				$actions['delete'] = __( 'Delete Permanently', 'visual-form-builder-pro' );
		}

		return apply_filters( 'vfb_entries_bulk_actions', $actions );
	}

	/**
	 * Process ALL actions on the Entries screen, not only Bulk Actions
	 *
	 * @since 1.2
	 */
	function process_bulk_action() {
		global $wpdb;

		$entry_id = '';

		// Set the Entry ID array
		if ( isset( $_REQUEST['entry'] ) ) {
			if ( is_array( $_REQUEST['entry'] ) )
				$entry_id = $_REQUEST['entry'];
			else
				$entry_id = (array) $_REQUEST['entry'];
		}

		switch( $this->current_action() ) {
			case 'trash' :
				foreach ( $entry_id as $id ) {
					$id = absint( $id );
					$wpdb->update( $this->entries_table_name, array( 'entry_approved' => 'trash' ), array( 'entries_id' => $id ) );
				}
			break;

			case 'delete' :
				foreach ( $entry_id as $id ) {
					$id = absint( $id );
					$wpdb->query( $wpdb->prepare( "DELETE FROM $this->entries_table_name WHERE entries_id = %d", $id ) );
				}
			break;

			case 'restore' :
				foreach ( $entry_id as $id ) {
					$id = absint( $id );
					$wpdb->update( $this->entries_table_name, array( 'entry_approved' => 1 ), array( 'entries_id' => $id ) );
				}
			break;

			case 'spam' :
				foreach ( $entry_id as $id ) {
					$id = absint( $id );

					$spam = $this->akismet_check( 'spam', $id );

					if ( is_wp_error( $spam ) )
						$this->errors = $spam;
					elseif ( $spam )
						$wpdb->update( $this->entries_table_name, array( 'entry_approved' => 'spam' ), array( 'entries_id' => $id ) );

				}
			break;

			case 'unspam' :
				foreach ( $entry_id as $id ) {
					$id = absint( $id );

					$spam = $this->akismet_check( 'ham', $id );

					if ( is_wp_error( $spam ) )
						$this->errors = $spam;
					elseif ( $spam )
						$wpdb->update( $this->entries_table_name, array( 'entry_approved' => 1 ), array( 'entries_id' => $id ) );
				}
			break;
		}
	}

	/**
	 * Output errors for the entries screen
	 *
	 * @since 2.1
	 * @return string Error message
	 */
	function entries_errors() {
		$error = $this->errors;

		if ( is_wp_error( $error ) )
			echo '<div id="vfb-entries-error" class="updated fade"><p>' . $error->get_error_message() . '</p></div>';
	}

	/**
	 * Adds our forms filter dropdown
	 *
	 * @since 1.2
	 */
	function extra_tablenav( $which ) {
		global $wpdb;

		$where = apply_filters( 'vfb_pre_get_entries_formlist', '' );

		$cols = $wpdb->get_results( "SELECT DISTINCT forms.form_title, forms.form_id FROM $this->form_table_name AS forms WHERE 1=1 $where ORDER BY forms.form_id ASC" );

		// Only display the dropdown on the top of the table
		if ( 'top' == $which ) {
			echo '<div class="alignleft actions">';
				$this->months_dropdown();
			echo '<select id="form-filter" name="form-filter">
				<option value="-1"' . selected( $this->current_filter_action(), -1 ) . '>' . __( 'View all forms' , 'visual-form-builder-pro') . '</option>';

			foreach ( $cols as $form ) {
				echo sprintf( '<option value="%1$d"%2$s>%1$d - %3$s</option>',
					$form->form_id,
					selected( $this->current_filter_action(), $form->form_id ),
					$form->form_title
				);
			}

			echo '</select>
				<input type="submit" value="' . __( 'Filter' , 'visual-form-builder-pro') . '" class="button-secondary" />
				</div>';
		}
	}

	/**
	 * Display Year/Month filter
	 *
	 * @since 1.4.1
	 */
	function months_dropdown( $post_type = '' ) {
		global $wpdb, $wp_locale;

		$where = apply_filters( 'vfb_pre_get_entries', '' );

	    $months = $wpdb->get_results( "
			SELECT DISTINCT YEAR( entries.date_submitted ) AS year, MONTH( entries.date_submitted ) AS month
			FROM $this->entries_table_name AS entries
			WHERE 1=1 $where
			ORDER BY entries.date_submitted DESC
		" );

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		$m = isset( $_REQUEST['m'] ) ? (int) $_REQUEST['m'] : 0;
?>
		<select name='m'>
			<option<?php selected( $m, 0 ); ?> value='0'><?php _e( 'Show all dates' ); ?></option>
<?php
		foreach ( $months as $arc_row ) {
			if ( 0 == $arc_row->year )
				continue;

			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;

			printf( "<option %s value='%s'>%s</option>\n",
				selected( $m, $year . $month, false ),
				esc_attr( $arc_row->year . $month ),
				sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
			);
		}
?>
		</select>
<?php
	}

	/**
	 * Set our forms filter action
	 *
	 * @since 1.2
	 * @returns int Form ID
	 */
	function current_filter_action() {
		if ( isset( $_POST['form-filter'] ) && -1 != $_POST['form-filter'] )
			return absint( $_POST['form-filter'] );

		return false;
	}

	/**
	 * Display Search box
	 *
	 * @since 1.4
	 * @returns html Search Form
	 */
	function search_box( $text, $input_id ) {
	    parent::search_box( $text, $input_id );
	}

	/**
	 * Prepares our data for display
	 *
	 * @since 1.2
	 */
	function prepare_items() {
		global $wpdb;

		// get the current user ID
		$user = get_current_user_id();

		// get the current admin screen
		$screen = get_current_screen();

		// retrieve the "per_page" option
		$screen_option = $screen->get_option( 'per_page', 'option' );

		// retrieve the value of the option stored for the current user
		$per_page = get_user_meta( $user, $screen_option, true );

		// get the default value if none is set
		if ( empty ( $per_page) || $per_page < 1 )
			$per_page = $screen->get_option( 'per_page', 'default' );

		// Get the date/time format that is saved in the options table
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		// What page are we looking at?
		$current_page = $this->get_pagenum();

		// Use offset for pagination
		$offset = ( $current_page - 1 ) * $per_page;

		// Get column headers
		$columns  = $this->get_columns();
		$hidden   = get_hidden_columns( $this->screen );

		// Get sortable columns
		$sortable = $this->get_sortable_columns();

		// Build the column headers
		$this->_column_headers = array($columns, $hidden, $sortable);

		// Get entries search terms
		$search_terms = ( !empty( $_POST['s'] ) ) ? explode( ' ', $_POST['s'] ) : array();

		$searchand = $search = '';
		// Loop through search terms and build query
		foreach( $search_terms as $term ) {
			$term = esc_sql( like_escape( $term ) );

			$search .= "{$searchand}((entries.subject LIKE '%{$term}%') OR (entries.sender_name LIKE '%{$term}%') OR (entries.sender_email LIKE '%{$term}%') OR (entries.emails_to LIKE '%{$term}%') OR (entries.data LIKE '%{$term}%'))";
			$searchand = ' AND ';
		}

		$search = ( !empty($search) ) ? " AND ({$search}) " : '';

		// Set our ORDER BY and ASC/DESC to sort the entries
		$orderby  = ( !empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date';
		$order    = ( !empty( $_GET['order'] ) ) ? $_GET['order'] : 'desc';

		// Get the sorted entries
		$entries = $this->get_entries( $orderby, $order, $per_page, $offset, $search );

		$data = array();

		// Loop trough the entries and setup the data to be displayed for each row
		foreach ( $entries as $entry ) {
			$data[] =
				array(
					'entry_id' 		=> $entry->entries_id,
					'id' 			=> $entry->entries_id,
					'form' 			=> stripslashes( $entry->form_title ),
					'subject' 		=> stripslashes( $entry->subject ),
					'sender_name'	=> stripslashes( $entry->sender_name ),
					'sender_email'	=> stripslashes( $entry->sender_email ),
					'emails_to' 	=> implode( ',', unserialize( stripslashes( $entry->emails_to ) ) ),
					'date' 			=> date( "$date_format $time_format", strtotime( $entry->date_submitted ) ),
					'ip_address' 	=> $entry->ip_address,
			);
		}

		$where = apply_filters( 'vfb_pre_get_entries', '' );

		// If the form filter dropdown is used
		if ( $this->current_filter_action() )
			$where .= ' AND entries.form_id = ' . $this->current_filter_action();

		// Get the month/year from the dropdown
		$m = isset( $_REQUEST['m'] ) ? (int) $_REQUEST['m'] : 0;

		// Parse month/year and build the clause
		if ( $m > 0 ) {
			$year	 = substr( $m, 0, 4 );
			$month   = substr( $m, -2 );

			$where .= " AND YEAR(entries.date_submitted) = $year AND MONTH(entries.date_submitted) = $month";
		}

		// Get the month/year from the dropdown
		$today = isset( $_REQUEST['today'] ) ? (int) $_REQUEST['today'] : 0;

		// Parse month/year and build the clause
		if ( $today > 0 )
			$where .= " AND entries.date_submitted >= curdate()";

		// Entry type filter
		$where .= ( $this->get_entry_status() && 'all' !== $this->get_entry_status() ) ? $wpdb->prepare( ' AND entries.entry_approved = %s', $this->get_entry_status() ) : '';

		// Always display approved entries, unless an Entries Type filter is set
		if ( !$this->get_entry_status() || 'all' == $this->get_entry_status() )
			$where .= $wpdb->prepare( ' AND entries.entry_approved = %d', 1 );

		// How many entries do we have?
		$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $this->entries_table_name AS entries WHERE 1=1 $where" );

		// Add sorted data to the items property
		$this->items = $data;

		// Register our pagination
		$this->set_pagination_args( array(
			'total_items'	=> $total_items,
			'per_page'		=> $per_page,
			'total_pages'	=> ceil( $total_items / $per_page )
		) );
	}

	/**
	 * Display the pagination.
	 * Customize default function to work with months and form drop down filters
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function pagination( $which ) {
		if ( empty( $this->_pagination_args ) )
			return;

		$total_items = $this->_pagination_args['total_items'];
		$total_pages = $this->_pagination_args['total_pages'];

		$output = '<span class="displaying-num">' . sprintf( _n( '1 entry', '%s entries', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		// Added to pick up the months dropdown
		$m = isset( $_REQUEST['m'] ) ? (int) $_REQUEST['m'] : 0;

		$disable_first = $disable_last = '';
		if ( $current == 1 )
			$disable_first = ' disabled';
		if ( $current == $total_pages )
			$disable_last = ' disabled';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page' ),
			esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		// Modified the add_query_args to include my custom dropdowns
		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page' ),
			esc_url( add_query_arg( array( 'paged' => max( 1, $current-1 ), 'm' => $m, 'form-filter' => $this->current_filter_action() ), $current_url ) ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which )
			$html_current_page = $current;
		else
			$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='paged' value='%s' size='%d' />",
				esc_attr__( 'Current page' ),
				$current,
				strlen( $total_pages )
			);

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page' ),
			esc_url( add_query_arg( array( 'paged' => min( $total_pages, $current+1 ), 'm' => $m, 'form-filter' => $this->current_filter_action() ), $current_url ) ),
			'&rsaquo;'
		);

		// Modified the add_query_args to include my custom dropdowns
		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page' ),
			esc_url( add_query_arg( array( 'paged' => $total_pages, 'm' => $m, 'form-filter' => $this->current_filter_action() ), $current_url ) ),
			'&raquo;'
		);

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) )
			$pagination_links_class = ' hide-if-js';
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages )
			$page_class = $total_pages < 2 ? ' one-page' : '';
		else
			$page_class = ' no-pages';

		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}

	/**
	 * Akismet check for marking an entry as Spam/Not Spam
	 *
	 * @since 2.1
	 * @returns bool|wp_error
	 */
	protected function akismet_check( $type, $id ) {
		if ( !method_exists( 'Akismet', 'http_post' ) || !function_exists( 'akismet_http_post' ) )
			return new WP_Error( 'missing_akismet', 'Akismet does not appear to be installed and/or active. Entry not marked as spam.' );

		global $akismet_api_host, $akismet_api_port, $wpdb;

		$query_string = '';
		$api = ( 'spam' == $type ) ? 'submit-spam' : 'submit-ham';

		$akismet_data = maybe_unserialize( $wpdb->get_var( $wpdb->prepare( "SELECT akismet FROM $this->entries_table_name WHERE entries_id = %d", $id ) ) );

		foreach ( array_keys( $akismet_data ) as $k ) {
			$query_string .= $k . '=' . urlencode( $akismet_data[ $k ] ) . '&';
		}

		if ( method_exists( 'Akismet', 'http_post' ) )
		    $response = Akismet::http_post( $query_string, $api );
		else
		    $response = akismet_http_post( $query_string, $akismet_api_host, "/1.1/$api", $akismet_api_port );

		// Only return true if a response is available
		if ( $response ) {
			if ( 'Thanks for making the web a better place.' == $response[1] )
				return true;
		}
		else
			return new WP_Error( 'unexpected_response', 'No response returned from Akismet. This could be a connectivity issue or a missing Akismet API key. Entry not marked as spam.' );

		return false;
	}
}
