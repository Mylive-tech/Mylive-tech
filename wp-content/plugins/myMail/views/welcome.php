<div class="wrap about-wrap">

	<h1>Welcome to MyMail 2.1</h1>

	<div class="about-text">
		Easily create, send and track your Newsletter Campaigns<br>
		<a href="https://twitter.com/mymailapp" class="twitter-follow-button" data-show-count="false"></a>
		<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		<a href="https://mymail.newsletter-plugin.com/wp-content/plugins/myMail/form.php?id=3&iframe=1" class="mymail-subscribe-button">Subscribe to the MyMail Newsletter</a><script type="text/javascript">!function(my,m,a,i,l){my[l]=my[l]||(function(){l=m.createElement(a);m=m.getElementsByTagName(a)[0];l.async=1;l.src=i;m.parentNode.insertBefore(l,m);return !0}())}(window,document,'script','//mymailapp.github.io/v1/button.js','MyMailSubscribe');</script>

	</div>

	<div class="mymail-badge">Version <?php echo MYMAIL_VERSION ?></div>


	<h2 class="nav-tab-wrapper">
		<a href="edit.php?post_type=newsletter&page=mymail_welcome" class="nav-tab nav-tab-active">What’s New</a>
		<?php if(current_user_can('mymail_manage_templates' )) : ?>
		<a href="edit.php?post_type=newsletter&page=mymail_templates&more" class="nav-tab">Templates</a>
		<?php endif; ?>
		<?php if(current_user_can('install_plugins' )) : ?>
		<a href="edit.php?post_type=newsletter&page=mymail_addons" class="nav-tab">Add-Ons</a>
		<?php endif; ?>

	</h2>


<?php if(!mymail_option('purchasecode')): ?>
	<div class="license">
		<?php if(isset($_POST['purchasecode']) && !empty($_POST['purchasecode'])):
			mymail_update_option('purchasecode', esc_attr($_POST['purchasecode']));
		?>
		<h3>Thanks, your license code has been saved!</h3>
		<?php else : ?>
		<h3>Enable Automatic Updates by providing your License Code.</h3>
		<p class="description">MyMail can get updated automatically so you can always work with the latest version and its features. <a href="https://help.revaxarts.com/where-is-my-purchasecode/" class="external">Where can I find it?</a></p>
		<form action="edit.php?post_type=newsletter&page=mymail_welcome" method="POST">
			<input type="text" class="widefat purchasecode" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" name="purchasecode" required pattern="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}" autocomplete="off" maxlength="36">
			<input type="submit" class="button button-primary" value="Save">
		</form>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php
if ( get_transient( '_mymail_translation' ) ) :
	$url = add_query_arg(array(
		'action' => 'do-translation-upgrade',
		'_wpnonce' => wp_create_nonce( 'upgrade-translations' ),
	), admin_url( 'update-core.php' ));
	?>
	<div class="update-nag"><p><strong><?php _e('MyMail is available in your language.', 'mymail') ?></strong> <a class="button button-primary button-small" href="<?php echo esc_url($url) ?>"><?php _e('load it now', 'mymail'); ?></a></p></div>
	<?php
endif;
?>

		<div class="feature-section two-col">
			<div class="col">
				<div class="media-container">
					<img src="//mymailapp.github.io/welcome/forms_interface.jpg">
				</div>
				<h3>Forms Interface</h3>
				<p>Forms are now more easy to manage and can be found in the Newsletter sub menu. It's easy to define fields, design and settings for each one.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mymail_forms">Goto Forms</a></div>
			</div>
			<div class="col">
				<div class="media-container">
				<video loop="" muted="" preload="auto" autoplay src="https://mymailapp.github.io/videos/subscribe_button.mp4" poster="https://mymailapp.github.io/welcome/subscribe_button.png" style="opacity: 1;">
					<source src="https://mymailapp.github.io/videos/subscribe_button.mp4" type="video/mp4">
					<source src="https://mymailapp.github.io/videos/subscribe_button.webm" type="video/webm">
				</video>
				</div>
				<h3>Subscribe Button</h3>
				<p>Embed your subscribe button just like other share buttons on any website and increase your subscriber base.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mymail_forms">Goto Forms</a></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="//mymailapp.github.io/welcome/templates.jpg">
				</div>
				<h3>Activate Templates</h3>
				<p>Activate premium templates with your licensescode instead of downloading, unzipping and reuploading it.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mymail_templates&more">Checkout Templates</a></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="//mymailapp.github.io/welcome/frontpage.jpg">
				</div>
				<h3>Updated Newsletter Frontpage</h3>
				<p>The webversion of the newsletter campaigns got some new social icons. No need for images anymore.</p>
				<div class="return-to-dashboard"><a href="options-general.php?page=newsletter-settings#frontend">Setup Frontpage</a></div>
			</div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<div class="media-container">
					<img src="//mymailapp.github.io/welcome/dashboard_widget.jpg">
				</div>
				<h3>Improved Dashboard Widget</h3>
				<p>See your subscriber grows directly on the Dashboard.</p>
				<div class="return-to-dashboard"><a href="index.php">Goto Dashboard</a></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="//mymailapp.github.io/welcome/subscriber_rating.jpg">
				</div>
				<h3>User Rating</h3>
				<p>You can now see the engagement level of your subscribers and target your best users with specific campaigns.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mymail_subscribers">Goto Subscribers</a></div>
			</div>
		</div>


		<div class="changelog">
			<h3>Under the Hood</h3>

			<div class="feature-section under-the-hood three-col">
				<div class="col">
					<h4>Honeypots for Forms</h4>
					<p>Forms have now an additional spam prevention with a honeypot.</p>
				</div>
				<div class="col">
					<h4>Segmentation with regular expressions</h4>
					<p>Segmentation is now even more flexible when using regular expression.</p>
				</div>
				<div class="col">
					<h4>Do-Not-Track feature</h4>
					<p>MyMail can respect you subscribers <a href="http://donottrack.us/">Do-Not-Track</a> settings to respect their privacy. Enable this option on the <a href="options-general.php?page=newsletter-settings#subscribers">settings page</a></p>
				</div>
				<div class="col">
					<h4>Ignoring Posts</h4>
					<p>If you use dynamic post tags you can exclude certain post with a <code>mymail_ignore</code> custom field.</p>
				</div>
				<div class="col">
					<h4>Background images</h4>
					<p>Some background images in templates can now get changed like other images in your campaign.</p>
				</div>
				<div class="col">
					<h4>Single Opt Out</h4>
					<p>Canceling subscription is now faster with a single click. Enable this option on the <a href="options-general.php?page=newsletter-settings#subscribers">settings page</a>.</p>
				</div>
			</div>
			<div class="feature-section under-the-hood three-col">
				<div class="col">
					<h4>Compressed Assets</h4>
					<p>All JavaScript and CSS files are now compressed via YUIcompresser to increase performance.</p>
				</div>
				<div class="col">
					<h4>Bounce Handling for Confirmation</h4>
					<p>If confirmation messages bounce back MyMail can handle them and change the status of the user.</p>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="edit.php?post_type=newsletter">Go to Newsletter</a>
			</div>

		</div>

<div class="clear"></div>



<div id="ajax-response"></div>
<br class="clear">
</div>
