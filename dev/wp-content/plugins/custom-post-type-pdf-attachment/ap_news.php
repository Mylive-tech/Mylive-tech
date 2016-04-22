<?php
if(!session_id()){
	@session_start();
}

add_action( 'wp_dashboard_setup', 'afo_news_dashboard_widget' );
add_action( 'admin_enqueue_scripts', 'ap_news_enque_scripts' );
add_action( 'admin_init', 'ap_news_news_loader' );

if (!function_exists('ap_news_enque_scripts')) {
	function ap_news_enque_scripts() {
		wp_enqueue_script( 'jquery' );
	}
}

if (!function_exists('ap_news_style')) {
	function ap_news_style(){
		echo '<style>.ap-news span{color:#bdbdbd;}.ap-news li{border-bottom:1px solid #bdbdbd; }</style>';
	}
}

if (!function_exists('ap_news_script')) {
	function ap_news_script(){ 
    	echo '<script type="text/javascript">
			function ap_load_news(){jQuery.ajax({method:"POST",beforeSend:function(){jQuery("#ap_news_feed").html("loading..")},data:{ap_news:"LoadAPNews"}}).done(function(e){jQuery("#ap_news_feed").html(e)})}
        </script>';
	}
}

if (!function_exists('ap_news_news_loader')) {
	function ap_news_news_loader(){
		if( isset($_REQUEST['ap_news']) and $_REQUEST['ap_news'] == 'LoadAPNews' ){
			
			if(!isset($_SESSION['ap_news'])){
				$news = file_get_contents('http://www.aviplugins.com/api/news.php');
				$_SESSION['ap_news'] = $news;
			} else {
				$news = $_SESSION['ap_news'];
			}
			$news = json_decode($news);
			if(is_array($news)){
				echo '<ul class="ap-news">';
				foreach($news as $key => $value){
					echo '<li>
					<h4>'.$value->title.'</h4>
					<span>'.$value->date.'</span>
					<p>'.html_entity_decode($value->desc).'</p>
					</li>';
				}
				echo '</ul>';
			}
			
			exit;
		}
	}
}

if (!function_exists('afonews_dashboard_widget_function')) {
	function afonews_dashboard_widget_function() {
		ap_news_style();
		ap_news_script();
		echo '<div id="ap_news_feed">'._('Click on <strong>Load News</strong> for news feed.').'</div>';
		
		if(isset($_SESSION['ap_news'])){
			echo '<script type="text/javascript">ap_load_news();</script>';
		}
	}
}

if (!function_exists('afo_news_dashboard_widget')) {
	function afo_news_dashboard_widget() {
		wp_add_dashboard_widget( 
		'afonews_dashboard_widget', 
		'aviplugins.com <a href="javascript:ap_load_news();">Load News</a>', 
		'afonews_dashboard_widget_function' 
		);
		global $wp_meta_boxes;
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$afonews_dashboard_widget = array( 'afonews_dashboard_widget' => $normal_dashboard['afonews_dashboard_widget'] );
		unset( $normal_dashboard['afonews_dashboard_widget'] );
		$sorted_dashboard = array_merge( $afonews_dashboard_widget, $normal_dashboard );
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	} 
}