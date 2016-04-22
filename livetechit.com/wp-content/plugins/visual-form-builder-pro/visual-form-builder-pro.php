<?php
/*
Plugin Name: Visual Form Builder Pro
Plugin URI: http://vfbpro.com
Description: Dynamically build forms using a simple interface. Forms include jQuery validation, a basic logic-based verification system, and entry tracking.
Author: Matthew Muro
Author URI: http://matthewmuro.com
Version: 2.5.1
*/

// Version number to output as meta tag
define( 'VFB_PRO_VERSION', '2.5.1' );

/**
 * Template tag function
 *
 * @since 1.9
 * @echo class function VFB form code
 */
function vfb_pro( $args = '' ){
	global $visual_form_builder_pro;

	// Parse the arguments into an array
	$args = wp_parse_args( $args );

	// Sanitize and save form id
	$form_id = absint( $args['id'] );

	// Print the output
	echo $visual_form_builder_pro->form_code( $args );
}

// Add action so themes can call via do_action( 'vfb_pro', $id );
add_action( 'vfb_pro', 'vfb_pro' );

// Instantiate new class
$visual_form_builder_pro = new Visual_Form_Builder_Pro();

// Visual Form Builder class
class Visual_Form_Builder_Pro{

	/**
	 * The DB version. Used for SQL install and upgrades.
	 *
	 * Should only be changed when needing to change SQL
	 * structure or custom capabilities.
	 *
	 * @since 1.0
	 * @var string
	 * @access protected
	 */
	protected $vfb_db_version = '2.6';

	/**
	 * The plugin API
	 *
	 * @since 1.0
	 * @var string
	 * @access protected
	 */
	protected $api_url = 'http://classic.vfbpro.com/plugin-api/';

	/**
	 * Flag used to add scripts to front-end only once
	 *
	 * @since 1.0
	 * @var string
	 * @access protected
	 */
	protected $add_scripts = false;

	/**
	 * An array of countries to be used throughout plugin
	 *
	 * @since 1.0
	 * @var array
	 * @access public
	 */
	public $countries = array( "", "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China", "Colombi", "Comoros", "Congo (Brazzaville)", "Congo", "Costa Rica", "Cote d\'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor (Timor Timur)", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia, The", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepa", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe" );

	/**
	 * Newly created entry ID (used during confirmation)
	 *
	 * @since 2.2.5
	 * @var int
	 * @access public
	 */
	public $new_entry_id = 0;

	/**
	 * Admin page menu hooks
	 *
	 * @since 2.2.6
	 * @var array
	 * @access private
	 */
	private $_admin_pages = array();

	/**
	 * Flag used to display post_max_vars error when saving
	 *
	 * @since 1.0
	 * @var string
	 * @access protected
	 */
	protected $post_max_vars = false;

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
	 * Constructor. Register core filters and actions.
	 *
	 * @access public
	 */
	public function __construct(){
		global $wpdb;

		// Setup global database table names
		$this->field_table_name 	= $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name 		= $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name 	= $wpdb->prefix . 'vfb_pro_entries';

		// Add suffix to load dev files
		$this->load_dev_files = ( defined( 'VFB_SCRIPT_DEBUG' ) && VFB_SCRIPT_DEBUG ) ? '' : '.min';

		// Build options and settings pages.
		add_action( 'admin_menu', array( &$this, 'add_admin' ) );

		// Saving functions
		add_action( 'admin_init', array( &$this, 'save_add_new_form' ) );
		add_action( 'admin_init', array( &$this, 'save_update_form' ) );
		add_action( 'admin_init', array( &$this, 'save_copy_form' ) );
		add_action( 'admin_init', array( &$this, 'save_email_design' ) );
		add_action( 'admin_init', array( &$this, 'save_email_delete_header' ) );
		add_action( 'admin_init', array( &$this, 'save_upgrade' ) );
		add_action( 'admin_init', array( &$this, 'save_entry_update' ) );
		add_action( 'admin_init', array( &$this, 'save_settings' ) );

		// Extra code to run at a later time than init
		add_action( 'admin_menu', array( &$this, 'additional_plugin_setup' ) );

		// Saves form order type (ordered or list mode)
		add_action( 'admin_init', array( &$this, 'form_order_type' ) );

		// Register AJAX functions
		$actions = array(
			// Form Builder
			'sort_field',
			'create_field',
			'delete_field',
			'duplicate_field',
			'bulk_add',
			'conditional_fields',
			'conditional_fields_options',
			'conditional_fields_save',
			'paypal_price',
			'form_settings',
			'email_rules',
			'email_rules_save',

			// All Forms list
			'form_order',

			// Analytics
			'graphs',

			// Media button
			'media_button',
		);

		// Add all AJAX functions
		foreach( $actions as $name ) {
			add_action( "wp_ajax_visual_form_builder_$name", array( &$this, "ajax_$name" ) );
		}

		// Adds additional media button to insert form shortcode
		add_action( 'media_buttons', array( &$this, 'add_media_button' ), 999 );

		// Adds a Dashboard widget
		add_action( 'wp_dashboard_setup', array( &$this, 'add_dashboard_widget' ) );

		// Load the includes files
		add_action( 'plugins_loaded', array( &$this, 'includes' ) );

		// Save Entries per page screen option
		add_filter( 'set-screen-option', array( &$this, 'save_screen_options' ), 10, 3 );

		// Adds a Settings link to the Plugins page
		add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );

		// Check the db version and run SQL install, if needed
		add_action( 'plugins_loaded', array( &$this, 'update_db_check' ) );

		// Display plugin details screen for updating
		add_filter( 'plugins_api', array( &$this, 'api_information' ), 10, 3 );

		// Hook into the plugin update check
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'api_check' ) );

		// For testing only
		//add_action( 'init', array( &$this, 'delete_transient' ) );

		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		add_action( 'admin_notices', array( &$this, 'migration_check' ) );

		add_shortcode( 'vfb', array( &$this, 'form_code' ) );
		add_action( 'init', array( &$this, 'email' ), 10 );
		add_action( 'init', array( &$this, 'confirmation' ), 12 );
		add_action( 'admin_bar_menu', array( &$this, 'admin_toolbar_menu' ), 999 );

		// Add CSS to the front-end
		add_action( 'wp_enqueue_scripts', array( &$this, 'css' ) );

		// Load i18n
		add_action( 'plugins_loaded', array( &$this, 'languages' ) );

		// Print meta keyword
		add_action( 'wp_head', array( &$this, 'add_meta_keyword' ) );

		add_action( 'wp_ajax_visual_form_builder_autocomplete', array( &$this, 'ajax_autocomplete' ) );
		add_action( 'wp_ajax_nopriv_visual_form_builder_autocomplete', array( &$this, 'ajax_autocomplete' ) );
		add_action( 'wp_ajax_visual_form_builder_check_username', array( &$this, 'ajax_check_username' ) );
		add_action( 'wp_ajax_nopriv_visual_form_builder_check_username', array( &$this, 'ajax_check_username' ) );
		/*add_action( 'wp_ajax_visual_form_builder_check_recaptcha', array( &$this, 'ajax_check_recaptcha' ) );
		add_action( 'wp_ajax_nopriv_visual_form_builder_check_recaptcha', array( &$this, 'ajax_check_recaptcha' ) );*/
	}

	/**
	 * Allow for additional plugin code to be run during admin_init
	 * which is not available during the plugin __construct()
	 *
	 * @since 2.1
	 */
	public function additional_plugin_setup() {

		$page_main = $this->_admin_pages[ 'vfb-pro' ];

		// If first time editing, disable advanced items by default.
		if( false === get_user_option( 'manage' . $page_main . 'columnshidden' ) ) {
			$user = wp_get_current_user();
			update_user_option( $user->ID, 'manage' . $page_main . 'columnshidden', array( 0 => 'merge-tag' ), true );
		}

		if ( !get_option( 'vfb_dashboard_widget_options' ) ) {
			$widget_options['vfb_dashboard_recent_entries'] = array(
				'items' => 5,
			);
			update_option( 'vfb_dashboard_widget_options', $widget_options );
		}

	}

	/**
	 * Output plugin version number to help with troubleshooting
	 *
	 * @since 2.3.3
	 */
	public function add_meta_keyword() {
		// Get global settings
		$vfb_settings 	= get_option( 'vfb-settings' );

		// Settings - Disable meta tag version
		$settings_meta	= isset( $vfb_settings['show-version'] ) ? '' : '<!-- <meta name="vfbPro" version="'. VFB_PRO_VERSION . '" /> -->' . "\n";

		echo apply_filters( 'vfb_show_version', $settings_meta );
	}

	/**
	 * Load localization file
	 *
	 * @since 2.1.2
	 */
	public function languages() {
		load_plugin_textdomain( 'visual-form-builder-pro', false , 'visual-form-builder-pro/languages' );
	}

	/**
	 * Delete transients on page load
	 *
	 * FOR TESTING PURPOSES ONLY
	 *
	 * @since 1.0
	 */
	public function delete_transient() {
		delete_site_transient( 'update_plugins' );
	}

	/**
	 * Check the plugin versions to see if there's a new one
	 *
	 * @since 1.0
	 */
	public function api_check( $transient ) {

		// If no checked transiest, just return its value without hacking it
		if ( empty( $transient->checked ) )
			return $transient;

		// Append checked transient information
		$plugin_slug = plugin_basename( __FILE__ );

		// POST data to send to your API
		$args = array(
			'action' 		=> 'update-check',
			'plugin_name' 	=> $plugin_slug,
			'version' 		=> $transient->checked[ $plugin_slug ],
		);

		// Send request checking for an update
		$response = $this->api_request( $args );

		// If response is false, don't alter the transient
		if ( false !== $response )
			$transient->response[ $plugin_slug ] = $response;

		return $transient;
	}

	/**
	 * Send a request to the alternative API, return an object
	 *
	 * @since 1.0
	 */
	public function api_request( $args ) {

		// Send request
		$request = wp_remote_post( $this->api_url, array( 'body' => $args ) );

		// If request fails, stop
		if ( is_wp_error( $request ) ||	wp_remote_retrieve_response_code( $request ) != 200	)
			return false;

		// Retrieve and set response
		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );

		// Read server response, which should be an object
		if ( is_object( $response ) )
			return $response;
		else
			return false;
	}

	/**
	 * Return the plugin details for the plugin update screen
	 *
	 * @since 1.0
	 */
	public function api_information( $false, $action, $args ) {

		$plugin_slug = plugin_basename( __FILE__ );

		// Check if requesting info
		if ( !isset( $args->slug ) )
			return $false;

		// Check if this plugins API is about this plugin
		if ( isset( $args->slug ) && $args->slug != $plugin_slug )
			return $false;

		// POST data to send to your API
		$args = array(
			'action' 		=> 'plugin_information',
			'plugin_name' 	=> $plugin_slug,
		);

		// Send request for detailed information
		$response = $this->api_request( $args );

		// Send request checking for information
		$request = wp_remote_post( $this->api_url, array( 'body' => $args ) );

		return $response;
	}

	/**
	 * Adds extra include files
	 *
	 * @since 1.0
	 */
	public function includes(){
		// Load the Email Designer class
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-email-designer.php' );

		// Load the Analytics class
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-analytics.php' );
	}

	/**
	 * Include the Entries files later because current_screen isn't available yet
	 *
	 * @since 1.4
	 */
	public function include_entries(){
		global $entries_list, $entries_detail;

		// Load the Entries List class
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-entries-list.php' );
		$entries_list = new VisualFormBuilder_Pro_Entries_List();

		// Load the Entries Details class
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-entries-detail.php' );
		$entries_detail = new VisualFormBuilder_Pro_Entries_Detail();
	}

	public function include_forms_list() {
		global $forms_order, $forms_list;

		// Load the Forms Order class
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-forms-order.php' );
		$forms_order = new VisualFormBuilder_Pro_Forms_Order();

		// Load the Forms List class
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-forms-list.php' );
		$forms_list = new VisualFormBuilder_Pro_Forms_List();
	}

	/**
	 * Include the Import/Export files later because current_screen isn't available yet
	 *
	 * @since 1.4
	 */
	public function include_import_export(){
		global $import;

		// Load the Import class
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-import.php' );
		$import = new VisualFormBuilder_Pro_Import();
	}

	/**
	 * Add Settings link to Plugins page
	 *
	 * @since 1.8
	 * @return $links array Links to add to plugin name
	 */
	public function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( __FILE__ ) )
			$links[] = '<a href="admin.php?page=visual-form-builder-pro">' . __( 'Settings' , 'visual-form-builder-pro') . '</a>';

		return $links;
	}

	/**
	 * Adds the media button image
	 *
	 * @since 1.4
	 */
	public function add_media_button(){
		if ( current_user_can( 'vfb_view_entries' ) ) :
?>
			<a href="<?php echo add_query_arg( array( 'action' => 'visual_form_builder_media_button', 'width' => '450' ), admin_url( 'admin-ajax.php' ) ); ?>" class="button add_media thickbox" title="Add Visual Form Builder form">
				<span class="dashicons dashicons-feedback" style="color:#888; display: inline-block; width: 18px; height: 18px; vertical-align: text-top; margin: 0 4px 0 0;"></span>
				<?php _e( 'Add Form', 'visual-form-builder-pro' ); ?>
			</a>
<?php
		endif;
	}

	/**
	 * Adds the dashboard widget
	 *
	 * @since 2.2.1
	 */
	public function add_dashboard_widget() {
		if ( current_user_can( 'vfb_view_entries' ) || current_user_can( 'vfb_edit_entries' ) )
			wp_add_dashboard_widget( 'vfb-pro-dashboard', __( 'Recent Visual Form Builder Pro Entries', 'visual-form-builder-pro' ), array( &$this, 'dashboard_widget' ), array( &$this, 'dashboard_widget_control' ) );
	}

	/**
	 * Displays the dashboard widget content
	 *
	 * @since 2.2.1
	 */
	public function dashboard_widget() {
		global $wpdb;

		// Get the date/time format that is saved in the options table
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		$widgets = get_option( 'vfb_dashboard_widget_options' );
		$total_items = isset( $widgets['vfb_dashboard_recent_entries'] ) && isset( $widgets['vfb_dashboard_recent_entries']['items'] ) ? absint( $widgets['vfb_dashboard_recent_entries']['items'] ) : 5;

		$forms = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->form_table_name}" );

		if ( !$forms ) :
			echo sprintf(
				'<p>%1$s <a href="%2$s">%3$s</a></p>',
				__( 'You currently do not have any forms.', 'visual-form-builder-pro' ),
				esc_url( admin_url( 'admin.php?page=vfb-add-new' ) ),
				__( 'Get started!', 'visual-form-builder-pro' )
			);

			return;
		endif;

		$entries = $wpdb->get_results( $wpdb->prepare( "SELECT forms.form_title, entries.entries_id, entries.form_id, entries.sender_name, entries.sender_email, entries.date_submitted FROM $this->form_table_name AS forms INNER JOIN $this->entries_table_name AS entries ON entries.form_id = forms.form_id ORDER BY entries.date_submitted DESC LIMIT %d", $total_items ) );

		if ( current_user_can( 'vfb_edit_entries' ) )
			$action = 'edit';
		elseif ( current_user_can( 'vfb_view_entries' ) )
			$action = 'view';

		if ( !$entries ) :
			echo sprintf( '<p>%1$s</p>', __( 'You currently do not have any entries.', 'visual-form-builder-pro' ) );
		else :

			$content = '';

			foreach ( $entries as $entry ) :

				$content .= sprintf(
					'<li><a href="%1$s">%4$s</a> via <a href="%2$s">%5$s</a> <span class="rss-date">%6$s</span><cite>%3$s</cite></li>',
					esc_url( add_query_arg( array( 'action' => $action, 'entry' => absint( $entry->entries_id ) ), admin_url( 'admin.php?page=vfb-entries' ) ) ),
					esc_url( add_query_arg( 'form-filter', absint( $entry->form_id ), admin_url( 'admin.php?page=vfb-entries' ) ) ),
					esc_html( $entry->sender_name ),
					esc_html( $entry->sender_email ),
					esc_html( $entry->form_title ),
					date( "$date_format $time_format", strtotime( $entry->date_submitted ) )
				);

			endforeach;

			echo "<div class='rss-widget'><ul>$content</ul></div>";

		endif;
	}

	/**
	 * Displays the dashboard widget form control
	 *
	 * @since 2.2.1
	 */
	public function dashboard_widget_control() {
		if ( !$widget_options = get_option( 'vfb_dashboard_widget_options' ) )
			$widget_options = array();

		if ( !isset( $widget_options['vfb_dashboard_recent_entries'] ) )
			$widget_options['vfb_dashboard_recent_entries'] = array();

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['vfb-widget-recent-entries'] ) ) {
			$number = absint( $_POST['vfb-widget-recent-entries']['items'] );
			$widget_options['vfb_dashboard_recent_entries']['items'] = $number;
			update_option( 'vfb_dashboard_widget_options', $widget_options );
		}

		$number = isset( $widget_options['vfb_dashboard_recent_entries']['items'] ) ? (int) $widget_options['vfb_dashboard_recent_entries']['items'] : '';

		echo sprintf(
			'<p>
			<label for="comments-number">%1$s</label>
			<input id="comments-number" name="vfb-widget-recent-entries[items]" type="text" value="%2$d" size="3" />
			</p>',
			__( 'Number of entries to show:', 'visual-form-builder-pro' ),
			$number
		);
	}


	/**
	 * Register contextual help. This is for the Help tab dropdown
	 *
	 * @since 1.0
	 */
	public function help(){
		$screen = get_current_screen();

		$help = '<ul>';
		$help .= '<li><a href="http://vfbpro.com/documentation/installing" target="_blank">' . __( 'Installing the Plugin', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/staying-updated" target="_blank">' . __( 'Staying Updated', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/glossary" target="_blank">' . __( 'Glossary', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '</ul>';

		$screen->add_help_tab( array(
			'id'         => 'vfb-help-tab-general-info',
			'title'      => __( 'General Info', 'visual-form-builder-pro' ),
			'content'    => $help,
		) );

		$help = '<ul>';
		$help .= '<li><a href="http://vfbpro.com/documentation/forms-interface" target="_blank">' . __( 'Interface Overview', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/forms-creating" target="_blank">' . __( 'Creating a New Form', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/forms-sorting" target="_blank">' . __( 'Sorting Your Forms', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/forms-building" target="_blank">' . __( 'Building Your Forms', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '</ul>';

		$screen->add_help_tab( array(
			'id'         => 'vfb-help-tab-forms',
			'title'      => __( 'Forms', 'visual-form-builder-pro' ),
			'content'    => $help,
		) );

		$help = '<ul>';
		$help .= '<li><a href="http://vfbpro.com/documentation/entries-interface" target="_blank">' . __( 'Interface Overview', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/entries-managing" target="_blank">' . __( 'Managing Your Entries', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/entries-searching-filtering" target="_blank">' . __( 'Searching and Filtering Your Entries', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '</ul>';

		$screen->add_help_tab( array(
			'id'         => 'vfb-help-tab-entries',
			'title'      => __( 'Entries', 'visual-form-builder-pro' ),
			'content'    => $help,
		) );

		$help = '<ul>';
		$help .= '<li><a href="http://vfbpro.com/documentation/email-design" target="_blank">' . __( 'Email Design Interface Overview', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/analytics" target="_blank">' . __( 'Analytics Interface Overview', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '</ul>';

		$screen->add_help_tab( array(
			'id'         => 'vfb-help-tab-email-analytics',
			'title'      => __( 'Email Design &amp; Analytics', 'visual-form-builder-pro' ),
			'content'    => $help,
		) );

		$help = '<ul>';
		$help .= '<li><a href="http://vfbpro.com/documentation/import" target="_blank">' . __( 'Import Interface Overview', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/export" target="_blank">' . __( 'Export Interface Overview', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '</ul>';

		$screen->add_help_tab( array(
			'id'         => 'vfb-help-tab-import-export',
			'title'      => __( 'Import &amp; Export', 'visual-form-builder-pro' ),
			'content'    => $help,
		) );

		$help = '<ul>';
		$help .= '<li><a href="http://vfbpro.com/documentation/conditional-logic" target="_blank">' . __( 'Conditional Logic', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/templating" target="_blank">' . __( 'Templating', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/documentation/custom-capabilities" target="_blank">' . __( 'Custom Capabilities', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '<li><a href="http://vfbpro.com/hooks" target="_blank">' . __( 'Filters and Actions', 'visual-form-builder-pro' ) . '</a></li>';
		$help .= '</ul>';

		$screen->add_help_tab( array(
			'id'         => 'vfb-help-tab-advanced',
			'title'      => __( 'Advanced Topics', 'visual-form-builder-pro' ),
			'content'    => $help,
		) );

		$help = '<p>' . __( '<strong>Always load CSS</strong> - Force Visual Form Builder Pro CSS to load on every page. Will override "Disable CSS" option, if selected.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Disable CSS</strong> - Disable CSS output for all forms.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Disable Email</strong> - Disable emails from sending for all forms.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Disable Notifications Email</strong> - Disable notification emails from sending for all forms.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Skip Empty Fields in Email</strong> - Fields that have no data will not be displayed in the email.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Disable saving new entry</strong> - Disable new entries from being saved.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Disable saving entry IP address</strong> - An entry will be saved, but the IP address will be removed.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Place Address labels above fields</strong> - The Address field labels will be placed above the inputs instead of below.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Remove default SPAM Verification</strong> - The default SPAM Verification question will be removed and only a submit button will be visible.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Disable meta tag version</strong> - Prevent the hidden Visual Form Builder Pro version number from printing in the source code.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Skip PayPal redirect if total is zero</strong> - If PayPal is configured, do not redirect if the total is zero.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Prepend Confirmation</strong> - Always display the form beneath the text confirmation after the form has been submitted.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Max Upload Size</strong> - Restrict the file upload size for all forms.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>Sender Mail Header</strong> - Control the Sender attribute in the mail header. This is useful for certain server configurations that require an existing email on the domain to be used.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>reCAPTCHA Public Key</strong> - Required if "Use reCAPTCHA" option is selected in the Secret field.', 'visual-form-builder-pro' ) . '</p>';
		$help .= '<p>' . __( '<strong>reCAPTCHA Private Key</strong> - Required if "Use reCAPTCHA" option is selected in the Secret field.', 'visual-form-builder-pro' ) . '</p>';

		$screen->add_help_tab( array(
			'id'         => 'vfb-help-tab-settings',
			'title'      => __( 'Settings', 'visual-form-builder-pro' ),
			'content'    => $help,
		) );

	}

	/**
	 * Allow for additional plugin code to be run during admin_init
	 * which is not available during the plugin __construct()
	 *
	 * @since 2.1
	 */
	public function screen_advanced_options( $columns ) {
		// Only display on the form edit screen
		if ( isset( $_REQUEST['form'] ) ) :
			return array(
				'_title'	=> __( 'Show advanced properties', 'visual-form-builder-pro' ),
				'cb'		=> '<input type="checkbox" />',
				'merge-tag'	=> __( 'Merge Tag' ),
			);
		endif;

		return $columns;
	}
	/**
	 * Adds the Screen Options tab to the Entries screen
	 *
	 * @since 1.0
	 */
	public function screen_options(){
		$screen = get_current_screen();

		$page_main		= $this->_admin_pages[ 'vfb-pro' ];
		$page_entries 	= $this->_admin_pages[ 'vfb-entries' ];

		switch( $screen->id ) :
			case $page_entries :

				add_screen_option( 'per_page', array(
					'label'		=> __( 'Entries per page', 'visual-form-builder-pro' ),
					'default'	=> 20,
					'option'	=> 'vfb_entries_per_page'
				) );

				break;

			case $page_main :

				add_screen_option( 'layout_columns', array(
					'max'		=> 3,
					'default'	=> 2
				) );
				add_screen_option( 'per_page', array(
					'label'		=> __( 'Forms per page', 'visual-form-builder-pro' ),
					'default'	=> 20,
					'option'	=> 'vfb_forms_per_page'
				) );

				break;
		endswitch;
	}

	/**
	 * Saves the Screen Options
	 *
	 * @since 1.0
	 */
	public function save_screen_options( $status, $option, $value ){

		if ( $option == 'vfb_entries_per_page' )
			return $value;
		elseif ( $option == 'vfb_forms_per_page' )
			return $value;

		$status;
	}

	/**
	 * Add meta boxes to form builder screen
	 *
	 * @since 1.8
	 */
	public function add_meta_boxes() {
		global $current_screen;

		$page_main = $this->_admin_pages[ 'vfb-pro' ];

		if ( $current_screen->id == $page_main && isset( $_REQUEST['form'] ) ) {
			add_meta_box( 'vfb_form_switcher', __( 'Quick Switch', 'visual-form-builder-pro' ), array( &$this, 'meta_box_switch_form' ), $page_main, 'side', 'high' );
			add_meta_box( 'vfb_form_items_meta_box', __( 'Form Items', 'visual-form-builder-pro' ), array( &$this, 'meta_box_form_items' ), $page_main, 'side', 'high' );
			add_meta_box( 'vfb_form_media_button_tip', __( 'Display Forms', 'visual-form-builder-pro' ), array( &$this, 'meta_box_display_forms' ), $page_main, 'side', 'low' );
		}
	}

	/**
	 * Output for form Quick Switch meta box
	 *
	 * @since 1.8
	 */
	public function meta_box_switch_form() {
		global $wpdb;

		// Query to get all forms
		$order = sanitize_sql_orderby( 'form_id ASC' );
		$where = apply_filters( 'vfb_pre_get_forms_switcher', '' );
		$forms = $wpdb->get_results( "SELECT * FROM $this->form_table_name WHERE 1=1 $where ORDER BY $order" );

		$form_nav_selected_id = ( isset( $_REQUEST['form'] ) ) ? $_REQUEST['form'] : $forms[0]->form_id;
		?>
		<select id="switcher_form">
		<?php
			foreach ( $forms as $form ) {
				echo sprintf( '<option value="%1$d"%2$s id="%4$s">%1$d - %3$s</option>',
					$form->form_id,
					selected( $form->form_id, $form_nav_selected_id, 0 ),
					$form->form_title,
					$form->form_key
				);
			}
		?>
		</select>
		<?php
	}

	/**
	 * Output for Form Items meta box
	 *
	 * @since 1.8
	 */
	public function meta_box_form_items() {
		$vfb_post = '';
		// Run Create Post add-on
		if ( class_exists( 'VFB_Pro_Create_Post' ) )
			$vfb_post = new VFB_Pro_Create_Post();
	?>
		<div class="taxonomydiv">
			<p><strong><?php _e( 'Click or Drag' , 'visual-form-builder-pro'); ?></strong> <?php _e( 'to Add a Field' , 'visual-form-builder-pro'); ?> <img id="add-to-form" alt="" src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" class="waiting spinner" /></p>
			<ul class="posttype-tabs add-menu-item-tabs" id="vfb-field-tabs">
				<li class="tabs"><a href="#standard-fields" class="nav-tab-link vfb-field-types"><?php _e( 'Standard' , 'visual-form-builder-pro'); ?></a></li>
				<li><a href="#advanced-fields" class="nav-tab-link vfb-field-types"><?php _e( 'Advanced' , 'visual-form-builder-pro'); ?></a></li>
				<?php
					if ( class_exists( 'VFB_Pro_Create_Post' ) && method_exists( $vfb_post, 'form_item_tab' ) )
						$vfb_post->form_item_tab();
				?>
			</ul>
			<div id="standard-fields" class="tabs-panel tabs-panel-active">
				<ul class="vfb-fields-col-1">
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-fieldset">Fieldset</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-text"><b></b>Text</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-checkbox"><b></b>Checkbox</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-select"><b></b>Select</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-datepicker"><b></b>Date</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-url"><b></b>URL</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-digits"><b></b>Number</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-phone"><b></b>Phone</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-file"><b></b>File Upload</a></li>
				</ul>
				<ul class="vfb-fields-col-2">
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-section">Section</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-textarea"><b></b>Textarea</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-radio"><b></b>Radio</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-address"><b></b>Address</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-email"><b></b>Email</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-currency"><b></b>Currency</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-time"><b></b>Time</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-html"><b></b>HTML</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-instructions"><b></b>Instructions</a></li>
				</ul>
				<div class="clear"></div>
			</div> <!-- #standard-fields -->
			<div id="advanced-fields"class="tabs-panel tabs-panel-inactive">
				<ul class="vfb-fields-col-1">
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-username"><b></b>Username</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-hidden"><b></b>Hidden</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-autocomplete"><b></b>Autocomplete</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-min"><b></b>Min</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-range"><b></b>Range</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-name"><b></b>Name</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-likert"><b></b>Likert</a></li>
				</ul>
				<ul class="vfb-fields-col-2">
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-password"><b></b>Password</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-color"><b></b>Color Picker</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-ip"><b></b>IP Address</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-max"><b></b>Max</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-pagebreak"><b></b>Page Break</a></li>
					<li><a href="#" class="vfb-draggable-form-items" id="form-element-rating"><b></b>Rating</a></li>
				</ul>
				<div class="clear"></div>
			</div> <!-- #advanced-fields -->
			<?php
				if ( class_exists( 'VFB_Pro_Create_Post' ) && method_exists( $vfb_post, 'form_items' ) )
					$vfb_post->form_items();
			?>
		</div> <!-- .taxonomydiv -->
		<div class="clear"></div>
	<?php
	}

	/**
	 * Output for the Display Forms meta box
	 *
	 * @since 1.8
	 */
	public function meta_box_display_forms() {
	?>
		<p><?php _e( 'Add forms to your Posts or Pages by locating the <strong>Add Form</strong> button in the area above your post/page editor.', 'visual-form-builder-pro' ); ?></p>
    	<p><?php _e( 'You may also manually insert the shortcode into a post/page or the template tag into a template file.', 'visual-form-builder-pro' ); ?></p>
    	<p>
    		<?php _e( 'Shortcode', 'visual-form-builder-pro' ); ?>
    		<input value="[vfb id='<?php echo (int) $_REQUEST['form']; ?>']" readonly="readonly" />
    	</p>
    	<p>
    		<?php _e( 'Template Tag', 'visual-form-builder-pro' ); ?>
    		<input value="&lt;?php vfb_pro( 'id=<?php echo (int) $_REQUEST['form']; ?>' ); ?&gt;" readonly="readonly"/>
    	</p>
	<?php
	}

	/**
	 * Display migrate link
	 *
	 * @access public
	 * @return void
	 */
	public function migration_check() {
		// If the free version is active, display warning
		if ( is_plugin_active( 'visual-form-builder/visual-form-builder.php' ) )
			echo sprintf( '<div id="message" class="error"><p>%s</p></div>', __( 'The free version of Visual Form Builder is still active. In order for Visual Form Builder Pro to function and render correctly, you must deactivate the free version.' , 'visual-form-builder-pro' ) );

		// If VFB is not detected and the user can't install plugins, quit
		if ( !get_option( 'vfb_db_version' ) && !current_user_can( 'install_plugins' ) )
			return;

		// If they have upgraded or dismissed, don't display
		if ( get_option( 'vfb_db_upgrade' ) || get_option( 'vfb_ignore_notice' ) )
			return;
?>
		<div class="updated">
			<h3><?php _e( 'New to Visual Form Builder Pro?', 'visual-form-builder-pro' ); ?></h3>
			<p>
				<?php _e( 'Transferring your existing data from Visual Form Builder to the Pro version is easy. Simply click on the Migrate Forms button below.', 'visual-form-builder-pro' ); ?>
			</p>
			<p>
				<?php _e( 'If you have already started using the Pro version, please be aware that <strong style="color:red">migrating will wipe the Visual Form Builder Pro tables clean</strong>.', 'visual-form-builder-pro' ); ?>
			</p>
			<p>
				<a href="<?php echo esc_attr( add_query_arg( 'action', 'vfb-upgrade', admin_url( 'admin.php?page=visual-form-builder-pro' ) ) ); ?>" class="button button-primary"><?php _e( 'Migrate Forms', 'visual-form-builder-pro' ); ?></a>
				<a href="<?php echo esc_attr( add_query_arg( 'action', 'vfb-ignore-notice', admin_url( 'admin.php?page=visual-form-builder-pro' ) ) ); ?>" class="button button-secondary"><?php _e( 'Dismiss', 'visual-form-builder-pro' ); ?></a>
			</p>
		</div> <!-- .updated -->
<?php
	}

	/**
	 * Check database version and run SQL install, if needed
	 *
	 * @since 2.1
	 */
	public function update_db_check() {
		// Add a database version to help with upgrades and run SQL install
		if ( !get_option( 'vfb_pro_db_version' ) ) {
			update_option( 'vfb_pro_db_version', $this->vfb_db_version );
			$this->install_db();
		}

		// If database version doesn't match, update and maybe run SQL install
		if ( version_compare( get_option( 'vfb_pro_db_version' ), $this->vfb_db_version, '<' ) ) {
			update_option( 'vfb_pro_db_version', $this->vfb_db_version );
			$this->install_db();
		}
	}

	/**
	 * Install database tables
	 *
	 * @since 1.0
	 */
	static function install_db() {
		global $wpdb;

		$field_table_name 	= $wpdb->prefix . 'vfb_pro_fields';
		$form_table_name 	= $wpdb->prefix . 'vfb_pro_forms';
		$entries_table_name = $wpdb->prefix . 'vfb_pro_entries';

		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$field_sql = "CREATE TABLE $field_table_name (
				field_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				form_id BIGINT(20) NOT NULL,
				field_key VARCHAR(255) NOT NULL,
				field_type VARCHAR(25) NOT NULL,
				field_options TEXT,
				field_options_other VARCHAR(255),
				field_description TEXT,
				field_name TEXT NOT NULL,
				field_sequence BIGINT(20) DEFAULT '0',
				field_parent BIGINT(20) DEFAULT '0',
				field_validation VARCHAR(25),
				field_required VARCHAR(25),
				field_size VARCHAR(25) DEFAULT 'medium',
				field_css VARCHAR(255),
				field_layout VARCHAR(255),
				field_default TEXT,
				field_rule_setting TINYINT(1),
				field_rule LONGTEXT,
				PRIMARY KEY  (field_id)
			) $charset_collate;";

		$form_sql = "CREATE TABLE $form_table_name (
				form_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				form_key TINYTEXT NOT NULL,
				form_title TEXT NOT NULL,
				form_email_subject TEXT,
				form_email_to TEXT,
				form_email_from VARCHAR(255),
				form_email_from_name VARCHAR(255),
				form_email_from_override VARCHAR(255),
				form_email_from_name_override VARCHAR(255),
				form_email_rule_setting TINYINT(1),
				form_email_rule LONGTEXT,
				form_success_type VARCHAR(25) DEFAULT 'text',
				form_success_message TEXT,
				form_notification_setting VARCHAR(25),
				form_notification_email_name VARCHAR(255),
				form_notification_email_from VARCHAR(255),
				form_notification_email VARCHAR(25),
				form_notification_subject VARCHAR(255),
				form_notification_message TEXT,
				form_notification_entry VARCHAR(25),
				form_email_design TEXT,
				form_paypal_setting VARCHAR(25),
				form_paypal_email VARCHAR(255),
				form_paypal_currency VARCHAR(25) DEFAULT 'USD',
				form_paypal_shipping VARCHAR(255),
				form_paypal_tax VARCHAR(255),
				form_paypal_field_price TEXT,
				form_paypal_item_name VARCHAR(255),
				form_label_alignment VARCHAR(25),
				form_verification TINYINT(1) DEFAULT '1',
				form_entries_allowed VARCHAR(25),
				form_entries_schedule VARCHAR(100),
				form_unique_entry TINYINT(1) DEFAULT '0',
				form_status VARCHAR(20) DEFAULT 'publish',
				PRIMARY KEY  (form_id)
			) $charset_collate;";

		$entries_sql = "CREATE TABLE $entries_table_name (
				entries_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				form_id BIGINT(20) NOT NULL,
				user_id BIGINT(20) DEFAULT '1',
				data LONGTEXT NOT NULL,
				subject TEXT,
				sender_name VARCHAR(255),
				sender_email VARCHAR(255),
				emails_to TEXT,
				date_submitted DATETIME,
				ip_address VARCHAR(25),
				notes TEXT,
				akismet TEXT,
				entry_approved VARCHAR(20) DEFAULT '1',
				PRIMARY KEY  (entries_id)
			) $charset_collate;";

		// Create or Update database tables
		dbDelta( $field_sql );
		dbDelta( $form_sql );
		dbDelta( $entries_sql );

		$role = get_role( 'administrator' );

		// If the capabilities have not been added, do so here
		if ( !empty( $role ) && !$role->has_cap( 'vfb_edit_settings' ) ) {
			// Setup the capabilities for each role that gets access
			$caps = array(
				'administrator' => array(
					'vfb_create_forms',
					'vfb_edit_forms',
					'vfb_copy_forms',
					'vfb_delete_forms',
					'vfb_import_forms',
					'vfb_export_forms',
					'vfb_view_entries',
					'vfb_edit_entries',
					'vfb_delete_entries',
					'vfb_edit_email_design',
					'vfb_view_analytics',
					'vfb_edit_settings',
				),
				'editor' => array(
					'vfb_view_entries',
					'vfb_edit_entries',
					'vfb_delete_entries',
					'vfb_view_analytics',
				)
			);

			// Assign the appropriate caps to the administrator role
			if ( !empty( $role ) ) {
				foreach ( $caps['administrator'] as $cap ) {
					$role->add_cap( $cap );
				}
			}

			// Assign the appropriate caps to the editor role
			$role = get_role( 'editor' );
			if ( !empty( $role ) ) {
				foreach ( $caps['editor'] as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}

	}

	/**
	 * Queue plugin scripts and CSS for sorting form fields
	 *
	 * @since 1.0
	 */
	public function admin_scripts() {
		wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( "/css/smoothness/jquery-ui-1.10.3$this->load_dev_files.css", __FILE__ ), array(), '1.10.3' );
		wp_enqueue_style( 'visual-form-builder-style', plugins_url( "/css/visual-form-builder-admin$this->load_dev_files.css", __FILE__ ), array(), '20140412' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'jquery-form-validation', plugins_url( '/js/jquery.validate.min.js', __FILE__ ), array( 'jquery' ), '1.9.0', true );
		wp_enqueue_script( 'vfb-admin', plugins_url( "/js/vfb-admin$this->load_dev_files.js", __FILE__ ) , array( 'jquery', 'jquery-form-validation' ), '20150102', true );
		wp_enqueue_script( 'nested-sortable', plugins_url( "/js/jquery.ui.nestedSortable$this->load_dev_files.js", __FILE__ ) , array( 'jquery', 'jquery-ui-sortable' ), '1.3.6', true );
		wp_enqueue_script( 'jquery-ui-timepicker', plugins_url( "/js/jquery.ui.timepicker$this->load_dev_files.js", __FILE__ ) , array( 'jquery', 'jquery-ui-datepicker' ), '1.5.0', true );

		// Only load Google Charts if viewing Analytics to prevent errors
		if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], array( 'vfb-reports' ) ) ) {
			wp_enqueue_script( 'raphael-js', plugins_url( '/js/raphael.min.js', __FILE__ ), array(), '2.1.2', false );
			wp_enqueue_script( 'morris-js', plugins_url( '/js/morris.min.js', __FILE__ ), array( 'raphael-js' ), '0.5.0', false );
			wp_enqueue_script( 'vfb-charts', plugins_url( "/js/vfb-charts$this->load_dev_files.js", __FILE__ ), array( 'morris-js' ), '20130916', false );
		}

		// Load CSS for Create Post add-on
		if ( class_exists( 'VFB_Pro_Create_Post' ) )
			wp_enqueue_style( 'vfb-pro-create-post', plugins_url( '/vfb-pro-create-post/css/vfb-pro-create-post.css' ), array( 'jquery-ui-datepicker', 'visual-form-builder-style' ), '20130916' );

		wp_localize_script( 'vfb-admin', 'VfbAdminPages', array( 'vfb_pages' => $this->_admin_pages ) );
	}

	/**
	 * Queue form validation scripts
	 *
	 * Scripts loaded in form-output.php, when field is present:
	 *	jQuery UI autocomplete
	 *	jQuery UI date picker
	 *  jQuery UI date picker i18n
	 *	Farbtastic
	 *	CKEditor
	 *
	 * @since 1.0
	 */
	public function scripts() {
		// Make sure scripts are only added once via shortcode
		$this->add_scripts = true;

		wp_register_script( 'jquery-form-validation', plugins_url( '/js/jquery.validate.min.js', __FILE__ ), array( 'jquery' ), '1.9.0', true );
		wp_register_script( 'visual-form-builder-validation', plugins_url( "/js/vfb-validation$this->load_dev_files.js", __FILE__ ) , array( 'jquery', 'jquery-form-validation' ), '20141015', true );
		wp_register_script( 'visual-form-builder-metadata', plugins_url( "/js/jquery.metadata$this->load_dev_files.js", __FILE__ ) , array( 'jquery', 'jquery-form-validation' ), '2.0', true );
		wp_register_script( 'farbtastic-js', plugins_url( "/js/farbtastic$this->load_dev_files.js", __FILE__ ), array( 'jquery' ), '2.0.0', true );
		wp_register_script( 'vfb-ckeditor', plugins_url( '/js/ckeditor/ckeditor.js', __FILE__ ), array( 'jquery' ), '4.1', true );

		wp_enqueue_script( 'jquery-form-validation' );
		wp_enqueue_script( 'visual-form-builder-validation' );
		wp_enqueue_script( 'visual-form-builder-metadata' );

		wp_localize_script( 'visual-form-builder-validation', 'VfbAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		$locale = get_locale();
        $translations = array(
        	'cs_CS',	// Czech
        	'de_DE',	// German
        	'el_GR',	// Greek
        	'en_US',	// English (US)
        	'en_AU',	// English (AU)
        	'en_GB',	// English (GB)
        	'es_ES',	// Spanish
        	'fr_FR',	// French
        	'he_IL', 	// Hebrew
        	'hu_HU',	// Hungarian
        	'id_ID',	// Indonseian
        	'it_IT',	// Italian
        	'ja_JP',	// Japanese
        	'ko_KR',	// Korean
        	'nl_NL',	// Dutch
        	'pl_PL',	// Polish
        	'pt_BR',	// Portuguese (Brazilian)
        	'pt_PT',	// Portuguese (European)
        	'ro_RO',	// Romanian
        	'ru_RU',	// Russian
        	'sv_SE',	// Swedish
        	'tr_TR', 	// Turkish
        	'zh_CN',	// Chinese
        	'zh_TW',	// Chinese (Taiwan)
        );

		// Load localized vaidation and datepicker text, if translation files exist
        if ( in_array( $locale, $translations ) ) {
            wp_register_script( 'vfb-validation-i18n', plugins_url( "/js/i18n/validate/messages-$locale.js", __FILE__ ), array( 'jquery-form-validation' ), '1.9.0', true );
            wp_register_script( 'vfb-datepicker-i18n', plugins_url( "/js/i18n/datepicker/datepicker-$locale.js", __FILE__ ), array( 'jquery-ui-datepicker' ), '1.0', true );

            wp_enqueue_script( 'vfb-validation-i18n' );
        }
        // Otherwise, load English translations
        else {
	        wp_register_script( 'vfb-validation-i18n', plugins_url( "/js/i18n/validate/messages-en_US.js", __FILE__ ), array( 'jquery-form-validation' ), '1.9.0', true );
            wp_register_script( 'vfb-datepicker-i18n', plugins_url( "/js/i18n/datepicker/datepicker-en_US.js", __FILE__ ), array( 'jquery-ui-datepicker' ), '1.0', true );

            wp_enqueue_script( 'vfb-validation-i18n' );
        }
	}

	/**
	 * Add form CSS to wp_head
	 *
	 * @since 1.0
	 */
	public function css() {

		$vfb_settings = get_option( 'vfb-settings' );

		wp_register_style( 'vfb-jqueryui-css', apply_filters( 'vfb-date-picker-css', plugins_url( '/css/smoothness/jquery-ui-1.10.3.min.css', __FILE__ ) ), array(), '20130916' );
		wp_register_style( 'visual-form-builder-css', apply_filters( 'visual-form-builder-css', plugins_url( "/css/visual-form-builder$this->load_dev_files.css", __FILE__ ) ), array(), '20140412' );

		// Settings - Always load CSS
		if ( isset( $vfb_settings['always-load-css'] ) ) {
			wp_enqueue_style( 'visual-form-builder-css' );
			wp_enqueue_style( 'vfb-jqueryui-css' );
			wp_enqueue_style( 'farbtastic' );

			return;
		}

		// Get active widgets
		$widget = is_active_widget( false, false, 'vfb_widget' );

		// If in admin Preview, always enqueue
		if ( !defined( 'VFB_PRO_PREVIEW' ) ) {
			// Settings - Disable CSS
			if ( isset( $vfb_settings['disable-css'] ) )
				return;

			// If no widget is found, test for shortcode
			if ( empty( $widget ) ) {
				// If WordPress 3.6, use internal function. Otherwise, my own
				if ( function_exists( 'has_shortcode' ) ) {
					global $post;

					// If no post exists, exit
					if ( !$post )
						return;

					if ( !has_shortcode( $post->post_content, 'vfb' ) )
						return;
				} elseif ( !$this->has_shortcode( 'vfb' ) ) {
					return;
				}
			}
		}

		wp_enqueue_style( 'visual-form-builder-css' );
		wp_enqueue_style( 'vfb-jqueryui-css' );
		wp_enqueue_style( 'farbtastic' );
	}

	/**
	 * Save new forms on the VFB Pro > Add New page
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_add_new_form() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'vfb-add-new' !== $_GET['page'] )
			return;

		if ( 'create_form' !== $_REQUEST['action'] )
			return;

		check_admin_referer( 'create_form' );

		$form_key 		= sanitize_title( $_REQUEST['form_title'] );
		$form_title 	= $_REQUEST['form_title'];
		$form_from_name = $_REQUEST['form_email_from_name'];
		$form_subject 	= $_REQUEST['form_email_subject'];
		$form_from 		= $_REQUEST['form_email_from'];
		$form_to 		= serialize( $_REQUEST['form_email_to'] );


		$email_design = array(
			'format' 				=> 'html',
			'link_love' 			=> 'yes',
			'footer_text' 			=> '',
			'background_color' 		=> '#eeeeee',
			'header_text' 			=> $form_subject,
			'header_image' 			=> '',
			'header_color' 			=> '#810202',
			'header_text_color' 	=> '#ffffff',
			'fieldset_color' 		=> '#680606',
			'section_color' 		=> '#5C6266',
			'section_text_color' 	=> '#ffffff',
			'text_color' 			=> '#333333',
			'link_color' 			=> '#1b8be0',
			'row_color' 			=> '#ffffff',
			'row_alt_color' 		=> '#eeeeee',
			'border_color' 			=> '#cccccc',
			'footer_color' 			=> '#333333',
			'footer_text_color' 	=> '#ffffff',
			'font_family' 			=> 'Arial',
			'header_font_size' 		=> 32,
			'fieldset_font_size' 	=> 20,
			'section_font_size' 	=> 15,
			'text_font_size' 		=> 13,
			'footer_font_size' 		=> 11
		);

		$newdata = array(
			'form_key' 				=> $form_key,
			'form_title' 			=> $form_title,
			'form_email_from_name'	=> $form_from_name,
			'form_email_subject'	=> $form_subject,
			'form_email_from'		=> $form_from,
			'form_email_to'			=> $form_to,
			'form_email_design' 	=> serialize( $email_design ),
			'form_success_message'	=> '<p class="vfb-form-success">Your form was successfully submitted. Thank you for contacting us.</p>'
		);

		// Create the form
		$wpdb->insert( $this->form_table_name, $newdata );

		// Get form ID to add our first field
		$new_form_selected = $wpdb->insert_id;

		// Setup the initial fieldset
		$initial_fieldset = array(
			'form_id' 			=> $wpdb->insert_id,
			'field_key' 		=> 'fieldset',
			'field_type' 		=> 'fieldset',
			'field_name' 		=> 'Fieldset',
			'field_sequence' 	=> 0
		);

		// Add the first fieldset to get things started
		$wpdb->insert(
			$this->field_table_name,
			$initial_fieldset,
			array(
				'%d', // form_id
				'%s', // field_key
				'%s', // field_type
				'%s', // field_name
				'%d', // field_sequence
			)
		);

		$verification_fieldset = array(
			'form_id' 			=> $new_form_selected,
			'field_key' 		=> 'verification',
			'field_type' 		=> 'verification',
			'field_name' 		=> 'Verification',
			'field_description' => '(This is for preventing spam)',
			'field_sequence' 	=> 1
		);

		// Insert the submit field
		$wpdb->insert(
			$this->field_table_name,
			$verification_fieldset,
			array(
				'%d', // form_id
				'%s', // field_key
				'%s', // field_type
				'%s', // field_name
				'%s', // field_description
				'%d', // field_sequence
			)
		);

		$verify_fieldset_parent_id = $wpdb->insert_id;

		$secret = array(
			'form_id' 			=> $new_form_selected,
			'field_key' 		=> 'secret',
			'field_type' 		=> 'secret',
			'field_name' 		=> 'Please enter any two digits',
			'field_description'	=> 'Example: 12',
			'field_size' 		=> 'medium',
			'field_required' 	=> 'yes',
			'field_parent' 		=> $verify_fieldset_parent_id,
			'field_sequence' 	=> 2
		);

		// Insert the submit field
		$wpdb->insert(
			$this->field_table_name,
			$secret,
			array(
				'%d', // form_id
				'%s', // field_key
				'%s', // field_type
				'%s', // field_name
				'%s', // field_description
				'%s', // field_size
				'%d', // field_parent
				'%d', // field_sequence
			)
		);

		// Make the submit last in the sequence
		$submit = array(
			'form_id' 			=> $new_form_selected,
			'field_key' 		=> 'submit',
			'field_type' 		=> 'submit',
			'field_name' 		=> 'Submit',
			'field_parent' 		=> $verify_fieldset_parent_id,
			'field_sequence' 	=> 3
		);

		// Insert the submit field
		$wpdb->insert(
			$this->field_table_name,
			$submit,
			array(
				'%d', // form_id
				'%s', // field_key
				'%s', // field_type
				'%s', // field_name
				'%d', // field_parent
				'%d', // field_sequence
			)
		);

		// Redirect to keep the URL clean (use AJAX in the future?)
		wp_redirect( 'admin.php?page=visual-form-builder-pro&action=edit&form=' . $new_form_selected );
		exit();
	}

	/**
	 * Save the form
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_update_form() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'visual-form-builder-pro' !== $_GET['page'] )
			return;

		if ( 'update_form' !== $_REQUEST['action'] )
			return;

		check_admin_referer( 'vfb_update_form' );

		$form_id 						= absint( $_REQUEST['form_id'] );
		$form_key 						= sanitize_title( $_REQUEST['form_title'], $form_id );
		$form_title 					= $_REQUEST['form_title'];
		$form_subject 					= $_REQUEST['form_email_subject'];
		$form_to 						= serialize( array_map( 'sanitize_email', $_REQUEST['form_email_to'] ) );

		$form_from 						= sanitize_email( $_REQUEST['form_email_from'] );
		$form_from_name 				= $_REQUEST['form_email_from_name'];
		$form_from_override 			= isset( $_REQUEST['form_email_from_override'] ) ? $_REQUEST['form_email_from_override'] : '';
		$form_from_name_override 		= isset( $_REQUEST['form_email_from_name_override'] ) ? $_REQUEST['form_email_from_name_override'] : '';

		$form_success_type 				= $_REQUEST['form_success_type'];

		$form_notification_setting 		= isset( $_REQUEST['form_notification_setting'] ) ? $_REQUEST['form_notification_setting'] : '';
		$form_notification_email_name 	= isset( $_REQUEST['form_notification_email_name'] ) ? $_REQUEST['form_notification_email_name'] : '';
		$form_notification_email_from 	= isset( $_REQUEST['form_notification_email_from'] ) ? sanitize_email( $_REQUEST['form_notification_email_from'] ) : '';
		$form_notification_email 		= isset( $_REQUEST['form_notification_email'] ) ? $_REQUEST['form_notification_email'] : '';
		$form_notification_subject 		= isset( $_REQUEST['form_notification_subject'] ) ? $_REQUEST['form_notification_subject'] : '';
		$form_notification_message 		= isset( $_REQUEST['form_notification_message'] ) ? wp_richedit_pre( $_REQUEST['form_notification_message'] ) : '';
		$form_notification_entry 		= isset( $_REQUEST['form_notification_entry'] ) ? $_REQUEST['form_notification_entry'] : '';

		$form_paypal_setting 			= isset( $_REQUEST['form_paypal_setting'] ) ? $_REQUEST['form_paypal_setting'] : '';
		$form_paypal_email 				= isset( $_REQUEST['form_paypal_email'] ) ? sanitize_email( $_REQUEST['form_paypal_email'] ) : '';
		$form_paypal_currency 			= isset( $_REQUEST['form_paypal_currency'] ) ? $_REQUEST['form_paypal_currency'] : '';
		$form_paypal_shipping 			= isset( $_REQUEST['form_paypal_shipping'] ) ? $_REQUEST['form_paypal_shipping'] : '';
		$form_paypal_tax 				= isset( $_REQUEST['form_paypal_tax'] ) ? $_REQUEST['form_paypal_tax'] : '';
		$form_paypal_field_price 		= isset( $_REQUEST['form_paypal_field_price'] ) ? serialize( $_REQUEST['form_paypal_field_price'] ) : '';
		$form_paypal_item_name 			= isset( $_REQUEST['form_paypal_item_name'] ) ? $_REQUEST['form_paypal_item_name'] : '';

		$form_label_alignment 			= $_REQUEST['form_label_alignment'];
		$form_verification 				= $_REQUEST['form_verification'];

		$form_entries_allowed			= isset( $_REQUEST['form_entries_allowed'] ) ? sanitize_text_field( $_REQUEST['form_entries_allowed'] ) : '';
		$form_entries_schedule			= isset( $_REQUEST['form_entries_schedule'] ) ? serialize( array_map( 'sanitize_text_field', $_REQUEST['form_entries_schedule'] ) ) : '';

		$form_unique_entry 				= isset( $_REQUEST['form_unique_entry'] ) ? absint( $_REQUEST['form_unique_entry'] ) : 0;

		$form_status					= $_REQUEST['form_status'];

		// Add confirmation based on which type was selected
		switch ( $form_success_type ) {
			case 'text' :
			case 'display-entry' :
				$form_success_message = wp_richedit_pre( $_REQUEST['form_success_message_text'] );
			break;
			case 'page' :
				$form_success_message = $_REQUEST['form_success_message_page'];
			break;
			case 'redirect' :
				$form_success_message = $_REQUEST['form_success_message_redirect'];
			break;
		}

		$newdata = array(
			'form_key'						=> $form_key,
			'form_title' 					=> $form_title,
			'form_email_subject' 			=> $form_subject,
			'form_email_to' 				=> $form_to,
			'form_email_from' 				=> $form_from,
			'form_email_from_name' 			=> $form_from_name,
			'form_email_from_override' 		=> $form_from_override,
			'form_email_from_name_override' => $form_from_name_override,
			'form_success_type' 			=> $form_success_type,
			'form_success_message' 			=> $form_success_message,
			'form_notification_setting' 	=> $form_notification_setting,
			'form_notification_email_name' 	=> $form_notification_email_name,
			'form_notification_email_from' 	=> $form_notification_email_from,
			'form_notification_email' 		=> $form_notification_email,
			'form_notification_subject' 	=> $form_notification_subject,
			'form_notification_message' 	=> $form_notification_message,
			'form_notification_entry' 		=> $form_notification_entry,
			'form_paypal_setting' 			=> $form_paypal_setting,
			'form_paypal_email' 			=> $form_paypal_email,
			'form_paypal_currency' 			=> $form_paypal_currency,
			'form_paypal_shipping' 			=> $form_paypal_shipping,
			'form_paypal_tax' 				=> $form_paypal_tax,
			'form_paypal_field_price' 		=> $form_paypal_field_price,
			'form_paypal_item_name' 		=> $form_paypal_item_name,
			'form_label_alignment' 			=> $form_label_alignment,
			'form_verification' 			=> $form_verification,
			'form_entries_allowed' 			=> $form_entries_allowed,
			'form_entries_schedule' 		=> $form_entries_schedule,
			'form_unique_entry'				=> $form_unique_entry,
			'form_status'					=> $form_status,
		);

		// Update form details
		$wpdb->update(
			$this->form_table_name,
			$newdata,
			array(
				'form_id' => $form_id
			),
			array(
				'%s', // form_key
				'%s', // form_title
				'%s', // form_email_subject
				'%s', // form_email_to
				'%s', // form_email_from
				'%s', // form_email_from_name
				'%s', // form_email_from_override
				'%s', // form_email_from_name_override
				'%s', // form_success_type
				'%s', // form_success_message
				'%s', // form_notification_setting
				'%s', // form_notification_email_name
				'%s', // form_notification_email_from
				'%s', // form_notification_email
				'%s', // form_notification_subject
				'%s', // form_notification_message
				'%s', // form_notification_entry
				'%s', // form_paypal_setting
				'%s', // form_paypal_email
				'%s', // form_paypal_currency
				'%s', // form_paypal_shipping
				'%s', // form_paypal_tax
				'%s', // form_paypal_field_price
				'%s', // form_paypal_item_name
				'%s', // form_label_alignment
				'%d', // form_verification
				'%s', // form_entries_allowed
				'%s', // form_entries_schedule
				'%d', // form_unique_entry
				'%s', // form_status
			),
			array(
				'%d', // form_id
			)
		);

		$field_ids = array();

		// Get max post vars, if available. Otherwise set to 1000
		$max_post_vars = ( ini_get( 'max_input_vars' ) ) ? intval( ini_get( 'max_input_vars' ) ) : 1000;

		// Set a message to be displayed if we've reached a limit
		if ( count( $_POST, COUNT_RECURSIVE ) > $max_post_vars )
			$this->post_max_vars = true;

		foreach ( $_REQUEST['field_id'] as $fields ) :
				$field_ids[] = $fields;
		endforeach;

		// Initialize field sequence
		$field_sequence = 0;

		// Loop through each field and update
		foreach ( $field_ids as $id ) :
			$id = absint( $id );

			$field_name          = ( isset( $_REQUEST['field_name-' . $id] ) ) ? trim( $_REQUEST['field_name-' . $id] ) : '';
			$field_key           = sanitize_key( sanitize_title( $field_name, $id ) );
			$field_desc          = ( isset( $_REQUEST['field_description-' . $id] ) ) ? trim( $_REQUEST['field_description-' . $id] ) : '';
			$field_options       = ( isset( $_REQUEST['field_options-' . $id] ) ) ? serialize( array_map( 'trim', $_REQUEST['field_options-' . $id] ) ) : '';
			$field_options_other = ( isset( $_REQUEST['field_options_other-' . $id] ) ) ? serialize( array_map( 'trim', $_REQUEST['field_options_other-' . $id] ) ) : '';
			$field_validation    = ( isset( $_REQUEST['field_validation-' . $id] ) ) ? $_REQUEST['field_validation-' . $id] : '';
			$field_required      = ( isset( $_REQUEST['field_required-' . $id] ) ) ? $_REQUEST['field_required-' . $id] : '';
			$field_size          = ( isset( $_REQUEST['field_size-' . $id] ) ) ? $_REQUEST['field_size-' . $id] : '';
			$field_css           = ( isset( $_REQUEST['field_css-' . $id] ) ) ? $_REQUEST['field_css-' . $id] : '';
			$field_layout        = ( isset( $_REQUEST['field_layout-' . $id] ) ) ? $_REQUEST['field_layout-' . $id] : '';
			$field_default       = ( isset( $_REQUEST['field_default-' . $id] ) ) ? trim( $_REQUEST['field_default-' . $id] ) : '';

			$field_data = array(
				'field_key'             => $field_key,
				'field_name'            => $field_name,
				'field_description'     => $field_desc,
				'field_options'         => $field_options,
				'field_options_other'   => $field_options_other,
				'field_validation'      => $field_validation,
				'field_required'        => $field_required,
				'field_size'            => $field_size,
				'field_css'             => $field_css,
				'field_layout'          => $field_layout,
				'field_sequence'        => $field_sequence,
				'field_default'         => $field_default
			);

			// Update all fields
			$wpdb->update(
				$this->field_table_name,
				$field_data,
				array(
					'form_id' 	=> $form_id,
					'field_id' 	=> $id
				),
				array(
					'%s', // field_key
					'%s', // field_name
					'%s', // field_description
					'%s', // field_options
					'%s', // field_options_other
					'%s', // field_validation
					'%s', // field_required
					'%s', // field_size
					'%s', // field_css
					'%s', // field_layout
					'%d', // field_sequence
					'%s', // field_default
				),
				array(
					'%d', // form_id
					'%d', // field_id
				)
			);

			$field_sequence++;
		endforeach;
	}

	/**
	 * Handle trashing and deleting forms
	 *
	 * This is a placeholder function since all processing is handled in includes/class-forms-list.php
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_trash_delete_form() {
		// Handled in the process_bulk_actions() function in includes/class-forms-list.php
	}

	/**
	 * Handle form duplication
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_copy_form() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'visual-form-builder-pro' !== $_GET['page'] )
			return;

		if ( 'copy_form' !== $_REQUEST['action'] )
			return;

		$id = absint( $_REQUEST['form'] );

		check_admin_referer( 'copy-form-' . $id );

		// Get all fields and data for the request form
		$fields     = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE form_id = %d", $id ) );
		$form       = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->form_table_name WHERE form_id = %d", $id ) );
		$override   = $wpdb->get_var( $wpdb->prepare( "SELECT form_email_from_override, form_email_from_name_override, form_notification_email FROM $this->form_table_name WHERE form_id = %d", $id ) );
		$from_name  = $wpdb->get_var( null, 1 );
		$notify     = $wpdb->get_var( null, 2 );

		// Copy this form and force the initial title to denote a copy
		$data = array(
			'form_key' 						=> sanitize_title( $form->form_key . ' copy' ),
			'form_title' 					=> $form->form_title . ' Copy',
			'form_email_subject' 			=> $form->form_email_subject,
			'form_email_to' 				=> $form->form_email_to,
			'form_email_from' 				=> $form->form_email_from,
			'form_email_from_name' 			=> $form->form_email_from_name,
			'form_email_from_override' 		=> $form->form_email_from_override,
			'form_email_from_name_override' => $form->form_email_from_name_override,
			'form_success_type' 			=> $form->form_success_type,
			'form_success_message' 			=> $form->form_success_message,
			'form_notification_setting' 	=> $form->form_notification_setting,
			'form_notification_email_name' 	=> $form->form_notification_email_name,
			'form_notification_email_from' 	=> $form->form_notification_email_from,
			'form_notification_email' 		=> $form->form_notification_email,
			'form_notification_subject' 	=> $form->form_notification_subject,
			'form_notification_message' 	=> $form->form_notification_message,
			'form_notification_entry' 		=> $form->form_notification_entry,
			'form_email_design' 			=> $form->form_email_design,
			'form_paypal_setting' 			=> $form->form_paypal_setting,
			'form_paypal_email' 			=> $form->form_paypal_email,
			'form_paypal_currency' 			=> $form->form_paypal_currency,
			'form_paypal_shipping' 			=> $form->form_paypal_shipping,
			'form_paypal_tax' 				=> $form->form_paypal_tax,
			'form_paypal_field_price' 		=> $form->form_paypal_field_price,
			'form_paypal_item_name' 		=> $form->form_paypal_item_name,
			'form_label_alignment' 			=> $form->form_label_alignment,
			'form_verification' 			=> $form->form_verification,
			'form_entries_allowed' 			=> $form->form_entries_allowed,
			'form_entries_schedule'			=> $form->form_entries_schedule,
			'form_unique_entry'				=> $form->form_unique_entry,
			'form_email_rule_setting'		=> $form->form_email_rule_setting,
			'form_email_rule'				=> $form->form_email_rule,
		);

		$wpdb->insert(
			$this->form_table_name,
			$data,
			array(
				'%s', // form_key
				'%s', // form_title
				'%s', // form_email_subject
				'%s', // form_email_to
				'%s', // form_email_from
				'%s', // form_email_form_name
				'%s', // form_email_from_override
				'%s', // form_email_from_name_override
				'%s', // form_success_type
				'%s', // form_success_message
				'%s', // form_notification_setting
				'%s', // form_notification_email_name
				'%s', // form_notification_email_from
				'%s', // form_notification_email
				'%s', // form_notification_subject
				'%s', // form_notification_message
				'%s', // form_notification_entry
				'%s', // form_email_design
				'%s', // form_paypal_setting
				'%s', // form_paypal_email
				'%s', // form_paypal_currency
				'%s', // form_paypal_shipping
				'%s', // form_paypal_tax
				'%s', // form_paypal_field_price
				'%s', // form_paypal_item_name
				'%s', // form_label_alignment
				'%d', // form_verification
				'%s', // form_entries_allowed
				'%s', // form_entries_schedule
				'%d', // form_unique_entry
				'%d', // form_email_rule_setting
				'%s', // form_email_rule
			)
		);

		// Get form ID to add our first field
		$new_form_selected = $wpdb->insert_id;

		// Copy each field and data
		foreach ( $fields as $field ) :

			$data = array(
				'form_id' 			=> $new_form_selected,
				'field_key' 		=> $field->field_key,
				'field_type' 		=> $field->field_type,
				'field_name' 		=> $field->field_name,
				'field_description' => $field->field_description,
				'field_options' 	=> $field->field_options,
				'field_options_other'=> $field->field_options_other,
				'field_sequence' 	=> $field->field_sequence,
				'field_validation' 	=> $field->field_validation,
				'field_required' 	=> $field->field_required,
				'field_size' 		=> $field->field_size,
				'field_css' 		=> $field->field_css,
				'field_layout' 		=> $field->field_layout,
				'field_parent' 		=> $field->field_parent,
				'field_default' 	=> $field->field_default,
				'field_rule_setting'=> $field->field_rule_setting,
				'field_rule' 		=> $field->field_rule
			);

			$wpdb->insert(
				$this->field_table_name,
				$data,
				array(
					'%d', // field_id
					'%s', // field_key
					'%s', // field_type
					'%s', // field_name
					'%s', // field_description
					'%s', // field_options
					'%s', // field_options_other
					'%d', // field_sequence
					'%s', // field_validation
					'%s', // field_required
					'%s', // field_size
					'%s', // field_css
					'%s', // field_layout
					'%d', // field_parent
					'%s', // field_default
					'%d', // field_rule_setting
					'%s', // field_rule
				)
			);

			// Save field IDs so we can update the field rules
			$old_ids[ $field->field_id ] = $wpdb->insert_id;

			// If a parent field, save the old ID and the new ID to update new parent ID
			if ( in_array( $field->field_type, array( 'fieldset', 'section', 'verification' ) ) )
				$parents[ $field->field_id ] = $wpdb->insert_id;

			if ( $override == $field->field_id )
				$wpdb->update( $this->form_table_name, array( 'form_email_from_override' => $wpdb->insert_id ), array( 'form_id' => $new_form_selected ) );

			if ( $from_name == $field->field_id )
				$wpdb->update( $this->form_table_name, array( 'form_email_from_name_override' => $wpdb->insert_id ), array( 'form_id' => $new_form_selected ) );

			if ( $notify == $field->field_id )
				$wpdb->update( $this->form_table_name, array( 'form_notification_email' => $wpdb->insert_id ), array( 'form_id' => $new_form_selected ) );
		endforeach;

		// Loop through our parents and update them to their new IDs
		foreach ( $parents as $k => $v ) {
			$wpdb->update(
				$this->field_table_name,
				array(
					'field_parent' => $v
				),
				array(
					'form_id'      => $new_form_selected,
					'field_parent' => $k
				),
				array(
					'%d', // field_parent
				),
				array(
					'%d', // form_id
					'%d', // field_parent
				)
			);
		}

		// Get email rule
		$get_email_rules = $wpdb->get_var( $wpdb->prepare( "SELECT form_email_rule FROM $this->form_table_name WHERE form_id = %d", $id ) );
		$email_rules = maybe_unserialize( $get_email_rules );

		// Update email rule field IDs
		if ( $email_rules ) :
			$new_email_rules = array(
				'form_email_rule_setting' 	=> $email_rules['form_email_rule_setting'],
				'form_id'					=> $new_form_selected,
			);

			foreach ( $email_rules['rules'] as $key => $val ) :
				$new_email_rules['rules'][$key]['field']	= $val['field'];
				$new_email_rules['rules'][$key]['option']	= $val['option'];
				$new_email_rules['rules'][$key]['email'] 	= $val['email'];

				if ( array_key_exists( $val['field'], $old_ids ) )
					$new_email_rules['rules'][$key]['field'] = $old_ids[ $val['field'] ];

			endforeach;

			$wpdb->update(
				$this->form_table_name,
				array(
					'form_email_rule' => maybe_serialize( $new_email_rules )
				),
				array(
					'form_id' => $new_form_selected
				),
				array(
					'%s' // form_email_rule
				),
				array(
					'%d' // form_id
				)
			);
		endif;

		// Update conditional logic field IDs
		foreach ( $old_ids as $k => $v ) :

			// Get field rule
			$get_field_rules = $wpdb->get_var( $wpdb->prepare( "SELECT field_rule FROM $this->field_table_name WHERE form_id = %d AND field_id = %d", $id, $k ) );

			$field_rules = maybe_unserialize( $get_field_rules );
			if ( !$field_rules )
				continue;

			$new = array(
				'conditional_setting' 	=> $field_rules['conditional_setting'],
				'conditional_show'		=> $field_rules['conditional_show'],
				'conditional_logic'		=> $field_rules['conditional_logic'],
				'rules'					=> array(),
				'field_id'				=> $v,
			);

			foreach ( $field_rules['rules'] as $key => $val ) :
				$new['rules'][$key]['field']		= $val['field'];
				$new['rules'][$key]['condition'] 	= $val['condition'];
				$new['rules'][$key]['option']		= $val['option'];

				if ( array_key_exists( $val['field'], $old_ids ) )
					$new['rules'][$key]['field'] = $old_ids[ $val['field'] ];

			endforeach;

			$wpdb->update(
				$this->field_table_name,
				array(
					'field_rule' => maybe_serialize( $new )
				),
				array(
					'field_id' => $v
				),
				array(
					'%s' // field_rule
				),
				array(
					'%d' // field_id
				)
			);

		endforeach;
	}

	/**
	 * Save options on the VFB Pro > Email Design page
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_email_design() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'vfb-email-design' !== $_GET['page'] )
			return;

		if ( 'email_design' !== $_REQUEST['action'] )
			return;

		$form_id = absint( $_REQUEST['form_id'] );

		check_admin_referer( 'update-design-' . $form_id );

		$email = unserialize( $wpdb->get_var( $wpdb->prepare( "SELECT form_email_design FROM $this->form_table_name WHERE form_id = %d", $form_id ) ) );

		$header_image = ( !empty( $email['header_image'] ) ) ? $email['header_image'] : '';

		if ( isset( $_FILES['header_image'] ) ) {
			$value = $_FILES['header_image'];

			if ( $value['size'] > 0 ) {
				// Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
				$uploaded_file = wp_handle_upload( $value, array( 'test_form' => false ) );

				@list( $width, $height, $type, $attr ) = getimagesize( $uploaded_file['file'] );

				if ( $width == 600 && $height == 137 )
					$header_image = ( isset( $uploaded_file['file'] ) ) ? $uploaded_file['url'] : '';
				elseif ( $width > 600 ) {
					$oitar = $width / 600;

					$image = wp_crop_image( $uploaded_file['file'], 0, 0, $width, $height, 600, $height / $oitar, false, str_replace( basename( $uploaded_file['file'] ), 'vfb-header-img-' . basename( $uploaded_file['file'] ), $uploaded_file['file'] ) );

					if ( is_wp_error( $image ) )
						wp_die( __( 'Image could not be processed.  Please go back and try again.' ), __( 'Image Processing Error' ) );
					$header_image = str_replace( basename( $uploaded_file['url'] ), basename( $image ), $uploaded_file['url'] );
				}
				else {
					$dst_width = 600;
					$dst_height = absint( $height * ( 600 / $width ) );

					$cropped = wp_crop_image( $uploaded_file['file'], 0, 0, $width, $height, $dst_width, $dst_height, false, str_replace( basename( $uploaded_file['file'] ), 'vfb-header-img-' . basename( $uploaded_file['file'] ), $uploaded_file['file'] ) );

					if ( is_wp_error( $cropped ) )
						wp_die( __( 'Image could not be processed.  Please go back and try again.' ), __( 'Image Processing Error' ) );
					$header_image = str_replace( basename( $uploaded_file['url'] ), basename( $cropped ), $uploaded_file['url'] );
				}
			}
		}

		$email_design = array(
			'format' 				=> $_REQUEST['format'],
			'link_love' 			=> $_REQUEST['link_love'],
			'footer_text' 			=> $_REQUEST['footer_text'],
			'background_color' 		=> $_REQUEST['background_color'],
			'header_text' 			=> $_REQUEST['header_text'],
			'header_image' 			=> $header_image,
			'header_color' 			=> $_REQUEST['header_color'],
			'header_text_color' 	=> $_REQUEST['header_text_color'],
			'fieldset_color' 		=> $_REQUEST['fieldset_color'],
			'section_color' 		=> $_REQUEST['section_color'],
			'section_text_color' 	=> $_REQUEST['section_text_color'],
			'text_color' 			=> $_REQUEST['text_color'],
			'link_color' 			=> $_REQUEST['link_color'],
			'row_color' 			=> $_REQUEST['row_color'],
			'row_alt_color' 		=> $_REQUEST['row_alt_color'],
			'border_color' 			=> $_REQUEST['border_color'],
			'footer_color' 			=> $_REQUEST['footer_color'],
			'footer_text_color' 	=> $_REQUEST['footer_text_color'],
			'font_family' 			=> $_REQUEST['font_family'],
			'header_font_size' 		=> $_REQUEST['header_font_size'],
			'fieldset_font_size' 	=> $_REQUEST['fieldset_font_size'],
			'section_font_size' 	=> $_REQUEST['section_font_size'],
			'text_font_size' 		=> $_REQUEST['text_font_size'],
			'footer_font_size' 		=> $_REQUEST['footer_font_size']
		);

		// Update form details
		$wpdb->update(
			$this->form_table_name,
			array(
				'form_email_design' => serialize( $email_design )
			),
			array(
				'form_id' => $form_id
			),
			array(
				'%s', // form_email_design
			),
			array(
				'%d', // form_id
			)
		);
	}

	/**
	 * Handle deleting the email image header on the VFB Pro > Email Design page
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_email_delete_header() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'vfb-email-design' !== $_GET['page'] )
			return;

		if ( 'email_delete_header' !== $_REQUEST['action'] )
			return;

		$form_id = absint( $_REQUEST['form'] );

		check_admin_referer( 'delete-header-img-' . $form_id );

		$email = unserialize( $wpdb->get_var( $wpdb->prepare( "SELECT form_email_design FROM $this->form_table_name WHERE form_id = %d", $form_id ) ) );

		foreach( $email as $field => &$value ) {
			$value = ( 'header_image' !== $field ) ? $value : '';
		}

		$wpdb->update(
			$this->form_table_name,
			array(
				'form_email_design' => serialize( $email )
			),
			array(
				'form_id' => $form_id
			),
			array(
				'%s', // form_email_design
			),
			array(
				'%d', // form_id
			)
		);

		// Redirect to keep the URL clean (use AJAX in the future?)
		wp_redirect( 'admin.php?page=vfb-email-design' );
		exit();
	}

	/**
	 * Handle data migration from free version to Pro
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_upgrade() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'visual-form-builder-pro' !== $_GET['page'] )
			return;

		if ( 'vfb-upgrade' !== $_REQUEST['action'] )
			return;

		// Set database names of free version
		$vfb_fields   = $wpdb->prefix . 'visual_form_builder_fields';
		$vfb_forms    = $wpdb->prefix . 'visual_form_builder_forms';
		$vfb_entries  = $wpdb->prefix . 'visual_form_builder_entries';

		// Get all forms, fields, and entries
		$forms = $wpdb->get_results( "SELECT * FROM $vfb_forms ORDER BY form_id" );

		// Truncate the tables in case any forms or fields have been added
		$wpdb->query( "TRUNCATE TABLE $this->form_table_name" );
		$wpdb->query( "TRUNCATE TABLE $this->field_table_name" );
		$wpdb->query( "TRUNCATE TABLE $this->entries_table_name" );

		// Setup email design defaults
		$email_design = array(
			'format' 				=> 'html',
			'link_love' 			=> 'yes',
			'footer_text' 			=> '',
			'background_color' 		=> '#eeeeee',
			'header_text'			=> '',
			'header_image' 			=> '',
			'header_color' 			=> '#810202',
			'header_text_color' 	=> '#ffffff',
			'fieldset_color' 		=> '#680606',
			'section_color' 		=> '#5C6266',
			'section_text_color' 	=> '#ffffff',
			'text_color' 			=> '#333333',
			'link_color' 			=> '#1b8be0',
			'row_color' 			=> '#ffffff',
			'row_alt_color' 		=> '#eeeeee',
			'border_color' 			=> '#cccccc',
			'footer_color' 			=> '#333333',
			'footer_text_color' 	=> '#ffffff',
			'font_family' 			=> 'Arial',
			'header_font_size' 		=> 32,
			'fieldset_font_size' 	=> 20,
			'section_font_size' 	=> 15,
			'text_font_size' 		=> 13,
			'footer_font_size' 		=> 11
		);

		// Migrate all forms, fields, and entries
		foreach ( $forms as $form ) :

			// Set email header text default as form subject
			$email_design['header_text'] = $form->form_email_subject;

			$data = array(
				'form_id' 						=> $form->form_id,
				'form_key' 						=> $form->form_key,
				'form_title' 					=> $form->form_title,
				'form_email_subject' 			=> $form->form_email_subject,
				'form_email_to' 				=> $form->form_email_to,
				'form_email_from' 				=> $form->form_email_from,
				'form_email_from_name' 			=> $form->form_email_from_name,
				'form_email_from_override' 		=> $form->form_email_from_override,
				'form_email_from_name_override' => $form->form_email_from_name_override,
				'form_success_type' 			=> $form->form_success_type,
				'form_success_message' 			=> $form->form_success_message,
				'form_notification_setting' 	=> $form->form_notification_setting,
				'form_notification_email_name' 	=> $form->form_notification_email_name,
				'form_notification_email_from' 	=> $form->form_notification_email_from,
				'form_notification_email' 		=> $form->form_notification_email,
				'form_notification_subject' 	=> $form->form_notification_subject,
				'form_notification_message' 	=> $form->form_notification_message,
				'form_notification_entry' 		=> $form->form_notification_entry,
				'form_email_design' 			=> serialize( $email_design ),
				'form_label_alignment' 			=> '',
				'form_verification' 			=> 1,
				'form_entries_allowed' 			=> '',
				'form_entries_schedule'			=> '',
				'form_unique_entry'				=> 0
			);

			$wpdb->insert( $this->form_table_name, $data );

			$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $vfb_fields WHERE form_id = %d ORDER BY field_id", $form->form_id ) );
			// Copy each field and data
			foreach ( $fields as $field ) {

				$data = array(
					'field_id' 			=> $field->field_id,
					'form_id' 			=> $field->form_id,
					'field_key' 		=> $field->field_key,
					'field_type' 		=> $field->field_type,
					'field_name' 		=> $field->field_name,
					'field_description' => $field->field_description,
					'field_options' 	=> $field->field_options,
					'field_sequence' 	=> $field->field_sequence,
					'field_validation' 	=> $field->field_validation,
					'field_required' 	=> $field->field_required,
					'field_size' 		=> $field->field_size,
					'field_css' 		=> $field->field_css,
					'field_layout' 		=> $field->field_layout,
					'field_parent' 		=> $field->field_parent,
					'field_default'		=> $field->field_default,
				);

				$wpdb->insert( $this->field_table_name, $data );
			}

			$entries = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $vfb_entries WHERE form_id = %d ORDER BY entries_id", $form->form_id ) );

			// Copy each entry
			foreach ( $entries as $entry ) {

				$data = array(
					'form_id' 			=> $entry->form_id,
					'data' 				=> $entry->data,
					'subject' 			=> $entry->subject,
					'sender_name' 		=> $entry->sender_name,
					'sender_email' 		=> $entry->sender_email,
					'emails_to' 		=> $entry->emails_to,
					'date_submitted' 	=> $entry->date_submitted,
					'ip_address'	 	=> $entry->ip_address
				);

				$wpdb->insert( $this->entries_table_name, $data );
			}

		endforeach;

		// Automatically deactivate free version of Visual Form Builder, if active
		if ( is_plugin_active( 'visual-form-builder/visual-form-builder.php' ) )
			deactivate_plugins( '/visual-form-builder/visual-form-builder.php' );

		// Set upgrade as complete so admin notice closes
		update_option( 'vfb_db_upgrade', 1 );
	}

	/**
	 * Update entry on VFB Pro > Entries (detail)
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_entry_update() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'vfb-entries' !== $_GET['page'] )
			return;

		if ( 'update_entry' !== $_REQUEST['action'] )
			return;

		if ( !isset( $_POST['entry_id'] ) )
			return;

		$entry_id = absint( $_POST['entry_id'] );

		check_admin_referer( 'update-entry-' . $entry_id );

		// Get this entry's data
		$entry = $wpdb->get_var( $wpdb->prepare( "SELECT data FROM $this->entries_table_name WHERE entries_id = %d", $entry_id ) );

		$data = unserialize( $entry );

		// Loop through each field in the update form and save in a way we can use
		foreach ( $_POST['field'] as $key => $value ) {
			$fields[ $key ] = $value;
		}

		foreach ( $data as $key => $value ) :

			$id = $data[ $key ]['id'];

			// Special case for checkbox and radios not showing up in $_POST
			if ( !isset( $fields[ $id ] ) && in_array( $data[ $key ][ 'type' ], array( 'checkbox', 'radio' ) ) )
				$data[ $key ]['value'] = '';

			// Only update value if set in $_POST
			if ( isset( $fields[ $id ] ) ) {
				if ( in_array( $data[ $key ][ 'type' ], array( 'checkbox' ) ) )
					$data[ $key ]['value'] = implode( ', ', $fields[ $id ] );
				else
					$data[ $key ]['value'] = esc_html( $fields[ $id ] );
			}
		endforeach;

		$where = array( 'entries_id' => $entry_id );
		// Update entry data
		$wpdb->update( $this->entries_table_name, array( 'data' => serialize( $data ), 'notes' => $_REQUEST['entries-notes'] ), $where );
	}

	/**
	 * Save options on the VFB Pro > Settings page
	 *
	 * @access public
	 * @since 2.4.3
	 * @return void
	 */
	public function save_settings() {

		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'vfb-settings' !== $_GET['page'] )
			return;

		if ( 'vfb_settings' !== $_REQUEST['action'] )
			return;

		check_admin_referer( 'vfb-update-settings' );

		$data = array();

		foreach ( $_POST['vfb-settings'] as $key => $val ) {
			$data[ $key ] = esc_html( $val );
		}

		update_option( 'vfb-settings', $data );
	}

	/**
	 * The jQuery field sorting callback
	 *
	 * @since 1.0
	 */
	public function ajax_sort_field() {
		global $wpdb;

		$data = array();

		foreach ( $_REQUEST['order'] as $k ) :
			if ( 'root' !== $k['item_id'] && !empty( $k['item_id'] ) ) :
				$data[] = array(
					'field_id' 	=> $k['item_id'],
					'parent' 	=> $k['parent_id']
				);
			endif;
		endforeach;

		foreach ( $data as $k => $v ) :
			// Update each field with it's new sequence and parent ID
			$wpdb->update( $this->field_table_name, array(
				'field_sequence'	=> $k,
				'field_parent'  	=> $v['parent'] ),
				array( 'field_id' => $v['field_id'] ),
				'%d'
			);
		endforeach;

		die(1);
	}

	/**
	 * The jQuery create field callback
	 *
	 * @since 1.9
	 */
	public function ajax_create_field() {
		global $wpdb;

		$data = array();
		$field_options = $field_validation = $parent = $previous = '';

		foreach ( $_REQUEST['data'] as $k ) {
			$data[ $k['name'] ] = $k['value'];
		}

		check_ajax_referer( 'create-field-' . $data['form_id'], 'nonce' );

		$form_id     = absint( $data['form_id'] );
		$field_key   = sanitize_title( $_REQUEST['field_type'] );
		$field_type  = strtolower( sanitize_title( $_REQUEST['field_type'] ) );

		$parent      = ( isset( $_REQUEST['parent'] ) && $_REQUEST['parent'] > 0 ) ? $_REQUEST['parent'] : 0;
		$previous    = ( isset( $_REQUEST['previous'] ) && $_REQUEST['previous'] > 0 ) ? $_REQUEST['previous'] : 0;

		// If a Page Break, the default name is Next, otherwise use the field type
		$field_name = ( 'page-break' == $field_type ) ? 'Next' : $_REQUEST['field_type'];

		// Set defaults for validation
		switch ( $field_type ) :
			case 'select' :
			case 'radio' :
			case 'checkbox' :
				$field_options = serialize( array( 'Option 1', 'Option 2', 'Option 3' ) );
				break;

			case 'email' :
			case 'url' :
			case 'phone' :
				$field_validation = $field_type;
				break;

			case 'currency' :
				$field_validation = 'number';
				break;

			case 'number' :
				$field_validation = 'digits';
				break;

			case 'min' :
			case 'max' :
				$field_validation = 'digits';
				$field_options = serialize( array( '10' ) );
				break;

			case 'range' :
				$field_validation = 'digits';
				$field_options = serialize( array( '1', '10' ) );
				break;

			case 'time' :
				$field_validation = 'time-12';
				break;

			case 'file-upload' :
				$field_options = serialize( array( 'png|jpe?g|gif' ) );
				break;

			case 'ip-address' :
				$field_validation = 'ipv6';
				break;

			case 'hidden' :
			case 'custom-field' :
				$field_options = serialize( array( '' ) );
				break;

			case 'autocomplete' :
				$field_validation = 'auto';
				$field_options = serialize( array( 'Option 1', 'Option 2', 'Option 3' ) );
				break;

			case 'name' :
				$field_options = serialize( array( 'normal' ) );
				break;

			case 'date' :
				$field_options = serialize( array( 'dateFormat' => 'mm/dd/yy' ) );
				break;

			case 'rating' :
				$field_options = serialize( array( 'negative' => 'Disagree', 'positive' => 'Agree', 'scale' => '10' ) );
				break;

			case 'likert' :
				$field_options = serialize( array( 'rows' => "Ease of Use\nPortability\nOverall", 'cols' => "Strongly Disagree\nDisagree\nUndecided\nAgree\nStrongly Agree" ) );
				break;
		endswitch;



		// Get fields info
		$all_fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE form_id = %d ORDER BY field_sequence ASC", $form_id ) );
		$field_sequence = 0;

		// We only want the fields that FOLLOW our parent or previous item
		if ( $parent > 0 || $previous > 0 ) {
			$cut_off = ( $previous > 0 ) ? $previous : $parent;

			foreach( $all_fields as $field_index => $field ) {
				if ( $field->field_id == $cut_off ) {
					$field_sequence = $field->field_sequence + 1;
					break;
				}
				else
					unset( $all_fields[ $field_index ] );
			}
			array_shift( $all_fields );

			// If the previous had children, we need to remove them so our item is placed correctly
			if ( !$parent && $previous > 0 ) {
				foreach( $all_fields as $field_index => $field ) {
					if ( !$field->field_parent )
						break;
					else {
						$field_sequence = $field->field_sequence + 1;
						unset( $all_fields[ $field_index ] );
					}
				}
			}
		}

		// Create the new field's data
		$newdata = array(
			'form_id' 			=> absint( $data['form_id'] ),
			'field_key' 		=> $field_key,
			'field_name' 		=> $field_name,
			'field_type' 		=> $field_type,
			'field_options' 	=> $field_options,
			'field_sequence' 	=> $field_sequence,
			'field_validation' 	=> $field_validation,
			'field_parent' 		=> $parent
		);

		// Create the field
		$wpdb->insert( $this->field_table_name, $newdata );
		$insert_id = $wpdb->insert_id;

		// VIP fields
		$vip_fields = array( 'verification', 'secret', 'submit' );

		// Rearrange the fields that follow our new data
		foreach( $all_fields as $field_index => $field ) {
			if ( !in_array( $field->field_type, $vip_fields ) ) {
				$field_sequence++;
				// Update each field with it's new sequence and parent ID
				$wpdb->update( $this->field_table_name, array( 'field_sequence' => $field_sequence ), array( 'field_id' => $field->field_id ) );
			}
		}

		// Move the VIPs
		foreach ( $vip_fields as $update ) {
			$field_sequence++;
			$where = array(
				'form_id' 		=> absint( $data['form_id'] ),
				'field_type' 	=> $update
			);
			$wpdb->update( $this->field_table_name, array( 'field_sequence' => $field_sequence ), $where );

		}

		echo $this->field_output( $data['form_id'], $insert_id );

		die(1);
	}


	/**
	 * The jQuery delete field callback
	 *
	 * @since 1.9
	 */
	public function ajax_delete_field() {
		global $wpdb;

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'visual_form_builder_delete_field' ) {
			$form_id     = absint( $_REQUEST['form'] );
			$field_id    = absint( $_REQUEST['field'] );

			check_ajax_referer( 'delete-field-' . $form_id, 'nonce' );

			if ( isset( $_REQUEST['child_ids'] ) ) {
				foreach ( $_REQUEST['child_ids'] as $children ) {
					$parent = absint( $_REQUEST['parent_id'] );

					// Update each child item with the new parent ID
					$wpdb->update( $this->field_table_name, array( 'field_parent' => $parent ), array( 'field_id' => $children ) );
				}
			}

			// Delete the field
			$wpdb->query( $wpdb->prepare( "DELETE FROM $this->field_table_name WHERE field_id = %d", $field_id ) );
		}

		die(1);
	}

	/**
	 * The jQuery create field callback
	 *
	 * @since 1.9
	 */
	public function ajax_duplicate_field() {
		global $wpdb;

		$form_id     = absint( $_REQUEST['form'] );
		$field_id    = absint( $_REQUEST['field'] );

		check_ajax_referer( 'duplicate-field-' . $form_id, 'nonce' );

		// Get fields info
		$this_field = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE field_id = %d", $field_id ) );
		$all_fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE form_id = %d ORDER BY field_sequence ASC", $form_id ) );
		$field_sequence = 0;

		// We only want the fields that FOLLOW our field
		foreach( $all_fields as $field_index => $field ) {
			if ( $field->field_id == $field_id ) {
				$field_sequence = $field->field_sequence + 1;
				break;
			}
			else
				unset( $all_fields[ $field_index ] );
		}
		array_shift( $all_fields );

		foreach ( $this_field as $field ) {
			$field_key              = $field->field_key;
			$field_type             = $field->field_type;
			$field_options          = $field->field_options;
			$field_options_other    = $field->field_options_other;
			$field_description		= $field->field_description;
			$field_name				= $field->field_name;
			$field_parent			= $field->field_parent;
			$field_validation		= $field->field_validation;
			$field_required			= $field->field_required;
			$field_size				= $field->field_size;
			$field_css				= $field->field_css;
			$field_layout			= $field->field_layout;
			$field_default			= $field->field_default;
		}

		// Create the new field's data
		$newdata = array(
			'form_id'               => $form_id,
			'field_key'             => $field_key,
			'field_type'            => $field_type,
			'field_options'         => $field_options,
			'field_options_other'   => $field_options_other,
			'field_description'     => $field_description,
			'field_name'            => $field_name,
			'field_sequence'        => $field_sequence,
			'field_parent'          => $field_parent,
			'field_validation'      => $field_validation,
			'field_required'        => $field_required,
			'field_size'            => $field_size,
			'field_css'             => $field_css,
			'field_layout'          => $field_layout,
			'field_default'         => $field_default,
		);

		// Create the field
		$wpdb->insert( $this->field_table_name, $newdata );
		$insert_id = $wpdb->insert_id;

		// VIP fields
		$vip_fields = array( 'verification', 'secret', 'submit' );

		// Rearrange the fields that follow our new data
		foreach( $all_fields as $field_index => $field ) {
			if ( !in_array( $field->field_type, $vip_fields ) ) {
				$field_sequence++;
				// Update each field with it's new sequence and parent ID
				$wpdb->update( $this->field_table_name, array( 'field_sequence' => $field_sequence ), array( 'field_id' => $field->field_id ) );
			}
		}

		// Move the VIPs
		foreach ( $vip_fields as $update ) {
			$field_sequence++;
			$where = array(
				'form_id' 		=> $form_id,
				'field_type' 	=> $update
			);
			$wpdb->update( $this->field_table_name, array( 'field_sequence' => $field_sequence ), $where );

		}

		echo $this->field_output( $form_id, $insert_id );

		die(1);
	}

	/**
	 * Display Bulk Add Options pop-up
	 *
	 * Activated by the Bulk Add Options link button which references the AJAX name
	 *
	 * @since 1.6
	 */
	public function ajax_bulk_add(){
		$field_id = absint( $_REQUEST['field_id'] );
	?>
<div id="vfb_bulk_add">
	<form id="vfb_bulk_add_options" class="media-upload-form type-form validate">
		<h3 class="media-title">Bulk Add Options</h3>
		<ol>
			<li>Select from the predefined categories</li>
			<li>If needed, customize the options. Place each option on a new line.</li>
			<li>Add to your field</li>
		</ol>
		<?php
			$bulk_options = $days = $years = array();

			// Build Days array
			for ( $i = 1; $i <= 31; ++$i ) {
				$days[] = $i;
			}

			//Build Years array
			for ( $i = date( 'Y' ); $i >= 1925; --$i ) {
				$years[] = $i;
			}

			$bulk_options = array(
				'U.S. States'		=> array( 'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming' ),

				'U.S. States Abbreviations'	=> array( 'AK','AL','AR','AS','AZ','CA','CO','CT','DC','DE','FL','GA','GU','HI','IA','ID', 'IL','IN','KS','KY','LA','MA','MD','ME','MH','MI','MN','MO','MS','MT','NC','ND','NE','NH','NJ','NM','NV','NY', 'OH','OK','OR','PA','PR','PW','RI','SC','SD','TN','TX','UT','VA','VI','VT','WA','WI','WV','WY' ),

				'Countries'			=> $this->countries,
				'Days of the Week'	=> array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
				'Days'				=> $days,
				'Months'			=> array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
				'Years'				=> $years,
				'Gender'			=> array( 'Male', 'Female', 'Prefer not to answer' ),
				'Age Range'			=> array( 'Under 18', '18 - 24', '25 - 34', '35 - 44', '45 - 54', '55 - 64', '65 or older', 'Prefer not to answer' ),
				'Marital Status'	=> array( 'Single', 'Married', 'Divorced', 'Separated', 'Widowed', 'Domestic Partner', 'Unmarried Partner', 'Prefer not to answer' ),
				'Ethnicity'			=> array( 'American Indian/Alaskan Native', 'Asian', 'Native Hawaiian or Other Pacific Islander', 'Black or African-American', 'White', 'Not disclosed' ),
				'Prefix'			=> array( 'Mr.', 'Mrs.', 'Ms.', 'Miss', 'Dr.' ),
				'Suffix'			=> array( 'Sr.', 'Jr.', 'Ph.D', 'M.D' ),
				'Agree'				=> array( 'Strongly Agree', 'Agree', 'Neutral', 'Disagree', 'Strongly Disagree', 'N/A' ),
				'Education'			=> array( 'Some High School', 'High School/GED', 'Some College', 'Associate\'s Degree', 'Bachelor\'s Degree', 'Master\'s Degree', 'Doctoral Degree', 'Professional Degree' )
			);

			$more_options = apply_filters( 'vfb_bulk_add_options', array() );

			// Merge our pre-defined bulk options with possible additions via filter
			$bulk_options = array_merge( $bulk_options, $more_options );
		?>
		<div id="bulk-options-left">
			<ul>
			<?php foreach ( $bulk_options as $name => $values ) : ?>
				<li>
					<a id="<?php echo $name; ?>" class="vfb-bulk-options" href="#"><?php echo $name; ?></a>
					<ul style="display:none;">
					<?php foreach ( $values as $value ) : ?>
						<li><?php echo $value; ?></li>
					<?php endforeach; ?>
					</ul>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		<div id="bulk-options-right">
			<textarea id="choicesText" class="textarea" name="choicesText"></textarea>
			<p><input type="submit" class="button-primary" value="Add Options" /></p>
			<input type="hidden" name="bulk-add-field-id" id="bulk-add-field-id" value="<?php echo $field_id; ?>">
		</div>
	</form>
</div>

	<?php
		die(1);
	}

	/**
	 * Display the conditional fields builder
	 *
	 * @since 1.9
	 */
	public function ajax_conditional_fields() {
		global $wpdb;

		$form_id = absint( $_REQUEST['form_id'] );
		$field_id = absint( $_REQUEST['field_id'] );

		// Get the field name and cache the query for the other variables
		$field_name = $wpdb->get_var( $wpdb->prepare( "SELECT field_name, field_key, field_rule_setting, field_rule FROM $this->field_table_name WHERE field_id = %d AND form_id = %d ORDER BY field_sequence ASC", $field_id, $form_id ) );
		$field_key	 		= $wpdb->get_var( null, 1 );
		$field_rule_setting = $wpdb->get_var( null, 2 );
		$rules 				= unserialize( $wpdb->get_var( null, 3 ) );

		// Only get checkbox, select, and radio for list of options
		$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE field_type IN('checkbox', 'select', 'radio') AND form_id = %d ORDER BY field_sequence ASC", $form_id ) );
		$field_options = $wpdb->get_var( null, 4 );

		// Display the conditional rules if setting is on
		$display = ( $field_rule_setting ) ? ' class="show-fields"' : '';

		// Count the number of rules for our index
		$num_fields = count( $rules['rules'] );

		if ( !$fields ) :
			echo sprintf( '<div class="warning error"><p>%s</p></div>', __( 'A Checkbox, Select, or Radio field is needed in order to perform conditional logic.', 'visual-form-builder-pro' ) );
		else :
	?>
<div id="vfb-conditional-fields">
	<form id="vfb-add-conditional-fields" class="media-upload-form type-form validate">
		<h3 class="media-title">Conditional Field Rules</h3>
			<label for="vfb-conditional-setting">
				<input type="checkbox" name="conditional_setting" id="vfb-conditional-setting" value="1" <?php checked( $field_rule_setting, '1' ); ?> />
				<?php _e( 'Enable Conditional Rule for this field', 'visual-form-builder-pro' ); ?>
			</label>

			<div id="vfb-build-conditional-fields-container"<?php echo $display; ?>>
					<?php if ( 1 == $field_rule_setting ) : ?>
					<p><select name="conditional_show">
						<option value="show" <?php selected( $rules['conditional_show'], 'show' ); ?>><?php _e( 'Show', 'visual-form-builder-pro' ); ?></option>
						<option value="hide" <?php selected( $rules['conditional_show'], 'hide' ); ?>><?php _e( 'Hide', 'visual-form-builder-pro' ); ?></option>
					</select> the <strong><?php echo esc_html( stripslashes( $field_name ) ); ?></strong> field based on
					<select name="conditional_logic">
						<option value="all" <?php selected( $rules['conditional_logic'], 'all' ); ?>><?php _e( 'all', 'visual-form-builder-pro' ); ?></option>
						<option value="any" <?php selected( $rules['conditional_logic'], 'any' ); ?>><?php _e( 'any', 'visual-form-builder-pro' ); ?></option>
					</select>
					 of the following rules:
					</p>

					<?php for ( $i = 0; $i < $num_fields; $i++ ) : ?>

					<div class="vfb-conditional-fields-data">
						if <select name="rules[<?php echo $i; ?>][field]" class="vfb-conditional-other-fields">
							<?php foreach ( $fields as $field ) : ?>
								<option value="<?php echo $field->field_id; ?>" <?php selected( $rules['rules'][ $i ]['field'], $field->field_id ); ?>><?php echo esc_html( stripslashes( $field->field_name ) ); ?></option>
							<?php endforeach; ?>
						</select>

						<select name="rules[<?php echo $i; ?>][condition]>" class="vfb-conditional-condition">
							<option value="is" <?php selected( $rules['rules'][ $i ]['condition'], 'is' ); ?>><?php _e( 'is', 'visual-form-builder-pro' ); ?></option>
							<option value="isnot" <?php selected( $rules['rules'][ $i ]['condition'], 'isnot' ); ?>><?php _e( 'is not', 'visual-form-builder-pro' ); ?></option>
						</select>
						<?php
							$these_opts = $wpdb->get_var( $wpdb->prepare( "SELECT field_options FROM $this->field_table_name WHERE field_id = %d ORDER BY field_sequence ASC", $rules['rules'][ $i ]['field'] ) );
						?>
						<select name="rules[<?php echo $i; ?>][option]" class="vfb-conditional-other-fields-options">
							<?php
							if ( !empty( $these_opts ) ) {
								$options = maybe_unserialize( $these_opts );

								foreach ( $options as $option ) {
								?>
									<option value="<?php echo esc_attr( stripslashes( $option ) ); ?>" <?php selected( esc_attr( stripslashes( $rules['rules'][ $i ]['option'] ) ), esc_html( stripslashes( $option ) ) ); ?>><?php echo esc_html( stripslashes( $option ) ); ?></option>
								<?php }
							}
							?>
						</select>

						<a href="#" class="vfb-add-condition vfb-interface-icon vfb-interface-plus" title="Add Condition">
							<?php _e( 'Add', 'visual-form-builder-pro' ); ?>
						</a>
						<a href="#" class="vfb-delete-condition vfb-interface-icon vfb-interface-minus" title="Delete Condition">
							<?php _e( 'Delete', 'visual-form-builder-pro' ); ?>
						</a>
					</div> <!-- #vfb-conditional-fields-data -->
					<?php endfor; ?>

					<?php else: ?>

					<p><select name="conditional_show">
						<option value="show"><?php _e( 'Show', 'visual-form-builder-pro' ); ?></option>
						<option value="hide"><?php _e( 'Hide', 'visual-form-builder-pro' ); ?></option>
					</select> the <strong><?php echo esc_html( stripslashes( $field_name ) ); ?></strong> field based on
					<select name="conditional_logic">
						<option value="all"><?php _e( 'all', 'visual-form-builder-pro' ); ?></option>
						<option value="any"><?php _e( 'any', 'visual-form-builder-pro' ); ?></option>
					</select> of the following rules:
					</p>

					<div class="vfb-conditional-fields-data">
						if <select name="rules[0][field]" class="vfb-conditional-other-fields">
							<?php foreach ( $fields as $field ) : ?>
								<option value="<?php echo $field->field_id; ?>"><?php echo esc_html( stripslashes( $field->field_name ) ); ?></option>
							<?php endforeach; ?>
						</select>
						<select name="rules[0][condition]>" class="vfb-conditional-condition">
							<option value="is"><?php _e( 'is', 'visual-form-builder-pro' ); ?></option>
							<option value="isnot"><?php _e( 'is not', 'visual-form-builder-pro' ); ?></option>
						</select>
						<select name="rules[0][option]" class="vfb-conditional-other-fields-options">
						<?php
							if ( !empty( $field_options ) ) {
								$options = maybe_unserialize( $field_options );

								foreach ( $options as $option ) {
									echo sprintf( '<option value="%1$s">%2$s</option>', esc_attr( stripslashes( $option ) ), esc_html( stripslashes( $option ) ) );
								}
							}
						?>
						</select>
						<a href="#" class="vfb-add-condition vfb-interface-icon vfb-interface-plus" title="Add Condition">
							<?php _e( 'Add', 'visual-form-builder-pro' ); ?></a>
						<a href="#" class="vfb-delete-condition vfb-interface-icon vfb-interface-minus" title="Delete Condition">
							<?php _e( 'Delete', 'visual-form-builder-pro' ); ?>
						</a>
					</div> <!-- #vfb-conditional-fields-data -->
					<?php endif; ?>
			</div> <!-- #vfb-build-conditional-fields-container -->
		<input type="hidden" name="field_id" value="<?php echo $field_id; ?>" />
		<p>
			<input type="submit" class="button-primary" value="Save" />
			<span id="vfb-saving-conditional">
				<img alt="" src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" class="waiting spinner"  />
				<div id="vfb-saving-conditonal-error"></div>
			</span>
		</p>
	</form>
</div> <!-- #vfb-conditional-fields -->
	<?php
		endif;

		die(1);
	}

	/**
	 * AJAX callback for the conditional fields options
	 *
	 * @since 1.9
	 */
	public function ajax_conditional_fields_options() {
		global $wpdb;

		$field_id = absint( $_REQUEST['field_id'] );

		$field_options = $wpdb->get_var( $wpdb->prepare( "SELECT field_options FROM $this->field_table_name WHERE field_id = %d ORDER BY field_sequence ASC", $field_id ) );

		$first_options = '';
		if ( !empty( $field_options ) ) {
			$options = maybe_unserialize( $field_options );

			foreach ( $options as $option ) {
				$first_options .= sprintf( '<option value="%1$s">%2$s</option>', esc_attr( stripslashes( $option ) ), esc_html( stripslashes( $option ) ) );
			}
		}

		echo $first_options;

		die(1);
	}

	/**
	 * Save the conditional fields
	 *
	 * @since 1.9
	 */
	public function ajax_conditional_fields_save() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) )
			return;

		if ( $_REQUEST['action'] == 'visual_form_builder_conditional_fields_save' ) {

			wp_parse_str( $_REQUEST['data'], $data );

			// Reset the array index in case it's become mangled during cloning
			$conditions = array_values( $data['rules'] );

			// Reload the rules back into our $data array
			$data['rules'] = $conditions;

			$field_id 		= absint( $data['field_id'] );
			$rule_setting 	= ( isset( $data['conditional_setting'] ) ) ? absint( $data['conditional_setting'] ) : 0;
			$rules 			= ( 1 == $rule_setting ) ? serialize( $data ) : '';

			$new_data = array(
				'field_rule_setting'	=> $rule_setting,
				'field_rule' 			=> $rules,
			);

			$result = $wpdb->update( $this->field_table_name, $new_data, array( 'field_id' => $field_id ) );

			// Return an error if updating failed
			if ( false === $result )
				echo 'Saving failed. Conditional fields not updated.';
		}

		die(1);
	}

	public function ajax_email_rules() {
		global $wpdb;

		$form_id = absint( $_REQUEST['form_id'] );

		// Get the field name and cache the query for the other variables
		$email_rules = $wpdb->get_var( $wpdb->prepare(
			"SELECT form_email_rule, form_email_rule_setting
			FROM $this->form_table_name
			WHERE form_id = %d",
			$form_id )
		);

		$email_rule_setting = $wpdb->get_var( null, 1 );
		$rules 		= maybe_unserialize( $email_rules );

		// Only get checkbox, select, and radio for list of options
		$fields = $wpdb->get_results( $wpdb->prepare(
			"SELECT *
			FROM $this->field_table_name
			WHERE field_type IN('select', 'radio') AND form_id = %d
			ORDER BY field_sequence ASC",
			$form_id )
		);

		$field_options = $wpdb->get_var( null, 4 );

		// Display the conditional rules if setting is on
		$display = ( $email_rule_setting ) ? ' class="show-fields"' : '';

		// Count the number of rules for our index
		$num_fields = count( $rules['rules'] );

		if ( !$fields ) {
			echo sprintf( '<div class="warning error"><p>%s</p></div>', __( 'A Select, or Radio field is needed in order to setup email rules.', 'visual-form-builder-pro' ) );
			wp_die();
		}
?>
<div id="vfb-email-rules">
	<form id="vfb-add-email-rules" class="media-upload-form type-form validate">
		<h3 class="media-title">Conditional Field Rules</h3>
			<p><?php _e( 'This option will allow you to send additional emails to different addresses based on user selection.', 'visual-form-builder-pro' ); ?></p>
			<p>
			<label for="vfb-email-rule-setting">
				<input type="checkbox" name="form_email_rule_setting" id="vfb-email-rule-setting" value="1" <?php checked( $email_rule_setting, '1' ); ?> />
				<?php _e( 'Enable Email Rules', 'visual-form-builder-pro' ); ?>
			</label>
			</p>
			<div id="vfb-build-email-rules-container"<?php echo $display; ?>>

			<?php if ( 1 == $email_rule_setting ) : ?>
				<?php for ( $i = 0; $i < $num_fields; $i++ ) : ?>

				<div class="vfb-email-rules-data">
					if <select name="rules[<?php echo $i; ?>][field]" class="vfb-conditional-other-fields">
						<?php foreach ( $fields as $field ) : ?>
							<option value="<?php echo $field->field_id; ?>" <?php selected( $rules['rules'][ $i ]['field'], $field->field_id ); ?>><?php echo esc_html( stripslashes( $field->field_name ) ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php _e( 'is', 'visual-form-builder-pro' ); ?>
					<?php
						$these_opts = $wpdb->get_var( $wpdb->prepare( "SELECT field_options FROM $this->field_table_name WHERE field_id = %d ORDER BY field_sequence ASC", $rules['rules'][ $i ]['field'] ) );
					?>
					<select name="rules[<?php echo $i; ?>][option]" class="vfb-conditional-other-fields-options">
						<?php
						if ( !empty( $these_opts ) ) {
							$options = maybe_unserialize( $these_opts );

							foreach ( $options as $option ) { ?>
								<option value="<?php echo esc_attr( stripslashes( $option ) ); ?>" <?php selected( esc_attr( stripslashes( $rules['rules'][ $i ]['option'] ) ), esc_html( stripslashes( $option ) ) ); ?>><?php echo esc_html( stripslashes( $option ) ); ?></option>
							<?php }
						}
						?>
					</select>
					<?php _e( 'email', 'visual-form-builder-pro' ); ?>
					<input type="text" name="rules[<?php echo $i; ?>][email]" class="vfb-conditional-condition" value="<?php esc_attr_e( $rules['rules'][$i]['email'] ); ?>">

					<a href="#" class="vfb-add-email-rule vfb-interface-icon vfb-interface-plus" title="Add Condition">
						<?php _e( 'Add', 'visual-form-builder-pro' ); ?>
					</a>
					<a href="#" class="vfb-delete-email-rule vfb-interface-icon vfb-interface-minus" title="Delete Condition">
						<?php _e( 'Delete', 'visual-form-builder-pro' ); ?>
					</a>
				</div> <!-- #vfb-conditional-fields-data -->
				<?php endfor; ?>
			<?php else : ?>
				<div class="vfb-email-rules-data">
					if <select name="rules[0][field]" class="vfb-conditional-other-fields">
						<?php foreach ( $fields as $field ) : ?>
							<option value="<?php echo $field->field_id; ?>"><?php echo esc_html( stripslashes( $field->field_name ) ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php _e( 'is', 'visual-form-builder-pro' ); ?>
					<select name="rules[0][option]" class="vfb-conditional-other-fields-options">
					<?php
						if ( !empty( $field_options ) ) {
							$options = maybe_unserialize( $field_options );

							foreach ( $options as $option ) {
								echo sprintf( '<option value="%1$s">%2$s</option>', esc_attr( stripslashes( $option ) ), esc_html( stripslashes( $option ) ) );
							}
						}
					?>
					</select>
					<?php _e( 'email', 'visual-form-builder-pro' ); ?>
					<input type="text" name="rules[0][email]" class="vfb-conditional-condition" value="">

					<a href="#" class="vfb-add-email-rule vfb-interface-icon vfb-interface-plus" title="Add Condition">
						<?php _e( 'Add', 'visual-form-builder-pro' ); ?></a>
					<a href="#" class="vfb-delete-email-rule vfb-interface-icon vfb-interface-minus" title="Delete Condition">
						<?php _e( 'Delete', 'visual-form-builder-pro' ); ?>
					</a>
				</div> <!-- #vfb-conditional-fields-data -->
			<?php endif; ?>
			</div> <!-- #vfb-build-email-rules-container -->
		<input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />
		<p>
			<input type="submit" class="button-primary" value="Save" />
			<span id="vfb-saving-email-rule">
				<img alt="" src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" class="waiting spinner"  />
				<div id="vfb-saving-email-rule-error"></div>
			</span>
		</p>
	</form>
</div> <!-- #vfb-email-rules -->
<?php
		wp_die();
	}

	/**
	 * Save the conditional fields
	 *
	 * @since 1.9
	 */
	public function ajax_email_rules_save() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) )
			return;

		if ( $_REQUEST['action'] == 'visual_form_builder_email_rules_save' ) {

			wp_parse_str( $_REQUEST['data'], $data );

			// Reset the array index in case it's become mangled during cloning
			$conditions = array_values( $data['rules'] );

			// Reload the rules back into our $data array
			$data['rules'] = $conditions;

			$form_id 		= absint( $data['form_id'] );
			$rule_setting 	= isset( $data['form_email_rule_setting'] ) ? absint( $data['form_email_rule_setting'] ) : 0;
			$rules 			= ( 1 == $rule_setting ) ? serialize( $data ) : '';

			$new_data = array(
				'form_email_rule_setting'=> $rule_setting,
				'form_email_rule'        => $rules,
			);

			$result = $wpdb->update( $this->form_table_name, $new_data, array( 'form_id' => $form_id ) );

			// Return an error if updating failed
			if ( false === $result )
				echo 'Saving failed. Email rules not updated.';
		}

		die(1);
	}

	/**
	 * The jQuery PayPal Assign Price to Fields callback
	 *
	 * @since 1.0
	 */
	public function ajax_paypal_price() {
		global $wpdb;

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'visual_form_builder_paypal_price' ) {
			$form_id = absint( $_REQUEST['form_id'] );
			$field_id = absint( $_REQUEST['field_id'] );

			$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE form_id = %d AND field_id = %d", $form_id, $field_id ) );
			$paypal_price_field = unserialize( $wpdb->get_var( $wpdb->prepare( "SELECT form_paypal_field_price FROM $this->form_table_name WHERE form_id = %d", $form_id ) ) );

			$price_option = '';

			foreach ( $fields as $field ) {
				// If a text input field, only display a message
				if ( in_array( $field->field_type, array( 'text', 'currency' ) ) )
					$price_option = '<p>Amount Based on User Input</p>';
				// If field has options, let user assign prices to inputs
				elseif ( in_array( $field->field_type, array( 'select', 'radio', 'checkbox' ) ) ) {
					$options = ( is_array( unserialize( $field->field_options ) ) ) ? unserialize( $field->field_options ) : explode( ',', unserialize( $field->field_options ) );

					// Loop through each option and output
					foreach ( $options as $option => $value ) {
						$price_field_amount = ( isset( $paypal_price_field['prices'] ) ) ? stripslashes( $paypal_price_field['prices'][$option]['amount'] ) : '';

						$price_option .= sprintf(
							'<p class="description description-wide"><label>%1$s<input class="widefat required" type="text" value="%2$s" name="form_paypal_field_price[prices][%3$d][amount]" /></label><br></p>',
							esc_attr( stripslashes( $value ) ),
							$price_field_amount,
							$option
						);

						echo sprintf( '<input type="hidden" name="form_paypal_field_price[prices][%1$d][id]" value="%2$s" />', $option, esc_attr( stripslashes( $value ) ) );
					}
				}

				// Store the name as vfb-field_key-field_id for comparison when setting up PayPal form redirection
				echo sprintf( '<input type="hidden" name="form_paypal_field_price[name]" value="vfb-%d" />', $field->field_id );
			}

			echo $price_option;
		}

		die(1);
	}

	/**
	 * Form Settings dropdown saving
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_form_settings() {
		global $current_user;
		get_currentuserinfo();

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'visual_form_builder_form_settings' ) {
			$form_id     = absint( $_REQUEST['form'] );
			$status      = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'opened';
			$accordion   = isset( $_REQUEST['accordion'] ) ? $_REQUEST['accordion'] : 'general-settings';
			$user_id     = $current_user->ID;

			$form_settings = get_user_meta( $user_id, 'vfb-form-settings', true );

			$array = array(
				'form_setting_tab' 	=> $status,
				'setting_accordion' => $accordion
			);

			// Set defaults if meta key doesn't exist
			if ( !$form_settings || $form_settings == '' ) {
				$meta_value[ $form_id ] = $array;

				update_user_meta( $user_id, 'vfb-form-settings', $meta_value );
			}
			else {
				$form_settings[ $form_id ] = $array;

				update_user_meta( $user_id, 'vfb-form-settings', $form_settings );
			}
		}

		die(1);
	}

	/**
	 * Form sorting callback
	 *
	 * @since 1.8
	 */
	public function ajax_form_order() {
		global $wpdb, $current_user;

		get_currentuserinfo();

		$data = array();

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'visual_form_builder_form_order' ) {
			$user_id = $current_user->ID;

			$form_order = get_user_meta( $user_id, 'vfb-form-order', true );

			foreach ( $_REQUEST['order'] as $k ) {
				preg_match( '/(\d+$)/', $k, $matches );
				$data[] = $matches[1];
			}

			// Set defaults if meta key doesn't exist
			if ( !$form_order || $form_order == '' ) {
				$meta_value = $data;

				update_user_meta( $user_id, 'vfb-form-order', $meta_value );
			}
			else {
				$form_order = $data;

				update_user_meta( $user_id, 'vfb-form-order', $form_order );
			}
		}

		die(1);
	}

	/**
	 * The Google Chart bar chart callback
	 *
	 * @since 1.0
	 */
	public function ajax_graphs() {
		global $wpdb;

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'visual_form_builder_graphs' ) {
			$form_id 	= absint( $_REQUEST['form'] );
			$view 		= esc_html( $_REQUEST['view'] );
			$start 		= esc_html( $_REQUEST['date_start'] );
			$end 		= esc_html( $_REQUEST['date_end'] );

			$date_where = $avg = '';
			$date = $rows = array();
			$i = 1;

			switch( $view ) :
				case 'monthly' :
					$where = 'Year, Month';
					$d = 'Y-m';
					break;

				case 'weekly' :
					$where = 'Year, Week';
					$d = 'Y \WW';
					break;

				case 'daily' :
					$where = 'Year, Month, Day';
					$d = 'Y-m-d';
					break;
			endswitch;

			if ( $start !== '0' )
				$date_where .= $wpdb->prepare( " AND date_submitted >= %s", date( 'Y-m-d', strtotime( $start ) ) );
			if ( $end !== '0' )
				$date_where .= $wpdb->prepare( " AND date_submitted < %s", date( 'Y-m-d', strtotime('+1 month', strtotime( $end ) ) ) );

			// Get counts of the entries based on the Date/view set above
			$entries = $wpdb->get_results( $wpdb->prepare( "SELECT DAY( date_submitted ) AS Day, MONTH( date_submitted ) as Month, WEEK( date_submitted ) as Week, YEAR( date_submitted ) as Year, COUNT(*) as Count FROM $this->entries_table_name WHERE form_id = %d $date_where GROUP BY $where ORDER BY $where", $form_id ) );

			// Send back empty values if nothing found
			if ( !$entries ) {
				echo '{"entries": [{"date": "0", "count": 0}]}';
				die(1);
			}

			// Loop through entries and setup our array for JSON output
			foreach ( $entries as $entry ) {
				$date[] = array(
					'date' 		=> date( $d, mktime( 0, 0, 0, $entry->Month, $entry->Day, $entry->Year ) ),
					'count' 	=> $entry->Count
				);
			}

			// Setup our JSON output array
			foreach ( $date as $val ) {
				$avg += $val[ 'count' ];
				$daily_average = round( ( $avg / $i ), 2 );

				$rows[] = '{"date": "' . $val['date'] . '", "count": ' . $val['count'] . ', "avg": ' . $daily_average . '}';

				$i++;
			}

			// Comma separate each row
			echo '{"entries": [' . implode( ',', $rows ) . ']}';
		}

		die(1);
	}

	/**
	 * Display the additional media button
	 *
	 * Used for inserting the form shortcode with desired form ID
	 *
	 * @since 1.4
	 */
	public function ajax_media_button(){
		global $wpdb;

		// Sanitize the sql orderby
		$order = sanitize_sql_orderby( 'form_id ASC' );
		$forms = $wpdb->get_results( "SELECT form_id, form_title FROM $this->form_table_name WHERE form_status != 'trash' ORDER BY $order" );
	?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$( '#add_vfb_form' ).submit(function(e){
			        e.preventDefault();

			        window.send_to_editor( '[vfb id=' + $( '#vfb_forms' ).val() + ']' );

			        window.tb_remove();
			    });
			});
	    </script>
		<div id="vfb_form">
			<form id="add_vfb_form" class="media-upload-form type-form validate">
				<h3 class="media-title">Insert Visual Form Builder Form</h3>
				<p>Select a form below to insert into any Post or Page.</p>
				<select id="vfb_forms" name="vfb_forms">
					<?php foreach( $forms as $form ) : ?>
						<option value="<?php echo $form->form_id; ?>"><?php echo $form->form_id; ?> - <?php echo $form->form_title; ?></option>
					<?php endforeach; ?>
				</select>
				<p><input type="submit" class="button-primary" value="Insert Form" /></p>
			</form>
		</div>
	<?php
		die(1);
	}

	/**
	 * The jQuery field autocomplete callback
	 *
	 * @since 1.0
	 */
	public function ajax_autocomplete() {
		global $wpdb;

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'visual_form_builder_autocomplete' ) {
			$term 		= esc_html( $_REQUEST['term'] );
			$form_id 	= absint( $_REQUEST['form'] );
			$field_id 	= absint( $_REQUEST['field'] );

			$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE form_id = %d AND field_id = %d ORDER BY field_sequence ASC", $form_id, $field_id ) );

			$suggestions = array();

			foreach ( $fields as $field ) {
				$options = unserialize( $field->field_options );

				foreach ( $options as $opts ){
					// Find a match in our list of options
					$pos = stripos( $opts, $term );

					// If a match was found, add it to the suggestions
					if ( $pos !== false )
						$suggestions[] = array( 'value' => $opts );
				}

				// Send a JSON-encoded array to our AJAX call
				echo json_encode( $suggestions );
			}
		}

		die(1);
	}

	/**
	 * The jQuery unique username callback
	 *
	 * @since 1.0
	 */
	public function ajax_check_username() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) )
			return;

		if ( 'visual_form_builder_check_username' !== $_REQUEST['action'] )
			return;

		$username = esc_html( $_REQUEST['username'] );
		$user_id  = username_exists( $username );
		$valid    = 'true';

		// If username exists, not valid
		if ( $user_id )
			$valid = 'false';

		echo $valid;

		die(1);
	}

	/**
	 * The jQuery unique username callback
	 *
	 * @since 1.0
	 */
	public function ajax_check_recaptcha() {
		global $wpdb;

		if ( !isset( $_REQUEST['action'] ) )
			return;

		if ( $_REQUEST['action'] !== 'visual_form_builder_check_recaptcha' )
			return;

		$vfb_settings   = get_option( 'vfb-settings' );
		$private_key    = $vfb_settings['recaptcha-private-key'];

		if ( !function_exists( 'recaptcha_get_html' ) )
	    	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/libraries/recaptchalib.php' );

		$resp = recaptcha_check_answer( $private_key,
	        $_SERVER['REMOTE_ADDR'],
	        $_POST['recaptcha_challenge_field'],
	        $_POST['recaptcha_response_field']
	    );

		$valid = 'true';

	    if ( !$resp->is_valid )
	    	$valid = 'false';

	    echo $valid;

		die(1);
	}

	/**
	 * Form order type callback
	 *
	 * @since 1.8
	 */
	public function form_order_type() {
		global $current_user;

		get_currentuserinfo();

		$data = array();

		if ( !isset( $_REQUEST['page'] ) )
			return;

		if ( 'visual-form-builder-pro' !== $_REQUEST['page'] )
			return;

		if ( isset( $_REQUEST['mode'] ) ) :
			$user_id = $current_user->ID;

			$type = get_user_meta( $user_id, 'vfb-form-order-type', true );

			$meta_value = ( in_array( $_REQUEST['mode'], array( 'order', 'list' ) ) ) ? esc_html( $_REQUEST['mode'] ) : '';
			update_user_meta( $user_id, 'vfb-form-order-type', $meta_value );

		endif;
	}

	/**
	 * All Forms output in admin
	 *
	 * @since 1.9
	 */
	public function all_forms() {
		global $wpdb, $current_user, $forms_order, $forms_list;

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

		// List view is default sorting
		$order = sanitize_sql_orderby( 'form_title ASC' );

		// Sort based on custom order, if mode is selected
		if ( in_array( $user_form_order_type, array( 'order' ) ) )
			$order = ( isset( $form_order ) ) ? "FIELD( form_id, $form_order )" : sanitize_sql_orderby( 'form_id DESC' );

		$where = apply_filters( 'vfb_pre_get_forms', '' );
		$forms = $wpdb->get_results( "SELECT form_id, form_title, form_paypal_setting FROM {$this->form_table_name} WHERE 1=1 $where ORDER BY $order" );

		if ( !$forms ) :
			echo '<div class="vfb-form-alpha-list"><h3 id="vfb-no-forms">You currently do not have any forms.  <a href="' . esc_url( admin_url( 'admin.php?page=vfb-add-new' ) ) . '">Click here to get started</a>.</h3></div>';
			return;
		endif;

		echo '<form id="forms-filter" method="post" action="">';

		// List view
		if ( in_array( $user_form_order_type, array( 'list', '' ) ) ) :
			$forms_list->views();
			$forms_list->prepare_items();

        	$forms_list->search_box( 'search', 'search_id' );
        	$forms_list->display();
		// Ordered view
		else :
			$forms_order->views();
			$forms_order->prepare_items();

			$forms_order->search_box( 'search', 'search_id' );
			$forms_order->display();

		endif;

		echo '</form>';
	}

	/**
	 * Build field output in admin
	 *
	 * @since 1.9
	 */
	public function field_output( $form_nav_selected_id, $field_id = NULL ) {
		require( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/admin-field-options.php' );
	}

	/**
	 * Add a menu to the WP admin toolbar
	 *
	 * @since 1.7
	 * @param object $wp_admin_bar
	 */
	public function admin_toolbar_menu( $wp_admin_bar ) {
		// Only display VFB toolbar if on a page with the shortcode
		if ( !is_admin() && current_user_can( 'vfb_edit_forms' ) ) {
			global $post;

			// If no post, exit
			if ( !$post )
				return;

			$post_to_check = get_post( get_the_ID() );

			// Finds content with the vfb shortcode
			if ( stripos( $post_to_check->post_content, '[vfb' ) !== false ) {
				preg_match_all( '/id=[\'"]?(\d+)[\'"]?/', $post_to_check->post_content, $matches );

				// If more than one form, display a new toolbar item with dropdown
				if ( count( $matches[1] ) > 1 ) {
					global $wpdb;

					$wp_admin_bar->add_node( array(
						'id' 		=> 'vfb_admin_toolbar_edit_main',
						'title'		=> 'Edit Forms',
						'parent'	=> false,
						'href'		=> admin_url( 'admin.php?page=visual-form-builder-pro' )
						)
					);

					// Loop through the forms
					foreach ( $matches[1] as $form_id ) {
						$name = $wpdb->get_var( $wpdb->prepare( "SELECT form_title FROM $this->form_table_name WHERE form_id = %d", $form_id ) );
						$wp_admin_bar->add_node( array(
							'id' 		=> 'vfb_admin_toolbar_edit_' . $form_id,
							'title'		=> 'Edit ' . stripslashes( $name ),
							'parent'	=> 'vfb_admin_toolbar_edit_main',
							'href'		=> admin_url( 'admin.php?page=visual-form-builder-pro&amp;action=edit&amp;form=' . $form_id )
							)
						);
					}
				} else {
					// A new toolbar item
					$wp_admin_bar->add_node( array(
						'id' 		=> 'vfb_admin_toolbar_edit_main',
						'title'		=> 'Edit Form',
						'parent'	=> false,
						'href'		=> admin_url( 'admin.php?page=visual-form-builder-pro&amp;action=edit&amp;form=' . $matches[1][0] )
						)
					);
					// An item added to the main VFB Pro menu
					$wp_admin_bar->add_node( array(
						'id' 		=> 'vfb_admin_toolbar_edit',
						'title'		=> 'Edit Form',
						'parent'	=> 'vfb_admin_toolbar',
						'href'		=> admin_url( 'admin.php?page=visual-form-builder-pro&amp;action=edit&amp;form=' . $matches[1][0] )
						)
					);
				}
			}
		}

		// Entire menu will be hidden if user does not have vfb_edit_forms cap
		if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], array( 'visual-form-builder-pro' ) ) && isset( $_REQUEST['form'] ) && current_user_can( 'vfb_edit_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_preview_form',
				'title'		=> 'Preview Form',
				'parent'	=> false,
				'href'		=> esc_url( add_query_arg( array( 'form' => $_REQUEST['form'], 'preview' => 1 ), plugins_url( 'visual-form-builder-pro/form-preview.php' ) ) ),
				'meta'		=> array( 'target' => '_blank' )
				)
			);
		}

		// Entire menu will be hidden if user does not have vfb_edit_forms cap
		if ( current_user_can( 'vfb_edit_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar',
				'title'		=> 'VFB Pro',
				'parent'	=> false,
				'href'		=> admin_url( 'admin.php?page=visual-form-builder-pro' )
				)
			);
		}

		// Add New Form
		if ( current_user_can( 'vfb_create_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_add',
				'title'		=> __( 'Add New Form', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-add-new' )
				)
			);
		}

		// Entries
		if ( current_user_can( 'vfb_view_entries' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_entries',
				'title'		=> __( 'Entries', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-entries' )
				)
			);
		}

		// Email Design
		if ( current_user_can( 'vfb_edit_email_design' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_email',
				'title'		=> __( 'Email Design', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-email-design' )
				)
			);
		}

		// Analytics
		if ( current_user_can( 'vfb_view_analytics' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_analytics',
				'title'		=> __( 'Analytics', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-reports' )
				)
			);
		}

		// Import
		if ( current_user_can( 'vfb_import_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_import',
				'title'		=> __( 'Import', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-import' )
				)
			);
		}

		// Export
		if ( current_user_can( 'vfb_export_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_export',
				'title'		=> __( 'Export', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-export' )
				)
			);
		}

		// Settings
		if ( current_user_can( 'vfb_edit_settings' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_settings',
				'title'		=> __( 'Settings', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-settings' )
				)
			);
		}

		// Payments
		if ( class_exists( 'VFB_Pro_Payments' ) && current_user_can( 'vfb_edit_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_payments',
				'title'		=> __( 'Payments', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-payments' )
				)
			);
		}

		// Display Entries
		if ( class_exists( 'VFB_Pro_Display_Entries' ) && current_user_can( 'vfb_edit_entries' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_display_entries',
				'title'		=> __( 'Display Entries', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-addon-display-entries' )
				)
			);
		}

		// Form Designer
		if ( class_exists( 'VFB_Pro_Form_Designer' ) && current_user_can( 'vfb_edit_email_design' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfb_admin_toolbar_form_designer',
				'title'		=> __( 'Form Design', 'visual-form-builder-pro' ),
				'parent'	=> 'vfb_admin_toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-addon-form-design' )
				)
			);
		}
	}

	/**
	 * Display admin notices
	 *
	 * @since 1.0
	 */
	public function admin_notices(){
		if ( !isset( $_REQUEST['action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( !in_array( $_GET['page'], array( 'visual-form-builder-pro', 'vfb-add-new', 'vfb-entries', 'vfb-email-design', 'vfb-reports', 'vfb-import', 'vfb-export', 'vfb-settings' ) ) )
			return;

		switch( $_REQUEST['action'] ) :
			case 'create_form' :
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', __( 'Form created.' , 'visual-form-builder-pro' ) );
				break;

			case 'update_form' :
				$preview_link = sprintf( __( 'Form updated. <a href="%s" target="_blank">View preview</a>' , 'visual-form-builder-pro' ), add_query_arg( array( 'form' => $_REQUEST['form_id'], 'preview' => 1 ), plugins_url( 'visual-form-builder-pro/form-preview.php' ) ) );
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', $preview_link );

				if ( $this->post_max_vars ) :
					// Get max post vars, if available. Otherwise set to 1000
					$max_post_vars = ( ini_get( 'max_input_vars' ) ) ? intval( ini_get( 'max_input_vars' ) ) : 1000;

					echo '<div id="message" class="error"><p>' . sprintf( __( 'Error saving form. The maximum amount of data allowed by your server has been reached. Please update <a href="%s" target="_blank">max_input_vars</a> in your php.ini file to allow more data to be saved. Current limit is <strong>%d</strong>', 'visual-form-builder-pro' ), 'http://www.php.net/manual/en/info.configuration.php#ini.max-input-vars', $max_post_vars ) . '</p></div>';
				endif;
				break;

			case 'trash' :
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', __( 'Item moved to the Trash.' , 'visual-form-builder-pro' ) );
				break;

			case 'delete' :
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', __( 'Item permanently deleted.' , 'visual-form-builder-pro' ) );
				break;

			case 'restore' :
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', __( 'Item restored from the Trash.' , 'visual-form-builder-pro' ) );
				break;

			case 'copy_form' :
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', __( 'Item successfully duplicated.' , 'visual-form-builder-pro' ) );
				break;

			case 'vfb-ignore-notice' :
				update_option( 'vfb_ignore_notice', 1 );
				break;

			case 'upgrade' :
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', __( 'You have successfully migrated to Visual Form Builder Pro!' , 'visual-form-builder-pro' ) );
				break;

			case 'email_design' :
				$preview_link = sprintf( __( 'Email design updated. <a href="%s" target="_blank">View preview</a>' , 'visual-form-builder-pro' ), add_query_arg( array( 'form' => $_REQUEST['form_id'], ), plugins_url( 'visual-form-builder-pro/email-preview.php' ) ) );
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', $preview_link );
				break;

			case 'update_entry' :
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', __( 'Entry updated.' , 'visual-form-builder-pro' ) );
				break;

			case 'vfb_settings' :
				echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', __( 'Settings saved.' , 'visual-form-builder-pro' ) );
				break;
		endswitch;
	}

	/**
	 * Add admin menu
	 *
	 * In addition to add menus and submenus
	 * this function also uses the menu hooks to load screen options,
	 * meta boxes, and include certain files
	 *
	 * @since 1.0
	 * @uses add_menu_page() Creates a menu item in the top level menu.
	 * @uses add_submenu_page() Creates a submenu item under the parent menu.
	 */
	public function add_admin() {
		$current_pages = array();

		$current_pages[ 'vfb-pro' ] = add_menu_page( __( 'VFB Pro CLASSIC', 'visual-form-builder-pro' ), __( 'VFB Pro CLASSIC', 'visual-form-builder-pro' ), 'vfb_edit_forms', 'visual-form-builder-pro', array( &$this, 'admin' ), 'dashicons-feedback' );

		add_submenu_page( 'visual-form-builder-pro', __( 'VFB Pro CLASSIC', 'visual-form-builder-pro' ), __( 'All Forms', 'visual-form-builder-pro' ), 'vfb_edit_forms', 'visual-form-builder-pro', array( &$this, 'admin' ) );

		$current_pages[ 'vfb-add-new' ] = add_submenu_page( 'visual-form-builder-pro', __( 'Add New Form', 'visual-form-builder-pro' ), __( 'Add New', 'visual-form-builder-pro' ), 'vfb_create_forms', 'vfb-add-new', array( &$this, 'admin_add_new' ) );
		$current_pages[ 'vfb-entries' ] = add_submenu_page( 'visual-form-builder-pro', __( 'Entries', 'visual-form-builder-pro' ), __( 'Entries', 'visual-form-builder-pro' ), 'vfb_view_entries', 'vfb-entries', array( &$this, 'admin_entries' ) );
		$current_pages[ 'vfb-email-design' ] = add_submenu_page( 'visual-form-builder-pro', __( 'Email Design', 'visual-form-builder-pro' ), __( 'Email Design', 'visual-form-builder-pro' ), 'vfb_edit_email_design', 'vfb-email-design', array( &$this, 'admin_email_design' ) );
		$current_pages[ 'vfb-analytics' ] = add_submenu_page( 'visual-form-builder-pro', __( 'Analytics', 'visual-form-builder-pro' ), __( 'Analytics', 'visual-form-builder-pro' ), 'vfb_view_analytics', 'vfb-reports', array( &$this, 'admin_analytics' ) );
		$current_pages[ 'vfb-import' ] = add_submenu_page( 'visual-form-builder-pro', __( 'Import', 'visual-form-builder-pro' ), __( 'Import', 'visual-form-builder-pro' ), 'vfb_import_forms', 'vfb-import', array( &$this, 'admin_import' ) );
		$current_pages[ 'vfb-export' ] = add_submenu_page( 'visual-form-builder-pro', __( 'Export', 'visual-form-builder-pro' ), __( 'Export', 'visual-form-builder-pro' ), 'vfb_export_forms', 'vfb-export', array( &$this, 'admin_export' ) );
		$current_pages[ 'vfb-settings' ] = add_submenu_page( 'visual-form-builder-pro', __( 'Settings', 'visual-form-builder-pro' ), __( 'Settings', 'visual-form-builder-pro' ), 'vfb_edit_settings', 'vfb-settings', array( &$this, 'admin_settings' ) );

		// All plugin page load hooks
		foreach ( $current_pages as $key => $page ) :
			// Load the jQuery and CSS we need if we're on our plugin page
			add_action( "load-$page", array( &$this, 'admin_scripts' ) );

			// Load the Help tab on all pages
			add_action( "load-$page", array( &$this, 'help' ) );
		endforeach;

		// Save pages array for filter/action use throughout plugin
		$this->_admin_pages = $current_pages;

		// Adds a Screen Options tab to the Entries screen
		add_action( 'load-' . $current_pages['vfb-pro'], array( &$this, 'screen_options' ) );
		add_action( 'load-' . $current_pages['vfb-entries'], array( &$this, 'screen_options' ) );

		// Add an Advanced Properties section to the Screen Options tab
		add_filter( 'manage_' . $current_pages['vfb-pro'] . '_columns', array( &$this, 'screen_advanced_options' ) );

		// Add meta boxes to the form builder admin page
		add_action( 'load-' . $current_pages['vfb-pro'], array( &$this, 'add_meta_boxes' ) );

		// Include Entries and Import files
		add_action( 'load-' . $current_pages['vfb-entries'], array( &$this, 'include_entries' ) );
		add_action( 'load-' . $current_pages['vfb-import'], array( &$this, 'include_import_export' ) );

		add_action( 'load-' . $current_pages['vfb-pro'], array( &$this, 'include_forms_list' ) );
	}

	/**
	 * Display Add New Form page
	 *
	 *
	 * @since 2.2.6
	 */
	public function admin_add_new() {
?>
	<div class="wrap">
		<h2><?php _e( 'Add New Form', 'visual-form-builder-pro' ); ?></h2>
<?php
		include_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/admin-new-form.php' );
?>
	</div>
<?php
	}

	/**
	 * Display Entries
	 *
	 *
	 * @since 2.2.6
	 */
	public function admin_entries() {
		global $wpdb, $entries_list, $entries_detail;
?>
	<div class="wrap">
		<h2>
			<?php _e( 'Entries', 'visual-form-builder-pro' ); ?>
<?php
			// If searched, output the query
			if ( isset( $_POST['s'] ) && !empty( $_POST['s'] ) )
				echo '<span class="subtitle">' . sprintf( __( 'Search results for "%s"' , 'visual-form-builder-pro'), esc_html( $_POST['s'] ) );
?>
		</h2>
<?php
		if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], array( 'view', 'edit', 'update_entry' ) ) ) :
			$entries_detail->entries_detail();
		else :
			$entries_list->entries_errors();
			$entries_list->views();
			$entries_list->prepare_items();
?>
    	<form id="entries-filter" method="post" action="">
<?php
        	$entries_list->search_box( 'search', 'search_id' );
        	$entries_list->display();
?>
        </form>
	<?php endif; ?>
	</div>
<?php
	}

	/**
	 * Display Add New Form page
	 *
	 *
	 * @since 2.2.6
	 */
	public function admin_email_design() {
?>
	<div class="wrap">
		<h2><?php _e( 'Email Design', 'visual-form-builder-pro' ); ?></h2>
<?php
		$design = new VisualFormBuilder_Pro_Designer();
		$design->design_options();
?>
	</div>
<?php
	}

	/**
	 * Display Analytics page
	 *
	 *
	 * @since 2.2.6
	 */
	public function admin_analytics() {
?>
	<div class="wrap">
		<h2><?php _e( 'Analytics', 'visual-form-builder-pro' ); ?></h2>
<?php
		$analytics = new VisualFormBuilder_Pro_Analytics();
		$analytics->display();
?>
	</div>
<?php
	}

	/**
	 * Display Import
	 *
	 *
	 * @since 2.2.6
	 */
	public function admin_import() {
		global $import;
?>
	<div class="wrap">
		<h2><?php _e( 'Import', 'visual-form-builder-pro' ); ?></h2>
<?php
		$import->display();
?>
	</div>
<?php
	}

	/**
	 * Display Export
	 *
	 *
	 * @since 2.2.6
	 */
	public function admin_export() {
		global $export;
?>
	<div class="wrap">
		<h2><?php _e( 'Export', 'visual-form-builder-pro' ); ?></h2>
<?php
		$export->display();
?>
	</div>
<?php
	}

	/**
	 * admin_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_settings() {

		$vfb_settings = get_option( 'vfb-settings' );
?>
	<div class="wrap">
		<h2><?php _e( 'Settings', 'visual-form-builder-pro' ); ?></h2>
		<form id="vfb-settings" method="post">
			<input name="action" type="hidden" value="vfb_settings" />
			<?php wp_nonce_field( 'vfb-update-settings' ); ?>
			<p><?php _e( 'These settings will affect all forms on your site. For explanations of what these settings mean, click on the Help tab above.', 'visual-form-builder-pro' ); ?></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'CSS', 'visual-form-builder-pro' ); ?></th>
					<td>
						<fieldset>
						<?php
							$disable = array(
								'always-load-css'     => __( 'Always load CSS', 'visual-form-builder-pro' ),
								'disable-css'         => __( 'Disable CSS', 'visual-form-builder-pro' ),	// visual-form-builder-css
							);

							foreach ( $disable as $key => $title ) :

								$vfb_settings[ $key ] = isset( $vfb_settings[ $key ] ) ? $vfb_settings[ $key ] : '';
						?>
							<label for="vfb-settings-<?php echo $key; ?>">
								<input type="checkbox" name="vfb-settings[<?php echo $key; ?>]" id="vfb-settings-<?php echo $key; ?>" value="1" <?php checked( $vfb_settings[ $key ], 1 ); ?> /> <?php echo $title; ?>
							</label>
							<br>
						<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Email', 'visual-form-builder-pro' ); ?></th>
					<td>
						<fieldset>
						<?php
							$disable = array(
								'disable-email'       => __( 'Disable Email', 'visual-form-builder-pro' ),	// vfb_send_email
								'disable-email-notify'=> __( 'Disable Notification Email', 'visual-form-builder-pro' ),	// vfb_send_notify_email
								'skip-empties'        => __( 'Skip Empty Fields in Email', 'visual-form-builder-pro' ),	// vfb_skip_empty_fields
							);

							foreach ( $disable as $key => $title ) :

								$vfb_settings[ $key ] = isset( $vfb_settings[ $key ] ) ? $vfb_settings[ $key ] : '';
						?>
							<label for="vfb-settings-<?php echo $key; ?>">
								<input type="checkbox" name="vfb-settings[<?php echo $key; ?>]" id="vfb-settings-<?php echo $key; ?>" value="1" <?php checked( $vfb_settings[ $key ], 1 ); ?> /> <?php echo $title; ?>
							</label>
							<br>
						<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Entries', 'visual-form-builder-pro' ); ?></th>
					<td>
						<fieldset>
						<?php
							$disable = array(
								'save-entry'          => __( 'Disable saving new entry', 'visual-form-builder-pro' ),	// vfb_entries_save_new
								'save-ip'             => __( "Disable saving entry's IP address", 'visual-form-builder-pro' ),	// vfb_entries_save_ip
							);

							foreach ( $disable as $key => $title ) :

								$vfb_settings[ $key ] = isset( $vfb_settings[ $key ] ) ? $vfb_settings[ $key ] : '';
						?>
							<label for="vfb-settings-<?php echo $key; ?>">
								<input type="checkbox" name="vfb-settings[<?php echo $key; ?>]" id="vfb-settings-<?php echo $key; ?>" value="1" <?php checked( $vfb_settings[ $key ], 1 ); ?> /> <?php echo $title; ?>
							</label>
							<br>
						<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Form Output', 'visual-form-builder-pro' ); ?></th>
					<td>
						<fieldset>
						<?php
							$disable = array(
								'address-labels'      => __( 'Place Address labels above fields', 'visual-form-builder-pro' ),	// vfb_address_labels_placement
								'spam-verification'   => __( 'Remove default SPAM Verification', 'visual-form-builder-pro' ),	// vfb_display_verification
								'show-version'        => __( 'Disable meta tag version', 'visual-form-builder-pro' ),	// vfb_show_version
							);

							foreach ( $disable as $key => $title ) :

								$vfb_settings[ $key ] = isset( $vfb_settings[ $key ] ) ? $vfb_settings[ $key ] : '';
						?>
							<label for="vfb-settings-<?php echo $key; ?>">
								<input type="checkbox" name="vfb-settings[<?php echo $key; ?>]" id="vfb-settings-<?php echo $key; ?>" value="1" <?php checked( $vfb_settings[ $key ], 1 ); ?> /> <?php echo $title; ?>
							</label>
							<br>
						<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'After Submit', 'visual-form-builder-pro' ); ?></th>
					<td>
						<fieldset>
						<?php
							$disable = array(
								'skip-total-zero'     => __( 'Skip PayPal redirect if total is zero', 'visual-form-builder-pro' ),	// vfb_skip_total_zero
								'prepend-confirm'     => __( 'Prepend Confirmation', 'visual-form-builder-pro' ),	// vfb_prepend_confirmation
							);

							foreach ( $disable as $key => $title ) :

								$vfb_settings[ $key ] = isset( $vfb_settings[ $key ] ) ? $vfb_settings[ $key ] : '';
						?>
							<label for="vfb-settings-<?php echo $key; ?>">
								<input type="checkbox" name="vfb-settings[<?php echo $key; ?>]" id="vfb-settings-<?php echo $key; ?>" value="1" <?php checked( $vfb_settings[ $key ], 1 ); ?> /> <?php echo $title; ?>
							</label>
							<br>
						<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="vfb-settings-spam-points"><?php _e( 'Spam word sensitivity', 'visual-form-builder-pro' ); ?></label></th>
					<td>
						<?php $vfb_settings['spam-points'] = isset( $vfb_settings['spam-points'] ) ? $vfb_settings['spam-points'] : '4'; ?>
						<input type="number" min="1" name="vfb-settings[spam-points]" id="vfb-settings-spam-points" value="<?php echo $vfb_settings['spam-points']; ?>" class="small-text" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="vfb-settings-max-upload-size"><?php _e( 'Max Upload Size', 'visual-form-builder-pro' ); ?></label></th>
					<td>
						<?php $vfb_settings['max-upload-size'] = isset( $vfb_settings['max-upload-size'] ) ? $vfb_settings['max-upload-size'] : '25'; ?>
						<input type="number" name="vfb-settings[max-upload-size]" id="vfb-settings-max-upload-size" value="<?php echo $vfb_settings['max-upload-size']; ?>" class="small-text" /> MB
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="vfb-settings-sender-mail-header"><?php _e( 'Sender Mail Header', 'visual-form-builder-pro' ); ?></label></th>
					<td>
						<?php
						// Use the admin_email as the From email
						$from_email = get_site_option( 'admin_email' );

						// Get the site domain and get rid of www.
						$sitename = strtolower( $_SERVER['SERVER_NAME'] );
						if ( substr( $sitename, 0, 4 ) == 'www.' )
							$sitename = substr( $sitename, 4 );

						// Get the domain from the admin_email
						list( $user, $domain ) = explode( '@', $from_email );

						// If site domain and admin_email domain match, use admin_email, otherwise a same domain email must be created
						$from_email = ( $sitename == $domain ) ? $from_email : "wordpress@$sitename";

						$vfb_settings['sender-mail-header'] = isset( $vfb_settings['sender-mail-header'] ) ? $vfb_settings['sender-mail-header'] : $from_email;
						?>
						<input type="text" name="vfb-settings[sender-mail-header]" id="vfb-settings-sender-mail-header" value="<?php echo $vfb_settings['sender-mail-header']; ?>" class="regular-text" />
						<p class="description"><?php _e( 'Some server configurations require an existing email on the domain be used when sending emails.', 'visual-form-builder-pro' ); ?></p>
					</td>
				</tr>
			</table>

			<h3><?php _e( 'reCAPTCHA Settings', 'visual-form-builder-pro' ); ?></h3>

			<p><?php _e( 'Using <a href="http://www.google.com/recaptcha/" target="blank">reCAPTCHA</a> in Visual Form Builder Pro will replace the standard Text Captcha. Note: only one form with reCAPTCHA is allowed per page.', 'visual-form-builder-pro' ); ?></p>
			<table class="form-table">
				<?php
					$recap = array(
						'recaptcha-public-key' => __( 'reCAPTCHA Public Key', 'visual-form-builder-pro' ),
						'recaptcha-private-key' => __( 'reCAPTCHA Private Key', 'visual-form-builder-pro' ),
					);

					foreach ( $recap as $key => $title ) :
						$vfb_settings[ $key ] = isset( $vfb_settings[ $key ] ) ? $vfb_settings[ $key ] : '';
				?>
				<tr valign="top">
					<th scope="row"><label for="vfb-<?php echo $key; ?>"><?php echo $title; ?></label></th>
					<td>
						<input type="text" name="vfb-settings[<?php echo $key; ?>]" id="vfb-<?php echo $key; ?>" value="<?php echo $vfb_settings[ $key ]; ?>" class="regular-text" />
						<p class="description"><?php _e( 'Required if "Use reCAPTCHA" option is selected in the Secret field.', 'visual-form-builder-pro' ); ?></p>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>

			<?php if ( class_exists( 'VFB_Pro_Create_User' ) ) : ?>
			<h3><?php _e( 'Create User Settings', 'visual-form-builder-pro' ); ?></h3>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="vfb-create-user-role"><?php _e( 'Role', 'visual-form-builder-pro' ); ?></label></th>
					<td>
						<?php $vfb_settings[ 'create-user-role' ] = isset( $vfb_settings[ 'create-user-role' ] ) ? $vfb_settings[ 'create-user-role' ] : ''; ?>
						<select name="vfb-settings[create-user-role]" id="vfb-create-user-role">
						<?php wp_dropdown_roles( $vfb_settings[ 'create-user-role' ] ); ?>
						</select>
					</td>
				</tr>
			</table>
			<?php endif; ?>

			<?php if ( class_exists( 'VFB_Pro_Create_Post' ) ) : ?>
			<h3><?php _e( 'Create Post Settings', 'visual-form-builder-pro' ); ?></h3>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="vfb-create-post-type"><?php _e( 'Post Type', 'visual-form-builder-pro' ); ?></label></th>
					<td>
						<?php $vfb_settings[ 'create-post-type' ] = isset( $vfb_settings[ 'create-post-type' ] ) ? $vfb_settings[ 'create-post-type' ] : 'post'; ?>
						<select name="vfb-settings[create-post-type]" id="vfb-create-post-type">
						<?php
						$post_types = get_post_types( '', 'names' );

						foreach ( $post_types as $post_type ) {
						   printf( '<option value="%1$s"%2$s>%1$s</option>', $post_type, selected( $vfb_settings[ 'create-post-type' ], $post_type, 0 ) );
						}
						?>
						</select>
					</td>
				</tr>
			</table>
			<?php endif; ?>

			<?php submit_button( __( 'Save', 'visual-form-builder-pro' ), 'primary', 'submit', false ); ?>
		</form>
	</div>
<?php
	}

	/**
	 * Builds the options settings page
	 *
	 * @since 1.0
	 */
	public function admin() {
		global $wpdb, $current_user;

		get_currentuserinfo();

		// Save current user ID
		$user_id = $current_user->ID;

		// Get the Form Order type settings, if any
		$user_form_order_type = get_user_meta( $user_id, 'vfb-form-order-type', true );

		$form_nav_selected_id = ( isset( $_REQUEST['form'] ) ) ? $_REQUEST['form'] : '0';
	?>
	<div class="wrap">
		<h2>
			<?php _e( 'VFB Pro CLASSIC', 'visual-form-builder-pro' ); ?>
<?php
			// Add New link
			echo sprintf( ' <a href="%1$s" class="add-new-h2">%2$s</a>', esc_url( admin_url( 'admin.php?page=vfb-add-new' ) ), esc_html( __( 'Add New', 'visual-form-builder-pro' ) ) );

			// If searched, output the query
			if ( isset( $_REQUEST['s'] ) && !empty( $_REQUEST['s'] ) )
				echo '<span class="subtitle">' . sprintf( __( 'Search results for "%s"' , 'visual-form-builder-pro'), $_REQUEST['s'] );
?>
		</h2>
<?php
			// Display form editor or the form list
			if ( isset( $_GET['form'] ) && 'edit' == $_GET['action'] ) :
				include_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/admin-form-creator.php' );
			else :
?>
			<div id="vfb-form-list">
				<div id="vfb-main" class="vfb-order-type-<?php echo ( in_array( $user_form_order_type, array( 'order', '' ) ) ) ? 'order' : 'list'; ?>">
				<?php $this->all_forms(); ?>
				</div> <!-- #vfb-main -->
			</div> <!-- #vfb-form-list -->
<?php
			endif;
?>
	</div>
	<?php
	}

	/**
	 * Handle confirmation when form is submitted
	 *
	 * @since 1.3
	 */
	function confirmation(){
		global $wpdb;

		$form_id = ( isset( $_REQUEST['form_id'] ) ) ? (int) esc_html( $_REQUEST['form_id'] ) : '';

		if ( !isset( $_REQUEST['vfb-submit'] ) )
			return;

		do_action( 'vfb_confirmation', $form_id, $this->new_entry_id );

		// Allow custom query arguments to be appended to redirects
		$query_args = apply_filters( 'vfb_redirect_query_args', '', $form_id, $this->new_entry_id );

		// Get forms
		$order = sanitize_sql_orderby( 'form_id DESC' );
		$forms 	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->form_table_name WHERE form_id = %d ORDER BY $order", $form_id ) );

		foreach ( $forms as $form ) :

			// If user wants this to redirect to PayPal
			if ( $form->form_paypal_setting ) {

				$paypal_data = array(
					'paypal_field'	=> $form->form_paypal_field_price,
					'item_name'		=> $form->form_paypal_item_name,
					'currency_code'	=> $form->form_paypal_currency,
					'tax_rate'		=> $form->form_paypal_tax,
					'shipping'		=> $form->form_paypal_shipping,
					'business'		=> $form->form_paypal_email,
				);

				$this->paypal_redirect( $paypal_data, $form_id );
			}

			// Allow templating within confirmation message
			$form->form_success_message = $this->templating( $form->form_success_message );

			// Apply a filter for the success message
			$form->form_success_message = apply_filters( 'vfb_form_success_message', $form->form_success_message, $form_id, $form->form_success_type );

			// If text, return output and format the HTML for display
			if ( 'text' == $form->form_success_type ) :
				return stripslashes( html_entity_decode( wp_kses_stripslashes( $form->form_success_message ) ) );
			// If page, redirect to the permalink
			elseif ( 'page' == $form->form_success_type ) :
				$page = get_permalink( $form->form_success_message );
				wp_redirect( esc_url_raw( $page . $query_args ) );
				exit();
			// If redirect, redirect to the URL
			elseif ( 'redirect' == $form->form_success_type ) :
				wp_redirect( esc_url_raw( $form->form_success_message . $query_args ) );
				exit();
			// Display Entry with Text message prepended
			elseif ( 'display-entry' == $form->form_success_type ) :
				// At least output the Text message
				$output = stripslashes( html_entity_decode( wp_kses_stripslashes( $form->form_success_message ) ) );

				// Only add entry to output if Display Entries is active
				if ( class_exists( 'VFB_Pro_Display_Entries' ) ) {
					$output .= vfb_display_entries( array(
						'entry_id' 	=> $this->new_entry_id,
						'echo'		=> 0
					));
				}

				return $output;
			endif;

		endforeach;
	}

	/**
	 * PayPal redirection
	 *
	 * @access public
	 * @param mixed $data
	 * @param mixed $form_id
	 * @return void
	 */
	public function paypal_redirect( $data, $form_id ) {

		// Get global settings
		$vfb_settings 	= get_option( 'vfb-settings' );

		// Settings - Disable meta tag version
		$settings_skip_total_zero	= isset( $vfb_settings['skip-total-zero'] ) ? true : false;

		extract( $data );

		$output = $query_string = '';

		$paypal_url 	= 'https://www.paypal.com/cgi-bin/webscr';
		$paypal_command	= '_xclick';
		$account_email 	= ( !empty( $business ) ) ? sanitize_email( $business ) : '';
		$item_name	 	= ( !empty( $item_name ) ) ? esc_html( $item_name ) : '';
		$currency_code 	= ( !empty( $currency_code ) ) ? esc_html( $currency_code ) : 'USD';
		$tax_rate 		= ( !empty( $tax_rate ) ) ? esc_html( $tax_rate ) : '';
		$shipping	 	= ( !empty( $shipping ) ) ? esc_html( $shipping ) : '';
		$paypal_field 	= ( !empty( $paypal_field ) ) ? unserialize( $paypal_field ) : '';

		$data = array(
			'cmd'            => $paypal_command,
			'business'       => $account_email,
			'item_name'  	 => $item_name,
			'currency_code'  => $currency_code,
			'tax_rate'		 => $tax_rate,
			'shipping'		 => $shipping,
		);

		// By default, amount based on user input
		$amount = ( is_array( $_REQUEST[ $paypal_field['name'] ] ) ) ? $_REQUEST[ $paypal_field['name'] ][0] : stripslashes( $_REQUEST[ $paypal_field['name' ] ] );

		// If multiple PayPal prices are set, loop through them
		if ( $paypal_field['prices'] && is_array( $paypal_field['prices'] ) ) :
			// Loop through prices and if multiple, amount is from select/radio/checkbox
			foreach ( $paypal_field['prices'] as $prices ) :
				// If it's a checkbox, account for that
				$name = ( is_array( $_REQUEST[ $paypal_field['name'] ] ) ) ? $_REQUEST[ $paypal_field['name'] ][0] : $_REQUEST[ $paypal_field['name'] ];

				if ( $prices['id'] == $name )
					$amount = $prices['amount'];
			endforeach;
		endif;

		$data['amount'] = $amount;

		$skip_total_zero = apply_filters( 'vfb_skip_total_zero', $settings_skip_total_zero, $form_id );

		if ( $skip_total_zero && empty( $data['amount'] ) )
			return;

		$extra_vars = apply_filters( 'vfb_paypal_extra_vars', array(), $form_id );

		// Merge our PayPal data with possible additions via filter
		$data = array_merge( $data, $extra_vars );

		// Build query string for PayPal URL
		foreach ( array_keys( $data ) as $k ) {
			$query_string .= $k . '=' . urlencode( $data[ $k ] ) . '&';
		}

		wp_redirect( "$paypal_url?$query_string" );
		exit();
	}

	/**
	 * Get conditional fields for the selected form
	 *
	 * @since 1.9
	 * @return json encoded array || false if no rules found
	 */
	private function get_conditional_fields( $form_id ) {
		global $wpdb;

		$rules = $wpdb->get_results( $wpdb->prepare( "SELECT field_id, field_rule FROM $this->field_table_name WHERE field_rule_setting = 1 AND field_rule != '' AND form_id = %d", $form_id ) );

		if ( !$rules )
			return false;

		$conditions = array();

		foreach ( $rules as $rule ) {
			$conditions[] = unserialize( $rule->field_rule );
		}

		return json_encode( $conditions );
	}

	/**
	 * Output form via shortcode
	 *
	 * @since 1.0
	 */
	public function form_code( $atts, $output = null ) {

		require( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/form-output.php' );

		return $output;
	}

	/**
	 * Handle emailing the content
	 *
	 * @since 1.0
	 * @uses wp_mail() E-mails a message
	 */
	public function email() {
		require( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/email.php' );
	}

	/**
	 * Validate the input
	 *
	 * @since 1.3
	 */
	public function validate_input( $data, $name, $type, $required, $form_verification, $field_rule_setting = 0 ) {

		do_action_ref_array( 'vfb_validate_input', array( $data, $name, $type, $required, $form_verification, $field_rule_setting ) );

		// Skip the validation if the verification is off and secret is still required
		if ( 'yes' == $required && 'secret' == $type && 0 == $form_verification )
			return true;

		// Skip conditional fields in case they are hidden and still required
		if ( $field_rule_setting && 'yes' == $required && strlen( $data ) == 0 )
			return true;

		if ( 'yes' == $required && strlen( $data ) == 0 )
			wp_die( "<h1>$name</h1><br>" . apply_filters( 'vfb_str_validate_required', __( 'This field is required and cannot be empty.', 'visual-form-builder-pro' ) ), $name, array( 'back_link' => true ) );

		if ( strlen( $data ) > 0 ) :
			switch( $type ) :
				case 'email' :
					if ( !is_email( $data ) )
						wp_die( "<h1>$name</h1><br>" . apply_filters( 'vfb_str_validate_email', __( 'Not a valid email address', 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );
						break;

				case 'number' :
				case 'currency' :
					if ( !is_numeric( $data ) )
						wp_die( "<h1>$name</h1><br>" . apply_filters( 'vfb_str_validate_number', __( 'Not a valid number', 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );
						break;

				case 'phone' :
					if ( strlen( $data ) > 9 && preg_match( '/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/', $data ) )
						return true;
					else
						wp_die( "<h1>$name</h1><br>" . apply_filters( 'vfb_str_validate_phone', __( 'Not a valid phone number. Most US/Canada and International formats accepted.', 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );
						break;

				case 'url' :
					if ( !preg_match( '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $data ) )
						wp_die( "<h1>$name</h1><br>" . apply_filters( 'vfb_str_validate_url', __( 'Not a valid URL.', 'visual-form-builder-pro' ) ), '', array( 'back_link' => true ) );
						break;

				default :
					return true;
					break;
			endswitch;
		endif;
	}

	/**
	 * Sanitize the input
	 *
	 * @since 1.9
	 */
	public function sanitize_input( $data, $type ) {

		do_action( 'vfb_sanitize_input', $data, $type );

		if ( strlen( $data ) > 0 ) :
			switch( $type ) :
				case 'text' :
					return sanitize_text_field( $data );
					break;

				case 'textarea' :
					return wpautop( wp_strip_all_tags( $data ) );
					break;

				case 'email' :
					return sanitize_email( $data );
					break;

				case 'username' :
					return sanitize_user( strtolower( $data ) );
					break;

				case 'html' :
					return wpautop( wp_kses_data( force_balance_tags( $data ) ) );
					break;

				case 'min' :
				case 'max' :
				case 'digits' :
					return preg_replace( '/\D/i', '', $data );
					break;

				case 'address' :
				case 'likert' :
					$allowed_html = array( 'br' => array() );
					return wp_kses( $data, $allowed_html );
					break;

				default :
					return wp_kses_data( $data );
					break;
			endswitch;
		endif;
	}


	/**
	 * Akismet check
	 *
	 * @access protected
	 * @param mixed $data
	 * @return void
	 */
	protected function akismet_check( $data ) {
		if ( !function_exists( 'akismet_http_post' ) )
			return false;

		do_action( 'vfb_akismet_check', $data );

		global $akismet_api_host, $akismet_api_port;

		$query_string = '';
		$result       = false;

		foreach ( array_keys( $data ) as $k ) {
			$query_string .= $k . '=' . urlencode( $data[ $k ] ) . '&';
		}

		if ( method_exists( 'Akismet', 'http_post' ) )
		    $response = Akismet::http_post( $query_string, 'comment-check' );
		else
		    $response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port );

		// Only return true if a response is available
		if ( $response ) {
			if ( 'true' == trim( $response[1] ) )
				$result = true;
		}

		return $result;
	}

	/**
	 * Make sure the User Agent string is not a SPAM bot
	 *
	 * @since 1.3
	 */
	public function isBot() {
		$bots = apply_filters( 'vfb_blocked_spam_bots', array(
			'<', '>', '&lt;', '%0A', '%0D', '%27', '%3C', '%3E', '%00', 'href',
			'binlar', 'casper', 'cmsworldmap', 'comodo', 'diavol',
			'dotbot', 'feedfinder', 'flicky', 'ia_archiver', 'jakarta',
			'kmccrew', 'nutch', 'planetwork', 'purebot', 'pycurl',
			'skygrid', 'sucker', 'turnit', 'vikspider', 'zmeu',
			)
		);

		$isBot = false;

		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_kses_data( $_SERVER['HTTP_USER_AGENT'] ) : '';

		do_action( 'vfb_isBot', $user_agent, $bots );

		foreach ( $bots as $bot ) {
			if ( stripos( $user_agent, $bot ) !== false )
				$isBot = true;
		}

		return $isBot;
	}

	/**
	 * Replace variables surrounded with {} brackets with $_POST values
	 *
	 * @since 1.9
	 * @return replaced values || original value if no brackets found
	 */
	public function templating( $key ) {
		$search = preg_match_all( '/{(.*?)}/', $key, $matches );

		if ( $search ) {
			foreach ( $matches[1] as $match ) {
				$value = isset( $_POST[ $match ] ) ? $this->build_array_form_item( $_POST[ $match ] ) : '';
				$key = str_ireplace( "{{$match}}", $value, $key );
			}
		}

		return $key;
	}

	/**
	 * Build array form items output for email
	 *
	 * Includes the Address, Name, Time, and Checkbox fields
	 *
	 * @access public
	 * @param mixed $value
	 * @param string $type (default: '')
	 * @return void
	 */
	public function build_array_form_item( $value, $type = '' ) {

		$output = '';

		// Basic check for type when not set
		if ( empty( $type ) ) :
			if ( is_array( $value ) && array_key_exists( 'address', $value ) )
				$type = 'address';
			elseif ( is_array( $value ) && array_key_exists( 'first', $value ) && array_key_exists( 'last', $value ) )
				$type = 'name';
			elseif ( is_array( $value ) && array_key_exists( 'hour', $value ) && array_key_exists( 'min', $value ) )
				$type = 'time';
			elseif ( is_array( $value ) )
				$type = 'checkbox';
			else
				$type = 'default';
		endif;

		// Build array'd form item output
		switch( $type ) :

			case 'time' :
				$output = ( array_key_exists( 'ampm', $value ) ) ? substr_replace( implode( ':', $value ), ' ', 5, 1 ) : implode( ':', $value );
				break;

			case 'address' :

				if ( !empty( $value['address'] ) )
					$output .= $value['address'];

				if ( !empty( $value['address-2'] ) ) {
					if ( !empty( $output ) )
						$output .= '<br>';
					$output .= $value['address-2'];
				}

				if ( !empty( $value['city'] ) ) {
					if ( !empty( $output ) )
						$output .= '<br>';
					$output .= $value['city'];
				}
				if ( !empty( $value['state'] ) ) {
					if ( !empty( $output ) && empty( $value['city'] ) )
						$output .= '<br>';
					elseif ( !empty( $output ) && !empty( $value['city'] ) )
						$output .= ', ';
					$output .= $value['state'];
				}
				if ( !empty( $value['zip'] ) ) {
					if ( !empty( $output ) && ( empty( $value['city'] ) && empty( $value['state'] ) ) )
						$output .= '<br>';
					elseif ( !empty( $output ) && ( !empty( $value['city'] ) || !empty( $value['state'] ) ) )
						$output .= ' ';
					$output .= $value['zip'];
				}
				if ( !empty( $value['country'] ) ) {
					if ( !empty( $output ) )
						$output .= '<br>';
					$output .= $value['country'];
				}

				break;

			case 'name' :

				if ( !empty( $value['first'] ) )
					$output .= $value['first'];

				if ( !empty( $value['last'] ) ) {
					if ( !empty( $output ) )
						$output .= ' ';
					$output .= $value['last'];
				}

				if ( !empty( $value['title'] ) ) {
					if ( !empty( $output ) )
						$output = ' ' . $output;
					$output = $value['title'] . $output;
				}

				if ( !empty( $value['suffix'] ) ) {
					if ( !empty( $output ) )
						$output .= ' ';
					$output .= $value['suffix'];
				}

				break;

			case 'likert' :

				foreach ( $value as $row => $col ) {
					$output .= sprintf( '* %1$s - %2$s<br>', esc_html( $row ), esc_html( $col ) );
				}

				break;

			case 'checkbox' :
				$output = esc_html( implode( ', ', $value ) );
				break;

			default :
				$output = wp_specialchars_decode( stripslashes( esc_html( $value ) ), ENT_QUOTES );
				break;

		endswitch;

		return $output;
	}

	/**
	 * Payments Add-On form output
	 *
	 * @access public
	 * @param mixed $form_id
	 * @return void
	 */
	public function payments_output( $form_id ) {
		// Run Payments add-on
		if ( class_exists( 'VFB_Pro_Payments' ) )
			$vfb_payments = new VFB_Pro_Payments();

		$output = '';

		// Output payment options
		if ( class_exists( 'VFB_Pro_Payments' ) && method_exists( $vfb_payments, 'running_total' ) ) :
			$enable_payment = $vfb_payments->enable_payment( $form_id );

			// Output payment options only if enabled
			if ( $enable_payment ) :
				// Set PayPal default to a Cart
				$paypal_command = '_cart';

				$account_details = $vfb_payments->account_details( $form_id );

				// Account email
				if ( $account_details )
					$output .= $account_details;

				$currency = $vfb_payments->currency_code( $form_id );

				// Currency Code
				if ( $currency )
					$output .= $currency;

				$prices = $vfb_payments->running_total( $form_id );

				// Pricing fields for jQuery
				if ( $prices ) :
					wp_localize_script( 'vfb-pro-payments', 'VfbPrices', array( 'prices' => $prices ) );

					// Show running total
					if ( $vfb_payments->show_running_total( $form_id ) )
						$output .= $vfb_payments->running_total_output( $form_id );

					// For dynamic pricing inputs
					$output .= '<div class="vfb-payment-hidden-inputs"></div>';

				endif;

				$recurring = $vfb_payments->recurring_payments( $form_id );

				// Recurring payment
				if ( $recurring ) :
					$paypal_command = '_xclick-subscriptions';

					// Recurring hidden inputs
					$output .= $recurring;

					// For totals
					$output .= '<div class="vfb-payment-hidden-totals"></div>';

					// Required input for subscriptions
					$output .= '<input type="hidden" name="no_note" value="1">';
				endif;

				// Collect Shipping Address
				if ( $vfb_payments->collect_shipping_address( $form_id ) ) :
					$output .= '<input type="hidden" name="no_shipping" value="2">';
				endif;

				// PayPal command
				$output .= '<input type="hidden" name="cmd" value="' . $paypal_command . '">';
			endif;
		endif;

		return $output;
	}


	/**
	 * Check whether the content contains the specified shortcode
	 *
	 * @access public
	 * @param string $shortcode (default: '')
	 * @return void
	 */
	function has_shortcode($shortcode = '') {

		$post_to_check = get_post(get_the_ID());

		// false because we have to search through the post content first
		$found = false;

		// if no short code was provided, return false
		if (!$shortcode) {
			return $found;
		}
		// check the post content for the short code
		if ( stripos($post_to_check->post_content, '[' . $shortcode) !== false ) {
			// we have found the short code
			$found = true;
		}

		// return our final results
		return $found;
	}
}

// The VFB Pro widget
require( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-widget.php' );

// Special case to load Export class so AJAX is registered
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-export.php' );
if ( !isset( $export ) )
	$export = new VisualFormBuilder_Pro_Export();
