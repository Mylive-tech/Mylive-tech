<?php
/**
 * Class that builds our Entries table
 *
 * @since 1.2
 */
class VisualFormBuilder_Pro_Forms_Order extends WP_List_Table {
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

	function __construct(){
		global $wpdb;

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
			case 'entries' :
				return $item[ $column_name ];
		}
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
		global $wpdb, $current_user;

		get_currentuserinfo();

		// Save current user ID
		$user_id = $current_user->ID;

		// Get the Form Order type settings, if any
		$user_form_order_type = get_user_meta( $user_id, 'vfb-form-order-type', true );

		// Get the Form Order settings, if any
		$user_form_order = get_user_meta( $user_id, 'vfb-form-order' );
		foreach ( $user_form_order as $form_order ) {
			$form_order = implode( ',', $form_order );
		}

		$where = '';

		// Forms type filter
		$where .= ( $this->get_form_status() && 'all' !== $this->get_form_status() ) ? $wpdb->prepare( ' AND forms.form_status = %s', $this->get_form_status() ) : '';

		// Always display all forms, unless an Form Type filter is set
		if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) )
			$where .= $wpdb->prepare( ' AND forms.form_status IN("%s", "%s")', 'publish', 'draft' );

		$sql_order = sanitize_sql_orderby( "$orderby $order" );

		if ( in_array( $user_form_order_type, array( 'order', '' ) ) )
			$sql_order = ( isset( $form_order ) ) ? "FIELD( form_id, $form_order )" : sanitize_sql_orderby( 'form_id DESC' );
		else
			$sql_order = sanitize_sql_orderby( 'form_title ASC' );

		$cols = $wpdb->get_results( "SELECT forms.form_id, forms.form_title, forms.form_status FROM $this->form_table_name AS forms WHERE 1=1 $where $search ORDER BY $sql_order" );

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
		$forms = $this->get_forms( $orderby, $order, $search );

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

		// Register our pagination
		$this->set_pagination_args( array(
			'total_items'	=> $total_items,
			'per_page'		=> $per_page,
			'total_pages'	=> ceil( $total_items / $per_page ),
		) );

		// Add sorted data to the items property
		$this->items = $data;
	}

	function display() {
		if ( $this->has_items() ) :

			echo '<div class="tablenav top">';
				$this->pagination( 'top' );
			echo '<br class="clear" /></div>';

			$this->display_forms();

			echo '<div class="vfb-empty-container ui-state-disabled"></div>';
		endif;
	}

	function display_forms() {
		foreach ( $this->items as $item )
			$this->single_row( $item );
	}

	function single_row( $item ) {

		$count = array_map( 'intval', $item['entries'] );

		$draft_state = '';

		// Append Draft status to title
		if ( 'draft' == $item['status'] && 'draft' !== $this->get_form_status() )
			$draft_state = sprintf( '<strong> - %s</strong>', __( 'Draft', 'visual-form-builder-pro' ) );
?>
		<div class="vfb-box form-boxes" id="vfb-form-<?php echo $item['form_id']; ?>">
			<div class="vfb-form-meta-actions">
				<h2 title="<?php esc_attr_e( 'Drag to reorder', 'visual-form-builder-pro' ); ?>" class="form-boxes-title"><?php echo $item['form_title'] . $draft_state; ?></h2>

				<div class="vfb-form-meta-entries">
					<ul class="vfb-meta-entries-list">
						<li><a  class="vfb-meta-entries-header" href="<?php echo esc_url( add_query_arg( array( 'form-filter' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-entries' ) ) ); ?>"><?php _e( 'Entries', 'visual-form-builder-pro' ); ?></a></li>
						<li><a class="vfb-meta-entries-total" href="<?php echo esc_url( add_query_arg( array( 'form-filter' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-entries' ) ) ); ?>"><span class="entries-count"><?php echo $count['total']; ?></span></a> <?php _e( 'Total', 'visual-form-builder-pro' ); ?></li>
						<li><a class="vfb-meta-entries-total-today" href="<?php echo esc_url( add_query_arg( array( 'form-filter' => $item['form_id'], 'today' => 1 ), admin_url( 'admin.php?page=vfb-entries' ) ) ); ?>"><span class="entries-count"><?php echo $count['today']; ?></span></a> <?php _e( 'Today', 'visual-form-builder-pro' ); ?></li>
					</ul>
				</div>

				<div class="vfb-form-meta-other">
					<ul>
						<li><a href="<?php echo esc_url( add_query_arg( array( 'form_id' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-email-design' ) ) ); ?>"><?php _e( 'Email Design', 'visual-form-builder-pro' ); ?></a></li>
						<li><a href="<?php echo esc_url( add_query_arg( array( 'form_id' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-reports' ) ) ); ?>"><?php _e( 'Analytics', 'visual-form-builder-pro' ); ?></a></li>
						<?php if ( class_exists( 'VFB_Pro_Payments' ) ) : ?>
						<li><a href="<?php echo esc_url( add_query_arg( array( 'form_id' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-payments' ) ) ); ?>"><?php _e( 'Payments', 'visual-form-builder-pro' ); ?></a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<div class="clear"></div>
			<div class="vfb-publishing-actions">
	            <p>
	            <?php
	            // Default Forms view
	            if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) :
					// Edit Form
					if ( current_user_can( 'vfb_edit_forms' ) ) :
						echo sprintf( '<a href="?page=%s&action=%s&form=%s"><strong>%s</strong></a> | ', $_REQUEST['page'], 'edit', $item['form_id'], __( 'Edit', 'visual-form-builder-pro' ) );
					endif;
				endif;

				// Trashed Forms view
            	if ( current_user_can( 'vfb_delete_forms' ) ) :
            		if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) )
						echo sprintf( '<a class="submitdelete menu-delete" href="?page=%s&action=%s&form=%s">%s</a> | ', $_REQUEST['page'], 'trash', $item['form_id'], __( 'Trash', 'visual-form-builder-pro' ) );
					elseif ( $this->get_form_status() && 'trash' == $this->get_form_status() ) {
						echo sprintf( '<a href="?page=%s&action=%s&form=%s">%s</a> | ', $_REQUEST['page'], 'restore', $item['form_id'], __( 'Restore', 'visual-form-builder-pro' ) );
						echo sprintf( '<a class="submitdelete menu-delete" href="?page=%s&action=%s&form=%s">%s</a>', $_REQUEST['page'], 'delete', $item['form_id'], __( 'Delete Permanently', 'visual-form-builder-pro' ) );
					}
            	endif;

            	// Ensure Preview link is always last
            	if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) :
	            	if ( current_user_can( 'vfb_edit_forms' ) ) :
						echo sprintf( '<a href="%1$s" id="%3$s" class="view-form" target="_blank">%3$s</a>', esc_url( add_query_arg( array( 'form' => $item['form_id'], 'preview' => 1 ), plugins_url( 'visual-form-builder-pro/form-preview.php' ) ) ), $item['form_id'], __( 'Preview', 'visual-form-builder-pro' ) );
					endif;
				endif;
	            ?>
	            </p>
			</div> <!-- .vfb-publishing-actions -->
		</div> <!-- .vfb-box -->
<?php
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

		$this->_pagination = "<div class='tablenav-pages'>$output</div>";

		echo $this->_pagination;

		// Current user ID
		$user_id = $current_user->ID;

		// Form order type
		$type = get_user_meta( $user_id, 'vfb-form-order-type', true );

		if ( 'top' == $which )
			$this->view_switcher( $type );
	}

}
