<?php
add_action('login_head', 'action_login_head');
function action_login_head() {
	// Custom login screen
	echo '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri().'/custom/livetech-login-styles.css" />';
	// login screen font
	echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,700" rel="stylesheet" type="text/css">';
}

//login home link
add_filter( 'login_headerurl', 'filter_login_headerurl' );
function filter_login_headerurl($url) {
	return  'https://www.mylive-tech.com' ;
}

add_action('get_header', 'action_get_header');
function action_get_header() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	$ips = explode("\n", get_option('aips_ips'));
	if(!in_array($ip,$ips) && !is_user_logged_in()) {
		auth_redirect();
	}
}

add_action('login_footer', 'action_login_footer');
function action_login_footer() {
	?>
	<script type="text/javascript">
		Element.prototype.remove = function() {
			this.parentElement.removeChild(this);
		}
		NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
			for(var i = this.length - 1; i >= 0; i--) {
				if(this[i] && this[i].parentElement) {
					this[i].parentElement.removeChild(this[i]);
				}
			}
		}
		document.getElementById("backtoblog").remove();
	</script>
	<?php
}

add_action( 'wp_enqueue_scripts', 'action_wp_enqueue_scripts' );
function action_wp_enqueue_scripts() {
	global $ipt_kb_version;

	// Fonts from Google Webfonts
	wp_enqueue_style( 'ipt_kb-fonts', '//fonts.googleapis.com/css?family=Oswald|Roboto:400,400italic,700,700italic', array(), $ipt_kb_version );

	// Main stylesheet
	wp_enqueue_style( 'ipt_kb-style', get_template_directory_uri() . '/style.css', array(), $ipt_kb_version );

	// Bootstrap
	wp_enqueue_style( 'ipt_kb-bootstrap', get_template_directory_uri() . '/lib/bootstrap/css/bootstrap.min.css', array(), $ipt_kb_version );
	wp_enqueue_style( 'ipt_kb-bootstrap-theme', get_template_directory_uri() . '/lib/bootstrap/css/bootstrap-theme.min.css', array(), $ipt_kb_version );

	// Icomoon
	wp_enqueue_style( 'ipt_kb-icomoon', get_template_directory_uri() . '/lib/icomoon/style.css', array(), $ipt_kb_version );

	// Now the JS
	wp_enqueue_script( 'ipt_kb-bootstrap', get_template_directory_uri() . '/lib/bootstrap/js/bootstrap.min.js', array( 'jquery' ), $ipt_kb_version );
	wp_enqueue_script( 'ipt_kb-bootstrap-jq', get_template_directory_uri() . '/lib/bootstrap/js/jquery.ipt-kb-bootstrap.js', array( 'jquery' ), $ipt_kb_version );

	wp_enqueue_script( 'ipt_kb-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), $ipt_kb_version, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'ipt_kb-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), $ipt_kb_version );
	}

	// Load the sticky kit
	wp_enqueue_script( 'ipt-kb-sticky-kit', get_template_directory_uri() . '/lib/sticky-kit/jquery.sticky-kit.min.js', array( 'jquery' ), $ipt_kb_version );

	// Load the theme js
	wp_enqueue_script( 'ipt-kb-js', get_template_directory_uri() . '/js/ipt-kb.js', array( 'jquery' ), $ipt_kb_version );
	wp_localize_script( 'ipt-kb-js', 'iptKBJS', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_error' => __( 'Oops, some problem to connect. Try again?', 'ipt_kb' ),
	) );
	wp_enqueue_style( 'ipt_kb_child-style', get_stylesheet_directory_uri() . '/style.css', array(), rand(0,100000));
}

add_action( 'admin_menu', 'action_admin_menu' );
function action_admin_menu() {
	add_options_page(
		'Approved IPs',
		'Approved IPs',
		'manage_options',
		'approved-ips',
		'option_page_approved_ips'
	);
	add_action( 'admin_init', 'action_admin_menu_admin_init' );
};
function option_page_approved_ips() {
?>
	<div class="wrap">
		<h2>Approved IPs</h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'aips' ); ?>
			<?php do_settings_sections( 'aips' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">IP's allow viewing without logining in first. (1 per line)</th>
					<td><textarea style="width:50em;height:10em;" name="aips_ips"><?php echo esc_attr( get_option('aips_ips') ); ?></textarea></td>
				</tr>
			</table>

			<?php submit_button(); ?>

		</form>
	</div>
    <?php
}
function action_admin_menu_admin_init() {
	//register our settings
	register_setting( 'aips', 'aips_ips' );
}


add_action( 'pre_get_posts', 'action_get_posts' );
function action_get_posts( $query ) {
	// Force all posts to be displayed in alphabetically order
    if ( $query->is_archive() ) {
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'ASC' );
    }
}


add_filter( 'the_content', 'filter_the_content' );
function filter_the_content($content) {
	global $post;
	if(is_single())	{
		$content = auto_toc($content);
	}
	return $content;
}


function auto_toc($content){
	$toc = '<div class="toc"><h4 class="toc-head">Table of Contents</h4><ul class="toc_list">';
	preg_match_all('/<(h\d*)>(\w[^<]*)/i', $content, $headings, PREG_SET_ORDER);
	if (count($headings) < 2 ) {
		return $content;
	}
	$count = 0;
	foreach ($headings as $heading){
		$content = str_replace($heading[0],'<'.$heading[1].' id="toc-' . $count . '">' . $heading[2],$content);
		$toc .= '<li class="toc_item toc-level-'.substr($heading[1],-1).'"><a href="#toc-'.$count.'">'.$heading[2].'</a></li>';
		$count++;
	}
	$toc .= '</ul></div>';
	return $toc.$content;
}


// Remove Actions & Filters
add_action( 'init' , 'action_init');
function action_init() {
	remove_filter('the_content', 'wpautop');
	remove_filter('the_content', 'wptexturize');
	remove_action_setup( 'wp_enqueue_scripts', 'ipt_kb_scripts');
}

function remove_action_setup($action, $function) {
	remove_action( $action, $function, has_action($action, $function) );
}