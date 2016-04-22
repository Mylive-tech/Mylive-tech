<?php
/**
* WoocommercePointOfSale Functions
*
* @author   Actuality Extensions
* @package  WoocommercePointOfSale/Admin/Functions
* @since    0.1
*/

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function pos_admin_page()
{
	global $post_type;
	if($post_type == 'product')
		return true;
	$pos_pages = array(
		'wc_pos_settings',
		'wc_pos_barcodes',
		'wc_pos_receipts',
		'wc_pos_users',
		'wc_pos_tiles',
		'wc_pos_grids',
		'wc_pos_outlets',
		'wc_pos_registers',
	);
	return isset($_GET['page']) && !empty($_GET['page']) && in_array($_GET['page'], $pos_pages) ;
}

function pos_tiles_admin_page()
{
	return isset($_GET['page']) && $_GET['page'] == 'wc_pos_tiles' ;
}

function pos_receipts_admin_page()
{
	return isset($_GET['page']) && $_GET['page'] == 'wc_pos_receipts' ;
}
function pos_settings_admin_page()
{
	return isset($_GET['page']) && $_GET['page'] == 'wc_pos_settings' ;
}

function pos_shop_order_page()
{
	return isset($_GET['post_type']) && $_GET['post_type'] == 'shop_order' ;
}
/**
 * Output a text input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wc_pos_text_input( $field ) {
	global $thepostid, $post, $woocommerce;

	$thepostid              = empty( $thepostid ) ? '' : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : (!empty( $thepostid ) ? get_post_meta( $thepostid, $field['id'], true ) : '' );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
	$data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];

	$field['wrapper_tag'] 		= isset( $field['wrapper_tag'] ) ? $field['wrapper_tag'] : 'div';
	$field['wrapper_label_tag'] 		= isset( $field['wrapper_label_tag'] ) ? $field['wrapper_label_tag'] : '%s';
	$field['wrapper_field_tag'] 		= isset( $field['wrapper_field_tag'] ) ? $field['wrapper_field_tag'] : '%s';

	switch ( $data_type ) {
		case 'price' :
			$field['class'] .= ' wc_input_price';
			$field['value']  = wc_format_localized_price( $field['value'] );
		break;
		case 'decimal' :
			$field['class'] .= ' wc_input_decimal';
			$field['value']  = wc_format_localized_decimal( $field['value'] );
		break;
	}

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) )
		foreach ( $field['custom_attributes'] as $attribute => $value )
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';

	$input = '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			$input .= '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			$input .= '<p class="description">' . $field['description'] . '<p>';
		}

	}

	$label = '<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';
	echo '<' . $field['wrapper_tag'] . ' class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">'. sprintf($field['wrapper_label_tag'], $label) . sprintf($field['wrapper_field_tag'], $input);


	echo '</' . $field['wrapper_tag'] . '>';
}


/**
 * Output a select input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wc_pos_select( $field ) {
	global $thepostid, $post, $woocommerce;

	$thepostid 				= empty( $thepostid ) ? '' : $thepostid;
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']           = isset( $field['value'] ) ? $field['value'] : (!empty( $thepostid ) ? get_post_meta( $thepostid, $field['id'], true ) : '' );
	$field['wrapper_tag'] 		= isset( $field['wrapper_tag'] ) ? $field['wrapper_tag'] : 'div';
	$field['wrapper_label_tag'] 		= isset( $field['wrapper_label_tag'] ) ? $field['wrapper_label_tag'] : '%s';
	$field['wrapper_field_tag'] 		= isset( $field['wrapper_field_tag'] ) ? $field['wrapper_field_tag'] : '%s';

	$select = '<select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '">';
	foreach ( $field['options'] as $key => $value ) {

		$select .= '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';

	}
	$select .= '</select> ';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			$select .= '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			$select .= '<p class="description">' .  $field['description']  . '<p>';
		}

	}

	$label = '<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	echo '<' . $field['wrapper_tag'] . ' class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">' . sprintf($field['wrapper_label_tag'], $label) . sprintf($field['wrapper_field_tag'], $select);



	echo '</' . $field['wrapper_tag'] . '>';
}

/**
 * Output a radio input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wc_pos_radio( $field ) {
	global $thepostid, $post, $woocommerce;

	$thepostid 				= empty( $thepostid ) ? '' : $thepostid;
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']           = isset( $field['value'] ) ? $field['value'] : (!empty( $thepostid ) ? get_post_meta( $thepostid, $field['id'], true ) : '' );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['wrapper_tag'] 		= isset( $field['wrapper_tag'] ) ? $field['wrapper_tag'] : 'div';
	$field['wrapper_label_tag'] 		= isset( $field['wrapper_label_tag'] ) ? $field['wrapper_label_tag'] : '%s';
	$field['wrapper_field_tag'] 		= isset( $field['wrapper_field_tag'] ) ? $field['wrapper_field_tag'] : '%s';

	$label = '<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';
	$inputs = '<ul class="wc-radios">';
	  foreach ( $field['options'] as $key => $value ) {

		$inputs .= '<li><label><input
			        		name="' . esc_attr( $field['name'] ) . '"
			        		value="' . esc_attr( $key ) . '"
			        		type="radio"
			        		class="' . esc_attr( $field['class'] ) . '"
			        		' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
			        		/> ' . esc_html( $value ) . '</label>
    						</li>';
		}
		$inputs .= '</ul>';
		if ( ! empty( $field['description'] ) ) {

			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				$inputs .= '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
			} else {
				$inputs .= '<p class="description">' . $field['description'] . '</p>';
			}

		}

	echo '<' . $field['wrapper_tag'] . ' class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">' . sprintf($field['wrapper_label_tag'], $label) .  sprintf($field['wrapper_field_tag'], $inputs);


    echo '</' . $field['wrapper_tag'] . '>';
}

function pos_set_register_lock( $register_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . "wc_poin_of_sale_registers";

	$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $register_id");
	
	if ( !$db_data)
		return false;

	if ( 0 == ($user_id = get_current_user_id()) )
		return false;

	$now = current_time( 'mysql' );
	
	$data['opened']     = $now;
	$data['_edit_last'] = $user_id;
	$rows_affected = $wpdb->update( $table_name, $data, array( 'ID' => $register_id ) );
	return array( $now, $user_id );
}

function pos_check_register_lock( $register_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . "wc_poin_of_sale_registers";

	$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $register_id");

	if ( !$db_data)
		return false;

	$row = $db_data[0];

	$user = $row->_edit_last;

	if ( strtotime($row->opened) >= strtotime($row->closed) && $user != get_current_user_id() ){
		return $user;
	}
	return false;
}
function pos_check_register_is_open( $register_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . "wc_poin_of_sale_registers";

	$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $register_id");

	if ( !$db_data)
		return false;

	$row = $db_data[0];

	if ($row->_edit_last > 0 && strtotime($row->opened) > strtotime($row->closed))
		return true;
	else
		return false;
}
function pos_check_user_can_open_register( $register_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . "wc_poin_of_sale_registers";

	$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $register_id");

	if ( !$db_data)
		return false;

	$row = $db_data[0];

	if ( !$outlet = $row->outlet )
		return false;

	$value_user_meta = esc_attr( get_user_meta( get_current_user_id(), 'outlet', true ) );
	if($value_user_meta == $outlet) return true;
	
	return false;
}

function _admin_notice_register_locked($register_id) {
	global $wpdb;

	$table_name = $wpdb->prefix . "wc_poin_of_sale_registers";

	$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $register_id");

	if ( !$db_data)
		return;

	$user = null;
	if (  $user_id = pos_check_register_lock( $register_id ) ){

		$user = get_userdata( $user_id );
	}

	if ( $user ) {
		$locked = true;
	} else {
		$locked = false;
	}

		$sendback = admin_url( 'admin.php?page=wc_pos_registers' );

		$sendback_text = __( 'All Registers', 'wc_point_of_sale' );

	?>
	<div id="post-lock-dialog" class="notification-dialog-wrap">
	<div class="notification-dialog-background"></div>
	<div class="notification-dialog">
	<?php
	if(!pos_check_user_can_open_register( $register_id )){
		?>
		<div class="post-locked-message not_close">
		<p class="currently-editing wp-tab-first" tabindex="0">
		<?php
			_e( 'You do not have permission to access this register.', 'wc_point_of_sale' );
		?>
		</p>
		<p>
		<a class="button" href="<?php echo esc_url( $sendback ); ?>"><?php echo $sendback_text; ?></a>
		</p>
		</div>
		<?php
	}else	if ( $locked ) {

		/**
		 * Filter whether to allow the post lock to be overridden.
		 *
		 * Returning a falsey value to the filter will disable the ability
		 * to override the post lock.
		 *
		 * @since 3.6.0
		 *
		 * @param bool    $override Whether to allow overriding post locks. Default true.
		 * @param WP_Post $post     Post object.
		 * @param WP_User $user     User object.
		 */
		$override = apply_filters( 'override_register_lock', false, $register_id, $user );
		$tab_last = $override ? '' : ' wp-tab-last';

		?>
		<div class="post-locked-message not_close">
		<div class="post-locked-avatar"><?php echo get_avatar( $user->ID, 64 ); ?></div>
		<p class="currently-editing wp-tab-first" tabindex="0">
		<?php
			_e( 'This register currently has a user (' . $user->display_name . ') logged on.' );
			if ( $override )
				printf( ' ' . __( 'If you take over, %s will be blocked from continuing to edit.' ), esc_html( $user->display_name ) );
		?>
		</p>
		<p>
		<a class="button" href="<?php echo esc_url( $sendback ); ?>"><?php echo $sendback_text; ?></a>
		<?php

		// Allow plugins to prevent some users overriding the post lock
		if ( $override ) {
			?>
			<a class="button button-primary wp-tab-last" href="admin.php?page=wc_pos_registers&amp;ation=get-post-lock&amp;id=<?php echo $register_id; ?>"><?php _e('Take over'); ?></a>
			<?php
		}

		?>
		</p>
		</div>
		<?php
	} else {
		?>
		<div class="post-taken-over" tabindex="0">
			<div id="process_loding">
				
			</div>
			<center>				
				<p><span class="spinner" style="display: block; float: none; visibility: visible;"></span></p>
			</center>
		</div>
		<?php
	}

	?>
	</div>
	</div>
	<?php
}

function set_outlet_taxable_address($address){
  $register_id = 0;
  if(isset($_POST['register_id']) && !empty($_POST['register_id']) ) {
  	$register_id = absint($_POST['register_id']);
  }elseif(isset($_GET['page']) && $_GET['page'] == 'wc_pos_registers' && isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id']) && !empty($_GET['action']) ){
  	$register_id = absint($_GET['id']);
  }
  if($register_id) {
      $id_outlet = getOutletID($register_id);

      $outlet = WC_POS()->outlet()->get_data($id_outlet);
      $address_data = $outlet[0]['contact'];
      return array( $address_data['country'], $address_data['state'], $address_data['postcode'], $address_data['city'] );
  }
  else{
      return $address;
  }
}
function isPrintReceipt($register_id = 0)
{	
	if($register_id){
		$register_data = WC_POS()->register()->get_data($register_id);
	    return $register_data[0]['settings']['print_receipt'];
	}
	return false;
}
function isNoteRequest($register_id = 0)
{	
	if($register_id){
		$register_data = WC_POS()->register()->get_data($register_id);
	    return $register_data[0]['settings']['note_request'];
	}
	return false;
}
function isEmailReceipt($register_id = 0)
{	
	if($register_id){
		$register_data = WC_POS()->register()->get_data($register_id);
		if( $register_data[0]['settings']['email_receipt'] ){
		    return array(
		    	'receipt_template' => $register_data[0]['detail']['receipt_template'],
		    	'outlet' => $register_data[0]['outlet']
		    	);
    	}
    	return false;
	}
	return false;
}

function sentEmailReceipt($register_id, $order_id)
{
	$email_receiptdetail = isEmailReceipt( $register_id );
	$email_notifications = get_option('wc_pos_email_notifications');
	

	/*if ( !class_exists( 'WC_Email' ) ){
		include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
		init()
	}*/

	if($email_notifications == 'yes'){
		$mail = WC()->mailer();
	    $mail->emails['WC_Email_New_Order']->trigger( $order_id );
    }
    if($email_receiptdetail){
		$mail = WC()->mailer();
	    $mail->emails['WC_Email_Customer_Processing_Order']->trigger( $order_id );
    }
	/*if($email_notifications == 'yes'){
		$mail = include_once( 'classes/emails/class-wc-pos-email-new-order.php' );
		$mail->trigger( $order_id );
    }
    if($email_receiptdetail){
	    $mail = include_once( 'classes/emails/class-wc-pos-email-customer-invoice.php' );
		$mail->trigger( $order_id );
    }*/

    /*if($email_notifications == 'yes'){
		do_action('wc_pos_email_new_order', $order_id);
    }
    if($email_receiptdetail){
    	do_action('wc_pos_email_customer_invoice', $order_id);
    }*/
}
function pos_set_html_content_type() {
    return 'text/html';
  }

function isChangeUser($register_id = 0)
{	
	if($register_id){
		$register_data = WC_POS()->register()->get_data($register_id);
	    return $register_data[0]['settings']['email_receipt'];
	}
	return false;
}
function front_enqueue_dependencies(){
	
	$wc_pos_version = WC_POS()->version;

  wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'jquery-ui-core', 'jquery-ui-datepicker'));

  wp_enqueue_script('jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js', array('jquery'), '2.66');
  wp_enqueue_script('woocommerce_admin_pos', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.min.js', array('jquery', 'jquery-blockui', 'jquery-placeholder', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip'));

  wp_enqueue_script('woocommerce_tiptip_js', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js');
  wp_enqueue_script('postbox_', admin_url() . '/js/postbox.min.js', array(), '2.66');
  
  wp_enqueue_script('accounting', WC()->plugin_url() . '/assets/js/admin/accounting.min.js', array('jquery'), '0.3.2');
  wp_enqueue_script('round', WC()->plugin_url() . '/assets/js/admin/round.min.js', array('jquery'), WC_VERSION);
  //wp_enqueue_script('woocommerce_admin_meta_boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes.js', array('jquery'), WC_VERSION);

  wp_enqueue_script('jquery_cycle', WC_POS()->plugin_url() . '/assets/plugins/jquery.cycle.all.js', array('jquery'), $wc_pos_version);
  wp_enqueue_script('jquery_barcodelistener', WC_POS()->plugin_url() . '/assets/plugins/anysearch.js', array('jquery'), $wc_pos_version);


  /****** START STYLE *****/
  wp_enqueue_style('thickbox');            
  wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

  wp_enqueue_style('wc-pos-fonts', WC_POS()->plugin_url() . '/assets/css/fonts.css', array(), $wc_pos_version);

  wp_enqueue_style('woocommerce_frontend_styles', WC()->plugin_url() . '/assets/css/admin.css');

  wp_enqueue_style('woocommerce-style', WC()->plugin_url() . '/assets/css/woocommerce-layout.css', array(), $wc_pos_version);
  wp_enqueue_style('wc-pos-style', WC_POS()->plugin_url() . '/assets/css/admin.css', array(), $wc_pos_version);

  /****** END STYLE *****/
  if ( defined('WC_VERSION') && version_compare( WC_VERSION, '2.3', '>=' ) ) {
      if( !wp_script_is( 'select2', 'enqueued' ) ){
          $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
          wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array( 'jquery' ), '3.5.2' );
          wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), WC_VERSION );
          wp_localize_script( 'select2', 'wc_select_params', array(
              'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
              'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
              'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
              'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
              'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
              'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
              'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
              'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
              'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
              'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
              'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
              'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
          ) );
          wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', array(
              'ajax_url'                         => admin_url( 'admin-ajax.php' ),
              'search_products_nonce'            => wp_create_nonce( 'search-products' ),
              'search_customers_nonce'           => wp_create_nonce( 'search-customers' )
          ) );

          wp_enqueue_script('wc-enhanced-select');
      }

  }else{
      wp_register_script('chosen_js', WC()->plugin_url() . '/assets/js/chosen/chosen.jquery.min.js', array('jquery'), '2.66', true);
      wp_register_script('ajax-chosen_js', WC()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery.min.js', array('jquery'), '2.66', true);

      wp_enqueue_script('chosen_js');
      wp_enqueue_script('ajax-chosen_js');                
  }
            
  if (in_array('woocommerce-gateway-stripe/woocommerce-gateway-stripe.php', apply_filters('active_plugins', get_option('active_plugins')))){

      wp_enqueue_script('jquery-payment', WC()->plugin_url() . '/assets/js/jquery-payment/jquery.payment.min.js', array( 'jquery' ), '1.0.2' );
      wp_enqueue_script( 'stripe', 'https://js.stripe.com/v1/', array('jquery'), '1.0' );
      wp_enqueue_script( 'woocommerce_stripe', plugins_url( 'woocommerce-gateway-stripe/assets/js/stripe.js' ), array( 'stripe' ), WC_STRIPE_VERSION );    

      $stripe = new WC_Gateway_Stripe();

      
      $testmode              = $stripe->get_option( 'testmode' ) === "yes" ? true : false;
      $secret_key            = $stripe->testmode ? $stripe->get_option( 'test_secret_key' ) : $stripe->get_option( 'secret_key' );
      $publishable_key       = $stripe->testmode ? $stripe->get_option( 'test_publishable_key' ) : $stripe->get_option( 'publishable_key' );



      $stripe_params = array(
          'key'        => $publishable_key,
          'i18n_terms' => __( 'Please accept the terms and conditions first', 'woocommerce-gateway-stripe' )
      );

      wp_localize_script( 'woocommerce_stripe', 'wc_stripe_params', $stripe_params );
  }


	wp_enqueue_script('wc-pos-register-functions', WC_POS()->plugin_url() . '/assets/js/register/functions.js', array(), $wc_pos_version);
	wp_enqueue_script('wc-pos-register-tax', WC_POS()->plugin_url() . '/assets/js/register/tax.js', array(), $wc_pos_version);
	wp_enqueue_script('wc-pos-register-coupons', WC_POS()->plugin_url() . '/assets/js/register/coupons.js', array(), $wc_pos_version);
	wp_enqueue_script('wc-pos-register-keypad', WC_POS()->plugin_url() . '/assets/js/register/keypad.js', array(), $wc_pos_version);
	wp_enqueue_script('wc-pos-register-script', WC_POS()->plugin_url() . '/assets/js/register.js', array('jquery'), $wc_pos_version);

  wp_enqueue_script('wc_pos_bootstrap-js', WC_POS()->plugin_url() . '/assets/plugins/bootstrap.min.js', array('jquery'), '3.1.1');
  wp_enqueue_script('wc_pos_bootstrap-switch-js', WC_POS()->plugin_url() . '/assets/plugins/bootstrap-switch/bootstrap-switch.min.js', '3.2.0');
  wp_enqueue_style('wc_pos_bootstrap-switch-css', WC_POS()->plugin_url() . '/assets/plugins/bootstrap-switch/bootstrap-switch.min.css', array(), '3.2.0');
  wp_enqueue_script('wc_pos_bootstrap-ladda', WC_POS()->plugin_url() . '/assets/plugins/ladda-bootstrap/bootstrap.min.js', array(), '3.2.0');
  wp_enqueue_script('wc_pos_ladda-bootstrap', WC_POS()->plugin_url() . '/assets/plugins/ladda-bootstrap/ladda.min.js', array('wc_pos_bootstrap-ladda'), '3.2.0');
  wp_enqueue_style('wc_pos_ladda-bootstrap', WC_POS()->plugin_url() . '/assets/plugins/ladda-bootstrap/ladda-themeless.css', array(), '3.2.0');

  wp_enqueue_script('wc_pos_timeago', WC_POS()->plugin_url() . '/assets/plugins/jquery.timeago.js', array('wc_pos_ladda-bootstrap'), '3.2.0');

  wp_enqueue_script('wc_pos_toastr', WC_POS()->plugin_url() . '/assets/plugins/toastr/toastr.js', array('jquery') );
  wp_enqueue_style('wc_pos_toastr', WC_POS()->plugin_url() . '/assets/plugins/toastr/toastr.css', array());

  wp_enqueue_script('wc_pos_ion_sound', WC_POS()->plugin_url() . '/assets/plugins/ion.sound/ion.sound.min.js', array('jquery', 'wc-pos-register-script') );

  wp_enqueue_script('wc_pos_offline', WC_POS()->plugin_url() . '/assets/plugins/offline/offline.min.js', array('jquery'));
  wp_localize_script('wc_pos_offline', 'wc_pos_offline_params', apply_filters('wc_pos_offline_params', array(
    'url' => WC_POS()->plugin_url() . '/assets/plugins/offline/blank.png',
    )));

  wp_enqueue_style('wc_pos_offline', WC_POS()->plugin_url() . '/assets/plugins/offline/offline-theme-chrome-indicator.css' );
  wp_enqueue_style('wc_pos_offline-language-english', WC_POS()->plugin_url() . '/assets/plugins/offline/offline-language-english.css' );
  /******************/
  wp_register_script('wc_pos_cardswipe', WC_POS()->plugin_url() . '/assets/plugins/jquery.cardswipe.js', array('jquery', 'wc-pos-register-script'), $wc_pos_version);
  wp_enqueue_script('wc_pos_cardswipe');

  wp_enqueue_script('wc_pos_payment_gateways', WC_POS()->plugin_url() . '/assets/js/payment_gateways.js', array('jquery', 'wc_pos_cardswipe'), $wc_pos_version);

  wp_enqueue_script('wc_pos_checkout',  WC_POS()->plugin_url() . '/assets/js/checkout.js?v='.rand(), array('jquery'), '');
  /******************/
  wp_register_script('woocommerce-point-of-sale-script-jquery_keypad_plugin', WC_POS()->plugin_url() . '/assets/plugins/jquery_keypad/jquery.plugin.min.js', array('jquery'), $wc_pos_version);

	wp_register_script('woocommerce-point-of-sale-script-jquery_keypad', WC_POS()->plugin_url() . '/assets/plugins/jquery_keypad/jquery.keypad.min.js', array('jquery'), $wc_pos_version);
  wp_enqueue_script('jquery_category_cycle', WC_POS()->plugin_url() . '/assets/js/category_cycle.js', array('jquery'), $wc_pos_version);
  wp_enqueue_script('js_md5_hash', WC_POS()->plugin_url() . '/assets/js/md5-min.js');

   //Detect special conditions devices
    $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
    $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
    $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
    $Safari   = stripos($_SERVER['HTTP_USER_AGENT'],"Safari");
    $Chrome   = stripos($_SERVER['HTTP_USER_AGENT'],"Chrome");
    
    $arr_scripts = array(
    	'jquery',
  		'woocommerce-point-of-sale-script-jquery_keypad_plugin',
  		'woocommerce-point-of-sale-script-jquery_keypad',
  		'wc-pos-register-tax',
  		'wc-pos-register-coupons',
  		'wc-pos-register-keypad',
  		'wc-pos-register-functions',
  		'wc-pos-register-script',
  		'wc_pos_timeago',
  		'jquery_category_cycle',
  		'js_md5_hash');
    if($Chrome === false && ($iPod !== false || $iPhone !== false  || $iPad !== false  || $webOS !== false  || $Safari !== false) ){
        wp_enqueue_script('woocommerce-point-of-sale-websql', WC_POS()->plugin_url() . '/assets/js/websql.js', $arr_scripts, '45');
    }else{
        wp_enqueue_script('woocommerce-point-of-sale-pos', WC_POS()->plugin_url() . '/assets/js/pos.js', $arr_scripts, '0.1');
    }
    

    wp_register_style('woocommerce-style', WC()->plugin_url() . '/assets/css/woocommerce-layout.css', array(), $wc_pos_version);
    wp_enqueue_style('woocommerce-style');

    wp_register_style('woocommerce-point-of-sale-jquery_plugin', WC_POS()->plugin_url() . '/assets/plugins/jquery_keypad/jquery.keypad.css', array(), $wc_pos_version);
    wp_enqueue_style('woocommerce-point-of-sale-jquery_plugin');    

    pos_localize_script('wc-pos-register-script');
}

function pos_localize_script($script_name = '')
{
	$pos_tax_based_on = get_option( 'woocommerce_pos_calculate_tax_based_on' );
	if($pos_tax_based_on == 'default'){
		$pos_tax_based_on = get_option( 'woocommerce_tax_based_on' );
	}

  	wp_localize_script($script_name, 'wc_pos_params', apply_filters('wc_pos_params', array(
      'wp_debug'        => defined('WP_DEBUG') ? WP_DEBUG : false,
      'avatar'          => get_avatar( 0, 30),
      'sound_path'      => WC_POS()->plugin_sound_url(),
      'ajax_url'        => WC()->ajax_url(),
      'admin_url'       => admin_url(),
      'ajax_loader_url' => apply_filters('woocommerce_ajax_loader_url', WC()->plugin_url() . '/assets/images/ajax-loader@2x.gif'),
      'reprint_receipt_url' => wp_nonce_url( admin_url( 'admin.php?print_pos_receipt=true&order_id=_order_id_' ), 'print_pos_receipt' ),
      'post_id'         => isset($post->ID) ? $post->ID : '',
      'def_img'         => wc_placeholder_img_src(),
      'custom_pr_id'    => (int)get_option('wc_pos_custom_product_id'),
      'attr_tax_names'  => pos_get_attribute_taxonomy_names(),
      'hidden_order_itemmeta'    => array_flip(apply_filters( 'woocommerce_hidden_order_itemmeta', array(
              '_qty',
              '_tax_class',
              '_product_id',
              '_variation_id',
              '_line_subtotal',
              '_line_subtotal_tax',
              '_line_tax_data',
              '_line_total',
              '_line_tax',
            ))),

      'new_update_pos_outlets_address_nonce'  => wp_create_nonce("new-update-pos-outlets-address"),
      'edit_update_pos_outlets_address_nonce' => wp_create_nonce("edit-update-pos-outlets-address"),
      'search_variations_for_product'         => wp_create_nonce("search_variations_for_product"),
      'printing_receipt_nonce'                => wp_create_nonce("printing_receipt"),
      'add_product_to_register'               => wp_create_nonce("add_product_to_register"),
      'remove_product_from_register'          => wp_create_nonce("remove_product_from_register"),
      'add_customers_to_register'             => wp_create_nonce("add_customers_to_register"),
      'check_shipping'                        => wp_create_nonce("check_shipping"),
      'load_order_data'                       => wp_create_nonce("load_order_data"),
      'load_pending_orders'                   => wp_create_nonce("load_pending_orders"),
      'search_products_and_variations'        => wp_create_nonce("search-products"),
      'add_product_grid'                      => wp_create_nonce("add-product_grid"),
      'search_customers'                      => wp_create_nonce("search-customers"),
      'void_register_nonce'                   => wp_create_nonce("void_register"),

      'remove_item_notice'      => __("Are you sure you want to remove the selected items?", 'wc_point_of_sale'),
      'void_register_notice'    => __("Are you sure you want to clear all fields and start from scratch?", 'wc_point_of_sale'),
      'register_discount_text'  => __("Order Discount. This is the total discount applied after tax.", 'wc_point_of_sale'),
      'product_no_sku'          => __('No SKU, for this product, please define an SKU to print barcodes.', 'wc_point_of_sale'),
      'variation_no_sku'        => __('No SKU, for this variation, please define an SKU to print barcodes.', 'wc_point_of_sale'),
      'no_default_selection'    => __('No Default Selection', 'wc_point_of_sale'),
      'open_another_tab'        => __('This register is already open in another tab.', 'wc_point_of_sale'),
      'remove_button'           => __('Remove', 'wc_point_of_sale'),
      'cannot_add_product'      => __('You cannot add that amount of "%NAME%" to the cart because there is not enough stock (%COUNT% remaining).', 'wc_point_of_sale'),
      'out_of_stock'            => __('This product is out of stock. Please select a different product or variation', 'wc_point_of_sale'),
      'cannot_be_purchased'     => __('Sorry, this product cannot be purchased.', 'wc_point_of_sale'),
      'i18n_ereceipt'           => __('Do you want to email the receipt?', 'wc_point_of_sale')."\n\n".__('Enter customer email', 'wc_point_of_sale'),
      'i18n_tax'                => __('Tax', 'wc_point_of_sale'),

      /***** Coupon *******/
      'i18n_c_individual_error'     => __('Sorry, coupon "%s" has already been applied and cannot be used in conjunction with other coupons.', 'wc_point_of_sale'),
      'i18n_c_already_applied'      => __('Coupon code already applied!', 'woocommerce'),
      'i18n_c_applied'              => __('Coupon code applied successfully.', 'woocommerce'),
      'i18n_c_not_exist'            => __('Coupon does not exist!', 'woocommerce'),
      'i18n_c_usage_limit'          => __('Coupon usage limit has been reached.', 'woocommerce'),
      'i18n_c_minimum_spend'        => __('The minimum spend for this coupon is %s.', 'wc_point_of_sale'),
      'i18n_c_maximum_spend'        => __('The maximum spend for this coupon is %s.', 'wc_point_of_sale'),
      'i18n_c_expired'              => __('This coupon has expired.', 'woocommerce'),
      'i18n_c_not_applicable'       => __('Sorry, this coupon is not applicable to your cart contents.', 'woocommerce'),
      'i18n_c_sale_items'    => __('Sorry, this coupon is not valid for sale items.', 'woocommerce'),
      'i18n_c_not_applicable_pr'    => __('Sorry, this coupon is not applicable to the products: %s', 'wc_point_of_sale'),
      'i18n_c_invalid'              => __('Sorry, it seems the coupon "%s" is invalid - it has now been removed from your order.', 'wc_point_of_sale'),
      'i18n_c_remove'               => __('Remove', 'wc_point_of_sale'),
      'product_ids_on_sale'         => wc_get_product_ids_on_sale(),
      /***** Coupon *******/
      
      'mon_decimal_point'            => get_option('woocommerce_price_decimal_sep'),
      'currency_format_num_decimals' => absint(get_option('woocommerce_price_num_decimals')),
      'currency_format_symbol'       => get_woocommerce_currency_symbol(),
      'currency_format_decimal_sep'  => esc_attr(stripslashes(get_option('woocommerce_price_decimal_sep'))),
      'currency_format_thousand_sep' => esc_attr(stripslashes(get_option('woocommerce_price_thousand_sep'))),

      'pos_calc_taxes'           => get_option( 'woocommerce_pos_tax_calculation'),
      'currency_format'          => esc_attr(str_replace(array('%1$s', '%2$s'), array('%s', '%v'), get_woocommerce_price_format())), // For accounting JS

      'ready_to_scan'            => get_option('woocommerce_pos_register_ready_to_scan'),
      'cc_scanning'              => get_option('woocommerce_pos_register_cc_scanning'),
      'instant_quantity'         => get_option('woocommerce_pos_register_instant_quantity'),
      'instant_quantity_keypad'  => get_option('woocommerce_pos_register_instant_quantity_keypad'),
      'term_relationships'       => pos_term_relationships(),
      'category_archive_display' => get_option( 'woocommerce_category_archive_display'),

      'barcode_url'        => plugins_url( 'includes/classes/barcode/image.php?filetype=PNG&dpi=72&scale=2&rotation=0&font_family=Arial.ttf&&thickness=30&start=NULL&code=BCGcode128' , realpath(dirname(__FILE__) ) ), 

      'wc_api_url'  => WC_POS()->wc_api_url(),
      
      'discount_presets' => WC_Admin_Settings::get_option( 'woocommerce_pos_register_discount_presets', array(5,10,15,20) ),
      'show_stock'       => WC_Admin_Settings::get_option( 'wc_pos_show_stock', 'yes' ),
      'user_can_edit_product'       => current_user_can( 'edit_private_products' ),
      'wc'          =>  array(
                          'tax_label'             => WC()->countries->tax_or_vat(), 
                          'tax_display_shop'      => get_option( 'woocommerce_tax_display_shop' ),
                          'calc_taxes'            => get_option( 'woocommerce_calc_taxes' ),
                          'prices_include_tax'    => get_option( 'woocommerce_prices_include_tax' ),
                          'tax_round_at_subtotal' => get_option( 'woocommerce_tax_round_at_subtotal' ),
                          'tax_display_cart'      => get_option( 'woocommerce_tax_display_cart' ),
                          'tax_total_display'     => get_option( 'woocommerce_tax_total_display' ),
                          'pos_tax_based_on'      => $pos_tax_based_on,
                          'precision'             => WC_ROUNDING_PRECISION,
                          'all_rates'             => wc_pos_find_all_rates(),
                          'outlet_location'       => wc_pos_get_outlet_location(),
                          'shop_location'         => wc_pos_get_shop_location(),
                          'european_union_countries' => WC()->countries->get_european_union_countries(),
                          'base_country'             => WC()->countries->get_base_country(),
                      ),


      
      ))
  );	
}
function wc_pos_get_outlet_location()
{
	$location = array();
	if( !isset($_GET['outlet']) || !isset($_GET['reg']) ) return $location;
		$slug = $_GET['reg'];
		$data = WC_POS()->register()->get_data_by_slug($slug);
		$data = $data[0];
		$slug      = $data['slug'];
		$register  = $slug;
		$outlet_id = $data['outlet'];
		$outlet    = WC_POS()->outlet()->get_data($outlet_id);
		$outlet    = $outlet[0];
		$location  = array(
				'country' 	=> $outlet['contact']['country'],
				'state' 	=> $outlet['contact']['state'],
				'city' 		=> $outlet['contact']['city'],
				'postcode' 	=> $outlet['contact']['postcode'],
			);
		return $location;
}

function wc_pos_get_shop_location()
{
	return array(
				'country' 	=> WC()->countries->get_base_country(),
				'state' 	=> WC()->countries->get_base_state(),
				'postcode' 	=> WC()->countries->get_base_postcode(),
				'city' 		=> WC()->countries->get_base_city()
			);
}

function wc_pos_find_all_rates()
{
	global $wpdb;
	// Run the query
	
	$tax_class = '';
	$rates     = array();
	$sql = "SELECT tax_rates.*
			FROM {$wpdb->prefix}woocommerce_tax_rates as tax_rates
			LEFT OUTER JOIN {$wpdb->prefix}woocommerce_tax_rate_locations as locations ON tax_rates.tax_rate_id = locations.tax_rate_id
			LEFT OUTER JOIN {$wpdb->prefix}woocommerce_tax_rate_locations as locations2 ON tax_rates.tax_rate_id = locations2.tax_rate_id
			GROUP BY tax_rate_id
			ORDER BY tax_rate_priority, tax_rate_order
		";
	$found_rates = $wpdb->get_results( $sql );


	foreach ( $found_rates as $key_rate => $found_rate ) {
		
		$sql = "SELECT location_code FROM {$wpdb->prefix}woocommerce_tax_rate_locations WHERE tax_rate_id = {$found_rate->tax_rate_id} AND location_type = 'postcode' ";
		$found_postcodes = $wpdb->get_results( $sql );
		$postcode = array();
		if($found_postcodes)
			foreach ($found_postcodes as $code) {
				$postcode[] = $code->location_code;
			}

		$sql = "SELECT location_code FROM {$wpdb->prefix}woocommerce_tax_rate_locations WHERE tax_rate_id = {$found_rate->tax_rate_id} AND location_type = 'city' ";
		$found_postcodes = $wpdb->get_results( $sql );
		$city = array();
		if($found_postcodes)
			foreach ($found_postcodes as $code) {
				$city[] = $code->location_code;
			}

		$rates[ $key_rate ] = array(
			'rate'     => $found_rate->tax_rate,
			'label'    => $found_rate->tax_rate_name,
			'shipping' => $found_rate->tax_rate_shipping ? 'yes' : 'no',
			'compound' => $found_rate->tax_rate_compound ? 'yes' : 'no',
			'country'  => $found_rate->tax_rate_country,
			'state'    => $found_rate->tax_rate_state,
			'city' 	   => implode(';', $city),
			'postcode' => implode(';', $postcode),
			'taxclass' => $found_rate->tax_rate_class,
			'priority' => $found_rate->tax_rate_priority
		);
	}

	return $rates;
}


function wc_pos_get_register($id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
	$reg = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id LIMIT 1");
	if(isset($reg))
		return $reg;
	else
		return false;
}
function getOutletID($reg_id = 0)
{
		global $wpdb;
		if(!$reg_id) return 0;
		
	  $db_data = wc_pos_get_register($reg_id);
		return $db_data->outlet;
}
function wc_pos_check_can_delete($type, $ids)
{
	global $wpdb;
	$table_reg = $wpdb->prefix . "wc_poin_of_sale_registers";
	switch ($type) {
		case 'outlet':
			if(is_array($ids)){
				foreach ($ids as $key => $id) {
					$result = $wpdb->get_results("SELECT ID FROM $table_reg WHERE outlet = $id");
					if($result)
						unset($ids[$key]);
				}
				if (!empty($ids)) {
	        $ids = implode(',', array_map('intval', $ids));
	        return "WHERE ID IN ($ids)";
				}
      }else{
      	$result = $wpdb->get_results("SELECT ID FROM $table_reg WHERE outlet = $ids");
				if(!$result)
        	return "WHERE ID = $ids";
      }
      return false;
			break;
		case 'grid':
			if(is_array($ids)){
					foreach ($ids as $key =>  $id) {
						$result = $wpdb->get_results("SELECT ID FROM $table_reg WHERE detail LIKE '%\"grid_template\":\"$id\"%' ");
						if($result)
							unset($ids[$key]);
					}

					if (!empty($ids))
						return $ids;
        }else{
        	$result = $wpdb->get_results("SELECT ID FROM $table_reg WHERE detail LIKE '%\"grid_template\":\"$ids\"%' ");
					if(!$result)
          	return $ids;
        }
      return false;
			break;
		case 'receipt':
			if(is_array($ids)){
					foreach ($ids as $key =>  $id) {
						$result = $wpdb->get_results("SELECT ID FROM $table_reg WHERE detail LIKE '%\"receipt_template\":\"$id\"%' ");
						if($result)
							unset($ids[$key]);
					}

					if (!empty($ids))
						return $ids;
        }else{
        	$result = $wpdb->get_results("SELECT ID FROM $table_reg WHERE detail LIKE '%\"receipt_template\":\"$ids\"%' ");
					if(!$result)
          	return $ids;
        }
      return false;
			break;
		
		default:
			return false;
			break;
	}
}

function pos_term_relationships()
{
	$relationships = array();	
	$hierarchy     = array();	
	$parents       = array();	
	$terms         = get_terms( 'product_cat');
	$relationships[0] = pos_get_non_cat_products(); 
	if($terms){
		foreach ($terms as $term) {
			$term_id = (int)$term->term_id;
			$parent  = (int)$term->parent;
			if($parent == 0 ){

				if( !isset( $hierarchy[$term_id] ) )
					$hierarchy[$term_id] = array();

				$parents[$term_id] = $term_id;
			}

			if( $parent > 0 )
				$hierarchy[$parent][] = $term_id;

			$products = new WP_Query(array(
				'posts_per_page' => -1,
				'post_type' => 'product',
				'fields'    => 'ids',
				'tax_query' => array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => $term->term_id,
					),
				),
			));
			$relationships[$term->term_id] = $products->posts; 
		}
	}

	$rel = array();
	$rel['relationships'] = $relationships;
	$rel['hierarchy']     = $hierarchy;
	$rel['parents']       = $parents;
	return $rel;
}

function pos_get_non_cat_products()
{
  global $wpdb;
  $products = array();

  $tax      = "SELECT tax.term_taxonomy_id tax_id FROM {$wpdb->term_taxonomy} tax WHERE tax.taxonomy = 'product_cat' ";
  $taxonomy = $wpdb->get_results($tax);
  $t = array();
  if($taxonomy){
    foreach ($taxonomy as $tx) {
      $t[] = $tx->tax_id;
    }
  }
  if(!empty($t)){
	  $t = implode(',', $t);  
	}else{
		$t = 0;
	}
  $query    = "SELECT post.ID FROM {$wpdb->posts} post 
    LEFT JOIN {$wpdb->term_relationships} rel ON(rel.object_id = post.ID AND rel.term_taxonomy_id IN({$t}) )
    WHERE post.post_type = 'product' AND post.post_status = 'publish' AND rel.object_id IS NULL
    ";
  $result   = $wpdb->get_results($query);
  if($result){
  	foreach ($result as $value) {
  		$products[] = $value->ID;
  	}
  }

  return $products;
}


function pos_get_registers_by_outlet($outlet_id=0)
{
	global $wpdb;
	$registers = array();

	if($outlet_id){
		$table  =  $wpdb->prefix . 'wc_poin_of_sale_registers';
		$query  = "SELECT ID FROM {$table} WHERE outlet = $outlet_id";
		$result = $wpdb->get_results( $query);
		if($result)
			foreach ($result as $reg)
			$registers[] = $reg->ID;
		
	}
	return $registers;
}
function pos_get_registers_by_cashier($cashier_id=0)
{
	global $wpdb;
	$registers = array();

	if($cashier_id){
		$order_types     = wc_get_order_types( 'order-count' );
		$order_types     = implode( "','", $order_types );
		$query = "SELECT CAST( meta_register_id.meta_value as SIGNED) as register_id
									FROM {$wpdb->posts} AS posts
									LEFT JOIN {$wpdb->postmeta} AS meta_register_id ON ( posts.ID = meta_register_id.post_id AND meta_register_id.meta_key = 'wc_pos_id_register' )
									LEFT JOIN {$wpdb->postmeta} AS meta_order_type ON ( posts.ID = meta_order_type.post_id AND meta_order_type.meta_key = 'wc_pos_order_type' )
      						WHERE posts.post_type IN ( '{$order_types}' )
      						AND posts.post_author = {$cashier_id}
        					AND meta_order_type.meta_value = 'POS' GROUP BY register_id";

		$result = $wpdb->get_results($query);

		if($result)
			foreach ($result as $reg)
			$registers[] = (int)$reg->register_id;
		
	}

	return $registers;
}
function pos_enable_generate_password($value)
{
	return 'yes';
}
/**
 * Get an array of product attribute taxonomies.
 *
 * @access public
 * @return array
 */
function pos_get_attribute_taxonomy_names() {
	$taxonomy_names = array();
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $tax ) {
			$taxonomy_names[wc_attribute_taxonomy_name( $tax->attribute_name )] = $tax->attribute_name;
		}
	}
	return $taxonomy_names;
}

function pos_get_user_html($user_to_add)
{
	if( $user_to_add > 0 ){
		$customer = new WP_User( $user_to_add);
	        
	    $b_addr = array(
	            'first_name' => $customer->billing_first_name,
	            'last_name'  => $customer->billing_last_name,
	            'company'    => $customer->billing_company,
	            'address_1'  => $customer->billing_address_1,
	            'address_2'  => $customer->billing_address_2,
	            'city'       => $customer->billing_city,
	            'state'      => $customer->billing_state,
	            'postcode'   => $customer->billing_postcode,
	            'country'    => $customer->billing_country,
	            'email'      => $customer->billing_email,
	            'phone'      => $customer->billing_phone,
	        );
	    $s_addr = array(
	            'first_name' => $customer->shipping_first_name,
	            'last_name'  => $customer->shipping_last_name,
	            'company'    => $customer->shipping_company,
	            'address_1'  => $customer->shipping_address_1,
	            'address_2'  => $customer->shipping_address_2,
	            'city'       => $customer->shipping_city,
	            'state'      => $customer->shipping_state,
	            'postcode'   => $customer->shipping_postcode,
	            'country'    => $customer->shipping_country,
	        );

	    $user_data = array(
	        'first_name' => $customer->first_name,
	        'last_name'  => $customer->last_name,
	        'email'      => $customer->user_email
	        );

	    if(empty($b_addr['first_name'])){
	        $b_addr['first_name'] = $user_data['first_name'];
	    }
	    if(empty($b_addr['last_name'])){
	        $b_addr['last_name'] = $user_data['last_name'];
	    }
	    if(empty($b_addr['email'])){
	        $b_addr['email'] = $user_data['email'];      
	    }
    } 


    $class = 'new_row';    
    ob_start();
    require( WC_POS()->plugin_path() . '/includes/views/html-admin-registers-customer.php' );

    $out = ob_get_contents();

    return $out;
}