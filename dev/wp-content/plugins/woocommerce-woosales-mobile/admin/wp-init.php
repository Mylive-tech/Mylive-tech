<?php
// Admin menu + page
add_action('admin_menu', 'woosales_menu');
function woosales_menu() {
    add_submenu_page( 'woocommerce', 'WooSales Mobile', 'WooSales Mobile', 'manage_options', 'woosales', 'woosales_page_callback' ); 
}
function edd_wsm_register_option() {
	// creates our settings in the options table
	register_setting('edd_wsm_license', 'edd_wsm_license_key', 'edd_sanitize_license' );
}
add_action('admin_init', 'edd_wsm_register_option');

function edd_sanitize_license( $new ) {
	$old = get_option( 'edd_wsm_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'edd_wsm_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}
function edd_wsm_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_wsm_nonce', 'edd_wsm_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_wsm_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_WSM_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_WSM_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'edd_wsm_license_status', $license_data->license );

	}
}
add_action('admin_init', 'edd_wsm_activate_license');
function edd_wsm_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_wsm_nonce', 'edd_wsm_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_wsm_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_WSM_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_WSM_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'edd_wsm_license_status' );

	}
}
add_action('admin_init', 'edd_wsm_deactivate_license');
function edd_wsm_check_license() {

	global $wp_version;

	$license = trim( get_option( 'edd_wsm_license_key' ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( EDD_WSM_ITEM_NAME ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( EDD_WSM_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}


function woosales_page_callback() {
    if ( !current_user_can( 'manage_options' ) )  {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}
    wp_register_style( 'WPReferralStylesheet', plugins_url('/woosales.css', __FILE__) );	
    wp_enqueue_style( 'WPReferralStylesheet' );
$woosalesssl_opt_val = get_option( 'woosales_ssl' );
    include('woosales-functions.php');

	$check_submit = 'check_submit_hidden';

	$woosalesapi_opt_name = 'woosales_api';
    $woosalesapi_opt_val = get_option( $woosalesapi_opt_name );
    

    if( isset($_POST[ $check_submit ]) && $_POST[ $check_submit ] == 'Y' ) {
        $woosalesapi_opt_val = $_POST[ $woosalesapi_opt_name ];
        update_option( $woosalesapi_opt_name, $woosalesapi_opt_val );
        
?>
<div class="updated"><p><strong>Settings saved.</strong></p></div>
<?php
 } 

//require('woosales-functions.php');

$woosalesapi_check = get_option('woosales_api');
if (!isset($woosalesapi_check) || $woosalesapi_check == '') {
	    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < 8; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
    $woosalesapi_opt_val = $randstring;
    update_option( $woosalesapi_opt_name, $woosalesapi_opt_val );
}

?>
<div class="woosalescontent">
	
	<?php 	$license 	= get_option( 'edd_wsm_license_key' );
	$status 	= get_option( 'edd_wsm_license_status' );
	?>
<h3>WooSales Mobile License</h3>
		<form method="post" action="options.php">

			<?php settings_fields('edd_wsm_license'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input id="edd_wsm_license_key" name="edd_wsm_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="edd_wsm_license_key"><?php _e('Enter your license key'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php if( $status !== false && $status == 'valid' ) {  _e('License is Active'); } else { _e('License is Inactive'); } ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<?php wp_nonce_field( 'edd_wsm_nonce', 'edd_wsm_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_wsm_nonce', 'edd_wsm_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>

		</form>

	
	<?php $status 	= get_option( 'edd_wsm_license_status' ); if ($status !== false && $status == 'valid') { ?>

<h3>WooSales Mobile Settings</h3>
<form name="form1" method="post" action="">
<p>With WooSaless Mobile you have access to all your WooCommerce statistics and reports directly on your mobile phone. We support any iOS device (iPhone and iPad) and any Android device (smartphone and tablet).</p>

<p>In order to get the stats on your mobile device please download WooSales Mobile from <a href="https://itunes.apple.com/us/app/woosales/id907187702" target="_blank">iTunes Store</a> or <a href="https://play.google.com/store/apps/details?id=com.markessence.woocommerce.woosales" target="_blank">Google Play</a>.</p>

<p>Your WooSaless Mobile API Key: <input type="text" name="<?php echo $woosalesapi_opt_name; ?>" value="<?php echo $woosalesapi_opt_val; ?>" size="40" placeholder="The API Key will auto generate"><br /><em>Your API key should be 8 characters long. You can use the generated key or you can set your own. To generate another key, just delete current one and hit Save.</em></p>
<p class="submit">
	<input type="hidden" name="check_submit_hidden" id="check_submit_hidden" value="Y" />
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>

<div class="tablewrapper" style="padding-right: 20px;">
<p>Currently you have <?php echo $resultok['usersTotal']; ?> users (including yourself) and <?php echo $resultok['usersActiveCustomers']; ?> of them are active customers who generated <?php echo $resultok['totalCountAllOrders']; ?> orders. See details below.</p>
			 <table class="widefat">
				<thead>
					<tr class="first">
						<th>Daily Sales</th>
						<th style="text-align: center;">Order Count</th>
						<th style="text-align: right;" class="amount">Amount</th>
						<th style="text-align: right;" class="amount">Average per order</th>
					</tr>
				</thead>
				<tbody>
					<?php					
						foreach ( $reportTodayResult as $key => $order_item ) {
							if ($order_item->Text!='') {
						if($key%2 == 1){$alternate = "alternate ";}else{$alternate = "";};
					?>
						<tr class="<?php echo $alternate."row_".$key;?>">
							<td><?php echo $order_item->Text?></td>
							<td align="center"><?php echo $order_item->OrderCount?></td>
							<td style="text-align: right;" class="amount"><?php echo woocommerce_price($order_item->OrderTotal);?></td>
							<td style="text-align: right;" class="amount"><?php echo ($order_item->OrderTotal == 0 ? woocommerce_price(floatval(0)) : woocommerce_price(floatval($order_item->OrderTotal)/intval($order_item->OrderCount)) ) ?></td>
						</tr>
					 <?php } }?>	
				<tbody>           
			</table>	
			<br /><hr /><br />
			 <table class="widefat">
				<thead>
					<tr class="first">
						<th>Monthly Sales</th>
						<th style="text-align: center;">Order Count</th>
						<th style="text-align: right;" class="amount">Amount</th>
						<th style="text-align: right;" class="amount">Average per order</th>
					</tr>
				</thead>
				<tbody>
					<?php					
						foreach ( $reportMonthResult as $key => $order_item ) {
							if ($order_item->Text!='') {
						if($key%2 == 1){$alternate = "alternate ";}else{$alternate = "";};
					?>
						<tr class="<?php echo $alternate."row_".$key;?>">
							<td><?php echo $order_item->Text?></td>
							<td align="center"><?php echo $order_item->OrderCount?></td>
							<td style="text-align: right;" class="amount"><?php echo woocommerce_price($order_item->OrderTotal);?></td>
							<td style="text-align: right;" class="amount"><?php echo ($order_item->OrderTotal == 0 ? woocommerce_price(floatval(0)) : woocommerce_price(floatval($order_item->OrderTotal)/intval($order_item->OrderCount)) ) ?></td>
						</tr>
					 <?php }} ?>	
				<tbody>           
			</table>		
			<br /><hr /><br />
			 <table class="widefat">
				<thead>
					<tr class="first">
						<th>Yearly Sales</th>
						<th style="text-align: center;">Order Count</th>
						<th style="text-align: right;" class="amount">Amount</th>
						<th style="text-align: right;" class="amount">Average per order</th>
					</tr>
				</thead>
				<tbody>
					<?php					
						foreach ( $reportYearResult as $key => $order_item ) {
							if ($order_item->Text!='') {
						if($key%2 == 1){$alternate = "alternate ";}else{$alternate = "";};
						
					?>
						<tr class="<?php echo $alternate."row_".$key;?>">
							<td><?php echo $order_item->Text?></td>
							<td align="center"><?php echo $order_item->OrderCount?></td>
							<td style="text-align: right;" class="amount"><?php echo woocommerce_price($order_item->OrderTotal);?></td>
							<td style="text-align: right;" class="amount"><?php echo ($order_item->OrderTotal == 0 ? woocommerce_price(floatval(0)) : woocommerce_price(floatval($order_item->OrderTotal)/intval($order_item->OrderCount)) ) ?></td>
						</tr>
					 <?php }} ?>	
				<tbody>           
			</table>		
</div>
<?php } ?>


</div><!-- /woosalescontent -->
<?php
}













?>