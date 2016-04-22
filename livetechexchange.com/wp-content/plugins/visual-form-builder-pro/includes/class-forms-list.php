<?php
/**
 * Class that builds our Entries table
 *
 * @since 1.2
 */
class VisualFormBuilder_Pro_Forms_List extends WP_List_Table {
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

		// Setup global database table names
		$this->field_table_name   = $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name    = $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name = $wpdb->prefix . 'vfb_pro_entries';

		// Set parent defaults
		parent::__construct( array(
			'singular'  => 'form',
			'plural'    => 'forms',
			'ajax'      => false
		) );

		// Handle our bulk actions
		$this->process_bulk_action();
	}

	/**
	 * Display column names
	 *
	 * @since 1.2
	 * @returns $item string Column name
	 */
	function column_default( $item, $column_name ){
		switch ( $column_name ) {
			case 'id':
			case 'form_id' :
				return $item[ $column_name ];
		}
	}

	/**
	 * Builds the on:hover links for the Form column
	 *
	 * @since 1.2
	 */
	function column_form_title( $item ){

		$actions = array();

		$form_title = sprintf( '<strong>%s</strong>', $item['form_title'] );

		$draft_state = '';

		// Default Entries view
		if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) :
			// Edit Form
			if ( current_user_can( 'vfb_edit_forms' ) ) :
				// Append Draft status to title
				if ( 'draft' == $item['status'] && 'draft' !== $this->get_form_status() )
					$draft_state = sprintf( '<strong> - %s</strong>', __( 'Draft', 'visual-form-builder-pro' ) );

				$form_title = sprintf( '<a href="?page=%s&action=%s&form=%s" id="%3$s" class="view-form">%s</a>%s', $_REQUEST['page'], 'edit', $item['form_id'], $form_title, $draft_state );
				$actions['edit'] = sprintf( '<a href="?page=%s&action=%s&form=%s" id="%3$s" class="view-form">%s</a>', $_REQUEST['page'], 'edit', $item['form_id'], __( 'Edit', 'visual-form-builder-pro' ) );
			endif;

			// Email Design
			if ( current_user_can( 'vfb_edit_email_design' ) ) :
				$actions['email'] = sprintf( '<a href="%s&form_id=%s" id="%3$s" class="view-form">%s</a>', admin_url( 'admin.php?page=vfb-email-design' ), $item['form_id'], __( 'Email Design', 'visual-form-builder-pro' ) );
			endif;

			// Analytics
			if ( current_user_can( 'vfb_view_analytics' ) ) :
				$actions['analytics'] = sprintf( '<a href="%s&form_id=%s" id="%3$s" class="view-form">%s</a>', admin_url( 'admin.php?page=vfb-reports' ), $item['form_id'], __( 'Analytics', 'visual-form-builder-pro' ) );
			endif;

			// Payments Add-on
			if ( current_user_can( 'vfb_edit_forms' ) && class_exists( 'VFB_Pro_Payments' ) ) :
				$actions['payments'] = sprintf( '<a href="%s&form_id=%s" id="%3$s" class="view-form">%s</a>', admin_url( 'admin.php?page=vfb-payments' ), $item['form_id'], __( 'Payments', 'visual-form-builder-pro' ) );
			endif;

			// Duplicate Form
			if ( current_user_can( 'vfb_copy_forms' ) ) :
				$actions['copy'] = sprintf( '<a href="%s&action=%s&form=%s" id="%3$s" class="view-form">%s</a>', wp_nonce_url( admin_url( 'admin.php?page=visual-form-builder-pro' ), 'copy-form-' . $item['form_id'] ), 'copy_form', $item['form_id'], __( 'Duplicate', 'visual-form-builder-pro' ) );
			endif;
		endif;

		// Trashed Forms view
		if ( current_user_can( 'vfb_delete_forms' ) ) :
			if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) )
				$actions['trash'] = sprintf( '<a href="?page=%s&action=%s&form=%s">%s</a>', $_REQUEST['page'], 'trash', $item['form_id'], __( 'Trash', 'visual-form-builder-pro' ) );
			elseif ( $this->get_form_status() && 'trash' == $this->get_form_status() ) {
				$actions['restore'] = sprintf( '<a href="?page=%s&action=%s&form=%s">%s</a>', $_REQUEST['page'], 'restore', $item['form_id'], __( 'Restore', 'visual-form-builder-pro' ) );
				$actions['delete'] = sprintf( '<a href="?page=%s&action=%s&form=%s">%s</a>', $_REQUEST['page'], 'delete', $item['form_id'], __( 'Delete Permanently', 'visual-form-builder-pro' ) );
			}
		endif;

		// Ensure Preview link is always last
		if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) :
			// Preview Form
			if ( current_user_can( 'vfb_edit_forms' ) ) :
				$actions['view'] = sprintf( '<a href="%1$s" id="%3$s" class="view-form" target="_blank">%3$s</a>', esc_url( add_query_arg( array( 'form' => $item['form_id'], 'preview' => 1 ), plugins_url( 'visual-form-builder-pro/form-preview.php' ) ) ), $item['form_id'], __( 'Preview', 'visual-form-builder-pro' ) );
			endif;
		endif;

		return sprintf( '%1$s %2$s', $form_title, $this->row_actions( $actions ) );
	}

	/**
	 * column_entries function.
	 *
	 * @access public
	 * @param mixed $item
	 * @return void
	 */
	function column_entries( $item ) {
		$this->comments_bubble( $item['form_id'], $item['entries'] );
	}

	/**
	 * comments_bubble function.
	 *
	 * @access public
	 * @param mixed $form_id
	 * @param mixed $count
	 * @return void
	 */
	function comments_bubble( $form_id, $count ) {

		echo sprintf(
			'<div class="entries-count-wrapper"><a href="%1$s" title="%2$s" class="vfb-meta-entries-total"><span class="entries-count">%4$s</span></a> %3$s</div>',
			esc_url( add_query_arg( array( 'form-filter' => $form_id ), admin_url( 'admin.php?page=vfb-entries' ) ) ),
			esc_attr__( 'Entries Total', 'visual-form-builder-pro' ),
			__( 'Total', 'visual-form-builder-pro' ),
			number_format_i18n( $count['total'] )
		);

		if ( $count['today'] )
			echo '<strong>';

		echo sprintf(
			'<div class="entries-count-wrapper"><a href="%1$s" title="%2$s" class="vfb-meta-entries-total"><span class="entries-count">%4$s</span></a> %3$s</div>',
			esc_url( add_query_arg( array( 'form-filter' => $form_id, 'today' => 1 ), admin_url( 'admin.php?page=vfb-entries' ) ) ),
			esc_attr__( 'Entries Today', 'visual-form-builder-pro' ),
			__( 'Today', 'visual-form-builder-pro' ),
			number_format_i18n( $count['today'] )
		);

		if ( $count['today'] )
			echo '</strong>';
	}

	/**
	 * Used for checkboxes and bulk editing
	 *
	 * @since 1.2
	 */
	function column_cb( $item ){
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['form_id'] );
	}

	/**
	 * Builds the actual columns
	 *
	 * @since 1.2
	 */
	function get_columns(){
		$columns = array(
			'cb' 			=> '<input type="checkbox" />', //Render a checkbox instead of text
			'form_title' 	=> __( 'Form' , 'visual-form-builder-pro'),
			'form_id' 		=> __( 'Form ID' , 'visual-form-builder-pro'),
			'entries'		=> __( 'Entries', 'visual-form-builder-pro' ),
		);

		return $columns;
	}

	/**
	 * A custom function to get the entries and sort them
	 *
	 * @since 1.2
	 * @returns array() $cols SQL results
	 */
	function get_forms( $orderby = 'form_id', $order = 'ASC', $per_page, $offset = 0, $search = '' ){
		global $wpdb;

		// Set OFFSET for pagination
		$offset = ( $offset > 0 ) ? "OFFSET $offset" : '';

		$where = apply_filters( 'vfb_pre_get_forms', '' );

		// If the form filter dropdown is used
		if ( $this->current_filter_action() )
			$where .= ' AND forms.form_id = ' . $this->current_filter_action();

		// Forms type filter
		$where .= ( $this->get_form_status() && 'all' !== $this->get_form_status() ) ? $wpdb->prepare( ' AND forms.form_status = %s', $this->get_form_status() ) : '';

		// Always display all forms, unless an Form Type filter is set
		if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) )
			$where .= $wpdb->prepare( ' AND forms.form_status IN("%s","%s")', 'publish', 'draft' );

		$sql_order = sanitize_sql_orderby( "$orderby $order" );
		$cols = $wpdb->get_results( "SELECT forms.form_id, forms.form_title, forms.form_status FROM $this->form_table_name AS forms WHERE 1=1 $where $search ORDER BY $sql_order LIMIT $per_page $offset" );

		return $cols;
	}

	/**
	 * Get the form status: All, Trash
	 *
	 * @since 2.3.7
	 * @returns string Form status
	 */
	function get_form_status() {
		if ( !isset( $_REQUEST['form_status'] ) )
			return false;

		return esc_html( $_REQUEST['form_status'] );
	}

	/**
	 * Build the different views for the entries screen
	 *
	 * @since 2.1
	 * @returns array $status_links Status links with counts
	 */
	function get_views() {
		$status_links = array();
		$num_forms    = $this->get_forms_count();
		$class        = '';
		$link         = '?page=visual-form-builder-pro';

		$stati = array(
			'all'    => _n_noop( 'All <span class="count">(<span class="pending-count">%s</span>)</span>', 'All <span class="count">(<span class="pending-count">%s</span>)</span>' ),
			'draft'  => _n_noop( 'Draft <span class="count">(<span class="pending-count">%s</span>)</span>', 'Drafts <span class="count">(<span class="pending-count">%s</span>)</span>' ),
			'trash'  => _n_noop( 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>', 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>' ),
		);

		$total_forms = (int) $num_forms->all;
		$entry_status = isset( $_REQUEST['form_status'] ) ? $_REQUEST['form_status'] : 'all';

		foreach ( $stati as $status => $label ) {
			$class = ( $status == $entry_status ) ? ' class="current"' : '';

			if ( !isset( $num_forms->$status ) )
				$num_forms->$status = 10;

			$link = add_query_arg( 'form_status', $status, $link );

			$status_links[ $status ] = "<li class='$status'><a href='$link'$class>" . sprintf(
				translate_nooped_plural( $label, $num_forms->$status ),
				number_format_i18n( $num_forms->$status )
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

		$total_entries = array();

		$entries = $wpdb->get_results( "SELECT form_id, COUNT(form_id) as num_entries FROM $this->entries_table_name AS entries WHERE entries.entry_approved = 1 GROUP BY form_id", ARRAY_A );

		if ( $entries ) {
			foreach ( $entries as $entry )
				$total_entries[ $entry['form_id'] ] = absint( $entry['num_entries'] );

			return $total_entries;
		}

		return $total_entries;
	}

	/**
	 * Get the number of entries for use with entry statuses
	 *
	 * @since 2.1
	 * @returns array $stats Counts of different entry types
	 */
	function get_entries_today_count() {
		global $wpdb;

		$total_entries = array();

		$entries = $wpdb->get_results( "SELECT form_id, COUNT(form_id) as num_entries FROM $this->entries_table_name AS entries WHERE entries.entry_approved = 1 AND date_submitted >= curdate() GROUP BY form_id", ARRAY_A );

		if ( $entries ) {
			foreach ( $entries as $entry )
				$total_entries[ $entry['form_id'] ] = absint( $entry['num_entries'] );

			return $total_entries;
		}

		return $total_entries;
	}

	/**
	 * Get the number of forms
	 *
	 * @since 2.2.7
	 * @returns int $count Form count
	 */
	function get_forms_count() {
		global $wpdb;

		$stats = array();

		$forms = $wpdb->get_results( "SELECT forms.form_status, COUNT(*) AS num_forms FROM $this->form_table_name AS forms GROUP BY forms.form_status", ARRAY_A );

		$total = 0;
		$published = array( 'publish' => 'publish', 'draft' => 'draft', 'trash' => 'trash' );
		foreach ( (array) $forms as $row ) {
			// Don't count trashed toward totals
			if ( 'trash' != $row['form_status'] )
				$total += $row['num_forms'];
			if ( isset( $published[ $row['form_status' ] ] ) )
				$stats[ $published[ $row['form_status' ] ] ] = $row['num_forms'];
		}

		$stats['all'] = $total;
		foreach ( $published as $key ) {
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
			'form_id'		=> array( 'form_id', true ),
			'form_title'	=> array( 'form_title', true ),
			'entries'		=> array( 'entries', false ),
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
		$actions = array();

		// Build the row actions
		if ( current_user_can( 'vfb_delete_forms' ) ) {
			if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) {
				$actions['trash'] = __( 'Move to Trash', 'visual-form-builder-pro' );
			}
			elseif ( $this->get_form_status() && 'trash' == $this->get_form_status() ) {
				$actions['restore'] = __( 'Restore', 'visual-form-builder-pro' );
				$actions['delete'] = __( 'Delete Permanently', 'visual-form-builder-pro' );
			}
		}

		return apply_filters( 'vfb_forms_bulk_actions', $actions );
	}

	/**
	 * Process ALL actions on the Form screen, not only Bulk Actions
	 *
	 * @since 1.2
	 */
	function process_bulk_action() {
		global $wpdb;

		$form_id = '';

		// Set the Entry ID array
		if ( isset( $_REQUEST['form'] ) ) {
			if ( is_array( $_REQUEST['form'] ) )
				$form_id = $_REQUEST['form'];
			else
				$form_id = (array) $_REQUEST['form'];
		}

		switch( $this->current_action() ) :
			case 'trash' :
				foreach ( $form_id as $id ) {
					$id = absint( $id );
					$wpdb->update( $this->form_table_name, array( 'form_status' => 'trash' ), array( 'form_id' => $id ) );
				}
				break;

			case 'restore' :
				foreach ( $form_id as $id ) {
					$id = absint( $id );
					$wpdb->update( $this->form_table_name, array( 'form_status' => 'publish' ), array( 'form_id' => $id ) );
				}
				break;

			case 'delete' :
				foreach ( $form_id as $id ) {
					$id = absint( $id );
					$wpdb->query( $wpdb->prepare( "DELETE FROM $this->form_table_name WHERE form_id = %d", $id ) );
					$wpdb->query( $wpdb->prepare( "DELETE FROM $this->field_table_name WHERE form_id = %d", $id ) );
					$wpdb->query( $wpdb->prepare( "DELETE FROM $this->entries_table_name WHERE form_id = %d", $id ) );
				}
				break;

		endswitch;
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
		$search_terms = ( !empty( $_REQUEST['s'] ) ) ? explode( ' ', $_REQUEST['s'] ) : array();

		$searchand = $search = '';
		// Loop through search terms and build query
		foreach( $search_terms as $term ) {
			$term = esc_sql( like_escape( $term ) );

			$search .= "{$searchand}((forms.form_title LIKE '%{$term}%') OR (forms.form_key LIKE '%{$term}%') OR (forms.form_email_subject LIKE '%{$term}%'))";
			$searchand = ' AND ';
		}

		$search = ( !empty($search) ) ? " AND ({$search}) " : '';

		// Set our ORDER BY and ASC/DESC to sort the entries
		$orderby  = ( !empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'form_id';
		$order    = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc';

		// Get the sorted entries
		$forms = $this->get_forms( $orderby, $order, $per_page, $offset, $search );

		// Get entries totals
		$entries_total = $this->get_entries_count();
		$entries_today = $this->get_entries_today_count();

		$data = array();

		// Loop trough the entries and setup the data to be displayed for each row
		foreach ( $forms as $form ) :

			// Check if index exists first, not every form has entries
			$entries_total[ $form->form_id ] = isset( $entries_total[ $form->form_id ] ) ? $entries_total[ $form->form_id ] : 0;

			// Check if index exists first, not every form has entries today
			$entries_today[ $form->form_id ] = isset( $entries_today[ $form->form_id ] ) ? $entries_today[ $form->form_id ] : 0;

			$entries_counts = array(
				'total' => $entries_total[ $form->form_id ],
				'today' => $entries_today[ $form->form_id ],
			);

			$data[] = array(
				'id' 			=> $form->form_id,
				'form_id'		=> $form->form_id,
				'form_title' 	=> stripslashes( $form->form_title ),
				'entries'		=> $entries_counts,
				'status'		=> $form->form_status,
			);
		endforeach;

		$where = '';

		// Forms type filter
		$where .= ( $this->get_form_status() && 'all' !== $this->get_form_status() ) ? $wpdb->prepare( ' AND forms.form_status = %s', $this->get_form_status() ) : '';

		// Always display all forms, unless an Form Type filter is set
		if ( !$this->get_form_status() || 'all' == $this->get_form_status() )
			$where .= $wpdb->prepare( ' AND forms.form_status = %s', 'publish' );

		// How many form do we have?
		$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $this->form_table_name AS forms WHERE 1=1 $where" );

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
	 * Display a view switcher
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function view_switcher( $current_mode ) {
		$modes = array(
			'list'	=> __( 'List View', 'visual-form-builder-pro' ),
			'order'	=> __( 'Custom Order', 'visual-form-builder-pro' ),
		);
?>
		<input type="hidden" name="mode" value="<?php echo esc_attr( $current_mode ); ?>" />
		<div class="view-switch">
<?php
//echo '<pre>'; print_r( $modes ); echo '</pre>';
			foreach ( $modes as $mode => $title ) :
				$current = ( $current_mode == $mode ) ? 'current' : '';

				//
				$class = 'order' == $mode ? 'excerpt' : 'list';

				// Use excerpt to switch the default WP image but keep order as the mode action
				$real_mode = ( 'order' == $mode ) ? 'excerpt' : 'list';

				echo "<a href='" . esc_url( add_query_arg( 'mode', $mode, $_SERVER['REQUEST_URI'] ) ) . "' class='view-$class $current' id='view-switch-$class'></a>\n";
			endforeach;
		?>
		</div>
<?php
	}

	/**
	 * Display the pagination.
	 * Customize default function to work with months and form drop down filters
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function pagination( $which ) {
		global $current_user;

		if ( empty( $this->_pagination_args ) )
			return;

		$total_items = $this->_pagination_args['total_items'];
		$total_pages = $this->_pagination_args['total_pages'];

		$output = '<span class="displaying-num">' . sprintf( _n( '1 form', '%s forms', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

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

		// Current user ID
		$user_id = $current_user->ID;

		// Form order type
		$type = get_user_meta( $user_id, 'vfb-form-order-type', true );

		if ( 'top' == $which )
			$this->view_switcher( $type );
	}

}
