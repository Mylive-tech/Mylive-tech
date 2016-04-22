<form method="post" id="visual-form-builder-new-form" action="">
	<input name="action" type="hidden" value="create_form" />
    <?php wp_nonce_field( "create_form" ); ?>
	<?php if ( current_user_can( 'vfb_create_forms' ) ) : ?>
	<h3><?php _e( 'Create a form' , 'visual-form-builder-pro'); ?></h3>

	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="form-name"><?php _e( 'Name the form' , 'visual-form-builder-pro'); ?></label></th>
				<td>
					<input type="text" autofocus="autofocus" class="regular-text required" id="form-name" name="form_title" />
					<p class="description"><?php _e( 'Required. This name is used for admin purposes.' , 'visual-form-builder-pro'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="form-email-sender-name"><?php _e( 'Your Name or Company' , 'visual-form-builder-pro'); ?></label></th>
				<td>
					<input type="text" value="" placeholder="" class="regular-text required" id="form-email-sender-name" name="form_email_from_name" />
					<p class="description"><?php _e( 'Required. This option sets the "From" display name of the email that is sent.' , 'visual-form-builder-pro'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="form-email-from"><?php _e( 'Reply-To E-mail' , 'visual-form-builder-pro'); ?></label></th>
				<td>
					<input type="text" value="" placeholder="" class="regular-text required" id="form-email-from" name="form_email_from" />
					<p class="description"><?php _e( 'Required. Replies to your email will go here.' , 'visual-form-builder-pro'); ?></p>
					<p class="description"><?php _e( 'Tip: for best results, use an email that exists on this domain.' , 'visual-form-builder-pro'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="form-email-subject"><?php _e( 'E-mail Subject' , 'visual-form-builder-pro'); ?></label></th>
				<td>
					<input type="text" value="" placeholder="" class="regular-text" id="form-email-subject" name="form_email_subject" />
					<p class="description"><?php _e( 'This sets the subject of the email that is sent.' , 'visual-form-builder-pro'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="form-email-to"><?php _e( 'E-mail To' , 'visual-form-builder-pro'); ?></label></th>
				<td>
					<input type="text" value="" placeholder="" class="regular-text" id="form-email-to" name="form_email_to[]" />
					<p class="description"><?php _e( 'Who to send the submitted data to. You can add more after creating the form.' , 'visual-form-builder-pro'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
		submit_button( __( 'Create Form', 'visual-form-builder-pro' ) );
		endif;
	?>
</form>
<h3><?php _e( 'Need more help?' , 'visual-form-builder-pro'); ?></h3>
<ol>
<li><?php _e( 'Click on the Help tab' , 'visual-form-builder-pro'); ?></li>
<li><a href="http://vfbpro.com/documentation/" target="_blank">Official Documentation</a></li>
<li><a href="http://vfbpro.com/faq" target="_blank">FAQ</a></li>
<li><a href="http://vimeo.com/user5193374/videos" target="_blank">Videos</a></li>
</ol>

<ul id="promote-vfb">
<li id="twitter"><?php _e( 'Follow me on Twitter' , 'visual-form-builder-pro'); ?>: <a href="http://twitter.com/#!/matthewmuro">@matthewmuro</a></li>
</ul>
