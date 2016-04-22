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

$interval = mymail_option('interval', 5)*60;
header("Refresh: $interval;url=".$_SERVER['REQUEST_URI']);

?>
<!doctype html>
<html lang="en-us">
<head>
	<meta charset="utf-8">

	<title>MyMail <?php echo MYMAIL_VERSION ?> Cronjob</title>
	<link rel="shortcut icon" type="image/png" href="<?php echo MYMAIL_URI . 'assets/img/icons/progressing.png';?>">

	<meta name='robots' content='noindex,nofollow'>
	<meta http-equiv="refresh" content="<?php echo $interval ?>">
<style type="text/css">
	html {
		background:#f9f9f9;
	}

	body {
		background:#fff;
		color:#333;
		font-family:sans-serif;
		margin:2em auto;
		padding:1em 2em;
		-webkit-border-radius:3px;
		border-radius:3px;
		border:1px solid #dfdfdf;
		max-width:700px;
		margin-top:50px;
		font-size:14px;
		line-height:1.5;
	}

	body p {
		margin:25px 0 20px;
	}

	ul {
		padding: 0;
	}

	ul li {
		font-size:12px;
		list-style: none;
		min-height: 12px;
	}

	a {
		color:#21759B;
		text-decoration:none;
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

	a:hover {
		color:#D54E21;
	}

	.button {
		display: inline-block;
		text-decoration: none;
		font-size: 12px;
		line-height: 23px;
		height: 24px;
		margin: 0;
		padding: 0 10px 1px;
		cursor: pointer;
		border-width: 1px;
		border-style: solid;
		-webkit-border-radius: 3px;
		-webkit-appearance: none;
		border-radius: 3px;
		white-space: nowrap;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		background: #F3F3F3;
		background-image: -webkit-gradient(linear,left top,left bottom,from(#FEFEFE),to(#F4F4F4));
		background-image: -webkit-linear-gradient(top,#FEFEFE,#F4F4F4);
		background-image: -moz-linear-gradient(top,#FEFEFE,#F4F4F4);
		background-image: -o-linear-gradient(top,#FEFEFE,#F4F4F4);
		background-image: linear-gradient(to bottom,#FEFEFE,#F4F4F4);
		border-color: #BBB;
		color: #333;
		text-shadow: 0 1px 0 white;
	}

	.button:hover {
		color:#000;
		border-color:#666;
	}

	.button:active {
		background-image:-ms-linear-gradient(top,#f2f2f2,#fff);
		background-image:-moz-linear-gradient(top,#f2f2f2,#fff);
		background-image:-o-linear-gradient(top,#f2f2f2,#fff);
		background-image:-webkit-gradient(linear,left top,left bottom,from(#f2f2f2),to(#fff));
		background-image:-webkit-linear-gradient(top,#f2f2f2,#fff);
		background-image:linear-gradient(top,#f2f2f2,#fff);
	}

	table{
		margin-bottom: 20px;
	}

	table, td{
		font-size:12px;
		border: 1px solid #ccc;
		border-collapse: collapse;
	}
	td{
		padding: 3px;
	}
	.error{
		color:#f33;
	}
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
	if(wp_next_scheduled('mymail_cron') - $time_start < 0) spawn_cron();

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
		<small><?php echo $time = (microtime(true) - $time_start) ?> sec.</small> <a onclick="location.reload();clearInterval(i);" class="button" id="button">reload</a>
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
