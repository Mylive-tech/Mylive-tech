<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/settings.animation.view.js"></script>
<div class="hmenu_settings_heading">
    <h2 class="hero_white size_18 weight_600">
        Animation Settings<br />
        <strong class="size_11 hero_grey">Animation settings for your menu.</strong>
    </h2>
</div>
<div class="hero_views hmenu_padding_top_10">
    <div class="hero_col_12">
        <!-- START: FORM -->
            <form> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                        
                        <div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Animations and actions</h2>
                            <p class="size_12 hero_grey">Set Menu animation type and duration.</p>
                        </div>
                        
                        <div class="hero_col_12">
                            <div class="hero_col_8">
                                <label>
                                    <h2 class="size_14 hero_green">Animation type</h2>
                                    <p class="size_12 hero_grey">Animation effect for your dropdown menus.</p>
                                </label>
                            </div>
                            <div class="hero_col_4">
                                <select data-size="lrg" id="animation" name="animation">
                                    <option value="fade">Fade</option>
                                    <option value="show">Show</option>
                                </select>
                            </div>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_8">
                                <label>
                                    <h2 class="size_14 hero_green">Animation time</h2>
                                    <p class="size_12 hero_grey">Animation duration for your dropdowns.</p>
                                </label>
                            </div>
                            <div class="hero_col_4">
                                <input type="text" data-size="lrg" data-hero_type="ms" id="animationDuration" name="animationDuration" value="1000" onKeyPress="return num_only(event);" >
                            </div>
                        </div>
                        <!--<div class="hero_col_12">
                            <div class="hero_col_8">
                                <label>
                                    <h2 class="size_14 hero_green">Submenu timeout</h2>
                                    <p class="size_12 hero_grey">Duration after hover off event has taken place before collapsing dropdown.</p>
                                </label>
                            </div>
                            <div class="hero_col_4">
                                <input type="text" data-size="lrg" data-hero_type="ms" id="animationTimeout" name="animationTimeout" value="1000" onKeyPress="return num_only(event);" >
                            </div>
                        </div>-->
                    </div>
                <!-- END: ACTIVATION -->  
            </form>
        <!-- END: FORM -->
    </div>
</div>