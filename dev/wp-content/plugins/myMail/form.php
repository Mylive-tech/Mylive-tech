<?php
$time_start = microtime(true);
if ( !defined('ABSPATH') ) {
	/** Load WordPress Bootstrap */
	require_once('../../../wp-load.php');

}
?>
<!doctype html>
<html lang="en-us">
<head>
	<meta charset="utf-8">

	<meta name='robots' content='noindex,nofollow'>

	<?php if(isset($_GET['s']) && $_GET['s'] == 1) :
		wp_register_style('theme-style', get_template_directory_uri().'/style.css', array(), MYMAIL_VERSION);
		wp_print_styles('theme-style');

	endif;
		echo mymail()->style();

do_action('wp_mymail_head');
?>

</head>
<body>
<div id="formwrap">
<?php
	mymail_form((isset($_GET['id']) ? intval($_GET['id']) : 0), 1, true, 'embeded');
?>
<?php
do_action('wp_mymail_footer');

if(mymail_option('ajax_form')) :

		wp_register_script('mymail-form', MYMAIL_URI . 'assets/js/form.js', array('jquery'), MYMAIL_VERSION);
		wp_print_scripts('jquery');
		wp_print_scripts('mymail-form');

endif; ?>
</div>
</body>
</html>
