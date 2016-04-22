<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/settings.integration.view.js"></script>
<div class="hmenu_settings_heading">
    <h2 class="hero_white size_18 weight_600">
        Menu Integration<br />
        <strong class="size_11 hero_grey">Hero Menu allows you to integrate by using registered menu locations, shortcodes or php do_shortcode script for the more hardcore developer.
    </h2>
</div>
<div class="hero_views hmenu_padding_top_10">
    <div class="hero_col_12">
        <!-- START: FORM -->
            <form>      	
            	<!-- START: LOCATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <div class="hero_col_8">
                                <h2 class="size_18 hero_red weight_600">Menu location</h2>
                                <strong class="size_12 hero_grey">Here you can set the location of your menu</strong>
                            </div>
                            <div class="hero_col_4">
                                <select data-size="lrg" id="menu_location" name="menu_location">
                                    <option value="">Select location</option>
                                </select>
                            </div>
                            <div class="hmenu_site_location">
                            	<div class="hmenu_site_location_inner">
                                </div>
                            </div>
                    	</div>
                    </div>
                <!-- END: LOCATION -->   
                <!-- START: LOCATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <div class="hero_col_12">
                                <h2 class="size_18 hero_red weight_600">Shortcodes</h2>
                                <strong class="size_12 hero_grey">Page shortcodes allow you to place a shortcode within your theme pages, posts and custom post type post editors</strong>
                            </div>
                            <div class="hero_col_12">
                                <pre class="hmenu_integrate_pages">[hmenu id=<span class="hmenu_integrate_id"></span>]</pre>
                                <p class="size_12 hero_red">Copy and paste this shortcode within the post editor. Example below!</p>
                            </div>
                            <div class="hero_col_12">
                                <h2 class="size_16 hero_green weight_300">Steps to add shortcode to a page</h2>
                                <strong class="size_12 hero_grey">Follow these steps to add the shortcode to your post pages.</strong>
                            </div>
                            <div class="hero_col_12">
                            	<ul class="hero_lists size_12 hero_grey">
                                	<li>Click on Pages/All Pages on the left hand side.</li>
                                	<li>Select a Page and click Edit.</li>
                                	<li>Paste the following shortcode within the Editor as shown below: <strong class="hmenu_integrate_pages hero_red"></strong></li>
                                </ul>
                            </div>
                            <div class="hero_col_12">
                                <h2 class="size_16 hero_green weight_300">Steps to add shortcode to a post</h2>
                                <strong class="size_12 hero_grey">Follow these steps to add the shortcode to your posts.</strong>
                            </div>
                            <div class="hero_col_12">
                            	<ul class="hero_lists size_12 hero_grey">
                                	<li>Click on Posts/All Posts on the left hand side.</li>
                                	<li>Select a Post and click Edit.</li>
                                	<li>Paste the following shortcode within the Editor as shown below: <strong class="hmenu_integrate_pages hero_red"></strong></li>
                                </ul>
                            </div>
                            <div class="hero_col_12 hero_integrate_pages">
                            	
                            </div>
                    	</div>
                    </div>
                <!-- END: LOCATION -->     
                <!-- START: LOCATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <div class="hero_col_12">
                                <h2 class="size_18 hero_red weight_600">PHP do_shortcodes</h2>
                                <strong class="size_12 hero_grey">This executes a shortcode outside of the post editor. Used to run shortcodes with the PHP script.</strong>
                            </div>
                            <div class="hero_col_12">
                                <pre class="hmenu_integrate_do"></pre>
                                <p class="size_12 hero_red">Copy and paste this php script within your header.php or footer.php</p>
                            </div> 
                            <div class="hero_col_12">
                                <h2 class="size_16 hero_green weight_300">Example on how to add do_shortcode to your files</h2>
                                <strong class="size_12 hero_grey">Follow these steps to add the menu to your php files.</strong>
                            </div>
                            <div class="hero_col_12">
                            	<ul class="hero_lists size_12 hero_grey">
                                	<li>Click on Appearance/Editor.</li>
                                	<li>On the right hand side under the Templates heading, select your header.php file</li>
                                	<li>Scroll down untill you find code that resembles the following, please note that not all themes will have the same HTML layout and that your theme might be slightly different.</li>
                                    <li>Replace the highlighted code with the following: <strong class="hmenu_integrate_do hero_red"></strong></li>
                                    <li>It is reccommended that you make a backup of the code that you are replacing, so that you can revert if you are not happy with the result.</li>
                                </ul>
                                <div class="hero_col_12 hero_code_example_1">
                                    
                                </div>
                            </div>                           
                    	</div>
                    </div>
                <!-- END: LOCATION -->      
            </form>
        <!-- END: FORM -->
    </div>
</div>