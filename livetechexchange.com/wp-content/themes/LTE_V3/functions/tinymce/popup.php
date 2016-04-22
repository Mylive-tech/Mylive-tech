<?php
require_once('config.php');

if ( ! current_user_can('edit_pages') && ! current_user_can('edit_posts') ) 
{
	wp_die(__("You don't have permissions","doover"));
}
	
global $wpdb;
?>
<html>
<head>
<title><?php _e("Shortcode Panel","doover"); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option( 'siteurl' ) ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option( 'siteurl' ) ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option( 'siteurl' ) ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo PLUGIN_URI ?>tinymce.js"></script>
	<base target="_self" />
	<style>
		body, select { font-size:12px;}
	</style>
</head>

<body onLoad="tinyMCEPopup.executeOnLoad('init();'); document.body.style.display='';">

	<form name="shortcodes" action="#" >

		<div style="height:100px;">
			<?php _e("Shortcode","doover"); ?>
			<select id="shortcode" name="shortcode" style="width: 200px">
                <option value="0">-- <?php _e("select","doover"); ?> --</option>
                <option value="0">---------- <?php _e("columns","doover"); ?> ----------</option>
				<?php
					if( is_array( $shortcode_tags ) ) {
						foreach( $shortcode_tags as $key => $value ) {
							if( $key == "accordion" ){
								echo '<option value="0" >---------- '. __("general","doover") .' ----------</option>'."\n";
							}
							elseif( $key == "clients" ){
								echo '<option value="0" >---------- '. __("pages","doover") .' ----------</option>'."\n";
							}
							if( stristr( $value, 'sc_' ) ) {
								$shortcode = str_replace('sc_', '' ,$value);
								$shortcode = str_replace('_', ' ' ,$shortcode);
								$shortcode = ucwords($shortcode);
								echo '<option value="'. $key .'" >'. $shortcode .'</option>'."\n";
							}
						}
					}
				?>
            </select>
		</div>

		<div class="mceActionPanel">
			<div style="float: left">
				<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel","doover"); ?>" onClick="tinyMCEPopup.close();" />
			</div>
	
			<div style="float: right">
				<input type="submit" id="insert" name="insert" value="<?php _e("Insert","doover"); ?>" onClick="doover_mce_submit();" />
			</div>
		</div>
	
	</form>
</body>
</html>