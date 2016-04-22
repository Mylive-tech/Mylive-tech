<style>
.mymail-badge{
	position: absolute;
	top: 0;
	right: 0;
	font-size: 14px;
	text-align: center;
	font-weight: 600;
	margin: 5px 0 0;
	padding-top: 120px;
	height: 40px;
	display: inline-block;
	width: 150px;
	text-rendering: optimizeLegibility;
	-webkit-box-shadow: 0 1px 3px rgba(0,0,0,.2);
	box-shadow: 0 1px 3px rgba(0,0,0,.2);
	background: #2BB3E7 url(<?php echo WP_PLUGIN_URL ?>/myMail/assets/img/logo.png) no-repeat 50% 34%;
	background-size: 50%;
	color:#fff;
}
.feature-section div, .changelog{
	padding-top: 20px;
}
.feature-postponed{
	font-size:12px;
	padding:3px 6px;
	border-radius:3px;
	margin-top:-20px;
	position: absolute;
}
.about-wrap .feature-section.three-col div {
    width: 30%;
    margin-right: 4.9999999%;
    float: left;
}
.about-wrap .feature-section.three-col .last-feature {
    margin-right: 0;
}
</style>
<div class="wrap about-wrap">

	<h1>Welcome to MyMail 2</h1>

	<div class="about-text">Now you can easily create, send and track your Newsletter Campaigns</div>

	<div class="mymail-badge">Version <?php echo MYMAIL_VERSION ?></div>

	<h2 class="nav-tab-wrapper">
		<a href="edit.php?post_type=newsletter&page=mymail_welcome" class="nav-tab nav-tab-active">What’s New</a>
		<a href="edit.php?post_type=newsletter&page=mymail_templates" class="nav-tab">Templates</a>
		<a href="edit.php?post_type=newsletter&page=mymail_addons" class="nav-tab">Add-Ons</a>
	</h2>

	<div class="changelog">
	<div class="alignright">
		<a href="https://twitter.com/mymailapp" class="twitter-follow-button" data-show-count="false" data-size="large">Follow MyMail on Twitter</a>
		<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</div>
		<h2 class="about-headline-callout">MyMail is now even better!</h2>
		<div class="feature-section col three-col">
			<div class="col-1">
				<img src="https://data.newsletter-plugin.com/welcome/easy_edit.gif">
				<h4>Improved Editor</h4>
				<p>The updated editor comes with a cleaned up UI and new, faster content edit system. Simple click on an element you like to edit and change it's content.</p>
			</div>
			<div class="col-2">
				<img src="https://data.newsletter-plugin.com/welcome/dragndrop.gif">
				<h4>Drag 'n Drop</h4>
				<p>You can now drag 'n drop images from your desktop right into your newsletter. This makes creating campaigns even faster. <br>(requires WordPress 3.9+)</p>
			</div>
			<div class="col-3 last-feature">
				<img src="https://data.newsletter-plugin.com/welcome/imageswap.gif">
				<h4>Image Swap</h4>
				<p>Change the position of your images with a simple drag 'n drop gesture. Hold <kbd>alt</kbd> to copy the image.<br>(requires WordPress 3.9+)</p>
			</div>
		</div>
		<div class="feature-section col three-col">
			<div class="col-2">
				<img src="https://data.newsletter-plugin.com/welcome/wordpress_users.gif">
				<h4>Better WordPress User integration</h4>
				<p>Subscribers with a WordPress user profile are now better merged together. You can sync certain fields on the <a href="options-general.php?page=newsletter-settings#wordpress-users">settings page</a>.</p>
			</div>
			<div class="col-2">
				<img src="https://data.newsletter-plugin.com/welcome/autoupdate.gif">
				<h4>Auto update stats</h4>
				<p>All statistics and counts will now updated automatically on the <a href="edit.php?post_type=newsletter">newsletter overview</a>. No need to refresh the page anymore. <br>(requires WordPress 3.5+)</p>
			</div>
			<div class="col-3 last-feature">
				<img src="https://data.newsletter-plugin.com/welcome/import.gif">
				<h4>Faster Import</h4>
				<p>MyMail 2 now imports subscribers up to 5 times faster comparing to it's previous version. Also working with multiple thousand subscribers shouldn't be a problem anymore.</p>
			</div>
		</div>
		<div class="feature-section col three-col">
			<div class="col-1">
				<img src="https://data.newsletter-plugin.com/welcome/environment.gif">
				<h4>Environment</h4>
				<p>See which Client your customers are using, if they open your campaigns on their mobile phones or their Desktop.</p>
			</div>
			<div class="col-2">
				<img src="https://data.newsletter-plugin.com/welcome/stats.gif">
				<h4>Cleaned up Statistics</h4>
				<p>You get now even better an more accurate statistsics from your sent campaigns. All data fields can get expanded to reveal more details.</p>
			</div>
			<div class="col-3 last-feature">
				<img src="https://data.newsletter-plugin.com/welcome/birthday.gif">
				<h4>Custom Field Dates</h4>
				<p>Send your subscribers a birthday message or any other date based transactional email</p>
			</div>
		</div>
		<div class="feature-section col three-col">
			<div class="col-1">
				<h4>Profile editing for Subscribers</h4>
				<p>You subscribers can now easily updated their profile. Just include a <code>{profile}</code> tag in your campaign to offer a link to the edit form.</p>
			</div>
			<div class="col-2">
				<h4>Follow Up Auto responder</h4>
				<p>Send your subscribers a follow up campaign after they have opened or clicked a campaign</p>
			</div>
			<div class="col-3 last-feature">
				<h4>Action Hook Autoresponders</h4>
				<p>Send campaigns via action hooks. Use <code>do_action('my_trigger_hook')</code> or <code>do_action('my_trigger_hook', $subscriber_id)</code></p>
			</div>
		</div>
	</div>
	<div class="changelog under-the-hood">
		<h3>Under the Hood</h3>

		<div class="feature-section col three-col">
			<div>
				<h4>Dedicate Database Tables</h4>
				<p>Users often requested dedicate tables which separates the MyMail data from the core. This will also improve the loading speed of your site.</p>

				<h4>Pending Subscribers</h4>
				<p>Pending Subscribers are now in the same table as the regular one. This helps to keep things in place.</p>
			</div>
			<div>
				<h4>Nice URLs</h4>
				<p>The URL structure of links in campaigns and subscription page has changed if you use custom permalinks. The old structure will still work though.</p>

				<h4>UTC everywhere</h4>
				<p>All timestamps are now stored in UTC. This helps while moving server or switching your timezone.</p>
			</div>
			<div class="last-feature">
				<h4>Timezone-based Sending</h4>
				<p>Send campaigns based on the users timezone (if available) to deliver mails when your subscribers read them.</p>

				<del><h4>Activate Templates</h4>
				<p>You can now simple activate premium templates with a licensescode instead of downloading, unzipping and reuploading it.</p></del>
				<span class="wp-ui-highlight feature-postponed">feature postponed</span>
			</div>
	</div>

	<hr>


</div>

<div class="clear"></div>



<div id="ajax-response"></div>
<br class="clear">
</div>
