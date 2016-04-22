<?php
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');
//$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
//require_once( $parse_uri[0] . 'wp-load.php' );
require_once( '../../../wp-load.php' );
include('admin/woosales-functions.php');
//include (plugin_dir_path( __FILE__ ).'/admin/woosales-functions.php');
$woosalesapi_check = get_option('woosales_api');

$deviceid_get = $_GET['deviceid'];
$asset = $_GET['asset'];
$deviceid_check = get_option('woosales_deviceid');
if (!isset($deviceid_check) || $deviceid_check == NULL || $deviceid_check == '') {
	update_option( 'woosales_deviceid', $deviceid_get );
	$deviceid = get_option('woosales_deviceid');
} 

$status = get_option( 'edd_wsm_license_status' );
if (isset($_GET["api"]) && $_GET["api"] == $woosalesapi_check && $status !== false && $status == 'valid') {
	if ((isset($_GET["action"]) && $_GET["action"] == 'general' )) {
		
		
		/*
		// Your license key
		$license = "put your license key in here";
		$server = $_SERVER["SERVER_NAME"];
		
		$c = curl_init();
		// Set the full url path to point to your verifyLicense.php on your server
		curl_setopt($c, CURLOPT_URL, "http://www.mywebsite.com/someFolder/verifyLicense.php");
		curl_setopt($c, CURLOPT_TIMEOUT, 30);
		curl_setopt($c, CURLOPT_POST, 1);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		$postfields = 'svr='.$server.'&lic='.$license;
		curl_setopt($c, CURLOPT_POSTFIELDS, $postfields);
		$result = curl_exec($c);
		
		if ($result=="fail") {
			// You could do some nasty stuff...delete files...rewrite webpages...etc
			echo '<script type="text/javascript">alert("This website is using un-authorized software. You have been reported to the authorities");</script>';
		}
		

		$fields = array('asset'  => $asset);
		//$headers = array('Authorization: app=WooSalesMobile', 'Content-Type: application/json');
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'http://oceanicbreeze.com/woosalesmobile/check/' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS,  $fields );
		$result = curl_exec($ch );
		curl_close( $ch );
		*/		
			echo json_encode($resultok);
	} else {
		$result = array('access' => 'no');
		echo json_encode($result);
	}
} else {
	$result = array('access' => 'no');
	echo json_encode($result);

}
?>