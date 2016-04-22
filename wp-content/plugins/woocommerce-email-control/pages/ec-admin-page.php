<?php
global $wp_scripts, $woocommerce, $woocommerce, $current_user, $email_control_template_collection;

$presentation_state = ( isset($_REQUEST["ec_in_popup"]) ) ? "pe-in-popup" : "pe-in-admin-page" ;
?>

<?php if ( isset( $_REQUEST["ec_in_popup"] ) ) { ?>
	<style>
	html.wp-toolbar { padding-top: 0 !important; }
	html, body { overflow: hidden !important; }
	</style>
<?php } ?>

<div class="wp-core-ui ec ec-admin-page <?php echo $presentation_state ?>">
	
	<table class="" border="1" cellpadding="0" cellspacing="0">
		<tr>
			<td class="left-column">
				
				<!-- Left Column -->
				
				<?php
				// Load WC Emails
				// ----------------------------------------
				
				// Load mailer
				if ( class_exists('WC') ) {
					$mailer = WC()->mailer();
					$mails = $mailer->get_emails();
				}
				else{
					$mailer = $woocommerce->mailer();
					$mails = $mailer->get_emails();
				}
				
				
				$show_type = isset($_REQUEST["ec_email_type"]) ? $_REQUEST["ec_email_type"] : current($mails)->id ;
				
				
				// Load WC Orders
				// ----------------------------------------
				
				$limit_orders = 800;
				
				$order_collection = new WP_Query(array(
					'post_type'			=> 'shop_order',
					'post_status'		=> array_keys( wc_get_order_statuses() ),
					'posts_per_page'	=> $limit_orders,
				));
				
				$order_collection = $order_collection->posts;
				$latest_order = ( count( $order_collection ) ) ? current( $order_collection )->ID : FALSE ;
				
				$show_order = isset($_REQUEST["ec_email_order"]) ? $_REQUEST["ec_email_order"] : $latest_order ;
				
				$src_url = "";
				$src_url .= 'admin.php?';
				$src_url .= 'page=woocommerce_email_control';
				$src_url .= '&ec_render_email=true';
				$src_url .= '&ec_email_type=' . $show_type;
				
				if ( $show_order ) {
					$src_url .= '&ec_email_order='.$show_order;
				}
				
				if ( isset( $_REQUEST["ec_in_popup"] ) ) {
					$src_url .= '&ec_in_popup=true';
				}
				?>
				
				<div class="ec-admin-panel ec-admin-panel-controls">
					
					<div class="main-controls-top-button-row">
						<?php if ( isset( $_GET['ec_is_popup'] ) ): ?>
							<a class="button close-button" href="#"><?php _e("Close", "email-control"); ?></a>
						<?php else: ?>
							<?php
							$backlink = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : admin_url() ;
							if ( strrpos($backlink,'login')!= -1 ) $backlink = admin_url() ;
							?>
							<a class="button-primary exit-button" href="<?php echo $backlink; ?>">
								<span class="dashicons dashicons-arrow-left ec-back-icon"></span>
								<?php _e("Back", "email-control"); ?>
							</a>
						<?php endif; ?>
					</div>
					
					<form class="main-controls-form" id="render_email" name="render_email" data-name="Render Email"  action="<?php echo wp_nonce_url( admin_url( $src_url ), 'email-control'); ?>" target="my-iframe" method="post" >
						<div class="main-controls">
							
							<div class="main-controls-element main-controls-element-heading-block">
								<div class="heading-first">
									<h2><?php _e("Email Customizer","email-control"); ?></h2>
								</div>
							</div>
							
							<div class="main-controls-element">
								<label class="controls-label">
									<?php _e("Email to show","email-control") ; ?> <span class="help-icon help_tip_new" data-tip="<?php _e( 'Choose which email to preview or send.', 'email-control' ); ?>" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</label>
								<div class="controls-field">
									<div class="controls-inner-row">
										<select class="w-select control-dropdown" id="ec_email_type" name="ec_email_type">
											<option value="">
												Select one...
											</option>
											<?php
											//Customer_Invoice
											if ( !empty( $mails ) ) {
												foreach ( $mails as $mail ) {
													?>
													<option value="<?php echo $mail->id ?>" <?php echo ($show_type == $mail->id) ? "selected" : "" ; ?> >
														<?php echo ucwords($mail->title); ?>
													</option>
													<?php
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>
							
							<?php
							global $ec_email_templates;
							
							if ( isset($ec_email_templates) && is_array($ec_email_templates) ) :
								?>
								<div class="main-controls-element">
									<label class="controls-label">
										<?php _e("Template to show","email-control") ; ?> <span class="help-icon help_tip_new" data-tip="<?php _e( 'Edit the email.', 'email-control' ); ?>" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
									</label>
									<div class="controls-field">
										<div class="controls-inner-row">
											<select class="w-select control-dropdown" id="ec_email_template" name="ec_email_template">
												<option value="woocommerce_original">
													<?php _e( 'WooCommerce (original, not editable)', 'email-control' ); ?>
												</option>
												
												<?php
												$ec_template_selected = get_option("ec_template");
												foreach ($ec_email_templates as $ec_email_template_key => $ec_email_template_args ) {
													
													$name = $ec_email_template_args['name'];
													$woocommerce_version_pass = ec_check_template_version( $ec_email_template_key );
													?>
													<option class="template-select" value="<?php echo $ec_email_template_key; ?>" <?php echo ( $ec_email_template_key == $ec_template_selected ) ? "selected" : "" ; ?> >
														<?php echo $name; ?><?php if ( ! $woocommerce_version_pass ) echo ' ' . __( '(WooCommerce update required)', 'email-control' ) ?>
													</option>
													<?php
												}
												?>
											</select>
											<input type="hidden" name="ec_email_template_active" id="ec_email_template_active" class="ec_email_template_active" value="<?php echo $ec_template_selected; ?>" >
											<input type="hidden" name="ec_email_template_preview" id="ec_email_template_preview" class="ec_email_template_preview">
										</div>
										<div class="controls-inner-row controls-inner-button-row ec_template_button_row" id="template-commit">
											<a class="button" id="ec_cancel_email_template" href="#"><i class="fa fa-reply"></i>&nbsp; <?php _e("Don't Use", "email-control"); ?></a>
											<a class="button-primary" id="ec_save_email_template" href="#"><i class="fa fa-check"></i>&nbsp; <?php _e("Use", "email-control"); ?></a>
										</div>
									</div>
								</div>
								<?php
							endif;
							?>
							
							<div class="main-controls-element" id="ec_edit_content_controls">
								<label class="controls-label">
									<?php _e("Customize Template","email-control") ; ?> <span class="help-icon help_tip_new" data-tip="<?php _e( 'Customize the email that you are showing.', 'email-control' ); ?>" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</label>
								<div class="controls-field">
									<div class="controls-inner-row">
										<a class="button" id="ec_edit_content" href="#">
											<i class="fa fa-pencil"></i> <?php _e("Customize", "email-control"); ?>
										</a>
									</div>
								</div>
								<?php if ( false ) : ?>
									<div class="get_templates button"><?php _e("Get Customized Templates", "email-control"); ?></div>
									<div class="get_templates_flyout">
										<!-- More Templates -->
									</div>
								<?php endif; ?>
							</div>
							
							<div class="main-controls-element">
								<label class="controls-label">
									<?php _e("Order to show","email-control") ; ?> <span class="help-icon help_tip_new" data-tip="<?php _e( 'Choose which order to use to populate the email template preview.', 'email-control' ); ?>" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</label>
								<div class="controls-field">
									<div class="controls-inner-row">
										
										<select class="w-select control-dropdown" id="ec_email_order" name="ec_email_order">
											
											<?php
											if ( count( $order_collection ) ) {
												?>
												<option value="">
													<?php _e("Select one...", "email-control"); ?>
												</option>
												<?php
											}
											else {
												?>
												<option value="">
													<?php _e("There are no orders to preview...", "email-control"); ?>
												</option>
												<?php
											}
											
											// Show the orders.
											foreach ($order_collection as $order_item) {
												
												$order = new WC_Order( $order_item->ID );
												?>
												<!-- <option value="<?php echo "['" . $order_item->ID . "','" . $order->billing_email . "']" ?>" <?php echo ( $order_item->ID == $show_order ) ? "selected" : "" ; ?> > -->
												<option value="<?php echo $order_item->ID ?>" data-order-email="<?php echo $order->billing_email ?>" <?php echo ( $order_item->ID == $show_order ) ? "selected" : "" ; ?> >
													<?php echo $order->get_order_number() ?> - <?php echo $order->billing_first_name ?> <?php echo $order->billing_last_name ?> (<?php echo $order->billing_email ?>)
												</option>
												<?php
											}
											// If more than the orders limit then let the user know.
											if ( $limit_orders <= count( $order_collection ) ) {
												?>
												<option><?php printf( __( '...Showing the most recent %u orders', 'email-control' ), $limit_orders ); ?></option>
												<?php
											}
											?>
										</select>
									</div>
								</div>
							</div>
							
							<div class="main-controls-element">
								<label class="controls-label">
									<?php _e("Show Header Info", "email-control") ; ?> <span class="help-icon help_tip_new" data-tip="<?php _e( 'Display the email header information.', 'email-control' ); ?>" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</label>
								<div class="controls-field">
									<div class="controls-inner-row">
										
										<label for="header_info">
											<?php
											$field_default = "off";
											$field_value = get_user_meta( $current_user->ID, "header_info_userspecifc", true);
											$field_value = ($field_value)? $field_value : $field_default;
											
											if ( $field_value == "on" ) $header = true;
											else $header = false;
											?>
											
											<input type="checkbox" class="header_info_userspecifc" <?php if ( $field_value == "on" ) echo "checked"; ?> name="header_info_userspecifc" value="on" /> Header Info
										</label>
									
									</div>
								</div>
							</div>
							
							<div class="main-controls-element">
								<?php
								//Load order for Email Customizer Send
								$order = new WC_Order( $show_order );
								?>
								<label class="controls-label">
									<?php _e("Send a Test", "email-control") ; ?> <span class="help-icon help_tip_new" data-tip="<?php _e( 'Send a test email to any address. Use a comma separated list to send to multiple addresses', 'email-control' ); ?>" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</label>
								<div class="controls-field">
									<div class="controls-inner-row">
										<input type="text" class="" id="ec_send_email" name="ec_send_email" value="<?php echo $order->billing_email ?>" placeholder="somone@somewhere.com, ..." />
										<button id="send_test" class="button send_test" name="send_test" type="button"><i class="fa fa-envelope"></i></button>
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" class="" id="ec_approve_preview" name="ec_approve_preview" />
					</form>
				</div>
				
				
				<?php
				global $ec_email_templates;
					
				if ( isset($ec_email_templates) && is_array($ec_email_templates) ) :
					?>
					<div class="ec-admin-panel ec-admin-panel-edit-content">
						
						<div class="edit-top-controls">
							<span class="edit-top-control close_settings" id="close_edit_settings" >
								<i class="fa fa-chevron-left"></i>
							</span>
							<span class="edit-top-control hide_settings">
								<i class="fa fa-eye"></i>
							</span>
						</div>
						
						
						<?php
						// Add so the 'all' fields are shown
						$ec_email_types_for_settings = $mails;
						$ec_email_types_for_settings[] = (object)array('id'=>'all');
						
						foreach ($ec_email_templates as $ec_email_template_key => $ec_email_template_args ) {
							
							$ec_email_template_id			= $ec_email_template_key;
							$ec_email_template_name			= $ec_email_template_args["name"];
							
							if ( !empty($ec_email_types_for_settings) && ec_get_settings($ec_email_template_id) ) {
								
								$form_id	= "ec_settings_form_" . $ec_email_template_id;
								
								$form_class	= "ec_settings_form ";
								$form_class	.= "ec_settings_form_" . $ec_email_template_id . " ";
								?>
								<form id="<?php echo $form_id ?>" class="<?php echo $form_class ?>" >
									
									<input type="button" id="save_edit_settings" class="button-primary save_edit_settings" value='Saved' disabled />
									
									<?php
									foreach ( $ec_email_types_for_settings as $mail ) {
										$ec_email_template_kind = $mail->id;
										
										if ( ec_get_settings( $ec_email_template_id, array( 'group' => $ec_email_template_kind ) ) ) {
											
											$ec_email_template_kind	= $ec_email_template_kind;
											$form_sub_id			= "ec_settings_form_sub_" . $ec_email_template_id . "_" . $ec_email_template_kind;
											
											$form_sub_class			= "ec_settings_form_sub ";
											$form_sub_class			.= "ec_settings_form_sub_" . $ec_email_template_id . " ";
											$form_sub_class			.= "ec_settings_form_sub_" . $ec_email_template_id . "_" . $ec_email_template_kind . " ";
											?>
											
											<div id="<?php echo $form_sub_id ?>" class="<?php echo $form_sub_class ?>" >
												
												<?php EC_Settings::output_fields( ec_get_settings($ec_email_template_id, array( 'group' => $ec_email_template_kind ) ) ); ?>
												
											</div>
											
											<?php
										}
									}
									?>
									
									<!--
									<input type="hidden" name="ec_email_type" value="<?php echo $ec_email_template_kind ?>" >
									-->
									<input type="hidden" name="ec_email_id" value="<?php echo $ec_email_template_id ?>" >
									<input type="hidden" name="ec_action" value="yes" >
									
									<div class="main-controls-element forminp-tags ec-allowed-tags">
										<label class="controls-label">
											<?php _e('Allowed Shortcodes:', 'email-control') ?>
											<span class="help-icon help_tip_new" data-tip="<?php echo esc_attr(__('Copy & Paste any of these [shortcodes] to use dynamic text in your text.', 'email-control')) ?>" >&nbsp;</span>
										</label>
										<div class="controls-field">
											<div class="controls-inner-row">
												[ec_firstname] [ec_lastname] [ec_email] [ec_order] [ec_pay_link] [ec_customer_note] [ec_user_login] [ec_account_link] [ec_user_password] [ec_reset_password_link] [ec_login_link] [ec_site_link] [ec_site_name]
											</div>
										</div>
									</div>
									
								</form>
								<?php
							}
						}
						?>
						
					</div>
				<?php endif; ?>
					
				<!-- Left Column -->
				
			</td>
			
			<td class="main-content">
				
				<!-- Main Content -->
				
				<iframe id="preview-email-template-iframe" name="my-iframe" width="100%" border="0" scrolling="no" src="<?php echo wp_nonce_url( admin_url( $src_url ), 'email-control'); ?>"></iframe>
				
				<!-- /Main Content -->
				
			</td>
		</tr>
	</table>

</div>

<?php
