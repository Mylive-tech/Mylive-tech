<?php
/**
 * Function to manage galleries.
 * @author Praveen Rajan
 */
?>
<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
	die('You are not allowed to call this page directly.'); 

//Loads WP default thickbox scripts
wp_enqueue_script('thickbox');
wp_enqueue_style('thickbox'); 

CvgCore::upgrade_plugin();

$title = __('Manage Video Gallery');

//Section to delete list of galleries
if(isset($_POST['TB_gallerylist']) && !empty($_POST['TB_gallerylist'])) {
	
	// wp_nonce_field('cvg_manage_delete_gallery_nonce','cvg_manage_delete_gallery_nonce_csrf');
	if ( check_admin_referer( 'cvg_manage_delete_gallery_nonce', 'cvg_manage_delete_gallery_nonce_csrf' ) ) {
		$gids = explode(',', $_POST['TB_gallerylist']);
		foreach($gids as $gid) {
			CvgCore::delete_video_gallery($gid);
			videoDB::delete_gallery($gid);
		}
		_e('<div class="clear"><div class="wrap"><div id="message" class="updated fade below-h2"><p>Galleries deleted successfully.</p></div></div></div>');
	}
}

//Section to delete a single gallery
if(isset($_POST['TB_gallerysingle']) && !empty($_POST['TB_gallerysingle'])) {
	
	// wp_nonce_field('cvg_manage_delete_single_gallery_nonce','cvg_manage_delete_single_gallery_nonce_csrf');
	if ( check_admin_referer( 'cvg_manage_delete_single_gallery_nonce', 'cvg_manage_delete_single_gallery_nonce_csrf' ) ) { 
		$gid = $_POST['TB_gallerysingle'];
		CvgCore::delete_video_gallery($gid);
		videoDB::delete_gallery($gid);
		_e('<div class="clear"><div class="wrap"><div id="message" class="updated fade below-h2"><p>Gallery ' . $gid . ' deleted successfully.</p></div></div></div>');
	}
}

//Build the pagination for more than 25 galleries
if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
	$_GET['paged'] = 1;

$options = get_option('cvg_settings');
$per_page = $options['max_cvg_gallery'];	
$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
if ( empty($pagenum) )
	$pagenum = 1;

/*Start and end page settings for pagination.*/
$start_page = ($pagenum - 1) * $per_page;
$end_page = $start_page + $per_page;
	
$total_num_pages = count(videoDB::find_all_galleries());

$total_value = ceil($total_num_pages / $per_page);
$defaults = array(
				'base' => add_query_arg( 'paged', '%#%' ), // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
				'format' => '?paged=%#%', // ?page=%#% : %#% is replaced by the page number
				'total' => $total_value,
				'current' => $pagenum,
				'show_all' => false,
				'prev_next' => true,
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'end_size' => 1,
				'mid_size' => 2,
				'type' => 'plain',
				'add_fragment' => ''
			);
$page_links = paginate_links( $defaults );			
				
$gallerylist = videoDB::find_all_galleries('gid', 'asc', TRUE ,$per_page, $start_page);
?>
<script type="text/javascript"> 

	function checkAll(form)	{
		for (i = 0, n = form.elements.length; i < n; i++) {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].name == "doaction[]") {
					if(form.elements[i].checked == true)
						form.elements[i].checked = false;
					else
						form.elements[i].checked = true;
				}
			}
		}
	}
	function getNumChecked(form){
		var num = 0;
		for (i = 0, n = form.elements.length; i < n; i++) {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].name == "doaction[]")
					if(form.elements[i].checked == true)
						num++;
			}
		}
		return num;
	}
	function checkSelected() {
	
		var numchecked = getNumChecked(document.getElementById('editgalleries'));
		if(numchecked < 1) { 
			alert('<?php echo esc_js(__('No video gallery selected')); ?>');
			return false; 
		} 
		actionId = jQuery('#bulkaction').val();
		switch (actionId) {
			case "no_action":
				return true;	
				break;
			case "delete_gallery":
				showDialog('delete_gallery', 100);
				return false;
				break;
		}
	}
	
	function showDialog( windowId, height ) {
		var form = document.getElementById('editgalleries');
		var elementlist = "";
		for (i = 0, n = form.elements.length; i < n; i++) {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].name == "doaction[]")
					if(form.elements[i].checked == true)
						if (elementlist == "")
							elementlist = form.elements[i].value
						else
							elementlist += "," + form.elements[i].value ;
			}
		}
		jQuery("#" + windowId + "_bulkaction").val(jQuery("#bulkaction").val());
		jQuery("#" + windowId + "_deletelist").val(elementlist);
		tb_show("", "#TB_inline?width=200&height=" + height + "&inlineId=" + windowId + "&modal=true", false);
	}

	function showDialogDelete(id) {
		jQuery("#delete_gallery_single_value").val(id);
		tb_show("", "#TB_inline?width=200&height=100&inlineId=delete_gallery_single&modal=true", false);
	}
	
	function showDialogShortCode(id, gallery_name) {
		jQuery("#shortcode_gallery_single_value").val(id);
		jQuery('#generated_shortcode_gallery').val("Copy and use Shortcode");
		tb_show("Generate Shortcode for Gallery - " + gallery_name, "#TB_inline?width=400&height=130&inlineId=shortcode_gallery_single&modal=false", false);
	}
	
</script>

<div class="wrap">
	<div class="icon32" id="icon-video"><br></div>
	<h2><?php echo esc_html( __($title) ); ?></h2>
	<div class="clear"></div>

	<form id="editgalleries" class="nggform" method="POST" action="<?php echo admin_url('admin.php?page=cvg-gallery-manage&paged=' . $_GET['paged']); ?>" accept-charset="utf-8">
		<div class="tablenav">
			<div class="alignleft actions">
				<div style="float:left;">
					<?php if ( function_exists('json_encode') ) : ?>
					<select name="bulkaction" id="bulkaction">
						<option value="no_action" ><?php _e("No action"); ?></option>
						<option value="delete_gallery" ><?php _e("Delete"); ?></option>
					</select>
					<input name="showThickbox" class="button-secondary" type="submit" value="<?php _e('Apply'); ?>" onclick="if ( !checkSelected() ) return false;" />
					<?php endif; ?>
				</div>	
				<div style="float:left;margin-left:10px;margin-top:-2px;">
					<a href="<?php echo admin_url('admin.php?page=cvg-gallery-add'); ?>" class="button-secondary action"><?php _e('Add new gallery') ?></a>
				</div>	
			</div>
			<?php if ( $page_links ) { ?>
				<div class="tablenav-pages">
					<?php
						$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
											number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
											number_format_i18n( min( $pagenum * $per_page, $total_num_pages ) ),
											number_format_i18n( $total_num_pages ),
											$page_links
											);
						echo $page_links_text;
					?>
				</div>
			<?php }?>
		</div>
		<table class="widefat" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" class="column-cb" >
					<input type="checkbox" onclick="checkAll(document.getElementById('editgalleries'));" name="checkall"/>
				</th>
				<th scope="col" ><?php _e('ID'); ?></th>
				<th scope="col" ><?php _e('Title'); ?></th>
				<th scope="col" ><?php _e('Description'); ?></th>
				<th scope="col" ><?php _e('Author'); ?></th>
				<th scope="col" ><?php _e('Quantity'); ?></th>
				<th scope="col" ><?php _e('Action'); ?></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th scope="col" class="column-cb" >
					<input type="checkbox" onclick="checkAll(document.getElementById('editgalleries'));" name="checkall"/>
				</th>
				<th scope="col" ><?php _e('ID'); ?></th>
				<th scope="col" ><?php _e('Title'); ?></th>
				<th scope="col" ><?php _e('Description'); ?></th>
				<th scope="col" ><?php _e('Author'); ?></th>
				<th scope="col" ><?php _e('Quantity'); ?></th>
				<th scope="col" ><?php _e('Action'); ?></th>
			</tr>
			</tfoot>            
			<tbody>
				<?php
				if($gallerylist) {
					
					$index = number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 );
					foreach($gallerylist as $gallery) {
						$class = ( !isset($class) || $class == 'class="alternate"' ) ? '' : 'class="alternate"';
						$gid = $gallery->gid;
						$name = (empty($gallery->title) ) ? $gallery->name : $gallery->title;
						$author_user = get_userdata( (int) $gallery->author );
						?>
						<tr id="gallery-<?php echo $gid ?>" <?php echo $class; ?> >
							<th scope="row" class="cb column-cb">
								<input name="doaction[]" type="checkbox" value="<?php echo $gid ?>" />
							</th>
							<td scope="row"><?php echo $gid; ?></td>
							<td>
								<a href="<?php echo admin_url( 'admin.php?page=cvg-gallery-manage&gid=' . $gid)?>" class='edit' title="<?php _e('Edit'); ?>" >
									<?php echo $name; ?>
								</a>
							</td>
							<td><?php echo $gallery->galdesc; ?>&nbsp;</td>
							<td><?php echo $author_user->display_name; ?></td>
							<td><?php echo $gallery->counter; ?></td>
							<td>
								<a onclick="showDialogDelete(<?php echo $gid ?>);" href="#" class="delete"><?php _e('Delete'); ?></a> | 
								<a onclick="showDialogShortCode(<?php echo $gid ?>, '<?php echo $name;?>');" href="#"><?php _e('Shortcode'); ?></a>
							</td>
						</tr>
						<?php
					}
				} else {
					echo '<tr><td colspan="7" align="center"><strong>' . __('No entries found') . '</strong></td></tr>';
				}
				?>			
			</tbody>
		</table>
		</form>
		
		<!-- #gallery_delete -->
		<div id="delete_gallery" style="display: none;" >
			<form id="form-delete-gallery" method="POST" accept-charset="utf-8" action="<?php echo admin_url('admin.php?page=cvg-gallery-manage'); ?>">
				<input type="hidden" id="delete_gallery_deletelist" name="TB_gallerylist" value="" />
				<input type="hidden" id="delete_gallery_bulkaction" name="TB_bulkaction" value="" />
				<input type="hidden" name="page" value="manage-galleries" />
				<table width="100%" border="0" cellspacing="3" cellpadding="3" >
					<tr valign="top">
						<td><strong><?php _e('Delete Gallery?'); ?></strong></td>
					</tr>
				  	<tr align="center">
				  		<?php wp_nonce_field('cvg_manage_delete_gallery_nonce','cvg_manage_delete_gallery_nonce_csrf'); ?>
				    	<td colspan="2" class="submit">
				    		<input class="button-primary" type="submit" name="TB_DeleteGallery" value="<?php _e('OK'); ?>" />
				    		&nbsp;
				    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel'); ?>&nbsp;" onclick="tb_remove()"/>
				    	</td>
					</tr>
				</table>
			</form>
		</div>
		<!-- #gallery_delete -->
		
		<!-- #gallery_delete_single -->
		<div id="delete_gallery_single" style="display: none;" >
			<form id="form-delete-gallery_single" method="POST" accept-charset="utf-8" action="<?php echo admin_url('admin.php?page=cvg-gallery-manage') ; ?>">
				<input type="hidden" id="delete_gallery_single_value" name="TB_gallerysingle" value="" />
				<table width="100%" border="0" cellspacing="3" cellpadding="3" >
					<tr valign="top">
						<td><strong><?php _e('Delete Gallery?'); ?></strong></td>
					</tr>
				  	<tr align="center">
				  		<?php wp_nonce_field('cvg_manage_delete_single_gallery_nonce','cvg_manage_delete_single_gallery_nonce_csrf'); ?>
				    	<td colspan="2" class="submit">
				    		<input class="button-primary" type="submit" name="TB_DeleteSingle" value="<?php _e('OK'); ?>" />
				    		&nbsp;
				    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel'); ?>&nbsp;" onclick="tb_remove()"/>
				    	</td>
					</tr>
				</table>
			</form>
		</div>
		<!-- #gallery_delete_single -->
		
		<!-- #Shortcode generator -->
		<div id="shortcode_gallery_single" style="display: none;" >
				<script type="text/javascript">
					
					function generateShortCode() {
						
						var galleryId = jQuery('#shortcode_gallery_single_value').val();
						var galleryLimit = jQuery('#shortcode_generate_gallery_limit').val();
						
						var selection = jQuery('input[name=showtypegallery_generate]:checked', "").val();
						var mode_temp = "";
						
						if(selection == "embed"){
							mode_temp = "mode='playlist'";
						}
						
						var mode_temp = '';
						if(selection != 'playlist')
							mode_temp = "limit='" + galleryLimit + "'";
		
						var shortcode_text;
						if (galleryId != 0 )
							shortcode_text = "[cvg-gallery galleryId='" + galleryId + "' mode='" + selection + "' " + mode_temp  +  " /]";
		
						
						jQuery('#generated_shortcode_gallery').val(shortcode_text);
					}
					
				</script>
				
				<input type="hidden" id="shortcode_gallery_single_value" value="" />
				<table width="100%" border="0" cellspacing="2" cellpadding="2" >
					<tr valign="top">
					<th align="left"><?php _e('Show as') ?></th>
					<td>
						<label><input name="showtypegallery_generate" type="radio" value="showcase" checked="checked" /> <?php _e('Showcase') ;?></label>&nbsp;&nbsp;&nbsp;&nbsp;
	            		<label><input name="showtypegallery_generate" type="radio" value="slideshow" /> <?php _e('Slideshow') ;?></label>&nbsp;&nbsp;&nbsp;&nbsp;
	            		<label><input name="showtypegallery_generate" type="radio" value="playlist" /> <?php _e('Playlist') ;?></label>&nbsp;&nbsp;
     				</td>
    			    </tr>
    			    <tr valign="top">
						<td><strong><?php _e('Limit: '); ?></strong></td>
						<td>
							<input type="text" size="5" maxlength="5" name="width" id="shortcode_generate_gallery_limit" value="5"/>
						</td>
					</tr>
				  	<tr align="right">
				  		<td colspan="2">
				  			<input type="text" style="width:100%;" id="generated_shortcode_gallery" readonly/>
				  		</td>
				    	<td colspan="2" class="submit">
				    		<input class="button-primary" onclick="generateShortCode();" type="button" id="generate_shortcode_video" value="<?php _e('Generate'); ?>" />
				    	</td>
					</tr>
				</table>
			</form>
		</div>
		<!-- #Shortcode generator -->
			
</div><!-- wrap -->