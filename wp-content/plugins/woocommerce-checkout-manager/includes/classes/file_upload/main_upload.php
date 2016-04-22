<?php

/**
 * WooCommerce Checkout Manager 
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wooccm_set_html_content_type() {

	return 'text/html';

}

function wccm_validate_upload_process_customer() {

	$options = get_option( 'wccs_settings' );

	if( !empty($options['checkness']['enable_file_upload'])) {
		return true;
	} else {
		return false;
	}

}

if( wccm_validate_upload_process_customer() ) {
	add_action('woocommerce_view_order','wccs_file_uploader_front_end');
	add_action( 'add_meta_boxes', 'wccs_initialize_metabox');
}

function wccs_upload_file_func_callback( $order_id ) {

	global $wpdb, $woocommerce, $post; // this is how you get access to the database

	$options = get_option( 'wccs_settings' );

	$order_id = ( isset( $_REQUEST['order_id'] ) ? absint( $_REQUEST['order_id'] ) : false );

	$order = new WC_Order( $order_id );

	// load files
	require_once( ABSPATH . 'wp-admin/includes/file.php' ); 
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$upload_dir = wp_upload_dir();

	$name = ( isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : false );

	if( empty( $name ) ) {
		echo ' '.__('Upload failed. Files were not uploaded.','woocommerce-checkout-manager').'';
		die();
	}

	if( empty( $order_id ) ) {
		echo ' '.__('Invalid Order. Files were not uploaded.','woocommerce-checkout-manager').'';
		die();
	}

	$files = $_FILES[''. $name .''];
	// $upload_overrides = array( 'test_form' => false );

	if( !empty( $files['name'] ) ) {
		foreach( $files['name'] as $key => $value ) {
			if( $files['name'][$key] ) {

				// using the wp_handle_upload
				if ( empty($options['checkness']['cat_file_upload']) ) {
					$file = array(
						'name'     => $files['name'][$key],
						'type'     => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'error'    => $files['error'][$key],
						'size'     => $files['size'][$key]
					);

					// $movefile = wp_handle_upload($file, $upload_overrides);
					$movefile = wp_handle_upload($file);

					// Check if the save process failed
					if( isset( $movefile['error'] ) ) {
						echo 'Could not save uploaded file. Files were not uploaded.';
						die();
					}

					$attachment = array(
						'guid' => $movefile['url'], 
						'post_mime_type' => $movefile['type'],
						'post_title' => preg_replace( '/\.[^.]+$/', '', basename($movefile['file'])),
						'post_content' => '',
						'post_status' => 'inherit'
					);

					$attach_id = wp_insert_attachment( $attachment, $movefile['url'], $order_id);

					// you must first include the image.php file
					// for the function wp_generate_attachment_metadata() to work

					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['url'] );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					// send email
					$email_recipients = $options['checkness']['wooccm_notification_email'];
					$message_content = '
This is an automatic message from WooCommerce Checkout Manager, reporting that files has been uploaded by '.$order->billing_first_name.' '.$order->billing_last_name.'.<br />
<h3>Customer Details</h3>
Name: '.$order->billing_first_name.' '.$order->billing_last_name.'<br />
E-mail: '.$order->billing_email.'<br />
Order Number: '.$order_id.' <br /> 
You can view the files and order details via back-end by following this <a href="'.admin_url('/post.php?post='.$order_id.'&action=edit').'">link</a>.
';

					add_filter( 'wp_mail_content_type', 'wooccm_set_html_content_type' );
					wp_mail( $email_recipients, 'WooCommerce Checkout Manager - Files Uploaded by Customer ['.$order->billing_first_name.' '.$order->billing_last_name.']', $message_content );

					// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
					remove_filter( 'wp_mail_content_type', 'wooccm_set_html_content_type' );

				} else {

					// using move_uploaded_file to categorized uploaded images
					if (!file_exists($upload_dir['basedir']. '/wooccm_uploads/'.$order_id.'/')) {
						wp_mkdir_p($upload_dir['basedir']. '/wooccm_uploads/'.$order_id.'/');
					}

					$filename = $files['name'][$key];
					$wp_filetype = wp_check_filetype($filename);
					$URLpath = $upload_dir['baseurl']. '/wooccm_uploads/'.$order_id.'/'.$filename;

					move_uploaded_file( $files["tmp_name"][$key], $upload_dir['basedir']. '/wooccm_uploads/'.$order_id.'/'.$filename);

					$attachment = array(
						'guid' => $URLpath, 
						'post_mime_type' => $wp_filetype['type'],
						'post_title' => preg_replace( '/\.[^.]+$/', '', $filename),
						'post_content' => '',
						'post_status' => 'inherit'
					);

					$attach_id = wp_insert_attachment( $attachment, $URLpath, $order_id);

					// you must first include the image.php file
					// for the function wp_generate_attachment_metadata() to work

					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $URLpath );
					wp_update_attachment_metadata( $attach_id, $attach_data );
					// send email
					$email_recipients = get_option('admin_email');
					$message_content = '
This is an automatic message from WooCommerce Checkout Manager, reporting that files has been uploaded by '.$order->billing_first_name.' '.$order->billing_last_name.'.<br />
<h3>Customer Details</h3>
Name: '.$order->billing_first_name.' '.$order->billing_last_name.'<br />
E-mail: '.$order->billing_email.'<br />
Order Number: '.$order_id.' <br /> 
You can view the files and order details via back-end by following this <a href="'.admin_url('/post.php?post='.$order_id.'&action=edit').'">link</a>.
';

					add_filter( 'wp_mail_content_type', 'wooccm_set_html_content_type' );
					wp_mail( $email_recipients, 'WooCommerce Checkout Manager - Files Uploaded by Customer', $message_content );

					// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
					remove_filter( 'wp_mail_content_type', 'wooccm_set_html_content_type' );

				}
			}
		}
		echo ' '.__('Files were uploaded successfully.','woocommerce-checkout-manager').'';
	} else {
		echo ' '.__('No uploads were recognised. Files were not uploaded.','woocommerce-checkout-manager').'';
	}
	die();

}
add_action("wp_ajax_wccs_upload_file_func", "wccs_upload_file_func_callback");
add_action("wp_ajax_nopriv_wccs_upload_file_func", "wccs_upload_file_func_callback");

function wccs_initialize_metabox() {

	global $post;

	$post_type = 'shop_order';
	add_meta_box( 'woocommerce-order-files', __( 'Order Uploaded files', 'woocommerce-checkout-manager' ), 'wccs_file_uploader_data_meta_box', $post_type, 'normal', 'default' );

}

function wccs_file_uploader_data_meta_box( $post ) {

	global $wpdb, $thepostid, $theorder, $woocommerce, $post;

	$options = get_option( 'wccs_settings' );

	$upload_dir = wp_upload_dir();

?>

<script type="text/javascript">
jQuery(document).ready(function($) {

	$('#wccm_save_order_submit').click(function() {
		$(".wccm_results").html("Saving, please wait....");
		var data = {
			action: 'update_attachment_wccm',
			post_id : '<?php echo $post->ID; ?>',
			product_image_gallery : $('#product_image_gallery').val(),
			wccm_default_keys_load : $('#wccm_default_keys_load').val()
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			$(".wccm_results").html(response);
		});
	}); 

});
</script>

<?php wp_enqueue_style( 'wccm_upload_file_style', plugins_url( 'includes/classes/file_upload/file_editing_table.css', WOOCCM_RELPATH ) ); ?>

<div class="woocommerce_order_items_wrapper">
	<table class="woocommerce_order_items back_end">

		<thead>
			<tr>
				<th nowrap>
					<attr title="<?php _e( 'Attachment ID', 'woocommerce-checkout-manager' ); ?>"><?php _e('ID','woocommerce-checkout-manager'); ?></attr>
				</th>
				<th><?php _e('Image','woocommerce-checkout-manager'); ?></th>
				<th><?php _e('Action','woocommerce-checkout-manager'); ?></th>
				<th><?php _e('Dimensions','woocommerce-checkout-manager'); ?></th>
				<th><?php _e('Extension','woocommerce-checkout-manager'); ?></th>
				<th><?php _e('Name','woocommerce-checkout-manager'); ?></th>
			</tr>
		</thead>

		<tbody class="product_images">
<?php

	$default_wccm_values = '';
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_status' => null,
		'post_parent' => $post->ID
	); 
	$attachment_args = get_posts( $args );
	if( $attachment_args ) {
		foreach($attachment_args as $attachment) {
			$array[] = $attachment->ID;
		}
		$default_wccm_values = implode(',',$array);
		$product_image_gallery = implode(',',$array);
	}

	if( empty($product_image_gallery)) {
		$product_image_gallery = '';
	}

	$attachments = array_filter( explode( ',', $product_image_gallery ) );
	if( $attachments ) {
		foreach ( $attachments as $attachment_id ) {

			$image_attributes = wp_get_attachment_url( $attachment_id );
			$image_attributes2 = wp_get_attachment_image_src( $attachment_id );
			$filename = basename( $image_attributes );
			$wp_filetype = wp_check_filetype( $filename );

			$value_declear = array_diff(explode( ',',$default_wccm_values), explode( ',',$attachment_id));

			echo '
			<tr class="image wccm_filesli wccmv_' . esc_attr( $attachment_id ) . '">
				<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery(".wccmx_' . esc_attr( $attachment_id ) . '").click(function(){

						jQuery(".wccmv_' . esc_attr( $attachment_id ) . '").hide();
						jQuery("#product_image_gallery").val(jQuery("#product_image_gallery").val().replace("'.esc_attr( $attachment_id ).'", ""));

					});
				});
				</script>
				<td>'.$attachment_id.'</td>
				<td>'.wp_get_attachment_image( $attachment_id, array(75,75), true ).'</td>
				<td style="text-align:left;">
					<a href="' . $image_attributes2[0] . '" target="_blank">' . __( 'Download', 'woocommerce-checkout-manager' ) . '</a><br />
					<a class="delete tips wccm_delete wccmx_' . esc_attr( $attachment_id ) . '" data-tip="' . __( 'Delete image', 'woocommerce' ) . '">' . __( 'Delete', 'woocommerce' ) . '</a>
				</td>
				<td>';
			if( $image_attributes2[1] == '' ) {
				echo '-';
			} else {
				echo $image_attributes2[1].' x '.$image_attributes2[2];
			}
			echo '</td>
				<td>'.strtoupper( $wp_filetype['ext'] ).'</td>
				<td style="text-align:left;">'.preg_replace( '/\.[^.]+$/', '', $filename).'</td>
			</tr>
';

		}
	}
?>
		</tbody>
	</table>
	<input type="hidden" class="wccm_add_to_list" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_image_gallery ); ?>" />
	<input type="hidden" id="wccm_default_keys_load" name="wccm_default_keys_load" value="<?php echo esc_attr( $default_wccm_values ); ?>" />
	<!-- .woocommerce_order_items -->

	<p class="add_product_images hide-if-no-js">
		<form method="POST" action="">
			<span class="btn button-primary wccm_add_order_link fileinput-button">
				<span><?php _e( 'Add Order Files', 'woocommerce-checkout-manager' ); ?></span>
				<input type="file" name="files_wccm" id="files_wccm" multiple />
				<button type="button" id="files_button_wccm"><?php _e( 'Upload Files!', 'woocommerce-checkout-manager' ); ?></button>
			</span>
		</form>
	</p>

	<!-- Edit Order: File upload -->
	<script type="text/javascript">
	jQuery(document).ready(function($){(

		function post_image_content() {

			var input = document.getElementById("files_wccm"), formdata = false;

			if(window.FormData) {
				formdata = new FormData();
				document.getElementById("files_button_wccm").style.display = "none";
			}

			input.addEventListener("change", function (evt) {

				$(".wccm_results").html("Uploading, please wait....");

				var i = 0, len = this.files.length, img, reader, file;

				for ( ; i < len; i++ ) {
					file = this.files[i];
					if (formdata) {
						formdata.append("files_wccm[]",file); 
					}
				}

				if(formdata) {
					$.ajax({
						url: "<?php echo admin_url('/admin-ajax.php?action=wccs_upload_file_func&order_id='.$post->ID.'&name=files_wccm'); ?>",
						type: "POST",
						data: formdata,
						processData: false,
						contentType: false,
						success: function (res) {
							$('#files_wccm').show();
							$(".wccm_results").html("Files Uploaded Successfully.");
							/* $(".wccm_results").html(res); */

							$.ajax({
								url: '<?php echo admin_url('/post.php?post='.$post->ID.'&action=edit'); ?>',
								data: {},
								success: function (data) {
									$("div#product_images_container").html($(data).find("div#product_images_container"));
									$(".wccm_results").html("Files Uploaded Successfully.");
									/* $(".wccm_results").html(data); */
								},
								dataType: 'html'
							});
						}
					});
				}
			}, false);
		}

	());

	});
	</script>

	<input type="button" id="wccm_save_order_submit" class="button button-primary" value="<?php _e( 'Save Changes', 'woocommerce-checkout-manager' ); ?>">
	<div class="wccm_results"></div>
	<div class="clear"></div>

</div>
<!-- .woocommerce_order_items_wrapper -->
<?php

}

function wooccm_js_str( $s ) {

	return '"' . addcslashes($s, "\0..\37\"\\") . '"';

}

function wooccm_js_array( $array ) {

	$temp = array_map('wooccm_js_str', $array);
	return '[' . implode(',', $temp) . ']';

}

// front end for user
function wccs_file_uploader_front_end( $order_id ) {

	global $wpdb, $thepostid, $theorder, $woocommerce, $post;

	$order = new WC_Order($order_id);

	$options = get_option( 'wccs_settings' );
	$length = (empty( $options['checkness']['file_upload_number'])) ? 'this.files.length' : $options['checkness']['file_upload_number'];

	$file_types = explode(",", $options['checkness']['file_types']);
	$number_of_types = count($file_types);

	$prefix = 'wc-';

	if( empty($options['checkness']['upload_os']) || ($order->post_status == $prefix.$options['checkness']['upload_os']) ) {

		$post_type = 'attachment';
		$upload_dir = wp_upload_dir();
		$args = array(
			'post_type' => $post_type,
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $order_id
		);
?>

<script type="text/javascript">
jQuery(document).ready(function($) {

	$('#wccm_save_order_submit').click(function() {
		$(".wccm_results").html("Deleting files, please wait....");
		var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
			data = {
			action: 'update_attachment_wccm',
			product_image_gallery : $('#product_image_gallery').val(),
			wccm_default_keys_load : $('#wccm_default_keys_load').val()
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			$(".wccm_results").html(response);
		});
	});

});
</script>

<?php wp_enqueue_style( 'wccm_upload_file_style', plugins_url( 'includes/classes/file_upload/file_editing_table.css', WOOCCM_RELPATH ) ); ?>

<h2><?php echo (empty($options['checkness']['upload_title'])) ? 'Order Uploaded Files' : esc_attr($options['checkness']['upload_title']); ?></h2>
<div class="woocommerce_order_items_wrapper front_end">
	<table class="woocommerce_order_items front_end">

		<thead>
			<tr>
				<th style="width:15%;text-align: center;"><?php _e('Attachment ID','woocommerce-checkout-manager'); ?></th>
				<th style="width:12%"><?php _e('Image','woocommerce-checkout-manager'); ?></th>
				<th style="width:10%"><?php _e('Action','woocommerce-checkout-manager'); ?></th>
				<th style="width:12%"><?php _e('Dimensions','woocommerce-checkout-manager'); ?></th>
				<th style="width:8%"><?php _e('Extension','woocommerce-checkout-manager'); ?></th>
				<th style="width:4%"><?php _e('Link','woocommerce-checkout-manager'); ?></th>
				<th style="width:30%;text-align: center;"><?php _e('Name','woocommerce-checkout-manager'); ?></th>
			</tr>
		</thead>

		<tbody class="product_images front_end">
<?php
		$attachment_args = get_posts( $args );	
	
		if ($attachment_args) {
			foreach($attachment_args as $attachment) {
				$array[] = $attachment->ID;
			}
			$default_wccm_values = implode(',',$array);
			$product_image_gallery = implode(',',$array);
		}

		$attachments = array_filter( explode( ',', $product_image_gallery ) );
		if( $attachments ) {
			foreach( $attachments as $attachment_id ) {

				$image_attributes = wp_get_attachment_url( $attachment_id );
				$image_attributes2 = wp_get_attachment_image_src( $attachment_id );
				$filename = basename($image_attributes);
				$wp_filetype = wp_check_filetype($filename);

				$value_declear = array_diff(explode( ',',$default_wccm_values), explode( ',',$attachment_id));

				echo '
		<tr class="image wccm_filesli wccmv_' . esc_attr( $attachment_id ) . '">

			<td style="display:none;">
				<script type="text/javascript">

					jQuery(document).ready(function(){

					jQuery(".wccmx_' . esc_attr( $attachment_id ) . '").click(function(){
						jQuery(".wccmv_' . esc_attr( $attachment_id ) . '").hide();
						jQuery("#product_image_gallery").val(jQuery("#product_image_gallery").val().replace("'.esc_attr( $attachment_id ).'", ""));

					});
				});

				</script>
			</td>
			<td>'.$attachment_id.'</td>
			<td>'.wp_get_attachment_link( $attachment_id, '' , false, false, wp_get_attachment_image( $attachment_id, array(75,75), true ) ).'</td>
			<td>
				<a class="delete tips wccm_delete wccmx_' . esc_attr( $attachment_id ) . '" data-tip="' . __( 'Delete image', 'woocommerce' ) . '">' . __( 'Delete', 'woocommerce' ) . '</a>
			</td>
			<td>';
				if( $image_attributes2[1] == '' ) {
					echo '-';
				} else {
					echo $image_attributes2[1].' x '.$image_attributes2[2];
				}
				echo '
			</td>
			<td>'.$wp_filetype['ext'].'</td>
			<td>'.wp_get_attachment_link( $attachment_id, '' , false, false, 'Link' ).'</td>
			<td>'.preg_replace( '/\.[^.]+$/', '', $filename).'</td>
		</tr>';
			}
		} else {
			echo '
		<tr>
			<td colspan="7">' . __( 'No uploaded files have been assigned to this Order.', 'woocommerce-checkout-manager' ) . '</td>
		</tr>
';
		}
?>
		</tbody>
	</table>
	<input type="hidden" class="wccm_add_to_list" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_image_gallery ); ?>" />
	<input type="hidden" id="wccm_default_keys_load" name="wccm_default_keys_load" value="<?php echo esc_attr($default_wccm_values); ?>" />
	<!-- .woocommerce_order_items -->
</div>
<!-- .woocommerce_order_items_wrapper -->

<button type="button" id="wccm_save_order_submit" class="file_upload_delete wooccm-btn wooccm-btn-danger delete">Confirm Delete</button>

<span id="wccm_uploader_select">
	<input type="file" style="display:none;" name="files_wccm" id="files_wccm" multiple />
	<button type="button" class="file_upload_account wooccm-btn wooccm-btn-primary start" id="files_button_wccm">Upload Files</button>
</span>
<!-- #wccm_uploader_select -->

<div class="wccm_results front_end"></div>	

<div class="clear"></div>
<?php
		// script for uploading the files
		echo '
<!-- Edit Order: File upload -->
<script type="text/javascript">
jQuery(document).ready(function($){

	(function post_image_content() {

		var input = document.getElementById("files_wccm"), formdata = false; 

		$("#files_button_wccm").click( function(){
			$("#wccm_uploader_select input[type=file]").click();
			return false;
		});

		if (window.FormData) {
			formdata = new FormData();
		}

		input.addEventListener("change", function (evt) {
			$("#wccm_uploader_select").block({message: null, overlayCSS: {background: "#fff url(" + woocommerce_params.plugin_url + "/assets/images/ajax-loader.gif) no-repeat center", opacity: 0.6}});

			$("#wccm_uploader_select").block({message: null, overlayCSS: {background: "#fff url(" + woocommerce_params.ajax_loader_url + ") no-repeat center", opacity: 0.6}});

			var length = '.$length.';
			var file_array = '.wooccm_js_array($file_types).';
			var wooempt = '.$file_types.';

			for ( i = 0; i < length; i++ ) {
				file = this.files[i];
				for(x=0; x < '.$number_of_types.'; x++){
					if( !wooempt || file.type.match(file_array[x])  ) {
						if (formdata) {
							formdata.append("files_wccm[]",file); 
						}
					}
				}
			}

					if (formdata) {
						$.ajax({
							url: "'.admin_url('/admin-ajax.php?action=wccs_upload_file_func&order_id='.$order_id.'&name=files_wccm').'",
							type: "POST",
							data: formdata,
							processData: false,
							contentType: false,
							success: function (res) {
								$("#files_wccm").show();

							$.ajax({
								url: "'.$order->get_view_order_url().'",
								data: {},
								success: function (data) {
									$("div.woocommerce_order_items_wrapper.front_end").html($(data).find("div.woocommerce_order_items_wrapper.front_end"));
									jQuery("#wccm_uploader_select").unblock();
								},
								dataType: "html"
							});

						}
					});
				}
			}, false);
		}
	());
});
</script>';
// end script
// ------------

	}

}

function update_attachment_wccm_callback() {

	global $post, $wpdb, $woocommerce;

	$array1 = explode( ',', sanitize_text_field( isset( $_POST['wccm_default_keys_load'] ) ? $_POST['wccm_default_keys_load'] : '' ) );
	$array2 = explode( ',', sanitize_text_field( isset( $_POST['product_image_gallery'] ) ? $_POST['product_image_gallery'] : '' ) );
	$attachment_ids = array_diff( $array1, $array2 );

	if( isset( $_POST['wccm_default_keys_load'] ) ) {
		if( !empty( $attachment_ids ) ) {
			foreach( $attachment_ids as $key => $values ) {
				wp_delete_attachment( $attachment_ids[$key] );
			}
		}
		echo __('Deleted Successfully.','woocommerce-checkout-manager');
	}
	die();

}
add_action( 'wp_ajax_update_attachment_wccm', 'update_attachment_wccm_callback' );
add_action( 'wp_ajax_nopriv_update_attachment_wccm', 'update_attachment_wccm_callback' );
?>