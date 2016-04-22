<?php
/**
 * Section to display author details
 * @author Praveen Rajan
 */
?>
<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
	die('You are not allowed to call this page directly.'); 

$title = __('About the Author');

CvgCore::upgrade_plugin();
?>
<style type="text/css">
	#dashboard-widgets a {
		text-decoration: none;
	}
</style>
<div class="wrap">
	<div class="icon32" id="icon-video">
		<br>
	</div>
	<h2><?php echo esc_html($title);?></h2>
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div style="width: 100%;" class="postbox-container">
				<div class="postbox" style="float:left;">
					<div class="inside">
						<div style="float:left;width:10%;padding:5%;">
							<img width="85" height="85" src="http://www.gravatar.com/avatar/efbe116dd2a8f8ca04fb0d9a3488d0d7?s=85&d=monsterid"></img>
							<br/>
							<b>Praveen Rajan</b>
							<br/>
							<br/>
							<div style="float:left;margin-left:10px;">
								<a href="https://www.facebook.com/praveenr1987"><img src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/facebook.png" /></a>
								<br/>
								<a href="http://in.linkedin.com/in/praveen87"><img src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/linkedin.png" /></a>
								<br/>
								<a href="https://twitter.com/#!/praveen_rajan"><img src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/twitter.png" /></a>
							</div>
						</div>
						<div style="float:left;width:60%;padding:2%;">
							Alas I found my space!!! <img width="20" height="20" src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/1.png" />
							<br />
							<br />
							<label style="color:green;">No features in here!!!</label> <label style="color:red;">No bugs in here!!!</label>
							<br/>
							<br />
							Just a few bits of my info!!! So it isn't really going to take your website space <img width="20" height="20" src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/2.png" />
							<br/>
							<br />
							Let's get started.
							<br/>
							<br />
							<div style="text-align: justify">
							Myself, a Software Professional by work. I started my career into Software Development Industry in 2009 with PHP as my technology. It was then I started playing with WordPress plugins for a project at my firm. Soon I thought of making my own plugins for WordPress and I finally ended up here.
							</div>
							<br />
							<div style="text-align: justify">
							Now I'm into Mobile Appliation Development(iOS, Android and BB Tablet) at my work. I do these stuffs as a hobby.  
							Kindly give your supports by adding your ratings and let the world know that this is the one they are looking for.
							</div>
							<br />
							Apologies for the bugs in this plugin <img width="20" height="20" src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/2.png" /> Do let me know your suggestions and issues through <a href="http://wordpress.org/support/plugin/cool-video-gallery" target="_blank">WordPress Plugin Support Forum</a>.
							<br/>
							<br />
							<b style="font-size:15px;">CODE IT <img width="20" height="20" src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/4.png" /> LEARN IT <img width="20" height="20" src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>/images/4.png" /></b>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>