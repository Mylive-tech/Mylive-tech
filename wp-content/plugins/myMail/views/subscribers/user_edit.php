
<?php
if($subscriber = $this->get_by_wpid($user->ID, true)) :

	if(!current_user_can('mymail_edit_subscribers' )) return;

?>
<h3>MyMail</h3>
<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_login"><?php _e('Profile', 'mymail'); ?></label></th>
		<td>
		<a class="button" href="edit.php?post_type=newsletter&page=mymail_subscribers&ID=<?php echo $subscriber->ID ?>">
			<?php IS_PROFILE_PAGE ? _e( 'Edit my MyMail Profile', 'mymail' ) : _e( 'Edit Users MyMail Profile', 'mymail' ); ?>
		</a>
		</td>
	</tr>
</table>

<?php else :

	if(!current_user_can('mymail_add_subscribers' )) return;

 ?>
<h3>MyMail</h3>
<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_login"><?php _e('Create', 'mymail'); ?></label></th>
		<td>
		<a class="button" href="edit.php?post_type=newsletter&page=mymail_subscribers&new&wp_user=<?php echo $user->ID ?>&_wpnonce=<?php echo wp_create_nonce( 'mymail_nonce' ); ?>">
			<?php IS_PROFILE_PAGE ? _e( 'Create MyMail Subscriber', 'mymail' ) : _e( 'Create MyMail Subscriber', 'mymail' ); ?>
		</a>

		</td>
	</tr>
</table>

<?php endif; ?>
