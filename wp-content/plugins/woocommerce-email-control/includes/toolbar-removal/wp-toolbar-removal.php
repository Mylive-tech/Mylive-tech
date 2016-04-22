<?php

	if ( !function_exists( 'add_action' ) )
		{
			header( 'HTTP/0.9 403 Forbidden' );
			header( 'HTTP/1.0 403 Forbidden' );
			header( 'HTTP/1.1 403 Forbidden' );
			header( 'Status: 403 Forbidden' );
			header( 'Connection: Close' );
				exit();
		}

	global $wp_version;

	if ( $wp_version < 3.1 )
		{
			wp_die( __( 'This Plugin Requires WordPress 3.1+ or Greater: Activation Stopped!', 'email-control' ) );
		}

	function wptbr_1st()
		{
			$path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );

			if ( $plugins = get_option( 'active_plugins' ) )
				{
					if ( $key = array_search( $path, $plugins ) )
						{
							array_splice( $plugins, $key, 1 );
							array_unshift( $plugins, $path );
							update_option( 'active_plugins', $plugins );
						}
				}
		}
	add_action( "activated_plugin", "wptbr_1st" );

	function wptbr_rbams()
		{
			echo "\n\n<!--Start Toolbar Removal Code-->\n\n";
			echo '<style type="text/css">#adminmenushadow,#adminmenuback{background-image:none}</style>';
			echo "\n\n<!--End Toolbar Removal Code-->\n\n";
		}

	if ( $wp_version >= 3.2 )
		{
			add_action( 'admin_head', 'wptbr_rbams' );
		}

	function wptbr_rbf28px()
		{
			echo "\n\n<!--Start Toolbar Removal Code-->\n\n";
			echo '<style type="text/css">html.wp-toolbar,html.wp-toolbar #wpcontent,html.wp-toolbar #adminmenu,html.wp-toolbar #wpadminbar,body.admin-bar,body.admin-bar #wpcontent,body.admin-bar #adminmenu,body.admin-bar #wpadminbar{padding-top:0px !important}</style>';
			echo "\n\n<!--End Toolbar Removal Code-->\n\n";
		}
	add_action( 'admin_print_styles', 'wptbr_rbf28px', 21 );

	function wptbr_abtlh()
		{
			echo "\n\n<!--Start Toolbar Removal Code-->\n\n";
?>
<style type="text/css">table#tbrcss td#tbrcss_ttl a:link,table#tbrcss td#tbrcss_ttl a:visited{text-decoration:none}table#tbrcss td#tbrcss_lgt,table#tbrcss td#tbrcss_lgt a{text-decoration:none}</style>
<table style="margin-left:6px;float:left;z-index:100;position:relative;left:0px;top:0px;background:none;padding:0px;border:0px;border-bottom:1px solid #DFDFDF" id="tbrcss" border="0" cols="4" width="97%" height="33">
<tr>
<td align="left" valign="center" id="tbrcss_ttl">
<?php

	echo '<a href="' . home_url() . '">' . __( get_bloginfo(), 'email-control' ) . '</a>';

?>
</td>
<td align="right" valign="center" id="tbrcss_lgt">
<div style="padding-top:2px">
<?php

	echo date_i18n( get_option( 'date_format' ) );

?>

 @ 

<?php

	echo date_i18n( get_option( 'time_format' ) );

?>

<?php

	wp_get_current_user();

	$current_user = wp_get_current_user();

	if ( !( $current_user instanceof WP_User ) )
		return;

	echo ' | ' . $current_user->display_name . '';

	if ( is_multisite() && is_super_admin() )
		{
			if ( !is_network_admin() )
				{
					echo ' | <a href="' . network_admin_url() . '">' . __( 'Network Admin', 'email-control' ) . '</a>';
				}
			else
				{
					echo ' | <a href="' . get_DashBoard_url( get_current_user_id() ) . '">' . __( 'Site Admin', 'email-control' ) . '</a>';
				}
		}

	echo ' | <a href="' . wp_logout_url( home_url() ) . '">' . __( 'Log Out', 'email-control' ) . '</a>';

?>
</div>
</td>
<td width="8">
</td>
</tr>
</table>
<?php
			echo "\n<!--End Toolbar Removal Code-->\n\n";
		}

	if ( $wp_version >= 3.3 )
		{
			//add_action( 'in_admin_header', 'wptbr_abtlh' );
			add_filter( 'show_wp_pointer_admin_bar', '__return_false' );
		}

	function wp_toolbar_init()
		{
			add_filter( 'show_admin_bar', '__return_false' );
			add_filter( 'wp_admin_bar_class', '__return_false' );
		}
	add_filter( 'init', 'wp_toolbar_init', 9 );

	function wptbr_ruppoabpc()
		{
			echo "\n\n<!--Start Toolbar Removal Code-->\n\n";
			echo '<style type="text/css">.show-admin-bar{display:none}</style>';
			echo "\n\n<!--End Toolbar Removal Code-->\n\n";
		}
	add_action( 'admin_print_styles-profile.php', 'wptbr_ruppoabpc' );

	$wp_scripts = new WP_Scripts();
	$wp_styles = new WP_Styles();
	
	
	function remove_all()
		{
			global $wp_scripts;
			if( is_array( $wp_scripts->registered ) && in_array( 'admin-bar', $wp_scripts->registered ) ) {
				wp_deregister_script( 'admin-bar' );
				wp_deregister_style( 'admin-bar' );
			}
		}

	
	
	
	
	remove_action( 'init', 'wp_admin_bar_init' );
	remove_filter( 'init', 'wp_admin_bar_init' );
	remove_action( 'wp_head', 'wp_admin_bar' );
	remove_filter( 'wp_head', 'wp_admin_bar' );
	remove_action( 'wp_footer', 'wp_admin_bar' );
	remove_filter( 'wp_footer', 'wp_admin_bar' );
	remove_action( 'admin_head', 'wp_admin_bar' );
	remove_filter( 'admin_head', 'wp_admin_bar' );
	remove_action( 'admin_footer', 'wp_admin_bar' );
	remove_filter( 'admin_footer', 'wp_admin_bar' );
	remove_action( 'wp_head', 'wp_admin_bar_class' );
	remove_filter( 'wp_head', 'wp_admin_bar_class' );
	remove_action( 'wp_footer', 'wp_admin_bar_class' );
	remove_filter( 'wp_footer', 'wp_admin_bar_class' );
	remove_action( 'admin_head', 'wp_admin_bar_class' );
	remove_filter( 'admin_head', 'wp_admin_bar_class' );
	remove_action( 'admin_footer', 'wp_admin_bar_class' );
	remove_filter( 'admin_footer', 'wp_admin_bar_class' );
	remove_action( 'wp_head', 'wp_admin_bar_css' );
	remove_filter( 'wp_head', 'wp_admin_bar_css' );
	remove_action( 'wp_head', 'wp_admin_bar_dev_css' );
	remove_filter( 'wp_head', 'wp_admin_bar_dev_css' );
	remove_action( 'wp_head', 'wp_admin_bar_rtl_css' );
	remove_filter( 'wp_head', 'wp_admin_bar_rtl_css' );
	remove_action( 'wp_head', 'wp_admin_bar_rtl_dev_css' );
	remove_filter( 'wp_head', 'wp_admin_bar_rtl_dev_css' );
	remove_action( 'admin_head', 'wp_admin_bar_css' );
	remove_filter( 'admin_head', 'wp_admin_bar_css' );
	remove_action( 'admin_head', 'wp_admin_bar_dev_css' );
	remove_filter( 'admin_head', 'wp_admin_bar_dev_css' );
	remove_action( 'admin_head', 'wp_admin_bar_rtl_css' );
	remove_filter( 'admin_head', 'wp_admin_bar_rtl_css' );
	remove_action( 'admin_head', 'wp_admin_bar_rtl_dev_css' );
	remove_filter( 'admin_head', 'wp_admin_bar_rtl_dev_css' );
	remove_action( 'wp_footer', 'wp_admin_bar_js' );
	remove_filter( 'wp_footer', 'wp_admin_bar_js' );
	remove_action( 'wp_footer', 'wp_admin_bar_dev_js' );
	remove_filter( 'wp_footer', 'wp_admin_bar_dev_js' );
	remove_action( 'admin_footer', 'wp_admin_bar_js' );
	remove_filter( 'admin_footer', 'wp_admin_bar_js' );
	remove_action( 'admin_footer', 'wp_admin_bar_dev_js' );
	remove_filter( 'admin_footer', 'wp_admin_bar_dev_js' );
	remove_action( 'locale', 'wp_admin_bar_lang' );
	remove_filter( 'locale', 'wp_admin_bar_lang' );
	remove_action( 'wp_head', 'wp_admin_bar_render', 1000 );
	remove_filter( 'wp_head', 'wp_admin_bar_render', 1000 );
	remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
	remove_filter( 'wp_footer', 'wp_admin_bar_render', 1000 );
	remove_action( 'admin_head', 'wp_admin_bar_render', 1000 );
	remove_filter( 'admin_head', 'wp_admin_bar_render', 1000 );
	remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 );
	remove_filter( 'admin_footer', 'wp_admin_bar_render', 1000 );
	remove_action( 'admin_footer', 'wp_admin_bar_render' );
	remove_filter( 'admin_footer', 'wp_admin_bar_render' );
	remove_action( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render', 1000 );
	remove_filter( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render', 1000 );
	remove_action( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render' );
	remove_filter( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render' );
	
	

	function wptbr_rml( $links, $file )
		{
			if ( $file == plugin_basename( __FILE__ ) )
				{
					$links[] = '<a href="//slangji.wordpress.com/donate/">Donate</a>';
					$links[] = '<a href="//slangji.wordpress.com/contact/">Contact</a>';
					$links[] = '<a href="//slangji.wordpress.com/plugins/">Other</a>';
				}
			return $links;
		}
	add_filter( 'plugin_row_meta', 'wptbr_rml', 10, 2 );

	function wptbr_hfl()
		{
			echo "\n<!--Plugin WP Toolbar Removal 2014.0507.0391 Active - Tag ".md5(md5("".""))."-->\n";
			echo "\n<!--Site Optimized to Speedup Control Panel Minimize Memory Consumption with Disabled";

			global $wp_version;

			if ( $wp_version >= 3.3 )
				{
					echo " Toolbar";
				}

			if ( $wp_version >= 3.1 )
				{
					if ( $wp_version < 3.3 )
						{
							echo " Admin Bar";
						}
				}

			echo "-->\n\n";
		}
	add_action( 'wp_head', 'wptbr_hfl' );
	add_action( 'wp_footer', 'wptbr_hfl' );

	if( file_exists( plugin_dir_path( __FILE__ ) . 'wp-toolbar-removal.js' ) )

		{

			add_action( 'admin_enqueue_scripts' , 'wp_toolbar_removal_js' );

		}

	function wp_toolbar_removal_js()

		{

			wp_enqueue_script( 'wp-toolbar-removal-js' , plugins_url( 'wp-toolbar-removal.js' , __FILE__ ) , array( 'admin-bar' , 'common' ) );

		}

	if( file_exists( plugin_dir_path( __FILE__ ) . 'wp-toolbar-removal.css' ) )

		{

			add_action( 'admin_enqueue_scripts' , 'wp_toolbar_removal_css' );

		}

	function wp_toolbar_removal_css()

		{

			wp_enqueue_style( 'wp-toolbar-removal-css' , plugins_url( 'wp-toolbar-removal.css' , __FILE__ ) );

		}

?>