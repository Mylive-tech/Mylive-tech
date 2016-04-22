<?php
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once( '../../../wp-load.php' );
//include('admin/woosales-functions.php');
$woosalesapi_check = get_option('woosales_api');

$deviceid_get = $_GET['deviceid'];
$asset = $_GET['asset'];

$deviceid_check = get_option('woosales_deviceid');

if (!isset($deviceid_check) || $deviceid_check == NULL || $deviceid_check == '') {
	update_option( 'woosales_deviceid', $deviceid_get );
	$deviceid = get_option('woosales_deviceid');
} 


function Dashboard() {
	global $wpdb;
	$today0 = date('Y-m-d');
	$today1 = date('Y-m-d',time()-60*60*24);
	$shopCurrency = get_woocommerce_currency_symbol();
	// GET CUSTOMERS FOR TODAY+
	for ($i = 0; $i <= 1; $i++) {
		${"totalCustomersDay".$i} = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}users as users WHERE DATE(users.user_registered) = '".${"today".$i}."' " );
	}
	// GET STATS FOR TODAY+				
	$sql = "";
	for ($i = 0; $i <= 1; $i++) {
		$sql .= "SELECT SUM(postmeta.meta_value) AS 'OrderTotal', COUNT(*) AS 'OrderCount', '".${"today".$i}."' AS 'Text'
				FROM {$wpdb->prefix}postmeta as postmeta 
				LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id
				WHERE meta_key='_order_total' AND post_type = 'shop_order' AND DATE(posts.post_date) = '".${"today".$i}."' ";
		if ($i < 1) {$sql .= " UNION ";} } $reportTodayResult = $wpdb->get_results($sql);	
	// GET ITEMS FOR TODAY+				
	$sql = '';
	for ($i = 0; $i <= 1; $i++) {
		$sql .= "SELECT COUNT(orderitems.order_item_type) AS 'ItemsTotal', '".${"today".$i}."' AS 'Text'
						FROM {$wpdb->prefix}woocommerce_order_items as orderitems 
						LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=orderitems.order_id
						LEFT JOIN  {$wpdb->prefix}posts as posts ON postmeta.post_id=posts.ID
						WHERE postmeta.meta_key = '_completed_date' AND DATE(postmeta.meta_value) = '".${"today".$i}."' ";
		if ($i < 1) {$sql .= " UNION ";} } $reportTodayItems = $wpdb->get_results($sql);	
	// PREPARE TODAY+
	$result='';
	for ($i = 0; $i <= 1; $i++) {
		$result[] = ${"reportToday".$i} = array('total' => (float)$reportTodayResult[$i]->OrderTotal,'orders' => intval($reportTodayResult[$i]->OrderCount),'items' => intval($reportTodayItems[$i]->ItemsTotal),'customers' => intval(${"totalCustomersDay".$i}));
	}
	$result[] = array('currency' => $shopCurrency);
	echo json_encode($result);

}


if (isset($_GET["api"]) && $_GET["api"] == $woosalesapi_check) {
	// Go Dashboard
	if ((isset($_GET["action"]) && $_GET["action"] == 'dashboard' )) {
		Dashboard();
	} elseif ((isset($_GET["action"]) && $_GET["action"] == 'orders' )) {
		$shopCurrency = get_woocommerce_currency_symbol();
		$sql = "SELECT postmeta.post_id AS 'id', postmeta.meta_value as 'amount', posts.post_date as 'date'
				FROM {$wpdb->prefix}postmeta as postmeta 
				LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id
				WHERE meta_key='_order_total' AND post_type = 'shop_order' ORDER BY posts.post_date DESC  ";
		$allOrders = $wpdb->get_results($sql);
		foreach ($allOrders as $item) {
			$result[]= array('id'=>$item->id, 'amount'=>html_entity_decode($shopCurrency).$item->amount, 'date'=>$item->date);
		}
		//print_r($result);
		echo json_encode($result);
	} elseif ((isset($_GET["action"]) && $_GET["action"] == 'products' )) {
		$result = array('access' => 'get the products');
		echo json_encode($result);
	} elseif ((isset($_GET["action"]) && $_GET["action"] == 'customers' )) {
		$result = array('access' => 'get the customers');
		echo json_encode($result);
	} else {
		Dashboard();
	}
} else {
	$result = array('access' => 'no');
	echo json_encode($result);
}
?>