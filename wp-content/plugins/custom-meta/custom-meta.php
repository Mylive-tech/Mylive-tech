<?php
/*
Plugin Name: Custom Meta
Plugin URI: http://www.unreliablepollution.net/
Description: This plugin adds a widget that's almost like the vanilla meta widget, but it lets you choose what items to show.
Version: 1.0
Author: TheLinx
Author URI: http://www.unreliablepollution.net/
*/

function widget_cmeta_register() {
	function widget_cmeta($args) {
		extract($args);
		$options = get_option('widget_cmeta');
		$title = empty($options['title']) ? 'Meta' : $options['title'];
		$show = array(
			'regstr'	=> $options['show_regstr'],
			'loginout'	=> $options['show_loginout'],
			'regrss'	=> $options['show_regrss'],
			'commrss'	=> $options['show_commrss'],
			'wplink'	=> $options['show_wplink']);
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php if ($show['regstr']) { wp_register(); } ?>
			<?php if ($show['loginout']) { ?><li><?php wp_loginout(); ?></li><?php } ?>
			<?php if ($show['regrss']) { ?><li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php echo attribute_escape(__('Syndicate this site using RSS 2.0')); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li><?php } ?>
			<?php if ($show['commrss']) { ?><li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php echo attribute_escape(__('The latest comments to all posts in RSS')); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li><?php } ?>
			<?php if ($show['wplink']) { ?><li><a href="http://wordpress.org/" title="<?php echo attribute_escape(__('Powered by WordPress, state-of-the-art semantic personal publishing platform.')); ?>">WordPress.org</a></li><?php } ?>
			<?php wp_meta(); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
	}

	function widget_cmeta_control() {
		$options = get_option('widget_cmeta');
		if ( isset($_POST["cmeta-submit"]) ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["cmeta-title"]));
			$newoptions['show_regstr'] = $_POST["cmeta-sregstr"];
			$newoptions['show_loginout'] = $_POST["cmeta-sloginout"];
			$newoptions['show_regrss'] = $_POST["cmeta-sregrss"];
			$newoptions['show_commrss'] = $_POST["cmeta-scommrss"];
			$newoptions['show_wplink'] = $_POST["cmeta-swplink"];
			$options = $newoptions;
			update_option('widget_cmeta', $options);
		}
		if ( $options != $newoptions ) {
		}
		$title = attribute_escape($options['title']);
		$show = array(
			'regstr'	=> $options['show_regstr'],
			'loginout'	=> $options['show_loginout'],
			'regrss'	=> $options['show_regrss'],
			'commrss'	=> $options['show_commrss'],
			'wplink'	=> $options['show_wplink']);
	?>
				<p><label for="cmeta-title"><?php _e('Title:'); ?> <input class="widefat" id="cmeta-title" name="cmeta-title" type="text" value="<?php echo $title; ?>" /></label></p>
				<p><label for="cmeta-sregstr"><input id="cmeta-sregstr" name="cmeta-sregstr" type="checkbox" value="1"<?php if ($show['regstr']) { echo " checked=\"checked\""; } ?> /> Show "Register/Site Admin"</label></p>
				<p><label for="cmeta-sloginout"><input id="cmeta-sloginout" name="cmeta-sloginout" type="checkbox" value="1"<?php if ($show['loginout']) { echo " checked=\"checked\""; } ?> /> Show "Log in/Log out"</label></p>
				<p><label for="cmeta-sregrss"><input id="cmeta-sregrss" name="cmeta-sregrss" type="checkbox" value="1"<?php if ($show['regrss']) { echo " checked=\"checked\""; } ?> /> Show "Entries RSS"</label></p>
				<p><label for="cmeta-scommrss"><input id="cmeta-scommrss" name="cmeta-scommrss" type="checkbox" value="1"<?php if ($show['commrss']) { echo " checked=\"checked\""; } ?> /> Show "Comments RSS"</label></p>
				<p><label for="cmeta-swplink"><input id="cmeta-swplink" name="cmeta-swplink" type="checkbox" value="1"<?php if ($show['wplink']) { echo " checked=\"checked\""; } ?> /> Show "WordPress.org"</label></p>
				<input type="hidden" id="cmeta-submit" name="cmeta-submit" value="1" />
	<?php
	}
	$ops = array('classname' => 'widget_cmeta', 'description' => "Log in/out, admin, feed and WordPress links, configurable" );
	wp_register_sidebar_widget('cmeta', 'Custom Meta', 'widget_cmeta', $widget_ops);
	wp_register_widget_control('cmeta', 'Custom Meta', 'widget_cmeta_control' );
}
add_action('widgets_init', 'widget_cmeta_register');

?>