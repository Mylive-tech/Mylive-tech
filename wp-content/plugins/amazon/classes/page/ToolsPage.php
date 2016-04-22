<?php
/**
 * ToolsPage class
 * 
 */

class WPLA_ToolsPage extends WPLA_Page {

	const slug = 'tools';

	public function onWpInit() {
		// parent::onWpInit();

		// custom (raw) screen options for tools page
		add_screen_options_panel('wpla_setting_options', '', array( &$this, 'renderSettingsOptions'), 'wp-lister_page_wpla-tools' );

		// load styles and scripts for this page only
		// add_action( 'admin_print_styles', array( &$this, 'onWpPrintStyles' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'onWpEnqueueScripts' ) );		
		add_thickbox();
	}

	public function onWpAdminMenu() {
		parent::onWpAdminMenu();

        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'repricing'; 
        $title_prefix = '';
		if ( $active_tab == 'repricing' ) $title_prefix = 'Repricing - '; 
		if ( $active_tab == 'inventory' ) $title_prefix = 'Inventory - '; 
		if ( $active_tab == 'skugen'    ) $title_prefix = 'SKU - '; 

		add_submenu_page( self::ParentMenuId, $this->getSubmenuPageTitle( $title_prefix . 'Tools' ), __('Tools','wpla'), 
						  self::ParentPermissions, $this->getSubmenuId( 'tools' ), array( &$this, 'onDisplayToolsPage' ) );
	}

	public function onDisplayToolsPage() {
		global $wpla_logger;
		
		$this->check_wplister_setup();

		// Repricing tab
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'repricing'; 
		if ( $active_tab == 'repricing' ) { 
			return WPLA()->pages['repricing']->displayRepricingPage();
		}
		if ( $active_tab == 'skugen' ) { 
			return WPLA()->pages['skugen']->displaySkuGenPage();
		}

		// check action - and nonce
		if ( isset($_REQUEST['action']) ) {
			if ( check_admin_referer( 'wpla_tools_page' ) ) {

				// view_logfile
				if ( $_REQUEST['action'] == 'view_logfile') {				
					$this->viewLogfile();
				}

				// wpla_clear_log
				if ( $_REQUEST['action'] == 'wpla_clear_log') {				
					$this->clearLogfile();
					$this->showMessage('Log file was cleared.');
				}

				// update_amazon_orders
				if ( $_REQUEST['action'] == 'update_amazon_orders_30') {				
					do_action( 'wpla_update_orders' );
				}
	
				// wpla_run_daily_schedule
				if ( $_REQUEST['action'] == 'wpla_run_daily_schedule') {
					do_action( 'wpla_daily_schedule' );
				}

				// wpla_run_autosubmit_fba_orders
				if ( $_REQUEST['action'] == 'wpla_run_autosubmit_fba_orders') {
					do_action( 'wpla_autosubmit_fba_orders' );
				}

				// wpla_refresh_minmax_prices_from_wc
				if ( $_REQUEST['action'] == 'wpla_refresh_minmax_prices_from_wc') {
					$this->refreshMinMaxPrices();
					wpla_show_message('Minimum and maximum prices in WP-Lister have been refreshed.');
				}


				// check_wc_out_of_sync
				if ( $_REQUEST['action'] == 'check_wc_out_of_sync') {				
					$ic = new WPLA_InventoryCheck();
					$mode   = isset( $_REQUEST['mode'] )   ? $_REQUEST['mode']   : 'published';
					$prices = isset( $_REQUEST['prices'] ) ? $_REQUEST['prices'] : false;
					$ic->checkProductInventory( $mode, $prices );
				}

				// check_wc_out_of_stock
				if ( $_REQUEST['action'] == 'check_wc_out_of_stock') {				
					$ic = new WPLA_InventoryCheck();
					$ic->checkProductStock();
				}

				// check_wc_fba_stock
				if ( $_REQUEST['action'] == 'check_wc_fba_stock') {				
					$ic = new WPLA_InventoryCheck();
					$mode = isset( $_REQUEST['mode'] )   ? $_REQUEST['mode']   : 'in_stock_only';
					$ic->checkFBAStock( $mode );
				}

				// check_wc_sold_stock
				if ( $_REQUEST['action'] == 'check_wc_sold_stock') {				
					$ic = new WPLA_InventoryCheck();
					$ic->checkSoldStock();
				}

				// wpla_fix_variable_stock_status
				if ( $_REQUEST['action'] == 'wpla_fix_variable_stock_status') {				
					$this->fixVariableStockStatus();
					wpla_show_message('All variation stock levels have been synchronized.');
				}

	
			} else {
				die ('not allowed');
			}
		}

		$aData = array(
			'plugin_url'				=> self::$PLUGIN_URL,
			'message'					=> $this->message,		
			'debug'						=> isset($debug) ? $debug : '',
			'log_size'					=> file_exists($wpla_logger->file) ? filesize($wpla_logger->file) : '',
			'tools_url'	 				=> 'admin.php?page='.self::ParentMenuId.'-tools',
			'form_action'				=> 'admin.php?page='.self::ParentMenuId.'-tools'.'&tab='.$active_tab
		);

		if ( $active_tab == 'developer' ) { 
			$this->display( 'tools_debug', $aData );
			return;
		}

		$this->display( 'tools_page', $aData );
	}


	public function refreshMinMaxPrices() {
		global $wpdb;

		$min_prices = $wpdb->get_results("
			SELECT 
				post_id,
				meta_value 
			FROM {$wpdb->prefix}postmeta
			WHERE meta_key = '_amazon_minimum_price'
		");
		
		$max_prices = $wpdb->get_results("
			SELECT 
				post_id,
				meta_value 
			FROM {$wpdb->prefix}postmeta
			WHERE meta_key = '_amazon_maximum_price'
		");

		foreach ($min_prices as $record) {
			$wpdb->update( $wpdb->prefix.'amazon_listings', array( 'min_price' => $record->meta_value ), array( 'post_id' => $record->post_id ) );
			// echo "<pre>";print_r($wpdb->last_query);echo"</pre>";#die();
		}

		foreach ($max_prices as $record) {
			$wpdb->update( $wpdb->prefix.'amazon_listings', array( 'max_price' => $record->meta_value ), array( 'post_id' => $record->post_id ) );
			// echo "<pre>";print_r($wpdb->last_query);echo"</pre>";#die();
		}

	} // refreshMinMaxPrices()


	public function fixVariableStockStatus() {

		// get all parent variations
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'variable' ),
				),
			),
		);
		$query = new WP_Query( $args );
		$parent_variations = $query->posts;

		// loop products
		foreach ( $parent_variations as $post ) {
			// $this->fixVariableStockStatusForProduct( $post->ID );
			WC_Product_Variable::sync_stock_status( $post->ID );
		}

	} // fixVariableStockStatus()













	public function viewLogfile() {
		global $wpla_logger;

		echo "<pre>";
		echo readfile( $wpla_logger->file );
		echo "<br>logfile: " . $wpla_logger->file . "<br>";
		echo "</pre>";

	}

	public function clearLogfile() {
		global $wpla_logger;
		file_put_contents( $wpla_logger->file, '' );
	}

	public function renderSettingsOptions() {
		?>
		<div class="hidden" id="screen-options-wrap" style="display: block;">
			<form method="post" action="" id="dev-settings">
				<h5>Show on screen</h5>
				<div class="metabox-prefs">
						<label for="dev-hide">
							<input type="checkbox" onclick="jQuery('#DeveloperToolBox').toggle();return false;" value="dev" id="dev-hide" name="dev-hide" class="hide-column-tog">
							Developer options
						</label>
					<br class="clear">
				</div>
			</form>
		</div>
		<?php
	}


	
	public function onWpPrintStyles() {

		// deprecated
		// jQuery UI theme - for progressbar
		// wp_register_style('jQueryUITheme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/cupertino/jquery-ui.css');
		// wp_register_style('jQueryUITheme', plugins_url( 'css/smoothness/jquery-ui-1.8.22.custom.css' , WPLA_PATH.'/wp-lister.php' ) );
		// wp_enqueue_style('jQueryUITheme'); 

	}

	public function onWpEnqueueScripts() {

		// testing:
		// jQuery UI progressbar
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-progressbar');

        // only enqueue JobRunner.js on WPLA pages
        if ( ! isset( $_REQUEST['page'] ) ) return;
       	if ( substr( $_REQUEST['page'], 0, 4 ) != 'wpla' ) return;

		// jqueryFileTree
		wp_register_script( 'wpla_JobRunner', self::$PLUGIN_URL.'/js/classes/JobRunner.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-progressbar' ), WPLA_VERSION );
		wp_enqueue_script( 'wpla_JobRunner' );

		wp_localize_script('wpla_JobRunner', 'wpla_JobRunner_i18n', array(
				'msg_loading_tasks' 	=> __('fetching list of tasks', 'wpla').'...',
				'msg_estimating_time' 	=> __('estimating time left', 'wpla').'...',
				'msg_finishing_up' 		=> __('finishing up', 'wpla').'...',
				'msg_all_completed' 	=> __('All {0} tasks have been completed.', 'wpla'),
				'msg_processing' 		=> __('processing {0} of {1}', 'wpla'),
				'msg_time_left' 		=> __('about {0} remaining', 'wpla'),
				'footer_dont_close' 	=> __("Please don't close this window until all tasks are completed.", 'wpla')
			)
		);

	    // jQuery UI Dialog
    	// wp_enqueue_style( 'wp-jquery-ui-dialog' );
	    // wp_enqueue_script ( 'jquery-ui-dialog' ); 

	}


}
