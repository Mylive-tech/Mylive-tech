<?php
/**
 * Plugin Name: WooCommerce Advanced Review Reminder
 * Plugin URI: http://kodemann.com
 * Description: Sends out customized emails asking your customers for leaving a review of their purchase. Increase conversions with social proof - Get reviews from your customers.
 * Author: kodemann.com
 * Author URI: http://kodemann.com
 * Version: 1.6.4
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

/*  
	Copyright Kodemann.com
*/

/*
Changelog

= 1.6.4 =
* FIX: Compability with WooCommerce Sequential Order Numbers.
* Updated Danish translations.


= 1.6.3 =
* Better WPML integration.
* New HTML buttons for better compability.

= 1.6.2 =
* NEW: Customize colors for Review Now buttons in settings page.
* NEW: Customize button text for the Review Now button.
* Updated some Spanish and Danish translations.

= 1.6.1 =
* Fixing the button color in emails.

= 1.6 =
* Default text now uses {order_table}

= 1.5.5 =
* Missing {order_table} dummy data in test email - thanks emielm.
* Better styling on review button in emails - thanks emielm.

= 1.5.4 =
* NEW: {order_table} shortcode to show images and big button of purchased products.

= 1.5.3 =
* Fix for using mb_encode_mimeheader() on PHP installations without mb_ extension installed. 
(http://php.net/manual/en/function.mb-encode-mimeheader.php - Install instructions here: http://php.net/manual/en/mbstring.installation.php)

= 1.5.2 =
* Minor PHP Notice fix for using esc_sql() instead of mysql_real_escape_string() 

= 1.5.1 =
* Fix: When setting 'Day(s) after order' to empty '', no emails will be sent, only by clicking the manual button on the order page.

= 1.5 =
* Unsubscribe confirmation - Users now get a confirmation email they have unsubscribed from further emails.

= 1.4.3 =
* Minor fix for PHP undefined index, 'send_reminder_now'

= 1.4.2 =
* Fix for UTF8 encoding problem in database.


= 1.4.1 =
* Fix: Bug in the scheduling and immediate order sending fixed.

= 1.4 =
* New: See email sending log notes directly on order page in admin
* New: Send review request immediately via button on order page in admin  

= 1.3.1 = 
* Fix for UTF-8 characters in subject line.
* Minor fix for a PHP warning.

= 1.3 =
* Added {customer_firstname} and {customer_lastname} macros by customer request.

= 1.2.1 =
* Fix: Compability with WooCommerce Custom Order Statuses & Actions plugin.
* Code cleanup/refactoring.
* Danish translation updated.
* Updated documentation, added instructional video and added link to documentation from settings page.

= 1.2 = 
* New: Introducing logging, so you can see what is going on.

= 1.1.1 = 
* Fix: Save THEN send test email. No need to first save and then afterwards send test email.
* Fix: Changed link in documentation to new CodeCanyon link: http://codecanyon.net/user/kodemann

= 1.1 =
* You can now send a test email out from the settings page.

= 1.0.1 = 
* Removed buggy update script.

= 1.0 =
* First release.


 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class advanced_review_rem {

	public static function init() {
		$class = __CLASS__ ;
		if ( empty( $GLOBALS[ $class ] ) )
			$GLOBALS[ $class ] = new $class;
	}

	public function __construct() {
		add_action( 'init', array(&$this,"on_init"));
		add_action( 'arr_send_email', array(&$this,"send_email_reminder"),1,3);
		add_action( 'woocommerce_order_status_completed', array(&$this,'action_woocommerce_order_status_completed'));
		add_action( 'woocommerce_order_status_processing', array(&$this,'woocommerce_order_status_no_longer_completed'));
		add_action( 'woocommerce_order_status_failed', array(&$this,'woocommerce_order_status_no_longer_completed'));
		add_action( 'woocommerce_order_status_pending', array(&$this,'woocommerce_order_status_no_longer_completed'));
		add_action( 'woocommerce_order_status_refunded', array(&$this,'woocommerce_order_status_no_longer_completed'));
		add_action( 'woocommerce_order_status_cancelled', array(&$this,'woocommerce_order_status_no_longer_completed'));
		add_action( 'woocommerce_order_status_on-hold', array(&$this,'woocommerce_order_status_no_longer_completed'));

		add_action( 'add_meta_boxes', array(&$this,'add_review_request_metabox') );
			add_action('save_post', array(&$this,'post_save_send_request_immediately'), 1, 2); // 

	} // __construct()


	/**
	 * Starts a microtimer and store in transient table
	 * @author larsk
	 * @param  string $watchname Name of the timer, used with timerstop()
	 * @since  1.6.4
	 * @return void
	 */
	public static function timerstart($watchname) {
		set_transient('_wcarg_'.$watchname,microtime(true),60*1); // set the transient to be deleted soon.
	}


	/**
	 * Stops a timer, returns the time and deletes from transient table
	 * @author larsk
	 * @param  [type]  $watchname Name of microtimer
	 * @param  integer $digits    Set number of digits after .
	 * @since  1.6.4
	 * @return float              Rounded number
	 */
	public static function timerstop($watchname,$digits=4) {
		$return= round(microtime(true)-get_transient('_wcarg_'.$watchname),$digits);
		delete_transient('_wcarg_'.$watchname);
		return $return;
		
	}



	/**
	 * Add review reminder meta box
	 * @author larsk
	 * @since  1.4
	 * @return null
	 */
	function add_review_request_metabox() {
		add_meta_box('wcarg_send_request_metabox', __('Email Review Request','wc-review-reminder'), array(&$this,'wcarg_send_request_metabox'), 'shop_order', 'side', 'low');
	}


	/**
	 * Output meta box on edit order in admin
	 * @author larsk
	 * @since  1.4
	 * @return null
	 */
	function wcarg_send_request_metabox() {
		global $post;
		echo '<p>'.__('Send a request to review this order immediately. Any scheduled emails will still be sent.','wc-review-reminder').'</p>';

		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="wcarr_send_immediately" id="wcarr_send_immediately" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
		?>
		<input type="submit" class="button send_reminder button-primary" name="send_reminder_now" value="<?php _e('Send reminder now','wc-review-reminder'); ?>">
		<?php
	}


	/**
	 * Process on save post (order) - send email requests immediately
	 * @author larsk
	 * @since  1.4
	 * @return null
	 */
	function post_save_send_request_immediately($post_id, $post) {
		if (!isset($_POST['send_reminder_now'])) { // we are not going to do anything, since the send button has not been clicked.
			return $post_id;
		}
		if ( !wp_verify_nonce( $_POST['wcarr_send_immediately'], plugin_basename(__FILE__) )) {
			return $post->ID;
		}
		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;
		// Look up billing email
		if ($_POST['send_reminder_now']) {
			$_billing_email = get_post_meta($post->ID,'_billing_email',true );
			$this->send_email_reminder($post->ID,0,$_billing_email);
		}
	}



	/**
	 * Runs on several woocommerce do_action() to remove any orders
	 * @author larsk
	 * @param  int $order_id The unique order id
	 * @return null
	 */
	function woocommerce_order_status_no_longer_completed( $order_id ) {
		$crons = _get_cron_array();
		$hook='arr_send_email';
		foreach( $crons as $timestamp => $cron ) {
			if (isset($cron[$hook])) {
				if ( is_array( $cron[$hook] ) )  {
					foreach ($cron[$hook] as $details) {
					$target=$details['args'][0]; // order id from paramaters
					if ($target==$order_id) { // unset this scheduled event
						unset( $crons[$timestamp][$hook] );
					}
				}
			}
			
		}
	}
	$this->log(sprintf( __( 'Order %s has changed status. Not completed, no e-mails scheduled.', 'wc-review-reminder'), $order_id ));
	_set_cron_array( $crons );
}


	/**
	 * Runs when an order is marked as complete
	 * @author larsk
	 * @param  int $order_id The id of the order
	 * @return null
	 */
	function action_woocommerce_order_status_completed( $order_id ) {
		$advanced_review_settings = get_option( 'woocommerce_wc_advanced_review_settings');
		// Check if plugin is enabled
		if ($advanced_review_settings['enabled']<>'yes') {
			return;
		}
		$reminderdays=explode(',',$advanced_review_settings['interval']); 
		if ($reminderdays) {
			$scheduleddays='';
			if ((is_array($reminderdays)) && ($advanced_review_settings['interval']<>'') ) {
				foreach ($reminderdays as $rd) {
					$_billing_email = get_post_meta($order_id,'_billing_email',true ); // for {customer_email}
					$args =  array ( $order_id, $rd , $_billing_email);
					$futuretime = time()+($rd*86400);
					$scheduleresult = wp_schedule_single_event($futuretime, 'arr_send_email', $args);
					$scheduleddays .= date_i18n( get_option( 'date_format' ), $futuretime ).' ';
					//$this->log("scheduled $futuretime $order_id $rd $_billing_email - $scheduleresult");
				}
			}
			if ($scheduleddays) {
				$this->log(sprintf( __( 'Order # %s marked as completed. Scheduled emails to be sent to %s on following dates: %s', 'wc-review-reminder'), $order_id, $_billing_email, $scheduleddays ));
			}
		}
	}



	/** 
	 * Runs on 'init' action - checks for &okey= to add customer to blocklist
	 * @author larsk
	 */
	function on_init() {  
		load_plugin_textdomain('wc-review-reminder', false, dirname(plugin_basename(__FILE__)) . '/languages');
		if (is_admin()) return;
		
		if (isset($_GET['okey'])) {
			if (!is_email($_GET['okey']) ) {
				$okey=sanitize_key($_GET['okey']);
			}
			else {
				$okey=$_GET['okey'];
			}
		}
		// Look for the parsing of "?okey=". If parsed, look up billing email from unique order key and add to blocklist.
		if (isset($okey)) {
			$this->timerstart('processing_blacklisting');
			global $wpdb,$woocommerce;
			$post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '%s' AND meta_key='_order_key';",$okey));
			if ((isset($post_id)) OR (is_email($okey))) {
				$customer_email = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '%d' AND meta_key='_billing_email';",$post_id));
				if (($customer_email) or (is_email($okey))) {
					$advanced_review_settings = get_option( 'woocommerce_wc_advanced_review_settings');
					$blocklist = $advanced_review_settings['blocklist'];
					$blocklist_arr = explode(',',$blocklist);
					if (is_array($blocklist_arr)) {

						if ($customer_email) {
							$blocklist_arr[] = $customer_email; // add to the blocked list
							$blocklist_arr   = array_unique($blocklist_arr); // remove duplicates
							$advanced_review_settings['blocklist'] = implode(',', $blocklist_arr);
							update_option('woocommerce_wc_advanced_review_settings',$advanced_review_settings );
						}

						if (!$customer_email) {
							$customer_email = $okey; // set the customer email to the $okey (the test email) to test the unsubscribe email gets sent
						}

						$unsubscribetext = $advanced_review_settings['unsubscribetext'];

						$unsubscribesubjectline = $advanced_review_settings['unsubscribesubjectline'];

						$mailer = $woocommerce->mailer();
						ob_start();
						do_action( 'woocommerce_email_header', $unsubscribesubjectline);
						echo nl2br($unsubscribetext); 
						do_action('woocommerce_email_footer'); 
						$email_content = ob_get_clean();
						$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";

						if ( function_exists('mb_encode_mimeheader') ) {
							$unsubscribesubjectline = mb_encode_mimeheader($unsubscribesubjectline);
						} 
						else {
							// the mb_ extension is not installed in PHP, so no conversion...
						}

						$mailer->send( $customer_email, $unsubscribesubjectline, $email_content,$headers);

						$processing_blacklisting = $this->timerstop('processing_blacklisting',2);

						$this->log(sprintf( __( '%s was added to the blocklist and an email confirmation was sent. Process took %s sec','wc-review-reminder'), $customer_email, $processing_blacklisting ));

					}
				}
			}
		}
		
	} // on_init()


	/**
	 * Logs 
	 * @author larsk
	 * @param  string  $text Text to be logged.
	 * @param  integer $prio Prioritization of log. Can set different style via CSS.
	 * @return null
	 */
	function log($text,$prio=0) {
		global $wpdb;
		$woocommerce_arr_log = $wpdb->prefix . "woocommerce_arr_log";   
		$time= current_time("mysql");
		$text= esc_sql($text); 
		$daquery="INSERT INTO `$woocommerce_arr_log` (time,prio,note) VALUES ('$time','$prio','$text');"; //todo - set up with $wpdb->prepare()
		$result=$wpdb->query($daquery);
		$total = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$woocommerce_arr_log`;");        
		if ($total>1000) {
			$targettime = $wpdb->get_var("SELECT `time` from `$woocommerce_arr_log` order by `time` DESC limit 500,1;");
			$query="DELETE from `$woocommerce_arr_log`  where `time` < '$targettime';";
			$success= $wpdb->query ($query);
		}
	}


	/**
	 * Sends email reminder
	 * @author larsk
	 * @param  int    $order_id  Order ID to process - Use 0 as value to send dummy email and data
	 * @param  int    $days      Number of days remidner
	 * @param  string $email     Customers email
	 * @return null
	 */
	function send_email_reminder($order_id,$days,$email) {
		global $wpdb,$woocommerce;
		$advanced_review_settings = get_option( 'woocommerce_wc_advanced_review_settings');
		
		$this->timerstart('email_reminder_timer');

		// Check if plugin is enabled
		if (($advanced_review_settings['enabled']<>'yes') AND ($order_id<>0) ) { // skip if set to send test email
			wp_clear_scheduled_hook( 'arr_send_email', array($order_id, $days, $email) ); // Remove the current cron if not enabled.
			return;
		}

		$button_bg_color 		= $advanced_review_settings['buttonbg'];
		$buttoncolor 			= $advanced_review_settings['buttoncolor'];
		$buttontext 			= $advanced_review_settings['buttontext'];

	//	$buttontext 			= icl_translate('WC Advanced Review Reminder', 'Please Review Button text',$advanced_review_settings['buttontext']);
// icl_translate ( string $context, string $name, string $value );
/*
		if ( (isset($_POST['woocommerce_wc_advanced_review_subject'])) && (function_exists('icl_register_string')) ) {
			icl_register_string('WC Advanced Review Reminder', 'Email subject', $_POST['woocommerce_wc_advanced_review_subject']);
		}

		if ( (isset($_POST['woocommerce_wc_advanced_review_email'])) && (function_exists('icl_register_string')) ) {
			icl_register_string('WC Advanced Review Reminder', 'Email content', $_POST['woocommerce_wc_advanced_review_email']);
		}

		if ( (isset($_POST['woocommerce_wc_advanced_review_unsubscribetext'])) && (function_exists('icl_register_string')) ) {
			icl_register_string('WC Advanced Review Reminder', 'Unsubscribe text', $_POST['woocommerce_wc_advanced_review_unsubscribetext']);
		}
		if ( (isset($_POST['woocommerce_wc_advanced_review_unsubscribesubjectline'])) && (function_exists('icl_register_string')) ) {
			icl_register_string('WC Advanced Review Reminder', 'Unsubscribe confirmation email subject', $_POST['woocommerce_wc_advanced_review_unsubscribesubjectline']);
		}
		if ( (isset($_POST['woocommerce_wc_advanced_review_stoptext'])) && (function_exists('icl_register_string')) ) {
			icl_register_string('WC Advanced Review Reminder', 'Unsubscribe from review emails anchor', $_POST['woocommerce_wc_advanced_review_stoptext']);
		}

		if ( (isset($_POST['woocommerce_wc_advanced_review_buttontext'])) && (function_exists('icl_register_string')) ) {
			icl_register_string('WC Advanced Review Reminder', 'Please Review Button text', $_POST['woocommerce_wc_advanced_review_buttontext']);
		}

 */

		if ( function_exists('icl_translate') ) {
			$buttontext =  icl_translate('WC Advanced Review Reminder', 'Please Review Now text', $advanced_review_settings['buttontext']);		
		}



		$order                  = new WC_Order($order_id);
		$order_date             = $order->order_date; // for {order_date}
		$_completed_date        = get_post_meta($order_id,'_completed_date',true );  // for {order_date_completed}
		$_billing_email         = get_post_meta($order_id,'_billing_email',true ); // for {customer_email}
		$_order_key             = get_post_meta($order_id,'_order_key',true );  // unique order key - used for blacklisting emails link
		$blacklist_link         = site_url().'?okey='.$_order_key; // for {blacklist_link}
		$stoplink               = '<a href="'.site_url().'?okey='.$_order_key.'">'.$advanced_review_settings['stoptext'].'</a>'; // for {stop_emails_link}
		$_shipping_last_name    = get_post_meta($order_id,'_shipping_last_name',true );  
		$_shipping_first_name   = get_post_meta($order_id,'_shipping_first_name',true );  
		$_order_list            = '';
		$items                  = $order->get_items();
		$items_for_review       = 0; // counter for items in review list.
		
		$product_table = '<table>';
		if ($items) {
			$_order_list .= '<ul>';
			foreach ($items as $item) {
				$order->get_product_from_item($item);
				$product_id = $item['product_id'];
				$matched_to_review = false; 
				$args = array ('post_id' => $product_id); 
				$comments = get_comments( $args );
				if ($comments) {
					foreach ($comments as $comment) {
						if ($comment->comment_author_email == $_billing_email) {
							$matched_to_review=true; // a match. the customer being email has already made a review!
						}
					}
				}
				if ($matched_to_review) {
					// We can do stuff here. If we want.
				}
				else {
					$_order_list .= "<li><a href='".get_permalink($product_id)."'>".$item['name']."</a></li>";
					$items_for_review++;
				}


			// generate html table for products


				$_product = $item['product_id'];

				$product = new WC_Product( $item['product_id'] );

			//	$_style_button = 'margin-top:5px;margin-bottom:15px;background-color:'.$button_bg_color.' !important;border-radius:3px;color:'.$buttoncolor.';display:inline-block;font-family:sans-serif;font-size:13px;line-height:38px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;';


				$button = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><table border="0" cellspacing="0" cellpadding="0"><tr><td><a href="'.get_permalink($item['product_id']).'" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: '.$buttoncolor.'; text-decoration: none; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; background-color: '.$button_bg_color.'; border-top: 12px solid '.$button_bg_color.'; border-bottom: 12px solid '.$button_bg_color.'; border-right: 18px solid '.$button_bg_color.'; border-left: 18px solid '.$button_bg_color.'; display: inline-block;">'.$buttontext.'</a></td></tr></table></td></tr></table>';

				$_style_tdstyle   = 'text-align:left; vertical-align:top; word-wrap:break-word;';

				$src = wp_get_attachment_image_src( get_post_thumbnail_id( $item['product_id'] ), 'thumbnail');

				$image='';

				$image              = get_the_post_thumbnail( $product->ID, apply_filters( 'single_product_large_thumbnail_size', array(150,150) ) );
				$image_title        = esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_link         = wp_get_attachment_url( get_post_thumbnail_id() );

				if (is_array($src)) {
					$imagehtml = apply_filters('woocommerce_order_product_image', '<img src="'.$src[0].'" alt="'.$product->post->post_title.'" height="150" width="150" style="vertical-align:middle; margin-right: 10px;" />', $_product);

				}
				else {
					$imagehtml = apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="'.$product->post->post_title.'" height="150" width="150" style="vertical-align:middle; margin-right: 10px;" />', woocommerce_placeholder_img_src() ), $product->ID );
				}

				$product_table .= '<tr>';

				$product_table .= '<td style="'.$_style_tdstyle.'">'.$imagehtml.'</td><td style="'.$_style_tdstyle.'"><h3>'.$product->post->post_title.'</h3>';
				$product_table .= wp_trim_words($product->post->post_excerpt,25);
				$product_table .= '<p>'.$product->get_price_html().'</p>';
			//	$product_table .= '<a href="'.get_permalink($item['product_id']).'" style="'.$_style_button.'">'.$buttontext.'</a></td>';
				$product_table .= $button;
				$product_table .= '</td></tr>';

			} // foreach ($items as $item)

			$_order_list .= '</ul>';

		}

		$product_table .= '</table><!-- end product_table -->';

		$_completed_date_time = strtotime($_completed_date); // completed date in UNIX format
		$now=current_time("mysql"); // this gets the proper local time to compare with
		$_now_time = strtotime($now);

		$replace_list = array();
		$replace_list['{customer_name}']            = $_shipping_first_name.' '.$_shipping_last_name;
		$replace_list['{customer_firstname}']       = $_shipping_first_name;
		$replace_list['{customer_lastname}']        = $_shipping_last_name;
		$replace_list['{order_id}']                 = $order_id;

		$saved_order_id = get_post_meta($order_id,'_order_number',true );
		if ($saved_order_id<>$order_id) {
			$replace_list['{order_id}']                 = $saved_order_id;

		}

		$replace_list['{customer_email}']           = $email; 
		$replace_list['{order_date}']               = $order_date;
		$replace_list['{order_date_completed}']     = $_completed_date;
		$replace_list['{stop_emails_link}']         = $stoplink;
		$replace_list['{blacklist_link}']           = $blacklist_link;
		$replace_list['{order_list}']               = $_order_list;
		$replace_list['{order_table}']              = $product_table;
		$replace_list['{days_ago}']                 = $days; // from parsed paramater
		$replace_list['{site_title}']               = get_bloginfo('name'); // from parsed paramater

		if ($order_id==0) { // use dummy data for test email

			$button = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><table border="0" cellspacing="0" cellpadding="0"><tr><td><a href="'.site_url('/').'" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: '.$buttoncolor.'; text-decoration: none; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; background-color: '.$button_bg_color.'; border-top: 12px solid '.$button_bg_color.'; border-bottom: 12px solid '.$button_bg_color.'; border-right: 18px solid '.$button_bg_color.'; border-left: 18px solid '.$button_bg_color.'; display: inline-block;">'.$buttontext.'</a></td></tr></table></td></tr></table>';

			$_style_tdstyle   = 'text-align:left; vertical-align:top; word-wrap:break-word;';

			$product_table = '<table><tbody><tr><td style="'.$_style_tdstyle.'"><img src="'.woocommerce_placeholder_img_src().'" alt="Alt text" height="150" width="150" style="vertical-align:middle; margin-right: 10px;"></td><td style="text-align:left; vertical-align:top; word-wrap:break-word;"><h3>Example Product #1</h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus mi erat, ultrices ut erat eget, fermentum malesuada massa. Praesent tempor blandit massa, quis auctor augue. Ut id urna in nunc maximus tincidunt non sit amet velit. Maecenas gravida laoreet tempor.<p><del><span class="amount">$100</span></del> <ins><span class="amount">$85</span></ins></p>'.$button.'</td></tr><tr><td style="'.$_style_tdstyle.'"><img src="'.woocommerce_placeholder_img_src().'" alt="Alt text" height="150" width="150" style="vertical-align:middle; margin-right: 10px;"></td><td style="text-align:left; vertical-align:top; word-wrap:break-word;"><h3>Example Product #2</h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus mi erat, ultrices ut erat eget, fermentum malesuada massa. Praesent tempor blandit massa, quis auctor augue.<p><del><span class="amount">$100</span></del> <ins><span class="amount">$85</span></ins></p>'.$button.'</td></tr></tbody></table>';

			$replace_list['{customer_name}']            = 'John Doe';
			$replace_list['{customer_firstname}']       = 'John';
			$replace_list['{customer_lastname}']        = 'Doe';            
			$replace_list['{order_id}']                 = '1';
			$replace_list['{customer_email}']           = $email; 
			$replace_list['{order_date}']               = $now;
			$replace_list['{order_date_completed}']     = $now;
			$replace_list['{stop_emails_link}']         = '<a href="'.site_url().'">'.$advanced_review_settings['stoptext'].'</a>';
			$replace_list['{blacklist_link}']           = site_url().'?okey='.$email;
			$replace_list['{stop_emails_link}']         = '<a href="'.site_url().'?okey='.$email.'">'.$advanced_review_settings['stoptext'].'</a>';
//			$replace_list['{order_list}']               = '<ul><li><a href="'.site_url().'">'.__('Example Product Review Link #1','wc-review-reminder').'</a></li><li><a href="'.site_url().'">'.__('Example Product Review Link #2','wc-review-reminder').'</a></li></ul>';

			$replace_list['{order_list}']               = '<ul><li><a href="'.site_url().'">'.__('Example Product Review Link #1','wc-review-reminder').'</a></li><li><a href="'.site_url().'">'.__('Example Product Review Link #2','wc-review-reminder').'</a></li></ul>';


			$replace_list['{order_table}']              = $product_table;
			$items_for_review=1; // to trigger generation   
		}


		$message = stripslashes($advanced_review_settings['email']);
		$subject_line = $advanced_review_settings['subject'];


		// integrating in to WPML translations
		if ( function_exists('icl_translate') ) {
			$subject_line = icl_translate('WC Advanced Review Reminder', 'Email subject', $subject_line);
			$message = icl_translate('WC Advanced Review Reminder', 'Email content', $message);
			$replace_list['{stop_emails_link}'] = '<a href="'.site_url().'">'.icl_translate('WC Advanced Review Reminder', 'Unsubscribe from review emails anchor', $advanced_review_settings['stoptext']).'</a>';
		}

		foreach ($replace_list as $searchfor => $replacewith) {
			$message = str_replace($searchfor, stripslashes($replacewith), $message);
			$subject_line = str_replace($searchfor, stripslashes($replacewith), $subject_line);
		}

		if ($items_for_review>0)  {
			$mailer = $woocommerce->mailer();
			ob_start();
			do_action( 'woocommerce_email_header', $subject_line);
			echo nl2br($message); 
			do_action('woocommerce_email_footer'); 
			$email_content = ob_get_clean();
			$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";

			if ( function_exists('mb_encode_mimeheader') ) {
				$subject_line = mb_encode_mimeheader($subject_line);
			} 
			else {
				// the mb_ extension is not installed in PHP, so no conversion...
			}

			$mailer->send( $email, $subject_line, $email_content,$headers);

			$email_reminder_timer = $this->timerstop('email_reminder_timer',2);

			if ($days<>0) {
				// Add log entry to own log system
				$this->log(sprintf( __( '(Order #%s) E-mail sent to %s - Day %s reminder. Took %s sec.', 'wc-review-reminder'), $order_id, $email, $days, $email_reminder_timer ));
				// Add note to the order
				$order->add_order_note( sprintf( __( 'Review reminder sent by Advanced Review Reminder - Day %s reminder. Took %s sec', 'wc-review-reminder' ), $days, $email_reminder_timer ) );                
			}
			else {
				$this->log(sprintf( __( '(Order #%s) E-mail sent to %s - Manual request. Took %s sec.', 'wc-review-reminder'), $order_id, $email, $days, $email_reminder_timer ));
				$order->add_order_note( sprintf( __( 'Manual request for review sent by Advanced Review Reminder. Took %s sec.', 'wc-review-reminder' ), $email_reminder_timer ) );                
			}
		}
		wp_clear_scheduled_hook( 'arr_send_email', array($order_id, $days, $email) );
	}
} // advanced_review_rem class
add_action( 'plugins_loaded', array( 'advanced_review_rem', 'init' ) );




register_activation_hook( __FILE__,  '_activate_routines' );

/**
 * Activation routines
 * @author larsk
 * @since    1.2
 */
function _activate_routines() {

	global $wpdb;
	require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

	$table_name = $wpdb->prefix . "woocommerce_arr_log";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
			prio tinyint(1) NOT NULL,
			note tinytext NOT NULL,
			PRIMARY KEY (id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
dbDelta($sql);
}   


}   



register_deactivation_hook( __FILE__, '_deactivate_routines' );
/**
 * Deactivation routines
 * @author larsk
 * @since  1.0
 * @return null
 */
function _deactivate_routines() {
	wp_clear_scheduled_hook('arr_send_email');

	// loop through all crons and remove any scheduled emails.
	$crons = _get_cron_array();
	$hook='arr_send_email';
	foreach( $crons as $timestamp => $cron ) {
		if ( (isset($cron[$hook])) AND (is_array($cron[$hook])) )  {
			$target=$details['args'][0]; // order id from paramaters
			unset( $crons[$timestamp][$hook] );
		}
	}
	_set_cron_array( $crons );
	global $wpdb;
	$table_name = $wpdb->prefix . "woocommerce_arr_log";
	$wpdb->query("DROP TABLE $table_name;");

} 

add_filter( 'woocommerce_email_classes',"woocommerce_review_gatherer_email");

/**
 * Includes the email class for WooCommerce - Moved outside main class to fix problem with other plugins overruling.
 * @author larsk
 * @param  array $email_classes WooCommerce incoming $email_classes
 */
function woocommerce_review_gatherer_email( $email_classes ) {
	require_once( 'includes/class-wc-advanced-review-reminder.php' );
	$email_classes['WC_Review_Reminder'] = new WC_Review_Reminder();
	return $email_classes;
}




