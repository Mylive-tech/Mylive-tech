<?php
/**
 * Class that builds our Entries table
 *
 * @since 1.2
 */
class VisualFormBuilder_Pro_Analytics {
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

	public function __construct(){
		global $wpdb;

		// Setup global database table names
		$this->field_table_name 	= $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name 		= $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name 	= $wpdb->prefix . 'vfb_pro_entries';

		add_action( 'admin_init', array( &$this, 'display' ) );
	}

	public function display(){
		global $wpdb;

		// Query to get all forms
		$order = sanitize_sql_orderby( 'form_id ASC' );
		$where = apply_filters( 'vfb_pre_get_forms_analytics', '' );
		$forms = $wpdb->get_results( "SELECT * FROM $this->form_table_name WHERE 1=1 $where ORDER BY $order" );

		if ( !$forms ) :
			echo '<div class="vfb-form-alpha-list"><h3 id="vfb-no-forms">You currently do not have any forms.  Click on the <a href="' . esc_url( admin_url( 'admin.php?page=vfb-add-new' ) ) . '">New Form</a> button to get started.</h3></div>';

		else :

		$form_nav_selected_id = ( isset( $_REQUEST['form_id'] ) ) ? absint( $_REQUEST['form_id'] ) : $forms[0]->form_id;

		$entries = $wpdb->get_results( "SELECT DAY( date_submitted ) AS Day, MONTH( date_submitted ) AS Month, YEAR( date_submitted ) AS Year, COUNT(*) AS Count FROM $this->entries_table_name WHERE form_id = $form_nav_selected_id GROUP BY Day ORDER BY Count DESC" );

		?>

        <form method="post" id="analytics-switcher">
            <div class="tablenav top">
	            <label for="form_id"><p style="margin:8px 0;"><em><?php _e( 'Select which form analytics to view', 'visual-form-builder-pro' ); ?>:</em></p></label>
	            <div class="alignleft actions">
		            <select name="form_id">
					<?php
					$count = $sum = $avg = 0;
					foreach ( $forms as $form ) {
						if ( $form_nav_selected_id == $form->form_id ) {
							$count = count( $entries );
							$busy_date = date( 'M d, Y', mktime( 0, 0, 0, $entries[0]->Month, $entries[0]->Day, $entries[0]->Year ) );
							$busy_count = $entries[0]->Count;

							foreach ( $entries as $entry ) {
								$sum += $entry->Count;
							}

							$avg = round( $sum / $count );
						}

						echo sprintf( '<option value="%1$d"%2$s id="%4$s">%1$d - %3$s</option>',
							$form->form_id,
							selected( $form->form_id, $form_nav_selected_id, 0 ),
							$form->form_title,
							$form->form_key
						);
					}
			?>
					</select>
					<?php submit_button( __( 'Select', 'visual-form-builder-pro' ), 'secondary', 'submit', false ); ?>
	            </div>

	            <div class="alignleft actions">
					<select name="analytics-start-date">
						<option value="0">Start Date</option>
						<?php $this->months_dropdown( 'start' ); ?>
					</select>
					<select name="analytics-end-date">
						<option value="0">End Date</option>
						<?php $this->months_dropdown( 'end' ); ?>
					</select>
					<?php submit_button( __( 'Filter', 'visual-form-builder-pro' ), 'secondary', 'analytics-filter', false ); ?>
	            </div>
            </div>
        </form>

        <div id="nav-menus-frame">
            <div id="menu-settings-column" class="metabox-holder">
                <div class="analytics-meta-boxes">
                    <h1><?php _e( 'Entries Total', 'visual-form-builder-pro' ); ?></h1>
                    <h2><?php echo $sum; ?></h2>
                </div>
                <div class="analytics-meta-boxes">
                    <h1><?php _e( 'Average per Day', 'visual-form-builder-pro' ); ?></h1>
                    <h2><?php echo $avg; ?></h2>
                </div>
                <div class="analytics-meta-boxes">
                    <h1><?php _e( 'Your Busiest Day', 'visual-form-builder-pro' ); ?></h1>
                    <h2><?php echo $busy_date; ?></h2>
					<h3><?php echo $busy_count; ?> <?php _e( 'Entries', 'visual-form-builder-pro' ); ?></h3>
                </div>
            </div>

            <div id="menu-management-liquid" class="charts-container">
                <h2><?php _e( 'Daily', 'visual-form-builder-pro' ); ?></h2>
                <div id="vfb-graph-daily">
                	<div class="chart-loading"><?php _e( 'Loading', 'visual-form-builder-pro' ); ?>... <img id="chart-loading" alt="" src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" class="waiting spinner" /></div>
                </div>
                <h2><?php _e( 'Monthly', 'visual-form-builder-pro' ); ?></h2>
                <div id="vfb-graph-monthly">
                	<div class="chart-loading"><?php _e( 'Loading', 'visual-form-builder-pro' ); ?>... <img id="chart-loading" alt="" src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" class="waiting spinner" /></div>
                </div>
            </div>
        </div>
<?php
		endif;
	}

	/**
	 * Display Year/Month filter
	 *
	 * @since 1.7
	 */
	public function months_dropdown( $period ) {
		global $wpdb, $wp_locale;

		//$where = apply_filters( 'vfb_pre_get_entries', '' );
		$where = '';
	    $months = $wpdb->get_results( "
			SELECT DISTINCT YEAR( forms.date_submitted ) AS year, MONTH( forms.date_submitted ) AS month
			FROM $this->entries_table_name AS forms
			WHERE 1=1 $where
			ORDER BY forms.date_submitted DESC
		" );

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		foreach ( $months as $arc_row ) {
			if ( 0 == $arc_row->year )
				continue;

			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;

			printf( "<option value='%s'>%s</option>\n",
				esc_attr( $arc_row->year . '-' . $month ),
				sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
			);
		}

	}

}
