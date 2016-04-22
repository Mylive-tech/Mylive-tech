<?php

	/*
	Plugin Name: AutoTubePress
	Plugin URI:
	Description: Adds Youtube videos to your blog on a schedule
	Author: George Katsoudas
	Version: 1.43
	Author URI: http://www.autotubepress.com
	*/

	define('YTA_VERSION', '1.43');

	$wp_plug_url = trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
  
	$silent = 1;
	/*
	date_default_timezone_set('Asia/Manila');	//debug
	Fedmich, debug
	*/
	$debugger = 'd:\fedmich\web\lib\scraping2.lib.php';
	if(file_exists($debugger)){
		include $debugger;
	}

	if(isset($_GET['server'])){ phpinfo(); exit(); }
	
	if(isset($_GET['clear'])){
		$query = "DELETE FROM `".DB_NAME."`.`{$table_prefix}posts`
				WHERE  post_type in('post','future')
				";
		$results = $wpdb->query( $query );
		
		$query = "DELETE FROM `".DB_NAME."`.`{$table_prefix}ytacom`";
		$results = $wpdb->query( $query );
		
		$query = "DELETE FROM `".DB_NAME."`.`{$table_prefix}comments`";
		$results = $wpdb->query( $query );
		
		$query = "DELETE FROM `".DB_NAME."`.`{$table_prefix}ytavid`";
		$results = $wpdb->query( $query );
		
		header("Location: ./admin.php?page=YouTubeAuto&db_cleared");
		exit();
	}
	
	
	/*
	 * WP actions hooks
	 */
	register_activation_hook( __FILE__, 'yta_activate' );

	add_action('admin_menu', 'yta_dashboard');
	add_action('init', 'yta_init');
	add_action('wp_head','addkeydesc');


	 
	/*
	 * Add keywords and description to post
	 */
	 function addkeydesc() {
		global $wp_query;
		$postid = $wp_query->post->ID;
		$keyopt = "autopost".$postid."key";
		$descopt = "autopost".$postid."desc";
		$desc = get_option($descopt);
		$key = get_option($keyopt);
		echo "<meta name=\"description\" value=\"".$desc."\" />";
		echo "<meta name=\"keywords\" value=\"".$key."\" />";
		}


	/*
	 * Create WP tables and fill with default values where needed
	 */
	function yta_activate() {

		global $wpdb;

		$tbl_yta = $wpdb->prefix . "yta";

		$tbl_ytaComments = $wpdb->prefix . "ytacom";

		$tbl_ytaVideos = $wpdb->prefix . "ytavid";

		if($wpdb->get_var("show tables like '$tbl_yta'") != $tbl_yta ) {
			$sql = "CREATE TABLE IF NOT EXISTS $tbl_yta (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `field` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
			  `data` varchar(16384) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`id`)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'keywords', '')";
			$results1 = $wpdb->query( $query );

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'negative_keywords', '')";
			$results2 = $wpdb->query( $query );

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'comment_options', 1)";
			$results3 = $wpdb->query( $query );

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'description_options', 1)";
			$results4 = $wpdb->query( $query );

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'post_qty', 10)";
			$results5 = $wpdb->query( $query );

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'post_freq', 2)";
			$results6 = $wpdb->query( $query );

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'post_timespan', 1)";
			$results7 = $wpdb->query( $query );

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'post_timeunit', 1)";
			$results8 = $wpdb->query( $query );

			$commsched = array();
			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'commsched', '" . serialize($commsched) . "')";
			$results9 = $wpdb->query( $query );

			$commdata = array();
			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'commdata', '" . serialize($commdata) . "')";
			$results10 = $wpdb->query( $query );

			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'working', '0')";
			$results11 = $wpdb->query( $query );
			
			$query = "INSERT INTO $tbl_yta ( field, data) VALUES ( 'categories', '')";
			$results12 = $wpdb->query( $query );

			if (
					$results12 === FALSE || $results1 === FALSE || $results2 === FALSE || $results3 === FALSE || $results4 === FALSE || $results5 === FALSE  || $results6 === FALSE || $results7 === FALSE || $results8 === FALSE || $results9 === FALSE || $results10 === FALSE || $results11 === FALSE ||
					$results12 == 0 || $results1 == 0 || $results2 == 0 || $results3 == 0 || $results4 == 0 || $results5 == 0 || $results6 == 0 || $results7 == 0 || $results8 == 0 || $results9 == 0 || $results10 == 0 || $results11 == 0
				)
				{
				echo "An error occurred.";
			}
		}

		if($wpdb->get_var("show tables like '$tbl_ytaComments'") != $tbl_ytaComments ) {
			$sql = "CREATE TABLE IF NOT EXISTS $tbl_ytaComments (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `postid` bigint(20) unsigned NOT NULL,
			  `author` tinytext COLLATE utf8_unicode_ci NOT NULL,
			  `content` text COLLATE utf8_unicode_ci NOT NULL,
			  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`id`)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

		if($wpdb->get_var("show tables like '$tbl_ytaVideos'") != $tbl_ytaVideos ) {
			$sql = "CREATE TABLE IF NOT EXISTS $tbl_ytaVideos (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `url` tinytext COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`id`)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

	}

	function tiphelp($f,$set='') {
		global $wp_plug_url;
		?>
		<div class="closewin">
		<a href="#" class="closetip"><img class="" src="<?php echo $wp_plug_url; ?>/images/close-help.gif" width="16" height="16" alt="" title="" /></a>
		<div style="clear:both;"></div>
		
		</div>
		<div class="tipmsg">
		<p>
		<?php echo read_phelp($f,$set);?>
		</p>
		</div>
		<div align="center">
		<a href="#" class="closetip" style="font-size: 20px;">close</a>
			
		</div>
		<?php
	}
	function read_phelp($f,$set='') {
		global $wp_plug_url;
		$htm = dirname(__FILE__)."/h/$f.htm";
		if(file_exists($htm)){
			$htm = trim(file_get_contents($htm));
			if($set){
				$htm = str_replace("'","\'",$htm);
				$htm = str_replace('"','%22',$htm);
				$htm = preg_replace("/\r\n|\n/",'\r\n',$htm);
			}
			else{
				$htm = nl2br($htm);
			}
			$htm = str_replace('/PLUG/',"$wp_plug_url/",$htm);
			echo $htm;
		}
	}
	
	/*
	 * Plugin Admin pages
	 */
	function yta_dashboard() {
		global $wp_plug_url;
		add_object_page ('YouTubeAuto', 'AutoTubePress', 'manage_options', 'YouTubeAuto', 'yta_settings'
		, "$wp_plug_url/images/favicon.ico");

		add_submenu_page('YouTubeAuto', 'AutoTubePress > Settings','Settings', 'manage_options', 'YouTubeAuto', 'yta_settings');

	}

	/*
	 * This is the main plugin control page, where settings are defined and search is launched
	 */
	function yta_settings() {

		global $wpdb;
		global $wp_plug_url;

		$tbl_yta = $wpdb->prefix . "yta";

		$tbl_ytaComments = $wpdb->prefix . "ytacom";

		$tbl_ytaVideos = $wpdb->prefix . "ytavid";
		
		?>
		<div class="wrap">
		<div class="icon32" id="icon-edit"><br></div>
		<h2>AutoTubePress <?php echo YTA_VERSION;?> | <a href="http://www.autotubepress.com/help.html" target="_blank">Help Topics</a> | <a href="http://www.autotubepress.com/contact.html" target="_blank">Contact</a> | <a href="http://www.autotubepress.com/Affiliates.html" target="_blank">Affiliates</a></h2>
		<hr />
		<?php
		
		$messages = Array();
		if($_POST["submitform"]&&$_POST["keywords"]) {
		
			update_option("autopost_template",stripslashes($_POST['template']));
		
			$datenumber = $_POST["datenumber"];
			
			$datewhat = $_POST["datewhat"];
			
			$datewhen = $_POST["datewhen"];
			
			update_option("autopost_datewhat",$datewhat);
			update_option("autopost_datewhen",$datewhen);
			update_option("autopost_datenumber",$datenumber);
			
			$pastfuture = 0;
			
			$ndays = 0;
			
			if($datewhen==1 || $datenumber==0 || $datewhat==1)
				$pastfuture = 0;
			
			if($datewhen==2)
				$pastfuture=-1;
			
			if($datewhen==3)
				$pastfuture=1;
				
			$numofdays = array(2=>1,3=>7,4=>30);
			
			$ndays = $datenumber * $numofdays[$datewhat];

			$keywords = $wpdb->escape($_POST['keywords']);
			
			$categories = $wpdb->escape($_POST['categories']);

			$negative_keywords = $wpdb->escape($_POST['negative_keywords']);

			$comment_options = $wpdb->escape($_POST['comment_options']);

			$description_options = $wpdb->escape($_POST['description_options']);

			$post_qty = $wpdb->escape($_POST['post_qty']);
			
			$post_freq = $wpdb->escape($_POST['post_freq']);

			//$post_timespan = $wpdb->escape($_POST['post_timespan']);
			$post_timespan=1;

			//$post_timeunit = $wpdb->escape($_POST['post_timeunit']);
			$post_timeunit=1;

			// Update
			$query = "UPDATE $tbl_yta SET data = '$keywords' WHERE field = 'keywords'";
			$results1 = $wpdb->query( $query );

			$query = "UPDATE $tbl_yta SET data = '$negative_keywords' WHERE field = 'negative_keywords'";
			$results2 = $wpdb->query( $query );

			$query = "UPDATE $tbl_yta SET data = '$comment_options' WHERE field = 'comment_options'";
			$results3 = $wpdb->query( $query );

			$query = "UPDATE $tbl_yta SET data = '$description_options' WHERE field = 'description_options'";
			$results4 = $wpdb->query( $query );

			$query = "UPDATE $tbl_yta SET data = '$post_qty' WHERE field = 'post_qty'";
			$results5 = $wpdb->query( $query );

			$query = "UPDATE $tbl_yta SET data = '$post_freq' WHERE field = 'post_freq'";
			$results6 = $wpdb->query( $query );

			$query = "UPDATE $tbl_yta SET data = '$post_timespan' WHERE field = 'post_timespan'";
			$results7 = $wpdb->query( $query );

			$query = "UPDATE $tbl_yta SET data = '$post_timeunit' WHERE field = 'post_timeunit'";
			$results8 = $wpdb->query( $query );
			
			$query = "UPDATE $tbl_yta SET data = '$categories' WHERE field = 'categories'";
			$results9 = $wpdb->query( $query );

			if (
					$results9 === FALSE || $results1 === FALSE || $results2 === FALSE || $results3 === FALSE || $results4 === FALSE ||
					$results5 === FALSE || $results6 === FALSE || $results7 === FALSE || $results8 === FALSE //||
					//$results1 == 0 || $results2 == 0 || $results3 == 0
				)
			{
				$messages[] = 'An error occurred saving the settings';
			} else {
				$messages[] = 'Settings saved.';
				
				$newposts = post_videos(0);	//not silent, show progress...
			}
			
			$messages[] = "<br />Posts added:  ".$newposts;
		}
		
		if(($_SERVER['REQUEST_METHOD']=='GET') and isset($_GET['db_cleared'])){
			$messages = 1;
		}
		else{
			$message = join('<br /><br />',$messages);
		}
		
		if($messages){
			yta_init_comments();
	
			?>
			<div class="updated">
			<p>
			<?php echo $message;?>
			<?php
				if($_SERVER['REQUEST_METHOD']=='GET'){
				if(isset($_GET['db_cleared'])){
				read_phelp('db_cleared');
				}
				}
			?>
			</p>
			</div>
			<?php
		}

	   $template = get_option("autopost_template");
	   if(!$template){
			$template="[video]\n\n[video description]\n\n[comments]";
			update_option("autopost_template",$template);
		}
			
		$datewhat = get_option("autopost_datewhat");
		if(! $datewhat) {
			$datewhat=1;
		}
			
		$datewhen = get_option("autopost_datewhen");
		if(!$datewhen) {
			$datewhen=1;
		}
			
		$datenumber = get_option("autopost_datenumber");
		if(!$datenumber) {
		$datenumber=0;
		}
			
		


	   $query = "SELECT data FROM $tbl_yta WHERE field = 'keywords'";
	   $keywords = stripslashes($wpdb->get_var($query) );
	   
	   $query = "SELECT data FROM $tbl_yta WHERE field = 'categories'";
	   $categories = stripslashes($wpdb->get_var($query) );

	   $query = "SELECT data FROM $tbl_yta WHERE field = 'negative_keywords'";
	   $negative_keywords = stripslashes($wpdb->get_var($query) );

	   $query = "SELECT data FROM $tbl_yta WHERE field = 'comment_options'";
	   $comment_options = stripslashes($wpdb->get_var($query) );

	   $query = "SELECT data FROM $tbl_yta WHERE field = 'description_options'";
	   $description_options = stripslashes($wpdb->get_var($query) );

	   $query = "SELECT data FROM $tbl_yta WHERE field = 'post_qty'";
	   $post_qty = stripslashes($wpdb->get_var($query) );

	   $query = "SELECT data FROM $tbl_yta WHERE field = 'post_freq'";
	   $post_freq = stripslashes($wpdb->get_var($query) );

	   $query = "SELECT data FROM $tbl_yta WHERE field = 'post_timespan'";
	   $post_timespan = stripslashes($wpdb->get_var($query) );

	   $query = "SELECT data FROM $tbl_yta WHERE field = 'post_timeunit'";
	   $post_timeunit = stripslashes($wpdb->get_var($query) );

	   
		?>
		<style type="text/css">
		.closewin{
			background-color : #DBDBDB;
			border-bottom:1px solid #C6C6C6;
			height : 16px;
			padding: 5px 5px 5px 5px;
		}
		.closewin a{
			float : right;
		}
		.helptip{
			z-index:100;
			 font-weight: normal;
			  position:absolute; 
			  border-style: solid;
			  background-color: white; 
			  border: 2px dotted #FFB12A;
			  padding: 5px;
		}
		.tipmsg{
			width : 600px;
			height : 400px;
			overflow:scroll;
			text-align:justify;
			white-space:normal;
		}
		.tipmsg p{
			margin: 0px 10px 0px 10px;
		}
		
		hr.linefoot{
			margin: 40px 0px 40px 0px;
			color: #cce;
			text-align:left;
			width : 90%;
		}
		
		.ytalabel {
			font-weight: bold;
		}

		#ytaform textarea {
			margin: 0 5px 0 0;
		}

		.ytasubmit {
			cursor: pointer;
			font-size: 24pt;
			font-weight: bold;
			width: 100%;
			height: 60px;
		}
		
		.ytasel1 {
			width: 45px;
		}

		.ytasel2 {
			width: 24%;
		}

		.td1 {
			padding:12px 10px;
			font-weight: bold;
			text-align: right;
		}

		.td2 {
			padding:0 10px;
		}

		.subm {
			font-weight:bold;
			margin:20px 0;
			width:80%;
		}

	</style>

	<script>
	
	function changed_datewhat(o){
		switch(o.value){
		case '1':
			document.getElementById('datewhen').selectedIndex = 1;
			document.getElementById('datenumber').selectedIndex = 1;
			break;
		case '2':
			document.getElementById('datenumber').selectedIndex = 2;
			break;
		case '3':
			document.getElementById('datenumber').selectedIndex = 2;
			break;
		case '4':
			document.getElementById('datenumber').selectedIndex = 2;
			break;
		default:
			break;
		}
	}
	function ajax()
	{
	var mysack = new sack( 
		   "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );    
	  mysack.execute = 1;
	  mysack.method = 'POST';
	  mysack.setVar( "action", "my_special_action" );
	  mysack.onError = function() { alert('Ajax error' )};
	  mysack.runAJAX();
	  mysack.onCompletion=function(){
	  }
	  return true;

	} 

	</script>

	<script type="text/javascript" language="JavaScript">
	var cX = 0; var cY = 0; var rX = 0; var rY = 0;
	function UpdateCursorPosition(e){ cX = e.pageX; cY = e.pageY;}
	function UpdateCursorPositionDocAll(e){ cX = event.clientX; cY = event.clientY;}
	if(document.all) { document.onmousemove = UpdateCursorPositionDocAll; }
	else { document.onmousemove = UpdateCursorPosition; }
	function AssignPosition(d,left,top) {
	if(self.pageYOffset) {
		rX = self.pageXOffset;
		rY = self.pageYOffset;
		}
	else if(document.documentElement && document.documentElement.scrollTop) {
		rX = document.documentElement.scrollLeft;
		rY = document.documentElement.scrollTop;
		}
	else if(document.body) {
		rX = document.body.scrollLeft;
		rY = document.body.scrollTop;
		}
	if(document.all) {
		cX += rX; 
		cY += rY;
		}
	d.style.left = (cX+10+left) + "px";
	d.style.top = (cY+10+top) + "px";
	}
	function HideContent(d) {
	if(d.length < 1) { return; }
	document.getElementById(d).style.display = "none";
	}
	function ShowContent(d,left,top) {
	if(d.length < 1) { return; }
	var dd = document.getElementById(d);
	dd.style.left = '25%';
	dd.style.top = '100px';
	
	jQuery('.helptip').hide();
	
	jQuery('#'+d).slideDown('fast');
	
	//	AssignPosition(dd,left,top);
	//	dd.style.display = "block";
	}
	function ReverseContentDisplay(d) {
	if(d.length < 1) { return; }
	var dd = document.getElementById(d);
	AssignPosition(dd);
	if(dd.style.display == "none") { dd.style.display = "block"; }
	else { dd.style.display = "none"; }
	}
	//-->

	//function to reset the template
	function reset()
		{
		var template = document.getElementById("template").value="[video]\n\n[video description]\n\n[comments]";
		ajax();
		}
	</script>
	
	<script type="text/javascript">
	if (!Array.prototype.map) {
	  Array.prototype.map = function(fun /*, thisp*/)
	  {
		var len = this.length;
		if (typeof fun != "function")
		  throw new TypeError();

		var res = new Array(len);
		var thisp = arguments[1];
		for (var i = 0; i < len; i++)
		{
		  if (i in this)
			res[i] = fun.call(thisp, this[i], i, this);
		}

		return res;
	  };
	}
	</script>		
	

		<form id="ytaform" name="update" method="post">
			<fieldset class="options">
				<table>
					<tr>
						<td colspan="2"><span class="ytalabel">
				Enter 1-15 keywords: <b><a 
	   onclick="ShowContent('helpdiv1',0,0); return true;"
	   href="javascript:ShowContent('helpdiv1',0,0)">Help</a></b><div 
	   class="helptip" id="helpdiv1" style="display:none;">
	<?php tiphelp('enter_keywords');?>
	
	
	</div>
	</span></td>
						<td colspan="2"><span class="ytalabel">
		Enter 0-15 categories: <b><a 
	   onclick="ShowContent('helpdiv2',-450,0); return true;"
	   href="javascript:ShowContent('helpdiv2',-450,0)">Help</a></b><div 
	   class="helptip" id="helpdiv2" 
	   style="display:none;">
	<?php tiphelp('enter_categories');?>
	
	</div></span></td>
		   <td colspan="2"><span class="ytalabel">Negative keywords: <b><a 
	   onclick="ShowContent('helpdiv3',-450,0); return true;"
	   href="javascript:ShowContent('helpdiv3',-450,0)">Help</a></b><div 
	   class="helptip" id="helpdiv3" 
	   style="display:none;">
		<?php tiphelp('negative_keywords');?>
		
	</div></span></td>
			<td rowspan="2" align="center">
			</td>
					</tr>
					<tr>
						<td colspan="2">
						<textarea wrap="off" name="keywords" id="ykeywords" rows="7" cols="25"><?php echo $keywords;?></textarea></td>
						<td colspan="2">
						<textarea wrap="off" name="categories" id="ycategories" rows="7" cols="25"><?php echo $categories;?></textarea></td>
						<td colspan="2">
						<textarea wrap="off" name="negative_keywords" id="ynegative_keywords" rows="7" cols="20"><?php echo $negative_keywords;?></textarea></td>
					</tr>

				</table>
				
		<table border="1" align="center">
			<tr>
				<td align="left" nowrap="nowrap"><table border="0">
					<tr>
					<td width="400" valign="top">
					<h3>Video Template:
					(<b><a href="javascript:reset()">Reset to default</a></b>)
					(<b><a 
	   onclick="ShowContent('helpdiv8',-550,-300); return true;"
	   href="javascript:ShowContent('helpdiv8',-550,-300)">Help</a></b>)</h3><div class="helptip" id="helpdiv8" 
	   style="display:none;">
	   <?php tiphelp('video_templates');?>		
		</div> 
					<textarea id="template" name="template" rows="7" cols="40"><?php echo $template;?></textarea>
					
					
					<br/><br/>
					Post
				  <select class="ytasel1" onchange="" name="post_qty">
				  <?php
				  //	$maxday = 10;
					foreach(Array(4,7,10,15,20) as $i){
					?>
					  <option value="<?php echo $i?>" <?php echo ($post_qty == $i)?'selected':'' ?> ><?php echo $i?></option>
					  <?php }?>
				  </select>
				 videos per week
				 <input type="hidden" id="" name="post_freq" value="2" />
				

				 <b><a 
	   onclick="ShowContent('helpdiv6',0,-150); return true;"
	   href="javascript:ShowContent('helpdiv6',0,-150)">Help</a></b><div 
	   class="helptip" id="helpdiv6" 
	   style="display:none;">
		<?php tiphelp('post_opt3');?>
		
	</div>				
					
					</td>
					<td align="center" width="240">
					
					  <p>
					    <input type="hidden" name="submittedform" value="1" />
					    <input type="image" src="<?php echo $wp_plug_url; ?>/images/fetch-videos-1.png" class="ytasubmit" value="Start Posting!" name="submitform" style="width : 214px; height: 214px; font-size: 16px;" onmouseover="this.src=this.src.replace('1.png','2.png');" onmouseout="this.src=this.src.replace('2.png','1.png');" />
					    <br>
					  </p></td>

					<td width="237"><p>This blue button will schedule posts<br>for about 3-4 weeks. More posts<br>will be rescheduled automatically<br>after that. You can always see the<br>
					scheduled (and published) posts<br>
					under &quot;Posts.&quot;</p>
					  <p>If you make a mistake and want to<br>delete posts &quot;en masse&quot;, scroll down<br>for the &quot;delete posts&quot; button.</p>
					  <p>If you experience any technical<br>
					  difficulties, the <a href="http://www.autotubepress.com/help.html" target="_blank">help page</a> should<br>
					  come in handy...</p>
					  <p>&nbsp;</p></td>
					</tr>
				</table>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p><b><a 
		   onclick="ShowContent('helpdiv9',-450,0); return true;"
		   href="javascript:ShowContent('helpdiv9',-450,0)">Read this BEFORE<br>clicking the button below</a></b><br><br>
				  <a href="<?php echo $_SERVER['REQUEST_URI'].'&clear';?>" onclick="return confirm('<?php echo read_phelp('are_you_sure',2);?>');"><img src="<?php echo $wp_plug_url; ?>/images/btn-delposts.png" alt="Delete Posts" width="94" height="94" hspace="20" title="Delete Posts" /></a>
				  <br />
				  </p>
				<div 
		   class="helptip" id="helpdiv9" 
		   style="display:none;">
			<?php tiphelp('delete_posts');?>
			
			</div>
				
			</div>
				
				 <?php  /*  
				 <select class="ytasel2" onchange="" name="post_freq">
					<option value="1" <?php echo ($post_freq == 1)?'selected':'' ?>>day</option>
					<option value="2" <?php echo ($post_freq == 2)?'selected':'' ?>>week</option>
					<option value="3" <?php echo ($post_freq == 3)?'selected':'' ?>>month</option>
				 </select>
				   */ ?>
				 
		<br/><br/>
		
	  <?php
	  if($datewhen!=2){
		$radioa = 1;
		//today
	  }
	  else{
		$radioa = 0;
	  }
		
	  ?>
	  
	  <input type="hidden" id="datewhen" name="datewhen" value="1" />
	  <input type="hidden" id="datewhat" name="datewhat" value="3" />
	  
	  <?php  /*  
	  <label>
	  <input type="radio" name="datewhen2" id="datewhen2a" value="1" class="datewhen2" <?=$radioa?'checked':'';?> />
	  Start posting today
	  </label>
	  <b><a 
	   onclick="ShowContent('helpdiv7',0,-200); return true;"
	   href="javascript:ShowContent('helpdiv7',0,-200)">Help</a></b><div 
	   class="helptip" id="helpdiv7" 
	   style="display:none;">
		<?php tiphelp('post_opt4');?>
		
		</div>
	  
	  
	  <br />
	  <label>
	 <input type="radio" name="datewhen2" id="datewhen2b" value="2" class="datewhen2"  <?=$radioa?'':'checked';?> />
	 Fill blog with 
	  </label>
	  
	  <span class="datewhen2_b">
		<select name="datenumber" id="datenumber"> 
		<?php
			if(!$datenumber)
				$datenumtext="";
			else
				$datenumtext=$datenumber;
			echo "<option value=\"".$datenumber."\"  selected=\"selected\" >".$datenumtext."</option>";
		?>
		  <option value="0"></option> 
		  <option value="1"  >1</option> 
		  <option value="2"  >2</option> 
		  <option value="3"  >3</option> 
		  <option value="4"  >4</option> 
		  <option value="5"  >5</option> 
		  <option value="6"  >6</option> 
		  <option value="7"  >7</option> 
		  <option value="8"  >8</option> 
		  <option value="9"  >9</option> 
		  <option value="10"  >10</option>
		  </select> 
	  <input type="hidden" id="datewhat" name="datewhat" value="3" />
	  <label for="datewhen2b">weeks worth of older posts</label>
		
	  <?php  /*  
	  <select onchange="changed_datewhat(this);" name="datewhat" id="datewhat"> 
		<?php
		if($datewhat==1)
			$datewhattext="Today";
		else if($datewhat==2)
			$datewhattext="days";
		else if($datewhat==3)
			$datewhattext="weeks";
		else
			$datewhattext="months";
		echo "<option value=\"".$datewhat."\" selected=\"selected\">".$datewhattext."</option> ";
		?>
		<option value="" disabled></option> 
		<option value="1" >Today</option> 
		
		<option value="2" >days</option> 
		<option value="3" >weeks</option> 
		<option value="4" >months</option> 
	  </select> 
	  <input type="hidden" id="datewhen" name="datewhen" value="<?=$datewhen;?>" />
	   */ ?>
	 
	  
	  <?php  /*  
	  <select onchange="" name="datewhen" id="datewhen">
		<?php
		if($datewhen==1){
			$datewhentext="";
		}
		else if($datewhen==2){
			$datewhentext="in the past";
		}
		else{
			$datewhentext="in the future";
		}
		echo "<option value=\"".$datewhen."\" selected=\"selected\">".$datewhentext."</option> ";
		?>
		<option value="1"></option> 
		<option value="2" >in the past</option> 
		<option value="3" >in the future</option> 
	  </select>
	    */ ?>
	  
	  </span>
	  
	  </div>
				
					
				</td>
				<td align="left">
				
			
			
			
			</fieldset>
		</form>
	
		</td>
		</tr>
			
		</table>
		
		<hr class="linefoot" />
			
		<script type="text/javascript">
			<?php  /*  
			changed_datewhat(document.getElementById('datewhat'));
			  */ ?>
			
			
			function changed_datewhat2(){
				var bst = true;
				if(document.getElementById('datewhen2b').checked){
					bst = false;
					jQuery('#datewhen').val(2);
				}
				else{
					bst = true;
					jQuery('#datewhen').val(1);
				}
				jQuery('.datewhen2_b select').each(function(){
					this.disabled = bst;
				});
				
			}
			/*
			jQuery(function() {
				jQuery('.datewhen2').click(function(){
					changed_datewhat2();
					
				});
				changed_datewhat2();
			});
			*/
			
			function line_cleaner(v){ return jQuery.trim(v); }
			jQuery(function() {
				jQuery('textarea#ykeywords, textarea#ycategories, textarea#ynegative_keywords')
					.change(function(){
						var vs = this.value.split("\n");
						var l2 = [];
						jQuery(vs).each(function(d){
							var d2 = jQuery.trim(this);
							if(d2){l2.push(d2);}
						});
						
						l2 = l2.slice(0,15);
						v = l2.join("\n");
						this.value = v;
					});
				jQuery('a.closetip').click(function(){ jQuery('.helptip').slideUp('fast'); });
				
			});
			
			
		</script>
	
	<?php

	}
	
	
	 /*
	 * Auto Post videos as needed
	 */
	function yta_init_posts_auto(){
		if(isset($_GET['db_cleared'])){ return; }
		if($_SERVER['REQUEST_METHOD']=='POST'){ return ;}
		
		$uri = $_SERVER['REQUEST_URI'];
		if(strstr($uri,'/wp-admin')){ return; }
		//	if(strstr($uri,'YouTubeAuto')){ return; }
		
		global $wpdb, $silent;
		
		$tbl_yta = $wpdb->prefix . "yta";
		$query = "SELECT data FROM $tbl_yta WHERE field = 'post_qty'";
		$post_qty = stripslashes($wpdb->get_var($query) );
		
		$query = "SELECT data FROM $tbl_yta WHERE field = 'post_freq'";
		$post_freq = stripslashes($wpdb->get_var($query) );
		
		$datewhen = get_option("autopost_datewhen");
		if(!$datewhen) {
			return;
		}
		if($datewhen==2) {
			return;
		}
		/* Currently on Today, so posts 30 days */
		
		$timenow = mktime();
		
		$sql = "select count(ID) from `{$wpdb->posts}`
		WHERE
		post_type in('post','future')
		and post_status not in ('trash','auto-draft','inherit')
		";
		$total_posts = $wpdb->get_var($sql);
		if($total_posts){
			$sql = "select max(post_date) from `{$wpdb->posts}`
			WHERE  post_type in('post','future')
			and post_status not in ('trash','auto-draft','inherit')
			";
			$date_last_post = $wpdb->get_var($sql);
			$pd1 = strtotime($date_last_post);
			$next_mo = strtotime('+1 month',$timenow);
			if($pd1>$next_mo){
				return;
			}
			if($pd1>$timenow){
				$pd2 = strtotime('+1 month',$pd1);
			}
			else{
				$pd2 = strtotime('+1 week',$pd1);
			}
			if($pd2>$timenow){
				$pd2 = $timenow;
			}
		}
		post_videos($silent,$pd1,$pd2);
	}
	
	function yta_init_clean_cache(){
		$uri = $_SERVER['REQUEST_URI'];
		if(strstr($uri,'/wp-admin')){ return; }
		
		$cache_location = dirname(__FILE__).'/_cache/';
		if(is_dir($cache_location)){
			$d = dir($cache_location);
			$timenow = mktime();
			while (false !== ($entry = $d->read())) {
				switch($entry){
				case '.': case '..': break;
				default:
					$dpast = (filemtime("$cache_location/$entry") - $timenow )/ 86400;
					if($dpast<-4){
						unlink("$cache_location/$entry");
					}
					break;
				}
			}
			$d->close();
		}
	}
	
	
	/*
	 * Publish WP posts when time has come
	 */
	function yta_init_posts(){

		global $wpdb;

		$scheduledIDs =  $wpdb->get_col(
			"SELECT `ID` FROM `{$wpdb->posts}` ".
			"WHERE ( ".
			" ((`post_date` > 0 )&& (`post_date` <= CURRENT_TIMESTAMP())) OR ".
			" ((`post_date_gmt` > 0) && (`post_date_gmt` <= UTC_TIMESTAMP())) ".
			") AND `post_status` = 'future'"
		);

		if(!count($scheduledIDs)) return;

		foreach($scheduledIDs as $scheduledID){

			if(!$scheduledID) continue;

			wp_publish_post($scheduledID);

		}

	}

	/*
	 * Publish comments at required times
	 */
	function yta_init_comments() {
		global $wpdb;
		$tbl_yta = $wpdb->prefix . "yta";
		$tbl_ytaComments = $wpdb->prefix . "ytacom";

		$query = "SELECT * FROM $tbl_ytaComments WHERE date <= UTC_TIMESTAMP() ORDER BY date asc "; //LIMIT 15";

		$comments = $wpdb->get_results( $query );

		foreach ($comments as $comment) {

			$commentStatus =  $wpdb->get_var("SELECT comment_status FROM $wpdb->posts WHERE ID = '" . $comment->postid . "'");

			if ($commentStatus == 'open') {

				$commentData = array(
					'comment_post_ID' => $comment->postid,
					'comment_author' => $comment->author,
					'comment_content' => $comment->content,
					'comment_date' => get_date_from_gmt($comment->date),
					'comment_approved' => 1,
				);

				$comment_id = wp_insert_comment($commentData);

			}

			$query = "DELETE FROM $tbl_ytaComments WHERE id = '" . $comment->id . "'";

			$results = $wpdb->query( $query );

		}

	}

	function yta_SCFlush(){
		static $output_handler = null;
		if ($output_handler === null)
		{
			$output_handler = @ini_get('output_handler');
		}

		if ($output_handler == 'ob_gzhandler')
		{
			// forcing a flush with this is very bad
			return;
		}

		flush();
		if (function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false)
		{
			@ob_flush();
		}
		else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE)
		{
			@ob_end_flush();
			@ob_start();
		}
	}

	
	
	function post_videos($silent=0,$pd1='',$pd2=''){
		global $wpdb;
		$tbl_yta = $wpdb->prefix . "yta";
		$tbl_ytaComments = $wpdb->prefix . "ytacom";
		$tbl_ytaVideos = $wpdb->prefix . "ytavid";
		
		$query = "SELECT data FROM $tbl_yta WHERE field = 'keywords'";
	    $keywords = stripslashes($wpdb->get_var($query));
	   
		$query = "SELECT data FROM $tbl_yta WHERE field = 'post_qty'";
		$post_qty = stripslashes($wpdb->get_var($query) );

		$query = "SELECT data FROM $tbl_yta WHERE field = 'post_freq'";
		$post_freq = stripslashes($wpdb->get_var($query) );
			
		global $wp_plug_url;
		if(! $silent){
		?>
		<style type="text/css">
		h3.yta_head{
			margin:0px;
			padding: 0px;
		}
		.yta_loading{
			height : 150px;
			overflow:scroll;
			background-color:#FFFFE0;
			border-color:#E6DB55;
		}
		.yta_loading_message{
			margin-bottom: 10px;
		}
		.yta_loading_message h4{
			margin:0px;
			padding: 0px;
		}
		.yta_loading_message p{
			margin:0px;
			padding: 0px;
		}
		</style>
		<div class="yta_loading_message" align="center">
			<h4>Retrieving Youtube Videos</h4>
			<br />
			<img src="<?php echo $wp_plug_url; ?>/images/loading.gif" width="86" height="10" alt="" title="" />
			<br />
			<p>
			<?php echo read_phelp('loading_top_msg');?>
			</p>
		</div>
		<script type="text/javascript">
			jQuery(function() {
				jQuery('.yta_loading_message').hide();
			});
		</script>
		
		<?php
	}
	
	$query = "SELECT data FROM $tbl_yta WHERE field = 'keywords'";
	$keywords = stripslashes($wpdb->get_var($query) );

	$query = "SELECT data FROM $tbl_yta WHERE field = 'categories'";
	$categories = stripslashes($wpdb->get_var($query) );

	$query = "SELECT data FROM $tbl_yta WHERE field = 'negative_keywords'";
	$negative_keywords = stripslashes($wpdb->get_var($query) );

	$query = "SELECT data FROM $tbl_yta WHERE field = 'comment_options'";
	$comment_options = stripslashes($wpdb->get_var($query) );

	$query = "SELECT data FROM $tbl_yta WHERE field = 'description_options'";
	$description_options = stripslashes($wpdb->get_var($query) );

	$query = "SELECT data FROM $tbl_yta WHERE field = 'post_qty'";
	$post_qty = stripslashes($wpdb->get_var($query) );

	$query = "SELECT data FROM $tbl_yta WHERE field = 'post_freq'";
	$post_freq = stripslashes($wpdb->get_var($query) );

	$query = "SELECT data FROM $tbl_yta WHERE field = 'post_timespan'";
	$post_timespan = stripslashes($wpdb->get_var($query) );

	$query = "SELECT data FROM $tbl_yta WHERE field = 'post_timeunit'";
	$post_timeunit = stripslashes($wpdb->get_var($query) );

	  
	set_time_limit(0);
		
	/*
	$freq_div = array( 1=>1, 2=>7, 3=>30 );
	if($post_freq==1){
		$qtyx = $post_qty;
	}
	else{
		$qtyx = round($freq_div[$post_freq]/$post_qty);
	}
	*/
	$qtyx = round($post_qty / 7)+1;
	
	$kws = stripslashes($keywords);

		$kwsArr = explode("\r\n", $kws);

		foreach ($kwsArr as $key => $keyw) {
		   if (preg_match('/^\s*$/', $keyw) > 0) {
			   unset($kwsArr[$key]);
		   }
		}
		$categories = stripslashes($categories);
		$cats = explode("\r\n", $categories);
		
		$cKw = count($kwsArr);
		
		$max_results = (ceil($qty / $cKw ) > 50 ) ? 50
			: ceil ( $qty / $cKw);
		
		$max_results = 50; //fed, always use many keywords
		
		$nkws = stripslashes($negative_keywords);

		$nkwsArr = explode("\r\n", $nkws);

		foreach ($nkwsArr as $key => $nkeyw) {

		   if (preg_match('/^\w/', $nkeyw) == 0) {

			   unset($nkwsArr[$key]);

		   }

		}

		$nks = '';
		foreach ($nkwsArr as $key => $nkeyw) {
			$nks .= " -$nkeyw";
		}
		
		if(! class_exists('SimplePie')){
			include dirname(__FILE__)."/lib/simplepie.php";
		}
		$cache_location = dirname(__FILE__).'/_cache/';
		if(! is_dir($cache_location)){
			mkdir($cache_location);
			if(! is_dir($cache_location)){
				chmod($cache_location,0777);
			}
		}
		if(! is_dir($cache_location)){
			$cache_location = '';
		}

		$sxmlArr = array();
		$sxmlArrK = array();
		//	$max_results = $qtyx;
		$max_results = 50; //fed, always use many keywords
		foreach($kwsArr as $ctrK => $kwrd) {
			yta_SCFlush();
			
			$kwrd = urlencode("$kwrd $nks");
			$rss = "http://gdata.youtube.com/feeds/api/videos?";
			//	$rss2 = "q=$kwrd&client=ytapi-youtube-search&alt=rss&v=2&max-results=$max_results&orderby=relevance";
			$rss2 = "q=$kwrd&client=ytapi-youtube-search&max-results=$max_results&orderby=relevance";
			//	$rss = $rss.urlencode($rss2);
			$rss = "$rss$rss2";
			
			$feed = new SimplePie();
			if($cache_location){
				$feed->set_cache_location($cache_location);
			}
			$feed->set_feed_url($rss);
			$feed->init();
			
			$avids = Array();
			foreach($feed->get_items() as $item){
				preg_match_all('@http://.*youtube.*/watch.*v=(.*)&|http://.*youtube.*/watch.*v=(.*)@',
					$link = $item->get_permalink(), $out);
				if(isset($out[1][0]))
					$yid = $out[1][0];
				if(! $yid){
					if(isset($out[2][0]))
						$yid = $out[2][0];
				}

				$avid = Array();
				$avid['yid'] = $yid;
				$avid['title'] = $item->get_title();
				$avid['url'] = $url = "http://www.youtube.com/v/$yid";
				$avid['description'] = $item->get_description();
				
				$ccats = Array();
				$categories = $item->get_categories();
				foreach($categories as $ccat){
					$cterm = $ccat->term;
					if(strstr($cterm,'/schemas/')){
						continue; //skip this
					}
					
					$ccats[] = $cterm;
				}
				$avid['categories'] = join(', ', $ccats);
				$avids[] = $avid;
				
			}
			unset($feed);
			
			$sxmlArr[] = $avids;
			$sxmlArrK[] = $cats[$ctrK];
			
		}
		
		$datewhen = get_option("autopost_datewhen");
		if(!$datewhen) {
			$datewhen=1;
		}
		
		$template = get_option("autopost_template");
		$datewhat = get_option("autopost_datewhat");
		$datewhen = get_option("autopost_datewhen");
		$datenumber = get_option("autopost_datenumber");
	
		
		$timenow = mktime();
		switch($datewhen){
		case 2:
			$date_operator = '-';
			break;
		default:
			$date_operator = '+'; //future
			break;
		}
	
		$date_interval = 'weeks';
		
			
		if((! $pd1) or (! $pd2)){

			switch($datewhen){
			case 2:			
				$ds1 = strtotime("- $datenumber $date_interval",$timenow);
				$ds2 = $timenow;
				
				$start_date = date('F j, Y', $ds1);
				$last_date = date('F j, Y', $ds2);
				
				break;
			default:
				//Today
				$date_interval = '';
				$ds1 = strtotime("+1 sec",$timenow);
				
				$ds2 = strtotime('+1 month', $timenow);
				$ds2 = strtotime(date("F j, Y 11:59:59", $ds2));
				
				$start_date = date('F j, Y', $ds1);
				$last_date = date('F j, Y', $ds2);
				
				break;
			}
			if(! $silent){
			echo "<h3 class=\"yta_head\">$start_date - $last_date</h3>";
			}
			
		}
		else{
			
			$ds1 = $pd1;
			$ds2 = $pd2;
			
			$start_date = date('F j, Y', $ds1);
			$last_date = date('F j, Y', $ds2);
			
			if(! $silent){
			echo "<h3 class=\"yta_head\">$start_date - $last_date</h3>";
			}
			
			$date_interval = 'weeks';
		}
		
		
		if(! $silent){
		?>
		<div class="yta_loading">
		
		<?php  /*  
		<?=$qtyx;?> video(s) per day
		  */ ?>
		<?=$post_qty;?> videos per week
		<br />
		<?php
		}
		
		yta_SCFlush();
			
		$ds1a = strtotime('-1 days',$ds1);
		
		$ds2a = $ds2;
		
		$counter = 0;

		$newposts = 0;
		
		/* Get cursing words */
		
		$pat_cursing = '/shit/i';
		$cursing_htm = dirname(__FILE__)."/h/bad_words.htm";
		if(file_exists($cursing_htm)){
			$cursing_htm = file_get_contents($cursing_htm);
			$pat_cursing = preg_split("/\n|\r\n/",$cursing_htm);
			$pat_cursing = array_map('trim',$pat_cursing);
			$pat_cursing = array_filter($pat_cursing);
			$pat_cursing = join('|',$pat_cursing);
			$pat_cursing = str_replace(".",'\.',$pat_cursing);
			$pat_cursing = str_replace("(",'\(',$pat_cursing);
			$pat_cursing = str_replace(")",'\)',$pat_cursing);
			$pat_cursing = "/$pat_cursing/iu";
		}
		/* Get cursing words */

		
		switch($post_qty){
		case 4:
			//	4 vids per week
			$freqs_poster = Array(1,0,1,0,1,0,1);
			break;
		case 7:
			//	7 vids per week
			$freqs_poster = Array(1,1,1,1,1,1,1);
			break;
		case 10:
			//	10 vids per week
			$freqs_poster = Array(1,2,1,2,1,2,1);
			break;
		case 15:
			//	15 vids per week
			$freqs_poster = Array(2,3,2,2,2,2,2);
			break;
		case 20:
		default:
			//	20 vids per week
			$freqs_poster = Array(3,2,3,3,3,3,3);
			break;
		}
		
		if($sxmlArr){
		$skippy = 0;
		$wk_day = 0;
		while ($ds1a<=$ds2a){
			$ds1a = strtotime('+1 days',$ds1a);
			
			$rmin = rand(13,45);
			$rsec = rand(10,30);
			$ds1a = strtotime(date("F j, Y 10:$rmin:$rsec", $ds1a));
			
			$posted = 0;
		
			if($date_operator=='+'){
				if($ds1a>$ds2a){ break; }
			}
			else{
				if($datewhen==1){
					//today
					if($ds1a>$ds2a){ break; }
				}
				else{
					if($ds1a>=$ds2a){ break; }
				}
			}
			
			++$wk_day;
			if($wk_day>7){
				$wk_day = 1;
				if(! $silent){
					echo "<br />\r\n";
					echo "<br />\r\n";
				}
				$skippy = 0;
			}
			else{
				if(! $silent){
					echo "<br />\r\n";
				}
			}
			$curd = date('M j', $ds1a);
			
			$qtyx = $freqs_poster[$wk_day-1];
			
			if(! $qtyx){
				continue;
			}
			
			if(! $silent){
				/*
				echo "Day $wk_day";
				echo " ($qtyx videos)";
				echo "<br />";
				*/
			}
			
			if(! $silent){
				echo $curd;
			}
			yta_SCFlush();
			
			/* Post it depend on how many posts already there * /
			$ds1a_2 = date('Y-m-d',$ds1a);
			$sql = "select count(ID) from `{$wpdb->posts}`
			WHERE date(`post_date`) = '$ds1a_2'
			and post_status not in ('trash','auto-draft','inherit')
			";
			
			$posted_before = $wpdb->get_var($sql);
			if($posted_before>=$qtyx){
				if(! $silent){
					echo "&nbsp;(<b>$posted_before</b> posts on this day)";
				}
				continue;
			}
			$posted += $posted_before;
			/* Post it depend on how many posts already there */
			
			$tries = 0;
			
			while($tries<=count($sxmlArr)){
			++$tries;
			
			foreach($sxmlArr as $sCtr => $vids){
				if($sCtr<$skippy){ continue; } //Force keyword rotations
				
				$found = 0;
				
				foreach($vids as $avid){
					$yid = $avid['yid'];
					$query = "SELECT id FROM $tbl_ytaVideos WHERE url = '$yid' LIMIT 1";
					$urlres = $wpdb->get_var($query);
					if($urlres){
						continue;
					}
					 
					$url = $avid['url'];
					$title = $avid['title'];
					$tags = $avid['categories'];
					
					$cat = $sxmlArrK[$sCtr];
					
					$metakey = $tags;
					$metakey = strip_tags(stripslashes($metakey));
					$metakey = "$cat, $metakey";	//mix the video tags and searched-for category
					
					$metakey = str_replace("'",'',$metakey);
					$metakey = str_replace('"','',$metakey);
					$metakey = str_replace('/','',$metakey);
					$metakey = str_replace('\\','',$metakey);
					
					$metadesc = $avid['description'];
					$metadesc = strip_tags(stripslashes($metadesc));
					
					$metadesc = str_replace("'",'',$metadesc);
					$metadesc = str_replace('"','',$metadesc);
					$metadesc = str_replace('/','',$metadesc);
					$metadesc = str_replace('\\','',$metadesc);
					
					//	$metadesc = addslashes($metadesc);
					
					$mins_to_add = round(147,318);
					$ds1a = strtotime("+$mins_to_add mins",$ds1a);
					
					$found = 1;
					break;
				}
				
				if(! $found){
					++$skippy;
					if($skippy >= count($sxmlArr)){
						$skippy =0;
					}
					
					continue;
				}
				
				$description = $metadesc;
				/*
				switch ($description_options) {
				case 1:
					$description = '';
					break;
				case 2:
					$description = $metadesc;
					break;
				case 3:
				default:
					if (rand(0, 11) > 5) {
						$description = $metadesc;
					} else {
						$description = '';
					}
				}
				*/
				
				
				// Ready to insert
				 $video = '<center><object width="425" height="344"><param name="movie" value="%url%&feature=player_embedded&hl=en"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="%url%&feature=player_embedded&hl=en" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object></center>';
				$url .= '&fs=1&rel=0';
				$video = str_replace('%url%',$url,$video);
			
				$textcontent = get_option("autopost_template");
				
				/*
				$query = "SELECT data FROM $tbl_yta WHERE field = 'comment_options'";
				$comment_options = stripslashes($wpdb->get_var($query) );
				
				switch ($comment_options) {
				case 1:
					$addComments = false;
					break;
				case 2:
					$addComments = true;
					break;
				case 3:
				default:
				if (rand(0, 11) > 5) {

					$addComments = false;

				} else {

					$addComments = true;
				}
				}
				*/
				
				if(! stristr($textcontent,'[video description]')){
					$description = '';
				}
				
				
				$addComments = 0;
				if(stristr($textcontent,'[comments]')){
					$addComments = 1;
				}
				$textcontent = str_replace("[comments]",'',$textcontent);
				
				$textcontent = str_replace("[video]",$video,$textcontent);
				$textcontent = str_replace("[video description]",$description,$textcontent);
				
				// Create post object
				$post = array();
				
				if($cat){
					$cat =  str_replace('\\','',$cat);
					$cat = preg_replace("/[^a-zA-Z0-9\s]/", "", $cat);
					$cat = ucwords(strtolower(str_replace('"','',$cat)));
					$cat[0] = strtoupper($cat[0]);
					
					if($cat!='Array'){
						$catid = get_cat_ID($cat);
						
						if($catid == 0){
							$catid = wp_create_category($cat);
						}
						$post['post_category'] = array($catid);
					}
				}

				$post['post_title'] = $title;					
				$post['post_content'] = $textcontent;
				$post['post_status'] = 'future';

				$ndays = $pastfuture * $ndays;					
				$ndays = $ndays." days";

				$post_time = strtotime( $ndays ) + $counter++  * $freq_div[$post_freq] * 24 * 60 * 60 / $post_qty;
				
				$post['post_date'] = date('Y-m-d H:i:s',$ds1a);
				
				if(! $silent){
				echo '<br />';
				echo '&nbsp;';
				echo '&nbsp;';
				echo date('g:i a',$ds1a);
				echo '&nbsp;';
				echo '&nbsp;';
				
				echo $posted+1;
				echo '/';
				echo $qtyx;
				echo '&nbsp;';
				echo "<b>$yid</b>";
				echo '&nbsp;';
				echo $title;
				}
				yta_SCFlush();
				$postid = wp_insert_post($post);
				//$postid = 1;
				if ($postid != 0) {
					$keyopt = "autopost".$postid."key";
					$descopt = "autopost".$postid."desc";
					update_option($keyopt,$metakey);
					update_option($descopt,$metadesc);
					
					++$newposts;

					$query = "INSERT INTO $tbl_ytaVideos ( url ) " .
								"VALUES ( '$yid' )";

					$resultsv = $wpdb->query( $query );

					$tag_ids = explode(', ', $tags);

					$restags = wp_set_object_terms( $postid, $tag_ids, 'post_tag');
					
					
					
					if ($addComments) {

						$commHrs = 96; //round(rand(2,8));
						
						if(! $silent){
						?>
						&nbsp; fetching comments...
						<?php
						yta_SCFlush();
						}
						
						$rss_comments = "http://gdata.youtube.com/feeds/api/videos/$yid/comments";
						
						$feed = new SimplePie();
						if($cache_location){
							$feed->set_cache_location($cache_location);
						}
						$feed->set_feed_url($rss_comments);
						$feed->init();
						
						$comment_time = $ds1a;
						$mins_to_add = round(65,233);
						$comment_time = strtotime("+$mins_to_add mins",$comment_time);
						
						$feed_comments = $feed->get_items();
						
						if(! $silent){
						?>
						(<?=count($feed_comments);?>)
						<?php
						yta_SCFlush();
						}
						foreach($feed_comments as $item){
							$content = $item->get_content();
							
							/* Dont add if the comment have bad words */
							if(preg_match($pat_cursing,$content)){ continue; }

							$author = $item->get_author()->name;
							
							$sq_comm = get_gmt_from_date(date('Y-m-d H:i:s', $comment_time));
								
							/*
							echo date(' H:i:s',$comment_time);
							echo ' ';
							echo $author;
							echo '<br />';
							*/
							
							$author = addslashes($author);
							$content = addslashes($content);
							$query = "INSERT INTO $tbl_ytaComments ( postid, author, content, date) " .
									"VALUES ( '$postid', '$author', '$content', '$sq_comm')";
							
							$results1 = $wpdb->query( $query );
							
							$mins_to_add = round(68,150);
							$comment_time = strtotime("+$mins_to_add mins",$comment_time);
						}
						unset($feed);
					}
					++$posted;
					
					++$skippy;
					if($skippy >= count($sxmlArr)){
						$skippy =0;
					}
					
					if($posted>=$qtyx){
						break;
					}
				
				}
			}
			
			//tries
			
				if($posted>=$qtyx){
					break;
				}
			
			}
			
			
			/*
			if(! $date_interval){
				break; //Today, so only do 1 day
			}
			*/
			
		}
		}
		if(! $silent){
		?>
		</div>
		<?php
		}
		
		/* Cleanup objects from memory */
		unset($sxmlArr);
		unset($sxmlArrK);
		/* Cleanup objects from memory */
		
		return $newposts;
	}






	/*
	 * Control publishing of posts and comments
	 */
	define('SCHEDULEDYTA_DELAY', 2); // Minutes

	define('SCHEDULEDYTA_TRANSIENT', 'scheduled_yta');

	function yta_init(){
		remove_action('publish_future_post', 'check_and_publish_future_post');

		if(get_transient(SCHEDULEDYTA_TRANSIENT)) return;
		set_transient(SCHEDULEDYTA_TRANSIENT,  1, abs(intval(SCHEDULEDYTA_DELAY)) * 2);
		
		yta_init_posts();
		yta_init_comments();
		
		yta_init_posts_auto();
		yta_init_clean_cache();
		
	}
	
	
	
	
	

	function reset_the_template()
		{
		update_option("autopost_template","[video]\n\n[video description]\n\n[comments]");
		}
		
	function js_header()
		{
		wp_print_scripts( array( 'sack' ));
		}

	add_action('wp_ajax_my_special_action', 'reset_the_template');
	add_action('wp_ajax_nopriv_my_special_action', 'reset_the_number');
	add_action('admin_head', 'js_header' );
	
?>