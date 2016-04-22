<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/settings.responsive.view.js"></script>
<div class="hmenu_settings_heading">
    <h2 class="hero_white size_18 weight_600">
        Responsive settings<br />
        <strong class="size_11 hero_grey">Responsive section for more advanced users.</strong>
    </h2>
</div>
<div class="hero_views hmenu_padding_top_10">
    <div class="hero_col_12">
        <!-- START: FORM -->
            <form> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                        <div class="hero_section_toggle" data-toggle-section="site_responsive">
                            <div class="hero_col_12">
                                <div class="hero_col_8">                                    
                                    <h2 class="size_18 hero_red weight_600">Advanced Responsive Settings</h2>
                                    <p class="size_12 hero_grey">Enable the menu’s responsive properties for PC, Mobile and Tablet. <br>Requires a responsive theme to work as intended.</p>                                    
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="siteResponsive" name="siteResponsive" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hmenu_site_responsive">
                            	<div class="hmenu_site_responsive_inner">
                                	Error
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_14 hero_green">Label for mobile menu</h2>
                                        <p class="size_12 hero_grey">This is the text that will be displayed on the collapsed menu bar. <br>Leave blank if you dont want any text to display.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" id="responsiveLabel" name="responsiveLabel">
                                </div>
                            </div>                            
                            <div class="hero_col_12">
                            	<div class="hero_col_1 hmenu_site_response hmenu_site_mobile">
                                </div>
                                <div class="hero_col_7">
                                    <label>
                                        <h2 class="size_14 hero_green">Mobile responsive break point</h2>
                                        <p class="size_12 hero_grey">This will enable your mobile navigation.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="px" id="siteResponsiveOne" maxlength="4" name="siteResponsiveOne">
                                </div>
                            </div>
                            <div class="hero_col_12">
                            	<div class="hero_col_1 hmenu_site_response hmenu_site_tablet">
                                </div>
                                <div class="hero_col_7">
                                    <label>
                                        <h2 class="size_14 hero_green">Tablet responsive break point</h2>
                                        <p class="size_12 hero_grey">This will define your break point for tablet sized screens.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="px" id="siteResponsiveTwo" maxlength="4" name="siteResponsiveTwo">
                                </div>
                            </div>
                            <div class="hero_col_12">
                            	<div class="hero_col_1 hmenu_site_response hmenu_site_desktop">
                                </div>
                                <div class="hero_col_7">
                                    <label>
                                        <h2 class="size_14 hero_green">Large responsive break point</h2>
                                        <p class="size_12 hero_grey">This will define your break point for desktop and laptop screens.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="px" id="siteResponsiveThree" maxlength="4" name="siteResponsiveThree">
                                </div>
                            </div>
                            <div class="hero_col_12">
                            	<div class="hero_col_1 hmenu_site_response hmenu_site_reset">
                                </div>
                            	<div class="hero_col_7">
                                    <label>
                                        <h2 class="size_14 hero_green">Reset to defaults</h2>
                                        <p class="size_12 hero_grey">Reset your defaults to 768px, 992px and 1200px respectively</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                	<div class="hero_button_auto green_button rounded_3 reset_to_defaults">Reset to defaults</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->
            </form>
        <!-- END: FORM -->
    </div>
</div>