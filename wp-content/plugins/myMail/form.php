<?php
$time_start = microtime(true);
if ( !defined('ABSPATH') ) {
	/** Load WordPress Bootstrap */
	require_once('../../../wp-load.php');

}

$use_cache = false;
$cache_key = 'mymail_form_'.md5(serialize($_GET));

ob_start('ob_gzhandler');
if ($use_cache && $form = get_transient( $cache_key ) ) {

	echo $form;
	exit;
}


do_action('mymail_form_header');

?><!DOCTYPE html>
<!--[if IE 8]><html class="lt-ie10 ie8" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 9]><html class="lt-ie10 ie9" <?php language_attributes(); ?>><![endif]-->
<!--[if gt IE 9]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
<html <?php language_attributes(); ?> class="mymail-emebed-form">
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<meta name='robots' content='noindex,nofollow'>
	<?php do_action('mymail_form_head'); ?>

</head>
<body>
	<div class="mymail-form-body">
		<div class="mymail-form-wrap">
			<div class="mymail-form-inner">
			<?php do_action('mymail_form_body'); ?>
			</div>
		</div>
	</div>
<?php do_action('mymail_form_footer'); ?>
</body>
</html>
<?php

	$output = ob_get_contents();
	set_transient( $cache_key, $output );
