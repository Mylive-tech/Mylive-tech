<?php
/**
 * Slider custom fields.
 *

 */

 
/*-----------------------------------------------------------------------------------*/
/*	Define Metabox Fields
/*-----------------------------------------------------------------------------------*/

$prefix = 'doover_';
 
$meta_box = array(
	'id' => 'doover-meta-box',
	'title' => __('Slider fields','doover'),
	'page' => 'slide',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => __('Subtitle','doover'),
			'desc' => __('Part of the title that will be display using white font color','doover'),
			'id' => $prefix . 'subtitle',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => __('Thumbnail','doover'),
			'desc' => __('Maximum size: 120px x 60px. If not set, resized futured image will be used','doover'),
			'id' => $prefix . 'thumbnail',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => '',
			'desc' => '',
			'id' => $prefix . 'upload_image_button',
			'type' => 'button',
			'std' => __('Upload','doover')
		),
		array(
			'name' => __('Button text','doover'),
			'desc' => '',
			'id' => $prefix . 'btn_text',
			'type' => 'text',
		
			'std' =>  __('See our offer for details','doover')
		),
		array(
			'name' => __('Button link','doover'),
			'desc' => __('The button will apear only if this field is fill correct','doover'),
			'id' => $prefix . 'link',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => __('YouTube video ID','doover'),
			'desc' => __('It’s placed in every YouTube video link after v= parameter, for example: http://www.youtube.com/watch?v=<b>NPoHPNeU9fc</b>','doover'),
			'id' => $prefix . 'video_youtube',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => __('Vimeo video ID','doover'),
			'desc' => __('It’s placed in every Vimeo video link after the last /, for example: http://vimeo.com/<b>19819283</b>','doover'),
			'id' => $prefix . 'video_vimeo',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => __('Player width','doover'),
			'desc' => __('Video slide only','doover'),
			'id' => $prefix . 'video_width',
			'type' => 'text',
			'std' => '510'
		),
		array(
			'name' => __('Player height','doover'),
			'desc' => __('Video slide only','doover'),
			'id' => $prefix . 'video_height',
			'type' => 'text',
			'std' => '350'
		),
	),
);


/*-----------------------------------------------------------------------------------*/
/*	Add metabox to edit page
/*-----------------------------------------------------------------------------------*/ 
function doover_add_box() {
	global $meta_box;
	add_meta_box($meta_box['id'], $meta_box['title'], 'doover_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}
add_action('admin_menu', 'doover_add_box');


/*-----------------------------------------------------------------------------------*/
/*	Callback function to show fields in meta box
/*-----------------------------------------------------------------------------------*/
function doover_show_box() {
	global $meta_box, $post;
 	
	// Use nonce for verification
	echo '<input type="hidden" name="doover_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
 
	echo '<table class="form-table">';
 
	$iteration = 1;
	foreach ($meta_box['fields'] as $field) {
		
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
function doover_save_data($post_id) {
	global $meta_box, $meta_box_video;
 
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
 
	foreach ($meta_box['fields'] as $field) {
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
add_action('save_post', 'doover_save_data');


/*-----------------------------------------------------------------------------------*/
/*	Scripts & styles
/*-----------------------------------------------------------------------------------*/
function doover_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('doover-upload', LIBS_URI .'/js/upload-button.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('doover-upload');
}
add_action('admin_print_scripts', 'doover_admin_scripts');

function doover_admin_styles() {
	wp_enqueue_style('thickbox');
}
add_action('admin_print_styles', 'doover_admin_styles');

?>