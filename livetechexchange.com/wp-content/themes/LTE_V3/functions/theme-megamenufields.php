<?php
/**
 * Mega Menu custom fields.
 *

 */

 
/*-----------------------------------------------------------------------------------*/
/*	Define Metabox Fields
/*-----------------------------------------------------------------------------------*/

$prefix = 'doover_mm_';
$meta_box_mm = array(
	'id' => 'doover-mm-meta-box',
	'title' => __('Mega menu fields','doover'),
	'page' => 'megamenu',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => __('Page link','doover'),
			'desc' => __('Link to any subpage.','doover'),
			'id' => $prefix . 'link',
			'type' => 'text',
			'std' => ''
		),
	),
);


/*-----------------------------------------------------------------------------------*/
/*	Add metabox to edit page
/*-----------------------------------------------------------------------------------*/ 
function doover_mm_add_box() {
	global $meta_box_mm;
	add_meta_box($meta_box_mm['id'], $meta_box_mm['title'], 'doover_mm_show_box', $meta_box_mm['page'], $meta_box_mm['context'], $meta_box_mm['priority']);
}
add_action('admin_menu', 'doover_mm_add_box');


/*-----------------------------------------------------------------------------------*/
/*	Callback function to show fields in meta box
/*-----------------------------------------------------------------------------------*/
function doover_mm_show_box() {
	global $meta_box_mm, $post;
 	
	// Use nonce for verification
	echo '<input type="hidden" name="doover_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
 
	echo '<table class="form-table">';
 
	$iteration = 1;
	foreach ($meta_box_mm['fields'] as $field) {
		
		$style = "";
		if( $iteration!=1 ){
			$style = ' style="border-top:1px solid #DFDFDF; box-shadow: 0 1px 0 #FFFFFF;"';
		}
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);
		switch ($field['type']) {
 				
			case 'text':
				echo '<tr'. $style .'>',
					'<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#777777; line-height: 16px; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
					'<td>';
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES)), '" size="30" style="width:75%; margin-right: 20px; float:left;" />';
				break;
 
			case 'button':
				echo '<input style="float: left;" type="button" class="button" name="', $field['id'], '" id="', $field['id'], '"value="', $meta ? $meta : $field['std'], '" />';
				echo '</td></tr>';
				break;

		}
		$iteration += 1;
	}
 
	echo '</table>';
}


/*-----------------------------------------------------------------------------------*/
/*	Save data when post is edited
/*-----------------------------------------------------------------------------------*/
function doover_mm_save_data($post_id) {
	global $meta_box_mm, $meta_box_mm_video;
 
	// verify nonce
	if( key_exists( 'doover_meta_box_nonce',$_POST ) ) {
		if ( ! wp_verify_nonce( $_POST['doover_meta_box_nonce'], basename(__FILE__) ) ) {
			return $post_id;
		}
	}
 
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
 
	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}
 
	foreach ($meta_box_mm['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		if( key_exists($field['id'], $_POST) ) {
			$new = $_POST[$field['id']];
		} else {
			$new = "";
		}
 
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}
add_action('save_post', 'doover_mm_save_data');
?>