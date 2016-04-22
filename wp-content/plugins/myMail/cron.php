<?php
$time_start = microtime(true);

header("Refresh: 15;url=".$_SERVER['REQUEST_URI']);
@ini_set('display_errors', true);

if ( !defined('ABSPATH') ) {

	if ( !defined('DISABLE_WP_CRON') )
		define('DISABLE_WP_CRON', true);

	/** Load WordPress Bootstrap */
	@ini_set('include_path', '../../../');
	require_once('../../../wp-load.php');

}

if (!defined('MYMAIL_VERSION')) wp_die('activate plugin!');

$interval = isset($_GET['interval']) ? intval($_GET['interval']) : mymail_option('interval', 5)*60;
header("Refresh: $interval;url=".$_SERVER['REQUEST_URI']);

$text_direction = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists( 'language_attributes' ) && function_exists( 'is_rtl' ) ) language_attributes(); else echo "dir='$text_direction'"; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width">
	<title>MyMail <?php echo MYMAIL_VERSION ?> - Cron</title>
	<meta name='robots' content='noindex,nofollow'>
	<meta http-equiv="refresh" content="<?php echo $interval ?>">
	<style type="text/css">
		html {
			background: #f1f1f1;
		}
		body {
			background: #fff;
			color: #444;
			font-family: "Open Sans", sans-serif;
			margin: 2em auto;
			padding: 1em 2em;
			max-width: 700px;
			-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);
			box-shadow: 0 1px 3px rgba(0,0,0,0.13);
		}
		h1 {
			border-bottom: 1px solid #dadada;
			clear: both;
			color: #666;
			font: 24px "Open Sans", sans-serif;
			margin: 30px 0 0 0;
			padding: 0;
			padding-bottom: 7px;
		}
		body {
			margin-top: 50px;
		}
		body p {
			font-size: 14px;
			line-height: 1.5;
			margin: 25px 0 20px;
		}
		body code {
			font-family: Consolas, Monaco, monospace;
		}
		ul li {
			margin-bottom: 10px;
			font-size: 14px ;
		}
		a {
			color: #0073aa;
		}
		a:hover,
		a:active {
			color: #00a0d2;
		}
		a:focus {
			color: #124964;
		    -webkit-box-shadow:
		    	0 0 0 1px #5b9dd9,
				0 0 2px 1px rgba(30, 140, 190, .8);
		    box-shadow:
		    	0 0 0 1px #5b9dd9,
				0 0 2px 1px rgba(30, 140, 190, .8);
			outline: none;
		}
		h2{
			font-size: 18px;
			font-weight: 100;
		}

		pre{
			padding: 0;
			font-size: 12px;
			white-space: pre;
			white-space: pre-wrap;
			white-space: -pre-wrap;
			white-space: -o-pre-wrap;
			white-space: -moz-pre-wrap;
			word-wrap: break-word;
		}
		.button {
			background: #f7f7f7;
			border: 1px solid #ccc;
			color: #555;
			display: inline-block;
			text-decoration: none;
			font-size: 13px;
			line-height: 26px;
			height: 28px;
			margin: 0;
			padding: 0 10px 1px;
			cursor: pointer;
			-webkit-border-radius: 3px;
			-webkit-appearance: none;
			border-radius: 3px;
			white-space: nowrap;
			-webkit-box-sizing: border-box;
			-moz-box-sizing:    border-box;
			box-sizing:         border-box;

			-webkit-box-shadow: 0 1px 0 #ccc;
			box-shadow: 0 1px 0 #ccc;
		 	vertical-align: top;
		}

		.button.button-large {
			height: 30px;
			line-height: 28px;
			padding: 0 12px 2px;
		}

		.button:hover,
		.button:focus {
			background: #fafafa;
			border-color: #999;
			color: #23282d;
		}

		.button:focus  {
			border-color: #5b9dd9;
			-webkit-box-shadow: 0 0 3px rgba( 0, 115, 170, .8 );
			box-shadow: 0 0 3px rgba( 0, 115, 170, .8 );
			outline: none;
		}

		.button:active {
			background: #eee;
			border-color: #999;
		 	-webkit-box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
		 	box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
		 	-webkit-transform: translateY(1px);
		 	-ms-transform: translateY(1px);
		 	transform: translateY(1px);
		}
		table{
			margin-bottom: 20px;
			border-top:1px solid #ccc;
		}

		table tr.odd{
			background-color: #fafafa;
		}
		table tr.even{
			background-color: #fff;
		}

		table, td{
			font-size:12px;
			border-collapse: collapse;
		}
		td{
			padding: 5px 9px;
			border-bottom:1px solid #ccc;
		}

		<?php
		if ( 'rtl' == $text_direction ) {
			echo 'body { font-family: Tahoma, Arial; }';
		}
		?>
	</style>
</head>
<body>
<div>
<?php

$secret = mymail_option('cron_secret');
if( (isset($_GET[$secret])) ||
	(isset($_GET['secret']) && $_GET['secret'] == $secret) ||
	(defined('MYMAIL_CRON_SECRET') && MYMAIL_CRON_SECRET == $secret)){

	//run wp_cron if it should
	if(wp_next_scheduled('mymail_cron')-$time_start < 0) spawn_cron();

	if(mymail_option('cron_service') != 'cron') die('wp_cron in use!');

	?>
	<script type="text/javascript">
		var finished = false;
		window.addEventListener('load', function () {
			if(!finished) document.getElementById('info').innerHTML = '<h2>Your servers execution time has been execed!</h2><p>No worries, emails still get sent. But it\'s recommended to increase the "max_execution_time" for your server, add <code>define("WP_MEMORY_LIMIT", "256M");</code> to your wp-config.php file  or decrease the <a href="<?php echo admin_url('/') ?>options-general.php?page=newsletter-settings&settings-updated=true#delivery" target="_blank">number of mails sent</a> maximum in the settings!</p><p><a onclick="location.reload();" class="button" id="button">ok, now reload</a></p>';
		});

	</script>
	<div id="info"><p>progressing...</p></div>
	<?php
	flush();
	do_action('mymail_cron_worker');
	?>
	<p>
		<a onclick="location.reload();clearInterval(i);" class="button" id="button">reload</a>
	</p>
	<p>
		<small><?php echo $time = round(microtime(true) - $time_start, 4) ?> sec.</small>
	</p>
	<script type="text/javascript">finished = true;document.getElementById('info').innerHTML = ''</script>
	<?php

}else{
	echo ('not allowed');
}

?>
</div>
<script type="text/javascript">
var a = <?php echo floor($interval) ?>,
	b = document.getElementById('button'),
	c = document.title,
	d = b.innerHTML,
	e = new Date().getTime(),
	f = setInterval(function(){
		var x = a-Math.ceil((new Date().getTime()-e)/1000),
			t = new Date(x*1000),
			h = t.getHours()-1,
			m = t.getMinutes(),
			s = t.getSeconds(),
			o = (x>=3600 ? (h<10?'0'+h:h)+':' : '')+(x>=60 ? (m<10?'0'+m:m)+':' : '' )+(s<10?'0'+s:s);

		if(x<=0){
			o = '⟲';
			clearInterval(f);
		}
	document.title = '('+o+') '+c;
	b.innerHTML = d+' ('+o+')';
}, 1000);
</script>
</body>
</html>
