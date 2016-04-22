<?php 
/**
 * Class specifying main functions of video gallery.
 * 
 * @author Praveen Rajan
 *
 */
class CvgCore{
	
	var $default_gallery_path;
	var $winabspath;
	
	/**
	 * Initializes values
	 * @author Praveen Rajan
	 */
	function CvgCore() {
		$cool_video_gallery = new CoolVideoGallery();
		$this->default_gallery_path = $cool_video_gallery->default_gallery_path;
		$this->winabspath = $cool_video_gallery->winabspath;
	}
	
	/**
	 * Function to upload and add gallery.
	 * @author Praveen Rajan
	 */
	function processor(){
		
    	if (isset($_POST['addgallery']) && $_POST['addgallery']){

			// wp_nonce_field('cvg_add_gallery_nonce','cvg_add_gallery_nonce_csrf');
			// if this fails, check_admin_referer() will automatically print a "failed" page and die.
			if ( check_admin_referer( 'cvg_add_gallery_nonce', 'cvg_add_gallery_nonce_csrf' ) ) {
			
	    		$newgallery = esc_attr( $_POST['galleryname'] );
	    		if(isset($_POST['gallerydesc'])) 
	    			$gallery_desc = esc_attr ( $_POST['gallerydesc'] );
				else 
					$gallery_desc = '';
	    		if ( !empty($newgallery) )
	    			CvgCore::create_gallery($newgallery, $gallery_desc);
	    		else
	    			CvgCore::show_video_error( __('No valid gallery name!') );
			}
    	}
		
		if (isset($_POST['uploadvideo']) && $_POST['uploadvideo']){
			
			// wp_nonce_field('cvg_upload_video_nonce','cvg_upload_video_nonce_csrf');
			if ( check_admin_referer( 'cvg_upload_video_nonce', 'cvg_upload_video_nonce_csrf' ) ) {
	    		if ( $_FILES['videofiles']['error'][0] == 0 ){
	    			$messagetext = CvgCore::upload_videos();
	    		}else{
	    			CvgCore::show_video_error( __('Upload failed! ' . CvgCore::decode_upload_error( $_FILES['videofiles']['error'][0])) );
	    		}
			}	
    	}
		
		if(isset($_POST['addvideo']) && $_POST['addvideo']) {
			
			// wp_nonce_field('cvg_attach_youtube_nonce','cvg_attach_youtube_nonce_csrf');
			if ( check_admin_referer( 'cvg_attach_youtube_nonce', 'cvg_attach_youtube_nonce_csrf' ) ) {
				if(empty($_POST['videourl']))
					CvgCore::show_video_error( __('Enter a valid Youtube video URL.') );
				else {
					CvgCore::add_youtube_videos();
				}
			}
		}
		
		if(isset($_POST['addmedia']) && $_POST['addmedia']) {
			
			// wp_nonce_field('cvg_add_media_nonce','cvg_add_media_nonce_csrf');
			if ( check_admin_referer( 'cvg_add_media_nonce', 'cvg_add_media_nonce_csrf' ) ) {
				
				CvgCore::add_media_videos();
			}
		}
	}
	
	/**
	 * Function to create a new gallery & folder
	 * 
	 * @param string $gallerytitle
	 * @param string $defaultpath
	 * @param bool $output if the function should show an error messsage or not
	 * @author Praveen Rajan
	 */
	function create_gallery($gallerytitle, $gallery_desc ,$output = true) {

		global $wpdb, $user_ID;
		
		$defaultpath = $this->default_gallery_path;	
		
		$galleryname = sanitize_file_name( $gallerytitle );
		$video_path = $defaultpath . $galleryname;
		$videoRoot = $this->winabspath . $defaultpath;
		$txt = '';

		if ( empty($galleryname) ) {	
			if ($output) 
				CvgCore::show_video_error( __('No valid gallery name!') );
			return false;
		}
		
		if ( !is_dir($videoRoot) ) {
			if ( !wp_mkdir_p( $videoRoot ) ) {
				$txt  = __('Directory').' <strong>' . $defaultpath . '</strong> '.__('didn\'t exist. Please create first the main gallery folder').'!<br />';
				$txt .= __('Check this link, if you didn\'t know how to set the permission :').' <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> ';
				if ($output) 
					CvgCore::show_video_error($txt);
				return false;
			}
		}

		if ( !is_writeable( $videoRoot ) ) {
			$txt  = __('Directory').' <strong>' . $defaultpath . '</strong> '.__('is not writeable !').'<br />';
			$txt .= __('Check this link, if you didn\'t know how to set the permission :').' <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> ';
			if ($output) 
				CvgCore::show_video_error($txt);
			return false;
		}

		if ( !is_dir($this->winabspath . $video_path) ) {
			if ( !wp_mkdir_p ($this->winabspath . $video_path) ) 
				$txt  = __('Unable to create directory').$video_path.'!<br />';
		}
		
		if ( !is_writeable($this->winabspath . $video_path ) ) {
			$txt .= __('Directory').' <strong>'.$video_path.'</strong> '.__('is not writeable !').'<br />';
		}
		
		if ( !is_dir($this->winabspath . $video_path . '/thumbs') ) {				
			if ( !wp_mkdir_p ( $this->winabspath . $video_path . '/thumbs') ) 
				$txt .= __('Unable to create directory').' <strong>' . $video_path . '/thumbs !</strong>';
		}
		
		if ( !empty($txt) ) {
			rmdir($this->winabspath . $video_path . '/thumbs');
			rmdir($this->winabspath . $video_path);
		}
		
		$result = $wpdb->get_var("SELECT name FROM " . $wpdb->prefix . "cvg_gallery WHERE name = '$galleryname' ");
		
		if ($result) {
			if ($output) 
				CvgCore::show_video_error( _n( 'Gallery', 'Galleries', 1 ) .' <strong>\'' . $galleryname . '\'</strong> '.__('already exists'));
			return false;			
		} else { 
			$result = $wpdb->query( $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "cvg_gallery (name, path, title, author, galdesc) VALUES (%s, %s, %s, %s, %s)", $galleryname, $video_path, $gallerytitle , $user_ID, $gallery_desc) );
			if ($result) {
				$message  = __("Gallery '$galleryname' successfully created.<br/>");
				if ($output)
					CvgCore::show_video_message($message); 
			}
			return true;
		} 
	}
	
	/**
	 * Function for uploading of videos via the upload form
	 * 
	 * @return void
	 * @author Praveen Rajan
	 */
	function upload_preview() {
	
		// Videos must be an array
		$imageslist = array();
	
		// get selected gallery
		$videoID = (int) $_POST['TB_previewimage_single'];
	
		if ($videoID == 0) {
			CvgCore::show_video_error(__('Error uploading preview image!'));
			return;	
		}
		
		$video = videoDB::find_video($videoID);
		$video_thumb_name = $video[0]->thumb_filename;
		
		$gallery_path = $this->winabspath . $video[0]->path;
		if ( empty($video[0]->path) ){
			CvgCore::show_video_error(__('Failure in database, no gallery path set !'));
			return;
		} 
		
		$videofiles = $_FILES['preview_image'];
		
		if (is_array($videofiles)) {
			foreach ($videofiles['name'] as $key => $value) {
	
				// look only for uploded files
				if ($videofiles['error'][$key] == 0) {
		
					$temp_file = $videofiles['tmp_name'][0];
							
					$temp_file_size = filesize($temp_file);
					$temp_file_size = intval(CvgCore::wp_convert_bytes_to_kb($temp_file_size));
					
					$max_upload_size = CvgCore::get_max_size();
					
					if($temp_file_size > $max_upload_size){
						CvgCore::show_video_error( __('File upload size limit exceeded.'));
						return;
					}
					
					$dest_file = $gallery_path . '/thumbs/' . $video_thumb_name;
				
					if ( !@move_uploaded_file($temp_file, $dest_file) ){
						CvgCore::show_video_error(__('Error, the file could not moved to : ') . $dest_file);
						return;
					}else {
						
						$new_size = @getimagesize ( $dest_file );
						$size ['width'] = $new_size [0];
						$size ['height'] = $new_size [1];
			
						// add them to the database
						videoDB::update_video_meta ( $video[0]->pid, array ('video_thumbnail' => $size) );
			
						if ( !CvgCore::chmod($dest_file) ) {
							CvgCore::show_video_error(__('Error, the file permissions could not set'));
							return;
						}
					}
				}else {
					CvgCore::show_video_error(CvgCore::decode_upload_error($videofiles['error'][0]));
					return;
				}
			}
		}
		CvgCore::show_video_message( ('Video preview image successfully added'));
		return;
	}
	
	/**
	 * Function for uploading of videos via the upload form
	 * 
	 * @return void
	 * @author Praveen Rajan
	 */
	function upload_videos() {
	
		// Videos must be an array
		$videoslist = array();
	
		// get selected gallery
		$galleryID = (int) $_POST['galleryselect'];
	
		if ($galleryID == 0) {
			CvgCore::show_video_error(__('No gallery selected !'));
			return;	
		}
		
		// get the path to the gallery	
		$gallery = videoDB::find_gallery($galleryID);
		
		if ( empty($gallery->path) ){
			CvgCore::show_video_error(__('Failure in database, no gallery path set !'));
			return;
		} 
	
		// read list of videos
		$dirlist = CvgCore::scandir_video_name($gallery->abspath);
		
		$videofiles = $_FILES['videofiles'];
		
		if (is_array($videofiles)) {
			foreach ($videofiles['name'] as $key => $value) {
	
				// look only for uploded files
				if ($videofiles['error'][$key] == 0) {
					
					$temp_file = $videofiles['tmp_name'][$key];
					
					$temp_file_size = filesize($temp_file);
					$temp_file_size = intval(CvgCore::wp_convert_bytes_to_kb($temp_file_size));
					
					$max_upload_size = CvgCore::get_max_size();

					if($temp_file_size > $max_upload_size){
						
						CvgCore::show_video_error( __('File upload size limit exceeded.'));
						continue;
					}
					//clean filename and extract extension
					$filepart = CvgCore::fileinfo( $videofiles['name'][$key] );
					$filename = $filepart['basename'];
					$file_name = $filepart['filename'];
						
					// check for allowed extension
					
					$cool_video_gallery = new CoolVideoGallery();
					$ext = $cool_video_gallery->allowed_extension; 
					
					if ( !in_array($filepart['extension'], $ext) || !@filesize($temp_file) ){ 
						CvgCore::show_video_error('<strong>' . $videofiles['name'][$key] . ' </strong>' . __('is no valid video file !'));
						continue;
					}
	
					// check if this filename already exist in the folder
					$i = 0;
					
					while ( in_array( $file_name, $dirlist ) ) {
						$i++;
						$filename = $filepart['filename'] . '_' . $i . '.' .$filepart['extension'];
						$file_name = $filepart['filename'] . '_' . $i;
					}
					
					$dest_file = $gallery->abspath . '/' . $filename;
					
					//check for folder permission
					if ( !is_writeable($gallery->abspath) ) {
						$message = sprintf(__('Unable to write to directory %s. Is this directory writable by the server?'), $gallery->abspath);
						CvgCore::show_video_error($message);
						return;				
					}
					
					// save temp file to gallery
					if ( !@move_uploaded_file($temp_file, $dest_file) ){
						CvgCore::show_video_error(__('Error, the file could not moved to : ') . $dest_file);
						continue;
					} 
					if ( !CvgCore::chmod($dest_file) ) {
						CvgCore::show_video_error(__('Error, the file permissions could not set.'));
						continue;
					}
					
					// add to videolist & dirlist
					$videolist[] = $filename;
					$dirlist[] = $file_name;
				}else {
					
					CvgCore::show_video_error(CvgCore::decode_upload_error($videofiles['error'][0]));
					return;
				}
			}
		}
	
		if (count($videolist) > 0) {
			
			// add videos to database		
			$videos_ids = CvgCore::add_Videos($galleryID, $videolist);
	
			if (CvgCore::ffmpegcommandExists()) 	{
				foreach($videos_ids as $video_id )
					CvgCore::create_thumbnail_video($video_id);
			}	
			
			CvgCore::xml_playlist($galleryID);
			
			CvgCore::show_video_message( count($videos_ids) . __(' Video(s) successfully added.'));
		}
		return;
	}
	
	/**
	 * Function for add videos from Youtube
	 * 
	 * @return void
	 * @author Praveen Rajan
	 */
	function add_youtube_videos() {
	
		// Videos must be an array
		$videoslist = array();
	
		// get selected gallery
		$galleryID = (int) $_POST['galleryselect_add'];
	
		if ($galleryID == 0) {
			CvgCore::show_video_error(__('No gallery selected !'));
			return;	
		}
		
		if(ini_get('allow_url_fopen')) {
			// get the path to the gallery	
			$gallery = videoDB::find_gallery($galleryID);
			
			if ( empty($gallery->path) ){
				CvgCore::show_video_error(__('Failure in database, no gallery path set !'));
				return;
			} 
		
			$videofiles = explode(",", trim($_POST['videourl']));
			
			if (is_array($videofiles)) {
				$videos_ids = CvgCore::process_youtube_videos($galleryID, $videofiles);
				
				CvgCore::xml_playlist($galleryID);
				if(count($videos_ids) != 0)
					CvgCore::show_video_message( count($videos_ids) . __(' Video(s) successfully added.'));
			}
		}else {
			CvgCore::show_video_error(__('Please enable the PHP setting `allow_url_fopen`'));
		}
		return;
	}

	/**
	 * Function to process youtube videos
	 * 
	 * @return void
	 * @author Praveen Rajan
	 */
	function process_youtube_videos($galleryID, $videolist) {
		
		global $wpdb;
	
		$cool_video_gallery = new CoolVideoGallery();
		$video_ids = array();
		
		if ( is_array($videolist) ) {
			foreach($videolist as $video) {
				
				$youtube_api = new CVGYoutubeAPI();
				
				$video_details = $youtube_api->youtube_video_details($video);
				
				if($video_details == "false") {
					
					CvgCore::show_video_error(__('SimpleXML PHP extension not loaded!'));
					return;
				}
				
				$alttext = $wpdb->escape($video_details->title);
				$time_updated = current_time('mysql', 1);
				$thumb_filename = $video_details->thumbnailURL;
				
				$videoDuration = CvgCore::secondsToWords((int) $video_details->length);
				
				$meta =  array ( 'videoDuration' => $videoDuration );
				
				// save it to the database 
				$result = $wpdb->query( $wpdb->prepare("INSERT INTO " . $wpdb->prefix ."cvg_videos (galleryid, filename, thumb_filename, alttext, description, video_title, videodate, video_type, meta_data) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)", $galleryID, $video_details->watchURL, $thumb_filename, $alttext, $wpdb->escape($video_details->description), $video_details->title, $time_updated, $cool_video_gallery->video_type_youtube, serialize($meta)) );
	
				$vid_id = (int) $wpdb->insert_id;
				
				if ($result) 
					$video_ids[] = $vid_id;
					
			} 
		} // is_array
	        
		return $video_ids;
	}

	/**
	 * Function to add videos from Media Library
	 * 
	 * @return void
	 * @author Praveen Rajan
	 */
	function add_media_videos() {
		
		global $wpdb;
		
		$cool_video_gallery = new CoolVideoGallery();
		
		// get selected gallery
		$galleryID = (int) $_POST['galleryselect_media'];
	
		if ($galleryID == 0) {
			CvgCore::show_video_error(__('No gallery selected !'));
			return;	
		}
		
		// get the path to the gallery	
		$gallery = videoDB::find_gallery($galleryID);
		
		if ( empty($gallery->path) ){
			CvgCore::show_video_error(__('Failure in database, no gallery path set !'));
			return;
		} 
		
		$media_post_id = (int) $_POST['mediaselect_add'];
		$post_details = get_post($media_post_id); 
		
		$time_updated = current_time('mysql', 1);
		$alttext = $wpdb->escape($post_details->post_name);
		$thumbs_file_name = "thumbs_" . $alttext . '.png';
		
		$video_title = isset($post_details->post_title) ? htmlspecialchars($post_details->post_title)  : htmlspecialchars($post_details->post_name);
		
		// save it to the database 
		$result = $wpdb->query( $wpdb->prepare("INSERT INTO " . $wpdb->prefix ."cvg_videos (galleryid, filename, thumb_filename, alttext, video_title, description, videodate, video_type) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", $galleryID, $post_details->guid, $thumbs_file_name, $alttext, $video_title, $wpdb->escape($post_details->post_content), $time_updated, $cool_video_gallery->video_type_media) );
		
		if ($result) {
			
			if (CvgCore::ffmpegcommandExists()) {
				
				$vid_id = (int) $wpdb->insert_id;
				CvgCore::create_thumbnail_video($vid_id);
			}	
			
			CvgCore::xml_playlist($galleryID);
			CvgCore::show_video_message(__('Video successfully added.'));
		}
		
	}

	/**
	 * Function to scan gallery folder for new videos
	 * @param $galleryID - gallery id
	 * @author Praveen Rajan
	 */
	function scan_upload_videos($galleryID, $enable = true){
		
		global $wpdb;
		
		$gallery = videoDB::find_gallery($galleryID);
		
		if(!$gallery)
			return;
		
		$dirlist = CvgCore::scandir_video($gallery->abspath);
		$videolist = array();
		
		foreach($dirlist as $video) {
			$video_newname = sanitize_file_name($video);
			$video_found = $wpdb->get_var("SELECT filename FROM " .  $wpdb->prefix . "cvg_videos  WHERE filename = '$video_newname' AND galleryid = '$galleryID'");
			if(!$video_found) {
				@rename($gallery->abspath . '/' . $video, $gallery->abspath . '/' . $video_newname );
				$videolist[] = $video_newname;
			}	
		}
		
		// add videos to database		
		$videos_ids = CvgCore::add_Videos($galleryID, $videolist);

		if (CvgCore::ffmpegcommandExists()) 	{
			foreach($videos_ids as $video_id )
				CvgCore::create_thumbnail_video($video_id);
		}	
		if(count($videos_ids)> 0) {
			
			CvgCore::xml_playlist($galleryID);
			
			if($enable)
				CvgCore::show_video_message( count($videos_ids) . __(' Video(s) successfully added.'));
		}else {
			
			if($enable)
				CvgCore::show_video_error( __(' No new video(s) found.'));
		} 
				
	}
	
	/**
	 * Add videos to database
	 * 
	 * @param int $galleryID
	 * @param array $videolist
	 * @return array $video_ids Id's which are sucessful added
	 * @author Praveen Rajan
	 */
	function add_Videos($galleryID, $videolist) {
		
		global $wpdb;
	
		$video_ids = array();
		
		$cool_video_gallery = new CoolVideoGallery();
		
		if ( is_array($videolist) ) {
			foreach($videolist as $video) {
				
				// strip off the extension of the filename
				$path_parts = pathinfo( $video );
				$alttext = ( !isset($path_parts['filename']) ) ? substr($path_parts['basename'], 0,strpos($path_parts['basename'], '.')) : $path_parts['filename'];
				$time_updated = current_time('mysql', 1);
				
				$thumb_filename = 'thumbs_' . $alttext . '.png';
				
				// save it to the database 
				$result = $wpdb->query( $wpdb->prepare("INSERT INTO " . $wpdb->prefix ."cvg_videos (galleryid, filename, thumb_filename, alttext, video_title, description, videodate, video_type) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", $galleryID, $video, $thumb_filename, $alttext, $alttext, $alttext, $time_updated, $cool_video_gallery->video_type_upload) );
				$vid_id = (int) $wpdb->insert_id;
				
				if ($result) 
					$video_ids[] = $vid_id;
	
			} 
		} // is_array
	        
		return $video_ids;
	}
	
	
	/**
	 * Function to generate xml for video playlist
	 * 
	 * @author Praveen Rajan
	 */
	function xml_playlist($galleryID = null) {
		
		global $wpdb;
		$cool_video_gallery = new CoolVideoGallery();
		
		$gallery_detail = videoDB::find_gallery($galleryID);
		$gallery_details = videoDB::get_gallery($galleryID, false);
		
		$options = get_option('cvg_settings');
		
		if(isset($options['cvg_random_video']) && $options['cvg_random_video'] == 1)
			shuffle($gallery_details);
		
		$xml = "";
		$xml .= '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<rss version="2.0" xmlns:jwplayer="http://developer.longtailvideo.com/trac/" xmlns:media="http://search.yahoo.com/mrss/">'; 
		$xml .= '<channel>';
		$xml .= '<title>'. htmlspecialchars($gallery_detail->galdesc) . '</title>';
		
		foreach($gallery_details as $video){
			
			if($video->video_type == $cool_video_gallery->video_type_upload) {

				$video_url = site_url()  . '/' . $video->path . '/' . $video->filename;
				$thumb_url = site_url() . '/' . $video->path . '/thumbs/' . $video->thumb_filename;
				
				if(!file_exists(ABSPATH . '/' . $video->path . '/thumbs/' . $video->thumb_filename ))
					$thumb_url  = WP_CONTENT_URL .  '/plugins/' . dirname(dirname( plugin_basename(__FILE__))) . '/images/default_video.png';
	
			}else if($video->video_type == $cool_video_gallery->video_type_youtube){

				$video_url = $video->filename;
				$thumb_url = $video->thumb_filename;
			}else if($video->video_type == $cool_video_gallery->video_type_media){
				
				$video_url = $video->filename;
	
				$thumb_url = site_url() . '/' . $video->path . '/thumbs/' . $video->thumb_filename;
				if(!file_exists(ABSPATH . '/' . $video->path . '/thumbs/' . $video->thumb_filename ))
					$thumb_url  = WP_CONTENT_URL .  '/plugins/' . dirname(dirname( plugin_basename(__FILE__))) . '/images/default_video.png';
	
			}else {
				
				$video_url = site_url()  . '/' . $video->path . '/' . $video->filename;
				$thumb_url = site_url() . '/' . $video->path . '/thumbs/' . $video->thumb_filename;
				
				if(!file_exists(ABSPATH . '/' . $video->path . '/thumbs/' . $video->thumb_filename ))
					$thumb_url  = WP_CONTENT_URL .  '/plugins/' . dirname(dirname( plugin_basename(__FILE__))) . '/images/default_video.png';
	
			}
				
			$desc = stripcslashes($video->description);
			$pub_date = $video->videodate;
			$title = isset($video->video_title) ? $video->video_title : "" ;
			
			$xml .= '<item>';
			$xml .= '<title>' . htmlspecialchars($title) . '</title>';
			$xml .= '<pubDate>' . htmlspecialchars($pub_date) . '</pubDate>';
			$xml .= '<description>' . htmlspecialchars($desc) . '</description>';
			$xml .= '<media:content url="' . htmlspecialchars($video_url) . '" />';
			$xml .= '<media:thumbnail url="' . htmlspecialchars($thumb_url) . '" />';
			$xml .= '<jwplayer:playlist.image>' . htmlspecialchars($thumb_url) . '</jwplayer:playlist.image>';
			$xml .= '</item>';
		}
		$xml .= '</channel></rss>';
		
		$gallery_name = $gallery_detail->name;
		
		$playlist_xml = ABSPATH . '/' . $gallery_detail->path . '/' . $gallery_name . '-playlist.xml';
	  	 if(CvgCore::createFile($playlist_xml)) {
			if (file_put_contents ($playlist_xml, $xml)) {
				CvgCore::chmod ($playlist_xml);
				return true;
			}
	   }	
	}
	
	/**
	 * Function to create a preview thumbnail for video
	 * 
	 * @param object | int $video contain all information about the video or the id
	 * @return string result code
	 * @author Praveen Rajan
	 */
	function create_thumbnail_video($videoID) {
	
		$options = get_option('cvg_settings');
		$thumb_width = $options['cvg_preview_width'];
		$thumb_height = $options['cvg_preview_height'];
				
		if (is_numeric ( $videoID ))
			$videoDetails = videoDB::find_video ( $videoID );
			
		$video = $videoDetails[0];
		
		if ( !is_object($video) ) 
			return __('Object didn\'t contain correct data');
			
		$filepart = CvgCore::fileinfo( $video->filename );
			
		// check for allowed extension 
		$cool_video_gallery = new CoolVideoGallery();
		$ext = $cool_video_gallery->allowed_extension;
		  
		if ( !in_array($filepart['extension'], $ext) ){ 
			return;
		}
		
		$gallery = videoDB::find_gallery($video->galleryid);
		$video_input = $gallery->abspath . '/' . $video->filename;
		$new_target_filename = $video->alttext . '.png';
		$new_target_file = $gallery->abspath . '/thumbs/thumbs_' . $new_target_filename;
		
		if($video->video_type == $cool_video_gallery->video_type_media){
			$command = $options['cvg_ffmpegpath'] . " -i '$video->filename' -vcodec mjpeg -vframes 1 -an -f rawvideo -ss 5 -s ".$thumb_width ."x".$thumb_height." '$new_target_file'";
		}else {
			$command = $options['cvg_ffmpegpath'] . " -i '$video_input' -vcodec mjpeg -vframes 1 -an -f rawvideo -ss 5 -s ".$thumb_width ."x".$thumb_height." '$new_target_file'";	
		}
		
		exec ( $command );
		
		//get video duration
		if($video->video_type == $cool_video_gallery->video_type_media){
			
			$video_duration = CvgCore::video_duration($video->filename);
		}else {
			
			$video_duration = CvgCore::video_duration($video_input);	
		}
		
		
		if (file_exists ( $new_target_file )) {
			
			CvgCore::chmod ($new_target_file); 
			
			/** ver 1.5
			 *	Size calculation 
			 */
			$new_size = @getimagesize ( $new_target_file );
			$size ['width'] = $new_size [0];
			$size ['height'] = $new_size [1];
			
			// add them to the database
			videoDB::update_video_meta ( $video->pid, array ('video_thumbnail' => $size , 'videoDuration' => $video_duration ) );
		}else {
			
			// add them to the database
			videoDB::update_video_meta ( $video->pid, array ('videoDuration' => $video_duration ) );
		}
	}
	
	/**
	 * Function to delete video file from a gallery
	 * 
	 * @param $pid - video id
	 * @author Praveen Rajan
	 */	
	function delete_video_files($pid = '') {
			
		$cool_video_gallery = new CoolVideoGallery();
		
		$video_detail = videoDB::find_video($pid);
		
		if($video_detail[0]->video_type == $cool_video_gallery->video_type_upload) {
		    $video_path = $this->winabspath . $video_detail[0]->path . '/' . $video_detail[0]->filename;
		    $thumb_filename = $video_detail[0]->alttext . '.png';
		    $thumb_path = $this->winabspath . $video_detail[0]->path . '/thumbs/thumbs_' . $thumb_filename;
			
			@unlink($video_path);
			@unlink($thumb_path);
		}else if($video_detail[0]->video_type == $cool_video_gallery->video_type_media) {
			
			$thumb_filename = $video_detail[0]->alttext . '.png';
		    $thumb_path = $this->winabspath . $video_detail[0]->path . '/thumbs/thumbs_' . $thumb_filename;
			@unlink($thumb_path);
		}
		
	}
	
	/**
	 * Function to delete folder for gallery.
	 * 
	 * @param $gid - gallery id
	 * @author Praveen Rajan
	 */
	function delete_video_gallery($gid = '') {
		
		$videos = videoDB::get_gallery($gid);
		$video_gallery_path = videoDB::find_gallery($gid);
		
		CvgCore::deleteDir( $video_gallery_path->abspath. '/thumbs' );
		CvgCore::deleteDir( $video_gallery_path->abspath );
	
		return true;	
	}
	
	
	/**
	 * Function to remove directory and its files recursively
	 * @param $directory - directory path
	 * @param $empty - recursive true/false
	 * @return true or false
	 * @author Praveen Rajan
	 */
	function deleteDir($directory, $empty = false) {
	    if(substr($directory,-1) == "/") {
	        $directory = substr($directory,0,-1);
	    }
	    if(!file_exists($directory) || !is_dir($directory)) {
	        return false;
	    } elseif(!is_readable($directory)) {
	        return false;
	    } else {
	        $directoryHandle = opendir($directory);
	        while ($contents = readdir($directoryHandle)) {
	            if($contents != '.' && $contents != '..') {
	                $path = $directory . "/" . $contents;
	                if(is_dir($path)) {
	                    CvgCore::deleteDir($path);
	                } else {
	                    @unlink($path);
	                }
	            }
	        }
	        closedir($directoryHandle);
	        if($empty == false) {
	            if(!@rmdir($directory)) {
	                return false;
	            }
	        }
	        return true;
	    }
	} 
	
	/**
	 * Function to generate xml sitemap for videos
	 * 
	 * @author Praveen Rajan
	 */
	function xml_sitemap() {
		
		if (!CvgCore::ffmpegcommandExists()) {
			
			CvgCore::show_video_error('Google XML Video Sitemap cannot be created since <b>FFMPEG</b> library is not installed in server. FFMPEG is required to collect video duration information.');
			return true;
		} 
		
		$cool_video_gallery = new CoolVideoGallery();
		$results = videoDB::get_all_videos_sitemap();
		
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';
		$xml .= '<!-- Generated by (http://wordpress.org/extend/plugins/cool-video-gallery/), Authored by Praveen Rajan -->' . "\n";
		$xml .= '<url>'; 
		$xml .= '<loc>'. site_url() . '</loc>';
		
		foreach($results as $result){
			
			if($result['meta_data'] != ''){
				$video_meta_data = unserialize($result['meta_data']);
				
				$seconds = date('s', strtotime($video_meta_data['videoDuration']));
				$minutes = date('i', strtotime($video_meta_data['videoDuration']));
				$hours = date('H', strtotime($video_meta_data['videoDuration']));
				
				$total_seconds = round( ($hours*60*60) + ($minutes*60) + $seconds );
			}else{
				
				$total_seconds = 100;
			}	
				
			$gallery_details = videoDB::get_gallery_sitemap($result['galleryid']);
			
			if($result['video_type'] == $cool_video_gallery->video_type_upload) {
					
				$video_url = site_url()  . '/' . $gallery_details[0]['path'] . '/' . $result['filename'];
				$thumb_url = site_url() . '/' . $gallery_details[0]['path'] . '/thumbs/' . $result['thumb_filename'];
				
			}else if($result['video_type'] == $cool_video_gallery->video_type_youtube){
					
				$video_url = $result['filename'];
				$thumb_url = $result['thumb_filename'];
				
			}else if($result['video_type'] == $cool_video_gallery->video_type_media) {
				
				$video_url = $result['filename'];
				$thumb_url = site_url() . '/' . $gallery_details[0]['path'] . '/thumbs/' . $result['thumb_filename'];
				
			}else {
				
				$video_url = site_url()  . '/' . $gallery_details[0]['path'] . '/' . $result['filename'];
				$thumb_url = site_url() . '/' . $gallery_details[0]['path'] . '/thumbs/' . $result['thumb_filename'];
			}
			
			$xml .= '<video:video>';
			$xml .= '<video:thumbnail_loc>' . htmlspecialchars($thumb_url) . '</video:thumbnail_loc>';
			$xml .= '<video:title>' . htmlspecialchars($result['video_title']) . '</video:title>';
			$xml .= '<video:description>' . htmlspecialchars(stripcslashes($result['description'])) . '</video:description>';
			$xml .= '<video:content_loc>' . htmlspecialchars($video_url) . '</video:content_loc>';
			$xml .= '<video:duration>' . $total_seconds . '</video:duration>';
			$xml .= '</video:video> ';
		}
		
	   $xml .= '</url>'; 
	   $xml .= '</urlset>'; 

	   $video_sitemap_url = ABSPATH . 'sitemap-video.xml';
	   
	   if(CvgCore::createFile($video_sitemap_url)) {
			if (file_put_contents ($video_sitemap_url, $xml)) {
				
				CvgCore::show_video_message('Google XML Video Sitemap successfully created at location <b>' . $video_sitemap_url . '</b>');
				return true;
			}
	   }	
	}
	
	/**
	 * Function to create a file with permissions.
	 * 
	 * @param $filename - file path
	 * @author Praveen Rajan
	 */
	function createFile($filename) {
		if(!is_writable($filename)) {
			if(!@chmod($filename, 0666)) {
				$pathtofilename = dirname($filename);
				if(!is_writable($pathtofilename)) {
					if(!@chmod($pathtoffilename, 0666)) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Function to return proper error messages while uploading files.
	 * 
	 * @param $code
	 * @author Praveen Rajan
	 */
	function decode_upload_error( $code ) {
		
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = __ ( 'The uploaded file exceeds the upload_max_filesize directive in php.ini' );
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = __ ( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form' );
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = __ ( 'The uploaded file was only partially uploaded' );
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = __ ( 'No file was uploaded' );
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = __ ( 'Missing a temporary folder' );
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = __ ( 'Failed to write file to disk' );
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = __ ( 'File upload stopped by extension' );
                break;
            default:
                $message = __ ( 'Unknown upload error' );
                break;
        }
        return $message; 
	}
	
	/**
	 * Function to display overview of video gallery
	 *
	 * @return html code to display overview
	 * @author Praveen Rajan 
	 * 
	 */
	function gallery_overview() {
			
		global $wpdb;
		
		$videos    = intval( $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "cvg_videos") );
		$galleries = intval( $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "cvg_gallery") );
		?>
		<div class="table table_content">
			<p class="sub"><?php _e('At a Glance'); ?></p>
			<table>
				<tbody>
					<tr class="first">
						<td class="first b"><a href="<?php echo admin_url('admin.php?page=cvg-gallery-add');?>"><?php echo $videos; ?></a></td>
						<td class="b"></td>
						<td class="t"><a href="<?php echo admin_url('admin.php?page=cvg-gallery-add');?>"><?php echo _n( 'Videos', 'Videos', $videos ); ?></a></td>
					</tr>
					<tr>
						<td class="first b"><a href="<?php echo admin_url('admin.php?page=cvg-gallery-manage');?>"><?php echo $galleries; ?></a></td>
						<td class="b"></td>
						<td class="t"><a href="<?php echo admin_url('admin.php?page=cvg-gallery-manage');?>"><?php echo _n( 'Gallery', 'Galleries', $galleries ); ?></a></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="versions" style="padding-top:14px">
		    <p>
			<a class="button rbutton" href="<?php echo admin_url('admin.php?page=cvg-gallery-add#uploadvideo');?>"><?php _e('Upload videos') ?></a>
			<p><?php echo 'Here you can control your videos and galleries.'; ?></p>
			</p>
		<br class="clear" />
		</div>    
		<?php
	}
	
	/**
	 * Function to get tab order.
	 * 
	 * @author Praveen Rajan
	 */
	function tabs_order() {
	    $tabs = array();
	    $tabs['addgallery'] = __( 'Add New Gallery' );
	    $tabs['uploadvideo'] = __( 'Upload Videos' );
		$tabs['addmedia'] = __( 'Attach Media' );
		$tabs['linkvideo'] = __( 'Youtube Videos' );
	   	return $tabs;
	}
	
	/**
	 * Function for gallery tab.
	 * 
	 * @author Praveen Rajan
	 */
 	function tab_addgallery() {
    ?>
		<!-- create gallery -->
		<h2><?php _e('Add New Gallery') ;?></h2>
		<form name="addgallery" id="addgallery_form" method="POST" action="<?php echo admin_url('admin.php?page=cvg-gallery-add') . '#add'; ?>" accept-charset="utf-8" >
			<table class="form-table"> 
			<tr valign="top"> 
				<th scope="row"><?php _e('New Gallery') ;?>:</th> 
				<td><input type="text" size="35" name="galleryname" value="" style="width:94%;"/><br />
				<i>( <?php _e('Allowed characters for file and folder names are') ;?>: a-z, A-Z, 0-9, -, _ )</i></td>
			</tr>
			<tr>
				<th><?php _e('Description') ?>:</th> 
				<td><textarea name="gallerydesc" cols="30" rows="3" style="width: 94%" ></textarea></td>
			</tr>
			</table>
			<?php wp_nonce_field('cvg_add_gallery_nonce','cvg_add_gallery_nonce_csrf'); ?>
			<div class="submit"><input class="button-primary" type="submit" name= "addgallery" value="<?php _e('Add gallery') ;?>"/></div>
		</form>
    <?php
    }

    /**
	 * Function for upload video tab.
	 * 
	 * @author Praveen Rajan
	 */
	 function tab_uploadvideo() {
	?>
    	<!-- upload videos -->
    	<?php 
			$max_upload_size = wp_convert_bytes_to_hr(wp_max_upload_size());
    	?>
    	<h2><?php _e('Upload Videos') ;?></h2>
    	<form name="uploadvideo" id="uploadvideo_form" method="POST" enctype="multipart/form-data" action="<?php echo admin_url('admin.php?page=cvg-gallery-add').'#uploadvideo'; ?>" accept-charset="utf-8" >
			<table class="form-table"> 
			<tr valign="top"> 
				<th scope="row"><?php _e('Upload Video') ;?></th>
				<td><span id='spanButtonPlaceholder'></span><input type="file" name="videofiles[]" id="videofiles" size="35" class="videofiles"/>
				<br/>
				<i><?php _e('Allowed File Formats: H.264 (.mp4, .mov, .m4v), FLV (.flv) and MP3 (.mp3)') ;?>
					<br />
					<?php echo 'Maximum file upload size: '. $max_upload_size ; ?> 
				</i></td>
			</tr> 
			<tr valign="top"> 
				<th scope="row"><?php _e('in to') ;?></th> 
				<td><select name="galleryselect" id="galleryselect">
				<option value="0" ><?php _e('Choose gallery') ?></option>
				<?php
					$gallerylist = videoDB::find_all_galleries('gid', 'ASC');
					foreach($gallerylist as $gallery) {
						$name = ( empty($gallery->title) ) ? $gallery->name : $gallery->title;
						echo '<option value="' . $gallery->gid . '" >' . $gallery->gid . ' - ' . $name . '</option>' . "\n";
					}					
					?>
				</select>
			</tr> 
			</table>
			<?php wp_nonce_field('cvg_upload_video_nonce','cvg_upload_video_nonce_csrf'); ?>
			<div class="submit">
				<input type="hidden" value="Upload Videos" name="uploadvideo" />
				<input class="button-primary" type="button" name="uploadvideo_btn" id="uploadvideo_btn" value="<?php _e('Upload Video(s)') ;?>" />
			</div>
		</form>
		
    <?php
    } 

	/**
	 * Function for link videos
	 * 
	 * @author Praveen Rajan
	 */
	 function tab_linkvideo() {
	 	
		?>
    	<!-- Add youtube videos -->
    	<h2><?php _e('Youtube Videos') ;?></h2>
    	<form name="addvideo" id="addvideo_form" method="POST"  action="<?php echo admin_url('admin.php?page=cvg-gallery-add').'#linkvideo'; ?>" accept-charset="utf-8" >
			<table class="form-table"> 
			<tr valign="top"> 
				<th scope="row"><?php _e('Enter Youtube Video IDs separated by comma') ;?></th> 
				<td><input type="text" size="35" name="videourl" value="" style="width:94%;" placeholder="Youtube Video ID"/><br />
					<i>Example: Video ID for URL http://www.youtube.com/watch?v=YE7VzlLtp-4 is YE7VzlLtp-4</i>
				</td>	
			</tr> 
			<tr valign="top"> 
				<th scope="row"><?php _e('in to') ;?></th> 
				<td><select name="galleryselect_add" id="galleryselect_add">
				<option value="0" ><?php _e('Choose gallery') ?></option>
				<?php
					$gallerylist = videoDB::find_all_galleries('gid', 'ASC');
					foreach($gallerylist as $gallery) {
						$name = ( empty($gallery->title) ) ? $gallery->name : $gallery->title;
						echo '<option value="' . $gallery->gid . '" >' . $gallery->gid . ' - ' . $name . '</option>' . "\n";
					}					
				?>
				</select>
			</tr> 
			</table>
			
			<?php wp_nonce_field('cvg_attach_youtube_nonce','cvg_attach_youtube_nonce_csrf'); ?>
			<div class="submit">
				<input type="hidden" value="Add Videos" name="addvideo" />
				<input class="button-primary" type="button" name="addvideo_btn" id="addvideo_btn" value="<?php _e('Add Video(s)') ;?>" />
			</div>
		</form>
		
    <?php
    }        
    
	/**
	 * Function for show tab for media videos
	 * 
	 * @author Praveen Rajan
	 */
	 function tab_addmedia() {
	 	
		?>
    	<!-- Add youtube videos -->
    	<h2><?php _e('Attach Media') ;?></h2>
    	<form name="addmedia" id="addmedia_form" method="POST"  action="<?php echo admin_url('admin.php?page=cvg-gallery-add').'#addmedia'; ?>" accept-charset="utf-8" >
			<table class="form-table"> 
			<tr valign="top"> 
				<th scope="row"><?php _e('Choose Media from Library') ;?></th> 
				<td>
					<select name="mediaselect_add" id="mediaselect_add">
					<option value="0" ><?php _e('Choose media') ?></option>
					<?php
						
						$cool_video_gallery = new CoolVideoGallery();
						$ext = $cool_video_gallery->allowed_extension;
					
						$args = array('post_type' => 'attachment', 'post_mime_type' => 'video' );
						$mediafiles = get_posts($args);
						foreach ($mediafiles as $file) {
							
							$filepart = CvgCore::fileinfo($file->guid);
								
							if (in_array($filepart['extension'], $ext)){
								$name = ( empty($file->post_name) ) ? $file->post_title : $file->post_name;
								echo '<option value="' . $file->ID . '" >' . $name . '</option>' . "\n";
							}
						}
						
						$args_audio = array('post_type' => 'attachment', 'post_mime_type' => 'audio' );
						$mediafiles_audio = get_posts($args_audio);
						foreach ($mediafiles_audio as $file) {
							
							$filepart = CvgCore::fileinfo($file->guid);
								
							if (in_array($filepart['extension'], $ext)){
								$name = ( empty($file->post_name) ) ? $file->post_title : $file->post_name;
								echo '<option value="' . $file->ID . '" >' . $name . '</option>' . "\n";
							}
						}					
					?>
					</select>
					
				</td>
			</tr> 
			<tr valign="top"> 
				<th scope="row"><?php _e('in to') ;?></th> 
				<td>
				<select name="galleryselect_media" id="galleryselect_media">
				<option value="0" ><?php _e('Choose gallery') ?></option>
				<?php
					$gallerylist = videoDB::find_all_galleries('gid', 'ASC');
					foreach($gallerylist as $gallery) {
						$name = ( empty($gallery->title) ) ? $gallery->name : $gallery->title;
						echo '<option value="' . $gallery->gid . '" >' . $gallery->gid . ' - ' . $name . '</option>' . "\n";
					}					
				?>
				</select>
			</tr> 
			</table>
			<?php wp_nonce_field('cvg_add_media_nonce','cvg_add_media_nonce_csrf'); ?>
			<div class="submit">
				<input type="hidden" value="Add Media" name="addmedia" />
				<input class="button-primary" type="button" name="addmedia_btn" id="addmedia_btn" value="<?php _e('Add Media') ;?>" />
			</div>
		</form>
		
    <?php
    } 

    /**
     * Function to get maximum upload size of a file.
     * @return file size
     * @author Praveen Rajan
     */
    function get_max_size() {
    	
		return intval(CvgCore::wp_convert_bytes_to_kb(wp_max_upload_size()));
    }
    
    function wp_convert_bytes_to_kb ($bytes) {
    	
    	return intval($bytes/1024);
    }
	/**
	 * Function to update video details.
	 * 
	 * @author Praveen Rajan
	 */
	function update_videos() {
		global $wpdb;
	
		$description = 	isset ( $_POST['description'] ) ? $_POST['description'] : false;
		$video_title = isset ( $_POST['video_title'] ) ? $_POST['video_title'] : false;
		
		$galleryId = $_POST['galleryId'];
		$wpdb->query( "UPDATE " . $wpdb->prefix . "cvg_videos SET exclude = '0' WHERE galleryid = $galleryId");
		
		if(isset($_POST['exclude'])) {
		
			foreach ($_POST['exclude'] as $key => $value) {
				$wpdb->query( "UPDATE " . $wpdb->prefix . "cvg_videos SET exclude = '1' WHERE pid = $value");
			}
		}
		if ( is_array($description) ) {
			foreach( $description as $key => $value ) {
				$desc = $wpdb->escape($value);
				$wpdb->query( "UPDATE " . $wpdb->prefix . "cvg_videos SET description = '$desc' WHERE pid = $key");
			}
		}
		
		if ( is_array($video_title) ) {
			foreach( $video_title as $key => $value ) {
				$desc = $wpdb->escape($value);
				$wpdb->query( "UPDATE " . $wpdb->prefix . "cvg_videos SET video_title = '$desc' WHERE pid = $key");
			}
		}
		return true;
	}
	
	/**
	 * Function to return duration of an uploaded video.
	 * 
	 * @param $videofile
	 * @return duration of VideoSource
	 * @author Praveen Rajan
	 */
	function video_duration($videofile) {
		ob_start ();
		
		$options = get_option('cvg_settings');
		passthru ( $options['cvg_ffmpegpath'] . " -i \"" . $videofile . "\" 2>&1" );
		$duration = ob_get_contents ();
		ob_end_clean ();
		preg_match ( '/Duration: (.*?),/', $duration, $matches );
		
		if(!empty($matches)) {
			$duration = $matches [1];
			return ($duration);	
		}else {
			return 0;
		}
	}
		
	/**
	 * Fucntion to convert time duration in seconds to hours, minutes and seconds
	 * @param - Seconds
	 * @author - Praveen Rajan
	 */
	function secondsToWords($seconds) {
		
	    /*** return value ***/
	    $ret = "";
	
	    /*** get the hours ***/
	    $hours = intval(intval($seconds) / 3600);
	    if($hours > 0)
	    {
	    	if(strlen($hours) == 1)
	        	$ret .= "0$hours:";
			else 
				$ret .= "$hours:";
	    }else {
	    	$ret .= "00:";
	    }
		
	    /*** get the minutes ***/
	    $minutes = bcmod((intval($seconds) / 60),60);
	    if($hours > 0 || $minutes > 0)
	    {
	    	if(strlen($minutes) == 1)
	        	$ret .= "0$minutes:";
			else 
				$ret .= "$minutes:";
			
	    }else {
	    	$ret .= "00:";
		}
	  
	    /*** get the seconds ***/
	    $seconds = bcmod(intval($seconds),60);
		
		if(strlen($seconds) == 1)
        	$ret .= "0$seconds.0";
		else 
			$ret .= "$seconds.0";
			
	    return $ret;
	}
	
	/**
	 * Function to get fileinfo 
	 * 
	 * @param string $name The name being checked. 
	 * @return array containing information about file
	 * author Praveen Rajan
	 */
	function fileinfo( $name ) {
		
		//Sanitizes a filename replacing whitespace with dashes
		$name = sanitize_file_name($name);
		
		//get the parts of the name
		$filepart = pathinfo ( strtolower($name) );
		
		if ( empty($filepart) )
			return false;
		
		if ( empty($filepart['filename']) ) 
			$filepart['filename'] = substr($filepart['basename'],0 ,strlen($filepart['basename']) - (strlen($filepart['extension']) + 1) );
		
		$filepart['filename'] = sanitize_title_with_dashes( $filepart['filename'] );
		
		$filepart['extension'] = $filepart['extension'];	
		//combine the new file name
		$filepart['basename'] = $filepart['filename'] . '.' . $filepart['extension'];
		
		return $filepart;
	}
	
	/**
	 * Scan folder for new videos
	 * 
	 * @param string $dirname
	 * @return array $files list of video filenames
	 * @author Praveen Rajan 
	 */
	function scandir_video( $dirname = '.' ) {
		
		$cool_video_gallery = new CoolVideoGallery();
		$ext = $cool_video_gallery->allowed_extension;  

		$files = array(); 
		if( $handle = opendir( $dirname ) ) { 
			while( false !== ( $file = readdir( $handle ) ) ) {
				$info = pathinfo( $file );
				// just look for video with the correct extension
                if ( isset($info['extension']) )
				    if ( in_array( strtolower($info['extension']), $ext) )
					   $files[] = utf8_encode( $file );
			}		
			closedir( $handle ); 
		} 
		sort( $files );
		return ( $files ); 
	} 
	
	/**
	 * Function to scan video file names
	 * @param $dirname - directory name
	 * @author Praveen Rajan
	 */
	function scandir_video_name( $dirname = '.' ) {
			 
		$cool_video_gallery = new CoolVideoGallery();
		$ext = $cool_video_gallery->allowed_extension; 

		$files = array(); 
		if( $handle = opendir( $dirname ) ) { 
			while( false !== ( $file = readdir( $handle ) ) ) {
				$info = pathinfo( $file );
				// just look for video with the correct extension
                if ( isset($info['extension']) )
				    if ( in_array( strtolower($info['extension']), $ext) )
					   $files[] = utf8_encode( $info['filename'] );
			}		
			closedir( $handle ); 
		} 
		sort( $files );
		return ( $files ); 
	}
	
	/**
	 * Function to check if ffmpeg is installed.
	 * 
	 * @author Praveen Rajan
	 */
	function ffmpegcommandExists() {
		
		$options = get_option('cvg_settings');
		
	    $command = escapeshellarg($options['cvg_ffmpegpath']);
	    exec($command, $output, $return);

		if ($return <= 1) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Function to get webserver information.
	 * author Praveen Rajan
	 */
	function cvg_serverinfo() {
	
		global $wpdb;
		
		// Get MYSQL Version
		$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
		
		// Get PHP Max Upload Size
		$upload_max = wp_convert_bytes_to_hr(wp_max_upload_size());
		
		if (CvgCore::ffmpegcommandExists()) 
		   $ffmpeg = 'Installed';
		else 
		   $ffmpeg = 'Not Installed';
		
		?>
		<li><?php _e('Operating System'); ?> : <span><?php echo PHP_OS; ?>&nbsp;(<?php echo (PHP_INT_SIZE * 8) ?>&nbsp;Bit)</span></li>
		<li><?php _e('Server'); ?> : <span><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></span></li>
		<li><?php _e('MySQL Version'); ?> : <span><?php echo $sqlversion; ?></span></li>
		<li><?php _e('PHP Version'); ?> : <span><?php echo PHP_VERSION; ?></span></li>
		<li><?php _e('PHP Max Upload Size'); ?> : <span><?php echo $upload_max; ?></span></li>
		<li><?php _e('FFMPEG'); ?> : <span><?php echo $ffmpeg; ?></span></li>
		<?php if($ffmpeg == 'Not Installed') {?> 
		<li style="text-align:justify;">
		<span style="color:red;font-weight:normal;">[Note: Preview images for uploaded videos will not be created automatically using FFMPEG. Manually upload preview images for videos.]</span>
		</li>
		<?php } ?>
		<li>
			<?php _e('PHP extension `SimpleXML` : ');
				if(function_exists('simplexml_load_file')){
					echo "<span>Enabled</span>";
				}else {
					echo "<span style='color:red;font-weight:normal;'>Not enabled (Required for Youtube video addition)</span>";
				}
			?>
		</li>
		<li>
			<?php _e('PHP setting `allow_url_fopen` : ');
				if(ini_get('allow_url_fopen')) {
					echo "<span>Enabled</span>";
				}else {
					echo "<span style='color:red;font-weight:normal;'>Not enabled (Required for Youtube video addition)</span>";
				}
			?>
		</li>
		
		<?php
		
	}
	
	/**
	 * Set correct file permissions (taken from wp core)
	 * 
	 * @param string $filename
	 * @return bool $result
	 * @author Praveen Rajan
	 */
	function chmod($filename = '') {

		$stat = @ stat(dirname($filename));
		$perms = $stat['mode'] & 0007777;
		$perms = $perms & 0000666;
		if ( @chmod($filename, $perms) )
			return true;
			
		return false;
	}
	
	/**
	* Show a error messages
	* author Praveen Rajan
	*/
	function show_video_error($message) {
		echo '<div class="wrap"><h2></h2><div class="error" id="error"><p>' . $message . '</p></div></div>' . "\n";
	}
	
	/**
	* Show a system messages
	* author Praveen Rajan
	*/
	function show_video_message($message) {
		echo '<div class="wrap"><h2></h2><div class="updated fade" id="message"><p>' . $message . '</p></div></div>' . "\n";
	}
	
	/**
	 * videoShowGallery() - return a gallery  
	 * 
	 * @param int $galleryID
	 * @param string $template (optional) name for a template file
	 * @param int $videos (optional) number of videos per page
	 * @return the content
	 * @author Praveen Rajan
	 */
	function videoShowGallery( $galleryID, $slide_show = false, $limit= 0, $place_holder = "main" ) {
	    
	    $galleryID = (int) $galleryID;
	    
		CvgCore::scan_upload_videos($galleryID, false);
		
	     $limit_by  = ( $limit > 0 ) ? $limit : 0;
	    
	    // get gallery values
	    $videolist = videoDB::get_gallery($galleryID, false, 'sortorder', 'ASC', $limit_by);
	    $outer = '';
	     
		$options = get_option('cvg_settings');
		
		if(isset($options['cvg_random_video']) && $options['cvg_random_video'] == 1)
			shuffle($videolist);
		
	    if ( !$videolist )
	        return __('[Gallery not found]');

	    if ( is_array($videolist) ) {
	    
	    	$outer .= '<div class="video-gallery-thumbnail-box-outer" id="video-'.$galleryID.'">';
	        $outer .= CvgCore::videoCreateGallery($videolist, $galleryID, $slide_show, $place_holder);
			$outer .= '</div>';
	    }	
        return $outer;
	}
	
	/**
	 * Build a gallery output
	 * 
	 * @param array $videolist
	 * @param bool $galleryID - gallery ID
	 * @param string $template (optional) name for a template file
	 * @param int $videos (optional) number of videos per page
	 * @return the content
	 * @author Praveen Rajan
	 */
	function videoCreateGallery($videolist, $galleryID = false, $slide_show = false, $place_holder = "main") {
	
	    if ( !is_array($videolist) )
	        $videolist = array($videolist);
	       
	    $video_gallery = videoDB::find_gallery($galleryID);
		
	    $video_gallery_name = $video_gallery->name;
		$index = 0;
		$out = '';
		$options = get_option('cvg_settings');
		
		$options_player = get_option('cvg_player_settings');
			
		if($options_player['cvgplayer_autoplay'] == 1)
			$autoplay = "true";
		else 
			$autoplay = "false";	
		
		if($options_player['cvgplayer_mute'] == 1)
			$mute = "true";
		else 
			$mute = "false";
		
		if($options_player['cvgplayer_share_option'] == 1)
			$player_swf = "player-share.swf";
		else 
			$player_swf = "player.swf";
			
		$cool_video_gallery = new CoolVideoGallery();	
		
		if($slide_show){
			$out .= ' <div class="video-gallery-thumbnail-box slide"><ul class="slideContent" id="slide_'.$galleryID.'_'. $place_holder .'">';
		}else {
			if(!empty($video_gallery->galdesc)){
				if($options['cvg_description'] == 1) 	
					$out .= '<div class="clear"></div><div><b>Description:</b> '.$video_gallery->galdesc.'</div><br clear="all"/>';
			}	
		}		
	    foreach ($videolist as $video) {
	
			if($slide_show) {
				$out .= '<li class="slideImage">';
				$out .= $cool_video_gallery->CVGVideo_Parse('[cvg-video videoId='. $video->pid . ' mode="slide_show" placeholder="'.$place_holder.'" /]');
		    	$out .= '<span class="bottom">Click to Play</span></li>';
			}else {	
				$out .= '<div style="float:left;margin-right:10px;"><div class="video-gallery-thumbnail-box" style="padding:0px;" id="vide-file-'.$index.'">';
				$out .= '<div class="video-gallery-thumbnail">';
				$out .= $cool_video_gallery->CVGVideo_Parse('[cvg-video videoId='. $video->pid . ' mode="list_items" placeholder="'.$place_holder.'"/]');
		    	$out .= '</div></div>';
		    	
				$options = get_option('cvg_settings');
				$thumb_width = $options['cvg_preview_width'];
				$thumb_height = $options['cvg_preview_height'];

				$current_video_title = isset($video->video_title) ? $video->video_title : "";
		    	if($options['cvg_description'] == 1) 	
		    		$out .= '<br clear="all"/><div style="text-align:center;width:'.$thumb_width.'px;">'. stripcslashes($current_video_title).'</div><div class="clear"></div></div>';
				else	
			    	$out .= '<div class="clear"></div></div>';
			}	
	    	$index++;
	    }
	    
	    if($slide_show){	
		 $out .= '<div class="clear slideImage"></div></ul></div><div class="clear"></div>';
		 
		 if($options['cvg_description'] == 1) 
		 	$out .= '<div>Description: '.$video_gallery->galdesc.'</div>';
		 	
		 $out .= '<div class="clear" style="min-height:10px;"></div>';	
		 ?>
		 		<script type="text/javascript">
					jQuery(document).ready(function() {
		
						
						jQuery("a[rel=fancy_cvg_gallery_slide_<?php echo $galleryID.'_'.$place_holder;?>]").fancybox({
							'titlePosition' : 'outside',
							'transitionIn' : 'none',
							'transitionOut' : 'none',
							'autoScale' : true,
							'titleFormat' : function(title, currentArray, currentIndex, currentOpts) {
								return '<span id="fancybox-title-over">Video ' + (currentIndex + 1) + ' / ' + currentArray.length + '</span><span class="fancybox-title-top">' + (title.length ?  jQuery.stripslashes(title) : '') + '</span>';
							},
							'content' : '<div id="video_fancy_cvg_slide_gallery_<?php echo $galleryID."_".$place_holder;?>" style="overflow:hidden;"></div>',
	
							'autoDimensions' : false,
							'width' : parseInt("<?php echo $options_player['cvgplayer_width']; ?>"),
							'height' :  parseInt("<?php echo $options_player['cvgplayer_height']; ?>") + 6,
							'padding' : 10,
							'onComplete' : function() {
								
								
								jwplayer('video_fancy_cvg_slide_gallery_<?php echo $galleryID.'_'.$place_holder;?>').setup({
									'file' : this.href,
									"autostart" : "<?php echo $autoplay;?>",
									"controlbar" : "<?php echo $options_player['cvgplayer_controlbar']; ?>",
									"flashplayer" : "<?php echo $cool_video_gallery->plugin_url . "cvg-player/" . $player_swf; ?>",
									"volume" : "<?php echo $options_player['cvgplayer_volume']; ?>",
									"width" : "<?php echo $options_player['cvgplayer_width']; ?>",
									"height" : "<?php echo $options_player['cvgplayer_height']; ?>",
									"mute" : "<?php echo $mute; ?>",
									"stretching" : "<?php echo $options_player['cvgplayer_stretching']; ?>",
									"skin" : "<?php echo $cool_video_gallery->video_player_url . 'skins/' . $options_player['cvgplayer_skin'] . '-skin/' . $options_player['cvgplayer_skin']  . '.xml' ?>"
																		
								});
								
								jwplayer('video_fancy_cvg_slide_gallery_<?php echo $galleryID.'_'.$place_holder;?>').onComplete(function() {
									
									<?php 
										if ($options_player['cvgplayer_autoplay']) {
										?>
											jQuery.fancybox.next();
										<?php } 
									?>
								});
							}
						});
						
					});
	
				</script>
		 
		 <?php
		}else {
		 	$out .= '<div class="clear"></div>';
		 	?>
		 	<script type="text/javascript">
				jQuery(document).ready(function() {
						jQuery("a[rel=fancy_cvg_gallery_<?php echo $galleryID.'_'.$place_holder;?>]").fancybox({
							'titlePosition' : 'outside',
							'transitionIn' : 'none',
							'transitionOut' : 'none',
							'autoDimensions' : false,
	
							'titleFormat' : function(title, currentArray, currentIndex, currentOpts) {
								return '<span id="fancybox-title-over">Video ' + (currentIndex + 1) + ' / ' + currentArray.length + '</span><span class="fancybox-title-top">' + (title.length ?  jQuery.stripslashes(title)  : '') + '</span>';
							},
							'content' : '<div id="video_fancy_cvg_items_gallery_<?php echo $galleryID.'_'.$place_holder;?>" style="overflow:hidden;"></div>',
	
							'width' : parseInt("<?php echo $options_player['cvgplayer_width'] ; ?>") ,
							'height' :  parseInt("<?php echo $options_player['cvgplayer_height']; ?>") + 6 ,
							'padding' : 10,
							'onComplete' : function() {
								
								jwplayer('video_fancy_cvg_items_gallery_<?php echo $galleryID.'_'.$place_holder;?>').setup({
									'file' : this.href,
									"autostart" : "<?php echo $autoplay;?>",
									"controlbar" : "<?php echo $options_player['cvgplayer_controlbar']; ?>",
									"flashplayer" : "<?php echo $cool_video_gallery->plugin_url . "cvg-player/" . $player_swf; ?>",
									"volume" : "<?php echo $options_player['cvgplayer_volume']; ?>",
									"width" : "<?php echo $options_player['cvgplayer_width']; ?>",
									"height" : "<?php echo $options_player['cvgplayer_height']; ?>",
									"mute" : "<?php echo $mute; ?>",
									"stretching" : "<?php echo $options_player['cvgplayer_stretching']; ?>",
									
									"skin" : "<?php echo $cool_video_gallery->video_player_url . 'skins/' . $options_player['cvgplayer_skin'] . '-skin/' . $options_player['cvgplayer_skin']  . '.xml' ?>"
								});
								
								jwplayer('video_fancy_cvg_items_gallery_<?php echo $galleryID.'_'.$place_holder;?>').onComplete(function() {
									
									<?php 
										if ($options_player['cvgplayer_autoplay']) {
										?>
											jQuery.fancybox.next();
										<?php } 
									?>
									
								});
							}
						});
					});
	
				</script>
		 
		 <?php
		 
		}
		return $out;
	}

	/**
	 * Function to upgrade plugin tables
	 * 
	 * @author Praveen Rajan
	 */
	function upgrade_plugin() {
		
		global $wpdb;

		$cool_video_gallery = new CoolVideoGallery();
		$installed_ver = get_option( "cvg_version" );
		$sub_name_videos = 'cvg_videos';
	    $sub_name_gallery = 'cvg_gallery';
	    $table_videos = $wpdb->prefix . $sub_name_videos;
		$table_gallery = $wpdb->prefix . $sub_name_gallery;
			
		if (version_compare($installed_ver, '1.5', '<')) {
			
			$sql_update = "ALTER TABLE " .  $table_videos . " ADD `video_type` varchar( 20 ) NOT NULL DEFAULT '". $cool_video_gallery->video_type_upload . "' AFTER `meta_data`" ;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$wpdb->query($wpdb->prepare($sql_update, null));
		}

		if (version_compare($installed_ver, '1.7', '<')) {
			
			$sql_update = "ALTER TABLE " .  $table_videos . " ADD `video_title` varchar( 20 ) NULL AFTER `thumb_filename`" ;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$wpdb->query($wpdb->prepare($sql_update, null));
		}

		if (version_compare($installed_ver, '1.8', '<')) {
			
			$sql_update = "ALTER TABLE " .  $table_videos . " ADD `exclude` tinyint(5) NOT NULL DEFAULT '0' AFTER `video_type`" ;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$wpdb->query($wpdb->prepare($sql_update, null));
			
			$sql_update_video_text = "ALTER TABLE " .  $table_videos . " MODIFY `video_title` mediumtext" ;
			$wpdb->query($wpdb->prepare($sql_update_video_text, null));
		}

		if($installed_ver != $cool_video_gallery->cvg_version) {
			
			update_option('cvg_version', $cool_video_gallery->cvg_version);
		}
		
	     $sql_collation_update_videos = "ALTER TABLE " . $table_videos . " CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
		 require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		 $wpdb->query($sql_collation_update_videos);
		 
		 $sql_collation_update_gallery = "ALTER TABLE " .  $table_gallery . " CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
		 $wpdb->query($sql_collation_update_gallery);

	}
	
	/**
	 * Function to publish videos as post.
	 * 
	 * @author Praveen Rajan
	 */
	function publish_video_post() {
		
		global $user_ID;
		
		if(isset($_POST['post_title']) && $_POST['post_title'] == "") {
			
			CvgCore::show_video_error(__('Please provide a title for Post'));
			return;	
		}
		
		if($_POST['width'] == "" || $_POST['height'] == "") {
			
			CvgCore::show_video_error(__('Width/Height not set properly.'));
			return;
		}
		
		$width  = (int) $_POST['width'];
		$height = (int) $_POST['height'];

		$mode = "";
		
		if(isset($_POST['showtypevideo']) && $_POST['showtypevideo'] == "embed") {
			$mode = "mode='playlist'"; 
		}

		$post['post_type']    = 'post';
		$post['post_content'] = "[cvg-video videoId='". $_POST['videosingle_publish'] ."' width='$width' height='$height' $mode/]";
		$post['post_author']  = $user_ID;
		$post['post_status']  = isset ( $_POST['publish'] ) ? 'publish' : 'draft';
		$post['post_title']   = $_POST['post_title'];

		$post_id = wp_insert_post ($post);
        
		if ($post_id != 0)
            CvgCore::show_video_message( __('Published a new post') );
	}

	/**
	 * Function to move video file and thumbnail from one gallery folder to another.
	 * 
	 * @param $vid - Video ID
	 * @param $gid - Gallery ID
	 * @author Praveen Rajan 
	 */
	function move_video($vid, $gid) {
		
		$details = videoDB::find_video($vid);
		$video_details = $details[0];
		
		if($video_details->video_type == 'upload') {
			
			$source_video_file = $this->winabspath . $video_details->path . '/' . $video_details->filename;
			$source_thumb_file = $this->winabspath . $video_details->path .  '/thumbs/' . $video_details->thumb_filename;
			
			$gallery_details = videoDB::find_gallery($gid);
			
			$dest_video_file = $gallery_details->abspath . '/' . $video_details->filename;
			$dest_thumb_file = $gallery_details->abspath . '/thumbs/' . $video_details->thumb_filename;
			
			if (file_exists($source_video_file)) {
				if (copy($source_video_file, $dest_video_file)) {
					
			       unlink($source_video_file);
			    }
			}
			
			if (file_exists($source_thumb_file)) {
				if (copy($source_thumb_file, $dest_thumb_file)) {
					
			       unlink($source_thumb_file);
			    }
			}
			
		}else {
			return;
		}
	}
}

/**
 * Function to override bcmod
 */
if( !function_exists('bcmod') ) {
	
	/**
	 * by Andrius Baranauskas and Laurynas Butkus
	 **/
	function bcmod( $x, $y )
	{
	    $take = 5;
	    $mod = ''; 
	
	    do
	    {
	        $a = (int)$mod.substr( $x, 0, $take );
	        $x = substr( $x, $take );
	        $mod = $a % $y;
	    }
	    while ( strlen($x) ); 
	
	    return (int)$mod;
	}
}
?>