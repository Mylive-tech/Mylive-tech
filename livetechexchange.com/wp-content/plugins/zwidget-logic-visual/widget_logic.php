<?php
/*
Plugin Name: Widget Logic Visual
Plugin URI: http://www.totalbounty.com
Description: Control widgets with WP's conditional tags, visually.  This plugin has options at the bottom of widgets added in "Appearance->Widgets"
Modified by Total Bounty
Author: Total Bounty
Version: 1.52
Author URI: http://www.totalbounty.com
*/ 

define("WLVPLUGINURL",plugins_url('',__FILE__));

global $wlv_options,$wlv_sidebar_widgets;
if((!$wlv_options = get_option('widget_logic_visual')) || !is_array($wlv_options) ) $wlv_options = array();

if (is_admin()) :
	add_action( 'admin_enqueue_scripts'	, 'widget_logic_visual_admin_enqueue_script');
	add_action( 'admin_print_styles'	, 'widget_logic_visual_admin_enqueue_style');			// admin enqueue style
	add_action( 'sidebar_admin_setup'	, 'widget_logic_visual_expand_control');				// save widget changes and add controls to each widget on the widget admin page
	add_action( 'sidebar_admin_page'	, 'widget_logic_visual_options_filter');				// add extra Widget Logic specific options on the widget admin page
	add_filter( 'widget_update_callback', 'widget_logic_visual_widget_update_callback', 10, 3); // save individual widget changes submitted by ajax method
	add_filter( 'plugin_action_links'	, 'widget_logic_visual_charity', 10, 2);				// add my justgiving page

else :
	add_filter( 'sidebars_widgets', 'widget_logic_visual_filter_sidebars_widgets', 10);			// actually remove the widgets from the front end depending on widget logic provided

	if ( isset($wlv_options['widget_logic_visual-options-filter']) && $wlv_options['widget_logic_visual-options-filter'] == 'checked' )
		add_filter( 'dynamic_sidebar_params', 'widget_logic_visual_widget_display_callback', 10); 			// redirect the widget callback so the output can be buffered and filtered
endif;

include('ajax.php');
include('custom.php');

add_action('admin_notices', 'widget_logic_visual_message');

function widget_logic_visual_message()
{
	if(function_exists('widget_logic_widget_update_callback') && function_exists('widget_logic_filter_sidebars_widgets')) :
	?>
    <div class="error">
    	<p>Please deactivate the original Widget Logic plugin before proceeding, you can't run both Widget Logic and Widget Logic Visual at the same time</p>
	</div>
    <?php
	endif;
}

function widget_logic_visual_debug($var)
{
	?><pre><?php
	print_r($var);
	?></pre><?php	
}

function widget_logic_visual_admin_enqueue_script()
{
	wp_register_script('jquery.nyromodal'	,plugin_dir_url(__FILE__).'js/jquery.nyromodal.js');
	
	wp_enqueue_script('jquery.nyromodal');
}

function widget_logic_visual_admin_enqueue_style()
{
	wp_register_style('nwlv-style'		,plugin_dir_url(__FILE__).'css/style.css');
	wp_register_style('jquery.nyromodal',plugin_dir_url(__FILE__).'css/jquery.nyromodal.css');
	
	wp_enqueue_style('nwlv-style');
	wp_enqueue_style('jquery.nyromodal');
}

// CALLED VIA 'sidebar_admin_setup' ACTION
function widget_logic_visual_expand_control()
{	global $wp_registered_widgets, $wp_registered_widget_controls, $wlv_options;

	// if we're updating the widgets, read in the widget logic settings (makes this WP2.5+ only?)
	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) ) :	
		
		// clean up empty options (in PHP5 use array_intersect_key)
		$regd_plus_new=array_merge(array_keys($wp_registered_widgets),array_values((array) $_POST['widget-id']),array('widget_logic_visual-options-filter', 'widget_logic_visual-options-wp_reset_query'));
		
		foreach (array_keys($wlv_options) as $key) :
			if (!in_array($key, $regd_plus_new))
				unset($wlv_options[$key]);
				
		endforeach;
				
	endif;
	
	// check the 'widget content' filter option
	if ( isset($_POST['widget_logic_visual-options-submit']) )
	{	$wlv_options['widget_logic_visual-options-filter']=$_POST['widget_logic_visual-options-filter'];
		$wlv_options['widget_logic_visual-options-wp_reset_query']=$_POST['widget_logic_visual-options-wp_reset_query'];
	}

	// before updating widget logic options (which gets EVALd)
	// we could test current_user_can('edit_theme_options')
	// although we shouldn't be able to get here without that capability anyway?

	// intercept the widget controls to add in our extra text field
	// pop the widget id on the params array (as it's not in the main params so not provided to the callback)
	foreach ( $wp_registered_widgets as $id => $widget )
	{	// controll-less widgets need an empty function so the callback function is called.
		if (!$wp_registered_widget_controls[$id])
			wp_register_widget_control($id,$widget['name'], 'widget_logic_visual_empty_control');
		$wp_registered_widget_controls[$id]['callback_wlv_redirect']=$wp_registered_widget_controls[$id]['callback'];
		$wp_registered_widget_controls[$id]['callback']='widget_logic_visual_extra_control';
		array_push($wp_registered_widget_controls[$id]['params'],$id);	
	}
}

// added to widget functionality in 'widget_logic_visual_expand_control' (above)
function widget_logic_visual_empty_control() {}

// added to widget functionality in 'widget_logic_visual_expand_control' (above)
function widget_logic_visual_extra_control()
{	
	global $wp_registered_widget_controls, $wlv_options;

	$params=func_get_args();
	$id=array_pop($params);

	// go to the original control function
	$callback=$wp_registered_widget_controls[$id]['callback_wlv_redirect'];

	if (is_callable($callback)) 
		call_user_func_array($callback, $params);		
	
	$value = !empty( $wlv_options[$id ] ) ? htmlspecialchars( stripslashes( $wlv_options[$id ] ),ENT_QUOTES ) : '';

	// dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
	$number=$params[0]['number'];
	if ($number==-1) {$number="%i%"; $value="";}
	$id_disp=$id;
	if (isset($number)) $id_disp=$wp_registered_widget_controls[$id]['id_base'].'-'.$number;

	// output our extra widget logic field
	?>
    	<div id="widget-logic-more-options-<?php echo $id_disp; ?>"></div>
        <a class="button" id="widget-logic-options-<?php echo $id_disp; ?>" href="#">Edit Limitation</a>
		<div id="visibility-<?php echo $id_disp; ?>" class="nwlv-widget-visibility"><?php widget_logic_visual_list_visibility_on_widget($id_disp); ?></div>

        <script type="text/javascript" language="javascript1.2">
		jQuery(document).ready(function(){
			
			jQuery('#widget-logic-options-<?php echo $id_disp; ?>').click(function(e){

				e.preventDefault();
				
				var value	= jQuery(this).val();
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				var data 	= {
								action		: 'widget-logic-options',
								widgetID	: "<?php echo $id_disp; ?>"
					   	  	  };
							  
				jQuery.post(ajaxurl, data, function(response) {
					jQuery.nmData(response,{
						sizes		: {
							initW	: 800,
							initH	: 500,
							w		: 800,
							h		: 500,
							minW	: 800,
							minH	: 500	
						}
					})
				});
				
				return false;
			});
		});
		</script>
    <?php
	
	//widget_logic_visual_more_extra_control($id_disp,$value);
	
}

// CALLED VIA 'sidebar_admin_page' ACTION
function widget_logic_visual_options_filter()
{	
	global $wp_registered_widget_controls, $wlv_options;
	?><div class="wrap">
		<form method="POST">
			<h2>Widget Logic options</h2>
			<p style="line-height: 30px;">

			<label for="widget_logic_visual-options-filter" title="Adds a new WP filter you can use in your own code. Not needed for main Widget Logic functionality.">Use 'widget_content' filter
			<input id="widget_logic_visual-options-filter" name="widget_logic_visual-options-filter" type="checkbox" value="checked" class="checkbox" <?php echo $wlv_options['widget_logic_visual-options-filter'] ?> /></label>
				&nbsp;&nbsp;
			<label for="widget_logic_visual-options-wp_reset_query" title="Resets a theme's custom queries before your Widget Logic is checked.">Use 'wp_reset_query' fix
			<input id="widget_logic_visual-options-wp_reset_query" name="widget_logic_visual-options-wp_reset_query" type="checkbox" value="checked" class="checkbox" <?php echo $wlv_options['widget_logic_visual-options-wp_reset_query'] ?> /></label>

			<span class="submit"><input type="submit" name="widget_logic_visual-options-submit" id="widget_logic_visual-options-submit" value="Save" /></span></p>
		</form>
	</div>
	<?php
}

// CALLED VIA 'widget_update_callback' ACTION (ajax update of a widget)
function widget_logic_visual_widget_update_callback($instance, $new_instance, $this_widget)
{	global $wlv_options;
	$widget_id=$this_widget->id;
	if ( isset($_POST[$widget_id.'-widget_logic_visual']))
	{	
		$wlv_options[$widget_id]=$_POST[$widget_id.'-widget_logic_visual'];
		update_option('widget_logic_visual', $wlv_options);
	}
	return $instance;
}

// CALLED ON 'plugin_action_links' ACTION
function widget_logic_visual_charity($links, $file)
{	if ($file == plugin_basename(__FILE__))
		array_push($links, '<a href="http://www.totalbounty.com/forums/topic/widget-logic-visual-version/">Forum Support</a>');
	return $links;
}



// FRONT END FUNCTIONS...
// CALLED ON 'sidebars_widgets' FILTER

function widget_logic_visual_filter_sidebars_widgets($sidebars_widgets)
{	
	global $wp_reset_query_is_done, $wlv_options,$post;
	
	// reset any database queries done now that we're about to make decisions based on the context given in the WP query for the page
	// add  && empty( $wp_reset_query_is_done ) to the next line to only exec the reset once per page
	if ( !empty( $wlv_options['widget_logic_visual-options-wp_reset_query'] ) && ( $wlv_options['widget_logic_visual-options-wp_reset_query'] == 'checked' ))
	{	wp_reset_query(); $wp_reset_query_is_done=true;	}

	// loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
	
	foreach($sidebars_widgets as $widget_area => $widget_list) :
		
		if ($widget_area == 'wp_inactive_widgets' || empty($widget_list)) continue;
		
		foreach($widget_list as $pos => $widget_id) :
			
			$delete	= widget_logic_visual_check_visibility($widget_id);
			
			if($delete) :
				unset($sidebars_widgets[$widget_area][$pos]);
			endif;

		endforeach;
		
	endforeach;
	
	return $sidebars_widgets;
}

// If 'widget_logic_visual-options-filter' is selected the widget_content filter is implemented...
// CALLED ON 'dynamic_sidebar_params' FILTER - this is called during 'dynamic_sidebar' just before each callback is run
// swap out the original call back and replace it with our own
function widget_logic_visual_widget_display_callback($params)
{	global $wp_registered_widgets;
	$id=$params[0]['widget_id'];
	$wp_registered_widgets[$id]['callback_wlv_redirect']=$wp_registered_widgets[$id]['callback'];
	$wp_registered_widgets[$id]['callback']='widget_logic_visual_redirected_callback';
	return $params;
}


// the redirection comes here
function widget_logic_visual_redirected_callback()
{	global $wp_registered_widgets, $wp_reset_query_is_done;

	// replace the original callback data
	$params=func_get_args();
	$id=$params[0]['widget_id'];
	$callback=$wp_registered_widgets[$id]['callback_wlv_redirect'];
	$wp_registered_widgets[$id]['callback']=$callback;

	// run the callback but capture and filter the output using PHP output buffering
	if ( is_callable($callback) ) 
	{	ob_start();
		call_user_func_array($callback, $params);
		$widget_content = ob_get_contents();
		ob_end_clean();
		echo apply_filters( 'widget_content', $widget_content, $id);
	}
}
add_action('admin_menu', 'widget_logic_visual_plugin_menu');

function widget_logic_visual_plugin_menu() 
{
	add_options_page('Widget Logic Visual Setting', 'Widget Logic Visual', 'manage_options', 'widget-logic-visual-setting', 'widget_logic_visual_setting_page');
}

function widget_logic_visual_setting_page()
{
	add_meta_box("mrt_sms1", "Plugin Settings"				, "widget_logic_visual_metabox1", "box1");
	add_meta_box("mrt_sms2", "Total Bounty Recent Blog Posts", "widget_logic_visual_metabox2", "box2");
	
	?>
    <div class="wrap">
    	<h2>Widget Logic Visual Setting</h2>
		<div id="dashboard-widgets-wrap">
			<div class="metabox-holder">
            	<div style="float:left;width:49%"><?php do_meta_boxes('box1','advanced',''); ?></div>
				<div style="float:right;width:49%"><?php do_meta_boxes('box2','advanced',''); ?></div>
               
			</div>
		</div>
    </div>
    <?php	
}

function widget_logic_visual_metabox1()
{
	?>
	<div style="padding:10px;">
        <p>
        	The widget logic visual plugin doesn't have any native settings
        </p>
        
        <p>
        	To use the plugin just visit "appereance -&gt; widgets" and at the bottom of any widget click "edit limitation" to use widget logic visual settings to limit
            the display of that widget to certain pages, posts or sections of you website
        </p>
        
        <p>
        	You can <a href="http://www.totalbounty.com/freebies/widget-logic-visual/">watch a video tutorial of how to use the plugin</a>
        </p>
        
        <p>
        	You can <a href="http://www.totalbounty.com/forums/topic/widget-logic-visual-version/">the forum page for the plugin</a>
        </p>
	</div>				
	<?php
}

function widget_logic_visual_metabox2()
{
	?>
	<div style="padding:10px;">
		<div style="font-size:13pt;text-align:center;">Our Tips, Tricks, and Posts...</div>

		<?php
			if(!function_exists('fetch_feed')) :
				include_once(ABSPATH . WPINC . '/feed.php');
			endif;
			
			$rss = fetch_feed('http://www.totalbounty.com/feed/');
			
			if (!is_wp_error( $rss ) ) : 
				$maxitems = $rss->get_item_quantity(10); 
			    $rss_items = $rss->get_items(0, $maxitems); 
			endif;
			
			if ($maxitems == 0) :
				?><p>No items.</p><?php
		    else :
		
		    foreach ( $rss_items as $item ) : 
			?>
			<p>
				<strong><a href="<?php echo $item->get_permalink(); ?>">
				<?php echo $item->get_title(); ?></a></strong>
			</p>
	    	<?php 
			endforeach;
			endif;
		?>

		<div style="font-size:13pt;text-align:center;">Total Bounty Marketplace</div>
		<div style="text-align:center"><em>HTML templates, WordPress themes and plugins, PSD templates and graphics, and more!</em>
			
            <br /><br />
    
            <a href="http://www.totalbounty.com" target="_blank">Visit our website: www.TotalBounty.com</a><br />
            <a href="http://www.totalbounty.com" border="0">
                <img src="http://www.totalbounty.com/wp-content/themes/total_bounty/images/logo-bt.png" alt="Total Bounty Marketplace" title="Total Bounty Marketplace">
            </a>
		</div>
	</div>				
	<?php
}

?>
