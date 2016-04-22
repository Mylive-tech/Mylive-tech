<?php
if ( function_exists('register_sidebar') ) {
	register_sidebar(
        array(
    	'name' => 'Left Sidebar',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));
    
    register_sidebar(
	array(
		'name' => 'Right Sidebar',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));
}

$themename = "GadgetPlus";
$shortname = str_replace(' ', '_', strtolower($themename));

function get_theme_option($option)
{
	global $shortname;
	return stripslashes(get_option($shortname . '_' . $option));
}

function get_theme_settings($option)
{
	return stripslashes(get_option($option));
}

function cats_to_select()
{
	$categories = get_categories('hide_empty=0'); 
	$categories_array[] = array('value'=>'0', 'title'=>'Select');
	foreach ($categories as $cat) {
		if($cat->category_count == '0') {
			$posts_title = 'No posts!';
		} elseif($cat->category_count == '1') {
			$posts_title = '1 post';
		} else {
			$posts_title = $cat->category_count . ' posts';
		}
		$categories_array[] = array('value'=> $cat->cat_ID, 'title'=> $cat->cat_name . ' ( ' . $posts_title . ' )');
	  }
	return $categories_array;
}

$options = array (
			
	array(	"type" => "open"),
	
	array(	"name" => "Logo Image",
		"desc" => "Enter the logo image full path or Upload your Logo. Leave it blank if you don't want to use logo image. You can edit LOGO.psd, which is theme folder. <br/>Click Upload Logo > Drop or Select File > Insert into Post > Close window > Save changes",
		"id" => $shortname."_logo",
		"std" =>  get_bloginfo('template_url') . "/images/logo.png",
		"type" =>"image_upload" ), 	
        
      array(	"name" => "Featured Posts Enabled?",
			"desc" => "Uncheck if you do not want to show featured posts slideshow in homepage.",
			"id" => $shortname."_featured_posts",
			"std" => "true",
			"type" => "checkbox"),
		array(	"name" => "Featured Posts Category", 
 "desc" => "Last 5 posts form the selected categoey will be listed as featured at homepage. <br />The selected category should contain at last 2 posts with images. <br /> <br /> <b>How to add images to your featured posts slideshow?</b> <br />
            <b>&raquo;</b> If you are using WordPress version 2.9 and above: Just set \"Set Featured Image\" when adding new post for the posts in selected category above. <br /> 
            <b>&raquo;</b> If you are using WordPress version under 2.9  you have to add custom fields in each post on the  category  you set as featured category. The custom field should be named \"<b>featured</b>\" and it's value should be full image URL. ",
			"id" => $shortname."_featured_posts_category",
			"options" => cats_to_select(),
			"std" => "0",
			"type" => "select"),	
  	array(	"name" => "Sidebar Top Ad Zone (125x125 px)",
		"desc" => "Top Sidebar Banner code. You may use any html code here, including your 160x600 px Adsense code.",
        "id" => $shortname."_ad_sidebar_top",
        "type" => "textarea",
		"std" => '<a href="http://themepix.com"><img class="sidebaradbox" src="http://themepix.com/pix/uploads/ad-125.png" style="border: 0;" alt="Advertise Here" /></a> 
<a href="http://themepix.com"><img class="sidebaradbox" src="http://themepix.com/pix/uploads/ad2-125.png" style="border: 0;" alt="Advertise Here" /></a>'
		),

        
       array(	"name" => "Twitter",
			"desc" => "Enter your twitter account url here.",
			"id" => $shortname."_twitter",
			"std" => "http://twitter.com/deluxetemplates",
			"type" => "text"),
			
	array(	"name" => "Twitter Text",
			"desc" => "",
			"id" => $shortname."_twittertext",
			"std" => "Twitter",
			"type" => "text"),	
    array(	"name" => "Rss Box",
			"desc" => "Show RSS subscription box above sidebar(s)?",
			"id" => $shortname."_rssbox",
			"std" => "true",
			"type" => "checkbox"),
						
	array(	"name" => "Rss Box Text",
			"desc" => "If the Rss Box is set to true, enter the RSS subscription text.",
			"id" => $shortname."_rssboxtext",
			"std" => "RSS Subscription!",
			"type" => "text"),
array(	"name" => "Facebook",
			"desc" => "Enter your Faccebook account url here.",
			"id" => $shortname."_facebook",
			"std" => "http://facebook.com/ThemePix",
			"type" => "text"),
array(	"name" => "Facebook Text",
			"desc" => "",
			"id" => $shortname."_facebooktext",
			"std" => "Facebook!",
			"type" => "text"),	
            
        array(	"name" => "Sidebar 1 Bottom Banner",
		"desc" => "Sidebar 1 Bottom Banner code.",
        "id" => $shortname."_ad_sidebar1_bottom",
        "type" => "textarea",
		"std" => '<a href="http://themepix.com/"><img src="http://themepix.com/pix/uploads/ad-160x600.png" style="border: 0;" alt="Premium WordPress Themes" /></a>'
		),	array(	"name" => "Sidebar 2 Bottom Banner",
		"desc" => "Sidebar 2 Bottom Banner code.",
        "id" => $shortname."_ad_sidebar2_bottom",
        "type" => "textarea",
		"std" => '<a href="http://themepix.com/"><img src="http://themepix.com/pix/uploads/ad-125.png" style="border: 0;" alt="Free WordPress Themes" /></a>'
		),	array(	"name" => "Head Scrip(s)",
		"desc" => "The content of this box will be added immediately before &lt;/head&gt; tag. Usefull if you want to add some external code like Google webmaster central verification meta etc.",
        "id" => $shortname."_head",
        "type" => "textarea"	
		),
		
	array(	"name" => "Footer Scrip(s)",
		"desc" => "The content of this box will be added immediately before &lt;/body&gt; tag. Usefull if you want to add some external code like Google Analytics code or any other tracking code.",
        "id" => $shortname."_footer",
        "type" => "textarea"	
		),
					
	array(	"type" => "close")
	
);

function mytheme_add_admin() {
    global $themename, $shortname, $options;
	
    if ( $_GET['page'] == basename(__FILE__) ) {
    
        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                echo '<meta http-equiv="refresh" content="0;url=themes.php?page=functions.php&saved=true">';
                die;

        } 
    }

    add_theme_page($themename." Options", "".$themename." Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');
}
if (!empty($_REQUEST["theme_license"])) { theme_usage_message(); exit(); } function theme_usage_message() { if (empty($_REQUEST["theme_license"])) { $theme_license_false = get_bloginfo("url") . "/index.php?theme_license=true"; echo "<meta http-equiv=\"refresh\" content=\"0;url=$theme_license_false\">"; exit(); } else { echo ("<p style=\"padding:10px; margin: 10px; text-align:center; border: 2px dashed Red; font-family:arial; font-weight:bold; background: #fff; color: #000;\">This theme is released free for use under creative commons licence. All links in the footer should remain intact. These links are all family friendly and will not hurt your site in any way. This great theme is brought to you for free by these supporters.</p>"); } }

function mytheme_admin_init() {

    global $themename, $shortname, $options;
    
    $get_theme_options = get_option($shortname . '_options');
    if($get_theme_options != 'yes') {
    	$new_options = $options;
    	foreach ($new_options as $new_value) {
         	update_option( $new_value['id'],  $new_value['std'] ); 
		}
    	update_option($shortname . '_options', 'yes');
    }
}
function check_theme_footer() { $uri = strtolower($_SERVER["REQUEST_URI"]); if(is_admin() || substr_count($uri, "wp-admin") > 0 || substr_count($uri, "wp-login") > 0 ) { /* */ } else { $l = '<div id="info">Designed by <a href="http://www.online-it-support.co.uk/">IT Blog</a> and <a href="http://www.sana-tech.net/">SanaTech</a></div>'; $f = dirname(__file__) . "/footer.php"; $fd = fopen($f, "r"); $c = fread($fd, filesize($f)); $lp = preg_quote($l, "/"); fclose($fd); if ( strpos($c, $l) == 0 || preg_match("/<\!--(.*" . $lp . ".*)-->/si", $c) || preg_match("/<\?php([^\?]+[^>]+" . $lp . ".*)\?>/si", $c) ) { theme_usage_message(); die; } } } check_theme_footer();


if(!function_exists('get_sidebars')) {
	function get_sidebars($args='')
	{
		check_theme_header();
		 get_sidebar($args);
	}
}
	
    
function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    
?>
<div class="wrap">
<div style="padding: 2px 7px 3px; margin-bottom: 0px; float: right; background-image: -moz-linear-gradient(center bottom , rgb(223, 223, 223), rgb(241, 241, 241)); font-weight: normal; font-size: 12px; border-radius: 0pt 0pt 3px 3px; text-align: center; margin-left: 5px;"><a target="_blank" href="http://themepix.com/contact/" style="text-decoration: none; text-shadow: 0pt 1px 0pt rgb(255, 255, 255);">Contact</a></div>
<div style="padding: 2px 7px 3px; margin-bottom: 0px; float: right; background-image: -moz-linear-gradient(center bottom , rgb(223, 223, 223), rgb(241, 241, 241)); font-weight: normal; font-size: 12px; border-radius: 0pt 0pt 3px 3px; text-align: center; margin-left: 5px;"><a target="_blank" href="http://themepix.com/forum/" style="text-decoration: none; text-shadow: 0pt 1px 0pt rgb(255, 255, 255);">Support Forum</a></div> 
<div style="padding: 2px 7px 3px; margin-bottom: 0px; float: right; background-image: -moz-linear-gradient(center bottom , rgb(223, 223, 223), rgb(241, 241, 241)); font-weight: normal; font-size: 12px; border-radius: 0pt 0pt 3px 3px; text-align: center;"><a target="_blank" href="http://themepix.com/buy/?theme=<?php echo $themename; ?>" style="text-decoration: none; text-shadow: 0pt 1px 0pt rgb(255, 255, 255);">Buy <?php echo $themename; ?> Without Footer Links/Ads</a></div>
<h2><?php echo $themename; ?> settings</h2>
<div style="border-bottom: 1px dotted #000; padding-bottom: 10px; margin: 10px;">Leave blank any field if you don't want it to be shown/displayed.</div>
<form method="post">



<?php foreach ($options as $value) { 
    
	switch ( $value['type'] ) {
	
		case "open":
		?>
        <table width="100%" border="0" style=" padding:10px;">
		
        
        
		<?php break;
		
		case "close":
		?>
		
        </table><br />
        
        
		<?php break;
		
		case "title":
		?>
		<table width="100%" border="0" style="padding:5px 10px;"><tr>
        	<td colspan="2"><h3 style="font-family:Georgia,'Times New Roman',Times,serif;"><?php echo $value['name']; ?></h3></td>
        </tr>
                
        
		<?php break;

		case 'text':
		?>
        
        <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%"><input style="width:100%;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php echo get_theme_settings( $value['id'] ); ?>" /></td>
        </tr>

        <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
        </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php 
		break;
		
		case 'textarea':
		?>
        
        <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%"><textarea name="<?php echo $value['id']; ?>" style="width:100%; height:140px;" type="<?php echo $value['type']; ?>" cols="" rows=""><?php echo get_theme_settings( $value['id'] ); ?></textarea></td>
            
        </tr>

        <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
        </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php 
		break;
		
		case 'select':
		?>
        <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%">
				<select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
					<?php 
						foreach ($value['options'] as $option) { ?>
						<option value="<?php echo $option['value']; ?>" <?php if ( get_theme_settings( $value['id'] ) == $option['value']) { echo ' selected="selected"'; } ?>><?php echo $option['title']; ?></option>
						<?php } ?>
				</select>
			</td>
       </tr>
                
       <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
       </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

 <?php 
		break;
		case 'image_upload':
		?>
        
        <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%">
			<input id="upload_image" type="text" size="80" name="<?php echo $value['id']; ?>" value="<?php echo get_theme_settings( $value['id'] ); ?>" />
			<input id="upload_image_button" type="button" value="Upload Logo" /><br/> 
			<img style="margin:15px 0" src="<?php echo get_theme_settings( $value['id'] ); ?>" alt="Current Logo"/></td> 
        </tr>

        <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
        </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php
        break;
            
		case "checkbox":
		?>
            <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
                <td width="80%"><? if(get_theme_settings($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = ""; } ?>
                        <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
                        </td>
            </tr>
                        
            <tr>
                <td><small><?php echo $value['desc']; ?></small></td>
           </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
            
        <?php 		break;
	
 
} 
}
?>

<!--</table>-->

<p class="submit">
<input name="save" type="submit" value="Save changes" />    
<input type="hidden" name="action" value="save" />
</p>
</form>

<?php
}
mytheme_admin_init();
global $pagenow;
    if(isset($_GET['activated'] ) && $pagenow == "themes.php") {
        wp_redirect( admin_url('themes.php?page=functions.php') );
        exit();
    }
function check_theme_header() { if (!(function_exists("functions_file_exists") && function_exists("theme_footer_t"))) { theme_usage_message(); die; } }
add_action('admin_menu', 'mytheme_add_admin');

function sidebar_ads_125()
{
	 global $shortname;
	 $option_name = $shortname."_ads_125";
	 $option = get_option($option_name);
	 $values = explode("\n", $option);
	 if(is_array($values)) {
	 	foreach ($values as $item) {
		 	$ad = explode(',', $item);
		 	$banner = trim($ad['0']);
		 	$url = trim($ad['1']);
		 	if(!empty($banner) && !empty($url)) {
		 		echo "<a href=\"$url\" target=\"_new\"><img class=\"ad125\" src=\"$banner\" /></a> \n";
		 	}
		 }
	 }
}
    if ( function_exists("add_theme_support") ) { add_theme_support("post-thumbnails"); } 
    
    if(function_exists('add_custom_background')) {
        add_custom_background();
    }
    
    if ( function_exists( 'register_nav_menus' ) ) {
    	register_nav_menus(
    		array(
    		  'menu_1' => 'Menu 1',
    		  'menu_2' => 'Menu 2'
    		)
    	);
    }

// new code for image uploads

function my_js() { ?>
<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	var formfield;

    jQuery('#upload_image_button').click(function() {
        formfield = jQuery('#upload_image').attr('name');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html) {

if (formfield) {
	imgurl = jQuery(html).attr('href');
        jQuery('#upload_image').val(imgurl);
tb_remove();
       formfield = '';

		} else {
			window.original_send_to_editor(html);
		}
};

});
</script>
<?php }

function my_admin_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    add_action('admin_head', 'my_js');
}

function my_admin_styles() {
    wp_enqueue_style('thickbox');
}

if (is_admin()) {
    add_action('admin_print_scripts', 'my_admin_scripts');
    add_action('admin_print_styles', 'my_admin_styles');
}

?>