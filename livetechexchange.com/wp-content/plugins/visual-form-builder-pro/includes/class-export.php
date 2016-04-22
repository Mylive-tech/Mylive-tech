<?php
/**
 * Class that builds our Entries table
 *
 * @since 1.2
 */
class VisualFormBuilder_Pro_Export {

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
	 * design_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $design_table_name;

	/**
	 * payment_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $payment_table_name;

	/**
	 * export_version
	 *
	 * (default value: '2.4')
	 *
	 * @var string
	 * @access protected
	 */
	protected $export_version = '2.4';

	/**
	 * delimiter
	 *
	 * @var mixed
	 * @access public
	 */
	public $delimiter;

	/**
	 * default_cols
	 *
	 * @var mixed
	 * @access public
	 */
	public $default_cols;

	public function __construct(){
		global $wpdb;

		// CSV delimiter
		$this->delimiter = apply_filters( 'vfb_csv_delimiter', ',' );

		// Setup our default columns
		$this->default_cols = array(
			'entries_id' 		=> __( 'Entries ID' , 'visual-form-builder-pro' ),
			'date_submitted' 	=> __( 'Date Submitted' , 'visual-form-builder-pro' ),
			'ip_address' 		=> __( 'IP Address' , 'visual-form-builder-pro' ),
			'subject' 			=> __( 'Subject' , 'visual-form-builder-pro' ),
			'sender_name' 		=> __( 'Sender Name' , 'visual-form-builder-pro' ),
			'sender_email' 		=> __( 'Sender Email' , 'visual-form-builder-pro' ),
			'emails_to' 		=> __( 'Emailed To' , 'visual-form-builder-pro' ),
		);

		// Setup global database table names
		$this->field_table_name 	= $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name 		= $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name 	= $wpdb->prefix . 'vfb_pro_entries';
		$this->design_table_name 	= $wpdb->prefix . 'vfb_pro_form_design';
		$this->payment_table_name 	= $wpdb->prefix . 'vfb_pro_payments';

		// AJAX for loading new entry checkboxes
		add_action( 'wp_ajax_visual_form_builder_export_load_options', array( &$this, 'ajax_load_options' ) );

		// AJAX for getting entries count
		add_action( 'wp_ajax_visual_form_builder_export_entries_count', array( &$this, 'ajax_entries_count' ) );

		$this->process_export_action();
	}

	/**
	 * Display the export form
	 *
	 * @since 1.7
	 *
	 */
	public function display(){
		global $wpdb;

		// Query to get all forms
		$order = sanitize_sql_orderby( 'form_id ASC' );
		$where = apply_filters( 'vfb_pre_get_forms_export', '' );
		$forms = $wpdb->get_results( "SELECT form_id, form_key, form_title FROM $this->form_table_name WHERE 1=1 $where ORDER BY $order" );

		if ( !$forms ) {
			echo '<div class="vfb-form-alpha-list"><h3 id="vfb-no-forms">You currently do not have any forms.  Click on the <a href="' . esc_url( admin_url( 'admin.php?page=vfb-add-new' ) ) . '">New Form</a> button to get started.</h3></div>';
			return;
		}

		$entries_count = $this->count_entries( $forms[0]->form_id );

		// Return nothing if no entries found
		if ( !$entries_count ) :
			$no_entries = __( 'No entries to pull field names from.', 'visual-form-builder-pro' );
		else :

			$limit = $entries_count > 1000 ? 1000 : $entries_count;

			// Safe to get entries now
			$entries = $wpdb->get_results( $wpdb->prepare( "SELECT data FROM $this->entries_table_name WHERE form_id = %d AND entry_approved = 1 LIMIT %d", $forms[0]->form_id, $limit ), ARRAY_A );

			// Get columns
			$columns = $this->get_cols( $entries );

			// Get JSON data
			$data = json_decode( $columns, true );
		endif;
?>
        <form method="post" id="vfb-export">
        	<p><?php _e( 'Backup and save some or all of your Visual Form Builder Pro data.', 'visual-form-builder-pro' ); ?></p>
        	<p><?php _e( 'Once you have saved the file, you will be able to import Visual Form Builder Pro data from this site into another site.', 'visual-form-builder-pro' ); ?></p>
        	<h3><?php _e( 'Choose what to export', 'visual-form-builder-pro' ); ?></h3>

        	<p><label><input type="radio" name="vfb-content" value="all" checked="checked" /> <?php _e( 'All data', 'visual-form-builder-pro' ); ?></label></p>
        	<p class="description">
        		<?php _e( 'This will contain all of your forms, fields, entries, and email design settings.', 'visual-form-builder-pro' ); ?>
        		<?php if ( class_exists( 'VFB_Pro_Form_Designer' ) || class_exists( 'VFB_Pro_Payments' ) ) : ?>
					<br>
					<?php _e( 'Add-ons detected. Add-on data will export, too.', 'visual-form-builder-pro' ); ?>
				<?php endif; ?>
        	</p>

        	<p><label><input type="radio" name="vfb-content" value="forms" /> <?php _e( 'Forms', 'visual-form-builder-pro' ); ?></label></p>

        	<ul id="forms-filters" class="vfb-export-filters">
        		<li><p class="description"><?php _e( 'This will contain all of your forms, fields, and email design settings.', 'visual-form-builder-pro' ); ?></p></li>
        		<li>
		        	<label for="form_id"><?php _e( 'Forms', 'visual-form-builder-pro' ); ?>:</label>
		            <select name="forms_form_id">
		            	<option value="0">All</option>
<?php
						foreach ( $forms as $form ) :
							echo sprintf(
								'<option value="%1$d" id="%2$s">%1$d - %3$s</option>',
								$form->form_id,
								$form->form_key,
								stripslashes( $form->form_title )
							);
						endforeach;
?>
					</select>
        		</li>
        	</ul>

        	<p><label><input type="radio" name="vfb-content" value="entries" /> <?php _e( 'Entries', 'visual-form-builder-pro' ); ?></label></p>

        	<ul id="entries-filters" class="vfb-export-filters">
        		<li><p class="description"><?php _e( 'This will export entries in either .csv, .txt, or .xls and cannot be used with the Import.  If you need to import entries on another site, please use the All data option above.', 'visual-form-builder-pro' ); ?></p></li>
        		<!-- Format -->
        		<li>
        			<label class="vfb-export-label" for="format"><?php _e( 'Format', 'visual-form-builder-pro' ); ?>:</label>
        			<select name="format">
        				<option value="csv" selected="selected"><?php _e( 'Comma Separated (.csv)', 'visual-form-builder-pro' ); ?></option>
        				<option value="txt"><?php _e( 'Tab Delimited (.txt)', 'visual-form-builder-pro' ); ?></option>
        				<option value="xls"><?php _e( 'Excel (.xls)', 'visual-form-builder-pro' ); ?></option>
        			</select>
        		</li>
        		<!-- Forms -->
        		<li>
		        	<label class="vfb-export-label" for="form_id"><?php _e( 'Form', 'visual-form-builder-pro' ); ?>:</label>
		            <select id="vfb-export-entries-forms" name="entries_form_id">
<?php
						foreach ( $forms as $form ) :
							echo sprintf(
								'<option value="%1$d" id="%2$s">%1$d - %3$s</option>',
								$form->form_id,
								$form->form_key,
								stripslashes( $form->form_title )
							);
						endforeach;
?>
					</select>
        		</li>
        		<!-- Date Range -->
        		<li>
        			<label class="vfb-export-label"><?php _e( 'Date Range', 'visual-form-builder-pro' ); ?>:</label>
        			<select name="entries_start_date">
        				<option value="0">Start Date</option>
        				<?php $this->months_dropdown(); ?>
        			</select>
        			<select name="entries_end_date">
        				<option value="0">End Date</option>
        				<?php $this->months_dropdown(); ?>
        			</select>
        			<?php _e( 'or', 'visual-form-builder-pro' ); ?>
        			<select name="entries_date_period">
        				<option value="0">Time Period</option>
        				<?php $this->time_periods(); ?>
        			</select>
        		</li>
				<!-- Pages to Export -->
				<?php $num_pages = ceil( $entries_count / 1000 ); ?>
				<li id="vfb-export-entries-pages" style="display:<?php echo ( $entries_count > 1000 ) ? 'list-item' : 'none'; ?>">
					<label class="vfb-export-label"><?php _e( 'Page to Export', 'visual-form-builder-pro' ); ?>:</label>
					<select id="vfb-export-entries-rows" name="entries_page">
<?php
					for ( $i = 1; $i <= $num_pages; $i++ ) {
						echo sprintf( '<option value="%1$d">%1$s</option>', $i );
					}
?>
					</select>
					<p class="description"><?php _e( 'A large number of entries have been detected for this form. Only 1000 entries can be exported at a time.', 'visual-form-builder-pro' ); ?></p>
				</li>
        		<!-- Fields -->
        		<li>
        			<label class="vfb-export-label"><?php _e( 'Fields', 'visual-form-builder-pro' ); ?>:</label>

        			<p>
        				<a id="vfb-export-select-all" href="#"><?php _e( 'Select All', 'visual-form-builder-pro' ); ?></a>
        				<a id="vfb-export-unselect-all" href="#"><?php _e( 'Unselect All', 'visual-form-builder-pro' ); ?></a>
        			</p>

        			<div id="vfb-export-entries-fields">
	        		<?php
						if ( isset( $no_entries ) )
							echo $no_entries;
						else
							echo $this->build_options( $data );
					 ?>
        			</div>
        		</li>
        	</ul>

        <?php submit_button( __( 'Download Export File', 'visual-form-builder-pro' ) ); ?>
        </form>
<?php
	}

	/**
	 * Export entire form database or just form data as an XML
	 *
	 * @since 1.7
	 *
	 * @param array $args Filters defining what should be included in the export
	 */
	public function export( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'content' 		=> 'all',
			'form_id' 		=> 0,
			'start_date'	=> false,
			'end_date' 		=> false,
		);
		$args = wp_parse_args( $args, $defaults );

		$where = '';

		$form_id = ( 0 !== $args['form_id'] ) ? $args['form_id'] : null;

		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty($sitename) ) $sitename .= '.';
		$filename = $sitename . 'vfb-pro-' . $args['content'] . '.' . date( 'Y-m-d' ) . '.xml';

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

		echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";

		// Output the correct generator type
		the_generator( 'export' );
		?>
<!-- This is a Visual Form Builder Pro RSS file generated by WordPress as an export of your forms and/or data. -->
<!-- It contains information about forms, fields, entries, and email design settings from Visual Form Builder Pro. -->
<!-- You may use this file to transfer that content from one site to another. -->

<!-- To import this information into a WordPress site follow these steps: -->
<!-- 1. Log in to that site as an administrator. -->
<!-- 2. Go to Visual Form Builder Pro: Import in the WordPress admin panel. -->
<!-- 3. Select and Upload this file using the form provided on that page. -->
<!-- 4. Visual Form Builder Pro will then import each of the forms, fields, entries, and email design settings -->
<!--    contained in this file into your site. -->
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:vfb="http://matthewmuro.com/export/1.9/"
>
<channel>
	<title><?php bloginfo_rss( 'name' ); ?></title>
	<link><?php bloginfo_rss( 'url' ); ?></link>
	<description><?php bloginfo_rss( 'description' ); ?></description>
	<pubDate><?php echo date( 'D, d M Y H:i:s +0000' ); ?></pubDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<vfb:export_version><?php echo $this->export_version; ?></vfb:export_version>
<?php
		do_action( 'rss2_head' );

		// Forms
		if ( in_array( $args['content'], array( 'all', 'forms' ) ) ) :

			$form_ids = $this->get_form_IDs( $form_id );

			if ( $form_ids ) :
				// fetch 20 forms at a time rather than loading the entire table into memory
				while ( $next_forms = array_splice( $form_ids, 0, 20 ) ) :
					$where = 'WHERE form_id IN (' . join( ',', $next_forms ) . ')';
					$forms = $wpdb->get_results( "SELECT * FROM {$this->form_table_name} $where" );

					foreach ( $forms as $form ) :
?>
<vfb:form>
	<vfb:form_id><?php echo $form->form_id; ?></vfb:form_id>
	<vfb:form_key><?php echo $form->form_key; ?></vfb:form_key>
	<vfb:form_title><?php echo $this->cdata( $form->form_title ); ?></vfb:form_title>
	<vfb:form_email_subject><?php echo $this->cdata( $form->form_email_subject ); ?></vfb:form_email_subject>
	<vfb:form_email_to><?php echo $this->cdata( $form->form_email_to ); ?></vfb:form_email_to>
	<vfb:form_email_from><?php echo $this->cdata( $form->form_email_from ); ?></vfb:form_email_from>
	<vfb:form_email_from_name><?php echo $this->cdata( $form->form_email_from_name ); ?></vfb:form_email_from_name>
	<vfb:form_email_from_override><?php echo $form->form_email_from_override; ?></vfb:form_email_from_override>
	<vfb:form_email_from_name_override><?php echo $form->form_email_from_name_override; ?></vfb:form_email_from_name_override>
	<vfb:form_email_rule_setting><?php echo $form->form_email_rule_setting; ?></vfb:form_email_rule_setting>
	<vfb:form_email_rule><?php echo $this->cdata( $form->form_email_rule ); ?></vfb:form_email_rule>
	<vfb:form_success_type><?php echo $form->form_success_type; ?></vfb:form_success_type>
	<vfb:form_success_message><?php echo $this->cdata( $form->form_success_message ); ?></vfb:form_success_message>
	<vfb:form_notification_setting><?php echo $form->form_notification_setting; ?></vfb:form_notification_setting>
	<vfb:form_notification_email_name><?php echo $this->cdata( $form->form_notification_email_name ); ?></vfb:form_notification_email_name>
	<vfb:form_notification_email_from><?php echo $this->cdata( $form->form_notification_email_from ); ?></vfb:form_notification_email_from>
	<vfb:form_notification_email><?php echo $form->form_notification_email; ?></vfb:form_notification_email>
	<vfb:form_notification_subject><?php echo $this->cdata( $form->form_notification_subject ); ?></vfb:form_notification_subject>
	<vfb:form_notification_message><?php echo $this->cdata( $form->form_notification_message ); ?></vfb:form_notification_message>
	<vfb:form_notification_entry><?php echo $form->form_notification_entry; ?></vfb:form_notification_entry>
	<vfb:form_email_design><?php echo $this->cdata( $form->form_email_design ); ?></vfb:form_email_design>
	<vfb:form_paypal_setting><?php echo $form->form_paypal_setting; ?></vfb:form_paypal_setting>
	<vfb:form_paypal_email><?php echo $this->cdata( $form->form_paypal_email ); ?></vfb:form_paypal_email>
	<vfb:form_paypal_currency><?php echo $form->form_paypal_currency; ?></vfb:form_paypal_currency>
	<vfb:form_paypal_shipping><?php echo $form->form_paypal_shipping; ?></vfb:form_paypal_shipping>
	<vfb:form_paypal_tax><?php echo $form->form_paypal_tax; ?></vfb:form_paypal_tax>
	<vfb:form_paypal_field_price><?php echo $this->cdata( $form->form_paypal_field_price ); ?></vfb:form_paypal_field_price>
	<vfb:form_paypal_item_name><?php echo $this->cdata( $form->form_paypal_item_name ); ?></vfb:form_paypal_item_name>
	<vfb:form_label_alignment><?php echo $form->form_label_alignment; ?></vfb:form_label_alignment>
	<vfb:form_verification><?php echo $form->form_verification; ?></vfb:form_verification>
	<vfb:form_entries_allowed><?php echo $form->form_entries_allowed; ?></vfb:form_entries_allowed>
	<vfb:form_entries_schedule><?php echo $this->cdata( $form->form_entries_schedule ); ?></vfb:form_entries_schedule>
	<vfb:form_unique_entry><?php echo $form->form_unique_entry; ?></vfb:form_unique_entry>
	<vfb:form_status><?php echo $form->form_status; ?></vfb:form_status>
</vfb:form>
<?php
					endforeach;
				endwhile;
			endif;
		endif;

		// Fields
		if ( in_array( $args['content'], array( 'all', 'forms' ) ) ) :

			$field_ids = $this->get_field_IDs( $form_id );

			if ( $field_ids ) :
				// fetch 20 entries at a time rather than loading the entire table into memory
				while ( $next_fields = array_splice( $field_ids, 0, 20 ) ) :
					$where = 'WHERE field_id IN (' . join( ',', $next_fields ) . ')';
					$fields = $wpdb->get_results( "SELECT * FROM {$this->field_table_name} $where" );

					foreach ( $fields as $field ) :
?>
<vfb:field>
	<vfb:field_id><?php echo $field->field_id; ?></vfb:field_id>
	<vfb:form_id><?php echo $field->form_id; ?></vfb:form_id>
	<vfb:field_key><?php echo $field->field_key; ?></vfb:field_key>
	<vfb:field_type><?php echo $field->field_type; ?></vfb:field_type>
	<vfb:field_options><?php echo $this->cdata( $field->field_options ); ?></vfb:field_options>
	<vfb:field_options_other><?php echo $this->cdata( $field->field_options_other ); ?></vfb:field_options_other>
	<vfb:field_description><?php echo $this->cdata( $field->field_description ); ?></vfb:field_description>
	<vfb:field_name><?php echo $this->cdata( $field->field_name ); ?></vfb:field_name>
	<vfb:field_sequence><?php echo $field->field_sequence; ?></vfb:field_sequence>
	<vfb:field_parent><?php echo $field->field_parent; ?></vfb:field_parent>
	<vfb:field_required><?php echo $field->field_required; ?></vfb:field_required>
	<vfb:field_validation><?php echo $field->field_validation; ?></vfb:field_validation>
	<vfb:field_size><?php echo $field->field_size; ?></vfb:field_size>
	<vfb:field_css><?php echo $field->field_css; ?></vfb:field_css>
	<vfb:field_layout><?php echo $field->field_layout; ?></vfb:field_layout>
	<vfb:field_default><?php echo $this->cdata( $field->field_default ); ?></vfb:field_default>
	<vfb:field_rule_setting><?php echo $field->field_rule_setting; ?></vfb:field_rule_setting>
	<vfb:field_rule><?php echo $this->cdata( $field->field_rule ); ?></vfb:field_rule>
</vfb:field>
<?php
					endforeach;
				endwhile;
			endif;
		endif;

		// Entries
		if ( in_array( $args['content'], array( 'all' ) ) ) :
			$entry_ids = $this->get_entry_IDs( $form_id );

			if ( $entry_ids ) :
				// fetch 20 entries at a time rather than loading the entire table into memory
				while ( $next_entries = array_splice( $entry_ids, 0, 20 ) ) :
					$where = 'WHERE entries_id IN (' . join( ',', $next_entries ) . ')';
					$entries = $wpdb->get_results( "SELECT * FROM {$this->entries_table_name} $where" );

					foreach ( $entries as $entry ) :
?>
<vfb:entry>
	<vfb:entries_id><?php echo $entry->entries_id; ?></vfb:entries_id>
	<vfb:form_id><?php echo $entry->form_id; ?></vfb:form_id>
	<vfb:user_id><?php echo $entry->user_id; ?></vfb:user_id>
	<vfb:data><![CDATA[<?php echo $entry->data; ?>]]></vfb:data>
	<vfb:subject><?php echo $this->cdata( $entry->subject ); ?></vfb:subject>
	<vfb:sender_name><?php echo $this->cdata( $entry->sender_name ); ?></vfb:sender_name>
	<vfb:sender_email><?php echo $this->cdata( $entry->sender_email ); ?></vfb:sender_email>
	<vfb:emails_to><?php echo $this->cdata( $entry->emails_to ); ?></vfb:emails_to>
	<vfb:date_submitted><?php echo $entry->date_submitted; ?></vfb:date_submitted>
	<vfb:ip_address><?php echo $entry->ip_address; ?></vfb:ip_address>
	<vfb:notes><?php echo $this->cdata( $entry->notes ); ?></vfb:notes>
	<vfb:akismet><?php echo $this->cdata( $entry->akismet ); ?></vfb:akismet>
	<vfb:entry_approved><?php echo $entry->entry_approved; ?></vfb:entry_approved>
</vfb:entry>
<?php
					endforeach;
				endwhile;
			endif;
		endif;

		// Form Designer add-on
		if ( in_array( $args['content'], array( 'all', 'form_design' ) ) ) :
			$form_design_ids = $this->get_form_design_IDs( $form_id );

			if ( $form_design_ids ) :
				// fetch 20 entries at a time rather than loading the entire table into memory
				while ( $next_designs = array_splice( $form_design_ids, 0, 20 ) ) :
					$where = 'WHERE design_id IN (' . join( ',', $next_designs ) . ')';
					$designs = $wpdb->get_results( "SELECT * FROM {$this->design_table_name} $where" );

					foreach ( $designs as $design ) :
?>
<vfb:form_design>
	<vfb:design_id><?php echo $design->design_id; ?></vfb:design_id>
	<vfb:form_id><?php echo $design->form_id; ?></vfb:form_id>
	<vfb:enable_design><?php echo $design->enable_design; ?></vfb:enable_design>
	<vfb:design_type><?php echo $design->design_type; ?></vfb:design_type>
	<vfb:design_themes><?php echo $design->design_themes; ?></vfb:design_themes>
	<vfb:design_custom><?php echo $this->cdata( $design->design_custom ); ?></vfb:design_custom>
</vfb:form_design>
<?php
					endforeach;
				endwhile;
			endif;
		endif;

		// Payments add-on
		if ( in_array( $args['content'], array( 'all', 'payments' ) ) ) :
			$payments_ids = $this->get_payments_IDs( $form_id );

			if ( $payments_ids ) :
				// fetch 20 entries at a time rather than loading the entire table into memory
				while ( $next_payments = array_splice( $payments_ids, 0, 20 ) ) :
					$where = 'WHERE payment_id IN (' . join( ',', $next_payments ) . ')';
					$payments = $wpdb->get_results( "SELECT * FROM {$this->payment_table_name} $where" );

					foreach ( $payments as $payment ) :
?>
<vfb:payment>
	<vfb:payment_id><?php echo $payment->payment_id; ?></vfb:payment_id>
	<vfb:form_id><?php echo $payment->form_id; ?></vfb:form_id>
	<vfb:enable_payment><?php echo $payment->enable_payment; ?></vfb:enable_payment>
	<vfb:merchant_type><?php echo $payment->merchant_type; ?></vfb:merchant_type>
	<vfb:merchant_details><?php echo $this->cdata( $payment->merchant_details ); ?></vfb:merchant_details>
	<vfb:currency><?php echo $payment->currency; ?></vfb:currency>
	<vfb:show_running_total><?php echo $payment->show_running_total; ?></vfb:show_running_total>
	<vfb:collect_shipping_address><?php echo $payment->collect_shipping_address; ?></vfb:collect_shipping_address>
	<vfb:collect_billing_info><?php echo $this->cdata( $payment->collect_billing_info ); ?></vfb:collect_billing_info>
	<vfb:recurring_payments><?php echo $this->cdata( $payment->recurring_payments ); ?></vfb:recurring_payments>
	<vfb:advanced_vars><?php echo $this->cdata( $payment->advanced_vars ); ?></vfb:advanced_vars>
	<vfb:price_fields><?php echo $this->cdata( $payment->price_fields ); ?></vfb:price_fields>
</vfb:payment>
<?php
					endforeach;
				endwhile;
			endif;
		endif;
?>
</channel>
</rss>
		<?php
	}

	/**
	 * Build the entries export array
	 *
	 * @since 1.7
	 *
	 * @param array $args Filters defining what should be included in the export
	 */
	public function export_entries( $args = array() ) {
		global $wpdb;

		// Set inital fields as a string
		$initial_fields = implode( ',', $this->default_cols );

		$defaults = array(
			'content' 		=> 'entries',
			'format' 		=> 'csv',
			'form_id' 		=> 0,
			'start_date' 	=> false,
			'end_date' 		=> false,
			'period'		=> false,
			'page'			=> 0,
			'fields'		=> $initial_fields,
		);
		$args = wp_parse_args( $args, $defaults );

		$where = '';

		$limit = '0,1000';

		if ( 'entries' == $args['content'] ) {
			if ( 0 !== $args['form_id'] )
				$where .= $wpdb->prepare( " AND form_id = %d", $args['form_id'] );

			if ( $args['period'] ) {

				switch ( $args['period'] ) :
					case 'today' :
						$where .= " AND DATE( date_submitted ) = CURDATE()" ;
						break;

					case 'yesterday' :
						$where .= " AND DATE( date_submitted ) = CURDATE() - INTERVAL 1 DAY" ;
						break;

					case 'week' :
						$where .= " AND YEARWEEK( date_submitted ) = YEARWEEK( CURDATE() )" ;
						break;

					case 'week-last' :
						$where .= " AND YEARWEEK( date_submitted ) = YEARWEEK( CURDATE() - INTERVAL 7 DAY )" ;
						break;

					case 'month' :
						$where .= " AND MONTH( date_submitted ) = MONTH( CURDATE() )" ;
						break;

					case 'month-last' :
						$where .= " AND MONTH( date_submitted ) = MONTH( CURDATE() - INTERVAL 1 MONTH )" ;
						break;

				endswitch;

			} else {
				if ( $args['start_date'] )
					$where .= $wpdb->prepare( " AND date_submitted >= %s", date( 'Y-m-d', strtotime( $args['start_date'] ) ) );

				if ( $args['end_date'] )
					$where .= $wpdb->prepare( " AND date_submitted < %s", date( 'Y-m-d', strtotime( '+1 month', strtotime( $args['end_date'] ) ) ) );
			}

			if ( $args['page'] > 1 )
				$limit = ( $args['page'] - 1 ) * 1000 . ',1000';
		}

		$form_id = ( 0 !== $args['form_id'] ) ? $args['form_id'] : null;

		$entries      = $wpdb->get_results( "SELECT * FROM $this->entries_table_name WHERE entry_approved = 1 $where ORDER BY entries_id ASC LIMIT $limit" );
		$form_key     = $wpdb->get_var( $wpdb->prepare( "SELECT form_key, form_title FROM $this->form_table_name WHERE form_id = %d", $args['form_id'] ) );
		$form_title   = $wpdb->get_var( null, 1 );

		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty($sitename) ) $sitename .= '.';
		$filename = $sitename . 'vfb-pro.' . "$form_key." . date( 'Y-m-d' ) . ".{$args['format']}";

		// Set content type based on file format
		switch ( $args['format'] ) {
			case 'csv' :
				$content_type = 'text/csv';
			break;
			case 'txt' :
				$content_type = 'text/plain';
			break;
			case 'xls' :
				$content_type = 'application/vnd.ms-excel';
			break;
		}

		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( "Content-Type: $content_type; charset=" . get_option( 'blog_charset' ), true );
		header( 'Expires: 0' );
		header( 'Pragma: public' );

		// Get columns
		$columns = $this->get_cols( $entries );

		// Get JSON data
		$data = json_decode( $columns, true );

		// Build array of fields to display
		$fields = !is_array( $args['fields'] ) ? array_map( 'trim', explode( ',', $args['fields'] ) ) : $args['fields'];

		// Strip slashes from header values
		$fields = array_map( 'stripslashes', $fields );

		if ( in_array( $args['format'], array( 'csv', 'txt' ) ) )
			$this->csv_tab( $data, $fields, $args['format'] );
		elseif ( 'xls' == $args['format'] )
			$this->xls( $data, $fields, $form_title );
	}

	/**
	 * Build the entries as JSON
	 *
	 * @since 1.7
	 *
	 * @param array $entries The resulting database query for entries
	 */
	public function get_cols( $entries ) {

		// Initialize row index at 0
		$row = 0;
		$output = array();

		// Loop through all entries
		foreach ( $entries as $entry ) :

			foreach ( $entry as $key => $value ) :

				switch ( $key ) {
					case 'entries_id':
					case 'date_submitted':
					case 'ip_address':
					case 'subject':
					case 'sender_name':
					case 'sender_email':
						$output[ $row ][ stripslashes( $this->default_cols[ $key ] ) ] = $value;
					break;

					case 'emails_to':
						$output[ $row ][ stripslashes( $this->default_cols[ $key ] ) ] = implode( ',', maybe_unserialize( $value ) );
					break;

					case 'data':
						// Unserialize value only if it was serialized
						$fields = maybe_unserialize( $value );

						// Make sure there are no errors with unserializing before proceeding
						if ( is_array( $fields ) ) {
							// Loop through our submitted data
							foreach ( $fields as $field_key => $field_value ) :
								// Cast each array as an object
								$obj = (object) $field_value;

								// Decode the values so HTML tags can be stripped
								$val = wp_specialchars_decode( $obj->value, ENT_QUOTES );

								switch ( $obj->type ) {
									case 'fieldset' :
									case 'section' :
									case 'instructions' :
									case 'page-break' :
									case 'verification' :
									case 'secret' :
									case 'submit' :
										break;

									case 'address' :

										$val = str_replace( array( '<p>', '</p>', '<br>' ), array( '', "\n", "\n" ), $val );

										$output[ $row ][ stripslashes( $obj->name ) . "{{{$obj->id}}}" ] =  $val;

										break;

									case 'html' :

										$output[ $row ][ stripslashes( $obj->name ) . "{{{$obj->id}}}" ] =  $val;

										break;

									default :

										$val = wp_strip_all_tags( $val );
										$output[ $row ][ stripslashes( $obj->name ) . "{{{$obj->id}}}" ] =  $val;

										break;
								} //end $obj switch
							endforeach; // end $fields loop
						}
					break;
				} //end $key switch
			endforeach; // end $entry loop
			$row++;
		endforeach; //end $entries loop

		return json_encode( $output );
	}

	public function count_entries( $form_id ) {
		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $this->entries_table_name WHERE form_id = %d", $form_id ) );

		if ( !$count )
			return 0;

		return $count;
	}

	public function get_form_IDs( $form_id = null ) {
		global $wpdb;

		$where = '';

		if ( $form_id )
			$where .= $wpdb->prepare( " AND form_id = %d", $form_id );

		$form_ids = $wpdb->get_col( "SELECT DISTINCT form_id FROM $this->form_table_name WHERE 1=1 $where" );

		if ( !$form_ids )
			return;

		return $form_ids;
	}

	public function get_field_IDs( $form_id = null ) {
		global $wpdb;

		$where = '';

		if ( $form_id )
			$where .= $wpdb->prepare( " AND form_id = %d", $form_id );

		$field_ids = $wpdb->get_col( "SELECT DISTINCT field_id FROM $this->field_table_name WHERE 1=1 $where" );

		if ( !$field_ids )
			return;

		return $field_ids;
	}

	public function get_entry_IDs( $form_id = null ) {
		global $wpdb;

		$where = '';

		if ( $form_id ) :
			$where .= $wpdb->prepare( " AND form_id = %d", $form_id );

			$count = $this->count_entries( $form_id );
			$where .= " LIMIT $count";
		endif;



		$entry_ids = $wpdb->get_col( "SELECT DISTINCT entries_id FROM $this->entries_table_name WHERE entry_approved = 1 $where" );

		if ( !$entry_ids )
			return;

		return $entry_ids;
	}

	public function get_form_design_IDs( $form_id = null ) {
		global $wpdb;

		$where = '';

		if ( $form_id )
			$where .= $wpdb->prepare( " AND form_id = %d", $form_id );

		$design_ids = $wpdb->get_col( "SELECT DISTINCT design_id FROM {$this->design_table_name} WHERE 1=1 $where" );

		if ( !$design_ids )
			return;

		return $design_ids;
	}

	public function get_payments_IDs( $form_id = null ) {
		global $wpdb;

		$where = '';

		if ( $form_id )
			$where .= $wpdb->prepare( " AND form_id = %d", $form_id );

		$payments_ids = $wpdb->get_col( "SELECT DISTINCT payment_id FROM {$this->payment_table_name} WHERE 1=1 $where" );

		if ( !$payments_ids )
			return;

		return $payments_ids;
	}

	/**
	 * Return the entries data formatted for CSV
	 *
	 * @since 1.7
	 *
	 * @param array $cols The multidimensional array of entries data
	 * @param int $row The row index
	 */
	public function csv_tab( $data, $fields, $format ) {

		// Override delimiter if tab separated
		if ( 'txt' == $format )
			$this->delimiter = "\t";

		// Open file with PHP wrapper
		$fh = @fopen( 'php://output', 'w' );

		$rows = $fields_clean = $fields_header = array();

		// Decode special characters
		foreach ( $fields as $field ) :
			// Strip unique ID for a clean header
			$search = preg_replace( '/{{(\d+)}}/', '', $field );
			$fields_header[] = wp_specialchars_decode( $search, ENT_QUOTES );

			// Field with unique ID to use as matching data
			$fields_clean[] = wp_specialchars_decode( $field, ENT_QUOTES );
		endforeach;

		// Build headers
		fputcsv( $fh, $fields_header, $this->delimiter );

		// Build table rows and cells
		foreach ( $data as $row ) :

			foreach ( $fields_clean as $label ) {
				$label = wp_specialchars_decode( $label );
				$rows[ $label ] =  ( isset( $row[ $label ] ) && in_array( $label, $fields_clean ) ) ? $row[ $label ] : '';
			}

			fputcsv( $fh, $rows, $this->delimiter );

		endforeach;

		// Close the file
		fclose( $fh );

		exit();
	}

	/**
	 * Return the entries data formatted for MS Excel (XLS)
	 *
	 * @since 1.7
	 *
	 * @param array $cols The multidimensional array of entries data
	 * @param int $row The row index
	 * @param string $form_title The form title, inserted into the Excel Worksheet tab
	 */
	public function xls( $data, $fields, $form_title ) {
		// Strip out illegal characters and truncate at 31 characters
		$title = preg_replace ( '/[\\\|:|\/|\?|\*|\[|\]]/', '', $form_title );
        $title = substr ( $title, 0, 31 );

        $rows = $fields_clean = $fields_header = array();

		foreach ( $fields as $field ) :
			// Strip unique ID for a clean header
			$search = preg_replace( '/{{(\d+)}}/', '', $field );
			$fields_header[] = $search;

			// Field with unique ID to use as matching data
			$fields_clean[] = wp_specialchars_decode( $field, ENT_QUOTES );
		endforeach;

		echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
	?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
	<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
		<AllowPNG />
	</OfficeDocumentSettings>
	<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
		<WindowHeight>15020</WindowHeight>
		<WindowWidth>25360</WindowWidth>
		<WindowTopX>240</WindowTopX>
		<WindowTopY>240</WindowTopY>
		<Date1904 />
		<ProtectStructure>False</ProtectStructure>
		<ProtectWindows>False</ProtectWindows>
	</ExcelWorkbook>
	<Styles>
		<Style ss:ID="Default" ss:Name="Normal">
			<Alignment ss:Vertical="Bottom"/>
			<Borders/>
			<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#000000"/>
			<Interior/>
			<NumberFormat/>
			<Protection/>
		</Style>
		<Style ss:ID="s62">
			<NumberFormat ss:Format="General Date"/>
		</Style>
		<Style ss:ID="s72">
			<Borders>
				<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
			</Borders>
			<Font ss:FontName="Calibri" ss:Size="14" ss:Color="#333333" ss:Bold="1"/>
			<Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
		</Style>
	</Styles>

	<Worksheet ss:Name="Sheet1">
		<Table x:FullColumns="1" x:FullRows="1" ss:DefaultColumnWidth="65" ss:DefaultRowHeight="15">
			<Row ss:AutoFitHeight="0" ss:StyleID="s72">
			<?php foreach ( $fields_header as $field ) : ?>
				<Cell><Data ss:Type="String"><?php echo wp_specialchars_decode( esc_html( $field ), ENT_QUOTES ); ?></Data></Cell>
			<?php endforeach; ?>
			</Row>
		<?php
		// Build table rows and cells
		foreach ( $data as $row ) :
			echo '<Row ss:AutoFitHeight="0">';

			foreach ( $fields_clean as $label ) {
				$label = wp_specialchars_decode( $label );

				if ( isset( $row[ $label ] ) && in_array( $label, $fields_clean ) ) {
					$type = 'String';
					$item = $row[ $label ];
					$style = '';
					$timestamp = strtotime( $item );

					if( preg_match( "/^-?\d+(?:[.,]\d+)?$/", $item ) && ( strlen( $item ) < 15 ) )
						$type = 'Number';
					else if ( preg_match( "/^(\d{1,2}|\d{4})[\/\-]\d{1,2}[\/\-](\d{1,2}|\d{4})([^\d].+)?$/", $item ) && $timestamp > 0 && $timestamp < strtotime( '+500 years' ) ) {
						$type    = 'DateTime';
						$item    = strftime( '%Y-%m-%dT%H:%M:%S', $timestamp );
						$style   = ' ss:StyleID="s62"';
					}

					echo sprintf( '<Cell%3$s><Data ss:Type="%2$s">%1$s</Data></Cell>', esc_html( $item ), $type, $style );
				}
				else
					echo '<Cell><Data ss:Type="String"></Data></Cell>';
			}

			echo '</Row>';

		endforeach;
		?>
		</Table>
		<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
			<Unsynced/>
			<PageLayoutZoom>0</PageLayoutZoom>
			<Selected/>
			<Panes>
				<Pane>
					<Number>3</Number>
					<ActiveRow>3</ActiveRow>
					<ActiveCol>3</ActiveCol>
				</Pane>
			</Panes>
			<ProtectObjects>False</ProtectObjects>
			<ProtectScenarios>False</ProtectScenarios>
		</WorksheetOptions>
	</Worksheet>
</Workbook>
	<?php
		exit();
	}

	/**
	 * Return the selected export type
	 *
	 * @since 1.7
	 *
	 * @return string|bool The type of export
	 */
	public function export_action() {
		if ( isset( $_REQUEST['vfb-content'] ) )
			return $_REQUEST['vfb-content'];

		return false;
	}

	/**
	 * Build the checkboxes when changing forms
	 *
	 * @since 2.6.8
	 *
	 * @return string Either no entries or the entry headers
	 */
	public function ajax_load_options() {
		global $wpdb, $export;

		if ( !isset( $_REQUEST['action'] ) )
			return;

		if ( $_REQUEST['action'] !== 'visual_form_builder_export_load_options' )
			return;

		$form_id = absint( $_REQUEST['id'] );

		// Safe to get entries now
		$entry_ids = $this->get_entry_IDs( $form_id );

		// Return nothing if no entries found
		if ( !$entry_ids ) {
			echo __( 'No entries to pull field names from.', 'visual-form-builder-pro' );
			wp_die();
		}

		$offset = '';
		$limit = 1000;

		if ( isset( $_REQUEST['count'] ) )
			$limit = ( $_REQUEST['count'] < 1000 ) ? absint( $_REQUEST['count'] ) : 1000;
		elseif ( isset( $_REQUEST['offset'] ) ) {
			// Reset offset/page to a zero index
			$offset = absint( $_REQUEST['offset'] ) - 1;

			// Calculate the offset
			$offset_num = $offset * 1000;

			// If page is 2 or greater, set the offset (page 2 is equal to offset 1 because of zero index)
			$offset = $offset >= 1 ? "OFFSET $offset_num" : '';
		}

		$entries = $wpdb->get_results( "SELECT data FROM {$this->entries_table_name} WHERE form_id = $form_id AND entry_approved = 1 LIMIT $limit $offset", ARRAY_A );

		// Get columns
		$columns = $export->get_cols( $entries );

		// Get JSON data
		$data = json_decode( $columns, true );

		echo $this->build_options( $data );

		wp_die();
	}

	public function ajax_entries_count() {
		global $wpdb, $export;

		if ( !isset( $_REQUEST['action'] ) )
			return;

		if ( $_REQUEST['action'] !== 'visual_form_builder_export_entries_count' )
			return;

		$form_id = absint( $_REQUEST['id'] );

		echo $export->count_entries( $form_id );

		wp_die();
	}

	public function build_options( $data ) {

		$output = '';

		$array = array();
		foreach ( $data as $row ) :
			$array = array_merge( $row, $array );
		endforeach;

		$array = array_keys( $array );
		$array = array_values( array_merge( $this->default_cols, $array ) );
		$array = array_map( 'stripslashes', $array );

		foreach ( $array as $k => $v ) :
			$selected = ( in_array( $v, $this->default_cols ) ) ? ' checked="checked"' : '';

			// Strip unique ID for a clean list
			$search = preg_replace( '/{{(\d+)}}/', '', $v );

			$output .= sprintf( '<label for="vfb-display-entries-val-%1$d"><input name="entries_columns[]" class="vfb-display-entries-vals" id="vfb-display-entries-val-%1$d" type="checkbox" value="%4$s" %3$s> %2$s</label><br>', $k, $search, $selected, esc_attr( $v ) );
		endforeach;

		return $output;
	}

	/**
	 * Determine which export process to run
	 *
	 * @since 1.7
	 *
	 */
	public function process_export_action() {

		$args = array();

		if ( ! isset( $_REQUEST['vfb-content'] ) || 'all' == $_REQUEST['vfb-content'] )
			$args['content'] = 'all';
		elseif ( 'forms' == $_REQUEST['vfb-content'] ) {
			$args['content'] = 'forms';

			if ( $_REQUEST['forms_form_id'] )
				$args['form_id'] = (int) $_REQUEST['forms_form_id'];
		}
		elseif ( 'entries' == $_REQUEST['vfb-content'] ) {
			$args['content'] = 'entries';

			if ( $_REQUEST['format'] )
				$args['format'] = (string) $_REQUEST['format'];

			if ( $_REQUEST['entries_form_id'] )
				$args['form_id'] = (int) $_REQUEST['entries_form_id'];

			if ( $_REQUEST['entries_date_period'] ) {
				$args['period'] = $_REQUEST['entries_date_period'];

			} elseif ( $_REQUEST['entries_start_date'] || $_REQUEST['entries_end_date'] ) {
				$args['start_date'] = $_REQUEST['entries_start_date'];
				$args['end_date'] = $_REQUEST['entries_end_date'];
			}

			if ( isset( $_REQUEST['entries_columns'] ) )
				$args['fields'] = array_map( 'esc_html',  $_REQUEST['entries_columns'] );

			if ( isset( $_REQUEST['entries_page'] ) )
				$args['page'] = absint( $_REQUEST['entries_page'] );
		}

		switch( $this->export_action() ) {
			case 'all' :
			case 'forms' :
				$this->export( $args );
				die(1);
			break;

			case 'entries' :
				$this->export_entries( $args );
				die(1);
			break;
		}
	}

	/**
	 * Wrap given string in XML CDATA tag.
	 *
	 * @since 1.7
	 *
	 * @param string $str String to wrap in XML CDATA tag.
	 * @return string
	 */
	function cdata( $str ) {
		if ( seems_utf8( $str ) == false )
			$str = utf8_encode( $str );

		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

	/**
	 * Display Year/Month filter
	 *
	 * @since 1.7
	 */
	public function months_dropdown() {
		global $wpdb, $wp_locale;

		$where = apply_filters( 'vfb_pre_get_entries', '' );

	    $months = $wpdb->get_results( "
			SELECT DISTINCT YEAR( forms.date_submitted ) AS year, MONTH( forms.date_submitted ) AS month
			FROM $this->entries_table_name AS forms
			WHERE 1=1 $where
			ORDER BY forms.date_submitted DESC
		" );

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		$m = isset( $_REQUEST['m'] ) ? (int) $_REQUEST['m'] : 0;

		foreach ( $months as $arc_row ) :
			if ( 0 == $arc_row->year )
				continue;

			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;

			printf( "<option value='%s'>%s</option>\n",
				esc_attr( $arc_row->year . '-' . $month ),
				sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
			);
		endforeach;
	}

	/**
	 * Output time periods for entires date range
	 *
	 * @access public
	 * @return void
	 */
	public function time_periods() {
		$entries = $this->get_entry_IDs();

		if ( !$entries )
			return;

		?>
		<option value="today">Today</option>
		<option value="yesterday">Yesterday</option>
		<option value="week">This Week</option>
		<option value="week-last">Last Week</option>
		<option value="month">This Month</option>
		<option value="month-last">Last Month</option>
		<?php
	}
}
