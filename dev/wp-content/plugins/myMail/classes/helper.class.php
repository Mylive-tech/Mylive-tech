<?php if (!defined('ABSPATH')) die('not allowed');

class mymail_helper {

	public function __construct() {}

	public function create_image($attach_id = NULL, $img_url = NULL, $width, $height = NULL, $crop = false) {

		if ($attach_id) {

			$attach_id = intval($attach_id);

			$image_src = wp_get_attachment_image_src($attach_id, 'full');
			$actual_file_path = get_attached_file($attach_id);

			if (!$width && !$height) {
				$orig_size = getimagesize($actual_file_path);
				$width = $orig_size[0];
				$height = $orig_size[1];
			}

		} else if ($img_url) {

				$file_path = parse_url($img_url);

				if(file_exists($img_url)){
					$actual_file_path = $img_url;
					$img_url = str_replace(ABSPATH, site_url('/'), $img_url);
				}else{
					$actual_file_path = realpath($_SERVER['DOCUMENT_ROOT']) . $file_path['path'];
					/* todo: reconize URLs */
					if(!file_exists($actual_file_path)){

						return array(
							'id' => $attach_id,
							'url' => $img_url,
							'width' => $width,
							'height' => NULL,
							'asp' => NULL,
							'_' => 1,
						);

					}
				}


				$actual_file_path = ltrim($file_path['path'], '/');
				$actual_file_path = rtrim(ABSPATH, '/') . $file_path['path'];
				if(file_exists($actual_file_path)){
					$orig_size = getimagesize($actual_file_path);
				}else{
					$actual_file_path = ABSPATH.str_replace(site_url('/'), '', $img_url);
					$orig_size = getimagesize($actual_file_path);
				}

				$image_src[0] = $img_url;
				$image_src[1] = $orig_size[0];
				$image_src[2] = $orig_size[1];

		}

		if (!$height && isset($image_src[2])) $height = round($width /($image_src[1]/$image_src[2]));

		$file_info = pathinfo($actual_file_path);
		$extension = $file_info['extension'];

		$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

		$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . '.' . $extension;

		if ($image_src[1] > $width || $image_src[2] > $height) {

			if (file_exists($cropped_img_path)) {
				$cropped_img_url = str_replace(basename($image_src[0]), basename($cropped_img_path), $image_src[0]);

				return array(
					'id' => $attach_id,
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height,
					'asp' => $height ? $width/$height : NULL,
					'_' => 2,
				);
			}

			if ($crop == false) {

				$proportional_size = wp_constrain_dimensions($image_src[1], $image_src[2], $width, $height);
				$resized_img_path = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . '.' . $extension;

				if (file_exists($resized_img_path)) {
					$resized_img_url = str_replace(basename($image_src[0]), basename($resized_img_path), $image_src[0]);

					return array(
						'id' => $attach_id,
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1],
						'asp' => $proportional_size[1] ? $proportional_size[0]/$proportional_size[1] : NULL,
						'_' => 3,
					);
				}
			}

			if(function_exists( 'wp_get_image_editor' )){
				$image = wp_get_image_editor($actual_file_path);
				if(!is_wp_error($image)){
					$image->resize( $width, $height, $crop );
					$imageobj = $image->save();
					$new_img_path = $imageobj['path'];
				}else{
					$new_img_path = image_resize($actual_file_path, $width, $height, $crop);
				}
			}else{
				$new_img_path = image_resize($actual_file_path, $width, $height, $crop);
			}

			if(is_wp_error($new_img_path)) $new_img_path = $actual_file_path;

			$new_img_size = getimagesize($new_img_path);
			$new_img = str_replace(basename($image_src[0]), basename($new_img_path), $image_src[0]);

			return array(
				'id' => $attach_id,
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1],
				'asp' => $new_img_size[1] ? $new_img_size[0]/$new_img_size[1] : NULL,
				'_' => 4
			);

		}

		return array(
			'id' => $attach_id,
			'url' => $image_src[0],
			'width' => $image_src[1],
			'height' => $image_src[2],
			'asp' => $image_src[2] ? $image_src[1]/$image_src[2] : NULL,
			'_' => 5
		);

	}

	public function get_wpuser_meta_fields() {

		global $wpdb;

		$exclude = array('comment_shortcuts', 'first_name', 'last_name', 'nickname', 'use_ssl', 'default_password_nag', 'dismissed_wp_pointers', 'rich_editing', 'show_admin_bar_front', 'show_welcome_panel', 'admin_color', 'screen_layout_dashboard', 'screen_layout_newsletter');

		$meta_values = $wpdb->get_col("SELECT meta_key FROM {$wpdb->usermeta} WHERE meta_value NOT LIKE '%{%}%' AND meta_key NOT LIKE '{$wpdb->prefix}%' AND meta_key NOT IN ('".implode("', '", $exclude)."') GROUP BY meta_key ASC");

		return $meta_values;

	}

	public function link_query( $args = array(), $countonly = false ) {

		global $wpdb;

		$pts = get_post_types( array( 'public' => true ), 'objects' );
		$pt_names = array_keys( $pts );

		$defaults = array(
			'post_type' => $pt_names,
			'suppress_filters' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status' => 'publish',
			'order' => 'DESC',
			'orderby' => 'post_date',
			'posts_per_page' => -1,
			'offset' => 0,
		);

		$query = wp_parse_args($args, $defaults);

		if ( isset( $args['s'] ) )
			$query['s'] = $args['s'];


		if($countonly){
			// Do main query with only one result to reduce server load
			$get_posts = new WP_Query(wp_parse_args(array('posts_per_page' => 1, 'offset' => 0), $query));
			return $wpdb->query(str_ireplace('LIMIT 0, 1', '', $get_posts->request));
		}

		// Do main query.
		$get_posts = new WP_Query($query);

		$sql = str_replace('posts.ID', 'posts.*', $get_posts->request);

		$posts = $wpdb->get_results($sql);

		// Build results.
		$results = array();
		foreach ( $posts as $post ) {
			if ( 'post' == $post->post_type )
				$info = mysql2date( __( 'Y/m/d', 'mymail' ), $post->post_date );
			else
				$info = $pts[ $post->post_type ]->labels->singular_name;

			$results[] = array(
				'ID' => $post->ID,
				'title' => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'permalink' => get_permalink( $post->ID ),
				'info' => $info,
			);
		}

		return $results;
	}

	public function get_next_date($starttime, $interval, $time_frame, $weekdays = array()) {

							//eg +3 weeks
		$nextdate = strtotime('+'.$interval.' '.$time_frame, $starttime);

		if(!empty($weekdays) && count($weekdays) < 7){

			$dayofweek = date('w', $nextdate);

			while(!in_array($dayofweek, $weekdays)){

				//try next day
				$nextdate = strtotime('+1 day', $nextdate);
				$dayofweek = date('w', $nextdate);

			}

		}

		return $nextdate;

	}

	public function get_next_date_in_future_OLD($starttime, $interval, $time_frame) {

		$now = time();

		switch ($time_frame) {
			case 'year':
				$count = date('Y', $now)-date('Y', $starttime);
				break;
			case 'month':
				$count = abs((date('Y', $now) - date('Y', $starttime))*12 + (date('m', $now) - date('m', $starttime)));
				break;
			case 'week':
				$count = floor((abs($now - $starttime)/86400)/7);
				break;
			case 'day':
				$count = floor(abs($now - $starttime)/86400);
				break;

			default:
				$count = $interval;
				break;
		}

		$times = ceil($count/$interval);

		$nextdate = strtotime(date('d-M-Y H:i:s', $starttime)." +".($interval*$times)." {$time_frame}");

		//add a single entity if date is still in the past
		if($nextdate - $now < 0)
			$nextdate = strtotime(date('d-M-Y H:i:s', $starttime)." +".($interval*$times+$interval)." {$time_frame}");

		return $nextdate;

	}

	public function get_next_date_in_future($utc_start, $interval, $time_frame, $weekdays = array(), $in_future = true) {

		//in local time
		$offset = $this->gmt_offset(true);
		$now = time() + $offset;
		$utc_start += $offset;
		$times = 1;

		//must be in future and starttime in the past
		if($in_future && $utc_start - $now < 0){
			//get how many $time_frame are in the time between now and the starttime
			switch ($time_frame) {
				case 'year':
					$count = date('Y', $now)-date('Y', $utc_start);
					break;
				case 'month':
					$count = (date('Y', $now) - date('Y', $utc_start))*12 + (date('m', $now) - date('m', $utc_start));
					break;
				case 'week':
					$count = floor((($now - $utc_start)/86400)/7);
					break;
				case 'day':
					$count = floor(($now - $utc_start)/86400);
					break;
				case 'hour':
					$count = floor(($now - $utc_start)/3600);
					break;
				default:
					$count = $interval;
					break;
			}

			$times = $interval ? ceil($count/$interval) : 0;
		}

		$nextdate = strtotime(date('Y-m-d H:i:s', $utc_start)." +".($interval*$times)." {$time_frame}");

		//add a single entity if date is still in the past or just now
		if($in_future && ($nextdate - $now < 0 || $nextdate == $utc_start))
			$nextdate = strtotime(date('Y-m-d H:i:s', $utc_start)." +".($interval*$times+$interval)." {$time_frame}");

		if(!empty($weekdays) && count($weekdays) < 7){

			$dayofweek = date('w', $nextdate);
			$i = 0;
			if(!$interval) $interval = 1;

			while(!in_array($dayofweek, $weekdays)){

				//try next $time_frame
				$nextdate = strtotime("+{$interval} {$time_frame}", $nextdate);
				$dayofweek = date('w', $nextdate);

				//force a break to prevent infinity loops
				if($i > 500) break;
				$i++;
			}

		}

		//return as UTC
		return $nextdate-$offset;

	}

	public function get_post_term_dropdown($post_type = 'post', $labels = true, $names = false, $values = array()) {

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$html = '';

		$taxwraps = array();

		foreach($taxonomies as $id => $taxonomy){
			$tax = '<div>'.($labels ? '<label class="dynamic_embed_options_taxonomy_label">'.$taxonomy->labels->name.': </label>' : '').'<span class="dynamic_embed_options_taxonomy_wrap">';

			$cats = get_categories( array('hide_empty' => false, 'taxonomy' => $id, 'type' => $post_type, 'orderby' => 'id' ,'number' => 999) );

			if(!isset($values[$id])) $values[$id] = array('-1');

			$selects = array();

			foreach($values[$id] as $term){
				$select = '<select class="dynamic_embed_options_taxonomy check-for-posts" '.($names ? 'name="mymail_data[autoresponder][terms]['.$id.'][]"': '').'>';
				$select .= '<option value="-1">'.sprintf(__('any %s', 'mymail'), $taxonomy->labels->singular_name).'</option>';
				foreach($cats as $cat){
					$select .= '<option value="'.$cat->term_id.'" '.selected($cat->term_id, $term, false).'>'.$cat->name.'</option>';
				}
				$select .= '</select>';
				$selects[] = $select;
			}

			$tax .= implode(' '.__('or', 'mymail').' ', $selects);

			$tax .= '</span></div>';

			$taxwraps[] = $tax;
		}

		$html = (!empty($taxwraps)) ? implode(($labels ? '<label class="dynamic_embed_options_taxonomy_label">&nbsp;</label>' : '').'<span>' .__('and', 'mymail') . '</span>',$taxwraps) : '';

		return $html;

	}

	public function social_services(){
		include MYMAIL_DIR . 'includes/social_services.php';

		return $mymail_social_services;


	}

	public function using_permalinks(){
		global $wp_rewrite;
		return is_object($wp_rewrite) && $wp_rewrite->using_permalinks();
	}

	public function gmt_offset($in_seconds = false){

		$offset = get_option('gmt_offset');
		if($offset == ''){
			$tzstring = get_option('timezone_string');
			$current = date_default_timezone_get();
			date_default_timezone_set($timezone);
			$offset = date('Z')/3600;
			date_default_timezone_set($current);
		}

		return $in_seconds ? $offset*3600 : (int) $offset;
	}

}

?>
