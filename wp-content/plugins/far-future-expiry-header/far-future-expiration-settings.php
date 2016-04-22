<?php 
function displayFarFutureExpirationSettings()
{
	$errors = '';

	$enable_ffe = '';
	$enable_gif = '';
	$enable_jpeg = '';
	$enable_jpg = '';
	$enable_png = '';
	$enable_ico = '';
	$enable_js = '';
	$enable_css = '';
	$enable_swf = '';
	$num_expiry_days = '';
        $enable_gzip = '';
	
	global $wpdb, $ffe_plugin;
	if (isset($_POST['ffe_save_settings']))
	{
		if (!empty($_POST['num-expiry-days'])) {
			if (is_numeric($_POST['num-expiry-days']) && ($_POST['num-expiry-days'] >= 0)){
				$num_expiry_days = esc_sql($_POST['num-expiry-days']);
			} else {
				$errors .= 'Please enter an integer amount greater than or equal to zero for the "Number of Days" field.<br/>';
			}
		}
		
		if(!isset($_POST["ffe-gif"]) && !isset($_POST["ffe-jpeg"]) && !isset($_POST["ffe-jpg"]) && !isset($_POST["ffe-png"]) && !isset($_POST["ffe-ico"])
			&& !isset($_POST["ffe-js"]) && !isset($_POST["ffe-css"]) && !isset($_POST["ffe-swf"])) {
				$errors .= 'You must choose at least one "File Type" by enabling one of the checkboxes!<br/>';
			}
		if (strlen($errors)> 0){
			echo '<div id="message" class="error"><p>' . $errors . '</p></div>';
		}
		else{
			$options = array(
				'enable_ffe' => isset($_POST["enable-ffe"])?1:0,
				'num_expiry_days' => $num_expiry_days,
				'enable_gif' => isset($_POST["ffe-gif"])?1:0,
				'enable_jpeg' => isset($_POST["ffe-jpeg"])?1:0,
				'enable_jpg' => isset($_POST["ffe-jpg"])?1:0,
				'enable_png' => isset($_POST["ffe-png"])?1:0,
				'enable_ico' => isset($_POST["ffe-ico"])?1:0,
				'enable_js' => isset($_POST["ffe-js"])?1:0,
				'enable_css' => isset($_POST["ffe-css"])?1:0,
				'enable_swf' => isset($_POST["ffe-swf"])?1:0,
                                'enable_gzip' => isset($_POST["enable-gzip"])?1:0
			);
			update_option('far_future_expiration_settings', $options); //store the results in WP options table
			echo '<div id="message" class="updated fade">';
			echo '<p>Settings Saved</p>';
			echo '</div>';
			//Now let's modify the .htaccess file
			$write_result = $ffe_plugin->write_to_htaccess();
			if ($write_result){
				echo '<div id="message" class="updated fade">';
				echo '<p>Your .htaccess file was successfully modified</p>';
				echo '</div>';
			} else {
				echo '<div id="message" class="error"><p>Unable to modify the .htaccess file. Please check the file permissions!</p></div>';
			}
		}
	}

	$far_future_expiration_settings = get_option('far_future_expiration_settings');
	if ($far_future_expiration_settings)
	{
		$enable_ffe = $far_future_expiration_settings['enable_ffe'];
		$num_expiry_days = $far_future_expiration_settings['num_expiry_days'];
		$enable_gif = $far_future_expiration_settings['enable_gif'];
		$enable_jpeg = $far_future_expiration_settings['enable_jpeg'];
		$enable_jpg = $far_future_expiration_settings['enable_jpg'];
		$enable_png = $far_future_expiration_settings['enable_png'];
		$enable_ico = $far_future_expiration_settings['enable_ico'];
		$enable_js = $far_future_expiration_settings['enable_js'];
		$enable_css = $far_future_expiration_settings['enable_css'];
		$enable_swf = $far_future_expiration_settings['enable_swf'];
                $enable_gzip = $far_future_expiration_settings['enable_gzip'];
	}
?>
<div id="poststuff"><div id="post-body">
<div class="postbox">
<h3><label for="title">Using The Far Future Expiration Plugin</label></h3>
<div class="inside">

<p>You can read the usage instruction on the <a href="http://www.tipsandtricks-hq.com/wordpress-far-future-expiration-plugin-5980" target="_blank">far future expiration plugin</a> page.</p>

<p>This plugin will modify your .htaccess file by inserting code which will add expires headers for common file types.</p>
<p>Expires header specifies a time far enough in the future so that browsers won't try to re-fetch images, CSS, javascript etc files 
that haven't changed (this reduces the number of HTTP requests) and hence the performance improvement on subsequent page views.</p>
<p>To use this plugin do the following:</p>
<ol>
<li>Ensure that the "mod_expires" module is enabled from your host's main configuration file. 
<br />Check with your hosting provider or if you have access to the httpd.conf file the following line should be uncommented:
<br /><i>LoadModule expires_module modules/mod_expires.so</i>
</li>
<li>Enable the "Far Future Expiration" checkbox</li>
<li>Set the number of days till expiry</li>
<li>Select the file types you wish to enable the "far future expiration" feature for by using the checkboxes in the "File Types" section</li>
</ol>
<p><strong>NOTE: When you use this plugin, the file selected file types are cached in the browser until they expire. Therefore you should not use this on files that change frequently.</strong></p>

</div></div>
        
<form action="" method="POST">
            
<div class="postbox">
<h3><label for="title">Far Future Expiration Settings</label></h3>
<div class="inside">
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="Enableffa"> Enable Far Future Expiration : </label></th>
		<td>
		<input type="checkbox" name="enable-ffe" <?php if($enable_ffe) echo ' checked="checked"'; ?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="NumDays"> Number of Days:</label>
		</th>
		<td>
		<input type="text" size="10" name="num-expiry-days" value="<?php echo $num_expiry_days; ?>" /> <span>(Days)</span>
		<span class="description"> This value sets the expiry date of the selected file types to x days into the future.</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="FileTypes"> File Types : </label></th>
		<td>
			<div><input type="checkbox" name="ffe-gif" <?php if($enable_gif) echo ' checked="checked"'; ?> /> GIF</div>
			<div><input type="checkbox" name="ffe-jpeg" <?php if($enable_jpeg) echo ' checked="checked"'; ?> /> JPEG</div>
			<div><input type="checkbox" name="ffe-jpg" <?php if($enable_jpg) echo ' checked="checked"'; ?> /> JPG</div>
			<div><input type="checkbox" name="ffe-png" <?php if($enable_png) echo ' checked="checked"'; ?> /> PNG</div>
			<div><input type="checkbox" name="ffe-ico" <?php if($enable_ico) echo ' checked="checked"'; ?> /> ICO</div>
			<div><input type="checkbox" name="ffe-js" <?php if($enable_js) echo ' checked="checked"'; ?> /> JS</div>
			<div><input type="checkbox" name="ffe-css" <?php if($enable_css) echo ' checked="checked"'; ?> /> CSS</div>
			<div><input type="checkbox" name="ffe-swf" <?php if($enable_swf) echo ' checked="checked"'; ?> /> SWF</div>
		</td>
	</tr>
</table>
<div style="border-bottom: 1px solid #dedede; height: 10px"></div>
<br />

</div></div>

<div class="postbox">
<h3><label for="title">Gzip Compression Settings</label></h3>
<div class="inside">
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="Enable-gzip"> Enable Gzip Compression : </label></th>
		<td>
		<input type="checkbox" name="enable-gzip" <?php if($enable_gzip) echo ' checked="checked"'; ?> />
		</td>
	</tr>
        </table>
</div></div>
    
<p><input type="submit" name="ffe_save_settings" value="Save" class="button-primary" /></p>
</form>      
        
</div></div><!-- end of poststuff -->
<?php 
}
