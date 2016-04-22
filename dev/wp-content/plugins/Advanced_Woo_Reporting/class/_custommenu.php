<?php
	if ( ! function_exists( 'wcx_wcreport_register_custom_menu' ) ) 
	{
		// ADDING MAIN MENU ITEMS
		function wcx_setup_menus() {
			global $submenu;
			add_menu_page('Woo Reporting', 'Woo Reporting', 'manage_options', 'wcx_wcreport_plugin_dashboard', 'wcx_plugin_menu_dashboard','
dashicons-chart-pie' );
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Dashboard',__WCX_WCREPORT_TEXTDOMAIN__), __('Dashboard',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_dashboard', 'wcx_plugin_menu_dashboard' );
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Products Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Products',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_products', 'wcx_plugin_submenu_products' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Orders Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Orders',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_orders', 'wcx_plugin_submenu_orders' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Categories Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Categories',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_categories', 'wcx_plugin_submenu_categories' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Customers Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Customers',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_customers', 'wcx_plugin_submenu_customers' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Billing Countries Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Billing Countries',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_billingcountries', 'wcx_plugin_submenu_billingcountries' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Billing States Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Billing States',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_billingstates', 'wcx_plugin_submenu_billingstates' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Payment Gateways Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Payment Gateways',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_paymentgateways', 'wcx_plugin_submenu_paymentgateways' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Order Statuses Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Order Statuses',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_orderstatuses', 'wcx_plugin_submenu_orderstatuses' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Coupons Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Coupons',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_coupons', 'wcx_plugin_submenu_coupons' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Stock Details',__WCX_WCREPORT_TEXTDOMAIN__), __('Stock',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_stock', 'wcx_plugin_submenu_stock' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Projected Vs. Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__), __('Projected Vs. Actual',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_projected', 'wcx_plugin_submenu_projected' );				
			add_submenu_page('wcx_wcreport_plugin_dashboard', __('Preferences',__WCX_WCREPORT_TEXTDOMAIN__), __('Preferences',__WCX_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_preferences', 'wcx_plugin_submenu_preferences' );
		}
		add_action('admin_menu', 'wcx_setup_menus');
		
		// ADDING TOP MENU ITEMS
		function wcx_top_menus() {
			global $wp_admin_bar;
			if(!is_super_admin() || !is_admin_bar_showing()) return;
			$argsParent=array(
				'id' => 'wcx_wcreport_plugin_topmenu',
				'title' => __('Woo Reporting',__WCX_WCREPORT_TEXTDOMAIN__),
				'href' => admin_url('admin.php?page=wcx_wcreport_plugin_dashboard')
			);
			$wp_admin_bar->add_menu($argsParent);
		}
		add_action('admin_bar_menu', 'wcx_top_menus', 1000);
		
		// FUNCTIONS FOR EASE OF CODING
		function object_to_array($obj) {
			if(is_object($obj)) $obj = (array) $obj;
			if(is_array($obj)) {
				$new = array();
				foreach($obj as $key => $val) {
					$new[$key] = object_to_array($val);
				}
			}
			else $new = $obj;
			return $new;       
		}
		function print_array($aArray) {
			echo '<pre>';
			print_r($aArray);
			echo '</pre>';
		}
		function sortbyval($a, $b) {
			if( $a['total'] > $b['total'] ) {
				return -1;
			}elseif( $a['total'] == $b['total'] ) {
				return 0;
			}else {
				return 1;
			}
		}
		function sortbyview($a, $b) {
			if(!isset($a['views']))$a['views'] = 0;
			if(!isset($b['views']))$b['views'] = 0;
			if( $a['views'] == $b['views'] ) {
				return 0;
			}elseif( $a['views'] > $b['views'] ) {
				return -1;
			}else{
				return 1;
			}
		}
		function sortbycnt($a, $b) {
			if( $a['cnt'] > $b['cnt'] ) {
				return -1;
			}elseif( $a['cnt'] == $b['cnt'] ) {
				return 0;
			}else {
				return 1;
			}
		}
		function humanTiming ($time){
			$time = time() - $time; 
			$tokens = array (
				31536000 => 'year',
				2592000 => 'month',
				604800 => 'week',
				86400 => 'day',
				3600 => 'hour',
				60 => 'minute',
				1 => 'second'
			);
			foreach ($tokens as $unit => $text) {
				if ($time < $unit) continue;
				$numberOfUnits = floor($time / $unit);
				return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
			}
		}
		
		// FUNCTIONS FOR ADD VIEW SUPPORT
		function getPostViews($post_ID){
			$count_key = 'post_views_count';
			$count = get_post_meta($post_ID, $count_key, true);
			if($count==''){
				delete_post_meta($post_ID, $count_key);
				add_post_meta($post_ID, $count_key, '0');
				return '0';
			}
			return $count;
		}
		function setPostViews($post_object) {
			if (is_singular( 'product' ) && is_single() ){
				$count_key = 'post_views_count';
				$count = get_post_meta(get_the_ID(), $count_key, true);
				if($count==''){
					$count = 0;
					delete_post_meta(get_the_ID(), $count_key);
					add_post_meta(get_the_ID(), $count_key, '0');
				}else{
					$count++;
					update_post_meta(get_the_ID(), $count_key, $count);
				}
			}
			return $post_object;
		}
		add_action( 'the_content', 'setPostViews');
		function posts_column_views($defaults){
			$defaults['post_views'] = __('Views');
			return $defaults;
		}
		add_filter('manage_posts_columns', 'posts_column_views');
		function posts_custom_column_views($column_name, $id){
		 if($column_name === 'post_views'){
				echo getPostViews(get_the_ID());
			}
		}
		add_action('manage_posts_custom_column', 'posts_custom_column_views',5,2);
		
		// THE MAIN REPORT CLASS IS LOCATED IN THIS SEPARATE FILE
		include('reportclass.php');
		
		// THE DASHBOARD PAGE CONTENT
		function wcx_plugin_menu_dashboard() {
			global $wpdb;
			$report = new wcx_plugin_reports;
			
			if(!isset($report -> stored_cus[0]))$report -> stored_cus[0] = 0;
			$statuses = Array(
				"wc-completed" => __("Completed",__WCX_WCREPORT_TEXTDOMAIN__), "wc-on-hold" => __("On Hold",__WCX_WCREPORT_TEXTDOMAIN__),"wc-cancelled" => __("Cancelled",__WCX_WCREPORT_TEXTDOMAIN__),"wc-refunded" => __("Refunded",__WCX_WCREPORT_TEXTDOMAIN__),"wc-failed" => __("Failed",__WCX_WCREPORT_TEXTDOMAIN__),"wc-pending" => __("Pending",__WCX_WCREPORT_TEXTDOMAIN__),"wc-processing" => __("Processing",__WCX_WCREPORT_TEXTDOMAIN__)
			);
		
			$curyear = date("Y");
			$curmonth = date("n");
			$curmonthn = date("F");
			$curday = date("j");
?>
		<div class="wrap">
			<h2>WooCommerce Reporting Plugin</h2><br>
			<div class="postbox hide">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle ui-sortable-handle"><span><?php _e('At A Glance',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
				<div class="inside">
<?php // INFO BOXES ARE CREATED IN THIS "ROW" ?>
					<div class="row my-page">
						<div class="col-md-2">
							<div class="my-boxes c1">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-line-chart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Total Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> total -> actual -> total, 2, '.', ','); ?></li>
									<li><?php echo "#",$report -> stored -> total -> actual -> cnt; ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>	
						<div class="col-md-2">
							<div class="my-boxes c2">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-line-chart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Proj. Sales',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curyear ?>)</span></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> total -> proj -> total, 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>		
						<div class="col-md-2">
							<div class="my-boxes c3">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-line-chart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Year Sales',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curyear ?>)</span></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> total -> actual -> total, 2, '.', ','); ?></li>
									<li><?php echo "#",$report -> stored -> $curyear -> total -> actual -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>		
						<div class="col-md-2">
							<div class="my-boxes c4">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-shopping-cart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Avg. Sales/Order',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curyear ?>)</span></h2>
								<ul>
									<li><?php echo "$",number_format((float)($report -> stored -> $curyear -> total -> actual -> cnt == 0)? 0 : $report -> stored -> $curyear -> total -> actual -> total / $report -> stored -> $curyear -> total -> actual -> cnt, 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>		
						<div class="col-md-2">
							<div class="my-boxes c5">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-sun-o fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Avg. Sales/Day',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curyear ?>)</span></h2>
								<ul>
									<li><?php echo "$", number_format((float)$report -> stored -> $curyear -> total -> actual -> total / date('z'), 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>		
						<div class="col-md-2">
							<div class="my-boxes c6">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-calendar-o fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Month Sales',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curmonthn." ".$curyear ?>)</span></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> $curmonth -> total -> actual -> total, 2, '.', ','); ?></li>
									<li><?php echo "#",$report -> stored -> $curyear -> $curmonth -> total -> actual -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>	
						<div class="col-md-2">
							<div class="my-boxes c7">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-line-chart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Proj. Sales',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curmonthn." ".$curyear ?>)</span></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> $curmonth -> total -> proj -> total, 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c8">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-line-chart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Avg. Sales/Day',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curmonthn." ".$curyear ?>)</span></h2>
								<ul>
									<li><?php echo "$", number_format((float)$report -> stored -> $curyear -> $curmonth -> total -> actual -> total / date('j'), 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c9">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-line-chart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Avg. Sales/Order',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curmonthn." ".$curyear ?>)</span></h2>
								<ul>
									<li><?php echo "$",number_format((float)($report -> stored -> $curyear -> $curmonth -> total -> actual -> cnt == 0)?0:$report -> stored -> $curyear -> $curmonth -> total -> actual -> total / $report -> stored -> $curyear -> $curmonth -> total -> actual -> cnt, 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c10">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-rocket fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Forecasted Sales',__WCX_WCREPORT_TEXTDOMAIN__);?> <span>(<?php echo $curmonthn." ".$curyear ?>)</span> </h2>
								<ul>
									<li><?php echo "$0.00"; ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c11">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-thumbs-down fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Total Refund',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> total -> refund -> total, 2, '.', ','); ?></li>
									<li><?php echo "#", $report -> stored -> total -> refund -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c12">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-suitcase fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Order Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> total -> tax -> total, 2, '.', ','); ?></li>
									<li><?php echo "#", $report -> stored -> total -> tax -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c13">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-suitcase fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Order Shipping Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> total -> shiptax -> total, 2, '.', ','); ?></li>
									<li><?php echo "#", $report -> stored -> total -> shiptax -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c14">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-suitcase fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Total Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> total -> tax -> total + $report -> stored -> total -> shiptax -> total, 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c15">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-ship fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Order Shipping Total',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> total -> ship -> total, 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c16">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-calendar fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Last Order Date',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<?php $query = "
								SELECT  
									posts.post_date AS date 
									FROM  {$wpdb->posts} AS posts
								WHERE 
									posts.post_status IN ( 'wc-completed','wc-processing','wc-on-hold','wc-pending' ) 
									AND posts.post_type IN ( 'shop_order' ) 
								ORDER BY
									posts.post_date
									DESC
								";
								$data = $wpdb->get_row($query,ARRAY_A);
								$time = strtotime($data['date']); ?>
								<ul>
									<li><?php echo date("F j, Y", strtotime($data['date'])); ?></li>
									<li><?php echo humanTiming($time).' ago'; ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c5">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-tags fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Total Coupons',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$", number_format((float)$report -> stored_coupon[0] -> total, 2, '.', ','); ?></li>
									<li><?php echo "#", $report -> stored_coupon[0] -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c7">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-users fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Total Registered Customers',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "#", count($report -> stored_cd) - $report -> stored_cus[0];  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c2">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-user-secret fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Total Guest Customers',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "#", $report -> stored_cus[0];  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
					</div>
					
<?php // TODAYS INFO BOXES ARE CREATED IN THIS "ROW" ?>
<div class="wr-title"><span><?php _e('Todays Summary',__WCX_WCREPORT_TEXTDOMAIN__);?></span></div>
					<div class="row my-page">
						<div class="col-md-2">
							<div class="my-boxes c10">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-line-chart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Todays Total Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> $curmonth -> $curday -> actual -> total, 2, '.', ','); ?></li>
									<li><?php echo "#",$report -> stored -> $curyear -> $curmonth -> $curday -> actual -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>		
						<div class="col-md-2">
							<div class="my-boxes c3">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-shopping-cart fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Todays Avg. Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)($report -> stored -> $curyear -> $curmonth -> $curday -> actual -> cnt == 0)?0:$report -> stored -> $curyear -> $curmonth -> $curday -> actual -> total / $report -> stored -> $curyear -> $curmonth -> $curday -> actual -> cnt, 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>	
						<div class="col-md-2">
							<div class="my-boxes c12">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-thumbs-down fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Todays Total Refund',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> $curmonth -> $curday -> refund -> total, 2, '.', ','); ?></li>
									<li><?php echo "#", $report -> stored -> $curyear -> $curmonth -> $curday -> refund -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c15">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-suitcase fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Todays Order Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> $curmonth -> $curday -> tax -> total, 2, '.', ','); ?></li>
									<li><?php echo "#", $report -> stored -> $curyear -> $curmonth -> $curday -> tax -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c9">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-suitcase fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Todays Order Shipping Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> $curmonth -> $curday -> shiptax -> total, 2, '.', ','); ?></li>
									<li><?php echo "#", $report -> stored -> $curyear -> $curmonth -> $curday -> shiptax -> cnt;  ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
						<div class="col-md-2">
							<div class="my-boxes c6">
								<div class="my-header">
									<div class="my-headers"><div class="charkh"><span class="fa fa-suitcase fa-3x" aria-hidden="true"></span></div></div>
								</div>
								<h2><?php _e('Todays Total Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></h2>
								<ul>
									<li><?php echo "$",number_format((float)$report -> stored -> $curyear -> $curmonth -> $curday -> shiptax -> total + $report -> stored -> $curyear -> $curmonth -> $curday -> shiptax -> total, 2, '.', ','); ?></li>
								</ul>
							</div>
							<div class="my-footer"></div>
						</div>
					</div>	
				</div>
			</div>	
<?php // THE MULTICHART BOX ?>			
			<div class="postbox hide multicharted">
<?php // TOP MENU OF MULTICHART BOX ?>
				
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Sales Overview',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
            <div class="my-menu">
					<span class="showchart"><div class="eleman actived"><?php _e('Sales By Months',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="showchart"><div class="eleman"><?php _e('Sales By Days',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="showchart"><div class="eleman"><?php _e('Sales By Week',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="showchart"><div class="eleman"><?php _e('Top Products By Sale',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="showchart"><div class="eleman"><?php _e('Top Products By View',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div>
			<div class="inside">
<?php // THE HIDDEN TABLES COLLECTING CHARTS DATA ?>
<?php // SALES BY MONTH DATA ?>
				<div class="row report_table hide">
					<table class="display charted" cellspacing="0" width="100%">
						<thead class="barpref">
							<tr>			
								<th><?php _e('Month',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>			
								<th><?php _e('Month',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php
							$nowdate = date('F 1, Y');
							$seekdate = date("F 1, Y", strtotime("-11 months", strtotime($nowdate)));
							do{
								$seekyear = date("Y", strtotime($seekdate));
								$seekmonth = date("n", strtotime($seekdate));
								
								echo "<tr>";
									echo "<td>";								
										echo date("F", mktime(0,0,0,$seekmonth,1,$seekyear));
										echo "-";
										echo $seekyear;
									echo "</td>";
									echo "<td>";
										echo "$";
										echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total, 2, '.', ',');
									echo "</td>";								
								echo "</tr>";
								
								$seekdate = date("F 1, Y", strtotime("+1 month", strtotime($seekdate)));
							}while($seekdate != $nowdate);
							
		?>
						</tbody>
					</table>
				</div>	
<?php // SALES BY DAYS DATA ?>
				<div class="row report_table hide">
					<table class="display charted" cellspacing="0" width="100%">
						<thead class="scatterpref">
							<tr>			
								<th><?php _e('Day',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>			
								<th><?php _e('Day',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php
							$nowdate = date('F j, Y');
							$seekdate = date("F j, Y", strtotime("-31 days", strtotime($nowdate)));
							do{
								$seekyear = date("Y", strtotime($seekdate));
								$seekmonth = date("n", strtotime($seekdate));
								$seekday = date("j", strtotime($seekdate));
								
								echo "<tr>";
									echo "<td>";								
										echo date("F j", mktime(0,0,0,$seekmonth,$seekday,$seekyear));
									echo "</td>";
									echo "<td>";
										echo "$";
										echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> $seekday -> actual -> total, 2, '.', ',');
									echo "</td>";								
								echo "</tr>";
								
								$seekdate = date("F j, Y", strtotime("+1 day", strtotime($seekdate)));
							}while($seekdate != $nowdate);
							
		?>
						</tbody>
					</table>
				</div>
<?php // SALES BY WEEK DATA ?>
				<div class="row report_table hide">
					<table class="display charted" cellspacing="0" width="100%">
						<thead class="scatterpref">
							<tr>			
								<th><?php _e('Day',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>			
								<th><?php _e('Day',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php
							$nowdate = date('F j, Y');
							$seekdate = date("F j, Y", strtotime("-7 days", strtotime($nowdate)));
							do{
								$seekyear = date("Y", strtotime($seekdate));
								$seekmonth = date("n", strtotime($seekdate));
								$seekday = date("j", strtotime($seekdate));
								
								echo "<tr>";
									echo "<td>";								
										echo date("F j", mktime(0,0,0,$seekmonth,$seekday,$seekyear));
									echo "</td>";
									echo "<td>";
										echo "$";
										echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> $seekday -> actual -> total, 2, '.', ',');
									echo "</td>";								
								echo "</tr>";
								
								$seekdate = date("F j, Y", strtotime("+1 day", strtotime($seekdate)));
							}while($seekdate != $nowdate);
							
		?>
						</tbody>
					</table>
				</div>	
<?php // TOP PRODUCTS BY SALE DATA ?>
				<div class="row report_table">
					<table class="display nodatatable charted" cellspacing="0" width="100%">
						<thead class="piepref">
							<tr>			
								<th><?php _e('Item Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>			
								<th><?php _e('Item Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php 
							foreach ($report -> stored_p as $key => $val) {
								if(!isset($val -> nolist) || (isset($val -> nolist) && $val -> nolist != 1)){
									echo("<tr>");
										echo("<td>");
											echo substr($val -> name,0,15).'...';
										echo("</td>");
										echo("<td>");
											echo ($val -> cnt == "")? 0 : $val -> cnt;
										echo("</td>");
										echo("<td>$");
											echo $val -> total;
										echo("</td>");
									echo("</tr>");
								}
							}
							?>
						</tbody>
					</table>
				</div>	
<?php // TOP PRODUCTS BY VIEW ?>
				<div class="row report_table">
					<table class="display nodatatable charted" cellspacing="0" width="100%">
						<thead class="barpref">
							<tr>			
								<th><?php _e('Item Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('views',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>			
								<th><?php _e('Item Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('views',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php 
							foreach ($report -> stored_view as $key => $val) {
								if(!isset($val -> nolist) || (isset($val -> nolist) && $val -> nolist != 1)){
									echo("<tr>");
										echo("<td>");
											echo substr($val -> name,0,20).'...';
										echo("</td>");
										echo("<td> ");
											echo ($val -> views == "")? 0 : $val -> views;
										echo("</td>");
									echo("</tr>");
								}
							}
							?>
						</tbody>
					</table>
				</div>	
<?php // THE MAIN CHART CONTAINER ?>				
				<div class="row report_chart hide">
					<div id="chart_overview" class="chartlocation" style="height:400px;"></div>
				</div>
			</div>	
			</div>
<?php // THE "SUMMARY OF THE YEAR" BOX ?>			
			<div class="postbox hide">
			<div class="handlediv" title="Click to toggle"><br></div>
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Summary Of The Year',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<table class="display npnsdatatable" cellspacing="0" width="100%">
						<thead>
							<tr>			
								<th><?php _e('Month',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Difference',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('%',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total % to Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Refund Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Part Refund Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total Discount Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Shipping Order Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total Shipping Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</thead>
				
						<tfoot>
							<tr>			
								<th><?php _e('Month',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Difference',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('%',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total % to Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Refund Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Part Refund Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total Discount Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Shipping Order Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total Shipping Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</tfoot>
					 
						<tbody>
					<?php
					$nowdate = date('F j, Y', mktime(0,0,0,1,1,$curyear+1));
					$seekdate = date("F j, Y", strtotime("-12 months", strtotime($nowdate)));
					do{
						$seekyear = date("Y", strtotime($seekdate));
						$seekmonth = date("n", strtotime($seekdate));
						
						echo "<tr>";
								echo "<td>";
									echo date('F-Y', strtotime($seekdate));
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo ($report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total < $report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total)? "-" : "+";
									echo "$";
									echo number_format((float)abs($report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total - $report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total), 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo ($report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total == 0)? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : number_format( (float)($report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total / $report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total)*100, 2, '.', ',')."%";
									
								echo "</td>";
								echo "<td>";
									echo "0%";
								
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> refund -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format(($report -> stored -> $seekyear -> $seekmonth -> total -> refund -> cnt == 0)? 0 : (float)$report -> stored -> $seekyear -> $seekmonth -> total -> refund -> total/$report -> stored -> $seekyear -> $seekmonth -> total -> refund -> cnt, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> discount -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> tax -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format(($report -> stored -> $seekyear -> $seekmonth -> total -> shiptax -> cnt == 0)? 0 : (float)$report -> stored -> $seekyear -> $seekmonth -> total -> shiptax -> total/$report -> stored -> $seekyear -> $seekmonth -> total -> shiptax -> cnt, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> shiptax -> total, 2, '.', ',');
								echo "</td>";
								
							echo "</tr>";
						
						
						$seekdate = date("F j, Y", strtotime("+1 month", strtotime($seekdate)));
					}while($seekdate != $nowdate);	
					
					
?>
						</tbody>
					</table>
				</div>	
			</div>	
			</div>
<?php // THE "ORDER SUMMARY" BOX ?>			
			<div class="row">
				<div class="col-md-6">
					<div class="postbox hide">
						<div class="my-menu">
							<span class="showtable"><div class="fa fa-table fa-lg eleman actived" aria-hidden="true"></div></span>
							<span class="showpiechart"><div class="fa fa-pie-chart fa-lg eleman" aria-hidden="true"></div></span>	
							<span class="showbarchart"><div class="fa fa-bar-chart fa-lg eleman" aria-hidden="true"></div></span>						
						</div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Order Summary',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row report_table">
								<table id="ordersummary" class="display nodatatable" cellspacing="0" width="100%">
									<thead class="barpref">
										<tr>			
											<th><?php _e('Sales Order',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Sales Order',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php
										$seekdate = date("F j, Y");
										$seekyear = date("Y", strtotime($seekdate));
										$seekmonth = date("n", strtotime($seekdate));
										$seekday = date("j", strtotime($seekdate));
										echo("<tr>");
										echo("<td>");
										_e('Today',__WCX_WCREPORT_TEXTDOMAIN__);
										echo("</td>");
										echo("<td>");
										echo "#".$report -> stored -> $seekyear -> $seekmonth -> $seekday -> actual -> cnt;
										echo("</td>");
										echo("<td>");
										echo "$".number_format((float)$report -> stored -> $seekyear -> $seekmonth -> $seekday -> actual -> total, 2, '.', ',');
										echo("</td>");
										echo("</tr>");
										$seekdate = date("F j, Y", strtotime("-1 day", strtotime($seekdate)));
										$seekyear = date("Y", strtotime($seekdate));
										$seekmonth = date("n", strtotime($seekdate));
										$seekday = date("j", strtotime($seekdate));
										
										echo("<tr>");
										echo("<td>".__('Yesterday',__WCX_WCREPORT_TEXTDOMAIN__)."</td>");
										echo("<td>");
										echo "#".$report -> stored -> $seekyear -> $seekmonth -> $seekday -> actual -> cnt;
										echo("</td>");
										echo("<td>");
										echo "$".number_format((float)$report -> stored -> $seekyear -> $seekmonth -> $seekday -> actual -> total, 2, '.', ',');
										echo("</td>");
										echo("</tr>");
										
										$nowdate = date("F j, Y", strtotime("+7 days",strtotime('last monday', strtotime('tomorrow'))));
										$seekdate = date("F j, Y", strtotime("-7 days", strtotime($nowdate)));
										$weektotal = 0;
										$weekcnt = 0;
										do{
											$seekyear = date("Y", strtotime($seekdate));
											$seekmonth = date("n", strtotime($seekdate));
											$seekday = date("j", strtotime($seekdate));
											
											$weektotal += $report -> stored -> $seekyear -> $seekmonth -> $seekday -> actual -> total;
											$weekcnt += $report -> stored -> $seekyear -> $seekmonth -> $seekday -> actual -> cnt;
											
											$seekdate = date("F j, Y", strtotime("+1 day", strtotime($seekdate)));
										}while($seekdate != $nowdate);
										
										
										echo("<tr>");
										echo("<td>");
										_e('Week',__WCX_WCREPORT_TEXTDOMAIN__);
										echo("</td>");
										echo("<td>");
										echo "#".$weekcnt;
										echo("</td>");
										echo("<td>");
										echo "$".number_format((float)$weektotal, 2, '.', ',');
										echo("</td>");
										echo("</tr>");
										echo("<tr>");
										echo("<td>");
										_e('Month',__WCX_WCREPORT_TEXTDOMAIN__);
										echo("</td>");
										echo("<td>");
										echo "#".$report -> stored -> $seekyear -> $seekmonth -> total -> actual -> cnt;
										echo("</td>");
										echo("<td>");
										echo "$".number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total, 2, '.', ',');
										echo("</td>");
										echo("</tr>");
										echo("<tr>");
										echo("<td>");
										_e('Year',__WCX_WCREPORT_TEXTDOMAIN__);
										echo("</td>");
										echo("<td>");
										echo "#".$report -> stored -> $seekyear -> total -> actual -> cnt;
										echo("</td>");
										echo("<td>");
										echo "$".number_format((float)$report -> stored -> $seekyear -> total -> actual -> total, 2, '.', ',');
										echo("</td>");
										echo("</tr>");
					?>
									</tbody>
								</table>
							</div>
							<div class="row report_chart hide">
								<div id="chart_ordersummary" class="chartlocation" style="height:400px;"></div>
							</div>						
						</div>	
					</div>
				</div>
				<div class="col-md-6">
					<div class="postbox hide">
						<div class="my-menu">
							<span class="showtable"><div class="fa fa-table fa-lg eleman actived" aria-hidden="true"></div></span>
							<span class="showpiechart"><div class="fa fa-pie-chart fa-lg eleman" aria-hidden="true"></div></span>	
							<span class="showbarchart"><div class="fa fa-bar-chart fa-lg eleman" aria-hidden="true"></div></span>						
						</div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Sales Order Status',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row report_table">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Order Status',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Order Status',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php foreach ($report -> stored_os as $key => $val) {
										echo("<tr>");
											echo("<td>");
												echo ($val -> name == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> name;
											echo("</td>");
											echo("<td>");
												echo ($val -> cnt == "")? 0 : $val -> cnt;
											echo("</td>");
											echo("<td>$");
												echo $val -> total;
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
							<div class="row report_chart hide">
								<div id="chart_orderstatus" class="chartlocation" style="height:400px;"></div>
							</div>	
						</div>	
					</div>
				</div>
			</div>
<?php // THE "TOP PRODUCTS" BOX ?>	
			<div class="row">
				<div class="col-md-6">
					<div class="postbox hide">
						<div class="my-menu">
							<span class="showtable"><div class="fa fa-table fa-lg eleman actived" aria-hidden="true"></div></span>
							<span class="showpiechart"><div class="fa fa-pie-chart fa-lg eleman" aria-hidden="true"></div></span>	
							<span class="showbarchart"><div class="fa fa-bar-chart fa-lg eleman" aria-hidden="true"></div></span>						
						</div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Top Products',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row report_table">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Item Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Item Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										foreach ($report -> stored_p as $key => $val) {
											if(!isset($val -> nolist) || (isset($val -> nolist) && $val -> nolist != 1)){
												echo("<tr>");
													echo("<td>");
														echo substr($val -> name,0,15).'...';
													echo("</td>");
													echo("<td>");
														echo ($val -> cnt == "")? 0 : $val -> cnt;
													echo("</td>");
													echo("<td>$");
														echo $val -> total;
													echo("</td>");
												echo("</tr>");
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<div class="row report_chart hide">
								<div id="chart_topproducts" class="chartlocation" style="height:400px;"></div>
							</div>		
						</div>	
					</div>
				</div>
				<div class="col-md-6">
					<div class="postbox hide">
						<div class="my-menu">
							<span class="showtable"><div class="fa fa-table fa-lg eleman actived" aria-hidden="true"></div></span>
							<span class="showpiechart"><div class="fa fa-pie-chart fa-lg eleman" aria-hidden="true"></div></span>	
							<span class="showbarchart"><div class="fa fa-bar-chart fa-lg eleman" aria-hidden="true"></div></span>						
						</div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Top Categories',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row report_table">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Category Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Item Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php foreach ($report -> stored_c as $key => $val) {
										echo("<tr>");
											echo("<td>");
												echo substr($val -> name,0,15).'...';
											echo("</td>");
											echo("<td>");
												echo ($val -> cnt == "")? 0 : $val -> cnt;
											echo("</td>");
											echo("<td>$");
												echo $val -> total;
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
							<div class="row report_chart hide">
								<div id="chart_topcats" class="chartlocation" style="height:400px;"></div>
							</div>		
						</div>	
					</div>
				</div>
			</div>
<?php // THE "BILLING COUNTRIES" BOX ?>	
			<div class="row">
				<div class="col-md-6">
					<div class="postbox hide">
						<div class="my-menu">
							<span class="showtable"><div class="fa fa-table fa-lg eleman actived" aria-hidden="true"></div></span>
							<span class="showpiechart"><div class="fa fa-pie-chart fa-lg eleman" aria-hidden="true"></div></span>	
							<span class="showbarchart"><div class="fa fa-bar-chart fa-lg eleman" aria-hidden="true"></div></span>						
						</div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Top Billing Countries',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row report_table">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Country',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Country',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php foreach ($report -> stored_bc as $key => $val) {
										echo("<tr>");
											echo("<td>");
												echo ($val -> id == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> name;
											echo("</td>");
											echo("<td>");
												echo ($val -> cnt == "")? 0 : $val -> cnt;
											echo("</td>");
											echo("<td>$");
												echo $val -> total;
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
							<div class="row report_chart hide">
								<div id="chart_topbillingcountry" class="chartlocation" style="height:400px;"></div>
							</div>		
						</div>	
					</div>
				</div>
				<div class="col-md-6">
					<div class="postbox hide">
						<div class="my-menu">
							<span class="showtable"><div class="fa fa-table fa-lg eleman actived" aria-hidden="true"></div></span>
							<span class="showpiechart"><div class="fa fa-pie-chart fa-lg eleman" aria-hidden="true"></div></span>	
							<span class="showbarchart"><div class="fa fa-bar-chart fa-lg eleman" aria-hidden="true"></div></span>						
						</div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Top Billing States',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row report_table">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php foreach ($report -> stored_bs as $key => $val) {
										echo("<tr>");
											echo("<td>");
												echo ($val -> id == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> name;
											echo("</td>");
											echo("<td>");
												echo ($val -> cnt == "")? 0 : $val -> cnt;
											echo("</td>");
											echo("<td>$");
												echo $val -> total;
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
							<div class="row report_chart hide">
								<div id="chart_topbillingstate" class="chartlocation" style="height:400px;"></div>
							</div>	
						</div>	
					</div>
				</div>
			</div>
<?php // THE "RECENT ORDERS" BOX ?>	
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Recent Orders',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Order ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Email',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Date',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Status',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Items',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Gross Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Discount Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Shipping Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Shipping Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Order Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Part Refund Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Net Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Order ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Email',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Date',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Status',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Items',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Gross Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Discount Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Shipping Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Shipping Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Order Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Part Refund Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Net Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<?php $query = "
												SELECT 
													posts.ID as ID,
													fnmeta.meta_value as firstname,
													lnmeta.meta_value as lastname,
													emmeta.meta_value as email,
													posts.post_date as pdate,
													posts.post_status as status,
													gameta.meta_value as gross,
													dameta.meta_value as discount,
													sameta.meta_value as shipping,
													stmeta.meta_value as shippingtax,
													otmeta.meta_value as ordertax
													FROM  {$wpdb->posts} AS posts
													LEFT JOIN {$wpdb->postmeta} AS fnmeta ON(posts.ID = fnmeta.post_id)
													LEFT JOIN {$wpdb->postmeta} AS lnmeta ON(posts.ID = lnmeta.post_id)
													LEFT JOIN {$wpdb->postmeta} AS emmeta ON(posts.ID = emmeta.post_id)
													LEFT JOIN {$wpdb->postmeta} AS gameta ON(posts.ID = gameta.post_id)
													LEFT JOIN {$wpdb->postmeta} AS dameta ON(posts.ID = dameta.post_id)
													LEFT JOIN {$wpdb->postmeta} AS sameta ON(posts.ID = sameta.post_id)
													LEFT JOIN {$wpdb->postmeta} AS stmeta ON(posts.ID = stmeta.post_id)
													LEFT JOIN {$wpdb->postmeta} AS otmeta ON(posts.ID = otmeta.post_id)
												WHERE 
													posts.post_status IN ( 'wc-completed','wc-processing','wc-on-hold','wc-pending' ) 
													AND posts.post_type IN ( 'shop_order' ) 
													AND lnmeta.meta_key IN ( '_billing_last_name')
													AND fnmeta.meta_key IN ( '_billing_first_name')
													AND emmeta.meta_key IN ( '_billing_email')
													AND gameta.meta_key IN ( '_order_total')
													AND dameta.meta_key IN ( '_order_discount')
													AND sameta.meta_key IN ( '_order_shipping')
													AND stmeta.meta_key IN ( '_order_shipping_tax')
													AND otmeta.meta_key IN ( '_order_tax')
												ORDER BY
													posts.post_date
													DESC
												";
										$data_recentorders = $wpdb->get_results($query,ARRAY_A);
										$data_recentorders = json_decode(json_encode($data_recentorders), FALSE);
									?>
									<tbody>
										<?php 
										foreach ($data_recentorders as $key => $val) {
										
										echo("<tr>");
											 echo("<td>");
												 echo "#",($val -> ID == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> ID;
											 echo("</td>");
											echo("<td>");
												 echo $val -> firstname, " ", $val -> lastname;
											echo("</td>");
											echo("<td>");
												 echo $val -> email;
											echo("</td>");
											echo("<td>");
												 echo date("F j, Y", strtotime($val -> pdate));
											echo("</td>");
											echo("<td>");
												 echo $statuses[$val -> status];
											echo("</td>");
											echo("<td>");
												 echo "0";
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gross, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> discount, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> shipping, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> shippingtax, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> ordertax, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)0, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> ordertax + $val -> shippingtax, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gross - $val -> discount + $val -> shipping + $val -> ordertax + $val -> shippingtax, 2, '.', ',');
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
<?php // THE "TOP CUSTOMERS" BOX ?>	
			<div class="row">
				<div class="col-md-4">
					<div class="postbox hide">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Top Customers',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Email',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Email',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php foreach ($report -> stored_cd as $key => $val) {
										echo("<tr>");
											echo("<td>");
												echo $val -> fname," ",$val -> lname;
											echo("</td>");
											echo("<td>");
												echo substr($val -> email,0,20).'...';
											echo("</td>");
											echo("<td>");
												echo ($val -> cnt == "")? 0 : $val -> cnt;
											echo("</td>");
											echo("<td>$");
												echo $val -> total;
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
				<div class="col-md-4">
					<div class="postbox hide">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Top Coupons',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Coupon Code',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Coupon Code',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php foreach ($report -> stored_coupon as $key => $val) {
											if(!isset($val -> nolist) || (isset($val -> nolist) && $val -> nolist != 1)){
												echo("<tr>");
													echo("<td>");
														echo ($val -> id == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> name;
													echo("</td>");
													echo("<td>");
														echo ($val -> cnt == "")? 0 : $val -> cnt;
													echo("</td>");
													echo("<td>$");
														echo $val -> total;
													echo("</td>");
												echo("</tr>");
											}
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
				<div class="col-md-4">
					<div class="postbox hide">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Top Payment Gateways',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display nodatatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Payment Method',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Payment Method',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php foreach ($report -> stored_pm as $key => $val) {
										echo("<tr>");
											echo("<td>");
												echo ($val -> name == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> name;
											echo("</td>");
											echo("<td>");
												echo ($val -> cnt == "")? 0 : $val -> cnt;
											echo("</td>");
											echo("<td>$");
												echo $val -> total;
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>	
		<?php
		}
		// THE PRODUCTS PAGE CONTENT
		function wcx_plugin_submenu_products() {
			global $wpdb;
			$report = new wcx_plugin_reports;
			$statuses = Array(
				"wc-completed" => __("Completed",__WCX_WCREPORT_TEXTDOMAIN__), "wc-on-hold" => __("On Hold",__WCX_WCREPORT_TEXTDOMAIN__),"wc-cancelled" => __("Cancelled",__WCX_WCREPORT_TEXTDOMAIN__),"wc-refunded" => __("Refunded",__WCX_WCREPORT_TEXTDOMAIN__),"wc-failed" => __("Failed",__WCX_WCREPORT_TEXTDOMAIN__),"wc-pending" => __("Pending",__WCX_WCREPORT_TEXTDOMAIN__),"wc-processing" => __("Processing",__WCX_WCREPORT_TEXTDOMAIN__)
			);
			// GET SEARCH OPTIONS IF EXISTS OR SETTING THE DEFAULTS IF NOT
			if(isset($_POST['todate']) && $_POST['todate'] != ""){
			   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
			}
			else{
				$_POST['todate'] = date("Y-m-d");
			}
			if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
			   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
			}
			else{
				$_POST['fromdate'] = "1000-01-01";
			}
			if(isset($_POST['status']) && $_POST['status'] != ""){
				if(isset($_POST['status']) && in_array("-1", $_POST['status']))
					$_POST['status'] = Array(
						"wc-completed" => "wc-completed", 
						"wc-on-hold" => "wc-on-hold", 
						"wc-cancelled" => "wc-cancelled", 
						"wc-refunded" => "wc-refunded", 
						"wc-failed" => "wc-failed", 
						"wc-pending" => "wc-pending", 
						"wc-processing" => "wc-processing"
					);
			}
			else{
				$_POST['status'] = Array(
					"wc-completed" => "wc-completed", 
					"wc-on-hold" => "wc-on-hold", 
					"wc-cancelled" => "wc-cancelled", 
					"wc-refunded" => "wc-refunded", 
					"wc-failed" => "wc-failed", 
					"wc-pending" => "wc-pending", 
					"wc-processing" => "wc-processing"
				);
			}
			if(isset($_POST['cat']) && $_POST['cat'] != ""){
				if(isset($_POST['cat']) && in_array("-1", $_POST['cat'])){
					foreach ($report -> stored_c as $key => $val) {
						$_POST['cat'][] = $val -> id;
					}
				}
			}
			else{
				$_POST['cat'] = array();
				foreach ($report -> stored_c as $key => $val) {
					$_POST['cat'][] = $val -> id;
				}
			}
			if(isset($_POST['product']) && $_POST['product'] != ""){
				if(isset($_POST['product']) && in_array("-1", $_POST['product'])){
					foreach ($report -> stored_p as $key => $val) {
						$_POST['product'][] = $val -> id;
					}
				}
			}
			else{
				$_POST['product'] = array();
				foreach ($report -> stored_p as $key => $val) {
					if(!isset($val -> nolist))$_POST['product'][] = $val -> ID;
				}
			}
			$setting['fromdate'] = $_POST['fromdate'];
			$setting['todate'] = $_POST['todate'];
			$setting['status'] = $_POST['status'];
			$setting['product'] = $_POST['product'];
			$setting['cat'] = $_POST['cat'];
		
			?>
	 		
		<div class="wrap">
			<div class="postbox hide">
			<div class="handlediv" title="Click to toggle"><br></div>
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
<?php // THE "CONFIGURATION" BOX ?>							
				<div class="row">
					<form class='alldetails' action='' method='post'>
						<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>
							<div class="col-md-3 sor">
								<?php _e('Category',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="cat[]" multiple="multiple" size="5"  data-size="5">
									<option value="-1"><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<?php
										foreach ($report -> stored_c as $key => $val) {
											if(in_array($val -> id, $setting['cat']))$valselected = 'selected="selected"'; else $valselected = "";
											echo "<option value='".$val -> id."' ".$valselected.">".$val -> name."</option>";
										}
									?>
								</select>  
							</div>	
							<div class="col-md-3 sor">
								<?php _e('Product',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="product[]" multiple="multiple" size="5"  data-size="5">
									<option value="-1"><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<?php
										foreach ($report -> stored_p as $key => $val) {
											
											if(!isset($val -> nolist)){
												if(in_array($val -> ID, $setting['product']))$valselected = 'selected="selected"'; else $valselected = "";
												echo "<option value='".$val -> ID."' ".$valselected.">".$val -> name."</option>";
											}
										}
									?>
								</select>  
								
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
							
							<div class="col-md-3 sor">
								<?php _e('Status',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="status[]" multiple>
									<option value="-1" <?php if(in_array("-1", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-pending" <?php if(in_array("wc-pending", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Pending Payment',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-processing" <?php if(in_array("wc-processing", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Processing',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-on-hold" <?php if(in_array("wc-on-hold", $setting['status']))echo 'selected="selected"'; ?>><?php _e('On Hold',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-completed" <?php if(in_array("wc-completed", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Completed',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-cancelled" <?php if(in_array("wc-cancelled", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Cancelled',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-refunded" <?php if(in_array("wc-refunded", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Refunded',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-failed" <?php if(in_array("wc-failed", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Failed',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
								</select>
							</div>
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
		<?php
		// RUN THE QUERY WITH SEARCHED OPTIONS
		$products = $report -> products($setting['fromdate'],$setting['todate'],$setting['cat'],$setting['status'],$setting['product']);
		?>
<?php // THE "RESULT" BOX ?>	
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
						<div class="my-menu">
							<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
							<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
							<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
							<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
						</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Products',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Product ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Category',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Stock',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Price',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Product ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Category',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Stock',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Price',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									
									<tbody>
										<?php 
										foreach ($report -> stored_c as $key => $val) {
											$allcats[$val -> id] = $val -> name;
										}
										foreach ($products as $key => $val) {
										
										$val -> cat = explode(",", $val -> cat);
										
										echo("<tr>");
											 echo("<td>");
												 echo ($val -> ID == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> ID;
											 echo("</td>");
											echo("<td>");
												 echo substr($val -> name,0,30);
												 if(strlen($val -> name)>30) echo '...';
											echo("</td>");
											echo("<td>");
												echo  implode(", ",array_intersect_key($allcats, array_flip($val -> cat)));
											echo("</td>");
											echo("<td>");
												 echo $val -> gmx -> total_sales;
											echo("</td>");
											echo("<td>");
												 echo number_format((float)$val -> gmx -> _stock, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gmx -> _price, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gmx -> total_sales * $val -> gmx -> _price, 2, '.', ',');
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
<?php
		}
?>
<?php
		// THE STOCK PAGE CONTENT
		function wcx_plugin_submenu_stock() {
		global $wpdb;
		
		$report = new wcx_plugin_reports;
		
		
		$statuses = Array(
				"wc-completed" => __("Completed",__WCX_WCREPORT_TEXTDOMAIN__), "wc-on-hold" => __("On Hold",__WCX_WCREPORT_TEXTDOMAIN__),"wc-cancelled" => __("Cancelled",__WCX_WCREPORT_TEXTDOMAIN__),"wc-refunded" => __("Refunded",__WCX_WCREPORT_TEXTDOMAIN__),"wc-failed" => __("Failed",__WCX_WCREPORT_TEXTDOMAIN__),"wc-pending" => __("Pending",__WCX_WCREPORT_TEXTDOMAIN__),"wc-processing" => __("Processing",__WCX_WCREPORT_TEXTDOMAIN__)
			);
						
						
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		if(isset($_POST['status']) && $_POST['status'] != ""){
			if(isset($_POST['status']) && in_array("-1", $_POST['status']))
				$_POST['status'] = Array(
						"wc-completed" => "wc-completed", 
						"wc-on-hold" => "wc-on-hold", 
						"wc-cancelled" => "wc-cancelled", 
						"wc-refunded" => "wc-refunded", 
						"wc-failed" => "wc-failed", 
						"wc-pending" => "wc-pending", 
						"wc-processing" => "wc-processing"
						);
		}
		else{
			$_POST['status'] = Array(
						"wc-completed" => "wc-completed", 
						"wc-on-hold" => "wc-on-hold", 
						"wc-cancelled" => "wc-cancelled", 
						"wc-refunded" => "wc-refunded", 
						"wc-failed" => "wc-failed", 
						"wc-pending" => "wc-pending", 
						"wc-processing" => "wc-processing"
						);
		}
		if(isset($_POST['cat']) && $_POST['cat'] != ""){
			if(isset($_POST['cat']) && in_array("-1", $_POST['cat'])){
				foreach ($report -> stored_c as $key => $val) {
					$_POST['cat'][] = $val -> id;
				}
			}
		}
		else{
			$_POST['cat'] = array();
			foreach ($report -> stored_c as $key => $val) {
				$_POST['cat'][] = $val -> id;
			}
		}
		if(isset($_POST['product']) && $_POST['product'] != ""){
			if(isset($_POST['product']) && in_array("-1", $_POST['product'])){
				foreach ($report -> stored_p as $key => $val) {
					$_POST['product'][] = $val -> id;
				}
			}
		}
		else{
			$_POST['product'] = array();
			foreach ($report -> stored_p as $key => $val) {
				if(!isset($val -> nolist))$_POST['product'][] = $val -> ID;
			}
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		$setting['status'] = $_POST['status'];
		$setting['product'] = $_POST['product'];
		$setting['cat'] = $_POST['cat'];
		
			?>
		 		
		<div class="wrap">
			<div class="postbox hide">
			<div class="handlediv" title="Click to toggle"><br></div>
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
<?php // THE "CONFIGURATION" BOX ?>	
				<div class="row">
					
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>
							<div class="col-md-3 sor">
								<?php _e('Category',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="cat[]" multiple="multiple" size="5"  data-size="5">
									<option value="-1"><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<?php
										foreach ($report -> stored_c as $key => $val) {
											if(in_array($val -> id, $setting['cat']))$valselected = 'selected="selected"'; else $valselected = "";
											echo "<option value='".$val -> id."' ".$valselected.">".$val -> name."</option>";
										}
									?>
								</select>  
								
							</div>	
							<div class="col-md-3 sor">
								<?php _e('Product',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="product[]" multiple="multiple" size="5"  data-size="5">
									<option value="-1"><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<?php
										foreach ($report -> stored_p as $key => $val) {
											
											if(!isset($val -> nolist)){
												if(in_array($val -> ID, $setting['product']))$valselected = 'selected="selected"'; else $valselected = "";
												echo "<option value='".$val -> ID."' ".$valselected.">".$val -> name."</option>";
											}
										}
									?>
								</select>  
								
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
							
							<div class="col-md-3 sor">
								<?php _e('Status',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="status[]" multiple>
									<option value="-1" <?php if(in_array("-1", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-pending" <?php if(in_array("wc-pending", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Pending Payment',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-processing" <?php if(in_array("wc-processing", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Processing',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-on-hold" <?php if(in_array("wc-on-hold", $setting['status']))echo 'selected="selected"'; ?>><?php _e('On Hold',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-completed" <?php if(in_array("wc-completed", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Completed',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-cancelled" <?php if(in_array("wc-cancelled", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Cancelled',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-refunded" <?php if(in_array("wc-refunded", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Refunded',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-failed" <?php if(in_array("wc-failed", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Failed',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
								</select>
							</div>
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
		<?php
		// RUN THE QUERY WITH SEARCHED OPTIONS
		$products = $report -> products($setting['fromdate'],$setting['todate'],$setting['cat'],$setting['status'],$setting['product']);
		?>
<?php // THE "RESULT" BOX ?>			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
			<div class="handlediv" title="Click to toggle"><br></div>
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Stock',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Product ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Category',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Created Date',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Modified Date',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Sold Qty.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Stock',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Price',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Regular Price',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Sale Price',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Downloadable',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Virtual',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Manage Stock',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Backorder',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Stock Status',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Product ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Category',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Created Date',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Modified Date',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Sold Qty.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Stock',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Price',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Regular Price',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Sale Price',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Downloadable',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Virtual',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Manage Stock',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Backorder',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Stock Status',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									
									<tbody>
										<?php 
										foreach ($report -> stored_c as $key => $val) {
											$allcats[$val -> id] = $val -> name;
										}
										foreach ($products as $key => $val) {
										
										$val -> cat = explode(",", $val -> cat);
										
										echo("<tr>");
											 echo("<td>");
												 echo ($val -> ID == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> ID;
											 echo("</td>");
											echo("<td>");
												 echo substr($val -> name,0,30);
												 if(strlen($val -> name)>30) echo '...';
											echo("</td>");
											echo("<td>");
												echo  implode(", ",array_intersect_key($allcats, array_flip($val -> cat)));
											echo("</td>");
											echo("<td>");
												 echo $val -> cdate;
											echo("</td>");
											echo("<td>");
												 echo $val -> mdate;
											echo("</td>");
											echo("<td>");
												 echo $val -> gmx -> total_sales;
											echo("</td>");
											echo("<td>");
												 echo number_format((float)$val -> gmx -> _stock, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gmx -> _price, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gmx -> _regular_price, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gmx -> _sale_price, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gmx -> total_sales * $val -> gmx -> _price , 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo $val -> gmx -> _downloadable;
											echo("</td>");
											echo("<td>");
												 echo $val -> gmx -> _virtual;
											echo("</td>");
											echo("<td>");
												 echo $val -> gmx -> _manage_stock;
											echo("</td>");
											echo("<td>");
												 echo $val -> gmx -> _backorders;
											echo("</td>");
											echo("<td>");
												 echo $val -> gmx -> _stock_status;
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		function wcx_plugin_submenu_categories() {
		global $wpdb;
		
		$report = new wcx_plugin_reports;
		
		
			$statuses = Array(
				"wc-completed" => __("Completed",__WCX_WCREPORT_TEXTDOMAIN__), "wc-on-hold" => __("On Hold",__WCX_WCREPORT_TEXTDOMAIN__),"wc-cancelled" => __("Cancelled",__WCX_WCREPORT_TEXTDOMAIN__),"wc-refunded" => __("Refunded",__WCX_WCREPORT_TEXTDOMAIN__),"wc-failed" => __("Failed",__WCX_WCREPORT_TEXTDOMAIN__),"wc-pending" => __("Pending",__WCX_WCREPORT_TEXTDOMAIN__),"wc-processing" => __("Processing",__WCX_WCREPORT_TEXTDOMAIN__)
			);
						
						
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		if(isset($_POST['status']) && $_POST['status'] != ""){
			if(isset($_POST['status']) && in_array("-1", $_POST['status']))
				$_POST['status'] = Array(
						"wc-completed" => "wc-completed", 
						"wc-on-hold" => "wc-on-hold", 
						"wc-cancelled" => "wc-cancelled", 
						"wc-refunded" => "wc-refunded", 
						"wc-failed" => "wc-failed", 
						"wc-pending" => "wc-pending", 
						"wc-processing" => "wc-processing"
						);
		}
		else{
			$_POST['status'] = ['wc-completed' => 'wc-completed'];
		}
		if(isset($_POST['cat']) && $_POST['cat'] != ""){
			if(isset($_POST['cat']) && in_array("-1", $_POST['cat'])){
				foreach ($report -> stored_c as $key => $val) {
					$_POST['cat'][] = $val -> id;
				}
			}
		}
		else{
			foreach ($report -> stored_c as $key => $val) {
				$_POST['cat'][] = $val -> id;
			}
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		$setting['status'] = $_POST['status'];
		$setting['cat'] = $_POST['cat'];
		?>
		 		
		<div class="wrap">
						
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			
		<?php
		$categories = $report -> categories($setting['fromdate'],$setting['todate']);
		?>
		
			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Categories',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Category ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Category ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Qty.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									
									<tbody>
										<?php 
										foreach ($categories as $key => $val) {
										
										
										
										echo("<tr>");
											 echo("<td>");
												 echo ($val -> id == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> id;
											 echo("</td>");
											echo("<td>");
												 echo substr($val -> name,0,30);
												 if(strlen($val -> name)>30) echo '...';
											echo("</td>");
											echo("<td>");
												 echo $val -> cnt;
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> total, 2, '.', ',');
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		function wcx_plugin_submenu_customers() {
		global $wpdb;
		$report = new wcx_plugin_reports;
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		 ?> 		
		<div class="wrap">
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			
		<?php
		$customers = $report -> stats($setting['fromdate'],$setting['todate']) -> costumers;
		?>
		
			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Customers',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('No.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('First Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Last Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Email',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('No.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('First Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Last Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Email',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										foreach ($customers as $key => $val) {
											echo("<tr>");
												 echo("<td>");
													 echo ($key == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $key;
												 echo("</td>");
												echo("<td>");
													 echo $val -> fname;
												echo("</td>");
												echo("<td>");
													 echo $val -> lname;
												echo("</td>");
												echo("<td>");
													 echo $val -> email;
												echo("</td>");
												echo("<td>");
													 echo $val -> cnt;
												echo("</td>");
												echo("<td>");
													 echo "$",number_format((float)$val -> total, 2, '.', ',');
												echo("</td>");
											echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		function wcx_plugin_submenu_billingcountries() {
		global $wpdb;
		$report = new wcx_plugin_reports;
		$countrycodes = array (
					'AF' => 'Afghanistan', 'AX' => '?land Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Zaire', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'C𴥠D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island and Mcdonald Islands', 'VA' => 'Vatican City State', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'KENYA', 'KI' => 'Kiribati', 'KP' => 'Korea, Democratic People\'s Republic of', 'KR' => 'Korea, Republic of', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia, the Former Yugoslav Republic of', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States of', 'MD' => 'Moldova, Republic of', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'R궮ion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'PM' => 'Saint Pierre and Miquelon', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome and Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia and the South Sandwich Islands', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard and Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan, Province of China', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania, United Republic of', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks and Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Minor Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis and Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe'
				);
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		
		if(isset($_POST['country']) && $_POST['country'] != ""){
			if(isset($_POST['country']) && in_array("-1", $_POST['country'])){
				foreach ($countrycodes as $key => $val) {
					$_POST['country'][] = $key;
				}
			}
		}
		else{
			$_POST['country'] = array();
			foreach ($countrycodes as $key => $val) {
				$_POST['country'][] = $key;
			}
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		$setting['country'] = $_POST['country'];
		
		 ?> 		
		<div class="wrap">
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('Country',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="country[]" multiple="multiple" size="5"  data-size="5">
									<option value="-1"><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<?php
										foreach ($countrycodes as $key => $val) {
											if(in_array($key, $setting['country']))$valselected = 'selected="selected"'; else $valselected = "";
											echo "<option value='".$key."' ".$valselected.">".$val."</option>";
										}
									?>
								</select>  
								
							</div>		
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			
		<?php
		$billcountries = $report -> stats($setting['fromdate'],$setting['todate'],$setting['country']) -> billcountries;
		?>
		
			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Billing Countries',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Country',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Country',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										foreach ($billcountries as $key => $val) {
											echo("<tr>");
												echo("<td>");
													 echo $val -> name;
												echo("</td>");
												echo("<td>");
													 echo $val -> cnt;
												echo("</td>");
												echo("<td>");
													 echo "$",number_format((float)$val -> total, 2, '.', ',');
												echo("</td>");
											echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		function wcx_plugin_submenu_billingstates() {
		global $wpdb;
		$report = new wcx_plugin_reports;
		$countrycodes = array (
					'AF' => 'Afghanistan', 'AX' => '?land Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Zaire', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'C𴥠D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island and Mcdonald Islands', 'VA' => 'Vatican City State', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'KENYA', 'KI' => 'Kiribati', 'KP' => 'Korea, Democratic People\'s Republic of', 'KR' => 'Korea, Republic of', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia, the Former Yugoslav Republic of', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States of', 'MD' => 'Moldova, Republic of', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'R궮ion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'PM' => 'Saint Pierre and Miquelon', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome and Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia and the South Sandwich Islands', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard and Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan, Province of China', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania, United Republic of', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks and Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Minor Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis and Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe'
				);
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		if(isset($_POST['country']) && $_POST['country'] != ""){
			if(isset($_POST['country']) && in_array("-1", $_POST['country'])){
				foreach ($countrycodes as $key => $val) {
					$_POST['country'][] = $key;
				}
			}
		}
		else{
			$_POST['country'] = array();
			foreach ($countrycodes as $key => $val) {
				$_POST['country'][] = $key;
			}
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		$setting['country'] = $_POST['country'];
		 ?> 		
		<div class="wrap">
		<?php 
						
						?>
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('Country',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="country[]" multiple="multiple" size="5"  data-size="5">
									<option value="-1"><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<?php
										foreach ($countrycodes as $key => $val) {
											if(in_array($key, $setting['country']))$valselected = 'selected="selected"'; else $valselected = "";
											echo "<option value='".$key."' ".$valselected.">".$val."</option>";
										}
									?>
								</select>  
								
							</div>		
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			
		<?php
		$billstates = $report -> stats($setting['fromdate'],$setting['todate'],$setting['country']) -> billstates;
		?>
		
			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Billing States',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										foreach ($billstates as $key => $val) {
											echo("<tr>");
												echo("<td>");
													 echo $val -> name;
												echo("</td>");
												echo("<td>");
													 echo $val -> cnt;
												echo("</td>");
												echo("<td>");
													 echo "$",number_format((float)$val -> total, 2, '.', ',');
												echo("</td>");
											echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		function wcx_plugin_submenu_paymentgateways() {
		global $wpdb;
		$report = new wcx_plugin_reports;
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		 ?> 		
		<div class="wrap">
		<?php 
						
						?>
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			
		<?php
		$paymethods = $report -> stats($setting['fromdate'],$setting['todate']) -> paymethods;
		?>
		
			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Payment Gateways',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										foreach ($paymethods as $key => $val) {
											echo("<tr>");
												echo("<td>");
													 echo $val -> name;
												echo("</td>");
												echo("<td>");
													 echo $val -> cnt;
												echo("</td>");
												echo("<td>");
													 echo "$",number_format((float)$val -> total, 2, '.', ',');
												echo("</td>");
											echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		function wcx_plugin_submenu_orderstatuses() {
		global $wpdb;
		$report = new wcx_plugin_reports;
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		 ?> 		
		<div class="wrap">
		<?php 
						
						?>
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			
		<?php
		$orderstatuses = $report -> stats($setting['fromdate'],$setting['todate']) -> orderstatuses;
		?>
		
			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Order Statuses',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										foreach ($orderstatuses as $key => $val) {
											echo("<tr>");
												echo("<td>");
													 echo $val -> name;
												echo("</td>");
												echo("<td>");
													 echo $val -> cnt;
												echo("</td>");
												echo("<td>");
													 echo "$",number_format((float)$val -> total, 2, '.', ',');
												echo("</td>");
											echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		function wcx_plugin_submenu_coupons() {
		global $wpdb;
		$report = new wcx_plugin_reports;
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		 ?> 		
		<div class="wrap">
		<?php 
						
						?>
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>							
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			
		<?php
		$coupons = $report -> coupons($setting['fromdate'],$setting['todate']);
		?>
		
			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Coupons',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Coupon Code',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('State',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Count',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										foreach ($coupons as $key => $val) {
											if(!isset($val -> nolist) || $val -> nolist != 1){
												echo("<tr>");
												echo("<td>");
													 echo $val -> name;
												echo("</td>");
												echo("<td>");
													 echo $val -> cnt;
												echo("</td>");
												echo("<td>");
													 echo "$",number_format((float)$val -> total, 2, '.', ',');
												echo("</td>");
												echo("</tr>");
											}
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		function wcx_plugin_submenu_projected() {
		global $wpdb;
		$report = new wcx_plugin_reports;
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		$curyear = date("Y");
		 ?> 		
		<div class="wrap">
		<?php 
						
						?>
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('Select Year',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select class="optyr" name="year">
									<?php
										for($iii = -5; $iii < 6; $iii ++){
											$pref = ($iii<0)?'m':'p';
											$slct = ($iii==0)?' selected="selected" ':' ';
											echo "<option value='m",$pref,abs($iii),"' ",$slct," >",$curyear+$iii,"</option>";
										}
									?>
								</select>
							</div>						
						</div>							
					</form>
				</div>	
			</div>	
			</div>	
						
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Projected Vs. Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<?php
				for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
			?>
			<?php echo "<div class='row msel m",$pref,abs($iii),"'>"; ?>
								<table class="display npnsdatatable" cellspacing="0" width="100%">
						<thead>
							<tr>			
								<th><?php _e('Month',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Difference',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('%',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total % to Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Refund Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Part Refund Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total Discount Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Shipping Order Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total Shipping Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</thead>
				
						<tfoot>
							<tr>			
								<th><?php _e('Month',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Actual Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Difference',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('%',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total % to Sales',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Refund Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Part Refund Amount',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total Discount Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Shipping Order Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
								<th><?php _e('Total Shipping Tax',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
							</tr>
						</tfoot>
					 
						<tbody>
					<?php
					$nowdate = date('F j, Y', mktime(0,0,0,1,1,$curyear+$iii+1));
					$seekdate = date("F j, Y", strtotime("-12 months", strtotime($nowdate)));
					do{
						$seekyear = date("Y", strtotime($seekdate));
						$seekmonth = date("n", strtotime($seekdate));
						echo "<tr>";
								echo "<td>";
									echo date('F-Y', strtotime($seekdate));
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo ($report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total < $report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total)? "-" : "+";
									echo "$";
									echo number_format((float)abs($report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total - $report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total), 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo ($report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total == 0)? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : number_format( (float)($report -> stored -> $seekyear -> $seekmonth -> total -> actual -> total / $report -> stored -> $seekyear -> $seekmonth -> total -> proj -> total)*100, 2, '.', ',')."%";
									
								echo "</td>";
								echo "<td>";
									echo "0%";
								
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> refund -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format(($report -> stored -> $seekyear -> $seekmonth -> total -> refund -> cnt == 0)? 0 : (float)$report -> stored -> $seekyear -> $seekmonth -> total -> refund -> total/$report -> stored -> $seekyear -> $seekmonth -> total -> refund -> cnt, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> discount -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> tax -> total, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format(($report -> stored -> $seekyear -> $seekmonth -> total -> shiptax -> cnt == 0)? 0 : (float)$report -> stored -> $seekyear -> $seekmonth -> total -> shiptax -> total/$report -> stored -> $seekyear -> $seekmonth -> total -> shiptax -> cnt, 2, '.', ',');
								echo "</td>";
								echo "<td>";
									echo "$";
									echo number_format((float)$report -> stored -> $seekyear -> $seekmonth -> total -> shiptax -> total, 2, '.', ',');
								echo "</td>";
								
							echo "</tr>";
						
						
						$seekdate = date("F j, Y", strtotime("+1 month", strtotime($seekdate)));
					}while($seekdate != $nowdate);	
					
					
?>
						</tbody>
					</table>
							</div>	
							<?php
				}
							?>
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		
		function wcx_plugin_submenu_orders() {
		global $wpdb;
		$report = new wcx_plugin_reports;
		
		
			$statuses = Array(
				"wc-completed" => __("Completed",__WCX_WCREPORT_TEXTDOMAIN__), "wc-on-hold" => __("On Hold",__WCX_WCREPORT_TEXTDOMAIN__),"wc-cancelled" => __("Cancelled",__WCX_WCREPORT_TEXTDOMAIN__),"wc-refunded" => __("Refunded",__WCX_WCREPORT_TEXTDOMAIN__),"wc-failed" => __("Failed",__WCX_WCREPORT_TEXTDOMAIN__),"wc-pending" => __("Pending",__WCX_WCREPORT_TEXTDOMAIN__),"wc-processing" => __("Processing",__WCX_WCREPORT_TEXTDOMAIN__)
			);
						
						
		if(isset($_POST['todate']) && $_POST['todate'] != ""){
		   $_POST['todate'] = date("Y-m-d",strtotime($_POST['todate']));
		}
		else{
			$_POST['todate'] = date("Y-m-d");
		}
		if(isset($_POST['fromdate']) && $_POST['fromdate'] != ""){
		   $_POST['fromdate'] = date("Y-m-d",strtotime($_POST['fromdate']));
		}
		else{
			$_POST['fromdate'] = "1000-01-01";
		}
		if(isset($_POST['status']) && $_POST['status'] != ""){
			if(isset($_POST['status']) && in_array("-1", $_POST['status']))
				$_POST['status'] = Array(
						"wc-completed" => "wc-completed", 
						"wc-on-hold" => "wc-on-hold", 
						"wc-cancelled" => "wc-cancelled", 
						"wc-refunded" => "wc-refunded", 
						"wc-failed" => "wc-failed", 
						"wc-pending" => "wc-pending", 
						"wc-processing" => "wc-processing"
						);
		}
		else{
			$_POST['status'] = ['wc-completed' => 'wc-completed'];
		}
		if(isset($_POST['cat']) && $_POST['cat'] != ""){
			if(isset($_POST['cat']) && in_array("-1", $_POST['cat'])){
				foreach ($report -> stored_c as $key => $val) {
					$_POST['cat'][] = $val -> id;
				}
			}
		}
		else{
			foreach ($report -> stored_c as $key => $val) {
				$_POST['cat'][] = $val -> id;
			}
		}
		$setting['fromdate'] = $_POST['fromdate'];
		$setting['todate'] = $_POST['todate'];
		$setting['status'] = $_POST['status'];
		$setting['cat'] = $_POST['cat'];
		
									
									
					
		$recentorders = $report -> recentorders($setting['fromdate'],$setting['todate'],$setting['status']);		
			?>
		 		
		<div class="wrap">
						
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Configuration',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('From Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="fromdate" type="text" readonly='true' class="datepick" value="<?php echo $setting['fromdate']; ?>" />
							</div>
							<div class="col-md-3 sor">
								<?php _e('Status',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select name="status[]" multiple>
									<option value="-1" <?php if(in_array("-1", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Select All',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-pending" <?php if(in_array("wc-pending", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Pending Payment',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-processing" <?php if(in_array("wc-processing", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Processing',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-on-hold" <?php if(in_array("wc-on-hold", $setting['status']))echo 'selected="selected"'; ?>><?php _e('On Hold',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-completed" <?php if(in_array("wc-completed", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Completed',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-cancelled" <?php if(in_array("wc-cancelled", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Cancelled',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-refunded" <?php if(in_array("wc-refunded", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Refunded',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
									<option value="wc-failed" <?php if(in_array("wc-failed", $setting['status']))echo 'selected="selected"'; ?>><?php _e('Failed',__WCX_WCREPORT_TEXTDOMAIN__);?></option>
								</select>
							</div>						
						</div>
						<div class="col-md-6">
							<div class="col-md-3 sor">
								<?php _e('To Date',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<input name="todate" type="text" readonly='true' class="datepick" value="<?php echo $setting['todate']; ?>" />
							</div>
						</div>
						<div class="col-md-12">
							<input type="button" value="Reset" class="button-secondary"/>
							<input type="submit" value="Search" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			

		
			
			<div class="row">
				<div class="col-md-12">
					<div class="postbox hide">
				<div class="my-menu">
					<span class="exportpdf"><div class="eleman"><?php _e('PDF',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportcsv"><div class="eleman"><?php _e('CSV',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportxls"><div class="eleman"><?php _e('XLS',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>
					<span class="exportprint"><div class="eleman"><?php _e('Print',__WCX_WCREPORT_TEXTDOMAIN__);?></div></span>					
				</div> 
						<h3 class="hndle ui-sortable-handle"><span><?php _e('Orders',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
						<div class="inside">
							<div class="row">
								<table class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>			
											<th><?php _e('Order ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Email',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Date',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Status',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Items',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Gross Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Discount Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Shipping Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Shipping Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Order Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Part Refund Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Net Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</thead>
									<tfoot>
										<tr>			
											<th><?php _e('Order ID',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Name',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Email',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Date',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Status',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Items',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Gross Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Discount Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Shipping Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Shipping Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Order Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Part Refund Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Total Tax Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
											<th><?php _e('Net Amt.',__WCX_WCREPORT_TEXTDOMAIN__);?></th>
										</tr>
									</tfoot>
									
									<tbody>
										<?php 
										foreach ($recentorders as $key => $val) {
										
										echo("<tr>");
											 echo("<td>");
												 echo "#",($val -> ID == "")? __('N/A',__WCX_WCREPORT_TEXTDOMAIN__) : $val -> ID;
											 echo("</td>");
											echo("<td>");
												 echo $val -> firstname, " ", $val -> lastname;
											echo("</td>");
											echo("<td>");
												 echo $val -> email;
											echo("</td>");
											echo("<td>");
												 echo date("F j, Y", strtotime($val -> pdate));
											echo("</td>");
											echo("<td>");
												 echo $statuses[$val -> status];
											echo("</td>");
											echo("<td>");
												 echo "0";
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gross, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> discount, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> shipping, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> shippingtax, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> ordertax, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)0, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> ordertax + $val -> shippingtax, 2, '.', ',');
											echo("</td>");
											echo("<td>");
												 echo "$",number_format((float)$val -> gross - $val -> discount + $val -> shipping + $val -> ordertax + $val -> shippingtax, 2, '.', ',');
											echo("</td>");
										echo("</tr>");
										}
										?>
									</tbody>
								</table>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
		
		</div>
<?php
		}
		
		function wcx_plugin_submenu_preferences() {
		//global $wpdb;	
		$curyr = date("Y");
		if(isset($_POST['newsettings'])){
			//var_dump($_POST);
		//delete_option( 'wcx_reporting_'+'proj_'+ );
			$opt = Array();
			foreach ($_POST as $key => $val) {
				if($key != 'newsettings' && $key != 'action' && $key != 'year'){
					$key = explode("_", $key, 3);
					if(!isset($opt[$key[1]]))$opt[$key[1]] = Array();
					$opt[$key[1]][$key[2]] = abs($val);
					//add_option( 'wcx_reporting_'+$key, $val, '', 'no' );
					//echo $key," -> ",$val,"<br>";
					
					
				}
			}
		//var_dump($opt);
			
			update_option( 'wcx_reporting_options_prj', $opt, 'no' );
			//add_option( 'wcx_reporting_'+$opt, $val, '', 'no' );
		}
		$options = get_option( 'wcx_reporting_options_prj' );
		//var_dump($options);
		?>
		<div class="wrap">
						
		
			<div class="postbox hide">
			<h3 class="hndle ui-sortable-handle"><span><?php _e('Preferences',__WCX_WCREPORT_TEXTDOMAIN__);?></span></h3>
			<div class="inside">
				<div class="row">
					<form class='alldetails' action='' method='post'>
					<input type='hidden' name='action' value='submit-form' />
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('Select Year',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<select class="optyr" name="year">
									<?php
										for($iii = -5; $iii < 6; $iii ++){
											$pref = ($iii<0)?'m':'p';
											$slct = ($iii==0)?' selected="selected" ':' ';
											echo "<option value='m",$pref,abs($iii),"' ",$slct," >",$curyr+$iii,"</option>";
										}
									?>
								</select>
							</div>						
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('January Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['1'])) ? $options[$curyr+$iii]['1'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_1' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('February Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['2'])) ? $options[$curyr+$iii]['2'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_2' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('March Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['3'])) ? $options[$curyr+$iii]['3'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_3' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('April Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['4'])) ? $options[$curyr+$iii]['4'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_4' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('May Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['5'])) ? $options[$curyr+$iii]['5'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_5' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('June Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['6'])) ? $options[$curyr+$iii]['6'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_6' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('July Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['7'])) ? $options[$curyr+$iii]['7'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_7' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('August Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['8'])) ? $options[$curyr+$iii]['8'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_8' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('September Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['9'])) ? $options[$curyr+$iii]['9'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_9' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('October Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['10'])) ? $options[$curyr+$iii]['10'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_10' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('November Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['11'])) ? $options[$curyr+$iii]['11'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_11' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-3 sor">
								<?php _e('December Projected Sales',__WCX_WCREPORT_TEXTDOMAIN__);?>:
							</div>
							<div class="col-md-9 sor">
								<?php
									for($iii = -5; $iii < 6; $iii ++){
										$pref = ($iii<0)?'m':'p';
										$valx = (isset($options[$curyr+$iii]['12'])) ? $options[$curyr+$iii]['12'] : 0;
										echo "<input name='prj_",$curyr+$iii,"_12' class='msel m",$pref,abs($iii),"' type='text' value='",$valx,"' />";
									}
								?>
							</div>
						</div>
						<div class="col-md-12">
							<input type="submit" name="newsettings" value="Save Settings" class="button-primary"/>							
						</div>						
					</form>
				</div>	
			</div>	
			</div>	
			
			
		
		</div>
<?php
		}
		
	}
?>