<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/styling.mobile.view.js"></script>
<div class="hero_views">
    <div class="hero_col_12">
    	<h2 class="hero_red size_18 weight_600">
            Main Navigation<br />
            <strong class="size_11 hero_grey">Set the styling for your mobile devices.</strong>
        </h2>
        <!-- START: FORM -->
            <form>  
            	<!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Mobile menu bar background color</h2>
                        </div>                   	
                        <label><h2 class="size_14 hero_green">Default background color | Gradients</h2></label><div class="hero_switch_position"><input type="checkbox" data-size="sml" id="bgBarGradient" name="bgBarGradient" value="1" data-smltoggler="barGradientToggle"></div>
                        <p class="size_12 hero_grey">This will be the mobile menu background color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                                <label>Start color</label>
                            	<input type="text" id="bgBarStartColor" class="color_picker" name="bgMenuStartColor" value="#DC4551">
                            </div>
                            <div class="barGradientToggle">
                                <div class="hero_col_2">
                                	<label>End color</label>
                                    <input type="text" id="bgBarEndColor" class="color_picker" name="bgBarEndColor" value="#DC4551">
                                </div>
                                <div class="hero_col_3">                                
                                	<label>Gradient direction</label>
                                    <select data-size="lrg" id="bgBarGradientPath" name="bgBarGradientPath">
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hero_col_2">
                                <label>Transparency</label>
                            	<select data-size="lrg" id="bgBarTransparency" name="bgBarTransparency">
                                 	<option value="0.0">0%</option>
                                    <option value="0.1">10%</option>
                                    <option value="0.2">20%</option>
                                    <option value="0.3">30%</option>
                                    <option value="0.4">40%</option>
                                    <option value="0.5">50%</option>
                                    <option value="0.6">60%</option>
                                    <option value="0.7">70%</option>
                                    <option value="0.8">80%</option>
                                    <option value="0.9">90%</option>
                                    <option value="1.0">100%</option>
                                </select>
                            </div>
                        </div>                      
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Mobile menu bar font</h2>
                        </div>  
                    	<label><h2 class="size_14 hero_green">Font size and color</h2></label>
                        <p class="size_12 hero_grey">This will style the main mobile menu navigation items.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                             	<label>Font family</label>
                            	<select data-size="lrg" id="fontBarFamily" name="fontBarFamily" data-height="200">
                                    <option value="arial">Arial</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font weight</label>
                            	<select data-size="lrg" id="fontBarWeight" name="fontBarWeight">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontBarSize" name="fontBarSize">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font sizing</label>
                            	<select data-size="lrg" id="fontBarSizing" name="fontBarSizing">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font color</label>
                            	<input type="text" id="fontBarColor" class="color_picker" name="fontBarColor" value="#DC4551">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->
            	<!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Mobile main navigation background color</h2>
                        </div>                   	
                        <label><h2 class="size_14 hero_green">Default background color | Gradients</h2></label><div class="hero_switch_position"><input type="checkbox" data-size="sml" id="bgMenuGradient" name="bgMenuGradient" value="1" data-smltoggler="menuGradientToggle"></div>
                        <p class="size_12 hero_grey">This will be the mobile menu background color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                                <label>Start color</label>
                            	<input type="text" id="bgMenuStartColor" class="color_picker" name="bgMenuStartColor" value="#DC4551">
                            </div>
                            <div class="menuGradientToggle">
                                <div class="hero_col_2">
                                	<label>End color</label>
                                    <input type="text" id="bgMenuEndColor" class="color_picker" name="bgMenuEndColor" value="#DC4551">
                                </div>
                                <div class="hero_col_3">                                
                                	<label>Gradient direction</label>
                                    <select data-size="lrg" id="bgMenuGradientPath" name="bgMenuGradientPath">
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hero_col_2">
                                <label>Transparency</label>
                            	<select data-size="lrg" id="bgMenuTransparency" name="bgMenuTransparency">
                                 	<option value="0.0">0%</option>
                                    <option value="0.1">10%</option>
                                    <option value="0.2">20%</option>
                                    <option value="0.3">30%</option>
                                    <option value="0.4">40%</option>
                                    <option value="0.5">50%</option>
                                    <option value="0.6">60%</option>
                                    <option value="0.7">70%</option>
                                    <option value="0.8">80%</option>
                                    <option value="0.9">90%</option>
                                    <option value="1.0">100%</option>
                                </select>
                            </div>
                        </div>                      
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Mobile main navigation hover background color</h2>
                        </div>                   	
                        <label><h2 class="size_14 hero_green">Default background color | Gradients</h2></label><div class="hero_switch_position"><input type="checkbox" data-size="sml" id="bgHoverGradient" name="bgHoverGradient" value="1" data-smltoggler="menuHoverGradientToggle"></div>
                        <p class="size_12 hero_grey">This will be the mobile menu background color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                                <label>Start color</label>
                            	<input type="text" id="bgHoverStartColor" class="color_picker" name="bgHoverStartColor" value="#DC4551">
                            </div>
                            <div class="menuHoverGradientToggle">
                                <div class="hero_col_2">
                                	<label>End color</label>
                                    <input type="text" id="bgHoverEndColor" class="color_picker" name="bgHoverEndColor" value="#DC4551">
                                </div>
                                <div class="hero_col_3">                                
                                	<label>Gradient direction</label>
                                    <select data-size="lrg" id="bgHoverGradientPath" name="bgHoverGradientPath">
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hero_col_2">
                                <label>Transparency</label>
                            	<select data-size="lrg" id="bgHoverTransparency" name="bgHoverTransparency">
                                 	<option value="0.0">0%</option>
                                    <option value="0.1">10%</option>
                                    <option value="0.2">20%</option>
                                    <option value="0.3">30%</option>
                                    <option value="0.4">40%</option>
                                    <option value="0.5">50%</option>
                                    <option value="0.6">60%</option>
                                    <option value="0.7">70%</option>
                                    <option value="0.8">80%</option>
                                    <option value="0.9">90%</option>
                                    <option value="1.0">100%</option>
                                </select>
                            </div>
                        </div>                      
                    </div>
                <!-- END: ACTIVATION -->
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Mobile fonts</h2>
                        </div>  
                    	<label><h2 class="size_14 hero_green">Font size and color</h2></label>
                        <p class="size_12 hero_grey">This will style the main mobile menu navigation items.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                             	<label>Font family</label>
                            	<select data-size="lrg" id="fontMobileFamily" name="fontMobileFamily" data-height="200">
                                    <option value="arial">Arial</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font weight</label>
                            	<select data-size="lrg" id="fontMobileWeight" name="fontMobileWeight">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontMobileSize" name="fontMobileSize">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font sizing</label>
                            	<select data-size="lrg" id="fontMobileSizing" name="fontMobileSizing">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font color</label>
                            	<input type="text" id="fontMobileColor" class="color_picker" name="fontMobileColor" value="#DC4551">
                            </div>
                        </div>
                        <label><h2 class="size_14 hero_green">Hover font color</h2></label>
                        <p class="size_12 hero_grey">This will style the main tablet menu navigation hover text color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                            	<label>Font hover color</label>
                            	<input type="text" id="fontMobileHoverColor" class="color_picker" name="fontMobileHoverColor" value="#DC4551">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Tablet main navigation fonts</h2>
                        </div>  
                    	<label><h2 class="size_14 hero_green">Font size and color</h2></label>
                        <p class="size_12 hero_grey">This will style the main tablet menu navigation items.</p>
                        <div class="hero_col_12">
                            <!--<div class="hero_col_3">
                             	<label>Font family</label>
                            	<select data-size="lrg" id="fontTabletFamily" name="fontTabletFamily" data-height="200">
                                    <option value="arial">Arial</option>
                                </select>
                            </div>-->
                            <div class="hero_col_2">
                             	<label>Font weight</label>
                            	<select data-size="lrg" id="fontTabletWeight" name="fontTabletWeight">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontTabletSize" name="fontTabletSize">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font sizing</label>
                            	<select data-size="lrg" id="fontTabletSizing" name="fontTabletSizing">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                            <!--<div class="hero_col_2">
                             	<label>Font color</label>
                            	<input type="text" id="fontTabletColor" class="color_picker" name="fontTabletColor" value="#DC4551">
                            </div>-->
                        </div>
                        <!--<label><h2 class="size_14 hero_green">Hover font color</h2></label>
                        <p class="size_12 hero_grey">This will style the main navigation hover text color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                            	<label>Font hover color</label>
                            	<input type="text" id="fontTabletHoverColor" class="color_picker" name="fontTabletHoverColor" value="#DC4551">
                            </div>
                        </div>-->
                    </div>
                <!-- END: ACTIVATION -->  
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                        <div class="hero_col_6">
                            <label>
                                <h2 class="size_18 hero_red weight_600">Tablet main menu padding</h2>
                                <p class="size_12 hero_grey">This will add padding to the left and right of each main tablet nav item.</p>
                            </label>
                            <div class="hero_col_12">
                                <div class="hero_col_3">
                                	<label>Left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingLeft" name="paddingLeft">
                                </div>
                                <div class="hero_col_3">
                                	<label>Right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingRight" name="paddingRight">
                                </div>
                            </div>  
                        </div>
                        <div class="hero_col_6">
                            <div class="hero_padding_example"></div>
                        </div>                                              
                    </div>
                <!-- END: ACTIVATION --> 
            </form>
        <!-- END: FORM -->
    </div>
</div>