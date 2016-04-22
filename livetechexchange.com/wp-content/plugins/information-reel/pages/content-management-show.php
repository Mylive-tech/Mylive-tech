<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
// Form submitted, check the data
if (isset($_POST['frm_IR_display']) && $_POST['frm_IR_display'] == 'yes')
{
	$did = isset($_GET['did']) ? $_GET['did'] : '0';
	
	$IR_success = '';
	$IR_success_msg = FALSE;
	
	// First check if ID exist with requested ID
	$sSql = $wpdb->prepare(
		"SELECT COUNT(*) AS `count` FROM ".WP_IR_TABLE."
		WHERE `IR_id` = %d",
		array($did)
	);
	$result = '0';
	$result = $wpdb->get_var($sSql);
	
	if ($result != '1')
	{
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist.', 'information-reel'); ?></strong></p></div><?php
	}
	else
	{
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('IR_form_show');
			
			//	Delete selected record from the table
			$sSql = $wpdb->prepare("DELETE FROM `".WP_IR_TABLE."`
					WHERE `IR_id` = %d
					LIMIT 1", $did);
			$wpdb->query($sSql);
			
			//	Set success message
			$IR_success_msg = TRUE;
			$IR_success = __('Selected record was successfully deleted.', 'information-reel');
		}
	}
	
	if ($IR_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $IR_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-post"></div>
    <h2><?php _e(WP_IR_TITLE, 'information-reel'); ?>
	<a class="add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/options-general.php?page=information-reel&amp;ac=add">
	<?php _e('Add New', 'information-reel'); ?>
	</a></h2>
    <div class="tool-box">
	<?php
		$sSql = "SELECT * FROM `".WP_IR_TABLE."` order by IR_type, IR_order";
		$myData = array();
		$myData = $wpdb->get_results($sSql, ARRAY_A);
		?>
		<script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/information-reel/pages/information-reel-setting.js"></script>
		<form name="frm_IR_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th class="check-column" scope="row"><input type="checkbox" name="IR_group_item[]" /></th>
			<th scope="col"><?php _e('Title', 'information-reel'); ?></th>
			<th scope="col"><?php _e('Description', 'information-reel'); ?></th>
			<th scope="col"><?php _e('Group', 'information-reel'); ?></th>
			<th scope="col"><?php _e('Status', 'information-reel'); ?></th>
			<th scope="col"><?php _e('Order', 'information-reel'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
            <th class="check-column" scope="row"><input type="checkbox" name="IR_group_item[]" /></th>
			<th scope="col"><?php _e('Title', 'information-reel'); ?></th>
			<th scope="col"><?php _e('Description', 'information-reel'); ?></th>
			<th scope="col"><?php _e('Group', 'information-reel'); ?></th>
			<th scope="col"><?php _e('Status', 'information-reel'); ?></th>
			<th scope="col"><?php _e('Order', 'information-reel'); ?></th>
          </tr>
        </tfoot>
		<tbody>
			<?php 
			$i = 0;
			if(count($myData) > 0 )
			{
				foreach ($myData as $data)
				{
					$IR_desc = substr(esc_html(stripslashes($data['IR_desc'])), 0, 100);
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
						<td align="left"><input type="checkbox" value="<?php echo $data['IR_id']; ?>" name="IR_group_item[]"></th>
						<td>
						<?php echo esc_html(stripslashes($data['IR_title'])); ?>
						<div class="row-actions">
						<span class="edit">
						<a title="Edit" href="<?php echo get_option('siteurl'); ?>/wp-admin/options-general.php?page=information-reel&ac=edit&did=<?php echo $data['IR_id']; ?>">
						<?php _e('Edit', 'information-reel'); ?></a> | </span>
						<span class="trash">
							<a onClick="javascript:IR_delete('<?php echo $data['IR_id']; ?>')" href="javascript:void(0);"><?php _e('Delete', 'information-reel'); ?></a>
						</span> 
						</div>
						</td>
						<td><?php echo $IR_desc; ?>...</td>
						<td><?php echo $data['IR_type']; ?></td>
						<td><?php echo $data['IR_status']; ?></td>
						<td><?php echo $data['IR_order']; ?></td>
					</tr>
					<?php 
					$i = $i+1; 
				} 
			}
			else
			{
				?><tr><td colspan="6" align="center"><?php _e('No records available.', 'information-reel'); ?></td></tr><?php 
			}
			?>
		</tbody>
        </table>
		<?php wp_nonce_field('IR_form_show'); ?>
		<input type="hidden" name="frm_IR_display" value="yes"/>
      </form>	
	  <div>
	  <h2>
		  <a class="button add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/options-general.php?page=information-reel&amp;ac=add">
		  		<?php _e('Add New', 'information-reel'); ?>
		  </a>
		  <a class="button add-new-h2" target="_blank" href="<?php echo WP_IR_FAV; ?>"><?php _e('Help', 'information-reel'); ?></a>
	  </h2>
	  </div>
		<h3><?php _e('Plugin configuration option', 'information-reel'); ?></h3>
		<ul>
			<li><?php _e('Drag and drop the widget to your sidebar.', 'information-reel'); ?></li>
		</ul>
	</div>
	<p class="description">
	<?php _e('Check official website for more information', 'information-reel'); ?>
	<a target="_blank" href="<?php echo WP_IR_FAV; ?>"><?php _e('click here', 'information-reel'); ?></a>
	</p>
</div>