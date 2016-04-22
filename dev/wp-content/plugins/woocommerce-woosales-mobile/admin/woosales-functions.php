<?php
//phpinfo();
/*
	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once( $parse_uri[0] . 'wp-load.php' );
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
*/

	global $wpdb;
	$year0 = date('Y');
	$year1 = date('Y',strtotime("-1 year"));
	$month0 = date('m');
	$month1 = date("m",strtotime("-1 month"));
	$month2 = date("m",strtotime("-2 month"));
	$month3 = date("m",strtotime("-3 month"));
	$month4 = date("m",strtotime("-4 month"));
	$month5 = date("m",strtotime("-5 month"));
	$month6 = date("m",strtotime("-6 month"));
	$monthy1 = '01';
	$monthy2 = '02';
	$monthy3 = '03';
	$monthy4 = '04';
	$monthy5 = '05';
	$monthy6 = '06';
	$monthy7 = '07';
	$monthy8 = '08';
	$monthy9 = '09';
	$monthy10 = '10';
	$monthy11 = '11';
	$monthy12 = '12';
	$month0y = date('Y');
	$month1y = date("Y",strtotime("-1 month"));
	$month2y = date("Y",strtotime("-2 month"));
	$month3y = date("Y",strtotime("-3 month"));
	$month4y = date("Y",strtotime("-4 month"));
	$month5y = date("Y",strtotime("-5 month"));
	$month6y = date("Y",strtotime("-6 month"));
	$today0 = date('Y-m-d');
	$today1 = date('Y-m-d',time()-60*60*24);
	$today2 = date('Y-m-d',time()-2*60*60*24);
	$today3 = date('Y-m-d',time()-3*60*60*24);
	$today4 = date('Y-m-d',time()-4*60*60*24);
	$today5 = date('Y-m-d',time()-5*60*60*24);
	$today6 = date('Y-m-d',time()-6*60*60*24);
	$today7 = date('Y-m-d',time()-7*60*60*24);
	
	//$totalUsers = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}users" );
	$totalUsersTemp = count_users();
	$totalUsers = $totalUsersTemp['total_users'];
	//$totalCustomersSQL = new WP_User_Query( array( 'role' => 'Customer' ) );
	//$totalCustomers = $totalCustomersSQL->total_users;
	$totalCustomers = $totalUsersTemp['avail_roles']['customer'];
	$shopName = get_bloginfo('name');
	$shopCurrency = get_woocommerce_currency_symbol();
	$ordersTotal = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}posts WHERE post_type='shop_order'");
	// GET CUSTOMERS FOR TODAY+
	for ($i = 0; $i <= 7; $i++) {
		${"totalCustomersDay".$i} = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}users as users WHERE DATE(users.user_registered) = '".${"today".$i}."' " );
	}
	
	// GET STATS FOR TODAY+				
	$sql = "";
	for ($i = 0; $i <= 7; $i++) {
		$sql .= "SELECT SUM(postmeta.meta_value) AS 'OrderTotal', COUNT(*) AS 'OrderCount', '".${"today".$i}."' AS 'Text'
				FROM {$wpdb->prefix}postmeta as postmeta 
				LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id
				WHERE meta_key='_order_total' AND post_type = 'shop_order' AND DATE(posts.post_date) = '".${"today".$i}."' ";
		if ($i < 7) {$sql .= " UNION ";} } $reportTodayResult = $wpdb->get_results($sql);	
	

	// GET ITEMS FOR TODAY+				
	$sql = '';
	for ($i = 0; $i <= 7; $i++) {
		$sql .= "SELECT COUNT(orderitems.order_item_type) AS 'ItemsTotal', '".${"today".$i}."' AS 'Text'
						FROM {$wpdb->prefix}woocommerce_order_items as orderitems 
						LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=orderitems.order_id
						LEFT JOIN  {$wpdb->prefix}posts as posts ON postmeta.post_id=posts.ID
						WHERE postmeta.meta_key = '_completed_date' AND DATE(postmeta.meta_value) = '".${"today".$i}."' ";
		if ($i < 7) {$sql .= " UNION ";} } $reportTodayItems = $wpdb->get_results($sql);	
	
	// PREPARE TODAY+ and WEEK
	$weekOrderTotal = $weekOrderCount = $weekItemsTotal = $weekCustomersTotal = 0;
	for ($i = 0; $i <= 7; $i++) {
		${"reportToday".$i} = array('total' => (float)$reportTodayResult[$i]->OrderTotal,'orders' => intval($reportTodayResult[$i]->OrderCount),'items' => intval($reportTodayItems[$i]->ItemsTotal),'customers' => intval(${"totalCustomersDay".$i}));
		if ($i < 7) {
			$weekOrderTotal += (float)$reportTodayResult[$i]->OrderTotal;
			$weekOrderCount += $reportTodayResult[$i]->OrderCount;
			$weekItemsTotal += $reportTodayItems[$i]->ItemsTotal;
			$weekCustomersTotal += ${"totalCustomersDay".$i};
		}
	}

	// PREPARE WEEK
	$reportWeek = array('total' => $weekOrderTotal,'orders' => $weekOrderCount,'items' => $weekItemsTotal,'customers' => $weekCustomersTotal);

	// GET STATS FOR MONTH+				
	$sql = "";
	for ($i = 0; $i <= 6; $i++) {
		$sql .= "SELECT SUM(postmeta.meta_value) AS 'OrderTotal', COUNT(*) AS 'OrderCount','".${"month".$i."y"}."-".${"month".$i}."' AS 'Text'
				FROM {$wpdb->prefix}postmeta as postmeta 
				LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id
				WHERE meta_key='_order_total' AND post_type = 'shop_order' AND YEAR(DATE(posts.post_date)) = '".${"month".$i."y"}."' AND MONTH(DATE(posts.post_date)) = '".${"month".$i}."' ";
		if ($i < 6) {$sql .= " UNION ";} } $reportMonthResult = $wpdb->get_results($sql);	
	

	// GET ITEMS FOR MONTH+				
	$sql = '';
	for ($i = 0; $i <= 6; $i++) {
		$sql .= "SELECT COUNT(orderitems.order_item_type) AS 'ItemsTotal', '".${"month".$i."y"}."-".${"month".$i}."' AS 'Text'
						FROM {$wpdb->prefix}woocommerce_order_items as orderitems 
						LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=orderitems.order_id
						LEFT JOIN  {$wpdb->prefix}posts as posts ON postmeta.post_id=posts.ID
						WHERE postmeta.meta_key = '_completed_date' AND YEAR(DATE(posts.post_date)) = '".${"month".$i."y"}."' AND MONTH(DATE(posts.post_date)) = '".${"month".$i}."' ";
		if ($i < 6) {$sql .= " UNION ";} } $reportMonthItems = $wpdb->get_results($sql);	
	
	// GET CUSTOMERS FOR MONTH+
	for ($i = 0; $i <= 6; $i++) {
		${"totalCustomersMonth".$i} = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users as users WHERE YEAR(DATE(users.user_registered)) = '".${"month".$i."y"}."' AND MONTH(DATE(users.user_registered)) = '".${"month".$i}."' " );
	}


	// PREPARE MONTH+
	for ($i = 0; $i <= 6; $i++) {
		${"reportMonth".$i} = array('total' => (float)$reportMonthResult[$i]->OrderTotal,'orders' => intval($reportMonthResult[$i]->OrderCount),'items' => intval($reportMonthItems[$i]->ItemsTotal),'customers' => intval(${"totalCustomersMonth".$i}));
	}



	// GET STATS FOR YEAR+				
	$sql = "";
	for ($i = 0; $i <= 1; $i++) {
		$sql .= "SELECT SUM(postmeta.meta_value) AS 'OrderTotal', COUNT(*) AS 'OrderCount','".${"year".$i}."' AS 'Text'
				FROM {$wpdb->prefix}postmeta as postmeta 
				LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id
				WHERE meta_key='_order_total' AND post_type = 'shop_order' AND YEAR(DATE(posts.post_date)) = '".${"year".$i}."' ";
		if ($i < 1) {$sql .= " UNION ";} } $reportYearResult = $wpdb->get_results($sql);	
	

	// GET ITEMS FOR YEAR+				
	$sql = '';
	for ($i = 0; $i <= 1; $i++) {
		$sql .= "SELECT COUNT(orderitems.order_item_type) AS 'ItemsTotal', '".${"year".$i}."' AS 'Text'
						FROM {$wpdb->prefix}woocommerce_order_items as orderitems 
						LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=orderitems.order_id
						LEFT JOIN  {$wpdb->prefix}posts as posts ON postmeta.post_id=posts.ID
						WHERE postmeta.meta_key = '_completed_date' AND YEAR(DATE(posts.post_date)) = '".${"year".$i}."' ";
		if ($i < 1) {$sql .= " UNION ";} } $reportYearItems = $wpdb->get_results($sql);	
	
	// GET CUSTOMERS FOR YEAR+
	for ($i = 0; $i <= 1; $i++) {
		${"totalCustomersYear".$i} = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users as users WHERE YEAR(DATE(users.user_registered)) = '".${"year".$i}."' " );
	}
	
	$sql = '';
	for ($i = 1; $i <= 12; $i++) {
		$sql .= "SELECT SUM(postmeta.meta_value) AS 'OrderTotal', '".$year0."-".${"monthy".$i}."' AS 'Text'
				FROM {$wpdb->prefix}postmeta as postmeta 
				LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id
				WHERE meta_key='_order_total' AND post_type = 'shop_order' AND YEAR(DATE(posts.post_date)) = '".$year0."' AND MONTH(DATE(posts.post_date)) = '".${"monthy".$i}."' ";
		if ($i < 12) {$sql .= " UNION ";} } $reportYear0MonthResult = $wpdb->get_results($sql);	
	
	$sql = '';
	for ($i = 1; $i <= 12; $i++) {
		$sql .= "SELECT SUM(postmeta.meta_value) AS 'OrderTotal', '".$year1."-".${"monthy".$i}."' AS 'Text'
				FROM {$wpdb->prefix}postmeta as postmeta 
				LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id
				WHERE meta_key='_order_total' AND post_type = 'shop_order' AND YEAR(DATE(posts.post_date)) = '".$year1."' AND MONTH(DATE(posts.post_date)) = '".${"monthy".$i}."' ";
		if ($i < 12) {$sql .= " UNION ";} } $reportYear1MonthResult = $wpdb->get_results($sql);	
		
	
	// GET MONTHLY FOR YEAR+
	$monthsReport0 = array('year' => intval($year0), 'jan' => (float)$reportYear0MonthResult[0]->OrderTotal, 'feb' => (float)$reportYear0MonthResult[1]->OrderTotal, 'mar' => (float)$reportYear0MonthResult[2]->OrderTotal, 'apr' => (float)$reportYear0MonthResult[3]->OrderTotal, 'may' => (float)$reportYear0MonthResult[4]->OrderTotal, 'jun' => (float)$reportYear0MonthResult[5]->OrderTotal, 'jul' => (float)$reportYear0MonthResult[6]->OrderTotal, 'aug' => (float)$reportYear0MonthResult[7]->OrderTotal, 'sep' => (float)$reportYear0MonthResult[8]->OrderTotal, 'oct' => (float)$reportYear0MonthResult[9]->OrderTotal, 'nov' => (float)$reportYear0MonthResult[10]->OrderTotal, 'dec' => (float)$reportYear0MonthResult[11]->OrderTotal);
	$monthsReport1 = array('year' => $year1, 'jan' => (float)$reportYear1MonthResult[0]->OrderTotal, 'feb' => (float)$reportYear1MonthResult[1]->OrderTotal, 'mar' => (float)$reportYear1MonthResult[2]->OrderTotal, 'apr' => (float)$reportYear1MonthResult[3]->OrderTotal, 'may' => (float)$reportYear1MonthResult[4]->OrderTotal, 'jun' => (float)$reportYear1MonthResult[5]->OrderTotal, 'jul' => (float)$reportYear1MonthResult[6]->OrderTotal, 'aug' => (float)$reportYear1MonthResult[7]->OrderTotal, 'sep' => (float)$reportYear1MonthResult[8]->OrderTotal, 'oct' => (float)$reportYear1MonthResult[9]->OrderTotal, 'nov' => (float)$reportYear1MonthResult[10]->OrderTotal, 'dec' => (float)$reportYear1MonthResult[11]->OrderTotal);
	


	// PREPARE YEAR+
	for ($i = 0; $i <= 1; $i++) {
		${"reportYear".$i} = array('total' => (float)$reportYearResult[$i]->OrderTotal,'orders' => intval($reportYearResult[$i]->OrderCount),'items' => intval($reportYearItems[$i]->ItemsTotal),'customers' => intval(${"totalCustomersYear".$i}), 'months' => ${"monthsReport".$i});
	}

		/*$sql .= "SELECT * FROM {$wpdb->prefix}postmeta as postmeta 
				LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id
				WHERE post_type = 'shop_order' ORDER BY post_date DESC LIMIT 2 ";*/
		$sql = '';
		$sql = "SELECT * FROM {$wpdb->prefix}posts as posts WHERE posts.post_type = 'shop_order' ORDER BY posts.post_date DESC LIMIT 10";
		$lastOrdersSQL = $wpdb->get_results($sql);

		

		function get_order_items_count($order_id) {
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id=$order_id AND order_item_type='line_item'" );
		}
		function get_order_coupons_count($order_id) {
			global $wpdb;
			$result = '';
			$result = $wpdb->get_results( "SELECT count(*) as count, order_item_name as name FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id=$order_id AND order_item_type='coupon'" );
			return $result[0]->count;
		}
		function get_order_coupons($order_id) {
			global $wpdb;
			$coupons = '';
			$thecoupons = $wpdb->get_results( "SELECT items.order_item_id as id, items.order_item_name as name FROM {$wpdb->prefix}woocommerce_order_items as items WHERE order_id=$order_id AND order_item_type='coupon'" );
			if(!empty($thecoupons)) {
				foreach($thecoupons as $thecoupon) {
					$coupons[] = array('id' => '', 'code' => $thecoupon->name, 'amount' => floatval($wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id=$thecoupon->id AND meta_key='discount_amount'")));
				}
				return $coupons;
			} else {
				return '0';
			}
		}
		function get_order_products($order_id) {
			global $wpdb;
			$theproducts = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id=$order_id AND order_item_type='line_item'" );
			foreach ($theproducts as $theproduct) {
				$products[] = array(
				'productName' => $theproduct->order_item_name,
				'productQty' => intval($wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id=$theproduct->order_item_id AND meta_key='_qty'")),
				'productPrice' => floatval($wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id=$theproduct->order_item_id AND meta_key='_line_subtotal'" )),
				'productTotal' => floatval($wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id=$theproduct->order_item_id AND meta_key='_line_total'" ))
				);
			}
			return $products;
		}
		//print_r($lastOrdersSQL);
		foreach ($lastOrdersSQL as $item) {
			//print_r($item);
			$lastOrders[] = array(
			//'orderNo' => get_post_meta($item->ID,'_order_number',true),
			'orderNo' => ((get_post_meta($item->ID,'_order_number',true) != '') ? '#'.get_post_meta($item->ID,'_order_number',true) : '#'.$item->ID),
			//'orderDate' => date("Y/m/d H:i:s", strtotime($row->created_at)),
			//'orderDate' => strtotime($row->completed_at),
			'orderDate' => date("Y/m/d H:i:s", strtotime($item->post_date)),
			'orderStatus' => str_replace('wc-', '', $item->post_status),
			'orderCustomerName' => get_post_meta($item->ID,'_billing_first_name',true).' '.get_post_meta($item->ID,'_billing_last_name',true),			
			//'orderCustomerName' => ((!empty(get_post_meta($item->ID,'_customer_user',true))) ? (get_userdata(intval(get_post_meta($item->ID,'_customer_user',true)))->first_name.' '.get_userdata(intval(get_post_meta($item->ID,'_customer_user',true)))->last_name) : (get_post_meta($item->ID,'_billing_first_name',true).' '.get_post_meta($item->ID,'_billing_last_name',true))),
			
			
			
			
			'orderItemCount' => intval(get_order_items_count($item->ID)),
			'orderTotal' => floatval(get_post_meta($item->ID,'_order_total',true)),
			'orderCurrency' => $shopCurrency,
			'orderShippingMethod' => ((get_post_meta($item->ID,'_order_shipping',true) != '') ? get_post_meta($item->ID,'_order_shipping',true) : 'None'),
			'orderShippingAddress' => ((get_post_meta($item->ID,'_shipping_address_1',true) != '') ? get_post_meta($item->ID,'_shipping_address_1',true).' '.get_post_meta($item->ID,'_shipping_address_2',true).' '.get_post_meta($item->ID,'_shipping_postcode',true).' '.get_post_meta($item->ID,'_shipping_state',true).' '.get_post_meta($item->ID,'_shipping_country',true) : 'None'),
			'orderCustomerPhone' => ((get_post_meta($item->ID,'_billing_phone',true) != '') ? get_post_meta($item->ID,'_billing_phone',true) : 'None'),
			'orderCustomerEmail' =>  ((get_post_meta($item->ID,'_billing_email',true) != '') ? get_post_meta($item->ID,'_billing_email',true) : 'None'),
			'orderCoupons' => ((get_order_coupons_count($item->ID) > 0) ? get_order_coupons($item->ID) : 'None'),
			'orderItemsCount' => get_order_items_count($item->ID),
			'orderItems' => get_order_products($item->ID)
			);
		}
		
		
/*
			$lastOrders[] = array(
			'orderNo' => $row->order_number,
			//'orderDate' => date("Y/m/d H:i:s", strtotime($row->created_at)),
			//'orderDate' => strtotime($row->completed_at),
			'orderDate' => date("Y/m/d H:i:s", $theOrderRealtime),
			'orderStatus' => $row->status,
			'orderCustomerName' => $row->customer->first_name.' '.$row->customer->last_name,
			'orderItemCount' => $total_products,
			'orderTotal' => (float)$row->total,
			'orderCurrency' => $basic_info->store->meta->currency_format,
			'orderShippingMethod' => (($row->shipping_methods != '') ? $row->shipping_methods : 'None'),
			'orderShippingAddress' => (($row->shipping_address->address_1 != '') ? $row->shipping_address->address_1.' '.$row->shipping_address->address_2.' '.$row->shipping_address->postcode.' '.$row->shipping_address->state.' '.$row->shipping_address->country : 'None'),
			'orderCustomerPhone' => (($row->customer->billing_address->phone != '') ? $row->customer->billing_address->phone : 'None'),
			'orderCustomerEmail' => (($row->customer->email != '') ? $row->customer->email : 'None'),
			'orderCoupons' => ((!empty($row->coupon_lines)) ? $row->coupon_lines : 'None'),
			'orderItemsCount' => count($products),
			'orderItems' => $products
			);

				$products[] = array(
				'productName' => $product->name,
				'productQty' => $product->quantity,
				'productPrice' => (float)$product->subtotal,
				'productTotal' => (float)$product->total
				);
*/


$resultok = array(
'access' => 'yes',
'usersTotal' => intval($totalUsers),
'shopName' => $shopName,
'usersActiveCustomers' => intval($totalCustomers),
'shopCurrency' => $shopCurrency,
'totalCountAllOrders' => intval($ordersTotal),
'reportToday' => $reportToday0,
'reportToday1' => $reportToday1,
'reportToday2' => $reportToday2,
'reportToday3' => $reportToday3,
'reportToday4' => $reportToday4,
'reportToday5' => $reportToday5,
'reportToday6' => $reportToday6,
'reportToday7' => $reportToday7,
'reportWeek' => $reportWeek,
'reportMonth' => $reportMonth0,
'reportLastMonth' => $reportMonth1,
'reportMonth2' => $reportMonth2,
'reportMonth3' => $reportMonth3,
'reportMonth4' => $reportMonth4,
'reportMonth5' => $reportMonth5,
'reportMonth6' => $reportMonth6,
'reportYear' => $reportYear0,
'reportLastYear' => $reportYear1,
'lastOrders' => $lastOrders,
'versionReport' => 24,
);

//print_r($resultok);

?>