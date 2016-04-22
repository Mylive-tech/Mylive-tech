<?php if(!defined('ABSPATH')) die('not allowed');


class mymail_templates {


	public $path;
	public $url;

	private $download_url = 'https://bitbucket.org/revaxarts/mymail-template/get/master.zip';
	private $headers = array(
			'name' => 'Template Name',
			'label' => 'Name',
			'uri' => 'Template URI',
			'description' => 'Description',
			'author' => 'Author',
			'author_uri' => 'Author URI',
			'version' => 'Version',
	);

	public function __construct() {

		$this->path = MYMAIL_UPLOAD_DIR.'/templates';
		$this->url = MYMAIL_UPLOAD_URI.'/templates';

		register_activation_hook(MYMAIL_FILE, array( &$this, 'activate'));

		add_action('init', array( &$this, 'init' ) );

		//delete_option('mymail_other_templates' );

	}

	public function init() {

		add_action('admin_menu', array( &$this, 'admin_menu' ), 50);
		add_action('wp_update_themes', array( &$this, 'get_other_templates'));

	}

	public function admin_menu() {

		if($updates =  $this->get_updates()){
			$updates = ' <span class="update-plugins count-'.$updates.'" title="'.sprintf( _n( '%d Update available', '%d Updates available', $updates, 'mymail'), $updates).'"><span class="update-count">'.$updates.'</span></span>';
		}else{
			$updates = '';
		}

		$page = add_submenu_page( 'edit.php?post_type=newsletter', __( 'Templates', 'mymail' ), __( 'Templates', 'mymail' ).$updates, 'mymail_manage_templates', 'mymail_templates', array( &$this, 'templates' )  );
		add_action( 'load-'.$page, array( &$this, 'scripts_styles' ) );
		add_action( 'load-'.$page, array( &$this, 'edit_entry'), 99);

	}



	public function get_path() {
		return $this->path;
	}

	public function get_url() {
		return $this->url;
	}



	public function remove_template($slug = '') {

		$this->templatepath = $this->path .'/' . $slug;

		if ( !file_exists( $this->templatepath . '/index.html' ) )
			return false;

		mymail_require_filesystem();

		global $wp_filesystem;
		return $wp_filesystem->delete($this->templatepath, true);
	}


	public function unzip_template($templatefile, $uploadfolder = NULL, $renamefolder = NULL, $overwrite = false) {

		global $wp_filesystem;

		mymail_require_filesystem();

		if(is_null($uploadfolder)) $uploadfolder = MYMAIL_UPLOAD_DIR.'/uploads';

		if(!is_dir($uploadfolder)) wp_mkdir_p($uploadfolder);

		if(!unzip_file($templatefile, $uploadfolder)){
			$wp_filesystem->delete($uploadfolder, true);
			return new WP_Error('unzip', __('Unable to unzip template', 'mymail'));
		}

		$templates = $this->get_templates(true);

		if($folders = scandir($uploadfolder)){

			foreach($folders as $folder){
				if(in_array($folder, array('.', '..')) || !is_dir($uploadfolder.'/'.$folder)) continue;

				if(!is_null($renamefolder)){

					$renamefolder = sanitize_file_name($renamefolder);

					if($wp_filesystem->move($uploadfolder.'/'.$folder, $uploadfolder.'/'.$renamefolder, true)){
						$folder = $renamefolder;
					}else{
						$wp_filesystem->delete($uploadfolder, true);
						return new WP_Error('not_writeable', __('Unable to save template', 'mymail'));
					}
				}

				$templateslug = $folder;

				if(!$overwrite && in_array($templateslug, $templates)){

					$data = $this->get_template_data($uploadfolder.'/'.$folder.'/index.html');

					$wp_filesystem->delete($uploadfolder, true);

					return new WP_Error('template_exists', sprintf(__('Template %s already exists!', 'mymail'), '"'.$data['name'].'"'));

				}


				//need index.html file
				if(file_exists($uploadfolder.'/'.$folder.'/index.html')){
					$data = $this->get_template_data($uploadfolder.'/'.$folder.'/index.html');

					$files = list_files($uploadfolder.'/'.$folder);

					foreach($files as $file){
						//remove unallowed files
						if(is_file($file) && !preg_match('#\.(html|gif|png|jpg|jpeg|tiff)$#', $file))
							$wp_filesystem->delete($file, true);
					}

					//with name value
					if(!empty($data['name'])){
						wp_mkdir_p($this->path .'/'.$folder);
						copy_dir($uploadfolder.'/'.$folder, $this->path .'/'.$folder);
					}else{
						$wp_filesystem->delete($uploadfolder, true);
						return new WP_Error('wrong_header', __('The header of this template files is missing or corrupt', 'mymail'));
					}


				}else{

					$wp_filesystem->delete($uploadfolder, true);
					return new WP_Error('wrong_file', __('This is not a valid MyMail template ZIP', 'mymail'));

				}

				if(file_exists($uploadfolder.'/'.$folder.'/colors.json')){

					$colors = $wp_filesystem->get_contents($uploadfolder.'/'.$folder.'/colors.json');

					if($colors){
						$colorschemas = json_decode($colors);

						$customcolors = get_option('mymail_colors', array());

						if(!isset($customcolors[$folder])){

							$customcolors[$folder] = array();
							foreach($colorschemas as $colorschema){
								$hash = md5(implode('', $colorschema));
								$customcolors[$folder][$hash] = $colorschema;
							}

							update_option('mymail_colors', $customcolors);

						}


					}
				}
			}

			$wp_filesystem->delete($uploadfolder, true);

			if($templateslug){

				//force a reload
				$this->get_other_templates($templateslug, true);

				return true;
			}

		}

		return new WP_Error('wrong_file', __('This is not a valid MyMail template ZIP', 'mymail'));

	}


	public function renew_default_template($slug = 'mymail') {

		$zip = download_url( $this->download_url, 60);

		if ( is_wp_error( $zip ) ) {
			die($zip->get_error_message());
		}

		$tempfolder = MYMAIL_UPLOAD_DIR.'/uploads';
		if(!is_dir($tempfolder)) wp_mkdir_p($tempfolder);

		return $this->unzip_template($zip, $tempfolder, $slug);

	}


	public function templates() {

		if(current_user_can('mymail_upload_templates')){
			remove_action('post-plupload-upload-ui', 'media_upload_flash_bypass');
			wp_enqueue_script('plupload-all');
		}

		include MYMAIL_DIR . 'views/templates.php';

	}

	/*----------------------------------------------------------------------*/
	/* AJAX
	/*----------------------------------------------------------------------*/



	private function ajax_nonce($return = NULL, $nonce = 'mymail_nonce') {
		if (!wp_verify_nonce($_REQUEST['_wpnonce'], $nonce)) {
			die( $return );
		}

	}

	private function ajax_filesystem() {
		if('ftpext' == get_filesystem_method() && (!defined('FTP_HOST') || !defined('FTP_USER') || !defined('FTP_PASS'))){
			$return['msg'] = __('WordPress is not able to access to your filesystem!', 'mymail');
			$return['msg'] .= "\n".sprintf(__('Please add following lines to the wp-config.php %s', 'mymail'), "\n\ndefine('FTP_HOST', 'your-ftp-host');\ndefine('FTP_USER', 'your-ftp-user');\ndefine('FTP_PASS', 'your-ftp-password');\n");
			$return['success'] = false;
			echo json_encode( $return );
			exit;
		}

	}
	/*----------------------------------------------------------------------*/
	/* Filters
	/*----------------------------------------------------------------------*/

	public function get_templates($slugsonly = false) {

		$templates = array();
		$files = list_files($this->path);
		sort($files);

		foreach($files as $file){
			if(basename($file) == 'index.html'){

				$filename = str_replace($this->path .'/', '', $file);
				$slug = dirname($filename);
				if(!$slugsonly){
					$templates[$slug] = $this->get_template_data($file);
				}else{
					$templates[] = $slug;
				}
			}
		}
		ksort($templates);
		return $templates;

	}

	public function get_all_files() {

		$templates = $this->get_templates();

		$files = array();

		foreach($templates as $slug => $data){
			$files[$slug] = $this->get_files($slug);
		}

		return $files;


	}

	public function get_files($slug = '') {

		if(empty($slug)) return array();

		$templates = array();
		$files = list_files($this->path .'/'.$slug, 1);

		sort($files);

		$list = array(
			'index.html' => $this->get_template_data($this->path .'/'.$slug .'/index.html'),
		);

		if(file_exists($this->path .'/'.$slug .'/notification.html'))
			$list['notification.html'] = $this->get_template_data($this->path .'/'.$slug .'/notification.html');

		foreach($files as $file){

			if(strpos($file, '.html') && is_file($file)) $list[basename($file)] = $this->get_template_data($file);

		}

		return $list;

	}

	public function get_versions($slug = NULL) {

		$templates = $this->get_templates();
		$versions = array();
		foreach($templates as $s => $data){

			$versions[$s] = $data['version'];
		}

		return !is_null($slug) ? (isset($versions[$slug]) ? $versions[$slug] : NULL) : $versions;

	}

	public function get_updates() {

		if(true || !current_user_can('mymail_update_templates')) return 0;

		return array_sum(wp_list_pluck($this->get_other_templates(), 'update' ));

	}


	public function get_raw_template( $file = 'index.html') {
		if ( !file_exists( $this->path .'/' . $this->slug . '/' .$file) )
			return false;

		return file_get_contents( $this->path .'/' . $this->slug . '/'. $file );
	}



	/*----------------------------------------------------------------------*/
	/* Styles & Scripts
	/*----------------------------------------------------------------------*/


	public function scripts_styles() {

		wp_register_style('mymail-templates', MYMAIL_URI . 'assets/css/templates-style.css', array(), MYMAIL_VERSION);
		wp_enqueue_style('mymail-templates');
		wp_enqueue_style('mymail-codemirror', MYMAIL_URI . 'assets/css/codemirror.css', array(), MYMAIL_VERSION);
		wp_enqueue_script('mymail-codemirror', MYMAIL_URI . 'assets/js/codemirror.js', array(), MYMAIL_VERSION);
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
		wp_register_script('mymail-templates', MYMAIL_URI . 'assets/js/templates-script.js', array('jquery'), MYMAIL_VERSION);
		wp_enqueue_script('mymail-templates');
		wp_localize_script('mymail-templates', 'mymailL10n', array(
			'delete_template_file' => __('Do you really like to remove file %s from template %s?', 'mymail'),
			'enter_template_name' => __('Please enter the name of the new template', 'mymail'),
			'uploading' => __('uploading zip file %s', 'mymail'),
			'enter_license' => __('Please enter a valid Licensecode!', 'mymail'),
			'update_note' => __('All template files will get overwritten if you proceed! Please make sure you have a backup of your files.', 'mymail'),
		));


	}



	public function edit_entry( ) {

		if(isset($_GET['action'])){

			$templates = $this->get_templates();

			switch($_GET['action']){

				case 'activate':

					$slug = esc_attr($_GET['template']);
					if (isset($templates[$slug]) && wp_verify_nonce($_GET['_wpnonce'], 'activate-'.$slug) && current_user_can('mymail_manage_templates') ){

						if(mymail_update_option('default_template', esc_attr($_GET['template']))){
							mymail_notice(sprintf(__('Template %s is now your default template', 'mymail'), '"'.$templates[$slug]['name'].'"'), 'updated', true);
							wp_redirect( 'edit.php?post_type=newsletter&page=mymail_templates' );
							exit;
						}
					}
					break;

				case 'delete':

					$slug = esc_attr($_GET['template']);
					if (isset($templates[$slug]) && wp_verify_nonce($_GET['_wpnonce'], 'delete-'.$slug) && current_user_can('mymail_delete_templates')){

						if($slug == mymail_option('default_template')){
							mymail_notice(sprintf(__('Cannot delete the default template %s', 'mymail'), '"'.$templates[$slug]['name'].'"'), 'error', true);
						}else if($this->remove_template($slug)){
							mymail_notice(sprintf(__('Template %s has been deleted', 'mymail'), '"'.$templates[$slug]['name'].'"'), 'updated', true);
							$templates = $this->get_templates();
						}else{
							mymail_notice(sprintf(__('Template %s has not been deleted', 'mymail'), '"'.$templates[$slug]['name'].'"'), 'error', true);
						}
						//force a reload
						$this->get_other_templates($slug, true);
						wp_redirect( 'edit.php?post_type=newsletter&page=mymail_templates' );
						exit;

					}
					break;

				case 'download':
				case 'update':

					$slug = esc_attr($_GET['template']);

					if (wp_verify_nonce($_GET['_wpnonce'], 'download-'.$slug) && current_user_can('mymail_manage_templates')){

						if($template = $this->get_other_templates($slug)){

							$this->download_slug = $slug;

							//if(isset($_GET['license'])) $this->update_license($slug, $_GET['license']);

							add_filter( 'http_request_args', array( &$this, 'download_http_request_args' ), 100, 2 );
							$tempfile = download_url( $template['download_url'], 3000 );
							remove_filter( 'http_request_args', array( &$this, 'download_http_request_args' ), 100, 2 );

							if(is_wp_error( $tempfile )){
								($tempfile->get_error_code() == 'http_404' && !$tempfile->get_error_message())
									? mymail_notice('[ 404 ] '.sprintf(__('File does not exist. Please contact %s for help!', 'mymail'), '<a href="'.$template['author_uri'].'">'.$template['author'].'</a>'), 'error', true)
									: mymail_notice(sprintf(__('There was an error: %s', 'mymail'), '"'.$tempfile->get_error_message().'"'), 'error', true);

									$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'edit.php?post_type=newsletter&page=mymail_templates&more';
							}else{

								$result = $this->unzip_template( $tempfile, NULL, NULL, true );

								if(is_wp_error( $result )){
									mymail_notice(sprintf(__('There was an error: %s', 'mymail'), '"'.$result->get_error_message().'"'), 'error', true);

									$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'edit.php?post_type=newsletter&page=mymail_templates&more';

								}else if($result){
									($type == 'update')
										? mymail_notice(__('Template succesfull updated!', 'mymail'), 'updated', true)
										: mymail_notice(__('Template succesfull loaded!', 'mymail'), 'updated', true);
								}

								//force a reload
								$this->get_other_templates($slug, true);
								$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'edit.php?post_type=newsletter&page=mymail_templates';
								$redirect = add_query_arg(array('new' => $slug), $redirect);

							}
							@unlink($tempfile);

						}

						wp_redirect( $redirect );
						exit;

					}
					break;

				case 'license':

					$slug = esc_attr($_GET['template']);

					if (wp_verify_nonce($_GET['_wpnonce'], 'license-'.$slug) && current_user_can('mymail_manage_templates')){

						if($template = $this->get_other_templates($slug)){

							$this->download_slug = $slug;

							if($this->update_license($slug, $_GET['license'])){

								mymail_notice(__('Licensecode has been changed!', 'mymail'), 'updated', true);

							}else{

							}

						}

						$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'edit.php?post_type=newsletter&page=mymail_templates&more';
						wp_redirect( $redirect );
						exit;

					}
					break;

			}
		}

	}

	public function download_http_request_args($r, $url) {

		global $wp_header_to_desc;

		$wp_header_to_desc[678] = 'Licensecode missing!';
		$wp_header_to_desc[679] = 'Licensecode invalid!';
		$wp_header_to_desc[680] = 'Licensecode already in use!';

		include ABSPATH . WPINC . '/version.php';

		if(!$wp_version) global $wp_version;

		$template = $this->get_other_templates($this->download_slug);

		$version = $this->get_versions($this->download_slug);

		$body = array(
			'licensecode' => $template['licensecode'],
			'version' => $version,
			'wp-version' => $wp_version,
			'referer' => untrailingslashit('http://'.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'],'', ABSPATH)),
			'multisite' => is_multisite()
		);

		$r['method'] = 'POST';
		$r['headers'] = array(
			'Content-Type' => 'application/x-www-form-urlencoded',
			'Content-Length' => strlen( $body ),
			'X-ip' => $_SERVER['SERVER_ADDR'],
		);
		$r['body'] = $body;

		return $r;

	}


	private function update_license( $slug, $license ) {

		$templates = $this->get_other_templates();

		$templates[$slug]['licensecode'] = $license;

		update_option( 'mymail_other_templates', array(
			'timestamp' => time()-3600,
			'templates' => $templates
		));

		return true;

	}


	/*----------------------------------------------------------------------*/
	/* Other
	/*----------------------------------------------------------------------*/


	public function get_screenshot( $slug, $file = 'index.html', $size = 600 ) {

		global $wp_filesystem;

		$fileuri = $this->url .'/'.$slug.'/'.$file;
		$screenshotfile = MYMAIL_UPLOAD_DIR.'/screenshots/'.$slug.'_'.$file.'.jpg';
		$screenshoturi = MYMAIL_UPLOAD_URI.'/screenshots/'.$slug.'_'.$file.'.jpg';
		$file = $this->path .'/'.$slug.'/'.$file;

		//serve saved
		if(file_exists($screenshotfile) && file_exists($file) && filemtime($file) < filemtime($screenshotfile)){
			$url = $screenshoturi.'?c='.filemtime($screenshotfile);
		}else if(!file_exists($file)){
			$url = 'https://data.newsletter-plugin.com/preview/not_available.gif';
		}else if(substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.' || $_SERVER['REMOTE_ADDR'] == '::1'){
			$url = 'https://data.newsletter-plugin.com/preview/not_available.gif';
		}else{
			$url = 'http://s.wordpress.com/mshots/v1/'.(rawurlencode($fileuri.'?c='.md5_file($file))).'?w='.$size;

			$remote = wp_remote_get($url, array('redirection' => 0));

			if(wp_remote_retrieve_response_code($remote) == 200){

				$data = wp_remote_retrieve_body($remote);

				mymail_require_filesystem();

				if(!is_dir( dirname($screenshotfile) )) wp_mkdir_p( dirname($screenshotfile) ) ;

				$wp_filesystem->put_contents($screenshotfile, wp_remote_retrieve_body($remote), false );
			}

			$url = 'https://data.newsletter-plugin.com/preview/create.gif';

		}
		return $url;
	}





	/*----------------------------------------------------------------------*/
	/* Activation
	/*----------------------------------------------------------------------*/



	public function activate() {

		add_action('shutdown', array( &$this, 'copy_templates'), 99 );

	}

	public function copy_templates() {

		global $wpdb;

		if (is_network_admin() && is_multisite()) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		}else{

			$blogids = array(false);

		}

		mymail_require_filesystem();

		foreach ($blogids as $blog_id) {

			if($blog_id) switch_to_blog( $blog_id );

			$upload_folder = wp_upload_dir();

			if(!is_dir( $upload_folder['basedir'].'/myMail/templates' )){
				wp_mkdir_p(  $upload_folder['basedir'].'/myMail/templates' );
				copy_dir(MYMAIL_DIR . 'templates', $upload_folder['basedir'].'/myMail/templates' );
			}
		}

		if($blog_id) switch_to_blog($old_blog);


	}





	/*----------------------------------------------------------------------*/
	/* Privates
	/*----------------------------------------------------------------------*/



	private function get_html_from_nodes($nodes, $separator = ''){

		$parts = array();

		if(!$nodes) return '';
		foreach ($nodes as $node) {
			$parts[] = $this->get_html_from_node($node);
		}

		return implode($separator, $parts);
	}

	private function get_html_from_node($node){

		$html = $node->ownerDocument->saveXML($node);
		return $html;

	}


	private function dom_rename_element(DOMElement $node, $name, $attributes = true) {
		$renamed = $node->ownerDocument->createElement($name);

		if($attributes){
			foreach ($node->attributes as $attribute) {
				$renamed->setAttribute($attribute->nodeName, $attribute->nodeValue);
			}
		}
		while ($node->firstChild) {
			$renamed->appendChild($node->firstChild);
		}

		return $node->parentNode->replaceChild($renamed, $node);
	}


	public function get_template_data($file) {

		$basename = false;
		if(!file_exists($file) && is_string($file)){
			$file_data = $file;
		}else{
			$basename = basename($file);
			$fp = fopen( $file, 'r' );
			$file_data = fread( $fp, 2048 );
			fclose( $fp );
		}

		foreach ( $this->headers as $field => $regex ) {
			preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
			if ( !empty( ${$field} ) )
				${$field} = _cleanup_header_comment( ${$field}[1] );
			else
				${$field} = '';

		}

		$file_data = compact( array_keys( $this->headers ) );
		if(empty($file_data['label'])) $file_data['label'] = $file_data['name'];

		if($basename == 'index.html') $file_data['label'] = __('Base', 'mymail');
		if($basename == 'notification.html') $file_data['label'] = __('Notification', 'mymail');

		if(empty($file_data['label'])) $file_data['label'] = substr($basename, 0, strrpos($basename, '.'));

		//if(empty($file_data['name'])) $file_data['name'] = ucwords(basename(dirname($file)));

		return $file_data;

	}



	public function get_other_templates( $slug = NULL, $force = false ) {

		return array();

		$other_templates = get_option('mymail_other_templates', array('timestamp' => 0, 'templates' => array()));


		if(time()-$other_templates['timestamp'] <= 43200 && !$force){
		//if(time()-$other_templates['timestamp'] <= 60 && !$force){
			$templates = $other_templates['templates'];
			return !is_null($slug) && isset($templates[$slug]) ? $templates[$slug] : $templates;
		}

		$url = 'http://data.newsletter-plugin.com/templates.json';

		$response = wp_remote_get( $url, array('timeout' => 3));

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( $response_code != 200 || is_wp_error( $response ) ) {
			$templates = array();
		}else{
			$templates = json_decode($response_body, true);

			$templates = $this->get_other_templates_info($templates);

			update_option( 'mymail_other_templates', array(
				'timestamp' => time(),
				'templates' => $templates
			));
		}

		return !is_null($slug) && isset($templates[$slug]) ? $templates[$slug] : $templates;

	}


	private function get_other_templates_info( $other_templates ) {

		$endpoints = wp_list_pluck( $other_templates, 'endpoint' );
		$collection = array();
		foreach($endpoints as $slug => $endpoint){
			if(!isset($collection[$endpoint])) $collection[$endpoint] = array();

			$collection[$endpoint][] = $slug;

		}

		$old = get_option('mymail_other_templates');

		$versions = $this->get_versions();
		foreach ($collection as $endpoint => $slugs) {

			$remote_url = trailingslashit($endpoint);

			$body = http_build_query( array('updatecenter_data' => $slugs), null, '&' );
			$post = array(
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Content-Length' => strlen( $body ),
					'X-ip' => $_SERVER['SERVER_ADDR'],
					),
				'body' => $body,
				'timeout' => 3
			);

			$response = wp_remote_post( add_query_arg(array(
				'updatecenter_action' => 'versions',
				'updatecenter_slug' => $slugs,
			), $remote_url), $post );

			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = trim( wp_remote_retrieve_body( $response ) );

			if ( $response_code != 200 || is_wp_error( $response ) ) {
				continue;
			}else{
				$response = json_decode($response_body, true);

				foreach ($slugs as $i => $slug) {
					$other_templates[$slug]['version'] = isset($versions[$slug]) ? $versions[$slug] : NULL;
					if(gettype($response) != 'array' || empty($response[$i])){
						unset($other_templates[$slug]);
						continue;
					}
					$other_templates[$slug]['new_version'] = $response[$i]['version'];
					$other_templates[$slug]['update'] = isset($versions[$slug]) && version_compare($response[$i]['version'],$versions[$slug], '>');
					$other_templates[$slug]['author'] = $response[$i]['author'];
					$other_templates[$slug]['author_profile'] = $response[$i]['author_profile'];
					$other_templates[$slug]['homepage'] = $response[$i]['homepage'];
					$other_templates[$slug]['description'] = $response[$i]['description'];
					$other_templates[$slug]['download_url'] = $response[$i]['download_link'];
					$other_templates[$slug]['licensecode'] = $old && isset($old['templates'][$slug]) ? $old['templates'][$slug]['licensecode']: NULL;

					//$other_templates[$slug]['response'] = $response[$i];
				}

			}

		}

		return $other_templates;

	}

	public function media_upload_form( $errors = null ) {

		global $type, $tab, $pagenow, $is_IE, $is_opera;

		if ( function_exists('_device_can_upload') && ! _device_can_upload() ) {
			echo '<p>' . __('The web browser on your device cannot be used to upload files. You may be able to use the <a href="http://wordpress.org/extend/mobile/">native app for your device</a> instead.', 'mymail') . '</p>';
			return;
		}

		$upload_size_unit = $max_upload_size = wp_max_upload_size();
		$sizes = array( 'KB', 'MB', 'GB' );

		for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) {
			$upload_size_unit /= 1024;
		}

		if ( $u < 0 ) {
			$upload_size_unit = 0;
			$u = 0;
		} else {
			$upload_size_unit = (int) $upload_size_unit;
		}
	?>

	<div id="media-upload-notice"><?php

		if (isset($errors['upload_notice']) )
			echo $errors['upload_notice'];

	?></div>
	<div id="media-upload-error"><?php

		if (isset($errors['upload_error']) && is_wp_error($errors['upload_error']))
			echo $errors['upload_error']->get_error_message();

	?></div>
	<?php
	if ( is_multisite() && !is_upload_space_available() ) {
		return;
	}

	$post_params = array(
			"action" => "mymail_template_upload_handler",
			"_wpnonce" => wp_create_nonce('mymail_nonce'),
	);
	$upload_action_url = admin_url('admin-ajax.php');


	$plupload_init = array(
		'runtimes' => 'html5,silverlight,flash,html4',
		'browse_button' => 'plupload-browse-button',
		'container' => 'plupload-upload-ui',
		'drop_element' => 'drag-drop-area',
		'file_data_name' => 'async-upload',
		'multiple_queues' => true,
		'max_file_size' => $max_upload_size . 'b',
		'url' => $upload_action_url,
		'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
		'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
		'filters' => array( array('title' => __( 'MyMail Template ZIP file', 'mymail' ), 'extensions' => 'zip') ),
		'multipart' => true,
		'urlstream_upload' => true,
		'multipart_params' => $post_params,
		'multi_selection' => false
	);

	?>

	<script type="text/javascript">
	var wpUploaderInit = <?php echo json_encode($plupload_init); ?>;
	</script>

	<div id="plupload-upload-ui" class="hide-if-no-js">
	<div id="drag-drop-area">
		<div class="drag-drop-inside">
		<p class="drag-drop-info"><?php _e('Drop your ZIP file here to upload new template', 'mymail'); ?></p>
		<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files', 'mymail'); ?></p>
		<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select File', 'mymail'); ?>" class="button" /></p>
		<p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %s.', 'mymail' ), esc_html($upload_size_unit.$sizes[$u]) ); ?></p>
		<p class="uploadinfo"></p>
		</div>
	</div>
	</div>

	<div id="html-upload-ui" class="hide-if-js">
		<p id="async-upload-wrap">
			<label class="screen-reader-text" for="async-upload"><?php _e('Upload', 'mymail'); ?></label>
			<input type="file" name="async-upload" id="async-upload" />
			<?php submit_button( __( 'Upload', 'mymail' ), 'button', 'html-upload', false ); ?>
			<a href="#" onclick="try{top.tb_remove();}catch(e){}; return false;"><?php _e('Cancel', 'mymail'); ?></a>
		</p>
		<div class="clear"></div>
	</div>

	<?php
	if ( ($is_IE || $is_opera) && $max_upload_size > 100 * 1024 * 1024 ) { ?>
		<span class="big-file-warning"><?php _e('Your browser has some limitations uploading large files with the multi-file uploader. Please use the browser uploader for files over 100MB.', 'mymail'); ?></span>
	<?php }

	}


}
?>
